<?php function rp_u($n){return 'Rp '.number_format((float)$n,0,',','.');} ?>
<style>



.filter-bar{position:relative;z-index:20;margin: 0 0 1rem;background:white;border-radius:18px;box-shadow:0 6px 24px rgba(0,0,0,.08);padding:1rem;}
.filter-bar label{font-size:.7rem;font-weight:700;text-transform:uppercase;color:#64748b;display:block;margin-bottom:4px;}
.filter-bar input{width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:.45rem .75rem;font-size:.85rem;background:#f8fafc;}
.btn-filter{display:block;width:100%;margin-top:.65rem;background:linear-gradient(135deg,#00c0ef,#0099bb);color:white;border:none;padding:.55rem;border-radius:10px;font-size:.85rem;font-weight:700;cursor:pointer;}
.method-card{background:white;border-radius:18px;box-shadow:0 3px 14px rgba(0,0,0,.05);border:1px solid #f1f5f9;padding:1rem;margin-bottom:.75rem;}
.method-header{display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem;}
.method-icon{width:42px;height:42px;border-radius:13px;display:flex;align-items:center;justify-content:center;color:white;font-size:1rem;}
.method-name{font-size:.9rem;font-weight:800;color:#1e293b;flex:1;}
.method-total{font-size:1rem;font-weight:900;color:#1e293b;}
.method-bar{background:#f1f5f9;border-radius:20px;height:8px;overflow:hidden;margin-bottom:.5rem;}
.method-bar-fill{height:8px;border-radius:20px;}
.method-pct{font-size:.7rem;color:#64748b;font-weight:700;}
.method-details{margin-top:.75rem;border-top:1px solid #f1f5f9;padding-top:.65rem;}
.detail-row{display:flex;justify-content:space-between;font-size:.78rem;margin-bottom:.3rem;}
.detail-name{color:#475569;}
.detail-amount{font-weight:700;color:#1e293b;}
.grand-card{background:linear-gradient(135deg,#00c0ef,#0099bb);border-radius:18px;padding:1.1rem;margin-bottom:1rem;color:white;text-align:center;}
.grand-label{font-size:.75rem;font-weight:700;opacity:.8;text-transform:uppercase;letter-spacing:.5px;}
.grand-val{font-size:1.5rem;font-weight:900;margin:.2rem 0;}
</style>

<div class="app-container">

    <div class="filter-bar">
        <form method="GET" action="<?= base_url('Owner/rpt_rekap_metode/'.$token) ?>">
            <input type="hidden" name="perusahaan_id" value="<?= $perusahaan_id ?>">
            <label>Tanggal</label>
            <input type="date" name="tanggal" value="<?= $hariini ?>">
            <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Tampilkan</button>
        </form>
    </div>

    <div class="grand-card">
        <div class="grand-label">Total Penerimaan</div>
        <div class="grand-val"><?= rp_u($payment_methods['total_all']) ?></div>
        <div class="grand-label"><?= count($pemasukan) ?> transaksi</div>
    </div>

    <?php
    $metode_ui = [
        'cash'     => ['label'=>'Tunai (Cash)',   'icon'=>'fas fa-money-bill-wave', 'color'=>'#10b981'],
        'transfer' => ['label'=>'Transfer Bank',  'icon'=>'fas fa-university',      'color'=>'#6366f1'],
        'edc'      => ['label'=>'EDC / Debit',    'icon'=>'fas fa-credit-card',     'color'=>'#f59e0b'],
        'ewallet'  => ['label'=>'E-Wallet',       'icon'=>'fas fa-mobile-alt',      'color'=>'#8b5cf6'],
        'lainnya'  => ['label'=>'Lainnya',        'icon'=>'fas fa-ellipsis-h',      'color'=>'#64748b'],
    ];
    foreach ($metode_ui as $key => $cfg):
        $pm = $payment_methods[$key] ?? ['total'=>0,'percentage'=>0,'details'=>[]];
        if ($pm['total'] <= 0) continue;
    ?>
    <div class="method-card">
        <div class="method-header">
            <div class="method-icon" style="background:<?= $cfg['color'] ?>;"><i class="<?= $cfg['icon'] ?>"></i></div>
            <div class="method-name"><?= $cfg['label'] ?></div>
            <div class="method-total"><?= rp_u($pm['total']) ?></div>
        </div>
        <div class="method-bar"><div class="method-bar-fill" style="width:<?= $pm['percentage'] ?>%;background:<?= $cfg['color'] ?>;"></div></div>
        <div class="method-pct"><?= number_format($pm['percentage'], 1) ?>% dari total penerimaan</div>
        <?php if (!empty($pm['details'])): ?>
        <div class="method-details">
            <?php foreach ($pm['details'] as $d): ?>
                <div class="detail-row">
                    <span class="detail-name"><?= htmlspecialchars($d['nama']) ?></span>
                    <span class="detail-amount"><?= rp_u($d['total']) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <div style="height:1.5rem;"></div>
</div>

