<?php function rp_pj($n){return 'Rp '.number_format((float)$n,0,',','.');} ?>
<style>



.filter-bar{position:relative;z-index:20;margin: 0 0 1rem;background:white;border-radius:18px;box-shadow:0 6px 24px rgba(0,0,0,.08);padding:1rem;}
.filter-bar label{font-size:.7rem;font-weight:700;text-transform:uppercase;color:#64748b;display:block;margin-bottom:4px;}
.filter-bar input,.filter-bar select{width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:.45rem .75rem;font-size:.85rem;background:#f8fafc;}
.filter-bar .filter-row{display:flex;gap:.5rem;}
.filter-bar .filter-row>div{flex:1;}
.btn-filter{display:block;width:100%;margin-top:.65rem;background:linear-gradient(135deg,#059669,#047857);color:white;border:none;padding:.55rem;border-radius:10px;font-size:.85rem;font-weight:700;cursor:pointer;}
.ppn-sum{background:linear-gradient(135deg,#059669,#047857);border-radius:18px;padding:1rem;margin-bottom:1rem;color:white;}
.ppn-sum-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:.5rem;}
.ppn-s-item{text-align:center;}
.ppn-s-val{font-size:.95rem;font-weight:900;display:block;}
.ppn-s-lbl{font-size:.58rem;font-weight:700;opacity:.8;text-transform:uppercase;}
.pajak-list{background:white;border-radius:18px;box-shadow:0 3px 14px rgba(0,0,0,.05);border:1px solid #f1f5f9;overflow:hidden;margin-bottom:1rem;}
.pajak-head{background:#f8fafc;padding:.75rem 1rem;border-bottom:1px solid #e2e8f0;font-size:.7rem;font-weight:800;color:#475569;text-transform:uppercase;}
.pajak-item{padding:.85rem 1rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:.75rem;}
.pajak-item:last-child{border-bottom:none;}
.pajak-dot{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#059669,#047857);display:flex;align-items:center;justify-content:center;color:white;font-size:.8rem;flex-shrink:0;}
.pajak-main{flex:1;}
.pajak-name{font-size:.82rem;font-weight:700;color:#1e293b;}
.pajak-sub{font-size:.68rem;color:#64748b;}
.pajak-right{text-align:right;}
.pajak-ppn{font-size:.85rem;font-weight:800;color:#059669;}
.pajak-dpp{font-size:.65rem;color:#64748b;}
</style>

<div class="app-container">

    <div class="filter-bar">
        <form method="GET" action="<?= base_url('Owner/rpt_pajak/'.$token) ?>">
            <input type="hidden" name="perusahaan_id" value="<?= $perusahaan_id ?>">
            <div class="filter-row">
                <div><label>Dari</label><input type="date" name="tgl_awal" value="<?= $tgl_awal ?>"></div>
                <div><label>Sampai</label><input type="date" name="tgl_akhir" value="<?= $tgl_akhir ?>"></div>
            </div>
            <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Tampilkan</button>
        </form>
    </div>

    <?php if($summary): ?>
    <div class="ppn-sum">
        <div class="ppn-sum-grid">
            <div class="ppn-s-item"><span class="ppn-s-val"><?= $summary->jumlah_transaksi ?></span><span class="ppn-s-lbl">Transaksi</span></div>
            <div class="ppn-s-item"><span class="ppn-s-val" style="font-size:.78rem;"><?= rp_pj($summary->total_dpp) ?></span><span class="ppn-s-lbl">Total DPP</span></div>
            <div class="ppn-s-item"><span class="ppn-s-val" style="font-size:.78rem;color:#fcd34d;"><?= rp_pj($summary->total_ppn) ?></span><span class="ppn-s-lbl">Total PPN</span></div>
        </div>
    </div>
    <?php endif; ?>

    <div class="pajak-list">
        <div class="pajak-head">📋 Detail Transaksi (<?= count($lap_pajak) ?>)</div>
        <?php if(empty($lap_pajak)): ?>
            <div style="text-align:center;padding:2rem;color:#94a3b8;"><i class="fas fa-inbox" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>Tidak ada transaksi</div>
        <?php else: ?>
            <?php foreach($lap_pajak as $pj): ?>
                <div class="pajak-item">
                    <div class="pajak-dot"><i class="fas fa-receipt"></i></div>
                    <div class="pajak-main">
                        <div class="pajak-name"><?= htmlspecialchars($pj->konsumen_nama ?? '-') ?></div>
                        <div class="pajak-sub"><?= $pj->nospk ?> · <?= date('d M Y', strtotime($pj->tgl)) ?> · <?= $pj->metode ?></div>
                    </div>
                    <div class="pajak-right">
                        <div class="pajak-ppn">PPN: <?= rp_pj($pj->ppn) ?></div>
                        <div class="pajak-dpp">DPP: <?= rp_pj($pj->dpp) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div style="height:1.5rem;"></div>
</div>

