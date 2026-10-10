<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Katalog extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Load the model needed
        $this->load->model('M_master');
    }

    public function index()
    {
        $data = [
            'title' => 'Katalog Produk',
            'perusahaan' => $this->db->get('tbl_perusahaan')->row_array()
        ];

        $q = $this->input->get('q', TRUE);
        $kategori = (int) $this->input->get('kategori_id', TRUE);
        $page = (int) $this->input->get('page', TRUE) ?: 1;
        $per_page = 12; // 12 products per page

        // 1. Get total products for calculation
        $this->db->from('tbl_product p');
        $this->db->group_start();
        $this->db->where('p.product_deskripsi !=', 'Manual entry via POS');
        $this->db->or_where('p.product_deskripsi IS NULL');
        $this->db->group_end();
        $this->db->where('EXISTS (SELECT 1 FROM tbl_harga_product hp WHERE hp.product_id = p.product_id AND hp.harga_1 > 0)', NULL, FALSE);
        if ($q) $this->db->like('p.product_nama', $q);
        if ($kategori) $this->db->where('p.kategori_id', $kategori);
        $total_products = $this->db->count_all_results();

        $total_pages = ceil($total_products / $per_page);
        $page = max(1, min($page, $total_pages));
        $offset = ($page - 1) * $per_page;
        if ($offset < 0) $offset = 0;

        // 2. Fetch paginated products
        $this->db->select('p.*, k.kategori_nama, s.subkategori_nama, st.satuan_nama');
        $this->db->select('COALESCE((SELECT SUM(qty) FROM tbl_pemesanan_detail WHERE product_id = p.product_id), 0) as terjual');
        $this->db->from('tbl_product p');
        $this->db->join('tbl_kategori k', 'p.kategori_id = k.kategori_id', 'left');
        $this->db->join('tbl_subkategori s', 'p.subkategori_id = s.subkategori_id', 'left');
        $this->db->join('tbl_satuan st', 'p.satuan_id = st.satuan_id', 'left');

        $this->db->group_start();
        $this->db->where('p.product_deskripsi !=', 'Manual entry via POS');
        $this->db->or_where('p.product_deskripsi IS NULL');
        $this->db->group_end();
        $this->db->where('EXISTS (SELECT 1 FROM tbl_harga_product hp WHERE hp.product_id = p.product_id AND hp.harga_1 > 0)', NULL, FALSE);
        if ($q) $this->db->like('p.product_nama', $q);
        if ($kategori) $this->db->where('p.kategori_id', $kategori);

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

        // Load the public views
        $this->load->view('layout_admin/head', $data);
        $this->load->view('public/katalog_index', $data);
        // Kita tidak memakai footer admin agar script admin tidak mengganggu
    }

    public function produk($id = null)
    {
        if ($id == null) {
            redirect(base_url('katalog'));
        }
        $id = (int) $id;

        $this->db->select('p.*, k.kategori_nama, s.subkategori_nama, st.satuan_nama');
        $this->db->select('COALESCE((SELECT SUM(qty) FROM tbl_pemesanan_detail WHERE product_id = p.product_id), 0) as terjual');
        $this->db->from('tbl_product p');
        $this->db->join('tbl_kategori k', 'p.kategori_id = k.kategori_id', 'left');
        $this->db->join('tbl_subkategori s', 'p.subkategori_id = s.subkategori_id', 'left');
        $this->db->join('tbl_satuan st', 'p.satuan_id = st.satuan_id', 'left');
        $this->db->where('p.product_id', $id);
        $product = $this->db->get()->row_array();

        if (!$product) {
            show_404();
            return;
        }

        $this->db->select('hp.*, s.side_name as ket_1_label, mp.mesin_nama as jenisprint_label');
        $this->db->from('tbl_harga_product hp');
        $this->db->join('tbl_side s', 'hp.ket_1 = s.side_id', 'left');
        $this->db->join('tbl_mesin_printing mp', 'hp.jenisprint_id = mp.id', 'left');
        $this->db->where('hp.product_id', $id);
        $this->db->order_by('hp.qty_min', 'ASC');
        $product['prices'] = $this->db->get()->result_array();

        $this->db->select('MIN(harga_1) as min_p, MAX(harga_1) as max_p');
        $this->db->where('product_id', $id);
        $price_sum = $this->db->get('tbl_harga_product')->row();
        $product['min_price'] = $price_sum->min_p ?? 0;
        $product['max_price'] = $price_sum->max_p ?? 0;

        // Fetch similar products
        $this->db->select('p.*');
        $this->db->select('COALESCE((SELECT SUM(qty) FROM tbl_pemesanan_detail WHERE product_id = p.product_id), 0) as terjual');
        $this->db->from('tbl_product p');
        $this->db->where('EXISTS (SELECT 1 FROM tbl_harga_product hp WHERE hp.product_id = p.product_id AND hp.harga_1 > 0)', NULL, FALSE);
        $this->db->where('p.kategori_id', $product['kategori_id']);
        $this->db->where('p.product_id !=', $id);
        $this->db->order_by('RAND()');
        $this->db->limit(6);
        $similar_products = $this->db->get()->result_array();

        foreach ($similar_products as &$sp) {
            $this->db->select('MIN(harga_1) as min_p, MAX(harga_1) as max_p');
            $this->db->where('product_id', $sp['product_id']);
            $price_sum_sp = $this->db->get('tbl_harga_product')->row();
            $sp['min_price'] = $price_sum_sp->min_p ?? 0;
            $sp['max_price'] = $price_sum_sp->max_p ?? 0;
        }

        $data = [
            'title' => 'Katalog: ' . $product['product_nama'],
            'perusahaan' => $this->db->get('tbl_perusahaan')->row_array(),
            'product' => $product,
            'similar_products' => $similar_products
        ];

        // Load the public views
        $this->load->view('layout_admin/head', $data);
        $this->load->view('public/katalog_detail', $data);
    }

    public function cetak($id = null)
    {
        if ($id == null) {
            redirect(base_url('katalog'));
        }
        $id = (int) $id;

        $this->db->select('p.*, k.kategori_nama, s.subkategori_nama, st.satuan_nama');
        $this->db->from('tbl_product p');
        $this->db->join('tbl_kategori k', 'p.kategori_id = k.kategori_id', 'left');
        $this->db->join('tbl_subkategori s', 'p.subkategori_id = s.subkategori_id', 'left');
        $this->db->join('tbl_satuan st', 'p.satuan_id = st.satuan_id', 'left');
        $this->db->where('p.product_id', $id);
        $product = $this->db->get()->row_array();

        if (!$product) {
            show_404();
            return;
        }

        $this->db->select('hp.*, s.side_name as ket_1_label, mp.mesin_nama as jenisprint_label');
        $this->db->from('tbl_harga_product hp');
        $this->db->join('tbl_side s', 'hp.ket_1 = s.side_id', 'left');
        $this->db->join('tbl_mesin_printing mp', 'hp.jenisprint_id = mp.id', 'left');
        $this->db->where('hp.product_id', $id);
        $this->db->order_by('hp.qty_min', 'ASC');
        $product['prices'] = $this->db->get()->result_array();

        $data = [
            'product' => $product
        ];

        $this->load->view('public/cetak_brosur', $data);
    }

    public function pesanan() {
        $cart_param = $this->input->get('cart', true);
        $items = [];
        $total_items = 0;

        if ($cart_param) {
            $pairs = explode(',', $cart_param);
            foreach ($pairs as $pair) {
                $parts = explode('-', $pair);
                if (count($parts) == 2) {
                    $id = (int)$parts[0];
                    $qty = (int)$parts[1];

                    if ($id > 0 && $qty > 0) {
                        // Fetch product details
                        $this->db->select('p.product_id, p.product_nama, p.product_img, p.kategori_id');
                        $this->db->from('tbl_product p');
                        $this->db->where('p.product_id', $id);
                        $product = $this->db->get()->row_array();

                        if ($product) {
                            // Get price info
                            $this->db->select('MIN(harga_1) as min_p, MAX(harga_1) as max_p');
                            $this->db->where('product_id', $id);
                            $price_sum = $this->db->get('tbl_harga_product')->row();
                            
                            $product['min_price'] = $price_sum->min_p;
                            $product['max_price'] = $price_sum->max_p;
                            $product['qty'] = $qty;

                            $items[] = $product;
                            $total_items += $qty;
                        }
                    }
                }
            }
        }

        $data = [
            'title' => 'Ringkasan Pesanan',
            'perusahaan' => $this->db->get('tbl_perusahaan')->row_array(),
            'items' => $items,
            'total_items' => $total_items
        ];

        $this->load->view('public/katalog_pesanan', $data);
    }
}
