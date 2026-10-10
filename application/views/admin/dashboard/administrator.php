<style>
    .admin-dash-container {
        padding: 1.5rem 0;
    }

    .dash-section-title {
        font-weight: 800;
        color: #1e293b;
        letter-spacing: 1px;
        border-left: 5px solid #3b82f6;
        padding-left: 1rem;
        margin-bottom: 2rem;
    }

    .stat-widget {
        background: #ffffff;
        border-radius: 20px;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .stat-widget:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        border-color: rgba(59, 130, 246, 0.3);
    }

    .widget-icon-box {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1.25rem;
    }

    .bg-soft-primary {
        background: #eff6ff;
        color: #3b82f6;
    }

    .bg-soft-warning {
        background: #fffbeb;
        color: #f59e0b;
    }

    .bg-soft-success {
        background: #f0fdf4;
        color: #10b981;
    }

    .bg-soft-danger {
        background: #fef2f2;
        color: #ef4444;
    }

    .bg-soft-indigo {
        background: #eef2ff;
        color: #6366f1;
    }

    .stat-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }

    .stat-footer {
        margin-top: 1.5rem;
        border-top: 1px solid #f1f5f9;
        padding-top: 1rem;
    }

    .stat-link {
        font-size: 0.7rem;
        font-weight: 800;
        text-decoration: none;
        display: flex;
        align-items: center;
    }

    /* Gradient Accent Bars */
    .stat-widget::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: transparent;
    }

    .border-accent-primary::after {
        background: linear-gradient(to right, #3b82f6, #60a5fa);
    }

    .border-accent-warning::after {
        background: linear-gradient(to right, #f59e0b, #fbbf24);
    }

    .border-accent-success::after {
        background: linear-gradient(to right, #10b981, #34d399);
    }

    .border-accent-danger::after {
        background: linear-gradient(to right, #ef4444, #f87171);
    }
</style>

<div class="admin-dash-container">
    <div class="container-fluid">
        <!-- Section: Status Pesanan -->
        <h3 class="dash-section-title text-uppercase">Status Pesanan Real-Time</h3>

        <div class="row g-4 mb-5">
            <!-- Perlu Diproses -->
            <div class="col-md-3">
                <div class="stat-widget border-accent-danger">
                    <div class="widget-icon-box bg-soft-danger">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-label">Perlu Di Proses</div>
                    <div class="stat-value"><?= $total_belumbayar ?></div>
                    <div class="stat-footer">
                        <a href="<?= base_url('penjualan') ?>" class="stat-link text-danger">
                            LIHAT DAFTAR <i class="fas fa-chevron-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sedang Diproses -->
            <div class="col-md-3">
                <div class="stat-widget border-accent-warning">
                    <div class="widget-icon-box bg-soft-warning">
                        <i class="fas fa-sync-alt fa-spin-hover"></i>
                    </div>
                    <div class="stat-label">Sedang di Proses</div>
                    <div class="stat-value"><?= $total_sudahbayar ?></div>
                    <div class="stat-footer">
                        <a href="<?= base_url('penjualan') ?>" class="stat-link text-warning">
                            LIHAT DAFTAR <i class="fas fa-chevron-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Selesai Produksi -->
            <div class="col-md-3">
                <div class="stat-widget border-accent-primary">
                    <div class="widget-icon-box bg-soft-primary">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-label">Selesai Produksi</div>
                    <div class="stat-value">---</div>
                    <div class="stat-footer">
                        <a href="<?= base_url('penjualan/onday') ?>" class="stat-link text-primary">
                            LIHAT DETAIL <i class="fas fa-chevron-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Diambil Konsumen -->
            <div class="col-md-3">
                <div class="stat-widget border-accent-success">
                    <div class="widget-icon-box bg-soft-success">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <div class="stat-label">Diambil Konsumen</div>
                    <div class="stat-value">---</div>
                    <div class="stat-footer">
                        <a href="<?= base_url('penjualan') ?>" class="stat-link text-success">
                            LIHAT RIWAYAT <i class="fas fa-chevron-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section: Performa Toko -->
        <h3 class="dash-section-title text-uppercase">Analisa Performa Penjualan</h3>

        <div class="row g-4 mb-5">
            <!-- Penjualan Kemarin -->
            <div class="col-md-3">
                <div class="stat-widget border-accent-primary">
                    <div class="widget-icon-box bg-soft-primary">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-label">Omzet Kemarin</div>
                    <div class="stat-value text-primary" style="font-size: 1.4rem;">Rp.
                        <?= number_format($total_kemarin['total']) ?></div>
                    <div class="stat-footer">
                        <a href="<?= base_url('penjualan/onday/2') ?>" class="stat-link text-primary">LAPORAN DETAIL <i
                                class="fas fa-chevron-right ms-2"></i></a>
                    </div>
                </div>
            </div>

            <!-- Penjualan Hari Ini -->
            <div class="col-md-3">
                <div class="stat-widget border-accent-success"
                    style="background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);">
                    <div class="widget-icon-box bg-soft-success">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-label">Omzet Hari Ini</div>
                    <div class="stat-value text-success" style="font-size: 1.4rem;">Rp.
                        <?= number_format($total_trxday['total']) ?></div>
                    <div class="stat-footer">
                        <a href="<?= base_url('penjualan/onday/1') ?>" class="stat-link text-success">PANTAU LIVE <i
                                class="fas fa-chevron-right ms-2"></i></a>
                    </div>
                </div>
            </div>

            <!-- Produk Kemarin -->
            <div class="col-md-3">
                <div class="stat-widget border-accent-indigo">
                    <div class="widget-icon-box bg-soft-indigo">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div class="stat-label">Item Terjual (Kemarin)</div>
                    <div class="stat-value"><?= number_format($total_penj_kemarin['total']) ?> <small class="text-muted"
                            style="font-size: 0.8rem;">Pcs</small></div>
                    <div class="stat-footer">
                        <a href="<?= base_url('penjualan/onday/3') ?>" class="stat-link text-indigo">LIHAT LIST <i
                                class="fas fa-chevron-right ms-2"></i></a>
                    </div>
                </div>
            </div>

            <!-- Produk Hari Ini -->
            <div class="col-md-3">
                <div class="stat-widget border-accent-indigo">
                    <div class="widget-icon-box bg-soft-indigo">
                        <i class="fas fa-shopping-basket"></i>
                    </div>
                    <div class="stat-label">Item Terjual (Hari Ini)</div>
                    <div class="stat-value"><?= number_format($total_penj_hariini['total']) ?> <small class="text-muted"
                            style="font-size: 0.8rem;">Pcs</small></div>
                    <div class="stat-footer">
                        <a href="<?= base_url('penjualan/onday/3') ?>" class="stat-link text-indigo">LIHAT LIST <i
                                class="fas fa-chevron-right ms-2"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <div class="panel-actions">
                    <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                    <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                </div>

                <h2 class="panel-title">Performa Toko <?= $perusahaan ?></h2>
                <p class="panel-subtitle">Grafik Penjualan </p>
            </header>
            <div class="panel-body">

                <!-- Flot: Bars -->
                <div class="chart chart-md" id="flotBars"></div>
                <script type="text/javascript">
                    var flotBarsData = [
                        ["Jan", <?= $jan['total'] ?>],
                        ["Feb", <?= $feb['total'] ?>],
                        ["Mar", <?= $mar['total'] ?>],
                        ["Apr", <?= $apr['total'] ?>],
                        ["May", <?= $mei['total'] ?>],
                        ["Jun", <?= $jun['total'] ?>],
                        ["Jul", <?= $jul['total'] ?>],
                        ["Aug", <?= $ags['total'] ?>],
                        ["Sep", <?= $sept['total'] ?>],
                        ["Oct", <?= $okt['total'] ?>],
                        ["Nov", <?= $nov['total'] ?>],
                        ["Dec", <?= $des['total'] ?>]
                    ];

                    // See: assets/javascripts/ui-elements/examples.charts.js for more settings.

                </script>

            </div>
        </section>
    </div>

    </section>