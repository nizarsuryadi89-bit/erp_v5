<?php
declare(strict_types=1);

if (!defined('BASEPATH')) {
    define('BASEPATH', __DIR__ . '/system/');
}

require __DIR__ . '/application/config/database.php';

function endsWith(string $haystack, string $needle): bool
{
    if ($needle === '') {
        return true;
    }

    return substr($haystack, -strlen($needle)) === $needle;
}

function usage(): void
{
    echo "Usage:\n";
    echo "  /Applications/XAMPP/xamppfiles/bin/php backfill_pembayaran_produk_snapshot.php [--backup=/path/file.gz] [--cutoff=YYYY-MM-DD] [--apply] [--limit=100]\n";
    echo "\n";
    echo "Default mode is dry-run.\n";
}

function parseArgs(array $argv): array
{
    $options = [
        'backup' => '/Users/a990/Downloads/PARTIAL_BACKUP_20260504_204431.gz',
        'cutoff' => '2026-04-30',
        'apply' => false,
        'limit' => 0,
    ];

    foreach (array_slice($argv, 1) as $arg) {
        if ($arg === '--help' || $arg === '-h') {
            usage();
            exit(0);
        }
        if ($arg === '--apply') {
            $options['apply'] = true;
            continue;
        }
        if (strpos($arg, '--backup=') === 0) {
            $options['backup'] = substr($arg, 9);
            continue;
        }
        if (strpos($arg, '--cutoff=') === 0) {
            $options['cutoff'] = substr($arg, 9);
            continue;
        }
        if (strpos($arg, '--limit=') === 0) {
            $options['limit'] = max(0, (int) substr($arg, 8));
            continue;
        }
    }

    return $options;
}

function loadPriceMap(string $backupPath): array
{
    if (!is_file($backupPath)) {
        throw new RuntimeException("Backup file not found: {$backupPath}");
    }

    $isGzip = endsWith($backupPath, '.gz');
    $handle = $isGzip ? gzopen($backupPath, 'rb') : fopen($backupPath, 'rb');
    if ($handle === false) {
        throw new RuntimeException("Failed to open backup file: {$backupPath}");
    }

    $priceMap = [];
    $prefix = 'INSERT INTO `tbl_harga_product` ';

    while (true) {
        $line = $isGzip ? gzgets($handle) : fgets($handle);
        if ($line === false) {
            break;
        }

        $line = trim((string) $line);
        if ($line === '' || strpos($line, $prefix) !== 0) {
            continue;
        }

        $valuesPos = strpos($line, ' VALUES ');
        if ($valuesPos === false) {
            continue;
        }

        $valuesPart = rtrim(substr($line, $valuesPos + 8), ';');
        if (!preg_match('/^\((.*)\)$/', $valuesPart, $matches)) {
            continue;
        }

        $fields = str_getcsv($matches[1], ',', "'");
        if (count($fields) < 15) {
            continue;
        }

        $hargaId = (int) $fields[0];
        $priceMap[$hargaId] = [
            'harga_id' => $hargaId,
            'detail_product' => $fields[2],
            'hpp' => (float) $fields[5],
            'harga_1' => (float) $fields[6],
            'qty_min' => (float) $fields[7],
            'qty_max' => (float) $fields[8],
            'ket_1' => ($fields[10] === '' || strtoupper($fields[10]) === 'NULL') ? null : (string) $fields[10],
            'side_id' => ($fields[13] === '' || strtoupper($fields[13]) === 'NULL') ? null : (int) $fields[13],
            'jenisprint_id' => (int) $fields[14],
        ];
    }

    if ($isGzip) {
        gzclose($handle);
    } else {
        fclose($handle);
    }

    return $priceMap;
}

function fetchColumns(mysqli $db, string $table): array
{
    $result = $db->query("SHOW COLUMNS FROM `{$table}`");
    if (!$result) {
        throw new RuntimeException("Failed to inspect {$table}: " . $db->error);
    }

    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[$row['Field']] = true;
    }
    $result->free();

    return $columns;
}

function decodeMetadata(?string $metadata): array
{
    if (empty($metadata)) {
        return [];
    }

    $decoded = json_decode($metadata, true);
    return is_array($decoded) ? $decoded : [];
}

function calculateSubtotal(array $row, array $price): float
{
    $qty = (float) ($row['qty'] ?? 0);
    $panjang = (float) ($row['panjang'] ?? 0);
    $lebar = (float) ($row['lebar'] ?? 0);
    $harga = (float) ($price['harga_1'] ?? 0);
    $satuanId = (int) ($row['satuan_id'] ?? 0);
    $kategoriId = (int) ($row['kategori_id'] ?? 0);
    $ket1 = (string) ($price['ket_1'] ?? '');

    if ($satuanId === 11) {
        return $qty * $panjang * $harga;
    }
    if ($kategoriId === 9 && $ket1 === '5') {
        return $qty * $panjang * $harga;
    }
    if ($panjang != 0 && $lebar != 0) {
        return $qty * $panjang * $lebar * $harga;
    }
    if ($panjang != 0 && $lebar == 0) {
        return $qty * $panjang * $harga;
    }

    return $qty * $harga;
}

