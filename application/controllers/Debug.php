<?php
class Debug extends CI_Controller {
    public function index() {
        $nospk = '2-16-0426-610';
        $detail = $this->db->get_where('tbl_pemesanan_detail', ['nospk' => $nospk])->result_array();
        $pembayaran = $this->db->get_where('tbl_pembayaran', ['nospk' => $nospk])->row_array();
        $pemesanan = $this->db->get_where('tbl_pemesanan', ['nospk' => $nospk])->row_array();
        echo json_encode([
            'detail' => $detail,
            'pembayaran' => $pembayaran,
            'pemesanan' => $pemesanan
        ]);
    }
}
