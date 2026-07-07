<?php
$db = new mysqli('localhost', 'root', '', 'tgm_erp');
if ($db->connect_error) die("Connection failed: " . $db->connect_error);
$res = $db->query("SELECT * FROM tbl_temp_transaksi ORDER BY temp_id DESC LIMIT 5");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
