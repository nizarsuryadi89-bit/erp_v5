<?php
$mysqli = new mysqli("localhost", "root", "", "tgm_erp");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

$res = $mysqli->query("SHOW COLUMNS FROM tbl_produksi_bom");
while ($row = $res->fetch_assoc()) {
    echo $row['Field'] . "\n";
}
?>
