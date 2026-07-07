<?php $this->load->view('layout_admin/header_modul'); ?>

<div class="app-content mt-4">
    <div class="report-section-card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="section-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-1 text-dark fw-bold">
                    <i class="fas fa-users-cog me-2 text-primary"></i> Daftar Operator Bersama
                </h4>
                <p class="mb-0 text-muted small">Kelola PIN produksi untuk akun stasiun/shared.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= site_url('Workcenter') ?>" class="btn btn-outline-primary rounded-pill px-4 shadow-sm fw-bold">
                    <i class="fas fa-list-ul me-1"></i> Kelola Workcenter
                </a>
                <button class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" onclick="showAddModal()">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Akun Stasiun
                </button>
            </div>
        </div>

        <div class="p-3 bg-white border-bottom">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="fw-bold text-muted small">Total: <?= count($operators) ?> Akun</div>
                    
                    <form id="filterForm" method="GET" action="" class="d-flex gap-2">
                        <select name="cabang" class="form-select form-select-sm border-0 bg-light fw-bold shadow-none" style="min-width: 150px;" onchange="document.getElementById('filterForm').submit();">
                            <option value="">Semua Cabang</option>
                            <?php foreach ($companies as $c): ?>
                                <option value="<?= $c->id_perusahaan ?>" <?= (isset($filter_cabang) && $filter_cabang == $c->id_perusahaan) ? 'selected' : '' ?>>
                                    <?= $c->nama_perusahaan ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <select name="operator" class="form-select form-select-sm border-0 bg-light fw-bold shadow-none" style="min-width: 180px;" onchange="document.getElementById('filterForm').submit();">
                            <option value="">Semua Workcenter</option>
                            <option value="none" <?= (isset($filter_operator) && $filter_operator == 'none') ? 'selected' : '' ?>>-- TANPA MAPPING --</option>
                            <?php foreach ($list_operator as $lop): ?>
                                <option value="<?= $lop->operator_id ?>" <?= (isset($filter_operator) && $filter_operator == $lop->operator_id) ? 'selected' : '' ?>>
                                    <?= strtoupper($lop->operator_nama) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
                
                <div class="input-group" style="max-width: 300px;">
                    <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="searchOperator" class="form-control bg-light border-0 shadow-none fw-bold" placeholder="Cari username, nama, atau NIK...">
                </div>
            </div>
        </div>
        <div class="p-0 bg-white">
            <div class="table-responsive">
                <table class="table premium-table table-hover align-middle mb-0" id="tableOperator">
                    <thead>
                        <tr class="bg-light">
                            <th class="text-center py-3" width="60"
                                style="font-size: 0.65rem; color: #64748b; font-weight: 900;">NO</th>
                            <th class="py-3" style="font-size: 0.65rem; color: #64748b; font-weight: 900;">USERNAME
                            </th>
                            <th class="py-3" style="font-size: 0.65rem; color: #64748b; font-weight: 900;">DIVISI & WORKCENTER
                            </th>
                            <th class="py-3" style="font-size: 0.65rem; color: #64748b; font-weight: 900;">IDENTITAS
                                DUMMY</th>
                            <th class="py-3" style="font-size: 0.65rem; color: #64748b; font-weight: 900;">LOKASI BEKERJA & PRODUKSI
                            </th>
                            <!-- <th class="text-center py-3" style="font-size: 0.65rem; color: #64748b; font-weight: 900;">
                                PIN PRODUKSI</th> -->
                            <th class="text-center py-3" width="200"
                                style="font-size: 0.65rem; color: #64748b; font-weight: 900;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($operators as $op): ?>
                            <tr>
                                <td class="text-center">
                                    <span class="text-muted fw-800 small"><?= $no++ ?></span>
                                </td>
                                <td>
                                    <span class="fw-900 text-dark fs-6"><?= $op->username ?></span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column align-items-start gap-1">
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill fw-bold text-start"
                                            style="font-size: 0.55rem;">
                                            <?= strtoupper($op->divisi_nama ?: 'OPERATOR') ?>
                                        </span>
                                        <?php if ($op->operator_nama): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill fw-bold text-start"
                                                style="font-size: 0.55rem;">
                                                <i class="fas fa-link me-1"></i> <?= strtoupper($op->operator_nama) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($op->mesin_nama_list)): ?>
                                            <?php foreach ($op->mesin_nama_list as $mn): ?>
                                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill fw-bold text-start"
                                                    style="font-size: 0.55rem;">
                                                    <i class="fas fa-print me-1"></i> <?= strtoupper($mn) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle-sm bg-light text-primary fw-bold">
                                            <?= substr($op->nama_lengkap ?? 'O', 0, 1) ?>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="text-dark fw-700 small"><?= $op->nama_lengkap ?></span>
                                            <span class="text-muted" style="font-size: 0.6rem;">NIK: <?= $op->nik ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill text-start" style="font-size: 0.55rem; width: fit-content;">
                                            <i class="fas fa-building me-1"></i> KERJA: <?= strtoupper($op->nama_perusahaan ?? 'TIDAK DIKETAHUI') ?>
                                        </span>
                                        <span class="badge bg-warning bg-opacity-25 text-dark border border-warning border-opacity-50 rounded-pill text-start" style="font-size: 0.55rem; width: fit-content;">
                                            <i class="fas fa-industry me-1"></i> PRODUKSI: <?= strtoupper($op->nama_perusahaan ?? 'TIDAK DIKETAHUI') ?>
                                        </span>
                                    </div>
                                </td>
                                <!-- <td class="text-center">
                                    <div
                                        class="d-inline-flex align-items-center gap-2 px-3 py-1 bg-primary bg-opacity-10 rounded-pill border border-primary border-opacity-25">
                                        <i class="fas fa-key text-primary" style="font-size: 0.7rem;"></i>
                                        <span class="fw-900 text-primary"
                                            style="font-size: 1rem; letter-spacing: 2px;"><?= $op->pin_produksi ?: '0000' ?></span>
                                    </div>
                                </td> -->
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="<?= site_url('Wip?operator=' . ($op->operator_id ?: 'none')) ?>" target="_blank"
                                            class="btn btn-primary btn-sm rounded-circle shadow-sm btn-icon" data-bs-toggle="tooltip" title="Masuk Halaman Workcenter">
                                            <i class="fas fa-external-link-alt text-white"></i>
                                        </a>
                                        <button class="btn btn-light btn-sm rounded-circle shadow-sm btn-icon" data-bs-toggle="tooltip" title="Ganti PIN"
                                            onclick="editPin('<?= $op->karyawan_id ?>', '<?= $op->username ?>')">
                                            <i class="fas fa-key text-warning"></i>
                                        </button>
                                        <button class="btn btn-light btn-sm rounded-circle shadow-sm btn-icon" data-bs-toggle="tooltip" title="Lihat Tugas (Routing)"
                                            onclick="showTasks('<?= htmlspecialchars($operator_tasks[$op->operator_id] ?? 'Tidak ada tugas yang terdaftar', ENT_QUOTES) ?>', '<?= htmlspecialchars($op->nama_lengkap, ENT_QUOTES) ?>')">
                                            <i class="fas fa-tasks text-success"></i>
                                        </button>
                                        <button class="btn btn-light btn-sm rounded-circle shadow-sm btn-icon" data-bs-toggle="tooltip" title="Reset Password"
                                            onclick="editPassword('<?= $op->user_id ?>', '<?= $op->username ?>')">
                                            <i class="fas fa-lock text-secondary"></i>
                                        </button>
                                        <button class="btn btn-light btn-sm rounded-circle shadow-sm btn-icon" data-bs-toggle="tooltip" title="Edit Data"
                                            onclick="editData('<?= $op->user_id ?>')">
                                            <i class="fas fa-pencil-alt text-primary"></i>
                                        </button>
                                        <a href="<?= site_url('OperatorBersama/delete_shared_account/' . $op->user_id) ?>"
                                            class="btn btn-light btn-sm rounded-circle shadow-sm btn-icon" data-bs-toggle="tooltip" title="Hapus Akun"
                                            onclick="return confirm('Hapus akun ini?')">
                                            <i class="fas fa-trash-alt text-danger"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($operators)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="opacity-25 mb-2"><i class="fas fa-users-slash fa-3x"></i></div>
                                    <h6 class="text-muted fw-bold">Belum ada akun operator bersama.</h6>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="section-footer bg-light p-3 border-top text-center">
            <span class="text-muted small fw-bold"><i class="fas fa-info-circle me-1"></i> Akun yang memiliki prefix
                <code class="text-primary">produksi_</code>, <code class="text-primary">op_</code>, <code
                    class="text-primary">fn_</code>, atau <code class="text-primary">pb_</code> otomatis terdaftar di
                sini.</span>
        </div>
    </div>
