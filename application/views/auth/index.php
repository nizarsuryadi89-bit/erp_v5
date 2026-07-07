<?php
date_default_timezone_set('Asia/Jakarta');
$waktuSekarang = date("H:i:s");

if ($waktuSekarang >= '04:59:00' && $waktuSekarang <= '11:00:00') {
    $waktu = "Pagi";
    $icon_waktu = "bi-brightness-high";
} elseif ($waktuSekarang >= '11:01:00' && $waktuSekarang <= '16:00:00') {
    $waktu = "Siang";
    $icon_waktu = "bi-sun-fill";
} elseif ($waktuSekarang >= '16:01:00' && $waktuSekarang <= '18:00:00') {
    $waktu = "Sore";
    $icon_waktu = "bi-cloud-sun";
} else {
    $waktu = "Malam";
    $icon_waktu = "bi-moon-stars-fill";
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#0f172a">
    <title>Login | TGM Core</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <link rel="stylesheet" href="<?= base_url('assets/css/tgm-auth.css') ?>">
    <style>
        body {
            background-color: var(--dark);
            background-image: url('<?= base_url("assets/img/login_bg.png") ?>');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        /* Overlay for background */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at center, rgba(15, 23, 42, 0.4) 0%, rgba(15, 23, 42, 0.9) 100%);
            z-index: 1;
        }
    </style>
</head>

<body>
    <div class="login-wrapper animate__animated animate__fadeIn">
        <main class="login-card">
            <!-- Branding -->
            <div class="brand-area">
                <div style="margin-bottom: 1.5rem; display: inline-block;">
                    <img src="<?= base_url('assets/images/app_icon.png') ?>" alt="TGM CORE"
                        style="width: 80px; height: 80px; object-fit: contain; mix-blend-mode: normal;">
                </div>
                <h1 class="brand-name">TGM CORE</h1>
                <p class="brand-motto">Integrated Printing & Management Core</p>
            </div>

            <!-- Greeting -->
            <div class="greeting-badge">
                <i class="bi <?= $icon_waktu ?>"></i>
                Selamat <?= $waktu ?>, 👋
            </div>

            <!-- Notification/Alerts -->
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert-custom alert-error">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span><?= $this->session->flashdata('error') ?></span>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert-custom alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    <span><?= $this->session->flashdata('success') ?></span>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="<?= base_url('auth/proses_login') ?>" method="post">
                <div class="form-floating-custom">
                    <label>Username</label>
                    <div class="input-group-custom">
                        <i class="bi bi-person-fill main-icon"></i>
                        <input type="text" name="username" class="form-input-premium" placeholder="Username anda"
                            required autocomplete="username">
                    </div>
                </div>

                <div class="form-floating-custom">
                    <label>Password</label>
                    <div class="input-group-custom">
                        <i class="bi bi-shield-lock-fill main-icon"></i>
                        <input type="password" name="password" id="password" class="form-input-premium"
                            placeholder="Password anda" required autocomplete="current-password">
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="bi bi-eye-fill" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="flex-between">
                    <label class="remember-me">
                        <input type="checkbox" name="rememberme">
                        <span>Ingat Saya</span>
                    </label>
                </div>

                <button type="submit" class="btn-submit" style="margin-bottom: 1rem;">
                    MASUK SEKARANG
                    <i class="bi bi-arrow-right-short fs-4"></i>
                </button>
                
                <a href="<?= base_url('katalog') ?>" class="btn-submit" style="background: transparent; border: 2px solid rgba(255,255,255,0.2); color: #94a3b8; text-decoration: none; text-align: center; display: flex; align-items: center; justify-content: center; transition: all 0.3s;" onmouseover="this.style.borderColor='rgba(255,255,255,0.5)'; this.style.color='#ffffff';" onmouseout="this.style.borderColor='rgba(255,255,255,0.2)'; this.style.color='#94a3b8';">
                    <i class="bi bi-bag-check-fill me-2" style="margin-right: 8px;"></i> Lihat Katalog Produk
                </a>

                <!-- Link Rahasia ke Dashboard Owner -->
                <a href="<?= base_url('owner') ?>" style="display: block; text-align: center; margin-top: 1.5rem; color: #475569; font-size: 0.85rem; text-decoration: none; transition: color 0.2s; font-weight: 600;" onmouseover="this.style.color='#94a3b8';" onmouseout="this.style.color='#475569';">
                    <i class="bi bi-incognito me-1"></i> Masuk sebagai Owner
                </a>
            </form>

            <div class="login-footer">
                <i class="bi bi-patch-check-fill text-success"></i>
                Sistem Terenkripsi & Aman
            </div>
        </main>
    </div>

    <!-- Password Toggle Script -->
    <script>
        function togglePassword() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
            } else {
                pwd.type = 'password';
                icon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
            }
        }
    </script>
</body>

</html>