<div class="app-content mt-2">
    <div class="container-fluid">
        <!-- HEADER SECTION -->
        <?php $this->load->view('layout_admin/header_modul'); ?>

        <!-- Flash Messages -->
        <div id="alert-container">
            <?= $this->session->flashdata('message'); ?>
        </div>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-check-circle fs-5"></i>
                    <div class="fw-bold"><?= $this->session->flashdata('success') ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- STATISTICS SECTION -->
        <div class="row g-3 mb-4 animate-up">
            <!-- STATS: TOTAL SUBKATEGORI -->
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm border-0 h-100" style="background: white;">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-v2-icon text-white rounded-4 d-flex align-items-center justify-content-center shadow-sm"
                            style="background: var(--info-gradient); width: 52px; height: 52px; flex-shrink: 0;">
                            <i class="fas fa-tags fs-4"></i>
                        </div>
                        <div>
                            <div class="stat-v2-label text-muted fw-bold small text-uppercase"
                                style="letter-spacing: 0.5px;">Master Subkategori</div>
                            <div class="stat-v2-value fw-900 text-dark" style="font-size: 1.5rem; line-height: 1;">
                                <?= number_format($total_rows) ?> <small class="text-muted fs-6 fw-normal">Item</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACTION: TAMBAH SUBKATEGORI -->
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm border-0 h-100 premium-card-hover"
                    style="background: var(--primary-gradient); cursor: pointer;" data-bs-toggle="modal"
                    data-bs-target="#modalAddSubcategory">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-20 text-white rounded-4 d-flex align-items-center justify-content-center"
                            style="width: 52px; height: 52px; flex-shrink: 0;">
                            <i class="fas fa-plus-circle fs-3"></i>
                        </div>
                        <div>
                            <div class="text-white fw-900 text-uppercase small mb-0" style="letter-spacing: 0.5px;">
                                Tambah Baru</div>
                            <div class="text-white text-opacity-75 small fw-bold mt-1"
                                style="font-size: 0.7rem; line-height: 1.2;">Buat turunan kategori produk.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACTION: BACK TO KATEGORI -->
            <div class="col-6 col-md-3">
                <a href="<?= base_url('pengaturan/kategori') ?>" class="text-decoration-none h-100">
                    <div class="summary-stat-card shadow-sm border-0 h-100 premium-card-hover"
                        style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="bg-white bg-opacity-10 text-info rounded-4 d-flex align-items-center justify-content-center shadow-sm"
                                style="width: 52px; height: 52px; flex-shrink: 0;">
                                <i class="fas fa-arrow-left fs-4"></i>
                            </div>
                            <div>
                                <div class="text-white fw-900 text-uppercase small mb-0" style="letter-spacing: 0.5px;">
                                    Ke Kategori</div>
                                <div class="text-white text-opacity-50 small fw-bold mt-1"
                                    style="font-size: 0.7rem; line-height: 1.2;">Kembali ke master induk.</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- STATUS FILTER INFO (If applicable) -->
            <?php if (!empty($filter_kategori)): ?>
                <div class="col-6 col-md-3">
                    <div class="summary-stat-card shadow-sm border-0 h-100"
                        style="background: #f8fafc; border: 1px dashed #cbd5e1 !important;">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-4 d-flex align-items-center justify-content-center"
                                style="width: 52px; height: 52px; flex-shrink: 0;">
                                <i class="fas fa-filter fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted fw-bold small text-uppercase" style="letter-spacing: 0.5px;">Filtered
                                    By</div>
                                <div class="text-primary fw-900 small text-uppercase truncate-text"
                                    style="max-width: 120px;">
                                    <?php
                                    foreach ($kategori as $k) {
                                        if ($k->kategori_id == $filter_kategori)
                                            echo $k->kategori_nama;
                                    }
                                    ?>
                                </div>
                                <a href="<?= base_url('pengaturan/subkategori') ?>"
                                    class="text-danger small fw-bold text-decoration-none"
                                    style="font-size: 0.65rem;">[RESET FILTER]</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- MAIN TABLE CARD -->
        <div class="report-section-card animate-up" style="animation-delay: 0.1s">
            <div class="section-header">
                <h5><i class="fas fa-sitemap me-2"></i> Daftar Sub-Kategori Produk</h5>
                <div class="d-flex gap-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="tableSearch" class="form-control border-0 shadow-none"
                            placeholder="Cari sub-kategori...">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table premium-table table-hover mb-0" id="datatable-details">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">No</th>
                            <th>Sub-Kategori</th>
                            <th>Kategori Induk</th>
                            <th class="text-center">Config (PxL)</th>
                            <th class="text-center" style="width: 150px;">Status</th>
                            <th class="text-center" style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="subkategoriTableBody">
                        <?php
                        $no = 1;
                        foreach ($tampilData as $dt):
                            $is_active = ($dt->status == 1);
                            $status_class = $is_active ? 'bg-success-subtle text-success border-success-subtle' : 'bg-danger-subtle text-danger border-danger-subtle';
                            $status_label = $is_active ? 'AKTIF' : 'NON-AKTIF';

                            // Config parsing
                            $config = json_decode($dt->subkategori_config ?? '{}', true);
                            $use_pxl = (isset($config['use_pxl']) && $config['use_pxl'] == 1);
                            ?>
                            <tr>
                                <td class="text-center fw-bold text-muted"><?= $no++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light p-2 rounded-3 shadow-sm border me-3 d-flex align-items-center justify-content-center"
                                            style="width: 40px; height: 40px;">
                                            <i class="fas fa-tag text-primary opacity-75"></i>
                                        </div>
                                        <div>
                                            <div class="fw-800 text-dark"><?= strtoupper($dt->subkategori_nama) ?></div>
                                            <div class="text-muted small fw-bold">ID: #<?= $dt->subkategori_id ?> | Urutan:
                                                <?= $dt->subkategori_urutan ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge border bg-light text-dark rounded-pill px-3 py-2 fw-bold"
                                        style="font-size: 0.7rem;">
                                        <i class="fas fa-folder text-warning me-1"></i>
                                        <?= strtoupper($dt->kategori_nama ?? 'TANPA INDUK') ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($use_pxl): ?>
                                        <span
                                            class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3 py-2 fw-bold"
                                            style="font-size: 0.65rem;">
                                            <i class="fas fa-ruler-combined me-1"></i> DIMENSI AKTIF
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border rounded-pill px-3 py-2 fw-bold"
                                            style="font-size: 0.65rem;">
                                            <i class="fas fa-times me-1"></i> QTY SAJA
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge border <?= $status_class ?> rounded-pill px-3 py-2 fw-bold"
                                        style="font-size: 0.7rem;">
                                        <i class="fas <?= $is_active ? 'fa-check-circle' : 'fa-times-circle' ?> me-1"></i>
                                        <?= $status_label ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="<?= base_url('pengaturan/subkategori_form_builder/' . $dt->subkategori_id) ?>"
                                            class="btn-action-premium btn-edit-p text-decoration-none"
                                            title="Configure Dynamic Form">
                                            <i class="fas fa-tools"></i>
                                        </a>
                                        <button type="button" class="btn-action-premium btn-edit-p" title="Ubah Data"
                                            onclick="openEditModal('<?= $dt->subkategori_id ?>', '<?= addslashes($dt->subkategori_nama) ?>', '<?= addslashes(trim($dt->subkategori_deskripsi)) ?>', '<?= $dt->subkategori_urutan ?>', '<?= $dt->subkategori_ket ?>', '<?= $dt->status ?>', '<?= $use_pxl ? 1 : 0 ?>')">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                        <button type="button"
                                            onclick="confirmDelete('<?= base_url('warehouse/actionDeleteSubcategory/' . $dt->subkategori_id) . ($filter_kategori ? '?kategori_id=' . $filter_kategori : '') ?>')"
                                            class="btn-action-premium btn-delete-p" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (empty($tampilData)): ?>
                <div class="p-5 text-center">
                    <i class="fas fa-box-open fs-1 text-muted opacity-25 mb-3"></i>
                    <h6 class="fw-800 text-muted">Belum ada data sub-kategori yang tersedia.</h6>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MODAL ADD SUB KATEGORI -->
