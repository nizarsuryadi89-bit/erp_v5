<div class="container mt-4">
    <h3 class="mb-4">Tracking WIP Produksi</h3>

    <!-- Informasi Produksi -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Informasi Produksi</h5>
            <p><strong>No SPK:</strong> <?= $produksi->nospk; ?></p>
            <p><strong>Konsumen:</strong> <?= $produksi->konsumen_name; ?></p>
            <p><strong>Produk:</strong> <?= $produksi->product_name; ?></p>
            <p><strong>Qty:</strong> <?= $produksi->qty; ?> pcs</p>
            <p><strong>Deadline:</strong> <?= date('d-m-Y H:i', strtotime($produksi->dateline_tgl . ' ' . $produksi->dateline_jam)); ?></p>
        </div>
    </div>

    <!-- Tabel Routing Step -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Proses Routing</h5>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Urutan</th>
                            <th>Proses</th>
                            <th>Mesin</th>
                            <th>Qty Produksi</th>
                            <th>Qty Missing</th>
                            <th>Lokasi Cetak</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Last Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detail_produksi as $row): ?>
                            <tr>
                                <td><?= $row->routing_urutan; ?></td>
                                <td><?= $row->routing_name; ?></td>
                                <td><?= $row->mesin_name; ?></td>
                                <td><?= $row->qty_produksi; ?></td>
                                <td><?= $row->qty_missing; ?></td>
                                <td><?= $row->lokasi_cetak; ?></td>
                                <td>
                                    <?php if ($row->status_produksi == 'Pending'): ?>
                                        <span class="badge bg-secondary">Pending</span>
                                    <?php elseif ($row->status_produksi == 'In Progress'): ?>
                                        <span class="badge bg-warning text-dark">In Progress</span>
                                    <?php elseif ($row->status_produksi == 'Done'): ?>
                                        <span class="badge bg-success">Done</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark"><?= $row->status_produksi; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <?php
                                            $progress = 0;
                                            if ($row->status_produksi == 'Pending') $progress = 0;
                                            if ($row->status_produksi == 'In Progress') $progress = 50;
                                            if ($row->status_produksi == 'Done') $progress = 100;
                                        ?>
                                        <div class="progress-bar" role="progressbar" style="width: <?= $progress; ?>%;" 
                                            aria-valuenow="<?= $progress; ?>" aria-valuemin="0" aria-valuemax="100">
                                            <?= $progress; ?>%
                                        </div>
                                    </div>
                                </td>
                                <td><?= date('d-m-Y H:i', strtotime($row->update_time)); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
