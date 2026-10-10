<?php if(empty($transactions)): ?>
    <div class="alert alert-info text-center m-4" style="background:#e0f2fe; border-color:#bae6fd; color:#0369a1; border-radius:10px;">
        <i class="fa fa-info-circle mb-2 fs-4 d-block"></i>
        Belum ada transaksi <?php echo $type == 'inbound' ? 'barang masuk' : 'barang keluar' ?> untuk hari ini.
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background:#f1f5f9;">
                <tr>
                    <th class="ps-4">No</th>
                    <?php if($type == 'inbound'): ?>
                        <th>Faktur</th>
                        <th>Supplier</th>
                        <th>Item Material</th>
                        <th>Total Rp</th>
                    <?php else: ?>
                        <th>No Ref</th>
                        <th>Gudang Tujuan</th>
                        <th>Karyawan</th>
                        <th>Item Material</th>
                    <?php endif; ?>
                    <th class="pe-4 text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($transactions as $t): ?>
                    <tr>
                        <td class="ps-4"><?= $no++ ?></td>
                        <?php if($type == 'inbound'): ?>
                            <td class="fw-bold text-primary"><?= $t->no_faktur ?></td>
                            <td><?= $t->supplier_nama ?? '-' ?></td>
                            <td>
                                <div class="d-flex flex-wrap gap-1" style="max-width: 400px;">
                                    <?php 
                                    $items = explode('|||', $t->materials_summary);
                                    foreach($items as $item): 
                                        if(trim($item) != ''):
                                    ?>
                                        <span class="badge bg-light text-dark border" style="font-weight: 500; padding: 0.4rem 0.6rem;"><i class="fa fa-box text-muted me-1"></i> <?= htmlspecialchars(trim($item)) ?></span>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </div>
                            </td>
                            <td>Rp <?= number_format($t->pembelian_total,0,',','.') ?></td>
                            <td class="pe-4 text-end">
                                <a href="<?= base_url('warehouse/purchase_edit/'.$t->pembelian_id) ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                                    <i class="fa fa-eye"></i> Detail
                                </a>
                            </td>
                        <?php else: ?>
                            <td class="fw-bold text-primary">OUT-<?= str_pad($t->pengambilan_id, 4, '0', STR_PAD_LEFT) ?></td>
                            <td><?= $t->gudang_nama ?? '-' ?></td>
                            <td><?= $t->nama_lengkap ?? '-' ?></td>
                            <td>
                                <div class="d-flex flex-wrap gap-1" style="max-width: 450px;">
                                    <?php 
                                    $items = explode('|||', $t->materials_summary);
                                    foreach($items as $item): 
                                        if(trim($item) != ''):
                                    ?>
                                        <span class="badge bg-light text-dark border" style="font-weight: 500; padding: 0.4rem 0.6rem;"><i class="fa fa-box text-muted me-1"></i> <?= htmlspecialchars(trim($item)) ?></span>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </div>
                            </td>
                            <td class="pe-4 text-end">
                                <a href="<?= base_url('warehouse/dispatch_edit/'.$t->pengambilan_id) ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                                    <i class="fa fa-eye"></i> Detail
                                </a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
