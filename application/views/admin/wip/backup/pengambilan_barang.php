<?php $this->load->view('layout_admin/header_modul'); ?>

<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap"
    rel="stylesheet">

<style>
    /* ==========================================================================
    ERP: PENGAMBILAN BARANG (HYBRID PREMIUM UI - DESKTOP & MOBILE)
    ========================================================================== */
    :root {
        --pb-primary: #0f172a;
        --pb-accent: #3b82f6;
        --pb-success: #10b981;
        --pb-warning: #f59e0b;
        --pb-danger: #ef4444;
        --pb-glass: rgba(255, 255, 255, 0.75);
        --pb-surface: #ffffff;
        --pb-bg: #f1f5f9;
        --premium-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        --font-outfit: 'Outfit', sans-serif;
    }

    body {
        font-family: var(--font-outfit);
        background-color: var(--pb-bg);
        color: #1e293b;
    }

    .pb-app-container {
        max-width: 1600px;
        margin: 0 auto;
        padding-bottom: 50px;
    }

    /* Header & Navigation */
    .pb-header {
        background: white;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .pb-nav-tabs {
        display: flex;
        background: #f1f5f9;
        padding: 6px;
        border-radius: 14px;
        gap: 4px;
    }

    .pb-nav-link {
        padding: 10px 24px;
        border-radius: 10px;
        font-weight: 800;
        font-size: 0.85rem;
        color: #64748b;
        text-decoration: none !important;
        transition: all 0.2s;
        border: none;
        background: transparent;
        text-transform: uppercase;
    }

    .pb-nav-link.active {
        background: white;
        color: var(--pb-primary);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    /* Action Bar (Search & Filter) */
    .pb-toolbar {
        background: white;
        padding: 1rem 2rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        align-items: center;
    }

    .pb-search-box {
        position: relative;
        flex: 1;
        min-width: 300px;
    }

    .pb-search-box i {
        position: absolute;
        left: 1.25rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .pb-search-input {
        width: 100%;
        padding: 0.8rem 1rem 0.8rem 3.2rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        font-weight: 700;
        transition: all 0.3s;
    }

    .pb-search-input:focus {
        outline: none;
        background: white;
        border-color: var(--pb-accent);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    /* Desktop Table */
    .pb-table-wrapper {
        padding: 1.5rem;
        background: transparent;
    }

    .pb-table-card {
        background: white;
        border-radius: 20px;
        padding: 1rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid #e2e8f0;
    }

    .pb-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
    }

    .pb-table thead th {
        padding: 1rem;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 1px;
        border: none;
    }

    .pb-table tbody tr {
        transition: all 0.2s;
    }

    .pb-table tbody tr:hover {
        background: #f8fafc;
    }

    .pb-table td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
        border-top: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }

    .pb-table tr:first-child td {
        border-top: none;
    }

    /* Tags & Badges */
    .badge-premium {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 800;
        font-size: 0.7rem;
        text-transform: uppercase;
    }

    .aging-tag {
        font-weight: 900;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.65rem;
    }

    /* Mobile Adaptations */
    @media (max-width: 991px) {
        .pb-desktop-only {
            display: none !important;
        }

        .pb-header {
            padding: 1rem;
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }

        .pb-nav-tabs {
            width: 100%;
        }

        .pb-nav-link {
            flex: 1;
            text-align: center;
        }

        .pb-toolbar {
            padding: 1rem;
        }

        .pb-table-card {
            border-radius: 0;
            border: none;
            padding: 0.5rem;
            margin: -1.5rem;
        }

        /* Fallback cards for mobile */
        .pb-mobile-card {
            background: white;
            margin-bottom: 1rem;
            padding: 1.25rem;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
        }
    }

    @media (min-width: 992px) {
        .pb-mobile-only {
            display: none !important;
        }
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Bulk Bar */
    .pb-bulk-bar {
        position: fixed;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%) translateY(200%);
        background: var(--pb-primary);
        color: white;
        padding: 1rem 2rem;
        border-radius: 20px;
        display: flex;
        align-items: center;
        gap: 2rem;
        z-index: 1050;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .pb-bulk-bar.show {
        transform: translateX(-50%) translateY(0);
    }

    .btn-handover {
        background: var(--pb-primary);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 800;
        font-size: 0.8rem;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-handover:hover {
        background: #1e293b;
        transform: translateY(-2px);
    }
</style>

<?php
function getAgingLabel($datetime)
{
    if (!$datetime)
        return ['label' => 'BARU', 'class' => 'bg-info bg-opacity-10 text-info'];
    $start = new DateTime($datetime);
    $now = new DateTime();
    $diff = $start->diff($now);
    if ($diff->d > 3)
        return ['label' => $diff->d . 'H+', 'class' => 'bg-danger bg-opacity-10 text-danger'];
    if ($diff->d > 0)
        return ['label' => $diff->d . 'HARI', 'class' => 'bg-warning bg-opacity-10 text-warning'];
    if ($diff->h > 0)
        return ['label' => $diff->h . 'JAM', 'class' => 'bg-success bg-opacity-10 text-success'];
    return ['label' => 'NOW', 'class' => 'bg-primary bg-opacity-10 text-primary'];
}

$branches = [];
foreach (array_merge($detail_pb ?? [], $detail_handover ?? []) as $row) {
    if (!isset($branches[$row->perusahaan_id])) {
        $branches[$row->perusahaan_id] = $row->asal_cabang ?: 'PUSAT';
    }
}
ksort($branches);
?>

<div class="pb-app-container">
    <!-- Main Header -->
    <header class="pb-header">
        <div>
            <h4 class="fw-900 mb-1">PB HUB <span class="text-primary">•</span> LOGISTICS</h4>
            <p class="text-muted small fw-bold mb-0 text-uppercase"><?= $this->session->userdata('nama_perusahaan') ?>
            </p>
        </div>

        <div class="d-flex align-items-center gap-4">
            <nav class="pb-nav-tabs">
                <button class="pb-nav-link active" data-target="tab-queue">Antrian Hub</button>
                <button class="pb-nav-link" data-target="tab-history">Riwayat Keluar</button>
            </nav>
            <button class="btn btn-dark rounded-pill px-4 fw-800" data-bs-toggle="modal"
                data-bs-target="#modalGuidePB">GUIDE SOP</button>
        </div>
    </header>

    <!-- Toolbar -->
    <div class="pb-toolbar">
        <div class="pb-search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="appSearch" class="pb-search-input"
                placeholder="Cari SPK, Nama Pelanggan, atau Produk..." autocomplete="off">
        </div>

        <div class="d-flex gap-2 ms-auto align-items-center">
            <span class="small fw-800 text-muted opacity-50 me-2">FILTER CABANG:</span>
            <select id="branchFilter" class="form-select border-0 bg-light rounded-pill fw-800 px-4"
                style="width: 200px; height: 45px;">
                <option value="all">SEMUA CABANG</option>
                <?php foreach ($branches as $id => $name): ?>
                    <option value="<?= strtoupper($name) ?>"><?= strtoupper($name) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary rounded-pill px-4" id="btnOpenScan" style="height: 45px;"><i
                    class="fas fa-barcode me-2"></i> SCAN BARCODE</button>
        </div>
    </div>

    <!-- Content Sections -->
    <div class="tab-content">
        <!-- Tab Antrian -->
        <div class="tab-pane fade show active" id="tab-queue">
            <div class="pb-table-wrapper">
                <div class="pb-table-card">
                    <div class="table-responsive">
                        <table class="table pb-table mb-0">
                            <thead>
                                <tr>
                                    <th width="40"><input type="checkbox" id="checkAll" class="form-check-input"></th>
                                    <th>SPK & Aging</th>
                                    <th>Pelanggan</th>
                                    <th>Produk Ready</th>
                                    <th>Status Bayar</th>
                                    <th>Cabang</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="queueTableBody">
                                <?php if (empty($detail_pb)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted fw-bold">Belum ada antrian
                                            pengambilan barang.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($detail_pb as $d): ?>
                                        <?php
                                        $aging = getAgingLabel($d->tgl_kirim_pb);
                                        $payStatus = strtolower($d->status_pembayaran);
                                        $isPaid = ($payStatus == 'paid');
                                        ?>
                                        <tr class="pb-row" data-spk="<?= $d->nospk ?>"
                                            data-customer="<?= strtoupper($d->konsumen_nama) ?>"
                                            data-branch="<?= strtoupper($d->asal_cabang ?: 'PUSAT') ?>">
                                            <td>
                                                <input type="checkbox" class="form-check-input bulk-cek"
                                                    data-id="<?= $d->produksi_id ?>" data-nospk="<?= $d->nospk ?>"
                                                    data-pay="<?= $payStatus ?>">
                                            </td>
                                            <td>
                                                <div class="fw-900 text-dark">#<?= $d->nospk ?></div>
                                                <span class="aging-tag <?= $aging['class'] ?>"><?= $aging['label'] ?></span>
                                            </td>
                                            <td>
                                                <div class="fw-800 text-dark"><?= strtoupper($d->konsumen_nama) ?></div>
                                                <div class="small text-muted fw-bold"><?= $d->hp ?: '-' ?></div>
                                            </td>
                                            <td>
                                                <?php if (!empty($d->breakdown)): ?>
                                                    <?php foreach ($d->breakdown as $bd): ?>
                                                        <div class="small fw-700 text-dark"><?= $bd->product_nama ?> <span
                                                                class="text-primary fw-900">•
                                                                <?= number_format($bd->total_ready, 0) ?></span></div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <div class="fw-900 text-primary"><?= number_format($d->total_qty_output, 0) ?>
                                                        QTY</div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge-premium <?= $isPaid ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' ?>">
                                                    <i
                                                        class="fas <?= $isPaid ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> me-1"></i>
                                                    <?= $isPaid ? 'LUNAS' : 'BELUM BAYAR' ?>
                                                </span>
                                            </td>
                                            <td><span
                                                    class="fw-800 text-muted"><?= strtoupper($d->asal_cabang ?: 'PUSAT') ?></span>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button class="btn btn-outline-primary btn-sm rounded-8"
                                                        onclick="verifySPK('<?= $d->nospk ?>')" title="Verifikasi">
                                                        <i class="fas fa-search-plus"></i>
                                                    </button>
                                                    <button class="btn-handover"
                                                        onclick="handover(this, <?= (int) $d->produksi_id ?>, '<?= $d->nospk ?>', <?= (float) $d->total_qty_output ?>, '<?= $payStatus ?>')">
                                                        <i class="fas fa-handshake"></i> SERAHKAN
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Riwayat -->
        <div class="tab-pane fade" id="tab-history">
            <div class="pb-table-wrapper">
                <div class="pb-table-card">
                    <div class="table-responsive">
                        <table class="table pb-table mb-0">
                            <thead>
                                <tr>
                                    <th>SPK</th>
                                    <th>Pelanggan</th>
                                    <th>Waktu Keluar</th>
                                    <th>Penerima / Note</th>
                                    <th>Foto</th>
                                    <th>Cabang</th>
                                </tr>
                            </thead>
                            <tbody id="historyTableBody">
                                <?php if (empty($detail_handover)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted fw-bold">Belum ada riwayat
                                            penyerahan.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($detail_handover as $d): ?>
                                        <tr data-branch="<?= strtoupper($d->asal_cabang ?: 'PUSAT') ?>">
                                            <td>
                                                <div class="fw-900">#<?= $d->nospk ?></div>
                                            </td>
                                            <td>
                                                <div class="fw-800"><?= strtoupper($d->konsumen_nama) ?></div>
                                            </td>
                                            <td>
                                                <div class="fw-900 text-dark"><?= date('H:i', strtotime($d->max_update)) ?>
                                                </div>
                                                <div class="small fw-bold text-muted">
                                                    <?= date('d/m/Y', strtotime($d->max_update)) ?></div>
                                            </td>
                                            <td>
                                                <div class="bg-light p-2 rounded-8 small fw-bold"
                                                    style="max-width: 250px; white-space: pre-wrap;"><?= $d->catatan ?: '-' ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($d->foto_handover): ?>
                                                    <img src="<?= base_url('uploads/handover/' . $d->foto_handover) ?>"
                                                        class="rounded-8 border cursor-pointer"
                                                        style="width: 45px; height: 45px; object-fit: cover;"
                                                        onclick="showLargePhoto(this.src)">
                                                <?php else: ?>
                                                    <span class="text-muted small italic">No Photo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><span
                                                    class="fw-800 text-muted"><?= strtoupper($d->asal_cabang ?: 'PUSAT') ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Mode Bar -->
    <div class="pb-bulk-bar" id="bulkBar">
        <div class="d-flex align-items-center gap-3">
            <i class="fas fa-check-double fa-lg"></i>
            <div class="fw-900"><span id="bulkCountText">0</span> ITEM TERPILIH</div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-link text-white text-decoration-none fw-bold" onclick="cancelBulk()">BATAL</button>
            <button class="btn btn-white px-4 rounded-pill fw-900 text-dark" onclick="bulkHandover()">PROSES
                MASSAL</button>
        </div>
    </div>
</div>

<!-- Icons & Modals -->
<div id="scannerContainer" class="d-none">
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.8); z-index: 1060;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 overflow-hidden">
                <div class="modal-header bg-dark text-white p-4">
                    <h5 class="modal-title fw-900">SYSTEM SCANNER</h5>
                    <button type="button" class="btn-close btn-close-white"
                        onclick="$('#scannerContainer').addClass('d-none')"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="reader" style="width: 100%; height: 400px; background: #000;"></div>
                    <div class="p-4 bg-light text-center">
                        <input type="text" id="manualSPK"
                            class="form-control form-control-lg text-center fw-900 rounded-pill border-primary mb-3"
                            placeholder="TYPE SPK # MANUALLY">
                        <button class="btn btn-primary w-100 py-3 rounded-pill fw-900"
                            onclick="processManualEntry()">CARI SPK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Modals for SOP, Preview SPK, etc. (Previously defined)
?>
<!-- Include Modals here -->

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    $(document).ready(function () {
        // Navigation
        $('.pb-nav-link').on('click', function () {
            const target = $(this).data('target');
            $('.pb-nav-link').removeClass('active');
            $(this).addClass('active');
            $('.tab-pane').removeClass('show active');
            $('#' + target).addClass('show active');
        });

        // Search Toggle
        $('#appSearch').on('keyup', function () {
            const query = $(this).val().toLowerCase();
            $('.pb-row').filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(query) > -1);
            });
        });

        // Branch Filter
        $('#branchFilter').on('change', function () {
            const branch = $(this).val();
            if (branch === 'all') {
                $('.pb-row').show();
            } else {
                $('.pb-row').hide();
                $(`.pb-row[data-branch="${branch}"]`).show();
            }
        });

        // Bulk Logic
        $('#checkAll').on('change', function () {
            $('.bulk-cek:visible').prop('checked', this.checked).trigger('change');
        });

        $('.bulk-cek').on('change', function () {
            const checkedCount = $('.bulk-cek:checked').length;
            if (checkedCount > 0) {
                $('#bulkCountText').text(checkedCount);
                $('#bulkBar').addClass('show');
            } else {
                $('#bulkBar').removeClass('show');
                $('#checkAll').prop('checked', false);
            }
        });

        // Scanner
        let qrReader;
        $('#btnOpenScan').on('click', function () {
            $('#scannerContainer').removeClass('d-none');
            qrReader = new Html5Qrcode("reader");
            const config = { fps: 20, qrbox: { width: 300, height: 300 } };
            qrReader.start({ facingMode: "environment" }, config, (decodedText) => {
                stopScan(decodedText);
            });
        });

        function stopScan(result) {
            if (qrReader) {
                qrReader.stop().then(() => {
                    $('#scannerContainer').addClass('d-none');
                    if (result) processBarcodeResult(result);
                });
            }
        }
    });

    function processManualEntry() {
        const val = $('#manualSPK').val().trim();
        if (val) {
            $('#scannerContainer').addClass('d-none');
            processBarcodeResult(val);
        }
    }

    function processBarcodeResult(nospk) {
        let row = $(`.pb-row[data-spk*="${nospk.toUpperCase()}"]`).first();
        if (row.length > 0) {
            $('html, body').animate({ scrollTop: row.offset().top - 150 }, 600);
            row.addClass('table-primary');
            setTimeout(() => {
                row.removeClass('table-primary');
                row.find('.btn-handover').click();
            }, 800);
        } else {
            Swal.fire('Info', 'SPK #' + nospk + ' tidak ditemukan di antrian.', 'info');
        }
    }

    function cancelBulk() {
        $('.bulk-cek').prop('checked', false);
        $('#bulkBar').removeClass('show');
        $('#checkAll').prop('checked', false);
    }

    // Global Core Functions (Handover, Verify, etc)
    function verifySPK(nospk) {
        Swal.fire({
            title: 'VERIFIKASI #' + nospk,
            html: '<div id="v-spin" class="p-4 text-center"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></div><div id="v-cont"></div>',
            width: '600px',
            showConfirmButton: false,
            showCloseButton: true,
            didOpen: () => {
                $.ajax({
                    url: '<?= site_url('pb/get_pb_verify_details_ajax') ?>',
                    type: 'POST',
                    data: { nospk: nospk },
                    dataType: 'json',
                    success: function (resp) {
                        $('#v-spin').hide();
                        if (resp.success && resp.items.length > 0) {
                            let inner = '';
                            resp.items.forEach(item => {
                                inner += `
                                <div class="p-3 bg-light rounded-4 border mb-2 d-flex justify-content-between align-items-center">
                                    <div class="text-start">
                                        <div class="fw-900">${item.product_nama}</div>
                                        <div class="small fw-bold text-muted mt-1">Order: ${parseInt(item.total_qty || item.qty)} | <span class="text-primary">Ready: ${parseInt(item.qty_final)}</span></div>
                                    </div>
                                    <i class="fas fa-check-circle text-success fs-5"></i>
                                </div>`;
                            });
                            $('#v-cont').html(`<div class="text-start p-2">${inner}</div><div class="d-grid mt-3"><button class="btn btn-primary rounded-4 py-3 fw-900" onclick="handover(null, null, '${nospk}', 0, 'paid')">PROSES PENYERAHAN</button></div>`);
                        }
                    }
                });
            }
        });
    }

    function handover(btn, id, nospk, qty, payStatus) {
        if (payStatus !== 'paid') {
            Swal.fire({
                title: 'BELUM LUNAS',
                text: "Pesanan ini belum berstatus LUNAS. Tetap diserahkan?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'YA, LANJUTKAN'
            }).then((result) => { if (result.isConfirmed) launchHandoverFlow(nospk); });
        } else {
            launchHandoverFlow(nospk);
        }
    }

    function launchHandoverFlow(nospk) {
        $.ajax({
            url: '<?= site_url('pb/get_pb_verify_details_ajax') ?>',
            type: 'POST',
            data: { nospk: nospk },
            dataType: 'json',
            success: function (resp) {
                let list = '';
                resp.items.forEach(item => {
                    if (item.status_produksi === 'on_pb') {
                        list += `<label class="d-flex align-items-center gap-3 p-3 bg-light rounded-4 border mb-2 cursor-pointer">
                        <input type="checkbox" name="prod_ids" value="${item.produksi_id}" checked style="width: 20px; height: 20px;">
                        <div class="text-start">
                            <div class="fw-800">${item.product_nama}</div>
                            <div class="text-primary fw-900 small">${item.qty_final} UNIT</div>
                        </div>
                    </label>`;
                    }
                });

                Swal.fire({
                    title: 'PENYERAHAN #' + nospk,
                    width: '600px',
                    html: `
                    <div class="text-start">
                        <div class="mb-3">${list}</div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <select id="via" class="form-select rounded-4 py-3 fw-800">
                                    <option value="Diambil Langsung">Langsung</option>
                                    <option value="Kurir / Ojek Online">Kurir / Ojol</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <input type="text" id="note-pb" class="form-control rounded-4 py-3 fw-800" placeholder="Penerima / Resi">
                            </div>
                        </div>
                        <input type="file" id="file-photo" class="form-control rounded-4 py-2 mb-3" accept="image/*" capture="environment">
                    </div>`,
                    showCancelButton: true,
                    confirmButtonText: 'KONFIRMASI PENYERAHAN',
                    preConfirm: () => {
                        const ids = Array.from(document.querySelectorAll('input[name="prod_ids"]:checked')).map(c => c.value);
                        if (ids.length === 0) return Swal.showValidationMessage('Pilih minimal 1 item!');
                        return { via: $('#via').val(), note: $('#note-pb').val(), photo: $('#file-photo')[0].files[0], ids: ids };
                    }
                }).then(res => {
                    if (res.isConfirmed) {
                        const d = new FormData();
                        d.append('nospk', nospk);
                        d.append('pengambilan_via', res.value.via);
                        d.append('pengambilan_note', res.value.note);
                        res.value.ids.forEach(i => d.append('produksi_ids[]', i));
                        if (res.value.photo) d.append('foto_handover', res.value.photo);

                        $.ajax({
                            url: '<?= site_url('pb/handover_to_customer') ?>',
                            type: 'POST', data: d, processData: false, contentType: false,
                            success: r => { if (r.success) location.reload(); }
                        });
                    }
                });
            }
        });
    }

    function bulkHandover() {
        const selected = $('.bulk-cek:checked');
        const spks = []; const ids = [];
        selected.each(function () {
            ids.push($(this).data('id'));
            const n = $(this).data('nospk');
            if (!spks.includes(n)) spks.push(n);
        });

        Swal.fire({
            title: 'BATCH MASAL: ' + spks.length + ' SPK',
            html: `
            <textarea id="bulk-note" class="form-control rounded-4 p-3 fw-bold mb-3" placeholder="Catatan untuk semua item..."></textarea>
            <input type="file" id="bulk-f" class="form-control rounded-4" accept="image/*">`,
            showCancelButton: true,
            confirmButtonText: 'PROSES SEMUA',
            preConfirm: () => { return { note: $('#bulk-note').val(), photo: $('#bulk-f')[0].files[0] }; }
        }).then(res => {
            if (res.isConfirmed) {
                const d = new FormData();
                ids.forEach(i => d.append('ids[]', i));
                spks.forEach(s => d.append('nospks[]', s));
                d.append('pengambilan_via', 'Bulk/Massal');
                d.append('pengambilan_note', res.value.note);
                if (res.value.photo) d.append('foto_handover', res.value.photo);

                $.ajax({
                    url: '<?= site_url('pb/bulk_handover_to_customer') ?>',
                    type: 'POST', data: d, processData: false, contentType: false,
                    success: r => { if (r.success) location.reload(); }
                });
            }
        });
    }

    function showLargePhoto(src) {
        Swal.fire({ imageUrl: src, imageWidth: '100%', showConfirmButton: false, showCloseButton: true });
    }
</script>

<!-- MODALS -->
<div class="modal fade" id="modalPreviewSPK" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-4 overflow-hidden border-0" style="height: 85vh;">
            <div class="modal-header bg-dark text-white p-4">
                <h5 class="modal-title fw-900 ls-1"><i class="fas fa-file-invoice me-3 text-primary"></i>INVOICE #<span
                        id="lblNospkPreview"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-secondary bg-opacity-10">
                <iframe id="iframePreview" class="w-100 h-100 border-0"></iframe>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalGuidePB" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header bg-dark text-white p-4">
                <h5 class="modal-title fw-900">PB STANDARD OPERATING PROCEDURE</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-5">
                <h5 class="fw-900 text-primary mb-3">TATA CARA PENYERAHAN BARANG</h5>
                <ol class="fw-bold text-muted lh-lg">
                    <li>Gunakan Scanner atau ketik SPK manual untuk mencari pesanan.</li>
                    <li>Pastikan status pembayaran adalah <span class="text-success">LUNAS</span>.</li>
                    <li>Periksa jumlah fisik barang sesuai dengan QTY yang tertera di sistem.</li>
                    <li>Klik tombol <span class="text-primary">SERAHKAN</span> dan isi nama penerima.</li>
                    <li>Wajib ambil foto bukti penyerahan barang (disarankan).</li>
                </ol>
            </div>
        </div>
    </div>
</div>