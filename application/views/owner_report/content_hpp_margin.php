<?php
// HPP & Margin read-only owner view
$tot_pend = 0; $tot_hpp = 0; $tot_mgn = 0;
if (!empty($hpp_data)) {
    $tot_pend = array_sum(array_map(function($r){ return (float)$r->total_pendapatan; }, $hpp_data));
    $tot_hpp  = array_sum(array_map(function($r){ return (float)$r->total_hpp; },       $hpp_data));
    $tot_mgn  = array_sum(array_map(function($r){ return (float)$r->total_margin; },    $hpp_data));
}
$avg_mgn = $tot_pend > 0 ? ($tot_mgn / $tot_pend) * 100 : 0;
?>

<div class="kpi-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
    <div class="kpi-card" style="border-left:4px solid #2563eb;">
        <div class="lbl">Total Pendapatan</div>
        <div class="val" style="color:#2563eb;">Rp <?= number_format($tot_pend,0,',','.') ?></div>
        <div class="sub">Bulan: <?= date('F Y', strtotime(($params['bulan'] ?? date('Y-m')).'-01')) ?></div>
    </div>
    <div class="kpi-card" style="border-left:4px solid #dc2626;">
        <div class="lbl">Total HPP</div>
        <div class="val" style="color:#dc2626;">Rp <?= number_format($tot_hpp,0,',','.') ?></div>
    </div>
    <div class="kpi-card" style="border-left:4px solid #059669;">
        <div class="lbl">Total Margin Kotor</div>
        <div class="val" style="color:#059669;">Rp <?= number_format($tot_mgn,0,',','.') ?></div>
    </div>
    <div class="kpi-card" style="border-left:4px solid <?= $avg_mgn>=20?'#059669':($avg_mgn>=10?'#d97706':'#dc2626') ?>;">
        <div class="lbl">Rata-rata Margin</div>
        <div class="val" style="color:<?= $avg_mgn>=20?'#059669':($avg_mgn>=10?'#d97706':'#dc2626') ?>;"><?= number_format($avg_mgn,1) ?>%</div>
        <div class="sub"><?= $avg_mgn>=20?'Margin Sehat':($avg_mgn>=10?'Perlu Perhatian':'Margin Rendah') ?></div>
    </div>
</div>

<div class="r-card">
    <div class="section-title">Ranking Profitabilitas Produk — <?= date('F Y', strtotime(($params['bulan'] ?? date('Y-m')).'-01')) ?></div>

    <?php if (empty($hpp_data)): ?>
    <div style="text-align:center;padding:2rem;color:#64748b;">
        <i class="fa fa-inbox" style="font-size:2rem;margin-bottom:.75rem;display:block;"></i>
        Tidak ada data HPP untuk periode ini.<br>
        <small>Pastikan HPP produk sudah diisi di master produk.</small>
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-bordered table-sm table-hover" id="tbl-ro-hpp">
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th>Nama Produk</th>
                    <th class="text-center">Qty Terjual</th>
                    <th class="text-right">Pendapatan</th>
                    <th class="text-right">HPP</th>
                    <th class="text-right">Margin Kotor</th>
                    <th class="text-center" style="min-width:130px;">Margin %</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($hpp_data as $idx => $row):
                $m = (float)($row->avg_margin_pct ?? 0);
                $color = $m >= 30 ? '#059669' : ($m >= 15 ? '#d97706' : '#dc2626');
            ?>
            <tr>
                <td class="text-center"><strong><?= $idx+1 ?></strong></td>
                <td><strong><?= $row->product_nama ?></strong></td>
                <td class="text-center"><?= number_format($row->total_qty,0,',','.') ?></td>
                <td class="text-right" style="color:#2563eb;">Rp <?= number_format($row->total_pendapatan,0,',','.') ?></td>
                <td class="text-right" style="color:#dc2626;">Rp <?= number_format($row->total_hpp,0,',','.') ?></td>
                <td class="text-right"><strong style="color:#059669;">Rp <?= number_format($row->total_margin,0,',','.') ?></strong></td>
                <td class="text-center">
                    <span class="mbadge <?= $m>=20?'mb-pos':($m>=10?'mb-edc':'mb-neg') ?>"><?= number_format($m,1) ?>%</span>
                    <div style="width:100%;background:#f1f5f9;border-radius:50px;height:6px;margin-top:4px;">
                        <div style="width:<?= min($m,100) ?>%;height:6px;border-radius:50px;background:<?= $color ?>;"></div>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"><strong>TOTAL</strong></td>
                    <td class="text-right" style="color:#2563eb;"><strong>Rp <?= number_format($tot_pend,0,',','.') ?></strong></td>
                    <td class="text-right" style="color:#dc2626;"><strong>Rp <?= number_format($tot_hpp,0,',','.') ?></strong></td>
                    <td class="text-right" style="color:#059669;"><strong>Rp <?= number_format($tot_mgn,0,',','.') ?></strong></td>
                    <td class="text-center"><strong><?= number_format($avg_mgn,1) ?>%</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php endif; ?>
</div>

<script>
$(document).ready(function(){
    $('#tbl-ro-hpp').DataTable({pageLength:25,order:[[5,'desc']],dom:'rtip'});
});
</script>
