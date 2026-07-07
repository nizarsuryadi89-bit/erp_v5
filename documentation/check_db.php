<?php
$conn = mysqli_connect('localhost', 'root', '', 'tgm_print');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$result = mysqli_query($conn, "SELECT nospk FROM tbl_pemesanan ORDER BY created_at DESC LIMIT 5");
while ($row = mysqli_fetch_assoc($result)) {
    print_r($row);
}
mysqli_close($conn);
