<?php
// Script to clear all Marketplace transactions
try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=tgm_erp', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get all Marketplace SPKs
    $stmt = $db->query("SELECT nospk FROM tbl_pemesanan WHERE id_jenis_transaksi = 2");
    $spks = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($spks)) {
        echo "No Marketplace transactions found.\n";
        exit;
    }

    $placeholders = implode(',', array_fill(0, count($spks), '?'));

    // 1. Get Production IDs to clean detail_produksi
    $stmt_prod = $db->prepare("SELECT produksi_id FROM tbl_produksi WHERE nospk IN ($placeholders)");
    $stmt_prod->execute($spks);
    $prod_ids = $stmt_prod->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($prod_ids)) {
        $p_placeholders = implode(',', array_fill(0, count($prod_ids), '?'));
        // Delete detail_produksi
        $db->prepare("DELETE FROM detail_produksi WHERE produksi_id IN ($p_placeholders)")->execute($prod_ids);
        // Delete tbl_produksi
        $db->prepare("DELETE FROM tbl_produksi WHERE produksi_id IN ($p_placeholders)")->execute($prod_ids);
    }

    // 2. Delete tbl_pemesanan_detail
    $db->prepare("DELETE FROM tbl_pemesanan_detail WHERE nospk IN ($placeholders)")->execute($spks);
    
    // 3. Delete tbl_pemesanan
    $db->prepare("DELETE FROM tbl_pemesanan WHERE nospk IN ($placeholders)")->execute($spks);
    
    // 4. Delete tbl_spk (Optional but good for total reset)
    $db->prepare("DELETE FROM tbl_spk WHERE nospk IN ($placeholders) OR metode_pesan = 2")->execute($spks);

    echo "Success: Cleared " . count($spks) . " Marketplace transactions.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
