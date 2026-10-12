<?php
// Offline controller regression tests; no server or ERP database is contacted.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('BASEPATH', __DIR__);
error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});
function log_message($level, $message) {}
function show_404() { throw new RuntimeException('HTTP 404'); }
function show_error($message, $status) { throw new RuntimeException('HTTP ' . $status); }
function redirect($url) { throw new RuntimeException('Redirect'); }

class MY_Controller
{
    public $db, $load, $input, $output, $security, $session, $Produksi_model, $wip_workflow, $view;
    protected function load_admin_view($view, $data) { $this->view = $data; }
    protected function get_operator_id_for_current_user() { return null; }
}
require __DIR__ . '/../libraries/Production_workflow.php';
require __DIR__ . '/../controllers/Wip.php';

class ControllerInput
{
    public $posts = [], $gets = [], $verb = 'post';
    public function get($name, $clean = false) { return $this->gets[$name] ?? null; }
    public function post($name, $clean = false) { return $name === null ? $this->posts : ($this->posts[$name] ?? null); }
    public function method() { return $this->verb; }
}
class ControllerOutput
{
    public $body, $status = 200;
    public function set_content_type($type) { return $this; }
    public function set_output($body) { $this->body = json_decode($body, true); return $this; }
    public function set_status_header($status) { $this->status = $status; return $this; }
    public function set_header($header) { return $this; }
}
class ControllerSecurity { public function get_csrf_hash() { return 'test-token'; } }
class ControllerSession { public function userdata($name) { return ['level' => 1, 'perusahaan_id' => 1][$name] ?? null; } }
class ControllerRows
{
    private $rows;
    public function __construct($rows) { $this->rows = array_values($rows); }
    public function row() { return $this->rows[0] ?? null; }
    public function result() { return $this->rows; }
    public function result_array() { return array_map(function ($row) { return (array) $row; }, $this->rows); }
}
class ControllerDb
{
    public $items, $steps, $product = null, $machines = [], $queries = [];
    private $table, $conditions = [];
    public function select($fields) { $this->queries[] = $fields; return $this; }
    public function from($table) { $this->table = $table; return $this; }
    public function join($table, $condition, $type = '') { $this->queries[] = $condition; return $this; }
    public function where($key, $value = null, $escape = null) { $this->conditions[$key] = $value; return $this; }
    public function order_by($key, $direction = null) { return $this; }
    public function group_by($key) { return $this; }
    public function get($table = null) {
        $table = $table ?: $this->table;
        $rows = [];
        if ($table === 'tbl_produksi p' || $table === 'tbl_produksi') $rows = $this->items;
        if ($table === 'detail_produksi dp' || $table === 'detail_produksi') $rows = $this->steps;
        if ($table === 'bom_lines bl') $rows = [(object) ['material_id' => 5, 'qty_required' => 2, 'material_name' => 'Kertas']];
        if ($table === 'routing') $rows = [(object) ['routing_id' => 21, 'routing_name' => 'Cetak']];
        if ($table === 'tbl_product' && $this->product) $rows = [$this->product];
        if ($table === 'tbl_mesin') $rows = $this->machines;
        foreach ($this->conditions as $key => $value) {
            $field = substr($key, strrpos('.' . $key, '.'));
            if (in_array($field, ['produksi_id', 'detail_produksi_id', 'nospk', 'mesin_id', 'status', 'kategori'], true)) {
                $rows = array_filter($rows, function ($row) use ($field, $value) { return ($row->$field ?? null) == $value; });
            }
        }
        $this->table = null; $this->conditions = [];
        return new ControllerRows(unserialize(serialize($rows)));
    }
    public function get_where($table, $where) { $this->table = $table; $this->conditions = $where; return $this->get(); }
    public function query($sql, $binds = []) { $this->queries[] = $sql; return new ControllerRows([]); }
}
class ControllerProduction
{
    public $item;
    public function get_produksi_by_id($id) { return $this->item && $this->item->produksi_id == $id ? $this->item : null; }
    public function get_detail_produksi_by_id($id, $operator) { return []; }
}
class ControllerWorkflow
{
    public $deny = false, $executed = false, $selected_steps = [];
    public function steps($item) { return $this->selected_steps[$item->produksi_id] ?? []; }
    public function actor($pin = null, $role = null) { return ['admin' => true]; }
    public function assert_access($item, $actor) {
        if (!$item) throw new DomainException('Item not found');
        if ($this->deny) throw new DomainException('Access denied');
    }
    public function execute(...$args) { $this->executed = true; return ['success' => true]; }
}
class ControllerUnderTest extends Wip
{
    public function __construct() {
        $this->db = new ControllerDb(); $this->input = new ControllerInput();
        $this->output = new ControllerOutput(); $this->security = new ControllerSecurity();
        $this->session = new ControllerSession(); $this->Produksi_model = new ControllerProduction();
        $this->wip_workflow = new ControllerWorkflow();
        $this->db->items = [];
        for ($id = 1; $id <= 2; $id++) {
            $this->db->items[] = (object) ['produksi_id' => $id, 'nospk' => 'TEST-1', 'product_id' => $id,
                'harga_id' => 1, 'kategori_id' => 1, 'sisi' => 1, 'jenisprint_id' => 1, 'perusahaan_id' => 1, 'status_produksi' => 'proses'];
            for ($order = 1; $order <= 2; $order++) {
                $this->db->steps[] = (object) ['produksi_id' => $id, 'detail_produksi_id' => $id * 10 + $order,
                    'routing_id' => 21, 'routing_urutan' => $order, 'routing_name' => 'Cetak', 'status_produksi' => '1'];
            }
        }
        $this->Produksi_model->item = $this->db->items[0];
    }
    protected function get_operator_id_for_current_user() { return null; }
}
class VariantControllerDb extends ControllerDb {
    public function get_where($table, $where) {
        if ($table === 'tbl_bom_dynamic_mapping') return new ControllerRows([(object)['id' => 1, 'product_id' => 1]]);
        if ($table === 'tbl_material') return new ControllerRows([(object)['material_id' => $where['material_id'], 'bahan_id' => 220]]);
        return parent::get_where($table, $where);
    }
}
$checks = 0;
function verify($condition, $label) { global $checks; if (!$condition) throw new RuntimeException($label); $checks++; }
function private_call($controller, $method, $args = []) {
    $reflection = new ReflectionMethod(Wip::class, $method);
    $reflection->setAccessible(true); return $reflection->invokeArgs($controller, $args);
}
function denied($work, $message) {
    try { $work(); } catch (Exception $e) { verify($e->getMessage() === $message, $message); return; }
    throw new RuntimeException('Expected ' . $message);
}

