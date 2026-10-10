<?php
/** Offline regression tests. Does not load CodeIgniter, config, or an ERP database. */
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('BASEPATH', __DIR__);
error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) { throw new ErrorException($message, 0, $severity, $file, $line); });
function log_message($level, $message) {}

class TestSession
{
    public function userdata($key) { return ['id' => 9, 'level' => 4, 'perusahaan_id' => 1][$key] ?? null; }
}
class TestLoader { public function library($name) {} public function model($name, $alias = null) {} }
class DisabledQuality { public function assert_storage() {} public function enroll($item, $actor) {} public function assert_handover($item) {} }
class CI_Model
{
    public $db;
    public $load;
    public $session;
    public function __construct() { $this->load = new TestLoader(); $this->session = new TestSession(); }
}
class TestRows
{
    private $rows;
    public function __construct($rows) { $this->rows = unserialize(serialize(array_values($rows))); }
    public function row() { return $this->rows[0] ?? null; }
    public function result() { return $this->rows; }
}
class MemoryProductionDb
{
    public $tables;
    public $depth = 0;
    public $fail_log = false;
    public $locks = [];
    public $engine = 'InnoDB';
    private $snapshot;
    private $conditions = [];
    private $affected = 0;
    public function __construct($tables) { $this->tables = $tables; }
    public function trans_begin() { if (!$this->depth) $this->snapshot = serialize($this->tables); $this->depth++; return true; }
    public function trans_commit() { if (!$this->depth) return false; $this->depth--; return true; }
    public function trans_rollback() { if (!$this->depth) return false; if (--$this->depth === 0) $this->tables = unserialize($this->snapshot); return true; }
    public function trans_status() { return true; }
    public function affected_rows() { return $this->affected; }
    public function where($key, $value = null) { foreach (is_array($key) ? $key : [$key => $value] as $k => $v) $this->conditions[$k] = $v; return $this; }
    public function or_where($key, $value) { $this->conditions[$key] = $value; return $this; }
    public function select($fields) { return $this; }
    public function group_start() { return $this; }
    public function group_end() { return $this; }
    public function order_by($field, $direction = null) { return $this; }
    public function get_where($table, $where) { return $this->where($where)->get($table); }
    public function get($table) {
        $rows = array_filter($this->tables[$table] ?? [], function ($row) {
            foreach ($this->conditions as $key => $value) {
                if ($key === 'harga_id' && ($row->$key ?? null) === null) continue;
                if (($row->$key ?? null) != $value) return false;
            }
            return true;
        });
        $this->conditions = [];
        return new TestRows($rows);
    }
    public function insert($table, $data) {
        if ($this->fail_log && in_array($table, ['tbl_stok_log', 'tbl_produksi_log'], true)) return false;
        if ($table === 'detail_produksi') $data['detail_produksi_id'] = count($this->tables[$table]) + 1;
        $this->tables[$table][] = (object) $data;
        return true;
    }
    public function update($table, $data) {
        foreach ($this->tables[$table] as $row) {
            $match = true;
            foreach ($this->conditions as $key => $value) if (($row->$key ?? null) != $value) $match = false;
            if ($match) foreach ($data as $key => $value) $row->$key = $value;
        }
        $this->conditions = [];
        return true;
    }
    public function query($sql, $binds = []) {
        if (strpos($sql, 'SHOW TABLE STATUS') === 0) {
            preg_match_all("/'([^']+)'/", $sql, $matches);
            return new TestRows(array_map(function ($name) { return (object) ['Name' => $name, 'Engine' => $this->engine]; }, $matches[1]));
        }
        if (strpos($sql, 'FOR UPDATE') !== false) $this->locks[] = $sql;
        if (strpos($sql, 'SELECT * FROM tbl_produksi ') === 0) return $this->get_where('tbl_produksi', ['produksi_id' => $binds[0]]);
        if (strpos($sql, 'SELECT * FROM detail_produksi ') === 0) {
            $rows = $this->get_where('detail_produksi', ['produksi_id' => $binds[0]])->result();
            usort($rows, function ($a, $b) { return [$a->routing_urutan, $a->detail_produksi_id] <=> [$b->routing_urutan, $b->detail_produksi_id]; });
            return new TestRows($rows);
        }
        if (strpos($sql, 'SELECT konversi FROM tbl_material ') === 0 || strpos($sql, 'SELECT material_id FROM tbl_material ') === 0) return $this->get_where('tbl_material', ['material_id' => $binds[0]]);
        if (strpos($sql, 'SELECT * FROM tbl_stok_gudang ') === 0) { $where = ['material_id' => $binds[0], 'gudang_id' => $binds[1]]; if (count($binds) > 2) $where['rak_id'] = $binds[2]; return $this->get_where('tbl_stok_gudang', $where); }
        if (strpos($sql, 'UPDATE tbl_stok_gudang SET qty = qty +') === 0) {
            $this->affected = 0;
            foreach ($this->tables['tbl_stok_gudang'] as $row) {
                if ($row->stok_id == $binds[2] && $row->qty + $binds[3] >= 0) { $row->qty += $binds[0]; $this->affected++; }
            }
            return true;
        }
        throw new RuntimeException('Unexpected offline query: ' . $sql);
    }
}

