<?php
/**
 * Rekap Pengeluaran - Premium UI/UX Edition
 * Displays expenses broken down by category and transaction detail with consistent styling.
 */

// Calculate Summary Data
$kategori_totals = [];
$grandtotal = 0;
foreach ($kategori as $k) {
    $kategori_totals[$k->id] = 0;
}
foreach ($pengeluaran as $p) {
    $cat_id = $p->kategori_pengeluaran_id;
    if (isset($kategori_totals[$cat_id])) {
        $kategori_totals[$cat_id] += $p->pengeluaran_jumlah;
    }
    $grandtotal += $p->pengeluaran_jumlah;
}

// Count categories that have expenses > 0
$kategori_aktif = 0;
foreach ($kategori_totals as $total) {
    if ($total > 0)
        $kategori_aktif++;
}
?>

<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">

<style>
    /* ==========================================================================
    TGM PREMIUM EXPENSE TRACKER (PENGELUARAN)
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

    /* Content Cards */
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

    /* Badges & Progress */
    .method-badge-premium {
        padding: 0.35rem 0.6rem;
        border-radius: 6px;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        letter-spacing: 0.5px;
    }

    .badge-tunai-soft { background: #dcfce7; color: #166534; }
    .badge-transfer-soft { background: #dbeafe; color: #1e40af; }
    .badge-debit-soft { background: #f3e8ff; color: #6b21a8; }

    .progress-premium {
        height: 6px;
        border-radius: 10px;
        background: #f1f5f9;
        overflow: hidden;
    }

    .progress-fill-premium {
        background: var(--fin-accent);
        border-radius: 10px;
    }

    /* Detail Hover Area */
    .detail-container {
        padding: 0.5rem;
        border-radius: 8px;
        background: #f8fafc;
        border-left: 3px solid var(--fin-accent);
        font-size: 0.8rem;
        color: #475569;
        line-height: 1.4;
    }

    @media print {
        .no-print { display: none !important; }
        .app-content { background: white; padding: 0 !important; }
        .summary-card { box-shadow: none; border: 1px solid #eee; }
    }
</style>

<div class="app-content mt-3 pb-5">
    <?php $this->load->view('layout_admin/header_modul'); ?>

    <div class="container-fluid py-4">
        <!-- TOP FILTER HUD -->
        <div class="filter-hud no-print">
            <form action="<?= base_url('laporan/pengeluaran') ?>" method="GET" class="row align-items-center g-3">
                <div class="col-xl-4 col-lg-12">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 bg-light rounded-3 text-danger border shadow-sm">
                            <i class="fas fa-wallet fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-900 text-dark ls-n1">REKAP PENGELUARAN</h6>
                            <p class="text-muted small mb-0 fw-bold">Audit Penggunaan Dana Operasional</p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-5">
                    <select class="form-select" name="perusahaan_id" onchange="this.form.submit()">
                        <?php foreach ($all_perusahaan as $p): ?>
                                <option value="<?= $p->id_perusahaan ?>" <?= ($current_perusahaan_id == $p->id_perusahaan) ? 'selected' : '' ?>>
                                    🏢 <?= $p->nama_perusahaan ?>
                                </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-xl-2 col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="far fa-calendar-alt text-muted"></i></span>
                        <input type="month" name="bulan" class="form-control" value="<?= $hariini ?>" onchange="this.form.submit()">
                    </div>
                </div>

                <div class="col-xl-3 col-lg-3 text-end">
                    <div class="d-flex gap-2">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#modalSopPengeluaran" class="btn btn-outline-primary border-2 fw-900 rounded-3 px-3 shadow-sm">
                            <i class="fas fa-book-open"></i>
                        </button>
                        <button type="button" data-bs-toggle="modal" data-bs-target="#modalAddPengeluaran" class="btn btn-dark w-100 rounded-3 py-2 fw-900 shadow-sm">
                            <i class="fas fa-plus me-2"></i> INPUT
                        </button>
                        <button type="button" class="btn btn-success rounded-3 py-2 px-3 fw-900 shadow-sm" onclick="location.reload()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- KPI GRID -->
        <div class="row g-4 mb-5">
            <div class="col-xl-4 col-md-6">
                <div class="cash-stat-card border-bottom border-danger border-4">
                    <div class="cash-stat-icon bg-danger bg-opacity-10 text-danger"><i class="fas fa-money-check-alt"></i></div>
                    <div class="cash-stat-label">Total Dana Keluar (C)</div>
                    <div class="cash-stat-value text-danger">Rp <?= number_format($grandtotal, 0, ',', '.') ?></div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6">
                <div class="cash-stat-card border-bottom border-info border-4">
                    <div class="cash-stat-icon bg-info bg-opacity-10 text-info"><i class="fas fa-tags"></i></div>
                    <div class="cash-stat-label">Kategori Aktif</div>
                    <div class="cash-stat-value"><?= $kategori_aktif ?> <small class="fs-6">DISTRIBUSI</small></div>
                </div>
            </div>
            <div class="col-xl-4 col-md-12">
                <div class="cash-stat-card border-bottom border-success border-4">
                    <div class="cash-stat-icon bg-success bg-opacity-10 text-success"><i class="fas fa-receipt"></i></div>
                    <div class="cash-stat-label">Frekuensi Transaksi</div>
                    <div class="cash-stat-value"><?= count($pengeluaran) ?> <small class="fs-6">NOTA</small></div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- BREAKDOWN SUMMARY (LEFT) -->
            <div class="col-xl-5">
                <div class="summary-card shadow-sm h-100">
                    <div class="summary-header">
                        <div>
                            <h6 class="fw-900 mb-0 ls-n1">DISTRIBUSI BIAYA</h6>
                            <small class="opacity-75 fw-bold text-uppercase ls-1">Analisa Pareto Kategori</small>
                        </div>
                        <i class="fas fa-chart-pie fs-4 opacity-50"></i>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-tx mb-0">
                            <thead>
                                <tr>
                                    <th>KATEGORI BIAYA</th>
                                    <th class="text-end">NOMINAL</th>
                                    <th class="text-center" width="120">PORSI (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($kategori as $value):
                                    $total = $kategori_totals[$value->id];
                                    if ($total > 0):
                                        $percent = $grandtotal > 0 ? round(($total / $grandtotal) * 100, 1) : 0;
                                        ?>
                                                <tr>
                                                    <td>
                                                        <div class="fw-900 text-dark"><?= $value->kategori_pengeluaran ?></div>
                                                        <small class="text-muted fw-bold"><?= $value->keterangan_pengeluaran ?: 'Operasional' ?></small>
                                                    </td>
                                                    <td class="text-end fw-bold monospaced">Rp <?= number_format($total, 0, ',', '.') ?></td>
                                                    <td class="text-center">
                                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                                            <div class="progress-premium flex-grow-1">
                                                                <div class="progress-fill-premium" style="width: <?= $percent ?>%; height: 100%;"></div>
                                                            </div>
                                                            <small class="fw-900 text-muted monospaced"><?= $percent ?>%</small>
                                                        </div>
                                                    </td>
                                                </tr>
                                        <?php endif; endforeach; ?>
                                <?php if ($grandtotal == 0): ?>
                                        <tr>
                                            <td colspan="3" class="text-center py-5 text-muted fw-bold italic">Belum ada data pengeluaran.</td>
                                        </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr style="background: #fff5f5;">
                                    <td class="py-4 ps-4 fw-900 text-danger fs-6">TOTAL PENGELUARAN</td>
                                    <td colspan="2" class="py-4 text-end pe-4 fw-900 text-danger fs-4 monospaced">Rp <?= number_format($grandtotal, 0, ',', '.') ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TRANSACTION LIST (RIGHT) -->
            <div class="col-xl-7">
                <div class="summary-card shadow-sm h-100 overflow-hidden bg-white">
                    <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-white">
                        <h6 class="fw-900 text-dark mb-0 ls-n1"><i class="fas fa-history me-2 text-primary"></i>AUDIT PENGELUARAN FISIK</h6>
                        <div class="d-flex gap-2 no-print">
                            <input type="text" id="tableSearch" class="form-control form-control-sm rounded-pill px-3 border-light bg-light" style="width: 200px" placeholder="Cari transaksi...">
                            <button onclick="window.print()" class="btn btn-sm btn-light rounded-circle"><i class="fas fa-print"></i></button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-tx table-hover mb-0" id="tablePengeluaran">
                            <thead>
                                <tr>
                                    <th class="text-center" width="70">TGL</th>
                                    <th>PENERIMA & METODE</th>
                                    <th class="text-end">NOMINAL</th>
                                    <th class="text-center no-print" width="50">#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pengeluaran as $dt): ?>
                                        <tr>
                                            <td class="text-center">
                                                <div class="fw-900 text-dark fs-5 monospaced"><?= date('d', strtotime($dt->pengeluaran_tgl)) ?></div>
                                                <small class="text-muted fw-bold text-uppercase"><?= date('M', strtotime($dt->pengeluaran_tgl)) ?></small>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <span class="fw-900 text-dark"><?= $dt->pengeluaran_penerima ?></span>
                                                    <?php
                                                    $metode = $dt->metode_bayar ?? 'tunai';
                                                    $m_class = "badge-$metode-soft";
                                                    $m_icon = ($metode == 'tunai') ? 'fa-money-bill' : (($metode == 'transfer') ? 'fa-university' : 'fa-credit-card');
                                                    ?>
                                                    <span class="method-badge-premium <?= $m_class ?>">
                                                        <i class="fa <?= $m_icon ?>"></i>
                                                        <?= str_replace('_', ' ', $metode) ?>
                                                    </span>
                                                </div>
                                                <div class="detail-container">
                                                    <div class="fw-bold mb-1"><i class="fa fa-tag me-2 text-info small"></i><?= $dt->kategori_pengeluaran ?></div>
                                                    <div class="small fst-italic"><?= $dt->pengeluaran_detail ?></div>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <div class="fw-900 text-danger fs-5 monospaced">Rp <?= number_format($dt->pengeluaran_jumlah, 0, ',', '.') ?></div>
                                                <small class="text-muted fw-bold"><?= date('H:i', strtotime($dt->created_at ?? $dt->pengeluaran_tgl)) ?> WIB</small>
                                            </td>
                                            <td class="text-center no-print">
                                                <button type="button" class="btn btn-sm btn-light text-danger rounded-circle p-2" onclick="hapusPengeluaran(<?= $dt->id ?>)">
                                                    <i class="fa fa-trash"></i>
                                                </button>
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

<!-- MODAL SOP PENGELUARAN (PREMIUM) -->
<div class="modal fade" id="modalSopPengeluaran" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-circle p-2 me-3">
                        <i class="fas fa-book-open text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-900 ls-1 mb-0 text-uppercase">Buku Saku Audit Pengeluaran</h5>
                        <small class="text-white-50 fw-bold">Pedoman Pengendalian Biaya Operasional</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-light">
                <!-- Header Highlight -->
                <div class="bg-primary bg-opacity-10 p-4 border-bottom border-primary border-opacity-10">
                    <div class="row align-items-center">
                        <div class="col-md-9">
                            <h6 class="fw-900 text-primary mb-1">PROTOKOL CASH-OUT & AUDIT FISIK</h6>
                            <p class="small text-muted mb-0 fw-bold">Setiap pengeluaran dana adalah pengurang laba bersih. Tertibkan pencatatan!</p>
                        </div>
                        <div class="col-md-3 text-md-end mt-3 mt-md-0">
                            <span class="badge bg-primary px-3 py-2 rounded-pill fw-900 shadow-sm">Standard Ops</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 p-md-5">
                    <div class="row g-4">
                        <!-- TAHAP 1 -->
                        <div class="col-md-6">
                            <div class="bg-white p-4 rounded-4 shadow-sm border-start border-4 border-danger h-100">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-danger text-white rounded-3 p-2 me-3"><i class="fas fa-stopwatch"></i></div>
                                    <h6 class="fw-900 text-dark mb-0 uppercase">1. INPUT REAL-TIME & SHIFT</h6>
                                </div>
                                <p class="small text-muted mb-2">Pencatatan **WAJIB** dilakukan saat uang fisik keluar dari laci/rekening.</p>
                                <ul class="x-small text-muted fw-bold ps-3 mb-0">
                                    <li class="mb-1">Admin Kasir wajib membuka **Shift** terlebih dahulu sebelum input.</li>
                                    <li>Pengeluaran tanpa input akan menyebabkan selisih minus saat tutup shift.</li>
                                </ul>
                            </div>
                        </div>

                        <!-- TAHAP 2 -->
                        <div class="col-md-6">
                            <div class="bg-white p-4 rounded-4 shadow-sm border-start border-4 border-info h-100">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-info text-white rounded-3 p-2 me-3"><i class="fas fa-chart-pie"></i></div>
                                    <h6 class="fw-900 text-dark mb-0 uppercase">2. STRATEGI KATEGORISASI</h6>
                                </div>
                                <p class="small text-muted mb-2">Pilihlah kategori yang paling relevan agar grafik **Pareto** akurat.</p>
                                <ul class="x-small text-muted fw-bold ps-3 mb-0">
                                    <li class="mb-1">Gunakan kategori "Bahan" hanya untuk pembelian raw material produksi.</li>
                                    <li>Kategori "Lain-lain" adalah opsi terakhir, usahakan gunakan kategori spesifik.</li>
                                </ul>
                            </div>
                        </div>

                        <!-- TAHAP 3 -->
                        <div class="col-12">
                            <div class="bg-white p-4 rounded-4 shadow-sm border-start border-4 border-success">
                                <h6 class="fw-900 text-dark mb-4 border-bottom pb-3 uppercase"><i class="fas fa-money-check text-success me-2"></i>VERIFIKASI MULTI-METODE PEMBAYARAN</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3 border h-100">
                                            <div class="fw-900 text-dark small mb-2 uppercase">METODE TUNAI</div>
                                            <p class="x-small text-muted mb-0 lh-base">Mengurangi saldo kas fisik di laci kasir. Pastikan ada **Bon/Nota Fisik** yang ditandatangani penerima.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3 border h-100">
                                            <div class="fw-900 text-dark small mb-2 uppercase">METODE TRANSFER</div>
                                            <p class="x-small text-muted mb-0 lh-base">Input **Bank Pengirim** dan **Detail Penerima**. Data ini akan dicocokkan dengan Mutasi Rekening Kantor.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3 border h-100">
                                            <div class="fw-900 text-dark small mb-2 uppercase">METODE DEBIT/EDC</div>
                                            <p class="x-small text-muted mb-0 lh-base">Hanya untuk pengeluaran yang menggunakan kartu kantor pada mesin EDC pihak ketiga/lain.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAHAP 4 -->
                        <div class="col-md-12">
                            <div class="bg-warning bg-opacity-10 p-4 rounded-4 border border-warning border-opacity-25 shadow-sm">
                                <h6 class="fw-900 text-dark mb-2 uppercase"><i class="fas fa-exclamation-triangle text-warning me-2"></i>PROSEDUR KOREKSI & PEMBATALAN</h6>
                                <p class="small text-muted mb-0">Jika terjadi kesalahan input nominal/kategori, gunakan tombol **Hapus (Ikon Tong Sampah)**. Sistem akan secara otomatis mengembalikan (void) saldo yang terpotong ke posisi semula di laporan harian. Hindari menghapus data yang sudah lewat tanggal/bulan tanpa persetujuan Manajer.</p>
                            </div>
                        </div>

                        <!-- FOOTER NOTE -->
                        <div class="col-12">
                            <div class="bg-dark p-4 rounded-4 shadow">
                                <div class="row align-items-center">
                                    <div class="col-md-10">
                                        <h6 class="fw-900 text-white mb-1 uppercase ls-1">PRINSIP AKUNTABILITAS</h6>
                                        <p class="x-small text-white text-opacity-75 mb-0 lh-base">Laporan Rekap Pengeluaran ini adalah instrumen utama dalam menghitung **Nett Profit** pada Laporan Laba Rugi. Ketidakjujuran atau kelalaian dalam input akan merusak akurasi profitabilitas perusahaan.</p>
                                    </div>
                                    <div class="col-md-2 text-md-end mt-3 mt-md-0">
                                        <i class="fas fa-shield-alt text-primary fs-2"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white border-top p-3">
                <button type="button" class="btn btn-primary rounded-pill px-5 fw-900 shadow-sm" data-bs-dismiss="modal">SAYA MENGERTI & PATUH</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Pengeluaran (Premium Style) -->
<div class="modal fade" id="modalAddPengeluaran" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?= base_url('laporan/add_pengeluaran') ?>" method="post" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-circle p-2 me-3">
                        <i class="fas fa-plus text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-900 ls-1 mb-0 text-uppercase">Input Pengeluaran Baru</h5>
                        <small class="text-white-50 fw-bold">Potong Saldo Kas/Bank Perusahaan</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <input type="hidden" name="perusahaan_id" value="<?= $current_perusahaan_id ?>">
                
                <?php if ($user_level == 3 && $current_shift): ?>
                        <div class="alert alert-success d-flex align-items-center gap-3 mb-4 rounded-3 border-0 shadow-sm">
                            <i class="fas fa-toggle-on fs-4"></i>
                            <div class="small fw-bold">Shift Kasir Aktif: <span class="text-primary">#<?= $current_shift->shift_id ?></span> (Data akan otomatis tersambung ke Laporan Harian)</div>
                        </div>
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-900 text-dark small text-uppercase">Kategori Pengeluaran <span class="badge bg-danger rounded-pill fw-bold ms-1" style="font-size: 0.6rem;"><i class="fas fa-star text-warning me-1"></i>FITUR BARU</span></label>
                        <select class="form-select form-control-premium" name="kategori" id="kategoriSelect" required>
                            <option value="" disabled selected>Pilih Kategori...</option>
                            <?php foreach ($kategori as $cat): ?>
                                    <option value="<?= $cat->id ?>"><?= $cat->kategori_pengeluaran ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12" id="karyawanField" style="display: none;">
                        <label class="form-label fw-900 text-dark small text-uppercase text-primary d-flex align-items-center gap-2">
                            <span><i class="fas fa-user-tag"></i> Pilih Karyawan (Kasbon)</span>
                            <span class="badge bg-danger rounded-pill fw-bold" style="font-size: 0.6rem;">TERINTEGRASI PAYROLL ✨</span>
                        </label>
                        <select class="form-select form-control-premium border-primary" name="karyawan_id" id="karyawan_id">
                            <option value="" disabled selected>Pilih Karyawan...</option>
                            <?php foreach ($karyawan_list ?? [] as $kar): ?>
                                    <option value="<?= $kar->karyawan_id ?>"><?= $kar->nama_lengkap ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-7">
                        <label class="form-label fw-900 text-dark small text-uppercase">Jumlah (Rp)</label>
                        <input type="number" name="jumlah" class="form-control form-control-premium fw-900 text-danger fs-4 monospaced" placeholder="0" required>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label fw-900 text-dark small text-uppercase">Penerima</label>
                        <input type="text" name="penerima" class="form-control form-control-premium" placeholder="Nama..." required>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-900 text-dark small text-uppercase">Keperluan / Detail</label>
                        <textarea name="keterangan" class="form-control form-control-premium" placeholder="Tuliskan tujuan pengeluaran dana..." rows="2" required></textarea>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-white rounded-4 border">
                    <label class="form-label fw-900 text-dark small text-uppercase d-block mb-3 text-center">Metode Pembayaran</label>
                    <div class="d-flex gap-2">
                        <div class="flex-grow-1">
                            <input type="radio" class="btn-check" name="metode_bayar" id="pay_tunai" value="tunai" checked>
                            <label class="btn btn-outline-success w-100 fw-900 border-2 rounded-3" for="pay_tunai">TUNAI</label>
                        </div>
                        <div class="flex-grow-1">
                            <input type="radio" class="btn-check" name="metode_bayar" id="pay_transfer" value="transfer">
                            <label class="btn btn-outline-primary w-100 fw-900 border-2 rounded-3" for="pay_transfer">TRANSFER</label>
                        </div>
                        <div class="flex-grow-1">
                            <input type="radio" class="btn-check" name="metode_bayar" id="pay_debit" value="debit">
                            <label class="btn btn-outline-info w-100 fw-900 border-2 rounded-3" for="pay_debit">DEBIT</label>
                        </div>
                    </div>
                </div>

                <!-- Fields for Transfer -->
                <div id="transferFields" class="mt-3 p-3 bg-primary bg-opacity-10 rounded-3 border border-primary border-opacity-10" style="display: none;">
                    <div class="mb-3">
                        <label class="form-label fw-900 text-primary small text-uppercase">Bank Kantor (Asal)</label>
                        <select name="bank_pengirim_id" class="form-select border-primary border-opacity-25">
                            <option value="">-- Pilih Bank Kantor --</option>
                            <?php foreach ($bank_list ?? [] as $bank): ?>
                                    <option value="<?= $bank->id ?>"><?= $bank->nama_bank ?> - <?= $bank->no_rekening ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label fw-900 text-primary small text-uppercase">Rekening Tujuan</label>
                        <input type="text" name="rekening_tujuan" class="form-control border-primary border-opacity-25" placeholder="Bank - No Rek / Nama">
                    </div>
                </div>

                <!-- Fields for EDC -->
                <div id="cardFields" class="mt-3 p-3 bg-info bg-opacity-10 rounded-3 border border-info border-opacity-10" style="display: none;">
                    <label class="form-label fw-900 text-info small text-uppercase">Mesin EDC Kantor</label>
                    <select name="edc_id" class="form-select border-info border-opacity-25">
                        <option value="">Pilih Mesin EDC</option>
                        <?php foreach ($edc_list ?? [] as $edc): ?>
                                <option value="<?= $edc->edc_id ?>"><?= $edc->edc_nama ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-light p-4 border-top">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-800" data-bs-dismiss="modal">BATAL</button>
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-900 shadow">
                    SIMPAN PENGELUARAN
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        const table = $('#tablePengeluaran').DataTable({
            pageLength: 20,
            language: { 
                url: "<?= base_url('assets/js/datatables-id.json') ?>",
                info: "DATA _START_ - _END_ DARI _TOTAL_",
                paginate: { "previous": "<i class='fas fa-chevron-left'></i>", "next": "<i class='fas fa-chevron-right'></i>" }
            },
            dom: "<'row'<'col-sm-12'tr>>" +
                 "<'row p-3'<'col-sm-5 small fw-bold text-muted'i><'col-sm-7'p>>",
            order: [[0, 'desc']]
        });

        $('#tableSearch').on('keyup', function () { table.search(this.value).draw(); });

        $('input[name="metode_bayar"]').on('change', function () {
            const val = $(this).val();
            $('#transferFields, #cardFields').hide();
            if (val === 'transfer') $('#transferFields').slideDown();
            if (val === 'debit') $('#cardFields').slideDown();
        });

        $('#kategoriSelect').on('change', function () {
            const val = $(this).val();
            if (val === '3') { // ID 3: Cashbon Karyawan
                $('#karyawanField').slideDown();
                $('#karyawan_id').attr('required', true);
            } else {
                $('#karyawanField').slideUp();
                $('#karyawan_id').removeAttr('required').val('');
            }
        });
    });

    function hapusPengeluaran(id) {
        Swal.fire({
            title: 'Hapus Catatan?',
            text: "Data pengeluaran akan ditarik kembali dari sistem harian.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            borderRadius: '15px'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('laporan/hapus_pengeluaran/') ?>" + id;
            }
        });
    }
</script>