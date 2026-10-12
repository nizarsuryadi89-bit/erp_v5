<?php
// Copy table STRUCTURES into a disposable database; never write ERP records.
if (PHP_SAPI !== 'cli' || !in_array('--isolated-db', $argv, true)) { exit("Run with --isolated-db to use a disposable database.\n"); }
define('BASEPATH', dirname(__DIR__, 2) . '/system/');
ob_start(); require __DIR__ . '/manufaktur_controller_test.php'; ob_end_clean();
define('APPPATH', dirname(__DIR__) . '/');
define('ENVIRONMENT', 'development');
function get_instance() { return null; }
function config_item($item) { return null; }
require BASEPATH . 'core/Common.php';
require BASEPATH . 'database/DB.php';
$_SERVER['HTTP_HOST'] = 'localhost';
require __DIR__ . '/../config/database.php';
$config = $db['default'];
$test_name = 'tgm_manufaktur_test_' . bin2hex(random_bytes(6));
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$connection = new mysqli($config['hostname'], $config['username'], $config['password'], '', $config['port'] ?? 3306, '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock');
$created = false;
try {
    $connection->query('CREATE DATABASE `' . $test_name . '`'); $created = true;
    $seed = fixture();
    foreach (array_keys($seed->db->tables) as $table) {
        $schema = $connection->real_escape_string($config['database']);
        $exists = $connection->query("SELECT 1 FROM information_schema.tables WHERE table_schema = '$schema' AND table_name = '$table'")->num_rows;
        if (!$exists) { unset($seed->db->tables[$table]); continue; }
        $connection->query('CREATE TABLE `' . $test_name . '`.`' . $table . '` LIKE `' . $connection->real_escape_string($config['database']) . '`.`' . $table . '`');
    }
    $config['database'] = $test_name; $config['hostname'] = '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock';
    $config['db_debug'] = false;
    $sql = DB($config, true);
    if (!$sql->conn_id) { throw new RuntimeException('Test DB connection failed.'); }
    // Force strict SQL conversion to catch column/type mistakes in the controller.
    $sql->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION'");
    foreach ($seed->db->tables as $table => $rows) {
        foreach ($rows as $row) {
            if (!$sql->insert($table, $row)) { throw new RuntimeException('Fixture ' . $table . ': ' . $sql->error()['message']); }
        }
    }
    $c = fixture(); $c->db = $sql; $c->M_master = new TestMaster($sql);
    $checks = 0;
    $r = runAction($c, 'clone_product', ['source_id' => 1, 'new_name' => 'Salinan uji SQL']);
    check($r['success'], 'real SQL clone'); $new_id = $r['new_id'];
    $prices = $sql->get_where('tbl_harga_product', ['product_id' => $new_id])->result_array();
    $bom = $sql->get_where('bom_lines', ['product_id' => $new_id])->row_array();
    $routes = $sql->order_by('routing_urutan')->get_where('routing', ['product_id' => $new_id])->result_array();
    $dynamic = $sql->get_where('tbl_bom_dynamic_mapping', ['product_id' => $new_id])->row_array();
    check($bom['harga_id'] == $prices[0]['harga_id'], 'SQL clone variation');
    check($routes[0]['bom_line_id'] == $bom['bom_line_id'], 'SQL clone BOM');
    check($routes[1]['bom_line_id'] === 'dyn_' . $dynamic['id'], 'SQL clone dynamic');
    $r = runAction($c, 'bulk_apply_template', ['template_id' => 1, 'target_ids' => [2], 'options' => ['bom', 'routing']]);
    check($r['success'], 'SQL template'); $bom = $sql->get_where('bom_lines', ['product_id' => 2])->row_array();
    check($bom['harga_id'] == 20, 'SQL template variation mapping');
    $r = runAction($c, 'update_bom', ['bom_line_id' => $bom['bom_line_id'], 'qty' => 4, 'scrap_factor' => 0]);
    check($r['success'], 'SQL quick BOM update');
    $row = $sql->get_where('bom_lines', ['bom_line_id' => $bom['bom_line_id']])->row_array();
    check($row['harga_id'] == 20 && $row['panjang'] == 12 && $row['lebar'] == 14, 'SQL quick edit preserves links/dimensions');
    $r = runAction($c, 'actionUpdateproduct', ['product_id' => 2, 'kategori_id' => 1, 'subkategori_id' => 0, 'satuan_id' => 1, 'tipe_id' => '', 'product_nama' => 'Uji edit SQL', 'metode_id' => 2]);
    check(isset($c->session->flash['success']), 'SQL product edit with optional type');
    $r = runAction($c, 'copy_bom_routing_to_variations', ['product_id' => 1, 'source_harga_id' => 10, 'target_harga_ids' => [11]]);
    check($r['success'], 'SQL variation copy');
    $r = runAction($c, 'delete_bom', ['bom_id' => $bom['bom_line_id']]);
    check($r['success'], 'SQL delete BOM');
    $routes = $sql->get_where('routing', ['product_id' => 2])->result_array();
    check($routes[0]['bom_line_id'] === null, 'SQL route detached on delete');
    $r = runAction($c, 'action_save_all', ['product_id' => 1, 'harga_id' => [10], 'detail_product' => ['Updated SQL'], 'harga_1' => ['1.500']]);
    check($r['success'], 'SQL matrix optional dimensions');
    // Make a copied route invalid; clone must roll back header/prices/materials already inserted.
    $sql->update('routing', ['bom_line_id' => 'dyn_999999'], ['routing_id' => 61]);
    $before = $sql->count_all_results('tbl_product');
    $r = runAction($c, 'clone_product', ['source_id' => 1, 'new_name' => 'Harus rollback']);
    check(!$r['success'] && $sql->count_all_results('tbl_product') === $before, 'SQL clone rollback');
    // No existing ERP image references are used; delete only a disposable clone.
    $sql->update('tbl_product', ['product_img' => ''], ['product_id' => $new_id]);
    $r = runAction($c, 'delete_bulk_product', ['ids' => [$new_id]]);
    check($r['status'] === 'success', 'SQL unused product deletion');
    check(!$sql->get_where('tbl_bom_dynamic_mapping', ['product_id' => $new_id])->row_array(), 'SQL dynamic rows deleted');
    $sql->close();
    echo "Manufaktur MySQL: $checks checks passed.\n";
} finally {
    if ($created) { $connection->query('DROP DATABASE `' . $test_name . '`'); }
    $connection->close();
}