require __DIR__ . '/../libraries/Production_workflow.php';
require __DIR__ . '/../models/Wip_workflow_model.php';
require __DIR__ . '/../models/M_gudang.php';

$checks = 0;
function check($condition, $label) { global $checks; if (!$condition) throw new RuntimeException($label); $checks++; }
function rejected($fn, $label) { try { $fn(); } catch (DomainException $e) { check(true, $label); return; } throw new RuntimeException($label); }
function fixture($header = 'proses', $step_status = '1', $qty = 10) {
    return new MemoryProductionDb([
        'tbl_produksi' => [(object) ['produksi_id' => 1, 'product_id' => 10, 'harga_id' => 1, 'qty' => $qty,
            'nospk' => 'OFFLINE-1', 'status_produksi' => $header, 'lokasi_cetak' => 1, 'perusahaan_id' => 1, 'tgl_mulai' => '2026-01-01 10:00:00']],
        'detail_produksi' => [(object) ['detail_produksi_id' => 11, 'produksi_id' => 1, 'routing_id' => 21,
            'routing_urutan' => 1, 'status_produksi' => $step_status, 'operator_id' => 70, 'mesin_id' => 3,
            'waktu_mulai' => '2026-01-01 10:00:00', 'waktu_selesai' => null, 'qty_output' => 0,
            'qty_missing' => 0, 'qty_bahan_pakai' => 0, 'qty_bahan_rusak' => 0]],
        'routing' => [(object) ['routing_id' => 21, 'product_id' => 10, 'harga_id' => null, 'routing_urutan' => 1,
            'operator_id' => 7, 'routing_name' => 'Cetak', 'is_finishing' => 0]],
        'tbl_pemesanan' => [(object) ['nospk' => 'OFFLINE-1', 'status_pembayaran' => 'paid']],
        'tbl_material' => [(object) ['material_id' => 5, 'konversi' => 500]],
        'tbl_stok_gudang' => [(object) ['stok_id' => 1, 'material_id' => 5, 'gudang_id' => 1, 'rak_id' => null, 'qty' => 2000]],
        'tbl_stok_log' => [], 'tbl_produksi_log' => []
    ]);
}
function service($db) { $model = new Wip_workflow_model(); $model->db = $db; $model->wip_quality = new DisabledQuality(); return $model; }
$actor = ['user_id' => 9, 'employee_id' => 70, 'branch' => 1, 'admin' => false, 'operator_role' => 7];

