<?php function rp_ak($n){return 'Rp '.number_format((float)$n,0,',','.');} ?>
<style>



.filter-bar{position:relative;z-index:20;margin: 0 0 1rem;background:white;border-radius:18px;box-shadow:0 6px 24px rgba(0,0,0,.08);padding:1rem;}
.filter-bar label{font-size:.7rem;font-weight:700;text-transform:uppercase;color:#64748b;display:block;margin-bottom:4px;}
.filter-bar input{width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:.45rem .75rem;font-size:.85rem;background:#f8fafc;}
.filter-bar .filter-row{display:flex;gap:.5rem;}
.filter-bar .filter-row>div{flex:1;}
.btn-filter{display:block;width:100%;margin-top:.65rem;background:linear-gradient(135deg,#0ea5e9,#0284c7);color:white;border:none;padding:.55rem;border-radius:10px;font-size:.85rem;font-weight:700;cursor:pointer;}
.ak-hero{border-radius:18px;padding:1.1rem;margin-bottom:1rem;text-align:center;}
.ak-hero.surplus{background:linear-gradient(135deg,#d1fae5,#a7f3d0);}
.ak-hero.defisit{background:linear-gradient(135deg,#fee2e2,#fecaca);}
.ak-hero .ah-label{font-size:.75rem;font-weight:700;text-transform:uppercase;color:#64748b;}
.ak-hero .ah-val{font-size:1.5rem;font-weight:900;}
.ak-rows{background:white;border-radius:18px;box-shadow:0 3px 14px rgba(0,0,0,.05);border:1px solid #f1f5f9;overflow:hidden;margin-bottom:1rem;}
.ak-section-head{background:#f8fafc;padding:.6rem 1rem;border-bottom:1px solid #e2e8f0;font-size:.7rem;font-weight:800;color:#475569;text-transform:uppercase;}
.ak-item{display:flex;justify-content:space-between;align-items:center;padding:.7rem 1rem;border-bottom:1px solid #f9fafb;}
.ak-item:last-child{border-bottom:none;}
.ak-item-label{font-size:.8rem;color:#475569;}
.ak-item-val{font-size:.85rem;font-weight:700;}
.ak-total-row{display:flex;justify-content:space-between;padding:.8rem 1rem;background:#f8fafc;}
.ak-total-label{font-size:.82rem;font-weight:800;color:#1e293b;}
.ak-total-val{font-size:.9rem;font-weight:900;}
.chart-wrap{background:white;border-radius:18px;box-shadow:0 3px 14px rgba(0,0,0,.05);border:1px solid #f1f5f9;padding:1rem;margin-bottom:1rem;}
.chart-title{font-size:.75rem;font-weight:800;text-transform:uppercase;color:#64748b;margin-bottom:.75rem;}
</style>

<div class="app-container">

    <div class="filter-bar">
        <form method="GET" action="<?= base_url('Owner/rpt_arus_kas/'.$token) ?>">
            <input type="hidden" name="perusahaan_id" value="<?= $perusahaan_id ?>">
            <div class="filter-row">
                <div><label>Dari</label><input type="date" name="mulai" value="<?= $mulai ?>"></div>
                <div><label>Sampai</label><input type="date" name="selesai" value="<?= $selesai ?>"></div>
            </div>
            <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Tampilkan</button>
        </form>
    </div>

    <div class="ak-hero <?= $net_arus_kas >= 0 ? 'surplus' : 'defisit' ?>">
        <div class="ah-label"><?= $net_arus_kas >= 0 ? '✅ Surplus' : '⚠️ Defisit' ?> Arus Kas</div>
        <div class="ah-val" style="color:<?= $net_arus_kas >= 0 ? '#059669' : '#dc2626' ?>;"><?= rp_ak(abs($net_arus_kas)) ?></div>
    </div>

    <!-- Chart -->
    <?php if(!empty($daily_trend)): ?>
    <div class="chart-wrap">
        <div class="chart-title"><i class="fas fa-chart-area"></i> Tren Harian</div>
        <canvas id="akChart" style="max-height:180px;"></canvas>
    </div>
    <?php endif; ?>

    <!-- Arus Masuk -->
    <div class="ak-rows">
        <div class="ak-section-head">📥 Arus Masuk</div>
        <div class="ak-item"><span class="ak-item-label">Tunai</span><span class="ak-item-val" style="color:#10b981;"><?= rp_ak($masuk_tunai) ?></span></div>
        <div class="ak-item"><span class="ak-item-label">Transfer Bank</span><span class="ak-item-val" style="color:#6366f1;"><?= rp_ak($masuk_transfer) ?></span></div>
        <div class="ak-item"><span class="ak-item-label">EDC / Debit</span><span class="ak-item-val" style="color:#f59e0b;"><?= rp_ak($masuk_debit) ?></span></div>
        <div class="ak-item"><span class="ak-item-label">E-Wallet</span><span class="ak-item-val" style="color:#8b5cf6;"><?= rp_ak($masuk_ewallet) ?></span></div>
        <div class="ak-total-row"><span class="ak-total-label">Total Masuk</span><span class="ak-total-val" style="color:#10b981;"><?= rp_ak($total_masuk) ?></span></div>
    </div>

    <!-- Arus Keluar -->
    <div class="ak-rows">
        <div class="ak-section-head">📤 Arus Keluar</div>
        <div class="ak-item"><span class="ak-item-label">Tunai</span><span class="ak-item-val" style="color:#ef4444;"><?= rp_ak($keluar_tunai) ?></span></div>
        <div class="ak-item"><span class="ak-item-label">Transfer Bank</span><span class="ak-item-val" style="color:#ef4444;"><?= rp_ak($keluar_transfer) ?></span></div>
        <div class="ak-item"><span class="ak-item-label">Lainnya</span><span class="ak-item-val" style="color:#ef4444;"><?= rp_ak($keluar_lain) ?></span></div>
        <div class="ak-total-row"><span class="ak-total-label">Total Keluar</span><span class="ak-total-val" style="color:#ef4444;"><?= rp_ak($total_keluar) ?></span></div>
    </div>

    <div style="height:1.5rem;"></div>
</div>

<?php if(!empty($daily_trend)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const akData = <?= json_encode($daily_trend) ?>;
new Chart(document.getElementById('akChart'), {
    type: 'line',
    data: {
        labels: akData.map(d => d.tgl),
        datasets: [
            { label:'Masuk', data: akData.map(d => d.total_masuk), borderColor:'#10b981', backgroundColor:'rgba(16,185,129,.1)', fill:true, tension:.4, pointRadius:2 },
            { label:'Keluar', data: akData.map(d => d.total_keluar), borderColor:'#ef4444', backgroundColor:'rgba(239,68,68,.05)', fill:true, tension:.4, pointRadius:2 }
        ]
    },
    options:{ responsive:true, maintainAspectRatio:true,
        plugins:{ legend:{ labels:{ boxWidth:10, font:{size:10} } } },
        scales:{ x:{ ticks:{ font:{size:8} } }, y:{ ticks:{ callback:v=>'Rp '+(v/1000000).toFixed(0)+'jt', font:{size:9} } } }
    }
});
</script>
<?php endif; ?>

