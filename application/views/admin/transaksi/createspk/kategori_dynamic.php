<?php
// 1. DATA PREPARATION & CLEANING
$product = $getproduk ?? [];
$raw_schema = $form_schema ?? '';
$kategori_id = $product['kategori_id'] ?? 0;
$subkategori_id = $product['subkategori_id'] ?? 0;
$product_nama = strtolower($product['product_nama'] ?? '');
$product_id = $product['product_id'] ?? 0;

// Determine Source & ID for Debugging
$source = 'Kategori';
$source_id = $kategori_id;
if (!empty($product['product_form_schema'])) {
    $source = 'Produk';
    $source_id = $product_id;
} elseif (!empty($product['subcategory_form_schema'])) {
    $source = 'Sub-Kategori';
    $source_id = $subkategori_id;
}

if (!empty($raw_schema) && is_string($raw_schema)) {
    $raw_schema = trim($raw_schema, '"');
    $raw_schema = stripslashes($raw_schema);
    $raw_schema = str_replace(['\n', '\r', "\n", "\r"], '', $raw_schema);
    $raw_schema = trim($raw_schema);
}
$schema = json_decode($raw_schema, true);
$json_error = ($raw_schema && (!is_array($schema) || empty($schema))) ? json_last_error_msg() : null;
?>
<style>
    .schema-source-badge {
        position: absolute;
        top: -25px;
        right: 0;
        font-size: 9px;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.6;
    }

    .badge-produk {
        background: #dcfce7;
        color: #166534;
    }

    .badge-sub {
        background: #fef9c3;
        color: #854d0e;
    }

    .badge-kat {
        background: #e0f2fe;
        color: #075985;
    }

    .badge-error {
        background: #fee2e2;
        color: #991b1b;
        opacity: 1 !important;
    }

    /* Premium Segmented Styling */
    .premium-segment-container {
        display: flex;
        flex-direction: row;
        background: #f8fafc;
        padding: 5px;
        border-radius: 12px;
        gap: 0;
        border: 1px solid #f1f5f9;
        overflow: hidden;
        width: 100%;
    }

    .premium-segment-item {
        flex: 1;
    }

    .premium-segment-item input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .premium-segment-item label {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px 5px;
        cursor: pointer;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 700;
        color: #64748b;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        margin-bottom: 0;
        text-transform: uppercase;
        height: 100%;
    }

    .premium-segment-item input[type="radio"]:checked+label {
        background: #fff;
        color: #2563eb;
        box-shadow: 0 0 0 2px #3b82f6 inset, 0 4px 12px rgba(37, 99, 235, 0.15);
    }

    /* Left border accent */
    .field-label-wrapper {
        border-left: 3px solid #3b82f6;
        padding-left: 12px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }

    .field-label-wrapper:focus-within {
        border-left-color: #2563eb;
        background: rgba(59, 130, 246, 0.02);
    }

    .nav-instruction {
        font-size: 0.7rem;
        color: #3b82f6;
        font-weight: 500;
        margin-left: 8px;
    }

    /* Blueprint Dimension Group */
    .blueprint-group {
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        padding: 10px;
        background: #fff;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .blueprint-input-box {
        flex: 1;
        position: relative;
    }

    .blueprint-input-box span {
        position: absolute;
        top: -8px;
        left: 10px;
        background: white;
        padding: 0 5px;
        font-size: 9px;
        font-weight: 700;
        color: #94a3b8;
    }

    .blueprint-input {
        border: 1px solid #e2e8f0 !important;
        text-align: center;
        font-weight: 800 !important;
        font-family: 'JetBrains Mono', monospace;
        font-size: 1rem;
        border-radius: 8px !important;
    }
</style>

