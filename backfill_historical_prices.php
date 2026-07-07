<?php
declare(strict_types=1);

if (!defined('BASEPATH')) {
    define('BASEPATH', __DIR__ . '/system/');
}

require __DIR__ . '/application/config/database.php';

function usage(): void
{
    echo "Usage:\n";
    echo "  /Applications/XAMPP/xamppfiles/bin/php backfill_historical_prices.php [--backup=/path/file.gz] [--cutoff=YYYY-MM-DD HH:MM:SS] [--apply] [--limit=100]\n";
    echo "\n";
    echo "Default mode is dry-run.\n";
}

function parseArgs(array $argv): array
{
    $options = [
        'backup' => '/Users/a990/Downloads/PARTIAL_BACKUP_20260504_204431.gz',
        'cutoff' => '2026-04-30 23:59:59',
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

    $handle = gzopen($backupPath, 'rb');
    if ($handle === false) {
        throw new RuntimeException("Failed to open gzip file: {$backupPath}");
    }

    $priceMap = [];
    $prefix = 'INSERT INTO `tbl_harga_product` ';

    while (!gzeof($handle)) {
        $line = trim((string) gzgets($handle));
        if ($line === '' || strpos($line, $prefix) !== 0) {
            continue;
        }

        $valuesPos = strpos($line, ' VALUES ');
        if ($valuesPos === false) {
            continue;
        }

        $valuesPart = substr($line, $valuesPos + 8);
        $valuesPart = rtrim($valuesPart, ';');

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
            'product_id' => (int) $fields[1],
            'detail_product' => $fields[2],
            'hpp' => (float) $fields[5],
            'harga_1' => (float) $fields[6],
            'qty_min' => (float) $fields[7],
            'qty_max' => (float) $fields[8],
            'harga_aktif' => (string) $fields[9],
            'ket_1' => ($fields[10] === '' || strtoupper($fields[10]) === 'NULL') ? null : (string) $fields[10],
            'side_id' => ($fields[13] === '' || strtoupper($fields[13]) === 'NULL') ? null : (int) $fields[13],
            'jenisprint_id' => (int) $fields[14],
        ];
    }

    gzclose($handle);

    return $priceMap;
}

function decodeMetadata(?string $metadata): array
{
    if (empty($metadata)) {
        return [];
    }

    $decoded = json_decode($metadata, true);
    return is_array($decoded) ? $decoded : [];
}

function calculateSubtotal(array $detail, array $oldPrice): float
{
    $qty = (float) ($detail['qty'] ?? 0);
    $panjang = (float) ($detail['panjang'] ?? 0);
    $lebar = (float) ($detail['lebar'] ?? 0);
    $harga = (float) ($oldPrice['harga_1'] ?? 0);
    $satuanId = (int) ($detail['satuan_id'] ?? 0);
    $kategoriId = (int) ($detail['kategori_id'] ?? 0);
    $ket1 = (string) ($oldPrice['ket_1'] ?? '');

    if ($satuanId === 11) {
        return $qty * $panjang * $harga;
    }

    if ($kategoriId === 9 && $ket1 === '5') {
        return $qty * $panjang * $harga;
    }

    if ($panjang != 0.0 && $lebar != 0.0) {
        return $qty * $panjang * $lebar * $harga;
    }

    if ($panjang != 0.0 && $lebar == 0.0) {
        return $qty * $panjang * $harga;
    }

    return $qty * $harga;
}

function fetchColumns(mysqli $db, string $table): array
{
    $result = $db->query("SHOW COLUMNS FROM `{$table}`");
    if (!$result) {
        throw new RuntimeException("Failed to inspect columns for {$table}: " . $db->error);
    }

    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[$row['Field']] = true;
    }
    $result->free();

    return $columns;
}

$options = parseArgs($argv);

$conn = @new mysqli(
    $db['default']['hostname'],
    $db['default']['username'],
    $db['default']['password'],
    $db['default']['database']
);

if ($conn->connect_error) {
    fwrite(STDERR, "DB connection failed: {$conn->connect_error}\n");
    exit(1);
}

$conn->set_charset('utf8mb4');

try {
    $priceMap = loadPriceMap($options['backup']);
    $columns = fetchColumns($conn, 'tbl_pemesanan_detail');
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}

$sql = "
    SELECT
        pd.id,
        pd.nospk,
        pd.product_id,
        pd.harga_id,
        pd.qty,
        pd.panjang,
        pd.lebar,
        pd.metadata,
        " . (isset($columns['unit_price']) ? "pd.unit_price," : "NULL AS unit_price,") . "
        " . (isset($columns['subtotal']) ? "pd.subtotal," : "NULL AS subtotal,") . "
        pm.created_at,
        p.kategori_id,
        p.satuan_id
    FROM tbl_pemesanan_detail pd
    INNER JOIN tbl_pemesanan pm ON pm.nospk = pd.nospk
    INNER JOIN tbl_product p ON p.product_id = pd.product_id
    WHERE pm.created_at <= ?
    ORDER BY pm.created_at ASC, pd.id ASC
";

if ($options['limit'] > 0) {
    $sql .= " LIMIT " . (int) $options['limit'];
}

$stmt = $conn->prepare($sql);
if (!$stmt) {
    fwrite(STDERR, "Failed to prepare select: {$conn->error}\n");
    exit(1);
}

$stmt->bind_param('s', $options['cutoff']);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
$missingRows = [];
$summary = [
    'checked' => 0,
    'matched_old_price' => 0,
    'ready_to_update' => 0,
    'missing_old_price' => 0,
    'already_same' => 0,
];

