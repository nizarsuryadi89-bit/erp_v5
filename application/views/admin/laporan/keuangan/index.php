<style>
    .dashboard-header {
        margin-bottom: 2.5rem;
    }
    .page-title {
        font-weight: 800;
        font-size: 1.6rem;
        letter-spacing: -0.8px;
        background: linear-gradient(135deg, #1a1a1a, #404040);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.2rem;
    }
    .finance-card-modern {
        background: rgba(255,255,255,0.95);
        border-radius: 20px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.08), 0 1.5px 4px rgba(0,0,0,0.04);
        border: 1px solid rgba(0,0,0,0.08);
        transition: all 0.3s cubic-bezier(.4,0,.2,1);
        text-align: center;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: 170px;
        padding: 2rem 1rem;
    }
    .finance-card-modern:hover {
        transform: translateY(-6px) scale(1.03);
        box-shadow: 0 8px 32px rgba(0,0,0,0.16), 0 2px 8px rgba(0,0,0,0.08);
        border-color: #1a1a1a;
    }
    .finance-card-modern .icon-wrap {
        background: linear-gradient(135deg, #1a1a1a 0%, #404040 100%);
        color: #fff;
        border-radius: 50%;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    }
    .finance-amount {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1a1a1a;
        margin-bottom: 2px;
    }
    .finance-label {
        font-size: 1.05rem;
        font-weight: 600;
        color: #6b7280;
        margin-bottom: 8px;
    }
    .btn-finance {
        background: linear-gradient(90deg, #1a1a1a 0%, #404040 100%);
        color: #fff;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        padding: 10px 0;
        transition: background 0.2s, box-shadow 0.2s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-top: auto;
        width: 100%;
    }
    .btn-finance:hover {
        background: linear-gradient(90deg, #404040 0%, #1a1a1a 100%);
        color: #fff;
        box-shadow: 0 4px 16px rgba(0,0,0,0.16);
    }
    .section-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: #1a1a1a;
        letter-spacing: 0.04em;
        margin-top: 2.2rem;
        margin-bottom: 1.2rem;
        text-transform: uppercase;
        border-left: 5px solid #1a1a1a;
        padding-left: 12px;
        background: linear-gradient(90deg, #f9fafb 60%, #f3f4f6 100%);
    }
    @media (max-width: 575.98px) {
        .finance-card-modern {
            min-height: 120px;
            padding: 1rem 0.5rem;
        }
        .finance-card-modern .icon-wrap {
            width: 36px;
            height: 36px;
            font-size: 1.3rem;
        }
        .finance-amount {
            font-size: 1.1rem;
        }
        .finance-label {
            font-size: 0.95rem;
        }
        .btn-finance {
            font-size: 0.9rem;
        }
    }
</style>
<div class="app-content">
    <div class="container-fluid pb-5">
        <div class="dashboard-header d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                <h2 class="page-title mb-1"><i class="fa fa-wallet me-2"></i>Dashboard Keuangan</h2>
                <div class="text-muted" style="font-size:0.97rem;">Rekap pemasukan, pengeluaran, dan transaksi keuangan</div>
                <div class="text-muted" style="font-size:0.85rem;">Update terakhir: <span id="last-update"><?= date('d M Y H:i') ?></span></div>
            </div>
        </div>

        <!-- ===== FINANCIAL HUB BANNER ===== -->
        <div style="background:linear-gradient(135deg,#1e293b 0%,#334155 100%);border-radius:20px;padding:1.5rem 2rem;margin-bottom:2rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;box-shadow:0 8px 25px rgba(30,41,59,.25);">
            <div style="display:flex;align-items:center;gap:1.25rem;">
                <div style="width:56px;height:56px;background:rgba(99,102,241,.25);border-radius:15px;display:flex;align-items:center;justify-content:center;font-size:1.6rem;color:#a5b4fc;border:1px solid rgba(99,102,241,.3);">
                    <i class="fa fa-line-chart"></i>
                </div>
                <div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span style="font-size:1.15rem;font-weight:900;color:white;">Pusat Laporan & Analitik Keuangan</span>
                        <span style="background:linear-gradient(135deg,#6366f1,#4338ca);color:white;font-size:.58rem;font-weight:900;padding:3px 10px;border-radius:20px;letter-spacing:1px;text-transform:uppercase;animation:glow-fin 2s ease-in-out infinite;">BARU</span>
                    </div>
                    <div style="color:#94a3b8;font-size:.85rem;margin-top:3px;">Tren 12 Bulan · Aging Piutang · Arus Kas · HPP & Margin · dan 12 laporan lainnya</div>
                </div>
            </div>
            <a href="<?= base_url('laporan/financial_hub') ?>" style="background:linear-gradient(135deg,#6366f1,#4338ca);color:white;border:none;border-radius:12px;padding:.75rem 1.75rem;font-weight:700;font-size:.88rem;text-decoration:none;display:inline-flex;align-items:center;gap:8px;white-space:nowrap;">
                <i class="fa fa-rocket"></i> Buka Hub Laporan
            </a>
        </div>
        <style>@keyframes glow-fin{0%,100%{box-shadow:0 0 6px rgba(99,102,241,.5);}50%{box-shadow:0 0 14px rgba(99,102,241,.9);}}</style>
        <!-- ===== END FINANCIAL HUB BANNER ===== -->
        <!-- PEMASUKAN HARIAN -->
        <div class="section-title">Pemasukan Harian <?= date_indo(date('Y-m-d')) ?></div>
        <div class="row g-4 mb-4">
            <?php
            $pemasukanHarian = [
                ['title' => 'Dashboard Terpadu', 'icon' => 'bi-speedometer2', 'total' => 'Lihat Detail', 'url' => 'keuangan/dashboard_terpadu'],
                ['title' => 'Total Transaksi Harian', 'icon' => 'bi-database', 'total' => $grandtotalharian['total'], 'url' => 'keuangan/transaksi_harian'],
                ['title' => 'Rekap Metode Pembayaran', 'icon' => 'bi-pie-chart', 'total' => 'Lihat Detail', 'url' => 'keuangan/rekap_metode_pembayaran'],
                ['title' => 'Total Piutang Harian', 'icon' => 'bi-credit-card', 'total' => $total_piutangharian['total'], 'url' => 'keuangan/transaksi_piutang_harian'],
                ['title' => 'Total Cash Harian', 'icon' => 'bi-cash', 'total' => $total_cashharian['total'], 'url' => 'keuangan/transaksi_cash_harian'],
                ['title' => 'Total Transfer Harian', 'icon' => 'bi-bank', 'total' => $total_tf_harian['total'], 'url' => 'keuangan/transaksi_transfer_harian'],
                ['title' => 'Total EDC/Debit Harian', 'icon' => 'bi-credit-card-2-front', 'total' => $total_edc_harian['total'], 'url' => 'keuangan/transaksi_edc_harian'],
                ['title' => 'Total E-Wallet Harian', 'icon' => 'bi-wallet2', 'total' => $total_ewallet_harian['total'], 'url' => 'keuangan/transaksi_ewallet_harian'],
            ];
            foreach ($pemasukanHarian as $item): ?>
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <div class="finance-card-modern">
                        <div class="icon-wrap mb-2"><i class="bi <?= $item['icon'] ?>"></i></div>
                        <div class="finance-amount mb-1">Rp. <?= number_format($item['total']) ?>,-</div>
                        <div class="finance-label mb-2"><?= $item['title'] ?></div>
                        <a href="<?= base_url($item['url']) ?>" class="btn btn-finance">Detail Transaksi</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- PEMASUKAN BULANAN -->
        <div class="section-title">Pemasukan Bulan <?= date_indo(date('Y-m')) ?></div>
        <div class="row g-4 mb-4">
            <?php
            $pemasukanBulanan = [
                ['title' => 'Total Transaksi Bulanan', 'icon' => 'bi-database', 'total' => $grandtotal['total'], 'url' => 'keuangan/transaksi_bulanan', 'desc' => 'Seluruh Transaksi Termasuk Piutang'],
                ['title' => 'Total Piutang Bulanan', 'icon' => 'bi-credit-card', 'total' => $total_piutang['total'], 'url' => 'keuangan/piutang', 'desc' => 'Seluruh Piutang'],
                ['title' => 'Transaksi Cash', 'icon' => 'bi-cash', 'total' => $total_cash['total'], 'url' => 'keuangan/transaksi_cash_bulanan', 'desc' => 'Transaksi + Bayar Piutang'],
                ['title' => 'Transaksi Transfer', 'icon' => 'bi-bank', 'total' => $total_transfer['total'], 'url' => 'keuangan/transaksi_transfer_bulanan', 'desc' => 'Transaksi + Bayar Piutang'],
                ['title' => 'Transaksi EDC / Debit', 'icon' => 'bi-credit-card-2-front', 'total' => $total_edc['total'], 'url' => 'keuangan/transaksi_edc_bulanan', 'desc' => 'Transaksi + Bayar Piutang'],
                ['title' => 'Transaksi E-Wallet', 'icon' => 'bi-wallet2', 'total' => $total_ewallet['total'], 'url' => 'keuangan/transaksi_ewallet_bulanan', 'desc' => 'Transaksi + Bayar Piutang'],
            ];
            foreach ($pemasukanBulanan as $item): ?>
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <div class="finance-card-modern">
                        <div class="icon-wrap mb-2"><i class="bi <?= $item['icon'] ?>"></i></div>
                        <div class="finance-amount mb-1">Rp. <?= number_format($item['total']) ?>,-</div>
                        <div class="finance-label mb-2"><?= $item['title'] ?></div>
                        <a href="<?= base_url($item['url']) ?>" class="btn btn-finance"><?= $item['desc'] ?></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- PENGELUARAN -->
        <div class="section-title">Pengeluaran</div>
        <div class="row g-4 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="finance-card-modern">
                    <div class="icon-wrap mb-2"><i class="bi bi-arrow-down-circle"></i></div>
                    <div class="finance-amount mb-1">Rp. <?= number_format($pengeluaran_harian['total']) ?>,-</div>
                    <div class="finance-label mb-2">Total Pengeluaran Harian</div>
                    <a href="<?= base_url('keuangan/pengeluaran_harian') ?>" class="btn btn-finance">Detail Pengeluaran</a>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="finance-card-modern">
                    <div class="icon-wrap mb-2"><i class="bi bi-arrow-down-circle"></i></div>
                    <div class="finance-amount mb-1">Rp. <?= number_format($pengeluaran_bulanan['total']) ?>,-</div>
                    <div class="finance-label mb-2">Total Pengeluaran Bulanan</div>
                    <a href="<?= base_url('keuangan/pengeluaran_bulanan') ?>" class="btn btn-finance">Detail Pengeluaran</a>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="finance-card-modern">
                    <div class="icon-wrap mb-2"><i class="bi bi-arrow-down-circle"></i></div>
                    <div class="finance-amount mb-1">Rp. <?= number_format($hutang['total_hutang']) ?>,-</div>
                    <div class="finance-label mb-2">Daftar Hutang</div>
                    <a href="<?= base_url('keuangan/hutang') ?>" class="btn btn-finance">Detail Hutang</a>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="finance-card-modern">
                    <div class="icon-wrap mb-2"><i class="bi bi-arrow-down-circle"></i></div>
                    <div class="finance-amount mb-1">Rp. <?= number_format($hutang['total_pembelian']) ?>,-</div>
                    <div class="finance-label mb-2">Pembelian</div>
                    <a href="<?= base_url('keuangan/hutang') ?>" class="btn btn-finance">Detail Pembelian</a>
                </div>
            </div>
        </div>
    </div> <!-- .container-fluid -->
</div> <!-- .app-content -->
