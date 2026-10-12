<?php
// Disposable memory records, no ERP connection or external notifications.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
if (!defined('BASEPATH')) define('BASEPATH', __DIR__);
define('FCPATH', sys_get_temp_dir() . '/tgm_absensi_' . getmypid() . '/');
date_default_timezone_set('Asia/Jakarta');
error_reporting(E_ALL);
set_error_handler(function ($n, $message, $file, $line) { if (error_reporting() & $n) throw new ErrorException($message, 0, $n, $file, $line); return true; });
function log_message($level, $message) {}
function log_activity($user, $action, $module, $id) {}
function redirect($url) { throw new ARedirect($url); }
function show_404() { throw new RuntimeException('404'); }
function show_error($text, $code = 500) { throw new RuntimeException($text, $code); }
function validation_errors() { return 'Invalid form'; }
class ARedirect extends RuntimeException {}
class MY_Controller { public $data = [], $db, $session, $input, $Absensi_model, $M_master, $Payroll_model, $agent, $form_validation, $load; }
require __DIR__ . '/../controllers/Absensi.php';
class AInput {
    public $posts = [], $gets = [], $ajax = true;
    public function post($key = null, $clean = false) { return $key === null ? $this->posts : ($this->posts[$key] ?? null); }
    public function get($key, $clean = false) { return $this->gets[$key] ?? null; }
    public function server($key) { return $key === 'REQUEST_METHOD' ? 'POST' : null; }
    public function is_ajax_request() { return $this->ajax; }
    public function user_agent() { return 'Offline test'; }
}
class ASession {
    public $data = ['logged_in' => true, 'id' => 7, 'karyawan_id' => 1, 'perusahaan_id' => 2], $flash = [];
    public function userdata($key) { return $this->data[$key] ?? null; }
    public function set_flashdata($key, $value) { $this->flash[$key] = $value; }
}
class AAgent {
    public function platform() { return 'Test'; }
    public function is_mobile() { return false; }
    public function browser() { return str_repeat('T', 80); }
}
class AValidation { public function set_rules($name, $label, $rules) {} public function run() { return true; } }
class ALoad { public function library($name) { throw new RuntimeException('External notification forbidden in tests'); } }
class ARows {
    private $rows;
    public function __construct($rows) { $this->rows = array_values($rows); }
    public function row() { return isset($this->rows[0]) ? (object)$this->rows[0] : null; }
    public function row_array() { return $this->rows[0] ?? []; }
    public function result() { return array_map(function ($r) { return (object)$r; }, $this->rows); }
}
class ADb {
    public $tables = [], $fail, $throw, $open = false, $commits = 0, $rollbacks = 0, $queries = [], $db_debug = true;
    private $conditions = [], $snapshot, $ok = true;
    public function select($fields) { return $this; }
    public function order_by($key, $direction = 'ASC') { return $this; }
    public function where($key, $value = null) {
        if (is_array($key)) { foreach ($key as $k => $v) $this->where($k, $v); }
        else $this->conditions[] = [$key, $value];
        return $this;
    }
    public function where_in($key, $values) { $this->conditions[] = [$key . ' IN', $values]; return $this; }
    private function matches($row) {
        foreach ($this->conditions as $pair) {
            list($key, $value) = $pair;
            if (preg_match('/^YEAR\((\w+)\)$/', $key, $m)) { if (substr($row[$m[1]], 0, 4) != $value) return false; continue; }
            if (strpos($key, 'DATE_ADD(') === 0) {
                $end = date('Y-m-d', strtotime($row['tanggal_mulai'] . ' +' . ($row['durasi_jumlah'] - 1) . ' days'));
                if ($end < $value) return false; continue;
            }
            if (preg_match('/^(.+) (>=|<=|!=|IN)$/', $key, $m)) {
                $actual = $row[$m[1]] ?? null;
                if ($m[2] === '>=' && $actual < $value || $m[2] === '<=' && $actual > $value
                    || $m[2] === '!=' && $actual == $value || $m[2] === 'IN' && !in_array($actual, $value, true)) return false;
            } elseif (($row[$key] ?? null) != $value) return false;
        }
        return true;
    }
    public function get($table) { $rows = array_filter($this->tables[$table] ?? [], function ($r) { return $this->matches($r); }); $this->conditions = []; return new ARows($rows); }
    public function get_where($table, $where) { return $this->where($where)->get($table); }
    public function query($sql, $binds = []) {
        $this->queries[] = $sql;
        if (preg_match('/^SELECT \* FROM (\w+) WHERE (\w+) = \? FOR UPDATE$/', $sql, $m)) return $this->get_where($m[1], [$m[2] => $binds[0]]);
        throw new RuntimeException('Unexpected SQL ' . $sql);
    }
    private function can_write($table) {
        if ($this->throw === $table) { $this->throw = null; throw new TypeError('Injected failure'); }
        if ($this->fail === $table) { $this->ok = false; $this->conditions = []; return false; }
        return true;
    }
    public function insert($table, $data) {
        if (!$this->can_write($table)) return false;
        if ($table === 'absensi') foreach (['karyawan_id','tanggal','status','jam_masuk','jam_pulang','foto_masuk','foto_pulang','dokumen'] as $key) {
            if (!array_key_exists($key, $data)) throw new RuntimeException('Missing required column ' . $key);
        }
        $data['id'] = count($this->tables[$table] ?? []) + 100;
        $this->tables[$table][] = $data; return true;
    }
    public function update($table, $data, $where = []) {
        $this->where($where); if (!$this->can_write($table)) return false;
        foreach ($this->tables[$table] as &$r) if ($this->matches($r)) $r = array_merge($r, $data);
        unset($r); $this->conditions = []; return true;
    }
    public function delete($table, $where = []) {
        $this->where($where); if (!$this->can_write($table)) return false;
        $this->tables[$table] = array_values(array_filter($this->tables[$table] ?? [], function ($r) { return !$this->matches($r); }));
        $this->conditions = []; return true;
    }
    public function table_exists($table) { return array_key_exists($table, $this->tables); }
    public function field_exists($field, $table) { return $this->table_exists($table); }
    public function trans_begin() { $this->snapshot = $this->tables; $this->open = true; $this->ok = true; return true; }
    public function trans_status() { return $this->ok; }
    public function trans_commit() { $this->open = false; $this->commits++; return true; }
    public function trans_rollback() { if ($this->open) { $this->tables = $this->snapshot; $this->open = false; $this->rollbacks++; } return true; }
}
class APayroll { public function _hitung_jam_lembur($id, $start, $end, $date, $att) { return ['raw_pagi' => 0, 'raw_wajib' => 0, 'raw_kerjaan' => 0]; } }
class AMaster { public $db; public function __construct($db) { $this->db = $db; } public function get_karyawan_by_nik($nik) { return $this->db->get_where('tbl_karyawan', ['nik' => $nik])->row(); } }
function fixture() {
    $c = (new ReflectionClass('Absensi'))->newInstanceWithoutConstructor();
    $c->db = new ADb; $c->input = new AInput; $c->session = new ASession; $c->agent = new AAgent;
    $c->form_validation = new AValidation; $c->Payroll_model = new APayroll; $c->load = new ALoad; $c->M_master = new AMaster($c->db);
    $c->db->tables = ['tbl_karyawan' => [ ['karyawan_id' => 1, 'perusahaan_id' => 2, 'nik' => 'TEST-001'], ['karyawan_id' => 2, 'perusahaan_id' => 3, 'nik' => 'TEST-002'] ],
        'absensi' => [], 'tbl_pengajuan_izin' => [], 'tbl_quota_cuti' => [], 'tbl_lembur' => [], 'hari_libur' => [],
        'tbl_pengajuan_koreksi_absen' => [], 'tbl_lembur_jam_config' => [], 'tbl_absensi_rekap_karyawan' => [],
        'tbl_jam_kerja' => [['hari' => 'Senin', 'jam_masuk' => '08:00:00', 'jam_pulang' => '17:00:00']]];
    return $c;
}
function call_private($c, $method, ...$args) { $m = new ReflectionMethod($c, $method); $m->setAccessible(true); return $m->invokeArgs($c, $args); }
function action($c, $method, $posts = [], $args = []) {
    $c->input->posts = $posts; $c->session->flash = []; ob_start();
    try { call_user_func_array([$c, $method], $args); } catch (ARedirect $e) {}
    $text = ob_get_clean(); return json_decode($text, true);
}
$checks = 0;
function check($ok, $label) { global $checks; if (!$ok) throw new RuntimeException('FAIL: ' . $label); $checks++; }
function attendance($date = null, $status = 'Hadir') { return ['id' => 10, 'karyawan_id' => 1, 'tanggal' => $date ?: date('Y-m-d'), 'status' => $status,
    'jam_masuk' => '08:00:00', 'jam_pulang' => '00:00:00', 'foto_masuk' => '', 'foto_pulang' => '', 'dokumen' => '', 'keterangan' => '']; }
