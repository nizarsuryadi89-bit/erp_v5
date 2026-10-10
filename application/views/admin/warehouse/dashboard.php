<style>
    /* ==========================================================================
    PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - WAREHOUSE DASHBOARD
    ========================================================================== */
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
        --pulse-red: rgba(247, 37, 133, 0.4);
    }

    @keyframes pulse-live {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 var(--pulse-red);
        }

        70% {
            transform: scale(1);
            box-shadow: 0 0 0 10px rgba(247, 37, 133, 0);
        }

        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(247, 37, 133, 0);
        }
    }

    .badge-live-pulse {
        background: #f72585 !important;
        color: white !important;
        animation: pulse-live 2s infinite;
        font-size: 0.6rem !important;
        letter-spacing: 1px;
    }

    /* NEW Feature Badge */
    @keyframes glow-new {

        0%,
        100% {
            box-shadow: 0 0 6px rgba(99, 102, 241, 0.6), 0 0 0 0 rgba(99, 102, 241, 0.4);
        }

        50% {
            box-shadow: 0 0 12px rgba(99, 102, 241, 0.9), 0 0 8px rgba(99, 102, 241, 0.3);
        }
    }

    .badge-new-feature {
        display: inline-block;
        background: linear-gradient(135deg, #6366f1, #4338ca);
        color: white !important;
        font-size: 0.62rem !important;
        font-weight: 900;
        letter-spacing: 1px;
        padding: 3px 10px;
        border-radius: 20px;
        text-transform: uppercase;
        animation: glow-new 2s ease-in-out infinite;
        white-space: nowrap;
    }


    body {
        background-color: #f8fafc;
        /* font-family: 'Inter', system-ui, -apple-system, sans-serif; REMOVED TO PREVENT SIDEBAR FONT CHANGE */
    }

    /* Executive Hero Header */
    .warehouse-dashboard {
        background: var(--primary-gradient);
        color: white;
        padding: 1.5rem 0 3.25rem 0;
        /* Compact padding */
        margin-bottom: 0;
        border-radius: 0 0 24px 24px;
        /* Tighter curves */
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        position: relative;
        overflow: hidden;
    }

    .warehouse-dashboard::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
        border-radius: 50%;
        z-index: 1;
    }

    .dashboard-content {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        position: relative;
        z-index: 2;
    }

    .dashboard-icon {
        width: 52px;
        /* Compact icon container */
        height: 52px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        color: white;
    }

    .dashboard-title {
        font-size: 1.75rem;
        /* Smaller premium title */
        font-weight: 900;
        margin: 0;
        letter-spacing: -0.5px;
        line-height: 1.2;
    }

    .dashboard-subtitle {
        font-size: 0.85rem;
        /* Compact subtitle */
        font-weight: 700;
        margin: 0 0 2px 0;
        opacity: 0.7;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .dashboard-subtitle::before {
        content: '';
        width: 12px;
        height: 2px;
        background: var(--accent-blue);
        display: inline-block;
    }

    .btn-premium {
        border-radius: 10px;
        /* More compact curves */
        padding: 0.45rem 1rem;
        /* Sleeker padding */
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.7rem;
        /* Smaller premium text */
        letter-spacing: 0.5px;
        transition: all 0.3s;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    /* Metric Cards */
    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.75rem;
        margin-top: -2.5rem;
        /* Compact alignment */
        position: relative;
        z-index: 10;
        padding-bottom: 2.5rem;
    }

    .stat-card {
        background: white;
        border-radius: 24px;
        padding: 1.75rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid rgba(255, 255, 255, 0.8);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        border-color: var(--accent-blue);
    }

    .stat-header {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        margin-bottom: 0.5rem;
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
    }

    .stat-icon.warehouse {
        background: var(--primary-gradient);
    }

    .stat-icon.materials {
        background: var(--info-gradient);
    }

    .stat-icon.low-stock {
        background: var(--danger-gradient);
    }

    .stat-icon.mutations {
        background: var(--success-gradient);
    }

    .stat-info h3 {
        font-size: 2.25rem;
        font-weight: 900;
        margin: 0;
        color: var(--slate-dark);
        line-height: 1;
    }

    .stat-info p {
        font-size: 0.8rem;
        color: var(--slate-muted);
        margin: 0;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Quick Actions Area */
    .quick-actions {
        background: white;
        border-radius: 28px;
        padding: 2rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid #f1f5f9;
        margin-bottom: 2.5rem;
    }

    .quick-actions h4 {
        color: var(--slate-dark);
        font-weight: 900;
        margin-bottom: 1.5rem;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .action-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1.25rem;
    }

    .action-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        padding: 1.5rem 1rem;
        background: #f8fafc;
        color: var(--slate-dark);
        text-decoration: none;
        border-radius: 20px;
        font-weight: 800;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #e2e8f0;
        text-align: center;
        font-size: 0.85rem;
    }

    .action-btn i {
        font-size: 1.75rem;
        transition: transform 0.3s;
    }

    .action-btn:hover {
        background: white;
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.08);
        border-color: var(--accent-blue);
        color: var(--accent-blue);
    }

    .action-btn:hover i {
        transform: scale(1.1);
    }

    /* Warehouse Grid */
    .warehouse-card {
        background: white;
        border-radius: 24px;
        padding: 1.75rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid #f1f5f9;
        margin-bottom: 1.5rem;
        transition: border-color 0.3s;
    }

    .warehouse-card:hover {
        border-color: var(--accent-blue);
    }

    .warehouse-header {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1.25rem;
        border-bottom: 1px dashed #e2e8f0;
    }

    .warehouse-icon {
        width: 48px;
        height: 48px;
        background: var(--slate-soft);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--accent-blue);
        font-size: 1.25rem;
        border: 1px solid #f1f5f9;
    }

    .warehouse-info h5 {
        margin: 0;
        font-weight: 800;
        color: var(--slate-dark);
        font-size: 1.15rem;
    }

    .warehouse-info p {
        margin: 0;
        color: var(--slate-muted);
        font-size: 0.8rem;
        font-weight: 600;
    }

    .warehouse-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }

    .warehouse-stat {
        text-align: center;
        padding: 1rem 0.5rem;
        background: var(--slate-soft);
        border-radius: 16px;
        border: 1px solid #f1f5f9;
    }

    .warehouse-stat h6 {
        margin: 0 0 4px 0;
        font-weight: 900;
        color: var(--slate-dark);
        font-size: 1.1rem;
    }

    .warehouse-stat p {
        margin: 0;
        color: var(--slate-muted);
        font-size: 0.65rem;
        text-transform: uppercase;
        font-weight: 800;
        letter-spacing: 0.5px;
    }

    .btn-detail-warehouse {
        width: 100%;
        border-radius: 14px;
        padding: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
    }

    /* Mutation Logs */
    .recent-mutations {
        background: white;
        border-radius: 28px;
        padding: 2rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid #f1f5f9;
    }

    .recent-mutations h4 {
        color: var(--slate-dark);
        font-weight: 900;
        margin-bottom: 1.5rem;
        font-size: 1.25rem;
    }

    .mutation-item {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        padding: 1.25rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .mutation-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .mutation-icon.masuk {
        background: #ecfdf5;
        color: #10b981;
    }

    .mutation-icon.keluar {
        background: #fff1f2;
        color: #f43f5e;
    }

    .mutation-icon.transfer {
        background: #eff6ff;
        color: #3b82f6;
    }

    .mutation-info h6 {
        margin: 0 0 2px 0;
        font-weight: 800;
        color: var(--slate-dark);
        font-size: 0.95rem;
    }

    .mutation-info p {
        margin: 0;
        color: var(--slate-muted);
        font-size: 0.75rem;
        font-weight: 600;
    }

    .mutation-qty {
        margin-left: auto;
        text-align: right;
    }

    .mutation-qty h6 {
        margin: 0;
        font-weight: 900;
        color: var(--slate-dark);
        font-size: 1.1rem;
    }

    /* Alerts */
    .low-stock-alert {
        background: #fffbeb;
        border: 1px solid #fef3c7;
        border-radius: 16px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .low-stock-alert i {
        font-size: 1.5rem;
        color: #f59e0b;
    }

    .low-stock-alert h6 {
        margin: 0;
        color: #92400e;
        font-weight: 800;
        font-size: 0.9rem;
    }

    .low-stock-alert p {
        margin: 2px 0 0 0;
        color: #b45309;
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* ==========================================================================
       MOBILE RESPONSIVE OVERRIDES
       ========================================================================== */
    @media (max-width: 768px) {
        .warehouse-dashboard {
            padding: 2.5rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 25px 25px;
        }

        .dashboard-content {
            flex-direction: column;
            text-align: center;
            gap: 1.25rem;
        }

        .dashboard-icon {
            width: 70px;
            height: 70px;
            font-size: 2.2rem;
            border-radius: 20px;
        }

        .dashboard-title {
            font-size: 1.75rem;
            margin-bottom: 0.25rem;
        }

        .dashboard-subtitle {
            justify-content: center;
            font-size: 1rem;
        }

        .stats-overview {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-top: -3.5rem;
            padding: 0 10px 1.5rem 10px;
        }

        .stat-card {
            padding: 1.25rem;
            border-radius: 20px;
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            font-size: 1.2rem;
        }

        .stat-info h3 {
            font-size: 1.6rem;
        }

        .stat-info p {
            font-size: 0.65rem;
        }

        .quick-actions {
            padding: 1.5rem;
            border-radius: 24px;
        }

        .action-buttons {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }

        .action-btn {
            padding: 1.25rem 0.5rem;
            border-radius: 18px;
        }

        .action-btn i {
            font-size: 1.5rem;
        }

        /* Modal Scanner Fixes */
        #modalOmniScanner .modal-body {
            padding: 2rem 1rem !important;
        }

        #manual_barcode {
            font-size: 1.25rem !important;
            height: 50px !important;
        }
    }

    @media (max-width: 576px) {
        .warehouse-header-row {
            flex-direction: column;
            gap: 1.5rem;
        }

        .header-actions-mobile {
            width: 100%;
            justify-content: stretch !important;
            flex-direction: column;
        }

        .header-actions-mobile .btn {
            width: 100%;
        }

        .stats-overview {
            grid-template-columns: repeat(2, 1fr);
        }

        .btn-premium {
            border-radius: 14px;
            padding: 0.6rem 1.25rem;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
</style>

<script>
    $(document).on('click', '.btn-sync-stock', function () {
        Swal.fire({
            title: 'Sinkronisasi Stok?',
            text: "Sistem akan menghitung ulang saldo stok berdasarkan audit log. Proses ini mungkin memakan waktu.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            confirmButtonText: 'Ya, Jalankan Sync'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    didOpen: () => { Swal.showLoading(); }
                });

                $.ajax({
                    url: '<?= base_url('warehouse/ajax_sync_stock') ?>',
                    type: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        Swal.fire('Berhasil', res.message, 'success').then(() => {
                            location.reload();
                        });
                    },
                    error: function () {
                        Swal.fire('Error', 'Terjadi kesalahan saat sinkronisasi.', 'error');
                    }
                });
            }
        });
    }); // End of previous document.ready

    // --- OMNI-SCANNER LOGIC (Wrapped for safety) ---
    $(document).ready(function () {
        console.log("Omni-Scanner Module Loaded"); // Debug check

        let html5QrCode = null;
        $('.btn-omni-scanner').click(function () {
            $('#modalOmniScanner').modal('show');
            $('#scan-result').hide();
            $('#hardware-scanner-area').show();
            setTimeout(() => $('#manual_barcode').focus(), 500);
        });

        $('#manual_barcode').on('keypress', function (e) {
            if (e.which == 13) {
                const barcode = $(this).val();
                lookupBarcode(barcode);
                $(this).val('');
            }
        });

        // Expose function to global scope
        window.lookupBarcode = async function (barcode) {
            try {
                const res = await $.getJSON('<?= base_url('warehouse/ajax_lookup_barcode') ?>', { barcode });
                console.log("Barcode result:", res);
                Swal.close();

                if (res.success) {
                    const item = res.data;
                    $('#res-item-name').text(item.item_name);
                    $('#res-barcode').text(item.barcode);
                    $('#res-unit').text(item.unit);

                    // Stock Grid
                    let stockHtml = '';
                    item.stock.forEach(s => {
                        stockHtml += `
                                <div class="col-6">
                                    <div class="p-3 border rounded-3 bg-light">
                                        <span class="small text-muted fw-bold d-block text-uppercase">${s.gudang_nama}</span>
                                        <h5 class="fw-900 mb-0 mt-1">${parseFloat(s.qty).toFixed(2)}</h5>
                                    </div>
                                </div>
                            `;
                    });
                    $('#res-stock-grid').html(stockHtml || '<div class="col-12 text-center text-muted">Stok tidak terekam</div>');

                    // Action Buttons
                    $('#btn-quick-purchase').attr('href', `<?= base_url('warehouse/purchase') ?>?material_id=${item.material_id}`);
                    $('#btn-quick-dispatch').attr('href', `<?= base_url('warehouse/dispatch') ?>?material_id=${item.material_id}`);
                    $('#btn-quick-opname').attr('href', `<?= base_url('warehouse/opname') ?>?material_id=${item.material_id}`);

                    $('#hardware-scanner-area').fadeOut(200, () => $('#scan-result').fadeIn());
                    if (html5QrCode) { stopCamera(); }
                } else {
                    Swal.fire({
                        title: 'Tidak Ditemukan',
                        text: res.message,
                        icon: 'error',
                        confirmButtonColor: '#1a1c23'
                    });
                }
            } catch (e) {
                console.error(e);
                Swal.fire('Error', 'Koneksi gagal. Cek konsol.', 'error');
            }
        };

        $('#toggleCamera').click(function () {
            if ($('#reader').is(':visible')) {
                stopCamera();
            } else {
                startCamera();
            }
        });

        function startCamera() {
            $('#hardware-scanner-area').hide();
            $('#reader').show();
            $('#scan-result').hide();

            html5QrCode = new Html5Qrcode("reader");
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            html5QrCode.start({ facingMode: "environment" }, config, (decodedText) => {
                lookupBarcode(decodedText);
            });

            $('#toggleCamera').html('<i class="fa fa-keyboard me-2"></i>Gunakan Scanner Hardware');
        }

        function stopCamera() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    $('#reader').hide();
                    $('#hardware-scanner-area').show();
                    $('#toggleCamera').html('<i class="fa fa-camera me-2"></i>Aktifkan Scanner Kamera');
                    setTimeout(() => $('#manual_barcode').focus(), 200);
                });
            }
        }

        $('#modalOmniScanner').on('hidden.bs.modal', function () {
            if (html5QrCode) stopCamera();
        });

        // Remove redundant toggle logic if not used, or keep for future
    });
