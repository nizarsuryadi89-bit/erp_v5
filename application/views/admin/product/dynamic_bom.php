<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-info-circle me-2"></i> <strong>Mapping BOM Dinamis</strong> memungkinkan Anda menautkan pilihan variasi (misal: Bahan Kertas) dengan stok barang riil di gudang. Sistem otomatis memotong stok berdasarkan rumus saat barang diproduksi.
            </div>
            <button class="btn btn-sm btn-warning fw-bold text-dark" onclick="openImportBomModal()">
                <i class="fas fa-download me-1"></i> Import dari BOM Lama
            </button>
        </div>
        
        <div class="table-responsive mt-3">
            <table class="table table-bordered table-hover align-middle" id="table-dynamic-bom" style="font-size: 0.85rem;">
                <thead class="table-light text-slate-700 text-center align-middle" style="letter-spacing: 0.5px;">
                    <tr>
                        <th width="15%" class="text-start ps-3">Atribut (Form)</th>
                        <th width="15%" class="text-start ps-3">Pilihan (Value)</th>
                        <th width="24%">Bahan Baku (Gudang)</th>
                        <th width="12%">Tipe Rumus</th>
                        <th width="12%">Pengali (Ops)</th>
                        <th width="8%">Pembagi</th>
                        <th width="6%">Waste %</th>
                        <th width="8%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Populated by JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var productId = '<?= $getProduct["product_id"] ?>';
    
    // Parse Schema to get all select/radio options and number inputs
    var rawSchema = <?= !empty($getProduct['product_form_schema']) ? json_encode($getProduct['product_form_schema']) : '"{}"' ?>;
    var schemaOptions = [];
    var numericInputs = [];
    
    try {
        if (typeof rawSchema === 'string' && rawSchema.trim() !== '') {
            rawSchema = JSON.parse(rawSchema);
        }
        if (Array.isArray(rawSchema)) {
            rawSchema.forEach(function(field) {
                if (field.type === 'number' || field.type === 'text') {
                    numericInputs.push({key: field.name, label: field.label});
                }
                if (field.type === 'select' || field.type === 'radio' || field.type === 'dropdown') {
                    if (Array.isArray(field.options)) {
                        field.options.forEach(function(opt) {
                            schemaOptions.push({
                                attr_key: field.name,
                                attr_label: field.label,
                                val_label: opt
                            });
                        });
                    } else if (typeof field.options === 'object' && field.options !== null) {
                        // Handle object format {"1": "Option A", "2": "Option B"}
                        Object.keys(field.options).forEach(function(key) {
                            schemaOptions.push({
                                attr_key: field.name,
                                attr_label: field.label,
                                val_label: field.options[key]
                            });
                        });
                    }
                }
            });
        }
    } catch (e) {
        console.error("Error parsing schema for dynamic BOM", e);
    }
    
    var bahanOptions = '<option value="">-- Pilih Bahan --</option>';
    <?php foreach ($bahan as $b): ?>
        bahanOptions += '<option value="<?= $b->bahan_id ?>"><?= addslashes($b->bahan_nama) ?></option>';
    <?php endforeach; ?>
    
    var calcTypeOptions = `
        <option value="fixed">Pasti / Fixed</option>
        <option value="area">Area (P x L)</option>
        <option value="sheets_plano">Lembaran (Plano/A3)</option>
        <option value="linear_meter">Meter Lari</option>
    `;
    
    var multiplierOptions = '<option value="">-- Tidak Ada --</option>';
    numericInputs.forEach(function(ni) {
        multiplierOptions += '<option value="'+ni.key+'">'+ni.label+' ('+ni.key+')</option>';
    });

    function loadDynamicBOM() {
        $.ajax({
            url: '<?= base_url("manufaktur/get_dynamic_bom") ?>',
            type: 'GET',
            data: { product_id: productId },
            dataType: 'json',
            success: function(res) {
                var tbody = $('#table-dynamic-bom tbody');
                tbody.empty();
                
                if (schemaOptions.length === 0) {
                    tbody.append('<tr><td colspan="8" class="text-center text-muted py-5">Belum ada form variasi yang menggunakan Dropdown/Radio di Form Builder.</td></tr>');
                    return;
                }
                
                var savedData = res.data || [];
                var savedMap = {};
                savedData.forEach(function(sd) {
                    var mapKey = sd.attribute_key + '|' + sd.attribute_value;
                    if(!savedMap[mapKey]) savedMap[mapKey] = [];
                    savedMap[mapKey].push(sd);
                });
                
                var table = $('#table-dynamic-bom');
                table.find('tbody').remove();
                
                if (schemaOptions.length === 0) {
                    table.append('<tbody><tr><td colspan="8" class="text-center text-muted py-5">Belum ada form variasi yang menggunakan Dropdown/Radio di Form Builder.</td></tr></tbody>');
                    return;
                }
                
                try {
                    schemaOptions.forEach(function(opt) {
                        var mapKey = opt.attr_key + '|' + opt.val_label;
                        var sds = savedMap[mapKey] || [];
                        
                        if (sds.length === 0) {
                            sds = [null]; // Start with one empty row
                        }
                        
                        var optionGroupTbody = $('<tbody class="border-bottom border-2 border-slate-200"></tbody>');

                        sds.forEach(function(sd, index) {
                            var tr = $('<tr></tr>');
                            
                            if (index === 0) {
                                tr.append('<td class="fw-bold bg-slate-50 align-middle text-start ps-3 border-end" rowspan="'+(sds.length+1)+'"><div class="text-slate-800">'+opt.attr_label+'</div><small class="text-muted fw-normal">['+opt.attr_key+']</small></td>');
                                tr.append('<td class="fw-bold align-middle bg-slate-50 text-start ps-3 border-end" rowspan="'+(sds.length+1)+'">'+opt.val_label+'</td>');
                            }

                            // Bahan Dropdown
                            var selBahan = $('<select class="form-select form-select-sm sel-bahan" style="font-size: 0.8rem;">'+bahanOptions+'</select>');
                            if (sd && sd.bahan_id) selBahan.val(sd.bahan_id);
                            var tdBahan = $('<td class="align-middle"></td>').append(selBahan);
                            if (sd && sd.unit_cost !== undefined) {
                                var formattedCost = new Intl.NumberFormat('id-ID').format(sd.unit_cost);
                                var uName = (sd.unit_name) ? sd.unit_name.toLowerCase() : 'satuan';
                                var isManual = sd.cost_is_manual === true;
                                var manualBadge = isManual ? '<span class="badge bg-warning text-dark ms-1" style="font-size:0.6rem;">MANUAL</span>' : '';
                                var editIcon = (sd.id) ? '<button class="btn btn-link btn-sm p-0 ms-1 text-muted btn-edit-cost" title="Edit HPP Manual" style="font-size:0.7rem; line-height:1; vertical-align:middle;"><i class="fas fa-pencil-alt"></i></button>' : '';
                                var resetLink = isManual ? '<a href="#" class="ms-1 text-danger btn-reset-cost" style="font-size:0.65rem;" title="Reset ke harga otomatis"><i class="fas fa-undo"></i></a>' : '';
                                var priceDiv = $('<div class="small text-success mt-1 ms-1 fw-semibold d-flex align-items-center price-display"></div>');
                                priceDiv.html('<i class="fas fa-coins me-1"></i>Rp ' + formattedCost + ' / ' + uName + manualBadge + editIcon + resetLink);
                                
                                // Inline edit form (hidden by default)
                                var editForm = $('<div class="d-none mt-1 ms-1 edit-cost-form"></div>');
                                editForm.html(
                                    '<div class="input-group input-group-sm" style="max-width:200px;">' +
                                    '<span class="input-group-text py-0" style="font-size:0.7rem;">Rp</span>' +
                                    '<input type="number" class="form-control py-0 inp-manual-cost" style="font-size:0.75rem;" placeholder="Harga / ' + uName + '" value="' + (isManual ? sd.manual_cost : '') + '" min="0" step="any">' +
                                    '<button class="btn btn-success btn-xs btn-save-cost py-0 px-2" style="font-size:0.7rem;"><i class="fas fa-check"></i></button>' +
                                    '<button class="btn btn-secondary btn-xs btn-cancel-cost py-0 px-2" style="font-size:0.7rem;"><i class="fas fa-times"></i></button>' +
                                    '</div>' +
                                    '<div class="text-muted" style="font-size:0.65rem;"><i class="fas fa-info-circle"></i> Kosongkan untuk kembali ke harga otomatis</div>'
                                );
                                
                                // Toggle edit form
                                priceDiv.on('click', '.btn-edit-cost', function(e) {
                                    e.preventDefault();
                                    priceDiv.addClass('d-none');
                                    editForm.removeClass('d-none').find('.inp-manual-cost').focus();
                                });
                                
                                // Cancel edit
                                editForm.on('click', '.btn-cancel-cost', function(e) {
                                    e.preventDefault();
                                    editForm.addClass('d-none');
                                    priceDiv.removeClass('d-none');
                                });
                                
                                // Save manual cost
                                editForm.on('click', '.btn-save-cost', function(e) {
                                    e.preventDefault();
                                    var btn = $(this);
                                    btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
                                    var newCost = editForm.find('.inp-manual-cost').val();
                                    $.post('<?= base_url("manufaktur/save_dynamic_manual_cost") ?>', {
                                        id: sd.id,
                                        manual_cost: newCost
                                    }, function(r) {
                                        loadDynamicBOM();
                                    });
                                });
                                
                                // Reset to auto
                                priceDiv.on('click', '.btn-reset-cost', function(e) {
                                    e.preventDefault();
                                    $.post('<?= base_url("manufaktur/save_dynamic_manual_cost") ?>', {
                                        id: sd.id,
                                        manual_cost: ''
                                    }, function(r) {
                                        loadDynamicBOM();
                                    });
                                });
                                
                                tdBahan.append(priceDiv).append(editForm);
                            }
                            tr.append(tdBahan);
                            
                            // Calc Type
                            var selCalc = $('<select class="form-select form-select-sm border-0 bg-light shadow-none" style="font-size: 0.8rem;">'+calcTypeOptions+'</select>');
                            if (sd && sd.calc_type) selCalc.val(sd.calc_type);
                            tr.append($('<td></td>').append(selCalc));
                            
                            // Multiplier
                            var selMultId = 'multList_' + Math.random().toString(36).substr(2, 9);
                            var multWrapper = $('<div></div>');
                            var selMult = $('<input type="text" list="'+selMultId+'" class="form-control form-control-sm border-0 bg-light shadow-none sel-mult" style="font-size: 0.8rem;" placeholder="Pilih/Ketik...">');
                            var dataList = $('<datalist id="'+selMultId+'">'+multiplierOptions+'</datalist>');
                            multWrapper.append(selMult).append(dataList);
                            if (sd && sd.multiplier_key) selMult.val(sd.multiplier_key);
                            tr.append($('<td></td>').append(multWrapper));
                            
                            // Divider
                            var inpDiv = $('<input type="number" class="form-control form-control-sm text-center border-0 bg-light shadow-none" min="1" step="any" value="'+(sd ? sd.divider_factor : '1')+'" style="font-size: 0.8rem; padding: 0.25rem;">');
                            tr.append($('<td></td>').append(inpDiv));
                            
                            // Scrap
                            var inpScrap = $('<input type="number" class="form-control form-control-sm text-center border-0 bg-light shadow-none" min="0" step="any" value="'+(sd ? sd.scrap_factor : '0')+'" style="font-size: 0.8rem; padding: 0.25rem;">');
                            tr.append($('<td></td>').append(inpScrap));
                            
                            // Button Action
                            var tdAction = $('<td class="text-center align-middle"></td>');
                            var btnGroup = $('<div class="d-flex justify-content-center gap-1"></div>');
                            
                            var btnSave = $('<button class="btn btn-sm btn-light border text-primary" title="Simpan"><i class="fas fa-save"></i></button>');
                            btnSave.click(function() {
                                var dataPost = {
                                    product_id: productId,
                                    attribute_key: opt.attr_key,
                                    attribute_value: opt.val_label,
                                    bahan_id: selBahan.val(),
                                    calc_type: selCalc.val(),
                                    multiplier_key: selMult.val(),
                                    divider_factor: inpDiv.val(),
                                    scrap_factor: inpScrap.val()
                                };
                                if (sd && sd.id) dataPost.id = sd.id;
                                
                                if (!dataPost.bahan_id) {
                                    Swal.fire('Warning', 'Pilih bahan baku terlebih dahulu', 'warning');
                                    return;
                                }
                                
                                var btn = $(this);
                                btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
                                
                                $.post('<?= base_url("manufaktur/save_dynamic_bom") ?>', dataPost, function(r) {
                                    loadDynamicBOM(); 
                                    if (typeof refreshAllData === 'function') refreshAllData();
                                });
                            });
                            btnGroup.append(btnSave);
                            
                            if (sd && sd.id) {
                                var btnDel = $('<button class="btn btn-sm btn-light border text-danger" title="Hapus"><i class="fas fa-trash"></i></button>');
                                btnDel.click(function() {
                                    Swal.fire({
                                        title: 'Hapus Mapping?',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonText: 'Ya, Hapus'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $.post('<?= base_url("manufaktur/delete_dynamic_bom") ?>', {id: sd.id}, function(r) {
                                                loadDynamicBOM();
                                                if (typeof refreshAllData === 'function') refreshAllData();
                                            });
                                        }
                                    });
                                });
                                btnGroup.append(btnDel);
                            } else if (index > 0) {
                                var btnRemoveUI = $('<button class="btn btn-sm btn-light border text-secondary" title="Batal"><i class="fas fa-times"></i></button>');
                                btnRemoveUI.click(function() {
                                    loadDynamicBOM();
                                });
                                btnGroup.append(btnRemoveUI);
                            }
                            
                            tdAction.append(btnGroup);
                            tr.append(tdAction);
                            
                            optionGroupTbody.append(tr);
                        });
                        
                        var trAdd = $('<tr><td colspan="6" class="text-start bg-transparent p-2 border-0"><button class="btn btn-sm text-primary fw-semibold btn-add-material"><i class="fas fa-plus me-1"></i> Tambah Bahan Tambahan</button></td></tr>');
                        trAdd.find('.btn-add-material').click(function() {
                            var firstTr = optionGroupTbody.find('tr').first();
                            var tds = firstTr.find('td');
                            var currentRs = parseInt(tds.eq(0).attr('rowspan') || 1);
                            tds.eq(0).attr('rowspan', currentRs + 1);
                            tds.eq(1).attr('rowspan', currentRs + 1);
                            
                            var newTr = $('<tr></tr>');
                            var selBahan = $('<select class="form-select form-select-sm sel-bahan" style="font-size: 0.8rem;">'+bahanOptions+'</select>');
                            newTr.append($('<td></td>').append(selBahan));
                            var selCalc = $('<select class="form-select form-select-sm sel-calc" style="font-size: 0.8rem;">'+calcTypeOptions+'</select>');
                            newTr.append($('<td></td>').append(selCalc));
                            var selMult = $('<select class="form-select form-select-sm sel-mult" style="font-size: 0.8rem;">'+multiplierOptions+'</select>');
                            newTr.append($('<td></td>').append(selMult));
                            var inpDiv = $('<input type="number" class="form-control form-control-sm text-center inp-div" min="1" step="any" value="1" style="font-size: 0.8rem; padding: 0.25rem;">');
                            newTr.append($('<td></td>').append(inpDiv));
                            var inpScrap = $('<input type="number" class="form-control form-control-sm text-center inp-scrap" min="0" step="any" value="0" style="font-size: 0.8rem; padding: 0.25rem;">');
                            newTr.append($('<td></td>').append(inpScrap));
                            
                            var tdAction = $('<td></td>');
                            var btnGroup = $('<div class="btn-group w-100 shadow-sm"></div>');
                            var btnSave = $('<button class="btn btn-sm btn-primary" title="Simpan"><i class="fas fa-save"></i></button>');
                            btnSave.click(function() {
                                var dataPost = {
                                    product_id: productId,
                                    attribute_key: opt.attr_key,
                                    attribute_value: opt.val_label,
                                    bahan_id: selBahan.val(),
                                    calc_type: selCalc.val(),
                                    multiplier_key: selMult.val(),
                                    divider_factor: inpDiv.val(),
                                    scrap_factor: inpScrap.val()
                                };
                                if (!dataPost.bahan_id) {
                                    Swal.fire('Warning', 'Pilih bahan baku terlebih dahulu', 'warning');
                                    return;
                                }
                                var btn = $(this);
                                btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
                                $.post('<?= base_url("manufaktur/save_dynamic_bom") ?>', dataPost, function(r) {
                                    loadDynamicBOM(); 
                                });
                            });
                            btnGroup.append(btnSave);
                            
                            var btnRemoveUI = $('<button class="btn btn-sm btn-outline-danger" title="Batal"><i class="fas fa-times"></i></button>');
                            btnRemoveUI.click(function() {
                                newTr.remove();
                                tds.eq(0).attr('rowspan', currentRs);
                                tds.eq(1).attr('rowspan', currentRs);
                            });
                            btnGroup.append(btnRemoveUI);
                            
                            tdAction.append(btnGroup);
                            newTr.append(tdAction);
                            
                            newTr.insertBefore(trAdd);
                            selBahan.select2({width: '100%'});
                        });
                        
                        optionGroupTbody.append(trAdd);
                        table.append(optionGroupTbody);
                    });
                } catch (e) {
                    table.append('<tbody><tr><td colspan="8" class="text-danger p-3">JS Error: ' + e.message + '</td></tr></tbody>');
                }
                
                // Init select2 if needed
                $('.sel-bahan').select2({width: '100%'});
            }
        });
    }
    
    window.openImportBomModal = function() {
        $.get('<?= base_url("manufaktur/get_old_bom_materials") ?>', {product_id: productId}, function(res) {
            if(res.status === 'success') {
                if(res.data.length === 0) {
                    Swal.fire('Info', 'Tidak ditemukan BOM Lama untuk produk ini.', 'info');
                    return;
                }
                
                var html = '<div class="table-responsive"><table class="table table-bordered table-sm text-start" id="tbl-import-bom">';
                html += '<thead class="bg-light"><tr><th>Material BOM Lama</th><th>Mapping Ke Form Builder</th></tr></thead><tbody>';
                
                var formOptionsHtml = '<option value="">-- Lewati / Jangan Import --</option>';
                schemaOptions.forEach(function(opt) {
                    formOptionsHtml += '<option value="'+opt.attr_key+'|'+opt.val_label+'">['+opt.attr_label+'] '+opt.val_label+'</option>';
                });
                
                res.data.forEach(function(item) {
                    html += '<tr>';
                    html += '<td class="fw-bold align-middle">'+item.bahan_nama+'</td>';
                    html += '<td><select class="form-select form-select-sm sel-import-mapping" data-bahan="'+item.bahan_id+'">'+formOptionsHtml+'</select></td>';
                    html += '</tr>';
                });
                
                html += '</tbody></table></div>';
                
                Swal.fire({
                    title: 'Import dari BOM Lama',
                    html: html,
                    width: '800px',
                    showCancelButton: true,
                    confirmButtonText: 'Simpan Import',
                    cancelButtonText: 'Batal',
                    preConfirm: () => {
                        var mappings = [];
                        $('#tbl-import-bom .sel-import-mapping').each(function() {
                            var val = $(this).val();
                            if(val) {
                                var parts = val.split('|');
                                mappings.push({
                                    product_id: productId,
                                    attribute_key: parts[0],
                                    attribute_value: parts[1],
                                    bahan_id: $(this).data('bahan'),
                                    calc_type: 'fixed',
                                    multiplier_key: '',
                                    divider_factor: 1,
                                    scrap_factor: 0
                                });
                            }
                        });
                        return mappings;
                    }
                }).then((result) => {
                    if (result.isConfirmed && result.value.length > 0) {
                        var promises = [];
                        result.value.forEach(function(mapData) {
                            promises.push($.post('<?= base_url("manufaktur/save_dynamic_bom") ?>', mapData));
                        });
                        
                        Promise.all(promises).then(function() {
                            Swal.fire('Berhasil', 'Mapping berhasil di-import', 'success');
                            loadDynamicBOM();
                        });
                    }
                });
                
                // Initialize select2 inside SweetAlert
                $('#tbl-import-bom .sel-import-mapping').select2({
                    dropdownParent: $('.swal2-container'),
                    width: '100%'
                });
            }
        });
    };

    // Load if tab is clicked
    $('#dynamic-bom-tab').on('shown.bs.tab', function (e) {
        loadDynamicBOM();
    });
    
    // Or load immediately if it's the active tab
    if ($('#dynamic-bom-tab').hasClass('active')) {
        loadDynamicBOM();
    }
});
</script>
