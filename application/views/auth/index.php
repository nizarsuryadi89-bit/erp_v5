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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#ffffff">
    <title>Login | <?= get_app_name() ?></title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <link rel="stylesheet" href="<?= base_url('assets/css/tgm-auth.css?v=' . time()) ?>">
</head>

<body>
    <div class="split-layout">
        <!-- LEFT PANEL (Login Form & Layanan Publik) -->
        <div class="split-left animate__animated animate__fadeIn">
            <div class="login-wrapper">
                <div class="login-card">
                    <div class="brand-header animate__animated animate__fadeInDown animate__delay-1s">
                        <div class="brand-logo-box">
                            <i class="bi bi-layers-half"></i>
                        </div>
                        <div>
                            <h1 class="brand-title"><?= get_app_name() ?></h1>
                            <p class="brand-subtitle">Integrated Management Core</p>
                        </div>
                    </div>

                    <div class="greeting-area mb-4">
                        <h2 class="greeting-title">Welcome back!</h2>
                        <p class="greeting-subtitle">
                            <i class="bi <?= $icon_waktu ?> text-accent"></i> Selamat <?= $waktu ?>, silakan masuk untuk melanjutkan.
                        </p>
                    </div>

                    <!-- Notification/Alerts -->
                    <?php if ($this->session->flashdata('error')): ?>
                        <div class="alert-custom alert-error animate__animated animate__headShake">
                            <i class="bi bi-exclamation-octagon-fill"></i>
                            <span><?= $this->session->flashdata('error') ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->session->flashdata('success')): ?>
                        <div class="alert-custom alert-success animate__animated animate__fadeIn">
                            <i class="bi bi-check-circle-fill"></i>
                            <span><?= $this->session->flashdata('success') ?></span>
                        </div>
                    <?php endif; ?>

                    <!-- Login Form -->
                    <form action="<?= base_url('auth/proses_login') ?>" method="post" class="login-form animate__animated animate__fadeInUp animate__delay-1s">
                        <div class="form-floating-custom">
                            <label>Username</label>
                            <div class="input-group-custom">
                                <i class="bi bi-person main-icon"></i>
                                <input type="text" name="username" class="form-input-premium" placeholder="Masukkan username" required autocomplete="username">
                                <div class="input-border"></div>
                            </div>
                        </div>

                        <div class="form-floating-custom">
                            <label>Password</label>
                            <div class="input-group-custom">
                                <i class="bi bi-lock main-icon"></i>
                                <input type="password" name="password" id="password" class="form-input-premium" placeholder="Masukkan password" required autocomplete="current-password">
                                <button type="button" class="password-toggle" onclick="togglePassword()">
                                    <i class="bi bi-eye" id="toggleIcon"></i>
                                </button>
                                <div class="input-border"></div>
                            </div>
                        </div>

                        <div class="flex-between">
                            <label class="remember-me">
                                <input type="checkbox" name="rememberme">
                                <span class="checkmark"></span>
                                <span class="label-text">Remember me</span>
                            </label>
                            <a href="#" class="forgot-link">Lupa sandi?</a>
                        </div>

                        <button type="submit" class="btn-submit">
                            <span>Sign in to your account</span>
                            <i class="bi bi-arrow-right"></i>
                        </button>

                        <div class="owner-login">
                            <span>Owner portal access?</span>
                            <a href="<?= base_url('owner') ?>">Login Here</a>
                        </div>
                    </form>
                </div>

                <!-- Layanan Publik (Bottom of Left Panel) -->
                <div class="layanan-publik-container animate__animated animate__fadeInUp animate__delay-2s">
                    <h4 class="layanan-title">
                        <span>Layanan Publik</span>
                    </h4>
                    <div class="key-modules">
                        <a href="<?= base_url('katalog') ?>" class="module-btn">
                            <div class="module-icon"><i class="bi bi-shop"></i></div>
                            <span>Katalog</span>
                        </a>
                        <a href="<?= base_url('tracking') ?>" class="module-btn">
                            <div class="module-icon"><i class="bi bi-search"></i></div>
                            <span>Tracking</span>
                        </a>
                        <a href="<?= base_url('auth/daftar') ?>" class="module-btn">
                            <div class="module-icon"><i class="bi bi-person-plus"></i></div>
                            <span>Register</span>
                        </a>
                        <a href="<?= base_url('kiosk') ?>" class="module-btn">
                            <div class="module-icon"><i class="bi bi-display"></i></div>
                            <span>KIOSK</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL (Dynamic Image & Branding) -->
        <div class="split-right animate__animated animate__fadeInRight">
            <div class="split-right-image" style="background-image: url('<?= base_url('assets/images/' . get_login_image()) ?>');"></div>
            <!-- Subtly shaded overlay just to make the text pop -->
            <div class="split-right-overlay"></div>
            
            <div class="split-right-content animate__animated animate__fadeInUp animate__delay-1s">
                <div class="glass-quote">
                    <i class="bi bi-quote quote-icon"></i>
                    <h2 class="right-title">Building meaningful experiences together.</h2>
                    <p class="right-subtitle">ERP Core System &bull; Next Generation Printing Management</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Toggle Script -->
    <script>
        function togglePassword() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            const wrapper = icon.closest('.password-toggle');
            
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
                wrapper.classList.add('active');
            } else {
                pwd.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
                wrapper.classList.remove('active');
            }
        }
        
        // Add focus states to input groups
        document.querySelectorAll('.form-input-premium').forEach(input => {
            input.addEventListener('focus', function() {
                this.closest('.input-group-custom').classList.add('focused');
            });
            input.addEventListener('blur', function() {
                if(this.value === '') {
                    this.closest('.input-group-custom').classList.remove('focused');
                }
            });
        });
    </script>
</body>
</html>
