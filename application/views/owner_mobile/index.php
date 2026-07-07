<?php
// Helper: format angka ringkas untuk strip
function fmt_short($n)
{
    if ($n >= 1000000000)
        return 'Rp ' . number_format($n / 1000000000, 1, ',', '.') . 'M';
    if ($n >= 1000000)
        return 'Rp ' . number_format($n / 1000000, 1, ',', '.') . 'jt';
    if ($n >= 1000)
        return 'Rp ' . number_format($n / 1000, 0, ',', '.') . 'rb';
    return 'Rp ' . number_format($n, 0, ',', '.');
}
?>

<style>
    /* ── Financial Hub Mobile Styles ── */
    .fh-strip {
        display: flex;
        gap: .75rem;
        flex-wrap: nowrap;
        overflow-x: auto;
        padding: .25rem .25rem 1rem;
        position: relative;
        z-index: 20;
    }

    .fh-strip::-webkit-scrollbar {
        display: none;
    }

    .fh-strip-item {
        background: white;
        border-radius: 16px;
        padding: 1.1rem 1rem;
        flex: 1 0 auto;
        text-align: center;
        min-width: 100px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, .08);
        border: 1px solid #f1f5f9;
    }

    .fh-strip-item .val {
        font-size: 1.1rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 4px;
    }

    .fh-strip-item .lbl {
        font-size: .65rem;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .fh-cards-area {
        position: relative;
        z-index: 10;
    }

    .fh-section-label {
        font-size: .7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: #64748b;
        margin: 1.5rem 0 .85rem;
        padding-left: 10px;
        border-left: 3px solid #1e3a8a;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .fh-card {
        background: white;
        border-radius: 18px;
        padding: 1.1rem 1.1rem .9rem;
        box-shadow: 0 4px 18px rgba(0, 0, 0, .05);
        border: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 1rem;
        text-decoration: none !important;
        color: #1e293b;
        margin-bottom: .75rem;
        transition: all .25s ease;
        position: relative;
        overflow: hidden;
    }

    .fh-card:active,
    .fh-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, .09);
        border-color: #1e3a8a;
        color: #1e293b;
    }

    .fh-card-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: white;
        flex-shrink: 0;
    }

    .fh-card-body {
        flex: 1;
    }

    .fh-card-title {
        font-size: .9rem;
        font-weight: 700;
        margin: 0 0 2px;
        line-height: 1.3;
    }

    .fh-card-desc {
        font-size: .72rem;
        color: #64748b;
        margin: 0;
        line-height: 1.45;
    }

    .fh-card-arrow {
        color: #cbd5e1;
        font-size: .85rem;
        flex-shrink: 0;
    }

    .fh-badge {
        display: inline-block;
        font-size: .55rem;
        font-weight: 900;
        padding: 2px 6px;
        border-radius: 20px;
        background: linear-gradient(135deg, #1e3a8a, #172554);
        color: white;
        text-transform: uppercase;
        letter-spacing: .5px;
        vertical-align: middle;
        margin-left: 4px;
        animation: badge-glow 2s ease-in-out infinite;
    }

    @keyframes badge-glow {

        0%,
        100% {
            box-shadow: 0 0 4px rgba(30, 58, 138, .4);
        }

        50% {
            box-shadow: 0 0 10px rgba(30, 58, 138, .8);
        }
    }

    .fh-card.fh-highlight {
        background: linear-gradient(135deg, #fff7ed, #fff);
        border-color: rgba(251, 146, 60, .3);
    }

    /* Specific styles for top dashboard */
    .dash-top-card {
        background: white;
        border-radius: 18px;
        padding: 1.25rem;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
        position: relative;
        z-index: 20;
        margin-bottom: 1rem;
        border: 1px solid #f1f5f9;
    }

    .dash-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    /* ===== Tablet / iPad Layout ===== */
    @media (min-width: 768px) {

        /* Make the top cards display horizontally across the screen */
        .dash-grid-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        /* Make the bottom cards display as a grid */
        .fh-cards-area {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .fh-section-label {
            grid-column: 1 / -1;
            margin-top: 1rem;
        }

        .fh-card {
            margin-bottom: 0;
            height: 100%;
        }

        /* Center strip items */
        .fh-strip {
            justify-content: center;
        }

        .fh-strip-item {
            flex: 1;
            max-width: 250px;
        }
    }

    @media (min-width: 1024px) {
        .fh-cards-area {
            grid-template-columns: repeat(3, 1fr);
        }
    }
</style>

<div class="app-container">

    <!-- Date Picker & Main Highlight -->
    <div class="dash-top-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div
                style="background:linear-gradient(135deg,#1e3a8a,#172554); width:40px; height:40px; border-radius:12px; display:flex; align-items:center; justify-content:center; color:white; font-size:1.1rem;">
                <i class="fas fa-wallet"></i>
            </div>
            <div>
                <input type="date" class="form-control form-control-sm border-0"
                    style="background: rgba(30, 58, 138, 0.1); color: #1e3a8a; font-weight: 800; border-radius: 8px; text-align: right;"
                    value="<?= $hariini ?>"
                    onchange="window.location.href='?perusahaan_id=<?= $perusahaan_id ?>&tanggal='+this.value">
            </div>
        </div>
        <div style="font-size: .75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Omset Hari Ini
        </div>
        <div style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin-bottom: 8px;">Rp
            <?= number_format($omset_hariini, 0, ',', '.') ?>
        </div>

        <div style="font-size: .75rem; color: #10b981; font-weight: 700; display:flex; align-items:center; gap:6px;">
            <i class="fas fa-chart-line"></i> Omset Bulan Ini: Rp <?= number_format($omset_bulanini, 0, ',', '.') ?>
        </div>
    </div>

    <div class="dash-grid-2">
        <div class="dash-top-card" style="margin-bottom:0; padding:1rem;">
            <div style="font-size: .65rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Kas Masuk Hari
                Ini</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #1e293b; margin: 4px 0;">Rp
                <?= number_format($kas_masuk_hariini, 0, ',', '.') ?>
            </div>
            <div style="font-size: .65rem; font-weight:700; color: <?= $saldo_kas >= 0 ? '#10b981' : '#ef4444' ?>;">
                <i class="fas fa-<?= $saldo_kas >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i> Saldo Laci: Rp
                <?= number_format($saldo_kas, 0, ',', '.') ?>
            </div>
        </div>
        <div class="dash-top-card" style="margin-bottom:0; padding:1rem;">
            <div style="font-size: .65rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Piutang Baru
                Hari Ini</div>
            <div style="font-size: 1.1rem; font-weight: 800; color: #ef4444; margin: 4px 0;">Rp
                <?= number_format($piutang_hariini, 0, ',', '.') ?>
            </div>
            <div style="font-size: .65rem; font-weight:700; color: #ef4444;">
                Total B.Lunas: Rp <?= number_format($total_piutang_outstanding, 0, ',', '.') ?>
            </div>
        </div>
    </div>

    <!-- Strip cards overlay (Bulan Ini) -->
    <div class="fh-strip mt-3">
        <div class="fh-strip-item">
            <div class="val" style="color:#10b981;"><?= fmt_short($laba_bersih) ?></div>
            <div class="lbl">Laba Bersih</div>
        </div>
        <div class="fh-strip-item">
            <div class="val" style="color:#f43f5e;"><?= fmt_short($total_piutang_outstanding) ?></div>
            <div class="lbl">Total Piutang</div>
        </div>
        <div class="fh-strip-item">
            <div class="val" style="color:#eab308;"><?= fmt_short($total_hutang) ?></div>
            <div class="lbl">Total Hutang</div>
        </div>
    </div>

    <!-- Cards -->
    <div class="fh-cards-area">

        <!-- ===== OPERASIONAL HARIAN ===== -->
        <div class="fh-section-label">🏧 Operasional Harian</div>

        <a href="<?= base_url('Owner/rpt_pemasukan_harian/' . $token . '?perusahaan_id=' . $perusahaan_id . '&tanggal=' . $hariini) ?>"
            class="fh-card">
            <div class="fh-card-icon" style="background:linear-gradient(135deg,#3c8dbc,#2471a3);"><i
                    class="fas fa-receipt"></i></div>
            <div class="fh-card-body">
                <div class="fh-card-title">Pemasukan Harian <span class="fh-badge"
                        style="background:linear-gradient(135deg,#3c8dbc,#2471a3);">NATIVE</span></div>
                <div class="fh-card-desc">Pemasukan, pengeluaran & saldo kas hari ini</div>
            </div>
            <i class="fas fa-chevron-right fh-card-arrow"></i>
        </a>

        <a href="<?= base_url('Owner/rpt_pemasukan_bulanan/' . $token . '?perusahaan_id=' . $perusahaan_id) ?>"
            class="fh-card">
            <div class="fh-card-icon" style="background:linear-gradient(135deg,#1e3a8a,#172554);"><i
                    class="fas fa-calendar-alt"></i></div>
            <div class="fh-card-body">
                <div class="fh-card-title">Laporan Bulanan</div>
                <div class="fh-card-desc">Rekap pemasukan & pengeluaran seluruh bulan</div>
            </div>
            <i class="fas fa-chevron-right fh-card-arrow"></i>
        </a>

        <a href="<?= base_url('Owner/rpt_marketplace/' . $token . '?perusahaan_id=' . $perusahaan_id) ?>"
            class="fh-card fh-highlight">
            <div class="fh-card-icon" style="background:linear-gradient(135deg,#f97316,#ea580c);"><i
                    class="fas fa-shopping-cart"></i></div>
            <div class="fh-card-body">
                <div class="fh-card-title">Laporan Marketplace <span class="fh-badge"
                        style="background:linear-gradient(135deg,#f97316,#ea580c);">KHUSUS</span></div>
                <div class="fh-card-desc">Shopee, Tokopedia & platform digital lainnya</div>
            </div>
            <i class="fas fa-chevron-right fh-card-arrow"></i>
        </a>

        <a href="<?= base_url('Owner/rpt_rekap_metode/' . $token . '?perusahaan_id=' . $perusahaan_id) ?>"
            class="fh-card">
            <div class="fh-card-icon" style="background:linear-gradient(135deg,#00c0ef,#0099bb);"><i
                    class="fas fa-credit-card"></i></div>
            <div class="fh-card-body">
                <div class="fh-card-title">Rekap Metode Bayar</div>
                <div class="fh-card-desc">Tunai, Transfer, EDC, e-Wallet — rekonsiliasi kasir</div>
            </div>
            <i class="fas fa-chevron-right fh-card-arrow"></i>
        </a>

        <a href="<?= base_url('Owner/rpt_legger/' . $token . '?perusahaan_id=' . $perusahaan_id) ?>" class="fh-card">
            <div class="fh-card-icon" style="background:linear-gradient(135deg,#27ae60,#1e8449);"><i
                    class="fas fa-book"></i></div>
            <div class="fh-card-body">
                <div class="fh-card-title">Legger / Buku Besar</div>
                <div class="fh-card-desc">Rekap harian per metode bayar — cocok untuk audit</div>
            </div>
            <i class="fas fa-chevron-right fh-card-arrow"></i>
        </a>

        <!-- ===== ANALITIK BISNIS ===== -->
        <div class="fh-section-label">📊 Analitik Bisnis & Manajemen <span class="fh-badge">BARU</span></div>

        <a href="<?= base_url('Owner/rpt_tren_keuangan/' . $token . '?perusahaan_id=' . $perusahaan_id) ?>"
            class="fh-card">
            <div class="fh-card-icon" style="background:linear-gradient(135deg,#1e3a8a,#3b82f6);"><i
                    class="fas fa-chart-line"></i></div>
            <div class="fh-card-body">
                <div class="fh-card-title">Tren Keuangan 12 Bulan <span class="fh-badge">BARU</span></div>
                <div class="fh-card-desc">Grafik pendapatan, pengeluaran & laba bersih</div>
            </div>
            <i class="fas fa-chevron-right fh-card-arrow"></i>
        </a>

        <a href="<?= base_url('Owner/rpt_laba_rugi/' . $token . '?perusahaan_id=' . $perusahaan_id) ?>" class="fh-card">
            <div class="fh-card-icon" style="background:linear-gradient(135deg,#f39c12,#e67e22);"><i
                    class="fas fa-balance-scale"></i></div>
            <div class="fh-card-body">
                <div class="fh-card-title">Laba Rugi Bulanan</div>
                <div class="fh-card-desc">L/R formal: HPP, margin kotor, laba bersih</div>
            </div>
            <i class="fas fa-chevron-right fh-card-arrow"></i>
        </a>

        <a href="<?= base_url('Owner/rpt_hpp_margin/' . $token . '?perusahaan_id=' . $perusahaan_id) ?>"
            class="fh-card">
            <div class="fh-card-icon" style="background:linear-gradient(135deg,#1abc9c,#16a085);"><i
                    class="fas fa-tag"></i></div>
            <div class="fh-card-body">
                <div class="fh-card-title">Margin & HPP Produk <span class="fh-badge">BARU</span></div>
                <div class="fh-card-desc">Profitabilitas per produk & ranking terprofitable</div>
            </div>
            <i class="fas fa-chevron-right fh-card-arrow"></i>
        </a>

        <a href="<?= base_url('Owner/rpt_produk_terlaris/' . $token . '?perusahaan_id=' . $perusahaan_id) ?>"
            class="fh-card">
            <div class="fh-card-icon" style="background:linear-gradient(135deg,#e74c3c,#c0392b);"><i
                    class="fas fa-fire"></i></div>
            <div class="fh-card-body">
                <div class="fh-card-title">Produk Terlaris</div>
                <div class="fh-card-desc">Ranking produk berdasarkan omset & frekuensi</div>
            </div>
            <i class="fas fa-chevron-right fh-card-arrow"></i>
        </a>

        <!-- Stok & Inventaris Card (added) -->
        <a href="<?= base_url('Owner/rpt_gudang_stok/' . $token . '?perusahaan_id=' . $perusahaan_id) ?>" class="fh-card">
            <div class="fh-card-icon" style="background:linear-gradient(135deg,#f59e0b,#f97316);"><i class="fas fa-boxes"></i></div>
            <div class="fh-card-body">
                <div class="fh-card-title">Stok & Inventaris</div>
                <div class="fh-card-desc">Pantau stok material & bahan baku di seluruh gudang</div>
            </div>
            <i class="fas fa-chevron-right fh-card-arrow"></i>
        </a>

        <a href="<?= base_url('Owner/rpt_penjualan_kategori/' . $token . '?perusahaan_id=' . $perusahaan_id) ?>"
            class="fh-card">
            <div class="fh-card-icon" style="background:linear-gradient(135deg,#8e44ad,#9b59b6);"><i
                    class="fas fa-tags"></i></div>
            <div class="fh-card-body">
                <div class="fh-card-title">Penjualan Kategori <span class="fh-badge">BARU</span></div>
                <div class="fh-card-desc">Analisis penjualan per kategori produk</div>
            </div>
            <i class="fas fa-chevron-right fh-card-arrow"></i>
        </a>

        <a href="<?= base_url('Owner/rpt_kinerja_operator/' . $token . '?perusahaan_id=' . $perusahaan_id) ?>"
            class="fh-card">
            <div class="fh-card-icon" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);"><i
                    class="fas fa-users"></i></div>
            <div class="fh-card-body">
                <div class="fh-card-title">Kinerja Operator <span class="fh-badge">BARU</span></div>
                <div class="fh-card-desc">Produktivitas divisi produksi — tugas & selesai</div>
            </div>
            <i class="fas fa-chevron-right fh-card-arrow"></i>
        </a>

        <!-- ===== MANAJEMEN ASET ===== -->
        <div class="fh-section-label">💳 Aset & Kewajiban</div>

        <a href="<?= base_url('Owner/rpt_aging_piutang/' . $token . '?perusahaan_id=' . $perusahaan_id) ?>"
            class="fh-card">
            <div class="fh-card-icon" style="background:linear-gradient(135deg,#f72585,#b5179e);"><i
                    class="fas fa-exclamation-circle"></i></div>
            <div class="fh-card-body">
                <div class="fh-card-title">Aging Piutang <span class="fh-badge"
                        style="background:linear-gradient(135deg,#f72585,#b5179e);">BARU</span></div>
                <div class="fh-card-desc">Analisis umur piutang: 0–15, 16–30, 31–60, >60 hari</div>
            </div>
            <i class="fas fa-chevron-right fh-card-arrow"></i>
        </a>

        <a href="<?= base_url('Owner/rpt_piutang/' . $token . '?perusahaan_id=' . $perusahaan_id) ?>" class="fh-card">
            <div class="fh-card-icon" style="background:linear-gradient(135deg,#f43f5e,#e11d48);"><i
                    class="fas fa-user-clock"></i></div>
            <div class="fh-card-body">
                <div class="fh-card-title">Piutang Konsumen</div>
                <div class="fh-card-desc">Daftar tagihan per konsumen & reminder WA</div>
            </div>
            <i class="fas fa-chevron-right fh-card-arrow"></i>
        </a>

        <a href="<?= base_url('Owner/rpt_hutang/' . $token . '?perusahaan_id=' . $perusahaan_id) ?>" class="fh-card">
            <div class="fh-card-icon" style="background:linear-gradient(135deg,#7c3aed,#5b21b6);"><i
                    class="fas fa-truck"></i></div>
            <div class="fh-card-body">
                <div class="fh-card-title">Hutang Supplier</div>
                <div class="fh-card-desc">Kewajiban ke supplier & jatuh tempo pembayaran</div>
            </div>
            <i class="fas fa-chevron-right fh-card-arrow"></i>
        </a>

        <a href="<?= base_url('Owner/rpt_pajak/' . $token . '?perusahaan_id=' . $perusahaan_id) ?>" class="fh-card">
            <div class="fh-card-icon" style="background:linear-gradient(135deg,#059669,#047857);"><i
                    class="fas fa-percent"></i></div>
            <div class="fh-card-body">
                <div class="fh-card-title">Laporan Pajak PPN</div>
                <div class="fh-card-desc">DPP & PPN 11% — export Excel/CSV</div>
            </div>
            <i class="fas fa-chevron-right fh-card-arrow"></i>
        </a>

        <!-- ===== ARUS KAS ===== -->
        <div class="fh-section-label">🌊 Arus Kas & Mutasi Rekening</div>

        <a href="<?= base_url('Owner/rpt_arus_kas/' . $token . '?perusahaan_id=' . $perusahaan_id) ?>" class="fh-card">
            <div class="fh-card-icon" style="background:linear-gradient(135deg,#0ea5e9,#0284c7);"><i
                    class="fas fa-exchange-alt"></i></div>
            <div class="fh-card-body">
                <div class="fh-card-title">Arus Kas <span class="fh-badge">BARU</span></div>
                <div class="fh-card-desc">Masuk vs keluar — posisi likuiditas bisnis</div>
            </div>
            <i class="fas fa-chevron-right fh-card-arrow"></i>
        </a>

        <a href="<?= base_url('Owner/rpt_mutasi_bank/' . $token . '?perusahaan_id=' . $perusahaan_id) ?>"
            class="fh-card">
            <div class="fh-card-icon" style="background:linear-gradient(135deg,#10b981,#059669);"><i
                    class="fas fa-university"></i></div>
            <div class="fh-card-body">
                <div class="fh-card-title">Mutasi Bank</div>
                <div class="fh-card-desc">Transfer masuk/keluar per rekening — rekonsiliasi</div>
            </div>
            <i class="fas fa-chevron-right fh-card-arrow"></i>
        </a>

        <a href="<?= base_url('Owner/rpt_mutasi_cash/' . $token . '?perusahaan_id=' . $perusahaan_id) ?>"
            class="fh-card">
            <div class="fh-card-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706);"><i
                    class="fas fa-money-bill-wave"></i></div>
            <div class="fh-card-body">
                <div class="fh-card-title">Mutasi Kas Tunai</div>
                <div class="fh-card-desc">Penerimaan & pengeluaran tunai — saldo laci</div>
            </div>
            <i class="fas fa-chevron-right fh-card-arrow"></i>
        </a>

        <a href="<?= base_url('Owner/rpt_mutasi_ewallet/' . $token . '?perusahaan_id=' . $perusahaan_id) ?>"
            class="fh-card">
            <div class="fh-card-icon" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);"><i
                    class="fas fa-mobile-alt"></i></div>
            <div class="fh-card-body">
                <div class="fh-card-title">Mutasi E-Wallet / EDC</div>
                <div class="fh-card-desc">OVO, GoPay, QRIS, EDC — pencairan dana digital</div>
            </div>
            <i class="fas fa-chevron-right fh-card-arrow"></i>
        </a>

        <!-- ===== SUMBER DAYA MANUSIA (HR) ===== -->
        <div class="fh-section-label" style="margin-top: 1.5rem;">👥 Sumber Daya Manusia (SDM)</div>

        <a href="<?= base_url('Owner/rpt_kehadiran/' . $token . '?perusahaan_id=' . $perusahaan_id) ?>" class="fh-card">
            <div class="fh-card-icon" style="background:linear-gradient(135deg,#6366f1,#4f46e5);"><i
                    class="fas fa-user-clock"></i></div>
            <div class="fh-card-body">
                <div class="fh-card-title">Kehadiran & Absensi</div>
                <div class="fh-card-desc">Log absen harian, keterlambatan, cuti & izin</div>
            </div>
            <i class="fas fa-chevron-right fh-card-arrow"></i>
        </a>

        <a href="<?= base_url('Owner/rpt_kpi_karyawan/' . $token . '?perusahaan_id=' . $perusahaan_id) ?>"
            class="fh-card">
            <div class="fh-card-icon" style="background:linear-gradient(135deg,#ec4899,#db2777);"><i
                    class="fas fa-star"></i></div>
            <div class="fh-card-body">
                <div class="fh-card-title">Kinerja & KPI Karyawan</div>
                <div class="fh-card-desc">Evaluasi nilai, pencapaian target & produktivitas</div>
            </div>
            <i class="fas fa-chevron-right fh-card-arrow"></i>
        </a>

        <a href="<?= base_url('Owner/rpt_penggajian/' . $token . '?perusahaan_id=' . $perusahaan_id) ?>" class="fh-card"
            style="margin-bottom:0;">
            <div class="fh-card-icon" style="background:linear-gradient(135deg,#14b8a6,#0d9488);"><i
                    class="fas fa-file-invoice-dollar"></i></div>
            <div class="fh-card-body">
                <div class="fh-card-title">Penggajian (Payroll)</div>
                <div class="fh-card-desc">Rincian beban gaji, lembur, bonus, dan kasbon</div>
            </div>
            <i class="fas fa-chevron-right fh-card-arrow"></i>
        </a>

    </div><!-- /fh-cards-area -->




</div><!-- /app-container -->