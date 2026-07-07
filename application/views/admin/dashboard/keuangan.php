<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    :root {
        --fin-primary: #0f172a;
        --fin-secondary: #1e293b;
        --fin-accent: #3b82f6;
        /* Financial Blue */
        --fin-accent-soft: rgba(59, 130, 246, 0.1);
        --fin-success: #10b981;
        --fin-danger: #ef4444;
        --fin-warning: #f59e0b;
        --fin-bg: #f1f5f9;
        --fin-card-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    }

    .fin-dashboard {
        background: var(--fin-bg);
        min-height: 100vh;
        padding-bottom: 3rem;
    }

    /* Premium Metric Card */
    .f-metric {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        border: 1px solid #e2e8f0;
        box-shadow: var(--fin-card-shadow);
        transition: all 0.2s;
    }

    .f-metric:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
    }

    .f-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.75rem;
        font-size: 1.1rem;
    }

    .f-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .f-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--fin-primary);
        margin-bottom: 0.25rem;
    }

    /* Soft Backgrounds */
    .bg-s-blue {
        background: #eff6ff;
        color: #3b82f6;
    }

    .bg-s-emerald {
        background: #ecfdf5;
        color: #10b981;
    }

    .bg-s-rose {
        background: #fff1f2;
        color: #f43f5e;
    }

    .bg-s-amber {
        background: #fffbeb;
        color: #f59e0b;
    }

    .bg-s-slate {
        background: #f8fafc;
        color: #475569;
    }

    /* Chart Containers */
    .f-chart-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        border: 1px solid #e2e8f0;
        box-shadow: var(--fin-card-shadow);
        height: 100%;
    }

    .f-chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .f-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--fin-primary);
    }

    /* Table Styling */
    .f-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
    }

    .f-table tr {
        background: #f8fafc;
        transition: background 0.2s;
    }

    .f-table tr:hover {
        background: #f1f5f9;
    }

    .f-table td {
        padding: 12px 16px;
        font-size: 0.85rem;
        border: none;
    }

    .f-table td:first-child {
        border-radius: 10px 0 0 10px;
    }

    .f-table td:last-child {
        border-radius: 0 10px 10px 0;
    }

    .status-pill {
        padding: 2px 8px;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
    }
</style>

