<?php
$conn = new mysqli("localhost", "root", "", "tgm_erp");
$res = $conn->query("DESCRIBE menu");
while ($row = $res->fetch_assoc())
    print_r($row);
