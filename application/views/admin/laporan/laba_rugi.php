<?php
/**
 * Laporan Laba Rugi - Premium UI/UX Edition
 * Integrated analysis dashboard with professional financial aesthetics.
 */
$current_perusahaan_id = $this->input->get('perusahaan_id') ?: $this->session->userdata('perusahaan_id');
?>

<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">

<style>
    /* ==========================================================================
    TGM PREMIUM FINANCIAL REPORTING (P&L)
    ========================================================================== */
    :root {
        --fin-primary: #1e293b;
        --fin-accent: #3b82f6;
        --fin-success: #10b981;
        --fin-danger: #ef4444;
        --fin-warning: #f59e0b;
        --fin-bg: #f1f5f9;
        --card-radius: 20px;
    }

    .app-content { background-color: var(--fin-bg); min-height: 100vh; font-family: 'Inter', system-ui, sans-serif; }

    /* Filter HUD Styling */
    .filter-hud {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        margin-bottom: 2rem;
    }

    .filter-hud .form-select, .filter-hud .form-control {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 0.6rem 1rem;
        font-weight: 700;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }

    .filter-hud .form-select:focus, .filter-hud .form-control:focus {
        border-color: var(--fin-accent);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Premium Stat Cards */
    .fin-stat-card {
        background: white;
        border-radius: var(--card-radius);
        padding: 1.5rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        transition: all 0.3s ease;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .fin-stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,0,0,0.06); }

    .fin-stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        margin-bottom: 1rem;
    }

    .fin-stat-value { font-size: 1.4rem; font-weight: 900; color: var(--fin-primary); letter-spacing: -0.5px; }
    .fin-stat-label { font-size: 0.7rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 1px; }

    /* Professional P&L Layout */
    .pl-container {
        background: white;
        border-radius: var(--card-radius);
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
    }

    .pl-header {
        background: var(--fin-primary);
        color: white;
        padding: 1.75rem 2rem;
        border-radius: var(--card-radius) var(--card-radius) 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .pl-table { width: 100%; margin-bottom: 0; }
    .pl-table tr td { padding: 1.1rem 2rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    
    .pl-section-title { 
        background: #f8fafc; 
        font-weight: 900; 
        font-size: 0.75rem; 
        color: #475569; 
        text-transform: uppercase; 
        letter-spacing: 1.5px;
        padding: 0.8rem 2rem !important;
    }

    .pl-label { font-weight: 700; color: #1e293b; padding-left: 3.5rem !important; }
    .pl-value { font-weight: 800; text-align: right; font-family: 'SF Mono', 'Josefin Sans', sans-serif; font-size: 1rem; color: #334155; }

    .pl-subtotal { background: #fafafa; font-weight: 900; }
    .pl-subtotal .pl-label { padding-left: 2rem !important; font-size: 0.95rem; color: var(--fin-primary); }

    .pl-net-row { background: var(--fin-primary); }
    .pl-net-row td { border: none !important; padding: 2.25rem 2rem !important; }
    .pl-net-label { color: rgba(255,255,255,0.8); font-weight: 900; font-size: 1.1rem; }
    .pl-net-value { color: white; font-size: 1.8rem; font-weight: 900; }

    /* Detail Side Cards */
    .side-card {
        background: white;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        height: 100%;
        overflow: hidden;
    }

    .side-card-header {
        padding: 1.1rem 1.5rem;
        font-weight: 900;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: white;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .side-table thead th {
        background: #f8fafc;
        padding: 0.75rem 1.5rem;
        font-size: 0.65rem;
        font-weight: 900;
        text-transform: uppercase;
        color: #94a3b8;
        border-bottom: 1px solid #e2e8f0;
    }

    .side-table tbody td {
        padding: 0.85rem 1.5rem;
        font-size: 0.8rem;
        font-weight: 600;
        color: #475569;
    }

    .monospaced { font-family: 'JetBrains Mono', monospace; }

    /* Navigation HUD */
    .nav-hud {
        background: white;
        border-radius: 16px;
        padding: 1rem;
        border: 1px solid #e2e8f0;
        display: flex;
        gap: 0.5rem;
    }
    
    .nav-hud-btn {
        flex: 1;
        padding: 0.75rem;
        border-radius: 10px;
        text-align: center;
        text-decoration: none;
        color: #64748b;
        font-weight: 800;
        font-size: 0.65rem;
        text-transform: uppercase;
        transition: all 0.2s;
        border: 1px solid transparent;
    }

    .nav-hud-btn:hover { background: #f8fafc; color: var(--fin-accent); border-color: #e2e8f0; }
    .nav-hud-btn i { font-size: 1.1rem; margin-bottom: 0.35rem; display: block; }

    @media print {
        .no-print { display: none !important; }
        .app-content { padding: 0 !important; }
        .pl-container { border: none; box-shadow: none; }
        .pl-header { background: white !important; color: black !important; border-bottom: 3px solid black; }
        .pl-net-row { background: #f1f5f9 !important; border-top: 3px solid black !important; }
        .pl-net-label, .pl-net-value { color: black !important; }
    }
</style>

<div class="app-content mt-3 pb-5">
    <?php $this->load->view('layout_admin/header_modul'); ?>

    <div class="container-fluid py-4">
        <!-- TOP FILTER HUD -->
        <div class="filter-hud no-print">
            <form action="<?= base_url('laporan/laba_rugi') ?>" method="GET" class="row align-items-center g-3">
                <div class="col-xl-4 col-lg-12">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 bg-light rounded-3 text-primary border">
                            <i class="fas fa-chart-pie fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-900 text-dark ls-n1">ANALISIS LABA RUGI</h6>
                            <p class="text-muted small mb-0 fw-bold">Monitoring Kesehatan Keuangan Unit</p>
                        </div>
                    </div>
                </div>

                <?php if ($is_admin_owner && !empty($all_perusahaan)): ?>
                    <div class="col-xl-3 col-lg-5">
                        <select class="form-select" name="perusahaan_id" onchange="this.form.submit()">
                            <?php foreach ($all_perusahaan as $p): ?>
                                <option value="<?= $p->id_perusahaan ?>" <?= ($current_perusahaan_id == $p->id_perusahaan) ? 'selected' : '' ?>>
                                    🏢 <?= $p->nama_perusahaan ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="perusahaan_id" value="<?= $current_perusahaan_id ?>">
                    <div class="col-xl-3 d-none d-xl-block"></div>
                <?php endif; ?>

                <div class="col-xl-3 col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="far fa-calendar-alt text-muted"></i></span>
                        <input type="month" name="bulan" class="form-control" value="<?= $periode ?>" onchange="this.form.submit()">
                    </div>
                </div>

                <div class="col-xl-2 col-lg-2 text-end">
                    <div class="d-flex gap-2">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#modalGuideLR" class="btn btn-primary w-100 rounded-3 py-2 fw-900 shadow-sm">
                            <i class="fas fa-book-open"></i>
                        </button>
                        <button type="button" onclick="window.print()" class="btn btn-dark w-100 rounded-3 py-2 fw-900 shadow-sm">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <?php
        $total_beban = ($pengeluaran_bulanan['pengeluaran_bulanan'] ?? 0) + ($pengeluaran_harian['pengeluaran_harian'] ?? 0);
        $laba_bersih_val = ($laba_rugi['laba'] ?? 0) - ($total_beban);
        $is_profit = $laba_bersih_val >= 0;
        ?>

        <!-- KPI GRID -->
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6">
                <div class="fin-stat-card border-bottom border-primary border-4">
                    <div class="fin-stat-icon bg-primary bg-opacity-10 text-primary"><i class="fas fa-hand-holding-usd"></i></div>
                    <div class="fin-stat-label">Total Pendapatan</div>
                    <div class="fin-stat-value">Rp <?= number_format($pemasukan_bulanan['total_pemasukan_bulanan'] ?? 0, 0, ',', '.') ?></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="fin-stat-card border-bottom border-info border-4">
                    <div class="fin-stat-icon bg-info bg-opacity-10 text-info"><i class="fas fa-chart-line"></i></div>
                    <div class="fin-stat-label">Laba Kotor (GP)</div>
                    <div class="fin-stat-value">Rp <?= number_format($laba_rugi['laba'] ?? 0, 0, ',', '.') ?></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="fin-stat-card border-bottom border-warning border-4">
                    <div class="fin-stat-icon bg-warning bg-opacity-10 text-warning"><i class="fas fa-wallet"></i></div>
                    <div class="fin-stat-label">Total Pengeluaran</div>
                    <div class="fin-stat-value">Rp <?= number_format($total_beban, 0, ',', '.') ?></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="fin-stat-card border-bottom <?= $is_profit ? 'border-success' : 'border-danger' ?> border-4">
                    <div class="fin-stat-icon <?= $is_profit ? 'bg-success text-success' : 'bg-danger text-danger' ?> bg-opacity-10"><i class="fas fa-balance-scale"></i></div>
                    <div class="fin-stat-label">Laba Bersih (NP)</div>
                    <div class="fin-stat-value <?= $is_profit ? 'text-success' : 'text-danger' ?>">Rp <?= number_format($laba_bersih_val, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- P&L MAIN STATEMENT -->
            <div class="col-xl-7">
                <div class="pl-container">
                    <div class="pl-header">
                        <div>
                            <h5 class="fw-900 mb-0 ls-n1">PROFIT & LOSS STATEMENT</h5>
                            <small class="opacity-75 fw-bold ls-1">PERIODE LAPORAN: <?= date('F Y', strtotime($periode)) ?></small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-white bg-opacity-20 rounded-pill px-3 py-2 fw-900 border border-white border-opacity-25">AUDITED</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="pl-table">
                            <tbody>
                                <tr class="pl-section-title"><td colspan="2">A. ANALISIS PENDAPATAN & HPP</td></tr>
                                <tr>
                                    <td class="pl-label">Pendapatan Penjualan</td>
                                    <td class="pl-value text-primary">Rp <?= number_format($laba_rugi['total_pendapatan'] ?? 0, 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td class="pl-label">Harga Pokok Penjualan (HPP)</td>
                                    <td class="pl-value text-danger">(Rp <?= number_format($laba_rugi['total_hpp'] ?? 0, 0, ',', '.') ?>)</td>
                                </tr>
                                <tr class="pl-subtotal">
                                    <td class="pl-label">LABA KOTOR (GROSS PROFIT)</td>
                                    <td class="pl-value border-top border-dark border-opacity-50">Rp <?= number_format($laba_rugi['laba'] ?? 0, 0, ',', '.') ?></td>
                                </tr>

                                <tr class="pl-section-title"><td colspan="2">B. BEBAN OPERASIONAL CABANG</td></tr>
                                <tr>
                                    <td class="pl-label">Beban Bulanan (Fixed Cost)</td>
                                    <td class="pl-value text-danger">Rp <?= number_format($pengeluaran_bulanan['pengeluaran_bulanan'] ?? 0, 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td class="pl-label">Beban Harian (Variable Cost)</td>
                                    <td class="pl-value text-danger">Rp <?= number_format($pengeluaran_harian['pengeluaran_harian'] ?? 0, 0, ',', '.') ?></td>
                                </tr>
                                <tr class="pl-subtotal">
                                    <td class="pl-label">TOTAL SELURUH BEBAN</td>
                                    <td class="pl-value border-top border-dark border-opacity-50">Rp <?= number_format($total_beban, 0, ',', '.') ?></td>
                                </tr>

                                <tr class="pl-net-row">
                                    <td class="pl-net-label">LABA / RUGI BERSIH (NET PROFIT)</td>
                                    <td class="pl-net-value text-end"><?= $laba_bersih_val < 0 ? '-' : '' ?> Rp <?= number_format(abs($laba_bersih_val), 0, ',', '.') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TREND VISUALIZATION -->
                <div class="bg-white rounded-4 border p-4 mt-4 shadow-sm no-print">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="fw-900 text-dark mb-0 ls-n1"><i class="fas fa-chart-area me-2 text-primary"></i>VISUALISASI TREN HARIAN</h6>
                        <div class="small fw-bold text-muted opacity-50">Pendapatan vs Pengeluaran</div>
                    </div>
                    <div style="height: 300px;">
                        <canvas id="dailyTrendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- DETAIL BREAKDOWNS -->
            <div class="col-xl-5">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="side-card shadow-sm">
                            <div class="side-card-header bg-info"><i class="fas fa-receipt"></i> BEBAN HARIAN (VARIABLE)</div>
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-hover side-table mb-0">
                                    <thead class="sticky-top">
                                        <tr>
                                            <th>TGL</th>
                                            <th>URAIAN PENGELUARAN</th>
                                            <th class="text-end">NOMINAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($pengeluaran_harian_list)): ?>
                                            <tr><td colspan="3" class="text-center py-5 text-muted fw-bold italic opacity-50">Belum ada rincian data harian.</td></tr>
                                        <?php endif; ?>
                                        <?php foreach ($pengeluaran_harian_list as $row): ?>
                                            <tr>
                                                <td class="fw-800 text-muted"><?= date('d/m', strtotime($row->pengeluaran_tgl)) ?></td>
                                                <td><?= $row->pengeluaran_detail ?></td>
                                                <td class="text-end fw-900 text-dark monospaced">Rp <?= number_format($row->pengeluaran_jumlah, 0, ',', '.') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="side-card shadow-sm">
                            <div class="side-card-header bg-warning"><i class="fas fa-bolt"></i> BEBAN BULANAN (FIXED)</div>
                            <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                                <table class="table table-hover side-table mb-0">
                                    <thead class="sticky-top">
                                        <tr>
                                            <th>TGL</th>
                                            <th>URAIAN PENGELUARAN</th>
                                            <th class="text-end">NOMINAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($pengeluaran_bulanan_list)): ?>
                                            <tr><td colspan="3" class="text-center py-5 text-muted fw-bold italic opacity-50">Belum ada rincian data bulanan.</td></tr>
                                        <?php endif; ?>
                                        <?php foreach ($pengeluaran_bulanan_list as $row): ?>
                                            <tr>
                                                <td class="fw-800 text-muted"><?= date('d/m', strtotime($row->pengeluaran_tanggal)) ?></td>
                                                <td><?= $row->pengeluaran_detail ?></td>
                                                <td class="text-end fw-900 text-dark monospaced">Rp <?= number_format($row->pengeluaran_jumlah, 0, ',', '.') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- NAVIGATION SHORTCUTS -->
                    <div class="col-12 no-print">
                        <div class="nav-hud shadow-sm">
                            <a href="<?= base_url('laporan/dashboard_terpadu?perusahaan_id=' . $current_perusahaan_id) ?>" class="nav-hud-btn">
                                <i class="fas fa-tachometer-alt text-primary"></i> DASHBOARD
                            </a>
                            <a href="<?= base_url('laporan/pemasukan_harian?perusahaan_id=' . $current_perusahaan_id) ?>" class="nav-hud-btn">
                                <i class="fas fa-hand-holding-usd text-success"></i> PEMASUKAN
                            </a>
                            <a href="<?= base_url('laporan/index?perusahaan_id=' . $current_perusahaan_id) ?>" class="nav-hud-btn">
                                <i class="fas fa-home text-info"></i> SEMUA
                            </a>
                            <a href="<?= base_url('laporan/pengeluaran?perusahaan_id=' . $current_perusahaan_id) ?>" class="nav-hud-btn">
                                <i class="fas fa-wallet text-danger"></i> BEBAN
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('dailyTrendChart').getContext('2d');
        const chartData = <?= json_encode($chart_data) ?>;
        chartData.sort((a, b) => new Date(a.date) - new Date(b.date));

        const labels = chartData.map(item => new Date(item.date).getDate());
        const pendapatanData = chartData.map(item => item.total_pemasukan);
        const pengeluaranData = chartData.map(item => item.total_pengeluaran);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan',
                    data: pendapatanData,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.05)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#10b981'
                }, {
                    label: 'Pengeluaran',
                    data: pengeluaranData,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.05)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#ef4444'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true, font: { weight: 'bold', family: 'Inter' } } },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(30, 41, 59, 1)',
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        padding: 12,
                        callbacks: { label: c => ' ' + c.dataset.label + ': Rp ' + c.parsed.y.toLocaleString('id-ID') }
                    }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: v => 'Rp ' + v.toLocaleString('id-ID'), font: { size: 10, weight: '600' }, color: '#94a3b8' }, grid: { borderDash: [5, 5], color: '#f1f5f9' } },
                    x: { ticks: { font: { size: 10, weight: '600' }, color: '#94a3b8' }, grid: { display: false } }
                }
            }
        });
    });