$c = new ControllerUnderTest(); $variantDb = new VariantControllerDb(); $variantDb->items = $c->db->items; $variantDb->steps = $c->db->steps; $c->db = $variantDb;
$c->load = new class { public function library($name) {} };
$c->production_bom = new class { public function resolve($item) { return [(object)['is_dynamic' => true, 'bom_material_id' => 361, 'bahan_id' => 220]]; } };
denied(function () use ($c) { private_call($c, '_workflow_post_material', [$c->db->items[0], $c->db->steps[0], 358, 1, ['employee_id' => 1]]); }, 'Warna atau ukuran material tidak sesuai BOM variasi pesanan.');
$c = new ControllerUnderTest(); $c->input->posts['produksi_id'] = '1'; $c->get_detail_produksi();
verify($c->view['produksi']->produksi_id === 1, 'POST detail loads header before access check');
$c = new ControllerUnderTest(); $c->wip_workflow->deny = true;
denied(function () use ($c) { $c->detail(1); }, 'HTTP 403');
verify($c->view === null, 'Denied item never renders');
foreach ([null, [], '1 OR 1', 0, 999] as $id) {
    $c = new ControllerUnderTest(); denied(function () use ($c, $id) { $c->detail($id); }, 'HTTP 404');
}

$c = new ControllerUnderTest(); $c->control_center(1);
verify(array_column($c->view['items'], 'produksi_id') === [1, 2], 'Control center preserves both distinct items');
foreach ($c->view['items'] as $item) {
    verify(count($item->routing_steps) === 2, 'Control center preserves every routing step');
    verify(array_column($item->routing_steps, 'detail_produksi_id') === [$item->produksi_id * 10 + 1, $item->produksi_id * 10 + 2], 'Routing steps belong to their original item');
}

$c = new ControllerUnderTest(); $c->input->posts['detail_produksi_id'] = 11;
ob_start(); $c->get_bom_data(); $response = json_decode(ob_get_clean(), true);
verify($response['success'] && $response['data']['material_id'] === 5 && $response['data']['qty_required'] === 2, 'BOM response retains frontend fields');
$c = new ControllerUnderTest(); $c->input->posts['detail_produksi_id'] = 11;
ob_start(); $c->get_routing_data(); $response = json_decode(ob_get_clean(), true);
verify($response['success'] && $response['data']['mesin_id'] === null, 'Routing without machine column returns no default without warnings');

