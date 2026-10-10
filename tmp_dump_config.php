<?php
define('BASEPATH', 'TRUE');
require_once '/Applications/XAMPP/xamppfiles/htdocs/tgm_erp/application/config/database.php';
$db_info = $db['default'];

$mysqli = new mysqli($db_info['hostname'], $db_info['username'], $db_info['password'], $db_info['database']);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$result = $mysqli->query("SELECT * FROM tbl_lembur_config");
while ($row = $result->fetch_assoc()) {
    echo $row['config_key'] . ": " . $row['config_value'] . "\n";
}
$mysqli->close();
?>