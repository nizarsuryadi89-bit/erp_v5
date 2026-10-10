<style>
    /* Fix Select2 z-index in nested modals */
    .select2-container--open {
        z-index: 9999999 !important;
    }

    /* ================================================================
     * PRODUCT CATALOG – Visual theme aligned with Warehouse module
     * ================================================================ */
    :root {
        --primary-gradient: linear-gradient(135deg, #1a1c23 0%, #2c3e50 100%);
        --success-gradient: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
        --info-gradient: linear-gradient(135deg, #4895ef 0%, #4cc9f0 100%);
        --warning-gradient: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);
        --danger-gradient: linear-gradient(135deg, #f72585 0%, #b5179e 100%);
        --slate-muted: #64748b;
        --slate-dark: #1e293b;
        --accent-blue: #6366f1;
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .product-catalog-shell {
        background-color: #f8fafc;
    }

    /* ---------- Hero Header ---------- */
    .product-header-section {
        background: #ffffff;
        color: #111827;
        padding: 2rem 0 3rem 0;
        margin-bottom: 0;
        border-radius: 0;
        box-shadow: none;
        border-bottom: 1px solid #e5e7eb;
        position: relative;
        overflow: visible;
    }

    .product-header-section::after {
        display: none;
    }

    .header-content {
        position: relative;
        z-index: 10;
        display: flex;
        align-items: center;
        gap: 1.75rem;
    }

    .header-actions {
        position: relative;
        z-index: 200;
    }

    .header-actions .dropdown-menu {
        z-index: 1050 !important;
    }

    .header-icon {
        width: 68px;
        height: 68px;
        background: #f8fafc;
        border-radius: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        border: 1px solid #e2e8f0;
        color: #111827;
    }

    .header-title {
        font-size: 2.2rem;
        font-weight: 900;
        margin: 0;
        letter-spacing: -0.5px;
    }

    .header-subtitle {
        font-size: 0.85rem;
        font-weight: 700;
        margin: 0 0 4px 0;
        opacity: 0.65;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #94a3b8;
    }

    /* ---------- Metric cards (float up from header) ---------- */
    .stats-container {
        margin-top: -3.5rem;
        position: relative;
        z-index: 10;
        margin-bottom: 2rem;
    }

    .metric-card {
        background: white;
        border-radius: 0;
        padding: 1.4rem 1.5rem;
        box-shadow: none;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
        height: 100%;
    }

    .metric-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        border-color: var(--accent-blue);
    }

    .metric-header {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .metric-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: white;
    }

    .metric-info p {
        font-size: 0.72rem;
        color: var(--slate-muted);
        margin: 0;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .metric-info h3 {
        font-size: 1.75rem;
        font-weight: 900;
        margin: 0;
        color: var(--slate-dark);
    }

    .bg-soft-blue {
        background: #eff6ff;
        color: #3b82f6;
    }

    .bg-soft-emerald {
        background: #ecfdf5;
        color: #10b981;
    }

    .bg-soft-amber {
        background: #fffbeb;
        color: #f59e0b;
    }

    .bg-soft-purple {
        background: #f5f3ff;
        color: #8b5cf6;
    }

    .bg-soft-rose {
        background: #fff1f2;
        color: #f43f5e;
    }

    /* ---------- Content card ---------- */
    .content-card {
        background: white;
        border-radius: 0;
        padding: 2rem;
        box-shadow: none;
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }

    /* ---------- Controls (search bar area) ---------- */
    .product-catalog-shell .form-control-premium {
        border-radius: 14px;
        padding: 0.75rem 1.25rem;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        font-weight: 500;
        transition: all 0.2s;
    }

    .product-catalog-shell .form-control-premium:focus {
        background: white;
        border-color: var(--accent-blue);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .product-catalog-shell .form-control,
    .product-catalog-shell .form-select,
    .product-catalog-shell .input-group-text,
    .product-catalog-shell .form-check-input,
    .product-catalog-shell .page-link,
    .product-catalog-shell .pagination .page-item .page-link {
        border-radius: 0 !important;
    }

    .product-catalog-shell .form-control,
    .product-catalog-shell .form-select {
        border: 1px solid #e2e8f0;
        padding: 0.65rem 1rem;
        font-size: 0.875rem;
        color: var(--slate-dark);
        background: #f8fafc;
        transition: all 0.2s;
    }

    .product-catalog-shell .form-control:focus,
    .product-catalog-shell .form-select:focus {
        background: white;
        border-color: var(--accent-blue);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.08);
    }

    .alphabet-filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        padding-top: 1rem;
        margin-top: 1rem;
        border-top: 1px solid #eef2f7;
    }

    .alphabet-filter-btn {
        min-width: 38px;
        height: 38px;
        padding: 0 0.75rem;
        border-radius: 6px;
        border: 1px solid #dbe4ee;
        background: #f8fafc;
        color: var(--slate-dark);
        font-size: 0.78rem;
        font-weight: 800;
        line-height: 1;
    }

    .page-link {
        border-radius: 0 !important;
        min-width: 32px;
        text-align: center;
    }

    .alphabet-filter-btn:hover {
        background: #eef4ff;
        border-color: #93c5fd;
        color: #1d4ed8;
    }

    .alphabet-filter-btn.active {
        background: var(--primary-gradient);
        border-color: transparent;
        color: white;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.14);
    }

    .product-control-shell {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
        width: 100%;
    }

    .product-control-shell .input-group {
        height: 48px;
    }

    .product-control-shell .input-group-text {
        background: #fff;
        border: 1px solid #dfe7f1;
        border-right: 0;
        color: #64748b;
        height: 48px;
        padding: 0 0.85rem;
    }

    .product-control-shell .form-control,
    .product-control-shell .form-select {
        height: 48px;
        min-height: 48px;
        border: 1px solid #dfe7f1;
        background: #f8fafc;
        color: #1e293b;
        box-shadow: none;
        font-size: 0.82rem;
        font-weight: 600;
    }

    .product-control-shell .form-control {
        border-left: 0;
    }

    .product-control-shell .form-select {
        width: 150px;
        min-width: 150px;
    }

    .product-control-shell .btn-group {
        border: 1px solid #dfe7f1;
        background: #fff;
        overflow: hidden;
        height: 48px;
    }

    .product-control-shell .btn-group .btn {
        height: 48px;
        padding: 0.7rem 1rem;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        color: #334155;
        background: #fff;
        border-color: #dfe7f1;
    }

    .product-view-switch {
        display: inline-flex;
        border: 1px solid #dfe7f1;
        background: #f8fafc;
        overflow: hidden;
        height: 48px;
        margin-left: auto;
    }

    .product-view-btn {
        border: 0;
        background: transparent;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        padding: 0.7rem 0.95rem;
        text-transform: uppercase;
        transition: all 0.2s ease;
    }

    .product-view-btn.active {
        background: #0f172a;
        color: #fff;
    }

    .product-data-view {
        display: none;
    }

    .product-data-view.active {
        display: block;
    }

    .product-card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1rem;
    }

    .product-card-item {
        background: #fff;
        border: 1px solid #e2e8f0;
        padding: 1rem;
        min-height: 220px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .product-card-image-wrap {
        width: 100%;
        height: 150px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background: linear-gradient(135deg, #f8fafc, #eef2ff);
        margin-bottom: 0.75rem;
    }

    .product-card-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        background-color: #f8fafc;
    }

    .product-card-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }

    .product-card-id {
        font-size: 0.68rem;
        font-weight: 800;
        color: #64748b;
        letter-spacing: 0.08em;
    }

    .product-card-status {
        display: inline-flex;
        align-items: center;
        padding: 0.2rem 0.5rem;
        font-size: 0.6rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        border: 1px solid rgba(15, 23, 42, 0.08);
    }

    .product-card-name {
        font-size: 1rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 0.5rem 0;
    }

    .product-card-meta {
        font-size: 0.75rem;
        color: #475569;
        line-height: 1.7;
    }

    .product-card-price {
        font-size: 1rem;
        font-weight: 900;
        color: #111827;
        margin-top: 0.75rem;
    }

    /* ---------- Buttons ---------- */
