<div class="app-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Dashboard Keuangan Semua Toko</h3>
            <div class="d-flex gap-2">
                <a href="<?= base_url('keuangan/export_transaksi_bulanan')?>" class="btn btn-success">
                    <i class="bi bi-file-excel me-1"></i>Export Keuangan Bulanan
                </a>
                 <a href="<?= base_url('transaksi/export_transaksi_bulanan')?>" class="btn btn-success">
                    <i class="bi bi-file-excel me-1"></i>Export Transaksi 
                </a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6>Total Pemasukan Hari Ini</h6>
                        <h3>Rp <?= number_format($total_daily_income) ?></h3>
                        <small>Dari semua toko</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6>Total Pemasukan Bulan Ini</h6>
                        <h3>Rp <?= number_format($total_monthly_income) ?></h3>
                        <small>Dari semua toko</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h6>Total Pengeluaran Hari Ini</h6>
                        <h3>Rp <?= number_format($total_daily_expense) ?></h3>
                        <small>Dari semua toko</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6>Total Pengeluaran Bulan Ini</h6>
                        <h3>Rp <?= number_format($total_monthly_expense) ?></h3>
                        <small>Dari semua toko</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row mb-4">
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Perbandingan Pemasukan Per Toko</h5>
                        <div class="chart-container" style="position: relative; height:300px; width:100%">
                            <canvas id="incomeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Metode Pembayaran</h5>
                        <div class="chart-container" style="position: relative; height:300px; width:100%">
                            <canvas id="paymentMethodChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Pengeluaran Per Toko</h5>
                        <div class="chart-container" style="position: relative; height:300px; width:100%">
                            <canvas id="expenseChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Store Details -->
        <div class="row">
            <?php foreach ($financial_data as $store): ?>
                <div class="col-lg-6 col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <img src="<?= base_url('assets/images/' . $store['logo']) ?>" alt="Logo" class="rounded-circle me-3" style="width: 40px; height: 40px;">
                                    <h5 class="mb-0"><?= htmlspecialchars($store['store_name']) ?></h5>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Aksi
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="viewDetails(<?= $store['id'] ?>)">Detail</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="exportStore(<?= $store['id'] ?>)">Export</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="printReport(<?= $store['id'] ?>)">Print</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="text-muted">Rekap Keuangan</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Jenis Pembayaran</th>
                                            <th>Grand Total</th>
                                            <th>Cash</th>
                                            <th>Piutang</th>
                                            <th>Transfer</th>
                                            <th>EDC</th>
                                            <th>E-Wallet</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Bulan ini -->
                                        <tr>
                                            <td><strong>Bulanan</strong></td>
                                            <td>Rp <?= number_format($store['monthly']['grand_total']['total'] ?? 0) ?></td>
                                            <td>Rp <?= number_format($store['monthly']['cash']['total'] ?? 0) ?></td>
                                            <td>Rp <?= number_format($store['monthly']['piutang']['total'] ?? 0) ?></td>
                                            <td>Rp <?= number_format($store['monthly']['transfer']['total'] ?? 0) ?></td>
                                            <td>Rp <?= number_format($store['monthly']['edc']['total'] ?? 0) ?></td>
                                            <td>Rp <?= number_format($store['monthly']['ewallet']['total'] ?? 0) ?></td>
                                        </tr>
                                        <!-- Harian ini -->
                                        <tr>
                                            <td><strong>Hari ini</strong></td>
                                            <td>Rp <?= number_format($store['daily']['grand_total']['total'] ?? 0) ?></td>
                                            <td>Rp <?= number_format($store['daily']['cash']['total'] ?? 0) ?></td>
                                            <td>Rp <?= number_format($store['daily']['piutang']['total'] ?? 0) ?></td>
                                            <td>Rp <?= number_format($store['daily']['transfer']['total'] ?? 0) ?></td>
                                            <td>Rp <?= number_format($store['daily']['edc']['total'] ?? 0) ?></td>
                                            <td>Rp <?= number_format($store['daily']['ewallet']['total'] ?? 0) ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <h6 class="text-muted mt-4">Rekap Pengeluaran</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Jenis Pengeluaran</th>
                                            <th>Total Bulanan</th>
                                            <th>Total Harian</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Pengeluaran</strong></td>
                                            <td>Rp <?= number_format($store['monthly']['expenses']['total'] ?? 0) ?></td>
                                            <td>Rp <?= number_format($store['daily']['expenses']['total'] ?? 0) ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Make sure Chart is defined
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded!');
        return;
    }

    const chartColors = {
        primary: '#4e73df',
        success: '#1cc88a',
        info: '#36b9cc',
        warning: '#f6c23e',
        danger: '#e74a3b',
        secondary: '#858796'
    };

    // Destroy existing charts if they exist
    ['incomeChart', 'paymentMethodChart', 'expenseChart'].forEach(chartId => {
        const chartInstance = Chart.getChart(chartId);
        if (chartInstance) {
            chartInstance.destroy();
        }
    });

    // Income Chart
    const incomeCtx = document.getElementById('incomeChart').getContext('2d');
    new Chart(incomeCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($financial_data, 'store_name')) ?>,
            datasets: [{
                label: 'Pemasukan Bulanan',
                data: <?= json_encode(array_map(function($store) {
                    return $store['monthly']['grand_total']['total'] ?? 0;
                }, $financial_data)) ?>,
                backgroundColor: chartColors.primary,
                borderColor: chartColors.primary,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Payment Method Chart
    const methodCtx = document.getElementById('paymentMethodChart').getContext('2d');
    new Chart(methodCtx, {
        type: 'doughnut',
        data: {
            labels: ['Cash', 'Transfer', 'EDC', 'E-Wallet', 'Piutang'],
            datasets: [{
                data: [
                    <?= $total_cash ?? 0 ?>,
                    <?= $total_transfer ?? 0 ?>,
                    <?= $total_edc ?? 0 ?>,
                    <?= $total_ewallet ?? 0 ?>,
                    <?= $total_piutang ?? 0 ?>
                ],
                backgroundColor: Object.values(chartColors)
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Expense Chart
    const expenseCtx = document.getElementById('expenseChart').getContext('2d');
    new Chart(expenseCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($financial_data, 'store_name')) ?>,
            datasets: [{
                label: 'Pengeluaran Bulanan',
                data: <?= json_encode(array_map(function($store) {
                    return $store['monthly']['expenses']['total'] ?? 0;
                }, $financial_data)) ?>,
                backgroundColor: chartColors.danger,
                borderColor: chartColors.danger,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});

function updateData() {
    const tanggal = document.getElementById('tanggal').value;
    window.location.href = '<?= base_url('keuangan?date=') ?>' + tanggal;
}

function exportData() {
    const tanggal = document.getElementById('tanggal').value;
    window.location.href = '<?= base_url('keuangan/export?date=') ?>' + tanggal;
}

function viewDetails(storeId) {
    window.location.href = '<?= base_url('keuangan/store/') ?>' + storeId;
}

function exportStore(storeId) {
    const tanggal = document.getElementById('tanggal').value;
    window.location.href = '<?= base_url('keuangan/export_store/') ?>' + storeId + '?date=' + tanggal;
}

function printReport(storeId) {
    const tanggal = document.getElementById('tanggal').value;
    window.open('<?= base_url('keuangan/print_store/') ?>' + storeId + '?date=' + tanggal, '_blank');
}
</script>