foreach (['2', '3', 'done', 'on_pb', 'on_costumer'] as $status) check(Production_workflow::step_completed($status), 'Legacy completed ' . $status);
foreach (['0', '1', '', '4'] as $status) check(!Production_workflow::step_completed($status), 'Open/unknown ' . $status);
check(Production_workflow::number('1,25', 'Qty') === 1.25, 'Decimal comma');
foreach ([-1, 'abc', INF, NAN, null] as $invalid) rejected(function () use ($invalid) { Production_workflow::number($invalid, 'Qty'); }, 'Invalid quantity');
check(Production_workflow::ids([3, '1', 3, 2]) === [1, 2, 3], 'Stable unique lock order');
rejected(function () { Production_workflow::ids(['1 OR 1']); }, 'Invalid IDs');
check(!Production_workflow::can_transition('pending', 'done'), 'No pending to done');
check(!Production_workflow::can_transition('proses', 'on_pb'), 'No in-progress to PB');
check(Production_workflow::can_transition('done', 'on_pb'), 'Done to PB');

$db = fixture(); $workflow = service($db);
$result = $workflow->execute([1], 'complete', $actor, [1 => 11], [], function () { return ['qty_output' => 10]; });
check($result['success'], 'Complete actual output');
check($db->tables['tbl_produksi'][0]->status_produksi === 'done', 'Header completed');
check($db->tables['detail_produksi'][0]->status_produksi === '2', 'Operation remains numeric completed');
check($db->depth === 0 && count($db->locks) >= 2, 'Transaction closes and locks records');
$before = serialize($db->tables);
$retry = $workflow->execute([1], 'complete', $actor, [1 => 11], [], function () { return ['qty_output' => 10]; });
check(!$retry['success'] && serialize($db->tables) === $before, 'Retry does not duplicate results');
$pb = $workflow->execute([1], 'handover', $actor);
check($pb['success'] && $db->tables['tbl_produksi'][0]->status_produksi === 'on_pb', 'Checked PB handover');
check($db->tables['detail_produksi'][0]->status_produksi === '2' && $db->tables['detail_produksi'][0]->waktu_mulai === '2026-01-01 10:00:00', 'PB retains operation evidence');
$before = serialize($db->tables);
check($workflow->execute([1], 'handover', $actor)['success'] && serialize($db->tables) === $before, 'PB retry is read-only');

$db = fixture(); $workflow = service($db); $stock = new M_gudang(); $stock->db = $db;
$version = Production_workflow::version($db->tables['detail_produksi'][0]);
$result = $workflow->execute([1], 'complete', $actor, [1 => 11], [1 => $version], function () use ($stock) {
    $stock->update_stock_with_log(5, 1, -0.2, 'Produksi', 1, 'Offline partial', null, 70);
    return ['qty_output' => 4, 'qty_missing' => 1, 'qty_bahan_pakai' => 100];
});
check($result['success'] && $db->tables['detail_produksi'][0]->status_produksi === '1', 'Partial output remains active');
check($db->tables['detail_produksi'][0]->qty_output === 4.0 && $db->tables['tbl_stok_gudang'][0]->qty === 1900.0, 'Partial result and base-unit consumption');
$before = serialize($db->tables);
$result = $workflow->execute([1], 'complete', $actor, [1 => 11], [1 => $version], function () { return ['qty_output' => 4]; });
check(!$result['success'] && serialize($db->tables) === $before, 'Partial retry/version conflict rolls back');
check(!$workflow->execute([1], 'handover', $actor)['success'], 'Partial output cannot handover');
check(!$workflow->execute([1], 'cancel', $actor, [1 => 11])['success'], 'Cannot erase used operation');

foreach (['stock_shortage', 'log_failure'] as $failure) {
    $db = fixture(); $workflow = service($db); $stock = new M_gudang(); $stock->db = $db;
    $db->fail_log = $failure === 'log_failure';
    $before = serialize($db->tables);
    $result = $workflow->execute([1], 'complete', $actor, [1 => 11], [], function () use ($stock, $failure) {
        $stock->update_stock_with_log(5, 1, $failure === 'stock_shortage' ? -5 : -1, 'Produksi', 1);
        return ['qty_output' => 10];
    });
    check(!$result['success'] && serialize($db->tables) === $before && $db->depth === 0, 'Atomic rollback: ' . $failure);
}

