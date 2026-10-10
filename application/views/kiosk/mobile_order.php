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
            --primary: #1e3a8a;
            /* Navy Blue - ERP Theme */
            --primary-dark: #1e40af;
            --primary-light: #3b82f6;
            --accent: #60a5fa;
            /* Bright Blue Accent */
            --accent-dark: #2563eb;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --navy-gradient: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #2563eb 100%);
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
            border-radius: var(--radius-md);
            border: 2px solid #e2e8f0;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .product-card:active {
            transform: scale(0.96);
            border-color: var(--accent);
            background-color: #eff6ff;
        }

        .product-icon {
            font-size: 2rem;
            color: var(--accent);
            margin-bottom: 15px;
            background: #eff6ff;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-title {
            font-weight: 700;
            font-size: 1rem;
            line-height: 1.3;
            color: var(--primary);
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
            box-shadow: 0 4px 10px rgba(30, 58, 138, 0.3);
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
            background: var(--navy-gradient);
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .section-header.header-dark {
            background: linear-gradient(to right, #0f172a, #1e293b);
        }

        .section-header.header-green {
            background: linear-gradient(to right, #10b981, #059669);
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
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            width: 100%;
            padding: 15px;
            border-radius: 12px;
            font-weight: 800;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
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

            .badge.bg-warning,
            .badge.bg-success {
                font-size: 0.65rem !important;
                padding: 4px 8px !important;
            }

            p.text-muted.small {
                font-size: 0.7rem !important;
            }

            /* Queue Info Banner Mobile */
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

            .alert .small {
                font-size: 0.75rem !important;
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

            /* Product Grid - Show 2 columns on mobile */
            .product-item.col-6 {
                flex: 0 0 50%;
                max-width: 50%;
            }

            .product-card {
                padding: 12px;
            }

            .product-icon {
                width: 45px;
                height: 45px;
                font-size: 1.5rem;
                margin-bottom: 10px;
            }

            .product-title {
                font-size: 0.85rem;
                line-height: 1.2;
            }

            .product-cat {
                font-size: 0.65rem;
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

            .badge.bg-warning {
                font-size: 0.8rem !important;
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
                /* Hide queue info banner in landscape to save space */
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

            .badge.bg-warning,
            .badge.bg-success {
                font-size: 0.6rem !important;
                padding: 3px 6px !important;
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

        /* ===== PROMOTIONAL BANNERS & ADS ===== */

        /* Promo Card Styles */
        .promo-banner {
            border-radius: var(--radius-md);
            overflow: hidden;
            margin-bottom: 20px;
            position: relative;
            box-shadow: 0 8px 20px -5px rgba(0, 0, 0, 0.15);
            animation: slideInUp 0.6s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .promo-gradient-red {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 50%, #b91c1c 100%);
            color: white;
        }

        .promo-gradient-navy {
            background: var(--navy-gradient);
            color: white;
        }

        .promo-gradient-purple {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 50%, #6d28d9 100%);
            color: white;
        }

        .promo-gradient-blue {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%);
            color: white;
        }

        .promo-gradient-green {
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            color: white;
        }

        .promo-content {
            padding: 20px;
            position: relative;
            z-index: 2;
        }

        .promo-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        .promo-title {
            font-size: 1.4rem;
            font-weight: 800;
            margin-bottom: 8px;
            line-height: 1.2;
        }

        .promo-subtitle {
            font-size: 0.9rem;
            opacity: 0.95;
            margin-bottom: 0;
        }

        .promo-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 4rem;
            opacity: 0.15;
            z-index: 1;
        }

        /* Flash Sale Banner */
        .flash-sale-banner {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            border-radius: var(--radius-md);
            padding: 15px 20px;
            margin-bottom: 20px;
            box-shadow: 0 8px 20px -5px rgba(245, 158, 11, 0.4);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 8px 20px -5px rgba(245, 158, 11, 0.4);
            }

            50% {
                box-shadow: 0 8px 30px -5px rgba(245, 158, 11, 0.6);
            }
        }

        .flash-badge {
            background: #dc2626;
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
            animation: blink 1.5s ease-in-out infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        /* Feature Highlights */
        .feature-scroll {
            overflow-x: auto;
            white-space: nowrap;
            padding-bottom: 10px;
            margin-bottom: 20px;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .feature-scroll::-webkit-scrollbar {
            display: none;
        }

        .feature-card {
            display: inline-block;
            background: white;
            border-radius: 12px;
            padding: 15px;
            margin-right: 12px;
            width: 160px;
            border: 2px solid #e2e8f0;
            vertical-align: top;
            white-space: normal;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.12);
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .feature-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .feature-desc {
            font-size: 0.7rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        /* Tips Banner */
        .tips-banner {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            border-radius: var(--radius-md);
            padding: 15px;
            margin-bottom: 20px;
            color: white;
            border-left: 4px solid #ecfeff;
        }

        .tips-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-right: 12px;
        }

        /* ===== SOCIAL MEDIA BANNER ===== */

        .social-banner {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            border-radius: var(--radius-md);
            padding: 25px 20px;
            margin-bottom: 20px;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 20px -5px rgba(30, 58, 138, 0.4);
        }

        .social-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .social-content {
            position: relative;
            z-index: 2;
        }

        .social-title {
            font-size: 1.2rem;
            font-weight: 800;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .social-subtitle {
            font-size: 0.85rem;
            opacity: 0.9;
            margin-bottom: 20px;
        }

        .social-icons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }

        .social-icon-btn {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .social-icon-btn:hover {
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            color: white;
        }

        .social-icon-btn.instagram {
            background: linear-gradient(135deg, #833ab4, #fd1d1d, #fcb045);
        }

        .social-icon-btn.facebook {
            background: #1877f2;
        }

        .social-icon-btn.tiktok {
            background: linear-gradient(135deg, #00f2ea, #ff0050);
        }

        .social-icon-btn.youtube {
            background: #ff0000;
        }

        .social-icon-btn.twitter {
            background: #1da1f2;
        }

        .social-icon-btn.linkedin {
            background: #0077b5;
        }

        /* Badge pulse animation */
        .social-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-right: 8px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: badgePulse 2s ease-in-out infinite;
        }

        @keyframes badgePulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        /* ===== PAGINATION ===== */

        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            margin: 30px auto 20px;
            padding: 0 15px;
            max-width: 100%;
        }

        .pagination-btn {
            min-width: 48px;
            height: 48px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            background: white;
            color: var(--text-main);
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .pagination-btn:hover:not(:disabled) {
            border-color: var(--primary);
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(30, 58, 138, 0.25);
        }

        .pagination-btn.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 6px 20px rgba(30, 58, 138, 0.4);
            transform: scale(1.05);
        }

        .pagination-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
            background: #f8fafc;
        }

        .pagination-btn.prev-next {
            min-width: 52px;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        .pagination-btn.prev-next:hover:not(:disabled) {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        }

        .pagination-info {
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 600;
            margin-top: 15px;
            text-align: center;
            padding: 10px 15px;
            background: #f8fafc;
            border-radius: 10px;
            display: inline-block;
        }

        #page_buttons {
            display: flex;
            gap: 6px;
            flex-wrap: nowrap;
            overflow-x: auto;
            max-width: calc(100vw - 180px);
            scrollbar-width: none;
        }

        #page_buttons::-webkit-scrollbar {
            display: none;
        }

        /* Mobile Optimizations */
        @media (max-width: 575px) {
            .pagination-container {
                gap: 8px;
                padding: 0 10px;
                margin: 25px auto 15px;
            }

            .pagination-btn {
                min-width: 44px;
                height: 44px;
                font-size: 0.95rem;
                border-radius: 10px;
            }

            .pagination-btn.prev-next {
                min-width: 48px;
                flex-shrink: 0;
            }

            #page_buttons {
                max-width: calc(100vw - 150px);
                gap: 5px;
            }

            /* Hide middle page numbers on very small screens, show only first, last, and current */
            #page_buttons .pagination-btn:not(.active):not(:first-child):not(:last-child) {
                display: none;
            }

            #page_buttons .pagination-btn.active,
            #page_buttons .pagination-btn.active+.pagination-btn,
            #page_buttons .pagination-btn:has(+ .pagination-btn.active) {
                display: flex !important;
            }

            .pagination-info {
                font-size: 0.85rem;
                padding: 8px 12px;
                margin-top: 12px;
            }
        }

        /* Very small screens */
        @media (max-width: 375px) {
            .pagination-container {
                gap: 6px;
            }

            .pagination-btn {
                min-width: 40px;
                height: 40px;
                font-size: 0.9rem;
            }

            .pagination-btn.prev-next {
                min-width: 44px;
            }

            #page_buttons {
                max-width: calc(100vw - 130px);
            }
        }

        /* Responsive Ads */
        @media (max-width: 575px) {
            .promo-title {
                font-size: 1.1rem;
            }

            .promo-subtitle {
                font-size: 0.8rem;
            }

            .promo-icon {
                font-size: 3rem;
                right: 10px;
            }

            .promo-content {
                padding: 15px;
            }

            .feature-card {
                width: 140px;
                padding: 12px;
            }

            .social-banner {
                padding: 20px 15px;
            }

            .social-title {
                font-size: 1rem;
            }

            .social-subtitle {
                font-size: 0.75rem;
                margin-bottom: 15px;
            }

            .social-icon-btn {
                width: 45px;
                height: 45px;
                font-size: 1.3rem;
            }

            .social-icons {
                gap: 10px;
            }
        }
    </style>
</head>

<body>

    <div class="container py-4" style="max-width: 1400px;">

        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                    style="width:50px; height:50px; font-size:1.5rem;">
                    <i class="fas fa-print"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h1 class="h4 fw-bold text-primary mb-0">InstaPrint Mobile</h1>
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2 animate-in"
                            style="font-size: 0.9rem;">
                            <i class="fas fa-ticket-alt me-1"></i><?= $queue->queue_number ?>
                        </span>
                        <span class="badge bg-success rounded-pill px-3 py-2 animate-in" style="font-size: 0.75rem;">
                            <i class="fas fa-map-marker-alt me-1"></i><?= $branch_name ?>
                        </span>
                    </div>
                    <p class="text-muted small fw-bold mb-0">Order Cepat • Bayar di InstaPrint Counter</p>
                </div>
            </div>
            <a href="<?= site_url('kiosk/queue_landing?perusahaan_id=' . $branch_id) ?>"
                class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold"><i class="fas fa-undo me-1"></i>
                Batal</a>
        </div>

        <!-- Queue Info Banner -->
        <div class="alert border-0 shadow-sm mb-4 animate-in"
            style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 16px;">
            <div class="row align-items-center g-3">
                <div class="col-auto">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-mobile-alt fa-2x text-white"></i>
                    </div>
                </div>
                <div class="col text-white">
                    <h5 class="fw-bold mb-2 text-white">
                        <i class="fas fa-check-circle me-2"></i>Antrian Anda: <?= $queue->queue_number ?>
                    </h5>
                    <div class="small">
                        <strong><?= $queue->konsumen_nama ?></strong> • <?= $queue->customer_phone ?><br>
                        Lengkapi order Anda sekarang. Bayar saat dipanggil ke counter.
                    </div>
                </div>
            </div>
        </div>

        <div id="form-container">
            <!-- 1. CUSTOMER (Always Visible, Compact) -->


            <!--  EXISTING DRAFT ORDERS (if any) -->
            <?php if (!empty($existing_orders)): ?>
                <div class="report-section-card animate-in" id="existing_orders_section">
                    <div class="section-header" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <h6 class="section-title">
                            <i class="fas fa-history"></i> Pesanan Anda (<?= count($existing_orders) ?>)
                        </h6>
                    </div>
                    <div class="section-body p-0">
                        <div style="padding: 15px; background: #fffbeb; border-bottom: 2px dashed #fbbf24;">
                            <div style="font-size: 0.85rem; color: #92400e; font-weight: 600;">
                                <i class="fas fa-edit me-1"></i> Pesanan di bawah masih DRAFT
                            </div>
                            <div style="font-size: 0.75rem; color: #78350f; margin-top: 4px;">
                                Anda bisa edit atau hapus item sebelum mengirim ke Deskprint
                            </div>
                        </div>

                        <?php foreach ($existing_orders as $order): ?>
                            <div class="cart-item" data-order-id="<?= $order->id ?>">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="cart-title"><?= $order->product_nama ?></div>
                                        <div class="cart-meta mb-1"><?= $order->detail_product ?></div>
                                        <div class="mb-1">
                                            <span class="badge bg-primary me-1"><?= $order->qty ?> Pcs</span>
                                            <?php if ($order->panjang > 0 && $order->lebar > 0): ?>
                                                <span class="badge bg-light text-dark border me-1">
                                                    <?= $order->panjang ?>x<?= $order->lebar ?> cm
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($order->catatan)): ?>
                                            <div class="cart-meta fst-italic mt-1 text-wrap" style="font-size:0.75rem;">
                                                <i class="fa fa-pencil-alt me-1"></i> <?= $order->catatan ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="mt-2">
                                            <?php if ($order->status === 'submitted'): ?>
                                                <span class="badge bg-success text-white me-1">
                                                    <i class="fas fa-check-circle"></i> TERKIRIM
                                                </span>
                                                <span class="badge bg-info text-white">
                                                    <i class="fas fa-hourglass-half"></i> Menunggu Validasi
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-edit"></i> DRAFT
                                                </span>
                                            <?php endif; ?>
                                            <br>
                                            <span class="badge bg-light text-dark mt-1 border">
                                                <i class="fas fa-clock"></i>
                                                <?= date('d/m/Y H:i', strtotime($order->created_at)) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-end ps-2">
                                        <div class="cart-price mb-2">
                                            Rp <?= number_format($order->total_harga, 0, ',', '.') ?>
                                        </div>
                                        <?php if ($order->status === 'draft'): ?>
                                            <button class="btn btn-sm btn-outline-primary border-0 mb-1"
                                                onclick='editDraftItem(<?= json_encode($order) ?>)'>
                                                <i class="fa fa-edit"></i> Ubah
                                            </button>
                                            <br>
                                            <button class="btn btn-sm btn-outline-danger border-0"
                                                onclick="deleteDraftItem(<?= $order->id ?>)">
                                                <i class="fa fa-trash"></i> Hapus
                                            </button>
                                        <?php else: ?>
                                            <span class="badge bg-success text-white" style="font-size: 0.7rem; padding: 5px 10px;">
                                                <i class="fas fa-lock"></i><br>Locked
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- Draft Orders Total -->
                        <?php
                        $draft_total = 0;
                        foreach ($existing_orders as $order) {
                            $draft_total += $order->total_harga;
                        }
                        ?>
                        <div
                            style="padding: 20px 23px; background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-top: 2px solid #fbbf24;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <div
                                        style="font-size: 0.75rem; color: #78350f; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                        <i class="fas fa-calculator me-1"></i> Total Draft Orders
                                    </div>
                                    <div style="font-size: 0.7rem; color: #92400e; margin-top: 2px;">
                                        <?= count($existing_orders) ?> item • Status: BELUM TERKIRIM
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div id="draft_total_display"
                                        style="font-size: 1.5rem; font-weight: 800; color: #b45309; line-height: 1;">
                                        Rp
                                        <?= number_format($draft_total, 0, ',', '.') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-warning mb-0 p-2"
                                style="background: rgba(251, 191, 36, 0.2); border: 1px dashed #f59e0b; border-radius: 8px;">
                                <small class="text-dark" style="font-size: 0.7rem;">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Estimasi belum final.</strong> Harga dapat berubah setelah validasi file oleh
                                    Deskprint. - Silahkan Menunggu Nomor Antrian anda dipanggil
                                </small>
                            </div>
                        </div>

                        <div style="padding: 15px; background: #f8fafc; text-align: center;">
                            <button class="btn btn-primary btn-sm" onclick="loadDraftToCart()">
                                <i class="fas fa-sync-alt me-1"></i> Lanjutkan Edit Pesanan Ini
                            </button>
                            <div style="font-size: 0.7rem; color: #64748b; margin-top: 8px;">
                                Atau tambah produk baru di bawah
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- �📣 FLASH SALE BANNER -->
            <div class="flash-sale-banner">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-bolt text-white" style="font-size: 1.5rem;"></i>
                        <div>
                            <!-- <div class="flash-badge">PROMO HARI INI</div> -->
                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">Order Sekarang, Gratis Laminasi!
                            </div>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-dark"></i>
                </div>
            </div>

            <!-- 🎯 FEATURE HIGHLIGHTS -->
            <div class="feature-scroll">
                <div class="feature-card">
                    <div class="feature-icon" style="background: linear-gradient(135deg, #ec4899, #db2777);">
                        <i class="fas fa-truck text-white"></i>
                    </div>
                    <div class="feature-title">Antar Gratis</div>
                    <div class="feature-desc">Minimal order 500rb antar gratis se-kota</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                        <i class="fas fa-clock text-white"></i>
                    </div>
                    <div class="feature-title">Express 2 Jam</div>
                    <div class="feature-desc">Layanan cetak kilat siap dalam 2 jam</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="fas fa-award text-white"></i>
                    </div>
                    <div class="feature-title">Kualitas Terjamin</div>
                    <div class="feature-desc">Garansi cetak ulang jika tidak puas</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <i class="fas fa-percent text-white"></i>
                    </div>
                    <div class="feature-title">Diskon Member</div>
                    <div class="feature-desc">Dapatkan poin setiap transaksi</div>
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
                <div class="row g-3" id="product_grid">
                    <?php foreach ($products as $p): ?>
                        <div class="col-6 col-md-3 product-item animate-in" data-cat="<?= $p->kategori_nama ?>"
                            data-name="<?= strtolower($p->product_nama) ?>">
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

                <!-- Pagination Controls -->
                <div class="pagination-container" id="pagination_controls" style="display:none;">
                    <button class="pagination-btn prev-next" id="btn_prev_page" onclick="changePage(-1)">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div id="page_buttons"></div>
                    <button class="pagination-btn prev-next" id="btn_next_page" onclick="changePage(1)">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div class="text-center pagination-info" id="pagination_info" style="display:none;">
                    Menampilkan <span id="showing_start">0</span>-<span id="showing_end">0</span> dari <span
                        id="total_products">0</span> produk
                </div>

                <!-- Empty State -->
                <div id="empty_search" class="text-center py-5 text-muted" style="display:none;">
                    <i class="fas fa-search fa-2x mb-3 opacity-25"></i>
                    <p>Produk tidak ditemukan</p>
                </div>

                <!-- 💰 PROMO BANNER (After Products) -->
                <div class="promo-banner promo-gradient-purple mt-4">
                    <div class="promo-content">
                        <div class="promo-badge">PENAWARAN SPESIAL</div>
                        <div class="promo-title">Diskon 20% Cetak Brosur!</div>
                        <div class="promo-subtitle">Minimal order 1000 lembar. Berlaku sampai akhir bulan.</div>
                        <i class="fas fa-tags promo-icon"></i>
                    </div>
                </div>

                <!-- 💡 TIPS BANNER -->
                <div class="tips-banner">
                    <div class="d-flex align-items-start">
                        <div class="tips-icon">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div>
                            <div class="fw-bold mb-1" style="font-size: 0.9rem;">Tips Upload File:</div>
                            <div style="font-size: 0.75rem; opacity: 0.95;">
                                • Format PDF, JPG, PNG (max 50MB)<br>
                                • Resolusi minimal 300 DPI untuk hasil terbaik<br>
                                • Gunakan color mode CMYK untuk cetak warna
                            </div>
                        </div>
                    </div>
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

        <!-- 📞 BUTUH BANTUAN BANNER -->
        <div class="promo-banner promo-gradient-green mt-4 mb-4">
            <div class="promo-content">
                <div class="promo-badge">CUSTOMER SERVICE</div>
                <div class="promo-title">Butuh Bantuan?</div>
                <div class="promo-subtitle">Tim kami siap membantu Anda 24/7 via WhatsApp</div>
                <i class="fab fa-whatsapp promo-icon"></i>
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

        <!-- 💬 WHATSAPP CONTACT BANNER (After Cart) -->
        <div class="promo-banner promo-gradient-green mt-4" id="wa_banner" style="display:none;">
            <div class="promo-content">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="promo-badge">HUBUNGI KAMI</div>
                        <div class="promo-title mb-2">Order via WhatsApp?</div>
                        <a href="https://wa.me/6281234567890?text=Halo%20saya%20mau%20order"
                            class="btn btn-light btn-sm fw-bold" target="_blank">
                            <i class="fab fa-whatsapp me-1"></i> Chat Sekarang
                        </a>
                    </div>
                    <i class="fab fa-whatsapp" style="font-size: 4rem; opacity: 0.2;"></i>
                </div>
            </div>
        </div>

        <!-- 📱 SOCIAL MEDIA BANNER -->
        <div class="social-banner mt-4">
            <div class="social-content">
                <div class="mb-3">
                    <span class="social-badge">FOLLOW US</span>
                    <div class="social-title">Ikuti Kami di Sosial Media</div>
                    <div class="social-subtitle">Dapatkan info promo terbaru, tips & tutorial cetak, serta inspirasi
                        desain!</div>
                </div>
                <div class="social-icons">
                    <a href="https://instagram.com/deskprint.id" target="_blank" class="social-icon-btn instagram"
                        title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://facebook.com/deskprint" target="_blank" class="social-icon-btn facebook"
                        title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://tiktok.com/@deskprint" target="_blank" class="social-icon-btn tiktok"
                        title="TikTok">
                        <i class="fab fa-tiktok"></i>
                    </a>
                    <a href="https://youtube.com/@deskprint" target="_blank" class="social-icon-btn youtube"
                        title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="https://twitter.com/deskprint" target="_blank" class="social-icon-btn twitter"
                        title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://linkedin.com/company/deskprint" target="_blank" class="social-icon-btn linkedin"
                        title="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
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
        const KONSUMEN_ID = '<?= $queue->konsumen_id ?>'; // Pass konsumen_id directly
        const DRAFT_TOTAL = <?= isset($draft_total) ? $draft_total : 0 ?>; // Existing draft orders total
        const BASE_URL = '<?= base_url() ?>';

        $(document).ready(function () {
            // Konsumen Search Autocomplete
            $("#nama_konsumen").autocomplete({
                source: BASE_URL + '/kiosk/search_konsumen',
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

            // Initialize Pagination (with small delay to ensure DOM is ready)
            setTimeout(function () {
                initPagination();
            }, 100);
        });

        // --- PAGINATION LOGIC ---
        let currentPage = 1;
        const itemsPerPage = 12; // 12 products per page (3x4 on desktop, 2x6 on mobile)
        let totalPages = 1;

        function initPagination() {
            // console.log('🔄 Initializing pagination...');
            const totalProducts = $('.product-item').length;
            // console.log('📦 Total products found:', totalProducts);
            updatePagination();
        }

        function updatePagination() {
            // Get all product items that match current filter (category or search)
            let allProducts = $('.product-item');
            let visibleProducts;

            // Determine which products should be visible based on current filter
            if (currentCat === 'all' && $('#product_search').val() === '') {
                // Show all products
                visibleProducts = allProducts;
            } else {
                // Get filtered products (either by category or search)
                visibleProducts = allProducts.filter(':visible');
            }

            const totalItems = visibleProducts.length;
            // console.log('👀 Visible products:', totalItems, '| Current category:', currentCat);

            // Calculate pages
            totalPages = Math.ceil(totalItems / itemsPerPage);
            // console.log('📄 Total pages calculated:', totalPages, '(', itemsPerPage, 'items per page)');

            // Show/hide pagination based on product count
            if (totalPages <= 1) {
                // console.log('⚠️ Only 1 page or less, hiding pagination');
                $('#pagination_controls').hide();
                $('#pagination_info').hide();
                // Show all matching products if only 1 page or less
                return;
            }

            // console.log('✅ Showing pagination controls');
            $('#pagination_controls').show();
            $('#pagination_info').show();

            // Update total count
            $('#total_products').text(totalItems);

            // Show current page items
            showPage(currentPage, visibleProducts);

            // Render page buttons
            renderPageButtons();

            // Update prev/next buttons
            $('#btn_prev_page').prop('disabled', currentPage === 1);
            $('#btn_next_page').prop('disabled', currentPage === totalPages);
        }

        function showPage(page, items) {
            const start = (page - 1) * itemsPerPage;
            const end = start + itemsPerPage;

            // Hide all, then show current page
            items.hide();
            items.slice(start, end).show();

            // Update info
            $('#showing_start').text(start + 1);
            $('#showing_end').text(Math.min(end, items.length));
        }

        function renderPageButtons() {
            let html = '';
            const maxButtons = 5; // Max page buttons to show

            let startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
            let endPage = Math.min(totalPages, startPage + maxButtons - 1);

            // Adjust start if near end
            if (endPage - startPage < maxButtons - 1) {
                startPage = Math.max(1, endPage - maxButtons + 1);
            }

            // First page
            if (startPage > 1) {
                html += `<button class="pagination-btn" onclick="goToPage(1)">1</button>`;
                if (startPage > 2) {
                    html += `<span class="pagination-info">...</span>`;
                }
            }

            // Page buttons
            for (let i = startPage; i <= endPage; i++) {
                const activeClass = i === currentPage ? 'active' : '';
                html += `<button class="pagination-btn ${activeClass}" onclick="goToPage(${i})">${i}</button>`;
            }

            // Last page
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    html += `<span class="pagination-info">...</span>`;
                }
                html += `<button class="pagination-btn" onclick="goToPage(${totalPages})">${totalPages}</button>`;
            }

            $('#page_buttons').html(html);
        }

        function goToPage(page) {
            currentPage = page;
            updatePagination();

            // Scroll to top of product grid
            $('html, body').animate({
                scrollTop: $("#product_grid").offset().top - 120
            }, 300);
        }

        function changePage(direction) {
            const newPage = currentPage + direction;
            if (newPage >= 1 && newPage <= totalPages) {
                goToPage(newPage);
            }
        }

        // --- NEW GRID & FILTER LOGIC ---
        let currentCat = 'all';

        function filterCat(cat, btn) {
            currentCat = cat;
            $('.cat-pill').removeClass('active');
            $(btn).addClass('active');

            // Clear search if switching category
            $('#product_search').val('');
            $('#btn_clear_search').hide();
            $('#empty_search').hide();

            if (cat === 'all') {
                $('.product-item').show();
            } else {
                $('.product-item').hide();
                $('.product-item[data-cat="' + cat + '"]').show();
            }

            // Reset to page 1 and update pagination
            currentPage = 1;
            updatePagination();
        }

        function searchProduct(term) {
            term = term.toLowerCase();

            if (term.length > 0) {
                $('#btn_clear_search').show();
                // Disable category active state visually (optional, or kept)

                let visible = 0;
                $('.product-item').each(function () {
                    let name = $(this).data('name') || '';
                    if (name.includes(term)) {
                        $(this).show();
                        visible++;
                    } else {
                        $(this).hide();
                    }
                });

                if (visible === 0) $('#empty_search').show();
                else $('#empty_search').hide();

            } else {
                $('#btn_clear_search').hide();
                $('#empty_search').hide();
                // Restore category view
                let activeBtn = $('.cat-pill.active');
                if (activeBtn.length) {
                    let cat = activeBtn.text() === 'SEMUA' ? 'all' : activeBtn.text();
                    // Simpler: just trigger click on active pill or re-run filter logic
                    // But activeBtn text might be uppercase. better rely on currentCat variable.

                    // Actually, let's just re-run filter for currentCat
                    if (currentCat === 'all') {
                        $('.product-item').show();
                    } else {
                        $('.product-item').hide();
                        $('.product-item[data-cat="' + currentCat + '"]').show();
                    }
                }
            }

            // Reset to page 1 and update pagination after search
            currentPage = 1;
            updatePagination();
        }

        function clearSearch() {
            $('#product_search').val('').trigger('input');
        }

        function selectProduct(pid, name, satuan, cat, callback = null) {
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
            $.post(BASE_URL + '/kiosk/get_variants', { product_id: pid }, function (data) {
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

                // Callback if exists (for Edit mode)
                if (callback && typeof callback === 'function') {
                    callback();
                }
            });
        }

        function editDraftItem(item) {
            Swal.fire({
                title: 'Ubah Item?',
                text: 'Item akan dipindahkan ke form edit untuk diubah.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ubah',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Memuat...', didOpen: () => Swal.showLoading() });

                    // 1. Delete from Draft (server)
                    fetch(BASE_URL + '/kiosk/delete_draft_item', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ order_id: item.id })
                    })
                        .then(r => r.json())
                        .then(res => {
                            if (res.status == 'success') {
                                // 2. Remove from DOM
                                $('[data-order-id="' + item.id + '"]').fadeOut(300, function () { $(this).remove(); });

                                // Check count
                                if ($('#existing_orders_section .cart-item').length <= 1) {
                                    $('#existing_orders_section').fadeOut();
                                }

                                // 3. Populate Form
                                const satuan = item.satuan_nama || 'Pcs';
                                const catName = item.kategori_nama || 'Umum';

                                selectProduct(item.product_id, item.product_nama, satuan, catName, function () {
                                    // Callback after variants loaded
                                    Swal.close();

                                    // Set values
                                    $('#temp_harga_id').val(item.harga_id).trigger('change');
                                    $('#qty').val(parseFloat(item.qty));
                                    $('#panjang').val(parseFloat(item.panjang));
                                    $('#lebar').val(parseFloat(item.lebar));
                                    $('#catatan').val(item.catatan);

                                    // Trigger price update
                                    setTimeout(updatePrice, 100);

                                    Swal.fire({
                                        toast: true,
                                        position: 'top',
                                        icon: 'info',
                                        title: 'Silakan ubah item',
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                });
                            } else {
                                Swal.fire('Gagal', res.msg || 'Gagal memproses', 'error');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error', 'Terjadi kesalahan jaringan', 'error');
                        });
                }
            });
        }

        function deleteDraftItem(orderId) {
            Swal.fire({
                title: 'Hapus Item?',
                text: 'Item ini akan dihapus dari pesanan Anda',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Menghapus...', didOpen: () => Swal.showLoading() });

                    fetch(BASE_URL + '/kiosk/delete_draft_item', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ order_id: orderId })
                    })
                        .then(r => r.json())
                        .then(res => {
                            if (res.status == 'success') {
                                // Remove from DOM
                                $('[data-order-id="' + orderId + '"]').fadeOut(300, function () {
                                    $(this).remove();

                                    // Check if any items left
                                    if ($('#existing_orders_section .cart-item').length === 0) {
                                        $('#existing_orders_section').fadeOut();
                                    }
                                });

                                Swal.fire({
                                    toast: true,
                                    position: 'top',
                                    icon: 'success',
                                    title: 'Item dihapus',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            } else {
                                Swal.fire('Gagal', res.msg || 'Tidak bisa menghapus item', 'error');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error', 'Terjadi kesalahan jaringan', 'error');
                        });
                }
            });
        }

        function loadDraftToCart() {
            Swal.fire({
                icon: 'info',
                title: 'Pesanan Anda Sudah Ditampilkan',
                html: 'Silakan:<br>• Klik <b>Ubah</b> untuk mengedit item<br>• Klik <b>Hapus</b> untuk menghapus item<br>• Tambah produk baru di bawah<br>• Klik "KIRIM KE DESKPRINT" saat siap',
                confirmButtonText: 'Mengerti'
            });

            // Scroll to product selection
            $('html, body').animate({
                scrollTop: $("#step-product").offset().top - 100
            }, 500);
        }

        function backToGrid() {
            $('#step-detail').fadeOut(200, function () {
                $('#step-product').fadeIn(200);
                $('#product_id').val(''); // Clear selection
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
                url: BASE_URL + '/kiosk/get_price',
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
            let cartTotal = 0; // New cart items only

            if (CART.length > 0) {
                $('#cart_section').show(); // Ensure cart is shown
                $('#wa_banner').slideDown(); // Show WhatsApp banner
                setTimeout(() => $('#checkout_bar').slideDown(), 200);
            } else {
                $('#cart_section').hide();
                $('#wa_banner').slideUp(); // Hide WhatsApp banner

                // Show checkout bar if there are draft orders
                if (DRAFT_TOTAL > 0) {
                    setTimeout(() => $('#checkout_bar').slideDown(), 200);
                } else {
                    $('#checkout_bar').slideUp();
                }
            }

            CART.forEach((item, idx) => {
                cartTotal += item.subtotal;

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

            // Calculate combined total (draft + new cart)
            const combinedTotal = DRAFT_TOTAL + cartTotal;
            $('#grand_total').text('Rp ' + new Intl.NumberFormat('id-ID').format(combinedTotal));
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
                            branch_id: BRANCH_ID,
                            konsumen_id: KONSUMEN_ID // Send direct ID
                        },
                        items: CART
                    }

                    fetch(BASE_URL + '/kiosk/submit_cart', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    })
                        .then(r => {
                            if (!r.ok) {
                                throw new Error('HTTP error! status: ' + r.status);
                            }
                            return r.json();
                        })
                        .then(res => {
                            if (res.status == 'success') {
                                // Immediately hide cart and checkout bar
                                CART = []; // Clear cart
                                $('#cart_section').fadeOut();
                                $('#checkout_bar').fadeOut();

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Pesanan Terkirim!',
                                    html: 'Nomor Referensi: <b>' + res.ref + '</b><br>Silakan tunggu dipanggil oleh kasir',
                                    timer: 3000,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                }).then(() => {
                                    // Reload halaman untuk order berikutnya
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire('Gagal', res.msg || 'Terjadi kesalahan', 'error');
                            }
                        }).catch((err) => {
                            console.error('Submit Error:', err);
                            console.error('Error details:', err.message);
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan Jaringan',
                                html: 'Error: ' + err.message + '<br><small>Cek browser console untuk detail</small>',
                                confirmButtonText: 'OK'
                            });
                        });
                }
            });
        }
    </script>
</body>

</html>