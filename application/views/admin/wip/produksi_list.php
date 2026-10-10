<?php if ($this->input->get('iframe') != '1'): ?>
    <?php $this->load->view('layout_admin/header_modul'); ?>
<?php endif; ?>

<link rel="stylesheet" href="<?= base_url('assets/css/wip_produksi.css') ?>">

<!-- Global Configuration for External JS -->
<script>
    const WIP_CONFIG = {
        baseUrl: '<?= site_url('wip/') ?>',
        siteUrl: '<?= site_url() ?>',
        csrfTokenName: '<?= $this->security->get_csrf_token_name() ?>',
        csrfHash: '<?= $this->security->get_csrf_hash() ?>',
        standardRolls: [1.1, 1.3, 1.6, 2.2, 2.5, 3.2],
        tolerance: 0, // Disabled 2cm margin
        defaultCategoryId: '<?= $default_category_id ?? 1 ?>'
    };

    // Helper for updating URL parameters without using new URL() which can conflict
    function updateWipFilter(key, value) {
        var uri = window.location.href;

        // Always reset to page 1 when changing filters
        if (key !== 'page') {
            var rePage = new RegExp("([?&])page=.*?(&|$)", "i");
            var separator = uri.indexOf('?') !== -1 ? "&" : "?";
            if (uri.match(rePage)) {
                uri = uri.replace(rePage, '$1' + 'page=1' + '$2');
            } else {
                uri = uri + separator + 'page=1';
            }
        }

        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            window.location.href = uri.replace(re, '$1' + key + "=" + value + '$2');
        } else {
            window.location.href = uri + separator + key + "=" + value;
        }
    }
</script>

