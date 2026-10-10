<div class="app-content mt-2">
    <div class="container-fluid">
        <!-- HEADER SECTION -->
        <?php $this->load->view('layout_admin/header_modul'); ?>
<?php $this->load->view('admin/pengaturan/_settings_nav'); ?>

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
            <!-- STATS: TOTAL KATEGORI -->
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm border-0 h-100" style="background: white;">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-v2-icon text-white rounded-4 d-flex align-items-center justify-content-center shadow-sm"
                            style="background: var(--info-gradient); width: 52px; height: 52px; flex-shrink: 0;">
                            <i class="fas fa-layer-group fs-4"></i>
                        </div>
                        <div>
                            <div class="stat-v2-label text-muted fw-bold small text-uppercase"
                                style="letter-spacing: 0.5px;">Master Kategori</div>
                            <div class="stat-v2-value fw-900 text-dark" style="font-size: 1.5rem; line-height: 1;">
                                <?= number_format($total_rows) ?> <small class="text-muted fs-6 fw-normal">Item</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACTION: TAMBAH KATEGORI -->
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm border-0 h-100 premium-card-hover"
                    style="background: var(--primary-gradient); cursor: pointer;" data-bs-toggle="modal"
                    data-bs-target="#modalAddKategori">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-20 text-white rounded-4 d-flex align-items-center justify-content-center"
                            style="width: 52px; height: 52px; flex-shrink: 0;">
                            <i class="fas fa-plus-circle fs-3"></i>
                        </div>
                        <div>
                            <div class="text-white fw-900 text-uppercase small mb-0" style="letter-spacing: 0.5px;">
                                Tambah Baru</div>
                            <div class="text-white text-opacity-75 small fw-bold mt-1"
                                style="font-size: 0.7rem; line-height: 1.2;">Registrasi master kategori produk.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACTION: BULK EXPORT -->
            <div class="col-6 col-md-3">
                <a href="<?= base_url('pengaturan/export_all_schemas') ?>" class="text-decoration-none h-100">
                    <div class="summary-stat-card shadow-sm border-0 h-100 premium-card-hover"
                        style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="bg-white bg-opacity-10 text-info rounded-4 d-flex align-items-center justify-content-center shadow-sm"
                                style="width: 52px; height: 52px; flex-shrink: 0;">
                                <i class="fas fa-file-export fs-4"></i>
                            </div>
                            <div>
                                <div class="text-white fw-900 text-uppercase small mb-0" style="letter-spacing: 0.5px;">
                                    Backup Master</div>
                                <div class="text-white text-opacity-50 small fw-bold mt-1"
                                    style="font-size: 0.7rem; line-height: 1.2;">Backup kategori & sub-kategori.</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- ACTION: BULK IMPORT -->
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm border-0 h-100 premium-card-hover"
                    style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); cursor: pointer;"
                    data-bs-toggle="modal" data-bs-target="#modalImportSchema">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-10 text-warning rounded-4 d-flex align-items-center justify-content-center shadow-sm"
                            style="width: 52px; height: 52px; flex-shrink: 0;">
                            <i class="fas fa-file-import fs-4"></i>
                        </div>
                        <div>
                            <div class="text-white fw-900 text-uppercase small mb-0" style="letter-spacing: 0.5px;">
                                Sync Master</div>
                            <div class="text-white text-opacity-50 small fw-bold mt-1"
                                style="font-size: 0.7rem; line-height: 1.2;">Sinkronisasi data antar sistem.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- MAIN TABLE CARD -->
        <div class="report-section-card animate-up" style="animation-delay: 0.1s">
            <div class="section-header">
                <h5><i class="fas fa-folder-open me-2"></i> Daftar Kategori Produk</h5>
                <div class="d-flex gap-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="tableSearch" class="form-control border-0 shadow-none"
                            placeholder="Cari kategori...">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table premium-table table-hover mb-0" id="datatable-details">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">No</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th>Sub Kategori</th>
                            <th class="text-center" style="width: 150px;">Status</th>
                            <th class="text-center" style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="kategoriTableBody">
                        <?php
                        $no = 1;
                        foreach ($tampilData as $dt):
                            $is_active = ($dt->status == 1);
                            $status_class = $is_active ? 'bg-success-subtle text-success border-success-subtle' : 'bg-danger-subtle text-danger border-danger-subtle';
                            $status_label = $is_active ? 'AKTIF' : 'NON-AKTIF';
                            ?>
                            <tr>
                                <td class="text-center fw-bold text-muted"><?= $no++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light p-2 rounded-3 shadow-sm border me-3 d-flex align-items-center justify-content-center"
                                            style="width: 40px; height: 40px;">
                                            <i class="fas fa-folder text-warning opacity-75"></i>
                                        </div>
                                        <div>
                                            <div class="fw-800 text-dark"><?= strtoupper($dt->kategori_nama) ?></div>
                                            <div class="text-muted small fw-bold">Urutan: <?= $dt->kategori_urutan ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-muted small fw-bold text-break" style="line-height: 1.4;">
                                        <?= !empty($dt->kategori_deskripsi) ? $dt->kategori_deskripsi : '<span class="opacity-25 fst-italic">- Tidak ada deskripsi -</span>' ?>
                                    </div>
                                </td>
                                <td>
                                    <a href="<?= base_url('pengaturan/subkategori?kategori_id=' . $dt->kategori_id) ?>"
                                        class="text-decoration-none d-flex align-items-center gap-2 group">
                                        <span
                                            class="badge rounded-pill fw-bold px-3 py-2 <?= $dt->total_sub > 0 ? 'bg-primary shadow-sm' : 'bg-secondary opacity-50' ?>"
                                            style="font-size: 0.75rem;">
                                            <?= $dt->total_sub ?> <small class="fw-normal">Item</small>
                                        </span>
                                        <i class="fas fa-chevron-right text-muted small transition-transform"></i>
                                    </a>
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
                                        <a href="<?= base_url('pengaturan/kategori_form_builder/' . $dt->kategori_id) ?>"
                                            class="btn-action-premium btn-edit-p text-decoration-none"
                                            title="Configure Dynamic Form">
                                            <i class="fas fa-tools"></i>
                                        </a>
                                        <button type="button" class="btn-action-premium btn-edit-p" title="Ubah Data"
                                            onclick="openEditModal('<?= $dt->kategori_id ?>', '<?= addslashes($dt->kategori_nama) ?>', '<?= addslashes(trim($dt->kategori_deskripsi)) ?>', '<?= $dt->kategori_urutan ?>', '<?= $dt->status ?>')">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                        <button type="button"
                                            onclick="confirmDelete('<?= base_url('pengaturan/kategori_delete/' . $dt->kategori_id) ?>')"
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
                    <h6 class="fw-800 text-muted">Belum ada data kategori yang tersedia.</h6>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MODAL ADD KATEGORI -->
