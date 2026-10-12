<?php
/**
 * Laporan Mutasi Saldo Harian (Konsolidasi Kas / Bank / EDC / E-Wallet) - TGM Premium Edition
 */
$current_perusahaan_id = $this->input->get('perusahaan_id') ?: $this->session->userdata('perusahaan_id');
$transaction_count = isset($mutasi) ? count($mutasi) : 0;
$this->load->view('layout_admin/header_modul');

// Channel breakdown counters
$c_tunai = $channel_summary['tunai'] ?? ['masuk' => 0, 'keluar' => 0, 'count' => 0];
$c_bank = $channel_summary['transfer'] ?? ['masuk' => 0, 'keluar' => 0, 'count' => 0];
$c_edc = $channel_summary['debit'] ?? ['masuk' => 0, 'keluar' => 0, 'count' => 0];
$c_ewallet = $channel_summary['ewallet'] ?? ['masuk' => 0, 'keluar' => 0, 'count' => 0];
?>

<!-- Premium Design Tokens -->
<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">
<style>
    :root {
        --tgm-indigo: #4f46e5;
        --tgm-indigo-dark: #3730a3;
        --tgm-emerald: #10b981;
        --tgm-rose: #f43f5e;
        --tgm-amber: #f59e0b;
        --tgm-cyan: #06b6d4;
        --tgm-purple: #8b5cf6;
        --tgm-slate-50: #f8fafc;
        --tgm-slate-100: #f1f5f9;
        --tgm-slate-200: #e2e8f0;
        --tgm-slate-600: #475569;
        --tgm-slate-700: #334155;
        --tgm-slate-900: #0f172a;
    }

    body {
        background-color: #f4f7fa;
        font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
    }

    .premium-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.02);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        background: #fff;
    }

    .premium-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 30px rgba(0, 0, 0, 0.04);
    }

    .filter-hud {
        background: white;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid var(--tgm-slate-100);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .table-premium thead th {
        background: var(--tgm-slate-50);
        color: var(--tgm-slate-700);
        text-transform: uppercase;
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        padding: 16px 14px;
        border: none;
        vertical-align: middle;
    }

    .table-premium tbody td {
        padding: 14px 14px;
        vertical-align: middle;
        color: var(--tgm-slate-900);
        border-bottom: 1px solid var(--tgm-slate-100);
        font-size: 0.88rem;
    }

    .font-mono {
        font-family: 'JetBrains Mono', 'Fira Code', monospace !important;
        letter-spacing: -0.02em;
    }

    .fw-900 { font-weight: 900; }
    .ls-1 { letter-spacing: -1px; }

    .btn-premium-primary {
        background: var(--tgm-indigo);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        transition: all 0.2s;
    }

    .btn-premium-primary:hover {
        background: var(--tgm-indigo-dark);
        color: white;
        transform: translateY(-1px);
    }

    .channel-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .channel-pill-tunai {
        background: rgba(245, 158, 11, 0.12);
        color: #d97706;
        border: 1px solid rgba(245, 158, 11, 0.25);
    }

    .channel-pill-transfer {
        background: rgba(79, 70, 229, 0.12);
        color: #4f46e5;
        border: 1px solid rgba(79, 70, 229, 0.25);
    }

    .channel-pill-edc {
        background: rgba(6, 182, 212, 0.12);
        color: #0891b2;
        border: 1px solid rgba(6, 182, 212, 0.25);
    }

    .channel-pill-ewallet {
        background: rgba(139, 92, 246, 0.12);
        color: #7c3aed;
        border: 1px solid rgba(139, 92, 246, 0.25);
    }

    .flow-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 8px;
        font-size: 0.85rem;
    }

    .flow-badge-in {
        background: rgba(16, 185, 129, 0.15);
        color: var(--tgm-emerald);
    }

    .flow-badge-out {
        background: rgba(244, 63, 94, 0.15);
        color: var(--tgm-rose);
    }

    .channel-card {
        border-radius: 16px;
        padding: 18px;
        background: #fff;
        border: 1px solid var(--tgm-slate-200);
        position: relative;
        overflow: hidden;
        transition: all 0.25s ease;
    }

    .channel-card:hover {
        border-color: var(--tgm-indigo);
        transform: translateY(-2px);
    }

    .quick-tab-btn {
        padding: 8px 18px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.82rem;
        border: 1px solid var(--tgm-slate-200);
        background: #fff;
        color: var(--tgm-slate-600);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .quick-tab-btn:hover, .quick-tab-btn.active {
        background: var(--tgm-indigo);
        color: #fff;
        border-color: var(--tgm-indigo);
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.25);
    }

    @media print {
        @page {
            size: A4 landscape;
            margin: 1cm;
        }

        .no-print {
            display: none !important;
        }

        .premium-card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
            border-radius: 0 !important;
        }

        .print-header {
            display: block !important;
            border-bottom: 2px solid #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
    }

    .print-header {
        display: none;
    }