<div class="modal fade" id="modalAddSubcategory" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-dark text-white p-4 border-0">
                <h5 class="modal-title fw-900 text-uppercase"><i class="fas fa-plus-circle me-2"></i> Tambah
                    Sub-Kategori</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="<?= base_url('warehouse/actionAddSubcategory') ?>" method="post">
                <input type="hidden" name="redirect_kategori_id" value="<?= $filter_kategori ?>">
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Urutan Tampil</label>
                            <input name="subkategori_urutan" type="number" class="form-control fw-bold" placeholder="0"
                                required
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Pilih Induk</label>
                            <select name="kategori_id" class="form-select fw-bold" required
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;">
                                <option value="">-- Pilih --</option>
                                <?php foreach ($kategori as $k): ?>
                                    <option value="<?= $k->kategori_id ?>" <?= ($filter_kategori == $k->kategori_id) ? 'selected' : '' ?>><?= strtoupper($k->kategori_nama) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Nama Sub-Kategori</label>
                        <input name="subkategori_nama" type="text" class="form-control fw-bold"
                            placeholder="Misal: Banner, Stiker..." required
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;">
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Deskripsi</label>
                        <textarea name="subkategori_deskripsi" class="form-control fw-bold" rows="2"
                            placeholder="Keterangan singkat..."
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;"></textarea>
                    </div>

                    <div class="mb-4 p-3 bg-light rounded-4 border">
                        <div class="form-check form-switch p-0 d-flex align-items-center justify-content-between">
                            <label class="fw-bold text-muted small text-uppercase mb-0" for="addUsePxl">Gunakan Dimensi
                                (P x L)</label>
                            <input class="form-check-input ms-0" type="checkbox" name="use_pxl" id="addUsePxl" value="1"
                                checked style="width: 3em; height: 1.5em; cursor: pointer;">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Status</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="addStatus1" value="1"
                                    checked>
                                <label class="form-check-label fw-bold text-success" for="addStatus1">AKTIF</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="addStatus0" value="0">
                                <label class="form-check-label fw-bold text-danger" for="addStatus0">NON-AKTIF</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                        data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow"
                        style="background: var(--primary-gradient)">
                        SIMPAN DATA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT SUB KATEGORI -->
