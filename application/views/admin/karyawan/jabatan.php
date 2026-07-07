<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    /* ==========================================================================
    PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - STRUCTURE MANAGEMENT
    ========================================================================== */
    :root {
        --primary-gradient: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        --success-gradient: linear-gradient(135deg, #059669 0%, #10b981 100%);
        --info-gradient: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    /* Summary Stat Cards */
    .summary-stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
    }

    .summary-stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--premium-shadow);
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
        font-size: 0.85rem;
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
        background: #fff3cd;
        color: #856404;
    }

    .btn-delete-p {
        background: #fee2e2;
        color: #b91c1c;
    }

    .btn-edit-p:hover {
        background: #ffe69c;
        transform: translateY(-2px);
    }

    .btn-delete-p:hover {
        background: #fecaca;
        transform: translateY(-2px);
    }

    /* Animations */
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
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--primary-gradient)"><i
                            class="fas fa-id-card-clip"></i></div>
                    <div>
                        <div class="stat-v2-label">Struktur Jabatan</div>
                        <div class="stat-v2-value"><?= number_format(is_array($tampilData) ? count($tampilData) : 0) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--success-gradient)"><i
                            class="fas fa-coins"></i></div>
                    <div>
                        <div class="stat-v2-label">Avg Tunjangan</div>
                        <div class="stat-v2-value" style="font-size: 1.2rem;">IDR
                            <?= number_format((is_array($tampilData) && !empty($tampilData)) ? array_sum(array_column($tampilData, 'tunjangan_jabatan')) / count($tampilData) : 0, 0, ',', '.') ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-end d-flex align-items-center justify-content-end gap-2">
                <button class="btn btn-primary rounded-pill px-4 fw-800 shadow"
                    style="background: var(--primary-gradient); border: none;" onclick="openModal()">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Jabatan
                </button>
                <button class="btn btn-light border rounded-pill px-3 fw-bold shadow-sm" onclick="exportToExcel()">
                    <i class="fas fa-file-excel text-success me-1"></i> Excel
                </button>
                <button class="btn btn-light border rounded-pill px-3 fw-bold shadow-sm" onclick="printTable()">
                    <i class="fas fa-print me-1"></i> Print
                </button>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="report-section-card animate-up" style="animation-delay: 0.1s">
            <div class="section-header">
                <h5><i class="fas fa-sitemap"></i> Matriks Jabatan & Tunjangan</h5>
                <span class="badge bg-white text-dark rounded-pill px-3 fw-bold">Human Capital Matrix</span>
            </div>

            <div class="table-responsive">
                <table class="table premium-table table-hover mb-0" id="datatable-details">
                    <thead>
                        <tr>
                            <th width="50" class="text-center">No</th>
                            <th>Jabatan / Otoritas</th>
                            <th>Finansial & Tunjangan</th>
                            <th>Keterangan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        $tampilData = is_array($tampilData) ? $tampilData : [];
                        foreach ($tampilData as $dt): ?>
                            <tr>
                                <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                                <td>
                                    <div class="fw-800 text-dark"><?= $dt->nama_jabatan ?></div>
                                    <div class="small text-muted text-uppercase fw-bold"
                                        style="font-size: 0.6rem; letter-spacing: 1px;">CODE: JBT-<?= $dt->jabatan_id ?>
                                    </div>
                                </td>
                                <td>
                                    <?php $total_tunj = ($dt->tunjangan_jabatan ?? 0) + ($dt->tunjangan_transportasi ?? 0); ?>
                                    <div class="fw-900 text-primary">Rp <?= number_format($total_tunj, 0, ',', '.') ?></div>
                                    <div class="small text-muted" style="font-size: 0.7rem;">Allowance Per Month</div>
                                </td>
                                <td>
                                    <span
                                        class="text-muted small fw-bold"><?= $dt->keterangan ? htmlspecialchars($dt->keterangan) : '-' ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <button class="btn-action-premium btn-edit-p"
                                            onclick='openModal(<?= json_encode($dt, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'
                                            title="Edit Node">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-action-premium btn-delete-p"
                                            onclick="hapusJabatan('<?= $dt->jabatan_id ?>', '<?= htmlspecialchars($dt->nama_jabatan, ENT_QUOTES) ?>')"
                                            title="Delete Node">
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

