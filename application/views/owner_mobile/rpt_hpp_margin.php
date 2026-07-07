<?php function rp_hpp($n){return 'Rp '.number_format((float)$n,0,',','.');} ?>
<style>



.filter-bar{position:relative;z-index:20;margin: 0 0 1rem;background:white;border-radius:18px;box-shadow:0 6px 24px rgba(0,0,0,.08);padding:1rem;}
.filter-bar label{font-size:.7rem;font-weight:700;text-transform:uppercase;color:#64748b;display:block;margin-bottom:4px;}
.filter-bar input{width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:.45rem .75rem;font-size:.85rem;background:#f8fafc;}
.btn-filter{display:block;width:100%;margin-top:.65rem;background:linear-gradient(135deg,#1abc9c,#16a085);color:white;border:none;padding:.55rem;border-radius:10px;font-size:.85rem;font-weight:700;cursor:pointer;}
.hpp-card{background:white;border-radius:18px;box-shadow:0 3px 14px rgba(0,0,0,.05);border:1px solid #f1f5f9;overflow:hidden;margin-bottom:.75rem;}
.hpp-head{background:#f8fafc;padding:.75rem 1rem;border-bottom:1px solid #e2e8f0;font-size:.7rem;font-weight:800;color:#475569;text-transform:uppercase;}
.hpp-item{padding:.9rem 1rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:.75rem;}
.hpp-item:last-child{border-bottom:none;}
.hpp-rank{width:32px;height:32px;border-radius:10px;background:linear-gradient(135deg,#1abc9c,#16a085);display:flex;align-items:center;justify-content:center;color:white;font-size:.75rem;font-weight:900;flex-shrink:0;}
.hpp-main{flex:1;}
.hpp-name{font-size:.85rem;font-weight:700;color:#1e293b;}
.hpp-sub{font-size:.7rem;color:#64748b;}
.hpp-right{text-align:right;}
.hpp-margin{font-size:.95rem;font-weight:800;color:#1abc9c;}
.hpp-margin-bar{background:#f1f5f9;border-radius:20px;height:5px;overflow:hidden;margin-top:4px;}
.hpp-margin-fill{height:5px;border-radius:20px;background:linear-gradient(90deg,#1abc9c,#16a085);}
.hpp-pct{font-size:.62rem;color:#64748b;}
</style>

<div class="app-container">

    <div class="filter-bar">
        <form method="GET" action="<?= base_url('Owner/rpt_hpp_margin/'.$token) ?>">
            <input type="hidden" name="perusahaan_id" value="<?= $perusahaan_id ?>">
            <label>Bulan</label>
            <input type="month" name="bulan" value="<?= $bulan ?>">
            <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Tampilkan</button>
        </form>
    </div>

    <div class="hpp-card">
        <div class="hpp-head">🔥 Ranking Produk Terprofitable (<?= count($hpp_data) ?>)</div>
        <?php if(empty($hpp_data)): ?>
            <div style="text-align:center;padding:2rem;color:#94a3b8;"><i class="fas fa-inbox" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>Tidak ada data bulan ini</div>
        <?php else: ?>
            <?php $maxMargin = max(array_map(fn($h)=>(float)$h->total_margin, $hpp_data)); ?>
            <?php foreach($hpp_data as $i => $h): ?>
                <div class="hpp-item">
                    <div class="hpp-rank"><?= $i+1 ?></div>
                    <div class="hpp-main">
                        <div class="hpp-name"><?= htmlspecialchars($h->product_nama) ?></div>
                        <div class="hpp-sub">Qty: <?= number_format($h->total_qty, 0, ',', '.') ?> · Omset: <?= rp_hpp($h->total_pendapatan) ?></div>
                        <?php $pct_bar = $maxMargin > 0 ? ($h->total_margin/$maxMargin)*100 : 0; ?>
                        <div class="hpp-margin-bar"><div class="hpp-margin-fill" style="width:<?= $pct_bar ?>%;"></div></div>
                    </div>
                    <div class="hpp-right">
                        <div class="hpp-margin"><?= rp_hpp($h->total_margin) ?></div>
                        <div class="hpp-pct"><?= number_format($h->avg_margin_pct ?? 0, 1) ?>% margin</div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div style="height:1.5rem;"></div>
</div>

