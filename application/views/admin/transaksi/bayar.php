<?php
$title = "PROSES PEMBAYARAN SPK";
// Calculate subtotal at the beginning
$subTotal = 0;
$totalItems = 0;

if (!empty($getdetail)) {
    foreach ($getdetail as $dt) {
        $harga = $dt->harga_1 ?? 0;
        if ($harga <= 0) {
            $harga = 0;
        }

        $meta = !empty($dt->metadata) ? json_decode($dt->metadata, true) : [];
        if (isset($meta['subtotal']) && is_numeric($meta['subtotal'])) {
            $jumlah = (float) $meta['subtotal'];
        } else if (isset($dt->subtotal) && $dt->subtotal !== null && $dt->subtotal !== '' && is_numeric($dt->subtotal)) {
            $jumlah = (float) $dt->subtotal;
        } else {
            if (isset($dt->satuan_id) && $dt->satuan_id == 11) {
                $jumlah = $dt->panjang * $harga * $dt->qty;
            } else if (isset($dt->kategori_id) && isset($dt->ket_1) && $dt->kategori_id == 9 && $dt->ket_1 == '5') {
                $jumlah = $dt->panjang * $harga * $dt->qty;
            } else if ($dt->panjang != '0' && $dt->lebar != '0') {
                $jumlah = $dt->panjang * $dt->lebar * $harga * $dt->qty;
            } else if ($dt->panjang != '0' && empty($dt->lebar)) {
                $jumlah = $dt->panjang * $harga * $dt->qty;
            } else {
                $jumlah = $harga * $dt->qty;
            }

            if (isset($dt->harga_minimal) && $dt->harga_minimal > 0 && $jumlah < $dt->harga_minimal) {
                $jumlah = $dt->harga_minimal;
            }
        }

        $subTotal += $jumlah;
        $totalItems += $dt->qty;
    }
}

// [NEW] Gunakan tagihan manual jika ada dan lebih besar dari 0 (untuk SPK Manual/Piutang)
if (isset($manual_total_tagihan) && floatval($manual_total_tagihan) > 0) {
    $subTotal = floatval($manual_total_tagihan);
}

// Bulatkan subTotal untuk mencegah bug JS formatRupiah yang menghapus titik desimal
$subTotal = round($subTotal);
?>
<!-- Debug: Calculated SubTotal = <?= $subTotal ?> -->

<link rel="stylesheet" href="<?= base_url('assets/css/tgm-bayar.css') ?>">

