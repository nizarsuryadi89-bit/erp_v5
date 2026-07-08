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

    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).data('bs-target');
        if (target === '#bom') initializeBahanSelect2();
        if (target === '#routing') initializeRoutingSelect2();
    });
});

const refreshAllData = () => {
    if (!PRODUCT_ID) return;
    Promise.all([loadBomData(), loadRoutingData(), loadPricingData()])
        .then(() => calculateHPP())
        .catch(err => console.error("Error refreshing data:", err));
};

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
                    <td class="text-center text-muted small">${index + 1}</td>
                    <td>
                        <div class="fw-bold text-slate-800">${item.material_name}</div>
                        <div class="small text-muted">${item.keterangan || ''}</div>
                    </td>
                    <td>
                        ${item.variasi_nama ? `<span class="badge bg-info text-dark">${item.variasi_nama}</span>` : `<span class="badge bg-secondary">Global</span>`}
                    </td>
                    <td class="text-center">${(parseFloat(item.panjang) > 0) ? `<span class="badge bg-slate-100 text-slate-600">${item.panjang}x${item.lebar}</span>` : '-'}</td>
                    <td class="text-center fw-semibold">${netQty.toFixed(2)}</td>
                    <td class="text-center text-danger small fw-bold">${scrap > 0 ? scrap + '%' : '0%'}</td>
                    <td class="text-center bg-primary-pastel text-primary-pro fw-bold">${grossQty.toFixed(2)}</td>
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
        html = '<tr><td colspan="9" class="text-center py-5 text-muted">Belum ada material terdaftar.</td></tr>';
    }
    $('#bomTableBody').html(html);
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
    $('#edit_material_name_display').text(item.material_name);
    $('#edit_qty').val(item.quantity);
    $('#edit_scrap_factor').val(item.scrap_factor);
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

// --- ROUTING FUNCTIONS ---
async function loadRoutingData() {
    return new Promise((resolve, reject) => {
        $.post(`${BASE_URL}manufaktur/get_routing_data`, { product_id: PRODUCT_ID }, res => {
            if (res.success) {
                globalRoutingData = res.data;
                displayRoutingData(res.data);
                $('#statRoutingCount').text(res.data.length);
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
                    <td class="text-center font-monospace small">${item.routing_urutan || (index + 1) * 10}</td>
                    <td>
                        <div class="fw-bold">${item.routing_name}</div>
                        ${item.is_finishing == '1' ? '<span class="badge bg-warning text-dark" style="font-size:0.6rem">Finishing</span>' : '<span class="badge bg-primary text-white" style="font-size:0.6rem">Cetak</span>'}
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
        html = '<tr><td colspan="8" class="text-center py-5 text-muted">Belum ada langkah manufaktur terdaftar.</td></tr>';
    }
    $('#routingTableBody').html(html);
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
    $('#edit_harga_id_routing').val(item.harga_id || '');
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
    // setTimeout(() => initializeRoutingSelect2(), 200); // Removed as it might re-init incorrectly
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

// --- HPP & ANALISA ---
async function loadPricingData() {
    return new Promise(resolve => $.post(`${BASE_URL}manufaktur/get_pricing_data`, { product_id: PRODUCT_ID }, r => { globalPricingData = r.success ? r.data : []; resolve(); }, 'json'));
}

function calculateHPP() {
    let mat = 0, lab = 0;
    globalBomData.forEach(i => mat += (parseFloat(i.quantity) * (1 + (parseFloat(i.scrap_factor) / 100)) * (parseFloat(i.unit_cost) || 0)));
    globalRoutingData.forEach(i => lab += (parseFloat(i.labor_cost) || 0));
    const oh = (mat + lab) * 0.05, tt = mat + lab + oh;
    lastCalculatedHPP = tt;
    $('#totalMaterialCost').text(formatPrice(mat)); $('#totalLaborCost').text(formatPrice(lab));
    $('#totalOverheadCost').text(formatPrice(oh)); $('#hppGrandTotal, #statHppValue').text(formatPrice(tt));
    renderProfitMatrix(tt);
}



function renderProfitMatrix(hpp) {
    if (!globalPricingData.length) return $('#profitMatrixContent').html('<div class="text-center py-5">No pricing data.</div>');
    let html = '<div class="table-responsive"><table class="table table-pro table-hover align-middle border"><thead><tr class="table-light"><th>Tier Qty</th><th>Variasi</th><th>Keterangan</th><th>Harga</th><th>HPP</th><th class="text-end">Laba</th><th class="text-center">Margin</th></tr></thead><tbody>';

    globalPricingData.forEach(i => {
        const j = parseFloat(i.harga_1) || 0;
        const l = j - hpp;
        const m = j > 0 ? (l / j) * 100 : 0;

        let variasiText = '';
        if (KATEGORI_ID == '2') {
            variasiText = i.mesin_nama ? `<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">${i.mesin_nama}</span>` : '-';
        } else {
            variasiText = `<span class="badge bg-light text-dark border">${i.ket_1 == 1 ? '1 Sisi' : '2 Sisi'}</span>`;
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
