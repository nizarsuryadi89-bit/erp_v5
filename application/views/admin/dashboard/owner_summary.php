<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    :root {
        --exec-primary: #0f172a;
        /* Slate 900 */
        --exec-secondary: #1e293b;
        /* Slate 800 */
        --exec-accent: #3b82f6;
        /* Blue 500 */
        --exec-accent-glow: rgba(59, 130, 246, 0.1);
        --exec-success: #10b981;
        --exec-danger: #ef4444;
        --exec-warning: #f59e0b;
        --exec-bg: #f8fafc;
        --exec-card-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    }

    .exec-dashboard {
        background: var(--exec-bg);
        min-height: 100vh;
        padding-bottom: 3rem;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    /* Premium Metric Cards */
    .metric-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        border: 1px solid #e2e8f0;
        box-shadow: var(--exec-card-shadow);
        transition: all 0.2s ease;
        position: relative;
    }

    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
    }

    .metric-card .icon-box {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        margin-bottom: 1rem;
    }

    /* Analytics Containers */
    .analytics-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        border: 1px solid #e2e8f0;
        box-shadow: var(--exec-card-shadow);
        height: 100%;
    }

    .analytics-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .analytics-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--exec-primary);
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    /* Business Health Scorecard */
    .health-scorecard {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        margin-bottom: 2rem;
        border: 1px solid #e2e8f0;
        display: flex;
        gap: 1rem;
        box-shadow: var(--exec-card-shadow);
    }

    .health-item {
        flex: 1;
        padding: 0.5rem 1.5rem;
        border-right: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .health-item:last-child {
        border-right: none;
    }

    .health-label {
        font-size: 0.7rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 0.25rem;
        letter-spacing: 0.05em;
    }

    .health-value {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--exec-primary);
    }

    /* Branch Performance */
    .branch-row {
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .branch-row:last-child {
        border-bottom: none;
    }

    .progress-minimal {
        height: 6px;
        border-radius: 3px;
        background: #f1f5f9;
        overflow: hidden;
    }

    /* Filters */
    .filter-pill {
        padding: 6px 16px;
        border-radius: 9999px;
        background: white;
        border: 1px solid #e2e8f0;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--exec-secondary);
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .filter-pill:hover,
    .filter-pill.active {
        background: var(--exec-primary);
        color: white;
        border-color: var(--exec-primary);
    }

    /* Tables */
    .premium-table th {
        background: #f8fafc;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        padding: 1rem;
        border: none;
    }

    .premium-table td {
        padding: 1rem;
        font-size: 0.85rem;
        border-color: #f1f5f9;
    }

    .avatar-exec {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        object-fit: cover;
    }
</style>

<div class="exec-dashboard mt-n4">
    <div class="container-fluid pt-4">

        <!-- Executive Header & Filters -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h3 class="fw-800 mb-1 text-primary">ANALISA BISNIS</h3>
                <p class="text-muted small mb-0 fw-600">Konsolidasi Data Strategis • <?= date('d F Y') ?></p>
            </div>
            <div class="d-flex align-items-center gap-2 bg-white p-2 px-3 rounded-pill shadow-sm border">
                <i class="bi bi-info-circle-fill text-primary"></i>
                <span class="small fw-800 text-muted text-uppercase" style="letter-spacing: 0.05em;">
                    Cakupan Data:
                    <span class="text-primary">
                        <?= ($selected_branch === null) ? ' Konsolidasi Grup (Seluruh Cabang)' : ' Cabang ' . $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $selected_branch])->row()->nama_perusahaan ?>
                    </span>
                </span>
            </div>
        </div>

        <!-- Strategic Health Scorecard -->
        <div class="health-scorecard">
            <div class="health-item">
                <?php
                $net_liquidity = $total_piutang - $summary_hutang->total_sisa_hutang;
                $is_surplus = $net_liquidity >= 0;
                ?>
                <div class="health-label">Likuiditas Netto</div>
                <div class="health-value <?= $is_surplus ? 'text-success' : 'text-danger' ?>">
                    <span style="font-size: 0.9rem;">Rp</span> <?= number_format($net_liquidity, 0, ',', '.') ?>
                </div>
                <div class="small fw-bold text-muted"><?= $is_surplus ? 'Surplus (Aman)' : 'Defisit (Perhatian)' ?>
                </div>
            </div>
            <div class="health-item">
                <div class="health-label">Waktu Proses Rata-rata</div>
                <div class="health-value"><?= $stats_produksi->avg_lead_time ?> Jam</div>
                <div class="small fw-bold text-muted">Durasi Alur Produksi</div>
            </div>
            <div class="health-item">
                <?php
                $load_percent = min(100, ($stats_produksi->processing ?? 0) * 10);
                ?>
                <div class="health-label">Beban Operasional</div>
                <div class="health-value text-primary"><?= $load_percent ?>%</div>
                <div class="small fw-bold text-muted">Kapasitas Mesin Terisi</div>
            </div>
            <div class="health-item">
                <div class="health-label">Aging Piutang</div>
                <?php
                $total_piutang_sum = ($aging_piutang->age_0_15 + $aging_piutang->age_16_30 + $aging_piutang->age_over_30) ?: 1;
                $bad_debt_ratio = round(($aging_piutang->age_over_30 / $total_piutang_sum) * 100, 1);
                ?>
                <div class="health-value <?= $bad_debt_ratio > 20 ? 'text-danger' : 'text-success' ?>">
                    <?= $bad_debt_ratio ?>%
                </div>
                <div class="small fw-bold text-muted">Rasio Piutang Macet (>30h)</div>
            </div>

            <div class="health-item">
                <div class="health-label">Total Aset & Inventaris</div>
                <div class="health-value text-info">
                    <span style="font-size: 0.9rem;">Rp</span> <?= number_format($total_nilai_aset ?? 0, 0, ',', '.') ?>
                </div>
                <div class="small fw-bold text-muted"><?= $total_aset ?? 0 ?> Unit Terdaftar</div>
            </div>
        </div>

        <!-- Strategic Notifications (Intelligence Layer) -->
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="d-flex gap-2 overflow-auto pb-2" style="scrollbar-width: none;">
                    <?php if (!empty($critical_materials)): ?>
                        <div
                            class="alert alert-danger border-0 shadow-sm d-flex align-items-center mb-0 px-4 py-2 rounded-pill flex-shrink-0">
                            <i class="bi bi-exclamation-triangle-fill me-2 pulse"></i>
                            <span class="small fw-800"><?= count($critical_materials) ?> MATERIAL KRITIS</span>
                        </div>
                    <?php endif; ?>

                    <div
                        class="alert alert-primary border-0 shadow-sm d-flex align-items-center mb-0 px-4 py-2 rounded-pill flex-shrink-0">
                        <i class="bi bi-bullseye me-2"></i>
                        <span class="small fw-800">TARGET: <?= $sales_percentage ?>% TERCAPAI</span>
                    </div>

                    <?php if ($bad_debt_ratio > 15): ?>
                        <div
                            class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-0 px-4 py-2 rounded-pill flex-shrink-0">
                            <i class="bi bi-cash-stack me-2"></i>
                            <span class="small fw-800">PERINGATAN PIUTANG (>15%)</span>
                        </div>
                    <?php endif; ?>

                    <div
                        class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-0 px-4 py-2 rounded-pill flex-shrink-0">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <span class="small fw-800">SISTEM ONLINE & STABIL</span>
                    </div>

                    <!-- Short-cut Monitoring -->
                    <a href="<?= site_url('administrative/spk_monitor') ?>"
                        class="btn btn-dark border-0 shadow-sm d-flex align-items-center mb-0 px-4 py-2 rounded-pill flex-shrink-0 hover-lift">
                        <i class="bi bi-display me-2 text-warning"></i>
                        <span class="small fw-800">MONITOR SPK</span>
                    </a>

                    <a href="<?= site_url('laporan/pemasukan_harian') ?>"
                        class="btn btn-dark border-0 shadow-sm d-flex align-items-center mb-0 px-4 py-2 rounded-pill flex-shrink-0 hover-lift">
                        <i class="bi bi-cash-stack me-2 text-success"></i>
                        <span class="small fw-800">LAP. HARIAN</span>
                    </a>

                    <a href="<?= site_url('absensi') ?>"
                        class="btn btn-dark border-0 shadow-sm d-flex align-items-center mb-0 px-4 py-2 rounded-pill flex-shrink-0 hover-lift">
                        <i class="bi bi-people me-2 text-info"></i>
                        <span class="small fw-800">ABSENSI</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <!-- Main KPI: Sales Target vs Reality -->
            <div class="col-xl-5">
                <div class="analytics-card"
                    style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border: none;">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h5 class="text-white fw-800 mb-1">PERFORMA OMZET BULAN INI</h5>
                            <p class="text-white-50 small mb-0">Target: Rp
                                <?= number_format($sales_target, 0, ',', '.') ?>
                            </p>
                        </div>
                        <div class="text-end">
                            <h2 class="text-white fw-900 mb-0"><?= $sales_percentage ?>%</h2>
                        </div>
                    </div>

                    <div class="progress mb-4"
                        style="height: 12px; background: rgba(255,255,255,0.1); border-radius: 6px;">
                        <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated"
                            role="progressbar" style="width: <?= $sales_percentage ?>%; border-radius: 6px;"></div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 rounded-4" style="background: rgba(255,255,255,0.05);">
                                <div class="text-white-50 small fw-700 label text-uppercase mb-1">Pencapaian</div>
                                <div class="text-white h5 fw-800 mb-0">Rp
                                    <?= number_format($sales_actual, 0, ',', '.') ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-4" style="background: rgba(255,255,255,0.05);">
                                <div class="text-white-50 small fw-700 label text-uppercase mb-1">Sisa Target</div>
                                <div class="text-warning h5 fw-800 mb-0">Rp
                                    <?= number_format(max(0, $sales_target - $sales_actual), 0, ',', '.') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="metric-card h-100">
                    <div class="icon-box bg-success bg-opacity-10 text-success">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="health-label">Profitabilitas (Gross)</div>
                    <h3 class="fw-800 mb-3 text-success">Rp <?= number_format($laba_estimasi, 0, ',', '.') ?></h3>
                    <div class="p-2 bg-light rounded-3">
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted fw-bold">Margin Rate:</span>
                            <span
                                class="text-success fw-900"><?= ($sales_actual > 0) ? round(($laba_estimasi / $sales_actual) * 100, 1) : 0 ?>%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-6">
                <div class="analytics-card h-100 p-2">
                    <div class="health-label mb-2 text-center" style="font-size: 0.65rem;">Distribusi Pendapatan</div>
                    <div style="height: 140px;">
                        <canvas id="categoryMixChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-6">
                <div class="analytics-card h-100 p-2">
                    <div class="health-label mb-2 text-center" style="font-size: 0.65rem;">Distribusi Pembayaran</div>
                    <div style="height: 140px;">
                        <canvas id="paymentMixChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric Row 2 -->
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6">
                <div class="metric-card shadow-sm border-start border-4 border-warning">
                    <div class="health-label mb-3">Antrian Produksi</div>

                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between align-items-center small">
                            <span class="text-muted fw-bold">Antrian Masuk:</span>
                            <span
                                class="badge bg-light text-dark fw-bolder border px-2 py-1"><?= $stats_produksi->total_queue ?>
                                SPK</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center small">
                            <span class="text-muted fw-bold">Sedang Produksi:</span>
                            <span
                                class="badge bg-warning text-dark fw-bolder border-warning px-2 py-1"><?= $stats_produksi->processing ?>
                                SPK</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center small">
                            <span class="text-muted fw-bold">Sudah Ada di PB:</span>
                            <span
                                class="badge bg-success bg-opacity-10 text-success fw-bolder border border-success px-2 py-1"><?= $antrian_pb ?>
                                SPK</span>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="progress-minimal">
                            <div class="bg-warning h-100"
                                style="width: <?= ($stats_produksi->total_queue > 0) ? ($stats_produksi->processing / $stats_produksi->total_queue) * 100 : 0 ?>%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="metric-card shadow-sm border-start border-4 border-primary">
                    <div class="health-label mb-2">Likuiditas Netto</div>
                    <h3 class="fw-800 mb-0 <?= $is_surplus ? 'text-primary' : 'text-danger' ?>">Rp
                        <?= number_format($net_liquidity, 0, ',', '.') ?>
                    </h3>
                    <div class="mt-3">
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">Piutang:</span>
                                <span class="text-primary fw-bold">Rp
                                    <?= number_format($total_piutang, 0, ',', '.') ?></span>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">Hutang:</span>
                                <span class="text-danger fw-bold">Rp
                                    <?= number_format($summary_hutang->total_sisa_hutang, 0, ',', '.') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="metric-card shadow-sm">
                    <div class="health-label mb-2">Efisiensi Terminal</div>
                    <h3 class="fw-800 mb-0"><?= $stats_produksi->avg_lead_time ?> Jam</h3>
                    <div class="mt-3 py-2 px-3 bg-light rounded-pill">
                        <span class="small fw-bold text-muted"><i class="bi bi-clock-history me-1"></i> Rerata SPK
                            Selesai</span>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="metric-card shadow-sm">
                    <div class="health-label mb-2">Kesehatan Absensi</div>
                    <h3 class="fw-800 mb-0 text-success"><?= $attendance_rate ?>%</h3>
                    <div class="mt-3 d-flex align-items-center gap-2">
                        <div class="flex-grow-1 progress-minimal">
                            <div class="bg-success h-100" style="width: <?= $attendance_rate ?>%"></div>
                        </div>
                        <span
                            class="small fw-800 text-muted"><?= $attendance_rate > 90 ? 'Aman' : ($attendance_rate > 70 ? 'Waspada' : 'Kritis') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <!-- Consolidated Sales Trend -->
            <div class="col-lg-8">
                <div class="analytics-card">
                    <div class="analytics-header">
                        <div class="analytics-title">Tren Pendapatan Tahunan</div>
                    </div>
                    <div style="height: 350px;">
                        <canvas id="salesTrendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Branch Performance Ranking -->
            <div class="col-lg-4">
                <div class="analytics-card">
                    <div class="analytics-title mb-4">Kontribusi Cabang (Bulan Ini)</div>
                    <div class="branch-list mt-2">
                        <?php
                        $max_rev = 0;
                        foreach ($branch_performance as $bp) {
                            if ($bp->revenue > $max_rev)
                                $max_rev = $bp->revenue;
                        }
                        ?>
                        <?php foreach ($branch_performance as $bp): ?>
                            <?php $percent = $max_rev > 0 ? ($bp->revenue / $max_rev) * 100 : 0; ?>
                            <div class="branch-row">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-700 text-dark"><?= $bp->nama_perusahaan ?></span>
                                    <span class="small fw-800 text-primary">Rp
                                        <?= number_format($bp->revenue, 0, ',', '.') ?></span>
                                </div>
                                <div class="progress-minimal">
                                    <div class="bg-primary h-100" style="width: <?= $percent ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4 pt-4 border-top">
                        <a href="<?= site_url('laporan/penjualan') ?>"
                            class="btn btn-primary w-100 rounded-pill fw-bold"
                            style="background: var(--exec-primary); border: none;">DETAIL LAPORAN CABANG</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-time Daily Income Report (Pemasukan Harian) -->
        <div class="row g-4 mb-5">
            <div class="col-xl-8">
                <div class="analytics-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="analytics-title mb-0">
                            <i class="bi bi-receipt-cutoff text-primary me-2"></i>PEMASUKAN HARI INI (REAL-TIME)
                        </div>
                        <a href="<?= site_url('laporan/pemasukan_harian') ?>"
                            class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                            <i class="bi bi-arrow-right-circle me-1"></i>SEMUA DATA
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table premium-table mb-0">
                            <thead>
                                <tr>
                                    <th>JAM</th>
                                    <th>KONSUMEN</th>
                                    <th>NO. SPK</th>
                                    <th>CABANG</th>
                                    <th class="text-end">NOMINAL</th>
                                    <th class="text-center">KASIR</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pemasukan_hariini_list)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted fw-bold">
                                            <i class="bi bi-hourglass-split d-block fs-2 mb-2"></i>
                                            Belum ada transaksi hari ini
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($pemasukan_hariini_list as $row): ?>
                                        <tr>
                                            <td class="small fw-600"><?= date('H:i', strtotime($row->jam)) ?></td>
                                            <td>
                                                <div class="fw-700 text-dark"><?= $row->konsumen ?></div>
                                            </td>
                                            <td><span class="badge bg-light text-dark fw-bold border"><?= $row->nospk ?></span>
                                            </td>
                                            <td class="small fw-600 text-muted"><?= strtoupper($row->cabang) ?></td>
                                            <td class="text-end fw-800 text-success">Rp
                                                <?= number_format($row->bayar_hari_ini, 0, ',', '.') ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="small fw-bold text-muted"><?= strtoupper($row->kasir) ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="analytics-card h-100">
                    <div class="analytics-title mb-4">
                        <i class="bi bi-wallet2 text-success me-2"></i>KAS HARI INI
                    </div>
                    <div class="d-flex flex-column gap-3">
                        <?php if (empty($metode_hariini)): ?>
                            <div class="p-4 bg-light rounded-4 text-center text-muted small fw-bold">
                                Belum ada aliran kas masuk
                            </div>
                        <?php else: ?>
                            <?php foreach ($metode_hariini as $m): ?>
                                <div
                                    class="p-3 rounded-4 border bg-white shadow-sm d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="icon-box-sm bg-primary bg-opacity-10 text-primary">
                                            <i
                                                class="bi <?= (strtoupper($m->metode) == 'TUNAI') ? 'bi-cash-coin' : 'bi-credit-card' ?>"></i>
                                        </div>
                                        <div>
                                            <div class="small fw-800 text-muted text-uppercase"><?= $m->metode ?></div>
                                            <div class="h6 mb-0 fw-900 text-dark">Rp
                                                <?= number_format($m->total, 0, ',', '.') ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <?php
                                        // $total_today_kas calculated earlier in similar logic but let's be safe
                                        $total_today_kas_m = array_sum(array_column($metode_hariini, 'total'));
                                        $method_percent = ($total_today_kas_m > 0) ? round(($m->total / $total_today_kas_m) * 100, 1) : 0;
                                        ?>
                                        <span class="badge bg-light text-primary fw-bold"><?= $method_percent ?>%</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <div class="p-4 rounded-4 mt-2"
                                style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                                <div class="text-white-50 small fw-700 text-uppercase mb-1">TOTAL KAS MASUK</div>
                                <div class="text-white h3 fw-900 mb-0">Rp
                                    <?= number_format($total_today_kas_m, 0, ',', '.') ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <!-- VIP Customer Insights -->
            <div class="col-lg-6">
                <div class="analytics-card">
                    <div class="analytics-title mb-4"><i class="fas fa-award text-warning me-2"></i>Pelanggan VIP
                        (Profit
                        Terbesar)</div>
                    <div class="table-responsive">
                        <table class="table premium-table">
                            <thead>
                                <tr>
                                    <th>KONSUMEN</th>
                                    <th class="text-center">TRX</th>
                                    <th class="text-end">TOTAL BELANJA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_customers_value as $top): ?>
                                    <tr>
                                        <td class="fw-700 text-dark"><?= $top->konsumen_nama ?></td>
                                        <td class="text-center"><span
                                                class="badge bg-light text-dark fw-bold"><?= $top->count_trx ?></span>
                                        </td>
                                        <td class="text-end fw-800 text-primary">Rp
                                            <?= number_format($top->total_spent, 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="analytics-card">
                    <div class="analytics-title mb-4"><i class="fas fa-heart text-danger me-2"></i>Pelanggan Loyal
                        (Frekuensi
                        Tertinggi)</div>
                    <div class="table-responsive">
                        <table class="table premium-table">
                            <thead>
                                <tr>
                                    <th>KONSUMEN</th>
                                    <th class="text-center">KUNJUNGAN</th>
                                    <th class="text-end">NILAI TRX</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_customers_freq as $top): ?>
                                    <tr>
                                        <td class="fw-700 text-dark"><?= $top->konsumen_nama ?></td>
                                        <td class="text-center"><span
                                                class="badge bg-success bg-opacity-10 text-success fw-bold"><?= $top->count_trx ?>
                                                Hari</span></td>
                                        <td class="text-end fw-800 text-muted">Rp
                                            <?= number_format($top->total_spent, 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Production & Inventory Pulse -->
            <div class="col-lg-5">
                <div class="analytics-card">
                    <div class="analytics-title mb-4">Hub Produksi & Stok</div>
                    <div class="mb-4">
                        <div class="small fw-700 text-muted mb-2">STOK MATERIAL KRITIS:</div>
                        <?php if (empty($critical_materials)): ?>
                            <div class="p-3 bg-light rounded text-center small fw-bold text-success">Semua stok aman <i
                                    class="fas fa-check-circle"></i></div>
                        <?php else: ?>
                            <?php foreach ($critical_materials as $mat): ?>
                                <?php
                                    $konv = (floatval($mat->konversi) > 0) ? floatval($mat->konversi) : 1;
                                    $qty_tampil = (float)$mat->total_qty / $konv;
                                    $qty_formatted = (floor($qty_tampil) == $qty_tampil) ? number_format($qty_tampil, 0, ',', '.') : number_format($qty_tampil, 2, ',', '.');
                                ?>
                                <div
                                    class="d-flex justify-content-between align-items-center mb-2 px-2 py-1 bg-light rounded shadow-sm">
                                    <div class="d-flex flex-column">
                                        <span class="small fw-600"><?= htmlspecialchars($mat->varian) ?></span>
                                        <span class="text-muted" style="font-size: 0.65rem;"><i class="fas fa-map-marker-alt text-danger me-1"></i> <?= htmlspecialchars($mat->nama_perusahaan ?? 'Semua Gudang') ?></span>
                                    </div>
                                    <span class="badge bg-danger fw-bold"><?= $qty_formatted ?> <?= htmlspecialchars(strtoupper($mat->satuan_nama)) ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4 pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="small fw-700 text-muted text-uppercase">Log Konsumsi (Real-time):</div>
                            <a href="<?= base_url('wip/konsumsi_bahan') ?>" class="btn btn-xs btn-outline-primary"
                                style="font-size:0.65rem; font-weight:700;">Lihat Semua</a>
                        </div>

                        <?php if (empty($live_consumption)): ?>
                            <div class="p-3 bg-light rounded text-center small fw-bold text-muted">Belum ada log penggunaan
                                bahan hari ini
                                <i class="fas fa-info-circle"></i>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                                <table class="table table-sm premium-table mb-0" style="font-size: 0.75rem;">
                                    <tbody>
                                        <?php foreach ($live_consumption as $dc): ?>
                                            <?php
                                            // Calculate usage in production unit
                                            $qty_cut_gudang = abs((float) $dc->qty_out);
                                            $konv = floatval($dc->konversi) > 0 ? floatval($dc->konversi) : 1;
                                            $qty_produksi = $qty_cut_gudang * $konv;

                                            if (preg_match('/Pakai:\s*([\d\.]+)/i', $dc->catatan, $matches)) {
                                                $qty_produksi = (float) $matches[1];
                                            }
                                            ?>
                                            <tr>
                                                <td class="ps-1 py-2" style="width: 60px;">
                                                    <div class="text-muted" style="font-size: 0.65rem;">
                                                        <?= date('H:i', strtotime($dc->created_at)) ?>
                                                    </div>
                                                </td>
                                                <td class="py-2">
                                                    <div class="fw-bold text-dark text-truncate" style="max-width: 140px;">
                                                        <?= $dc->material_nama ?>
                                                    </div>
                                                    <div class="text-muted text-truncate"
                                                        style="font-size: 0.65rem; max-width: 140px;">
                                                        <i
                                                            class="fas fa-user-circle me-1"></i><?= explode(' ', $dc->operator_nama)[0] ?>
                                                    </div>
                                                </td>
                                                <td class="text-end pe-1 py-2">
                                                    <div class="fw-900 text-danger">
                                                        -<?= number_format($qty_produksi, ($qty_produksi == floor($qty_produksi) ? 0 : 2)) ?>
                                                        <span class="text-muted"
                                                            style="font-size: 0.65rem; font-weight:normal;"><?= $dc->satuan_produksi ?></span>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Distribusi pembayaran dipindah ke atas -->
                </div>
            </div>

            <!-- Discipline Analytics -->
            <div class="col-lg-4">
                <div class="analytics-card">
                    <div class="analytics-title mb-4">Monitoring Kedisiplinan Karyawan</div>
                    <div class="row">
                        <div class="col-md-6 border-end">
                            <div class="small fw-800 text-success mb-3 text-center">PALING RAJIN</div>
                            <?php foreach ($karyawan_rajin as $k): ?>
                                <div class="d-flex align-items-center mb-3">
                                    <img src="<?= $k->foto ? base_url('assets/images/karyawan/' . $k->foto) : base_url('assets/img/user.png') ?>"
                                        class="avatar-exec me-3" loading="lazy">
                                    <div class="flex-grow-1">
                                        <div class="small fw-700 text-dark"><?= $k->nama_lengkap ?></div>
                                        <div class="progress-minimal mt-1">
                                            <div class="bg-success h-100" style="width: 100%"></div>
                                        </div>
                                    </div>
                                    <span class="ms-3 small fw-800 text-success"><?= $k->total_hadir ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="col-md-6">
                            <div class="small fw-800 text-danger mb-3 text-center">BUTUH PEMBINAAN</div>
                            <?php if (empty($karyawan_malas)): ?>
                                <div class="text-center py-5 text-muted small fw-bold">Tidak ada indikasi indisipliner.
                                </div>
                            <?php else: ?>
                                <?php foreach ($karyawan_malas as $k): ?>
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="<?= $k->foto ? base_url('assets/images/karyawan/' . $k->foto) : base_url('assets/img/user.png') ?>"
                                            class="avatar-exec me-3" loading="lazy">
                                        <div class="flex-grow-1">
                                            <div class="small fw-700 text-dark"><?= $k->nama_lengkap ?></div>
                                            <div class="small text-danger fw-bold" style="font-size: 0.65rem;">
                                                <?= $k->total_alpha > 0 ? $k->total_alpha . 'x Alpha' : '' ?>
                                                <?= ($k->total_alpha > 0 && $k->total_telat > 0) ? ' | ' : '' ?>
                                                <?= $k->total_telat > 0 ? $k->total_telat . 'm Telat' : '' ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="analytics-card">
                    <div class="analytics-title mb-3">
                        <i class="fas fa-rocket text-primary me-2"></i> Update Fitur Sistem Terbaru
                    </div>
                    <div class="d-flex flex-column gap-3">
                        <?php if (empty($roadmap_updates)): ?>
                            <div class="p-3 bg-light rounded-4 text-center small text-muted">Belum ada update fitur terbaru.</div>
                        <?php else: ?>
                            <?php 
                            $colors = ['primary', 'success', 'warning', 'info', 'danger'];
                            $icons = ['fa-sync-alt', 'fa-chart-pie', 'fa-check-double', 'fa-rocket', 'fa-star'];
                            $i = 0;
                            foreach ($roadmap_updates as $ru): 
                                $color = $colors[$i % count($colors)];
                                $icon = $icons[$i % count($icons)];
                            ?>
                                <div class="p-3 bg-light rounded-4 border-start border-4 border-<?= $color ?>">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-dark"><i class="fas <?= $icon ?> text-<?= $color ?> me-2"></i><?= htmlspecialchars($ru->feature_name) ?></span>
                                        <span class="badge bg-<?= $color ?> <?= $color == 'warning' ? 'text-dark' : '' ?> rounded-pill">Update</span>
                                    </div>
                                    <div class="small text-muted"><?= htmlspecialchars($ru->description) ?></div>
                                </div>
                            <?php 
                                $i++;
                            endforeach; 
                            ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- End container-fluid -->
</div> <!-- End exec-dashboard -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Category Mix Chart (Profit Centers)
    const ctxCategory = document.getElementById('categoryMixChart').getContext('2d');
    const categoryLabels = <?= json_encode(array_map(function ($c) {
        return $c->label;
    }, $category_contribution)) ?>;
    const categoryValues = <?= json_encode(array_map(function ($c) {
        return (float) $c->value;
    }, $category_contribution)) ?>;

    new Chart(ctxCategory, {
        type: 'pie',
        data: {
            labels: categoryLabels,
            datasets: [{
                data: categoryValues,
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#64748b'],
                borderWidth: 0,
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: { boxWidth: 10, font: { size: 9 } }
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            let label = context.label || '';
                            let value = context.raw || 0;
                            return label + ': Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
    // Sales Trend Chart
    const ctxTrend = document.getElementById('salesTrendChart').getContext('2d');
    const salesLabels = <?= json_encode($chart_labels ?? []) ?>;
    const rawBranchData = <?= json_encode($chart_data_branches ?? []) ?>;

    const branchColors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
    const datasets = rawBranchData.map((branch, index) => {
        return {
            label: branch.label,
            data: branch.data,
            borderColor: branchColors[index % branchColors.length],
            backgroundColor: branchColors[index % branchColors.length] + '1A',
            tension: 0.4,
            borderWidth: 3,
            pointRadius: 4,
            pointBackgroundColor: '#fff',
            fill: false
        };
    });

    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: salesLabels,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 8
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    grid: { borderDash: [5, 5] },
                    ticks: {
                        callback: v => 'Rp ' + v.toLocaleString('id-ID')
                    }
                },
                x: { grid: { display: false } }
            }
        }
    });
    // Payment Mix Chart
    const ctxMix = document.getElementById('paymentMixChart').getContext('2d');
    const mixLabels = <?= json_encode(array_map(function ($m) {
        return $m->metode;
    }, $metode_today)) ?>;
    const mixData = <?= json_encode(array_map(function ($m) {
        return (float) $m->total;
    }, $metode_today)) ?>;

    new Chart(ctxMix, {
        type: 'pie',
        data: {
            labels: mixLabels,
            datasets: [{
                data: mixData,
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#64748b'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: { boxWidth: 10, font: { size: 9 } }
                }
            }
        }
    });
</script>