<?php
// Aging piutang read-only content
$total_piutang_all = ($aging_summary->age_0_15??0)+($aging_summary->age_16_30??0)+($aging_summary->age_31_60??0)+($aging_summary->age_over_60??0);
?>
<div class="kpi-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
    <div class="kpi-card" style="border-left:4px solid #2563eb;">
        <div class="lbl">🟢 0–15 Hari (Aman)</div>
        <div class="val" style="color:#2563eb;">Rp <?= number_format($aging_summary->age_0_15??0,0,',','.') ?></div>
    </div>
    <div class="kpi-card" style="border-left:4px solid #059669;">
        <div class="lbl">🟡 16–30 Hari (Perhatian)</div>
        <div class="val" style="color:#059669;">Rp <?= number_format($aging_summary->age_16_30??0,0,',','.') ?></div>
    </div>
    <div class="kpi-card" style="border-left:4px solid #d97706;">
        <div class="lbl">🟠 31–60 Hari (Waspada)</div>
        <div class="val" style="color:#d97706;">Rp <?= number_format($aging_summary->age_31_60??0,0,',','.') ?></div>
    </div>
    <div class="kpi-card" style="border-left:4px solid #dc2626;">
        <div class="lbl">🔴 &gt;60 Hari (KRITIS)</div>
        <div class="val" style="color:#dc2626;">Rp <?= number_format($aging_summary->age_over_60??0,0,',','.') ?></div>
    </div>
    <div class="kpi-card" style="border-left:4px solid #6366f1;">
        <div class="lbl">Total Piutang Belum Lunas</div>
        <div class="val" style="color:#6366f1;">Rp <?= number_format($total_piutang_all,0,',','.') ?></div>
        <div class="sub"><?= count($aging_detail) ?> konsumen</div>
    </div>
</div>
<div class="r-card">
    <div class="section-title">Detail Piutang per Konsumen</div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm table-hover" id="tbl-aging-ro">
            <thead><tr>
                <th>No SPK</th><th>Konsumen</th><th>No HP</th>
                <th class="text-right">Total Tagihan</th><th class="text-right">Sisa Piutang</th>
                <th class="text-center">Umur (Hari)</th><th class="text-center">Tgl Transaksi</th><th class="text-center">Risiko</th>
            </tr></thead>
            <tbody>
            <?php foreach($aging_detail as $row):
                $sisa = $row->total_tagihan - $row->total_bayar;
                $umur = (int)$row->umur_hari;
                if($umur<=15){$r='Aman';$cl='mb-pos';}
                elseif($umur<=30){$r='Perhatian';$cl='mb-tf';}
                elseif($umur<=60){$r='Waspada';$cl='mb-edc';}
                else{$r='KRITIS';$cl='mb-neg';}
            ?>
            <tr>
                <td><code><?= $row->nospk ?></code></td>
                <td><strong><?= $row->konsumen_nama ?></strong></td>
                <td><?= $row->konsumen_nohp ?: '—' ?></td>
                <td class="text-right">Rp <?= number_format($row->total_tagihan,0,',','.') ?></td>
                <td class="text-right"><strong style="color:#dc2626;">Rp <?= number_format($sisa,0,',','.') ?></strong></td>
                <td class="text-center"><span style="font-weight:900;color:<?= $umur>60?'#dc2626':($umur>30?'#d97706':'#059669') ?>;"><?= $umur ?> hr</span></td>
                <td class="text-center"><?= date('d/m/Y', strtotime($row->tgl_bayar)) ?></td>
                <td class="text-center"><span class="mbadge <?= $cl ?>"><?= $r ?></span></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
$(document).ready(function(){
    $('#tbl-aging-ro').DataTable({pageLength:25,order:[[5,'desc']]});
});
</script>
