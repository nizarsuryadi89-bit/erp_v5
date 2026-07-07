<div class="row p-4">
    <div class="col-12">
        <!-- Header Section -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line"></i> 
                    Transaksi Harian - <?= date('d F Y', strtotime($hariini)) ?>
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
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>Rp <?= number_format($total_pemasukan['totalna'] ?? 0) ?></h3>
                        <p>Total Pemasukan</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>Rp <?= number_format($total_pemasukan_piutang['totalna'] ?? 0) ?></h3>
                        <p>Pembayaran Piutang</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>Rp <?= number_format(($total_pemasukan['totalna'] ?? 0) + ($total_pemasukan_piutang['totalna'] ?? 0)) ?></h3>
                        <p>Grand Total</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3><?= count($pemasukan) ?></h3>
                        <p>Jumlah Transaksi</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods Summary -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-credit-card"></i> 
                    Ringkasan Metode Pembayaran - <?= date('d F Y', strtotime($hariini)) ?>
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th width="20%">Metode Pembayaran</th>
                                <th width="20%">Total Pembayaran</th>
                                <th width="20%">Pembayaran Piutang</th>
                                <th width="20%">Total</th>
                                <th width="15%">Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Calculate transfer totals
                            $total_transfer = 0;
                            foreach ($rekening as $value): 
                                $transfer_data = $this->db->query("SELECT SUM(bayar_transfer) as total_transfer FROM tbl_pembayaran WHERE rekening_id = '$value->id' AND pembayaran_tgl = '$hariini'")->row_array();
                                $total_transfer += $transfer_data['total_transfer'] ?? 0;
                            endforeach;
                            
                            // Calculate EDC totals
                            $total_edc = 0;
                            foreach ($edc as $value): 
                                $edc_data = $this->db->query("SELECT SUM(bayar_debit) as total_debit FROM tbl_pembayaran WHERE id_edc = '$value->edc_id' AND pembayaran_tgl = '$hariini'")->row_array();
                                $total_edc += $edc_data['total_debit'] ?? 0;
                            endforeach;
                            
                            // Calculate E-Wallet totals
                            $total_ewallet = 0;
                            foreach ($ewallet as $value): 
                                $ewallet_data = $this->db->query("SELECT SUM(bayar_ewallet) as total_ewallet FROM tbl_pembayaran WHERE ewallet_id = '$value->id' AND pembayaran_tgl = '$hariini'")->row_array();
                                $total_ewallet += $ewallet_data['total_ewallet'] ?? 0;
                            endforeach;
                            
                            // Calculate piutang totals
                            $total_transfer_piutang = 0;
                            $transfer_piutang_data = $this->db->query("SELECT SUM(payment_amount) as total_transfer FROM tbl_pembayaran_piutang WHERE payment_method = 'transfer' AND payment_date = '$hariini'")->row_array();
                            $total_transfer_piutang = $transfer_piutang_data['total_transfer'] ?? 0;
                            
                            $total_edc_piutang = 0;
                            $edc_piutang_data = $this->db->query("SELECT SUM(payment_amount) as total_debit FROM tbl_pembayaran_piutang WHERE (payment_method = 'edc' OR payment_method = 'debit') AND payment_date = '$hariini'")->row_array();
                            $total_edc_piutang = $edc_piutang_data['total_debit'] ?? 0;
                            
                            $total_ewallet_piutang = 0;
                            $ewallet_piutang_data = $this->db->query("SELECT SUM(payment_amount) as total_ewallet FROM tbl_pembayaran_piutang WHERE payment_method = 'ewallet' AND payment_date = '$hariini'")->row_array();
                            $total_ewallet_piutang = $ewallet_piutang_data['total_ewallet'] ?? 0;
                            
                            // Calculate totals for percentage calculation
                            $grand_total_cash = ($cash['totalna'] ?? 0) + ($cash_piutang['totalna'] ?? 0);
                            $grand_total_transfer = $total_transfer + $total_transfer_piutang;
                            $grand_total_edc = $total_edc + $total_edc_piutang;
                            $grand_total_ewallet = $total_ewallet + $total_ewallet_piutang;
                            
                            $total_all_methods = $grand_total_cash + $grand_total_transfer + $grand_total_edc + $grand_total_ewallet;
                            ?>
                            
                            <!-- Cash -->
                            <tr class="table-success">
                                <td>1</td>
                                <td>
                                    <i class="fas fa-money-bill-wave text-success"></i> 
                                    <strong>Tunai</strong>
                                </td>
                                <td class="text-end">Rp <?= number_format($cash['totalna'] ?? 0) ?>,-</td>
                                <td class="text-end">Rp <?= number_format($cash_piutang['totalna'] ?? 0) ?>,-</td>
                                <td class="text-end"><strong>Rp <?= number_format($grand_total_cash) ?>,-</strong></td>
                                <td class="text-center">
                                    <?php if ($total_all_methods > 0): ?>
                                        <span class="badge bg-success"><?= number_format(($grand_total_cash / $total_all_methods) * 100, 1) ?>%</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">0%</span>
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <!-- Transfer -->
                            <tr class="table-primary">
                                <td>2</td>
                                <td>
                                    <i class="fas fa-university text-primary"></i> 
                                    <strong>Transfer</strong>
                                </td>
                                <td class="text-end">Rp <?= number_format($total_transfer) ?>,-</td>
                                <td class="text-end">Rp <?= number_format($total_transfer_piutang) ?>,-</td>
                                <td class="text-end"><strong>Rp <?= number_format($grand_total_transfer) ?>,-</strong></td>
                                <td class="text-center">
                                    <?php if ($total_all_methods > 0): ?>
                                        <span class="badge bg-primary"><?= number_format(($grand_total_transfer / $total_all_methods) * 100, 1) ?>%</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">0%</span>
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <!-- EDC -->
                            <tr class="table-warning">
                                <td>3</td>
                                <td>
                                    <i class="fas fa-credit-card text-warning"></i> 
                                    <strong>Electronic Data Capture (EDC)</strong>
                                </td>
                                <td class="text-end">Rp <?= number_format($total_edc) ?>,-</td>
                                <td class="text-end">Rp <?= number_format($total_edc_piutang) ?>,-</td>
                                <td class="text-end"><strong>Rp <?= number_format($grand_total_edc) ?>,-</strong></td>
                                <td class="text-center">
                                    <?php if ($total_all_methods > 0): ?>
                                        <span class="badge bg-warning"><?= number_format(($grand_total_edc / $total_all_methods) * 100, 1) ?>%</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">0%</span>
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <!-- E-Wallet -->
                            <tr class="table-info">
                                <td>4</td>
                                <td>
                                    <i class="fas fa-mobile-alt text-info"></i> 
                                    <strong>E-Wallet</strong>
                                </td>
                                <td class="text-end">Rp <?= number_format($total_ewallet) ?>,-</td>
                                <td class="text-end">Rp <?= number_format($total_ewallet_piutang) ?>,-</td>
                                <td class="text-end"><strong>Rp <?= number_format($grand_total_ewallet) ?>,-</strong></td>
                                <td class="text-center">
                                    <?php if ($total_all_methods > 0): ?>
                                        <span class="badge bg-info"><?= number_format(($grand_total_ewallet / $total_all_methods) * 100, 1) ?>%</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">0%</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            
                            <!-- Total Row -->
                            <tr class="table-dark">
                                <td colspan="4" class="text-center"><strong>TOTAL KESELURUHAN</strong></td>
                                <td class="text-end"><strong>Rp <?= number_format($total_all_methods) ?>,-</strong></td>
                                <td class="text-center"><strong>100%</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Payment Methods Chart -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-chart-pie"></i> 
                                    Grafik Metode Pembayaran
                                </h5>
                            </div>
                            <div class="card-body">
                                <canvas id="paymentMethodsChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-list"></i> 
                                    Detail Per Bank/EDC/EWallet
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Transfer Details -->
                                <?php if ($total_transfer > 0): ?>
                                <div class="mb-3">
                                    <h6 class="text-primary"><i class="fas fa-university"></i> Transfer Bank:</h6>
                                    <?php foreach ($rekening as $value): 
                                        $transfer_data = $this->db->query("SELECT SUM(bayar_transfer) as total_transfer FROM tbl_pembayaran WHERE rekening_id = '$value->id' AND pembayaran_tgl = '$hariini'")->row_array();
                                        $bank_total = $transfer_data['total_transfer'] ?? 0;
                                        if ($bank_total > 0):
                                    ?>
                                        <div class="d-flex justify-content-between">
                                            <span><?= $value->nama_bank ?> (<?= $value->no_rekening ?>)</span>
                                            <strong>Rp <?= number_format($bank_total) ?>,-</strong>
                                        </div>
                                    <?php endif; endforeach; ?>
                                </div>
                                <?php endif; ?>
                                
                                <!-- EDC Details -->
                                <?php if ($total_edc > 0): ?>
                                <div class="mb-3">
                                    <h6 class="text-warning"><i class="fas fa-credit-card"></i> EDC:</h6>
                                    <?php foreach ($edc as $value): 
                                        $edc_data = $this->db->query("SELECT SUM(bayar_debit) as total_debit FROM tbl_pembayaran WHERE id_edc = '$value->edc_id' AND pembayaran_tgl = '$hariini'")->row_array();
                                        $edc_total = $edc_data['total_debit'] ?? 0;
                                        if ($edc_total > 0):
                                    ?>
                                        <div class="d-flex justify-content-between">
                                            <span><?= $value->edc_nama ?></span>
                                            <strong>Rp <?= number_format($edc_total) ?>,-</strong>
                                        </div>
                                    <?php endif; endforeach; ?>
                                </div>
                                <?php endif; ?>
                                
                                <!-- E-Wallet Details -->
                                <?php if ($total_ewallet > 0): ?>
                                <div class="mb-3">
                                    <h6 class="text-info"><i class="fas fa-mobile-alt"></i> E-Wallet:</h6>
                                    <?php foreach ($ewallet as $value): 
                                        $ewallet_data = $this->db->query("SELECT SUM(bayar_ewallet) as total_ewallet FROM tbl_pembayaran WHERE ewallet_id = '$value->id' AND pembayaran_tgl = '$hariini'")->row_array();
                                        $ewallet_total = $ewallet_data['total_ewallet'] ?? 0;
                                        if ($ewallet_total > 0):
                                    ?>
                                        <div class="d-flex justify-content-between">
                                            <span><?= $value->ewallet_nama ?></span>
                                            <strong>Rp <?= number_format($ewallet_total) ?>,-</strong>
                                        </div>
                                    <?php endif; endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

                            

        <!-- Detailed Transactions -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> 
                    Detail Transaksi Harian
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="datatable-transaksi">	
                        <thead class="table-dark">
                            <tr>
                                <th width="3%">No</th>
                                <th width="12%">NO SPK</th>
                                <th width="15%">Konsumen</th>
                                <th width="12%">Total Transaksi</th>
                                <th width="10%">Cash</th>
                                <th width="10%">Transfer</th>
                                <th width="10%">EDC</th>
                                <th width="10%">E-Wallet</th>
                                <th width="10%">Piutang</th>
                                <th width="8%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pemasukan)): ?>
                                <?php $no = 1; foreach ($pemasukan as $dt): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <strong><?= $dt->nospk ?></strong>
                                        <br>
                                        <small class="text-muted"><?= date('d/m/Y H:i', strtotime($dt->pembayaran_tgl ?? $dt->created_date)) ?></small>
                                    </td>
                                    <td>
                                        <strong><?= $dt->konsumen_nama ?></strong>
                                        <br>
                                        <small class="text-muted"><?= $dt->konsumen_nohp ?></small>
                                        <?php if (!empty($dt->konsumen_alamat)): ?>
                                            <br>
                                            <small class="text-muted"><?= substr($dt->konsumen_alamat, 0, 50) ?>...</small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <strong>Rp <?= number_format($dt->grand_total) ?></strong>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($dt->bayar_tunai > 0): ?>
                                            <span class="badge bg-success">Rp <?= number_format($dt->bayar_tunai) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($dt->bayar_transfer > 0): ?>
                                            <span class="badge bg-primary">Rp <?= number_format($dt->bayar_transfer) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($dt->bayar_debit > 0): ?>
                                            <span class="badge bg-warning">Rp <?= number_format($dt->bayar_debit) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($dt->bayar_ewallet > 0): ?>
                                            <span class="badge bg-info">Rp <?= number_format($dt->bayar_ewallet) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($dt->piutang > 0): ?>
                                            <span class="badge bg-danger">Rp <?= number_format($dt->piutang) ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Lunas</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($dt->piutang > 0): ?>
                                            <span class="badge bg-warning">Piutang</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Lunas</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> 
                                            Tidak ada transaksi untuk hari ini (<?= date('d F Y', strtotime($hariini)) ?>)
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- DataTables Script -->
<script>
$(document).ready(function() {
    destroy_datatable('datatable-transaksi');
    $('#datatable-transaksi').DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print"],
        "pageLength": 25,
        "order": [[1, "desc"]]
    }).buttons().container().appendTo('#datatable-transaksi_wrapper .col-md-6:eq(0)');
    
    // Initialize Payment Methods Chart
    initializePaymentMethodsChart();
});

