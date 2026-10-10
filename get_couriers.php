<?php
// Get list of pengiriman via CI
define('BASEPATH', 'dummy');
require_once 'index.php';
$CI =& get_instance();
$CI->load->database();
$list = $CI->db->get('tbl_pengiriman')->result_array();
echo json_encode($list);
