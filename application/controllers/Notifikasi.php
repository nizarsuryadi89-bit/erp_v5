<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifikasi extends MY_Controller {

    public function baca($id) {
        $this->db->where('id', $id)->where('user_id', $this->session->userdata('id'));
        $this->db->update('tbl_notifikasi', ['is_read' => 1]);
        
        $notif = $this->db->get_where('tbl_notifikasi', ['id' => $id])->row();
        
        if ($notif && !empty($notif->link)) {
            redirect($notif->link);
        } else {
            redirect('dashboard');
        }
    }
    
    public function baca_semua() {
        $this->db->where('user_id', $this->session->userdata('id'));
        $this->db->update('tbl_notifikasi', ['is_read' => 1]);
        redirect($_SERVER['HTTP_REFERER'] ?? 'dashboard');
    }
}