$options = parseArgs($argv);

$dbConn = @new mysqli(
    $db['default']['hostname'],
    $db['default']['username'],
    $db['default']['password'],
    $db['default']['database']
);

if ($dbConn->connect_error) {
    fwrite(STDERR, "DB connection failed: {$dbConn->connect_error}\n");
    exit(1);
}

$dbConn->set_charset('utf8mb4');

try {
    $priceMap = loadPriceMap($options['backup']);
    $columns = fetchColumns($dbConn, 'tbl_pembayaran_produk');
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}

$sql = "
    SELECT
        pp.id,
        pp.nospk,
        pp.product_id,
        pp.harga_id,
        pp.qty,
        pp.panjang,
        pp.lebar,
        pp.metadata,
        " . (isset($columns['snapshot_harga']) ? "pp.snapshot_harga," : "NULL AS snapshot_harga,") . "
        " . (isset($columns['snapshot_hpp']) ? "pp.snapshot_hpp," : "NULL AS snapshot_hpp,") . "
        " . (isset($columns['snapshot_detail_product']) ? "pp.snapshot_detail_product," : "NULL AS snapshot_detail_product,") . "
        " . (isset($columns['snapshot_qty_min']) ? "pp.snapshot_qty_min," : "NULL AS snapshot_qty_min,") . "
        " . (isset($columns['snapshot_qty_max']) ? "pp.snapshot_qty_max," : "NULL AS snapshot_qty_max,") . "
        " . (isset($columns['snapshot_ket_1']) ? "pp.snapshot_ket_1," : "NULL AS snapshot_ket_1,") . "
        " . (isset($columns['snapshot_side_id']) ? "pp.snapshot_side_id," : "NULL AS snapshot_side_id,") . "
        " . (isset($columns['snapshot_jenisprint_id']) ? "pp.snapshot_jenisprint_id," : "NULL AS snapshot_jenisprint_id,") . "
        " . (isset($columns['snapshot_subtotal']) ? "pp.snapshot_subtotal," : "NULL AS snapshot_subtotal,") . "
        " . (isset($columns['snapshot_source_harga_id']) ? "pp.snapshot_source_harga_id," : "NULL AS snapshot_source_harga_id,") . "
        p.kategori_id,
        p.satuan_id,
        pb.tgl_bayar
    FROM tbl_pembayaran_produk pp
    INNER JOIN tbl_pembayaran pb ON pb.nospk = pp.nospk
    INNER JOIN tbl_product p ON p.product_id = pp.product_id
    WHERE pb.tgl_bayar <= ?
    ORDER BY pb.tgl_bayar ASC, pp.id ASC
";

if ($options['limit'] > 0) {
    $sql .= " LIMIT " . (int) $options['limit'];
}

$stmt = $dbConn->prepare($sql);
if (!$stmt) {
    fwrite(STDERR, "Failed to prepare select: {$dbConn->error}\n");
    exit(1);
}
$stmt->bind_param('s', $options['cutoff']);
$stmt->execute();
$result = $stmt->get_result();

$rowsToUpdate = [];
$missingRows = [];
$summary = [
    'checked' => 0,
    'matched' => 0,
    'to_update' => 0,
    'missing' => 0,
    'same' => 0,
];

