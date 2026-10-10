<?php
// application/controllers/Db_check_menu.php
defined('BASEPATH') OR exit('No direct script access allowed');

class Db_check_menu extends CI_Controller {
    public function index() {
        header('Content-Type: text/plain');
        $this->db->select('id, nama_menu, link, status');
        $res = $this->db->get('menu')->result_array();
        foreach($res as $row) {
            echo "ID: {$row['id']} | Status: {$row['status']} | Link: {$row['link']} | Name: {$row['nama_menu']}\n";
        }
    }
}