<!-- Unified Modal -->
<div class="modal fade" id="modalJabatan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formJabatan" class="modal-content border-0 rounded-4 shadow-lg" autocomplete="off">
            <div class="modal-header border-0 bg-dark text-white p-4">
                <h5 class="modal-title fw-900" id="modalTitle"><i class="fas fa-briefcase me-2"></i>Entity Configuration
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="jabatan_id" id="jabatan_id">
                <input type="hidden" name="gaji_pokok" id="gaji_pokok">
                <input type="hidden" name="tunjangan_transportasi" id="tunjangan_transportasi">

                <div class="mb-3">
                    <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Label Jabatan <span
                            class="text-danger">*</span></label>
                    <input type="text" name="nama_jabatan" id="nama_jabatan" class="form-control"
                        style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                        placeholder="Contoh: Manager Operasional" required>
                </div>

                <div class="mb-3">
                    <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Tunjangan Jabatan
                        (Nominal)</label>
                    <div class="input-group" style="border: 2px solid #f1f5f9; border-radius: 12px; overflow: hidden;">
                        <span class="input-group-text border-0 bg-light fw-bold text-muted">Rp</span>
                        <input type="text" name="tunjangan_jabatan" id="tunjangan_jabatan" class="form-control border-0"
                            style="padding: 0.75rem; font-weight: 800;" placeholder="0" onkeyup="formatCurrency(this)">
                    </div>
                </div>

                <div class="mb-0">
                    <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Brief Description</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3"
                        style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                        placeholder="Detail tanggung jawab..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                    data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow"
                    style="background: var(--primary-gradient)">Simpan Konfigurasi</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function formatCurrency(input) {
        let value = input.value.replace(/\D/g, '');
        input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    let modalCtrl;

    function openModal(data = null) {
        if (!modalCtrl) modalCtrl = new bootstrap.Modal(document.getElementById('modalJabatan'));
        const title = document.getElementById('modalTitle');
        const form = document.getElementById('formJabatan');

        if (data) {
            title.innerHTML = '<i class="fas fa-edit me-2"></i>Update Jabatan';
            document.getElementById('jabatan_id').value = data.jabatan_id;
            document.getElementById('nama_jabatan').value = data.nama_jabatan;
            document.getElementById('gaji_pokok').value = data.gaji_pokok || 0;
            document.getElementById('tunjangan_transportasi').value = data.tunjangan_transportasi || 0;
            document.getElementById('tunjangan_jabatan').value = parseFloat(data.tunjangan_jabatan || 0).toLocaleString('id-ID');
            document.getElementById('deskripsi').value = data.keterangan || '';
        } else {
            title.innerHTML = '<i class="fas fa-plus-circle me-2"></i>Jabatan Baru';
            form.reset();
            document.getElementById('jabatan_id').value = '';
            document.getElementById('gaji_pokok').value = 0;
            document.getElementById('tunjangan_transportasi').value = 0;
        }
        modalCtrl.show();
    }

    document.getElementById('formJabatan').addEventListener('submit', function (e) {
        e.preventDefault();
        const id = document.getElementById('jabatan_id').value;
        const url = id ? '<?= base_url('pengaturan/aksi_update_jabatan') ?>' : '<?= base_url('pengaturan/aksi_tambah_jabatan') ?>';
        const formData = new FormData(this);
        const btn = this.querySelector('button[type="submit"]');

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

        fetch(url, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.pesan, timer: 1500, showConfirmButton: false }).then(() => location.reload());
                } else {
                    Swal.fire('Gagal', res.pesan, 'error');
                }
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = 'Simpan Konfigurasi';
            });
    });

    function hapusJabatan(id, nama) {
        Swal.fire({
            title: 'Hapus Jabatan?',
            text: `Anda yakin ingin menghapus "${nama}"? Data akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f72585',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('<?= base_url('pengaturan/actionDelete/') ?>' + id, { method: 'POST' })
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
        let table = document.getElementById("datatable-details");
        let html = table.outerHTML;
        let url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
        let link = document.createElement("a");
        link.download = "Data_Jabatan_" + new Date().getTime() + ".xls";
        link.href = url;
        link.click();
    }

    function printTable() { window.print(); }
</script>