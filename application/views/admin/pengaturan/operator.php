<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    /* ==========================================================================
    PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - OPERATOR & MACHINE MANAGEMENT
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

    .machine-badge {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 4px 10px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin: 2px;
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

    .btn-edit-p { background: #e0f2fe; color: #0369a1; }
    .btn-delete-p { background: #fee2e2; color: #b91c1c; }
    .btn-edit-p:hover { background: #bae6fd; transform: translateY(-2px); }
    .btn-delete-p:hover { background: #fecaca; transform: translateY(-2px); }

    /* Machine Selection List */
    .machine-selector-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 10px;
        max-height: 250px;
        overflow-y: auto;
        padding: 15px;
        background: #f8fafc;
        border-radius: 12px;
        border: 2px solid #f1f5f9;
    }

    .machine-checkbox-item {
        display: flex;
        align-items: center;
        gap: 10px;
        background: white;
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.2s;
    }

    .machine-checkbox-item:hover {
        border-color: #3b82f6;
        background: #eff6ff;
    }

    .machine-checkbox-item input {
        cursor: pointer;
    }

    .machine-checkbox-item .machine-info {
        display: flex;
        flex-direction: column;
    }

    .machine-checkbox-item .machine-name {
        font-weight: 700;
        font-size: 0.8rem;
        color: #1a1c23;
    }

    /* Animation */
    @keyframes slideUpFade {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-up { animation: slideUpFade 0.5s ease-out forwards; }
</style>

<div class="app-content mt-2">
    <div class="container-fluid">
        <!-- Quick Stats -->
        <div class="row g-3 mb-4 animate-up">
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm h-100">
                    <div class="stat-v2-icon text-white" style="background: var(--info-gradient)"><i class="fas fa-microchip"></i></div>
                    <div>
                        <div class="stat-v2-label">Total Workcenter</div>
                        <div class="stat-v2-value"><?= number_format(count($tampilData)) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm h-100">
                    <div class="stat-v2-icon text-white" style="background: var(--success-gradient)"><i class="fas fa-print"></i></div>
                    <div>
                        <div class="stat-v2-label">Total Mesin Aktif</div>
                        <div class="stat-v2-value"><?= number_format(count($all_machines)) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="summary-stat-card shadow-sm h-100 d-flex flex-row align-items-center justify-content-between p-4">
                    <div>
                        <h6 class="fw-900 text-dark mb-1">Manajemen Operator & Mesin</h6>
                        <p class="text-muted small mb-0">Hubungkan setiap stasiun dengan mesin yang dikelolanya.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary rounded-pill px-4 fw-800" onclick="openModal()" style="background: var(--primary-gradient); border: none;">
                            <i class="fas fa-plus me-2"></i> Tambah Operator
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="report-section-card animate-up" style="animation-delay: 0.1s">
            <div class="section-header">
                <h5><i class="fas fa-tools"></i> Daftar Hubungan Operator & Mesin</h5>
                <span class="badge bg-white text-dark rounded-pill px-3 fw-bold shadow-sm">Real-time Sync</span>
            </div>

            <div class="table-responsive">
                <table class="table premium-table table-hover mb-0" id="datatable-operator">
                    <thead>
                        <tr>
                            <th width="40" class="text-center">No</th>
                            <th>Nama Operator / Stasiun</th>
                            <th>Mesin yang Ditangani</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($tampilData as $dt): ?>
                        <tr>
                            <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                            <td>
                                <div class="fw-800 text-dark"><?= htmlspecialchars($dt->operator_nama) ?></div>
                                <div class="small text-muted font-monospace" style="font-size: 0.7rem;">ID: #<?= $dt->operator_id ?></div>
                            </td>
                            <td>
                                <?php if($dt->handled_machines): ?>
                                    <?php $machines = explode(', ', $dt->handled_machines); foreach($machines as $m): ?>
                                        <span class="machine-badge"><i class="fas fa-hdd text-info"></i> <?= htmlspecialchars($m) ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted small fst-italic">Belum ada mesin yang ditautkan</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center">
                                    <button class="btn-action-premium btn-edit-p" 
                                            data-id="<?= $dt->operator_id ?>"
                                            data-nama="<?= htmlspecialchars($dt->operator_nama, ENT_QUOTES) ?>"
                                            data-ket="<?= htmlspecialchars($dt->operator_keterangan, ENT_QUOTES) ?>"
                                            data-machines="<?= $dt->handled_machine_ids ?>"
                                            onclick="openModal({operator_id: this.getAttribute('data-id'), operator_nama: this.getAttribute('data-nama'), operator_keterangan: this.getAttribute('data-ket'), machine_ids: this.getAttribute('data-machines')})"
                                            title="Edit & Tautkan Mesin">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    <button class="btn-action-premium btn-delete-p" 
                                            onclick="hapusOperator('<?= $dt->operator_id ?>', '<?= htmlspecialchars($dt->operator_nama, ENT_QUOTES) ?>')"
                                            title="Hapus">
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
<div class="modal fade" id="modalOperator" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="formOperator" class="modal-content border-0 rounded-4 shadow-lg" autocomplete="off">
            <div class="modal-header border-0 bg-dark text-white p-4">
                <h5 class="modal-title fw-900" id="modalTitle"><i class="fas fa-plus-circle me-2"></i>Tambah Operator</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="operator_id" id="operator_id">
                
                <div class="row g-4">
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Nama Operator / Stasiun</label>
                            <input type="text" name="operator_nama" id="operator_nama" class="form-control" style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;" placeholder="Contoh: Operator A3" required>
                        </div>
                        <div class="mb-0">
                            <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Catatan Internal</label>
                            <textarea name="operator_keterangan" id="operator_keterangan" class="form-control" style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;" rows="5" placeholder="Catatan khusus tentang stasiun kerja ini..."></textarea>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Pilih Mesin yang Ditangani</label>
                        <p class="text-muted x-small mb-3">Centang mesin yang dikelola oleh stasiun kerja ini. Klik simpan untuk sinkronisasi.</p>
                        <div class="machine-selector-grid">
                            <?php foreach ($all_machines as $machine): ?>
                            <label class="machine-checkbox-item">
                                <input type="checkbox" name="mesin_ids[]" value="<?= $machine->mesin_id ?>" class="machine-check">
                                <div class="machine-info">
                                    <span class="machine-name"><?= htmlspecialchars($machine->mesin_nama) ?></span>
                                    <span class="text-muted" style="font-size: 0.65rem;"><?= $machine->kategori ?></span>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow" style="background: var(--primary-gradient)">
                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let modalOp;
    function openModal(data = null) {
        if (!modalOp) modalOp = new bootstrap.Modal(document.getElementById('modalOperator'));
        const title = document.getElementById('modalTitle');
        const machineChecks = document.querySelectorAll('.machine-check');
        
        // Reset checks
        machineChecks.forEach(c => c.checked = false);

        if (data) {
            title.innerHTML = '<i class="fas fa-edit me-2"></i>Edit Operator & Mesin';
            document.getElementById('operator_id').value = data.operator_id;
            document.getElementById('operator_nama').value = data.operator_nama;
            document.getElementById('operator_keterangan').value = data.operator_keterangan;
            
            // Check handled machines
            if (data.machine_ids) {
                const idArray = data.machine_ids.split(',');
                machineChecks.forEach(c => {
                    if (idArray.includes(c.value)) c.checked = true;
                });
            }
        } else {
            title.innerHTML = '<i class="fas fa-plus-circle me-2"></i>Tambah Operator Baru';
            document.getElementById('formOperator').reset();
            document.getElementById('operator_id').value = '';
        }
        modalOp.show();
    }

    document.getElementById('formOperator').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('operator_id').value;
        const url = id ? '<?= base_url("pengaturan/aksi_update_operator") ?>' : '<?= base_url("pengaturan/aksi_tambah_operator") ?>';
        
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
        
        fetch(url, { method: 'POST', body: new FormData(this) })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                Swal.fire({ 
                    icon: 'success', 
                    title: 'Sinkronisasi Berhasil', 
                    text: res.pesan, 
                    timer: 1500, 
                    showConfirmButton: false 
                }).then(() => location.reload());
            } else {
                Swal.fire('Gagal', res.pesan, 'error');
            }
        })
        .catch(err => Swal.fire('Error', 'Kesalahan koneksi server', 'error'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });

    function hapusOperator(id, nama) {
        Swal.fire({
            title: 'Hapus Operator?',
            text: `Hapus stasiun "${nama}"? Hubungan dengan mesin akan dilepas namun mesin tidak akan terhapus.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e63946',
            confirmButtonText: 'Ya, Lepas & Hapus'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('<?= base_url("pengaturan/aksi_hapus_operator/") ?>' + id, { method: 'POST' })
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
</script>