function request($type = 'cuti', $duration = 2) { return ['id' => 20, 'karyawan_id' => 1, 'tanggal_mulai' => '2026-10-12', 'durasi_tipe' => 'hari',
    'durasi_jumlah' => $duration, 'tipe_izin' => $type, 'keterangan' => 'Uji izin', 'dokumen' => '', 'status' => 'Pending']; }
$c = fixture();
foreach (['2026-02-30', '2026-13-01', '12/10/2026', [], '', '1899-12-31'] as $date) check(!call_private($c, '_valid_date', $date), 'invalid date rejected');
check(call_private($c, '_valid_date', '2024-02-29'), 'leap date accepted');
check(!call_private($c, '_valid_range', '2026-10-12', '2026-10-11'), 'reversed range');
check(!call_private($c, '_valid_range', '2024-01-01', '2026-01-01'), 'oversized range');
check(call_private($c, '_normal_time', '08:30') === '08:30:00', 'short time normalized');
check(call_private($c, '_normal_time', '08:30:01') === '08:30:01', 'seconds preserved');
foreach (['24:00', '12:70', [], 'invalid'] as $time) { try { call_private($c, '_normal_time', $time); check(false, 'bad time'); } catch (InvalidArgumentException $e) { check(true, 'bad time'); } }
check(call_private($c, '_payroll_range', 24, '2026-03-01') === ['2026-02-24', '2026-03-24'], 'March cutoff');
check(call_private($c, '_payroll_range', 31, '2026-03-01') === ['2026-02-28', '2026-03-31'], 'clamped month boundary');
check(call_private($c, '_payroll_range', 31, '2024-03-01') === ['2024-02-29', '2024-03-31'], 'leap cutoff');
check(call_private($c, 'calculate_distance', 0, 0, 0, 1) > 111000, 'zero coordinate accepted');
check(is_finite(call_private($c, 'calculate_distance', 0, 0, 0, 180)), 'antipodal distance finite');
$att = (object)attendance('2025-01-06'); $att->jam_pulang = '19:00:00';
check(call_private($c, '_meal_allowance', '2025-01-06', $att, null) === 15000, 'historic date meal');
$att->jam_masuk = '09:00:00'; check(call_private($c, '_meal_allowance', '2025-01-06', $att, null) === 0, 'late meal deduction');
$att->status = 'Cuti'; check(call_private($c, '_meal_allowance', '2025-01-06', $att, null) === 0, 'leave not paid meal');
$c = fixture(); action($c, 'absen_pulang', ['karyawan_id' => 1]); check(isset($c->session->flash['error']) && !$c->db->tables['absensi'], 'checkout requires checkin');
$c = fixture(); action($c, 'absen_masuk', ['karyawan_id' => 1]); check(count($c->db->tables['absensi']) === 1 && $c->db->commits === 1, 'checkin defaults complete');
check(strlen($c->db->tables['absensi'][0]['device_type']) === 50, 'device length capped');
action($c, 'absen_masuk', ['karyawan_id' => 1]); check(isset($c->session->flash['error']) && count($c->db->tables['absensi']) === 1, 'double checkin rejected');
action($c, 'absen_pulang', ['karyawan_id' => 1]); $end = $c->db->tables['absensi'][0]['jam_pulang']; check(isset($c->session->flash['success']) && $end !== '00:00:00', 'checkout saved');
action($c, 'absen_pulang', ['karyawan_id' => 1]); check(isset($c->session->flash['error']) && $c->db->tables['absensi'][0]['jam_pulang'] === $end, 'double checkout preserves original');
$c = fixture(); $c->db->tables['absensi'][] = attendance(null, 'Alpha'); action($c, 'absen_masuk', ['karyawan_id' => 1]); check(count($c->db->tables['absensi']) === 1 && $c->db->tables['absensi'][0]['status'] === 'Hadir', 'Alpha replaced');
$c = fixture(); $c->session->data['logged_in'] = false; action($c, 'scan', ['nip_qr' => 'TEST-001']); check(!$c->db->tables['absensi'], 'anonymous scan blocked');
$c = fixture(); action($c, 'scan', ['nip_qr' => 'TEST-002']); check(!$c->db->tables['absensi'] && isset($c->session->flash['error']), 'other employee QR blocked');
action($c, 'scan', ['nip_qr' => 'TEST-001']); check(count($c->db->tables['absensi']) === 1, 'own scan works');
$c = fixture(); action($c, 'absen_masuk', ['karyawan_id' => 2]); check(!$c->db->tables['absensi'], 'other employee checkin blocked');
$c = fixture(); action($c, 'izin_absen', ['karyawan_id' => 2, 'tanggal' => date('Y-m-d'), 'status' => 'izin', 'durasi_tipe' => 'hari', 'durasi_jumlah' => 1]); check(!$c->db->tables['tbl_pengajuan_izin'], 'leave ownership');
$c = fixture(); action($c, 'izin_absen', ['karyawan_id' => 1, 'tanggal' => '2026-02-30', 'status' => 'izin', 'durasi_tipe' => 'hari', 'durasi_jumlah' => 1]); check(!$c->db->tables['tbl_pengajuan_izin'], 'leave date validated');
$c = fixture(); $c->db->tables['absensi'][] = attendance(); action($c, 'izin_absen', ['karyawan_id' => 1, 'tanggal' => date('Y-m-d'), 'status' => 'pulang_cepat', 'durasi_tipe' => 'jam', 'jam_pulang_cepat' => '14:30', 'kegiatan' => 'Test']);
check(isset($c->session->flash['success']) && strpos($c->db->tables['tbl_pengajuan_izin'][0]['keterangan'], '14:30') !== false, 'early leave stores plan and allows existing attendance');
$c = fixture(); $c->db->tables['tbl_pengajuan_izin'][] = request('izin_telat', 0); $c->db->tables['tbl_pengajuan_izin'][0]['durasi_tipe'] = 'jam';
action($c, 'approve_action', [], [20]); check($c->db->tables['tbl_pengajuan_izin'][0]['status'] === 'Approved' && !$c->db->tables['absensi'], 'late approval creates no fake attendance');
action($c, 'approve_action', [], [20]); check(isset($c->session->flash['error']) && $c->db->commits === 1, 'reapproval rejected');
action($c, 'reject_action', [], [20]); check($c->db->tables['tbl_pengajuan_izin'][0]['status'] === 'Approved', 'approved request cannot be rejected');
$c = fixture(); $c->db->tables['tbl_pengajuan_izin'][] = request('pulang_cepat', 1); $c->db->tables['tbl_pengajuan_izin'][0]['durasi_tipe'] = 'jam'; $c->db->tables['absensi'][] = attendance('2026-10-12');
action($c, 'approve_action', [], [20]); check(isset($c->session->flash['success']) && $c->db->tables['absensi'][0]['jam_pulang'] === '00:00:00', 'early leave does not fake checkout or read missing column');
$c = fixture(); $c->db->tables['tbl_pengajuan_izin'][] = request(); action($c, 'approve_action', [], [20]);
check(count($c->db->tables['absensi']) === 2 && $c->db->commits === 1, 'two-day leave atomic');
check($c->db->tables['tbl_quota_cuti'][0]['terpakai'] == 2, 'leave quota usage');
$c = fixture(); $c->db->tables['tbl_pengajuan_izin'][] = request(); $c->db->fail = 'absensi'; $before = $c->db->tables; action($c, 'approve_action', [], [20]); check($c->db->tables === $before && isset($c->session->flash['error']), 'failed attendance rolls back approval');
$c = fixture(); $c->db->tables['tbl_pengajuan_izin'][] = request(); $c->db->throw = 'tbl_quota_cuti'; $before = $c->db->tables; action($c, 'approve_action', [], [20]); check($c->db->tables === $before && !$c->db->open, 'TypeError quota rolls back all');
$c = fixture(); $c->db->tables['tbl_pengajuan_izin'][] = request(); $c->db->tables['absensi'][] = attendance('2026-10-12'); action($c, 'approve_action', [], [20]); check($c->db->tables['tbl_pengajuan_izin'][0]['status'] === 'Pending' && $c->db->tables['absensi'][0]['status'] === 'Hadir', 'full leave cannot overwrite attendance');
$c = fixture(); $c->db->tables['tbl_pengajuan_izin'][] = request(); $c->db->tables['tbl_pengajuan_izin'][0]['karyawan_id'] = 2; action($c, 'approve_action', [], [20]); check($c->db->tables['tbl_pengajuan_izin'][0]['status'] === 'Pending', 'approval company scoped');
$c = fixture(); $c->db->tables['tbl_pengajuan_koreksi_absen'][] = ['id' => 5, 'karyawan_id' => 1, 'tanggal_absen' => '2026-10-12', 'jam_masuk_baru' => '08:20:00', 'jam_pulang_baru' => null, 'status' => 'Pending'];
action($c, 'approve_koreksi', [], [5]); check($c->db->tables['absensi'][0]['jam_pulang'] === '00:00:00' && $c->db->tables['tbl_pengajuan_koreksi_absen'][0]['status'] === 'Disetujui', 'correction defaults');
action($c, 'reject_koreksi', [], [5]); check($c->db->tables['tbl_pengajuan_koreksi_absen'][0]['status'] === 'Disetujui', 'correction final state preserved');
$c = fixture(); $c->db->tables['tbl_pengajuan_koreksi_absen'][] = ['id' => 5, 'karyawan_id' => 1, 'tanggal_absen' => '2026-10-12', 'jam_masuk_baru' => '08:20:00', 'jam_pulang_baru' => null, 'status' => 'Pending']; $c->db->fail = 'absensi'; $before = $c->db->tables;
action($c, 'approve_koreksi', [], [5]); check($c->db->tables === $before, 'correction write failure atomic');
$c = fixture(); $post = ['karyawan_id' => 1, 'tanggal' => '2026-10-12', 'status' => 'Cuti', 'jam_masuk' => '08:00:30', 'jam_pulang' => '17:00', 'keterangan' => 'Manual'];
action($c, 'update_attendance_manual', $post); check(isset($c->session->flash['success']) && $c->db->tables['absensi'][0]['jam_masuk'] === '08:00:30', 'manual seconds not duplicated');
check($c->db->tables['tbl_quota_cuti'][0]['terpakai'] == 1 && count($c->db->tables['tbl_pengajuan_izin']) === 1, 'manual leave synced');
action($c, 'update_attendance_manual', $post); check(count($c->db->tables['absensi']) === 1 && count($c->db->tables['tbl_pengajuan_izin']) === 1, 'manual upsert avoids duplicates');
$post['status'] = 'Hadir'; action($c, 'update_attendance_manual', $post); check(!$c->db->tables['tbl_pengajuan_izin'] && $c->db->tables['tbl_quota_cuti'][0]['terpakai'] == 0, 'manual leave reverted');
$c = fixture(); $c->db->tables['absensi'][] = attendance('2026-10-12'); $post['id'] = 10; $post['karyawan_id'] = 2; $before = $c->db->tables; action($c, 'update', $post); check($c->db->tables === $before, 'forged manual ID blocked');
$c = fixture(); $row = ['karyawan_id' => 1, 'status' => 'Hadir', 'jam_masuk' => '08:00', 'jam_pulang' => '17:00'];
$r = action($c, 'save_manual_batch', ['tanggal' => '2026-10-12', 'attendance' => [$row, $row]]); check(!$r['status'] && !$c->db->tables['absensi'], 'duplicate batch rejected');
$r = action($c, 'save_manual_batch', ['tanggal' => '2026-10-12', 'attendance' => 'invalid']); check(!$r['status'], 'invalid batch shape');
$c->db->fail = 'tbl_quota_cuti'; $before = $c->db->tables; $r = action($c, 'save_manual_batch', ['tanggal' => '2026-10-12', 'attendance' => [$row]]); check(!$r['status'] && $c->db->tables === $before, 'batch quota failure rollback');
$c = fixture(); $c->db->tables['tbl_quota_cuti'][] = ['id' => 3, 'karyawan_id' => 1, 'tahun' => date('Y'), 'quota_awal' => 0]; call_private($c, '_sync_quota_cuti', 1); check($c->db->tables['tbl_quota_cuti'][0]['sisa'] == 0 && $c->db->tables['tbl_quota_cuti'][0]['quota_awal'] == 0, 'zero quota preserved');
$c = fixture(); $c->db->tables['tbl_pengajuan_izin'][] = request('cuti menikah', 9); call_private($c, '_sync_quota_cuti', 1, 2026); check($c->db->tables['tbl_quota_cuti'][0]['terpakai'] == 2 && $c->db->tables['tbl_quota_cuti'][0]['menikah_sisa'] == 0, 'special spillover retained');
$c = fixture(); $quota_post = ['karyawan_id' => 1, 'sisa' => 0, 'menikah_sisa' => 7, 'duka_sisa' => 3, 'sakitkel_sisa' => 2, 'melahirkansuami_sisa' => 3, 'melahirkan_sisa' => 90]; action($c, 'update_quota_cuti', $quota_post); check(isset($c->session->flash['success']) && $c->db->tables['tbl_quota_cuti'][0]['sisa'] == 0, 'quota editor accepts zero');
$quota_post['sisa'] = -1; $before = $c->db->tables; action($c, 'update_quota_cuti', $quota_post); check($c->db->tables === $before && isset($c->session->flash['error']), 'negative quota rejected');
$c = fixture(); $c->db->tables['absensi'][] = array_merge(attendance('2026-10-12'), ['jam_pulang' => '19:00:00']);
$r = action($c, 'sync_to_payroll', ['karyawan_id' => 1, 'bulan' => '2026-10', 'start_date' => '2026-10-12', 'end_date' => '2026-10-12', 'total_hadir' => 9999, 'total_lembur' => 9999]);
check($r['success'] && $c->db->tables['tbl_absensi_rekap_karyawan'][0]['total_hadir'] === 1 && $c->db->tables['tbl_absensi_rekap_karyawan'][0]['total_lembur'] == 0, 'payroll uses server totals');
check($c->db->tables['tbl_absensi_rekap_karyawan'][0]['biaya_makan_lembur'] === 15000 && $c->db->db_debug, 'payroll allowance server calculated and debug restored');
$r = action($c, 'sync_to_payroll', ['karyawan_id' => 1, 'bulan' => '2026-09', 'start_date' => '2026-10-12', 'end_date' => '2026-10-12']); check(!$r['success'], 'payroll mismatched month rejected');
$c = fixture(); $r = action($c, 'sync_to_payroll', ['karyawan_id' => 2, 'start_date' => '2026-10-12', 'end_date' => '2026-10-12']); check(!$r['success'] && !$c->db->tables['tbl_absensi_rekap_karyawan'], 'payroll company scope');
$c = fixture(); $c->db->tables['absensi'] = [attendance('2026-10-12'), array_merge(attendance('2026-10-12'), ['id' => 11])];
$r = action($c, 'sync_to_payroll', ['karyawan_id' => 1, 'start_date' => '2026-10-12', 'end_date' => '2026-10-12']); check(!$r['success'], 'payroll duplicate attendance rejected');
$c = fixture(); $c->data['is_readonly'] = true; $c->db->tables['tbl_pengajuan_izin'][] = request(); action($c, 'approve_action', [], [20]);
check($c->db->tables['tbl_pengajuan_izin'][0]['status'] === 'Pending' && !$c->db->tables['absensi'], 'readonly GET approval blocked');
$c = fixture(); $c->db->tables['tbl_pengajuan_izin'][] = request('izin_telat', 0); $c->db->tables['tbl_pengajuan_izin'][0]['durasi_tipe'] = 'jam'; $c->db->tables['absensi'][] = attendance('2026-10-12');
action($c, 'approve_action', [], [20]); check($c->db->tables['absensi'][0]['is_izin_telat'] === 1 && $c->db->tables['absensi'][0]['jam_masuk'] === '08:00:00', 'late approval marks existing record without replacing time');
$c = fixture(); $late = request('izin_telat', 0); $late['tanggal_mulai'] = date('Y-m-d'); $late['status'] = 'Approved'; $late['durasi_tipe'] = 'jam'; $c->db->tables['tbl_pengajuan_izin'][] = $late;
action($c, 'absen_masuk', ['karyawan_id' => 1]); check($c->db->tables['absensi'][0]['is_izin_telat'] === 1, 'approved late permission applied on actual arrival');
$c = fixture(); $c->db->tables['tbl_pengajuan_izin'][] = request(); action($c, 'reject_action', [], [20]); check($c->db->tables['tbl_pengajuan_izin'][0]['status'] === 'Rejected' && $c->db->tables['tbl_quota_cuti'][0]['terpakai'] == 0, 'pending rejection releases quota');
$c = fixture(); $c->db->tables['tbl_pengajuan_izin'][] = request(); $c->db->fail = 'tbl_quota_cuti'; $before = $c->db->tables; action($c, 'reject_action', [], [20]); check($c->db->tables === $before, 'rejection quota failure atomic');
$c = fixture(); $c->db->tables['absensi'][] = attendance(); $c->db->tables['tbl_karyawan'][1]['perusahaan_id'] = 2;
$before = $c->db->tables; action($c, 'update', ['id' => 10, 'karyawan_id' => 2, 'tanggal' => date('Y-m-d'), 'status' => 'Hadir']); check($c->db->tables === $before, 'manual ID employee mismatch in same branch');
$c = fixture(); action($c, 'add_riwayat_manual', ['karyawan_id' => 1, 'tipe_izin' => 'Cuti', 'tanggal_mulai' => '2025-12-20', 'durasi_jumlah' => 2, 'keterangan' => 'History']);
check($c->db->tables['tbl_quota_cuti'][0]['tahun'] == 2025 && $c->db->tables['tbl_quota_cuti'][0]['terpakai'] == 2 && $c->db->tables['tbl_pengajuan_izin'][0]['responded_by'] === 7, 'historical manual quota uses proper year and identity');
$c = fixture(); $req = request(); $req['status'] = 'Approved'; $c->db->tables['tbl_pengajuan_izin'][] = $req; $c->db->tables['absensi'][] = attendance('2026-10-12', 'Cuti');
$r = action($c, 'delete_riwayat_cuti', [], [20]); check($r['status'] === 'error' && count($c->db->tables['tbl_pengajuan_izin']) === 1, 'linked history deletion blocked');
$c = fixture(); $req = request(); $req['status'] = 'Approved'; $c->db->tables['tbl_pengajuan_izin'][] = $req; $r = action($c, 'delete_riwayat_cuti', [], [20]); check($r['status'] === 'success' && !$c->db->tables['tbl_pengajuan_izin'] && $c->db->tables['tbl_quota_cuti'][0]['terpakai'] == 0, 'unlinked manual history delete syncs quota');
$c = fixture(); $c->db->tables['tbl_pengajuan_koreksi_absen'][] = ['id' => 5, 'karyawan_id' => 1, 'tanggal_absen' => '2026-10-12', 'jam_masuk_baru' => '25:99:00', 'status' => 'Pending']; action($c, 'approve_koreksi', [], [5]); check(!$c->db->tables['absensi'] && $c->db->tables['tbl_pengajuan_koreksi_absen'][0]['status'] === 'Pending', 'invalid correction time blocks approval');
$c = fixture(); $c->db->fail = 'tbl_absensi_rekap_karyawan'; $r = action($c, 'sync_to_payroll', ['karyawan_id' => 1, 'start_date' => '2026-10-12', 'end_date' => '2026-10-12']); check(!$r['success'] && !$c->db->open && $c->db->db_debug, 'payroll write error rollback and debug restore');
$c = fixture(); action($c, 'absen_masuk', ['karyawan_id' => 1, 'foto_selfie' => 'data:image/jpeg;base64,' . base64_encode('bad')]); check(!$c->db->tables['absensi'] && isset($c->session->flash['error']), 'invalid supplied selfie blocks attendance');
$c = fixture(); foreach (['data:image/jpeg;base64,' . base64_encode('<?php echo 1;'), 'data:image/png;base64,@@@', [], 'not-image'] as $image) check(call_private($c, '_upload_selfie', $image, 1, 'masuk') === '', 'malformed selfie rejected');
$png = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII=';
$path = call_private($c, '_upload_selfie', $png, 1, 'masuk'); check($path !== '' && is_file(FCPATH . $path), 'real PNG saved');
check(call_private($c, '_upload_selfie', $png, '../outside', 'masuk') === '', 'selfie path traversal blocked');
unlink(FCPATH . $path); rmdir(FCPATH . 'uploads/absensi'); rmdir(FCPATH . 'uploads'); rmdir(FCPATH);
echo "Absensi controller: $checks checks passed.\n";
