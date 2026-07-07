<?php
$conn = mysqli_connect("localhost", "root", "", "tgm_print");
$result = mysqli_query($conn, "SELECT nospk FROM tbl_pembayaran ORDER BY tgl_bayar DESC LIMIT 5");
while ($row = mysqli_fetch_assoc($result)) {
    print_r($row);
}
?>