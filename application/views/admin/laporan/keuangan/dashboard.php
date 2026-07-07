<style>
    /* Ensure consistency with the main report page */
    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 0;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #2c3e50, #34495e);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .stat-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .stat-icon.income {
        background: linear-gradient(135deg, #28a745, #20c997);
    }

    .stat-icon.profit {
        background: linear-gradient(135deg, #17a2b8, #6f42c1);
    }

    .stat-icon.expense {
        background: linear-gradient(135deg, #ffc107, #fd7e14);
    }

    .stat-icon.info {
        background: linear-gradient(135deg, #3498db, #2980b9);
    }

    .stat-info h3 {
        font-size: 2.2rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
        line-height: 1;
    }

    .stat-info p {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    /* Report Card Style for Charts/Tables */
    .report-section-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .section-header {
        background: linear-gradient(135deg, #2c3e50, #34495e);
        color: white;
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .section-header h5 {
        margin: 0;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .section-body {
        padding: 1.5rem;
    }

    .table {
        border-radius: 0;
    }

    .table th {
        background: #f8f9fa;
        color: #2c3e50;
        font-weight: 600;
        border-color: #e9ecef;
    }

    .table td {
        border-color: #e9ecef;
        vertical-align: middle;
    }

    .btn-success {
        background: linear-gradient(135deg, #28a745, #20c997);
        border: none;
        border-radius: 0;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(40, 167, 69, 0.4);
    }

    .btn-primary {
        background: linear-gradient(135deg, #2c3e50, #34495e);
        border: none;
        border-radius: 0;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
    }

    /* Quick Nav Cards */
    .nav-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
        text-decoration: none !important;
        display: block;
        height: 100%;
        position: relative;
    }

    .nav-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #2c3e50, #34495e);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .nav-card:hover::before {
        opacity: 1;
    }

    .nav-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        border-color: #2c3e50;
    }

    .nav-icon {
        width: 60px;
        height: 60px;
        margin: 0 auto 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: #2c3e50;
        background: #f8f9fa;
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .nav-card:hover .nav-icon {
        background: linear-gradient(135deg, #2c3e50, #34495e);
        color: white;
    }

    .nav-title {
        font-weight: 700;
        color: #2c3e50;
    }

    @media (max-width: 768px) {
        .stats-overview {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="app-content">
    <!-- Header Modul -->
    <?php $this->load->view('layout_admin/header_modul'); ?>

    <!-- <div class="container-fluid"> -->
    <!-- Branch Selection (Consistent with index.php) -->
    <?php if ($is_admin_owner && !empty($all_perusahaan)): ?>
        <div class="card border-0 shadow-sm mb-4"
            style="border-radius: 15px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-1 fw-bold text-dark"><i class="fas fa-building me-2 text-primary"></i> Pilih
                            Cabang / Perusahaan</h5>
                        <p class="text-muted small mb-0">Tampilkan data laporan berdasarkan unit bisnis yang dipilih.
                        </p>
                    </div>
                    <div class="col-md-6 mt-3 mt-md-0">
                        <form action="<?= base_url('laporan/dashboard_terpadu') ?>" method="GET" id="perusahaanForm">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-store text-muted"></i></span>
                                <select class="form-select border-start-0 ps-0 fw-bold" name="perusahaan_id"
                                    onchange="this.form.submit()" style="height: 50px;">
                                    <?php foreach ($all_perusahaan as $p): ?>
                                        <option value="<?= $p->id_perusahaan ?>" <?= ($current_perusahaan_id == $p->id_perusahaan) ? 'selected' : '' ?>>
                                            <?= $p->nama_perusahaan ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0; border: 1px solid #e9ecef;">
        <div class="card-body p-3">
            <form id="dateFilterForm" class="row g-3 align-items-end">
                <input type="hidden" name="perusahaan_id" value="<?= $current_perusahaan_id ?>">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">TANGGAL MULAI</label>
                    <input type="date" class="form-control" name="mulai" value="<?= $tanggal_mulai ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">TANGGAL AKHIR</label>
                    <input type="date" class="form-control" name="selesai" value="<?= $tanggal_selesai ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">PRESET</label>
                    <select class="form-select" onchange="setPeriod(this.value)">
                        <option value="">Pilih...</option>
                        <option value="today">Hari Ini</option>
                        <option value="week">Minggu Ini</option>
                        <option value="month">Bulan Ini</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i
                                class="fas fa-search me-2"></i>Tampilkan</button>
                        <button type="button" class="btn btn-success" onclick="exportDashboard()"><i
                                class="fas fa-file-excel me-2"></i>Export</button>
                        <button type="button" class="btn btn-secondary" onclick="resetFilter()"><i
                                class="fas fa-undo"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Overview (Exact same structure as index.php) -->
    <div class="stats-overview">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon income">
                    <i class="fa fa-wallet"></i>
                </div>
                <div class="stat-info">
                    <h3>Rp <?= number_format($financial_summary['total_pendapatan'], 0, ',', '.') ?></h3>
                    <p>Total Pendapatan</p>
                </div>
            </div>
            <div class="mt-2 pt-2 border-top">
                <small
                    class="fw-bold <?= $financial_summary['pendapatan_growth'] >= 0 ? 'text-success' : 'text-danger' ?>">
                    <i
                        class="fa <?= $financial_summary['pendapatan_growth'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' ?> me-1"></i>
                    <?= abs($financial_summary['pendapatan_growth']) ?>%
                </small>
                <small class="text-muted ms-1">vs periode lalu</small>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon expense">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="stat-info">
                    <h3>Rp <?= number_format($financial_summary['total_pengeluaran'], 0, ',', '.') ?></h3>
                    <p>Total Pengeluaran</p>
                </div>
            </div>
            <div class="mt-2 pt-2 border-top">
                <small
                    class="fw-bold <?= $financial_summary['pengeluaran_growth'] <= 0 ? 'text-success' : 'text-danger' ?>">
                    <i
                        class="fa <?= $financial_summary['pengeluaran_growth'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' ?> me-1"></i>
                    <?= abs($financial_summary['pengeluaran_growth']) ?>%
                </small>
                <small class="text-muted ms-1">vs periode lalu</small>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon profit">
                    <i class="fa fa-percent"></i>
                </div>
                <div class="stat-info">
                    <h3><?= number_format($financial_summary['profit_margin'], 1) ?>%</h3>
                    <p>Profit Margin</p>
                </div>
            </div>
            <div class="mt-2">
                <div class="progress" style="height: 6px; border-radius: 0;">
                    <div class="progress-bar bg-info" role="progressbar"
                        style="width: <?= min(100, $financial_summary['profit_margin']) ?>%"></div>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon info">
                    <i class="fa fa-exchange-alt"></i>
                </div>
                <div class="stat-info">
                    <h3><?= number_format($financial_summary['jumlah_transaksi'], 0, ',', '.') ?></h3>
                    <p>Transaksi</p>
                </div>
            </div>
            <div class="mt-2 pt-2 border-top">
                <small class="text-muted">Rata-rata Harian:
                    <strong><?= $financial_summary['avg_transaction'] ?></strong></small>
            </div>
        </div>
    </div>

    <!-- Branch Performance Analysis Section -->
    <?php if (!empty($branch_comparison)): ?>
        <div class="report-section-card mb-4"
            style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <div class="section-header py-3"
                style="background: linear-gradient(135deg, #0f2027, #203a43, #2c5364); border-radius: 12px 12px 0 0;">
                <h5 class="mb-0 fw-bold"><i class="fa fa-chart-bar me-2"></i>ANALISIS PERFORMA ANTAR CABANG</h5>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-white text-dark"><?= count($branch_comparison['labels']) ?> UNIT BISNIS</span>
                </div>
            </div>
            <div class="section-body p-4">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="p-3 bg-light rounded-3" style="height: 400px;">
                            <canvas id="branchComparisonChart"></canvas>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="p-3 bg-light rounded-3" style="height: 400px;">
                            <canvas id="branchProfitShareChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Charts and Tables -->
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="report-section-card h-100">
                <div class="section-header">
                    <h5><i class="fa fa-chart-area me-2"></i>Trend Keuangan</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light py-0" type="button" data-bs-toggle="dropdown"><i
                                class="fa fa-ellipsis-v"></i></button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="updateChart('daily')">Harian</a></li>
                            <li><a class="dropdown-item" href="#" onclick="updateChart('weekly')">Mingguan</a></li>
                            <li><a class="dropdown-item" href="#" onclick="updateChart('monthly')">Bulanan</a></li>
                        </ul>
                    </div>
                </div>
                <div class="section-body">
                    <div style="height: 350px;">
                        <canvas id="revenueTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="report-section-card h-100">
                <div class="section-header">
                    <h5><i class="fa fa-chart-pie me-2"></i>Metode Pembayaran</h5>
                </div>
                <div class="section-body">
                    <div style="height: 350px;">
                        <canvas id="paymentMethodsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="report-section-card">
                <div class="section-header">
                    <h5><i class="fa fa-sync-alt me-2"></i>Analisis Arus Kas</h5>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th>Harian</th>
                                <th>Bulanan</th>
                                <th class="text-center">Trend</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cash_flow as $flow): ?>
                                <tr>
                                    <td class="fw-bold"><?= $flow['kategori'] ?></td>
                                    <td class="text-success fw-bold">Rp
                                        <?= number_format($flow['harian'], 0, ',', '.') ?>
                                    </td>
                                    <td>Rp <?= number_format($flow['bulanan'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <span class="badge <?= $flow['trend'] >= 0 ? 'bg-success' : 'bg-danger' ?>">
                                            <?= $flow['trend'] >= 0 ? '+' : '' ?>     <?= $flow['trend'] ?>%
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="report-section-card">
                <div class="section-header">
                    <h5><i class="fa fa-star me-2"></i>Performa Metode</h5>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th width="80">#</th>
                                <th>Metode</th>
                                <th>Nilai</th>
                                <th width="150">Kontribusi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($top_performers as $index => $performer): ?>
                                <tr>
                                    <td class="text-center"><?= $index + 1 ?></td>
                                    <td class="fw-bold"><?= $performer['kategori'] ?></td>
                                    <td>Rp <?= number_format($performer['nilai'], 0, ',', '.') ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar bg-primary" role="progressbar"
                                                    style="width: <?= $performer['persentase'] ?>%"></div>
                                            </div>
                                            <span
                                                class="small fw-bold"><?= number_format($performer['persentase'], 1) ?>%</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Navigation (Consistent with report-card style) -->
    <h5 class="fw-bold mb-3 mt-4 text-dark"><i class="fa fa-directions me-2"></i>NAVIGASI CEPAT</h5>
    <div class="row g-4 mb-5">
        <div class="col-6 col-md-3">
            <a href="<?= base_url('laporan/rekap_metode_pembayaran?perusahaan_id=' . $current_perusahaan_id) ?>"
                class="nav-card">
                <div class="nav-icon"><i class="fa fa-cash-register"></i></div>
                <div class="nav-title">Rekap Kasir</div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="<?= base_url('laporan/pemasukan_harian?perusahaan_id=' . $current_perusahaan_id) ?>"
                class="nav-card">
                <div class="nav-icon"><i class="fa fa-money-bill-wave"></i></div>
                <div class="nav-title">Pemasukan</div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="<?= base_url('laporan/index?perusahaan_id=' . $current_perusahaan_id) ?>" class="nav-card">
                <div class="nav-icon"><i class="fa fa-home"></i></div>
                <div class="nav-title">Pusat Laporan</div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="<?= base_url('laporan/pengeluaran?perusahaan_id=' . $current_perusahaan_id) ?>" class="nav-card">
                <div class="nav-icon"><i class="fa fa-shopping-basket"></i></div>
                <div class="nav-title">Pengeluaran</div>
            </a>
        </div>
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Chart.defaults.color = '#6c757d';
        Chart.defaults.font.family = "'Inter', 'Segoe UI', sans-serif";

        initializeRevenueTrendChart();
        initializePaymentMethodsChart();

        <?php if (!empty($branch_comparison)): ?>
            initializeBranchComparisonChart();
            initializeBranchProfitShareChart();
        <?php endif; ?>
    });

    <?php if (!empty($branch_comparison)): ?>
        function initializeBranchComparisonChart() {
            const ctx = document.getElementById('branchComparisonChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($branch_comparison['labels']) ?>,
                    datasets: [
                        {
                            label: 'Pendapatan',
                            data: <?= json_encode($branch_comparison['pendapatan']) ?>,
                            backgroundColor: '#28a745',
                            borderRadius: 5,
                        },
                        {
                            label: 'Pengeluaran',
                            data: <?= json_encode($branch_comparison['pengeluaran']) ?>,
                            backgroundColor: '#dc3545',
                            borderRadius: 5,
                        },
                        {
                            label: 'Laba Bersih',
                            data: <?= json_encode($branch_comparison['laba']) ?>,
                            backgroundColor: '#007bff',
                            borderRadius: 5,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return context.dataset.label + ': Rp ' + context.raw.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    if (value >= 1000000) return 'Rp ' + (value / 1000000) + 'jt';
                                    return 'Rp ' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        function initializeBranchProfitShareChart() {
            const ctx = document.getElementById('branchProfitShareChart').getContext('2d');
            new Chart(ctx, {
                type: 'polarArea',
                data: {
                    labels: <?= json_encode($branch_comparison['labels']) ?>,
                    datasets: [{
                        data: <?= json_encode($branch_comparison['laba']) ?>,
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.7)',
                            'rgba(0, 123, 255, 0.7)',
                            'rgba(255, 193, 7, 0.7)',
                            'rgba(23, 162, 184, 0.7)',
                            'rgba(111, 66, 193, 0.7)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: { display: true, text: 'Distribusi Laba per Cabang', font: { size: 14, weight: 'bold' } },
                        legend: { position: 'bottom' }
                    }
                }
            });
        }
    <?php endif; ?>

    function initializeRevenueTrendChart() {
        const ctx = document.getElementById('revenueTrendChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($trend_analysis['labels']) ?>,
                datasets: [{
                    label: 'Pendapatan',
                    data: <?= json_encode($trend_analysis['pendapatan']) ?>,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Pengeluaran',
                    data: <?= json_encode($trend_analysis['pengeluaran']) ?>,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'top' } },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: v => 'Rp ' + v.toLocaleString() } }
                }
            }
        });
    }

    function initializePaymentMethodsChart() {
        const ctx = document.getElementById('paymentMethodsChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Cash', 'Transfer', 'EDC', 'E-Wallet'],
                datasets: [{
                    data: [
                        <?= $payment_methods['cash']['total'] ?? 0 ?>,
                        <?= $payment_methods['transfer']['total'] ?? 0 ?>,
                        <?= $payment_methods['edc']['total'] ?? 0 ?>,
                        <?= $payment_methods['ewallet']['total'] ?? 0 ?>
                    ],
                    backgroundColor: ['#28a745', '#007bff', '#ffc107', '#17a2b8']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    function setPeriod(period) {
        const today = new Date();
        let start, end;
        if (period === 'today') { start = end = today.toISOString().split('T')[0]; }
        else if (period === 'week') {
            const first = today.getDate() - today.getDay();
            start = new Date(today.setDate(first)).toISOString().split('T')[0];
            end = new Date(today.setDate(first + 6)).toISOString().split('T')[0];
        } else if (period === 'month') {
            start = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
            end = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().split('T')[0];
        }
        document.querySelector('input[name="mulai"]').value = start;
        document.querySelector('input[name="selesai"]').value = end;
        document.getElementById('dateFilterForm').submit();
    }

    function resetFilter() { window.location.href = '<?= base_url('laporan/dashboard_terpadu?perusahaan_id=' . $current_perusahaan_id) ?>'; }
    function exportDashboard() {
        const params = new URLSearchParams(new FormData(document.getElementById('dateFilterForm'))).toString();
        window.location.href = `<?= base_url('laporan/export_dashboard') ?>?${params}`;
    }
</script>