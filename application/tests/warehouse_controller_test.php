<?php
// Disposable memory records, no ERP connection or external notifications.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
if (!defined('BASEPATH')) define('BASEPATH', __DIR__);
define('FCPATH', sys_get_temp_dir() . '/tgm_absensi_' . getmypid() . '/');
date_default_timezone_set('Asia/Jakarta');
error_reporting(E_ALL);
set_error_handler(function ($n, $message, $file, $line) { if (error_reporting() & $n) throw new ErrorException($message, 0, $n, $file, $line); return true; });
function log_message($level, $message) { if (!empty($GLOBALS['warehouse_sql_debug'])) fwrite(STDERR, $message . "\n"); }
function log_activity($user, $action, $module, $id) {}
function redirect($url) { throw new ARedirect($url); }
function show_404() { throw new RuntimeException('404'); }
function show_error($text, $code = 500) { throw new RuntimeException($text, $code); }
function validation_errors() { return 'Invalid form'; }
class ARedirect extends RuntimeException {}
class MY_Controller { public $data = [], $db, $session, $input, $warehouse, $warehouse_stock, $m_gudang, $M_master, $form_validation, $load, $uri; protected function _log_manual($a, $b, $c) {} }
require __DIR__ . '/../controllers/Warehouse.php';
require __DIR__ . '/../libraries/Warehouse_stock.php';
class CI_Model { public $db, $session, $load, $dbforge; public function __construct() {} }
require __DIR__ . '/../models/Warehouse_model.php';
class AInput {
    public $posts = [], $gets = [], $ajax = true;
    public function post($key = null, $clean = false) { return $key === null ? $this->posts : ($this->posts[$key] ?? null); }
    public function get($key, $clean = false) { return $this->gets[$key] ?? null; }
    public function server($key) { return $key === 'REQUEST_METHOD' ? 'POST' : null; }
    public function is_ajax_request() { return $this->ajax; }
    public function user_agent() { return 'Offline test'; }
}
class ASession {
    public $data = ['logged_in' => true, 'id' => 7, 'karyawan_id' => 1, 'perusahaan_id' => 2, 'level' => 5], $flash = [];
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
    public $tables = [], $last_id = 0, $fail, $throw, $open = false, $commits = 0, $rollbacks = 0, $queries = [], $db_debug = true;
    private $conditions = [], $snapshot, $ok = true, $table, $sum;
    public function from($table) { $this->table = $table; return $this; }
    public function join($a, $b, $c = '') { return $this; }
    public function select_sum($key) { $this->sum = $key; return $this; }
    public function group_start() { return $this; }
    public function group_end() { return $this; }
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
    public function get($table = null) { $table = $table ?: $this->table; $rows = array_filter($this->tables[$table] ?? [], function ($r) { return $this->matches($r); }); if ($this->sum) { $total = 0; foreach ($rows as $r) $total += (float)$r[$this->sum]; $rows = [[$this->sum => $total]]; $this->sum = null; } $this->conditions = []; $this->table = null; return new ARows($rows); }
    public function get_where($table, $where) { return $this->where($where)->get($table); }
    public function query($sql, $binds = []) {
        $this->queries[] = $sql;
        if ($sql === "SHOW COLUMNS FROM tbl_pembelian LIKE 'metode_bayar'") return new ARows([['Type' => "enum('cash','kredit')"]]);
        if ($sql === 'SHOW COLUMNS FROM tbl_hutang') return new ARows([['Field' => 'sisa_hutang', 'Extra' => '']]);
        if ($sql === 'SHOW TABLE STATUS WHERE Name = ?') return new ARows([['Engine' => 'InnoDB']]);
        if (preg_match('/^SELECT (?:\*|\w+) FROM (\w+) WHERE (\w+) = \?(.*)$/', $sql, $m)) {
            $where = [$m[2] => $binds[0]];
            if (strpos($m[3], 'AND gudang_id = ?') !== false) $where['gudang_id'] = $binds[1];
            if (strpos($m[3], 'AND rak_id = ?') !== false) $where['rak_id'] = $binds[2];
            if (strpos($m[3], 'AND rak_id IS NULL') !== false) $where['rak_id'] = null;
            return $this->get_where($m[1], $where);
        }
        throw new RuntimeException('Unexpected SQL ' . $sql);
    }
    private function can_write($table) {
        if ($this->throw === $table) { $this->throw = null; throw new TypeError('Injected failure'); }
        if ($this->fail === $table) { $this->ok = false; $this->conditions = []; return false; }
        return true;
    }
    public function insert($table, $data) {
        if (!$this->can_write($table)) return false;
        $id = count($this->tables[$table] ?? []) + 100; $this->last_id = $id;
        $keys = ['tbl_stok_gudang' => 'stok_id', 'tbl_stok_log' => 'log_id', 'tbl_temp_beli' => 'temp_id', 'tbl_temp_pengambilan' => 'temp_id', 'tbl_pembelian' => 'pembelian_id', 'tbl_pengambilan' => 'pengambilan_id', 'tbl_stok_opname' => 'son_id', 'tbl_pengeluaran' => 'id', 'tbl_pengajuan_material' => 'pengajuan_id'];
        $data[$keys[$table] ?? 'id'] = $id;
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
    public function insert_id() { return $this->last_id; }
    public function error() { return ['code' => 0, 'message' => 'Injected memory failure']; }
    public function empty_table($table) { return $this->delete($table); }
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
    $c = (new ReflectionClass('Warehouse'))->newInstanceWithoutConstructor(); $c->db = new ADb; $c->session = new ASession; $c->input = new AInput;
    $c->warehouse_stock = new Warehouse_stock(['db' => $c->db, 'session' => $c->session]);
    $c->warehouse = new Warehouse_model; $c->warehouse->db = $c->db; $c->warehouse->session = $c->session;
    $c->db->tables = [
        'users' => [['user_id' => 7]], 'tbl_karyawan' => [],
        'tbl_material' => [['material_id' => 1, 'bahan_id' => 1, 'konversi' => 500, 'varian' => 'Paper', 'satuan_gudang_id' => 1], ['material_id' => 2, 'bahan_id' => 2, 'konversi' => 2, 'varian' => 'Test', 'satuan_gudang_id' => 1]],
        'tbl_stok_gudang' => [['stok_id' => 10, 'material_id' => 1, 'gudang_id' => 1, 'rak_id' => 11, 'qty' => 200], ['stok_id' => 11, 'material_id' => 1, 'gudang_id' => 1, 'rak_id' => null, 'qty' => 500]],
        'tbl_gudang' => [['gudang_id' => 1, 'gudang_nama' => 'Gudang Utama'], ['gudang_id' => 2, 'gudang_nama' => 'Produksi']],
        'tbl_rak' => [['rak_id' => 11, 'gudang_id' => 1, 'is_active' => 1], ['rak_id' => 12, 'gudang_id' => 1, 'is_active' => 1], ['rak_id' => 21, 'gudang_id' => 2, 'is_active' => 1]],
        'tbl_bahan' => [['bahan_id' => 1], ['bahan_id' => 2]], 'tbl_supplier' => [['supplier_id' => 1, 'supplier_nama' => 'Test Supplier']],
        'tbl_rekening' => [['id' => 1, 'active' => '1']], 'tbl_stok_log' => [], 'tbl_temp_beli' => [], 'tbl_temp_pengambilan' => [], 'tbl_pembelian' => [], 'tbl_pembelian_detail' => [],
        'tbl_hutang' => [], 'tbl_hutang_pembayaran' => [], 'tbl_return_pembelian' => [], 'tbl_return_pembelian_detail' => [], 'tbl_pengeluaran' => [],
        'tbl_pengambilan' => [], 'tbl_pengambilan_detail' => [], 'tbl_stok_opname' => [], 'tbl_temp_pengajuan' => [], 'tbl_pengajuan_material' => [], 'tbl_pengajuan_material_detail' => [], 'bom_lines' => []];
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
function cart_item($kind, $qty = 1, $user = 7) { return ['temp_id' => 5, 'user_id' => $user, 'perusahaan_id' => 2, 'material_id' => 1, 'bahan_id' => 1, 'rak_id' => 11, 'qty' => $qty, 'harga' => 1000, 'keterangan' => 'Test']; }
function purchase_post($method = 'Cash') { return ['supplier_id' => 1, 'pembelian_tgl' => '2026-10-12', 'no_faktur' => 'TEST-INV', 'pembelian_total' => 999999, 'metode_bayar' => $method, 'rekening_id' => 1, 'rekening_tujuan' => 'Test', 'jatuh_tempo' => '2026-11-12']; }
$c = fixture();
foreach ([-1, 'NaN', 'INF', [], '1.234', 'bad'] as $value) { try { call_private($c, '_number', $value); check(false, 'bad number'); } catch (InvalidArgumentException $e) { check(true, 'invalid amount rejected'); } }
check(call_private($c, '_number', '1.234,50', false, true) == 1234.5, 'localized money parsed');
check(call_private($c, '_number', '12.50') == 12.5, 'plain decimal preserved');
foreach (['2026-02-30', [], '12-10-2026'] as $date) { try { call_private($c, '_date', $date); check(false, 'date'); } catch (InvalidArgumentException $e) { check(true, 'bad date'); } }
check($c->warehouse->get_current_stock(1, 1) == 700, 'stock sums all racks');
$r = action($c, 'ajax_update_material_rack', ['material_id' => 1, 'gudang_id' => 1, 'rak_asal' => 11, 'rak_tujuan' => 12]);
check($r['status'] === 'success' && $c->warehouse_stock->total(1, 1) == 700, 'rack move preserves total');
check($c->db->get_where('tbl_stok_gudang', ['rak_id' => 12])->row()->qty == 200 && $c->db->tables['tbl_stok_log'][0]['qty_base'] == 200, 'base quantity not converted twice');
check($c->db->tables['tbl_stok_log'][0]['trx_type'] === 'Mutation' && $c->db->tables['tbl_stok_log'][0]['qty_out'] == 0.4, 'valid enum and display-unit log');
$c = fixture(); $c->db->fail = 'tbl_stok_log'; $before = $c->db->tables; $r = action($c, 'ajax_update_material_rack', ['material_id' => 1, 'gudang_id' => 1, 'rak_asal' => 11, 'rak_tujuan' => 12]); check($r['status'] === 'error' && $c->db->tables === $before, 'rack log failure atomic');
$c = fixture(); $r = action($c, 'ajax_update_material_rack', ['material_id' => 1, 'gudang_id' => 1, 'rak_asal' => 11, 'rak_tujuan' => 21]); check($r['status'] === 'error' && !$c->db->open, 'rack warehouse mismatch');
$c = fixture(); $r = action($c, 'ajax_update_material_rack', ['material_id' => 1, 'gudang_id' => 1, 'rak_asal' => 12, 'rak_tujuan' => 11]); check($r['status'] === 'error' && !$c->db->open, 'empty source ends transaction');
$c = fixture(); $r = action($c, 'ajax_dispatch_add_item', ['material_id' => 1, 'qty' => -1]); check($r['status'] === 'error' && !$c->db->tables['tbl_temp_pengambilan'], 'negative cart blocked');
$r = action($c, 'ajax_dispatch_add_item', ['material_id' => 1, 'qty' => 1.2, 'bahan_id' => 999]); check($r['status'] === 'success' && $c->db->tables['tbl_temp_pengambilan'][0]['bahan_id'] == 1, 'fractional cart uses master material');
$c->db->tables['tbl_temp_pengambilan'][] = cart_item('dispatch', 2, 8);
action($c, 'dispatch_finalize', ['gudang_id' => 2, 'pengambilan_tgl' => '2026-10-12']);
check(isset($c->session->flash['last_dispatch_id']) && $c->warehouse_stock->total(1, 1) == 100 && $c->warehouse_stock->total(1, 2) == 600, 'dispatch across racks balanced');
check(count($c->db->tables['tbl_temp_pengambilan']) === 1 && $c->db->tables['tbl_temp_pengambilan'][0]['user_id'] == 8, 'dispatch leaves other cart');
check($c->db->tables['tbl_pengambilan'][0]['karyawan_id'] === 1 && $c->db->tables['tbl_pengambilan_detail'][0]['qty'] == 1.2, 'employee identity and fractional detail');
$c = fixture(); $c->db->tables['tbl_temp_pengambilan'][] = cart_item('dispatch', 2); $before = $c->db->tables;
action($c, 'dispatch_finalize', ['gudang_id' => 2, 'pengambilan_tgl' => '2026-10-12']); check($c->db->tables === $before && isset($c->session->flash['error']), 'stale picklist insufficient stock rollback');
$c = fixture(); $c->db->tables['tbl_temp_pengambilan'][] = cart_item('dispatch', 1); $c->db->throw = 'tbl_stok_log'; $before = $c->db->tables;
action($c, 'dispatch_finalize', ['gudang_id' => 2, 'pengambilan_tgl' => '2026-10-12']); check($c->db->tables === $before && !$c->db->open, 'dispatch TypeError atomic');
$c = fixture(); $c->db->tables['tbl_temp_pengambilan'][] = cart_item('dispatch', 1, 8); $r = action($c, 'ajax_dispatch_update_qty', ['temp_id' => 5, 'qty' => 0.5]); check($r['status'] === 'error' && $c->db->tables['tbl_temp_pengambilan'][0]['qty'] === 1, 'other cart update blocked');
$c = fixture(); action($c, 'purchase_add_item', ['material_id' => 1, 'rak_id' => 11, 'qty' => 0.25, 'harga' => 2000]); check(isset($c->session->flash['success']) && $c->db->tables['tbl_temp_beli'][0]['user_id'] === 7, 'purchase cart scoped and fractional');
$c->db->tables['tbl_temp_beli'][] = cart_item('purchase', 2, 8); action($c, 'purchase_finalize', purchase_post());
check(isset($c->session->flash['success']) && $c->db->tables['tbl_pembelian'][0]['pembelian_total'] == 500, 'purchase total from server');
check($c->warehouse_stock->total(1, 1) == 825 && $c->db->tables['tbl_pengeluaran'][0]['pengeluaran_jumlah'] == 500, 'purchase stock and expense');
check(count($c->db->tables['tbl_temp_beli']) === 1 && $c->db->tables['tbl_temp_beli'][0]['user_id'] == 8, 'purchase preserves another draft');
action($c, 'purchase_finalize', purchase_post()); check(count($c->db->tables['tbl_pembelian']) === 1 && isset($c->session->flash['error']), 'double finalize creates no second purchase');
$c = fixture(); $c->db->tables['tbl_temp_beli'][] = cart_item('purchase'); $c->db->fail = 'tbl_pengeluaran'; $before = $c->db->tables; action($c, 'purchase_finalize', purchase_post()); check($c->db->tables === $before, 'payment failure restores stock and cart');
$c = fixture(); $c->db->tables['tbl_temp_beli'][] = cart_item('purchase'); action($c, 'purchase_finalize', purchase_post('Hutang')); check(isset($c->session->flash['success']) && $c->db->tables['tbl_pembelian'][0]['metode_bayar'] === 'kredit' && $c->db->tables['tbl_hutang'][0]['total_tagihan'] == 1000, 'debt header stored using real enum');
$c = fixture(); $c->db->tables['tbl_temp_beli'][] = cart_item('purchase'); action($c, 'purchase_finalize', purchase_post('Transfer')); check(isset($c->session->flash['success']) && $c->db->tables['tbl_pengeluaran'][0]['metode_bayar'] === 'transfer', 'transfer preserved in expense despite cash header enum');
$c = fixture(); $c->db->tables['tbl_temp_beli'][] = cart_item('purchase'); action($c, 'purchase_finalize', purchase_post()); $id = $c->db->tables['tbl_pembelian'][0]['pembelian_id'];
$c->db->tables['tbl_stok_gudang'][0]['qty'] = 100; $before_total = $c->warehouse_stock->total(1, 1);
$post = array_merge(purchase_post(), ['material_id' => [1], 'qty' => [1], 'harga' => ['2.000'], 'rak_id' => [11]]);
action($c, 'purchase_update', $post, [$id]); check(isset($c->session->flash['success']) && $c->warehouse_stock->total(1, 1) == $before_total && $c->db->tables['tbl_pembelian'][0]['pembelian_total'] == 2000, 'net edit leaves consumed stock intact');
$post['qty'] = [0.1]; $before = $c->db->tables; action($c, 'purchase_update', $post, [$id]); check($c->db->tables === $before && isset($c->session->flash['error']), 'cannot reduce consumed purchase quantity');
$c = fixture(); $c->db->tables['tbl_temp_beli'][] = cart_item('purchase'); action($c, 'purchase_finalize', purchase_post('Hutang')); $id = $c->db->tables['tbl_pembelian'][0]['pembelian_id']; $c->db->tables['tbl_hutang'][0]['total_bayar'] = 100; $before = $c->db->tables;
action($c, 'purchase_update', array_merge(purchase_post(), ['material_id' => [1], 'qty' => [1], 'harga' => [1000]]), [$id]); check($c->db->tables === $before, 'paid debt preserved on edit');
action($c, 'deletePembelian', [], [$id]); check($c->db->tables === $before, 'paid debt preserved on deletion');
$c = fixture(); $r = action($c, 'actionDeleteMaterial', ['material_id' => 1]); check($r['status'] === 'error' && count($c->db->tables['tbl_material']) === 2, 'stock material deletion blocked');
$c = fixture(); $c->db->tables['tbl_pembelian_detail'][] = ['material_id' => 2]; $r = action($c, 'actionDeleteMaterial', ['material_id' => 2]); check($r['status'] === 'error' && count($c->db->tables['tbl_pembelian_detail']) === 1, 'material history not deleted');
$c = fixture(); $r = action($c, 'actionDeleteMaterial', ['material_id' => 2]); check($r['status'] === 'success' && count($c->db->tables['tbl_material']) === 1, 'unused variant deletable');
$c = fixture(); $r = action($c, 'actionDeleteMaterialBulk', ['ids' => [2,1]]); check($r['status'] === 'error' && count($c->db->tables['tbl_material']) === 2, 'bulk delete atomic if one material used');
$c = fixture(); $post = ['material_id' => 1, 'gudang_id' => 1, 'son_aplikasi' => 1.4, 'son_real' => 1.1, 'sync_now' => '1', 'catatan' => 'Test'];
// Only the POST branch is tested; page config is stubbed below.
// _get_conf is private and reads company/division; provide fixture tables and minimal URI-independent config.
$c->db->tables['tbl_perusahaan'] = [['id_perusahaan' => 2, 'nama_perusahaan' => 'Test']]; $c->db->tables['tbl_divisi'] = [];
action($c, 'opname', $post); check(isset($c->session->flash['success']) && $c->warehouse_stock->total(1, 1) == 550 && $c->db->tables['tbl_stok_opname'][0]['son_real'] == 1.1, 'opname fractional correction uses whole warehouse');
$post['son_aplikasi'] = 1.4; $before = $c->db->tables; action($c, 'opname', $post); check($c->db->tables === $before && isset($c->session->flash['error']), 'stale opname rejected');
$c = fixture(); $c->db->tables['tbl_stok_log'][] = ['log_id' => 1, 'material_id' => 1, 'gudang_id' => 1, 'rak_id' => 11, 'qty_in' => 0.5, 'qty_out' => 0, 'qty_base' => 250, 'qty_balance' => 250]; $r = action($c, 'ajax_sync_stock'); check($r['success'] && $c->db->tables['tbl_stok_gudang'][0]['qty'] == 250 && $c->db->tables['tbl_stok_gudang'][1]['qty'] == 500, 'stock sync uses base rack balance and retains unlogged stock');
$c = fixture(); $c->data['is_readonly'] = true; $r = action($c, 'ajax_sync_stock'); check(!$r['success'] && $c->db->tables['tbl_stok_gudang'][0]['qty'] == 200, 'readonly sync blocked');
$c = fixture(); $c->db->fail = 'tbl_stok_log'; $before = $c->db->tables; $c->db->trans_begin(); try { $c->warehouse_stock->post(1, 1, -100, 'Mutation', 1, '', null, true); check(false, 'post failure'); } catch (RuntimeException $e) { $c->db->trans_rollback(); check($c->db->tables === $before, 'stock engine write failure rollback'); }
$c = fixture(); $header = ['pengajuan_id' => 3, 'perusahaan_id' => 2, 'status' => 0, 'gudang_id' => 2];
$c->db->tables['tbl_pengajuan_material'][] = $header; $c->db->tables['tbl_pengajuan_material_detail'][] = ['pengajuan_id' => 3, 'material_id' => 1, 'qty' => 1, 'keterangan' => 'Test'];
$r = action($c, 'ajax_process_request_to_dispatch', [], [3]); check($r['status'] === 'success' && $c->db->tables['tbl_temp_pengambilan'][0]['user_id'] === 7, 'request staging scoped');
$before = $c->db->tables; $r = action($c, 'ajax_process_request_to_dispatch', [], [3]); check($r['status'] === 'error' && $c->db->tables === $before, 'request cannot process twice');
action($c, 'requests_reject', [], [3]); check($c->db->tables['tbl_pengajuan_material'][0]['status'] === 1, 'verified request cannot be rejected');
$c = fixture(); $header['perusahaan_id'] = 3; $c->db->tables['tbl_pengajuan_material'][] = $header; $r = action($c, 'ajax_process_request_to_dispatch', [], [3]); check($r['status'] === 'error', 'other company request blocked');
$c = fixture(); $header['perusahaan_id'] = 2; $c->db->tables['tbl_pengajuan_material'][] = $header; $c->db->tables['tbl_pengajuan_material_detail'][] = ['pengajuan_id' => 3, 'material_id' => 1, 'qty' => 2, 'keterangan' => 'Test'];
$before = $c->db->tables; $r = action($c, 'ajax_process_request_to_dispatch', [], [3]); check($r['status'] === 'error' && $c->db->tables === $before, 'unavailable request atomic');
$c = fixture(); $c->db->tables['tbl_pembelian'][] = ['pembelian_id' => 1]; $c->db->fail = 'tbl_stok_log'; $before = $c->db->tables; check(!$c->warehouse->factory_reset() && $c->db->tables === $before && $c->db->db_debug, 'failed reset rolls back and restores debug');
check(!array_filter($c->db->queries, function ($q) { return strpos($q, 'TRUNCATE') !== false || strpos($q, 'FOREIGN_KEY_CHECKS') !== false; }), 'reset keeps transactions and FK checks');
$c = fixture(); $c->db->fail = 'tbl_stok_log'; $before = $c->db->tables; $r = action($c, 'ajax_update_material_rack', ['material_id' => 1, 'gudang_id' => 1, 'rak_asal' => 11, 'rak_tujuan' => 12]); check($r['status'] === 'error' && $c->db->db_debug && !$c->db->open, 'write error restores debug setting');
$c = fixture(); $draft = cart_item('purchase'); $draft['user_id'] = null; $draft['perusahaan_id'] = null; $c->db->tables['tbl_temp_beli'][] = $draft;
action($c, 'claim_legacy_draft', ['kind' => 'purchase', 'temp_id' => 5]); check(isset($c->session->flash['success']) && $c->db->tables['tbl_temp_beli'][0]['user_id'] === 7, 'unowned legacy draft can be claimed');
$c->session->data['id'] = 8; $c->db->tables['users'][] = ['user_id' => 8]; action($c, 'claim_legacy_draft', ['kind' => 'purchase', 'temp_id' => 5]); check(isset($c->session->flash['error']) && $c->db->tables['tbl_temp_beli'][0]['user_id'] === 7, 'legacy draft cannot be claimed twice');
function base_url($path = '') { return '/tgm_erp_old/' . $path; }
function html_escape($value) { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
class WViewSecurity { public function get_csrf_token_name() { return 'csrf_token'; } public function get_csrf_hash() { return 'test-hash'; } }
class WViewRenderer {
    public $security;
    public function __construct() { $this->security = new WViewSecurity; }
    public function render($legacy_drafts, $draft_kind) { ob_start(); include __DIR__ . '/../views/admin/warehouse/components/legacy_drafts.php'; return ob_get_clean(); }
}
$html = (new WViewRenderer)->render([(object)['temp_id' => 5, 'material_id' => 1, 'bahan_nama' => '<script>bad</script>', 'varian' => 'Test', 'qty' => 0.5]], 'purchase');
check(strpos($html, '<script>bad</script>') === false && strpos($html, '&lt;script&gt;') !== false, 'draft panel escapes material text');
check(strpos($html, 'csrf_token') !== false && strpos($html, 'claim_legacy_draft') !== false && strpos($html, 'name="temp_id" value="5"') !== false, 'draft form contains CSRF and correct record');
$c = fixture(); try { call_private($c, '_number', 100000000, true); check(false, 'quantity storage bound'); } catch (InvalidArgumentException $e) { check(true, 'quantity bounded by detail storage'); }
$c->db->tables['tbl_material'][0]['konversi'] = 0.01; $c->db->tables['tbl_temp_beli'][] = cart_item('purchase', 0.01); $before = $c->db->tables;
action($c, 'purchase_finalize', purchase_post()); check($c->db->tables === $before && isset($c->session->flash['error']), 'receipt smaller than base storage precision rejected');
$c = fixture(); $c->session->data['level'] = 1; $c->db->tables['tbl_stok_gudang'][0]['qty'] = -100;
action($c, 'reset_stock_gudang', [], [1]); check(isset($c->session->flash['success']) && $c->db->tables['tbl_stok_gudang'][0]['qty'] == 0 && $c->db->tables['tbl_stok_gudang'][1]['qty'] == 0 && count($c->db->tables['tbl_stok_log']) === 2, 'reset zeros each positive and negative rack without deleting history');
echo "Warehouse controller: $checks checks passed.\n";
