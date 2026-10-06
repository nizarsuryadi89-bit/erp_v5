<!-- Modal Sub Kategori -->
<div class="modal fade" id="modalsubkategori" tabindex="-1" aria-labelledby="modalsubkategoriLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalsubkategoriLabel">
                    <i class="bi bi-tag me-2"></i>Data Sub Kategori
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formTambahSubkategori" method="post" class="mb-4">
                    <div class="row">
                        <div class="col-md-10">
                            <input type="text" name="subkategori_nama" class="form-control"
                                placeholder="Masukkan nama sub kategori baru" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-plus-circle me-1"></i>Tambah
                            </button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-success">
                            <tr>
                                <th width="10%">No</th>
                                <th>Sub Kategori</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($subkategori as $sub): ?>
                                <tr data-id="<?= $sub->subkategori_id ?>">
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td>
                                        <input type="text" class="form-control subkategori-nama"
                                            value="<?= $sub->subkategori_nama ?>">
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <!-- <a href="<?= base_url('pengaturan/subkategori_form_builder/' . $sub->subkategori_id) ?>"
                                                class="btn btn-primary btn-sm px-2" title="Form Builder">
                                                <i class="bi bi-pencil-square"></i>
                                            </a> -->
                                            <button type="button" class="btn btn-success btn-sm btn-edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm btn-delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>