</script>

<!-- MODAL SOP LABA RUGI -->
<div class="modal fade" id="modalGuideLR" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg rounded-4 overflow-hidden border-0">
            <div class="modal-header bg-dark text-white p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-circle p-2 me-3">
                        <i class="fas fa-calculator text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-900 ls-1 mb-0 text-uppercase">PANDUAN LAPORAN LABA RUGI (P&L)</h5>
                        <small class="text-white-50 fw-bold">Interpretasi Data & Standar Audit Keuangan</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-light">
                <div class="p-4 p-md-5">
                    <!-- Rumus Utama -->
                    <div class="row mb-5">
                        <div class="col-12">
                            <div class="bg-white rounded-4 p-4 shadow-sm border-start border-5 border-info">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h4 class="fw-900 text-dark mb-3"><i class="fas fa-info-circle text-info me-2"></i>Logika Perhitungan</h4>
                                        <div class="d-flex align-items-center gap-3 flex-wrap">
                                            <div class="px-3 py-2 bg-primary bg-opacity-10 rounded-3 fw-bold text-primary">Laba Kotor = Pemasukan - HPP</div>
                                            <i class="fas fa-minus text-muted"></i>
                                            <div class="px-3 py-2 bg-warning bg-opacity-10 rounded-3 fw-bold text-warning">Total Beban</div>
                                            <i class="fas fa-equals text-muted"></i>
                                            <div class="px-3 py-2 bg-success bg-opacity-10 rounded-3 fw-bold text-success">Laba Bersih</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center border-start d-none d-md-block">
                                        <p class="small text-muted mb-1 fw-bold">TINGKAT AKURASI</p>
                                        <h2 class="fw-900 text-dark">100%</h2>
                                        <span class="badge bg-success rounded-pill">REAL-TIME DATA</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="h-100 bg-white rounded-4 p-4 shadow-sm border border-opacity-10 position-relative pt-5">
                                <div class="position-absolute top-0 start-0 ms-4 mt-n3 bg-primary text-white fw-900 rounded-3 px-3 py-2 shadow-sm" style="margin-top: -20px;">01</div>
                                <div class="bg-light rounded-3 p-3 d-inline-block mb-3"><i class="fas fa-chart-line text-primary fs-4"></i></div>
                                <h5 class="fw-900 text-dark mb-2">Audit Laba Kotor</h5>
                                <p class="small text-muted mb-0">Pastikan **Penjualan** vs **HPP** (Biaya Bahan) sudah sesuai. Jika Laba Kotor terlalu tipis, cek efisiensi penggunaan bahan di produksi.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="h-100 bg-white rounded-4 p-4 shadow-sm border border-opacity-10 position-relative pt-5">
                                <div class="position-absolute top-0 start-0 ms-4 mt-n3 bg-primary text-white fw-900 rounded-3 px-3 py-2 shadow-sm" style="margin-top: -20px;">02</div>
                                <div class="bg-light rounded-3 p-3 d-inline-block mb-3"><i class="fas fa-wallet text-primary fs-4"></i></div>
                                <h5 class="fw-900 text-dark mb-2">Audit Beban</h5>
                                <p class="small text-muted mb-0">Beban terbagi dua: **Harian** (Parkir, makan, dll) dan **Bulanan** (Gaji, Listrik, Sewa). Gunakan detail di samping untuk mencari pemborosan.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="h-100 bg-white rounded-4 p-4 shadow-sm border border-opacity-10 position-relative pt-5">
                                <div class="position-absolute top-0 start-0 ms-4 mt-n3 bg-primary text-white fw-900 rounded-3 px-3 py-2 shadow-sm" style="margin-top: -20px;">03</div>
                                <div class="bg-light rounded-3 p-3 d-inline-block mb-3"><i class="fas fa-print text-primary fs-4"></i></div>
                                <h5 class="fw-900 text-dark mb-2">Finalisasi & Cetak</h5>
                                <p class="small text-muted mb-0">Gunakan tombol **CETAK** untuk keperluan meeting atau arsip fisik. Layout akan otomatis berubah menjadi format dokumen.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-primary bg-opacity-10 rounded-4 p-4 mt-5 border border-primary border-opacity-25">
                        <h5 class="fw-900 text-dark mb-3"><i class="fas fa-book me-2 text-primary"></i>PENTING UNTUK DIINGAT</h5>
                        <ul class="mb-0 small fw-bold text-muted">
                            <li class="mb-2">Data Laba Rugi dihitung berdasarkan **TANGGAL TRANSAKSI**, bukan tanggal pelunasan saja.</li>
                            <li class="mb-2">Gunakan fitur **FILTER CABANG** untuk melihat performa per unit atau konsolidasi pusat.</li>
                            <li>Warna **MERAH** pada Laba Bersih menandakan unit bisnis sedang mengalami kerugian operasional di periode tersebut.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white p-4">
                <button type="button" class="btn btn-dark rounded-pill px-5 fw-900" data-bs-dismiss="modal">MENGERTI</button>
            </div>
        </div>
    </div>
</div>