<div class="app-content">
    <?php $this->load->view('layout_admin/header_modul'); ?>

    <div class="container-fluid py-4">
        <?php if (!empty($getdata['is_blacklist']) && $getdata['is_blacklist'] == 1): ?>
            <!-- Alert Peringatan Konsumen Pengawasan Kasir -->
            <div class="alert alert-danger border-2 border-danger shadow-sm d-flex align-items-center mb-4 p-3 rounded-4" role="alert">
                <div class="fs-1 text-danger me-3 ps-2">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-danger text-white px-2 py-1"><i class="fas fa-shield-alt me-1"></i>PERINGATAN KASIR</span>
                        <h6 class="alert-heading fw-bold mb-0 text-danger text-uppercase">KONSUMEN DALAM PENGAWASAN KHUSUS</h6>
                    </div>
                    <div class="text-dark small">
                        <strong>Catatan/Alasan:</strong> <?= !empty($getdata['catatan_blacklist']) ? html_escape($getdata['catatan_blacklist']) : 'Konsumen ini ditandai dalam daftar pengawasan kasir.' ?>
                    </div>
                    <div class="text-danger small mt-1 fw-semibold">
                        <i class="fas fa-info-circle me-1"></i>Harap pastikan pembayaran sesuai ketentuan dan periksa sisa piutang sebelum menyelesaikan transaksi.
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="row g-4">
            <!-- Info Transaksi & Action Header -->
            <div class="col-12">
                <div class="report-section-card mb-0 shadow-sm border-primary border-opacity-25">
                    <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-2"
                        style="background: var(--secondary-gradient);">
                        <h5 class="mb-0 text-white"><i class="fas fa-file-invoice-dollar me-2"></i>Invoice
                            #<?= $nospk ?></h5>
                        <button type="button" class="btn btn-sm btn-info py-1 px-3 fw-bold text-white shadow-sm"
                            data-bs-toggle="modal" data-bs-target="#modalPanduanBayar" title="Lihat Panduan Kasir"
                            style="font-size: 0.75rem;">
                            <i class="fas fa-book-reader me-1"></i> PANDUAN KASIR
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4 align-items-center">
                            <!-- Tanggal -->
                            <div class="col-md-3 border-end">
                                <span class="info-label"><i class="fas fa-calendar-alt me-1"></i>Tanggal Pesan</span>
                                <div class="info-value mt-1 fs-6">
                                    <?= date_indo(date('Y-m-d', strtotime($getdata['created_at']))) ?>
                                    <span
                                        class="badge bg-light text-muted ms-1 fw-normal"><?= date('H:i', strtotime($getdata['created_at'])) ?></span>
                                </div>
                            </div>

                            <!-- Konsumen -->
                            <div class="col-md-3 border-end ps-md-4">
                                <span class="info-label"><i class="fas fa-user me-1"></i>Konsumen</span>
                                <div class="info-value text-primary fs-5 mt-1 d-flex align-items-center flex-wrap gap-1">
                                    <span><?= $getdata['konsumen_nama'] ?></span>
                                    <?php if (!empty($getdata['is_blacklist']) && $getdata['is_blacklist'] == 1): ?>
                                        <span class="badge bg-danger text-white shadow-sm" style="font-size: 0.65rem;" title="<?= !empty($getdata['catatan_blacklist']) ? html_escape($getdata['catatan_blacklist']) : 'Perhatian Khusus' ?>">
                                            <i class="fas fa-exclamation-triangle me-1"></i>Pengawasan Kasir
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="small text-muted fw-bold mt-1">
                                    <i class="fas fa-phone-alt me-1"></i> <?= $getdata['konsumen_nohp'] ?>
                                </div>
                            </div>

                            <!-- Metode -->
                            <div class="col-md-3 border-end ps-md-4">
                                <span class="info-label"><i class="fas fa-tags me-1"></i>Metode Pesanan</span>
                                <div class="d-flex align-items-center mt-2">
                                    <span class="badge rounded-pill px-3 py-2 bg-light text-dark shadow-sm border">
                                        <i class="fas fa-shopping-bag text-primary me-2"></i>
                                        <span class="fw-bold"><?= $getdata['nama_jenis_transaksi'] ?></span>
                                    </span>
                                </div>
                                <?php if ($getdata['id_jenis_transaksi'] == '2'): ?>
                                    <div class="mt-2 small fw-bold text-primary">
                                        <span
                                            class="badge bg-warning text-dark px-2 mb-1"><?= $getdata['marketplace_nama'] ?></span><br>
                                        <i class="fas fa-hashtag me-1"></i> <?= $getdata['resi_marketplace'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Total -->
                            <div class="col-md-3 ps-md-4 text-center">
                                <span class="info-label">TOTAL PEMBAYARAN</span>
                                <div class="fw-800 text-dark mt-2" style="font-size: 1.8rem;">Rp
                                    <?= number_format($subTotal) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="card-footer bg-light p-3 border-top d-flex flex-wrap justify-content-end gap-3 align-items-center">
                        <button class="btn btn-premium btn-premium-warning shadow-sm px-4" data-bs-toggle="modal"
                            data-bs-target="#modalPreview">
                            <i class="fas fa-eye me-2"></i><span>Preview Transaksi</span>
                        </button>

                        <button class="btn btn-premium btn-premium-primary shadow-lg px-5 py-2" data-bs-toggle="modal"
                            data-bs-target="#modalHeaderColorPrimary" style="font-size: 1.1rem;">
                            <i class="fas fa-credit-card me-2"></i><span>Proses Pembayaran</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Detail Produk Card -->
            <div class="col-12">
                <div class="report-section-card">
                    <div class="section-header">
                        <h5><i class="fas fa-boxes me-2"></i>Detail Produk & Layanan</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th width="35%">Nama Produk & Spesifikasi</th>
                                        <th width="15%">Harga Satuan</th>
                                        <th width="10%" class="text-center">Qty</th>
                                        <th width="15%">Subtotal</th>
                                        <th width="20%">Dateline Produksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    if (empty($getdetail)) {
                                        echo '<tr><td colspan="6" class="p-5 text-center text-muted"><i class="fas fa-folder-open fa-3x mb-3 d-block opacity-25"></i>Data detail tidak ditemukan</td></tr>';
                                    } else {
                                        foreach ($getdetail as $dt) {
                                            $harga = $dt->harga_1 ?? 0;
                                            if ($harga <= 0)
                                                $harga = 0;

                                            $meta = !empty($dt->metadata) ? json_decode($dt->metadata, true) : [];
                                            if (isset($meta['subtotal']) && is_numeric($meta['subtotal'])) {
                                                $jumlah = (float) $meta['subtotal'];
                                            } else if (isset($dt->subtotal) && $dt->subtotal !== null && $dt->subtotal !== '' && is_numeric($dt->subtotal)) {
                                                $jumlah = (float) $dt->subtotal;
                                            } else {
                                                if (isset($dt->satuan_id) && $dt->satuan_id == 11) {
                                                    $jumlah = $dt->panjang * $harga * $dt->qty;
                                                } else if (isset($dt->kategori_id) && isset($dt->ket_1) && $dt->kategori_id == 9 && $dt->ket_1 == '5') {
                                                    $jumlah = $dt->panjang * $harga * $dt->qty;
                                                } else if ($dt->panjang != '0' && $dt->lebar != '0') {
                                                    $jumlah = $dt->panjang * $dt->lebar * $harga * $dt->qty;
                                                } else if ($dt->panjang != '0' && empty($dt->lebar)) {
                                                    $jumlah = $dt->panjang * $harga * $dt->qty;
                                                } else {
                                                    $jumlah = $harga * $dt->qty;
                                                }

                                                if (isset($dt->harga_minimal) && $dt->harga_minimal > 0 && $jumlah < $dt->harga_minimal) {
                                                    $jumlah = $dt->harga_minimal;
                                                }
                                            }

                                            // [NEW] Override item level display for manual invoice
                                            if (isset($manual_total_tagihan) && floatval($manual_total_tagihan) > 0 && count($getdetail) == 1) {
                                                $jumlah = floatval($manual_total_tagihan);
                                                $harga = $jumlah / max(1, $dt->qty);
                                            }
                                            ?>
                                            <tr>
                                                <td class="text-center">
                                                    <span class="badge bg-light text-dark fw-bold rounded-circle border p-2"
                                                        style="width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center;"><?= $no++ ?></span>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-dark fs-6"><?= $dt->product_nama ?></div>
                                                    <?php if (!empty($dt->keterangan)): ?>
                                                        <div class="small text-muted py-1 fst-italic">Ket:
                                                            <?= htmlspecialchars($dt->keterangan ?? '') ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="d-flex gap-2 mt-1 flex-wrap">
                                                        <?php
                                                        $lokasi_cetak = $dt->lokasi_cetak ?? 0;
                                                        $store_name = 'Cabang Lain';
                                                        if ($lokasi_cetak == 2) {
                                                            $store_name = 'TGM Official (Pusat)';
                                                        } elseif ($lokasi_cetak == 3) {
                                                            $store_name = 'TGM Merch (Cabang)';
                                                        } else {
                                                            $store_name = 'TGM Official (Pusat)';
                                                        }

                                                        if (stripos($store_name, 'Merch') !== false) {
                                                            $badge_class = 'bg-warning bg-opacity-25 text-dark border-warning border-opacity-50';
                                                            $icon_color = 'text-dark';
                                                        } elseif (stripos($store_name, 'Official') !== false || stripos($store_name, 'Pusat') !== false) {
                                                            $badge_class = 'bg-primary bg-opacity-10 text-primary border-primary border-opacity-25';
                                                            $icon_color = 'text-primary';
                                                        } else {
                                                            $badge_class = 'bg-light text-dark border-dark border-opacity-25';
                                                            $icon_color = 'text-dark';
                                                        }
                                                        ?>
                                                        <span class="badge <?= $badge_class ?> border px-2"
                                                            title="Lokasi Produksi">
                                                            <i class="fas fa-building <?= $icon_color ?> me-1"></i>
                                                            <?= htmlspecialchars($store_name) ?>
                                                        </span>
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
                                                                        if ($key === 'jenisprint') {
                                                                            if (($dt->kategori_id ?? 0) == 1) {
                                                                                $label = 'PILIHAN SISI';
                                                                            } else {
                                                                                $label = 'MESIN';
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                                
                                                                $printed_metadata[$label] = $resolved_val;
                                                            }
                                                        }

                                                        if (!empty($printed_metadata)) {
                                                            foreach ($printed_metadata as $lbl => $val) {
                                                                $icon = 'fa-info-circle';
                                                                $color_class = 'secondary';
                                                                if (strpos($lbl, 'MESIN') !== false) {
                                                                    $icon = 'fa-print';
                                                                    $color_class = 'primary';
                                                                } elseif (strpos($lbl, 'LEBAR') !== false) {
                                                                    $icon = 'fa-arrows-alt-h';
                                                                    $color_class = 'success';
                                                                }
                                                                
                                                                if (strpos($lbl, 'HIDDEN_LABEL_') === 0) {
                                                                    $dval = ucwords(strtolower($val)); echo "<span class='badge bg-{$color_class} bg-opacity-10 text-{$color_class} border border-{$color_class} border-opacity-25 px-3 py-1 mb-1 fw-normal rounded-pill' style='font-size: 0.75rem; letter-spacing: 0.3px;'><i class='fas {$icon} me-1 opacity-75'></i><span class='fw-bold'>{$dval}</span></span> ";
                                                                } else {
                                                                    $dlbl = ucwords(strtolower(str_replace('_', ' ', $lbl))); $dval = ucwords(strtolower($val)); echo "<span class='badge bg-{$color_class} bg-opacity-10 text-{$color_class} border border-{$color_class} border-opacity-25 px-3 py-1 mb-1 fw-normal rounded-pill' style='font-size: 0.75rem; letter-spacing: 0.3px;'><i class='fas {$icon} me-1 opacity-75'></i><span class='opacity-75 me-1'>{$dlbl}:</span><span class='fw-bold'>{$dval}</span></span> ";
                                                                }
                                                            }
                                                        } else {
                                                            // 2. Intelligent Variation Resolver using Schema
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
                                                                            $color_class = 'info';
                                                                            if ($var_key === 'jenisprint_id' || strpos($lbl, 'MESIN') !== false) {
                                                                                $icon = 'fa-print';
                                                                                $color_class = 'primary';
                                                                            } elseif ($var_key === 'side_id') {
                                                                                $icon = 'fa-clone';
                                                                                $color_class = 'secondary';
                                                                            }
                                                                            $dlbl = ucwords(strtolower(str_replace('_', ' ', $lbl))); $dval = ucwords(strtolower($val)); echo "<span class='badge bg-{$color_class} bg-opacity-10 text-{$color_class} border border-{$color_class} border-opacity-25 px-3 py-1 mb-1 fw-normal rounded-pill' style='font-size: 0.75rem; letter-spacing: 0.3px;'><i class='fas {$icon} me-1 opacity-75'></i><span class='opacity-75 me-1'>{$dlbl}:</span><span class='fw-bold'>{$dval}</span></span> ";
                                                                            $printed_variations = true;
                                                                        }
                                                                    }
                                                                }
                                                            }

                                                            if (!$printed_variations && empty($schema_json)) {
                                                                // Fallback to absolute legacy
                                                                $variation_label = '';
                                                                $variation_value = '';
                                                                $variation_color = 'info';
                                                                $icon = 'fa-layer-group';
                                                                
                                                                if (($dt->kategori_id ?? 0) == 2 && !empty($dt->jenisprint_id)) {
                                                                    $variation_label = 'MESIN';
                                                                    $mesin_label = '';
                                                                    if ($dt->jenisprint_id == 2) $mesin_label = 'Outdoor';
                                                                    elseif ($dt->jenisprint_id == 3) $mesin_label = 'Indoor Solvent';
                                                                    elseif ($dt->jenisprint_id == 4) $mesin_label = 'Indoor UV';
                                                                    $variation_value = $mesin_label ?: '-';
                                                                    $variation_color = 'primary';
                                                                    $icon = 'fa-print';
                                                                } elseif (($dt->kategori_id ?? 0) == 9) {
                                                                    $variation_label = '';
                                                                } else {
                                                                    if (isset($dt->ket_1) && $dt->ket_1 == '1') {
                                                                        $variation_label = 'Cetak';
                                                                        $variation_value = '1 Sisi';
                                                                    } else if (isset($dt->ket_1) && $dt->ket_1 == '2') {
                                                                        $variation_label = 'Cetak';
                                                                        $variation_value = '2 Sisi (Bolak-Balik)';
                                                                    }
                                                                }

                                                                if (($dt->kategori_id ?? 0) == 9) {
                                                                    $variation_label = '';
                                                                    $variation_value = '';
                                                                }

                                                                if (!empty($variation_label) && !empty($variation_value) && !in_array($variation_value, ['-', '0'])) {
                                                                    $dlbl = ucwords(strtolower(str_replace('_', ' ', $variation_label))); $dval = ucwords(strtolower($variation_value)); echo "<span class='badge bg-{$variation_color} bg-opacity-10 text-{$variation_color} border border-{$variation_color} border-opacity-25 px-3 py-1 mb-1 fw-normal rounded-pill' style='font-size: 0.75rem; letter-spacing: 0.3px;'><i class='fas {$icon} me-1 opacity-75'></i><span class='opacity-75 me-1'>{$dlbl}:</span><span class='fw-bold'>{$dval}</span></span> ";
                                                                }
                                                            }
                                                        }
                                                        ?>

                                                        <?php
                                                        if ($dt->approve == '1') {
                                                            $approve_class = 'bg-success';
                                                            $approve_text = '🚀 CETAK SEMUA';
                                                        } else {
                                                            $approve_class = 'bg-danger';
                                                            $approve_text = '🧪 SAMPEL DULU';
                                                        }
                                                        ?>
                                                        <a href="javascript:void(0)" data-bs-toggle="modal"
                                                            data-bs-target="#masterApproveModal" data-id="<?= $dt->id ?>"
                                                            data-status="<?= $dt->approve ?>"
                                                            data-product="<?= htmlspecialchars($dt->product_nama, ENT_QUOTES) ?>"
                                                            class="badge <?= $approve_class ?> bg-opacity-10 text-<?= str_replace('bg-', '', $approve_class) ?> border border-<?= str_replace('bg-', '', $approve_class) ?> border-opacity-25 px-2 text-decoration-none">
                                                            <?= $approve_text ?> <i class="fas fa-edit ms-1 small"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-dark">Rp <?= number_format($harga) ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="fw-800 text-primary fs-5"><?= $dt->qty ?></div>
                                                    <?php if (isset($dt->satuan_id) && $dt->satuan_id == 11): ?>
                                                        <span
                                                            class="badge bg-secondary bg-opacity-10 text-secondary small fw-normal">
                                                            <?= $dt->panjang ?> M
                                                            <?php if (!empty($dt->lebar) && $dt->lebar != '0' && $dt->lebar != '0.0')
                                                                echo " (L: {$dt->lebar}cm)"; ?>
                                                        </span>
                                                    <?php elseif ($dt->panjang != '0' && $dt->lebar != '0'): ?>
                                                        <span
                                                            class="badge bg-secondary bg-opacity-10 text-secondary small fw-normal">
                                                            <?= $dt->panjang ?> x <?= $dt->lebar ?>
                                                        </span>
                                                    <?php elseif ($dt->panjang != '0' && empty($dt->lebar)): ?>
                                                        <span
                                                            class="badge bg-secondary bg-opacity-10 text-secondary small fw-normal">
                                                            <?= $dt->panjang ?> M (Meter)
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-dark fs-6">Rp <?= number_format($jumlah) ?></span>
                                                </td>
                                                <td>
                                                    <form action="" method="post">
                                                        <input type="hidden" name="id" value="<?= $dt->id ?>">
                                                        <div class="d-flex flex-column gap-1">
                                                            <div class="input-group input-group-sm">
                                                                <?php
                                                                $dateline_tgl = ($dt->kategori_id == '1') ? date('Y-m-d') : $dt->dateline_tgl;
                                                                $dateline_jam = ($dt->kategori_id == '1') ? date('H:i:s') : $dt->dateline_jam;
                                                                ?>
                                                                <span class="input-group-text bg-light"><i
                                                                        class="far fa-calendar-alt text-muted"></i></span>
                                                                <input type="date" name="dateline_tgl"
                                                                    value="<?= $dateline_tgl ?>"
                                                                    class="form-control border-light shadow-none">
                                                            </div>
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-text bg-light"><i
                                                                        class="far fa-clock text-muted"></i></span>
                                                                <input type="time" name="dateline_jam"
                                                                    value="<?= $dateline_jam ?>"
                                                                    class="form-control border-light shadow-none">
                                                                <button type="submit" class="btn btn-primary px-3 border-0"
                                                                    title="Update Dateline">
                                                                    <i class="fas fa-save"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                                <?php if (!empty($getdetail)): ?>
                                    <tfoot class="bg-light border-top-0">
                                        <tr>
                                            <td colspan="3" class="text-end py-3 text-muted small fw-bold">TOTAL KUANTITAS:
                                            </td>
                                            <td class="text-center py-3 fw-800 fs-5 text-dark"><?= $totalItems ?></td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>



