<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Marketplace extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Marketplace_model');
        $this->load->model('M_master');
        
        // AUTO MIGRATION: Register Marketplace Menu
        $cek_menu = $this->db->get_where('menu', ['link' => 'marketplace'])->row_array();
        if (!$cek_menu) {
            $max_urutan = $this->db->select_max('urutan')->get('menu')->row()->urutan;
            $this->db->insert('menu', [
                'nama_menu' => 'Marketplace',
                'link' => 'marketplace',
                'icon' => 'fas fa-store',
                'urutan' => $max_urutan + 1
            ]);
            $menu_id = $this->db->insert_id();
            
            // Give access to Kasir (3), Admin (1), Owner (17), dll
            $roles = [1, 3, 17, 21, 22];
            $access_data = [];
            foreach ($roles as $role) {
                $access_data[] = [
                    'role_id' => $role,
                    'menu_id' => $menu_id
                ];
            }
            $this->db->insert_batch('tbl_menu_access', $access_data);
        }
    }

    private function _get_common_data($title = '')
    {
        $userid = $this->session->userdata('id');
        $perusahaan_id = $this->session->userdata('perusahaan_id');
        $level = $this->session->userdata('level');

        $perusahaan = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row_array();
        $divisi = $this->db->get_where('tbl_divisi', ['divisi_id' => $level])->row_array();

        return [
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => $title ? $title . ' - TGM Core' : 'TGM Core',
            'perusahaan' => $perusahaan['nama_perusahaan'] ?? '',
            'perusahaan_id' => $perusahaan_id,
            'hakakses' => $divisi['divisi_nama'] ?? '',
            'logo' => $perusahaan['logo'] ?? '',
            'level' => $this->db->get_where('users', ['user_id' => $userid])->row_array(),
            'submenu' => $this->M_master->getmenu($this->uri->segment(1))->result(),
            'companies' => $this->db->get('tbl_perusahaan')->result(),
        ];
    }

    public function index()
    {
        $data = $this->_get_common_data('Transaksi Marketplace');
        
        $perusahaan_id = $this->session->userdata('level') == 1 || $this->session->userdata('level') == 17 ? null : $this->session->userdata('perusahaan_id');
        
        $marketplace_id = $this->input->get('marketplace_id');
        $status = $this->input->get('status');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');
        
        if (empty($date_from)) $date_from = date('Y-m-01');
        if (empty($date_to)) $date_to = date('Y-m-d');
        
        $data['marketplaces'] = $this->db->get('tbl_marketplace')->result();
        $data['transactions'] = $this->Marketplace_model->get_transaksi_marketplace($perusahaan_id, $marketplace_id, $status, $date_from, $date_to)->result();
        
        $data['selected_market'] = $marketplace_id;
        $data['selected_status'] = $status;
        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;

        $this->load_admin_view('admin/marketplace/index', $data);
    }
    
    public function detail($nospk = null)
    {
        if (!$nospk) {
            redirect('marketplace');
        }
        
        $data = $this->_get_common_data('Detail Transaksi Marketplace');
        $data['detail'] = $this->Marketplace_model->get_detail_transaksi($nospk);
        
        if (!$data['detail']) {
            $this->session->set_flashdata('error', 'Transaksi tidak ditemukan!');
            redirect('marketplace');
        }
        
        // Items
        $this->db->select('pd.*, hp.harga_1, pr.product_nama');
        $this->db->from('tbl_pemesanan_detail pd');
        $this->db->join('tbl_harga_product hp', 'pd.harga_id = hp.harga_id', 'left');
        $this->db->join('tbl_product pr', 'pd.product_id = pr.product_id', 'left');
        $this->db->where('pd.nospk', $nospk);
        $data['items'] = $this->db->get()->result();
        $this->load_admin_view('admin/marketplace/detail', $data);
    }

    public function dashboard()
    {
        $perusahaan_id = $this->session->userdata('perusahaan_id');
        
        $data['metrics'] = $this->Marketplace_model->get_dashboard_metrics($perusahaan_id);
        $data['platforms'] = $this->Marketplace_model->get_platforms();
        
        $this->load_admin_view('admin/marketplace/dashboard', $data);
    }

    public function update_platform()
    {
        if ($this->input->is_ajax_request()) {
            $id = $this->input->post('id');
            $field = $this->input->post('field');
            $value = $this->input->post('value');
            
            $allowed_fields = ['is_active', 'url_toko', 'biaya_admin_persen'];
            
            if (in_array($field, $allowed_fields)) {
                $this->Marketplace_model->update_platform($id, [$field => $value]);
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid field']);
            }
        }
    }

    public function tracking_batch()
    {
        $perusahaan_id = $this->session->userdata('perusahaan_id');
        
        $this->db->select('p.*, pm.marketplace_id, m.marketplace_nama, pm.resi_marketplace, k.konsumen_nama, exp.nama_pengiriman');
        $this->db->from('tbl_produksi p');
        $this->db->join('tbl_pemesanan pm', 'p.nospk = pm.nospk', 'inner');
        $this->db->join('tbl_marketplace m', 'pm.marketplace_id = m.marketplace_id', 'left');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->join('tbl_pengiriman exp', 'pm.id_pengiriman = exp.id_pengiriman', 'left');
        $this->db->where('pm.id_jenis_transaksi', 2); // 2 = Marketplace
        $this->db->where_not_in('p.status_produksi', ['done', 'on_costumer']);
        if ($perusahaan_id) {
            $this->db->where('p.perusahaan_id', $perusahaan_id);
        }
        $this->db->group_by('p.nospk');
        $this->db->order_by('p.dateline_tgl', 'ASC');
        $this->db->order_by('p.dateline_jam', 'ASC');
        $produksi = $this->db->get()->result();

        // Group by dateline_tgl
        $batches = [];
        foreach ($produksi as $row) {
            $tgl = $row->dateline_tgl ?: '0000-00-00';
            if (!isset($batches[$tgl])) {
                $batches[$tgl] = [];
            }
            $batches[$tgl][] = $row;
        }
        
        $data['batches'] = $batches;
        
        $this->load_admin_view('admin/marketplace/tracking_batch', $data);
    }
    
    public function update_resi()
    {
        $nospk = $this->input->post('nospk', true);
        $resi = $this->input->post('resi_marketplace', true);
        
        if ($this->Marketplace_model->update_resi($nospk, $resi)) {
            $this->session->set_flashdata('success', 'Resi berhasil diupdate!');
        } else {
            $this->session->set_flashdata('error', 'Gagal update resi.');
        }
        
        redirect('marketplace/detail/' . $nospk);
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
    
    public function laporan_marketplace()
    {
        $data = $this->_get_common_data('Laporan Keuangan Marketplace');
        $perusahaan_id = $data['perusahaan_id'];
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        $bulan_ini = $this->input->get('bulan', true) ?: date('Y-m');
        $start_date = $bulan_ini . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));
        $market_id = $this->input->get('marketplace_id', true);
        $market_filter = $market_id ? 'AND pm.marketplace_id = ' . (int) $market_id : '';

        // 1. LIST TRANSAKSI
        $pemasukan_list = $this->db->query("
            SELECT
                DATE(pm.created_at) AS tanggal,
                TIME(pm.created_at) AS jam,
                pm.nospk,
                COALESCE(k.konsumen_nama, pm.resi_marketplace) AS konsumen,
                k.konsumen_nohp AS telepon,
                pm.resi_marketplace AS resi,
                COALESCE(mk.marketplace_nama, 'Lainnya') AS platform,
                pm.status_pembayaran,
                COALESCE(SUM(pp.snapshot_subtotal), 0) AS total_tagihan,
                COALESCE(SUM(pp.snapshot_subtotal), 0) AS total_bayar,
                COALESCE(mk.biaya_admin_persen, 0) AS biaya_admin_persen,
                (COALESCE(SUM(pp.snapshot_subtotal), 0) - (COALESCE(SUM(pp.snapshot_subtotal), 0) * COALESCE(mk.biaya_admin_persen, 0) / 100)) AS net_revenue,
                0 AS sisa_piutang,
                '-' AS kasir,
                'Lunas (Marketplace)' AS status_transaksi
            FROM tbl_pemesanan pm
            LEFT JOIN tbl_konsumen k ON pm.konsumen_id = k.konsumen_id
            LEFT JOIN tbl_marketplace mk ON pm.marketplace_id = mk.marketplace_id
            LEFT JOIN tbl_pembayaran_produk pp ON pm.nospk = pp.nospk
            WHERE pm.id_jenis_transaksi = 2
              AND pm.status_pembayaran = 'paid'
              AND (DATE(pm.created_at) BETWEEN ? AND ?)
              AND pm.perusahaan_id IN ($ids_sql)
              $market_filter
            GROUP BY pm.nospk
            ORDER BY pm.created_at DESC
        ", [$start_date, $end_date])->result_array();

        // 2. BREAKDOWN PER PLATFORM
        $by_platform = $this->db->query("
            SELECT
                COALESCE(mk.marketplace_nama, 'Lainnya') AS platform,
                COUNT(pm.nospk) AS jumlah_transaksi,
                COALESCE(SUM(pp.snapshot_subtotal), 0) AS total_omset,
                COALESCE(SUM(pp.snapshot_subtotal), 0) AS total_terbayar,
                COALESCE(mk.biaya_admin_persen, 0) AS biaya_admin_persen,
                (COALESCE(SUM(pp.snapshot_subtotal), 0) - (COALESCE(SUM(pp.snapshot_subtotal), 0) * COALESCE(mk.biaya_admin_persen, 0) / 100)) AS total_net,
                0 AS total_piutang
            FROM tbl_pemesanan pm
            LEFT JOIN tbl_marketplace mk ON pm.marketplace_id = mk.marketplace_id
            LEFT JOIN tbl_pembayaran_produk pp ON pm.nospk = pp.nospk
            WHERE pm.id_jenis_transaksi = 2
              AND pm.status_pembayaran = 'paid'
              AND (DATE(pm.created_at) BETWEEN ? AND ?)
              AND pm.perusahaan_id IN ($ids_sql)
            GROUP BY pm.marketplace_id
            ORDER BY total_omset DESC
        ", [$start_date, $end_date])->result_array();

        // 3. TRANSAKSI PENDING
        $pending_list = $this->db->query("
            SELECT
                DATE(pm.created_at) AS tanggal,
                pm.nospk,
                COALESCE(k.konsumen_nama, pm.resi_marketplace) AS konsumen,
                pm.resi_marketplace AS resi,
                COALESCE(mk.marketplace_nama, 'Lainnya') AS platform,
                COALESCE(SUM(pp.snapshot_subtotal), 0) AS total_estimasi
            FROM tbl_pemesanan pm
            LEFT JOIN tbl_konsumen k ON pm.konsumen_id = k.konsumen_id
            LEFT JOIN tbl_marketplace mk ON pm.marketplace_id = mk.marketplace_id
            LEFT JOIN tbl_pembayaran_produk pp ON pm.nospk = pp.nospk
            WHERE pm.id_jenis_transaksi = 2
              AND pm.status_pembayaran != 'paid'
              AND (DATE(pm.created_at) BETWEEN ? AND ?)
              AND pm.perusahaan_id IN ($ids_sql)
              $market_filter
            GROUP BY pm.nospk
            ORDER BY pm.created_at DESC
        ", [$start_date, $end_date])->result_array();

        $all_marketplace = $this->db->get('tbl_marketplace')->result();

        $total_omset = 0;
        foreach ($pemasukan_list as $pl) {
            $total_omset += $pl['total_tagihan'];
        }
        $total_pending = 0;
        foreach ($pending_list as $p) {
            $total_pending += $p['total_estimasi'];
        }

        $summary = ['marketplace' => $total_omset];

        $data = array_merge($data, [
            'bulan_ini' => $bulan_ini,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'market_id_filter' => $market_id,
            'all_marketplace' => $all_marketplace,
            'pemasukan_list' => $pemasukan_list,
            'by_platform' => $by_platform,
            'pending_list' => $pending_list,
            'summary' => $summary,
            'breakdown_transfer' => [],
            'stat_keuangan' => [
                'total_transaksi' => count($pemasukan_list),
                'omset_penjualan' => $total_omset,
                'total_terbayar' => $total_omset,
                'total_piutang' => 0,
                'total_uang_masuk' => $total_omset,
                'total_pending' => $total_pending,
                'total_pending_trx' => count($pending_list),
            ],
        ]);

        $this->load_admin_view('admin/marketplace/laporan_marketplace', $data);
    }

    public function laporan_marketplace_harian()
    {
        $data = $this->_get_common_data('Laporan Keuangan Marketplace Harian');
        $perusahaan_id = $data['perusahaan_id'];
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        $hariini = $this->input->get('tanggal', true) ?: date('Y-m-d');
        $market_id = $this->input->get('marketplace_id', true);
        $market_filter = $market_id ? 'AND pm.marketplace_id = ' . (int) $market_id : '';

        $pemasukan_list = $this->db->query("
            SELECT
                DATE(pm.created_at) AS tanggal,
                TIME(pm.created_at) AS jam,
                pm.nospk,
                COALESCE(k.konsumen_nama, pm.resi_marketplace) AS konsumen,
                k.konsumen_nohp AS telepon,
                pm.resi_marketplace AS resi,
                COALESCE(mk.marketplace_nama, 'Lainnya') AS platform,
                pm.status_pembayaran,
                COALESCE(SUM(pp.snapshot_subtotal), 0) AS total_tagihan,
                COALESCE(SUM(pp.snapshot_subtotal), 0) AS total_bayar,
                0 AS sisa_piutang,
                '-' AS kasir,
                'Lunas (Marketplace)' AS status_transaksi
            FROM tbl_pemesanan pm
            LEFT JOIN tbl_konsumen k ON pm.konsumen_id = k.konsumen_id
            LEFT JOIN tbl_marketplace mk ON pm.marketplace_id = mk.marketplace_id
            LEFT JOIN tbl_pembayaran_produk pp ON pm.nospk = pp.nospk
            WHERE pm.id_jenis_transaksi = 2
              AND pm.status_pembayaran = 'paid'
              AND DATE(pm.created_at) = ?
              AND pm.perusahaan_id IN ($ids_sql)
              $market_filter
            GROUP BY pm.nospk
            ORDER BY pm.created_at DESC
        ", [$hariini])->result_array();

        $by_platform = $this->db->query("
            SELECT
                COALESCE(mk.marketplace_nama, 'Lainnya') AS platform,
                COUNT(pm.nospk) AS jumlah_transaksi,
                COALESCE(SUM(pp.snapshot_subtotal), 0) AS total_omset,
                COALESCE(SUM(pp.snapshot_subtotal), 0) AS total_terbayar,
                0 AS total_piutang
            FROM tbl_pemesanan pm
            LEFT JOIN tbl_marketplace mk ON pm.marketplace_id = mk.marketplace_id
            LEFT JOIN tbl_pembayaran_produk pp ON pm.nospk = pp.nospk
            WHERE pm.id_jenis_transaksi = 2
              AND pm.status_pembayaran = 'paid'
              AND DATE(pm.created_at) = ?
              AND pm.perusahaan_id IN ($ids_sql)
            GROUP BY pm.marketplace_id
            ORDER BY total_omset DESC
        ", [$hariini])->result_array();

        $pending_list = $this->db->query("
            SELECT
                DATE(pm.created_at) AS tanggal,
                pm.nospk,
                COALESCE(k.konsumen_nama, pm.resi_marketplace) AS konsumen,
                pm.resi_marketplace AS resi,
                COALESCE(mk.marketplace_nama, 'Lainnya') AS platform,
                COALESCE(SUM(pp.snapshot_subtotal), 0) AS total_estimasi
            FROM tbl_pemesanan pm
            LEFT JOIN tbl_konsumen k ON pm.konsumen_id = k.konsumen_id
            LEFT JOIN tbl_marketplace mk ON pm.marketplace_id = mk.marketplace_id
            LEFT JOIN tbl_pembayaran_produk pp ON pm.nospk = pp.nospk
            WHERE pm.id_jenis_transaksi = 2
              AND pm.status_pembayaran != 'paid'
              AND DATE(pm.created_at) = ?
              AND pm.perusahaan_id IN ($ids_sql)
              $market_filter
            GROUP BY pm.nospk
            ORDER BY pm.created_at DESC
        ", [$hariini])->result_array();

        $all_marketplace = $this->db->get('tbl_marketplace')->result();

        $total_omset = 0;
        foreach ($pemasukan_list as $pl) {
            $total_omset += $pl['total_tagihan'];
        }
        $total_pending = 0;
        foreach ($pending_list as $p) {
            $total_pending += $p['total_estimasi'];
        }

        $summary = ['marketplace' => $total_omset];

        $data = array_merge($data, [
            'hariini' => $hariini,
            'market_id_filter' => $market_id,
            'all_marketplace' => $all_marketplace,
            'pemasukan_list' => $pemasukan_list,
            'by_platform' => $by_platform,
            'pending_list' => $pending_list,
            'summary' => $summary,
            'stat_keuangan' => [
                'total_transaksi' => count($pemasukan_list),
                'omset_penjualan' => $total_omset,
                'total_terbayar' => $total_omset,
                'total_piutang' => 0,
                'total_uang_masuk' => $total_omset,
                'total_pending' => $total_pending,
                'total_pending_trx' => count($pending_list),
            ],
        ]);

        $this->load_admin_view('admin/marketplace/laporan_marketplace_harian', $data);
    }

    public function tracking()
    {
        $spk = $this->input->get('spk', true) ?: $this->input->post('spk', true);
        
        $data['search_spk'] = $spk;
        $data['spk_found'] = false;
        
        if ($spk) {
            $this->load->model('Produksi_model');
            $produksi = $this->Produksi_model->get_produksi_by_spk($spk);
            
            // Validate if SPK exists and is a marketplace order
            if ($produksi && !empty($produksi->marketplace_id)) {
                $data['produksi'] = $produksi;
                $data['detail_produksi'] = $this->Produksi_model->get_detail_produksi_by_id($produksi->produksi_id);
                $data['spk_found'] = true;
            } else {
                $data['error_message'] = 'SPK #' . $spk . ' tidak ditemukan atau bukan transaksi dari platform Marketplace.';
            }
        }
        
        $this->load_admin_view('admin/marketplace/tracking', $data);
    }
}
