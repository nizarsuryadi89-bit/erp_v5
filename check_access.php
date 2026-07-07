<?php
$conn = new mysqli("localhost", "root", "", "tgm_erp");
$r = $conn->query("SELECT m.link FROM tbl_menu_access a JOIN menu m ON a.menu_id = m.id WHERE a.role_id = 3");
while ($row = $r->fetch_assoc())
    echo $row["link"] . "\n";
$conn->close();