<div class="premium-dynamic-form px-1 position-relative" id="form-container-<?= $product_id ?>">
    <!-- 
        DEVELOPER TRACE INFO:
        ===================================
        Product ID    : <?= $product_id ?> 
        Product Name  : <?= $product['product_nama'] ?> 
        Source Table  : <?= $source ?> 
        Raw JSON      : <?= $raw_schema ?: 'EMPTY' ?> 
        JSON Status   : <?= $json_error ?: 'VALID/EMPTY' ?> 
        ===================================
    -->

    <!-- Schema Inspector Badge -->
    <?php if ($json_error): ?>
        <span class="schema-source-badge badge-error" title="<?= htmlspecialchars($json_error) ?>">
            <i class="fas fa-exclamation-triangle me-1"></i> JSON ERROR
        </span>
    <?php elseif ($schema): ?>
        <span
            class="schema-source-badge badge-<?= ($source == 'Produk' ? 'produk' : ($source == 'Sub-Kategori' ? 'sub' : 'kat')) ?>"
            onclick="$('#debug-table-<?= $product_id ?>').toggle()" style="cursor:help" title="Klik untuk rincian data">
            <i class="fas fa-database me-1"></i> <?= $source ?> #<?= $source_id ?>
        </span>

        <!-- Hidden Debug Table (Tampil di UI jika badge diklik) -->
        <div id="debug-table-<?= $product_id ?>"
            style="display:none; font-size:10px; background:#fff; border:1px solid #e2e8f0; margin-bottom:15px; border-radius:8px; overflow:hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <table class="table table-sm table-bordered mb-0">
                <tr class="bg-light">
                    <th class="py-1">TEKNIS</th>
                    <th class="py-1">DATA</th>
                </tr>
                <tr>
                    <td class="py-1">Table Source</td>
                    <td class="py-1 fw-bold"><?= $source ?></td>
                </tr>
                <tr>
                    <td class="py-1">JSON Hash</td>
                    <td class="py-1 text-muted"><?= md5($raw_schema) ?></td>
                </tr>
                <tr>
                    <td class="py-1">Field Count</td>
                    <td class="py-1 fw-bold text-primary"><?= count($schema) ?> Fields</td>
                </tr>
            </table>
        </div>
    <?php endif; ?>

    <?php
    if (!empty($schema) && is_array($schema)):
        $is_skip = false;
        foreach ($schema as $index => $field):
            if ($is_skip) {
                $is_skip = false;
                continue;
            }

            $type = $field['type'] ?? 'text';
            $label = $field['label'] ?? 'Field';
            $name = $field['name'] ?? 'custom_field';
            $options = $field['options'] ?? [];
            $defaultValue = $field['default'] ?? ($field['value'] ?? ($product[$name] ?? ''));

            // Check for next field for potential grouping (Panjang x Lebar)
            $next_field = $schema[$index + 1] ?? null;
            $is_dimension_pair = false;

            // LOGIC: Only group if BOTH are visible text/number inputs
            if (
                $next_field &&
                (strpos(strtolower($name), 'panjang') !== false || strpos(strtolower($name), 'p') === 0) &&
                (strpos(strtolower($next_field['name']), 'lebar') !== false || strpos(strtolower($next_field['name']), 'l') === 0) &&
                $type !== 'hidden' && ($next_field['type'] ?? '') !== 'hidden'
            ) {
                $is_dimension_pair = true;
            }

            if ($is_dimension_pair):
                $is_skip = true; // Skip next field in main loop
                $label_combined = "UKURAN (PxL)";
                ?>
                <div class="field-label-wrapper">
                    <label class="form-label small fw-bold text-muted text-uppercase d-block mb-2"><?= $label_combined ?></label>
                    <div class="blueprint-group">
                        <div class="blueprint-input-box">
                            <span>P</span>
                            <input type="number" class="form-control blueprint-input" name="<?= $name ?>" placeholder="P"
                                value="<?= $defaultValue ?>" step="0.01">
                        </div>
                        <span class="fw-bold fs-4 text-muted">×</span>
                        <div class="blueprint-input-box">
                            <span>L</span>
                            <input type="number" class="form-control blueprint-input" name="<?= $next_field['name'] ?>"
                                placeholder="L" value="<?= $product[$next_field['name']] ?? ($next_field['default'] ?? '') ?>"
                                step="0.01">
                        </div>
                        <span class="fw-bold text-muted small">MTR</span>
                    </div>
                </div>
            <?php else: ?>
                <div class="field-label-wrapper">
                    <label class="form-label small fw-bold text-muted text-uppercase d-flex align-items-center mb-2">
                        <?= $label ?>
                        <?php if ($type == 'segmented' || $type == 'select' || $type == 'radio'): ?>
                            <span class="nav-instruction fw-bold"><i class="fas fa-keyboard me-1"></i> Gunakan <i
                                    class="fas fa-arrow-left"></i> <i class="fas fa-arrow-right"></i> untuk memilih</span>
                        <?php endif; ?>
                    </label>

                    <?php if ($type == 'segmented' || $type == 'radio' || $type == 'select'):
                        $normalizedOptions = [];
                        if (is_array($options)) {
                            foreach ($options as $key => $val) {
                                if (is_array($val))
                                    $normalizedOptions[] = ['label' => $val['label'] ?? '', 'value' => $val['value'] ?? ''];
                                else
                                    $normalizedOptions[] = ['label' => $val, 'value' => $key];
                            }
                        }
                        ?>
                        <div class="premium-segment-container" role="radiogroup">
                            <?php foreach ($normalizedOptions as $opt): ?>
                                <div class="premium-segment-item">
                                    <input type="radio" name="<?= $name ?>" id="<?= $name ?>_<?= $opt['value'] ?>"
                                        value="<?= $opt['value'] ?>" <?= ($defaultValue == $opt['value'] || (empty($defaultValue) && $opt['value'] == '0')) ? 'checked' : '' ?> tabindex="0">
                                    <label for="<?= $name ?>_<?= $opt['value'] ?>"><?= strtoupper($opt['label']) ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    <?php elseif ($type == 'dimension' || $type == 'dimensions'): ?>
                        <div class="blueprint-group">
                            <div class="blueprint-input-box"><span>P</span><input type="number" class="form-control blueprint-input"
                                    name="panjang" placeholder="P" value="<?= $product['panjang'] ?? '' ?>" step="0.01"></div>
                            <span class="fw-bold fs-4 text-muted">×</span>
                            <div class="blueprint-input-box"><span>L</span><input type="number" class="form-control blueprint-input"
                                    name="lebar" placeholder="L" value="<?= $product['lebar'] ?? '' ?>" step="0.01"></div>
                        </div>

                    <?php elseif ($type == 'textarea'): ?>
                        <textarea name="<?= $name ?>" class="form-control form-control-sm rounded-3" rows="<?= $field['rows'] ?? 2 ?>"
                            placeholder="<?= $placeholder ?>"><?= $defaultValue ?></textarea>

                    <?php elseif ($type == 'hidden'): ?>
                        <input type="hidden" name="<?= $name ?>" value="<?= $defaultValue ?>">

                    <?php else: ?>
                        <input type="<?= ($type == 'meter_lari' || $type == 'number') ? 'number' : 'text' ?>" name="<?= $name ?>"
                            class="form-control form-control-sm fw-bold rounded-3" placeholder="<?= $placeholder ?>"
                            value="<?= $defaultValue ?>" step="any">
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

    <?php else: // NO SCHEMA: enforce form_schema usage (no hardcoded fallback) ?>

        <div class="field-label-wrapper">
            <div class="alert alert-warning mb-0 p-3" style="border-radius:8px;">
                <strong>Perhatian:</strong> Tampilan spesifikasi harus dikonfigurasi melalui <code>form_schema</code>.
                Tidak ada fallback hardcoded — silakan siapkan schema untuk produk / sub-kategori / kategori.
                <div style="margin-top:8px; font-size:0.9rem;">
                    <?php if (!empty($product_id)): ?>
                        <a href="<?= base_url('pengaturan/product_form_builder/' . $product_id) ?>" class="btn btn-sm btn-outline-primary">Buka Product Form Builder</a>
                    <?php endif; ?>
                    <a href="<?= base_url('pengaturan/product_form_builder') ?>" class="btn btn-sm btn-outline-secondary">Form Builder (Umum)</a>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>

