<?php
$esc = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
$labels = ['pending' => 'Menunggu QC', 'hold' => 'Ditahan / Perlu tindak lanjut', 'passed' => 'Lulus QC', 'reprint' => 'Permintaan reprint', 'all' => 'Semua'];
?>
<div class="container-fluid py-4" id="quality-app" data-save-url="<?= site_url('wip/quality_inspect') ?>" data-history-url="<?= site_url('wip/quality_history') ?>">
    <div class="d-flex flex-wrap justify-content-between gap-3 align-items-center mb-3">
        <div><h3 class="mb-1">Pemeriksaan QC</h3><p class="text-muted mb-0">Periksa hasil akhir sebelum barang di PB diserahkan ke konsumen.</p></div>
        <a class="btn btn-outline-primary" href="<?= site_url('wip/produksi') ?>">Kembali ke produksi</a>
    </div>
    <?php if ($qc_error): ?><div class="alert alert-danger"><?= $esc($qc_error) ?></div><?php endif; ?>
    <?php if (!$qc_enabled): ?><div class="alert alert-warning">Pendaftaran QC otomatis belum aktif. Pemeriksaan yang sudah terdaftar tetap berlaku.</div><?php endif; ?>
    <div class="alert alert-light border small">
        Pekerjaan yang selesai setelah QC diaktifkan masuk ke antrean ini. Pekerjaan lama yang sudah selesai tetap mengikuti alur sebelumnya.
        <?php if (!$qc_can_review): ?><br>Hasil pemeriksaan hanya dapat disimpan oleh admin atau petugas QC yang ditunjuk.<?php endif; ?>
    </div>
    <div class="d-flex flex-wrap gap-2 mb-3">
        <?php foreach ($labels as $key => $label): ?>
            <a class="btn btn-sm <?= $key === $qc_status ? 'btn-primary' : 'btn-outline-secondary' ?>" href="<?= site_url('wip/quality') . '?' . $esc(http_build_query(['status' => $key, 'search' => $qc_search])) ?>"><?= $esc($label) ?></a>
        <?php endforeach; ?>
    </div>
    <form method="get" class="d-flex gap-2 mb-3">
        <input type="hidden" name="status" value="<?= $esc($qc_status) ?>">
        <input class="form-control" name="search" value="<?= $esc($qc_search) ?>" maxlength="100" placeholder="Cari nomor SPK" aria-label="Cari nomor SPK">
        <button class="btn btn-primary" type="submit">Cari</button>
    </form>
    <?php if ($qc_status === 'reprint'): ?><div class="alert alert-info small">Permintaan reprint berikut terhubung ke riwayat pemeriksaan. Permintaan belum membuat pekerjaan produksi baru. Buka riwayat untuk melihat jumlah dan alasan.</div><?php endif; ?>
    <div class="card border-0 shadow-sm"><div class="table-responsive">
        <table class="table align-middle mb-0"><thead><tr><th>SPK / Produk</th><th>Jumlah diperiksa</th><th>Status QC</th><th>Masuk QC</th><th>Tindakan</th></tr></thead><tbody>
        <?php foreach ($qc_queue['rows'] as $row): ?>
            <tr>
                <td><a href="<?= site_url('wip/control_center/' . (int) $row->produksi_id) ?>"><?= $esc($row->nospk) ?></a><div class="text-muted small"><?= $esc($row->product_nama) ?></div></td>
                <td><?= $esc((float) $row->target_qty) ?></td>
                <td><span class="badge <?= $row->status === 'passed' ? 'bg-success' : ($row->status === 'hold' ? 'bg-danger' : 'bg-warning text-dark') ?>"><?= $esc($labels[$row->status] ?? $row->status) ?></span></td>
                <td class="small"><?= $esc($row->created_at) ?></td>
                <td>
                    <?php if ($qc_can_review && in_array($row->status, ['pending', 'hold'], true) && in_array($row->status_produksi, ['done', 'on_pb'], true)): ?>
                        <button type="button" class="btn btn-sm btn-primary qc-inspect" data-job="<?= $esc(json_encode(['produksi_id' => $row->produksi_id, 'version' => $row->version, 'target' => (float) $row->target_qty, 'status' => $row->status, 'nospk' => $row->nospk, 'request_key' => bin2hex(random_bytes(16))])) ?>"><?= $row->status === 'hold' ? 'Periksa ulang' : 'Periksa hasil' ?></button>
                    <?php endif; ?>
                    <button type="button" class="btn btn-sm btn-outline-secondary qc-history" data-id="<?= (int) $row->produksi_id ?>">Riwayat</button>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$qc_queue['rows']): ?><tr><td colspan="5" class="text-center text-muted py-5">Belum ada item pada antrean ini.</td></tr><?php endif; ?>
        </tbody></table>
    </div></div>
    <div class="d-flex gap-3 align-items-center mt-3"><span class="small text-muted"><?= (int) $qc_queue['total'] ?> item · halaman <?= (int) $qc_page ?></span>
        <?php foreach ([$qc_page - 1 => 'Sebelumnya', $qc_page + 1 => 'Berikutnya'] as $page => $label): if ($page < 1 || ($page > $qc_page && $qc_page * 30 >= $qc_queue['total'])) continue; ?>
            <a class="btn btn-sm btn-light" href="<?= site_url('wip/quality') . '?' . $esc(http_build_query(['status' => $qc_status, 'search' => $qc_search, 'page' => $page])) ?>"><?= $label ?></a>
        <?php endforeach; ?>
    </div>

    <dialog id="qc-dialog" class="border-0 rounded-3 p-4 shadow" style="width:min(620px,95vw)">
        <form id="qc-form">
            <h4>Pemeriksaan <span id="qc-job-name"></span></h4>
            <p class="text-muted">Jumlah yang diperiksa: <strong id="qc-target"></strong>. Isi hasil seluruh jumlah ini; pemeriksaan ulang tidak menambah hasil produksi.</p>
            <input type="hidden" name="produksi_id"><input type="hidden" name="version"><input type="hidden" name="request_key">
            <input type="hidden" name="wip_qc_csrf" value="<?= $esc($qc_csrf) ?>">
            <div class="row g-3"><div class="col-6"><label for="qc-accepted" class="form-label">Diterima QC</label><input id="qc-accepted" name="accepted_qty" class="form-control" type="number" min="0" step="0.0001" required></div>
                <div class="col-6"><label for="qc-rejected" class="form-label">Ditolak QC</label><input id="qc-rejected" name="rejected_qty" class="form-control" type="number" min="0" step="0.0001" required></div></div>
            <label for="qc-note" class="form-label mt-3">Catatan / alasan penolakan / tindak lanjut</label><textarea id="qc-note" name="note" class="form-control" maxlength="4000" rows="3"></textarea>
            <div class="form-check mt-3"><input id="qc-reprint" name="request_reprint" class="form-check-input" type="checkbox" value="1"><label for="qc-reprint" class="form-check-label">Catat permintaan reprint untuk jumlah yang ditolak</label></div>
            <p class="small text-muted mt-2">Permintaan terhubung ke item asal. Riwayat produksi tetap tersimpan.</p>
            <div id="qc-form-message" class="alert alert-danger d-none" role="alert"></div>
            <div class="d-flex justify-content-end gap-2 mt-3"><button type="button" class="btn btn-light qc-close">Batal</button><button type="submit" class="btn btn-primary" id="qc-save">Simpan hasil QC</button></div>
        </form>
    </dialog>
    <dialog id="qc-history-dialog" class="border-0 rounded-3 p-4 shadow" style="width:min(820px,95vw)"><div class="d-flex justify-content-between"><h4>Riwayat pemeriksaan</h4><button class="btn btn-light qc-close" type="button">Tutup</button></div><div id="qc-history-content" class="mt-3"></div></dialog>
</div>
<style>dialog::backdrop{background:rgba(0,0,0,.45)} #quality-app dialog{max-height:90vh;overflow:auto}</style>
<script src="<?= base_url('assets/js/wip_quality.js?v=' . (file_exists(FCPATH . 'assets/js/wip_quality.js') ? filemtime(FCPATH . 'assets/js/wip_quality.js') : '1.0')) ?>"></script>
