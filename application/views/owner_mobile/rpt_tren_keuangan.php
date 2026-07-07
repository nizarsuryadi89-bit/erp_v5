<?php function rp_w($n){return 'Rp '.number_format((float)$n,0,',','.');} ?>
<style>



.filter-bar{position:relative;z-index:20;margin: 0 0 1rem;background:white;border-radius:18px;box-shadow:0 6px 24px rgba(0,0,0,.08);padding:1rem;}
.filter-bar label{font-size:.7rem;font-weight:700;text-transform:uppercase;color:#64748b;display:block;margin-bottom:4px;}
.filter-bar input{width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:.45rem .75rem;font-size:.85rem;background:#f8fafc;}
.btn-filter{display:block;width:100%;margin-top:.65rem;background:linear-gradient(135deg,#6366f1,#4338ca);color:white;border:none;padding:.55rem;border-radius:10px;font-size:.85rem;font-weight:700;cursor:pointer;}
.tren-chart-wrap{background:white;border-radius:18px;box-shadow:0 3px 14px rgba(0,0,0,.05);border:1px solid #f1f5f9;padding:1.1rem;margin-bottom:1rem;overflow:hidden;}
.tren-chart-title{font-size:.75rem;font-weight:800;text-transform:uppercase;color:#64748b;margin-bottom:1rem;}
canvas{max-height:220px;}
.tren-table{background:white;border-radius:18px;box-shadow:0 3px 14px rgba(0,0,0,.05);border:1px solid #f1f5f9;overflow:hidden;margin-bottom:1rem;}
.tren-head{background:#f8fafc;padding:.75rem 1rem;border-bottom:1px solid #e2e8f0;font-size:.7rem;font-weight:800;color:#475569;text-transform:uppercase;}
.tren-row{display:grid;grid-template-columns:auto 1fr 1fr 1fr;gap:.5rem;align-items:center;padding:.7rem 1rem;border-bottom:1px solid #f1f5f9;font-size:.78rem;}
.tren-row:last-child{border-bottom:none;}
.tren-row.head-row{background:#f8fafc;font-weight:800;font-size:.65rem;text-transform:uppercase;color:#64748b;}
.tren-bulan{font-weight:700;color:#1e293b;}
.tren-pend{color:#10b981;font-weight:700;}
.tren-peng{color:#ef4444;font-weight:700;}
.tren-laba{font-weight:800;}
</style>

<div class="app-container">

    <div class="filter-bar">
        <form method="GET" action="<?= base_url('Owner/rpt_tren_keuangan/'.$token) ?>">
            <input type="hidden" name="perusahaan_id" value="<?= $perusahaan_id ?>">
            <label>Cabang</label>
            <select name="perusahaan_id">
                <?php foreach($all_perusahaan as $p): ?>
                    <option value="<?= $p->id_perusahaan ?>" <?= $p->id_perusahaan==$perusahaan_id?'selected':'' ?>><?= $p->nama_perusahaan ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-filter"><i class="fas fa-sync"></i> Refresh</button>
        </form>
    </div>

    <div class="tren-chart-wrap">
        <div class="tren-chart-title"><i class="fas fa-chart-area"></i> Grafik Tren 12 Bulan Terakhir</div>
        <canvas id="trenChart"></canvas>
    </div>

    <div class="tren-table">
        <div class="tren-row head-row">
            <span>Bulan</span><span style="text-align:right;">Pendapatan</span><span style="text-align:right;">Pengeluaran</span><span style="text-align:right;">Laba</span>
        </div>
        <?php foreach($tren_data as $t):
            $laba = $t['total_pendapatan'] - $t['total_pengeluaran'];
        ?>
        <div class="tren-row">
            <span class="tren-bulan" style="font-size:.7rem;"><?= date('M Y', strtotime($t['bulan'].'-01')) ?></span>
            <span class="tren-pend" style="text-align:right;font-size:.75rem;"><?= rp_w($t['total_pendapatan']) ?></span>
            <span class="tren-peng" style="text-align:right;font-size:.75rem;"><?= rp_w($t['total_pengeluaran']) ?></span>
            <span class="tren-laba" style="text-align:right;font-size:.75rem;color:<?= $laba>=0?'#10b981':'#ef4444' ?>;"><?= rp_w($laba) ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <div style="height:1.5rem;"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const trenData = <?= json_encode($tren_data) ?>;
const labels = trenData.map(t => {
    const d = new Date(t.bulan + '-01');
    return d.toLocaleDateString('id-ID',{month:'short',year:'2-digit'});
});
const pend = trenData.map(t => t.total_pendapatan);
const peng = trenData.map(t => t.total_pengeluaran);
const laba = trenData.map(t => t.total_pendapatan - t.total_pengeluaran);
new Chart(document.getElementById('trenChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            { label:'Pendapatan', data:pend, backgroundColor:'rgba(99,102,241,.7)', borderRadius:4 },
            { label:'Pengeluaran', data:peng, backgroundColor:'rgba(239,68,68,.5)', borderRadius:4 },
            { label:'Laba', type:'line', data:laba, borderColor:'#10b981', backgroundColor:'rgba(16,185,129,.1)', fill:true, tension:.4, pointRadius:3 }
        ]
    },
    options:{ responsive:true, maintainAspectRatio:true, plugins:{ legend:{ labels:{ boxWidth:10, font:{ size:10 } } } },
        scales:{ x:{ ticks:{ font:{size:9} } }, y:{ ticks:{ callback:v => 'Rp '+(v/1000000).toFixed(0)+'jt', font:{size:9} } } } }
});
</script>