<script>
    $(document).ready(function () {
        setTimeout(() => {
            let container = $('#form-container-<?= $product_id ?>');
            let firstInput = container.find('input[type="radio"]:checked, input:not([type="hidden"]):visible').first();
            if (firstInput.length) { firstInput.focus(); if (firstInput.is('input[type="number"], input[type="text"]')) firstInput.select(); }
        }, 300);

        $(document).off('keydown.dynamicForm').on('keydown.dynamicForm', function (e) {
            const target = $(e.target);
            if (target.closest('.premium-dynamic-form').length === 0) return;

            if (e.key === 'Enter') {
                e.preventDefault();
                let allInputs = $('.premium-dynamic-form, #qty').find('input:visible, select:visible, textarea:visible');
                let next = allInputs.eq(allInputs.index(target) + 1);
                if (next.length) next.focus().select(); else $('#qty').focus().select();
            } else if (['ArrowLeft', 'ArrowRight'].includes(e.key) && target.is('input[type="radio"]')) {
                setTimeout(() => {
                    let checked = $('input[name="' + target.attr('name') + '"]:checked');
                    checked.next('label').addClass('animate__animated animate__pulse');
                    setTimeout(() => checked.next('label').removeClass('animate__animated animate__pulse'), 400);
                }, 50);
            }
        });
    });
</script>