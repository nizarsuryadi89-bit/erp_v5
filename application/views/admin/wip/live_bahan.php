<?php $esc = function ($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }; ?>
<div class="app-content mt-3" id="material-live" data-feed-url="<?= site_url('wip/ajax_live_bahan') ?>" data-detail-url="<?= site_url('wip/control_center/') ?>">
    <div class="live-material-card">
        <div class="live-material-header">
            <div><div class="live-material-eyebrow"><span class="live-material-dot"></span> MONITOR PRODUKSI</div>
                <h3>Live Penggunaan Material</h3><p>Pengurangan bahan aktual dari proses produksi, diperbarui setiap 5 detik.</p></div>
            <a href="<?= site_url('wip/konsumsi_bahan') ?>" class="btn btn-outline-light rounded-pill">Rekap konsumsi bahan</a>
        </div>
        <div class="p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
                <div class="d-flex align-items-center gap-2"><span id="live-status" class="badge bg-success">LIVE</span><span id="live-updated" class="small text-muted" aria-live="polite">Memuat data…</span></div>
                <div class="d-flex gap-2"><button id="live-pause" class="btn btn-sm btn-outline-secondary" type="button">Jeda otomatis</button>
                    <button id="live-refresh" class="btn btn-sm btn-success" type="button"><i class="fas fa-sync-alt me-1" aria-hidden="true"></i>Perbarui sekarang</button></div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-4"><div class="live-material-stat"><span>Mutasi pengurangan</span><strong id="live-total"><?= (int)$live_feed['total'] ?></strong></div></div>
                <div class="col-md-4"><div class="live-material-stat"><span>SPK terkait</span><strong id="live-spk-count"><?= (int)$live_feed['spk_count'] ?></strong></div></div>
                <div class="col-md-4"><div class="live-material-stat"><span>Varian material</span><strong id="live-material-count"><?= (int)$live_feed['material_count'] ?></strong></div></div>
            </div>
            <form id="live-filters" class="row g-3 align-items-end mb-3">
                <div class="col-md-3"><label for="live-date" class="form-label">Tanggal penggunaan</label><div class="input-group">
                    <input id="live-date" class="form-control" type="date" name="date" value="<?= $esc($live_feed['filters']['date']) ?>" required><button id="live-today" class="btn btn-outline-secondary" type="button">Hari ini</button></div></div>
                <div class="col-md-3"><label for="live-warehouse" class="form-label">Gudang produksi</label><select id="live-warehouse" class="form-select" name="warehouse">
                    <?php if ($live_admin): ?><option value="0" <?= !$live_feed['filters']['warehouse'] ? 'selected' : '' ?>>Semua gudang</option><?php endif; ?>
                    <?php foreach ($live_warehouses as $warehouse): ?><option value="<?= (int)$warehouse['gudang_id'] ?>" <?= (int)$live_feed['filters']['warehouse'] === (int)$warehouse['gudang_id'] ? 'selected' : '' ?>><?= $esc($warehouse['gudang_lokasi']) ?></option><?php endforeach; ?>
                </select></div>
                <div class="col-md-4"><label for="live-search" class="form-label">Cari material, varian, SPK, atau produk</label><input id="live-search" name="search" class="form-control" maxlength="100" value="<?= $esc($live_feed['filters']['search']) ?>" placeholder="Contoh: Tumbler Sakura atau nomor SPK"></div>
                <div class="col-md-2"><button class="btn btn-dark w-100" type="submit">Terapkan filter</button></div>
            </form>
            <p class="small text-muted mb-3">Qty Produksi menunjukkan <strong>bahan terpakai dalam satuan produksi</strong> (pcs, lembar, meter, dan lainnya). Data muncul setelah pengurangan stok berhasil dicatat. Mutasi terbaru tampil di atas.</p>
            <div id="live-error" class="alert alert-warning d-none" role="alert"></div>
            <div class="table-responsive live-material-table"><table class="table align-middle mb-0">
                <thead><tr><th>Nama Material</th><th>Varian</th><th class="text-end">Qty Produksi</th><th>No SPK</th><th>Nama Produk Produksi</th></tr></thead>
                <tbody id="live-rows">
                    <?php foreach ($live_feed['rows'] as $row): ?><tr data-log-id="<?= (int)$row['log_id'] ?>">
                        <td><div class="fw-bold"><?= $esc($row['material_name'] ?: ('Material #' . $row['material_id'])) ?></div><div class="small text-muted"><?= $esc($row['warehouse_name'] ?: ('Gudang #' . $row['gudang_id'])) ?></div></td>
                        <td><?= $esc($row['variant'] ?: '—') ?></td><td class="text-end"><strong><?= $esc(rtrim(rtrim(number_format($row['production_qty'], 4, ',', '.'), '0'), ',')) ?></strong><span class="small text-muted ms-1"><?= $esc($row['unit'] ?: 'unit') ?></span></td>
                        <td><?php if ($row['nospk']): ?><a href="<?= site_url('wip/control_center/' . (int)$row['produksi_id']) ?>" target="_blank" rel="noopener"><?= $esc($row['nospk']) ?></a><?php else: ?><span class="text-muted">Referensi produksi #<?= (int)$row['produksi_id'] ?></span><?php endif; ?><div class="small text-muted"><?= $esc($row['created_at']) ?> WIB</div></td>
                        <td><?= $esc($row['product_name'] ?: 'Produk tidak tersedia') ?></td>
                    </tr><?php endforeach; ?>
                    <?php if (!$live_feed['rows']): ?><tr><td colspan="5" class="text-center text-muted py-5">Belum ada pengurangan bahan produksi untuk filter ini.</td></tr><?php endif; ?>
                </tbody>
            </table></div>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3"><span id="live-pagination-label" class="small text-muted"></span>
                <div class="d-flex gap-2"><button id="live-prev" class="btn btn-sm btn-outline-secondary" type="button">Sebelumnya</button><button id="live-next" class="btn btn-sm btn-outline-secondary" type="button">Berikutnya</button></div></div>
        </div>
    </div>
    <script id="live-initial" type="application/json"><?= json_encode($live_feed, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
</div>
<style>
#material-live .live-material-card{background:#fff;border:1px solid #e4e9ef;border-radius:20px;overflow:hidden;box-shadow:0 8px 28px rgba(15,23,42,.05)}
#material-live .live-material-header{background:linear-gradient(115deg,#142d36,#1c4949);color:#fff;padding:28px 32px;display:flex;align-items:center;justify-content:space-between;gap:18px;flex-wrap:wrap}
#material-live .live-material-header h3{margin:8px 0;font-weight:700}#material-live .live-material-header p{margin:0;color:#cfdfdf}
#material-live .live-material-eyebrow{font-size:11px;letter-spacing:1.6px;font-weight:700;color:#b6ead2;display:flex;align-items:center;gap:8px}
#material-live .live-material-dot{width:8px;height:8px;border-radius:50%;background:#58dc9d;box-shadow:0 0 0 4px rgba(88,220,157,.12)}
#material-live .live-material-stat{border:1px solid #e6ebee;background:#f8fafb;border-radius:12px;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;gap:12px}
#material-live .live-material-stat span{color:#687887;font-size:13px}#material-live .live-material-stat strong{font-size:25px;color:#173f40}
#material-live .live-material-table{border:1px solid #e7ecf0;border-radius:12px}#material-live th{background:#f6f8fa;color:#627182;font-size:12px;padding:16px;white-space:nowrap}
#material-live td{padding:16px;font-size:14px;min-width:125px}#material-live td:first-child,#material-live td:last-child{min-width:220px}
#material-live .live-material-new{animation:live-material-flash 3s ease-out}@keyframes live-material-flash{from{background:#dff7eb}to{background:transparent}}
@media(prefers-reduced-motion:reduce){#material-live .live-material-new{animation:none;background:#eefaf3}}
</style>
<script src="<?= base_url('assets/js/material_live.js?v=' . filemtime(FCPATH . 'assets/js/material_live.js')) ?>" defer></script>
