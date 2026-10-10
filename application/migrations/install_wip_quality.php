<?php
/** Explicit CLI-only additive installer. No historical records are enrolled or changed. */
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
if (!in_array('--install', $argv, true)) exit("Usage: php application/migrations/install_wip_quality.php --install\n");
define('BASEPATH', __DIR__);
require __DIR__ . '/../config/database.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$c = $db['default'];
$socket = file_exists('/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock') ? '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock' : null;
try {
    $connection = new mysqli($c['hostname'], $c['username'], $c['password'], $c['database'], $c['port'] ?? 3306, $socket);
    $sql = file_get_contents(__DIR__ . '/20261006_wip_quality.sql');
    foreach (explode(';', $sql) as $statement) {
        if (trim($statement)) $connection->query($statement);
    }
    $expected = [
        'tbl_wip_qc' => ['qc_id', 'produksi_id', 'target_qty', 'status', 'version', 'created_by', 'created_at', 'updated_at'],
        'tbl_wip_qc_inspection' => ['inspection_id', 'qc_id', 'revision', 'accepted_qty', 'rejected_qty', 'decision', 'note', 'request_reprint', 'inspector_user_id', 'request_key', 'payload_hash', 'inspected_at']
    ];
    foreach ($expected as $table => $columns) {
        $actual = array_column($connection->query('SHOW COLUMNS FROM ' . $table)->fetch_all(MYSQLI_ASSOC), 'Field');
        if (array_diff($columns, $actual)) throw new RuntimeException('Existing QC schema differs; no automatic alterations performed.');
        $status = $connection->query("SHOW TABLE STATUS WHERE Name = '" . $table . "'")->fetch_assoc();
        if (strtolower($status['Engine']) !== 'innodb') throw new RuntimeException('QC table is not InnoDB.');
        $count = $connection->query('SELECT COUNT(*) AS total FROM ' . $table)->fetch_assoc()['total'];
        echo $table . ': InnoDB, ' . $count . " rows\n";
    }
    $connection->close();
    echo "QC tables installed. No existing ERP table/data was modified. Enable application/config/wip_quality.php when ready.\n";
} catch (Exception $e) {
    fwrite(STDERR, "QC installation failed; inspect database/schema settings. No historical data migration was attempted.\n");
    exit(1);
}
