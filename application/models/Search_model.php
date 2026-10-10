<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Search_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Menjalankan query SQL yang dihasilkan oleh AI
     * 
     * @param string $sql
     * @return array
     */
    public function execute_ai_sql($sql)
    {
        // Matikan db_debug agar error tidak menyebabkan 500 error page
        $orig_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;

        $query = $this->db->query($sql);

        if (!$query) {
            $error = $this->db->error();
            $this->db->db_debug = $orig_debug; // Kembalikan state
            return [
                'status' => 'error',
                'message' => $error['message'] ?? 'Unknown SQL Error',
                'code' => $error['code'] ?? 0
            ];
        }

        $this->db->db_debug = $orig_debug; // Kembalikan state

        // Jika query menghasilkan result set (SELECT)
        if (is_object($query)) {
            return [
                'status' => 'success',
                'data' => $query->result_array(),
                'num_rows' => $query->num_rows(),
                'fields' => $query->list_fields(),
                'type' => 'select'
            ];
        }

        // Jika query non-SELECT (UPDATE/DELETE/INSERT)
        return [
            'status' => 'success',
            'affected_rows' => $this->db->affected_rows(),
            'type' => 'non-select'
        ];
    }
}
