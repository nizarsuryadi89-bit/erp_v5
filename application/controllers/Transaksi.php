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

        if (isset($item->snapshot_harga) && is_numeric($item->snapshot_harga)) {
            return (float) $item->snapshot_harga;
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

        if (isset($item->snapshot_subtotal) && is_numeric($item->snapshot_subtotal)) {
            return (float) $item->snapshot_subtotal;
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
        $term = trim((string) ($this->input->get('q', true) ?? ''));
        if ($term === '') {
            echo json_encode([]);
            return;
        }

        // Pecah kata kunci berdasarkan spasi agar pencarian multi-kata / fleksibel
        $keywords = array_filter(preg_split('/\s+/', $term));
        $clean_phone = preg_replace('/[^0-9]/', '', $term);

        $this->db->select('konsumen_id, konsumen_nama, konsumen_nohp, konsumen_email, is_blacklist, catatan_blacklist');
        $this->db->from('tbl_konsumen');

        if (!empty($keywords)) {
            $this->db->group_start();
            foreach ($keywords as $kw) {
                $kw_clean = strtolower($kw);
                $this->db->group_start();
                $this->db->like('LOWER(konsumen_nama)', $kw_clean);
                $this->db->or_like('LOWER(konsumen_nohp)', $kw_clean);
                $this->db->or_like('LOWER(konsumen_email)', $kw_clean);
                if (strlen($clean_phone) >= 3) {
                    $this->db->or_like("REPLACE(REPLACE(konsumen_nohp, ' ', ''), '-', '')", $clean_phone);
                }
                $this->db->group_end();
            }
            $this->db->group_end();
        }

        // Sembunyikan data satu-kali-pakai dari Marketplace berdasarkan flag khusus di No. HP
        $this->db->where('konsumen_nohp !=', 'MARKETPLACE');

        // Prioritas hasil: yang namanya berawalan kata kunci diletakkan di urutan paling atas
        $first_kw = strtolower(reset($keywords) ?: $term);
        $escaped_kw = $this->db->escape_like_str($first_kw);
        $this->db->order_by("CASE WHEN LOWER(konsumen_nama) LIKE '{$escaped_kw}%' THEN 1 ELSE 2 END", 'ASC', FALSE);
        $this->db->order_by('konsumen_nama', 'ASC');
        $this->db->limit(35);

        $query = $this->db->get();
        if ($query) {
            echo json_encode($query->result());
        } else {
            echo json_encode([]);
        }
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
            return;
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
                'title' => 'Rp ' . number_format($event->total_amount) . ' (' . $event->total_transactions . ' transaksi)',
                'date' => $event->tanggal,
                'backgroundColor' => ($event->total_amount > 0) ? '#28a745' : '#6c757d',
                'borderColor' => ($event->total_amount > 0) ? '#28a745' : '#6c757d',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'total' => $event->total_amount,
                    'count' => $event->total_transactions
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

        return array_map(function ($row) { return (object) $row; }, array_values($combined_data));
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
        if (!$is_temp) {
            foreach (['unit_price', 'subtotal'] as $field) {
                if ($this->db->field_exists($field, $table)) { $this->db->select("t.$field"); }
            }
        }
        $this->db->from("$table t");
        $this->db->join('tbl_harga_product h', 't.harga_id = h.harga_id', 'left');
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

        $schema = [];
        foreach (['product_form_schema', 'subcategory_form_schema', 'category_form_schema'] as $field) {
            $candidate = $this->decode_metadata_array($product_data[$field] ?? null);
            if ($candidate) { $schema = $candidate; break; }
        }
        if (!$schema) { return; }

        $key_dim1 = 'ket_1';
        $key_dim2 = 'jenisprint_id';
        $unmapped_names = [];

        foreach ($schema as $field) {
            if (is_array($field) && is_array($field['options'] ?? null) && !empty($field['options']) && in_array($field['type'] ?? '', ['select', 'radio'])) {
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
            $value = $this->input->post($key_dim1, true);
            $inputs['ket_1'] = is_scalar($value) ? $value : 0;
        }
        if ($key_dim2 && $this->input->post($key_dim2) !== null) {
            $value = $this->input->post($key_dim2, true);
            $inputs['jenisprint_id'] = is_scalar($value) ? $value : 0;
        }
    }
    /**
     * Estimate price without inserting - for live preview in SPK form
     */
    private function validate_item_inputs(array &$inputs)
    {
        if (!is_scalar($inputs['product']) || !ctype_digit((string) $inputs['product']) || (int) $inputs['product'] <= 0 ||
            !is_numeric($inputs['qty']) || !is_finite((float) $inputs['qty']) || (float) $inputs['qty'] <= 0) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'Produk dan quantity tidak valid.']));
            return false;
        }
        foreach (['panjang', 'lebar'] as $field) {
            $value = $inputs[$field] ?? 0;
            if ($value === '') { $value = 0; }
            if (!is_numeric($value) || !is_finite((float) $value) || (float) $value < 0) {
                $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'Ukuran harus berupa angka nonnegatif.']));
                return false;
            }
            $inputs[$field] = (float) $value;
        }
        foreach (['ket_1', 'jenisprint_id'] as $field) {
            if (isset($inputs[$field]) && !is_scalar($inputs[$field])) {
                $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'Spesifikasi produk tidak valid.']));
                return false;
            }
        }
        return true;
    }

    private function validate_item_spk($nospk, $draft)
    {
        $table = $draft ? 'tbl_spk' : 'tbl_pemesanan';
        $row = is_string($nospk) && $nospk !== '' ? $this->db->get_where($table, ['nospk' => $nospk])->row() : null;
        $owner = $row ? ($draft ? $row->user_id : $row->deskprint_id) : null;
        $privileged = in_array((int) $this->session->userdata('level'), [1, 17], true);
        if (!$row || (!$privileged && ($owner != $this->session->userdata('id') ||
                (int) $row->perusahaan_id !== (int) $this->session->userdata('perusahaan_id'))) ||
            (!$draft && ($row->status_pembayaran !== 'pending' || $this->db->get_where('tbl_pembayaran', ['nospk' => $nospk])->row()))) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error', 'message' => 'SPK tidak dapat diubah: tidak ditemukan, di luar akses Anda, atau sudah diproses kasir.'
            ]));
            return false;
        }
        return true;
    }

    private function pricing_quantity(array &$inputs, $satuan_id)
    {
        $pricing_qty = floatval($inputs['qty']);
        // Area/meter units also apply to finishing, including laminasi in category 16.
        if (in_array($inputs['kategori'], [2, 3, 5, 6, 8, 9]) || in_array((int) $satuan_id, [2, 11], true)) {
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
                // Area = P * L (Input unit matches pricing unit)
                $area = ($panjang * $lebar);
                $pricing_qty = $area * $inputs['qty'];
            } else if ($panjang > 0 && empty($lebar)) {
                $pricing_qty = $panjang * $inputs['qty'];
            }
        }

        return $pricing_qty;
    }

    private function priced_subtotal($price, $pricing_qty)
    {
        return max((float) ($price['harga_minimal'] ?? 0), (float) ($price['harga_1'] ?? 0) * $pricing_qty);
    }

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

        if (!$this->validate_item_inputs($inputs)) { return; }

        // Resolve dynamic pricing keys
        $this->_resolve_dynamic_pricing_keys($inputs['product'], $inputs);

        $product_info = $this->db->select('satuan_id, kategori_id')->get_where('tbl_product', ['product_id' => $inputs['product']])->row();
        if (!$product_info) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'Produk tidak ditemukan.']));
            return;
        }
        $satuan_id = $product_info->satuan_id;
        $inputs['kategori'] = $product_info->kategori_id;

        $pricing_qty = $this->pricing_quantity($inputs, $satuan_id);

        $hargaQuery = $this->M_transaksi->get_product_price($inputs['product'], $pricing_qty, [
            'ket_1' => $inputs['ket_1'],
            'jenisprint_id' => $inputs['jenisprint_id']
        ]);

        $cekhargaproduct = $hargaQuery->row_array();

        if ($cekhargaproduct) {
            $harga_satuan = floatval($cekhargaproduct['harga_1'] ?? 0);
            $subtotal = $harga_satuan * floatval($pricing_qty);
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

        if (!$this->validate_item_inputs($inputs) || !$this->validate_item_spk($inputs['nospk'], true)) { return; }

        // Ensure dynamic pricing keys match schema definitions
        $this->_resolve_dynamic_pricing_keys($inputs['product'], $inputs);

        $product_info = $this->db->select('satuan_id, kategori_id')->get_where('tbl_product', ['product_id' => $inputs['product']])->row();
        if (!$product_info) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'Produk tidak ditemukan.']));
            return;
        }
        $satuan_id = $product_info->satuan_id;
        $inputs['kategori'] = $product_info->kategori_id;

        // Logic Penentuan Harga - Universal
        $pricing_qty = $this->pricing_quantity($inputs, $satuan_id);

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

        log_message('debug', "ADD_TEMP_DEBUG: product=" . $inputs["product"] . " qty=" . $pricing_qty . " P=" . $inputs["panjang"] . " L=" . $inputs["lebar"] . " ket1=" . $inputs["ket_1"] . " jp=" . $inputs["jenisprint_id"] . " | DB rows=" . $hargaQuery->num_rows());
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
                $machine_map[$m->id] = $m->mesin_nama;
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
                    if (!is_scalar($val)) { $metadata[$key] = $val; continue; }
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

            $subtotal = $this->priced_subtotal($cekhargaproduct, $pricing_qty);
            $metadata = $this->build_price_snapshot_metadata($metadata, $cekhargaproduct['harga_1'] ?? 0, $subtotal);

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

        if (!$this->validate_item_inputs($inputs) || !$this->validate_item_spk($inputs['nospk'], false)) { return; }

        // Ensure dynamic pricing keys match schema definitions
        $this->_resolve_dynamic_pricing_keys($inputs['product'], $inputs);

        // print_r($inputs);

        $product_info = $this->db->select('satuan_id, kategori_id')->get_where('tbl_product', ['product_id' => $inputs['product']])->row();
        if (!$product_info) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'Produk tidak ditemukan.']));
            return;
        }
        $satuan_id = $product_info->satuan_id;
        $inputs['kategori'] = $product_info->kategori_id;

        // Logic Penentuan Harga - Universal
        $pricing_qty = $this->pricing_quantity($inputs, $satuan_id);

        // Capture Dynamic Metadata for Edit Mode
        $standard_keys = ['product_id', 'nospk', 'qty', 'panjang', 'lebar', 'kategori_id', 'keterangan', 'approve', 'lokasi_cetak'];
        $metadata = [];

        $hargaQuery = $this->M_transaksi->get_product_price($inputs['product'], $pricing_qty, [
            'ket_1' => $inputs['ket_1'],
            'jenisprint_id' => $inputs['jenisprint_id']
        ]);

        log_message('debug', "ADD_TEMP_DEBUG: product=" . $inputs["product"] . " qty=" . $pricing_qty . " P=" . $inputs["panjang"] . " L=" . $inputs["lebar"] . " ket1=" . $inputs["ket_1"] . " jp=" . $inputs["jenisprint_id"] . " | DB rows=" . $hargaQuery->num_rows());
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
                $machine_map[$m->id] = $m->mesin_nama;
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
                    if (!is_scalar($val)) { $metadata[$key] = $val; continue; }
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
            $subtotal = $this->priced_subtotal($cekhargaproduct, $pricing_qty);

            $metadata = $this->build_price_snapshot_metadata($metadata, $harga_satuan, $subtotal);

            $add_temp = [
                'product_id' => $inputs['product'],
                'nospk' => $inputs['nospk'],
                'qty' => $inputs['qty'],
                'harga_id' => $cekhargaproduct['harga_id'],
                'panjang' => $inputs['panjang'],
                'lebar' => $inputs['lebar'],
                'approve' => $inputs['approve'] ?? 0,
                'lokasi_cetak' => $inputs['lokasi_cetak'] ?? $this->session->userdata('perusahaan_id'),
                'keterangan' => $inputs['keterangan'],
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
                log_message('debug', "ADD_PEMESANAN ERROR: Validation failed - $label is empty.");
                $this->session->set_flashdata('error', "$label tidak boleh kosong.");
                redirect('transaksi/createspk');
                return;
            }
        }

        // 3. Fetch Temporary Data
        $detailPemesanan = $this->M_transaksi->tampilTmp($nospk)->result();
        if (empty($detailPemesanan)) {
            log_message('debug', "ADD_PEMESANAN ERROR: No items found in temp table for SPK $nospk");
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
            'perusahaan_id' => $perusahaan_id,
            'status_pembayaran' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ];
        if ($this->db->field_exists('batch_pengiriman', 'tbl_pemesanan')) {
            $dataPemesanan['batch_pengiriman'] = $this->input->post('batch_pengiriman', true) ?: '';
        }

        // 5. Database Transaction
        $this->db->trans_begin();
        try {
            $spk = $this->db->query('SELECT * FROM tbl_spk WHERE nospk = ? FOR UPDATE', [$nospk])->row();
            if (!$spk || ($spk->user_id != $userid && !in_array((int) $this->session->userdata('level'), [1, 17], true))) {
                throw new DomainException('SPK tidak ditemukan atau bukan milik Anda.');
            }
            if (!in_array((int) $this->session->userdata('level'), [1, 17], true) && (int) $spk->perusahaan_id !== (int) $perusahaan_id) {
                throw new DomainException('SPK tidak dimiliki cabang aktif.');
            }
            $perusahaan_id = $dataPemesanan['perusahaan_id'] = $spk->perusahaan_id;
            $detailPemesanan = $this->M_transaksi->tampilTmp($nospk)->result();
            if (!$detailPemesanan) { throw new DomainException('Tidak ada item produk dalam pesanan.'); }
            $total_pemesanan = 0;

            // Check if transaction already exists for this SPK
            $existing = $this->db->get_where('tbl_pemesanan', ['nospk' => $nospk])->row();

            if ($existing && ($existing->status_pembayaran !== 'pending' || $this->db->get_where('tbl_pembayaran', ['nospk' => $nospk])->row())) {
                throw new DomainException('SPK sudah diproses kasir dan tidak dapat ditimpa dari draft.');
            }

            if ($existing) {
                log_message('debug', "LOG PENGECEKAN: SPK $nospk sudah ada. Menjalankan mode UPDATE.");
                // Update Header
                $this->db->where('nospk', $nospk);
                $this->checked_transaction_write($this->db->update('tbl_pemesanan', $dataPemesanan));

                // Clear existing details for re-insertion
                $this->checked_transaction_write($this->db->delete('tbl_pemesanan_detail', ['nospk' => $nospk]));
            } else {
                log_message('debug', "LOG PENGECEKAN: SPK $nospk belum ada. Menjalankan mode INSERT.");
                // Insert Header
                $this->checked_transaction_write($this->db->insert('tbl_pemesanan', $dataPemesanan));
            }

            // Insert/Process Details
            $item_count = 0;
            foreach ($detailPemesanan as $tmp) {
                $harga_satuan = $this->resolve_detail_unit_price($tmp);
                $harga_minimal = (float) ($tmp->harga_minimal ?? 0);
                $snapshot = $this->decode_metadata_array($tmp->metadata ?? null);
                $subtotal = isset($snapshot['subtotal']) && is_numeric($snapshot['subtotal'])
                    ? (float) $snapshot['subtotal']
                    : $this->calculate_snapshot_subtotal($tmp, $harga_satuan, 'temp_qty', 'temp_panjang', 'temp_lebar', $harga_minimal);
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
                ];

                if ($this->db->field_exists('metadata', 'tbl_pemesanan_detail') && !empty($metadata)) {
                    $detail_data['metadata'] = json_encode($metadata);
                }
                if ($this->db->field_exists('keterangan', 'tbl_pemesanan_detail')) {
                    $detail_data['keterangan'] = $tmp->keterangan ?? null;
                }
                if ($this->db->field_exists('unit_price', 'tbl_pemesanan_detail')) {
                    $detail_data['unit_price'] = $harga_satuan;
                }
                if ($this->db->field_exists('subtotal', 'tbl_pemesanan_detail')) {
                    $detail_data['subtotal'] = (int) round($subtotal);
                }

                $this->checked_transaction_write($this->db->insert('tbl_pemesanan_detail', $detail_data));
                $item_count++;
                $total_pemesanan += $subtotal;
            }
            log_message('debug', "LOG PROSES: Berhasil memproses $item_count item untuk SPK $nospk.");

            if ($dataPemesanan['id_jenis_transaksi'] == '2') {
                $this->load->model('M_pembayaran');
                $this->checked_transaction_write($this->M_pembayaran->insertDetailDanProduksi($nospk, $perusahaan_id));
                $this->checked_transaction_write($this->db->update('tbl_pemesanan', ['status_pembayaran' => 'paid'], ['nospk' => $nospk]));
            }
            $this->checked_transaction_write($this->M_transaksi->deleteTmp($nospk));
            $this->checked_transaction_write($this->db->trans_status());
            $this->checked_transaction_write($this->db->trans_commit());
        } catch (Throwable $e) {
            $this->db->trans_rollback();
            log_message('error', 'Save order: ' . $e->getMessage());
            $this->session->set_flashdata('error', $e instanceof DomainException ? $e->getMessage() : 'Gagal memproses pesanan. Perubahan dibatalkan.');
            redirect('transaksi/createspk');
            return;
        }

        // 6. Response & Finalization
        if ($this->db->trans_status() === FALSE) {
            $error = $this->db->error();
            log_message('debug', "ADD_PEMESANAN FATAL: Transaction failed! Message: " . $error['message']);
            $this->session->set_flashdata('error', 'Gagal memproses pemesanan. Database error: ' . $error['message']);
        } else {
            log_activity($userid, $action, $module, $nospk);

            // [AUDIT LOG] Manual entry for explicit tracking
            $this->_log_manual("Input Pemesanan Baru SPK: $nospk", json_encode([
                'konsumen_id' => $konsumen_id,
                'total' => $total_pemesanan,
                'item_count' => $item_count
            ]), 'WRITE');


            // --- KIRIM WA OTOMATIS (BOOKING) ---
            $konsumen = $this->db->get_where('tbl_konsumen', ['konsumen_id' => $konsumen_id])->row();
            if ($konsumen && !empty($konsumen->konsumen_nohp)) {
                $perusahaan_info = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row();
                $nama_toko = ($perusahaan_info) ? $perusahaan_info->nama_perusahaan : 'TGM PRINT';

                $wa_msg = "INVOICE #$nospk\n";
                $wa_msg .= "Pelanggan: " . $konsumen->konsumen_nama . "\n";
                $wa_msg .= "--------------------------------\n";

                foreach ($detailPemesanan as $tmp) {
                    $harga_satuan = $this->resolve_detail_unit_price($tmp);
                    $snapshot = $this->decode_metadata_array($tmp->metadata ?? null);
                    $subtotal = isset($snapshot['subtotal']) && is_numeric($snapshot['subtotal'])
                        ? (float) $snapshot['subtotal']
                        : $this->calculate_snapshot_subtotal($tmp, $harga_satuan, 'temp_qty', 'temp_panjang', 'temp_lebar', $tmp->harga_minimal ?? 0);

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

                log_message('debug', "ADD_PEMESANAN BYPASS: SPK $nospk Marketplace dikirim ke Produksi.");
                $this->session->set_flashdata('success', 'Pesanan Marketplace ' . $nospk . ' berhasil terkirim ke PRODUKSI.');
            } else {
                log_message('debug', "ADD_PEMESANAN SUCCESS: SPK $nospk berhasil dikirim ke kasir.");
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
        $data['getdata'] = $this->M_transaksi->getDetailPemesanan($nospk)->row_array();
        $data['getdetail'] = $this->M_transaksi->getDetail($nospk)->result();
        foreach ($data['getdetail'] as $item) {
            $item->harga_1 = $this->resolve_detail_unit_price($item);
            $item->subtotal = $this->resolve_detail_subtotal($item, $item->harga_1);
        }
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

        try {
            if (!is_array($post) || !is_string($post['nospk'] ?? null) || trim($post['nospk']) === '') {
                throw new DomainException('Nomor SPK tidak valid.');
            }
            $payment_fields = ['cash' => 'bayar_tunai', 'transfer' => 'bayar_transfer', 'edc' => 'bayar_debit',
                'ewallet' => 'bayar_ewallet', 'qris' => 'bayar_qris', 'piutang' => 'bayar_dp'];
            if (!is_string($post['metode'] ?? null) || !isset($payment_fields[$post['metode']])) {
                throw new DomainException('Metode pembayaran tidak valid.');
            }
            foreach ($payment_fields as $method => $field) {
                if ($method !== $post['metode']) { $post[$field] = 0; }
            }
            foreach (['grandtotal', 'diskon', 'ongkir', 'bayar_tunai', 'bayar_transfer', 'bayar_debit', 'bayar_ewallet', 'bayar_dp', 'bayar_qris'] as $field) {
                $value = $post[$field] ?? ($field === 'grandtotal' ? null : 0);
                if ($value === '') { $value = 0; }
                if (!is_scalar($value) || !preg_match('/^(?:Rp\s*)?[0-9]+(?:[.,][0-9]+)*$/i', trim((string) $value))) {
                    throw new DomainException('Nilai pembayaran, diskon, atau ongkir tidak valid.');
                }
            }
            $order = $this->db->get_where('tbl_pemesanan', ['nospk' => $post['nospk']])->row();
            if (!$order) { throw new DomainException('Data SPK tidak ditemukan.'); }
            if (!in_array((int) $this->session->userdata('level'), [1, 17], true) &&
                (int) $order->perusahaan_id !== (int) $this->session->userdata('perusahaan_id')) {
                throw new DomainException('SPK tidak dimiliki cabang aktif.');
            }
            $perusahaan = $order->perusahaan_id;
            $post['konsumen_id'] = $order->konsumen_id;
            $result = $this->M_pembayaran->prosesTransaksi($post, $perusahaan, $userid);
            if (!is_array($result) || !isset($result['status'], $result['message'])) {
                throw new RuntimeException('Respons proses pembayaran tidak valid.');
            }
        } catch (Throwable $e) {
            $this->db->trans_rollback();
            log_message('error', 'Cashier payment: ' . $e->getMessage());
            $this->session->set_flashdata('error', $e instanceof DomainException ? $e->getMessage() : 'Gagal memproses pembayaran.');
            redirect('transaksi/transaksi_belumbayar');
            return;
        }
        if ($result['status'] == 'success') {
            log_activity($userid, $action, $module, $post['nospk']);

            // [AUDIT LOG]
            $this->_log_manual("Kasir: Pembayaran SPK #" . $post['nospk'], json_encode([
                'grandtotal' => $post['grandtotal'],
                'metode' => $post['id_jenis_transaksi'] ?? null
            ]), 'WRITE');


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

    private function validate_manual_input(array $post)
    {
        if (empty($post['konsumen_id']) || !is_array($post['products'] ?? null) || !$post['products']) {
            throw new DomainException('Konsumen dan detail produk wajib diisi.');
        }
        foreach ($post['products'] as $item) {
            if (!is_array($item) || !is_scalar($item['product_id'] ?? null) || (string) $item['product_id'] === '' ||
                !is_numeric($item['jumlah'] ?? null) || !is_finite((float) $item['jumlah']) || (float) $item['jumlah'] <= 0 ||
                !is_numeric($item['harga_satuan'] ?? null) || !is_finite((float) $item['harga_satuan']) || (float) $item['harga_satuan'] < 0 ||
                !is_finite((float) $item['jumlah'] * (float) $item['harga_satuan'])) {
                throw new DomainException('Produk, quantity, atau harga satuan transaksi manual tidak valid.');
            }
        }
        $date = $post['tanggal_transaksi'] ?? null;
        $parsed = is_string($date) ? DateTime::createFromFormat('!Y-m-d', $date) : false;
        if (!$parsed || $parsed->format('Y-m-d') !== $date) { throw new DomainException('Tanggal transaksi tidak valid.'); }
        if (!in_array($post['metode_bayar'] ?? null, ['cash', 'tunai', 'transfer', 'debit', 'kartu_kredit', 'ewallet', 'piutang', 'lainnya'], true)) {
            throw new DomainException('Metode pembayaran tidak valid.');
        }
    }

    public function simpan_manual()
    {
        $this->load->model('Penjualan_manual_model', 'penjualan');
        try {
            $post = $this->input->post();
            $this->validate_manual_input($post);
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
        } catch (Throwable $e) {
            $this->db->trans_rollback();
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
            return;
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
            return;
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
            return;
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
        $nospk = $nospk ?: $this->input->post('nospk', true);
        $started = false;
        $this->output->set_content_type($this->input->is_ajax_request() ? 'application/json' : 'text/html');
        try {
            if (!is_string($nospk) || $nospk === '') { throw new DomainException('No SPK tidak valid.'); }
            $user_id = $this->session->userdata('id');
            $this->checked_transaction_write($this->db->trans_begin()); $started = true;
            $order = $this->db->query('SELECT * FROM tbl_pemesanan WHERE nospk = ? FOR UPDATE', [$nospk])->row();
            if (!$order) { throw new DomainException('Data pemesanan tidak ditemukan.'); }
            if (!in_array((int) $this->session->userdata('level'), [1, 17], true) &&
                ($order->deskprint_id != $user_id || (int) $order->perusahaan_id !== (int) $this->session->userdata('perusahaan_id'))) {
                throw new DomainException('Anda hanya dapat menarik SPK milik Anda pada cabang aktif.');
            }
            if ($order->status_pembayaran !== 'pending' || $this->db->get_where('tbl_pembayaran', ['nospk' => $nospk])->row()) {
                throw new DomainException('SPK sudah diproses kasir dan tidak dapat ditarik kembali.');
            }
            $details = $this->db->get_where('tbl_pemesanan_detail', ['nospk' => $nospk])->result();
            if (!$details) { throw new DomainException('Detail pemesanan tidak ditemukan.'); }
            $this->checked_transaction_write($this->db->delete('tbl_temp_transaksi', ['no_spk' => $nospk]));
            foreach ($details as $item) {
                $metadata = $this->decode_metadata_array($item->metadata ?? null);
                foreach (['unit_price', 'subtotal'] as $field) {
                    if (!isset($metadata[$field]) && isset($item->$field) && is_numeric($item->$field)) {
                        $metadata[$field] = (float) $item->$field;
                    }
                }
                $data = ['product_id' => $item->product_id, 'no_spk' => $nospk, 'temp_qty' => $item->qty,
                    'temp_panjang' => $item->panjang, 'temp_lebar' => $item->lebar, 'harga_id' => $item->harga_id,
                    'approve' => $item->approve, 'lokasi_cetak' => $item->lokasi_cetak,
                    'perusahaan_id' => $order->perusahaan_id, 'user_id' => $user_id,
                    'keterangan' => $item->keterangan ?? null, 'metadata' => $metadata ? json_encode($metadata) : null];
                foreach (['dateline_tgl', 'dateline_jam', 'finishing_id'] as $field) {
                    if ($this->db->field_exists($field, 'tbl_temp_transaksi') && isset($item->$field)) { $data[$field] = $item->$field; }
                }
                if ($this->db->field_exists('temp_diskon', 'tbl_temp_transaksi')) { $data['temp_diskon'] = $item->diskon ?? 0; }
                $this->checked_transaction_write($this->db->insert('tbl_temp_transaksi', $data));
            }
            $this->checked_transaction_write($this->db->delete('tbl_pemesanan_detail', ['nospk' => $nospk]));
            $this->checked_transaction_write($this->db->delete('tbl_pemesanan', ['nospk' => $nospk]));
            $this->checked_transaction_write($this->db->trans_status());
            $this->checked_transaction_write($this->db->trans_commit()); $started = false;
        } catch (Throwable $e) {
            if ($started) { $this->db->trans_rollback(); }
            log_message('error', 'Withdraw order: ' . $e->getMessage());
            $message = $e instanceof DomainException ? $e->getMessage() : 'Gagal menarik SPK. Perubahan dibatalkan.';
            if ($this->input->is_ajax_request()) { echo json_encode(['status' => 'error', 'message' => $message]); return; }
            $this->session->set_flashdata('error', $message);
            redirect('transaksi/transaksi_belumbayar'); return;
        }
        if (function_exists('log_activity')) {
            try { log_activity($user_id, 'Tarik_Balik_SPK', 'Deskprint', $nospk); }
            catch (Throwable $e) { log_message('error', 'Withdrawal audit: ' . $e->getMessage()); }
        }
        $message = 'SPK #' . $nospk . ' berhasil ditarik kembali ke draft.';
        if ($this->input->is_ajax_request()) {
            echo json_encode(['status' => 'success', 'message' => $message, 'redirect' => base_url('transaksi/createspk/' . $nospk)]);
            return;
        }
        $this->session->set_flashdata('success', $message);
        redirect('transaksi/createspk/' . $nospk);
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

        $started = false;
        try {
            if ($userId <= 0 || $perusahaan <= 0 || !is_scalar($metode_pesan) || !ctype_digit((string) $metode_pesan) || (int) $metode_pesan <= 0) {
                throw new DomainException('Petugas, cabang, atau jenis transaksi tidak valid.');
            }
            $this->checked_transaction_write($this->db->trans_begin()); $started = true;
            // Serialize number generation for this user, including the first SPK of a month.
            $user = $this->db->query('SELECT user_id FROM users WHERE user_id = ? FOR UPDATE', [$userId])->row();
            if (!$user) { throw new DomainException('Petugas tidak ditemukan.'); }

            // logic Khusus Marketplace: Buat Konsumen Otomatis (Status: 0 / Inactive untuk memisahkan dari database utama)
            if ($metode_pesan == 2) {
                $m_nama = $this->input->post('m_nama_konsumen', true);
                $m_alamat = $this->input->post('m_alamat_konsumen', true);

                if (!is_string($m_nama) || trim($m_nama) === '') { throw new DomainException('Nama konsumen marketplace wajib diisi.'); }
                $data_m_konsumen = [
                    'konsumen_nama' => $m_nama,
                    'konsumen_alamat' => $m_alamat,
                    'konsumen_nohp' => 'MARKETPLACE',
                    'status' => '0' // Default status
                ];
                $this->checked_transaction_write($this->db->insert('tbl_konsumen', $data_m_konsumen));
                $konsumen = $this->db->insert_id();
            }

            if (!is_scalar($konsumen) || !ctype_digit((string) $konsumen) || (int) $konsumen <= 0) { throw new DomainException('Konsumen wajib dipilih.'); }

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

            $this->checked_transaction_write($this->db->insert('tbl_spk', $addSPK));
            $this->checked_transaction_write($this->db->trans_status());
            $this->checked_transaction_write($this->db->trans_commit()); $started = false;
            $response = ['status' => 'success', 'message' => 'SPK berhasil dibuat', 'nospk' => $newSPK];
        } catch (Throwable $e) {
            if ($started) { $this->db->trans_rollback(); }
            log_message('error', 'Create SPK: ' . $e->getMessage());
            $response = ['status' => 'error', 'message' => $e instanceof DomainException ? $e->getMessage() : 'Gagal membuat SPK. Perubahan dibatalkan.'];
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

    private function checked_transaction_write($result)
    {
        if (!$result) {
            $err = $this->db->error();
            $msg = !empty($err['message']) ? 'Gagal menyimpan database: ' . $err['message'] : 'Gagal menyimpan perubahan database.';
            throw new RuntimeException($msg);
        }
    }

    private function receivable_payment_input()
    {
        $raw_amount = $this->input->post('payment_amount', true);
        $amount = is_numeric($raw_amount) ? round((float) $raw_amount, 2) : 0;
        $consumer = $this->input->post('konsumen_id', true);
        $method = $this->input->post('payment_method', true);
        $methods = ['cash' => 'tunai', 'transfer' => 'transfer', 'qris' => 'ewallet', 'edc' => 'debit'];
        $date = $this->input->post('payment_date', true) ?: date('Y-m-d');
        if (!is_finite($amount) || $amount <= 0 || !is_scalar($consumer) ||
            !ctype_digit((string) $consumer) || (int) $consumer <= 0 || !is_string($method) || !isset($methods[$method])) {
            throw new DomainException('Konsumen, jumlah, atau metode pembayaran tidak valid.');
        }
        $parsed = is_string($date) ? DateTime::createFromFormat('!Y-m-d', $date) : false;
        if (!$parsed || $parsed->format('Y-m-d') !== $date) {
            throw new DomainException('Tanggal pembayaran tidak valid.');
        }
        $data = ['amount' => $amount, 'consumer' => (int) $consumer, 'method' => $methods[$method],
            'date' => $date . ' ' . date('H:i:s'), 'notes' => $this->input->post('payment_notes', true) ?? '',
            'bank_id' => null, 'edc_id' => null, 'ewallet_id' => null];
        foreach (['transfer' => ['bank_id', 'bank_id', 'tbl_rekening', 'id'], 'debit' => ['edc_id', 'edc_id', 'tbl_mesin_edc', 'edc_id'], 'ewallet' => ['ewallet_id', 'wallet_id', 'tbl_ewallet', 'id']] as $db_method => $fields) {
            if ($data['method'] !== $db_method) { continue; }
            $id = $this->input->post($fields[1], true);
            if (!is_scalar($id) || !ctype_digit((string) $id) || (int) $id <= 0) {
                throw new DomainException('Pilih rekening, mesin EDC, atau e-wallet sesuai metode pembayaran.');
            }
            if (!$this->db->get_where($fields[2], [$fields[3] => $id])->row()) {
                throw new DomainException('Rekening, mesin EDC, atau e-wallet tidak ditemukan.');
            }
            $data[$fields[0]] = (int) $id;
        }
        if (!is_string($data['notes'])) { throw new DomainException('Catatan pembayaran tidak valid.'); }
        return $data;
    }

    private function locked_receivables(array $payment, $nospk = null)
    {
        $this->db->select('p.*')->from('tbl_pembayaran p')
            ->join('tbl_pemesanan pm', 'pm.nospk = p.nospk')->where('pm.konsumen_id', $payment['consumer']);
        if (!in_array((int) $this->session->userdata('level'), [1, 17], true)) {
            $branch = (int) $this->session->userdata('perusahaan_id');
            if ($branch <= 0) { throw new DomainException('Cabang aktif tidak ditemukan.'); }
            $this->db->where('pm.perusahaan_id', $branch)->where('p.perusahaan_id', $branch);
        }
        if ($nospk !== null) { $this->db->where('p.nospk', $nospk); }
        else { $this->db->where('(p.total_tagihan - p.total_bayar) >', 0); }
        $sql = $this->db->order_by('pm.created_at', 'ASC')->order_by('p.pembayaran_id', 'ASC')->get_compiled_select();
        $result = $this->db->query($sql . ' FOR UPDATE');
        if (!$result) { throw new RuntimeException('Gagal membaca data pembayaran.'); }
        $rows = $result->result();
        $seen = [];
        foreach ($rows as $row) {
            if (isset($seen[$row->nospk])) { throw new DomainException('Terdapat data pembayaran ganda untuk SPK ini. Periksa data sebelum melanjutkan.'); }
            $seen[$row->nospk] = true;
        }
        return $rows;
    }

    private function apply_receivable_payment($debt, $amount, array $payment, $collective = false)
    {
        $new_total = round((float) $debt->total_bayar + $amount, 2);
        $paid = $new_total >= round((float) $debt->total_tagihan, 2);
        $this->checked_transaction_write($this->db->update('tbl_pembayaran', [
            'total_bayar' => $new_total, 'status_lunas' => $paid ? '1' : '0', 'updated_at' => date('Y-m-d H:i:s')
        ], ['pembayaran_id' => $debt->pembayaran_id]));
        if ($paid) {
            $this->checked_transaction_write($this->db->update('tbl_pemesanan', ['status_pembayaran' => 'paid'], ['nospk' => $debt->nospk]));
        }
        $notes = $payment['notes'] . ($collective ? ' (Bayar Kolektif)' : '');
        $this->checked_transaction_write($this->db->insert('tbl_pembayaran_detail', [
            'pembayaran_id' => $debt->pembayaran_id, 'metode' => $payment['method'], 'jumlah' => $amount,
            'bank_id' => $payment['bank_id'], 'edc_id' => $payment['edc_id'], 'ewallet_id' => $payment['ewallet_id'],
            'keterangan' => $notes, 'created_at' => $payment['date']
        ]));
        $piutang = $this->db->get_where('tbl_piutang', ['nospk' => $debt->nospk])->row();
        if ($piutang) {
            $pi_total = round((float) $piutang->total_bayar + $amount, 2);
            $this->checked_transaction_write($this->db->update('tbl_piutang', [
                'total_bayar' => $pi_total, 'status' => $pi_total >= round((float) $piutang->total_tagihan, 2) ? 'LUNAS' : 'SEBAGIAN',
                'updated_at' => date('Y-m-d H:i:s')
            ], ['piutang_id' => $piutang->piutang_id]));
            $this->checked_transaction_write($this->db->insert('tbl_piutang_pembayaran', [
                'piutang_id' => $piutang->piutang_id, 'tanggal_bayar' => $payment['date'], 'jumlah_bayar' => $amount,
                'metode_bayar' => $payment['method'], 'rekening_id' => $payment['bank_id'] ?? 0,
                'edc_id' => $payment['edc_id'] ?? 0, 'ewallet_id' => $payment['ewallet_id'] ?? 0, 'keterangan' => $notes
            ]));
        }
    }

    private function process_receivable_payment($collective)
    {
        $this->output->set_content_type('application/json');
        if (!$this->input->is_ajax_request()) {
            $this->output->set_status_header(400);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }
        $started = false;
        try {
            $payment = $this->receivable_payment_input();
            $nospk = $collective ? null : $this->input->post('nospk', true);
            if (!$collective && (!is_string($nospk) || trim($nospk) === '')) {
                throw new DomainException('Nomor SPK wajib diisi.');
            }
            $this->checked_transaction_write($this->db->trans_begin());
            $started = true;
            $debts = $this->locked_receivables($payment, $nospk);
            if (!$debts) { throw new DomainException('Data piutang tidak ditemukan untuk konsumen dan cabang ini.'); }
            $available = 0;
            foreach ($debts as $debt) { $available += max(0, round((float) $debt->total_tagihan - (float) $debt->total_bayar, 2)); }
            $available = round($available, 2);
            if ($payment['amount'] > $available) { throw new DomainException('Jumlah pembayaran melebihi sisa piutang.'); }
            $remaining = $payment['amount'];
            foreach ($debts as $debt) {
                if ($remaining <= 0) { break; }
                $due = max(0, round((float) $debt->total_tagihan - (float) $debt->total_bayar, 2));
                $amount = min($remaining, $due);
                if ($amount <= 0) { continue; }
                $this->apply_receivable_payment($debt, $amount, $payment, $collective);
                $remaining = round($remaining - $amount, 2);
            }
            $this->checked_transaction_write($this->db->trans_status());
            $this->checked_transaction_write($this->db->trans_commit());
            $started = false;
        } catch (Throwable $e) {
            if ($started) { $this->db->trans_rollback(); }
            log_message('error', 'Receivable payment: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e instanceof DomainException ? $e->getMessage() : 'Gagal memproses pembayaran. Perubahan dibatalkan.']);
            return;
        }
        if (function_exists('log_activity')) {
            try { log_activity($this->session->userdata('id'), $collective ? 'Collective_Payment_Processed' : 'Payment_Processed', 'Piutang', $nospk ?? (string) $payment['consumer']); }
            catch (Throwable $e) { log_message('error', 'Payment audit: ' . $e->getMessage()); }
        }
        echo json_encode(['success' => true, 'message' => $collective ? 'Pembayaran kolektif berhasil diproses.' : 'Pembayaran berhasil diproses untuk SPK: ' . $nospk]);
    }

    public function process_payment()
    {
        return $this->process_receivable_payment(false);
    }

    public function process_payment_collective()
    {
        return $this->process_receivable_payment(true);
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

    private function shift_report_branch($branch = null)
    {
        $active = (int) $this->session->userdata('perusahaan_id');
        if (!in_array((int) $this->session->userdata('level'), [1, 17], true)) { return $active; }
        return is_scalar($branch) && ctype_digit((string) $branch) && (int) $branch > 0 ? (int) $branch : $active;
    }

    private function shift_report_rows($branch, $id = null, array $filters = [])
    {
        $this->db->select("ks.*, ks.shift_id as id, ks.user_id as kasir_id, ks.jam_open as tanggal_mulai,
            ks.jam_close as tanggal_selesai, u.perusahaan_id1 as perusahaan_id,
            COALESCE(k.nama_lengkap, u.username) as nama_karyawan, k.foto, k.email, k.telepon, p.nama_perusahaan,
            CASE WHEN ks.status = 'open' THEN 'aktif' ELSE 'selesai' END as status", false)
            ->from('kasir_shift ks')->join('users u', 'u.user_id = ks.user_id')
            ->join('tbl_karyawan k', 'k.karyawan_id = u.karyawan_id', 'left')
            ->join('tbl_perusahaan p', 'p.id_perusahaan = u.perusahaan_id1', 'left')
            ->where('u.perusahaan_id1', $branch);
        if ($id !== null) { $this->db->where('ks.shift_id', $id); }
        if (!empty($filters['kasir_id'])) { $this->db->where('ks.user_id', $filters['kasir_id']); }
        if (!empty($filters['status'])) {
            $statuses = ['aktif' => 'open', 'selesai' => 'closed', 'open' => 'open', 'closed' => 'closed'];
            $this->db->where('ks.status', $statuses[$filters['status']] ?? 'invalid');
        }
        if (!empty($filters['tanggal_mulai'])) { $this->db->where('ks.tanggal_shift >=', $filters['tanggal_mulai']); }
        if (!empty($filters['tanggal_selesai'])) { $this->db->where('ks.tanggal_shift <=', $filters['tanggal_selesai']); }
        return $this->db->order_by('ks.shift_id', 'DESC')->get()->result();
    }

    private function fill_shift_report($shift, $details = false)
    {
        // Match full datetimes so a shift spanning midnight includes both days.
        $this->db->where('kasir_id', $shift->kasir_id)->where('perusahaan_id', $shift->perusahaan_id)
            ->where("CONCAT(tgl_bayar, ' ', jam_bayar) >=", $shift->tanggal_mulai);
        if ($shift->tanggal_selesai) { $this->db->where("CONCAT(tgl_bayar, ' ', jam_bayar) <=", $shift->tanggal_selesai); }
        $payments = $this->db->get('tbl_pembayaran')->result();
        $expenses = $this->db->where('shift_id', $shift->id)->where('perusahaan_id', $shift->perusahaan_id)
            ->get('tbl_pengeluaran')->result();
        $this->db->distinct()->select('pi.*')->from('tbl_piutang pi')->join('tbl_pembayaran pb', 'pb.nospk = pi.nospk')
            ->where('pb.kasir_id', $shift->kasir_id)->where('pi.perusahaan_id', $shift->perusahaan_id)
            ->where("CONCAT(pb.tgl_bayar, ' ', pb.jam_bayar) >=", $shift->tanggal_mulai);
        if ($shift->tanggal_selesai) { $this->db->where("CONCAT(pb.tgl_bayar, ' ', pb.jam_bayar) <=", $shift->tanggal_selesai); }
        $receivables = $this->db->get()->result();
        $shift->total_transaksi = count($payments);
        $shift->total_pendapatan = array_sum(array_column($payments, 'total_bayar'));
        $shift->total_pengeluaran = array_sum(array_column($expenses, 'pengeluaran_jumlah'));
        $shift->total_piutang = 0;
        foreach ($receivables as $row) { $shift->total_piutang += max(0, (float) $row->total_tagihan - (float) $row->total_bayar); }
        $shift->saldo_kasir = $shift->total_pendapatan - $shift->total_pengeluaran;
        if ($details) {
            $shift->transaksi_pembayaran = $payments;
            $shift->transaksi_pengeluaran = $expenses;
            $shift->transaksi_piutang = $receivables;
        }
        return $shift;
    }

    public function get_kasir_shift()
    {
        return $this->shift_report_rows($this->shift_report_branch());
    }

    public function get_kasir_shift_report($perusahaan_id = null)
    {
        $rows = $this->shift_report_rows($this->shift_report_branch($perusahaan_id));
        foreach ($rows as $row) { $this->fill_shift_report($row); }
        return $rows;
    }

    public function get_kasir_shift_detail($shift_id)
    {
        $rows = $this->shift_report_rows($this->shift_report_branch(), $shift_id);
        return $rows ? $this->fill_shift_report($rows[0], true) : null;
    }

    public function get_kasir_summary($perusahaan_id = null)
    {
        $branch = $this->shift_report_branch($perusahaan_id);
        $rows = $this->shift_report_rows($branch);
        $active = []; $today = 0;
        foreach ($rows as $row) {
            if ($row->status === 'aktif') { $active[$row->kasir_id] = true; }
            if (substr($row->tanggal_mulai, 0, 10) === date('Y-m-d')) { $today++; }
        }
        $payments = $this->db->where('perusahaan_id', $branch)->where('tgl_bayar', date('Y-m-d'))
            ->get('tbl_pembayaran')->result();
        $expenses = $this->db->where('perusahaan_id', $branch)->where('pengeluaran_tgl', date('Y-m-d'))
            ->get('tbl_pengeluaran')->result();
        $income = array_sum(array_column($payments, 'total_bayar'));
        $expense = array_sum(array_column($expenses, 'pengeluaran_jumlah'));
        return ['total_kasir_aktif' => count($active), 'total_shift_hari_ini' => $today,
            'total_pendapatan_hari_ini' => $income, 'total_pengeluaran_hari_ini' => $expense, 'saldo_kas_hari_ini' => $income - $expense];
    }

    public function get_all_kasir_shift_admin($perusahaan_id = null, $filters = [])
    {
        if (!in_array((int) $this->session->userdata('level'), [1, 17], true)) { return false; }
        $rows = $this->shift_report_rows($this->shift_report_branch($perusahaan_id), null, is_array($filters) ? $filters : []);
        foreach ($rows as $row) { $this->fill_shift_report($row); }
        return $rows;
    }

    public function get_kasir_shift_rekap($perusahaan_id = null, $periode = 'bulan')
    {
        $rows = $this->get_kasir_shift_report($perusahaan_id);
        $periods = []; $cashiers = []; $period_cashiers = [];
        foreach ($rows as $row) {
            $timestamp = strtotime($row->tanggal_mulai);
            $period = date($periode === 'hari' ? 'Y-m-d' : ($periode === 'minggu' ? 'o-W' : 'Y-m'), $timestamp);
            if (!isset($periods[$period])) {
                $periods[$period] = ['periode' => $period, 'total_kasir' => 0, 'total_shift' => 0, 'shift_aktif' => 0, 'shift_selesai' => 0, 'shift_batal' => 0];
            }
            $periods[$period]['total_shift']++;
            $periods[$period][$row->status === 'aktif' ? 'shift_aktif' : 'shift_selesai']++;
            $period_cashiers[$period][$row->kasir_id] = true;
            if (!isset($cashiers[$row->kasir_id])) {
                $cashiers[$row->kasir_id] = ['kasir_id' => $row->kasir_id, 'nama_karyawan' => $row->nama_karyawan, 'foto' => $row->foto,
                    'total_shift' => 0, 'shift_aktif' => 0, 'shift_selesai' => 0, 'shift_batal' => 0,
                    'total_pendapatan' => 0, 'total_pengeluaran' => 0, 'total_piutang' => 0];
            }
            $cashiers[$row->kasir_id]['total_shift']++;
            $cashiers[$row->kasir_id][$row->status === 'aktif' ? 'shift_aktif' : 'shift_selesai']++;
            foreach (['total_pendapatan', 'total_pengeluaran', 'total_piutang'] as $field) { $cashiers[$row->kasir_id][$field] += $row->$field; }
        }
        foreach ($periods as $key => &$period) { $period['total_kasir'] = count($period_cashiers[$key]); }
        unset($period); krsort($periods);
        return ['per_periode' => array_map(function ($row) { return (object) $row; }, array_values($periods)),
            'per_kasir' => array_map(function ($row) { return (object) $row; }, array_values($cashiers))];
    }

    // Fungsi untuk export laporan kasir shift
    function export_kasir_shift_report($perusahaan_id = null, $format = 'excel')
    {
        if (!$perusahaan_id) {
            $perusahaan_id = $this->session->userdata('perusahaan_id');
        }

        // Check role access
        $role_id = (int) $this->session->userdata('level');
        if (!in_array($role_id, [1, 17], true)) { // 1 = Administrator, 2 = Owner
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
        if (!in_array((int) $this->session->userdata('level'), [1, 17], true)) {
            show_404(); return;
        }
        redirect('kasir_shift');
    }

    // Method untuk API get all kasir shift admin (POST method)
    // public function get_all_kasir_shift_admin() {
    //     // Check role access
    //     $role_id = (int) $this->session->userdata('level');
    //     if (!in_array($role_id, [1, 17], true)) { // 1 = Administrator, 2 = Owner
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
        $role_id = (int) $this->session->userdata('level');
        if (!in_array($role_id, [1, 17], true)) { // 1 = Administrator, 2 = Owner
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
        $role_id = (int) $this->session->userdata('level');
        if (!in_array($role_id, [1, 17], true)) { // 1 = Administrator, 2 = Owner
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
            return;
        }

        $transaksi = $this->manual_sales->get_transaksi_by_nospk($nospk);
        if (!$transaksi) {
            $this->session->set_flashdata('error', 'Transaksi tidak ditemukan.');
            redirect('penjualan/manual_list');
            return;
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
        if (empty($products) || !is_array($products)) {
            echo json_encode(['status' => 'error', 'message' => 'Detail produk tidak boleh kosong']);
            return;
        }

        try {
            $post = $this->input->post();
            $this->validate_manual_input($post);
            if (!is_string($nospk) || strpos($nospk, 'MAN-') !== 0) { throw new DomainException('SPK ini bukan transaksi manual.'); }
            $this->db->trans_begin();
            $order = $this->db->query('SELECT * FROM tbl_pemesanan WHERE nospk = ? FOR UPDATE', [$nospk])->row();
            if (!$order) { throw new DomainException('Transaksi tidak ditemukan.'); }
            if (!in_array((int) $this->session->userdata('level'), [1, 17], true) &&
                (int) $order->perusahaan_id !== (int) $this->session->userdata('perusahaan_id')) {
                throw new DomainException('Transaksi tidak dimiliki cabang aktif.');
            }
            $existing_piutang = $this->db->get_where('tbl_piutang', ['nospk' => $nospk])->row();
            if ($existing_piutang && $this->db->get_where('tbl_piutang_pembayaran', ['piutang_id' => $existing_piutang->piutang_id])->num_rows() > 0) {
                throw new DomainException('Transaksi sudah memiliki cicilan. Gunakan penyesuaian piutang agar riwayat pembayaran tetap tersimpan.');
            }

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
                    'perusahaan_id' => $order->perusahaan_id,
                    'total_tagihan' => $total_tagihan,
                    'total_bayar' => 0,
                    'jatuh_tempo' => date('Y-m-d', strtotime($post['tanggal_transaksi'] . " +{$termin} days")),
                    'status' => 'BELUM LUNAS'
                ]);
            }

            $this->checked_transaction_write($this->db->trans_status());
            $this->checked_transaction_write($this->db->trans_commit());
            echo json_encode(['status' => 'success', 'message' => 'Transaksi berhasil diperbarui!']);

        } catch (Throwable $e) {
            $this->db->trans_rollback();
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui: ' . $e->getMessage()]);
        }
    }
}



?>