foreach ([['branch' => 2], ['operator_role' => 8], ['operator_role' => null]] as $overrides) {
    $db = fixture(); $before = serialize($db->tables);
    $result = service($db)->execute([1], 'complete', array_merge($actor, $overrides), [1 => 11], [], function () { return ['qty_output' => 10]; });
    check(!$result['success'] && serialize($db->tables) === $before, 'Record/operation authorization');
}
$db = fixture('pending', '0'); $db->tables['detail_produksi'][0]->waktu_mulai = null;
check(!service($db)->execute([1], 'complete', $actor, [1 => 11], [], function () { return ['qty_output' => 10]; })['success'], 'Cannot finish pending operation');
check(service($db)->execute([1], 'start', $actor)['success'], 'Start pending operation');
check(!service($db)->execute([1], 'start', $actor)['success'], 'Double start does not open another operation');
$start_time = $db->tables['detail_produksi'][0]->waktu_mulai;
check(service($db)->execute([1], 'cancel', $actor, [1 => 11])['success'], 'Cancel unused start');
check($db->tables['detail_produksi'][0]->waktu_mulai === $start_time && $db->tables['detail_produksi'][0]->operator_id === 70, 'Cancel preserves evidence');

$db = fixture();
$db->tables['detail_produksi'][] = (object) array_merge((array) $db->tables['detail_produksi'][0], ['detail_produksi_id' => 12, 'routing_id' => 22, 'routing_urutan' => 2, 'status_produksi' => '0']);
$db->tables['routing'][] = (object) ['routing_id' => 22, 'product_id' => 10, 'harga_id' => null, 'routing_urutan' => 2, 'operator_id' => 8, 'routing_name' => 'Finishing', 'is_finishing' => 1];
$workflow = service($db);
check(!$workflow->execute([1], 'complete', $actor, [1 => 12], [], function () { return ['qty_output' => 10]; })['success'], 'Cannot skip to later operation');
check($workflow->execute([1], 'complete', $actor, [1 => 11], [], function () { return ['qty_output' => 10]; })['success'], 'Complete first operation');
check($db->tables['tbl_produksi'][0]->status_produksi === 'proses' && $db->tables['detail_produksi'][1]->status_produksi === '0', 'Later operation remains pending');
check(!$workflow->execute([1], 'start', $actor)['success'], 'Next operation requires assigned division');
check($workflow->execute([1], 'start', array_merge($actor, ['operator_role' => 8]))['success'], 'Assigned division can start next operation');

$db = fixture('done', 'on_pb'); $before = serialize($db->tables['detail_produksi']);
check(service($db)->execute([1], 'handover', $actor)['success'] && serialize($db->tables['detail_produksi']) === $before, 'Legacy completed history is read without migration');
$db = fixture('done', '0'); $before = serialize($db->tables);
check(!service($db)->execute([1], 'handover', $actor)['success'] && serialize($db->tables) === $before, 'Inconsistent legacy history is not silently repaired');
$db = fixture(); $db->tables['tbl_produksi'][0]->finishing_id = 99; $db->tables['tbl_produksi'][0]->status_finishing = 'pending';
check(service($db)->execute([1], 'complete', $actor, [1 => 11], [], function () { return ['qty_output' => 10]; })['success'] && $db->tables['tbl_produksi'][0]->status_produksi === 'finishing', 'Unresolved finishing stays visible');

$db = fixture('pending', '0'); $db->tables['detail_produksi'] = [];
check(service($db)->steps($db->tables['tbl_produksi'][0]) === [] && $db->tables['detail_produksi'] === [], 'Reading does not initialize routing');
check(service($db)->execute([1], 'start', $actor)['success'] && count($db->tables['detail_produksi']) === 1, 'Routing initialization happens only during start');

// A failure after the first item succeeds still rolls back the entire batch.
$db = fixture();
$db->tables['tbl_produksi'][] = (object) array_merge((array) $db->tables['tbl_produksi'][0], ['produksi_id' => 2]);
$db->tables['detail_produksi'][] = (object) array_merge((array) $db->tables['detail_produksi'][0], ['detail_produksi_id' => 22, 'produksi_id' => 2]);
$before = serialize($db->tables); $stock = new M_gudang(); $stock->db = $db;
$result = service($db)->execute([2, 1], 'complete', $actor, [1 => 11, 2 => 22], [], function ($item) use ($stock) {
    $stock->update_stock_with_log(5, 1, $item->produksi_id === 1 ? -1 : -5, 'Produksi', $item->produksi_id);
    return ['qty_output' => 10];
});
check(!$result['success'] && serialize($db->tables) === $before && $db->depth === 0, 'Batch second-item failure rolls back first item and stock');

