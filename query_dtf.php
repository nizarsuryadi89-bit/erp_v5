<?php
$db = new mysqli('localhost', 'root', '', 'tgm_erp');
if ($db->connect_error) die("Connection failed: " . $db->connect_error);
$res = $db->query("SELECT p.product_id, p.product_nama, hp.ket_1, hp.harga_1 FROM tbl_product p JOIN tbl_harga_product hp ON p.product_id = hp.product_id WHERE p.kategori_id = 9 LIMIT 20");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
