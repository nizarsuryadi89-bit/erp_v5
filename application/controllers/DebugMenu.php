<?php
class DebugMenu extends CI_Controller {
    public function index() {
        $this->load->database();
        echo "--- MENU ---\n";
        $menus = $this->db->get('menu')->result();
        foreach($menus as $m) print_r($m);
        
        echo "\n--- SUBMENU ---\n";
        $submenus = $this->db->get('submenu')->result();
        foreach($submenus as $s) print_r($s);
    }
}
