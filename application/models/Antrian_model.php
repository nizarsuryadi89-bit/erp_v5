<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Antrian_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Ambil nomor antrian baru
     */
    public function ambil_nomor_antrian($counter_id)
    {
        // Hitung nomor antrian hari ini untuk counter ini
        $today = date('Y-m-d');

        $result = $this->db->select('COUNT(*) as count')
            ->from('tbl_antrian')
            ->where('counter_id', $counter_id)
            ->where('DATE(waktu_ambil)', $today)
            ->get()
            ->row_array();

        $nomor_urut = ($result['count'] ?? 0) + 1;
        $counter = $this->db->get_where('tbl_antrian_counter', ['counter_id' => $counter_id])->row_array();

        // Format: C1-001, C2-002 dll
        $counter_prefix = 'C' . $counter['counter_nomorurut'];
        $nomor_antrian = $counter_prefix . '-' . str_pad($nomor_urut, 3, '0', STR_PAD_LEFT);

        // Insert ke database
        $data = [
            'nomor_antrian' => $nomor_antrian,
            'counter_id' => $counter_id,
            'counter_kode' => $counter['counter_kode'],
            'status' => 'menunggu',
            'waktu_ambil' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->db->insert('tbl_antrian', $data)) {
            $antrian_id = $this->db->insert_id();
            $antrian = $this->get_antrian($antrian_id);
            return $antrian;
        }

        return false;
    }

    /**
     * Get detail antrian
     */
    public function get_antrian($antrian_id)
    {
        return $this->db->select('ta.*, tac.counter_nama, tac.counter_kode as master_counter_kode')
            ->from('tbl_antrian ta')
            ->join('tbl_antrian_counter tac', 'tac.counter_id = ta.counter_id')
            ->where('ta.antrian_id', $antrian_id)
            ->get()
            ->row_array();
    }

    /**
     * Get antrian berdasarkan nomor
     */
    public function get_antrian_by_nomor($nomor_antrian)
    {
        return $this->db->select('ta.*, tac.counter_nama, tac.counter_kode as master_counter_kode')
            ->from('tbl_antrian ta')
            ->join('tbl_antrian_counter tac', 'tac.counter_id = ta.counter_id')
            ->where('ta.nomor_antrian', $nomor_antrian)
            ->get()
            ->row_array();
    }

    /**
     * Panggil antrian (deskprint)
     */
    public function panggil_antrian($antrian_id, $user_id)
    {
        $antrian = $this->get_antrian($antrian_id);

        if (!$antrian) {
            return ['status' => 'error', 'message' => 'Antrian tidak ditemukan'];
        }

        if ($antrian['status'] != 'menunggu') {
            return ['status' => 'error', 'message' => 'Antrian sudah dipanggil atau selesai'];
        }

        // Update status antrian
        $update_data = [
            'status' => 'dipanggil',
            'waktu_panggil' => date('Y-m-d H:i:s'),
            'user_id' => $user_id
        ];

        $this->db->where('antrian_id', $antrian_id);
        if ($this->db->update('tbl_antrian', $update_data)) {
            // Log ke history
            $this->_log_antrian_history($antrian_id, 'menunggu', 'dipanggil', $user_id);
            return ['status' => 'success', 'message' => 'Antrian dipanggil'];
        }

        return ['status' => 'error', 'message' => 'Gagal memanggil antrian'];
    }

    /**
     * Lanjut ke status sedang dilayani
     */
    public function mulai_layani($antrian_id, $user_id)
    {
        $antrian = $this->get_antrian($antrian_id);

        if (!$antrian) {
            return ['status' => 'error', 'message' => 'Antrian tidak ditemukan'];
        }

        $update_data = [
            'status' => 'sedang_dilayani',
            'user_id' => $user_id
        ];

        $this->db->where('antrian_id', $antrian_id);
        if ($this->db->update('tbl_antrian', $update_data)) {
            $this->_log_antrian_history($antrian_id, $antrian['status'], 'sedang_dilayani', $user_id);
            return ['status' => 'success', 'message' => 'Mulai melayani'];
        }

        return ['status' => 'error', 'message' => 'Gagal memulai layanan'];
    }

    /**
     * Selesaikan antrian
     */
    public function selesai_antrian($antrian_id, $user_id)
    {
        $antrian = $this->get_antrian($antrian_id);

        if (!$antrian) {
            return ['status' => 'error', 'message' => 'Antrian tidak ditemukan'];
        }

        $update_data = [
            'status' => 'selesai',
            'waktu_selesai' => date('Y-m-d H:i:s'),
            'user_id' => $user_id
        ];

        $this->db->where('antrian_id', $antrian_id);
        if ($this->db->update('tbl_antrian', $update_data)) {
            $this->_log_antrian_history($antrian_id, $antrian['status'], 'selesai', $user_id);
            return ['status' => 'success', 'message' => 'Antrian selesai'];
        }

        return ['status' => 'error', 'message' => 'Gagal menyelesaikan antrian'];
    }

    /**
     * Batalkan antrian
     */
    public function batalkan_antrian($antrian_id, $user_id, $keterangan = '')
    {
        $antrian = $this->get_antrian($antrian_id);

        if (!$antrian) {
            return ['status' => 'error', 'message' => 'Antrian tidak ditemukan'];
        }

        $update_data = [
            'status' => 'batal',
            'user_id' => $user_id,
            'keterangan' => $keterangan
        ];

        $this->db->where('antrian_id', $antrian_id);
        if ($this->db->update('tbl_antrian', $update_data)) {
            $this->_log_antrian_history($antrian_id, $antrian['status'], 'batal', $user_id, $keterangan);
            return ['status' => 'success', 'message' => 'Antrian dibatalkan'];
        }

        return ['status' => 'error', 'message' => 'Gagal membatalkan antrian'];
    }

    /**
     * Get antrian yang menunggu untuk counter tertentu
     */
    public function get_antrian_menunggu($counter_id, $limit = 10)
    {
        return $this->db->select('ta.*, tac.counter_nama, tac.counter_kode as master_counter_kode')
            ->from('tbl_antrian ta')
            ->join('tbl_antrian_counter tac', 'tac.counter_id = ta.counter_id')
            ->where('ta.counter_id', $counter_id)
            ->where('ta.status', 'menunggu')
            ->order_by('ta.waktu_ambil', 'ASC')
            ->limit($limit)
            ->get()
            ->result_array();
    }

    /**
     * Get antrian berikutnya untuk di panggil
     */
    public function get_antrian_berikutnya($counter_id)
    {
        return $this->db->select('ta.*, tac.counter_nama, tac.counter_kode as master_counter_kode')
            ->from('tbl_antrian ta')
            ->join('tbl_antrian_counter tac', 'tac.counter_id = ta.counter_id')
            ->where('ta.counter_id', $counter_id)
            ->where('ta.status', 'menunggu')
            ->order_by('ta.waktu_ambil', 'ASC')
            ->limit(1)
            ->get()
            ->row_array();
    }

    /**
     * Get status antrian saat ini untuk display
     */
    public function get_antrian_display($counter_id)
    {
        return $this->db->select('ta.*, tac.counter_nama, tac.counter_kode as master_counter_kode')
            ->from('tbl_antrian ta')
            ->join('tbl_antrian_counter tac', 'tac.counter_id = ta.counter_id')
            ->where('ta.counter_id', $counter_id)
            ->where_in('ta.status', ['dipanggil', 'sedang_dilayani'])
            ->order_by('ta.waktu_panggil', 'DESC')
            ->limit(1)
            ->get()
            ->row_array();
    }

    /**
     * Get semua counter
     */
    public function get_all_counter()
    {
        return $this->db->where('status', 'aktif')
            ->order_by('counter_nomorurut', 'ASC')
            ->get('tbl_antrian_counter')
            ->result_array();
    }

    /**
     * Get available counter with least load (for public auto-assignment)
     * Returns single counter row or null
     */
    public function get_best_counter()
    {
        $today = date('Y-m-d');

        // Subquery: count waiting antrian per counter for today
        $sub = $this->db->select('counter_id, COUNT(*) as waiting_count')
            ->from('tbl_antrian')
            ->where('DATE(waktu_ambil)', $today)
            ->where('(status = "menunggu" OR status = "dipanggil")')
            ->group_by('counter_id')
            ->get_compiled_select();

        $sql = "SELECT tac.*, COALESCE(tw.waiting_count,0) as waiting_count
                FROM tbl_antrian_counter tac
                LEFT JOIN ({$sub}) tw ON tw.counter_id = tac.counter_id
                WHERE tac.status = 'aktif'
                ORDER BY COALESCE(tw.waiting_count,0) ASC, tac.counter_nomorurut ASC
                LIMIT 1";

        $query = $this->db->query($sql);
        $row = $query->row_array();
        return $row ?: null;
    }

    /**
     * Get statistik antrian harian
     */
    public function get_statistik_harian($counter_id = null, $tanggal = null)
    {
        if (!$tanggal) {
            $tanggal = date('Y-m-d');
        }

        $this->db->select('
            COUNT(CASE WHEN status="selesai" THEN 1 END) as total_selesai,
            COUNT(CASE WHEN status="menunggu" THEN 1 END) as total_menunggu,
            COUNT(CASE WHEN status="dipanggil" THEN 1 END) as total_dipanggil,
            COUNT(CASE WHEN status="batal" THEN 1 END) as total_batal,
            COUNT(*) as total_antrian
        ')
            ->from('tbl_antrian')
            ->where('DATE(waktu_ambil)', $tanggal);

        if ($counter_id) {
            $this->db->where('counter_id', $counter_id);
        }

        return $this->db->get()->row_array();
    }

    /**
     * Log perubahan status antrian
     */
    private function _log_antrian_history($antrian_id, $status_lama, $status_baru, $user_id, $keterangan = '')
    {
        $data = [
            'antrian_id' => $antrian_id,
            'status_lama' => $status_lama,
            'status_baru' => $status_baru,
            'user_id' => $user_id,
            'keterangan' => $keterangan
        ];

        $this->db->insert('tbl_antrian_history', $data);
    }

    /**
     * Get riwayat antrian
     */
    public function get_riwayat_antrian($antrian_id)
    {
        return $this->db->select('tah.*, u.username')
            ->from('tbl_antrian_history tah')
            ->join('users u', 'u.user_id = tah.user_id', 'left')
            ->where('tah.antrian_id', $antrian_id)
            ->order_by('tah.waktu_perubahan', 'ASC')
            ->get()
            ->result_array();
    }
}