</div>



<!-- Modal Ganti Password Login -->
<div class="modal fade" id="modalEditPassword" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-dark p-4">
                <h5 class="modal-title fw-bold text-white"><i class="fas fa-shield-alt me-2"></i> Reset Password Login
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-4">
                    <p class="text-muted small mb-1 fw-bold">SEDANG RESET PASSWORD AKUN</p>
                    <div class="h3 fw-900 text-dark ls-n1" id="resetUserDisp"></div>
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label fw-bold text-muted small px-2">PASSWORD BARU</label>
                    <div class="input-group input-group-lg bg-light rounded-pill overflow-hidden p-1 shadow-sm">
                        <span class="input-group-text border-0 bg-transparent text-muted px-3"><i
                                class="fas fa-lock"></i></span>
                        <input type="password" id="newPassword" class="form-control border-0 bg-transparent fw-bold"
                            placeholder="Password Minimal 4 Karakter">
                    </div>
                </div>
                <input type="hidden" id="resetUserId">
                <p class="text-muted" style="font-size: 0.65rem;">Hati-hati: Tindakan ini akan memaksa user untuk login
                    kembali dengan password baru.</p>
            </div>
            <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batalkan</button>
                <button type="button" class="btn btn-dark rounded-pill px-5 shadow-sm fw-bold" onclick="savePassword()">
                    <i class="fas fa-key-skeleton me-1"></i> Terapkan Password Baru
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Data Lengkap -->
<div class="modal fade" id="modalEditOperator" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-primary p-4">
                <h5 class="modal-title fw-bold text-white"><i class="fas fa-user-edit me-2"></i> Update Data Operator
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <form id="formEditOperator">
                    <input type="hidden" name="user_id" id="val_user_id">
                    <input type="hidden" name="karyawan_id" id="val_karyawan_id">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">USERNAME (Fokus Kategori)</label>
                            <input type="text" name="username" id="val_username"
                                class="form-control fw-bold border-0 shadow-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">NIK IDENTITAS</label>
                            <input type="text" name="nik" id="val_nik" class="form-control fw-bold border-0 shadow-sm"
                                required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted">NAMA OPERATOR STASIUN</label>
                            <input type="text" name="nama_lengkap" id="val_nama_lengkap"
                                class="form-control fw-bold border-0 shadow-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">UNIT / CABANG</label>
                            <select name="perusahaan_id" id="val_perusahaan_id"
                                class="form-select border-0 shadow-sm fw-bold">
                                <?php foreach ($companies as $c): ?>
                                    <option value="<?= $c->id_perusahaan ?>"><?= $c->nama_perusahaan ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">DIVISI / LEVEL</label>
                            <select name="divisi_id" id="val_divisi_id" class="form-select border-0 shadow-sm fw-bold">
                                <?php foreach ($divisions as $d): ?>
                                    <?php if (in_array((int) $d->divisi_id, [3, 5, 7])): ?>
                                        <option value="<?= $d->divisi_id ?>"><?= $d->divisi_nama ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12 mt-3">
                            <div class="p-3 rounded-4 bg-white border border-dashed border-info">
                                <label class="form-label fw-bold small text-info"><i class="fas fa-link me-1"></i>
                                    WORKCENTER (EXPLICIT MAPPING)</label>
                                <select name="operator_id" id="val_operator_id"
                                    class="form-select border-0 shadow-sm fw-bold bg-light">
                                    <option value="">-- TANPA MAPPING (GUNAKAN USERNAME) --</option>
                                    <?php foreach ($list_operator as $lop): ?>
                                        <option value="<?= $lop->operator_id ?>"><?= strtoupper($lop->operator_nama) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="text-muted" style="font-size: 0.6rem; margin-top: 5px;">Memprioritaskan
                                    antrean kategori tertentu pada modul WIP Produksi.</div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 p-4 bg-white">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold"
                    onclick="saveOperator()">
                    <i class="fas fa-check-circle me-1"></i> Update Data
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Lihat Tugas -->
<div class="modal fade" id="modalLihatTugas" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-light p-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-tasks me-2 text-success"></i> Tugas Terdaftar (Routing)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">WORKCENTER</label>
                    <div class="h4 fw-900 text-dark" id="tugasOperatorName"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">DAFTAR ROUTING TUGAS</label>
                    <div id="tugasListContainer" class="mt-2 table-responsive">
                        <!-- Badges will be inserted here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                <button type="button" class="btn btn-light rounded-pill px-5 fw-bold shadow-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit PIN -->
