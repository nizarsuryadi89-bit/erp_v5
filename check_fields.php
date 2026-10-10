<?php
define('BASEPATH', 'index.php');
include('index.php');
$CI =& get_instance();
$CI->load->database();
$fields = $CI->db->list_fields('tbl_product');
foreach ($fields as $field) {
    echo $field . "\n";
}
