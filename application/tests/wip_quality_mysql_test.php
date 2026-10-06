<?php
// Creates and removes ONLY a uniquely named test database; never writes to the ERP database.
if (PHP_SAPI !== 'cli' || !in_array('--isolated-db', $argv, true)) exit("Run with --isolated-db to create a disposable QC test database.\n");
define('BASEPATH', __DIR__);
require __DIR__ . '/../config/database.php';
$connection_config = $db['default'];
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
function connection($config) {
    return new mysqli($config['hostname'], $config['username'], $config['password'], '', $config['port'] ?? 3306, '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock');
}
class CI_Model { public $config; public $db; public function __construct() {} }
function log_message($level, $message) { if (getenv("QC_TEST_DEBUG")) fwrite(STDERR, $message . "\n"); }
class QcTestConfig {
    private $enabled;
    public function __construct($enabled) { $this->enabled = $enabled; }
    public function load($name, $section) {}
    public function item($name) { return ['enabled' => $this->enabled, 'reviewer_user_ids' => [12]]; }
}
class QcRows {
    private $rows;
    public function __construct($result) { $this->rows = $result->fetch_all(MYSQLI_ASSOC); }
    public function row() { return $this->rows ? (object) $this->rows[0] : null; }
    public function result() { return array_map(function ($row) { return (object) $row; }, $this->rows); }
}
class QcSql {
    public $connection;
    public $fail_audit = false;
    private $affected = 0;
    public function __construct($connection) { $this->connection = $connection; }
    public function query($sql, $binds = []) {
        if ($this->fail_audit && strpos($sql, 'INSERT INTO tbl_produksi_log') === 0) return false;
        if ($binds) {
            $statement = $this->connection->prepare($sql);
            $statement->bind_param(str_repeat('s', count($binds)), ...$binds);
            $statement->execute(); $this->affected = $statement->affected_rows; $result = $statement->get_result();
            return $result instanceof mysqli_result ? new QcRows($result) : true;
        }
        $result = $this->connection->query($sql); $this->affected = $this->connection->affected_rows;
        return $result instanceof mysqli_result ? new QcRows($result) : $result;
    }
    public function trans_begin() { return $this->connection->begin_transaction(); }
    public function trans_commit() { return $this->connection->commit(); }
    public function trans_rollback() { return $this->connection->rollback(); }
    public function trans_status() { return true; }
    public function affected_rows() { return $this->affected; }
}
require __DIR__ . '/../libraries/Production_workflow.php';
require __DIR__ . '/../models/Wip_quality_model.php';
class QualityUnderTest extends Wip_quality_model {
    public function __construct($db, $enabled = true) { $this->db = $db; $this->config = new QcTestConfig($enabled); parent::__construct(); }
}
$checks = 0;
function verify($condition, $name) { global $checks; if (!$condition) throw new RuntimeException($name); $checks++; }
function denied($callback, $name) { try { $callback(); } catch (DomainException $e) { verify(true, $name); return; } throw new RuntimeException($name); }
$test_name = 'tgm_wip_qc_test_' . bin2hex(random_bytes(6));
$admin = connection($connection_config);
$created = false;
try {
    $admin->query('CREATE DATABASE `' . $test_name . '` CHARACTER SET utf8mb4'); $created = true;
    $admin->select_db($test_name);
    $sql = new QcSql($admin);
    $sql->query('CREATE TABLE tbl_produksi (produksi_id BIGINT PRIMARY KEY, nospk VARCHAR(80), product_id INT, qty DECIMAL(18,4), perusahaan_id INT, lokasi_cetak INT, status_produksi VARCHAR(20)) ENGINE=InnoDB');
    $sql->query('CREATE TABLE tbl_produksi_log (log_id INT AUTO_INCREMENT PRIMARY KEY, produksi_id BIGINT, user_id INT, status_asal VARCHAR(30), status_tujuan VARCHAR(30), catatan TEXT) ENGINE=InnoDB');
    $sql->query('CREATE TABLE tbl_product (product_id INT PRIMARY KEY, product_nama VARCHAR(100)) ENGINE=InnoDB');
    $sql->query('CREATE TABLE users (user_id INT PRIMARY KEY, username VARCHAR(50)) ENGINE=InnoDB');
    $sql->query("INSERT INTO users VALUES (9, 'qc-admin')");
    $sql->query("INSERT INTO tbl_product VALUES (1, 'Produk uji')");
    $quality = new QualityUnderTest($sql);
    denied(function () use ($quality) { $quality->assert_storage(); }, 'Enabled QC fails closed if schema missing');
    $disabled = new QualityUnderTest($sql, false);
    verify(!$disabled->assert_storage(), 'Disabled QC with absent schema preserves legacy workflow');
    $migration = file_get_contents(__DIR__ . '/../migrations/20261006_wip_quality.sql');
    foreach (explode(';', $migration) as $query) if (trim($query)) $sql->query($query);
    $quality = new QualityUnderTest($sql);
    verify($quality->assert_storage(), 'Additive schema uses InnoDB');
    foreach ([[1, 2], [2, 3], [3, 2], [4, 2]] as $fixture) $sql->query("INSERT INTO tbl_produksi VALUES (?, ?, 1, 10, ?, 0, 'done')", [$fixture[0], 'QC-TEST-' . $fixture[0], $fixture[1]]);
    $actor = ['user_id' => 9, 'admin' => true, 'branch' => 2];
    $item = $sql->query('SELECT * FROM tbl_produksi WHERE produksi_id = 1')->row();
    $legacy = $sql->query('SELECT * FROM tbl_produksi WHERE produksi_id = 3')->row();
    $quality->assert_handover($legacy); verify(true, 'Historical done item without QC can hand over');
    $before = json_encode($sql->query('SELECT * FROM tbl_produksi ORDER BY produksi_id')->result());
    $sql->trans_begin(); $quality->enroll($item, $actor); $quality->enroll($item, $actor); $sql->trans_commit();
    verify((int) $sql->query('SELECT COUNT(*) AS n FROM tbl_wip_qc')->row()->n === 1, 'Repeated enrollment creates one ticket');
    verify($before === json_encode($sql->query('SELECT * FROM tbl_produksi ORDER BY produksi_id')->result()), 'QC enrollment does not rewrite original production');
    denied(function () use ($quality, $item) { $quality->assert_handover($item); }, 'Pending inspection blocks handover');
    $disabled = new QualityUnderTest($sql, false);
    denied(function () use ($disabled, $item) { $disabled->assert_handover($item); }, 'Disabling enrollment does not bypass existing hold');
    $token = str_repeat('a', 32);
    verify(!$quality->inspect(1, 1, 10, 0, '', false, $token, ['user_id' => 10, 'admin' => false, 'branch' => 2])['success'], 'Unassigned user cannot inspect');
    verify(!$quality->inspect(1, 1, 10, 0, '', false, $token, ['user_id' => 12, 'admin' => false, 'branch' => 3])['success'], 'Assigned inspector cannot cross branch');
    verify(!$quality->inspect(1, 1, 8, 0, '', false, $token, $actor)['success'], 'Incomplete total rejected');
    verify(!$quality->inspect(1, 1, -1, 11, 'bad', false, $token, $actor)['success'], 'Negative quantity rejected');
    verify(!$quality->inspect(1, 1, 8, 2, '', true, $token, $actor)['success'], 'Reject requires reason');
    verify(!$quality->inspect(1, 1, 10, 0, '', true, $token, $actor)['success'], 'Reprint requires rejected quantity');
    verify(!$quality->inspect(1, 1, 10, 0, '', false, 'invalid', $actor)['success'], 'Invalid request identity rejected');
    $sql->fail_audit = true;
    verify(!$quality->inspect(1, 1, 8, 2, 'Warna tidak sesuai', true, $token, $actor)['success'], 'Audit failure reported');
    verify((int) $sql->query('SELECT COUNT(*) AS n FROM tbl_wip_qc_inspection')->row()->n === 0 && $sql->query('SELECT status FROM tbl_wip_qc WHERE produksi_id = 1')->row()->status === 'pending', 'Audit failure rolls back inspection and decision');
    $sql->fail_audit = false;
    verify($quality->inspect(1, 1, 8, 2, 'Warna tidak sesuai', true, $token, $actor)['success'], 'Rejected inspection and linked reprint request saved');
    denied(function () use ($quality, $item) { $quality->assert_handover($item); }, 'Rejected inspection blocks handover');
    verify(!empty($quality->inspect(1, 1, 8, 2, 'Warna tidak sesuai', true, $token, $actor)['duplicate']), 'Identical retry returns existing result');
    verify((int) $sql->query('SELECT COUNT(*) AS n FROM tbl_wip_qc_inspection')->row()->n === 1, 'Retry never duplicates inspection');
    verify(!$quality->inspect(1, 1, 10, 0, 'changed', false, $token, $actor)['success'], 'Reused request identity with changed data rejected');
    verify(!$quality->inspect(1, 1, 10, 0, 'checked', false, str_repeat('b', 32), $actor)['success'], 'Stale revision rejected');
    verify(!$quality->inspect(1, 2, 10, 0, '', false, str_repeat('b', 32), $actor)['success'], 'Reinspection requires follow-up note');
    verify($quality->inspect(1, 2, 10, 0, 'Diperiksa ulang setelah tindak lanjut', false, str_repeat('b', 32), $actor)['success'], 'Reinspection can pass');
    $quality->assert_handover($item); verify(true, 'Passed QC allows handover');
    $history = $quality->history(1, $actor);
    verify(count($history) === 2 && (int) $history[0]->request_reprint === 1 && $history[0]->note === 'Warna tidak sesuai', 'Reinspection preserves rejection and reprint history');
    verify(!$quality->inspect(1, 3, 10, 0, 'overwrite', false, str_repeat('c', 32), $actor)['success'], 'Approved history cannot be overwritten');
    denied(function () use ($quality) { $quality->history(1, ['user_id' => 12, 'admin' => false, 'branch' => 3]); }, 'History respects branch access');
    verify($quality->queue(['user_id' => 12, 'admin' => false, 'branch' => 3], 'all', '', 1)['total'] === 0, 'Queue respects branch access');
    verify($quality->queue($actor, 'reprint', '', 1)['total'] === 1, 'Linked request remains discoverable after reinspection');
    verify($quality->queue($actor, 'all', "' OR 1=1 --", 1)['total'] === 0, 'Search uses bound parameters');
    verify($before === json_encode($sql->query('SELECT * FROM tbl_produksi ORDER BY produksi_id')->result()), 'Inspections never rewrite production results/status');
    // Lock contention uses a second connection against this disposable database.
    $item4 = $sql->query('SELECT * FROM tbl_produksi WHERE produksi_id = 4')->row();
    $sql->trans_begin(); $quality->enroll($item4, $actor); $sql->trans_commit();
    $other = connection($connection_config); $other->select_db($test_name); $other->query('SET innodb_lock_wait_timeout = 1');
    $other_quality = new QualityUnderTest(new QcSql($other));
    $sql->trans_begin(); $sql->query('SELECT * FROM tbl_produksi WHERE produksi_id = 4 FOR UPDATE');
    verify(!$other_quality->inspect(4, 1, 10, 0, '', false, str_repeat('d', 32), $actor)['success'], 'Concurrent header lock prevents competing inspection');
    $sql->trans_commit();
    verify($other_quality->inspect(4, 1, 10, 0, '', false, str_repeat('d', 32), $actor)['success'], 'Retry succeeds after lock released');
    $other->close();
    $sql->query('ALTER TABLE tbl_wip_qc ENGINE=MyISAM');
    $bad_engine = new QualityUnderTest($sql);
    denied(function () use ($bad_engine) { $bad_engine->assert_storage(); }, 'Nontransactional QC storage is refused');
    echo "PASS: $checks QC checks on disposable MySQL database; ERP data untouched.\n";
} finally {
    if ($created) $admin->query('DROP DATABASE `' . $test_name . '`');
    $admin->close();
}