<div class="modal fade" id="modalEditPin" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-light p-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-key me-2 text-warning"></i> Ganti PIN Produksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">SEDANG EDIT AKUN</label>
                    <div class="h4 fw-900 text-dark" id="editUsernameDisp"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">MASUKKAN PIN BARU (4 DIGIT)</label>
                    <input type="number" id="newPin"
                        class="form-control form-control-lg text-center fw-900 fs-1 border-0 bg-light" maxlength="4"
                        oninput="if(this.value.length > 4) this.value = this.value.slice(0, 4);" placeholder="0000">
                </div>
                <input type="hidden" id="editKaryawanId">
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold" onclick="savePin()">
                    <i class="fas fa-save me-1"></i> Simpan PIN
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

    .avatar-circle-sm {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
    }

    .fw-900 {
        font-weight: 900;
    }

    .fw-800 {
        font-weight: 800;
    }

    .fw-700 {
        font-weight: 700;
    }
</style>

<!-- Modal Tambah Akun Stasiun Langsung -->
<div class="modal fade" id="modalAddOperator" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-success p-4">
                <h5 class="modal-title fw-bold text-white"><i class="fas fa-plus-circle me-2"></i> Buat Akun Stasiun
                    Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <form id="formAddOperator">
                    <div class="alert alert-info border-0 rounded-4 shadow-sm mb-4">
                        <div class="d-flex gap-3">
                            <i class="fas fa-info-circle fa-2x"></i>
                            <div class="small">
                                <strong>PENTING:</strong> Gunakan prefix <code>produksi_</code>, <code>op_</code>,
                                <code>fn_</code>, atau <code>pb_</code> agar akun otomatis muncul di manajemen ini. <br>
                                <em>PIN Produksi awal akan diset otomatis: <strong>1234</strong></em>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">USERNAME (LOGIN)</label>
                            <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                <select name="username_prefix" class="form-select border-0 bg-light fw-bold"
                                    style="max-width: 110px;">
                                    <option value="produksi_">produksi_</option>
                                    <option value="op_">op_</option>
                                    <option value="fn_">fn_</option>
                                    <option value="pb_">pb_</option>
                                </select>
                                <input type="text" name="username_suffix" class="form-control fw-bold border-0"
                                    placeholder="nama_stasiun" required>
                            </div>
                            <div class="text-muted" style="font-size: 0.6rem; margin-top: 5px;">Contoh: dtf_merch,
                                label_cetak, dll.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">PASSWORD LOGIN</label>
                            <input type="password" name="password" class="form-control fw-bold border-0 shadow-sm"
                                placeholder="Minimal 4 Karakter" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted">NAMA STASIUN / TERMINAL</label>
                            <input type="text" name="nama_lengkap" class="form-control fw-bold border-0 shadow-sm"
                                placeholder="Contoh: TERMINAL DTF CABANG PUSAT" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">UNIT / CABANG</label>
                            <select name="perusahaan_id" class="form-select border-0 shadow-sm fw-bold">
                                <?php foreach ($companies as $c): ?>
                                    <option value="<?= $c->id_perusahaan ?>"><?= $c->nama_perusahaan ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">DIVISI / LEVEL</label>
                            <select name="divisi_id" class="form-select border-0 shadow-sm fw-bold">
                                <?php foreach ($divisions as $d): ?>
                                    <?php if (in_array((int) $d->divisi_id, [3, 5, 7])): ?>
                                        <option value="<?= $d->divisi_id ?>"><?= $d->divisi_nama ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12 mt-3">
                            <div class="p-3 rounded-4 bg-white border border-dashed border-success">
                                <label class="form-label fw-bold small text-success"><i class="fas fa-link me-1"></i>
                                    WORKCENTER (EXPLICIT MAPPING)</label>
                                <select name="operator_id" class="form-select border-0 shadow-sm fw-bold bg-light">
                                    <option value="">-- TANPA MAPPING --</option>
                                    <?php foreach ($list_operator as $lop): ?>
                                        <option value="<?= $lop->operator_id ?>"><?= strtoupper($lop->operator_nama) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 p-4 bg-white">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success rounded-pill px-5 shadow-sm fw-bold"
                    onclick="createOperator()">
                    <i class="fas fa-save me-1"></i> Daftarkan Akun
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function showAddModal() {
        $('#formAddOperator')[0].reset();
        new bootstrap.Modal(document.getElementById('modalAddOperator')).show();
    }

    function createOperator() {
        const formData = $('#formAddOperator').serialize();

        // Gunakan target modal agar ARIA-hidden tidak konflik dengan body
        Swal.fire({
            title: 'Mendaftarkan akun...',
            allowOutsideClick: false,
            target: document.getElementById('modalAddOperator'),
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: '<?= site_url('OperatorBersama/add_operator_ajax') ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    bootstrap.Modal.getInstance(document.getElementById('modalAddOperator')).hide();
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false }).then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message, target: document.getElementById('modalAddOperator') });
                }
            },
            error: function (xhr, status, error) {
                Swal.fire({ icon: 'error', title: 'Error Sistem', text: 'Gagal terhubung ke server atau terjadi kesalahan internal database.', target: document.getElementById('modalAddOperator') });
            }
        });
    }



    function editPassword(userId, username) {
        $('#resetUserId').val(userId);
        $('#resetUserDisp').text(username);
        $('#newPassword').val('');
        new bootstrap.Modal(document.getElementById('modalEditPassword')).show();
        setTimeout(() => $('#newPassword').focus(), 500);
    }

    function savePassword() {
        const password = $('#newPassword').val();
        if (password.length < 4) {
            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Password minimal 4 karakter!', confirmButtonColor: '#343a40' });
            return;
        }

        Swal.fire({ title: 'Mereset Password...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

        $.ajax({
            url: '<?= site_url('OperatorBersama/update_password_ajax') ?>',
            type: 'POST',
            data: { user_id: $('#resetUserId').val(), password: password },
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

    function editData(userId) {
        Swal.fire({ title: 'Memuat data...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

        $.getJSON('<?= site_url('OperatorBersama/get_operator_ajax/') ?>' + userId, function (res) {
            Swal.close();
            $('#val_user_id').val(res.user_id);
            $('#val_karyawan_id').val(res.karyawan_id);
            $('#val_username').val(res.username);
            $('#val_nik').val(res.nik);
            $('#val_nama_lengkap').val(res.nama_lengkap);
            $('#val_perusahaan_id').val(res.perusahaan_id1);
            $('#val_divisi_id').val(res.divisi_id);
            $('#val_operator_id').val(res.operator_id);
            if (res.mesin_ids) {
                $('#val_mesin_ids').val(res.mesin_ids.split(','));
            } else {
                $('#val_mesin_ids').val([]);
            }

            new bootstrap.Modal(document.getElementById('modalEditOperator')).show();
        });
    }

    function saveOperator() {
        const formData = $('#formEditOperator').serialize();

        Swal.fire({ title: 'Menyimpan perubahan...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

        $.ajax({
            url: '<?= site_url('OperatorBersama/save_operator_ajax') ?>',
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

    function editPin(id, username) {
        $('#editKaryawanId').val(id);
        $('#editUsernameDisp').text(username);
        $('#newPin').val('');
        new bootstrap.Modal(document.getElementById('modalEditPin')).show();
        setTimeout(() => $('#newPin').focus(), 500);
    }

    function savePin() {
        const pin = $('#newPin').val();
        if (pin.length !== 4) {
            Swal.fire({ icon: 'error', title: 'Gagal', text: 'PIN harus 4 digit!', confirmButtonColor: '#3085d6' });
            return;
        }

        Swal.fire({
            title: 'Memperbarui PIN...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: '<?= site_url('OperatorBersama/update_pin') ?>',
            type: 'POST',
            data: { karyawan_id: $('#editKaryawanId').val(), pin: pin },
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

    function showTasks(tasks, operatorName) {
        $('#tugasOperatorName').text(operatorName);
        let container = $('#tugasListContainer');
        container.empty();
        
        let html = '<table class="table table-hover table-bordered align-middle text-start mb-0">';
        html += '<thead class="table-light"><tr><th width="10%" class="text-center">NO</th><th>NAMA TUGAS / PROSES ROUTING</th></tr></thead><tbody>';
        
        if (tasks && tasks.trim() !== '' && tasks !== 'Tidak ada tugas yang terdaftar') {
            let taskArray = tasks.split(',');
            let no = 1;
            taskArray.forEach(function(t) {
                if(t.trim() !== '') {
                    html += '<tr><td class="text-center fw-bold text-muted">' + (no++) + '</td><td class="fw-bold text-dark"><i class="fas fa-check-circle text-success me-2"></i>' + t.trim() + '</td></tr>';
                }
            });
        } else {
            html += '<tr><td colspan="2" class="text-center text-muted fw-bold fst-italic py-4">Tidak ada tugas routing yang terdaftar untuk operator ini.</td></tr>';
        }
        
        html += '</tbody></table>';
        container.html(html);
        
        new bootstrap.Modal(document.getElementById('modalLihatTugas')).show();
    }

    $(document).ready(function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        $('#searchOperator').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#tableOperator tbody tr').filter(function() {
                if($(this).find('.fa-users-slash').length > 0) return;
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>