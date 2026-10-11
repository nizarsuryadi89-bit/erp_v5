<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Manufaktur extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_master');
        $this->load->model('M_gudang');
    }

    public function get_common_data($title = '')
    {
        $this->data['title'] = $title;
        return $this->data;
    }

    public function index()
    {
        $data = $this->get_common_data('Daftar Semua Product');

        // 1. Capture Filter & Pagination Parameters
        $q = $this->input->get('q', TRUE);
        $kategori = $this->input->get('kategori', TRUE);
        $status = $this->input->get('status', TRUE); // complete, partial, empty
        $starts_with = strtoupper(trim((string) $this->input->get('starts_with', TRUE)));
        $sort = strtolower(trim((string) $this->input->get('sort', TRUE)));
        $orderby = strtolower(trim((string) $this->input->get('orderby', TRUE)));
        $page = (int) $this->input->get('page', TRUE) ?: 1;
        $per_page = (int) $this->input->get('per_page', TRUE) ?: 20;

        if (!in_array($per_page, [20, 50, 100, 200], true)) {
            $per_page = 20;
        }
        if (!in_array($sort, ['asc', 'desc'], true)) {
            $sort = 'asc';
        }

        // 2. Prepare Filters for Query
        $filters = [
            'q' => $q,
            'kategori' => $kategori,
            'metode' => $this->input->get('metode', true),
            'status' => $status,
            'starts_with' => $starts_with,
            'sort' => $sort,
            'orderby' => $orderby
        ];

        // 3. Get Total Data (for Pagination)
        // We use the same model function but without limit/offset to get count
        // Note: For better performance, a dedicated count function is recommended, but we re-use logic here
        $queryTotal = $this->M_master->getDataProductWithPagination(null, null, $filters);

        // Filter by Status (PHP side filtering for complex status logic if not in SQL)
        // Adjust this if status logic moves to SQL. Currently M_master doesn't support 'status' filter in SQL directly easily without complex subqueries
        // To support pagination with PHP filtering, we'd need to fetch ALL and filter, which is heavy. 
        // For now, assuming SQL handles basic filters. If status is needed, it might be better to handle in SQL.
        // Let's rely on standard filters for now. If status filtering is critical and missing in SQL, we might need to add it to M_master.

        $total_products = $queryTotal->num_rows();

        // 4. Calculate Complete/Partial/Harga Ready Stats
        $stats = $this->db->query("
            SELECT 
                COUNT(p.product_id) as total,
                SUM(CASE WHEN EXISTS (SELECT 1 FROM tbl_harga_product h WHERE h.product_id = p.product_id) AND EXISTS (SELECT 1 FROM bom_lines b WHERE b.product_id = p.product_id) AND EXISTS (SELECT 1 FROM routing r WHERE r.product_id = p.product_id) THEN 1 ELSE 0 END) as complete,
                SUM(CASE WHEN (EXISTS (SELECT 1 FROM tbl_harga_product h WHERE h.product_id = p.product_id) OR EXISTS (SELECT 1 FROM bom_lines b WHERE b.product_id = p.product_id) OR EXISTS (SELECT 1 FROM routing r WHERE r.product_id = p.product_id)) AND NOT (EXISTS (SELECT 1 FROM tbl_harga_product h WHERE h.product_id = p.product_id) AND EXISTS (SELECT 1 FROM bom_lines b WHERE b.product_id = p.product_id) AND EXISTS (SELECT 1 FROM routing r WHERE r.product_id = p.product_id)) THEN 1 ELSE 0 END) as partial,
                SUM(CASE WHEN EXISTS (SELECT 1 FROM tbl_harga_product h WHERE h.product_id = p.product_id) THEN 1 ELSE 0 END) as harga_ready 
            FROM tbl_product p;
        ")->row();

        // 5. Calculate Pagination
        $total_pages = ceil($total_products / $per_page);
        $page = max(1, min($page, $total_pages)); // Ensure page is valid
        $offset = ($page - 1) * $per_page;
        if ($offset < 0)
            $offset = 0;

        // 6. Get Paginated Data
        $data['tampilData'] = $this->M_master->getDataProductWithPagination($per_page, $offset, $filters)->result();

        // 7. Pass Data to View
        $data['total_products'] = $total_products;
        $data['produk_complete'] = $stats->complete ?? 0;
        $data['produk_partial'] = $stats->partial ?? 0;
        $data['produk_harga_ready'] = $stats->harga_ready ?? 0;
        $data['current_page'] = $page;
        $data['total_pages'] = $total_pages;
        $data['per_page'] = $per_page;
        $data['offset'] = $offset;
        $data['current_filters'] = [
            'q' => $q,
            'kategori' => $kategori,
            'status' => $status,
            'starts_with' => $starts_with,
            'sort' => $sort,
            'orderby' => $orderby,
            'per_page' => $per_page
        ];
        $data['alphabet_filters'] = array_merge(range('A', 'Z'), ['0-9']);

        // Populate dropdowns using existing get_common_data or fetch new
        $data['kategori'] = $this->db->get('tbl_kategori')->result();
        $data['subkategori'] = $this->db->get('tbl_subkategori')->result();
        $data['metode'] = $this->db->get('tbl_metode')->result();

        // Helper arrays for view
        $data['tipe'] = $this->db->get('tbl_type')->result();
        $data['satuan'] = $this->db->get('tbl_satuan')->result();
        $data['sisi'] = $this->db->get('tbl_side')->result();

        $this->load_admin_view('admin/product/index', $data);
    }

    public function catalog()
    {
        $data = $this->get_common_data('Katalog Harga Produk');

        $q = $this->input->get('q', TRUE);
        $kategori = $this->input->get('kategori_id', TRUE);
        $page = (int) $this->input->get('page', TRUE) ?: 1;
        $per_page = 12; // 12 products per page

        // 1. Get total products for calculation
        $this->db->from('tbl_product p');
        if ($q)
            $this->db->like('p.product_nama', $q);
        if ($kategori)
            $this->db->where('p.kategori_id', $kategori);
        $total_products = $this->db->count_all_results();

        $total_pages = ceil($total_products / $per_page);
        $page = max(1, min($page, $total_pages));
        $offset = ($page - 1) * $per_page;
        if ($offset < 0)
            $offset = 0;

        // 2. Fetch paginated products
        $this->db->select('p.*, k.kategori_nama, s.subkategori_nama, st.satuan_nama');
        $this->db->from('tbl_product p');
        $this->db->join('tbl_kategori k', 'p.kategori_id = k.kategori_id', 'left');
        $this->db->join('tbl_subkategori s', 'p.subkategori_id = s.subkategori_id', 'left');
        $this->db->join('tbl_satuan st', 'p.satuan_id = st.satuan_id', 'left');

        if ($q)
            $this->db->like('p.product_nama', $q);
        if ($kategori)
            $this->db->where('p.kategori_id', $kategori);

        $this->db->order_by('k.kategori_nama', 'ASC');
        $this->db->order_by('p.product_nama', 'ASC');
        $this->db->limit($per_page, $offset);

        $products = $this->db->get()->result_array();

        foreach ($products as &$p) {
            $this->db->select('hp.*, s.side_name as ket_1_label, mp.mesin_nama as jenisprint_label');
            $this->db->from('tbl_harga_product hp');
            $this->db->join('tbl_side s', 'hp.ket_1 = s.side_id', 'left');
            $this->db->join('tbl_mesin_printing mp', 'hp.jenisprint_id = mp.id', 'left');
            $this->db->where('hp.product_id', $p['product_id']);
            $this->db->order_by('hp.qty_min', 'ASC');
            $p['prices'] = $this->db->get()->result_array();

            $this->db->select('MIN(harga_1) as min_p, MAX(harga_1) as max_p');
            $this->db->where('product_id', $p['product_id']);
            $price_sum = $this->db->get('tbl_harga_product')->row();
            $p['min_price'] = $price_sum->min_p;
            $p['max_price'] = $price_sum->max_p;
        }

        $data['products'] = $products;
        $data['total_products'] = $total_products;
        $data['current_page'] = $page;
        $data['total_pages'] = $total_pages;
        $data['kategori_list'] = $this->db->get('tbl_kategori')->result();
        $data['q'] = $q;
        $data['kategori_id'] = $kategori;

        $this->load_admin_view('admin/product/catalog', $data);
    }

    public function catalog_print()
    {
        $data = $this->get_common_data('Katalog Harga Produk LENGKAP');

        $q = $this->input->get('q', TRUE);
        $kategori = $this->input->get('kategori_id', TRUE);

        $this->db->select('p.*, k.kategori_nama, s.subkategori_nama, st.satuan_nama');
        $this->db->from('tbl_product p');
        $this->db->join('tbl_kategori k', 'p.kategori_id = k.kategori_id', 'left');
        $this->db->join('tbl_subkategori s', 'p.subkategori_id = s.subkategori_id', 'left');
        $this->db->join('tbl_satuan st', 'p.satuan_id = st.satuan_id', 'left');

        if ($q)
            $this->db->like('p.product_nama', $q);
        if ($kategori)
            $this->db->where('p.kategori_id', $kategori);

        $this->db->order_by('k.kategori_nama', 'ASC');
        $this->db->order_by('p.product_nama', 'ASC');

        $products = $this->db->get()->result_array();

        foreach ($products as &$p) {
            $this->db->select('hp.*, s.side_name as ket_1_label, mp.mesin_nama as jenisprint_label');
            $this->db->from('tbl_harga_product hp');
            $this->db->join('tbl_side s', 'hp.ket_1 = s.side_id', 'left');
            $this->db->join('tbl_mesin_printing mp', 'hp.jenisprint_id = mp.id', 'left');
            $this->db->where('hp.product_id', $p['product_id']);
            $this->db->order_by('hp.qty_min', 'ASC');
            $p['prices'] = $this->db->get()->result_array();

            $this->db->select('MIN(harga_1) as min_p, MAX(harga_1) as max_p');
            $this->db->where('product_id', $p['product_id']);
            $price_sum = $this->db->get('tbl_harga_product')->row();
            $p['min_price'] = $price_sum->min_p;
            $p['max_price'] = $price_sum->max_p;
        }

        $data['products'] = $products;
        $data['perusahaan'] = $this->db->get('tbl_perusahaan')->row_array();

        // Load specific print view
        $this->load->view('admin/product/catalog_print', $data);
    }

    public function actionAdd()
    {
        $kategori_id = $this->input->post('kategori_id', true);
        $subkategori_id = $this->input->post('subkategori_id', true);
        $product_nama = $this->input->post('product_nama', true);
        $tipe_id = $this->input->post('tipe_id', true);
        $satuan_id = $this->input->post('satuan_id', true);
        $side_id = $this->input->post('side_id', true);
        $metode_id = $this->input->post('metode_id', true);
        $product_deskripsi = $this->input->post('product_deskripsi', true);

        $data = array(
            'kategori_id' => $kategori_id,
            'subkategori_id' => $subkategori_id,
            'product_nama' => $product_nama,
            'tipe_id' => $tipe_id,
            'satuan_id' => $satuan_id,
            'side_id' => $side_id,
            'metode_id' => $metode_id,
            'product_deskripsi' => $product_deskripsi,
            'created_at' => date('Y-m-d H:i:s')
        );

        $this->db->insert('tbl_product', $data);
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Data Product Berhasil Ditambahkan</div>');
        redirect('manufaktur');
    }

    public function routing()
    {
        $kode = $this->uri->segment(3);
        $data = $this->get_common_data('Daftar Routing Manufacturing Product');

        $data['table'] = 'tbl_kategori';
        $data['product'] = $this->M_master->getproductorderby('product_nama')->result();
        $data['total_rows'] = $this->db->get('routing')->num_rows();
        $data['operator_list'] = $this->db->get('tbl_operator')->result();
        $data['routing_list'] = $this->db->get('tbl_routing')->result();
        $data['bom'] = $this->M_master->getbom('bom_line_id')->result();

        if ($kode != '') {
            $getproduct = $this->db->get_where('tbl_product', ['product_id' => $kode])->row_array();
            $data['getproduct'] = $getproduct;

            $data['tampilData'] = $this->db->select('routing.*, tbl_product.product_nama, tbl_operator.*')
                ->from('routing')
                ->join('tbl_product', 'routing.product_id = tbl_product.product_id')
                ->join('tbl_operator', 'routing.operator_id = tbl_operator.operator_id', 'left')
                ->where('routing.product_id', $kode)
                ->order_by('routing.routing_urutan', 'ASC')
                ->get()->result();
            $data['total_rows'] = count($data['tampilData']);
        } else {
            $data['getproduct'] = ['product_nama' => 'Pilih Product', 'product_id' => ''];
            $data['tampilData'] = [];
            $data['total_rows'] = 0;
        }

        $this->load_admin_view('admin/manufaktur/routing', $data);
    }

    public function bom()
    {
        $kode = $this->uri->segment(3);
        $data = $this->get_common_data('Daftar Bill Of Material');

        $data['table'] = 'tbl_kategori';
        $data['product'] = $this->M_master->getproductorderby('product_nama')->result();
        $data['total_rows'] = 0;
        $data['tampilData'] = [];
        $data['routing'] = $this->db->get('routing')->result();
        $data['satuan'] = $this->db->get('tbl_satuan')->result();
        $data['bahan'] = $this->M_master->getDataBahan('')->result();

        if ($kode != '') {
            $getproduct = $this->db->get_where('tbl_product', ['product_id' => $kode])->row_array();
            $data['getproduct'] = $getproduct;
            $data['tampilData'] = $this->M_master->getbomkode('product_id', $kode)->result();
            $data['total_rows'] = $this->M_master->getbomkode('product_id', $kode)->num_rows();
        } else {
            $data['getproduct'] = ['product_nama' => 'Pilih Product', 'product_id' => ''];
        }

        $this->load_admin_view('admin/manufaktur/bom', $data);
    }

    public function aksi_simpan_bom()
    {
        $product_id = $this->input->post('product_id', true);
        $bahan_id = $this->input->post('bahan_id', true);
        $quantity = $this->input->post('quantity', true);

        // Auto-fetch satuan_id from tbl_bahan if not provided or hidden
        $satuan_id = $this->input->post('satuan_id', true);
        if (empty($satuan_id) && $bahan_id) {
            $bahan = $this->db->get_where('tbl_bahan', ['bahan_id' => $bahan_id])->row();
            if ($bahan)
                $satuan_id = $bahan->satuan_id;
        }

        if (!$product_id || !$bahan_id || !$quantity) {
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi!']);
                return;
            }
            $this->session->set_flashdata('error', 'Semua field wajib diisi!');
            redirect('manufaktur/bom/' . $product_id);
            return;
        }

        $data = [
            'product_id' => $product_id,
            'bahan_id' => $bahan_id,
            'quantity' => $quantity,
            'satuan_id' => $satuan_id,
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->input->post('ukuran_enable', true)) {
            $panjang = $this->input->post('panjang', true);
            $lebar = $this->input->post('lebar', true);
            if ($panjang && $lebar) {
                $data['panjang'] = $panjang;
                $data['lebar'] = $lebar;
            }
        }

        // Handle Scrap Factor if present
        if ($this->input->post('scrap_factor', true)) {
            $data['scrap_factor'] = $this->input->post('scrap_factor', true);
        }

        try {
            $this->db->trans_start();
            $this->db->insert('bom_lines', $data);
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Gagal menyimpan data BOM');
            }

            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Data BOM berhasil ditambahkan']);
                return;
            }

            $this->session->set_flashdata('success', 'Data BOM berhasil ditambahkan');
        } catch (Exception $e) {
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                return;
            }
            $this->session->set_flashdata('error', $e->getMessage());
        }

        redirect('manufaktur/bom/' . $product_id);
    }

    public function skalaharga()
    {
        $kode = $this->uri->segment(3);
        $data = $this->get_common_data('Daftar Product');

        $data['table'] = 'tbl_kategori';
        $data['product'] = $this->M_master->getproductorderby('product_nama')->result();
        $data['getProduct'] = $kode ? $this->M_master->getProduct($kode)->row_array() : null;
        $data['getHarga'] = $kode ? $this->M_master->getHarga($kode)->result() : [];
        $data['jmlHarga'] = $kode ? $this->M_master->getHarga($kode)->num_rows() : 0;
        $data['getRange'] = $this->db->get('tbl_range')->result();
        $data['mesin'] = $this->db->get('tbl_mesin')->result();
        $data['mesin_printing'] = $this->db->where_in('id', [2, 3, 4])->get('tbl_mesin_printing')->result();
        $data['getmata'] = $this->db->get('tbl_mata_cutting')->result();
        $data['side'] = $this->db->get('tbl_side')->result();
        $data['segment2'] = $this->uri->segment(2);
        $data['lebarkain'] = $this->db->get('tbl_lebar_textile')->result();
        $data['list_bahan'] = $this->M_master->getDataBahan()->result();
        $data['list_satuan'] = $this->db->get('tbl_satuan')->result();
        $data['list_operator'] = $this->db->get('tbl_operator')->result();

        if ($kode != '' && $data['getProduct']) {
            $data['tampilData'] = $this->M_master->getbomkode('product_id', $kode)->result();
            $data['total_rows'] = $this->M_master->getbomkode('product_id', $kode)->num_rows();

            $data['routingData'] = $this->db->select('routing.*, tbl_product.product_nama, tbl_operator.operator_nama')
                ->from('routing')
                ->join('tbl_product', 'routing.product_id = tbl_product.product_id')
                ->join('tbl_operator', 'routing.operator_id = tbl_operator.operator_id', 'left')
                ->where('routing.product_id', $kode)
                ->order_by('routing.routing_urutan', 'ASC')
                ->get()->result();
        } else {
            $data['getProduct'] = ['product_nama' => 'Pilih Product', 'product_id' => ''];
            $data['getHarga'] = [];
            $data['jmlHarga'] = 0;
            $data['tampilData'] = [];
            $data['total_rows'] = 0;
            $data['routingData'] = [];
        }

        $this->load_admin_view('admin/product/detail', $data);
    }

    public function aksi_simpan_routing()
    {
        $product = $this->input->post('product_id', true);
        $data = [
            'routing_name' => $this->input->post('routing_name', true),
            'product_id' => $product,
        ];

        $add = $this->M_master->addData('routing', $data);
        if ($add) {
            $this->session->set_flashdata('success', 'Data Berhasil Disimpan');
            redirect('manufaktur/routing/' . $product);
        }
    }

    public function aksideletestep($id, $product)
    {
        $this->db->where('routing_id', $id);
        $delete = $this->db->delete('routing');

        if ($delete) {
            $this->session->set_flashdata('hapus', 'Data Berhasil Dihapus');
            redirect('manufaktur/routing/' . $product);
        }
    }

    public function get_subkategori()
    {
        $id = $this->input->post('id', true);
        $query = $this->M_master->get_subkategori($id);
        $data = $query ? $query->result() : [];

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    public function action_add_routing()
    {
        $product = $this->input->post('product_id', true);
        $data = [
            'routing_name' => $this->input->post('routing_name', true),
            'product_id' => $product,
            'routing_urutan' => $this->input->post('routing_urutan', true),
            'operator_id' => $this->input->post('mesin_id', true),
            'is_active' => '1',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('routing', $data);
        $this->session->set_flashdata('success', 'Data routing berhasil ditambahkan.');
        redirect('manufaktur/skalaharga/' . $product);
    }

    public function deleteproduct($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            $this->session->set_flashdata('error', 'ID produk tidak valid.');
            $referer = $this->input->server('HTTP_REFERER');
            redirect($referer ? $referer : 'manufaktur');
            return;
        }

        $product = $this->db->get_where('tbl_product', ['product_id' => $id])->row();
        if (!$product) {
            $this->session->set_flashdata('error', 'Produk tidak ditemukan.');
            $referer = $this->input->server('HTTP_REFERER');
            redirect($referer ? $referer : 'manufaktur');
            return;
        }

        // if ($this->isProductInUse($id)) {
        //     $this->session->set_flashdata('error', 'Produk tidak dapat dihapus karena masih digunakan dalam transaksi aktif.');
        //     redirect('manufaktur');
        //     return;
        // }

        try {
            $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
            $this->db->trans_start();

            $tables_to_clean = [
                'tbl_harga_product' => 'product_id',
                'bom_lines' => 'product_id',
                'routing' => 'product_id',
                'tbl_pemesanan_detail' => 'product_id',
                'tbl_temp_transaksi' => 'product_id',
                'tbl_retur_detail' => 'product_id',
            ];

            foreach ($tables_to_clean as $table => $column) {
                if ($this->db->table_exists($table)) {
                    $this->db->where($column, $id)->delete($table);
                }
            }

            $this->deleteProductImage($product->product_img);

            $this->db->where('product_id', $id);
            if (!$this->db->delete('tbl_product')) {
                throw new Exception('Gagal menghapus data produk utama');
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Gagal menghapus produk dan data terkait');
            }

            $this->session->set_flashdata('success', 'Produk "' . $product->product_nama . '" berhasil dihapus beserta semua data terkait.');

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error deleting product: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Gagal menghapus produk: ' . $e->getMessage());
        } finally {
            $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        }

        $referer = $this->input->server('HTTP_REFERER');
        redirect($referer ? $referer : 'manufaktur');
    }

    public function delete_bulk_product()
    {
        $ids = $this->input->post('ids');
        if (!empty($ids) && is_array($ids)) {
            $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
            $this->db->trans_start();

            $tables_to_clean = [
                'tbl_harga_product' => 'product_id',
                'bom_lines' => 'product_id',
                'routing' => 'product_id',
                'tbl_pemesanan_detail' => 'product_id',
                'tbl_temp_transaksi' => 'product_id',
                'tbl_retur_detail' => 'product_id',
            ];

            // Delete images first
            $this->db->select('product_img');
            $this->db->where_in('product_id', $ids);
            $products = $this->db->get('tbl_product')->result();
            foreach ($products as $p) {
                $this->deleteProductImage($p->product_img);
            }

            foreach ($tables_to_clean as $table => $column) {
                if ($this->db->table_exists($table)) {
                    $this->db->where_in($column, $ids)->delete($table);
                }
            }

            $this->db->where_in('product_id', $ids);
            $this->db->delete('tbl_product');

            $this->db->trans_complete();
            $this->db->query('SET FOREIGN_KEY_CHECKS = 1');

            if ($this->db->trans_status() === FALSE) {
                echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus produk']);
            } else {
                $this->session->set_flashdata('success', count($ids) . ' Produk berhasil dihapus beserta semua data terkait.');
                echo json_encode(['status' => 'success']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Tidak ada produk yang dipilih']);
        }
    }

    private function isProductInUse($product_id)
    {
        // Logic simplified assuming standard active states exist
        $active_transaction = $this->db->where('product_id', $product_id)->count_all_results('tbl_temp_transaksi');
        if ($active_transaction > 0)
            return true;

        $active_order = $this->db->select('COUNT(*) as count')
            ->from('tbl_pemesanan_detail pod')
            ->join('tbl_pemesanan po', 'po.nospk = pod.nospk')
            ->where('pod.product_id', $product_id)
            ->get()->row();

        if ($active_order && $active_order->count > 0)
            return true;

        return false;
    }

    private function deleteProductImage($image_name)
    {
        if (empty($image_name) || $image_name == 'official.png')
            return;
        $image_path = FCPATH . 'assets/images/product/' . $image_name;
        if (file_exists($image_path)) {
            @unlink($image_path);
        }
    }

    public function formUpdate($id)
    {
        $data = $this->get_common_data('Edit Data Product');

        $data['bahan'] = $this->db->get('tbl_bahan')->result();
        $data['kategori'] = $this->db->get('tbl_kategori')->result();
        $data['waktu'] = $this->db->get('tbl_waktu')->result();
        $data['satuan'] = $this->db->get('tbl_satuan')->result();
        $data['getProduct'] = $this->M_master->getProduct($id)->row_array();
        $data['getproduct'] = $data['getProduct']; // Alias for legacy view compatibility
        $data['getHarga'] = $this->M_master->getHarga($id)->result();
        $data['mesin_printing'] = $this->db->where_in('id', [2, 3, 4])->get('tbl_mesin_printing')->result();
        $data['getmata'] = $this->db->get('tbl_mata_cutting')->result();
        $data['side'] = $this->db->get('tbl_side')->result();
        $data['subkategori'] = $this->M_master->get_subkategori($data['getProduct']['kategori_id']);
        $data['segment2'] = $this->uri->segment(2);
        $data['controller'] = $this->uri->segment(1);

        $this->load_admin_view('admin/product/form_update', $data);
    }

    public function actionUpdateproduct()
    {
        $product_id = $this->input->post('product_id', true);
        if (!$product_id)
            show_error('ID produk tidak ditemukan');

        $data = [
            'kategori_id' => $this->input->post('kategori_id', true),
            'subkategori_id' => $this->input->post('subkategori_id', true),
            'bahan_id' => $this->input->post('bahan_id', true),
            'product_nama' => $this->input->post('product_nama', true),
            'tipe_id' => $this->input->post('tipe_id', true),
            'satuan_id' => $this->input->post('satuan_id', true),
            'product_deskripsi' => $this->input->post('product_deskripsi', true),
            'side_id' => $this->input->post('side_id', true),
            'detail_id' => $this->input->post('metode_id', true),
        ];

        $this->db->where('product_id', $product_id);
        $this->db->update('tbl_product', $data);

        $this->session->set_flashdata('success', 'Data produk berhasil diperbarui.');
        redirect('product');
    }

    public function detail($id)
    {
        $data = $this->get_common_data('Tambah Data Product');

        $data['bahan'] = $this->db->select('m.material_id as bahan_id, CONCAT(m.varian, " [", b.jenis_barang, "]") as bahan_nama', FALSE)
            ->from('tbl_material m')
            ->join('tbl_bahan b', 'm.bahan_id = b.bahan_id')
            ->where('b.active', '1')
            ->order_by('m.varian', 'ASC')
            ->get()->result();
        $data['kategori'] = $this->M_master->get_kategori()->result();
        $data['waktu'] = $this->db->get('tbl_waktu')->result();
        $data['satuan'] = $this->db->get('tbl_satuan')->result();
        $data['getProduct'] = $this->M_master->getProduct($id)->row_array();
        $data['getHarga'] = $this->M_master->getHarga($id)->result();
        $data['jmlHarga'] = $this->M_master->getHarga($id)->num_rows();
        $data['getRange'] = $this->db->get('tbl_range')->result();
        $data['mesin'] = $this->db->get('tbl_mesin')->result();
        $data['mesin_printing'] = $this->db->where_in('id', [2, 3, 4])->get('tbl_mesin_printing')->result();
        $data['getmata'] = $this->db->get('tbl_mata_cutting')->result();
        $data['getWarna'] = $this->db->get('tbl_warna')->result();
        $data['getLebar'] = $this->db->get('tbl_lebar_textile')->result();
        $data['kodeProduct'] = $id;
        $data['side'] = $this->db->get('tbl_side')->result();
        $data['list_bom'] = $this->M_master->getbomkode('product_id', $id)->result();
        $data['segment2'] = $this->uri->segment(2); // Added for universal_pricing view compatibility

        $data['nextProduct'] = $this->db->select('product_id, product_nama')
            ->from('tbl_product')
            ->where('product_id >', $id)
            ->order_by('product_id', 'ASC')
            ->limit(1)
            ->get()->row_array();

        $data['prevProduct'] = $this->db->select('product_id, product_nama')
            ->from('tbl_product')
            ->where('product_id <', $id)
            ->order_by('product_id', 'DESC')
            ->limit(1)
            ->get()->row_array();

        $this->load_admin_view('admin/product/detail', $data);
    }

    public function addHarga()
    {
        $product = $this->input->post('product_id', true);
        $data = [
            'product_id' => $product,
            'detail_product' => $this->input->post('product_nama', true),
        ];

        $insert = $this->db->insert('tbl_harga_product', $data);
        $new_id = $this->db->insert_id();

        if ($this->input->is_ajax_request()) {
            $response = $insert
                ? ['success' => true, 'message' => 'Data berhasil ditambahkan', 'product_id' => $product, 'new_id' => $new_id]
                : ['success' => false, 'message' => 'Gagal menambahkan data'];

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
            return;
        }

        if ($insert) {
            $this->session->set_flashdata('success', 'Data Berhasil Disimpan');
            redirect('manufaktur/skala-harga/' . $product);
        }
    }

    public function clone_side_prices()
    {
        header('Content-Type: application/json');
        $product_id = $this->input->post('product_id', true);
        $markup = (float) $this->input->post('markup', true); // e.g., 80 for 80%

        if (!$product_id) {
            echo json_encode(['success' => false, 'message' => 'Product ID is required']);
            return;
        }

        // Get existing Side 1 prices
        $side1_prices = $this->db->get_where('tbl_harga_product', [
            'product_id' => $product_id,
            'ket_1' => 1 // 1 Side
        ])->result_array();

        if (empty($side1_prices)) {
            echo json_encode(['success' => false, 'message' => 'Tidak ditemukan data harga 1 Sisi untuk dikloning.']);
            return;
        }

        $this->db->trans_start();
        $count = 0;
        foreach ($side1_prices as $price) {
            // Check if Side 2 already exists for this exact range
            $exists = $this->db->get_where('tbl_harga_product', [
                'product_id' => $product_id,
                'ket_1' => 2,
                'qty_min' => $price['qty_min'],
                'qty_max' => $price['qty_max']
            ])->row();

            if ($exists)
                continue;

            $new_price = $price;
            unset($new_price['harga_id']);
            $new_price['ket_1'] = 2; // Change to Side 2
            $new_price['detail_product'] = str_replace(['1 Sisi', '1 Side'], '2 Sisi', $price['detail_product']);

            // [NEW] Update Metadata JSON
            $meta_array = !empty($price['metadata']) ? json_decode($price['metadata'], true) : [];
            $meta_array['sisi'] = 2;
            $new_price['metadata'] = json_encode($meta_array);

            if ($markup > 0) {
                $new_price['harga_1'] = round($price['harga_1'] * (1 + ($markup / 100)));
                $new_price['hpp'] = round($price['hpp'] * (1 + ($markup / 100))); // Assumed labor/ink also increases
            }

            $new_price['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('tbl_harga_product', $new_price);
            $count++;
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['success' => false, 'message' => 'Gagal melakukan kloning harga.']);
        } else {
            echo json_encode(['success' => true, 'message' => "Berhasil mengkloning $count baris harga ke 2 Sisi.", 'count' => $count]);
        }
    }

    public function action_save_all()
    {
        $product = $this->input->post('product_id', true);
        $hargaid = $this->input->post('harga_id', true);

        if (!empty($hargaid) && is_array($hargaid)) {
            $detail_product = $this->input->post('detail_product', true);
            $rangeid = $this->input->post('range_id', true);
            $ket1 = $this->input->post('ket_1', true);
            $hpp = $this->input->post('hpp', true);
            $harga1_raw = $this->input->post('harga_1', true);
            $harga_minimal_raw = $this->input->post('harga_minimal', true);
            $qty_min = $this->input->post('qty_min', true);
            $qty_max = $this->input->post('qty_max', true);
            $jenisprint_id = $this->input->post('jenisprint_id', true);
            $side_id = $this->input->post('side_id', true);

            $clean_currency = function ($val) {
                return (int) str_replace('.', '', (string) $val);
            };

            // [NEW] Fetch product's dynamic schemas to map dimension field names safely using model method
            $getProduct = $this->M_master->getProduct($product)->row_array();

            $raw_schema = $getProduct['product_form_schema'] ?? $getProduct['subcategory_form_schema'] ?? $getProduct['category_form_schema'] ?? '';
            $schema = !empty($raw_schema) ? json_decode($raw_schema, true) : [];

            // Reconstruct the dimension options map for label lookup
            $schemaSideOptions = [];
            $schemaMachineOptions = [];
            $schemaThirdOptions = [];

            $key_dim1 = 'ket_1';
            $key_dim2 = 'jenisprint_id';
            $key_dim3 = 'side_id';

            $extraDimensions = [];

            if (!empty($schema)) {
                $unmapped_opts = [];
                $unmapped_names = [];
                $unmapped_full = [];

                foreach ($schema as $field) {
                    if (isset($field['options']) && !empty($field['options']) && in_array($field['type'], ['select', 'radio'])) {
                        $name = $field['name'] ?? '';
                        $labelLower = strtolower($field['label'] ?? '');

                        if ($name === 'jenisprint_id' || strpos($labelLower, 'mesin') !== false || strpos($labelLower, 'metode') !== false || strpos($labelLower, 'jenis print') !== false) {
                            $schemaMachineOptions = $field['options'];
                            $key_dim2 = 'jenisprint_id';
                        } elseif ($name === 'side_id') {
                            $schemaThirdOptions = $field['options'];
                            $key_dim3 = 'side_id';
                        } elseif ($name === 'ket_1' || $name === 'sisi' || strpos($labelLower, 'varian') !== false || strpos($labelLower, 'sisi') !== false) {
                            $schemaSideOptions = $field['options'];
                            $key_dim1 = 'ket_1';
                        } else {
                            $unmapped_opts[] = $field['options'];
                            $unmapped_names[] = $field['name'];
                            $unmapped_full[] = $field;
                        }
                    }
                }

                // Fallbacks for missing mappings
                if (empty($schemaSideOptions) && !empty($unmapped_opts)) {
                    $schemaSideOptions = array_shift($unmapped_opts);
                    $key_dim1 = array_shift($unmapped_names);
                    array_shift($unmapped_full);
                }
                if (empty($schemaMachineOptions) && !empty($unmapped_opts)) {
                    $schemaMachineOptions = array_shift($unmapped_opts);
                    $key_dim2 = array_shift($unmapped_names);
                    array_shift($unmapped_full);
                }
                if (empty($schemaThirdOptions) && !empty($unmapped_opts)) {
                    $schemaThirdOptions = array_shift($unmapped_opts);
                    $key_dim3 = array_shift($unmapped_names);
                    array_shift($unmapped_full);
                }

                $extraDimensions = $unmapped_full;
            }

            // Fallback database maps
            $sides = $this->db->get('tbl_side')->result_array();
            $side_map = [];
            foreach ($sides as $s) {
                $side_map[$s['side_id']] = $s['side_name'];
            }

            $machines = $this->db->where_in('id', [2, 3, 4])->get('tbl_mesin_printing')->result_array();
            $machine_map = [];
            foreach ($machines as $m) {
                $machine_map[$m['id']] = $m['mesin_nama'];
            }

            // Cleaned key names (remove _id to avoid being filtered out in print view)
            $clean_key1 = str_replace('_id', '', $key_dim1);
            $clean_key2 = str_replace('_id', '', $key_dim2);
            $clean_key3 = str_replace('_id', '', $key_dim3);

            $harga1 = array_map($clean_currency, (array) $harga1_raw);
            $harga_minimal = array_map($clean_currency, (array) $harga_minimal_raw);
            $hpp = array_map($clean_currency, (array) $hpp);

            $berhasil = true;
            foreach ($hargaid as $i => $id) {
                if (empty($id))
                    continue;

                // [NEW] Build JSON Metadata dynamically based on schema keys and option labels
                $meta_array = [];
                if (isset($ket1[$i]) && $ket1[$i] !== '') {
                    $val1 = $schemaSideOptions[$ket1[$i]] ?? $side_map[$ket1[$i]] ?? $ket1[$i];
                    $meta_array[$clean_key1] = $val1;
                    if ($clean_key1 !== $key_dim1) {
                        $meta_array[$key_dim1] = $ket1[$i];
                    }
                }
                if (isset($jenisprint_id[$i]) && $jenisprint_id[$i] !== '') {
                    $val2 = $schemaMachineOptions[$jenisprint_id[$i]] ?? $machine_map[$jenisprint_id[$i]] ?? $jenisprint_id[$i];
                    $meta_array[$clean_key2] = $val2;
                    if ($clean_key2 !== $key_dim2) {
                        $meta_array[$key_dim2] = $jenisprint_id[$i];
                    }
                }
                if (isset($side_id[$i]) && $side_id[$i] !== '') {
                    $val3 = $schemaThirdOptions[$side_id[$i]] ?? $side_id[$i];
                    $meta_array[$clean_key3] = $val3;
                    if ($clean_key3 !== $key_dim3) {
                        $meta_array[$key_dim3] = $side_id[$i];
                    }
                }

                // [NEW] Dynamically add extra dimensions from Form Builder (> 3 dimensions)
                foreach ($extraDimensions as $extDim) {
                    $post_vals = $this->input->post('dim_' . $extDim['name'], true);
                    if (isset($post_vals[$i]) && $post_vals[$i] !== '') {
                        $val = $post_vals[$i];
                        $meta_array[$extDim['name']] = $val;

                        // Also store the human readable label 
                        $clean_key = str_replace('_id', '', $extDim['name']);
                        if (isset($extDim['options'][$val])) {
                            if ($clean_key !== $extDim['name']) {
                                $meta_array[$clean_key] = $extDim['options'][$val];
                            } else {
                                $meta_array[$extDim['name'] . '_label'] = $extDim['options'][$val];
                            }
                        }
                    }
                }

                $update = [
                    'detail_product' => $detail_product[$i] ?? '',
                    'range_id' => !empty($rangeid[$i]) ? $rangeid[$i] : 0,
                    'harga_1' => $harga1[$i] ?? 0,
                    'harga_minimal' => $harga_minimal[$i] ?? 0,
                    'qty_min' => !empty($qty_min[$i]) ? $qty_min[$i] : 0,
                    'qty_max' => !empty($qty_max[$i]) ? $qty_max[$i] : 0,
                    'ket_1' => ($ket1[$i] !== '' && $ket1[$i] !== null) ? $ket1[$i] : 0,
                    'hpp' => $hpp[$i] ?? 0,
                    'jenisprint_id' => ($jenisprint_id[$i] !== '' && $jenisprint_id[$i] !== null) ? $jenisprint_id[$i] : 0,
                    'side_id' => ($side_id[$i] !== '' && $side_id[$i] !== null) ? $side_id[$i] : 0,
                    'is_active' => '1',
                    'metadata' => !empty($meta_array) ? json_encode($meta_array) : null // [NEW] Save dynamic metadata
                ];

                $this->db->where('harga_id', $id);
                if (!$this->db->update('tbl_harga_product', $update)) {
                    $berhasil = false;
                }
            }

            if ($berhasil) {
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['success' => true, 'message' => 'Matriks harga berhasil diperbarui']);
                    return;
                }
                $this->session->set_flashdata('success', 'Data berhasil diupdate');
                redirect('manufaktur/detail/' . $product);
            } else {
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['success' => false, 'message' => 'Gagal memperbarui matriks harga. Cek kembali isian form Anda.']);
                    return;
                }
                $this->session->set_flashdata('message', '<div class="alert alert-danger">Gagal memperbarui data.</div>');
                redirect('manufaktur/detail/' . $product);
            }
        }
    }

    public function get_pricing_partial($id)
    {
        $data['getProduct'] = $this->M_master->getProduct($id)->row_array();
        $data['getHarga'] = $this->M_master->getHarga($id)->result();
        $data['jmlHarga'] = $this->M_master->getHarga($id)->num_rows();
        $data['side'] = $this->db->get('tbl_side')->result();
        $data['mesin_printing'] = $this->db->where_in('id', [2, 3, 4])->get('tbl_mesin_printing')->result();
        $data['getmata'] = $this->db->get('tbl_mata_cutting')->result();
        $data['segment2'] = $this->uri->segment(2);

        $this->load->view('admin/product/add_harga/universal_pricing', $data);
    }

    public function get_calculated_hpp_ajax($id)
    {
        // Fetch data for HPP calculation
        $bom = $this->M_master->getbomkode('product_id', $id)->result();
        $routing = $this->db->get_where('routing', ['product_id' => $id])->result();

        $mat = 0;
        $lab = 0;
        foreach ($bom as $i) {
            $qty = (float) $i->quantity;
            $scrap = (float) $i->scrap_factor;
            $cost = (float) $i->unit_cost;
            $mat += ($qty * (1 + ($scrap / 100)) * $cost);
        }
        foreach ($routing as $i) {
            $lab += (float) $i->labor_cost;
        }
        $oh = ($mat + $lab) * 0.05;
        $total = $mat + $lab + $oh;

        echo json_encode(['success' => true, 'hpp' => $total]);
    }


    public function copy_product($kode)
    {
        $data = $this->get_common_data('Duplikat Data Product');

        $data['bahan'] = $this->db->get('tbl_bahan')->result();
        $data['kategori'] = $this->db->get('tbl_kategori')->result();
        $data['satuan'] = $this->db->get('tbl_satuan')->result();
        $data['type'] = $this->db->get('tbl_type')->result();
        $data['getProduct'] = $this->M_master->getProduct($kode)->row_array();
        $data['getproduct'] = $data['getProduct'];
        $data['getHarga'] = $this->M_master->getHarga($kode)->result();
        $data['mesin_printing'] = $this->db->where_in('id', [2, 3, 4])->get('tbl_mesin_printing')->result();
        $data['getmata'] = $this->db->get('tbl_mata_cutting')->result();
        $data['side'] = $this->db->get('tbl_side')->result();
        $data['subkategori'] = $this->M_master->get_subkategori($data['getProduct']['kategori_id']);
        $data['segment2'] = $this->uri->segment(2);
        $data['controller'] = $this->uri->segment(1);

        $this->load_admin_view('admin/product/form_copy', $data);
    }

    public function actionCopy()
    {
        $controller = $this->uri->segment(1);
        $data = [
            'kategori_id' => $this->input->post('kategori_id', true),
            'subkategori_id' => $this->input->post('subkategori_id', true),
            'bahan_id' => $this->input->post('bahan_id', true),
            'product_nama' => $this->input->post('product_nama', true),
            'satuan_id' => $this->input->post('satuan_id', true),
            'product_waktu_pengerjaan' => $this->input->post('product_waktu_pengerjaan', true),
            'satuan_waktu_id' => $this->input->post('satuan_waktu_id', true),
            'product_deskripsi' => $this->input->post('product_deskripsi', true),
            'barcode' => $this->input->post('barcode', true),
        ];

        if ($this->M_master->addData('tbl_product', $data)) {
            $this->session->set_flashdata('success', 'Data Berhasil Disimpan');
            redirect($controller);
        }
    }

    public function del_harga($id, $product_id)
    {
        $this->db->where('harga_id', $id);
        $res = $this->db->delete('tbl_harga_product');

        if ($this->input->is_ajax_request()) {
            echo json_encode(['success' => $res, 'message' => $res ? 'Baris harga berhasil dihapus' : 'Gagal menghapus data']);
            return;
        }

        if ($res) {
            $this->session->set_flashdata('success', 'Data berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data.');
        }

        redirect('manufaktur/detail/' . $product_id);
    }

    public function bulk_del_harga()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $harga_ids = $this->input->post('harga_ids', true);
        if (empty($harga_ids) || !is_array($harga_ids)) {
            echo json_encode(['success' => false, 'message' => 'Tidak ada variasi yang dipilih']);
            return;
        }

        $this->db->where_in('harga_id', $harga_ids);
        $res = $this->db->delete('tbl_harga_product');

        if ($res) {
            echo json_encode(['success' => true, 'message' => count($harga_ids) . ' baris harga berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus baris harga']);
        }
    }

    public function updaterouting($id)
    {
        $data = $this->get_common_data('Edit Routing Product');

        $data['bahan'] = $this->db->get('tbl_bahan')->result();
        $data['waktu'] = $this->db->get('tbl_waktu')->result();
        $data['satuan'] = $this->db->get('tbl_satuan')->result();
        $data['step'] = $this->db->get('tbl_operator')->result();
        $data['routing'] = $this->db->get_where('routing', ['routing_id' => $id])->row_array();
        $data['product_list'] = $this->db->get('tbl_product')->result();
        $data['mesin_list'] = $this->db->get('tbl_mesin')->result();
        $data['segment4'] = $this->uri->segment(4);
        $data['operator_list'] = $this->db->get('tbl_operator')->result();
        $data['bom_list'] = $this->M_master->getbombyproduct($this->uri->segment(4))->result();

        // Warning: using $data['product'] which is not yet defined? 
        // Logic fix needed if original code used it.
        // Assuming $routing['product_id'] can fetch it.
        // For now trusting original flow but it seemed buggy.

        $this->load_admin_view('admin/manufaktur/update_routing', $data);
    }

    public function action_update_routing()
    {
        $id = $this->input->post('routing_id', true);
        $data = [
            'routing_name' => $this->input->post('routing_name', true),
            'product_id' => $this->input->post('product_id', true),
            'routing_urutan' => $this->input->post('routing_urutan', true),
            'operator_id' => $this->input->post('operator_id', true),
            'bom_line_id' => $this->input->post('bom_line_id', true),
            'est_time' => $this->input->post('est_time', true) ?: 0,
            'labor_cost' => str_replace(['.', ','], '', $this->input->post('labor_cost', true) ?: 0),
        ];

        if ($this->db->update('routing', $data, ['routing_id' => $id])) {
            $this->session->set_flashdata('success', 'Data routing berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error', 'Terjadi kesalahan saat memperbarui data routing.');
        }

        redirect('manufaktur/routing/' . $this->input->post('product_id', true));
    }

    // ==================== AJAX / API METHODS ====================

    public function get_bom_data()
    {
        header('Content-Type: application/json');
        $product_id = $this->input->post('product_id', true);

        if (!$product_id) {
            echo json_encode(['success' => false, 'message' => 'Product ID is required']);
            return;
        }

        $result = $this->db->select('bl.*, hp.detail_product as variasi_nama, m.varian as variant_name, b.bahan_nama as base_material_name, m.konversi, b.satuan_id as prod_satuan_id')
            ->from('bom_lines bl')
            ->join('tbl_harga_product hp', 'hp.harga_id = bl.harga_id', 'left')
            ->join('tbl_material m', 'm.material_id = bl.bahan_id', 'left')
            ->join('tbl_bahan b', 'b.bahan_id = m.bahan_id', 'left')
            ->where('bl.product_id', $product_id)
            ->order_by('hp.detail_product', 'ASC')
            ->order_by('hp.ket_1', 'ASC')
            ->order_by('hp.jenisprint_id', 'ASC')
            ->order_by('hp.side_id', 'ASC')
            ->order_by('hp.qty_min', 'ASC')
            ->order_by('bl.bom_line_id', 'ASC')
            ->get()->result();

        $variations = $this->db->get_where('tbl_harga_product', ['product_id' => $product_id])->result();
        $variation_map = [];
        foreach ($variations as &$v) {
            $var_name = $v->detail_product ?: 'Variasi ID: ' . $v->harga_id;
            $meta_desc = [];
            if (!empty($v->metadata)) {
                $meta = json_decode($v->metadata, true);
                if (is_array($meta)) {
                    $ignored = ['product_id', 'nospk', 'qty', 'panjang', 'lebar', 'kategori_id', 'keterangan', 'approve', 'lokasi_cetak'];
                    foreach ($meta as $k => $val) {
                        if (!in_array($k, $ignored) && strpos($k, 'dim_') === false && strpos($k, '_id') === false && $val !== '') {
                            $meta_desc[] = $val;
                        }
                    }
                }
            }
            if (empty($meta_desc)) {
                if (!empty($v->ket_1) && $v->ket_1 !== '0')
                    $meta_desc[] = $v->ket_1;
                if (!empty($v->jenisprint_id) && $v->jenisprint_id !== '0')
                    $meta_desc[] = $v->jenisprint_id;
                if (!empty($v->side_id) && $v->side_id !== '0')
                    $meta_desc[] = $v->side_id;
            }
            if (!empty($meta_desc)) {
                $var_name .= ' (' . implode(' | ', $meta_desc) . ')';
            }
            $v->display_name = $var_name;
            $variation_map[$v->harga_id] = $var_name;
        }
        unset($v);

        if ($result) {
            foreach ($result as $row) {
                // If variant join failed, fallback to old way (direct bahan_id)
                // Only show variant name to avoid doubling names
                $row->material_name = $row->variant_name ?: ($row->base_material_name ?: '-');

                // Update variasi_nama with the detailed display name
                if (!empty($row->harga_id) && isset($variation_map[$row->harga_id])) {
                    $row->variasi_nama = $variation_map[$row->harga_id];
                }

                // Get latest purchase price for the specific material_id
                $latest_price = $this->db->select('pd.harga_beli')
                    ->from('tbl_pembelian_detail pd')
                    ->where('pd.material_id', $row->bahan_id)
                    ->order_by('pd.detail_id', 'DESC')
                    ->limit(1)
                    ->get()->row();

                if (!$latest_price) {
                    // Fallback to old table if using material_id there (unlikely but safe)
                    $latest_price = $this->db->select('harga_beli')
                        ->from('tbl_belanja_bahan')
                        ->where('bahan_id', $row->bahan_id)
                        ->order_by('belanja_id', 'DESC')
                        ->limit(1)
                        ->get()->row();
                }

                $raw_cost = $latest_price ? (float) $latest_price->harga_beli : 0;
                $konversi = (float) $row->konversi;
                if ($konversi <= 0)
                    $konversi = 1;
                $row->unit_cost = $raw_cost / $konversi;

                if (isset($row->manual_cost) && $row->manual_cost !== null && (float) $row->manual_cost > 0) {
                    $row->unit_cost = (float) $row->manual_cost;
                }

                $satuan_id = $row->satuan_id ?? $row->prod_satuan_id;
                if ($satuan_id) {
                    $satuan = $this->db->get_where('tbl_satuan', ['satuan_id' => $satuan_id])->row();
                    $row->satuan = $satuan ? $satuan->satuan_nama : '-';
                } else {
                    $row->satuan = '-';
                }

                // Compatibility for qty
                $row->qty_required = $row->qty = $row->quantity ?? ($row->qty ?? 0);
            }
        }

        echo json_encode(['success' => true, 'data' => $result, 'variations' => $variations]);
    }

    public function get_bahan_options()
    {
        header('Content-Type: application/json');
        $term = $this->input->get('term', true);
        $jenis = $this->input->get('jenis', true);

        $this->db->select("
            m.material_id as id,
            CONCAT(m.varian, ' [', b.jenis_barang, ']') as text,
            b.jenis_barang,
            s.satuan_nama as satuan
        ");
        $this->db->from('tbl_material m');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 's.satuan_id = b.satuan_id', 'left'); // Use Production Unit
        $this->db->where('b.active', '1');

        if ($jenis) {
            $this->db->where('b.jenis_barang', $jenis);
        }

        if ($term) {
            $this->db->group_start();
            $this->db->like('b.bahan_nama', $term);
            $this->db->or_like('m.varian', $term);
            $this->db->group_end();
        }

        $this->db->order_by('b.jenis_barang', 'ASC');
        $this->db->order_by('b.bahan_nama', 'ASC');
        $this->db->limit(50);

        $result = $this->db->get()->result();
        echo json_encode(['results' => $result]);
    }

    public function get_bahan_unit()
    {
        header('Content-Type: application/json');
        $material_id = $this->input->post('bahan_id', true);

        if (!$material_id) {
            echo json_encode(['success' => false, 'message' => 'Material ID is required']);
            return;
        }

        $result = $this->db->select('s.satuan_nama as satuan')
            ->from('tbl_material m')
            ->join('tbl_bahan b', 'b.bahan_id = m.bahan_id')
            ->join('tbl_satuan s', 's.satuan_id = b.satuan_id', 'left') // Get smallest unit
            ->where('m.material_id', $material_id)
            ->get()->row();

        if ($result) {
            echo json_encode(['success' => true, 'data' => $result]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Material/Production unit not found']);
        }
    }

    public function add_bom()
    {
        header('Content-Type: application/json');
        try {
            $product_id = $this->input->post('product_id', true);
            $harga_ids = $this->input->post('harga_id');
            $bahan_id = $this->input->post('bahan_id', true);
            $qty = $this->input->post('qty', true);
            $scrap_factor = $this->input->post('scrap_factor', true) ?: 0;

            if (!is_array($harga_ids)) {
                $harga_ids = [$harga_ids];
            }
            if (empty($harga_ids)) {
                $harga_ids = [null];
            }

            if (!$product_id || !$bahan_id || $qty === null || $qty === '') {
                echo json_encode(['success' => false, 'message' => 'Lengkapilah data material dan kuantitas']);
                return;
            }

            // Get material info
            $material = $this->db->get_where('tbl_material', ['material_id' => $bahan_id])->row();
            if (!$material) {
                echo json_encode(['success' => false, 'message' => 'Variant material tidak ditemukan dalam database']);
                return;
            }

            // Get smallest unit from tbl_bahan
            $bahan = $this->db->get_where('tbl_bahan', ['bahan_id' => $material->bahan_id])->row();

            $inserted = 0;
            $duplicates = 0;

            foreach ($harga_ids as $hid) {
                $hid = ($hid === '' || $hid === 'null' || $hid === '0' || $hid === null) ? null : $hid;

                // Check if already exists to prevent duplicate
                $existing = $this->db->get_where('bom_lines', [
                    'product_id' => $product_id,
                    'bahan_id' => $bahan_id,
                    'harga_id' => $hid
                ])->row();

                if ($existing) {
                    $duplicates++;
                    continue;
                }

                $data = [
                    'product_id' => $product_id,
                    'harga_id' => $hid,
                    'bahan_id' => $bahan_id,
                    'quantity' => $qty,
                    'panjang' => 0,
                    'lebar' => 0,
                    'calc_type' => $this->input->post('calc_type', true) ?: 'fixed',
                    'scrap_factor' => $scrap_factor,
                    'satuan_id' => $bahan ? $bahan->satuan_id : $material->satuan_gudang_id,
                    'keterangan' => $this->input->post('keterangan', true) ?: '',
                    'created_at' => date('Y-m-d H:i:s')
                ];

                if ($this->db->insert('bom_lines', $data)) {
                    $inserted++;
                }
            }

            if ($inserted > 0) {
                $msg = $inserted . ' Material berhasil ditambahkan ke BOM.';
                if ($duplicates > 0) {
                    $msg .= ' (' . $duplicates . ' dilewati karena duplikat)';
                }
                echo json_encode(['success' => true, 'message' => $msg]);
            } else if ($duplicates > 0) {
                echo json_encode(['success' => false, 'message' => 'Material ini sudah ada dalam daftar BOM produk untuk variasi terpilih']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal simpan ke database']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'System error: ' . $e->getMessage()]);
        }
    }

    public function auto_populate_bom()
    {
        header('Content-Type: application/json');
        try {
            $product_id = $this->input->post('product_id', true);
            if (!$product_id) {
                echo json_encode(['success' => false, 'message' => 'Product ID is required']);
                return;
            }

            // Get all variations from tbl_harga_product
            $variations = $this->db->get_where('tbl_harga_product', ['product_id' => $product_id])->result();
            if (empty($variations)) {
                echo json_encode(['success' => false, 'message' => 'Tidak ada variasi (matriks harga) yang ditemukan untuk produk ini. Silakan buat variasi terlebih dahulu.']);
                return;
            }

            $bahan_id = $this->input->post('bahan_id', true);
            $satuan_id = null;

            if ($bahan_id) {
                // Get the selected material
                $material = $this->db->get_where('tbl_material', ['material_id' => $bahan_id])->row();

                if (!$material) {
                    echo json_encode(['success' => false, 'message' => 'Data material tidak ditemukan di database']);
                    return;
                }

                $bahan = $this->db->get_where('tbl_bahan', ['bahan_id' => $material->bahan_id])->row();
                $satuan_id = $bahan ? $bahan->satuan_id : $material->satuan_gudang_id;
            } else {
                $bahan_id = 0;
                $satuan_id = 0;
            }

            $inserted = 0;
            $skipped = 0;

            $harga_ids = $this->input->post('harga_ids');
            if (!is_array($harga_ids)) {
                $harga_ids = [];
            }

            // Track physical signatures that already have a BOM to prevent duplicates across qty ranges
            $processed_signatures = [];
            $existing_boms = $this->db->get_where('bom_lines', ['product_id' => $product_id])->result();
            foreach ($existing_boms as $ebom) {
                if ($ebom->harga_id) {
                    foreach ($variations as $v) {
                        if ($v->harga_id == $ebom->harga_id) {
                            $processed_signatures[] = $v->detail_product . '|' . $v->ket_1 . '|' . $v->jenisprint_id . '|' . $v->side_id;
                            break;
                        }
                    }
                }
            }

            foreach ($variations as $var) {
                // If specific variations are selected, skip those not in the list
                if (!empty($harga_ids) && !in_array($var->harga_id, $harga_ids)) {
                    continue;
                }

                $signature = $var->detail_product . '|' . $var->ket_1 . '|' . $var->jenisprint_id . '|' . $var->side_id;
                if (in_array($signature, $processed_signatures)) {
                    $skipped++;
                    continue;
                }

                $processed_signatures[] = $signature;

                $data = [
                    'product_id' => $product_id,
                    'harga_id' => $var->harga_id,
                    'bahan_id' => $bahan_id, // Default material
                    'quantity' => 1,
                    'panjang' => 0,
                    'lebar' => 0,
                    'calc_type' => 'fixed',
                    'scrap_factor' => 0,
                    'satuan_id' => $satuan_id,
                    'keterangan' => 'Auto-generated',
                    'created_at' => date('Y-m-d H:i:s')
                ];

                if ($this->db->insert('bom_lines', $data)) {
                    $inserted++;
                }
            }

            if ($inserted > 0) {
                echo json_encode(['success' => true, 'message' => "Berhasil generate $inserted baris BOM. Silakan atur materialnya di tab Managemen BOM."]);
            } else if ($skipped > 0) {
                echo json_encode(['success' => false, 'message' => "Semua $skipped variasi sudah memiliki BOM. Tidak ada yang ditambahkan."]);
            } else {
                echo json_encode(['success' => false, 'message' => "Gagal generate BOM"]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'System error: ' . $e->getMessage()]);
        }
    }

    public function delete_bom()
    {
        header('Content-Type: application/json');
        $bom_id = $this->input->post('bom_id', true);

        if (!$bom_id) {
            echo json_encode(['success' => false, 'message' => 'BOM ID is required']);
            return;
        }

        if ($this->db->delete('bom_lines', ['bom_line_id' => $bom_id])) {
            echo json_encode(['success' => true, 'message' => 'BOM berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus BOM']);
        }
    }

    public function delete_bulk_bom()
    {
        header('Content-Type: application/json');
        $bom_ids = $this->input->post('bom_ids');

        if (empty($bom_ids) || !is_array($bom_ids)) {
            echo json_encode(['success' => false, 'message' => 'Tidak ada BOM yang dipilih']);
            return;
        }

        $this->db->where_in('bom_line_id', $bom_ids);
        if ($this->db->delete('bom_lines')) {
            echo json_encode(['success' => true, 'message' => count($bom_ids) . ' BOM berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus BOM terpilih']);
        }
    }

    public function update_bom()
    {
        header('Content-Type: application/json');
        $bom_line_id = $this->input->post('bom_line_id', true);
        $bahan_id = $this->input->post('bahan_id', true);
        $qty = $this->input->post('qty', true);
        $scrap_factor = $this->input->post('scrap_factor', true);
        $harga_id = $this->input->post('harga_id', true);
        if ($harga_id === '' || $harga_id === 'null' || $harga_id === '0') {
            $harga_id = null;
        }
        $manual_cost = $this->input->post('manual_cost', true);
        if ($manual_cost === '') {
            $manual_cost = null;
        }

        if (!$bom_line_id) {
            echo json_encode(['success' => false, 'message' => 'ID BOM tidak ditemukan']);
            return;
        }

        $data = [
            'quantity' => $qty,
            'scrap_factor' => $scrap_factor,
            'harga_id' => $harga_id,
            'panjang' => 0,
            'lebar' => 0,
            'calc_type' => $this->input->post('calc_type', true) ?: 'fixed',
            'keterangan' => $this->input->post('keterangan', true) ?: ''
        ];

        // If material/variant is changed, update bahan_id and satuan_id
        if ($bahan_id) {
            $material = $this->db->get_where('tbl_material', ['material_id' => $bahan_id])->row();
            if ($material) {
                $data['bahan_id'] = $bahan_id;
                // Update to smallest unit of new material
                $bahan = $this->db->get_where('tbl_bahan', ['bahan_id' => $material->bahan_id])->row();
                $data['satuan_id'] = $bahan ? $bahan->satuan_id : $material->satuan_gudang_id;
            }
        }

        $this->db->where('bom_line_id', $bom_line_id);
        if ($this->db->update('bom_lines', $data)) {
            echo json_encode(['success' => true, 'message' => 'Material BOM berhasil diperbarui']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui material BOM']);
        }
    }

    public function update_bom_variation_inline()
    {
        header('Content-Type: application/json');
        $bom_line_id = $this->input->post('bom_line_id', true);
        $harga_id = $this->input->post('harga_id', true);

        if ($harga_id === '' || $harga_id === 'null' || $harga_id === '0') {
            $harga_id = null;
        }

        if (!$bom_line_id) {
            echo json_encode(['success' => false, 'message' => 'ID BOM tidak ditemukan']);
            return;
        }

        $this->db->where('bom_line_id', $bom_line_id);
        $this->db->update('bom_lines', ['harga_id' => $harga_id]);

        echo json_encode(['success' => true]);
    }

    public function update_bom_material_inline()
    {
        header('Content-Type: application/json');
        $bom_line_id = $this->input->post('bom_line_id', true);
        $bahan_id = $this->input->post('bahan_id', true);

        if (!$bom_line_id) {
            echo json_encode(['success' => false, 'message' => 'ID BOM tidak ditemukan']);
            return;
        }

        $material = $this->db->get_where('tbl_material', ['material_id' => $bahan_id])->row();
        if ($material) {
            $bahan = $this->db->get_where('tbl_bahan', ['bahan_id' => $material->bahan_id])->row();
            $satuan_id = $bahan ? $bahan->satuan_id : $material->satuan_gudang_id;

            $this->db->where('bom_line_id', $bom_line_id);
            $this->db->update('bom_lines', [
                'bahan_id' => $bahan_id,
                'satuan_id' => $satuan_id
            ]);

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Material tidak ditemukan']);
        }
    }

    public function get_routing_data()
    {
        header('Content-Type: application/json');
        $product_id = $this->input->post('product_id', true);

        if (!$product_id) {
            echo json_encode(['success' => false, 'message' => 'Product ID is required']);
            return;
        }

        $operator_table = $this->db->table_exists('tbl_operator') ? 'tbl_operator' : 'operator';

        $result = $this->db->select('r.*, hp.detail_product as variasi_nama, o.operator_nama as operator_name, b.bahan_nama as bom_material_name, bl.harga_id as bom_harga_id')
            ->from('routing r')
            ->join('tbl_harga_product hp', 'hp.harga_id = r.harga_id', 'left')
            ->join($operator_table . ' o', 'o.operator_id = r.operator_id', 'left')
            ->join('bom_lines bl', 'bl.bom_line_id = r.bom_line_id', 'left')
            ->join('tbl_material tm', 'tm.material_id = bl.bahan_id', 'left')
            ->join('tbl_bahan b', 'b.bahan_id = tm.bahan_id', 'left')
            ->where('r.product_id', $product_id)
            ->order_by('r.routing_urutan', 'ASC')
            ->get()->result();

        $variations = $this->db->get_where('tbl_harga_product', ['product_id' => $product_id])->result();
        $variation_map = [];
        foreach ($variations as &$v) {
            $var_name = $v->detail_product ?: 'Variasi ID: ' . $v->harga_id;
            $meta_desc = [];
            if (!empty($v->metadata)) {
                $meta = json_decode($v->metadata, true);
                if (is_array($meta)) {
                    $ignored = ['product_id', 'nospk', 'qty', 'panjang', 'lebar', 'kategori_id', 'keterangan', 'approve', 'lokasi_cetak'];
                    foreach ($meta as $k => $val) {
                        if (!in_array($k, $ignored) && strpos($k, 'dim_') === false && strpos($k, '_id') === false && $val !== '') {
                            $meta_desc[] = $val;
                        }
                    }
                }
            }
            if (empty($meta_desc)) {
                if (!empty($v->ket_1) && $v->ket_1 !== '0')
                    $meta_desc[] = $v->ket_1;
                if (!empty($v->jenisprint_id) && $v->jenisprint_id !== '0')
                    $meta_desc[] = $v->jenisprint_id;
                if (!empty($v->side_id) && $v->side_id !== '0')
                    $meta_desc[] = $v->side_id;
            }
            if (!empty($meta_desc)) {
                $var_name .= ' (' . implode(' | ', $meta_desc) . ')';
            }
            $v->display_name = $var_name;
            $variation_map[$v->harga_id] = $var_name;
        }
        unset($v);

        // Fetch dynamic boms to map bom_material_name if r.bom_line_id is dyn_*
        $dyn_boms_map = [];
        $dyn_boms_var_map = [];
        $dyn_boms = $this->db->select('dm.id, dm.attribute_key, dm.attribute_value, b.bahan_nama, m.varian, s.satuan_nama')
            ->from('tbl_bom_dynamic_mapping dm')
            ->join('tbl_material m', 'm.material_id = dm.bahan_id', 'left')
            ->join('tbl_bahan b', 'b.bahan_id = m.bahan_id', 'left')
            ->join('tbl_satuan s', 's.satuan_id = b.satuan_id', 'left')
            ->where('dm.product_id', $product_id)
            ->get()->result();

        foreach ($dyn_boms as $dbom) {
            $mat_nama = trim(($dbom->bahan_nama ?: '') . ($dbom->varian ? ' - ' . $dbom->varian : ''));
            if (empty($mat_nama)) {
                $mat_nama = 'Material #' . $dbom->id;
            }
            $attr_label = ucfirst($dbom->attribute_key) . ': ' . $dbom->attribute_value;
            $dyn_boms_map['dyn_' . $dbom->id] = "[BOM Variasi] " . $mat_nama . " [Var: " . $attr_label . "]";
            $dyn_boms_var_map['dyn_' . $dbom->id] = $attr_label;
        }

        if ($result) {
            foreach ($result as &$r) {
                if (!empty($r->harga_id) && isset($variation_map[$r->harga_id])) {
                    $r->variasi_nama = $variation_map[$r->harga_id];
                }
                if (!empty($r->bom_line_id) && isset($dyn_boms_map[$r->bom_line_id])) {
                    $r->bom_material_name = $dyn_boms_map[$r->bom_line_id];
                }

                // If variasi_nama is empty, derive from linked BOM
                if (empty($r->variasi_nama)) {
                    if (!empty($r->bom_line_id) && isset($dyn_boms_var_map[$r->bom_line_id])) {
                        $r->variasi_nama = $dyn_boms_var_map[$r->bom_line_id];
                    } else if (!empty($r->bom_harga_id) && isset($variation_map[$r->bom_harga_id])) {
                        $r->variasi_nama = $variation_map[$r->bom_harga_id];
                    }
                }
            }
        }

        echo json_encode(['success' => true, 'data' => $result, 'variations' => $variations]);
    }
    public function get_pricing_data()
    {
        header('Content-Type: application/json');
        $product_id = $this->input->post('product_id', true);

        if (!$product_id) {
            echo json_encode(['success' => false, 'message' => 'Product ID is required']);
            return;
        }

        $result = $this->db->select('hp.*, mp.mesin_nama')
            ->from('tbl_harga_product hp')
            ->join('tbl_mesin_printing mp', 'hp.jenisprint_id = mp.id', 'left')
            ->where('hp.product_id', $product_id)
            ->get()->result();

        echo json_encode(['success' => true, 'data' => $result]);
    }

    public function bulk_price_adjustment_ajax()
    {
        header('Content-Type: application/json');
        $category_id = $this->input->post('category_id', true);
        $adj_method = $this->input->post('adj_method', true);
        $adj_value = (float) $this->input->post('adj_value', true);
        $targets = $this->input->post('targets', true);

        if (!$adj_value || empty($targets)) {
            echo json_encode(['success' => false, 'message' => 'Nilai penyesuaian atau target tidak valid.']);
            return;
        }

        $this->db->trans_start();

        // Build base query
        $this->db->select('hp.harga_id, hp.harga_1, hp.hpp, p.product_id');
        $this->db->from('tbl_harga_product hp');
        $this->db->join('tbl_product p', 'p.product_id = hp.product_id');

        if ($category_id !== 'all') {
            $this->db->where('p.kategori_id', $category_id);
        }

        $prices = $this->db->get()->result();
        $count = 0;

        foreach ($prices as $p) {
            $update = [];
            foreach ($targets as $target) {
                $current_val = (float) $p->$target;
                $new_val = $current_val;

                if ($adj_method === 'percent') {
                    $new_val = $current_val + ($current_val * ($adj_value / 100));
                } else {
                    $new_val = $current_val + $adj_value;
                }

                $update[$target] = max(0, round($new_val));
            }

            if (!empty($update)) {
                $this->db->where('harga_id', $p->harga_id);
                $this->db->update('tbl_harga_product', $update);
                $count++;

                // Tambahkan keterangan ke catatan produk (tbl_product)
                $sign = ($adj_value >= 0) ? '+' : '';
                $unit = ($adj_method === 'percent') ? '%' : ' (Flat)';
                $log_note = "\n[ADJUST HARGA " . date('d/m/y') . ": $sign$adj_value$unit]";

                $this->db->set('product_deskripsi', "CONCAT(COALESCE(product_deskripsi, ''), " . $this->db->escape($log_note) . ")", FALSE);
                $this->db->where('product_id', $p->product_id); // we need to select product_id too in the query
                $this->db->update('tbl_product');
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui database.']);
        } else {
            echo json_encode(['success' => true, 'message' => "Berhasil memperbarui $count baris harga."]);
        }
    }

    public function get_operator_options()
    {
        header('Content-Type: application/json');
        $term = $this->input->get('term', true);

        $operator_table = 'tbl_operator';
        if (!$this->db->table_exists('tbl_operator') && $this->db->table_exists('operator')) {
            $operator_table = 'operator';
        }

        $this->db->select('operator_id as id, operator_nama as text')
            ->from($operator_table)
            ->order_by('operator_nama', 'ASC')
            ->limit(20);

        if ($term)
            $this->db->like('operator_nama', $term);

        echo json_encode(['results' => $this->db->get()->result()]);
    }

    public function get_bom_options()
    {
        header('Content-Type: application/json');
        $product_id = $this->input->get('product_id', true);

        if (!$product_id) {
            echo json_encode(['results' => []]);
            return;
        }

        // Get variations map
        $variations = $this->db->get_where('tbl_harga_product', ['product_id' => $product_id])->result();
        $var_map = [];
        foreach ($variations as $v) {
            $var_name = $v->detail_product ?: 'Variasi ID: ' . $v->harga_id;
            $meta_desc = [];
            if (!empty($v->metadata)) {
                $meta = json_decode($v->metadata, true);
                if (is_array($meta)) {
                    $ignored = ['product_id', 'nospk', 'qty', 'panjang', 'lebar', 'kategori_id', 'keterangan', 'approve', 'lokasi_cetak'];
                    foreach ($meta as $k => $val) {
                        if (!in_array($k, $ignored) && strpos($k, 'dim_') === false && strpos($k, '_id') === false && $val !== '') {
                            $meta_desc[] = $val;
                        }
                    }
                }
            }
            if (empty($meta_desc)) {
                if (!empty($v->ket_1) && $v->ket_1 !== '0')
                    $meta_desc[] = $v->ket_1;
                if (!empty($v->jenisprint_id) && $v->jenisprint_id !== '0')
                    $meta_desc[] = $v->jenisprint_id;
                if (!empty($v->side_id) && $v->side_id !== '0')
                    $meta_desc[] = $v->side_id;
            }
            if (!empty($meta_desc)) {
                $var_name .= ' (' . implode(' | ', $meta_desc) . ')';
            }
            $var_map[$v->harga_id] = $var_name;
        }

        $this->_check_routing_bom_column();

        $boms = $this->db->select('bl.bom_line_id as id, bl.harga_id, bl.keterangan, b.bahan_nama, m.varian, bl.quantity, s.satuan_nama')
            ->from('bom_lines bl')
            ->join('tbl_material m', 'm.material_id = bl.bahan_id', 'left')
            ->join('tbl_bahan b', 'b.bahan_id = m.bahan_id', 'left')
            ->join('tbl_satuan s', 's.satuan_id = bl.satuan_id', 'left')
            ->where('bl.product_id', $product_id)
            ->order_by('bl.harga_id', 'ASC')
            ->order_by('b.bahan_nama', 'ASC')
            ->get()->result();

        $result = [];
        foreach ($boms as $bom) {
            $var_text = ($bom->harga_id && isset($var_map[$bom->harga_id])) ? " [Var: " . $var_map[$bom->harga_id] . "]" : " [Var: Global]";
            $ket_text = !empty($bom->keterangan) ? " - " . $bom->keterangan : "";
            $mat_nama = trim(($bom->bahan_nama ?: '') . ($bom->varian ? ' - ' . $bom->varian : ''));
            $text = $mat_nama . ' (' . $bom->quantity . ' ' . $bom->satuan_nama . ')' . $ket_text . $var_text;
            $result[] = (object) [
                'id' => (string) $bom->id,
                'text' => $text,
                'harga_id' => $bom->harga_id,
                'var_name' => ($bom->harga_id && isset($var_map[$bom->harga_id])) ? $var_map[$bom->harga_id] : ''
            ];
        }

        // 2. Dynamic BOMs (Variasi BOM)
        $dyn_boms = $this->db->select('dm.id, dm.attribute_key, dm.attribute_value, dm.calc_type, dm.divider_factor, b.bahan_nama, m.varian, s.satuan_nama')
            ->from('tbl_bom_dynamic_mapping dm')
            ->join('tbl_material m', 'm.material_id = dm.bahan_id', 'left')
            ->join('tbl_bahan b', 'b.bahan_id = m.bahan_id', 'left')
            ->join('tbl_satuan s', 's.satuan_id = b.satuan_id', 'left')
            ->where('dm.product_id', $product_id)
            ->order_by('dm.attribute_key', 'ASC')
            ->order_by('dm.attribute_value', 'ASC')
            ->get()->result();

        foreach ($dyn_boms as $dbom) {
            $mat_nama = trim(($dbom->bahan_nama ?: '') . ($dbom->varian ? ' - ' . $dbom->varian : ''));
            if (empty($mat_nama)) {
                $mat_nama = 'Material #' . $dbom->id;
            }
            $satuan = !empty($dbom->satuan_nama) ? ' (' . $dbom->satuan_nama . ')' : '';
            $attr_label = ucfirst($dbom->attribute_key) . ': ' . $dbom->attribute_value;
            $text = "[BOM Variasi] " . $mat_nama . $satuan . " [Var: " . $attr_label . "]";
            $result[] = (object) [
                'id' => 'dyn_' . $dbom->id,
                'text' => $text,
                'attr_key' => $dbom->attribute_key,
                'attr_val' => $dbom->attribute_value,
                'var_name' => $attr_label
            ];
        }

        // Filter by term if provided
        $term = trim($this->input->get('term', true) ?? '');
        if (!empty($term)) {
            $filtered = [];
            foreach ($result as $item) {
                if (stripos($item->text, $term) !== false) {
                    $filtered[] = $item;
                }
            }
            $result = $filtered;
        }

        // Add 'No Material' option
        array_unshift($result, (object) ['id' => '99999', 'text' => '-- Tanpa Material --']);

        echo json_encode(['results' => $result]);
    }

    private function _check_routing_bom_column()
    {
        try {
            $fields = $this->db->field_data('routing');
            foreach ($fields as $field) {
                if ($field->name === 'bom_line_id' && ($field->type === 'int' || $field->type === 'integer')) {
                    $this->db->query("ALTER TABLE routing MODIFY bom_line_id VARCHAR(50) NULL DEFAULT NULL");
                    break;
                }
            }
        } catch (Exception $e) {
        }
    }

    public function add_routing()
    {
        header('Content-Type: application/json');
        try {
            $this->_check_routing_bom_column();
            $product_id = $this->input->post('product_id', true);
            $routing_name = $this->input->post('routing_name', true);
            $bom_line_id = $this->input->post('bom_line_id', true);
            $routing_urutan = $this->input->post('routing_urutan', true);
            $operator_id = $this->input->post('operator_id', true);
            $is_finishing = $this->input->post('is_finishing', true) ?: '0';
            $harga_ids = $this->input->post('harga_id');

            if (!is_array($harga_ids)) {
                $harga_ids = [$harga_ids];
            }
            if (empty($harga_ids)) {
                $harga_ids = [null];
            }

            $est_time = $this->input->post('est_time', true) ?: 0;
            $raw_labor = $this->input->post('labor_cost', true);
            // Ensure string for str_replace
            $labor_cost = str_replace(['.', ','], '', (string) ($raw_labor ?: '0'));

            if (empty($product_id) || empty($routing_name) || empty($routing_urutan) || empty($operator_id)) {
                echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi']);
                return;
            }

            // Handle "No Material" option from dropdown (99999 or empty)
            if ($bom_line_id == '99999' || empty($bom_line_id)) {
                $bom_line_id = NULL;
            }

            $inserted = 0;
            $duplicates = 0;

            foreach ($harga_ids as $hid) {
                $hid = ($hid === '' || $hid === 'null' || $hid === '0' || $hid === null) ? null : $hid;

                // Auto-detect variation from bom_line_id if harga_id is not specified
                if (empty($hid) && !empty($bom_line_id)) {
                    if (is_numeric($bom_line_id)) {
                        $bl = $this->db->get_where('bom_lines', ['bom_line_id' => $bom_line_id])->row();
                        if ($bl && !empty($bl->harga_id)) {
                            $hid = $bl->harga_id;
                        }
                    } else if (strpos($bom_line_id, 'dyn_') === 0) {
                        $dyn_id = substr($bom_line_id, 4);
                        $dyn = $this->db->get_where('tbl_bom_dynamic_mapping', ['id' => $dyn_id])->row();
                        if ($dyn) {
                            $vars = $this->db->get_where('tbl_harga_product', ['product_id' => $product_id])->result();
                            foreach ($vars as $v) {
                                if (!empty($v->metadata)) {
                                    $m = json_decode($v->metadata, true);
                                    if (is_array($m) && isset($m[$dyn->attribute_key]) && strcasecmp((string)$m[$dyn->attribute_key], (string)$dyn->attribute_value) === 0) {
                                        $hid = $v->harga_id;
                                        break;
                                    }
                                }
                                if (stripos($v->detail_product, $dyn->attribute_value) !== false) {
                                    $hid = $v->harga_id;
                                    break;
                                }
                            }
                        }
                    }
                }

                // Check for duplicates
                $existing = $this->db->get_where('routing', [
                    'product_id' => $product_id,
                    'routing_urutan' => $routing_urutan,
                    'harga_id' => $hid
                ])->row();

                if ($existing) {
                    $duplicates++;
                    continue;
                }

                $data = [
                    'routing_name' => $routing_name,
                    'product_id' => $product_id,
                    'bom_line_id' => $bom_line_id,
                    'routing_urutan' => $routing_urutan,
                    'operator_id' => $operator_id,
                    'is_finishing' => $is_finishing,
                    'harga_id' => $hid,
                    'est_time' => $est_time,
                    'labor_cost' => $labor_cost,
                    'is_active' => '1',
                    'created_at' => date('Y-m-d H:i:s')
                ];

                if ($this->db->insert('routing', $data)) {
                    $inserted++;
                }
            }

            if ($inserted > 0) {
                $msg = $inserted . ' Routing berhasil ditambahkan.';
                if ($duplicates > 0) {
                    $msg .= ' (' . $duplicates . ' dilewati karena urutan duplikat)';
                }
                echo json_encode(['success' => true, 'message' => $msg]);
            } else if ($duplicates > 0) {
                echo json_encode(['success' => false, 'message' => 'Urutan routing sudah digunakan pada variasi terpilih']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menyimpan routing']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()]);
        }
    }

    public function update_routing()
    {
        header('Content-Type: application/json');
        try {
            $this->_check_routing_bom_column();
            $routing_id = $this->input->post('routing_id', true);
            $routing_name = $this->input->post('routing_name', true);
            $bom_line_id = $this->input->post('bom_line_id', true);
            $routing_urutan = $this->input->post('routing_urutan', true);
            $operator_id = $this->input->post('operator_id', true);
            $is_finishing = $this->input->post('is_finishing', true) ?: '0';
            $harga_id = $this->input->post('harga_id', true);
            if ($harga_id === '' || $harga_id === 'null' || $harga_id === '0') {
                $harga_id = null;
            }
            $est_time = $this->input->post('est_time', true) ?: 0;
            $labor_cost = str_replace(['.', ','], '', $this->input->post('labor_cost', true) ?: 0);

            if (!$routing_id || !$routing_name || !$routing_urutan || !$operator_id) {
                echo json_encode(['success' => false, 'message' => 'Lengkapilah data yang wajib diisi (Urutan, Nama Operasi, Operator)']);
                return;
            }

            if ($bom_line_id == '99999' || empty($bom_line_id)) {
                $bom_line_id = NULL;
            }

            // Auto-detect variation from bom_line_id if harga_id is not specified
            if (empty($harga_id) && !empty($bom_line_id)) {
                $existing_r = $this->db->get_where('routing', ['routing_id' => $routing_id])->row();
                $pid = $existing_r ? $existing_r->product_id : 0;
                if (is_numeric($bom_line_id)) {
                    $bl = $this->db->get_where('bom_lines', ['bom_line_id' => $bom_line_id])->row();
                    if ($bl && !empty($bl->harga_id)) {
                        $harga_id = $bl->harga_id;
                    }
                } else if (strpos($bom_line_id, 'dyn_') === 0) {
                    $dyn_id = substr($bom_line_id, 4);
                    $dyn = $this->db->get_where('tbl_bom_dynamic_mapping', ['id' => $dyn_id])->row();
                    if ($dyn && $pid) {
                        $vars = $this->db->get_where('tbl_harga_product', ['product_id' => $pid])->result();
                        foreach ($vars as $v) {
                            if (!empty($v->metadata)) {
                                $m = json_decode($v->metadata, true);
                                if (is_array($m) && isset($m[$dyn->attribute_key]) && strcasecmp((string)$m[$dyn->attribute_key], (string)$dyn->attribute_value) === 0) {
                                    $harga_id = $v->harga_id;
                                    break;
                                }
                            }
                            if (stripos($v->detail_product, $dyn->attribute_value) !== false) {
                                $harga_id = $v->harga_id;
                                break;
                            }
                        }
                    }
                }
            }

            $data = [
                'routing_name' => $routing_name,
                'bom_line_id' => $bom_line_id,
                'routing_urutan' => $routing_urutan,
                'operator_id' => $operator_id,
                'is_finishing' => $is_finishing,
                'harga_id' => $harga_id,
                'est_time' => $est_time,
                'labor_cost' => $labor_cost
            ];

            $this->db->where('routing_id', $routing_id);
            if ($this->db->update('routing', $data)) {
                echo json_encode(['success' => true, 'message' => 'Routing berhasil diperbarui']);
            } else {
                $error = $this->db->error();
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $error['message']]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'System error: ' . $e->getMessage()]);
        }
    }

    public function delete_routing()
    {
        header('Content-Type: application/json');
        $routing_id = $this->input->post('routing_id', true);

        if (!$routing_id) {
            echo json_encode(['success' => false, 'message' => 'Routing ID is required']);
            return;
        }

        if ($this->db->delete('routing', ['routing_id' => $routing_id])) {
            echo json_encode(['success' => true, 'message' => 'Routing berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus routing']);
        }
    }

    public function delete_bulk_routing()
    {
        header('Content-Type: application/json');
        $routing_ids = $this->input->post('routing_ids');

        if (empty($routing_ids) || !is_array($routing_ids)) {
            echo json_encode(['success' => false, 'message' => 'Tidak ada Langkah Kerja yang dipilih']);
            return;
        }

        $this->db->where_in('routing_id', $routing_ids);
        if ($this->db->delete('routing')) {
            echo json_encode(['success' => true, 'message' => count($routing_ids) . ' Langkah Kerja berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus Langkah Kerja terpilih']);
        }
    }

    public function get_product_detail($id)
    {
        if (!$id) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'ID tidak valid']));
        }

        $query = $this->M_master->getProduct($id);
        $product = $query ? $query->row_array() : null;

        if ($product) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'data' => $product]));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Produk tidak ditemukan']));
        }
    }

    public function update_product_ajax()
    {
        $product_id = $this->input->post('product_id', true);
        if (!$product_id) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'ID produk tidak ditemukan']));
        }

        $data = [
            'product_nama' => $this->input->post('product_nama', true),
            'kategori_id' => $this->input->post('kategori_id', true),
            'subkategori_id' => $this->input->post('subkategori_id', true),
            'tipe_id' => $this->input->post('tipe_id', true),
            'satuan_id' => $this->input->post('satuan_id', true),
            'side_id' => $this->input->post('side_id', true),
            'detail_id' => $this->input->post('metode_id', true),
            'product_deskripsi' => $this->input->post('product_deskripsi', true)
        ];

        $this->db->where('product_id', $product_id);
        if ($this->db->update('tbl_product', $data)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Produk berhasil diperbarui']));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal memperbarui produk']));
        }
    }

    public function rename_product_ajax()
    {
        $product_id = $this->input->post('product_id', true);
        $product_nama = $this->input->post('product_nama', true);

        if (!$product_id || !$product_nama) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']));
        }

        $this->db->where('product_id', $product_id);
        if ($this->db->update('tbl_product', ['product_nama' => $product_nama])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Nama produk berhasil diperbarui']));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal memperbarui nama produk']));
        }
    }

    public function toggleProductStatus()
    {
        $product_id = $this->input->post('product_id', true);
        $status = $this->input->post('status', true);

        if (!$product_id) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'ID produk tidak ditemukan']));
        }

        $this->db->where('product_id', $product_id);
        if ($this->db->update('tbl_product', ['status' => $status])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Status produk berhasil diubah']));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal mengubah status produk']));
        }
    }

    public function clone_product()
    {
        header('Content-Type: application/json');
        $source_id = $this->input->post('source_id', true);
        $new_name = $this->input->post('new_name', true);

        if (!$source_id || !$new_name) {
            echo json_encode(['success' => false, 'message' => 'Source ID dan Nama Baru wajib diisi.']);
            return;
        }

        $source_product = $this->db->get_where('tbl_product', ['product_id' => $source_id])->row_array();
        if (!$source_product) {
            echo json_encode(['success' => false, 'message' => 'Produk sumber tidak ditemukan.']);
            return;
        }

        $this->db->trans_start();

        // 1. Clone Product Header
        $new_product = $source_product;
        unset($new_product['product_id']); // Remove PK
        $new_product['product_nama'] = $new_name;
        $new_product['created_at'] = date('Y-m-d H:i:s');

        $this->db->insert('tbl_product', $new_product);
        $new_product_id = $this->db->insert_id();

        // 2. Clone BOM Lines with Mapping
        $bom_mapping = [99999 => 99999]; // Default for "No Material"
        $bom_lines = $this->db->get_where('bom_lines', ['product_id' => $source_id])->result_array();
        foreach ($bom_lines as $line) {
            $old_bom_id = $line['bom_line_id'];
            unset($line['bom_line_id']);
            $line['product_id'] = $new_product_id;
            $line['created_at'] = date('Y-m-d H:i:s');

            $this->db->insert('bom_lines', $line);
            $bom_mapping[$old_bom_id] = $this->db->insert_id();
        }

        // 3. Clone Routing with Remapped BOM links
        $routings = $this->db->get_where('routing', ['product_id' => $source_id])->result_array();
        foreach ($routings as $routing) {
            unset($routing['routing_id']);
            $routing['product_id'] = $new_product_id;

            // Remap BOM link if exists
            if (isset($routing['bom_line_id']) && isset($bom_mapping[$routing['bom_line_id']])) {
                $routing['bom_line_id'] = $bom_mapping[$routing['bom_line_id']];
            }

            $routing['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('routing', $routing);
        }

        // 4. Clone Scale Prices (tbl_harga_product)
        $prices = $this->db->get_where('tbl_harga_product', ['product_id' => $source_id])->result_array();
        foreach ($prices as $price) {
            unset($price['harga_id']);
            $price['product_id'] = $new_product_id;
            $this->db->insert('tbl_harga_product', $price);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['success' => false, 'message' => 'Gagal menduplikasi produk. Silakan coba lagi.']);
        } else {
            echo json_encode(['success' => true, 'message' => 'Produk berhasil diduplikasi.', 'new_id' => $new_product_id]);
        }
    }

    public function bulk_apply_template()
    {
        header('Content-Type: application/json');
        $template_id = $this->input->post('template_id', true);
        $target_ids = $this->input->post('target_ids', true); // Array of IDs
        $options = $this->input->post('options', true); // ['bom', 'routing']

        if (!$template_id || empty($target_ids)) {
            echo json_encode(['success' => false, 'message' => 'Template dan Produk Target wajib dipilih.']);
            return;
        }

        $this->db->trans_start();

        foreach ($target_ids as $target_id) {
            // 1. Clear existing if needed? Or just append? Usually better to append or clear.
            // Let's go with clear existing for the selected options to ensure template matches.

            if (in_array('bom', $options)) {
                $this->db->delete('bom_lines', ['product_id' => $target_id]);

                $bom_mapping = [99999 => 99999];
                $bom_lines = $this->db->get_where('bom_lines', ['product_id' => $template_id])->result_array();
                foreach ($bom_lines as $line) {
                    $old_bom_id = $line['bom_line_id'];
                    unset($line['bom_line_id']);
                    $line['product_id'] = $target_id;
                    $line['created_at'] = date('Y-m-d H:i:s');
                    $this->db->insert('bom_lines', $line);
                    $bom_mapping[$old_bom_id] = $this->db->insert_id();
                }
            }

            if (in_array('routing', $options)) {
                $this->db->delete('routing', ['product_id' => $target_id]);

                $routings = $this->db->get_where('routing', ['product_id' => $template_id])->result_array();
                foreach ($routings as $routing) {
                    unset($routing['routing_id']);
                    $routing['product_id'] = $target_id;

                    // Remap BOM link if BOM was also copied
                    if (in_array('bom', $options) && isset($routing['bom_line_id']) && isset($bom_mapping[$routing['bom_line_id']])) {
                        $routing['bom_line_id'] = $bom_mapping[$routing['bom_line_id']];
                    } else if (!in_array('bom', $options)) {
                        // If BOM not copied, we should probably NULL the link or keep it if it's 99999
                        if ($routing['bom_line_id'] != 99999) {
                            $routing['bom_line_id'] = NULL;
                        }
                    }

                    $routing['created_at'] = date('Y-m-d H:i:s');
                    $this->db->insert('routing', $routing);
                }
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['success' => false, 'message' => 'Gagal menerapkan template.']);
        } else {
            echo json_encode(['success' => true, 'message' => 'Template berhasil diterapkan ke ' . count($target_ids) . ' produk.']);
        }
    }

    public function get_products_by_subkategori()
    {
        header('Content-Type: application/json');
        $subkategori_id = $this->input->get('subkategori_id', true);
        if (!$subkategori_id) {
            echo json_encode([]);
            return;
        }

        $result = $this->db->select('product_id as id, product_nama as text')
            ->from('tbl_product')
            ->where('subkategori_id', $subkategori_id)
            ->order_by('product_nama', 'ASC')
            ->get()->result();

        echo json_encode($result);
    }

    public function search_product()
    {
        header('Content-Type: application/json');
        $keyword = trim($this->input->get('term', true) ?? '');

        $this->db->select('p.product_id, p.product_nama, k.kategori_nama');
        $this->db->from('tbl_product p');
        $this->db->join('tbl_kategori k', 'k.kategori_id = p.kategori_id', 'left');

        if (!empty($keyword)) {
            $this->db->group_start();
            $this->db->like('p.product_nama', $keyword);
            $this->db->or_like('p.product_id', $keyword);
            $this->db->or_like('k.kategori_nama', $keyword);
            $this->db->group_end();
        }

        $this->db->order_by('p.product_nama', 'ASC');
        $this->db->limit(35);
        $products = $this->db->get()->result();

        $results = [];
        foreach ($products as $p) {
            $cat_info = !empty($p->kategori_nama) ? ' (' . $p->kategori_nama . ')' : '';
            $results[] = [
                'id' => $p->product_id,
                'text' => '#' . $p->product_id . ' - ' . $p->product_nama . $cat_info
            ];
        }

        echo json_encode(['results' => $results]);
    }

    public function uploadproduct($id)
    {
        $data = $this->get_common_data('Upload Image Product');
        $data['product'] = $this->db->get_where('tbl_product', array('product_id' => $id))->row_array();
        $this->load_admin_view('admin/product/form_upload', $data);
    }

    public function actionuploadaproduct()
    {
        $id = $this->input->post('product_id', true);
        $path = FCPATH . 'assets/images/product/';
        if (!is_dir($path)) {
            @mkdir($path, 0777, true);
        }
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'jpg|jpeg|png|webp';
        $config['max_size'] = '5048';

        $this->load->library('upload');
        $this->upload->initialize($config);

        if (!empty($_FILES['product_img_1']['name'])) {
            if ($this->upload->do_upload('product_img_1')) {
                $foto_baru = $this->upload->data('file_name');
                $this->db->where('product_id', $id)->update('tbl_product', ['product_img' => $foto_baru]);
                $this->session->set_flashdata('success', 'Gambar berhasil diupload');
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors());
            }
        }
        redirect('manufaktur/uploadproduct/' . $id);
    }




    public function all_routing()
    {
        $data = $this->get_common_data('Semua Data Routing');

        $operator_table = $this->db->table_exists('tbl_operator') ? 'tbl_operator' : 'operator';

        $data['tampilData'] = $this->db->select('routing.*, tbl_product.product_nama, ' . $operator_table . '.operator_nama, hp.detail_product as variasi_nama, hp.ket_1, hp.jenisprint_id, hp.side_id, hp.metadata, b.bahan_nama as bom_material_name, tm.varian as bom_varian')
            ->from('routing')
            ->join('tbl_product', 'routing.product_id = tbl_product.product_id', 'left')
            ->join($operator_table, 'routing.operator_id = ' . $operator_table . '.operator_id', 'left')
            ->join('tbl_harga_product hp', 'hp.harga_id = routing.harga_id', 'left')
            ->join('bom_lines bl', 'bl.bom_line_id = routing.bom_line_id', 'left')
            ->join('tbl_material tm', 'tm.material_id = bl.bahan_id', 'left')
            ->join('tbl_bahan b', 'b.bahan_id = tm.bahan_id', 'left')
            ->order_by('routing.routing_id', 'DESC')
            ->get()->result();

        // Proses nama variasi agar sama dengan format detail produk (jika ada meta)
        foreach ($data['tampilData'] as &$dt) {
            if (empty($dt->harga_id) || $dt->harga_id == '0' || $dt->harga_id == null) {
                $dt->jenis_routing = 'Global';
                $dt->display_variasi = '-';
            } else {
                $dt->jenis_routing = 'Variasi';
                $var_name = $dt->variasi_nama ?: 'Variasi ID: ' . $dt->harga_id;
                $meta_desc = [];
                if (!empty($dt->metadata)) {
                    $meta = json_decode($dt->metadata, true);
                    if (is_array($meta)) {
                        $ignored = ['product_id', 'nospk', 'qty', 'panjang', 'lebar', 'kategori_id', 'keterangan', 'approve', 'lokasi_cetak'];
                        foreach ($meta as $k => $val) {
                            if (!in_array($k, $ignored) && strpos($k, 'dim_') === false && strpos($k, '_id') === false && $val !== '') {
                                $meta_desc[] = $val;
                            }
                        }
                    }
                }
                if (empty($meta_desc)) {
                    if (!empty($dt->ket_1) && $dt->ket_1 !== '0')
                        $meta_desc[] = $dt->ket_1;
                    if (!empty($dt->jenisprint_id) && $dt->jenisprint_id !== '0')
                        $meta_desc[] = $dt->jenisprint_id;
                    if (!empty($dt->side_id) && $dt->side_id !== '0')
                        $meta_desc[] = $dt->side_id;
                }
                if (!empty($meta_desc)) {
                    $var_name .= ' (' . implode(' | ', $meta_desc) . ')';
                }
                $dt->display_variasi = $var_name;
            }
        }
        unset($dt);

        $data['total_rows'] = count($data['tampilData']);

        $this->load_admin_view('admin/manufaktur/all_routing', $data);
    }

    public function all_bom()
    {
        $data = $this->get_common_data('Semua Data BOM');

        $data['tampilData'] = $this->db->select('bl.*, tbl_product.product_nama, b.bahan_nama, m.varian, s.satuan_nama, hp.detail_product as variasi_nama, hp.ket_1, hp.jenisprint_id, hp.side_id, hp.metadata')
            ->from('bom_lines bl')
            ->join('tbl_product', 'bl.product_id = tbl_product.product_id', 'left')
            ->join('tbl_material m', 'm.material_id = bl.bahan_id', 'left')
            ->join('tbl_bahan b', 'b.bahan_id = m.bahan_id', 'left')
            ->join('tbl_satuan s', 's.satuan_id = bl.satuan_id', 'left')
            ->join('tbl_harga_product hp', 'hp.harga_id = bl.harga_id', 'left')
            ->order_by('bl.bom_line_id', 'DESC')
            ->get()->result();

        // Proses nama variasi
        foreach ($data['tampilData'] as &$dt) {
            if (empty($dt->harga_id) || $dt->harga_id == '0' || $dt->harga_id == null) {
                $dt->jenis_bom = 'Global';
                $dt->display_variasi = '-';
            } else {
                $dt->jenis_bom = 'Variasi';
                $var_name = $dt->variasi_nama ?: 'Variasi ID: ' . $dt->harga_id;
                $meta_desc = [];
                if (!empty($dt->metadata)) {
                    $meta = json_decode($dt->metadata, true);
                    if (is_array($meta)) {
                        $ignored = ['product_id', 'nospk', 'qty', 'panjang', 'lebar', 'kategori_id', 'keterangan', 'approve', 'lokasi_cetak'];
                        foreach ($meta as $k => $val) {
                            if (!in_array($k, $ignored) && strpos($k, 'dim_') === false && strpos($k, '_id') === false && $val !== '') {
                                $meta_desc[] = $val;
                            }
                        }
                    }
                }
                if (empty($meta_desc)) {
                    if (!empty($dt->ket_1) && $dt->ket_1 !== '0')
                        $meta_desc[] = $dt->ket_1;
                    if (!empty($dt->jenisprint_id) && $dt->jenisprint_id !== '0')
                        $meta_desc[] = $dt->jenisprint_id;
                    if (!empty($dt->side_id) && $dt->side_id !== '0')
                        $meta_desc[] = $dt->side_id;
                }
                if (!empty($meta_desc)) {
                    $var_name .= ' (' . implode(' | ', $meta_desc) . ')';
                }
                $dt->display_variasi = $var_name;
            }
        }
        unset($dt);

        $data['total_rows'] = count($data['tampilData']);

        $this->load_admin_view('admin/manufaktur/all_bom', $data);
    }

    public function copy_bom_routing_to_variations()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $product_id = $this->input->post('product_id');
        $source_harga_id = $this->input->post('source_harga_id');
        $target_harga_ids = $this->input->post('target_harga_ids');

        if (empty($product_id) || empty($target_harga_ids) || !is_array($target_harga_ids)) {
            echo json_encode(['success' => false, 'message' => 'Parameter tidak lengkap.']);
            return;
        }

        try {
            $this->db->trans_start();

            // Fetch BOM Sources
            if (empty($source_harga_id)) {
                $this->db->where("(harga_id IS NULL OR harga_id = 0 OR harga_id = '')");
            } else {
                $this->db->where('harga_id', $source_harga_id);
            }
            $this->db->where('product_id', $product_id);
            $source_boms = $this->db->get('bom_lines')->result_array();

            // Fetch Routing Sources
            if (empty($source_harga_id)) {
                $this->db->where("(harga_id IS NULL OR harga_id = 0 OR harga_id = '')");
            } else {
                $this->db->where('harga_id', $source_harga_id);
            }
            $this->db->where('product_id', $product_id);
            $source_routings = $this->db->get('routing')->result_array();

            if (empty($source_boms) && empty($source_routings)) {
                echo json_encode(['success' => false, 'message' => 'Tidak ada data BOM / Routing pada variasi sumber.']);
                return;
            }

            foreach ($target_harga_ids as $target_id) {
                // Skip if target is the same as source
                if ($target_id == $source_harga_id)
                    continue;

                $db_target_id = ($target_id === '0' || $target_id === '') ? null : $target_id;

                // 1. Delete Existing BOM and Routing for Target
                if (empty($db_target_id)) {
                    $this->db->where("(harga_id IS NULL OR harga_id = 0 OR harga_id = '')");
                } else {
                    $this->db->where('harga_id', $db_target_id);
                }
                $this->db->where('product_id', $product_id);
                $this->db->delete('bom_lines');

                if (empty($db_target_id)) {
                    $this->db->where("(harga_id IS NULL OR harga_id = 0 OR harga_id = '')");
                } else {
                    $this->db->where('harga_id', $db_target_id);
                }
                $this->db->where('product_id', $product_id);
                $this->db->delete('routing');

                // 2. Insert BOMs and keep mapping of Old ID -> New ID
                $bom_id_map = [];
                foreach ($source_boms as $bom) {
                    $old_bom_id = $bom['bom_line_id'];
                    unset($bom['bom_line_id']);
                    $bom['harga_id'] = $db_target_id;
                    $bom['created_at'] = date('Y-m-d H:i:s');

                    $this->db->insert('bom_lines', $bom);
                    $bom_id_map[$old_bom_id] = $this->db->insert_id();
                }

                // 3. Insert Routings
                foreach ($source_routings as $routing) {
                    unset($routing['routing_id']);
                    $routing['harga_id'] = $db_target_id;
                    $routing['created_at'] = date('Y-m-d H:i:s');

                    // Update bom_line_id if it referenced a BOM from the source
                    if (!empty($routing['bom_line_id']) && isset($bom_id_map[$routing['bom_line_id']])) {
                        $routing['bom_line_id'] = $bom_id_map[$routing['bom_line_id']];
                    }

                    $this->db->insert('routing', $routing);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                echo json_encode(['success' => false, 'message' => 'Gagal memproses data di database.']);
            } else {
                echo json_encode(['success' => true, 'message' => 'Berhasil menyalin setup ke variasi yang dipilih.']);
            }

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function get_old_bom_materials()
    {
        header('Content-Type: application/json');
        $product_id = $this->input->get('product_id', true);

        $this->db->select('b.bahan_id, b.bahan_nama, MAX(bl.calc_type) as old_calc_type');
        $this->db->from('bom_lines bl');
        $this->db->join('tbl_material m', 'm.material_id = bl.bahan_id', 'left');
        $this->db->join('tbl_bahan b', 'b.bahan_id = m.bahan_id', 'left');
        $this->db->where('bl.product_id', $product_id);
        $this->db->where('b.bahan_id IS NOT NULL');
        $this->db->group_by('b.bahan_id');

        $data = $this->db->get()->result();
        echo json_encode(['status' => 'success', 'data' => $data]);
    }

    public function get_dynamic_bom()
    {
        header('Content-Type: application/json');
        $product_id = $this->input->get('product_id', true);

        $this->db->select('dm.*, CONCAT(m.varian, " [", b.jenis_barang, "]") as bahan_nama, s.satuan_nama as unit_name', FALSE);
        $this->db->from('tbl_bom_dynamic_mapping dm');
        $this->db->join('tbl_material m', 'm.material_id = dm.bahan_id', 'left');
        $this->db->join('tbl_bahan b', 'b.bahan_id = m.bahan_id', 'left');
        $this->db->join('tbl_satuan s', 's.satuan_id = b.satuan_id', 'left');
        $this->db->where('dm.product_id', $product_id);

        $data = $this->db->get()->result();

        if ($data) {
            foreach ($data as $row) {
                // Get latest purchase price for the specific bahan_id by joining tbl_material
                $latest_price = $this->db->select('pd.harga_beli, m.konversi')
                    ->from('tbl_pembelian_detail pd')
                    ->join('tbl_material m', 'm.material_id = pd.material_id', 'left')
                    ->where('pd.material_id', $row->bahan_id)
                    ->order_by('pd.detail_id', 'DESC')
                    ->limit(1)
                    ->get()->row();

                if (!$latest_price) {
                    $latest_price = $this->db->select('bb.harga_beli, m.konversi')
                        ->from('tbl_belanja_bahan bb')
                        ->join('tbl_material m', 'm.bahan_id = bb.bahan_id', 'left')
                        ->where('m.material_id', $row->bahan_id)
                        ->order_by('bb.belanja_id', 'DESC')
                        ->limit(1)
                        ->get()->row();
                }

                $harga_beli = (isset($latest_price) && isset($latest_price->harga_beli)) ? (float) $latest_price->harga_beli : 0;

                // Konversi defaults to 1 if not found or <= 0
                $konversi = (isset($latest_price) && isset($latest_price->konversi) && (float) $latest_price->konversi > 0) ? (float) $latest_price->konversi : 1;

                $auto_cost = $harga_beli / $konversi;

                // If manual_cost is set, use it; otherwise use auto-calculated
                if (isset($row->manual_cost) && $row->manual_cost !== null && (float) $row->manual_cost > 0) {
                    $row->unit_cost = (float) $row->manual_cost;
                    $row->cost_is_manual = true;
                } else {
                    $row->unit_cost = $auto_cost;
                    $row->cost_is_manual = false;
                }
            }
        }

        echo json_encode(['status' => 'success', 'data' => $data]);
    }

    public function save_dynamic_bom()
    {
        header('Content-Type: application/json');
        $id = $this->input->post('id', true);
        $data = [
            'product_id' => $this->input->post('product_id', true),
            'attribute_key' => $this->input->post('attribute_key', true),
            'attribute_value' => $this->input->post('attribute_value', true),
            'bahan_id' => $this->input->post('bahan_id', true),
            'calc_type' => $this->input->post('calc_type', true),
            'multiplier_key' => $this->input->post('multiplier_key', true),
            'divider_factor' => $this->input->post('divider_factor', true) ?: 1,
            'scrap_factor' => $this->input->post('scrap_factor', true) ?: 0
        ];

        // Only include manual_cost if explicitly passed (preserve existing value on normal save)
        $manual_cost_raw = $this->input->post('manual_cost');
        if ($manual_cost_raw !== null) {
            $data['manual_cost'] = ($manual_cost_raw !== '') ? (float) $manual_cost_raw : null;
        }

        if ($id) {
            $this->db->where('id', $id);
            $this->db->update('tbl_bom_dynamic_mapping', $data);
        } else {
            $this->db->insert('tbl_bom_dynamic_mapping', $data);
        }

        echo json_encode(['status' => 'success']);
    }

    public function save_dynamic_manual_cost()
    {
        header('Content-Type: application/json');
        $id = $this->input->post('id', true);
        $manual_cost_raw = $this->input->post('manual_cost', true);
        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID tidak ditemukan']);
            return;
        }
        // Empty string = reset to auto
        $manual_cost = ($manual_cost_raw !== null && $manual_cost_raw !== '') ? (float) $manual_cost_raw : null;
        $this->db->where('id', $id);
        $this->db->update('tbl_bom_dynamic_mapping', ['manual_cost' => $manual_cost]);
        echo json_encode(['status' => 'success', 'manual_cost' => $manual_cost]);
    }

    public function delete_dynamic_bom()
    {
        header('Content-Type: application/json');
        $id = $this->input->post('id', true);
        if ($id) {
            $this->db->where('id', $id);
            $this->db->delete('tbl_bom_dynamic_mapping');
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID not found']);
        }
    }
}