.product-catalog-shell .btn {
        border-radius: 4px;
        padding: 0.6rem 1.25rem;
        font-weight: 700;
        font-size: 0.8rem;
        letter-spacing: 0.3px;
        transition: all 0.2s ease;
    }

    .btn-premium {
        border-radius: 4px;
        padding: 0.7rem 1.5rem;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 1px;
    }

    /* Default btn-group styling applies */

    .btn-premium-primary {
        background: var(--primary-gradient);
        border: none;
        color: white;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .btn-premium-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        color: white;
    }

    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }

    /* ---------- Premium table ---------- */
    .product-catalog-shell .table-premium thead th {
        background: #f8fafc;
        border: none;
        color: var(--slate-muted);
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 1px;
        padding: 1.2rem 1rem;
    }

    .product-catalog-shell .table thead th {
        background: #1a1c23;
        color: white;
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        padding: 1rem 1rem;
        border: none;
        vertical-align: middle;
    }

    .product-catalog-shell .table tbody td {
        padding: 1rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: var(--slate-dark);
        font-size: 0.9rem;
        font-weight: 500;
    }

    .product-catalog-shell .table-hover tbody tr:hover {
        background-color: #f8fafc !important;
        cursor: pointer;
    }

    /* ---------- Status / jenis badges ---------- */
    .status-badge {
        font-size: 0.58rem;
        font-weight: 700;
        padding: 0.12rem 0.45rem;
        border-radius: 4px;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }

    .setup-status-group {
        display: inline-flex;
        align-items: stretch;
        border: 1px solid rgba(15, 23, 42, 0.08);
        background: #fff;
        overflow: hidden;
        box-shadow: none;
        border-radius: 6px;
    }

    .setup-status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 24px;
        padding: 0.38rem 0.62rem;
        font-size: 0.58rem;
        line-height: 1;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        white-space: nowrap;
        border-right: 1px solid rgba(15, 23, 42, 0.08);
    }

    .setup-status-pill:last-child {
        border-right: none;
    }

    /* ---------- Feature Indicators (Price / BOM / Routing) ---------- */
    .setup-container {
        display: flex;
        gap: 8px;
        justify-content: center;
        align-items: center;
    }

    .feature-indicator {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #f8fafc;
        color: #94a3b8;
        border: 1.5px solid #e2e8f0;
        position: relative;
    }

    .feature-indicator i {
        font-size: 1rem;
        margin-bottom: -2px;
    }

    .feature-indicator .count-badge {
        position: absolute;
        top: -6px;
        right: -6px;
        background: #64748b;
        color: white;
        font-size: 0.6rem;
        padding: 2px 5px;
        border-radius: 6px;
        border: 2px solid white;
        min-width: 18px;
        font-weight: 800;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .indicator-price.has-data {
        background: #eff6ff;
        color: #2563eb;
        border-color: #bfdbfe;
    }

    .indicator-price.has-data .count-badge {
        background: #2563eb;
    }

    .indicator-bom.has-data {
        background: #fffbeb;
        color: #d97706;
        border-color: #fef3c7;
    }

    .indicator-bom.has-data .count-badge {
        background: #d97706;
    }

    .indicator-routing.has-data {
        background: #f5f3ff;
        color: #4f46e5;
        border-color: #ddd6fe;
    }

    .indicator-routing.has-data .count-badge {
        background: #4f46e5;
    }

    .feature-indicator:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.12);
        z-index: 10;
    }

    .feature-indicator.has-data:hover {
        border-color: currentColor;
    }

    .feature-indicator:not(.has-data) {
        opacity: 0.55;
        border-style: dashed;
    }

    /* ---------- Product name link hover ---------- */
    .hover-primary:hover {
        color: var(--accent-blue) !important;
        transition: color 0.2s ease;
    }

    /* ---------- Select2 ---------- */
    .product-catalog-shell .select2-container {
        z-index: 999999 !important;
    }

    .product-catalog-shell .select2-dropdown {
        z-index: 999999 !important;
    }

    .product-catalog-shell .select2-container--default .select2-selection--single {
        height: 42px !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        background: #f8fafc !important;
    }

    .product-catalog-shell .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px !important;
        padding-left: 12px !important;
        color: var(--slate-dark) !important;
    }

    .product-catalog-shell .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }

    .product-catalog-shell .select2-search__field {
        height: auto !important;
        padding: 8px !important;
    }

    .product-catalog-shell .select2-search--dropdown {
        display: block !important;
        padding: 4px !important;
    }

    /* ---------- Mini Step Tracker (Inside Modals) ---------- */
    .mini-sop-tracker {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
        background: #f8fafc;
        padding: 1rem;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
    }

    .mini-step {
        display: flex;
        align-items: center;
        gap: 8px;
        opacity: 0.4;
        transition: all 0.3s;
    }

    .mini-step.active {
        opacity: 1;
    }

    .mini-step.active .step-num {
        background: #2563eb;
        color: white;
        border-color: #2563eb;
    }

    .mini-step.completed .step-num {
        background: #10b981;
        color: white;
        border-color: #10b981;
    }

    .mini-step .step-num {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 900;
    }

    .mini-step .step-text {
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .step-line {
        flex: 1;
        height: 2px;
        background: #e2e8f0;
        align-self: center;
        margin: 0 10px;
    }

    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            text-align: center;
        }

        .stats-container {
            margin-top: -2rem;
        }
    }

    /* ---------- SOP Progress Bar ---------- */
    .product-sop-progressive {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        display: flex;
        overflow: hidden;
        margin-bottom: 2rem;
        border: 1px solid #f1f5f9;
        height: 100px;
    }

    .sop-hero {
        background: #2563eb;
        color: white;
        padding: 0 2rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        width: 25%;
        position: relative;
    }

    .sop-hero h4 {
        margin: 0;
        font-weight: 900;
        letter-spacing: 1px;
        font-size: 1.1rem;
    }

    .sop-hero p {
        margin: 4px 0 0 0;
        font-size: 0.75rem;
        opacity: 0.8;
    }

    .sop-step-item {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        text-align: center;
        padding: 0 10px;
        transition: all 0.3s;
        border-right: 1px solid #f1f5f9;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
    }

    .sop-step-item:last-child {
        border-right: none;
    }

    .sop-step-item:hover {
        background: #f8fafc;
    }

    .sop-number {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #eff6ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 0.8rem;
        margin-bottom: 8px;
        border: 1px solid #dbeafe;
    }

    .sop-label {
        font-size: 0.75rem;
        font-weight: 800;
        color: #1e293b;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    /* ---------- Interactive Metric Cards ---------- */
    .metric-card.clickable {
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid transparent;
    }

    .metric-card.clickable:hover {
        transform: translateY(-5px);
        background: #ffffff;
        box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.1);
        border-color: #e2e8f0;
    }

    .metric-card.clickable:active {
        transform: translateY(-2px);
    }

    .sop-sublabel {
        font-size: 0.65rem;
        color: #64748b;
        font-weight: 500;
    }

    .sop-arrow {
        position: absolute;
        right: -8px;
        top: 50%;
        transform: translateY(-50%);
        color: #cbd5e1;
        font-size: 1.2rem;
        z-index: 5;
    }

    /* ---------- Sortable Headers ---------- */
    .sortable {
        cursor: pointer;
        position: relative;
        transition: background-color 0.2s;
    }

    .sortable:hover {
        background-color: #f1f5f9 !important;
        color: var(--accent-blue) !important;
    }

    .sort-icon {
        margin-left: 5px;
        font-size: 0.8rem;
        opacity: 0.5;
    }

    .sortable.active {
        color: var(--accent-blue) !important;
    }

    .sortable.active .sort-icon {
        opacity: 1;
    }
</style>