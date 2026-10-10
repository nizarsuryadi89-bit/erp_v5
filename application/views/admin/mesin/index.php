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
                        <i class="fas fa-microchip"></i>
                    </div>
                    <div>
                        <div class="stat-v2-label">Total Mesin</div>
                        <div class="stat-v2-value"><?= number_format($total_rows) ?></div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--success-gradient)">
                        <i class="fas fa-industry"></i>
                    </div>
                    <div>
                        <div class="stat-v2-label">Produksi</div>
                        <div class="stat-v2-value">
                            <?= count(array_filter($tampilData, function ($item) {
                                return $item->kategori == 'Produksi'; })) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--warning-gradient)">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div>
                        <div class="stat-v2-label">Finishing</div>
                        <div class="stat-v2-value">
                            <?= count(array_filter($tampilData, function ($item) {
                                return $item->kategori == 'Finishing'; })) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--info-gradient)">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div>
                        <div class="stat-v2-label">Lokasi Workshop</div>
                        <div class="stat-v2-value">
                            <?= count(array_unique(array_column($tampilData, 'nama_perusahaan'))) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN TABLE CARD -->
        <div class="report-section-card animate-up" style="animation-delay: 0.1s">
            <div class="section-header">
                <h5><i class="fas fa-desktop me-2"></i> Daftar Peralatan & Mesin Produksi</h5>
                <div class="d-flex gap-2">
                    <a href="<?= base_url('pengaturan/mesin_aktifitas') ?>"
                        class="btn btn-sm btn-white text-dark rounded-pill px-3 fw-bold shadow-sm">
                        <i class="fas fa-chart-line text-primary me-1"></i> Aktifitas
                    </a>
                    <button type="button" class="btn btn-sm btn-white text-dark rounded-pill px-3 fw-bold shadow-sm"
                        data-bs-toggle="modal" data-bs-target="#modalAddMesin">
                        <i class="fas fa-plus text-success me-1"></i> Tambah Mesin
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table premium-table table-hover mb-0" id="mesinTable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Nama Mesin & Spesifikasi</th>
                            <th>Kategori</th>
                            <th>Lokasi Workshop</th>
                            <th class="text-center" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        foreach ($tampilData as $dt):
                            $is_produksi = ($dt->kategori == 'Produksi');
                            $cat_bg = $is_produksi ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning';
                            $cat_icon = $is_produksi ? 'fa-print' : 'fa-cut';
                            ?>
                            <tr>
                                <td class="text-center fw-bold text-muted"><?= $no++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light p-2 rounded-3 shadow-sm border me-3 d-flex align-items-center justify-content-center"
                                            style="width: 40px; height: 40px;">
                                            <i class="fas <?= $cat_icon ?> small text-dark opacity-50"></i>
                                        </div>
                                        <div>
                                            <div class="fw-800 text-dark"><?= strtoupper($dt->mesin_nama) ?></div>
                                            <div class="text-muted small fw-bold text-break" style="font-size: 0.75rem;">
                                                <?= $dt->mesin_keterangan ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="badge <?= $cat_bg ?> border border-opacity-25 rounded-pill px-3 py-1 fw-bold"
                                        style="font-size: 0.65rem;">
                                        <?= strtoupper($dt->kategori) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center text-dark fw-bold small text-uppercase ls-1">
                                        <i class="fas fa-building me-2 text-muted opacity-50"></i>
                                        <?= $dt->nama_perusahaan ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <button type="button" class="btn-action-premium btn-edit-p" title="Ubah Data"
                                            onclick="openEditModal('<?= $dt->mesin_id ?>', '<?= addslashes($dt->mesin_nama) ?>', '<?= addslashes($dt->mesin_keterangan) ?>', '<?= $dt->kategori ?>', '<?= $dt->perusahaan_id ?>')">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                        <button type="button"
                                            onclick="confirmDelete('<?= base_url('pengaturan/mesin_delete/' . $dt->mesin_id) ?>')"
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
                    <i class="fas fa-microchip fs-1 text-muted opacity-25 mb-3"></i>
                    <h6 class="fw-800 text-muted">Belum ada data mesin yang tersedia.</h6>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MODAL ADD MESIN using Premium Style -->
