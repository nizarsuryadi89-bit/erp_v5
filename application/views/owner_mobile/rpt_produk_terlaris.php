<?php function rp_pr($n){return 'Rp '.number_format((float)$n,0,',','.');} ?>
<style>



.filter-bar{position:relative;z-index:20;margin: 0 0 1rem;background:white;border-radius:18px;box-shadow:0 6px 24px rgba(0,0,0,.08);padding:1rem;}
.filter-bar label{font-size:.7rem;font-weight:700;text-transform:uppercase;color:#64748b;display:block;margin-bottom:4px;}
.filter-bar select{width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:.45rem .75rem;font-size:.85rem;background:#f8fafc;}
.btn-filter{display:block;width:100%;margin-top:.65rem;background:linear-gradient(135deg,#e74c3c,#c0392b);color:white;border:none;padding:.55rem;border-radius:10px;font-size:.85rem;font-weight:700;cursor:pointer;}
.produk-list{background:white;border-radius:18px;box-shadow:0 3px 14px rgba(0,0,0,.05);border:1px solid #f1f5f9;overflow:hidden;margin-bottom:1rem;}
.produk-head{background:#f8fafc;padding:.75rem 1rem;border-bottom:1px solid #e2e8f0;font-size:.7rem;font-weight:800;color:#475569;text-transform:uppercase;}
.produk-item{padding:.9rem 1rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:.75rem;}
.produk-item:last-child{border-bottom:none;}
.produk-rank{width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,#e74c3c,#c0392b);display:flex;align-items:center;justify-content:center;color:white;font-size:.75rem;font-weight:900;flex-shrink:0;}
.produk-rank.top1{background:linear-gradient(135deg,#f59e0b,#d97706);}
.produk-rank.top2{background:linear-gradient(135deg,#94a3b8,#64748b);}
.produk-rank.top3{background:linear-gradient(135deg,#b45309,#92400e);}
.produk-main{flex:1;}
.produk-name{font-size:.85rem;font-weight:700;color:#1e293b;}
.produk-sub{font-size:.7rem;color:#64748b;}
.produk-right{text-align:right;}
.produk-qty{font-size:.95rem;font-weight:800;color:#1e293b;}
.produk-bar{background:#f1f5f9;border-radius:20px;height:6px;overflow:hidden;margin-top:8px;}
.produk-bar-fill{height:6px;border-radius:20px;background:linear-gradient(90deg,#1e3a8a,#3b82f6);}

.period-btn-wrap{background:white;border-radius:16px;padding:0.5rem;box-shadow:0 6px 24px rgba(0,0,0,.08);margin-bottom:1.25rem;position:relative;z-index:20;}
.period-btn{display:flex;gap:.5rem;}
.period-btn a{flex:1;text-align:center;padding:.65rem;border-radius:12px;font-size:.75rem;font-weight:700;text-decoration:none;color:#64748b;background:#f8fafc;transition:.2s;}
.period-btn a.active{background:linear-gradient(135deg,#1e3a8a,#172554);color:white;box-shadow:0 4px 12px rgba(30,58,138,.25);}
</style>

<div class="app-container">

    <div class="period-btn-wrap">
        <div class="period-btn">
            <?php foreach(['week'=>'Minggu Ini','month'=>'Bulan Ini','year'=>'Tahun Ini'] as $pk => $pl): ?>
                <a href="<?= base_url('Owner/rpt_produk_terlaris/'.$token.'?perusahaan_id='.$perusahaan_id.'&period='.$pk) ?>"
                   class="<?= $selected_period == $pk ? 'active' : '' ?>"><?= $pl ?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="produk-list">
        <div class="produk-head">🏆 Top Produk — <?= $selected_period == 'week' ? 'Minggu Ini' : ($selected_period=='year'?'Tahun Ini':'Bulan Ini') ?></div>
        <?php if(empty($top_products)): ?>
            <div style="text-align:center;padding:2rem;color:#94a3b8;"><i class="fas fa-inbox" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>Tidak ada data</div>
        <?php else: ?>
            <?php $maxQty = max(array_map(fn($p)=>(float)($p->total_sold??$p->total_terjual??$p->qty??1), $top_products)); ?>
            <?php foreach($top_products as $i => $p): 
                $qty = (float)($p->total_sold ?? $p->total_terjual ?? $p->qty ?? 0);
                $rankClass = $i==0?'top1':($i==1?'top2':($i==2?'top3':''));
                $pct = $maxQty > 0 ? ($qty/$maxQty)*100 : 0;
            ?>
                <div class="produk-item">
                    <div class="produk-rank <?= $rankClass ?>"><?= $i+1 ?></div>
                    <div class="produk-main">
                        <div class="produk-name"><?= htmlspecialchars($p->product_nama ?? $p->nama ?? '-') ?></div>
                        <div class="produk-sub"><?= $p->kategori_nama ?? '' ?></div>
                        <div class="produk-bar"><div class="produk-bar-fill" style="width:<?= $pct ?>%;"></div></div>
                    </div>
                    <div class="produk-right">
                        <div class="produk-qty"><?= number_format($qty, 0, ',', '.') ?> unit</div>
                        <?php if(!empty($p->total_revenue) || !empty($p->total_omset)): ?>
                            <div style="font-size:.68rem;color:#64748b;"><?= rp_pr($p->total_revenue ?? $p->total_omset) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div style="height:1.5rem;"></div>
</div>

