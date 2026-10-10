<!-- Global 2025 Design Tokens - Shared with Profile -->
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.85);
        --glass-border: rgba(255, 255, 255, 0.4);
        --card-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        --accent-gradient: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
        --danger-gradient: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
        --neutral-gradient: linear-gradient(135deg, #64748b 0%, #475569 100%);
        --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
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

    /* Floating Label Pattern Overlay */
    .form-floating-custom {
        position: relative;
        margin-bottom: 1.25rem;
    }

    .form-floating-custom input {
        width: 100%;
        padding: 1.25rem 3rem 0.5rem 1rem;
        /* Extra right padding for eye icon */
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

    .toggle-pass-2025 {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        cursor: pointer;
        font-size: 1.2rem;
        padding: 5px;
        z-index: 10;
        transition: color 0.2s;
    }

    .toggle-pass-2025:hover {
        color: #0ea5e9;
    }

    /* Avatar Squircle */
    .squircle-avatar {
        width: 100px;
        height: 100px;
        border-radius: 32px;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    /* Premium Buttons */
    .btn-2025 {
        border: none;
        border-radius: 18px;
        padding: 1.1rem;
        font-weight: 700;
        font-size: 0.95rem;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        width: 100%;
        color: white;
        text-decoration: none;
    }

    .btn-2025:active {
        transform: scale(0.96);
    }

    .btn-success-2025 {
        background: var(--success-gradient);
    }

    .btn-neutral-2025 {
        background: var(--neutral-gradient);
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
        <a href="<?= base_url('mobile/profile') ?>" class="text-dark me-3"><i class="bi bi-chevron-left fs-4"></i></a>
        <div>
            <h5 class="mb-0 fw-bold">Ubah Password</h5>
            <small class="text-muted">Keamanan Akun Karyawan</small>
        </div>
    </div>
</div>

<div class="px-3 pb-5">
    <!-- Profile Mini Info -->
    <div class="text-center mb-4 animate-up" style="animation-delay: 0.1s">
        <img src="<?= base_url('assets/images/karyawan/' . ($karyawan_terpilih['foto'] ?? 'default.png')) ?>" onerror="this.onerror=null; this.src='<?= base_url('assets/images/karyawan/default.png') ?>'" class="squircle-avatar" alt="Profile">
        <h5 class="mt-3 fw-bold mb-0"><?= $karyawan_terpilih['nama_lengkap'] ?></h5>
        <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2 mt-2">
            <i class="bi bi-shield-lock-fill me-1"></i> Account Protection Active
        </span>
    </div>

    <!-- Alert Messages (Same as Profile) -->
    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 animate-up" role="alert">
            <i class="bi bi-exclamation-octagon-fill me-2"></i><?= $this->session->flashdata('error') ?>
        </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 animate-up" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= $this->session->flashdata('success') ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('profile/ganti_password') ?>" id="formReset">
        <div class="bento-card animate-up" style="animation-delay: 0.2s">
            <div class="section-tag">Verifikasi Identitas</div>

            <div class="form-floating-custom">
                <input type="password" name="password_lama" id="pass_old" placeholder=" " required>
                <label>Password Saat Ini</label>
                <i class="bi bi-eye toggle-pass-2025" onclick="toggleView('pass_old', this)"></i>
            </div>
        </div>

        <div class="bento-card animate-up" style="animation-delay: 0.3s">
            <div class="section-tag">Kredensial Baru</div>

            <div class="form-floating-custom">
                <input type="password" name="password_baru" id="pass_new" placeholder=" " minlength="6" required>
                <label>Password Baru</label>
                <i class="bi bi-eye toggle-pass-2025" onclick="toggleView('pass_new', this)"></i>
            </div>

            <div class="form-floating-custom mb-2">
                <input type="password" name="konfirmasi" id="pass_conf" placeholder=" " required>
                <label>Konfirmasi Password</label>
                <i class="bi bi-eye toggle-pass-2025" onclick="toggleView('pass_conf', this)"></i>
            </div>

            <small class="text-muted d-block mt-2">
                <i class="bi bi-info-circle me-1"></i> Gunakan minimal 6 karakter unik.
            </small>
        </div>

        <div class="animate-up" style="animation-delay: 0.4s">
            <button type="submit" class="btn-2025 btn-success-2025 shadow-lg mb-3">
                <i class="bi bi-save2-fill"></i> UPDATE PASSWORD
            </button>


        </div>
    </form>
</div>

<script>
    function toggleView(id, el) {
        const input = document.getElementById(id);
        if (input.type === 'password') {
            input.type = 'text';
            el.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            el.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }

    document.getElementById('formReset').addEventListener('submit', function (e) {
        const p1 = document.getElementById('pass_new').value;
        const p2 = document.getElementById('pass_conf').value;

        if (p1 !== p2) {
            e.preventDefault();
            Swal.fire({
                title: 'Password Tidak Cocok',
                text: 'Konfirmasi password harus sama dengan password baru.',
                icon: 'error',
                confirmButtonColor: '#f43f5e',
                borderRadius: '24px'
            });
            return;
        }

        // Show loading state
        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> MEMPERBARUI...`;
    });
</script>