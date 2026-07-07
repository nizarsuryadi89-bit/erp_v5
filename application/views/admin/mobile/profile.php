<!-- Global 2025 Design Tokens -->
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.85);
        --glass-border: rgba(255, 255, 255, 0.4);
        --card-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        --accent-gradient: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
        --danger-gradient: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
        --neutral-gradient: linear-gradient(135deg, #64748b 0%, #475569 100%);
    }

    body {
        background-color: #f1f5f9;
        color: #1e293b;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    /* Sticky Glass Header */
    .premium-header {
        position: sticky;
        top: 0;
        z-index: 1000;
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-bottom: 1px solid var(--glass-border);
        padding: 1.25rem 1rem;
        margin-bottom: 1.5rem;
    }

    /* Bento Card System */
    .bento-card {
        background: white;
        border-radius: 24px;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: var(--card-shadow);
    }

    /* Floating Label Pattern */
    .form-floating-custom {
        position: relative;
        margin-bottom: 1.25rem;
    }

    .form-floating-custom input,
    .form-floating-custom textarea {
        width: 100%;
        padding: 1.25rem 1rem 0.5rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 16px;
        background: #f8fafc;
        font-size: 0.95rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .form-floating-custom label {
        position: absolute;
        left: 1rem;
        top: 0.85rem;
        font-size: 0.85rem;
        color: #64748b;
        pointer-events: none;
        transition: all 0.2s ease;
    }

    .form-floating-custom input:focus,
    .form-floating-custom input:not(:placeholder-shown) {
        border-color: #0ea5e9;
        background: white;
        outline: none;
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
    }

    .form-floating-custom input:focus~label,
    .form-floating-custom input:not(:placeholder-shown)~label {
        top: 0.35rem;
        font-size: 0.7rem;
        font-weight: 700;
        color: #0ea5e9;
        text-transform: uppercase;
    }

    /* Profile Photo Hub */
    .photo-wrapper {
        position: relative;
        width: 130px;
        height: 130px;
        margin: 0 auto;
    }

    .large-profile-photo {
        width: 100%;
        height: 100%;
        border-radius: 40px;
        /* Squircle style */
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .photo-edit-badge {
        position: absolute;
        bottom: -5px;
        right: -5px;
        background: var(--accent-gradient);
        color: white;
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid white;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
    }

    /* Premium Buttons */
    .btn-2025 {
        border: none;
        border-radius: 18px;
        padding: 1.1rem;
        font-weight: 700;
        font-size: 0.95rem;
        letter-spacing: 0.5px;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        width: 100%;
        color: white;
    }

    .btn-2025:active {
        transform: scale(0.96);
    }

    .btn-primary-2025 {
        background: var(--accent-gradient);
    }

    .btn-secondary-2025 {
        background: var(--neutral-gradient);
    }

    .btn-danger-2025 {
        background: var(--danger-gradient);
    }

    .section-tag {
        font-size: 0.7rem;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-tag::after {
        content: "";
        height: 1px;
        flex-grow: 1;
        background: #e2e8f0;
    }

    /* Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-up {
        animation: fadeInUp 0.5s ease-out forwards;
    }
</style>

<div class="premium-header">
    <div class="d-flex align-items-center">
        <a href="<?= base_url('mobile') ?>" class="text-dark me-3"><i class="bi bi-chevron-left fs-4"></i></a>
        <div>
            <h5 class="mb-0 fw-bold">Profil Akun</h5>
            <small class="text-muted">Kelola data profesional Anda</small>
        </div>
    </div>
</div>

<div class="px-3 pb-5">
    <form method="post" action="<?= base_url('mobile/aksi_edit_profile') ?>" enctype="multipart/form-data"
        id="profileForm">
        <input type="hidden" name="karyawan_id" value="<?= htmlspecialchars($profile['karyawan_id']) ?>">

        <!-- Photo Section -->
        <div class="text-center mb-4 animate-up" style="animation-delay: 0.1s">
            <div class="photo-wrapper">
                <?php if (!empty($profile['foto']) && file_exists('assets/images/karyawan/' . $profile['foto'])): ?>
                    <img src="<?= base_url('assets/images/karyawan/' . $profile['foto']) ?>" class="large-profile-photo"
                        id="previewFoto">
                <?php else: ?>
                    <img src="<?= base_url('assets/images/karyawan/default.png') ?>" class="large-profile-photo" id="previewFoto" onerror="this.onerror=null; this.src='<?= base_url('assets/images/karyawan/default.png') ?>'">
                <?php endif; ?>

                <label for="fotoInput" class="photo-edit-badge">
                    <i class="bi bi-camera-fill"></i>
                </label>
                <input type="file" name="foto" id="fotoInput" accept="image/*" class="d-none">
            </div>
            <h5 class="mt-3 fw-bold mb-0"><?= $profile['nama_lengkap'] ?></h5>
            <span class="badge rounded-pill bg-info bg-opacity-10 text-info px-3 py-2 mt-2">
                <?= $profile['nama_jabatan'] ?? 'Team Member' ?>
            </span>
        </div>

        <!-- Account Section -->
        <div class="bento-card animate-up" style="animation-delay: 0.2s">
            <div class="section-tag">Informasi Kontak</div>

            <div class="form-floating-custom">
                <input type="text" name="nama_lengkap" placeholder=" "
                    value="<?= htmlspecialchars($profile['nama_lengkap']) ?>" required>
                <label>Nama Lengkap</label>
            </div>

            <div class="form-floating-custom">
                <input type="email" name="email" placeholder=" " value="<?= htmlspecialchars($profile['email']) ?>"
                    required>
                <label>Email Perusahaan</label>
            </div>

            <div class="form-floating-custom">
                <input type="tel" name="telepon" placeholder=" " value="<?= htmlspecialchars($profile['telepon']) ?>">
                <label>WhatsApp / No. HP</label>
            </div>
        </div>

        <!-- Details Section -->
        <div class="bento-card animate-up" style="animation-delay: 0.3s">
            <div class="section-tag">Detail Personal</div>

            <div class="form-floating-custom">
                <textarea name="alamat" rows="2" placeholder=" "><?= htmlspecialchars($profile['alamat']) ?></textarea>
                <label>Alamat Domisili</label>
            </div>

            <div class="row g-2">
                <div class="col-6">
                    <div class="form-floating-custom">
                        <input type="text" name="tempat_lahir" placeholder=" "
                            value="<?= htmlspecialchars($profile['tempat_lahir']) ?>">
                        <label>Kota Lahir</label>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-floating-custom">
                        <input type="date" name="tanggal_lahir" placeholder=" "
                            value="<?= htmlspecialchars($profile['tanggal_lahir']) ?>">
                        <label>Tgl Lahir</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Section -->
        <div class="animate-up" style="animation-delay: 0.4s">
            <button type="submit" class="btn-2025 btn-primary-2025 shadow-lg mb-4">
                <i class="bi bi-check-circle-fill"></i> SIMPAN PERUBAHAN
            </button>

            <!-- Settings Tiles Pattern (Modern 2025 Standard) -->
            <div class="section-tag">Pengaturan & Keamanan</div>

            <div class="bento-card p-0 overflow-hidden mb-5">
                <!-- Ganti Password Tile -->
                <a href="<?= base_url('mobile/reset_pass') ?>" class="d-flex align-items-center p-3 text-decoration-none border-bottom" style="transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                    <div class="bg-secondary bg-opacity-10 text-secondary rounded-3 p-2 me-3" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-key-fill fs-5"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">Ubah Password</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Perbarui keamanan akun Anda</div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>

                <!-- Logout Tile -->
                <a href="javascript:void(0)" onclick="confirmLogout()" class="d-flex align-items-center p-3 text-decoration-none" style="transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-2 me-3" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-box-arrow-right fs-5"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold text-danger" style="font-size: 0.95rem;">Keluar Aplikasi</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Selesaikan sesi kerja ini</div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<script>
    // Preview photo before upload & Validation
    document.getElementById('fotoInput').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            // Updated limit to 10MB to match server
            if (file.size > 10 * 1024 * 1024) {
                Swal.fire({
                    title: 'Ukuran Terlalu Besar',
                    text: 'Maksimal ukuran foto adalah 10MB',
                    icon: 'warning',
                    confirmButtonColor: '#0ea5e9',
                    borderRadius: '20px'
                });
                this.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function (e) {
                const previewFoto = document.getElementById('previewFoto');
                previewFoto.style.opacity = '0';
                setTimeout(() => {
                    previewFoto.src = e.target.result;
                    previewFoto.style.opacity = '1';
                }, 200);
            }
            reader.readAsDataURL(file);
        }
    });

    // Smart Form Submission
    const profileForm = document.getElementById('profileForm');
    profileForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Update Profil?',
            text: 'Pastikan data yang Anda masukkan sudah benar.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0ea5e9',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Update',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            borderRadius: '24px'
        }).then((result) => {
            if (result.isConfirmed) {
                const btn = form.querySelector('button[type="submit"]');

                // Active loading state
                btn.disabled = true;
                btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> MEMPROSES...`;

                // Use standard form submission to ensure all multipart data is sent correctly
                form.submit();
            }
        });
    });
</script>