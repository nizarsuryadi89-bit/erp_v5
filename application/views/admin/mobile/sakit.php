<!-- Page Header -->
<div class="custom-page-header">
  <h5><i class="bi bi-heart-pulse-fill me-2 text-danger"></i>Pengajuan Izin & Sakit</h5>
  <p>Formulir permohonan ketidakhadiran kerja</p>
</div>

<!-- Main Content -->
<div class="content-container">

  <!-- Flash Messages -->
  <?php if ($this->session->flashdata('success')): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= $this->session->flashdata('success') ?>',
        confirmButtonColor: '#1e293b',
        borderRadius: '15px'
      });
    </script>
  <?php endif; ?>

  <?php if ($this->session->flashdata('error')): ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: '<?= $this->session->flashdata('error') ?>',
        confirmButtonColor: '#1e293b',
        borderRadius: '15px'
      });
    </script>
  <?php endif; ?>

  <!-- Form Pengajuan -->
  <div class="stat-card shadow-sm border-0 mb-4">
    <style>
      .premium-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
        display: block;
      }

      .premium-input {
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.2s;
        background: #f8fafc;
      }

      .premium-input:focus {
        border-color: var(--accent);
        background: #fff;
        box-shadow: 0 0 0 4px rgba(15, 52, 96, 0.1);
        outline: none;
      }

      .premium-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 16px 12px;
      }
    </style>

    <form action="<?= base_url('absensi/izin_absen') ?>" method="post" id="izinForm" enctype="multipart/form-data">
      <input type="hidden" name="karyawan_id" value="<?= $karyawan_terpilih['karyawan_id'] ?>">

      <div class="mb-3">
        <label class="premium-label">Jenis Pengajuan</label>
        <select name="status" id="status" class="premium-input premium-select w-100" required>
          <option value="">-- Pilih Jenis --</option>
          <option value="izin">Izin Pribadi</option>
          <option value="sakit">Sakit</option>
          <option value="pulang_cepat">Pulang Cepat</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="premium-label">Tanggal Terkait</label>
        <input type="date" name="tanggal" class="premium-input w-100" required min="<?= date('Y-m-d') ?>"
          value="<?= date('Y-m-d') ?>">
      </div>

      <div id="duration_section" class="row g-2 mb-3">
        <div class="col-7">
          <label class="premium-label">Durasi</label>
          <select name="durasi_tipe" id="durasi_tipe" class="premium-input premium-select w-100" required>
            <option value="hari">Hari</option>
            <option value="jam">Jam</option>
          </select>
        </div>
        <div class="col-5">
          <label class="premium-label">Jumlah</label>
          <input type="number" name="durasi_jumlah" id="durasi_jumlah" class="premium-input w-100" placeholder="0"
            min="1" required>
        </div>
      </div>

      <!-- Khusus Pulang Cepat -->
      <div id="pulang_cepat_section" class="mb-3" style="display: none;">
        <label class="premium-label">Jam Rencana Pulang</label>
        <small class="text-muted d-block mb-2">Hanya diperbolehkan mulai jam 14:00</small>
        <input type="time" name="jam_pulang_cepat" id="jam_pulang_cepat" class="premium-input w-100">
      </div>

      <div class="mb-4">
        <label class="premium-label">Keterangan / Alasan</label>
        <textarea name="kegiatan" class="premium-input w-100" rows="3" placeholder="Jelaskan detail alasan Anda..."
          required></textarea>
      </div>

      <!-- Fitur Upload Dokumen (Surat Sakit) -->
      <div class="mb-4" id="upload_dokumen_section" style="display: none;">
        <label class="premium-label">Upload Surat Sakit (PDF/JPG/PNG)</label>
        <small class="text-muted d-block mb-2">Opsional: Lampirkan surat keterangan jika ada</small>
        <input type="file" name="dokumen" id="dokumen" class="premium-input w-100" accept=".pdf,.jpg,.jpeg,.png">
      </div>

      <button type="submit" class="action-btn w-100 py-3"
        style="background: linear-gradient(135deg, var(--primary), var(--accent));">
        <i class="bi bi-send-fill me-2"></i> KIRIM PENGAJUAN
      </button>
    </form>
  </div>

  <!-- Monthly Summary -->
  <div class="row g-2 mb-4">
    <div class="col-4">
      <div class="stat-card p-3 text-center mb-0">
        <span class="d-block h4 fw-bold text-primary mb-0"><?= $total_izin ?></span>
        <span class="text-muted small fw-bold">IZIN</span>
      </div>
    </div>
    <div class="col-4">
      <div class="stat-card p-3 text-center mb-0">
        <span class="d-block h4 fw-bold text-warning mb-0"><?= $total_sakit ?></span>
        <span class="text-muted small fw-bold">SAKIT</span>
      </div>
    </div>
    <div class="col-4">
      <div class="stat-card p-3 text-center mb-0">
        <span class="d-block h4 fw-bold text-success mb-0"><?= $total_cuti ?></span>
        <span class="text-muted small fw-bold">CUTI</span>
      </div>
    </div>
  </div>

  <!-- History List -->
  <div class="section-header">
    <div class="section-title"><i class="bi bi-clock-history me-2"></i>Riwayat Terakhir</div>
  </div>
  <div class="history-list">
    <?php if (!empty($data_izin_sakit_bulan_ini)): ?>
      <?php foreach ($data_izin_sakit_bulan_ini as $absensi): ?>
        <div class="stat-card py-3 px-4 mb-2 shadow-sm border-0">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-bold text-dark small"><?= date('d F Y', strtotime($absensi->tanggal)) ?></span>
            <span
              class="badge <?= strtolower($absensi->status) == 'sakit' ? 'bg-warning-subtle text-warning' : 'bg-primary-subtle text-primary' ?> rounded-pill px-3">
              <?= ucfirst($absensi->status) ?>
            </span>
          </div>
          <div class="d-flex justify-content-between align-items-center">
            <p class="text-muted small mb-0 fst-italic">"<?= htmlspecialchars($absensi->keterangan ?? '-') ?>"</p>
            <?php if (!empty($absensi->dokumen)): ?>
              <a href="<?= base_url($absensi->dokumen) ?>" target="_blank" class="btn btn-link btn-sm p-0 text-info">
                <i class="bi bi-file-earmark-medical-fill me-1"></i> Lihat Surat
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="text-center py-4 bg-white rounded-4 border border-dashed">
        <p class="text-muted small mb-0">Belum ada riwayat pengajuan bulan ini.</p>
      </div>
    <?php endif; ?>
  </div>

