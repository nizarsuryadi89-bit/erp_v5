<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('insert_notifikasi')) {
    function insert_notifikasi($user_id, $judul, $pesan, $link) {
        $CI =& get_instance();
        $data = [
            'user_id' => $user_id,
            'judul' => $judul,
            'pesan' => $pesan,
            'link' => $link,
            'tipe' => 'info',
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        return $CI->db->insert('tbl_notifikasi', $data);
    }
}

if (!function_exists('broadcast_notifikasi')) {
    function broadcast_notifikasi($judul, $pesan, $link) {
        $CI =& get_instance();
        $users = $CI->db->select('user_id')->get('users')->result();
        
        $data = [];
        $now = date('Y-m-d H:i:s');
        foreach ($users as $u) {
            $data[] = [
                'user_id' => $u->user_id,
                'judul' => $judul,
                'pesan' => $pesan,
                'link' => $link,
                'tipe' => 'info',
                'is_read' => 0,
                'created_at' => $now
            ];
        }
        
        if (!empty($data)) {
            return $CI->db->insert_batch('tbl_notifikasi', $data);
        }
        return false;
    }
}
