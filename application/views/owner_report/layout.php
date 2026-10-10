<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= $report_title ?> — <?= $perusahaan ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/css/bootstrap.min.css">
    <style>
        /* ===== ROOT DESIGN TOKENS ===== */
        :root {
            --slate-dark: #1e293b;
            --slate-mid: #334155;
            --slate-muted: #64748b;
            --accent: #6366f1;
            --accent-dark: #4338ca;
            --shadow: 0 4px 24px rgba(0,0,0,.07);
        }
        * { box-sizing: border-box; }
        body {
            background: #f1f5f9;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: var(--slate-dark);
            min-height: 100vh;
            margin: 0;
        }

        /* ===== TOP BAR ===== */
        .top-bar {
            background: linear-gradient(135deg, var(--slate-dark), var(--slate-mid));
            color: white;
            padding: 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 12px rgba(0,0,0,.2);
        }
        .top-bar-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: .85rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: .75rem;
        }
        .top-bar-brand { display: flex; align-items: center; gap: 12px; }
        .top-bar-brand .logo-box {
            width: 42px; height: 42px;
            background: rgba(99,102,241,.25);
            border: 1px solid rgba(99,102,241,.4);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; color: #a5b4fc;
        }
        .top-bar-brand .company { font-size: .72rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }
        .top-bar-brand .report-name { font-size: 1rem; font-weight: 800; color: white; margin-top: 1px; }
        .top-bar-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .btn-top {
            height: 36px; padding: 0 14px;
            border-radius: 9px; font-weight: 700; font-size: .78rem;
            border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
            text-decoration: none;
        }
        .btn-print-top { background: white; color: var(--slate-dark); }
        .btn-print-top:hover { background: #f1f5f9; color: var(--slate-dark); }

        /* ===== EXPIRY BADGE ===== */
        .expiry-badge {
            background: rgba(99,102,241,.15);
            border: 1px solid rgba(99,102,241,.3);
            border-radius: 8px; padding: 4px 12px;
            font-size: .72rem; font-weight: 700; color: #a5b4fc;
            display: flex; align-items: center; gap: 5px;
        }

        /* ===== MAIN CONTENT ===== */
        .report-wrapper { max-width: 1200px; margin: 0 auto; padding: 1.5rem; }

        /* ===== CONFIDENTIAL WATERMARK ===== */
        .watermark-banner {
            background: linear-gradient(90deg, #fffbeb, #fef3c7);
            border: 1px solid #fde68a;
            border-radius: 12px;
            padding: .6rem 1.25rem;
            margin-bottom: 1.25rem;
            display: flex; align-items: center; gap: 10px;
            font-size: .82rem; font-weight: 700; color: #92400e;
        }

        /* ===== HERO ===== */
        .report-hero {
            background: linear-gradient(135deg, var(--slate-dark), var(--slate-mid));
            border-radius: 22px; padding: 2rem 2.5rem;
            margin-bottom: 1.5rem; color: white;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 1rem;
            box-shadow: 0 12px 32px rgba(30,41,59,.2);
        }
        .report-hero h1 { font-size: 1.7rem; font-weight: 900; margin: 0 0 .25rem 0; }
        .report-hero p { margin: 0; color: #94a3b8; font-size: .88rem; }
        .hero-icon {
            width: 60px; height: 60px;
            background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2);
            border-radius: 16px; display: flex; align-items: center; justify-content: center;
            font-size: 1.7rem;
        }

        /* ===== CARDS ===== */
        .r-card {
            background: white; border-radius: 20px;
            padding: 1.75rem; box-shadow: var(--shadow);
            border: 1px solid #f1f5f9; margin-bottom: 1.5rem;
        }
        .section-title {
            font-size: .95rem; font-weight: 800; color: var(--slate-dark);
            margin-bottom: 1.25rem; display: flex; align-items: center; gap: 10px;
        }
        .section-title::before {
            content: ''; display: inline-block; width: 4px; height: 20px;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            border-radius: 4px;
        }
        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
        .kpi-card {
            background: white; border-radius: 16px; padding: 1.25rem 1.5rem;
            box-shadow: var(--shadow); border: 1px solid #f1f5f9;
        }
        .kpi-card .lbl { font-size: .7rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; color: var(--slate-muted); margin-bottom: .3rem; }
        .kpi-card .val { font-size: 1.25rem; font-weight: 900; color: var(--slate-dark); line-height: 1.1; }
        .kpi-card .sub { font-size: .7rem; color: var(--slate-muted); margin-top: 2px; }

        /* ===== TABLE ===== */
        .table thead th { background: var(--slate-dark) !important; color: white !important; font-size: .8rem; }
        .table tfoot td { background: #f8fafc; font-weight: 900; font-size: .85rem; }
        .mbadge { display: inline-block; padding: .25rem .65rem; border-radius: 7px; font-size: .78rem; font-weight: 700; }
        .mb-cash  { background: #ecfdf5; color: #059669; }
        .mb-tf    { background: #eff6ff; color: #2563eb; }
        .mb-edc   { background: #fff7ed; color: #d97706; }
        .mb-ew    { background: #faf5ff; color: #7c3aed; }
        .mb-piu   { background: #fff1f2; color: #dc2626; }
        .mb-pos   { background: #ecfdf5; color: #059669; }
        .mb-neg   { background: #fff1f2; color: #dc2626; }

        /* ===== FOOTER ===== */
        .report-footer {
            text-align: center; padding: 2rem 1rem;
            color: var(--slate-muted); font-size: .78rem;
            border-top: 1px solid #e2e8f0; margin-top: 2rem;
        }

        /* ===== PRINT STYLES ===== */
        @media print {
            .top-bar, .watermark-banner, .btn-print-top, .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate { display: none !important; }
            body { background: white !important; }
            .report-wrapper { padding: 0 !important; max-width: 100% !important; }
            .report-hero { border-radius: 0 !important; margin-bottom: .5rem !important; }
            .r-card { box-shadow: none !important; border: 1px solid #ddd !important; border-radius: 8px !important; }
            .kpi-card { box-shadow: none !important; border: 1px solid #ddd !important; }
            thead th { background-color: #1e293b !important; color: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .print-header { display: block !important; }
        }
        .print-header { display: none; text-align: center; margin-bottom: 12px; }
        .print-header h2 { font-size: 14pt; font-weight: 900; margin: 0 0 3px; }
        .print-header p  { font-size: 8pt; color: #666; margin: 0; }
        .print-header hr { border-top: 2px solid #1e293b; margin: 6px 0; }
    </style>
</head>
<body>

<!-- Print-only header -->
<div class="print-header">
    <h2><?= $report_title ?></h2>
    <p><?= $perusahaan ?> &nbsp;|&nbsp; Dicetak: <?= date('d/m/Y H:i') ?></p>
    <hr>
</div>

<!-- Top Bar -->
<div class="top-bar">
    <div class="top-bar-inner">
        <div class="top-bar-brand">
            <div class="logo-box"><i class="fas fa-chart-line"></i></div>
            <div>
                <div class="company"><?= $perusahaan ?></div>
                <div class="report-name"><?= $report_title ?></div>
            </div>
        </div>
        <div class="top-bar-actions">
            <div class="expiry-badge">
                <i class="fa fa-clock"></i>
                Berlaku hingga <?= date('d M Y H:i', strtotime($expires_at)) ?>
            </div>
            <button class="btn-top btn-print-top" onclick="window.print()">
                <i class="fa fa-print"></i> Cetak / Save PDF
            </button>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="report-wrapper">
    <!-- Confidential notice -->
    <div class="watermark-banner">
        <i class="fa fa-lock"></i>
        <span>KONFIDENSIAL — Laporan ini hanya untuk Owner/Pemilik. Jangan bagikan link ini ke pihak lain.</span>
        <span style="margin-left:auto;color:#b45309;">Link diakses <?= $token_record->view_count ?> kali</span>
    </div>

    <!-- Hero -->
    <div class="report-hero">
        <div style="display:flex;align-items:center;gap:1.25rem;">
            <div class="hero-icon">
                <?php
                $icons = ['legger'=>'fa-book','tren_keuangan'=>'fa-line-chart','aging_piutang'=>'fa-clock-o','arus_kas'=>'fa-exchange','hpp_margin'=>'fa-tag','laba_rugi'=>'fa-balance-scale'];
                ?>
                <i class="fa <?= $icons[$report_type] ?? 'fa-chart-bar' ?>"></i>
            </div>
            <div>
                <h1><?= $report_title ?></h1>
                <p>
                    <i class="fa fa-building-o"></i> <?= $perusahaan ?>
                    <?php if (!empty($params['mulai'])): ?>
                        &nbsp;&middot;&nbsp; <i class="fa fa-calendar"></i>
                        <?= date('d M Y', strtotime($params['mulai'])) ?> s/d <?= date('d M Y', strtotime($params['selesai'] ?? date('Y-m-t'))) ?>
                    <?php elseif (!empty($params['bulan'])): ?>
                        &nbsp;&middot;&nbsp; <i class="fa fa-calendar"></i> <?= date('F Y', strtotime($params['bulan'] . '-01')) ?>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:.7rem;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Generated</div>
            <div style="font-size:.9rem;color:white;font-weight:700;"><?= date('d M Y H:i', strtotime($token_record->created_at)) ?></div>
        </div>
    </div>

    <!-- Report-Specific Content -->
    <?php $this->load->view('owner_report/content_' . $report_type, $this->load->get_vars()); ?>

    <!-- Footer -->
    <div class="report-footer">
        <i class="fa fa-shield"></i>
        Laporan ini dibuat secara otomatis oleh sistem ERP TGM Print.
        Hanya dapat dibaca &mdash; tidak dapat dimodifikasi.
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <i class="fa fa-clock-o"></i> Link berlaku hingga <?= date('d M Y H:i', strtotime($expires_at)) ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
