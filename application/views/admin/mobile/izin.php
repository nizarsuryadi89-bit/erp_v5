<div class="container my-4">
  <div class="card shadow-sm">
    <div class="card-body">
      <h5 class="card-title text-center mb-3">Pengajuan Lembur</h5>
      <hr>

      <form action="<?= base_url('mobile/aksi_simpan_lembur') ?>" method="post">
        
        <div class="mb-3">
          <label class="form-label">Karyawan</label>
          <select class="form-select" disabled required>
            <?php foreach($listkaryawan as $row): ?>
              <option value="<?= $row->karyawan_id ?>"
                <?= isset($karyawan_terpilih['karyawan_id']) && $karyawan_terpilih['karyawan_id'] == $row->karyawan_id ? 'selected' : '' ?>>
                <?= htmlspecialchars($row->nama_lengkap, ENT_QUOTES, 'UTF-8') ?>
              </option>
            <?php endforeach; ?>
          </select>
          <input type="hidden" name="karyawan_id" value="<?= $karyawan_terpilih['karyawan_id'] ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Tanggal</label>
          <input type="date" name="tanggal" class="form-control" required value="<?= date('Y-m-d') ?>" readonly>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Jam Mulai</label>
            <input type="time" name="jam_mulai" class="form-control" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Jam Selesai</label>
            <input type="time" name="jam_selesai" class="form-control" required>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Kegiatan</label>
          <textarea name="kegiatan" class="form-control" rows="3" placeholder="Tuliskan kegiatan lembur..."></textarea>
        </div>

        <p class="text-muted text-center">Pengajuan Lembur hanya untuk hari berjalan</p>
        <hr>

        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-success">
            <i class="bi bi-save me-1"></i> Simpan
          </button>
          <a href="<?= base_url('mobile') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
          </a>
        </div>
        
      </form>
    </div>
  </div>
</div>
