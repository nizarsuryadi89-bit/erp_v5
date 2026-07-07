<?php
define('BASEPATH', '/Applications/XAMPP/xamppfiles/htdocs/tgm_erp/system/');
require_once '/Applications/XAMPP/xamppfiles/htdocs/tgm_erp/application/config/database.php';

$db_config = $db['default'];

try {
    $dsn = "mysql:host={$db_config['hostname']};dbname={$db_config['database']};charset={$db_config['char_set']}";
    $pdo = new PDO($dsn, $db_config['username'], $db_config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT * FROM tbl_pengajuan_kontrak");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($rows)) {
        echo "Tabel tbl_pengajuan_kontrak kosong.\n";
    } else {
        print_r($rows);
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
