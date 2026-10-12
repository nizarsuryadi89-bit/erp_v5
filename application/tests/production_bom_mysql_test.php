<?php
// Real SQL and stock postings in a disposable DB. Never changes ERP records/balances.
if (PHP_SAPI !== 'cli' || !in_array('--isolated-db', $argv, true)) exit("Run with --isolated-db.\n");
define('BASEPATH', dirname(__DIR__, 2) . '/system/'); define('APPPATH', dirname(__DIR__) . '/'); define('ENVIRONMENT', 'development');
$_SERVER['HTTP_HOST'] = 'localhost';
class CI_Model { public $db, $load, $session, $production_bom, $M_gudang; }
function get_instance() { global $active; return $active; }
function config_item($k) { return null; }
function log_message($level, $message) {}
require BASEPATH . 'core/Common.php'; require BASEPATH . 'database/DB.php';
require APPPATH . 'config/database.php';
require APPPATH . 'libraries/Production_bom.php'; require APPPATH . 'models/M_gudang.php'; require APPPATH . 'models/M_pembayaran.php';
class BomSession { public function userdata($key) { return $key === 'id' ? 1 : null; } }
class BomLoad { public function model($name) {} public function library($name) {} }
$checks = 0;
function check($ok, $label) { global $checks; $checks++; if (!$ok) throw new RuntimeException($label); }
function reject($fn, $label) { try { $fn(); } catch (DomainException $e) { check(true, $label); return; } check(false, $label); }
$config = $db['default']; $name = 'tgm_bom_test_' . bin2hex(random_bytes(6));
mysqli_report(MYSQLI_REPORT_OFF);
$m = new mysqli($config['hostname'], $config['username'], $config['password'], '', $config['port'] ?? 3306, '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock');
if ($m->connect_errno) throw new RuntimeException('Test server not available.');
$created = false; $sql = null;
try {
    if (!$m->query('CREATE DATABASE `' . $name . '`')) throw new RuntimeException('Cannot create test DB.');
    $created = true;
    foreach (['tbl_product', 'tbl_harga_product', 'bom_lines', 'tbl_bom_dynamic_mapping', 'tbl_material', 'tbl_bahan', 'tbl_satuan', 'tbl_produksi', 'detail_produksi', 'tbl_pembayaran_produk', 'tbl_stok_log', 'tbl_stok_gudang'] as $table) {
        if (!$m->query('CREATE TABLE `' . $name . '`.`' . $table . '` LIKE `' . $m->real_escape_string($config['database']) . '`.`' . $table . '`')) throw new RuntimeException('Missing schema: ' . $table);
    }
    $config['database'] = $name; $config['hostname'] = '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock'; $config['db_debug'] = false;
    $sql = DB($config, true); $sql->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
    $insert = function ($table, $row) use (&$sql) {
        foreach ($sql->query('SHOW COLUMNS FROM `' . $table . '`')->result_array() as $c) {
            $f = $c['Field'];
            if (!array_key_exists($f, $row) && $c['Null'] === 'NO' && $c['Default'] === null && strpos($c['Extra'], 'auto_increment') === false) {
                if (preg_match('/^enum\(\x27([^\x27]+)\x27/', $c['Type'], $match)) $row[$f] = $match[1];
                elseif (preg_match('/int|decimal|float|double/', $c['Type'])) $row[$f] = 0;
                elseif ($c['Type'] === 'date') $row[$f] = '2026-10-12';
                elseif (strpos($c['Type'], 'datetime') === 0 || strpos($c['Type'], 'timestamp') === 0) $row[$f] = '2026-10-12 08:00:00';
                elseif (strpos($c['Type'], 'time') === 0) $row[$f] = '08:00:00';
                else $row[$f] = '';
            }
        }
        if (!$sql->insert($table, $row)) throw new RuntimeException('Fixture insert failed: ' . $table . ': ' . $sql->error()['message']);
    };
    $colors = [1 => 'Merah', 2 => 'Biru', 3 => 'Hijau', 4 => 'Orange', 5 => 'Hitam', 6 => 'Ungu'];
    $insert('tbl_product', ['product_id' => 688, 'product_nama' => 'Pulpen Gepeng', 'form_schema' => json_encode([['name' => 'ket_1', 'options' => $colors]])]);
    $insert('tbl_bahan', ['bahan_id' => 220, 'barcode' => 'TEST-PEN', 'bahan_nama' => 'Pulpen', 'satuan_id' => 3]);
    $insert('tbl_bahan', ['bahan_id' => 900, 'barcode' => 'TEST-PACK', 'bahan_nama' => 'Kemasan', 'satuan_id' => 3]);
    $insert('tbl_satuan', ['satuan_id' => 3, 'satuan_nama' => 'Pcs']);
    foreach ([359 => 'Merah', 361 => 'Biru', 363 => 'Hijau', 362 => 'Orange', 358 => 'Hitam', 360 => 'Ungu'] as $mid => $color) {
        $insert('tbl_material', ['material_id' => $mid, 'bahan_id' => 220, 'varian' => 'Pulpen Gepeng ' . $color, 'barcode' => 'TEST-' . $mid, 'konversi' => 1, 'status' => '1', 'satuan_gudang_id' => 3]);
        $insert('tbl_bom_dynamic_mapping', ['product_id' => 688, 'attribute_key' => 'ket_1', 'attribute_value' => $color, 'bahan_id' => 220, 'calc_type' => 'fixed', 'divider_factor' => 1, 'scrap_factor' => 0]);
        $insert('tbl_stok_gudang', ['material_id' => $mid, 'gudang_id' => 3, 'rak_id' => null, 'qty' => 100]);
    }
    $insert('bom_lines', ['product_id' => 688, 'bahan_id' => 358, 'quantity' => 1, 'calc_type' => 'fixed']);
    $active = new M_pembayaran; $active->db = $sql; $active->load = new BomLoad; $active->session = new BomSession;
    $active->production_bom = new Production_bom(['db' => $sql]);
    $active->M_gudang = new M_gudang; $active->M_gudang->db = $sql; $active->M_gudang->session = $active->session;
    $method = new ReflectionMethod(M_pembayaran::class, 'autoDeductStock'); $method->setAccessible(true);
    $cleanup = new ReflectionMethod(M_pembayaran::class, '_cleanup_existing_produksi'); $cleanup->setAccessible(true);
    $item = (object)['product_id' => 688, 'harga_id' => 0, 'qty' => 50, 'panjang' => 0, 'lebar' => 0, 'metadata' => '{"ket_1":"2"}', 'lokasi_cetak' => 3, 'nospk' => 'TEST-BLUE'];
    $insert('tbl_produksi', ['produksi_id' => 1, 'product_id' => 688, 'nospk' => $item->nospk, 'qty' => 50, 'metadata' => $item->metadata, 'status_produksi' => 'done']);
    $method->invoke($active, $item, 2, 1);
    check((float)$sql->get_where('tbl_stok_gudang', ['material_id' => 361])->row()->qty === 50.0, 'SQL selected blue deducted');
    foreach ([358,359,360,362,363] as $mid) check((float)$sql->get_where('tbl_stok_gudang', ['material_id' => $mid])->row()->qty === 100.0, 'SQL other color unchanged ' . $mid);
    check($sql->count_all_results('tbl_stok_log') === 1 && (int)$sql->get('tbl_stok_log')->row()->material_id === 361, 'SQL only blue stock log');
    $method->invoke($active, $item, 2, 1);
    check($sql->count_all_results('tbl_stok_log') === 1 && (float)$sql->get_where('tbl_stok_gudang', ['material_id' => 361])->row()->qty === 50.0, 'SQL repeated same production is idempotent');
    $changed = clone $item; $changed->qty = 51;
    reject(function () use ($method, $active, $changed) { $method->invoke($active, $changed, 2, 1); }, 'SQL changed repeat rejected');
    reject(function () use ($cleanup, $active) { $cleanup->invoke($active, 'TEST-BLUE'); }, 'SQL revision cannot delete posted production');
    check($sql->get_where('tbl_produksi', ['produksi_id' => 1])->row() !== null, 'SQL posted header preserved');
    $sql->trans_begin(); $bad = clone $item; $bad->qty = 101; $bad->nospk = 'TEST-INSUFFICIENT';
    $insert('tbl_produksi', ['produksi_id' => 2, 'product_id' => 688, 'nospk' => $bad->nospk, 'qty' => 101]);
    $method->invoke($active, $bad, 2, 2);
    check((float)$sql->get_where('tbl_stok_gudang', ['material_id' => 361])->row()->qty === -51.0, 'SQL existing negative-balance policy records actual consumption');
    $sql->trans_rollback();
    check($sql->get_where('tbl_produksi', ['produksi_id' => 2])->row() === null && $sql->count_all_results('tbl_stok_log') === 1, 'SQL outer cancellation rolls back new header and logs');
    check((float)$sql->get_where('tbl_stok_gudang', ['material_id' => 361])->row()->qty === 50.0, 'SQL outer cancellation restores previous balance');
    // Inject a failure on the second material to prove the first deduction is rolled back.
    $insert('tbl_material', ['material_id' => 901, 'bahan_id' => 900, 'varian' => 'Kemasan', 'barcode' => 'TEST-901', 'konversi' => 10, 'status' => '1', 'satuan_gudang_id' => 3]);
    $insert('tbl_stok_gudang', ['material_id' => 901, 'gudang_id' => 3, 'rak_id' => null, 'qty' => 100]);
    $insert('bom_lines', ['product_id' => 688, 'bahan_id' => 901, 'quantity' => 1, 'calc_type' => 'fixed']);
    $active->production_bom = new Production_bom(['db' => $sql]);
    if (!$sql->query("CREATE TRIGGER force_bom_failure BEFORE INSERT ON tbl_stok_log FOR EACH ROW BEGIN IF NEW.material_id = 901 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Injected test failure'; END IF; END")) throw new RuntimeException('Trigger setup failed.');
    $sql->trans_begin(); $next = clone $item; $next->qty = 2; $next->nospk = 'TEST-FAILURE';
    $insert('tbl_produksi', ['produksi_id' => 3, 'product_id' => 688, 'nospk' => $next->nospk, 'qty' => 2]);
    try { $method->invoke($active, $next, 2, 3); throw new RuntimeException('Injected failure did not abort.'); } catch (RuntimeException $e) { if ($e->getMessage() === 'Injected failure did not abort.') throw $e; }
    $sql->trans_rollback();
    check($sql->get_where('tbl_produksi', ['produksi_id' => 3])->row() === null && $sql->count_all_results('tbl_stok_log') === 1, 'SQL second-material failure rolls back header and both logs');
    check((float)$sql->get_where('tbl_stok_gudang', ['material_id' => 361])->row()->qty === 50.0 && (float)$sql->get_where('tbl_stok_gudang', ['material_id' => 901])->row()->qty === 100.0, 'SQL both balances rolled back');
    $sql->query('DROP TRIGGER force_bom_failure');
    // CI retains failed transaction status until rollback/next request; create a fresh connection.
    $sql->close(); $sql = DB($config, true); $active->db = $active->M_gudang->db = $sql;
    $active->production_bom = new Production_bom(['db' => $sql]);
    $insert('tbl_produksi', ['produksi_id' => 4, 'product_id' => 688, 'nospk' => 'TEST-SUCCESS', 'qty' => 2]);
    $next->nospk = 'TEST-SUCCESS'; $method->invoke($active, $next, 2, 4);
    check((float)$sql->get_where('tbl_stok_gudang', ['material_id' => 361])->row()->qty === 48.0, 'SQL selected blue plus common packaging');
    check((float)$sql->get_where('tbl_stok_gudang', ['material_id' => 901])->row()->qty === 98.0 && abs((float)$sql->get_where('tbl_stok_log', ['material_id' => 901])->row()->qty_out - 0.2) < 0.0001, 'SQL production base units converted once');
    // Exercise the real no-routing creation path for every reported tumbler color and shirt size.
    $active->M_master = new class {
        public function routingbykode($product, $price) { return new class { public function result_array() { return []; } }; }
    };
    $create = new ReflectionMethod(M_pembayaran::class, 'insertProduksi'); $create->setAccessible(true);
    $datasets = [
        [606, 144, 'jenisprint_id', [1 => 'Hitam', 2 => 'Putih'], [131,132]],
        [610, 148, 'warna_id', [1 => 'Hitam', 2 => 'Merah', 3 => 'Abu', 4 => 'Biru', 5 => 'Cream'], [144,146,147,148,145]],
        [776, 127, 'ukuran', [1 => 'S', 2 => 'M', 3 => 'L', 4 => 'XL', 5 => '2XL', 6 => '3XL', 7 => '4XL', 8 => '5XL'], [1151,1152,1153,1154,1155,1156,1157,1158]]
    ];
    foreach ($datasets as $set) {
        list($pid, $family, $key, $options, $mids) = $set;
        $insert('tbl_product', ['product_id' => $pid, 'product_nama' => 'Fixture ' . $pid, 'form_schema' => json_encode([['name' => $key, 'options' => $options]])]);
        $insert('tbl_bahan', ['bahan_id' => $family, 'barcode' => 'TEST-F-' . $family, 'bahan_nama' => 'Fixture family', 'satuan_id' => 3]);
        foreach ($options as $code => $label) {
            $mid = $mids[$code-1];
            $insert('tbl_material', ['material_id' => $mid, 'bahan_id' => $family, 'varian' => 'Fixture ' . $label, 'barcode' => 'TEST-M-' . $mid, 'konversi' => 1, 'status' => '1', 'satuan_gudang_id' => 3]);
            $insert('tbl_bom_dynamic_mapping', ['product_id' => $pid, 'attribute_key' => $key, 'attribute_value' => $label, 'bahan_id' => $mid, 'calc_type' => 'fixed', 'divider_factor' => 1, 'scrap_factor' => 0]);
            $insert('tbl_stok_gudang', ['material_id' => $mid, 'gudang_id' => 3, 'rak_id' => null, 'qty' => 100]);
        }
        $active->production_bom = new Production_bom(['db' => $sql]);
        foreach ($options as $code => $label) {
            $mid = $mids[$code-1];
            $order = (object)['product_id' => $pid, 'harga_id' => 99999, 'qty' => 2, 'panjang' => 0, 'lebar' => 0,
                'metadata' => json_encode([($key === 'ukuran' ? 'ket_1' : $key) => (string)$code]), 'lokasi_cetak' => 3,
                'nospk' => 'TEST-' . $pid . '-' . $code, 'dateline_tgl' => '2026-10-12', 'dateline_jam' => '08:00:00', 'konsumen_id' => 1, 'approve' => '1'];
            $sql->trans_begin();
            check($create->invoke($active, $order, 2) === true, 'SQL no-routing creation ' . $pid . ' ' . $label);
            $sql->trans_commit();
            $header = $sql->get_where('tbl_produksi', ['nospk' => $order->nospk])->row();
            $logs = $sql->get_where('tbl_stok_log', ['trx_type' => 'Produksi', 'trx_id' => $header->produksi_id])->result();
            check($header->status_produksi === 'done' && count($logs) === 1 && (int)$logs[0]->material_id === $mid, 'SQL only chosen variant deducted ' . $pid . ' ' . $label);
            check((int)$logs[0]->gudang_id === 3 && (float)$logs[0]->qty_base === 2.0, 'SQL production location and base quantity');
            check((float)$sql->get_where('tbl_stok_gudang', ['material_id' => $mid])->row()->qty === 98.0, 'SQL chosen balance');
        }
    }
    echo "Production BOM MySQL strict: $checks checks passed.\n";
} finally {
    if ($sql) $sql->close();
    if ($created && !$m->query('DROP DATABASE `' . $name . '`')) throw new RuntimeException('Disposable DB cleanup failed.');
    $m->close();
}
