<?php
try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=tgm_erp', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 1. Update tbl_spk
    $check_p = $db->query("SHOW COLUMNS FROM tbl_spk LIKE 'id_pengiriman'");
    if ($check_p->rowCount() == 0) {
        $db->exec("ALTER TABLE tbl_spk ADD COLUMN id_pengiriman INT(2) DEFAULT 0 AFTER resi_marketplace");
    }
    
    $check_b = $db->query("SHOW COLUMNS FROM tbl_spk LIKE 'batch_pengiriman'");
    if ($check_b->rowCount() == 0) {
        $db->exec("ALTER TABLE tbl_spk ADD COLUMN batch_pengiriman VARCHAR(50) DEFAULT NULL AFTER id_pengiriman");
    }

    // 2. Update tbl_pemesanan
    $check_pb = $db->query("SHOW COLUMNS FROM tbl_pemesanan LIKE 'batch_pengiriman'");
    if ($check_pb->rowCount() == 0) {
        $db->exec("ALTER TABLE tbl_pemesanan ADD COLUMN batch_pengiriman VARCHAR(50) DEFAULT NULL AFTER id_pengiriman");
    }

    echo "Migration Success: Shipping fields added to tbl_spk and tbl_pemesanan.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
