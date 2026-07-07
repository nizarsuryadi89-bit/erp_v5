<?php
define('BASEPATH', '/Applications/XAMPP/xamppfiles/htdocs/tgm_erp/system/');
require_once '/Applications/XAMPP/xamppfiles/htdocs/tgm_erp/application/config/database.php';

$db_config = $db['default'];

try {
    $dsn = "mysql:host={$db_config['hostname']};dbname={$db_config['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $db_config['username'], $db_config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    // Cek apakah tabel system_audit_logs ada
    $q = $pdo->query("SHOW TABLES LIKE 'system_audit_logs'");
    $table_exists = $q->fetch();
    
    if ($table_exists) {
        echo "TABEL system_audit_logs ADA\n";
    } else {
        echo "TABEL system_audit_logs TIDAK ADA!\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
