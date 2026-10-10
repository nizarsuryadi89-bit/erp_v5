<?php function rp_t($n) { return 'Rp ' . number_format((float)$n, 0, ',', '.'); } ?>
<style>



.filter-bar {position:relative;z-index:20;margin: 0 0 1rem;background:white;border-radius:18px;box-shadow:0 6px 24px rgba(0,0,0,.08);padding:1rem;}
.filter-bar label {font-size:.7rem;font-weight:700;text-transform:uppercase;color:#64748b;display:block;margin-bottom:4px;}
.filter-bar select,.filter-bar input {width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:.45rem .75rem;font-size:.85rem;background:#f8fafc;}
.filter-bar .filter-row {display:flex;gap:.5rem;}
.filter-bar .filter-row > div {flex:1;}
.btn-filter {display:block;width:100%;margin-top:.65rem;background:linear-gradient(135deg,#f97316,#ea580c);color:white;border:none;padding:.55rem;border-radius:10px;font-size:.85rem;font-weight:700;cursor:pointer;}
.stat-grid {display:grid;grid-template-columns:repeat(2,1fr);gap:.75rem;margin-bottom:1.25rem;}
.stat-card {background:white;border-radius:16px;padding:1rem;box-shadow:0 3px 14px rgba(0,0,0,.05);border:1px solid #f1f5f9;}
.stat-card .s-label {font-size:.65rem;font-weight:700;text-transform:uppercase;color:#64748b;margin-bottom:4px;}
.stat-card .s-val {font-size:.95rem;font-weight:800;color:#1e293b;}
.platform-list {background:white;border-radius:18px;box-shadow:0 3px 14px rgba(0,0,0,.05);border:1px solid #f1f5f9;overflow:hidden;margin-bottom:1rem;}
.platform-item {display:flex;align-items:center;gap:.75rem;padding:.85rem 1rem;border-bottom:1px solid #f1f5f9;}
.platform-item:last-child {border-bottom:none;}
.platform-icon {width:40px;height:40px;background:linear-gradient(135deg,#f97316,#ea580c);border-radius:12px;display:flex;align-items:center;justify-content:center;color:white;font-size:1rem;}
.platform-name {font-size:.85rem;font-weight:700;color:#1e293b;}
.platform-count {font-size:.7rem;color:#64748b;}
.platform-amount {font-size:.9rem;font-weight:800;color:#1e293b;}
.trx-list {background:white;border-radius:18px;box-shadow:0 3px 14px rgba(0,0,0,.05);border:1px solid #f1f5f9;overflow:hidden;margin-bottom:1rem;}
.trx-head {background:#f8fafc;padding:.75rem 1rem;border-bottom:1px solid #e2e8f0;font-size:.75rem;font-weight:800;color:#475569;text-transform:uppercase;}
.trx-item {padding:.85rem 1rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:.75rem;}
.trx-item:last-child {border-bottom:none;}
.trx-dot {width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#f97316,#ea580c);display:flex;align-items:center;justify-content:center;color:white;font-size:.8rem;font-weight:700;flex-shrink:0;}
.trx-main {flex:1;}
.trx-name {font-size:.85rem;font-weight:700;color:#1e293b;}
.trx-sub {font-size:.7rem;color:#64748b;}
.trx-amount {font-size:.9rem;font-weight:800;color:#10b981;text-align:right;}
</style>

<div class="app-container">

    <!-- Filter -->
    <div class="filter-bar">
        <form method="GET" action="<?= base_url('Owner/rpt_marketplace/' . $token) ?>">
            <div class="filter-row">
                <div>
                    <label>Bulan</label>
                    <input type="month" name="bulan" value="<?= $bulan_ini ?>">
                </div>
                <div>
                    <label>Platform</label>
                    <select name="marketplace_id">
                        <option value="">Semua</option>
                        <?php foreach ($all_marketplace as $mk): ?>
                            <option value="<?= $mk->marketplace_id ?>" <?= $market_id_filter == $mk->marketplace_id ? 'selected' : '' ?>>
                                <?= $mk->marketplace_nama ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <input type="hidden" name="perusahaan_id" value="<?= $perusahaan_id ?>">
            <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Tampilkan</button>
        </form>
    </div>

    <!-- Stats -->
    <div class="stat-grid">
        <div class="stat-card" style="grid-column:1/-1;">
            <div class="s-label">Total Omset Marketplace</div>
            <div class="s-val" style="font-size:1.4rem;color:#f97316;"><?= rp_t($stat_keuangan['omset_penjualan']) ?></div>
        </div>
        <div class="stat-card">
            <div class="s-label">Transaksi</div>
            <div class="s-val" style="color:#6366f1;"><?= number_format($stat_keuangan['total_transaksi']) ?></div>
        </div>
        <div class="stat-card">
            <div class="s-label">Rata-rata/Trx</div>
            <div class="s-val" style="color:#10b981;">
                <?= $stat_keuangan['total_transaksi'] > 0 ? rp_t($stat_keuangan['omset_penjualan'] / $stat_keuangan['total_transaksi']) : 'Rp 0' ?>
            </div>
        </div>
    </div>

    <!-- Breakdown per Platform -->
    <?php if (!empty($by_platform)): ?>
    <div class="platform-list">
        <div class="trx-head">📦 Per Platform</div>
        <?php foreach ($by_platform as $bp): ?>
            <div class="platform-item">
                <div class="platform-icon"><i class="fas fa-store"></i></div>
                <div style="flex:1;">
                    <div class="platform-name"><?= htmlspecialchars($bp['platform']) ?></div>
                    <div class="platform-count"><?= $bp['jumlah_transaksi'] ?> transaksi</div>
                </div>
                <div class="platform-amount"><?= rp_t($bp['total_omset']) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Daftar Transaksi -->
    <div class="trx-list">
        <div class="trx-head">📋 Detail Transaksi (<?= count($pemasukan_list) ?>)</div>
        <?php if (empty($pemasukan_list)): ?>
            <div style="text-align:center;padding:2rem;color:#94a3b8;"><i class="fas fa-inbox" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>Tidak ada transaksi</div>
        <?php else: ?>
            <?php foreach ($pemasukan_list as $trx): ?>
                <div class="trx-item">
                    <div class="trx-dot"><?= substr($trx['platform'], 0, 1) ?></div>
                    <div class="trx-main">
                        <div class="trx-name"><?= htmlspecialchars($trx['konsumen'] ?? '-') ?></div>
                        <div class="trx-sub"><?= htmlspecialchars($trx['nospk']) ?> · <?= $trx['platform'] ?>
                            <?php if ($trx['resi']): ?><br><span style="color:#0ea5e9;">Resi: <?= htmlspecialchars($trx['resi']) ?></span><?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <div class="trx-amount"><?= rp_t($trx['total_tagihan']) ?></div>
                        <div style="font-size:.62rem;color:#64748b;text-align:right;"><?= date('d M', strtotime($trx['tanggal'])) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div style="height:1.5rem;"></div>
</div>

