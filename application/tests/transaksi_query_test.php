<?php
// Compile controller queries with CodeIgniter's real query builder, with no connection.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
ob_start(); require __DIR__ . '/transaksi_controller_test.php'; ob_end_clean();
require dirname(__DIR__, 2) . '/system/core/Common.php';
require dirname(__DIR__, 2) . '/system/database/DB_driver.php';
require dirname(__DIR__, 2) . '/system/database/DB_query_builder.php';
class CompileDb extends CI_DB_query_builder {
    protected $_escape_char = '`';
    public $queries = [], $last_rows = [];
    public function __construct() { parent::__construct(['dbdriver' => 'offline']); }
    public function escape_str($str, $like = false) { return str_replace("'", "''", $str); }
    public function field_exists($field, $table) {
        return $table === 'tbl_pemesanan_detail' && in_array($field, ['metadata', 'snapshot_harga', 'snapshot_subtotal'], true);
    }
    public function query($sql, $binds = false, $return_object = null) {
        $this->queries[] = $sql;
        return new TRows($this->last_rows);
    }
}
$checks = 0;
$c = fixture(); $c->db = new CompileDb();
invokePrivate($c, 'locked_receivables', [['consumer' => 10], 'SPK-A']);
$sql = end($c->db->queries);
check(strpos($sql, 'FOR UPDATE') !== false, 'payment SQL takes row locks');
check(strpos($sql, '`pm`.`konsumen_id` = 10') !== false && strpos($sql, "`p`.`nospk` = 'SPK-A'") !== false, 'single payment consumer and SPK predicates');
check(strpos($sql, '`pm`.`perusahaan_id` = 2') !== false && strpos($sql, '`p`.`perusahaan_id` = 2') !== false, 'both branch columns scoped');
invokePrivate($c, 'locked_receivables', [['consumer' => 10]]);
$sql = end($c->db->queries);
check(strpos($sql, '(p.total_tagihan - p.total_bayar) > 0') !== false, 'receivable expression compiles correctly');
check(strpos($sql, 'ORDER BY `pm`.`created_at` ASC, `p`.`pembayaran_id` ASC') !== false, 'stable oldest-first lock ordering');
invokePrivate($c, 'shift_report_rows', [2, 4]);
$sql = end($c->db->queries);
check(strpos($sql, 'FROM `kasir_shift` `ks`') !== false && strpos($sql, '`ks`.`shift_id` = 4') !== false, 'actual shift table and ID');
check(strpos($sql, '`u`.`perusahaan_id1` = 2') !== false && strpos($sql, '`p`.`id_perusahaan` = `u`.`perusahaan_id1`') !== false, 'actual branch fields in shift query');
check(strpos($sql, '`k`.`karyawan_id` = `u`.`karyawan_id`') !== false, 'actual employee join');
$shift = (object) ['id' => 4, 'kasir_id' => 7, 'perusahaan_id' => 2, 'tanggal_mulai' => '2026-10-10 09:00:00', 'tanggal_selesai' => '2026-10-11 10:00:00'];
invokePrivate($c, 'fill_shift_report', [$shift]);
$sql = implode("\n", $c->db->queries);
check(strpos($sql, "CONCAT(tgl_bayar, ' ', jam_bayar) >= '2026-10-10 09:00:00'") !== false, 'full datetime start boundary');
check(strpos($sql, "CONCAT(tgl_bayar, ' ', jam_bayar) <= '2026-10-11 10:00:00'") !== false, 'full datetime end boundary across midnight');
check(strpos($sql, '`shift_id` = 4') !== false && strpos($sql, 'FROM `tbl_pengeluaran`') !== false, 'expenses scoped by actual shift ID');
check(strpos($sql, 'SELECT DISTINCT `pi`.*') !== false && strpos($sql, '`pb`.`kasir_id` = 7') !== false, 'receivables scoped through cashier payment header');
$model = new M_transaksi(); $model->db = new CompileDb(); $model->session = new TSession();
$model->getDataPemesanankasir('0', 2);
$sql = end($model->db->queries);
check(strpos($sql, 'JSON_VALID(pd.metadata)') !== false && strpos($sql, '$.subtotal') !== false, 'Cashier list uses stored totals and tolerates malformed historical metadata');
check(strpos($sql, 'pd.snapshot_subtotal') !== false && strpos($sql, 'pd.snapshot_harga') !== false, 'List supports snapshot columns before current master price');
check(strpos($sql, "'{}'") !== false && strpos($sql, 'DECIMAL(20,4)') !== false, 'JSON and numeric expressions compile intact');
$model->getDetailPemesanan('TEST-SPK');
$sql = end($model->db->queries);
check(strpos($sql, 'SELECT `tbl_pemesanan`.*') !== false && strpos($sql, 'tbl_harga_product') === false, 'Cashier header preserves order date without price-table collisions');
$model->getDetailPesan('TEST-SPK');
$sql = end($model->db->queries);
check(strpos($sql, 'LEFT JOIN `tbl_harga_product`') !== false, 'Deleted historical price does not drop order items before stock deduction');
check(strpos($sql, '`tbl_pemesanan_detail`.`harga_id`') !== false, 'Recorded price ID preserved for variant BOM selection');
echo "Transaksi CodeIgniter queries: $checks checks passed.\n";