</script>
<style>
    /* Ensure scanner buttons are always clickable */
    .btn-omni-scanner,
    .toggle-simulation,
    .simulation-area button {
        position: relative;
        z-index: 1056 !important;
        /* Higher than bootstrap modal default */
        cursor: pointer;
    }

    /* Ensure manual input receives focus properly */
    #manual_barcode:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2) !important;
    }
</style>
<script src="https://unpkg.com/html5-qrcode"></script>
<div class="container-fluid px-4">
    <!-- ================= DASHBOARD HEADER ================= -->
    <div class="warehouse-dashboard">
        <div class="container-fluid px-5">
            <div class="row align-items-center warehouse-header-row">
                <div class="col-md-8">
                    <div class="dashboard-content">
                        <div class="dashboard-icon">
                            <i class="fa fa-warehouse"></i>
                        </div>
                        <div class="dashboard-text">
                            <!-- <p class="dashboard-subtitle">Pusat Kendali</p> -->
                            <h1 class="dashboard-title">Manajemen Gudang - TGM</h1>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end d-flex gap-2 justify-content-end header-actions-mobile">
                    <button class="btn btn-outline-light btn-premium rounded-pill px-4 shadow-sm" data-bs-toggle="modal"
                        data-bs-target="#modalWarehouseGuide">
                        <i class="fa fa-info-circle me-2"></i>BANTUAN/SOP
                    </button>
                    <button class="btn btn-light btn-premium rounded-pill px-6 shadow-sm btn-omni-scanner">
                        <i class="fa fa-expand text-primary me-2"></i>SMART SCANNER
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- OMNI-SCANNER MODAL -->
    <div class="modal fade" id="modalOmniScanner" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 30px; overflow: hidden;">
                <div class="modal-header p-4 border-0" style="background: var(--primary-gradient); color: white;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 45px; height: 45px;">
                            <i class="fa fa-qrcode text-primary fs-4"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-900 mb-0">Omni-Scanner v2.0</h5>
                            <p class="small mb-0 opacity-75">Deteksi Otomatis Hardware atau Kamera</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-5 text-center bg-light">
                    <!-- Scanner Viewport (For Camera) -->
                    <div id="reader"
                        style="width: 100%; max-width: 500px; margin: 0 auto; border-radius: 20px; overflow: hidden; background: #000; display: none;">
                    </div>

                    <!-- Hardware Scanner Input (Invisible but focusable) -->
                    <div id="hardware-scanner-area">
                        <div class="mb-4">
                            <i class="fa fa-barcode fa-5x text-muted opacity-25 mb-4"></i>
                            <h4 class="fw-800 text-dark">Siap Memindai</h4>
                            <p class="text-muted">Gunakan scanner barcode fisik Anda atau aktifkan kamera di bawah.</p>
                        </div>
                        <input type="text" id="manual_barcode" class="form-control form-control-lg text-center fw-900"
                            placeholder="WAID-XXXX-XXXX"
                            style="border-radius: 15px; border: 2px solid #ddd; height: 60px; font-size: 1.5rem;">

                    </div>

                    <!-- Result Panel (Hidden initially) -->
                    <div id="scan-result" class="mt-4 text-start" style="display: none;">
                        <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background: white;">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h3 class="fw-900 text-primary mb-1" id="res-item-name">-</h3>
                                    <p class="badge bg-soft-blue text-primary px-3 py-1 fw-bold fs-7 mb-0"
                                        id="res-barcode">-</p>
                                </div>
                                <div class="text-end">
                                    <span class="text-muted small fw-bold text-uppercase d-block">Satuan</span>
                                    <h4 class="fw-900 mb-0" id="res-unit">-</h4>
                                </div>
                            </div>

                            <hr class="opacity-10">

                            <div class="row g-3" id="res-stock-grid">
                                <!-- Stock levels will be injected here -->
                            </div>

                            <div class="mt-4 pt-3 border-top d-flex flex-wrap gap-2">
                                <a href="#" id="btn-quick-purchase" class="btn btn-primary btn-premium flex-grow-1">
                                    <i class="fa fa-shopping-cart me-2"></i>Isi Stok
                                </a>
                                <a href="#" id="btn-quick-dispatch"
                                    class="btn btn-info text-white btn-premium flex-grow-1">
                                    <i class="fa fa-paper-plane me-2"></i>Kirim
                                </a>
                                <a href="#" id="btn-quick-opname"
                                    class="btn btn-warning text-white btn-premium flex-grow-1">
                                    <i class="fa fa-check-double me-2"></i>Opname
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-0 p-4">
                    <button class="btn btn-outline-primary btn-premium rounded-pill w-100" id="toggleCamera">
                        <i class="fa fa-camera me-2"></i>Aktifkan Scanner Kamera
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid px-4">
        <!-- ================= HIGH-LEVEL STATS ================= -->
        <div class="stats-overview">
            <div class="stat-card" data-bs-toggle="modal" data-bs-target="#modalTodayTransactions"
                onclick="loadTodayTransactions('inbound')" style="cursor: pointer;">
                <div class="stat-header">
                    <div class="stat-icon warehouse" style="background: var(--info-gradient);"><i
                            class="fa fa-truck-loading"></i></div>
                    <div class="stat-info">
                        <h3 id="stat-inbound-today">0</h3>
                        <p>Barang Masuk (Hari Ini)</p>
                    </div>
                </div>
            </div>
            <div class="stat-card" data-bs-toggle="modal" data-bs-target="#modalTodayTransactions"
                onclick="loadTodayTransactions('outbound')" style="cursor: pointer;">
                <div class="stat-header">
                    <div class="stat-icon materials" style="background: var(--primary-gradient);"><i
                            class="fa fa-paper-plane"></i></div>
                    <div class="stat-info">
                        <h3 id="stat-outbound-today">0</h3>
                        <p>Barang Keluar (Hari Ini)</p>
                    </div>
                </div>
            </div>
            <div class="stat-card" onclick="location.href='<?= base_url('warehouse/laporan_stok_kritis') ?>'"
                style="cursor: pointer;">
                <div class="stat-header">
                    <div class="stat-icon low-stock"><i class="fa fa-exclamation-triangle"></i></div>
                    <div class="stat-info">
                        <h3 id="stat-low-stock">0</h3>
                        <p>Stok Kritis</p>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon mutations"><i class="fa fa-sync-alt"></i></div>
                    <div class="stat-info">
                        <h3 id="stat-mutations">0</h3>
                        <p>Mutasi Hari Ini</p>
                    </div>
                </div>
            </div>
            <div class="stat-card" onclick="location.href='<?= base_url('warehouse/requests') ?>'"
                style="cursor: pointer;">
                <div class="stat-header">
                    <div class="stat-icon bg-danger"><i class="fa fa-file-signature"></i></div>
                    <div class="stat-info">
                        <h3 id="stat-pending-requests">0</h3>
                        <p>Pengajuan Pending</p>
                    </div>
                </div>
            </div>
            <div class="stat-card" onclick="location.href='<?= base_url('warehouse/audit_log') ?>'"
                style="cursor: pointer;">
                <div class="stat-header">
                    <div class="stat-icon bg-secondary text-white"><i class="fa fa-history"></i></div>
                    <div class="stat-info">
                        <h3>Audit</h3>
                        <p>Rekam Jejak & Log</p>
                    </div>
                </div>
            </div>
            <div class="stat-card" onclick="location.href='<?= base_url('warehouse/laporan_pembelian') ?>'"
                style="cursor: pointer;">
                <div class="stat-header">
                    <div class="stat-icon" style="background: var(--success-gradient); color: white;"><i
                            class="fa fa-money-bill-wave"></i></div>
                    <div class="stat-info">
                        <h3 id="stat-monthly-spending" style="font-size: 1.2rem;">Rp 0</h3>
                        <p>Total Belanja Bulan Ini</p>
                    </div>
                </div>
            </div>
            <div class="stat-card" onclick="location.href='<?= base_url('warehouse/stock_report') ?>'"
                style="cursor: pointer;">
                <div class="stat-header">
                    <div class="stat-icon" style="background: var(--warning-gradient); color: white;"><i
                            class="fa fa-boxes"></i></div>
                    <div class="stat-info">
                        <h3 id="stat-inventory-value" style="font-size: 1.2rem;">Rp 0</h3>
                        <p>Nilai Aset Gudang</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= PRODUCT REGISTRATION WORKFLOW ================= -->
        <div class="registration-workflow mb-5">
            <div class="card border-0 shadow-sm overflow-hidden"
                style="border-radius: 28px; background: white; border: 1px solid var(--border-color) !important;">
                <div class="row g-0">
                    <div class="col-lg-3 p-4 d-flex flex-column justify-content-center text-white"
                        style="background: #334155;">
                        <h4 class="fw-900 mb-2 mt-0"><i class="fa fa-clipboard-check text-warning me-2"></i>SOP HARIAN
                        </h4>
                        <p class="small opacity-75 mb-0" style="line-height: 1.5;">Ikuti 6 rutinitas ini setiap hari
                            (Pagi - Sore) untuk memastikan tidak ada selisih stok gudang.</p>
                    </div>
                    <div class="col-lg-9 p-0 bg-white">
                        <div class="row g-0 h-100">
                            <!-- STEP 1 -->
                            <div class="col border-end position-relative">
                                <a href="<?= base_url('warehouse/requests') ?>"
                                    class="text-decoration-none p-3 d-block h-100 workflow-step">
                                    <div
                                        class="text-center position-relative h-100 d-flex flex-column justify-content-center">
                                        <div class="step-number mx-auto mb-2"
                                            style="background:#fee2e2; color:#ef4444; border: 2px solid #fca5a5;">1
                                        </div>
                                        <h6 class="fw-900 mb-1 small" style="color: #ef4444;">PENGAJUAN</h6>
                                        <p class="text-muted mb-0" style="font-size: 0.6rem;">Validasi Permintaan</p>
                                        <i class="fa fa-chevron-right position-absolute top-50 end-0 translate-middle-y text-muted opacity-25 me-2"
                                            style="font-size: 0.7rem;"></i>
                                    </div>
                                </a>
                            </div>
                            <!-- STEP 2 -->
                            <div class="col border-end position-relative">
                                <a href="<?= base_url('warehouse/laporan_stok_kritis') ?>"
                                    class="text-decoration-none p-3 d-block h-100 workflow-step">
                                    <div
                                        class="text-center position-relative h-100 d-flex flex-column justify-content-center">
                                        <div class="step-number mx-auto mb-2"
                                            style="background:#fef3c7; color:#f59e0b; border: 2px solid #fde68a;">2
                                        </div>
                                        <h6 class="fw-900 mb-1 small" style="color: #f59e0b;">STOK KRITIS</h6>
                                        <p class="text-muted mb-0" style="font-size: 0.6rem;">Cek Batas Minimum</p>
                                        <i class="fa fa-chevron-right position-absolute top-50 end-0 translate-middle-y text-muted opacity-25 me-2"
                                            style="font-size: 0.7rem;"></i>
                                    </div>
                                </a>
                            </div>
                            <!-- STEP 3 -->
                            <div class="col border-end position-relative">
                                <a href="<?= base_url('warehouse/purchase') ?>"
                                    class="text-decoration-none p-3 d-block h-100 workflow-step">
                                    <div
                                        class="text-center position-relative h-100 d-flex flex-column justify-content-center">
                                        <div class="step-number mx-auto mb-2"
                                            style="background:#e0f2fe; color:#0ea5e9; border: 2px solid #bae6fd;">3
                                        </div>
                                        <h6 class="fw-900 mb-1 small" style="color: #0ea5e9;">INBOUND</h6>
                                        <p class="text-muted mb-0" style="font-size: 0.6rem;">Penerimaan Supplier</p>
                                        <i class="fa fa-chevron-right position-absolute top-50 end-0 translate-middle-y text-muted opacity-25 me-2"
                                            style="font-size: 0.7rem;"></i>
                                    </div>
                                </a>
                            </div>
                            <!-- STEP 4 -->
                            <div class="col border-end position-relative">
                                <a href="<?= base_url('warehouse/dispatch') ?>"
                                    class="text-decoration-none p-3 d-block h-100 workflow-step">
                                    <div
                                        class="text-center position-relative h-100 d-flex flex-column justify-content-center">
                                        <div class="step-number mx-auto mb-2"
                                            style="background:#e0e7ff; color:#3b82f6; border: 2px solid #bfdbfe;">4
                                        </div>
                                        <h6 class="fw-900 mb-1 small" style="color: #3b82f6;">OUTBOUND</h6>
                                        <p class="text-muted mb-0" style="font-size: 0.6rem;">Kirim ke Produksi</p>
                                        <i class="fa fa-chevron-right position-absolute top-50 end-0 translate-middle-y text-muted opacity-25 me-2"
                                            style="font-size: 0.7rem;"></i>
                                    </div>
                                </a>
                            </div>
                            <!-- STEP 5 -->
                            <div class="col border-end position-relative">
                                <a href="<?= base_url('warehouse/ledger') ?>"
                                    class="text-decoration-none p-3 d-block h-100 workflow-step">
                                    <div
                                        class="text-center position-relative h-100 d-flex flex-column justify-content-center">
                                        <div class="step-number mx-auto mb-2"
                                            style="background:#f1f5f9; color:#64748b; border: 2px solid #e2e8f0;">5
                                        </div>
                                        <h6 class="fw-900 mb-1 small" style="color: #475569;">KARTU STOK</h6>
                                        <p class="text-muted mb-0" style="font-size: 0.6rem;">Sinkronisasi Akhir</p>
                                        <i class="fa fa-chevron-right position-absolute top-50 end-0 translate-middle-y text-muted opacity-25 me-2"
                                            style="font-size: 0.7rem;"></i>
                                    </div>
                                </a>
                            </div>
                            <!-- STEP 6 -->
                            <div class="col position-relative">
                                <a href="<?= base_url('warehouse/opname') ?>"
                                    class="text-decoration-none p-3 d-block h-100 workflow-step">
                                    <div
                                        class="text-center position-relative h-100 d-flex flex-column justify-content-center">
                                        <div class="step-number mx-auto mb-2"
                                            style="background:#d1fae5; color:#10b981; border: 2px solid #a7f3d0;">6
                                        </div>
                                        <h6 class="fw-900 mb-1 small" style="color: #10b981;">OPNAME</h6>
                                        <p class="text-muted mb-0" style="font-size: 0.6rem;">Cek Fisik Harian</p>
                                        <i class="fa fa-chevron-right position-absolute top-50 end-0 translate-middle-y text-muted opacity-25 me-2"
                                            style="font-size: 0.7rem;"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .registration-workflow .step-number {
                width: 40px;
                height: 40px;
                background: #f1f5f9;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 900;
                color: var(--accent-blue);
                font-size: 1.1rem;
                flex-shrink: 0;
                border: 2px solid #e2e8f0;
                transition: all 0.3s;
            }

            .workflow-step {
                transition: all 0.3s;
                position: relative;
            }

            .workflow-step:hover {
                background: #f8fafc;
            }

            .workflow-step:hover .step-number {
                background: var(--accent-blue);
                color: white;
                border-color: var(--accent-blue);
                transform: scale(1.1);
            }

            .workflow-step::after {
                content: '\f105';
                font-family: 'Font Awesome 5 Free';
                font-weight: 900;
                position: absolute;
                right: 20px;
                top: 50%;
                transform: translateY(-50%);
                color: #e2e8f0;
                font-size: 1.2rem;
            }
        </style>

        <!-- ================= QUICK ACTIONS ================= -->

        <div class="quick-actions">
            <h4><i class="fa fa-directions text-primary me-2"></i>Alur Manajemen Gudang</h4>
            <div class="action-buttons">
                <a href="<?= base_url('warehouse/materials') ?>" class="action-btn">
                    <i class="fa fa-database" style="color: #f59e0b;"></i>
                    <span>1. Master Material</span>
                </a>
                <a href="<?= base_url('warehouse/order') ?>" class="action-btn">
                    <i class="fa fa-shopping-cart" style="color: #6366f1;"></i>
                    <span>2. Dokumen PO</span>
                </a>
                <a href="<?= base_url('warehouse/purchase') ?>" class="action-btn">
                    <i class="fa fa-truck-loading" style="color: #ec4899;"></i>
                    <span>3. Inbound (Penerimaan)</span>
                </a>
                <a href="<?= base_url('warehouse/stock') ?>" class="action-btn">
                    <i class="fa fa-boxes" style="color: #8b5cf6;"></i>
                    <span>4. Penyesuaian Stok</span>
                </a>
                <a href="<?= base_url('warehouse/dispatch') ?>" class="action-btn">
                    <i class="fa fa-paper-plane" style="color: #06b6d4;"></i>
                    <span>5. Outbound (Pengeluaran)</span>
                </a>
                <a href="<?= base_url('warehouse/opname') ?>" class="action-btn">
                    <i class="fa fa-check-double" style="color: #10b981;"></i>
                    <span>6. Stock Opname</span>
                </a>
                <a href="<?= base_url('warehouse/ledger') ?>" class="action-btn">
                    <i class="fa fa-book-open" style="color: #64748b;"></i>
                    <span>7. Kartu Stok</span>
                </a>
                <a href="<?= base_url('warehouse/requests') ?>" class="action-btn">
                    <i class="fa fa-file-signature" style="color: #f43f5e;"></i>
                    <span>8. Pengajuan Material</span>
                </a>
                <a href="<?= base_url('warehouse/stock?gudang_id=1') ?>" class="action-btn">
                    <i class="fa fa-desktop" style="color: #3b82f6;"></i>
                    <span>9. Stok Gudang Utama</span>
                </a>
                <a href="<?= base_url('wip/konsumsi_bahan') ?>" class="action-btn">
                    <i class="fa fa-boxes" style="color: #f97316;"></i>
                    <span>10. Rekap Konsumsi Bahan</span>
                </a>

            </div>
        </div>

        <!-- ================= ADVANCED REPORTS & MAINTENANCE ================= -->
        <div class="quick-actions" style="background: var(--slate-soft); border: 1px dashed var(--accent-blue);">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="m-0"><i class="fa fa-chart-pie text-accent-blue me-2"></i>Laporan Lanjutan & Pemeliharaan
                </h4>
                <span class="badge bg-soft-blue text-primary px-3 py-2 rounded-pill fw-bold"
                    style="font-size: 0.7rem;">FITUR ADMIN</span>
            </div>

            <!-- ✨ NEW ANALYTICS BANNER -->
            <a href="<?= base_url('warehouse/analytics') ?>" style="
                display: flex; align-items: center; justify-content: space-between;
                background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
                border-radius: 18px; padding: 1.25rem 1.75rem;
                text-decoration: none; margin-bottom: 1.25rem;
                border: 1px solid rgba(99,102,241,0.3);
                transition: all 0.3s ease; position: relative; overflow: hidden;
            " onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 15px 35px rgba(99,102,241,0.25)'"
                onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div
                    style="position:absolute;top:-40px;right:-40px;width:150px;height:150px;background:radial-gradient(circle,rgba(99,102,241,0.15) 0%,transparent 70%);border-radius:50%;">
                </div>
                <div style="display:flex;align-items:center;gap:1.25rem;">
                    <div
                        style="width:52px;height:52px;background:rgba(99,102,241,0.2);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;border:1px solid rgba(99,102,241,0.4);">
                        <i class="fa fa-chart-bar" style="color:#818cf8;"></i>
                    </div>
                    <div>
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
                            <span style="color:white;font-size:1rem;font-weight:800;">Pusat Laporan & Analitik
                                Gudang</span>
                            <span class="badge-new-feature">✨ BARU</span>
                        </div>
                        <p style="margin:0;color:#64748b;font-size:0.8rem;">8 laporan: Nilai Inventaris, Stok Kritis,
                            Dead Stock, Tren Pembelian, dan lainnya</p>
                    </div>
                </div>
                <div style="color:#6366f1;font-size:1.5rem;flex-shrink:0;">
                    <i class="fa fa-arrow-right"></i>
                </div>
            </a>
            <div class="action-buttons">
                <a href="<?= base_url('warehouse/stock_report') ?>" class="action-btn" style="background: white;">
                    <i class="fa fa-file-invoice text-primary"></i>
                    <span>Report Akumulasi</span>
                </a>
                <a href="<?= base_url('warehouse/returns') ?>" class="action-btn" style="background: white;">
                    <i class="fa fa-reply-all text-danger"></i>
                    <span>Retur Pembelian</span>
                </a>
                <a href="javascript:void(0)" class="action-btn btn-sync-stock" style="background: white;">
                    <i class="fa fa-sync-alt text-success"></i>
                    <span>Sinkronisasi Stok</span>
                </a>
                <a href="<?= base_url('warehouse/racks') ?>" class="action-btn" style="background: white;">
                    <i class="fa fa-th text-info"></i>
                    <span>Master Rak</span>
                </a>

                <a href="<?= base_url('warehouse/laporan_pembelian') ?>" class="action-btn" style="background: white;">
                    <i class="fa fa-chart-line" style="color: #14b8a6;"></i>
                    <span>Laporan Inbound</span>
                </a>

                <a href="<?= base_url('warehouse/laporan_barang_keluar') ?>" class="action-btn"
                    style="background: white;">
                    <i class="fa fa-chart-area" style="color: #ef4444;"></i>
                    <span>Laporan Outbound</span>
                </a>

                <!-- <a href="<?= base_url('warehouse/system_reset') ?>" class="action-btn"
                    style="background: white; border: 1px solid #fecaca;">
                    <i class="fa fa-trash-alt text-danger"></i>
                    <span class="text-danger">Full Reset Data</span>
                </a> -->
            </div>
        </div>


        <div class="row">
            <!-- ================= LIVE TRANSACTION MONITOR ================= -->
            <div class="col-md-8">
                <div id="transaction-monitor-container" class="h-100">
                    <div
                        class="text-center py-5 bg-white rounded-4 shadow-sm h-100 d-flex flex-column justify-content-center align-items-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted fw-800">MENGHUBUNGKAN KE LIVE TRANSAKSI...</p>
                    </div>
                </div>
            </div>

            <!-- ================= UNIFIED LIVE MONITOR ================= -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 h-100 monitor-center-card d-flex flex-column"
                    style="border-radius: 28px; background: white;">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-shrink-0">
                        <h4 class="m-0 fw-900 text-dark"><i class="fas fa-desktop text-primary me-2"></i>Warehouse
                            Monitor</h4>
                        <span class="badge badge-live-pulse px-3 py-1 rounded-pill fw-bold">LIVE MONITOR</span>
                    </div>
                    <div id="recent-activity-container" class="flex-grow-1 d-flex flex-column">
                        <div class="text-center py-5 flex-grow-1 d-flex flex-column justify-content-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3 text-muted fw-800">MENGHUBUNGKAN KE MONITOR...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function refreshLiveMonitor() {
        console.log("Refreshing Live Monitor Data...");

        // 1. Preserve Active Tab in Sidebar
        const activeTabId = $('#monitorTab .nav-link.active').attr('id');

        // 2. Refresh High-Level Stats (JSON)
        $.getJSON('<?= base_url('warehouse/ajax_dashboard_stats') ?>', function (res) {
            $('.stat-info h3').each(function () {
                const id = $(this).attr('id');
                if (id == 'stat-inbound-today') $(this).text(res.inbound_today);
                if (id == 'stat-outbound-today') $(this).text(res.outbound_today);
                if (id == 'stat-low-stock') {
                    $(this).text(res.low_stock_count);
                    if (res.low_stock_count > 0) $(this).addClass('text-danger');
                    else $(this).removeClass('text-danger');
                }
                if (id == 'stat-mutations') $(this).text(res.mutation_count);
                if (id == 'stat-pending-requests') {
                    $(this).text(res.pending_requests_count);
                    if (res.pending_requests_count > 0) {
                        $(this).addClass('text-danger');
                        $(this).closest('.stat-card').addClass('border-danger shadow-sm');
                    } else {
                        $(this).removeClass('text-danger');
                        $(this).closest('.stat-card').removeClass('border-danger shadow-sm');
                    }
                }
                if (id == 'stat-monthly-spending') $(this).text(res.monthly_spending);
                if (id == 'stat-inventory-value') $(this).text(res.total_inventory_value);
            });
        });

        // 3. Refresh Live Transaction Monitor (HTML)
        $('#transaction-monitor-container').load('<?= base_url('warehouse/ajax_transaction_monitor') ?>');

        // 4. Refresh Live Feed & Monitor (HTML)
        $('#recent-activity-container').load('<?= base_url('warehouse/ajax_recent_activity') ?>', function () {
            // Re-activate previously active tab
            if (activeTabId) {
                const tabTrigger = new bootstrap.Tab(document.getElementById(activeTabId));
                tabTrigger.show();
            }
        });
    }

    $(document).ready(function () {
        // Initial Load
        refreshLiveMonitor();

        // Set Interval for Auto-Refresh (Every 30 seconds)
        setInterval(refreshLiveMonitor, 30000);
    });

    function loadTodayTransactions(type) {
        // Show loading indicator in modal body
        $('#modalTodayTransactionsBody').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Memuat data transaksi...</p></div>');

        // Use base_url in JS if available, else hardcode for now based on domain
        const baseUrl = '<?= base_url() ?>';
        const url = baseUrl + 'warehouse/ajax_today_transactions/' + type;

        $.ajax({
            url: url,
            method: 'GET',
            success: function (html) {
                $('#modalTodayTransactionsBody').html(html);
                const title = type === 'inbound' ? 'Transaksi Masuk (Hari Ini)' : 'Transaksi Keluar (Hari Ini)';
                $('#modalTodayTransactionsLabel').text(title);
            },
            error: function () {
                $('#modalTodayTransactionsBody').html('<div class="alert alert-danger">Gagal memuat data transaksi hari ini.</div>');
            }
        });
    }
</script>

<!-- Modal for Today Transactions -->
<div class="modal fade" id="modalTodayTransactions" tabindex="-1" aria-labelledby="modalTodayTransactionsLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header border-0 shadow-sm" style="background: var(--slate-soft);">
                <h5 class="modal-title fw-bold text-dark" id="modalTodayTransactionsLabel">Transaksi Hari Ini</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="modalTodayTransactionsBody">
                <!-- AJAX loaded content will appear here -->
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('admin/warehouse/components/v_modal_warehouse_guide'); ?>