<div class="modal fade" id="modalAddKategori" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-dark text-white p-4 border-0">
                <h5 class="modal-title fw-900 text-uppercase"><i class="fas fa-plus-circle me-2"></i> Tambah Kategori
                    Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="<?= base_url('pengaturan/kategori_add') ?>" method="post">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Urutan Tampil</label>
                        <input name="kategori_urutan" type="number" class="form-control fw-bold" placeholder="0"
                            required style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;">
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Nama Kategori</label>
                        <input name="kategori_nama" type="text" class="form-control fw-bold"
                            placeholder="Misal: A3+, Large Format..." required
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;">
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Deskripsi</label>
                        <textarea name="kategori_deskripsi" class="form-control fw-bold" rows="3"
                            placeholder="Opsional..."
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;"></textarea>
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

<!-- MODAL EDIT KATEGORI -->
<div class="modal fade" id="modalEditKategori" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-dark text-white p-4 border-0">
                <h5 class="modal-title fw-900 text-uppercase"><i class="fas fa-edit me-2"></i> Update Kategori</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="<?= base_url('pengaturan/kategori_update') ?>" method="post">
                <input type="hidden" name="kategori_id" id="edit_kategori_id">
                <div class="modal-body p-4">
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Urutan</label>
                            <input name="kategori_urutan" id="edit_kategori_urutan" type="number"
                                class="form-control fw-bold" required
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;">
                        </div>
                        <div class="col-6 text-end">
                            <label class="fw-bold text-muted small text-uppercase mb-2 d-block">ID Kategori</label>
                            <span class="badge bg-light text-dark border p-2">#<span
                                    id="display_kategori_id"></span></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Nama Kategori</label>
                        <input name="kategori_nama" id="edit_kategori_nama" type="text" class="form-control fw-bold"
                            required style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;">
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Deskripsi</label>
                        <textarea name="kategori_deskripsi" id="edit_kategori_deskripsi" class="form-control fw-bold"
                            rows="3"
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;"></textarea>
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
            $("#kategoriTableBody tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Auto-hide alert after 5 seconds
        setTimeout(function () {
            $('#alert-container .alert').fadeOut('slow');
        }, 5000);
    });

    function openEditModal(id, nama, deskripsi, urutan, status) {
        $('#edit_kategori_id').val(id);
        $('#display_kategori_id').text(id);
        $('#edit_kategori_nama').val(nama);
        $('#edit_kategori_deskripsi').val(deskripsi);
        $('#edit_kategori_urutan').val(urutan);

        if (status == 1) {
            $('#editStatus1').prop('checked', true);
        } else {
            $('#editStatus0').prop('checked', true);
        }

        var myModal = new bootstrap.Modal(document.getElementById('modalEditKategori'));
        myModal.show();
    }

    function confirmDelete(url) {
        Swal.fire({
            title: 'Hapus Kategori?',
            text: "Data yang dihapus mungkin memengaruhi relasi produk lainnya.",
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

<!-- MODAL IMPORT SCHEMA -->
<div class="modal fade" id="modalImportSchema" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-dark text-white p-4 border-0">
                <h5 class="modal-title fw-900 text-uppercase"><i class="fas fa-sync me-2"></i> Sync / Restore Master
                    Data
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="<?= base_url('pengaturan/import_all_schemas') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="mb-4 text-center">
                        <i class="fas fa-file-invoice fs-1 text-primary opacity-25 mb-3"></i>
                        <h6 class="fw-bold">Upload Backup JSON</h6>
                        <p class="text-muted small">Fitur ini akan memperbarui atau menambah data Kategori,
                            Sub-Kategori, dan Skema Form Builder berdasarkan file backup.</p>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Pilih File JSON (.json)</label>
                        <input type="file" name="schema_file" class="form-control fw-bold" accept=".json" required
                            style="border: 2px dashed #cbd5e1; border-radius: 12px; padding: 1.5rem; background: #f8fafc;">
                    </div>

                    <div class="p-3 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-3">
                        <p class="small text-danger mb-0 fw-bold">
                            <i class="fas fa-exclamation-triangle me-1"></i> PERINGATAN: Proses ini akan menimpa
                            (overwrite) skema form builder yang ada saat ini.
                        </p>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                        data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold shadow">
                        MULAI IMPORT DATA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>