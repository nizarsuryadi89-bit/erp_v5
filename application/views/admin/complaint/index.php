<div class="app-content complaint-shell">
<?php $this->load->view('admin/complaint/_nav', get_defined_vars()); ?>
<div class="row g-3 mb-4">
<?php $cards = array('Tiket periode ini' => $report['total'], 'Belum ada PIC' => $report['unassigned'], 'Sedang ditangani' => $report['status']['ASSIGNED'] + $report['status']['ON_PROGRESS'], 'Selesai / ditutup' => $report['status']['RESOLVED'] + $report['status']['CLOSED'], 'Perlu tinjauan' => $report['review'], 'Kritis' => $report['critical']); foreach ($cards as $label => $value): ?>
<div class="col-6 col-lg-2"><div class="complaint-card h-100"><div class="complaint-muted small mb-2"><?= complaint_e($label) ?></div><div class="complaint-kpi"><?= (int) $value ?></div></div></div>
<?php endforeach; ?>
</div>
<div class="complaint-card mb-4">
<form method="get" action="<?= complaint_e(site_url('complaint')) ?>" class="row g-3 align-items-end">
    <div class="col-6 col-lg-2"><label for="from">Tanggal awal</label><input class="form-control" type="date" id="from" name="from" value="<?= complaint_e($filters['from']) ?>" required></div>
    <div class="col-6 col-lg-2"><label for="to">Tanggal akhir</label><input class="form-control" type="date" id="to" name="to" value="<?= complaint_e($filters['to']) ?>" required></div>
    <?php foreach (array('status' => array('Status', $statuses), 'priority' => array('Prioritas', $priorities), 'kategori' => array('Kategori', $categories)) as $key => $field): ?>
    <div class="col-6 col-lg-2"><label for="<?= complaint_e($key) ?>"><?= complaint_e($field[0]) ?></label><select class="form-select" id="<?= complaint_e($key) ?>" name="<?= complaint_e($key) ?>"><option value="">Semua</option><?php foreach ($field[1] as $option): ?><option <?= $filters[$key] === $option ? 'selected' : '' ?> value="<?= complaint_e($option) ?>"><?= complaint_e($option) ?></option><?php endforeach; ?></select></div>
    <?php endforeach; ?>
    <div class="col-lg-2"><button class="btn btn-primary w-100">Tampilkan</button></div>
    <div class="col-lg-8"><input aria-label="Cari tiket" name="q" class="form-control" maxlength="100" placeholder="Cari nomor tiket, SPK, judul, atau pelapor…" value="<?= complaint_e($filters['q']) ?>"></div>
    <div class="col-lg-4"><label class="d-flex align-items-center gap-2 mb-0"><input type="checkbox" class="form-check-input mt-0" name="review" value="1" <?= $filters['review'] ? 'checked' : '' ?>>Hanya tiket yang perlu ditinjau</label></div>
</form>
<p class="small complaint-muted mb-0 mt-3">Angka ringkasan mengikuti tanggal laporan masuk. Status menunjukkan kondisi saat ini; daftar tiket juga mengikuti filter tambahan.</p>
</div>
<div class="complaint-card">
<div class="d-flex justify-content-between mb-3"><h2 class="h5 mb-0">Daftar tiket</h2><span class="complaint-muted small"><?= (int) $listing['total'] ?> tiket</span></div>
<div class="table-responsive"><table class="table table-hover"><thead><tr><th>TIKET / PELAPOR</th><th>MASALAH</th><th>KATEGORI</th><th>PRIORITAS</th><th>STATUS</th><th>PIC</th></tr></thead><tbody>
<?php foreach ($listing['rows'] as $row): ?>
<tr><td><a class="fw-semibold text-decoration-none" href="<?= complaint_e(site_url('complaint/detail/'.$row['complaint_id'])) ?>"><?= complaint_e($row['ticket_no']) ?></a><div class="small complaint-muted"><?= complaint_e($row['pelapor']) ?><br><?= complaint_e($row['reported_at']) ?></div></td><td style="min-width:220px"><div class="fw-semibold"><?= complaint_e($row['judul']) ?></div><?php if ($row['needs_review']): ?><span class="small text-warning">Perlu tinjauan</span><?php endif; ?></td><td><?= complaint_e($row['kategori']) ?></td><td><?= complaint_badge($row['priority']) ?></td><td><?= complaint_badge($row['status']) ?></td><td><?= complaint_e($row['pic_name'] ?: 'Belum ditetapkan') ?></td></tr>
<?php endforeach; ?>
<?php if (!$listing['rows']): ?><tr><td colspan="6" class="text-center py-5 complaint-muted"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Belum ada tiket pada filter ini. Impor chat WhatsApp atau buat tiket manual.</td></tr><?php endif; ?>
</tbody></table></div>
<?php complaint_pagination('complaint', $listing, $filters); ?>
</div></div>
