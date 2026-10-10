<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Page_visit_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function log_visit($user_id, $page_url, $page_title)
    {
        // Normalize URL (remove base_url if present, keep distinct path)
        // $page_url should be compliant with CI uri string or relative path

        $data = [
            'user_id' => $user_id,
            'page_url' => $page_url,
            'page_title' => $page_title
        ];

        // Check if exists
        $this->db->where('user_id', $user_id);
        $this->db->where('page_url', $page_url);
        $query = $this->db->get('tbl_page_visits');

        if ($query->num_rows() > 0) {
            // Update
            $this->db->set('visit_count', 'visit_count+1', FALSE);
            $this->db->set('last_visit', date('Y-m-d H:i:s'));
            $this->db->where('id', $query->row()->id);
            $this->db->update('tbl_page_visits');
        } else {
            // Insert
            $data['visit_count'] = 1;
            $data['last_visit'] = date('Y-m-d H:i:s');
            $this->db->insert('tbl_page_visits', $data);
        }
    }

    public function get_frequent_pages($user_id, $limit = 6)
    {
        $this->db->select('*');
        $this->db->from('tbl_page_visits');
        $this->db->where('user_id', $user_id);
        $this->db->order_by('visit_count', 'DESC');
        $this->db->order_by('last_visit', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }
}