<div class="app-content mt-3">
    <?php if ($this->input->get('iframe') == '1' || $this->input->get('readonly') == '1'): ?>
        <style>
            <?php if ($this->input->get('iframe') == '1'): ?>
                .app-content {
                    margin-top: 0 !important;
                    padding: 0 !important;
                }

                .report-section-card {
                    border-radius: 0 !important;
                    margin-bottom: 0 !important;
                    box-shadow: none !important;
                    border: none !important;
                }

                .section-header {
                    display: none !important;
                }

            <?php endif; ?>
            .btn-action-container,
            .btn-group,
            .btn-primary:not(#btn-search),
            .btn-success,
            .btn-danger:not(#btn-reset) {
                display: none !important;
            }

            .badge.bg-primary.cursor-pointer {
                pointer-events: none;
            }

            /* Disable all action buttons in the table for PB Tracking */
            tbody .btn,
            tbody .btn-action-premium,
            tbody button {
                display: none !important;
            }
        </style>
    <?php endif; ?>
    <!-- Main Content Card -->
    <div class="report-section-card">
        <div class="section-header">
            <h5>
                <i class="fas fa-industry"></i> Monitoring Alur Produksi
                <?php
                $br_name = $this->session->userdata('nama_perusahaan');
                if ($br_name)
                    echo " <span class='badge bg-white text-dark ms-2'>[" . strtoupper($br_name) . "]</span>";
                ?>
            </h5>
            <div class="d-flex align-items-center gap-2">
                <button onclick="openGlobalSearchModal()"
                    class="btn btn-sm btn-primary rounded-pill px-3 fw-bold shadow-sm">
                    <i class="fas fa-search me-1"></i> Lacak SPK Global
                </button>
                <button onclick="showManualGuide()"
                    class="btn btn-sm btn-info text-white rounded-pill px-3 fw-bold shadow-sm">
                    <i class="fas fa-question-circle me-1"></i> Panduan
                </button>
                <button onclick="location.reload()" class="btn btn-sm btn-light rounded-pill px-3 fw-bold">
                    <i class="fas fa-sync-alt me-1"></i> Refresh Data
                </button>
            </div>
        </div>

        <?php
        $has_pb = (!empty($stats['ready_to_pb']) && $stats['ready_to_pb'] > 0);
        $has_proses = (!empty($stats['produksi']) && $stats['produksi'] > 0);
        ?>
        <?php if ($has_pb || $has_proses): ?>
            <div class="d-none d-flex flex-column flex-md-row border-bottom w-100 animate__animated animate__fadeInDown">
                <?php if ($has_pb): ?>
                    <div
                        class="flex-fill bg-warning bg-opacity-10 px-4 py-3 d-flex align-items-center justify-content-between <?= $has_proses ? 'border-end border-warning border-opacity-25' : 'border-bottom border-warning' ?>">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                style="width: 40px; height: 40px;">
                                <i class="fas fa-truck-loading fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark"><?= $stats['ready_to_pb'] ?> SPK Siap Dikirim ke PB!</h6>
                                <p class="small mb-0 text-muted opacity-75">Klik tombol PB pada daftar untuk serah terima.</p>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-warning rounded-pill px-4 fw-bold shadow-sm ms-3 text-nowrap"
                            onclick="filterSiapPB()">Lihat Daftarnya</button>
                    </div>
                <?php endif; ?>

                <?php if ($has_proses): ?>
                    <div
                        class="flex-fill bg-primary bg-opacity-10 px-4 py-3 d-flex align-items-center justify-content-between <?= !$has_pb ? 'border-bottom border-primary' : '' ?>">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                style="width: 40px; height: 40px;">
                                <i class="fas fa-cogs fa-lg"></i>
                            </div>
                            <div>
                                <p class="mb-0 fw-bold text-dark">Ada <?= $stats['produksi'] ?> Pekerjaan Sedang Aktif – Gunakan
                                    filter untuk melihat daftar yang sedang Anda kerjakan.</p>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-primary rounded-pill px-4 fw-bold shadow-sm ms-3 text-nowrap"
                            onclick="filterSedangProses()"><i class="fas fa-filter me-1"></i> Filter Aktif</button>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($this->input->get('search_spk'))): ?>
            <div
                class="alert alert-info border-info border-start border-4 mb-0 rounded-0 d-flex flex-column flex-md-row align-items-center justify-content-between px-4 py-3 bg-info bg-opacity-10">
                <div class="d-flex align-items-center gap-3 mb-2 mb-md-0">
                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                        style="width: 40px; height: 40px;">
                        <i class="fas fa-search fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark">Mode Pencarian Aktif</h6>
                        <p class="small mb-0 text-muted opacity-75">Menampilkan hasil pencarian untuk:
                            <strong><?= htmlspecialchars($this->input->get('search_spk')) ?></strong>. Filter tab status dan
                            tanggal diabaikan sementara.</p>
                    </div>
                </div>
                <a href="<?= site_url('wip/produksi?page=1&operator=' . ($active_operator ?? 'all') . '&group_mode=' . ($group_mode ?? 'bahan') . '&status_view=' . ($filter_status_view ?? 'pending')) ?>"
                    class="btn btn-danger btn-sm rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center gap-2">
                    <i class="fas fa-times-circle"></i> Reset Pencarian
                </a>
            </div>
        <?php endif; ?>

        <div class="bg-white px-4 py-3 border-bottom border-light d-flex align-items-center justify-content-between">
            <?php $this->load->view('admin/wip/components/v_filter_tabs'); ?>


        </div>

        <div class="p-0">
            <?php
            // Pre-calculate which view should be shown initially to prevent FOUC (Flash of Unstyled Content)
            $is_material_view_default = $is_material_view_default ?? false;
            ?>
            <!-- VIEW SPK: Default for most categories -->
            <div id="container-view-spk" class="<?= $is_material_view_default ? 'd-none' : '' ?>">
                <?php $this->load->view('admin/wip/components/v_table_spk_view'); ?>
            </div>

            <!-- VIEW ITEM: Optimized for Large Format (Ripping/Nesting) -->
            <div id="container-view-item" class="<?= $is_material_view_default ? '' : 'd-none' ?>">
                <?php $this->load->view('admin/wip/components/v_table_item_view', ['group_mode' => $group_mode ?? 'bahan']); ?>
            </div>

            <?php if (!empty($pagination)): ?>
                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-center p-3 border-top bg-light">
                    <div class="text-muted small mb-2 mb-md-0">
                        Menampilkan <span class="fw-bold text-dark"><?= $offset + 1 ?></span>
                        sampai <span class="fw-bold text-dark"><?= min($offset + $per_page, $total_products) ?></span>
                        dari <span class="fw-bold text-dark"><?= $total_products ?></span> SPK
                    </div>
                    <div>
                        <?= $pagination ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ================= MODAL SECTION ================= -->
