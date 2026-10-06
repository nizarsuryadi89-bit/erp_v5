<div class="app-content complaint-shell">
<?php $this->load->view('admin/complaint/_nav', get_defined_vars()); ?>
<div class="complaint-card mb-4">
<h2 class="h4 mb-2">Impor chat WhatsApp</h2><p class="complaint-muted">Di WhatsApp, ekspor percakapan tanpa media. Unggah file .txt atau tempel teksnya di bawah. Maksimal 2 MB dan 500 pesan per impor.</p>
<form action="<?= complaint_e(site_url('complaint/preview')) ?>" method="post" enctype="multipart/form-data" class="row g-3">
<?php complaint_form_token($complaint_csrf); ?>
<div class="col-md-8"><label for="conversation">Nama / ID percakapan</label><input class="form-control" id="conversation" name="conversation" required maxlength="191" placeholder="Contoh: Operasional TGM Cabang 1"><div class="form-text">Gunakan nama yang sama untuk setiap impor dari percakapan ini agar duplikat dikenali.</div></div>
<div class="col-md-4"><label for="date_order">Urutan tanggal ekspor</label><select class="form-select" id="date_order" name="date_order"><option value="DMY">Tanggal / Bulan / Tahun</option><option value="MDY">Bulan / Tanggal / Tahun</option></select></div>
<div class="col-12"><label for="chat_file">File ekspor .txt</label><input type="file" accept=".txt,text/plain" class="form-control" id="chat_file" name="chat_file"></div>
<div class="col-12"><label for="chat_text">Atau tempel chat</label><textarea class="form-control font-monospace" rows="6" id="chat_text" name="chat_text" placeholder="06/10/2026, 14.32 - Operator A3: Mesin hasil cetaknya bergaris. SPK 261006001 tertunda."></textarea></div>
<div class="col-12"><button class="btn btn-primary">Periksa sebelum impor</button></div>
</form></div>
<?php if (isset($preview)): ?>
<div class="complaint-card">
<h2 class="h5">Pratinjau <?= count($preview['parsed']['messages']) ?> pesan · <?= complaint_e($preview['conversation']) ?></h2><p class="small complaint-muted">Menampilkan maksimal 25 pesan pertama. <?= (int) $preview['parsed']['system_messages'] ?> pesan sistem dilewati. Klasifikasi aturan adalah saran awal; semua tiket dari chat memerlukan tinjauan.</p>
<div class="table-responsive"><table class="table"><thead><tr><th>Waktu / pengirim</th><th>Pesan</th><th>Hasil awal</th></tr></thead><tbody>
<?php foreach ($preview_rows as $row): ?><tr><td><?= complaint_e($row['sent_at']) ?><br><?= complaint_e($row['sender']) ?></td><td style="max-width:650px"><div style="white-space:pre-wrap;overflow-wrap:anywhere"><?= complaint_e($row['body']) ?></div></td><td><?= complaint_e($row['analysis']['classification']) ?><br><span class="small complaint-muted"><?= complaint_e($row['analysis']['category']) ?></span></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php
$canonical = '';
foreach ($preview['parsed']['messages'] as $message) {
    $date = new DateTimeImmutable($message['sent_at']);
    $canonical .= $date->format($preview['date_order'] === 'DMY' ? 'd/m/Y, H:i:s' : 'm/d/Y, H:i:s').' - '.$message['sender'].': '.$message['body']."\n";
}
?>
<form method="post" action="<?= complaint_e(site_url('complaint/import_save')) ?>">
<?php complaint_form_token($complaint_csrf); ?>
<input type="hidden" name="conversation" value="<?= complaint_e($preview['conversation']) ?>"><input type="hidden" name="date_order" value="<?= complaint_e($preview['date_order']) ?>">
<textarea name="chat_text" hidden><?= complaint_e($canonical) ?></textarea>
<button class="btn btn-success">Impor <?= count($preview['parsed']['messages']) ?> pesan dan proses offline</button>
</form></div>
<?php endif; ?>
</div>
