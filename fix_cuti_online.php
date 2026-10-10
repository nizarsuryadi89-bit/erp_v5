<?php
// Script to fix tbl_quota_cuti missing columns on Live Server
$mysqli = new mysqli('localhost', 'tgmprin2_nizar', 'nizar152489', 'tgmprin2_erp_2026');

if ($mysqli->connect_errno) {
    die("Gagal connect DB: " . $mysqli->connect_error);
}

$columns = [
    'menikah_terpakai' => "INT(11) NOT NULL DEFAULT '0'",
    'duka_terpakai' => "INT(11) NOT NULL DEFAULT '0'",
    'sakitkel_terpakai' => "INT(11) NOT NULL DEFAULT '0'",
    'melahirkansuami_terpakai' => "INT(11) NOT NULL DEFAULT '0'",
    'melahirkan_terpakai' => "INT(11) NOT NULL DEFAULT '0'",
    'menikah_sisa' => "INT(11) NOT NULL DEFAULT '0'",
    'duka_sisa' => "INT(11) NOT NULL DEFAULT '0'",
    'sakitkel_sisa' => "INT(11) NOT NULL DEFAULT '0'",
    'melahirkansuami_sisa' => "INT(11) NOT NULL DEFAULT '0'",
    'melahirkan_sisa' => "INT(11) NOT NULL DEFAULT '0'"
];

foreach ($columns as $col => $type) {
    $check = $mysqli->query("SHOW COLUMNS FROM tbl_quota_cuti LIKE '$col'");
    if ($check->num_rows == 0) {
        $sql = "ALTER TABLE tbl_quota_cuti ADD COLUMN $col $type";
        if ($mysqli->query($sql)) {
            echo "Kolom $col berhasil ditambahkan.<br>";
        } else {
            echo "Gagal tambah $col: " . $mysqli->error . "<br>";
        }
    } else {
        echo "Kolom $col sudah ada.<br>";
    }
}

echo "Selesai.";
$mysqli->close();