<!-- Enhanced Modal Bootstrap 5 -->
<!-- Enhanced Modal Bootstrap 5 -->
<div class="modal fade" id="modalHeaderColorPrimary" tabindex="-1" aria-labelledby="modalHeaderColorPrimaryLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <form id="formBayar" action="<?= base_url('transaksi/action_bayar'); ?>" method="post"
            class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white border-0"
                style="background: var(--primary-gradient); padding: 1.25rem 1.75rem;">
                <h5 class="modal-title d-flex align-items-center" id="modalHeaderColorPrimaryLabel">
                    <div class="bg-white bg-opacity-20 rounded-circle p-2 me-3"
                        style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-credit-card fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-800 fs-5">Selesaikan Pembayaran</div>
                        <div class="small opacity-75 fw-normal">Checkout Transaksi SPK #<?= $nospk ?></div>
                    </div>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Tutup"></button>
            </div>

            <div class="modal-body p-4 bg-light"
                style="background-image: radial-gradient(#e2e8f0 0.5px, transparent 0.5px); background-size: 20px 20px;">
                <!-- Step Indicator (Reduced Margin) -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div
                            class="bg-white rounded-4 p-3 shadow-sm d-flex align-items-center border-start border-5 border-success">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle me-3 d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px; min-width: 40px;">
                                <i class="fas fa-check-double"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="small fw-800 text-success text-uppercase mb-0"
                                    style="font-size: 0.65rem; letter-spacing: 1px;">FINAL STEP: KONFIRMASI & BAYAR
                                </div>
                                <div class="fw-bold text-dark opacity-75 small">Tinjau rincian biaya dan selesaikan
                                    transaksi.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Kolom Kiri: RINCIAN BIAYA -->
                    <div class="col-lg-4">
                        <div class="form-section h-100 shadow-sm border-0">
                            <h6 class="mb-3"><i class="fas fa-calculator me-2"></i>Rincian Biaya</h6>

                            <input type="hidden" name="nospk" value="<?= $nospk ?>">
                            <input type="hidden" name="konsumen_id" value="<?= $getdata['konsumen_id'] ?>">
                            <input type="hidden" name="deskprint_id" value="<?= $getdata['deskprint_id'] ?>">
                            <input type="hidden" name="id_jenis_transaksi"
                                value="<?= $getdata['id_jenis_transaksi'] ?>">
                            <input type="hidden" name="marketplace_id" value="<?= $getdata['marketplace_id'] ?: 0 ?>">
                            <input type="hidden" name="pembayaran_tgl" value="<?= $hariini ?>">
                            <input type="hidden" name="pembayaran_jam" value="<?= $jamini ?>">

                            <div class="mb-3">
                                <label class="info-label">SUBTOTAL ITEM</label>
                                <div class="premium-input-group">
                                    <i class="fas fa-wallet"></i>
                                    <input type="text" name="sub_total" id="sub_total" value="<?= $subTotal ?>"
                                        readonly>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="info-label text-danger">DISKON TRANSAKSI</label>
                                <div class="premium-input-group" style="border-left: 4px solid #e74c3c;">
                                    <i class="fas fa-minus-circle text-danger"></i>
                                    <input type="text" id="diskon" name="diskon" class="text-danger" 
                                        value="<?= isset($pending_payment->diskon_total) && $pending_payment->diskon_total > 0 ? number_format($pending_payment->diskon_total, 0, ',', '.') : '0' ?>"
                                        placeholder="0">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="info-label text-info">BIAYA ONGKIR</label>
                                <div class="premium-input-group" style="border-left: 4px solid #3498db;">
                                    <i class="fas fa-truck text-info"></i>
                                    <input type="text" id="ongkir" name="ongkir" class="text-info" 
                                        value="<?= isset($pending_payment->ongkir) && $pending_payment->ongkir > 0 ? number_format($pending_payment->ongkir, 0, ',', '.') : '0' ?>"
                                        placeholder="0">
                                </div>
                            </div>

                            <div class="grand-total-box py-3 px-2">
                                <div class="small text-uppercase fw-800 opacity-50 mb-1">TOTAL AKHIR</div>
                                <div class="d-flex align-items-center justify-content-center">
                                    <span class="fs-4 fw-bold me-1 text-white-50">Rp</span>
                                    <input type="text" id="grandtotal" name="grandtotal"
                                        class="border-0 bg-transparent text-white text-center fw-800 fs-2 p-0"
                                        value="<?= $subTotal ?>" style="pointer-events: none; width: 100%;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan: METODE PEMBAYARAN -->
                    <div class="col-lg-8">
                        <div class="form-section h-100 shadow-sm border-0">
                            <h6 class="mb-3"><i class="fas fa-credit-card me-2"></i>Metode Pembayaran</h6>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="info-label">CABANG PENERIMA</label>
                                    <?php
                                    $perusahaan_session = $this->session->userdata('perusahaan_id');
                                    ?>
                                    <div class="premium-input-group">
                                        <i class="fas fa-building"></i>
                                        <select name="perusahaan_id"
                                            class="border-0 bg-transparent fw-bold w-100 focus-none">
                                            <?php foreach ($companies as $c): ?>
                                                <option value="<?= $c->id_perusahaan ?>"
                                                    <?= ($perusahaan_session == $c->id_perusahaan) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($c->nama_perusahaan) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="info-label">PILIH CARA BAYAR</label>
                                    <div class="premium-input-group border-primary" style="border-width: 2px;">
                                        <i class="fas fa-money-check-alt text-primary"></i>
                                        <select name="metode" id="metodeBayar"
                                            class="border-0 bg-transparent fw-bold w-100 text-primary" required>
                                            <option value="">-- Pilih Metode --</option>
                                            <option value="cash">💰 TUNAI / CASH</option>
                                            <option value="transfer">🏦 TRANSFER BANK</option>
                                            <option value="edc">💳 EDC</option>
                                            <option value="qris">📱 QRIS</option>
                                            <option value="ewallet">📱 E-WALLET</option>
                                            <option value="piutang">🤝 PIUTANG (DP)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Detail Area -->
                            <div class="payment-method-card shadow-sm border-dashboard"
                                style="min-height: 200px; padding: 1.25rem;">
                                <!-- Payment Icon & Info (Dynamic) -->
                                <div id="paymentMethodInfo" class="text-center py-1 d-none">
                                    <div id="methodIcon" class="mb-2 fs-2 animate__animated animate__zoomIn"></div>
                                    <h6 id="methodTitle" class="fw-800 text-dark mb-1"></h6>
                                    <p id="methodDescription" class="text-muted small mx-auto mb-2"
                                        style="max-width: 300px;"></p>
                                    <hr class="my-2 opacity-5">
                                </div>

                                <!-- Default Placeholder -->
                                <div id="paymentPlaceholder" class="text-center py-4">
                                    <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                        style="width: 70px; height: 70px;">
                                        <i class="fas fa-hand-holding-usd fa-2x text-muted opacity-50"></i>
                                    </div>
                                    <h6 class="fw-bold text-muted mb-1">Siap Menerima Pembayaran</h6>
                                    <p class="text-muted small">Pilih metode di atas untuk melanjutkan.</p>
                                </div>

                                <!-- FORM INPUTS (Dynamic) -->
                                <div class="metode-input d-none" id="cashForm">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="info-label">JUMLAH DITERIMA</label>
                                            <div class="premium-input-group" style="padding: 0.75rem;">
                                                <input type="text" name="bayar_tunai"
                                                    class="fs-4 text-success p-0 fw-bold" placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="info-label">KEMBALIAN</label>
                                            <div class="premium-input-group bg-light border-0"
                                                style="padding: 0.75rem;">
                                                <input type="text" id="kembalian" name="kembalian"
                                                    class="fs-4 text-muted p-0 fw-bold" value="0" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="metode-input d-none" id="transferForm">
                                    <div class="row g-3">
                                        <div class="col-md-5">
                                            <label class="info-label">JUMLAH TRANSFER</label>
                                            <div class="premium-input-group mb-1">
                                                <input type="text" name="bayar_transfer" id="bayar_transfer"
                                                    class="text-primary fs-5" placeholder="Rp 0">
                                            </div>
                                            <div class="small text-muted d-flex align-items-center"
                                                style="font-size: 0.7rem;">
                                                <i class="fas fa-info-circle me-1"></i>Max: <span id="maxTransfer"
                                                    class="fw-bold ms-1 text-dark">Rp 0</span>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <label class="info-label">REKENING TUJUAN</label>
                                            <div class="premium-input-group">
                                                <i class="fas fa-university me-2 text-muted"></i>
                                                <select name="rekening_id"
                                                    class="border-0 bg-transparent fw-bold w-100">
                                                    <option value="0">-- Pilih Rekening --</option>
                                                    <?php foreach ($rekening as $rek): ?>
                                                        <option value="<?= $rek->id ?>"><?= $rek->no_rekening ?>
                                                            (<?= $rek->atas_nama ?>)</option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="metode-input d-none" id="edcForm">
                                    <div class="row g-3">
                                        <div class="col-md-5">
                                            <label class="info-label">JUMLAH GESEK</label>
                                            <div class="premium-input-group mb-1">
                                                <input type="text" name="bayar_debit" id="bayar_debit"
                                                    class="text-primary fs-5" placeholder="Rp 0">
                                            </div>
                                            <div class="small text-muted d-flex align-items-center"
                                                style="font-size: 0.7rem;">
                                                <i class="fas fa-info-circle me-1"></i>Max: <span id="maxDebit"
                                                    class="fw-bold ms-1 text-dark">Rp 0</span>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <label class="info-label">TERMINAL EDC</label>
                                            <div class="premium-input-group">
                                                <i class="fas fa-tablet-alt me-2 text-muted"></i>
                                                <select name="id_edc" class="border-0 bg-transparent fw-bold w-100">
                                                    <option value="0">-- Pilih EDC --</option>
                                                    <?php if (isset($edc)): ?>
                                                        <?php foreach ($edc as $m_edc): ?>
                                                            <option value="<?= $m_edc->edc_id ?>">🏦 <?= $m_edc->edc_nama ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="metode-input d-none" id="ewalletForm">
                                    <div class="row g-3">
                                        <div class="col-md-5">
                                            <label class="info-label">JUMLAH SCAN</label>
                                            <div class="premium-input-group mb-1">
                                                <input type="text" name="bayar_ewallet" id="bayar_ewallet"
                                                    class="text-primary fs-5" placeholder="Rp 0">
                                            </div>
                                            <div class="small text-muted d-flex align-items-center"
                                                style="font-size: 0.7rem;">
                                                <i class="fas fa-info-circle me-1"></i>Max: <span id="maxEwallet"
                                                    class="fw-bold ms-1 text-dark">Rp 0</span>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <label class="info-label">APLIKASI</label>
                                            <div class="premium-input-group">
                                                <i class="fas fa-mobile-alt me-2 text-muted"></i>
                                                <select name="ewallet_id" class="border-0 bg-transparent fw-bold w-100">
                                                    <option value="0">-- Pilih Aplikasi --</option>
                                                    <?php foreach ($ewallet as $rek): ?>
                                                        <option value="<?= $rek->id ?>"><?= $rek->ewallet_nama ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="metode-input d-none" id="qrisForm">
                                    <div class="row g-3">
                                        <div class="col-md-5">
                                            <label class="info-label">JUMLAH SCAN QRIS</label>
                                            <div class="premium-input-group mb-1">
                                                <input type="text" name="bayar_qris" id="bayar_qris"
                                                    class="text-primary fs-5" placeholder="Rp 0">
                                            </div>
                                            <div class="small text-muted d-flex align-items-center"
                                                style="font-size: 0.7rem;">
                                                <i class="fas fa-info-circle me-1"></i>Max: <span id="maxQris"
                                                    class="fw-bold ms-1 text-dark">Rp 0</span>
                                            </div>
                                            
                                            <!-- QR Code Display -->
                                            <div class="mt-3 text-center border rounded p-2 bg-white">
                                                <div class="fw-bold text-dark small mb-2">Scan untuk Membayar</div>
                                                <img id="qris_image_display" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=PILIH_REKENING_DAHULU" alt="QRIS Demo" class="img-fluid border" style="width: 150px; height: 150px; object-fit: contain;">
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <label class="info-label">REKENING TUJUAN</label>
                                            <div class="premium-input-group">
                                                <i class="fas fa-university me-2 text-muted"></i>
                                                <select name="rekening_qris_id" id="rekening_qris_id" class="border-0 bg-transparent fw-bold w-100">
                                                    <option value="0" data-qris="">-- Pilih Rekening --</option>
                                                    <?php foreach ($rekening as $rek): ?>
                                                        <option value="<?= $rek->id ?>" data-qris="<?= !empty($rek->qris_image) ? base_url('assets/img/qris/' . $rek->qris_image) : '' ?>"><?= $rek->no_rekening ?>
                                                            (<?= $rek->atas_nama ?>)</option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="metode-input d-none" id="piutangForm">
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="info-label">DP (UANG MUKA)</label>
                                            <div class="premium-input-group" style="padding: 0.75rem;">
                                                <input type="text" name="bayar_dp" class="fs-4 text-success p-0 fw-bold"
                                                    placeholder="Rp 0">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="info-label">SISA PIUTANG</label>
                                            <div class="premium-input-group bg-danger bg-opacity-10 border-danger border-opacity-25"
                                                style="padding: 0.75rem;">
                                                <input type="text" name="piutang" class="fs-4 text-danger p-0 fw-bold"
                                                    value="0" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="info-label">METODE BAYAR DP</label>
                                            <div class="premium-input-group border-info">
                                                <i class="fas fa-wallet text-info"></i>
                                                <select name="metode_dp" id="metodeDp"
                                                    class="border-0 bg-transparent fw-bold w-100 text-info">
                                                    <option value="cash">💰 TUNAI / CASH</option>
                                                    <option value="transfer">🏦 TRANSFER BANK</option>
                                                    <option value="edc">💳 EDC / QRIS</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 d-none" id="dpTransferField">
                                            <label class="info-label">REKENING DP</label>
                                            <div class="premium-input-group">
                                                <select name="rekening_id_dp"
                                                    class="border-0 bg-transparent fw-bold w-100">
                                                    <option value="0">-- Pilih Rekening --</option>
                                                    <?php foreach ($rekening as $rek): ?>
                                                        <option value="<?= $rek->id ?>"><?= $rek->no_rekening ?>
                                                            (<?= $rek->atas_nama ?>)</option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 d-none" id="dpEdcField">
                                            <label class="info-label">TERMINAL EDC DP</label>
                                            <div class="premium-input-group">
                                                <select name="id_edc_dp" class="border-0 bg-transparent fw-bold w-100">
                                                    <option value="0">-- Pilih EDC --</option>
                                                    <option value="1">🏦 EDC BCA / QRIS</option>
                                                    <option value="2">🏦 EDC BRI / QRIS</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-white p-3 border-top shadow-sm justify-content-end">
                <button type="button"
                    class="btn btn-light px-4 py-2 border rounded-3 fw-bold text-muted transition-all me-3"
                    data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>BATAL
                </button>
                <button type="submit" class="btn btn-premium btn-premium-success px-5 py-3 shadow-lg">
                    <i class="fas fa-save me-2"></i>SIMPAN & CETAK STRUK
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Single Reusable Modal for Approval Edit -->
<div class="modal fade" id="masterApproveModal" tabindex="-1" aria-labelledby="masterApproveModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg overflow-hidden">
            <div class="modal-header border-0 text-white" style="background: var(--primary-gradient);">
                <h5 class="modal-title w-100 text-center fw-bold" id="masterApproveModalLabel">
                    <i class="fas fa-edit me-2"></i>Status Approval
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="<?= base_url('transaksi/update_approve') ?>" method="post">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="editApproveId">
                    <input type="hidden" name="nospk" value="<?= $nospk ?>">

                    <div class="text-center mb-4">
                        <div class="badge bg-light text-dark mb-2 px-3 py-2 border">PRODUK</div>
                        <div class="fw-bold text-dark fs-5" id="editApproveProductName">
                            <!-- Product Name via JS -->
                        </div>
                    </div>

                    <div class="form-section mb-0 shadow-none border bg-light">
                        <label for="editApproveStatus" class="info-label mb-2"><i
                                class="fas fa-check-circle me-1"></i>PILIH
                            STATUS</label>
                        <select class="form-select form-select-lg fw-bold border-0 shadow-sm" id="editApproveStatus"
                            name="approve">
                            <option value="0">🧪 Sampel Dulu</option>
                            <option value="1">🚀 Cetak Semua</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-premium btn-premium-primary px-4">
                        <i class="fas fa-save me-2"></i>SIMPAN
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function formatRupiah(angka, prefix = "Rp") {
        const number = angka.replace(/[^,\d]/g, "");
        const [int, dec] = number.split(",");
        const sisa = int.length % 3;
        let rupiah = int.substring(0, sisa);
        const ribuan = int.substring(sisa).match(/\d{3}/g);
        if (ribuan) rupiah += (sisa ? "." : "") + ribuan.join(".");
        return prefix + " " + (dec !== undefined ? rupiah + "," + dec : rupiah);
    }

    function toAngka(rupiah) {
        return parseInt(rupiah.replace(/[^0-9]/g, "")) || 0;
    }

    function hitungGrandTotal() {
        const subTotal = toAngka(document.getElementById("sub_total").value);
        const diskon = toAngka(document.getElementById("diskon").value);
        const ongkir = toAngka(document.getElementById("ongkir").value);
        const total = subTotal + ongkir - diskon;
        document.getElementById("grandtotal").value = formatRupiah(total.toString());
        hitungKembalian();
    }

    function hitungKembalian() {
        const grandTotal = toAngka(document.getElementById("grandtotal").value);
        const metode = document.getElementById('metodeBayar').value;
        let totalBayar = 0;

        const activeForm = document.querySelector('.metode-input:not(.d-none)');
        if (activeForm) {
            if (metode === 'piutang') {
                const bayarDP = activeForm.querySelector('input[name="bayar_dp"]');
                totalBayar = bayarDP ? toAngka(bayarDP.value) : 0;
            } else if (metode === 'cash') {
                const bayarTunai = activeForm.querySelector('input[name="bayar_tunai"]');
                totalBayar = bayarTunai ? toAngka(bayarTunai.value) : 0;
            } else if (metode === 'transfer') {
                const bayarTransfer = activeForm.querySelector('input[name="bayar_transfer"]');
                totalBayar = bayarTransfer ? toAngka(bayarTransfer.value) : 0;
            } else if (metode === 'edc') {
                const bayarDebit = activeForm.querySelector('input[name="bayar_debit"]');
                totalBayar = bayarDebit ? toAngka(bayarDebit.value) : 0;
            } else if (metode === 'ewallet') {
                const bayarEwallet = activeForm.querySelector('input[name="bayar_ewallet"]');
                totalBayar = bayarEwallet ? toAngka(bayarEwallet.value) : 0;
            } else if (metode === 'qris') {
                const bayarQris = activeForm.querySelector('input[name="bayar_qris"]');
                totalBayar = bayarQris ? toAngka(bayarQris.value) : 0;
            }
        }

        const kembali = Math.max(totalBayar - grandTotal, 0);
        const piutang = Math.max(grandTotal - totalBayar, 0);

        const kembalianInput = document.getElementById("kembalian");
        if (kembalianInput) {
            kembalianInput.value = formatRupiah(kembali.toString());
        }

        const piutangInput = document.querySelector('input[name="piutang"]');
        if (piutangInput && metode === 'piutang') {
            piutangInput.value = formatRupiah(piutang.toString());
        }
    }

    function validatePaymentAmount(inputName, inputElement, maxAmount) {
        const inputValue = toAngka(inputElement.value);
        const isValid = inputValue <= maxAmount && inputValue >= 0;

        if (inputValue > maxAmount) {
            inputElement.classList.add('is-invalid');
            inputElement.classList.remove('is-valid');
            return false;
        } else if (inputValue > 0) {
            inputElement.classList.remove('is-invalid');
            inputElement.classList.add('is-valid');
        } else {
            inputElement.classList.remove('is-invalid', 'is-valid');
        }

        return isValid;
    }

    function updateMaxAmountDisplay() {
        const grandTotal = toAngka(document.getElementById("grandtotal").value);
        const maxAmountDisplay = formatRupiah(grandTotal.toString());

        // Update max amount displays
        const maxTransfer = document.getElementById('maxTransfer');
        const maxDebit = document.getElementById('maxDebit');
        const maxEwallet = document.getElementById('maxEwallet');
        const maxQris = document.getElementById('maxQris');

        if (maxTransfer) maxTransfer.textContent = maxAmountDisplay;
        if (maxDebit) maxDebit.textContent = maxAmountDisplay;
        if (maxEwallet) maxEwallet.textContent = maxAmountDisplay;
        if (maxQris) maxQris.textContent = maxAmountDisplay;
    }

    function setupInputHandlers() {
        const inputs = ['diskon', 'ongkir', 'bayar_tunai', 'bayar_transfer', 'bayar_debit', 'bayar_ewallet', 'bayar_qris', 'bayar_dp'];
        inputs.forEach(id => {
            const input = document.querySelector(`[name="${id}"]`);
            if (input) {
                input.addEventListener('input', function () {
                    const angka = this.value.replace(/[^0-9]/g, '');
                    this.value = formatRupiah(angka);

                    if (id === 'diskon' || id === 'ongkir') {
                        hitungGrandTotal();
                        updateMaxAmountDisplay();
                    } else {
                        hitungKembalian();

                        // Validate payment amount against grand total
                        const grandTotal = toAngka(document.getElementById("grandtotal").value);
                        if (id === 'bayar_transfer' || id === 'bayar_debit' || id === 'bayar_ewallet' || id === 'bayar_qris') {
                            validatePaymentAmount(id, this, grandTotal);
                        }
                    }
                });

                input.addEventListener('keypress', function (e) {
                    if (!/\d/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'Tab') {
                        e.preventDefault();
                    }
                });

                // Validate on blur
                input.addEventListener('blur', function () {
                    const grandTotal = toAngka(document.getElementById("grandtotal").value);
                    if (id === 'bayar_transfer' || id === 'bayar_debit' || id === 'bayar_ewallet' || id === 'bayar_qris') {
                        validatePaymentAmount(id, this, grandTotal);
                    }
                });
            }
        });
    }

    document.getElementById('metodeBayar').addEventListener('change', function () {
        document.querySelectorAll('.metode-input').forEach(el => el.classList.add('d-none'));
        const selectedForm = document.getElementById(this.value + 'Form');
        const paymentInfo = document.getElementById('paymentMethodInfo');
        const methodIcon = document.getElementById('methodIcon');
        const methodTitle = document.getElementById('methodTitle');
        const methodDescription = document.getElementById('methodDescription');
        const paymentPlaceholder = document.getElementById('paymentPlaceholder');

        // Clear payment info and reset placeholder visibility
        paymentInfo.classList.add('d-none');
        if (paymentPlaceholder) paymentPlaceholder.classList.remove('d-none');

        // --- RESET DATA (Clean residual values when switching methods) ---
        const paymentDataToReset = [
            'bayar_tunai', 'bayar_transfer', 'bayar_debit', 'bayar_ewallet',
            'bayar_qris', 'bayar_dp', 'piutang', 'kembalian'
        ];
        paymentDataToReset.forEach(name => {
            const el = document.querySelector(`[name="${name}"]`);
            if (el) {
                if (['piutang', 'kembalian'].includes(name)) el.value = 'Rp 0';
                else el.value = '';
            }
        });

        const selectsToReset = [
            'rekening_id', 'id_edc', 'ewallet_id', 'rekening_qris_id',
            'metode_dp', 'rekening_id_dp', 'id_edc_dp'
        ];
        selectsToReset.forEach(name => {
            const el = document.querySelector(`[name="${name}"]`);
            if (el) {
                if (name === 'metode_dp') el.value = 'cash';
                else el.value = '0';
            }
        });

        // Clear validation states
        document.querySelectorAll('.is-invalid, .is-valid').forEach(el => {
            el.classList.remove('is-invalid', 'is-valid');
        });
        // -----------------------------------------------------------------

        if (this.value) {
            if (paymentPlaceholder) paymentPlaceholder.classList.add('d-none');
        }

        if (selectedForm) {
            selectedForm.classList.remove('d-none');

            // --- Logic Khusus untuk Metode Piutang (DP Sub-methods) ---
            if (this.value === 'piutang') {
                const metodeDp = document.getElementById('metodeDp');
                const updateDpFields = function () {
                    const dpType = metodeDp.value;
                    const dpTransferField = document.getElementById('dpTransferField');
                    const dpEdcField = document.getElementById('dpEdcField');

                    // Reset visibilitas
                    if (dpTransferField) dpTransferField.classList.add('d-none');
                    if (dpEdcField) dpEdcField.classList.add('d-none');

                    if (dpType === 'transfer' && dpTransferField) {
                        dpTransferField.classList.remove('d-none');
                    } else if (dpType === 'edc' && dpEdcField) {
                        dpEdcField.classList.remove('d-none');
                    }
                };

                metodeDp.removeEventListener('change', updateDpFields);
                metodeDp.addEventListener('change', updateDpFields);
                updateDpFields();
            }
            // ---------------------------------------------------------

            // Show payment method info
            const methodData = {
                cash: {
                    icon: '💰',
                    title: 'Pembayaran Tunai',
                    description: 'Pembayaran langsung dengan uang tunai'
                },
                transfer: {
                    icon: '🏦',
                    title: 'Transfer Bank',
                    description: 'Pembayaran melalui transfer bank'
                },
                edc: {
                    icon: '💳',
                    title: 'EDC',
                    description: 'Pembayaran dengan kartu debit/kredit'
                },
                qris: {
                    icon: '📱',
                    title: 'QRIS',
                    description: 'Scan kode QR untuk membayar'
                },
                ewallet: {
                    icon: '📱',
                    title: 'E-Wallet',
                    description: 'Pembayaran menggunakan dompet digital'
                },
                piutang: {
                    icon: '🤝',
                    title: 'Pembayaran Piutang',
                    description: 'Pembayaran sebagian dengan sisa piutang'
                }
            };

            if (methodData[this.value]) {
                methodIcon.textContent = methodData[this.value].icon;
                methodTitle.textContent = methodData[this.value].title;
                methodDescription.textContent = methodData[this.value].description;
                paymentInfo.classList.remove('d-none');
            }

            // AUTOFILL LOGIC: Otomatis isi jumlah bayar sesuai total tagihan
            if (this.value !== 'piutang' && this.value !== '') {
                // Ambil nilai Grand Total (sudah terformat Rp)
                const grandTotalVal = document.getElementById("grandtotal").value;

                // Cari input aktif dalam form yang terpilih
                const inputSelectors = {
                    'cash': 'input[name="bayar_tunai"]',
                    'transfer': 'input[name="bayar_transfer"]',
                    'edc': 'input[name="bayar_debit"]',
                    'ewallet': 'input[name="bayar_ewallet"]',
                    'qris': 'input[name="bayar_qris"]'
                };

                if (inputSelectors[this.value]) {
                    const targetInput = selectedForm.querySelector(inputSelectors[this.value]);
                    if (targetInput) {
                        // Isi nilai autofill
                        targetInput.value = grandTotalVal;

                        // Fokus dan select text agar mudah diedit jika perlu
                        setTimeout(() => {
                            targetInput.focus();
                            targetInput.select();
                        }, 50);
                    }
                }
            }
        }

        updateMaxAmountDisplay();
        hitungKembalian();
    });

    // Event listener for Rekening QRIS selection
    document.getElementById('rekening_qris_id').addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const qrisUrl = selectedOption.getAttribute('data-qris');
        const qrisImageDisplay = document.getElementById('qris_image_display');
        
        if (qrisImageDisplay) {
            if (qrisUrl) {
                qrisImageDisplay.src = qrisUrl;
            } else {
                qrisImageDisplay.src = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=BELUM_ADA_QRIS";
            }
        }
    });


    document.addEventListener('DOMContentLoaded', () => {
        setupInputHandlers();

        // Modal Logic for Single Reuse
        var masterApproveModal = document.getElementById('masterApproveModal');
        if (masterApproveModal) {
            masterApproveModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var id = button.getAttribute('data-id');
                var status = button.getAttribute('data-status');
                var product = button.getAttribute('data-product');

                masterApproveModal.querySelector('#editApproveId').value = id;
                masterApproveModal.querySelector('#editApproveProductName').textContent = product;
                masterApproveModal.querySelector('#editApproveStatus').value = status;
            });
        }

        // Format initial values
        const subTotal = document.getElementById("sub_total");
        const grandTotal = document.getElementById("grandtotal");
        const diskonEl = document.getElementById("diskon");
        const ongkirEl = document.getElementById("ongkir");
        if (subTotal && subTotal.value) {
            subTotal.value = formatRupiah(subTotal.value);
        }
        if (diskonEl && diskonEl.value) {
            diskonEl.value = formatRupiah(diskonEl.value);
        }
        if (ongkirEl && ongkirEl.value) {
            ongkirEl.value = formatRupiah(ongkirEl.value);
        }
        if (grandTotal && grandTotal.value) {
            grandTotal.value = formatRupiah(grandTotal.value);
        }

        hitungGrandTotal();
        updateMaxAmountDisplay();

        // Add some visual enhancements
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function () {
                this.style.transform = 'translateY(-2px)';
                this.style.transition = 'transform 0.2s ease';
            });

            card.addEventListener('mouseleave', function () {
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>
<script>
    // Sanitize formatted currency fields to plain numbers before submit
    document.getElementById('formBayar').addEventListener('submit', function (e) {
        const toNumber = v => (v || '').toString().replace(/[^0-9]/g, '') || '0';
        const sub = document.getElementById('sub_total');
        const dis = document.getElementById('diskon');
        const ong = document.getElementById('ongkir');
        const grd = document.getElementById('grandtotal');
        if (sub) sub.value = toNumber(sub.value);
        if (dis) dis.value = toNumber(dis.value);
        if (ong) ong.value = toNumber(ong.value);
        if (grd) grd.value = toNumber(grd.value);

        // Validate payment amounts before submit
        const grandTotal = parseInt(grd.value) || 0;
        const metode = document.getElementById('metodeBayar').value;
        let isValid = true;
        let errorMessage = '';

        if (metode === 'cash') {
            const bayarTunaiVal = parseInt(toNumber(document.querySelector('[name="bayar_tunai"]').value)) || 0;
            if (bayarTunaiVal < grandTotal) {
                isValid = false;
                errorMessage = 'Pembayaran TUNAI / CASH tidak boleh kurang dari total tagihan!\n\nJika pelanggan ingin melakukan pembayaran sebagian (DP), silakan ganti Metode Pembayaran menjadi "PIUTANG (DP)".';
                document.querySelector('[name="bayar_tunai"]').focus();
            }
        } else if (metode === 'transfer') {
            const bayarTransfer = document.querySelector('[name="bayar_transfer"]');
            const rekId = document.querySelector('[name="rekening_id"]').value;

            if (rekId === "0" || !rekId) {
                isValid = false;
                errorMessage = 'Silakan pilih Rekening Tujuan transfer!';
                document.querySelector('[name="rekening_id"]').focus();
            } else if (bayarTransfer && bayarTransfer.value) {
                const transferAmount = parseInt(toNumber(bayarTransfer.value)) || 0;
                if (transferAmount > grandTotal) {
                    isValid = false;
                    errorMessage = 'Jumlah transfer tidak boleh melebihi total transaksi!';
                    bayarTransfer.classList.add('is-invalid');
                    bayarTransfer.focus();
                } else if (transferAmount < grandTotal) {
                    isValid = false;
                    errorMessage = 'Pembayaran TRANSFER tidak boleh kurang dari total tagihan!\n\nGunakan metode "PIUTANG (DP)" jika ingin bayar sebagian.';
                    bayarTransfer.classList.add('is-invalid');
                    bayarTransfer.focus();
                }
            }
        } else if (metode === 'edc') {
            const bayarDebit = document.querySelector('[name="bayar_debit"]');
            const edcId = document.querySelector('[name="id_edc"]').value;

            if (edcId === "0" || !edcId) {
                isValid = false;
                errorMessage = 'Silakan pilih Terminal EDC!';
                document.querySelector('[name="id_edc"]').focus();
            } else if (bayarDebit && bayarDebit.value) {
                const debitAmount = parseInt(toNumber(bayarDebit.value)) || 0;
                if (debitAmount > grandTotal) {
                    isValid = false;
                    errorMessage = 'Jumlah EDC tidak boleh melebihi total transaksi!';
                    bayarDebit.classList.add('is-invalid');
                    bayarDebit.focus();
                } else if (debitAmount < grandTotal) {
                    isValid = false;
                    errorMessage = 'Pembayaran EDC tidak boleh kurang dari total tagihan!\n\nGunakan metode "PIUTANG (DP)" jika ingin bayar sebagian.';
                    bayarDebit.classList.add('is-invalid');
                    bayarDebit.focus();
                }
            }
        } else if (metode === 'ewallet') {
            const bayarEwallet = document.querySelector('[name="bayar_ewallet"]');
            const ewalletId = document.querySelector('[name="ewallet_id"]').value;

            if (ewalletId === "0" || !ewalletId) {
                isValid = false;
                errorMessage = 'Silakan pilih Aplikasi E-Wallet!';
                document.querySelector('[name="ewallet_id"]').focus();
            } else if (bayarEwallet && bayarEwallet.value) {
                const ewalletAmount = parseInt(toNumber(bayarEwallet.value)) || 0;
                if (ewalletAmount > grandTotal) {
                    isValid = false;
                    errorMessage = 'Jumlah E-Wallet tidak boleh melebihi total transaksi!';
                    bayarEwallet.classList.add('is-invalid');
                    bayarEwallet.focus();
                } else if (ewalletAmount < grandTotal) {
                    isValid = false;
                    errorMessage = 'Pembayaran E-WALLET tidak boleh kurang dari total tagihan!\n\nGunakan metode "PIUTANG (DP)" jika ingin bayar sebagian.';
                    bayarEwallet.classList.add('is-invalid');
                    bayarEwallet.focus();
                }
            }
        } else if (metode === 'piutang') {
            const bayarDp = document.querySelector('[name="bayar_dp"]');
            const metodeDp = document.getElementById('metodeDp').value;

            if (metodeDp === 'transfer') {
                const rekIdDp = document.querySelector('[name="rekening_id_dp"]').value;
                if (rekIdDp === "0" || !rekIdDp) {
                    isValid = false;
                    errorMessage = 'Silakan pilih Rekening untuk DP Transfer!';
                    document.querySelector('[name="rekening_id_dp"]').focus();
                }
            } else if (metodeDp === 'edc') {
                const edcIdDp = document.querySelector('[name="id_edc_dp"]').value;
                if (edcIdDp === "0" || !edcIdDp) {
                    isValid = false;
                    errorMessage = 'Silakan pilih Terminal EDC untuk DP!';
                    document.querySelector('[name="id_edc_dp"]').focus();
                }
            }
        }

        if (!isValid) {
            e.preventDefault();
            alert(errorMessage);
            return false;
        }
        // sanitize metode-specific amounts
        ['bayar_tunai', 'bayar_transfer', 'bayar_debit', 'bayar_ewallet', 'bayar_dp', 'piutang', 'kembalian'].forEach(name => {
            const el = document.querySelector(`[name="${name}"]`);
            if (el) el.value = toNumber(el.value);
        });
    });
</script>

<!-- External CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/bayar.css') ?>">
<!-- Preview Modal -->
<div class="modal fade" id="modalPreview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg overflow-hidden">
            <div class="modal-header border-0 text-dark" style="background: var(--warning-gradient);">
                <h5 class="modal-title fw-bold d-flex align-items-center">
                    <div class="bg-dark bg-opacity-10 rounded-circle p-2 me-3"
                        style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-eye"></i>
                    </div>
                    <span>Preview Transaksi</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light p-0" id="previewContent">
                <!-- Content via JS -->
            </div>
            <div class="modal-footer bg-white border-0 p-4">
                <div class="d-flex w-100 justify-content-between align-items-center">
                    <button type="button" class="btn btn-light border px-4 fw-bold text-muted" data-bs-dismiss="modal">
                        <i class="fas fa-arrow-left me-2"></i>KEMBALI
                    </button>
                    <div class="d-flex gap-2">
                        <button type="button" id="btnCopy" class="btn btn-outline-info px-3 shadow-sm"
                            title="Copy Text">
                            <i class="fas fa-copy"></i>
                        </button>
                        <button type="button" id="btnDownloadImg" class="btn btn-outline-secondary px-3 shadow-sm"
                            title="Save Image">
                            <i class="fas fa-image"></i>
                        </button>
                        <a href="#" id="btnKirimWA" target="_blank" class="btn btn-success px-4 shadow-sm">
                            <i class="fab fa-whatsapp me-2"></i>WA
                        </a>
                        <button type="button" class="btn btn-premium btn-premium-primary px-4 shadow-sm ms-2"
                            data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#modalHeaderColorPrimary">
                            <i class="fas fa-check-circle me-2"></i>PROSES BAYAR
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    document.getElementById('modalPreview').addEventListener('show.bs.modal', function () {
        const subTotal = document.getElementById('sub_total').value;
        const diskon = document.getElementById('diskon').value;
        const ongkir = document.getElementById('ongkir').value;
        const grandTotal = document.getElementById('grandtotal').value;

        const tableContainer = document.querySelector('.card-body .table-responsive table');
        let tableHTML = '';

        if (tableContainer) {
            const clone = tableContainer.cloneNode(true);

            // Transform inputs to text
            clone.querySelectorAll('input, select, textarea').forEach(el => {
                if (el.type === 'hidden') {
                    el.remove();
                } else {
                    const val = el.value;
                    const textNode = document.createTextNode(val);
                    if (el.parentNode) el.parentNode.replaceChild(textNode, el);
                }
            });

            // Remove buttons and forms (actions)
            clone.querySelectorAll('button, form, .btn').forEach(el => el.remove());

            tableHTML = clone.innerHTML;
        }

        const html = `
        <div class="p-4 bg-white" id="captureArea">
            <div class="d-flex align-items-center mb-4 p-3 rounded-3 bg-primary bg-opacity-10 border-start border-4 border-primary d-print-none">
                <div class="fs-4 me-3 text-primary"><i class="fas fa-info-circle"></i></div>
                <div>
                    <div class="fw-bold text-dark">Review Detail Transaksi</div>
                    <div class="small text-muted">Pastikan seluruh rincian pesanan dan total biaya sudah benar.</div>
                </div>
            </div>
            
            <div class="report-section-card mb-4 border border-light">
                <div class="section-header bg-dark py-2">    
                    <h6 class="fw-bold text-white m-0 d-flex align-items-center">
                        <i class="fas fa-list me-2"></i> RINCIAN ITEM PESANAN
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 text-nowrap align-middle">
                            ${tableHTML}
                        </table>
                    </div>
                </div>
            </div>

            <div class="report-section-card border-primary border-opacity-25">
                <div class="card-body p-4 bg-light bg-opacity-50">
                    <div class="row align-items-center">
                        <div class="col-md-5 text-muted small border-end d-none d-md-block">
                            <div class="mb-2"><i class="fas fa-shield-alt text-success me-2"></i>Transaksi Aman & Terverifikasi</div>
                            <div class="mb-2"><i class="fas fa-print text-primary me-2"></i>Struk otomatis tercetak usai bayar</div>
                            <div><i class="fas fa-check-double text-info me-2"></i>Detail pesanan telah tervalidasi</div>
                        </div>
                        <div class="col-md-7 ps-md-4">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted fw-bold pb-2">SUBTOTAL</td>
                                    <td class="text-end fw-bold text-dark pb-2 fs-6">${subTotal}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-bold pb-2">DISKON</td>
                                    <td class="text-end fw-bold text-danger pb-2">-${diskon}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-bold pb-2">ONGKIR</td>
                                    <td class="text-end fw-bold text-primary pb-2">+${ongkir}</td>
                                </tr>
                                <tr class="border-top border-2">
                                    <td class="pt-3 fw-800 text-dark">TOTAL AKHIR</td>
                                    <td class="text-end fw-800 text-success fs-3 pt-2">${grandTotal}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `;

        document.getElementById('previewContent').innerHTML = html;

        // WhatsApp Logic Variables
        const phoneOrig = "<?= $getdata['konsumen_nohp'] ?>";
        let phone = phoneOrig.trim();
        if (phone.startsWith('0')) {
            phone = '62' + phone.substring(1);
        }
        const name = "<?= $getdata['konsumen_nama'] ?>";
        const nospk = "<?= $nospk ?>";
        const trackingLink = "<?= base_url('cekpesanan/spk/' . urlencode(encrypt_spk($nospk))) ?>";

        let items = "";
        if (tableContainer) {
            const rows = tableContainer.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const cols = row.querySelectorAll('td');
                if (cols.length > 3) {
                    const nameEl = cols[1].querySelector('div.fw-bold');
                    let pName = nameEl ? nameEl.innerText.trim() : cols[1].innerText.split('\n')[0].trim();

                    // Ambil info variasi dari badge
                    let varText = [];
                    const badges = cols[1].querySelectorAll('.badge:not([title="Lokasi Produksi"])');
                    badges.forEach(b => {
                        let txt = b.innerText.trim();
                        if (txt && !txt.toUpperCase().includes('SAMPEL') && !txt.toUpperCase().includes('CETAK SEMUA')) {
                            let parts = txt.split(':');
                            if (parts.length > 1) {
                                txt = parts.slice(1).join(':').trim();
                            }
                            varText.push(txt);
                        }
                    });
                    let sisiSub = varText.length > 0 ? " [" + varText.join(", ") + "]" : "";
                    let pQty = cols[3].innerText.replace(/\s+/g, ' ').trim();
                    let pHarga = cols[2] ? cols[2].innerText.replace(/\s+/g, ' ').trim() : '';
                    items += `• ${pName}${sisiSub} (${pQty} @ ${pHarga})\n`;
                }
            });
        }

        const waMsg = `Halo Kak *${name}*,\nBerikut rincian pesanan SPK *#${nospk}*:\n\n${items}\nSubtotal: ${subTotal}\nDiskon: -${diskon}\nOngkir: +${ongkir}\n------------------\n*Total Tagihan: ${grandTotal}*\n\nPantau status pesanan dan invoice digital Anda di sini:\n${trackingLink}\n\n❗ Mohon pastikan seluruh detail pesanan sudah sesuai.\nProses produksi akan dilakukan setelah pembayaran diterima.\nTerima kasih 🙏`;

        const waUrl = `https://wa.me/${phone}?text=${encodeURIComponent(waMsg)}`;
        const btnWA = document.getElementById('btnKirimWA');
        if (btnWA) btnWA.href = waUrl;

        // Copy Text Logic
        const btnCopy = document.getElementById('btnCopy');
        if (btnCopy) {
            btnCopy.onclick = function () {
                navigator.clipboard.writeText(waMsg).then(function () {
                    const originalHtml = btnCopy.innerHTML;
                    btnCopy.innerHTML = '<i class="fas fa-check"></i>';
                    setTimeout(() => { btnCopy.innerHTML = originalHtml; }, 1500);
                }, function (err) {
                    alert('Gagal menyalin teks: ' + err);
                });
            }
        }

        // Capture Image Logic
        const btnDownload = document.getElementById('btnDownloadImg');
        if (btnDownload) {
            btnDownload.onclick = function () {
                const captureElement = document.getElementById('captureArea') || document.getElementById('previewContent');

                html2canvas(captureElement, {
                    scale: 2,
                    backgroundColor: "#ffffff",
                    ignoreElements: (element) => element.classList.contains('d-print-none')
                }).then(canvas => {
                    const link = document.createElement('a');
                    link.download = `Preview-SPK-${nospk}.png`;
                    link.href = canvas.toDataURL("image/png");
                    link.click();
                }).catch(err => {
                    alert("Gagal membuat gambar: " + err);
                });
            }
        }
    });
</script>

<?php $this->load->view('admin/transaksi/components/v_modal_panduan_bayar'); ?>
