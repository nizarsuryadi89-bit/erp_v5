<div class="app-content">
    <div class="container-fluid">
        <h3 class="mb-4">Laporan Transaksi Bulanan - <?= $bulan ?></h3>
        
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6>Total Cash</h6>
                        <h3>Rp <?= number_format($totals['cash']) ?></h3>
                        <small><?= count($transactions['cash']) ?> transaksi</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6>Total Transfer</h6>
                        <h3>Rp <?= number_format($totals['transfer']) ?></h3>
                        <small><?= count($transactions['transfer']) ?> transaksi</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h6>Total Debit</h6>
                        <h3>Rp <?= number_format($totals['debit']) ?></h3>
                        <small><?= count($transactions['debit']) ?> transaksi</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs for different payment methods -->
        <ul class="nav nav-tabs" id="paymentTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#cash">Transaksi Cash</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#transfer">Transaksi Transfer</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#debit">Transaksi Debit</a>
            </li>
        </ul>

        <div class="tab-content mt-3">
            <?php foreach (['cash' => 'Cash', 'transfer' => 'Transfer', 'debit' => 'Debit'] as $method => $label): ?>
                <div class="tab-pane fade <?= $method == 'cash' ? 'show active' : '' ?>" id="<?= $method ?>">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="table-<?= $method ?>">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>No SPK</th>
                                            <th>Konsumen</th>
                                            <th>Telepon</th>
                                            <th>Jumlah <?= $label ?></th>
                                            <th>Total</th>
                                            <th>Kasir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($transactions[$method] as $i => $trx): ?>
                                        <tr>
                                            <td><?= $i + 1 ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($trx['tanggal'] . ' ' . $trx['jam'])) ?></td>
                                            <td><?= $trx['nospk'] ?></td>
                                            <td><?= $trx['konsumen'] ?></td>
                                            <td><?= $trx['telepon'] ?></td>
                                            <td class="text-right">Rp <?= number_format($trx['jumlah']) ?></td>
                                            <td class="text-right">Rp <?= number_format($trx['total']) ?></td>
                                            <td><?= $trx['kasir'] ?></td>
                                        </tr>
                                        <?php endforeach; ?>
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
$(document).ready(function() {
    // Initialize DataTables
    $('#table-cash, #table-transfer, #table-debit').DataTable({
        "pageLength": 25,
        "ordering": true,
        "searching": true
    });
});
</script>
