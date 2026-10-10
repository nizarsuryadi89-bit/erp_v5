<?php
/**
 * Laporan Pemasukan Bulanan - Premium UI/UX Edition
 * Accounting Standards: Monthly Accrual Omset vs Cash Flow Reconciliation.
 */
$current_perusahaan_id = $this->input->get('perusahaan_id') ?: $this->session->userdata('perusahaan_id');
$bulan_ini = $this->input->get('bulan') ?: date('Y-m');
?>

<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">

<style>
    /* ==========================================================================
    TGM PREMIUM MONTHLY CASH FLOW (INCOME REPORT)
    ========================================================================== */
    :root {
        --fin-primary: #0f172a;
        --fin-accent: #3b82f6;
        --fin-success: #10b981;
        --fin-danger: #ef4444;
        --fin-warning: #f59e0b;
        --fin-info: #06b6d4;
        --fin-bg: #f8fafc;
        --card-radius: 20px;
    }

    .app-content {
        background-color: var(--fin-bg);
        min-height: 100vh;
        font-family: 'Inter', sans-serif;
    }

    /* Filter HUD Styling */
    .filter-hud {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        margin-bottom: 2rem;
    }

    .filter-hud .form-select,
    .filter-hud .form-control {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 0.6rem 1rem;
        font-weight: 700;
        font-size: 0.85rem;
    }

    /* Premium Stat Cards */
    .cash-stat-card {
        background: white;
        border-radius: var(--card-radius);
        padding: 1.5rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .cash-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    }

    .cash-stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        margin-bottom: 0.75rem;
    }

    .cash-stat-value {
        font-size: 1.4rem;
        font-weight: 900;
        color: var(--fin-primary);
        letter-spacing: -0.5px;
        line-height: 1.2;
    }

    .cash-stat-label {
        font-size: 0.7rem;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.25rem;
    }

    .cash-stat-desc {
        font-size: 0.65rem;
        font-weight: 600;
        color: #cbd5e1;
    }

    /* Summary Card Styling */
    .summary-card {
        background: white;
        border-radius: var(--card-radius);
        border: 1px solid #e2e8f0;
        box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .summary-header {
        background: var(--fin-primary);
        color: white;
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .summary-table {
        width: 100%;
    }

    .summary-table tr td {
        padding: 0.9rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .row-group-title {
        background: #f8fafc;
        font-weight: 900;
        font-size: 0.7rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        padding: 0.75rem 1.5rem !important;
    }

    .row-label {
        font-weight: 700;
        color: #334155;
        font-size: 0.85rem;
    }

    .row-value {
        font-weight: 800;
        text-align: right;
        font-family: 'SF Mono', 'JetBrains Mono', monospace;
        font-size: 0.9rem;
    }

    .row-sublabel {
        font-size: 0.75rem;
        color: var(--fin-accent);
        padding-left: 2.5rem !important;
        font-weight: 600;
    }

    .row-subvalue {
        font-size: 0.8rem;
        font-weight: 700;
        text-align: right;
        color: var(--fin-accent);
        font-family: 'JetBrains Mono', monospace;
    }

    .row-total {
        background: #f0f9ff;
    }

    .row-total td {
        border-top: 1px solid #bae6fd !important;
        padding: 1.25rem 1.5rem !important;
    }

    .total-label {
        font-weight: 900;
        color: var(--fin-accent);
        font-size: 0.9rem;
    }

    .total-value {
        font-weight: 900;
        color: var(--fin-accent);
        font-size: 1.1rem;
        text-align: right;
    }

    /* Transaction List Table */
    .tx-card {
        background: white;
        border-radius: var(--card-radius);
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
    }

    .table-tx thead th {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        padding: 1rem 1.25rem;
        font-weight: 800;
        color: #64748b;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .table-tx tbody td {
        padding: 1rem 1.25rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
        border-bottom: 1px solid #f1f5f9;
    }

    .badge-premium {
        padding: 0.4rem 0.75rem;
        border-radius: 8px;
        font-weight: 800;
        font-size: 0.65rem;
        letter-spacing: 0.5px;
    }

    .badge-success-soft {
        background: #dcfce7;
        color: #166534;
    }

    .badge-warning-soft {
        background: #fef9c3;
        color: #854d0e;
    }

    /* Utility */
    .monospaced {
        font-family: 'JetBrains Mono', monospace;
    }

    @media print {
        .no-print {
            display: none !important;
        }

        .app-content {
            padding: 0 !important;
        }

        .summary-card,
        .tx-card {
            border: none;
            box-shadow: none;
        }

        .summary-header {
            background: white !important;
            color: black !important;
            border-bottom: 2px solid black;
        }
    }
</style>

<div class="app-content mt-3 pb-5">
    <?php $this->load->view('layout_admin/header_modul'); ?>

    <div class="container-fluid py-4">
        <!-- TOP FILTER HUD -->
        <div class="filter-hud no-print">
            <form action="" method="GET" class="row align-items-center g-3">
                <div class="col-xl-4 col-lg-12">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 bg-light rounded-3 text-primary border shadow-sm">
                            <i class="fas fa-calendar-check fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-900 text-dark ls-n1">LAPORAN BULANAN</h6>
                            <p class="text-muted small mb-0 fw-bold">Analisa Cash Flow & Piutang Periode</p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-5">
                    <?php
                    $level = $this->session->userdata('level');
                    $is_admin_owner = ($level == 1 || $level == 17);
                    if ($is_admin_owner):
                        ?>
                        <select class="form-select" name="perusahaan_id" onchange="this.form.submit()">
                            <?php foreach ($all_perusahaan as $p): ?>
                                <option value="<?= $p->id_perusahaan ?>" <?= ($current_perusahaan_id == $p->id_perusahaan) ? 'selected' : '' ?>>
                                    🏢 <?= $p->nama_perusahaan ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <input type="text" class="form-control"
                            value="<?= $this->session->userdata('nama_perusahaan') ?: 'Cabang Anda' ?>" readonly>
                        <input type="hidden" name="perusahaan_id" value="<?= $current_perusahaan_id ?>">
                    <?php endif; ?>
                </div>

                <div class="col-xl-2 col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="far fa-calendar-alt text-muted"></i></span>
                        <input type="month" name="bulan" class="form-control" value="<?= $bulan_ini ?>"
                            onchange="this.form.submit()">
                    </div>
                </div>

                <div class="col-xl-3 col-lg-3 text-end">
                    <div class="d-flex gap-2">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#modalGuideMonthly"
                            class="btn btn-outline-primary border-2 fw-900 rounded-3 px-3 shadow-sm">
                            <i class="fas fa-book-open"></i>
                        </button>
                        <button type="button" class="btn btn-dark w-100 rounded-3 py-2 fw-900 shadow-sm"
                            data-bs-toggle="modal" data-bs-target="#modalPilihCabangPrint">
                            <i class="fas fa-print me-2"></i> CETAK
                        </button>
                        <button type="button" class="btn btn-success rounded-3 py-2 px-3 fw-900 shadow-sm"
                            id="btn-export">
                            <i class="fas fa-file-excel"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- KPI GRID -->
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6">
                <div class="cash-stat-card border-bottom border-primary border-4">
                    <div class="cash-stat-icon bg-primary bg-opacity-10 text-primary"><i class="fas fa-chart-line"></i>
                    </div>
                    <div class="cash-stat-label">Total Omset (Sales)</div>
                    <div class="cash-stat-value">Rp <?= number_format($stat_keuangan['omset_penjualan'], 0, ',', '.') ?>
                    </div>
                    <div class="cash-stat-desc mt-1">Akumulasi Penjualan Akrual</div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="cash-stat-card border-bottom border-success border-4">
                    <div class="cash-stat-icon bg-success bg-opacity-10 text-success"><i class="fas fa-wallet"></i>
                    </div>
                    <div class="cash-stat-label">Total Uang Masuk</div>
                    <div class="cash-stat-value text-success">Rp
                        <?= number_format($stat_keuangan['total_uang_masuk'], 0, ',', '.') ?></div>
                    <div class="cash-stat-desc mt-1">Realisasi Penerimaan Kas</div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="cash-stat-card border-bottom border-warning border-4">
                    <div class="cash-stat-icon bg-warning bg-opacity-10 text-warning"><i class="fas fa-user-clock"></i>
                    </div>
                    <div class="cash-stat-label">Piutang Baru</div>
                    <div class="cash-stat-value text-warning">Rp
                        <?= number_format($stat_keuangan['piutang_baru_terbentuk'], 0, ',', '.') ?></div>
                    <div class="cash-stat-desc mt-1">Saldo Hutang Konsumen Baru</div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="cash-stat-card border-bottom border-info border-4 bg-light">
                    <div class="cash-stat-icon bg-info bg-opacity-10 text-info"><i class="fas fa-piggy-bank"></i></div>
                    <div class="cash-stat-label">Saldo Kas Fisik</div>
                    <div class="cash-stat-value text-info">Rp
                        <?= number_format($stat_keuangan['total_uang_masuk'] - $stat_keuangan['total_pengeluaran'], 0, ',', '.') ?>
                    </div>
                    <div class="cash-stat-desc mt-1">Estimasi Tunai di Tangan</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- BREAKDOWN SUMMARY (LEFT) -->
            <div class="col-xl-5">
                <div class="summary-card shadow-sm h-100">
                    <div class="summary-header">
                        <div>
                            <h6 class="fw-900 mb-0 ls-n1">REKAPITULASI KEUANGAN</h6>
                            <small
                                class="opacity-75 fw-bold text-uppercase ls-1"><?= date('F Y', strtotime($bulan_ini)) ?></small>
                        </div>
                        <i class="fas fa-file-invoice-dollar fs-4 opacity-50"></i>
                    </div>

                    <div class="table-responsive">
                        <table class="summary-table">
                            <tbody>
                                <tr class="row-group-title">
                                    <td colspan="2">A. ANALISA PEMASUKAN</td>
                                </tr>

                                <tr>
                                    <td class="row-label">1. Penerimaan Tunai (Cash)</td>
                                    <td class="row-value"><?= number_format($summary['cash']['total'], 0, ',', '.') ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="row-label">2. Transfer Bank</td>
                                    <td class="row-value">
                                        <?= number_format($summary['transfer']['total'], 0, ',', '.') ?></td>
                                </tr>
                                <?php if (!empty($breakdown_transfer)):
                                    foreach ($breakdown_transfer as $bt): ?>
                                        <tr>
                                            <td class="row-sublabel"><i class="fas fa-caret-right me-1 opacity-50"></i>
                                                <?= $bt['nama_bank'] ?> (<?= $bt['no_rekening'] ?>)</td>
                                            <td class="row-subvalue"><?= number_format($bt['total'], 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; endif; ?>

                                <tr>
                                    <td class="row-label">3. EDC / Debit Card</td>
                                    <td class="row-value"><?= number_format($summary['debit']['total'], 0, ',', '.') ?>
                                    </td>
                                </tr>
                                <?php if (!empty($breakdown_edc)):
                                    foreach ($breakdown_edc as $be): ?>
                                        <tr>
                                            <td class="row-sublabel"><i class="fas fa-caret-right me-1 opacity-50"></i>
                                                <?= $be['edc_nama'] ?></td>
                                            <td class="row-subvalue"><?= number_format($be['total'], 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; endif; ?>

                                <tr>
                                    <td class="row-label">4. E-Wallet / QRIS</td>
                                    <td class="row-value">
                                        <?= number_format($summary['ewallet']['total'], 0, ',', '.') ?></td>
                                </tr>
                                <?php if (!empty($breakdown_ewallet)):
                                    foreach ($breakdown_ewallet as $bew): ?>
                                        <tr>
                                            <td class="row-sublabel"><i class="fas fa-caret-right me-1 opacity-50"></i>
                                                <?= $bew['ewallet_nama'] ?></td>
                                            <td class="row-subvalue"><?= number_format($bew['total'], 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; endif; ?>

                                <tr class="row-total">
                                    <td class="total-label text-primary">TOTAL PEMASUKAN REAL (KAS)</td>
                                    <td class="total-value text-primary">Rp
                                        <?= number_format($stat_keuangan['total_uang_masuk'], 0, ',', '.') ?></td>
                                </tr>

                                <tr class="row-group-title">
                                    <td colspan="2">B. ANALISA PENGELUARAN & SALDO</td>
                                </tr>
                                <tr>
                                    <td class="row-label">Total Pengeluaran Bulanan</td>
                                    <td class="row-value text-danger">Rp
                                        <?= number_format($stat_keuangan['total_pengeluaran'], 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td class="row-label">Total Piutang Berjalan</td>
                                    <td class="row-value text-warning">Rp
                                        <?= number_format($stat_keuangan['piutang_baru_terbentuk'], 0, ',', '.') ?></td>
                                </tr>

                                <tr class="row-total" style="background: var(--fin-primary);">
                                    <td class="py-4 ps-4 fw-900 text-white fs-6">SALDO AKHIR PERIODE</td>
                                    <td class="py-4 text-end pe-4 fw-900 text-white fs-4">Rp
                                        <?= number_format($stat_keuangan['total_uang_masuk'] - $stat_keuangan['total_pengeluaran'], 0, ',', '.') ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Action Button for Printing -->
                <div class="mt-4 no-print">
                    <?php if (in_array($this->session->userdata('level'), [1, 17])): ?>
                        <button type="button" class="btn btn-dark w-100 py-3 fw-900 rounded-4 shadow-sm"
                            data-bs-toggle="modal" data-bs-target="#modalPilihCabangPrint">
                            <i class="fas fa-print me-2"></i> CETAK LAPORAN BULAN INI
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- TRANSACTION LIST (RIGHT) -->
            <div class="col-xl-7">
                <div class="tx-card shadow-sm h-100 overflow-hidden">
                    <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-white">
                        <h6 class="fw-900 text-dark mb-0 ls-n1"><i class="fas fa-history me-2 text-primary"></i>AUDIT
                            TRANSACTION LOG</h6>
                        <div class="d-flex align-items-center" style="gap: 10px;">
                            <span class="badge bg-light text-muted fw-bold p-2"><?= count($pemasukan_list) ?> ENTRIES</span>
                            <a href="<?= base_url('laporan/cetak_transaksi_bulanan?perusahaan_id=' . $current_perusahaan_id . '&bulan=' . $bulan_ini) ?>" target="_blank" class="btn btn-sm btn-danger fw-900 px-3 py-1 shadow-sm rounded-3">
                                <i class="fas fa-file-pdf me-1"></i> PDF
                            </a>
                        </div>
                    </div>

                    <div class="p-3 bg-light border-bottom no-print">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-0"><i
                                    class="fas fa-search text-muted"></i></span>
                            <input type="text" id="tableSearch" class="form-control border-0 shadow-none ps-0"
                                placeholder="Cari berdasarkan SPK, Konsumen, atau Status...">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-tx table-hover mb-0" id="txTable">
                            <thead>
                                <tr>
                                    <th>TANGGAL</th>
                                    <th>KONSUMEN / SPK</th>
                                    <th class="text-end">TAGIHAN</th>
                                    <th class="text-end text-success">DIBAYAR</th>
                                    <th class="text-end text-danger">PIUTANG</th>
                                    <th class="text-center">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pemasukan_list as $trx): ?>
                                    <tr>
                                        <td class="monospaced fw-bold text-muted">
                                            <div class="text-dark"><?= date('d M Y', strtotime($trx['tanggal'])) ?></div>
                                            <small><?= date('H:i', strtotime($trx['jam'])) ?></small>
                                        </td>
                                        <td>
                                            <div class="fw-900 text-dark"><?= $trx['nospk'] ?></div>
                                            <div class="small fw-bold text-muted mb-1">
                                                <i class="fas fa-user text-primary opacity-75 me-1"></i> <?= $trx['konsumen'] ?: 'UMUM/NON-MEMBER' ?>
                                            </div>
                                            <div class="small text-muted" style="font-size: 0.7rem; line-height: 1.2;">
                                                <i class="fas fa-box text-secondary opacity-75 me-1"></i> <?= $trx['produk'] ?: '-' ?>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold monospaced">Rp
                                            <?= number_format($trx['total_tagihan'], 0, ',', '.') ?></td>
                                        <td class="text-end text-success fw-900 monospaced">Rp
                                            <?= number_format($trx['bayar_hari_ini'], 0, ',', '.') ?></td>
                                        <td class="text-end text-danger monospaced small">
                                            <?= ($trx['sisa_piutang'] > 0) ? 'Rp ' . number_format($trx['sisa_piutang'], 0, ',', '.') : '-' ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($trx['status_transaksi'] == 'Lunas'): ?>
                                                <span class="badge badge-premium badge-success-soft">LUNAS</span>
                                            <?php else: ?>
                                                <span class="badge badge-premium badge-warning-soft">PIUTANG</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL GUIDE MONTHLY -->
<div class="modal fade" id="modalGuideMonthly" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-4 overflow-hidden border-0">
            <div class="modal-header bg-dark text-white p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-circle p-2 me-3">
                        <i class="fas fa-book-open text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-900 ls-1 mb-0 text-uppercase">Buku Saku Tutup Buku Bulanan</h5>
                        <small class="text-white-50 fw-bold">Manual Operasional Akuntansi PB HUB</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-light">
                <!-- Header Highlight -->
                <div class="bg-primary bg-opacity-10 p-4 border-bottom border-primary border-opacity-10">
                    <div class="row align-items-center">
                        <div class="col-md-9">
                            <h6 class="fw-900 text-primary mb-1">AUDIT & CLOSING WORKFLOW</h6>
                            <p class="small text-muted mb-0 fw-bold">Pastikan integritas data keuangan bulanan dengan mengikuti langkah berikut.</p>
                        </div>
                        <div class="col-md-3 text-md-end mt-3 mt-md-0">
                            <span class="badge bg-primary px-3 py-2 rounded-pill fw-900 shadow-sm">Audit v2.5</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 p-md-5">
                    <div class="row g-4">
                        <!-- TAHAP 1 -->
                        <div class="col-md-6">
                            <div class="bg-white p-4 rounded-4 shadow-sm border border-opacity-10 h-100">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary text-white rounded-3 p-2 me-3"><i class="fas fa-calendar-check"></i></div>
                                    <h6 class="fw-900 text-dark mb-0">1. KALIBRASI DATA</h6>
                                </div>
                                <ul class="small text-muted fw-bold ps-3 mb-0">
                                    <li class="mb-2">Gunakan **Filter Bulan** untuk emmilih periode closing yang tepat.</li>
                                    <li class="mb-2">Verifikasi sinkronisasi SPK. Semua nota bulan berjalan harus sudah terinput di sistem.</li>
                                    <li>Pastikan "Saldo Awal" bulan (jika ada) sudah divalidasi dengan saldo akhir bulan lalu.</li>
                                </ul>
                            </div>
                        </div>

                        <!-- TAHAP 2 -->
                        <div class="col-md-6">
                            <div class="bg-white p-4 rounded-4 shadow-sm border border-opacity-10 h-100">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-success text-white rounded-3 p-2 me-3"><i class="fas fa-balance-scale"></i></div>
                                    <h6 class="fw-900 text-dark mb-0">2. ANALISA KPI AKRUAL</h6>
                                </div>
                                <ul class="small text-muted fw-bold ps-3 mb-0">
                                    <li class="mb-2">**Total Omset**: Mengukur pertumbuhan bisnis (akumulasi nilai pekerjaan).</li>
                                    <li class="mb-2">**Total Kas Masuk**: Mengukur likuiditas bisnis (realita uang di tangan).</li>
                                    <li>Selisih keduanya adalah **Piutang Berjalan**. Semakin kecil selisih, semakin sehat cash flow perusahaan.</li>
                                </ul>
                            </div>
                        </div>

                        <!-- TAHAP 3 -->
                        <div class="col-12">
                            <div class="bg-white p-4 rounded-4 shadow-sm border border-opacity-10">
                                <h6 class="fw-900 text-dark mb-4 border-bottom pb-3"><i class="fas fa-check-double text-info me-2"></i>REKONSILIASI PENERIMAAN (3-WAY MATCH)</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3 border h-100">
                                            <div class="fw-900 text-dark small mb-2 uppercase">REKONSILIASI BANK</div>
                                            <p class="x-small text-muted mb-0 lh-base">Cocokkan aggregate **Transfer Bank** di sistem dengan total mutasi kredit pada Rekening Koran asli per tanggal 1 s/d 30/31.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3 border h-100">
                                            <div class="fw-900 text-dark small mb-2 uppercase">LOGISTIK EDC (DIGITAL)</div>
                                            <p class="x-small text-muted mb-0 lh-base">Total **EDC & E-Wallet** harus sama dengan dana masuk di settlement bank/dana QRIS setelah dipotong MDR.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3 border h-100">
                                            <div class="fw-900 text-dark small mb-2 uppercase">KAS & PENGELUARAN</div>
                                            <p class="x-small text-muted mb-0 lh-base">Review setiap pengeluaran di sisi kanan. Pastikan tidak ada pengeluaran laci yang lupa diinput agar saldo kas akurat.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAHAP 4 -->
                        <div class="col-12">
                            <div class="bg-dark p-4 rounded-4 shadow-dark shadow">
                                <div class="row align-items-center">
                                    <div class="col-md-1 d-none d-md-block text-center border-end border-white border-opacity-25 pe-4">
                                        <i class="fas fa-print text-white fs-3"></i>
                                    </div>
                                    <div class="col-md-8 ps-md-4">
                                        <h6 class="fw-900 text-white mb-1 uppercase ls-1">STANDAR PELAPORAN MANAJERIAL</h6>
                                        <p class="x-small text-white text-opacity-75 mb-0 lh-base">Gunakan tombol **Cetak Laporan Bulanan**, pilih mode **Konsolidasi** untuk Owner. Pada setelan print browser: Wajib orientasi **Landscape** dan centang **Background Graphics** agar visual premium laporan tidak hilang.</p>
                                    </div>
                                    <div class="col-md-3 text-md-end mt-3 mt-md-0">
                                        <button type="button" class="btn btn-primary btn-sm px-4 fw-900 rounded-pill shadow-sm" data-bs-dismiss="modal">SIAP CLOSING</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white p-3 border-0">
                <button type="button" class="btn btn-light rounded-pill px-5 fw-900 shadow-sm" data-bs-dismiss="modal">TUTUP PANDUAN</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PILIH CABANG UNTUK PRINT -->
<div class="modal fade" id="modalPilihCabangPrint" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-4 overflow-hidden border-0">
            <div class="modal-header bg-dark text-white p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-circle p-2 me-3">
                        <i class="fas fa-print text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-900 ls-1 mb-0 text-uppercase">CETAK PEMASUKAN BULANAN</h5>
                        <small class="text-white-50 fw-bold">Pilih unit bisnis yang akan dicetak</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="list-group list-group-flush rounded-3 border">
                    <?php if (!empty($all_perusahaan)): ?>
                        <?php foreach ($all_perusahaan as $p): ?>
                            <a href="<?= base_url('laporan/cetak_pemasukan_bulanan?bulan=' . $bulan_ini . '&perusahaan_id=' . $p->id_perusahaan) ?>"
                                target="_blank"
                                class="list-group-item list-group-item-action py-3 px-4 d-flex align-items-center justify-content-between border-bottom">
                                <span class="fw-bold text-dark"><i class="fas fa-building me-3 text-muted"></i>
                                    <?= strtoupper($p->nama_perusahaan) ?></span>
                                <i class="fas fa-external-link-alt text-primary small"></i>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <a href="<?= base_url('laporan/cetak_pemasukan_bulanan?bulan=' . $bulan_ini) ?>" target="_blank"
                        class="list-group-item list-group-item-action py-4 px-4 d-flex align-items-center justify-content-between bg-primary bg-opacity-10">
                        <span class="fw-900 text-primary"><i class="fas fa-globe me-3"></i> SEMUA CABANG
                            (KONSOLIDASI)</span>
                        <i class="fas fa-arrow-right text-primary"></i>
                    </a>
                </div>
            </div>
            <div class="modal-footer bg-white p-3 border-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-800"
                    data-bs-dismiss="modal">BATAL</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof $ !== 'undefined' && $.fn.DataTable) {
            var table = $('#txTable').DataTable({
                "pageLength": 15,
                "ordering": false,
                "lengthChange": false,
                "searching": true,
                "dom": "<'row mb-3 px-4 pt-3 d-none'<'col-sm-12'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row p-4'<'col-sm-5 small fw-bold text-muted'i><'col-sm-7'p>>",
                "language": {
                    "search": "",
                    "searchPlaceholder": "Cari SPK, Konsumen atau Nominal...",
                    "info": "MENAMPILKAN _START_ - _END_ DARI _TOTAL_ DATA",
                    "paginate": { "previous": "<i class='fas fa-chevron-left'></i>", "next": "<i class='fas fa-chevron-right'></i>" }
                }
            });

            $('#tableSearch').on('keyup', function () {
                table.search(this.value).draw();
            });

            $('#btn-export').on('click', function () {
                var url = '<?= base_url("laporan/export_pemasukan_bulanan?perusahaan_id=" . $current_perusahaan_id . "&bulan=" . $bulan_ini) ?>';
                window.location.href = url;
            });
        }
    });
</script>