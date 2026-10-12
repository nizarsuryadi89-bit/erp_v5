<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #1a1c23 0%, #2c3e50 100%);
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, .07);
        --slate-dark: #1e293b;
        --slate-muted: #64748b;
        --accent-blue: #6366f1;
    }

    .fin-hero {
        background: var(--primary-gradient);
        color: white;
        padding: 2.5rem 0 5.5rem;
        border-radius: 0 0 30px 30px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, .15);
        position: relative;
        overflow: hidden;
    }

    .fin-hero::after {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(99, 102, 241, .15) 0%, transparent 70%);
        border-radius: 50%;
    }

    .cards-area {
        margin-top: -4rem;
        position: relative;
        z-index: 10;
        padding-bottom: 2rem;
    }

    .section-label {
        font-size: .72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: var(--slate-muted);
        margin: 2rem 0 1.25rem;
        padding-left: 10px;
        border-left: 3px solid var(--accent-blue);
    }

    .rcard {
        background: white;
        border-radius: 20px;
        padding: 1.75rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid #f1f5f9;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all .35s ease;
        margin-bottom: 1.5rem;
    }

    .rcard:hover {
        transform: translateY(-7px);
        box-shadow: 0 22px 45px rgba(0, 0, 0, .1);
        border-color: var(--accent-blue);
    }

    .rcard-icon {
        width: 52px;
        height: 52px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: white;
        margin-bottom: 1rem;
    }

    .rcard h4 {
        font-size: 1rem;
        font-weight: 800;
        color: var(--slate-dark);
        margin: 0 0 .4rem 0;
    }

    .rcard p {
        font-size: .8rem;
        color: var(--slate-muted);
        margin: 0 0 1.25rem 0;
        line-height: 1.55;
    }

    .btn-r {
        display: block;
        width: 100%;
        padding: .65rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: .78rem;
        text-align: center;
        text-decoration: none;
        transition: all .3s;
        color: white;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .btn-r:hover {
        opacity: .88;
        color: white;
        text-decoration: none;
    }

    .badge-new {
        display: inline-block;
        font-size: .58rem;
        font-weight: 900;
        letter-spacing: 1px;
        padding: 2px 8px;
        border-radius: 20px;
        background: linear-gradient(135deg, #6366f1, #4338ca);
        color: white;
        text-transform: uppercase;
        vertical-align: middle;
        margin-left: 6px;
        animation: glow-badge 2s ease-in-out infinite;
    }

    @keyframes glow-badge {

        0%,
        100% {
            box-shadow: 0 0 5px rgba(99, 102, 241, .5);
        }

        50% {
            box-shadow: 0 0 12px rgba(99, 102, 241, .9);
        }
    }

    .stat-strip {
        background: rgba(255, 255, 255, .07);
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 16px;
        padding: 1.25rem 1.75rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .stat-strip-item {
        text-align: center;
    }

    .stat-strip-item .val {
        font-size: 1.5rem;
        font-weight: 900;
        color: white;
    }

    .stat-strip-item .lbl {
        font-size: .68rem;
        color: #94a3b8;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .sep {
        width: 1px;
        height: 40px;
        background: rgba(255, 255, 255, .15);
    }
</style>

<div class="container-fluid px-4">
    <div class="fin-hero">
        <div class="container-fluid px-5">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
                <div style="display:flex;align-items:center;gap:1.5rem;">
                    <div
                        style="width:80px;height:80px;background:rgba(255,255,255,.1);border-radius:22px;display:flex;align-items:center;justify-content:center;font-size:2.2rem;border:1px solid rgba(255,255,255,.2);">
                        <i class="fa fa-line-chart"></i>
                    </div>
                    <div>
                        <h1 style="font-size:2rem;font-weight:900;margin:0 0 .3rem 0;">Pusat Laporan Keuangan</h1>
                        <p style="margin:0;color:#94a3b8;font-size:.95rem;">Semua laporan keuangan operasional &
                            analitik owner tersentralisasi</p>
                    </div>
                </div>
                <!-- Live Summary Strip -->
                <div class="stat-strip d-none d-lg-flex">
                    <div class="stat-strip-item">
                        <div class="val" id="strip-omset">—</div>
                        <div class="lbl">Omset Bulan Ini</div>
                    </div>
                    <div class="sep"></div>
                    <div class="stat-strip-item">
                        <div class="val" id="strip-piutang">—</div>
                        <div class="lbl">Total Piutang</div>
                    </div>
                    <div class="sep"></div>
                    <div class="stat-strip-item">
                        <div class="val" id="strip-hutang">—</div>
                        <div class="lbl">Total Hutang</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="cards-area">
        <div class="container-fluid px-5">

            <!-- ===== OPERASIONAL HARIAN ===== -->
            <div class="section-label">🏧 Laporan Operasional Harian</div>
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="rcard">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#3c8dbc,#2471a3);"><i
                                    class="fa fa-calendar-check-o"></i></div>
                            <h4>Laporan Harian</h4>
                            <p>Detail pemasukan, pengeluaran, dan saldo kas hari ini. Breakdown per metode pembayaran.
                            </p>
                        </div>
                        <a href="<?= base_url('laporan/pemasukan_harian') ?>" class="btn-r"
                            style="background:linear-gradient(135deg,#3c8dbc,#2471a3);">→ Buka Laporan</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="rcard">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#6366f1,#4338ca);"><i
                                    class="fa fa-calendar"></i></div>
                            <h4>Laporan Bulanan</h4>
                            <p>Rekap pemasukan dan pengeluaran seluruh bulan dengan filter cabang dan metode bayar.</p>
                        </div>
                        <a href="<?= base_url('laporan/pemasukan_bulanan') ?>" class="btn-r"
                            style="background:linear-gradient(135deg,#6366f1,#4338ca);">→ Buka Laporan</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="rcard"
                        style="border-color:rgba(251,146,60,.35);background:linear-gradient(135deg,#fff7ed,white);">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#f97316,#ea580c);"><i
                                    class="fa fa-shopping-cart"></i></div>
                            <h4>Laporan Marketplace <span class="badge-new">KHUSUS</span></h4>
                            <p>Laporan keuangan <strong>khusus transaksi marketplace</strong>. Dipisah per platform:
                                Shopee, Tokopedia, dll.</p>
                        </div>
                        <a href="<?= base_url('marketplace/laporan_marketplace') ?>" class="btn-r"
                            style="background:linear-gradient(135deg,#f97316,#ea580c);">→ Laporan Marketplace</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="rcard">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#00c0ef,#0099bb);"><i
                                    class="fa fa-credit-card"></i></div>
                            <h4>Rekap Metode Bayar</h4>
                            <p>Breakdown pembayaran: Tunai, Transfer, EDC/Debit, E-Wallet per hari. Cocok untuk
                                rekonsiliasi kasir.</p>
                        </div>
                        <a href="<?= base_url('laporan/rekap_metode_pembayaran') ?>" class="btn-r"
                            style="background:linear-gradient(135deg,#00c0ef,#0099bb);">→ Buka Laporan</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="rcard">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#27ae60,#1e8449);"><i
                                    class="fa fa-book"></i></div>
                            <h4>Legger / Buku Besar</h4>
                            <p>Buku besar bulanan: total harian per metode pembayaran. Cocok untuk audit keuangan.</p>
                        </div>
                        <a href="<?= base_url('laporan/legger') ?>" class="btn-r"
                            style="background:linear-gradient(135deg,#27ae60,#1e8449);">→ Buka Laporan</a>
                    </div>
                </div>
            </div>

            <!-- ===== ANALITIK BISNIS ===== -->
            <div class="section-label">📊 Analitik Bisnis & Manajemen<span class="badge-new">BARU</span></div>
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="rcard"
                        style="border-color:rgba(99,102,241,.2);background:linear-gradient(135deg,#fafbff,white);">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#6366f1,#818cf8);"><i
                                    class="fa fa-line-chart"></i></div>
                            <h4>Tren Keuangan 12 Bulan <span class="badge-new">BARU</span></h4>
                            <p>Grafik tren pendapatan, pengeluaran, dan laba bersih selama 12 bulan terakhir untuk
                                perencanaan bisnis.</p>
                        </div>
                        <a href="<?= base_url('laporan/tren_keuangan') ?>" class="btn-r"
                            style="background:linear-gradient(135deg,#6366f1,#4338ca);">→ Lihat Tren</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="rcard"
                        style="border-color:rgba(99,102,241,.2);background:linear-gradient(135deg,#fafbff,white);">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#f39c12,#e67e22);"><i
                                    class="fa fa-balance-scale"></i></div>
                            <h4>Laba Rugi Bulanan</h4>
                            <p>Laporan L/R formal: pendapatan, HPP, margin kotor, pengeluaran, dan laba bersih per
                                bulan.</p>
                        </div>
                        <a href="<?= base_url('laporan/laba_rugi') ?>" class="btn-r"
                            style="background:linear-gradient(135deg,#f39c12,#e67e22);">→ Buka L/R</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="rcard"
                        style="border-color:rgba(99,102,241,.2);background:linear-gradient(135deg,#fafbff,white);">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#1abc9c,#16a085);"><i
                                    class="fa fa-tag"></i></div>
                            <h4>Margin & HPP Produk <span class="badge-new">BARU</span></h4>
                            <p>Analisis profitabilitas per produk: pendapatan vs HPP, margin kotor, dan ranking produk
                                terprofitable.</p>
                        </div>
                        <a href="<?= base_url('laporan/hpp_margin') ?>" class="btn-r"
                            style="background:linear-gradient(135deg,#1abc9c,#16a085);">→ Lihat Margin</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="rcard"
                        style="border-color:rgba(99,102,241,.2);background:linear-gradient(135deg,#fafbff,white);">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#e74c3c,#c0392b);"><i
                                    class="fa fa-fire"></i></div>
                            <h4>Produk Terlaris</h4>
                            <p>Ranking produk berdasarkan omset, jumlah transaksi, dan frekuensi penjualan bulan ini.
                            </p>
                        </div>
                        <a href="<?= base_url('laporan/produk_terlaris') ?>" class="btn-r"
                            style="background:linear-gradient(135deg,#e74c3c,#c0392b);">→ Lihat Ranking</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mt-4">
                    <div class="rcard"
                        style="border-color:rgba(99,102,241,.2);background:linear-gradient(135deg,#fafbff,white);">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#8e44ad,#9b59b6);"><i
                                    class="fa fa-tags"></i></div>
                            <h4>Penjualan Kategori <span class="badge-new">BARU</span></h4>
                            <p>Analisis penjualan berdasarkan kategori produk. Lihat kategori paling laris bulan ini.
                            </p>
                        </div>
                        <a href="<?= base_url('laporan/penjualan_kategori') ?>" class="btn-r"
                            style="background:linear-gradient(135deg,#8e44ad,#9b59b6);">→ Lihat Kategori</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mt-4">
                    <div class="rcard"
                        style="border-color:rgba(99,102,241,.2);background:linear-gradient(135deg,#fafbff,white);">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);"><i
                                    class="fa fa-users"></i></div>
                            <h4>Kinerja Operator <span class="badge-new">BARU</span></h4>
                            <p>Pantau produktivitas setiap divisi produksi. Lihat tugas yang belum dikerjakan, proses,
                                dan selesai.</p>
                        </div>
                        <a href="<?= base_url('wip/kinerja_operator') ?>" class="btn-r"
                            style="background:linear-gradient(135deg,#2563eb,#1d4ed8);">→ Kinerja Operator</a>
                    </div>
                </div>
            </div>

            <!-- ===== MANAJEMEN ASET ===== -->
            <div class="section-label">💳 Manajemen Aset & Kewajiban</div>
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="rcard">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#f72585,#b5179e);"><i
                                    class="fa fa-clock-o"></i></div>
                            <h4>Aging Piutang <span class="badge-new">BARU</span></h4>
                            <p>Analisis umur piutang per konsumen: 0-15 hari, 16-30 hari, 31-60 hari, >60 hari. Risk
                                scoring otomatis.</p>
                        </div>
                        <a href="<?= base_url('laporan/aging_piutang') ?>" class="btn-r"
                            style="background:linear-gradient(135deg,#f72585,#b5179e);">→ Lihat Aging</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="rcard">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#f43f5e,#e11d48);"><i
                                    class="fa fa-user-o"></i></div>
                            <h4>Piutang Konsumen</h4>
                            <p>Daftar piutang per konsumen dengan status lunas, sebagian, dan jatuh tempo. Kirim
                                reminder WA.</p>
                        </div>
                        <a href="<?= base_url('laporan/laporan_piutang') ?>" class="btn-r"
                            style="background:linear-gradient(135deg,#f43f5e,#e11d48);">→ Buka Piutang</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="rcard">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#7c3aed,#5b21b6);"><i
                                    class="fa fa-truck"></i></div>
                            <h4>Hutang Supplier</h4>
                            <p>Daftar hutang ke supplier dari pembelian. Tracking jatuh tempo dan sisa kewajiban
                                pembayaran.</p>
                        </div>
                        <a href="<?= base_url('laporan/hutang') ?>" class="btn-r"
                            style="background:linear-gradient(135deg,#7c3aed,#5b21b6);">→ Buka Hutang</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="rcard">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#059669,#047857);"><i
                                    class="fa fa-percent"></i></div>
                            <h4>Laporan Pajak PPN</h4>
                            <p>Laporan pajak digunggung per bulan. Hitung DPP & PPN 11% per transaksi. Export ke
                                Excel/CSV.</p>
                        </div>
                        <a href="<?= base_url('laporan/laporan_pajak') ?>" class="btn-r"
                            style="background:linear-gradient(135deg,#059669,#047857);">→ Laporan Pajak</a>
                    </div>
                </div>
            </div>

            <!-- ===== ARUS KAS ===== -->
            <div class="section-label">🌊 Arus Kas & Mutasi Rekening</div>
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="rcard"
                        style="border-color:rgba(99,102,241,.2);background:linear-gradient(135deg,#fafbff,white);">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#0ea5e9,#0284c7);"><i
                                    class="fa fa-exchange"></i></div>
                            <h4>Arus Kas <span class="badge-new">BARU</span></h4>
                            <p>Laporan arus kas formal: masuk vs keluar per periode. Identifikasi posisi likuiditas
                                bisnis.</p>
                        </div>
                        <a href="<?= base_url('laporan/arus_kas') ?>" class="btn-r"
                            style="background:linear-gradient(135deg,#0ea5e9,#0284c7);">→ Lihat Arus Kas</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="rcard">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#10b981,#059669);"><i
                                    class="fa fa-bank"></i></div>
                            <h4>Mutasi Bank</h4>
                            <p>Riwayat transfer masuk dan keluar per rekening bank. Cocok untuk rekonsiliasi bank
                                bulanan.</p>
                        </div>
                        <a href="<?= base_url('laporan/mutasi_bank') ?>" class="btn-r"
                            style="background:linear-gradient(135deg,#10b981,#059669);">→ Mutasi Bank</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="rcard">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706);"><i
                                    class="fa fa-money"></i></div>
                            <h4>Mutasi Kas Tunai</h4>
                            <p>Riwayat penerimaan dan pengeluaran tunai harian. Posisi saldo kas fisik di laci.</p>
                        </div>
                        <a href="<?= base_url('laporan/mutasi_cash') ?>" class="btn-r"
                            style="background:linear-gradient(135deg,#f59e0b,#d97706);">→ Mutasi Kas</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="rcard">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);"><i
                                    class="fa fa-mobile"></i></div>
                            <h4>Mutasi E-Wallet / EDC</h4>
                            <p>Rekap penerimaan via OVO, GoPay, QRIS, dan mesin EDC. Berguna untuk pencairan dana
                                digital.</p>
                        </div>
                        <a href="<?= base_url('laporan/mutasi_ewallet') ?>" class="btn-r"
                            style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);">→ Mutasi e-Wallet</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="rcard"
                        style="border-color:rgba(79,70,229,.35);background:linear-gradient(135deg,#f8faff,white);">
                        <div>
                            <div class="rcard-icon" style="background:linear-gradient(135deg,#4f46e5,#06b6d4);"><i
                                    class="fa fa-money-check-alt"></i></div>
                            <h4>Mutasi Saldo Harian <span class="badge-new">UTAMA</span></h4>
                            <p>Konsolidasi mutasi seluruh channel: Kas Tunai, Bank Transfer, Mesin EDC, dan E-Wallet dalam satu layar.</p>
                        </div>
                        <a href="<?= base_url('laporan/mutasi_harian') ?>" class="btn-r"
                            style="background:linear-gradient(135deg,#4f46e5,#06b6d4);">→ Buka Mutasi Harian</a>
                    </div>
                </div>
            </div>

        </div><!-- /container -->
    </div><!-- /cards-area -->
</div>

<script>
    // Load live summary stats
    $(document).ready(function () {
        // Fetch monthly revenue for strip
        $.getJSON('<?= base_url('laporan/ajax_financial_summary') ?>', function (res) {
            if (res.success) {
                $('#strip-omset').text('Rp ' + numberFormat(res.omset_bulan_ini));
                $('#strip-piutang').text('Rp ' + numberFormat(res.total_piutang));
                $('#strip-hutang').text('Rp ' + numberFormat(res.total_hutang));
            }
        }).fail(function () {
            $('#strip-omset, #strip-piutang, #strip-hutang').text('—');
        });

        function numberFormat(n) {
            if (!n) return '0';
            if (n >= 1000000000) return (n / 1000000000).toFixed(1) + 'M';
            if (n >= 1000000) return (n / 1000000).toFixed(1) + 'jt';
            if (n >= 1000) return (n / 1000).toFixed(0) + 'rb';
            return n.toFixed(0);
        }
    });
</script>