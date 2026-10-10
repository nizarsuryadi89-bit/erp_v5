<?php $this->load->view('layout_admin/header_modul'); ?>

<div class="app-content mt-4">
    <div class="report-section-card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="section-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-1 text-dark fw-bold">
                    <i class="fas fa-print me-2 text-info"></i> Daftar Mesin Produksi
                </h4>
                <p class="mb-0 text-muted small">Melihat daftar seluruh mesin dan keterikatan Workcenter.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= site_url('Workcenter') ?>" class="btn btn-outline-primary rounded-pill px-4 shadow-sm fw-bold">
                    <i class="fas fa-buildings me-1"></i> Kembali ke Workcenter
                </a>
                <button class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" onclick="showAddModal()">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Mesin
                </button>
            </div>
        </div>

        <div class="p-0 bg-white">
            <div class="table-responsive">
                <table class="table premium-table table-hover align-middle mb-0" id="tableMesin">
                    <thead>
                        <tr class="bg-light">
                            <th class="text-center py-3" width="60" style="font-size: 0.65rem; color: #64748b; font-weight: 900;">NO</th>
                            <th class="py-3" style="font-size: 0.65rem; color: #64748b; font-weight: 900;">NAMA MESIN</th>
                            <th class="py-3" style="font-size: 0.65rem; color: #64748b; font-weight: 900;">LOKASI CABANG</th>
                            <th class="py-3" style="font-size: 0.65rem; color: #64748b; font-weight: 900;">WORKCENTER (KELOMPOK)</th>
                            <th class="text-center py-3" width="120" style="font-size: 0.65rem; color: #64748b; font-weight: 900;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($mesin as $m): ?>
                            <tr>
                                <td class="text-center">
                                    <span class="text-muted fw-800 small"><?= $no++ ?></span>
                                </td>
                                <td>
                                    <span class="fw-900 text-dark fs-6"><?= strtoupper($m->mesin_nama) ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($m->nama_perusahaan)): ?>
                                        <span class="fw-bold text-secondary" style="font-size: 0.75rem;"><i class="fas fa-map-marker-alt me-1 text-danger"></i> <?= strtoupper($m->nama_perusahaan) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small italic" style="font-size: 0.7rem;">- Belum diatur -</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($m->nama_workcenter)): ?>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill text-start px-3" style="font-size: 0.7rem;">
                                            <i class="fas fa-buildings me-1"></i> <?= strtoupper($m->nama_workcenter) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border rounded-pill text-start px-3" style="font-size: 0.7rem;">
                                            <i class="fas fa-times me-1"></i> BELUM DITAUTKAN
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-light btn-sm rounded-circle shadow-sm btn-icon" data-bs-toggle="tooltip" title="Edit Mesin"
                                            onclick="editMesin('<?= $m->id ?>')">
                                            <i class="fas fa-pencil-alt text-warning"></i>
                                        </button>
                                        <button class="btn btn-light btn-sm rounded-circle shadow-sm btn-icon" data-bs-toggle="tooltip" title="Hapus Mesin"
                                            onclick="deleteMesin('<?= $m->id ?>')">
                                            <i class="fas fa-trash-alt text-danger"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($mesin)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="opacity-25 mb-2"><i class="fas fa-print fa-3x"></i></div>
                                    <h6 class="text-muted fw-bold">Belum ada data mesin.</h6>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .btn-icon:hover {
        transform: translateY(-2px);
        background-color: #fff !important;
    }

    .premium-table thead th {
        border-top: none;
    }

    .premium-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .fw-900 {
        font-weight: 900;
    }

    .fw-800 {
        font-weight: 800;
    }
</style>

<!-- Modal Edit Mesin -->
<div class="modal fade" id="modalEditMesin" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-primary p-4">
                <h5 class="modal-title fw-bold text-white"><i class="fas fa-edit me-2"></i> Form Data Mesin</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <form id="formMesin">
                    <input type="hidden" name="id" id="val_id">

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted">NAMA MESIN</label>
                            <input type="text" name="mesin_nama" id="val_mesin_nama"
                                class="form-control fw-bold border-0 shadow-sm text-uppercase" placeholder="Misal: KINGJET KJ-604" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted">LOKASI CABANG</label>
                            <select name="perusahaan_id" id="val_perusahaan_id" class="form-select fw-bold border-0 shadow-sm">
                                <option value="">-- Tentukan Cabang --</option>
                                <?php foreach ($perusahaan as $p): ?>
                                    <option value="<?= $p->id_perusahaan ?>"><?= strtoupper($p->nama_perusahaan) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted">KETERANGAN / DESKRIPSI (Opsional)</label>
                            <input type="text" name="mesin_keterangan" id="val_mesin_keterangan"
                                class="form-control fw-bold border-0 shadow-sm" placeholder="Catatan mesin...">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 p-4 bg-white">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold"
                    onclick="saveMesin()">
                    <i class="fas fa-check-circle me-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function showAddModal() {
        $('#formMesin')[0].reset();
        $('#val_id').val('');
        new bootstrap.Modal(document.getElementById('modalEditMesin')).show();
        setTimeout(() => $('#val_mesin_nama').focus(), 500);
    }

    function editMesin(id) {
        Swal.fire({ title: 'Memuat data...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

        $.getJSON('<?= site_url('Workcenter/get_mesin_ajax/') ?>' + id, function (res) {
            Swal.close();
            $('#val_id').val(res.id);
            $('#val_mesin_nama').val(res.mesin_nama);
            $('#val_perusahaan_id').val(res.perusahaan_id);
            $('#val_mesin_keterangan').val(res.mesin_keterangan);

            new bootstrap.Modal(document.getElementById('modalEditMesin')).show();
        });
    }

    function saveMesin() {
        const nama = $('#val_mesin_nama').val();
        if (!nama) {
            Swal.fire('Error', 'Nama mesin tidak boleh kosong!', 'error');
            return;
        }

        const formData = $('#formMesin').serialize();

        Swal.fire({ title: 'Menyimpan perubahan...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

        $.ajax({
            url: '<?= site_url('Workcenter/save_mesin_ajax') ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false }).then(() => location.reload());
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            }
        });
    }

    function deleteMesin(id) {
        Swal.fire({
            title: 'Hapus Mesin?',
            text: "Data mesin yang dihapus tidak bisa dikembalikan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Menghapus...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                $.getJSON('<?= site_url('Workcenter/delete_mesin_ajax/') ?>' + id, function (res) {
                    if (res.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'Dihapus!', text: res.message, timer: 1500 }).then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                });
            }
        });
    }
</script>
