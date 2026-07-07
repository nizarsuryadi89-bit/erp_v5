<?php
$mysqli = new mysqli("localhost", "root", "", "tgm_erp");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}
$result = $mysqli->query("SHOW COLUMNS FROM tbl_produksi");
$has_ripping = false;
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . "\n";
    if ($row['Field'] === 'ripping_group') $has_ripping = true;
}

if (!$has_ripping) {
    echo "Adding ripping_group column...\n";
    $mysqli->query("ALTER TABLE tbl_produksi ADD COLUMN ripping_group VARCHAR(50) NULL AFTER status_produksi");
    echo "Done adding ripping_group.\n";
} else {
    echo "ripping_group already exists.\n";
}
$mysqli->close();
?>
