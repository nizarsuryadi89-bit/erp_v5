<div class="app-content complaint-shell">
<?php $this->load->view('admin/complaint/_nav', get_defined_vars()); ?>
<div class="complaint-card">
<div class="d-flex flex-wrap justify-content-between gap-3 align-items-center mb-4"><div><h2 class="h4 mb-1">Kotak Masuk WhatsApp</h2><div class="complaint-muted small"><?= (int) $inbox['total'] ?> pesan · <?= (int) $inbox['pending'] ?> menunggu pemrosesan</div></div>
<?php if ($complaint_manager && empty($is_readonly)): ?><form action="<?= complaint_e(site_url('complaint/process')) ?>" method="post"><?php complaint_form_token($complaint_csrf); ?><button class="btn btn-primary">Proses antrean offline (50 pesan)</button></form><?php endif; ?>
</div>
<p class="small complaint-muted">Hanya complaint/trouble yang otomatis menjadi tiket. Follow-up bisa ditautkan ke tiket yang sama; pesan yang terlewat dapat dijadikan tiket secara manual. Status penyelesaian tetap diperbarui oleh PIC.</p>
<?php foreach ($inbox['rows'] as $message): ?>
<article class="border rounded-3 p-3 mb-3"><div class="d-flex flex-wrap justify-content-between gap-2 mb-2"><div><strong><?= complaint_e($message['sender']) ?></strong><span class="complaint-muted small ms-2"><?= complaint_e($message['conversation']) ?> · <?= complaint_e($message['sent_at']) ?></span></div><span class="badge text-bg-light"><?= complaint_e($message['state']) ?> · <?= complaint_e($message['classification'] ?: 'Belum dianalisis') ?></span></div>
<details><summary class="small text-primary">Lihat pesan asli</summary><pre class="complaint-source mt-2"><?= complaint_e($message['body']) ?></pre></details>
<?php if ($message['summary']): ?><p class="mb-1 mt-2" style="white-space:pre-wrap;overflow-wrap:anywhere"><?= complaint_e($message['summary']) ?></p><div class="small complaint-muted"><?= complaint_e($message['category']) ?> · <?= complaint_e($message['priority']) ?> · <?= $message['analysis_method'] === 'openai' ? 'Analisis AI' : 'Klasifikasi aturan / tinjauan manual' ?></div><?php endif; ?>
<?php if ($message['analysis_error']): ?><div class="small text-warning mt-2"><?= complaint_e($message['analysis_error']) ?></div><?php endif; ?>
<?php if ($message['ticket_id']): ?><a class="btn btn-outline-primary btn-sm mt-3" href="<?= complaint_e(site_url('complaint/detail/'.$message['ticket_id'])) ?>"><?= complaint_e($message['ticket_no']) ?></a>
<?php elseif ($complaint_manager && empty($is_readonly) && $message['state'] !== 'processing'): ?>
<form method="post" action="<?= complaint_e(site_url('complaint/link')) ?>" class="d-flex flex-wrap gap-2 align-items-center mt-3">
<?php complaint_form_token($complaint_csrf); ?><input type="hidden" name="message_id" value="<?= (int) $message['id'] ?>"><input class="form-control form-control-sm" style="max-width:230px" type="number" min="1" name="ticket_id" aria-label="ID tiket tujuan" placeholder="ID tiket yang sudah ada"><button class="btn btn-outline-primary btn-sm">Tautkan ke tiket / buat tiket baru</button><span class="small complaint-muted">Kosongkan ID untuk membuat tiket baru.</span>
</form>
<?php if ($message['state'] === 'failed'): ?><form method="post" action="<?= complaint_e(site_url('complaint/retry')) ?>" class="mt-2"><?php complaint_form_token($complaint_csrf); ?><input type="hidden" name="message_id" value="<?= (int) $message['id'] ?>"><button class="btn btn-outline-warning btn-sm">Antrekan ulang</button></form><?php endif; ?>
<?php endif; ?></article>
<?php endforeach; ?>
<?php if (!$inbox['rows']): ?><div class="text-center py-5 complaint-muted"><i class="bi bi-whatsapp fs-1 d-block mb-2"></i>Belum ada pesan. Mulai dengan ekspor percakapan WhatsApp lalu impor file .txt.</div><?php endif; ?>
<?php complaint_pagination('complaint/inbox', $inbox); ?>
</div></div>
