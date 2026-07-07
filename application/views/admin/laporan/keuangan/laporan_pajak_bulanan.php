<?php
/**
 * Laporan Pajak Bulanan (Gunggung) - TGM Premium Edition
 * Provides tax breakdown for SPT Masa PPN compliance with premium styling.
 */
$current_perusahaan_id = $this->input->get('perusahaan_id') ?: $this->session->userdata('perusahaan_id');
?>

<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">

<style>
    /* ==========================================================================
    TGM PREMIUM TAX REPORT (PAJAK GUNGGUNG)
    ========================================================================== */
    :root {
        --fin-primary: #0f172a;
        --fin-accent: #6366f1;
        /* Indigo for Tax */
        --fin-success: #10b981;
        --fin-danger: #ef4444;
        --fin-warning: #f59e0b;
        --fin-info: #3b82f6;
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
        border-color: var(--fin-accent);
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

    /* Content Cards */
    .summary-card {
        background: white;
        border-radius: var(--card-radius);
        border: 1px solid #e2e8f0;
        box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .summary-header {
        background: var(--fin-primary);
        color: white;
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Table Styling */
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
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .monospaced {
        font-family: 'JetBrains Mono', 'SF Mono', monospace;
    }

    /* Tax Alert Premium */
    .tax-alert-premium {
        background: linear-gradient(135deg, #0f172a, #1e293b);
        border-radius: 16px;
        padding: 1.5rem;
        color: white;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.2);
        position: relative;
        overflow: hidden;
    }

    .tax-alert-premium::after {
        content: '\f19c';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        right: -20px;
        top: -10px;
        font-size: 8rem;
        opacity: 0.03;
        color: white;
    }

    @media print {
        .no-print {
            display: none !important;
        }

        .app-content {
            background: white;
            padding: 0 !important;
        }

        .summary-card {
            box-shadow: none;
            border: 1px solid #eee;
        }
    }
</style>

<div class="app-content mt-3 pb-5">
    <?php $this->load->view('layout_admin/header_modul'); ?>

    <div class="container-fluid py-4">

        <!-- TOP FILTER HUD -->
        <div class="filter-hud no-print">
            <form action="<?= base_url('laporan/laporan_pajak') ?>" method="GET" class="row align-items-center g-3">
                <div class="col-xl-4 col-lg-12">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 bg-light rounded-3 text-indigo border shadow-sm">
                            <i class="fas fa-landmark fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-900 text-dark ls-n1 uppercase">LAPORAN PAJAK</h6>
                            <p class="text-muted small mb-0 fw-bold italic text-uppercase" style="font-size: 0.65rem;">
                                Mekanisme Digunggung (SPT Masa PPN)</p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-6">
                    <div class="row g-2">
                        <div class="col-md-7">
                            <?php if ($is_admin_owner && !empty($all_perusahaan)): ?>
                                <select class="form-select" name="perusahaan_id" onchange="this.form.submit()">
                                    <?php foreach ($all_perusahaan as $p): ?>
                                        <option value="<?= $p->id_perusahaan ?>" <?= ($current_perusahaan_id == $p->id_perusahaan) ? 'selected' : '' ?>>
                                            🏢 <?= $p->nama_perusahaan ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <input type="text" class="form-control" value="<?= $perusahaan ?>" readonly>
                                <input type="hidden" name="perusahaan_id" value="<?= $current_perusahaan_id ?>">
                            <?php endif; ?>
                        </div>
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i
                                        class="far fa-calendar-alt text-muted"></i></span>
                                <input type="month" name="bulan" class="form-control" value="<?= $bulanini ?>"
                                    onchange="this.form.submit()">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-6 text-end">
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#modalSopPajak"
                            class="btn btn-outline-primary border-2 fw-900 rounded-3 px-3 shadow-sm">
                            <i class="fas fa-book-open"></i> PANDUAN SPT
                        </button>
                        <button type="button" onclick="window.print()"
                            class="btn btn-white text-dark shadow-sm rounded-pill fw-bold px-3 py-2">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- TAX ALERT BOX -->
        <div class="tax-alert-premium mb-5 no-print">
            <div class="bg-indigo bg-opacity-20 p-3 rounded-circle border border-indigo border-opacity-25">
                <i class="fas fa-file-invoice-dollar fa-2x text-indigo"></i>
            </div>
            <div>
                <h5 class="fw-900 mb-1 ls-n1 text-white">Validasi Pelaporan Pajak Digunggung</h5>
                <p class="mb-0 opacity-75 small fw-bold">
                    Data di bawah adalah akumulasi transaksi B2C (Eceran).
                    Pindahkan nilai DPP ke <span class="text-warning">Formulir 1111 AB Bagian I.B.2</span> di Aplikasi
                    E-Faktur.
                </p>
            </div>
        </div>

        <!-- KPI GRID -->
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6">
                <div class="cash-stat-card border-bottom border-indigo border-4">
                    <div class="cash-stat-icon bg-indigo bg-opacity-10 text-indigo"><i class="fas fa-cash-register"></i>
                    </div>
                    <div class="cash-stat-label">Total Penjualan (Gross)</div>
                    <div class="cash-stat-value">Rp <?= number_format($summary->total_penjualan ?? 0, 0, ',', '.') ?>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="cash-stat-card border-bottom border-success border-4">
                    <div class="cash-stat-icon bg-success bg-opacity-10 text-success"><i class="fas fa-calculator"></i>
                    </div>
                    <div class="cash-stat-label">Total DPP (Dasar Pajak)</div>
                    <div class="cash-stat-value text-success">Rp
                        <?= number_format($summary->total_dpp ?? 0, 0, ',', '.') ?>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="cash-stat-card border-bottom border-danger border-4">
                    <div class="cash-stat-icon bg-danger bg-opacity-10 text-danger"><i class="fas fa-percent"></i></div>
                    <div class="cash-stat-label">Total PPN 11% (Hutang Pajak)</div>
                    <div class="cash-stat-value text-danger">Rp
                        <?= number_format($summary->total_ppn ?? 0, 0, ',', '.') ?>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="cash-stat-card border-bottom border-info border-4">
                    <div class="cash-stat-icon bg-info bg-opacity-10 text-info"><i class="fas fa-receipt"></i></div>
                    <div class="cash-stat-label">Volume Transaksi</div>
                    <div class="cash-stat-value text-info">
                        <?= number_format($summary->jumlah_transaksi ?? 0, 0, ',', '.') ?> <small
                            class="fs-6">NOTA</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- MAIN DATA TABLE -->
            <div class="col-xl-8">
                <div class="summary-card shadow-sm h-100">
                    <div class="summary-header">
                        <div>
                            <h6 class="fw-900 mb-0 ls-n1 uppercase">RINCIAN TRANSAKSI DIGUNGGUNG</h6>
                            <small class="opacity-75 fw-bold text-uppercase ls-1" style="font-size: 0.65rem;">Periode:
                                <?= date('F Y', strtotime($bulanini . '-01')) ?></small>
                        </div>
                        <div class="d-flex gap-2 no-print">
                            <button id="btn-export-pajak-excel"
                                class="btn btn-sm btn-outline-primary rounded-3 fw-900 px-3">
                                <i class="fas fa-file-excel me-1"></i> EXCEL AUDIT
                            </button>
                            <button id="btn-export-pajak" class="btn btn-sm btn-success rounded-3 fw-900 px-3">
                                <i class="fas fa-file-csv me-1"></i> CSV E-FAKTUR
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-tx table-hover mb-0" id="datatable-pajak">
                            <thead>
                                <tr>
                                    <th class="ps-4">TANGGAL & JAM</th>
                                    <th>BUKTI / SPK</th>
                                    <th class="text-center">METODE BAYAR</th>
                                    <th class="text-end">DPP (NET)</th>
                                    <th class="text-end text-danger">PPN (11%)</th>
                                    <th class="text-end pe-4">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lap_pajak as $row): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-900 text-dark"><?= date('d/m/Y', strtotime($row->tgl)) ?></div>
                                            <small
                                                class="fw-bold text-muted monospaced"><?= date('H:i', strtotime($row->jam)) ?>
                                                WIB</small>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-light text-dark border px-2 fw-900 mb-1">#<?= $row->nospk ?></span>
                                            <div class="small fw-bold text-muted text-truncate" style="max-width: 150px;">
                                                <i class="fas fa-user-circle me-1 opacity-50"></i>
                                                <?= $row->konsumen_nama ?: 'Konsumen Umum' ?>
                                            </div>
                                        </td>
                                        <td class="text-center text-uppercase fw-bold"><?= $row->metode ?? '-' ?></td>
                                        <td class="text-end fw-bold monospaced">Rp
                                            <?= number_format($row->dpp, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-end text-danger fw-bold monospaced">Rp
                                            <?= number_format($row->ppn, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-end fw-900 text-dark pe-4 monospaced">Rp
                                            <?= number_format($row->total_bayar, 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr style="background: #f1f5f9;">
                                    <td colspan="2" class="py-3 ps-4 fw-900 text-muted uppercase small">Grand Total
                                        Periode</td>
                                    <td class="py-3 text-end fw-900 text-dark monospaced">Rp
                                        <?= number_format($summary->total_dpp ?? 0, 0, ',', '.') ?>
                                    </td>
                                    <td class="py-3 text-end fw-900 text-danger monospaced">Rp
                                        <?= number_format($summary->total_ppn ?? 0, 0, ',', '.') ?>
                                    </td>
                                    <td class="py-3 text-end pe-4 fw-900 text-primary monospaced fs-5">Rp
                                        <?= number_format($summary->total_penjualan ?? 0, 0, ',', '.') ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- SIDEBAR ANALYSIS -->
            <div class="col-xl-4">
                <!-- SPT PREVIEW -->
                <div class="summary-card mb-4 border-top border-indigo border-5">
                    <div class="p-4 border-bottom">
                        <h6 class="fw-900 text-dark mb-0 uppercase ls-n1"><i
                                class="fas fa-file-invoice text-indigo me-2"></i>REKONSILIASI SPT MASA</h6>
                    </div>
                    <div class="p-4">
                        <div
                            class="bg-indigo bg-opacity-10 p-4 rounded-4 mb-4 text-center border border-indigo border-opacity-20">
                            <small class="fw-900 text-indigo text-uppercase ls-1 d-block mb-1">Status Pelaporan</small>
                            <h4 class="fw-900 text-dark mb-0">MASA PAJAK
                                <?= strtoupper(date('M Y', strtotime($bulanini . '-01'))) ?>
                            </h4>
                        </div>

                        <div class="space-y-3">
                            <div
                                class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom dashed">
                                <span class="fw-900 text-muted small text-uppercase">Nilai Penyerahan (DPP)</span>
                                <span class="fw-900 text-dark monospaced">Rp
                                    <?= number_format($summary->total_dpp ?? 0, 0, ',', '.') ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-0">
                                <span class="fw-900 text-danger small text-uppercase">PPN Keluaran (11%)</span>
                                <span class="fw-900 text-danger monospaced">Rp
                                    <?= number_format($summary->total_ppn ?? 0, 0, ',', '.') ?></span>
                            </div>
                        </div>

                        <div class="alert alert-warning mt-4 rounded-3 border-0 small fw-bold mb-0">
                            <i class="fas fa-info-circle me-2"></i> Nilai ini wajib sama dengan rekapitulasi penjualan
                            di laporan harian.
                        </div>
                    </div>
                </div>

                <!-- PAYMENT BREAKDOWN -->
                <div class="summary-card">
                    <div class="p-4 border-bottom">
                        <h6 class="fw-900 text-dark mb-0 uppercase ls-n1"><i
                                class="fas fa-chart-pie text-muted me-2"></i>KORELASI CASHFLOW</h6>
                    </div>
                    <div class="p-0">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr class="bg-light">
                                    <th class="ps-4 small fw-900 text-muted uppercase py-2">METODE</th>
                                    <th class="text-end pe-4 small fw-900 text-muted uppercase py-2">TOTAL GROSS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($by_method as $m): ?>
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="fw-900 text-dark text-uppercase"><?= $m['metode'] ?></div>
                                            <small class="fw-bold text-muted monospaced">PPN: Rp
                                                <?= number_format($m['ppn'], 0) ?></small>
                                        </td>
                                        <td class="text-end pe-4 py-3 fw-900 text-dark monospaced">
                                            Rp <?= number_format($m['total'], 0, ',', '.') ?>
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

<!-- MODAL SOP PAJAK (PREMIUM) -->
<div class="modal fade" id="modalSopPajak" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-indigo rounded-circle p-2 me-3">
                        <i class="fas fa-landmark text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-900 ls-1 mb-0 text-uppercase">Buku Saku Kepatuhan Pajak</h5>
                        <small class="text-white-50 fw-bold">Manual Teknis Pelaporan SPT Masa PPN</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-light">
                <!-- Header Highlight -->
                <div class="bg-indigo bg-opacity-10 p-4 border-bottom border-indigo border-opacity-10">
                    <div class="row align-items-center">
                        <div class="col-md-9">
                            <h6 class="fw-900 text-indigo mb-1">PROTOKOL PELAPORAN DIGUNGGUNG (RETAIL)</h6>
                            <p class="small text-muted mb-0 fw-bold">Panduan ini wajib diikuti untuk menghindari sanksi administrasi & denda pajak.</p>
                        </div>
                        <div class="col-md-3 text-md-end mt-3 mt-md-0">
                            <span class="badge bg-indigo px-3 py-2 rounded-pill fw-900 shadow-sm">Form 1111 AB</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 p-md-5">
                    <div class="row g-4">
                        <!-- SECTION 1: DEFINISI -->
                        <div class="col-12">
                            <div class="bg-white p-4 rounded-4 shadow-sm border-start border-4 border-info">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-info-circle text-info me-2"></i>
                                    <h6 class="fw-900 text-dark mb-0 uppercase">1. APA ITU PAJAK DIGUNGGUNG?</h6>
                                </div>
                                <p class="small text-muted mb-0 lh-base text-justify">
                                    Adalah mekanisme pelaporan PPN untuk penyerahan Barang/Jasa Kena Pajak kepada **konsumen akhir** (perorangan) yang identitasnya tidak lengkap, di mana perusahaan **TIDAK** menerbitkan Faktur Pajak Standar (Kode 010) satu per satu.
                                </p>
                            </div>
                        </div>

                        <!-- SECTION 2: INPUT E-FAKTUR -->
                        <div class="col-md-6">
                            <div class="bg-white p-4 rounded-4 shadow-sm h-100 border-start border-4 border-indigo">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-indigo text-white rounded-3 p-2 me-3"><i class="fas fa-keyboard"></i></div>
                                    <h6 class="fw-900 text-dark mb-0 uppercase">2. INPUT KE E-FAKTUR</h6>
                                </div>
                                <ol class="x-small text-muted fw-bold ps-3 mb-0 lh-lg">
                                    <li>Buka Aplikasi E-Faktur Desktop.</li>
                                    <li>Masuk Menu **SPT** > **Formulir Induk 1111**.</li>
                                    <li>Pilih Tab **1111 AB** (Rekapitulasi Penyerahan).</li>
                                    <li>Isi Baris **I.B.2**: "Penyerahan Dalam Negeri dengan Faktur Pajak Digunggung".</li>
                                    <li>Masukkan nilai **TOTAL DPP** dari laporan ini.</li>
                                </ol>
                            </div>
                        </div>

                        <!-- SECTION 3: PEMBAYARAN -->
                        <div class="col-md-6">
                            <div class="bg-white p-4 rounded-4 shadow-sm h-100 border-start border-4 border-success">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-success text-white rounded-3 p-2 me-3"><i class="fas fa-money-bill-wave"></i></div>
                                    <h6 class="fw-900 text-dark mb-0 uppercase">3. PENYETORAN PPN</h6>
                                </div>
                                <ul class="x-small text-muted fw-bold ps-3 mb-0 lh-lg">
                                    <li>Buat Kode Billing dengan KAP: **411211** & KJS: **100**.</li>
                                    <li>Nominal setor adalah selisih **PPN Keluaran** (dari sini) dikurangi **PPN Masukan** (dari pembelian bahan).</li>
                                    <li>Batas setor: **Akhir bulan berikutnya** (M+1).</li>
                                    <li>Batas lapor: **Akhir bulan berikutnya** (M+1).</li>
                                </ul>
                            </div>
                        </div>

                        <!-- SECTION 4: REKONSILIASI -->
                        <div class="col-12">
                            <div class="bg-white p-4 rounded-4 shadow-sm border-start border-5 border-warning">
                                <h6 class="fw-900 text-dark mb-3 uppercase border-bottom pb-2"><i class="fas fa-tasks text-warning me-2"></i>CHECKLIST REKONSILIASI BULANAN</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3 border h-100">
                                            <div class="fw-900 text-dark x-small mb-1 uppercase">OMSET VS PAJAK</div>
                                            <p class="x-small text-muted mb-0 lh-base">Pastikan Total Penjualan di Laporan Keuangan Bulanan SAMA PERSIS dengan Total Gross di Laporan Pajak ini.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3 border h-100">
                                            <div class="fw-900 text-dark x-small mb-1 uppercase">ARUS KAS BANK</div>
                                            <p class="x-small text-muted mb-0 lh-base">Total uang masuk di Mutasi Rekening (Kredit) harus sinkron dengan Total Penjualan Transfer/EDC yang dilaporkan.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3 border h-100">
                                            <div class="fw-900 text-dark x-small mb-1 uppercase">ARUS KAS FISIK</div>
                                            <p class="x-small text-muted mb-0 lh-base">Simpan file **Excel Audit** setiap bulan sebagai arsip jika sewaktu-waktu diperiksa fiskus.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FOOTER WARNING -->
                        <div class="col-12">
                            <div class="bg-dark p-4 rounded-4 text-center position-relative overflow-hidden">
                                <i class="fas fa-exclamation-triangle text-white position-absolute top-50 start-0 translate-middle-y ms-4 opacity-10 fa-3x"></i>
                                <h6 class="fw-900 text-white mb-1 uppercase ls-1 text-warning">PERINGATAN RISIKO AUDIT (SP2DK)</h6>
                                <p class="x-small text-white text-opacity-75 mb-0 fw-bold px-5">
                                    Ketidakpatuhan pelaporan atau perbedaan data antara Rekening Koran dan SPT Masa adalah pemicu utama terbitnya **Surat Permintaan Penjelasan atas Data dan/atau Keterangan (SP2DK)** dari KPP Pratama. Pastikan validitas data 100%!
                                </p>
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
        const table = $('#datatable-pajak').DataTable({
            responsive: true,
            pageLength: 20,
            language: {
                url: '<?= base_url('assets/js/datatables-id.json') ?>',
                search: "",
                searchPlaceholder: "Cari nomor SPK / Nota...",
                info: "DATA _START_ - _END_ DARI _TOTAL_",
                paginate: { "previous": "<i class='fas fa-chevron-left'></i>", "next": "<i class='fas fa-chevron-right'></i>" }
            },
            dom: "<'row mb-3'<'col-sm-6'l><'col-sm-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>",
            drawCallback: function () {
                $('.dataTables_paginate > .pagination').addClass('pagination-sm');
            }
        });

        // Export CSV Logic
        $('#btn-export-pajak').on('click', function () {
            window.location.href = '<?= base_url("laporan/export_pajak?perusahaan_id=" . $current_perusahaan_id . "&bulan=" . $bulanini) ?>';
        });

        // Export Excel Logic
        $('#btn-export-pajak-excel').on('click', function () {
            window.location.href = '<?= base_url("laporan/export_pajak_spreadsheet?perusahaan_id=" . $current_perusahaan_id . "&bulan=" . $bulanini) ?>';
        });
    });
</script>