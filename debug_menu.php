<?php
include 'index.php'; // Load CodeIgniter
echo "MENU TABLE:\n";
$menus = $this->db->get('menu')->result_array();
foreach($menus as $m) {
    echo "- ID: {$m['id']}, Name: {$m['nama_menu']}, Link: {$m['link']}, Order: {$m['urutan']}\n";
}
echo "\nSUBMENU TABLE:\n";
$submenus = $this->db->limit(10)->get('submenu')->result_array();
foreach($submenus as $s) {
    echo "- ID: {$s['id']}, MenuID: {$s['id_menu']}, Name: {$s['nama_submenu']}, Link: {$s['link_submenu']}\n";
}
