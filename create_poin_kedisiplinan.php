<?php
$mysqli = new mysqli("localhost", "root", "", "tgm_erp");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

$sql = "CREATE TABLE IF NOT EXISTS `tbl_pengaturan_kedisiplinan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `potongan_telat` decimal(5,2) NOT NULL DEFAULT '1.00',
  `potongan_alpha` decimal(5,2) NOT NULL DEFAULT '5.00',
  `potongan_izin` decimal(5,2) NOT NULL DEFAULT '1.00',
  `potongan_sakit` decimal(5,2) NOT NULL DEFAULT '1.00',
  `potongan_pulang_cepat` decimal(5,2) NOT NULL DEFAULT '3.00',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($mysqli->query($sql) === TRUE) {
    echo "Table tbl_pengaturan_kedisiplinan created successfully\n";
    
    // Insert default values if empty
    $res = $mysqli->query("SELECT COUNT(*) as count FROM tbl_pengaturan_kedisiplinan");
    $row = $res->fetch_assoc();
    if ($row['count'] == 0) {
        $mysqli->query("INSERT INTO tbl_pengaturan_kedisiplinan (id, potongan_telat, potongan_alpha, potongan_izin, potongan_sakit, potongan_pulang_cepat) VALUES (1, 1, 5, 1, 1, 3)");
    }
} else {
    echo "Error creating table: " . $mysqli->error;
}
$mysqli->close();
