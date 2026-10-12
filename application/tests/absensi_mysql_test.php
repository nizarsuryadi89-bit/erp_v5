<?php
// Copies table structures to a disposable database; never writes ERP records.
if (PHP_SAPI !== 'cli' || !in_array('--isolated-db', $argv, true)) exit("Run with --isolated-db to use a disposable database.\n");
define('BASEPATH', dirname(__DIR__, 2) . '/system/');
ob_start(); require __DIR__ . '/absensi_controller_test.php'; ob_end_clean();
define('APPPATH', dirname(__DIR__) . '/'); define('ENVIRONMENT', 'development');
function &get_instance() { global $active; return $active; }
function config_item($key) { return null; }
require dirname(__DIR__, 2) . '/system/core/Common.php';
require dirname(__DIR__, 2) . '/system/database/DB.php';
class CI_Model { public $db, $table; public function __construct() {} }
require __DIR__ . '/../models/Absensi_model.php';
$_SERVER['HTTP_HOST'] = 'localhost'; require __DIR__ . '/../config/database.php';
$config = $db['default'];
$name = 'tgm_absensi_test_' . bin2hex(random_bytes(6));
mysqli_report(MYSQLI_REPORT_OFF);
$m = new mysqli($config['hostname'], $config['username'], $config['password'], '', $config['port'] ?? 3306, '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock');
if ($m->connect_errno) throw new RuntimeException('Cannot connect to test server.');
$created = false; $sql = null;
try {
    if (!$m->query('CREATE DATABASE `' . $name . '`')) throw new RuntimeException('Cannot create disposable DB.');
    $created = true; $seed = fixture();
    $tables = array_unique(array_merge(array_keys($seed->db->tables), ['tbl_jabatan','tbl_divisi','tbl_perusahaan','users']));
    foreach ($tables as $table) {
        if (!$m->query('CREATE TABLE `' . $name . '`.`' . $table . '` LIKE `' . $m->real_escape_string($config['database']) . '`.`' . $table . '`')) throw new RuntimeException('Missing schema: ' . $table);
    }
    $config['database'] = $name; $config['hostname'] = '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock'; $config['db_debug'] = false;
    $sql = DB($config, true);
    $sql->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION'");
    // Complete only fixture fields required by the real schema; no ERP row contents copied.
    $insert = function ($table, $row) use (&$sql) {
        $columns = $sql->query('SHOW COLUMNS FROM `' . $table . '`')->result_array();
        foreach ($columns as $col) {
            $field = $col['Field'];
            if (!array_key_exists($field, $row) && $col['Null'] === 'NO' && $col['Default'] === null && strpos($col['Extra'], 'auto_increment') === false) {
                $type = $col['Type'];
                if (preg_match('/^enum\(\x27([^\x27]+)\x27/', $type, $match)) $row[$field] = $match[1];
                elseif (preg_match('/int|decimal|float|double/', $type)) $row[$field] = 0;
                elseif ($type === 'date') $row[$field] = '2026-10-12';
                elseif (strpos($type, 'datetime') === 0 || strpos($type, 'timestamp') === 0) $row[$field] = '2026-10-12 08:00:00';
                elseif (strpos($type, 'time') === 0) $row[$field] = '00:00:00';
                else $row[$field] = '';
            }
        }
        if (!$sql->insert($table, $row)) throw new RuntimeException('Fixture insert failed: ' . $table . ': ' . $sql->error()['message']);
    };
    foreach ($seed->db->tables as $table => $rows) foreach ($rows as $row) $insert($table, $row);
    $active = fixture(); $active->db = $sql; $active->M_master = new AMaster($sql); $checks = 0;
    $r = action($active, 'absen_masuk', ['karyawan_id' => 1]);
    check(isset($active->session->flash['success']), 'SQL strict checkin');
    action($active, 'absen_masuk', ['karyawan_id' => 1]); check(isset($active->session->flash['error']) && $sql->count_all_results('absensi') === 1, 'SQL duplicate checkin');
    action($active, 'absen_pulang', ['karyawan_id' => 1]); check(isset($active->session->flash['success']), 'SQL checkout');
    $sql->empty_table('absensi');
    $insert('tbl_pengajuan_izin', request());
    action($active, 'approve_action', [], [20]);
    check(isset($active->session->flash['success']) && $sql->count_all_results('absensi') === 2, 'SQL two day approval');
    check($sql->get_where('tbl_quota_cuti', ['karyawan_id' => 1, 'tahun' => 2026])->row()->terpakai == 2, 'SQL quota sync');
    action($active, 'reject_action', [], [20]); check($sql->get_where('tbl_pengajuan_izin', ['id' => 20])->row()->status === 'Approved', 'SQL final state preserved');
    // Covering multi-day approved leave prevents extra HR leave records on edits.
    $post = ['karyawan_id' => 1, 'tanggal' => '2026-10-13', 'status' => 'Cuti', 'jam_masuk' => '08:00:01', 'jam_pulang' => '17:00', 'keterangan' => 'SQL manual'];
    action($active, 'update_attendance_manual', $post);
    check(isset($active->session->flash['success']) && $sql->count_all_results('tbl_pengajuan_izin') === 1, 'SQL DATE_ADD coverage predicate');
    $sql->empty_table('absensi'); $sql->empty_table('tbl_pengajuan_izin'); $sql->empty_table('tbl_quota_cuti');
    $req = request('izin_telat', 0); $req['durasi_tipe'] = 'jam'; $insert('tbl_pengajuan_izin', $req);
    action($active, 'approve_action', [], [20]); check(isset($active->session->flash['success']) && $sql->count_all_results('absensi') === 0, 'SQL late approval no fake presence');
    action($active, 'absen_masuk', ['karyawan_id' => 1]); check(isset($active->session->flash['success']), 'SQL attendance after late approval');
    $sql->empty_table('absensi'); $sql->empty_table('tbl_pengajuan_izin'); $sql->empty_table('tbl_quota_cuti');
    $insert('tbl_pengajuan_izin', request());
    if (!$sql->query("CREATE TRIGGER force_attendance_failure BEFORE INSERT ON absensi FOR EACH ROW BEGIN IF NEW.tanggal = '2026-10-13' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Injected failure'; END IF; END")) throw new RuntimeException('Failed trigger');
    action($active, 'approve_action', [], [20]);
    check(isset($active->session->flash['error']) && $sql->count_all_results('absensi') === 0 && $sql->get_where('tbl_pengajuan_izin', ['id' => 20])->row()->status === 'Pending', 'SQL rollback after second day fails');
    $sql->query('DROP TRIGGER force_attendance_failure');
    // Each controller request gets a fresh CI database object; failed transaction status is request-local.
    $sql->close(); $sql = DB($config, true); $active->db = $sql; $active->M_master = new AMaster($sql);
    $sql->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION'");
    $sql->empty_table('tbl_pengajuan_izin');
    $insert('tbl_pengajuan_koreksi_absen', ['id' => 5, 'karyawan_id' => 1, 'tanggal_absen' => '2026-10-12', 'jam_masuk_baru' => '08:20:00', 'status' => 'Pending']);
    action($active, 'approve_koreksi', [], [5]); check(isset($active->session->flash['success']) && $sql->get('absensi')->row()->jam_pulang === '00:00:00', 'SQL correction complete defaults');
    $sql->empty_table('absensi');
    $row = ['karyawan_id' => 1, 'status' => 'Cuti', 'jam_masuk' => '08:00', 'jam_pulang' => '17:00'];
    $r = action($active, 'save_manual_batch', ['tanggal' => '2026-10-12', 'attendance' => [$row]]);
    check($r['status'] && $sql->count_all_results('absensi') === 1, 'SQL manual batch');
    check($sql->get('tbl_pengajuan_izin')->row()->responded_by == 7, 'SQL correct approver identity');
    $sql->empty_table('absensi'); $sql->empty_table('tbl_pengajuan_izin');
    $insert('absensi', array_merge(attendance('2026-10-12'), ['jam_pulang' => '19:00:00']));
    $r = action($active, 'sync_to_payroll', ['karyawan_id' => 1, 'bulan' => '2026-10', 'start_date' => '2026-10-12', 'end_date' => '2026-10-12', 'total_hadir' => 100]);
    check($r['success'], 'SQL payroll sync'); $recap = $sql->get('tbl_absensi_rekap_karyawan')->row();
    check($recap->total_hadir == 1 && $recap->biaya_makan_lembur == 15000, 'SQL payroll calculated totals');
    $model = new Absensi_model; $model->db = $sql;
    $model->get_filtered_absensi('2026-10-12'); check($sql->error()['code'] === 0, 'SQL filtered model query');
    $model->get_bulk_attendance_for_date("2026-10-12' OR 1=1 -- "); check($sql->error()['code'] === 0, 'SQL malicious date escaped');
    $model->get_rekap_all_karyawan('2026-10-12', '2026-10-13'); check($sql->error()['code'] === 0, 'SQL recap query');
    echo "Absensi MySQL strict: $checks checks passed.\n";
} finally {
    if ($sql) $sql->close();
    if ($created) $m->query('DROP DATABASE `' . $name . '`');
    $m->close();
}
