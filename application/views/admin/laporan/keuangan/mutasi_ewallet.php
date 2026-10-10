<?php
/**
 * Mutasi E-Wallet (System Record) - TGM Premium Edition
 * Tracking Digital Payments (QRIS, OVO, GOPAY, ShopeePay)
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
        --tgm-indigo: #6366f1;
        --tgm-emerald: #10b981;
        --tgm-rose: #f43f5e;
        --tgm-amber: #f59e0b;
        --tgm-violet: #8b5cf6;
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
        background: var(--tgm-violet);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.2);
    }

    .btn-premium-primary:hover {
        background: #7c3aed;
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
        @page { size: A4 landscape; margin: 1cm; }
        .no-print { display: none !important; }
        .premium-card { box-shadow: none !important; border: 1px solid #eee !important; border-radius: 0 !important; }
        .print-header { display: block !important; border-bottom: 3px double #333; margin-bottom: 20px; padding-bottom: 10px; }
    }

    .print-header { display: none; }
</style>

<div class="app-content py-4">
    <!-- Print Header -->
    <div class="print-header text-center w-100">
        <h2 class="mb-0 fw-bold"><?= strtoupper($this->session->userdata('nama_perusahaan') ?? 'TGM ERP SYSTEM') ?></h2>
        <h4 class="mb-1">LAPORAN MUTASI E-WALLET (DOMPET DIGITAL)</h4>
        <p class="mb-0">Periode: <?= date('d/m/Y', strtotime($start_date)) ?> s/d <?= date('d/m/Y', strtotime($end_date)) ?></p>
        <div class="small text-muted mt-1">Dicetak pada: <?= date('d/m/Y H:i') ?></div>
    </div>

    <div class="container-fluid">
        <!-- Header & Breadcrumb -->
        <div class="row align-items-center mb-5 no-print">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <h2 class="fw-900 text-dark ls-1 mb-1">MUTASI <span class="text-violet">E-WALLET</span></h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent p-0 mb-0">
                        <li class="breadcrumb-item small fw-bold text-muted">LAPORAN KEUANGAN</li>
                        <li class="breadcrumb-item small fw-bold text-violet active">DIGITAL CASH LOG</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <button type="button" class="btn btn-white border-2 border-violet text-violet rounded-pill px-4 fw-900 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSopEwallet">
                    <i class="fas fa-qrcode me-2"></i> PROTOKOL WALLET
                </button>
                <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 fw-900 shadow-sm ms-2">
                    <i class="fas fa-print me-2"></i> CETAK LAPORAN
                </button>
            </div>
        </div>

        <!-- Filter HUD -->
        <div class="premium-card filter-hud mb-5 no-print">
            <h6 class="fw-900 text-dark mb-4 text-uppercase ls-1 italic">
                <i class="fas fa-filter text-violet me-2"></i> Parameter Filter Digital Wallet
            </h6>
            <form action="" method="GET" class="row g-4 pt-2">
                <div class="col-md-10">
                    <div class="row g-3">
                        <div class="col-sm-3">
                            <label class="small fw-bold text-muted text-uppercase mb-2">Pilih E-Wallet</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-wallet text-muted"></i></span>
                                <select name="ewallet_id" class="form-select border-start-0 ps-0 select2" required>
                                    <option value="">-- Pilih Akun --</option>
                                    <?php foreach ($ewallet_list as $e): ?>
                                            <option value="<?= $e->id ?>" <?= ($selected_ewallet == $e->id) ? 'selected' : '' ?>>
                                                <?= $e->ewallet_nama ?> (<?= $e->wallet_ket ?>)
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
                        <i class="fas fa-bolt me-2"></i> ANALISIS WALLET
                    </button>
                </div>
            </form>
        </div>

        <?php if ($selected_ewallet): ?>
                <!-- Premium Stat Cards -->
                <div class="row g-4 mb-5">
                    <div class="col-xl-3 col-md-6">
                        <div class="premium-card p-4 border-start border-5 border-success">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-muted small fw-bold text-uppercase mb-0 ls-1">Total Masuk (In)</h6>
                                <div class="stat-icon bg-success bg-opacity-10 text-success shadow-sm">
                                    <i class="fas fa-arrow-down"></i>
                                </div>
                            </div>
                            <h3 class="fw-900 text-dark font-mono mb-0">Rp <?= number_format($total_masuk, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="premium-card p-4 border-start border-5 border-rose">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-muted small fw-bold text-uppercase mb-0 ls-1">Total Keluar (Out)</h6>
                                <div class="stat-icon bg-rose bg-opacity-10 text-rose shadow-sm">
                                    <i class="fas fa-arrow-up"></i>
                                </div>
                            </div>
                            <h3 class="fw-900 text-dark font-mono mb-0">Rp <?= number_format($total_keluar, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="premium-card p-4 border-start border-5 border-violet">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-muted small fw-bold text-uppercase mb-0 ls-1">Net Flow</h6>
                                <div class="stat-icon bg-violet bg-opacity-10 text-violet shadow-sm">
                                    <i class="fas fa-exchange-alt"></i>
                                </div>
                            </div>
                            <h3 class="fw-900 font-mono mb-0 <?= $net_movement >= 0 ? 'text-success' : 'text-rose' ?>">
                                Rp <?= number_format($net_movement, 0, ',', '.') ?>
                            </h3>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="premium-card p-4 border-start border-5 border-dark">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-muted small fw-bold text-uppercase mb-0 ls-1">Total Record</h6>
                                <div class="stat-icon bg-dark bg-opacity-10 text-dark shadow-sm">
                                    <i class="fas fa-list-ul"></i>
                                </div>
                            </div>
                            <h3 class="fw-900 text-dark font-mono mb-0"><?= $transaction_count ?> <small class="fw-bold">ENTRI</small></h3>
                        </div>
                    </div>
                </div>

                <!-- Main Log Card -->
                <div class="premium-card overflow-hidden mb-5">
                    <div class="p-4 border-bottom d-flex align-items-center justify-content-between bg-white">
                        <?php
                        $sel_wallet = null;
                        foreach ($ewallet_list as $e) {
                            if ($e->id == $selected_ewallet) {
                                $sel_wallet = $e;
                                break;
                            }
                        }
                        ?>
                        <div>
                            <h5 class="fw-900 mb-0 ls-1 uppercase">Log Transaksi Digital (System Record)</h5>
                            <p class="small text-muted fw-bold mb-0">Wallet: <span class="text-violet"><?= $sel_wallet ? $sel_wallet->ewallet_nama : 'Unknown' ?></span> - <?= $sel_wallet ? $sel_wallet->wallet_ket : '' ?></p>
                        </div>
                        <div class="text-end no-print">
                            <div class="bg-violet bg-opacity-10 text-violet p-3 rounded-4">
                                <div class="small fw-bold text-uppercase">Tracking Period</div>
                                <div class="fw-900"><?= date('d M', strtotime($start_date)) ?> - <?= date('d M Y', strtotime($end_date)) ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-premium mb-0" id="tableEwalletPremium">
                            <thead>
                                <tr>
                                    <th class="ps-4">Timestamp</th>
                                    <th>Alur & Tipe</th>
                                    <th>Referensi & Pelanggan</th>
                                    <th>Keterangan</th>
                                    <th class="text-end">Jumlah Masuk</th>
                                    <th class="text-end pe-4">Jumlah Keluar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($mutasi)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="py-4">
                                                    <i class="fas fa-wallet fa-3x text-muted opacity-25 mb-3"></i>
                                                    <h6 class="text-muted fw-bold">Belum ada aktivitas digital terekam akun ini</h6>
                                                </div>
                                            </td>
                                        </tr>
                                <?php else: ?>
                                        <?php foreach ($mutasi as $m): ?>
                                                <tr>
                                                    <td class="ps-4">
                                                        <div class="fw-bold text-dark"><?= date('d/m/Y', strtotime($m['tanggal'])) ?></div>
                                                        <div class="small text-muted fw-bold"><?= isset($m['jam']) ? date('H:i', strtotime($m['jam'])) : '-' ?></div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="flow-indicator <?= $m['arus'] == 'masuk' ? 'bg-success bg-opacity-10 text-success' : 'bg-rose bg-opacity-10 text-rose' ?>">
                                                                <i class="fas <?= $m['arus'] == 'masuk' ? 'fa-arrow-down' : 'fa-arrow-up' ?> x-small"></i>
                                                            </div>
                                                            <div>
                                                                <div class="fw-900 small text-uppercase" style="font-size: 0.65rem;">
                                                                    <?php if ($m['tipe'] == 'transaksi'): ?>
                                                                            <span class="text-primary"><i class="fas fa-cash-register me-1"></i> SALES</span>
                                                                    <?php elseif ($m['tipe'] == 'piutang'): ?>
                                                                            <span class="text-warning"><i class="fas fa-file-invoice-dollar me-1"></i> AR PAY</span>
                                                                    <?php else: ?>
                                                                            <span class="text-rose"><i class="fas fa-cog me-1"></i> OPERATIONAL</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="fw-900 text-violet ls-1"><?= $m['nospk'] ?></div>
                                                        <div class="small text-muted fw-bold"><?= $m['konsumen'] ?: 'UMUM' ?></div>
                                                    </td>
                                                    <td>
                                                        <div class="small fw-bold text-muted lh-base" style="max-width:300px;"><?= $m['keterangan'] ?: '-' ?></div>
                                                    </td>
                                                    <td class="text-end text-success fw-bold font-mono">
                                                        <?= ($m['arus'] == 'masuk') ? 'Rp ' . number_format($m['jumlah'], 0, ',', '.') : '-' ?>
                                                    </td>
                                                    <td class="text-end pe-4 text-rose fw-bold font-mono">
                                                        <?= ($m['arus'] == 'keluar') ? 'Rp ' . number_format($m['jumlah'], 0, ',', '.') : '-' ?>
                                                    </td>
                                                </tr>
                                        <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="bg-dark text-white">
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-end py-4 fw-900 ls-1 uppercase small ps-4">Accumulated Volume</td>
                                    <td class="text-end py-4 text-success font-mono fw-900 h5 mb-0">Rp <?= number_format($total_masuk, 0, ',', '.') ?></td>
                                    <td class="text-end pe-4 py-4 text-rose font-mono fw-900 h5 mb-0">Rp <?= number_format($total_keluar, 0, ',', '.') ?></td>
                                </tr>
                                <tr style="background: #000;">
                                    <td colspan="4" class="text-end py-3 fw-900 uppercase small ps-4 text-white-50">Grand Net Movement (Period)</td>
                                    <td colspan="2" class="text-end pe-4 py-3 fw-900 font-mono h4 mb-0 text-violet">Rp <?= number_format($net_movement, 0, ',', '.') ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
        <?php else: ?>
                <div class="text-center py-5 no-print">
                    <i class="fas fa-mobile-alt fa-5x text-muted opacity-25 mb-4"></i>
                    <h4 class="fw-900 text-muted mt-4">Pilih Akun E-Wallet untuk Dimulau Audit</h4>
                    <p class="text-muted px-5">Analisis ini akan menarik data dari sistem untuk dicocokkan dengan mutasi aplikasi (OVO, GoBiz, Shopee, dll).</p>
                </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal SOP E-Wallet -->
<div class="modal fade" id="modalSopEwallet" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-violet rounded-circle p-2 me-3">
                        <i class="fas fa-shield-alt text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-900 ls-1 mb-0 text-uppercase">Protokol Audit E-Wallet</h5>
                        <small class="text-white-50 fw-bold">Verifikasi Saldo Digital vs Record ERP</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-light">
                <div class="bg-violet bg-opacity-10 p-4 border-bottom border-violet border-opacity-10">
                    <h6 class="fw-900 text-violet mb-1 uppercase">KEAMANAN TRANSAKSI DIGITAL</h6>
                    <p class="small text-muted mb-0 fw-bold italic">" Pastikan setiap notifikasi pembayaran dari aplikasi pihak ketiga telah dikonfirmasi sebelum menyerahkan produk ke konsumen. "</p>
                </div>
                <div class="p-4 p-md-5">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="bg-white p-4 rounded-4 shadow-sm h-100 border-start border-4 border-violet">
                                <h6 class="fw-900 text-dark mb-3 uppercase">1. VERIFIKASI APLIKASI</h6>
                                <ol class="x-small text-muted fw-bold ps-3 mb-0 lh-lg">
                                    <li>Buka riwayat transaksi di aplikasi E-Wallet (OVO/GoPay/DANA).</li>
                                    <li>Cocokkan nomor invoice atau jumlah Rp dengan baris di laporan sistem ini.</li>
                                    <li>Pastikan status transaksi di aplikasi adalah **"BERHASIL / COMPLETED"**.</li>
                                </ol>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-white p-4 rounded-4 shadow-sm h-100 border-start border-4 border-rose">
                                <h6 class="fw-900 text-dark mb-3 uppercase">2. PEMOTONGAN BIAYA (MDR)</h6>
                                <ul class="x-small text-muted fw-bold ps-3 mb-0 lh-lg">
                                    <li>Ketahuilah bahwa dana yang masuk ke rekening bank mungkin sudah dipotong biaya jasa.</li>
                                    <li>Cek apakah ada transaksi "In-Flight" (Gantung) yang belum cair ke akun bank utama.</li>
                                    <li>Catat selisih biaya admin sebagai pengeluaran operasional (jika belum otomatis).</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="bg-white p-4 rounded-4 shadow-sm border-start border-5 border-success position-relative">
                                <h6 class="fw-900 text-dark mb-3 uppercase"><i class="fas fa-check-double text-success me-2"></i>REKONSILIASI HARIAN</h6>
                                <p class="small text-muted mb-0 lh-base">
                                    Lakukan rekap harian untuk setiap jenis dompet digital. Jika ada transaksi yang terekam di sistem tapi tidak ada di aplikasi E-Wallet, segera laporkan ke Admin Finance untuk pelacakan nota.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white p-3 border-top">
                <button type="button" class="btn btn-violet text-white rounded-pill px-5 fw-900 shadow-sm" data-bs-dismiss="modal">SAYA MENGERTI & AKAN TELITI</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        if ($('.select2').length) {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }

        if ($('#tableEwalletPremium').length) {
            $('#tableEwalletPremium').DataTable({
                "order": [[0, "desc"]],
                "pageLength": 50,
                "language": {
                    "url": "<?= base_url('assets/js/datatables-id.json') ?>",
                    "search": "",
                    "searchPlaceholder": "Cari SPK / Konsumen...",
                    "info": "AUDIT DATA _START_ - _END_ DARI _TOTAL_",
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