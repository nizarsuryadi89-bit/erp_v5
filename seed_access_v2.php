<?php
$conn = new mysqli("localhost", "root", "", "tgm_erp");
if ($conn->connect_error)
    die("Connection failed");

$link = "produksi_request";
$menu_name = "Pengajuan Material";
$icon_fa = "bi-file-earmark-plus";
$icon_menu = "bi-file-earmark-plus"; // fallback
$urutan = 21;

// 1. Create Menu
$check = $conn->query("SELECT id FROM menu WHERE link = '$link'");
if ($check->num_rows == 0) {
    $sql = "INSERT INTO menu (nama_menu, alias, icon_fa, link, icon_menu, urutan, status) 
            VALUES ('$menu_name', '$menu_name', '$icon_fa', '$link', '$icon_menu', $urutan, '1')";
    if ($conn->query($sql)) {
        $menu_id = $conn->insert_id;
        echo "Menu created with ID: $menu_id\n";
    } else {
        echo "Error creating menu: " . $conn->error . "\n";
        exit;
    }
} else {
    $menu_id = $check->fetch_assoc()['id'];
    echo "Menu already exists with ID: $menu_id\n";
}

// 2. Grant Access (Role 2: Deskprint, Role 3: Kasir, Role 4: Operator Produksi)
$roles = [2, 3, 4];
foreach ($roles as $role_id) {
    $check_acc = $conn->query("SELECT id FROM tbl_menu_access WHERE role_id = $role_id AND menu_id = $menu_id");
    if ($check_acc->num_rows == 0) {
        if ($conn->query("INSERT INTO tbl_menu_access (role_id, menu_id, is_readonly) VALUES ($role_id, $menu_id, 0)")) {
            echo "Access granted for Role $role_id\n";
        }
    } else {
        echo "Access already exists for Role $role_id\n";
    }
}

$conn->close();
