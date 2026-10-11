/**
 * TGM ERP Product Management Script
 * Logic for BOM, Routing, and Profitability Analysis
 */

let globalBomData = [];
let globalRoutingData = [];
let globalPricingData = [];
let lastCalculatedHPP = 0;

function applyCalculatedHPPToAll() {
    if (lastCalculatedHPP <= 0) {
        Swal.fire('Info', 'Hitung HPP terlebih dahulu dengan mengisi BOM & Alur Kerja', 'info');
        return;
    }

    Swal.fire({
        title: 'Terapkan HPP?',
        text: `Terapkan HPP Rp ${new Intl.NumberFormat('id-ID').format(Math.round(lastCalculatedHPP))} ke semua baris skala harga?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Terapkan'
    }).then(result => {
        if (result.isConfirmed) {
            $('.hpp-input').val(new Intl.NumberFormat('id-ID').format(Math.round(lastCalculatedHPP)).replace(/,/g, '.'));
            Swal.fire({ icon: 'success', title: 'Berhasil', text: 'HPP telah diperbarui di tabel. Simpan matriks untuk menyimpan perubahan.', timer: 2000, showConfirmButton: false });
        }
    });
}

const formatPrice = (amount) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0
    }).format(amount || 0);
};

const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text).then(() => {
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Nama produk disalin', timer: 1500, showConfirmButton: false });
    });
};

// Use window assignment to avoid "already declared" errors if included in sub-views
window.initRupiahFields = () => {
    $(document).off('input', '.rupiah-input').on('input', '.rupiah-input', function () {
        let val = $(this).val().replace(/[^\d]/g, '');
        if (val !== "") $(this).val(new Intl.NumberFormat('id-ID').format(val).replace(/,/g, '.'));
    });

    // Also target specific fields in universal_pricing if they don't have the class
    $(document).off('input', 'input[name="hpp[]"], input[name="harga_1[]"]').on('input', 'input[name="hpp[]"], input[name="harga_1[]"]', function () {
        let val = $(this).val().replace(/[^\d]/g, '');
        if (val !== "") $(this).val(new Intl.NumberFormat('id-ID').format(val).replace(/,/g, '.'));
    });
};

const getRawValue = (formatted) => formatted.toString().replace(/[^\d]/g, '');

$(document).ready(function () {
    refreshAllData();
    window.initRupiahFields();
    initializeBahanSelect2();
    initializeRoutingSelect2();

    // BOM Checkbox Listeners
    $('#selectAllBOM').on('change', function() {
        $('.bom-checkbox').prop('checked', $(this).prop('checked'));
        updateBulkDeleteBomBtn();
    });

    $(document).on('change', '.bom-checkbox', function() {
        let allChecked = $('.bom-checkbox:checked').length === $('.bom-checkbox').length;
        $('#selectAllBOM').prop('checked', allChecked);
        updateBulkDeleteBomBtn();
    });

    $('#btnBulkDeleteBOM').on('click', deleteBulkBom);

    // Routing Checkbox Listeners
    $('#selectAllRouting').on('change', function() {
        $('.routing-checkbox').prop('checked', $(this).prop('checked'));
        updateBulkDeleteRoutingBtn();
    });

    $(document).on('change', '.routing-checkbox', function() {
        let allChecked = $('.routing-checkbox:checked').length === $('.routing-checkbox').length;
        $('#selectAllRouting').prop('checked', allChecked);
        updateBulkDeleteRoutingBtn();
    });

    $('#btnBulkDeleteRouting').on('click', deleteBulkRouting);

    // Auto-switch tab based on URL param
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    if (tabParam === 'form_builder') {
        $('#form-builder-tab').tab('show');
    }

    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).data('bs-target');
        if (target === '#bom') initializeBahanSelect2();
        if (target === '#routing') initializeRoutingSelect2();
    });
});

const updateBulkDeleteBomBtn = () => {
    let count = $('.bom-checkbox:checked').length;
    $('#bulkDeleteBomCount').text(count);
    if (count > 0) {
        $('#btnBulkDeleteBOM').removeClass('d-none');
    } else {
        $('#btnBulkDeleteBOM').addClass('d-none');
    }
};

const updateBulkDeleteRoutingBtn = () => {
    let count = $('.routing-checkbox:checked').length;
    $('#bulkDeleteRoutingCount').text(count);
    if (count > 0) {
        $('#btnBulkDeleteRouting').removeClass('d-none');
    } else {
        $('#btnBulkDeleteRouting').addClass('d-none');
    }
};

let globalDynamicBomData = [];

const refreshAllData = () => {
    if (!PRODUCT_ID) return;
    Promise.all([loadBomData(), loadRoutingData(), loadPricingData(), loadDynamicBomData()])
        .then(() => calculateHPP())
        .catch(err => console.error("Error refreshing data:", err));
};

async function loadDynamicBomData() {
    return new Promise((resolve) => {
        $.get(`${BASE_URL}manufaktur/get_dynamic_bom`, { product_id: PRODUCT_ID }, res => {
            if (res && res.status === 'success') {
                globalDynamicBomData = res.data;
            } else {
                globalDynamicBomData = [];
            }
            resolve();
        }, 'json').fail(() => {
            globalDynamicBomData = [];
            resolve();
        });
    });
}

// --- BOM FUNCTIONS ---
async function loadBomData() {
    return new Promise((resolve, reject) => {
        $.post(`${BASE_URL}manufaktur/get_bom_data`, { product_id: PRODUCT_ID }, res => {
            if (res.success) {
                globalBomData = res.data;
                displayBomData(res.data);
                $('#statBomCount').text(res.data.length);
            } else {
                displayBomData([]);
                $('#statBomCount').text('0');
            }
            resolve();
        }, 'json').fail(reject);
    });
}

function displayBomData(data) {
    let html = '';
    if (data && data.length > 0) {
        data.forEach((item, index) => {
            const netQty = parseFloat(item.quantity) || 0;
            const scrap = parseFloat(item.scrap_factor) || 0;
            const grossQty = netQty * (1 + (scrap / 100));
            html += `
                <tr>
                    <td class="text-center">
                        <input class="form-check-input bom-checkbox" type="checkbox" value="${item.bom_line_id}">
                    </td>
                    <td class="text-center text-muted small">${index + 1}</td>
                    <td style="min-width: 200px;">
                        <select class="form-select form-select-sm shadow-none bg-slate-50 text-dark border-0 fw-bold inline-material-select" 
                                style="width: 100%; font-size: 0.8rem;"
                                data-bom-id="${item.bom_line_id}">
                            <option value="${item.bahan_id && item.bahan_id !== '0' ? item.bahan_id : ''}" selected>
                                ${item.material_name ? item.material_name : '-- Pilih Material --'}
                            </option>
                        </select>
                        <div class="small text-muted mt-1" style="font-size: 0.75rem;">${item.keterangan || ''}</div>
                    </td>
                    <td>
                        <div class="fw-bold text-slate-800" style="max-width: 150px; white-space: normal; font-size: 0.8rem;">
                            ${item.variasi_nama ? item.variasi_nama : '<span class="badge bg-secondary">Global</span>'}
                        </div>
                    </td>
                    <td class="text-center">${(parseFloat(item.panjang) > 0) ? `<span class="badge bg-slate-100 text-slate-600">${item.panjang}x${item.lebar}</span>` : '-'}</td>
                    <td class="text-center">
                        <div class="fw-semibold text-primary" style="font-size:1.1em;">${new Intl.NumberFormat('id-ID', { maximumFractionDigits: 4 }).format(netQty)}</div>
                        ${(netQty > 0 && netQty < 1) ? `
                        <div class="mt-2 text-start p-1 px-2 bg-warning bg-opacity-10 border border-warning rounded shadow-sm" style="font-size:0.65rem; line-height:1.2; min-width: 140px;">
                            <div class="text-warning-emphasis fw-bold mb-1 border-bottom border-warning pb-1"><i class="fas fa-info-circle me-1"></i> Rumus Kapasitas</div>
                            <span class="text-slate-700">1 ${item.satuan || 'Bahan'} muat untuk <b>${Math.round(1 / netQty)} Pcs</b> produk ini.</span>
                        </div>` : ''}
                    </td>
                    <td class="text-center text-danger small fw-bold">${scrap > 0 ? scrap + '%' : '0%'}</td>
                    <td class="text-center bg-primary-pastel text-primary-pro fw-bold">${new Intl.NumberFormat('id-ID', { maximumFractionDigits: 4 }).format(grossQty)}</td>
                    <td class="text-end fw-bold text-success">
                        <a href="${BASE_URL}warehouse/materials?q=${encodeURIComponent(item.base_material_name || item.material_name)}" target="_blank" class="text-success text-decoration-none" title="Harga Satuan: ${formatPrice(parseFloat(item.unit_cost) || 0)}&#10;Klik untuk cek Master Material di Gudang">
                            ${formatPrice(grossQty * (parseFloat(item.unit_cost) || 0))} <i class="fas fa-external-link-alt ms-1 text-muted" style="font-size:0.6rem;"></i>
                        </a>
                    </td>
                    <td class="text-center small text-muted font-monospace">${item.satuan || '-'}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <button class="btn btn-sm btn-light border" onclick='openEditBomModal(${JSON.stringify(item)})'><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-light border text-danger" onclick="deleteBom(${item.bom_line_id})"><i class="bi bi-trash"></i></button>
                        </div>
                    </td>
                </tr>`;
        });
    } else {
        html = '<tr><td colspan="11" class="text-center py-5 text-muted">Belum ada material terdaftar.</td></tr>';
    }
    $('#bomTableBody').html(html);
    $('#selectAllBOM').prop('checked', false);
    updateBulkDeleteBomBtn();

    // Initialize select2 for inline material
    $('.inline-material-select').select2({
        placeholder: '-- Pilih Material --', allowClear: true, width: '100%',
        ajax: {
            url: `${BASE_URL}manufaktur/get_bahan_options`, dataType: 'json', delay: 250,
            data: params => ({ term: params.term }),
            processResults: data => ({ results: data.results || [] }), cache: true
        },
        minimumInputLength: 1
    }).on('select2:select', function(e) {
        const bom_id = $(this).data('bom-id');
        const bahan_id = e.params.data.id;
        updateBomMaterialInline(bom_id, bahan_id, this);
    });
}

function initializeBahanSelect2() {
    $('#bahan_id').select2({
        placeholder: '-- Cari Material --', allowClear: true, width: '100%',
        ajax: {
            url: `${BASE_URL}manufaktur/get_bahan_options`, dataType: 'json', delay: 250,
            data: params => ({ term: params.term }),
            processResults: data => ({ results: data.results || [] }), cache: true
        },
        minimumInputLength: 1
    }).on('select2:select', e => {
        $.post(`${BASE_URL}manufaktur/get_bahan_unit`, { bahan_id: e.params.data.id }, res => {
            if (res.success) $('#satuan').val(res.data.satuan);
        }, 'json');
    }).on('select2:open', function() {
        const variasiText = $('#harga_id option:selected').text().trim();
        if (variasiText && !variasiText.startsWith('--')) {
            const words = variasiText.split(' ').slice(0, 3).join(' ');
            setTimeout(() => {
                const searchBox = $('.select2-container--open .select2-search__field');
                if (searchBox.length && !searchBox.val()) {
                    searchBox.val(words).trigger('input').trigger('keyup');
                }
            }, 100);
        }
    });

    $('#edit_bahan_id').select2({
        placeholder: '-- Cari Material --', allowClear: true, width: '100%',
        dropdownParent: $('#modalEditBOM'),
        ajax: {
            url: `${BASE_URL}manufaktur/get_bahan_options`, dataType: 'json', delay: 250,
            data: params => ({ term: params.term }),
            processResults: data => ({ results: data.results || [] }), cache: true
        },
        minimumInputLength: 1
    });
}

$(document).on('submit', '#addBomForm', function (e) {
    e.preventDefault();
    $.post(`${BASE_URL}manufaktur/add_bom`, $(this).serialize(), res => {
        if (res.success) {
            $('#addBomForm')[0].reset();
            $('#bahan_id').val(null).trigger('change');
            $('#addBomSection').collapse('hide');
            loadBomData().then(calculateHPP);
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
        }
    }, 'json');
});

function openEditBomModal(item) {
    $('#edit_bom_line_id').val(item.bom_line_id);
    $('#edit_harga_id_bom').val(item.harga_id || '');
    
    if (item.bahan_id && item.bahan_id !== '0' && item.material_name) {
        var option = new Option(item.material_name, item.bahan_id, true, true);
        $('#edit_bahan_id').empty().append(option).trigger('change');
    } else {
        $('#edit_bahan_id').empty().val(null).trigger('change');
    }
    
    $('#edit_qty').val(item.quantity);
    if (item.quantity && parseFloat(item.quantity) > 0) {
        let qty = parseFloat(item.quantity);
        let cap = 1 / qty;
        // Check if cap is close to an integer
        if (Math.abs(cap - Math.round(cap)) < 0.001) {
            $('#edit_kapasitas_kalkulator').val(Math.round(cap));
        } else {
            $('#edit_kapasitas_kalkulator').val(cap.toFixed(2));
        }
    } else {
        $('#edit_kapasitas_kalkulator').val('');
    }
    
    $('#edit_scrap_factor').val(item.scrap_factor);
    $('#edit_manual_cost').val(item.manual_cost && parseFloat(item.manual_cost) > 0 ? parseFloat(item.manual_cost) : '');
    $('#modalEditBOM').modal('show');
}

$(document).on('submit', '#editBomForm', function (e) {
    e.preventDefault();
    $.post(`${BASE_URL}manufaktur/update_bom`, $(this).serialize(), res => {
        if (res.success) {
            $('#modalEditBOM').modal('hide');
            loadBomData().then(calculateHPP);
        }
    }, 'json');
});

function deleteBom(id) {
    Swal.fire({ title: 'Hapus BOM?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya, Hapus' }).then(result => {
        if (result.isConfirmed) $.post(`${BASE_URL}manufaktur/delete_bom`, { bom_id: id }, () => loadBomData().then(calculateHPP), 'json');
    });
}

function deleteBulkBom() {
    let selected = [];
    $('.bom-checkbox:checked').each(function() {
        selected.push($(this).val());
    });
    
    if (selected.length === 0) return;
    
    Swal.fire({ 
        title: `Hapus ${selected.length} BOM?`, 
        text: 'Data yang dihapus tidak bisa dikembalikan!',
        icon: 'warning', 
        showCancelButton: true, 
        confirmButtonText: 'Ya, Hapus Semua' 
    }).then(result => {
        if (result.isConfirmed) {
            $.post(`${BASE_URL}manufaktur/delete_bulk_bom`, { bom_ids: selected }, (res) => {
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false });
                    loadBomData().then(calculateHPP);
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                }
            }, 'json');
        }
    });
}

// --- ROUTING FUNCTIONS ---
async function loadRoutingData() {
    return new Promise((resolve, reject) => {
        $.post(`${BASE_URL}manufaktur/get_routing_data`, { product_id: PRODUCT_ID }, res => {
            if (res.success) {
                globalRoutingData = res.data;
                displayRoutingData(res.data);
                $('#statRoutingCount').text(res.data.length);

                if (res.variations && res.variations.length > 0) {
                    let optHtml = '<option value="">-- Semua Variasi (Global) --</option>';
                    res.variations.forEach(v => {
                        optHtml += `<option value="${v.harga_id}">${v.display_name || v.detail_product || ('Variasi #' + v.harga_id)}</option>`;
                    });
                    const curAdd = $('#routing_harga_id').val();
                    $('#routing_harga_id').html(optHtml).val(curAdd || '');
                    const curEdit = $('#edit_harga_id_routing').val();
                    $('#edit_harga_id_routing').html(optHtml).val(curEdit || '');
                }
            } else {
                displayRoutingData([]);
                $('#statRoutingCount').text('0');
            }
            resolve();
        }, 'json').fail(reject);
    });
}

function displayRoutingData(data) {
    let html = '';
    if (data && data.length > 0) {
        data.forEach((item, index) => {
            html += `
                <tr>
                    <td class="text-center">
                        <input class="form-check-input routing-checkbox" type="checkbox" value="${item.routing_id}">
                    </td>
                    <td class="text-center font-monospace small">${item.routing_urutan || (index + 1) * 10}</td>
                    <td>
                        <div class="fw-bold">${item.routing_name}</div>
                    </td>
                    <td>
                        ${item.variasi_nama ? `<span class="badge bg-info text-dark">${item.variasi_nama}</span>` : `<span class="badge bg-secondary">Global</span>`}
                    </td>
                    <td>
                        <div class="fw-semibold text-slate-700">${item.operator_name || '-'}</div>
                    </td>
                    <td class="text-center fw-bold text-primary-pro">${item.est_time || 0} Min</td>
                    <td class="text-end fw-bold text-success-pro">${formatPrice(item.labor_cost)}</td>
                    <td class="text-center small text-muted">
                        ${item.bom_material_name ? `<i class="bi bi-link-45deg text-primary"></i> ${item.bom_material_name}` : '-'}
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <button type="button" class="btn btn-sm btn-light border" onclick='openEditRoutingModal(${JSON.stringify(item)})'><i class="bi bi-pencil"></i></button>
                            <button type="button" class="btn btn-sm btn-light border text-danger" onclick="deleteRouting(${item.routing_id})"><i class="bi bi-trash"></i></button>
                        </div>
                    </td>
                </tr>`;
        });
    } else {
        html = '<tr><td colspan="9" class="text-center py-5 text-muted">Belum ada langkah manufaktur terdaftar.</td></tr>';
    }
    $('#routingTableBody').html(html);
    $('#selectAllRouting').prop('checked', false);
    updateBulkDeleteRoutingBtn();
}

function initializeRoutingSelect2() {
    // Select2 for Add Form (No Dropdown Parent)
    ['#operator_id'].forEach(s => $(s).select2({
        placeholder: '-- Pilih --', allowClear: true, width: '100%',
        ajax: { url: `${BASE_URL}manufaktur/get_operator_options`, dataType: 'json', delay: 250, data: p => ({ term: p.term }), processResults: d => ({ results: d.results || [] }) }
    }));
    ['#bom_line_id'].forEach(s => $(s).select2({
        placeholder: '-- Pilih BOM --', allowClear: true, width: '100%',
        ajax: { url: `${BASE_URL}manufaktur/get_bom_options`, dataType: 'json', delay: 250, data: p => ({ term: p.term, product_id: PRODUCT_ID }), processResults: d => ({ results: d.results || [] }) }
    }));

    // Auto-select variation on selecting BOM in Add Form
    $('#bom_line_id').off('select2:select').on('select2:select', function (e) {
        const d = e.params.data;
        if (d && d.harga_id) {
            $('#routing_harga_id').val(d.harga_id);
        } else if (d && d.attr_val) {
            const targetVal = d.attr_val.toLowerCase();
            $('#routing_harga_id option').each(function () {
                if ($(this).val() && $(this).text().toLowerCase().includes(targetVal)) {
                    $('#routing_harga_id').val($(this).val());
                    return false;
                }
            });
        }
    });

    // Select2 for Edit Modal
    ['#edit_operator_id'].forEach(s => $(s).select2({
        placeholder: '-- Pilih --', allowClear: true, width: '100%',
        dropdownParent: $('#modalEditRouting'),
        ajax: { url: `${BASE_URL}manufaktur/get_operator_options`, dataType: 'json', delay: 250, data: p => ({ term: p.term }), processResults: d => ({ results: d.results || [] }) }
    }));
    ['#edit_bom_line_id_routing'].forEach(s => $(s).select2({
        placeholder: '-- Pilih BOM --', allowClear: true, width: '100%',
        dropdownParent: $('#modalEditRouting'),
        ajax: { url: `${BASE_URL}manufaktur/get_bom_options`, dataType: 'json', delay: 250, data: p => ({ term: p.term, product_id: PRODUCT_ID }), processResults: d => ({ results: d.results || [] }) }
    }));

    // Auto-select variation on selecting BOM in Edit Modal
    $('#edit_bom_line_id_routing').off('select2:select').on('select2:select', function (e) {
        const d = e.params.data;
        if (d && d.harga_id) {
            $('#edit_harga_id_routing').val(d.harga_id);
        } else if (d && d.attr_val) {
            const targetVal = d.attr_val.toLowerCase();
            $('#edit_harga_id_routing option').each(function () {
                if ($(this).val() && $(this).text().toLowerCase().includes(targetVal)) {
                    $('#edit_harga_id_routing').val($(this).val());
                    return false;
                }
            });
        }
    });
}

$(document).on('submit', '#addRoutingForm', function (e) {
    e.preventDefault();
    const data = $(this).serializeArray();
    const lb = data.find(i => i.name === 'labor_cost');
    if (lb) lb.value = getRawValue(lb.value);

    // Provide visual feedback
    const btn = $(this).find('button[type="submit"]');
    const originalText = btn.html();
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...');

    $.post(`${BASE_URL}manufaktur/add_routing`, data, res => {
        btn.prop('disabled', false).html(originalText);
        if (res.success) {
            $('#addRoutingForm')[0].reset();
            $('#addRoutingSection').collapse('hide');
            Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false });
            loadRoutingData().then(calculateHPP);
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
        }
    }, 'json').fail(err => {
        btn.prop('disabled', false).html(originalText);
        Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan sistem saat menyimpan data.' });
        console.error("Routing submission error:", err);
    });
});

function openEditRoutingModal(item) {
    $('#edit_routing_id').val(item.routing_id);

    // Handle Variation Selection
    if (item.harga_id) {
        $('#edit_harga_id_routing').val(item.harga_id);
    } else if (item.variasi_nama) {
        let matched = false;
        const targetVar = item.variasi_nama.toLowerCase().replace(/^[a-z_]+:\s*/i, '').trim();
        $('#edit_harga_id_routing option').each(function () {
            if ($(this).val() && $(this).text().toLowerCase().includes(targetVar)) {
                $('#edit_harga_id_routing').val($(this).val());
                matched = true;
                return false;
            }
        });
        if (!matched) {
            $('#edit_harga_id_routing').val('');
        }
    } else {
        $('#edit_harga_id_routing').val('');
    }

    $('#edit_routing_urutan').val(item.routing_urutan);
    $('#edit_routing_name').val(item.routing_name);
    $('#edit_est_time').val(item.est_time);
    $('#edit_labor_cost').val(new Intl.NumberFormat('id-ID').format(item.labor_cost || 0).replace(/,/g, '.'));

    // Handle BOM Selection
    const bomId = item.bom_line_id || '99999';
    const bomText = item.bom_material_name || '-- Tanpa Material --';

    // Create option if not exists (for AJAX Select2)
    if ($('#edit_bom_line_id_routing').find("option[value='" + bomId + "']").length) {
        $('#edit_bom_line_id_routing').val(bomId).trigger('change');
    } else {
        var newOption = new Option(bomText, bomId, true, true);
        $('#edit_bom_line_id_routing').append(newOption).trigger('change');
    }

    // Handle Operator Selection
    const operatorId = item.operator_id || '';
    const operatorText = item.operator_name || '-- Pilih --';
    
    if (operatorId) {
        if ($('#edit_operator_id').find("option[value='" + operatorId + "']").length) {
            $('#edit_operator_id').val(operatorId).trigger('change');
        } else {
            var opOption = new Option(operatorText, operatorId, true, true);
            $('#edit_operator_id').append(opOption).trigger('change');
        }
    } else {
        $('#edit_operator_id').val(null).trigger('change');
    }

    $('#modalEditRouting').modal('show');
}

$(document).on('submit', '#editRoutingForm', function (e) {
    e.preventDefault();
    const data = $(this).serializeArray();
    const lb = data.find(i => i.name === 'labor_cost');
    if (lb) lb.value = getRawValue(lb.value);
    $.post(`${BASE_URL}manufaktur/update_routing`, data, () => {
        $('#modalEditRouting').modal('hide');
        loadRoutingData().then(calculateHPP);
    }, 'json');
});

function deleteRouting(id) {
    Swal.fire({ title: 'Hapus Routing?', icon: 'warning', showCancelButton: true }).then(r => {
        if (r.isConfirmed) $.post(`${BASE_URL}manufaktur/delete_routing`, { routing_id: id }, () => loadRoutingData().then(calculateHPP), 'json');
    });
}

function deleteBulkRouting() {
    let selected = [];
    $('.routing-checkbox:checked').each(function() {
        selected.push($(this).val());
    });
    
    if (selected.length === 0) return;
    
    Swal.fire({ 
        title: `Hapus ${selected.length} Langkah Kerja?`, 
        text: 'Data yang dihapus tidak bisa dikembalikan!',
        icon: 'warning', 
        showCancelButton: true, 
        confirmButtonText: 'Ya, Hapus Semua' 
    }).then(result => {
        if (result.isConfirmed) {
            $.post(`${BASE_URL}manufaktur/delete_bulk_routing`, { routing_ids: selected }, (res) => {
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false });
                    loadRoutingData().then(calculateHPP);
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                }
            }, 'json');
        }
    });
}

// --- HPP & ANALISA ---
async function loadPricingData() {
    return new Promise(resolve => $.post(`${BASE_URL}manufaktur/get_pricing_data`, { product_id: PRODUCT_ID }, r => { globalPricingData = r.success ? r.data : []; resolve(); }, 'json'));
}

window.getMatForVariation = function(hargaId) {
    let mat = 0;
    let varInfo = null;
    
    if (hargaId && hargaId !== '0') {
        varInfo = globalPricingData.find(p => p.harga_id == hargaId);
    }

    // 1. Static BOM lines
    globalBomData.forEach(i => {
        if (!i.harga_id || i.harga_id === '0' || i.harga_id == hargaId) {
            mat += (parseFloat(i.quantity) * (1 + (parseFloat(i.scrap_factor) / 100)) * (parseFloat(i.unit_cost) || 0));
        }
    });

    // 2. Dynamic BOM lines
    if (varInfo && globalDynamicBomData && globalDynamicBomData.length > 0) {
        let meta = {};
        if (varInfo.metadata) {
            try { meta = JSON.parse(varInfo.metadata); } catch(e){}
        }
        
        globalDynamicBomData.forEach(dm => {
            let match = false;
            const av = String(dm.attribute_value).trim();
            
            if (dm.attribute_key === 'ket_1') {
                // Compare against ket_1 value or metadata label
                if (av == varInfo.ket_1) match = true;
                else if (meta['ket_1'] && av == meta['ket_1']) match = true;
            } else if (dm.attribute_key === 'side_id') {
                if (av == varInfo.side_id) match = true;
                else if (meta['side'] && av == meta['side']) match = true;
            } else if (dm.attribute_key === 'jenisprint_id') {
                // attribute_value could be the label ("1 Sisi") or the ID ("1")
                if (av == varInfo.jenisprint_id) match = true;
                else if (meta['jenisprint'] && av == meta['jenisprint']) match = true;
                else if (meta['jenisprint_id'] && av == meta['jenisprint_id']) match = true;
            } else if (dm.attribute_key && dm.attribute_key.startsWith('metadata:')) {
                const mk = dm.attribute_key.split(':')[1];
                if (meta[mk] && av == meta[mk]) match = true;
            }
            
            if (match) {
                // Base quantity from BOM mapping (default 1 for fixed/operational items)
                let qty = parseFloat(dm.quantity) || 1;
                
                // Apply multiplier_key (numeric or metadata-ref)
                if (dm.multiplier_key) {
                    const mkVal = String(dm.multiplier_key).trim();
                    if (!isNaN(parseFloat(mkVal))) {
                        qty *= parseFloat(mkVal);
                    } else if (mkVal.startsWith('metadata:')) {
                        const mk = mkVal.split(':')[1];
                        if (meta[mk]) qty *= (parseFloat(meta[mk]) || 1);
                    }
                }
                
                // Apply divider
                let divider = parseFloat(dm.divider_factor) || 1;
                qty = qty / divider;
                
                mat += (qty * (1 + (parseFloat(dm.scrap_factor) / 100)) * (parseFloat(dm.unit_cost) || 0));
            }
        });
    }
    
    return mat;
};

window.getHppForVariation = function(hargaId) {
    let mat = window.getMatForVariation(hargaId);
    let lab = 0;
    globalRoutingData.forEach(i => lab += (parseFloat(i.labor_cost) || 0));
    return mat + lab + ((mat + lab) * 0.05);
};

function calculateHPP() {
    let lab = 0;
    globalRoutingData.forEach(i => lab += (parseFloat(i.labor_cost) || 0));

    let minMat = null;
    if (globalPricingData && globalPricingData.length > 0) {
        globalPricingData.forEach(p => {
            let mat = window.getMatForVariation(p.harga_id);
            if (minMat === null || mat < minMat) minMat = mat;
        });
    } else {
        minMat = window.getMatForVariation('0');
    }
    
    if (minMat === null) minMat = 0;
    
    const oh = (minMat + lab) * 0.05;
    const tt = minMat + lab + oh;
    lastCalculatedHPP = tt; // Minimum base HPP

    $('#totalMaterialCost').text(formatPrice(minMat)); 
    $('#totalLaborCost').text(formatPrice(lab));
    $('#totalOverheadCost').text(formatPrice(oh)); 
    $('#hppGrandTotal, #statHppValue').text(formatPrice(tt));
    
    // Build Variation List
    let varListHtml = '';
    if (globalPricingData && globalPricingData.length > 0) {
        let uniqueVariations = new Map();
        
        globalPricingData.forEach(p => {
            const hpp = window.getHppForVariation(p.harga_id);
            let variasiText = '';
            
            if (KATEGORI_ID == '2') {
                variasiText = p.mesin_nama || '-';
            } else {
                let badges = [];
                if (p.metadata && p.metadata.trim() !== '') {
                    try {
                        const meta = JSON.parse(p.metadata);
                        for (const key in meta) {
                            if (meta[key]) badges.push(meta[key]);
                        }
                    } catch(e) {}
                }
                if (badges.length === 0) {
                    if (p.ket_1 == '1' || p.ket_1 == '2') badges.push(p.ket_1 + ' Sisi');
                    else if (p.ket_1) badges.push(p.ket_1);
                }
                variasiText = badges.length > 0 ? badges.join(', ') : 'Base / Global';
            }
            if (p.detail_product) variasiText += ` (${p.detail_product})`;
            
            if (!uniqueVariations.has(variasiText)) {
                uniqueVariations.set(variasiText, { hpp: hpp, hargaId: p.harga_id });
            }
        });

        let varIdx = 0;
        uniqueVariations.forEach((vData, varName) => {
            const { hpp, hargaId } = vData;
            const collapseId = 'bomBreakdown_' + varIdx++;
            
            // Build breakdown rows
            let breakdownHtml = '';
            let varInfo = null;
            if (hargaId && hargaId !== '0') {
                varInfo = globalPricingData.find(p => p.harga_id == hargaId);
            }
            let meta = {};
            if (varInfo && varInfo.metadata) { try { meta = JSON.parse(varInfo.metadata); } catch(e){} }
            
            // Static BOM
            globalBomData.forEach(bom => {
                if (!bom.harga_id || bom.harga_id === '0' || bom.harga_id == hargaId) {
                    const cost = parseFloat(bom.unit_cost) || 0;
                    const qty = parseFloat(bom.quantity) || 1;
                    const scrap = parseFloat(bom.scrap_factor) || 0;
                    const total = qty * (1 + scrap/100) * cost;
                    if (total > 0 || cost > 0) {
                        breakdownHtml += `<div class="d-flex justify-content-between align-items-center py-1 border-bottom" style="font-size:0.72rem;">
                            <span class="text-slate-600">${bom.material_name || bom.bahan_nama || '-'} <span class="text-muted">(BOM Resep)</span></span>
                            <span class="fw-semibold">${formatPrice(total)}</span>
                        </div>`;
                    }
                }
            });
            
            // Dynamic BOM
            if (globalDynamicBomData && globalDynamicBomData.length > 0) {
                globalDynamicBomData.forEach(dm => {
                    let match = false;
                    const av = String(dm.attribute_value).trim();
                    
                    if (dm.attribute_key === 'ket_1') {
                        if (av == (varInfo ? varInfo.ket_1 : null)) match = true;
                        else if (meta['ket_1'] && av == meta['ket_1']) match = true;
                    } else if (dm.attribute_key === 'side_id') {
                        if (av == (varInfo ? varInfo.side_id : null)) match = true;
                        else if (meta['side'] && av == meta['side']) match = true;
                    } else if (dm.attribute_key === 'jenisprint_id') {
                        if (varInfo && av == varInfo.jenisprint_id) match = true;
                        else if (meta['jenisprint'] && av == meta['jenisprint']) match = true;
                        else if (meta['jenisprint_id'] && av == meta['jenisprint_id']) match = true;
                    } else if (dm.attribute_key && dm.attribute_key.startsWith('metadata:')) {
                        const mk = dm.attribute_key.split(':')[1];
                        if (meta[mk] && av == meta[mk]) match = true;
                    }
                    
                    if (match) {
                        let qty = parseFloat(dm.quantity) || 1;
                        if (dm.multiplier_key) {
                            const mkVal = String(dm.multiplier_key).trim();
                            if (!isNaN(parseFloat(mkVal))) {
                                qty *= parseFloat(mkVal);
                            } else if (mkVal.startsWith('metadata:')) {
                                const mk = mkVal.split(':')[1];
                                if (meta[mk]) qty *= (parseFloat(meta[mk]) || 1);
                            }
                        }
                        let divider = parseFloat(dm.divider_factor) || 1;
                        qty = qty / divider;
                        const cost = parseFloat(dm.unit_cost) || 0;
                        const scrap = parseFloat(dm.scrap_factor) || 0;
                        const total = qty * (1 + scrap/100) * cost;
                        const isManual = dm.cost_is_manual === true;
                        const manualBadge = isManual ? `<span class="badge bg-warning text-dark ms-1" style="font-size:0.55rem;">MANUAL</span>` : '';
                        const unitName = dm.unit_name ? dm.unit_name.toLowerCase() : 'satuan';
                        const multDisp = (dm.multiplier_key && dm.multiplier_key != '1' && dm.multiplier_key !== '') ? ` ×${dm.multiplier_key}` : '';
                        breakdownHtml += `<div class="d-flex justify-content-between align-items-center py-1 border-bottom" style="font-size:0.72rem;">
                            <span class="text-slate-600">${dm.bahan_nama || '-'} ${manualBadge}<span class="text-muted ms-1">(BOM Dinamis · ${formatPrice(cost)}/${unitName}${multDisp})</span></span>
                            <span class="fw-semibold ${isManual ? 'text-warning' : ''}">${formatPrice(total)}</span>
                        </div>`;
                    }
                });
            }
            
            if (!breakdownHtml) breakdownHtml = `<div class="text-muted small py-1" style="font-size:0.7rem;">Tidak ada bahan baku terhubung.</div>`;
            
            varListHtml += `
                <div class="border rounded-2 overflow-hidden">
                    <div class="d-flex justify-content-between align-items-center bg-light px-3 py-2 cursor-pointer" 
                         data-bs-toggle="collapse" data-bs-target="#${collapseId}" style="cursor:pointer;">
                        <span class="small fw-semibold text-slate-700">${varName}</span>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-success" style="font-size: 0.85rem;">${formatPrice(hpp)}</span>
                            <i class="fas fa-chevron-down text-muted" style="font-size:0.7rem;"></i>
                        </div>
                    </div>
                    <div class="collapse" id="${collapseId}">
                        <div class="px-3 py-2 bg-white">
                            ${breakdownHtml}
                            <div class="d-flex justify-content-between align-items-center pt-2 mt-1" style="font-size:0.75rem;">
                                <span class="fw-bold text-muted">Total HPP (+ OH 5%)</span>
                                <span class="fw-bold text-success">${formatPrice(hpp)}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    if (varListHtml === '') {
        varListHtml = '<div class="text-muted small py-2 text-center">Belum ada variasi.</div>';
    }
    $('#variationHppList').html(varListHtml);

    renderProfitMatrix();
    
    // Update simulator
    if (typeof populateSimVariasiSelect === 'function') {
        populateSimVariasiSelect();
        $('#simHppRef').text(formatPrice(getSimHpp()));
        buildSimRangeTable();
        $('#simHargaJual').trigger('input');
    }
}


function renderProfitMatrix() {
    if (!globalPricingData.length) return $('#profitMatrixContent').html('<div class="text-center py-5">No pricing data.</div>');
    let html = '<div class="table-responsive"><table class="table table-pro table-hover align-middle border"><thead><tr class="table-light"><th>Tier Qty</th><th>Variasi</th><th>Keterangan</th><th>Harga</th><th>HPP (Est)</th><th class="text-end">Laba</th><th class="text-center">Margin</th></tr></thead><tbody>';

    globalPricingData.forEach(i => {
        const j = parseFloat(i.harga_1) || 0;
        const hpp = window.getHppForVariation(i.harga_id);
        const l = j - hpp;
        const m = j > 0 ? (l / j) * 100 : 0;

        let variasiText = '';
        if (KATEGORI_ID == '2') {
            variasiText = i.mesin_nama ? `<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">${i.mesin_nama}</span>` : '-';
        } else {
            let badges = [];
            if (i.metadata && i.metadata.trim() !== '') {
                try {
                    const meta = JSON.parse(i.metadata);
                    for (const key in meta) {
                        if (meta[key]) badges.push(`<span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 me-1">${meta[key]}</span>`);
                    }
                } catch(e) {}
            }
            
            if (badges.length === 0) {
                if (i.ket_1 == '1' || i.ket_1 == '2') {
                    badges.push(`<span class="badge bg-light text-dark border">${i.ket_1} Sisi</span>`);
                } else if (i.ket_1) {
                    badges.push(`<span class="badge bg-light text-dark border">${i.ket_1}</span>`);
                }
            }
            
            variasiText = badges.length > 0 ? badges.join('') : '-';
        }

        html += `<tr>
            <td><div class="small fw-bold">${i.qty_min}-${i.qty_max}</div></td>
            <td>${variasiText}</td>
            <td><div class="small text-muted">${i.detail_product || '-'}</div></td>
            <td class="fw-bold text-slate-800">${formatPrice(j)}</td>
            <td class="text-muted small">${formatPrice(hpp)}</td>
            <td class="text-end fw-bold ${l >= 0 ? 'text-success' : 'text-danger'}">${formatPrice(l)}</td>
            <td class="text-center">
                <span class="badge ${m > 20 ? 'bg-success' : (m > 0 ? 'bg-warning' : 'bg-danger')}">
                    ${m.toFixed(1)}%
                </span>
            </td>
        </tr>`;
    });
    $('#profitMatrixContent').html(html + '</tbody></table></div>');
}

// --- COPY SETUP FUNCTIONS ---
function openCopyModal() {
    $('#copySetupForm')[0].reset();
    $('#modalCopySetup').modal('show');
}

$(document).on('submit', '#copySetupForm', function(e) {
    e.preventDefault();
    
    const targetIds = [];
    $('.copy-target-checkbox:checked').each(function() {
        targetIds.push($(this).val());
    });

    if (targetIds.length === 0) {
        Swal.fire('Peringatan', 'Silakan pilih setidaknya satu variasi tujuan.', 'warning');
        return;
    }

    const sourceId = $('#copy_source_harga_id').val();
    
    // Check if source is in targets
    if (targetIds.includes(sourceId)) {
        Swal.fire('Peringatan', 'Variasi tujuan tidak boleh sama dengan variasi sumber.', 'warning');
        return;
    }

    Swal.fire({
        title: 'Konfirmasi Salin Setup',
        text: 'BOM dan Routing di variasi tujuan akan DITIMPA dengan konfigurasi dari variasi sumber. Lanjutkan?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0dcaf0',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Timpa & Salin!'
    }).then((result) => {
        if (result.isConfirmed) {
            const btn = $(this).find('button[type="submit"]');
            const originalText = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...');

            $.ajax({
                url: `${BASE_URL}manufaktur/copy_bom_routing_to_variations`,
                type: 'POST',
                data: {
                    product_id: PRODUCT_ID,
                    source_harga_id: sourceId,
                    target_harga_ids: targetIds
                },
                dataType: 'json',
                success: function(res) {
                    btn.prop('disabled', false).html(originalText);
                    if (res.success) {
                        $('#modalCopySetup').modal('hide');
                        Swal.fire('Berhasil', res.message, 'success');
                        refreshAllData(); // Refresh BOM, Routing, and Profit Matrix
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: function(err) {
                    btn.prop('disabled', false).html(originalText);
                    Swal.fire('Error', 'Terjadi kesalahan sistem saat menyalin data.', 'error');
                }
            });
        }
    });
});

function updateBomVariationInline(bomLineId, selectObj) {
    const hargaId = selectObj.value;
    const originalBg = $(selectObj).css('background-color');
    
    $(selectObj).css('background-color', '#e2e8f0').prop('disabled', true);
    
    $.post(`${BASE_URL}manufaktur/update_bom_variation_inline`, {
        bom_line_id: bomLineId,
        harga_id: hargaId
    }, res => {
        $(selectObj).css('background-color', originalBg).prop('disabled', false);
        if (res.success) {
            loadBomData().then(calculateHPP);
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
        }
    }, 'json').fail(err => {
        $(selectObj).css('background-color', originalBg).prop('disabled', false);
        console.error("Variation update error:", err);
    });
}

function updateBomMaterialInline(bomLineId, bahanId, selectElement) {
    const originalBg = $(selectElement).next('.select2-container').find('.select2-selection').css('background-color');
    $(selectElement).next('.select2-container').find('.select2-selection').css('background-color', '#e2e8f0');
    
    $.post(`${BASE_URL}manufaktur/update_bom_material_inline`, {
        bom_line_id: bomLineId,
        bahan_id: bahanId
    }, res => {
        $(selectElement).next('.select2-container').find('.select2-selection').css('background-color', originalBg);
        if (res.success) {
            loadBomData().then(calculateHPP);
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
        }
    }, 'json').fail(err => {
        $(selectElement).next('.select2-container').find('.select2-selection').css('background-color', originalBg);
        console.error("Material update error:", err);
    });
}


// --- CAPACITY CALCULATOR HELPER ---
// Moved to HTML files directly

// --- SIMULATOR HARGA & LABA ---

function getSimHpp() {
    const $sel = $('#simVariasiSelect').find(':selected');
    if (!$sel.length || $sel.val() === 'all') return lastCalculatedHPP || 0;
    return parseFloat($sel.attr('data-hpp')) || lastCalculatedHPP || 0;
}

function buildSimRangeTable() {
    const hpp = getSimHpp();
    const minM = parseFloat($('#simMarginMin').val());
    const maxM = parseFloat($('#simMarginMax').val());
    
    $('#simHppRef').text(formatPrice(hpp));
    
    if (!hpp || isNaN(minM) || isNaN(maxM) || minM >= maxM) {
        $('#simRangeTable').html('<div class="text-white-50 text-center py-3 small">Isi HPP, min & max laba dengan benar</div>');
        return;
    }
    
    // Generate steps: max 7 rows
    const steps = [];
    const count = Math.min(7, Math.round(maxM - minM) + 1);
    const step = (maxM - minM) / (count - 1 || 1);
    for (let i = 0; i < count; i++) {
        const m = minM + i * step;
        if (m < 100) steps.push(Math.round(m * 10) / 10);
    }
    
    const cellBase = 'padding:6px 8px; border:none; vertical-align:middle;';
    const cellTransp = 'background:transparent;';
    
    let html = `<div style="font-size:0.78rem; color:#e2e8f0;">
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; border-bottom:1px solid rgba(255,255,255,0.12); padding:5px 8px;">
            <span style="color:rgba(255,255,255,0.45); font-size:0.68rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">Margin</span>
            <span style="color:rgba(255,255,255,0.45); font-size:0.68rem; font-weight:600; text-transform:uppercase; text-align:right;">Harga Jual</span>
            <span style="color:rgba(255,255,255,0.45); font-size:0.68rem; font-weight:600; text-transform:uppercase; text-align:right; padding-right:4px;">Laba / unit</span>
        </div>`;
    
    steps.forEach((m, idx) => {
        const harga = hpp / (1 - m / 100);
        const laba = harga - hpp;
        const isMiddle = idx === Math.floor(steps.length / 2);
        const rowBg = isMiddle ? 'background:rgba(6,182,212,0.18);' : (idx % 2 === 0 ? 'background:rgba(255,255,255,0.03);' : '');
        const markerHtml = isMiddle ? `<span style="background:#06b6d4;color:#0f172a;font-size:0.55rem;padding:1px 5px;border-radius:3px;margin-left:4px;font-weight:700;">SARAN</span>` : '';
        const marginColor = isMiddle ? '#67e8f9' : '#94a3b8';
        
        html += `<div style="display:grid; grid-template-columns:1fr 1fr 1fr; padding:6px 8px; ${rowBg} align-items:center; border-bottom:1px solid rgba(255,255,255,0.05);">
            <span style="color:${marginColor}; font-weight:600;">${m.toFixed(1)}%${markerHtml}</span>
            <span style="color:#67e8f9; font-weight:700; text-align:right;">${formatPrice(Math.round(harga))}</span>
            <span style="color:#4ade80; font-weight:600; text-align:right; padding-right:4px;">+${formatPrice(Math.round(laba))}</span>
        </div>`;
    });
    
    html += '</div>';
    $('#simRangeTable').html(html);
}

function populateSimVariasiSelect() {
    const $sel = $('#simVariasiSelect');
    $sel.find('option:not([value="all"])').remove();
    
    if (!window.getHppForVariation || !globalPricingData) return;
    
    let seen = new Map();
    globalPricingData.forEach(p => {
        let label = '';
        let meta = {};
        if (p.metadata) { try { meta = JSON.parse(p.metadata); } catch(e){} }
        const keys = Object.values(meta).filter(v => v);
        label = keys.length > 0 ? keys.join(', ') : (p.detail_product || 'Base');
        if (p.detail_product && !label.includes(p.detail_product)) label += ` (${p.detail_product})`;
        
        if (!seen.has(label)) {
            const hpp = window.getHppForVariation(p.harga_id);
            seen.set(label, hpp);
            $sel.append(`<option value="${label}" data-hpp="${hpp}" class="text-dark">${label} — HPP ${formatPrice(hpp)}</option>`);
        }
    });
}

$(document).on('input', '#simMarginMin, #simMarginMax', buildSimRangeTable);
$(document).on('change', '#simVariasiSelect', function() {
    $('#simHppRef').text(formatPrice(getSimHpp()));
    buildSimRangeTable();
    // Also update manual check
    $('#simHargaJual').trigger('input');
});

$(document).on('click', '#btnApplySimulatedPrice', function() {
    const minM = parseFloat($('#simMarginMin').val());
    const maxM = parseFloat($('#simMarginMax').val());
    if (isNaN(minM) || isNaN(maxM) || minM >= maxM) {
        alert("Mohon isi rentang target laba (MIN dan MAX) dengan benar terlebih dahulu.");
        return;
    }
    
    const idToLabel = {};
    if (typeof globalPricingData !== 'undefined') {
        globalPricingData.forEach(p => {
            let label = '';
            let meta = {};
            if (p.metadata) { try { meta = JSON.parse(p.metadata); } catch(e){} }
            const keys = Object.values(meta).filter(v => v);
            label = keys.length > 0 ? keys.join(', ') : (p.detail_product || 'Base');
            if (p.detail_product && !label.includes(p.detail_product)) label += ` (${p.detail_product})`;
            idToLabel[p.harga_id] = label;
        });
    }
    
    // Group rows in the table by Variation Label
    const rowsByLabel = {};
    $('.row-bom-checkbox').each(function() {
        const id = $(this).val();
        const label = idToLabel[id] || 'Unknown';
        if (!rowsByLabel[label]) rowsByLabel[label] = { rows: [], hpp: window.getHppForVariation(id) };
        rowsByLabel[label].rows.push($(this).closest('tr'));
    });
    
    const selVal = $('#simVariasiSelect').val();
    let appliedCount = 0;
    
    for (const label in rowsByLabel) {
        if (selVal !== 'all' && selVal !== label) continue;
        
        const group = rowsByLabel[label];
        const rows = group.rows;
        const hpp = group.hpp;
        if (!hpp) continue;
        
        const rowCount = rows.length;
        const step = rowCount > 1 ? (maxM - minM) / (rowCount - 1) : 0;
        
        rows.forEach((row, idx) => {
            // Highest margin for lowest qty (first row), lowest margin for highest qty
            const currentMargin = rowCount > 1 ? maxM - (step * idx) : maxM;
            const targetHarga = hpp / (1 - (currentMargin / 100));
            // Round to nearest 100
            const roundedHarga = Math.round(targetHarga / 100) * 100;
            
            row.find('input[name="harga_1[]"]').val(roundedHarga).trigger('input');
        });
        
        appliedCount++;
    }
    
    if (appliedCount > 0) {
        const $btn = $(this);
        const oriText = $btn.html();
        $btn.html('<i class="bi bi-check-circle-fill me-1"></i>Berhasil Diterapkan!');
        $btn.removeClass('btn-info text-dark').addClass('btn-success text-white');
        setTimeout(() => {
            $btn.html(oriText);
            $btn.removeClass('btn-success text-white').addClass('btn-info text-dark');
        }, 2500);
    } else {
        alert("Tidak ada variasi yang cocok di tabel Skala Harga.");
    }
});

$(document).on('input', '#simHargaJual', function() {
    const rawVal = $(this).val().replace(/[^0-9]/g, '');
    const harga = parseFloat(rawVal) || 0;
    if (rawVal) $(this).val(new Intl.NumberFormat('id-ID').format(harga));
    else $(this).val('');
    
    const hpp = getSimHpp();
    if (!hpp || harga <= 0) {
        $('#simSaranMargin').text('0%').attr('class', 'text-info');
        $('#simLabaAmount').text('Rp 0').attr('class', 'text-success small');
        return;
    }
    
    const margin = ((harga - hpp) / harga) * 100;
    const laba = harga - hpp;
    $('#simSaranMargin').text(margin.toFixed(2) + '%');
    $('#simLabaAmount').text((laba >= 0 ? '+' : '') + formatPrice(Math.round(laba)));
    
    if (margin > 25) {
        $('#simSaranMargin').attr('class', 'text-success fw-bold');
        $('#simLabaAmount').attr('class', 'text-success small fw-bold');
    } else if (margin > 0) {
        $('#simSaranMargin').attr('class', 'text-warning fw-bold');
        $('#simLabaAmount').attr('class', 'text-warning small fw-bold');
    } else {
        $('#simSaranMargin').attr('class', 'text-danger fw-bold');
        $('#simLabaAmount').attr('class', 'text-danger small fw-bold');
    }
});

function toggleProductStatus(productId, newStatus) {
    const actionText = newStatus === 1 ? 'mengaktifkan' : 'menonaktifkan';
    const btnText = newStatus === 1 ? 'Ya, Aktifkan!' : 'Ya, Nonaktifkan!';
    const btnColor = newStatus === 1 ? '#198754' : '#dc3545';
    
    Swal.fire({
        title: 'Konfirmasi',
        text: `Apakah Anda yakin ingin ${actionText} produk ini?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: btnColor,
        cancelButtonColor: '#6c757d',
        confirmButtonText: btnText,
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            
            const baseUrl = window.location.origin + window.location.pathname.split('/manufaktur')[0];
            $.ajax({
                url: baseUrl + '/manufaktur/toggleProductStatus',
                type: 'POST',
                data: { product_id: productId, status: newStatus },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Gagal!', res.message || 'Terjadi kesalahan sistem.', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Gagal menghubungi server.', 'error');
                }
            });
        }
    });
}
