<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_monitor_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get Top Kasir / Customer Service Performance (Monthly)
     * Metrics: Number of SPKs created and total omset (gross).
     */
    public function get_top_kasir($date_start = null, $date_end = null) {
        if (!$date_start) $date_start = date('Y-m-01');
        if (!$date_end) $date_end = date('Y-m-t');

        $sql = "
            SELECT 
                u.user_id,
                u.username,
                k.nama_lengkap,
                COUNT(p.nospk) AS total_spk,
                COALESCE(SUM(pp.snapshot_subtotal), 0) AS total_omset
            FROM 
                users u
            JOIN 
                tbl_karyawan k ON u.karyawan_id = k.karyawan_id
            JOIN 
                tbl_pemesanan p ON p.deskprint_id = u.user_id
            LEFT JOIN 
                tbl_pembayaran_produk pp ON pp.nospk = p.nospk
            WHERE 
                DATE(p.created_at) >= ? AND DATE(p.created_at) <= ?
            GROUP BY 
                u.user_id
            ORDER BY 
                total_omset DESC, total_spk DESC
            LIMIT 10
        ";

        return $this->db->query($sql, [$date_start, $date_end])->result();
    }

    /**
     * Get Top Operator / Produksi Performance (Monthly)
     * Metrics: Number of batches completed.
     */
    public function get_top_produksi($date_start = null, $date_end = null) {
        if (!$date_start) $date_start = date('Y-m-01');
        if (!$date_end) $date_end = date('Y-m-t');

        $sql = "
            SELECT 
                u.user_id,
                u.username,
                k.nama_lengkap,
                COUNT(dp.detail_produksi_id) AS total_diselesaikan
            FROM 
                users u
            JOIN 
                tbl_karyawan k ON u.karyawan_id = k.karyawan_id
            JOIN 
                detail_produksi dp ON dp.operator_id = k.karyawan_id
            WHERE 
                DATE(dp.waktu_selesai) >= ? AND DATE(dp.waktu_selesai) <= ?
                AND dp.status_produksi = '2'
            GROUP BY 
                u.user_id
            ORDER BY 
                total_diselesaikan DESC
            LIMIT 10
        ";

        return $this->db->query($sql, [$date_start, $date_end])->result();
    }

    /**
     * Get user active status based on today's login / activity log
     */
    public function get_online_status() {
        $today = date('Y-m-d');
        
        $sql = "
            SELECT 
                u.user_id,
                u.username,
                k.nama_lengkap,
                u.last_login,
                CASE 
                    WHEN DATE(u.last_login) = ? THEN 'Online Today'
                    ELSE 'Offline'
                END AS status
            FROM 
                users u
            JOIN 
                tbl_karyawan k ON u.karyawan_id = k.karyawan_id
            WHERE u.active = 1
            ORDER BY u.last_login DESC
        ";

        return $this->db->query($sql, [$today])->result();
    }

    /**
     * Get live activity timeline from system_audit_logs
     */
    public function get_live_timeline($limit = 50) {
        $sql = "
            SELECT 
                s.*,
                u.username,
                k.nama_lengkap
            FROM 
                system_audit_logs s
            LEFT JOIN 
                users u ON s.user_id = u.user_id
            LEFT JOIN 
                tbl_karyawan k ON u.karyawan_id = k.karyawan_id
            ORDER BY 
                s.created_at DESC
            LIMIT ?
        ";

        return $this->db->query($sql, [$limit])->result();
    }

    /**
     * Get sensitive audit logs
     */
    public function get_sensitive_logs($limit = 50) {
        $sql = "
            SELECT 
                s.*,
                u.username,
                k.nama_lengkap
            FROM 
                system_audit_logs s
            LEFT JOIN 
                users u ON s.user_id = u.user_id
            LEFT JOIN 
                tbl_karyawan k ON u.karyawan_id = k.karyawan_id
            WHERE 
                s.action_type IN ('DELETE', 'UPDATE_PRICE', 'CANCEL_ORDER', 'CHANGE_DEADLINE')
            ORDER BY 
                s.created_at DESC
            LIMIT ?
        ";

        return $this->db->query($sql, [$limit])->result();
    }

}