while ($row = $result->fetch_assoc()) {
    $summary['checked']++;
    $hargaId = (int) $row['harga_id'];
    if (!isset($priceMap[$hargaId])) {
        $summary['missing']++;
        if (count($missingRows) < 10) {
            $missingRows[] = $row;
        }
        continue;
    }

    $summary['matched']++;
    $old = $priceMap[$hargaId];
    $subtotal = round(calculateSubtotal($row, $old), 2);
    $metadata = decodeMetadata($row['metadata']);
    $metadata['unit_price'] = (float) $old['harga_1'];
    $metadata['subtotal'] = $subtotal;
    $metadata['old_price_restored_from_backup'] = basename($options['backup']);
    $metadata['old_price_cutoff'] = $options['cutoff'] . ' 23:59:59';
    $metadata['price_snapshot_at'] = $metadata['price_snapshot_at'] ?? ($options['cutoff'] . ' 23:59:59');
    $metadataJson = json_encode($metadata, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $needsUpdate = false;
    $existingMeta = !empty($row['metadata']) ? json_encode(decodeMetadata($row['metadata']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null;
    if ($existingMeta !== $metadataJson) {
        $needsUpdate = true;
    }

    $payload = [
        'id' => (int) $row['id'],
        'nospk' => $row['nospk'],
        'harga_id' => $hargaId,
        'metadata' => $metadataJson,
        'snapshot_harga' => (float) $old['harga_1'],
        'snapshot_hpp' => (float) $old['hpp'],
        'snapshot_detail_product' => $old['detail_product'],
        'snapshot_qty_min' => (float) $old['qty_min'],
        'snapshot_qty_max' => (float) $old['qty_max'],
        'snapshot_ket_1' => $old['ket_1'],
        'snapshot_side_id' => $old['side_id'],
        'snapshot_jenisprint_id' => (int) $old['jenisprint_id'],
        'snapshot_subtotal' => $subtotal,
        'snapshot_source_harga_id' => $hargaId,
        'snapshot_valid_from' => null,
        'snapshot_valid_to' => null,
        'tgl_bayar' => $row['tgl_bayar'],
    ];

    foreach ([
        'snapshot_harga',
        'snapshot_hpp',
        'snapshot_detail_product',
        'snapshot_qty_min',
        'snapshot_qty_max',
        'snapshot_ket_1',
        'snapshot_side_id',
        'snapshot_jenisprint_id',
        'snapshot_subtotal',
        'snapshot_source_harga_id',
    ] as $field) {
        if (!isset($columns[$field])) {
            continue;
        }

        $currentValue = $row[$field] ?? null;
        $expectedValue = $payload[$field];

        if (is_numeric($currentValue) && is_numeric($expectedValue)) {
            $isDifferent = ((float) $currentValue !== (float) $expectedValue);
        } else {
            $isDifferent = ((string) $currentValue !== (string) $expectedValue);
        }

        if ($isDifferent) {
            $needsUpdate = true;
        }
    }

    if (!$needsUpdate) {
        $summary['same']++;
        continue;
    }

    $summary['to_update']++;
    $rowsToUpdate[] = $payload;
}

$result->free();
$stmt->close();

echo "Backfill pembayaran snapshot\n";
echo "Mode      : " . ($options['apply'] ? 'APPLY' : 'DRY-RUN') . "\n";
echo "Backup    : {$options['backup']}\n";
echo "Cutoff    : {$options['cutoff']}\n";
echo "Checked   : {$summary['checked']}\n";
echo "Matched   : {$summary['matched']}\n";
echo "To update : {$summary['to_update']}\n";
echo "Missing   : {$summary['missing']}\n";
echo "Same      : {$summary['same']}\n";

if (!empty($rowsToUpdate)) {
    echo "\nSample rows:\n";
    foreach (array_slice($rowsToUpdate, 0, 10) as $sample) {
        echo sprintf(
            "- id=%d nospk=%s tgl=%s harga_id=%d snapshot_harga=%.0f subtotal=%.0f\n",
            $sample['id'],
            $sample['nospk'],
            $sample['tgl_bayar'],
            $sample['harga_id'],
            $sample['snapshot_harga'],
            $sample['snapshot_subtotal']
        );
    }
}

if (!empty($missingRows)) {
    echo "\nMissing rows:\n";
    foreach ($missingRows as $missing) {
        echo sprintf(
            "- id=%d nospk=%s tgl=%s harga_id=%d\n",
            (int) $missing['id'],
            $missing['nospk'],
            $missing['tgl_bayar'],
            (int) $missing['harga_id']
        );
    }
}

if (!$options['apply']) {
    echo "\nDry-run complete. Re-run with --apply to write changes.\n";
    exit(0);
}

if (empty($rowsToUpdate)) {
    echo "\nNothing to update.\n";
    exit(0);
}

$setParts = ['metadata = ?'];
$bindTypes = 's';
$bindFields = ['metadata'];

foreach ([
    'snapshot_harga' => 'd',
    'snapshot_hpp' => 'd',
    'snapshot_detail_product' => 's',
    'snapshot_qty_min' => 'd',
    'snapshot_qty_max' => 'd',
    'snapshot_ket_1' => 's',
    'snapshot_side_id' => 'i',
    'snapshot_jenisprint_id' => 'i',
    'snapshot_subtotal' => 'd',
    'snapshot_source_harga_id' => 'i',
    'snapshot_valid_from' => 's',
    'snapshot_valid_to' => 's',
] as $field => $type) {
    if (isset($columns[$field])) {
        $setParts[] = "{$field} = ?";
        $bindTypes .= $type;
        $bindFields[] = $field;
    }
}

$bindTypes .= 'i';
$updateSql = "UPDATE tbl_pembayaran_produk SET " . implode(', ', $setParts) . " WHERE id = ?";

$dbConn->begin_transaction();

try {
    $updateStmt = $dbConn->prepare($updateSql);
    if (!$updateStmt) {
        throw new RuntimeException("Failed to prepare update: {$dbConn->error}");
    }

    $updated = 0;
    foreach ($rowsToUpdate as $row) {
        $params = [];
        $params[] = &$bindTypes;
        foreach ($bindFields as $field) {
            $params[] = &$row[$field];
        }
        $params[] = &$row['id'];

        call_user_func_array([$updateStmt, 'bind_param'], $params);
        if (!$updateStmt->execute()) {
            throw new RuntimeException("Failed updating ID {$row['id']}: {$updateStmt->error}");
        }
        $updated++;
    }

    $updateStmt->close();
    $dbConn->commit();
    echo "\nApply complete. Updated {$updated} rows.\n";
} catch (Throwable $e) {
    $dbConn->rollback();
    fwrite(STDERR, "\nApply failed: {$e->getMessage()}\n");
    exit(1);
}
