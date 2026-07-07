<?php
/**
 * Mutasi Bank (System Record) - TGM Premium Edition
 * Comparing System Records (Transactions & AR) with Bank Statement
 */
$current_perusahaan_id = $this->input->get('perusahaan_id') ?: $this->session->userdata('perusahaan_id');
$net_movement = (isset($total_masuk) ? $total_masuk : 0) - (isset($total_keluar) ? $total_keluar : 0);
$transaction_count = isset($mutasi) ? count($mutasi) : 0;

$this->load->view('layout_admin/header_modul');
?>

<!-- Premium Design Tokens -->
<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">
<style>
    :root {
        --tgm-indigo: #4f46e5;
        --tgm-emerald: #10b981;
        --tgm-rose: #f43f5e;
        --tgm-slate-50: #f8fafc;
        --tgm-slate-100: #f1f5f9;
        --tgm-slate-200: #e2e8f0;
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
        transition: transform 0.3s ease;
        background: #fff;
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
        padding: 18px 15px;
        border: none;
    }

    .table-premium tbody td {
        padding: 16px 15px;
        vertical-align: middle;
        color: var(--tgm-slate-900);
        border-bottom: 1px solid var(--tgm-slate-100);
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
    }

    .btn-premium-primary:hover {
        background: #4338ca;
        color: white;
        transform: translateY(-2px);
    }

    .flow-indicator {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
    }

    @media print {
        .no-print { display: none !important; }
        .premium-card { box-shadow: none !important; border: 1px solid #eee !important; }
    }
</style>

<div class="app-content py-4">
    <div class="container-fluid">
        <!-- Header & Breadcrumb -->
        <div class="row align-items-center mb-5 no-print">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <h2 class="fw-900 text-dark ls-1 mb-1">MUTASI BANK <span class="text-indigo">(SYSTEM RECORD)</span></h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent p-0 mb-0">
                        <li class="breadcrumb-item small fw-bold text-muted">LAPORAN KEUANGAN</li>
                        <li class="breadcrumb-item small fw-bold text-indigo active">AUDIT REKONSILIASI</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <button type="button" class="btn btn-white border-2 border-indigo text-indigo rounded-pill px-4 fw-900 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSopRekonsiliasi">
                    <i class="fas fa-book-reader me-2"></i> PANDUAN REKONSILIASI
                </button>
                <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 fw-900 shadow-sm ms-2">
                    <i class="fas fa-print me-2"></i> CETAK LAPORAN
                </button>
            </div>
        </div>

        <!-- Filter HUD -->
        <div class="premium-card filter-hud mb-5 no-print">
            <h6 class="fw-900 text-dark mb-4 text-uppercase ls-1 italic">
                <i class="fas fa-filter text-indigo me-2"></i> Parameter Audit Mutasi
            </h6>
            <form action="" method="GET" class="row g-4 pt-2">
                <div class="col-md-10">
                    <div class="row g-3">
                        <div class="col-sm-3">
                            <label class="small fw-bold text-muted text-uppercase mb-2">Pilih Rekening Bank</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-university text-muted"></i></span>
                                <select name="bank_id" class="form-select border-start-0 ps-0" required>
                                    <option value="">-- Pilih Rekening --</option>
                                    <?php foreach ($bank_list as $b): ?>
                                            <option value="<?= $b->id ?>" <?= ($selected_bank == $b->id) ? 'selected' : '' ?>>
                                                <?= $b->nama_bank ?> - <?= $b->no_rekening ?>
                                            </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold text-muted text-uppercase mb-2">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold text-muted text-uppercase mb-2">Tanggal Akhir</label>
                            <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-premium-primary w-100 py-3 rounded-4">
                        <i class="fas fa-bolt me-2"></i> ANALISIS
                    </button>
                </div>
            </form>
        </div>

        <?php if ($selected_bank): ?>
                <!-- Premium Stat Cards -->
                <div class="row g-4 mb-5">
                    <div class="col-xl-3 col-md-6">
                        <div class="premium-card p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="stat-icon bg-success bg-opacity-10 text-success">
                                    <i class="fas fa-arrow-down"></i>
                                </div>
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">INFLOW</span>
                            </div>
                            <h6 class="text-muted small fw-bold text-uppercase mb-1 ls-1">Total Dana Masuk</h6>
                            <h3 class="fw-900 text-dark font-mono mb-0">Rp <?= number_format($total_masuk, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="premium-card p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="stat-icon bg-rose bg-opacity-10 text-rose">
                                    <i class="fas fa-arrow-up"></i>
                                </div>
                                <span class="badge bg-rose bg-opacity-10 text-rose rounded-pill px-3">OUTFLOW</span>
                            </div>
                            <h6 class="text-muted small fw-bold text-uppercase mb-1 ls-1">Total Dana Keluar</h6>
                            <h3 class="fw-900 text-dark font-mono mb-0">Rp <?= number_format($total_keluar, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="premium-card p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="stat-icon bg-indigo bg-opacity-10 text-indigo">
                                    <i class="fas fa-balance-scale"></i>
                                </div>
                                <span class="badge bg-indigo bg-opacity-10 text-indigo rounded-pill px-3">NET</span>
                            </div>
                            <h6 class="text-muted small fw-bold text-uppercase mb-1 ls-1">Pergerakan Bersih</h6>
                            <h3 class="fw-900 font-mono mb-0 <?= $net_movement >= 0 ? 'text-success' : 'text-rose' ?>">
                                <?= $net_movement >= 0 ? '+' : '' ?>Rp <?= number_format($net_movement, 0, ',', '.') ?>
                            </h3>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="premium-card p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="stat-icon bg-dark bg-opacity-10 text-dark">
                                    <i class="fas fa-history"></i>
                                </div>
                                <span class="badge bg-dark bg-opacity-10 text-dark rounded-pill px-3">VOL</span>
                            </div>
                            <h6 class="text-muted small fw-bold text-uppercase mb-1 ls-1">Total Aktivitas</h6>
                            <h3 class="fw-900 text-dark font-mono mb-0"><?= $transaction_count ?> <small class="fw-bold">BARIS</small></h3>
                        </div>
                    </div>
                </div>

                <!-- Main Log Card -->
                <div class="premium-card overflow-hidden mb-5">
                    <div class="p-4 border-bottom d-flex align-items-center justify-content-between">
                        <?php
                        $selected_bank_info = null;
                        foreach ($bank_list as $b) {
                            if ($b->id == $selected_bank) {
                                $selected_bank_info = $b;
                                break;
                            }
                        }
                        ?>
                        <div>
                            <h5 class="fw-900 mb-0 ls-1">LOG TRANSAKSI SISTEM</h5>
                            <p class="small text-muted fw-bold mb-0">Rekening: <?= $selected_bank_info ? $selected_bank_info->nama_bank : 'Unknown' ?> - <?= $selected_bank_info ? $selected_bank_info->no_rekening : '' ?></p>
                        </div>
                        <div class="bg-indigo bg-opacity-10 text-indigo p-3 rounded-4 text-center">
                            <div class="small fw-bold text-uppercase">Audit Range</div>
                            <div class="fw-900"><?= date('d M', strtotime($start_date)) ?> - <?= date('d M Y', strtotime($end_date)) ?></div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-premium mb-0" id="tableMutasiPremium">
                            <thead>
                                <tr>
                                    <th class="ps-4">Timestamp</th>
                                    <th>Alur & Tipe</th>
                                    <th>Referensi & Konsumen</th>
                                    <th>Keterangan</th>
                                    <th class="text-end">Debet (Masuk)</th>
                                    <th class="text-end pe-4">Kredit (Keluar)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($mutasi)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="py-4">
                                                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                                    <h6 class="text-muted fw-bold">Belum ada aktivitas terekam pada periode ini</h6>
                                                </div>
                                            </td>
                                        </tr>
                                <?php else: ?>
                                        <?php foreach ($mutasi as $m): ?>
                                                <tr>
                                                    <td class="ps-4">
                                                        <div class="fw-bold text-dark"><?= date('d/m/Y', strtotime($m['tanggal'])) ?></div>
                                                        <div class="small text-muted"><?= date('H:i', strtotime($m['tanggal'])) ?></div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="flow-indicator <?= $m['arus'] == 'masuk' ? 'bg-success bg-opacity-10 text-success' : 'bg-rose bg-opacity-10 text-rose' ?>">
                                                                <i class="fas <?= $m['arus'] == 'masuk' ? 'fa-arrow-down' : 'fa-arrow-up' ?> x-small"></i>
                                                            </div>
                                                            <div>
                                                                <div class="fw-900 small text-uppercase" style="font-size: 0.65rem;">
                                                                    <?php if ($m['tipe'] == 'transaksi'): ?>
                                                                            <span class="text-primary"><i class="fas fa-cash-register me-1"></i> DIRECT</span>
                                                                    <?php elseif ($m['tipe'] == 'piutang'): ?>
                                                                            <span class="text-warning"><i class="fas fa-file-invoice-dollar me-1"></i> AR ADJUST</span>
                                                                    <?php else: ?>
                                                                            <span class="text-rose"><i class="fas fa-external-link-alt me-1"></i> OPR EXP</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="fw-900 ls-1 text-indigo"><?= $m['nospk'] ?></div>
                                                        <div class="small text-muted fw-bold"><?= $m['konsumen'] ?: 'UMUM' ?></div>
                                                    </td>
                                                    <td>
                                                        <div class="small fw-bold text-muted lh-base"><?= $m['keterangan'] ?: '-' ?></div>
                                                    </td>
                                                    <td class="text-end">
                                                        <?php if ($m['arus'] == 'masuk'): ?>
                                                                <span class="fw-900 font-mono text-success">Rp <?= number_format($m['jumlah'], 0, ',', '.') ?></span>
                                                        <?php else: ?>
                                                                <span class="text-muted opacity-25">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-end pe-4">
                                                        <?php if ($m['arus'] == 'keluar'): ?>
                                                                <span class="fw-900 font-mono text-rose">Rp <?= number_format($m['jumlah'], 0, ',', '.') ?></span>
                                                        <?php else: ?>
                                                                <span class="text-muted opacity-25">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                        <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="bg-dark text-white">
                                <tr>
                                    <td colspan="4" class="text-end fw-900 ps-4 py-4 uppercase">Sistem Saldo Aggregate Periode</td>
                                    <td class="text-end py-4">
                                        <div class="small text-white-50 fw-bold uppercase" style="font-size: 0.6rem;">Total Masuk</div>
                                        <div class="fw-900 font-mono h5 mb-0">Rp <?= number_format($total_masuk, 0, ',', '.') ?></div>
                                    </td>
                                    <td class="text-end pe-4 py-4">
                                        <div class="small text-white-50 fw-bold uppercase" style="font-size: 0.6rem;">Total Keluar</div>
                                        <div class="fw-900 font-mono h5 mb-0">Rp <?= number_format($total_keluar, 0, ',', '.') ?></div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
        <?php else: ?>
                <div class="text-center py-5 no-print">
                    <i class="fas fa-university fa-5x text-muted opacity-25 mb-4"></i>
                    <h4 class="fw-900 text-muted mt-4">Silakan Pilih Rekening & Periode Audit</h4>
                    <p class="text-muted px-5">Data mutasi sistem akan ditampilkan setelah Anda menentukan parameter filter di HUD atas.</p>
                </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal SOP Rekonsiliasi -->
<div class="modal fade" id="modalSopRekonsiliasi" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-indigo rounded-circle p-2 me-3">
                        <i class="fas fa-shield-alt text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-900 ls-1 mb-0 text-uppercase">Protokol Rekonsiliasi Bank</h5>
                        <small class="text-white-50 fw-bold">Manual Audit Keselarasan Sistem vs Realitas</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-light">
                <div class="bg-indigo bg-opacity-10 p-4 border-bottom border-indigo border-opacity-10">
                    <h6 class="fw-900 text-indigo mb-1">PENTING: REKONSILIASI ADALAH LAYAR PERTAHANAN KEUANGAN</h6>
                    <p class="small text-muted mb-0 fw-bold">Pastikan angka di sistem ini AKURAT 100% dengan Mutasi asli di E-Banking Anda.</p>
                </div>
                <div class="p-4 p-md-5">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="bg-white p-4 rounded-4 shadow-sm h-100 border-start border-4 border-indigo">
                                <h6 class="fw-900 text-dark mb-3 uppercase">1. PROSES VERIFIKASI</h6>
                                <p class="small text-muted mb-2 lh-base text-justify">Bandingkan setiap baris di tabel ini dengan **Rekening Koran** asli Anda.</p>
                                <ul class="x-small text-muted fw-bold ps-3 mb-0">
                                    <li>Cek Nominal (sampai digit terakhir).</li>
                                    <li>Cek Tanggal Valuta (Waktu Real Time).</li>
                                    <li>Cek Sumber Dana (Nama Pengirim).</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-white p-4 rounded-4 shadow-sm h-100 border-start border-4 border-emerald">
                                <h6 class="fw-900 text-dark mb-3 uppercase">2. TINDAKAN SELISIH</h6>
                                <p class="small text-muted mb-2 lh-base text-justify">Jika data tidak cocok, lakukan investigasi segera.</p>
                                <ul class="x-small text-muted fw-bold ps-3 mb-0 text-justify">
                                    <li>Ada uang masuk liar? Cek Customer Deposit.</li>
                                    <li>Biaya admin bank? Input via Pengeluaran.</li>
                                    <li>SPK belum lunas di sistem? Update Pelunasan.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="bg-white p-4 rounded-4 shadow-sm border-start border-5 border-warning text-center">
                                <h6 class="fw-900 text-dark mb-3 uppercase">TIMELINE AUDIT WAJIB</h6>
                                <div class="d-flex justify-content-around align-items-center py-2">
                                    <div class="text-center">
                                        <div class="h4 fw-900 text-indigo mb-0">DAILY</div>
                                        <span class="x-small text-muted fw-bold">Monitoring</span>
                                    </div>
                                    <div class="h4 text-muted opacity-25">|</div>
                                    <div class="text-center">
                                        <div class="h4 fw-900 text-warning mb-0">WEEKLY</div>
                                        <span class="x-small text-muted fw-bold">Sync Check</span>
                                    </div>
                                    <div class="h4 text-muted opacity-25">|</div>
                                    <div class="text-center">
                                        <div class="h4 fw-900 text-dark mb-0">MONTHLY</div>
                                        <span class="x-small text-muted fw-bold">Full Reconcile</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white p-3 border-top">
                <button type="button" class="btn btn-primary rounded-pill px-5 fw-900 shadow-sm" data-bs-dismiss="modal">SAYA MENGERTI & AKAN PATUH</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        if ($('#tableMutasiPremium').length) {
            $('#tableMutasiPremium').DataTable({
                "order": [[0, "desc"]],
                "pageLength": 50,
                "language": {
                    "url": "<?= base_url('assets/js/datatables-id.json') ?>",
                    "search": "",
                    "searchPlaceholder": "Cari referensi / konsumen...",
                    "info": "AUDIT BARIS _START_ SAMPAI _END_ DARI TOTAL _TOTAL_",
                    "paginate": { "previous": "<i class='fas fa-chevron-left'></i>", "next": "<i class='fas fa-chevron-right'></i>" }
                },
                "dom": "<'row mb-3 no-print'<'col-sm-6'l><'col-sm-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-3 no-print'<'col-sm-5'i><'col-sm-7'p>>",
                drawCallback: function () {
                    $('.dataTables_paginate > .pagination').addClass('pagination-sm');
                }
            });
        }
    });
</script>