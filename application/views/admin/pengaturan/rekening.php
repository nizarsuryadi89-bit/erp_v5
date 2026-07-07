<div class="app-content mt-2">
    <div class="container-fluid">
        <!-- HEADER SECTION -->
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
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div>
                        <div class="stat-v2-label">Total Rekening</div>
                        <div class="stat-v2-value"><?= number_format(count($tampilData)) ?></div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--success-gradient)">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div>
                        <div class="stat-v2-label">Rekening Aktif</div>
                        <div class="stat-v2-value">
                            <?= count(array_filter($tampilData, function ($item) {
                                return isset($item->active) && $item->active == 1;
                            })) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--info-gradient)">
                        <i class="fas fa-building-columns"></i>
                    </div>
                    <div>
                        <div class="stat-v2-label">Entitas Bank</div>
                        <div class="stat-v2-value"><?= count(array_unique(array_column($tampilData, 'nama_bank'))) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm d-flex flex-row align-items-center justify-content-center p-0"
                    style="background: var(--primary-gradient);">
                    <button type="button"
                        class="btn btn-link text-white text-decoration-none w-100 h-100 fw-900 text-uppercase ls-1"
                        data-bs-toggle="modal" data-bs-target="#modalTambahRekening">
                        <i class="fas fa-plus-circle fa-2x mb-2 d-block"></i>
                        Tambah Rekening
                    </button>
                </div>
            </div>
        </div>

        <!-- MAIN TABLE CARD -->
        <div class="report-section-card animate-up" style="animation-delay: 0.1s">
            <div class="section-header">
                <h5><i class="fas fa-vault me-2"></i> Vault Rekening Perusahaan</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-light btn-premium rounded-pill px-3 shadow-sm btn-sm fw-bold"
                        onclick="exportToExcel()">
                        <i class="fas fa-file-excel text-success me-1"></i> Excel
                    </button>
                    <button class="btn btn-light btn-premium rounded-pill px-3 shadow-sm btn-sm fw-bold"
                        onclick="printTable()">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table premium-table table-hover mb-0" id="datatable-rekening">
                    <thead>
                        <tr>
                            <th width="50" class="text-center">No</th>
                            <th>Identitas Rekening</th>
                            <th>Lembaga Keuangan</th>
                            <th>Pemegang Rekening</th>
                            <th class="text-end">Total Saldo Masuk</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-rekening">
                        <?php $no = 1;
                        foreach ($tampilData as $dt): ?>
                            <tr>
                                <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                                <td>
                                    <div class="fw-900 text-dark fs-6" style="letter-spacing: 0.5px;">
                                        <?= $dt->no_rekening ?></div>
                                    <div class="small text-muted fw-bold opacity-50 text-uppercase">ID System:
                                        #<?= $dt->id ?></div>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-light text-primary border border-primary-subtle rounded-pill px-3 fw-bold">
                                        <?= strtoupper($dt->nama_bank) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= strtoupper($dt->atas_nama) ?></div>
                                </td>
                                <td class="text-end">
                                    <div class="fw-900 text-success fs-6">Rp
                                        <?= number_format($dt->total_masuk, 0, ',', '.') ?></div>
                                    <div class="small text-muted fw-bold" style="font-size: 0.7rem;">
                                        <?= $dt->total_transaksi ?> TRANSAKSI</div>
                                </td>
                                <td class="text-center">
                                    <?php if (isset($dt->active) && $dt->active == 1): ?>
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 fw-bold">ACTIVE</span>
                                    <?php else: ?>
                                        <span
                                            class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 fw-bold">INACTIVE</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="<?= base_url('pengaturan/aktifitas_rekening/' . $dt->id) ?>"
                                            class="btn-action-premium btn-edit-p text-decoration-none"
                                            title="Lihat Aktifitas">
                                            <i class="fas fa-chart-line"></i>
                                        </a>
                                        <button type="button" class="btn-action-premium btn-edit-p btn-edit-rekening"
                                            data-id="<?= $dt->id ?>" data-nomor="<?= $dt->no_rekening ?>"
                                            data-bank="<?= $dt->nama_bank ?>" data-atas="<?= $dt->atas_nama ?>"
                                            data-status="<?= $dt->active ?>" title="Edit Rekening">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                        <button type="button" class="btn-action-premium btn-delete-p btn-hapus-rekening"
                                            data-id="<?= $dt->id ?>" data-nomor="<?= $dt->no_rekening ?>"
                                            title="Hapus Rekening">
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
                    <i class="fas fa-wallet fs-1 text-muted opacity-25 mb-3"></i>
                    <h6 class="fw-800 text-muted">Belum ada data rekening.</h6>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Tambah Rekening -->
<div class="modal fade" id="modalTambahRekening" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formTambahRekening" class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 bg-dark text-white p-4">
                <h5 class="modal-title fw-900 text-uppercase"><i class="fas fa-plus-circle me-2"></i> Registrasi
                    Rekening</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Lembaga Perbankan <span
                            class="text-danger">*</span></label>
                    <input type="text" name="nama_bank" class="form-control fw-bold"
                        placeholder="Contoh: BCA, Mandiri, BNI"
                        style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;" required>
                </div>
                <div class="mb-3">
                    <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Nomor Rekening <span
                            class="text-danger">*</span></label>
                    <input type="text" name="no_rekening" class="form-control fw-bold" placeholder="xxxx-xxxx-xxxx"
                        style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;" required>
                </div>
                <div class="mb-3">
                    <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Nama Pemegang Rekening <span
                            class="text-danger">*</span></label>
                    <input type="text" name="atas_nama" class="form-control fw-bold" placeholder="Atas Nama"
                        style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;" required>
                </div>
                <div class="form-check form-switch mt-4 p-3 bg-light rounded-3">
                    <input class="form-check-input ms-0 me-3" type="checkbox" name="status" value="1"
                        id="flexSwitchCheckChecked" checked style="cursor: pointer;">
                    <label class="form-check-label fw-bold text-muted" for="flexSwitchCheckChecked"
                        style="cursor: pointer;">Status Rekening Aktif</label>
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

