<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">

<style>
    :root {
        --tgm-primary: #2563eb;
        --tgm-primary-soft: #eff6ff;
        --slate-50: #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --slate-300: #cbd5e1;
        --slate-400: #94a3b8;
        --slate-500: #64748b;
        --slate-600: #475569;
        --slate-700: #334155;
        --slate-800: #1e293b;
        --slate-900: #0f172a;
        --emerald: #10b981;
        --rose: #f43f5e;
        --amber: #f59e0b;
        --sky: #0ea5e9;
    }

    .app-content {
        background-color: #f8fafc;
        padding: 2rem;
        min-height: 100vh;
    }

    /* Typography & Premium Utilities */
    .fw-800 {
        font-weight: 800;
    }

    .fw-900 {
        font-weight: 900;
    }

    .ls-n1 {
        letter-spacing: -1px;
    }

    .ls-1 {
        letter-spacing: 1px;
    }

    .font-mono {
        font-family: 'JetBrains Mono', 'Fira Code', monospace;
    }

    .text-primary-tgm {
        color: var(--tgm-primary);
    }

    .bg-primary-tgm {
        background: var(--tgm-primary);
    }

    /* Buttons */
    .btn-primary-tgm {
        background: var(--tgm-primary);
        color: white;
        border: none;
        font-weight: 800;
        border-radius: 12px;
        padding: 10px 24px;
        transition: all 0.3s;
    }

    .btn-primary-tgm:hover {
        background: #1d4ed8;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
    }

    .btn-white {
        background: white;
        color: var(--slate-700);
        border: 1px solid var(--slate-200);
        font-weight: 800;
        border-radius: 12px;
        padding: 10px 24px;
        transition: all 0.3s;
    }

    .btn-white:hover {
        background: var(--slate-50);
        color: var(--tgm-primary);
        border-color: var(--tgm-primary);
    }

    /* Clean Card System */
    .clean-card {
        background: white;
        border: 1px solid var(--slate-200);
        border-radius: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
        overflow: hidden;
    }

    /* Stat Box Grid */
    .stats-minimal {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-box {
        padding: 1.5rem;
        border-radius: 20px;
        border: 1px solid var(--slate-200);
        background: white;
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }

    .stat-box::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: var(--slate-200);
        transition: all 0.3s;
    }

    .stat-box:hover {
        transform: translateY(-5px);
        border-color: var(--tgm-primary);
    }

    .stat-box.active:hover::after {
        background: var(--emerald);
    }

    .stat-box.blocked:hover::after {
        background: var(--rose);
    }

    /* Table System */
    .table-premium {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-premium thead th {
        background: var(--slate-50);
        color: var(--slate-500);
        font-weight: 900;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 1.2rem;
        border-bottom: 2px solid var(--slate-100);
    }

    .table-premium tbody td {
        padding: 1.2rem;
        border-bottom: 1px solid var(--slate-100);
        color: var(--slate-700);
        vertical-align: middle;
    }

    /* Avatar & Badges */
    .user-avatar-wrapper {
        width: 42px;
        height: 42px;
        overflow: hidden;
        border-radius: 12px;
        border: 2px solid white;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .badge-soft {
        font-weight: 900;
        font-size: 0.65rem;
        padding: 6px 12px;
        border-radius: 8px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .role-label {
        background: var(--slate-100);
        color: var(--slate-600);
        font-weight: 800;
        font-size: 0.65rem;
        padding: 4px 10px;
        border-radius: 6px;
        display: inline-block;
    }

    .btn-action {
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        border: 1px solid var(--slate-200);
        background: white;
        transition: all 0.2s;
    }

    .btn-action:hover {
        background: var(--slate-50);
        transform: scale(1.1);
    }

    @media print {
        .no-print {
            display: none !important;
        }

        .app-content {
            padding: 0;
        }
    }
</style>
<?php $this->load->view('layout_admin/header_modul'); ?>
<div class="app-content">


    <!-- <div class="container-fluid px-0"> -->

    <!-- Premium Header -->


    <!-- Statistics Overview Grid -->
    <div class="stats-minimal no-print">
        <div class="stat-box">
            <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.6rem;">Total Pengguna</div>
            <div class="h2 fw-900 font-mono text-dark mb-0 mt-2"><?= number_format($total_rows) ?></div>
            <div class="small fw-700 text-muted mt-1 opacity-50">REGISTERED USERS</div>
        </div>
        <div class="stat-box active">
            <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.6rem;">Pengguna Aktif</div>
            <div class="h2 fw-900 font-mono text-emerald mb-0 mt-2"><?= number_format($active_users) ?></div>
            <div class="small fw-700 text-emerald mt-1">ONLINE ACCESS</div>
        </div>
        <div class="stat-box">
            <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.6rem;">ID Tidak Aktif</div>
            <div class="h2 fw-900 font-mono text-warning mb-0 mt-2"><?= number_format($inactive_users ?? 0) ?></div>
            <div class="small fw-700 text-warning mt-1">SUSPENDED</div>
        </div>
        <div class="stat-box blocked">
            <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.6rem;">Akses Diblokir</div>
            <div class="h2 fw-900 font-mono text-rose mb-0 mt-2"><?= number_format($blocked_users ?? 0) ?></div>
            <div class="small fw-700 text-rose mt-1">SECURITY BLOCKED</div>
        </div>
    </div>

    <!-- Top Area: Quick Actions & Filter (Full Width) -->
    <!-- Consolidated Management Hub (Full Width Bar) -->
    <div class="clean-card shadow-sm border-0 mb-4 no-print overflow-visible">
        <div class="p-4 bg-slate-100 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="fw-900 mb-0 text-dark ls-1"><i class="bi bi-gear-wide-connected me-2 text-primary-tgm"></i>
                KONTROL PENGGUNA</h6>
            <div class="small fw-900 text-muted opacity-50 ls-1">MANAGE & FILTER</div>
        </div>
        <div class="p-4 bg-white">
            <form id="searchForm">
                <div class="row g-3 align-items-end">
                    <!-- Main Action -->
                    <!-- Main Action -->
                    <div class="col-xl-3 col-lg-4">
                        <label class="fw-900 small text-muted text-uppercase mb-2 ls-1"
                            style="font-size: 0.65rem;">Kontrol Akses</label>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn-white shadow-sm px-3" style="height: 45px;"
                                data-bs-toggle="modal" data-bs-target="#modalGuideAccount" title="Buka Panduan Modul">
                                <i class="bi bi-journal-text"></i>
                            </button>
                            <button type="button"
                                class="btn btn-primary-tgm w-100 d-flex align-items-center justify-content-center gap-2"
                                style="height: 45px; font-size: 0.85rem;" data-bs-toggle="modal"
                                data-bs-target="#modalTambahUser">
                                <i class="bi bi-person-plus-fill"></i> TAMBAH AKUN
                            </button>
                        </div>
                    </div>

                    <!-- Separator -->
                    <div class="col-auto d-none d-xl-block">
                        <div class="vr h-100 mx-1" style="opacity: 0.1; width: 1px; margin-top: 28px; height: 45px !important;"></div>
                    </div>

                    <!-- Filter Fields -->
                    <div class="col-xl-3 col-lg-4">
                        <label class="fw-900 small text-muted text-uppercase mb-2 ls-1" style="font-size: 0.65rem;">Cari Nama / User</label>
                        <div class="input-group">
                            <span class="input-group-text bg-slate-50 border-0"
                                style="border-radius: 12px 0 0 12px; height: 45px;"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control border-0 bg-slate-50 fw-900" id="search"
                                name="search" placeholder="Input keyword..."
                                value="<?= $this->input->get('search') ?>"
                                style="border-radius: 0 12px 12px 0; height: 45px; font-size: 0.85rem;">
                        </div>
                    </div>

                    <div class="col-xl-2 col-lg-4">
                        <label class="fw-900 small text-muted text-uppercase mb-2 ls-1"
                            style="font-size: 0.65rem;">Status Akun</label>
                        <select class="form-select border-0 bg-slate-50 fw-900" id="status" name="status"
                            style="border-radius: 12px; height: 45px; font-size: 0.85rem;">
                            <option value="">-- SEMUA --</option>
                            <option value="1" <?= $this->input->get('status') == '1' ? 'selected' : '' ?>>AKTIF</option>
                            <option value="0" <?= $this->input->get('status') == '0' ? 'selected' : '' ?>>NONAKTIF</option>
                            <option value="2" <?= $this->input->get('status') == '2' ? 'selected' : '' ?>>DIBLOKIR</option>
                        </select>
                    </div>

                    <div class="col-xl-2 col-lg-4">
                        <label class="fw-900 small text-muted text-uppercase mb-2 ls-1" style="font-size: 0.65rem;">Akses Role</label>
                        <select class="form-select border-0 bg-slate-50 fw-900" id="role" name="role"
                            style="border-radius: 12px; height: 45px; font-size: 0.85rem;">
                            <option value="">-- SEMUA --</option>
                            <?php foreach ($list_role as $role): ?>
                                <option value="<?= $role->role_id ?>" <?= $this->input->get('role') == $role->role_id ? 'selected' : '' ?>>
                                    <?= strtoupper(htmlspecialchars($role->role_name)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-xl-1 col-lg-2">
                        <button type="submit"
                            class="btn btn-primary-tgm w-100 fw-900 d-flex align-items-center justify-content-center shadow-sm"
                            style="height: 45px; font-size: 0.8rem;">
                            <i class="bi bi-filter"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Section: Data Table -->
    <div class="row g-4">
        <div class="col-12">
            <div class="clean-card shadow-sm border-0 h-100">
                <div class="p-4 bg-slate-100 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-900 mb-0 text-dark ls-1"><i class="bi bi-table me-2 text-primary-tgm"></i>INDEX DATA
                        PENGGUNA</h6>
                    <span class="badge-soft bg-primary-tgm text-white fw-900 px-3 py-2"><?= count($tampilData) ?> TOTAL
                        DATA</span>
                </div>
                <div class="table-responsive">
                    <table class="table-premium" id="tableAkun">
                        <thead>
                            <tr>
                                <th class="text-center" width="50">#</th>
                                <th>DATA IDENTITAS</th>
                                <th>AKSES & STORE</th>
                                <th class="text-center">STATUS</th>
                                <th class="text-center no-print">AKSI</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <?php if (!empty($tampilData)):
                                $no = 1;
                                foreach ($tampilData as $dt): ?>
                                    <tr>
                                        <td class="text-center font-mono small text-muted"><?= $no++ ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="user-avatar-wrapper shadow-sm">
                                                    <img src="<?= base_url('assets/images/karyawan/' . ($dt->foto ?: 'default.png')); ?>"
                                                        class="w-100 h-100" style="object-fit: cover;" alt="Avatar">
                                                </div>
                                                <div>
                                                    <div class="fw-900 text-dark ls-n1">
                                                        <?= strtoupper(htmlspecialchars($dt->nama_karyawan ?: '-')) ?>
                                                    </div>
                                                    <div class="text-primary-tgm font-mono small fw-900">
                                                        @<?= htmlspecialchars($dt->username) ?></div>
                                                    <div class="text-muted small fw-800 ls-1" style="font-size: 0.65rem;">
                                                        <?= htmlspecialchars($dt->email) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="role-label mb-1 ls-1">
                                                <?= strtoupper(htmlspecialchars($dt->role_name ?: '-')) ?>
                                            </div>
                                            <div class="text-muted small fw-900 font-mono"><i
                                                    class="bi bi-shop me-1 text-primary-tgm"></i><?= strtoupper(htmlspecialchars($dt->nama_perusahaan ?: '-')) ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($dt->active == '1'): ?>
                                                <span class="badge-soft bg-soft-success bg-emerald fw-900">ENABLED</span>
                                            <?php elseif ($dt->active == '0'): ?>
                                                <span class="badge-soft bg-rose text-white fw-900">SUSPENDED</span>
                                            <?php else: ?>
                                                <span class="badge-soft bg-amber text-dark fw-900">BANNED</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="no-print">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn-action btn-reset-password" data-userid="<?= $dt->user_id ?>"
                                                    title="Reset Password">
                                                    <i class="bi bi-shield-lock text-info"></i>
                                                </button>
                                                <button class="btn-action btn-edit-akun" data-userid="<?= $dt->user_id ?>"
                                                    title="Edit Akun">
                                                    <i class="bi bi-pencil-square text-warning"></i>
                                                </button>
                                                <button class="btn-action btn-hapus-akun"
                                                    data-href="<?= base_url('akun/delete_user/' . $dt->user_id) ?>"
                                                    title="Hapus Akun">
                                                    <i class="bi bi-trash3 text-rose"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="5">
                                        <div class="text-center py-5">
                                            <i class="bi bi-people text-muted display-3 opacity-25"></i>
                                            <h6 class="mt-3 fw-900 text-muted ls-1">MODUL DATA KOSONG</h6>
                                            <div class="small fw-700 text-muted">Belum ada akun yang terdaftar dalam
                                                database.</div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="clean-card p-3 mt-4 no-print shadow-sm border-0" id="paginationContainer" style="display: none;">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                <i class="bi bi-info-circle me-1 text-primary-tgm"></i> Menampilkan <strong id="currentCount"
                    class="text-dark">0</strong> dari <strong id="totalCount" class="text-dark">0</strong> data terpilih
                <span class="badge-soft bg-sky text-dark ms-2 px-3 fw-900" id="searchIndicator"
                    style="display: none;">HASIL FILTER AKTIF</span>
            </div>

            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0 gap-1" id="paginationList">
                    <!-- Pagination will be generated by jQuery -->
                </ul>
            </nav>
        </div>
    </div>
</div>
</div>

<!-- Modal Advanced Search -->
<div class="modal fade" id="modalAdvancedSearch" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-slate-900 text-white p-4">
                <h5 class="modal-title fw-900 ls-n1"><i class="bi bi-search me-2 text-primary-tgm"></i>PENCARIAN
                    LANJUTAN</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-slate-50">
                <form id="advancedSearchForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fw-900 small text-muted text-uppercase mb-2 ls-1">Username</label>
                            <input type="text" class="form-control border-0 bg-white fw-900 px-3" name="username"
                                placeholder="Cari username..." style="border-radius: 12px; height: 50px;">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-900 small text-muted text-uppercase mb-2 ls-1">Email Karyawan</label>
                            <input type="email" class="form-control border-0 bg-white fw-900 px-3" name="email"
                                placeholder="Cari email..." style="border-radius: 12px; height: 50px;">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-900 small text-muted text-uppercase mb-2 ls-1">Nama Lengkap</label>
                            <input type="text" class="form-control border-0 bg-white fw-900 px-3" name="nama_karyawan"
                                placeholder="Cari nama karyawan..." style="border-radius: 12px; height: 50px;">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-900 small text-muted text-uppercase mb-2 ls-1">Status Keanggotaan</label>
                            <select class="form-select border-0 bg-white fw-900 px-3" name="status"
                                style="border-radius: 12px; height: 50px;">
                                <option value="">SEMUA STATUS</option>
                                <option value="1">AKTIF (ENABLED)</option>
                                <option value="0">TIDAK AKTIF (SUSPEND)</option>
                                <option value="2">DIBLOKIR (BANNED)</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 p-4 bg-white">
                <button type="button" class="btn btn-slate-100 px-4 fw-900 rounded-pill"
                    data-bs-dismiss="modal">BATAL</button>
                <button type="button" class="btn btn-primary-tgm px-5 fw-900 rounded-pill shadow-sm ls-1"
                    id="performAdvancedSearch">
                    <i class="bi bi-search me-2"></i> CARI DATA
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah User -->
<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?= base_url('akun/action_add_user') ?>" method="POST" id="formTambahUser" autocomplete="off"
            class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-primary-tgm text-white p-4">
                <h5 class="modal-title fw-900 ls-n1"><i class="bi bi-person-plus-fill me-2"></i>REGISTRASI AKUN BARU
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-slate-50">
                <div class="badge-soft bg-sky text-dark mb-4 p-3 d-flex align-items-center gap-3">
                    <i class="bi bi-info-circle-fill fs-5 text-primary-tgm"></i>
                    <div class="small fw-800 ls-1 text-dark">Pilih karyawan dari list. Sistem akan mencoba mengisi data
                        secara otomatis berdasarkan profil karyawan.</div>
                </div>

                <div class="mb-3">
                    <label class="fw-900 small text-muted text-uppercase mb-2 ls-1">Pilih Karyawan</label>
                    <select name="karyawan_id" id="tambahKaryawanId" class="form-select border-0 fw-900 px-3" required
                        style="border-radius: 12px; height: 50px;">
                        <option value="" selected disabled>-- CARI NAMA KARYAWAN --</option>
                        <?php foreach ($karyawan as $k): ?>
                            <option value="<?= $k->karyawan_id ?>" data-email="<?= htmlspecialchars($k->email) ?>"
                                data-nama="<?= htmlspecialchars($k->nama_lengkap) ?>">
                                <?= strtoupper(htmlspecialchars($k->nama_lengkap)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="fw-900 small text-muted text-uppercase mb-2 ls-1">Username</label>
                            <input type="text" name="username" id="tambahUsername"
                                class="form-control border-0 fw-900 px-3" required
                                style="border-radius: 12px; height: 50px;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="fw-900 small text-muted text-uppercase mb-2 ls-1">Email</label>
                            <input type="email" name="email" id="tambahEmail" class="form-control border-0 fw-900 px-3"
                                required style="border-radius: 12px; height: 50px;">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="fw-900 small text-muted text-uppercase mb-2 ls-1">Set Password Baru</label>
                    <input type="password" name="password" class="form-control border-0 fw-900 px-3" required
                        placeholder="Minimal 6 karakter..." style="border-radius: 12px; height: 50px;">
                </div>

                <div class="mb-3">
                    <label class="fw-900 small text-muted text-uppercase mb-2 ls-1">Lokasi Kerja (Store)</label>
                    <select name="perusahaan_id" class="form-select border-0 fw-900 px-3" required
                        style="border-radius: 12px; height: 50px;">
                        <option value="" selected disabled>-- PILIH CABANG --</option>
                        <?php foreach ($list_perusahaan as $p): ?>
                            <option value="<?= $p->id_perusahaan ?>">
                                <?= strtoupper(htmlspecialchars($p->nama_perusahaan)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="fw-900 small text-muted text-uppercase mb-2 ls-1">Hak Akses Role</label>
                    <select name="role_id" id="tambahRoleId" class="form-select border-0 fw-900 px-3" required
                        style="border-radius: 12px; height: 50px;">
                        <option value="" selected disabled>-- PILIH LEVEL AKSES --</option>
                        <?php foreach ($list_role as $role): ?>
                            <option value="<?= $role->role_id ?>"><?= strtoupper(htmlspecialchars($role->role_name)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-0" id="tambahOperatorField" style="display: none;">
                    <label class="fw-900 small text-muted text-uppercase mb-2 ls-1">Relasi Operator</label>
                    <select name="operator_id" id="tambahOperatorId" class="form-select border-0 fw-900 px-3"
                        style="border-radius: 12px; height: 50px;">
                        <option value="">-- PILIH OPERATOR PRODUKSI --</option>
                        <?php foreach ($list_operator as $operator): ?>
                            <option value="<?= $operator->operator_id ?>">
                                <?= strtoupper(htmlspecialchars($operator->operator_nama)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-white">
                <button type="button" class="btn btn-slate-100 px-4 fw-900 rounded-pill"
                    data-bs-dismiss="modal">BATAL</button>
                <button type="submit" class="btn btn-primary-tgm px-5 fw-900 rounded-pill shadow-sm ls-1">DAFTARKAN
                    AKUN</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="modalEditUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?= base_url('akun/action_edit_user') ?>" method="POST" id="formEditUser" autocomplete="off"
            class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-slate-900 text-white p-4">
                <h5 class="modal-title fw-900 ls-n1"><i class="bi bi-person-gear me-2 text-primary-tgm"></i>PENGATURAN
                    AKSES</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-slate-50">
                <div class="d-flex align-items-center bg-white p-3 border-0 shadow-sm rounded-4 mb-4">
                    <div class="user-avatar-wrapper me-3" style="width: 55px; height: 55px; border-radius: 15px;">
                        <img id="editAvatar" src="<?= base_url('assets/images/karyawan/default.png') ?>"
                            class="w-100 h-100" style="object-fit:cover;">
                    </div>
                    <div>
                        <h6 class="mb-0 fw-900 text-dark ls-n1" id="editNamaLengkapLabel">-</h6>
                        <small class="text-primary-tgm fw-800 ls-1 font-mono" id="editEmailLabel">-</small>
                    </div>
                </div>

                <input type="hidden" name="user_id" id="editUserId">

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="fw-900 small text-muted text-uppercase mb-2 ls-1">Username</label>
                            <input type="text" name="username" id="editUsername"
                                class="form-control border-0 fw-900 px-3" required
                                style="border-radius: 12px; height: 50px;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="fw-900 small text-muted text-uppercase mb-2 ls-1">Email</label>
                            <input type="email" name="email" id="editEmail" class="form-control border-0 fw-900 px-3"
                                required style="border-radius: 12px; height: 50px;">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="fw-900 small text-muted text-uppercase mb-2 ls-1">Relasi Karyawan</label>
                    <select name="karyawan_id" id="editKaryawanId" class="form-select border-0 fw-900 px-3" required
                        style="border-radius: 12px; height: 50px;">
                        <option value="">-- HUBUNGKAN KE KARYAWAN --</option>
                        <?php foreach ($list_karyawan as $k): ?>
                            <option value="<?= $k->karyawan_id ?>"><?= strtoupper(htmlspecialchars($k->nama_lengkap)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="fw-900 small text-muted text-uppercase mb-2 ls-1">Lokasi Store (Cabang)</label>
                    <select name="perusahaan_id" id="editPerusahaanId" class="form-select border-0 fw-900 px-3" required
                        style="border-radius: 12px; height: 50px;">
                        <?php foreach ($list_perusahaan as $perusahaan): ?>
                            <option value="<?= $perusahaan->id_perusahaan ?>">
                                <?= strtoupper(htmlspecialchars($perusahaan->nama_perusahaan)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="fw-900 small text-muted text-uppercase mb-2 ls-1">Level Hak Akses</label>
                    <select name="role_id" id="editRoleId" class="form-select border-0 fw-900 px-3" required
                        style="border-radius: 12px; height: 50px;">
                        <?php foreach ($list_role as $role): ?>
                            <option value="<?= $role->role_id ?>"
                                data-role-name="<?= htmlspecialchars($role->role_name) ?>">
                                <?= strtoupper(htmlspecialchars($role->role_name)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-0" id="operatorField" style="display: none;">
                    <label class="fw-900 small text-muted text-uppercase mb-2 ls-1">Operator Terikat</label>
                    <select name="operator_id" id="editOperatorId" class="form-select border-0 fw-900 px-3"
                        style="border-radius: 12px; height: 50px;">
                        <option value="">-- PILIH OPERATOR PRODUKSI --</option>
                        <?php foreach ($list_operator as $operator): ?>
                            <option value="<?= $operator->operator_id ?>">
                                <?= strtoupper(htmlspecialchars($operator->operator_nama)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-white">
                <button type="button" class="btn btn-slate-100 px-4 fw-900 rounded-pill"
                    data-bs-dismiss="modal">BATAL</button>
                <button type="submit" class="btn btn-primary-tgm px-5 fw-900 rounded-pill shadow-sm ls-1">SIMPAN
                    PERUBAHAN</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Panduan Pengelolaan Akun -->
<div class="modal fade" id="modalGuideAccount" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-slate-900 text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary-tgm text-white rounded-pill d-flex align-items-center justify-content-center"
                        style="width: 45px; height: 45px;">
                        <i class="bi bi-shield-lock-fill fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-900 mb-0 ls-n1">PANDUAN <span class="opacity-75">USER &
                                SECURITY</span></h5>
                        <p class="small mb-0 opacity-75 fw-800 ls-1">SOP Pengelolaan Hak Akses & Akun</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-slate-50">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="clean-card p-4 h-100 shadow-none">
                            <h6 class="fw-900 text-dark mb-3 ls-1 border-bottom pb-2">SIKLUS REGISTRASI</h6>
                            <ul class="list-unstyled mb-0 font-mono small fw-800 text-muted">
                                <li class="mb-3 d-flex gap-2"><i class="bi bi-1-circle-fill text-primary-tgm"></i>
                                    <span>Daftarkan data karyawan terlebih dahulu di modul Karyawan.</span>
                                </li>
                                <li class="mb-3 d-flex gap-2"><i class="bi bi-2-circle-fill text-primary-tgm"></i>
                                    <span>Buka modal Tambah Akun dan pilih nama karyawan tersebut.</span>
                                </li>
                                <li class="d-flex gap-2"><i class="bi bi-3-circle-fill text-primary-tgm"></i> <span>Set
                                        username dan email aktif untuk notifikasi sistem.</span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="clean-card p-4 h-100 shadow-none">
                            <h6 class="fw-900 text-dark mb-3 ls-1 border-bottom pb-2">STRUKTUR AKSES</h6>
                            <div class="mb-3">
                                <span class="badge-soft bg-emerald py-1 px-2 fw-900">ROLE ID</span>
                                <p class="small text-muted fw-700 mt-2">Setiap akun wajib memiliki satu Role ID yang
                                    menentukan menu apa saja yang bisa diakses user.</p>
                            </div>
                            <div>
                                <span class="badge-soft bg-sky py-1 px-2 fw-900">STORE TAG</span>
                                <p class="small text-muted fw-700 mt-2">Menghubungkan user ke cabang tertentu untuk
                                    filter data transaksi kasir/stok.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="clean-card p-4 shadow-none">
                            <h6 class="fw-900 text-dark mb-3 ls-1 border-bottom pb-2"><i
                                    class="bi bi-shield-check text-emerald me-2"></i>KEBIJAKAN KEAMANAN</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="p-3 bg-slate-100 rounded-3">
                                        <div class="fw-900 small mb-1">RESET PASSWORD</div>
                                        <p class="small text-muted fw-700 mb-0">Klik tombol kunci (<i
                                                class="bi bi-shield-lock"></i>) untuk mengembalikan password ke default
                                            jika user lupa.</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 bg-slate-100 rounded-3">
                                        <div class="fw-900 small mb-1">SUSPEND AKSES</div>
                                        <p class="small text-muted fw-700 mb-0">Disarankan untuk me-nonaktifkan status
                                            akun daripada menghapus data permanen.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-white">
                <button type="button" class="btn btn-primary-tgm w-100 py-3 rounded-pill fw-900 ls-1 shadow-sm"
                    data-bs-dismiss="modal">SAYA MENGERTI</button>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        // Initialize Select2
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }

        // Initialize tooltips
        if (typeof bootstrap !== 'undefined') {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // Animate stat values
        const statValues = document.querySelectorAll('.stat-box .h2');
        statValues.forEach(stat => {
            const finalValue = parseInt(stat.textContent.replace(/,/g, ''));

            if (!isNaN(finalValue)) {
                let currentValue = 0;
                const increment = finalValue / 50;

                const timer = setInterval(() => {
                    currentValue += increment;
                    if (currentValue >= finalValue) {
                        currentValue = finalValue;
                        clearInterval(timer);
                    }

                    stat.textContent = Math.floor(currentValue).toLocaleString();
                }, 30);
            }
        });

        // Pagination variables
        let currentPage = 1;
        let totalPages = <?= isset($total_pages) ? $total_pages : 1 ?>;
        let totalRecords = <?= isset($total_records) ? $total_records : (isset($total_rows) ? $total_rows : 0) ?>;
        let currentCount = <?= count($tampilData) ?>;

        // Initialize pagination and load initial data
        initializePagination();
        loadData(false); // Load initial data without loading indicator

        // Search form submission
        $('#searchForm').on('submit', function (e) {
            e.preventDefault();
            currentPage = 1;
            loadData(true);
        });

        // Auto-submit form on filter changes
        $('#status, #role, #perusahaan').on('change', function () {
            currentPage = 1;
            loadData(true);
        });

        // Add search functionality with debounce
        let searchTimeout;
        $('#search').on('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentPage = 1;
                loadData(true);
            }, 500);
        });

        // Load data function
        function loadData(showLoading = true) {
            const searchData = {
                search: $('#search').val(),
                status: $('#status').val(),
                role: $('#role').val(),
                perusahaan: $('#perusahaan').val(),
                page: currentPage
            };

            // Show loading only if not initial load
            if (showLoading) {
                $('#tableBody').html('<tr><td colspan="5" class="text-center py-5"><div class="spinner-border text-primary-tgm" role="status"></div><div class="mt-2 fw-900 small text-muted ls-1">MENGAMBIL DATA...</div></td></tr>');
            }

            $.ajax({
                url: '<?= base_url('akun/get_data_ajax') ?>',
                type: 'POST',
                data: searchData,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        updateTable(response.data);
                        updatePagination(response.pagination);
                        updateStats(response.stats);

                        // Show search indicator if filters are applied
                        if (searchData.search || searchData.status || searchData.role || searchData.perusahaan) {
                            $('#searchIndicator').show();
                        } else {
                            $('#searchIndicator').hide();
                        }
                    } else {
                        $('#tableBody').html('<tr><td colspan="8" class="text-center text-danger">Error: ' + response.message + '</td></tr>');
                    }
                },
                error: function () {
                    $('#tableBody').html('<tr><td colspan="8" class="text-center text-danger">Terjadi kesalahan sistem</td></tr>');
                }
            });
        }

        // Update table function
        function updateTable(data) {
            let html = '';

            if (data.length > 0) {
                data.forEach((item, index) => {
                    const no = ((currentPage - 1) * 10) + index + 1;
                    let statusBadge = '';

                    if (item.active == '1') {
                        statusBadge = '<span class="badge-soft bg-soft-success bg-emerald fw-900">ENABLED</span>';
                    } else if (item.active == '0') {
                        statusBadge = '<span class="badge-soft bg-rose text-white fw-900">SUSPENDED</span>';
                    } else {
                        statusBadge = '<span class="badge-soft bg-amber text-dark fw-900">BANNED</span>';
                    }

                    html += `
                    <tr>
                        <td class="text-center font-mono small text-muted">${no}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="user-avatar-wrapper shadow-sm">
                                    <img src="<?= base_url('assets/images/karyawan/') ?>${item.foto || 'default.png'}" 
                                         class="w-100 h-100" style="object-fit: cover;" alt="Avatar">
                                </div>
                                <div>
                                    <div class="fw-900 text-dark ls-n1">${(item.nama_karyawan || '-').toUpperCase()}</div>
                                    <div class="text-primary-tgm font-mono small fw-900">@${item.username}</div>
                                    <div class="text-muted small fw-800 ls-1" style="font-size: 0.65rem;">${item.email}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="role-label mb-1 ls-1">${(item.role_name || '-').toUpperCase()}</div>
                            <div class="text-muted small fw-900 font-mono"><i class="bi bi-shop me-1 text-primary-tgm"></i>${(item.nama_perusahaan || '-').toUpperCase()}</div>
                        </td>
                        <td class="text-center">${statusBadge}</td>
                        <td class="no-print">
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn-action btn-reset-password" data-userid="${item.user_id}" title="Reset Password">
                                    <i class="bi bi-shield-lock text-info"></i>
                                </button>
                                <button class="btn-action btn-edit-akun" data-userid="${item.user_id}" title="Edit Akun">
                                    <i class="bi bi-pencil-square text-warning"></i>
                                </button>
                                <button class="btn-action btn-hapus-akun" data-href="<?= base_url('akun/delete_user/') ?>${item.user_id}" title="Hapus Akun">
                                    <i class="bi bi-trash3 text-rose"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                });
            } else {
                html = `
                <tr>
                    <td colspan="5">
                        <div class="text-center py-5">
                            <i class="bi bi-people text-muted display-3 opacity-25"></i>
                            <h6 class="mt-3 fw-900 text-muted ls-1">MODUL DATA KOSONG</h6>
                            <div class="small fw-700 text-muted">Belum ada akun yang terdaftar dalam database.</div>
                        </div>
                    </td>
                </tr>
            `;
            }

            $('#tableBody').html(html);

            // Re-initialize tooltips
            if (typeof bootstrap !== 'undefined') {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }

            // Reinitialize event handlers for new buttons
            initializeButtonHandlers();
        }

        // Update pagination function
        function updatePagination(pagination) {
            currentPage = pagination.current_page;
            totalPages = pagination.total_pages;
            totalRecords = pagination.total_records;
            currentCount = pagination.current_count;

            $('#currentCount').text(currentCount);
            $('#totalCount').text(totalRecords);

            if (totalPages > 1) {
                $('#paginationContainer').show();

                let paginationHtml = '';
                const maxLinks = 5;
                const start = Math.max(1, currentPage - Math.floor(maxLinks / 2));
                const end = Math.min(totalPages, start + maxLinks - 1);

                // First page
                if (start > 1) {
                    paginationHtml += `
                    <li class="page-item">
                        <a class="page-link rounded-0" href="javascript:void(0)" data-page="1" title="Halaman Pertama">
                            <i class="bi bi-chevron-double-left"></i>
                        </a>
                    </li>
                `;
                }

                // Previous page
                if (currentPage > 1) {
                    paginationHtml += `
                    <li class="page-item">
                        <a class="page-link rounded-0" href="javascript:void(0)" data-page="${currentPage - 1}" title="Halaman Sebelumnya">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                `;
                }

                // Page numbers
                for (let i = start; i <= end; i++) {
                    paginationHtml += `
                    <li class="page-item ${i == currentPage ? 'active' : ''}">
                        <a class="page-link rounded-0" href="javascript:void(0)" data-page="${i}">${i}</a>
                    </li>
                `;
                }

                // Next page
                if (currentPage < totalPages) {
                    paginationHtml += `
                    <li class="page-item">
                        <a class="page-link rounded-0" href="javascript:void(0)" data-page="${currentPage + 1}" title="Halaman Selanjutnya">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                `;
                }

                // Last page
                if (end < totalPages) {
                    paginationHtml += `
                    <li class="page-item">
                        <a class="page-link rounded-0" href="javascript:void(0)" data-page="${totalPages}" title="Halaman Terakhir">
                            <i class="bi bi-chevron-double-right"></i>
                        </a>
                    </li>
                `;
                }

                $('#paginationList').html(paginationHtml);

                // Add click handlers to pagination links
                $('#paginationList .page-link').on('click', function () {
                    const page = $(this).data('page');
                    if (page && page !== currentPage) {
                        currentPage = page;
                        loadData(true);
                    }
                });
            } else {
                $('#paginationContainer').hide();
            }
        }

        // Update stats function
        function updateStats(stats) {
            if (stats) {
                $('.stat-info h3').each(function (index) {
                    const value = [stats.total_rows, stats.active_users, stats.inactive_users, stats.blocked_users][index];
                    if (value !== undefined) {
                        $(this).text(value.toLocaleString());
                    }
                });
            }
        }

        // Initialize pagination
        function initializePagination() {
            if (totalPages > 1) {
                updatePagination({
                    current_page: currentPage,
                    total_pages: totalPages,
                    total_records: totalRecords,
                    current_count: currentCount
                });
            }
        }

        // Initialize button handlers
        function initializeButtonHandlers() {
            // Delete account confirmation
            $('.btn-hapus-akun').off('click').on('click', function (e) {
                e.preventDefault();
                const href = $(this).data('href');

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Yakin ingin menghapus?',
                        text: "Akun yang dihapus tidak bisa dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = href;
                        }
                    });
                } else {
                    if (confirm('Yakin ingin menghapus akun ini?')) {
                        window.location.href = href;
                    }
                }
            });

            // Reset password confirmation
            $('.btn-reset-password').off('click').on('click', function (e) {
                e.preventDefault();
                const userId = $(this).data('userid');

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Reset Password?',
                        text: "Password akan dikembalikan ke default!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, reset!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "<?= base_url('akun/reset_password/') ?>" + userId;
                        }
                    });
                } else {
                    if (confirm('Yakin ingin reset password akun ini?')) {
                        window.location.href = "<?= base_url('akun/reset_password/') ?>" + userId;
                    }
                }
            });

            // Edit account functionality
            $('.btn-edit-akun').off('click').on('click', function (e) {
                e.preventDefault();
                const userId = $(this).data('userid');

                // Show loading state
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Loading...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => Swal.showLoading()
                    });
                }

                // Get user data via AJAX
                $.ajax({
                    url: '<?= base_url('akun/get_user_detail/') ?>' + userId,
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (typeof Swal !== 'undefined') {
                            Swal.close();
                        }
                        if (response.success) {
                            populateEditForm(response.data);
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Error', response.message || 'Gagal memuat data akun', 'error');
                            } else {
                                alert('Gagal memuat data akun');
                            }
                        }
                    },
                    error: function () {
                        if (typeof Swal !== 'undefined') {
                            Swal.close();
                            Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                        } else {
                            alert('Terjadi kesalahan sistem');
                        }
                    }
                });
            });
        }

        // Flash Message Handling with SweetAlert2
        <?php if ($this->session->flashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '<?= $this->session->flashdata('success') ?>',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '<?= $this->session->flashdata('error') ?>',
                timer: 5000,
                showConfirmButton: true
            });
        <?php endif; ?>
    });

    // Populate edit form function (global scope)
    function populateEditForm(data) {
        $('#editUserId').val(data.user_id);
        $('#editUsername').val(data.username);
        $('#editEmail').val(data.email);
        $('#editEmailLabel').text(data.email);
        $('#editKaryawanId').val(data.karyawan_id);
        $('#editPerusahaanId').val(data.perusahaan_id);
        $('#editRoleId').val(data.role_id);
        $('#editOperatorId').val(data.operator_id || '');
        $('#editNamaLengkapLabel').text(data.nama_karyawan || '-');

        const avatarImg = document.getElementById('editAvatar');
        avatarImg.src = data.foto ?
            '<?= base_url('assets/images/karyawan/') ?>' + data.foto :
            '<?= base_url('assets/images/karyawan/default.png') ?>';

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('modalEditUser'));
        modal.show();

        // Initialize Select2 in modal if available
        if (typeof $.fn.select2 !== 'undefined') {
            $('#modalEditUser .select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                dropdownParent: $('#modalEditUser')
            });
        }

        // Check if role is operator and show/hide operator field
        checkOperatorFieldVisibility();
    }

    // Function to check and show/hide operator field based on selected role
    function checkOperatorFieldVisibility() {
        const selectedRole = $('#editRoleId').val();
        const selectedRoleText = $('#editRoleId option:selected').text().toLowerCase();

        // Machine Production Roles
        const productionRoles = [4, 7, 8, 9, 10];

        if (selectedRoleText.includes('operator') && productionRoles.includes(parseInt(selectedRole))) {
            $('#operatorField').show();
            $('#editOperatorId').prop('required', true);
        } else {
            $('#operatorField').hide();
            $('#editOperatorId').prop('required', false);
            $('#editOperatorId').val('');
        }
    }

    // Event listener for role change in edit form
    $(document).on('change', '#editRoleId', function () {
        checkOperatorFieldVisibility();
    });

    // Event listener for role change in add form
    $(document).on('change', '#tambahRoleId', function () {
        checkAddOperatorFieldVisibility();
    });

    // Event listener for karyawan selection in add form
    $(document).on('change', '#tambahKaryawanId', function () {
        const selectedOption = $(this).find('option:selected');
        const email = selectedOption.data('email');
        const nama = selectedOption.data('nama');

        if (email) {
            $('#tambahEmail').val(email);
        }

        if (nama) {
            // Generate username from nama (lowercase, no spaces, no special chars)
            const username = nama.toLowerCase()
                .replace(/[^a-z0-9]/g, '') // Remove special characters
                .substring(0, 20); // Limit to 20 characters
            $('#tambahUsername').val(username);
        }
    });

    // Function to check and show/hide operator field in add form
    function checkAddOperatorFieldVisibility() {
        const selectedRole = $('#tambahRoleId').val();
        const selectedRoleText = $('#tambahRoleId option:selected').text().toLowerCase();
        const productionRoles = [4, 7, 8, 9, 10];

        if (selectedRoleText.includes('operator') && productionRoles.includes(parseInt(selectedRole))) {
            $('#tambahOperatorField').show();
            $('#tambahOperatorId').prop('required', true);
        } else {
            $('#tambahOperatorField').hide();
            $('#tambahOperatorId').prop('required', false);
            $('#tambahOperatorId').val('');
        }
    }

    // Print functionality
    $(document).on('click', '#printBtn', function () {
        window.open('<?= base_url('akun/cetak') ?>', '_blank');
    });

    // Handle export functionality
    $(document).on('click', '#exportBtn', function () {
        const search = $('#search').val();
        const status = $('#status').val();
        const role = $('#role').val();
        const perusahaan = $('#perusahaan').val();

        window.location.href = '<?= base_url('akun/export_users') ?>' +
            '?search=' + search +
            '&status=' + status +
            '&role=' + role +
            '&perusahaan=' + perusahaan;
    });
</script>