<?php

class Chat_model extends CI_Model
{
    public function get_all()
    {
        // Fallback or full export usage
        $this->db->select('c.*, u.username');
        $this->db->from('tbl_chat_umum c');
        $this->db->join('users u', 'u.user_id = c.user_id');
        $this->db->order_by('c.created_at', 'ASC');
        return $this->db->get()->result();
    }

    public function get_limit($limit = 50)
    {
        $this->db->select('c.*, u.username');
        $this->db->from('tbl_chat_umum c');
        $this->db->join('users u', 'u.user_id = c.user_id');
        $this->db->order_by('c.created_at', 'DESC'); // Get latest first
        $this->db->limit($limit);

        $query = $this->db->get();
        return array_reverse($query->result()); // Flip back to ASC for display
    }

    public function get_latest_id()
    {
        $this->db->select_max('chat_id');
        $query = $this->db->get('tbl_chat_umum');
        $row = $query->row();
        return $row->chat_id ?? 0;
    }

    public function insert($data)
    {
        return $this->db->insert('tbl_chat_umum', $data);
    }


}
