<?php
// KPI Strip
$tot = $grandtotal ?: 0;
?>
<div class="kpi-grid">
    <div class="kpi-card"><div class="lbl">Grand Total</div><div class="val" style="color:#6366f1;">Rp <?= number_format($tot,0,',','.') ?></div><div class="sub"><?= count($legger) ?> hari transaksi</div></div>
    <div class="kpi-card"><div class="lbl">Tunai</div><div class="val" style="color:#059669;">Rp <?= number_format($cash,0,',','.') ?></div><div class="sub"><?= $tot>0?number_format(($cash/$tot)*100,1):0 ?>%</div></div>
    <div class="kpi-card"><div class="lbl">Transfer Bank</div><div class="val" style="color:#2563eb;">Rp <?= number_format($transfer,0,',','.') ?></div><div class="sub"><?= $tot>0?number_format(($transfer/$tot)*100,1):0 ?>%</div></div>
    <div class="kpi-card"><div class="lbl">EDC/Debit</div><div class="val" style="color:#d97706;">Rp <?= number_format($edc,0,',','.') ?></div><div class="sub"><?= $tot>0?number_format(($edc/$tot)*100,1):0 ?>%</div></div>
    <div class="kpi-card"><div class="lbl">E-Wallet</div><div class="val" style="color:#7c3aed;">Rp <?= number_format($ewallet,0,',','.') ?></div><div class="sub"><?= $tot>0?number_format(($ewallet/$tot)*100,1):0 ?>%</div></div>
    <div class="kpi-card"><div class="lbl">Piutang</div><div class="val" style="color:#dc2626;">Rp <?= number_format($piutang,0,',','.') ?></div><div class="sub"><?= $tot>0?number_format(($piutang/$tot)*100,1):0 ?>%</div></div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="r-card" style="padding:1.5rem;">
            <div class="section-title">Komposisi Metode Bayar</div>
            <canvas id="pie-ro" style="max-height:220px;"></canvas>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="r-card">
            <div class="section-title">Rekapitulasi Kas Harian</div>
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="tbl-ro-legger">
                    <thead><tr>
                        <th>Tanggal</th><th class="text-right">Tunai</th><th class="text-right">Transfer</th>
                        <th class="text-right">EDC</th><th class="text-right">E-Wallet</th>
                        <th class="text-right">Piutang</th><th class="text-right">Total</th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($legger as $row): ?>
                    <tr>
                        <td><strong><?= date('d M Y', strtotime($row->tgl_bayar)) ?></strong><br><small class="text-muted"><?= date('l', strtotime($row->tgl_bayar)) ?></small></td>
                        <td class="text-right"><span class="mbadge mb-cash">Rp <?= number_format($row->total_cash,0,',','.') ?></span></td>
                        <td class="text-right"><span class="mbadge mb-tf">Rp <?= number_format($row->total_transfer,0,',','.') ?></span></td>
                        <td class="text-right"><span class="mbadge mb-edc">Rp <?= number_format($row->total_edc,0,',','.') ?></span></td>
                        <td class="text-right"><span class="mbadge mb-ew">Rp <?= number_format($row->total_ewallet,0,',','.') ?></span></td>
                        <td class="text-right"><span class="mbadge mb-piu">Rp <?= number_format($row->total_piutang,0,',','.') ?></span></td>
                        <td class="text-right"><strong style="color:#6366f1;">Rp <?= number_format($row->total_harian,0,',','.') ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot><tr>
                        <td><strong>TOTAL</strong></td>
                        <td class="text-right" style="color:#059669;"><strong>Rp <?= number_format($cash,0,',','.') ?></strong></td>
                        <td class="text-right" style="color:#2563eb;"><strong>Rp <?= number_format($transfer,0,',','.') ?></strong></td>
                        <td class="text-right" style="color:#d97706;"><strong>Rp <?= number_format($edc,0,',','.') ?></strong></td>
                        <td class="text-right" style="color:#7c3aed;"><strong>Rp <?= number_format($ewallet,0,',','.') ?></strong></td>
                        <td class="text-right" style="color:#dc2626;"><strong>Rp <?= number_format($piutang,0,',','.') ?></strong></td>
                        <td class="text-right"><strong style="color:#6366f1;font-size:.95rem;">Rp <?= number_format($tot,0,',','.') ?></strong></td>
                    </tr></tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function(){
    $('#tbl-ro-legger').DataTable({pageLength:31,order:[[0,'asc']],dom:'rtip'});
    new Chart(document.getElementById('pie-ro').getContext('2d'),{
        type:'doughnut',
        data:{labels:['Tunai','Transfer','EDC','E-Wallet','Piutang'],
            datasets:[{data:[<?= $cash ?>,<?= $transfer ?>,<?= $edc ?>,<?= $ewallet ?>,<?= $piutang ?>],
                backgroundColor:['#059669','#2563eb','#d97706','#7c3aed','#dc2626'],borderWidth:3,borderColor:'#fff',hoverOffset:10}]},
        options:{responsive:true,cutout:'70%',plugins:{legend:{position:'bottom',labels:{usePointStyle:true,padding:16}},
            tooltip:{callbacks:{label:function(c){var t=c.dataset.data.reduce(function(a,b){return a+b;},0);return c.label+': Rp '+c.raw.toLocaleString('id-ID')+' ('+(t>0?((c.raw/t)*100).toFixed(1):0)+'%)';}}}}}
    });
});
</script>
