<div class="app-content complaint-shell">
<?php $this->load->view('admin/complaint/_nav', get_defined_vars()); ?>
<div class="complaint-card" style="max-width:900px"><h2 class="h4 mb-4">Buat tiket manual</h2>
<form method="post" action="<?= complaint_e(site_url('complaint/save')) ?>" class="row g-3">
<?php complaint_form_token($complaint_csrf); ?>
<div class="col-md-8"><label for="judul">Judul masalah</label><input class="form-control" id="judul" name="judul" required maxlength="191" placeholder="Contoh: Mesin A3 menghasilkan cetakan bergaris"></div>
<div class="col-md-4"><label for="pelapor">Pelapor</label><input class="form-control" id="pelapor" name="pelapor" value="<?= complaint_e($username) ?>" required maxlength="191"></div>
<div class="col-12"><label for="deskripsi">Deskripsi dan dampak</label><textarea class="form-control" name="deskripsi" id="deskripsi" required rows="5" maxlength="16000"></textarea></div>
<div class="col-md-6"><label for="kategori">Kategori</label><select class="form-select" id="kategori" name="kategori"><?php foreach ($categories as $category): ?><option><?= complaint_e($category) ?></option><?php endforeach; ?></select></div>
<div class="col-md-6"><label for="priority">Prioritas</label><select class="form-select" id="priority" name="priority"><?php foreach ($priorities as $p): ?><option <?= $p === 'MEDIUM' ? 'selected' : '' ?>><?= complaint_e($p) ?></option><?php endforeach; ?></select></div>
<div class="col-md-6"><label for="mesin_id">Mesin terkait</label><select class="form-select" id="mesin_id" name="mesin_id"><option value="">Belum diketahui / tidak ada</option><?php foreach ($machines as $machine): ?><option value="<?= (int) $machine['mesin_id'] ?>"><?= complaint_e($machine['mesin_nama']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-6"><label for="spks">SPK terkait</label><input class="form-control" id="spks" name="spks" maxlength="1200" placeholder="Pisahkan dengan koma"><div class="form-text">Harus merupakan SPK yang tercatat di cabang aktif.</div></div>
<div class="col-12"><button class="btn btn-primary">Simpan tiket</button></div>
</form></div></div>
