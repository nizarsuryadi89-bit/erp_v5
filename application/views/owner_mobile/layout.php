<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title><?= $title ?? 'Owner Dashboard' ?></title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        :root {
            --primary: #1e3a8a;
            --primary-dark: #172554;
            --success: #10B981;
            --danger: #EF4444;
            --warning: #F59E0B;
            --dark: #111827;
            --gray-100: #F3F4F6;
            --gray-200: #E5E7EB;
            --gray-500: #6B7280;
            --gray-800: #1F2937;
            --bg-color: #F9FAFB;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--gray-800);
            padding-bottom: 80px; /* Space for bottom nav */
            -webkit-font-smoothing: antialiased;
        }

        /* ===== Watermark ===== */
        .watermark-bg {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70%;
            max-width: 400px;
            opacity: 0.05;
            pointer-events: none;
            z-index: 0;
            filter: grayscale(100%);
        }

        /* ===== Header ===== */
        .app-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 1.5rem 1rem 3rem 1rem;
            border-bottom-left-radius: 24px;
            border-bottom-right-radius: 24px;
            position: relative;
            margin-bottom: -2rem;
            box-shadow: 0 4px 20px rgba(79, 70, 229, 0.2);
        }

        .header-content-wrapper {
            max-width: 1024px;
            margin: 0 auto;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .company-selector select {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 0.4rem 2rem 0.4rem 0.8rem;
            font-size: 0.85rem;
            font-weight: 600;
            outline: none;
            backdrop-filter: blur(10px);
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23ffffff%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E');
            background-repeat: no-repeat;
            background-position: right 0.7rem top 50%;
            background-size: 0.65rem auto;
            cursor: pointer;
        }
        .company-selector select option {
            color: var(--dark);
            background-color: white;
        }

        .header-title {
            font-size: 1.5rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .header-subtitle {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.8);
            margin: 0;
        }

        /* ===== Main Container ===== */
        .app-container {
            padding: 0 1rem;
            max-width: 1024px;
            margin: 0 auto;
        }

        /* ===== Cards ===== */
        .summary-card {
            background: white;
            border-radius: 20px;
            padding: 1.25rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            margin-bottom: 1rem;
            border: 1px solid rgba(0,0,0,0.02);
            position: relative;
            overflow: hidden;
        }

        .summary-card.main-card {
            background: linear-gradient(135deg, #ffffff, #f8fafc);
            border: 1px solid #e2e8f0;
            padding: 1.5rem;
        }

        .card-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .icon-blue { background: #EEF2FF; color: var(--primary); }
        .icon-green { background: #ECFDF5; color: var(--success); }
        .icon-red { background: #FEF2F2; color: var(--danger); }
        .icon-orange { background: #FFFBEB; color: var(--warning); }

        .card-label {
            font-size: 0.8rem;
            color: var(--gray-500);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .card-value {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--dark);
            margin: 0;
            letter-spacing: -0.5px;
        }
        
        .card-value.small {
            font-size: 1.25rem;
        }

        .card-subtext {
            font-size: 0.75rem;
            color: var(--gray-500);
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* ===== Grid Layouts ===== */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        /* ===== Section Title ===== */
        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--dark);
            margin: 1.5rem 0 1rem 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ===== Action Menu ===== */
        .action-menu {
            background: white;
            border-radius: 20px;
            padding: 0.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.02);
            margin-bottom: 1.5rem;
        }

        .action-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid var(--gray-100);
            text-decoration: none !important;
            color: var(--dark);
        }
        .action-item:last-child {
            border-bottom: none;
        }
        .action-item:hover, .action-item:active {
            background: var(--gray-100);
            border-radius: 14px;
        }
        
        .action-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: var(--gray-100);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            margin-right: 1rem;
            font-size: 1.1rem;
        }

        .action-text {
            flex-grow: 1;
        }
        .action-title {
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 2px;
        }
        .action-desc {
            font-size: 0.75rem;
            color: var(--gray-500);
        }

        /* ===== Bottom Nav ===== */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-around;
            padding: 0.75rem 0;
            padding-bottom: calc(0.75rem + env(safe-area-inset-bottom)); /* iOS safe area */
            z-index: 1000;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: var(--gray-500);
            text-decoration: none !important;
            font-size: 0.7rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .nav-item.active {
            color: var(--primary);
        }
        
        .nav-item i {
            font-size: 1.25rem;
            margin-bottom: 4px;
        }
        
        .nav-item:hover, .nav-item:active {
            color: var(--primary);
            transform: scale(1.05);
        }

        /* Utilities */
        .text-success { color: var(--success) !important; }
        .text-danger { color: var(--danger) !important; }
    </style>
</head>
<body>

<!-- Elegant Glassmorphic Loading Overlay -->
<div id="pageLoaderOverlay" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(249, 250, 251, 0.7); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 9999; opacity: 0; pointer-events: none; transition: opacity 0.25s ease-in-out;">
    <div style="text-align: center;">
        <div class="spinner-border" role="status" style="width: 2.5rem; height: 2.5rem; border-width: 0.22em; color: #1e3a8a !important;">
            <span class="sr-only">Memuat...</span>
        </div>
        <div style="margin-top: 12px; font-size: 0.8rem; font-weight: 700; color: #1e293b; letter-spacing: 0.5px; text-transform: uppercase;">Memuat Cabang...</div>
    </div>
</div>

<!-- Watermark Logo -->
<img src="<?= base_url('assets/images/logo.png') ?>" class="watermark-bg" alt="Watermark">

<?php
$is_home = ($title == 'Pusat Laporan Keuangan' || $title == 'Dashboard Owner');
?>
<div class="app-header">
    <div class="header-content-wrapper">
        <div class="header-top" style="position: relative;">
            <?php
            // Jangan tampilkan tombol back di halaman home/dashboard
            $is_home = ($title == 'Dashboard Owner' || $title == 'Pusat Laporan Keuangan');
            if (!$is_home): 
            ?>
                <a href="javascript:history.back()" class="btn btn-sm btn-light" style="border-radius:12px; font-weight:600; padding: 0.3rem 0.8rem; position: relative; z-index: 2;">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            <?php else: ?>
                <!-- Placeholder untuk balance layout jika home -->
                <div style="width: 80px;"></div>
            <?php endif; ?>

            <!-- Middle Logo -->
            <div style="position: absolute; left: 50%; transform: translateX(-50%); z-index: 1;">
                <img src="<?= base_url('assets/images/logo.png') ?>" alt="TGM Logo" style="height: 34px; border-radius: 6px; background: white; padding: 2px;">
            </div>

            <div class="company-selector" style="position: relative; z-index: 2;">
                <select id="branchSelector">
                    <?php foreach($all_perusahaan as $p): ?>
                        <option value="<?= $p->id_perusahaan ?>" <?= $perusahaan_id == $p->id_perusahaan ? 'selected' : '' ?>>
                            <?= $p->id_perusahaan == 1 ? $p->nama_perusahaan . ' (Semua Cabang)' : $p->nama_perusahaan ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="header-title"><?= $title ?? 'Laporan' ?></div>
        <div class="header-subtitle"><?= is_object($perusahaan) ? $perusahaan->nama_perusahaan : (is_string($perusahaan) ? $perusahaan : 'Ringkasan Keuangan Terkini') ?></div>
    </div>
</div>

<script>
    function triggerLoading() {
        const overlay = document.getElementById('pageLoaderOverlay');
        const container = document.querySelector('.app-container');
        if (overlay) {
            overlay.style.opacity = '1';
            overlay.style.pointerEvents = 'auto';
        }
        if (container) {
            container.style.opacity = '0.3';
            container.style.transition = 'opacity 0.25s ease';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const selector = document.getElementById('branchSelector');
        if (selector) {
            selector.addEventListener('change', function() {
                triggerLoading();
                const url = new URL(window.location.href);
                url.searchParams.set('perusahaan_id', this.value);
                setTimeout(function() {
                    window.location.href = url.toString();
                }, 50); // small delay to let transition start smoothly
            });
        }
    });

    function changeCabang(val) {
        triggerLoading();
        const url = new URL(window.location.href);
        url.searchParams.set('perusahaan_id', val);
        setTimeout(function() {
            window.location.href = url.toString();
        }, 50);
    }
</script>
