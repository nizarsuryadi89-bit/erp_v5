<?php
require 'application/config/database.php';
$db_host = $db['default']['hostname'];
$db_user = $db['default']['username'];
$db_pass = $db['default']['password'];
$db_name = $db['default']['database'];

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$res = $conn->query("SELECT p.produksi_id, p.nospk, p.metadata, p.harga_id, h.metadata as h_meta, p.ket_1 FROM tbl_produksi p LEFT JOIN tbl_harga_product h ON p.harga_id = h.harga_id WHERE p.nospk LIKE '%3-7-0626-46%'");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
$conn->close();
