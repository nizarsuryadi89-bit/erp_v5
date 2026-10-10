<?php function rp_pk($n){return 'Rp '.number_format((float)$n,0,',','.');} ?>
<style>



.filter-bar{position:relative;z-index:20;margin: 0 0 1rem;background:white;border-radius:18px;box-shadow:0 6px 24px rgba(0,0,0,.08);padding:1rem;}
.filter-bar label{font-size:.7rem;font-weight:700;text-transform:uppercase;color:#64748b;display:block;margin-bottom:4px;}
.filter-bar input{width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:.45rem .75rem;font-size:.85rem;background:#f8fafc;}
.filter-bar .filter-row{display:flex;gap:.5rem;}
.filter-bar .filter-row>div{flex:1;}
.btn-filter{display:block;width:100%;margin-top:.65rem;background:linear-gradient(135deg,#8e44ad,#9b59b6);color:white;border:none;padding:.55rem;border-radius:10px;font-size:.85rem;font-weight:700;cursor:pointer;}
.kat-card{background:white;border-radius:18px;box-shadow:0 3px 14px rgba(0,0,0,.05);border:1px solid #f1f5f9;margin-bottom:.75rem;overflow:hidden;}
.kat-header{padding:1rem;display:flex;align-items:center;gap:.75rem;cursor:pointer;background:#f8fafc;border-bottom:1px solid #f1f5f9;}
.kat-icon{width:42px;height:42px;border-radius:13px;background:linear-gradient(135deg,#8e44ad,#9b59b6);display:flex;align-items:center;justify-content:center;color:white;font-size:1rem;}
.kat-name{font-size:.9rem;font-weight:800;color:#1e293b;flex:1;}
.kat-omset{font-size:.95rem;font-weight:900;color:#8e44ad;}
.kat-qty{font-size:.7rem;color:#64748b;}
.kat-body{padding:.75rem;}
.kat-row{display:flex;justify-content:space-between;font-size:.78rem;padding:.4rem 0;border-bottom:1px solid #f9fafb;}
.kat-row:last-child{border-bottom:none;}
.kat-row-name{color:#475569;max-width:60%;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.kat-row-val{font-weight:700;color:#1e293b;}
.total-bar{background:linear-gradient(135deg,#8e44ad,#9b59b6);border-radius:14px;padding:.75rem;color:white;text-align:center;margin-bottom:1rem;}
</style>

<div class="app-container">

    <div class="filter-bar">
        <form method="GET" action="<?= base_url('Owner/rpt_penjualan_kategori/'.$token) ?>">
            <input type="hidden" name="perusahaan_id" value="<?= $perusahaan_id ?>">
            <div class="filter-row">
                <div><label>Dari</label><input type="date" name="start_date" value="<?= $start_date ?>"></div>
                <div><label>Sampai</label><input type="date" name="end_date" value="<?= $end_date ?>"></div>
            </div>
            <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Tampilkan</button>
        </form>
    </div>

    <?php $total_grand = array_sum(array_column($penjualan_kategori, 'total_omset')); ?>
    <div class="total-bar">
        <div style="font-size:.7rem;opacity:.8;text-transform:uppercase;font-weight:700;">Total Omset Semua Kategori</div>
        <div style="font-size:1.3rem;font-weight:900;"><?= rp_pk($total_grand) ?></div>
    </div>

    <?php
    $colors = ['#8e44ad','#6366f1','#0ea5e9','#10b981','#f59e0b','#ef4444','#ec4899','#14b8a6'];
    foreach($penjualan_kategori as $ci => $kat):
        $col = $colors[$ci % count($colors)];
        $pct = $total_grand > 0 ? ($kat['total_omset']/$total_grand)*100 : 0;
    ?>
    <div class="kat-card">
        <div class="kat-header">
            <div class="kat-icon" style="background:<?= $col ?>;"><i class="fas fa-layer-group"></i></div>
            <div style="flex:1;">
                <div class="kat-name"><?= htmlspecialchars($kat['kategori_nama']) ?></div>
                <div class="kat-qty"><?= number_format($kat['total_qty'],0,',','.') ?> unit · <?= number_format($pct,1) ?>% dari total</div>
            </div>
            <div class="kat-omset"><?= rp_pk($kat['total_omset']) ?></div>
        </div>
        <?php if(!empty($kat['transactions'])): ?>
        <div class="kat-body">
            <?php foreach(array_slice($kat['transactions'],0,5) as $trx): ?>
                <div class="kat-row">
                    <span class="kat-row-name"><?= htmlspecialchars($trx['product_nama']) ?></span>
                    <span class="kat-row-val"><?= rp_pk($trx['subtotal']) ?></span>
                </div>
            <?php endforeach; ?>
            <?php if(count($kat['transactions'])>5): ?>
                <div style="font-size:.68rem;color:#94a3b8;text-align:center;padding-top:.3rem;">+<?= count($kat['transactions'])-5 ?> transaksi lainnya</div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <?php if(empty($penjualan_kategori)): ?>
        <div style="text-align:center;padding:2rem;color:#94a3b8;background:white;border-radius:18px;"><i class="fas fa-inbox" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>Tidak ada data periode ini</div>
    <?php endif; ?>

    <div style="height:1.5rem;"></div>
</div>

