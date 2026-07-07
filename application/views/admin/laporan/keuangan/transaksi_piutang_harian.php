<div class="row p-4">
    <div class="col-12">
        <!-- Header Section -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-hand-holding-usd"></i> 
                    Transaksi Piutang Harian - <?= date('d F Y', strtotime($hariini)) ?>
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
            <div class="col-lg-4 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>Rp <?= number_format($total_piutang_today) ?></h3>
                        <p>Total Pembayaran Piutang Hari Ini</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= count($piutang_transactions) ?></h3>
                        <p>Jumlah Transaksi Piutang</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?= count($payment_methods_summary) ?></h3>
                        <p>Metode Pembayaran Digunakan</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods Summary -->
        <?php if (!empty($payment_methods_summary)): ?>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie"></i> 
                    Ringkasan Metode Pembayaran
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($payment_methods_summary as $method): ?>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-<?= $this->get_payment_method_color($method->payment_method) ?>">
                                <i class="fas <?= $this->get_payment_method_icon($method->payment_method) ?>"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text"><?= ucfirst($method->payment_method) ?></span>
                                <span class="info-box-number">Rp <?= number_format($method->total) ?></span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: <?= ($method->total / $total_piutang_today) * 100 ?>%"></div>
                                </div>
                                <span class="progress-description">
                                    <?= $method->count ?> transaksi
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Detailed Transactions -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> 
                    Detail Transaksi Piutang Hari Ini
                </h3>
            </div>
            <div class="card-body">
                <?php if (!empty($piutang_transactions)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="datatable-piutang">	
                        <thead class="table-dark">
                            <tr>
                                <th width="3%">No</th>
                                <th width="12%">Waktu</th>
                                <th width="15%">Konsumen</th>
                                <th width="10%">No SPK</th>
                                <th width="12%">Jumlah Bayar</th>
                                <th width="10%">Metode</th>
                                <th width="15%">Kasir</th>
                                <th width="13%">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($piutang_transactions as $trans): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <strong><?= date('H:i', strtotime($trans->created_time)) ?></strong>
                                    <br>
                                    <small class="text-muted"><?= date('d/m/Y', strtotime($trans->payment_date)) ?></small>
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
                                            <i class="fas fa-map-marker-alt"></i> <?= substr($trans->konsumen_alamat, 0, 30) ?>...
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?= $trans->nospk ?></span>
                                </td>
                                <td class="text-end">
                                    <strong class="text-success">Rp <?= number_format($trans->payment_amount) ?></strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $this->get_payment_method_color($trans->payment_method) ?>">
                                        <i class="fas <?= $this->get_payment_method_icon($trans->payment_method) ?>"></i>
                                        <?= ucfirst($trans->payment_method) ?>
                                    </span>
                                </td>
                                <td>
                                    <i class="fas fa-user"></i> <?= $trans->kasir_nama ?: 'System' ?>
                                </td>
                                <td>
                                    <?php if (!empty($trans->payment_notes)): ?>
                                        <small class="text-muted"><?= $trans->payment_notes ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
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
                        <h4>Tidak ada transaksi piutang untuk hari ini</h4>
                        <p class="mb-0">Belum ada pembayaran piutang yang dilakukan pada <?= date('d F Y', strtotime($hariini)) ?></p>
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
    if ($('#datatable-piutang').length) {
        $('#datatable-piutang').DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print"],
            "pageLength": 25,
            "order": [[1, "desc"]]
        }).buttons().container().appendTo('#datatable-piutang_wrapper .col-md-6:eq(0)');
    }
});

// Export to Excel function
function exportToExcel() {
    window.location.href = '<?= base_url("keuangan/export_piutang_harian") ?>?tanggal=<?= $hariini ?>';
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

.progress {
    height: 3px;
    margin: 5px 0;
}

.progress-description {
    font-size: 11px;
    color: #6c757d;
}
</style> 