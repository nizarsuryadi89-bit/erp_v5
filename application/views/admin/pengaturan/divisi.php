<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    /* ==========================================================================
    PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - DIVISION MANAGEMENT
    ========================================================================== */
    :root {
        --primary-gradient: linear-gradient(135deg, #1a1c23 0%, #2c3e50 100%);
        --success-gradient: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
        --info-gradient: linear-gradient(135deg, #4895ef 0%, #4cc9f0 100%);
        --warning-gradient: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    /* Stats Cards */
    .summary-stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .stat-v2-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .stat-v2-label {
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.6rem;
        letter-spacing: 1px;
    }

    .stat-v2-value {
        font-size: 1.5rem;
        font-weight: 900;
        color: #1a1c23;
        line-height: 1;
    }

    /* Tables */
    .report-section-card {
        background: white;
        border-radius: 24px;
        border: none;
        box-shadow: var(--premium-shadow);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .section-header {
        background: var(--primary-gradient);
        padding: 1.25rem 1.75rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
    }

    .section-header h5 {
        margin: 0;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .premium-table thead th {
        background: #f8fafc;
        border: none;
        color: #475569;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.65rem;
        letter-spacing: 1px;
        padding: 1.25rem 1.5rem;
    }

    .premium-table tbody td {
        padding: 1.25rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }

    /* Action Buttons */
    .btn-action-premium {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        border: none;
        margin: 0 2px;
    }

    .btn-edit-p {
        background: #e0f2fe;
        color: #0369a1;
    }

    .btn-delete-p {
        background: #fee2e2;
        color: #b91c1c;
    }

    .btn-edit-p:hover {
        background: #bae6fd;
        transform: translateY(-2px);
    }

    .btn-delete-p:hover {
        background: #fecaca;
        transform: translateY(-2px);
    }

    /* Animation */
    @keyframes slideUpFade {
        from {
            opacity: 0;
            transform: translateY(15px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-up {
        animation: slideUpFade 0.5s ease-out forwards;
    }
</style>

<div class="app-content mt-2">
    <div class="container-fluid">
        <!-- Quick Stats -->
        <div class="row g-3 mb-4 animate-up">
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm h-100">
                    <div class="stat-v2-icon text-white" style="background: var(--primary-gradient)"><i
                            class="fas fa-sitemap"></i></div>
                    <div>
                        <div class="stat-v2-label">Total Divisi</div>
                        <div class="stat-v2-value"><?= number_format(count($tampilData)) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm h-100">
                    <div class="stat-v2-icon text-white" style="background: var(--success-gradient)"><i
                            class="fas fa-users"></i></div>
                    <div>
                        <div class="stat-v2-label">Total Karyawan</div>
                        <div class="stat-v2-value">
                            <?= number_format(array_sum(array_column($tampilData, 'total_karyawan'))) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div
                    class="summary-stat-card shadow-sm h-100 d-flex flex-row align-items-center justify-content-between p-4">
                    <div>
                        <h6 class="fw-900 text-dark mb-1">Aksi Cepat Divisi</h6>
                        <p class="text-muted small mb-0">Kelola dan update struktur organisasi</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary rounded-pill px-4 fw-800" onclick="openModal()"
                            style="background: var(--primary-gradient); border: none;">
                            <i class="fas fa-plus me-2"></i> Tambah
                        </button>
                        <button class="btn btn-light border rounded-pill px-3 fw-800" onclick="exportToExcel()">
                            <i class="fas fa-file-excel text-success me-1"></i> Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="report-section-card animate-up" style="animation-delay: 0.1s">
            <div class="section-header">
                <h5><i class="fas fa-folder-tree"></i> Daftar Hak Akses & Divisi</h5>
                <span class="badge bg-white text-dark rounded-pill px-3 fw-bold shadow-sm">Real-time Data</span>
            </div>

            <div class="table-responsive">
                <table class="table premium-table table-hover mb-0" id="datatable-divisi">
                    <thead>
                        <tr>
                            <th width="40" class="text-center">No</th>
                            <th>Nama Divisi / Role</th>
                            <th class="text-center">Access Level</th>
                            <th class="text-center">Total Karyawan</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($tampilData as $dt): ?>
                            <tr>
                                <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                                <td>
                                    <div class="fw-800 text-dark"><?= htmlspecialchars($dt->divisi_nama) ?></div>
                                    <div class="small text-muted font-monospace" style="font-size: 0.7rem;">ID ROLE:
                                        #<?= $dt->divisi_id ?></div>
                                </td>
                                <td class="text-center">
                                    <?php if ($dt->is_super_admin == 1): ?>
                                        <span
                                            class="badge bg-warning text-dark border border-warning rounded-pill px-3 fw-bold">
                                            <i class="fas fa-crown me-1"></i> SUPER ADMIN
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border rounded-pill px-3 fw-bold">
                                            STANDARD
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="badge bg-light text-primary border rounded-pill px-3 fw-800">
                                        <?= $dt->total_karyawan ?> Karyawan
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <button class="btn-action-premium btn-edit-p" data-id="<?= $dt->divisi_id ?>"
                                            data-nama="<?= htmlspecialchars($dt->divisi_nama, ENT_QUOTES) ?>"
                                            data-superadmin="<?= $dt->is_super_admin ?>"
                                            onclick="openModal({divisi_id: this.getAttribute('data-id'), divisi_nama: this.getAttribute('data-nama'), is_super_admin: this.getAttribute('data-superadmin')})"
                                            title="Edit Divisi">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-action-premium btn-delete-p"
                                            onclick="hapusDivisi('<?= $dt->divisi_id ?>', '<?= htmlspecialchars($dt->divisi_nama, ENT_QUOTES) ?>')"
                                            title="Hapus Divisi">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
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

<!-- Add/Edit Modal -->
<div class="modal fade" id="modalDivisi" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formDivisi" class="modal-content border-0 rounded-4 shadow-lg" autocomplete="off">
            <div class="modal-header border-0 bg-dark text-white p-4">
                <h5 class="modal-title fw-900" id="modalTitle"><i class="fas fa-plus-circle me-2"></i>Tambah Divisi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="divisi_id" id="divisi_id">
                <div class="mb-2">
                    <label class="small text-uppercase fw-bold text-muted mb-2 d-block"
                        style="letter-spacing: 1px;">Nama Divisi / Role</label>
                    <input type="text" name="nama_divisi" id="nama_divisi" class="form-control"
                        style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600; background: #f8fafc;"
                        placeholder="Contoh: Produksi, Keuangan, dsb" required>
                    <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle me-1"></i> Nama ini akan muncul
                        sebagai Hak Akses sistem.</small>
                </div>
                <div class="mb-2">
                    <div class="form-check form-switch p-3 bg-light rounded-3 border">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_super_admin"
                            name="is_super_admin" value="1"
                            style="transform: scale(1.3); margin-left: -10px; margin-right: 10px;">
                        <label class="form-check-label fw-bold" for="is_super_admin">
                            Super Admin Access (Full Control)
                        </label>
                        <small class="d-block text-muted mt-1">Jika aktif, role ini dapat mengakses SEMUA menu tanpa
                            batasan.</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                    data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow"
                    style="background: var(--primary-gradient)">
                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let modalDivisi;

    function openModal(data = null) {
        if (!modalDivisi) {
            modalDivisi = new bootstrap.Modal(document.getElementById('modalDivisi'));
        }

        const title = document.getElementById('modalTitle');
        const form = document.getElementById('formDivisi');

        if (data) {
            title.innerHTML = '<i class="fas fa-edit me-2"></i>Edit Divisi';
            document.getElementById('divisi_id').value = data.divisi_id;
            document.getElementById('nama_divisi').value = data.divisi_nama;
            document.getElementById('is_super_admin').checked = (data.is_super_admin == 1);
        } else {
            title.innerHTML = '<i class="fas fa-plus-circle me-2"></i>Tambah Divisi Baru';
            form.reset();
            document.getElementById('divisi_id').value = '';
            document.getElementById('is_super_admin').checked = false;
        }
        modalDivisi.show();
    }

    document.getElementById('formDivisi').addEventListener('submit', function (e) {
        e.preventDefault();
        const id = document.getElementById('divisi_id').value;
        const url = id ? '<?= base_url('pengaturan/aksi_update_divisi') ?>' : '<?= base_url('pengaturan/aksi_tambah_divisi') ?>';

        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';

        fetch(url, { method: 'POST', body: new FormData(this) })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.pesan, timer: 1500, showConfirmButton: false })
                        .then(() => location.reload());
                } else {
                    Swal.fire('Gagal', res.pesan, 'error');
                }
            })
            .catch(err => Swal.fire('Error', 'Terjadi kesalahan sistem', 'error'))
            .finally(() => { btn.disabled = false; btn.innerHTML = originalText; });
    });

    function hapusDivisi(id, nama) {
        Swal.fire({
            title: 'Hapus Divisi?',
            text: `Anda yakin ingin menghapus divisi "${nama}"? Semua hak akses terkait akan terpengaruh.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f72585',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('<?= base_url('pengaturan/actionDeleteDivisi/') ?>' + id, { method: 'POST' })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success') {
                            Swal.fire('Terhapus!', res.pesan, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Gagal', res.pesan, 'error');
                        }
                    });
            }
        });
    }

    function exportToExcel() {
        let table = document.getElementById("datatable-divisi");
        let html = table.outerHTML;
        let a = document.createElement('a');
        a.href = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
        a.download = 'Data_Divisi_' + new Date().toISOString().slice(0, 10) + '.xls';
        a.click();
    }
</script>