<div class="fin-dashboard app-content mt-n4">
    <div class="container-fluid">

        <!-- Header -->
        <div class="row align-items-center mb-4 pt-3">
            <div class="col-md-6">
                <span
                    class="badge bg-s-blue mb-2 px-3 py-2 rounded-pill fw-bold border border-primary-subtle text-primary">MANAJEMEN
                    KEUANGAN</span>
                <h3 class="fw-900 text-dark mb-1">Dashboard Pengendali Keuangan</h3>
                <p class="text-muted fw-500 mb-0">Pemantauan arus kas, pelaporan, dan kepatuhan transaksi.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="d-inline-flex align-items-center gap-2">
                    <form action="<?= site_url('dashboard/ringkasan_keuangan') ?>" method="GET" class="me-2">
                        <select name="branch_id"
                            class="form-select form-select-sm rounded-pill fw-bold border-0 shadow-sm px-4"
                            style="height: 45px; min-width: 200px;" onchange="this.form.submit()">
                            <option value="all" <?= !$selected_branch ? 'selected' : '' ?>>🌐 Konsolidasi Grup</option>
                            <?php foreach ($branches as $b): ?>
                                <option value="<?= $b->id_perusahaan ?>" <?= $selected_branch == $b->id_perusahaan ? 'selected' : '' ?>>📍 <?= $b->nama_perusahaan ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                    <div class="bg-white rounded-pill shadow-sm px-4 py-2 border d-flex align-items-center"
                        style="height: 45px;">
                        <i class="fas fa-calendar-alt text-primary me-2"></i>
                        <span class="fw-bold text-dark small"><?= date('d M Y') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric Row 1: Flow -->
        <div class="row g-4 mb-4">
            <!-- Pemasukan -->
            <div class="col-xl-3 col-md-6">
                <div class="f-metric">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="f-icon bg-s-emerald"><i class="fas fa-hand-holding-usd"></i></div>
                            <div class="f-label">Pemasukan Hari Ini</div>
                            <div class="f-value">Rp <?= number_format($pemasukan_hariini, 0, ',', '.') ?></div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-s-emerald text-success px-2 py-1 rounded-pill small">Aktif</span>
                        </div>
                    </div>
                    <div class="mt-2 text-muted small fw-600">
                        Bulan ini: <span class="text-dark fw-800">Rp
                            <?= number_format($pemasukan_bulanini, 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>

            <!-- Pengeluaran -->
            <div class="col-xl-3 col-md-6">
                <div class="f-metric">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="f-icon bg-s-rose"><i class="fas fa-money-bill-wave"></i></div>
                            <div class="f-label">Pengeluaran Hari Ini</div>
                            <div class="f-value text-danger">Rp <?= number_format($pengeluaran_hariini, 0, ',', '.') ?>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 text-muted small fw-600">
                        Bulan ini: <span class="text-danger fw-800">Rp
                            <?= number_format($pengeluaran_bulanini, 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>

            <!-- Piutang -->
            <div class="col-xl-3 col-md-6">
                <div class="f-metric">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="f-icon bg-s-amber"><i class="fas fa-file-invoice-dollar"></i></div>
                            <div class="f-label">Total Piutang</div>
                            <div class="f-value">Rp <?= number_format($total_piutang, 0, ',', '.') ?></div>
                        </div>
                    </div>
                    <div class="mt-2 text-muted small fw-600">
                        <i class="fas fa-exclamation-circle text-warning me-1"></i> Tagihan belum lunas
                    </div>
                </div>
            </div>

            <!-- Hutang -->
            <div class="col-xl-3 col-md-6">
                <div class="f-metric">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="f-icon bg-s-slate"><i class="fas fa-file-contract"></i></div>
                            <div class="f-label">Total Hutang</div>
                            <div class="f-value">Rp <?= number_format($total_hutang, 0, ',', '.') ?></div>
                        </div>
                    </div>
                    <div class="mt-2 text-muted small fw-600">
                        Kewajiban pihak ketiga
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric Row 2: Performance Audit -->
        <div class="row g-4 mb-4">
            <!-- Profitability -->
            <div class="col-md-6">
                <div class="f-metric"
                    style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border: none;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="f-label text-info opacity-75">Estimasi Laba Masuk (Bulan Ini)</div>
                            <div class="f-value text-white mb-0" style="font-size: 2rem;">Rp
                                <?= number_format($profit_bulanini, 0, ',', '.') ?></div>
                            <div class="mt-2">
                                <span class="badge bg-info text-dark rounded-pill px-3 py-1">
                                    <i class="fas fa-chart-line me-1"></i> Profit Rate: <?= $profit_rate ?>%
                                </span>
                            </div>
                        </div>
                        <div class="text-white opacity-25">
                            <i class="fas fa-vault fa-4x"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Audit Alert -->
            <div class="col-md-6">
                <div class="f-metric bg-white border-0 shadow-sm d-flex align-items-center justify-content-between p-4">
                    <div class="d-flex align-items-center">
                        <div class="f-icon bg-s-amber rounded-circle me-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-user-clock fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="fw-800 text-dark mb-1"><?= $pending_audit ?> Transaksi</h5>
                            <p class="text-muted small mb-0 fw-600">Pending Audit / Belum Lunas > 3 Hari</p>
                        </div>
                    </div>
                    <a href="<?= site_url('laporan/piutang') ?>" class="btn btn-warning rounded-pill fw-900 px-4">AUDIT
                        SEKARANG</a>
                </div>
            </div>
        </div>

        <!-- Charts & Analysis -->
        <div class="row g-4 mb-4">
            <!-- Cash Flow Trend -->
            <div class="col-lg-8">
                <div class="f-chart-card">
                    <div class="f-chart-header">
                        <div class="f-title"><i class="fas fa-chart-area text-primary me-2"></i>Tren Arus Kas (12 Bulan
                            Terakhir)</div>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-secondary">CSV</button>
                            <button class="btn btn-outline-secondary">PDF</button>
                        </div>
                    </div>
                    <div style="height: 350px;">
                        <canvas id="cashFlowChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Payment Methods Today -->
            <div class="col-lg-4">
                <div class="f-chart-card">
                    <div class="f-chart-header">
                        <div class="f-title"><i class="fas fa-wallet text-indigo me-2"></i>Komposisi Pembayaran</div>
                    </div>
                    <div style="height: 250px;" class="mb-4">
                        <canvas id="paymentMethodChart"></canvas>
                    </div>
                    <div class="payment-legend">
                        <?php foreach ($metode_today as $mt): ?>
                            <div
                                class="d-flex justify-content-between align-items-center mb-2 px-2 py-1 bg-light rounded-8">
                                <span class="small fw-bold text-dark"><?= $mt->metode ?></span>
                                <span class="small fw-800 text-muted">Rp
                                    <?= number_format($mt->total, 0, ',', '.') ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tables: Recent Activity -->
        <div class="row g-4 mb-4">
            <!-- Recent Transactions -->
            <div class="col-lg-6">
                <div class="f-chart-card">
                    <div class="f-title mb-4"><i class="fas fa-receipt text-success me-2"></i>Transaksi Masuk Terakhir
                    </div>
                    <div class="table-responsive">
                        <table class="f-table">
                            <tbody>
                                <?php foreach ($recent_payments as $rp): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-800 text-dark"><?= $rp->nospk ?></div>
                                            <div class="text-muted" style="font-size: 10px;"><?= $rp->jam_bayar ?></div>
                                        </td>
                                        <td>
                                            <div class="fw-700 text-dark"><?= $rp->konsumen_nama ?></div>
                                            <div class="text-muted" style="font-size: 10px;">Kasir: <?= $rp->kasir ?></div>
                                        </td>
                                        <td class="text-end fw-900 text-success">
                                            Rp <?= number_format($rp->total_bayar, 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 text-center">
                        <a href="<?= site_url('laporan/penjualan') ?>"
                            class="btn btn-sm btn-link text-primary fw-bold">Lihat Semua Penjualan <i
                                class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>

            <!-- Recent Expenses -->
            <div class="col-lg-6">
                <div class="f-chart-card">
                    <div class="f-title mb-4"><i class="fas fa-file-invoice text-danger me-2"></i>Pengeluaran Terakhir
                    </div>
                    <div class="table-responsive">
                        <table class="f-table">
                            <tbody>
                                <?php foreach ($recent_expenses as $re): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-800 text-dark"><?= $re->pengeluaran_detail ?></div>
                                            <div class="text-muted" style="font-size: 10px;"><?= $re->pengeluaran_tgl ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="status-pill bg-s-slate text-secondary"><?= $re->kategori_pengeluaran ?></span>
                                        </td>
                                        <td class="text-end fw-900 text-danger">
                                            - Rp <?= number_format($re->pengeluaran_jumlah, 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 text-center">
                        <a href="<?= site_url('pengeluaran') ?>" class="btn btn-sm btn-link text-danger fw-bold">Kelola
                            Pengeluaran <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access to Reports -->
        <div class="row g-4">
            <div class="col-md-3">
                <a href="<?= site_url('laporan/pembayaran') ?>" class="text-decoration-none">
                    <div class="f-metric bg-white border-0 shadow-sm text-center py-4">
                        <div class="f-icon bg-s-blue mx-auto mb-3"><i class="fas fa-table"></i></div>
                        <div class="fw-800 text-dark">Laporan Kas</div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="<?= site_url('laporan/percabang') ?>" class="text-decoration-none">
                    <div class="f-metric bg-white border-0 shadow-sm text-center py-4">
                        <div class="f-icon bg-s-emerald mx-auto mb-3"><i class="fas fa-chart-pie"></i></div>
                        <div class="fw-800 text-dark">Laba Rugi Cabang</div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="<?= site_url('laporan/piutang') ?>" class="text-decoration-none">
                    <div class="f-metric bg-white border-0 shadow-sm text-center py-4">
                        <div class="f-icon bg-s-amber mx-auto mb-3"><i class="fas fa-user-clock"></i></div>
                        <div class="fw-800 text-dark">Buku Piutang</div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="<?= site_url('laporan/pengeluaran') ?>" class="text-decoration-none">
                    <div class="f-metric bg-white border-0 shadow-sm text-center py-4">
                        <div class="f-icon bg-s-rose mx-auto mb-3"><i class="fas fa-file-export"></i></div>
                        <div class="fw-800 text-dark">Audit Pengeluaran</div>
                    </div>
                </a>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Cash Flow Chart (Income vs Expense)
    const ctxCF = document.getElementById('cashFlowChart').getContext('2d');
    const cfLabels = <?= json_encode(array_keys($chart_data)) ?>;
    const incomeData = <?= json_encode(array_column($chart_data, 'income')) ?>;
    const expenseData = <?= json_encode(array_column($chart_data, 'expense')) ?>;

    new Chart(ctxCF, {
        type: 'bar',
        data: {
            labels: cfLabels,
            datasets: [
                {
                    label: 'Pemasukan',
                    data: incomeData,
                    backgroundColor: '#10b981',
                    borderRadius: 6,
                },
                {
                    label: 'Pengeluaran',
                    data: expenseData,
                    backgroundColor: '#f43f5e',
                    borderRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', align: 'end', labels: { usePointStyle: true, font: { weight: '700', size: 11 } } }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [5, 5], color: '#f1f5f9' },
                    ticks: {
                        callback: function (value) {
                            if (value >= 1000000) return (value / 1000000) + 'jt';
                            return value;
                        }
                    }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // Payment Method Chart
    const ctxPM = document.getElementById('paymentMethodChart').getContext('2d');
    const pmLabels = <?= json_encode(array_map(function ($m) {
        return $m->metode;
    }, $metode_today)) ?>;
    const pmData = <?= json_encode(array_map(function ($m) {
        return (float) $m->total;
    }, $metode_today)) ?>;

    new Chart(ctxPM, {
        type: 'doughnut',
        data: {
            labels: pmLabels,
            datasets: [{
                data: pmData,
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#64748b'],
                borderWidth: 0,
                hoverOffset: 12
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: { legend: { display: false } }
        }
    });
</script>