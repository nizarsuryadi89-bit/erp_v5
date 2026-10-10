<?php
// Helper function for date formatting
function date_indo($date) {
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    $split = explode('-', $date);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}
?>

<div class="row">
    <section class="panel">
        <div class="panel-body">
            <!-- Date Range Filter -->
            <div class="col-md-12">
                <form action="<?= base_url('keuangan/laporan_keuangan')?>" method="get" class="form-inline">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Tanggal Mulai</label>
                            <input type="date" name="mulai" class="form-control" required value="<?= $tanggal_mulai ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Tanggal Akhir</label>
                            <input type="date" name="selesai" class="form-control" required value="<?= $tanggal_selesai ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label><br>
                            <button class="btn btn-primary" type="submit">
                                <i class="fa fa-search"></i> Tampilkan
                            </button>
                            <a href="<?= base_url('keuangan/export_laporan_keuangan?mulai='.$tanggal_mulai.'&selesai='.$tanggal_selesai) ?>" 
                               class="btn btn-success">
                                <i class="fa fa-file-excel-o"></i> Export Excel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<!-- Financial Summary Cards -->
<div class="row">
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3><?= number_format($financial_summary['total_transactions']) ?></h3>
                <p>Total Transaksi</p>
            </div>
            <div class="icon">
                <i class="fa fa-shopping-cart"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner">
                <h3>Rp <?= number_format($financial_summary['total_bayar']) ?></h3>
                <p>Total Pembayaran</p>
            </div>
            <div class="icon">
                <i class="fa fa-money"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3>Rp <?= number_format($financial_summary['total_piutang']) ?></h3>
                <p>Total Piutang</p>
            </div>
            <div class="icon">
                <i class="fa fa-credit-card"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
            <div class="inner">
                <h3><?= number_format($financial_summary['persentase_lunas'], 1) ?>%</h3>
                <p>Persentase Lunas</p>
            </div>
            <div class="icon">
                <i class="fa fa-percentage"></i>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Summary -->
<div class="row">
    <div class="col-md-6">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Ringkasan Keuangan</h3>
                <div class="box-tools pull-right">
                    <span class="label label-info">Periode: <?= date_indo($tanggal_mulai) ?> - <?= date_indo($tanggal_selesai) ?></span>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-bordered">
                    <tr>
                        <td>Total Tagihan</td>
                        <td class="text-right">Rp <?= number_format($financial_summary['total_tagihan']) ?></td>
                    </tr>
                    <tr>
                        <td>Total Pembayaran</td>
                        <td class="text-right">Rp <?= number_format($financial_summary['total_bayar']) ?></td>
                    </tr>
                    <tr>
                        <td>Total Piutang</td>
                        <td class="text-right">Rp <?= number_format($financial_summary['total_piutang']) ?></td>
                    </tr>
                    <tr>
                        <td>Transaksi Lunas</td>
                        <td class="text-right"><?= number_format($financial_summary['total_lunas']) ?></td>
                    </tr>
                    <tr>
                        <td>Transaksi Belum Lunas</td>
                        <td class="text-right"><?= number_format($financial_summary['total_belum_lunas']) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Metode Pembayaran</h3>
            </div>
            <div class="box-body">
                <?php if (!empty($payment_methods)): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Metode</th>
                                <th>Jumlah Transaksi</th>
                                <th>Total Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payment_methods as $method): ?>
                            <tr>
                                <td>
                                    <span class="label label-<?= $this->keuangan->get_payment_method_color($method->metode) ?>">
                                        <?= ucfirst($method->metode) ?>
                                    </span>
                                </td>
                                <td class="text-center"><?= number_format($method->jumlah_transaksi) ?></td>
                                <td class="text-right">Rp <?= number_format($method->total_pembayaran) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">Tidak ada data metode pembayaran untuk periode ini.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Daily Transactions Chart -->
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Transaksi Harian</h3>
            </div>
            <div class="box-body">
                <?php if (!empty($daily_transactions)): ?>
                    <canvas id="dailyChart" style="height: 300px;"></canvas>
                <?php else: ?>
                    <p class="text-muted">Tidak ada data transaksi harian untuk periode ini.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Top Performers -->
<div class="row">
    <div class="col-md-6">
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">Top 10 Konsumen</h3>
            </div>
            <div class="box-body">
                <?php if (!empty($top_customers)): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Konsumen</th>
                                <th>Jumlah Transaksi</th>
                                <th>Total Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($top_customers as $customer): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $customer->konsumen_nama ?></td>
                                <td class="text-center"><?= number_format($customer->jumlah_transaksi) ?></td>
                                <td class="text-right">Rp <?= number_format($customer->total_bayar) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">Tidak ada data konsumen untuk periode ini.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Top 10 Kasir</h3>
            </div>
            <div class="box-body">
                <?php if (!empty($top_cashiers)): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kasir</th>
                                <th>Jumlah Transaksi</th>
                                <th>Total Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($top_cashiers as $cashier): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $cashier->kasir_nama ?></td>
                                <td class="text-center"><?= number_format($cashier->jumlah_transaksi) ?></td>
                                <td class="text-right">Rp <?= number_format($cashier->total_bayar) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">Tidak ada data kasir untuk periode ini.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="row">
    <div class="col-md-12">
        <div class="box box-default">
            <div class="box-body text-center">
                <a href="<?= base_url('keuangan/laporan_keuangan_detail?mulai='.$tanggal_mulai.'&selesai='.$tanggal_selesai) ?>" 
                   class="btn btn-info btn-lg">
                    <i class="fa fa-list"></i> Lihat Detail Transaksi
                </a>
                <a href="<?= base_url('keuangan/export_laporan_keuangan?mulai='.$tanggal_mulai.'&selesai='.$tanggal_selesai) ?>" 
                   class="btn btn-success btn-lg">
                    <i class="fa fa-file-excel-o"></i> Export Excel
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js for Daily Transactions -->
<?php if (!empty($daily_transactions)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('dailyChart').getContext('2d');
    var dailyData = <?= json_encode($daily_transactions) ?>;
    
    var labels = dailyData.map(function(item) {
        return new Date(item.tanggal).toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short'
        });
    });
    
    var totalBayar = dailyData.map(function(item) {
        return parseFloat(item.total_bayar);
    });
    
    var totalTagihan = dailyData.map(function(item) {
        return parseFloat(item.total_tagihan);
    });
    
    var chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Pembayaran',
                data: totalBayar,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }, {
                label: 'Total Tagihan',
                data: totalTagihan,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                        }
                    }
                }
            }
        }
    });
});
</script>
<?php endif; ?>
