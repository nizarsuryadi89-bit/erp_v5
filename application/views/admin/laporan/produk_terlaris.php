<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    /* ==========================================================================
       PREMIUM DESIGN SYSTEM - BEST SELLING MODULE
       ========================================================================== */
    :root {
        --primary-gradient: linear-gradient(135deg, #1a1c23 0%, #2c3e50 100%);
        --success-gradient: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
        --info-gradient: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        --warning-gradient: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);
        --danger-gradient: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        
        /* Medal Colors */
        --gold-gradient: linear-gradient(135deg, #FFD700 0%, #DAA520 100%);
        --silver-gradient: linear-gradient(135deg, #C0C0C0 0%, #808080 100%);
        --bronze-gradient: linear-gradient(135deg, #CD7F32 0%, #8B4513 100%);
    }

    .report-section-card {
        background: white;
        border-radius: 20px;
        border: none;
        box-shadow: var(--premium-shadow);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .section-header {
        background: var(--primary-gradient);
        padding: 1.5rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
    }

    .section-header h5 {
        margin: 0;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-header i {
        background: rgba(255, 255, 255, 0.2);
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 1.2rem;
    }

    /* Stats v2 */
    .summary-stat-card {
        background: white;
        padding: 2rem;
        border-radius: 25px;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        position: relative;
        transition: all 0.3s ease;
        border: 1px solid #f1f5f9;
        height: 100%;
    }

    .summary-stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
    }

    .stat-v2-icon {
        width: 60px;
        height: 60px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .stat-v2-label {
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
    }

    .stat-v2-value {
        font-size: 1.75rem;
        font-weight: 900;
        color: #1a1c23;
        line-height: 1.2;
    }

    /* Table Styles */
    .premium-table thead th {
        background: #f8fafc;
        border: none;
        color: #475569;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 1px;
        padding: 1.25rem 1.5rem;
    }

    .premium-table tbody td {
        padding: 1.25rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-weight: 500;
        color: #334155;
    }

    .rank-indicator {
        width: 35px;
        height: 35px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.9rem;
    }

    /* Period Buttons */
    .btn-period-premium {
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none !important;
    }

    .btn-period-premium.active {
        background: #1a1c23;
        color: white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .btn-period-premium:not(.active) {
        background: #f1f5f9;
        color: #64748b;
    }

    .btn-period-premium:hover:not(.active) {
        background: #e2e8f0;
        color: #1a1c23;
    }

    .id-badge {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.75rem;
        background: #f1f5f9;
        padding: 2px 8px;
        border-radius: 4px;
        color: #64748b;
    }
</style>

<div class="app-content mt-n4">
    <div class="container-fluid">
        <!-- Dashboard Filters -->
        <div class="report-section-card mb-4" style="background: rgba(255,255,255,0.9); backdrop-filter: blur(10px);">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h4 class="fw-900 text-dark mb-0 d-flex align-items-center gap-3">
                            <div class="bg-warning bg-opacity-10 p-2 rounded-3">
                                <i class="fas fa-crown text-warning"></i>
                            </div>
                            ANALISIS PRODUK TERLARIS
                        </h4>
                    </div>
                    <div class="col-lg-6 text-end">
                        <div class="d-inline-flex gap-2">
                            <a href="?period=month&perusahaan_id=<?= $current_perusahaan_id ?>"
                                class="btn-period-premium <?= $selected_period == 'month' ? 'active' : '' ?>">
                                <i class="fas fa-calendar-alt"></i> BULAN INI
                            </a>
                            <a href="?period=year&perusahaan_id=<?= $current_perusahaan_id ?>"
                                class="btn-period-premium <?= $selected_period == 'year' ? 'active' : '' ?>">
                                <i class="fas fa-history"></i> TAHUN INI
                            </a>
                            <a href="?period=all&perusahaan_id=<?= $current_perusahaan_id ?>"
                                class="btn-period-premium <?= $selected_period == 'all' ? 'active' : '' ?>">
                                <i class="fas fa-layer-group"></i> SEMUA WAKTU
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (empty($top_products)): ?>
            <div class="report-section-card p-5 text-center">
                <div class="opacity-20 mb-3">
                    <i class="fas fa-box-open fa-5x"></i>
                </div>
                <h4 class="fw-800 text-muted">Data penjualan tidak ditemukan</h4>
                <p class="text-muted">Belum ada aktivitas penjualan pada periode yang Anda pilih.</p>
                <a href="<?= base_url('penjualan') ?>" class="btn btn-primary rounded-pill px-4 mt-3 fw-bold">
                    KEMBALI KE DASHBOARD
                </a>
            </div>
        <?php else: ?>
            <!-- TOP 3 PODIUM -->
            <div class="row g-4 mb-4">
                <?php 
                $ranks = [
                    1 => ['gradient' => 'var(--gold-gradient)', 'label' => 'GOLD MEDAL', 'icon' => 'fa-trophy'],
                    2 => ['gradient' => 'var(--silver-gradient)', 'label' => 'SILVER MEDAL', 'icon' => 'fa-medal'],
                    3 => ['gradient' => 'var(--bronze-gradient)', 'label' => 'BRONZE MEDAL', 'icon' => 'fa-award']
                ];
                $rank_idx = 1;
                foreach (array_slice($top_products, 0, 3) as $tp): 
                    $style = $ranks[$rank_idx];
                ?>
                <div class="col-md-4">
                    <div class="summary-stat-card shadow-sm">
                        <div class="stat-v2-icon text-white" style="background: <?= $style['gradient'] ?>">
                            <i class="fas <?= $style['icon'] ?>"></i>
                        </div>
                        <div>
                            <div class="stat-v2-label"><?= $style['label'] ?> - RANK #<?= $rank_idx ?></div>
                            <div class="stat-v2-value text-dark fw-900 mt-2">
                                <?= strtoupper($tp->product_nama) ?>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top border-light">
                            <div class="text-success fw-900 fs-5">
                                <?= number_format($tp->total_sold) ?> <span class="small fw-bold text-muted"><?= strtoupper($tp->satuan_nama ?: 'PCS') ?></span>
                            </div>
                            <div class="badge bg-light text-dark border fw-bold px-3 py-2">
                                <?= number_format($tp->transaction_count) ?> TRX
                            </div>
                        </div>
                    </div>
                </div>
                <?php $rank_idx++; endforeach; ?>
            </div>

            <!-- RANKING LIST -->
            <div class="report-section-card">
                <div class="section-header">
                    <h5><i class="fas fa-list-ol"></i> DAFTAR PERINGKAT PRODUK LENGKAP</h5>
                </div>
                <div class="table-responsive">
                    <table class="table premium-table mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" width="8%">RANK</th>
                                <th width="35%">DETAIL PRODUK</th>
                                <th class="text-center" width="15%">VOLUME TERJUAL</th>
                                <th class="text-end" width="20%">ESTIMASI OMSET</th>
                                <th class="text-center" width="12%">FREK. TRX</th>
                                <th class="text-center" width="10%">POSISI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $rank = 1; foreach ($top_products as $tp): ?>
                            <tr>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <?php 
                                        $bg = 'background: #f1f5f9; color: #64748b;';
                                        if($rank == 1) $bg = 'background: var(--gold-gradient); color: white;';
                                        if($rank == 2) $bg = 'background: var(--silver-gradient); color: white;';
                                        if($rank == 3) $bg = 'background: var(--bronze-gradient); color: white;';
                                        ?>
                                        <div class="rank-indicator shadow-sm" style="<?= $bg ?>">
                                            <?= $rank ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-800 text-dark"><?= $tp->product_nama ?></div>
                                    <div class="id-badge mt-1 d-inline-block">PID-<?= str_pad($tp->product_id, 5, '0', STR_PAD_LEFT) ?></div>
                                </td>
                                <td class="text-center">
                                    <div class="fw-900 text-primary">
                                        <?= number_format($tp->total_sold) ?> 
                                        <span class="small text-muted fw-bold"><?= $tp->satuan_nama ?: 'PCS' ?></span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="fw-900 text-success">
                                        Rp <?= number_format($tp->total_revenue, 0, ',', '.') ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-3 py-2 fw-800">
                                        <?= number_format($tp->transaction_count) ?> <i class="fas fa-exchange-alt ms-1 opacity-50"></i>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if($rank <= 3): ?>
                                        <i class="fas fa-chevron-up text-success scale-up"></i>
                                    <?php else: ?>
                                        <i class="fas fa-minus text-muted"></i>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php $rank++; endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>