while ($row = $result->fetch_assoc()) {
    $summary['checked']++;

    $hargaId = (int) $row['harga_id'];
    if (!isset($priceMap[$hargaId])) {
        $summary['missing_old_price']++;
        if (count($missingRows) < 10) {
            $missingRows[] = [
                'id' => (int) $row['id'],
                'nospk' => $row['nospk'],
                'harga_id' => $hargaId,
                'created_at' => $row['created_at'],
            ];
        }
        continue;
    }

    $summary['matched_old_price']++;
    $oldPrice = $priceMap[$hargaId];
    $unitPrice = (float) $oldPrice['harga_1'];
    $subtotal = round(calculateSubtotal($row, $oldPrice));

    $metadata = decodeMetadata($row['metadata']);
    $metadata['unit_price'] = $unitPrice;
    $metadata['subtotal'] = $subtotal;
    $metadata['old_price_restored_from_backup'] = basename($options['backup']);
    $metadata['old_price_cutoff'] = $options['cutoff'];
    $metadata['price_snapshot_at'] = $metadata['price_snapshot_at'] ?? $options['cutoff'];

    $newMetadataJson = json_encode($metadata, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $currentMetadataJson = !empty($row['metadata']) ? json_encode(decodeMetadata($row['metadata']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null;
    $currentUnitPrice = isset($row['unit_price']) && $row['unit_price'] !== null ? (float) $row['unit_price'] : null;
    $currentSubtotal = isset($row['subtotal']) && $row['subtotal'] !== null ? (float) $row['subtotal'] : null;

    $needsUpdate = $newMetadataJson !== $currentMetadataJson;
    if (isset($columns['unit_price']) && $currentUnitPrice !== $unitPrice) {
        $needsUpdate = true;
    }
    if (isset($columns['subtotal']) && $currentSubtotal !== $subtotal) {
        $needsUpdate = true;
    }

    if (!$needsUpdate) {
        $summary['already_same']++;
        continue;
    }

    $summary['ready_to_update']++;
    $rows[] = [
        'id' => (int) $row['id'],
        'nospk' => $row['nospk'],
        'harga_id' => $hargaId,
        'created_at' => $row['created_at'],
        'unit_price' => $unitPrice,
        'subtotal' => $subtotal,
        'metadata_json' => $newMetadataJson,
    ];
}

$result->free();
$stmt->close();

echo "Backfill historical prices\n";
echo "Mode      : " . ($options['apply'] ? 'APPLY' : 'DRY-RUN') . "\n";
echo "Backup    : {$options['backup']}\n";
echo "Cutoff    : {$options['cutoff']}\n";
echo "Checked   : {$summary['checked']}\n";
echo "Matched   : {$summary['matched_old_price']}\n";
echo "To update : {$summary['ready_to_update']}\n";
echo "Missing   : {$summary['missing_old_price']}\n";
echo "Same      : {$summary['already_same']}\n";

if (!empty($rows)) {
    echo "\nSample rows:\n";
    foreach (array_slice($rows, 0, 10) as $sample) {
        echo sprintf(
            "- id=%d nospk=%s tanggal=%s harga_id=%d unit_price=%.0f subtotal=%.0f\n",
            $sample['id'],
            $sample['nospk'],
            $sample['created_at'],
            $sample['harga_id'],
            $sample['unit_price'],
            $sample['subtotal']
        );
    }
}

if (!empty($missingRows)) {
    echo "\nMissing price rows:\n";
    foreach ($missingRows as $missing) {
        echo sprintf(
            "- id=%d nospk=%s tanggal=%s harga_id=%d\n",
            $missing['id'],
            $missing['nospk'],
            $missing['created_at'],
            $missing['harga_id']
        );
    }
}

if (!$options['apply']) {
    echo "\nDry-run complete. Re-run with --apply to write changes.\n";
    exit(0);
}

if (empty($rows)) {
    echo "\nNothing to update.\n";
    exit(0);
}

$conn->begin_transaction();

try {
    $setClauses = ['metadata = ?'];
    if (isset($columns['unit_price'])) {
        $setClauses[] = 'unit_price = ?';
    }
    if (isset($columns['subtotal'])) {
        $setClauses[] = 'subtotal = ?';
    }

    $updateSql = "UPDATE tbl_pemesanan_detail SET " . implode(', ', $setClauses) . " WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    if (!$updateStmt) {
        throw new RuntimeException("Failed to prepare update: {$conn->error}");
    }

    $updated = 0;
    foreach ($rows as $row) {
        if (isset($columns['unit_price']) && isset($columns['subtotal'])) {
            $updateStmt->bind_param('sdii', $row['metadata_json'], $row['unit_price'], $row['subtotal'], $row['id']);
        } elseif (isset($columns['unit_price'])) {
            $updateStmt->bind_param('sdi', $row['metadata_json'], $row['unit_price'], $row['id']);
        } elseif (isset($columns['subtotal'])) {
            $updateStmt->bind_param('sii', $row['metadata_json'], $row['subtotal'], $row['id']);
        } else {
            $updateStmt->bind_param('si', $row['metadata_json'], $row['id']);
        }

        if (!$updateStmt->execute()) {
            throw new RuntimeException("Failed updating detail ID {$row['id']}: {$updateStmt->error}");
        }
        $updated++;
    }

    $updateStmt->close();
    $conn->commit();
    echo "\nApply complete. Updated {$updated} rows.\n";
} catch (Throwable $e) {
    $conn->rollback();
    fwrite(STDERR, "\nApply failed: {$e->getMessage()}\n");
    exit(1);
}
