<?php
define('BASEPATH', 'dummy');
require_once 'index.php';
$CI =& get_instance();
$CI->load->database();
// Repair mismatch: Copy marketplace_id from tbl_spk if it is 0 in tbl_pemesanan
$CI->db->query("UPDATE tbl_pemesanan p 
                JOIN tbl_spk s ON p.nospk = s.nospk 
                SET p.marketplace_id = s.id_marketplace, 
                    p.resi_marketplace = s.resi_marketplace 
                WHERE p.id_jenis_transaksi = 2 
                AND (p.marketplace_id = 0 OR p.marketplace_id IS NULL)");
echo "Repair completed for " . $CI->db->affected_rows() . " rows.";
