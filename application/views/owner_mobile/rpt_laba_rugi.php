<?php function rp_lr($n){return 'Rp '.number_format((float)$n,0,',','.');} ?>
<style>



.filter-bar{position:relative;z-index:20;margin: 0 0 1rem;background:white;border-radius:18px;box-shadow:0 6px 24px rgba(0,0,0,.08);padding:1rem;}
.filter-bar label{font-size:.7rem;font-weight:700;text-transform:uppercase;color:#64748b;display:block;margin-bottom:4px;}
.filter-bar input{width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:.45rem .75rem;font-size:.85rem;background:#f8fafc;}
.btn-filter{display:block;width:100%;margin-top:.65rem;background:linear-gradient(135deg,#f39c12,#e67e22);color:white;border:none;padding:.55rem;border-radius:10px;font-size:.85rem;font-weight:700;cursor:pointer;}
.lr-card{background:white;border-radius:18px;box-shadow:0 3px 14px rgba(0,0,0,.05);border:1px solid #f1f5f9;overflow:hidden;margin-bottom:1rem;}
.lr-section{padding:.85rem 1rem;}
.lr-section-title{font-size:.7rem;font-weight:800;text-transform:uppercase;color:#64748b;margin-bottom:.65rem;border-left:3px solid #f39c12;padding-left:8px;}
.lr-row{display:flex;justify-content:space-between;align-items:center;padding:.45rem 0;border-bottom:1px solid #f9fafb;}
.lr-row:last-child{border-bottom:none;}
.lr-label{font-size:.8rem;color:#475569;}
.lr-val{font-size:.85rem;font-weight:700;color:#1e293b;}
.lr-total-row{background:linear-gradient(135deg,#f39c12,#e67e22);padding:1rem;border-radius:14px;margin-bottom:.75rem;color:white;display:flex;justify-content:space-between;align-items:center;}
.lr-total-label{font-size:.8rem;font-weight:700;opacity:.9;}
.lr-total-val{font-size:1.1rem;font-weight:900;}
.laba-bersih{border-radius:18px;padding:1.25rem;text-align:center;margin-bottom:1rem;}
.laba-bersih.positif{background:linear-gradient(135deg,#d1fae5,#a7f3d0);}
.laba-bersih.negatif{background:linear-gradient(135deg,#fee2e2,#fecaca);}
.laba-bersih .lb-label{font-size:.75rem;font-weight:700;text-transform:uppercase;color:#64748b;}
.laba-bersih .lb-val{font-size:1.5rem;font-weight:900;}
</style>

<div class="app-container">

    <div class="filter-bar">
        <form method="GET" action="<?= base_url('Owner/rpt_laba_rugi/'.$token) ?>">
            <input type="hidden" name="perusahaan_id" value="<?= $perusahaan_id ?>">
            <label>Bulan</label>
            <input type="month" name="bulan" value="<?= $periode ?>">
            <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Tampilkan</button>
        </form>
    </div>

    <?php
    $pend  = $pendapatan['total_pendapatan'] ?? $pendapatan['total'] ?? ($pemasukan_bulanan['total_bayar'] ?? 0);
    $peng_tot = $pengeluaran_bulanan['total_pengeluaran'] ?? $pengeluaran_bulanan['total'] ?? 0;
    $laba_kotor = $laba_rugi['total_laba'] ?? $laba_rugi['laba'] ?? 0;
    $laba_bersih_val = (float)$pend - (float)$peng_tot;
    ?>

    <!-- Pendapatan -->
    <div class="lr-card">
        <div class="lr-section">
            <div class="lr-section-title">📈 Pendapatan</div>
            <div class="lr-row"><span class="lr-label">Total Omset / Penjualan</span><span class="lr-val" style="color:#10b981;"><?= rp_lr($pend) ?></span></div>
        </div>
    </div>

    <!-- Pengeluaran -->
    <div class="lr-card">
        <div class="lr-section">
            <div class="lr-section-title">📉 Pengeluaran</div>
            <?php if(!empty($pengeluaran_bulanan_list)): ?>
                <?php foreach($pengeluaran_bulanan_list as $pgl): ?>
                    <div class="lr-row">
                        <span class="lr-label">
                            <?= htmlspecialchars($pgl->pengeluaran_detail ?? $pgl->keterangan ?? '-') ?>
                            <div style="font-size:0.7rem; color:#64748b; margin-top:2px;">
                                Penerima: <?= htmlspecialchars($pgl->pengeluaran_penerima ?? '-') ?>
                            </div>
                        </span>
                        <span class="lr-val" style="color:#ef4444;"><?= rp_lr($pgl->pengeluaran_jumlah ?? $pgl->total ?? 0) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="lr-row"><span class="lr-label">Total Pengeluaran</span><span class="lr-val" style="color:#ef4444;"><?= rp_lr($peng_tot) ?></span></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="lr-total-row">
        <div class="lr-total-label">Total Pengeluaran</div>
        <div class="lr-total-val"><?= rp_lr($peng_tot) ?></div>
    </div>

    <!-- Laba Bersih -->
    <div class="laba-bersih <?= $laba_bersih_val >= 0 ? 'positif' : 'negatif' ?>">
        <div class="lb-label"><?= $laba_bersih_val >= 0 ? '🟢 Laba Bersih' : '🔴 Rugi Bersih' ?></div>
        <div class="lb-val" style="color:<?= $laba_bersih_val >= 0 ? '#059669' : '#dc2626' ?>;"><?= rp_lr(abs($laba_bersih_val)) ?></div>
        <?php if ($pend > 0): ?>
            <div style="font-size:.72rem;color:#64748b;margin-top:.3rem;">Margin: <?= number_format(($laba_bersih_val/$pend)*100, 1) ?>%</div>
        <?php endif; ?>
    </div>

    <div style="height:1.5rem;"></div>
</div>

