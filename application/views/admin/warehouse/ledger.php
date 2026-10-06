<?php
// application/views/admin/warehouse/ledger.php
?>

<!-- PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - WAREHOUSE STOCK LEDGER -->
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #1a1c23 0%, #2c3e50 100%);
        --success-gradient: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
        --info-gradient: linear-gradient(135deg, #4895ef 0%, #4cc9f0 100%);
        --warning-gradient: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);
        --danger-gradient: linear-gradient(135deg, #f72585 0%, #b5179e 100%);
        --slate-soft: #f8fafc;
        --slate-muted: #64748b;
        --slate-dark: #1e293b;
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        --accent-blue: #6366f1;
    }

    body {
        background-color: #f8fafc;
        /* font-family: 'Inter', system-ui, -apple-system, sans-serif; */
    }

    /* Standard Header */
    .warehouse-header-section {
        background: var(--primary-gradient);
        color: white;
        padding: 1.5rem 0 3.25rem 0; /* Compact padding */
        margin-bottom: 0;
        border-radius: 0 0 24px 24px; /* Tighter curves */
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        position: relative;
        overflow: hidden;
    }

    .warehouse-header-section::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -5%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        z-index: 1;
    }

    .header-content,
    .header-actions {
        position: relative;
        z-index: 10;
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .header-icon {
        width: 52px; /* Compact icon container */
        height: 52px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .header-title {
        font-size: 1.75rem; /* Smaller premium title */
        font-weight: 900;
        margin: 0;
        letter-spacing: -0.5px;
        line-height: 1.2;
    }

    .header-subtitle {
        font-size: 0.85rem; /* Compact subtitle */
        font-weight: 700;
        margin: 0 0 2px 0;
        opacity: 0.7;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #94a3b8;
    }

    /* Metric Cards */
    .stats-container {
        margin-top: -2rem; /* Compact alignment */
        position: relative;
        z-index: 10;
        margin-bottom: 1.5rem;
    }

    .metric-card {
        background: white;
        border-radius: 24px;
        padding: 1.5rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid rgba(255, 255, 255, 0.8);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        gap: 1.25rem;
        height: 100%;
    }

    .metric-icon {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
    }

    .metric-info p {
        font-size: 0.75rem;
        color: var(--slate-muted);
        margin: 0;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .metric-info h3 {
        font-size: 1.8rem;
        font-weight: 900;
        margin: 0;
        color: var(--slate-dark);
    }

    /* Content Area */
    .content-card {
        background: white;
        border-radius: 28px;
        padding: 2rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid #f8fafc;
        margin-bottom: 2rem;
    }

    .card-title-premium {
        font-size: 1.25rem;
        font-weight: 900;
        color: var(--slate-dark);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    /* Form Premium */
    .form-control-premium {
        background: #f8fafc;
        border: 2px solid #f1f5f9;
        border-radius: 14px;
        padding: 1rem 1.25rem;
        font-weight: 600;
        transition: all 0.25s;
    }

    .form-control-premium:focus {
        background: white;
        border-color: var(--accent-blue);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .form-label-premium {
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--slate-muted);
        letter-spacing: 1px;
        margin-bottom: 0.75rem;
        display: block;
    }

    /* Tables */
    .table-premium thead th {
        background: #f8fafc;
        border: none;
        color: var(--slate-muted);
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 1px;
        padding: 1.25rem 1.5rem;
    }

    .table-premium tbody td {
        padding: 1.25rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f8fafc;
        color: var(--slate-dark);
    }

    /* Badge Custom */
    .badge-premium {
        padding: 0.5rem 1rem;
        border-radius: 10px;
        font-weight: 800;
        font-size: 0.7rem;
        letter-spacing: 0.5px;
    }

    .bg-soft-success {
        background: #ecfdf5;
        color: #10b981;
    }

    .bg-soft-warning {
        background: #fffbeb;
        color: #d97706;
    }

    .bg-soft-danger {
        background: #fef2f2;
        color: #dc2626;
    }

    .bg-soft-blue {
        background: #eff6ff;
        color: #3b82f6;
    }

    /* Warehouse Selector Area */
    .selector-area {
        position: relative;
        z-index: 20;
        margin-bottom: 2.5rem;
    }

    .warehouse-nav-container {
        padding: 0.6rem;
        background: white;
        border-radius: 20px;
        box-shadow: var(--premium-shadow);
        display: inline-flex;
        gap: 10px;
        border: 1px solid rgba(255, 255, 255, 0.8);
        max-width: 100%;
        overflow-x: auto;
    }

    .nav-btn-premium {
        padding: 0.9rem 1.75rem;
        border-radius: 16px;
        color: var(--slate-muted);
        font-weight: 800;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        background: transparent;
        white-space: nowrap;
        text-decoration: none !important;
    }

    .nav-btn-premium:hover {
        background: #f8fafc;
        color: var(--slate-dark);
    }

    .nav-btn-premium.active {
        background: var(--primary-gradient);
        color: white;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    /* Loader */
    .ledger-loader-container {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(8px);
        z-index: 1000;
        display: none;
        align-items: center;
        justify-content: center;
        border-radius: 30px;
    }

    .ledger-loader-container.active {
        display: flex;
    }

    .btn-premium {
        border-radius: 10px; /* More compact curves */
        padding: 0.45rem 1rem; /* Sleeker padding */
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.7rem; /* Smaller premium text */
        letter-spacing: 0.5px;
        transition: all 0.3s;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
</style>

<div style="height: 35px;"></div>

<div class="container-fluid px-5">
    <?php $this->load->view('admin/warehouse/components/sop_stepper', ['active_step' => 5]); ?>
    <!-- Stats Overview -->
    <div class="stats-container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="metric-icon" style="background: var(--info-gradient);">
                        <i class="fa fa-exchange-alt"></i>
                    </div>
                    <div class="metric-info">
                        <p>Aktivitas Audit Log</p>
                        <h3>
                            <?= number_format(count($logs)) ?> <small class="text-muted"
                                style="font-size: 0.8rem;">Log</small>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="metric-icon" style="background: var(--success-gradient);">
                        <i class="fa fa-warehouse"></i>
                    </div>
                    <div class="metric-info">
                        <p>Gudang Aktif</p>
                        <h3>
                            <?= count($warehouses) ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="metric-icon" style="background: var(--warning-gradient);">
                        <i class="fa fa-history"></i>
                    </div>
                    <div class="metric-info">
                        <p>Periode Audit</p>
                        <h3>
                            <?= date('M Y') ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Warehouse Selector -->
    <div class="selector-area">
        <div class="warehouse-nav-container">
            <button class="nav-btn-premium tab-btn <?= $current_gudang == 0 ? 'active' : '' ?>" data-id="0">
                <i class="fas fa-globe-asia"></i> Semua Gudang (Rekap)
            </button>
            <?php foreach ($warehouses as $wh): ?>
                <button class="nav-btn-premium tab-btn <?= $current_gudang == $wh->gudang_id ? 'active' : '' ?>"
                    data-id="<?= $wh->gudang_id ?>">
                    <i class="fas fa-warehouse"></i> <?= $wh->gudang_nama ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
    </div>

    <!-- Dynamic Content Section -->
    <div id="ledger-wrapper" style="position: relative; min-height: 500px; margin-bottom: 3rem;">
        <div class="ledger-loader-container" id="ledgerLoader">
            <div class="text-center">
                <div class="spinner-border text-primary" style="width: 3.5rem; height: 3.5rem; border-width: 6px;">
                </div>
                <div class="mt-3 fw-900 text-dark text-uppercase small" style="letter-spacing: 2px;">Menyinkronkan Data
                    Ledger...</div>
            </div>
        </div>

        <div id="ledger-content">
            <?php $this->load->view('admin/warehouse/ledger_content'); ?>
        </div>
    </div>
</div>

<!-- Modal Manual Adjustment -->
<div class="modal fade" id="modalAdjustment" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 28px; overflow: hidden;">
            <div class="modal-header px-4 py-4" style="background: var(--primary-gradient); color: white;">
                <h5 class="modal-title fw-900"><i class="fas fa-layer-group me-2"></i>Penyesuaian Stok Manual</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAdjustment">
                <div class="modal-body p-4 bg-white">
                    <input type="hidden" name="gudang_id" value="<?= $current_gudang ?>">

                    <div class="mb-4">
                        <label class="form-label fw-800 text-muted small text-uppercase mb-2">1. Pilih Material /
                            Barang</label>
                        <select name="material_id" class="form-select select2-material" style="width: 100%;" required>
                            <option value="">Cari nama bahan atau varian...</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-800 text-muted small text-uppercase mb-2">2. Masukkan Stok Aktual
                            Saat
                            Ini</label>
                        <div class="input-group input-group-lg overflow-hidden"
                            style="border: 2px solid #f1f5f9; border-radius: 16px;">
                            <span class="input-group-text bg-light border-0 text-primary fw-900"><i
                                    class="fas fa-calculator"></i></span>
                            <input type="number" step="0.001" name="qty_aktual"
                                class="form-control border-0 fw-900 ps-3" placeholder="0.000" style="background: white;"
                                required>
                            <span class="input-group-text bg-white border-0 text-muted fw-bold pe-3"
                                id="label-satuan">SATUAN</span>
                        </div>
                        <div class="small text-danger mt-2 fw-bold italic">&bull; Sistem akan menghitung deviasi stok
                            secara otomatis.</div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-800 text-muted small text-uppercase mb-2">3. Catatan Internal /
                            Alasan</label>
                        <textarea name="catatan" class="form-control border-0 bg-light p-3" rows="3"
                            placeholder="Contoh: Penyesuaian stok fisik periodik..."
                            style="border-radius: 16px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light btn-premium px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-premium btn-premium-primary px-4 btn-save-adjustment">
                        <i class="fas fa-save me-2"></i> Update Stok Aktual
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
    rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        function initSelect2() {
            $('.select2-material').select2({
                dropdownParent: $('#modalAdjustment'),
                theme: 'bootstrap-5',
                placeholder: 'Cari Material...',
                ajax: {
                    url: '<?= base_url('warehouse/get_material_list_json') ?>',
                    dataType: 'json',
                    delay: 250,
                    data: (params) => ({ q: params.term }),
                    processResults: (data) => ({ results: data }),
                    cache: true
                }
            });
        }
        initSelect2();

        $('.select2-material').on('select2:select', function (e) {
            $('#label-satuan').text(e.params.data.satuan || 'UNIT');
        });

        function loadLedger(id) {
            $('#ledgerLoader').addClass('active');
            $('.tab-btn').removeClass('active');
            $(`.tab-btn[data-id="${id}"]`).addClass('active');

            $.ajax({
                url: `<?= base_url('warehouse/ledger/') ?>${id}`,
                type: 'GET',
                success: function (html) {
                    $('#ledger-content').fadeOut(100, function () {
                        $(this).html(html).fadeIn(100);
                        $('#ledgerLoader').removeClass('active');
                        $('input[name="gudang_id"]').val(id);
                    });
                    window.history.pushState({ id: id }, 'Stock Ledger', `<?= base_url('warehouse/ledger/') ?>${id}`);
                },
                error: function () {
                    $('#ledgerLoader').removeClass('active');
                    Swal.fire('Error', 'Gagal mensinkronisasi data audit.', 'error');
                }
            });
        }

        $('.tab-btn').click(function () { loadLedger($(this).data('id')); });
        $(document).on('click', '.btn-refresh-ledger', function () { loadLedger($('.tab-btn.active').data('id')); });

        $('#formAdjustment').submit(function (e) {
            e.preventDefault();
            const btn = $('.btn-save-adjustment');
            const originalVal = btn.html();

            Swal.fire({
                title: 'Konfirmasi Audit Stok',
                text: "Anda akan menyesuaikan stok sistem dengan angka aktual. Lanjutkan?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1a1c23',
                confirmButtonText: 'Ya, Audit Stok',
                cancelButtonText: 'Review Lagi'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Processing...');
                    $.ajax({
                        url: '<?= base_url('warehouse/actionAdjustmentStok') ?>',
                        type: 'POST',
                        data: $(this).serialize(),
                        dataType: 'json',
                        success: function (res) {
                            if (res.success) {
                                Swal.fire('Tersimpan', res.message, 'success');
                                $('#modalAdjustment').modal('hide');
                                $('#formAdjustment')[0].reset();
                                $('.select2-material').val(null).trigger('change');
                                loadLedger($('.tab-btn.active').data('id'));
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        },
                        complete: () => btn.prop('disabled', false).html(originalVal)
                    });
                }
            });
        });
    });

    function viewMaterialHistory(material_id, gudang_id, bahan_nama, varian) {
        let displayTitle = bahan_nama;
        if (varian && bahan_nama.toLowerCase().trim() !== varian.toLowerCase().trim()) {
            displayTitle += ' - ' + varian;
        }
        $('#historyMaterialName').text(displayTitle);
        $('#historyTableBody').html('<tr><td colspan="7" class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-primary mb-2"></i><br>Loading history...</td></tr>');
        $('#modalMaterialHistory').modal('show');

        $.ajax({
            url: '<?= base_url('warehouse/ajax_get_material_history') ?>',
            type: 'GET',
            data: { material_id: material_id, gudang_id: gudang_id },
            dataType: 'json',
            success: function(res) {
                if(res.status === 'success') {
                    let html = '';
                    if(res.data.length === 0) {
                        html = '<tr><td colspan="7" class="text-center py-4 text-muted">Belum ada history pergerakan.</td></tr>';
                    } else {
                        res.data.forEach(function(row) {
                            let badgeStyle = 'bg-light text-dark';
                            if (row.trx_type === 'Pembelian') badgeStyle = 'bg-soft-success text-success';
                            else if (row.trx_type === 'Pengambilan') badgeStyle = 'bg-soft-danger text-danger';
                            else if (row.trx_type === 'Produksi') badgeStyle = 'bg-soft-warning text-warning';
                            else if (row.trx_type === 'Opname') badgeStyle = 'bg-soft-danger text-danger';
                            else if (row.trx_type && row.trx_type.indexOf('Mutation') !== -1) badgeStyle = 'bg-soft-blue text-primary';

                            html += `
                                <tr>
                                    <td class="text-nowrap" style="font-size:0.75rem">${row.tanggal}</td>
                                    <td><span class="badge-premium ${badgeStyle}" style="font-size:0.65rem">${row.trx_type || 'SYS'}</span></td>
                                    <td class="text-success text-nowrap">
                                        <div class="fw-bold">${row.qty_in > 0 ? '+' + row.qty_in_base_fmt : '-'}</div>
                                        ${row.qty_in > 0 ? `<div class="small opacity-75" style="font-size:0.65rem;">+${row.qty_in_fmt}</div>` : ''}
                                    </td>
                                    <td class="text-danger text-nowrap">
                                        <div class="fw-bold">${row.qty_out > 0 ? '-' + row.qty_out_base_fmt : '-'}</div>
                                        ${row.qty_out > 0 ? `<div class="small opacity-75" style="font-size:0.65rem;">-${row.qty_out_fmt}</div>` : ''}
                                    </td>
                                    <td class="bg-light text-nowrap">
                                        <div class="fw-bold">${row.qty_balance_base_fmt}</div>
                                        <div class="small text-muted" style="font-size:0.65rem;">${row.qty_balance_fmt}</div>
                                    </td>
                                    <td style="font-size:0.75rem">${row.catatan || '-'}</td>
                                    <td><span class="badge-premium bg-soft-blue text-primary px-2" style="font-size:0.65rem">${row.username || 'SYS'}</span></td>
                                </tr>
                            `;
                        });
                    }
                    $('#historyTableBody').html(html);
                } else {
                    $('#historyTableBody').html('<tr><td colspan="7" class="text-center py-4 text-danger">Gagal memuat data: ' + res.message + '</td></tr>');
                }
            },
            error: function() {
                $('#historyTableBody').html('<tr><td colspan="7" class="text-center py-4 text-danger">Terjadi kesalahan koneksi.</td></tr>');
            }
        });
    }
</script>

<!-- Modal Material History -->
<div class="modal fade" id="modalMaterialHistory" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom px-4 py-3 bg-light">
                <h5 class="modal-title fw-800 text-dark">
                    <i class="fas fa-history text-primary me-2"></i> History Pergerakan: <span id="historyMaterialName" class="text-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="bg-light px-4 py-2 border-bottom d-flex flex-wrap gap-3" style="font-size: 0.7rem;">
                    <div class="fw-bold text-muted me-2"><i class="fas fa-info-circle me-1"></i> LEGEND TRX:</div>
                    <div><span class="badge-premium bg-soft-success text-success py-1 px-2">Pembelian</span> Barang masuk dari PO</div>
                    <div><span class="badge-premium bg-soft-blue text-primary py-1 px-2">Mutation</span> Perpindahan antar gudang</div>
                    <div><span class="badge-premium bg-soft-danger text-danger py-1 px-2">Opname</span> / <span class="badge-premium bg-soft-danger text-danger py-1 px-2">Adjustment</span> Penyesuaian stok manual</div>
                    <div><span class="badge-premium bg-soft-warning text-warning py-1 px-2">Produksi</span> Barang digunakan produksi</div>
                    <div><span class="badge-premium bg-light text-dark py-1 px-2 border">SYS</span> Transaksi sistem (Misal: Revert Invoice)</div>
                </div>
                <div class="table-responsive">
                    <table class="table table-premium table-hover align-middle mb-0">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th class="text-nowrap px-4">TANGGAL</th>
                                <th>TIPE TRX</th>
                                <th>IN</th>
                                <th>OUT</th>
                                <th>BALANCE</th>
                                <th>CATATAN / REF</th>
                                <th class="px-4">USER</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                            <!-- Data will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light px-4 border-top">
                <button type="button" class="btn btn-premium btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('admin/warehouse/components/v_modal_ledger_guide'); ?>