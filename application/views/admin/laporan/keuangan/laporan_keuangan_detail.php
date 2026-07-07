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
                <form action="<?= base_url('keuangan/laporan_keuangan_detail')?>" method="get" class="form-inline">
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

<!-- Summary Cards -->
<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Laporan Keuangan Detail</h3>
                <div class="box-tools pull-right">
                    <span class="label label-info">Periode: <?= date_indo($tanggal_mulai) ?> - <?= date_indo($tanggal_selesai) ?></span>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-aqua"><i class="fa fa-shopping-cart"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Transaksi</span>
                                <span class="info-box-number"><?= number_format(count($transactions)) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-money"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Tagihan</span>
                                <span class="info-box-number">Rp <?= number_format(array_sum(array_column($transactions, 'total_tagihan'))) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-yellow"><i class="fa fa-credit-card"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Pembayaran</span>
                                <span class="info-box-number">Rp <?= number_format(array_sum(array_column($transactions, 'total_bayar'))) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-red"><i class="fa fa-exclamation-triangle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Piutang</span>
                                <span class="info-box-number">Rp <?= number_format(array_sum(array_column($transactions, 'total_tagihan')) - array_sum(array_column($transactions, 'total_bayar'))) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Transactions Table -->
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Detail Transaksi</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <?php if (!empty($transactions)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="transactionsTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th>No SPK</th>
                                    <th>Konsumen</th>
                                    <th>No HP</th>
                                    <th>Total Tagihan</th>
                                    <th>Total Bayar</th>
                                    <th>Sisa</th>
                                    <th>Status</th>
                                    <th>Kasir</th>
                                    <th>Perusahaan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($transactions as $trans): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= date('d/m/Y', strtotime($trans->tgl_bayar)) ?></td>
                                    <td><?= date('H:i', strtotime($trans->jam_bayar)) ?></td>
                                    <td>
                                        <span class="label label-primary"><?= $trans->nospk ?></span>
                                    </td>
                                    <td><?= $trans->konsumen_nama ?></td>
                                    <td><?= $trans->konsumen_nohp ?></td>
                                    <td class="text-right">Rp <?= number_format($trans->total_tagihan) ?></td>
                                    <td class="text-right">Rp <?= number_format($trans->total_bayar) ?></td>
                                    <td class="text-right">
                                        <?php $sisa = $trans->total_tagihan - $trans->total_bayar; ?>
                                        <span class="label label-<?= $sisa > 0 ? 'warning' : 'success' ?>">
                                            Rp <?= number_format($sisa) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($trans->status_lunas == '1'): ?>
                                            <span class="label label-success">Lunas</span>
                                        <?php else: ?>
                                            <span class="label label-warning">Belum Lunas</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $trans->kasir_nama ?></td>
                                    <td><?= $trans->perusahaan_nama ?></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-xs btn-info" 
                                                onclick="showPaymentDetails('<?= $trans->pembayaran_id ?>')">
                                            <i class="fa fa-eye"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Tidak ada data transaksi untuk periode ini.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Payment Details Summary -->
<?php if (!empty($payment_details)): ?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Detail Metode Pembayaran</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>No SPK</th>
                                <th>Metode</th>
                                <th>Jumlah</th>
                                <th>Referensi</th>
                                <th>Bank/EWallet</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($payment_details as $detail): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d/m/Y', strtotime($detail->tgl_bayar)) ?></td>
                                <td>
                                    <span class="label label-primary"><?= $detail->nospk ?></span>
                                </td>
                                <td>
                                    <span class="label label-<?= $this->keuangan->get_payment_method_color($detail->metode) ?>">
                                        <?= ucfirst($detail->metode) ?>
                                    </span>
                                </td>
                                <td class="text-right">Rp <?= number_format($detail->jumlah) ?></td>
                                <td><?= $detail->referensi ?: '-' ?></td>
                                <td>
                                    <?php if ($detail->nama_bank): ?>
                                        <?= $detail->nama_bank ?>
                                    <?php elseif ($detail->ewallet_nama): ?>
                                        <?= $detail->ewallet_nama ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?= $detail->keterangan ?: '-' ?></td>
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

<!-- Payment Details Modal -->
<div class="modal fade" id="paymentDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Detail Pembayaran</h4>
            </div>
            <div class="modal-body" id="paymentDetailsContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- DataTables CSS and JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap.min.css">
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#transactionsTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        "pageLength": 25,
        "order": [[1, "desc"], [2, "desc"]],
        "responsive": true,
        "dom": '<"top"lf>rt<"bottom"ip><"clear">'
    });
});

// Function to show payment details
function showPaymentDetails(pembayaranId) {
    $.ajax({
        url: '<?= base_url("keuangan/get_payment_details_ajax") ?>',
        type: 'POST',
        data: {
            pembayaran_id: pembayaranId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#paymentDetailsContent').html(response.html);
                $('#paymentDetailsModal').modal('show');
            } else {
                alert('Gagal memuat detail pembayaran: ' + response.message);
            }
        },
        error: function() {
            alert('Terjadi kesalahan saat memuat detail pembayaran.');
        }
    });
}

// Export functionality
function exportToExcel() {
    var url = '<?= base_url("keuangan/export_laporan_keuangan") ?>' + 
              '?mulai=<?= $tanggal_mulai ?>&selesai=<?= $tanggal_selesai ?>';
    window.location.href = url;
}
</script>
