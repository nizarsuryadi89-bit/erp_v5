<?php function rp_v($n){return 'Rp '.number_format((float)$n,0,',','.');} ?>
<style>



.filter-bar{position:relative;z-index:20;margin: 0 0 1rem;background:white;border-radius:18px;box-shadow:0 6px 24px rgba(0,0,0,.08);padding:1rem;}
.filter-bar label{font-size:.7rem;font-weight:700;text-transform:uppercase;color:#64748b;display:block;margin-bottom:4px;}
.filter-bar input{width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:.45rem .75rem;font-size:.85rem;background:#f8fafc;}
.filter-bar .filter-row{display:flex;gap:.5rem;}
.filter-bar .filter-row>div{flex:1;}
.btn-filter{display:block;width:100%;margin-top:.65rem;background:linear-gradient(135deg,#27ae60,#1e8449);color:white;border:none;padding:.55rem;border-radius:10px;font-size:.85rem;font-weight:700;cursor:pointer;}
.summary-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:.5rem;margin-bottom:1rem;}
.sum-card{background:white;border-radius:14px;padding:.75rem;box-shadow:0 3px 12px rgba(0,0,0,.05);border:1px solid #f1f5f9;text-align:center;}
.sum-card .s-lbl{font-size:.6rem;font-weight:700;text-transform:uppercase;color:#64748b;margin-bottom:3px;}
.sum-card .s-val{font-size:.85rem;font-weight:800;color:#1e293b;}
.legger-list{background:white;border-radius:18px;box-shadow:0 3px 14px rgba(0,0,0,.05);border:1px solid #f1f5f9;overflow:hidden;margin-bottom:1rem;}
.legger-head{background:#f8fafc;padding:.75rem 1rem;border-bottom:1px solid #e2e8f0;font-size:.7rem;font-weight:800;color:#475569;text-transform:uppercase;}
.legger-row{padding:.9rem 1rem;border-bottom:1px solid #f1f5f9;display:grid;grid-template-columns:auto 1fr;gap:.65rem;align-items:start;}
.legger-row:last-child{border-bottom:none;}
.legger-date{width:44px;height:44px;background:linear-gradient(135deg,#27ae60,#1e8449);border-radius:12px;display:flex;flex-direction:column;align-items:center;justify-content:center;color:white;flex-shrink:0;}
.legger-date .d-day{font-size:.95rem;font-weight:900;line-height:1;}
.legger-date .d-mon{font-size:.55rem;font-weight:700;text-transform:uppercase;opacity:.85;}
.legger-data{}
.legger-total{font-size:.95rem;font-weight:800;color:#1e293b;}
.legger-methods{display:flex;flex-wrap:wrap;gap:.35rem;margin-top:.4rem;}
.method-chip{font-size:.62rem;font-weight:700;padding:2px 7px;border-radius:20px;background:#f1f5f9;color:#475569;}
.method-chip.cash{background:#d1fae5;color:#059669;}
.method-chip.tf{background:#e0e7ff;color:#4338ca;}
.method-chip.edc{background:#fef9c3;color:#ca8a04;}
.method-chip.ew{background:#ede9fe;color:#7c3aed;}
.method-chip.pt{background:#fee2e2;color:#dc2626;}
.grand-footer{background:linear-gradient(135deg,#27ae60,#1e8449);border-radius:16px;padding:1rem;color:white;text-align:center;margin-bottom:1rem;}
.grand-footer .g-label{font-size:.72rem;opacity:.8;text-transform:uppercase;font-weight:700;}
.grand-footer .g-val{font-size:1.35rem;font-weight:900;}
</style>

<div class="app-container">

    <div class="filter-bar">
        <form method="GET" action="<?= base_url('Owner/rpt_legger/'.$token) ?>">
            <input type="hidden" name="perusahaan_id" value="<?= $perusahaan_id ?>">
            <div class="filter-row">
                <div><label>Dari</label><input type="date" name="mulai" value="<?= $mulai ?>"></div>
                <div><label>Sampai</label><input type="date" name="selesai" value="<?= $selesai ?>"></div>
            </div>
            <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Tampilkan</button>
        </form>
    </div>

    <div class="summary-grid">
        <div class="sum-card"><div class="s-lbl">Tunai</div><div class="s-val" style="color:#10b981;font-size:.75rem;"><?= rp_v($cash) ?></div></div>
        <div class="sum-card"><div class="s-lbl">Transfer</div><div class="s-val" style="color:#6366f1;font-size:.75rem;"><?= rp_v($transfer) ?></div></div>
        <div class="sum-card"><div class="s-lbl">EDC</div><div class="s-val" style="color:#f59e0b;font-size:.75rem;"><?= rp_v($edc) ?></div></div>
        <div class="sum-card"><div class="s-lbl">e-Wallet</div><div class="s-val" style="color:#8b5cf6;font-size:.75rem;"><?= rp_v($ewallet) ?></div></div>
        <div class="sum-card"><div class="s-lbl">Piutang</div><div class="s-val" style="color:#ef4444;font-size:.75rem;"><?= rp_v($piutang) ?></div></div>
        <div class="sum-card"><div class="s-lbl">Grand Total</div><div class="s-val" style="color:#27ae60;font-size:.75rem;"><?= rp_v($grandtotal) ?></div></div>
    </div>

    <div class="legger-list">
        <div class="legger-head">📋 Detail per Hari (<?= count($legger) ?> hari)</div>
        <?php if(empty($legger)): ?>
            <div style="text-align:center;padding:2rem;color:#94a3b8;"><i class="fas fa-inbox" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>Tidak ada data</div>
        <?php else: ?>
            <?php foreach($legger as $row): ?>
            <div class="legger-row">
                <div class="legger-date">
                    <div class="d-day"><?= date('d', strtotime($row->tanggal ?? $row->tgl_bayar ?? date('Y-m-d'))) ?></div>
                    <div class="d-mon"><?= date('M', strtotime($row->tanggal ?? $row->tgl_bayar ?? date('Y-m-d'))) ?></div>
                </div>
                <div class="legger-data">
                    <div class="legger-total"><?= rp_v($row->total_harian) ?></div>
                    <div class="legger-methods">
                        <?php if ($row->total_cash > 0): ?><span class="method-chip cash">Tunai: <?= rp_v($row->total_cash) ?></span><?php endif; ?>
                        <?php if ($row->total_transfer > 0): ?><span class="method-chip tf">Transfer: <?= rp_v($row->total_transfer) ?></span><?php endif; ?>
                        <?php if ($row->total_edc > 0): ?><span class="method-chip edc">EDC: <?= rp_v($row->total_edc) ?></span><?php endif; ?>
                        <?php if ($row->total_ewallet > 0): ?><span class="method-chip ew">e-Wallet: <?= rp_v($row->total_ewallet) ?></span><?php endif; ?>
                        <?php if ($row->total_piutang > 0): ?><span class="method-chip pt">Piutang: <?= rp_v($row->total_piutang) ?></span><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="grand-footer">
        <div class="g-label">Grand Total Periode</div>
        <div class="g-val"><?= rp_v($grandtotal) ?></div>
    </div>

    <div style="height:1.5rem;"></div>
</div>

