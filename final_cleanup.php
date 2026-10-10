<?php
$mysqli = new mysqli("127.0.0.1", "root", "", "tgm_erp");

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

// 1. Get all Marketplace SPKs
$res = $mysqli->query("SELECT nospk FROM tbl_pemesanan WHERE id_jenis_transaksi = 2");
$spks = [];
while ($row = $res->fetch_assoc()) {
    $spks[] = "'" . $row['nospk'] . "'";
}

if (empty($spks)) {
    // Try picking up from tbl_spk just in case it wasn't converted to pemesanan yet
    $res = $mysqli->query("SELECT nospk FROM tbl_spk WHERE metode_pesan = 2");
    while ($row = $res->fetch_assoc()) {
        $spks[] = "'" . $row['nospk'] . "'";
    }
}

if (!empty($spks)) {
    $spk_list = implode(",", $spks);
    
    // Delete detail_produksi
    $mysqli->query("DELETE FROM detail_produksi WHERE produksi_id IN (SELECT produksi_id FROM tbl_produksi WHERE nospk IN ($spk_list))");
    
    // Delete tbl_produksi
    $mysqli->query("DELETE FROM tbl_produksi WHERE nospk IN ($spk_list)");
    
    // Delete tbl_pemesanan_detail
    $mysqli->query("DELETE FROM tbl_pemesanan_detail WHERE nospk IN ($spk_list)");
    
    // Delete tbl_pemesanan
    $mysqli->query("DELETE FROM tbl_pemesanan WHERE nospk IN ($spk_list)");
    
    // Delete tbl_spk
    $mysqli->query("DELETE FROM tbl_spk WHERE nospk IN ($spk_list) OR metode_pesan = 2");
    
    echo "Success: Cleared " . count($spks) . " Marketplace transaction records.";
} else {
    echo "No Marketplace data found.";
}

$mysqli->close();
?>
