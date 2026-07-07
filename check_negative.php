<?php
$mysqli = new mysqli("localhost", "root", "", "tgm_erp");
$res = $mysqli->query("SELECT m.material_id, b.bahan_nama, m.varian, m.konversi, sg.qty, sg.gudang_id FROM tbl_material m JOIN tbl_bahan b ON m.bahan_id = b.bahan_id LEFT JOIN tbl_stok_gudang sg ON m.material_id = sg.material_id WHERE sg.qty < 0 AND sg.gudang_id = 1 LIMIT 10");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
?>
