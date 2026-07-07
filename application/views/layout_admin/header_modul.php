<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('assets/css/tgm-header-modul.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/premium-erp.css') ?>">

<div class="master-header">
    <div class="header-flex">
        <div class="brand-section">
            <div class="brand-icon" style="background: transparent; box-shadow: none;">
                <img src="<?= base_url('assets/images/app_icon.png') ?>" alt="Icon"
                    style="width: 100%; height: 100%; object-fit: contain; mix-blend-mode: normal;">
            </div>
            <div class="brand-text">
                <h1><?= $title ?></h1>
                <p><?= isset($perusahaan) ? $perusahaan : 'TGM Core - Smart Printing ERP' ?></p>
            </div>
        </div>

        <div class="nav-glass">
            <?php $url_kembali = isset($back_url) ? $back_url : 'javascript:history.back()'; ?>
            <a href="<?= $url_kembali ?>" class="glass-item btn-back-glass">
                <i class="fas fa-arrow-left glass-icon"></i>
                <div class="glass-content">
                    <div class="glass-value">Kembali</div>
                </div>
            </a>

            <?php if (isset($is_preview) && $is_preview): ?>
                <a href="<?= site_url('dashboard') ?>" class="glass-item"
                    style="background: rgba(56, 189, 248, 0.2) !important; border: 1px solid var(--dev-accent) !important;">
                    <i class="fas fa-tools glass-icon text-info"></i>
                    <div class="glass-content">
                        <div class="glass-label">Portal</div>
                        <div class="glass-value text-info">Developer</div>
                    </div>
                </a>
            <?php endif; ?>



            <div class="glass-item d-none d-md-flex">
                <i class="fas fa-calendar-alt glass-icon text-info"></i>
                <div class="glass-content">
                    <div class="glass-label">Today</div>
                    <div class="glass-value"><?= date('d M Y') ?></div>
                </div>
            </div>

            <div class="glass-item">
                <i class="fas fa-clock glass-icon text-success"></i>
                <div class="glass-content">
                    <div class="glass-label">Real-time</div>
                    <div class="glass-value" id="header-clock"><?= date('H:i') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateHeaderClock() {
        const now = new Date();
        const timeStr = now.getHours().toString().padStart(2, '0') + ':' +
            now.getMinutes().toString().padStart(2, '0');
        const clockEl = document.getElementById('header-clock');
        if (clockEl) clockEl.innerText = timeStr;
    }
    setInterval(updateHeaderClock, 30000);
</script>