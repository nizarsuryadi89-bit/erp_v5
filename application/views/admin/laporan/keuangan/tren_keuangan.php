<style>
:root{--primary-gradient:linear-gradient(135deg,#1a1c23 0%,#2c3e50 100%);--premium-shadow:0 10px 30px rgba(0,0,0,.05);--slate-dark:#1e293b;--slate-muted:#64748b;--accent:#6366f1;}
.report-hero{background:var(--primary-gradient);color:white;padding:2rem 0 5rem;border-radius:0 0 30px 30px;box-shadow:0 15px 35px rgba(0,0,0,.15);}
.report-content-area{margin-top:-3.5rem;position:relative;z-index:10;padding-bottom:2rem;}
.report-content-card{background:white;border-radius:24px;padding:2rem;box-shadow:var(--premium-shadow);border:1px solid #f1f5f9;}
.btn-back{background:rgba(255,255,255,.1);color:#94a3b8;border:1px solid rgba(255,255,255,.2);border-radius:10px;padding:0 16px;height:38px;font-weight:700;font-size:.82rem;text-decoration:none;display:inline-flex;align-items:center;gap:6px;}
.btn-back:hover{background:rgba(255,255,255,.2);color:white;}
.btn-print{background:linear-gradient(135deg,#1e293b,#334155);color:white;border:none;border-radius:10px;padding:0 18px;height:38px;font-weight:700;font-size:.82rem;cursor:pointer;display:inline-flex;align-items:center;gap:6px;}
.btn-export{background:linear-gradient(135deg,#27ae60,#1e8449);color:white;border:none;border-radius:10px;padding:0 18px;height:38px;font-weight:700;font-size:.82rem;display:inline-flex;align-items:center;gap:6px;text-decoration:none;}
.btn-export:hover{opacity:.88;color:white;}
.filter-bar{background:#f8fafc;border:1px solid #e2e8f0;border-radius:16px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;display:flex;align-items:center;flex-wrap:wrap;gap:12px;}
.section-title{font-size:1rem;font-weight:800;color:var(--slate-dark);margin-bottom:1.25rem;display:flex;align-items:center;gap:10px;}
.section-title::before{content:'';display:inline-block;width:4px;height:20px;background:linear-gradient(135deg,#6366f1,#4338ca);border-radius:4px;}
.kpi-card{background:white;border-radius:16px;padding:1.25rem 1.5rem;box-shadow:var(--premium-shadow);border:1px solid #f1f5f9;text-align:center;margin-bottom:1rem;}
.kpi-card .val{font-size:1.5rem;font-weight:900;color:var(--slate-dark);}
.kpi-card .lbl{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--slate-muted);}
@media print{.report-hero,.filter-bar,.btn-back,.btn-print,.btn-export,.dataTables_wrapper .dataTables_filter,.dataTables_wrapper .dataTables_length,.dataTables_wrapper .dataTables_info,.dataTables_wrapper .dataTables_paginate,.main-header,.main-sidebar,.content-header,.control-sidebar,footer{display:none !important;}.report-content-area{margin-top:0 !important;}.report-content-card{box-shadow:none !important;border:none !important;border-radius:0 !important;padding:0 !important;}table{font-size:9pt !important;}thead th{background-color:#1e293b !important;color:white !important;-webkit-print-color-adjust:exact;print-color-adjust:exact;}.print-header{display:block !important;}body{background:white !important;}.content-wrapper{margin-left:0 !important;}}
.print-header{display:none;text-align:center;margin-bottom:16px;}
.print-header h2{font-size:16pt;font-weight:900;margin:0 0 4px;}
.print-header p{font-size:9pt;color:#666;margin:0;}
.print-header hr{border-top:2px solid #1e293b;margin:8px 0;}
</style>

<div class="print-header">
    <h2>Laporan Tren Keuangan 12 Bulan</h2>
    <p>Dicetak: <?= date('d/m/Y H:i') ?> | Perusahaan: <?= $perusahaan ?></p><hr>
</div>

<div class="container-fluid px-4">
    <div class="report-hero">
        <div class="container-fluid px-5">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
                <div style="display:flex;align-items:center;gap:1.25rem;">
                    <div style="width:65px;height:65px;background:rgba(255,255,255,.1);border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:1.8rem;border:1px solid rgba(255,255,255,.2);"><i class="fa fa-line-chart"></i></div>
                    <div>
                        <h1 style="font-size:1.9rem;font-weight:900;margin:0 0 .25rem 0;">Tren Keuangan 12 Bulan</h1>
                        <p style="margin:0;color:#94a3b8;font-size:.88rem;">Analisis pendapatan, pengeluaran, dan laba bersih per bulan</p>
                    </div>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <a href="<?= base_url('laporan/export_tren_keuangan?perusahaan_id='.$current_perusahaan_id) ?>" class="btn-export"><i class="fa fa-file-excel-o"></i> Excel</a>
                    <button onclick="window.print()" class="btn-print"><i class="fa fa-print"></i> Cetak</button>
                    <button type="button" class="btn-kirim-owner no-print"
                        data-toggle="modal" data-target="#modalKirimOwner"
                        data-report-type="tren_keuangan"
                        data-params='<?= json_encode(['perusahaan_id'=>(int)$current_perusahaan_id]) ?>'>
                        <i class="fa fa-paper-plane"></i> Kirim ke Owner
                    </button>
                    <a href="<?= base_url('laporan/financial_hub') ?>" class="btn-back"><i class="fa fa-arrow-left"></i> Kembali</a>
                </div>
            </div>
        </div>
    </div>

    <div class="report-content-area">
        <div class="container-fluid px-5">
            <?php if ($is_admin_owner): ?>
            <div class="filter-bar">
                <form action="" method="GET" style="display:flex;align-items:center;flex-wrap:wrap;gap:12px;width:100%">
                    <label style="font-weight:700;font-size:.85rem;margin:0;">Cabang</label>
                    <select class="form-control" name="perusahaan_id" style="width:auto;border-radius:10px;height:38px;">
                        <?php foreach ($all_perusahaan as $p): ?>
                            <option value="<?= $p->id_perusahaan ?>" <?= $p->id_perusahaan == $current_perusahaan_id ? 'selected' : '' ?>><?= $p->nama_perusahaan ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" style="background:linear-gradient(135deg,#6366f1,#4338ca);color:white;border:none;border-radius:10px;padding:0 20px;height:38px;font-weight:700;cursor:pointer;"><i class="fa fa-refresh"></i> Tampilkan</button>
                </form>
            </div>
            <?php endif; ?>

            <?php
            $total_pend_12 = array_sum(array_column($tren_data, 'total_pendapatan'));
            $total_peng_12 = array_sum(array_column($tren_data, 'total_pengeluaran'));
            $total_laba_12 = $total_pend_12 - $total_peng_12;
            $last  = end($tren_data);
            $prev  = count($tren_data) >= 2 ? $tren_data[count($tren_data)-2] : ['total_pendapatan'=>0];
            $growth = $prev['total_pendapatan'] > 0 ? (($last['total_pendapatan'] - $prev['total_pendapatan']) / $prev['total_pendapatan']) * 100 : 0;
            ?>
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="kpi-card">
                        <div class="lbl">Total Pendapatan 12 Bln</div>
                        <div class="val text-primary">Rp <?= number_format($total_pend_12, 0, ',', '.') ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card">
                        <div class="lbl">Total Pengeluaran 12 Bln</div>
                        <div class="val text-danger">Rp <?= number_format($total_peng_12, 0, ',', '.') ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card">
                        <div class="lbl">Laba Bersih 12 Bln</div>
                        <div class="val <?= $total_laba_12 >= 0 ? 'text-success' : 'text-danger' ?>">Rp <?= number_format($total_laba_12, 0, ',', '.') ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card">
                        <div class="lbl">Growth Bulan Terakhir</div>
                        <div class="val <?= $growth >= 0 ? 'text-success' : 'text-danger' ?>"><?= ($growth>=0?'+':'').number_format($growth,1) ?>%</div>
                    </div>
                </div>
            </div>

            <div class="report-content-card">
                <div class="section-title">Grafik Tren 12 Bulan</div>
                <canvas id="tren-chart" style="max-height:320px;margin-bottom:2rem;"></canvas>

                <div class="section-title">Tabel Detail per Bulan</div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead style="background-color:#1e293b;color:white;">
                            <tr>
                                <th>Bulan</th>
                                <th class="text-right">Pendapatan</th>
                                <th class="text-right">Pengeluaran</th>
                                <th class="text-right">Laba Bersih</th>
                                <th class="text-center">Margin</th>
                                <th class="text-center">Growth MoM</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $prev_p = 0; foreach ($tren_data as $idx => $row):
                                $laba_r = $row['total_pendapatan'] - $row['total_pengeluaran'];
                                $margin_r = $row['total_pendapatan'] > 0 ? ($laba_r / $row['total_pendapatan']) * 100 : 0;
                                $growth_r = $prev_p > 0 ? (($row['total_pendapatan'] - $prev_p) / $prev_p) * 100 : 0;
                                $prev_p = $row['total_pendapatan'];
                            ?>
                            <tr>
                                <td><strong><?= date('F Y', strtotime($row['bulan'].'-01')) ?></strong></td>
                                <td class="text-right text-primary"><strong>Rp <?= number_format($row['total_pendapatan'],0,',','.') ?></strong></td>
                                <td class="text-right text-danger">Rp <?= number_format($row['total_pengeluaran'],0,',','.') ?></td>
                                <td class="text-right <?= $laba_r>=0?'text-success':'text-danger' ?>"><strong>Rp <?= number_format($laba_r,0,',','.') ?></strong></td>
                                <td class="text-center"><span class="label label-<?= $margin_r>=20?'success':($margin_r>=10?'warning':'danger') ?>"><?= number_format($margin_r,1) ?>%</span></td>
                                <td class="text-center">
                                    <?php if($idx>0): ?><span style="color:<?= $growth_r>=0?'#27ae60':'#f43f5e' ?>;font-weight:700;"><?= ($growth_r>=0?'▲ +':'▼ ').number_format($growth_r,1) ?>%</span>
                                    <?php else: ?>—<?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr style="background:#f8fafc;font-weight:900;">
                                <td><strong>TOTAL</strong></td>
                                <td class="text-right text-primary"><strong>Rp <?= number_format($total_pend_12,0,',','.') ?></strong></td>
                                <td class="text-right text-danger"><strong>Rp <?= number_format($total_peng_12,0,',','.') ?></strong></td>
                                <td class="text-right <?= $total_laba_12>=0?'text-success':'text-danger' ?>"><strong>Rp <?= number_format($total_laba_12,0,',','.') ?></strong></td>
                                <td class="text-center"><?php $am=$total_pend_12>0?($total_laba_12/$total_pend_12)*100:0; ?><span class="label label-<?= $am>=20?'success':($am>=10?'warning':'danger') ?>"><?= number_format($am,1) ?>%</span></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function _init_tren_keuangan(){ if(typeof jQuery==="undefined"){return setTimeout(_init_tren_keuangan,80);} var $=jQuery; (function(){
    var labels = <?= json_encode(array_map(function($r){return date('M Y',strtotime($r['bulan'].'-01'));}, $tren_data)) ?>;
    var pend   = <?= json_encode(array_map(function($r){return (float)$r['total_pendapatan'];}, $tren_data)) ?>;
    var peng   = <?= json_encode(array_map(function($r){return (float)$r['total_pengeluaran'];}, $tren_data)) ?>;
    var laba   = pend.map(function(p,i){return p-peng[i];});
    new Chart(document.getElementById('tren-chart').getContext('2d'),{
        type:'bar',
        data:{labels:labels,datasets:[
            {label:'Pendapatan',data:pend,backgroundColor:'rgba(99,102,241,0.75)',borderRadius:6,order:2},
            {label:'Pengeluaran',data:peng,backgroundColor:'rgba(244,63,94,0.6)',borderRadius:6,order:2},
            {label:'Laba Bersih',data:laba,type:'line',borderColor:'#27ae60',backgroundColor:'rgba(39,174,96,.1)',borderWidth:2.5,pointRadius:5,tension:.4,fill:true,order:1}
        ]},
        options:{responsive:true,interaction:{mode:'index',intersect:false},
            plugins:{legend:{position:'top'},tooltip:{callbacks:{label:function(c){return c.dataset.label+': Rp '+c.raw.toLocaleString('id-ID');}}}},
            scales:{y:{ticks:{callback:function(v){return 'Rp '+(v/1000000).toFixed(0)+'jt';}}}}}
    });
})();
</script>
<?php $this->load->view('admin/laporan/partials/kirim_owner_modal'); ?>
