<?php
// Offline variant regressions; never reads or writes the ERP database.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('BASEPATH', __DIR__);
require __DIR__ . '/../libraries/Production_bom.php';
error_reporting(E_ALL);
set_error_handler(function ($s, $m, $f, $l) { throw new ErrorException($m, 0, $s, $f, $l); });
$checks = 0;
function check($ok, $name) { global $checks; $checks++; if (!$ok) throw new RuntimeException($name); }
function rejected($fn, $name) { try { $fn(); } catch (DomainException $e) { check(true, $name); return; } check(false, $name); }
function material($id, $family, $name) { return ['material_id' => $id, 'bahan_id' => $family, 'varian' => $name, 'konversi' => 1, 'satuan_id' => 3, 'satuan_nama' => 'Pcs']; }
function mapping($id, $key, $value, $mid) { return ['id' => $id, 'attribute_key' => $key, 'attribute_value' => $value, 'bahan_id' => $mid, 'calc_type' => 'fixed', 'divider_factor' => 1, 'scrap_factor' => 0]; }
function bom($id, $mid, $price = null) { return ['bom_line_id' => $id, 'bahan_id' => $mid, 'harga_id' => $price, 'quantity' => 1, 'calc_type' => 'fixed']; }
function product($key, $options) { return ['form_schema' => json_encode([['name' => $key, 'options' => $options]])]; }
function item($meta, $price = 0) { return (object)['product_id' => 1, 'harga_id' => $price, 'qty' => 50, 'panjang' => 1.2, 'lebar' => 1.7, 'metadata' => json_encode($meta)]; }
$options = [1 => 'Merah', 2 => 'Biru', 3 => 'Hijau', 4 => 'Orange', 5 => 'Hitam', 6 => 'Ungu'];
$materials = []; $maps = [];
foreach ([359 => 'Merah', 361 => 'Biru', 363 => 'Hijau', 362 => 'Orange', 358 => 'Hitam', 360 => 'Ungu'] as $id => $color) {
    $materials[$id] = material($id, 220, 'Pulpen Gepeng ' . $color);
    $maps[] = mapping($id, 'ket_1', $color, 220); // Broken historical parent-ID mappings.
}
$materials[900] = material(900, 901, 'Kemasan');
foreach ($options as $code => $color) foreach ([(string)$code, $color] as $value) {
    $lines = Production_bom::select(item(['ket_1' => $value]), product('ket_1', $options), [], [bom(1, 358), bom(2, 900)], $maps, $materials);
    check(count($lines) === 2, 'one color plus common packaging');
    check($lines[0]->bahan_nama === 'Pulpen Gepeng ' . $color, 'exact selected color ' . $value);
    check($lines[0]->bom_material_id !== 220 && $lines[0]->bahan_id === 220, 'material ID differs from parent');
    check(Production_bom::usage($lines[0], item([])) === 50.0, 'one pen per ordered piece');
}
rejected(function () use ($options, $maps, $materials) { Production_bom::select(item(['ket_1' => '0']), product('ket_1', $options), [], [], $maps, $materials); }, 'numeric zero must not match all colors on PHP 7');
rejected(function () use ($options, $maps, $materials) { Production_bom::select(item([]), product('ket_1', $options), [], [], $maps, $materials); }, 'missing color must not consume default black');
$ambiguous = $materials; $ambiguous[999] = material(999, 220, 'Duplikat Biru');
rejected(function () use ($options, $maps, $ambiguous) { Production_bom::select(item(['ket_1' => 2]), product('ket_1', $options), [], [], $maps, $ambiguous); }, 'ambiguous parent rescue refused');
foreach (['jenisprint_id', 'warna_id'] as $key) foreach ([1 => 'Hitam', 2 => 'Putih'] as $code => $color) {
    $ms = [131 => material(131, 144, 'Tumbler Hitam'), 132 => material(132, 144, 'Tumbler Putih')];
    $dm = [mapping(1, $key, 'Hitam', 131), mapping(2, $key, 'Putih', 132)];
    $lines = Production_bom::select(item([$key => (string)$code]), product($key, [1 => 'Hitam', 2 => 'Putih']), [], [], $dm, $ms);
    check(count($lines) === 1 && $lines[0]->bom_material_id === ($code === 1 ? 131 : 132), 'tumbler ID translated to color');
    $lines = Production_bom::select(item([] , 10), product($key, [1 => 'Hitam', 2 => 'Putih']), [['harga_id' => 10, 'metadata' => json_encode([preg_replace('/_id$/', '', $key) => $color])]], [], $dm, $ms);
    check(count($lines) === 1, 'price metadata fallback');
}
$sizes = [1 => 'S', 2 => 'M', 3 => 'L', 4 => 'XL', 5 => '2XL', 6 => '3XL', 7 => '4XL', 8 => '5XL'];
$ms = []; $dm = [];
foreach ($sizes as $code => $size) { $ms[1150 + $code] = material(1150 + $code, 127, 'Kaos Hitam ' . $size); $dm[] = mapping($code, 'ukuran', $size, 1150 + $code); }
foreach ($sizes as $code => $size) foreach ([['ukuran' => $size], ['ukuran' => (string)$code], ['ket_1' => (string)$code], ['ket_1' => $size]] as $meta) {
    $lines = Production_bom::select(item($meta, 99999), product('ukuran', $sizes), [], [], $dm, $ms);
    check(count($lines) === 1 && $lines[0]->bom_material_id === 1150 + $code, 'shirt size selected even with deleted harga_id');
}
$lines = Production_bom::select(item(['ket_1' => 4], 10), product('ukuran', $sizes), [['harga_id' => 10, 'metadata' => '{"ukuran":"S"}']], [], $dm, $ms);
check($lines[0]->bom_material_id === 1154, 'order size overrides old price size');
$ms = [1 => material(1, 11, 'Kertas'), 2 => material(2, 12, 'Bahan lain')];
$prices = [['harga_id' => 10, 'metadata' => '{"ukuran":"L","qty_min":1}'], ['harga_id' => 11, 'metadata' => '{"ukuran":"L","qty_min":50}'], ['harga_id' => 12, 'metadata' => '{"ukuran":"L","qty_min":100}']];
$lines = Production_bom::select(item(['ukuran' => 'L'], 10), product('ukuran', $sizes), $prices, [bom(1, 1), bom(2, 2, 11), bom(3, 2, 12)], [], $ms);
check(count($lines) === 1 && $lines[0]->bom_line_id === 2, 'single sibling variant BOM overrides global');
$lines = Production_bom::select(item([], 0), [], [], [bom(1, 1), bom(2, 2, 11)], [], $ms);
check(count($lines) === 1 && $lines[0]->bom_line_id === 1, 'unknown price never consumes all variants');
$r = mapping(1, 'warna', 'Hitam', 1); $r['calc_type'] = 'area'; $r['multiplier_key'] = '2'; $r['divider_factor'] = 4; $r['scrap_factor'] = 10;
$lines = Production_bom::select(item(['warna' => 'Hitam']), [], [], [], [$r], $ms);
check(abs(Production_bom::usage($lines[0], item([])) - 56.1) < 0.00001, 'area multiplier divider scrap applied once');
$r['calc_type'] = 'linear_meter';
$lines = Production_bom::select(item(['warna' => 'Hitam']), [], [], [], [$r], $ms);
check(abs(Production_bom::usage($lines[0], item([])) - 33) < 0.00001, 'linear meter calculation');
$r['divider_factor'] = 0;
rejected(function () use ($r, $ms) { Production_bom::select(item(['warna' => 'Hitam']), [], [], [], [$r], $ms); }, 'zero divider rejected');
$r['divider_factor'] = 1; $r['multiplier_key'] = 'missing';
rejected(function () use ($r, $ms) { Production_bom::select(item(['warna' => 'Hitam']), [], [], [], [$r], $ms); }, 'missing multiplier rejected');
$shirt = product('ukuran', [1 => 'S']); $shirt['product_nama'] = 'Kaos Katun 24s Dewasa [Hitam]';
$shirtMaterial = [1151 => material(1151, 127, 'Kaos Hitam S')];
rejected(function () use ($shirt, $shirtMaterial) { Production_bom::select(item(['ket_1' => 1, 'jenisprint' => 'Putih']), $shirt, [], [], [mapping(1, 'ukuran', 'S', 1151)], $shirtMaterial); }, 'historical shirt color conflict never guesses stock');
$shirt['product_nama'] = 'Kaos Katun 24s Dewasa [Putih]';
rejected(function () use ($shirt, $shirtMaterial) { Production_bom::select(item(['ket_1' => 1]), $shirt, [], [], [mapping(1, 'ukuran', 'S', 1151)], $shirtMaterial); }, 'wrong-color shirt master mapping rejected');
class CI_Model { public $db; }
require __DIR__ . '/../models/M_pembayaran.php';
$payment = new M_pembayaran; $payment->db = new class { public function field_exists($f, $t) { return false; } };
$snapshotMethod = new ReflectionMethod(M_pembayaran::class, 'buildPembayaranProdukSnapshot'); $snapshotMethod->setAccessible(true);
rejected(function () use ($payment, $snapshotMethod) { $snapshotMethod->invoke($payment, (object)['harga_id' => 99999, 'metadata' => '{}']); }, 'deleted price without saved price must not become zero payment');
$snapshot = $snapshotMethod->invoke($payment, (object)['harga_id' => 99999, 'metadata' => '{"unit_price":45000,"subtotal":90000}']);
check((float)json_decode($snapshot['metadata'], true)['subtotal'] === 90000.0, 'deleted price with saved snapshot preserves payment amount');
echo "Production BOM offline: $checks checks passed.\n";
