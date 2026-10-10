<?php $this->load->view('layout_admin/header_modul'); ?>

<link rel="stylesheet" href="<?= base_url('assets/css/tgm-gudang-dashboard.css') ?>">

<div class="app-content mt-2">
    <!-- <div class="container-fluid"> -->
    <!-- Statistics Grid -->
    <div class="row g-3 mb-4 animate-up">
        <div class="col-6 col-md-3">
            <a href="<?= base_url('warehouse/pembelian') ?>" class="summary-stat-card shadow-sm h-100">
                <div class="stat-v2-icon text-white" style="background: var(--accent-gradient)">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div>
                    <div class="stat-v2-label">Masuk (30 Hari)</div>
                    <div class="stat-v2-value" id="total_pembelian">0</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="<?= base_url('warehouse/pengambilan') ?>" class="summary-stat-card shadow-sm h-100">
                <div class="stat-v2-icon text-white" style="background: var(--info-gradient)">
                    <i class="fas fa-truck-loading"></i>
                </div>
                <div>
                    <div class="stat-v2-label">Total Keluar</div>
                    <div class="stat-v2-value" id="total_pengiriman">0</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="<?= base_url('warehouse/stok') ?>" class="summary-stat-card shadow-sm h-100">
                <div class="stat-v2-icon text-white" style="background: var(--success-gradient)">
                    <i class="fas fa-boxes"></i>
                </div>
                <div>
                    <div class="stat-v2-label">Items Stok</div>
                    <div class="stat-v2-value" id="total_stok">0</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="<?= base_url('warehouse/bahan_baku') ?>" class="summary-stat-card shadow-sm h-100">
                <div class="stat-v2-icon text-white" style="background: var(--danger-gradient)">
                    <i class="fas fa-microchip"></i>
                </div>
                <div>
                    <div class="stat-v2-label">Varian Material</div>
                    <div class="stat-v2-value" id="total_bahan">0</div>
                </div>
            </a>
        </div>
    </div>

    <!-- Quick Actions Bar -->
    <div class="d-flex flex-wrap gap-2 mb-4 animate-up" style="animation-delay: 0.1s">
        <a href="<?= base_url('warehouse/purchase') ?>"
            class="btn-premium-action btn-premium-primary text-decoration-none">
            <i class="fas fa-plus-circle"></i> Tambah Masuk
        </a>
        <a href="<?= base_url('warehouse/PengambilanBahan') ?>"
            class="btn-premium-action text-decoration-none text-dark">
            <i class="fas fa-exchange-alt"></i> Produksi
        </a>
        <a href="<?= base_url('warehouse/ledger') ?>" class="btn-premium-action text-decoration-none text-dark">
            <i class="fas fa-book-open"></i> Kartu Stok
        </a>
        <a href="<?= base_url('warehouse/stok-opname') ?>" class="btn-premium-action text-decoration-none text-dark">
            <i class="fas fa-clipboard-check"></i> Opname
        </a>
        <button onclick="loadDashboardData()" class="btn-premium-action text-decoration-none text-dark ms-auto">
            <i class="fas fa-sync-alt"></i> Sync
        </button>
    </div>

    <!-- Master Data Management Section -->
    <div class="row g-3 mb-4 animate-up" style="animation-delay: 0.15s">
        <div class="col-12">
            <h6 class="text-uppercase fw-bold text-muted small mb-3" style="letter-spacing: 1px;">
                <i class="fas fa-database me-2"></i> Pengaturan & Data Master
            </h6>
        </div>
        <div class="col-6 col-md-2">
            <a href="<?= base_url('warehouse/bahan') ?>" class="master-nav-card shadow-sm">
                <i class="fas fa-box-open"></i>
                <span>Bahan / Jasa</span>
            </a>
        </div>
        <div class="col-6 col-md-2">
            <a href="<?= base_url('warehouse/kategori') ?>" class="master-nav-card shadow-sm">
                <i class="fas fa-tags"></i>
                <span>Kategori</span>
            </a>
        </div>
        <div class="col-6 col-md-2">
            <a href="<?= base_url('warehouse/subkategori') ?>" class="master-nav-card shadow-sm">
                <i class="fas fa-list-ul"></i>
                <span>Sub Kategori</span>
            </a>
        </div>
        <div class="col-6 col-md-2">
            <a href="<?= base_url('warehouse/satuan') ?>" class="master-nav-card shadow-sm">
                <i class="fas fa-balance-scale"></i>
                <span>Satuan</span>
            </a>
        </div>
        <div class="col-6 col-md-2">
            <a href="<?= base_url('warehouse/supplier') ?>" class="master-nav-card shadow-sm">
                <i class="fas fa-truck-field"></i>
                <span>Supplier</span>
            </a>
        </div>
    </div>

    <div class="row g-4 animate-up" style="animation-delay: 0.2s">
        <!-- Recent Transactions -->
        <div class="col-lg-8">
            <div class="report-section-card h-100">
                <div class="section-header">
                    <h5><i class="fas fa-history"></i> Transaksi Terakhir</h5>
                    <div class="badge bg-white text-dark small fw-bold px-3 py-1 rounded-pill">REALTIME</div>
                </div>
                <div class="table-responsive">
                    <table class="table premium-table table-hover mb-0" id="recentTable">
                        <thead>
                            <tr>
                                <th width="120">Waktu</th>
                                <th width="100">Tipe</th>
                                <th>Keterangan / Ref</th>
                                <th width="150" class="text-end">Nilai</th>
                                <th width="100" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Ajax Content -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="report-section-card mb-4">
                <div class="section-header" style="background: var(--danger-gradient)">
                    <h5><i class="fas fa-exclamation-triangle"></i> Stok Kritis</h5>
                </div>
                <div id="critical-items" class="py-2">
                    <!-- Ajax Content -->
                </div>
            </div>

            <div class="report-section-card">
                <div class="section-header" style="background: var(--info-gradient)">
                    <h5><i class="fas fa-chart-pie"></i> Distribusi Stok</h5>
                </div>
                <div class="p-4">
                    <div style="height: 250px;">
                        <canvas id="distChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function () {
        loadDashboardData();
        // Auto refresh every 5 minutes
        setInterval(loadDashboardData, 300000);
    });

    function loadDashboardData() {
        const $btn = $('.fa-sync-alt').parent();
        $btn.addClass('fa-spin');

        $.getJSON('<?= base_url("warehouse/get_dash_stats_ajax") ?>', function (resp) {
            if (resp.success) {
                // Update Counters
                simulateCounter('total_pembelian', resp.stats.pembelian);
                simulateCounter('total_pengiriman', resp.stats.pengiriman);
                simulateCounter('total_stok', resp.stats.stok);
                simulateCounter('total_bahan', resp.stats.bahan);

                // Update Transaction Table
                const $tbody = $('#recentTable tbody');
                $tbody.empty();

                if (resp.recent && resp.recent.length > 0) {
                    resp.recent.forEach(item => {
                        const isPembelian = item.tipe === 'Pembelian';
                        const badgeStyle = isPembelian ? 'background: rgba(39, 174, 96, 0.1); color: #27ae60;' : 'background: rgba(52, 152, 219, 0.1); color: #3498db;';

                        $tbody.append(`
                            <tr>
                                <td class="text-muted small fw-bold">${item.tanggal}</td>
                                <td><span class="badge-premium" style="${badgeStyle}">${item.tipe}</span></td>
                                <td>
                                    <div class="fw-bold text-dark">${item.ref}</div>
                                    <div class="small text-muted" style="font-size: 0.75rem;">${item.desc}</div>
                                </td>
                                <td class="text-end fw-900 ${isPembelian ? 'text-success' : 'text-primary'}">${item.nilai}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-light rounded-pill"><i class="fas fa-eye"></i></button>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    $tbody.append('<tr><td colspan="5" class="text-center py-5 text-muted">Belum ada transaksi terbaru</td></tr>');
                }

                // Update Critical Items
                const $critList = $('#critical-items');
                $critList.empty();
                if (resp.critical && resp.critical.length > 0) {
                    resp.critical.forEach(item => {
                        $critList.append(`
                            <div class="critical-item">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="critical-icon"><i class="fas fa-box"></i></div>
                                    <div>
                                        <div class="fw-bold text-dark small">${item.nama}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">${item.varian || 'Normal'}</div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-900 text-danger">${item.stok}</div>
                                    <div class="text-muted" style="font-size: 0.6rem; text-transform: uppercase;">MIN: ${item.min}</div>
                                </div>
                            </div>
                        `);
                    });
                } else {
                    $critList.append('<div class="p-5 text-center text-muted small"><i class="fas fa-check-circle d-block fa-2x mb-2 text-success opacity-20"></i>Semua stok terpantau aman</div>');
                }

                updateChart(resp.dist);
            }
        }).always(function () {
            setTimeout(() => $btn.removeClass('fa-spin'), 1000);
        });
    }

    function simulateCounter(id, target) {
        let current = parseInt($(`#${id}`).text()) || 0;
        const duration = 1000;
        const stepTime = 50;
        const steps = duration / stepTime;
        const increment = (target - current) / steps;

        let counter = 0;
        const timer = setInterval(() => {
            current += increment;
            counter++;
            $(`#${id}`).text(Math.round(current).toLocaleString());
            if (counter >= steps) {
                clearInterval(timer);
                $(`#${id}`).text(target.toLocaleString());
            }
        }, stepTime);
    }

    let distChart;
    function updateChart(data) {
        const ctx = document.getElementById('distChart').getContext('2d');
        if (distChart) distChart.destroy();

        const colors = ['#4361ee', '#4cc9f0', '#f72585', '#7209b7', '#3a0ca3'];

        distChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.values,
                    backgroundColor: colors.slice(0, data.labels.length),
                    borderWidth: 0,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { family: 'Outfit, Inter, sans-serif', size: 10, weight: '700' }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    }
</script>