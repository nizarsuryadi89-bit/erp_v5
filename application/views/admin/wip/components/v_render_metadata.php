<?php
/**
 * Component: Metadata Renderer
 * Description: Renders dynamic product specifications based on Form Schema & JSON Metadata
 * Used In: v_table_item_view.php, v_table_spk_view.php
 */

$meta = !empty($item->metadata) ? json_decode($item->metadata, true) : [];

// [NEW] Backward Compatibility: Inject old flat columns into the $meta array
if (empty($meta)) {
    if (isset($item->ket_1) && $item->ket_1 !== '') $meta['ket_1'] = $item->ket_1;
    if (isset($item->jenisprint_id) && $item->jenisprint_id !== '') $meta['jenisprint_id'] = $item->jenisprint_id;
    if (isset($item->side_id) && $item->side_id !== '') $meta['side_id'] = $item->side_id;
    if (isset($item->keterangan) && $item->keterangan !== '') $meta['keterangan'] = $item->keterangan;
    
}

$schema = !empty($item->merged_schema_assoc) ? array_values($item->merged_schema_assoc) : ($category_schemas[$item->kategori_id] ?? []);

// Control flags
$renderBadges = !isset($only_instructions) || $only_instructions === false;
$renderInstructions = !isset($only_badges) || $only_badges === false;

// Standard fields to exclude
$excludeColumns = ['panjang', 'lebar', 'qty', 'catatan', 'keterangan', 'lock_qty', 'persen_toleransi'];

// --- PART 1: BADGES (Specs) ---
if ($renderBadges && !empty($schema) && !empty($meta)) {
    echo '<div class="d-flex flex-wrap gap-1 mt-1">';

    $uniform_badge_style = "padding: 4px 10px; font-size: 0.65rem; border-radius: 6px; font-weight: 700; display: inline-flex; align-items: center; justify-content: center;";

    foreach ($schema as $field) {
        $key = $field['name'];
        if (in_array($key, $excludeColumns))
            continue;

        // Check if value exists in meta
        if (!isset($meta[$key]) || $meta[$key] === '' || $meta[$key] === '0' || $meta[$key] === '-') {
            // Check fallback with _id
            if (isset($meta[$key . '_id']) && $meta[$key . '_id'] !== '' && $meta[$key . '_id'] !== '0') {
                $meta[$key] = $meta[$key . '_id'];
            } else {
                continue;
            }
        }
        
        $label = $field['label'] ?? $key;
        $val = $meta[$key];
        if (isset($field['options']) && is_array($field['options']) && isset($field['options'][$val])) {
            $val = $field['options'][$val];
        }

        // [NEW] Dynamically choose icon and color based on key
        $icon = 'fa-tag';
        $bg = 'bg-light';
        $txt = 'text-dark';
        
        $keyLower = strtolower($key);
        $labelLower = strtolower($label);
        
        if (strpos($keyLower, 'mesin') !== false || strpos($keyLower, 'jenisprint') !== false) {
            $icon = 'fa-print';
            $bg = 'bg-primary bg-opacity-10';
            $txt = 'text-primary border-primary border-opacity-25';
        } elseif (strpos($keyLower, 'warna') !== false) {
            $icon = 'fa-palette';
            $bg = 'bg-info bg-opacity-10';
            $txt = 'text-info border-info border-opacity-25';
        } elseif (strpos($keyLower, 'material') !== false || strpos($keyLower, 'bahan') !== false) {
            $icon = 'fa-layer-group';
            $bg = 'bg-dark';
            $txt = 'text-white border-secondary shadow-sm';
        } elseif (strpos($keyLower, 'ukuran') !== false || strpos($keyLower, 'bidang') !== false || strpos($keyLower, 'tebal') !== false || strpos($keyLower, 'thickness') !== false) {
            $icon = 'fa-ruler-combined';
            $bg = 'bg-dark text-white border-secondary shadow-sm';
            $txt = 'text-white';
        } elseif (strpos($keyLower, 'model') !== false || strpos($keyLower, 'varian') !== false) {
            $icon = 'fa-cubes';
            $bg = 'bg-primary';
            $txt = 'text-white border-primary shadow-sm';
        } elseif (strpos($keyLower, 'sisi') !== false || strpos($keyLower, 'side') !== false) {
            $icon = 'fa-copy';
            $bg = 'bg-secondary bg-opacity-10';
            $txt = 'text-secondary border-secondary border-opacity-25';
        }
        
        $isFinishing = false;
        $keywords = ['finish', 'mata ayam', 'mataayam', 'seaming', 'selongsong', 'lebihan', 'laminasi', 'potong'];
        foreach ($keywords as $kw) {
            if (strpos($keyLower . ' ' . $labelLower, $kw) !== false) {
                $isFinishing = true;
                break;
            }
        }
        
        if ($isFinishing) {
            echo "<span class='badge bg-warning text-dark border border-warning shadow-sm' style='{$uniform_badge_style}'>";
            echo "<i class='fas fa-tools me-1 opacity-75'></i> <span class='opacity-75 me-1'>" . strtoupper($label) . ":</span>";
            echo "<span class='text-uppercase'>" . $val . "</span>";
            echo "</span>";
        } else {
            echo "<span class='badge {$bg} {$txt} border' style='{$uniform_badge_style}'>";
            echo "<i class='fas {$icon} me-1 opacity-75'></i> <span class='opacity-75 me-1 text-uppercase'>" . $label . ":</span>";
            echo "<span class='text-uppercase'>" . $val . "</span>";
            echo "</span>";
        }
    }
    echo '</div>';
}

// --- PART 2: INSTRUCTIONS ---
if ($renderInstructions && !empty($meta['keterangan'])) {
    echo '<div class="mt-2 p-2 bg-light rounded-3 border border-dashed" style="font-size: 0.75rem;">';
    echo '<div class="fw-900 text-muted small mb-1 ls-1"><i class="fas fa-exclamation-circle me-1 text-warning"></i> INSTRUKSI KHUSUS:</div>';
    echo '<div class="fw-bold text-dark text-uppercase" style="white-space: pre-wrap;">' . $meta['keterangan'] . '</div>';
    echo '</div>';
}
?>