<!-- Mobile SDM Detail View - Consistent Dark Theme -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

<style>
    :root {
        --primary: #1a1a2e;
        --secondary: #16213e;
    }

    * {
        -webkit-tap-highlight-color: transparent;
        box-sizing: border-box;
    }

    body {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        min-height: 100vh;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        margin: 0;
        padding: 0;
    }

    .page-header {
        padding: 1.5rem 1rem;
        color: #fff;
        text-align: center;
    }

    .page-header h5 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .page-header small {
        opacity: 0.7;
        font-size: 0.8rem;
    }

    .content-container {
        background: #f5f5f5;
        border-radius: 24px 24px 0 0;
        min-height: calc(100vh - 100px);
        padding: 1.25rem;
    }

    /* Profile Header */
    .profile-header {
        text-align: center;
        padding: 1rem;
        background: #fff;
        border-radius: 16px;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .profile-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--primary);
        margin-bottom: 0.75rem;
        background: #e5e7eb;
    }

    .profile-name {
        font-size: 1rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 0.15rem;
        line-height: 1.3;
    }

    .profile-role {
        font-size: 0.8rem;
        color: #666;
        margin-bottom: 0.5rem;
    }

    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .status-badge.active {
        background: #dcfce7;
        color: #16a34a;
    }

    .status-badge.inactive {
        background: #fee2e2;
        color: #dc2626;
    }

    /* Info Card */
    .info-card {
        background: #fff;
        border-radius: 14px;
        margin-bottom: 0.75rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .info-card-header {
        padding: 0.6rem 0.875rem;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.8rem;
        font-weight: 600;
        color: #333;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        background: #fafafa;
    }

    .info-card-header i {
        font-size: 0.9rem;
    }

    .info-card-body {
        padding: 0.5rem 0.875rem;
    }

    /* Info Row */
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f5f5f5;
        gap: 0.5rem;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-size: 0.75rem;
        color: #888;
        display: flex;
        align-items: center;
        gap: 0.35rem;
        flex-shrink: 0;
        min-width: 90px;
    }

    .info-label i {
        font-size: 0.8rem;
        color: var(--primary);
        width: 14px;
        text-align: center;
    }

    .info-value {
        font-size: 0.8rem;
        font-weight: 500;
        color: #333;
        text-align: right;
        word-break: break-word;
        flex: 1;
        min-width: 0;
    }

    /* Buttons */
    .btn-back {
        width: 100%;
        padding: 0.8rem;
        background: #f3f4f6;
        color: #333;
        border: none;
        border-radius: 12px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        margin-top: 0.5rem;
    }

    .btn-back:active {
        background: #e5e7eb;
    }

    /* Responsive */
    @media (max-width: 380px) {
        .content-container {
            padding: 1rem;
        }

        .profile-avatar {
            width: 70px;
            height: 70px;
        }

        .profile-name {
            font-size: 0.95rem;
        }

        .info-label {
            font-size: 0.7rem;
            min-width: 80px;
        }

        .info-value {
            font-size: 0.75rem;
        }

        .info-card-header {
            font-size: 0.75rem;
        }
    }
</style>

<!-- Header -->
<div class="page-header">
    <h5><i class="bi bi-person-badge me-2"></i>Detail Karyawan</h5>
    <small>Informasi lengkap karyawan</small>
</div>

