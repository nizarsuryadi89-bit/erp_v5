<?php function rp_ew($n){return 'Rp '.number_format((float)$n,0,',','.');} ?>
<style>



.filter-bar{position:relative;z-index:20;margin: 0 0 1rem;background:white;border-radius:18px;box-shadow:0 6px 24px rgba(0,0,0,.08);padding:1rem;}
.filter-bar label{font-size:.7rem;font-weight:700;text-transform:uppercase;color:#64748b;display:block;margin-bottom:4px;}
.filter-bar input,.filter-bar select{width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:.45rem .75rem;font-size:.85rem;background:#f8fafc;}
.filter-bar .filter-row{display:flex;gap:.5rem;}
.filter-bar .filter-row>div{flex:1;}
.btn-filter{display:block;width:100%;margin-top:.65rem;background:linear-gradient(135deg,#8b5cf6,#7c3aed);color:white;border:none;padding:.55rem;border-radius:10px;font-size:.85rem;font-weight:700;cursor:pointer;}
.mutasi-sum{display:grid;grid-template-columns:repeat(3,1fr);gap:.5rem;margin-bottom:1rem;}
.ms-card{background:white;border-radius:14px;padding:.8rem;box-shadow:0 3px 12px rgba(0,0,0,.05);border:1px solid #f1f5f9;text-align:center;}
.ms-lbl{font-size:.6rem;font-weight:700;text-transform:uppercase;color:#64748b;margin-bottom:4px;}
.ms-val{font-size:.85rem;font-weight:800;}
.mutasi-list{background:white;border-radius:18px;box-shadow:0 3px 14px rgba(0,0,0,.05);border:1px solid #f1f5f9;overflow:hidden;margin-bottom:1rem;}
.mutasi-head{background:#f8fafc;padding:.75rem 1rem;border-bottom:1px solid #e2e8f0;font-size:.7rem;font-weight:800;color:#475569;text-transform:uppercase;}
.mutasi-item{padding:.8rem 1rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:.75rem;}
.mutasi-item:last-child{border-bottom:none;}
.mt-dot{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;font-size:.9rem;flex-shrink:0;}
.mt-masuk{background:#8b5cf6;}
.mt-keluar{background:#64748b;}
.mt-main{flex:1;}
.mt-name{font-size:.82rem;font-weight:700;color:#1e293b;}
.mt-sub{font-size:.68rem;color:#64748b;}
.mt-val{font-size:.9rem;font-weight:800;text-align:right;}
.mt-masuk-val{color:#10b981;}
.mt-keluar-val{color:#ef4444;}
.placeholder-hint{background:linear-gradient(135deg,#f5f3ff,#ede9fe);border-radius:14px;padding:1rem;margin-bottom:1rem;border:1px solid #ddd6fe;text-align:center;}
</style>

<div class="app-container">

    <div class="filter-bar">
        <form method="GET" action="<?= base_url('Owner/rpt_mutasi_ewallet/'.$token) ?>">
            <input type="hidden" name="perusahaan_id" value="<?= $perusahaan_id ?>">
            <div class="filter-row">
                <div><label>E-Wallet</label>
                    <select name="ewallet_id">
                        <option value="">-- Pilih E-Wallet --</option>
                        <?php foreach($ewallet_list as $ew): ?>
                            <option value="<?= $ew->id ?>" <?= $selected_ewallet==$ew->id?'selected':'' ?>><?= $ew->ewallet_nama ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="filter-row" style="margin-top:.5rem;">
                <div><label>Dari</label><input type="date" name="start_date" value="<?= $start_date ?>"></div>
                <div><label>Sampai</label><input type="date" name="end_date" value="<?= $end_date ?>"></div>
            </div>
            <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Tampilkan</button>
        </form>
    </div>

    <?php if(!$selected_ewallet): ?>
    <div class="placeholder-hint">
        <i class="fas fa-mobile-alt" style="font-size:1.5rem;color:#7c3aed;display:block;margin-bottom:.5rem;"></i>
        <div style="font-size:.82rem;color:#5b21b6;font-weight:600;">Pilih e-wallet di filter atas untuk melihat mutasi</div>
    </div>
    <?php else: ?>

    <div class="mutasi-sum">
        <div class="ms-card"><div class="ms-lbl">Masuk</div><div class="ms-val" style="color:#10b981;"><?= rp_ew($total_masuk) ?></div></div>
        <div class="ms-card"><div class="ms-lbl">Keluar</div><div class="ms-val" style="color:#ef4444;"><?= rp_ew($total_keluar) ?></div></div>
        <div class="ms-card"><div class="ms-lbl">Net</div><div class="ms-val" style="color:<?= ($total_masuk-$total_keluar)>=0?'#10b981':'#ef4444' ?>;"><?= rp_ew($total_masuk-$total_keluar) ?></div></div>
    </div>

    <div class="mutasi-list">
        <div class="mutasi-head">📋 Riwayat Mutasi (<?= count($mutasi) ?>)</div>
        <?php if(empty($mutasi)): ?>
            <div style="text-align:center;padding:2rem;color:#94a3b8;"><i class="fas fa-inbox" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>Tidak ada mutasi</div>
        <?php else: ?>
            <?php foreach($mutasi as $m): $isOut = $m['arus']=='keluar'; ?>
                <div class="mutasi-item">
                    <div class="mt-dot <?= $isOut?'mt-keluar':'mt-masuk' ?>">
                        <i class="fas fa-<?= $isOut?'arrow-up':'arrow-down' ?>"></i>
                    </div>
                    <div class="mt-main">
                        <div class="mt-name"><?= htmlspecialchars($m['konsumen']??'-') ?></div>
                        <div class="mt-sub"><?= $m['nospk']??'' ?> · <?= date('d M Y', strtotime($m['tanggal'])) ?> · <?= ucfirst($m['tipe']??'') ?></div>
                    </div>
                    <div class="mt-val <?= $isOut?'mt-keluar-val':'mt-masuk-val' ?>"><?= ($isOut?'-':'+').rp_ew($m['jumlah']) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div style="height:1.5rem;"></div>
</div>

