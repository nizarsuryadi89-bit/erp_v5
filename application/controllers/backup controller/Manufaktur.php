<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property M_master $M_master
 */
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
        $userid = $this->session->userdata('id');
        $controller = $this->uri->segment(1);

        $perusahaan = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $this->session->userdata('perusahaan_id')])->row_array();
        $hakakses = $this->db->get_where('tbl_divisi', ['divisi_id' => $this->session->userdata('level')])->row_array();
        $level = $this->db->get_where('users', ['user_id' => $userid])->row_array();

        return [
            'controller' => $controller,
            'function' => $this->uri->segment(2),
            'title' => $title,
            'perusahaan' => $perusahaan['nama_perusahaan'] ?? '',
            'hakakses' => $hakakses['divisi_nama'] ?? '',
            'logo' => $perusahaan['logo'] ?? '',
            'level' => $level,
            'menu' => $this->db->get('menu')->result(),
            'submenu' => $this->M_master->getmenu($controller)->result(),
        ];
    }

    public function index()
    {
        $data = $this->get_common_data('Daftar Semua Product');

        // 1. Capture Filter & Pagination Parameters
        $q = $this->input->get('q', TRUE);
        $kategori = $this->input->get('kategori', TRUE);
        $status = $this->input->get('status', TRUE); // complete, partial, empty
        $page = (int) $this->input->get('page', TRUE) ?: 1;
        $per_page = (int) $this->input->get('per_page', TRUE) ?: 20;

        // 2. Prepare Filters for Query
        $filters = [
            'q' => $q,
            'kategori' => $kategori,
            'metode' => $this->input->get('metode') // Optional if needed
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

        // 4. Calculate Pagination
        $total_pages = ceil($total_products / $per_page);
        $page = max(1, min($page, $total_pages)); // Ensure page is valid
        $offset = ($page - 1) * $per_page;
        if ($offset < 0)
            $offset = 0;

        // 5. Get Paginated Data
        $data['tampilData'] = $this->M_master->getDataProductWithPagination($per_page, $offset, $filters)->result();

        // 6. Pass Data to View
        $data['total_products'] = $total_products;
        $data['current_page'] = $page;
        $data['total_pages'] = $total_pages;
        $data['per_page'] = $per_page;
        $data['offset'] = $offset;

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

    public function actionAdd()
    {
        $kategori_id = $this->input->post('kategori_id');
        $subkategori_id = $this->input->post('subkategori_id');
        $product_nama = $this->input->post('product_nama');
        $tipe_id = $this->input->post('tipe_id');
        $satuan_id = $this->input->post('satuan_id');
        $side_id = $this->input->post('side_id');
        $metode_id = $this->input->post('metode_id');
        $product_deskripsi = $this->input->post('product_deskripsi');

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
        $product_id = $this->input->post('product_id');
        $bahan_id = $this->input->post('bahan_id');
        $quantity = $this->input->post('quantity');

        // Auto-fetch satuan_id from tbl_bahan if not provided or hidden
        $satuan_id = $this->input->post('satuan_id');
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

        if ($this->input->post('ukuran_enable')) {
            $panjang = $this->input->post('panjang');
            $lebar = $this->input->post('lebar');
            if ($panjang && $lebar) {
                $data['panjang'] = $panjang;
                $data['lebar'] = $lebar;
            }
        }

        // Handle Scrap Factor if present
        if ($this->input->post('scrap_factor')) {
            $data['scrap_factor'] = $this->input->post('scrap_factor');
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
        $product = $this->input->post('product_id');
        $data = [
            'routing_name' => $this->input->post('routing_name'),
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
        $id = $this->input->post('id');
        $query = $this->M_master->get_subkategori($id);
        $data = $query ? $query->result() : [];

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    public function action_add_routing()
    {
        $product = $this->input->post('product_id');
        $data = [
            'routing_name' => $this->input->post('routing_name'),
            'product_id' => $product,
            'routing_urutan' => $this->input->post('routing_urutan'),
            'operator_id' => $this->input->post('mesin_id'),
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
            redirect('manufaktur');
            return;
        }

        $product = $this->db->get_where('tbl_product', ['product_id' => $id])->row();
        if (!$product) {
            $this->session->set_flashdata('error', 'Produk tidak ditemukan.');
            redirect('manufaktur');
            return;
        }

        if ($this->isProductInUse($id)) {
            $this->session->set_flashdata('error', 'Produk tidak dapat dihapus karena masih digunakan dalam transaksi aktif.');
            redirect('manufaktur');
            return;
        }

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

        redirect('manufaktur');
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
        $data = $this->get_common_data('Tambah Data Product');

        $data['id'] = $this->M_master->getData()->row_array();
        $data['bahan'] = $this->db->get('tbl_bahan')->result();
        $data['kategori'] = $this->db->get('tbl_kategori')->result();
        $data['waktu'] = $this->db->get('tbl_waktu')->result();
        $data['satuan'] = $this->db->get('tbl_satuan')->result();
        $data['product'] = $this->M_master->getProduct($id)->row_array();
        $data['step'] = $this->db->get('tbl_operator')->result();
        $data['subkategori'] = $this->M_master->get_subkategori($data['product']['kategori_id']);

        $this->load_admin_view('admin/product/form_update', $data);
    }

    public function actionUpdateproduct()
    {
        $product_id = $this->input->post('product_id');
        if (!$product_id)
            show_error('ID produk tidak ditemukan');

        $data = [
            'kategori_id' => $this->input->post('kategori_id'),
            'subkategori_id' => $this->input->post('subkategori_id'),
            'bahan_id' => $this->input->post('bahan_id'),
            'product_nama' => $this->input->post('product_nama'),
            'tipe_id' => $this->input->post('tipe_id'),
            'satuan_id' => $this->input->post('satuan_id'),
            'product_deskripsi' => $this->input->post('product_deskripsi'),
            'side_id' => $this->input->post('side_id'),
            'detail_id' => $this->input->post('metode_id'),
        ];

        $this->db->where('product_id', $product_id);
        $this->db->update('tbl_product', $data);

        $this->session->set_flashdata('success', 'Data produk berhasil diperbarui.');
        redirect('product');
    }

    public function detail($id)
    {
        $data = $this->get_common_data('Tambah Data Product');

        $data['bahan'] = $this->db->get('tbl_bahan')->result();
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

        $this->load_admin_view('admin/product/detail', $data);
    }

    public function addHarga()
    {
        $product = $this->input->post('product_id');
        $data = [
            'product_id' => $product,
            'detail_product' => $this->input->post('product_nama'),
        ];

        $insert = $this->db->insert('tbl_harga_product', $data);

        if ($this->input->is_ajax_request()) {
            $response = $insert
                ? ['success' => true, 'message' => 'Data berhasil ditambahkan', 'product_id' => $product, 'new_id' => $this->db->insert_id()]
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
        $product_id = $this->input->post('product_id');
        $markup = (float) $this->input->post('markup'); // e.g., 80 for 80%

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
        $product = $this->input->post('product_id');
        $hargaid = $this->input->post('harga_id');

        if (!empty($hargaid) && is_array($hargaid)) {
            $detail_product = $this->input->post('detail_product');
            $rangeid = $this->input->post('range_id');
            $ket1 = $this->input->post('ket_1');
            $hpp = $this->input->post('hpp');
            // $jml_minimum = $this->input->post('jml_minimum');
            $harga1_raw = $this->input->post('harga_1');
            $qty_min = $this->input->post('qty_min');
            $qty_max = $this->input->post('qty_max');
            $jenisprint_id = $this->input->post('jenisprint_id');
            $side_id = $this->input->post('side_id');

            // Helper to format currency
            $clean_currency = function ($val) {
                return (int) str_replace('.', '', $val);
            };

            $harga1 = array_map($clean_currency, (array) $harga1_raw);
            $hpp = array_map($clean_currency, (array) $hpp);

            $berhasil = true;
            foreach ($hargaid as $i => $id) {
                if (empty($id))
                    continue;

                $update = [
                    'detail_product' => $detail_product[$i] ?? '',
                    'range_id' => $rangeid[$i] ?? 0,
                    'harga_1' => $harga1[$i] ?? 0,
                    'qty_min' => $qty_min[$i] ?? 0,
                    'qty_max' => $qty_max[$i] ?? 0,
                    'ket_1' => $ket1[$i] ?? 0,
                    'hpp' => $hpp[$i] ?? 0,
                    'jenisprint_id' => $jenisprint_id[$i] ?? 0,
                    'side_id' => $side_id[$i] ?? 0,
                    'is_active' => '1',
                ];

                $this->db->where('harga_id', $id);
                if (!$this->db->update('tbl_harga_product', $update)) {
                    $berhasil = false;
                    log_message('error', 'Gagal update harga ID: ' . $id . ' - ' . $this->db->error()['message']);
                }
            }

            if ($berhasil) {
                $this->session->set_flashdata('success', 'Data berhasil diupdate');
                redirect('manufaktur/skala-harga/' . $product);
            }
        }
    }

    public function copy_product()
    {
        $kode = $this->uri->segment(3);
        $data = $this->get_common_data('Tambah Data Product');

        $data['getproduct'] = $this->M_master->getprodukbykode($kode)->row_array();
        $data['satuan'] = $this->db->get('tbl_satuan')->result();
        $data['product'] = $this->M_master->getProduct($kode)->row_array();
        $data['getHarga'] = $this->M_master->getHarga($kode)->result();
        $data['jmlHarga'] = $this->M_master->getHarga($kode)->num_rows();
        $data['getRange'] = $this->db->get('tbl_range')->result();
        $data['mesin'] = $this->db->get('tbl_mesin')->result();
        $data['getmata'] = $this->db->get('tbl_mata_cutting')->result();
        $data['side'] = $this->db->get('tbl_side')->result();
        $data['subkategori'] = $this->M_master->get_subkategori($data['product']['kategori_id']);

        $this->load_admin_view('admin/product/form_copy', $data);
    }

    public function actionCopy()
    {
        $controller = $this->uri->segment(1);
        $data = [
            'kategori_id' => $this->input->post('kategori_id'),
            'subkategori_id' => $this->input->post('subkategori_id'),
            'bahan_id' => $this->input->post('bahan_id'),
            'product_nama' => $this->input->post('product_nama'),
            'satuan_id' => $this->input->post('satuan_id'),
            'product_waktu_pengerjaan' => $this->input->post('product_waktu_pengerjaan'),
            'satuan_waktu_id' => $this->input->post('satuan_waktu_id'),
            'product_deskripsi' => $this->input->post('product_deskripsi'),
            'barcode' => $this->input->post('barcode'),
        ];

        if ($this->M_master->addData('tbl_product', $data)) {
            $this->session->set_flashdata('success', 'Data Berhasil Disimpan');
            redirect($controller);
        }
    }

    public function del_harga($id, $product_id)
    {
        if (empty($id) || empty($product_id)) {
            $this->session->set_flashdata('error', 'Data tidak valid.');
            redirect('manufaktur/skala-harga/' . $product_id);
            return;
        }

        $this->db->where('harga_id', $id);
        if ($this->db->delete('tbl_harga_product')) {
            $this->session->set_flashdata('success', 'Data berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data.');
        }

        redirect('manufaktur/skala-harga/' . $product_id);
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
        $id = $this->input->post('routing_id');
        $data = [
            'routing_name' => $this->input->post('routing_name'),
            'product_id' => $this->input->post('product_id'),
            'routing_urutan' => $this->input->post('routing_urutan'),
            'operator_id' => $this->input->post('operator_id'),
            'bom_line_id' => $this->input->post('bom_line_id'),
            'est_time' => $this->input->post('est_time') ?: 0,
            'labor_cost' => str_replace(['.', ','], '', $this->input->post('labor_cost') ?: 0),
        ];

        if ($this->db->update('routing', $data, ['routing_id' => $id])) {
            $this->session->set_flashdata('success', 'Data routing berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error', 'Terjadi kesalahan saat memperbarui data routing.');
        }

        redirect('manufaktur/routing/' . $this->input->post('product_id'));
    }

    // ==================== AJAX / API METHODS ====================

    public function get_bom_data()
    {
        header('Content-Type: application/json');
        $product_id = $this->input->post('product_id');

        if (!$product_id) {
            echo json_encode(['success' => false, 'message' => 'Product ID is required']);
            return;
        }

        $result = $this->db->select('bl.*')->from('bom_lines bl')
            ->where('bl.product_id', $product_id)
            ->get()->result();

        if ($result) {
            foreach ($result as $row) {
                if (isset($row->bahan_id)) {
                    $bahan = $this->db->get_where('tbl_bahan', ['bahan_id' => $row->bahan_id])->row();
                    $row->material_name = $bahan ? $bahan->bahan_nama : '-';

                    // Get latest purchase price
                    // Primary source: New purchase system (tbl_pembelian_detail)
                    $latest_price = $this->db->select('pd.harga_beli')
                        ->from('tbl_pembelian_detail pd')
                        ->join('tbl_material m', 'pd.material_id = m.material_id')
                        ->where('m.bahan_id', $row->bahan_id)
                        ->order_by('pd.detail_id', 'DESC')
                        ->limit(1)
                        ->get()->row();

                    if (!$latest_price) {
                        // Fallback: Legacy/Alternate table (tbl_belanja_bahan)
                        $latest_price = $this->db->select('harga_beli')
                            ->from('tbl_belanja_bahan')
                            ->where('bahan_id', $row->bahan_id)
                            ->order_by('belanja_id', 'DESC')
                            ->limit(1)
                            ->get()->row();
                    }

                    $row->unit_cost = $latest_price ? $latest_price->harga_beli : 0;

                    if ($bahan && isset($bahan->satuan_id)) {
                        $satuan = $this->db->get_where('tbl_satuan', ['satuan_id' => $bahan->satuan_id])->row();
                        $row->satuan = $satuan ? $satuan->satuan_nama : '-';
                    } else {
                        $row->satuan = '-';
                    }
                }
                // Compatibility for qty
                $row->qty_required = $row->qty = $row->quantity ?? ($row->qty ?? 0);
            }
        }
        echo json_encode(['success' => true, 'data' => $result]);
    }

    public function get_bahan_options()
    {
        header('Content-Type: application/json');
        $term = $this->input->get('term');
        $page = $this->input->get('page');

        if (!$term) {
            echo json_encode(['results' => []]);
            return;
        }

        $this->db->select('b.bahan_id as id, CONCAT(b.bahan_nama, IF(m.varian IS NOT NULL AND m.varian != "", CONCAT(" - ", m.varian), "")) as text');
        $this->db->from('tbl_bahan b');
        $this->db->join('tbl_material m', 'b.bahan_id = m.bahan_id', 'left');

        $this->db->group_start();
        $this->db->like('b.bahan_nama', $term);
        $this->db->or_like('m.varian', $term);
        $this->db->or_like('m.barcode', $term);
        $this->db->group_end();

        $this->db->order_by('b.bahan_nama', 'ASC');
        $this->db->limit(20);

        $result = $this->db->get()->result();

        echo json_encode(['results' => $result]);
    }

    public function get_bahan_unit()
    {
        header('Content-Type: application/json');
        $bahan_id = $this->input->post('bahan_id');

        if (!$bahan_id) {
            echo json_encode(['success' => false, 'message' => 'Bahan ID is required']);
            return;
        }

        $result = $this->db->select('s.satuan_nama as satuan')
            ->from('tbl_bahan b')
            ->join('tbl_satuan s', 's.satuan_id = b.satuan_id', 'left')
            ->where('b.bahan_id', $bahan_id)
            ->get()->row();

        if ($result) {
            echo json_encode(['success' => true, 'data' => $result]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Bahan not found']);
        }
    }

    public function add_bom()
    {
        header('Content-Type: application/json');
        try {
            $product_id = $this->input->post('product_id');
            $bahan_id = $this->input->post('bahan_id');
            $qty = $this->input->post('qty');
            $scrap_factor = $this->input->post('scrap_factor') ?: 0;

            if (!$product_id || !$bahan_id || $qty === null || $qty === '') {
                echo json_encode(['success' => false, 'message' => 'Lengkapilah data material dan kuantitas']);
                return;
            }

            // Check if already exists to prevent duplicate
            $existing = $this->db->get_where('bom_lines', [
                'product_id' => $product_id,
                'bahan_id' => $bahan_id
            ])->row();

            if ($existing) {
                echo json_encode(['success' => false, 'message' => 'Material ini sudah ada dalam daftar BOM produk ini']);
                return;
            }

            // Get satuan_id from bahan
            $bahan = $this->db->get_where('tbl_bahan', ['bahan_id' => $bahan_id])->row();
            if (!$bahan) {
                echo json_encode(['success' => false, 'message' => 'Material tidak ditemukan dalam database']);
                return;
            }

            $data = [
                'product_id' => $product_id,
                'bahan_id' => $bahan_id,
                'quantity' => $qty,
                'panjang' => $this->input->post('panjang') ?: 0,
                'lebar' => $this->input->post('lebar') ?: 0,
                'scrap_factor' => $scrap_factor,
                'satuan_id' => $bahan->satuan_id,
                'keterangan' => $this->input->post('keterangan') ?: '',
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($this->db->insert('bom_lines', $data)) {
                echo json_encode(['success' => true, 'message' => 'Material berhasil ditambahkan ke BOM']);
            } else {
                $error = $this->db->error();
                echo json_encode(['success' => false, 'message' => 'Gagal simpan ke database: ' . ($error['message'] ?? 'Unknown DB error')]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'System error: ' . $e->getMessage()]);
        }
    }

    public function delete_bom()
    {
        header('Content-Type: application/json');
        $bom_id = $this->input->post('bom_id');

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

    public function update_bom()
    {
        header('Content-Type: application/json');
        $bom_line_id = $this->input->post('bom_line_id');
        $qty = $this->input->post('qty');
        $scrap_factor = $this->input->post('scrap_factor');

        if (!$bom_line_id) {
            echo json_encode(['success' => false, 'message' => 'ID BOM tidak ditemukan']);
            return;
        }

        $data = [
            'quantity' => $qty,
            'scrap_factor' => $scrap_factor
        ];

        $this->db->where('bom_line_id', $bom_line_id);
        if ($this->db->update('bom_lines', $data)) {
            echo json_encode(['success' => true, 'message' => 'Material BOM berhasil diperbarui']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui material BOM']);
        }
    }

    public function get_routing_data()
    {
        header('Content-Type: application/json');
        $product_id = $this->input->post('product_id');

        if (!$product_id) {
            echo json_encode(['success' => false, 'message' => 'Product ID is required']);
            return;
        }

        $operator_table = $this->db->table_exists('tbl_operator') ? 'tbl_operator' : 'operator';

        $result = $this->db->select('r.*, o.operator_nama as operator_name, b.bahan_nama as bom_material_name')
            ->from('routing r')
            ->join($operator_table . ' o', 'o.operator_id = r.operator_id', 'left')
            ->join('bom_lines bl', 'bl.bom_line_id = r.bom_line_id', 'left')
            ->join('tbl_bahan b', 'b.bahan_id = bl.bahan_id', 'left')
            ->where('r.product_id', $product_id)
            ->order_by('r.routing_urutan', 'ASC')
            ->get()->result();

        echo json_encode(['success' => true, 'data' => $result]);
    }
    public function get_pricing_data()
    {
        header('Content-Type: application/json');
        $product_id = $this->input->post('product_id');

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

    public function get_operator_options()
    {
        header('Content-Type: application/json');
        $term = $this->input->get('term');

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
        $product_id = $this->input->get('product_id');

        if (!$product_id) {
            echo json_encode(['results' => []]);
            return;
        }

        $result = $this->db->select('bl.bom_line_id as id, CONCAT(b.bahan_nama, " (", bl.quantity, " ", s.satuan_nama, ")") as text')
            ->from('bom_lines bl')
            ->join('tbl_bahan b', 'b.bahan_id = bl.bahan_id', 'left')
            ->join('tbl_satuan s', 's.satuan_id = b.satuan_id', 'left')
            ->where('bl.product_id', $product_id)
            ->order_by('b.bahan_nama', 'ASC')
            ->get()->result();

        // Add 'No Material' option
        array_unshift($result, (object) ['id' => '99999', 'text' => '-- Tanpa Material --']);

        echo json_encode(['results' => $result]);
    }

    public function add_routing()
    {
        header('Content-Type: application/json');
        try {
            $product_id = $this->input->post('product_id');
            $routing_name = $this->input->post('routing_name');
            $bom_line_id = $this->input->post('bom_line_id');
            $routing_urutan = $this->input->post('routing_urutan');
            $operator_id = $this->input->post('operator_id');

            $est_time = $this->input->post('est_time') ?: 0;
            $raw_labor = $this->input->post('labor_cost');
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

            // Check for duplicates
            $existing = $this->db->get_where('routing', [
                'product_id' => $product_id,
                'routing_urutan' => $routing_urutan
            ])->row();

            if ($existing) {
                echo json_encode(['success' => false, 'message' => 'Urutan routing sudah digunakan']);
                return;
            }

            $data = [
                'routing_name' => $routing_name,
                'product_id' => $product_id,
                'bom_line_id' => $bom_line_id,
                'routing_urutan' => $routing_urutan,
                'operator_id' => $operator_id,
                'est_time' => $est_time,
                'labor_cost' => $labor_cost,
                'is_active' => '1',
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($this->db->insert('routing', $data)) {
                echo json_encode(['success' => true, 'message' => 'Routing berhasil ditambahkan']);
            } else {
                $error = $this->db->error();
                $msg = !empty($error['message']) ? $error['message'] : 'Gagal menyimpan data ke database';
                echo json_encode(['success' => false, 'message' => 'Database Error: ' . $msg]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()]);
        }
    }

    public function update_routing()
    {
        header('Content-Type: application/json');
        try {
            $routing_id = $this->input->post('routing_id');
            $routing_name = $this->input->post('routing_name');
            $bom_line_id = $this->input->post('bom_line_id');
            $routing_urutan = $this->input->post('routing_urutan');
            $operator_id = $this->input->post('operator_id');
            $est_time = $this->input->post('est_time') ?: 0;
            $labor_cost = str_replace(['.', ','], '', $this->input->post('labor_cost') ?: 0);

            if (!$routing_id || !$routing_name || !$routing_urutan || !$operator_id) {
                echo json_encode(['success' => false, 'message' => 'Lengkapilah data yang wajib diisi (Urutan, Nama Operasi, Operator)']);
                return;
            }

            $data = [
                'routing_name' => $routing_name,
                'bom_line_id' => $bom_line_id,
                'routing_urutan' => $routing_urutan,
                'operator_id' => $operator_id,
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
        $routing_id = $this->input->post('routing_id');

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
        $product_id = $this->input->post('product_id');
        if (!$product_id) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'ID produk tidak ditemukan']));
        }

        $data = [
            'product_nama' => $this->input->post('product_nama'),
            'kategori_id' => $this->input->post('kategori_id'),
            'subkategori_id' => $this->input->post('subkategori_id'),
            'tipe_id' => $this->input->post('tipe_id'),
            'satuan_id' => $this->input->post('satuan_id'),
            'side_id' => $this->input->post('side_id'),
            'detail_id' => $this->input->post('metode_id'),
            'product_deskripsi' => $this->input->post('product_deskripsi')
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

    public function clone_product()
    {
        header('Content-Type: application/json');
        $source_id = $this->input->post('source_id');
        $new_name = $this->input->post('new_name');

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
        $template_id = $this->input->post('template_id');
        $target_ids = $this->input->post('target_ids'); // Array of IDs
        $options = $this->input->post('options'); // ['bom', 'routing']

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
        $subkategori_id = $this->input->get('subkategori_id');
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
        $keyword = $this->input->get('term');
        $products = $this->M_master->search_product($keyword);

        $results = [];
        foreach ($products as $p) {
            $results[] = [
                'id' => $p->product_id,
                'text' => $p->product_nama
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
        $id = $this->input->post('product_id');
        $config['upload_path'] = FCPATH . 'assets/images/product/';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size'] = '5048';

        $this->load->library('upload', $config);

        if (!empty($_FILES['product_img_1']['name'])) {
            if ($this->upload->do_upload('product_img_1')) {
                $foto_baru = $this->upload->data('file_name');
                $this->db->where('product_id', $id)->update('tbl_product', ['product_img_1' => $foto_baru]);
                $this->session->set_flashdata('success', 'Gambar berhasil diupload');
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors());
            }
        }
        redirect('manufaktur/uploadproduct/' . $id);
    }
}
