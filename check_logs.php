<?php
$mysqli = new mysqli("localhost", "root", "", "tgm_erp");
$res = $mysqli->query("SELECT * FROM tbl_stok_log WHERE material_id = 130 AND gudang_id = 1 ORDER BY created_at DESC LIMIT 10");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
?>
