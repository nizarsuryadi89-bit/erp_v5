<?php
class Produk_model extends CI_Model
{
    public function get_paginated($limit, $start, $keyword = null)
    {
        if ($keyword) {
            $this->db->like('product_nama', $keyword);
        }
        return $this->db->get('tbl_produk', $limit, $start)->result();
    }

    public function count_all($keyword = null)
    {
        if ($keyword) {
            $this->db->like('product_nama', $keyword);
        }
        return $this->db->count_all_results('tbl_produk');
    }

}