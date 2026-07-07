<div class="row p-4">
    <div class="col-12">
        <!-- Header Section -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-money-bill-wave"></i> 
                    Transaksi Cash Harian - <?= date('d F Y', strtotime($hariini)) ?>
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('keuangan') ?>" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="button" class="btn btn-sm btn-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> Cetak
                    </button>
                    <button type="button" class="btn btn-sm btn-success" onclick="exportToExcel()">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>Rp <?= number_format($total_cash_today) ?></h3>
                        <p>Total Cash Hari Ini</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= $cash_summary->total_transactions ?></h3>
                        <p>Jumlah Transaksi Cash</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>Rp <?= number_format($cash_summary->total_sales) ?></h3>
                        <p>Total Penjualan</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>Rp <?= number_format($cash_summary->total_piutang) ?></h3>
                        <p>Total Piutang</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cash Statistics -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie"></i> 
                            Statistik Cash
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-success">
                                        <i class="fas fa-calculator"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Rata-rata Cash</span>
                                        <span class="info-box-number">Rp <?= number_format($cash_summary->avg_cash_per_transaction) ?></span>
                                        <small class="text-muted">per transaksi</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-info">
                                        <i class="fas fa-percentage"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Persentase Cash</span>
                                        <span class="info-box-number">
                                            <?= $cash_summary->total_sales > 0 ? number_format(($total_cash_today / $cash_summary->total_sales) * 100, 1) : 0 ?>%
                                        </span>
                                        <small class="text-muted">dari total penjualan</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i> 
                            Informasi Tambahan
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Total Cash:</strong></td>
                                <td class="text-end">Rp <?= number_format($total_cash_today) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Total Penjualan:</strong></td>
                                <td class="text-end">Rp <?= number_format($cash_summary->total_sales) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Total Piutang:</strong></td>
                                <td class="text-end">Rp <?= number_format($cash_summary->total_piutang) ?></td>
                            </tr>
                            <tr class="table-success">
                                <td><strong>Saldo Cash:</strong></td>
                                <td class="text-end"><strong>Rp <?= number_format($total_cash_today - $cash_summary->total_piutang) ?></strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Transactions -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> 
                    Detail Transaksi Cash Hari Ini
                </h3>
            </div>
            <div class="card-body">
                <?php if (!empty($cash_transactions)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="datatable-cash">	
                        <thead class="table-dark">
                            <tr>
                                <th width="3%">No</th>
                                <th width="10%">Waktu</th>
                                <th width="15%">Konsumen</th>
                                <th width="10%">No SPK</th>
                                <th width="10%">Cash</th>
                                <th width="10%">Total</th>
                                <th width="10%">Piutang</th>
                                <th width="10%">Kasir</th>
                                <th width="12%">Jenis Transaksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($cash_transactions as $trans): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <strong><?= date('H:i', strtotime($trans->pembayaran_jam)) ?></strong>
                                    <br>
                                    <small class="text-muted"><?= date('d/m/Y', strtotime($trans->pembayaran_tgl)) ?></small>
                                </td>
                                <td>
                                    <strong><?= $trans->konsumen_nama ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-phone"></i> <?= $trans->konsumen_nohp ?>
                                    </small>
                                    <?php if (!empty($trans->konsumen_alamat)): ?>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt"></i> <?= substr($trans->konsumen_alamat, 0, 25) ?>...
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?= $trans->nospk ?></span>
                                </td>
                                <td class="text-end">
                                    <strong class="text-success">Rp <?= number_format($trans->bayar_tunai) ?></strong>
                                </td>
                                <td class="text-end">
                                    <strong>Rp <?= number_format($trans->grand_total) ?></strong>
                                    <?php if ($trans->diskon > 0): ?>
                                        <br>
                                        <small class="text-muted">Diskon: Rp <?= number_format($trans->diskon) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($trans->piutang > 0): ?>
                                        <span class="badge bg-warning">Rp <?= number_format($trans->piutang) ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Lunas</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <i class="fas fa-user"></i> <?= $trans->kasir_nama ?>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?= $trans->jenis_transaksi_nama ?: 'Reguler' ?></span>
                                    <?php if (!empty($trans->marketplace_nama)): ?>
                                        <br>
                                        <small class="text-muted"><?= $trans->marketplace_nama ?></small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle fa-3x mb-3"></i>
                        <h4>Tidak ada transaksi cash untuk hari ini</h4>
                        <p class="mb-0">Belum ada transaksi cash yang dilakukan pada <?= date('d F Y', strtotime($hariini)) ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- DataTables Script -->
<script>
$(document).ready(function() {
    if ($('#datatable-cash').length) {
        $('#datatable-cash').DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print"],
            "pageLength": 25,
            "order": [[1, "desc"]]
        }).buttons().container().appendTo('#datatable-cash_wrapper .col-md-6:eq(0)');
    }
});

// Export to Excel function
function exportToExcel() {
    window.location.href = '<?= base_url("keuangan/export_cash_harian") ?>?tanggal=<?= $hariini ?>';
}
</script>

<style>
@media print {
    .card-tools, .btn, .dataTables_wrapper .dt-buttons {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .table {
        font-size: 12px;
    }
}

.small-box {
    margin-bottom: 20px;
}

.info-box {
    margin-bottom: 20px;
}

.badge {
    font-size: 0.8em;
}

.table th {
    vertical-align: middle;
}

.table td {
    vertical-align: middle;
}

.table-success {
    background-color: #d4edda !important;
}

.table-success td {
    color: #155724 !important;
    font-weight: bold;
}
</style> 