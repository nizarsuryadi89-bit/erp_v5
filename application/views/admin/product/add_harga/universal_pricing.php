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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold m-0 text-slate-900">
            <i class="bi bi-currency-dollar me-2 text-primary-pro"></i>Matriks Skala Harga Jual
        </h5>
        <div class="d-flex gap-2">
    <?php else: ?>
    <div class="d-flex justify-content-end align-items-center mb-4">
        <div class="d-flex gap-2">
    <?php endif; ?>
            <?php if ($showSide): ?>
                <button type="button" class="btn-pro btn-pro-outline btn-sm" onclick="cloneToSide2()">
                    <i class="bi bi-layers me-1 text-info"></i> Auto-Generate 2 Sisi
                </button>
            <?php endif; ?>
            <button type="button" class="btn-pro btn-pro-primary btn-sm" onclick="addNewPriceRow()">
                <i class="bi bi-plus-lg me-1"></i> Tambah Range
            </button>
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
            } elseif ($name === 'side_id' || strpos($labelLower, 'sisi') !== false) {
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
                <table class="table table-pro mb-0" id="priceMatrixTable">
                    <thead>
                        <tr>
                            <th width="25%">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span>KETERANGAN VARIASI</span>
                                    <button type="button" class="btn btn-xs btn-outline-primary py-0 px-1"
                                        onclick="applyVariasiToAll()" title="Terapkan teks baris pertama ke semua baris">
                                        <i class="bi bi-magic"></i>
                                    </button>
                                </div>
                            </th>
                            <?php if ($showSide): ?>
                                <th width="12%"><?= $labelSide ?></th>
                            <?php endif; ?>
                            <?php if ($showMachine): ?>
                                <th width="15%"><?= $labelMachine ?></th>
                            <?php endif; ?>
                            <?php if ($showThird): ?>
                                <th width="12%"><?= $labelThird ?></th>
                            <?php endif; ?>
                            <?php foreach ($extraDimensions as $extDim): ?>
                                <th width="12%"><?= $extDim['label'] ?></th>
                            <?php endforeach; ?>
                            <th width="20%" class="text-center">RANGE QUANTITY</th>
                            <th width="15%">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span>HPP (EST)</span>
                                    <button type="button" class="btn btn-xs btn-outline-primary py-0 px-1"
                                        onclick="applyCalculatedHPPToAll()" title="Terapkan HPP Kalkulasi">
                                        <i class="bi bi-magic"></i>
                                    </button>
                                </div>
                            </th>
                            <th width="15%">HARGA JUAL</th>
                            <th width="15%">MIN CHARGE</th>
                            <th width="5%" class="text-center"></th>
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
                            <?php foreach ($getHarga as $hrg): ?>
                                <tr>
                                    <td>
                                        <input type="hidden" name="harga_id[]" value="<?= $hrg->harga_id ?>">
                                        <input type="text" name="detail_product[]" class="form-control border-0 bg-light small"
                                            placeholder="Nama variasi..." value="<?= htmlspecialchars($hrg->detail_product) ?>">
                                    </td>

                                    <?php if ($showSide): ?>
                                        <td>
                                            <select name="ket_1[]" class="form-select border-0 bg-light small">
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
                                            <select name="jenisprint_id[]" class="form-select border-0 bg-light small">
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
                                            <select name="side_id[]" class="form-select border-0 bg-light small">
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
                                            <select name="dim_<?= $extDim['name'] ?>[]" class="form-select border-0 bg-light small">
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
                                            <span class="input-group-text border-0 bg-slate-800 text-white">Rp</span>
                                            <input type="text" name="harga_1[]"
                                                class="form-control border-0 bg-slate-100 fw-bold text-primary-pro"
                                                value="<?= number_format($hrg->harga_1, 0, ',', '.') ?>">
                                        </div>
                                    </td>

                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text border-0 bg-slate-800 text-white">Rp</span>
                                            <input type="text" name="harga_minimal[]"
                                                class="form-control border-0 bg-slate-100 fw-bold text-warning"
                                                value="<?= number_format($hrg->harga_minimal ?? 0, 0, ',', '.') ?>" placeholder="Min Harga">
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <button type="button" class="btn btn-link text-danger p-0 border-0"
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

            <div class="bg-white p-3 border-top d-flex justify-content-end">
                <button type="submit" class="btn-pro btn-pro-primary">
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
            text: `Apakah Anda ingin menerapkan HPP Rp ${new Intl.NumberFormat('id-ID').format(Math.round(lastCalculatedHPP))} ke semua baris skala harga?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Terapkan Secara Massal',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Format nilai ke format ribuan rupiah (titik)
                const formattedHPP = new Intl.NumberFormat('id-ID').format(Math.round(lastCalculatedHPP)).replace(/,/g, '.');

                // Cari semua input hpp dan update nilainya
                $('.hpp-input').each(function () {
                    $(this).val(formattedHPP);
                    // Trigger input event if needed for custom formatting
                    $(this).trigger('input');
                });

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Diterapkan',
                    text: 'HPP telah diperbarui di tabel. Jangan lupa klik "Simpan Perubahan Matriks" untuk menyimpan secara permanen.',
                    timer: 2500,
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
<tr>
    <td>
        <input type="hidden" name="harga_id[]" value="${newId}">
        <input type="text" name="detail_product[]" class="form-control border-0 bg-light small"
            placeholder="Nama variasi..." value="${productNama}">
    </td>`;

                    if (showSide) {
                        newRow += `
                        <td>
                        <select name="ket_1[]" class="form-select border-0 bg-light small">
                            <option value="">-- ${labelSide} --</option>
                            ${sideOptions}
                        </select>
                        </td>`;
                    }

                    if (showMachine) {
                        newRow += `
                        <td>
                        <select name="jenisprint_id[]" class="form-select border-0 bg-light small">
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
                        <select name="side_id[]" class="form-select border-0 bg-light small">
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
                        $extraDimCells .= "<td><select name='dim_" . $extDim['name'] . "[]' class='form-select border-0 bg-light small'><option value=''>-- " . ucwords(strtolower($extDim['label'])) . " --</option>" . $optionsHtml . "</select></td>";
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
                                <span class="input-group-text border-0 bg-slate-800 text-white">Rp</span>
                                <input type="text" name="harga_1[]" class="form-control border-0 bg-slate-100 fw-bold text-primary-pro"
                                    value="0">
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text border-0 bg-slate-800 text-white">Rp</span>
                                <input type="text" name="harga_minimal[]" class="form-control border-0 bg-slate-100 fw-bold text-warning"
                                    value="0" placeholder="Min Harga">
                            </div>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-link text-danger p-0 border-0"
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
</script>