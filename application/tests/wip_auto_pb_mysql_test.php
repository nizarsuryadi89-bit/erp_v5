<?php
// Real workflow and PB list queries against empty schema copies in a disposable database.
// Never writes production rows, stock, or schema in the ERP database.
if (PHP_SAPI !== 'cli' || !in_array('--isolated-db', $argv, true)) exit("Run with --isolated-db.\n");
define('BASEPATH', dirname(__DIR__, 2) . '/system/');
define('APPPATH', dirname(__DIR__) . '/');
define('ENVIRONMENT', 'development');
$_SERVER['HTTP_HOST'] = 'localhost';
class CI_Model { public $db, $load, $session, $wip_quality; public function __construct() { $this->load = new AutoPbLoader(); $this->session = new AutoPbSession(); } }
function get_instance() { global $active; return $active; }
function config_item($key) { return null; }
function log_message($level, $message) {}
require BASEPATH . 'core/Common.php';
require BASEPATH . 'database/DB.php';
require APPPATH . 'config/database.php';
require APPPATH . 'libraries/Production_workflow.php';
require APPPATH . 'models/Wip_workflow_model.php';
require APPPATH . 'models/Produksi_model.php';
require APPPATH . 'models/M_gudang.php';
class AutoPbLoader { public function library($name) {} public function model($name, $alias = null) {} }
class AutoPbSession { public function userdata($key) { return $key === 'id' ? 9 : null; } }
class AutoPbQuality { public function assert_storage() {} public function enroll($item, $actor) {} public function assert_handover($item) {} }
$checks = 0;
function verify_pb($ok, $name) { global $checks; if (!$ok) throw new RuntimeException($name); $checks++; }
$config = $db['default']; $test_name = 'tgm_auto_pb_test_' . bin2hex(random_bytes(6));
mysqli_report(MYSQLI_REPORT_OFF);
$admin = new mysqli($config['hostname'], $config['username'], $config['password'], '', $config['port'] ?? 3306, '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock');
if ($admin->connect_errno) throw new RuntimeException('Test server unavailable.');
$created = false; $sql = null;
try {
    if (!$admin->query('CREATE DATABASE `' . $test_name . '`')) throw new RuntimeException('Cannot create test DB.');
    $created = true;
    foreach (['tbl_produksi','detail_produksi','routing','tbl_produksi_log','tbl_material','tbl_stok_gudang','tbl_stok_log','tbl_pemesanan','tbl_product','tbl_satuan','tbl_konsumen','tbl_jenis_transaksi','tbl_marketplace','tbl_piutang','tbl_perusahaan'] as $table) {
        if (!$admin->query('CREATE TABLE `' . $test_name . '`.`' . $table . '` LIKE `' . $admin->real_escape_string($config['database']) . '`.`' . $table . '`')) throw new RuntimeException('Schema copy failed: ' . $table);
    }
    $config['database'] = $test_name; $config['hostname'] = '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock'; $config['db_debug'] = false;
    $sql = DB($config, true);
    $sql->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
    $insert = function ($table, $row) use ($sql) {
        foreach ($sql->query('SHOW COLUMNS FROM `' . $table . '`')->result_array() as $column) {
            $field = $column['Field'];
            if (!array_key_exists($field, $row) && $column['Null'] === 'NO' && $column['Default'] === null && strpos($column['Extra'], 'auto_increment') === false) {
                if (preg_match('/^enum\(\x27([^\x27]+)\x27/', $column['Type'], $match)) $row[$field] = $match[1];
                elseif (preg_match('/int|decimal|float|double/', $column['Type'])) $row[$field] = 0;
                elseif ($column['Type'] === 'date') $row[$field] = '2026-10-12';
                elseif (preg_match('/^(datetime|timestamp)/', $column['Type'])) $row[$field] = '2026-10-12 08:00:00';
                elseif (strpos($column['Type'], 'time') === 0) $row[$field] = '08:00:00';
                else $row[$field] = '';
            }
        }
        if (!$sql->insert($table, $row)) throw new RuntimeException('Fixture failed: ' . $table . ': ' . $sql->error()['message']);
    };
    $insert('tbl_product', ['product_id'=>10, 'product_nama'=>'Finishing A3 test', 'satuan_id'=>1]);
    $insert('tbl_satuan', ['satuan_id'=>1, 'satuan_nama'=>'Pcs']);
    $insert('routing', ['routing_id'=>21, 'product_id'=>10, 'harga_id'=>null, 'routing_urutan'=>1, 'operator_id'=>7, 'routing_name'=>'F.Laminating', 'is_finishing'=>1]);
    $insert('tbl_pemesanan', ['nospk'=>'AUTO-PB-1', 'status_pembayaran'=>'paid']);
    $insert('tbl_material', ['material_id'=>5, 'bahan_id'=>1, 'barcode'=>'PB-TEST-5', 'konversi'=>1]);
    $insert('tbl_stok_gudang', ['material_id'=>5, 'gudang_id'=>1, 'rak_id'=>null, 'qty'=>100]);
    foreach ([[1,'proses',10,'1'],[2,'proses',99,'1'],[3,'done',3,'2'],[4,'done',44,'0'],[5,'done',55,'4'],[6,'done',66,'2']] as $row) {
        $insert('tbl_produksi', ['produksi_id'=>$row[0], 'product_id'=>10, 'harga_id'=>0, 'nospk'=>'AUTO-PB-1', 'qty'=>$row[2], 'perusahaan_id'=>1, 'lokasi_cetak'=>1, 'status_produksi'=>$row[1], 'finishing_id'=>$row[0]===6 ? 99 : 0, 'status_finishing'=>$row[0]===6 ? 'pending' : 'done']);
        $insert('detail_produksi', ['detail_produksi_id'=>10+$row[0], 'produksi_id'=>$row[0], 'routing_id'=>21, 'routing_urutan'=>1, 'status_produksi'=>$row[3], 'waktu_mulai'=>'2026-10-12 08:00:00', 'qty_output'=>$row[3]==='2' ? $row[2] : 0]);
    }
    $active = new Wip_workflow_model(); $active->db = $sql; $active->wip_quality = new AutoPbQuality();
    $stock = new M_gudang(); $stock->db = $sql;
    $actor = ['user_id'=>9, 'employee_id'=>70, 'branch'=>1, 'admin'=>false, 'operator_role'=>7];
    $result = $active->execute([1], 'complete', $actor, [1=>11], [], function () use ($stock) {
        if (!$stock->update_stock_with_log(5,1,-10,'Produksi',1,'Auto PB test',null,70)) throw new RuntimeException('Stock posting failed.');
        return ['qty_output'=>10];
    });
    verify_pb($result['success'] && $result['auto_pb_ids']===[1], 'Real completion enters PB: ' . ($result['message'] ?? ''));
    $header = $sql->get_where('tbl_produksi',['produksi_id'=>1])->row();
    verify_pb($header->status_produksi==='on_pb' && $header->tgl_selesai===$header->tgl_kirim_pb && !empty($header->tgl_selesai), 'PB timestamp saved with completion');
    verify_pb($sql->get_where('detail_produksi',['detail_produksi_id'=>11])->row()->status_produksi==='2', 'Numeric operation evidence preserved');
    verify_pb((float)$sql->get('tbl_stok_gudang')->row()->qty===90.0 && $sql->count_all_results('tbl_stok_log')===1, 'One stock deduction on completion');
    $retry = $active->execute([1], 'complete', $actor, [1=>11], [], function () { throw new RuntimeException('Retry callback must not run'); });
    verify_pb(!$retry['success'] && $sql->count_all_results('tbl_stok_log')===1 && $sql->count_all_results('tbl_produksi_log')===1, 'Completion retry never deducts stock again');
    $model = new Produksi_model(); $model->db = $sql; $model->load = new AutoPbLoader();
    verify_pb((int)$model->get_summary()->done_today===1, 'Dashboard still counts finished SPK after automatic PB arrival');
    foreach (['get_pb_list','get_produksi_pb'] as $method) {
        $ids = array_map('intval', array_column($model->$method(),'produksi_id')); sort($ids);
        verify_pb($ids===[1,3], $method . ' includes PB and valid historical done only');
    }
    $pb = $model->get_detail_pb();
    verify_pb(count($pb)===1 && (int)$pb[0]->total_items===2, 'One SPK has two finished items in PB');
    verify_pb((float)$pb[0]->total_qty===13.0 && (float)$pb[0]->total_qty_output===13.0, 'PB quantity excludes unfinished and corrupt historical siblings');
    $breakdown = $model->get_qty_breakdown('AUTO-PB-1');
    verify_pb(count($breakdown)===1 && (float)$breakdown[0]->total_ready===13.0 && (float)$breakdown[0]->total_qty===13.0, 'Single breakdown counts only finished material');
    $batched = $model->get_qty_breakdown_batched(['AUTO-PB-1']);
    verify_pb(count($batched['AUTO-PB-1'])===1 && (float)$batched['AUTO-PB-1'][0]->total_ready===13.0, 'Batched breakdown excludes open jobs');
    $sql->trans_begin(); $items = $active->customer_handover_items([1,3],$actor,'AUTO-PB-1'); $sql->trans_rollback();
    verify_pb(count($items)===2, 'New and historical completed items need no extra PB action');
    // Inject final audit failure: stock, operation, and automatic PB status all roll back together.
    if (!$sql->query("CREATE TRIGGER force_pb_audit_failure BEFORE INSERT ON tbl_produksi_log FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Injected PB audit failure'")) throw new RuntimeException('Trigger setup failed.');
    $result = $active->execute([2], 'complete', $actor, [2=>12], [], function () use ($stock) {
        if (!$stock->update_stock_with_log(5,1,-99,'Produksi',2,'Rollback test',null,70)) throw new RuntimeException('Stock posting failed.');
        return ['qty_output'=>99];
    });
    verify_pb(!$result['success'], 'Failed audit aborts automatic PB arrival');
    verify_pb($sql->get_where('tbl_produksi',['produksi_id'=>2])->row()->status_produksi==='proses' && $sql->get_where('detail_produksi',['detail_produksi_id'=>12])->row()->status_produksi==='1', 'Failure restores production and operation states');
    verify_pb((float)$sql->get('tbl_stok_gudang')->row()->qty===90.0 && $sql->count_all_results('tbl_stok_log')===1, 'Failure restores stock and logs');
    echo "PASS: $checks real SQL automatic PB checks; ERP data unchanged.\n";
} finally {
    if ($sql) $sql->close();
    if ($created && !$admin->query('DROP DATABASE `' . $test_name . '`')) throw new RuntimeException('Disposable DB cleanup failed.');
    $admin->close();
}
