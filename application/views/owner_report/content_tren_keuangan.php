<?php
$tot_p = array_sum(array_column($tren_data, 'total_pendapatan'));
$tot_g = array_sum(array_column($tren_data, 'total_pengeluaran'));
$tot_l = $tot_p - $tot_g;
$last  = end($tren_data);
$prev  = count($tren_data) >= 2 ? $tren_data[count($tren_data)-2] : ['total_pendapatan'=>0];
$growth= $prev['total_pendapatan'] > 0 ? (($last['total_pendapatan']-$prev['total_pendapatan'])/$prev['total_pendapatan'])*100 : 0;
?>
<div class="kpi-grid">
    <div class="kpi-card"><div class="lbl">Total Pendapatan 12 Bln</div><div class="val" style="color:#2563eb;">Rp <?= number_format($tot_p,0,',','.') ?></div></div>
    <div class="kpi-card"><div class="lbl">Total Pengeluaran 12 Bln</div><div class="val" style="color:#dc2626;">Rp <?= number_format($tot_g,0,',','.') ?></div></div>
    <div class="kpi-card"><div class="lbl">Laba Bersih 12 Bln</div><div class="val" style="color:<?= $tot_l>=0?'#059669':'#dc2626' ?>;">Rp <?= number_format($tot_l,0,',','.') ?></div></div>
    <div class="kpi-card"><div class="lbl">Growth Bulan Terakhir</div><div class="val" style="color:<?= $growth>=0?'#059669':'#dc2626' ?>;"><?= ($growth>=0?'+':'').number_format($growth,1) ?>%</div></div>
</div>
<div class="r-card">
    <div class="section-title">Grafik Tren 12 Bulan</div>
    <canvas id="tren-ro" style="max-height:300px;margin-bottom:1.5rem;"></canvas>
    <div class="section-title">Tabel Detail</div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm">
            <thead><tr><th>Bulan</th><th class="text-right">Pendapatan</th><th class="text-right">Pengeluaran</th><th class="text-right">Laba Bersih</th><th class="text-center">Margin</th><th class="text-center">Growth</th></tr></thead>
            <tbody>
            <?php $pp=0; foreach($tren_data as $idx=>$row):
                $lr=$row['total_pendapatan']-$row['total_pengeluaran'];
                $mr=$row['total_pendapatan']>0?($lr/$row['total_pendapatan'])*100:0;
                $gr=$pp>0?(($row['total_pendapatan']-$pp)/$pp)*100:0;
                $pp=$row['total_pendapatan'];
            ?>
            <tr>
                <td><strong><?= date('F Y', strtotime($row['bulan'].'-01')) ?></strong></td>
                <td class="text-right" style="color:#2563eb;">Rp <?= number_format($row['total_pendapatan'],0,',','.') ?></td>
                <td class="text-right" style="color:#dc2626;">Rp <?= number_format($row['total_pengeluaran'],0,',','.') ?></td>
                <td class="text-right" style="color:<?= $lr>=0?'#059669':'#dc2626' ?>;"><strong>Rp <?= number_format($lr,0,',','.') ?></strong></td>
                <td class="text-center"><span class="mbadge <?= $mr>=20?'mb-pos':($mr>=10?'mb-edc':'mb-neg') ?>"><?= number_format($mr,1) ?>%</span></td>
                <td class="text-center"><?php if($idx>0):?><span style="color:<?=$gr>=0?'#059669':'#dc2626';?>;font-weight:700;"><?= ($gr>=0?'▲ +':'▼ ').number_format($gr,1) ?>%</span><?php else:?>—<?php endif;?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot><tr>
                <td><strong>TOTAL</strong></td>
                <td class="text-right" style="color:#2563eb;"><strong>Rp <?= number_format($tot_p,0,',','.') ?></strong></td>
                <td class="text-right" style="color:#dc2626;"><strong>Rp <?= number_format($tot_g,0,',','.') ?></strong></td>
                <td class="text-right" style="color:<?=$tot_l>=0?'#059669':'#dc2626';?>;"><strong>Rp <?= number_format($tot_l,0,',','.') ?></strong></td>
                <td class="text-center"><strong><?= $tot_p>0?number_format(($tot_l/$tot_p)*100,1):0 ?>%</strong></td><td></td>
            </tr></tfoot>
        </table>
    </div>
</div>
<script>
$(document).ready(function(){
    var labels=<?= json_encode(array_map(function($r){return date('M Y',strtotime($r['bulan'].'-01'));}, $tren_data)) ?>;
    var pend=<?= json_encode(array_map(function($r){return (float)$r['total_pendapatan'];}, $tren_data)) ?>;
    var peng=<?= json_encode(array_map(function($r){return (float)$r['total_pengeluaran'];}, $tren_data)) ?>;
    var laba=pend.map(function(p,i){return p-peng[i];});
    new Chart(document.getElementById('tren-ro').getContext('2d'),{
        type:'bar',data:{labels:labels,datasets:[
            {label:'Pendapatan',data:pend,backgroundColor:'rgba(37,99,235,.7)',borderRadius:5,order:2},
            {label:'Pengeluaran',data:peng,backgroundColor:'rgba(220,38,38,.6)',borderRadius:5,order:2},
            {label:'Laba Bersih',data:laba,type:'line',borderColor:'#059669',backgroundColor:'rgba(5,150,105,.08)',borderWidth:2.5,pointRadius:5,tension:.4,fill:true,order:1}
        ]},
        options:{responsive:true,interaction:{mode:'index',intersect:false},
            plugins:{legend:{position:'top'},tooltip:{callbacks:{label:function(c){return c.dataset.label+': Rp '+c.raw.toLocaleString('id-ID');}}}},
            scales:{y:{ticks:{callback:function(v){return 'Rp '+(v/1000000).toFixed(0)+'jt';}}}}}
    });
});
</script>
