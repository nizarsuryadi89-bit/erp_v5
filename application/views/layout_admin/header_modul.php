<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('assets/css/premium-erp.css') ?>">
<?php /* <link rel="stylesheet" href="<?= base_url('assets/css/tgm-header-modul.css') ?>"> (Dinonaktifkan agar lebih ringan) */ ?>

<div class="d-flex align-items-center justify-content-between bg-white px-4 py-2 border-bottom mb-4 shadow-sm" style="margin-left: -15px; margin-right: -15px; margin-top: 0;">
    <!-- Left: Breadcrumb & Title -->
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 0.70rem; font-weight: 500;">
                <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>" class="text-decoration-none text-muted"><i class="fas fa-home"></i> Home</a></li>
                <?php if (isset($breadcrumb)): ?>
                    <li class="breadcrumb-item text-muted"><?= $breadcrumb ?></li>
                <?php endif; ?>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page"><?= isset($title) ? $title : 'Modul' ?></li>
            </ol>
        </nav>
        <h5 class="mb-0 fw-bold text-dark mt-1" style="font-size: 1.1rem; letter-spacing: -0.3px; font-family: 'Outfit', sans-serif;">
            <?= isset($title) ? $title : 'Dashboard' ?>
        </h5>
    </div>

    <!-- Right: Actions & Info -->
    <div class="d-flex align-items-center gap-2">
        <?php $url_kembali = isset($back_url) ? $back_url : 'javascript:history.back()'; ?>
        <a href="<?= $url_kembali ?>" class="btn btn-light btn-sm fw-bold shadow-sm border text-dark d-flex align-items-center justify-content-center" style="border-radius: 8px; height: 34px;">
            <i class="fas fa-arrow-left me-md-2"></i> <span class="d-none d-md-inline">Kembali</span>
        </a>

        <?php if (isset($is_preview) && $is_preview): ?>
            <a href="<?= site_url('dashboard') ?>" class="btn btn-info btn-sm fw-bold text-white shadow-sm d-flex align-items-center justify-content-center" style="border-radius: 8px; height: 34px;">
                <i class="fas fa-tools me-md-2"></i> <span class="d-none d-md-inline">Dev Portal</span>
            </a>
        <?php endif; ?>

        <div class="bg-light border rounded-3 px-3 py-1 d-none d-lg-flex flex-column align-items-end justify-content-center shadow-sm" style="height: 34px; min-width: 90px;">
            <div class="text-dark fw-bold" style="font-size: 0.8rem; line-height: 1;">
                <i class="far fa-clock text-success me-1"></i> <span id="header-clock"><?= date('H:i') ?></span>
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