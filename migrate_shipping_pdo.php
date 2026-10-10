<?php
// Simple PDO Migration
try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=tgm_erp', 'root', '');
    $db->setAttribute(PDO::ATTR_ERR_CODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if column exists
    $check = $db->query("SHOW COLUMNS FROM tbl_spk LIKE 'id_pengiriman'");
    if ($check->rowCount() == 0) {
        $db->exec("ALTER TABLE tbl_spk ADD COLUMN id_pengiriman INT(2) DEFAULT 0 AFTER resi_marketplace");
        echo "Success: Column id_pengiriman added to tbl_spk.\n";
    } else {
        echo "Skipped: Column id_pengiriman already exists.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
