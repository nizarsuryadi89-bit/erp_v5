<div class="app-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-credit-card me-2"></i>Rekap Metode Pembayaran
            </h1>
            <div class="d-flex gap-2">
                <button class="btn btn-success" onclick="exportToExcel()">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
                <a href="<?= base_url('laporan/dashboard_terpadu') ?>" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>

        <!-- Date Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Pilih Tanggal</label>
                        <input type="date" class="form-control" name="tanggal" value="<?= $hariini ?>" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Tampilkan
                            </button>
                            <a href="<?= base_url('laporan/rekap_metode_pembayaran') ?>" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <!-- Total Pemasukan -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Pemasukan
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    Rp <?= number_format($total_pemasukan['total'] ?? 0) ?>
                                </div>
                                <div class="text-xs text-muted">
                                    Tanggal: <?= date('d F Y', strtotime($hariini)) ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cash -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Cash/Tunai
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    Rp <?= number_format($payment_methods['cash']['total']) ?>
                                </div>
                                <div class="text-xs text-muted">
                                    <?= number_format($payment_methods['cash']['percentage'], 1) ?>% dari total
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transfer -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Transfer
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    Rp <?= number_format($payment_methods['transfer']['total']) ?>
                                </div>
                                <div class="text-xs text-muted">
                                    <?= number_format($payment_methods['transfer']['percentage'], 1) ?>% dari total
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-university fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- EDC -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    EDC/Debit
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    Rp <?= number_format($payment_methods['edc']['total']) ?>
                                </div>
                                <div class="text-xs text-muted">
                                    <?= number_format($payment_methods['edc']['percentage'], 1) ?>% dari total
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- E-Wallet Card -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    E-Wallet
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    Rp <?= number_format($payment_methods['ewallet']['total']) ?>
                                </div>
                                <div class="text-xs text-muted">
                                    <?= number_format($payment_methods['ewallet']['percentage'], 1) ?>% dari total
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-mobile-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Payment Methods Pie Chart -->
            <div class="col-xl-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Distribusi Metode Pembayaran</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4 pb-2">
                            <canvas id="paymentMethodsChart"></canvas>
                        </div>
                        <div class="mt-4 text-center small">
                            <span class="mr-2">
                                <i class="fas fa-circle text-success"></i> Cash
                            </span>
                            <span class="mr-2">
                                <i class="fas fa-circle text-primary"></i> Transfer
                            </span>
                            <span class="mr-2">
                                <i class="fas fa-circle text-warning"></i> EDC
                            </span>
                            <span class="mr-2">
                                <i class="fas fa-circle text-danger"></i> E-Wallet
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Methods Bar Chart -->
            <div class="col-xl-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Perbandingan Metode Pembayaran</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-bar">
                            <canvas id="paymentMethodsBarChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Tables -->
        <div class="row">
            <!-- Transfer Details -->
            <?php if (!empty($payment_methods['transfer']['details'])): ?>
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Detail Transfer Bank</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Bank</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payment_methods['transfer']['details'] as $detail): ?>
                                    <tr>
                                        <td><?= $detail['nama'] ?></td>
                                        <td>Rp <?= number_format($detail['total']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- EDC Details -->
            <?php if (!empty($payment_methods['edc']['details'])): ?>
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Detail EDC</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Mesin EDC</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payment_methods['edc']['details'] as $detail): ?>
                                    <tr>
                                        <td><?= $detail['nama'] ?></td>
                                        <td>Rp <?= number_format($detail['total']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- E-Wallet Details -->
        <?php if (!empty($payment_methods['ewallet']['details'])): ?>
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Detail E-Wallet</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>E-Wallet</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payment_methods['ewallet']['details'] as $detail): ?>
                                    <tr>
                                        <td><?= $detail['nama'] ?></td>
                                        <td>Rp <?= number_format($detail['total']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Transaction List -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Transaksi</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="transactionTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No SPK</th>
                                <th>Konsumen</th>
                                <th>Total Tagihan</th>
                                <th>Cash</th>
                                <th>Transfer</th>
                                <th>EDC</th>
                                <th>E-Wallet</th>
                                <th>Total Bayar</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($pemasukan as $item): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $item->nospk ?></td>
                                <td>
                                    <?= $item->konsumen_nama ?><br>
                                    <small class="text-muted"><?= $item->konsumen_nohp ?></small>
                                </td>
                                <td>Rp <?= number_format($item->total_tagihan) ?></td>
                                <td>Rp <?= number_format($item->bayar_tunai) ?></td>
                                <td>Rp <?= number_format($item->bayar_transfer) ?></td>
                                <td>Rp <?= number_format($item->bayar_debit) ?></td>
                                <td>Rp <?= number_format($item->bayar_ewallet) ?></td>
                                <td>Rp <?= number_format($item->total_bayar) ?></td>
                                <td>
                                    <?php if ($item->status_lunas == '1'): ?>
                                        <span class="badge bg-success">Lunas</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Belum Lunas</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<script>
// Initialize DataTable
$(document).ready(function() {
    $('#transactionTable').DataTable({
        "pageLength": 25,
        "order": [[0, "asc"]],
        "language": {
            "url": "<?= base_url('assets/js/datatables-id.json') ?>"
        }
    });
});

// Payment Methods Pie Chart
const ctxPie = document.getElementById('paymentMethodsChart').getContext('2d');
new Chart(ctxPie, {
    type: 'doughnut',
    data: {
        labels: ['Cash', 'Transfer', 'EDC', 'E-Wallet'],
        datasets: [{
            data: [
                <?= (float)($payment_methods['cash']['total'] ?? 0) ?>,
                <?= (float)($payment_methods['transfer']['total'] ?? 0) ?>,
                <?= (float)($payment_methods['edc']['total'] ?? 0) ?>,
                <?= (float)($payment_methods['ewallet']['total'] ?? 0) ?>
            ],
            backgroundColor: [
                '#28a745',
                '#007bff',
                '#ffc107',
                '#dc3545'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});

// Payment Methods Bar Chart
const ctxBar = document.getElementById('paymentMethodsBarChart').getContext('2d');
new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: ['Cash', 'Transfer', 'EDC', 'E-Wallet'],
        datasets: [{
            label: 'Total Pembayaran',
            data: [
                <?= (float)($payment_methods['cash']['total'] ?? 0) ?>,
                <?= (float)($payment_methods['transfer']['total'] ?? 0) ?>,
                <?= (float)($payment_methods['edc']['total'] ?? 0) ?>,
                <?= (float)($payment_methods['ewallet']['total'] ?? 0) ?>
            ],
            backgroundColor: [
                '#28a745',
                '#007bff',
                '#ffc107',
                '#dc3545'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                    }
                }
            }
        }
    }
});

// Export function
function exportToExcel() {
    const tanggal = '<?= $hariini ?>';
    window.location.href = `<?= base_url('laporan/export_rekap_metode_pembayaran') ?>?tanggal=${tanggal}`;
}
</script>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}

.chart-pie {
    position: relative;
    height: 15rem;
    width: 100%;
}

.chart-bar {
    position: relative;
    height: 15rem;
    width: 100%;
}

.gap-2 {
    gap: 0.5rem;
}

@media (max-width: 768px) {
    .chart-pie, .chart-bar {
        height: 12rem;
    }
}
</style>