</style>

<div class="app-content py-4">
    <!-- Print Header -->
    <div class="print-header text-center w-100">
        <h2 class="mb-0 fw-bold"><?= strtoupper($perusahaan ?? 'TGM ERP SYSTEM') ?></h2>
        <h4 class="mb-1">LAPORAN MUTASI SALDO HARIAN (KAS / BANK / EDC / E-WALLET)</h4>
        <p class="mb-0">Periode: <?= date('d/m/Y', strtotime($start_date)) ?> s/d <?= date('d/m/Y', strtotime($end_date)) ?> | Channel: <?= strtoupper($channel) ?></p>
        <div class="small text-muted mt-1">Dicetak pada: <?= date('d/m/Y H:i') ?> | Oleh: <?= $this->session->userdata('username') ?></div>
    </div>

    <div class="container-fluid">
        <!-- Header & Breadcrumb -->
        <div class="row align-items-center mb-4 no-print">
            <div class="col-md-7 text-center text-md-start mb-3 mb-md-0">
                <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-3">
                    <div style="width: 50px; height: 50px; border-radius: 16px; background: linear-gradient(135deg, #4f46e5, #06b6d4); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.4rem; box-shadow: 0 8px 18px rgba(79, 70, 229, 0.3);">
                        <i class="fas fa-money-check-alt"></i>
                    </div>
                    <div>
                        <h2 class="fw-900 text-dark ls-1 mb-0">MUTASI SALDO <span class="text-indigo">HARIAN</span></h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb bg-transparent p-0 mb-0">
                                <li class="breadcrumb-item small fw-bold text-muted">LAPORAN KEUANGAN</li>
                                <li class="breadcrumb-item small fw-bold text-indigo active">KONSOLIDASI KAS & BANK</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="col-md-5 text-center text-md-end">
                <a href="<?= base_url('laporan/financial_hub') ?>" class="btn btn-outline-secondary rounded-pill px-3 fw-bold shadow-sm me-2">
                    <i class="fas fa-th-large me-1"></i> Hub Keuangan
                </a>
                <a href="<?= base_url('laporan/export_mutasi_harian?start_date=' . $start_date . '&end_date=' . $end_date . '&channel=' . $channel . '&perusahaan_id=' . $current_perusahaan_id) ?>" class="btn btn-success rounded-pill px-3 fw-bold shadow-sm me-2">
                    <i class="fas fa-file-excel me-1"></i> Export Excel
                </a>
                <button onclick="window.print()" class="btn btn-dark rounded-pill px-3 fw-bold shadow-sm">
                    <i class="fas fa-print me-1"></i> Cetak
                </button>
            </div>
        </div>

        <!-- Filter HUD -->
        <div class="premium-card filter-hud mb-4 no-print">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-900 text-dark mb-0 text-uppercase ls-1">
                    <i class="fas fa-filter text-indigo me-2"></i> Parameter Filter Mutasi
                </h6>
                <div class="d-flex gap-2">
                    <a href="?start_date=<?= date('Y-m-d') ?>&end_date=<?= date('Y-m-d') ?>&channel=<?= $channel ?>" class="badge bg-light text-dark text-decoration-none border px-2 py-1">Hari Ini</a>
                    <a href="?start_date=<?= date('Y-m-d', strtotime('-1 day')) ?>&end_date=<?= date('Y-m-d', strtotime('-1 day')) ?>&channel=<?= $channel ?>" class="badge bg-light text-dark text-decoration-none border px-2 py-1">Kemarin</a>
                    <a href="?start_date=<?= date('Y-m-01') ?>&end_date=<?= date('Y-m-d') ?>&channel=<?= $channel ?>" class="badge bg-light text-dark text-decoration-none border px-2 py-1">Bulan Ini</a>
                </div>
            </div>

            <form action="" method="GET" class="row g-3">
                <?php if ($is_admin_owner): ?>
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted text-uppercase mb-1">Cabang / Unit</label>
                        <select name="perusahaan_id" class="form-select border-2">
                            <option value="1">SEMUA CABANG (KONSOLIDASI)</option>
                            <?php foreach ($all_perusahaan as $p): ?>
                                <option value="<?= $p->id_perusahaan ?>" <?= ($current_perusahaan_id == $p->id_perusahaan) ? 'selected' : '' ?>>
                                    <?= strtoupper($p->nama_perusahaan) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="col-md-<?= $is_admin_owner ? '3' : '4' ?>">
                    <label class="small fw-bold text-muted text-uppercase mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control border-2" value="<?= $start_date ?>">
                </div>

                <div class="col-md-<?= $is_admin_owner ? '3' : '4' ?>">
                    <label class="small fw-bold text-muted text-uppercase mb-1">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control border-2" value="<?= $end_date ?>">
                </div>

                <div class="col-md-<?= $is_admin_owner ? '3' : '4' ?> d-flex align-items-end">
                    <input type="hidden" name="channel" value="<?= $channel ?>">
                    <button type="submit" class="btn btn-premium-primary w-100 py-2 rounded-3">
                        <i class="fas fa-search me-2"></i> Tampilkan Data
                    </button>
                </div>
            </form>
        </div>

        <!-- KPI Primary Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="premium-card p-4 border-start border-4 border-success">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-muted small fw-bold text-uppercase ls-1">Total Dana Masuk</span>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                    </div>
                    <h3 class="fw-900 text-success font-mono mb-1">Rp <?= number_format($total_masuk, 0, ',', '.') ?></h3>
                    <p class="small text-muted mb-0 fw-bold">Dari Penjualan & Pelunasan Piutang</p>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="premium-card p-4 border-start border-4 border-danger">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-muted small fw-bold text-uppercase ls-1">Total Dana Keluar</span>
                        <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                    </div>
                    <h3 class="fw-900 text-danger font-mono mb-1">Rp <?= number_format($total_keluar, 0, ',', '.') ?></h3>
                    <p class="small text-muted mb-0 fw-bold">Biaya Operasional & Pengeluaran</p>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="premium-card p-4 border-start border-4 <?= $net_movement >= 0 ? 'border-primary' : 'border-warning' ?>">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-muted small fw-bold text-uppercase ls-1">Pergerakan Bersih (Net)</span>
                        <div class="stat-icon <?= $net_movement >= 0 ? 'bg-primary text-primary' : 'bg-warning text-warning' ?> bg-opacity-10">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                    </div>
                    <h3 class="fw-900 font-mono mb-1 <?= $net_movement >= 0 ? 'text-primary' : 'text-danger' ?>">
                        <?= $net_movement >= 0 ? '+' : '' ?>Rp <?= number_format($net_movement, 0, ',', '.') ?>
                    </h3>
                    <p class="small text-muted mb-0 fw-bold">Selisih Masuk vs Keluar Periode Ini</p>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="premium-card p-4 border-start border-4 border-dark">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-muted small fw-bold text-uppercase ls-1">Total Mutasi Transaksi</span>
                        <div class="stat-icon bg-dark bg-opacity-10 text-dark">
                            <i class="fas fa-receipt"></i>
                        </div>
                    </div>
                    <h3 class="fw-900 text-dark font-mono mb-1"><?= number_format($transaction_count, 0, ',', '.') ?> <span class="fs-6 text-muted fw-bold">Trx</span></h3>
                    <p class="small text-muted mb-0 fw-bold">Saldo Awal Tunai: Rp <?= number_format($saldo_awal_cash, 0, ',', '.') ?></p>
                </div>
            </div>
        </div>

        <!-- 4 Channel Mini Cards (Breakdown) -->
        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="channel-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="channel-pill channel-pill-tunai"><i class="fas fa-money-bill-wave"></i> Kas Tunai</span>
                        <a href="<?= base_url('laporan/mutasi_cash?tgl_awal=' . $start_date . '&tgl_akhir=' . $end_date) ?>" class="small fw-bold text-decoration-none text-muted" title="Detail Kas Tunai">Detail <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="d-flex justify-content-between text-muted small mt-2">
                        <span>Masuk:</span>
                        <span class="font-mono text-success fw-bold">Rp <?= number_format($c_tunai['masuk'], 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between text-muted small mt-1">
                        <span>Keluar:</span>
                        <span class="font-mono text-danger fw-bold">Rp <?= number_format($c_tunai['keluar'], 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-2 mt-2 fw-bold small">
                        <span>Net Tunai:</span>
                        <span class="font-mono <?= ($c_tunai['masuk'] - $c_tunai['keluar']) >= 0 ? 'text-dark' : 'text-danger' ?>">
                            Rp <?= number_format($c_tunai['masuk'] - $c_tunai['keluar'], 0, ',', '.') ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="channel-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="channel-pill channel-pill-transfer"><i class="fas fa-university"></i> Transfer Bank</span>
                        <a href="<?= base_url('laporan/mutasi_bank?start_date=' . $start_date . '&end_date=' . $end_date) ?>" class="small fw-bold text-decoration-none text-muted" title="Detail Mutasi Bank">Detail <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="d-flex justify-content-between text-muted small mt-2">
                        <span>Masuk:</span>
                        <span class="font-mono text-success fw-bold">Rp <?= number_format($c_bank['masuk'], 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between text-muted small mt-1">
                        <span>Keluar:</span>
                        <span class="font-mono text-danger fw-bold">Rp <?= number_format($c_bank['keluar'], 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-2 mt-2 fw-bold small">
                        <span>Net Bank:</span>
                        <span class="font-mono <?= ($c_bank['masuk'] - $c_bank['keluar']) >= 0 ? 'text-dark' : 'text-danger' ?>">
                            Rp <?= number_format($c_bank['masuk'] - $c_bank['keluar'], 0, ',', '.') ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="channel-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="channel-pill channel-pill-edc"><i class="fas fa-credit-card"></i> Mesin EDC</span>
                        <a href="<?= base_url('laporan/mutasi_edc?start_date=' . $start_date . '&end_date=' . $end_date) ?>" class="small fw-bold text-decoration-none text-muted" title="Detail Mutasi EDC">Detail <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="d-flex justify-content-between text-muted small mt-2">
                        <span>Masuk:</span>
                        <span class="font-mono text-success fw-bold">Rp <?= number_format($c_edc['masuk'], 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between text-muted small mt-1">
                        <span>Keluar:</span>
                        <span class="font-mono text-danger fw-bold">Rp <?= number_format($c_edc['keluar'], 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-2 mt-2 fw-bold small">
                        <span>Net EDC:</span>
                        <span class="font-mono <?= ($c_edc['masuk'] - $c_edc['keluar']) >= 0 ? 'text-dark' : 'text-danger' ?>">
                            Rp <?= number_format($c_edc['masuk'] - $c_edc['keluar'], 0, ',', '.') ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="channel-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="channel-pill channel-pill-ewallet"><i class="fas fa-qrcode"></i> E-Wallet & QRIS</span>
                        <a href="<?= base_url('laporan/mutasi_ewallet?start_date=' . $start_date . '&end_date=' . $end_date) ?>" class="small fw-bold text-decoration-none text-muted" title="Detail Mutasi E-Wallet">Detail <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="d-flex justify-content-between text-muted small mt-2">
                        <span>Masuk:</span>
                        <span class="font-mono text-success fw-bold">Rp <?= number_format($c_ewallet['masuk'], 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between text-muted small mt-1">
                        <span>Keluar:</span>
                        <span class="font-mono text-danger fw-bold">Rp <?= number_format($c_ewallet['keluar'], 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-2 mt-2 fw-bold small">
                        <span>Net E-Wallet:</span>
                        <span class="font-mono <?= ($c_ewallet['masuk'] - $c_ewallet['keluar']) >= 0 ? 'text-dark' : 'text-danger' ?>">
                            Rp <?= number_format($c_ewallet['masuk'] - $c_ewallet['keluar'], 0, ',', '.') ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Channel Filter Tabs & Quick Nav -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 no-print">
            <div class="d-flex flex-wrap gap-2">
                <a href="?start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&channel=all&perusahaan_id=<?= $current_perusahaan_id ?>" 
                   class="quick-tab-btn <?= $channel == 'all' ? 'active' : '' ?>">
                    <i class="fas fa-layer-group"></i> Semua Channel (<?= $transaction_count ?>)
                </a>
                <a href="?start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&channel=tunai&perusahaan_id=<?= $current_perusahaan_id ?>" 
                   class="quick-tab-btn <?= $channel == 'tunai' ? 'active' : '' ?>">
                    <i class="fas fa-money-bill-wave text-warning"></i> Kas Tunai (<?= $c_tunai['count'] ?>)
                </a>
                <a href="?start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&channel=transfer&perusahaan_id=<?= $current_perusahaan_id ?>" 
                   class="quick-tab-btn <?= $channel == 'transfer' ? 'active' : '' ?>">
                    <i class="fas fa-university text-primary"></i> Bank Transfer (<?= $c_bank['count'] ?>)
                </a>
                <a href="?start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&channel=edc&perusahaan_id=<?= $current_perusahaan_id ?>" 
                   class="quick-tab-btn <?= in_array($channel, ['edc', 'debit']) ? 'active' : '' ?>">
                    <i class="fas fa-credit-card text-info"></i> EDC (<?= $c_edc['count'] ?>)
                </a>
                <a href="?start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&channel=ewallet&perusahaan_id=<?= $current_perusahaan_id ?>" 
                   class="quick-tab-btn <?= $channel == 'ewallet' ? 'active' : '' ?>">
                    <i class="fas fa-qrcode text-purple"></i> E-Wallet (<?= $c_ewallet['count'] ?>)
                </a>
            </div>
        </div>

        <!-- Mutasi Ledger Table -->
        <div class="premium-card overflow-hidden mb-5">
            <div class="p-4 border-bottom d-flex align-items-center justify-content-between bg-white flex-wrap gap-2">
                <div>
                    <h5 class="fw-900 mb-0 ls-1 text-uppercase">Buku Mutasi Saldo Terintegrasi</h5>
                    <p class="small text-muted fw-bold mb-0">Riwayat kronologis mutasi kas masuk dan keluar dari seluruh saluran pembayaran.</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-light text-dark border px-3 py-2 fw-bold">
                        <i class="fas fa-calendar-alt text-indigo me-1"></i> <?= date('d M Y', strtotime($start_date)) ?> s/d <?= date('d M Y', strtotime($end_date)) ?>
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-premium mb-0" id="tableMutasiHarian">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width: 130px;">Waktu</th>
                            <th style="width: 160px;">Channel</th>
                            <th style="width: 140px;">No. SPK / Ref</th>
                            <th>Pihak Terkait</th>
                            <th>Keterangan Transaksi</th>
                            <th class="text-end" style="width: 160px;">Masuk (+)</th>
                            <th class="text-end" style="width: 160px;">Keluar (-)</th>
                            <th class="pe-4 text-center" style="width: 110px;">Kasir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($mutasi)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <div class="py-4">
                                        <i class="fas fa-receipt fa-3x text-muted opacity-25 mb-3"></i>
                                        <h6 class="fw-bold text-dark">Tidak Ada Mutasi Ditemukan</h6>
                                        <p class="small text-muted mb-0">Tidak terdapat riwayat mutasi saldo pada periode dan filter yang dipilih.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($mutasi as $row): 
                                $met = strtolower($row['metode']);
                                $pill_class = 'channel-pill-tunai';
                                $icon = 'fa-money-bill-wave';
                                if ($met == 'transfer') {
                                    $pill_class = 'channel-pill-transfer';
                                    $icon = 'fa-university';
                                } elseif ($met == 'debit' || $met == 'kartu_kredit' || $met == 'edc') {
                                    $pill_class = 'channel-pill-edc';
                                    $icon = 'fa-credit-card';
                                } elseif ($met == 'ewallet') {
                                    $pill_class = 'channel-pill-ewallet';
                                    $icon = 'fa-qrcode';
                                }
                            ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark"><?= date('d/m/Y', strtotime($row['tanggal'])) ?></div>
                                        <div class="small text-muted font-mono"><i class="far fa-clock me-1"></i><?= substr($row['jam'], 0, 5) ?></div>
                                    </td>
                                    <td>
                                        <span class="channel-pill <?= $pill_class ?>">
                                            <i class="fas <?= $icon ?>"></i>
                                            <?= htmlspecialchars($row['channel_nama']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($row['nospk']) && $row['nospk'] != '-'): ?>
                                            <span class="badge bg-light text-dark font-mono border px-2 py-1">
                                                <i class="fas fa-tag me-1 text-indigo"></i><?= htmlspecialchars($row['nospk']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($row['pihak_terkait']) ?></div>
                                    </td>
                                    <td>
                                        <div class="text-muted small"><?= htmlspecialchars($row['keterangan']) ?></div>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($row['masuk'] > 0): ?>
                                            <span class="font-mono fw-bold text-success">
                                                +Rp <?= number_format($row['masuk'], 0, ',', '.') ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted font-mono">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($row['keluar'] > 0): ?>
                                            <span class="font-mono fw-bold text-danger">
                                                -Rp <?= number_format($row['keluar'], 0, ',', '.') ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted font-mono">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="pe-4 text-center">
                                        <span class="badge bg-light text-muted border px-2 py-1 small">
                                            <i class="fas fa-user me-1"></i><?= htmlspecialchars($row['kasir']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($mutasi)): ?>
                        <tfoot>
                            <tr class="bg-light fw-bold">
                                <td colspan="5" class="ps-4 text-uppercase">TOTAL MUTASI PERIODE INI</td>
                                <td class="text-end font-mono text-success fs-6">+Rp <?= number_format($total_masuk, 0, ',', '.') ?></td>
                                <td class="text-end font-mono text-danger fs-6">-Rp <?= number_format($total_keluar, 0, ',', '.') ?></td>
                                <td class="pe-4 text-center font-mono small text-muted"><?= count($mutasi) ?> Trx</td>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
$(document).ready(function() {
    if ($.fn.DataTable && $('#tableMutasiHarian tbody tr').length > 1) {
        $('#tableMutasiHarian').DataTable({
            language: {
                search: "Cari Mutasi:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ mutasi",
                paginate: {
                    previous: "<i class='fas fa-chevron-left'></i>",
                    next: "<i class='fas fa-chevron-right'></i>"
                }
            },
            pageLength: 25,
            ordering: false,
            responsive: true
        });
    }
});
</script>
