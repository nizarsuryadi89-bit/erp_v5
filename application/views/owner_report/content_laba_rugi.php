<?php
// Laba Rugi read-only owner view
$lr          = $laba_rugi ?? null;
$pend        = (float)($lr->total_pendapatan ?? 0);
$hpp         = (float)($lr->total_hpp ?? 0);
$laba_kotor  = (float)($lr->laba ?? 0);
$biaya_ops   = (float)($pengeluaran ?? 0);
$laba_bersih = (float)($laba_bersih ?? ($laba_kotor - $biaya_ops));
$margin_kotor  = $pend > 0 ? ($laba_kotor / $pend) * 100 : 0;
$margin_bersih = $pend > 0 ? ($laba_bersih / $pend) * 100 : 0;
$bulan_label = date('F Y', strtotime(($params['bulan'] ?? date('Y-m')).'-01'));
?>

<div class="kpi-grid" style="grid-template-columns:repeat(auto-fit,minmax(190px,1fr));">
    <div class="kpi-card" style="border-left:4px solid #2563eb;">
        <div class="lbl">Total Pendapatan</div>
        <div class="val" style="color:#2563eb;">Rp <?= number_format($pend,0,',','.') ?></div>
        <div class="sub"><?= $bulan_label ?></div>
    </div>
    <div class="kpi-card" style="border-left:4px solid #dc2626;">
        <div class="lbl">HPP / Harga Pokok</div>
        <div class="val" style="color:#dc2626;">Rp <?= number_format($hpp,0,',','.') ?></div>
    </div>
    <div class="kpi-card" style="border-left:4px solid #059669;">
        <div class="lbl">Laba Kotor</div>
        <div class="val" style="color:#059669;">Rp <?= number_format($laba_kotor,0,',','.') ?></div>
        <div class="sub">Margin <?= number_format($margin_kotor,1) ?>%</div>
    </div>
    <div class="kpi-card" style="border-left:4px solid #d97706;">
        <div class="lbl">Biaya Operasional</div>
        <div class="val" style="color:#d97706;">Rp <?= number_format($biaya_ops,0,',','.') ?></div>
    </div>
    <div class="kpi-card" style="border-left:4px solid <?= $laba_bersih>=0?'#6366f1':'#dc2626' ?>;">
        <div class="lbl">Laba Bersih</div>
        <div class="val" style="color:<?= $laba_bersih>=0?'#6366f1':'#dc2626' ?>;font-size:1.3rem;">Rp <?= number_format(abs($laba_bersih),0,',','.') ?></div>
        <div class="sub"><?= $laba_bersih>=0?'✅ Profit':'⚠️ Rugi' ?> · Margin <?= number_format($margin_bersih,1) ?>%</div>
    </div>
</div>

<div class="r-card">
    <div class="section-title">Laporan Laba Rugi — <?= $bulan_label ?></div>
    <table class="table table-sm" style="max-width:600px;">
        <tbody>
            <tr style="background:#f8fafc;">
                <td colspan="2" style="font-weight:800;font-size:.8rem;text-transform:uppercase;color:#64748b;letter-spacing:.5px;padding:.75rem 1rem;">PENDAPATAN</td>
            </tr>
            <tr>
                <td style="padding:.6rem 1.25rem;">Penjualan Bersih</td>
                <td class="text-right" style="color:#2563eb;font-weight:700;">Rp <?= number_format($pend,0,',','.') ?></td>
            </tr>
            <tr style="background:#f8fafc;">
                <td colspan="2" style="font-weight:800;font-size:.8rem;text-transform:uppercase;color:#64748b;letter-spacing:.5px;padding:.75rem 1rem;">HARGA POKOK PENJUALAN</td>
            </tr>
            <tr>
                <td style="padding:.6rem 1.25rem;">HPP Produk</td>
                <td class="text-right" style="color:#dc2626;font-weight:700;">(Rp <?= number_format($hpp,0,',','.') ?>)</td>
            </tr>
            <tr style="border-top:2px solid #e2e8f0;">
                <td style="padding:.75rem 1.25rem;font-weight:900;">LABA KOTOR</td>
                <td class="text-right" style="font-weight:900;font-size:1.05rem;color:#059669;">Rp <?= number_format($laba_kotor,0,',','.') ?></td>
            </tr>
            <tr style="background:#f8fafc;">
                <td colspan="2" style="font-weight:800;font-size:.8rem;text-transform:uppercase;color:#64748b;letter-spacing:.5px;padding:.75rem 1rem;">BIAYA OPERASIONAL</td>
            </tr>
            <tr>
                <td style="padding:.6rem 1.25rem;">Pengeluaran Operasional</td>
                <td class="text-right" style="color:#d97706;font-weight:700;">(Rp <?= number_format($biaya_ops,0,',','.') ?>)</td>
            </tr>
            <tr style="border-top:3px solid #1e293b;background:<?= $laba_bersih>=0?'#ecfdf5':'#fff1f2' ?>;">
                <td style="padding:1rem 1.25rem;font-weight:900;font-size:1rem;">LABA BERSIH <?= $laba_bersih>=0?'(PROFIT)':'(RUGI)' ?></td>
                <td class="text-right" style="font-weight:900;font-size:1.2rem;color:<?= $laba_bersih>=0?'#059669':'#dc2626' ?>;">
                    <?= $laba_bersih<0?'(' : '' ?>Rp <?= number_format(abs($laba_bersih),0,',','.') ?><?= $laba_bersih<0?')' : '' ?>
                </td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top:1.5rem;padding-top:1.25rem;border-top:1px dashed #e2e8f0;">
        <div style="font-size:.78rem;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:#64748b;margin-bottom:.75rem;">Analisis Margin</div>
        <div style="display:flex;gap:1.5rem;flex-wrap:wrap;">
            <div>
                <div style="font-size:.72rem;color:#64748b;margin-bottom:3px;">Margin Kotor</div>
                <div style="width:180px;background:#f1f5f9;border-radius:50px;height:10px;overflow:hidden;">
                    <div style="width:<?= min($margin_kotor,100) ?>%;height:10px;border-radius:50px;background:#059669;"></div>
                </div>
                <div style="font-weight:900;color:#059669;font-size:.88rem;margin-top:3px;"><?= number_format($margin_kotor,1) ?>%</div>
            </div>
            <div>
                <div style="font-size:.72rem;color:#64748b;margin-bottom:3px;">Margin Bersih</div>
                <div style="width:180px;background:#f1f5f9;border-radius:50px;height:10px;overflow:hidden;">
                    <div style="width:<?= min(abs($margin_bersih),100) ?>%;height:10px;border-radius:50px;background:<?= $margin_bersih>=0?'#6366f1':'#dc2626' ?>;"></div>
                </div>
                <div style="font-weight:900;color:<?= $margin_bersih>=0?'#6366f1':'#dc2626' ?>;font-size:.88rem;margin-top:3px;"><?= number_format($margin_bersih,1) ?>%</div>
            </div>
        </div>
    </div>
</div>
