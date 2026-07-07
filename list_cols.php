<?php
$conn = new mysqli("localhost", "root", "", "tgm_erp");
$res = $conn->query("SHOW COLUMNS FROM menu");
$cols = [];
while ($row = $res->fetch_assoc())
    $cols[] = $row['Field'];
echo implode(", ", $cols);
