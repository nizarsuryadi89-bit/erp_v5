<?php
$conn = new mysqli("localhost", "root", "", "tgm_erp");
$r = $conn->query("SELECT s.link_submenu FROM tbl_submenu_access a JOIN submenu s ON a.submenu_id = s.id WHERE a.role_id = 3");
while ($row = $r->fetch_assoc())
    echo $row["link_submenu"] . "\n";
$conn->close();
