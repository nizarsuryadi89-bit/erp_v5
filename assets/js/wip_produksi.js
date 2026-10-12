/** ==========================================================================
 *  Production Hub Application Module
 *  ========================================================================== */
const ProductionApp = {
    config: {
        baseUrl: WIP_CONFIG.baseUrl,
        standardRolls: WIP_CONFIG.standardRolls,
        tolerance: WIP_CONFIG.tolerance,
        csrfToken: { [WIP_CONFIG.csrfTokenName]: WIP_CONFIG.csrfHash }
    },

    // Initialize state object
    state: {
        activeCategory: 'all',
        csrfHash: WIP_CONFIG.csrfHash,
        operationSnapshots: {},
        advanceRequests: {}
    },

    machineOptionsUrl(stepName, catId, ids) {
        const params = new URLSearchParams({ stepName, catId });
        ids.forEach(id => params.append('ids[]', id));
        return this.config.baseUrl + 'get_machines_ajax?' + params.toString();
    },

    finishingNeedsMachine(stepName, categoryId = 0, productName = '') {
        if (Number(categoryId) === 1 || /\bA3\b/i.test(String(productName || '') + ' ' + String(stepName || ''))) return false;
        return /\b(?:cutting|potong|kiss\s*cut|die[\s-]*cut)\b/i.test(String(stepName || ''));
    },

    init() {
        this.initDataTable();
        this.bindEvents();
        this.initAutoFocus();
        
        // Anti-Autofill Aggressive Workaround
        setTimeout(() => {
            const urlParams = new URLSearchParams(window.location.search);
            const searchSpk = urlParams.get('search_spk');
            
            if (!searchSpk) {
                $('.search-wip-table').val('');
                $('input[type="search"]').val('');
                if ($.fn.DataTable) {
                    $('#tableSemuaProduksi').DataTable().search('').draw();
                }
            }
        }, 300);
        setTimeout(() => {
            const urlParams = new URLSearchParams(window.location.search);
            const searchSpk = urlParams.get('search_spk');
            
            if (searchSpk) {
                $('.search-wip-table').val(searchSpk);
                $('input[type="search"]').val(searchSpk);
                if ($.fn.DataTable) {
                    $('#tableSemuaProduksi').DataTable().search(searchSpk).draw();
                }
                if ($('.search-wip-table').length > 0) {
                    ProductionApp.searchWipTable($('.search-wip-table')[0]);
                }
            } else {
                $('.search-wip-table').val('');
                $('input[type="search"]').val('');
            }
        }, 800);
    },

    initAutoFocus() {
        if (window.WipPinQr) window.WipPinQr.init();
        // Bootstrap Modals Autofocus
        $('#modalSelesaiSPK').on('shown.bs.modal', () => $('#selesaiSpkPin').focus());
        $('#modalSelesaiFinishing').on('shown.bs.modal', () => $('#finOperatorPin').focus());
        $('#modalNestingCompletion').on('shown.bs.modal', () => $('#compOperatorPin').focus());
        $('#modalLaporRijek').on('shown.bs.modal', () => $('#rijekPin').focus());

        // Enter Key Triggers
        $('#selesaiSpkPin').on('keyup', (e) => { if (e.keyCode === 13) this.submitSelesaiSPK(); });
        $('#finOperatorPin').on('keyup', (e) => { if (e.keyCode === 13) this.submitFinishing(); });
        $('#compOperatorPin').on('keyup', (e) => { if (e.keyCode === 13) this.submitCompletion(); });
    },

    initDataTable() {
        if ($.fn.DataTable && $('#tableSemuaProduksi').length && !$.fn.DataTable.isDataTable('#tableSemuaProduksi')) {
            $('#tableSemuaProduksi').DataTable({
                language: { sSearch: "Cari:", oPaginate: { sNext: "→", sPrevious: "←" } },
                pageLength: 50, responsive: true, ordering: false, dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 text-end'f>>t<'row'<'col-sm-12 col-md-6'i><'col-sm-12 col-md-6 text-end'p>>"
            });
        }
    },

    bindEvents() {
        $(document).on('change', '.check-item-nesting', (e) => {
            const checkbox = $(e.target);
            const rg = checkbox.data('ripping-group');
            if (rg && rg.trim() !== '') {
                const isChecked = checkbox.prop('checked');
                $(`.check-item-nesting[data-ripping-group="${rg}"]`).prop('checked', isChecked);
            }
            this.updateNestingSummary();
        });
        $(document).on('change', '.check-all-nesting', (e) => this.toggleAllNesting(e.target.checked, e.target));
        $(document).on('keyup', '#searchBahanInput', (e) => this.searchBahan(e.target.value));
        $(document).on('keyup', '.search-wip-table', (e) => this.searchWipTable(e.target));
    },

    searchWipTable(inputEl) {
        const val = $(inputEl).val().toLowerCase();
        const targetSelector = $(inputEl).data('target');
        const $table = $(targetSelector);

        if (!$table.length) return;

        // Iterate through all item rows and match against SPK or item text
        $table.find('.tr-item-row').each(function () {
            const nospk = ($(this).data('nospk') || '').toString().toLowerCase();
            const materialName = ($(this).data('material') || '').toString().toLowerCase();
            const rowText = $(this).text().toLowerCase();

            if (nospk.includes(val) || materialName.includes(val) || rowText.includes(val)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        // Hide group headers if all items within the group are hidden
        $table.find('.group-header').each(function () {
            // Find all item rows following this group header until the next group header
            const $items = $(this).nextUntil('.group-header', '.tr-item-row');
            const visibleCount = $items.filter(':visible').length;

            if (visibleCount === 0) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    },

    escapeText(value) {
        return $('<div>').text(value == null ? '' : String(value)).html();
    },

    captureOperations(ids) {
        const snapshots = {};
        ids.forEach(id => {
            const row = $(`.check-item-nesting[data-id="${id}"]`).first();
            const configured = (WIP_CONFIG.operationSnapshots || {})[id];
            snapshots[id] = row.length ? { id: row.data('step-id'), version: row.data('step-version'), target: row.data('qty'), completed: row.data('step-output'), productName: row.data('product-name') } : configured;
            if (snapshots[id] && snapshots[id].id) this.state.operationSnapshots[id] = snapshots[id];
        });
        return snapshots;
    },

    resultInputs(id, prefix, target, completed = null) {
        const snapshot = this.state.operationSnapshots[id] || {};
        if (!Number.isFinite(Number(target))) target = snapshot.target ?? target;
        if (completed == null) completed = $(`.check-item-nesting[data-id="${id}"]`).first().data('step-output') ?? snapshot.completed ?? 0;
        const goal = Number(target), recorded = Number(completed);
        const suggested = target !== '' && Number.isFinite(goal) && goal >= 0 && Number.isFinite(recorded)
            ? Math.max(0, Number((goal - Math.max(0, recorded)).toFixed(2))) : '';
        return `<div class="row g-2 mt-2">
            <div class="col-6"><label class="small fw-bold">Hasil baik aktual (target ${this.escapeText(target)})</label>
                <input type="number" min="0" step="0.01" class="form-control form-control-sm" id="${prefix}_good_${id}" value="${suggested}" placeholder="Jumlah tambahan hasil" required><div class="form-text">Saran sisa target; sesuaikan dengan hasil aktual.</div></div>
            <div class="col-6"><label class="small fw-bold">Hasil rusak aktual</label>
                <input type="number" min="0" step="0.01" class="form-control form-control-sm" id="${prefix}_reject_${id}" value="0"></div>
        </div>`;
    },

    resultValues(ids, prefix) {
        const result = {};
        for (const id of ids) {
            const raw = $(`#${prefix}_good_${id}`).val();
            const qty = Number(raw);
            const reject = Number($(`#${prefix}_reject_${id}`).val());
            if (raw === '' || raw == null || !Number.isFinite(qty) || qty < 0 || !Number.isFinite(reject) || reject < 0) {
                Swal.fire('Hasil belum lengkap', 'Isi hasil baik dan rusak aktual pada setiap item.', 'warning');
                return null;
            }
            result[id] = { qty_output: qty, qty_missing: reject };
        }
        return result;
    },

    async sendSPKToPB(nospk) {
        const confirmation = await Swal.fire({ title: 'Serahkan SPK ke PB?', text: 'Kesiapan setiap item diperiksa oleh server.', icon: 'question', showCancelButton: true });
        if (!confirmation.isConfirmed) return;
        $.post(this.config.baseUrl + 'kirim_pb_spk', this.getAuthData({ nospk }), res => {
            if (res.success) location.reload();
            else Swal.fire('Gagal', res.message, 'error');
        }, 'json').fail(() => Swal.fire('Gagal', 'Koneksi server gagal.', 'error'));
    },

    // Helper to get fresh CSRF token if needed
    getAuthData(extraData = {}) {
        let payload = {};
        // If we want to use the latest token (though in CI3 it might rotate)
        // For simplicity, we use the initial one or rely on standard jQuery Ajax setup if present.
        // But here we explicitly add it.
        payload[WIP_CONFIG.csrfTokenName] = this.state.csrfHash; // Use state hash which we can update
        const ids = extraData.ids || (extraData.produksi_id ? [extraData.produksi_id] : []);
        const expected = {}, versions = {};
        ids.forEach(id => {
            const snapshot = this.state.operationSnapshots[id];
            if (snapshot && snapshot.id) {
                expected[id] = snapshot.id;
                if (snapshot.version) versions[id] = snapshot.version;
            }
        });
        return { ...payload, expected_step_ids: expected, expected_versions: versions, ...extraData };
    },

    // Helper to estimate linear meter efficiently on rolls
    estimateLinearMeter(p, l, qty, W) {
        if (p <= 0 || l <= 0 || W <= 0 || qty <= 0) return 0;
        let meter1 = Infinity;
        if (p <= W) {
            let cols1 = Math.floor(W / p);
            let rows1 = Math.ceil(qty / cols1);
            meter1 = rows1 * l;
        }
        let meter2 = Infinity;
        if (l <= W) {
            let cols2 = Math.floor(W / l);
            let rows2 = Math.ceil(qty / cols2);
            meter2 = rows2 * p;
        }
        let bestMeter = Math.min(meter1, meter2);
        if (bestMeter === Infinity) {
            bestMeter = Math.max(p, l) * qty;
        }
        return bestMeter;
    },

    /** SPK & Tag Handlers */
    previewSPK(nospk) {
        $('#lblNospkPreview').text(nospk);
        $('#modalPreviewSPK').modal('show');
        $('#iframePreview').attr('src', WIP_CONFIG.siteUrl + 'transaksi/preview_invoice/' + nospk);
    },

    previewTag(id) {
        $('#modalPreviewTag').modal('show');
        $('#iframePreviewTag').attr('src', this.config.baseUrl + 'print_tag/' + id);
    },

    previewTagSPK(idsStr) {
        $('#modalPreviewTag').modal('show');
        $('#iframePreviewTag').attr('src', this.config.baseUrl + 'print_tag_ids?ids=' + idsStr);
    },

    prosesSPK(nospk, kategoriId = "") {
        $('#lblNospk').text(nospk);
        $('#modalProsesSPK').modal('show');
        $('#loadingProses').removeClass('d-none');
        $('#contentProses').addClass('d-none');

        $.post(this.config.baseUrl + 'get_spk_details_ajax', this.getAuthData({
            nospk: nospk,
            kategori_id: kategoriId
        }), (res) => {
            $('#loadingProses').addClass('d-none');
            $('#contentProses').removeClass('d-none');
            $('#listProdukRouting').html(''); // Clear first
            (res.items || []).forEach(it => {
                if (it.workflow_step_id) this.state.operationSnapshots[it.produksi_id] = { id: it.workflow_step_id, version: it.workflow_version, target: it.qty, completed: it.workflow_qty_output ?? 0, productName: it.product_nama };
            });
            let html = (res.items || []).map(it => {
                let btnAction = '';
                // Determine Action Button based on Workflow
                if (it.status_produksi === 'approved' || it.status_produksi === 'approve' || it.status_produksi === 'pending' || it.status_produksi === '' || it.status_produksi === '0' || !it.status_produksi) {
                    // Start Button
                    btnAction = `<button onclick="ProductionApp.advanceStep(${it.produksi_id}, 'start')" class="btn btn-primary btn-sm rounded-pill fw-bold"><i class="fas fa-play me-1"></i> Mulai</button>`;
                } else if (['proses', 'finishing'].includes(it.status_produksi)) {
                    if (it.total_steps > 1 && it.current_step_urutan < it.total_steps) {
                        // Intermediate: Advance Button
                        let nextName = it.next_step_name || 'Next Step';
                        let actStep = null;
                        if (it.routing) {
                            actStep = it.routing.find(r => r.status_produksi == '1');
                        }

                        let bomQty = (actStep && actStep.quantity) ? parseFloat(actStep.quantity) : (it.bom ? parseFloat(it.bom.quantity || it.bom.bom_qty) : 0);
                        let estQty = (bomQty > 0) ? (bomQty * parseFloat(it.qty)) : parseFloat(it.qty);
                        let bomMatId = (actStep && actStep.routing_material_id) ? actStep.routing_material_id : 0;
                        let satuanStep = (actStep && actStep.routing_material_satuan) ? actStep.routing_material_satuan : (it.satuan_nama || 'Unit');
                        let prevPakai = (actStep && actStep.qty_bahan_pakai) ? actStep.qty_bahan_pakai : 0;
                        let isFinishing = (actStep && actStep.is_finishing == '1') ? 1 : 0;

                        btnAction = `<button onclick="ProductionApp.advanceStep(${it.produksi_id}, 'next', '${it.current_step_name}', ${estQty}, ${bomMatId}, ${prevPakai}, '${satuanStep}', ${it.kategori_id}, ${isFinishing})" class="btn btn-info text-white btn-sm rounded-pill fw-bold" title="Lanjut ke: ${nextName}"><i class="fas fa-forward me-1"></i> Selesai ${it.current_step_name}</button>`;
                    } else {
                        // Final Step: Control Button (Use standard finish)
                        btnAction = `<a href="${this.config.baseUrl}control_center/${it.produksi_id}" class="btn btn-success btn-sm rounded-pill fw-bold"><i class="fas fa-check me-1"></i> Finalisasi</a>`;
                    }
                } else if (['done', 'on_pb'].includes(it.status_produksi)) {
                    btnAction = `<span class="badge bg-success"><i class="fas fa-check-double"></i> DONE</span>`;
                }

                return `
                <tr>
                    <td class="py-2 px-4">
                        <div class="fw-bold">${it.product_nama}</div>
                        <div class="text-muted small">${it.dimensi || ''}</div>
                    </td>
                    <td>${it.bom?.bahan_nama || '-'}</td>
                    <td class="text-center fw-bold">${it.qty}</td>
                    <td class="text-center">
                        <span class="badge ${this.getStatusClass(it.status_produksi)} rounded-pill mb-1 d-block">${(it.status_produksi || 'QUEUE').toUpperCase()}</span>
                        ${it.current_step_name ? `<div class="small fw-bold text-muted" style="font-size: 0.6rem;">Step: ${it.current_step_name}</div>` : ''}
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            ${btnAction}
                            <a href="${this.config.baseUrl}control_center/${it.produksi_id}" class="btn btn-outline-dark btn-sm rounded-pill" title="Full Control"><i class="fas fa-cog"></i></a>
                        </div>
                    </td>
                </tr>`;
            }).join('');
            $('#listProdukRouting').html(html);
        }, 'json').fail((xhr, status, error) => {
            console.error("AJAX Error:", status, error);
            $('#loadingProses').addClass('d-none');
            alert("Gagal memuat data. Server Error: " + (xhr.responseJSON?.message || error || "Unknown Error"));
        });
    },

    selesaiSPK(nospk, kategoriId = "") {
        $('#lblNospkSelesai').text(nospk);
        // Store metadata on modal for submission
        $('#modalSelesaiSPK').data('nospk', nospk).data('cat', kategoriId);
        $('#selesaiSpkPin').val(''); // Clear previous PIN
        $('#modalSelesaiSPK').modal('show');
        $('#loadingSelesai').removeClass('d-none');
        $('#contentSelesai').addClass('d-none');

        $.post(this.config.baseUrl + 'get_spk_details_ajax', this.getAuthData({
            nospk: nospk,
            kategori_id: kategoriId
        }), (res) => {
            $('#loadingSelesai').addClass('d-none');
            $('#contentSelesai').removeClass('d-none');

            $('#modalSelesaiSPK').data('ids', (res.items || []).filter(it => ['proses', 'finishing'].includes(it.status_produksi)).map(it => it.produksi_id));
            (res.items || []).forEach(it => {
                if (it.workflow_step_id) this.state.operationSnapshots[it.produksi_id] = { id: it.workflow_step_id, version: it.workflow_version, target: it.qty, completed: it.workflow_qty_output ?? 0, productName: it.product_nama };
            });
            let totalM2 = 0;
            let html = (res.items || []).map(it => {
                let p = parseFloat(it.panjang || 0);
                let l = parseFloat(it.lebar || 0);
                let bp = parseFloat(it.bom?.panjang || 0);
                let bl = parseFloat(it.bom?.lebar || 0);

                if (p === 0 && bp > 0) p = bp;
                if (l === 0 && bl > 0) l = bl;

                let area = (p * l * it.qty);
                totalM2 += area;

                let dimensi = (p > 0 && l > 0) ? `${p} x ${l}m` : '-';
                let usage = (p > 0 && l > 0) ? area.toFixed(2) + ' m²' : (it.qty + ' ' + (it.satuan_nama || 'Pcs'));

                let rolls = this.config.standardRolls || [1.1, 1.3, 1.6, 2.2, 2.5, 3.2];
                let maxDim = Math.min(p, l) || 0;
                let roll = maxDim > 0 ? (rolls.find(r => r >= maxDim) || 3.2) : '-';

                let statusBadge = '';
                if (['done', 'on_pb'].includes(it.status_produksi)) {
                    statusBadge = '<span class="badge bg-success rounded-pill px-2"><i class="fas fa-check-double me-1"></i> DONE</span>';
                } else if (['proses', 'finishing'].includes(it.status_produksi)) {
                    statusBadge = '<span class="badge bg-warning text-dark rounded-pill px-2"><i class="fas fa-cog fa-spin me-1"></i> PROSES</span>';
                } else {
                    statusBadge = '<span class="badge bg-secondary rounded-pill px-2">ANTRIAN</span>';
                }

                return `
                    <tr>
                        <td class="ps-3 py-3">
                            <div class="fw-bold text-dark" style="font-size: 0.9rem;">${it.product_nama}</div>
                            <div class="small text-muted">${it.qty} ${it.satuan_nama || 'Unit'}</div>
                        </td>
                        <td class="text-center fw-bold text-primary">${dimensi}</td>
                        <td class="text-center font-monospace text-dark">${usage}</td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border rounded-pill px-2 fw-normal">
                                ${roll !== '-' ? 'Roll ' + (roll * 100) + 'cm' : '-'}
                            </span>
                        </td>
                        <td class="pe-3 text-center">
                            ${statusBadge}${['proses', 'finishing'].includes(it.status_produksi) ? this.resultInputs(it.produksi_id, 'spk', it.qty, it.workflow_qty_output ?? 0) : ''}
                        </td>
                    </tr>
                `;
            }).join('');

            if (totalM2 > 0) {
                html += `
                    <tr class="bg-light fw-900 border-top">
                        <td colspan="2" class="ps-3 py-2 text-uppercase small">Total Estimasi Luas Cetakan</td>
                        <td class="text-center text-success h6 mb-0">${totalM2.toFixed(2)} m²</td>
                        <td colspan="2"></td>
                    </tr>
                `;
            }

            $('#listProdukSelesai').html(html);
        }, 'json').fail((xhr, status, error) => {
            console.error("AJAX Error:", status, error);
            $('#loadingSelesai').addClass('d-none');
            alert("Gagal memuat data. Server Error: " + (xhr.responseJSON?.message || error || "Unknown Error"));
        });
    },

    submitSelesaiSPK() {
        const modal = $('#modalSelesaiSPK');
        const nospk = modal.data('nospk');
        const cat = modal.data('cat');
        const pin = $('#selesaiSpkPin').val();
        const btnSubmit = $('#btnSubmitSelesaiSpk'); // The submit button id from v_modals_produksi.php

        if (!pin || pin.length < 4) {
            return Swal.fire('Error', 'Masukkan PIN Operator untuk verifikasi!', 'error');
        }

        // Disable button to prevent double-click race condition
        btnSubmit.prop('disabled', true);

        Swal.fire({
            title: 'Memproses...',
            didOpen: () => { Swal.showLoading() },
            allowOutsideClick: false
        });

        $.post(this.config.baseUrl + 'selesai_spk_ajax', this.getAuthData({
            nospk: nospk,
            cat: cat,
            pin_operator: pin,
            ids, outcomes
        }), (res) => {
            if (res.success) {
                Swal.fire('Berhasil', res.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('Gagal', res.message, 'error');
                btnSubmit.prop('disabled', false); // Re-enable if failed
            }
        }, 'json').fail(() => {
            Swal.fire('Error', 'Koneksi Server Gagal', 'error');
            btnSubmit.prop('disabled', false); // Re-enable if failed
        });
    },

    /** Batch Operation */
    batchSendToPB(catId) {
        Swal.fire({
            title: 'Kirim Batch ke PB?',
            html: 'Kirim semua SPK A3 yang sudah <b>100% Selesai</b> ke Pengambilan Barang?<br><small class="text-muted">Aksi ini memerlukan PIN Anda.</small>',
            icon: 'question',
            input: 'password',
            inputPlaceholder: 'PIN Produksi',
            inputAttributes: {
                autocapitalize: 'off',
                autocorrect: 'off',
                style: 'text-align:center; letter-spacing: 5px; font-size: 24px;'
            },
            showCancelButton: true,
            confirmButtonText: 'Ya, Proses Batch',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#0ea5e9',
            showLoaderOnConfirm: true,
            backdrop: `rgba(0,0,123,0.4)`,
            preConfirm: (pin) => {
                if (!pin) {
                    Swal.showValidationMessage('PIN Wajib diisi');
                    return false;
                }
                return $.ajax({
                    url: this.config.baseUrl + 'process_all_ready_to_pb_ajax',
                    type: 'POST',
                    dataType: 'json',
                    data: this.getAuthData({
                        cat_id: catId,
                        pin_operator: pin
                    })
                })
                    .then(response => {
                        if (!response.success) {
                            throw new Error(response.message);
                        }
                        return response;
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Request failed: ${error.message || error.statusText || 'Unknown Error'}`
                        );
                    });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: result.value.message,
                    icon: 'success'
                }).then(() => location.reload());
            }
        });
    },

    /** Batch Operations */
    filterSedangProses() {
        // Reset tab buttons
        $('#categoryTabs .nav-link').removeClass('active');

        // Ensure SPK view is active (because PROSES is grouped by SPK)
        $('#container-view-spk').removeClass('d-none');
        $('#container-view-item').addClass('d-none');

        // Use DataTables Search API
        const table = $('#tableSemuaProduksi').DataTable();
        table.search('#PROSES#').draw();

        // Scroll to table with a slight offset
        $('html, body').animate({
            scrollTop: $("#tableSemuaProduksi").offset().top - 150
        }, 500);

        // Success Toast
        Swal.fire({
            title: 'Filter Aktif',
            text: 'Menampilkan SPK yang sedang dalam proses Cetak / Ripping',
            icon: 'info',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    },

        updateNestingSummary() {
    let checked = $('.check-item-nesting:checked');
    let totalArea = 0, maxShorter = 0, totalQty = 0;

    // Defensively get active category
    let activeCat = '';
    let activeCatEl = $('#categoryTabs .nav-link.active');
    if (activeCatEl.length && activeCatEl.attr('id')) {
        activeCat = activeCatEl.attr('id').replace('btn-cat-', '');
    }

    // Fallback for views where tabs might be missing (Control Center, etc)
    if (!activeCat && checked.length > 0) {
        activeCat = ($(checked[0]).data('kategori-id') || '').toString();
    }

    checked.each(function () {
        let p = parseFloat($(this).data('panjang') || 0);
        let l = parseFloat($(this).data('lebar') || 0);

        // PRIORITY: If item has fixed dimensions in BOM (e.g. 60x160 for X-Banner), 
        // use those for area calculation if production record is empty.
        if ((p === 0 || l === 0)) {
            let bp = parseFloat($(this).data('bom-p') || 0);
            let bl = parseFloat($(this).data('bom-l') || 0);
            if (bp > 0 && bl > 0) {
                p = bp;
                l = bl;
            }
        }

        let qty = parseFloat($(this).data('qty') || 0);

        if (p > 0 && l > 0) {
            p += ProductionApp.config.tolerance;
            l += ProductionApp.config.tolerance;
            totalArea += (p * l * qty);
            maxShorter = Math.max(maxShorter, Math.min(p, l || 999));
        } else {
            // FALLBACK: If it's a 1D material (like Lanyard) and we have BOM quantity, use it directly as meter lari
            let bomQty = parseFloat($(this).data('bom-qty') || 1);
            // We assume totalArea acts as the linear meter accumulator here if 1D
            totalArea += (bomQty * qty);
        }

        totalQty += qty;
    });

    if (checked.length > 0) {
        $('#nestingSummary').removeClass('d-none').addClass('animate__slideInUp');
        $('#nestCount').text(checked.length);
        $('#nestTotalArea').text(totalArea.toFixed(2));

        if (activeCat === '1') {
            // Digital A3+ specific labels
            $('.nesting-stat:eq(1)').addClass('d-none'); // Hide Area
            $('.nesting-stat:eq(2) .small').text('Total Cetak');
            $('#nestWidthSuggest').html(`<span class="badge bg-primary text-white px-3 py-1 rounded-pill">${totalQty} Lembar</span>`);
            $('.nesting-stat:eq(3)').addClass('d-none'); // Hide Linear Meter
            $('#visualizeNestingBtn').addClass('d-none');
        } else {
            // Standard (LF, etc)
            $('.nesting-stat:eq(1)').removeClass('d-none'); // Show Area
            $('.nesting-stat:eq(2) .small').text('Saran Lebar Bahan');

            // Safety check for standardRolls config
            let rolls = this.config.standardRolls || [1.1, 1.3, 1.6, 2.2, 2.5, 3.2];
            let roll = rolls.find(s => s >= maxShorter) || 3.2;

            $('#nestWidthSuggest').html(`<span class="badge bg-success text-white px-3 py-1 rounded-pill">Roll ${(roll * 100).toFixed(0)}cm</span>`);
            $('.nesting-stat:eq(3)').removeClass('d-none'); // Show Linear Meter

            // If maxShorter is 0, it means we used 1D BOM quantity directly, so totalArea is already the linear meter!
            if (maxShorter === 0) {
                $('#nestLinear').text(parseFloat(totalArea.toFixed(2)));
            } else {
                let totalLinear = 0;
                checked.each((i, el) => {
                    let ep = parseFloat($(el).data('panjang') || 0);
                    let el_l = parseFloat($(el).data('lebar') || 0);
                    if (ep === 0 || el_l === 0) {
                        let bp = parseFloat($(el).data('bom-p') || 0);
                        let bl = parseFloat($(el).data('bom-l') || 0);
                        if (bp > 0 && bl > 0) { ep = bp; el_l = bl; }
                    }
                    let eqty = parseFloat($(el).data('qty') || 0);
                    if (ep > 0 && el_l > 0) {
                        ep += ProductionApp.config.tolerance;
                        el_l += ProductionApp.config.tolerance;
                        totalLinear += ProductionApp.estimateLinearMeter(ep, el_l, eqty, roll);
                    }
                });
                $('#nestLinear').text(parseFloat(totalLinear.toFixed(2)));
            }

            $('#visualizeNestingBtn').removeClass('d-none');
        }

        let allProses = checked.toArray().every(el => String($(el).data('step-status')) === '1');
        let allFinishing = checked.toArray().every(el => this.isFinishingRow(el));
        let allDone = checked.toArray().every(el => ['done', 'on_pb', 'on_costumer'].includes($(el).data('status')));

        if (allFinishing) {
            $('#btnRipping, #btnSelesaiStok').addClass('d-none');
            $('#btnSelesaiFinishing').toggleClass('d-none', allDone);
        } else if (activeCat === '1') {
            // Queue items must start their current operation before result posting.
            $('#btnRipping').addClass('d-none');
            $('#btnSelesaiStok').toggleClass('d-none', !allProses || allDone || allFinishing);
            $('#btnSelesaiFinishing').toggleClass('d-none', !(allProses && allFinishing));
        } else {
            // Standard (LF, etc)
            $('#btnRipping').toggleClass('d-none', allProses || allDone);
            $('#btnSelesaiStok').toggleClass('d-none', !(allProses && !allFinishing));
            $('#btnSelesaiFinishing').toggleClass('d-none', !(allProses && allFinishing));
        }

    } else {
        $('#nestingSummary').addClass('d-none');
    }
    this.enforceConsistency();
},

toggleAllNesting(checked, targetElement) {
    let table = $(targetElement).closest('table');
    let available = table.find('.check-item-nesting:visible:not(:disabled)');
    if (checked && available.length > 0) {
        let firstMat = $(available[0]).closest('.tr-nesting-item').data('material');
        available.each(function () {
            if ($(this).closest('.tr-nesting-item').data('material') === firstMat) $(this).prop('checked', true);
        });
    } else {
        table.find('.check-item-nesting').prop('checked', false);
    }
    this.updateNestingSummary();
},

enforceConsistency() {
    let checked = $('.check-item-nesting:checked');
    if (checked.length > 0) {
        let baseMat = $(checked[0]).closest('.tr-nesting-item').data('material');
        $('.check-item-nesting').each(function () {
            let mat = $(this).closest('.tr-nesting-item').data('material');
            let isDone = ['done', 'on_pb', 'on_costumer'].includes($(this).data('status'));
            $(this).prop('disabled', mat !== baseMat || isDone);
            $(this).closest('tr').css('opacity', (mat !== baseMat) ? '0.3' : '1');
        });
    } else {
        $('.check-item-nesting').each(function () { $(this).prop('disabled', ['done', 'on_pb', 'on_costumer'].includes($(this).data('status'))); });
        $('.tr-nesting-item').css('opacity', '1');
    }
},

/** Batch Operations */
batchProcess() {
    let ids = $('.check-item-nesting:checked').map((i, el) => $(el).data('id')).get();
    if (!ids.length) return;

    // Hide Bootstrap modal to prevent SweetAlert focus trap (PIN not typing)
    $('#modalVisualizeNesting').modal('hide');

    // Determine category ID from the first selected item
    let catId = $('.check-item-nesting:checked').first().data('kategori-id') || 2;
    let stepName = 'Cetak / Produksi'; // Assume ripping is for printing

    $.get(this.machineOptionsUrl(stepName, catId, ids), (res) => {
        let data = typeof res === 'string' ? JSON.parse(res) : res;
        let m = data.machines || [];
        if (data.message || (!m.length && data.is_mandatory)) {
            Swal.fire('Pilihan mesin', data.message || 'Tidak ada mesin aktif yang sesuai lokasi item terpilih. Periksa lokasi produksi dan master mesin; pisahkan batch jika lokasinya berbeda.', 'warning');
            return;
        }
        let isMandatory = data.is_mandatory || false;
        let userMesinIds = data.user_mesin_ids || [];

        let machineOptions = isMandatory ? '<option value="">-- Pilih Mesin Wajib --</option><option value="0">-- Tanpa Mesin (Manual) --</option>' : '<option value="0">-- Tanpa Mesin (Manual) --</option>';
        let firstMatchId = null;
        if (m && Array.isArray(m)) {
            m.forEach((mac, index) => {
                let isSelected = '';
                if (!firstMatchId && (userMesinIds.includes(mac.mesin_id.toString()) || index === 0)) {
                    isSelected = 'selected';
                    firstMatchId = mac.mesin_id;
                }
                machineOptions += `<option value="${mac.mesin_id}" ${isSelected}>${mac.mesin_nama} (${mac.kategori})</option>`;
            });
        }

        let mandatoryBadge = isMandatory ? '<span class="badge bg-danger ms-2">Wajib</span>' : '<span class="badge bg-secondary ms-2">Opsional</span>';

        Swal.fire({
            title: 'Konfirmasi Ripping',
            html: `
                    <div class="text-center">
                        <p class="text-muted mb-4 px-3" style="font-size: 0.85rem;">Pilih mesin untuk memproses <b>${ids.length} item</b> ini ke tahap Cetak.</p>
                        
                        <div class="mb-3 text-start px-4">
                            <label class="form-label fw-bold small text-dark mb-1 d-flex align-items-center">
                                <i class="fas fa-print me-1 text-primary"></i> Pilih Mesin Produksi ${mandatoryBadge}
                            </label>
                            <select id="swal-mesin-id" class="form-select form-select-lg fw-bold" style="border-radius: 12px; font-size: 0.9rem;">
                                ${machineOptions}
                            </select>
                        </div>
                    </div>
                `,
            didOpen: () => {
                const mesinSelect = Swal.getPopup().querySelector('#swal-mesin-id');
                if (mesinSelect) {
                    mesinSelect.focus();
                }
            },
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Mulai Ripping!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#0d6efd',
            customClass: {
                popup: 'rounded-5 shadow-lg border-0 p-3',
                confirmButton: 'btn btn-primary rounded-pill px-4 py-2 fw-bold mx-2',
                cancelButton: 'btn btn-light rounded-pill px-4 py-2 fw-bold mx-2'
            },
            preConfirm: () => {
                const mesin_id = Swal.getPopup().querySelector('#swal-mesin-id').value;

                if (isMandatory && !mesin_id) {
                    Swal.showValidationMessage('Anda Wajib memilih Mesin Produksi untuk tahap ini!');
                    return false;
                }

                return { mesin_id };
            }
        }).then(r => {
            if (r.isConfirmed) {
                // Save roll preference for these IDs in localStorage
                let visualSel = $('#visualRollSuggestion');
                if (visualSel.length && visualSel.val()) {
                    ids.forEach(id => localStorage.setItem('roll_pref_' + id, visualSel.val()));
                }

                let payload = {
                    ids: ids,
                    status: 'proses',
                    pin_operator: '',
                    mesin_id: r.value.mesin_id
                };
                $.post(this.config.baseUrl + 'batch_status_produksi', this.getAuthData(payload), (res) => {
                    if (res && res.success) {
                        location.reload();
                    } else {
                        Swal.fire('Error', (res ? res.message : 'Gagal update status'), 'error');
                    }
                }, 'json').fail(() => {
                    Swal.fire('Error', 'Server Error!', 'error');
                });
            }
        });
    });
},

moveToPB(id) {
    Swal.fire({ title: 'Kirim ke PB?', text: 'Item dikirim ke Hub Pengambilan.', icon: 'question', showCancelButton: true }).then(r => {
        if (r.isConfirmed) $.post(this.config.baseUrl + 'move_to_pb', this.getAuthData({ produksi_id: id }), (res) => res.success ? location.reload() : Swal.fire('Error', res.message, 'error'), 'json');
    });
},

batchMoveToPB() {
    let ids = $('.check-item-nesting:checked').map((i, el) => $(el).data('id')).get();
    if (!ids.length) return;
    Swal.fire({ title: 'Kirim Batch ke PB?', text: `Kirim ${ids.length} item ke PB?`, icon: 'question', showCancelButton: true }).then(r => {
        if (r.isConfirmed) $.post(this.config.baseUrl + 'batch_move_to_pb', this.getAuthData({ ids }), (res) => res.success ? location.reload() : Swal.fire('Error', 'Gagal', 'error'), 'json');
    });
},

selectAllByMaterial(mat) {
    $('.tr-nesting-item').each(function () {
        if ($(this).data('material') === mat) {
            let cb = $(this).find('.check-item-nesting');
            if (!cb.prop('disabled')) cb.prop('checked', true);
        }
    });
    this.updateNestingSummary();
},

/** Completion & Logic */
openCompletionModal() {
    if (window.WipPinQr) window.WipPinQr.reset();
    else $('#compOperatorPin').val('');
    let checked = $('.check-item-nesting:checked');
    this.state.completionIds = checked.map((i, el) => $(el).data('id')).get();
    this.captureOperations(this.state.completionIds);
    let activeCat = String(checked.first().data('kategori-id') || '0');

    let pid = checked.first().data('id');
    let savedRoll = localStorage.getItem('roll_pref_' + pid);
    
    let finalRecRollText = $('#nestWidthSuggest').text();
    let visualSel = $('#visualRollSuggestion');
    if (visualSel.length && visualSel.val()) {
        finalRecRollText = 'Roll ' + (parseFloat(visualSel.val()) * 100).toFixed(0) + 'cm';
    } else if (savedRoll) {
        finalRecRollText = 'Roll ' + (parseFloat(savedRoll) * 100).toFixed(0) + 'cm';
    }
    $('#compRecRoll').text(finalRecRollText);

    let est = $('#nestLinear').text();
    if (activeCat === '2' || activeCat === '5' || activeCat === '9') {
        let rollWidthStr = finalRecRollText.replace('Roll', '').replace('cm', '').trim();
        let finalRollWidth = parseFloat(rollWidthStr) / 100 || 1.1;
        let totalLinearUsage = 0;
        let useCalculated = false;
        checked.each(function () {
            let ep = parseFloat($(this).data('panjang') || 0);
            let el_l = parseFloat($(this).data('lebar') || 0);
            if (ep === 0 || el_l === 0) {
                let bp = parseFloat($(this).data('bom-p') || 0);
                let bl = parseFloat($(this).data('bom-l') || 0);
                if (bp > 0 && bl > 0) { ep = bp; el_l = bl; }
            }
            let eqty = Math.max(0, (parseFloat($(this).data('qty')) || 0) - (parseFloat($(this).data('step-output')) || 0));
            if (ep > 0 && el_l > 0) {
                totalLinearUsage += ProductionApp.estimateLinearMeter(ep + ProductionApp.config.tolerance, el_l + ProductionApp.config.tolerance, eqty, finalRollWidth);
                useCalculated = true;
            }
        });
        if (useCalculated) {
            est = parseFloat(totalLinearUsage.toFixed(2));
        }
    }

    $('#compJobCount').text(checked.length);
    $('#compTotalUsage').text(est);
    $('#compActualUsage').val(est);

    // Load Machines dynamically based on active category and step name
    let stepName = checked.first().closest('tr').find('.text-primary.fw-900').text().trim() || 'Produksi';
    let satuanBahan = String(checked.first().data('satuan') || 'UNIT').toUpperCase();
    let isFinishingOnly = stepName.toLowerCase().includes('finishing');

    // Calculate total quantity directly from data-qty attribute, multiplying by BOM qty if present
    let totalQty = 0;
    checked.each(function () {
        let itemQty = Math.max(0, (parseFloat($(this).data('qty')) || 0) - (parseFloat($(this).data('step-output')) || 0));
        let bomQty = Number($(this).data('bom-qty') ?? 1);
        if (!Number.isFinite(bomQty) || bomQty < 0) bomQty = 1;
        totalQty += (itemQty * bomQty);
    });

    // Dynamic Display Logic for Large Format, Textile, and DTF vs Others
    if (isFinishingOnly) {
        $('.lf-only-stat').addClass('d-none');
        $('#actualUsageContainer').addClass('d-none');
        $('#compActualUsage').prop('required', false).val(0);
    } else {
        $('#actualUsageContainer').removeClass('d-none');
        if (activeCat === '2' || activeCat === '5' || activeCat === '9') {
            $('.lf-only-stat').removeClass('d-none');
            $('#labelActualUsage').text('Actual Usage / Berapa ' + satuanBahan.toLowerCase() + ' terpotong?');
            $('#textActualUsage').text(satuanBahan);
            $('#compActualUsage').prop('required', true).val(est);
            // Make readonly if using exact BOM quantity without area calculation, but allow edit if they want to override waste
        } else if (activeCat === '1') {
            $('.lf-only-stat').addClass('d-none');
            $('#labelActualUsage').text('Total Lembar Cetak (Termasuk Rusak / Test Print)');
            $('#textActualUsage').text(satuanBahan !== 'UNIT' ? satuanBahan : 'LEMBAR');
            let sLower = satuanBahan.toLowerCase();
            let roundedTotal = (sLower === 'lbr' || sLower === 'lembar' || sLower === 'pcs' || sLower === 'set') ? Math.ceil(totalQty) : totalQty;
            $('#compActualUsage').prop('required', true).val(roundedTotal > 0 ? parseFloat(roundedTotal.toFixed(2)) : checked.length);
        } else {
            $('.lf-only-stat').addClass('d-none');
            $('#labelActualUsage').text('Pemakaian Bahan Terpilih (Total)');
            $('#textActualUsage').text(satuanBahan);
            let sLower = satuanBahan.toLowerCase();
            let roundedTotal = (sLower === 'lbr' || sLower === 'lembar' || sLower === 'pcs' || sLower === 'set') ? Math.ceil(totalQty) : totalQty;
            $('#compActualUsage').prop('required', true).val(roundedTotal > 0 ? parseFloat(roundedTotal.toFixed(2)) : checked.length);
        }
    }

    $('#compMesinId').empty().prop('disabled', true);
    $.get(this.machineOptionsUrl(stepName, activeCat, this.state.completionIds), (res) => {
        let data = typeof res === 'string' ? JSON.parse(res) : res;
        let m = data.machines || [];
        if (data.message || (!m.length && data.is_mandatory)) {
            Swal.fire('Pilihan mesin', data.message || 'Tidak ada mesin aktif yang sesuai lokasi item terpilih. Periksa lokasi produksi dan master mesin; pisahkan batch jika lokasinya berbeda.', 'warning');
        }
        let isMandatory = data.is_mandatory || false;
        let userMesinIds = data.user_mesin_ids || [];

        let sel = $('#compMesinId').empty().prop('disabled', false);
        if (isMandatory) {
            sel.append('<option value="">-- Pilih Mesin Wajib --</option>');
            sel.append('<option value="0">-- Tanpa Mesin (Manual) --</option>');
            $('#compMesinBadge').show().removeClass('bg-secondary').addClass('bg-danger').text('Wajib');
            $('#compMesinId').prop('required', true);
        } else {
            sel.append('<option value="0">-- Tanpa Mesin (Manual) --</option>');
            $('#compMesinBadge').show().removeClass('bg-danger').addClass('bg-secondary').text('Opsional');
            $('#compMesinId').prop('required', false);
        }

        let firstMatchId = null;
        if (m && Array.isArray(m)) {
            m.forEach((mac, index) => {
                let isSelected = '';
                if (!firstMatchId && (userMesinIds.includes(mac.mesin_id.toString()) || index === 0)) {
                    isSelected = 'selected';
                    firstMatchId = mac.mesin_id;
                }
                sel.append(`<option value="${mac.mesin_id}" ${isSelected}>${mac.mesin_nama} (${mac.kategori})</option>`);
            });
        }
    });

    let html = checked.toArray().map(el => {
        let pid = $(el).data('id'), tr = $(el).closest('tr');

        // Calculate consumption data
        let p = parseFloat($(el).data('panjang')) || 0;
        let l = parseFloat($(el).data('lebar')) || 0;
        let qty = parseFloat($(el).data('qty')) || 0;
        let bomQty = parseFloat($(el).data('bom-qty')) || 1;
        let totalItemQty = parseFloat((qty * bomQty).toFixed(2));
        let satuan = $(el).data('satuan') || 'UNIT';
        let stepNameStr = $(el).data('step-name') || 'PRODUKSI';
        let konsumsiHtml = '';

        let meterLari = 0;
        if (activeCat === '2' || activeCat === '5' || activeCat === '9') {
            let rollWidthStr = finalRecRollText.replace('Roll', '').replace('cm', '').trim();
            let rollWidth = parseFloat(rollWidthStr) / 100 || 1.1;
            meterLari = (p > 0 && l > 0) ? ProductionApp.estimateLinearMeter(p + ProductionApp.config.tolerance, l + ProductionApp.config.tolerance, qty, rollWidth).toFixed(2) : (p * totalItemQty).toFixed(2);
            konsumsiHtml = `<div class="small text-muted mt-1"><i class="fas fa-ruler-combined text-info me-1"></i> Estimasi Panjang Ditarik: <b>${meterLari} M</b> <span style="font-size: 0.7rem;">(P: ${p}m × L: ${l}m × ${qty} Qty)</span></div>`;
        } else if (activeCat === '1') {
            konsumsiHtml = `<div class="small text-muted mt-1"><i class="fas fa-copy text-info me-1"></i> Estimasi Terpakai: <b>${totalItemQty} ${satuan}</b> <span style="font-size: 0.7rem;">(A3+)</span></div>`;
        } else {
            konsumsiHtml = `<div class="small text-muted mt-1"><i class="fas fa-box text-info me-1"></i> Estimasi Terpakai: <b>${totalItemQty} ${satuan}</b></div>`;
        }

        let bomLines = $(el).data('bom-lines') || [];
        let currentBomLineId = $(el).data('current-bom-line-id');
        if (currentBomLineId) {
            let filtered = bomLines.filter(b => b.bom_line_id == currentBomLineId);
            if(filtered.length > 0) bomLines = filtered;
        }
        let bomHtml = '';
        if (bomLines.length > 0) {
            let bomInputs = bomLines.map(bom => {
                let bq = parseFloat(bom.quantity ?? bom.bom_qty ?? 1);
                if (!Number.isFinite(bq) || bq < 0) bq = 0;
                let bomReq = (bq * qty);
                let sn = (bom.satuan_nama || '').toLowerCase();

                if (!bom.is_dynamic && (activeCat === '2' || activeCat === '5' || activeCat === '9')) {
                    if (sn === 'm' || sn === 'meter') {
                        bomReq = meterLari * bq;
                    } else if (sn === 'm2' || sn === 'm²' || sn.includes('persegi')) {
                        bomReq = (p * l * qty) * bq;
                    } else if ((bom.bahan_nama || '').toLowerCase().includes('mata ayam')) {
                        bomReq = meterLari * bq; // Heuristic: Mata ayam scales with meter lari
                    } else {
                        bomReq = bq * qty;
                    }
                } else {
                    bomReq = bq * qty;
                }

                if (sn === 'lbr' || sn === 'lembar' || sn === 'pcs' || sn === 'set') {
                    bomReq = Math.ceil(bomReq);
                }

                return `
                        <div class="col-6 mb-2">
                            <label class="form-label mb-1 text-truncate" style="font-size: 0.65rem; font-weight: 700; color: #555; max-width: 100%;" title="${bom.bahan_nama}">${bom.bahan_nama} (${bom.satuan_nama})</label>
                            <input type="number" step="0.01" class="form-control form-control-sm border-primary border-opacity-25 bom-input-${pid}" 
                                data-bom-id="${bom.bom_material_id}" data-bahan-id="${bom.bahan_id}" value="${parseFloat(bomReq).toFixed(2)}">
                        </div>`;
            }).join('');
            bomHtml = `
                    <div class="mt-3 p-2 bg-light border border-secondary border-opacity-25 rounded-3">
                        <div class="text-muted fw-bold mb-2" style="font-size: 0.65rem; text-transform: uppercase;"><i class="fas fa-boxes me-1"></i> Pemakaian Aktual BOM</div>
                        <div class="row g-2">
                            ${bomInputs}
                        </div>
                    </div>`;
        }

        return `
                <div class="p-3 border-bottom outcome-row bg-white" data-id="${pid}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="fw-bold text-dark" style="font-size: 0.85rem;">${$(el).data('item-title') || tr.find('.fw-800').text()}</span>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 ms-2" style="font-size: 0.65rem;">${stepNameStr}</span>
                            ${konsumsiHtml}
                        </div>
                        <div class="btn-group btn-group-sm">
                            <input type="radio" class="btn-check" name="o_${pid}" id="s_${pid}" value="success" checked onchange="setJobOutcome(${pid}, 'success')">
                            <label class="btn btn-outline-success px-3 fw-bold" for="s_${pid}">OK</label>
                            <input type="radio" class="btn-check" name="o_${pid}" id="f_${pid}" value="fail" onchange="setJobOutcome(${pid}, 'fail')">
                            <label class="btn btn-outline-danger px-3 fw-bold" for="f_${pid}">NG</label>
                        </div>
                    </div>
                    ${bomHtml}
                    ${this.resultInputs(pid, 'comp', qty)}
                    <div id="reason_section_${pid}" style="display:none;" class="animate__animated animate__fadeIn">
                        <textarea class="form-control form-control-sm mt-2 border-danger border-opacity-25" id="reason_${pid}" placeholder="Jelaskan alasan rijek & langkah selanjutnya (misal: Reprint)..." rows="2"></textarea>
                        <div class="form-text text-danger" style="font-size: 0.7rem;"><i class="fas fa-redo me-1"></i> Hasil dan bahan tetap dicatat; tahap tetap terbuka untuk tindak lanjut.</div>
                    </div>
                </div>`;
    }).join('');
    $('#compItemList').html(html);
    $('#compActualUsage').attr('placeholder', 'Estimasi kebutuhan bahan; sesuaikan pemakaian aktual');

    let bahanId = checked.first().data('bahan-id');
    let bomMaterialId = checked.first().data('bom-material-id');
    let recRoll = finalRecRollText.replace('Roll', '').trim().toLowerCase();
    let isJasaProduksi = String(checked.first().data('tipe-id')) !== '1';

    if (isJasaProduksi || isFinishingOnly) {
        $('#bomWarningMessage').addClass('d-none');
        $('#sectionSearchBahan').hide();
        $('#labelPilihStokGudang').text(isFinishingOnly ? 'Proses Finishing (Tanpa Potong Stok Utama)' : 'Barang Jasa (Tanpa Stok Bahan)');
        $('#compMaterialId').empty().append('<option value="none">-- Tidak Memerlukan Stok --</option>');
        $('#compMaterialId').prop('required', false);
    } else if (bahanId && bahanId > 0) {
        $('#bomWarningMessage').addClass('d-none');
        $('#sectionSearchBahan').hide();
        $('#labelPilihStokGudang').text('Pilih Stok Bahan (Gudang)');
        $('#compMaterialId').prop('required', true);
        this.loadVariants(bahanId, recRoll, bomMaterialId);
    } else {
        $('#bomWarningMessage').removeClass('d-none');
        $('#sectionSearchBahan').show();
        $('#labelPilihStokGudang').text('Stok Manual Dipilih');
        $('#compMaterialId').empty().append('<option value="">-- Cari dan Pilih Bahan di Atas Terlebih Dahulu --</option>');
        $('#compMaterialId').prop('required', true);
    }

    $('#modalNestingCompletion').modal('show');
},

loadVariants(bahanId, recRoll = '', bomMaterialId = null) {
    $.get(this.config.baseUrl + 'ajax_get_material_variants', { bahan_id: bahanId }, (res) => {
        let sel = $('#compMaterialId').empty().append('<option value="">-- Pilih Stok --</option>');
        let variants = res || [];

        if (recRoll && recRoll.includes('cm')) {
            // Remove 'cm' to match numbers directly (e.g. "130")
            let searchVal = recRoll.replace('cm', '').trim();
            variants.sort((a, b) => {
                let aMatch = a.varian.toLowerCase().includes(searchVal);
                let bMatch = b.varian.toLowerCase().includes(searchVal);
                if (aMatch && !bMatch) return -1;
                if (!aMatch && bMatch) return 1;
                return 0;
            });
        }

        variants.forEach(m => {
            let sisaGudang = parseFloat(m.stok_aktual_gudang).toFixed(2) + ' ' + (m.satuan_gudang || '');
            let sisaBase = parseFloat(m.stok_aktual_base).toFixed(2) + ' ' + (m.satuan_produksi || '');

            let isRec = '';
            let isRecommended = false;
            
            if (recRoll && recRoll.includes('cm')) {
                let searchVal = recRoll.replace('cm', '').trim();
                if (m.varian.toLowerCase().includes(searchVal)) {
                    isRecommended = true;
                }
            }
            
            if (isRecommended) {
                isRec = ' [⭐ REKOMENDASI]';
            } else if (bomMaterialId && m.material_id == bomMaterialId) {
                isRec = ' [🎯 SESUAI BOM]';
            }
            sel.append(`<option value="${m.material_id}" data-stok-gudang="${sisaGudang}" data-stok-base="${sisaBase}">${m.varian}${isRec} (Sisa: ${sisaGudang} / ${sisaBase})</option>`);
        });

        // Auto select first match based on recommendation
        let searchVal = (recRoll && recRoll.includes('cm')) ? recRoll.replace('cm', '').trim() : null;
        let isRecFound = false;
        
        if (searchVal && variants.length > 0 && variants[0].varian.toLowerCase().includes(searchVal)) {
            sel.val(variants[0].material_id);
            isRecFound = true;
        }
        
        if (!isRecFound && bomMaterialId && variants.some(v => v.material_id == bomMaterialId)) {
            sel.val(bomMaterialId);
        } else if (!isRecFound && variants.length === 1) {
            // If there is only 1 variant in the whole list, just auto-select it
            sel.val(variants[0].material_id);
        }
        
        sel.off('change').on('change', function() {
            let opt = $(this).find('option:selected');
            if (opt.val()) {
                let materialId = opt.val();
                let sg = opt.data('stok-gudang') || '0';
                let sb = opt.data('stok-base') || '0';
                $('#helpPilihStokGudang').html(`<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 py-1 px-2"><i class="fas fa-boxes me-1"></i> Stok Gudang: <b>${sg}</b></span> <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 py-1 px-2 ms-1"><i class="fas fa-layer-group me-1"></i> Base: <b>${sb}</b></span>`);
                
                // [FIX] Auto-adjust Actual Usage label and value if the selected material is in the BOM
                if (activeCat !== '2' && activeCat !== '5' && activeCat !== '9' && activeCat !== '1') {
                    let unitStr = sb.replace(/[0-9.-]/g, '').trim().toUpperCase();
                    if (unitStr) {
                        $('#labelActualUsage').text('Pemakaian Bahan Terpilih (Total)');
                        $('#textActualUsage').text(unitStr);
                    }
                    
                    let bomSum = 0;
                    let foundInBom = false;
                    $(`input[data-bom-id="${materialId}"]`).each(function() {
                        bomSum += parseFloat($(this).val()) || 0;
                        foundInBom = true;
                    });
                    
                    if (foundInBom) {
                        $('#compActualUsage').val(parseFloat(bomSum.toFixed(2)));
                    }
                }
            } else {
                $('#helpPilihStokGudang').text('Pilih fisik stok gudang.');
            }
        });
        
        sel.trigger('change');
    }, 'json');
},

submitCompletion(sendToPb = false) {
    let mesinId = $('#compMesinId').val();
    let isMandatory = $('#compMesinBadge').hasClass('bg-danger');
    if (isMandatory && !mesinId) {
        return Swal.fire('Error', 'Anda Wajib memilih Mesin Produksi!', 'error');
    }

    let data = {
        ids: this.state.completionIds || [],
        outcomes: {},
        material_id: $('#compMaterialId').val(),
        pin_operator: $('#compOperatorPin').val(),
        total_usage: $('#compActualUsage').val(),
        mesin_id: mesinId
    };
    const actual = this.resultValues(data.ids, 'comp');
    if (!data.ids.length || !actual) return;
    data.ids.forEach(id => {
        let bom_usage = [];
        $(`.bom-input-${id}`).each(function () {
            bom_usage.push({
                bahan_id: $(this).data('bahan-id'),
                bom_material_id: $(this).data('bom-id'),
                qty: parseFloat($(this).val()) || 0
            });
        });
        data.outcomes[id] = {
            status: $(`input[name="o_${id}"]:checked`).val(),
            reason: $(`#reason_${id}`).val() || '',
            ...actual[id],
            bom_usage: bom_usage
        };
    });
    let firstChecked = $('.check-item-nesting:checked').first();
    let isJasaProduksi = String(firstChecked.data('tipe-id')) !== '1';

    if (data.material_id && (data.total_usage === '' || !Number.isFinite(Number(data.total_usage)) || Number(data.total_usage) < 0)) {
        return Swal.fire('Pemakaian belum lengkap', 'Isi jumlah bahan terpakai aktual dalam satuan dasar material.', 'warning');
    }
    if (!/^[0-9]{4,6}$/.test(data.pin_operator || '')) return Swal.fire('Error', 'Masukkan PIN Operator 4–6 digit atau scan QR PIN petugas.', 'error');
    if (!isJasaProduksi && !data.material_id) return Swal.fire('Error', 'Pilih Material', 'error');

    let activeCat = firstChecked.data('kategori-id') || 0;

    let postData = Object.assign({}, data);
    postData.outcomes = JSON.stringify(data.outcomes);

    // Disable submit buttons to prevent double-submission
    let submitBtn = $('#modalNestingCompletion .modal-footer .btn-success');
    let originalText = submitBtn.html() || '<i class="fas fa-check-circle me-1"></i> Simpan';
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Memproses...');

    const performBatchSubmit = (submitData, supervisorPin = '') => {
        let payload = this.getAuthData(submitData);
        payload.supervisor_pin = supervisorPin;
        $.post(this.config.baseUrl + 'batch_selesai_dan_stok', payload, (res) => {
            if (res.success) {
                // Clear roll preferences for completed ids
                submitData.ids.forEach(id => localStorage.removeItem('roll_pref_' + id));
                
                // [NEW] Auto Print TAG for Large Format (Cat 2)
                if (String(activeCat) === '2' || String(activeCat) === '5') {
                    let tagUrl = this.config.baseUrl + 'print_tag_ids?ids=' + submitData.ids.join(',');
                    let iframe = document.getElementById('hiddenPrintFrame');
                    if (!iframe) {
                        iframe = document.createElement('iframe');
                        iframe.id = 'hiddenPrintFrame';
                        iframe.style.display = 'none';
                        document.body.appendChild(iframe);
                    }
                    iframe.src = tagUrl;
                    iframe.onload = function () {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                        location.reload();
                    };
                } else {
                    location.reload();
                }
            } else if (res.require_pin_override) {
                submitBtn.prop('disabled', false).html(originalText);
                Swal.fire({
                    title: 'Otorisasi Dibutuhkan',
                    text: res.message,
                    icon: 'warning',
                    input: 'password',
                    inputPlaceholder: 'Masukkan PIN Supervisor/Otorisasi',
                    showCancelButton: true,
                    confirmButtonText: 'Bypass & Lanjut',
                    cancelButtonText: 'Batal',
                    preConfirm: (pin) => {
                        if (!pin) {
                            Swal.showValidationMessage('PIN wajib diisi!');
                            return false;
                        }
                        return pin;
                    }
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Memproses...');
                        performBatchSubmit(submitData, result.value);
                    }
                });
            } else {
                submitBtn.prop('disabled', false).html(originalText);
                Swal.fire('Error', res.message, 'error');
            }
        }, 'json').fail(() => {
            submitBtn.prop('disabled', false).html(originalText);
            Swal.fire('Error', 'Gagal menghubungi server.', 'error');
        });
    };

    performBatchSubmit(postData);
},
printGroupTag(groupKey) {
    let ids = [];
    $('.tr-nesting-item').filter(function() { return $(this).data('material') == groupKey; }).find('.check-item-nesting').each(function () {
        let id = $(this).data('id');
        if (id) ids.push(id);
    });
    if (ids.length > 0) {
        this.previewTagSPK(ids.join(','));
    } else {
        Swal.fire('Error', 'Tidak ada item ditemukan untuk grup ini', 'error');
    }
},


sendGroupToPB(matKey) {
    let ids = $('.tr-nesting-item').filter(function() { return $(this).data('material') == matKey; }).find('.check-item-nesting').filter(function () {
        return ['done', 'on_pb', 'on_costumer'].includes($(this).data('status'));
    }).map((i, el) => $(el).data('id')).get();

    if (ids.length === 0) return;

    Swal.fire({
        title: 'Kirim ke PB?',
        text: `Yakin ingin mengirim ${ids.length} item ke Pengambilan Barang?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Kirim Sekarang'
    }).then(r => {
        if (r.isConfirmed) {
            $.post(this.config.baseUrl + 'batch_move_to_pb', this.getAuthData({ ids: ids }), (res) => {
                if (res.success) {
                    location.reload();
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            }, 'json');
        }
    });
},

openFinishingModal() {
    let checked = $('.check-item-nesting:checked');
    this.state.finishingIds = checked.map((i, el) => $(el).data('id')).get();
    this.captureOperations(this.state.finishingIds);
    let activeCat = String(checked.first().data('kategori-id') || '0');
    $('#finOperatorPin').val('');
    const machineRequired = checked.toArray().some(el => this.finishingNeedsMachine($(el).data('step-name'), $(el).data('kategori-id'), $(el).data('product-name')));
    this.state.finishingMachineRequired = machineRequired;
    const machineRequest = (this.state.finishingMachineRequest || 0) + 1;
    this.state.finishingMachineRequest = machineRequest;
    $('#sectionFinMesin').prop('hidden', !machineRequired);
    const machineSelect = $('#finMesinId').empty().prop('disabled', true).prop('required', machineRequired);
    machineSelect.append($('<option>').val('').text('-- Pilih Mesin --'));
    if (machineRequired) {
        $.get(this.machineOptionsUrl('Finishing', activeCat, this.state.finishingIds), (res) => {
            if (this.state.finishingMachineRequest !== machineRequest) return;
            const data = typeof res === 'string' ? JSON.parse(res) : res;
            const machines = data.machines || [];
            machineSelect.prop('disabled', false);
            machines.forEach(machine => machineSelect.append($('<option>').val(machine.mesin_id).text(machine.mesin_nama)));
            if (data.message || !machines.length) {
                Swal.fire('Pilihan mesin', data.message || 'Tidak ada mesin finishing aktif pada lokasi item. Periksa master mesin atau pisahkan batch sesuai lokasi.', 'warning');
            }
        }, 'json').fail(() => {
            if (this.state.finishingMachineRequest !== machineRequest) return;
            Swal.fire('Pilihan mesin', 'Daftar mesin belum dapat dimuat. Buka ulang form untuk mencoba lagi.', 'error');
        });
    }

    let html = checked.toArray().map(el => {
        let pid = $(el).data('id'), tr = $(el).closest('tr');

        // Calculate consumption data
        let p = parseFloat($(el).data('panjang')) || 0;
        let l = parseFloat($(el).data('lebar')) || 0;
        let qty = parseFloat($(el).data('qty')) || 0;
        let satuan = $(el).data('satuan') || 'UNIT';
        let satuanProduk = $(el).data('satuan-produk') || satuan;
        let stepNameStr = $(el).data('step-name') || 'FINISHING';
        let konsumsiHtml = '';

        let meterLari = 0;
        if (activeCat === '2' || activeCat === '5' || activeCat === '9') {
            let rollWidthStr = $('#nestWidthSuggest').text().replace('Roll', '').replace('cm', '').trim();
            let rollWidth = parseFloat(rollWidthStr) / 100 || 1.1;
            meterLari = (p > 0 && l > 0) ? ProductionApp.estimateLinearMeter(p + ProductionApp.config.tolerance, l + ProductionApp.config.tolerance, qty, rollWidth).toFixed(2) : (p * qty).toFixed(2);
            konsumsiHtml = `<div class="small text-muted mt-1"><i class="fas fa-ruler-combined text-info me-1"></i> Estimasi Panjang Ditarik: <b>${meterLari} M</b> <span style="font-size: 0.7rem;">(P: ${p}m × L: ${l}m × ${qty} Qty)</span></div>`;
        } else if (activeCat === '1') {
            konsumsiHtml = `<div class="small text-muted mt-1"><i class="fas fa-box text-info me-1"></i> Konsumsi Akhir: <b>${qty} ${satuanProduk}</b></div>`;
        } else {
            konsumsiHtml = `<div class="small text-muted mt-1"><i class="fas fa-box text-info me-1"></i> Konsumsi Akhir: <b>${qty} ${satuanProduk}</b></div>`;
        }

        let bomLines = $(el).data('bom-lines') || [];
        let currentBomLineId = $(el).data('current-bom-line-id');
        if (currentBomLineId) {
            let filtered = bomLines.filter(b => b.bom_line_id == currentBomLineId);
            if(filtered.length > 0) bomLines = filtered;
        }
        let bomHtml = '';
        if (bomLines.length > 0) {
            let bomInputs = bomLines.map(bom => {
                let bq = parseFloat(bom.quantity ?? bom.bom_qty ?? 1);
                if (!Number.isFinite(bq) || bq < 0) bq = 0;
                let bomReq = (bq * qty);
                let sn = (bom.satuan_nama || '').toLowerCase();

                if (!bom.is_dynamic && (activeCat === '2' || activeCat === '5' || activeCat === '9')) {
                    if (sn === 'm' || sn === 'meter') {
                        bomReq = meterLari * bq;
                    } else if (sn === 'm2' || sn === 'm²' || sn.includes('persegi')) {
                        bomReq = (p * l * qty) * bq;
                    } else if ((bom.bahan_nama || '').toLowerCase().includes('mata ayam')) {
                        bomReq = meterLari * bq; // Heuristic: Mata ayam scales with meter lari
                    } else {
                        bomReq = bq * qty;
                    }
                } else {
                    bomReq = bq * qty;
                }

                if (sn === 'lbr' || sn === 'lembar' || sn === 'pcs' || sn === 'set') {
                    bomReq = Math.ceil(bomReq);
                }

                return `
                        <div class="col-6 mb-2">
                            <label class="form-label mb-1 text-truncate" style="font-size: 0.65rem; font-weight: 700; color: #555; max-width: 100%;" title="${bom.bahan_nama}">${bom.bahan_nama} (${bom.satuan_nama})</label>
                            <input type="number" step="0.01" class="form-control form-control-sm border-primary border-opacity-25 bom-input-${pid}" 
                                data-bom-id="${bom.bom_material_id}" data-bahan-id="${bom.bahan_id}" value="${parseFloat(bomReq).toFixed(2)}">
                        </div>`;
            }).join('');
            bomHtml = `
                    <div class="mt-3 p-2 bg-light border border-secondary border-opacity-25 rounded-3">
                        <div class="text-muted fw-bold mb-2" style="font-size: 0.65rem; text-transform: uppercase;"><i class="fas fa-boxes me-1"></i> Pemakaian Aktual BOM</div>
                        <div class="row g-2">
                            ${bomInputs}
                        </div>
                    </div>`;
        }

        return `
                <div class="p-3 border-bottom bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="fw-bold text-dark">${tr.find('.fw-800').text()}</span>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 ms-2" style="font-size: 0.65rem;">${stepNameStr}</span>
                            ${konsumsiHtml}
                        </div>
                        <div class="btn-group btn-group-sm">
                            <input type="radio" class="btn-check" name="fo_${pid}" id="fs_${pid}" value="success" checked onchange="setJobOutcome(${pid}, 'success', 'f')">
                            <label class="btn btn-outline-success px-3 fw-bold" for="fs_${pid}">OK</label>
                            <input type="radio" class="btn-check" name="fo_${pid}" id="ff_${pid}" value="fail" onchange="setJobOutcome(${pid}, 'fail', 'f')">
                            <label class="btn btn-outline-danger px-3 fw-bold" for="ff_${pid}">NG</label>
                        </div>
                    </div>
                    ${bomHtml}
                    ${this.resultInputs(pid, 'fin', qty)}
                    <div id="f_reason_section_${pid}" style="display:none;" class="animate__animated animate__fadeIn">
                        <textarea class="form-control form-control-sm mt-2 border-danger border-opacity-25" id="f_reason_${pid}" placeholder="Alasan rijek Finishing..." rows="2"></textarea>
                        <div class="form-text text-danger small"><i class="fas fa-undo me-1"></i> Riwayat tetap tersimpan; tahap terbuka untuk tindak lanjut.</div>
                    </div>
                </div>`;
    }).join('');
    let qcWarningHtml = '<div class="alert alert-info p-2 small">Catat hasil finishing aktual. Setelah seluruh tahap selesai, barang otomatis masuk PB. Hasil parsial/rusak tetap terbuka.</div>';
    $('#finishingItemList').html(qcWarningHtml + html);
    $('#modalSelesaiFinishing').modal('show');
},

submitFinishing() {
    let ids = this.state.finishingIds || [];
    const actual = this.resultValues(ids, 'fin');
    if (!ids.length || !actual) return;
    let pin = $('#finOperatorPin').val();
    const mesinId = this.state.finishingMachineRequired ? $('#finMesinId').val() : 0;

    if (!/^[0-9]{4,6}$/.test(pin || '')) return Swal.fire('Error', 'Masukkan PIN Operator 4–6 digit.', 'warning');
    if (this.state.finishingMachineRequired && ($('#finMesinId').prop('disabled') || !/^[1-9][0-9]*$/.test(String(mesinId || ''))))
        return Swal.fire('Error', 'Pilih mesin aktual untuk tahap cutting/potong ini.', 'warning');

    let outcomes = {};
    ids.forEach(id => {
        let bom_usage = [];
        $(`.bom-input-${id}`).each(function () {
            bom_usage.push({
                bahan_id: $(this).data('bahan-id'),
                bom_material_id: $(this).data('bom-id'),
                qty: parseFloat($(this).val()) || 0
            });
        });
        outcomes[id] = {
            status: $(`input[name="fo_${id}"]:checked`).val(),
            reason: $(`#f_reason_${id}`).val(),
            bom_usage: bom_usage,
            ...actual[id]
        };
    });
    $.post(this.config.baseUrl + 'batch_selesai_dan_stok', this.getAuthData({
        ids,
        outcomes: JSON.stringify(outcomes),
        pin_operator: pin,
        material_id: null,
        total_usage: 0,
        mesin_id: mesinId
    }), (res) => {
        if (res.success) {
            Swal.fire({
                title: 'Hasil Finishing Disimpan',
                text: res.message,
                icon: 'success',
                timer: 3000,
                showConfirmButton: false
            }).then(() => location.reload());
        } else {
            Swal.fire('Error', res.message, 'error');
        }
    }, 'json');
},

/** Visualization */
visualizeNesting(keepSelection = false) {
    let checked = $('.check-item-nesting:checked');
    if (!checked.length) return;
    $('#modalVisualizeNesting').modal('show');

    let canvas = $('#nestingCanvas').empty(), scale = 70; // Adjusted scale for horizontal layout
    let currentX = 0, currentY = 0, colMaxW = 0, maxDim = 0;
    let totalAreaActual = 0, itemSummary = {};

    // 1. Determine Req Roll Width 
    checked.each(function () {
        let p = parseFloat($(this).data('panjang') || 0) + ProductionApp.config.tolerance, l = parseFloat($(this).data('lebar') || 0) + ProductionApp.config.tolerance;
        maxDim = Math.max(maxDim, Math.min(p, l));  // Minimum dimension must fit in the roll
    });

    // 2. Select Roll Width
    let rollW = 3.2;
    let selectEl = $('#visualRollSuggestion');
    let rolls = ProductionApp.config.standardRolls || [1.1, 1.3, 1.6, 2.2, 2.5, 3.2];
    
    if (keepSelection && selectEl.val()) {
        rollW = parseFloat(selectEl.val());
    } else {
        rollW = rolls.find(s => s >= maxDim) || Math.max(...rolls);
        // Populate select box
        selectEl.empty();
        rolls.forEach(s => {
            let isSelected = (s === rollW) ? 'selected' : '';
            // Allow selecting smaller rolls so operator can still preview the overflow
            let isInvalidClass = (s < maxDim) ? 'text-danger bg-danger bg-opacity-10' : '';
            let invalidText = (s < maxDim) ? ' (Kekecilan)' : '';
            selectEl.append(`<option value="${s}" ${isSelected} class="${isInvalidClass}">ROLL ${(s * 100).toFixed(0)}cm${invalidText}</option>`);
        });
    }

    // 3. Horizontal Nesting Logic (Like real RIP software)
    // Y-Axis = Roll Width (Fixed)
    // X-Axis = Media Length (Infinite)
    checked.each(function () {
        let p = parseFloat($(this).data('panjang') || 0), l = parseFloat($(this).data('lebar') || 0),
            effP = p + ProductionApp.config.tolerance, effL = l + ProductionApp.config.tolerance,
            q = parseFloat($(this).data('qty') || 0),
            name = $(this).closest('tr').find('.fw-800').text();

        totalAreaActual += (p * l * q);
        itemSummary[name] = (itemSummary[name] || 0) + q;

        for (let i = 0; i < q; i++) {
            // Determine orientation (Smart Rotate for best fit)
            // Goal: minimize linear meter (X-axis) by putting longest side across the roll (Y-axis)
            let itemW = Math.min(effP, effL); // Horizontal (Length pulled)
            let itemH = Math.max(effP, effL); // Vertical (Width against the roll)

            // If placing the longest side vertically exceeds roll width (Y-axis), we MUST rotate it
            if (itemH > rollW) {
                itemW = Math.max(effP, effL);
                itemH = Math.min(effP, effL);
            }

            // Can we fit it in the current column?
            if (currentY + itemH > rollW) {
                // No space vertically, start a new column/section to the right
                currentX += colMaxW;
                currentY = 0;
                colMaxW = 0;
            }

            // Place the item
            canvas.append($('<div class="nest-block"></div>').css({
                position: 'absolute',
                left: currentX * scale,
                top: currentY * scale,
                width: itemW * scale,
                height: itemH * scale,
                border: '1.5px solid #2ecc71', background: 'rgba(46, 204, 113, 0.25)', overflow: 'hidden',
                display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', color: '#2ecc71'
            }).html(`
                    <div class="text-white fw-900 text-truncate px-1" style="font-size: 10px; max-width: 100%; text-shadow: 0 1px 2px rgba(0,0,0,0.8);">${name}</div>
                    <div style="background: rgba(0,0,0,0.4); padding: 2px 5px; border-radius: 4px; font-size: 8px;">${itemW.toFixed(2)} x ${itemH.toFixed(2)}</div>
                `));

            // Move down for the next item in this column
            currentY += itemH;
            // Track the widest item in this column to know how far to move right later
            colMaxW = Math.max(colMaxW, itemW);
        }
    });

    // 4. Final Dimensions
    let finalLength = currentX + colMaxW; // This is the total linear meter used (X-axis)
    let totalAreaNesting = rollW * finalLength;
    let efficiency = (totalAreaActual / totalAreaNesting) * 100;

    $('#visualReqWidth').text(maxDim.toFixed(2));
    $('#visualRollHeight').text(finalLength.toFixed(2));
    $('#visualEfficiency').text(efficiency.toFixed(1) + '%');

    // Render Canvas Dimensions
    // Height is fixed (Roll Width), Width grows infinitely
    canvas.css({
        height: rollW * scale,
        width: finalLength * scale + 50,
        position: 'relative',
        background: '#151515',
        borderBottom: '4px solid #444',
        borderTop: '4px solid #444'
    });

    let listHtml = Object.entries(itemSummary).map(([name, qty]) => `
            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-white bg-opacity-5 rounded-3">
                <span class="small fw-bold text-truncate" style="max-width: 150px;">${name}</span>
                <span class="badge bg-info text-dark rounded-pill">${qty} Qty</span>
            </div>
        `).join('');
    $('#visualItemList').html(listHtml);
},

/** Search & Aux */
searchBahan(q) {
    if (q.length < 2) return $('#searchBahanResults').hide();
    $.get(this.config.baseUrl + 'ajax_search_bahan', { q }, (res) => {
        $('#searchBahanResults').empty().show().append((res || []).map(it => `<a href="javascript:void(0)" class="list-group-item list-group-item-action" onclick="selectSearchBahan(${it.bahan_id}, '${it.bahan_nama}')">${it.bahan_nama}</a>`).join(''));
    }, 'json');
},

updateManualDim(el, type) {
    let tr = $(el).closest('tr'),
        cb = tr.find('.check-item-nesting'),
        p = parseFloat(tr.find('.manual-dim-p').val()) || 0,
        l = parseFloat(tr.find('.manual-dim-l').val()) || 0,
        qty = parseFloat(cb.data('qty') || 0),
        id = cb.data('id');

    // Update data cache on checkbox
    cb.data('panjang', p);
    cb.data('lebar', l);

    // Update Area UI immediately with correct precision formatting
    tr.find('.td-area .area-val').text((p * l * qty).toFixed(2));

    // Use debounce for AJAX Persistence so we don't spam the server on every keypress
    clearTimeout(tr.data('dim-timer'));
    tr.data('dim-timer', setTimeout(() => {
        $.post(this.config.baseUrl + 'update_manual_dimension_ajax', this.getAuthData({ produksi_id: id, panjang: p, lebar: l }), res => { if (!res.success) Swal.fire('Gagal', res.message, 'error'); }, 'json');
    }, 800)); // 800ms delay after the operator stops typing

    if (cb.prop('checked')) this.updateNestingSummary();
},

clearNestingSelection() {
    $('.check-item-nesting, .check-all-nesting').prop('checked', false);
    this.updateNestingSummary();
},

finishGroupBatch(matKey) {
    this.clearNestingSelection();
    const app = this;
    $('.tr-nesting-item').filter(function() { return $(this).data('material') == matKey; }).find('.check-item-nesting').each(function () {
        let st = String($(this).data('status')).toLowerCase();
        if (!$(this).prop('disabled') && (st === 'proses' || st === 'finishing' || st === '1'
            || (app.isFinishingRow(this) && ['', '0', 'pending', 'approve', 'approved'].includes(st)))) {
            $(this).prop('checked', true);
        }
    });
    this.updateNestingSummary();
    this.openAppropriateCompletionModal();
},

startGroupBatch(matKey) {
    this.clearNestingSelection();
    let ids = [];
    $('.tr-nesting-item').filter(function() { return $(this).data('material') == matKey; }).find('.check-item-nesting').each(function () {
        let st = String($(this).data('status') ?? '').toLowerCase();
        let stepSt = String($(this).data('step-status') ?? '').toLowerCase();
        if (st === 'approved' || st === 'pending' || st === '0' || st === '' || stepSt === '0' || (st === 'finishing' && stepSt !== '1')) {
            ids.push($(this).data('id'));
        }
    });

    if (!ids.length) {
        Swal.fire('Info', 'Tidak ada item yang dapat dimulai.', 'info');
        return;
    }

    Swal.fire({
        title: 'Mulai Batch Finishing?',
        text: `Anda akan memulai pengerjaan untuk ${ids.length} item secara bersamaan.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Mulai',
        cancelButtonText: 'Batal'
    }).then(r => {
        if (r.isConfirmed) {
            $.post(this.config.baseUrl + 'batch_status_produksi', this.getAuthData({ ids: ids, status: 'proses' }), (res) => {
                if (res.success) {
                    location.reload();
                } else {
                    Swal.fire('Error', res.message || 'Gagal memulai batch', 'error');
                }
            }, 'json');
        }
    });
},

startSPKBatch(matKey) {
    this.clearNestingSelection();
    let ids = [];
    $('.tr-nesting-item').filter(function() { return $(this).data('material') == matKey; }).find('.check-item-nesting').each(function () {
        let st = String($(this).data('status') ?? '').toLowerCase();
        let stepSt = String($(this).data('step-status') ?? '').toLowerCase();
        if (st === 'approved' || st === 'pending' || st === '0' || st === '' || stepSt === '0' || (st === 'finishing' && stepSt !== '1')) {
            ids.push($(this).data('id'));
        }
    });

    if (!ids.length) {
        Swal.fire('Info', 'Tidak ada item dalam SPK ini yang dapat dimulai.', 'info');
        return;
    }

    Swal.fire({
        title: 'Mulai Semua di SPK ini?',
        text: `Anda akan memulai pengerjaan untuk ${ids.length} item secara bersamaan tanpa proses Ripping. Yakin?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Mulai Semua',
        cancelButtonText: 'Batal'
    }).then(r => {
        if (r.isConfirmed) {
            $.post(this.config.baseUrl + 'batch_status_produksi', this.getAuthData({ ids: ids, status: 'proses' }), (res) => {
                if (res.success) {
                    location.reload();
                } else {
                    Swal.fire('Error', res.message || 'Gagal memulai batch', 'error');
                }
            }, 'json');
        }
    });
},

/**
 * Used by Control Center or other SPK-specific views
 * to trigger the unified batch modal for an item's group.
 */
isFinishingRow(el) {
    const flag = $(el).data('is-finishing');
    if (flag !== undefined) return Number(flag) === 1;
    // Compatibility for older control-center rows without the explicit operation flag.
    return (parseInt($(el).data('step-urutan')) || 1) > 1;
},

openAppropriateCompletionModal() {
    let checked = $('.check-item-nesting:checked');
    if (!checked.length) return;
    let allFinishing = checked.toArray().every(el => this.isFinishingRow(el));
    if (allFinishing) {
        this.openFinishingModal();
    } else {
        this.openCompletionModal();
    }
},

finishItemAsBatch(prodId) {
    this.clearNestingSelection();
    let target = $(`.check-item-nesting[data-id="${prodId}"]`);
    if (!target.length) return;

    let mat = target.closest('.tr-nesting-item').data('material');

    // Select items with same material in current view safely
    $('.tr-nesting-item').filter(function() { return $(this).data('material') == mat; }).find('.check-item-nesting').each(function () {
        if ($(this).data('status') === 'proses') {
            $(this).prop('checked', true);
        }
    });

    this.updateNestingSummary();
    this.openAppropriateCompletionModal();
},

finishItemIndividual(prodId) {
    this.clearNestingSelection();
    let target = $(`.check-item-nesting[data-id="${prodId}"]`);
    if (!target.length) return;

    target.prop('checked', true);

    this.updateNestingSummary();
    this.openAppropriateCompletionModal();
},

/**
 * Fast Finish for Individual Items (No Material/Stock Update Modal)
 */
finishItemSimple(id) {
    this.finishItemIndividual(id);
},

setJobOutcome(pid, status, prefix = '') {
    // Ensure prefix handles the underscore correctly if provided
    let p = prefix ? prefix + '_' : '';
    // Use generic selector helper to avoid ID conflicts
    let section = document.getElementById(`${p}reason_section_${pid}`);
    let input = document.getElementById(`${p}reason_${pid}`);

    if (section) {
        if (status === 'fail') {
            $(section).show().removeClass('d-none').addClass('animate__animated animate__fadeIn');
            if (input) input.focus();
        } else {
            $(section).hide().addClass('d-none');
        }
    }
},

getStatusClass(st) {
    return st === 'proses' ? 'bg-warning text-dark' : (['done', 'on_pb'].includes(st) ? 'bg-success' : 'bg-secondary');
},

advanceStep(id, type, stepName = 'Step Ini', estQty = 0, bomMaterialId = 0, prevPakai = 0, unit = 'Unit', catId = 1, isFinishing = 0) {
    isFinishing = Boolean(Number(isFinishing)) || /^(?:f[.\s_-]|finishing\b|lamina(?:si|ting|s)\b|potong\b|cutting\b|jahit\b|packing\b|pon\b|kiss\s*cut\b|die[\s-]*cut\b)/i.test(String(stepName).trim());
    this.captureOperations([id]);
    const productName = this.state.operationSnapshots[id]?.productName || '';
    const finishingMachineRequired = isFinishing && this.finishingNeedsMachine(stepName, catId, productName);
    if (type === 'start') {
        Swal.fire({
            title: 'Mulai Produksi?',
            text: 'Mulai tahap yang ditugaskan? Aktivitas dicatat atas akun yang sedang login.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Mulai!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#0d6efd',
            customClass: {
                popup: 'rounded-5 shadow-lg border-0 p-3',
                confirmButton: 'btn btn-primary rounded-pill px-5 py-2 fw-bold mx-2',
                cancelButton: 'btn btn-light rounded-pill px-5 py-2 fw-bold mx-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                this._postAdvance(id, type, {
                    pin_operator: '',
                    mesin_id: '',
                    lokasi_proses: ''
                }, catId);
            }
        });
    } else if (type === 'cancel') {
        Swal.fire({
            title: 'Batal Produksi?',
            text: 'Apakah Anda yakin ingin membatalkan proses ini dan mengembalikannya ke antrean?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Batal!',
            cancelButtonText: 'Kembali',
            confirmButtonColor: '#dc3545',
            customClass: {
                popup: 'rounded-5 shadow-lg border-0 p-3',
                confirmButton: 'btn btn-danger rounded-pill px-5 py-2 fw-bold mx-2',
                cancelButton: 'btn btn-light rounded-pill px-5 py-2 fw-bold mx-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                this._postAdvance(id, type, {
                    pin_operator: '',
                    mesin_id: '',
                    lokasi_proses: ''
                }, catId);
            }
        });
    } else {
        // Load Machines dynamically for the branch based on step and category for completion
        const showResultDialog = (res) => {
            let data = typeof res === 'string' ? JSON.parse(res) : res;
            let m = data.machines || [];
            let isMandatory = finishingMachineRequired || (!isFinishing && (data.is_mandatory || false));
            if (data.message || (!m.length && isMandatory)) {
                Swal.fire('Pilihan mesin', data.message || 'Tidak ada mesin aktif yang sesuai lokasi item terpilih. Periksa lokasi produksi dan master mesin; pisahkan batch jika lokasinya berbeda.', 'warning');
                return;
            }
            let userMesinIds = data.user_mesin_ids || [];

            let machineOptions = isMandatory ? '<option value="">-- Pilih Mesin Wajib --</option>' : '<option value="">-- Tanpa Mesin / Manual --</option>';
            
            let firstMatchId = null;
            if (m && Array.isArray(m)) {
                m.forEach((mac, index) => {
                    let isSelected = '';
                    if (!firstMatchId && (userMesinIds.includes(mac.mesin_id.toString()) || index === 0)) {
                        isSelected = 'selected';
                        firstMatchId = mac.mesin_id;
                    }
                    machineOptions += `<option value="${mac.mesin_id}" ${isSelected}>${mac.mesin_nama} (${mac.kategori})</option>`;
                });
            }

            let mandatoryBadge = isMandatory ? '<span class="badge bg-danger ms-2">Wajib</span>' : '<span class="badge bg-secondary ms-2">Opsional</span>';

            // Build Material Options
            let materialOptions = '<option value="">-- Pilih Bahan Baku (Opsional) --</option>';
            if (WIP_CONFIG.materials && Array.isArray(WIP_CONFIG.materials)) {
                WIP_CONFIG.materials.forEach(mat => {
                    let stockInfo = mat.stok ? ` (Stok: ${parseFloat(mat.stok)} ${mat.satuan || ''})` : ' (Stok: 0)';
                    let isSelected = (mat.bahan_id == bomMaterialId || mat.raw_bahan_id == bomMaterialId) ? 'selected' : '';
                    materialOptions += `<option value="${mat.bahan_id}" ${isSelected} class="fw-bold text-dark">${mat.bahan_nama}${stockInfo}</option>`;
                });
            }

            // REVISION / REPRINT DETECTION
            let isReprint = (parseFloat(prevPakai) > 0);

            // FLEXIBLE UNIT LABELS
            let isPcsBased = ['pcs', 'unit', 'lembar', 'set'].includes(unit.toLowerCase());

            let modalTitle = isFinishing ? 'Catat Hasil Finishing' : isReprint ? 'Koreksi / Reprint (' + unit + ')' : 'Selesai & Lapor Bahan (' + unit + ')';

            // Dynamic label based on unit type
            let usageLabel, usagePlaceholder, usageHelpText;
            if (isPcsBased) {
                usageLabel = 'Bahan terpakai aktual (' + unit + ')';
                usagePlaceholder = 'Contoh: 5 (untuk 5 ' + unit + ')';
                usageHelpText = isReprint
                    ? 'Isi dengan jumlah cetak ulang saja.'
                    : `Estimasi: ${estQty} ${unit}. Masukkan pemakaian dalam satuan dasar material yang dipilih.`;
            } else {
                usageLabel = isReprint ? 'Bahan Tambahan (' + unit + ')' : 'Bahan Terpakai (' + unit + ')';
                usagePlaceholder = 'Contoh: 2.5 (untuk 2.5 ' + unit + ')';
                usageHelpText = isReprint
                    ? `Isi dengan pemakaian ${unit} tambahan yang digunakan.`
                    : `Estimasi BOM: ${estQty} ${unit} (termasuk waste).`;
            }

            let usageHelp = isReprint ? `<div class="alert alert-danger p-2 small mb-3 border-danger border-opacity-25" style="background:#fff5f5">
                                                 <i class="fas fa-redo-alt me-1"></i> <b>MODE REPRINT:</b> Masukkan hanya jumlah <b>${unit}</b> tambahan yang baru digunakan.
                                              </div>` : '';
            let currentDisplay = isReprint ? `<div class="bg-light p-2 rounded mb-3 border small">
                                                     <div class="text-muted">Total Terpakai Sebelumnya:</div>
                                                     <div class="fw-bold text-dark fs-6 font-monospace">${parseFloat(prevPakai)} ${unit}</div>
                                                   </div>` : '';

            // Check BOM Status for Warning
            let bomWarning = '';
            let pType = WIP_CONFIG.productType || 0;
            if (!bomMaterialId || bomMaterialId == 0) {
                if (pType == 1) { // Barang Produksi
                    bomWarning = `
                            <div class="alert alert-warning d-flex align-items-center mb-3">
                                <i class="fas fa-exclamation-triangle fa-2x me-3 text-warning"></i>
                                <div>
                                    <div class="fw-bold">PERHATIAN: BOM Belum Di-set!</div>
                                    <div class="small">Admin belum input BOM. Harap pilih bahan manual.</div>
                                </div>
                            </div>`;
                }
            }

            // Finish Step: Require Input
            Swal.fire({
                title: modalTitle,
                html: `
                        <div class="text-start">
                            ${isFinishing ? '' : usageHelp}
                            ${isFinishing ? '' : bomWarning}
                            <p class="small text-muted mb-2">Tahap: <b>${stepName}</b></p>
                            
                            ${isFinishing ? '' : `
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-dark"><i class="fas fa-box-open me-1"></i> 1. Bahan Baku Utama</label>
                                <select id="swal-bahan-id" class="form-select bg-light border-0 fw-bold text-dark shadow-sm select2-swal" style="width: 100%;">
                                    ${materialOptions}
                                </select>
                            </div>

                            ${currentDisplay}

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-dark"><i class="fas fa-ruler-combined me-1"></i> 2. ${usageLabel}</label>
                                <input type="number" id="swal-qty-pakai" class="form-control mb-1 fw-bold" placeholder="${usagePlaceholder}" step="0.01" value="${isReprint ? '0' : (estQty > 0 ? estQty : '')}" style="border-radius: 12px;">
                                <div class="form-text small text-primary fst-italic"><i class="fas fa-info-circle"></i> ${usageHelpText}</div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-danger"><i class="fas fa-trash-alt me-1"></i> 3. Tambahan Gagal / Afdruk (Jika Ada)</label>
                                <input type="number" id="swal-qty-rusak" class="form-control border-danger text-danger bg-danger bg-opacity-10 fw-bold" placeholder="0" step="0.01" style="border-radius: 12px;">
                                <div class="form-text small text-danger">Diisi jika ada kesalahan selama proses perbaikan ini.</div>
                            </div>
                            `}

                            ${isFinishing && !finishingMachineRequired ? '' : `
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-dark"><i class="fas fa-print me-1 text-primary"></i> Pilih Mesin ${isFinishing ? 'Cutting / Potong' : 'Produksi'} ${mandatoryBadge}</label>
                                <select id="swal-mesin-id" class="form-select bg-light border-0 fw-bold text-dark shadow-sm select2-swal" style="width: 100%;">
                                    ${machineOptions}
                                </select>
                                <div class="form-text small">Pilih mesin yang Anda gunakan untuk pengerjaan tahap ini.</div>
                            </div>
                            `}

                            ${this.resultInputs(id, 'advance', $(`.check-item-nesting[data-id="${id}"]`).first().data('qty') || '-')}
                            <div class="mt-4 p-3 bg-light rounded-3 border-dashed border-primary">
                                <label class="form-label fw-bold small text-primary text-uppercase"><i class="fas fa-fingerprint me-1"></i> Verifikasi PIN Operator</label>
                                <input type="password" id="swal-pin-next" class="form-control form-control-lg fw-bold text-center" 
                                    maxlength="6" placeholder="PIN 4-6 Digit" 
                                    style="letter-spacing: 0.5rem; font-size: 1.5rem;" inputmode="numeric">
                                <div class="form-text small text-center">Wajib diisi untuk memvalidasi penanggung jawab.</div>
                            </div>
                        </div >
                    `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Simpan & Lanjut',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-4 shadow-lg border-0',
                    confirmButton: 'btn btn-primary rounded-pill px-4',
                    cancelButton: 'btn btn-light rounded-pill px-4'
                },
                preConfirm: () => {
                    const actual = this.resultValues([id], 'advance');
                    if (!actual) return false;
                    const bahan = Swal.getPopup().querySelector('#swal-bahan-id')?.value || '';
                    let pakai = Swal.getPopup().querySelector('#swal-qty-pakai')?.value ?? '0';
                    const rusak = Swal.getPopup().querySelector('#swal-qty-rusak')?.value || '0';
                    const pin = Swal.getPopup().querySelector('#swal-pin-next').value;
                    const mesin_id = isFinishing && !finishingMachineRequired ? '0' : (Swal.getPopup().querySelector('#swal-mesin-id')?.value || '');

                    if (!pin || pin.length < 4) {
                        Swal.showValidationMessage('Harap masukkan PIN (min 4 digit)!');
                        return false;
                    }

                    if (isMandatory && !mesin_id) {
                        Swal.showValidationMessage('Anda Wajib memilih Mesin untuk tahap ini!');
                        return false;
                    }

                    let pType = WIP_CONFIG.productType || 0;

                    // Validation: Required only for Non-Services (Type 1 or Default)
                    if (pType != 2 && (pakai === '' || pakai === null)) {
                        Swal.showValidationMessage('Harap isi jumlah bahan terpakai!');
                        return false;
                    }

                    // Default empty to 0
                    if (pakai === '') pakai = 0;

                    return { pin_operator: pin, qty_pakai: pakai, qty_rusak: rusak || 0, bahan_id: bahan, mesin_id: mesin_id, ...actual[id] };
                },
                didOpen: () => {
                    $('.select2-swal').select2({
                        dropdownParent: Swal.getPopup(),
                        theme: 'bootstrap-5',
                        placeholder: '-- Pilih --',
                        width: '100%'
                    });

                    // Autofocus PIN field & Enter Trigger
                    const pinInput = $('#swal-pin-next');
                    pinInput.focus();
                    pinInput.on('keyup', (e) => {
                        if (e.key === 'Enter') Swal.clickConfirm();
                    });

                    // Set Auto-Value safely
                    if (estQty && estQty > 0 && !isReprint && Swal.getPopup().querySelector('#swal-qty-pakai')) {
                        Swal.getPopup().querySelector('#swal-qty-pakai').value = estQty;
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    this._postAdvance(id, type, result.value, catId);
                }
            });
        };
        if (isFinishing && !finishingMachineRequired) showResultDialog({ machines: [], is_mandatory: false });
        else $.get(this.machineOptionsUrl(stepName, catId, [id]), showResultDialog);
    }
},

_postAdvance(id, type, data, catId = 1, supervisorPin = '') {
    if (this.state.advanceRequests[id]) return;
    this.state.advanceRequests[id] = true;
    // MODULAR ROUTING: If Category 2 (LF), use dedicated LF endpoint. 
    // Otherwise use standard/legacy A3+ endpoint.
    const endpoint = (parseInt(catId) === 2) ? 'advance_step_lf_ajax' : 'advance_step_ajax';

    $.post(this.config.baseUrl + endpoint, this.getAuthData({
        produksi_id: id,
        type: type,
        pin_operator: data.pin_operator,
        qty_pakai: data.qty_pakai,
        qty_rusak: data.qty_rusak,
        qty_output: data.qty_output,
        qty_missing: data.qty_missing,
        bahan_id: data.bahan_id,
        mesin_id: data.mesin_id,
        lokasi_proses: data.lokasi_proses,
        supervisor_pin: supervisorPin
    }), (res) => {
        if (res && res.success) {
            location.reload();
        } else if (res && res.require_pin_override) {
            Swal.fire({
                title: 'Otorisasi Dibutuhkan',
                text: res.message,
                icon: 'warning',
                input: 'password',
                inputPlaceholder: 'Masukkan PIN Supervisor/Otorisasi',
                showCancelButton: true,
                confirmButtonText: 'Bypass & Lanjut',
                cancelButtonText: 'Batal',
                preConfirm: (pin) => {
                    if (!pin) {
                        Swal.showValidationMessage('PIN wajib diisi!');
                        return false;
                    }
                    return pin;
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    this._postAdvance(id, type, data, catId, result.value);
                }
            });
        } else {
            Swal.fire('Error', (res ? res.message : 'Gagal update status'), 'error');
        }
    }, 'json').fail((xhr) => {
        console.error(xhr);
        Swal.fire('Error', 'Server Error: ' + xhr.status, 'error');
    }).always(() => {
        delete this.state.advanceRequests[id];
    });
}
};

/** Global Proxy */
window.ProductionApp = ProductionApp;
window.previewSPK = ProductionApp.previewSPK.bind(ProductionApp);
window.previewTag = ProductionApp.previewTag.bind(ProductionApp);
window.previewTagSPK = ProductionApp.previewTagSPK.bind(ProductionApp);
window.prosesProduksiSPK = ProductionApp.prosesSPK.bind(ProductionApp);
window.selesaiProduksiSPK = ProductionApp.selesaiSPK.bind(ProductionApp);
window.visualizeNesting = ProductionApp.visualizeNesting.bind(ProductionApp);
window.updateManualDim = ProductionApp.updateManualDim.bind(ProductionApp);
window.selectAllByMaterial = ProductionApp.selectAllByMaterial.bind(ProductionApp);
window.batchProcessNesting = ProductionApp.batchProcess.bind(ProductionApp);
window.moveToPB = ProductionApp.moveToPB.bind(ProductionApp);
window.openNestingCompletionModal = ProductionApp.openCompletionModal.bind(ProductionApp);
window.openFinishingCompletionModal = ProductionApp.openFinishingModal.bind(ProductionApp);
window.finishItemSimple = ProductionApp.finishItemSimple.bind(ProductionApp);
window.submitBatchFinishing = ProductionApp.submitFinishing.bind(ProductionApp);
window.submitBatchCompletion = ProductionApp.submitCompletion.bind(ProductionApp);
window.clearNestingSelection = ProductionApp.clearNestingSelection.bind(ProductionApp);
window.setJobOutcome = ProductionApp.setJobOutcome.bind(ProductionApp);
window.filterSedangProses = ProductionApp.filterSedangProses.bind(ProductionApp);
window.selectSearchBahan = (id, name) => { $('#searchBahanInput').val(name); $('#searchBahanResults').hide(); ProductionApp.loadVariants(id); };

$(document).ready(() => ProductionApp.init());

// Token refresh is scoped to WIP requests; global CSRF configuration is not changed.
$(document).ajaxSuccess((event, xhr, settings, response) => {
    if (String(settings.url || '').startsWith(WIP_CONFIG.baseUrl) && response && response.csrf_hash) {
        ProductionApp.state.csrfHash = response.csrf_hash;
        WIP_CONFIG.csrfHash = response.csrf_hash;
    }
});
