<?php
/**
 * Goods Pickup Letter - Bootstrap 5 Enhanced
 * Print-optimized layout with edit functionality
 */
?>




<!-- ========================================
    MAIN CONTENT
======================================== -->
<div class="app-content d-block">
    <div class="container-fluid py-3">
        <div class="card shadow-sm">
            <div class="card-header">
                <button type="button" class="btn btn-secondary" onclick="window.history.back();">
                    <i class="bi bi-arrow-left"></i> Kembali
                </button>
            </div>
    
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width:5%;">No</th>
                        <th style="width:40%;">Nama Barang</th>
                        <th class="text-center" style="width:10%;">Qty</th>
                        <th class="text-center" style="width:15%;">Satuan</th>
                        <th class="text-center" style="width:15%;">Kondisi</th>
                        <th class="text-center" style="width:15%;">Keterangan</th>
                        <th class="text-center no-print" style="width:8%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>

                <form action="<?= base_url('warehouse/updatePengambilanAction') ?>" method="post">
                    <?php 
                    $no = 1;
                    $totalQty = 0;
                    foreach ($tampilData as $key => $value) {
                        $totalQty += $value->pengambilan_qty ?? 0;
                    ?>
                    <tr>
                        <td class="text-center"><?= $no ?></td>
                        <td><?= $value->barang_nama ?? $value->bahan_nama ?? 'N/A' ?></td>
                        <td class="text-center"><input type="text" value="<?= number_format($value->pengambilan_qty ?? $value->qty ?? 0) ?>" name="qty" class="form-control"></td>
                        <td class="text-center"><?= $value->satuan_nama ?? 'PCS' ?></td>
                        <td class="text-center">
                            <select name="kondisi" class="form-control">
                                <option value="Baik" <?= $value->kondisi == 'Baik' ? 'selected' : '' ?>>Baik</option>
                                <option value="Rusak" <?= $value->kondisi == 'Rusak' ? 'selected' : '' ?>>Rusak</option>
                            </select>
                        </td>
                        <td class="text-center"><input type="text" value="<?= $value->keterangan ?? '-' ?>" name="keterangan" class="form-control"></td>
                        <td class="text-center no-print">
                            <button class="btn btn-sm btn-primary" type="submit">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                            <input type="hidden" name="pengambilan_detail_id" value="<?= $value->pengambilan_detail_id ?>">
<!-- ...input lainnya... -->
                    </tr>
                    <?php
                        $no++;
                    }
                    ?>
                    <tr class="table-light fw-bold">
                        <td colspan="2" class="text-end">Total Qty:</td>
                        <td class="text-center"><?= number_format($totalQty) ?></td>
                        <td colspan="4"></td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>




