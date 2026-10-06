<div class="app-content d-block">
    <div class="container-fluid py-3">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="panel-title"><?= $title ?></h2>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <p>Jumlah Data: <?= $total_rows ?></p>
                    </div>
                    <div class="col-sm-6 text-end">
                        <a href="<?= base_url('manufaktur') ?>" class="btn btn-primary">Home</a>
                        <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="datatable-detail">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Nama Product / Items</th>
                                <th width="10%">Jenis BOM</th>
                                <th width="15%">Detail Variasi</th>
                                <th width="20%">Material / Varian</th>
                                <th width="10%">Quantity</th>
                                <th width="10%">Satuan</th>
                                <th width="10%">Keterangan</th>
                                <th width="5%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($tampilData)): ?>
                                <?php
                                $no = 1;
                                foreach ($tampilData as $dt):
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $dt->product_nama ?></td>
                                        <td>
                                            <?php if ($dt->jenis_bom == 'Global'): ?>
                                                <span class="badge bg-primary">Global</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Variasi</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $dt->display_variasi ?></td>
                                        <td><strong><?= $dt->bahan_nama ?></strong> <?= !empty($dt->varian) ? ' - ' . $dt->varian : '' ?></td>
                                        <td><?= $dt->quantity ?></td>
                                        <td><?= $dt->satuan_nama ?></td>
                                        <td><?= $dt->keterangan ?></td>
                                        <td class="text-center">
                                            <a href="<?= base_url('manufaktur/bom/' . $dt->product_id) ?>"
                                                class="btn btn-info btn-sm" title="Lihat BOM Product">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        Tidak ada data BOM
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

<!-- Initialize Datatable if datatable-detail is used -->
<script>
    $(document).ready(function () {
        if ($.fn.DataTable.isDataTable('#datatable-detail')) {
            $('#datatable-detail').DataTable().destroy();
        }
        $('#datatable-detail').DataTable({
            "pageLength": 25,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
    });
</script>
