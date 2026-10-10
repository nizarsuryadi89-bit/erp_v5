<?php
define('BASEPATH', 'true');
include 'application/config/database.php';
$db_config = $db['default'];
$conn = new mysqli($db_config['hostname'], $db_config['username'], $db_config['password'], $db_config['database']);

echo "Columns in tbl_pemesanan:\n";
$res = $conn->query("SHOW COLUMNS FROM tbl_pemesanan");
while ($row = $res->fetch_assoc()) {
    echo $row['Field'] . "\n";
}

echo "\nColumns in tbl_produksi:\n";
$res = $conn->query("SHOW COLUMNS FROM tbl_produksi");
while ($row = $res->fetch_assoc()) {
    echo $row['Field'] . "\n";
}
