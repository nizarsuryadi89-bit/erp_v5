<?php function rp_x($n){return 'Rp '.number_format((float)$n,0,',','.');} ?>
<style>



.aging-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:.75rem;margin-bottom:1rem;}
.age-card{border-radius:16px;padding:1rem;text-align:center;box-shadow:0 3px 14px rgba(0,0,0,.07);}
.age-card .a-label{font-size:.6rem;font-weight:800;text-transform:uppercase;margin-bottom:4px;}
.age-card .a-val{font-size:1rem;font-weight:900;}
.age-0{background:#d1fae5;color:#065f46;} .age-0 .a-val{color:#059669;}
.age-1{background:#fef9c3;color:#713f12;} .age-1 .a-val{color:#d97706;}
.age-2{background:#fed7aa;color:#7c2d12;} .age-2 .a-val{color:#ea580c;}
.age-3{background:#fee2e2;color:#7f1d1d;} .age-3 .a-val{color:#dc2626;}
.aging-detail{background:white;border-radius:18px;box-shadow:0 3px 14px rgba(0,0,0,.05);border:1px solid #f1f5f9;overflow:hidden;margin-bottom:1rem;}
.aging-head{background:#f8fafc;padding:.75rem 1rem;border-bottom:1px solid #e2e8f0;font-size:.7rem;font-weight:800;color:#475569;text-transform:uppercase;}
.aging-item{padding:.85rem 1rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:.75rem;}
.aging-item:last-child{border-bottom:none;}
.age-badge{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:900;flex-shrink:0;color:white;}
.age-safe{background:#10b981;}
.age-warn{background:#f59e0b;}
.age-risk{background:#ef4444;}
.age-crit{background:#7f1d1d;animation:pulse 1.5s infinite;}
@keyframes pulse{0%,100%{opacity:1;}50%{opacity:.7;}}
.aging-main{flex:1;}
.aging-name{font-size:.85rem;font-weight:700;color:#1e293b;}
.aging-sub{font-size:.7rem;color:#64748b;}
.aging-right{text-align:right;}
.aging-sisa{font-size:.9rem;font-weight:800;}
</style>

<div class="app-container">

    <div style="height:1.5rem;"></div>

    <!-- Summary Grid -->
    <div class="aging-grid">
        <div class="age-card age-0">
            <div class="a-label">0–15 Hari <span style="font-size:.5rem;">(AMAN)</span></div>
            <div class="a-val"><?= rp_x($aging_summary->age_0_15 ?? 0) ?></div>
        </div>
        <div class="age-card age-1">
            <div class="a-label">16–30 Hari <span style="font-size:.5rem;">(PERHATIAN)</span></div>
            <div class="a-val"><?= rp_x($aging_summary->age_16_30 ?? 0) ?></div>
        </div>
        <div class="age-card age-2">
            <div class="a-label">31–60 Hari <span style="font-size:.5rem;">(WASPADA)</span></div>
            <div class="a-val"><?= rp_x($aging_summary->age_31_60 ?? 0) ?></div>
        </div>
        <div class="age-card age-3">
            <div class="a-label">60+ Hari <span style="font-size:.5rem;">(KRITIS)</span></div>
            <div class="a-val"><?= rp_x($aging_summary->age_over_60 ?? 0) ?></div>
        </div>
    </div>

    <!-- Detail List -->
    <div class="aging-detail">
        <div class="aging-head">📋 Detail Piutang Belum Lunas (<?= count($aging_detail) ?>)</div>
        <?php if(empty($aging_detail)): ?>
            <div style="text-align:center;padding:2rem;color:#94a3b8;"><i class="fas fa-check-circle" style="font-size:2.5rem;color:#10b981;display:block;margin-bottom:.5rem;"></i>Tidak ada piutang outstanding!</div>
        <?php else: ?>
            <?php foreach($aging_detail as $d):
                $umur = (int)$d->umur_hari;
                $sisa = $d->total_tagihan - $d->total_bayar;
                if ($umur<=15) { $cls='age-safe'; $risiko='Aman'; }
                elseif ($umur<=30) { $cls='age-warn'; $risiko='Perhatian'; }
                elseif ($umur<=60) { $cls='age-risk'; $risiko='Waspada'; }
                else { $cls='age-crit'; $risiko='KRITIS'; }
            ?>
            <div class="aging-item">
                <div class="age-badge <?= $cls ?>"><?= $umur ?>hr</div>
                <div class="aging-main">
                    <div class="aging-name"><?= htmlspecialchars($d->konsumen_nama ?? '-') ?></div>
                    <div class="aging-sub"><?= $d->nospk ?> · <?= date('d M Y', strtotime($d->tgl_bayar)) ?>
                        <?php if(!empty($d->konsumen_nohp)): ?>
                            <br><a href="https://wa.me/62<?= ltrim($d->konsumen_nohp,'0') ?>?text=Halo+<?= urlencode($d->konsumen_nama??'') ?>+tagihan+SPK+<?= urlencode($d->nospk) ?>+sebesar+<?= urlencode(rp_x($sisa)) ?>+mohon+segera+dilunasi." target="_blank" style="color:#25D366;font-weight:700;"><i class="fab fa-whatsapp"></i> Ingatkan</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="aging-right">
                    <div class="aging-sisa" style="color:<?= $umur>60?'#dc2626':'#ef4444' ?>;"><?= rp_x($sisa) ?></div>
                    <div style="font-size:.6rem;color:#94a3b8;font-weight:700;"><?= $risiko ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div style="height:1.5rem;"></div>
</div>

