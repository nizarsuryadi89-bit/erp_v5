<?php
$conn = new mysqli('localhost', 'root', '', 'tgm_erp');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "=== SCHEMA tbl_produksi ===\n";
$res1 = $conn->query("DESCRIBE tbl_produksi");
while ($row = $res1->fetch_assoc()) {
    echo "{$row['Field']} - {$row['Type']}\n";
}

echo "\n=== RECENT ROW FROM tbl_produksi ===\n";
$res2 = $conn->query("SELECT * FROM tbl_produksi ORDER BY produksi_id DESC LIMIT 1");
$row = $res2->fetch_assoc();
print_r($row);
