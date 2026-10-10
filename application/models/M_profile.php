<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_profile extends CI_Model {

    function  getprofile(){
        $kode = $this->session->userdata('id');
        $this->db->join('tbl_karyawan','tbl_karyawan.karyawan_id = users.karyawan_id');
        $this->db->join('tbl_jabatan','tbl_jabatan.jabatan_id = tbl_karyawan.jabatan_id');
        $this->db->where('user_id',$kode);
        return $this->db->get('users');
    }     

     function  getprofilebyid($id){

      
        $this->db->join('tbl_jabatan','tbl_jabatan.jabatan_id = tbl_karyawan.jabatan_id');
        $this->db->where('karyawan_id',$id);
        return $this->db->get('tbl_karyawan');
    }    

    public function get_by_id($id) {
        return $this->db->get_where('users', ['user_id' => $id]);
    }

    public function update_password($user_id, $new_password)
        {
            $this->db->where('user_id', $user_id);
            return $this->db->update('users', ['password' => $new_password]);
        }



}
