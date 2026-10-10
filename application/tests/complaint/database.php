<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
/** Optional MySQL/MariaDB integration test. Creates/drops only its random test DB.
 * COMPLAINT_TEST_DB_ALLOW_CREATE=1 php application/tests/complaint/database.php
 * Connection: COMPLAINT_TEST_DB_HOST/PORT/USER/PASSWORD; defaults localhost TCP,
 * 3306/root/empty. It never loads ERP's database.php or accesses ERP tables.
 */
if (getenv('COMPLAINT_TEST_DB_ALLOW_CREATE') !== '1') { fwrite(STDERR, "Set COMPLAINT_TEST_DB_ALLOW_CREATE=1 to create a disposable test database.\n"); exit(2); }
$root = dirname(__DIR__, 3);
$system = getenv('CI_SYSTEM_PATH') ?: $root.'/system';
define('BASEPATH', rtrim($system, '/').'/');
define('APPPATH', $root.'/application/');
define('ENVIRONMENT', 'testing');
date_default_timezone_set('Asia/Jakarta');
error_reporting(E_ALL & ~E_DEPRECATED);
function log_message($level, $message) { if ($level === 'error') { fwrite(STDERR, $message."\n"); } }
function is_php($version) { return version_compare(PHP_VERSION, $version, '>='); }
function get_instance() { return $GLOBALS['test_ci']; }
function show_error($message) { throw new RuntimeException(is_array($message) ? implode('; ', $message) : $message); }
function config_item($name) { return NULL; }
class CI_Model { public $db; public $load; }
class TestLoader {
    public function library($name) { if ($name !== 'Complaint_analyzer') { throw new RuntimeException('Unknown test library'); } }
}
require APPPATH.'libraries/Complaint_analyzer.php';
require APPPATH.'models/Complaint_model.php';
require BASEPATH.'database/DB.php';
$host = getenv('COMPLAINT_TEST_DB_HOST') ?: '127.0.0.1';
$port = (int) (getenv('COMPLAINT_TEST_DB_PORT') ?: 3306);
$user = getenv('COMPLAINT_TEST_DB_USER') ?: 'root';
$password = getenv('COMPLAINT_TEST_DB_PASSWORD') ?: '';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$admin = new mysqli($host, $user, $password, '', $port);
$name = 'tgm_complaint_test_'.bin2hex(random_bytes(6));
$admin->query('CREATE DATABASE `'.$name.'` CHARACTER SET utf8mb4');
$checks = 0;
function check_db($ok, $label) { global $checks; if (!$ok) { throw new RuntimeException('FAIL: '.$label); } $checks++; }
function denies_db($call, $label) { try { $call(); } catch (InvalidArgumentException $e) { check_db(TRUE, $label); return; } check_db(FALSE, $label); }
try {
    $db = DB(array('dbdriver' => 'mysqli', 'hostname' => $host, 'port' => $port, 'username' => $user,
        'password' => $password, 'database' => $name, 'pconnect' => FALSE, 'db_debug' => FALSE,
        'dbprefix' => '', 'char_set' => 'utf8mb4', 'dbcollat' => 'utf8mb4_general_ci', 'save_queries' => FALSE, 'stricton' => TRUE));
    foreach (array(
        'CREATE TABLE users (user_id INT PRIMARY KEY, username VARCHAR(50), active INT, karyawan_id INT, perusahaan_id1 INT) ENGINE=InnoDB',
        'CREATE TABLE tbl_karyawan (karyawan_id INT PRIMARY KEY, nama_lengkap VARCHAR(100), perusahaan_id INT) ENGINE=InnoDB',
        'CREATE TABLE tbl_mesin (mesin_id INT PRIMARY KEY, mesin_nama VARCHAR(100), perusahaan_id INT) ENGINE=InnoDB',
        'CREATE TABLE tbl_pemesanan (nospk VARCHAR(50), perusahaan_id INT) ENGINE=InnoDB',
        "INSERT INTO users VALUES (1,'pengelola',1,1,1),(2,'pic',1,2,1),(3,'cabang2',1,3,2),(4,'nonaktif',0,4,1)",
        "INSERT INTO tbl_karyawan VALUES (1,'Pengelola',1),(2,'PIC',1),(3,'Cabang lain',2),(4,'Nonaktif',1)",
        "INSERT INTO tbl_mesin VALUES (1,'Mesin cabang1',1),(2,'Mesin cabang2',2)",
        "INSERT INTO tbl_pemesanan VALUES ('DEMO-001',1),('DEMO-002',2)"
    ) as $statement) { $db->query($statement); }
    $sql = preg_replace('/^\s*--.*$/m', '', file_get_contents($root.'/database/complaint_center.sql'));
    for ($repeat = 0; $repeat < 2; $repeat++) {
        foreach (explode(';', $sql) as $statement) { if (trim($statement) !== '') { check_db((bool) $db->query($statement), 'migration statement pass '.($repeat + 1)); } }
    }
    $model = new Complaint_model(); $model->db = $db; $model->load = new TestLoader();
    $GLOBALS['test_ci'] = (object) array('complaint_analyzer' => new Complaint_analyzer());
    check_db($model->ready(), 'module tables available');
    check_db(array_map('intval', array_column($model->users(1), 'user_id')) === array(1, 2), 'only active branch PIC users');
    $fields = array('judul' => 'Mesin rusak', 'pelapor' => 'Demo', 'deskripsi' => 'Mesin rusak, perlu pemeriksaan.',
        'kategori' => 'Mesin', 'priority' => 'HIGH', 'mesin_id' => 1, 'reported_at' => '2026-10-06 08:00:00', 'spks' => array('DEMO-001'));
    $id = $model->create(1, $fields, 1);
    check_db((bool) $model->ticket(1, $id) && !$model->ticket(2, $id), 'ticket scoped to branch');
    check_db(count($model->spks(1, $id)) === 1 && !$model->spks(2, $id), 'SPK scoped to branch');
    $bad = $fields; $bad['mesin_id'] = 2;
    denies_db(function () use ($model, $bad) { $model->create(1, $bad, 1); }, 'reject foreign branch machine');
    $bad = $fields; $bad['spks'] = array('DEMO-002');
    denies_db(function () use ($model, $bad) { $model->create(1, $bad, 1); }, 'reject foreign branch SPK');
    check_db($db->count_all('tbl_complaint') === 1, 'invalid creation rolled back');
    $update = $fields + array('status' => 'ASSIGNED', 'pic_id' => 2, 'version' => 1, 'resolution' => '', 'root_cause' => '', 'preventive_action' => '', 'note' => 'Ditugaskan', 'needs_review' => 0);
    $bad = $update; $bad['pic_id'] = 3;
    denies_db(function () use ($model, $id, $bad) { $model->update_ticket(1, $id, $bad, 1, TRUE); }, 'reject foreign branch PIC');
    $bad['pic_id'] = 4;
    denies_db(function () use ($model, $id, $bad) { $model->update_ticket(1, $id, $bad, 1, TRUE); }, 'reject inactive PIC');
    $model->update_ticket(1, $id, $update, 1, TRUE);
    check_db($model->ticket(1, $id)['status'] === 'ASSIGNED', 'manager assignment');
    denies_db(function () use ($model, $id, $update) { $model->update_ticket(1, $id, $update, 1, TRUE); }, 'stale form rejected');
    $update['version'] = 2; $update['status'] = 'ON_PROGRESS';
    denies_db(function () use ($model, $id, $update) { $model->update_ticket(1, $id, $update, 3, FALSE); }, 'unassigned user cannot edit');
    $model->update_ticket(1, $id, $update, 2, FALSE);
    $update['version'] = 3; $update['status'] = 'RESOLVED';
    denies_db(function () use ($model, $id, $update) { $model->update_ticket(1, $id, $update, 2, FALSE); }, 'solution required');
    $update['resolution'] = 'Komponen diganti, cetak diuji normal.';
    $model->update_ticket(1, $id, $update, 2, FALSE);
    $update['version'] = 4; $update['status'] = 'CLOSED';
    denies_db(function () use ($model, $id, $update) { $model->update_ticket(1, $id, $update, 2, FALSE); }, 'PIC cannot close ticket');
    $model->update_ticket(1, $id, $update, 1, TRUE);
    check_db($model->ticket(1, $id)['closed_at'] !== NULL && count($model->history(1, $id)) === 5, 'closure and history persisted');
    $key = hash('sha256', 'demo-message');
    check_db($model->ingest(1, 'import', $key, 'Demo', 'Operator', '2026-10-06 09:00:00', 'Mesin rusak SPK DEMO-001'), 'new message');
    check_db(!$model->ingest(1, 'import', $key, 'Demo', 'Operator', '2026-10-06 09:00:00', 'Mesin rusak SPK DEMO-001'), 'redelivery idempotent');
    check_db(!$model->claim(2), 'worker branch scope');
    $message = $model->claim(1);
    check_db((bool) $message && !$model->claim(1), 'active processing lease not reclaimed');
    $a = (new Complaint_analyzer())->rules($message['body']);
    check_db($model->complete_analysis($message, $a), 'ticket generated from trouble');
    check_db(!$model->complete_analysis($message, $a), 'stale worker cannot finish twice');
    check_db($db->count_all('tbl_complaint') === 2 && $db->count_all('tbl_complaint_spk') === 2, 'one automatic ticket and known SPK');
    $model->ingest(1, 'import', hash('sha256', 'followup'), 'Demo', 'Teknisi', '2026-10-06 10:00:00', 'Mesin sudah diperbaiki dan sudah normal.');
    $followup = $model->claim(1);
    $model->complete_analysis($followup, (new Complaint_analyzer())->rules($followup['body']));
    check_db($db->count_all('tbl_complaint') === 2, 'followup does not duplicate ticket');
    $model->link_message(1, $followup['id'], $id, 1);
    check_db(count($model->evidence(1, $id)) === 1 && $model->ticket(1, $id)['status'] === 'CLOSED', 'followup linked without changing status');
    $model->ingest(1, 'webhook', hash('sha256', 'lease'), 'Demo', 'Operator', '2026-10-06 10:00:00', 'Mesin macet');
    $lease = $model->claim(1);
    $db->where('id', $lease['id'])->update('tbl_wa_complaint_messages', array('locked_at' => date('Y-m-d H:i:s', time() - 301)));
    $reclaimed = $model->claim(1);
    check_db($reclaimed['lock_token'] !== $lease['lock_token'], 'expired lease reclaimed');
    check_db(!$model->complete_analysis($lease, (new Complaint_analyzer())->rules($lease['body'])), 'old lease cannot create ticket');
    $db->where('id', $reclaimed['id'])->update('tbl_wa_complaint_messages', array('attempts' => 3, 'locked_at' => date('Y-m-d H:i:s', time() - 301)));
    check_db(!$model->claim(1), 'three-attempt expired lease stops');
    $model->retry(1, $lease['id']);
    check_db((bool) $model->claim(1), 'manual retry recovers failed message');
    $report = $model->report(1, '2026-10-01', '2026-10-31');
    check_db($report['total'] === 2 && $report['status']['CLOSED'] === 1 && $report['affected_spks'] === 1, 'report aggregates current status and distinct SPK');
    check_db($model->report(2, '2026-10-01', '2026-10-31')['total'] === 0, 'report excludes other branch');
    $list = $model->listing(1, array('from' => '2026-10-01', 'to' => '2026-10-31', 'q' => 'DEMO-001'), 1);
    check_db($list['total'] === 2, 'search by related SPK');
    echo 'PASS '.$checks." database integration checks.\n";
    $db->close();
} finally { $admin->query('DROP DATABASE `'.$name.'`'); $admin->close(); }
