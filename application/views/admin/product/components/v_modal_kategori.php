<!-- Modal Kategori -->
<div class="modal fade" id="modalkategori" tabindex="-1" aria-labelledby="modalkategori" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalkategori">
                    <i class="bi bi-tags me-2"></i>Data Kategori
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-warning">
                            <tr>
                                <th width="10%">No</th>
                                <th>Kategori</th>
                                <!-- <th width="15%" class="text-center">Aksi</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($kategori as $kat): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><strong><?= $kat->kategori_nama ?></strong></td>
                                    <!-- <td class="text-center">
                                        <a href="<?= base_url('pengaturan/kategori_form_builder/' . $kat->kategori_id) ?>"
                                            class="btn btn-xs btn-primary py-1 px-2">
                                            <i class="bi bi-pencil-square me-1"></i>BUILDER
                                        </a>
                                    </td> -->
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>