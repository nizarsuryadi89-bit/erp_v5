<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Utility Controller to Clear Today's Queue
 * Access: http://192.168.100.170/tgm_erp/clear_queue
 */
class Clear_queue extends MY_Controller
{
    public function index()
    {
        $date = date('Y-m-d');

        // Delete today's queue entries
        $this->db->where("DATE(created_at) = '$date'", NULL, FALSE);
        $this->db->delete('tbl_queue');

        $affected = $this->db->affected_rows();

        echo json_encode([
            'status' => 'success',
            'message' => "Deleted $affected queue entries for $date",
            'date' => $date
        ]);
    }

    public function branch($branch_id)
    {
        $date = date('Y-m-d');

        // Delete today's queue entries for specific branch
        $this->db->where('branch_id', $branch_id);
        $this->db->where("DATE(created_at) = '$date'", NULL, FALSE);
        $this->db->delete('tbl_queue');

        $affected = $this->db->affected_rows();

        echo json_encode([
            'status' => 'success',
            'message' => "Deleted $affected queue entries for branch $branch_id on $date",
            'date' => $date,
            'branch_id' => $branch_id
        ]);
    }
}