<div class="modal fade" id="modalAddMesin" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-dark text-white p-4 border-0">
                <h5 class="modal-title fw-900 text-uppercase"><i class="fas fa-plus-circle me-2"></i> Tambah Mesin Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="<?= base_url('pengaturan/mesin_simpan') ?>" method="post">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Nama Mesin</label>
                        <input name="mesin_nama" type="text" class="form-control"
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                            placeholder="Misal: Roland RF-640..." required>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Keterangan / Spesifikasi</label>
                        <textarea name="mesin_keterangan" class="form-control" rows="2"
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                            placeholder="Detail spesifikasi mesin..."></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Kategori</label>
                            <select name="kategori" class="form-select"
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                                required>
                                <option value="Produksi">PRODUKSI</option>
                                <option value="Finishing">FINISHING</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Lokasi Workshop</label>
                            <select name="perusahaan_id" class="form-select"
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                                required>
                                <option value="">- Pilih Lokasi -</option>
                                <?php foreach ($list_perusahaan as $p): ?>
                                    <option value="<?= $p->id_perusahaan ?>"><?= $p->nama_perusahaan ?></option>
                                <?php endforeach; ?>
                            </select>
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

<!-- MODAL EDIT MESIN -->
<div class="modal fade" id="modalEditMesin" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-dark text-white p-4 border-0">
                <h5 class="modal-title fw-900 text-uppercase"><i class="fas fa-edit me-2"></i> Update Data Mesin</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="<?= base_url('pengaturan/mesin_update') ?>" method="post">
                <input type="hidden" name="mesin_id" id="edit_mesin_id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="fw-bold text-muted small text-uppercase mb-0">Nama Mesin</label>
                            <span class="badge bg-light text-dark border">ID: <span id="display_mesin_id"></span></span>
                        </div>
                        <input name="mesin_nama" id="edit_mesin_nama" type="text"
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                            class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Keterangan / Spesifikasi</label>
                        <textarea name="mesin_keterangan" id="edit_mesin_keterangan" class="form-control" rows="2"
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Kategori</label>
                            <select name="kategori" id="edit_mesin_kategori" class="form-select"
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                                required>
                                <option value="Produksi">PRODUKSI</option>
                                <option value="Finishing">FINISHING</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Lokasi Workshop</label>
                            <select name="perusahaan_id" id="edit_mesin_perusahaan" class="form-select"
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                                required>
                                <?php foreach ($list_perusahaan as $p): ?>
                                    <option value="<?= $p->id_perusahaan ?>"><?= $p->nama_perusahaan ?></option>
                                <?php endforeach; ?>
                            </select>
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
    function openEditModal(id, nama, keterangan, kategori, perusahaan) {
        $('#edit_mesin_id').val(id);
        $('#display_mesin_id').text(id);
        $('#edit_mesin_nama').val(nama);
        $('#edit_mesin_keterangan').val(keterangan);
        $('#edit_mesin_kategori').val(kategori);
        $('#edit_mesin_perusahaan').val(perusahaan);

        var myModal = new bootstrap.Modal(document.getElementById('modalEditMesin'));
        myModal.show();
    }

    function confirmDelete(url) {
        Swal.fire({
            title: 'Hapus Mesin?',
            text: "Data mesin yang dihapus tidak dapat dipulihkan!",
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

    $(document).ready(function () {
        if ($.fn.DataTable) {
            $('#mesinTable').DataTable({
                "pageLength": 10,
                "language": { "sSearch": "Cari Mesin:", "oPaginate": { "sNext": "<i class='fas fa-chevron-right'></i>", "sPrevious": "<i class='fas fa-chevron-left'></i>" } },
                "dom": "<'row px-4 py-3'<'col-md-6'l><'col-md-6 d-flex justify-content-end text-end'f>>t<'row px-4 py-3'<'col-md-6'i><'col-md-6 d-flex justify-content-end text-end'p>>"
            });
        }

        // Auto-hide alert after 5 seconds
        setTimeout(function () {
            $('#alert-container .alert').fadeOut('slow');
        }, 5000);
    });
</script>