// A zero-output failed attempt can still consume secondary materials; its version must change.
$db = fixture(); $stock = new M_gudang(); $stock->db = $db; $workflow = service($db);
$version = Production_workflow::version($db->tables['detail_produksi'][0]);
$result = $workflow->execute([1], 'complete', $actor, [1 => 11], [1 => $version], function () use ($stock) {
    $stock->update_stock_with_log(5, 1, -0.1, 'Produksi', 1);
    return ['qty_output' => 0, 'qty_missing' => 0, '_failed' => true, '_note' => 'Offline misprint'];
});
check($result['success'] && Production_workflow::version($db->tables['detail_produksi'][0]) !== $version, 'Zero-output failure receives a new version without resetting history');
$before = serialize($db->tables);
$result = $workflow->execute([1], 'complete', $actor, [1 => 11], [1 => $version], function () use ($stock) {
    $stock->update_stock_with_log(5, 1, -0.1, 'Produksi', 1);
    return ['qty_output' => 0, '_failed' => true];
});
check(!$result['success'] && serialize($db->tables) === $before, 'Failed-attempt retry cannot duplicate secondary consumption');

$db = fixture(); $workflow = service($db);
$old_version = Production_workflow::version($db->tables['detail_produksi'][0]);
check($workflow->execute([1], 'cancel', $actor, [1 => 11])['success'], 'Cancel test cycle');
check($workflow->execute([1], 'start', $actor)['success'], 'Resume test cycle');
check(Production_workflow::version($db->tables['detail_produksi'][0]) !== $old_version, 'Cancel/resume invalidates earlier completion request');
$before = serialize($db->tables);
check(!$workflow->execute([1], 'complete', $actor, [1 => 11], [1 => $old_version], function () { return ['qty_output' => 10]; })['success'] && serialize($db->tables) === $before, 'Stale completion after resume is rejected');

$db = fixture(); $before = serialize($db->tables);
check(!service($db)->execute([1], 'complete', $actor, [], [], function () { return ['qty_output' => 10]; })['success'] && serialize($db->tables) === $before, 'Missing operation identity cannot mutate a later operation');
$db = fixture(); $db->tables['tbl_pemesanan'][0]->status_pembayaran = 'unpaid'; $before = serialize($db->tables);
check(!service($db)->execute([1], 'complete', $actor, [1 => 11], [], function () { return ['qty_output' => 10]; })['success'] && serialize($db->tables) === $before, 'Unpaid order follows existing release policy');

$db = fixture(); $db->tables['tbl_produksi'][0]->finishing_id = 99; $db->tables['tbl_produksi'][0]->status_finishing = 'pending';
$db->tables['routing'][0]->is_finishing = 1;
check(service($db)->execute([1], 'complete', $actor, [1 => 11], [], function () { return ['qty_output' => 10]; })['success'] && $db->tables['tbl_produksi'][0]->status_finishing === 'done' && $db->tables['tbl_produksi'][0]->status_produksi === 'done', 'Actual finishing completion resolves existing finishing fields');

$db = fixture(); $db->tables['tbl_stok_gudang'][] = clone $db->tables['tbl_stok_gudang'][0]; $stock = new M_gudang(); $stock->db = $db;
$before = serialize($db->tables);
rejected(function () use ($stock) { $stock->update_stock_with_log(5, 1, -0.1, 'Produksi', 1); }, 'Ambiguous duplicate balance rejected');
check(serialize($db->tables) === $before && $db->depth === 0, 'Duplicate balances left untouched');

