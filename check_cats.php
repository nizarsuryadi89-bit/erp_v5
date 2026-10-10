<?php
define('BASEPATH', 'index.php');
include('index.php');
$CI =& get_instance();
$CI->load->database();
$query = $CI->db->query("SELECT kategori_id, kategori_nama FROM tbl_kategori");
foreach ($query->result() as $row) {
    echo $row->kategori_id . ": " . $row->kategori_nama . "\n";
}
