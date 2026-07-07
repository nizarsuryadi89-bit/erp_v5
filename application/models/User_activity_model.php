<?php
class User_activity_model extends CI_Model
{
    public function get_daily_activity_summary($userid = null)
    {
        if ($userid) {
            $this->db->where('a.user_id', $userid);
        }
    
        $this->db->select('u.username, DATE(a.timestamp) as tanggal, COUNT(*) as total_aktivitas');
        $this->db->from('user_activity_log a');
        $this->db->join('users u', 'u.user_id = a.user_id');
        $this->db->group_by('a.user_id, DATE(a.timestamp)');
        $this->db->order_by('tanggal', 'DESC');
        return $this->db->get()->result();
    }

    public function get_activity_score($userid = null)
    {
        if ($userid) {
            $this->db->where('a.user_id', $userid);
        }
    
        // Calculate the performance score based on specific actions
        // Assuming 'input_pemesanan', 'input_transaksi', and 'cetak_laporan' are the actions to be scored
    
        $this->db->select('u.username, SUM(
            CASE 
                WHEN a.action = "input_pemesanan" THEN 1
                WHEN a.action = "input_transaksi" THEN 1
                WHEN a.action = "cetak_laporan" THEN 1
                ELSE 0
            END) as skor_kinerja');
        $this->db->from('user_activity_log a');
        $this->db->join('users u', 'u.user_id = a.user_id');
        $this->db->group_by('a.user_id');
        $this->db->order_by('skor_kinerja', 'DESC');
        return $this->db->get()->result();
    }

    public function get_user_daily_activity()
    {
        $query = $this->db->query("
            SELECT u.username, DATE(a.timestamp) as tanggal, COUNT(*) as jumlah
            FROM user_activity_log a
            JOIN users u ON u.id = a.user_id
            GROUP BY u.username, DATE(a.timestamp)
            ORDER BY tanggal ASC
        ");
        return $query->result();
    }

   

    

}