<?php $this->load->view('admin/wip/components/v_modals_produksi'); ?>

<!-- NESTING SUMMARY WIDGET (Floating) -->
<?php $this->load->view('admin/wip/components/v_nesting_widget'); ?>

<script src="<?= base_url('assets/js/wip_produksi.js?v=' . time()) ?>"></script>

<!-- Inline Script for New Features (Simulating Controller Endpoint) -->
<script>
    /**
     * Membuka Modal Lapor Rijek
     */
    function laporRijekSPK(nospk, btn) {
        // Reset Form
        $('#formLaporRijek')[0].reset();
        $('#rijekNospk').val(nospk);
        $('#lblRijekNospk').text(nospk);

        // Get items data from button
        const items = $(btn).data('items');
        const container = $('#rijekItemsContainer');
        container.empty();
        $('#countRijekSelected').text('0 terpilih');
        $('#checkAllRijek').prop('checked', false);
        $('#rijekItemError').hide();

        // Render Checkboxes with QTY Input
        if (items && items.length > 0) {
            items.forEach(item => {
                const isItemDone = (item.status === 'done' || item.status === 'on_pb' || item.status === 'on_costumer');
                const badge = isItemDone ? '<span class="badge bg-success" style="font-size:0.6rem">DONE</span>' : '';

                const html = `
                    <div class="d-flex align-items-center border-bottom mb-1 pb-1 gap-2">
                        <div class="form-check m-0 flex-grow-1">
                            <input class="form-check-input rijek-item-check" type="checkbox" 
                                value="${item.id}" id="rjk_${item.id}" 
                                data-qty="${item.qty}"
                                onchange="toggleRijekQty(this)">
                            <label class="form-check-label w-100" for="rjk_${item.id}">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold small text-dark">${item.nama}</span>
                                    ${badge}
                                </div>
                                <div class="text-muted small" style="font-size: 0.7rem;">
                                    Total Order: <b>${item.qty}</b> pcs | ${item.dimensi}
                                </div>
                            </label>
                        </div>
                        <div style="width: 80px;">
                            <input type="number" class="form-control form-control-sm text-center rijek-qty-input fw-bold" 
                                id="qty_rjk_${item.id}" 
                                value="${item.qty}" 
                                max="${item.qty}" min="1" 
                                placeholder="Qty" disabled>
                        </div>
                    </div>
                `;
                container.append(html);
            });
        } else {
            container.html('<div class="text-center text-muted small py-2">Tidak ada data item.</div>');
        }

        // Show Modal
        var myModal = new bootstrap.Modal(document.getElementById('modalLaporRijek'));
        myModal.show();
    }

    // Enter key listener for Rijek PIN
    $(document).on('keyup', '#rijekPin', function (e) {
        if (e.keyCode === 13) submitLaporRijek();
    });

    function toggleRijekQty(checkbox) {
        const id = $(checkbox).val();
        const input = $(`#qty_rjk_${id}`);

        if ($(checkbox).is(':checked')) {
            input.prop('disabled', false).focus();
        } else {
            input.prop('disabled', true);
            // Reset to max qty when unchecked
            const max = $(checkbox).data('qty');
            input.val(max);
        }
        updateRijekCount();
    }

    function updateRijekCount() {
        const count = $('.rijek-item-check:checked').length;
        $('#countRijekSelected').text(count + ' terpilih');
    }

    function toggleAllRijek(source) {
        const isChecked = source.checked;
        $('.rijek-item-check').each(function () {
            $(this).prop('checked', isChecked);
            toggleRijekQty(this); // Trigger enable/disable logic
        });
        updateRijekCount();
    }

    /**
     * Submit Laporan Rijek
     */
    function submitLaporRijek() {
        const nospk = $('#rijekNospk').val();
        const jenis = $('#rijekJenis').val();
        const ket = $('#rijekKeterangan').val();
        const reprint = $('#rijekReprint').is(':checked') ? 1 : 0;
        const pin = $('#rijekPin').val();

        if (!pin || pin.length < 4) {
            Swal.fire('Error', 'Masukkan PIN Verifikasi (min 4 digit)!', 'error');
            return;
        }

        // Get Selected Items with Qty
        let selectedItems = [];
        let hasError = false;

        $('.rijek-item-check:checked').each(function () {
            const id = $(this).val();
            const qtyVal = parseInt($(`#qty_rjk_${id}`).val());
            const maxQty = parseInt($(this).data('qty'));

            if (!qtyVal || qtyVal <= 0) {
                hasError = true;
                $(`#qty_rjk_${id}`).addClass('is-invalid');
            } else {
                $(`#qty_rjk_${id}`).removeClass('is-invalid');
                selectedItems.push({
                    id: id,
                    qty_rijek: qtyVal
                });
            }
        });

        if (hasError) {
            Swal.fire('Error', 'Pastikan Qty Rijek diisi dengan benar (min 1)!', 'error');
            return;
        }

        if (selectedItems.length === 0) {
            $('#rijekItemError').show();
            $('#rijekItemsContainer').parent().addClass('border-danger');
            setTimeout(() => $('#rijekItemsContainer').parent().removeClass('border-danger'), 500);
            return;
        }
        $('#rijekItemError').hide();

        if (!jenis) {
            Swal.fire('Error', 'Pilih jenis masalah terlebih dahulu!', 'error');
            return;
        }

        Swal.fire({
            title: 'Kirim Laporan Masalah?',
            text: `Data ${selectedItems.length} item terpilih akan dilaporkan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Tandai Bermasalah!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading() }
                });

                $.ajax({
                    url: WIP_CONFIG.baseUrl + 'lapor_masalah',
                    type: 'POST',
                    data: {
                        nospk: nospk,
                        items_rijek: selectedItems, // Modified Payload [{id:1, qty_rijek:5}, ...]
                        jenis_masalah: jenis,
                        keterangan: ket,
                        is_reprint: reprint,
                        pin_operator: pin,
                        csrf_token_name: WIP_CONFIG.csrfHash
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Laporan Terkirim',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', response.message || 'Terjadi kesalahan sistem', 'error');
                        }
                    },
                    error: function (xhr, status, error) {
                        try {
                            console.error(xhr.responseText);
                            Swal.fire('Gagal', 'Error Backend: ' + error, 'error');
                        } catch (e) {
                            Swal.fire('Gagal', 'Koneksi Error', 'error');
                        }
                    }
                });
            }
        });
    }

    /**
     * Menampilkan Modal Panduan Operator
     */
    function showManualGuide() {
        var myModal = new bootstrap.Modal(document.getElementById('modalManualGuide'));
        myModal.show();
    }

    /**
     * Tampilkan Modal Preview SPK
     */
    function previewSPK(nospk) {
        Swal.fire({
            title: 'Memuat Detail SPK...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: WIP_CONFIG.baseUrl + 'get_spk_details_ajax',
            type: 'POST',
            data: {
                nospk: nospk,
                csrf_token_name: WIP_CONFIG.csrfHash
            },
            dataType: 'json',
            success: function (response) {
                if (response && response.items && response.items.length > 0) {
                    let html = `<div class="text-start text-dark" style="font-size: 0.95rem; line-height: 1.6;">`;

                    // 1. Produk
                    html += `<b>Produk :</b><br>`;
                    response.items.forEach((item) => {
                        let qtyStr = parseFloat(item.qty);
                        let sat = item.satuan_nama ? item.satuan_nama : 'Pcs';
                        html += `- ${item.product_nama} ${qtyStr} ${sat}<br>`;
                    });

                    // 2. Work Center
                    let wcSet = new Set();
                    response.items.forEach((item) => {
                        if (item.routing && item.routing.length > 0) {
                            item.routing.forEach(r => {
                                if (r.routing_name) wcSet.add(r.routing_name);
                            });
                        }
                    });
                    if (wcSet.size > 0) {
                        html += `<br><b>Work Center :</b><br>${Array.from(wcSet).join(', ')}<br>`;
                    }

                    // 3. Bahan
                    let bahanMap = {};
                    response.items.forEach((item) => {
                        if (item.bom_lines && item.bom_lines.length > 0) {
                            item.bom_lines.forEach(b => {
                                if (!b.bahan_nama) return;
                                let total = parseFloat(b.bom_qty) * parseFloat(item.qty);
                                let key = b.bahan_nama;
                                if (bahanMap[key]) {
                                    bahanMap[key].qty += total;
                                } else {
                                    bahanMap[key] = { qty: total, unit: b.satuan_nama || '' };
                                }
                            });
                        }
                    });
                    let bahanKeys = Object.keys(bahanMap);
                    if (bahanKeys.length > 0) {
                        html += `<br><b>Bahan :</b><br>`;
                        bahanKeys.forEach(key => {
                            let b = bahanMap[key];
                            let qtyFmt = b.qty.toLocaleString('id-ID', { maximumFractionDigits: 2 });
                            html += `- ${key} ${qtyFmt} ${b.unit}<br>`;
                        });
                    }

                    // 4. Catatan
                    let catatanSet = new Set();
                    response.items.forEach((item) => {
                        if (item.catatan) catatanSet.add(item.catatan);
                    });
                    if (catatanSet.size > 0) {
                        html += `<br><b>Catatan :</b> `;
                        html += Array.from(catatanSet).join(' | ') + `<br>`;
                    }

                    html += `</div>`;

                    Swal.fire({
                        title: 'Detail SPK ' + nospk,
                        html: html,
                        confirmButtonText: 'Tutup',
                        confirmButtonColor: '#3085d6',
                        width: '500px'
                    });
                } else {
                    Swal.fire('Gagal', 'Data SPK tidak ditemukan atau belum diproses.', 'info');
                }
            },
            error: function () {
                Swal.fire('Error', 'Gagal memuat detail dari server.', 'error');
            }
        });
    }

    /**
     * Switch Mode Grouping di Tabel
     */
    function switchGroupMode(mode) {
        const url = new URL(window.location.href);
        url.searchParams.set('group_mode', mode);
        window.location.href = url.toString();
    }

    // --- AUTO REFRESH SCRIPT ---
    // Halaman akan otomatis refresh setiap 60 detik untuk memperbarui antrean
    let autoRefreshInterval;
    const REFRESH_TIME = 60000; // 60 detik

    function startAutoRefresh() {
        clearInterval(autoRefreshInterval);
        autoRefreshInterval = setInterval(() => {
            // JANGAN refresh jika ada modal (bootstrap/sweetalert) yang sedang terbuka
            if ($('.modal.show').length > 0 || $('.swal2-container').length > 0) {
                return;
            }
            // JANGAN refresh jika operator sedang mengetik sesuatu di input/search
            if ($('input:focus, textarea:focus, select:focus').length > 0) {
                return;
            }

            // Lakukan Silent Refresh (AJAX) tanpa reload halaman utuh
            $.ajax({
                url: window.location.href,
                type: 'GET',
                success: function(html) {
                    var $parsed = $(html);
                    
                    // Update Tabel SPK View
                    var newSpk = $parsed.find('#container-view-spk').html();
                    if (newSpk !== undefined) $('#container-view-spk').html(newSpk);
                    
                    // Update Tabel Item View
                    var newItem = $parsed.find('#container-view-item').html();
                    if (newItem !== undefined) $('#container-view-item').html(newItem);
                    
                    // Update Badge Angka pada Tabs
                    var newTabs = $parsed.find('.bg-white.px-4.py-3.border-bottom').html();
                    if (newTabs !== undefined) $('.bg-white.px-4.py-3.border-bottom').html(newTabs);

                    // Update Banner Need Action (SPK Siap PB, dll)
                    var newBanners = $parsed.find('.animate__fadeInDown').html();
                    if (newBanners !== undefined) $('.animate__fadeInDown').html(newBanners);
                }
            });
        }, REFRESH_TIME);
    }

    $(document).ready(function () {
        startAutoRefresh();
    });
</script>