<?php function rp_pi($n)
{
    return 'Rp ' . number_format((float) $n, 0, ',', '.');
} ?>
<style>
    .filter-bar {
        position: relative;
        z-index: 20;
        margin: 0 0 1rem;
        background: white;
        border-radius: 18px;
        box-shadow: 0 6px 24px rgba(0, 0, 0, .08);
        padding: 1rem;
    }

    .filter-bar label {
        font-size: .7rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        display: block;
        margin-bottom: 4px;
    }

    .filter-bar select {
        width: 100%;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: .45rem .75rem;
        font-size: .85rem;
        background: #f8fafc;
    }

    .btn-filter {
        display: block;
        width: 100%;
        margin-top: .65rem;
        background: linear-gradient(135deg, #f43f5e, #e11d48);
        color: white;
        border: none;
        padding: .55rem;
        border-radius: 10px;
        font-size: .85rem;
        font-weight: 700;
        cursor: pointer;
    }

    .sum-strip {
        background: linear-gradient(135deg, #f43f5e, #e11d48);
        border-radius: 18px;
        padding: 1rem;
        margin-bottom: 1rem;
        color: white;
    }

    .sum-strip .ss-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: .5rem;
    }

    .ss-item {
        text-align: center;
    }

    .ss-val {
        font-size: 1rem;
        font-weight: 900;
        display: block;
    }

    .ss-lbl {
        font-size: .58rem;
        font-weight: 700;
        opacity: .8;
        text-transform: uppercase;
    }

    .piutang-list {
        background: white;
        border-radius: 18px;
        box-shadow: 0 3px 14px rgba(0, 0, 0, .05);
        border: 1px solid #f1f5f9;
        overflow: hidden;
        margin-bottom: 1rem;
    }

    .piutang-head {
        background: #f8fafc;
        padding: .75rem 1rem;
        border-bottom: 1px solid #e2e8f0;
        font-size: .7rem;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
    }

    .piutang-item {
        padding: .85rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: .75rem;
    }

    .piutang-item:last-child {
        border-bottom: none;
    }

    .piutang-avatar {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: linear-gradient(135deg, #f43f5e, #e11d48);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: .85rem;
        font-weight: 700;
        flex-shrink: 0;
    }

    .piutang-main {
        flex: 1;
    }

    .piutang-name {
        font-size: .85rem;
        font-weight: 700;
        color: #1e293b;
    }

    .piutang-sub {
        font-size: .7rem;
        color: #64748b;
    }

    .piutang-right {
        text-align: right;
    }

    .piutang-sisa {
        font-size: .9rem;
        font-weight: 800;
        color: #ef4444;
    }

    .status-badge {
        display: inline-block;
        font-size: .6rem;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 20px;
        text-transform: uppercase;
    }

    .sb-lunas {
        background: #d1fae5;
        color: #059669;
    }

    .sb-belum {
        background: #fee2e2;
        color: #dc2626;
    }

    .sb-sebagian {
        background: #fef9c3;
        color: #d97706;
    }
</style>

<div class="app-container">

    <div class="filter-bar">
        <form method="GET" action="<?= base_url('Owner/rpt_piutang/' . $token) ?>">
            <input type="hidden" name="perusahaan_id" value="<?= $perusahaan_id ?>">
            <label>Status</label>
            <select name="status">
                <option value="">Semua</option>
                <option value="BELUM LUNAS" <?= $status_filter == 'BELUM LUNAS' ? 'selected' : '' ?>>Belum Lunas</option>
                <option value="SEBAGIAN" <?= $status_filter == 'SEBAGIAN' ? 'selected' : '' ?>>Sebagian Bayar</option>
                <option value="LUNAS" <?= $status_filter == 'LUNAS' ? 'selected' : '' ?>>Lunas</option>
            </select>
            <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Filter</button>
        </form>
    </div>

    <!-- Summary Strip -->
    <?php
    $sm = $piutang_summary;
    $total_sisa = $sm->total_sisa_piutang ?? 0;
    $belum_lunas = $sm->belum_lunas ?? 0;
    $sebagian = $sm->sebagian ?? 0;
    ?>
    <div class="sum-strip">
        <div class="ss-grid">
            <div class="ss-item"><span class="ss-val"><?= rp_pi($total_sisa) ?></span><span class="ss-lbl">Total
                    Piutang</span></div>
            <div class="ss-item"><span class="ss-val"><?= $belum_lunas ?></span><span class="ss-lbl">Belum Lunas</span>
            </div>
            <div class="ss-item"><span class="ss-val"><?= $sebagian ?></span><span class="ss-lbl">Sebagian</span></div>
        </div>
    </div>

    <!-- List -->
    <div class="piutang-list">
        <div class="piutang-head">📋 Daftar Piutang (<?= count($piutang_list) ?>)</div>
        <?php if (empty($piutang_list)): ?>
            <div style="text-align:center;padding:2rem;color:#94a3b8;"><i class="fas fa-check-circle"
                    style="font-size:2.5rem;color:#10b981;display:block;margin-bottom:.5rem;"></i>Tidak ada piutang!</div>
        <?php else: ?>
            <?php foreach ($piutang_list as $pi):
                $sisa = $pi->sisa_piutang;
                ?>
                <div class="piutang-item">
                    <div class="piutang-avatar"><?= strtoupper(substr($pi->konsumen_nama ?? '-', 0, 1)) ?></div>
                    <div class="piutang-main">
                        <div class="piutang-name"><?= htmlspecialchars($pi->konsumen_nama ?? '-') ?></div>
                        <div class="piutang-sub">
                            SPK: <?= $pi->nospk ?>
                            <?php if (!empty($pi->konsumen_nohp)): ?>
                                · <a href="https://wa.me/62<?= ltrim($pi->konsumen_nohp, '0') ?>?text=Halo+<?= urlencode($pi->konsumen_nama ?? '') ?>+tagihan+SPK+<?= urlencode($pi->nospk) ?>+sebesar+<?= urlencode(rp_pi($sisa)) ?>+mohon+segera+dilunasi."
                                    target="_blank" style="color:#25D366;"><i class="fab fa-whatsapp"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="piutang-right">
                        <div class="piutang-sisa"><?= rp_pi($sisa) ?></div>
                        <?php
                        $status_upper = strtoupper($pi->status);
                        if ($status_upper == 'LUNAS'): ?><span class="status-badge sb-lunas">Lunas</span>
                        <?php elseif ($status_upper == 'SEBAGIAN'): ?><span class="status-badge sb-sebagian">Sebagian</span>
                        <?php else: ?><span class="status-badge sb-belum">Belum</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div style="height:1.5rem;"></div>
</div>