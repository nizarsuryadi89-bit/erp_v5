<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('whatsapp');
        $this->load->model('M_laporan');
        $this->load->model('M_pembayaran');
        $this->load->model('M_master');
        $this->load->model('M_hutang');
        $this->load->model('M_transaksi');
        $this->load->model('M_penjualan');
        $this->load->model('KasirShift_model');
    }

    public function send_piutang_wa()
    {
        header('Content-Type: application/json');
        $nospk = $this->input->post('nospk', true);
        $nama = $this->input->post('nama', true);
        $nohp = $this->input->post('nohp', true);
        $sisa = $this->input->post('sisa', true);
        $tempo = $this->input->post('tempo', true);

        if (empty($nohp)) {
            echo json_encode(['success' => false, 'message' => 'Nomor HP tidak ada.']);
            return;
        }

        $sisa_format = number_format($sisa, 0, ',', '.');

        $pesan = "*PENGINGAT TAGIHAN - TGM PRINT*\n\n";
        $pesan .= "Halo Bapak/Ibu *$nama*,\n";
        $pesan .= "Kami menginformasikan bahwa terdapat tagihan piutang yang belum terselesaikan:\n\n";
        $pesan .= "No. SPK: *$nospk*\n";
        $pesan .= "Sisa Tagihan: *Rp $sisa_format*\n";
        $pesan .= "Jatuh Tempo: *$tempo*\n\n";
        $pesan .= "Mohon kesediaannya untuk melakukan penyelesaian pembayaran melalui kasir kami atau transfer bank. Jika sudah melakukan pembayaran, mohon abaikan pesan ini.\n\n";
        $pesan .= "Terima kasih atas kerja samanya.\n";
        $pesan .= "_Pesan otomatis via ERP TGM Print_";

        $response = send_wa($nohp, $pesan);

        if ($response && isset($response['status']) && $response['status'] == true) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $response['reason'] ?? 'Gagal mengirim pesan']);
        }
    }

    private function _get_ids($perusahaan_id)
    {
        if ($perusahaan_id == 1 || empty($perusahaan_id)) {
            $companies = $this->db->select('id_perusahaan')->get_where('tbl_perusahaan', ['id_perusahaan >=' => 1])->result();
            $ids = [];
            foreach ($companies as $c) {
                $ids[] = $c->id_perusahaan;
            }
            return !empty($ids) ? $ids : [1, 2, 3];
        }
        return [(int) $perusahaan_id];
    }

    /**
     * Helper to prepare common view data used across methods
     */
    private function _prepare_common_data($title, $function = null)
    {
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: $this->session->userdata('perusahaan_id');
        $user_level = $this->session->userdata('level');
        $is_admin = ($user_level == 1);
        $is_owner = ($user_level == 17);

        // Get company info
        $perusahaan_name = 'TGM ERP System';
        $p_info = $this->db->select('nama_perusahaan')->get_where('tbl_perusahaan', array('id_perusahaan' => $perusahaan_id))->row_array();
        if ($p_info) {
            $perusahaan_name = $p_info['nama_perusahaan'];
        }

        return array(
            'controller' => $this->uri->segment(1),
            'function' => $function ?: ($this->uri->segment(2) ?: 'Dashboard'),
            'title' => $title,
            'perusahaan_id' => $perusahaan_id, // Standardize variable name
            'current_perusahaan_id' => $perusahaan_id,
            'perusahaan' => $perusahaan_name,
            'is_admin_owner' => ($is_admin || $is_owner),
            'all_perusahaan' => ($is_admin || $is_owner) ? $this->db->get_where('tbl_perusahaan', ['status' => '1'])->result() : $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->result()
        );
    }

    public function index()
    {
        // Use helper for consolidated data
        $data = $this->_prepare_common_data('Dashboard Laporan Keuangan', 'Dashboard');
        $perusahaan_id = $data['perusahaan_id'];
        $ids = $this->_get_ids($perusahaan_id);

        $bulanini = date('Y-m');
        $hariini = date('Y-m-d');
        $kemarin = date('Y-m-d', strtotime('-1 day'));

        // Get financial overview
        $laba_rugi = $this->M_laporan->getLabaRugi($perusahaan_id, $bulanini)->row_array();
        $pengeluaran_bulanan = $this->M_laporan->pengeluaran_bulanan($perusahaan_id, $bulanini)->row_array();

        // Optimized: Get combined daily income (Transaction + Piutang)
        $ids_sql = implode(',', $ids);

        // Income Today & Yesterday (Using tbl_pembayaran_detail for all payments including piutang)
        $total_pend_hariini = $this->db->query("SELECT SUM(pd.jumlah) as total FROM tbl_pembayaran_detail pd JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id WHERE DATE(pd.created_at) = ? AND p.perusahaan_id IN ($ids_sql)", [$hariini])->row()->total ?? 0;
        $total_pend_kemarin = $this->db->query("SELECT SUM(pd.jumlah) as total FROM tbl_pembayaran_detail pd JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id WHERE DATE(pd.created_at) = ? AND p.perusahaan_id IN ($ids_sql)", [$kemarin])->row()->total ?? 0;

        // Piutang Today (Calculated from same source as payments after initial SPK date)
        $total_piutang_hari_ini = $this->db->query("SELECT SUM(pd.jumlah) as total FROM tbl_pembayaran_detail pd JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id WHERE DATE(pd.created_at) = ? AND DATE(pd.created_at) > p.tgl_bayar AND p.perusahaan_id IN ($ids_sql)", [$hariini])->row()->total ?? 0;

        // Expenses Today & Yesterday
        $pengeluaran_stats = $this->db->select("
                SUM(CASE WHEN pengeluaran_tgl = '$hariini' THEN pengeluaran_jumlah ELSE 0 END) as hari_ini,
                SUM(CASE WHEN pengeluaran_tgl = '$kemarin' THEN pengeluaran_jumlah ELSE 0 END) as kemarin
            ")
            ->from('tbl_pengeluaran')
            ->where_in('pengeluaran_tgl', [$hariini, $kemarin])
            ->where_in('perusahaan_id', $ids)
            ->get()
            ->row_array();

        // Additional Dashboard Stats
        $data['total_job'] = $this->db->count_all('tbl_flow_diagram');
        $data['total_produksi'] = $this->db->where('status_transaksi', 'Masuk Proses Produksi')->count_all_results('tbl_flow_diagram');
        $data['total_done'] = $this->db->where('status_transaksi', 'Produksi Selesai, Barang di Bagian PB')->count_all_results('tbl_flow_diagram');

        // Merge Financial Stats
        $data = array_merge($data, [
            'content' => 'admin/laporan/index',
            'total_pendapatan' => $total_pend_hariini,
            'total_pendapatan_kemarin' => $total_pend_kemarin,
            'total_laba' => $laba_rugi['laba'] ?? 0,
            'total_hpp' => $laba_rugi['total_hpp'] ?? 0,
            'total_pengeluaran' => $pengeluaran_stats['hari_ini'] ?? 0,
            'total_pengeluaran_kemarin' => $pengeluaran_stats['kemarin'] ?? 0,
            'total_pengeluaran_bulan_ini' => $pengeluaran_bulanan['pengeluaran_bulanan'] ?? 0,
            'total_piutang_hari_ini' => $total_piutang_hari_ini,
            'periode' => $bulanini,
        ]);

        $this->load_admin_view('admin/laporan/index', $data);
    }

    public function laba_rugi()
    {
        $data = $this->_prepare_common_data('Laporan Laba Rugi', 'Laba Rugi');
        $perusahaan_id = $data['perusahaan_id'];
        $bulanini = $this->input->get('bulan', true) ?: date('Y-m');

        // Fetch Report Data
        $laba_rugi = $this->M_laporan->getLabaRugi($perusahaan_id, $bulanini); // Object

        $data = array_merge($data, [
            'content' => 'admin/laporan/laba_rugi',
            'laba_rugi' => $laba_rugi->row_array(),
            'pendapatan' => $this->M_laporan->total_pendapatan($perusahaan_id, $bulanini)->row_array(),
            'pengeluaran_bulanan' => $this->M_laporan->pengeluaran_bulanan($perusahaan_id, $bulanini)->row_array(),
            'pengeluaran_harian' => $this->M_laporan->pengeluaran($perusahaan_id)->row_array(),
            'pemasukan_bulanan' => $this->M_laporan->pemasukan_bulanan($perusahaan_id, $bulanini)->row_array(),
            'pengeluaran_harian_list' => $this->M_laporan->pengeluaran_harian_list($perusahaan_id, $bulanini)->result(),
            'pengeluaran_bulanan_list' => $this->M_laporan->pengeluaran_bulanan_list($perusahaan_id, $bulanini)->result(),
            'chart_data' => $this->M_laporan->get_daily_laba_rugi($perusahaan_id, $bulanini)->result(),
            'periode' => $bulanini
        ]);

        $this->load_admin_view('admin/laporan/laba_rugi', $data);
    }

    public function pemasukan_harian()
    {
        $data = $this->_prepare_common_data('Laporan Keuangan Harian Detail');
        $perusahaan_id = $data['perusahaan_id'];
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);
        $hariini = $this->input->get('tanggal', true) ?: date('Y-m-d');
        $userid = $this->session->userdata('id');
        $this->load->model('M_transaksi');

        // --- KONSEP AKUNTANSI: MEMISAHKAN OMSET (SALES) VS ARUS KAS (CASH FLOW) ---

        // 1. DATA TRANSAKSI HARI INI (Untuk list view & Omset)
        $where_kasir = ($this->session->userdata('level') == 3) ? " AND p.kasir_id = '$userid'" : "";

        $pemasukan_list = $this->db->query("
                SELECT p.tgl_bayar as tanggal, p.jam_bayar as jam, p.nospk, 
                       k.konsumen_nama as konsumen, k.konsumen_nohp as telepon,
                       p.total_tagihan, 
                       p.total_bayar as bayar_hari_ini,
                       (p.total_tagihan - p.total_bayar) as sisa_piutang,
                       u.username as kasir,
                       CASE WHEN p.status_lunas = '1' THEN 'Lunas' ELSE 'DP/Termin' END as status_transaksi,
                       CASE WHEN p.nospk LIKE 'MAN-%' THEN 'Manual' ELSE 'SPK' END as jenis_label
                FROM tbl_pembayaran p
                LEFT JOIN tbl_konsumen k ON p.konsumen_id = k.konsumen_id
                LEFT JOIN users u ON p.kasir_id = u.user_id
                WHERE p.tgl_bayar = ? AND p.perusahaan_id IN ($ids_sql) $where_kasir
                ORDER BY p.tgl_bayar DESC, p.jam_bayar DESC
            ", [$hariini])->result_array();

        // 2. ARUS KAS MASUK (SEMUA PEMBAYARAN HARI INI)
        $transaksi_langsung = $this->db->query("
                SELECT pd.metode, SUM(pd.jumlah) as total 
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                WHERE DATE(pd.created_at) = ? AND p.perusahaan_id IN ($ids_sql)
                GROUP BY pd.metode
            ", [$hariini])->result_array();

        // --- BREAKDOWN PER METODE (Untuk Sub-Row di View) ---

        // 2b. Breakdown Transfer Bank (Transaksi Langsung + Pelunasan Piutang)
        $breakdown_transfer = $this->db->query("
                SELECT 
                    COALESCE(r.nama_bank, 'Tidak Terdeteksi') as nama_bank, 
                    COALESCE(r.no_rekening, '-') as no_rekening, 
                    SUM(pd.jumlah) as total 
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                LEFT JOIN tbl_rekening r ON pd.bank_id = r.id
                WHERE DATE(pd.created_at) = ? 
                  AND p.perusahaan_id IN ($ids_sql) AND pd.metode = 'transfer'
                GROUP BY pd.bank_id
            ", [$hariini])->result_array();

        // 2c. Breakdown EDC / Debit (Transaksi Langsung + Pelunasan Piutang)
        $breakdown_edc = $this->db->query("
                SELECT me.edc_nama, SUM(pd.jumlah) as total
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                LEFT JOIN tbl_mesin_edc me ON pd.edc_id = me.edc_id
                WHERE DATE(pd.created_at) = ? 
                  AND p.perusahaan_id IN ($ids_sql) AND pd.metode = 'debit'
                GROUP BY pd.edc_id
            ", [$hariini])->result_array();

        // 2d. Breakdown E-Wallet (Transaksi Langsung + Pelunasan Piutang)
        $breakdown_ewallet = $this->db->query("
                SELECT e.ewallet_nama, SUM(pd.jumlah) as total
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                LEFT JOIN tbl_ewallet e ON pd.ewallet_id = e.id
                WHERE DATE(pd.created_at) = ? 
                  AND p.perusahaan_id IN ($ids_sql) AND pd.metode = 'ewallet'
                GROUP BY pd.ewallet_id
            ", [$hariini])->result_array();

        // --- PIUTANG BREAKDOWN ---

        // 3a. Rekap Piutang per Metode
        $rekap_piutang = $this->db->query("
                SELECT pd.metode, SUM(pd.jumlah) as total 
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                WHERE DATE(pd.created_at) = ? AND DATE(pd.created_at) > p.tgl_bayar 
                  AND p.perusahaan_id IN ($ids_sql)
                GROUP BY pd.metode
            ", [$hariini])->result_array();

        // 3b. Breakdown Transfer (Pelunasan Piutang)
        $breakdown_transfer_piutang = $this->db->query("
                SELECT r.nama_bank, r.no_rekening, SUM(pd.jumlah) as total 
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                LEFT JOIN tbl_rekening r ON pd.bank_id = r.id
                WHERE DATE(pd.created_at) = ? AND DATE(pd.created_at) > p.tgl_bayar 
                  AND p.perusahaan_id IN ($ids_sql) AND pd.metode = 'transfer'
                GROUP BY pd.bank_id
            ", [$hariini])->result_array();

        // 3c. Breakdown EDC (Pelunasan Piutang)
        $breakdown_edc_piutang = $this->db->query("
                SELECT me.edc_nama, SUM(pd.jumlah) as total
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                LEFT JOIN tbl_mesin_edc me ON pd.edc_id = me.edc_id
                WHERE DATE(pd.created_at) = ? AND DATE(pd.created_at) > p.tgl_bayar 
                  AND p.perusahaan_id IN ($ids_sql) AND pd.metode = 'debit'
                GROUP BY pd.edc_id
            ", [$hariini])->result_array();

        // 3d. Breakdown E-Wallet (Pelunasan Piutang)
        $breakdown_ewallet_piutang = $this->db->query("
                SELECT e.ewallet_nama, SUM(pd.jumlah) as total
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                LEFT JOIN tbl_ewallet e ON pd.ewallet_id = e.id
                WHERE DATE(pd.created_at) = ? AND DATE(pd.created_at) > p.tgl_bayar 
                  AND p.perusahaan_id IN ($ids_sql) AND pd.metode = 'ewallet'
                GROUP BY pd.ewallet_id
            ", [$hariini])->result_array();

        // 4. PENGELUARAN (Expense)
        // Modified to distinguish Cash vs Non-Cash expenses for accurate Cash-in-Hand calculation
        $pengeluaran_harian = $this->db->query("
                SELECT 
                    SUM(pengeluaran_jumlah) as total,
                    SUM(CASE 
                        WHEN metode_bayar = 'tunai' THEN pengeluaran_jumlah
                        WHEN metode_bayar IS NULL AND (rekening_id IS NULL OR rekening_id = 0) THEN pengeluaran_jumlah 
                        ELSE 0 
                    END) as total_tunai
                FROM tbl_pengeluaran 
                WHERE pengeluaran_tgl = ? AND perusahaan_id IN ($ids_sql)
            ", [$hariini])->row_array();

        // 4b. DETAIL PENGELUARAN (List)
        $list_pengeluaran = $this->db->query("
            SELECT 
                p.id as pengeluaran_id,
                p.pengeluaran_tgl,
                p.pengeluaran_jumlah,
                p.pengeluaran_detail,
                p.pengeluaran_penerima,
                p.metode_bayar,
                p.rekening_tujuan,
                r.nama_bank as bank_asal,
                r.no_rekening as norek_asal
            FROM tbl_pengeluaran p
            LEFT JOIN tbl_rekening r ON p.rekening_id = r.id
            WHERE p.pengeluaran_tgl = ? AND p.perusahaan_id IN ($ids_sql)
            ORDER BY p.id ASC
        ", [$hariini])->result_array();

        // 5. DATA PIUTANG BARU HARI INI
        $piutang_baru = $this->db->query("
            SELECT SUM(total_tagihan - total_bayar) as total 
            FROM tbl_pembayaran 
            WHERE tgl_bayar = ? AND perusahaan_id IN ($ids_sql) AND status_lunas = '0'
        ", [$hariini])->row_array();


        // --- KONSOLIDASI DATA UNTUK LAPORAN ---

        $summary = [
            'cash' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
            'transfer' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
            'debit' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
            'ewallet' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
            'lainnya' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
        ];

        // Mapping Transaction Payments
        foreach ($transaksi_langsung as $t) {
            $key = strtolower($t['metode'] ?? 'lainnya');
            if ($key == 'tunai')
                $key = 'cash';
            if ($key == 'edc')
                $key = 'debit';

            if (isset($summary[$key])) {
                $summary[$key]['transaksi'] += $t['total'];
                $summary[$key]['total'] += $t['total'];
            } else {
                $summary['lainnya']['transaksi'] += $t['total'];
                $summary['lainnya']['total'] += $t['total'];
            }
        }

        // Mapping Piutang Payments (dari rekap_piutang)
        foreach ($rekap_piutang as $p) {
            $key = strtolower($p['metode'] ?? 'lainnya');
            if ($key == 'tunai')
                $key = 'cash';
            if ($key == 'edc')
                $key = 'debit';

            if (isset($summary[$key])) {
                $summary[$key]['piutang'] += $p['total'];
            }
        }

        // Hitung Saldo Akhir Fisik (Uang di Laci)
        // Corrected: Only subtract Cash Expenses, not Total Expenses
        $total_cash_in_hand = $summary['cash']['total'] - ($pengeluaran_harian['total_tunai'] ?? 0);

        // Hitung Total Sales (Omset) Hari Ini
        $total_omset_hari_ini = 0;
        $total_omset_manual = 0;
        foreach ($pemasukan_list as $pl) {
            $total_omset_hari_ini += $pl['total_tagihan'];
            if ($pl['jenis_label'] == 'Manual') {
                $total_omset_manual += $pl['total_tagihan'];
            }
        }

        $data = array_merge($data, [
            'hariini' => $hariini,
            'summary' => $summary,
            'pemasukan_list' => $pemasukan_list,
            'list_pengeluaran' => $list_pengeluaran,
            'breakdown_transfer' => $breakdown_transfer,
            'breakdown_transfer_piutang' => $breakdown_transfer_piutang,
            'breakdown_edc' => $breakdown_edc,
            'breakdown_edc_piutang' => $breakdown_edc_piutang,
            'breakdown_ewallet' => $breakdown_ewallet,
            'breakdown_ewallet_piutang' => $breakdown_ewallet_piutang,
            'stat_keuangan' => [
                'omset_penjualan' => $total_omset_hari_ini,
                'omset_manual' => $total_omset_manual,
                'total_uang_masuk' => array_sum(array_column($summary, 'total')),
                'piutang_baru_terbentuk' => $piutang_baru['total'] ?? 0,
                'total_pengeluaran' => $pengeluaran_harian['total'] ?? 0,
                'saldo_cash_fisik' => $total_cash_in_hand,
                'saldo_non_tunai' => (array_sum(array_column($summary, 'total')) - $summary['cash']['total'])
            ],
            'rekening' => $this->db->get('tbl_rekening')->result(),
            'ewallet_list' => $this->db->get('tbl_ewallet')->result(),
            'edc' => $this->db->get('tbl_mesin_edc')->result(),
        ]);

        $this->load_admin_view('admin/laporan/keuangan/laporan_pemasukan_harian', $data);
    }

    /**
     * Laporan Penjualan per Kategori
     */
    public function penjualan_kategori()
    {
        $data = $this->_prepare_common_data('Laporan Penjualan Kategori');
        $perusahaan_id = $data['perusahaan_id'];
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        $start_date = $this->input->get('start_date', true) ?: date('Y-m-01');
        $end_date = $this->input->get('end_date', true) ?: date('Y-m-t');

        // Mengambil data penjualan berdasarkan kategori (Raw details)
        $query = "
            SELECT 
                p.nospk,
                p.created_at as tgl_pesan,
                pr.product_nama,
                COALESCE(pd.snapshot_detail_product, hp.detail_product) as detail_product,
                COALESCE(k.kategori_nama, 'Tanpa Kategori') as kategori_nama,
                k.kategori_id,
                pd.qty,
                pd.panjang,
                pd.lebar,
                pd.snapshot_subtotal,
                pd.snapshot_harga,
                pd.metadata,
                pr.satuan_id,
                hp.harga_1,
                hp.ket_1
            FROM tbl_pemesanan p
            JOIN tbl_pemesanan_detail pd ON p.nospk = pd.nospk
            JOIN tbl_product pr ON pd.product_id = pr.product_id
            LEFT JOIN tbl_kategori k ON pr.kategori_id = k.kategori_id
            LEFT JOIN tbl_harga_product hp ON pd.harga_id = hp.harga_id
            WHERE DATE(p.created_at) >= ? AND DATE(p.created_at) <= ? 
            AND p.perusahaan_id IN ($ids_sql)
            AND p.status_pembayaran != 'canceled'
        ";

        $details = $this->db->query($query, [$start_date, $end_date])->result();

        $penjualan_kategori_map = [];

        foreach ($details as $dt) {
            $cat_name = $dt->kategori_nama;

            if (!isset($penjualan_kategori_map[$cat_name])) {
                $penjualan_kategori_map[$cat_name] = [
                    'kategori_nama' => $cat_name,
                    'total_qty' => 0,
                    'total_omset' => 0,
                    'transactions' => []
                ];
            }

            // Hitung subtotal dengan fallback persis seperti nota
            $subtotal = 0;

            if (isset($dt->snapshot_subtotal) && is_numeric($dt->snapshot_subtotal)) {
                $subtotal = (float) $dt->snapshot_subtotal;
            } else {
                $metadata = json_decode($dt->metadata, true);
                if (isset($metadata['subtotal']) && is_numeric($metadata['subtotal'])) {
                    $subtotal = (float) $metadata['subtotal'];
                } else {
                    // Kalkulasi Manual
                    $harga = 0;
                    if (isset($dt->snapshot_harga) && is_numeric($dt->snapshot_harga)) {
                        $harga = (float) $dt->snapshot_harga;
                    } elseif (isset($metadata['unit_price']) && is_numeric($metadata['unit_price'])) {
                        $harga = (float) $metadata['unit_price'];
                    } else {
                        $harga = (float) $dt->harga_1;
                    }

                    $qty = (float) $dt->qty;
                    $panjang = (float) $dt->panjang;
                    $lebar = (float) $dt->lebar;
                    $satuan_id = (int) $dt->satuan_id;
                    $kategori_id = (int) $dt->kategori_id;
                    $ket_1 = (string) $dt->ket_1;

                    if ($satuan_id === 11 || ($kategori_id === 9 && $ket_1 === '5')) {
                        $subtotal = $panjang * $harga * $qty;
                    } elseif ($panjang != 0 && $lebar != 0) {
                        $subtotal = $panjang * $lebar * $harga * $qty;
                    } else {
                        $subtotal = $harga * $qty;
                    }
                }
            }

            $penjualan_kategori_map[$cat_name]['total_qty'] += (float) $dt->qty;
            $penjualan_kategori_map[$cat_name]['total_omset'] += $subtotal;

            // Limit transactions in memory to prevent huge JSON overhead, store only top recent or just all if not too many
            // In a real heavy app we should paginate, but here we can just push
            $penjualan_kategori_map[$cat_name]['transactions'][] = [
                'nospk' => $dt->nospk,
                'tgl_pesan' => date('d M Y H:i', strtotime($dt->tgl_pesan)),
                'product_nama' => $dt->product_nama,
                'detail_product' => $dt->detail_product,
                'qty' => $dt->qty,
                'subtotal' => $subtotal
            ];
        }

        // Urutkan dari omset tertinggi
        usort($penjualan_kategori_map, function ($a, $b) {
            return $b['total_omset'] <=> $a['total_omset'];
        });

        $penjualan_kategori = $penjualan_kategori_map;

        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['penjualan_kategori'] = $penjualan_kategori;

        $this->load_admin_view('admin/laporan/keuangan/laporan_penjualan_kategori', $data);
    }

    /**
     * Laporan pemasukan bulanan
     */
    /**
     * Laporan pemasukan bulanan
     */
    public function pemasukan_bulanan()
    {
        $data = $this->_prepare_common_data('Laporan Keuangan Bulanan Detail');
        $perusahaan_id = $data['perusahaan_id'];
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        $bulan_ini = $this->input->get('bulan', true) ?: date('Y-m');
        $start_date = $bulan_ini . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));
        $userid = $this->session->userdata('id');

        $this->load->model('M_transaksi');

        // --- KONSEP AKUNTANSI MODERN: OMSET VS CASH FLOW ---

        // 1. DATA TRANSAKSI BULAN INI (Untuk list & Omset)
        $pemasukan_list = $this->db->query("
                SELECT p.tgl_bayar as tanggal, p.jam_bayar as jam, p.nospk, 
                       k.konsumen_nama as konsumen, k.konsumen_nohp as telepon,
                       p.total_tagihan, 
                       p.total_bayar as bayar_hari_ini,
                       (p.total_tagihan - p.total_bayar) as sisa_piutang,
                       u.username as kasir,
                       CASE WHEN p.status_lunas = '1' THEN 'Lunas' ELSE 'DP/Termin' END as status_transaksi
                FROM tbl_pembayaran p
                LEFT JOIN tbl_konsumen k ON p.konsumen_id = k.konsumen_id
                LEFT JOIN users u ON p.kasir_id = u.user_id
                WHERE (p.tgl_bayar BETWEEN ? AND ?) AND p.perusahaan_id IN ($ids_sql)
                ORDER BY p.tgl_bayar DESC, p.jam_bayar DESC
            ", [$start_date, $end_date])->result_array();

        // 2. ARUS KAS MASUK 1: DARI TRANSAKSI/PENJUALAN BARU
        $transaksi_langsung = $this->db->query("
                SELECT pd.metode, SUM(pd.jumlah) as total 
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                WHERE (p.tgl_bayar BETWEEN ? AND ?) AND p.perusahaan_id IN ($ids_sql)
                GROUP BY pd.metode
            ", [$start_date, $end_date])->result_array();

        // 2b. Breakdown Transfer Bank (Transaksi)
        $breakdown_transfer = $this->db->query("
                SELECT r.nama_bank, r.no_rekening, SUM(pd.jumlah) as total 
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                LEFT JOIN tbl_rekening r ON pd.bank_id = r.id
                WHERE (p.tgl_bayar BETWEEN ? AND ?) AND p.perusahaan_id IN ($ids_sql) AND pd.metode = 'transfer'
                GROUP BY pd.bank_id
            ", [$start_date, $end_date])->result_array();

        // 2c. Breakdown EDC / Debit
        $breakdown_edc = $this->db->query("
                SELECT me.edc_nama, SUM(pd.jumlah) as total
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                LEFT JOIN tbl_mesin_edc me ON pd.edc_id = me.edc_id
                WHERE (p.tgl_bayar BETWEEN ? AND ?) AND p.perusahaan_id IN ($ids_sql) AND pd.metode = 'debit'
                GROUP BY pd.edc_id
            ", [$start_date, $end_date])->result_array();

        // 2d. Breakdown E-Wallet (Transaksi) - New
        $breakdown_ewallet = $this->db->query("
                SELECT e.ewallet_nama, SUM(pd.jumlah) as total
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                LEFT JOIN tbl_ewallet e ON pd.ewallet_id = e.id
                WHERE (p.tgl_bayar BETWEEN ? AND ?) AND p.perusahaan_id IN ($ids_sql) AND pd.metode = 'ewallet'
                GROUP BY pd.ewallet_id
            ", [$start_date, $end_date])->result_array();

        // 2. ARUS KAS MASUK (SEMUA PEMBAYARAN)
        $transaksi_langsung = $this->db->query("
                SELECT pd.metode, SUM(pd.jumlah) as total 
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                WHERE (DATE(pd.created_at) BETWEEN ? AND ?) AND p.perusahaan_id IN ($ids_sql)
                GROUP BY pd.metode
            ", [$start_date, $end_date])->result_array();

        // 2b. Breakdown Transfer Bank
        $breakdown_transfer = $this->db->query("
                SELECT r.nama_bank, r.no_rekening, SUM(pd.jumlah) as total 
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                LEFT JOIN tbl_rekening r ON pd.bank_id = r.id
                WHERE (DATE(pd.created_at) BETWEEN ? AND ?) AND p.perusahaan_id IN ($ids_sql) AND pd.metode = 'transfer'
                GROUP BY pd.bank_id
            ", [$start_date, $end_date])->result_array();

        // 2c. Breakdown EDC / Debit
        $breakdown_edc = $this->db->query("
                SELECT me.edc_nama, SUM(pd.jumlah) as total
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                LEFT JOIN tbl_mesin_edc me ON pd.edc_id = me.edc_id
                WHERE (DATE(pd.created_at) BETWEEN ? AND ?) AND p.perusahaan_id IN ($ids_sql) AND pd.metode = 'debit'
                GROUP BY pd.edc_id
            ", [$start_date, $end_date])->result_array();

        // 2d. Breakdown E-Wallet
        $breakdown_ewallet = $this->db->query("
                SELECT e.ewallet_nama, SUM(pd.jumlah) as total
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                LEFT JOIN tbl_ewallet e ON pd.ewallet_id = e.id
                WHERE (DATE(pd.created_at) BETWEEN ? AND ?) AND p.perusahaan_id IN ($ids_sql) AND pd.metode = 'ewallet'
                GROUP BY pd.ewallet_id
            ", [$start_date, $end_date])->result_array();

        // Note: Pelunasan Piutang sudah tercover di tbl_pembayaran_detail.
        $rekap_piutang = $this->db->query("
                SELECT pd.metode, SUM(pd.jumlah) as total 
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                WHERE (DATE(pd.created_at) BETWEEN ? AND ?) AND DATE(pd.created_at) > p.tgl_bayar AND p.perusahaan_id IN ($ids_sql)
                GROUP BY pd.metode
            ", [$start_date, $end_date])->result_array();

        // 4. PENGELUARAN BULAN INI
        $pengeluaran_bulanan = $this->db->query("
                SELECT 
                    SUM(pengeluaran_jumlah) as total,
                    SUM(CASE 
                        WHEN metode_bayar = 'tunai' THEN pengeluaran_jumlah
                        WHEN metode_bayar IS NULL AND (rekening_id IS NULL OR rekening_id = 0) THEN pengeluaran_jumlah 
                        ELSE 0 
                    END) as total_tunai
                FROM tbl_pengeluaran 
                WHERE (pengeluaran_tgl BETWEEN ? AND ?) AND perusahaan_id IN ($ids_sql)
            ", [$start_date, $end_date])->row_array();

        // 5. PIUTANG BARU TERBENTUK BULAN INI (Sisa tagihan penjualan bulan ini)
        $piutang_baru = $this->db->query("
            SELECT SUM(total_tagihan - total_bayar) as total 
            FROM tbl_pembayaran 
            WHERE (tgl_bayar BETWEEN ? AND ?) AND perusahaan_id IN ($ids_sql) AND status_lunas = '0'
        ", [$start_date, $end_date])->row_array();


        // --- KONSOLIDASI DATA ---

        $summary = [
            'cash' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
            'transfer' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
            'debit' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
            'ewallet' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
            'lainnya' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
        ];

        // Mapping Transaction Payments
        foreach ($transaksi_langsung as $t) {
            $key = strtolower($t['metode'] ?? 'lainnya');
            if ($key == 'tunai')
                $key = 'cash';
            if ($key == 'edc')
                $key = 'debit';

            if (isset($summary[$key])) {
                $summary[$key]['transaksi'] += $t['total'];
                $summary[$key]['total'] += $t['total'];
            } else {
                $summary['lainnya']['transaksi'] += $t['total'];
                $summary['lainnya']['total'] += $t['total'];
            }
        }

        // Mapping Piutang Payments (dari rekap_piutang)
        foreach ($rekap_piutang as $p) {
            $key = strtolower($p['metode'] ?? 'lainnya');
            if ($key == 'tunai')
                $key = 'cash';
            if ($key == 'edc')
                $key = 'debit';

            if (isset($summary[$key])) {
                $summary[$key]['piutang'] += $p['total'];
            }
        }

        // Hitung Saldo Akhir Fisik (Uang di Laci)
        $total_cash_in_hand = $summary['cash']['total'] - ($pengeluaran_bulanan['total_tunai'] ?? 0);

        // Hitung Total Sales (Omset) Bulan Ini
        $total_omset = 0;
        foreach ($pemasukan_list as $pl) {
            $total_omset += $pl['total_tagihan'];
        }

        $data = array_merge($data, [
            'bulan_ini' => $bulan_ini,
            'bulan_akhir' => $end_date,
            'summary' => $summary,
            'pemasukan_list' => $pemasukan_list,
            'breakdown_transfer' => $breakdown_transfer,
            'breakdown_edc' => $breakdown_edc,
            'breakdown_ewallet' => $breakdown_ewallet,
            'stat_keuangan' => [
                'omset_penjualan' => $total_omset,
                'total_uang_masuk' => array_sum(array_column($summary, 'total')),
                'piutang_baru_terbentuk' => $piutang_baru['total'] ?? 0,
                'total_pengeluaran' => $pengeluaran_bulanan['total'] ?? 0,
                'saldo_cash_fisik' => $total_cash_in_hand,
                'saldo_non_tunai' => (array_sum(array_column($summary, 'total')) - $summary['cash']['total'])
            ],
            // Backward compatibility keys for view if not updated
            'cash' => ['totalna' => $summary['cash']['total']],
            'transfer' => ['totalna' => $summary['transfer']['total']],
            'debit' => ['totalna' => $summary['debit']['total']],
            'ewallet' => ['totalna' => $summary['ewallet']['total']],
        ]);

        $this->load_admin_view('admin/laporan/keuangan/laporan_pemasukan_bulanan', $data);
    }

    public function cetak_pemasukan_bulanan()
    {
        $data = $this->_prepare_common_data('Cetak Laporan Keuangan Bulanan');
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: $this->session->userdata('perusahaan_id');
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        $bulan_ini = $this->input->get('bulan', true) ?: date('Y-m');
        $start_date = $bulan_ini . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));

        // 1. ARUS KAS MASUK (SEMUA PEMBAYARAN)
        $transaksi_langsung = $this->db->query("
                SELECT pd.metode, SUM(pd.jumlah) as total 
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                WHERE (DATE(pd.created_at) BETWEEN ? AND ?) AND p.perusahaan_id IN ($ids_sql)
                GROUP BY pd.metode
            ", [$start_date, $end_date])->result_array();

        // 2. Breakdown Data
        $breakdown_transfer = $this->db->query("
                SELECT r.nama_bank, r.no_rekening, SUM(pd.jumlah) as total 
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                LEFT JOIN tbl_rekening r ON pd.bank_id = r.id
                WHERE (DATE(pd.created_at) BETWEEN ? AND ?) AND p.perusahaan_id IN ($ids_sql) AND pd.metode = 'transfer'
                GROUP BY pd.bank_id
            ", [$start_date, $end_date])->result_array();

        $breakdown_edc = $this->db->query("
                SELECT me.edc_nama, SUM(pd.jumlah) as total
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                LEFT JOIN tbl_mesin_edc me ON pd.edc_id = me.edc_id
                WHERE (DATE(pd.created_at) BETWEEN ? AND ?) AND p.perusahaan_id IN ($ids_sql) AND pd.metode = 'debit'
                GROUP BY pd.edc_id
            ", [$start_date, $end_date])->result_array();

        $breakdown_ewallet = $this->db->query("
                SELECT e.ewallet_nama, SUM(pd.jumlah) as total
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                LEFT JOIN tbl_ewallet e ON pd.ewallet_id = e.id
                WHERE (DATE(pd.created_at) BETWEEN ? AND ?) AND p.perusahaan_id IN ($ids_sql) AND pd.metode = 'ewallet'
                GROUP BY pd.ewallet_id
            ", [$start_date, $end_date])->result_array();

        // 3. Pelunasan Piutang (untuk identifikasi)
        $rekap_piutang = $this->db->query("
                SELECT pd.metode, SUM(pd.jumlah) as total 
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                WHERE (DATE(pd.created_at) BETWEEN ? AND ?) AND DATE(pd.created_at) > p.tgl_bayar AND p.perusahaan_id IN ($ids_sql)
                GROUP BY pd.metode
            ", [$start_date, $end_date])->result_array();

        // 4. PENGELUARAN BULAN INI (SUMMARY & LIST)
        $pengeluaran_bulanan = $this->db->query("
                SELECT 
                    SUM(pengeluaran_jumlah) as total,
                    SUM(CASE 
                        WHEN metode_bayar = 'tunai' THEN pengeluaran_jumlah
                        WHEN metode_bayar IS NULL AND (rekening_id IS NULL OR rekening_id = 0) THEN pengeluaran_jumlah 
                        ELSE 0 
                    END) as total_tunai
                FROM tbl_pengeluaran 
                WHERE (pengeluaran_tgl BETWEEN ? AND ?) AND perusahaan_id IN ($ids_sql)
            ", [$start_date, $end_date])->row_array();

        $list_pengeluaran = $this->db->query("
            SELECT 
                p.id as pengeluaran_id,
                p.pengeluaran_tgl,
                p.pengeluaran_jumlah,
                p.pengeluaran_detail,
                p.pengeluaran_penerima,
                p.metode_bayar,
                p.rekening_tujuan,
                r.nama_bank as bank_asal,
                r.no_rekening as norek_asal
            FROM tbl_pengeluaran p
            LEFT JOIN tbl_rekening r ON p.rekening_id = r.id
            WHERE (p.pengeluaran_tgl BETWEEN ? AND ?) AND p.perusahaan_id IN ($ids_sql)
            ORDER BY p.pengeluaran_tgl ASC
        ", [$start_date, $end_date])->result_array();

        // 5. PIUTANG BARU
        $piutang_baru = $this->db->query("
            SELECT SUM(total_tagihan - total_bayar) as total 
            FROM tbl_pembayaran 
            WHERE (tgl_bayar BETWEEN ? AND ?) AND perusahaan_id IN ($ids_sql) AND status_lunas = '0'
        ", [$start_date, $end_date])->row_array();

        // --- KONSOLIDASI DATA ---
        $summary = [
            'cash' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
            'transfer' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
            'debit' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
            'ewallet' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
            'lainnya' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
        ];

        // Mapping All Payments
        foreach ($transaksi_langsung as $t) {
            $key = strtolower($t['metode'] ?? 'lainnya');
            if ($key == 'tunai')
                $key = 'cash';
            if ($key == 'edc')
                $key = 'debit';

            if (isset($summary[$key])) {
                $summary[$key]['transaksi'] += $t['total']; // Simplification: we put everything in total first
                $summary[$key]['total'] += $t['total'];
            } else {
                $summary['lainnya']['transaksi'] += $t['total'];
                $summary['lainnya']['total'] += $t['total'];
            }
        }

        // Identify Piutang portion (just for display if needed)
        foreach ($rekap_piutang as $p) {
            $key = strtolower($p['metode'] ?? 'lainnya');
            if ($key == 'tunai')
                $key = 'cash';
            if ($key == 'edc')
                $key = 'debit';
            if (isset($summary[$key])) {
                $summary[$key]['piutang'] += $p['total'];
            }
        }

        $total_uang_masuk = array_sum(array_column($summary, 'total'));
        $total_cash_in_hand = $summary['cash']['total'] - ($pengeluaran_bulanan['total_tunai'] ?? 0);

        $data = array_merge($data, [
            'bulan_ini' => $bulan_ini,
            'summary' => $summary,
            'breakdown_transfer' => $breakdown_transfer,
            'breakdown_edc' => $breakdown_edc,
            'breakdown_ewallet' => $breakdown_ewallet,
            'list_pengeluaran' => $list_pengeluaran,
            'stat_keuangan' => [
                'total_uang_masuk' => $total_uang_masuk,
                'piutang_baru_terbentuk' => $piutang_baru['total'] ?? 0,
                'total_pengeluaran' => $pengeluaran_bulanan['total'] ?? 0,
                'saldo_cash_fisik' => $total_cash_in_hand,
            ]
        ]);

        $this->load->view('admin/laporan/keuangan/cetak_laporan_bulanan', $data);



    }

    public function laporan_pajak()
    {
        $userid = $this->session->userdata('id');
        // Get perusahaan_id from GET or default to session
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: $this->session->userdata('perusahaan_id');
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        $bulanini = $this->input->get('bulan', true) ?: date('Y-m');

        // Get filter parameters
        $tgl_awal = $this->input->get('tgl_awal', true);
        $tgl_akhir = $this->input->get('tgl_akhir', true);
        $search = $this->input->get('search', true);

        // Build WHERE clause (only include transactions that are already paid/lunas)
        $where_clause = "pb.perusahaan_id IN ($ids_sql)";
        if ($tgl_awal && $tgl_akhir) {
            $where_clause .= " AND pb.tgl_bayar BETWEEN '$tgl_awal' AND '$tgl_akhir'";
        } else {
            $start_date = $bulanini . '-01';
            $end_date = date('Y-m-t', strtotime($start_date));
            $where_clause .= " AND pb.tgl_bayar BETWEEN '$start_date' AND '$end_date'";
        }
        // Filter: only fully paid transactions (status_lunas = '1')
        $where_clause .= " AND pb.status_lunas = '1'";

        if ($search) {
            $search_escaped = $this->db->escape_like_str($search);
            $where_clause .= " AND (k.konsumen_nama LIKE '%$search_escaped%' OR pb.nospk LIKE '%$search_escaped%')";
        }

        // Get laporan pajak data grouped by SPK to avoid duplication if multiple payment methods
        // For 'Pajak di Gunggung', we need the gross total per SPK
        // Include payment method(s) per pembayaran. Use GROUP_CONCAT to handle multiple detail rows.
        $lap_pajak = $this->db->query(" 
                SELECT 
                    pb.pembayaran_id,
                    pb.nospk,
                    pb.tgl_bayar as tgl,
                    pb.jam_bayar as jam,
                    pb.total_bayar,
                    k.konsumen_nama,
                    k.konsumen_nohp,
                    ROUND(pb.total_bayar / 1.11, 0) AS dpp,
                    ROUND(pb.total_bayar - (pb.total_bayar / 1.11), 0) AS ppn,
                    COALESCE(GROUP_CONCAT(DISTINCT pd.metode SEPARATOR ', '), '-') AS metode
                FROM tbl_pembayaran pb
                LEFT JOIN tbl_konsumen k ON pb.konsumen_id = k.konsumen_id
                LEFT JOIN tbl_pembayaran_detail pd ON pd.pembayaran_id = pb.pembayaran_id
                WHERE $where_clause
                GROUP BY pb.pembayaran_id
                ORDER BY pb.tgl_bayar DESC, pb.jam_bayar DESC
            ")->result();

        // Get summary data
        $summary = $this->db->query("
                SELECT 
                    COUNT(nospk) AS jumlah_transaksi,
                    SUM(total_bayar) AS total_penjualan,
                    SUM(ROUND(total_bayar / 1.11, 0)) AS total_dpp,
                    SUM(ROUND(total_bayar - (total_bayar / 1.11), 0)) AS total_ppn
                FROM tbl_pembayaran pb
                LEFT JOIN tbl_konsumen k ON pb.konsumen_id = k.konsumen_id
                WHERE $where_clause
            ")->row();

        // Categorized by Payment Method (for auditing correlation)
        $by_method = $this->db->query("
                SELECT 
                    pd.metode,
                    SUM(pd.jumlah) as total,
                    SUM(ROUND(pd.jumlah / 1.11, 0)) as dpp,
                    SUM(ROUND(pd.jumlah - (pd.jumlah / 1.11), 0)) as ppn
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran pb ON pd.pembayaran_id = pb.pembayaran_id
                LEFT JOIN tbl_konsumen k ON pb.konsumen_id = k.konsumen_id
                WHERE $where_clause
                GROUP BY pd.metode
            ")->result_array();

        $user_level = $this->session->userdata('level');
        $is_admin = ($user_level == 1);
        $is_owner = ($user_level == 17);

        // Get company name
        $perusahaan_name = 'TGM ERP System';
        $p_info = $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $perusahaan_id))->row_array();
        if ($p_info) {
            $perusahaan_name = $p_info['nama_perusahaan'];
        }

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Laporan Pajak Bulanan',
            'perusahaan' => $perusahaan_name,
            'lap_pajak' => $lap_pajak,
            'summary' => $summary,
            'by_method' => $by_method,
            'bulanini' => $bulanini,
            'tgl_awal' => $tgl_awal,
            'tgl_akhir' => $tgl_akhir,
            'search' => $search,
            'current_perusahaan_id' => $perusahaan_id,
            'is_admin_owner' => ($is_admin || $is_owner),
            'all_perusahaan' => ($is_admin || $is_owner) ? $this->db->get_where('tbl_perusahaan', ['status' => '1'])->result() : $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->result()
        );

        $this->load_admin_view('admin/laporan/keuangan/laporan_pajak_bulanan', $data);
    }



    public function export_pajak()
    {
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: $this->session->userdata('perusahaan_id');
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        $bulanini = $this->input->get('bulan', true) ?: date('Y-m');
        $tgl_awal = $this->input->get('tgl_awal', true);
        $tgl_akhir = $this->input->get('tgl_akhir', true);
        $search = $this->input->get('search', true);

        // Build WHERE clause (only include transactions that are already paid/lunas)
        $where_clause = "pb.perusahaan_id IN ($ids_sql)";
        if ($tgl_awal && $tgl_akhir) {
            $where_clause .= " AND pb.tgl_bayar BETWEEN '$tgl_awal' AND '$tgl_akhir'";
        } else {
            $start_date = $bulanini . '-01';
            $end_date = date('Y-m-t', strtotime($start_date));
            $where_clause .= " AND pb.tgl_bayar BETWEEN '$start_date' AND '$end_date'";
        }
        $where_clause .= " AND pb.status_lunas = '1'";

        if ($search) {
            $search_escaped = $this->db->escape_like_str($search);
            $where_clause .= " AND (k.konsumen_nama LIKE '%$search_escaped%' OR pb.nospk LIKE '%$search_escaped%')";
        }

        // Include payment method in export as well
        $data = $this->db->query(" 
                SELECT 
                    pb.pembayaran_id,
                    pb.tgl_bayar,
                    pb.nospk,
                    k.konsumen_nama,
                    pb.total_bayar,
                    ROUND(pb.total_bayar / 1.11, 0) AS dpp,
                    ROUND(pb.total_bayar - (pb.total_bayar / 1.11), 0) AS ppn,
                    COALESCE(GROUP_CONCAT(DISTINCT pd.metode SEPARATOR ', '), '-') AS metode
                FROM tbl_pembayaran pb
                LEFT JOIN tbl_konsumen k ON pb.konsumen_id = k.konsumen_id
                LEFT JOIN tbl_pembayaran_detail pd ON pd.pembayaran_id = pb.pembayaran_id
                WHERE $where_clause
                GROUP BY pb.pembayaran_id
                ORDER BY pb.tgl_bayar ASC
            ")->result_array();

        // CSV Export Configuration
        $filename = "Laporan_Pajak_Gunggung_" . str_replace('-', '', $bulanini) . ".csv";

        // Clean output buffer to avoid corruption
        if (ob_get_level())
            ob_end_clean();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // Add BOM for Excel UTF-8 compatibility
        fputs($output, "\xEF\xBB\xBF");

        // Report Info
        $p_info = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row();
        $p_name = $p_info ? strtoupper($p_info->nama_perusahaan) : 'SEMUA CABANG';

        fputcsv($output, ['LAPORAN PAJAK (GUNGGUNG) - ' . $p_name]);
        fputcsv($output, ['PERIODE: ' . ($tgl_awal ? "$tgl_awal s/d $tgl_akhir" : date('F Y', strtotime($bulanini . '-01')))]);
        fputcsv($output, []); // Empty Row

        // Headers
        fputcsv($output, ['TANGGAL', 'NOMOR SPK', 'KONSUMEN', 'METODE BAYAR', 'DPP (DASAR PENGENAAN)', 'PPN (11%)', 'TOTAL (GROSS)']);

        // Data Rows
        $total_dpp = 0;
        $total_ppn = 0;
        $total_gross = 0;

        foreach ($data as $row) {
            fputcsv($output, [
                $row['tgl_bayar'],
                "'" . $row['nospk'], // Force string in Excel
                $row['konsumen_nama'],
                $row['metode'],
                $row['dpp'],
                $row['ppn'],
                $row['total_bayar']
            ]);

            $total_dpp += $row['dpp'];
            $total_ppn += $row['ppn'];
            $total_gross += $row['total_bayar'];
        }

        // Summary Row
        fputcsv($output, []);
        fputcsv($output, ['', '', 'GRAND TOTAL', $total_dpp, $total_ppn, $total_gross]);

        fclose($output);
        exit;
    }

    public function export_pajak_spreadsheet()
    {
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: $this->session->userdata('perusahaan_id');
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        $bulanini = $this->input->get('bulan', true) ?: date('Y-m');
        $tgl_awal = $this->input->get('tgl_awal', true);
        $tgl_akhir = $this->input->get('tgl_akhir', true);
        $search = $this->input->get('search', true);

        // Build WHERE clause (only include transactions that are already paid/lunas)
        $where_clause = "pb.perusahaan_id IN ($ids_sql)";
        if ($tgl_awal && $tgl_akhir) {
            $where_clause .= " AND pb.tgl_bayar BETWEEN '$tgl_awal' AND '$tgl_akhir'";
        } else {
            $start_date = $bulanini . '-01';
            $end_date = date('Y-m-t', strtotime($start_date));
            $where_clause .= " AND pb.tgl_bayar BETWEEN '$start_date' AND '$end_date'";
        }
        $where_clause .= " AND pb.status_lunas = '1'";

        if ($search) {
            $search_escaped = $this->db->escape_like_str($search);
            $where_clause .= " AND (k.konsumen_nama LIKE '%$search_escaped%' OR pb.nospk LIKE '%$search_escaped%')";
        }

        // Include payment method for spreadsheet export
        $data = $this->db->query(" 
                SELECT 
                    pb.pembayaran_id,
                    pb.tgl_bayar,
                    pb.nospk,
                    k.konsumen_nama,
                    pb.total_bayar,
                    ROUND(pb.total_bayar / 1.11, 0) AS dpp,
                    ROUND(pb.total_bayar - (pb.total_bayar / 1.11), 0) AS ppn,
                    COALESCE(GROUP_CONCAT(DISTINCT pd.metode SEPARATOR ', '), '-') AS metode
                FROM tbl_pembayaran pb
                LEFT JOIN tbl_konsumen k ON pb.konsumen_id = k.konsumen_id
                LEFT JOIN tbl_pembayaran_detail pd ON pd.pembayaran_id = pb.pembayaran_id
                WHERE $where_clause
                GROUP BY pb.pembayaran_id
                ORDER BY pb.tgl_bayar ASC
            ")->result_array();

        // Load company name
        $p_info = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row();
        $p_name = $p_info ? strtoupper($p_info->nama_perusahaan) : 'SEMUA CABANG';

        // Load PHPExcel directly
        require_once APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $objPHPExcel = new PHPExcel();

        // Set properties
        $objPHPExcel->getProperties()->setTitle("Laporan Pajak Gunggung - " . $p_name);
        $objPHPExcel->getProperties()->setSubject("Pajak Bulanan");

        // Create worksheet
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setTitle('Laporan Pajak');

        // Header Style
        $headerStyle = array(
            'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '1E293B')),
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
        );

        // Report Info
        $sheet->setCellValue('A1', 'LAPORAN PAJAK (DIGUNGGUNG) - ' . $p_name);
        $sheet->setCellValue('A2', 'PERIODE: ' . ($tgl_awal ? "$tgl_awal s/d $tgl_akhir" : date('F Y', strtotime($bulanini . '-01'))));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true);

        // Header Table (include Metode Bayar)
        $sheet->setCellValue('A4', 'NO');
        $sheet->setCellValue('B4', 'TANGGAL');
        $sheet->setCellValue('C4', 'NOMOR SPK');
        $sheet->setCellValue('D4', 'KONSUMEN');
        $sheet->setCellValue('E4', 'METODE BAYAR');
        $sheet->setCellValue('F4', 'DPP (DASAR PAJAK)');
        $sheet->setCellValue('G4', 'PPN (11%)');
        $sheet->setCellValue('H4', 'TOTAL (GROSS)');

        $sheet->getStyle('A4:G4')->applyFromArray($headerStyle);

        // Fill Data
        $row = 5;
        $no = 1;
        $total_dpp = 0;
        $total_ppn = 0;
        $total_gross = 0;

        foreach ($data as $d) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($d['tgl_bayar'])));
            $sheet->setCellValueExplicit('C' . $row, $d['nospk'], PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $row, $d['konsumen_nama'] ?: 'UMUM');
            $sheet->setCellValue('E' . $row, $d['metode']);
            $sheet->setCellValue('F' . $row, $d['dpp']);
            $sheet->setCellValue('G' . $row, $d['ppn']);
            $sheet->setCellValue('H' . $row, $d['total_bayar']);

            // Format numbers
            $sheet->getStyle('F' . $row . ':H' . $row)->getNumberFormat()->setFormatCode('#,##0');

            $total_dpp += $d['dpp'];
            $total_ppn += $d['ppn'];
            $total_gross += $d['total_bayar'];
            $row++;
        }

        // Summary Row
        $sheet->setCellValue('D' . $row, 'TOTAL');
        $sheet->setCellValue('F' . $row, $total_dpp);
        $sheet->setCellValue('G' . $row, $total_ppn);
        $sheet->setCellValue('H' . $row, $total_gross);

        $sheet->getStyle('A' . $row . ':H' . $row)->getFont()->setBold(true);
        $sheet->getStyle('F' . $row . ':H' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('A' . $row . ':H' . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F1F5F9');

        // Auto size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create XLS writer (Excel 97-2003) for maximum compatibility
        // Clean output buffer to ensure clean binary
        if (ob_get_length())
            ob_clean();

        // Output Excel
        $filename = "Audit_Pajak_" . str_replace('-', '', $bulanini) . ".xls";

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    function laporan_keuangan()
    {
        $userid = $this->session->userdata('id');
        $perusahaan_id = $this->session->userdata('perusahaan_id');
        $bulanini = date('Y-m');
        $selected_date = $this->input->get('date', true) ?: date('Y-m-d');

        // Load model pembayaran
        $this->load->model('M_pembayaran');

        // Ambil semua cabang perusahaan
        $all_perusahaan = $this->db->get('tbl_perusahaan')->result();

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Laporan Keuangan',
            'selected_date' => $selected_date,
            'bulanini' => $bulanini,
            'rekening' => $this->db->get('tbl_rekening')->result(),
            'ewallet' => $this->db->get('tbl_ewallet')->result(),
            'all_perusahaan' => $all_perusahaan,
            'current_perusahaan_id' => $perusahaan_id,
        );

        $this->load_admin_view('admin/laporan/laporan_keuangan', $data);
    }

    // API untuk mengambil data laporan berdasarkan tanggal
    public function get_laporan_by_date()
    {
        try {
            $date = $this->input->post('date', true);
            $perusahaan_id = $this->input->post('perusahaan_id', true) ?: $this->session->userdata('perusahaan_id');
            log_message('debug', 'get_laporan_by_date called with date: ' . $date . ' and perusahaan_id: ' . $perusahaan_id);

            if (!$date) {
                $response = array('success' => false, 'message' => 'Tanggal tidak boleh kosong');
            } else {
                // Load model pembayaran
                $this->load->model('M_pembayaran');

                // Ambil data pembayaran untuk tanggal tertentu dan perusahaan tertentu
                $pembayaran = $this->M_pembayaran->get_pembayaran_by_date_perusahaan($date, $perusahaan_id);
                log_message('debug', 'Pembayaran data count: ' . count($pembayaran));

                // Hitung total
                $total_pemasukan = 0;
                $total_pengeluaran = 0;
                $metode_pembayaran = array();

                if ($pembayaran) {
                    foreach ($pembayaran as $p) {
                        $total_pemasukan += (float) $p->grand_total;

                        // Hitung metode pembayaran
                        $metode = $p->metode ?: 'Tidak Diketahui';
                        if (!isset($metode_pembayaran[$metode])) {
                            $metode_pembayaran[$metode] = 0;
                        }
                        $metode_pembayaran[$metode] += (float) $p->grand_total;
                    }
                }

                // Ambil data pengeluaran dari tbl_pengeluaran_harian
                $pengeluaran = array();
                try {
                    // Query pengeluaran dari tbl_pengeluaran
                    $this->db->select('ph.*, kp.kategori_pengeluaran');
                    $this->db->from('tbl_pengeluaran ph');
                    $this->db->join('tbl_kategori_pengeluaran kp', 'ph.kategori_pengeluaran_id = kp.id', 'left');
                    $this->db->where('ph.pengeluaran_tgl', $date);
                    $this->db->where('ph.perusahaan_id', $perusahaan_id);
                    $this->db->order_by('ph.pengeluaran_tgl', 'ASC');

                    $pengeluaran = $this->db->get()->result();

                    foreach ($pengeluaran as $p) {
                        $total_pengeluaran += (float) $p->pengeluaran_jumlah;
                    }

                    log_message('debug', 'Pengeluaran data count: ' . count($pengeluaran));

                } catch (Exception $e) {
                    // Jika terjadi error, set pengeluaran kosong
                    log_message('error', 'Error fetching pengeluaran: ' . $e->getMessage());
                    $pengeluaran = array();
                    $total_pengeluaran = 0;
                }

                $laba_bersih = $total_pemasukan - $total_pengeluaran;

                $response = array(
                    'success' => true,
                    'data' => array(
                        'date' => $date,
                        'perusahaan_id' => $perusahaan_id,
                        'total_pemasukan' => $total_pemasukan,
                        'total_pengeluaran' => $total_pengeluaran,
                        'laba_bersih' => $laba_bersih,
                        'metode_pembayaran' => $metode_pembayaran,
                        'detail_pembayaran' => $pembayaran ?: array(),
                        'detail_pengeluaran' => $pengeluaran
                    )
                );
            }

            header('Content-Type: application/json');
            echo json_encode($response);

        } catch (Exception $e) {
            // Log error
            log_message('error', 'Error in get_laporan_by_date: ' . $e->getMessage());
            log_message('error', 'Error trace: ' . $e->getTraceAsString());

            header('Content-Type: application/json');
            echo json_encode(array(
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ));
        }
    }

    // API untuk mengambil data daily totals berdasarkan bulan
    public function get_daily_totals_by_month()
    {
        try {
            $year_month = $this->input->post('year_month', true);
            $perusahaan_id = $this->input->post('perusahaan_id', true) ?: $this->session->userdata('perusahaan_id');
            log_message('debug', 'get_daily_totals_by_month called with year_month: ' . $year_month . ' and perusahaan_id: ' . $perusahaan_id);

            if (!$year_month) {
                $response = array('success' => false, 'message' => 'Bulan tidak boleh kosong');
            } else {
                // Load model pembayaran
                $this->load->model('M_pembayaran');

                // Ambil data daily totals berdasarkan perusahaan
                $daily_totals = $this->M_pembayaran->get_daily_totals_by_month_perusahaan($year_month, $perusahaan_id);
                log_message('debug', 'Daily totals data count: ' . count($daily_totals));

                $response = array(
                    'success' => true,
                    'data' => $daily_totals ?: array()
                );
            }

            header('Content-Type: application/json');
            echo json_encode($response);

        } catch (Exception $e) {
            // Log error
            log_message('error', 'Error in get_daily_totals_by_month: ' . $e->getMessage());
            log_message('error', 'Error trace: ' . $e->getTraceAsString());

            header('Content-Type: application/json');
            echo json_encode(array(
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ));
        }
    }

    // API untuk mengambil laporan keuangan semua cabang
    public function get_laporan_semua_cabang()
    {
        try {
            $date = $this->input->post('date', true) ?: date('Y-m-d');
            log_message('debug', 'get_laporan_semua_cabang called with date: ' . $date);

            // Load model pembayaran
            $this->load->model('M_pembayaran');

            // Ambil semua perusahaan
            $all_perusahaan = $this->db->get('tbl_perusahaan')->result();
            $laporan_cabang = array();

            foreach ($all_perusahaan as $perusahaan) {
                // Ambil data pembayaran untuk perusahaan ini
                $pembayaran = $this->M_pembayaran->get_pembayaran_by_date_perusahaan($date, $perusahaan->id_perusahaan);

                // Hitung total
                $total_pemasukan = 0;
                $total_pengeluaran = 0;
                $metode_pembayaran = array();

                if ($pembayaran) {
                    foreach ($pembayaran as $p) {
                        $total_pemasukan += (float) $p->grand_total;

                        // Hitung metode pembayaran
                        $metode = $p->metode ?: 'Tidak Diketahui';
                        if (!isset($metode_pembayaran[$metode])) {
                            $metode_pembayaran[$metode] = 0;
                        }
                        $metode_pembayaran[$metode] += (float) $p->grand_total;
                    }
                }

                // Ambil data pengeluaran dari tbl_pengeluaran_harian
                $pengeluaran = array();
                try {
                    $this->db->select('ph.*, kp.kategori_pengeluaran');
                    $this->db->from('tbl_pengeluaran ph');
                    $this->db->join('tbl_kategori_pengeluaran kp', 'ph.kategori_pengeluaran_id = kp.id', 'left');
                    $this->db->where('ph.pengeluaran_tgl', $date);
                    $this->db->where('ph.perusahaan_id', $perusahaan->id_perusahaan);
                    $this->db->order_by('ph.pengeluaran_tgl', 'ASC');

                    $pengeluaran = $this->db->get()->result();

                    foreach ($pengeluaran as $p) {
                        $total_pengeluaran += (float) $p->pengeluaran_jumlah;
                    }
                } catch (Exception $e) {
                    log_message('error', 'Error fetching pengeluaran for semua cabang: ' . $e->getMessage());
                    $pengeluaran = array();
                    $total_pengeluaran = 0;
                }

                $laba_bersih = $total_pemasukan - $total_pengeluaran;

                $laporan_cabang[] = array(
                    'perusahaan_id' => $perusahaan->id_perusahaan,
                    'nama_perusahaan' => $perusahaan->nama_perusahaan,
                    'total_pemasukan' => $total_pemasukan,
                    'total_pengeluaran' => $total_pengeluaran,
                    'laba_bersih' => $laba_bersih,
                    'metode_pembayaran' => $metode_pembayaran,
                    'jumlah_transaksi' => count($pembayaran),
                    'jumlah_pengeluaran' => count($pengeluaran)
                );
            }

            $response = array(
                'success' => true,
                'data' => $laporan_cabang
            );

            header('Content-Type: application/json');
            echo json_encode($response);

        } catch (Exception $e) {
            log_message('error', 'Error in get_laporan_semua_cabang: ' . $e->getMessage());

            header('Content-Type: application/json');
            echo json_encode(array(
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ));
        }
    }

    // Export laporan keuangan ke Excel
    public function export_laporan_keuangan()
    {
        $date = $this->input->get('date', true);
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: $this->session->userdata('perusahaan_id');

        if (!$date) {
            show_error('Tanggal tidak boleh kosong');
            return;
        }

        // Load model pembayaran
        $this->load->model('M_pembayaran');

        // Ambil data pembayaran untuk tanggal tertentu dan perusahaan tertentu
        $pembayaran = $this->M_pembayaran->get_pembayaran_by_date_perusahaan($date, $perusahaan_id);

        // Ambil data pengeluaran dari tbl_pengeluaran_harian
        $pengeluaran = array();
        try {
            $this->db->select('ph.*, kp.kategori_pengeluaran');
            $this->db->from('tbl_pengeluaran ph');
            $this->db->join('tbl_kategori_pengeluaran kp', 'ph.kategori_pengeluaran_id = kp.id', 'left');
            $this->db->where('ph.pengeluaran_tgl', $date);
            $this->db->where('ph.perusahaan_id', $perusahaan_id);
            $this->db->order_by('ph.pengeluaran_tgl', 'ASC');

            $pengeluaran = $this->db->get()->result();
        } catch (Exception $e) {
            log_message('error', 'Error fetching pengeluaran for export: ' . $e->getMessage());
            $pengeluaran = array();
        }

        // Hitung total
        $total_pemasukan = 0;
        $total_pengeluaran = 0;

        foreach ($pembayaran as $p) {
            $total_pemasukan += $p->grand_total;
        }

        foreach ($pengeluaran as $p) {
            $total_pengeluaran += $p->pengeluaran_jumlah;
        }

        $laba_bersih = $total_pemasukan - $total_pengeluaran;

        // Load library Excel
        $this->load->library('excel');
        $objPHPExcel = new Excel();

        // Set properties
        $objPHPExcel->getProperties()->setTitle("Laporan Keuangan " . date('d/m/Y', strtotime($date)));
        $objPHPExcel->getProperties()->setSubject("Laporan Keuangan");
        $objPHPExcel->getProperties()->setDescription("Laporan Keuangan tanggal " . date('d/m/Y', strtotime($date)));

        // Create worksheet
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setTitle('Laporan Keuangan');

        // Header
        $sheet->setCellValue('A1', 'LAPORAN KEUANGAN');
        $sheet->setCellValue('A2', 'Tanggal: ' . date('d/m/Y', strtotime($date)));
        $sheet->setCellValue('A3', '');

        // Summary
        $sheet->setCellValue('A4', 'RINGKASAN');
        $sheet->setCellValue('A5', 'Total Pemasukan:');
        $sheet->setCellValue('B5', 'Rp ' . number_format($total_pemasukan, 0, ',', '.'));
        $sheet->setCellValue('A6', 'Total Pengeluaran:');
        $sheet->setCellValue('B6', 'Rp ' . number_format($total_pengeluaran, 0, ',', '.'));
        $sheet->setCellValue('A7', 'Laba Bersih:');
        $sheet->setCellValue('B7', 'Rp ' . number_format($laba_bersih, 0, ',', '.'));
        $sheet->setCellValue('A8', '');

        // Pemasukan header
        $sheet->setCellValue('A9', 'DETAIL PEMASUKAN');
        $sheet->setCellValue('A10', 'No SPK');
        $sheet->setCellValue('B10', 'Konsumen');
        $sheet->setCellValue('C10', 'Metode Pembayaran');
        $sheet->setCellValue('D10', 'Total');

        // Pemasukan data
        $row = 11;
        foreach ($pembayaran as $p) {
            $sheet->setCellValue('A' . $row, $p->nospk);
            $sheet->setCellValue('B' . $row, $p->konsumen_nama);
            $sheet->setCellValue('C' . $row, $p->metode);
            $sheet->setCellValue('D' . $row, 'Rp ' . number_format($p->grand_total, 0, ',', '.'));
            $row++;
        }

        $sheet->setCellValue('A' . $row, '');
        $row++;

        // Pengeluaran header
        $sheet->setCellValue('A' . $row, 'DETAIL PENGELUARAN');
        $row++;
        $sheet->setCellValue('A' . $row, 'Keterangan');
        $sheet->setCellValue('B' . $row, 'Jumlah');

        // Pengeluaran data
        $row++;
        foreach ($pengeluaran as $p) {
            $keterangan = $p->kategori_pengeluaran ?: 'Umum';
            if ($p->pengeluaran_detail) {
                $keterangan .= ' - ' . $p->pengeluaran_detail;
            }
            if ($p->pengeluaran_penerima) {
                $keterangan .= ' (Penerima: ' . $p->pengeluaran_penerima . ')';
            }
            $sheet->setCellValue('A' . $row, $keterangan);
            $sheet->setCellValue('B' . $row, 'Rp ' . number_format($p->pengeluaran_jumlah, 0, ',', '.'));
            $row++;
        }

        // Auto size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Style header
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A9')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A10:D10')->getFont()->setBold(true);
        $sheet->getStyle('A10:D10')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');

        // Output Excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="laporan_keuangan_' . date('Y-m-d', strtotime($date)) . '.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    // Print laporan keuangan harian
    public function print_laporan_harian()
    {
        $date = $this->input->get('date', true) ?: date('Y-m-d');
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: $this->session->userdata('perusahaan_id');

        if (!$date) {
            show_error('Tanggal tidak boleh kosong');
            return;
        }

        // Get IDs for consolidated or single branch
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        // 1. Get Income Breakdown from tbl_pembayaran_detail (Transaction Payments)
        $pembayaran_detail = $this->db->query("
            SELECT pd.metode, pd.jumlah, r.nama_bank, r.no_rekening, r.atas_nama
            FROM tbl_pembayaran_detail pd
            JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
            LEFT JOIN tbl_rekening r ON pd.bank_id = r.id
            WHERE p.tgl_bayar = '$date' AND p.perusahaan_id IN ($ids_sql)
        ")->result();

        // 1b. Get Income from tbl_piutang_pembayaran (Debt Payments)
        $piutang_pembayaran = $this->db->query("
            SELECT pp.metode_bayar as metode, pp.jumlah_bayar as jumlah, r.nama_bank, r.no_rekening, r.atas_nama
            FROM tbl_piutang_pembayaran pp
            JOIN tbl_piutang p ON pp.piutang_id = p.piutang_id
            LEFT JOIN tbl_rekening r ON pp.rekening_id = r.id
            WHERE DATE(pp.tanggal_bayar) = '$date' AND p.perusahaan_id IN ($ids_sql)
        ")->result();

        $metode_pembayaran = [
            'Saldo' => 0,
            'Cash' => 0,
            'Transfer' => 0,
            'EDC' => 0,
            'QRIS' => 0,
            'OVO' => 0
        ];
        $transfer_details = [];
        $total_pemasukan = 0;

        // Combine both sources
        $all_payments = array_merge($pembayaran_detail, $piutang_pembayaran);

        foreach ($all_payments as $pd) {
            $total_pemasukan += (float) $pd->jumlah;
            $metode = strtolower($pd->metode);

            if (strpos($metode, 'tunai') !== false || strpos($metode, 'cash') !== false) {
                $metode_pembayaran['Cash'] += (float) $pd->jumlah;
            } elseif (strpos($metode, 'saldo') !== false) {
                $metode_pembayaran['Saldo'] += (float) $pd->jumlah;
            } elseif (strpos($metode, 'transfer') !== false) {
                $metode_pembayaran['Transfer'] += (float) $pd->jumlah;
                $acc_name = $pd->nama_bank ?: ($pd->no_rekening ?: 'Transfer');
                $acc_no = $pd->no_rekening ?: '';
                $key = $acc_name . ($acc_no ? " : " . $acc_no : "");

                if (!isset($transfer_details[$key]))
                    $transfer_details[$key] = 0;
                $transfer_details[$key] += (float) $pd->jumlah;
            } elseif (strpos($metode, 'debit') !== false || strpos($metode, 'edc') !== false) {
                $metode_pembayaran['EDC'] += (float) $pd->jumlah;
            } elseif (strpos($metode, 'ewallet') !== false || strpos($metode, 'ovo') !== false) {
                $metode_pembayaran['OVO'] += (float) $pd->jumlah;
            } elseif (strpos($metode, 'qris') !== false) {
                $metode_pembayaran['QRIS'] += (float) $pd->jumlah;
            } else {
                // Fallback to Cash for any unspecified cash-like payments
                $metode_pembayaran['Cash'] += (float) $pd->jumlah;
            }
        }

        // 2. Get Expenses from tbl_pengeluaran
        $pengeluaran = $this->db->query("
            SELECT p.*, kp.kategori_pengeluaran 
            FROM tbl_pengeluaran p
            LEFT JOIN tbl_kategori_pengeluaran kp ON p.kategori_pengeluaran_id = kp.id
            WHERE p.pengeluaran_tgl = '$date' AND p.perusahaan_id IN ($ids_sql)
            ORDER BY p.id ASC
        ")->result();

        $total_pengeluaran = 0;
        foreach ($pengeluaran as $p) {
            $total_pengeluaran += (float) $p->pengeluaran_jumlah;
        }

        // 3. Get TOTAL Piutang (Outstanding Amount)
        $total_piutang = $this->db->query("
            SELECT SUM(total_tagihan - total_bayar) as sisa 
            FROM tbl_pembayaran 
            WHERE status_lunas = '0' AND perusahaan_id IN ($ids_sql)
        ")->row()->sisa ?? 0;

        // 4. Perusahaan Data
        $perusahaan_data = $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $perusahaan_id))->row();

        $data = array(
            'date' => $date,
            'formatted_date' => date('d F Y', strtotime($date)),
            'total_pemasukan' => $total_pemasukan,
            'metode_pembayaran' => $metode_pembayaran,
            'transfer_details' => $transfer_details,
            'total_transfer' => $metode_pembayaran['Transfer'],
            'total_piutang' => $total_piutang,
            'pengeluaran' => $pengeluaran,
            'total_pengeluaran' => $total_pengeluaran,
            'grand_total' => $total_pemasukan,
            'perusahaan' => $perusahaan_data
        );

        // Load view print
        $this->load->view('admin/laporan/print_laporan_harian', $data);
    }

    /**
     * Dashboard Keuangan Terpadu - Menampilkan analisis komprehensif keuangan harian dan bulanan
     */
    public function dashboard_terpadu()
    {
        $controller = $this->uri->segment(1);
        $userid = $this->session->userdata('id');
        // Get perusahaan_id from GET or default to session
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: $this->session->userdata('perusahaan_id');

        // Get date range
        $tanggal_mulai = $this->input->get('mulai', true) ?: date('Y-m-01');
        $tanggal_selesai = $this->input->get('selesai', true) ?: date('Y-m-t');
        $hariini = $this->input->get('tanggal', true) ?: date('Y-m-d');

        // Get company config
        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $perusahaan_id))->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', array('divisi_id' => $this->session->userdata('level')))->row_array(),
        );

        $user_level = $this->session->userdata('level');
        $is_admin = ($user_level == 1);
        $is_owner = ($user_level == 17);

        $data = array(
            'controller' => $controller,
            'function' => 'Dashboard Terpadu',
            'title' => 'Dashboard  Terpadu',
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            'hariini' => $hariini,

            // Company info
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'logo' => $conf['perusahaan']['logo'],
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            // 'menu' diambil otomatis dari MY_Controller
            'submenu' => $this->M_master->getmenu($controller)->result(),

            // Branch selection info
            'is_admin_owner' => ($is_admin || $is_owner),
            'all_perusahaan' => ($is_admin || $is_owner) ? $this->db->get_where('tbl_perusahaan', ['status' => '1'])->result() : $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->result(),
            'current_perusahaan_id' => $perusahaan_id,

            // Financial data
            'financial_summary' => $this->get_comprehensive_financial_summary($tanggal_mulai, $tanggal_selesai, $hariini, $perusahaan_id),
            'payment_methods' => $this->get_payment_methods_analysis($tanggal_mulai, $tanggal_selesai, $hariini, $perusahaan_id),
            'trend_analysis' => $this->get_trend_analysis($tanggal_mulai, $tanggal_selesai, $perusahaan_id),
            'top_performers' => $this->get_top_performers($tanggal_mulai, $tanggal_selesai, $perusahaan_id),
            'cash_flow' => $this->get_cash_flow_analysis($tanggal_mulai, $tanggal_selesai, $perusahaan_id),
            'branch_comparison' => ($is_admin || $is_owner) ? $this->get_branch_comparison_data($tanggal_mulai, $tanggal_selesai) : null,
        );

        $this->load_admin_view('admin/laporan/keuangan/dashboard', $data);
    }

    /**
     * Get comprehensive financial summary
     */
    private function get_comprehensive_financial_summary($tanggal_mulai, $tanggal_selesai, $hariini, $perusahaan_id)
    {
        $ids = $this->_get_ids($perusahaan_id);

        // Get current period data
        $current_period = $this->db->select('SUM(total_tagihan) as total')
            ->from('tbl_pembayaran')
            ->where('tgl_bayar >=', $tanggal_mulai)
            ->where('tgl_bayar <=', $tanggal_selesai)
            ->where_in('perusahaan_id', $ids)
            ->get()
            ->row_array();

        $current_expenses = $this->M_laporan->pengeluaran_bulanan($perusahaan_id, date('Y-m', strtotime($tanggal_mulai)))->row_array();

        // Get previous period
        $prev_start = date('Y-m-d', strtotime($tanggal_mulai . ' -1 month'));
        $prev_end = date('Y-m-d', strtotime($tanggal_selesai . ' -1 month'));
        $prev_period = $this->db->select('SUM(total_tagihan) as total')
            ->from('tbl_pembayaran')
            ->where('tgl_bayar >=', $prev_start)
            ->where('tgl_bayar <=', $prev_end)
            ->where_in('perusahaan_id', $ids)
            ->get()
            ->row_array();

        $prev_expenses = $this->M_laporan->pengeluaran_bulanan($perusahaan_id, date('Y-m', strtotime($prev_start)))->row_array();

        // Growths
        $pendapatan_growth = ($prev_period['total'] ?? 0) > 0 ?
            ((($current_period['total'] ?? 0) - ($prev_period['total'] ?? 0)) / ($prev_period['total'] ?? 1)) * 100 : 0;

        $pengeluaran_growth = ($prev_expenses['pengeluaran_bulanan'] ?? 0) > 0 ?
            ((($current_expenses['pengeluaran_bulanan'] ?? 0) - ($prev_expenses['pengeluaran_bulanan'] ?? 0)) / ($prev_expenses['pengeluaran_bulanan'] ?? 1)) * 100 : 0;

        // Transaction count
        $transaction_count = $this->db->where('tgl_bayar >=', $tanggal_mulai)
            ->where('tgl_bayar <=', $tanggal_selesai)
            ->where_in('perusahaan_id', $ids)
            ->count_all_results('tbl_pembayaran');

        $days_diff = (strtotime($tanggal_selesai) - strtotime($tanggal_mulai)) / (60 * 60 * 24) + 1;
        $avg_transaction = $days_diff > 0 ? $transaction_count / $days_diff : 0;

        return [
            'total_pendapatan' => $current_period['total'] ?? 0,
            'total_pengeluaran' => $current_expenses['pengeluaran_bulanan'] ?? 0,
            'pendapatan_growth' => number_format($pendapatan_growth, 1),
            'pengeluaran_growth' => number_format($pengeluaran_growth, 1),
            'profit_margin' => ($current_period['total'] ?? 0) > 0 ? ((($current_period['total'] ?? 0) - ($current_expenses['pengeluaran_bulanan'] ?? 0)) / $current_period['total']) * 100 : 0,
            'jumlah_transaksi' => $transaction_count,
            'avg_transaction' => number_format($avg_transaction, 1)
        ];
    }

    private function get_branch_comparison_data($tanggal_mulai, $tanggal_selesai)
    {
        $branches = $this->db->get_where('tbl_perusahaan', ['status' => '1'])->result();
        $labels = [];
        $pendapatan = [];
        $pengeluaran = [];
        $laba = [];

        foreach ($branches as $b) {
            $labels[] = $b->nama_perusahaan;

            // Income
            $income = $this->db->select_sum('total_tagihan')
                ->where('perusahaan_id', $b->id_perusahaan)
                ->where('tgl_bayar >=', $tanggal_mulai)
                ->where('tgl_bayar <=', $tanggal_selesai)
                ->get('tbl_pembayaran')->row()->total_tagihan ?? 0;

            // Expense
            $expense = $this->db->select_sum('pengeluaran_jumlah')
                ->where('perusahaan_id', $b->id_perusahaan)
                ->where('pengeluaran_tgl >=', $tanggal_mulai)
                ->where('pengeluaran_tgl <=', $tanggal_selesai)
                ->get('tbl_pengeluaran')->row()->pengeluaran_jumlah ?? 0;

            $pendapatan[] = (float) $income;
            $pengeluaran[] = (float) $expense;
            $laba[] = (float) ($income - $expense);
        }

        return [
            'labels' => $labels,
            'pendapatan' => $pendapatan,
            'pengeluaran' => $pengeluaran,
            'laba' => $laba
        ];
    }

    private function get_trend_analysis($tanggal_mulai, $tanggal_selesai, $perusahaan_id)
    {
        $ids = $this->_get_ids($perusahaan_id);

        $query = $this->db->select('DATE(tgl_bayar) as tanggal, SUM(total_tagihan) as pendapatan')
            ->from('tbl_pembayaran')
            ->where('tgl_bayar >=', $tanggal_mulai)
            ->where('tgl_bayar <=', $tanggal_selesai)
            ->where_in('perusahaan_id', $ids)
            ->group_by('DATE(tgl_bayar)')
            ->order_by('tanggal')
            ->get();

        $labels = [];
        $pendapatan = [];
        $pengeluaran = [];

        foreach ($query->result() as $row) {
            $labels[] = date('d/m', strtotime($row->tanggal));
            $pendapatan[] = (float) $row->pendapatan;

            // Get expenses for this date
            $expense = $this->db->select('SUM(pengeluaran_jumlah) as total')
                ->from('tbl_pengeluaran')
                ->where('pengeluaran_tgl', $row->tanggal)
                ->where_in('perusahaan_id', $ids)
                ->get()
                ->row_array();
            $pengeluaran[] = (float) ($expense['total'] ?? 0);
        }

        return ['labels' => $labels, 'pendapatan' => $pendapatan, 'pengeluaran' => $pengeluaran];
    }

    private function get_top_performers($tanggal_mulai, $tanggal_selesai, $perusahaan_id)
    {
        $ids = $this->_get_ids($perusahaan_id);

        // Get payment methods performance from tbl_pembayaran_detail
        $this->db->select('pd.metode, SUM(pd.jumlah) as total');
        $this->db->from('tbl_pembayaran_detail pd');
        $this->db->join('tbl_pembayaran p', 'pd.pembayaran_id = p.pembayaran_id');
        $this->db->where('p.tgl_bayar >=', $tanggal_mulai);
        $this->db->where('p.tgl_bayar <=', $tanggal_selesai);
        $this->db->where_in('p.perusahaan_id', $ids);
        $this->db->group_by('pd.metode');
        $methods = $this->db->get()->result();

        $performers = [];
        $total_val = 0;
        foreach ($methods as $m) {
            $nama = ucfirst($m->metode);
            if ($m->metode == 'tunai')
                $nama = 'Cash';
            if ($m->metode == 'debit')
                $nama = 'EDC';

            $performers[] = [
                'kategori' => $nama,
                'nilai' => (float) $m->total
            ];
            $total_val += (float) $m->total;
        }

        foreach ($performers as &$p) {
            $p['persentase'] = $total_val > 0 ? ($p['nilai'] / $total_val) * 100 : 0;
        }

        usort($performers, function ($a, $b) {
            return $b['nilai'] <=> $a['nilai'];
        });

        return $performers;
    }

    private function get_cash_flow_analysis($tanggal_mulai, $tanggal_selesai, $perusahaan_id)
    {
        $ids = $this->_get_ids($perusahaan_id);
        $hariini = date('Y-m-d');

        // Pendapatan
        $pendapatan_hari = $this->db->select('SUM(total_tagihan) as total')->from('tbl_pembayaran')->where('tgl_bayar', $hariini)->where_in('perusahaan_id', $ids)->get()->row_array();
        $pendapatan_bulan = $this->db->select('SUM(total_tagihan) as total')->from('tbl_pembayaran')->where('tgl_bayar >=', $tanggal_mulai)->where('tgl_bayar <=', $tanggal_selesai)->where_in('perusahaan_id', $ids)->get()->row_array();

        // Pengeluaran
        $pengeluaran_hari = $this->db->select('SUM(pengeluaran_jumlah) as total')->from('tbl_pengeluaran')->where('pengeluaran_tgl', $hariini)->where_in('perusahaan_id', $ids)->get()->row_array();
        $pengeluaran_bulan = $this->M_laporan->pengeluaran_bulanan($perusahaan_id, date('Y-m', strtotime($tanggal_mulai)))->row_array();

        // Piutang
        $piutang_hari = $this->db->select('SUM(total_tagihan - total_bayar) as total')->from('tbl_pembayaran')->where('tgl_bayar', $hariini)->where_in('perusahaan_id', $ids)->where('status_lunas', '0')->get()->row_array();
        $piutang_bulan = $this->db->select('SUM(total_tagihan - total_bayar) as total')->from('tbl_pembayaran')->where('tgl_bayar >=', $tanggal_mulai)->where('tgl_bayar <=', $tanggal_selesai)->where_in('perusahaan_id', $ids)->where('status_lunas', '0')->get()->row_array();

        return [
            ['kategori' => 'Pendapatan', 'harian' => (float) ($pendapatan_hari['total'] ?? 0), 'bulanan' => (float) ($pendapatan_bulan['total'] ?? 0), 'trend' => 0],
            ['kategori' => 'Pengeluaran', 'harian' => (float) ($pengeluaran_hari['total'] ?? 0), 'bulanan' => (float) ($pengeluaran_bulan['pengeluaran_bulanan'] ?? 0), 'trend' => 0],
            ['kategori' => 'Piutang', 'harian' => (float) ($piutang_hari['total'] ?? 0), 'bulanan' => (float) ($piutang_bulan['total'] ?? 0), 'trend' => 0]
        ];
    }

    /**
     * Get payment methods analysis
     */
    private function get_payment_methods_analysis($tanggal_mulai, $tanggal_selesai, $hariini, $perusahaan_id)
    {
        $ids = $this->_get_ids($perusahaan_id);

        // Get master data
        $rekening = $this->db->get('tbl_rekening')->result();
        $edc = $this->db->get('tbl_mesin_edc')->result();
        $ewallet = $this->db->get('tbl_ewallet')->result();

        // Get totals by method from tbl_pembayaran_detail
        $this->db->select('pd.metode, SUM(pd.jumlah) as total');
        $this->db->from('tbl_pembayaran_detail pd');
        $this->db->join('tbl_pembayaran p', 'pd.pembayaran_id = p.pembayaran_id');
        $this->db->where('p.tgl_bayar', $hariini);
        $this->db->where_in('p.perusahaan_id', $ids);
        $this->db->group_by('pd.metode');
        $totals_by_metode = $this->db->get()->result();

        $sums = [
            'tunai' => 0,
            'transfer' => 0,
            'debit' => 0,
            'kartu_kredit' => 0,
            'ewallet' => 0,
            'lainnya' => 0
        ];

        foreach ($totals_by_metode as $row) {
            $sums[$row->metode] = (float) $row->total;
        }

        // Calculate transfer details
        $this->db->select('pd.bank_id, SUM(pd.jumlah) as total');
        $this->db->from('tbl_pembayaran_detail pd');
        $this->db->join('tbl_pembayaran p', 'pd.pembayaran_id = p.pembayaran_id');
        $this->db->where('p.tgl_bayar', $hariini);
        $this->db->where_in('p.perusahaan_id', $ids);
        $this->db->where('pd.metode', 'transfer');
        $this->db->where('pd.bank_id IS NOT NULL');
        $this->db->group_by('pd.bank_id');
        $transfer_raw = $this->db->get()->result();

        $transfer_details = [];
        foreach ($rekening as $rek) {
            foreach ($transfer_raw as $raw) {
                if ($raw->bank_id == $rek->id) {
                    $transfer_details[] = [
                        'nama' => $rek->nama_bank . ' (' . $rek->no_rekening . ')',
                        'total' => (float) $raw->total
                    ];
                }
            }
        }

        // Calculate EDC details
        $this->db->select('pd.edc_id, SUM(pd.jumlah) as total');
        $this->db->from('tbl_pembayaran_detail pd');
        $this->db->join('tbl_pembayaran p', 'pd.pembayaran_id = p.pembayaran_id');
        $this->db->where('p.tgl_bayar', $hariini);
        $this->db->where_in('p.perusahaan_id', $ids);
        $this->db->where('pd.metode', 'debit');
        $this->db->where('pd.edc_id IS NOT NULL');
        $this->db->group_by('pd.edc_id');
        $edc_raw = $this->db->get()->result();

        $edc_details = [];
        foreach ($edc as $item) {
            foreach ($edc_raw as $raw) {
                if ($raw->edc_id == $item->edc_id) {
                    $edc_details[] = [
                        'nama' => $item->edc_nama,
                        'total' => (float) $raw->total
                    ];
                }
            }
        }

        // Calculate E-Wallet details
        $this->db->select('pd.ewallet_id, SUM(pd.jumlah) as total');
        $this->db->from('tbl_pembayaran_detail pd');
        $this->db->join('tbl_pembayaran p', 'pd.pembayaran_id = p.pembayaran_id');
        $this->db->where('p.tgl_bayar', $hariini);
        $this->db->where_in('p.perusahaan_id', $ids);
        $this->db->where('pd.metode', 'ewallet');
        $this->db->where('pd.ewallet_id IS NOT NULL');
        $this->db->group_by('pd.ewallet_id');
        $ewallet_raw = $this->db->get()->result();

        $ewallet_details = [];
        foreach ($ewallet as $item) {
            foreach ($ewallet_raw as $raw) {
                if ($raw->ewallet_id == $item->id) {
                    $ewallet_details[] = [
                        'nama' => $item->ewallet_nama,
                        'total' => (float) $raw->total
                    ];
                }
            }
        }

        $total_all = array_sum($sums);

        return array(
            'cash' => array(
                'pembayaran' => $sums['tunai'],
                'piutang' => 0,
                'total' => $sums['tunai'],
                'percentage' => $total_all > 0 ? ($sums['tunai'] / $total_all) * 100 : 0
            ),
            'transfer' => array(
                'pembayaran' => $sums['transfer'],
                'piutang' => 0,
                'total' => $sums['transfer'],
                'percentage' => $total_all > 0 ? ($sums['transfer'] / $total_all) * 100 : 0,
                'details' => $transfer_details
            ),
            'edc' => array(
                'pembayaran' => $sums['debit'],
                'piutang' => 0,
                'total' => $sums['debit'],
                'percentage' => $total_all > 0 ? ($sums['debit'] / $total_all) * 100 : 0,
                'details' => $edc_details
            ),
            'ewallet' => array(
                'pembayaran' => $sums['ewallet'],
                'piutang' => 0,
                'total' => $sums['ewallet'],
                'percentage' => $total_all > 0 ? ($sums['ewallet'] / $total_all) * 100 : 0,
                'details' => $ewallet_details
            ),
            'total_all' => $total_all
        );
    }

    /**
     * Export dashboard data to Excel
     */
    public function export_dashboard()
    {
        $tanggal_mulai = $this->input->get('mulai', true) ?: date('Y-m-01');
        $tanggal_selesai = $this->input->get('selesai', true) ?: date('Y-m-t');
        $hariini = $this->input->get('tanggal', true) ?: date('Y-m-d');
        $perusahaan_id = $this->session->userdata('perusahaan_id');

        // Get all data
        $financial_summary = $this->get_comprehensive_financial_summary($tanggal_mulai, $tanggal_selesai, $hariini, $perusahaan_id);
        $payment_methods = $this->get_payment_methods_analysis($tanggal_mulai, $tanggal_selesai, $hariini, $perusahaan_id);
        $trend_analysis = $this->get_trend_analysis($tanggal_mulai, $tanggal_selesai, $perusahaan_id);
        $top_performers = $this->get_top_performers($tanggal_mulai, $tanggal_selesai, $perusahaan_id);
        $cash_flow = $this->get_cash_flow_analysis($tanggal_mulai, $tanggal_selesai, $perusahaan_id);

        // Load PHPExcel library
        require_once APPPATH . 'third_party/PHPExcel/PHPExcel.php';

        $excel = new PHPExcel();

        // Set document properties
        $excel->getProperties()
            ->setCreator($this->session->userdata('username'))
            ->setLastModifiedBy($this->session->userdata('username'))
            ->setTitle('Dashboard Keuangan Terpadu')
            ->setSubject('Dashboard Keuangan - ' . date('d F Y', strtotime($tanggal_mulai)) . ' s/d ' . date('d F Y', strtotime($tanggal_selesai)))
            ->setDescription('Laporan dashboard keuangan terpadu');

        // Set active sheet
        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'DASHBOARD KEUANGAN TERPADU');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        // Set period
        $sheet->setCellValue('A2', 'Periode: ' . date('d F Y', strtotime($tanggal_mulai)) . ' s/d ' . date('d F Y', strtotime($tanggal_selesai)));
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        // Financial Summary
        $sheet->setCellValue('A4', 'RINGKASAN KEUANGAN');
        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(14);

        $summary_data = [
            ['Total Pendapatan', $financial_summary['total_pendapatan']],
            ['Total Pengeluaran', $financial_summary['total_pengeluaran']],
            ['Profit Margin', $financial_summary['profit_margin'] . '%'],
            ['Jumlah Transaksi', $financial_summary['jumlah_transaksi']],
            ['Rata-rata Transaksi/Hari', $financial_summary['avg_transaction']]
        ];

        $row = 5;
        foreach ($summary_data as $data) {
            $sheet->setCellValue('A' . $row, $data[0]);
            $sheet->setCellValue('B' . $row, $data[1]);
            $row++;
        }

        // Payment Methods
        $sheet->setCellValue('D4', 'METODE PEMBAYARAN');
        $sheet->getStyle('D4')->getFont()->setBold(true)->setSize(14);

        $methods_data = [
            ['Cash', $payment_methods['cash']['total']],
            ['Transfer', $payment_methods['transfer']['total']],
            ['EDC', $payment_methods['edc']['total']],
            ['E-Wallet', $payment_methods['ewallet']['total']]
        ];

        $row = 5;
        foreach ($methods_data as $data) {
            $sheet->setCellValue('D' . $row, $data[0]);
            $sheet->setCellValue('E' . $row, $data[1]);
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $row++;
        }

        // Top Performers
        $sheet->setCellValue('A12', 'TOP PERFORMERS');
        $sheet->getStyle('A12')->getFont()->setBold(true)->setSize(14);

        $sheet->setCellValue('A13', 'Rank');
        $sheet->setCellValue('B13', 'Kategori');
        $sheet->setCellValue('C13', 'Nilai');
        $sheet->setCellValue('D13', 'Persentase');
        $sheet->getStyle('A13:D13')->getFont()->setBold(true);

        $row = 14;
        foreach ($top_performers as $index => $performer) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $performer['kategori']);
            $sheet->setCellValue('C' . $row, $performer['nilai']);
            $sheet->setCellValue('D' . $row, number_format($performer['persentase'], 1) . '%');
            $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $row++;
        }

        // Cash Flow
        $sheet->setCellValue('F12', 'CASH FLOW ANALYSIS');
        $sheet->getStyle('F12')->getFont()->setBold(true)->setSize(14);

        $sheet->setCellValue('F13', 'Kategori');
        $sheet->setCellValue('G13', 'Harian');
        $sheet->setCellValue('H13', 'Bulanan');
        $sheet->getStyle('F13:H13')->getFont()->setBold(true);

        $row = 14;
        foreach ($cash_flow as $flow) {
            $sheet->setCellValue('F' . $row, $flow['kategori']);
            $sheet->setCellValue('G' . $row, $flow['harian']);
            $sheet->setCellValue('H' . $row, $flow['bulanan']);
            $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $row++;
        }

        // Auto size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set filename
        $filename = 'Dashboard_Keuangan_Terpadu_' . date('Y-m-d', strtotime($tanggal_mulai)) . '_' . date('Y-m-d', strtotime($tanggal_selesai)) . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Save file
        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $writer->save('php://output');
        exit;
    }

    /**
     * Rekap Metode Pembayaran
     */
    public function rekap_metode_pembayaran()
    {
        $userid = $this->session->userdata('id');
        $user_level = $this->session->userdata('level');
        $is_admin = ($user_level == 1);
        $is_owner = ($user_level == 17);

        // Get perusahaan_id from GET or default to session
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: $this->session->userdata('perusahaan_id');
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        $hariini = $this->input->get('tanggal', true) ?: date('Y-m-d');

        // Get company name
        $perusahaan_name = 'TGM ERP System';
        $p_info = $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $perusahaan_id))->row_array();
        if ($p_info) {
            $perusahaan_name = $p_info['nama_perusahaan'];
        }

        // Get transaction data from tbl_pembayaran and tbl_pembayaran_detail
        $this->db->select('p.pembayaran_id, p.nospk, p.konsumen_id, p.tgl_bayar, p.jam_bayar, p.total_tagihan, p.total_bayar, p.status_lunas, p.kasir_id, p.perusahaan_id,
                          k.konsumen_nama, k.konsumen_nohp, k.konsumen_alamat');
        $this->db->from('tbl_pembayaran p');
        $this->db->join('tbl_konsumen k', 'k.konsumen_id = p.konsumen_id', 'left');
        $this->db->where('p.tgl_bayar', $hariini);
        $this->db->where_in('p.perusahaan_id', $ids);
        $pemasukan = $this->db->get()->result();

        // Get payment methods data from tbl_pembayaran_detail
        $this->db->select('pd.metode, pd.jumlah, pd.referensi, pd.bank_id, pd.edc_id, pd.ewallet_id, pd.keterangan,
                          r.nama_bank, r.no_rekening, r.atas_nama,
                          e.edc_nama, e.edc_ket,
                          ew.ewallet_nama, ew.wallet_ket,
                          p.nospk, p.tgl_bayar, p.jam_bayar, p.total_tagihan, p.total_bayar, p.status_lunas');
        $this->db->from('tbl_pembayaran_detail pd');
        $this->db->join('tbl_pembayaran p', 'p.pembayaran_id = pd.pembayaran_id');
        $this->db->join('tbl_rekening r', 'r.id = pd.bank_id', 'left');
        $this->db->join('tbl_mesin_edc e', 'e.edc_id = pd.edc_id', 'left');
        $this->db->join('tbl_ewallet ew', 'ew.id = pd.ewallet_id', 'left');
        $this->db->where('p.tgl_bayar', $hariini);
        $this->db->where_in('p.perusahaan_id', $ids);
        $payment_methods_data = $this->db->get()->result();

        // Calculate payment method totals
        $payment_methods = $this->calculate_payment_methods_new($payment_methods_data);

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Rekap Metode Pembayaran',
            'hariini' => $hariini,
            'pemasukan' => $pemasukan,
            'payment_methods_data' => $payment_methods_data,
            'payment_methods' => $payment_methods,
            'perusahaan' => $perusahaan_name,
            'current_perusahaan_id' => $perusahaan_id,
            'is_admin_owner' => ($is_admin || $is_owner),
            'all_perusahaan' => ($is_admin || $is_owner) ? $this->db->get_where('tbl_perusahaan', ['status' => '1'])->result() : $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->result()
        );

        $this->load_admin_view('admin/laporan/keuangan/rekap_metode_pembayaran', $data);
    }

    /**
     * Calculate payment methods from tbl_pembayaran_detail
     */
    private function calculate_payment_methods_new($payment_methods_data)
    {
        $methods = [
            'tunai' => ['total' => 0, 'details' => []],
            'transfer' => ['total' => 0, 'details' => []],
            'debit' => ['total' => 0, 'details' => []],
            'ewallet' => ['total' => 0, 'details' => []],
            'kartu_kredit' => ['total' => 0, 'details' => []],
            'lainnya' => ['total' => 0, 'details' => []]
        ];

        $total_all = 0;

        foreach ($payment_methods_data as $data) {
            $metode = strtolower($data->metode);
            $jumlah = $data->jumlah;

            if (isset($methods[$metode])) {
                $methods[$metode]['total'] += $jumlah;
                $total_all += $jumlah;

                // Add details for specific methods
                if ($metode == 'transfer' && $data->bank_id) {
                    $bank_key = $data->nama_bank . ' - ' . $data->no_rekening;
                    if (!isset($methods[$metode]['details'][$bank_key])) {
                        $methods[$metode]['details'][$bank_key] = [
                            'nama' => $data->nama_bank . ' - ' . $data->no_rekening,
                            'total' => 0
                        ];
                    }
                    $methods[$metode]['details'][$bank_key]['total'] += $jumlah;
                } elseif ($metode == 'debit' && $data->edc_id) {
                    $edc_key = $data->edc_nama;
                    if (!isset($methods[$metode]['details'][$edc_key])) {
                        $methods[$metode]['details'][$edc_key] = [
                            'nama' => $data->edc_nama,
                            'total' => 0
                        ];
                    }
                    $methods[$metode]['details'][$edc_key]['total'] += $jumlah;
                } elseif ($metode == 'ewallet' && $data->ewallet_id) {
                    $ewallet_key = $data->ewallet_nama;
                    if (!isset($methods[$metode]['details'][$ewallet_key])) {
                        $methods[$metode]['details'][$ewallet_key] = [
                            'nama' => $data->ewallet_nama,
                            'total' => 0
                        ];
                    }
                    $methods[$metode]['details'][$ewallet_key]['total'] += $jumlah;
                } elseif ($metode == 'kartu_kredit') {
                    $cc_key = 'Kartu Kredit';
                    if (!isset($methods[$metode]['details'][$cc_key])) {
                        $methods[$metode]['details'][$cc_key] = [
                            'nama' => 'Kartu Kredit',
                            'total' => 0
                        ];
                    }
                    $methods[$metode]['details'][$cc_key]['total'] += $jumlah;
                } elseif ($metode == 'lainnya') {
                    $lainnya_key = 'Lainnya';
                    if (!isset($methods[$metode]['details'][$lainnya_key])) {
                        $methods[$metode]['details'][$lainnya_key] = [
                            'nama' => 'Lainnya',
                            'total' => 0
                        ];
                    }
                    $methods[$metode]['details'][$lainnya_key]['total'] += $jumlah;
                }
            }
        }

        // Calculate percentages
        foreach ($methods as $key => &$method) {
            $method['percentage'] = $total_all > 0 ? ($method['total'] / $total_all) * 100 : 0;
            $method['details'] = array_values($method['details']); // Convert to indexed array
        }

        return [
            'cash' => $methods['tunai'],
            'transfer' => $methods['transfer'],
            'edc' => $methods['debit'],
            'ewallet' => $methods['ewallet'],
            'kartu_kredit' => $methods['kartu_kredit'],
            'lainnya' => $methods['lainnya'],
            'total_all' => $total_all
        ];
    }

    /**
     * Export Rekap Metode Pembayaran to Excel
     */
    public function export_rekap_metode_pembayaran()
    {
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: $this->session->userdata('perusahaan_id');
        $ids = $this->_get_ids($perusahaan_id);
        $hariini = $this->input->get('tanggal', true) ?: date('Y-m-d');

        // Get payment methods data from tbl_pembayaran_detail
        $this->db->select('pd.metode, pd.jumlah, pd.referensi, pd.bank_id, pd.edc_id, pd.ewallet_id, pd.keterangan,
                          r.nama_bank, r.no_rekening, r.atas_nama,
                          e.edc_nama, e.edc_ket,
                          ew.ewallet_nama, ew.wallet_ket,
                          p.nospk, p.tgl_bayar, p.jam_bayar, p.total_tagihan, p.total_bayar, p.status_lunas');
        $this->db->from('tbl_pembayaran_detail pd');
        $this->db->join('tbl_pembayaran p', 'p.pembayaran_id = pd.pembayaran_id');
        $this->db->join('tbl_rekening r', 'r.id = pd.bank_id', 'left');
        $this->db->join('tbl_mesin_edc e', 'e.edc_id = pd.edc_id', 'left');
        $this->db->join('tbl_ewallet ew', 'ew.id = pd.ewallet_id', 'left');
        $this->db->where('p.tgl_bayar', $hariini);
        $this->db->where_in('p.perusahaan_id', $ids);
        $payment_methods_data = $this->db->get()->result();

        $payment_methods = $this->calculate_payment_methods_new($payment_methods_data);

        // Load PHPExcel
        require_once APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        // Title
        $p_info = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row();
        $sheet->setCellValue('A1', 'REKAP METODE PEMBAYARAN - ' . ($p_info ? $p_info->nama_perusahaan : 'TGM'));
        $sheet->setCellValue('A2', 'TANGGAL: ' . date('d F Y', strtotime($hariini)));
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(12);

        // Table 1: Summary By Method
        $sheet->setCellValue('A4', 'RINGKASAN PER METODE');
        $sheet->getStyle('A4')->getFont()->setBold(true);
        $headers_summary = ['NO', 'METODE PEMBAYARAN', 'TOTAL NOMINAL', 'PERSENTASE', 'KETERANGAN'];
        $col = 0;
        foreach ($headers_summary as $h) {
            $sheet->setCellValueByColumnAndRow($col, 5, $h);
            $sheet->getStyleByColumnAndRow($col, 5)->getFont()->setBold(true);
            $col++;
        }

        $rowNum = 6;
        $idx = 1;
        $list_methods = [
            ['Tunai', $payment_methods['cash']],
            ['Transfer', $payment_methods['transfer']],
            ['EDC / Debit', $payment_methods['edc']],
            ['E-Wallet', $payment_methods['ewallet']],
            ['Lainnya', $payment_methods['lainnya']]
        ];

        foreach ($list_methods as $m) {
            $sheet->setCellValue('A' . $rowNum, $idx++);
            $sheet->setCellValue('B' . $rowNum, $m[0]);
            $sheet->setCellValue('C' . $rowNum, $m[1]['total']);
            $sheet->setCellValue('D' . $rowNum, number_format($m[1]['percentage'], 1) . '%');

            $details_str = '';
            if (!empty($m[1]['details'])) {
                foreach ($m[1]['details'] as $det) {
                    $details_str .= $det['nama'] . ': ' . number_format($det['total']) . '; ';
                }
            }
            $sheet->setCellValue('E' . $rowNum, $details_str);
            $rowNum++;
        }

        // Grand Total
        $sheet->setCellValue('B' . $rowNum, 'TOTAL KESELURUHAN');
        $sheet->setCellValue('C' . $rowNum, $payment_methods['total_all']);
        $sheet->setCellValue('D' . $rowNum, '100%');
        $sheet->getStyle('A' . $rowNum . ':E' . $rowNum)->getFont()->setBold(true);
        $sheet->getStyle('C6:C' . $rowNum)->getNumberFormat()->setFormatCode('#,##0');

        // Table 2: Detailed Transactions
        $rowNum += 3;
        $sheet->setCellValue('A' . $rowNum, 'RINCIAN TRANSAKSI');
        $sheet->getStyle('A' . $rowNum)->getFont()->setBold(true);
        $rowNum++;
        $headers_detail = ['WAKTU', 'NO SPK', 'METODE', 'JUMLAH', 'REFERENSI / DETAIL'];
        $col = 0;
        foreach ($headers_detail as $h) {
            $sheet->setCellValueByColumnAndRow($col, $rowNum, $h);
            $sheet->getStyleByColumnAndRow($col, $rowNum)->getFont()->setBold(true);
            $col++;
        }
        $rowNum++;

        foreach ($payment_methods_data as $pm) {
            $sheet->setCellValue('A' . $rowNum, date('H:i', strtotime($pm->jam_bayar)));
            $sheet->setCellValue('B' . $rowNum, $pm->nospk);
            $sheet->setCellValue('C' . $rowNum, ucfirst($pm->metode));
            $sheet->setCellValue('D' . $rowNum, $pm->jumlah);

            $detail = '';
            if ($pm->nama_bank)
                $detail = $pm->nama_bank . ' (' . $pm->no_rekening . ')';
            elseif ($pm->edc_nama)
                $detail = $pm->edc_nama;
            elseif ($pm->ewallet_nama)
                $detail = $pm->ewallet_nama;

            if ($pm->referensi)
                $detail .= ' | Ref: ' . $pm->referensi;
            $sheet->setCellValue('E' . $rowNum, $detail);
            $rowNum++;
        }

        $sheet->getStyle('D' . ($rowNum - count($payment_methods_data)) . ':D' . $rowNum)->getNumberFormat()->setFormatCode('#,##0');

        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $filename = "Rekap_Metode_Pembayaran_" . str_replace('-', '', $hariini) . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function pengeluaran()
    {
        $controller = $this->uri->segment(1);
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: $this->session->userdata('perusahaan_id');
        $ids = $this->_get_ids($perusahaan_id);

        // Context: Bulan (Monthly Recap) but title says Harian (Daily breakdown in a month)
        $bulanini = $this->input->get('bulan', true) ?: date('Y-m');
        $hariini_now = date('Y-m-d');
        $userid = $this->session->userdata('id');

        // Check user level
        $user_level = $this->session->userdata('level');
        $is_admin = ($user_level == 1);
        $is_owner = ($user_level == 17);

        // Load models
        $this->load->model('KasirShift_model');

        // Get company info
        $perusahaan_name = 'TGM ERP System';
        $p_info = $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $perusahaan_id))->row_array();
        if ($p_info) {
            $perusahaan_name = $p_info['nama_perusahaan'];
        }

        $data = array(
            'controller' => $controller,
            'function' => 'pengeluaran',
            'title' => 'Rekap Pengeluaran',
            'hariini' => $bulanini, // Monthly context (YYYY-MM)
            'hariinii' => $hariini_now, // Today context (YYYY-MM-DD)
            'pengeluaran' => $this->M_laporan->getpengeluaranharian($perusahaan_id, $bulanini)->result(),
            'jml_pengeluaran' => $this->M_laporan->gettotalpengeluaran($perusahaan_id, $bulanini)->row_array(),
            'kategori' => $this->db->get('tbl_kategori_pengeluaran')->result(),
            'ewallet_list' => $this->db->get('tbl_ewallet')->result(),
            'edc_list' => $this->db->get('tbl_mesin_edc')->result(),
            'bank_list' => $this->db->get('tbl_rekening')->result(),
            'karyawan_list' => $this->db->select('karyawan_id, nama_lengkap')->where('perusahaan_id', $perusahaan_id)->order_by('nama_lengkap', 'asc')->get('tbl_karyawan')->result(),
            'current_perusahaan_id' => $perusahaan_id,
            'perusahaan' => $perusahaan_name,
            'is_admin_owner' => ($is_admin || $is_owner),
            'all_perusahaan' => ($is_admin || $is_owner) ? $this->db->get_where('tbl_perusahaan', ['status' => '1'])->result() : $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->result(),
            'user_level' => $user_level,
            'current_shift' => $this->KasirShift_model->get_current_shift($userid)
        );

        $this->load_admin_view('admin/laporan/keuangan/rekap_pengeluaran', $data);
    }


    public function add_pengeluaran()
    {
        $metode_bayar = $this->input->post('metode_bayar', true);
        $rekening_tujuan = trim($this->input->post('rekening_tujuan', true));
        $ewallet_id = $this->input->post('ewallet_id', true);
        $edc_id = $this->input->post('edc_id', true);

        // Validate metode pembayaran
        if (!$metode_bayar) {
            $this->session->set_flashdata('error', 'Metode pembayaran harus dipilih');
            redirect('laporan/pengeluaran');
            return;
        }

        // Validate specific payment method requirements
        if ($metode_bayar == 'transfer' && !$rekening_tujuan) {
            $this->session->set_flashdata('error', 'Isi rekening tujuan untuk pembayaran transfer');
            redirect('laporan/pengeluaran');
            return;
        }

        if ($metode_bayar == 'ewallet' && !$ewallet_id) {
            $this->session->set_flashdata('error', 'Pilih e-wallet untuk pembayaran e-wallet');
            redirect('laporan/pengeluaran');
            return;
        }

        if (($metode_bayar == 'debit' || $metode_bayar == 'kartu_kredit') && !$edc_id) {
            $this->session->set_flashdata('error', 'Pilih mesin EDC untuk pembayaran kartu');
            redirect('laporan/pengeluaran');
            return;
        }

        $keterangan = $this->input->post('keterangan', true);

        // Logic handled for specific payment methods
        if ($metode_bayar == 'transfer') {
            $bank_pengirim_id = $this->input->post('bank_pengirim_id', true);
            $rekening_tujuan_detail = $this->input->post('rekening_tujuan', true);

            // Fetch sender bank details to append to description
            $bank_sender = $this->db->get_where('tbl_rekening', ['id' => $bank_pengirim_id])->row();
            $sender_info = $bank_sender ? ($bank_sender->nama_bank . ' - ' . $bank_sender->no_rekening) : 'Unknown Bank';

            // Format: [Transfer: Sender -> Receiver] Detail
            $keterangan .= " (Transfer: $sender_info -> $rekening_tujuan_detail)";
        }

        $kategori_id = $this->input->post('kategori', true);
        $jumlah_angka = str_replace(',', '', $this->input->post('jumlah', true));

        // [OTOMATISASI KASBON]
        if ($kategori_id == 3) {
            $karyawan_id = $this->input->post('karyawan_id', true);
            if ($karyawan_id) {
                $karyawan = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $karyawan_id])->row();
                $karyawan_name = $karyawan ? $karyawan->nama_lengkap : 'Unknown';

                // Tambahkan nama karyawan ke keterangan untuk histori kasir
                $keterangan .= " (Otomatis Kasbon: " . $karyawan_name . ")";

                // Update / Insert ke tbl_payroll_manual bulan berjalan
                $bulan_date = date('Y-m-01');
                $existing_payroll = $this->db->get_where('tbl_payroll_manual', [
                    'karyawan_id' => $karyawan_id,
                    'bulan' => $bulan_date
                ])->row();

                if ($existing_payroll) {
                    $this->db->where('id', $existing_payroll->id);
                    $this->db->update('tbl_payroll_manual', ['kasbon' => $existing_payroll->kasbon + $jumlah_angka]);
                } else {
                    $this->db->insert('tbl_payroll_manual', [
                        'karyawan_id' => $karyawan_id,
                        'bulan' => $bulan_date,
                        'kasbon' => $jumlah_angka
                    ]);
                }
            }
        }

        $add = array(
            'pengeluaran_tgl' => date('Y-m-d'),
            'kategori_pengeluaran_id' => $kategori_id,
            'pengeluaran_jumlah' => $jumlah_angka,
            'pengeluaran_detail' => $keterangan,
            'pengeluaran_penerima' => $this->input->post('penerima', true),
            'metode_bayar' => $metode_bayar,
            'rekening_tujuan' => $rekening_tujuan ?: null,
            'rekening_id' => ($metode_bayar == 'transfer') ? $bank_pengirim_id : null,
            'ewallet_id' => $ewallet_id ?: null,
            'edc_id' => $edc_id ?: null,
            'user_id' => $this->session->userdata('id'),
            'perusahaan_id' => $this->input->post('perusahaan_id', true) ?: $this->session->userdata('perusahaan_id'),
        );

        $success = $this->db->insert('tbl_pengeluaran', $add);
        if ($success) {
            // [AUDIT LOG]
            $this->_log_manual("Input Pengeluaran Baru", json_encode([
                'penerima' => $add['pengeluaran_penerima'],
                'jumlah' => $add['pengeluaran_jumlah'],
                'detail' => $add['pengeluaran_detail']
            ]), 'WRITE');

            $this->session->set_flashdata('success', 'Data berhasil disimpan');
            redirect('laporan/pengeluaran');
        }
    }

    public function hapus_pengeluaran($id)
    {
        if (!$id) {
            $this->session->set_flashdata('error', 'ID tidak valid');
            redirect('laporan/pengeluaran');
            return;
        }

        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_pengeluaran');

        if ($success) {
            // [AUDIT LOG]
            $this->_log_manual("Hapus Pengeluaran ID: $id", "User: " . $this->session->userdata('username'), 'DELETE');

            $this->session->set_flashdata('success', 'Data pengeluaran berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data');
        }

        redirect('laporan/pengeluaran');
    }

    /**
     * Add Monthly Expense
     */

    public function laporan_piutang()
    {
        $userid = $this->session->userdata('id');
        // Get perusahaan_id from GET or default to session
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: $this->session->userdata('perusahaan_id');

        // Check user level
        $user_level = $this->session->userdata('level');
        $is_admin = ($user_level == 1);
        $is_owner = ($user_level == 17);

        // Load model pembayaran
        $this->load->model('M_pembayaran');

        // Get filter parameters
        $status = $this->input->get('status', true);
        $limit = $this->input->get('limit', true) ?: 50;

        // Get rekening data
        $rekening_list = $this->db->where('active', '1')->get('tbl_rekening')->result();

        // Get e-wallet data
        $ewallet_list = $this->db->get('tbl_ewallet')->result();

        // Get EDC data
        $edc_list = $this->db->get('tbl_mesin_edc')->result();

        // Get company info
        $perusahaan_name = 'TGM ERP System';
        $p_info = $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $perusahaan_id))->row_array();
        if ($p_info) {
            $perusahaan_name = $p_info['nama_perusahaan'];
        }

        $ids = $this->_get_ids($perusahaan_id);

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => 'laporan_piutang',
            'title' => 'Laporan Piutang Konsumen',
            'piutang_list' => $this->M_pembayaran->getPiutangList($ids, $status, $limit),
            'piutang_summary' => $this->M_pembayaran->getPiutangSummary($ids),
            'rekening_list' => $rekening_list,
            'ewallet_list' => $ewallet_list,
            'edc_list' => $edc_list,
            'status_filter' => $status,
            'limit_filter' => $limit,
            'current_perusahaan_id' => $perusahaan_id,
            'perusahaan' => $perusahaan_name,
            'is_admin_owner' => ($is_admin || $is_owner),
            'all_perusahaan' => ($is_admin || $is_owner) ? $this->db->get_where('tbl_perusahaan', ['status' => '1'])->result() : $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->result()
        );

        $this->load_admin_view('admin/laporan/laporan_piutang', $data);
    }

    /**
     * Update pembayaran piutang
     */
    public function update_pembayaran_piutang()
    {
        $nospk = $this->input->post('nospk', true);
        $konsumen_id = $this->input->post('konsumen_id', true); // New for collective
        $additional_payment = rupiahToInt($this->input->post('additional_payment', true));
        $metode_bayar = $this->input->post('metode_bayar', true);
        $rekening_id = $this->input->post('rekening_id', true);
        $ewallet_id = $this->input->post('ewallet_id', true);
        $edc_id = $this->input->post('edc_id', true);
        $keterangan = $this->input->post('keterangan', true);

        if ((empty($nospk) && empty($konsumen_id)) || $additional_payment <= 0 || empty($metode_bayar)) {
            $this->session->set_flashdata('error', 'Data tidak valid. Pastikan semua field wajib diisi.');
            redirect('laporan/laporan_piutang');
            return;
        }

        // Validate based on method
        if ($metode_bayar == 'transfer' && !$rekening_id) {
            $this->session->set_flashdata('error', 'Pilih rekening untuk pembayaran transfer.');
            redirect('laporan/laporan_piutang');
            return;
        }
        if ($metode_bayar == 'ewallet' && !$ewallet_id) {
            $this->session->set_flashdata('error', 'Pilih e-wallet.');
            redirect('laporan/laporan_piutang');
            return;
        }
        if ($metode_bayar == 'debit' && !$edc_id) {
            $this->session->set_flashdata('error', 'Pilih mesin EDC.');
            redirect('laporan/laporan_piutang');
            return;
        }

        $this->load->model('M_pembayaran');

        if ($konsumen_id) {
            // Collective Payment Flow
            $success = $this->M_pembayaran->processCollectivePayment($konsumen_id, $additional_payment, $metode_bayar, $keterangan, $rekening_id, $ewallet_id, $edc_id);
        } else {
            // Single SPK Flow (Legacy support)
            $success = $this->M_pembayaran->updatePiutangPaymentWithMethod($nospk, $additional_payment, $metode_bayar, $keterangan, $rekening_id, $ewallet_id, $edc_id);
        }

        if ($success) {
            // [AUDIT LOG]
            $log_msg = $konsumen_id ? "Bayar Piutang Kolektif Konsumen ID: $konsumen_id" : "Bayar Piutang SPK #$nospk";
            $this->_log_manual($log_msg, "Jumlah: $additional_payment, Metode: $metode_bayar", 'WRITE');

            // --- KIRIM WA OTOMATIS (PEMBAYARAN PIUTANG) ---
            $this->load->helper('whatsapp');
            if (!$konsumen_id && $nospk) { // Single SPK Piutang
                $pemesanan = $this->db->get_where('tbl_pemesanan', ['nospk' => $nospk])->row();
                if ($pemesanan) {
                    $kons = $this->db->get_where('tbl_konsumen', ['konsumen_id' => $pemesanan->konsumen_id])->row();
                    if ($kons && !empty($kons->konsumen_nohp)) {
                        $piutang = $this->db->get_where('tbl_piutang', ['nospk' => $nospk])->row();
                        $status_lunas = ($piutang && $piutang->status_lunas == '1') ? 'LUNAS' : 'BELUM LUNAS (Piutang)';

                        $wa_msg = "*PEMBAYARAN PIUTANG DITERIMA*\n\n";
                        $wa_msg .= "Halo *" . $kons->konsumen_nama . "*,\n";
                        $wa_msg .= "Pembayaran piutang Anda telah kami terima.\n\n";
                        $wa_msg .= "No. SPK: *$nospk*\n";
                        $wa_msg .= "Diterima: *Rp " . number_format($additional_payment, 0, ',', '.') . "*\n";
                        $wa_msg .= "Status SPK: *$status_lunas*\n\n";
                        $link_cek = base_url('cekpesanan?spk=' . urlencode(encrypt_spk($nospk)));
                        $wa_msg .= "Cek status pesanan Anda di sini:\n$link_cek\n\n";
                        $wa_msg .= "Terima kasih telah mempercayakan kebutuhan cetak Anda kepada kami.\n\n";
                        $wa_msg .= "_Pesan otomatis via ERP TGM Print_";

                        send_wa_notif($kons->konsumen_nohp, $wa_msg, 'notif_pembayaran');
                    }
                }
            } else if ($konsumen_id) { // Collective Payment
                $kons = $this->db->get_where('tbl_konsumen', ['konsumen_id' => $konsumen_id])->row();
                if ($kons && !empty($kons->konsumen_nohp)) {
                    $wa_msg = "*PEMBAYARAN PIUTANG (KOLEKTIF) DITERIMA*\n\n";
                    $wa_msg .= "Halo *" . $kons->konsumen_nama . "*,\n";
                    $wa_msg .= "Pembayaran piutang kolektif Anda telah kami terima sebesar *Rp " . number_format($additional_payment, 0, ',', '.') . "*.\n\n";
                    $wa_msg .= "Terima kasih telah mempercayakan kebutuhan cetak Anda kepada kami.\n\n";
                    $wa_msg .= "_Pesan otomatis via ERP TGM Print_";

                    send_wa_notif($kons->konsumen_nohp, $wa_msg, 'notif_pembayaran');
                }
            }
            // ----------------------------------------------

            $this->session->set_flashdata('success', 'Pembayaran piutang berhasil diperbarui');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui pembayaran piutang');
        }

        redirect('laporan/laporan_piutang');
    }

    public function get_unpaid_piutang_by_konsumen()
    {
        $konsumen_id = $this->input->post('konsumen_id', true);
        if (!$konsumen_id) {
            echo json_encode(['success' => false, 'message' => 'ID Konsumen tidak valid']);
            return;
        }

        $this->load->model('M_pembayaran');
        $data = $this->M_pembayaran->getUnpaidPiutangByKonsumen($konsumen_id);

        // Format for display
        foreach ($data as $d) {
            $d->sisa = $d->total_tagihan - $d->total_bayar;
        }

        echo json_encode(['success' => true, 'data' => $data]);
    }

    /**
     * Get payment history for piutang
     */
    public function get_payment_history()
    {
        $nospk = $this->input->post('nospk', true);

        if (!$nospk) {
            echo json_encode(['success' => false, 'message' => 'No SPK tidak valid']);
            return;
        }

        // Load model pembayaran
        $this->load->model('M_pembayaran');

        $payment_history = $this->M_pembayaran->getPiutangPaymentHistory($nospk);

        if ($payment_history) {
            echo json_encode(['success' => true, 'data' => $payment_history]);
        } else {
            echo json_encode(['success' => false, 'data' => []]);
        }
    }

    /**
     * Export laporan piutang ke Excel
     */
    public function export_laporan_piutang()
    {
        $perusahaan_id = $this->session->userdata('perusahaan_id');
        $status = $this->input->get('status', true);

        // Load model pembayaran
        $this->load->model('M_pembayaran');

        $piutang_list = $this->M_pembayaran->getPiutangList($perusahaan_id, $status);
        $piutang_summary = $this->M_pembayaran->getPiutangSummary($perusahaan_id);

        // Load PHPExcel library
        require_once APPPATH . 'third_party/PHPExcel/PHPExcel.php';

        $excel = new PHPExcel();

        // Set document properties
        $excel->getProperties()
            ->setCreator($this->session->userdata('username'))
            ->setLastModifiedBy($this->session->userdata('username'))
            ->setTitle('Laporan Piutang')
            ->setSubject('Laporan Piutang - ' . date('d F Y'))
            ->setDescription('Laporan piutang perusahaan');

        // Set active sheet
        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'LAPORAN PIUTANG');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        // Set date
        $sheet->setCellValue('A2', 'Tanggal: ' . date('d F Y'));
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        // Summary
        $sheet->setCellValue('A4', 'RINGKASAN PIUTANG');
        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(14);

        $summary_data = [
            ['Total Piutang', $piutang_summary->total_piutang ?? 0],
            ['Total Sisa Piutang', 'Rp ' . number_format($piutang_summary->total_sisa_piutang ?? 0, 0, ',', '.')],
            ['Belum Lunas', $piutang_summary->belum_lunas ?? 0],
            ['Sebagian', $piutang_summary->sebagian ?? 0],
            ['Lunas', $piutang_summary->lunas ?? 0],
            ['Jatuh Tempo', $piutang_summary->jatuh_tempo ?? 0]
        ];

        $row = 5;
        foreach ($summary_data as $data) {
            $sheet->setCellValue('A' . $row, $data[0]);
            $sheet->setCellValue('B' . $row, $data[1]);
            $row++;
        }

        // Detail table headers
        $headers = ['No', 'No SPK', 'Konsumen', 'No HP', 'Total Tagihan', 'Total Bayar', 'Sisa Piutang', 'Jatuh Tempo', 'Status'];

        $col = 'A';
        $row = 10;

        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
            $sheet->getStyle($col . $row)->getFont()->getColor()->setRGB('FFFFFF');
            $col++;
        }

        // Detail data
        $row = 11;
        $no = 1;
        foreach ($piutang_list as $piutang) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $piutang->nospk);
            $sheet->setCellValue('C' . $row, $piutang->konsumen_nama);
            $sheet->setCellValue('D' . $row, $piutang->konsumen_nohp);
            $sheet->setCellValue('E' . $row, 'Rp ' . number_format($piutang->total_tagihan, 0, ',', '.'));
            $sheet->setCellValue('F' . $row, 'Rp ' . number_format($piutang->total_bayar, 0, ',', '.'));
            $sheet->setCellValue('G' . $row, 'Rp ' . number_format($piutang->sisa_piutang, 0, ',', '.'));
            $sheet->setCellValue('H' . $row, date('d/m/Y', strtotime($piutang->jatuh_tempo)));
            $sheet->setCellValue('I' . $row, $piutang->status);

            // Format currency columns
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');

            $row++;
        }

        // Auto size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set filename
        $filename = 'Laporan_Piutang_' . date('Y-m-d') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Save file
        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $writer->save('php://output');
        exit;
    }

    // Hutang

    function hutang()
    {
        $controller = $this->uri->segment(1);
        $hariini = date('Y-m-d');
        $userid = $this->session->userdata('id');
        // Get perusahaan_id from GET or default to session
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: $this->session->userdata('perusahaan_id');

        // Check user level
        $user_level = $this->session->userdata('level');
        $is_admin = ($user_level == 1);
        $is_owner = ($user_level == 17);

        // Get consolidated IDs
        $ids = $this->_get_ids($perusahaan_id);

        // Get filter parameters
        $status = $this->input->get('status', true);
        $limit = $this->input->get('limit', true) ?: 50;

        // Load model hutang
        $this->load->model('M_hutang');

        // Get company info
        $perusahaan_name = 'TGM ERP System';
        $p_info = $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $perusahaan_id))->row_array();
        if ($p_info) {
            $perusahaan_name = $p_info['nama_perusahaan'];
        }

        // Get hutang data and summary
        // Note: Currently M_hutang might not support branch filtering, 
        // but we pass it for future-proofing and consistency.
        $hutang_data = $this->M_hutang->getAllHutang($limit, $status);
        $hutang_summary = $this->M_hutang->getHutangSummary();

        // Calculate totals for display
        $total_hutang = $hutang_summary->total_tagihan ?? 0;
        $total_bayar = $hutang_summary->total_bayar ?? 0;
        $total_sisa = $hutang_summary->total_sisa_hutang ?? 0;
        $jatuh_tempo_count = $hutang_summary->jatuh_tempo ?? 0;

        $data = array(
            'controller' => $controller,
            'function' => 'hutang',
            'title' => 'Laporan Hutang Supplier',
            'hariini' => $hariini,
            'tampilData' => $hutang_data,
            'hutang_summary' => $hutang_summary,
            'total_hutang' => $total_hutang,
            'total_bayar' => $total_bayar,
            'total_sisa' => $total_sisa,
            'jatuh_tempo_count' => $jatuh_tempo_count,
            'status_filter' => $status,
            'limit_filter' => $limit,
            'current_perusahaan_id' => $perusahaan_id,
            'perusahaan' => $perusahaan_name,
            'is_admin_owner' => ($is_admin || $is_owner),
            'all_perusahaan' => ($is_admin || $is_owner) ? $this->db->get_where('tbl_perusahaan', ['status' => '1'])->result() : $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->result(),
            'all_supplier' => $this->db->get('tbl_supplier')->result()
        );

        $this->load_admin_view('admin/laporan/keuangan/hutang/index', $data);
    }

    /**
     * Tambah Hutang Manual (Lain-lain)
     */
    public function tambah_hutang_manual()
    {
        $no_faktur = $this->input->post('no_faktur', true);
        $supplier_id = $this->input->post('supplier_id', true);
        $tanggal_tagihan = $this->input->post('tanggal_tagihan', true);
        $jatuh_tempo = $this->input->post('jatuh_tempo', true);
        $total_tagihan = rupiahToInt($this->input->post('total_tagihan', true));
        $keterangan = $this->input->post('keterangan', true);

        if (!$no_faktur || !$supplier_id || $total_tagihan <= 0 || !$jatuh_tempo || !$tanggal_tagihan) {
            $this->session->set_flashdata('error', 'Data tidak valid. Pastikan semua field wajib diisi.');
            redirect('laporan/hutang');
            return;
        }

        $this->load->model('M_hutang');

        $data = [
            'no_faktur' => $no_faktur,
            'supplier_id' => $supplier_id,
            'tanggal_tagihan' => $tanggal_tagihan,
            'jatuh_tempo' => $jatuh_tempo,
            'total_tagihan' => $total_tagihan,
            'keterangan' => $keterangan
        ];

        $success = $this->M_hutang->addManualHutang($data);

        if ($success) {
            // [AUDIT LOG]
            $this->_log_manual("Input Hutang Manual: $no_faktur", "Total: Rp " . number_format($total_tagihan, 0, ',', '.'), 'WRITE');
            $this->session->set_flashdata('success', 'Hutang manual berhasil ditambahkan dan tercatat di Ledger.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambahkan data hutang.');
        }

        redirect('laporan/hutang');
    }

    /**
     * Update pembayaran hutang
     */
    public function update_pembayaran_hutang()
    {
        $hutang_id = $this->input->post('hutang_id', true);
        $jumlah_bayar = rupiahToInt($this->input->post('jumlah_bayar', true));
        $metode_bayar = $this->input->post('metode_bayar', true);
        $rekening_tujuan = trim($this->input->post('rekening_tujuan', true));
        $keterangan = $this->input->post('keterangan', true);

        if (!$hutang_id || $jumlah_bayar <= 0 || !$metode_bayar) {
            $this->session->set_flashdata('error', 'Data tidak valid. Pastikan semua field wajib diisi.');
            redirect('laporan/hutang');
            return;
        }

        // Validate rekening_tujuan for transfer payment
        if ($metode_bayar == 'transfer' && !$rekening_tujuan) {
            $this->session->set_flashdata('error', 'Isi rekening tujuan untuk pembayaran transfer');
            redirect('laporan/hutang');
            return;
        }

        // Load model hutang
        $this->load->model('M_hutang');

        $success = $this->M_hutang->updateHutangPaymentWithMethod($hutang_id, $jumlah_bayar, $metode_bayar, $rekening_tujuan, $keterangan);

        if ($success) {
            $this->session->set_flashdata('success', 'Pembayaran hutang berhasil diperbarui');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui pembayaran hutang');
        }

        redirect('laporan/hutang');
    }

    /**
     * Export laporan hutang ke Excel
     */
    public function export_laporan_hutang()
    {
        $status = $this->input->get('status', true);
        $date_from = $this->input->get('date_from', true);
        $date_to = $this->input->get('date_to', true);

        // Load model hutang
        $this->load->model('M_hutang');

        $hutang_list = $this->M_hutang->getHutangForExport($status, $date_from, $date_to);

        // Load PHPExcel library
        require_once APPPATH . 'third_party/PHPExcel/PHPExcel.php';

        $excel = new PHPExcel();
        $excel->getProperties()->setTitle("Laporan Hutang");
        $excel->getProperties()->setSubject("Laporan Hutang");
        $excel->getProperties()->setDescription("Laporan Hutang Perusahaan");

        $sheet = $excel->getActiveSheet();
        $sheet->setTitle("Laporan Hutang");

        // Header
        $sheet->setCellValue('A1', 'LAPORAN HUTANG PERUSAHAAN');
        $sheet->setCellValue('A2', 'Tanggal: ' . date('d F Y'));
        $sheet->setCellValue('A3', 'Dicetak pada: ' . date('d/m/Y H:i:s'));

        // Merge cells for title
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:H3');

        // Style title
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A3')->getFont()->setSize(10);

        // Table headers
        $headers = ['No', 'No Faktur', 'Supplier', 'Tanggal Pembelian', 'Jatuh Tempo', 'Total Tagihan', 'Total Bayar', 'Sisa Hutang', 'Status'];

        $col = 'A';
        $row = 5;

        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
            $sheet->getStyle($col . $row)->getFont()->getColor()->setRGB('FFFFFF');
            $col++;
        }

        // Data
        $row = 6;
        $no = 1;
        foreach ($hutang_list as $hutang) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $hutang->no_faktur);
            $sheet->setCellValue('C' . $row, $hutang->supplier_nama);
            $sheet->setCellValue('D' . $row, date('d/m/Y', strtotime($hutang->pembelian_tgl)));
            $sheet->setCellValue('E' . $row, date('d/m/Y', strtotime($hutang->jatuh_tempo)));
            $sheet->setCellValue('F' . $row, 'Rp ' . number_format($hutang->total_tagihan, 0, ',', '.'));
            $sheet->setCellValue('G' . $row, 'Rp ' . number_format($hutang->total_bayar, 0, ',', '.'));
            $sheet->setCellValue('H' . $row, 'Rp ' . number_format($hutang->sisa_hutang, 0, ',', '.'));
            $sheet->setCellValue('I' . $row, $hutang->status);

            // Format currency columns
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0');

            $row++;
        }

        // Auto size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set filename
        $filename = 'Laporan_Hutang_' . date('Y-m-d') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Save file
        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $writer->save('php://output');
        exit;
    }

    public function legger()
    {
        $userid = $this->session->userdata('id');
        $user_level = $this->session->userdata('level');
        $is_admin = ($user_level == 1);
        $is_owner = ($user_level == 17);

        // Get perusahaan_id selection
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: $this->session->userdata('perusahaan_id');
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        // Get date range or default to current month
        $mulai = $this->input->get('mulai', true) ?: date('Y-m-01');
        $selesai = $this->input->get('selesai', true) ?: date('Y-m-t');

        // Fetch Data
        $legger = $this->M_transaksi->get_legger($mulai, $selesai, $ids_sql)->result();

        // Calculate Totals for Stats
        $total_cash = 0;
        $total_transfer = 0;
        $total_edc = 0;
        $total_ewallet = 0;
        $total_piutang = 0;
        $grand_total = 0;

        foreach ($legger as $row) {
            $total_cash += $row->total_cash;
            $total_transfer += $row->total_transfer;
            $total_edc += $row->total_edc;
            $total_ewallet += $row->total_ewallet;
            $total_piutang += $row->total_piutang;
            $grand_total += $row->total_harian;
        }

        // Get company name
        $perusahaan_name = 'TGM ERP System';
        $p_info = $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $perusahaan_id))->row_array();
        if ($p_info) {
            $perusahaan_name = $p_info['nama_perusahaan'];
        }

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Buku Besar Bulanan (Legger)',
            'perusahaan' => $perusahaan_name,
            'bulan' => date('F Y', strtotime($mulai)),
            'mulai' => $mulai,
            'selesai' => $selesai,
            'legger' => $legger,
            'cash' => $total_cash,
            'transfer' => $total_transfer,
            'edc' => $total_edc,
            'ewallet' => $total_ewallet,
            'piutang' => $total_piutang,
            'grandtotal' => $grand_total,
            'current_perusahaan_id' => $perusahaan_id,
            'is_admin_owner' => ($is_admin || $is_owner),
            'all_perusahaan' => ($is_admin || $is_owner) ? $this->db->get_where('tbl_perusahaan', ['status' => '1'])->result() : $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->result()
        );

        $this->load_admin_view('admin/laporan/keuangan/legger', $data);
    }

    public function export_legger()
    {
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: $this->session->userdata('perusahaan_id');
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        $mulai = $this->input->get('mulai', true) ?: date('Y-m-01');
        $selesai = $this->input->get('selesai', true) ?: date('Y-m-t');

        $data = $this->M_transaksi->get_legger($mulai, $selesai, $ids_sql)->result();

        // Load PHPExcel
        require_once APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        // Title & Info
        $p_info = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row();
        $sheet->setCellValue('A1', 'BUKU BESAR BULANAN (LEGGER) - ' . ($p_info ? $p_info->nama_perusahaan : 'TGM'));
        $sheet->setCellValue('A2', 'PERIODE: ' . $mulai . ' s/d ' . $selesai);
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(12);

        // Header
        $headers = ['TANGGAL', 'CASH', 'TRANSFER', 'EDC/DEBIT', 'E-WALLET', 'PIUTANG', 'TOTAL HARIAN'];
        $col = 0;
        foreach ($headers as $h) {
            $sheet->setCellValueByColumnAndRow($col, 4, $h);
            $sheet->getStyleByColumnAndRow($col, 4)->getFont()->setBold(true);
            $sheet->getStyleByColumnAndRow($col, 4)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $col++;
        }

        // Data
        $rowNum = 5;
        $total_cash = 0;
        $total_transfer = 0;
        $total_edc = 0;
        $total_ewallet = 0;
        $total_piutang = 0;
        $grand_total = 0;

        foreach ($data as $row) {
            $sheet->setCellValueByColumnAndRow(0, $rowNum, $row->tgl_bayar);
            $sheet->setCellValueByColumnAndRow(1, $rowNum, $row->total_cash);
            $sheet->setCellValueByColumnAndRow(2, $rowNum, $row->total_transfer);
            $sheet->setCellValueByColumnAndRow(3, $rowNum, $row->total_edc);
            $sheet->setCellValueByColumnAndRow(4, $rowNum, $row->total_ewallet);
            $sheet->setCellValueByColumnAndRow(5, $rowNum, $row->total_piutang);
            $sheet->setCellValueByColumnAndRow(6, $rowNum, $row->total_harian);

            $total_cash += $row->total_cash;
            $total_transfer += $row->total_transfer;
            $total_edc += $row->total_edc;
            $total_ewallet += $row->total_ewallet;
            $total_piutang += $row->total_piutang;
            $grand_total += $row->total_harian;
            $rowNum++;
        }

        // Footer
        $sheet->setCellValueByColumnAndRow(0, $rowNum, 'TOTAL AKUMULASI');
        $sheet->setCellValueByColumnAndRow(1, $rowNum, $total_cash);
        $sheet->setCellValueByColumnAndRow(2, $rowNum, $total_transfer);
        $sheet->setCellValueByColumnAndRow(3, $rowNum, $total_edc);
        $sheet->setCellValueByColumnAndRow(4, $rowNum, $total_ewallet);
        $sheet->setCellValueByColumnAndRow(5, $rowNum, $total_piutang);
        $sheet->setCellValueByColumnAndRow(6, $rowNum, $grand_total);
        $sheet->getStyle('A' . $rowNum . ':G' . $rowNum)->getFont()->setBold(true);

        // Format
        $sheet->getStyle('B5:G' . $rowNum)->getNumberFormat()->setFormatCode('#,##0');
        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $filename = "Legger_Keuangan_" . str_replace('-', '', $mulai) . "_" . str_replace('-', '', $selesai) . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    // function transaksi_manual(){
    //     $data = array(
    //         'title' => 'Laporan Transaksi Manual',
    //         // 'transaksi' => $this->Penjualan_manual_model->get_transaksi_manual()->result(),
    //     );
    //     $this->load_admin_view('admin/laporan/transaksi_manual', $data);
    // }

    function rekap_transaksi_manual()
    {
        $userid = $this->session->userdata('id');
        // Get perusahaan_id from GET or default to session
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: $this->session->userdata('perusahaan_id');
        $bulan = $this->input->get('bulan', true) ?: date('Y-m');

        // Check user level
        $user_level = $this->session->userdata('level');
        $is_admin = ($user_level == 1);
        $is_owner = ($user_level == 17);

        // Get consolidated IDs
        $ids = $this->_get_ids($perusahaan_id);

        // Get company info
        $perusahaan_name = 'TGM ERP System';
        $p_info = $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $perusahaan_id))->row_array();
        if ($p_info) {
            $perusahaan_name = $p_info['nama_perusahaan'];
        }

        // Get transaction data for the month
        $transaksi_data = $this->M_laporan->get_rekap_transaksi_manual($ids, $bulan);

        // Calculate summary statistics
        $total_transaksi = count($transaksi_data);
        $total_pendapatan = 0;
        $total_piutang = 0;
        $metode_pembayaran = [];

        foreach ($transaksi_data as $transaksi) {
            $total_pendapatan += $transaksi->total_tagihan;

            if ($transaksi->status_pembayaran == 'pending') {
                $total_piutang += ($transaksi->total_tagihan - $transaksi->total_bayar);
            }

            // Count payment methods
            if ($transaksi->metode) {
                $metode = ucfirst($transaksi->metode);
                if (!isset($metode_pembayaran[$metode])) {
                    $metode_pembayaran[$metode] = 0;
                }
                $metode_pembayaran[$metode]++;
            }
        }

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => 'rekap_transaksi_manual',
            'title' => 'Rekap Transaksi Manual',
            'bulan' => $bulan,
            'transaksi' => $transaksi_data,
            'total_transaksi' => $total_transaksi,
            'total_pendapatan' => $total_pendapatan,
            'total_piutang' => $total_piutang,
            'metode_pembayaran' => $metode_pembayaran,
            'current_perusahaan_id' => $perusahaan_id,
            'perusahaan' => $perusahaan_name,
            'is_admin_owner' => ($is_admin || $is_owner),
            'all_perusahaan' => ($is_admin || $is_owner) ? $this->db->get_where('tbl_perusahaan', ['status' => '1'])->result() : $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->result()
        );

        $this->load_admin_view('admin/laporan/rekap_transaksi_manual', $data);
    }

    function export_transaksi_manual()
    {
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: $this->session->userdata('perusahaan_id');
        $ids = $this->_get_ids($perusahaan_id);
        $bulan = $this->input->get('bulan', true) ? $this->input->get('bulan', true) : date('Y-m');

        // Get transaction data
        $transaksi_data = $this->M_laporan->get_rekap_transaksi_manual($ids, $bulan);

        // Load PHPExcel library
        $this->load->library('excel');

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("TGM ERP")
            ->setLastModifiedBy("TGM ERP")
            ->setTitle("Rekap Transaksi Manual - " . date('F Y', strtotime($bulan . '-01')))
            ->setSubject("Laporan Transaksi Manual")
            ->setDescription("Rekap transaksi penjualan manual untuk periode " . date('F Y', strtotime($bulan . '-01')));

        // Add header
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'REKAP TRANSAKSI MANUAL')
            ->setCellValue('A2', 'Periode: ' . date('F Y', strtotime($bulan . '-01')))
            ->setCellValue('A3', 'Tanggal Export: ' . date('d/m/Y H:i:s'));

        // Add table headers
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A5', 'No. SPK')
            ->setCellValue('B5', 'Tanggal')
            ->setCellValue('C5', 'Konsumen')
            ->setCellValue('D5', 'Telepon')
            ->setCellValue('E5', 'Produk')
            ->setCellValue('F5', 'Qty')
            ->setCellValue('G5', 'Total Tagihan')
            ->setCellValue('H5', 'Total Bayar')
            ->setCellValue('I5', 'Status')
            ->setCellValue('J5', 'Metode Bayar')
            ->setCellValue('K5', 'Referensi')
            ->setCellValue('L5', 'Keterangan');

        // Style header
        $objPHPExcel->getActiveSheet()->getStyle('A1:L1')->getFont()->setBold(true)->setSize(16);
        $objPHPExcel->getActiveSheet()->getStyle('A5:L5')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A5:L5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');

        // Add data
        $row = 6;
        foreach ($transaksi_data as $transaksi) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $row, $transaksi->nospk)
                ->setCellValue('B' . $row, date('d/m/Y H:i', strtotime($transaksi->created_at)))
                ->setCellValue('C' . $row, $transaksi->konsumen_nama)
                ->setCellValue('D' . $row, $transaksi->konsumen_nohp ?? '-')
                ->setCellValue('E' . $row, $transaksi->product_nama)
                ->setCellValue('F' . $row, $transaksi->qty)
                ->setCellValue('G' . $row, $transaksi->total_tagihan)
                ->setCellValue('H' . $row, $transaksi->total_bayar ?? 0)
                ->setCellValue('I' . $row, $transaksi->status_pembayaran == 'paid' ? 'Lunas' : 'Piutang')
                ->setCellValue('J' . $row, $transaksi->metode ? ucfirst($transaksi->metode) : '-')
                ->setCellValue('K' . $row, $transaksi->referensi ?? '-')
                ->setCellValue('L' . $row, $transaksi->keterangan ?? '-');
            $row++;
        }

        // Auto size columns
        foreach (range('A', 'L') as $col) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
        }

        // Set active sheet index to the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to client browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Rekap_Transaksi_Manual_' . $bulan . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    function print_transaksi_manual()
    {
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: $this->session->userdata('perusahaan_id');
        $ids = $this->_get_ids($perusahaan_id);
        $bulan = $this->input->get('bulan', true) ? $this->input->get('bulan', true) : date('Y-m');

        // Get transaction data
        $transaksi_data = $this->M_laporan->get_rekap_transaksi_manual($ids, $bulan);

        // Calculate summary statistics
        $total_transaksi = count($transaksi_data);
        $total_pendapatan = 0;
        $total_piutang = 0;
        $metode_pembayaran = [];

        foreach ($transaksi_data as $transaksi) {
            $total_pendapatan += $transaksi->total_tagihan;

            if ($transaksi->status_pembayaran == 'pending') {
                $total_piutang += ($transaksi->total_tagihan - $transaksi->total_bayar);
            }

            // Count payment methods
            if ($transaksi->metode) {
                $metode = ucfirst($transaksi->metode);
                if (!isset($metode_pembayaran[$metode])) {
                    $metode_pembayaran[$metode] = 0;
                }
                $metode_pembayaran[$metode]++;
            }
        }

        $data = array(
            'title' => 'Rekap Transaksi Manual',
            'bulan' => $bulan,
            'transaksi' => $transaksi_data,
            'total_transaksi' => $total_transaksi,
            'total_pendapatan' => $total_pendapatan,
            'total_piutang' => $total_piutang,
            'metode_pembayaran' => $metode_pembayaran
        );

        // Load print view
        $this->load->view('admin/laporan/print_transaksi_manual', $data);
    }

    public function export_pemasukan_bulanan()
    {
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: $this->session->userdata('perusahaan_id');
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        $bulan_ini = $this->input->get('bulan', true) ?: date('Y-m');
        $start_date = $bulan_ini . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));

        // 1. Transaksi Langsung (Sales Payment)
        $transaksi = $this->db->query("
                SELECT p.tgl_bayar as tanggal, p.jam_bayar as jam, p.nospk, 
                       k.konsumen_nama as konsumen,
                       p.total_bayar as total,
                       'Transaksi' as jenis,
                       u.username as kasir
                FROM tbl_pembayaran p
                LEFT JOIN tbl_konsumen k ON p.konsumen_id = k.konsumen_id
                LEFT JOIN users u ON p.kasir_id = u.user_id
                WHERE (p.tgl_bayar BETWEEN '$start_date' AND '$end_date') 
                  AND p.perusahaan_id IN ($ids_sql)
                  AND p.total_bayar > 0
            ")->result_array();

        // 2. Pelunasan Piutang
        $piutang = $this->db->query("
                SELECT pp.tanggal_bayar as tanggal, TIME(pp.created_at) as jam, p.nospk,
                       k.konsumen_nama as konsumen,
                       pp.jumlah_bayar as total,
                       'Pelunasan Piutang' as jenis,
                       '-' as kasir
                FROM tbl_piutang_pembayaran pp
                JOIN tbl_piutang p ON pp.piutang_id = p.piutang_id
                LEFT JOIN tbl_konsumen k ON p.konsumen_id = k.konsumen_id
                WHERE (DATE(pp.tanggal_bayar) BETWEEN '$start_date' AND '$end_date') 
                  AND p.perusahaan_id IN ($ids_sql)
            ")->result_array();

        // Merge and Sort
        $data = array_merge($transaksi, $piutang);
        usort($data, function ($a, $b) {
            $t1 = strtotime($a['tanggal'] . ' ' . $a['jam']);
            $t2 = strtotime($b['tanggal'] . ' ' . $b['jam']);
            return $t1 - $t2;
        });

        // Load PHPExcel
        require_once APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        // Header Style
        $styleHeader = array(
            'font' => array('bold' => true),
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
        );

        // Title
        $p_info = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row();
        $sheet->setCellValue('A1', 'LAPORAN PEMASUKAN REAL (CASH FLOW) - ' . ($p_info ? $p_info->nama_perusahaan : 'TGM'));
        $sheet->setCellValue('A2', 'PERIODE: ' . date('F Y', strtotime($start_date)));
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true);

        // Table Headers
        $headers = ['TANGGAL', 'JAM', 'JENIS', 'NOMOR SPK', 'KONSUMEN', 'NOMINAL MASUK', 'KASIR'];
        $col = 0;
        foreach ($headers as $h) {
            $sheet->setCellValueByColumnAndRow($col, 4, $h);
            $sheet->getStyleByColumnAndRow($col, 4)->applyFromArray($styleHeader);
            $col++;
        }

        // Fill Data
        $rowNum = 5;
        $total_pemasukan = 0;
        foreach ($data as $row) {
            $sheet->setCellValueByColumnAndRow(0, $rowNum, date('d/m/Y', strtotime($row['tanggal'])));
            $sheet->setCellValueByColumnAndRow(1, $rowNum, date('H:i', strtotime($row['jam'])));
            $sheet->setCellValueByColumnAndRow(2, $rowNum, $row['jenis']);
            $sheet->setCellValueByColumnAndRow(3, $rowNum, $row['nospk']);
            $sheet->setCellValueByColumnAndRow(4, $rowNum, $row['konsumen']);
            $sheet->setCellValueByColumnAndRow(5, $rowNum, $row['total']);
            $sheet->setCellValueByColumnAndRow(6, $rowNum, $row['kasir']);

            $total_pemasukan += $row['total'];
            $rowNum++;
        }

        // Summary Footer
        $sheet->setCellValueByColumnAndRow(4, $rowNum, 'TOTAL PEMASUKAN');
        $sheet->setCellValueByColumnAndRow(5, $rowNum, $total_pemasukan);
        $sheet->getStyle('A' . $rowNum . ':G' . $rowNum)->getFont()->setBold(true);
        $sheet->getStyle('F5:F' . $rowNum)->getNumberFormat()->setFormatCode('#,##0');

        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $filename = "Laporan_Pemasukan_Bulanan_" . str_replace('-', '', $bulan_ini) . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function export_pemasukan_harian()
    {
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: $this->session->userdata('perusahaan_id');
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        $tanggal = $this->input->get('tanggal', true) ?: date('Y-m-d');

        // Unified Data Source for Daily Income Export (Matching the Dashboard)
        $data = $this->db->query("
                SELECT pd.created_at as full_timestamp, DATE(pd.created_at) as tanggal, TIME(pd.created_at) as jam, p.nospk, 
                       k.konsumen_nama as konsumen,
                       pd.jumlah as total,
                       CASE WHEN DATE(pd.created_at) > p.tgl_bayar THEN 'Pelunasan Piutang' ELSE 'Transaksi' END as jenis,
                       u.username as kasir
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                LEFT JOIN tbl_konsumen k ON p.konsumen_id = k.konsumen_id
                LEFT JOIN users u ON p.kasir_id = u.user_id
                WHERE DATE(pd.created_at) = ? 
                  AND p.perusahaan_id IN ($ids_sql)
                  AND pd.jumlah > 0
                ORDER BY pd.created_at ASC
            ", [$tanggal])->result_array();

        // Load PHPExcel
        require_once APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        // Header Style
        $styleHeader = array(
            'font' => array('bold' => true),
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
        );

        // Title
        $p_info = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row();
        $sheet->setCellValue('A1', 'LAPORAN PEMASUKAN HARIAN (REAL CASH) - ' . ($p_info ? $p_info->nama_perusahaan : 'TGM'));
        $sheet->setCellValue('A2', 'TANGGAL: ' . date('d F Y', strtotime($tanggal)));
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true);

        // Table Headers
        $headers = ['TANGGAL', 'JAM', 'JENIS', 'NOMOR SPK', 'KONSUMEN', 'NOMINAL MASUK', 'KASIR'];
        $col = 0;
        foreach ($headers as $h) {
            $sheet->setCellValueByColumnAndRow($col, 4, $h);
            $sheet->getStyleByColumnAndRow($col, 4)->applyFromArray($styleHeader);
            $col++;
        }

        // Fill Data
        $rowNum = 5;
        $total_pemasukan = 0;
        foreach ($data as $row) {
            $sheet->setCellValueByColumnAndRow(0, $rowNum, date('d/m/Y', strtotime($row['tanggal'])));
            $sheet->setCellValueByColumnAndRow(1, $rowNum, date('H:i', strtotime($row['jam'])));
            $sheet->setCellValueByColumnAndRow(2, $rowNum, $row['jenis']);
            $sheet->setCellValueByColumnAndRow(3, $rowNum, $row['nospk']);
            $sheet->setCellValueByColumnAndRow(4, $rowNum, $row['konsumen']);
            $sheet->setCellValueByColumnAndRow(5, $rowNum, $row['total']);
            $sheet->setCellValueByColumnAndRow(6, $rowNum, $row['kasir']);

            $total_pemasukan += $row['total'];
            $rowNum++;
        }

        // Summary Footer
        $sheet->setCellValueByColumnAndRow(4, $rowNum, 'TOTAL PEMASUKAN');
        $sheet->setCellValueByColumnAndRow(5, $rowNum, $total_pemasukan);
        $sheet->getStyle('A' . $rowNum . ':G' . $rowNum)->getFont()->setBold(true);
        $sheet->getStyle('F5:F' . $rowNum)->getNumberFormat()->setFormatCode('#,##0');

        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $filename = "Laporan_Pemasukan_Harian_" . str_replace('-', '', $tanggal) . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    function mutasi_bank()
    {
        $data = $this->_prepare_common_data('Mutasi Bank (System Record)');
        $perusahaan_id = $data['perusahaan_id'];
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        $start_date = $this->input->get('start_date', true) ?: date('Y-m-01');
        $end_date = $this->input->get('end_date', true) ?: date('Y-m-d');
        $bank_id = $this->input->get('bank_id', true);

        // Populate Bank List
        $data['bank_list'] = $this->db->get('tbl_rekening')->result();

        $mutasi = [];
        $total_masuk = 0;
        $total_keluar = 0;

        if ($bank_id) {
            // 1. Masuk dari Penerimaan (Menggunakan sumber tunggal tbl_pembayaran_detail untuk menghindari double count)
            $q1 = $this->db->query("
                SELECT DATE(pd.created_at) as tanggal, TIME(pd.created_at) as jam, p.nospk, 
                       k.konsumen_nama as konsumen,
                       pd.jumlah,
                       CASE WHEN DATE(pd.created_at) > p.tgl_bayar THEN 'piutang' ELSE 'transaksi' END as tipe,
                       CASE WHEN DATE(pd.created_at) > p.tgl_bayar THEN 'Pelunasan Piutang' ELSE 'Pembayaran Transaksi Baru' END as keterangan,
                       'masuk' as arus
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                LEFT JOIN tbl_konsumen k ON p.konsumen_id = k.konsumen_id
                WHERE pd.bank_id = ? 
                  AND (DATE(pd.created_at) BETWEEN ? AND ?)
                  AND p.perusahaan_id IN ($ids_sql)
            ", [$bank_id, $start_date, $end_date])->result_array();

            // 2. Keluar dari Pengeluaran
            $q2 = $this->db->query("
                SELECT pengeluaran_tgl as tanggal, 
                       '00:00:00' as jam, 
                       '-' as nospk,
                       pengeluaran_penerima as konsumen,
                       pengeluaran_jumlah as jumlah,
                       'pengeluaran' as tipe,
                       pengeluaran_detail as keterangan,
                       'keluar' as arus
                FROM tbl_pengeluaran 
                WHERE rekening_id = ? 
                  AND (pengeluaran_tgl BETWEEN ? AND ?)
                  AND perusahaan_id IN ($ids_sql)
            ", [$bank_id, $start_date, $end_date])->result_array();

            $mutasi = array_merge($q1, $q2);

            // Sort by Date + Time
            usort($mutasi, function ($a, $b) {
                $jam_a = !empty($a['jam']) ? $a['jam'] : '00:00:00';
                $jam_b = !empty($b['jam']) ? $b['jam'] : '00:00:00';

                $t1 = strtotime($a['tanggal'] . ' ' . $jam_a);
                $t2 = strtotime($b['tanggal'] . ' ' . $jam_b);
                return $t1 - $t2;
            });

            foreach ($mutasi as $m) {
                if ($m['arus'] == 'masuk') {
                    $total_masuk += $m['jumlah'];
                } else {
                    $total_keluar += $m['jumlah'];
                }
            }
        }

        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['selected_bank'] = $bank_id;
        $data['mutasi'] = $mutasi;
        $data['total_masuk'] = $total_masuk;
        $data['total_keluar'] = $total_keluar;

        $this->load_admin_view('admin/laporan/keuangan/mutasi_bank', $data);
    }

    public function mutasi_cash()
    {
        $data = $this->_prepare_common_data('Laporan Mutasi Transaksi Tunai (Cash)');
        $perusahaan_id = $data['perusahaan_id'];
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        // Get filter parameters
        $tgl_awal = $this->input->get('tgl_awal', true) ?: date('Y-m-01');
        $tgl_akhir = $this->input->get('tgl_akhir', true) ?: date('Y-m-d');

        // 1. Ambil Saldo Awal (Fisik Tunai sebelum tgl_awal)
        $saldo_awal_trx = $this->db->query("
            SELECT SUM(pd.jumlah) as total 
            FROM tbl_pembayaran_detail pd
            JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
            WHERE DATE(pd.created_at) < ? AND pd.metode = 'tunai' AND p.perusahaan_id IN ($ids_sql)
        ", [$tgl_awal])->row()->total ?? 0;

        // Note: Saldo pelunasan piutang sudah ada di tbl_pembayaran_detail, jangan ditambah lagi.

        $saldo_awal_pengeluaran = $this->db->query("
            SELECT SUM(pengeluaran_jumlah) as total 
            FROM tbl_pengeluaran 
            WHERE pengeluaran_tgl < ? AND metode_bayar = 'tunai' AND perusahaan_id IN ($ids_sql)
        ", [$tgl_awal])->row()->total ?? 0;

        $saldo_awal = $saldo_awal_trx - $saldo_awal_pengeluaran;

        // 2. Query Detail Mutasi (Menggunakan satu sumber: tbl_pembayaran_detail)
        $mutasi = $this->db->query("
            SELECT * FROM (
                -- Semua Penerimaan Tunai (DP & Pelunasan)
                SELECT 
                    CASE WHEN DATE(pd.created_at) > p.tgl_bayar THEN 'PIUTANG' ELSE 'TRANSAKSI' END as sumber,
                    DATE(pd.created_at) as tanggal,
                    TIME(pd.created_at) as jam,
                    p.nospk,
                    k.konsumen_nama as konsumen,
                    pd.jumlah as masuk,
                    0 as keluar,
                    CASE 
                        WHEN DATE(pd.created_at) > p.tgl_bayar THEN CONCAT('Pelunasan Piutang SPK: ', p.nospk)
                        ELSE CONCAT('Pembayaran SPK: ', p.nospk)
                    END as keterangan
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                LEFT JOIN tbl_konsumen k ON p.konsumen_id = k.konsumen_id
                WHERE DATE(pd.created_at) BETWEEN ? AND ? 
                    AND pd.metode = 'tunai' 
                    AND p.perusahaan_id IN ($ids_sql)

                UNION ALL

                -- Pengeluaran (Keluar)
                SELECT 
                    'PENGELUARAN' as sumber,
                    pengeluaran_tgl as tanggal,
                    '00:00:00' as jam,
                    '-' as nospk,
                    '-' as konsumen,
                    0 as masuk,
                    pengeluaran_jumlah as keluar,
                    pengeluaran_detail as keterangan
                FROM tbl_pengeluaran
                WHERE pengeluaran_tgl BETWEEN ? AND ? 
                    AND metode_bayar = 'tunai' 
                    AND perusahaan_id IN ($ids_sql)
            ) as combined
            ORDER BY tanggal ASC, jam ASC
        ", [$tgl_awal, $tgl_akhir, $tgl_awal, $tgl_akhir])->result_array();

        $data = array_merge($data, [
            'tgl_awal' => $tgl_awal,
            'tgl_akhir' => $tgl_akhir,
            'saldo_awal' => $saldo_awal,
            'mutasi' => $mutasi,
            'content' => 'admin/laporan/keuangan/mutasi_cash'
        ]);

        $this->load_admin_view('admin/laporan/keuangan/mutasi_cash', $data);
    }


    public function mutasi_edc()
    {
        $data = $this->_prepare_common_data('Mutasi EDC (System Record)');
        $perusahaan_id = $data['perusahaan_id'];
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        $start_date = $this->input->get('start_date', true) ?: date('Y-m-01');
        $end_date = $this->input->get('end_date', true) ?: date('Y-m-d');
        $edc_id = $this->input->get('edc_id', true);

        // Populate EDC List
        $data['edc_list'] = $this->db->get('tbl_mesin_edc')->result();

        $mutasi = [];
        $total_masuk = 0;
        $total_keluar = 0;

        if ($edc_id) {
            // 1. Masuk dari Penerimaan (Menggunakan sumber tunggal tbl_pembayaran_detail untuk menghindari double count)
            $q1 = $this->db->query("
                SELECT DATE(pd.created_at) as tanggal, TIME(pd.created_at) as jam, p.nospk, 
                       k.konsumen_nama as konsumen,
                       pd.jumlah,
                       CASE WHEN DATE(pd.created_at) > p.tgl_bayar THEN 'piutang' ELSE 'transaksi' END as tipe,
                       CASE WHEN DATE(pd.created_at) > p.tgl_bayar THEN 'Pelunasan Piutang' ELSE 'Pembayaran Transaksi Baru' END as keterangan,
                       'masuk' as arus
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                LEFT JOIN tbl_konsumen k ON p.konsumen_id = k.konsumen_id
                WHERE pd.edc_id = ? 
                  AND (DATE(pd.created_at) BETWEEN ? AND ?)
                  AND p.perusahaan_id IN ($ids_sql)
            ", [$edc_id, $start_date, $end_date])->result_array();

            // 2. Keluar dari Pengeluaran
            $q2 = $this->db->query("
                SELECT pengeluaran_tgl as tanggal, 
                       '00:00:00' as jam, 
                       '-' as nospk,
                       pengeluaran_penerima as konsumen,
                       pengeluaran_jumlah as jumlah,
                       'pengeluaran' as tipe,
                       pengeluaran_detail as keterangan,
                       'keluar' as arus
                FROM tbl_pengeluaran 
                WHERE edc_id = ?
                  AND (pengeluaran_tgl BETWEEN ? AND ?)
                  AND perusahaan_id IN ($ids_sql)
            ", [$edc_id, $start_date, $end_date])->result_array();

            $mutasi = array_merge($q1, $q2);

            // Sort by Date + Time
            usort($mutasi, function ($a, $b) {
                $jam_a = !empty($a['jam']) ? $a['jam'] : '00:00:00';
                $jam_b = !empty($b['jam']) ? $b['jam'] : '00:00:00';

                $t1 = strtotime($a['tanggal'] . ' ' . $jam_a);
                $t2 = strtotime($b['tanggal'] . ' ' . $jam_b);
                return $t1 - $t2;
            });

            foreach ($mutasi as $m) {
                if ($m['arus'] == 'masuk') {
                    $total_masuk += $m['jumlah'];
                } else {
                    $total_keluar += $m['jumlah'];
                }
            }
        }

        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['selected_edc'] = $edc_id;
        $data['mutasi'] = $mutasi;
        $data['total_masuk'] = $total_masuk;
        $data['total_keluar'] = $total_keluar;

        $this->load_admin_view('admin/laporan/keuangan/mutasi_edc', $data);
    }

    public function mutasi_ewallet()
    {
        $data = $this->_prepare_common_data('Mutasi E-Wallet (System Record)');
        $perusahaan_id = $data['perusahaan_id'];
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        $start_date = $this->input->get('start_date', true) ?: date('Y-m-01');
        $end_date = $this->input->get('end_date', true) ?: date('Y-m-d');
        $ewallet_id = $this->input->get('ewallet_id', true);

        // Populate E-Wallet List
        $data['ewallet_list'] = $this->db->get('tbl_ewallet')->result();

        $mutasi = [];
        $total_masuk = 0;
        $total_keluar = 0;

        if ($ewallet_id) {
            // 1. Masuk dari Penerimaan
            $q1 = $this->db->query("
                SELECT DATE(pd.created_at) as tanggal, TIME(pd.created_at) as jam, p.nospk, 
                       k.konsumen_nama as konsumen,
                       pd.jumlah,
                       CASE WHEN DATE(pd.created_at) > p.tgl_bayar THEN 'piutang' ELSE 'transaksi' END as tipe,
                       CASE WHEN DATE(pd.created_at) > p.tgl_bayar THEN 'Pelunasan Piutang' ELSE 'Pembayaran Transaksi Baru' END as keterangan,
                       'masuk' as arus
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                LEFT JOIN tbl_konsumen k ON p.konsumen_id = k.konsumen_id
                WHERE pd.ewallet_id = ? 
                  AND (DATE(pd.created_at) BETWEEN ? AND ?)
                  AND p.perusahaan_id IN ($ids_sql)
            ", [$ewallet_id, $start_date, $end_date])->result_array();

            // 2. Keluar dari Pengeluaran
            $q2 = $this->db->query("
                SELECT pengeluaran_tgl as tanggal, 
                       '00:00:00' as jam, 
                       '-' as nospk,
                       pengeluaran_penerima as konsumen,
                       pengeluaran_jumlah as jumlah,
                       'pengeluaran' as tipe,
                       pengeluaran_detail as keterangan,
                       'keluar' as arus
                FROM tbl_pengeluaran 
                WHERE ewallet_id = ?
                  AND (pengeluaran_tgl BETWEEN ? AND ?)
                  AND perusahaan_id IN ($ids_sql)
            ", [$ewallet_id, $start_date, $end_date])->result_array();

            $mutasi = array_merge($q1, $q2);

            // Sort by Date + Time
            usort($mutasi, function ($a, $b) {
                $jam_a = !empty($a['jam']) ? $a['jam'] : '00:00:00';
                $jam_b = !empty($b['jam']) ? $b['jam'] : '00:00:00';

                $t1 = strtotime($a['tanggal'] . ' ' . $jam_a);
                $t2 = strtotime($b['tanggal'] . ' ' . $jam_b);
                return $t1 - $t2;
            });

            foreach ($mutasi as $m) {
                if ($m['arus'] == 'masuk') {
                    $total_masuk += $m['jumlah'];
                } else {
                    $total_keluar += $m['jumlah'];
                }
            }
        }

        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['selected_ewallet'] = $ewallet_id;
        $data['mutasi'] = $mutasi;
        $data['total_masuk'] = $total_masuk;
        $data['total_keluar'] = $total_keluar;

        $this->load_admin_view('admin/laporan/keuangan/mutasi_ewallet', $data);
    }

    public function cetak_pemasukan_harian()
    {
        $data = $this->_prepare_common_data('Cetak Laporan Keuangan Harian');

        // Handle override company if specified in URL (for Admin/Owner)
        $req_perusahaan_id = $this->input->get('perusahaan_id', true);
        if ($req_perusahaan_id && $req_perusahaan_id > 1) {
            $perusahaan_id = $req_perusahaan_id;
            // Override IDs just for this report
            $ids = [$perusahaan_id];
            $data['perusahaan_id'] = $perusahaan_id; // Update view data
        } else {
            $perusahaan_id = $data['perusahaan_id'];
            $ids = $this->_get_ids($perusahaan_id);
        }

        $ids_sql = implode(',', $ids);
        $hariini = $this->input->get('tanggal', true) ?: date('Y-m-d');
        $userid = $this->session->userdata('id');

        // 1. DATA TRANSAKSI HARI INI (Untuk list view & Omset)
        $where_kasir = ($this->session->userdata('level') == 3) ? " AND p.kasir_id = '$userid'" : "";

        $pemasukan_list = $this->db->query("
                 SELECT p.tgl_bayar as tanggal, p.jam_bayar as jam, p.nospk, 
                        k.konsumen_nama as konsumen, k.konsumen_nohp as telepon,
                        p.total_tagihan, 
                        p.total_bayar as bayar_hari_ini,
                        (p.total_tagihan - p.total_bayar) as sisa_piutang,
                        u.username as kasir,
                        CASE WHEN p.status_lunas = '1' THEN 'Lunas' ELSE 'DP/Termin' END as status_transaksi,
                        CASE WHEN p.nospk LIKE 'MAN-%' THEN 'Manual' ELSE 'SPK' END as jenis_label
                 FROM tbl_pembayaran p
                 LEFT JOIN tbl_konsumen k ON p.konsumen_id = k.konsumen_id
                 LEFT JOIN users u ON p.kasir_id = u.user_id
                 WHERE p.tgl_bayar = ? AND p.perusahaan_id IN ($ids_sql) $where_kasir
                 ORDER BY p.tgl_bayar DESC, p.jam_bayar DESC
             ", [$hariini])->result_array();

        // 2. ARUS KAS MASUK (SEMUA PEMBAYARAN HARI INI)
        $transaksi_langsung = $this->db->query("
                 SELECT pd.metode, SUM(pd.jumlah) as total 
                 FROM tbl_pembayaran_detail pd
                 JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                 WHERE DATE(pd.created_at) = ? AND p.perusahaan_id IN ($ids_sql)
                 GROUP BY pd.metode
             ", [$hariini])->result_array();

        // 2b. Breakdown Transfer Bank (Transaksi Langsung + Pelunasan Piutang)
        $breakdown_transfer = $this->db->query("
                 SELECT 
                     COALESCE(r.nama_bank, 'Tidak Terdeteksi') as nama_bank, 
                     COALESCE(r.no_rekening, '-') as no_rekening, 
                     SUM(pd.jumlah) as total 
                 FROM tbl_pembayaran_detail pd
                 JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                 LEFT JOIN tbl_rekening r ON pd.bank_id = r.id
                 WHERE DATE(pd.created_at) = ? 
                   AND p.perusahaan_id IN ($ids_sql) AND pd.metode = 'transfer'
                 GROUP BY pd.bank_id
             ", [$hariini])->result_array();

        // 2c. Breakdown EDC / Debit (Transaksi Langsung + Pelunasan Piutang)
        $breakdown_edc = $this->db->query("
                 SELECT me.edc_nama, SUM(pd.jumlah) as total
                 FROM tbl_pembayaran_detail pd
                 JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                 LEFT JOIN tbl_mesin_edc me ON pd.edc_id = me.edc_id
                 WHERE DATE(pd.created_at) = ? 
                   AND p.perusahaan_id IN ($ids_sql) AND pd.metode = 'debit'
                 GROUP BY pd.edc_id
             ", [$hariini])->result_array();

        // 2d. Breakdown E-Wallet (Transaksi Langsung + Pelunasan Piutang)
        $breakdown_ewallet = $this->db->query("
                 SELECT e.ewallet_nama, SUM(pd.jumlah) as total
                 FROM tbl_pembayaran_detail pd
                 JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                 LEFT JOIN tbl_ewallet e ON pd.ewallet_id = e.id
                 WHERE DATE(pd.created_at) = ? 
                   AND p.perusahaan_id IN ($ids_sql) AND pd.metode = 'ewallet'
                 GROUP BY pd.ewallet_id
             ", [$hariini])->result_array();

        // 3a. Rekap Piutang per Metode
        $rekap_piutang = $this->db->query("
                 SELECT pd.metode, SUM(pd.jumlah) as total 
                 FROM tbl_pembayaran_detail pd
                 JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                 WHERE DATE(pd.created_at) = ? AND DATE(pd.created_at) > p.tgl_bayar 
                   AND p.perusahaan_id IN ($ids_sql)
                 GROUP BY pd.metode
             ", [$hariini])->result_array();

        // 3b. Breakdown Transfer (Pelunasan Piutang)
        $breakdown_transfer_piutang = $this->db->query("
                 SELECT r.nama_bank, r.no_rekening, SUM(pd.jumlah) as total 
                 FROM tbl_pembayaran_detail pd
                 JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                 LEFT JOIN tbl_rekening r ON pd.bank_id = r.id
                 WHERE DATE(pd.created_at) = ? AND DATE(pd.created_at) > p.tgl_bayar 
                   AND p.perusahaan_id IN ($ids_sql) AND pd.metode = 'transfer'
                 GROUP BY pd.bank_id
             ", [$hariini])->result_array();

        // 3c. Breakdown EDC (Pelunasan Piutang)
        $breakdown_edc_piutang = $this->db->query("
                 SELECT me.edc_nama, SUM(pd.jumlah) as total
                 FROM tbl_pembayaran_detail pd
                 JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                 LEFT JOIN tbl_mesin_edc me ON pd.edc_id = me.edc_id
                 WHERE DATE(pd.created_at) = ? AND DATE(pd.created_at) > p.tgl_bayar 
                   AND p.perusahaan_id IN ($ids_sql) AND pd.metode = 'debit'
                 GROUP BY pd.edc_id
             ", [$hariini])->result_array();

        // 3d. Breakdown E-Wallet (Pelunasan Piutang)
        $breakdown_ewallet_piutang = $this->db->query("
                 SELECT e.ewallet_nama, SUM(pd.jumlah) as total
                 FROM tbl_pembayaran_detail pd
                 JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                 LEFT JOIN tbl_ewallet e ON pd.ewallet_id = e.id
                 WHERE DATE(pd.created_at) = ? AND DATE(pd.created_at) > p.tgl_bayar 
                   AND p.perusahaan_id IN ($ids_sql) AND pd.metode = 'ewallet'
                 GROUP BY pd.ewallet_id
             ", [$hariini])->result_array();

        // 4. PENGELUARAN (Expense)
        $pengeluaran_harian = $this->db->query("
                 SELECT 
                     SUM(pengeluaran_jumlah) as total,
                     SUM(CASE 
                         WHEN metode_bayar = 'tunai' THEN pengeluaran_jumlah
                         WHEN metode_bayar IS NULL AND (rekening_id IS NULL OR rekening_id = 0) THEN pengeluaran_jumlah 
                         ELSE 0 
                     END) as total_tunai
                 FROM tbl_pengeluaran 
                 WHERE pengeluaran_tgl = ? AND perusahaan_id IN ($ids_sql)
             ", [$hariini])->row_array();

        // 4b. DETAIL PENGELUARAN (List)
        $list_pengeluaran = $this->db->query("
             SELECT 
                 p.id as pengeluaran_id,
                 p.pengeluaran_tgl,
                 p.pengeluaran_jumlah,
                 p.pengeluaran_detail,
                 p.pengeluaran_penerima,
                 p.metode_bayar,
                 p.rekening_tujuan,
                 r.nama_bank as bank_asal,
                 r.no_rekening as norek_asal
             FROM tbl_pengeluaran p
             LEFT JOIN tbl_rekening r ON p.rekening_id = r.id
             WHERE p.pengeluaran_tgl = ? AND p.perusahaan_id IN ($ids_sql)
             ORDER BY p.id ASC
         ", [$hariini])->result_array();

        // 5. DATA PIUTANG BARU HARI INI
        $piutang_baru = $this->db->query("
             SELECT SUM(total_tagihan - total_bayar) as total 
             FROM tbl_pembayaran 
             WHERE tgl_bayar = ? AND perusahaan_id IN ($ids_sql) AND status_lunas = '0'
         ", [$hariini])->row_array();


        // --- KONSOLIDASI DATA UNTUK LAPORAN ---
        $summary = [
            'cash' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
            'transfer' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
            'debit' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
            'ewallet' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
            'lainnya' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
        ];

        // Mapping Transaction Payments
        foreach ($transaksi_langsung as $t) {
            $key = strtolower($t['metode'] ?? 'lainnya');
            if ($key == 'tunai')
                $key = 'cash';
            if ($key == 'edc')
                $key = 'debit';
            if (isset($summary[$key])) {
                $summary[$key]['transaksi'] += $t['total'];
                $summary[$key]['total'] += $t['total'];
            }
        }

        // Mapping Piutang Payments (dari rekap_piutang)
        foreach ($rekap_piutang as $p) {
            $key = strtolower($p['metode'] ?? 'lainnya');
            if ($key == 'tunai')
                $key = 'cash';
            if ($key == 'edc')
                $key = 'debit';
            if (isset($summary[$key])) {
                $summary[$key]['piutang'] += $p['total'];
            }
        }

        // Hitung Saldo Akhir Fisik (Uang di Laci)
        $total_cash_in_hand = $summary['cash']['total'] - ($pengeluaran_harian['total_tunai'] ?? 0);

        $total_omset_hari_ini = 0;
        $total_omset_manual = 0;
        foreach ($pemasukan_list as $pl) {
            $total_omset_hari_ini += $pl['total_tagihan'];
            if ($pl['jenis_label'] == 'Manual') {
                $total_omset_manual += $pl['total_tagihan'];
            }
        }

        $data = array_merge($data, [
            'hariini' => $hariini,
            'summary' => $summary,
            'pemasukan_list' => $pemasukan_list,
            'list_pengeluaran' => $list_pengeluaran,
            'breakdown_transfer' => $breakdown_transfer,
            'breakdown_transfer_piutang' => $breakdown_transfer_piutang,
            'breakdown_edc' => $breakdown_edc,
            'breakdown_edc_piutang' => $breakdown_edc_piutang,
            'breakdown_ewallet' => $breakdown_ewallet,
            'breakdown_ewallet_piutang' => $breakdown_ewallet_piutang,
            'stat_keuangan' => [
                'omset_penjualan' => $total_omset_hari_ini,
                'omset_manual' => $total_omset_manual,
                'total_uang_masuk' => ($summary['cash']['total'] + $summary['transfer']['total'] + $summary['debit']['total'] + $summary['ewallet']['total']),
                'total_pengeluaran' => ($pengeluaran_harian['total'] ?? 0),
                'saldo_cash_fisik' => $total_cash_in_hand,
                'piutang_baru_terbentuk' => ($piutang_baru['total'] ?? 0)
            ]
        ]);

        $this->load->view('admin/laporan/keuangan/cetak_laporan_harian', $data);
    }

    /**
     * Laporan Produk Terlaris
     * Menampilkan peringkat produk berdasarkan jumlah penjualan
     */
    public function produk_terlaris()
    {
        $period = $this->input->get('period', true) ?: 'month';
        $data = $this->_prepare_common_data('Peringkat Produk Terlaris - ' . ucfirst($period), 'produk_terlaris');
        $perusahaan_ids = $this->_get_ids($data['perusahaan_id']);

        $this->load->model('M_penjualan');
        $data['top_products'] = $this->M_penjualan->getBestSellingProducts($period, $perusahaan_ids);
        $data['selected_period'] = $period;

        $this->load_admin_view('admin/laporan/produk_terlaris', $data);
    }

    /* =========================================================
     * FINANCIAL ANALYTICS HUB - New Comprehensive Reports
     * ========================================================= */

    /**
     * Hub Laporan Keuangan — landing page navigasi semua laporan
     */
    public function financial_hub()
    {
        $data = $this->_prepare_common_data('Pusat Laporan Keuangan', 'financial_hub');
        $this->load_admin_view('admin/laporan/financial_hub', $data);
    }

    /**
     * AJAX: summary stats untuk strip di financial_hub
     */
    public function ajax_financial_summary()
    {
        header('Content-Type: application/json');
        $perusahaan_id = $this->session->userdata('perusahaan_id');
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);
        $bulan = date('Y-m');

        $omset = $this->db->query("SELECT SUM(total_tagihan) as total FROM tbl_pembayaran WHERE DATE_FORMAT(tgl_bayar,'%Y-%m')='$bulan' AND perusahaan_id IN ($ids_sql)")->row()->total ?? 0;
        $piutang = $this->db->query("SELECT SUM(total_tagihan-total_bayar) as total FROM tbl_pembayaran WHERE status_lunas='0' AND perusahaan_id IN ($ids_sql)")->row()->total ?? 0;
        $hutang = $this->db->query("SELECT SUM(total_tagihan-total_bayar) as total FROM tbl_hutang WHERE status!='lunas'")->row()->total ?? 0;

        echo json_encode(['success' => true, 'omset_bulan_ini' => (float) $omset, 'total_piutang' => (float) $piutang, 'total_hutang' => (float) $hutang]);
    }

    /**
     * Laporan Tren Keuangan 12 Bulan
     */
    public function tren_keuangan()
    {
        $data = $this->_prepare_common_data('Tren Keuangan 12 Bulan', 'tren_keuangan');
        $perusahaan_id = $data['perusahaan_id'];
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        // Ambil 12 bulan terakhir
        $tren = [];
        for ($i = 11; $i >= 0; $i--) {
            $bulan = date('Y-m', strtotime("-$i months"));
            $pend = $this->db->query("SELECT SUM(total_tagihan) as total FROM tbl_pembayaran WHERE DATE_FORMAT(tgl_bayar,'%Y-%m')='$bulan' AND perusahaan_id IN ($ids_sql)")->row()->total ?? 0;
            $peng = $this->db->query("SELECT SUM(pengeluaran_jumlah) as total FROM tbl_pengeluaran WHERE DATE_FORMAT(pengeluaran_tgl,'%Y-%m')='$bulan' AND perusahaan_id IN ($ids_sql)")->row()->total ?? 0;
            $tren[] = ['bulan' => $bulan, 'total_pendapatan' => (float) $pend, 'total_pengeluaran' => (float) $peng];
        }

        $data['tren_data'] = $tren;
        $this->load_admin_view('admin/laporan/keuangan/tren_keuangan', $data);
    }

    /**
     * Export Tren Keuangan ke Excel
     */
    public function export_tren_keuangan()
    {
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: $this->session->userdata('perusahaan_id');
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        $tren = [];
        for ($i = 11; $i >= 0; $i--) {
            $bulan = date('Y-m', strtotime("-$i months"));
            $pend = $this->db->query("SELECT SUM(total_tagihan) as total FROM tbl_pembayaran WHERE DATE_FORMAT(tgl_bayar,'%Y-%m')='$bulan' AND perusahaan_id IN ($ids_sql)")->row()->total ?? 0;
            $peng = $this->db->query("SELECT SUM(pengeluaran_jumlah) as total FROM tbl_pengeluaran WHERE DATE_FORMAT(pengeluaran_tgl,'%Y-%m')='$bulan' AND perusahaan_id IN ($ids_sql)")->row()->total ?? 0;
            $tren[] = ['bulan' => $bulan, 'pendapatan' => (float) $pend, 'pengeluaran' => (float) $peng, 'laba' => (float) ($pend - $peng)];
        }

        require_once APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $excel = new PHPExcel();
        $sheet = $excel->getActiveSheet();
        $p_info = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row();
        $sheet->setCellValue('A1', 'LAPORAN TREN KEUANGAN 12 BULAN - ' . ($p_info ? $p_info->nama_perusahaan : 'TGM'));
        $sheet->setCellValue('A2', 'Dicetak: ' . date('d/m/Y H:i'));
        $headers = ['BULAN', 'PENDAPATAN (Rp)', 'PENGELUARAN (Rp)', 'LABA BERSIH (Rp)', 'MARGIN (%)'];
        foreach ($headers as $c => $h) {
            $sheet->setCellValueByColumnAndRow($c, 4, $h);
            $sheet->getStyleByColumnAndRow($c, 4)->getFont()->setBold(true);
        }
        $row = 5;
        foreach ($tren as $r) {
            $margin = $r['pendapatan'] > 0 ? ($r['laba'] / $r['pendapatan']) * 100 : 0;
            $sheet->setCellValue('A' . $row, date('F Y', strtotime($r['bulan'] . '-01')));
            $sheet->setCellValue('B' . $row, $r['pendapatan']);
            $sheet->setCellValue('C' . $row, $r['pengeluaran']);
            $sheet->setCellValue('D' . $row, $r['laba']);
            $sheet->setCellValue('E' . $row, number_format($margin, 1) . '%');
            $sheet->getStyle('B' . $row . ':D' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $row++;
        }
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        if (ob_get_length())
            ob_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Tren_Keuangan_12Bulan.xlsx"');
        header('Cache-Control: max-age=0');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
        exit;
    }

    /**
     * Laporan Aging Piutang Detail
     */
    public function aging_piutang()
    {
        $data = $this->_prepare_common_data('Aging Piutang', 'aging_piutang');
        $perusahaan_id = $data['perusahaan_id'];
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        // Summary aging (model existing tapi extended)
        $aging_summary = $this->db->query("
            SELECT
                SUM(CASE WHEN DATEDIFF(CURDATE(), tgl_bayar) <= 15  THEN (total_tagihan-total_bayar) ELSE 0 END) as age_0_15,
                SUM(CASE WHEN DATEDIFF(CURDATE(), tgl_bayar) BETWEEN 16 AND 30  THEN (total_tagihan-total_bayar) ELSE 0 END) as age_16_30,
                SUM(CASE WHEN DATEDIFF(CURDATE(), tgl_bayar) BETWEEN 31 AND 60  THEN (total_tagihan-total_bayar) ELSE 0 END) as age_31_60,
                SUM(CASE WHEN DATEDIFF(CURDATE(), tgl_bayar) > 60  THEN (total_tagihan-total_bayar) ELSE 0 END) as age_over_60
            FROM tbl_pembayaran
            WHERE status_lunas='0' AND perusahaan_id IN ($ids_sql)
        ")->row();

        // Detail per SPK
        $aging_detail = $this->db->query("
            SELECT p.nospk, p.tgl_bayar, p.total_tagihan, p.total_bayar,
                   k.konsumen_nama, k.konsumen_nohp,
                   DATEDIFF(CURDATE(), p.tgl_bayar) as umur_hari
            FROM tbl_pembayaran p
            LEFT JOIN tbl_konsumen k ON p.konsumen_id = k.konsumen_id
            WHERE p.status_lunas='0' AND p.perusahaan_id IN ($ids_sql)
            ORDER BY umur_hari DESC
        ")->result();

        $data['aging_summary'] = $aging_summary;
        $data['aging_detail'] = $aging_detail;
        $this->load_admin_view('admin/laporan/keuangan/aging_piutang', $data);
    }

    /**
     * Export Aging Piutang ke Excel
     */
    public function export_aging_piutang()
    {
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: $this->session->userdata('perusahaan_id');
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        $detail = $this->db->query("
            SELECT p.nospk, p.tgl_bayar, p.total_tagihan, p.total_bayar,
                   (p.total_tagihan - p.total_bayar) as sisa,
                   k.konsumen_nama, k.konsumen_nohp,
                   DATEDIFF(CURDATE(), p.tgl_bayar) as umur_hari
            FROM tbl_pembayaran p
            LEFT JOIN tbl_konsumen k ON p.konsumen_id = k.konsumen_id
            WHERE p.status_lunas='0' AND p.perusahaan_id IN ($ids_sql)
            ORDER BY umur_hari DESC
        ")->result_array();

        require_once APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $excel = new PHPExcel();
        $sheet = $excel->getActiveSheet();
        $p_info = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row();
        $sheet->setCellValue('A1', 'LAPORAN AGING PIUTANG - ' . ($p_info ? $p_info->nama_perusahaan : 'TGM'));
        $sheet->setCellValue('A2', 'Dicetak: ' . date('d/m/Y H:i'));
        $headers = ['NO SPK', 'KONSUMEN', 'NO HP', 'TGL TRANSAKSI', 'TOTAL TAGIHAN', 'SUDAH BAYAR', 'SISA PIUTANG', 'UMUR (HARI)', 'KATEGORI RISIKO'];
        foreach ($headers as $c => $h) {
            $sheet->setCellValueByColumnAndRow($c, 4, $h);
            $sheet->getStyleByColumnAndRow($c, 4)->getFont()->setBold(true);
        }
        $row = 5;
        foreach ($detail as $r) {
            $umur = (int) $r['umur_hari'];
            $risiko = $umur <= 15 ? 'Aman' : ($umur <= 30 ? 'Perhatian' : ($umur <= 60 ? 'Waspada' : 'KRITIS'));
            $sheet->setCellValue('A' . $row, $r['nospk']);
            $sheet->setCellValue('B' . $row, $r['konsumen_nama']);
            $sheet->setCellValue('C' . $row, $r['konsumen_nohp']);
            $sheet->setCellValue('D' . $row, date('d/m/Y', strtotime($r['tgl_bayar'])));
            $sheet->setCellValue('E' . $row, $r['total_tagihan']);
            $sheet->setCellValue('F' . $row, $r['total_bayar']);
            $sheet->setCellValue('G' . $row, $r['sisa']);
            $sheet->setCellValue('H' . $row, $umur);
            $sheet->setCellValue('I' . $row, $risiko);
            $sheet->getStyle('E' . $row . ':G' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $row++;
        }
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        if (ob_get_length())
            ob_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Aging_Piutang_' . date('Ymd') . '.xlsx"');
        header('Cache-Control: max-age=0');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
        exit;
    }

    /**
     * Laporan Arus Kas (Cash Flow Statement)
     */
    public function arus_kas()
    {
        $data = $this->_prepare_common_data('Laporan Arus Kas', 'arus_kas');
        $perusahaan_id = $data['perusahaan_id'];
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        $mulai = $this->input->get('mulai', true) ?: date('Y-m-01');
        $selesai = $this->input->get('selesai', true) ?: date('Y-m-t');

        // Arus masuk operasional: semua pembayaran masuk
        $masuk_tunai = $this->db->query("SELECT SUM(pd.jumlah) as total FROM tbl_pembayaran_detail pd JOIN tbl_pembayaran p ON pd.pembayaran_id=p.pembayaran_id WHERE DATE(pd.created_at) BETWEEN '$mulai' AND '$selesai' AND p.perusahaan_id IN ($ids_sql) AND pd.metode='tunai'")->row()->total ?? 0;
        $masuk_transfer = $this->db->query("SELECT SUM(pd.jumlah) as total FROM tbl_pembayaran_detail pd JOIN tbl_pembayaran p ON pd.pembayaran_id=p.pembayaran_id WHERE DATE(pd.created_at) BETWEEN '$mulai' AND '$selesai' AND p.perusahaan_id IN ($ids_sql) AND pd.metode='transfer'")->row()->total ?? 0;
        $masuk_debit = $this->db->query("SELECT SUM(pd.jumlah) as total FROM tbl_pembayaran_detail pd JOIN tbl_pembayaran p ON pd.pembayaran_id=p.pembayaran_id WHERE DATE(pd.created_at) BETWEEN '$mulai' AND '$selesai' AND p.perusahaan_id IN ($ids_sql) AND pd.metode='debit'")->row()->total ?? 0;
        $masuk_ewallet = $this->db->query("SELECT SUM(pd.jumlah) as total FROM tbl_pembayaran_detail pd JOIN tbl_pembayaran p ON pd.pembayaran_id=p.pembayaran_id WHERE DATE(pd.created_at) BETWEEN '$mulai' AND '$selesai' AND p.perusahaan_id IN ($ids_sql) AND pd.metode='ewallet'")->row()->total ?? 0;

        // Arus keluar: pengeluaran
        $keluar_tunai = $this->db->query("SELECT SUM(pengeluaran_jumlah) as total FROM tbl_pengeluaran WHERE pengeluaran_tgl BETWEEN '$mulai' AND '$selesai' AND perusahaan_id IN ($ids_sql) AND (metode_bayar='tunai' OR metode_bayar IS NULL)")->row()->total ?? 0;
        $keluar_transfer = $this->db->query("SELECT SUM(pengeluaran_jumlah) as total FROM tbl_pengeluaran WHERE pengeluaran_tgl BETWEEN '$mulai' AND '$selesai' AND perusahaan_id IN ($ids_sql) AND metode_bayar='transfer'")->row()->total ?? 0;
        $keluar_lain = $this->db->query("SELECT SUM(pengeluaran_jumlah) as total FROM tbl_pengeluaran WHERE pengeluaran_tgl BETWEEN '$mulai' AND '$selesai' AND perusahaan_id IN ($ids_sql) AND metode_bayar NOT IN ('tunai','transfer') AND metode_bayar IS NOT NULL")->row()->total ?? 0;

        // Trend harian arus kas
        $daily_trend = $this->db->query("
            SELECT d.tgl,
                COALESCE(m.total_masuk,0) as total_masuk,
                COALESCE(k.total_keluar,0) as total_keluar
            FROM (
                SELECT DISTINCT tgl_bayar as tgl FROM tbl_pembayaran WHERE tgl_bayar BETWEEN '$mulai' AND '$selesai' AND perusahaan_id IN ($ids_sql)
                UNION
                SELECT DISTINCT pengeluaran_tgl FROM tbl_pengeluaran WHERE pengeluaran_tgl BETWEEN '$mulai' AND '$selesai' AND perusahaan_id IN ($ids_sql)
            ) d
            LEFT JOIN (SELECT DATE(pd.created_at) as tgl, SUM(pd.jumlah) as total_masuk FROM tbl_pembayaran_detail pd JOIN tbl_pembayaran p ON pd.pembayaran_id=p.pembayaran_id WHERE p.perusahaan_id IN ($ids_sql) GROUP BY DATE(pd.created_at)) m ON d.tgl=m.tgl
            LEFT JOIN (SELECT pengeluaran_tgl as tgl, SUM(pengeluaran_jumlah) as total_keluar FROM tbl_pengeluaran WHERE perusahaan_id IN ($ids_sql) GROUP BY pengeluaran_tgl) k ON d.tgl=k.tgl
            ORDER BY d.tgl ASC
        ")->result_array();

        $total_masuk = (float) $masuk_tunai + (float) $masuk_transfer + (float) $masuk_debit + (float) $masuk_ewallet;
        $total_keluar = (float) $keluar_tunai + (float) $keluar_transfer + (float) $keluar_lain;

        $data = array_merge($data, [
            'mulai' => $mulai,
            'selesai' => $selesai,
            'masuk_tunai' => (float) $masuk_tunai,
            'masuk_transfer' => (float) $masuk_transfer,
            'masuk_debit' => (float) $masuk_debit,
            'masuk_ewallet' => (float) $masuk_ewallet,
            'keluar_tunai' => (float) $keluar_tunai,
            'keluar_transfer' => (float) $keluar_transfer,
            'keluar_lain' => (float) $keluar_lain,
            'total_masuk' => $total_masuk,
            'total_keluar' => $total_keluar,
            'net_arus_kas' => $total_masuk - $total_keluar,
            'daily_trend' => $daily_trend,
        ]);
        $this->load_admin_view('admin/laporan/keuangan/arus_kas', $data);
    }

    /**
     * Laporan HPP & Margin per Produk
     */
    public function hpp_margin()
    {
        $data = $this->_prepare_common_data('Analisis HPP & Margin Produk', 'hpp_margin');
        $perusahaan_id = $data['perusahaan_id'];
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);
        $bulan = $this->input->get('bulan', true) ?: date('Y-m');

        $hpp_data = $this->db->query("
            SELECT
                pr.product_nama,
                SUM(pd.qty) as total_qty,
                SUM(pd.qty * hp.harga_1) as total_pendapatan,
                SUM(pd.qty * hp.hpp) as total_hpp,
                SUM(pd.qty * (hp.harga_1 - hp.hpp)) as total_margin,
                AVG((hp.harga_1 - hp.hpp) / NULLIF(hp.harga_1,0) * 100) as avg_margin_pct
            FROM tbl_pemesanan_detail pd
            JOIN tbl_harga_product hp ON pd.harga_id = hp.harga_id
            JOIN tbl_product pr ON hp.product_id = pr.product_id
            JOIN tbl_pembayaran p ON pd.nospk = p.nospk
            WHERE p.perusahaan_id IN ($ids_sql)
              AND DATE_FORMAT(p.tgl_bayar,'%Y-%m') = " . $this->db->escape($bulan) . "
            GROUP BY pr.product_id
            ORDER BY total_margin DESC
        ")->result();

        $data['hpp_data'] = $hpp_data;
        $data['bulan'] = $bulan;
        $this->load_admin_view('admin/laporan/keuangan/hpp_margin', $data);
    }


    public function rekap_spk()
    {
        $data = $this->_prepare_common_data('Rekapitulasi Transaksi SPK', 'rekap_spk');

        // Ambil filter tanggal dari input (opsional, default 30 hari terakhir)
        $start_date = $this->input->get('start_date', true) ?: date('Y-m-d', strtotime('-30 days'));
        $end_date = $this->input->get('end_date', true) ?: date('Y-m-d');

        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;

        $this->load_admin_view('admin/laporan/rekap_spk', $data);
    }

    public function ajax_rekap_spk()
    {
        header('Content-Type: application/json');

        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        // Datatables variables
        $draw = intval($this->input->post('draw'));
        $start = intval($this->input->post('start'));
        $length = intval($this->input->post('length'));
        $search = $this->input->post('search');
        $searchValue = isset($search['value']) ? $search['value'] : '';

        // Ordering
        $order = $this->input->post('order');
        $colIdx = isset($order[0]['column']) ? intval($order[0]['column']) : 1;
        $colDir = isset($order[0]['dir']) ? $order[0]['dir'] : 'desc';

        $columns = [
            0 => 's.id',
            1 => 's.spk_time',
            2 => 's.nospk',
            3 => 'k.konsumen_nama',
            4 => 'pr.status_produksi',
            5 => 'p.total_tagihan',
            6 => 'p.total_bayar',
            7 => '(p.total_tagihan - p.total_bayar)'
        ];

        $orderBy = isset($columns[$colIdx]) ? $columns[$colIdx] : 's.id';

        // Base Query
        $this->db->from('tbl_spk s')
            ->join('tbl_konsumen k', 's.konsumen_id = k.konsumen_id', 'left')
            ->join('tbl_produksi pr', 's.nospk = pr.nospk', 'left')
            ->join('tbl_pembayaran p', 's.nospk = p.nospk', 'left')
            ->where('DATE(s.spk_time) >=', $start_date)
            ->where('DATE(s.spk_time) <=', $end_date);

        // Get total records before filtering
        $totalRecords = $this->db->count_all_results('', false);

        // Applying search filtering
        if (!empty($searchValue)) {
            $this->db->group_start()
                ->like('s.nospk', $searchValue)
                ->or_like('k.konsumen_nama', $searchValue)
                ->group_end();
        }

        // Get total records after filtering
        $totalRecordwithFilter = $this->db->count_all_results('', false);

        // Group, Order, and Limit
        $this->db->select('s.nospk, s.spk_time, k.konsumen_nama, pr.status_produksi, p.total_tagihan, p.total_bayar');
        $this->db->group_by('s.nospk');
        $this->db->order_by($orderBy, $colDir);
        if ($length != -1) {
            $this->db->limit($length, $start);
        }

        $records = $this->db->get()->result();

        $data = array();
        $no = $start + 1;
        foreach ($records as $record) {
            $data[] = array(
                "no" => $no++,
                "spk_time" => $record->spk_time,
                "nospk" => $record->nospk,
                "konsumen_nama" => $record->konsumen_nama,
                "status_produksi" => $record->status_produksi,
                "total_tagihan" => $record->total_tagihan,
                "total_bayar" => $record->total_bayar
            );
        }

        $response = array(
            "draw" => $draw,
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        );

        echo json_encode($response);
    }

    /**
     * AJAX: Detail SPK untuk modal Rekapitulasi SPK
     */
    public function ajax_detail_spk()
    {
        header('Content-Type: application/json');
        $nospk = $this->input->post('nospk', true);

        if (empty($nospk)) {
            echo json_encode(['success' => false, 'message' => 'No SPK tidak valid']);
            return;
        }

        // Ambil header SPK + info konsumen + kasir + perusahaan
        $spk = $this->db->query("
            SELECT
                s.nospk, s.spk_time,
                k.konsumen_nama, k.konsumen_email, k.konsumen_nohp,
                per.nama_perusahaan,
                p.tgl_bayar, p.jam_bayar, p.total_tagihan, p.total_bayar, p.status_lunas,
                u.username as kasir_nama,
                pr.status_produksi,
                mp.metode as metode_bayar
            FROM tbl_spk s
            LEFT JOIN tbl_konsumen k ON s.konsumen_id = k.konsumen_id
            LEFT JOIN tbl_pembayaran p ON s.nospk = p.nospk
            LEFT JOIN tbl_perusahaan per ON p.perusahaan_id = per.id_perusahaan
            LEFT JOIN users u ON p.kasir_id = u.user_id
            LEFT JOIN tbl_produksi pr ON s.nospk = pr.nospk
            LEFT JOIN (SELECT pembayaran_id, MIN(metode) as metode FROM tbl_pembayaran_detail GROUP BY pembayaran_id) mp ON p.pembayaran_id = mp.pembayaran_id
            WHERE s.nospk = ?
            LIMIT 1
        ", [$nospk])->row();

        if (!$spk) {
            echo json_encode(['success' => false, 'message' => 'Data SPK tidak ditemukan']);
            return;
        }

        // Ambil detail item pesanan (dengan fallback harga dari tbl_harga_product jika snapshot NULL)
        $items = $this->db->query("
            SELECT
                pr.product_nama,
                pd.qty,
                pd.snapshot_detail_product as satuan,
                COALESCE(pd.snapshot_harga, hp.harga_1, 0) as harga,
                COALESCE(pd.snapshot_subtotal, pd.qty * COALESCE(pd.snapshot_harga, hp.harga_1, 0), 0) as subtotal
            FROM tbl_pemesanan_detail pd
            LEFT JOIN tbl_product pr ON pd.product_id = pr.product_id
            LEFT JOIN tbl_harga_product hp ON pd.harga_id = hp.harga_id
            WHERE pd.nospk = ?
            ORDER BY pd.id ASC
        ", [$nospk])->result();

        echo json_encode([
            'success' => true,
            'spk' => $spk,
            'items' => $items,
        ]);
    }
}
