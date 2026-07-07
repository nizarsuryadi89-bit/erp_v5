<div class="app-content mt-2">
    <div class="container-fluid">
        <!-- HEADER SECTION REPLACED WITH STANDARD MODULE HEADER -->
        <?php $this->load->view('layout_admin/header_modul'); ?>
<?php $this->load->view('admin/pengaturan/_settings_nav'); ?>

        <!-- Flash Messages -->
        <div id="alert-container">
            <?= $this->session->flashdata('message') ?? $this->session->flashdata('success') ?? $this->session->flashdata('error'); ?>
        </div>

        <!-- STATISTICS SECTION -->
        <div class="row g-3 mb-4 animate-up">
            <div class="col-12 col-md-4">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--info-gradient)">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <div class="stat-v2-label">Total E-Wallet</div>
                        <div class="stat-v2-value">
                            <?= number_format($total_rows) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN TABLE CARD -->
        <div class="report-section-card animate-up" style="animation-delay: 0.1s">
            <div class="section-header">
                <h5><i class="fas fa-mobile-alt me-2"></i> Daftar E-Wallet</h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-white text-dark rounded-pill px-3 fw-bold shadow-sm"
                        data-bs-toggle="modal" data-bs-target="#modalAddEwallet">
                        <i class="fas fa-plus text-success me-1"></i> Tambah E-Wallet
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table premium-table table-hover mb-0" id="ewalletTable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Nama E-Wallet</th>
                            <th>Keterangan</th>
                            <th class="text-center" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        foreach ($tampilData as $dt):
                            ?>
                            <tr>
                                <td class="text-center fw-bold text-muted">
                                    <?= $no++ ?>
                                </td>
                                <td>
                                    <div class="fw-800 text-dark">
                                        <?= strtoupper($dt->ewallet_nama) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-muted small fw-bold text-break" style="font-size: 0.85rem;">
                                        <?= $dt->wallet_ket ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <button type="button" class="btn-action-premium btn-edit-p" title="Ubah Data"
                                            onclick="openEditModal('<?= $dt->id ?>', '<?= addslashes($dt->ewallet_nama) ?>', '<?= addslashes($dt->wallet_ket) ?>')">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                        <button type="button"
                                            onclick="confirmDelete('<?= base_url('pengaturan/ewallet_delete/' . $dt->id) ?>')"
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
                    <i class="fas fa-wallet fs-1 text-muted opacity-25 mb-3"></i>
                    <h6 class="fw-800 text-muted">Belum ada data E-Wallet yang tersedia.</h6>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MODAL ADD EWALLET -->
<div class="modal fade" id="modalAddEwallet" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-dark text-white p-4 border-0">
                <h5 class="modal-title fw-900 text-uppercase"><i class="fas fa-plus-circle me-2"></i> Tambah E-Wallet
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="<?= base_url('pengaturan/ewallet_simpan') ?>" method="post">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Nama E-Wallet</label>
                        <input name="ewallet_nama" type="text" class="form-control"
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                            placeholder="Misal: GoPay..." required>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Keterangan</label>
                        <textarea name="wallet_ket" class="form-control" rows="2"
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                            placeholder="Detail / ID E-Wallet..."></textarea>
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

<!-- MODAL EDIT EWALLET -->
<div class="modal fade" id="modalEditEwallet" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-dark text-white p-4 border-0">
                <h5 class="modal-title fw-900 text-uppercase"><i class="fas fa-edit me-2"></i> Update Data E-Wallet
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="<?= base_url('pengaturan/ewallet_update') ?>" method="post">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="fw-bold text-muted small text-uppercase mb-0">Nama E-Wallet</label>
                            <span class="badge bg-light text-dark border">ID: <span id="display_id"></span></span>
                        </div>
                        <input name="ewallet_nama" id="edit_ewallet_nama" type="text"
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                            class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Keterangan</label>
                        <textarea name="wallet_ket" id="edit_wallet_ket" class="form-control" rows="2"
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
    function openEditModal(id, nama, keterangan) {
        $('#edit_id').val(id);
        $('#display_id').text(id);
        $('#edit_ewallet_nama').val(nama);
        $('#edit_wallet_ket').val(keterangan);

        var myModal = new bootstrap.Modal(document.getElementById('modalEditEwallet'));
        myModal.show();
    }

    function confirmDelete(url) {
        Swal.fire({
            title: 'Hapus E-Wallet?',
            text: "Data E-Wallet yang dihapus tidak dapat dipulihkan!",
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
            $('#ewalletTable').DataTable({
                "pageLength": 10,
                "language": { "sSearch": "Cari:", "oPaginate": { "sNext": "<i class='fas fa-chevron-right'></i>", "sPrevious": "<i class='fas fa-chevron-left'></i>" } },
                "dom": "<'row px-4 py-3'<'col-md-6'l><'col-md-6 d-flex justify-content-end text-end'f>>t<'row px-4 py-3'<'col-md-6'i><'col-md-6 d-flex justify-content-end text-end'p>>"
            });
        }

        // Auto-hide alert after 5 seconds
        setTimeout(function () {
            $('#alert-container .alert').fadeOut('slow');
        }, 5000);
    });
</script>