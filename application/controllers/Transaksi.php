<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_DB_query_builder $db
 * @property CI_Input $input
 * @property CI_Session $session
 * @property CI_URI $uri
 * @property M_transaksi $M_transaksi
 * @property M_penjualan $M_penjualan
 * @property M_master $M_master
 */
class Transaksi extends MY_Controller
{
    private function decode_metadata_array($metadata)
    {
        if (is_array($metadata)) {
            return $metadata;
        }

        if (empty($metadata) || !is_string($metadata)) {
            return [];
        }

        $decoded = json_decode($metadata, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function calculate_snapshot_subtotal($item, $harga_satuan, $qty_field = 'qty', $panjang_field = 'panjang', $lebar_field = 'lebar', $harga_minimal = 0)
    {
        $qty = (float) ($item->$qty_field ?? 0);
        $panjang = (float) ($item->$panjang_field ?? 0);
        $lebar = (float) ($item->$lebar_field ?? 0);
        $harga_satuan = (float) $harga_satuan;
        $satuan_id = isset($item->satuan_id) ? (int) $item->satuan_id : 0;
        $kategori_id = isset($item->kategori_id) ? (int) $item->kategori_id : 0;
        $ket_1 = isset($item->ket_1) ? (string) $item->ket_1 : '';

        // 1=Lembar, 3=Pcs, 6=Roll, 7=Box, 8=Rim, 9=Paket, 10=Set, 12=Pack, 13=Liter, 15=Galon, 17=Botol, 18=Ml, 19=Kg
        $non_area_satuan = [1, 3, 6, 7, 8, 9, 10, 12, 13, 15, 17, 18, 19];

        $subtotal = 0;

        if (in_array($satuan_id, $non_area_satuan)) {
            $subtotal = $qty * $harga_satuan;
        } elseif ($satuan_id === 11) {
            $subtotal = $qty * $panjang * $harga_satuan;
        } elseif ($kategori_id === 9 && $ket_1 === '5') {
            $subtotal = $qty * $panjang * $harga_satuan;
        } elseif ($panjang != 0 && $lebar != 0) {
            $subtotal = $qty * $panjang * $lebar * $harga_satuan;
        } elseif ($panjang != 0 && $lebar == 0) {
            $subtotal = $qty * $panjang * $harga_satuan;
        } else {
            $subtotal = $qty * $harga_satuan;
        }

        if ($harga_minimal > 0 && $subtotal < $harga_minimal) {
            return $harga_minimal;
        }

        return $subtotal;
    }

    private function build_price_snapshot_metadata($existing_metadata, $harga_satuan, $subtotal)
    {
        $metadata = $this->decode_metadata_array($existing_metadata);
        $metadata['unit_price'] = (float) $harga_satuan;
        $metadata['subtotal'] = (float) round($subtotal);
        $metadata['price_snapshot_at'] = date('Y-m-d H:i:s');

        return $metadata;
    }

    private function resolve_detail_unit_price($item)
    {
        $metadata = $this->decode_metadata_array($item->metadata ?? null);

        if (isset($metadata['unit_price']) && is_numeric($metadata['unit_price'])) {
            return (float) $metadata['unit_price'];
        }

        if (isset($item->unit_price) && is_numeric($item->unit_price)) {
            return (float) $item->unit_price;
        }

        return (float) ($item->harga_1 ?? 0);
    }

    private function resolve_detail_subtotal($item, $harga_satuan = null)
    {
        $metadata = $this->decode_metadata_array($item->metadata ?? null);

        if (isset($metadata['subtotal']) && is_numeric($metadata['subtotal'])) {
            return (float) $metadata['subtotal'];
        }

        if (isset($item->subtotal) && $item->subtotal !== null && $item->subtotal !== '' && is_numeric($item->subtotal)) {
            return (float) $item->subtotal;
        }

        if ($harga_satuan === null) {
            $harga_satuan = $this->resolve_detail_unit_price($item);
        }

        return $this->calculate_snapshot_subtotal($item, $harga_satuan, 'qty', 'panjang', 'lebar', $item->harga_minimal ?? 0);
    }

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('whatsapp');
        $this->load->model('M_transaksi');
        $this->load->model('M_penjualan');
        $this->load->model('M_master');
        $this->load->model('Penjualan_manual_model', 'manual_sales');

        // AUTO MIGRATION: Ensure all required columns exist
        if (!$this->db->field_exists('id_pengiriman', 'tbl_spk')) {
            $this->db->query("ALTER TABLE tbl_spk ADD COLUMN id_pengiriman INT(2) DEFAULT 0 AFTER resi_marketplace");
        }
        if (!$this->db->field_exists('batch_pengiriman', 'tbl_spk')) {
            $this->db->query("ALTER TABLE tbl_spk ADD COLUMN batch_pengiriman VARCHAR(50) DEFAULT NULL AFTER id_pengiriman");
        }
        if (!$this->db->field_exists('batch_pengiriman', 'tbl_pemesanan')) {
            $this->db->query("ALTER TABLE tbl_pemesanan ADD COLUMN batch_pengiriman VARCHAR(50) DEFAULT NULL AFTER id_pengiriman");
        }

        // AUTO SUBMENU: Register Papan Sortir
        $m = $this->db->get_where('menu', ['link' => 'transaksi'])->row_array();
        if ($m) {
            $cek = $this->db->get_where('submenu', ['id_menu' => $m['id_menu'], 'link_submenu' => 'papan_sortir'])->row();
            if (!$cek) {
                $this->db->insert('submenu', [
                    'id_menu' => $m['id_menu'],
                    'nama_submenu' => 'Papan Sortir (Logistik)',
                    'link_submenu' => 'papan_sortir',
                    'urutan' => 99
                ]);
            }
        }

        // AUTO SEED: tbl_pengiriman
        $cek_pengiriman = $this->db->get('tbl_pengiriman')->num_rows();
        if ($cek_pengiriman == 0) {
            $data_pengiriman = [
                ['nama_pengiriman' => 'SICEPAT', 'ket_pengiriman' => 'Ekspedisi SiCepat'],
                ['nama_pengiriman' => 'J&T EXPRESS', 'ket_pengiriman' => 'Ekspedisi J&T'],
                ['nama_pengiriman' => 'SHOPEE EXPRESS', 'ket_pengiriman' => 'Kurir Internal Shopee'],
                ['nama_pengiriman' => 'JNE', 'ket_pengiriman' => 'Ekspedisi JNE'],
                ['nama_pengiriman' => 'GRAB/GOJEK', 'ket_pengiriman' => 'Ojek Online'],
                ['nama_pengiriman' => 'LAINNYA', 'ket_pengiriman' => 'Ekspedisi Lainnya']
            ];
            $this->db->insert_batch('tbl_pengiriman', $data_pengiriman);
        }
    }

    /**
     * Helper to get common data used across most viewsb
     */
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
            // Menghapus 'menu' manual agar menggunakan $this->data['menu'] dari MY_Controller
            'submenu' => $this->M_master->getmenu($this->uri->segment(1))->result(),
            'companies' => $this->db->get('tbl_perusahaan')->result(),
        ];
    }
    public function search_konsumen()
    {
        $term = $this->input->get('q', true);

        $this->db->select('konsumen_id, konsumen_nama, konsumen_nohp, konsumen_email');
        $this->db->from('tbl_konsumen');
        $this->db->group_start();
        $this->db->like('LOWER(konsumen_nama)', strtolower($term));
        $this->db->or_like('konsumen_nohp', $term); // nomor tidak perlu strtolower
        $this->db->or_like('LOWER(konsumen_email)', strtolower($term));
        $this->db->group_end();
        // Sembunyikan data satu-kali-pakai dari Marketplace berdasarkan flag khusus di No. HP
        $this->db->where('konsumen_nohp !=', 'MARKETPLACE');

        $query = $this->db->get();
        echo json_encode($query->result());
    }

    public function get_transaksi_by_konsumen_ajax()
    {
        $konsumen_id = $this->input->post('konsumen_id', true);
        if (!$konsumen_id) {
            echo json_encode(['status' => 'error', 'message' => 'ID Konsumen tidak valid']);
            return;
        }

        $this->db->select('tbl_pemesanan.nospk, tbl_pemesanan.created_at, tbl_pemesanan.status_pembayaran, tbl_perusahaan.nama_perusahaan, COALESCE(tbl_pembayaran.total_tagihan, 0) as total_tagihan, COALESCE(tbl_pembayaran.total_bayar, 0) as total_bayar');
        $this->db->from('tbl_pemesanan');
        $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_pemesanan.perusahaan_id', 'left');
        $this->db->join('tbl_pembayaran', 'tbl_pembayaran.nospk = tbl_pemesanan.nospk', 'left');
        $this->db->where('tbl_pemesanan.konsumen_id', $konsumen_id);
        $this->db->order_by('tbl_pemesanan.created_at', 'DESC');
        
        $query = $this->db->get();
        
        if ($query) {
            $data = $query->result_array();
            foreach($data as &$row) {
                // Ambil status_produksi dari tbl_produksi
                $produksi = $this->db->select('status_produksi')->get_where('tbl_produksi', ['nospk' => $row['nospk']])->row();
                $row['status_produksi'] = $produksi ? $produksi->status_produksi : '';
                
                // Prepare formatting for JS
                if (!empty($row['created_at'])) {
                    $dt = new DateTime($row['created_at']);
                    $row['tanggal'] = $dt->format('d M Y');
                    $row['waktu'] = $dt->format('H:i');
                } else {
                    $row['tanggal'] = '-';
                    $row['waktu'] = '-';
                }
                
                $row['total_pembayaran'] = $row['total_tagihan'];
                $row['sisa_pembayaran'] = $row['total_tagihan'] - $row['total_bayar'];
                $row['status_wip'] = $row['status_produksi'];
            }
            echo json_encode(['status' => 'success', 'data' => $data]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengambil data']);
        }
    }

    public function debug_marketplace()
    {
        $res = $this->db->get('tbl_marketplace')->result_array();
        echo json_encode($res);
    }

    public function clean_marketplace_transactions()
    {
        // Only Developer/Superadmin (Level 1)
        if ($this->session->userdata('level') != 1) {
            $this->session->set_flashdata('error', 'Akses ditolak.');
            redirect('transaksi/papan_sortir');
            return;
        }

        $today = date('Y-m-d');

        // 1. Get SPK numbers for marketplace today
        $this->db->select('nospk');
        $this->db->where('metode_pesan', 2);
        $this->db->where('DATE(spk_time)', $today);
        $spks = $this->db->get('tbl_spk')->result_array();

        if (empty($spks)) {
            $this->session->set_flashdata('info', 'Tidak ada transaksi marketplace hari ini.');
            redirect('transaksi/papan_sortir');
            return;
        }

        $spk_list = array_column($spks, 'nospk');

        $this->db->trans_start();
        $this->db->where_in('nospk', $spk_list);
        $this->db->delete('tbl_pemesanan_detail');

        $this->db->where_in('nospk', $spk_list);
        $this->db->delete('tbl_pemesanan');

        $this->db->where_in('nospk', $spk_list);
        $this->db->delete('tbl_spk');

        $this->db->where_in('no_spk', $spk_list);
        $this->db->delete('tbl_temp_transaksi');

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'Gagal membersihkan data.');
        } else {
            $this->session->set_flashdata('success', 'Berhasil menghapus ' . count($spk_list) . ' transaksi marketplace hari ini.');
        }

        redirect('transaksi/papan_sortir');
    }

    public function papan_sortir()
    {
        $userid = $this->session->userdata('id');
        $perusahaan_id = $this->session->userdata('perusahaan_id');
        $today = date('Y-m-d');

        // Query Utama: Ambil pesanan Marketplace hari ini
        $this->db->select('p.*, k.konsumen_nama, m.marketplace_nama, m.marketplace_id as m_id, exp.nama_pengiriman');
        $this->db->from('tbl_pemesanan p');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->join('tbl_marketplace m', 'p.marketplace_id = m.marketplace_id', 'left');
        $this->db->join('tbl_pengiriman exp', 'p.id_pengiriman = exp.id_pengiriman', 'left');
        $this->db->where('p.id_jenis_transaksi', 2); // Marketplace
        $this->db->where('DATE(p.created_at)', $today);

        // Access Control
        if ($this->session->userdata('level') != 1 && $this->session->userdata('level') != 17) {
            $this->db->where('p.perusahaan_id', $perusahaan_id);
        }

        $orders = $this->db->get()->result();

        // Ambil detail items per order
        foreach ($orders as $o) {
            $this->db->select('pd.*, pr.product_nama');
            $this->db->from('tbl_pemesanan_detail pd');
            $this->db->join('tbl_product pr', 'pd.product_id = pr.product_id', 'left');
            $this->db->where('pd.nospk', $o->nospk);
            $o->items = $this->db->get()->result();
        }

        $data = $this->_get_common_data('Papan Sortir Pengiriman');
        $data['orders'] = $orders;
        $data['today'] = $today;

        // Grouping logic untuk tampilan kolom (Marketplace + Kurir)
        $board = [];
        foreach ($orders as $o) {
            $market = $o->marketplace_nama ?: 'LAINNYA';
            $courier = $o->nama_pengiriman ?: 'BELUM DIPILIH';
            $batch = $o->batch_pengiriman ?: 'TANPA BATCH';

            $key = $market . ' - ' . $courier;
            if (!isset($board[$key])) {
                $board[$key] = [
                    'market' => $market,
                    'courier' => $courier,
                    'm_id' => $o->m_id,
                    'batches' => []
                ];
            }

            if (!isset($board[$key]['batches'][$batch])) {
                $board[$key]['batches'][$batch] = [];
            }
            $board[$key]['batches'][$batch][] = $o;
        }
        $data['board'] = $board;

        $this->load_admin_view('admin/transaksi/papan_sortir', $data);
    }

    public function rekap_transaksi_marketplace()
    {
        $userid = $this->session->userdata('id');
        $perusahaan_id = $this->session->userdata('perusahaan_id');
        $date_from = $this->input->get('date_from', true) ?: date('Y-m-01');
        $date_to = $this->input->get('date_to', true) ?: date('Y-m-d');
        $market_id = $this->input->get('marketplace_id', true);

        // Logic Query: Ambil data dari tbl_pemesanan yang jenisnya Marketplace (2)
        $this->db->select('p.*, k.konsumen_nama, k.konsumen_alamat, m.perusahaan_id, pr.nama_perusahaan');
        $this->db->from('tbl_pemesanan p');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->join('tbl_spk m', 'p.nospk = m.nospk', 'left');
        $this->db->join('tbl_perusahaan pr', 'p.perusahaan_id = pr.id_perusahaan', 'left');
        $this->db->where('p.id_jenis_transaksi', 2); // 2 = Marketplace
        $this->db->where('DATE(p.created_at) >=', $date_from);
        $this->db->where('DATE(p.created_at) <=', $date_to);

        if (!empty($market_id)) {
            $this->db->where('p.marketplace_id', $market_id);
        }

        // Access Control
        if ($this->session->userdata('level') != 1 && $this->session->userdata('level') != 17) {
            $this->db->where('p.perusahaan_id', $perusahaan_id);
        }

        $this->db->order_by('p.created_at', 'DESC');
        $transactions = $this->db->get()->result();

        // Hitung Omzet per Transaksi
        foreach ($transactions as $t) {
            $this->db->select('SUM(pd.qty * hp.harga_1) as subtotal');
            $this->db->from('tbl_pemesanan_detail pd');
            $this->db->join('tbl_harga_product hp', 'pd.harga_id = hp.harga_id', 'left');
            $this->db->where('pd.nospk', $t->nospk);
            $t->total_omzet = $this->db->get()->row()->subtotal ?? 0;
        }

        $data = $this->_get_common_data('Rekapitulasi Penjualan Marketplace');
        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['selected_market'] = $market_id;
        $data['transactions'] = $transactions;
        $data['total_omzet_all'] = array_sum(array_column($transactions, 'total_omzet'));
        $data['total_trx'] = count($transactions);

        $this->load_admin_view('admin/transaksi/rekap_marketplace', $data);
    }

    public function search_product()
    {
        $keyword = $this->input->get('q', true);
        $nospk = $this->input->get('nospk', true);

        $metode_transaksi = null;
        if ($nospk) {
            $spk_data = $this->db->get_where('tbl_spk', ['nospk' => $nospk])->row_array();
            if ($spk_data) {
                $metode_transaksi = $spk_data['metode_pesan'];
            }
        }

        $result = $this->M_master->search_product_by_metode($keyword, $metode_transaksi);

        $grouped = [];
        foreach ($result as $row) {
            $cat_name = strtoupper($row->kategori_nama ?: 'LAIN-LAIN');
            if (!isset($grouped[$cat_name])) {
                $grouped[$cat_name] = [
                    'text' => '📁 ' . $cat_name,
                    'children' => []
                ];
            }
            $grouped[$cat_name]['children'][] = [
                'id' => $row->product_id,
                'text' => $row->product_nama
            ];
        }

        echo json_encode(array_values($grouped));
    }

    public function get_product_details_ajax()
    {
        try {
            $product_id = $this->input->get('product_id', true);
            $product = $this->M_master->getProductByIdComplete($product_id);

            if ($product) {
                $cat_view = '';
                // Clean up data for view
                $data['getproduk'] = $product;
                // SMART SCHEMA PRIORITY: Product > Subcategory > Category
                $form_schema = null;

                // Helper function logic for Priority with Validation
                $p_schema = trim($product['product_form_schema'] ?? '');
                $s_schema = trim($product['subcategory_form_schema'] ?? '');
                $c_schema = trim($product['category_form_schema'] ?? '');

                if (!empty($p_schema) && $p_schema !== '[]' && $p_schema !== 'null') {
                    $form_schema = $p_schema;
                } else if (!empty($s_schema) && $s_schema !== '[]' && $s_schema !== 'null') {
                    $form_schema = $s_schema;
                } else if (!empty($c_schema) && $c_schema !== '[]' && $c_schema !== 'null') {
                    $form_schema = $c_schema;
                }

                $data['form_schema'] = $form_schema;
                $data['subkategori_config'] = !empty($product['subkategori_config']) ? json_decode($product['subkategori_config'], true) : [];

                // Safe handling for optional/new tables
                if ($this->db->table_exists('tbl_mesin_printing')) {
                    $data['mesin'] = $this->db->where_in('id', [2, 3, 4])->get('tbl_mesin_printing')->result();
                } else {
                    $data['mesin'] = [];
                }

                if ($this->db->table_exists('tbl_mata_cutting')) {
                    $data['mata'] = $this->db->get('tbl_mata_cutting')->result();
                } else {
                    $data['mata'] = [];
                }

                if (!empty($form_schema)) {
                    // 100% Dynamic Renderer
                    $cat_view = $this->load->view('admin/transaksi/createspk/kategori_dynamic', $data, TRUE);
                } else {
                    // Fallback to General
                    $cat_view = $this->load->view('admin/transaksi/createspk/kategori_umum', $data, TRUE);
                }

                echo json_encode([
                    'status' => 'success',
                    'product' => $product,
                    'subkategori_config' => $data['subkategori_config'],
                    'cat_view' => $cat_view
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Produk tidak ditemukan']);
            }
        } catch (Exception $e) {
            log_message('error', 'Error in get_product_details_ajax: ' . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan server: ' . $e->getMessage()]);
        }
    }

    public function update_spk_logistik()
    {
        $nospk = $this->input->post('nospk', true);
        $id_pengiriman = $this->input->post('id_pengiriman', true);
        $batch_pengiriman = $this->input->post('batch_pengiriman', true);

        $data = [];
        if ($id_pengiriman !== null)
            $data['id_pengiriman'] = $id_pengiriman;
        if ($batch_pengiriman !== null)
            $data['batch_pengiriman'] = $batch_pengiriman;

        if (empty($data)) {
            echo json_encode(['status' => 'error', 'message' => 'No data to update']);
            return;
        }

        $this->db->where('nospk', $nospk);
        $update = $this->db->update('tbl_spk', $data);

        // Also sync to tbl_pemesanan if it exists (for logistics reporting consistency)
        $this->db->where('nospk', $nospk);
        $this->db->update('tbl_pemesanan', $data);

        if ($update) {
            echo json_encode(['status' => 'success', 'message' => 'Logistik berhasil diperbarui']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui database']);
        }
    }

    public function update_spk_konsumen()
    {
        $nospk = $this->input->post('nospk', true);
        $konsumen_id = $this->input->post('konsumen_id', true);
        $userId = $this->session->userdata('id');

        // Pastikan SPK milik user tersebut dan benar-benar masih draft
        $spk = $this->db->get_where('tbl_spk', ['nospk' => $nospk, 'user_id' => $userId])->row_array();
        $is_not_finished = $this->db->get_where('tbl_pemesanan', ['nospk' => $nospk])->num_rows() == 0;

        if ($spk && $is_not_finished) {
            $this->db->where('nospk', $nospk);
            if ($this->db->update('tbl_spk', ['konsumen_id' => $konsumen_id])) {
                echo json_encode(['status' => 'success', 'message' => 'Konsumen berhasil diperbarui']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui konsumen']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'SPK tidak ditemukan atau sudah diproses']);
        }
    }


    public function index()
    {
        $userid = $this->session->userdata('id');
        $user_level = $this->session->userdata('level');

        // Check if user is owner (level 17) or administrator (level 1)
        if (in_array($user_level, [1, 17])) {
            $this->calendar_view();
            return;
        }

        // Check shift status for kasir (level 3)
        if ($user_level == 3) {
            redirect_if_no_shift($userid);
        }

        $data = $this->_get_common_data('Dashboard Utama Kasir');
        $data['table'] = 'tbl_kategori';
        $data['tampilData'] = $this->M_master->getDataProduct()->result();
        $data['total_rows'] = $this->M_master->getDataProduct()->num_rows();
        $data['total_belumbayar'] = $this->M_transaksi->getDataPemesanankasir('0')->num_rows();
        $data['total_sudahbayar'] = $this->M_transaksi->getbayarsukses('1')->num_rows();
        $data['total_transaksi'] = $this->M_transaksi->get_transaksi()->row_array();
        
        $is_privileged = in_array($user_level, [1, 3, 17, 22]);
        $selected_branch = $this->input->get('branch_id', true);
        
        $data['is_privileged'] = $is_privileged;
        $data['selected_branch'] = $selected_branch;
        
        // Use target_perusahaan to apply filter correctly
        $target_perusahaan = ($is_privileged && $selected_branch && $selected_branch !== 'all') ? $selected_branch : ($is_privileged ? null : $this->session->userdata('perusahaan_id'));
        $data['transaksi_belumbayar'] = $this->M_transaksi->getDataPemesanankasir('0', $target_perusahaan)->result();

        $this->load_admin_view('admin/transaksi/transaksi_belumbayar', $data);
    }

    /**
     * View for orders from other branches that are being produced at the current branch
     */
    public function order_titipan()
    {
        $userid = $this->session->userdata('id');
        $user_level = $this->session->userdata('level');
        $perusahaan_id = $this->session->userdata('perusahaan_id');

        $data = $this->_get_common_data('Pesanan Titipan Produksi Cabang Lain');

        $page = (int) $this->input->get('page') ?: 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Get orders where at least one item is produced locally but the order belongs to another branch
        $this->db->select('p.*, k.konsumen_nama, kp.nama_perusahaan as asal_cabang, 
                          (SELECT COUNT(*) FROM tbl_pemesanan_detail WHERE nospk = p.nospk) AS jumlah_item,
                          (SELECT ROUND(SUM(CASE WHEN (pd.panjang > 0 AND pd.lebar > 0) THEN (pd.panjang * pd.lebar * pd.qty * hp.harga_1) ELSE (pd.qty * hp.harga_1) END)) 
                           FROM tbl_pemesanan_detail pd LEFT JOIN tbl_harga_product hp ON hp.harga_id = pd.harga_id WHERE pd.nospk = p.nospk) AS total_transaksi');
        $this->db->from('tbl_pemesanan p');
        $this->db->join('tbl_konsumen k', 'k.konsumen_id = p.konsumen_id');
        $this->db->join('tbl_perusahaan kp', 'kp.id_perusahaan = p.perusahaan_id');
        $this->db->join('tbl_produksi pr', 'pr.nospk = p.nospk');
        $this->db->where('pr.lokasi_cetak', $perusahaan_id);
        $this->db->where('p.perusahaan_id !=', $perusahaan_id);
        $this->db->group_by('p.nospk');
        $this->db->order_by('p.created_at', 'DESC');
        $this->db->limit($limit, $offset);

        $data['orders'] = $this->db->get()->result();

        if ($this->input->get('page')) {
            $this->load->view('admin/transaksi/order_titipan_rows', $data);
            return;
        }

        $this->load_admin_view('admin/transaksi/order_titipan', $data);
    }

    /**
     * Calendar view for owner, administrator, and kasir showing daily sales
     */
    public function calendar_view()
    {
        $user_level = $this->session->userdata('level');
        $is_owner = ($user_level == 17);
        $is_admin = ($user_level == 1);
        $is_kasir = ($user_level == 3);

        if (!$is_owner && !$is_admin && !$is_kasir) {
            $this->session->set_flashdata('error', 'Akses ditolak. Kalender Penjualan hanya dapat diakses oleh Owner, Administrator, dan Kasir.');
            redirect('dashboard');
        }

        // Get current year and month for calendar
        $year = $this->input->get('year', true) ?: date('Y');
        $month = $this->input->get('month', true) ?: date('m');
        $selected_date = $this->input->get('date', true) ?: date('Y-m-d');

        // Get calendar data for the month
        $calendar_data = $this->getSalesCalendarData($year, $month);
        $calendar_events = [];
        foreach ($calendar_data as $event) {
            $calendar_events[] = [
                'title' => 'Rp ' . number_format($event->total_penjualan) . ' (' . $event->jumlah_transaksi . ' transaksi)',
                'date' => $event->tanggal,
                'backgroundColor' => ($event->total_penjualan > 0) ? '#28a745' : '#6c757d',
                'borderColor' => ($event->total_penjualan > 0) ? '#28a745' : '#6c757d',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'total' => $event->total_penjualan,
                    'count' => $event->jumlah_transaksi
                ]
            ];
        }

        $data = $this->_get_common_data('Kalender Penjualan - Dashboard Owner/Administrator/Kasir');
        $data['user_level'] = $user_level;
        $data['is_owner'] = $is_owner;
        $data['is_admin'] = $is_admin;
        $data['is_kasir'] = $is_kasir;
        $data['selected_year'] = $year;
        $data['selected_month'] = $month;
        $data['selected_date'] = $selected_date;
        $data['calendar_events'] = $calendar_events;
        $data['sales_data'] = $this->getSalesByDate($selected_date);
        $data['sales_detail'] = $this->getSalesDetailByDate($selected_date);
        $data['total_belumbayar'] = $this->M_transaksi->getDataPemesanankasir('0')->num_rows();
        $data['total_sudahbayar'] = $this->M_transaksi->getbayarsukses('1')->num_rows();
        $data['total_transaksi'] = $this->M_transaksi->get_transaksi()->row_array();

        $this->load_admin_view('admin/transaksi/calendar', $data);
    }

    /**
     * Get sales calendar data for a specific month (paid and unpaid transactions only)
     */
    private function getSalesCalendarData($year, $month)
    {
        $perusahaan_id = $this->session->userdata('perusahaan_id');

        // Get paid transactions for the month (from tbl_pembayaran)
        $this->db->select('DATE(p.tgl_bayar) as tanggal, 
                                COUNT(*) as jumlah_transaksi, 
                                SUM(p.total_tagihan) as total_penjualan,
                                "paid" as status_type');
        $this->db->from('tbl_pembayaran p');
        $this->db->join('tbl_pemesanan pm', 'pm.nospk = p.nospk');
        $this->db->where('YEAR(p.tgl_bayar)', $year);
        $this->db->where('MONTH(p.tgl_bayar)', $month);
        $this->db->where('pm.perusahaan_id', $perusahaan_id);
        $this->db->group_by('DATE(p.tgl_bayar)');
        $paid_data = $this->db->get()->result();

        // Get unpaid transactions for the month (from tbl_pemesanan joined with tbl_pemesanan_detail)
        $this->db->select('DATE(p.created_at) as tanggal, 
                               COUNT(DISTINCT p.nospk) as jumlah_transaksi, 
                               SUM(pd.qty) as total_qty,
                               SUM(pd.qty * COALESCE(hp.harga_1, 0)) as total_penjualan,
                               "unpaid" as status_type');
        $this->db->from('tbl_pemesanan p');
        $this->db->join('tbl_pemesanan_detail pd', 'p.nospk = pd.nospk', 'left');
        $this->db->join('tbl_harga_product hp', 'pd.harga_id = hp.harga_id', 'left');
        $this->db->where('YEAR(p.created_at)', $year);
        $this->db->where('MONTH(p.created_at)', $month);
        $this->db->where('p.perusahaan_id', $perusahaan_id);
        $this->db->where('p.status_pembayaran', 'pending'); // Only unpaid transactions
        $this->db->group_by('DATE(p.created_at)');
        $unpaid_data = $this->db->get()->result();

        // Combine and merge data by date
        $combined_data = [];

        // Process paid transactions
        foreach ($paid_data as $paid) {
            $date = $paid->tanggal;
            if (!isset($combined_data[$date])) {
                $combined_data[$date] = [
                    'tanggal' => $date,
                    'paid_count' => 0,
                    'unpaid_count' => 0,
                    'paid_amount' => 0,
                    'unpaid_amount' => 0,
                    'total_amount' => 0,
                    'total_transactions' => 0
                ];
            }
            $combined_data[$date]['paid_count'] = $paid->jumlah_transaksi;
            $combined_data[$date]['paid_amount'] = $paid->total_penjualan;
            $combined_data[$date]['total_amount'] += $paid->total_penjualan;
            $combined_data[$date]['total_transactions'] += $paid->jumlah_transaksi;
        }

        // Process unpaid transactions
        foreach ($unpaid_data as $unpaid) {
            $date = $unpaid->tanggal;
            if (!isset($combined_data[$date])) {
                $combined_data[$date] = [
                    'tanggal' => $date,
                    'paid_count' => 0,
                    'unpaid_count' => 0,
                    'paid_amount' => 0,
                    'unpaid_amount' => 0,
                    'total_amount' => 0,
                    'total_transactions' => 0
                ];
            }
            $combined_data[$date]['unpaid_count'] = $unpaid->jumlah_transaksi;
            $combined_data[$date]['unpaid_amount'] = $unpaid->total_penjualan;
            $combined_data[$date]['total_amount'] += $unpaid->total_penjualan;
            $combined_data[$date]['total_transactions'] += $unpaid->jumlah_transaksi;
        }

        return array_values($combined_data);
    }

    /**
     * Get sales data for a specific date (paid and unpaid transactions only)
     */
    private function getSalesByDate($date)
    {
        $perusahaan_id = $this->session->userdata('perusahaan_id');

        // Debug logging
        log_message('debug', 'getSalesByDate called for date: ' . $date . ', perusahaan_id: ' . $perusahaan_id);

        // Get paid transactions (from tbl_pembayaran)
        $this->db->select('p.*, k.konsumen_nama, k.konsumen_nohp, u.username as kasir_name, "paid" as status_type');
        $this->db->from('tbl_pembayaran p');
        $this->db->join('tbl_pemesanan pm', 'pm.nospk = p.nospk');
        $this->db->join('tbl_konsumen k', 'k.konsumen_id = pm.konsumen_id');
        $this->db->join('users u', 'u.user_id = p.kasir_id', 'left');
        $this->db->where('DATE(p.tgl_bayar)', $date);
        $this->db->where('pm.perusahaan_id', $perusahaan_id);
        $this->db->order_by('p.tgl_bayar', 'DESC');
        $paid_transactions = $this->db->get()->result();

        log_message('debug', 'Paid transactions found: ' . count($paid_transactions));

        // Get unpaid transactions (from tbl_pemesanan where status_pembayaran = 0)
        $this->db->select('pm.*, k.konsumen_nama, k.konsumen_nohp, u.username as deskprint_name, "unpaid" as status_type');
        $this->db->from('tbl_pemesanan pm');
        $this->db->join('tbl_konsumen k', 'k.konsumen_id = pm.konsumen_id');
        $this->db->join('users u', 'u.user_id = pm.deskprint_id', 'left');
        $this->db->where('DATE(pm.created_at)', $date);
        $this->db->where('pm.perusahaan_id', $perusahaan_id);
        $this->db->where('pm.status_pembayaran', 'pending'); // Only unpaid transactions
        $this->db->order_by('pm.created_at', 'DESC');
        $unpaid_transactions = $this->db->get()->result();

        log_message('debug', 'Unpaid transactions found: ' . count($unpaid_transactions));

        return [
            'paid' => $paid_transactions,
            'unpaid' => $unpaid_transactions,
            'total_paid' => count($paid_transactions),
            'total_unpaid' => count($unpaid_transactions),
            'total_paid_amount' => array_sum(array_column($paid_transactions, 'total_tagihan')),
            'total_unpaid_amount' => array_sum(array_column($unpaid_transactions, 'total_penjualan'))
        ];
    }

    /**
     * Get detailed sales data for a specific date
     */
    private function getSalesDetailByDate($date)
    {
        $perusahaan_id = $this->session->userdata('perusahaan_id');

        // Get payment method breakdown for the date
        $this->db->select('pd.metode, COUNT(*) as jumlah, SUM(pd.jumlah) as total');
        $this->db->from('tbl_pembayaran p');
        $this->db->join('tbl_pemesanan pm', 'pm.nospk = p.nospk');
        $this->db->join('tbl_pembayaran_detail pd', 'pd.pembayaran_id = p.pembayaran_id');
        $this->db->where('DATE(p.tgl_bayar)', $date);
        $this->db->where('pm.perusahaan_id', $perusahaan_id);
        $this->db->group_by('pd.metode');
        $this->db->order_by('total', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * AJAX method to get calendar events for a specific month
     */
    public function get_calendar_events()
    {
        $user_level = $this->session->userdata('level');
        $is_owner = ($user_level == 17);
        $is_admin = ($user_level == 1);
        $is_kasir = ($user_level == 3);

        // Allow owner (17), administrator (1), and kasir (3) to access
        if (!$is_owner && !$is_admin && !$is_kasir) {
            $this->output->set_status_header(403);
            echo json_encode(['error' => 'Access denied - Only owner, administrator, and kasir can access this data']);
            return;
        }

        $year = $this->input->get('year', true) ?: date('Y');
        $month = $this->input->get('month', true) ?: date('m');

        $calendar_data = $this->getSalesCalendarData($year, $month);
        $events = [];

        foreach ($calendar_data as $event) {
            // Determine color based on transaction status
            $backgroundColor = '#6c757d'; // Default gray
            $borderColor = '#6c757d';

            if ($event->paid_count > 0 && $event->unpaid_count > 0) {
                $backgroundColor = '#fd7e14'; // Orange for mixed (paid + unpaid)
                $borderColor = '#fd7e14';
            } elseif ($event->paid_count > 0) {
                $backgroundColor = '#28a745'; // Success green for paid only
                $borderColor = '#28a745';
            } elseif ($event->unpaid_count > 0) {
                $backgroundColor = '#dc3545'; // Danger red for unpaid only
                $borderColor = '#dc3545';
            }

            $events[] = [
                'title' => 'Rp ' . number_format($event->total_amount) . ' (' . $event->total_transactions . ' transaksi)',
                'date' => $event->tanggal,
                'backgroundColor' => $backgroundColor,
                'borderColor' => $borderColor,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'total' => $event->total_amount,
                    'count' => $event->total_transactions,
                    'paid_count' => $event->paid_count,
                    'unpaid_count' => $event->unpaid_count,
                    'paid_amount' => $event->paid_amount,
                    'unpaid_amount' => $event->unpaid_amount
                ]
            ];
        }

        $this->output->set_content_type('application/json');
        echo json_encode(['success' => true, 'data' => $events]);
    }

    /**
     * AJAX method to get sales data for a specific date
     */
    public function get_sales_by_date()
    {
        $user_level = $this->session->userdata('level');
        $is_owner = ($user_level == 17);
        $is_admin = ($user_level == 1);
        $is_kasir = ($user_level == 3);

        // Allow owner (17), administrator (1), and kasir (3) to access
        if (!$is_owner && !$is_admin && !$is_kasir) {
            $this->output->set_status_header(403);
            echo json_encode(['error' => 'Access denied - Only owner, administrator, and kasir can access this data']);
            return;
        }

        $date = $this->input->get('date', true) ?: date('Y-m-d');

        // Debug: Check if there are any unpaid transactions for this date
        $perusahaan_id = $this->session->userdata('perusahaan_id');
        $this->db->select('COUNT(*) as total_unpaid');
        $this->db->from('tbl_pemesanan');
        $this->db->where('DATE(created_at)', $date);
        $this->db->where('perusahaan_id', $perusahaan_id);
        $this->db->where('status_pembayaran', 'pending');
        $total_unpaid_check = $this->db->get()->row()->total_unpaid;

        log_message('debug', 'Total unpaid transactions for date ' . $date . ': ' . $total_unpaid_check);

        $sales_data = $this->getSalesByDate($date);
        $sales_detail = $this->getSalesDetailByDate($date);

        $this->output->set_content_type('application/json');
        echo json_encode([
            'success' => true,
            'data' => $sales_data,
            'detail' => $sales_detail,
            'debug' => [
                'date' => $date,
                'perusahaan_id' => $perusahaan_id,
                'total_unpaid_check' => $total_unpaid_check
            ]
        ]);
    }

    public function load_transaksi_editspk($nospk)
    {
        $data['transaksi'] = $this->M_transaksi->tampilTmp_editspk($nospk)->result();
        // print_r($data['transaksi']);

        $this->load->view('admin/transaksi/tmp_body_edit_spk', $data);
    }

    public function load_transaksi($nospk)
    {
        $data['transaksi'] = $this->M_transaksi->tampilTmp($nospk)->result();
        // print_r($data['transaksi']);

        $this->load->view('admin/transaksi/tmp_body', $data);
    }

    public function load_pemesanan($nospk)
    {
        $data['pemesanan'] = $this->M_transaksi->detail_pemesanan($nospk)->result();
        // print_r($data['pemesanan']);

        $this->load->view('admin/transaksi/pemesanan_body', $data);
    }


    public function loadsummary($nospk)
    {
        $this->_get_summary_json($nospk, true);
    }

    public function loadsummary_editspk($nospk)
    {
        $this->_get_summary_json($nospk, false);
    }

    private function _get_summary_json($nospk, $is_temp = true)
    {
        $table = $is_temp ? 'tbl_temp_transaksi' : 'tbl_pemesanan_detail';
        $where_field = $is_temp ? 'no_spk' : 'nospk';
        $qty_field = $is_temp ? 'temp_qty' : 'qty';
        $panjang_field = $is_temp ? 'temp_panjang' : 'panjang';
        $lebar_field = $is_temp ? 'temp_lebar' : 'lebar';

        $this->db->select("t.$qty_field as qty, t.$panjang_field as panjang, t.$lebar_field as lebar, t.metadata, h.harga_1, h.harga_minimal, h.ket_1, p.kategori_id, p.satuan_id");
        $this->db->from("$table t");
        $this->db->join('tbl_harga_product h', 't.harga_id = h.harga_id');
        $this->db->join('tbl_product p', 'p.product_id = t.product_id', 'left');
        $this->db->where("t.$where_field", $nospk);
        $query = $this->db->get()->result();

        $total = 0;
        $jml_item = count($query);

        foreach ($query as $row) {
            $harga_satuan = $this->resolve_detail_unit_price($row);
            $subtotal = $this->resolve_detail_subtotal($row, $harga_satuan);
            $total += $subtotal;
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'total_harga' => $total,
                'jml_item' => $jml_item
            ]));
    }



    public function get_sub_category()
    {
        $category_id = $this->input->post('id', true);
        $data = $this->M_transaksi->get_sub_category($category_id)->result();

        echo json_encode($data);
    }

    private function _resolve_dynamic_pricing_keys($product_id, &$inputs)
    {
        $product_data = $this->M_master->getProductByIdComplete($product_id);
        if (!$product_data) return;

        $raw_schema = $product_data['product_form_schema'] ?? $product_data['subcategory_form_schema'] ?? $product_data['category_form_schema'] ?? '';
        if (empty($raw_schema)) return;

        $schema = json_decode($raw_schema, true);
        if (!is_array($schema)) return;

        $key_dim1 = 'ket_1';
        $key_dim2 = 'jenisprint_id';
        $unmapped_names = [];

        foreach ($schema as $field) {
            if (isset($field['options']) && !empty($field['options']) && in_array($field['type'], ['select', 'radio'])) {
                $name = $field['name'] ?? '';
                $labelLower = strtolower($field['label'] ?? '');

                if ($name === 'jenisprint_id' || strpos($labelLower, 'mesin') !== false || strpos($labelLower, 'metode') !== false || strpos($labelLower, 'jenis print') !== false) {
                    $key_dim2 = $name;
                } elseif ($name === 'side_id') {
                    // ignore for pricing (ket_1 and jenisprint_id are the only ones used)
                } elseif ($name === 'ket_1' || strpos($labelLower, 'varian') !== false) {
                    $key_dim1 = $name;
                } else {
                    $unmapped_names[] = $name;
                }
            }
        }

        if ($key_dim1 === 'ket_1' && !empty($unmapped_names)) {
            $key_dim1 = array_shift($unmapped_names);
        }
        if ($key_dim2 === 'jenisprint_id' && !empty($unmapped_names)) {
            $key_dim2 = array_shift($unmapped_names);
        }
        
        // Prevent assigning the same POST field to both dimensions
        if ($key_dim1 === $key_dim2) {
            // If they are the same, it means the default key (e.g. ket_1) was explicitly matched to the other dimension (e.g. jenisprint_id)
            // We clear dim1 to avoid duplication
            $key_dim1 = null;
        }

        // Reset inputs to avoid dirty duplicate values from action_add_temp defaults
        $inputs['ket_1'] = 0;
        $inputs['jenisprint_id'] = 0;

        if ($key_dim1 && $this->input->post($key_dim1) !== null) {
            $inputs['ket_1'] = $this->input->post($key_dim1, true);
        }
        if ($key_dim2 && $this->input->post($key_dim2) !== null) {
            $inputs['jenisprint_id'] = $this->input->post($key_dim2, true);
        }
    }
    /**
     * Estimate price without inserting - for live preview in SPK form
     */
    public function estimate_price()
    {
        $inputs = [
            'product' => $this->input->post('product_id', true),
            'qty' => $this->input->post('qty', true),
            'panjang' => $this->input->post('panjang', true) ?? 0,
            'lebar' => $this->input->post('lebar', true) ?? 0,
            'kategori' => $this->input->post('kategori_id', true),
            'ket_1' => $this->input->post('ket_1', true) ?? 0,
            'jenisprint_id' => $this->input->post('jenisprint_id', true) ?? 0,
        ];

        if (empty($inputs['product']) || empty($inputs['qty'])) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'empty']));
        }

        // Resolve dynamic pricing keys
        $this->_resolve_dynamic_pricing_keys($inputs['product'], $inputs);

        $product_info = $this->db->select('satuan_id')->get_where('tbl_product', ['product_id' => $inputs['product']])->row();
        $satuan_id = $product_info ? $product_info->satuan_id : 0;

        // Calculate Pricing Quantity based on Category (same logic as action_add_temp)
        $pricing_qty = floatval($inputs['qty']);
        if (in_array($inputs['kategori'], [2, 3, 5, 6, 8, 9])) {
            $panjang = floatval($inputs['panjang'] ?? 0);
            $lebar = floatval($inputs['lebar'] ?? 0);

            if (in_array($inputs['kategori'], [3, 6]) && ($panjang > 0 || $lebar > 0)) {
                if ($panjang < 5) $panjang = 5;
                if ($lebar < 5) $lebar = 5;
            }

            if ($inputs['kategori'] == 6 && !empty($this->input->post('metode_laser', true))) {
                $inputs['jenisprint_id'] = $this->input->post('metode_laser', true);
            }

            if (in_array($inputs['kategori'], [8, 9])) {
                $area_mapping = [
                    '1' => 100, '2' => 350, '3' => 630, '4' => 1260, '5' => 1536
                ];
                if ($inputs['kategori'] == 9 && $inputs['ket_1'] == '5') {
                    $inputs['lebar'] = 0;
                    $lebar = 0;
                } else {
                    $selected_area = $area_mapping[$inputs['ket_1']] ?? 0;
                    if ($selected_area > 0) {
                        $pricing_qty = ($selected_area / 10000) * $inputs['qty'];
                    }
                }
            }

            if ($satuan_id == 11) {
                $pricing_qty = $panjang * $inputs['qty'];
            } else if ($panjang > 0 && $lebar > 0) {
                $area = ($panjang * $lebar) / 10000;
                $pricing_qty = $area * $inputs['qty'];
            } else if ($panjang > 0 && empty($lebar)) {
                $pricing_qty = $panjang * $inputs['qty'];
            }
        }

        $hargaQuery = $this->M_transaksi->get_product_price($inputs['product'], $pricing_qty, [
            'ket_1' => $inputs['ket_1'],
            'jenisprint_id' => $inputs['jenisprint_id']
        ]);

        $cekhargaproduct = $hargaQuery->row_array();

        if ($cekhargaproduct) {
            $harga_satuan = floatval($cekhargaproduct['harga_1'] ?? 0);
            $subtotal = $harga_satuan * floatval($inputs['qty']);
            $detail = $cekhargaproduct['detail_product'] ?? '';
            
            $harga_minimal = floatval($cekhargaproduct['harga_minimal'] ?? 0);
            $is_min_applied = false;
            
            if ($harga_minimal > 0 && $subtotal < $harga_minimal) {
                $subtotal = $harga_minimal;
                $is_min_applied = true;
            }

            $response = [
                'status' => 'success',
                'harga_satuan' => $harga_satuan,
                'subtotal' => $subtotal,
                'qty' => floatval($inputs['qty']),
                'pricing_qty' => round($pricing_qty, 4),
                'detail' => $detail,
                'is_min_applied' => $is_min_applied,
                'harga_minimal_value' => $harga_minimal
            ];
        } else {
            $response = ['status' => 'not_found', 'message' => 'Harga belum dikonfigurasi untuk spesifikasi ini'];
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    public function action_add_temp()
    {
        $inputs = [
            'product' => $this->input->post('product_id', true),
            'nospk' => $this->input->post('nospk', true),
            'qty' => $this->input->post('qty', true),
            'panjang' => $this->input->post('panjang', true) ?? 0,
            'lebar' => $this->input->post('lebar', true) ?? 0,
            'kategori' => $this->input->post('kategori_id', true),
            'ket_1' => $this->input->post('ket_1', true) ?? 0,
            'jenisprint_id' => $this->input->post('jenisprint_id', true) ?? 0,
            'keterangan' => $this->input->post('keterangan', true),
            'approve' => $this->input->post('approve', true),
            'lokasi_cetak' => $this->input->post('lokasi_cetak', true)
        ];

        // Ensure dynamic pricing keys match schema definitions
        $this->_resolve_dynamic_pricing_keys($inputs['product'], $inputs);

        $product_info = $this->db->select('satuan_id')->get_where('tbl_product', ['product_id' => $inputs['product']])->row();
        $satuan_id = $product_info ? $product_info->satuan_id : 0;

        // Logic Penentuan Harga - Universal
        // Calculate Pricing Quantity based on Category
        $pricing_qty = floatval($inputs['qty']);
        if (in_array($inputs['kategori'], [2, 3, 5, 6, 8, 9])) {
            $panjang = floatval($inputs['panjang'] ?? 0);
            $lebar = floatval($inputs['lebar'] ?? 0);

            // SPECIAL LOGIC: Minimum Dimension for Category 3 & 6
            if (in_array($inputs['kategori'], [3, 6]) && ($panjang > 0 || $lebar > 0)) {
                if ($panjang < 5)
                    $panjang = 5;
                if ($lebar < 5)
                    $lebar = 5;
            }

            // Map Laser Method to jenisprint_id
            if ($inputs['kategori'] == 6 && !empty($this->input->post('metode_laser', true))) {
                $inputs['jenisprint_id'] = $this->input->post('metode_laser', true);
            }

            // DTG & DTF Area Mapping (Fixed Size to m2)
            if (in_array($inputs['kategori'], [8, 9])) {
                $area_mapping = [
                    '1' => 100,  // Logo
                    '2' => 350,  // A5
                    '3' => 630,  // A4
                    '4' => 1260, // A3
                    '5' => 1536  // A3+ (for DTG)
                ];

                // For DTF (Cat 9), option 5 is "Meteran", let dimension block handle it
                if ($inputs['kategori'] == 9 && $inputs['ket_1'] == '5') {
                    // Force Linear Meter Calculation (ignore UI width 60cm for SQL logic)
                    $inputs['lebar'] = 0;
                    $lebar = 0;
                } else {
                    $selected_area = $area_mapping[$inputs['ket_1']] ?? 0;
                    if ($selected_area > 0) {
                        $pricing_qty = ($selected_area / 10000) * $inputs['qty'];
                    }
                }
            }

            if ($satuan_id == 11) {
                // Meter Lari strictly bypasses area calculation and uses Length * Qty
                $pricing_qty = $panjang * $inputs['qty'];
            } else if ($panjang > 0 && $lebar > 0) {
                // Area in m2 = (P cm * L cm / 10000) * Qty Lembar
                $area = ($panjang * $lebar) / 10000;
                $pricing_qty = $area * $inputs['qty'];
            } else if ($panjang > 0 && empty($lebar)) {
                // Meter Lari logic: Harga per meter fix, so Qty = P * Qty
                $pricing_qty = $panjang * $inputs['qty'];
            }
        }

        $hargaQuery = $this->M_transaksi->get_product_price($inputs['product'], $pricing_qty, [
            'ket_1' => $inputs['ket_1'],
            'jenisprint_id' => $inputs['jenisprint_id']
        ]);

        // Safety fallback for perusahaan_id
        $perusahaan_id = $this->session->userdata('perusahaan_id');
        if (empty($perusahaan_id)) {
            // Try to get from SPK or fallback to 1
            $spk = $this->db->get_where('tbl_spk', ['nospk' => $inputs['nospk']])->row_array();
            $perusahaan_id = $spk['perusahaan_id'] ?? 1;
        }

        error_log("ADD_TEMP_DEBUG: product=" . $inputs["product"] . " qty=" . $pricing_qty . " P=" . $inputs["panjang"] . " L=" . $inputs["lebar"] . " ket1=" . $inputs["ket_1"] . " jp=" . $inputs["jenisprint_id"] . " | DB rows=" . $hargaQuery->num_rows());
        $cekhargaproduct = $hargaQuery->row_array();
        // Capture Dynamic Metadata (EAV/JSON)
        // Fields that have their own columns are in standard_keys to avoid duplication in metadata JSON
        $standard_keys = ['product_id', 'nospk', 'qty', 'panjang', 'lebar', 'kategori_id', 'keterangan', 'approve', 'lokasi_cetak'];
        $metadata = [];

        if ($cekhargaproduct) {
            // [NEW] Resolve option labels from dynamic schema
            $product_data = $this->M_master->getProductByIdComplete($inputs['product']);
            $schema_options = [];
            if ($product_data) {
                $raw_schema = !empty($product_data['product_form_schema'])
                    ? $product_data['product_form_schema']
                    : (!empty($product_data['subcategory_form_schema'])
                        ? $product_data['subcategory_form_schema']
                        : ($product_data['category_form_schema'] ?? ''));
                if (!empty($raw_schema)) {
                    $schema = json_decode($raw_schema, true);
                    if (is_array($schema)) {
                        foreach ($schema as $field) {
                            if (isset($field['options']) && is_array($field['options']) && !empty($field['options'])) {
                                $f_name = $field['name'] ?? '';
                                foreach ($field['options'] as $o_val => $o_lbl) {
                                    $schema_options[$f_name][$o_val] = $o_lbl;
                                }
                            }
                        }
                    }
                }
            }

            // Fallback tables mapping
            $side_map = [];
            $sides = $this->db->get('tbl_side')->result();
            foreach ($sides as $s) {
                $side_map[$s->side_id] = $s->side_name;
            }
            $machine_map = [];
            $machines = $this->db->get('tbl_mesin_printing')->result();
            foreach ($machines as $m) {
                $machine_map[$m->mesin_id] = $m->mesin_nama;
            }

            // Merge original product metadata from tbl_harga_product
            if (!empty($cekhargaproduct['metadata'])) {
                $product_metadata = json_decode($cekhargaproduct['metadata'], true);
                if (is_array($product_metadata)) {
                    foreach ($product_metadata as $pk => $pv) {
                        // Skip empty keys caused by unlabelled attributes in price generator
                        if ($pk === '') continue;
                        if (!isset($metadata[$pk])) {
                            $metadata[$pk] = $pv;
                        }
                    }
                }
            }

            foreach ($this->input->post() as $key => $val) {
                if (!in_array($key, $standard_keys) && !empty($val)) {
                    // Resolve value to label if it is an option select
                    $resolved_val = $schema_options[$key][$val] ?? ($key === 'jenisprint_id' ? ($machine_map[$val] ?? $val) : ($key === 'side_id' || $key === 'ket_1' ? ($side_map[$val] ?? $val) : $val));
                    $metadata[$key] = $val;

                    // Also save clean key name without _id for printing (e.g. 'jenisprint' => '[OUTDOR SOLVEN]')
                    $clean_k = str_replace('_id', '', $key);
                    if ($clean_k !== $key) {
                        $metadata[$clean_k] = $resolved_val;
                    }
                }
            }

            $add_temp = [
                'product_id' => $inputs['product'],
                'no_spk' => $inputs['nospk'],
                'temp_qty' => $inputs['qty'],
                'harga_id' => $cekhargaproduct['harga_id'],
                'user_id' => $this->session->userdata('id'),
                'keterangan' => $inputs['keterangan'],
                'temp_panjang' => $inputs['panjang'],
                'temp_lebar' => $inputs['lebar'],
                'perusahaan_id' => (int) $perusahaan_id,
                'approve' => $inputs['approve'],
                'lokasi_cetak' => $inputs['lokasi_cetak'],
                'metadata' => !empty($metadata) ? json_encode($metadata) : null
            ];

            if ($this->db->insert('tbl_temp_transaksi', $add_temp)) {
                $response = ['status' => 'success', 'message' => 'Data berhasil disimpan'];
            } else {
                $response = ['status' => 'error', 'message' => 'Gagal menyimpan data ke database'];
            }
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Data harga tidak ditemukan untuk konfigurasi ini',
                'config' => [
                    'product_id' => $inputs['product'],
                    'qty_checked' => number_format($pricing_qty, 2),
                    'ket_1' => $inputs['ket_1'],
                    'jenisprint_id' => $inputs['jenisprint_id'],
                    'sql' => $this->db->last_query()
                ]
            ];
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    public function delete_temp_item()
    {
        $id = $this->input->post('temp_id', true);
        if ($id) {
            if (is_array($id)) {
                $this->db->where_in('temp_id', $id);
            } else {
                $this->db->where('temp_id', $id);
            }
            $this->db->delete('tbl_temp_transaksi');
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID not found']);
        }
    }

    public function delete_temp_item_editspk()
    {
        $temp_id = $this->input->post('temp_id', true);
        $this->db->where('id', $temp_id);
        $this->db->delete('tbl_pemesanan_detail'); // sesuaikan dengan nama tabel

        echo json_encode(['status' => 'success']);
    }

    public function action_add_temp_editspk()
    {
        $inputs = [
            'kategori' => $this->input->post('kategori_id', true),
            'subkategori' => $this->input->post('subkategori_id', true),
            'bahan' => $this->input->post('bahan_id', true),
            'qty' => $this->input->post('qty', true),
            'user' => $this->input->post('user_id', true),
            'keterangan' => $this->input->post('keterangan', true),
            'panjang' => $this->input->post('panjang', true),
            'lebar' => $this->input->post('lebar', true),
            'ket_1' => $this->input->post('ket_1', true),
            'finishing' => $this->input->post('finishing', true),
            'nospk' => $this->input->post('nospk', true),
            'approve' => $this->input->post('approve', true),
            'product' => $this->input->post('product_id', true),
            'jenisprint_id' => $this->input->post('jenisprint_id', true),
            'lokasi_cetak' => $this->input->post('lokasi_cetak', true)
        ];

        // Ensure dynamic pricing keys match schema definitions
        $this->_resolve_dynamic_pricing_keys($inputs['product'], $inputs);

        // print_r($inputs);

        $product_info = $this->db->select('satuan_id')->get_where('tbl_product', ['product_id' => $inputs['product']])->row();
        $satuan_id = $product_info ? $product_info->satuan_id : 0;

        // Logic Penentuan Harga - Universal
        // Calculate Pricing Quantity based on Category
        $pricing_qty = $inputs['qty'];
        if (in_array($inputs['kategori'], [2, 3, 5, 6, 8, 9])) {
            $panjang = floatval($inputs['panjang'] ?? 0);
            $lebar = floatval($inputs['lebar'] ?? 0);

            // Minimum 5x5 for Flatbed and Laser
            if (in_array($inputs['kategori'], [3, 6]) && ($panjang > 0 || $lebar > 0)) {
                if ($panjang < 5)
                    $panjang = 5;
                if ($lebar < 5)
                    $lebar = 5;
            }

            // Map Laser Method to jenisprint_id
            if ($inputs['kategori'] == 6 && !empty($this->input->post('metode_laser', true))) {
                $inputs['jenisprint_id'] = $this->input->post('metode_laser', true);
            }

            // DTG & DTF Area Mapping (Fixed Size to m2)
            if (in_array($inputs['kategori'], [8, 9])) {
                $area_mapping = [
                    '1' => 100,  // Logo
                    '2' => 350,  // A5
                    '3' => 630,  // A4
                    '4' => 1260, // A3
                    '5' => 1536  // A3+
                ];

                if ($inputs['kategori'] == 9 && $inputs['ket_1'] == '5') {
                    // Force Linear Meter Calculation
                    $inputs['lebar'] = 0;
                    $lebar = 0;
                } else {
                    $selected_area = $area_mapping[$inputs['ket_1']] ?? 0;
                    if ($selected_area > 0) {
                        $pricing_qty = ($selected_area / 10000) * $inputs['qty'];
                    }
                }
            }

            if ($satuan_id == 11) {
                $pricing_qty = $panjang * $inputs['qty'];
            } else if ($panjang > 0 && $lebar > 0) {
                // Area in m2
                $area = ($panjang * $lebar) / 10000;
                $pricing_qty = $area * $inputs['qty'];
            } else if ($panjang > 0 && empty($lebar)) {
                $pricing_qty = $panjang * $inputs['qty'];
            }
        }

        // Capture Dynamic Metadata for Edit Mode
        $standard_keys = ['product_id', 'nospk', 'qty', 'panjang', 'lebar', 'kategori_id', 'keterangan', 'approve', 'lokasi_cetak'];
        $metadata = [];

        $hargaQuery = $this->M_transaksi->get_product_price($inputs['product'], $pricing_qty, [
            'ket_1' => $inputs['ket_1'],
            'jenisprint_id' => $inputs['jenisprint_id']
        ]);

        error_log("ADD_TEMP_DEBUG: product=" . $inputs["product"] . " qty=" . $pricing_qty . " P=" . $inputs["panjang"] . " L=" . $inputs["lebar"] . " ket1=" . $inputs["ket_1"] . " jp=" . $inputs["jenisprint_id"] . " | DB rows=" . $hargaQuery->num_rows());
        $cekhargaproduct = $hargaQuery->row_array();
        $hasilcek = $hargaQuery->num_rows();

        if (!empty($hasilcek)) {
            // [NEW] Resolve option labels from dynamic schema
            $product_data = $this->M_master->getProductByIdComplete($inputs['product']);
            $schema_options = [];
            if ($product_data) {
                $raw_schema = !empty($product_data['product_form_schema'])
                    ? $product_data['product_form_schema']
                    : (!empty($product_data['subcategory_form_schema'])
                        ? $product_data['subcategory_form_schema']
                        : ($product_data['category_form_schema'] ?? ''));
                if (!empty($raw_schema)) {
                    $schema = json_decode($raw_schema, true);
                    if (is_array($schema)) {
                        foreach ($schema as $field) {
                            if (isset($field['options']) && is_array($field['options']) && !empty($field['options'])) {
                                $f_name = $field['name'] ?? '';
                                foreach ($field['options'] as $o_val => $o_lbl) {
                                    $schema_options[$f_name][$o_val] = $o_lbl;
                                }
                            }
                        }
                    }
                }
            }

            // Fallback tables mapping
            $side_map = [];
            $sides = $this->db->get('tbl_side')->result();
            foreach ($sides as $s) {
                $side_map[$s->side_id] = $s->side_name;
            }
            $machine_map = [];
            $machines = $this->db->get('tbl_mesin_printing')->result();
            foreach ($machines as $m) {
                $machine_map[$m->mesin_id] = $m->mesin_nama;
            }

            // Merge original product metadata from tbl_harga_product
            if (!empty($cekhargaproduct['metadata'])) {
                $product_metadata = json_decode($cekhargaproduct['metadata'], true);
                if (is_array($product_metadata)) {
                    foreach ($product_metadata as $pk => $pv) {
                        // Skip empty keys caused by unlabelled attributes in price generator
                        if ($pk === '') continue;
                        if (!isset($metadata[$pk])) {
                            $metadata[$pk] = $pv;
                        }
                    }
                }
            }

            foreach ($this->input->post() as $key => $val) {
                if (!in_array($key, $standard_keys) && !empty($val)) {
                    // Resolve value to label if it is an option select
                    $resolved_val = $schema_options[$key][$val] ?? ($key === 'jenisprint_id' ? ($machine_map[$val] ?? $val) : ($key === 'side_id' || $key === 'ket_1' ? ($side_map[$val] ?? $val) : $val));
                    $metadata[$key] = $val;

                    // Also save clean key name without _id for printing (e.g. 'jenisprint' => '[OUTDOR SOLVEN]')
                    $clean_k = str_replace('_id', '', $key);
                    if ($clean_k !== $key) {
                        $metadata[$clean_k] = $resolved_val;
                    }
                }
            }

            $harga_satuan = (float) ($cekhargaproduct['harga_1'] ?? 0);
            $harga_minimal = (float) ($cekhargaproduct['harga_minimal'] ?? 0);
            $temp_item = (object) [
                'qty' => $inputs['qty'],
                'panjang' => $inputs['panjang'],
                'lebar' => $inputs['lebar'],
                'satuan_id' => $inputs['satuan_id'],
                'kategori_id' => $inputs['kategori_id'],
                'ket_1' => $inputs['ket_1']
            ];
            $subtotal = $this->calculate_snapshot_subtotal($temp_item, $harga_satuan, 'qty', 'panjang', 'lebar', $harga_minimal);

            $metadata = $this->build_price_snapshot_metadata($metadata, $harga_satuan, $subtotal);

            $add_temp = [
                'product_id' => $inputs['product'],
                'nospk' => $inputs['nospk'],
                'qty' => $inputs['qty'],
                'harga_id' => $cekhargaproduct['harga_id'],
                'panjang' => $inputs['panjang'],
                'lebar' => $inputs['lebar'],
                'metadata' => !empty($metadata) ? json_encode($metadata) : null
            ];

            if ($this->db->field_exists('unit_price', 'tbl_pemesanan_detail')) {
                $add_temp['unit_price'] = $harga_satuan;
            }
            if ($this->db->field_exists('subtotal', 'tbl_pemesanan_detail')) {
                $add_temp['subtotal'] = (int) round($subtotal);
            }


            if ($this->db->insert('tbl_pemesanan_detail', $add_temp)) {
                $response = ['status' => 'success', 'message' => 'Data berhasil disimpan'];
            } else {
                $response = ['status' => 'error', 'message' => 'Gagal menyimpan data ke database'];
            }

        } else {
            $response = [
                'status' => 'error',
                'message' => 'Data harga tidak ditemukan',
                'config' => [
                    'product_id' => $inputs['product'],
                    'qty_checked' => number_format($pricing_qty, 2),
                    'ket_1' => $inputs['ket_1'],
                    'jenisprint_id' => $inputs['jenisprint_id'],
                    'sql' => $this->db->last_query()
                ]
            ];
        }


        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function add_pemesanan()
    {
        // 1. Data Collection
        $nospk = $this->input->post('nospk', true);
        $konsumen_id = $this->input->post('konsumen_id', true);
        $total_pemesanan = $this->input->post('total_pemesanan', true);
        $userid = $this->session->userdata('id');
        $perusahaan_id = $this->session->userdata('perusahaan_id');

        // Safety fallback for perusahaan_id to prevent null error
        if (empty($perusahaan_id)) {
            $spk = $this->db->get_where('tbl_spk', ['nospk' => $nospk])->row_array();
            $perusahaan_id = $spk['perusahaan_id'] ?? 1;
        }

        $module = 'Deskprint';
        $action = 'Input_Pemesanan';

        // 2. Validation
        $required_fields = [
            'nospk' => 'Nomor SPK',
            'konsumen_id' => 'Konsumen',
            'total_pemesanan' => 'Total Pemesanan',
            'deskprint_id' => 'Petugas Deskprint',
            'id_jenis_transaksi' => 'Jenis Transaksi'
        ];

        foreach ($required_fields as $field => $label) {
            if (empty($this->input->post($field))) {
                error_log("ADD_PEMESANAN ERROR: Validation failed - $label is empty.");
                $this->session->set_flashdata('error', "$label tidak boleh kosong.");
                redirect('transaksi/createspk');
                return;
            }
        }

        // 3. Fetch Temporary Data
        $detailPemesanan = $this->M_transaksi->tampilTmp($nospk)->result();
        if (empty($detailPemesanan)) {
            error_log("ADD_PEMESANAN ERROR: No items found in temp table for SPK $nospk");
            $this->session->set_flashdata('error', 'Tidak ada item produk dalam pesanan.');
            redirect('transaksi/createspk');
            return;
        }

        // 4. Data Preparation
        $dataPemesanan = [
            'nospk' => $nospk,
            'konsumen_id' => $konsumen_id,
            'deskprint_id' => $this->input->post('deskprint_id', true),
            'id_jenis_transaksi' => $this->input->post('id_jenis_transaksi', true),
            'marketplace_id' => $this->input->post('marketplace_id', true) ?: 0,
            'resi_marketplace' => $this->input->post('resi_marketplace', true) ?: '-',
            'id_pengiriman' => $this->input->post('id_pengiriman', true) ?: 0,
            'batch_pengiriman' => $this->input->post('batch_pengiriman', true) ?: '',
            'perusahaan_id' => $perusahaan_id,
            'status_pembayaran' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // 5. Database Transaction
        $this->db->trans_start();

        // Check if transaction already exists for this SPK
        $existing = $this->db->get_where('tbl_pemesanan', ['nospk' => $nospk])->row();

        if ($existing) {
            error_log("LOG PENGECEKAN: SPK $nospk sudah ada. Menjalankan mode UPDATE.");
            // Update Header
            $this->db->where('nospk', $nospk);
            $this->db->update('tbl_pemesanan', $dataPemesanan);

            // Clear existing details for re-insertion
            $this->db->delete('tbl_pemesanan_detail', ['nospk' => $nospk]);
        } else {
            error_log("LOG PENGECEKAN: SPK $nospk belum ada. Menjalankan mode INSERT.");
            // Insert Header
            $this->db->insert('tbl_pemesanan', $dataPemesanan);
        }

        // Insert/Process Details
        $item_count = 0;
        foreach ($detailPemesanan as $tmp) {
            $harga_satuan = (float) ($tmp->harga_1 ?? 0);
            $harga_minimal = (float) ($tmp->harga_minimal ?? 0);
            $subtotal = $this->calculate_snapshot_subtotal($tmp, $harga_satuan, 'temp_qty', 'temp_panjang', 'temp_lebar', $harga_minimal);
            $metadata = $this->build_price_snapshot_metadata($tmp->metadata ?? null, $harga_satuan, $subtotal);

            $detail_data = [
                'product_id' => $tmp->product_id ?? 0,
                'qty' => $tmp->temp_qty ?? 0,
                'diskon' => $tmp->temp_diskon ?? 0,
                'panjang' => $tmp->temp_panjang ?? 0.0,
                'lebar' => $tmp->temp_lebar ?? 0.0,
                'nospk' => $nospk,
                'harga_id' => $tmp->harga_id ?? null,
                'dateline_tgl' => $tmp->dateline_tgl ?? date('Y-m-d'),
                'dateline_jam' => $tmp->dateline_jam ?? date('H:i:s'),
                'posisi_barang' => 0,
                'finishing_id' => $tmp->finishing_id ?? null,
                'approve' => $tmp->approve ?? 0,
                'lokasi_cetak' => $tmp->lokasi_cetak ?? 0,
                'metadata' => !empty($metadata) ? json_encode($metadata) : null,
                'keterangan' => $tmp->keterangan ?? null,
            ];

            if ($this->db->field_exists('unit_price', 'tbl_pemesanan_detail')) {
                $detail_data['unit_price'] = $harga_satuan;
            }
            if ($this->db->field_exists('subtotal', 'tbl_pemesanan_detail')) {
                $detail_data['subtotal'] = (int) round($subtotal);
            }

            if ($this->db->insert('tbl_pemesanan_detail', $detail_data)) {
                $item_count++;
            }
        }
        error_log("LOG PROSES: Berhasil memproses $item_count item untuk SPK $nospk.");

        $this->db->trans_complete();

        // 6. Response & Finalization
        if ($this->db->trans_status() === FALSE) {
            $error = $this->db->error();
            error_log("ADD_PEMESANAN FATAL: Transaction failed! Message: " . $error['message']);
            $this->session->set_flashdata('error', 'Gagal memproses pemesanan. Database error: ' . $error['message']);
        } else {
            log_activity($userid, $action, $module, $nospk);

            // [AUDIT LOG] Manual entry for explicit tracking
            $this->_log_manual("Input Pemesanan Baru SPK: $nospk", json_encode([
                'konsumen_id' => $konsumen_id,
                'total' => $total_pemesanan,
                'item_count' => $item_count
            ]), 'WRITE');

            $this->M_transaksi->deleteTmp($nospk);

            // --- KIRIM WA OTOMATIS (BOOKING) ---
            $konsumen = $this->db->get_where('tbl_konsumen', ['konsumen_id' => $konsumen_id])->row();
            if ($konsumen && !empty($konsumen->konsumen_nohp)) {
                $perusahaan_info = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row();
                $nama_toko = ($perusahaan_info) ? $perusahaan_info->nama_perusahaan : 'TGM PRINT';

                $wa_msg = "INVOICE #$nospk\n";
                $wa_msg .= "Pelanggan: " . $konsumen->konsumen_nama . "\n";
                $wa_msg .= "--------------------------------\n";

                foreach ($detailPemesanan as $tmp) {
                    $harga_satuan = $tmp->harga_1;
                    $subtotal = ($tmp->temp_panjang != 0 && $tmp->temp_lebar != 0)
                        ? $tmp->temp_qty * $tmp->temp_panjang * $tmp->temp_lebar * $harga_satuan
                        : $tmp->temp_qty * $harga_satuan;

                    $wa_msg .= $tmp->detail_product . " (x" . $tmp->temp_qty . ") - Rp " . number_format($subtotal, 0, ',', '.') . "\n";
                }

                $wa_msg .= "--------------------------------\n";
                $wa_msg .= "Total: Rp " . number_format($total_pemesanan, 0, ',', '.') . "\n\n";
                $wa_msg .= "Link Invoice: https://erp.tgmprint.com/cekpesanan/spk/" . urlencode(encrypt_spk($nospk)) . "\n";
                $wa_msg .= "Terima Kasih - $nama_toko";

                send_wa_notif($konsumen->konsumen_nohp, $wa_msg, 'notif_spk_baru');
            }
            // ------------------------------------

            // ==========================================
            // MARKETPLACE BYPASS LOGIC (LEWATI KASIR)
            // ==========================================
            if ($dataPemesanan['id_jenis_transaksi'] == '2') {
                $this->load->model('M_pembayaran');

                // 1. Jalankan sinkronisasi ke antrian produksi
                $this->M_pembayaran->insertDetailDanProduksi($nospk, $perusahaan_id);

                // 2. Tandai sebagai Paid agar tidak muncul di kasir
                $this->db->where('nospk', $nospk);
                $this->db->update('tbl_pemesanan', ['status_pembayaran' => 'paid']);

                error_log("ADD_PEMESANAN BYPASS: SPK $nospk Marketplace dikirim ke Produksi.");
                $this->session->set_flashdata('success', 'Pesanan Marketplace ' . $nospk . ' berhasil terkirim ke PRODUKSI.');
            } else {
                error_log("ADD_PEMESANAN SUCCESS: SPK $nospk berhasil dikirim ke kasir.");
                $this->session->set_flashdata('success', 'Pemesanan SPK ' . $nospk . ' berhasil dikirim ke kasir.');
            }
        }

        redirect('transaksi/createspk');
    }




    public function pembayaran()
    {
        $data = $this->_get_common_data('Daftar Pemesanan');
        $data['table'] = 'tbl_kategori';
        $data['tampilData'] = $this->M_transaksi->getDataPemesanan()->result();
        $data['total_rows'] = count($data['tampilData']);
        $data['transaksi_belumbayar'] = $this->M_transaksi->getDataPemesanankasir('0')->result();
        $data['total_belumbayar'] = count($data['transaksi_belumbayar']);
        $data['transaksi_sudahbayar'] = $this->M_transaksi->getbayarsukses('1')->result();
        $data['total_sudahbayar'] = count($data['transaksi_sudahbayar']);

        $this->load_admin_view('admin/transaksi/pembayaran', $data);
    }

    public function transaksi_belumbayar()
    {
        $userid = $this->session->userdata('id');
        $level = (int) $this->session->userdata('level');
        $perusahaan_id = $this->session->userdata('perusahaan_id');
        $selected_branch = $this->input->get('branch_id', true);
        if (empty($selected_branch)) {
            $selected_branch = $perusahaan_id;
        }

        $is_privileged = in_array($level, [1, 2, 8, 17, 21, 22]);
        $target_perusahaan = ($is_privileged && $selected_branch !== 'all') ? $selected_branch : ($is_privileged && $selected_branch === 'all' ? null : $perusahaan_id);

        $data = $this->_get_common_data('Dashboard ' . ($is_privileged ? 'Global ' : '') . 'Transaksi');
        $data['table'] = 'tbl_kategori';
        $data['controller'] = 'transaksi';

        // Load data perusahaan untuk filter (BUG FIX)
        $data['companies'] = $this->db->get('tbl_perusahaan')->result();

        // Optimasi: Gunakan count method yang ringan untuk badges
        $data['total_belumbayar'] = $this->M_transaksi->getCountPemesanankasir('0', $target_perusahaan);
        $data['total_sudahbayar'] = $this->M_transaksi->getCountPemesanankasir('1', $target_perusahaan);

        // Load data transaksi pending saja
        $data['transaksi_belumbayar'] = $this->M_transaksi->getDataPemesanankasir('0', $target_perusahaan)->result();

        $data['total_penjualan_harian'] = $this->M_transaksi->getTotalTrxharian($target_perusahaan)->num_rows();
        $data['total_nominal_harian'] = ($this->M_transaksi->getTotalNominalTrxharian($target_perusahaan)->row()->total ?? 0);
        $data['total_pelanggan_harian'] = $this->M_transaksi->get_konsumen_harian($target_perusahaan)->num_rows();
        $data['is_privileged'] = $is_privileged;
        $data['selected_branch'] = $target_perusahaan;

        $this->load_admin_view('admin/transaksi/transaksi_belumbayar', $data);
    }

    /**
     * AJAX Method untuk memuat isi tabel saja (Menghindari 404 saat Segarkan/Reload)
     */
    public function tabel_transaksi_belumbayar()
    {
        $level = (int) $this->session->userdata('level');
        $perusahaan_id = $this->session->userdata('perusahaan_id');
        $selected_branch = $this->input->get('branch_id', true);
        if (empty($selected_branch)) {
            $selected_branch = $perusahaan_id;
        }

        $is_privileged = in_array($level, [1, 2, 8, 17, 21, 22]);
        $target_perusahaan = ($is_privileged && $selected_branch !== 'all') ? $selected_branch : ($is_privileged && $selected_branch === 'all' ? null : $perusahaan_id);

        $data = $this->_get_common_data('Dashboard Transaksi');
        $data['transaksi_belumbayar'] = $this->M_transaksi->getDataPemesanankasir('0', $target_perusahaan)->result();
        $data['controller'] = 'transaksi';

        $this->load->view('admin/transaksi/partial_tabel_transaksi', $data);
    }

    public function transaksi_sudahbayar()
    {
        $data = $this->_get_common_data('Daftar Semua Transaksi');
        $data['table'] = 'tbl_kategori';
        $data['controller'] = 'transaksi';
        
        $data['total_sudahbayar'] = $this->M_transaksi->count_all_sudahbayar('1');
        $data['total_belumbayar'] = $this->M_transaksi->getCountPemesanankasir('0');
        $data['transaksi_sudahbayar'] = []; // Not used anymore for initial load
        $data['all_details'] = []; // Not used anymore for initial load

        $this->load_admin_view('admin/transaksi/transaksi_sudahbayar', $data);
    }

    private function _invoice_detail_metadata($item)
    {
        if (empty($item->metadata) || !is_string($item->metadata)) {
            return [];
        }

        $decoded = json_decode($item->metadata, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function _invoice_detail_price($item)
    {
        $metadata = $this->_invoice_detail_metadata($item);
        if (isset($metadata['unit_price']) && is_numeric($metadata['unit_price'])) {
            return (float) $metadata['unit_price'];
        }

        return (float) ($item->harga_1 ?? 0);
    }

    private function _invoice_detail_total($item, $harga = null)
    {
        $metadata = $this->_invoice_detail_metadata($item);
        if (isset($metadata['subtotal']) && is_numeric($metadata['subtotal'])) {
            return (float) $metadata['subtotal'];
        }

        $harga = $harga === null ? $this->_invoice_detail_price($item) : (float) $harga;
        $qty = (float) ($item->qty ?? 0);
        $panjang = (float) ($item->panjang ?? 0);
        $lebar = (float) ($item->lebar ?? 0);

        if ($panjang > 0 && $lebar > 0) {
            return round($panjang * $lebar * $harga * $qty);
        }

        return $harga * $qty;
    }

    public function ajax_transaksi_sudahbayar()
    {
        $postData = $this->input->post();
        $list = $this->M_transaksi->get_datatables_sudahbayar($postData, '1');
        $data = array();
        
        $nospks = array_column($list, 'nospk');
        $all_details = [];
        if (!empty($nospks)) {
            $chunks = array_chunk($nospks, 500);
            $all_details_flat = [];
            foreach ($chunks as $chunk) {
                $all_details_flat = array_merge($all_details_flat, $this->M_transaksi->getDetailPesanByNospkArray($chunk));
            }
            foreach ($all_details_flat as $det) {
                $all_details[$det->nospk][] = $det;
            }
        }

        foreach ($list as $dt) {
            $row = array();
            
            // Format sama persis dengan view
            $konsumen = $dt->konsumen_nama . ' | ' . $dt->konsumen_nohp;
            
            $qdetail = isset($all_details[$dt->nospk]) ? $all_details[$dt->nospk] : [];
            $item = count($qdetail);

            $real_total = 0;
            foreach ($qdetail as $item_dt) {
                $harga_satuan = $this->_invoice_detail_price($item_dt);
                $real_total += $this->_invoice_detail_total($item_dt, $harga_satuan);
            }
            
            $total_bayar = (float) ($dt->total_bayar ?? 0);
            $kurang_bayar = $real_total - $total_bayar;
            if ($kurang_bayar < 0) $kurang_bayar = 0;
            
            // Generate action buttons
            $action = '<div class="btn-group">';
            if ($dt->status_pembayaran == 'pending' || $dt->status_pembayaran == 'paid' || $dt->status_pembayaran == 'finish') {
                $action .= '<a href="' . base_url() . 'transaksi/invoice_pesanan/' . $dt->nospk . '" class="btn btn-sm btn-info text-white"><i class="fa fa-print"></i> Detail</a>';
            }
            $action .= '</div>';
            
            $status_pemb = "";
            if ($dt->status_pembayaran == 'pending') {
                $status_pemb = '<span class="badge badge-warning text-white">BELUM BAYAR</span>';
            } elseif ($dt->status_pembayaran == 'paid') {
                $status_pemb = '<span class="badge badge-success text-white">LUNAS</span>';
            } elseif ($dt->status_pembayaran == 'finish') {
                $status_pemb = '<span class="badge badge-success text-white">SELESAI</span>';
            } else {
                $status_pemb = '<span class="badge badge-danger text-white">HUTANG</span>';
            }

            $row[] = $dt->nospk;
            $row[] = shortdate_indo($dt->created_at);
            $row[] = $konsumen;
            $row[] = $dt->nama_jenis_transaksi . '<br><small>' . $dt->marketplace_nama . '</small>';
            $row[] = $item;
            $row[] = "Rp. " . number_format($real_total, 0, ',', '.');
            $row[] = $status_pemb;
            $row[] = "Rp. " . number_format($total_bayar, 0, ',', '.');
            $row[] = "Rp. " . number_format($kurang_bayar, 0, ',', '.');
            $row[] = strtoupper($dt->karyawan_nama);
            $row[] = $action;

            $data[] = $row;
        }

        $output = array(
            "draw" => isset($postData['draw']) ? intval($postData['draw']) : 0,
            "recordsTotal" => $this->M_transaksi->count_all_sudahbayar('1'),
            "recordsFiltered" => $this->M_transaksi->count_filtered_sudahbayar($postData, '1'),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function get_transaction_preview_ajax()
    {
        $nospk = $this->input->post('nospk', true);
        if (!$nospk) {
            echo json_encode(['status' => 'error', 'message' => 'No SPK required']);
            return;
        }

        $header = $this->db->select('tbl_pemesanan.*, tbl_konsumen.konsumen_nama, tbl_konsumen.konsumen_nohp, tbl_jenis_transaksi.nama_jenis_transaksi, tbl_marketplace.marketplace_nama')
            ->from('tbl_pemesanan')
            ->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_pemesanan.konsumen_id')
            ->join('tbl_jenis_transaksi', 'tbl_jenis_transaksi.id_jenis_transaksi = tbl_pemesanan.id_jenis_transaksi')
            ->join('tbl_marketplace', 'tbl_marketplace.marketplace_id = tbl_pemesanan.marketplace_id', 'left')
            ->where('tbl_pemesanan.nospk', $nospk)
            ->get()->row();

        if (!$header) {
            echo json_encode(['status' => 'error', 'message' => 'Transaction not found']);
            return;
        }

        $details = $this->M_transaksi->getDetailPesan($nospk)->result();
        
        $side_map = [];
        $sides = $this->db->get('tbl_side')->result();
        foreach ($sides as $s) {
            $side_map[$s->side_id] = str_replace('Side', 'Sisi', $s->side_name);
        }

        $schema_cache = [];
        $items = [];
        foreach ($details as $item) {
            $harga_satuan = $this->resolve_detail_unit_price($item);

            if (isset($item->satuan_id) && $item->satuan_id == 11) {
                $dimensi = $item->panjang . ' m';
            } else if (isset($item->kategori_id) && isset($item->ket_1) && $item->kategori_id == 9 && $item->ket_1 == '5') {
                $dimensi = $item->panjang . ' m';
            } else if (!empty($item->panjang) && !empty($item->lebar) && $item->panjang != '0' && $item->lebar != '0') {
                $dimensi = $item->panjang . ' x ' . $item->lebar;
            } else if (!empty($item->panjang) && $item->panjang != '0' && (empty($item->lebar) || $item->lebar == '0')) {
                $dimensi = $item->panjang . ' m';
            } else if (isset($item->kategori_id) && $item->kategori_id == 1 && !empty($item->ket_1)) {
                $resolved_side = isset($side_map[$item->ket_1]) ? $side_map[$item->ket_1] : $item->ket_1;
                if (stripos($resolved_side, 'Tidak Ada') !== false) {
                    $dimensi = '';
                } else {
                    $dimensi = $resolved_side;
                }
            } else {
                $dimensi = '';
            }

            // Extract metadata (e.g. warna, finishing, variasi, dll)
            $meta = !empty($item->metadata) ? json_decode($item->metadata, true) : [];
            $skip_keys = ['unit_price', 'subtotal', 'price_snapshot_at', 'kategori_id', 'jenisprint_id', 'user_id', 'user', 'subkategori_id', 'subkategori', 'kodeproduk'];
            $variasi_list = [];
            if (is_array($meta)) {
                $prod_id = $item->product_id ?? 0;
                $cat_id = $item->kategori_id ?? 0;
                $cache_key = $cat_id . '_' . $prod_id;
                
                if (!isset($schema_cache[$cache_key])) {
                    $local_schema = [];
                    if ($cat_id) {
                        $cat = $this->db->get_where('tbl_kategori', ['kategori_id' => $cat_id])->row();
                        if ($cat && !empty($cat->form_schema)) {
                            $sc = json_decode($cat->form_schema, true);
                            if (is_array($sc)) $local_schema = array_merge($local_schema, $sc);
                        }
                    }
                    if ($prod_id) {
                        $prod = $this->db->get_where('tbl_product', ['product_id' => $prod_id])->row();
                        if ($prod) {
                            if (!empty($prod->form_schema)) {
                                $sc = json_decode($prod->form_schema, true);
                                if (is_array($sc)) $local_schema = array_merge($local_schema, $sc);
                            }
                            if (!empty($prod->subkategori_id)) {
                                $sub = $this->db->get_where('tbl_subkategori', ['subkategori_id' => $prod->subkategori_id])->row();
                                if ($sub && !empty($sub->form_schema)) {
                                    $sc = json_decode($sub->form_schema, true);
                                    if (is_array($sc)) $local_schema = array_merge($local_schema, $sc);
                                }
                            }
                        }
                    }
                    $schema_cache[$cache_key] = $local_schema;
                }
                $schema = $schema_cache[$cache_key];

                foreach ($meta as $mk => $mv) {
                    if (!in_array(strtolower($mk), $skip_keys) && !empty($mv) && is_scalar($mv)) {
                        $label = ucwords(str_replace('_', ' ', $mk));
                        $resolved_val = $mv;
                        foreach ($schema as $field) {
                            if (isset($field['name']) && $field['name'] == $mk) {
                                if (isset($field['label']) && !empty($field['label'])) {
                                    $label = $field['label'];
                                }
                                if (isset($field['options'][$mv])) {
                                    $resolved_val = $field['options'][$mv];
                                }
                                break;
                            }
                        }
                        $variasi_list[] = [
                            'label' => $label,
                            'value' => strtoupper($resolved_val)
                        ];
                    }
                }
            }

            $subtotal = $this->resolve_detail_subtotal($item, $harga_satuan);

            $items[] = [
                'nama' => $item->detail_product ?? $item->product_nama ?? 'Item',
                'qty' => $item->qty,
                'satuan' => !empty($item->satuan_nama) ? $item->satuan_nama : 'pcs',
                'dimensi' => $dimensi,
                'variasi' => $variasi_list,
                'ket_1' => $item->ket_1 ?? '',
                'harga' => (int) $harga_satuan,
                'subtotal' => (int) round($subtotal)
            ];
        }

        // Fetch payment details
        $pembayaran = $this->db->select('MAX(total_tagihan) as total_tagihan, MAX(total_bayar) as total_bayar')
            ->from('tbl_pembayaran')
            ->where('nospk', $nospk)
            ->get()->row();
            
        $total_items = 0;
        foreach ($items as $item_row) {
            $total_items += (int)($item_row['subtotal'] ?? 0);
        }

        $dp = $pembayaran ? (int)$pembayaran->total_bayar : 0;
        $total_tagihan = ($pembayaran && (int)$pembayaran->total_tagihan > 0) ? (int)$pembayaran->total_tagihan : $total_items;
        $sisa_tagihan = $total_tagihan - $dp;
        if ($sisa_tagihan < 0) $sisa_tagihan = 0;
        
        // Fetch completion date
        $produksi = $this->db->select('MAX(tgl_selesai) as tgl_selesai')
            ->from('tbl_produksi')
            ->where('nospk', $nospk)
            ->get()->row();
        $selesai_at = ($produksi && !empty($produksi->tgl_selesai)) ? date('d/m/Y H:i', strtotime($produksi->tgl_selesai)) : '-';

        // Count total transactions for this customer up to this order
        $trx_count_query = $this->db->select('count(*) as total')
            ->from('tbl_pemesanan')
            ->where('konsumen_id', $header->konsumen_id)
            ->where('created_at <=', $header->created_at)
            ->get()->row();
        $trx_count = $trx_count_query ? $trx_count_query->total : 1;

        $perusahaan = null;
        if (!empty($header->perusahaan_id)) {
            $perusahaan = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $header->perusahaan_id])->row_array();
        }
        if (!$perusahaan) {
            $session_perusahaan_id = $this->session->userdata('perusahaan_id');
            if ($session_perusahaan_id) {
                $perusahaan = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $session_perusahaan_id])->row_array();
            }
        }
        if (!$perusahaan) {
            $perusahaan = $this->db->get_where('tbl_perusahaan', ['status' => '1'])->row_array() 
                ?? $this->db->get_where('tbl_perusahaan', ['status' => 'aktif'])->row_array() 
                ?? $this->db->get('tbl_perusahaan')->row_array();
        }

        echo json_encode([
            'status' => 'success',
            'data' => [
                'nospk' => $header->nospk,
                'nospk_encrypted' => encrypt_url($header->nospk),
                'nospk_encrypted_safe' => urlencode(encrypt_spk($header->nospk)),
                'tanggal' => date('d/m/Y', strtotime($header->created_at)),
                'terima_at' => date('d/m/Y H:i', strtotime($header->created_at)),
                'selesai_at' => $selesai_at,
                'konsumen' => $header->konsumen_nama,
                'konsumen_trx_count' => $trx_count,
                'nohp' => $header->konsumen_nohp,
                'metode' => $header->nama_jenis_transaksi,
                'marketplace' => $header->marketplace_nama,
                'resi' => $header->resi_marketplace,
                'items' => $items,
                'is_paid' => $header->status_pembayaran === 'paid',
                'status_pembayaran_text' => $header->status_pembayaran === 'paid' ? 'Lunas' : 'Belum lunas',
                'dp' => $dp,
                'total_tagihan' => $total_tagihan,
                'sisa_tagihan' => $sisa_tagihan,
                'perusahaan_nama' => $perusahaan['nama_perusahaan'] ?? 'TGM MERCH',
                'perusahaan_alamat' => $perusahaan['alamat'] ?? 'Jl. Serma Marzuki No.50D - Bekasi Selatan',
                'perusahaan_telp' => $perusahaan['no_telp'] ?? '081288192057'
            ]
        ]);
    }


    function bayar($nospkna = null)
    {
        if (empty($nospkna)) {
            $this->session->set_flashdata('error', 'Akses tidak valid. SPK tidak ditemukan.');
            redirect('transaksi/transaksi_belumbayar');
            return;
        }

        $nospk = decrypt_url($nospkna);
        if (empty($nospk)) {
            $this->session->set_flashdata('error', 'Akses tidak valid. SPK gagal diproses.');
            redirect('transaksi/transaksi_belumbayar');
            return;
        }
        $data = $this->_get_common_data('Proses Pembayaran SPK');
        $data['nospk'] = $nospk;
        $data['getdata'] = $this->M_transaksi->getDetailPemesanan()->row_array();
        $data['getdetail'] = $this->M_transaksi->getDetail()->result();
        $data['rekening'] = $this->db->get('tbl_rekening')->result();
        $data['ewallet'] = $this->db->get('tbl_ewallet')->result();
        $data['edc'] = $this->db->get('tbl_mesin_edc')->result();
        $data['hariini'] = date('Y-m-d');
        $data['jamini'] = date('H:i:s');

        // Cek apakah ada invoice gantung/manual di tbl_pembayaran
        $pending_payment = $this->db->order_by('pembayaran_id', 'desc')
            ->get_where('tbl_pembayaran', ['nospk' => $nospk])->row();
        $data['manual_total_tagihan'] = $pending_payment ? $pending_payment->total_tagihan : null;
        $data['pending_payment'] = $pending_payment;

        if ($this->input->post('dateline_tgl', true)) {
            $id = $this->input->post('id', true);
            $update_data = [
                'dateline_tgl' => $this->input->post('dateline_tgl', true),
                'dateline_jam' => $this->input->post('dateline_jam', true),
            ];
            $this->db->where('id', $id)->update('tbl_pemesanan_detail', $update_data);
            $this->session->set_flashdata('success', 'Data Berhasil Disimpan');
            redirect('transaksi/bayar/' . $nospkna);
        }

        $this->load_admin_view('admin/transaksi/bayar', $data);
    }



    public function action_bayar()
    {
        $this->load->model('M_pembayaran');
        $post = $this->input->post(NULL, TRUE);
        $perusahaan = $this->input->post('perusahaan_id', true);
        $userid = $this->session->userdata('id');
        $module = 'kasir';
        $action = 'input_transaksi';

        $this->load->library('ciqrcode');

        $result = $this->M_pembayaran->prosesTransaksi($post, $perusahaan, $userid);
        log_activity($userid, $action, $module, $post['nospk']);

        // [AUDIT LOG]
        $this->_log_manual("Kasir: Pembayaran SPK #" . $post['nospk'], json_encode([
            'grandtotal' => $post['grandtotal'],
            'metode' => $post['id_jenis_transaksi']
        ]), 'WRITE');

        if ($result['status'] == 'success') {
            // --- KIRIM WA OTOMATIS (PEMBAYARAN) ---
            $nospk = $post['nospk'];
            $konsumen_id = $post['konsumen_id'];
            $konsumen = $this->db->get_where('tbl_konsumen', ['konsumen_id' => $konsumen_id])->row();

            if ($konsumen && !empty($konsumen->konsumen_nohp)) {
                $perusahaan_info = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan])->row();
                $nama_toko = ($perusahaan_info) ? $perusahaan_info->nama_perusahaan : 'TGM Print';

                $total_tagihan = number_format(rupiahToInt($post['grandtotal']), 0, ',', '.');
                $total_bayar = 0;
                $total_bayar += rupiahToInt($post['bayar_tunai'] ?? 0);
                $total_bayar += rupiahToInt($post['bayar_transfer'] ?? 0);
                $total_bayar += rupiahToInt($post['bayar_debit'] ?? 0);
                $total_bayar += rupiahToInt($post['bayar_ewallet'] ?? 0);
                $total_bayar += rupiahToInt($post['bayar_dp'] ?? 0);
                $total_bayar += rupiahToInt($post['bayar_qris'] ?? 0);

                $sisa = rupiahToInt($post['grandtotal']) - $total_bayar;
                $status_lunas = ($sisa <= 0) ? 'LUNAS' : 'BELUM LUNAS (Piutang)';

                $wa_msg = "*PEMBAYARAN DITERIMA - $nama_toko*\n\n";
                $wa_msg .= "Halo *" . $konsumen->konsumen_nama . "*,\n";
                $wa_msg .= "Pembayaran Anda telah kami terima.\n\n";
                $wa_msg .= "No. SPK: *$nospk*\n";
                $wa_msg .= "Total Tagihan: *Rp $total_tagihan*\n";
                $wa_msg .= "Diterima: *Rp " . number_format($total_bayar, 0, ',', '.') . "*\n";
                $wa_msg .= "Status: *$status_lunas*\n\n";

                // Add Check Info Link
                $link_cek = base_url('cekpesanan?spk=' . urlencode(encrypt_spk($nospk)));
                $wa_msg .= "Cek status pesanan Anda di sini:\n$link_cek\n\n";

                $wa_msg .= "Terima kasih telah mempercayakan kebutuhan cetak Anda kepada kami.\n\n";
                $wa_msg .= "_Pesan otomatis via ERP TGM Print_";

                send_wa_notif($konsumen->konsumen_nohp, $wa_msg, 'notif_pembayaran');
            }
            // ---------------------------------------

            // Auto-queue the SPK for Lantai 2 printer/operator
            $this->load->model('Print_queue_model');
            $this->Print_queue_model->insert_queue($post['nospk'], 'SPK', 'Lantai 2', 1);

            $this->session->set_flashdata('success', $result['message']);
            redirect(base_url('transaksi/cetak_invoice/' . $post['nospk']));
        } else {
            $this->session->set_flashdata('error', $result['message']);
            $encrypted_spk = encrypt_url($post['nospk']);
            redirect('transaksi/bayar/' . $encrypted_spk);
        }
    }


    public function cetak_invoice($nospk = NULL)
    {
        if (empty($nospk)) {
            $this->session->set_flashdata('error', 'Nomor SPK tidak valid.');
            redirect('administrative/transaksi');
        }
        $this->load->model('M_pembayaran');
        $this->load->model('M_transaksi');
        $level = $this->session->userdata('level');

        // Ambil data pembayaran berdasarkan nospk
        $pembayaran = $this->M_pembayaran->get_pembayaran_by_nospk($nospk);
        if (!$pembayaran) {
            $this->session->set_flashdata('error', 'Data pembayaran tidak ditemukan.');
            redirect('transaksi/pembayaran');
        }

        // Ambil data perusahaan dari tbl_pemesanan
        $pemesanan = $this->db->get_where('tbl_pemesanan', ['nospk' => $nospk])->row_array();

        // --- IDOR PROTECTION ---
        $current_perusahaan_id = $this->session->userdata('perusahaan_id');
        $is_privileged = in_array($level, [1, 17, 22]); // Admin, Owner, Developer
        if (!$is_privileged && isset($pemesanan['perusahaan_id']) && $pemesanan['perusahaan_id'] != $current_perusahaan_id) {
            $this->session->set_flashdata('error', 'Akses Ditolak: Transaksi ini bukan dari cabang Anda.');
            redirect('administrative/transaksi');
        }
        $perusahaan = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $pemesanan['perusahaan_id']])->row_array();

        // Ambil data hak akses berdasarkan level
        $hakakses = $this->db->get_where('tbl_divisi', ['divisi_id' => $level])->row_array();

        // Ambil detail pembayaran (metode pembayaran)
        $detailBayar = $this->M_pembayaran->get_pembayaran_detail($pembayaran->pembayaran_id);

        // Ambil detail produk dari tbl_pembayaran_produk
        $detailProduk = $this->M_pembayaran->get_pembayaran_produk($nospk);

        // Siapkan data untuk view
        $data = [
            'title' => 'Cek Invoice',
            'perusahaan' => $perusahaan,
            'hakakses' => $hakakses['divisi_nama'],
            'pembayaran' => $pembayaran,
            'detailBayar' => $detailBayar,
            'detailProduk' => $detailProduk,
        ];

        // Tampilkan view cetak invoice
        $this->load->view('admin/transaksi/cetak_invoice', $data);
    }

    public function manual()
    {
        $this->load->model('Penjualan_manual_model', 'penjualan');
        $data = $this->_get_common_data('Input Transaksi Manual');
        $data['konsumen'] = $this->db->get('tbl_konsumen')->result();
        $data['produk'] = $this->M_master->getDataProduct()->result();
        $data['jenis_transaksi'] = $this->db->get('tbl_jenis_transaksi')->result();
        $data['rekening'] = $this->db->get('tbl_rekening')->result();
        $data['ewallet'] = $this->db->get('tbl_ewallet')->result();
        $data['edc'] = $this->db->get('tbl_mesin_edc')->result();
        $data['transaksi_terakhir'] = $this->penjualan->get_transaksi_terakhir();

        $this->load_admin_view('admin/transaksi/manual', $data);
    }

    public function simpan_manual()
    {
        $this->load->model('Penjualan_manual_model', 'penjualan');
        try {
            $post = $this->input->post();
            if (empty($post['products']))
                throw new Exception('Detail produk tidak boleh kosong.');
            if (empty($post['konsumen_id']))
                throw new Exception('Data konsumen wajib dipilih.');

            $result = $this->penjualan->simpan_transaksi($post);
            if ($result) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Transaksi Manual #' . $result['nospk'] . ' berhasil.',
                    'nospk' => $result['nospk'],
                    'print_url' => base_url('transaksi/manual_invoice/' . $result['nospk'])
                ]);
            } else {
                throw new Exception('Gagal menyimpan ke database.');
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function manual_invoice($nospk)
    {
        $this->load->model('Penjualan_manual_model', 'penjualan');
        $this->load->model('M_pembayaran');

        $transaksi = $this->penjualan->get_transaksi_by_nospk($nospk);
        if (!$transaksi)
            show_404();

        $data = $this->_get_common_data('Invoice Manual');
        $data['pembayaran'] = $transaksi;
        $data['detailBayar'] = $this->M_pembayaran->get_pembayaran_detail($transaksi->pembayaran_id);
        $data['detailProduk'] = $transaksi->products;

        $this->load->view('admin/transaksi/manual_invoice', $data);
    }

    public function manual_detail($transaction_id)
    {
        $data = $this->_get_common_data('Detail Transaksi Manual');
        $this->load->model('Penjualan_manual_model', 'penjualan');

        $transaction = $this->penjualan->get_transaction_by_id($transaction_id);
        if (!$transaction) {
            $this->session->set_flashdata('error', 'Transaksi tidak ditemukan.');
            redirect('transaksi/manual_list');
        }

        // View expects $transaction as array
        $data['transaction'] = (array) $transaction;

        // View expects $customer separately
        $data['customer'] = [
            'customer_name' => $transaction->customer_name,
            'customer_phone' => $transaction->customer_phone,
            'customer_email' => $transaction->customer_email,
            'customer_address' => $transaction->customer_address,
            'customer_type' => $transaction->customer_type ?? 'umum'
        ];

        $data['items'] = $this->penjualan->get_transaction_items($transaction_id);
        $data['payments'] = $this->penjualan->get_payment_history($transaction_id);

        $this->load_admin_view('admin/transaksi/manual_detail', $data);
    }

    /**
     * Display all manual transactions with filtering and search
     */
    public function manual_list()
    {
        $data = $this->_get_common_data('Daftar Transaksi Manual');

        $filters = array(
            'search' => $this->input->get('search', true),
            'status' => $this->input->get('status', true),
            'payment_status' => $this->input->get('payment_status', true),
            'date_from' => $this->input->get('date_from', true),
            'date_to' => $this->input->get('date_to', true),
            'perusahaan_id' => $this->session->userdata('perusahaan_id')
        );

        $this->load->model('Penjualan_manual_model', 'penjualan');
        $data['transactions'] = $this->penjualan->get_transactions($filters);
        $data['stats'] = $this->penjualan->get_transaction_stats($filters['perusahaan_id']);
        $data['filters'] = $filters;

        $this->load_admin_view('admin/transaksi/manual_list', $data);
    }

    /**
     * Get manual transaction statistics
     */
    private function get_manual_transaction_stats()
    {
        $stats = array();

        // Total transactions
        $stats['total'] = $this->db->count_all('manual_transactions');

        // Today's transactions
        $stats['today'] = $this->db->where('DATE(created_at)', date('Y-m-d'))->count_all_results('manual_transactions');

        // This month's transactions
        $stats['this_month'] = $this->db->where('MONTH(created_at)', date('m'))
            ->where('YEAR(created_at)', date('Y'))
            ->count_all_results('manual_transactions');

        // Payment status counts
        $stats['paid'] = $this->db->where('payment_status', 'paid')->count_all_results('manual_transactions');
        $stats['pending'] = $this->db->where('payment_status', 'pending')->count_all_results('manual_transactions');
        $stats['partial'] = $this->db->where('payment_status', 'partial')->count_all_results('manual_transactions');

        // Total revenue
        $stats['total_revenue'] = $this->db->select_sum('grand_total')->get('manual_transactions')->row()->grand_total ?: 0;

        // Today's revenue
        $stats['today_revenue'] = $this->db->select_sum('grand_total')
            ->where('DATE(created_at)', date('Y-m-d'))
            ->get('manual_transactions')->row()->grand_total ?: 0;

        return $stats;
    }

    public function delete($nospk = null)
    {
        // Handle $nospk from URL or POST
        if (!$nospk) {
            $nospk = $this->input->post('nospk', true);
        }

        if (!$nospk) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'No SPK tidak valid.']);
                return;
            }
            $this->session->set_flashdata('error', 'No SPK tidak valid atau link kadaluarsa.');
            redirect('transaksi/transaksi_belumbayar');
        }

        // Check if SPK exists
        $cek = $this->db->get_where('tbl_pemesanan', ['nospk' => $nospk])->row();
        if (!$cek) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'Data SPK #' . $nospk . ' tidak ditemukan.']);
                return;
            }
            $this->session->set_flashdata('error', 'Data SPK #' . $nospk . ' tidak ditemukan.');
            redirect('transaksi/transaksi_belumbayar');
        }

        // Perform deletion
        $result = $this->M_transaksi->delete_spk_complete($nospk);

        if ($result) {
            // SUCCESS
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'success', 'message' => 'SPK #' . $nospk . ' berhasil dihapus.']);
            } else {
                $this->session->set_flashdata('success', 'SPK #' . $nospk . ' berhasil dihapus beserta seluruh data terkait.');
                redirect('transaksi/transaksi_belumbayar');
            }
        } else {
            // FAILED
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus SPK. Terjadi kesalahan pada database.']);
            } else {
                $this->session->set_flashdata('error', 'Gagal menghapus SPK #' . $nospk . '. Terjadi kesalahan pada database.');
                redirect('transaksi/transaksi_belumbayar');
            }
        }
    }

    /**
     * Delete manual transaction
     */
    function manual_delete($transaction_id)
    {
        $user_id = $this->session->userdata('id');

        // Check if transaction exists
        $transaction = $this->db->get_where('manual_transactions', array('transaction_id' => $transaction_id))->row();
        if (!$transaction) {
            $this->session->set_flashdata('error', 'Transaksi tidak ditemukan.');
            redirect('transaksi/manual_list');
        }

        // Start transaction
        $this->db->trans_start();

        try {
            // Delete related records
            $this->db->delete('manual_payment_history', array('transaction_id' => $transaction_id));
            $this->db->delete('manual_transaction_items', array('transaction_id' => $transaction_id));
            $this->db->delete('manual_transactions', array('transaction_id' => $transaction_id));

            // Log activity
            log_activity($user_id, 'deleted', 'manual_transaction', $transaction_id);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Gagal menghapus transaksi.');
            } else {
                $this->session->set_flashdata('success', 'Transaksi berhasil dihapus.');
            }

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Manual transaction delete error: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Terjadi kesalahan sistem.');
        }

        redirect('transaksi/manual_list');
    }

    /**
     * Export manual transactions to Excel
     */
    function manual_export()
    {
        // Get filter parameters (same as manual_list)
        $search = $this->input->get('search', true);
        $status = $this->input->get('status', true);
        $payment_status = $this->input->get('payment_status', true);
        $date_from = $this->input->get('date_from', true);
        $date_to = $this->input->get('date_to', true);
        $priority = $this->input->get('priority', true);

        // Build query
        $this->db->select('mt.*, mc.customer_name, mc.customer_phone, mc.customer_email');
        $this->db->from('manual_transactions mt');
        $this->db->join('manual_customers mc', 'mt.customer_id = mc.customer_id', 'left');

        // Apply filters
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('mt.transaction_number', $search);
            $this->db->or_like('mc.customer_name', $search);
            $this->db->or_like('mc.customer_phone', $search);
            $this->db->group_end();
        }

        if (!empty($status))
            $this->db->where('mt.status', $status);
        if (!empty($payment_status))
            $this->db->where('mt.payment_status', $payment_status);
        if (!empty($date_from))
            $this->db->where('mt.transaction_date >=', $date_from);
        if (!empty($date_to))
            $this->db->where('mt.transaction_date <=', $date_to);
        if (!empty($priority))
            $this->db->where('mt.priority', $priority);

        $this->db->order_by('mt.created_at', 'DESC');
        $transactions = $this->db->get()->result();

        // Load PHPExcel library
        require_once APPPATH . 'third_party/PHPExcel/PHPExcel.php';

        $excel = new PHPExcel();
        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();

        // Set title
        $sheet->setTitle('Manual Transactions');

        // Set headers
        $headers = array(
            'No',
            'Transaction Number',
            'Customer Name',
            'Phone',
            'Email',
            'Transaction Date',
            'Payment Method',
            'Payment Status',
            'Priority',
            'Subtotal',
            'Discount',
            'Grand Total',
            'Sales Person',
            'Notes',
            'Created At'
        );

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $col++;
        }

        // Add data
        $row = 2;
        foreach ($transactions as $index => $transaction) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $transaction->transaction_number);
            $sheet->setCellValue('C' . $row, $transaction->customer_name);
            $sheet->setCellValue('D' . $row, $transaction->customer_phone);
            $sheet->setCellValue('E' . $row, $transaction->customer_email);
            $sheet->setCellValue('F' . $row, $transaction->transaction_date);
            $sheet->setCellValue('G' . $row, ucfirst($transaction->payment_method));
            $sheet->setCellValue('H' . $row, ucfirst($transaction->payment_status));
            $sheet->setCellValue('I' . $row, ucfirst($transaction->priority));
            $sheet->setCellValue('J' . $row, number_format($transaction->subtotal, 0, ',', '.'));
            $sheet->setCellValue('K' . $row, number_format($transaction->discount_total, 0, ',', '.'));
            $sheet->setCellValue('L' . $row, number_format($transaction->grand_total, 0, ',', '.'));
            $sheet->setCellValue('M' . $row, $transaction->sales_person);
            $sheet->setCellValue('N' . $row, $transaction->notes);
            $sheet->setCellValue('O' . $row, $transaction->created_at);
            $row++;
        }

        // Auto size columns
        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set filename
        $filename = 'manual_transactions_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $writer->save('php://output');
        exit;
    }

    function cetak_invoice_ulang($nospk = NULL)
    {
        if (empty($nospk)) {
            $this->session->set_flashdata('error', 'Nomor SPK tidak valid.');
            redirect('administrative/transaksi');
        }
        $this->load->model('M_pembayaran');
        $this->load->model('M_transaksi');
        $level = $this->session->userdata('level');

        // Match logic with cetak_invoice
        $pembayaran = $this->M_pembayaran->get_pembayaran_by_nospk($nospk);
        if (!$pembayaran) {
            $this->session->set_flashdata('error', 'Data pembayaran tidak ditemukan.');
            redirect('administrative/transaksi');
        }

        $pemesanan = $this->db->get_where('tbl_pemesanan', ['nospk' => $nospk])->row_array();

        // --- IDOR PROTECTION ---
        $current_perusahaan_id = $this->session->userdata('perusahaan_id');
        $is_privileged = in_array($level, [1, 17, 22]); // Admin, Owner, Developer
        if (!$is_privileged && isset($pemesanan['perusahaan_id']) && $pemesanan['perusahaan_id'] != $current_perusahaan_id) {
            $this->session->set_flashdata('error', 'Akses Ditolak: Transaksi ini bukan dari cabang Anda.');
            redirect('administrative/transaksi');
        }

        $perusahaan = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $pemesanan['perusahaan_id']])->row_array();

        // Override Logo with current user's company logo (or main company logo)
        // This ensures the invoice prints with the logo of the branch printing it, or a specific main logo as requested
        $current_perusahaan_id = $this->session->userdata('perusahaan_id');
        $current_perusahaan = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $current_perusahaan_id])->row_array();
        if ($current_perusahaan && !empty($current_perusahaan['logo'])) {
            $perusahaan['logo'] = $current_perusahaan['logo'];
        }
        $hakakses = $this->db->get_where('tbl_divisi', ['divisi_id' => $level])->row_array();

        $detailBayar = $this->M_pembayaran->get_pembayaran_detail($pembayaran->pembayaran_id);
        $detailProduk = $this->M_pembayaran->get_pembayaran_produk($nospk);

        // Auto-Regenerate QR Code if missing
        $qr_file = FCPATH . 'assets/qrcode/' . $nospk . '.png';
        if (!file_exists($qr_file)) {
            $this->load->library('ciqrcode');
            $config = [
                'cacheable' => true,
                'cachedir' => './assets/',
                'errorlog' => './assets/',
                'imagedir' => './assets/qrcode/',
                'quality' => true,
                'size' => '1024',
                'black' => [0, 0, 0],
                'white' => [255, 255, 255],
            ];
            $this->ciqrcode->initialize($config);
            $params = [
                'data' => "https://erp.tgmprint.com/cekpesanan/spk/" . urlencode(encrypt_spk($nospk)),
                'level' => 'H',
                'size' => 10,
                'savename' => $qr_file,
            ];
            $this->ciqrcode->generate($params);
        }

        $data = [
            'title' => 'Cetak Ulang Invoice',
            'perusahaan' => $perusahaan,
            'hakakses' => $hakakses['divisi_nama'] ?? '',
            'pembayaran' => $pembayaran,
            'detailBayar' => $detailBayar,
            'detailProduk' => $detailProduk,
            'preview' => $this->input->get('preview') == 'true',
        ];

        $this->load->view('admin/transaksi/cetak_invoice_ulang', $data);
    }

    function preview_invoice($nospk)
    {
        $data = $this->_get_common_data('Preview Invoice');

        $pembayaran = $this->M_transaksi->getPembayaran($nospk)->row();
        if (!$pembayaran) {
            $this->session->set_flashdata('error', 'Data pembayaran tidak ditemukan.');
            redirect('administrative/transaksi');
        }

        $pemesanan = $this->db->get_where('tbl_pemesanan', ['nospk' => $nospk])->row_array();

        // --- IDOR PROTECTION ---
        $current_perusahaan_id = $this->session->userdata('perusahaan_id');
        $level = $this->session->userdata('level');
        $is_privileged = in_array($level, [1, 17, 22]); // Admin, Owner, Developer
        if (!$is_privileged && isset($pemesanan['perusahaan_id']) && $pemesanan['perusahaan_id'] != $current_perusahaan_id) {
            $this->session->set_flashdata('error', 'Akses Ditolak: Transaksi ini bukan dari cabang Anda.');
            redirect('administrative/transaksi');
        }

        $perusahaan = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $pemesanan['perusahaan_id'] ?? 0])->row_array();

        $data['pembayaran'] = $pembayaran;
        $data['perusahaan'] = $perusahaan;
        $data['detailBayar'] = $this->M_transaksi->getDetailPembayaran($nospk)->result();
        $data['detailProduk'] = $this->M_transaksi->detailproduk($nospk)->result();
        $data['preview'] = true;

        $this->load->view('admin/transaksi/cetak_invoice_ulang', $data);
    }

    function cek_invoice($nospk)
    {
        $this->load->model('M_pembayaran');
        $data = $this->_get_common_data('Cek Invoice');
        $data['pembayaran'] = $this->M_transaksi->getPembayaran($nospk)->row();
        $data['detailBayar'] = $this->M_pembayaran->get_pembayaran_produk($nospk);
        $data['detailProduk'] = $this->M_transaksi->detailproduk($nospk)->result();
        $data['detailprod'] = $data['detailProduk'];

        $this->load_admin_view('admin/transaksi/cek_invoice', $data);
    }




    function op_produksi($op)
    {
        $data = $this->_get_common_data('Daftar Produksi Product');
        $data['table'] = 'tbl_kategori';

        $op_data = $this->M_transaksi->getOPProduksina($op)->result();
        $data['tampilData'] = $op_data;
        $data['total_rows'] = count($op_data);
        $data['operator'] = $this->db->get('tbl_operator')->result();

        // Providing compatibility for views that might expect 'user' or 'divisi'
        $data['user'] = $data['level']['username'] ?? '';
        $data['divisi'] = $data['hakakses'];

        $this->load_admin_view('admin/transaksi/produksi', $data);
    }


    function get_finishing()
    {
        $id = $this->input->post('id', true);
        $data = $this->M_transaksi->get_finishing($id);
        echo json_encode($data);
    }

    function get_largeformat()
    {
        $id = $this->input->post('id', true);
        $data = $this->M_transaksi->get_largeformat($id);
        echo json_encode($data);
    }

    function spk_finishing($nospk)
    {
        $data = $this->_get_common_data('SPK Finishing');

        // Specific data for this view
        $data['pembayaran'] = $this->M_transaksi->getPembayaran($nospk)->row_array();
        $data['detailBayar'] = $this->M_transaksi->getDetailPembayaran($nospk)->result();

        // Ensure 'perusahaan' is passed as array if needed by the view
        $data['perusahaan_data'] = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $data['perusahaan_id']])->row_array();

        $this->load->view('admin/transaksi/spk_finishing', $data);
    }



    public function draft()
    {
        $userId = $this->session->userdata('id');
        $data = $this->_get_common_data('Draft SPK');

        // Ambil data SPK yang belum ada di tbl_pemesanan (Draft)
        $this->db->select('s.*, k.konsumen_nama, jt.nama_jenis_transaksi, (SELECT COUNT(*) FROM tbl_temp_transaksi WHERE no_spk = s.nospk) as total_items');
        $this->db->from('tbl_spk s');
        $this->db->join('tbl_konsumen k', 'k.konsumen_id = s.konsumen_id', 'left');
        $this->db->join('tbl_jenis_transaksi jt', 'jt.id_jenis_transaksi = s.metode_pesan', 'left');
        $this->db->join('tbl_pemesanan p', 'p.nospk = s.nospk', 'left');
        $this->db->where('s.user_id', $userId);
        $this->db->where('p.nospk IS NULL');
        $this->db->order_by('s.spk_time', 'DESC');
        $data['drafts'] = $this->db->get()->result();

        $this->load_admin_view('admin/transaksi/draft', $data);
    }

    public function delete_draft($nospk)
    {
        $userId = $this->session->userdata('id');
        // Pastikan SPK milik user tersebut dan benar-benar masih draft
        $spk = $this->db->get_where('tbl_spk', ['nospk' => $nospk, 'user_id' => $userId])->row_array();
        $is_not_finished = $this->db->get_where('tbl_pemesanan', ['nospk' => $nospk])->num_rows() == 0;

        if ($spk && $is_not_finished) {
            $this->db->trans_start();
            $this->db->delete('tbl_spk', ['nospk' => $nospk]);
            $this->db->delete('tbl_temp_transaksi', ['no_spk' => $nospk]);
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Gagal menghapus draft SPK.');
            } else {
                $this->session->set_flashdata('success', 'Draft SPK berhasil dihapus.');
            }
        } else {
            $this->session->set_flashdata('error', 'Draft tidak ditemukan atau sudah diproses.');
        }
        redirect('transaksi/draft');
    }

    /**
     * Bulk Delete Draft SPKs
     */
    public function bulk_delete_draft()
    {
        $nospk_array = $this->input->post('nospk', true);
        $userId = $this->session->userdata('id');

        if (empty($nospk_array) || !is_array($nospk_array)) {
            echo json_encode(['status' => 'error', 'message' => 'Tidak ada draft yang dipilih.']);
            return;
        }

        $this->db->trans_start();
        $deleted_count = 0;

        foreach ($nospk_array as $nospk) {
            // Verify ownership and status
            $spk = $this->db->get_where('tbl_spk', ['nospk' => $nospk, 'user_id' => $userId])->row_array();
            $is_not_finished = $this->db->get_where('tbl_pemesanan', ['nospk' => $nospk])->num_rows() == 0;

            if ($spk && $is_not_finished) {
                $this->db->delete('tbl_spk', ['nospk' => $nospk]);
                $this->db->delete('tbl_temp_transaksi', ['no_spk' => $nospk]);
                $deleted_count++;
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan saat menghapus draft.']);
        } else {
            echo json_encode([
                'status' => 'success',
                'message' => $deleted_count . ' draft SPK berhasil dihapus.',
                'count' => $deleted_count
            ]);
        }
    }

    function createspk()
    {
        $nospk = $this->uri->segment(3);
        $kodeproduk = $this->input->get('kodeproduk', true);

        $data = $this->_get_common_data('Dashboard Deskprint');
        $data['deskprint_id'] = $this->session->userdata('id');
        $data['nospk'] = $nospk;
        $data['tampilData'] = $this->M_master->getDataProduct()->result();
        $data['total_rows'] = count($data['tampilData']);
        $data['kategori'] = $this->db->get('tbl_kategori')->result();
        $data['subkategori'] = $this->db->get('tbl_subkategori')->result();
        $data['konsumen'] = $this->db->get('tbl_konsumen')->result();
        $data['hariini'] = date('Y-m-d');
        $data['jamini'] = date('H:i:s');

        $spk = $this->db->get_where('tbl_spk', ['nospk' => $nospk])->row_array();
        $data['getkosumen'] = $this->M_transaksi->getkonsumen($spk['konsumen_id'] ?? null, $nospk)->row_array();
        $data['getproduct'] = $data['tampilData'];
        if ($kodeproduk) {
            $data['getproduk'] = $this->M_master->getProductByIdComplete($kodeproduk);
            // Temporary debug logging: record product schema info to application logs
            try {
                $pid = $data['getproduk']['product_id'] ?? 'NULL';
                $pfs = $data['getproduk']['product_form_schema'] ?? '';
                $hash = $pfs ? md5($pfs) : 'EMPTY';
                $len = is_string($pfs) ? strlen($pfs) : 0;
                log_message('debug', "createspk: product_id={$pid} product_form_schema_len={$len} product_form_schema_md5={$hash}");
                if (!empty($pfs))
                    log_message('debug', "createspk: product_form_schema_raw={$pfs}");
            } catch (Exception $e) {
                log_message('error', 'createspk: failed to log product_form_schema: ' . $e->getMessage());
            }
            if (!empty($data['getproduk']['subkategori_config'])) {
                $data['subkategori_config'] = json_decode($data['getproduk']['subkategori_config'], true);
            }
            // Enforce: only use product-level form_schema. No automatic fallback to subcategory/category.
            // This ensures product-specific schema must be defined to render dynamic form.
            if (!empty($data['getproduk']['product_form_schema']) && $data['getproduk']['product_form_schema'] !== '[]') {
                $data['form_schema'] = $data['getproduk']['product_form_schema'];
            } else {
                $data['form_schema'] = null;
            }
        } else {
            $data['getproduk'] = null;
        }
        $data['cek_transaksi'] = $this->db->get_where('tbl_pemesanan', ['nospk' => $nospk])->num_rows();
        $data['bahan'] = $this->db->get('tbl_bahan')->result();
        $data['tipe'] = $this->db->get('tbl_type')->result();
        $data['side'] = $this->db->get('tbl_side')->result();
        $data['mata'] = $this->db->get('tbl_mata_cutting')->result();
        $data['lebarkain'] = $this->db->get('tbl_lebar_textile')->result();
        $data['temp_data'] = $this->M_transaksi->tampilTmp($nospk)->num_rows();

        // Debug helper: jika ?debug_kategori_name=Tekstil diberikan, tampilkan laporan ringkas di browser
        $debug_kategori = $this->input->get('debug_kategori_name', true);
        if (!empty($debug_kategori)) {
            $kname = $debug_kategori;
            $kat = $this->db->get_where('tbl_kategori', ['kategori_nama' => $kname])->row_array();
            if (!$kat) {
                echo "<h3>Debug kategori:</h3><p>Tidak menemukan kategori dengan nama '" . htmlspecialchars($kname) . "'.</p>";
                exit;
            }

            $this->db->select('`tbl_product`.*, `tbl_kategori`.`form_schema` as category_form_schema, `tbl_subkategori`.`form_schema` as subcategory_form_schema');
            $this->db->join('tbl_kategori', 'tbl_product.kategori_id = tbl_kategori.kategori_id', 'left');
            $this->db->join('tbl_subkategori', 'tbl_product.subkategori_id = tbl_subkategori.subkategori_id', 'left');
            $this->db->where('tbl_product.kategori_id', $kat['kategori_id']);
            $prods = $this->db->get('tbl_product')->result_array();

            echo "<h3>Debug kategori: " . htmlspecialchars($kname) . " (ID=" . $kat['kategori_id'] . ")</h3>";
            echo "<table border=1 cellpadding=6 cellspacing=0 style='border-collapse:collapse;font-family:Arial;font-size:13px;'>";
            echo "<tr style='background:#f1f5f9;'><th>product_id</th><th>product_nama</th><th>product_form_schema_len</th><th>product_form_schema_md5</th><th>subcategory_form_schema_len</th><th>category_form_schema_len</th></tr>";
            foreach ($prods as $p) {
                $pfs = $p['product_form_schema'] ?? '';
                $sfs = $p['subcategory_form_schema'] ?? '';
                $cfs = $p['category_form_schema'] ?? '';
                $plen = is_string($pfs) ? strlen($pfs) : 0;
                $slen = is_string($sfs) ? strlen($sfs) : 0;
                $clen = is_string($cfs) ? strlen($cfs) : 0;
                echo "<tr>";
                echo "<td>" . htmlspecialchars($p['product_id']) . "</td>";
                echo "<td>" . htmlspecialchars($p['product_nama']) . "</td>";
                echo "<td align=right>" . $plen . "</td>";
                echo "<td style='font-family:monospace;'>" . ($plen ? md5($pfs) : '') . "</td>";
                echo "<td align=right>" . $slen . "</td>";
                echo "<td align=right>" . $clen . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            exit;
        }

        $this->load_admin_view('admin/transaksi/createspk', $data);
    }



    /**
     * Logic for "Tarik Kembali" or Edit SPK that has been sent to Cashier 
     * but NOT yet paid (status still 'pending').
     */
    public function edit_pending_spk($nospk = null)
    {
        // Handle $nospk from URL or POST
        if (!$nospk) {
            $nospk = $this->input->post('nospk', true);
        }

        if (!$nospk) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'No SPK tidak valid.']);
                return;
            }
            $this->session->set_flashdata('error', 'No SPK tidak valid.');
            redirect('transaksi/transaksi_belumbayar');
        }

        $userId = $this->session->userdata('id');
        $perusahaanId = $this->session->userdata('perusahaan_id');

        // 1. Check if SPK exists and its payment status
        $pemesanan = $this->db->get_where('tbl_pemesanan', ['nospk' => $nospk])->row();

        if (!$pemesanan) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'Data pemesanan tidak ditemukan.']);
                return;
            }
            $this->session->set_flashdata('error', 'Data pemesanan tidak ditemukan.');
            redirect('transaksi/transaksi_belumbayar');
        }

        // --- NEW OWNERSHIP CHECK ---
        $userLevel = $this->session->userdata('level');
        // Only allow the original creator OR Admin (1) OR Owner (17)
        if ($pemesanan->deskprint_id != $userId && !in_array($userLevel, [1, 17])) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'Maaf, Anda hanya dapat menarik kembali SPK yang Anda buat sendiri.']);
                return;
            }
            $this->session->set_flashdata('error', 'Maaf, Anda hanya dapat menarik kembali SPK yang Anda buat sendiri.');
            redirect('transaksi/transaksi_belumbayar');
            return;
        }
        // ---------------------------

        // IMPORTANT: Prevent editing if already paid (partial or full)
        if ($pemesanan->status_pembayaran != 'pending') {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'Maaf, SPK ini sudah diproses pembayarannya oleh kasir dan tidak dapat diedit kembali.']);
                return;
            }
            $this->session->set_flashdata('error', 'Maaf, SPK ini sudah diproses pembayarannya oleh kasir dan tidak dapat diedit kembali.');
            redirect('transaksi/transaksi_belumbayar');
        }

        // 2. Start Transaction to move data back to Temp
        $this->db->trans_start();

        // Fetch current details
        $details = $this->db->get_where('tbl_pemesanan_detail', ['nospk' => $nospk])->result();

        // Clear any existing temp data for this SPK just in case
        $this->db->delete('tbl_temp_transaksi', ['no_spk' => $nospk]);

        // Insert back to temp
        foreach ($details as $d) {
            $temp_data = [
                'product_id' => $d->product_id,
                'no_spk' => $nospk,
                'temp_qty' => $d->qty,
                // 'temp_diskon'   => $d->diskon, // Optional: ensure these exist in tbl_temp_transaksi before enabling
                'temp_panjang' => $d->panjang,
                'temp_lebar' => $d->lebar,
                'harga_id' => $d->harga_id,
                // 'dateline_tgl'  => $d->dateline_tgl,
                // 'dateline_jam'  => $d->dateline_jam,
                // 'finishing_id'  => $d->finishing_id,
                'approve' => $d->approve,
                'lokasi_cetak' => $d->lokasi_cetak,
                'perusahaan_id' => $perusahaanId,
                'user_id' => $userId,
                'keterangan' => $d->keterangan ?? null,
            ];
            $this->db->insert('tbl_temp_transaksi', $temp_data);
        }

        // 3. Remove from final tables (it becomes a draft again)
        $this->db->delete('tbl_pemesanan_detail', ['nospk' => $nospk]);
        $this->db->delete('tbl_pemesanan', ['nospk' => $nospk]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'Gagal memproses penarikan SPK.');
            redirect('transaksi/transaksi_belumbayar');
        } else {
            // Log this action
            log_activity($userId, 'Tarik_Balik_SPK', 'Deskprint', $nospk);

            $this->session->set_flashdata('success', 'SPK #' . $nospk . ' berhasil ditarik kembali ke mode draft para ahli.');
            $this->session->set_flashdata('success', 'SPK #' . $nospk . ' berhasil ditarik kembali ke mode draft para ahli.');
            redirect('transaksi/createspk/' . $nospk);
        }
    }

    function step_satu()
    {
        $userId = $this->session->userdata('id');
        $perusahaan = $this->session->userdata('perusahaan_id');

        // Fallback if session missing
        if (empty($perusahaan)) {
            $perusahaan = $this->input->post('perusahaan_id', true);
        }

        // Safety Cast
        $perusahaan = (int) $perusahaan;
        $userId = (int) $userId;

        $konsumen = $this->input->post('konsumen_id', true);
        $metode_pesan = $this->input->post('id_jenis_transaksi', true);

        // logic Khusus Marketplace: Buat Konsumen Otomatis (Status: 0 / Inactive untuk memisahkan dari database utama)
        if ($metode_pesan == 2) {
            $m_nama = $this->input->post('m_nama_konsumen', true);
            $m_alamat = $this->input->post('m_alamat_konsumen', true);

            $data_m_konsumen = [
                'konsumen_nama' => $m_nama,
                'konsumen_alamat' => $m_alamat,
                'konsumen_nohp' => 'MARKETPLACE',
                'status' => '0' // Default status
            ];
            $this->db->insert('tbl_konsumen', $data_m_konsumen);
            $konsumen = $this->db->insert_id();
        }

        // Ambil nomor urut SPK terakhir untuk bulan & cabang ini, lalu increment
        $lastUrut = $this->M_transaksi->getNoSPK($userId, $perusahaan)->row_array();

        // Handle first-time SPK in the current month (null result)
        $currentUrut = isset($lastUrut['no_urut_spk']) ? (int) $lastUrut['no_urut_spk'] : 0;
        $noUrutSPK = $currentUrut + 1;

        // Generate nospk format: [Branch]-[User]-[MMYY]-[Sequence]
        $newSPK = $perusahaan . '-' . $userId . '-' . date('my') . '-' . $noUrutSPK;

        // Data untuk insert SPK
        $addSPK = [
            'nospk' => $newSPK,
            'user_id' => $userId,
            'no_urut_spk' => $noUrutSPK,
            'konsumen_id' => $konsumen,
            'metode_pesan' => $this->input->post('id_jenis_transaksi', true),
            'id_marketplace' => $this->input->post('marketplace_id', true) ?: 0,
            'resi_marketplace' => $this->input->post('resi_marketplace', true) ?: '-',
            'id_pengiriman' => $this->input->post('id_pengiriman', true) ?: 0,
            'batch_pengiriman' => $this->input->post('batch_pengiriman', true) ?: '',
            'perusahaan_id' => $perusahaan,
        ];

        // Simpan SPK
        if ($this->db->insert('tbl_spk', $addSPK)) {
            $response = [
                'status' => 'success',
                'message' => 'SPK berhasil dibuat',
                'nospk' => $newSPK
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Gagal membuat SPK'
            ];
        }

        // Return JSON response
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }



    public function tambah_konsumen()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            show_404();
            return;
        }
        $nohp = $this->input->post('konsumen_nohp', true);
        $cek = $this->db->get_where('tbl_konsumen', ['konsumen_nohp' => $nohp])->num_rows();
        if ($cek > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Nomor HP sudah terdaftar!']);
            return;
        }
        $data = [
            'konsumen_nama' => $this->input->post('konsumen_nama', true),
            'konsumen_nohp' => $nohp,
            'konsumen_email' => $this->input->post('konsumen_email', true),
            'konsumen_alamat' => $this->input->post('konsumen_alamat', true),
        ];
        $this->db->insert('tbl_konsumen', $data);
        echo json_encode(['status' => 'success']);
    }





    public function daftar_piutang()
    {
        $userid = $this->session->userdata('id');
        $user_level = $this->session->userdata('level');
        $access_params = $this->getAccessControlParams($user_level, $userid);
        
        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Daftar Piutang Konsumen',
            'piutang' => $this->M_transaksi->getPiutangGrouped($access_params)->result(),
            'total_piutang' => $this->M_transaksi->getTotalPiutang($access_params),
            'konsumen_berpiutang' => $this->M_transaksi->getKonsumenBerpiutang($access_params)->num_rows(),
            'banks' => $this->db->get('tbl_rekening')->result(),
            'edcs' => $this->db->get('tbl_mesin_edc')->result(),
            'wallets' => $this->db->get('tbl_ewallet')->result(),
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
        );

        $this->load_admin_view('admin/transaksi/daftar_piutang', $data);
    }

    public function daftar_piutang_lunas()
    {
        $userid = $this->session->userdata('id');
        $user_level = $this->session->userdata('level');
        $access_params = $this->getAccessControlParams($user_level, $userid);
        
        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Riwayat Piutang Lunas',
            'piutang' => $this->M_transaksi->getPiutangLunasGrouped($access_params)->result(),
            'konsumen_lunas' => $this->M_transaksi->getKonsumenLunas($access_params)->num_rows(),
            'banks' => $this->db->get('tbl_rekening')->result(), // Keep for potential refunds or consistency
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
        );

        $this->load_admin_view('admin/transaksi/daftar_piutang_lunas', $data);
    }

    public function get_piutang_detail_ajax()
    {
        $konsumen_id = $this->input->post('konsumen_id', true);
        $userid = $this->session->userdata('id');
        $user_level = $this->session->userdata('level');
        $access_params = $this->getAccessControlParams($user_level, $userid);
        
        $details = $this->M_transaksi->getPiutangDetail($konsumen_id, $access_params)->result();

        echo json_encode(['success' => true, 'data' => $details]);
    }

    public function process_payment()
    {
        // Check if request is AJAX
        if (!$this->input->is_ajax_request()) {
            $this->output->set_status_header(400);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        // Get POST data
        $konsumen_id = $this->input->post('konsumen_id', true);
        $nospk = $this->input->post('nospk', true);
        $payment_amount = $this->input->post('payment_amount', true);
        $payment_method = $this->input->post('payment_method', true);
        $payment_date = $this->input->post('payment_date', true);
        $payment_notes = $this->input->post('payment_notes', true);
        $bank_id = $this->input->post('bank_id', true);
        $edc_id = $this->input->post('edc_id', true);
        $wallet_id = $this->input->post('wallet_id', true);

        // Validate input
        if (empty($konsumen_id) || empty($nospk) || empty($payment_amount) || empty($payment_method) || empty($payment_date)) {
            echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi untuk SPK: ' . $nospk]);
            return;
        }

        try {
            $this->db->trans_start();

            // 1. Get current payment data from tbl_pembayaran
            $pembayaran = $this->db->get_where('tbl_pembayaran', ['nospk' => $nospk])->row();
            if (!$pembayaran) {
                echo json_encode(['success' => false, 'message' => 'Data pembayaran tidak ditemukan untuk SPK: ' . $nospk]);
                return;
            }

            $current_total_bayar = (float) $pembayaran->total_bayar;
            $total_tagihan = (float) $pembayaran->total_tagihan;
            $new_total_bayar = $current_total_bayar + $payment_amount;

            if ($new_total_bayar > $total_tagihan + 1) { // +1 for floating point safety
                echo json_encode(['success' => false, 'message' => 'Jumlah pembayaran melebihi total tagihan.']);
                return;
            }

            // 2. Update tbl_pembayaran
            $status_lunas = ($new_total_bayar >= $total_tagihan - 1) ? '1' : '0';
            $this->db->where('nospk', $nospk);
            $this->db->update('tbl_pembayaran', [
                'total_bayar' => $new_total_bayar,
                'status_lunas' => $status_lunas,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Update tbl_pemesanan status if fully paid
            if ($status_lunas === '1') {
                $this->db->where('nospk', $nospk);
                $this->db->update('tbl_pemesanan', ['status_pembayaran' => 'paid']);
            }

            // Map payment methods to DB enum
            $method_map = [
                'cash' => 'tunai',
                'transfer' => 'transfer',
                'qris' => 'ewallet',
                'edc' => 'debit'
            ];
            $db_method = isset($method_map[$payment_method]) ? $method_map[$payment_method] : 'tunai';

            // 3. Insert into tbl_pembayaran_detail
            $this->db->insert('tbl_pembayaran_detail', [
                'pembayaran_id' => $pembayaran->pembayaran_id,
                'metode' => $db_method,
                'jumlah' => $payment_amount,
                'bank_id' => ($db_method == 'transfer') ? $bank_id : null,
                'edc_id' => ($db_method == 'debit') ? $edc_id : null,
                'ewallet_id' => ($db_method == 'ewallet') ? $wallet_id : null,
                'keterangan' => $payment_notes,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // 4. Update tbl_piutang if exists
            $piutang = $this->db->get_where('tbl_piutang', ['nospk' => $nospk])->row();
            if ($piutang) {
                $pi_new_total_bayar = (float) $piutang->total_bayar + $payment_amount;
                $pi_status = ($pi_new_total_bayar >= (float) $piutang->total_tagihan - 1) ? 'LUNAS' : 'SEBAGIAN';

                $this->db->where('nospk', $nospk);
                $this->db->update('tbl_piutang', [
                    'total_bayar' => $pi_new_total_bayar,
                    'status' => $pi_status,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                // 5. Insert into tbl_piutang_pembayaran
                $this->db->insert('tbl_piutang_pembayaran', [
                    'piutang_id' => $piutang->piutang_id,
                    'tanggal_bayar' => date('Y-m-d H:i:s'),
                    'jumlah_bayar' => $payment_amount,
                    'metode_bayar' => $db_method,
                    'rekening_id' => ($db_method == 'transfer') ? $bank_id : 0,
                    'edc_id' => ($db_method == 'debit') ? $edc_id : 0,
                    'ewallet_id' => ($db_method == 'ewallet') ? $wallet_id : 0,
                    'keterangan' => $payment_notes
                ]);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                echo json_encode(['success' => false, 'message' => 'Gagal memproses transaksi database.']);
            } else {
                // Log activity
                $user_id = $this->session->userdata('id');
                // Check if log_activity exists before calling (original code called it)
                if (function_exists('log_activity')) {
                    log_activity($user_id, 'Payment_Processed', 'Piutang', $nospk);
                }
                echo json_encode(['success' => true, 'message' => 'Pembayaran berhasil diproses untuk SPK: ' . $nospk]);
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function process_payment_collective()
    {
        // Check if request is AJAX
        if (!$this->input->is_ajax_request()) {
            $this->output->set_status_header(400);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        // Get POST data
        $konsumen_id = $this->input->post('konsumen_id', true);
        $payment_amount = (float) $this->input->post('payment_amount', true);
        $payment_method = $this->input->post('payment_method', true);
        $payment_notes = $this->input->post('payment_notes', true);
        $bank_id = $this->input->post('bank_id', true);
        $edc_id = $this->input->post('edc_id', true);
        $wallet_id = $this->input->post('wallet_id', true);
        $payment_date = $this->input->post('payment_date', true) ?: date('Y-m-d');

        // Validate input
        if (empty($konsumen_id) || empty($payment_amount) || empty($payment_method)) {
            echo json_encode(['success' => false, 'message' => 'Data pembayaran tidak lengkap.']);
            return;
        }

        try {
            $this->db->trans_start();

            // Get all unpaid debts for this consumer
            $this->db->select('p.*');
            $this->db->from('tbl_pembayaran p');
            $this->db->join('tbl_pemesanan pm', 'pm.nospk = p.nospk');
            $this->db->where('pm.konsumen_id', $konsumen_id);
            $this->db->where('(p.total_tagihan - p.total_bayar) > 0.5'); // Use tolerance for floating point
            $this->db->order_by('pm.created_at', 'ASC'); // Pay oldest first
            $unpaid_debts = $this->db->get()->result();

            if (empty($unpaid_debts)) {
                echo json_encode(['success' => false, 'message' => 'Tidak ada piutang yang perlu dibayar untuk konsumen ini.']);
                return;
            }

            $remaining_payment = $payment_amount;

            // Map payment methods to DB enum
            $method_map = [
                'cash' => 'tunai',
                'transfer' => 'transfer',
                'qris' => 'ewallet',
                'edc' => 'debit'
            ];
            $db_method = isset($method_map[$payment_method]) ? $method_map[$payment_method] : 'tunai';

            foreach ($unpaid_debts as $debt) {
                if ($remaining_payment <= 0.5)
                    break;

                $sisa_piutang = (float) $debt->total_tagihan - (float) $debt->total_bayar;
                $pay_on_this = min($remaining_payment, $sisa_piutang);

                $new_total_bayar = (float) $debt->total_bayar + $pay_on_this;
                $status_lunas = ($new_total_bayar >= (float) $debt->total_tagihan - 1) ? '1' : '0';

                // 1. Update tbl_pembayaran
                $this->db->where('pembayaran_id', $debt->pembayaran_id);
                $this->db->update('tbl_pembayaran', [
                    'total_bayar' => $new_total_bayar,
                    'status_lunas' => $status_lunas,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                // 2. Update tbl_pemesanan status if fully paid
                if ($status_lunas === '1') {
                    $this->db->where('nospk', $debt->nospk);
                    $this->db->update('tbl_pemesanan', ['status_pembayaran' => 'paid']);
                }

                // 3. Insert into tbl_pembayaran_detail
                $this->db->insert('tbl_pembayaran_detail', [
                    'pembayaran_id' => $debt->pembayaran_id,
                    'metode' => $db_method,
                    'jumlah' => $pay_on_this,
                    'bank_id' => ($db_method == 'transfer') ? $bank_id : null,
                    'edc_id' => ($db_method == 'debit') ? $edc_id : null,
                    'ewallet_id' => ($db_method == 'ewallet') ? $wallet_id : null,
                    'keterangan' => $payment_notes . " (Bayar Kolektif)",
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                // 4. Update tbl_piutang if exists
                $piutang = $this->db->get_where('tbl_piutang', ['nospk' => $debt->nospk])->row();
                if ($piutang) {
                    $pi_new_total_bayar = (float) $piutang->total_bayar + $pay_on_this;
                    $pi_status = ($pi_new_total_bayar >= (float) $piutang->total_tagihan - 1) ? 'LUNAS' : 'SEBAGIAN';

                    $this->db->where('nospk', $debt->nospk);
                    $this->db->update('tbl_piutang', [
                        'total_bayar' => $pi_new_total_bayar,
                        'status' => $pi_status,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                    // 5. Insert into tbl_piutang_pembayaran
                    $this->db->insert('tbl_piutang_pembayaran', [
                        'piutang_id' => $piutang->piutang_id,
                        'tanggal_bayar' => date('Y-m-d H:i:s'),
                        'jumlah_bayar' => $pay_on_this,
                        'metode_bayar' => $db_method,
                        'rekening_id' => ($db_method == 'transfer') ? $bank_id : 0,
                        'edc_id' => ($db_method == 'debit') ? $edc_id : 0,
                        'ewallet_id' => ($db_method == 'ewallet') ? $wallet_id : 0,
                        'keterangan' => $payment_notes . " (Kolektif)"
                    ]);
                }

                $remaining_payment -= $pay_on_this;
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                echo json_encode(['success' => false, 'message' => 'Gagal memproses transaksi database pembayaran kolektif.']);
            } else {
                echo json_encode(['success' => true, 'message' => 'Pembayaran kolektif berhasil diproses.']);
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function update_spk()
    {
        $nospk = $this->uri->segment(3);
        $kodeproduk = $this->input->get('kodeproduk', true);

        $data = $this->_get_common_data('Dashboard Deskprint');
        $data['nospk'] = $nospk;
        $data['tampilData'] = $this->M_master->getDataProduct()->result();
        $data['total_rows'] = count($data['tampilData']);
        $data['kategori'] = $this->db->get('tbl_kategori')->result();
        $data['subkategori'] = $this->db->get('tbl_subkategori')->result();
        $data['konsumen'] = $this->db->get('tbl_konsumen')->result();
        $data['hariini'] = date('Y-m-d');
        $data['jamini'] = date('H:i:s');

        $spk = $this->db->get_where('tbl_spk', ['nospk' => $nospk])->row_array();
        $data['getkosumen'] = $this->M_transaksi->getkonsumen($spk['konsumen_id'] ?? null, $nospk)->row_array();
        $data['getproduct'] = $data['tampilData'];
        $data['getproduk'] = $this->db->get_where('tbl_product', ['product_id' => $kodeproduk])->row_array();
        $data['cek_transaksi'] = $this->db->get_where('tbl_pemesanan', ['nospk' => $nospk])->num_rows();
        $data['bahan'] = $this->db->get('tbl_bahan')->result();
        $data['tipe'] = $this->db->get('tbl_type')->result();
        $data['side'] = $this->db->get('tbl_side')->result();
        $data['mata'] = $this->db->get('tbl_mata_cutting')->result();
        $data['lebarkain'] = $this->db->get('tbl_lebar_textile')->result();
        $data['temp_data'] = $this->M_transaksi->detail_pemesanan($nospk)->result();

        $this->load_admin_view('admin/transaksi/update_spk', $data);
    }


    function transaksi_deskprint()
    {
        $userid = $this->session->userdata('id');
        $data = $this->_get_common_data('Transaksi Deskprint');

        // Fetch additional profile data
        $this->db->select('tbl_karyawan.foto, tbl_karyawan.nama_lengkap');
        $this->db->from('users');
        $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = users.karyawan_id', 'left');
        $this->db->where('users.user_id', $userid);
        $user_more = $this->db->get()->row_array();

        if ($user_more) {
            $data['level'] = array_merge($data['level'], $user_more);
            $data['user_profile'] = $data['level'];
        }

        $jml_transaksi = $this->M_transaksi->jml_transaksi($userid)->row_array();
        $total_transaksi = $this->M_transaksi->total_transaksi($userid)->row_array();

        $data['tampilData'] = $this->M_transaksi->transaksideskprint($userid)->result();
        $data['jml_transaksi_bulan_ini'] = $jml_transaksi['jml'] ?? 0;
        $data['total_transaksi_user'] = $total_transaksi['jml'] ?? 0;

        $this->load_admin_view('admin/transaksi/transaksi_deskprint', $data);
    }

    public function action_add_pemesanan_detail()
    {
        // Simply reuse the unified logic
        $this->action_add_temp();
    }

    function update_approve()
    {
        $id = $this->input->post('id', true);
        $approve = $this->input->post('approve', true);
        $nospk = $this->input->post('nospk', true);

        // Update the approve status in the database
        $this->db->where('id', $id);
        if ($this->db->update('tbl_pemesanan_detail', ['approve' => $approve])) {
            // If update is successful, return success response
            $nospk = encrypt_url($nospk);
            $this->session->set_flashdata('success', 'Data Berhasil Disimpan');
            redirect('transaksi/bayar/' . $nospk);
        } else {
            // If update fails, return error response
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui status']);
        }
    }



    public function export_transaksi_bulanan()
    {
        // Load PHPExcel library
        require_once APPPATH . 'third_party/PHPExcel/PHPExcel.php';

        // Create new PHPExcel object
        $excel = new PHPExcel();

        // Get current month's transactions
        $bulan_ini = date('Y-m');
        $this->db->select('
            p.nospk, 
            p.tgl_bayar,
            p.jam_bayar,
            k.konsumen_nama,
            k.konsumen_nohp,
            p.total_tagihan,
            p.total_bayar,
            u.username as kasir
        ');
        $this->db->from('tbl_pembayaran p');
        $this->db->join('tbl_pemesanan pm', 'pm.nospk = p.nospk');
        $this->db->join('tbl_konsumen k', 'k.konsumen_id = pm.konsumen_id');
        $this->db->join('users u', 'u.user_id = p.kasir_id');
        $this->db->where('DATE_FORMAT(p.tgl_bayar, "%Y-%m") =', $bulan_ini);
        $this->db->where('pm.perusahaan_id', $this->session->userdata('perusahaan_id'));

        $this->db->order_by('p.tgl_bayar DESC, p.jam_bayar DESC');

        $transaksi = $this->db->get()->result();

        // Set document properties
        $excel->getProperties()
            ->setCreator($this->session->userdata('username'))
            ->setTitle('Laporan Transaksi Bulanan')
            ->setSubject('Laporan Transaksi Bulan ' . date('F Y'))
            ->setDescription('Laporan transaksi untuk bulan ' . date('F Y'));

        // Add header
        $excel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'LAPORAN TRANSAKSI BULANAN')
            ->setCellValue('A2', 'Periode: ' . date('F Y'))
            ->setCellValue('A4', 'No')
            ->setCellValue('B4', 'No SPK')
            ->setCellValue('C4', 'Tanggal')
            ->setCellValue('D4', 'Jam')
            ->setCellValue('E4', 'Konsumen')
            ->setCellValue('F4', 'No HP')
            ->setCellValue('G4', 'Total Tagihan')
            ->setCellValue('H4', 'Total Bayar')
            ->setCellValue('I4', 'Kasir');

        // Style header
        $excel->getActiveSheet()->getStyle('A1:I4')->getFont()->setBold(true);
        $excel->getActiveSheet()->mergeCells('A1:I1');
        $excel->getActiveSheet()->mergeCells('A2:I2');
        $excel->getActiveSheet()->getStyle('A1:I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $excel->getActiveSheet()->getStyle('A2:I2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        // Add data
        $row = 5;
        $no = 1;
        $total_tagihan = 0;
        $total_bayar = 0;

        foreach ($transaksi as $trx) {
            $excel->setActiveSheetIndex(0)
                ->setCellValue('A' . $row, $no++)
                ->setCellValue('B' . $row, $trx->nospk)
                ->setCellValue('C' . $row, date('d/m/Y', strtotime($trx->tgl_bayar)))
                ->setCellValue('D' . $row, $trx->jam_bayar)
                ->setCellValue('E' . $row, $trx->konsumen_nama)
                ->setCellValue('F' . $row, $trx->konsumen_nohp)
                ->setCellValue('G' . $row, $trx->total_tagihan)
                ->setCellValue('H' . $row, $trx->total_bayar)
                ->setCellValue('I' . $row, $trx->kasir);

            // Sum totals
            $total_tagihan += $trx->total_tagihan;
            $total_bayar += $trx->total_bayar;

            $row++;
        }

        // Add totals
        $excel->setActiveSheetIndex(0)
            ->setCellValue('A' . ($row + 1), 'TOTAL')
            ->setCellValue('G' . ($row + 1), $total_tagihan)
            ->setCellValue('H' . ($row + 1), $total_bayar);

        // Style totals
        $excel->getActiveSheet()->getStyle('A' . ($row + 1) . ':I' . ($row + 1))->getFont()->setBold(true);
        $excel->getActiveSheet()->mergeCells('A' . ($row + 1) . ':F' . ($row + 1));

        // Set column widths
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);

        // Format numbers
        $excel->getActiveSheet()->getStyle('G5:H' . $row)->getNumberFormat()->setFormatCode('#,##0');

        // Set active sheet index to the first sheet
        $excel->setActiveSheetIndex(0);

        // Set filename
        $filename = 'Laporan_Transaksi_' . date('F_Y') . '.xlsx';

        // Redirect output to client browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $writer->save('php://output');
        exit;
    }

    public function transaksi_kasir()
    {
        $user_level = $this->session->userdata('level');
        $userid = $this->session->userdata('id');

        // Get current year and month for calendar
        $year = $this->input->get('year', true) ?: date('Y');
        $month = $this->input->get('month', true) ?: date('m');
        $selected_date = $this->input->get('date', true) ?: date('Y-m-d');

        $data = $this->_get_common_data('Laporan Transaksi Kasir : ' . $this->session->userdata('username'));
        $data['table'] = 'tbl_pembayaran';

        // Determine access control parameters
        $access_params = $this->getAccessControlParams($user_level, $userid);

        // Calendar data with access control
        $data['selected_year'] = $year;
        $data['selected_month'] = $month;
        $data['selected_date'] = $selected_date;
        $data['calendar_events'] = $this->M_penjualan->getSalesCalendarData($year, $month, $access_params);

        // Sales data for selected date with access control
        $data['sales_data'] = $this->M_penjualan->getSalesByDate($selected_date, $access_params);
        $data['sales_detail'] = $this->M_penjualan->getSalesDetailByDate($selected_date, $access_params);

        // Statistics with access control
        $data['total_sales'] = $this->M_penjualan->getTotalSales($access_params);
        $data['total_transactions'] = $this->M_penjualan->getTotalTransactions($access_params);
        $data['total_customers'] = $this->M_penjualan->getTotalCustomers($access_params);
        $data['monthly_sales'] = $this->M_penjualan->getMonthlySales($year, $month, $access_params);

        // Payment status data with access control
        $data['unpaid_transactions'] = $this->M_transaksi->getDataPemesanankasir('0')->result();
        $data['access_params'] = $access_params;

        $this->load_admin_view('admin/transaksi/transaksi_kasir', $data);
    }

    /**
     * Get access control parameters based on user role
     */
    private function getAccessControlParams($user_level, $userid)
    {
        $selected_perusahaan = $this->input->get('perusahaan_id', true);
        if ($selected_perusahaan === null || $selected_perusahaan === '') {
            $selected_perusahaan = $this->input->post('perusahaan_id', true);
        }
        if ($selected_perusahaan === null || $selected_perusahaan === '') {
            $selected_perusahaan = $this->session->userdata('perusahaan_id');
        }

        $params = array(
            'user_level' => $user_level,
            'user_id' => $userid,
            'perusahaan_id' => $this->session->userdata('perusahaan_id'),
            'filter_branch' => false,
            'branch_id' => null
        );

        // Role 1 (Super Admin), 17 (Owner), 21 (HR), 2 (Manager) have visibility permissions
        $privileged_roles = [1, 2, 8, 17, 21];

        if (in_array($user_level, $privileged_roles)) {
            if ($selected_perusahaan && $selected_perusahaan != 'all') {
                $params['filter_branch'] = true;
                $params['branch_id'] = $selected_perusahaan;
                $params['filter_by_branch'] = true;
            } else {
                $params['filter_branch'] = false;
            }
        }
        // Check if user is kasir (cashier) - level 3
        else if ($user_level == 3) {
            // Kasir can only see transactions they processed
            $params['filter_branch'] = true;
            $params['branch_id'] = $userid;
            $params['filter_by_user'] = true; // Flag to filter by kasir_id
        }

        return $params;
    }

    function get_kasir_shift()
    {
        $this->db->select('ks.*, k.nama_karyawan, k.foto, p.nama_perusahaan');
        $this->db->from('tbl_kasir_shift ks');
        $this->db->join('tbl_karyawan k', 'ks.kasir_id = k.id', 'left');
        $this->db->join('tbl_perusahaan p', 'ks.perusahaan_id = p.id', 'left');
        $this->db->where('ks.perusahaan_id', $this->session->userdata('perusahaan_id'));
        $this->db->order_by('ks.id', 'DESC');
        return $this->db->get()->result();
    }

    // Fungsi untuk mendapatkan data kasir shift dengan laporan lengkap
    function get_kasir_shift_report($perusahaan_id = null)
    {
        if (!$perusahaan_id) {
            $perusahaan_id = $this->session->userdata('perusahaan_id');
        }

        // Ambil data kasir shift
        $this->db->select('ks.*, k.nama_karyawan, k.foto, p.nama_perusahaan');
        $this->db->from('tbl_kasir_shift ks');
        $this->db->join('tbl_karyawan k', 'ks.kasir_id = k.id', 'left');
        $this->db->join('tbl_perusahaan p', 'ks.perusahaan_id = p.id', 'left');
        $this->db->where('ks.perusahaan_id', $perusahaan_id);
        $this->db->order_by('ks.id', 'DESC');
        $kasir_shifts = $this->db->get()->result();

        // Ambil laporan transaksi untuk setiap kasir shift
        foreach ($kasir_shifts as $shift) {
            // Hitung total transaksi
            $this->db->select('COUNT(*) as total_transaksi, SUM(total_bayar) as total_pendapatan');
            $this->db->from('tbl_pembayaran');
            $this->db->where('kasir_id', $shift->kasir_id);
            $this->db->where('perusahaan_id', $perusahaan_id);
            $this->db->where('DATE(pembayaran_tgl) >=', $shift->tanggal_mulai);
            if ($shift->tanggal_selesai) {
                $this->db->where('DATE(pembayaran_tgl) <=', $shift->tanggal_selesai);
            }
            $transaksi_data = $this->db->get()->row();

            // Hitung total pengeluaran
            $this->db->select('SUM(pengeluaran_jumlah) as total_pengeluaran');
            $this->db->from('tbl_pengeluaran');
            $this->db->where('kasir_id', $shift->kasir_id);
            $this->db->where('perusahaan_id', $perusahaan_id);
            $this->db->where('DATE(pengeluaran_tgl) >=', $shift->tanggal_mulai);
            if ($shift->tanggal_selesai) {
                $this->db->where('DATE(pengeluaran_tgl) <=', $shift->tanggal_selesai);
            }
            $pengeluaran_data = $this->db->get()->row();

            // Hitung total piutang
            $this->db->select('SUM(piutang_sisa) as total_piutang');
            $this->db->from('tbl_piutang');
            $this->db->where('kasir_id', $shift->kasir_id);
            $this->db->where('perusahaan_id', $perusahaan_id);
            $this->db->where('DATE(piutang_tgl) >=', $shift->tanggal_mulai);
            if ($shift->tanggal_selesai) {
                $this->db->where('DATE(piutang_tgl) <=', $shift->tanggal_selesai);
            }
            $piutang_data = $this->db->get()->row();

            // Tambahkan data laporan ke shift
            $shift->total_transaksi = $transaksi_data->total_transaksi ?? 0;
            $shift->total_pendapatan = $transaksi_data->total_pendapatan ?? 0;
            $shift->total_pengeluaran = $pengeluaran_data->total_pengeluaran ?? 0;
            $shift->total_piutang = $piutang_data->total_piutang ?? 0;
            $shift->saldo_kasir = ($shift->total_pendapatan - $shift->total_pengeluaran);
        }

        return $kasir_shifts;
    }

    // Fungsi untuk mendapatkan detail laporan kasir per shift
    function get_kasir_shift_detail($shift_id)
    {
        $this->db->select('ks.*, k.nama_karyawan, k.foto, p.nama_perusahaan');
        $this->db->from('tbl_kasir_shift ks');
        $this->db->join('tbl_karyawan k', 'ks.kasir_id = k.id', 'left');
        $this->db->join('tbl_perusahaan p', 'ks.perusahaan_id = p.id', 'left');
        $this->db->where('ks.id', $shift_id);
        $shift = $this->db->get()->row();

        if ($shift) {
            $perusahaan_id = $shift->perusahaan_id;

            // Detail transaksi pembayaran
            $this->db->select('*');
            $this->db->from('tbl_pembayaran');
            $this->db->where('kasir_id', $shift->kasir_id);
            $this->db->where('perusahaan_id', $perusahaan_id);
            $this->db->where('DATE(pembayaran_tgl) >=', $shift->tanggal_mulai);
            if ($shift->tanggal_selesai) {
                $this->db->where('DATE(pembayaran_tgl) <=', $shift->tanggal_selesai);
            }
            $this->db->order_by('pembayaran_tgl', 'DESC');
            $shift->transaksi_pembayaran = $this->db->get()->result();

            // Detail pengeluaran
            $this->db->select('*');
            $this->db->from('tbl_pengeluaran');
            $this->db->where('kasir_id', $shift->kasir_id);
            $this->db->where('perusahaan_id', $perusahaan_id);
            $this->db->where('DATE(pengeluaran_tgl) >=', $shift->tanggal_mulai);
            if ($shift->tanggal_selesai) {
                $this->db->where('DATE(pengeluaran_tgl) <=', $shift->tanggal_selesai);
            }
            $this->db->order_by('pengeluaran_tgl', 'DESC');
            $shift->transaksi_pengeluaran = $this->db->get()->result();

            // Detail piutang
            $this->db->select('*');
            $this->db->from('tbl_piutang');
            $this->db->where('kasir_id', $shift->kasir_id);
            $this->db->where('perusahaan_id', $perusahaan_id);
            $this->db->where('DATE(piutang_tgl) >=', $shift->tanggal_mulai);
            if ($shift->tanggal_selesai) {
                $this->db->where('DATE(piutang_tgl) <=', $shift->tanggal_selesai);
            }
            $this->db->order_by('piutang_tgl', 'DESC');
            $shift->transaksi_piutang = $this->db->get()->result();
        }

        return $shift;
    }

    // Fungsi untuk mendapatkan summary laporan kasir
    function get_kasir_summary($perusahaan_id = null)
    {
        if (!$perusahaan_id) {
            $perusahaan_id = $this->session->userdata('perusahaan_id');
        }

        $summary = array();

        // Total kasir aktif
        $this->db->select('COUNT(DISTINCT kasir_id) as total_kasir');
        $this->db->from('tbl_kasir_shift');
        $this->db->where('perusahaan_id', $perusahaan_id);
        $this->db->where('status', 'aktif');
        $summary['total_kasir_aktif'] = $this->db->get()->row()->total_kasir ?? 0;

        // Total shift hari ini
        $this->db->select('COUNT(*) as total_shift_hari_ini');
        $this->db->from('tbl_kasir_shift');
        $this->db->where('perusahaan_id', $perusahaan_id);
        $this->db->where('DATE(tanggal_mulai)', date('Y-m-d'));
        $summary['total_shift_hari_ini'] = $this->db->get()->row()->total_shift_hari_ini ?? 0;

        // Total pendapatan hari ini
        $this->db->select('SUM(total_bayar) as total_pendapatan_hari_ini');
        $this->db->from('tbl_pembayaran');
        $this->db->where('perusahaan_id', $perusahaan_id);
        $this->db->where('DATE(pembayaran_tgl)', date('Y-m-d'));
        $summary['total_pendapatan_hari_ini'] = $this->db->get()->row()->total_pendapatan_hari_ini ?? 0;

        // Total pengeluaran hari ini
        $this->db->select('SUM(pengeluaran_jumlah) as total_pengeluaran_hari_ini');
        $this->db->from('tbl_pengeluaran');
        $this->db->where('perusahaan_id', $perusahaan_id);
        $this->db->where('DATE(pengeluaran_tgl)', date('Y-m-d'));
        $summary['total_pengeluaran_hari_ini'] = $this->db->get()->row()->total_pengeluaran_hari_ini ?? 0;

        // Saldo kas hari ini
        $summary['saldo_kas_hari_ini'] = $summary['total_pendapatan_hari_ini'] - $summary['total_pengeluaran_hari_ini'];

        return $summary;
    }

    // Fungsi untuk administrator/owner melihat semua data shift kasir
    function get_all_kasir_shift_admin($perusahaan_id = null, $filters = array())
    {
        if (!$perusahaan_id) {
            $perusahaan_id = $this->session->userdata('perusahaan_id');
        }

        // Check role access
        $role_id = $this->session->userdata('role_id');
        if (!in_array($role_id, [1, 2])) { // 1 = Administrator, 2 = Owner
            return false;
        }

        $this->db->select('ks.*, k.nama_karyawan, k.foto, k.email, k.telepon, p.nama_perusahaan');
        $this->db->from('tbl_kasir_shift ks');
        $this->db->join('tbl_karyawan k', 'ks.kasir_id = k.id', 'left');
        $this->db->join('tbl_perusahaan p', 'ks.perusahaan_id = p.id', 'left');
        $this->db->where('ks.perusahaan_id', $perusahaan_id);

        // Apply filters
        if (!empty($filters['status'])) {
            $this->db->where('ks.status', $filters['status']);
        }
        if (!empty($filters['kasir_id'])) {
            $this->db->where('ks.kasir_id', $filters['kasir_id']);
        }
        if (!empty($filters['tanggal_mulai'])) {
            $this->db->where('DATE(ks.tanggal_mulai) >=', $filters['tanggal_mulai']);
        }
        if (!empty($filters['tanggal_selesai'])) {
            $this->db->where('DATE(ks.tanggal_mulai) <=', $filters['tanggal_selesai']);
        }

        $this->db->order_by('ks.id', 'DESC');
        $kasir_shifts = $this->db->get()->result();

        // Ambil laporan transaksi untuk setiap kasir shift
        foreach ($kasir_shifts as $shift) {
            // Hitung total transaksi
            $this->db->select('COUNT(*) as total_transaksi, SUM(total_bayar) as total_pendapatan');
            $this->db->from('tbl_pembayaran');
            $this->db->where('kasir_id', $shift->kasir_id);
            $this->db->where('perusahaan_id', $perusahaan_id);
            $this->db->where('DATE(pembayaran_tgl) >=', $shift->tanggal_mulai);
            if ($shift->tanggal_selesai) {
                $this->db->where('DATE(pembayaran_tgl) <=', $shift->tanggal_selesai);
            }
            $transaksi_data = $this->db->get()->row();

            // Hitung total pengeluaran
            $this->db->select('SUM(pengeluaran_jumlah) as total_pengeluaran');
            $this->db->from('tbl_pengeluaran');
            $this->db->where('kasir_id', $shift->kasir_id);
            $this->db->where('perusahaan_id', $perusahaan_id);
            $this->db->where('DATE(pengeluaran_tgl) >=', $shift->tanggal_mulai);
            if ($shift->tanggal_selesai) {
                $this->db->where('DATE(pengeluaran_tgl) <=', $shift->tanggal_selesai);
            }
            $pengeluaran_data = $this->db->get()->row();

            // Hitung total piutang
            $this->db->select('SUM(piutang_sisa) as total_piutang');
            $this->db->from('tbl_piutang');
            $this->db->where('kasir_id', $shift->kasir_id);
            $this->db->where('perusahaan_id', $perusahaan_id);
            $this->db->where('DATE(piutang_tgl) >=', $shift->tanggal_mulai);
            if ($shift->tanggal_selesai) {
                $this->db->where('DATE(piutang_tgl) <=', $shift->tanggal_selesai);
            }
            $piutang_data = $this->db->get()->row();

            // Tambahkan data laporan ke shift
            $shift->total_transaksi = $transaksi_data->total_transaksi ?? 0;
            $shift->total_pendapatan = $transaksi_data->total_pendapatan ?? 0;
            $shift->total_pengeluaran = $pengeluaran_data->total_pengeluaran ?? 0;
            $shift->total_piutang = $piutang_data->total_piutang ?? 0;
            $shift->saldo_kasir = ($shift->total_pendapatan - $shift->total_pengeluaran);
        }

        return $kasir_shifts;
    }

    // Fungsi untuk mendapatkan rekap kasir shift per periode
    function get_kasir_shift_rekap($perusahaan_id = null, $periode = 'bulan')
    {
        if (!$perusahaan_id) {
            $perusahaan_id = $this->session->userdata('perusahaan_id');
        }

        $rekap = array();

        switch ($periode) {
            case 'hari':
                $group_by = 'DATE(ks.tanggal_mulai)';
                $date_format = '%Y-%m-%d';
                break;
            case 'minggu':
                $group_by = 'YEARWEEK(ks.tanggal_mulai)';
                $date_format = '%Y-%u';
                break;
            case 'bulan':
            default:
                $group_by = 'DATE_FORMAT(ks.tanggal_mulai, "%Y-%m")';
                $date_format = '%Y-%m';
                break;
        }

        // Rekap per periode
        $this->db->select("
                {$group_by} as periode,
                COUNT(DISTINCT ks.kasir_id) as total_kasir,
                COUNT(ks.id) as total_shift,
                SUM(CASE WHEN ks.status = 'aktif' THEN 1 ELSE 0 END) as shift_aktif,
                SUM(CASE WHEN ks.status = 'selesai' THEN 1 ELSE 0 END) as shift_selesai,
                SUM(CASE WHEN ks.status = 'batal' THEN 1 ELSE 0 END) as shift_batal
            ");
        $this->db->from('tbl_kasir_shift ks');
        $this->db->where('ks.perusahaan_id', $perusahaan_id);
        $this->db->group_by($group_by);
        $this->db->order_by('periode', 'DESC');
        $rekap['per_periode'] = $this->db->get()->result();

        // Rekap per kasir
        $this->db->select("
                k.nama_karyawan,
                k.foto,
                COUNT(ks.id) as total_shift,
                SUM(CASE WHEN ks.status = 'aktif' THEN 1 ELSE 0 END) as shift_aktif,
                SUM(CASE WHEN ks.status = 'selesai' THEN 1 ELSE 0 END) as shift_selesai,
                SUM(CASE WHEN ks.status = 'batal' THEN 1 ELSE 0 END) as shift_batal,
                SUM(pb.total_bayar) as total_pendapatan,
                SUM(pe.pengeluaran_jumlah) as total_pengeluaran,
                SUM(pi.piutang_sisa) as total_piutang
            ");
        $this->db->from('tbl_karyawan k');
        $this->db->join('tbl_kasir_shift ks', 'k.id = ks.kasir_id', 'left');
        $this->db->join('(SELECT kasir_id, SUM(total_bayar) as total_bayar FROM tbl_pembayaran WHERE perusahaan_id = ' . $perusahaan_id . ' GROUP BY kasir_id) pb', 'k.id = pb.kasir_id', 'left');
        $this->db->join('(SELECT kasir_id, SUM(pengeluaran_jumlah) as pengeluaran_jumlah FROM tbl_pengeluaran WHERE perusahaan_id = ' . $perusahaan_id . ' GROUP BY kasir_id) pe', 'k.id = pe.kasir_id', 'left');
        $this->db->join('(SELECT kasir_id, SUM(piutang_sisa) as piutang_sisa FROM tbl_piutang WHERE perusahaan_id = ' . $perusahaan_id . ' GROUP BY kasir_id) pi', 'k.id = pi.kasir_id', 'left');
        $this->db->where('k.perusahaan_id', $perusahaan_id);
        $this->db->where('k.role_id', 3); // Role kasir
        $this->db->group_by('k.id');
        $this->db->order_by('total_shift', 'DESC');
        $rekap['per_kasir'] = $this->db->get()->result();

        return $rekap;
    }

    // Fungsi untuk export laporan kasir shift
    function export_kasir_shift_report($perusahaan_id = null, $format = 'excel')
    {
        if (!$perusahaan_id) {
            $perusahaan_id = $this->session->userdata('perusahaan_id');
        }

        // Check role access
        $role_id = $this->session->userdata('role_id');
        if (!in_array($role_id, [1, 2])) { // 1 = Administrator, 2 = Owner
            return false;
        }

        $data = $this->get_all_kasir_shift_admin($perusahaan_id);

        if ($format === 'excel') {
            return $this->generate_excel_report($data);
        } elseif ($format === 'pdf') {
            return $this->generate_pdf_report($data);
        }

        return false;
    }

    // Generate Excel Report
    private function generate_excel_report($data)
    {
        // Implementation for Excel generation
        // This would use PHPExcel or similar library
        return true;
    }

    // Generate PDF Report
    private function generate_pdf_report($data)
    {
        // Implementation for PDF generation
        // This would use TCPDF or similar library
        return true;
    }

    // Method untuk menampilkan view rekap kasir shift
    public function kasir_shift_rekap()
    {
        // Check role access
        $role_id = $this->session->userdata('role_id');
        if (!in_array($role_id, [1, 2])) { // 1 = Administrator, 2 = Owner
            redirect('auth/access_denied');
        }

        $perusahaan_id = $this->session->userdata('perusahaan_id');

        // Get perusahaan data
        $perusahaan = $this->db->get_where('tbl_perusahaan', ['id' => $perusahaan_id])->row_array();

        // Get kasir list for filter
        $kasir_list = $this->db->select('id, nama_karyawan')
            ->from('tbl_karyawan')
            ->where('perusahaan_id', $perusahaan_id)
            ->where('role_id', 3) // Role kasir
            ->order_by('nama_karyawan', 'ASC')
            ->get()->result();

        $data = array(
            'title' => 'Rekap Kasir Shift',
            'perusahaan' => $perusahaan,
            'kasir_list' => $kasir_list
        );

        $this->load->view('admin/transaksi/kasir_shift_rekap', $data);
    }

    // Method untuk API get all kasir shift admin (POST method)
    // public function get_all_kasir_shift_admin() {
    //     // Check role access
    //     $role_id = $this->session->userdata('role_id');
    //     if (!in_array($role_id, [1, 2])) { // 1 = Administrator, 2 = Owner
    //         $this->output->set_status_header(403);
    //         echo json_encode(['error' => 'Access denied']);
    //         return;
    //     }

    //     $perusahaan_id = $this->session->userdata('perusahaan_id');

    //     // Get POST data
    //     $filters = json_decode(file_get_contents('php://input'), true);

    //     // Get data
    //     $data = $this->get_all_kasir_shift_admin($perusahaan_id, $filters);

    //     if ($data === false) {
    //         $this->output->set_status_header(403);
    //         echo json_encode(['error' => 'Access denied']);
    //         return;
    //     }

    //     $this->output->set_content_type('application/json');
    //     echo json_encode($data);
    // }

    // Method untuk API get kasir summary
    public function get_kasir_summary_api()
    {
        // Check role access
        $role_id = $this->session->userdata('role_id');
        if (!in_array($role_id, [1, 2])) { // 1 = Administrator, 2 = Owner
            $this->output->set_status_header(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        $perusahaan_id = $this->session->userdata('perusahaan_id');

        // Get data
        $data = $this->get_kasir_summary($perusahaan_id);

        $this->output->set_content_type('application/json');
        echo json_encode($data);
    }

    // Method untuk API get kasir shift rekap
    public function get_kasir_shift_rekap_api()
    {
        // Check role access
        $role_id = $this->session->userdata('role_id');
        if (!in_array($role_id, [1, 2])) { // 1 = Administrator, 2 = Owner
            $this->output->set_status_header(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        $perusahaan_id = $this->session->userdata('perusahaan_id');
        $periode = $this->input->get('periode', true) ?: 'bulan';

        // Get data
        $data = $this->get_kasir_shift_rekap($perusahaan_id, $periode);

        $this->output->set_content_type('application/json');
        echo json_encode($data);
    }

    // Method untuk mendapatkan data produk dari pemesanan detail
    public function get_products_from_pemesanan($period = 'month')
    {
        $perusahaan_id = $this->session->userdata('perusahaan_id');

        // Load model
        $this->load->model('M_penjualan');

        // Get data
        $data = $this->M_penjualan->getProductsFromPemesananDetail($period, $perusahaan_id);

        $this->output->set_content_type('application/json');
        echo json_encode($data);
    }

    // Method untuk mendapatkan detail transaksi produk
    public function get_product_transaction_details($nospk = null)
    {
        $perusahaan_id = $this->session->userdata('perusahaan_id');

        // Load model
        $this->load->model('M_penjualan');

        // Get data
        $data = $this->M_penjualan->getProductTransactionDetails($nospk, $perusahaan_id);

        $this->output->set_content_type('application/json');
        echo json_encode($data);
    }

    // Method untuk mendapatkan rekap produk dari pemesanan
    public function get_product_summary_from_pemesanan($period = 'month')
    {
        $perusahaan_id = $this->session->userdata('perusahaan_id');

        // Load model
        $this->load->model('M_penjualan');

        // Get data
        $data = $this->M_penjualan->getProductSummaryFromPemesanan($period, $perusahaan_id);

        $this->output->set_content_type('application/json');
        echo json_encode($data);
    }

    // Method untuk mendapatkan produk yang paling sering dipesan
    public function get_most_ordered_products($period = 'month', $limit = 10)
    {
        $perusahaan_id = $this->session->userdata('perusahaan_id');

        // Load model
        $this->load->model('M_penjualan');

        // Get data
        $data = $this->M_penjualan->getMostOrderedProducts($period, $perusahaan_id, $limit);

        $this->output->set_content_type('application/json');
        echo json_encode($data);
    }

    // Method untuk mendapatkan produk berdasarkan status pembayaran
    public function get_products_by_payment_status($payment_status = 'paid', $period = 'month')
    {
        $perusahaan_id = $this->session->userdata('perusahaan_id');

        // Load model
        $this->load->model('M_penjualan');

        // Get data
        $data = $this->M_penjualan->getProductsByPaymentStatus($payment_status, $period, $perusahaan_id);

        $this->output->set_content_type('application/json');
        echo json_encode($data);
    }

    // Method untuk menampilkan view analisis produk dari pemesanan
    public function analisis_produk_pemesanan()
    {
        $perusahaan_id = $this->session->userdata('perusahaan_id');

        // Load model
        $this->load->model('M_penjualan');

        // Get data untuk periode bulan ini
        $products_paid = $this->M_penjualan->getProductsByPaymentStatus('paid', 'month', $perusahaan_id);
        $products_unpaid = $this->M_penjualan->getProductsByPaymentStatus('unpaid', 'month', $perusahaan_id);
        $most_ordered = $this->M_penjualan->getMostOrderedProducts('month', $perusahaan_id, 10);
        $product_summary = $this->M_penjualan->getProductSummaryFromPemesanan('month', $perusahaan_id);

        $data = array(
            'title' => 'Analisis Produk dari Pemesanan',
            'products_paid' => $products_paid,
            'products_unpaid' => $products_unpaid,
            'most_ordered' => $most_ordered,
            'product_summary' => $product_summary
        );

        $this->load_admin_view('admin/transaksi/analisis_produk_pemesanan', $data);
    }

    public function manual_edit($nospk = null)
    {
        if (!$nospk) {
            $this->session->set_flashdata('error', 'No. SPK tidak ditemukan.');
            redirect('penjualan/manual_list');
        }

        $transaksi = $this->manual_sales->get_transaksi_by_nospk($nospk);
        if (!$transaksi) {
            $this->session->set_flashdata('error', 'Transaksi tidak ditemukan.');
            redirect('penjualan/manual_list');
        }

        $data = $this->_get_common_data('Edit Transaksi Manual');
        $data['transaksi'] = $transaksi;
        $data['produk'] = $this->manual_sales->get_produk();
        $data['metode_pembayaran'] = $this->manual_sales->get_metode_pembayaran();
        $data['rekening'] = $this->manual_sales->get_rekening();
        $data['ewallet'] = $this->manual_sales->get_ewallet();
        $data['edc'] = $this->manual_sales->get_edc();

        $this->load_admin_view('admin/transaksi/manual_edit', $data);
    }

    public function manual_update()
    {
        $nospk = $this->input->post('nospk', true);
        if (!$nospk) {
            echo json_encode(['status' => 'error', 'message' => 'No. SPK tidak ditemukan']);
            return;
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('tanggal_transaksi', 'Tanggal Transaksi', 'required');
        $this->form_validation->set_rules('konsumen_id', 'Konsumen', 'required');
        $this->form_validation->set_rules('metode_bayar', 'Metode Pembayaran', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }

        $products = $this->input->post('products', true);
        if (empty($products)) {
            echo json_encode(['status' => 'error', 'message' => 'Detail produk tidak boleh kosong']);
            return;
        }

        try {
            $this->db->trans_start();

            $post = $this->input->post();
            $total_tagihan = 0;
            foreach ($products as $p) {
                $total_tagihan += (floatval($p['jumlah']) * floatval($p['harga_satuan']));
            }

            // 1. Update tbl_spk & tbl_pemesanan
            $this->db->where('nospk', $nospk)->update('tbl_spk', ['konsumen_id' => $post['konsumen_id']]);
            $this->db->where('nospk', $nospk)->update('tbl_pemesanan', [
                'konsumen_id' => $post['konsumen_id'],
                'status_pembayaran' => ($post['metode_bayar'] == 'piutang') ? 'pending' : 'paid'
            ]);

            // 2. Clear old details and re-insert (Cleanest approach for dynamic list)
            $this->db->delete('tbl_pemesanan_detail', ['nospk' => $nospk]);
            $this->db->delete('tbl_pembayaran_produk', ['nospk' => $nospk]);

            foreach ($products as $product) {
                // Using reflection from model to resolve product id (new tags support)
                $p_id = $this->manual_sales->resolve_product_id($product['product_id']);
                $h_id = $this->manual_sales->get_harga_id($p_id);

                $data_detail = [
                    'nospk' => $nospk,
                    'product_id' => $p_id,
                    'qty' => $product['jumlah'],
                    'harga_id' => $h_id,
                    'dateline_tgl' => date('Y-m-d'),
                    'dateline_jam' => date('H:i:s'),
                    'posisi_barang' => 1,
                    'lokasi_cetak' => 1,
                    'approve' => 1,
                    'metadata' => json_encode([
                        'unit_price' => $product['harga_satuan'],
                        'subtotal' => $product['harga_satuan'] * $product['jumlah'],
                        'product_name' => $product['product_name'] ?? null,
                        'price_snapshot_at' => date('Y-m-d H:i:s')
                    ])
                ];
                if ($this->db->field_exists('unit_price', 'tbl_pemesanan_detail')) {
                    $data_detail['unit_price'] = $product['harga_satuan'];
                }
                if ($this->db->field_exists('subtotal', 'tbl_pemesanan_detail')) {
                    $data_detail['subtotal'] = (int) round($product['harga_satuan'] * $product['jumlah']);
                }
                $this->db->insert('tbl_pemesanan_detail', $data_detail);

                $pembayaran_produk = [
                    'nospk' => $nospk,
                    'product_id' => $p_id,
                    'qty' => $product['jumlah'],
                    'harga_id' => $h_id,
                    'dateline_tgl' => date('Y-m-d'),
                    'dateline_jam' => date('H:i:s'),
                    'approve' => '1',
                    'metadata' => json_encode([
                        'unit_price' => $product['harga_satuan'],
                        'subtotal' => $product['harga_satuan'] * $product['jumlah'],
                        'product_name' => $product['product_name'] ?? null
                    ])
                ];
                if ($this->db->field_exists('snapshot_harga', 'tbl_pembayaran_produk')) {
                    $pembayaran_produk['snapshot_harga'] = $product['harga_satuan'];
                }
                if ($this->db->field_exists('snapshot_subtotal', 'tbl_pembayaran_produk')) {
                    $pembayaran_produk['snapshot_subtotal'] = $product['harga_satuan'] * $product['jumlah'];
                }
                if ($this->db->field_exists('snapshot_detail_product', 'tbl_pembayaran_produk')) {
                    $pembayaran_produk['snapshot_detail_product'] = $product['product_name'] ?? null;
                }
                if ($this->db->field_exists('snapshot_source_harga_id', 'tbl_pembayaran_produk')) {
                    $pembayaran_produk['snapshot_source_harga_id'] = $h_id;
                }
                $this->db->insert('tbl_pembayaran_produk', $pembayaran_produk);
            }

            // 3. Update tbl_pembayaran
            $this->db->where('nospk', $nospk)->update('tbl_pembayaran', [
                'konsumen_id' => $post['konsumen_id'],
                'tgl_bayar' => $post['tanggal_transaksi'],
                'total_tagihan' => $total_tagihan,
                'total_bayar' => ($post['metode_bayar'] == 'piutang') ? 0 : $total_tagihan,
                'status_lunas' => ($post['metode_bayar'] == 'piutang') ? '0' : '1'
            ]);

            $pembayaran = $this->db->get_where('tbl_pembayaran', ['nospk' => $nospk])->row();
            if ($pembayaran) {
                $this->db->delete('tbl_pembayaran_detail', ['pembayaran_id' => $pembayaran->pembayaran_id]);
                if ($post['metode_bayar'] != 'piutang') {
                    $metode_mapped = $post['metode_bayar'];
                    if ($metode_mapped == 'cash')
                        $metode_mapped = 'tunai';

                    $this->db->insert('tbl_pembayaran_detail', [
                        'pembayaran_id' => $pembayaran->pembayaran_id,
                        'metode' => $metode_mapped,
                        'jumlah' => $total_tagihan,
                        'referensi' => $post['referensi'] ?? null,
                        'bank_id' => ($post['metode_bayar'] == 'transfer') ? ($post['rekening_id'] ?? null) : null,
                        'edc_id' => ($post['metode_bayar'] == 'debit') ? ($post['edc_id'] ?? null) : null,
                        'ewallet_id' => ($post['metode_bayar'] == 'ewallet') ? ($post['ewallet_id'] ?? null) : null,
                        'keterangan' => $post['keterangan'] ?? null
                    ]);
                }
            }

            // 4. Update tbl_piutang
            $this->db->delete('tbl_piutang', ['nospk' => $nospk]);
            if ($post['metode_bayar'] == 'piutang') {
                $konsumen = $this->manual_sales->get_konsumen_by_id($post['konsumen_id']);
                $termin = (isset($konsumen->termin) && $konsumen->termin > 0) ? $konsumen->termin : 30;
                $this->db->insert('tbl_piutang', [
                    'nospk' => $nospk,
                    'konsumen_id' => $post['konsumen_id'],
                    'perusahaan_id' => $this->session->userdata('perusahaan_id'),
                    'total_tagihan' => $total_tagihan,
                    'total_bayar' => 0,
                    'jatuh_tempo' => date('Y-m-d', strtotime("+{$termin} days")),
                    'status' => 'BELUM LUNAS'
                ]);
            }

            $this->db->trans_complete();
            echo json_encode(['status' => 'success', 'message' => 'Transaksi berhasil diperbarui!']);

        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui: ' . $e->getMessage()]);
        }
    }
}



?>