foreach (['not-json', 'null', '42', ['bad'], [['bom_usage' => 'bad']], [['bom_usage' => ['bad']]]] as $invalid) {
    $c = new ControllerUnderTest(); $c->input->posts['outcomes'] = $invalid;
    denied(function () use ($c) {
        try { private_call($c, '_workflow_execute', [[1], 'complete']); }
        catch (DomainException $e) { throw new RuntimeException('Invalid payload'); }
    }, 'Invalid payload');
    verify(!$c->wip_workflow->executed, 'Malformed payload rejected before transaction');
}
$c = new ControllerUnderTest(); $c->input->posts['outcomes'] = '{"1":{"bom_usage":[{"bom_material_id":5,"qty":1}]}}';
verify(private_call($c, '_workflow_execute', [[1], 'complete'])['success'], 'Valid JSON outcome accepted');
private_call($c, '_workflow_response', [function () { throw new TypeError('Test runtime error'); }]);
verify(!$c->output->body['success'] && $c->output->body['csrf_hash'] === 'test-token', 'Runtime error returns valid JSON and CSRF token');
$c->input->verb = 'get';
private_call($c, '_workflow_response', [function () { throw new RuntimeException('Must not execute'); }]);
verify($c->output->status === 405, 'GET cannot execute production write');
$c = new ControllerUnderTest();
$item = (object) ['product_id' => 1, 'perusahaan_id' => 1];
foreach ([(object) ['is_finishing' => 1, 'routing_name' => 'Tahap akhir'],
          (object) ['is_finishing' => 0, 'routing_name' => 'F.Laminating'],
          (object) ['is_finishing' => 0, 'routing_name' => 'Finishing Pin']] as $step) {
    $step->mesin_id = 999; $step->bom_line_id = 5;
    $fields = private_call($c, '_workflow_fields', [$item, $step, ['qty_output' => 1, 'mesin_id' => 123]]);
    verify(!array_key_exists('mesin_id', $fields), 'Finishing ignores submitted machine and historical machine fallback');
}
verify(!$c->db->queries, 'Finishing result does not query or validate machines');
$c->db->product = (object) ['product_id' => 1, 'kategori_id' => 1, 'tipe_id' => 1];
denied(function () use ($c, $item) {
    private_call($c, '_workflow_fields', [$item, (object) ['is_finishing' => 0, 'routing_name' => 'Cetak', 'bom_line_id' => 5], ['qty_output' => 1]]);
}, 'Pilih mesin aktual untuk tahap cetak ini.');
$cutting = (object) ['routing_name' => 'F.Cutting', 'is_finishing' => 0];
$c->db->product = (object) ['product_id' => 1, 'kategori_id' => 2, 'product_nama' => 'Cutting Meteran'];
denied(function () use ($c, $item, $cutting) {
    private_call($c, '_workflow_fields', [$item, $cutting, ['qty_output' => 1, 'mesin_id' => 0]]);
}, 'Pilih mesin aktual untuk tahap cutting/potong ini.');
$c->db->machines = [(object) ['mesin_id' => 6, 'mesin_nama' => 'Mesin Potong', 'perusahaan_id' => 1, 'status' => '1', 'kategori' => 'Finishing']];
$fields = private_call($c, '_workflow_fields', [$item, $cutting, ['qty_output' => 1, 'mesin_id' => '6']]);
verify($fields['mesin_id'] === 6, 'Cutting saves active machine on production location');
$c->db->machines[0]->perusahaan_id = 2;
denied(function () use ($c, $item, $cutting) {
    private_call($c, '_workflow_fields', [$item, $cutting, ['qty_output' => 1, 'mesin_id' => '6']]);
}, 'Mesin yang dipilih tidak sesuai lokasi produksi item. Buka ulang form dan pilih mesin yang tersedia untuk lokasi item.');
$c->db->machines[0]->perusahaan_id = 1; $c->db->machines[0]->status = '0';
denied(function () use ($c, $item, $cutting) {
    private_call($c, '_workflow_fields', [$item, $cutting, ['qty_output' => 1, 'mesin_id' => '6']]);
}, 'Mesin tidak aktif atau tidak ditemukan.');
$c->db->machines[0]->status = '1';
$c->input->gets = ['ids' => ['1'], 'stepName' => 'Finishing', 'catId' => '10'];
$cutting->status_produksi = '1'; $c->wip_workflow->selected_steps[1] = [$cutting];
ob_start(); $c->get_machines_ajax(); $options = json_decode(ob_get_clean(), true);
verify($options['is_mandatory'] && count($options['machines']) === 1, 'Machine API derives cutting requirement from actual routing, including category 10');
$c->wip_workflow->selected_steps[1][0] = (object) ['routing_name' => 'F.Laminating', 'status_produksi' => '1'];
ob_start(); $c->get_machines_ajax(); $options = json_decode(ob_get_clean(), true);
verify(!$options['is_mandatory'], 'Machine API does not require machines for ordinary finishing');
foreach (['Cutting Kiss Cut A3 (Standar)', 'Cutting Die Cut A3', 'Ongkos Potong A3'] as $name) {
    $c->db->product = (object) ['product_id' => 1, 'kategori_id' => 16, 'product_nama' => $name];
    $fields = private_call($c, '_workflow_fields', [$item, $cutting, ['qty_output' => 1, 'mesin_id' => 999]]);
    verify(!isset($fields['mesin_id']), 'A3 cutting/potong accepts results without a machine and ignores stale machine');
    $c->wip_workflow->selected_steps[1] = [$cutting];
    ob_start(); $c->get_machines_ajax(); $options = json_decode(ob_get_clean(), true);
    verify(!$options['is_mandatory'], 'Machine picker derives A3 exception from product master, even with misleading category parameter');
}
$c->db->product = (object) ['product_id' => 1, 'kategori_id' => 1, 'product_nama' => 'Brosur'];
$fields = private_call($c, '_workflow_fields', [$item, $cutting, ['qty_output' => 1]]);
verify(!isset($fields['mesin_id']), 'Digital A3 category supports finishing without machine even when route name omits A3');
echo "PASS: $checks offline WIP controller regression checks; no ERP database connection.\n";
