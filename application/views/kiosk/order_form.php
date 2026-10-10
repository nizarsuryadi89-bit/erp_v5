<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $title ?></title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <style>
        :root {
            --primary: #0f172a;
            /* Deep Navy */
            --accent: #1e40af;
            /* Royal Blue/Navy */
            --accent-light: #eff6ff;
            --bg: #f1f5f9;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --radius-md: 16px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            padding-bottom: 160px;
            /* Space for larger checkout bar */
            -webkit-tap-highlight-color: transparent;
        }

        /* --- Touch Friendly Product Grid --- */
        .product-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 12px 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 12px -3px rgba(0, 0, 0, 0.08);
            border-color: var(--accent);
        }

        .product-card:active {
            transform: scale(0.96);
            background-color: #eff6ff;
        }

        .product-icon {
            font-size: 1.2rem;
            color: var(--accent);
            margin-bottom: 8px;
            background: #eff6ff;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-title {
            font-weight: 700;
            font-size: 0.8rem;
            line-height: 1.2;
            color: var(--primary);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 2em;
        }

        .product-cat {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Category Filter Pills */
        .cat-scroll {
            overflow-x: auto;
            white-space: nowrap;
            padding-bottom: 10px;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .cat-scroll::-webkit-scrollbar {
            display: none;
        }

        .cat-pill {
            display: inline-block;
            background: white;
            border: 1px solid #cbd5e1;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            color: var(--text-muted);
            margin-right: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .cat-pill.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.3);
        }

        /* --- Admin-Like Styles --- */
        .report-section-card {
            background: var(--card-bg);
            border-radius: var(--radius-md);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .section-header {
            padding: 12px 20px;
            background: linear-gradient(to right, #1e293b, #0f172a);
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .section-header.header-dark {
            background: linear-gradient(to right, #0f172a, #020617);
        }

        .section-header.header-green {
            background: linear-gradient(to right, #1e40af, #1e3a8a);
        }

        .section-title {
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-body {
            padding: 20px;
        }

        /* Inputs */
        .form-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .form-control,
        .form-select {
            height: 48px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 10px 16px;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        /* Qty Spinner */
        .qty-input-group {
            display: flex;
            align-items: center;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
        }

        .qty-btn {
            width: 50px;
            height: 48px;
            background: #f8fafc;
            border: none;
            color: var(--text-main);
            font-size: 1.1rem;
        }

        #qty {
            border: none;
            text-align: center;
            font-weight: 800;
            font-size: 1.2rem;
            height: 48px;
            box-shadow: none;
        }

        /* --- Cart & Checkout --- */
        .cart-item {
            border-bottom: 1px solid #e2e8f0;
            padding: 15px 23px;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-title {
            font-weight: 700;
            color: var(--text-main);
            font-size: 1rem;
        }

        .cart-meta {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .cart-price {
            font-weight: 800;
            color: var(--primary);
            font-size: 1rem;
        }

        .checkout-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: white;
            border-top: 1px solid #e2e8f0;
            padding: 15px 20px 25px 20px;
            /* Safe area padding */
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.05);
            z-index: 100;
        }

        .btn-add-cart {
            width: 100%;
            background: var(--bg);
            border: 2px dashed #cbd5e1;
            color: var(--text-muted);
            font-weight: 700;
            padding: 15px;
            border-radius: 10px;
            margin-top: 10px;
            transition: all 0.2s;
        }

        .btn-add-cart:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: #eff6ff;
        }

        .btn-checkout {
            background: linear-gradient(135deg, #1e40af, #1e3a8a);
            color: white;
            border: none;
            width: 100%;
            padding: 15px;
            border-radius: 12px;
            font-weight: 800;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(30, 64, 175, 0.4);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .grand-total-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            color: var(--text-muted);
            font-weight: 700;
        }

        .grand-total-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--primary);
            line-height: 1;
        }

        /* UI Autocomplete */
        .ui-autocomplete {
            border-radius: 8px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
            padding: 5px;
            z-index: 1050;
            background: white;
        }

        .ui-menu-item .ui-menu-item-wrapper {
            padding: 10px 15px;
            border-radius: 6px;
            font-size: 0.95rem;
        }

        .ui-state-active,
        .ui-widget-content .ui-state-active {
            background: #eff6ff;
            color: var(--accent);
            border: none;
        }

        .animate-in {
            animation: fadeIn 0.4s ease-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .catatan-hints {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .hint-chip {
            font-size: 0.75rem;
            background: #f1f5f9;
            color: var(--text-main);
            padding: 5px 12px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 600;
            border: 1px solid #e2e8f0;
        }

        .hint-chip:hover {
            border-color: var(--accent);
            background: #eff6ff;
            color: var(--accent);
        }

        /* ===== MOBILE RESPONSIVE OPTIMIZATIONS ===== */

        /* Small Mobile Devices (320px - 575px) */
        @media (max-width: 575px) {
            body {
                padding-bottom: 180px;
                /* More space for mobile checkout bar */
            }

            .container {
                padding-left: 12px !important;
                padding-right: 12px !important;
            }

            /* Header Optimizations */
            .d-flex.align-items-center.justify-content-between.mb-4 {
                flex-direction: column;
                gap: 12px;
                align-items: flex-start !important;
            }

            .d-flex.align-items-center.gap-3 {
                gap: 10px !important;
            }

            .d-flex.align-items-center.gap-3>div:first-child {
                width: 40px !important;
                height: 40px !important;
                font-size: 1.2rem !important;
            }

            h1.h4 {
                font-size: 1.1rem !important;
            }

            .badge.bg-success {
                font-size: 0.65rem !important;
                padding: 4px 8px !important;
            }

            p.text-muted.small {
                font-size: 0.7rem !important;
            }

            /* Info Banner Mobile */
            .alert.border-0 {
                padding: 15px !important;
            }

            .alert .col-auto {
                display: none !important;
                /* Hide icon on very small screens */
            }

            .alert h5 {
                font-size: 0.95rem !important;
                margin-bottom: 10px !important;
            }

            .alert .row.g-2 .col-md-6 {
                font-size: 0.75rem !important;
            }

            .alert .row.g-2 .col-md-6 i {
                font-size: 0.7rem;
            }

            /* Customer Input Section */
            #card-customer .row.g-2 {
                flex-wrap: wrap;
            }

            #card-customer .col-1 {
                display: none;
                /* Hide icon on mobile to save space */
            }

            #card-customer .col-6,
            #card-customer .col-5 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            #card-customer .form-control {
                height: 44px;
                font-size: 0.9rem;
            }

            /* Search Bar */
            .input-group-lg {
                font-size: 0.95rem !important;
            }

            .input-group-lg .form-control {
                height: 46px !important;
                font-size: 0.9rem !important;
            }

            /* Category Pills */
            .cat-pill {
                padding: 8px 16px;
                font-size: 0.75rem;
                margin-right: 8px;
            }

            /* Product Grid - Show 3 columns on mobile */
            .product-item.col-4 {
                flex: 0 0 33.333333%;
                max-width: 33.333333%;
                padding-left: 5px !important;
                padding-right: 5px !important;
            }

            .product-card {
                padding: 10px 5px;
            }

            .product-icon {
                width: 32px;
                height: 32px;
                font-size: 1rem;
                margin-bottom: 5px;
                border-radius: 8px;
            }

            .product-title {
                font-size: 0.7rem;
                height: 2.4em;
            }

            .product-cat {
                font-size: 0.6rem;
            }

            /* Detail Form */
            #step-detail .btn.rounded-circle {
                width: 40px !important;
                height: 40px !important;
            }

            #detail_title {
                font-size: 1.1rem !important;
            }

            #detail_cat {
                font-size: 0.75rem !important;
            }

            .form-label {
                font-size: 0.7rem;
            }

            .form-control,
            .form-select {
                height: 44px;
                font-size: 0.85rem;
                padding: 8px 12px;
            }

            /* Quantity Section */
            .qty-btn {
                width: 44px;
                height: 44px;
                font-size: 1rem;
            }

            #qty {
                font-size: 1.1rem;
                height: 44px;
            }

            .col-6.text-end small {
                font-size: 0.7rem !important;
            }

            #item_price_display {
                font-size: 0.9rem;
            }

            /* Total Display */
            .alert.alert-primary .h3 {
                font-size: 1.5rem !important;
            }

            /* Notes Section */
            #catatan {
                font-size: 0.85rem;
            }

            .hint-chip {
                font-size: 0.7rem;
                padding: 4px 10px;
            }

            /* Add to Cart Button */
            .btn-lg.btn-success {
                padding: 12px !important;
                font-size: 0.95rem !important;
            }

            /* Cart Section */
            .section-header {
                padding: 10px 15px;
            }

            .section-title {
                font-size: 0.8rem;
            }

            .section-body {
                padding: 15px;
            }

            .cart-item {
                padding: 12px 15px;
            }

            .cart-title {
                font-size: 0.9rem;
            }

            .cart-meta {
                font-size: 0.75rem;
            }

            .cart-price {
                font-size: 0.9rem;
            }

            .cart-item .badge {
                font-size: 0.7rem;
                padding: 3px 8px;
            }

            .cart-item .btn-sm {
                font-size: 0.75rem;
                padding: 4px 8px;
            }

            /* Checkout Bar - Mobile Optimized */
            .checkout-bar {
                padding: 12px 15px 25px 15px;
            }

            .checkout-bar .row {
                flex-wrap: nowrap;
                gap: 10px;
            }

            .checkout-bar .col-5 {
                flex: 0 0 40%;
                max-width: 40%;
            }

            .checkout-bar .col-7 {
                flex: 0 0 60%;
                max-width: 60%;
            }

            .grand-total-label {
                font-size: 0.65rem;
            }

            .grand-total-value {
                font-size: 1.3rem;
            }

            .checkout-bar small {
                font-size: 0.6rem !important;
            }

            .btn-checkout {
                padding: 12px 10px;
                font-size: 0.85rem;
                letter-spacing: 0.5px;
            }

            /* Safe Area for iOS devices */
            @supports (padding-bottom: env(safe-area-inset-bottom)) {
                body {
                    padding-bottom: calc(180px + env(safe-area-inset-bottom));
                }

                .checkout-bar {
                    padding-bottom: calc(25px + env(safe-area-inset-bottom));
                }
            }
        }

        /* Medium Mobile Devices (576px - 767px) */
        @media (min-width: 576px) and (max-width: 767px) {
            .container {
                padding-left: 15px !important;
                padding-right: 15px !important;
            }

            h1.h4 {
                font-size: 1.3rem !important;
            }

            .product-title {
                font-size: 0.9rem;
            }

            .btn-checkout {
                font-size: 1rem;
            }

            .grand-total-value {
                font-size: 1.5rem;
            }
        }

        /* Tablets (768px - 991px) */
        @media (min-width: 768px) and (max-width: 991px) {
            .product-item.col-md-3 {
                flex: 0 0 33.333%;
                max-width: 33.333%;
            }

            .checkout-bar .col-5 {
                flex: 0 0 35%;
            }

            .checkout-bar .col-7 {
                flex: 0 0 65%;
            }
        }

        /* Touch Interactions - All Mobile Devices */
        @media (hover: none) and (pointer: coarse) {

            /* Enhance touch targets */
            .product-card {
                min-height: 140px;
            }

            .cat-pill {
                min-height: 44px;
                display: inline-flex;
                align-items: center;
            }

            .qty-btn {
                min-width: 44px;
                min-height: 44px;
            }

            /* Remove hover effects on touch devices */
            .product-card:hover {
                transform: none;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            }

            .hint-chip:hover {
                border-color: #e2e8f0;
                background: #f1f5f9;
                color: var(--text-main);
            }
        }

        /* Landscape Mobile Orientation */
        @media (max-width: 767px) and (orientation: landscape) {
            body {
                padding-bottom: 140px;
            }

            .alert.border-0 {
                display: none;
                /* Hide info banner in landscape to save space */
            }

            .checkout-bar {
                padding: 10px 15px 20px 15px;
            }

            .grand-total-value {
                font-size: 1.2rem;
            }
        }

        /* Very Small Screens (320px and below) */
        @media (max-width: 320px) {
            h1.h4 {
                font-size: 0.95rem !important;
            }

            .product-title {
                font-size: 0.75rem;
            }

            .btn-checkout {
                font-size: 0.75rem;
                padding: 10px 8px;
            }

            .grand-total-value {
                font-size: 1.1rem;
            }
        }
    </style>
