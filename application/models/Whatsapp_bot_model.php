<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp_bot_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function cek_transaksi($nospk)
    {
        $this->db->select('
            tbl_pemesanan.*, 
            tbl_pemesanan.status_pembayaran as status_bayar_trx, 
            tbl_konsumen.nama_konsumen, 
            tbl_produksi.status_produksi, 
            tbl_produksi.tgl_selesai, 
            tbl_produksi.dateline_tgl, 
            tbl_produksi.dateline_jam,
            tbl_pembayaran.total_tagihan,
            tbl_pembayaran.total_bayar
        ');
        $this->db->from('tbl_pemesanan');
        $this->db->where('tbl_pemesanan.nospk', $nospk);
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_pemesanan.konsumen_id', 'left');
        $this->db->join('tbl_produksi', 'tbl_produksi.nospk = tbl_pemesanan.nospk', 'left');
        $this->db->join('tbl_pembayaran', 'tbl_pembayaran.nospk = tbl_pemesanan.nospk', 'left');
        
        return $this->db->get()->row();
    }

    public function save_log($sender, $name, $message_in, $message_out, $status)
    {
        $data = array(
            'sender_number' => $sender,
            'sender_name' => $name,
            'message_in' => $message_in,
            'message_out' => $message_out,
            'status_send' => $status,
            'created_at' => date('Y-m-d H:i:s')
        );
        return $this->db->insert('whatsapp_logs', $data);
    }
}
?>