</div>

<script>
  // Logic Toggle Field Dinamis
  document.getElementById('status').addEventListener('change', function () {
    const uploadSection = document.getElementById('upload_dokumen_section');
    const durationSection = document.getElementById('duration_section');
    const pulangCepatSection = document.getElementById('pulang_cepat_section');
    const durasiTipe = document.getElementById('durasi_tipe');
    const durasiJumlah = document.getElementById('durasi_jumlah');
    const jamPulangCepat = document.getElementById('jam_pulang_cepat');

    // Reset defaults
    uploadSection.style.display = 'none';
    durationSection.style.display = 'flex';
    pulangCepatSection.style.display = 'none';
    
    // Manage required attributes to prevent "not focusable" errors
    jamPulangCepat.removeAttribute('required');
    durasiTipe.setAttribute('required', 'required');
    durasiJumlah.setAttribute('required', 'required');

    if (this.value === 'sakit') {
      uploadSection.style.display = 'block';
    } else if (this.value === 'izin') {
      durationSection.style.display = 'none';
      durasiTipe.removeAttribute('required');
      durasiJumlah.removeAttribute('required');
      durasiTipe.value = 'hari';
      durasiJumlah.value = '1';
    } else if (this.value === 'pulang_cepat') {
      durationSection.style.display = 'none';
      durasiTipe.removeAttribute('required');
      durasiJumlah.removeAttribute('required');
      
      pulangCepatSection.style.display = 'block';
      jamPulangCepat.setAttribute('required', 'required');
      durasiTipe.value = 'jam';
    }
  });

  document.getElementById('izinForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const status = document.getElementById('status').value;
    const jamPulangCepat = document.getElementById('jam_pulang_cepat').value;

    if (status === 'pulang_cepat') {
      if (!jamPulangCepat) {
        Swal.fire({ icon: 'error', title: 'Oops...', text: 'Jam pulang harus diisi!' });
        return;
      }
      const hour = parseInt(jamPulangCepat.split(':')[0]);
      if (hour < 14) {
        Swal.fire({
          icon: 'error',
          title: 'Waktu Tidak Valid',
          text: 'Pulang cepat hanya bisa dilakukan mulai dari jam 14.00!',
          confirmButtonColor: '#1e293b'
        });
        return;
      }
    }

    Swal.fire({
      title: 'Kirim Pengajuan?',
      text: 'Pastikan data yang diisi sudah benar.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#1e293b',
      confirmButtonText: 'Ya, Kirim',
      cancelButtonText: 'Batal',
      reverseButtons: true,
      borderRadius: '15px'
    }).then((result) => {
      if (result.isConfirmed) {
        this.submit();
      }
    });
  });
</script>