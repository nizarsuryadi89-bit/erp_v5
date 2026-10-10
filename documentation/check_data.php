<?php
// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'db_erp');

// Cek koneksi
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Periksa data di tabel tbl_pengambilan_detail
$sql = "SELECT pengambilan_id, COUNT(*) as item_count, SUM(pengambilan_qty) as qty_total 
        FROM tbl_pengambilan_detail 
        GROUP BY pengambilan_id";

$result = $conn->query($sql);

echo "<h3>Data dari tbl_pengambilan_detail:</h3>";

if ($result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>Pengambilan ID</th><th>Item Count</th><th>Qty Total</th></tr>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["pengambilan_id"] . "</td>";
        echo "<td>" . $row["item_count"] . "</td>";
        echo "<td>" . $row["qty_total"] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "Tidak ada data di tabel tbl_pengambilan_detail";
}

// Periksa data di tabel tbl_pengambilan
$sql = "SELECT * FROM tbl_pengambilan";
$result = $conn->query($sql);

echo "<h3>Data dari tbl_pengambilan:</h3>";

if ($result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Karyawan ID</th><th>Gudang ID</th><th>Tanggal</th></tr>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["pengambilan_id"] . "</td>";
        echo "<td>" . $row["karyawan_id"] . "</td>";
        echo "<td>" . $row["gudang_id"] . "</td>";
        echo "<td>" . $row["pengambilan_tgl"] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "Tidak ada data di tabel tbl_pengambilan";
}

$conn->close();
?>