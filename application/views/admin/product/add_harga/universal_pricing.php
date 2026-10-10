<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="content-card-pro border-0 shadow-sm" style="background: transparent;">
    <?php if (!isset($hide_tracker) || !$hide_tracker): ?>
    <!-- Mini Step Tracker (Final Step) -->
    <div class="mini-sop-tracker mb-4 mt-2">
        <div class="mini-step completed">
            <div class="step-num">0</div>
            <div class="step-text">Config Form</div>
        </div>
        <div class="step-line"></div>
        <div class="mini-step completed">
            <div class="step-num">1</div>
            <div class="step-text">Identitas</div>
        </div>
        <div class="step-line"></div>
        <div class="mini-step completed">
            <div class="step-num">2</div>
            <div class="step-text">Resep BOM</div>
        </div>
        <div class="step-line"></div>
        <div class="mini-step completed">
            <div class="step-num">3</div>
            <div class="step-text">Alur Kerja</div>
        </div>
        <div class="step-line"></div>
        <div class="mini-step active">
            <div class="step-num">4</div>
            <div class="step-text">Matriks Harga</div>
        </div>
    </div>
    <?php endif; ?>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
        <?php if (!isset($hide_tracker) || !$hide_tracker): ?>
        <h5 class="fw-bold m-0 text-slate-900">
            <i class="bi bi-currency-dollar me-2 text-primary-pro"></i>Matriks Skala Harga Jual
        </h5>
        <?php else: ?>
        <h6 class="fw-bold m-0 text-primary text-uppercase letter-spacing-1">
            <i class="bi bi-table me-2"></i> Manajemen Skala Harga
        </h6>
        <?php endif; ?>
        
        <div class="d-flex flex-wrap gap-2 justify-content-md-end align-items-center">
            <?php if ($showSide): ?>
                <button type="button" class="btn btn-sm btn-info text-white rounded-pill shadow-sm px-3 fw-bold" onclick="cloneToSide2()">
                    <i class="bi bi-layers me-1"></i> Auto 2 Sisi
                </button>
            <?php endif; ?>
            

            
            <button type="button" class="btn btn-sm btn-primary rounded-pill shadow-sm px-3 fw-bold" onclick="addNewPriceRow()">
                <i class="bi bi-plus-lg me-1"></i> Tambah Range
            </button>
            
            <div class="btn-group shadow-sm rounded-pill">
                <button type="button" class="btn btn-sm btn-danger px-3 fw-bold" onclick="bulkDeletePrice()" style="border-top-left-radius: 50rem; border-bottom-left-radius: 50rem;">
                    <i class="bi bi-trash3 me-1"></i> Hapus
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger fw-bold" onclick="detectDuplicates()" title="Cari & Tandai Duplikat" style="border-top-right-radius: 50rem; border-bottom-right-radius: 50rem; background-color: #f8d7da;">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
    </div>

    <?php
    // Priority: Product > Subcat > Category
    $raw_schema = $getProduct['product_form_schema'] ?? $getProduct['subcategory_form_schema'] ?? $getProduct['category_form_schema'] ?? '';
    $schema = !empty($raw_schema) ? json_decode($raw_schema, true) : [];

    // Pricing Dimensions (Max 3 based on tbl_harga_product columns)
    $dimensions = [];

    if (!empty($schema)) {
        foreach ($schema as $field) {
            // Only fields with predefined options can be used as pricing dimensions
            if (isset($field['options']) && !empty($field['options']) && in_array($field['type'], ['select', 'radio'])) {
                $dimensions[] = [
                    'name' => $field['name'],
                    'label' => strtoupper($field['label']),
                    'options' => $field['options']
                ];
            }
        }
    }

    $extraDimensions = [];
    if (!empty($dimensions) && count($dimensions) > 3) {
        $extraDimensions = array_slice($dimensions, 3);
    }

    // Mapping Dimensions to Database Columns
    $showSide = false;
    $labelSide = "";
    $schemaSideOptions = [];
    $showMachine = false;
    $labelMachine = "";
    $schemaMachineOptions = [];
    $showThird = false;
    $labelThird = "";
    $schemaThirdOptions = [];

    if (!empty($dimensions)) {
        $unmapped = [];
        
        foreach ($dimensions as &$dim) {
            $name = $dim['name'] ?? '';
            $labelLower = strtolower($dim['label'] ?? '');
            
            if ($name === 'jenisprint_id' || strpos($labelLower, 'mesin') !== false || strpos($labelLower, 'metode') !== false || strpos($labelLower, 'jenis print') !== false) {
                $showMachine = true;
                $labelMachine = $dim['label'];
                $schemaMachineOptions = $dim['options'];
                $dim['name'] = 'jenisprint_id';
            } elseif ($name === 'side_id') {
                $showThird = true;
                $labelThird = $dim['label'];
                $schemaThirdOptions = $dim['options'];
                $dim['name'] = 'side_id';
            } elseif ($name === 'ket_1' || strpos($labelLower, 'varian') !== false) {
                $showSide = true;
                $labelSide = $dim['label'];
                $schemaSideOptions = $dim['options'];
                $dim['name'] = 'ket_1';
            } else {
                $unmapped[] = $dim;
            }
        }
        
        // Fallbacks for missing mappings
        if (!$showSide && !empty($unmapped)) {
            $first = array_shift($unmapped);
            $showSide = true;
            $labelSide = $first['label'];
            $schemaSideOptions = $first['options'];
        }
        if (!$showMachine && !empty($unmapped)) {
            $second = array_shift($unmapped);
            $showMachine = true;
            $labelMachine = $second['label'];
            $schemaMachineOptions = $second['options'];
        }
        if (!$showThird && !empty($unmapped)) {
            $third = array_shift($unmapped);
            $showThird = true;
            $labelThird = $third['label'];
            $schemaThirdOptions = $third['options'];
        }

        $extraDimensions = $unmapped;
    } else {
        // LEGACY FALLBACK (Only if no schema defined)
        // Di-disable atas permintaan pengguna agar form builder wajib disetting
        /*
        if ($getProduct['side_id'] == 2) {
            $showSide = true;
            $labelSide = "SISI";
        }
        if ($getProduct['kategori_id'] == 2) { // Digital Printing
            $showMachine = true;
            $labelMachine = "JENIS PRINT";
        }
        // DTG/DTF overrides
        if (in_array($getProduct['kategori_id'], [8, 9])) {
            $showSide = true;
            $labelSide = "UKURAN CETAK";
        }
        // Banner override
        if ($getProduct['subkategori_id'] == 13 && isset($getmata)) {
            $showSide = true;
            $labelSide = "PILIHAN MATA";
            foreach ($getmata as $m)
                $schemaSideOptions[$m->id] = $m->mata_nama;
        }
        */
    }
    ?>

    <style>
        #priceMatrixTable {
            counter-reset: rowNumber;
        }
        #priceMatrixTable tbody tr:not(.empty-row) {
            counter-increment: rowNumber;
        }
        #priceMatrixTable tbody tr:not(.empty-row) td.row-number::before {
            content: counter(rowNumber);
            font-weight: 600;
            color: #64748b;
        }
    </style>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <?php if (empty($schema)): ?>
            <div class="alert alert-warning m-3 d-flex align-items-center" role="alert" style="border-radius: 16px; border: 1px solid #fde047; background-color: #fefce8;">
                <i class="bi bi-exclamation-triangle-fill fs-3 me-3 text-warning"></i>
                <div>
                    <h6 class="fw-bold mb-1 text-dark">Form Builder Tidak Terdeteksi</h6>
                    <p class="mb-0 small text-dark opacity-75">Form Builder tidak terdeteksi di Kategori, Sub Kategori, dan Produk. Silakan setting terlebih dahulu di Master Kategori agar matriks variasi sesuai.</p>
                </div>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('manufaktur/action_save_all') ?>" method="post" id="formSavePrices">
            <input type="hidden" name="product_id" value="<?= $getProduct['product_id'] ?>">
            <input type="hidden" name="segmen2" value="<?= $segment2 ?>">

            <div class="table-responsive">
                <table class="table table-sm table-pro mb-0 align-middle" id="priceMatrixTable" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th style="width: 40px;" class="text-center align-middle">
                                <input class="form-check-input shadow-sm" type="checkbox" id="selectAllForBOM" checked onchange="$('.row-bom-checkbox').prop('checked', this.checked)">
                            </th>
                            <th style="width: 40px;" class="text-center text-muted">#</th>
                            <th style="min-width: 180px;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span>KETERANGAN VARIASI</span>
                                    <button type="button" class="btn btn-xs btn-outline-primary py-0 px-1"
                                        onclick="applyVariasiToAll()" title="Terapkan teks baris pertama ke semua baris">
                                        <i class="bi bi-magic"></i>
                                    </button>
                                </div>
                            </th>
                            <?php if ($showSide): ?>
                                <th style="min-width: 100px;"><?= $labelSide ?></th>
                            <?php endif; ?>
                            <?php if ($showMachine): ?>
                                <th style="min-width: 100px;"><?= $labelMachine ?></th>
                            <?php endif; ?>
                            <?php if ($showThird): ?>
                                <th style="min-width: 100px;"><?= $labelThird ?></th>
                            <?php endif; ?>
                            <?php foreach ($extraDimensions as $extDim): ?>
                                <th style="min-width: 100px;"><?= $extDim['label'] ?></th>
                            <?php endforeach; ?>
                            <th style="min-width: 140px;" class="text-center">RENTANG JUMLAH</th>
                            <th style="min-width: 130px;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span>HPP (EST)</span>
                                    <button type="button" class="btn btn-xs btn-outline-primary py-0 px-1"
                                        onclick="applyCalculatedHPPToAll()" title="Terapkan HPP Kalkulasi">
                                        <i class="bi bi-magic"></i>
                                    </button>
                                </div>
                            </th>
                            <th style="min-width: 130px;">HARGA JUAL</th>
                            <th style="min-width: 130px;">BIAYA MINIMAL</th>
                            <th style="min-width: 70px;" class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($getHarga)): ?>
                            <tr class="empty-row text-center">
                                <td colspan="<?= ($showSide ? 1 : 0) + ($showMachine ? 1 : 0) + ($showThird ? 1 : 0) + count($extraDimensions) + 4 ?>"
                                    class="py-5 text-muted">
                                    <div class="py-4">
                                        <i class="bi bi-clipboard2-x fs-1 opacity-25 d-block mb-3"></i>
                                        <p class="mb-0 fw-semibold">Belum ada strategi harga yang dikonfigurasi</p>
                                        <small>Klik "Tambah Range" untuk memulai input harga</small>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            // Pastel colors for visual grouping
                            $pastelColors = ['#ffffff', '#f1f5f9', '#f0fdf4', '#eff6ff', '#fefce8', '#fff1f2', '#fdf4ff', '#fff7ed'];
                            $groupColors = [];
                            $colorIndex = 0;
                            ?>
                            <?php foreach ($getHarga as $hrg): ?>
                                <?php 
                                    // Determine the primary grouping key
                                    $groupKey = '';
                                    if ($showSide) {
                                        $groupKey = $hrg->ket_1;
                                    } elseif ($showMachine) {
                                        $groupKey = $hrg->jenisprint_id;
                                    } elseif (!empty($extraDimensions)) {
                                        $meta = !empty($hrg->metadata) ? json_decode($hrg->metadata, true) : [];
                                        $groupKey = $meta[$extraDimensions[0]['name']] ?? '';
                                    }
                                    
                                    if (!isset($groupColors[$groupKey])) {
                                        $groupColors[$groupKey] = $pastelColors[$colorIndex % count($pastelColors)];
                                        $colorIndex++;
                                    }
                                    $rowBgColor = $groupColors[$groupKey];
                                ?>
                                <tr style="--bs-table-bg: <?= $rowBgColor ?>; --bs-table-striped-bg: <?= $rowBgColor ?>; background-color: <?= $rowBgColor ?> !important;">
                                    <td class="text-center align-middle">
                                        <input class="form-check-input shadow-sm row-bom-checkbox" type="checkbox" value="<?= $hrg->harga_id ?>" checked>
                                    </td>
                                    <td class="text-center align-middle row-number"></td>
                                    <td>
                                        <input type="hidden" name="harga_id[]" value="<?= $hrg->harga_id ?>">
                                        <input type="text" name="detail_product[]" class="form-control form-control-sm border-0 bg-light"
                                            placeholder="Nama variasi..." value="<?= htmlspecialchars($hrg->detail_product) ?>" style="font-size: 0.8rem;">
                                    </td>

                                    <?php if ($showSide): ?>
                                        <td>
                                            <select name="ket_1[]" class="form-select form-select-sm border-0 bg-light variation-select" style="font-size: 0.8rem;">
                                                <option value="">-- <?= ucwords(strtolower($labelSide)) ?> --</option>
                                                <?php if (!empty($schemaSideOptions)): ?>
                                                    <?php foreach ($schemaSideOptions as $val => $label): ?>
                                                        <option value="<?= $val ?>" <?= $val == $hrg->ket_1 ? 'selected' : '' ?>>
                                                            <?= $label ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <?php foreach ($side as $s): ?>
                                                        <option value="<?= $s->side_id ?>" <?= $s->side_id == $hrg->ket_1 ? 'selected' : '' ?>>
                                                            <?= $s->side_name ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </td>
                                    <?php endif; ?>

                                    <?php if ($showMachine): ?>
                                        <td>
                                            <select name="jenisprint_id[]" class="form-select form-select-sm border-0 bg-light variation-select" style="font-size: 0.8rem;">
                                                <option value="">-- <?= ucwords(strtolower($labelMachine)) ?> --</option>
                                                <?php if (!empty($schemaMachineOptions)): ?>
                                                    <?php foreach ($schemaMachineOptions as $val => $label): ?>
                                                        <option value="<?= $val ?>" <?= $val == $hrg->jenisprint_id ? 'selected' : '' ?>>
                                                            <?= $label ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <?php if (isset($mesin_printing)):
                                                        foreach ($mesin_printing as $m): ?>
                                                            <option value="<?= $m->id ?>" <?= $hrg->jenisprint_id == $m->id ? 'selected' : '' ?>>
                                                                <?= $m->mesin_nama ?>
                                                            </option>
                                                        <?php endforeach; endif; ?>
                                                <?php endif; ?>
                                            </select>
                                        </td>
                                    <?php endif; ?>

                                    <?php if ($showThird): ?>
                                        <td>
                                            <select name="side_id[]" class="form-select form-select-sm border-0 bg-light variation-select" style="font-size: 0.8rem;">
                                                <option value="">-- <?= ucwords(strtolower($labelThird)) ?> --</option>
                                                <?php foreach ($schemaThirdOptions as $val => $label): ?>
                                                    <option value="<?= $val ?>" <?= $val == $hrg->side_id ? 'selected' : '' ?>>
                                                        <?= $label ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                    <?php endif; ?>

                                    <?php foreach ($extraDimensions as $extDim): ?>
                                        <td>
                                            <select name="dim_<?= $extDim['name'] ?>[]" class="form-select form-select-sm border-0 bg-light variation-select" style="font-size: 0.8rem;">
                                                <option value="">-- <?= ucwords(strtolower($extDim['label'])) ?> --</option>
                                                <?php 
                                                $meta = !empty($hrg->metadata) ? json_decode($hrg->metadata, true) : [];
                                                $selectedValue = $meta[$extDim['name']] ?? '';
                                                foreach ($extDim['options'] as $val => $label): ?>
                                                    <option value="<?= htmlspecialchars($val) ?>" <?= $val == $selectedValue ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($label) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                    <?php endforeach; ?>

                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="qty_min[]"
                                                class="form-control border-0 bg-light text-center" placeholder="Min"
                                                value="<?= $hrg->qty_min ?>" min="1">
                                            <span class="input-group-text border-0 bg-white opacity-50"><i
                                                    class="bi bi-arrow-right"></i></span>
                                            <input type="number" name="qty_max[]"
                                                class="form-control border-0 bg-light text-center" placeholder="Max"
                                                value="<?= $hrg->qty_max ?>" min="1">
                                        </div>
                                    </td>

                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text border-0 bg-light opacity-50">Rp</span>
                                            <input type="text" name="hpp[]"
                                                class="form-control border-0 bg-light fw-semibold hpp-input"
                                                value="<?= number_format($hrg->hpp, 0, ',', '.') ?>">
                                        </div>
                                    </td>

                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text border-0 bg-light text-secondary">Rp</span>
                                            <input type="text" name="harga_1[]"
                                                class="form-control border-0 bg-light fw-bold text-primary-pro"
                                                value="<?= number_format($hrg->harga_1, 0, ',', '.') ?>">
                                        </div>
                                    </td>

                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text border-0 bg-light text-secondary">Rp</span>
                                            <input type="text" name="harga_minimal[]"
                                                class="form-control border-0 bg-light fw-bold text-warning"
                                                value="<?= number_format($hrg->harga_minimal ?? 0, 0, ',', '.') ?>" placeholder="Min Harga">
                                        </div>
                                    </td>

                                    <td class="text-center text-nowrap">
                                        <button type="button" class="btn btn-link text-success p-0 border-0 me-2" title="Simpan Baris"
                                            onclick="saveSinglePriceRow(this)">
                                            <i class="bi bi-floppy"></i>
                                        </button>
                                        <button type="button" class="btn btn-link text-primary p-0 border-0 me-2" title="Duplikat Row"
                                            onclick="duplicatePriceRow(this)">
                                            <i class="bi bi-copy"></i>
                                        </button>
                                        <button type="button" class="btn btn-link text-danger p-0 border-0" title="Hapus"
                                            onclick="confirmDeletePrice('<?= base_url('manufaktur/del_harga/' . $hrg->harga_id . '/' . $getProduct['product_id']) ?>')">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="bg-white p-3 border-top d-flex justify-content-center">
                <button type="submit" class="btn btn-primary rounded-pill shadow-sm px-5 py-2 fw-bold text-white">
                    <i class="bi bi-check-lg me-1"></i> Simpan Perubahan Matriks
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        if (typeof window.initRupiahFields === 'function') window.initRupiahFields();
    });

    function duplicatePriceRow(btn) {
        const $row = $(btn).closest('tr');
        const productId = '<?= $getProduct['product_id'] ?>';
        const detailProductVal = $row.find('input[name="detail_product[]"]').val();

        $.ajax({
            url: '<?= base_url('manufaktur/addHarga') ?>',
            type: 'POST',
            data: {
                product_id: productId,
                product_nama: detailProductVal || '<?= htmlspecialchars($getProduct['product_nama']) ?>',
                segmen2: '<?= $segment2 ?>'
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    const newId = response.new_id;
                    
                    const $clonedRow = $row.clone();
                    $clonedRow.find('input[name="harga_id[]"]').val(newId);
                    $clonedRow.find('.row-bom-checkbox').val(newId);
                    
                    // Copy select values manually
                    const originalSelects = $row.find('select');
                    $clonedRow.find('select').each(function(index, item) {
                        $(item).val(originalSelects.eq(index).val());
                    });

                    // Copy input values manually
                    const originalInputs = $row.find('input[type="text"], input[type="number"]');
                    $clonedRow.find('input[type="text"], input[type="number"]').each(function(index, item) {
                        $(item).val(originalInputs.eq(index).val());
                    });

                    // Auto-increment Quantity Range
                    let oldMin = parseInt($row.find('input[name="qty_min[]"]').val()) || 1;
                    let oldMax = parseInt($row.find('input[name="qty_max[]"]').val()) || 1;
                    
                    if (oldMax >= oldMin) {
                        let diff = oldMax - oldMin;
                        let newMin = oldMax + 1;
                        let newMax = newMin + diff;

                        $clonedRow.find('input[name="qty_min[]"]').val(newMin);
                        $clonedRow.find('input[name="qty_max[]"]').val(newMax);
                    }

                    // Update delete button URL
                    $clonedRow.find('.btn-danger').attr('onclick', `confirmDeletePrice('<?= base_url('manufaktur/del_harga/') ?>${newId}/${productId}')`);

                    $row.after($clonedRow);
                    if (typeof window.initRupiahFields === 'function') window.initRupiahFields();
                    
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Variasi berhasil diduplikat',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire('Error', 'Gagal menduplikat data', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan jaringan', 'error');
            }
        });
    }

    function saveSinglePriceRow(btn) {
        const $row = $(btn).closest('tr');
        const $form = $('#formSavePrices');
        const url = $form.attr('action');
        
        const data = {};
        data['product_id'] = $form.find('input[name="product_id"]').val();
        data['segmen2'] = $form.find('input[name="segmen2"]').val();

        $row.find('input, select').each(function() {
            let name = $(this).attr('name');
            if (name) {
                if (name.endsWith('[]')) {
                    name = name.substring(0, name.length - 2);
                    if (!data[name]) data[name] = [];
                    data[name].push($(this).val());
                } else {
                    data[name] = $(this).val();
                }
            }
        });

        const $icon = $(btn).find('i');
        const originalClass = $icon.attr('class');
        $icon.attr('class', 'spinner-border spinner-border-sm text-success');
        $(btn).prop('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Data baris berhasil disimpan',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire('Error', response.message || 'Gagal menyimpan data', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan jaringan saat menyimpan', 'error');
            },
            complete: function() {
                $icon.attr('class', originalClass);
                $(btn).prop('disabled', false);
            }
        });
    }

    /**
     * Terapkan HPP kalkulasi dari BOM/Routing ke semua input HPP di tabel
     */
    function applyCalculatedHPPToAll() {
        // Cek apakah variabel global dari product-detail.js tersedia
        if (typeof lastCalculatedHPP === 'undefined' || lastCalculatedHPP <= 0) {
            Swal.fire({
                icon: 'info',
                title: 'Data HPP Belum Ada',
                text: 'Silakan isi data di tab Management BOM & Alur Kerja terlebih dahulu untuk menghitung HPP.',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }

        Swal.fire({
            title: 'Terapkan HPP Otomatis?',
            text: `Apakah Anda ingin menghitung ulang dan menerapkan HPP Estimasi yang sesuai ke masing-masing baris variasi?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Terapkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Cari semua input hpp dan update nilainya sesuai harga_id
                $('.hpp-input').each(function () {
                    const row = $(this).closest('tr');
                    const hargaId = row.find('input[name="harga_id[]"]').val() || '0';
                    let hpp = 0;
                    if (typeof window.getHppForVariation === 'function') {
                        hpp = window.getHppForVariation(hargaId);
                    } else {
                        hpp = lastCalculatedHPP || 0;
                    }

                    const formattedHPP = new Intl.NumberFormat('id-ID').format(Math.round(hpp)).replace(/,/g, '.');
                    $(this).val(formattedHPP);
                    // Trigger input event if needed for custom formatting
                    $(this).trigger('input');
                });

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Diterapkan',
                    text: 'HPP telah diperbarui di tabel berdasarkan variasi masing-masing. Jangan lupa klik "Simpan Perubahan Matriks" untuk menyimpan.',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        });
    }


    function addNewPriceRow() {
        const productId = '<?= $getProduct['product_id'] ?>';
        const productNama = '<?= htmlspecialchars($getProduct['product_nama']) ?>';

        $.ajax({
            url: '<?= base_url('manufaktur/addHarga') ?>',
            type: 'POST',
            data: {
                product_id: productId,
                product_nama: productNama,
                segmen2: '<?= $segment2 ?>'
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    const newId = response.new_id;

                    // Priority 1: Use Schema Options if available
                    // Priority 2: Fallback to DB Tables

                    const sideOptions = `<?= !empty($schemaSideOptions)
                        ? addslashes(implode('', array_map(function ($label, $val) {
                        return "<option value='{$val}'>{$label}</option>";
                    }, $schemaSideOptions, array_keys($schemaSideOptions))))
                        : (count($side) > 0 ? addslashes(implode('', array_map(function ($s) {
                        return "<option value='{$s->side_id}'>{$s->side_name}</option>";
                    }, $side))) : '') ?>`;

                    const machineOptions = `<?= !empty($schemaMachineOptions)
                        ? addslashes(implode('', array_map(function ($label, $val) {
                            return "<option value='{$val}'>{$label}</option>";
                        }, $schemaMachineOptions, array_keys($schemaMachineOptions))))
                        : (isset($mesin_printing) && count($mesin_printing) > 0 ? addslashes(implode('', array_map(function ($m) {
                            return "<option value='{$m->id}'>{$m->mesin_nama}</option>";
                        }, $mesin_printing))) : '') ?>`;

                    const showSide = <?= $showSide ? 'true' : 'false' ?>;
                    const labelSide = '<?= $labelSide ?>';
                    const showMachine = <?= $showMachine ? 'true' : 'false' ?>;
                    const labelMachine = '<?= $labelMachine ?>';
                    const showThird = <?= $showThird ? 'true' : 'false' ?>;
                    const labelThird = '<?= $labelThird ?>';

                    let newRow = `
<tr class="bg-light">
    <td class="text-center align-middle">
        <input class="form-check-input shadow-sm row-bom-checkbox" type="checkbox" value="${newId}" checked>
    </td>
    <td class="text-center align-middle row-number"></td>
    <td>
        <input type="hidden" name="harga_id[]" value="${newId}">
        <input type="text" name="detail_product[]" class="form-control form-control-sm border-0 bg-light"
            placeholder="Nama variasi..." value="${productNama}" style="font-size: 0.8rem;">
    </td>`;

                    if (showSide) {
                        newRow += `
                        <td>
                        <select name="ket_1[]" class="form-select form-select-sm border-0 bg-light variation-select" style="font-size: 0.8rem;">
                            <option value="">-- ${labelSide} --</option>
                            ${sideOptions}
                        </select>
                        </td>`;
                    }

                    if (showMachine) {
                        newRow += `
                        <td>
                        <select name="jenisprint_id[]" class="form-select form-select-sm border-0 bg-light variation-select" style="font-size: 0.8rem;">
                            <option value="">-- ${labelMachine} --</option>
                            ${machineOptions}
                        </select>
                        </td>`;
                    }

                    if (showThird) {
                        const thirdOptions = `<?= !empty($schemaThirdOptions)
                            ? addslashes(implode('', array_map(function ($label, $val) {
                                return "<option value='{$val}'>{$label}</option>";
                            }, $schemaThirdOptions, array_keys($schemaThirdOptions)))) : '' ?>`;
                        newRow += `
                        <td>
                        <select name="side_id[]" class="form-select form-select-sm border-0 bg-light variation-select" style="font-size: 0.8rem;">
                            <option value="">-- ${labelThird} --</option>
                            ${thirdOptions}
                        </select>
                        </td>`;
                    }

                    <?php
                    $extraDimCells = '';
                    foreach ($extraDimensions as $extDim) {
                        $optionsHtml = "";
                        foreach ($extDim['options'] as $val => $label) {
                            $optionsHtml .= "<option value='" . htmlspecialchars($val, ENT_QUOTES) . "'>" . htmlspecialchars($label, ENT_QUOTES) . "</option>";
                        }
                        $extraDimCells .= "<td><select name='dim_" . $extDim['name'] . "[]' class='form-select form-select-sm border-0 bg-light variation-select' style='font-size: 0.8rem;'><option value=''>-- " . ucwords(strtolower($extDim['label'])) . " --</option>" . $optionsHtml . "</select></td>";
                    }
                    ?>
                    newRow += `<?= $extraDimCells ?>`;

                    newRow += `
                <td>
                    <div class="input-group input-group-sm">
                        <input type="number" name="qty_min[]" class="form-control border-0 bg-light text-center" placeholder="Min"
                            value="1" min="1">
                            <span class="input-group-text border-0 bg-white opacity-50"><i class="bi bi-arrow-right"></i></span>
                            <input type="number" name="qty_max[]" class="form-control border-0 bg-light text-center" placeholder="Max"
                                value="1" min="1">
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text border-0 bg-light opacity-50">Rp</span>
                                <input type="text" name="hpp[]" class="form-control border-0 bg-light fw-semibold hpp-input" value="0">
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text border-0 bg-light text-secondary">Rp</span>
                                <input type="text" name="harga_1[]" class="form-control border-0 bg-light fw-bold text-primary-pro"
                                    value="0">
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text border-0 bg-light text-secondary">Rp</span>
                                <input type="text" name="harga_minimal[]" class="form-control border-0 bg-light fw-bold text-warning"
                                    value="0" placeholder="Min Harga">
                            </div>
                        </td>
                        <td class="text-center text-nowrap">
                            <button type="button" class="btn btn-link text-success p-0 border-0 me-2" title="Simpan Baris"
                                onclick="saveSinglePriceRow(this)">
                                <i class="bi bi-floppy"></i>
                            </button>
                            <button type="button" class="btn btn-link text-primary p-0 border-0 me-2" title="Duplikat Row"
                                onclick="duplicatePriceRow(this)">
                                <i class="bi bi-copy"></i>
                            </button>
                            <button type="button" class="btn btn-link text-danger p-0 border-0" title="Hapus"
                                onclick="confirmDeletePrice('<?= base_url('manufaktur/del_harga/') ?>${newId}/${productId}')">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </td>
                    </tr>
`;

                    $('#priceMatrixTable tbody .empty-row').remove();
                    $('#priceMatrixTable tbody').append(newRow);
                    initRupiahFields();
                }
            }
        });
    }

    function confirmDeletePrice(url) {
        Swal.fire({
            title: 'Hapus Skala Harga?',
            text: "Data ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // If we are in the Modal (index.php context)
                if ($('#modalPricing').length > 0 && $('#modalPricing').is(':visible')) {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json',
                        success: function (res) {
                            if (res.success) {
                                // Refresh partial content
                                if (typeof openPricingModal === 'function') {
                                    openPricingModal('<?= $getProduct['product_id'] ?>', '<?= addslashes($getProduct['product_nama']) ?>');
                                } else {
                                    location.reload();
                                }
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        }
                    });
                } else {
                    // Legacy behavior for detail page
                    window.location.href = url;
                }
            }
        });
    }

    function cloneToSide2() {
        Swal.fire({
            title: 'Generate 2 Sisi?',
            text: "Aplikasi akan menduplikasi entry '1 Sisi' menjadi '2 Sisi' secara otomatis.",
            input: 'number',
            inputLabel: 'Tambahan Harga (Margin/Markup %) - Contoh: 80 untuk +80% harga 1 sisi',
            inputValue: '80',
            showCancelButton: true,
            confirmButtonText: 'Proses sekarang',
            confirmButtonColor: '#3b82f6',
            cancelButtonText: 'Batal',
            showLoaderOnConfirm: true,
            preConfirm: (markup) => {
                return $.ajax({
                    url: '<?= base_url('manufaktur/clone_side_prices') ?>',
                    type: 'POST',
                    data: {
                        product_id: '<?= $getProduct['product_id'] ?>',
                        markup: markup
                    },
                    dataType: 'json'
                }).done(function (response) {
                    return response;
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: result.value.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        if ($('#modalPricing').length > 0 && $('#modalPricing').is(':visible') && typeof openPricingModal === 'function') {
                            openPricingModal('<?= $getProduct['product_id'] ?>', '<?= addslashes($getProduct['product_nama']) ?>');
                        } else {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire('Gagal', result.value.message, 'error');
                }
            }
        });
    }

    function applyVariasiToAll() {
        const inputs = document.querySelectorAll('input[name="detail_product[]"]');
        if (inputs.length > 1) {
            const firstValue = inputs[0].value;
            for (let i = 1; i < inputs.length; i++) {
                inputs[i].value = firstValue;
            }
            Toast.fire({
                icon: 'success',
                title: 'Keterangan variasi diterapkan ke seluruh baris!'
            });
        }
    }

    function bulkDeletePrice() {
        let selectedHargaIds = [];
        $('.row-bom-checkbox:checked').each(function() {
            if ($(this).val()) selectedHargaIds.push($(this).val());
        });

        if (selectedHargaIds.length === 0) {
            Swal.fire('Pilih Variasi', 'Silakan centang minimal 1 baris variasi di tabel untuk dihapus', 'warning');
            return;
        }

        Swal.fire({
            title: 'Hapus Variasi Terpilih?',
            text: `Anda akan menghapus ${selectedHargaIds.length} variasi. Tindakan ini tidak dapat dibatalkan. Lanjutkan?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return $.ajax({
                    url: '<?= base_url('manufaktur/bulk_del_harga') ?>',
                    type: 'POST',
                    data: { harga_ids: selectedHargaIds },
                    dataType: 'json'
                }).done(function (response) {
                    return response;
                }).fail(function() {
                    Swal.showValidationMessage('Terjadi kesalahan jaringan');
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Dihapus!',
                        text: result.value.message,
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Info', result.value.message, 'info');
                }
            }
        });
    }

    function detectDuplicates() {
        let seen = {};
        let duplicates = 0;
        
        // Reset borders
        $('#priceMatrixTable tbody tr').removeClass('duplicate-row').css('border', '');
        $('.row-bom-checkbox').prop('checked', false);

        $('#priceMatrixTable tbody tr:not(.empty-row)').each(function() {
            const row = $(this);
            let signature = '';
            
            // build signature
            signature += row.find('input[name="detail_product[]"]').val() + '|';
            row.find('select.variation-select').each(function() {
                signature += $(this).val() + '|';
            });
            signature += row.find('input[name="qty_min[]"]').val() + '|';
            signature += row.find('input[name="qty_max[]"]').val() + '|';
            
            if (seen[signature]) {
                duplicates++;
                row.addClass('duplicate-row').css('border', '2px solid #ef4444');
                row.find('.row-bom-checkbox').prop('checked', true);
            } else {
                seen[signature] = true;
            }
        });

        if (duplicates > 0) {
            Swal.fire({
                icon: 'info',
                title: 'Ditemukan ' + duplicates + ' Duplikat',
                text: 'Baris yang sama persis (duplikat) telah ditandai dengan garis merah dan dicentang otomatis. Anda bisa klik "Hapus Terpilih" untuk menghapus semuanya secara massal.'
            });
        } else {
            Swal.fire('Aman!', 'Tidak ditemukan baris variasi yang duplikat.', 'success');
        }
    }

    function autoGenerateBOM() {
        const bahan_id = $('#autoBOMMaterialSelect').val() || '';

        let selectedHargaIds = [];
        $('.row-bom-checkbox:checked').each(function() {
            if ($(this).val()) selectedHargaIds.push($(this).val());
        });

        if (selectedHargaIds.length === 0) {
            Swal.fire('Pilih Variasi', 'Silakan centang minimal 1 baris variasi di tabel untuk di-generate BOM-nya', 'warning');
            return;
        }

        Swal.fire({
            title: 'Auto-Generate BOM?',
            text: `Sistem akan membuatkan masing-masing 1 Resep BOM untuk ${selectedHargaIds.length} variasi yang dicentang menggunakan material yang Anda pilih. Lanjutkan?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Generate BOM!',
            cancelButtonText: 'Batal',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return $.ajax({
                    url: '<?= base_url('manufaktur/auto_populate_bom') ?>',
                    type: 'POST',
                    data: {
                        product_id: '<?= $getProduct['product_id'] ?>',
                        bahan_id: bahan_id,
                        harga_ids: selectedHargaIds
                    },
                    dataType: 'json'
                }).done(function (response) {
                    return response;
                }).fail(function() {
                    Swal.showValidationMessage('Terjadi kesalahan jaringan');
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: result.value.message,
                    }).then(() => {
                        // Switch to BOM tab if on detail page
                        if ($('#bom-tab').length > 0) {
                            $('#bom-tab').tab('show');
                        } else if ($('.nav-pills button[data-bs-target="#sop-bom"]').length > 0) {
                            // Focus on the right spot if inside modal
                            $('.nav-pills button[data-bs-target="#sop-bom"]').tab('show');
                        }
                    });
                } else {
                    Swal.fire('Info', result.value.message, 'info');
                }
            }
        });
    }

    $(document).ready(function() {
        if ($('#autoBOMMaterialSelect').length) {
            $('#autoBOMMaterialSelect').select2({
                placeholder: "Cari material...",
                allowClear: true,
                ajax: {
                    url: '<?= base_url('manufaktur/get_bahan_options') ?>',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { term: params.term };
                    },
                    processResults: function (data) {
                        return { results: data.results };
                    },
                    cache: true
                },
                minimumInputLength: 2
            });
        }
    });
</script>