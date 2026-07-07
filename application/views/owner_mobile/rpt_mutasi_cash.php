<?php function rp_mc($n){return 'Rp '.number_format((float)$n,0,',','.');} ?>
<style>



.filter-bar{position:relative;z-index:20;margin: 0 0 1rem;background:white;border-radius:18px;box-shadow:0 6px 24px rgba(0,0,0,.08);padding:1rem;}
.filter-bar label{font-size:.7rem;font-weight:700;text-transform:uppercase;color:#64748b;display:block;margin-bottom:4px;}
.filter-bar input{width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:.45rem .75rem;font-size:.85rem;background:#f8fafc;}
.filter-bar .filter-row{display:flex;gap:.5rem;}
.filter-bar .filter-row>div{flex:1;}
.btn-filter{display:block;width:100%;margin-top:.65rem;background:linear-gradient(135deg,#f59e0b,#d97706);color:white;border:none;padding:.55rem;border-radius:10px;font-size:.85rem;font-weight:700;cursor:pointer;}
.saldo-box{background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:18px;padding:1rem;margin-bottom:1rem;color:white;}
.sb-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:.5rem;text-align:center;}
.sb-val{font-size:.9rem;font-weight:900;display:block;}
.sb-lbl{font-size:.58rem;font-weight:700;opacity:.8;text-transform:uppercase;}
.mutasi-list{background:white;border-radius:18px;box-shadow:0 3px 14px rgba(0,0,0,.05);border:1px solid #f1f5f9;overflow:hidden;margin-bottom:1rem;}
.mutasi-head{background:#f8fafc;padding:.75rem 1rem;border-bottom:1px solid #e2e8f0;font-size:.7rem;font-weight:800;color:#475569;text-transform:uppercase;}
.mutasi-item{padding:.8rem 1rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:.75rem;}
.mutasi-item:last-child{border-bottom:none;}
.mt-badge{font-size:.58rem;font-weight:800;padding:2px 6px;border-radius:8px;text-transform:uppercase;flex-shrink:0;}
.mtb-transaksi{background:#d1fae5;color:#059669;}
.mtb-piutang{background:#e0e7ff;color:#4338ca;}
.mtb-pengeluaran{background:#fee2e2;color:#dc2626;}
.mt-dot{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;font-size:.9rem;flex-shrink:0;}
.mt-masuk{background:#f59e0b;}
.mt-keluar{background:#64748b;}
.mt-main{flex:1;}
.mt-ket{font-size:.8rem;font-weight:700;color:#1e293b;}
.mt-sub{font-size:.68rem;color:#64748b;}
.mt-val{font-size:.9rem;font-weight:800;text-align:right;}
.mt-masuk-val{color:#10b981;}
.mt-keluar-val{color:#ef4444;}
</style>

<div class="app-container">

    <div class="filter-bar">
        <form method="GET" action="<?= base_url('Owner/rpt_mutasi_cash/'.$token) ?>">
            <input type="hidden" name="perusahaan_id" value="<?= $perusahaan_id ?>">
            <div class="filter-row">
                <div><label>Dari</label><input type="date" name="tgl_awal" value="<?= $tgl_awal ?>"></div>
                <div><label>Sampai</label><input type="date" name="tgl_akhir" value="<?= $tgl_akhir ?>"></div>
            </div>
            <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Tampilkan</button>
        </form>
    </div>

    <?php
    $total_masuk_cash = array_sum(array_column(array_filter($mutasi, fn($m)=>$m['masuk']>0), 'masuk'));
    $total_keluar_cash = array_sum(array_column(array_filter($mutasi, fn($m)=>$m['keluar']>0), 'keluar'));
    $saldo_akhir = $saldo_awal + $total_masuk_cash - $total_keluar_cash;
    ?>
    <div class="saldo-box">
        <div class="sb-grid">
            <div><span class="sb-val"><?= rp_mc($saldo_awal) ?></span><span class="sb-lbl">Saldo Awal</span></div>
            <div><span class="sb-val"><?= rp_mc($total_masuk_cash) ?></span><span class="sb-lbl">Masuk</span></div>
            <div><span class="sb-val" style="color:<?= $saldo_akhir>=0?'#fcd34d':'#fca5a5' ?>;"><?= rp_mc($saldo_akhir) ?></span><span class="sb-lbl">Saldo Akhir</span></div>
        </div>
    </div>

    <div class="mutasi-list">
        <div class="mutasi-head">📋 Detail Mutasi (<?= count($mutasi) ?>)</div>
        <?php if(empty($mutasi)): ?>
            <div style="text-align:center;padding:2rem;color:#94a3b8;"><i class="fas fa-inbox" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>Tidak ada mutasi</div>
        <?php else: ?>
            <?php foreach($mutasi as $m): $isOut = $m['keluar'] > 0; ?>
                <div class="mutasi-item">
                    <div class="mt-dot <?= $isOut?'mt-keluar':'mt-masuk' ?>">
                        <i class="fas fa-<?= $isOut?'minus':'plus' ?>"></i>
                    </div>
                    <div class="mt-main">
                        <div class="mt-ket"><?= htmlspecialchars(substr($m['keterangan']??'-',0,45)) ?></div>
                        <div class="mt-sub">
                            <?= date('d M', strtotime($m['tanggal'])) ?> 
                            <span class="mt-badge <?= strtolower($m['sumber'])=='pengeluaran'?'mtb-pengeluaran':($m['sumber']=='PIUTANG'?'mtb-piutang':'mtb-transaksi') ?>">
                                <?= $m['sumber'] ?>
                            </span>
                        </div>
                    </div>
                    <div class="mt-val <?= $isOut?'mt-keluar-val':'mt-masuk-val' ?>"><?= ($isOut?'-':'+').rp_mc($isOut?$m['keluar']:$m['masuk']) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div style="height:1.5rem;"></div>
</div>

