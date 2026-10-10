<!-- External CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">
<!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

<style>
    .modal-content {
        border-radius: 0.5rem;
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .modal-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
</style>

<!-- <div class="app-content"> -->
<!-- Header -->
<?php $this->load->view('layout_admin/header_modul'); ?>

<!-- Main Content -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="card-title text-dark mb-0 d-flex align-items-center">
                        <i class="fas fa-bullhorn me-2 text-primary"></i>
                        <span class="fw-bold">Daftar Pengumuman Internal</span>
                    </h6>
                    <button class="btn btn-primary btn-sm px-3" onclick="addPengumuman()">
                        <i class="fas fa-plus me-1"></i> Tambah Pengumuman
                    </button>
                </div>

                <!-- Flash Messages -->
                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?= $this->session->flashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?= $this->session->flashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="dataTable">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th>Judul Pengumuman</th>
                                <th class="text-center">Kategori</th>
                                <th class="text-center">Periode Tampil</th>
                                <th class="text-center">Status</th>
                                <th>Dibuat Oleh</th>
                                <th class="text-center" width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($pengumuman as $p): ?>
                                <tr>
                                    <td class="text-center text-muted"><?= $no++ ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= $p->judul ?></div>
                                        <small class="text-muted"><?= substr(strip_tags($p->isi), 0, 60) ?>...</small>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $badge = 'bg-info';
                                        if ($p->kategori == 'Penting')
                                            $badge = 'bg-danger';
                                        if ($p->kategori == 'Promo')
                                            $badge = 'bg-success';
                                        if ($p->kategori == 'Kegiatan')
                                            $badge = 'bg-warning text-dark';
                                        ?>
                                        <span class="badge <?= $badge ?> rounded-pill px-3"><?= $p->kategori ?></span>
                                    </td>
                                    <td class="text-center">
                                        <small
                                            class="text-dark fw-bold"><?= date('d M Y', strtotime($p->tanggal_mulai)) ?></small>
                                        <div class="small text-muted">s/d
                                            <?= date('d M Y', strtotime($p->tanggal_selesai)) ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($p->status): ?>
                                            <span
                                                class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2"
                                                style="font-size: 0.7rem;">AKTIF</span>
                                        <?php else: ?>
                                            <span
                                                class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2"
                                                style="font-size: 0.7rem;">NON-AKTIF</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="small fw-bold"><?= $p->pembuat ?></div>
                                        <div class="small text-muted" style="font-size: 0.65rem;">
                                            <?= date('d/m/y H:i', strtotime($p->created_at)) ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button class="btn btn-sm btn-outline-info"
                                                onclick="editPengumuman(<?= $p->id ?>)" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="<?= base_url('content/hapus_pengumuman/' . $p->id) ?>"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Hapus pengumuman ini?')" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="modalPengumuman" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="<?= base_url('content/simpan_pengumuman') ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTitle">
                        <i class="fas fa-bullhorn me-2 text-primary"></i>Tambah Pengumuman
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="p_id">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Judul Pengumuman <span
                                class="text-danger">*</span></label>
                        <input type="text" name="judul" id="p_judul" class="form-control" required
                            placeholder="Contoh: Jadwal Libur Lebaran">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Kategori</label>
                                <select name="kategori" id="p_kategori" class="form-select">
                                    <option value="Info">Info</option>
                                    <option value="Penting">Penting</option>
                                    <option value="Promo">Promo</option>
                                    <option value="Kegiatan">Kegiatan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 pt-4">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="status" id="p_status" checked>
                                    <label class="form-check-label fw-bold small" for="p_status">Aktifkan
                                        Pengumuman</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Tanggal Mulai <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="tanggal_mulai" id="p_mulai" class="form-control" required
                                    value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Tanggal Selesai <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="tanggal_selesai" id="p_selesai" class="form-control" required
                                    value="<?= date('Y-m-d', strtotime('+7 days')) ?>">
                            </div>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold">Isi Pengumuman <span
                                class="text-danger">*</span></label>
                        <textarea name="isi" id="p_isi" class="form-control" rows="5" required
                            placeholder="Tulis rincian pengumuman di sini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-link text-muted text-decoration-none"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i> Simpan & Publikasikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        if ($('#dataTable').length) {
            $('#dataTable').DataTable({
                responsive: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Lanjut",
                        previous: "Kembali"
                    },
                    emptyTable: "Tidak ada data pengumuman"
                }
            });
        }

        // Initialize Summernote
        $('#p_isi').summernote({
            placeholder: 'Tulis rincian pengumuman di sini...',
            tabsize: 2,
            height: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });

    function addPengumuman() {
        $('#p_id').val('');
        $('#modalTitle').html('<i class="fas fa-bullhorn me-2 text-primary"></i>Tambah Pengumuman Baru');
        $('#modalPengumuman form')[0].reset();
        $('#p_isi').summernote('code', ''); // Reset Summernote
        $('#p_status').prop('checked', true);
        const modal = new bootstrap.Modal(document.getElementById('modalPengumuman'));
        modal.show();
    }

    function editPengumuman(id) {
        $.getJSON('<?= base_url('content/get_pengumuman_data/') ?>' + id, function (data) {
            $('#p_id').val(data.id);
            $('#p_judul').val(data.judul);
            $('#p_kategori').val(data.kategori);
            $('#p_mulai').val(data.tanggal_mulai);
            $('#p_selesai').val(data.tanggal_selesai);
            $('#p_isi').summernote('code', data.isi); // Set Summernote content
            $('#p_status').prop('checked', data.status == 1);

            $('#modalTitle').html('<i class="fas fa-edit me-2 text-primary"></i>Edit Pengumuman');
            const modal = new bootstrap.Modal(document.getElementById('modalPengumuman'));
            modal.show();
        });
    }
</script>
<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>