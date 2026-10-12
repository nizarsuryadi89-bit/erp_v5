<?php
// Copies table structures to a disposable database; never writes ERP records.
if (PHP_SAPI !== 'cli' || !in_array('--isolated-db', $argv, true)) exit("Run with --isolated-db to use a disposable database.\n");
define('BASEPATH', dirname(__DIR__, 2) . '/system/');
ob_start(); require __DIR__ . '/warehouse_controller_test.php'; ob_end_clean();
define('APPPATH', dirname(__DIR__) . '/'); define('ENVIRONMENT', 'development');
function &get_instance() { global $active; return $active; }
function config_item($key) { return null; }
require dirname(__DIR__, 2) . '/system/core/Common.php';
require dirname(__DIR__, 2) . '/system/database/DB.php';
require dirname(__DIR__, 2) . '/system/database/DB_forge.php';
require dirname(__DIR__, 2) . '/system/database/drivers/mysqli/mysqli_forge.php';
class WSqlLoad { public function dbforge() {} }
$_SERVER['HTTP_HOST'] = 'localhost'; require __DIR__ . '/../config/database.php';
$config = $db['default'];
$name = 'tgm_warehouse_test_' . bin2hex(random_bytes(6));
mysqli_report(MYSQLI_REPORT_OFF);
$m = new mysqli($config['hostname'], $config['username'], $config['password'], '', $config['port'] ?? 3306, '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock');
if ($m->connect_errno) throw new RuntimeException('Cannot connect to test server.');
$created = false; $sql = null;
try {
    if (!$m->query('CREATE DATABASE `' . $name . '`')) throw new RuntimeException('Cannot create disposable DB.');
    $created = true; $seed = fixture();
    $tables = array_unique(array_merge(array_keys($seed->db->tables), ['tbl_jabatan','tbl_divisi','tbl_perusahaan','users','tbl_satuan']));
    foreach ($tables as $table) {
        if (!$m->query('CREATE TABLE `' . $name . '`.`' . $table . '` LIKE `' . $m->real_escape_string($config['database']) . '`.`' . $table . '`')) throw new RuntimeException('Missing schema: ' . $table);
    }
    $config['database'] = $name; $config['hostname'] = '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock'; $config['db_debug'] = false;
    $sql = DB($config, true);
    $sql->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION'");
    // Complete only fixture fields required by the real schema; no ERP row contents copied.
    $insert = function ($table, $row) use (&$sql) {
        $allowed = array_column($sql->query('SHOW COLUMNS FROM `' . $table . '`')->result_array(), 'Field');
        $row = array_intersect_key($row, array_flip($allowed));
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
    foreach ($seed->db->tables as $table => $rows) foreach ($rows as $row) {
        if ($table === 'tbl_material') { $row['barcode'] = 'TEST-' . $row['material_id']; $row['status'] = '1'; }
        if ($table === 'tbl_bahan') $row['barcode'] = 'BAHAN-' . $row['bahan_id'];
        if ($table === 'tbl_rak') $row['rak_kode'] = 'RAK-' . $row['rak_id'];
        $insert($table, $row);
    }
    $active = fixture(); $active->db = $sql;
    $GLOBALS['warehouse_sql_debug'] = getenv('WAREHOUSE_SQL_DEBUG') === '1';
    $active->warehouse_stock = new Warehouse_stock(['db' => $sql, 'session' => $active->session]);
    $active->warehouse = new Warehouse_model; $active->warehouse->db = $sql; $active->warehouse->session = $active->session;
    $active->warehouse->load = new WSqlLoad; $active->warehouse->dbforge = new CI_DB_mysqli_forge($sql);
    $checks = 0;
    $active->warehouse->ensure_integrity_schema();
    check($sql->field_exists('user_id', 'tbl_temp_beli') && $sql->field_exists('perusahaan_id', 'tbl_temp_pengambilan'), 'SQL scoped cart migration');
    $types = []; foreach ($sql->field_data('tbl_stok_opname') as $f) $types[$f->name] = $f->type;
    check(stripos($types['son_real'], 'decimal') !== false, 'SQL fractional opname migration');
    check($active->warehouse->get_current_stock(1, 1) == 700, 'SQL multi rack stock');
    $r = action($active, 'ajax_update_material_rack', ['material_id' => 1, 'gudang_id' => 1, 'rak_asal' => 11, 'rak_tujuan' => 12]);
    check($r['status'] === 'success', 'SQL rack move: ' . ($r['message'] ?? ''));
    check($active->warehouse_stock->total(1, 1) == 700 && $sql->get_where('tbl_stok_gudang', ['rak_id' => 12])->row()->qty == 200, 'SQL rack conservation');
    $r = action($active, 'ajax_dispatch_add_item', ['material_id' => 1, 'qty' => 1.2]); check($r['status'] === 'success', 'SQL fractional dispatch cart');
    $other = cart_item('dispatch', 1, 8); $other['temp_id'] = 999; $insert('tbl_temp_pengambilan', $other);
    action($active, 'dispatch_finalize', ['gudang_id' => 2, 'pengambilan_tgl' => '2026-10-12']);
    check(isset($active->session->flash['last_dispatch_id']), 'SQL dispatch: ' . ($active->session->flash['error'] ?? ''));
    check($active->warehouse_stock->total(1, 1) == 100 && $active->warehouse_stock->total(1, 2) == 600, 'SQL balanced dispatch');
    check($sql->get('tbl_pengambilan_detail')->row()->qty == 1.2 && $sql->get('tbl_temp_pengambilan')->row()->user_id == 8, 'SQL detail precision and other cart preserved');
    action($active, 'purchase_add_item', ['material_id' => 1, 'rak_id' => 11, 'qty' => 0.25, 'harga' => 2000]); check(isset($active->session->flash['success']), 'SQL fractional purchase draft');
    action($active, 'purchase_finalize', purchase_post('Transfer'));
    check(isset($active->session->flash['success']), 'SQL transfer purchase: ' . ($active->session->flash['error'] ?? ''));
    $purchase = $sql->get('tbl_pembelian')->row(); $pid = $purchase->pembelian_id;
    check($purchase->pembelian_total == 500 && $purchase->metode_bayar === 'cash', 'SQL supported payment header enum and trusted total');
    $expense = $sql->get('tbl_pengeluaran')->row(); check($expense->metode_bayar === 'transfer' && $expense->pengeluaran_jumlah == 500, 'SQL expense records transfer');
    $post = array_merge(purchase_post('Transfer'), ['material_id' => [1], 'qty' => [0.25], 'harga' => ['3.000'], 'rak_id' => [11]]);
    action($active, 'purchase_update', $post, [$pid]);
    check(isset($active->session->flash['success']) && $sql->get('tbl_pembelian')->row()->pembelian_total == 750, 'SQL purchase edit: ' . ($active->session->flash['error'] ?? ''));
    check($sql->count_all_results('tbl_pengeluaran') === 1 && $sql->get('tbl_pengeluaran')->row()->pengeluaran_jumlah == 750, 'SQL expense update targets actual id column');
    action($active, 'deletePembelian', [], [$pid]);
    check(isset($active->session->flash['success']) && $sql->count_all_results('tbl_pembelian') === 0 && $active->warehouse_stock->total(1, 1) == 100, 'SQL purchase cancellation balance');
    action($active, 'purchase_add_item', ['material_id' => 1, 'rak_id' => 11, 'qty' => 1, 'harga' => 1000]);
    action($active, 'purchase_finalize', purchase_post('Hutang'));
    check(isset($active->session->flash['success']) && $sql->get('tbl_hutang')->row()->status === 'BELUM LUNAS', 'SQL actual debt enum: ' . ($active->session->flash['error'] ?? ''));
    $pid = $sql->get('tbl_pembelian')->row()->pembelian_id;
    $sql->update('tbl_hutang', ['total_bayar' => 100], ['pembelian_id' => $pid]);
    action($active, 'deletePembelian', [], [$pid]); check(isset($active->session->flash['error']) && $sql->count_all_results('tbl_pembelian') === 1, 'SQL paid debt preserved');
    action($active, 'opname', ['material_id' => 1, 'gudang_id' => 1, 'son_real' => 0.9, 'son_aplikasi' => 1.2, 'sync_now' => '1']);
    check(isset($active->session->flash['success']) && $active->warehouse_stock->total(1, 1) == 450 && $sql->get('tbl_stok_opname')->row()->son_real == 0.9, 'SQL fractional opname: ' . ($active->session->flash['error'] ?? ''));
    // Failure after source deduction must roll back balances, header/detail, and cart.
    $r = action($active, 'ajax_dispatch_add_item', ['material_id' => 1, 'qty' => 0.5]); check($r['status'] === 'success', 'SQL failure fixture picklist');
    $before_stock = $sql->order_by('stok_id')->get('tbl_stok_gudang')->result_array();
    $before_dispatch = $sql->count_all_results('tbl_pengambilan');
    if (!$sql->query("CREATE TRIGGER force_destination_failure BEFORE INSERT ON tbl_stok_log FOR EACH ROW BEGIN IF NEW.gudang_id = 2 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Injected destination failure'; END IF; END")) throw new RuntimeException('Trigger fixture failed.');
    action($active, 'dispatch_finalize', ['gudang_id' => 2, 'pengambilan_tgl' => '2026-10-12']);
    check(isset($active->session->flash['error']) && $before_stock === $sql->order_by('stok_id')->get('tbl_stok_gudang')->result_array() && $before_dispatch === $sql->count_all_results('tbl_pengambilan'), 'SQL failed destination rollback');
    check($sql->get_where('tbl_temp_pengambilan', ['user_id' => 7])->row() !== null, 'SQL failed dispatch retains picklist');
    $sql->query('DROP TRIGGER force_destination_failure');
    $sql->close(); $sql = DB($config, true); $active->db = $sql; $active->warehouse->db = $sql;
    $active->warehouse_stock = new Warehouse_stock(['db' => $sql, 'session' => $active->session]);
    $sql->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION'");
    $r = action($active, 'actionDeleteMaterial', ['material_id' => 1]); check($r['status'] === 'error' && $sql->count_all_results('tbl_stok_log') > 0, 'SQL used material history preserved');
    $r = action($active, 'actionDeleteMaterial', ['material_id' => 2]); check($r['status'] === 'success', 'SQL unused material deletion');
    // Stock report compilation against real schema (includes SQL subqueries and GROUP_CONCAT).
    $active->warehouse->get_stock_by_warehouse(1); check($sql->error()['code'] === 0, 'SQL warehouse report query');
    check(abs($active->warehouse->get_total_inventory_value() - 2100) < 0.01, 'SQL valuation converts base qty to purchase units');
    $active->warehouse->get_inventory_valuation(); check($sql->error()['code'] === 0, 'SQL valuation report query');
    $legacy = cart_item('purchase', 0.5); $legacy['temp_id'] = 1001; $legacy['user_id'] = null; $legacy['perusahaan_id'] = null;
    $insert('tbl_temp_beli', $legacy);
    check(count($active->warehouse->get_unowned_drafts('purchase')) === 1, 'SQL legacy draft remains reviewable');
    action($active, 'claim_legacy_draft', ['kind' => 'purchase', 'temp_id' => 1001]);
    check(isset($active->session->flash['success']) && $sql->get_where('tbl_temp_beli', ['temp_id' => 1001])->row()->user_id == 7, 'SQL legacy draft claimed without losing data');
    $sql->update('tbl_material', ['stok_min' => 1], ['material_id' => 1]);
    check(count($active->warehouse->get_low_stock_materials(1)) === 1, 'SQL low stock groups whole material across racks');
    $summary = $active->warehouse->get_stock_summary_by_warehouse();
    check(count($summary) === 2 && $summary[0]->total_materials == 1 && $summary[0]->low_stock_count == 1, 'SQL dashboard counts material once and applies unit conversion');
    echo "Warehouse MySQL strict: $checks checks passed.\n";
} finally {
    if ($sql) $sql->close();
    if ($created && !$m->query('DROP DATABASE `' . $name . '`')) throw new RuntimeException('Disposable DB cleanup failed.');
    $m->close();
}
