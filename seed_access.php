<?php
$conn = new mysqli("localhost", "root", "", "tgm_erp");
if ($conn->connect_error)
    die("Connection failed");

// 1. Add Menu for Produksi_Request
$menu_name = "Pengajuan Material";
$link = "produksi_request";
$icon = "bi-file-earmark-plus";
$urutan = 20;

// Check if exists
$check = $conn->query("SELECT id FROM menu WHERE link = '$link'");
if ($check->num_rows == 0) {
    $sql = "INSERT INTO menu (nama_menu, alias, link, icon_fa, urutan, status) 
            VALUES ('$menu_name', '$menu_name', '$link', '$icon', $urutan, 1)";
    $conn->query($sql);
    $menu_id = $conn->insert_id;
    echo "Menu created with ID: $menu_id\n";
} else {
    $menu_id = $check->fetch_assoc()['id'];
    echo "Menu already exists with ID: $menu_id\n";
}

// 2. Grant Access to Roles (2, 3, 4)
// Role 2: Deskprint, Role 3: Kasir, Role 4: Operator Produksi
$roles = [2, 3, 4];
foreach ($roles as $role_id) {
    $check_acc = $conn->query("SELECT id FROM tbl_menu_access WHERE role_id = $role_id AND menu_id = $menu_id");
    if ($check_acc->num_rows == 0) {
        $conn->query("INSERT INTO tbl_menu_access (role_id, menu_id, is_readonly) VALUES ($role_id, $menu_id, 0)");
        echo "Access granted for Role $role_id\n";
    } else {
        echo "Access already exists for Role $role_id\n";
    }
}

$conn->close();
