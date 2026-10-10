<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Administrative extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('M_transaksi');
        $this->load->model('M_master');
        $this->load->model('M_gudang');
    }

    public function index()
    {
        redirect('administrative/transaksi');
    }

    public function transaksi()
    {
        $status = $this->input->get('status', true);
        $start_date = $this->input->get('start_date', true) ?: date('Y-m-d');
        $end_date = $this->input->get('end_date', true) ?: date('Y-m-d');
        $perusahaan_id = $this->session->userdata('perusahaan_id');
        $user_level = $this->session->userdata('level');

        // 1. Optimize Summary Query (Ultra Fast Stats)
        $this->db->select('
            COUNT(p.nospk) as total_count,
            SUM(CASE WHEN p.status_pembayaran = "paid" THEN 1 ELSE 0 END) as paid_count,
            SUM(CASE WHEN p.status_pembayaran = "pending" THEN 1 ELSE 0 END) as pending_count
        ');
        $this->db->from('tbl_pemesanan p');
        $this->db->where('DATE(p.created_at) >=', $start_date);
        $this->db->where('DATE(p.created_at) <=', $end_date);
        if (!in_array($user_level, [1, 17]))
            $this->db->where('p.perusahaan_id', $perusahaan_id);
        if ($status && $status !== 'all')
            $this->db->where('p.status_pembayaran', $status);
        $stats = $this->db->get()->row_array();

        // 2. Base Transaction Query with Optimized JOINS
        // We pre-aggregate payments and totals into temporary result sets (subqueries) to avoid N+1 query problems
        $this->db->select('
            p.*, 
            k.konsumen_nama, 
            jt.nama_jenis_transaksi, 
            u.username,
            COALESCE(sums.grand_total, 0) as grand_total,
            COALESCE(pay.bayar_tunai, 0) as bayar_tunai,
            COALESCE(pay.bayar_transfer, 0) as bayar_transfer,
            COALESCE(pay.bayar_ewallet, 0) as bayar_ewallet,
            COALESCE(pay.bayar_debit, 0) + COALESCE(pay.bayar_kartukredit, 0) as bayar_edc
        ');
        $this->db->from('tbl_pemesanan p');
        $this->db->join('tbl_konsumen k', 'k.konsumen_id = p.konsumen_id', 'left');
        $this->db->join('tbl_jenis_transaksi jt', 'jt.id_jenis_transaksi = p.id_jenis_transaksi', 'left');
        $this->db->join('users u', 'u.user_id = p.deskprint_id', 'left');

        // JOIN 1: Product Totals (Aggregated by SPK)
        $has_subtotal = $this->db->field_exists('subtotal', 'tbl_pemesanan_detail');
        $sum_logic = $has_subtotal
            ? "SUM(COALESCE(pd.subtotal, (CASE WHEN pd.panjang > 0 AND pd.lebar > 0 THEN (pd.panjang * pd.lebar * pd.qty * hp.harga_1) WHEN pd.panjang > 0 AND (pd.lebar <= 0 OR pd.lebar IS NULL) THEN (pd.panjang * pd.qty * hp.harga_1) ELSE (pd.qty * hp.harga_1) END)))"
            : "SUM(CASE WHEN pd.panjang > 0 AND pd.lebar > 0 THEN (pd.panjang * pd.lebar * pd.qty * hp.harga_1) WHEN pd.panjang > 0 AND (pd.lebar <= 0 OR pd.lebar IS NULL) THEN (pd.panjang * pd.qty * hp.harga_1) ELSE (pd.qty * hp.harga_1) END)";

        $this->db->join('(
            SELECT pd.nospk, ' . $sum_logic . ' as grand_total 
            FROM tbl_pemesanan_detail pd 
            JOIN tbl_harga_product hp ON pd.harga_id = hp.harga_id 
            GROUP BY pd.nospk
        ) as sums', 'sums.nospk = p.nospk', 'left');

        // JOIN 2: Payments Breakdown (Aggregated by SPK)
        $this->db->join('(
            SELECT pb.nospk,
                SUM(CASE WHEN pd.metode = "tunai" THEN pd.jumlah ELSE 0 END) as bayar_tunai,
                SUM(CASE WHEN pd.metode = "transfer" THEN pd.jumlah ELSE 0 END) as bayar_transfer,
                SUM(CASE WHEN pd.metode = "ewallet" THEN pd.jumlah ELSE 0 END) as bayar_ewallet,
                SUM(CASE WHEN pd.metode = "debit" THEN pd.jumlah ELSE 0 END) as bayar_debit,
                SUM(CASE WHEN pd.metode = "kartu_kredit" THEN pd.jumlah ELSE 0 END) as bayar_kartukredit
            FROM tbl_pembayaran pb
            JOIN tbl_pembayaran_detail pd ON pb.pembayaran_id = pd.pembayaran_id
            GROUP BY pb.nospk
        ) as pay', 'pay.nospk = p.nospk', 'left');

        $this->db->where('DATE(p.created_at) >=', $start_date);
        $this->db->where('DATE(p.created_at) <=', $end_date);
        if (!in_array($user_level, [1, 17]))
            $this->db->where('p.perusahaan_id', $perusahaan_id);
        if ($status && $status !== 'all')
            $this->db->where('p.status_pembayaran', $status);

        $this->db->order_by('p.created_at', 'DESC');
        $this->db->limit(1000); // Guard for safety
        $transaksi = $this->db->get()->result();

        // 3. Post-process Anomaly (Calculated in PHP on small current set)
        foreach ($transaksi as &$tr) {
            $total_paid = (float) $tr->bayar_tunai + (float) $tr->bayar_transfer + (float) $tr->bayar_ewallet + (float) $tr->bayar_edc;
            $tr->total_bayar = $total_paid;
            $tr->kurang_bayar = max(0, (float) $tr->grand_total - $total_paid);
            $tr->anomali = (round($total_paid) < round((float) $tr->grand_total) && $tr->status_pembayaran == 'paid');
        }

        $data = [
            'title' => 'Audit & Koreksi Transaksi (Admin)',
            'transaksi' => $transaksi,
            'stats' => $stats,
            'status_filter' => $status,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'jenis_transaksi' => $this->db->get('tbl_jenis_transaksi')->result()
        ];

        $this->load_admin_view('admin/administrative/transaksi_all', $data);
    }

    /**
     * AJAX Endpoint for Koreksi Modal Detail
     * Avoids embedding large JSON in every table row
     */
    public function get_transaksi_audit_json()
    {
        $nospk = $this->input->post('nospk', true);

        $this->db->select('p.*, COALESCE(sums.grand_total, 0) as grand_total');
        $this->db->from('tbl_pemesanan p');
        $has_subtotal = $this->db->field_exists('subtotal', 'tbl_pemesanan_detail');
        $sum_logic = $has_subtotal
            ? "SUM(COALESCE(pd.subtotal, (CASE WHEN pd.panjang > 0 AND pd.lebar > 0 THEN (pd.panjang * pd.lebar * pd.qty * hp.harga_1) WHEN pd.panjang > 0 AND (pd.lebar <= 0 OR pd.lebar IS NULL) THEN (pd.panjang * pd.qty * hp.harga_1) ELSE (pd.qty * hp.harga_1) END)))"
            : "SUM(CASE WHEN pd.panjang > 0 AND pd.lebar > 0 THEN (pd.panjang * pd.lebar * pd.qty * hp.harga_1) WHEN pd.panjang > 0 AND (pd.lebar <= 0 OR pd.lebar IS NULL) THEN (pd.panjang * pd.qty * hp.harga_1) ELSE (pd.qty * hp.harga_1) END)";

        $this->db->join('(
            SELECT pd.nospk, ' . $sum_logic . ' as grand_total 
            FROM tbl_pemesanan_detail pd 
            JOIN tbl_harga_product hp ON pd.harga_id = hp.harga_id 
            WHERE pd.nospk = ' . $this->db->escape($nospk) . '
            GROUP BY pd.nospk
        ) as sums', 'sums.nospk = p.nospk', 'left');
        $this->db->where('p.nospk', $nospk);
        $header = $this->db->get()->row_array();

        if (!$header) {
            echo json_encode(['status' => 'error']);
            return;
        }

        // Get breakdown payments
        $this->db->select('pd.metode, SUM(pd.jumlah) as total');
        $this->db->from('tbl_pembayaran pb');
        $this->db->join('tbl_pembayaran_detail pd', 'pb.pembayaran_id = pd.pembayaran_id');
        $this->db->where('pb.nospk', $nospk);
        $this->db->group_by('pd.metode');
        $payments = $this->db->get()->result();

        $pay_data = ['tunai' => 0, 'transfer' => 0, 'ewallet' => 0, 'debit' => 0, 'kartu_kredit' => 0];
        foreach ($payments as $p) {
            $pay_data[$p->metode] = $p->total;
        }

        echo json_encode([
            'status' => 'success',
            'header' => $header,
            'payments' => $pay_data
        ]);
    }

    public function verify_pin_koreksi()
    {
        $pin = $this->input->post('pin', true);

        if (empty($pin)) {
            echo json_encode(['success' => false, 'message' => 'PIN harus diisi.']);
            return;
        }

        $this->db->select('k.karyawan_id, k.nama_lengkap, u.role_id');
        $this->db->from('tbl_karyawan k');
        $this->db->join('users u', 'u.karyawan_id = k.karyawan_id');
        $this->db->where('k.pin_produksi', $pin);
        // Admin=1, Owner=17
        $this->db->where_in('u.role_id', [1, 17]);
        $karyawan = $this->db->get()->row();

        if ($karyawan) {
            $this->session->set_userdata('verified_karyawan_koreksi', $karyawan->nama_lengkap);

            // Log akses masuk ke form koreksi
            $this->_log_manual("Akses Form Koreksi Transaksi", "Karyawan: " . $karyawan->nama_lengkap . " memverifikasi PIN", "ACCESS");

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal verifikasi! PIN tidak ditemukan atau Anda bukan Admin/Owner.']);
        }
    }

    public function update_pembayaran()
    {
        if (!$this->session->userdata('verified_karyawan_koreksi')) {
            $this->session->set_flashdata('error', 'Akses ditolak! Anda harus memverifikasi PIN otorisasi terlebih dahulu.');
            redirect('administrative/transaksi');
        }

        $nospk = $this->input->post('nospk', true);

        $this->db->trans_start();

        // 1. Update status pemesanan
        $data_pemesanan = [
            'status_pembayaran' => $this->input->post('status_pembayaran', true),
            'id_jenis_transaksi' => $this->input->post('id_jenis_transaksi', true)
        ];
        $this->db->where('nospk', $nospk)->update('tbl_pemesanan', $data_pemesanan);

        // 2. Sync ke Tabel Keuangan (tbl_pembayaran & tbl_pembayaran_detail)
        $pembayaran = $this->db->get_where('tbl_pembayaran', ['nospk' => $nospk])->row();

        $total_bayar_baru = (float) $this->input->post('bayar_tunai', true) +
            (float) $this->input->post('bayar_transfer', true) +
            (float) $this->input->post('bayar_ewallet', true) +
            (float) $this->input->post('bayar_debit', true) +
            (float) $this->input->post('bayar_kartukredit', true);

        if ($pembayaran) {
            $pembayaran_id = $pembayaran->pembayaran_id;
            // Update master pembayaran
            $this->db->where('pembayaran_id', $pembayaran_id)->update('tbl_pembayaran', [
                'total_bayar' => $total_bayar_baru,
                'status_lunas' => ($this->input->post('status_pembayaran', true) == 'paid' ? '1' : '0'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Clear old details and insert new corrected ones
            $this->db->where('pembayaran_id', $pembayaran_id)->delete('tbl_pembayaran_detail');

            $methods = [
                'tunai' => 'bayar_tunai',
                'transfer' => 'bayar_transfer',
                'ewallet' => 'bayar_ewallet',
                'debit' => 'bayar_debit',
                'kartu_kredit' => 'bayar_kartukredit'
            ];

            foreach ($methods as $db_metode => $post_key) {
                $val = (float) $this->input->post($post_key);
                if ($val > 0) {
                    $this->db->insert('tbl_pembayaran_detail', [
                        'pembayaran_id' => $pembayaran_id,
                        'metode' => $db_metode,
                        'jumlah' => $val,
                        'keterangan' => 'Koreksi Admin: ' . $this->input->post('catatan', true)
                    ]);
                }
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'Gagal melakukan sinkronisasi keuangan.');
        } else {
            // [AUDIT LOG]
            $verified_by = $this->session->userdata('verified_karyawan_koreksi') ?: 'Admin (Tanpa PIN)';
            $this->_log_manual("Koreksi Keuangan SPK #$nospk ($verified_by)", "Status: " . $this->input->post('status_pembayaran', true) . ", Total: $total_bayar_baru, Catatan: " . $this->input->post('catatan', true), 'WRITE');

            $this->session->unset_userdata('verified_karyawan_koreksi');

            $this->session->set_flashdata('success', 'Data transaksi & Keuangan #' . $nospk . ' berhasil disinkronkan.');
        }

        redirect('administrative/transaksi');
    }

    public function delete_transaksi_ajax()
    {
        if (!$this->session->userdata('verified_karyawan_koreksi')) {
            echo json_encode(['success' => false, 'message' => 'Otorisasi ditolak. Masukkan PIN terlebih dahulu.']);
            return;
        }

        $nospk = $this->input->post('nospk', true);

        if (empty($nospk)) {
            echo json_encode(['success' => false, 'message' => 'No SPK tidak valid.']);
            return;
        }

        $this->db->trans_start();

        // 1. Get produksi_ids for detail_produksi
        $prods = $this->db->select('produksi_id')->get_where('tbl_produksi', ['nospk' => $nospk])->result();
        $pids = array_map(function ($p) {
            return $p->produksi_id;
        }, $prods);

        if (!empty($pids)) {
            $this->db->where_in('produksi_id', $pids)->delete('detail_produksi');
        }

        // 2. Delete main tables based on nospk
        $this->db->where('nospk', $nospk)->delete('tbl_produksi');

        // 3. Get pembayaran_ids for tbl_pembayaran_detail
        $pems = $this->db->select('pembayaran_id')->get_where('tbl_pembayaran', ['nospk' => $nospk])->result();
        $pmids = array_map(function ($p) {
            return $p->pembayaran_id;
        }, $pems);

        if (!empty($pmids)) {
            $this->db->where_in('pembayaran_id', $pmids)->delete('tbl_pembayaran_detail');
        }

        $this->db->where('nospk', $nospk)->delete('tbl_pembayaran');
        $this->db->where('nospk', $nospk)->delete('tbl_pembayaran_produk');
        $this->db->where('nospk', $nospk)->delete('tbl_pemesanan_detail');
        $this->db->where('nospk', $nospk)->delete('tbl_pemesanan');
        $this->db->where('nospk', $nospk)->delete('tbl_spk');

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus data di database.']);
        } else {
            // [AUDIT LOG]
            $verified_by = $this->session->userdata('verified_karyawan_koreksi') ?: 'Admin';
            $this->_log_manual("Hapus Transaksi SPK #$nospk", "Dihapus atas otorisasi: $verified_by. User sistem: " . $this->session->userdata('username'), 'DELETE');

            $this->session->unset_userdata('verified_karyawan_koreksi');
            echo json_encode(['success' => true]);
        }
    }

    public function spk_monitor()
    {
        $start_date = $this->input->get('start_date', true) ?: date('Y-m-01');
        $end_date = $this->input->get('end_date', true) ?: date('Y-m-d');
        $user_level = $this->session->userdata('level');
        $perusahaan_id = $this->session->userdata('perusahaan_id');

        // 1. Ambil Summary jumlah SPK per Deskprint
        $this->db->select('u.username, k.nama_lengkap, COUNT(p.nospk) as total_spk, SUM(pb.total_tagihan) as total_omset')
            ->from('tbl_pemesanan p')
            ->join('tbl_pembayaran pb', 'pb.nospk = p.nospk', 'left')
            ->join('users u', 'u.user_id = p.deskprint_id')
            ->join('tbl_karyawan k', 'k.karyawan_id = u.karyawan_id', 'left')
            ->where('DATE(p.created_at) >=', $start_date)
            ->where('DATE(p.created_at) <=', $end_date);

        if (!in_array($user_level, [1, 17])) {
            $this->db->where('p.perusahaan_id', $perusahaan_id);
        }

        $summary = $this->db->group_by('p.deskprint_id')
            ->order_by('total_spk', 'DESC')
            ->get()->result();

        // 2. Ambil List Seluruh SPK
        $this->db->select('p.*, pb.total_tagihan, u.username as deskprint_username, k.nama_lengkap as deskprint_name, con.konsumen_nama, con.konsumen_nohp, jt.nama_jenis_transaksi');
        $this->db->from('tbl_pemesanan p');
        $this->db->join('tbl_pembayaran pb', 'pb.nospk = p.nospk', 'left');
        $this->db->join('users u', 'u.user_id = p.deskprint_id', 'left');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = u.karyawan_id', 'left');
        $this->db->join('tbl_konsumen con', 'con.konsumen_id = p.konsumen_id', 'left');
        $this->db->join('tbl_jenis_transaksi jt', 'jt.id_jenis_transaksi = p.id_jenis_transaksi', 'left');

        if (!in_array($user_level, [1, 17])) {
            $this->db->where('p.perusahaan_id', $perusahaan_id);
        }

        if ($start_date && $end_date) {
            $this->db->where('DATE(p.created_at) >=', $start_date);
            $this->db->where('DATE(p.created_at) <=', $end_date);
        }

        $this->db->order_by('p.created_at', 'DESC');
        $spk_list = $this->db->get()->result();

        // 3. Global Stats Calculation
        $global_stats = [
            'total_spk' => 0,
            'total_omset' => 0,
            'paid_spk' => 0,
            'pending_spk' => 0
        ];

        foreach ($spk_list as $spk) {
            $global_stats['total_spk']++;
            $global_stats['total_omset'] += $spk->total_tagihan;
            if ($spk->status_pembayaran == 'paid') {
                $global_stats['paid_spk']++;
            } else {
                $global_stats['pending_spk']++;
            }
        }

        $data = [
            'title' => 'Monitoring Performa Deskprint',
            'summary' => $summary,
            'spk_list' => $spk_list,
            'global_stats' => $global_stats,
            'start_date' => $start_date,
            'end_date' => $end_date
        ];

        $this->load_admin_view('admin/administrative/spk_monitor', $data);
    }

    public function get_deskprint_detail()
    {
        $username = $this->input->get('username', true);
        $start_date = $this->input->get('start_date', true);
        $end_date = $this->input->get('end_date', true);
        $user_level = $this->session->userdata('level');
        $perusahaan_id = $this->session->userdata('perusahaan_id');

        $this->db->select('p.*, pb.total_tagihan, con.konsumen_nama, jt.nama_jenis_transaksi');
        $this->db->from('tbl_pemesanan p');
        $this->db->join('tbl_pembayaran pb', 'pb.nospk = p.nospk', 'left');
        $this->db->join('users u', 'u.user_id = p.deskprint_id', 'left');
        $this->db->join('tbl_konsumen con', 'con.konsumen_id = p.konsumen_id', 'left');
        $this->db->join('tbl_jenis_transaksi jt', 'jt.id_jenis_transaksi = p.id_jenis_transaksi', 'left');

        $this->db->where('u.username', $username);
        $this->db->where('DATE(p.created_at) >=', $start_date);
        $this->db->where('DATE(p.created_at) <=', $end_date);

        if (!in_array($user_level, [1, 17])) {
            $this->db->where('p.perusahaan_id', $perusahaan_id);
        }

        $this->db->order_by('p.created_at', 'DESC');

        $list = $this->db->get()->result_array();

        echo json_encode($list);
    }

    public function monitoring_produksi()
    {
        $start_date = $this->input->get('start_date', true) ?: date('Y-m-d');
        $end_date = $this->input->get('end_date', true) ?: date('Y-m-d');
        $search_spk = $this->input->get('search_spk', true);

        $data = $this->_get_monitoring_data($start_date, $end_date, $search_spk);
        $data['title'] = 'Real-time Production Monitoring';

        $this->load_admin_view('admin/administrative/monitoring_produksi', $data);
    }

    public function monitoring_produksi_ajax()
    {
        $start_date = $this->input->get('start_date', true) ?: date('Y-m-d');
        $end_date = $this->input->get('end_date', true) ?: date('Y-m-d');
        $search_spk = $this->input->get('search_spk', true);

        $data = $this->_get_monitoring_data($start_date, $end_date, $search_spk);
        echo json_encode($data);
    }

    private function _get_monitoring_data($start_date, $end_date, $search_spk)
    {
        $user_level = $this->session->userdata('level');
        $perusahaan_id = $this->session->userdata('perusahaan_id');

        // 1. Ambil Summary Konsumsi Bahan Hari Ini (Daily Aggregation)
        // Note: daily usage inside M_gudang should ideally also filter by perusahaan_id
        $daily_usage = $this->M_gudang->get_daily_consumption($start_date);

        // 2. Ambil List Log Penggunaan Bahan Terbaru (Real-time Flow)
        $this->db->select('sl.*, m.varian as material_name, b.bahan_nama, s.satuan_nama as satuan_gudang, u.username');
        $this->db->from('tbl_stok_log sl');
        $this->db->join('tbl_material m', 'sl.material_id = m.material_id', 'left');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id', 'left');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id', 'left');
        $this->db->join('users u', 'u.user_id = sl.user_id', 'left');

        if (!in_array($user_level, [1, 17])) {
            $this->db->join('tbl_produksi p_log', 'p_log.produksi_id = sl.trx_id', 'left');
            $this->db->join('tbl_pemesanan pms_log', 'pms_log.nospk = p_log.nospk', 'left');
            $this->db->where('pms_log.perusahaan_id', $perusahaan_id);
        }

        if ($search_spk) {
            $this->db->like('sl.catatan', $search_spk);
        } else {
            $this->db->where('DATE(sl.created_at) >=', $start_date);
            $this->db->where('DATE(sl.created_at) <=', $end_date);
        }

        $this->db->where('sl.trx_type', 'Produksi');
        $this->db->order_by('sl.created_at', 'DESC');
        $logs = $this->db->get()->result();

        // 3. Ambil Update Produksi Terkini (Step-by-step update)
        // Kita gunakan COALESCE karena operator_id bisa merujuk ke karyawan_id (Pribadi) 
        // atau ke operator_id/divisi_id (Role/Grup Mesin). 
        // Prioritaskan nama Operator/Station (o.operator_nama) daripada nama Karyawan (k.nama_lengkap)
        // karena ID kecil (1, 2, dst) seringkali bertabrakan antara ID Karyawan dan ID Station.
        $this->db->select('dp.*, COALESCE(o.operator_nama, k.nama_lengkap, "System") as operator_nama, r.routing_name, tp.nospk, prod.product_nama, tp.status_produksi as status_utama, s.satuan_nama, con.konsumen_nama, cab.perusahaan_alias as lokasi_cetak');
        $this->db->from('detail_produksi dp');
        $this->db->join('tbl_operator o', 'dp.operator_id = o.operator_id', 'left');
        $this->db->join('tbl_karyawan k', 'dp.operator_id = k.karyawan_id', 'left');
        $this->db->join('routing r', 'dp.routing_id = r.routing_id', 'left');
        $this->db->join('tbl_produksi tp', 'dp.produksi_id = tp.produksi_id', 'left');
        $this->db->join('tbl_product prod', 'tp.product_id = prod.product_id', 'left');
        $this->db->join('tbl_satuan s', 'prod.satuan_id = s.satuan_id', 'left');
        $this->db->join('tbl_pemesanan pms', 'pms.nospk = tp.nospk', 'left');
        $this->db->join('tbl_konsumen con', 'con.konsumen_id = pms.konsumen_id', 'left');
        $this->db->join('tbl_perusahaan cab', 'cab.id_perusahaan = pms.perusahaan_id', 'left');

        if (!in_array($user_level, [1, 17])) {
            $this->db->where('pms.perusahaan_id', $perusahaan_id);
        }

        if ($search_spk) {
            $this->db->where('tp.nospk', $search_spk);
            $this->db->limit(200);
        } else {
            $this->db->where('DATE(dp.update_time) >=', $start_date);
            $this->db->where('DATE(dp.update_time) <=', $end_date);
            $this->db->limit(100);
        }

        $this->db->order_by('dp.update_time', 'DESC');
        $production_updates = $this->db->get()->result();

        foreach ($production_updates as &$pu) {
            $pu->nospk_encrypted = urlencode(encrypt_spk($pu->nospk));
        }

        // 4. Ambil Daftar Material untuk Modal Koreksi
        $this->db->select('m.*, b.bahan_nama');
        $this->db->from('tbl_material m');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id', 'left');
        $this->db->where('m.status', '1');
        $this->db->order_by('b.bahan_nama', 'ASC');
        $materials = $this->db->get()->result();

        return [
            'daily_usage' => $daily_usage,
            'logs' => $logs,
            'production_updates' => $production_updates,
            'materials' => $materials,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'search_spk' => $search_spk
        ];
    }

    public function update_koreksi_produksi()
    {
        $id = $this->input->post('detail_produksi_id', true);
        $qty_input = $this->input->post('qty_input', true);
        $qty_output = $this->input->post('qty_output', true);
        $qty_pakai = (float) $this->input->post('qty_bahan_pakai', true);
        $qty_rusak = (float) $this->input->post('qty_bahan_rusak', true);
        $material_id = $this->input->post('material_id', true);
        $catatan = $this->input->post('catatan', true);

        $this->db->trans_start();

        // 1. Ambil data lama untuk audit/log reversal jika perlu
        $old = $this->db->get_where('detail_produksi', ['detail_produksi_id' => $id])->row();

        // 2. Update Detail Produksi
        $update_data = [
            'qty_input' => $qty_input,
            'qty_output' => $qty_output,
            'qty_bahan_pakai' => $qty_pakai,
            'qty_bahan_rusak' => $qty_rusak,
            'material_id' => !empty($material_id) ? $material_id : NULL,
            'catatan' => $catatan . ' (Koreksi Admin: ' . $this->session->userdata('username') . ')'
        ];

        $this->db->where('detail_produksi_id', $id);
        $this->db->update('detail_produksi', $update_data);

        // 3. Logic Re-stocking (Jika Qty Pakai/Rusak berubah, kita hapus log lama & buat baru)
        if ($old && ($old->qty_bahan_pakai != $qty_pakai || $old->qty_bahan_rusak != $qty_rusak || $old->material_id != $material_id)) {
            // Hapus log stok lama terkait step ini
            $this->db->where('trx_type', 'Produksi');
            $this->db->where('trx_id', $old->produksi_id);
            $this->db->like('catatan', '(Step: ' . $id . ')');
            $old_logs = $this->db->get('tbl_stok_log')->result();

            foreach ($old_logs as $ol) {
                // Kembalikan stok lama (Reverse)
                $this->M_gudang->update_stock_with_log(
                    $ol->material_id,
                    $ol->gudang_id,
                    $ol->qty_out, // Tambahkan kembali qty_out
                    'Adjustment',
                    $id,
                    'Reversal Koreksi Admin SPK: ' . $id
                );
                $this->db->delete('tbl_stok_log', ['log_id' => $ol->log_id]);
            }

            // Buat log baru jika material_id dan qty baru valid
            if (!empty($material_id) && ($qty_pakai > 0 || $qty_rusak > 0)) {
                $this->load->model('M_produksi');

                // 1. Get material info for conversion to ensure inventory accuracy (e.g. Sheets -> Rim, Meter -> Roll)
                $mat = $this->db->select('m.*, b.bahan_nama, s.satuan_nama as satuan_produksi')
                    ->from('tbl_material m')
                    ->join('tbl_bahan b', 'm.bahan_id = b.bahan_id', 'left')
                    ->join('tbl_satuan s', 'b.satuan_id = s.satuan_id', 'left')
                    ->where('m.material_id', $material_id)
                    ->get()->row();

                $konversi = ($mat && (float) $mat->konversi > 0) ? (float) $mat->konversi : 1;
                $sat_prod = ($mat && $mat->satuan_produksi) ? $mat->satuan_produksi : 'Unit';

                // Kita asumsikan gudang 2 (Gudang Produksi) atau ambil dari sistem
                $gudang_id = 2; // Default produksi biasanya di gudang 2
                $total_reduced_prod = $qty_pakai + $qty_rusak;

                // CONVERSION: Produksi (Sheets/Meters) -> Gudang (RIM/ROLL)
                $warehouse_reduction = $total_reduced_prod / $konversi;

                // Ambil info SPK untuk catatan log
                $tp = $this->db->get_where('tbl_produksi', ['produksi_id' => $old->produksi_id])->row();
                $final_catatan = "Produksi SPK: " . ($tp ? $tp->nospk : 'N/A') . ". Pakai: $qty_pakai $sat_prod. Rusak: $qty_rusak $sat_prod (Koreksi Admin Step: $id)";

                $this->M_gudang->update_stock_with_log(
                    $material_id,
                    $gudang_id,
                    -$warehouse_reduction, // Potong stok baru dalam unit GUDANG
                    'Produksi',
                    $old->produksi_id,
                    $final_catatan
                );
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'Gagal melakukan koreksi produksi.');
        } else {
            $this->session->set_flashdata('success', 'Koreksi produksi berhasil disimpan.');
        }

        redirect('administrative/monitoring_produksi');
    }
}
