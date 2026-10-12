<?php
// Controller and model regressions with disposable memory records. No ERP DB or notifications.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('BASEPATH', __DIR__);
error_reporting(E_ALL);
set_error_handler(function ($n, $message, $file, $line) { throw new ErrorException($message, 0, $n, $file, $line); });
function log_message($level, $message) {}
function log_activity($user, $action, $module, $id) {}
function encrypt_url($value) { return 'encoded-' . $value; }
function base_url($path = '') { return '/tgm_erp_old/' . $path; }
function redirect($url) { throw new TRedirect($url); }
function show_404() { throw new RuntimeException('404'); }
class TRedirect extends RuntimeException {}
class MY_Controller {
    public $db, $session, $input, $output, $M_transaksi, $M_master, $M_pembayaran, $Print_queue_model, $load, $manual_sales, $form_validation;
    public $audit = [];
    protected function _log_manual($title, $payload, $verb) { $this->audit[] = $payload; }
}
class CI_Model { public $db, $session, $uri; }
require __DIR__ . '/../controllers/Transaksi.php';
require __DIR__ . '/../models/M_transaksi.php';
class TInput {
    public $posts = [], $gets = [], $ajax = true;
    public function post($key = null, $clean = false) { return $key === null ? $this->posts : ($this->posts[$key] ?? null); }
    public function get($key, $clean = false) { return $this->gets[$key] ?? null; }
    public function is_ajax_request() { return $this->ajax; }
}
class TSession {
    public $data = ['id' => 7, 'level' => 3, 'perusahaan_id' => 2], $flash = [];
    public function userdata($key) { return $this->data[$key] ?? null; }
    public function set_flashdata($key, $value) { $this->flash[$key] = $value; }
}
class TOutput {
    public $body, $status = 200;
    public function set_content_type($type) { return $this; }
    public function set_output($body) { $this->body = json_decode($body, true); return $this; }
    public function set_status_header($status) { $this->status = $status; return $this; }
}
class TRows {
    private $rows;
    public function __construct($rows) { $this->rows = array_values($rows); }
    public function row() { return isset($this->rows[0]) ? (object) $this->rows[0] : null; }
    public function row_array() { return $this->rows[0] ?? null; }
    public function result() { return array_map(function ($r) { return (object) $r; }, $this->rows); }
    public function result_array() { return $this->rows; }
    public function num_rows() { return count($this->rows); }
}
class TDb {
    public $tables = [], $failTable, $throwTable, $open = false, $commits = 0, $rollbacks = 0, $queries = [], $queued = [];
    private $conditions = [], $table, $snapshot, $ok = true;
    public function distinct() { return $this; }
    public function select($fields) { return $this; }
    public function from($table) { $this->table = $table; return $this; }
    public function join($table, $condition, $type = '') { $this->queries[] = "JOIN $table $condition $type"; return $this; }
    public function where($key, $value = null) {
        if (is_array($key)) { foreach ($key as $k => $v) { $this->where($k, $v); } }
        else { $this->conditions[] = [$key, $value]; }
        return $this;
    }
    public function order_by($key, $direction = 'ASC') { return $this; }
    public function limit($n) { return $this; }
    public function group_by($field) { return $this; }
    private function reset() { $this->table = null; $this->conditions = []; }
    private function matches($row) {
        foreach ($this->conditions as $c) {
            list($key, $value) = $c;
            if (strpos($key, '(p.total_tagihan - p.total_bayar)') === 0) { if ($row['total_tagihan'] - $row['total_bayar'] <= $value) { return false; } continue; }
            $name = preg_replace('/^[a-z]+\./', '', $key);
            if (strpos($name, '(') !== false) { continue; }
            if (($row[$key] ?? $row[$name] ?? null) != $value) { return false; }
        }
        return true;
    }
    private function read($table) {
        $name = explode(' ', $table)[0]; $rows = $this->tables[$name] ?? [];
        if ($table === 'tbl_pembayaran p') {
            foreach ($rows as &$row) {
                foreach ($this->tables['tbl_pemesanan'] as $order) {
                    if ($order['nospk'] === $row['nospk']) { $row['pm.konsumen_id'] = $order['konsumen_id']; $row['pm.perusahaan_id'] = $order['perusahaan_id']; }
                }
            }
            unset($row);
            usort($rows, function ($a, $b) { return $a['pembayaran_id'] <=> $b['pembayaran_id']; });
        }
        $rows = array_filter($rows, function ($r) { return $this->matches($r); });
        return new TRows($rows);
    }
    public function get($table = null) {
        if ($this->queued) { $rows = new TRows(array_shift($this->queued)); }
        else { $rows = $this->read($table ?: $this->table); }
        $this->reset(); return $rows;
    }
    public function get_where($table, $where) { return $this->where($where)->get($table); }
    public function get_compiled_select() { $query = 'MEM:' . json_encode([$this->table, $this->conditions]); $this->reset(); return $query; }
    public function query($sql, $binds = []) {
        $this->queries[] = $sql;
        if (strpos($sql, 'MEM:') === 0) {
            list($table, $this->conditions) = json_decode(substr($sql, 4, -11), true);
            $rows = $this->read($table); $this->reset(); return $rows;
        }
        if (strpos($sql, 'SELECT user_id FROM users WHERE user_id = ? FOR UPDATE') === 0) { return new TRows([['user_id' => $binds[0]]]); }
        if (preg_match('/SELECT \* FROM (tbl_spk|tbl_pemesanan) WHERE nospk = \? FOR UPDATE/', $sql, $m)) {
            return $this->get_where($m[1], ['nospk' => $binds[0]]);
        }
        throw new RuntimeException('Unexpected SQL: ' . $sql);
    }
    private function write($table) {
        if ($this->throwTable === $table) { throw new TypeError('Injected failure'); }
        if ($this->failTable === $table) { $this->ok = false; $this->reset(); return false; }
        return true;
    }
    public function insert_id() { return 90; }
    public function insert($table, $data) { if (!$this->write($table)) { return false; } $this->tables[$table][] = $data; return true; }
    public function update($table, $data, $where = []) {
        if (!$this->write($table)) { return false; } $this->where($where);
        foreach ($this->tables[$table] as &$row) { if ($this->matches($row)) { $row = array_replace($row, $data); } }
        unset($row); $this->reset(); return true;
    }
    public function delete($table, $where = []) {
        if (!$this->write($table)) { return false; } $this->where($where);
        $this->tables[$table] = array_values(array_filter($this->tables[$table] ?? [], function ($r) { return !$this->matches($r); }));
        $this->reset(); return true;
    }
    public function field_exists($field, $table) { return true; }
    public function trans_begin() { $this->snapshot = $this->tables; $this->ok = true; $this->open = true; return true; }
    public function trans_status() { return $this->ok; }
    public function trans_commit() { $this->open = false; $this->commits++; return true; }
    public function trans_rollback() { if ($this->open) { $this->tables = $this->snapshot; } $this->open = false; $this->rollbacks++; return true; }
    public function error() { return ['message' => 'Injected failure']; }
    public function last_query() { return 'test'; }
}
class TMaster {
    public $product = ['product_id' => 50, 'satuan_id' => 3, 'kategori_id' => 1, 'product_form_schema' => '', 'subcategory_form_schema' => '', 'category_form_schema' => ''];
    public function getProductByIdComplete($id) { return $this->product; }
}
class TTransactions {
    public $db, $price, $lookup;
    public function getNoSPK($user, $company) { return new TRows([]); }
    public function get_product_price($id, $qty, $params) { $this->lookup = [$id, $qty, $params]; return new TRows([$this->price]); }
    public function tampilTmp($id) { return $this->db->get_where('tbl_temp_transaksi', ['no_spk' => $id]); }
    public function deleteTmp($id) { $m = (new ReflectionClass(M_transaksi::class))->newInstanceWithoutConstructor(); $m->db = $this->db; $m->session = new TSession(); return $m->deleteTmp($id); }
}
class TLoad { public function model($name, $alias = null) {} public function library($name) {} }
class TValidation { public function set_rules($a, $b, $c) {} public function run() { return true; } }
function fixture() {
    $c = (new ReflectionClass(Transaksi::class))->newInstanceWithoutConstructor();
    $c->db = new TDb(); $c->input = new TInput(); $c->output = new TOutput(); $c->session = new TSession(); $c->load = new TLoad(); $c->form_validation = new TValidation();
    $c->M_master = new TMaster(); $c->M_transaksi = new TTransactions(); $c->M_transaksi->db = $c->db;
    $c->M_transaksi->price = ['harga_id' => 80, 'harga_1' => 1000, 'harga_minimal' => 0, 'detail_product' => 'Produk uji', 'metadata' => null];
    $c->db->tables = [
        'tbl_pemesanan' => [['nospk' => 'SPK-A', 'konsumen_id' => 10, 'perusahaan_id' => 2, 'deskprint_id' => 7, 'status_pembayaran' => 'pending'], ['nospk' => 'SPK-B', 'konsumen_id' => 10, 'perusahaan_id' => 2, 'deskprint_id' => 7, 'status_pembayaran' => 'pending'], ['nospk' => 'SPK-C', 'konsumen_id' => 10, 'perusahaan_id' => 3, 'deskprint_id' => 8, 'status_pembayaran' => 'pending']],
        'tbl_pembayaran' => [['pembayaran_id' => 1, 'nospk' => 'SPK-A', 'perusahaan_id' => 2, 'total_tagihan' => 100, 'total_bayar' => 20, 'status_lunas' => '0'], ['pembayaran_id' => 2, 'nospk' => 'SPK-B', 'perusahaan_id' => 2, 'total_tagihan' => 200, 'total_bayar' => 0, 'status_lunas' => '0'], ['pembayaran_id' => 3, 'nospk' => 'SPK-C', 'perusahaan_id' => 3, 'total_tagihan' => 1000, 'total_bayar' => 0, 'status_lunas' => '0']],
        'tbl_piutang' => [['piutang_id' => 5, 'nospk' => 'SPK-A', 'perusahaan_id' => 2, 'total_tagihan' => 100, 'total_bayar' => 20, 'status' => 'SEBAGIAN']],
        'tbl_pembayaran_detail' => [], 'tbl_piutang_pembayaran' => [], 'tbl_product' => [$c->M_master->product], 'tbl_side' => [['side_id' => 1, 'side_name' => '1 Sisi']],
        'tbl_mesin_printing' => [['id' => 2, 'mesin_nama' => 'Konica']], 'tbl_spk' => [['nospk' => 'DRAFT-A', 'user_id' => 7, 'perusahaan_id' => 2]],
        'tbl_rekening' => [['id' => 1]], 'tbl_mesin_edc' => [['edc_id' => 1]], 'tbl_ewallet' => [['id' => 1]],
        'tbl_temp_transaksi' => [], 'tbl_pemesanan_detail' => [], 'tbl_pembayaran_produk' => [], 'tbl_konsumen' => []
    ];
    return $c;
}
function act($c, $method, $post = [], $args = []) {
    $c->input->posts = $post; $c->output->body = null; ob_start();
    try { call_user_func_array([$c, $method], $args); } catch (TRedirect $e) { $c->output->body = ['redirect' => $e->getMessage()]; }
    finally { $text = ob_get_clean(); }
    return $text !== '' ? json_decode($text, true) : $c->output->body;
}
function invokePrivate($c, $method, $args = []) { $m = new ReflectionMethod($c, $method); $m->setAccessible(true); return $m->invokeArgs($c, $args); }
$checks = 0;
function check($condition, $label) { global $checks; $checks++; if (!$condition) { throw new RuntimeException($label); } }
$pay = ['nospk' => 'SPK-A', 'konsumen_id' => 10, 'payment_amount' => 50, 'payment_method' => 'cash', 'payment_date' => '2026-10-10'];
foreach ([-10, 0, 'bad', ['50'], '1e999'] as $amount) {
    $c = fixture(); $before = $c->db->tables; $r = act($c, 'process_payment', array_replace($pay, ['payment_amount' => $amount]));
    check(!$r['success'] && $c->db->tables === $before && !$c->db->open, 'invalid payment rejected');
}
foreach ([['payment_date' => '2026-02-30'], ['payment_method' => 'bad'], ['payment_method' => 'transfer', 'bank_id' => ''], ['konsumen_id' => ['10']], ['payment_notes' => ['bad']]] as $invalid) {
    $c = fixture(); $r = act($c, 'process_payment', array_replace($pay, $invalid)); check(!$r['success'] && !$c->db->open, 'invalid payment metadata rejected');
}
$c = fixture(); $r = act($c, 'process_payment', $pay);
check($r['success'], 'single payment succeeds'); check($c->db->tables['tbl_pembayaran'][0]['total_bayar'] == 70, 'single total updated');
check($c->db->tables['tbl_piutang'][0]['total_bayar'] == 70, 'receivable synchronized');
check(count($c->db->tables['tbl_pembayaran_detail']) === 1 && count($c->db->tables['tbl_piutang_pembayaran']) === 1, 'payment histories recorded');
check(strpos($c->db->tables['tbl_piutang_pembayaran'][0]['tanggal_bayar'], '2026-10-10 ') === 0, 'chosen payment date honored');
check(!$c->db->open && $c->db->commits === 1 && strpos(implode(' ', $c->db->queries), 'FOR UPDATE') !== false, 'single locks and commits');
foreach ([['nospk' => 'MISSING'], ['konsumen_id' => 99], ['nospk' => 'SPK-C'], ['payment_amount' => 81]] as $invalid) {
    $c = fixture(); $before = $c->db->tables; $r = act($c, 'process_payment', array_replace($pay, $invalid));
    check(!$r['success'] && !$c->db->open && $before === $c->db->tables, 'missing, foreign or excessive payment rolls back');
}
foreach (['tbl_pembayaran', 'tbl_pembayaran_detail', 'tbl_piutang', 'tbl_piutang_pembayaran'] as $table) {
    $c = fixture(); $before = $c->db->tables; $c->db->failTable = $table; $r = act($c, 'process_payment', $pay);
    check(!$r['success'] && $c->db->tables === $before && !$c->db->open, 'single database failure atomic ' . $table);
}
$c = fixture(); $before = $c->db->tables; $c->db->throwTable = 'tbl_piutang_pembayaran'; $r = act($c, 'process_payment', $pay);
check(!$r['success'] && $c->db->tables === $before && !$c->db->open, 'payment Throwable rollback');
$c = fixture(); $r = act($c, 'process_payment_collective', array_replace($pay, ['payment_amount' => 150]));
check($r['success'], 'collective succeeds'); check($c->db->tables['tbl_pembayaran'][0]['total_bayar'] == 100 && $c->db->tables['tbl_pembayaran'][1]['total_bayar'] == 70, 'oldest debt paid first');
check($c->db->tables['tbl_pemesanan'][0]['status_pembayaran'] === 'paid', 'settled invoice marked paid');
check($c->db->tables['tbl_pembayaran'][2]['total_bayar'] == 0, 'other branch untouched');
check(array_sum(array_column($c->db->tables['tbl_pembayaran_detail'], 'jumlah')) == 150, 'every collective cent recorded');
$c = fixture(); $before = $c->db->tables; $r = act($c, 'process_payment_collective', array_replace($pay, ['payment_amount' => 281]));
check(!$r['success'] && $before === $c->db->tables && !$c->db->open, 'collective overpay rejected without silent discard');
$c = fixture(); $c->db->tables['tbl_pembayaran'][0]['total_bayar'] = 99.7; $c->db->tables['tbl_pembayaran'][1]['total_bayar'] = 200;
$r = act($c, 'process_payment_collective', array_replace($pay, ['payment_amount' => 0.3]));
check($r['success'] && $c->db->tables['tbl_pembayaran'][0]['total_bayar'] == 100, 'small outstanding amount not skipped');
$c = fixture(); $c->db->tables['tbl_pembayaran'][] = $c->db->tables['tbl_pembayaran'][0]; $before = $c->db->tables;
$r = act($c, 'process_payment', $pay); check(!$r['success'] && $before === $c->db->tables, 'ambiguous duplicated payment blocked');
$line = ['product_id' => 50, 'nospk' => 'DRAFT-A', 'qty' => 2, 'panjang' => 0, 'lebar' => 0, 'jenisprint_id' => 2, 'ket_1' => 1];
foreach ([['kategori' => 1, 'satuan' => 3, 'p' => 0, 'l' => 0, 'expected' => 2000], ['kategori' => 2, 'satuan' => 2, 'p' => 2, 'l' => 3, 'expected' => 12000], ['kategori' => 3, 'satuan' => 4, 'p' => 2, 'l' => 3, 'expected' => 50000], ['kategori' => 9, 'satuan' => 2, 'p' => 0, 'l' => 0, 'expected' => 20], ['kategori' => 9, 'satuan' => 11, 'p' => 2, 'l' => 0.6, 'ket' => 5, 'expected' => 4000]] as $case) {
    $c = fixture(); $c->M_master->product['kategori_id'] = $case['kategori']; $c->M_master->product['satuan_id'] = $case['satuan']; $c->db->tables['tbl_product'] = [$c->M_master->product];
    $post = array_replace($line, ['kategori_id' => 999, 'panjang' => $case['p'], 'lebar' => $case['l'], 'ket_1' => $case['ket'] ?? 1]);
    $preview = act($c, 'estimate_price', $post); $r = act($c, 'action_add_temp', $post);
    $meta = json_decode($c->db->tables['tbl_temp_transaksi'][0]['metadata'], true);
    check($r['status'] === 'success' && $preview['subtotal'] == $case['expected'] && $meta['subtotal'] == $preview['subtotal'], 'preview equals stored draft by category');
    $c->db->tables['tbl_pembayaran'] = [];
    $r = act($c, 'action_add_temp_editspk', array_replace($post, ['nospk' => 'SPK-A'])); $detail = $c->db->tables['tbl_pemesanan_detail'][0];
    check($r['status'] === 'success' && $detail['subtotal'] == $preview['subtotal'], 'existing SPK matches preview without undefined inputs');
    check($meta['jenisprint'] === 'Konica', 'printing machine uses actual primary key');
}
$c = fixture(); $c->M_transaksi->price['harga_minimal'] = 5000; $preview = act($c, 'estimate_price', $line); $r = act($c, 'action_add_temp', $line);
check($preview['subtotal'] == 5000 && json_decode($c->db->tables['tbl_temp_transaksi'][0]['metadata'], true)['subtotal'] == 5000, 'minimum price frozen');
$c = fixture(); $r = act($c, 'action_add_temp', array_replace($line, ['extra' => ['a', 'b'], 'unit_price' => 1, 'subtotal' => 1])); $meta = json_decode($c->db->tables['tbl_temp_transaksi'][0]['metadata'], true);
check($r['status'] === 'success' && $meta['extra'] === ['a', 'b'] && $meta['subtotal'] == 2000 && $meta['unit_price'] == 1000, 'array metadata safe and client prices overwritten');
$c = fixture(); $r = act($c, 'action_add_temp', array_replace($line, ['qty' => -1])); check($r['status'] === 'error' && !$c->db->tables['tbl_temp_transaksi'], 'negative item quantity rejected');
$c = fixture(); $r = act($c, 'action_add_temp', array_replace($line, ['panjang' => -1])); check($r['status'] === 'error', 'negative dimensions rejected');
$c = fixture(); $c->M_master->product['subcategory_form_schema'] = '[{"name":"sisi","type":"select","options":{"1":"Satu"}}]';
$r = act($c, 'estimate_price', array_replace($line, ['sisi' => '1'])); check($c->M_transaksi->lookup[2]['ket_1'] === '1', 'empty product schema falls back to subcategory');
$c = fixture(); $c->db->tables['tbl_temp_transaksi'] = [['no_spk' => 'DRAFT-A', 'user_id' => 7], ['no_spk' => 'DRAFT-B', 'user_id' => 7], ['no_spk' => 'DRAFT-C', 'user_id' => 8]];
check($c->M_transaksi->deleteTmp('DRAFT-A'), 'actual model returns deletion result');
check(array_column($c->db->tables['tbl_temp_transaksi'], 'no_spk') === ['DRAFT-B', 'DRAFT-C'], 'deleting one draft preserves other drafts');
$c = fixture(); $c->db->tables['tbl_pembayaran'] = []; $c->db->tables['tbl_pemesanan_detail'] = [['nospk' => 'SPK-A', 'product_id' => 50, 'qty' => 2, 'panjang' => 2, 'lebar' => 3, 'harga_id' => 80, 'approve' => 1, 'lokasi_cetak' => 2, 'metadata' => '{"unit_price":1000,"subtotal":12000,"warna":"merah"}', 'dateline_tgl' => '2026-10-12']];
$r = act($c, 'edit_pending_spk', ['nospk' => 'SPK-A']);
check($r['status'] === 'success' && strpos($r['redirect'], 'createspk/SPK-A') !== false, 'withdrawal responds JSON with draft location');
check(json_decode($c->db->tables['tbl_temp_transaksi'][0]['metadata'], true)['subtotal'] == 12000, 'withdrawal retains price snapshot');
check($c->db->tables['tbl_temp_transaksi'][0]['dateline_tgl'] === '2026-10-12' && !$c->db->open, 'withdrawal retains deadline and commits');
$c = fixture(); $before = $c->db->tables; $r = act($c, 'edit_pending_spk', ['nospk' => 'SPK-A']); check($r['status'] === 'error' && $before === $c->db->tables && !$c->db->open, 'already invoiced order cannot withdraw');
$c = fixture(); $r = act($c, 'edit_pending_spk', ['nospk' => 'SPK-C']); check($r['status'] === 'error' && !$c->db->open, 'foreign withdrawal blocked');
$c = fixture(); $c->db->queued = [[['tanggal' => '2026-10-10', 'jumlah_transaksi' => 1, 'total_penjualan' => 100]], [['tanggal' => '2026-10-10', 'jumlah_transaksi' => 2, 'total_penjualan' => 200]]];
$r = act($c, 'get_calendar_events'); check($r['success'] && $r['data'][0]['extendedProps']['total'] == 300 && $r['data'][0]['extendedProps']['count'] == 3, 'calendar handles merged records as objects');
$manual = ['konsumen_id' => 10, 'tanggal_transaksi' => '2026-10-10', 'metode_bayar' => 'tunai', 'products' => [['product_id' => 50, 'jumlah' => -2, 'harga_satuan' => 100]]];
$c = fixture(); $r = act($c, 'manual_update', array_merge($manual, ['nospk' => 'MAN-TEST'])); check($r['status'] === 'error' && !$c->db->open, 'manual negative quantity rejected before writes');
$c = fixture(); $r = act($c, 'manual_update', array_merge($manual, ['products' => 'bad', 'nospk' => 'MAN-TEST'])); check($r['status'] === 'error', 'manual products must be array');
foreach ([['payment_method' => 'transfer', 'bank_id' => 1, 'db' => 'transfer'], ['payment_method' => 'qris', 'wallet_id' => 1, 'db' => 'ewallet'], ['payment_method' => 'edc', 'edc_id' => 1, 'db' => 'debit']] as $method) {
    $c = fixture(); $r = act($c, 'process_payment', array_replace($pay, $method));
    check($r['success'] && $c->db->tables['tbl_pembayaran_detail'][0]['metode'] === $method['db'], 'payment method mapped to supported database enum');
}
$c = fixture(); $r = act($c, 'process_payment', array_replace($pay, ['payment_method' => 'transfer', 'bank_id' => 999])); check(!$r['success'], 'nonexistent payment account rejected');
function draftFixture() {
    $c = fixture();
    $c->db->tables['tbl_temp_transaksi'] = [['no_spk' => 'DRAFT-A', 'user_id' => 7, 'product_id' => 50, 'temp_qty' => 2, 'temp_panjang' => 1, 'temp_lebar' => 1, 'harga_id' => 80, 'harga_1' => 9999, 'metadata' => '{"unit_price":1000,"subtotal":2500}', 'satuan_id' => 3], ['no_spk' => 'DRAFT-B', 'user_id' => 7]];
    return $c;
}
$draftPost = ['nospk' => 'DRAFT-A', 'konsumen_id' => 10, 'total_pemesanan' => 999999, 'deskprint_id' => 7, 'id_jenis_transaksi' => 1];
$c = draftFixture(); $r = act($c, 'add_pemesanan', $draftPost);
check(isset($c->session->flash['success']) && $c->db->commits === 1, 'SPK commits');
$detail = $c->db->tables['tbl_pemesanan_detail'][0]; check($detail['unit_price'] == 1000 && $detail['subtotal'] == 2500, 'finalized SPK retains frozen draft price over current master price');
check(array_column($c->db->tables['tbl_temp_transaksi'], 'no_spk') === ['DRAFT-B'], 'finalizing SPK preserves unrelated drafts');
check(json_decode($c->audit[0], true)['total'] == 2500, 'order total calculated from actual details');
foreach (['tbl_pemesanan', 'tbl_pemesanan_detail', 'tbl_temp_transaksi'] as $table) {
    $c = draftFixture(); $before = $c->db->tables; $c->db->failTable = $table; $r = act($c, 'add_pemesanan', $draftPost);
    check(isset($c->session->flash['error']) && $c->db->tables === $before && !$c->db->open, 'failed SPK save retains draft atomically ' . $table);
}
$c = draftFixture(); $c->db->tables['tbl_spk'][0]['user_id'] = 99; $before = $c->db->tables; $r = act($c, 'add_pemesanan', $draftPost);
check(isset($c->session->flash['error']) && $before === $c->db->tables, 'foreign SPK finalization rejected');
$c = draftFixture(); $c->db->tables['tbl_pemesanan'][] = ['nospk' => 'DRAFT-A', 'status_pembayaran' => 'paid']; $before = $c->db->tables; $r = act($c, 'add_pemesanan', $draftPost);
check(isset($c->session->flash['error']) && $before === $c->db->tables, 'paid SPK cannot be overwritten from draft');
$c = fixture(); $c->db->tables['tbl_pembayaran'] = []; $c->db->tables['tbl_pemesanan_detail'] = [['nospk' => 'SPK-A', 'product_id' => 50, 'qty' => 2, 'panjang' => 1, 'lebar' => 1, 'harga_id' => 80, 'approve' => 1, 'lokasi_cetak' => 2]];
$before = $c->db->tables; $c->db->failTable = 'tbl_pemesanan'; $r = act($c, 'edit_pending_spk', ['nospk' => 'SPK-A']);
check($r['status'] === 'error' && $before === $c->db->tables && !$c->db->open, 'failed withdrawal restores order and temp');
$c = fixture(); $r = act($c, 'step_satu', ['konsumen_id' => 10, 'id_jenis_transaksi' => 1]);
check($r['status'] === 'success' && strpos($r['nospk'], '2-7-') === 0 && !$c->db->open, 'SPK number generation locks user and commits');
$c = fixture(); $c->db->failTable = 'tbl_spk'; $before = $c->db->tables;
$r = act($c, 'step_satu', ['id_jenis_transaksi' => 2, 'm_nama_konsumen' => 'Marketplace test']);
check($r['status'] === 'error' && $before === $c->db->tables && !$c->db->open, 'failed marketplace SPK rolls back newly created consumer');
class TCashier {
    public $posted, $company;
    public function prosesTransaksi($post, $company, $user) { $this->posted = $post; $this->company = $company; return ['status' => 'error', 'message' => 'Injected decline']; }
}
$c = fixture(); $c->M_pembayaran = new TCashier();
$r = act($c, 'action_bayar', ['nospk' => 'SPK-A', 'grandtotal' => '1.000', 'metode' => 'cash', 'bayar_tunai' => '1.000', 'bayar_transfer' => '2.000', 'perusahaan_id' => 999, 'konsumen_id' => 999]);
check($c->M_pembayaran->company === 2 && $c->M_pembayaran->posted['konsumen_id'] === 10, 'cashier uses stored order branch and consumer');
check($c->M_pembayaran->posted['bayar_transfer'] === 0, 'stale payment fields excluded from chosen method');
check(!$c->audit && isset($c->session->flash['error']), 'declined payment does not log success');
$c = fixture(); $c->M_pembayaran = new TCashier(); $r = act($c, 'action_bayar', ['nospk' => 'SPK-A', 'grandtotal' => -1, 'metode' => 'cash']);
check($c->M_pembayaran->posted === null && isset($c->session->flash['error']), 'invalid cashier amount rejected before model');
$c = fixture(); $c->M_pembayaran = new TCashier(); $r = act($c, 'action_bayar', ['nospk' => 'SPK-C', 'grandtotal' => 100, 'metode' => 'cash']);
check($c->M_pembayaran->posted === null && isset($c->session->flash['error']), 'cashier foreign branch rejected');
$shift = ['id' => 1, 'kasir_id' => 7, 'perusahaan_id' => 2, 'tanggal_mulai' => '2026-10-10 09:00:00', 'tanggal_selesai' => '2026-10-11 10:00:00', 'status' => 'selesai', 'nama_karyawan' => 'Test kasir', 'foto' => null];
$c = fixture(); $c->db->queued = [[$shift], [['total_bayar' => 100]], [['pengeluaran_jumlah' => 20]], [['total_tagihan' => 200, 'total_bayar' => 50]]];
$r = $c->get_kasir_shift_detail(1);
check($r->total_pendapatan == 100 && $r->total_piutang == 150 && $r->saldo_kasir == 80, 'shift reports use actual payment and receivable fields');
check(strpos(implode(' ', $c->db->queries), 'k.karyawan_id = u.karyawan_id') !== false, 'shift joins use actual employee primary key');
$c = fixture(); $c->session->data['level'] = 17;
$c->db->queued = [[$shift, array_replace($shift, ['id' => 2, 'tanggal_mulai' => '2026-10-12 09:00:00'])], [['total_bayar' => 100]], [], [], [['total_bayar' => 200]], [], []];
$r = $c->get_kasir_shift_rekap(2, 'bulan');
check($r['per_periode'][0]->total_shift === 2 && $r['per_kasir'][0]->total_pendapatan == 300, 'shift recap sums isolated shifts without repeating aggregated totals');
check(invokePrivate($c, 'shift_report_branch', [3]) === 3, 'owner can select report branch');
$c->session->data['level'] = 3; check(invokePrivate($c, 'shift_report_branch', [3]) === 2, 'cashier report forced to active branch');
$c = fixture(); $c->db->tables['tbl_pemesanan'][] = ['nospk' => 'MAN-TEST', 'perusahaan_id' => 2];
$c->db->tables['tbl_piutang'][] = ['piutang_id' => 99, 'nospk' => 'MAN-TEST']; $c->db->tables['tbl_piutang_pembayaran'][] = ['piutang_id' => 99]; $before = $c->db->tables;
$r = act($c, 'manual_update', ['nospk' => 'MAN-TEST', 'konsumen_id' => 10, 'tanggal_transaksi' => '2026-10-10', 'metode_bayar' => 'piutang', 'products' => [['product_id' => 50, 'jumlah' => 1, 'harga_satuan' => 100]]]);
check($r['status'] === 'error' && $c->db->tables === $before && !$c->db->open, 'manual installment history protected');
class TManualSales {
    public function resolve_product_id($id) { return 50; }
    public function get_harga_id($id) { return 80; }
    public function get_konsumen_by_id($id) { return (object) ['termin' => 30]; }
}
function manualFixture() {
    $c = fixture(); $c->manual_sales = new TManualSales();
    $c->db->tables['tbl_pemesanan'][] = ['nospk' => 'MAN-TEST', 'perusahaan_id' => 2, 'konsumen_id' => 10, 'status_pembayaran' => 'pending'];
    $c->db->tables['tbl_pembayaran'][] = ['pembayaran_id' => 9, 'nospk' => 'MAN-TEST', 'perusahaan_id' => 2, 'total_bayar' => 0, 'total_tagihan' => 100];
    return $c;
}
$manualPost = ['nospk' => 'MAN-TEST', 'konsumen_id' => 10, 'tanggal_transaksi' => '2026-10-10', 'metode_bayar' => 'piutang', 'products' => [['product_id' => 50, 'jumlah' => 2, 'harga_satuan' => 500]]];
$c = manualFixture(); $r = act($c, 'manual_update', $manualPost);
check($r['status'] === 'success' && !$c->db->open, 'manual update commits');
check($c->db->tables['tbl_pembayaran'][3]['total_tagihan'] == 1000, 'manual total recomputed from numeric line items');
check($c->db->tables['tbl_piutang'][1]['jatuh_tempo'] === '2026-11-09', 'manual receivable deadline relative to chosen transaction date');
$c = manualFixture(); $before = $c->db->tables; $c->db->failTable = 'tbl_pembayaran_produk'; $r = act($c, 'manual_update', $manualPost);
check($r['status'] === 'error' && $before === $c->db->tables && !$c->db->open, 'manual database failure never reported as success');
$c = manualFixture(); $before = $c->db->tables; $c->db->throwTable = 'tbl_pemesanan_detail'; $r = act($c, 'manual_update', $manualPost);
check($r['status'] === 'error' && $before === $c->db->tables && !$c->db->open, 'manual Throwable rollback');
$c = fixture(); $r = act($c, 'action_add_temp', array_replace($line, ['nospk' => 'FOREIGN-DRAFT']));
check($r['status'] === 'error' && !$c->db->tables['tbl_temp_transaksi'], 'draft item cannot be appended to nonexistent SPK');
$c = fixture(); $r = act($c, 'action_add_temp_editspk', array_replace($line, ['nospk' => 'SPK-A']));
check($r['status'] === 'error' && !$c->db->tables['tbl_pemesanan_detail'], 'invoiced SPK cannot accept unaccounted extra items');
// Category 16 finishing follows the material's area/linear unit, not category exclusions.
$c = fixture();
foreach ([[1.2, 1.7, 2.04], [3.5, 1, 3.5], [1, 1, 1]] as $dimensions) {
    $inputs = ['kategori' => 16, 'qty' => 1, 'panjang' => $dimensions[0], 'lebar' => $dimensions[1], 'ket_1' => 0];
    $method = new ReflectionMethod(Transaksi::class, 'pricing_quantity'); $method->setAccessible(true);
    $quantity = $method->invokeArgs($c, [&$inputs, 2]);
    check(abs($quantity - $dimensions[2]) < 0.000001, 'Laminasi m2 uses length times width');
}
$inputs = ['kategori' => 16, 'qty' => 2, 'panjang' => 3.5, 'lebar' => 1, 'ket_1' => 0];
check($method->invokeArgs($c, [&$inputs, 3]) === 2.0, 'Finishing charged per piece does not use area dimensions');
check($method->invokeArgs($c, [&$inputs, 11]) === 7.0, 'Finishing linear meters use length times quantity');
$item = (object) ['metadata' => '{"unit_price":22000,"subtotal":44880}', 'snapshot_harga' => 99999, 'snapshot_subtotal' => 99999, 'harga_1' => 99999];
check(invokePrivate($c, 'resolve_detail_unit_price', [$item]) === 22000.0 && invokePrivate($c, 'resolve_detail_subtotal', [$item]) === 44880.0, 'Cashier prefers agreed metadata over changed master and legacy snapshots');
$item->metadata = null;
check(invokePrivate($c, 'resolve_detail_unit_price', [$item]) === 99999.0 && invokePrivate($c, 'resolve_detail_subtotal', [$item]) === 99999.0, 'Cashier supports dedicated historical snapshot columns');
echo "Transaksi controller: $checks checks passed.\n";
