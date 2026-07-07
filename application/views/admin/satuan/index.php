<div class="app-content mt-2">
    <div class="container-fluid">
        <!-- HEADER SECTION REPLACED WITH STANDARD MODULE HEADER -->
        <?php $this->load->view('layout_admin/header_modul'); ?>
<?php $this->load->view('admin/pengaturan/_settings_nav'); ?>

        <!-- Flash Messages -->
        <div id="alert-container">
            <?= $this->session->flashdata('message'); ?>
        </div>

        <!-- STATISTICS SECTION -->
        <div class="row g-3 mb-4 animate-up">
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--primary-gradient)">
                        <i class="fas fa-ruler-combined"></i>
                    </div>
                    <div>
                        <div class="stat-v2-label">Total Satuan</div>
                        <div class="stat-v2-value"><?= number_format($total_rows) ?></div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--success-gradient)">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <div class="stat-v2-label">Unit Dasar</div>
                        <div class="stat-v2-value">
                            <?= count(array_filter($satuan, function ($s) {
                                return $s->satuan_turunan == 1; })) ?></div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--info-gradient)">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div>
                        <div class="stat-v2-label">Unit Turunan</div>
                        <div class="stat-v2-value">
                            <?= count(array_filter($satuan, function ($s) {
                                return $s->satuan_turunan > 1; })) ?></div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm d-flex flex-row align-items-center justify-content-center p-0"
                    style="background: var(--warning-gradient);">
                    <button type="button"
                        class="btn btn-link text-white text-decoration-none w-100 h-100 fw-900 text-uppercase ls-1"
                        data-bs-toggle="modal" data-bs-target="#modalAddSatuan">
                        <i class="fas fa-plus-circle fa-2x mb-2 d-block"></i>
                        Tambah Satuan
                    </button>
                </div>
            </div>
        </div>

        <!-- MAIN TABLE CARD -->
        <div class="report-section-card animate-up" style="animation-delay: 0.1s">
            <div class="section-header">
                <h5><i class="fas fa-ruler me-2"></i> Daftar Satuan & Unit Pengukuran</h5>
                <div class="d-flex gap-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="tableSearch" class="form-control border-0 shadow-none"
                            placeholder="Cari satuan...">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table premium-table table-hover mb-0" id="satuanTable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">No</th>
                            <th>Nama Satuan</th>
                            <th class="text-center">Turunan / Multiplier</th>
                            <th style="width: 40%;">Deskripsi</th>
                            <th class="text-center" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="satuanTableBody">
                        <?php
                        $no = 1;
                        foreach ($satuan as $s):
                            $is_base = ($s->satuan_turunan == 1);
                            ?>
                            <tr>
                                <td class="text-center fw-bold text-muted"><?= $no++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light p-2 rounded-3 shadow-sm border me-3 d-flex align-items-center justify-content-center"
                                            style="width: 40px; height: 40px;">
                                            <i class="fas fa-tag small text-dark opacity-50"></i>
                                        </div>
                                        <div>
                                            <div class="fw-800 text-dark"><?= strtoupper($s->satuan_nama) ?></div>
                                            <div class="text-muted small fw-bold text-uppercase"
                                                style="font-size: 0.65rem;">
                                                ID: <?= $s->satuan_id ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php if ($is_base): ?>
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-bold"
                                            style="font-size: 0.7rem;">
                                            BASE UNIT (1)
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3 py-2 fw-bold"
                                            style="font-size: 0.7rem;">
                                            <?= $s->satuan_turunan ?> UNIT
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="text-muted small fw-bold text-break" style="line-height: 1.4;">
                                        <?= !empty($s->satuan_deskripsi) ? $s->satuan_deskripsi : '<span class="opacity-25 fst-italic">- Tidak ada deskripsi -</span>' ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <button type="button" class="btn-action-premium btn-edit-p" title="Ubah Data"
                                            onclick="openEditModal('<?= $s->satuan_id ?>', '<?= addslashes($s->satuan_nama) ?>', '<?= $s->satuan_turunan ?>', '<?= addslashes(trim($s->satuan_deskripsi)) ?>')">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                        <button type="button"
                                            onclick="confirmDelete('<?= base_url('pengaturan/satuan_delete/' . $s->satuan_id) ?>')"
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
            <?php if (empty($satuan)): ?>
                <div class="p-5 text-center">
                    <i class="fas fa-ruler-horizontal fs-1 text-muted opacity-25 mb-3"></i>
                    <h6 class="fw-800 text-muted">Belum ada data satuan yang tersedia.</h6>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MODAL ADD SATUAN -->
<div class="modal fade" id="modalAddSatuan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-dark text-white p-4 border-0">
                <h5 class="modal-title fw-900 text-uppercase"><i class="fas fa-plus-circle me-2"></i> Tambah Satuan Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="<?= base_url('pengaturan/satuan_add') ?>" method="post">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Nama Satuan</label>
                        <input name="satuan_nama" type="text" class="form-control"
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                            placeholder="Misal: PCS, METER, BOX..." required>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Multiplier / Turunan</label>
                        <input name="satuan_turunan" type="number" class="form-control"
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                            placeholder="1" value="1" required>
                        <div class="form-text small mt-1 text-muted"><i class="fas fa-info-circle me-1"></i> Gunakan
                            angka <strong>1</strong> untuk satuan dasar.</div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Keterangan / Deskripsi</label>
                        <textarea name="satuan_deskripsi" class="form-control" rows="3"
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                            placeholder="Jelaskan detail satuan ini..."></textarea>
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

<!-- MODAL EDIT SATUAN -->
<div class="modal fade" id="modalEditSatuan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-dark text-white p-4 border-0">
                <h5 class="modal-title fw-900 text-uppercase"><i class="fas fa-edit me-2"></i> Update Data Satuan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="<?= base_url('pengaturan/satuan_update') ?>" method="post">
                <input type="hidden" name="satuan_id" id="edit_satuan_id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="fw-bold text-muted small text-uppercase mb-0">Nama Satuan</label>
                            <span class="badge bg-light text-dark border">ID: <span
                                    id="display_satuan_id"></span></span>
                        </div>
                        <input name="satuan_nama" id="edit_satuan_nama" type="text" class="form-control"
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Multiplier / Turunan</label>
                        <input name="satuan_turunan" id="edit_satuan_turunan" type="number" class="form-control"
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Keterangan / Deskripsi</label>
                        <textarea name="satuan_deskripsi" id="edit_satuan_deskripsi" class="form-control" rows="3"
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"></textarea>
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
    function openEditModal(id, nama, turunan, deskripsi) {
        $('#edit_satuan_id').val(id);
        $('#display_satuan_id').text(id);
        $('#edit_satuan_nama').val(nama);
        $('#edit_satuan_turunan').val(turunan);
        $('#edit_satuan_deskripsi').val(deskripsi);

        var myModal = new bootstrap.Modal(document.getElementById('modalEditSatuan'));
        myModal.show();
    }

    function confirmDelete(url) {
        Swal.fire({
            title: 'Hapus Satuan?',
            text: "Pastikan satuan ini tidak sedang digunakan oleh produk apa pun.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f72585',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'YA, HAPUS!',
            cancelButtonText: 'BATAL',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        })
    }

    // Client-side search for smoother UX
    $(document).ready(function () {
        $("#tableSearch").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#satuanTableBody tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Auto-hide alert after 5 seconds
        setTimeout(function () {
            $('#alert-container .alert').fadeOut('slow');
        }, 5000);
    });
</script>