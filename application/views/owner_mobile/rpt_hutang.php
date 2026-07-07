<?php function rp_ht($n){return 'Rp '.number_format((float)$n,0,',','.');} ?>
<style>



.sum-strip{background:linear-gradient(135deg,#7c3aed,#5b21b6);border-radius:18px;padding:1rem;margin:1.5rem 0 1rem;color:white;}
.sum-strip .ss-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:.5rem;}
.ss-item{text-align:center;}
.ss-val{font-size:.95rem;font-weight:900;display:block;}
.ss-lbl{font-size:.58rem;font-weight:700;opacity:.8;text-transform:uppercase;}
.hutang-list{background:white;border-radius:18px;box-shadow:0 3px 14px rgba(0,0,0,.05);border:1px solid #f1f5f9;overflow:hidden;margin-bottom:1rem;}
.hutang-head{background:#f8fafc;padding:.75rem 1rem;border-bottom:1px solid #e2e8f0;font-size:.7rem;font-weight:800;color:#475569;text-transform:uppercase;}
.hutang-item{padding:.85rem 1rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:.75rem;}
.hutang-item:last-child{border-bottom:none;}
.hutang-avatar{width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#7c3aed,#5b21b6);display:flex;align-items:center;justify-content:center;color:white;font-size:.85rem;font-weight:700;flex-shrink:0;}
.hutang-main{flex:1;}
.hutang-name{font-size:.85rem;font-weight:700;color:#1e293b;}
.hutang-sub{font-size:.7rem;color:#64748b;}
.hutang-right{text-align:right;}
.hutang-sisa{font-size:.9rem;font-weight:800;color:#7c3aed;}
.status-badge{display:inline-block;font-size:.6rem;font-weight:700;padding:2px 7px;border-radius:20px;text-transform:uppercase;}
.sb-lunas{background:#d1fae5;color:#059669;}
.sb-belum{background:#ede9fe;color:#6d28d9;}
.sb-jatuh{background:#fee2e2;color:#dc2626;}
</style>

<div class="app-container">

    <div class="sum-strip">
        <div class="ss-grid">
            <div class="ss-item"><span class="ss-val"><?= rp_ht($total_sisa) ?></span><span class="ss-lbl">Total Sisa</span></div>
            <div class="ss-item"><span class="ss-val"><?= rp_ht($total_hutang) ?></span><span class="ss-lbl">Total Tagihan</span></div>
            <div class="ss-item"><span class="ss-val" style="color:#fcd34d;"><?= $jatuh_tempo_count ?></span><span class="ss-lbl">Jatuh Tempo</span></div>
        </div>
    </div>

    <div class="hutang-list">
        <div class="hutang-head">📋 Daftar Hutang Supplier (<?= count($tampilData) ?>)</div>
        <?php if(empty($tampilData)): ?>
            <div style="text-align:center;padding:2rem;color:#94a3b8;"><i class="fas fa-check-circle" style="font-size:2.5rem;color:#10b981;display:block;margin-bottom:.5rem;"></i>Tidak ada hutang supplier!</div>
        <?php else: ?>
            <?php foreach($tampilData as $h):
                $sisa = ($h->total_tagihan ?? 0) - ($h->total_bayar ?? 0);
            ?>
            <div class="hutang-item">
                <div class="hutang-avatar"><?= strtoupper(substr($h->supplier_nama??$h->nama_supplier??'-',0,1)) ?></div>
                <div class="hutang-main">
                    <div class="hutang-name"><?= htmlspecialchars($h->supplier_nama ?? $h->nama_supplier ?? '-') ?></div>
                    <div class="hutang-sub">
                        <?= date('d M Y', strtotime($h->tanggal_hutang ?? $h->tgl_beli ?? date('Y-m-d'))) ?>
                        <?php if(!empty($h->jatuh_tempo)): ?>
                        · JT: <span style="color:<?= strtotime($h->jatuh_tempo)<time()?'#dc2626':'#475569' ?>;"><?= date('d M Y', strtotime($h->jatuh_tempo)) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="hutang-right">
                    <div class="hutang-sisa"><?= rp_ht($sisa) ?></div>
                    <?php if ($h->status == 'lunas'): ?>
                        <span class="status-badge sb-lunas">Lunas</span>
                    <?php elseif (isset($h->jatuh_tempo) && strtotime($h->jatuh_tempo) < time()): ?>
                        <span class="status-badge sb-jatuh">Jatuh Tempo</span>
                    <?php else: ?>
                        <span class="status-badge sb-belum">Aktif</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div style="height:1.5rem;"></div>
</div>

