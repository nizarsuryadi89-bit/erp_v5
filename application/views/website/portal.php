<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TGM Print | Printing Cepat, Harga Transparan, Tracking Mudah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #eef4ff;
            --paper: #ffffff;
            --paper-soft: #f7faff;
            --ink: #10233f;
            --muted: #5b6f8f;
            --brand: #1f57a6;
            --brand-dark: #12386f;
            --brand-strong: #0c2e5f;
            --accent: #3ea1ff;
            --line: rgba(16, 35, 63, 0.1);
            --shadow: 0 24px 60px rgba(15, 44, 88, 0.1);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: 'Manrope', sans-serif;
            font-size: 15.5px;
            line-height: 1.6;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(62, 161, 255, 0.18), transparent 24%),
                radial-gradient(circle at top right, rgba(31, 87, 166, 0.16), transparent 22%),
                linear-gradient(180deg, #f4f8ff 0%, #edf4ff 48%, #e8f0fd 100%);
        }

        a {
            text-decoration: none;
        }

        .site-shell {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(14px);
            background: rgba(255, 255, 255, 0.92);
            border-bottom: 1px solid rgba(16, 35, 63, 0.08);
        }

        .utility-bar {
            background: linear-gradient(90deg, #11417f, #1c5aa8);
            color: rgba(255, 255, 255, 0.88);
            font-size: 0.82rem;
        }

        .utility-bar a {
            color: rgba(255, 255, 255, 0.92);
        }

        .utility-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .brand-mark {
            width: 48px;
            height: 48px;
            object-fit: contain;
            border-radius: 14px;
            background: #fff;
            padding: 8px;
            box-shadow: 0 8px 20px rgba(16, 35, 63, 0.08);
        }

        .brand-copy small {
            color: var(--muted);
            font-size: 0.68rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .brand-copy strong {
            font-size: 1rem;
            letter-spacing: -0.01em;
        }

        .hero {
            padding: 18px 0 30px;
        }

        .hero-panel,
        .glass-card,
        .feature-card,
        .product-card,
        .tracking-card,
        .cta-band {
            background: rgba(255, 253, 248, 0.82);
            border: 1px solid rgba(255, 255, 255, 0.72);
            box-shadow: var(--shadow);
        }

        .hero-panel {
            border-radius: 32px;
            overflow: hidden;
            position: relative;
            background:
                linear-gradient(90deg, #16599d 0%, #1b6ab7 26%, #3cb4f0 70%, #74d2ff 100%);
        }

        .hero-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 78% 18%, rgba(255,255,255,0.35), transparent 18%),
                radial-gradient(circle at 82% 86%, rgba(255,255,255,0.16), transparent 24%),
                radial-gradient(circle at 20% 20%, rgba(255,255,255,0.18), transparent 28%);
            pointer-events: none;
        }

        .hero-copy {
            padding: 44px;
            position: relative;
            z-index: 1;
            color: #fff;
        }

        .hero-kicker {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 9px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            color: #fff;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .hero h1 {
            font-size: clamp(2.05rem, 4vw, 3.55rem);
            line-height: 1.04;
            letter-spacing: -0.03em;
            margin: 18px 0 14px;
            max-width: 620px;
        }

        .hero p {
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.98rem;
            line-height: 1.72;
            max-width: 560px;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 28px;
        }

        .btn-brand,
        .btn-ghost {
            border-radius: 999px;
            padding: 12px 19px;
            font-size: 0.9rem;
            font-weight: 700;
            border: none;
        }

        .btn-brand {
            background: linear-gradient(135deg, #0f2f61, #194e97);
            color: #fff;
        }

        .btn-ghost {
            background: rgba(255,255,255,0.12);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.24);
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-top: 28px;
        }

        .stat-block {
            padding: 16px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .stat-block strong {
            display: block;
            font-size: 1.4rem;
            letter-spacing: -0.03em;
            color: #fff;
        }

        .stat-block span {
            font-size: 0.88rem;
            color: rgba(255, 255, 255, 0.82);
        }

        .hero-visual {
            min-height: 100%;
            padding: 28px;
            position: relative;
            background:
                radial-gradient(circle at 75% 15%, rgba(255,255,255,0.32), transparent 16%),
                linear-gradient(180deg, rgba(11, 46, 93, 0.08), rgba(37, 119, 189, 0.12));
            color: #fff;
            display: flex;
            align-items: flex-end;
        }

        .visual-stack {
            width: 100%;
            display: grid;
            gap: 14px;
        }

        .glass-card {
            border-radius: 24px;
            padding: 18px 20px;
        }

        .glass-card.dark {
            background: rgba(255, 255, 255, 0.16);
            border-color: rgba(255, 255, 255, 0.22);
            color: #fff;
            box-shadow: none;
        }

        .section {
            padding: 28px 0;
        }

        .section-title {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: end;
            margin-bottom: 20px;
        }

        .section-title h2 {
            margin: 0;
            font-size: clamp(1.45rem, 2.4vw, 2.15rem);
            line-height: 1.18;
            letter-spacing: -0.025em;
        }

        .section-title p {
            margin: 0;
            color: var(--muted);
            font-size: 0.93rem;
            line-height: 1.7;
            max-width: 560px;
        }

        .feature-card,
        .product-card,
        .tracking-card,
        .tool-card {
            border-radius: 28px;
            padding: 21px;
            height: 100%;
            border: 1px solid rgba(23, 32, 51, 0.08);
        }

        .tool-card {
            background: linear-gradient(180deg, rgba(255,255,255,0.96), rgba(247,250,255,0.96));
            box-shadow: var(--shadow);
        }

        .tool-icon,
        .feature-icon {
            width: 50px;
            height: 50px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            font-size: 1.2rem;
            margin-bottom: 14px;
        }

        .tool-card h2,
        .tracking-card h3,
        .product-card h3,
        .feature-card h3 {
            letter-spacing: -0.02em;
        }

        .tool-card h2 {
            font-size: 1.42rem;
            margin-bottom: 10px;
        }

        .nav-link {
            font-size: 0.9rem;
            color: var(--muted);
        }

        .nav-link:hover {
            color: var(--brand-dark);
        }

        .feature-card h3,
        .product-card h3,
        .tracking-card h3 {
            font-size: 1.08rem;
        }

        .tool-card p,
        .feature-card p,
        .product-card p,
        .tracking-card p,
        .glass-card p {
            font-size: 0.93rem;
            line-height: 1.72;
        }

        .tool-icon {
            background: rgba(31, 87, 166, 0.12);
            color: var(--brand-dark);
        }

        .feature-icon {
            background: rgba(62, 161, 255, 0.14);
            color: var(--brand);
        }

        .form-control,
        .form-select {
            border-radius: 18px;
            padding: 12px 15px;
            font-size: 0.94rem;
            border-color: rgba(16, 35, 63, 0.1);
            background: rgba(255, 255, 255, 0.98);
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.25rem rgba(31, 87, 166, 0.12);
            border-color: rgba(31, 87, 166, 0.32);
        }

        .chips {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .chip {
            padding: 7px 12px;
            border-radius: 999px;
            background: #fff;
            border: 1px solid var(--line);
            color: var(--muted);
            font-size: 0.8rem;
        }

        .price-range {
            font-size: 1.34rem;
            font-weight: 800;
            line-height: 1.18;
            letter-spacing: -0.02em;
        }

        .product-sample {
            padding: 12px 14px;
            border-radius: 18px;
            background: rgba(16, 35, 63, 0.04);
            margin-top: 10px;
        }

        .product-sample .fw-semibold {
            font-size: 0.93rem;
        }

        .product-sample .small {
            font-size: 0.82rem;
        }

        .result-anchor {
            scroll-margin-top: 96px;
        }

        .progress-shell {
            height: 12px;
            background: rgba(23, 32, 51, 0.08);
            border-radius: 999px;
            overflow: hidden;
        }

        .progress-shell span {
            display: block;
            height: 100%;
            background: linear-gradient(90deg, var(--brand), var(--accent));
            border-radius: inherit;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 8px 13px;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .status-warning {
            background: rgba(255, 193, 7, 0.15);
            color: #8a6100;
        }

        .status-primary {
            background: rgba(13, 110, 253, 0.12);
            color: #0d58ca;
        }

        .status-success {
            background: rgba(25, 135, 84, 0.12);
            color: #146c43;
        }

        .status-danger {
            background: rgba(220, 53, 69, 0.12);
            color: #b02a37;
        }

        .cta-band {
            border-radius: 32px;
            padding: 24px 26px;
            background: linear-gradient(135deg, #11386e, #1c5ca9);
            color: #fff;
        }

        .cta-band p,
        .cta-band h2 {
            color: #fff;
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
        }

        .category-card {
            display: block;
            padding: 18px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(23, 32, 51, 0.08);
            color: var(--ink);
            box-shadow: var(--shadow);
            transition: transform 0.18s ease, border-color 0.18s ease;
        }

        .category-card:hover,
        .category-card.is-active {
            transform: translateY(-2px);
            border-color: rgba(31, 87, 166, 0.35);
        }

        .category-card strong {
            display: block;
            font-size: 1rem;
            margin-bottom: 6px;
        }

        .category-card span,
        .category-card small {
            color: var(--muted);
            font-size: 0.86rem;
            line-height: 1.65;
        }

        .form-label {
            font-size: 0.9rem;
            margin-bottom: 7px;
        }

        .alert-soft {
            border-radius: 18px;
            padding: 14px 16px;
            border: 1px solid transparent;
            font-size: 0.92rem;
        }

        .alert-soft-success {
            background: rgba(25, 135, 84, 0.08);
            border-color: rgba(25, 135, 84, 0.14);
            color: #146c43;
        }

        .alert-soft-danger {
            background: rgba(220, 53, 69, 0.08);
            border-color: rgba(220, 53, 69, 0.14);
            color: #b02a37;
        }

        .order-summary {
            padding: 16px 18px;
            border-radius: 20px;
            background: rgba(23, 32, 51, 0.04);
        }

        .order-summary strong {
            font-size: 1.2rem;
            letter-spacing: -0.02em;
        }

        .footer-note {
            padding: 28px 0 38px;
            font-size: 0.88rem;
            color: var(--muted);
        }

        .footer-company-grid {
            display: grid;
            grid-template-columns: 1.2fr 1.8fr;
            gap: 18px;
        }

        .branch-list {
            display: grid;
            gap: 14px;
        }

        .branch-card {
            padding: 16px 18px;
            border-radius: 20px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.14);
        }

        .branch-card strong {
            display: block;
            margin-bottom: 6px;
            font-size: 0.98rem;
        }

        .branch-card p,
        .branch-card a,
        .branch-card div {
            color: rgba(255,255,255,0.84);
            font-size: 0.86rem;
            line-height: 1.65;
        }

        .footer-meta a {
            color: rgba(255,255,255,0.88);
        }

        .search-strip {
            background: #fff;
            border-radius: 0;
            border-top: 1px solid rgba(16, 35, 63, 0.06);
            border-bottom: 1px solid rgba(16, 35, 63, 0.06);
        }

        .portal-search {
            position: relative;
        }

        .portal-search input {
            border-radius: 12px;
            padding: 14px 48px 14px 16px;
            border: 1px solid rgba(16, 35, 63, 0.12);
        }

        .portal-search i {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
        }

        .catalog-link {
            color: var(--muted);
            font-size: 0.92rem;
            white-space: nowrap;
        }

        .catalog-link:hover {
            color: var(--brand-dark);
        }

        .search-meta {
            color: var(--muted);
            font-size: 0.88rem;
        }

        .mini-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 8px 12px;
            background: rgba(255,255,255,0.14);
            border: 1px solid rgba(255,255,255,0.18);
            color: #fff;
            font-size: 0.78rem;
        }

        .hero-showcase {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            width: 100%;
        }

        .showcase-item {
            background: rgba(255,255,255,0.9);
            color: var(--ink);
            border-radius: 24px;
            padding: 12px;
            text-align: center;
            box-shadow: 0 16px 40px rgba(10, 40, 80, 0.12);
        }

        .showcase-item img {
            width: 100%;
            height: 118px;
            object-fit: cover;
            border-radius: 16px;
            background: var(--paper-soft);
        }

        .showcase-item span {
            display: block;
            margin-top: 8px;
            font-size: 0.8rem;
            font-weight: 700;
        }

        @media (max-width: 991.98px) {
            .hero-copy,
            .hero-visual {
                padding: 26px;
            }

            .hero-showcase {
                grid-template-columns: 1fr 1fr;
            }

            .footer-company-grid {
                grid-template-columns: 1fr;
            }

            .section-title {
                display: block;
            }

            .section-title p {
                margin-top: 8px;
            }
        }

        @media (max-width: 767.98px) {
            .site-shell {
                width: min(100% - 20px, 1180px);
            }

            .hero {
                padding-top: 28px;
            }

            .hero-stats {
                grid-template-columns: 1fr;
            }

            .hero-showcase {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php
        $company_name = !empty($perusahaan['nama_perusahaan']) ? $perusahaan['nama_perusahaan'] : 'TGM Print';
        $tracking_data = isset($tracking) ? $tracking : null;
        $tracking_status_class = $tracking_data ? 'status-' . $tracking_data['status_class'] : 'status-warning';
        $landing_success = $this->session->flashdata('landing_success');
        $landing_error = $this->session->flashdata('landing_error');
        $main_company = !empty($primary_branch) ? $primary_branch : null;
    ?>

    <header class="topbar">
        <div class="utility-bar py-2">
            <div class="site-shell d-flex flex-column flex-lg-row justify-content-between align-items-center gap-2">
                <div class="utility-pill">
                    <i class="bi bi-headset"></i>
                    <span>CS TGM Print</span>
                </div>
                <div class="utility-pill">
                    <i class="bi bi-chevron-left small"></i>
                    <span>Cetak online untuk kebutuhan bisnis dan personal</span>
                    <i class="bi bi-chevron-right small"></i>
                </div>
                <div class="d-flex gap-4">
                    <a href="#tracking" class="utility-pill"><i class="bi bi-geo-alt"></i><span>Order tracking</span></a>
                    <div class="utility-pill"><span>Ind / Rp</span></div>
                </div>
            </div>
        </div>
        <div class="site-shell">
            <nav class="navbar navbar-expand-lg py-3">
                <a href="<?= base_url('website') ?>" class="navbar-brand d-flex align-items-center gap-3">
                    <img src="<?= base_url('assets/images/logo.png') ?>" alt="TGM Print" class="brand-mark">
                    <div class="brand-copy">
                        <strong class="d-block text-dark">TGM Print</strong>
                        <small><?= htmlspecialchars($company_name) ?></small>
                    </div>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                        <li class="nav-item"><a class="nav-link" href="#layanan">Layanan</a></li>
                        <li class="nav-item"><a class="nav-link" href="#kategori">Kategori</a></li>
                        <li class="nav-item"><a class="nav-link" href="#cek-harga">Cek Harga</a></li>
                        <li class="nav-item"><a class="nav-link" href="#self-order">Self Order</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tracking">Tracking</a></li>
                        <li class="nav-item ms-lg-2"><a class="btn btn-dark rounded-pill px-4" href="<?= base_url('auth') ?>">Login ERP</a></li>
                    </ul>
                </div>
            </nav>
        </div>
        <div class="search-strip py-3">
            <div class="site-shell">
                <div class="row align-items-center g-3">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-center gap-3">
                            <img src="<?= base_url('assets/images/logo.png') ?>" alt="TGM Print" style="width: 58px; height: 58px; object-fit: contain;">
                            <div>
                                <strong class="d-block">TGM Print Online</strong>
                                <small class="text-secondary">Solusi cetak profesional untuk bisnis, event, dan personal</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <form method="get" action="<?= base_url('website') ?>" class="portal-search">
                            <?php if (!empty($selected_category)): ?>
                                <input type="hidden" name="category" value="<?= (int) $selected_category ?>">
                            <?php endif; ?>
                            <input type="text" name="price" value="<?= htmlspecialchars($price_keyword ?? '') ?>" placeholder="Cari produk printing, bahan, atau kategori">
                            <i class="bi bi-search"></i>
                        </form>
                    </div>
                    <div class="col-lg-3">
                        <div class="d-flex justify-content-lg-end gap-3 align-items-center">
                            <span class="search-meta"><i class="bi bi-shield-check me-1"></i> Harga lebih transparan</span>
                            <span class="search-meta"><i class="bi bi-shop me-1"></i> Didukung jaringan cabang</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="site-shell">
                <div class="hero-panel">
                    <div class="row g-0">
                        <div class="col-lg-7">
                            <div class="hero-copy">
                                <div class="hero-kicker">
                                    <i class="bi bi-stars"></i>
                                    Cetak online cepat, rapi, dan terpercaya
                                </div>
                                <h1>Semua kebutuhan print Anda, dikerjakan lebih cepat dengan hasil yang siap tampil profesional.</h1>
                                <p>
                                    Dari kebutuhan promosi, branding, event, sampai produksi harian bisnis,
                                    TGM Print membantu Anda menemukan produk, mengecek harga, dan memantau pesanan dalam satu tempat.
                                </p>
                                <div class="hero-actions">
                                    <a href="#hasil-harga" class="btn btn-brand">Lihat katalog produk</a>
                                    <a href="#tracking" class="btn btn-ghost">Cek status pesanan</a>
                                </div>
                                <div class="hero-stats">
                                    <div class="stat-block">
                                        <strong><?= number_format((int) $total_product) ?>+</strong>
                                        <span>pilihan produk aktif siap dipesan</span>
                                    </div>
                                    <div class="stat-block">
                                        <strong><?= number_format((int) $total_konsumen) ?>+</strong>
                                        <span>pelanggan telah mempercayakan cetaknya</span>
                                    </div>
                                    <div class="stat-block">
                                        <strong>Multi Cabang</strong>
                                        <span>layanan lebih dekat dan lebih mudah dijangkau</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="hero-visual">
                                <div class="visual-stack">
                                    <div class="glass-card dark">
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="mini-badge"><i class="bi bi-lightning-charge"></i> Estimasi cepat</span>
                                            <span class="mini-badge"><i class="bi bi-box"></i> Tracking mudah</span>
                                            <span class="mini-badge"><i class="bi bi-grid"></i> Katalog lengkap</span>
                                        </div>
                                        <h3 class="h4 mt-3 mb-2">Dirancang untuk memudahkan calon pelanggan mengambil keputusan lebih cepat.</h3>
                                        <p class="mb-0 opacity-75">Mulai dari mencari produk, melihat estimasi harga, sampai memantau progress pesanan, semuanya dibuat lebih praktis dan nyaman.</p>
                                    </div>
                                    <div class="hero-showcase">
                                        <div class="showcase-item">
                                            <img src="<?= base_url('assets/images/product/X BANNER_1.jpg') ?>" alt="Banner">
                                            <span>X Banner</span>
                                        </div>
                                        <div class="showcase-item">
                                            <img src="<?= base_url('assets/images/product/BROSUR A5_1.jpg') ?>" alt="Brosur">
                                            <span>Brosur</span>
                                        </div>
                                        <div class="showcase-item">
                                            <img src="<?= base_url('assets/images/product/ROLLUP BANNER_1.jpg') ?>" alt="Roll Up">
                                            <span>Roll Up</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section" id="layanan">
            <div class="site-shell">
                <div class="section-title">
                    <div>
                        <h2>Layanan yang dirancang untuk membuat proses cetak terasa lebih mudah</h2>
                    </div>
                    <p>TGM Print menghadirkan pengalaman yang lebih ringkas untuk pelanggan: mudah menemukan produk, cepat mendapat gambaran harga, dan tenang karena status pesanan bisa dipantau kapan saja.</p>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="bi bi-cash-coin"></i></div>
                            <h3 class="h5">Cek harga lebih cepat</h3>
                            <p class="text-secondary mb-0">Pelanggan bisa langsung melihat kisaran harga aktif sebagai referensi awal sebelum berkonsultasi lebih lanjut dengan tim kami.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="bi bi-truck"></i></div>
                            <h3 class="h5">Tracking pesanan real-time</h3>
                            <p class="text-secondary mb-0">Dengan nomor SPK, pelanggan dapat memantau progres pesanan secara lebih jelas tanpa harus menunggu update manual.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="bi bi-building-check"></i></div>
                            <h3 class="h5">Terhubung ke sistem operasional</h3>
                            <p class="text-secondary mb-0">Informasi produk, harga, dan status transaksi diambil dari sistem yang berjalan agar tampil lebih relevan dan selalu sinkron.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section result-anchor" id="kategori">
            <div class="site-shell">
                <div class="section-title">
                    <div>
                        <h2>Temukan produk sesuai kebutuhan Anda</h2>
                    </div>
                    <p>Jelajahi berbagai kategori produk TGM Print, dari kebutuhan promosi, display, merchandise, hingga material pendukung untuk operasional bisnis Anda.</p>
                </div>
                <div class="category-grid">
                    <a href="<?= base_url('website#hasil-harga') ?>" class="category-card <?= empty($selected_category) ? 'is-active' : '' ?>">
                        <strong>Semua Produk</strong>
                        <span>Lihat seluruh pilihan produk aktif dalam satu tampilan.</span>
                    </a>
                    <?php foreach ($categories as $category): ?>
                        <a href="<?= base_url('website?category=' . (int) $category['kategori_id'] . '#hasil-harga') ?>" class="category-card <?= (int) $selected_category === (int) $category['kategori_id'] ? 'is-active' : '' ?>">
                            <strong><?= htmlspecialchars($category['kategori_nama']) ?></strong>
                            <small><?= (int) $category['total_products'] ?> produk aktif</small>
                            <span class="d-block mt-2"><?= htmlspecialchars(trim($category['kategori_deskripsi']) ?: 'Pilihan produk untuk berbagai kebutuhan cetak') ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="site-shell">
                <div class="row g-4 align-items-start">
                    <div class="col-lg-6 result-anchor" id="cek-harga">
                        <div class="tool-card">
                            <div class="tool-icon"><i class="bi bi-search"></i></div>
                            <h2 class="h3">Lihat estimasi harga produk</h2>
                            <p class="text-secondary">Cari produk, bahan, atau kategori untuk mendapatkan gambaran harga awal sebelum Anda melanjutkan ke proses pemesanan.</p>
                            <form method="get" action="<?= base_url('website') ?>">
                                <div class="mb-3">
                                    <label for="price" class="form-label fw-semibold">Cari produk</label>
                                    <input type="text" class="form-control" id="price" name="price" value="<?= htmlspecialchars($price_keyword ?? '') ?>" placeholder="Contoh: brosur, banner, stiker, x-banner">
                                </div>
                                <?php if (!empty($selected_category)): ?>
                                    <input type="hidden" name="category" value="<?= (int) $selected_category ?>">
                                <?php endif; ?>
                                <?php if (!empty($tracking_keyword)): ?>
                                    <input type="hidden" name="spk" value="<?= htmlspecialchars($tracking_keyword) ?>">
                                <?php endif; ?>
                                <button type="submit" class="btn btn-brand">Lihat estimasi harga</button>
                            </form>
                            <div class="chips mt-4">
                                <span class="chip">Harga lebih informatif</span>
                                <span class="chip">Pilihan qty terlihat</span>
                                <span class="chip">Mudah untuk perbandingan awal</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 result-anchor" id="tracking">
                        <div class="tool-card">
                            <div class="tool-icon"><i class="bi bi-upc-scan"></i></div>
                            <h2 class="h3">Pantau status pesanan Anda</h2>
                            <p class="text-secondary">Masukkan nomor SPK untuk melihat ringkasan transaksi dan posisi pesanan terbaru secara lebih cepat dan praktis.</p>
                            <form method="get" action="<?= base_url('website') ?>">
                                <div class="mb-3">
                                    <label for="spk" class="form-label fw-semibold">Nomor SPK</label>
                                    <input type="text" class="form-control" id="spk" name="spk" value="<?= htmlspecialchars($tracking_keyword ?? '') ?>" placeholder="Contoh: SPK12345">
                                </div>
                                <?php if (!empty($selected_category)): ?>
                                    <input type="hidden" name="category" value="<?= (int) $selected_category ?>">
                                <?php endif; ?>
                                <?php if (!empty($price_keyword)): ?>
                                    <input type="hidden" name="price" value="<?= htmlspecialchars($price_keyword) ?>">
                                <?php endif; ?>
                                <button type="submit" class="btn btn-brand">Cek status pesanan</button>
                            </form>
                            <div class="chips mt-4">
                                <span class="chip">Status lebih jelas</span>
                                <span class="chip">Detail item tersedia</span>
                                <span class="chip">Akses invoice lebih mudah</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section result-anchor" id="hasil-harga">
            <div class="site-shell">
                <div class="section-title">
                    <div>
                        <h2><?= !empty($price_keyword) ? 'Hasil pencarian produk' : 'Pilihan produk unggulan' ?></h2>
                    </div>
                    <p><?= !empty($price_keyword) ? 'Berikut produk yang paling relevan dengan pencarian Anda.' : 'Beberapa pilihan produk yang paling sering dicari untuk membantu Anda mulai lebih cepat.' ?><?php if (!empty($selected_category)): ?> Kategori pilihan Anda sedang aktif.<?php endif; ?></p>
                </div>
                <div class="row g-4">
                    <?php $price_source = !empty($price_keyword) ? $price_results : $featured_prices; ?>
                    <?php if (!empty($price_source)): ?>
                        <?php foreach ($price_source as $product): ?>
                            <div class="col-md-6 col-xl-4">
                                <div class="product-card">
                                    <div class="d-flex justify-content-between gap-3 align-items-start">
                                        <div>
                                            <small class="text-uppercase text-secondary"><?= htmlspecialchars($product['kategori_nama'] ?: 'Produk Printing') ?></small>
                                            <h3 class="h4 mt-2 mb-2"><?= htmlspecialchars($product['product_nama']) ?></h3>
                                        </div>
                                        <span class="chip"><?= (int) $product['variant_count'] ?> varian</span>
                                    </div>

                                    <div class="price-range mt-3">
                                        Rp <?= number_format((float) $product['min_price'], 0, ',', '.') ?>
                                        <?php if ((float) $product['max_price'] > (float) $product['min_price']): ?>
                                            <span class="text-secondary fw-semibold">- Rp <?= number_format((float) $product['max_price'], 0, ',', '.') ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-secondary mt-2 mb-0">Nilai harga dapat menyesuaikan jumlah, material, ukuran, dan spesifikasi detail pesanan.</p>

                                    <?php if (!empty($product['samples'])): ?>
                                        <?php foreach ($product['samples'] as $sample): ?>
                                            <div class="product-sample">
                                                <div class="fw-semibold"><?= htmlspecialchars($sample['detail_product'] ?: $product['product_nama']) ?></div>
                                                <div class="small text-secondary mt-1">
                                                    Qty <?= number_format((int) $sample['qty_min']) ?> - <?= number_format((int) $sample['qty_max']) ?>
                                                    <span class="mx-1">|</span>
                                                    Rp <?= number_format((float) $sample['harga_1'], 0, ',', '.') ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="product-card">
                                <h3 class="h5 mb-2">Produk yang Anda cari belum ditemukan</h3>
                                <p class="mb-0 text-secondary">Coba gunakan kata kunci yang lebih umum seperti `brosur`, `banner`, `stiker`, atau nama material yang paling sering digunakan.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="section result-anchor" id="self-order">
            <div class="site-shell">
                <div class="section-title">
                    <div>
                        <h2>Pesan lebih praktis langsung dari website</h2>
                    </div>
                    <p>Pelanggan dapat mengirimkan pesanan awal secara mandiri, lalu tim TGM Print akan membantu menindaklanjuti detail produksi agar proses berjalan lebih cepat dan rapi.</p>
                </div>

                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="tool-card">
                            <div class="tool-icon"><i class="bi bi-cart-check"></i></div>
                            <h2 class="h3">Kirim pesanan awal Anda</h2>
                            <p class="text-secondary">Pilih produk, tentukan jumlah, lalu tinggalkan data kontak Anda. Tim kami akan menindaklanjuti pesanan dengan nomor referensi otomatis.</p>

                            <?php if (!empty($landing_success)): ?>
                                <div class="alert-soft alert-soft-success mb-3"><?= htmlspecialchars($landing_success) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($landing_error)): ?>
                                <div class="alert-soft alert-soft-danger mb-3"><?= htmlspecialchars($landing_error) ?></div>
                            <?php endif; ?>

                            <form method="post" action="<?= base_url('website/self-order') ?>">
                                <div class="mb-3">
                                    <label for="harga_id" class="form-label fw-semibold">Produk / varian</label>
                                    <select class="form-select" id="harga_id" name="harga_id" required>
                                        <option value="">Pilih produk yang ingin Anda pesan</option>
                                        <?php foreach ($order_options as $option): ?>
                                            <option
                                                value="<?= (int) $option['harga_id'] ?>"
                                                data-price="<?= (float) $option['harga_1'] ?>"
                                                data-unit="<?= htmlspecialchars($option['satuan_nama'] ?: 'pcs') ?>"
                                            >
                                                <?= htmlspecialchars($option['kategori_nama'] . ' - ' . $option['product_nama']) ?>
                                                <?php if (!empty($option['detail_product']) && $option['detail_product'] !== $option['product_nama']): ?>
                                                    | <?= htmlspecialchars($option['detail_product']) ?>
                                                <?php endif; ?>
                                                | Rp <?= number_format((float) $option['harga_1'], 0, ',', '.') ?>
                                                | Qty <?= number_format((float) $option['qty_min'], 0, ',', '.') ?> - <?= number_format((float) $option['qty_max'], 0, ',', '.') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="qty" class="form-label fw-semibold">Qty</label>
                                        <input type="number" class="form-control" id="qty" name="qty" min="1" step="1" value="1" required>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label fw-semibold">Estimasi nilai pesanan</label>
                                        <div class="order-summary">
                                            <strong id="estimatedTotal">Rp 0</strong>
                                            <div class="small text-secondary mt-1" id="estimatedNote">Pilih produk untuk melihat gambaran nilai pesanan Anda.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 mt-1">
                                    <div class="col-md-6">
                                        <label for="nama_lengkap" class="form-label fw-semibold">Nama lengkap</label>
                                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" placeholder="Masukkan nama Anda" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="no_hp" class="form-label fw-semibold">Nomor WhatsApp</label>
                                        <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder="08xxxxxxxxxx" required>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label for="catatan" class="form-label fw-semibold">Catatan pesanan</label>
                                    <textarea class="form-control" id="catatan" name="catatan" rows="4" placeholder="Contoh: deadline cepat, file desain menyusul, atau kebutuhan finishing tertentu"></textarea>
                                </div>
                                <button type="submit" class="btn btn-brand mt-3">Kirim pesanan sekarang</button>
                            </form>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="tracking-card h-100">
                            <span class="status-pill status-primary mb-3">
                                <i class="bi bi-diagram-3"></i>
                                Alur pemesanan
                            </span>
                            <div class="product-sample mt-0">
                                <div class="fw-semibold">1. Pilih produk yang Anda butuhkan</div>
                                <div class="small text-secondary mt-1">Tentukan produk, jumlah, dan isi data kontak untuk memulai pesanan.</div>
                            </div>
                            <div class="product-sample">
                                <div class="fw-semibold">2. Pesanan masuk ke tim kami</div>
                                <div class="small text-secondary mt-1">Tim TGM Print akan meninjau detail, material, file desain, dan kebutuhan finishing Anda.</div>
                            </div>
                            <div class="product-sample">
                                <div class="fw-semibold">3. Lanjut ke proses konfirmasi</div>
                                <div class="small text-secondary mt-1">Nomor referensi akan digunakan sebagai acuan untuk konfirmasi, pembayaran, dan proses produksi.</div>
                            </div>
                            <div class="chips mt-4">
                                <span class="chip">Referensi otomatis</span>
                                <span class="chip">Proses lebih terarah</span>
                                <span class="chip">Cocok untuk pemesanan awal</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section result-anchor" id="hasil-tracking">
            <div class="site-shell">
                <div class="section-title">
                    <div>
                        <h2>Tracking pesanan pelanggan</h2>
                    </div>
                    <p>Gunakan nomor SPK untuk memantau perjalanan pesanan Anda, mulai dari proses produksi hingga siap diambil atau dikirim.</p>
                </div>

                <?php if ($tracking_found === true && !empty($tracking_data['pembayaran'])): ?>
                    <div class="tracking-card">
                        <div class="row g-4 align-items-center">
                            <div class="col-lg-8">
                                <div class="d-flex flex-wrap gap-3 align-items-center mb-3">
                                    <span class="status-pill <?= $tracking_status_class ?>">
                                        <i class="bi bi-record-circle"></i>
                                        <?= htmlspecialchars($tracking_data['status_badge']) ?>
                                    </span>
                                    <span class="chip">SPK <?= htmlspecialchars($tracking_data['searched_spk']) ?></span>
                                </div>
                                <h3 class="h3 mb-1"><?= htmlspecialchars($tracking_data['pembayaran']->konsumen_nama ?? 'Customer') ?></h3>
                                <p class="text-secondary mb-3">Update terbaru pesanan Anda: <?= htmlspecialchars($tracking_data['progress_label']) ?></p>
                                <div class="progress-shell mb-2">
                                    <span style="width: <?= (int) $tracking_data['progress_percent'] ?>%"></span>
                                </div>
                                <div class="small text-secondary"><?= (int) $tracking_data['progress_percent'] ?>% progress terindikasi dari status produksi saat ini.</div>
                            </div>
                            <div class="col-lg-4">
                                <div class="glass-card">
                                    <div class="small text-uppercase text-secondary">Ringkasan</div>
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-secondary">Nomor HP</span>
                                            <strong><?= htmlspecialchars($tracking_data['pembayaran']->konsumen_nohp ?? '-') ?></strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-secondary">Total item</span>
                                            <strong><?= count($tracking_data['detailProduk']) ?></strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-secondary">Detail transaksi</span>
                                            <a href="<?= base_url('cekpesanan/spk/' . urlencode(encrypt_spk($tracking_data['searched_spk']))) ?>" class="fw-bold">Lihat invoice</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($tracking_data['detailProduk'])): ?>
                            <div class="row g-3 mt-2">
                                <?php foreach ($tracking_data['detailProduk'] as $detail): ?>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="product-sample h-100">
                                            <div class="fw-semibold"><?= htmlspecialchars($detail->detail_product ?: $detail->product_nama) ?></div>
                                            <div class="small text-secondary mt-1"><?= htmlspecialchars($detail->kategori_nama ?: 'Produk Printing') ?></div>
                                            <div class="small mt-2">
                                                Qty <?= number_format((float) $detail->qty, 0, ',', '.') ?>
                                                <?php if ((float) $detail->panjang > 0 || (float) $detail->lebar > 0): ?>
                                                    <span class="mx-1">|</span>
                                                    <?= rtrim(rtrim(number_format((float) $detail->panjang, 2, '.', ''), '0'), '.') ?> x <?= rtrim(rtrim(number_format((float) $detail->lebar, 2, '.', ''), '0'), '.') ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php elseif ($tracking_found === false): ?>
                    <div class="tracking-card">
                        <span class="status-pill status-danger mb-3">
                            <i class="bi bi-exclamation-circle"></i>
                            SPK tidak ditemukan
                        </span>
                        <h3 class="h4">Nomor SPK `<?= htmlspecialchars($tracking_keyword) ?>` belum kami temukan.</h3>
                        <p class="mb-0 text-secondary">Silakan periksa kembali nomor yang Anda masukkan, atau gunakan nomor yang tertera pada nota maupun invoice Anda.</p>
                    </div>
                <?php else: ?>
                    <div class="tracking-card">
                        <h3 class="h4">Siap membantu Anda memantau pesanan</h3>
                        <p class="mb-0 text-secondary">Masukkan nomor SPK pada form di atas untuk melihat status pesanan Anda secara cepat dan praktis.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="section">
            <div class="site-shell">
                <div class="cta-band">
                    <div class="footer-company-grid">
                        <div>
                            <h2 class="h3 mb-2"><?= htmlspecialchars($main_company['nama_perusahaan'] ?? 'TGM Print') ?></h2>
                            <p class="mb-3">Hadir dengan dukungan beberapa cabang, TGM Print siap melayani kebutuhan cetak Anda dengan jangkauan yang lebih luas dan pelayanan yang tetap konsisten.</p>
                            <div class="footer-meta">
                                <?php if (!empty($main_company['telepon'])): ?>
                                    <div class="mb-2"><strong>Telepon:</strong> <?= htmlspecialchars($main_company['telepon']) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($main_company['no_wa'])): ?>
                                    <div class="mb-2"><strong>WhatsApp:</strong> <?= htmlspecialchars($main_company['no_wa']) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($main_company['email'])): ?>
                                    <div class="mb-2"><strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($main_company['email']) ?>"><?= htmlspecialchars($main_company['email']) ?></a></div>
                                <?php endif; ?>
                                <?php if (!empty($main_company['website'])): ?>
                                    <div><strong>Website:</strong> <a href="<?= strpos($main_company['website'], 'http') === 0 ? htmlspecialchars($main_company['website']) : 'https://' . htmlspecialchars($main_company['website']) ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($main_company['website']) ?></a></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="branch-list">
                            <?php foreach ($company_branches as $branch): ?>
                                <?php
                                    $branch_address = trim(($branch['alamat_perusahaan'] ?? '') . (!empty($branch['kota']) ? ', ' . $branch['kota'] : '') . (!empty($branch['provinsi']) ? ', ' . $branch['provinsi'] : ''));
                                    $map_url = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($branch_address);
                                ?>
                                <div class="branch-card">
                                    <strong><?= htmlspecialchars($branch['nama_perusahaan']) ?></strong>
                                    <div class="mb-1"><?= htmlspecialchars($branch['perusahaan_alias'] ?? '') ?></div>
                                    <p class="mb-2"><?= htmlspecialchars($branch_address) ?></p>
                                    <div class="d-flex flex-wrap gap-3">
                                        <?php if (!empty($branch['telepon'])): ?>
                                            <span><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($branch['telepon']) ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($branch['no_wa'])): ?>
                                            <span><i class="bi bi-whatsapp me-1"></i><?= htmlspecialchars($branch['no_wa']) ?></span>
                                        <?php endif; ?>
                                        <a href="<?= $map_url ?>" target="_blank" rel="noopener noreferrer"><i class="bi bi-geo-alt me-1"></i>Buka lokasi di Google Maps</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer-note">
        <div class="site-shell d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div>© <?= date('Y') ?> <?= htmlspecialchars($main_company['nama_perusahaan'] ?? 'TGM Print') ?>. Solusi cetak profesional untuk kebutuhan bisnis dan personal.</div>
            <div><?= count($company_branches) ?> lokasi aktif siap membantu kebutuhan cetak Anda.</div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            const hasPriceResult = <?= !empty($price_keyword) ? 'true' : 'false' ?>;
            const hasTrackingResult = <?= $tracking_found !== null ? 'true' : 'false' ?>;
            const hasOrderFlash = <?= (!empty($landing_success) || !empty($landing_error)) ? 'true' : 'false' ?>;
            const targetId = hasOrderFlash ? 'self-order' : (hasTrackingResult ? 'hasil-tracking' : (hasPriceResult ? 'hasil-harga' : ''));

            if (!targetId) {
                return;
            }

            const target = document.getElementById(targetId);
            if (target) {
                setTimeout(() => target.scrollIntoView({ behavior: 'smooth', block: 'start' }), 120);
            }
        })();

        (function () {
            const productSelect = document.getElementById('harga_id');
            const qtyInput = document.getElementById('qty');
            const totalEl = document.getElementById('estimatedTotal');
            const noteEl = document.getElementById('estimatedNote');

            if (!productSelect || !qtyInput || !totalEl || !noteEl) {
                return;
            }

            const formatIDR = (value) => new Intl.NumberFormat('id-ID').format(value || 0);

            const refreshEstimate = () => {
                const selected = productSelect.options[productSelect.selectedIndex];
                const unitPrice = parseFloat(selected?.dataset?.price || '0');
                const unitName = selected?.dataset?.unit || 'pcs';
                const qty = parseFloat(qtyInput.value || '0');
                const total = unitPrice * qty;

                totalEl.textContent = 'Rp ' + formatIDR(total);
                if (unitPrice > 0) {
                    noteEl.textContent = 'Estimasi berdasarkan harga aktif Rp ' + formatIDR(unitPrice) + ' per ' + unitName + '.';
                } else {
                    noteEl.textContent = 'Pilih produk untuk melihat estimasi harga awal.';
                }
            };

            productSelect.addEventListener('change', refreshEstimate);
            qtyInput.addEventListener('input', refreshEstimate);
            refreshEstimate();
        })();
    </script>
</body>
</html>
