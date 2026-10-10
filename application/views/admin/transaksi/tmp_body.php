<?php
$no = 1;
$totalPemesanan = 0; // Inisialisasi agar tidak error di server produksi

// Metadata Mappings for cleaner code
$map_lokasi = [
    2 => 'TGM Pusat',
    3 => 'TGM Merchandise'
];

$map_approve = [
    1 => '<span class="badge bg-success">Lanjut Produksi</span>',
    2 => '<span class="badge bg-danger">Sampel Dulu</span>'
];

$map_mesin = [
    2 => 'Outdoor Solvent',
    3 => 'Indoor - Eco Solvent',
    4 => 'Indoor UV'
];

// Fetch Textile Widths once for the whole table


if (!empty($transaksi)) {
    foreach ($transaksi as $dt) {
        // 1. Calculate Unit Price based on active price field
        $hargaField = 'harga_' . ($dt->harga_aktif ?? 1);
        $hargaSatuan = $dt->$hargaField ?? 0;

        // 2. Prepare Labels
        $label_lokasi = $map_lokasi[$dt->lokasi_cetak] ?? $dt->lokasi_cetak;
        $label_approve = $map_approve[$dt->approve] ?? '-';
        ?>
        <tr class="del_mem<?= $dt->temp_id ?>">
            <td class="text-center">
                <div class="form-check d-flex justify-content-center">
                    <input class="form-check-input item-checkbox" type="checkbox" value="<?= $dt->temp_id ?>"
                        style="cursor:pointer; width:1.1rem; height:1.1rem;">
                </div>
            </td>
            <td class="text-center"><?= $no ?></td>
            <td>
                <?php if (!empty($dt->detail_product)): ?>
                    <p class="mb-1 text-dark fw-bold"><?= $dt->detail_product ?></p>

                    <?php
                    // [NEW] 1. Try to resolve from Dynamic Metadata first
                    $meta = !empty($dt->metadata) ? json_decode($dt->metadata, true) : [];
                    $exclude_keys = ['product_id', 'nospk', 'qty', 'panjang', 'lebar', 'kategori_id', 'keterangan', 'approve', 'lokasi_cetak', 'subkategori_id', 'user_id', 'kodeproduk', 'jenis_transaksi', 'id_jenis_transaksi', 'konsumen_id', 'deskprint_id', 'user', 'subkategori', 'konsumen', 'deskprint', 'unit_price', 'subtotal', 'price_snapshot_at'];
                    $is_lf_or_dtf = in_array((int)$dt->kategori_id, [2, 9]);

                    $schema_json = !empty($dt->product_form_schema) && $dt->product_form_schema !== '[]'
                        ? $dt->product_form_schema
                        : (!empty($dt->subcategory_form_schema) && $dt->subcategory_form_schema !== '[]'
                            ? $dt->subcategory_form_schema
                            : ($dt->category_form_schema ?? ''));
                            
                    $schema_fields = [];
                    if (!empty($schema_json)) {
                        $schema = json_decode($schema_json, true);
                        if (is_array($schema)) {
                            foreach ($schema as $field) {
                                $name = $field['name'] ?? ($field['variabel_id'] ?? '');
                                if (!empty($name)) {
                                    $schema_fields[$name] = $field;
                                }
                            }
                        }
                    }

                    $printed_metadata = [];
                    if (!empty($meta) && is_array($meta)) {
                        foreach ($meta as $key => $val) {
                            if ($key === '') continue; // Skip empty keys from dirty data
                            if (in_array($key, $exclude_keys)) continue;
                            if (empty($val) || $val === '0') continue;
                            
                            $schema_key = $key;
                            $in_schema = isset($schema_fields[$schema_key]);
                            if (!$in_schema && isset($schema_fields[$key . '_id'])) {
                                $in_schema = true;
                                $schema_key = $key . '_id';
                            }

                            // Specific hiding for DTF 'Meteran' (which was value '5')
                            if (!$in_schema && $is_lf_or_dtf && $key === 'ket_1' && $val === '5') continue;

                            // Skip technical ID versions in favor of clean key versions
                            if (substr($key, -3) === '_id') {
                                $clean_k = substr($key, 0, -3);
                                if (isset($meta[$clean_k]) && !empty($meta[$clean_k])) {
                                    continue;
                                }
                            }

                            // Skip legacy label keys that were saved dynamically
                            if (substr($key, -6) === '_label') {
                                continue;
                            }

                            // Skip legacy 'side' or 'ket' if the schema already defines equivalent fields
                            if (strtolower($key) === 'side' && (isset($schema_fields['sisi']) || isset($meta['sisi']))) {
                                continue;
                            }
                            if (strtolower($key) === 'jenisprint' && (isset($schema_fields['mesin']) || isset($meta['mesin']))) {
                                continue;
                            }

                            // Enforce category-based Sisi exclusion
                            if (!$in_schema && $is_lf_or_dtf && in_array(strtolower($key), ['sisi', 'side', 'side_id', 'ket_1'])) {
                                continue;
                            }

                            $label = '';
                            $resolved_val = $val;
                            
                            if ($in_schema) {
                                $field = $schema_fields[$schema_key];
                                $label = strtoupper($field['label'] ?? $schema_key);
                                if (isset($field['options']) && is_array($field['options'])) {
                                    if (isset($field['options'][$val])) {
                                        $resolved_val = $field['options'][$val];
                                    }
                                }
                            } else {
                                if (strpos(strtolower($key), 'ket_') === 0) {
                                    $label = 'HIDDEN_LABEL_' . $key;
                                } else {
                                    $label = strtoupper(str_replace('_', ' ', $key));
                                    if ($key === 'jenisprint') $label = 'MESIN';
                                }
                            }
                            
                            $printed_metadata[$label] = $resolved_val;
                        }
                    }

                    if (!empty($printed_metadata)) {
                        foreach ($printed_metadata as $lbl => $val) {
                            $icon = 'fa-info-circle';
                            $color_class = 'text-info';
                            if (strpos($lbl, 'MESIN') !== false) {
                                $icon = 'fa-print';
                                $color_class = 'text-primary';
                            } elseif (strpos($lbl, 'LEBAR') !== false) {
                                $icon = 'fa-arrows-alt-h';
                                $color_class = 'text-success';
                            }
                            if (strpos($lbl, 'HIDDEN_LABEL_') === 0) {
                                echo "<small class='{$color_class} fw-bold d-block text-uppercase'><i class='fas {$icon} me-1'></i>" . strtoupper($val) . "</small>";
                            } else {
                                echo "<small class='{$color_class} fw-bold d-block text-uppercase'><i class='fas {$icon} me-1'></i>{$lbl}: " . strtoupper($val) . "</small>";
                            }
                        }
                    } else {
                        // 2. Intelligent Variation Resolver using Schema
                        $schema_json = !empty($dt->product_form_schema) && $dt->product_form_schema !== '[]'
                            ? $dt->product_form_schema
                            : (!empty($dt->subcategory_form_schema) && $dt->subcategory_form_schema !== '[]'
                                ? $dt->subcategory_form_schema
                                : ($dt->category_form_schema ?? ''));

                        $variations_to_check = [
                            'ket_1' => $dt->ket_1 ?? '',
                            'jenisprint_id' => $dt->jenisprint_id ?? '',
                            'side_id' => $dt->side_id ?? ''
                        ];
                        
                        $db_meta = !empty($dt->metadata) ? json_decode($dt->metadata, true) : [];
                        if (empty($db_meta) && !empty($dt->product_metadata)) {
                            $db_meta = json_decode($dt->product_metadata, true);
                        }
                        if (is_array($db_meta)) {
                            foreach($db_meta as $mk => $mv) {
                                if (strpos($mk, 'dim_') === 0 && !isset($variations_to_check[$mk])) {
                                    $variations_to_check[$mk] = $mv;
                                }
                            }
                        }

                        $printed_variations = false;
                        if (!empty($schema_json)) {
                            $schema = json_decode($schema_json, true);
                            if (is_array($schema)) {
                                foreach ($variations_to_check as $var_key => $raw_val) {
                                    if (empty($raw_val) || $raw_val === '0') continue;
                                    
                                    $found = false;
                                    $lbl = '';
                                    $val = '';
                                    
                                    foreach ($schema as $field) {
                                        if (($field['name'] ?? '') === $var_key || ($field['variabel_id'] ?? '') === $var_key) {
                                            $lbl = strtoupper($field['label'] ?? $var_key);
                                            $val = $field['options'][$raw_val] ?? $raw_val;
                                            $found = true;
                                            break;
                                        }
                                    }
                                    
                                    if ($found && !empty($lbl) && !empty($val) && !in_array($val, ['-', '0'])) {
                                        $icon = 'fa-info-circle';
                                        $color = 'text-info';
                                        if ($var_key === 'jenisprint_id' || strpos($lbl, 'MESIN') !== false) {
                                            $icon = 'fa-print';
                                            $color = 'text-primary';
                                        } elseif ($var_key === 'side_id') {
                                            $icon = 'fa-clone';
                                            $color = 'text-secondary';
                                        }
                                        echo "<small class='{$color} fw-bold d-block text-uppercase'><i class='fas {$icon} me-1'></i>{$lbl}: " . strtoupper($val) . "</small>";
                                        $printed_variations = true;
                                    }
                                }
                            }
                        }
                        
                        if (!$printed_variations && empty($schema_json)) {
                            // Fallback to absolute legacy
                            $variation_label = '';
                            $variation_value = '';
                            $variation_color = 'text-info';
                            
                            if ($dt->kategori_id == 2) {
                                $variation_label = 'MESIN';
                                $variation_value = $map_mesin[$dt->ket_1] ?? '-';
                                $variation_color = 'text-primary';
                            } elseif ($dt->kategori_id == 9) {
                                $variation_label = '';
                            } elseif (isset($map_lebar_textile[$dt->ket_1])) {
                                $variation_label = 'JENIS LEBAR';
                                $variation_value = $map_lebar_textile[$dt->ket_1];
                                $variation_color = 'text-success';
                            } else {
                                if ($dt->ket_1 == '1') {
                                    $variation_label = 'Cetak';
                                    $variation_value = '1 Sisi';
                                } else if ($dt->ket_1 == '2') {
                                    $variation_label = 'Cetak';
                                    $variation_value = '2 Sisi (Bolak-Balik)';
                                }
                            }

                            if ($dt->kategori_id == 9) {
                                $variation_label = '';
                                $variation_value = '';
                            }

                            if (!empty($variation_label) && !empty($variation_value) && !in_array($variation_value, ['-', '0'])): ?>
                                <small class="<?= $variation_color ?> fw-bold d-block text-uppercase">
                                    <?php if (strpos($variation_label, 'MESIN') !== false): ?>
                                        <i class="fa fa-print me-1"></i>
                                    <?php elseif (strpos($variation_label, 'LEBAR') !== false): ?>
                                        <i class="fas fa-arrows-alt-h me-1"></i>
                                    <?php endif; ?>
                                    <?= $variation_label ?>: <?= strtoupper($variation_value) ?>
                                </small>
                            <?php endif;
                        }
                    }
                    ?>
                <?php else: ?>
                    <b class="text-muted italic">Spesifikasi belum diset admin</b>
                    <button type="button" class="btn_del btn btn-xs btn-warning d-block mt-1"
                        id="<?= $dt->temp_id ?>">Hapus</button>
                <?php endif; ?>
            </td>
            
            <td class="text-start text-muted small" style="white-space: pre-wrap; font-size: 0.8rem;"><?= !empty($dt->keterangan) ? $dt->keterangan : '-' ?></td>

            <td class="text-center fw-bold"><?= $dt->temp_qty ?></td>

            <td class="text-end text-muted"><?= number_format($hargaSatuan) ?></td>

            <td class="text-center">
                <?php
                if ($dt->satuan_id == '11') { // Meter Lari
                    echo number_format($dt->temp_panjang, 2) . " M";
                    if ($dt->temp_lebar > 0) {
                        echo "<br><small class='text-muted'>(L: {$dt->temp_lebar}cm)</small>";
                    }
                    echo "<small class='text-muted d-block'>(Meter Lari)</small>";
                } else if ($dt->temp_panjang > 0 && $dt->temp_lebar > 0) {
                    echo "{$dt->temp_panjang} x {$dt->temp_lebar}";
                } else if ($dt->temp_panjang > 0 && empty($dt->temp_lebar)) {
                    echo number_format($dt->temp_panjang, 2) . " M <small class='text-muted d-block'>(Meter Lari)</small>";
                } else {
                    echo "-";
                }
                ?>
            </td>

            <td class="text-center">
                <div class="small fw-bold mb-1"><?= $label_lokasi ?></div>
                <?= $label_approve ?>
            </td>

            <td class="text-end">
                <?php
                // Calculation Logic (Preserved exactly as original)
                $totalHarga = $hargaSatuan * $dt->temp_qty;

                if ($dt->satuan_id == '11') {
                    $totalHarga = $hargaSatuan * $dt->temp_qty * $dt->temp_panjang;
                } else if ($dt->kategori_id == 9 && $dt->ket_1 == '5') {
                    $totalHarga = $hargaSatuan * $dt->temp_qty * $dt->temp_panjang;
                } else if (in_array($dt->satuan_id, ['2', '4'])) {
                    if ($dt->temp_panjang > 0 && $dt->temp_lebar > 0) {
                        $totalHarga *= ($dt->temp_panjang * $dt->temp_lebar);
                    } else if ($dt->temp_panjang > 0 && empty($dt->temp_lebar)) {
                        $totalHarga *= $dt->temp_panjang;
                    }
                }

                $is_min_price_applied = false;
                if (isset($dt->harga_minimal) && $dt->harga_minimal > 0 && $totalHarga < $dt->harga_minimal) {
                    $totalHarga = $dt->harga_minimal;
                    $is_min_price_applied = true;
                }
                ?>
                <b class="text-dark">Rp <?= number_format($totalHarga) ?></b>
                <?php if ($is_min_price_applied): ?>
                    <div class="mt-1"><span class="badge bg-warning text-dark" style="font-size: 0.6rem;"><i class="fas fa-exclamation-circle me-1"></i>Min Price</span></div>
                <?php endif; ?>
            </td>

            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm btn_del" id="<?= $dt->temp_id ?>" title="Hapus item">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
        <?php
        $no++;
        $totalPemesanan += $totalHarga;
    }
} else {
    echo "<tr><td colspan='9' class='text-center py-4 text-muted'><b>Belum Ada Item Dalam SPK Ini</b></td></tr>";
}
?>