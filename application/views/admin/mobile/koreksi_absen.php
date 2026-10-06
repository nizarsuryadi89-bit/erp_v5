<!-- /application/views/admin/mobile/koreksi_absen.php -->
<!-- Page Header -->
<div class="custom-page-header">
  <h5><i class="bi bi-clock-history me-2 text-warning"></i>Koreksi Absen</h5>
  <p>Formulir pengajuan revisi jam absensi</p>
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

    <form action="<?= base_url('mobile/submit_koreksi_absen') ?>" method="post" id="koreksiForm">
      <div class="mb-3">
        <label class="premium-label">Tanggal Absen (Data Kesiangan)</label>
        <select name="tanggal_absen" id="tanggal_absen" class="premium-input premium-select w-100" required>
            <option value="">-- Pilih Tanggal --</option>
            <?php if (!empty($tgl_kesiangan)): ?>
                <?php foreach ($tgl_kesiangan as $tgl): ?>
                    <?php 
                        $masuk_time = strtotime($tgl->tanggal . ' ' . $tgl->jam_masuk);
                        $batas_time = strtotime($tgl->tanggal . ' 08:00:00');
                        $telat_menit = round(($masuk_time - $batas_time) / 60);
                    ?>
                    <option value="<?= $tgl->tanggal ?>">
                        <?= date('d M Y', strtotime($tgl->tanggal)) ?> (Telat <?= $telat_menit ?> mnt)
                    </option>
                <?php endforeach; ?>
            <?php else: ?>
                <option value="" disabled>Tidak ada catatan kesiangan.</option>
            <?php endif; ?>
        </select>
      </div>
      
      <div id="info_absen" class="alert alert-info rounded-3 mb-3" style="display: none; font-size: 0.85rem;">
        <!-- Akan diisi dengan AJAX -->
      </div>
      
      <div class="row mb-3 g-2">
        <div class="col-6">
          <label class="premium-label">Jam Masuk (Benar)</label>
          <input type="time" name="jam_masuk_baru" id="jam_masuk_baru" class="premium-input w-100">
        </div>
        <div class="col-6">
          <label class="premium-label">Jam Pulang (Benar)</label>
          <input type="time" name="jam_pulang_baru" id="jam_pulang_baru" class="premium-input w-100">
        </div>
      </div>
      
      <div class="mb-4">
        <small class="text-muted d-block mb-2"><i class="bi bi-info-circle me-1"></i>Kosongkan jam jika tidak ingin mengubahnya.</small>
      </div>

      <div class="mb-4">
        <label class="premium-label">Keterangan / Alasan</label>
        <textarea name="alasan" class="premium-input w-100" rows="3" placeholder="Jelaskan detail alasan Anda (misal: Lupa absen pagi)..."
          required></textarea>
      </div>

      <button type="submit" class="action-btn w-100 py-3"
        style="background: linear-gradient(135deg, var(--primary), var(--accent));">
        <i class="bi bi-send-fill me-2"></i> AJUKAN KOREKSI
      </button>
    </form>
  </div>

  <!-- History List -->
  <div class="section-header mt-4">
    <div class="section-title"><i class="bi bi-clock-history me-2"></i>Riwayat Pengajuan</div>
  </div>
  <div class="history-list">
    <?php if (!empty($riwayat)): ?>
      <?php foreach ($riwayat as $r): ?>
        <div class="stat-card py-3 px-4 mb-2 shadow-sm border-0">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-bold text-dark small"><?= date('d M Y', strtotime($r->tanggal_absen)) ?></span>
            <?php 
                if ($r->status == 'Pending') {
                    $badge_class = 'bg-warning-subtle text-warning';
                } elseif ($r->status == 'Disetujui') {
                    $badge_class = 'bg-success-subtle text-success';
                } else {
                    $badge_class = 'bg-danger-subtle text-danger';
                }
            ?>
            <span class="badge <?= $badge_class ?> rounded-pill px-3">
              <?= $r->status ?>
            </span>
          </div>
          <div class="d-flex justify-content-between align-items-center">
            <p class="text-muted small mb-0 fst-italic">"<?= htmlspecialchars($r->alasan ?? '-') ?>"</p>
          </div>
          <div class="mt-2 text-secondary small">
            <strong>Masuk:</strong> <?= substr($r->jam_masuk_baru ?? '-', 0, 5) ?> | 
            <strong>Pulang:</strong> <?= substr($r->jam_pulang_baru ?? '-', 0, 5) ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="text-center py-4 bg-white rounded-4 border border-dashed">
        <p class="text-muted small mb-0">Belum ada riwayat pengajuan koreksi absen.</p>
      </div>
    <?php endif; ?>
  </div>

</div>

<script>
  document.getElementById('tanggal_absen').addEventListener('change', function() {
    const tanggal = this.value;
    const infoBox = document.getElementById('info_absen');
    
    if (tanggal) {
        fetch('<?= base_url('mobile/get_absensi_info_by_date') ?>?tanggal=' + tanggal)
            .then(response => response.json())
            .then(data => {
                infoBox.style.display = 'block';
                if (data.status) {
                    infoBox.className = 'alert alert-info rounded-3 mb-3';
                    infoBox.innerHTML = '<i class="bi bi-info-circle-fill me-2"></i>' + data.message;
                    
                    // Optional: prefill the current jam for easy correction
                    // document.getElementById('jam_masuk_baru').value = data.jam_masuk !== '-' ? data.jam_masuk : '';
                    // document.getElementById('jam_pulang_baru').value = data.jam_pulang !== '-' ? data.jam_pulang : '';
                } else {
                    infoBox.className = 'alert alert-warning rounded-3 mb-3';
                    infoBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>' + data.message;
                }
            })
            .catch(error => console.error('Error fetching data:', error));
    } else {
        infoBox.style.display = 'none';
    }
  });

  document.getElementById('koreksiForm').addEventListener('submit', function (e) {
    e.preventDefault();

    Swal.fire({
      title: 'Kirim Pengajuan?',
      text: 'Pastikan data jam dan tanggal yang diisi sudah benar.',
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