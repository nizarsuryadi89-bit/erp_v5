<?php
// Database Migration Web Script
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'tgm_erp';

echo "<h3>Memulai Migrasi Database via Web (Apache)...</h3>";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    // Try socket
    $conn = new mysqli('localhost', $user, $pass, $db, null, '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock');
}

if ($conn->connect_error) {
    die("<h3 style='color:red;'>Koneksi database gagal: " . $conn->connect_error . "</h3>");
}
echo "<p style='color:green;'>Koneksi database sukses!</p>";

// Helper function to check if column exists
function column_exists($conn, $table, $column) {
    $result = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $result && $result->num_rows > 0;
}

// 2. Migrate tbl_harga_product
if (!column_exists($conn, 'tbl_harga_product', 'metadata')) {
    $sql = "ALTER TABLE `tbl_harga_product` ADD COLUMN `metadata` TEXT NULL AFTER `ket_1`";
    if ($conn->query($sql)) {
        echo "<p style='color:green;'>Kolom `metadata` berhasil ditambahkan ke `tbl_harga_product`!</p>";
    } else {
        echo "<p style='color:red;'>Gagal pada `tbl_harga_product`: " . $conn->error . "</p>";
    }
} else {
    echo "<p>Kolom `metadata` sudah ada di `tbl_harga_product`.</p>";
}

// 3. Migrate tbl_pemesanan_detail
if (!column_exists($conn, 'tbl_pemesanan_detail', 'metadata')) {
    $sql = "ALTER TABLE `tbl_pemesanan_detail` ADD COLUMN `metadata` TEXT NULL AFTER `keterangan`";
    if ($conn->query($sql)) {
        echo "<p style='color:green;'>Kolom `metadata` berhasil ditambahkan ke `tbl_pemesanan_detail`!</p>";
    } else {
        echo "<p style='color:red;'>Gagal pada `tbl_pemesanan_detail`: " . $conn->error . "</p>";
    }
} else {
    echo "<p>Kolom `metadata` sudah ada di `tbl_pemesanan_detail`.</p>";
}

$conn->close();
echo "<h3>Migrasi Selesai! Hapus file migrate_web.php demi keamanan.</h3>";
