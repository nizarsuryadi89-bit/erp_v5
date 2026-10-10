<?php
/**
 * Mutasi Cash (Tunai) Detail Report - TGM Premium Edition
 */
$current_perusahaan_id = $this->input->get('perusahaan_id') ?: $this->session->userdata('perusahaan_id');
$in = array_sum(array_column($mutasi, 'masuk'));
$out = array_sum(array_column($mutasi, 'keluar'));
$this->load->view('layout_admin/header_modul');
?>

<!-- Premium Design Tokens -->
<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">
<style>
    :root {
        --tgm-indigo: #4f46e5;
        --tgm-emerald: #10b981;
        --tgm-rose: #f43f5e;
        --tgm-amber: #f59e0b;
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

    .fw-900 {
        font-weight: 900;
    }

    .ls-1 {
        letter-spacing: -1px;
    }

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
        @page {
            size: A4 landscape;
            margin: 1cm;
        }

        .no-print {
            display: none !important;
        }

        .premium-card {
            box-shadow: none !important;
            border: 1px solid #eee !important;
            border-radius: 0 !important;
        }

        .print-header {
            display: block !important;
            border-bottom: 3px double #333;
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
        <h2 class="mb-0 fw-bold"><?= strtoupper($this->session->userdata('nama_perusahaan') ?? 'TGM ERP SYSTEM') ?></h2>
        <h4 class="mb-1">LAPORAN MUTASI KAS TUNAI (CASH)</h4>
        <p class="mb-0">Periode: <?= date('d/m/Y', strtotime($tgl_awal)) ?> s/d
            <?= date('d/m/Y', strtotime($tgl_akhir)) ?>
        </p>
        <div class="small text-muted mt-1">Dicetak pada: <?= date('d/m/Y H:i') ?></div>
    </div>

    <div class="container-fluid">
        <!-- Header & Breadcrumb -->
        <div class="row align-items-center mb-5 no-print">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <h2 class="fw-900 text-dark ls-1 mb-1">MUTASI KAS <span class="text-indigo">TUNAI</span></h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent p-0 mb-0">
                        <li class="breadcrumb-item small fw-bold text-muted">LAPORAN KEUANGAN</li>
                        <li class="breadcrumb-item small fw-bold text-indigo active">KASIR CASH LOG</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <button type="button"
                    class="btn btn-white border-2 border-indigo text-indigo rounded-pill px-4 fw-900 shadow-sm"
                    data-bs-toggle="modal" data-bs-target="#modalSopCashier">
                    <i class="fas fa-cash-register me-2"></i> PANDUAN KASIR
                </button>
                <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 fw-900 shadow-sm ms-2">
                    <i class="fas fa-print me-2"></i> CETAK LAPORAN
                </button>
            </div>
        </div>

        <!-- Filter HUD -->
        <div class="premium-card filter-hud mb-5 no-print">
            <h6 class="fw-900 text-dark mb-4 text-uppercase ls-1 italic">
                <i class="fas fa-filter text-indigo me-2"></i> Parameter Filter Kas
            </h6>
            <form action="" method="GET" class="row g-4 pt-2">
                <div class="col-md-10">
                    <div class="row g-3">
                        <?php if ($is_admin_owner): ?>
                            <div class="col-sm-3">
                                <label class="small fw-bold text-muted text-uppercase mb-2">Cabang / Unit</label>
                                <select name="perusahaan_id" class="form-select select2">
                                    <option value="1">SEMUA CABANG (KONSOLIDASI)</option>
                                    <?php foreach ($all_perusahaan as $p): ?>
                                        <option value="<?= $p->id_perusahaan ?>" <?= ($current_perusahaan_id == $p->id_perusahaan) ? 'selected' : '' ?>>
                                            <?= strtoupper($p->nama_perusahaan) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                        <div class="col-sm-3">
                            <label class="small fw-bold text-muted text-uppercase mb-2">Tanggal Mulai</label>
                            <input type="date" name="tgl_awal" class="form-control" value="<?= $tgl_awal ?>">
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold text-muted text-uppercase mb-2">Tanggal Akhir</label>
                            <input type="date" name="tgl_akhir" class="form-control" value="<?= $tgl_akhir ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-premium-primary w-100 py-3 rounded-4">
                        <i class="fas fa-bolt me-2"></i> ANALISIS KAS
                    </button>
                </div>
            </form>
        </div>

        <!-- Premium Stat Cards -->
        <div class="row g-4 mb-5">
            <div class="col-xl-4 col-md-6">
                <div class="premium-card p-4 border-start border-5 border-indigo">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted small fw-bold text-uppercase mb-0 ls-1">Saldo Awal</h6>
                        <div class="stat-icon bg-indigo bg-opacity-10 text-indigo shadow-sm">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                    <h3 class="fw-900 text-dark font-mono mb-1">Rp <?= number_format($saldo_awal, 0, ',', '.') ?></h3>
                    <p class="small text-muted mb-0 fw-bold italic">Per: <?= date('d M Y', strtotime($tgl_awal)) ?></p>
                </div>
            </div>
            <div class="col-xl-4 col-md-6">
                <div class="premium-card p-4 border-start border-5 border-success">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted small fw-bold text-uppercase mb-0 ls-1">Dana Masuk (Inflow)</h6>
                        <div class="stat-icon bg-success bg-opacity-10 text-success shadow-sm">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                    </div>
                    <h3 class="fw-900 text-success font-mono mb-1">Rp <?= number_format($in, 0, ',', '.') ?></h3>
                    <p class="small text-muted mb-0 fw-bold italic">B2C & Pelunasan Piutang</p>
                </div>
            </div>
            <div class="col-xl-4 col-md-6">
                <div class="premium-card p-4 border-start border-5 border-rose">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted small fw-bold text-uppercase mb-0 ls-1">Dana Keluar (Outflow)</h6>
                        <div class="stat-icon bg-rose bg-opacity-10 text-rose shadow-sm">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                    <h3 class="fw-900 text-rose font-mono mb-1">Rp <?= number_format($out, 0, ',', '.') ?></h3>
                    <p class="small text-muted mb-0 fw-bold italic">Operational Cash Expense</p>
                </div>
            </div>
        </div>

        <div class="premium-card overflow-hidden mb-5">
            <div class="p-4 border-bottom d-flex align-items-center justify-content-between bg-white">
                <div>
                    <h5 class="fw-900 mb-0 ls-1 uppercase">Buku Kas Tunai (Main Ledger)</h5>
                    <p class="small text-muted fw-bold mb-0 italic">Detail pergerakan fisik uang pada laci kasir.</p>
                </div>
                <div class="text-end">
                    <div class="small fw-bold text-muted text-uppercase mb-1">Saldo Akhir Fisik</div>
                    <?php
                    $final_run_balance = $saldo_awal;
                    foreach ($mutasi as $m) {
                        $final_run_balance += $m['masuk'];
                        $final_run_balance -= $m['keluar'];
                    }
                    ?>
                    <h4 class="fw-900 text-indigo font-mono mb-0">Rp
                        <?= number_format($final_run_balance, 0, ',', '.') ?>
                    </h4>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-premium mb-0" id="tableMutasiCash">
                    <thead>
                        <tr>
                            <th class="ps-4">Timestamp</th>
                            <th>Klasifikasi</th>
                            <th>Referensi & Konsumen</th>
                            <th>Keterangan</th>
                            <th class="text-end">Masuk</th>
                            <th class="text-end">Keluar</th>
                            <th class="text-end pe-4">S. Running</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Saldo Awal Row -->
                        <tr class="bg-light bg-opacity-50">
                            <td class="ps-4 text-muted fw-bold small uppercase ls-1">Opening Balance</td>
                            <td></td>
                            <td class="text-center">
                                <span class="badge bg-dark rounded-pill px-3 py-2 fw-900 opacity-75">SALDO AWAL
                                    PERIODE</span>
                            </td>
                            <td></td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end pe-4 fw-900 font-mono text-dark opacity-50">Rp
                                <?= number_format($saldo_awal, 0, ',', '.') ?>
                            </td>
                        </tr>

                        <?php
                        $running_balance = $saldo_awal;
                        foreach ($mutasi as $m):
                            $running_balance += $m['masuk'];
                            $running_balance -= $m['keluar'];

                            $source_pill = 'bg-primary';
                            if ($m['sumber'] == 'PIUTANG')
                                $source_pill = 'bg-warning text-dark';
                            if ($m['sumber'] == 'PENGELUARAN')
                                $source_pill = 'bg-rose';
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark"><?= date('d/m/Y', strtotime($m['tanggal'])) ?></div>
                                    <div class="small text-muted fw-bold">
                                        <?= ($m['jam'] != '00:00:00') ? date('H:i', strtotime($m['jam'])) : '-' ?>
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="badge <?= $source_pill ?> rounded-pill px-3 py-2 fw-900 ls-1 uppercase shadow-sm"
                                        style="font-size: 0.6rem;">
                                        <?= $m['sumber'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-900 text-indigo ls-1"><?= $m['nospk'] ?></div>
                                    <div class="small text-muted fw-bold"><?= $m['konsumen'] ?: 'UMUM' ?></div>
                                </td>
                                <td>
                                    <div class="small fw-bold text-muted lh-base" style="max-width:300px;">
                                        <?= $m['keterangan'] ?>
                                    </div>
                                </td>
                                <td class="text-end text-success fw-bold">
                                    <?= ($m['masuk'] > 0) ? '<span class="font-mono">+ Rp ' . number_format($m['masuk'], 0, ',', '.') . '</span>' : '-' ?>
                                </td>
                                <td class="text-end text-rose fw-bold">
                                    <?= ($m['keluar'] > 0) ? '<span class="font-mono">- Rp ' . number_format($m['keluar'], 0, ',', '.') . '</span>' : '-' ?>
                                </td>
                                <td class="text-end pe-4 fw-900 text-dark">
                                    <span class="font-mono">Rp <?= number_format($running_balance, 0, ',', '.') ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-dark text-white">
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-end py-4 fw-900 ls-1 uppercase small ps-4">Accumulated Totals</td>
                            <td class="text-end py-4 text-success font-mono fw-900 italic">Rp
                                <?= number_format($in, 0, ',', '.') ?>
                            </td>
                            <td class="text-end py-4 text-rose font-mono fw-900 italic">Rp
                                <?= number_format($out, 0, ',', '.') ?>
                            </td>
                            <td class="text-end pe-4 py-4">
                                <div class="small text-white-50 fw-bold uppercase" style="font-size: 0.6rem;">Current
                                    Physical</div>
                                <div class="h5 mb-0 font-mono fw-900">Rp
                                    <?= number_format($running_balance, 0, ',', '.') ?>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal SOP Cashier -->
<div class="modal fade" id="modalSopCashier" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-amber rounded-circle p-2 me-3">
                        <i class="fas fa-coins text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-900 ls-1 mb-0 text-uppercase">Manual Operasional Kas Tunai</h5>
                        <small class="text-white-50 fw-bold">Protokol Penanganan Uang Fisik Kasir</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-light">
                <div class="bg-amber bg-opacity-10 p-4 border-bottom border-amber border-opacity-10">
                    <h6 class="fw-900 text-amber mb-1 uppercase">PEMBERSIHAN SALDO & CLOSING LACI</h6>
                    <p class="small text-muted mb-0 fw-bold italic">"Ketelitian adalah mata uang utama di meja kasir."
                    </p>
                </div>
                <div class="p-4 p-md-5">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="bg-white p-4 rounded-4 shadow-sm h-100 border-start border-4 border-indigo">
                                <h6 class="fw-900 text-dark mb-3 uppercase">1. PROSEDUR SERAH TERIMA</h6>
                                <ol class="x-small text-muted fw-bold ps-3 mb-0 lh-lg">
                                    <li>Hitung **Uang Fisik** di laci sebelum mulai shift (Saldo Awal).</li>
                                    <li>Pastikan uang kecil (pecahan kecil) cukup untuk kembalian.</li>
                                    <li>Cek kembali rekaman SPK yang dibayar "Cash" pada shift sebelumnya.</li>
                                </ol>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-white p-4 rounded-4 shadow-sm h-100 border-start border-4 border-rose">
                                <h6 class="fw-900 text-dark mb-3 uppercase">2. PENGELUARAN CASH</h6>
                                <ul class="x-small text-muted fw-bold ps-3 mb-0 lh-lg">
                                    <li>SETIAP pengeluaran uang laci WAJIB ada nota fisik/kwitansi.</li>
                                    <li>Input segera di sistem agar **Running Balance** tetap sinkron.</li>
                                    <li>Batas maksimal pengeluaran tunai disesuaikan kebijakan limit cabang.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-12">
                            <div
                                class="bg-white p-4 rounded-4 shadow-sm border-start border-5 border-success position-relative">
                                <h6 class="fw-900 text-dark mb-3 uppercase"><i
                                        class="fas fa-check-circle text-success me-2"></i>REKONSILIASI CLOSING (END OF
                                    DAY)</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <p class="small text-muted mb-0 lh-base">
                                            Bandingkan baris **SALDO AKHIR FISIK** di laporan ini dengan jumlah uang
                                            nyata yang ada di dompet/laci kasir.
                                        </p>
                                    </div>
                                    <div class="col-md-6 border-start ps-4">
                                        <div class="x-small text-muted fw-bold">JIKA SELISIH (SHORTAGE/OVERAGE):</div>
                                        <p class="x-small text-rose fw-900 mb-0 italic mt-1">
                                            Wajib membuat Berita Acara Selisih Kas dan melakukan tracking nota yang
                                            tertinggal/salah input.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white p-3 border-top">
                <button type="button" class="btn btn-warning rounded-pill px-5 fw-900 shadow-sm"
                    data-bs-dismiss="modal">SAYA MENGERTI & AKAN TELITI</button>
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

        $('#tableMutasiCash').DataTable({
            "order": [], // Keep original running balance order
            "pageLength": 100,
            "language": {
                "url": "<?= base_url('assets/js/datatables-id.json') ?>",
                "search": "",
                "searchPlaceholder": "Cari SPK / Keterangan...",
                "info": "AUDIT BARIS _START_ - _END_ DARI _TOTAL_",
                "paginate": { "previous": "<i class='fas fa-chevron-left'></i>", "next": "<i class='fas fa-chevron-right'></i>" }
            },
            "dom": "<'row mb-3 no-print'<'col-sm-6'l><'col-sm-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row mt-3 no-print'<'col-sm-5'i><'col-sm-7'p>>",
            drawCallback: function () {
                $('.dataTables_paginate > .pagination').addClass('pagination-sm');
            }
        });
    });
</script>