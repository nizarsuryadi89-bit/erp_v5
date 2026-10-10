<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_pengeluaran extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    // Method untuk mengambil data pengeluaran berdasarkan tanggal
    public function get_pengeluaran_by_date($date)
    {
        try {
            // Cek apakah tabel pengeluaran ada
            if (!$this->db->table_exists('tbl_pengeluaran')) {
                log_message('info', 'Table tbl_pengeluaran does not exist');
                return array();
            }
            
            $this->db->select('*');
            $this->db->from('tbl_pengeluaran');
            $this->db->where('DATE(tanggal)', $date);
            $this->db->order_by('tanggal', 'ASC');
            
            $result = $this->db->get()->result();
            return $result ?: array();
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_pengeluaran_by_date: ' . $e->getMessage());
            return array();
        }
    }

    // Method untuk mengambil data pengeluaran berdasarkan tanggal dan perusahaan
    public function get_pengeluaran_by_date_perusahaan($date, $perusahaan_id)
    {
        try {
            // Cek apakah tabel pengeluaran ada
            if (!$this->db->table_exists('tbl_pengeluaran')) {
                log_message('info', 'Table tbl_pengeluaran does not exist');
                return array();
            }
            
            $this->db->select('*');
            $this->db->from('tbl_pengeluaran');
            $this->db->where('DATE(tanggal)', $date);
            $this->db->where('perusahaan_id', $perusahaan_id);
            $this->db->order_by('tanggal', 'ASC');
            
            $result = $this->db->get()->result();
            return $result ?: array();
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_pengeluaran_by_date_perusahaan: ' . $e->getMessage());
            return array();
        }
    }
    
    // Method untuk mengambil data pengeluaran berdasarkan bulan
    public function get_pengeluaran_by_month($year_month)
    {
        try {
            // Cek apakah tabel pengeluaran ada
            if (!$this->db->table_exists('tbl_pengeluaran')) {
                log_message('info', 'Table tbl_pengeluaran does not exist');
                return array();
            }
            
            $this->db->select('*');
            $this->db->from('tbl_pengeluaran');
            $this->db->where('DATE_FORMAT(tanggal, "%Y-%m") =', $year_month);
            $this->db->order_by('tanggal', 'ASC');
            
            $result = $this->db->get()->result();
            return $result ?: array();
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_pengeluaran_by_month: ' . $e->getMessage());
            return array();
        }
    }
    
    // Method untuk menghitung total pengeluaran per hari dalam bulan tertentu
    public function get_daily_totals_by_month($year_month)
    {
        try {
            // Cek apakah tabel pengeluaran ada
            if (!$this->db->table_exists('tbl_pengeluaran')) {
                log_message('info', 'Table tbl_pengeluaran does not exist');
                return array();
            }
            
            $this->db->select('DATE(tanggal) as tanggal, SUM(jumlah) as total_harian, COUNT(*) as jumlah_item');
            $this->db->from('tbl_pengeluaran');
            $this->db->where('DATE_FORMAT(tanggal, "%Y-%m") =', $year_month);
            $this->db->group_by('DATE(tanggal)');
            $this->db->order_by('tanggal', 'ASC');
            
            $result = $this->db->get()->result();
            return $result ?: array();
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_daily_totals_by_month: ' . $e->getMessage());
            return array();
        }
    }
    
    // Method untuk menambah pengeluaran baru
    public function add_pengeluaran($data)
    {
        try {
            // Cek apakah tabel pengeluaran ada
            if (!$this->db->table_exists('tbl_pengeluaran')) {
                log_message('error', 'Table tbl_pengeluaran does not exist');
                return false;
            }
            return $this->db->insert('tbl_pengeluaran', $data);
        } catch (Exception $e) {
            log_message('error', 'Error in add_pengeluaran: ' . $e->getMessage());
            return false;
        }
    }
    
    // Method untuk update pengeluaran
    public function update_pengeluaran($id, $data)
    {
        try {
            // Cek apakah tabel pengeluaran ada
            if (!$this->db->table_exists('tbl_pengeluaran')) {
                log_message('error', 'Table tbl_pengeluaran does not exist');
                return false;
            }
            $this->db->where('id', $id);
            return $this->db->update('tbl_pengeluaran', $data);
        } catch (Exception $e) {
            log_message('error', 'Error in update_pengeluaran: ' . $e->getMessage());
            return false;
        }
    }
    
    // Method untuk hapus pengeluaran
    public function delete_pengeluaran($id)
    {
        try {
            // Cek apakah tabel pengeluaran ada
            if (!$this->db->table_exists('tbl_pengeluaran')) {
                log_message('error', 'Table tbl_pengeluaran does not exist');
                return false;
            }
            $this->db->where('id', $id);
            return $this->db->delete('tbl_pengeluaran');
        } catch (Exception $e) {
            log_message('error', 'Error in delete_pengeluaran: ' . $e->getMessage());
            return false;
        }
    }
    
    // Method untuk mengambil semua pengeluaran
    public function get_all_pengeluaran($limit = null, $offset = null)
    {
        try {
            // Cek apakah tabel pengeluaran ada
            if (!$this->db->table_exists('tbl_pengeluaran')) {
                log_message('info', 'Table tbl_pengeluaran does not exist');
                return array();
            }
            
            if ($limit) {
                $this->db->limit($limit, $offset);
            }
            $this->db->order_by('tanggal', 'DESC');
            $result = $this->db->get('tbl_pengeluaran')->result();
            return $result ?: array();
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_all_pengeluaran: ' . $e->getMessage());
            return array();
        }
    }
    
    // Method untuk mengambil pengeluaran berdasarkan ID
    public function get_pengeluaran_by_id($id)
    {
        try {
            // Cek apakah tabel pengeluaran ada
            if (!$this->db->table_exists('tbl_pengeluaran')) {
                log_message('error', 'Table tbl_pengeluaran does not exist');
                return null;
            }
            $result = $this->db->get_where('tbl_pengeluaran', array('id' => $id))->row();
            return $result;
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_pengeluaran_by_id: ' . $e->getMessage());
            return null;
        }
    }
}
