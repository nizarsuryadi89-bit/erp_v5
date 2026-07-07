<?php
// Initialize variables to prevent "Undefined variable" errors
$no = 1;
$total_rows = isset($jmlTmp) ? $jmlTmp : 0;
$items = isset($transaksi) ? $transaksi : [];
// Ensure totalPemesanan is initialized in the view or passed from controller, 
// using a local variable to sum up for this view's display.
$running_total = 0;

// Debugging (optional, remove in production)
// if (empty($items)) { echo "<!-- Debug: Data transaksi kosong or invalid variable -->"; }

if ($total_rows > 0 && !empty($items)) {
    foreach ($items as $dt) {
        // Safe property access with fallbacks
        $id_detail = $dt->id_detail ?? $dt->id ?? 0; // Accommodate different ID column names
        $detail_product = $dt->product_nama ?? $dt->detail_product ?? 'Produk Tidak Diketahui';
        $satuan_nama = $dt->satuan_nama ?? '-';
        $harga_aktif = $dt->harga_aktif ?? '1'; // Default to price 1
        $ket_1 = $dt->ket_1 ?? '';

        // Dynamic Price Field
        $hargaField = 'harga_' . $harga_aktif;
        $hargaSatuan = isset($dt->$hargaField) ? $dt->$hargaField : ($dt->harga_satuan ?? 0);

        // Calculation Logic
        $qty = $dt->qty ?? 0;
        $panjang = $dt->panjang ?? 0;
        $lebar = $dt->lebar ?? 0;
        $satuan_id = $dt->satuan_id ?? 0;

        $totalHargaItem = $hargaSatuan * $qty;

        // Calculation dependent on business logic for dimensions
        if ($satuan_id == '11') {
            // Satuan 11 is explicitly Meter Lari (ML)
            $totalHargaItem = $hargaSatuan * $qty * ($panjang > 0 ? $panjang : 1);
        } else if (isset($dt->kategori_id) && $dt->kategori_id == 9 && $ket_1 == '5') {
            $totalHargaItem = $hargaSatuan * $qty * ($panjang > 0 ? $panjang : 1);
        } else if ($satuan_id == '2' || $satuan_id == '4') {
            if ($panjang > 0 && $lebar > 0) {
                $totalHargaItem = $hargaSatuan * $qty * ($panjang * $lebar);
            } else if ($panjang > 0 && empty($lebar)) {
                $totalHargaItem = $hargaSatuan * $qty * $panjang;
            } else {
                $totalHargaItem = $hargaSatuan * $qty;
            }
        }

        if (isset($dt->harga_minimal) && $dt->harga_minimal > 0 && $totalHargaItem < $dt->harga_minimal) {
            $totalHargaItem = $dt->harga_minimal;
        }

        $running_total += $totalHargaItem;
        ?>

        <tr class="row_item_<?= $id_detail ?>">
            <td class="text-center"><?= $no ?></td>
            <td>
                <strong><?= $detail_product ?></strong>
                <?php
                $meta = !empty($dt->metadata) ? json_decode($dt->metadata, true) : [];
                $exclude_keys = ['product_id', 'nospk', 'qty', 'panjang', 'lebar', 'kategori_id', 'keterangan', 'approve', 'lokasi_cetak', 'subkategori_id', 'user_id', 'kodeproduk', 'jenis_transaksi', 'id_jenis_transaksi', 'konsumen_id', 'deskprint_id', 'user', 'subkategori', 'konsumen', 'deskprint'];
                $is_lf_or_dtf = in_array((int)($dt->kategori_id ?? 0), [2, 9]);

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
                        if (in_array($key, $exclude_keys)) continue;
                        if (empty($val) || $val === '0') continue;
                        
                        $in_schema = isset($schema_fields[$key]);

                        // Specific hiding for DTF 'Meteran' (which was value '5')
                        if (!$in_schema && $is_lf_or_dtf && $key === 'ket_1' && $val === '5') continue;

                        // Skip technical ID versions in favor of clean key versions
                        if (substr($key, -3) === '_id') {
                            $clean_k = substr($key, 0, -3);
                            if (isset($meta[$clean_k]) && !empty($meta[$clean_k])) {
                                continue;
                            }
                        }

                        // Enforce category-based Sisi exclusion
                        if (!$in_schema && $is_lf_or_dtf && in_array(strtolower($key), ['sisi', 'side', 'side_id', 'ket_1'])) {
                            continue;
                        }

                        $label = '';
                        $resolved_val = $val;
                        
                        if ($in_schema) {
                            $field = $schema_fields[$key];
                            $label = strtoupper($field['label'] ?? $key);
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
                    // 2. Fallback to Legacy Variation Resolver
                    $variation_label = '';
                    $variation_value = '';
                    $variation_color = 'text-info';
        
                    $schema_json = !empty($dt->product_form_schema) && $dt->product_form_schema !== '[]'
                        ? $dt->product_form_schema
                        : (!empty($dt->subcategory_form_schema) && $dt->subcategory_form_schema !== '[]'
                            ? $dt->subcategory_form_schema
                            : ($dt->category_form_schema ?? ''));

                    $found_in_schema = false;
                    if (!empty($schema_json)) {
                        $schema = json_decode($schema_json, true);
                        if (is_array($schema)) {
                            $raw_val = $dt->ket_1;
                            if (!empty($raw_val) && $raw_val !== '0') {
                                $target_field = null;
                                foreach ($schema as $field) {
                                    if (($field['name'] ?? '') === 'ket_1' || ($field['variabel_id'] ?? '') === 'ket_1') {
                                        $target_field = $field;
                                        break;
                                    }
                                }

                                if ($target_field) {
                                    $variation_label = strtoupper($target_field['label'] ?? 'Variation');
                                    if (isset($target_field['options'][$raw_val])) {
                                        $variation_value = $target_field['options'][$raw_val];
                                    } else {
                                        $variation_value = $raw_val;
                                    }
                                    $found_in_schema = true;
                                }

                                if (!$found_in_schema) {
                                    foreach ($schema as $field) {
                                        if (isset($field['options'][$raw_val])) {
                                            $variation_label = strtoupper($field['label'] ?? 'Variation');
                                            $variation_value = $field['options'][$raw_val];
                                            $found_in_schema = true;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if (!$found_in_schema && empty($schema_json)) {
                        if (($dt->kategori_id ?? 0) == 2 && !empty($dt->jenisprint_id)) {
                            $variation_label = 'MESIN';
                            $mesin_label = '';
                            if ($dt->jenisprint_id == 2) $mesin_label = 'Outdoor';
                            elseif ($dt->jenisprint_id == 3) $mesin_label = 'Indoor Solvent';
                            elseif ($dt->jenisprint_id == 4) $mesin_label = 'Indoor UV';
                            $variation_value = $mesin_label ?: '-';
                            $variation_color = 'text-primary';
                        } elseif (($dt->kategori_id ?? 0) == 9) {
                            $variation_label = '';
                        } else {
                            if ($ket_1 == '1') {
                                $variation_label = 'Cetak';
                                $variation_value = '1 Sisi';
                            } else if ($ket_1 == '2') {
                                $variation_label = 'Cetak';
                                $variation_value = '2 Sisi (Bolak-Balik)';
                            }
                        }
                    }

                    if (($dt->kategori_id ?? 0) == 9) {
                        $variation_label = '';
                        $variation_value = '';
                    }

                    if (!empty($variation_label) && !empty($variation_value) && !in_array($variation_value, ['-', '0'])): ?>
                        <br><small class="<?= $variation_color ?> fw-bold text-uppercase">
                            <?php if (strpos($variation_label, 'MESIN') !== false): ?>
                                <i class="fa fa-print me-1"></i>
                            <?php elseif (strpos($variation_label, 'LEBAR') !== false): ?>
                                <i class="fas fa-arrows-alt-h me-1"></i>
                            <?php else: ?>
                                <i class="fas fa-info-circle me-1"></i>
                            <?php endif; ?>
                            <?= $variation_label ?>: <?= strtoupper($variation_value) ?>
                        </small>
                    <?php endif;
                } ?>
            </td>
            <td class="text-center"><?= $qty ?></td>
            <td class="text-center"><?= $satuan_nama ?></td>
            <td class="text-end">Rp <?= number_format($hargaSatuan, 0, ',', '.') ?></td>
            <td class="text-center">
                <?php if ($satuan_id == '11'): ?>
                    <span class="badge bg-light text-dark border p-1"><?= $panjang ?> M
                        <?php if ($lebar > 0)
                            echo "<br>(L: {$lebar}cm)"; ?>
                        <br><small class="text-muted" style="font-size: 0.65rem;">(Meter Lari)</small></span>
                <?php elseif ($panjang > 0 && $lebar > 0): ?>
                    <span class="badge bg-light text-dark border"><?= $panjang ?> x <?= $lebar ?></span>
                <?php elseif ($panjang > 0 && empty($lebar)): ?>
                    <span class="badge bg-light text-dark border p-1"><?= $panjang ?> M <br><small class="text-muted"
                            style="font-size: 0.65rem;">(Meter Lari)</small></span>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
            <!-- Status Approval Column -->
            <td class="text-center">
                <?php
                // Logic for approval status label (assuming 'status' or 'approve' column)
                $approval = $dt->approve ?? 1;
                if ($approval == 1) {
                    echo '<span class="badge bg-success">Lanjut Produksi</span>';
                } else {
                    echo '<span class="badge bg-warning text-dark">Sampel</span>';
                }
                ?>
            </td>
            <td class="text-end fw-bold">Rp <?= number_format($totalHargaItem, 0, ',', '.') ?></td>
            <td class="text-center">
                <!-- IMPORTANT: Class 'btn_del_permanent' matches the JS in editspk.php -->
                <button type="button" class="btn btn-danger btn-sm btn_del_permanent" data-id="<?= $id_detail ?>"
                    title="Hapus Item">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>

        <?php
        $no++;
    }
} else {
    // Empty State matching the colspan of editspk.php (9 columns)
    echo '<tr><td colspan="9" class="text-center py-4 text-muted"><em>Tidak ada item transaksi</em></td></tr>';
}
?>

<!-- Hidden Input to pass total to JS if needed -->
<input type="hidden" id="calculated_total" value="<?= $running_total ?>">