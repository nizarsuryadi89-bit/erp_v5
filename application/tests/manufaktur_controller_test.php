<?php
// Offline regressions: disposable in-memory records, never connect to ERP data.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
if (!defined('BASEPATH')) { define('BASEPATH', __DIR__); }
define('FCPATH', sys_get_temp_dir() . '/manufaktur-test/');
error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) { throw new ErrorException($message, 0, $severity, $file, $line); });
function log_message($level, $message) {}
function show_404() { throw new RuntimeException('404'); }
function show_error($message) { throw new RuntimeException($message); }
function redirect($url) { throw new RedirectResult($url); }
class RedirectResult extends RuntimeException {}
class MY_Controller {
    public $db, $input, $output, $session, $uri, $M_master, $data = [], $view;
    protected function load_admin_view($view, $data) { $this->view = $data; }
}
require __DIR__ . '/../controllers/Manufaktur.php';
class TestInput {
    public $posts = [];
    public function post($key, $clean = false) { return $this->posts[$key] ?? null; }
    public function is_ajax_request() { return true; }
}
class TestOutput {
    public $body;
    public function set_content_type($type) { return $this; }
    public function set_output($body) { $this->body = json_decode($body, true); return $this; }
}
class TestSession {
    public $flash = [];
    public function set_flashdata($key, $value) { $this->flash[$key] = $value; }
}
class TestUri { public function segment($n) { return $n === 1 ? 'manufaktur' : ''; } }
class TestRows {
    private $rows;
    public function __construct($rows) { $this->rows = array_values($rows); }
    public function result_array() { return $this->rows; }
    public function result() { return array_map(function ($r) { return (object) $r; }, $this->rows); }
    public function row_array() { return $this->rows[0] ?? null; }
    public function row() { return isset($this->rows[0]) ? (object) $this->rows[0] : null; }
    public function num_rows() { return count($this->rows); }
}
class TestDb {
    public $tables = [], $failTable, $throwTable, $open = false, $commits = 0, $rollbacks = 0;
    private $conditions = [], $snapshot, $lastId, $ok = true, $order, $maxField;
    private $pks = ['tbl_product' => 'product_id', 'tbl_harga_product' => 'harga_id', 'bom_lines' => 'bom_line_id', 'routing' => 'routing_id', 'tbl_bom_dynamic_mapping' => 'id'];
    public function where($key, $value = null) {
        if (is_array($key)) { foreach ($key as $k => $v) { $this->where($k, $v); } }
        else { $this->conditions[] = [$key, $value, false]; }
        return $this;
    }
    public function where_in($key, $values) { $this->conditions[] = [$key, $values, true]; return $this; }
    public function order_by($key, $direction = 'ASC') { $this->order = [$key, $direction]; return $this; }
    public function limit($count) { return $this; }
    public function select_max($field) { $this->maxField = $field; return $this; }
    private function matches($row) {
        foreach ($this->conditions as $c) {
            list($key, $val, $in) = $c;
            if (strpos($key, 'harga_id IS NULL') !== false) { if (!empty($row['harga_id'])) { return false; } continue; }
            $neq = substr($key, -3) === ' !=';
            if ($neq) { $key = substr($key, 0, -3); }
            $actual = $row[$key] ?? null;
            if ($in ? !in_array($actual, $val) : ($neq ? $actual == $val : $actual != $val)) { return false; }
        }
        return true;
    }
    public function get($table) {
        $rows = array_filter($this->tables[$table] ?? [], function ($r) { return $this->matches($r); });
        if ($this->order) { list($key, $dir) = $this->order; usort($rows, function ($a, $b) use ($key, $dir) { return ($a[$key] <=> $b[$key]) * ($dir === 'DESC' ? -1 : 1); }); }
        if ($this->maxField) { $vals = array_column($rows, $this->maxField); $rows = [[$this->maxField => $vals ? max($vals) : null]]; }
        $this->conditions = []; $this->order = null; $this->maxField = null;
        return new TestRows($rows);
    }
    public function get_where($table, $where) { return $this->where($where)->get($table); }
    private function canWrite($table) {
        if ($table === $this->throwTable) { throw new TypeError('Injected write error'); }
        if ($table === $this->failTable) { $this->ok = false; $this->conditions = []; return false; }
        return true;
    }
    public function insert($table, $data) {
        if (!$this->canWrite($table)) { return false; }
        $pk = $this->pks[$table]; $ids = array_column($this->tables[$table] ?? [], $pk);
        $data[$pk] = $ids ? max($ids) + 1 : 1; $this->lastId = $data[$pk];
        $this->tables[$table][] = $data; return true;
    }
    public function insert_id() { return $this->lastId; }
    public function update($table, $data, $where = []) {
        if (!$this->canWrite($table)) { return false; }
        $this->where($where);
        foreach ($this->tables[$table] as &$row) { if ($this->matches($row)) { $row = array_replace($row, $data); } }
        unset($row); $this->conditions = []; return true;
    }
    public function delete($table, $where = []) {
        if (!$this->canWrite($table)) { return false; }
        $this->where($where);
        $this->tables[$table] = array_values(array_filter($this->tables[$table] ?? [], function ($r) { return !$this->matches($r); }));
        $this->conditions = []; return true;
    }
    public function table_exists($table) { return isset($this->tables[$table]); }
    public function field_exists($field, $table) { return $field === 'product_id'; }
    public function count_all_results($table) { return $this->get($table)->num_rows(); }
    public function trans_begin() { $this->snapshot = $this->tables; $this->open = true; $this->ok = true; return true; }
    public function trans_status() { return $this->ok; }
    public function trans_commit() { $this->open = false; $this->commits++; return true; }
    public function trans_rollback() { if ($this->open) { $this->tables = $this->snapshot; } $this->open = false; $this->rollbacks++; return true; }
}
class TestMaster {
    private $db;
    public function __construct($db) { $this->db = $db; }
    public function getProduct($id) { return $this->db->get_where('tbl_product', ['product_id' => $id]); }
    public function getbomkode($order, $id) { return $this->db->get_where('bom_lines', ['product_id' => $id]); }
    public function addData($table, $data) { return $this->db->insert($table, $data); }
}
function fixture() {
    $c = (new ReflectionClass(Manufaktur::class))->newInstanceWithoutConstructor();
    $c->db = new TestDb(); $c->input = new TestInput(); $c->output = new TestOutput();
    $c->session = new TestSession(); $c->uri = new TestUri(); $c->M_master = new TestMaster($c->db);
    $price = ['ket_1' => '1', 'jenisprint_id' => '2', 'side_id' => '0', 'qty_min' => '1', 'qty_max' => '100', 'range_id' => '0', 'metadata' => '{"sisi":"1","mesin":"Konica"}'];
    $c->db->tables = [
        'tbl_product' => [['product_id' => 1, 'product_nama' => 'Sumber', 'product_img' => 'shared.png', 'side_id' => '2'], ['product_id' => 2, 'product_nama' => 'Tujuan', 'product_img' => 'shared.png']],
        'tbl_harga_product' => [array_merge($price, ['harga_id' => 10, 'product_id' => 1]), array_merge($price, ['harga_id' => 20, 'product_id' => 2]), array_merge($price, ['harga_id' => 11, 'product_id' => 1, 'ket_1' => '2'])],
        'bom_lines' => [['bom_line_id' => 30, 'product_id' => 1, 'harga_id' => 10, 'bahan_id' => 50, 'quantity' => 2, 'scrap_factor' => 10, 'panjang' => 12, 'lebar' => 14], ['bom_line_id' => 31, 'product_id' => 2, 'harga_id' => 20, 'bahan_id' => 50]],
        'tbl_bom_dynamic_mapping' => [['id' => 40, 'product_id' => 1, 'attribute_key' => 'sisi', 'attribute_value' => '1', 'bahan_id' => 50, 'divider_factor' => 2, 'scrap_factor' => 0, 'manual_cost' => 50]],
        'routing' => [['routing_id' => 60, 'product_id' => 1, 'harga_id' => 10, 'bom_line_id' => '30', 'labor_cost' => 100, 'routing_urutan' => 1], ['routing_id' => 61, 'product_id' => 1, 'harga_id' => null, 'bom_line_id' => 'dyn_40', 'labor_cost' => 0, 'routing_urutan' => 2], ['routing_id' => 62, 'product_id' => 1, 'harga_id' => null, 'bom_line_id' => '99999', 'labor_cost' => 0, 'routing_urutan' => 3], ['routing_id' => 63, 'product_id' => 2, 'harga_id' => 20, 'bom_line_id' => '31']],
        'tbl_material' => [['material_id' => 50, 'bahan_id' => 500, 'konversi' => 10]],
        'tbl_bahan' => [['bahan_id' => 500, 'satuan_id' => 1]],
        'tbl_belanja_bahan' => [['belanja_id' => 1, 'bahan_id' => 500, 'harga_beli' => 10000]],
        'tbl_pembelian_detail' => [], 'tbl_side' => [], 'tbl_mesin_printing' => [], 'tbl_temp_transaksi' => [], 'tbl_pemesanan_detail' => [], 'tbl_retur_detail' => []
    ];
    return $c;
}
function runAction($c, $method, $posts = [], $args = []) {
    $c->input->posts = $posts; ob_start();
    try { call_user_func_array([$c, $method], $args); } catch (RedirectResult $e) { $c->output->body = ['redirect' => $e->getMessage()]; }
    finally { $text = ob_get_clean(); }
    return $text !== '' ? json_decode($text, true) : $c->output->body;
}
$checks = 0;
function check($ok, $label) { global $checks; $checks++; if (!$ok) { throw new RuntimeException($label); } }
function rowsFor($c, $table, $id) { return array_values(array_filter($c->db->tables[$table], function ($r) use ($id) { return $r['product_id'] == $id; })); }
$c = fixture(); $r = runAction($c, 'clone_product', ['source_id' => 1, 'new_name' => 'Salinan']);
check($r['success'], 'clone succeeds'); $id = $r['new_id'];
$prices = rowsFor($c, 'tbl_harga_product', $id); $bom = rowsFor($c, 'bom_lines', $id); $dyn = rowsFor($c, 'tbl_bom_dynamic_mapping', $id); $routes = rowsFor($c, 'routing', $id);
check(count($prices) === 2, 'clone prices'); check($bom[0]['harga_id'] == $prices[0]['harga_id'], 'clone BOM variation remapped');
check($routes[0]['harga_id'] == $prices[0]['harga_id'], 'clone routing variation remapped'); check($routes[0]['bom_line_id'] == $bom[0]['bom_line_id'], 'clone static link remapped');
check($routes[1]['bom_line_id'] === 'dyn_' . $dyn[0]['id'], 'clone dynamic link remapped'); check($routes[2]['bom_line_id'] == 99999, 'no-material sentinel preserved');
check($dyn[0]['manual_cost'] === 50, 'dynamic costs preserved'); check(!$c->db->open && $c->db->commits === 1, 'clone commits');
foreach (['tbl_product', 'tbl_harga_product', 'tbl_bom_dynamic_mapping', 'bom_lines', 'routing'] as $table) {
    $c = fixture(); $before = $c->db->tables; $c->db->failTable = $table;
    $r = runAction($c, 'clone_product', ['source_id' => 1, 'new_name' => 'Failure']);
    check(!$r['success'], 'failed clone reported ' . $table); check($c->db->tables === $before && !$c->db->open, 'failed clone atomic ' . $table);
}
$c = fixture(); $before = $c->db->tables; $c->db->throwTable = 'routing'; $r = runAction($c, 'clone_product', ['source_id' => 1, 'new_name' => 'Failure']);
check(!$r['success'] && $c->db->tables === $before, 'Throwable clone rollback');
$c = fixture(); $before = $c->db->tables; $r = runAction($c, 'bulk_apply_template', ['template_id' => 1, 'target_ids' => [1, 2], 'options' => ['bom', 'routing']]);
check(!$r['success'] && $c->db->tables === $before && !$c->db->open, 'self-template rejected without deleting source');
foreach ([null, 'bom', ['invalid']] as $options) {
    $c = fixture(); $r = runAction($c, 'bulk_apply_template', ['template_id' => 1, 'target_ids' => [2], 'options' => $options]);
    check(!$r['success'] && !$c->db->open, 'invalid options rejected');
}
$c = fixture(); $r = runAction($c, 'bulk_apply_template', ['template_id' => 1, 'target_ids' => [2, 2], 'options' => ['bom', 'routing']]);
check($r['success'], 'template succeeds'); $bom = rowsFor($c, 'bom_lines', 2); $routes = rowsFor($c, 'routing', 2); $dyn = rowsFor($c, 'tbl_bom_dynamic_mapping', 2);
check(count($bom) === 1 && $bom[0]['harga_id'] == 20, 'template target variation'); check($routes[0]['harga_id'] == 20 && $routes[0]['bom_line_id'] == $bom[0]['bom_line_id'], 'template static link');
check($routes[1]['bom_line_id'] === 'dyn_' . $dyn[0]['id'], 'template dynamic link'); check(count(rowsFor($c, 'bom_lines', 1)) === 1, 'source retained');
$c = fixture(); $c->db->tables['tbl_harga_product'][1]['ket_1'] = '3'; $before = $c->db->tables;
$r = runAction($c, 'bulk_apply_template', ['template_id' => 1, 'target_ids' => [2], 'options' => ['bom', 'routing']]);
check(!$r['success'] && $c->db->tables === $before, 'incompatible template rolls back');
$c = fixture(); $r = runAction($c, 'bulk_apply_template', ['template_id' => 1, 'target_ids' => [2], 'options' => ['bom']]);
check($r['success'] && rowsFor($c, 'routing', 2)[0]['bom_line_id'] === null, 'BOM-only detaches removed links');
$c = fixture(); $r = runAction($c, 'bulk_apply_template', ['template_id' => 1, 'target_ids' => [2], 'options' => ['routing']]);
check($r['success'] && rowsFor($c, 'routing', 2)[0]['bom_line_id'] === null, 'routing-only never links source BOM');
$c = fixture(); $r = runAction($c, 'copy_bom_routing_to_variations', ['product_id' => 1, 'source_harga_id' => 10, 'target_harga_ids' => [20]]);
check(!$r['success'] && !$c->db->open, 'foreign variation rejected');
$c = fixture(); $r = runAction($c, 'copy_bom_routing_to_variations', ['product_id' => 1, 'source_harga_id' => 11, 'target_harga_ids' => [10]]);
check(!$r['success'] && !$c->db->open, 'empty variation closes transaction');
$c = fixture(); $r = runAction($c, 'copy_bom_routing_to_variations', ['product_id' => 1, 'source_harga_id' => 10, 'target_harga_ids' => [11]]);
check($r['success'], 'variation setup copied'); $bom = rowsFor($c, 'bom_lines', 1); $route = rowsFor($c, 'routing', 1);
check($bom[1]['harga_id'] == 11 && $route[3]['bom_line_id'] == $bom[1]['bom_line_id'], 'copied variation BOM linked');
$c = fixture(); $r = runAction($c, 'update_bom', ['bom_line_id' => 30, 'qty' => 3, 'scrap_factor' => 0]); $bom = $c->db->tables['bom_lines'][0];
check($r['success'] && $bom['quantity'] === 3, 'quick edit saved'); check($bom['harga_id'] === 10, 'quick edit retains variation'); check($bom['panjang'] === 12 && $bom['lebar'] === 14, 'quick edit retains dimensions');
$c = fixture(); $r = runAction($c, 'update_bom', ['bom_line_id' => 30, 'qty' => -3, 'scrap_factor' => 0]); check(!$r['success'], 'negative quantity rejected');
$c = fixture(); $r = runAction($c, 'delete_bom', ['bom_id' => 30]); check($r['success'] && $c->db->tables['routing'][0]['bom_line_id'] === null, 'delete detaches BOM route');
$c = fixture(); $before = $c->db->tables; $c->db->failTable = 'bom_lines'; $r = runAction($c, 'delete_bom', ['bom_id' => 30]); check(!$r['success'] && $c->db->tables === $before, 'delete BOM rollback');
$c = fixture(); $c->db->tables['tbl_pemesanan_detail'][] = ['product_id' => 1]; $before = $c->db->tables; $r = runAction($c, 'delete_bulk_product', ['ids' => [1, 2]]);
check($r['status'] === 'error' && $c->db->tables === $before, 'used product blocks bulk deletion atomically');
$c = fixture(); $r = runAction($c, 'delete_bulk_product', ['ids' => [1]]); check($r['status'] === 'success', 'unused product deleted');
check(!rowsFor($c, 'tbl_bom_dynamic_mapping', 1), 'delete cleans dynamic mappings'); check(count($c->db->tables['tbl_product']) === 1, 'other product preserved');
$c = fixture(); $r = runAction($c, 'get_calculated_hpp_ajax', [], [1]); check(abs($r['hpp'] - 2415) < 0.001, 'HPP uses base purchase price divided by conversion plus scrap and labor');
$c = fixture(); $r = runAction($c, 'bulk_price_adjustment_ajax', ['adj_value' => 10, 'adj_method' => 'flat', 'targets' => ['product_id']]); check(!$r['success'] && !$c->db->open, 'invalid price column rejected');
$c = fixture(); $r = runAction($c, 'actionUpdateproduct', ['product_id' => 1, 'product_nama' => 'Edited', 'tipe_id' => '', 'metode_id' => 2]);
check(isset($c->session->flash['success']), 'product edit success'); check(!array_key_exists('bahan_id', $c->db->tables['tbl_product'][0]), 'edit uses existing schema'); check($c->db->tables['tbl_product'][0]['detail_id'] == 2 && $c->db->tables['tbl_product'][0]['metode_id'] == 2, 'method fields synchronized');
$c = fixture(); $r = runAction($c, 'action_update_routing', ['routing_id' => 60, 'routing_name' => 'Potong', 'routing_urutan' => 1, 'operator_id' => 2, 'is_finishing' => '1', 'is_active' => '1', 'bom_line_id' => 30]);
check($c->db->tables['routing'][0]['is_finishing'] === 1, 'finishing flag saved'); check($c->db->tables['routing'][0]['labor_cost'] === 100, 'legacy edit preserves omitted labor cost');
$c = fixture(); $r = runAction($c, 'save_dynamic_bom', ['product_id' => 1, 'bahan_id' => 50, 'attribute_key' => 'sisi', 'divider_factor' => 0]); check($r['status'] === 'error', 'zero divisor rejected');
$c = fixture(); $c->db->failTable = 'tbl_bom_dynamic_mapping'; $r = runAction($c, 'save_dynamic_bom', ['product_id' => 1, 'bahan_id' => 50, 'attribute_key' => 'sisi', 'attribute_value' => '1', 'calc_type' => 'fixed', 'divider_factor' => 1]); check($r['status'] === 'error', 'dynamic write failure reported');
$validDynamic = ['product_id' => 1, 'bahan_id' => 50, 'attribute_key' => 'sisi', 'attribute_value' => '1', 'calc_type' => 'fixed', 'divider_factor' => 1];
$c = fixture(); $r = runAction($c, 'save_dynamic_bom', $validDynamic); check($r['status'] === 'success', 'valid stock material mapping saved');
$c = fixture(); $before = $c->db->tables; $r = runAction($c, 'save_dynamic_bom', array_merge($validDynamic, ['bahan_id' => 500])); check($r['status'] === 'error' && $before === $c->db->tables, 'parent bahan ID rejected without data change');
$c = fixture(); $r = runAction($c, 'save_dynamic_bom', array_merge($validDynamic, ['calc_type' => 'invalid'])); check($r['status'] === 'error', 'unsupported dynamic calculation rejected');
$c = fixture(); $r = runAction($c, 'save_dynamic_bom', array_merge($validDynamic, ['product_id' => 999])); check($r['status'] === 'error', 'missing product rejected');
$c = fixture(); $r = runAction($c, 'save_dynamic_bom', array_merge($validDynamic, ['id' => 40, 'manual_cost' => '100'])); check($r['status'] === 'success' && $c->db->tables['tbl_bom_dynamic_mapping'][0]['manual_cost'] === 50, 'schema without manual_cost is handled without overwriting existing fixture value');
$c = fixture(); $r = runAction($c, 'save_dynamic_manual_cost', ['id' => 40, 'manual_cost' => '-1']); check($r['status'] === 'error', 'negative manual cost rejected');
$c = fixture(); $r = runAction($c, 'delete_dynamic_bom', ['id' => 40]); check($r['status'] === 'success' && $c->db->tables['routing'][1]['bom_line_id'] === null, 'delete dynamic detaches route');
$c = fixture(); $r = runAction($c, 'action_save_all', ['product_id' => 1, 'harga_id' => [10], 'detail_product' => ['Updated'], 'harga_1' => ['1.500']]);
check($r['success'] && $c->db->tables['tbl_harga_product'][0]['harga_1'] === 1500, 'matrix optional dimensions safe');
check(!$c->db->open && $c->db->commits === 1, 'matrix commits');
$c = fixture(); $before = $c->db->tables; $c->db->failTable = 'tbl_harga_product'; $r = runAction($c, 'action_save_all', ['product_id' => 1, 'harga_id' => [10], 'harga_1' => ['1.500']]);
check(!$r['success'] && $before === $c->db->tables && !$c->db->open, 'matrix failed save rolls back');
$c = fixture(); $r = runAction($c, 'actionUpdateproduct', ['product_id' => 1, 'product_nama' => 'Edited']);
check($c->db->tables['tbl_product'][0]['side_id'] === '2', 'identity edit preserves omitted side');
$c = fixture(); $before = $c->db->tables;
$r = runAction($c, 'action_save_all', ['product_id' => 1, 'harga_id' => [10, 20], 'harga_1' => ['1.500', '2.500']]);
check(!$r['success'] && $c->db->tables === $before && !$c->db->open, 'mixed foreign matrix row rolls back earlier writes');
$c = fixture(); $before = $c->db->tables; $c->db->throwTable = 'tbl_harga_product';
$r = runAction($c, 'action_save_all', ['product_id' => 1, 'harga_id' => [10], 'harga_1' => ['1.500']]);
check(!$r['success'] && $c->db->tables === $before && !$c->db->open, 'matrix Throwable rollback');
$c = fixture(); $r = runAction($c, 'action_save_all', ['product_id' => 1, 'harga_id' => [10], 'harga_1' => 'bad']);
check(!$r['success'] && !$c->db->open, 'scalar matrix rejected');
$c = fixture(); $r = runAction($c, 'update_bom_variation_inline', ['bom_line_id' => 30, 'harga_id' => 20]);
check(!$r['success'] && $c->db->tables['bom_lines'][0]['harga_id'] === 10, 'foreign inline variation rejected');
$c = fixture(); $c->db->failTable = 'bom_lines';
$r = runAction($c, 'update_bom_variation_inline', ['bom_line_id' => 30, 'harga_id' => 11]);
check(!$r['success'], 'inline variation database failure reported');
$c = fixture(); $c->db->failTable = 'bom_lines';
$r = runAction($c, 'update_bom_material_inline', ['bom_line_id' => 30, 'bahan_id' => 50]);
check(!$r['success'], 'inline material database failure reported');
$c = fixture(); $r = runAction($c, 'update_bom', ['bom_line_id' => 30, 'qty' => 3, 'scrap_factor' => 0, 'bahan_id' => 9999]);
check(!$r['success'] && $c->db->tables['bom_lines'][0]['quantity'] === 2, 'quick edit missing material rejected');
$c = fixture(); $r = runAction($c, 'update_bom', ['bom_line_id' => 30, 'qty' => 3, 'scrap_factor' => 0, 'harga_id' => 20]);
check(!$r['success'], 'quick edit foreign variation rejected');
echo "Manufaktur controller: $checks checks passed.\n";