$db = fixture(); $db->tables['tbl_stok_gudang'][0]->rak_id = 4; $db->tables['tbl_stok_gudang'][0]->qty = 100;
$db->tables['tbl_stok_gudang'][] = (object) ['stok_id' => 2, 'material_id' => 5, 'gudang_id' => 1, 'rak_id' => 8, 'qty' => 200];
$stock = new M_gudang(); $stock->db = $db;
check($stock->update_stock_with_log(5, 1, -0.5, 'Produksi', 1), 'Actual consumption can use multiple existing rack balances');
check($db->tables['tbl_stok_gudang'][0]->qty === 0.0 && $db->tables['tbl_stok_gudang'][1]->qty === 50.0, 'Rack allocations preserve aggregate balance');
check(count($db->tables['tbl_stok_log']) === 2 && array_sum(array_column($db->tables['tbl_stok_log'], 'qty_base')) === 250.0, 'Rack ledger total equals actual base consumption');
$db = fixture(); $db->tables['tbl_material'][0]->konversi = null; $stock = new M_gudang(); $stock->db = $db;
check($stock->update_stock_with_log(5, 1, -10, 'Produksi', 1) && $db->tables['tbl_material'][0]->konversi === null && $db->tables['tbl_stok_gudang'][0]->qty === 1990.0, 'Unset conversion retains legacy factor one without rewriting master data');
$db = fixture(); $db->engine = 'MyISAM'; $before = serialize($db->tables);
check(!service($db)->execute([1], 'complete', $actor, [1 => 11], [], function () { return ['qty_output' => 10]; })['success'] && serialize($db->tables) === $before && $db->depth === 0, 'Nontransactional storage is rejected before production writes');
$db = fixture(); $stock = new M_gudang(); $stock->db = $db;
check($stock->update_stock_with_log(5, 1, 1, 'Adjustment', 0) && $db->tables['tbl_stok_gudang'][0]->qty === 2500.0 && $db->depth === 0, 'Non-production receipt retains its existing behavior in a transaction');
check(count($db->locks) === 1 && strpos($db->locks[0], 'tbl_material') !== false, 'Non-production writer shares the production material lock');
$db = fixture(); $db->fail_log = true; $stock = new M_gudang(); $stock->db = $db; $before = serialize($db->tables);
check(!$stock->update_stock_with_log(5, 1, 1, 'Adjustment', 0) && serialize($db->tables) === $before && $db->depth === 0, 'Receipt ledger failure rolls back balance');

$db = fixture();
$db->tables['users'] = [(object) ['user_id' => 9, 'karyawan_id' => 70, 'operator_id' => 7], (object) ['user_id' => 10, 'karyawan_id' => 80, 'operator_id' => 8]];
$db->tables['tbl_karyawan'] = [(object) ['karyawan_id' => 70, 'pin_produksi' => '1111', 'perusahaan_id' => 1, 'divisi_id' => 7], (object) ['karyawan_id' => 80, 'pin_produksi' => '2222', 'perusahaan_id' => 1, 'divisi_id' => 8]];
check(service($db)->actor('1111', 7)['employee_id'] === 70, 'PIN resolves actual employee independently of routing role');
rejected(function () use ($db) { service($db)->actor('2222', 7); }, 'PIN from another division cannot authorize operation');
$db->tables['tbl_karyawan'][0]->perusahaan_id = 2;
rejected(function () use ($db) { service($db)->actor('1111', 7); }, 'PIN from another branch is rejected');
// Start needs an authorized session, without requiring an employee PIN.
$db = fixture('pending', '0');
$db->tables['detail_produksi'][0]->operator_id = null;
$starter = array_merge($actor, ['employee_id' => null, 'admin' => true]);
check(service($db)->execute([1], 'start', $starter)['success'], 'Authorized account without linked employee can start without PIN');
check($db->tables['detail_produksi'][0]->operator_id === null, 'Start does not invent an employee identity');
check($db->tables['tbl_produksi_log'][0]->user_id === $starter['user_id'], 'Start audit identifies signed-in user');
$db = fixture('pending', '0'); $before = serialize($db->tables);
check(!service($db)->execute([1], 'start', array_merge($actor, ['employee_id' => null, 'branch' => 2]))['success'] && serialize($db->tables) === $before, 'PIN-free start still enforces branch access');
$db = fixture('pending', '0'); $before = serialize($db->tables);
check(!service($db)->execute([1], 'start', array_merge($actor, ['employee_id' => null, 'operator_role' => 8]))['success'] && serialize($db->tables) === $before, 'PIN-free start still enforces assigned division');

