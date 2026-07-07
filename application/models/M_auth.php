<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_auth extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        // $this->load->database();
    }

    public function login($username)
    {
        $this->db->where('username', $username);
        $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = users.karyawan_id', 'left');
        $this->db->join('roles', 'roles.role_id = users.role_id', 'left');
        return $this->db->get('users');
    }

    public function get_user_roles($user_id)
    {
        $this->db->select('r.role_name');
        // $this->db->from('user_roles ur');
        $this->db->join('roles r', 'ur.role_id = r.role_id');
        $this->db->where('ur.user_id', $user_id);
        $query = $this->db->get();
        return $query->result_array();
    }

}