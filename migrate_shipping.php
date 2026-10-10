<?php
define('BASEPATH', 'dummy');
require_once 'index.php';
$CI =& get_instance();
$CI->load->database();
// Migration: Add id_pengiriman to tbl_spk
$CI->db->query("ALTER TABLE tbl_spk ADD COLUMN id_pengiriman INT(2) DEFAULT 0 AFTER resi_marketplace");
echo "Migration successful: column id_pengiriman added to tbl_spk.";
