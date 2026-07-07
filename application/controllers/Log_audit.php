<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_DB_query_builder $db
 * @property CI_Input $input
 * @property CI_Session $session
 * @property CI_URI $uri
 * @property M_transaksi $M_transaksi
 * @property M_penjualan $M_penjualan
 * @property M_master $M_master
 */
class Log_audit extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // Optimization: Include necessary fields for the view
        $this->db->select('l.log_id, l.created_at, l.log_type, l.log_action, l.ip_address, l.log_details, l.controller, l.method, u.username');
        $this->db->from('tbl_audit_log l');
        $this->db->join('users u', 'u.user_id = l.user_id', 'left');
        $this->db->order_by('l.created_at', 'DESC');
        $this->db->limit(500);

        $data['title'] = 'Audit Trail - Log Aktivitas';
        $data['logs'] = $this->db->get()->result_array();

        $this->load_dev_view('admin/log_audit/index', $data);
    }

    /**
     * Purge old logs to keep DB slim
     * Default: Delete logs older than 3 days
     */
    public function purge()
    {
        $days = (int) $this->input->get('days', true) ?: 3;
        $threshold = date('Y-m-d H:i:s', strtotime("-$days days"));

        // Count before delete for logging
        $this->db->where('created_at <', $threshold);
        $count = $this->db->count_all_results('tbl_audit_log');

        if ($count > 0) {
            $this->db->where('created_at <', $threshold);
            $this->db->delete('tbl_audit_log');

            // [AUDIT LOG] Record the purge action itself
            $this->_log_manual("System Purge: Cleaned Audit Logs", "Deleted $count rows older than $threshold", 'DELETE');

            $this->session->set_flashdata('success', "Berhasil membersihkan $count data log (Data > $days hari).");
        } else {
            $this->session->set_flashdata('info', "Tidak ada data log lama untuk dibersihkan.");
        }

        redirect('log_audit');
    }

    /**
     * Detail Log (AJAX)
     */
    public function detail($id)
    {
        $id = (int)$id;
        // Here we select '*' because we need the heavy 'details' JSON for the modal
        $log = $this->db->get_where('tbl_audit_log', ['log_id' => $id])->row_array();
        if ($log) {
            echo json_encode($log);
        }
    }

    /**
     * Halaman dokumentasi teknis pengembangan
     */
    public function changelog()
    {
        $data['title'] = 'Developer Changelog & Documentation';
        $this->load_dev_view('admin/log_audit/changelog', $data);
    }
}