<div class="modal fade" id="modalEditSubcategory" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-dark text-white p-4 border-0">
                <h5 class="modal-title fw-900 text-uppercase"><i class="fas fa-edit me-2"></i> Update Sub-Kategori</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="<?= base_url('warehouse/actionUpdateSubcategory') ?>" method="post">
                <input type="hidden" name="subkategori_id" id="edit_subkategori_id">
                <input type="hidden" name="redirect_kategori_id" value="<?= $filter_kategori ?>">
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Urutan</label>
                            <input name="subkategori_urutan" id="edit_subkategori_urutan" type="number"
                                class="form-control fw-bold" required
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Ganti Induk</label>
                            <select name="kategori_id" id="edit_kategori_id" class="form-select fw-bold" required
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;">
                                <?php foreach ($kategori as $k): ?>
                                    <option value="<?= $k->kategori_id ?>"><?= strtoupper($k->kategori_nama) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Nama Sub-Kategori</label>
                        <input name="subkategori_nama" id="edit_subkategori_nama" type="text"
                            class="form-control fw-bold" required
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;">
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Deskripsi</label>
                        <textarea name="subkategori_deskripsi" id="edit_subkategori_deskripsi"
                            class="form-control fw-bold" rows="2"
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;"></textarea>
                    </div>

                    <div class="mb-4 p-3 bg-light rounded-4 border">
                        <div class="form-check form-switch p-0 d-flex align-items-center justify-content-between">
                            <label class="fw-bold text-muted small text-uppercase mb-0" for="editUsePxl">Gunakan Dimensi
                                (P x L)</label>
                            <input class="form-check-input ms-0" type="checkbox" name="use_pxl" id="editUsePxl"
                                value="1" style="width: 3em; height: 1.5em; cursor: pointer;">
                        </div>
                        <div class="small text-muted mt-2 fw-bold" style="font-size: 0.65rem;">
                            <i class="fas fa-info-circle me-1"></i> Status: <span id="lblConfigStatus">Hanya
                                Quantity</span>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Status</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="editStatus1" value="1">
                                <label class="form-check-label fw-bold text-success" for="editStatus1">AKTIF</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="editStatus0" value="0">
                                <label class="form-check-label fw-bold text-danger" for="editStatus0">NON-AKTIF</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                        data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow"
                        style="background: var(--primary-gradient)">
                        UPDATE DATA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Client-side search
        $("#tableSearch").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#subkategoriTableBody tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Toggle label config di edit modal
        $('#editUsePxl').on('change', function () {
            $('#lblConfigStatus').text($(this).is(':checked') ? 'Dimensi Aktif' : 'Hanya Quantity');
        });

        // Auto-hide alert
        setTimeout(function () {
            $('#alert-container .alert').fadeOut('slow');
        }, 5000);
    });

    function openEditModal(id, nama, deskripsi, urutan, kategori, status, use_pxl) {
        $('#edit_subkategori_id').val(id);
        $('#edit_subkategori_nama').val(nama);
        $('#edit_subkategori_deskripsi').val(deskripsi);
        $('#edit_subkategori_urutan').val(urutan);
        $('#edit_kategori_id').val(kategori);

        if (status == 1) {
            $('#editStatus1').prop('checked', true);
        } else {
            $('#editStatus0').prop('checked', true);
        }

        if (use_pxl == 1) {
            $('#editUsePxl').prop('checked', true);
            $('#lblConfigStatus').text('Dimensi Aktif');
        } else {
            $('#editUsePxl').prop('checked', false);
            $('#lblConfigStatus').text('Hanya Quantity');
        }

        var myModal = new bootstrap.Modal(document.getElementById('modalEditSubcategory'));
        myModal.show();
    }

    function confirmDelete(url) {
        Swal.fire({
            title: 'Hapus Sub-Kategori?',
            text: "Pastikan tidak ada produk yang masih menggunakan sub-kategori ini.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff0055',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'YA, HAPUS!',
            cancelButtonText: 'BATAL',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        })
    }
</script>

<style>
    .truncate-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
    }
</style>