<!-- Modal Edit Rekening -->
<div class="modal fade" id="modalEditRekening" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formEditRekening" class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 bg-dark text-white p-4">
                <h5 class="modal-title fw-900 text-uppercase"><i class="fas fa-edit me-2"></i> Update Data Rekening</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="rekening_id" id="edit_rekening_id">
                <div class="mb-3">
                    <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Lembaga Perbankan</label>
                    <input type="text" name="nama_bank" id="edit_nama_bank" class="form-control fw-bold"
                        style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;" required>
                </div>
                <div class="mb-3">
                    <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Nomor Rekening</label>
                    <input type="text" name="no_rekening" id="edit_no_rekening" class="form-control fw-bold"
                        style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;" required>
                </div>
                <div class="mb-3">
                    <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Nama Pemegang Rekening</label>
                    <input type="text" name="atas_nama" id="edit_atas_nama" class="form-control fw-bold"
                        style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;" required>
                </div>
                <div class="form-check form-switch mt-4 p-3 bg-light rounded-3">
                    <input class="form-check-input ms-0 me-3" type="checkbox" name="status" value="1" id="edit_status"
                        style="cursor: pointer;">
                    <label class="form-check-label fw-bold text-muted" for="edit_status" style="cursor: pointer;">Status
                        Rekening Aktif</label>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        if ($.fn.DataTable) {
            $('#datatable-rekening').DataTable({
                "language": { "sSearch": "", "searchPlaceholder": "Cari rekening...", "oPaginate": { "sNext": "<i class='fas fa-chevron-right'></i>", "sPrevious": "<i class='fas fa-chevron-left'></i>" } },
                "pageLength": 10,
                "dom": "t<'row px-3 py-3'<'col-md-6 text-muted small'i><'col-md-6 d-flex justify-content-end'p>>",
                "drawCallback": function () {
                    $('.pagination .page-item .page-link').addClass('rounded-circle mx-1 fw-bold border-0 shadow-sm');
                    $('.pagination .page-item.active .page-link').addClass('bg-dark border-dark');
                }
            });
            // Custom search input
            $('.dataTables_filter input').addClass('form-control rounded-pill border-0 bg-light px-3');
        }

        // Add Rekening
        $('#formTambahRekening').on('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            $.ajax({
                url: '<?= base_url('pengaturan/aksi_tambah_rekening') ?>',
                type: 'POST',
                data: formData,
                processData: false, contentType: false,
                success: function (res) {
                    if (res.status === 'success') {
                        Swal.fire({ title: 'Berhasil!', text: res.pesan, icon: 'success', timer: 1500, showConfirmButton: false }).then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', res.pesan, 'error');
                    }
                }
            });
        });

        // Edit Button
        $(document).on('click', '.btn-edit-rekening', function () {
            $('#edit_rekening_id').val($(this).data('id'));
            $('#edit_no_rekening').val($(this).data('nomor'));
            $('#edit_nama_bank').val($(this).data('bank'));
            $('#edit_atas_nama').val($(this).data('atas'));
            $('#edit_status').prop('checked', $(this).data('status') == 1);
            $('#modalEditRekening').modal('show');
        });

        // Update Rekening
        $('#formEditRekening').on('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            $.ajax({
                url: '<?= base_url('pengaturan/aksi_update_rekening') ?>',
                type: 'POST',
                data: formData,
                processData: false, contentType: false,
                success: function (res) {
                    if (res.status === 'success') {
                        Swal.fire({ title: 'Berhasil!', text: res.pesan, icon: 'success', timer: 1500, showConfirmButton: false }).then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', res.pesan, 'error');
                    }
                }
            });
        });

        // Delete Button
        $(document).on('click', '.btn-hapus-rekening', function () {
            const id = $(this).data('id');
            const nomor = $(this).data('nomor');
            Swal.fire({
                title: 'Hapus Rekening?',
                text: "Data rekening " + nomor + " akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff0055',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'YA, HAPUS',
                cancelButtonText: 'BATAL',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('pengaturan/actionDeleteRekening/') ?>' + id,
                        type: 'GET',
                        success: function (res) {
                            if (res.status === 'success') {
                                Swal.fire({ title: 'Terhapus!', text: res.pesan, icon: 'success', timer: 1500, showConfirmButton: false }).then(() => location.reload());
                            } else {
                                Swal.fire('Error', res.pesan, 'error');
                            }
                        }
                    });
                }
            });
        });

        // Auto-hide alert after 5 seconds
        setTimeout(function () {
            $('#alert-container .alert').fadeOut('slow');
        }, 5000);
    });

    function exportToExcel() {
        let table = document.getElementById("datatable-rekening");
        let html = table.outerHTML;
        let url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
        let link = document.createElement("a");
        link.download = "Data_Rekening_" + new Date().getTime() + ".xls";
        link.href = url;
        link.click();
    }

    function printTable() {
        window.print();
    }
</script>