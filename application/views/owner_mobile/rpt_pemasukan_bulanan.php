<?php
// Helper: format rupiah
function rp_m($n) { return 'Rp ' . number_format((float)$n, 0, ',', '.'); }
?>
<style>



.rpt-filter-bar {
    position: relative; z-index: 20; margin: 0 0 1rem;
    background: white; border-radius: 18px;
    box-shadow: 0 6px 24px rgba(0,0,0,.08);
    padding: 1rem;
}
.rpt-filter-bar label { font-size: .7rem; font-weight: 700; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 4px; }
.rpt-filter-bar input, .rpt-filter-bar select {
    width: 100%; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: .45rem .75rem;
    font-size: .85rem; background: #f8fafc;
}
.rpt-filter-bar .filter-row { display: flex; gap: .5rem; }
.rpt-filter-bar .filter-row > div { flex: 1; }
.btn-filter {
    display: block; width: 100%; margin-top: .65rem;
    background: linear-gradient(135deg, #6366f1, #4338ca);
    color: white; border: none; padding: .55rem 1rem;
    border-radius: 10px; font-size: .85rem; font-weight: 700; cursor: pointer;
}
.stat-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: .75rem; margin-bottom: 1.25rem; }
.stat-card {
    background: white; border-radius: 16px; padding: 1rem;
    box-shadow: 0 3px 14px rgba(0,0,0,.05); border: 1px solid #f1f5f9;
}
.stat-card .s-label { font-size: .65rem; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 4px; }
.stat-card .s-val   { font-size: 1rem; font-weight: 800; color: #1e293b; }
.stat-card .s-val.green { color: #10b981; }
.stat-card .s-val.red   { color: #ef4444; }
.stat-card .s-val.blue  { color: #6366f1; }
.rpt-table-wrap {
    background: white; border-radius: 18px;
    box-shadow: 0 3px 14px rgba(0,0,0,.05);
    border: 1px solid #f1f5f9; overflow: hidden; margin-bottom: 1rem;
}
.rpt-table-head {
    background: #f8fafc; padding: .75rem 1rem;
    border-bottom: 1px solid #e2e8f0;
    font-size: .75rem; font-weight: 800; color: #475569; text-transform: uppercase;
}
.rpt-table-item {
    padding: .85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: flex-start; gap: .75rem;
}
.rpt-table-item:last-child { border-bottom: none; }
.rpt-item-dot {
    width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem; font-weight: 700; color: white;
}
.rpt-item-main { flex: 1; }
.rpt-item-title { font-size: .85rem; font-weight: 700; color: #1e293b; margin-bottom: 2px; }
.rpt-item-sub   { font-size: .72rem; color: #64748b; }
.rpt-item-right { text-align: right; }
.rpt-item-amount { font-size: .9rem; font-weight: 800; color: #1e293b; }
.rpt-item-badge {
    display: inline-block; font-size: .6rem; font-weight: 700;
    padding: 2px 7px; border-radius: 20px; text-transform: uppercase;
}
.badge-lunas   { background: #d1fae5; color: #059669; }
.badge-belum   { background: #fee2e2; color: #dc2626; }
.badge-dp      { background: #fef9c3; color: #ca8a04; }
.rpt-empty {
    text-align: center; padding: 2rem;
    color: #94a3b8; font-size: .85rem;
}
.breakdown-section {
    background: white; border-radius: 18px;
    box-shadow: 0 3px 14px rgba(0,0,0,.05);
    border: 1px solid #f1f5f9; padding: 1rem; margin-bottom: 1rem;
}
.breakdown-title { font-size: .75rem; font-weight: 800; text-transform: uppercase; color: #64748b; margin-bottom: .85rem; }
.breakdown-row { display: flex; align-items: center; gap: .65rem; margin-bottom: .65rem; }
.breakdown-label { flex: 1; font-size: .8rem; font-weight: 600; color: #1e293b; }
.breakdown-bar-wrap { flex: 2; background: #f1f5f9; border-radius: 20px; height: 8px; overflow: hidden; }
.breakdown-bar { height: 8px; border-radius: 20px; }
.breakdown-amount { font-size: .78rem; font-weight: 800; color: #1e293b; min-width: 70px; text-align: right; }
</style>

<div class="app-container">

    <!-- Filter -->
    <div class="rpt-filter-bar">
        <form method="GET" action="<?= base_url('Owner/rpt_pemasukan_bulanan/' . $token) ?>">
            <input type="hidden" name="perusahaan_id" value="<?= $perusahaan_id ?>">
            <div class="filter-row">
                <div>
                    <label>Bulan</label>
                    <input type="month" name="bulan" value="<?= $bulan_ini ?>">
                </div>
                <div>
                    <label>Cabang</label>
                    <select name="perusahaan_id">
                        <?php foreach ($all_perusahaan as $p): ?>
                            <option value="<?= $p->id_perusahaan ?>" <?= $p->id_perusahaan == $perusahaan_id ? 'selected' : '' ?>>
                                <?= $p->nama_perusahaan ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Tampilkan</button>
        </form>
    </div>

    <!-- Stat Summary -->
    <?php
    $total_masuk_all = ($stat_keuangan['cash']['total'] ?? 0) + ($stat_keuangan['transfer']['total'] ?? 0) + ($stat_keuangan['debit']['total'] ?? 0) + ($stat_keuangan['ewallet']['total'] ?? 0);
    if (!isset($stat_keuangan['total_uang_masuk'])) {
        $total_masuk_all = $summary['cash']['total'] + $summary['transfer']['total'] + $summary['debit']['total'] + $summary['ewallet']['total'];
    } else {
        $total_masuk_all = $stat_keuangan['total_uang_masuk'];
    }
    $total_peng = $stat_keuangan['total_pengeluaran'] ?? 0;
    $saldo_cash = $stat_keuangan['saldo_cash_fisik'] ?? ($summary['cash']['total']);
    $laba_kasar = $total_masuk_all - $total_peng;
    ?>
    <div class="stat-grid">
        <div class="stat-card">
            <div class="s-label">Total Omset</div>
            <div class="s-val blue"><?= rp_m($stat_keuangan['omset_penjualan'] ?? 0) ?></div>
        </div>
        <div class="stat-card">
            <div class="s-label">Uang Masuk</div>
            <div class="s-val green"><?= rp_m($total_masuk_all) ?></div>
        </div>
        <div class="stat-card">
            <div class="s-label">Pengeluaran</div>
            <div class="s-val red"><?= rp_m($total_peng) ?></div>
        </div>
        <div class="stat-card">
            <div class="s-label">Saldo Kas Tunai</div>
            <div class="s-val <?= $saldo_cash >= 0 ? 'green' : 'red' ?>"><?= rp_m($saldo_cash) ?></div>
        </div>
    </div>

    <!-- Breakdown Metode -->
    <?php
    $metode_config = [
        'cash'     => ['label' => 'Tunai', 'color' => '#10b981'],
        'transfer' => ['label' => 'Transfer', 'color' => '#6366f1'],
        'debit'    => ['label' => 'EDC / Debit', 'color' => '#f59e0b'],
        'ewallet'  => ['label' => 'E-Wallet', 'color' => '#8b5cf6'],
    ];
    $grand_masuk = array_sum(array_map(fn($k) => $summary[$k]['total'], ['cash','transfer','debit','ewallet']));
    ?>
    <?php if ($grand_masuk > 0): ?>
    <div class="breakdown-section">
        <div class="breakdown-title"><i class="fas fa-chart-bar"></i> Breakdown Metode Pembayaran</div>
        <?php foreach ($metode_config as $key => $cfg): ?>
            <?php $val = $summary[$key]['total'] ?? 0; $pct = $grand_masuk > 0 ? ($val/$grand_masuk)*100 : 0; ?>
            <div class="breakdown-row">
                <div class="breakdown-label"><?= $cfg['label'] ?></div>
                <div class="breakdown-bar-wrap">
                    <div class="breakdown-bar" style="width:<?= $pct ?>%;background:<?= $cfg['color'] ?>;"></div>
                </div>
                <div class="breakdown-amount"><?= rp_m($val) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>


    <div style="height:1.5rem;"></div>
</div>

