<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Print_queue_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->auto_setup_table();
    }

    /**
     * Auto setup table tbl_print_queue if it doesn't exist
     */
    private function auto_setup_table()
    {
        if (!$this->db->table_exists('tbl_print_queue')) {
            $sql = "CREATE TABLE tbl_print_queue (
                print_id INT AUTO_INCREMENT PRIMARY KEY,
                nospk VARCHAR(100) NOT NULL,
                jenis_dokumen VARCHAR(50) NOT NULL DEFAULT 'SPK',
                printer_name VARCHAR(100) NOT NULL DEFAULT 'Lantai 2',
                status ENUM('pending', 'printing', 'printed', 'failed') NOT NULL DEFAULT 'pending',
                jumlah_cetak INT NOT NULL DEFAULT 1,
                created_at DATETIME NOT NULL,
                printed_at DATETIME NULL,
                error_message TEXT NULL,
                INDEX idx_nospk (nospk),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->db->query($sql);
        }
    }

    /**
     * Add an SPK to the print queue if not already queued
     * STRICT FILTER: Only queue if it contains at least one Large Format item (kategori_id = 2)
     */
    public function insert_queue($nospk, $jenis_dokumen = 'SPK', $printer_name = 'Lantai 2', $jumlah_cetak = 1)
    {
        // 1. Strict Category check: Large Format (kategori_id = 2) only
        $this->db->from('tbl_pemesanan_detail pd');
        $this->db->join('tbl_product p', 'p.product_id = pd.product_id');
        $this->db->where('pd.nospk', $nospk);
        $this->db->where('p.kategori_id', 2);
        $has_large_format = $this->db->get()->num_rows() > 0;

        if (!$has_large_format) {
            return false; // Skip Digital Print or non-Large-Format SPK
        }

        // Check if already in queue with pending/printing status to avoid double print
        $this->db->where('nospk', $nospk);
        $this->db->where('jenis_dokumen', $jenis_dokumen);
        $this->db->group_start();
        $this->db->where('status', 'pending');
        $this->db->or_where('status', 'printing');
        $this->db->group_end();
        $exists = $this->db->get('tbl_print_queue')->num_rows() > 0;

        if ($exists) {
            return false; // Already queued
        }

        $data = [
            'nospk' => $nospk,
            'jenis_dokumen' => $jenis_dokumen,
            'printer_name' => $printer_name,
            'status' => 'pending',
            'jumlah_cetak' => $jumlah_cetak,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('tbl_print_queue', $data);
    }

    /**
     * Get all pending print queue items
     */
    public function get_pending_queue()
    {
        $this->db->select('q.*, pms.created_at as spk_created_at, pms.status_pembayaran, c.konsumen_nama');
        $this->db->from('tbl_print_queue q');
        $this->db->join('tbl_pemesanan pms', 'pms.nospk = q.nospk', 'left');
        $this->db->join('tbl_konsumen c', 'c.konsumen_id = pms.konsumen_id', 'left');
        $this->db->where('q.status', 'pending');
        $this->db->order_by('q.print_id', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Update status of print queue
     */
    public function update_status($print_id, $status, $error_message = NULL)
    {
        $data = ['status' => $status];
        if ($status === 'printed') {
            $data['printed_at'] = date('Y-m-d H:i:s');
        }
        if ($error_message !== NULL) {
            $data['error_message'] = $error_message;
        }

        $this->db->where('print_id', $print_id);
        return $this->db->update('tbl_print_queue', $data);
    }

    /**
     * Get all queue items (for dashboard/auditing)
     */
    public function get_all_queue($limit = 100)
    {
        $this->db->select('q.*, pms.created_at as spk_created_at, pms.status_pembayaran, c.konsumen_nama');
        $this->db->from('tbl_print_queue q');
        $this->db->join('tbl_pemesanan pms', 'pms.nospk = q.nospk', 'left');
        $this->db->join('tbl_konsumen c', 'c.konsumen_id = pms.konsumen_id', 'left');
        $this->db->order_by('q.created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }
}