// QC enrollment/guard are part of the same production transaction.
class QualityProbe extends DisabledQuality {
    public $enrolled = 0; public $hold = false; public $fail = false;
    public function enroll($item, $actor) { $this->enrolled++; if ($this->fail) throw new RuntimeException('QC write failure'); }
    public function assert_handover($item) { if ($this->hold) throw new DomainException('QC hold'); }
}
$db = fixture(); $workflow = service($db); $probe = new QualityProbe(); $workflow->wip_quality = $probe;
check($workflow->execute([1], 'complete', $actor, [1 => 11], [], function () { return ['qty_output' => 10]; })['success'] && $probe->enrolled === 1, 'Final completion enrolls QC');
$db = fixture(); $workflow = service($db); $probe = new QualityProbe(); $workflow->wip_quality = $probe;
$version = Production_workflow::version($workflow->steps($db->tables['tbl_produksi'][0])[0]);
check($workflow->execute([1], 'complete', $actor, [1 => 11], [1 => $version], function () { return ['qty_output' => 2]; })['success'] && $probe->enrolled === 0, 'Partial result does not enroll QC');
$db = fixture(); $workflow = service($db); $probe = new QualityProbe(); $probe->fail = true; $workflow->wip_quality = $probe; $before = serialize($db->tables);
check(!$workflow->execute([1], 'complete', $actor, [1 => 11], [], function () { return ['qty_output' => 10]; })['success'] && serialize($db->tables) === $before, 'QC enrollment failure rolls back completion and logs');
$db = fixture('done', '2'); $workflow = service($db); $probe = new QualityProbe(); $probe->hold = true; $workflow->wip_quality = $probe; $before = serialize($db->tables);
check(!$workflow->execute([1], 'handover', $actor)['success'] && serialize($db->tables) === $before, 'Legacy handover endpoint respects QC hold');

$db = fixture('on_pb', '2'); $workflow = service($db); $probe = new QualityProbe(); $probe->hold = true; $workflow->wip_quality = $probe;
rejected(function () use ($workflow, $actor) { $workflow->customer_handover_items([1], $actor); }, 'Customer handover cannot bypass QC hold');
$probe->hold = false;
check(count($workflow->customer_handover_items([1], $actor, 'OFFLINE-1')) === 1, 'PB item with approved QC can reach customer handover');
rejected(function () use ($workflow, $actor) { $workflow->customer_handover_items([1], $actor, 'OTHER-SPK'); }, 'Customer handover validates SPK ownership');
$db->tables['tbl_produksi'][0]->status_produksi = 'done';
rejected(function () use ($workflow, $actor) { $workflow->customer_handover_items([1], $actor); }, 'Customer handover cannot skip PB');

// Machine picker and write validation must use the same item location policy.
$machine = (object) ['perusahaan_id' => 3];
check(!Production_workflow::machine_matches_locations($machine, [2]), 'Cabang machine excluded from Pusat job even for an admin picker');
check(Production_workflow::machine_matches_locations($machine, [3]), 'Machine available for its production location');
check(!Production_workflow::machine_matches_locations($machine, [3, 2]), 'Machine cannot serve a batch containing another location');
check(Production_workflow::machine_matches_locations((object) ['perusahaan_id' => 0], [3, 2]), 'Explicit shared machine remains compatible');
check(!Production_workflow::machine_matches_locations($machine, [0]), 'Unknown production location is rejected');
check(Production_workflow::production_location((object) ['lokasi_cetak' => 3, 'perusahaan_id' => 2]) === 3, 'Print destination takes precedence over sales branch');
check(Production_workflow::production_location((object) ['lokasi_cetak' => 0, 'perusahaan_id' => 2]) === 2, 'Legacy unset print destination uses sales branch');
echo "PASS: $checks offline production regression checks; no ERP database connection.\n";
