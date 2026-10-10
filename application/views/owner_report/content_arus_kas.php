<?php // arus_kas read-only ?>
<div style="background:<?= $net_arus_kas>=0?'linear-gradient(135deg,#059669,#047857)':'linear-gradient(135deg,#dc2626,#b91c1c)' ?>;color:white;border-radius:20px;padding:1.75rem 2rem;margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;">
    <div>
        <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;opacity:.8;">Net Arus Kas Bersih</div>
        <div style="font-size:2rem;font-weight:900;">Rp <?= number_format(abs($net_arus_kas),0,',','.') ?></div>
        <div style="font-size:.85rem;opacity:.8;"><?= $net_arus_kas>=0?'✅ Arus Kas Positif':'⚠️ Arus Kas Negatif — Perlu Perhatian' ?></div>
    </div>
    <div style="font-size:4rem;opacity:.2;"><i class="fa fa-<?= $net_arus_kas>=0?'arrow-up':'arrow-down' ?>"></i></div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="r-card" style="background:linear-gradient(135deg,#f0fdf4,white);border-left:4px solid #059669;">
            <div class="section-title" style="--accent:#059669;--accent-dark:#047857;">💰 Arus Kas Masuk</div>
            <?php $items=[['Tunai',$masuk_tunai],['Transfer Bank',$masuk_transfer],['EDC/Debit',$masuk_debit],['E-Wallet',$masuk_ewallet]];
            foreach($items as [$l,$v]):?>
            <div style="display:flex;justify-content:space-between;padding:.6rem 0;border-bottom:1px dashed #d1fae5;font-size:.88rem;">
                <span><?= $l ?></span><strong style="color:#059669;">Rp <?= number_format($v,0,',','.') ?></strong>
            </div>
            <?php endforeach; ?>
            <div style="display:flex;justify-content:space-between;padding:.75rem 0 0;font-size:1rem;">
                <strong>TOTAL MASUK</strong><strong style="color:#059669;font-size:1.1rem;">Rp <?= number_format($total_masuk,0,',','.') ?></strong>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="r-card" style="background:linear-gradient(135deg,#fff1f2,white);border-left:4px solid #dc2626;">
            <div class="section-title" style="--accent:#dc2626;--accent-dark:#b91c1c;">📤 Arus Kas Keluar</div>
            <div style="display:flex;justify-content:space-between;padding:.6rem 0;border-bottom:1px dashed #fecdd3;font-size:.88rem;">
                <span>Total Pengeluaran</span><strong style="color:#dc2626;">Rp <?= number_format($total_keluar,0,',','.') ?></strong>
            </div>
            <div style="display:flex;justify-content:space-between;padding:.75rem 0 0;font-size:1rem;">
                <strong>TOTAL KELUAR</strong><strong style="color:#dc2626;font-size:1.1rem;">Rp <?= number_format($total_keluar,0,',','.') ?></strong>
            </div>
        </div>
    </div>
</div>
