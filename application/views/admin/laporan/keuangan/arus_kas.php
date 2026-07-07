<style>
:root{--primary-gradient:linear-gradient(135deg,#1a1c23 0%,#2c3e50 100%);--premium-shadow:0 10px 30px rgba(0,0,0,.05);--slate-dark:#1e293b;--slate-muted:#64748b;}
.report-hero{background:var(--primary-gradient);color:white;padding:2rem 0 5rem;border-radius:0 0 30px 30px;box-shadow:0 15px 35px rgba(0,0,0,.15);}
.report-content-area{margin-top:-3.5rem;position:relative;z-index:10;padding-bottom:2rem;}
.report-content-card{background:white;border-radius:24px;padding:2rem;box-shadow:var(--premium-shadow);border:1px solid #f1f5f9;margin-bottom:1.5rem;}
.btn-back{background:rgba(255,255,255,.1);color:#94a3b8;border:1px solid rgba(255,255,255,.2);border-radius:10px;padding:0 16px;height:38px;font-weight:700;font-size:.82rem;text-decoration:none;display:inline-flex;align-items:center;gap:6px;}
.btn-back:hover{background:rgba(255,255,255,.2);color:white;}
.btn-print{background:linear-gradient(135deg,#1e293b,#334155);color:white;border:none;border-radius:10px;padding:0 18px;height:38px;font-weight:700;font-size:.82rem;cursor:pointer;display:inline-flex;align-items:center;gap:6px;}
.filter-bar{background:#f8fafc;border:1px solid #e2e8f0;border-radius:16px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;display:flex;align-items:center;flex-wrap:wrap;gap:12px;}
.section-title{font-size:1rem;font-weight:800;color:var(--slate-dark);margin-bottom:1.25rem;display:flex;align-items:center;gap:10px;}
.section-title::before{content:'';display:inline-block;width:4px;height:20px;background:linear-gradient(135deg,#0ea5e9,#0284c7);border-radius:4px;}
.flow-row{display:flex;align-items:stretch;gap:1.5rem;margin-bottom:1.5rem;flex-wrap:wrap;}
.flow-box{flex:1;min-width:220px;border-radius:18px;padding:1.5rem;}
.flow-masuk{background:linear-gradient(135deg,#ecfdf5,#d1fae5);border:1px solid #a7f3d0;}
.flow-keluar{background:linear-gradient(135deg,#fff1f2,#fecdd3);border:1px solid #fca5a5;}
.flow-net{border-radius:18px;padding:1.5rem;text-align:center;}
.flow-box h4{font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.5px;margin-bottom:1rem;}
.flow-item{display:flex;justify-content:space-between;align-items:center;padding:.5rem 0;border-bottom:1px dashed rgba(0,0,0,.07);font-size:.88rem;}
.flow-item:last-child{border-bottom:none;}
.flow-item strong{font-weight:800;}
@media print{.report-hero,.filter-bar,.btn-back,.btn-print,.main-header,.main-sidebar,.content-header,.control-sidebar,footer{display:none !important;}.report-content-area{margin-top:0 !important;}.report-content-card{box-shadow:none !important;border:1px solid #ddd !important;}.print-header{display:block !important;}body{background:white !important;}.content-wrapper{margin-left:0 !important;}}
.print-header{display:none;text-align:center;margin-bottom:16px;}
.print-header h2{font-size:16pt;font-weight:900;margin:0 0 4px;}
.print-header p{font-size:9pt;color:#666;margin:0;}
.print-header hr{border-top:2px solid #1e293b;margin:8px 0;}
</style>

<div class="print-header">
    <h2>Laporan Arus Kas</h2>
    <p>Periode: <?= date('d/m/Y', strtotime($mulai)) ?> s/d <?= date('d/m/Y', strtotime($selesai)) ?> | Dicetak: <?= date('d/m/Y H:i') ?></p><hr>
</div>

<div class="container-fluid px-4">
    <div class="report-hero">
        <div class="container-fluid px-5">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
                <div style="display:flex;align-items:center;gap:1.25rem;">
                    <div style="width:65px;height:65px;background:rgba(255,255,255,.1);border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:1.8rem;border:1px solid rgba(255,255,255,.2);"><i class="fa fa-exchange"></i></div>
                    <div>
                        <h1 style="font-size:1.9rem;font-weight:900;margin:0 0 .25rem 0;">Laporan Arus Kas</h1>
                        <p style="margin:0;color:#94a3b8;font-size:.88rem;">Aktivitas kas masuk dan keluar per periode — posisi likuiditas bisnis</p>
                    </div>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <button onclick="window.print()" class="btn-print"><i class="fa fa-print"></i> Cetak</button>
                    <button type="button" class="btn-kirim-owner no-print"
                        data-toggle="modal" data-target="#modalKirimOwner"
                        data-report-type="arus_kas"
                        data-params='<?= json_encode(['perusahaan_id'=>(int)$current_perusahaan_id,'mulai'=>$mulai,'selesai'=>$selesai]) ?>'>
                        <i class="fa fa-paper-plane"></i> Kirim ke Owner
                    </button>
                    <a href="<?= base_url('laporan/financial_hub') ?>" class="btn-back"><i class="fa fa-arrow-left"></i> Kembali</a>
                </div>
            </div>
        </div>
    </div>

    <div class="report-content-area">
        <div class="container-fluid px-5">
            <form class="filter-bar" action="" method="GET">
                <label style="font-weight:700;font-size:.85rem;margin:0;">Periode</label>
                <input type="date" class="form-control" name="mulai" value="<?= $mulai ?>" style="width:auto;border-radius:10px;height:38px;">
                <span>s/d</span>
                <input type="date" class="form-control" name="selesai" value="<?= $selesai ?>" style="width:auto;border-radius:10px;height:38px;">
                <?php if ($is_admin_owner): ?>
                <select class="form-control" name="perusahaan_id" style="width:auto;border-radius:10px;height:38px;">
                    <?php foreach ($all_perusahaan as $p): ?>
                        <option value="<?= $p->id_perusahaan ?>" <?= $p->id_perusahaan == $current_perusahaan_id ? 'selected' : '' ?>><?= $p->nama_perusahaan ?></option>
                    <?php endforeach; ?>
                </select>
                <?php endif; ?>
                <button type="submit" style="background:linear-gradient(135deg,#0ea5e9,#0284c7);color:white;border:none;border-radius:10px;padding:0 20px;height:38px;font-weight:700;cursor:pointer;"><i class="fa fa-refresh"></i> Tampilkan</button>
            </form>

            <!-- Net Cash Flow Banner -->
            <div style="background:<?= $net_arus_kas >= 0 ? 'linear-gradient(135deg,#059669,#047857)' : 'linear-gradient(135deg,#dc2626,#b91c1c)' ?>;color:white;border-radius:20px;padding:1.75rem 2rem;margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;opacity:.8;">Net Arus Kas Bersih</div>
                    <div style="font-size:2rem;font-weight:900;">Rp <?= number_format(abs($net_arus_kas), 0, ',', '.') ?></div>
                    <div style="font-size:.85rem;opacity:.8;"><?= $net_arus_kas >= 0 ? '✅ Arus Kas Positif — Likuiditas Sehat' : '⚠️ Arus Kas Negatif — Perlu Perhatian' ?></div>
                </div>
                <div style="font-size:4rem;opacity:.25;"><i class="fa fa-<?= $net_arus_kas >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i></div>
            </div>

            <!-- Flow Detail -->
            <div class="flow-row">
                <div class="flow-box flow-masuk">
                    <h4 style="color:#059669;">💰 Arus Kas Masuk (Penerimaan)</h4>
                    <div class="flow-item"><span>Tunai</span><strong style="color:#059669;">Rp <?= number_format($masuk_tunai,0,',','.') ?></strong></div>
                    <div class="flow-item"><span>Transfer Bank</span><strong style="color:#059669;">Rp <?= number_format($masuk_transfer,0,',','.') ?></strong></div>
                    <div class="flow-item"><span>EDC/Debit</span><strong style="color:#059669;">Rp <?= number_format($masuk_debit,0,',','.') ?></strong></div>
                    <div class="flow-item"><span>E-Wallet</span><strong style="color:#059669;">Rp <?= number_format($masuk_ewallet,0,',','.') ?></strong></div>
                    <div class="flow-item" style="border-top:2px solid #a7f3d0;margin-top:.5rem;"><span><strong>TOTAL MASUK</strong></span><strong style="color:#059669;font-size:1.1rem;">Rp <?= number_format($total_masuk,0,',','.') ?></strong></div>
                </div>
                <div class="flow-box flow-keluar">
                    <h4 style="color:#dc2626;">📤 Arus Kas Keluar (Pengeluaran)</h4>
                    <div class="flow-item"><span>Tunai</span><strong style="color:#dc2626;">Rp <?= number_format($keluar_tunai,0,',','.') ?></strong></div>
                    <div class="flow-item"><span>Transfer Bank</span><strong style="color:#dc2626;">Rp <?= number_format($keluar_transfer,0,',','.') ?></strong></div>
                    <div class="flow-item"><span>Lainnya</span><strong style="color:#dc2626;">Rp <?= number_format($keluar_lain,0,',','.') ?></strong></div>
                    <div class="flow-item" style="border-top:2px solid #fca5a5;margin-top:.5rem;"><span><strong>TOTAL KELUAR</strong></span><strong style="color:#dc2626;font-size:1.1rem;">Rp <?= number_format($total_keluar,0,',','.') ?></strong></div>
                </div>
            </div>

            <div class="report-content-card">
                <div class="section-title">Grafik Arus Kas Harian</div>
                <canvas id="arus-chart" style="max-height:280px;"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function _init_arus_kas(){ if(typeof jQuery==="undefined"){return setTimeout(_init_arus_kas,80);} var $=jQuery; (function(){
    var labels = <?= json_encode(array_map(function($r){return date('d/m',strtotime($r['tgl']));}, $daily_trend)) ?>;
    var masuk  = <?= json_encode(array_map(function($r){return (float)$r['total_masuk'];}, $daily_trend)) ?>;
    var keluar = <?= json_encode(array_map(function($r){return (float)$r['total_keluar'];}, $daily_trend)) ?>;
    new Chart(document.getElementById('arus-chart').getContext('2d'),{
        type:'bar',
        data:{labels:labels,datasets:[
            {label:'Arus Masuk',data:masuk,backgroundColor:'rgba(5,150,105,.7)',borderRadius:5},
            {label:'Arus Keluar',data:keluar,backgroundColor:'rgba(220,38,38,.65)',borderRadius:5}
        ]},
        options:{responsive:true,interaction:{mode:'index',intersect:false},
            plugins:{legend:{position:'top'},tooltip:{callbacks:{label:function(c){return c.dataset.label+': Rp '+c.raw.toLocaleString('id-ID');}}}},
            scales:{y:{ticks:{callback:function(v){return 'Rp '+(v/1000000).toFixed(1)+'jt';}}}}}
    });
})();
</script>
<?php $this->load->view('admin/laporan/partials/kirim_owner_modal'); ?>
