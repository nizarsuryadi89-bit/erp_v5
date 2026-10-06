<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_katalog extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");
        
        // Setup CORS for external Next.js access
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        
        if ($this->input->method() === 'options') {
            exit;
        }
    }

    public function list()
    {
        $q = $this->input->get('q', TRUE);
        $kategori = (int) $this->input->get('kategori_id', TRUE);
        $page = (int) $this->input->get('page', TRUE) ?: 1;
        $per_page = (int) $this->input->get('limit', TRUE) ?: 100;

        // 1. Get Categories
        $categories = $this->db->get('tbl_kategori')->result_array();

        // 2. Count total
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
        $page = max(1, min($page, $total_pages > 0 ? $total_pages : 1));
        $offset = ($page - 1) * $per_page;

        // 3. Fetch Products
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

        // 4. Attach price summary & image URL
        foreach ($products as &$p) {
            $this->db->select('MIN(harga_1) as min_p, MAX(harga_1) as max_p');
            $this->db->where('product_id', $p['product_id']);
            $price_sum = $this->db->get('tbl_harga_product')->row();
            $p['min_price'] = $price_sum->min_p ?: 0;
            $p['max_price'] = $price_sum->max_p ?: 0;
            
            // Image URL resolution
            $p['image_url'] = !empty($p['product_img']) ? base_url('assets/images/product/' . $p['product_img']) : base_url('assets/images/noproduct.jpg');
        }

        // Output JSON
        echo json_encode([
            'status' => 'success',
            'data' => [
                'products' => $products,
                'categories' => $categories,
                'pagination' => [
                    'total_items' => $total_products,
                    'total_pages' => $total_pages,
                    'current_page' => $page,
                    'per_page' => $per_page
                ],
                'perusahaan' => $this->db->get('tbl_perusahaan')->row_array()
            ]
        ]);
    }

    public function detail()
    {
        $id = (int) $this->input->get('id', TRUE);
        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID Produk diperlukan']);
            return;
        }

        $this->db->select('p.*, k.kategori_nama, s.subkategori_nama, st.satuan_nama');
        $this->db->select('COALESCE((SELECT SUM(qty) FROM tbl_pemesanan_detail WHERE product_id = p.product_id), 0) as terjual');
        $this->db->from('tbl_product p');
        $this->db->join('tbl_kategori k', 'p.kategori_id = k.kategori_id', 'left');
        $this->db->join('tbl_subkategori s', 'p.subkategori_id = s.subkategori_id', 'left');
        $this->db->join('tbl_satuan st', 'p.satuan_id = st.satuan_id', 'left');
        $this->db->where('p.product_id', $id);
        
        $product = $this->db->get()->row_array();

        if (!$product) {
            echo json_encode(['status' => 'error', 'message' => 'Produk tidak ditemukan']);
            return;
        }

        $product['image_url'] = !empty($product['product_img']) ? base_url('assets/images/product/' . $product['product_img']) : base_url('assets/images/noproduct.jpg');

        $this->db->select('hp.*, s.side_name as ket_1_label, mp.mesin_nama as jenisprint_label');
        $this->db->from('tbl_harga_product hp');
        $this->db->join('tbl_side s', 'hp.ket_1 = s.side_id', 'left');
        $this->db->join('tbl_mesin_printing mp', 'hp.jenisprint_id = mp.id', 'left');
        $this->db->where('hp.product_id', $id);
        $this->db->order_by('hp.qty_min', 'ASC');
        $prices = $this->db->get()->result_array();

        $product['prices'] = $prices;

        echo json_encode([
            'status' => 'success',
            'data' => $product
        ]);
    }
}
