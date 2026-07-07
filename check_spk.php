<?php
$db = new mysqli('localhost', 'root', '', 'tgm_erp');
if ($db->connect_error) { die("Connection failed"); }
$spk = '2-62-0626-150';
echo "SPK: $spk\n";
$res = $db->query("SELECT * FROM tbl_produksi WHERE nospk = '$spk'");
while ($row = $res->fetch_assoc()) { print_r($row); }
echo "\nDetails:\n";
$res2 = $db->query("SELECT id, product_id, operator_id, qty, qty_selesai, finish, status_detail, is_sub_spk, parent_detail_id FROM detail_produksi WHERE nospk = '$spk'");
while ($row = $res2->fetch_assoc()) { print_r($row); }