// Export to Excel function
function exportToExcel() {
    window.location.href = '<?= base_url("keuangan/export_transaksi_harian") ?>?tanggal=<?= $hariini ?>';
}

// Initialize Payment Methods Pie Chart
function initializePaymentMethodsChart() {
    const ctx = document.getElementById('paymentMethodsChart');
    if (!ctx) return;
    
    // Get data from PHP variables
    const cashTotal = <?= $grand_total_cash ?? 0 ?>;
    const transferTotal = <?= $grand_total_transfer ?? 0 ?>;
    const edcTotal = <?= $grand_total_edc ?? 0 ?>;
    const ewalletTotal = <?= $grand_total_ewallet ?? 0 ?>;
    
    const data = {
        labels: ['Tunai', 'Transfer', 'EDC', 'E-Wallet'],
        datasets: [{
            data: [cashTotal, transferTotal, edcTotal, ewalletTotal],
            backgroundColor: [
                '#28a745', // Success green
                '#007bff', // Primary blue
                '#ffc107', // Warning yellow
                '#17a2b8'  // Info cyan
            ],
            borderColor: [
                '#1e7e34',
                '#0056b3',
                '#e0a800',
                '#117a8b'
            ],
            borderWidth: 2
        }]
    };
    
    const config = {
        type: 'pie',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: Rp ${value.toLocaleString('id-ID')} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    };
    
    new Chart(ctx, config);
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

.badge {
    font-size: 0.8em;
}

.table th {
    vertical-align: middle;
}

.table td {
    vertical-align: middle;
}
</style> 