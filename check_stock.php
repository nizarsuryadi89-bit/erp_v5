<?php
$mysqli = new mysqli("localhost", "root", "", "tgm_erp");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

$res = $mysqli->query("SELECT m.material_id, b.bahan_nama, m.varian, sg.qty, sg.gudang_id FROM tbl_material m JOIN tbl_bahan b ON m.bahan_id = b.bahan_id LEFT JOIN tbl_stok_gudang sg ON m.material_id = sg.material_id WHERE b.bahan_nama LIKE '%Flexy%' LIMIT 10");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
echo "\n==== LOGS ====\n";
$res2 = $mysqli->query("SELECT * FROM tbl_stok_log WHERE material_id = 76 ORDER BY created_at DESC LIMIT 5");
while ($row = $res2->fetch_assoc()) {
    print_r($row);
}
?>
