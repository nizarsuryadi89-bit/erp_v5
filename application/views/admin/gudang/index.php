<!DOCTYPE html>
<html>

<head>
    <title>Warehouse Management - TGM ERP</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">
    <style>
        .dashboard-header {
            text-align: center;
            margin-bottom: 2rem;
            padding: 2rem 0;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .hr-stats-overview {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-bottom: 3rem;
            justify-content: center;
        }

        .hr-stat-card {
            flex: 1;
            min-width: 240px;
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
            border: 1px solid rgba(0, 0, 0, 0.05);
            text-decoration: none;
            color: inherit;
        }

        .hr-stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            color: inherit;
        }

        .hr-stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .hr-stat-icon.primary {
            background: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }

        .hr-stat-icon.success {
            background: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        .hr-stat-icon.warning {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        .hr-stat-icon.danger {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .hr-stat-icon.info {
            background: rgba(13, 202, 240, 0.1);
            color: #0dcaf0;
        }

        .hr-stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            line-height: 1.2;
        }

        .hr-stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            font-weight: 500;
            margin-top: 0.25rem;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .menu-item {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            transition: all 0.3s ease;
            text-decoration: none;
            color: #495057;
            border: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            color: #0d6efd;
            background: white;
        }

        .menu-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #6c757d;
            transition: color 0.3s;
        }

        .menu-item:hover .menu-icon {
            color: #0d6efd;
        }

        .menu-title {
            font-weight: 600;
            font-size: 0.95rem;
        }
    </style>
</head>

<body>
    <div class="app-content">
        <!-- <div class="container-fluid py-4"> -->
        <!-- Enhanced Header -->
        <?php $this->load->view('layout_admin/header_modul'); ?>

        <!-- Statistics Overview -->
        <div class="hr-stats-overview">
            <!-- Total Bahan Baku -->
            <a href="<?= base_url('gudang/bahan_baku') ?>" class="hr-stat-card">
                <div class="hr-stat-icon primary">
                    <i class="fa fa-database"></i>
                </div>
                <div class="hr-stat-value" id="stat-total-bahan"><?= isset($total_bahan) ? $total_bahan : 0 ?></div>
                <div class="hr-stat-label">Total Bahan Baku</div>
            </a>

            <!-- Pembelian -->
            <a href="<?= base_url('pembelian') ?>" class="hr-stat-card">
                <div class="hr-stat-icon success">
                    <i class="fa fa-cart-plus"></i>
                </div>
                <div class="hr-stat-value" id="stat-total-masuk"><?= isset($total_masuk) ? $total_masuk : 0 ?></div>
                <div class="hr-stat-label">Pembelian (Masuk)</div>
            </a>

            <!-- Pengambilan -->
            <a href="<?= base_url('gudang/PengambilanBahan') ?>" class="hr-stat-card">
                <div class="hr-stat-icon warning">
                    <i class="fa fa-cart-arrow-down"></i>
                </div>
                <div class="hr-stat-value" id="stat-total-keluar"><?= isset($total_keluar) ? $total_keluar : 0 ?>
                </div>
                <div class="hr-stat-label">Pengambilan (Keluar)</div>
            </a>

            <!-- Stok Gudang -->
            <a href="<?= base_url('gudang/stok') ?>" class="hr-stat-card">
                <div class="hr-stat-icon info">
                    <i class="fa fa-boxes"></i>
                </div>
                <div class="hr-stat-value">Stok</div>
                <div class="hr-stat-label">Cek Stok Gudang</div>
            </a>
        </div>

        <!-- Quick Access Menu -->
        <h5 class="mb-4 text-muted fw-bold px-2"><i class="fa fa-th-large me-2"></i>Menu Gudang</h5>
        <div class="menu-grid">
            <a href="<?= base_url('gudang/monitoring_stok') ?>" class="menu-item">
                <i class="menu-icon fa fa-chart-line"></i>
                <span class="menu-title">Monitoring Stok</span>
            </a>
            <a href="<?= base_url('gudang/dashboard') ?>" class="menu-item">
                <i class="menu-icon fa fa-tachometer-alt"></i>
                <span class="menu-title">Dashboard Analitik</span>
            </a>
            <!-- Add more menu items here as needed for future expansion -->
        </div>

        <!-- Stok Minim Alert Container -->
        <div class="card border-0 shadow-sm rounded-12 mt-4">
            <div class="card-header bg-white border-bottom border-light py-3">
                <h6 class="mb-0 fw-bold text-danger"><i class="fa fa-exclamation-triangle me-2"></i>Peringatan Stok
                    Minim</h6>
            </div>
            <div class="card-body" id="stokMinimList">
                <div class="text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            // Load initial data
            loadStokMinimAlert();
            updateStats();

            // Auto refresh
            setInterval(loadStokMinimAlert, 30000); // 30 seconds for alerts
            setInterval(updateStats, 5000); // 5 seconds for stats
        });

        // Real-time Stats Updater
        function updateStats() {
            fetch('<?= base_url('gudang/get_realtime_stats') ?>')
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        $('#stat-total-bahan').text(data.total_bahan);
                        $('#stat-total-masuk').text(data.total_masuk);
                        $('#stat-total-keluar').text(data.total_keluar);
                    }
                })
                .catch(error => console.error('Error fetching stats:', error));
        }

        // Load stok minim alert
        function loadStokMinimAlert() {
            fetch('<?= base_url('gudang/get_material_stok_json') ?>')
                .then(response => response.json())
                .then(data => {
                    let stokMinimList = $('#stokMinimList');
                    stokMinimList.empty();

                    if (data.status === 'success') {
                        let materialArray = Array.isArray(data.data) ? data.data : Object.values(data.data);
                        // Filter logic matches previous implementation
                        let stokMinim = materialArray.filter(material => {
                            const stok_aktif = parseFloat(material.stok_aktif || 0);
                            const stok_min = parseFloat(material.stok_min || 0);
                            return stok_aktif <= stok_min && stok_min > 0;
                        });

                        if (stokMinim.length > 0) {
                            stokMinim.slice(0, 5).forEach(material => {
                                const stok_aktif = parseFloat(material.stok_aktif || 0);
                                const stok_min = parseFloat(material.stok_min || 0);

                                const item = `
                            <div class="alert alert-danger d-flex align-items-center mb-2" role="alert">
                                <i class="fa fa-exclamation-circle me-3 fs-4"></i>
                                <div class="flex-grow-1">
                                    <strong>${material.material_nama || 'Unknown Material'}</strong>
                                    <div class="small">Sisa Stok: ${stok_aktif} ${material.satuan_nama || 'unit'} (Min: ${stok_min})</div>
                                </div>
                                <span class="badge bg-danger rounded-pill">URGENT</span>
                            </div>
                        `;
                                stokMinimList.append(item);
                            });

                            if (stokMinim.length > 5) {
                                stokMinimList.append(`<div class="text-center mt-2"><small class="text-muted">Dan ${stokMinim.length - 5} bahan lainnya...</small></div>`);
                            }
                        } else {
                            stokMinimList.html(`
                        <div class="text-center py-4 text-muted">
                            <i class="fa fa-check-circle fa-3x text-success mb-3"></i>
                            <p class="mb-0">Semua stok dalam kondisi aman.</p>
                        </div>
                    `);
                        }
                    } else {
                        stokMinimList.html('<div class="alert alert-warning">Gagal memuat data stok.</div>');
                    }
                })
                .catch(error => {
                    console.error('Error loading stok minim alert:', error);
                    $('#stokMinimList').html('<div class="alert alert-warning">Terjadi kesalahan koneksi.</div>');
                });
        }
    </script>
</body>

</html>