<!-- Content -->
<div class="content-container">

    <!-- Profile Header -->
    <div class="profile-header">
        <?php if (!empty($karyawan['foto'])): ?>
            <img src="<?= base_url('assets/images/karyawan/' . $karyawan['foto']) ?>" class="profile-avatar"
                alt="<?= htmlspecialchars($karyawan['nama_lengkap']) ?>"
                onerror="this.src='<?= base_url('assets/images/karyawan/default.png') ?>'">
        <?php else: ?>
            <img src="<?= base_url('assets/images/karyawan/default.png') ?>" class="profile-avatar" alt="Foto">
        <?php endif; ?>
        <div class="profile-name"><?= htmlspecialchars($karyawan['nama_lengkap'] ?? '-') ?></div>
        <div class="profile-role"><?= htmlspecialchars($karyawan['nama_jabatan'] ?? $karyawan['jabatan'] ?? '-') ?>
        </div>
        <span class="status-badge <?= ($karyawan['status_aktif'] ?? 0) ? 'active' : 'inactive' ?>">
            <?= ($karyawan['status_aktif'] ?? 0) ? 'Aktif' : 'Tidak Aktif' ?>
        </span>
    </div>

    <!-- Admin Quick Actions (Visible to Level 1 & 17 only) -->
    <?php if (in_array((int) $this->session->userdata('level'), [1, 17])): ?>
        <div class="info-card border border-warning">
            <div class="info-card-header bg-warning bg-opacity-10 text-dark">
                <i class="bi bi-shield-lock-fill text-warning"></i> Quick Admin Actions
            </div>
            <div class="info-card-body py-3">
                <div class="d-grid gap-2 text-center">
                    <button class="btn btn-warning btn-sm fw-bold rounded-pill py-2 text-dark shadow-sm"
                        onclick="confirmReset('<?= $karyawan['karyawan_id'] ?>', '<?= $karyawan['nama_lengkap'] ?>')">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> RESET PASSWORD DEFAULT
                    </button>
                    <small class="text-muted" style="font-size: 0.65rem;">
                        Password akan direset menjadi: <strong>erptgm2026</strong>
                    </small>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Personal Info -->
    <div class="info-card">
        <div class="info-card-header">
            <i class="bi bi-person text-primary"></i> Informasi Pribadi
        </div>
        <div class="info-card-body">
            <div class="info-row">
                <span class="info-label"><i class="bi bi-hash"></i> NIK</span>
                <span class="info-value"><?= htmlspecialchars($karyawan['nik'] ?? '-') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="bi bi-gender-ambiguous"></i> Kelamin</span>
                <span
                    class="info-value"><?= ($karyawan['jenis_kelamin'] ?? '') == 'L' ? 'Laki-laki' : 'Perempuan' ?></span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="bi bi-geo-alt"></i> TTL</span>
                <span class="info-value">
                    <?= htmlspecialchars($karyawan['tempat_lahir'] ?? '-') ?>,
                    <?= !empty($karyawan['tanggal_lahir']) ? date('d/m/Y', strtotime($karyawan['tanggal_lahir'])) : '-' ?>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="bi bi-heart"></i> Status</span>
                <span class="info-value"><?= htmlspecialchars($karyawan['status_pernikahan'] ?? '-') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="bi bi-book"></i> Pendidikan</span>
                <span class="info-value"><?= htmlspecialchars($karyawan['pendidikan'] ?? '-') ?></span>
            </div>
        </div>
    </div>

    <!-- Contact Info -->
    <div class="info-card">
        <div class="info-card-header">
            <i class="bi bi-telephone text-success"></i> Kontak
        </div>
        <div class="info-card-body">
            <div class="info-row">
                <span class="info-label"><i class="bi bi-envelope"></i> Email</span>
                <span class="info-value"><?= htmlspecialchars($karyawan['email'] ?? '-') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="bi bi-whatsapp"></i> No. HP</span>
                <span
                    class="info-value"><?= htmlspecialchars($karyawan['telepon'] ?? $karyawan['no_hp'] ?? '-') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="bi bi-house"></i> Alamat</span>
                <span class="info-value"><?= htmlspecialchars($karyawan['alamat'] ?? '-') ?></span>
            </div>
        </div>
    </div>

    <!-- Job Info -->
    <div class="info-card">
        <div class="info-card-header">
            <i class="bi bi-briefcase text-warning"></i> Pekerjaan
        </div>
        <div class="info-card-body">
            <div class="info-row">
                <span class="info-label"><i class="bi bi-building"></i> Divisi</span>
                <span
                    class="info-value"><?= htmlspecialchars($karyawan['divisi_nama'] ?? $karyawan['nama_divisi'] ?? $karyawan['divisi'] ?? '-') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="bi bi-award"></i> Jabatan</span>
                <span
                    class="info-value"><?= htmlspecialchars($karyawan['nama_jabatan'] ?? $karyawan['jabatan'] ?? '-') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="bi bi-calendar-check"></i> Tgl Masuk</span>
                <span
                    class="info-value"><?= !empty($karyawan['tanggal_masuk']) ? date('d/m/Y', strtotime($karyawan['tanggal_masuk'])) : '-' ?></span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="bi bi-file-text"></i> Status</span>
                <span class="info-value"><?= htmlspecialchars($karyawan['status_kerja'] ?? '-') ?></span>
            </div>
        </div>
    </div>

    <!-- Bank Info -->
    <?php if (!empty($karyawan['nama_bank']) || !empty($karyawan['no_rekening'])): ?>
        <div class="info-card">
            <div class="info-card-header">
                <i class="bi bi-bank text-info"></i> Bank
            </div>
            <div class="info-card-body">
                <div class="info-row">
                    <span class="info-label"><i class="bi bi-bank2"></i> Bank</span>
                    <span class="info-value"><?= htmlspecialchars($karyawan['nama_bank'] ?? '-') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="bi bi-credit-card"></i> No. Rek</span>
                    <span class="info-value"><?= htmlspecialchars($karyawan['no_rekening'] ?? '-') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="bi bi-person-check"></i> A/N</span>
                    <span
                        class="info-value"><?= htmlspecialchars($karyawan['atas_nama'] ?? $karyawan['nama_lengkap'] ?? '-') ?></span>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Back Button -->
    <a href="<?= base_url('mobile/sdm') ?>" class="btn-back mb-4">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmReset(id, name) {
        Swal.fire({
            title: 'Reset Password?',
            text: "Password " + name + " akan direset ke default (erptgm2026). Tetap lanjukan?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Reset Sekarang',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('mobile/action_reset_password/') ?>" + id;
            }
        })
    }
</script>