<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'tgm_erp');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function dumpTable($conn, $table) {
    echo "--- TABLE: $table ---\n";
    $result = $conn->query("SELECT * FROM $table");
    if ($result) {
        $fields = $result->fetch_fields();
        foreach ($fields as $field) echo $field->name . "\t";
        echo "\n";
        while($row = $result->fetch_assoc()) {
            echo implode("\t", $row) . "\n";
        }
    } else {
        echo "Error: " . $conn->error . "\n";
    }
    echo "\n";
}

dumpTable($conn, 'menu');
dumpTable($conn, 'submenu');
dumpTable($conn, 'tbl_divisi'); // To see role names
dumpTable($conn, 'tbl_menu_access');
dumpTable($conn, 'tbl_submenu_access');

$conn->close();
?>
