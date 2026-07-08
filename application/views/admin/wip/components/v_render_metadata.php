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
    
    // Alias ket_1 to sisi if category is A3+
    if (isset($item->kategori_id) && $item->kategori_id == 1 && isset($item->ket_1)) {
        $meta['sisi'] = $item->ket_1;
    }
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

        // [NEW] Category 2 (Large Format) & 9 (DTF) Sisi/Side Exclusion rule
        if (in_array($item->kategori_id, [2, 9]) && in_array($key, ['sisi', 'side_id', 'ket_1'])) {
            continue;
        }

        if (!isset($meta[$key]) || $meta[$key] === '') {
            // [NEW] Clean key fallback: check if clean key (without _id) exists in metadata
            $clean_k = str_replace('_id', '', $key);
            if (isset($meta[$clean_k]) && $meta[$clean_k] !== '') {
                $meta[$key] = $meta[$clean_k];
            } else {
                continue;
            }
        }

        $val = $meta[$key];
        $label = $field['label'] ?? strtoupper($key);

        // Formatter
        if (in_array($field['type'], ['select', 'radio', 'checkbox'])) {
            if (isset($field['options']) && is_array($field['options'])) {
                if (!is_array($val))
                    $val = $field['options'][$val] ?? $val;
            }
        }
        if ($field['type'] === 'boolean')
            $val = ($val == 1 || $val == 'true') ? 'YA' : 'TIDAK';

        // Highlighting for Large Format Finishing
        $isLFFinishing = false;
        if ($item->kategori_id == 2) {
            $checkStr = strtolower($key . ' ' . $label);
            $keywords = ['finish', 'mata ayam', 'mataayam', 'seaming', 'selongsong', 'lebihan', 'laminasi', 'potong'];
            foreach ($keywords as $kw) {
                if (strpos($checkStr, $kw) !== false) {
                    $isLFFinishing = true;
                    break;
                }
            }
        }

        // Styling
        if ($key === 'jenisprint_id') {
            if ($item->kategori_id == 3) {
                // Flatbed Color Profile
                echo "<span class='badge bg-info bg-opacity-10 text-info border border-info border-opacity-25' style='{$uniform_badge_style}'>";
                echo "<i class='fas fa-palette me-1'></i> WARNA: " . strtoupper($val);
                echo "</span>";
            } else {
                // Standard Machine Badge
                echo "<span class='badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25' style='{$uniform_badge_style}'>";
                echo "<i class='fas fa-print me-1'></i> " . strtoupper($val);
                echo "</span>";
            }
        } elseif ($item->kategori_id == 6 && $key === 'ket_1') {
            // Laser Material Branding
            echo "<span class='badge bg-dark text-white border border-secondary shadow-sm' style='{$uniform_badge_style}'>";
            echo "<i class='fas fa-layer-group me-1'></i> MATERIAL: " . strtoupper($val);
            echo "</span>";
        } elseif ($item->kategori_id == 6 && $key === 'thickness') {
            // Laser Thickness Branding
            echo "<span class='badge bg-dark text-white border border-secondary shadow-sm' style='{$uniform_badge_style}'>";
            echo "<i class='fas fa-ruler-vertical me-1'></i> TEBAL: " . strtoupper($val) . " MM";
            echo "</span>";
        } elseif ($item->kategori_id == 6 && $key === 'metode_laser') {
            // Laser Method Branding
            echo "<span class='badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25' style='{$uniform_badge_style}'>";
            echo "<i class='fas fa-cut me-1'></i> " . strtoupper($val);
            echo "</span>";
        } elseif ($item->kategori_id == 7 && $key === 'model_apparel') {
            // Apparel Model
            echo "<span class='badge bg-primary text-white border border-primary shadow-sm' style='{$uniform_badge_style}'>";
            echo "<i class='fas fa-tshirt me-1'></i> " . strtoupper($val);
            echo "</span>";
        } elseif ($item->kategori_id == 7 && in_array($key, ['size_details', 'jersey_data'])) {
            // Apparel Specific Details
            $icon = ($key === 'size_details') ? 'fa-ruler-combined' : 'fa-id-card';
            echo "<span class='badge bg-info bg-opacity-10 text-info border border-info border-opacity-25' style='{$uniform_badge_style}'>";
            echo "<i class='fas $icon me-1'></i> " . strtoupper($val);
            echo "</span>";
        } elseif ($item->kategori_id == 8 && $key === 'ket_1') {
            // DTG Print Size
            echo "<span class='badge bg-dark text-white border border-secondary shadow-sm' style='{$uniform_badge_style}'>";
            echo "<i class='fas fa-expand-arrows-alt me-1'></i> BIDANG: " . strtoupper($val);
            echo "</span>";
        } elseif ($item->kategori_id == 8 && $key === 'jenisprint_id') {
            // DTG Process Style (override standard machine look)
            $isDark = (strpos(strtolower($val), 'gelap') !== false || strpos(strtolower($val), 'warna') !== false);
            $bg = $isDark ? 'bg-secondary' : 'bg-light';
            $txt = $isDark ? 'text-white' : 'text-dark';
            echo "<span class='badge $bg $txt border shadow-sm' style='{$uniform_badge_style}'>";
            echo "<i class='fas fa-filter me-1'></i> PROSES: " . strtoupper($val);
            echo "</span>";
        } elseif ($item->kategori_id == 8 && $key === 'posisi_cetak') {
            // DTG Print Position
            echo "<span class='badge bg-info bg-opacity-10 text-info border border-info border-opacity-25' style='{$uniform_badge_style}'>";
            echo "<i class='fas fa-bullseye me-1'></i> POSISI: " . strtoupper($val);
            echo "</span>";
        } elseif ($item->kategori_id == 10 && $key === 'ket_1') {
            // Merchandise Model/Varian
            echo "<span class='badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 shadow-sm' style='{$uniform_badge_style}'>";
            echo "<i class='fas fa-gift me-1'></i> MODEL: " . strtoupper($val);
            echo "</span>";
        } elseif ($item->kategori_id == 10 && $key === 'keterangan_merch') {
            // Merchandise Special Notes
            echo "<span class='badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25' style='{$uniform_badge_style}'>";
            echo "<i class='fas fa-sticky-note me-1'></i> NOTE: " . strtoupper($val);
            echo "</span>";
        } elseif (in_array($item->kategori_id, [5, 9]) && $key === 'ket_1') {
            $formatted_val = str_replace(',', ' x', $val);
            echo "<span class='badge bg-dark bg-opacity-10 text-dark border border-dark border-opacity-25 shadow-sm' style='{$uniform_badge_style}'>";
            echo "<i class='fas fa-ruler-horizontal me-1'></i> " . strtoupper($formatted_val);
            echo "</span>";
        } elseif ($isLFFinishing) {
            // Highly prominent badge for Finishing Instructions in Large Format
            echo "<span class='badge bg-warning text-dark border border-warning shadow-sm' style='{$uniform_badge_style}'>";
            echo "<i class='fas fa-tools me-1 opacity-75'></i> <span class='opacity-75 me-1'>" . strtoupper($label) . ":</span>";
            echo "<span class='text-uppercase'>" . $val . "</span>";
            echo "</span>";
        } else {
            echo "<span class='badge bg-light text-dark border' style='{$uniform_badge_style}'>";
            echo "<span class='text-muted text-uppercase me-1'>" . $label . ":</span>";
            echo "<span>" . strtoupper($val) . "</span>";
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