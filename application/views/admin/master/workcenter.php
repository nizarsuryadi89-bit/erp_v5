<?php $this->load->view('layout_admin/header_modul'); ?>

<div class="app-content mt-4">
    <div class="report-section-card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="section-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-1 text-dark fw-bold">
                    <i class="fas fa-buildings me-2 text-primary"></i> Manajemen Workcenter
                </h4>
                <p class="mb-0 text-muted small">Kelola stasiun kerja dan mesin yang terdaftar di dalamnya.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= site_url('OperatorBersama') ?>"
                    class="btn btn-outline-primary rounded-pill px-4 shadow-sm fw-bold">
                    <i class="fas fa-users-cog me-1"></i> Akun Stasiun
                </a>
                <button class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" onclick="showAddModal()">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Workcenter
                </button>
            </div>
        </div>

        <div class="p-0 bg-white">
            <div class="table-responsive">
                <table class="table premium-table table-hover align-middle mb-0" id="tableWorkcenter">
                    <thead>
                        <tr class="bg-light">
                            <th class="text-center py-3" width="60"
                                style="font-size: 0.65rem; color: #64748b; font-weight: 900;">NO</th>
                            <th class="py-3" style="font-size: 0.65rem; color: #64748b; font-weight: 900;">NAMA
                                WORKCENTER</th>
                            <th class="py-3" style="font-size: 0.65rem; color: #64748b; font-weight: 900;">MESIN
                                TERDAFTAR</th>
                            <th class="text-center py-3" width="150"
                                style="font-size: 0.65rem; color: #64748b; font-weight: 900;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($workcenters as $wc): ?>
                            <tr>
                                <td class="text-center">
                                    <span class="text-muted fw-800 small"><?= $no++ ?></span>
                                </td>
                                <td>
                                    <span class="fw-900 text-dark fs-6"><?= strtoupper($wc->operator_nama) ?></span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php if (!empty($wc->mesins)): ?>
                                            <?php foreach ($wc->mesins as $m): ?>
                                                <span
                                                    class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill text-start"
                                                    style="font-size: 0.65rem;">
                                                    <i class="fas fa-print me-1"></i> <?= strtoupper($m->mesin_nama) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted border rounded-pill text-start"
                                                style="font-size: 0.65rem;">
                                                BELUM ADA MESIN
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-light btn-sm rounded-circle shadow-sm btn-icon"
                                            data-bs-toggle="tooltip" title="Edit Data"
                                            onclick="editWorkcenter('<?= $wc->operator_id ?>')">
                                            <i class="fas fa-pencil-alt text-primary"></i>
                                        </button>
                                        <button class="btn btn-light btn-sm rounded-circle shadow-sm btn-icon"
                                            data-bs-toggle="tooltip" title="Hapus Workcenter"
                                            onclick="deleteWorkcenter('<?= $wc->operator_id ?>')">
                                            <i class="fas fa-trash-alt text-danger"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($workcenters)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="opacity-25 mb-2"><i class="fas fa-box-open fa-3x"></i></div>
                                    <h6 class="text-muted fw-bold">Belum ada workcenter.</h6>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Workcenter -->
<div class="modal fade" id="modalEditWorkcenter" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-primary p-4">
                <h5 class="modal-title fw-bold text-white"><i class="fas fa-edit me-2"></i> Form Workcenter</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <form id="formWorkcenter">
                    <input type="hidden" name="operator_id" id="val_operator_id">

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted">NAMA WORKCENTER</label>
                            <input type="text" name="operator_nama" id="val_operator_nama"
                                class="form-control fw-bold border-0 shadow-sm text-uppercase"
                                placeholder="Misal: DIGITAL A3+" required>
                        </div>
                        <div class="col-md-12 mt-3">
                            <div class="p-3 rounded-4 bg-white border border-dashed border-info">
                                <label class="form-label fw-bold small text-info"><i class="fas fa-print me-1"></i>
                                    PILIH MESIN (MAPPING)</label>
                                <select name="mesin_ids[]" id="val_mesin_ids"
                                    class="form-select border-0 shadow-sm fw-bold bg-light" multiple="multiple"
                                    style="height: 150px;">
                                    <?php foreach ($list_mesin as $lm): ?>
                                        <option value="<?= $lm->id ?>"><?= strtoupper($lm->mesin_nama) ?>
                                            (<?= $lm->workcenter_id ? 'TERPAKAI' : 'KOSONG' ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="text-muted" style="font-size: 0.65rem; margin-top: 5px;">Tahan tombol CTRL
                                    (atau CMD di Mac) untuk memilih lebih dari satu mesin sekaligus.</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 p-4 bg-white">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold"
                    onclick="saveWorkcenter()">
                    <i class="fas fa-check-circle me-1"></i> Simpan
                </button>
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

<script>
    function showAddModal() {
        $('#formWorkcenter')[0].reset();
        $('#val_operator_id').val('');
        $('#val_mesin_ids').val([]);
        new bootstrap.Modal(document.getElementById('modalEditWorkcenter')).show();
        setTimeout(() => $('#val_operator_nama').focus(), 500);
    }

    function editWorkcenter(id) {
        Swal.fire({ title: 'Memuat data...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

        $.getJSON('<?= site_url('Workcenter/get_workcenter_ajax/') ?>' + id, function (res) {
            Swal.close();
            $('#val_operator_id').val(res.operator_id);
            $('#val_operator_nama').val(res.operator_nama);
            if (res.mesin_ids && res.mesin_ids.length > 0) {
                $('#val_mesin_ids').val(res.mesin_ids);
            } else {
                $('#val_mesin_ids').val([]);
            }

            new bootstrap.Modal(document.getElementById('modalEditWorkcenter')).show();
        });
    }

    function saveWorkcenter() {
        const nama = $('#val_operator_nama').val();
        if (!nama) {
            Swal.fire('Error', 'Nama workcenter tidak boleh kosong!', 'error');
            return;
        }

        const formData = $('#formWorkcenter').serialize();

        Swal.fire({ title: 'Menyimpan perubahan...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

        $.ajax({
            url: '<?= site_url('Workcenter/save_workcenter_ajax') ?>',
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

    function deleteWorkcenter(id) {
        Swal.fire({
            title: 'Hapus Workcenter?',
            text: "Workcenter yang sedang digunakan oleh akun stasiun tidak dapat dihapus. Anda yakin ingin melanjutkan?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Menghapus...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                $.getJSON('<?= site_url('Workcenter/delete_workcenter_ajax/') ?>' + id, function (res) {
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