</head>

<body>

    <div class="container py-4" style="max-width: 1400px;">

        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center border border-white border-2"
                    style="width:50px; height:50px; font-size:1.5rem; box-shadow: 0 4px 10px rgba(15, 23, 42, 0.2);">
                    <i class="fas fa-print"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h1 class="h4 fw-bold text-primary mb-0">InstaPrint TGM Core</h1>
                        <span class="badge bg-success rounded-pill px-3 py-2 animate-in" style="font-size: 0.75rem;">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            <?= $branch_name ?>
                        </span>
                    </div>
                    <p class="text-muted small fw-bold mb-0">Order Cepat • Harga Pasti • Proses Instant</p>
                </div>
            </div>
            <a href="<?= site_url('kiosk') ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold"><i
                    class="fas fa-undo me-1"></i> Reset</a>
        </div>

        <!-- Info Banner -->
        <div class="alert border-0 shadow-sm mb-4 animate-in"
            style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%); border-radius: 16px;">
            <div class="row align-items-center g-3">
                <div class="col-auto">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-bolt fa-2x text-white"></i>
                    </div>
                </div>
                <div class="col text-white">
                    <h5 class="fw-bold mb-2 text-white">
                        <i class="fas fa-star me-2"></i>Kenapa Gunakan Kiosk Ini?
                    </h5>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-rocket me-2"></i>
                                <span class="small"><strong>Proses Lebih Cepat</strong> - Order langsung masuk ke sistem
                                    Deskprint</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-calculator me-2"></i>
                                <span class="small"><strong>Estimasi Harga Real-time</strong> - Lihat harga sebelum
                                    pesan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="form-container">
            <!-- 1. CUSTOMER (Always Visible, Compact) -->
            <div class="report-section-card animate-in" id="card-customer">
                <div class="section-body py-3">
                    <div class="row g-2 align-items-center">
                        <div class="col-1 text-center text-muted"><i class="fas fa-user"></i></div>
                        <div class="col-6">
                            <input type="text" id="nama_konsumen" class="form-control border-0 bg-light"
                                placeholder="Ketuk untuk isi Nama Pelanggan...">
                        </div>
                        <div class="col-5">
                            <input type="tel" id="no_wa" class="form-control border-0 bg-light"
                                placeholder="No. HP / WA" inputmode="numeric">
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 1: PRODUCT SELECTION (GRID) -->
            <div id="step-product" class="animate-in">

                <!-- Search Bar -->
                <div class="mb-3">
                    <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-3"><i
                                class="fas fa-search text-muted"></i></span>
                        <input type="text" id="product_search" class="form-control border-0"
                            placeholder="Cari Produk..." oninput="searchProduct(this.value)">
                        <button class="btn btn-white border-0 px-3" onclick="clearSearch()" id="btn_clear_search"
                            style="display:none;"><i class="fas fa-times"></i></button>
                    </div>
                </div>

                <!-- Category Filters -->
                <div class="cat-scroll mb-3">
                    <div class="cat-pill active" onclick="filterCat('all', this)">SEMUA</div>
                    <?php
                    // Extract Categories
                    $cats = [];
                    foreach ($products as $p) {
                        if (!in_array($p->kategori_nama, $cats))
                            $cats[] = $p->kategori_nama;
                    }
                    foreach ($cats as $c): ?>
                        <div class="cat-pill" onclick="filterCat('<?= $c ?>', this)"><?= strtoupper($c) ?></div>
                    <?php endforeach; ?>
                </div>

                <!-- Product Grid -->
                <div class="row g-2 g-md-3" id="product_grid">
                    <?php foreach ($products as $p): ?>
                        <div class="col-4 col-md-2 product-item animate-in" data-cat="<?= $p->kategori_nama ?>"
                            data-name="<?= mb_strtolower($p->product_nama) ?>">
                            <div class="product-card"
                                onclick="selectProduct(<?= $p->product_id ?>, '<?= $p->product_nama ?>', '<?= strtolower($p->satuan_nama) ?>', '<?= $p->kategori_nama ?>')">
                                <div class="product-icon">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div class="product-title"><?= $p->product_nama ?></div>
                                <div class="product-cat"><?= $p->kategori_nama ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination UI -->
                <div id="pagination_container" class="d-flex justify-content-center mt-4 gap-2">
                    <!-- Dynamic Pagination -->
                </div>

                <!-- Empty State -->
                <div id="empty_search" class="text-center py-5 text-muted" style="display:none;">
                    <i class="fas fa-search fa-2x mb-3 opacity-25"></i>
                    <p>Produk tidak ditemukan</p>
                </div>
            </div>

            <!-- STEP 2: DETAILS FORM (Hidden) -->
            <div id="step-detail" style="display:none;" class="animate-in pb-5">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <button class="btn btn-light rounded-circle" style="width:45px; height:45px;"
                        onclick="backToGrid()">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <div>
                        <h5 class="fw-bold text-primary mb-0" id="detail_title">Product Name</h5>
                        <small class="text-muted" id="detail_cat">Category</small>
                    </div>
                </div>

                <div class="report-section-card">
                    <div class="section-body">
                        <!-- Hidden Logic Fields -->
                        <input type="hidden" id="product_id">

                        <!-- Variant -->
                        <div class="mb-4">
                            <label class="form-label">Pilih Spesifikasi / Bahan</label>
                            <select id="temp_harga_id" class="form-select form-select-lg bg-light border-0">
                                <option value="">-- Memuat Varian --</option>
                            </select>
                            <input type="hidden" id="harga_id_final">
                        </div>

                        <!-- Dimensions -->
                        <div id="dim_section" style="display:none;" class="mb-4">
                            <label class="form-label">Ukuran (CM)</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-0">P</span>
                                        <input type="number" id="panjang" class="form-control bg-light border-0"
                                            value="0" step="0.01">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-0">L</span>
                                        <input type="number" id="lebar" class="form-control bg-light border-0" value="0"
                                            step="0.01">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quantity -->
                        <div id="qty_section" style="display:none;">
                            <div class="row align-items-center mb-4">
                                <div class="col-6">
                                    <label class="form-label">Jumlah (<span id="unit_display">Pcs</span>)</label>
                                    <div class="qty-input-group">
                                        <button type="button" class="qty-btn" onclick="updateQty(-1)"><i
                                                class="fa fa-minus"></i></button>
                                        <input type="number" id="qty" class="form-control text-center p-0 fw-bold"
                                            value="1" min="1" oninput="updatePrice()">
                                        <button type="button" class="qty-btn" onclick="updateQty(1)"><i
                                                class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                                <div class="col-6 text-end">
                                    <small class="text-muted d-block">Harga Satuan</small>
                                    <div class="fw-bold" id="item_price_display">Rp 0</div>
                                </div>
                            </div>

                            <!-- Total & Cart -->
                            <div
                                class="alert alert-primary border-0 d-flex justify-content-between align-items-center mb-0">
                                <div>
                                    <small class="text-uppercase fw-bold opacity-75">Total Estimasi</small>
                                    <div class="h3 fw-bold mb-0" id="item_total_display">Rp 0</div>
                                </div>
                                <input type="hidden" id="subtotal_hidden" value="0">
                            </div>

                            <!-- Notes -->
                            <div class="mt-4">
                                <label class="form-label">Catatan Tambahan</label>
                                <textarea id="catatan" class="form-control bg-light border-0" rows="2"
                                    placeholder="Finishing, Mata Ayam, Lipat..."></textarea>
                                <div class="catatan-hints">
                                    <div class="hint-chip" onclick="addNote('Potong Kotak')">+ Potong</div>
                                    <div class="hint-chip" onclick="addNote('Mata Ayam')">+ Mata Ayam</div>
                                    <div class="hint-chip" onclick="addNote('Lipat')">+ Lipat</div>
                                    <div class="hint-chip" onclick="addNote('Laminasi')">+ Laminasi</div>
                                </div>
                            </div>

                            <button class="btn btn-lg btn-success w-100 py-3 mt-4 fw-bold rounded-4 shadow-sm"
                                onclick="addToCart()" id="btn_add_cart">
                                <i class="fas fa-plus-circle me-2"></i> TAMBAH KE ORDER
                            </button>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <!-- 3. CART LIST -->
        <div class="report-section-card animate-in mt-4" id="cart_section" style="display:none;">
            <div class="section-header header-green">
                <h6 class="section-title"><i class="fas fa-shopping-cart"></i> DAFTAR BELANJA (<span
                        id="cart_count">0</span>)</h6>
            </div>
            <div class="section-body p-0" id="cart_list">
                <!-- Items injected here -->
            </div>
        </div>

    </div>

    <!-- CHECKOUT BAR -->
    <div class="checkout-bar" id="checkout_bar" style="display:none;">
        <div class="container p-0" style="max-width: 900px;">
            <div class="row align-items-center">
                <div class="col-5">
                    <span class="grand-total-label">Total Estimasi</span>
                    <div class="grand-total-value" id="grand_total">Rp 0</div>
                    <small class="text-muted" style="font-size:0.65rem;">*Bayar setelah validasi file</small>
                </div>
                <div class="col-7">
                    <button class="btn btn-checkout w-100" onclick="submitOrder()">
                        KIRIM KE DESKPRINT <i class="fas fa-paper-plane ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Global Cart State
        let CART = [];
        const BRANCH_ID = '<?= $branch_id ?>';

        $(document).ready(function () {
            // Konsumen Search Autocomplete
            $("#nama_konsumen").autocomplete({
                source: "<?= site_url('kiosk/search_konsumen') ?>",
                minLength: 2,
                classes: { "ui-autocomplete": "shadow-lg border-0" },
                select: function (event, ui) {
                    event.preventDefault();
                    $("#nama_konsumen").val(ui.item.value); // Name
                    $("#no_wa").val(ui.item.phone).prop('readonly', true).addClass('text-success fw-bold');
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        icon: 'success',
                        title: 'Konsumen: ' + ui.item.value,
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
            $("#nama_konsumen").on('input', function () {
                if ($("#no_wa").prop('readonly')) {
                    $("#no_wa").prop('readonly', false).removeClass('text-success fw-bold').val('');
                }
            });

            $('#panjang, #lebar').on('input', updatePrice);

            // On Variant Change
            $('#temp_harga_id').change(function () {
                if ($(this).val()) {
                    $('#qty_section').slideDown();
                    updatePrice();
                }
            });
        });

        // --- PAGINATION & FILTER LOGIC ---
        let currentCat = 'all';
        let itemsPerPage = 12;
        let currentPage = 1;

        $(window).on('load resize', function () {
            itemsPerPage = $(window).width() < 576 ? 12 : 18;
            applyPagination();
        });

        function filterCat(cat, btn) {
            currentCat = cat;
            $('.cat-pill').removeClass('active');
            $(btn).addClass('active');

            // Clear search if switching category
            $('#product_search').val('');
            $('#btn_clear_search').hide();
            $('#empty_search').hide();

            currentPage = 1;
            applyPagination();
        }

        function searchProduct(term) {
            term = term.toLowerCase();

            if (term.length > 0) {
                $('#btn_clear_search').show();
            } else {
                $('#btn_clear_search').hide();
            }

            currentPage = 1;
            applyPagination();
        }

        function applyPagination() {
            let term = $('#product_search').val().toLowerCase();
            let $items = $('.product-item');
            let filteredItems = [];

            // 1. Identify items that match category AND search term
            $items.each(function () {
                let cat = $(this).data('cat');
                let name = $(this).data('name') || '';
                let matchCat = (currentCat === 'all' || cat === currentCat);
                let matchSearch = (term === '' || name.includes(term));

                if (matchCat && matchSearch) {
                    filteredItems.push(this);
                }
                $(this).hide(); // Initially hide all
            });

            // 2. Calculate pagination
            let totalItems = filteredItems.length;
            let totalPages = Math.ceil(totalItems / itemsPerPage);

            if (totalItems === 0) {
                $('#empty_search').show();
                $('#pagination_container').hide();
                return;
            } else {
                $('#empty_search').hide();
                $('#pagination_container').show();
            }

            // Boundary checks
            if (currentPage < 1) currentPage = 1;
            if (currentPage > totalPages) currentPage = totalPages;

            // 3. Show only items for current page
            let start = (currentPage - 1) * itemsPerPage;
            let end = start + itemsPerPage;

            $(filteredItems.slice(start, end)).show();

            // 4. Render Pagination Buttons
            renderPaginationUI(totalPages);
        }

        function renderPaginationUI(totalPages) {
            let html = '';
            if (totalPages <= 1) {
                $('#pagination_container').html('');
                return;
            }

            // Prev Button
            html += `<button class="btn btn-white border ${currentPage === 1 ? 'disabled opacity-25' : ''}" onclick="changePage(${currentPage - 1})"><i class="fas fa-chevron-left"></i></button>`;

            // Page Numbers (Smart limited view)
            let startPage = Math.max(1, currentPage - 1);
            let endPage = Math.min(totalPages, startPage + 2);
            if (endPage - startPage < 2) startPage = Math.max(1, endPage - 2);

            for (let i = startPage; i <= endPage; i++) {
                html += `<button class="btn ${i === currentPage ? 'btn-primary' : 'btn-white border'}" onclick="changePage(${i})">${i}</button>`;
            }

            // Next Button
            html += `<button class="btn btn-white border ${currentPage === totalPages ? 'disabled opacity-25' : ''}" onclick="changePage(${currentPage + 1})"><i class="fas fa-chevron-right"></i></button>`;

            $('#pagination_container').html(html);
        }

        function changePage(page) {
            currentPage = page;
            applyPagination();
            // Scroll to top of grid
            $('html, body').animate({
                scrollTop: $("#product_grid").offset().top - 100
            }, 300);
        }

        function clearSearch() {
            $('#product_search').val('').trigger('input');
        }

        function selectProduct(pid, name, satuan, cat) {
            // Reset Fields
            $('#product_id').val(pid);
            $('#detail_title').text(name);
            $('#detail_cat').text(cat);
            $('#unit_display').text(satuan);

            $('#qty').val(1);
            $('#panjang, #lebar, #subtotal_hidden').val(0);
            $('#catatan').val('');
            $('#item_total_display').text('Rp 0');
            $('#item_price_display').text('Rp 0');
            $('#qty_section').hide();

            // Handle Dimensions UI
            var nonDimUnits = ['pcs', 'lembar', 'lbr', 'rim', 'kodi', 'box', 'unit', 'paket', 'set', 'buku'];
            if (nonDimUnits.includes(satuan.toLowerCase())) {
                $('#dim_section').hide();
                $('#panjang, #lebar').val(0);
            } else {
                $('#dim_section').show();
            }

            // Load Variants
            $('#temp_harga_id').html('<option value="">Memuat Varian...</option>').prop('disabled', true);

            // Switch View
            $('#step-product').fadeOut(200, function () {
                $('#step-detail').fadeIn(200);
            });

            // Fetch AJAX
            $.post('<?= site_url('kiosk/get_variants') ?>', { product_id: pid }, function (data) {
                var variants = JSON.parse(data);
                var html = '<option value="">-- Pilih Spesifikasi --</option>';
                if (variants.length === 0) html = '<option value="" disabled>Tidak ada varian</option>';

                var seen = {};
                variants.forEach(function (v) {
                    var sideInfo = '';
                    if (v.ket_1 && v.ket_1 != '0') {
                        var isNum = /^\d+$/.test(v.ket_1);
                        sideInfo = ' (' + v.ket_1 + (isNum ? ' Sisi' : '') + ')';
                    }
                    var uniqueKey = v.detail_product + '###' + v.ket_1;
                    if (!seen[uniqueKey]) {
                        html += '<option value="' + v.harga_id + '">' + v.detail_product + sideInfo + '</option>';
                        seen[uniqueKey] = true;
                    }
                });
                $('#temp_harga_id').html(html).prop('disabled', false);
            });
        }

        function backToGrid() {
            $('#step-detail').fadeOut(200, function () {
                $('#step-product').fadeIn(200);
                $('#product_id').val(''); // Clear selection
                applyPagination(); // Refresh view
            });
        }

        function updateQty(change) {
            let el = $('#qty');
            let val = parseInt(el.val()) || 0;
            if (val + change >= 1) {
                el.val(val + change);
                updatePrice();
            }
        }

        function updatePrice() {
            var pid = $('#product_id').val();
            var hid = $('#temp_harga_id').val();
            if (!pid || !hid) return;

            // Show loading
            $('#item_total_display').text('...');

            $.ajax({
                url: '<?= site_url('kiosk/get_price') ?>',
                method: 'POST',
                dataType: 'json', // Auto parse JSON
                data: {
                    product_id: pid,
                    harga_id: hid,
                    qty: $('#qty').val(),
                    panjang: $('#panjang').val(),
                    lebar: $('#lebar').val()
                },
                success: function (res) {
                    if (res.status == 'success') {
                        $('#item_total_display').text(res.formatted_total);
                        $('#item_price_display').text('Rp ' + new Intl.NumberFormat('id-ID').format(res.base_price));
                        $('#subtotal_hidden').val(res.total_estimate);
                        if (res.harga_id) $('#harga_id_final').val(res.harga_id);
                    } else {
                        // Backend Error
                        $('#item_total_display').text('Error');
                        Swal.fire('Info', res.msg || 'Harga tidak ditemukan', 'info');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', error);
                    $('#item_total_display').text('Koneksi Error');
                }
            });
        }

        function addNote(text) {
            let curr = $('#catatan').val();
            $('#catatan').val(curr + (curr ? ', ' : '') + text);
        }

        function addToCart() {
            // Validation
            if (!$('#product_id').val() || !$('#temp_harga_id').val()) {
                Swal.fire('Ups', 'Pilih spesifikasi dulu', 'warning');
                return;
            }
            if (!$('#nama_konsumen').val()) {
                Swal.fire('Data Diri', 'Mohon isi Nama Pelanggan dulu', 'warning');
                // Scroll up
                // Better: show prompt
                Swal.fire({
                    title: 'Siapa nama Anda?',
                    input: 'text',
                    inputLabel: 'Nama Pelanggan',
                    inputPlaceholder: 'Masukkan nama...',
                    showCancelButton: true
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        $('#nama_konsumen').val(result.value);
                        addToCart(); // Retry
                    }
                });
                return;
            }

            // Build Item Object
            let qty = parseFloat($('#qty').val());
            let subtotal = parseFloat($('#subtotal_hidden').val());

            let item = {
                product_id: $('#product_id').val(),
                product_name: $('#detail_title').text(),
                harga_id_ref: $('#temp_harga_id').val(),
                harga_id: $('#harga_id_final').val(), // Dynamic ID
                variant_name: $('#temp_harga_id option:selected').text(),
                qty: qty,
                panjang: parseFloat($('#panjang').val()) || 0,
                lebar: parseFloat($('#lebar').val()) || 0,
                subtotal: subtotal,
                catatan: $('#catatan').val()
            };

            // Add to Global Cart
            CART.push(item);

            // Go back to grid
            backToGrid();

            // Render
            renderCart();

            // Smooth Scroll to Result
            setTimeout(() => {
                $('html, body').animate({
                    scrollTop: $("#cart_section").offset().top - 100
                }, 500);
            }, 300);

            Swal.fire({
                toast: true, position: 'top', icon: 'success',
                title: 'Berhasil Ditambahkan', showConfirmButton: false, timer: 1000
            });
        }

        function renderCart() {
            let html = '';
            let grandTotal = 0;

            if (CART.length > 0) {
                $('#cart_section').show(); // Ensure cart is shown
                setTimeout(() => $('#checkout_bar').slideDown(), 200);
            } else {
                $('#cart_section').hide();
                $('#checkout_bar').slideUp();
            }

            CART.forEach((item, idx) => {
                grandTotal += item.subtotal;

                let dimInfo = '';
                if (item.panjang > 0 && item.lebar > 0) {
                    dimInfo = `<span class="badge bg-light text-dark border me-1">${item.panjang}x${item.lebar} cm</span>`;
                }

                html += `
                <div class="cart-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="cart-title">${item.product_name}</div>
                            <div class="cart-meta mb-1">${item.variant_name}</div>
                            <div class="mb-1">
                                <span class="badge bg-primary me-1">${item.qty} Pcs</span>
                                ${dimInfo}
                            </div>
                            ${item.catatan ? `<div class="cart-meta fst-italic mt-1 text-wrap" style="font-size:0.75rem;"><i class="fa fa-pencil-alt me-1"></i> ${item.catatan}</div>` : ''}
                        </div>
                        <div class="text-end ps-2">
                            <div class="cart-price mb-2">Rp ${new Intl.NumberFormat('id-ID').format(item.subtotal)}</div>
                            <button class="btn btn-sm btn-outline-danger border-0" onclick="deleteItem(${idx})">
                                <i class="fa fa-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>`;
            });

            $('#cart_list').html(html);
            $('#cart_count').text(CART.length);
            $('#grand_total').text('Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal));
        }

        function deleteItem(idx) {
            CART.splice(idx, 1);
            renderCart();
        }

        function submitOrder() {
            if (CART.length === 0) return;

            Swal.fire({
                title: 'Kirim ke Deskprint?',
                html: "Estimasi Total: <b>" + $('#grand_total').text() + "</b><br><small>Pembayaran dilakukan setelah pemeriksaan file.</small>",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                confirmButtonText: 'Ya, Kirim!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Mengirim...', didOpen: () => Swal.showLoading() });

                    let payload = {
                        customer: {
                            nama: $('#nama_konsumen').val(),
                            wa: $('#no_wa').val(),
                            branch_id: BRANCH_ID
                        },
                        items: CART
                    };

                    fetch('<?= site_url('kiosk/submit_cart') ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    })
                        .then(r => r.json())
                        .then(res => {
                            if (res.status == 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Pesanan Terkirim!',
                                    html: 'Nomor Referensi: <b>' + res.ref + '</b><br>Silakan tunggu dipanggil oleh kasir',
                                    confirmButtonColor: '#0f172a',
                                    timer: 3000,
                                    timerProgressBar: true
                                }).then(() => {
                                    // Reload halaman untuk order berikutnya
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire('Gagal', res.msg, 'error');
                            }
                        }).catch((err) => {
                            console.error(err);
                            Swal.fire('Error', 'Terjadi kesalahan jaringan', 'error');
                        });
                }
            });
        }
    </script>
</body>

</html>