<?php
$isBelumAbsen = ($hasilcek == '0');
$jamSekarang = date('H:i');
$issuccess = ($success != '0');
$isPulangAllowed = ($jamSekarang >= $jam_pulang_minimal);
$hasPulangRecord = (!empty($cekabsen['jam_pulang']) && $cekabsen['jam_pulang'] != "00:00:00");
?>

<!-- Page Header -->
<div class="custom-page-header">
  <h5><i class="bi bi-calendar2-check-fill me-2 text-warning"></i>Sistem Presensi</h5>
  <p><?= date('l, d F Y') ?></p>
</div>

<!-- Main Content Container -->
<div class="content-container">

  <!-- Attendance Console -->
  <div class="stat-card text-center mb-4 shadow-sm border-0">
    <style>
      .btn-absen {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        border: none;
        color: #fff;
        font-weight: 700;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin: 1rem auto;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      }

      .btn-absen:active {
        transform: scale(0.9);
      }

      .btn-absen i {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
      }

      .btn-masuk {
        background: linear-gradient(135deg, #22c55e, #16a34a);
      }

      .btn-pulang {
        background: linear-gradient(135deg, #f59e0b, #d97706);
      }

      .status-indicator {
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-block;
        margin-bottom: 1rem;
      }

      .status-indicator.red {
        background: #fee2e2;
        color: #dc2626;
      }

      .status-indicator.green {
        background: #dcfce7;
        color: #16a34a;
      }

      .status-indicator.yellow {
        background: #fef3c7;
        color: #d97706;
      }

      .log-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 1rem;
        background: #fff;
        border-radius: 18px;
        margin-bottom: 0.85rem;
        border: 1px solid #f1f5f9;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
      }

      .log-date {
        min-width: 45px;
        text-align: center;
        border-right: 1px solid #f1f5f9;
        padding-right: 0.75rem;
      }

      .log-date .day {
        font-weight: 800;
        font-size: 1.1rem;
        color: #1e293b;
        line-height: 1;
        display: block;
      }

      .log-date .month {
        font-size: 0.6rem;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 600;
      }

      .log-details {
        flex: 1;
      }

      .log-status-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.4rem;
      }

      .log-times-row {
        display: flex;
        gap: 1rem;
      }

      .time-slot {
        display: flex;
        flex-direction: column;
      }

      .time-label {
        font-size: 0.6rem;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        margin-bottom: 0.1rem;
      }

      .time-value {
        font-size: 0.85rem;
        font-weight: 700;
        color: #334155;
      }

      .foto-thumb {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        object-fit: cover;
        background: #f1f5f9;
        border: 2px solid #fff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: transform 0.2s;
      }

      .foto-thumb:active {
        transform: scale(0.9);
      }

      .history-photo-group {
        display: flex;
        gap: 0.5rem;
      }

      /* Camera Modal Styles */
      .camera-preview-container {
        position: relative;
        width: 100%;
        margin: 0 auto 1rem;
        aspect-ratio: 4/5;
        /* Standard portrait ratio */
        background: #000;
        border-radius: 16px;
        /* Soft rounded corners */
        overflow: hidden;
        border: 2px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
      }

      #webcam-preview {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transform: scaleX(-1);
        /* Mirror effect */
      }

      .camera-grid {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        box-sizing: border-box;
      }

      /* Face Frame (Rounded Rectangle) */
      .camera-grid::after {
        content: '';
        position: absolute;
        top: 15%;
        left: 15%;
        width: 70%;
        height: 70%;
        border: 2px dashed rgba(255, 255, 255, 0.5);
        border-radius: 100px;
        /* Oval shape for face */
      }
    </style>

    <?php if (!$boleh_absen): ?>
      <div class="status-indicator <?= ($is_libur || $is_minggu) ? 'green' : 'red' ?>">
        <?= ($is_libur || $is_minggu) ? 'SELAMAT BERLIBUR' : 'TIDAK BISA PRESENSI' ?>
      </div>
      <p class="text-muted small mb-0">
        <?php if ($is_libur): ?>
          Hari ini Libur: <strong> <br><?= $holiday_name ?></strong>
        <?php elseif ($is_minggu): ?>
          Hari ini adalah hari Minggu
        <?php else: ?>
          Di luar jam operasional (06:00 - 21:00)
        <?php endif; ?>
      </p>

    <?php elseif ($issuccess): ?>
      <div class="status-indicator green">PRESENSI SELESAI</div>
      <p class="text-muted small">Terima kasih atas dedikasi Anda hari ini.</p>
      <div class="d-flex justify-content-center gap-4 mt-2">
        <div class="small">
          <span class="d-block text-muted">Masuk</span>
          <strong class="text-success"><?= $cekabsen['jam_masuk'] ?></strong>
        </div>
        <div class="small">
          <span class="d-block text-muted">Pulang</span>
          <strong class="text-primary"><?= $cekabsen['jam_pulang'] ?></strong>
        </div>
      </div>

    <?php elseif ($isBelumAbsen): ?>
      <form id="formAbsenMasuk" method="post" enctype="multipart/form-data"
        action="<?= base_url('absensi/absen_masuk') ?>">
        <input type="hidden" name="karyawan_id" value="<?= $karyawan_terpilih['karyawan_id'] ?>">
        <input type="hidden" name="tanggal" value="<?= date('Y-m-d') ?>">
        <input type="hidden" name="jam_masuk" value="<?= date('H:i:s') ?>">
        <input type="hidden" name="jam_masuk" value="<?= date('H:i:s') ?>">
        <input type="hidden" name="foto_selfie" id="foto_data_masuk">
        <input type="hidden" name="lokasi_absen" id="lokasi_data_masuk">
        <button type="button" class="btn-absen btn-masuk" onclick="triggerAbsen('masuk')">
          <i class="bi bi-camera-fill"></i>
          MASUK
        </button>
      </form>
      <p class="mt-2 mb-0 text-muted small"><i class="bi bi-clock me-1"></i><?= date('H:i') ?></p>

    <?php elseif ($isPulangAllowed && !$hasPulangRecord): ?>
      <form id="formAbsenPulang" method="post" enctype="multipart/form-data"
        action="<?= base_url('absensi/absen_pulang') ?>">
        <input type="hidden" name="karyawan_id" value="<?= $karyawan_terpilih['karyawan_id'] ?>">
        <input type="hidden" name="jam_pulang" value="<?= date('H:i:s') ?>">
        <input type="hidden" name="tanggal" value="<?= date('Y-m-d') ?>">
        <input type="hidden" name="tanggal" value="<?= date('Y-m-d') ?>">
        <input type="hidden" name="foto_selfie" id="foto_data_pulang">
        <input type="hidden" name="lokasi_absen" id="lokasi_data_pulang">
        <button type="button" class="btn-absen btn-pulang" onclick="triggerAbsen('pulang')">
          <i class="bi bi-camera-fill"></i>
          PULANG
        </button>
      </form>
      <p class="mt-2 mb-0 text-muted small"><i class="bi bi-clock me-1"></i><?= date('H:i') ?></p>

    <?php else: ?>
      <div class="status-indicator yellow">SUDAH MASUK</div>
      <p class="mb-2 text-muted small">Waktu Masuk: <strong><?= $cekabsen['jam_masuk'] ?></strong></p>
      <div class="alert alert-info py-2 small border-0" style="border-radius: 12px; background: #e0f2fe; color: #0369a1;">
        <?php if (!$isPulangAllowed): ?>
          <i class="bi bi-info-circle me-1"></i> Pulang tersedia setelah <?= $jam_pulang_minimal ?>
        <?php else: ?>
          <i class="bi bi-check-circle me-1"></i> Menunggu proses sinkronisasi...
        <?php endif; ?>
      </div>
      <small class="text-muted" style="font-size: 10px;">Server Time: <?= date('H:i:s') ?></small>
    <?php endif; ?>
  </div>

  <!-- Monthly Statistics -->
  <div class="section-header">
    <div class="section-title"><i class="bi bi-bar-chart-fill me-2"></i>Statistik Bulan Ini</div>
  </div>
  <div class="row g-2 mb-4">
    <div class="col-3">
      <div class="stat-card p-2 text-center" style="background: #fffbeb;">
        <span class="d-block fw-bold text-warning"
          style="font-size: 1.2rem;"><?= $statistik_bulan_ini['terlambat'] ?? 0 ?>x</span>
        <?php if (!empty($statistik_bulan_ini['terlambat_menit'])): ?>
          <span style="font-size: 0.55rem; font-weight: 700; color: #92400e; display: block; margin-top: -3px; margin-bottom: 2px;">(<?= $statistik_bulan_ini['terlambat_menit'] ?>m)</span>
        <?php endif; ?>
        <span style="font-size: 0.6rem; font-weight: 700; color: #92400e; text-transform: uppercase;">Telat</span>
      </div>
    </div>
    <div class="col-3">
      <div class="stat-card p-2 text-center" style="background: #ecfeff;">
        <span class="d-block fw-bold text-info"
          style="font-size: 1.2rem;"><?= $statistik_bulan_ini['sakit'] ?? 0 ?></span>
        <span style="font-size: 0.6rem; font-weight: 700; color: #0891b2; text-transform: uppercase;">Sakit</span>
      </div>
    </div>
    <div class="col-3">
      <div class="stat-card p-2 text-center" style="background: #eff6ff;">
        <span class="d-block fw-bold text-primary"
          style="font-size: 1.2rem;"><?= $statistik_bulan_ini['izin'] ?? 0 ?></span>
        <span style="font-size: 0.6rem; font-weight: 700; color: #1e40af; text-transform: uppercase;">Izin</span>
      </div>
    </div>
    <div class="col-3">
      <div class="stat-card p-2 text-center" style="background: #fef2f2;">
        <span class="d-block fw-bold text-danger"
          style="font-size: 1.2rem;"><?= $statistik_bulan_ini['alpha'] ?? 0 ?></span>
        <span style="font-size: 0.6rem; font-weight: 700; color: #991b1b; text-transform: uppercase;">Alfa</span>
      </div>
    </div>
  </div>

  <!-- Overtime Statistics -->
  <?php if (isset($rekap_lembur)): ?>
    <div class="section-header mt-3">
      <div class="section-title"><i class="bi bi-briefcase-fill me-2 text-indigo"></i>Rekap Lembur Bulan Ini</div>
    </div>
    <div class="row g-2 mb-4">
      <div class="col-4">
        <div class="stat-card p-2 text-center" style="background: #f1f5f9;">
          <span class="d-block fw-bold text-slate-700"
            style="font-size: 1.1rem;"><?= number_format($rekap_lembur['detail_lembur']['wajib']['jam'], 1) ?> <span
              style="font-size: 0.6rem;">J</span></span>
          <span style="font-size: 0.55rem; font-weight: 700; color: #475569; text-transform: uppercase;">Wajib</span>
        </div>
      </div>
      <div class="col-4">
        <div class="stat-card p-2 text-center" style="background: #f1f5f9;">
          <span class="d-block fw-bold text-slate-700"
            style="font-size: 1.1rem;"><?= number_format($rekap_lembur['detail_lembur']['kerjaan']['jam'], 1) ?> <span
              style="font-size: 0.6rem;">J</span></span>
          <span style="font-size: 0.55rem; font-weight: 700; color: #475569; text-transform: uppercase;">Kerjaan</span>
        </div>
      </div>
      <div class="col-4">
        <div class="stat-card p-2 text-center" style="background: #eef2ff;">
          <span class="d-block fw-bold text-indigo"
            style="font-size: 1.1rem;"><?= number_format($rekap_lembur['total_jam_lembur'], 1) ?> <span
              style="font-size: 0.6rem;">J</span></span>
          <span style="font-size: 0.55rem; font-weight: 700; color: #4f46e5; text-transform: uppercase;">Total</span>
        </div>
      </div>
      <div class="col-12 mt-2">
        <div class="d-flex justify-content-between align-items-center p-2 rounded-3"
          style="background: #ecfdf5; border: 1px dashed #10b981;">
          <span style="font-size: 0.65rem; font-weight: 700; color: #065f46; text-transform: uppercase;"><i
              class="bi bi-wallet2 me-1"></i>Estimasi Uang Lembur</span>
          <span class="fw-bold text-success" style="font-size: 0.85rem;">Rp
            <?= number_format($rekap_lembur['uang_lembur'], 0, ',', '.') ?></span>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Weekly History -->
  <div class="section-header">
    <div class="section-title"><i class="bi bi-clock-history me-2"></i>Riwayat Mingguan</div>
  </div>
  <div class="history-list">
    <?php if (!empty($absensi_mingguan)): ?>
      <?php foreach ($absensi_mingguan as $absensi): ?>
        <div class="log-item">
          <div class="log-date">
            <span class="day"><?= date('d', strtotime($absensi->tanggal)) ?></span>
            <span class="month"><?= date('M', strtotime($absensi->tanggal)) ?></span>
          </div>

          <div class="log-details">
            <div class="log-status-row">
              <?php if ($absensi->status == 'Hadir'): ?>
                <?php 
                  $m_telat = 0;
                  if (!empty($absensi->jam_masuk) && $absensi->jam_masuk >= '08:01:00') {
                      $m_telat = floor((strtotime($absensi->jam_masuk) - strtotime('08:00:00')) / 60);
                  }
                ?>
                <?php if ($m_telat > 0): ?>
                  <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2"
                    style="font-size: 0.6rem;">TELAT <?= $m_telat > 0 ? "({$m_telat}m)" : "" ?></span>
                <?php else: ?>
                  <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2"
                    style="font-size: 0.6rem;">HADIR</span>
                <?php endif; ?>
              <?php elseif ($absensi->status == 'Sakit'): ?>
                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2"
                  style="font-size: 0.6rem;">SAKIT</span>
              <?php elseif ($absensi->status == 'Izin'): ?>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2"
                  style="font-size: 0.6rem;">IZIN</span>
              <?php elseif (in_array($absensi->status, ['Alpha', 'Alfa', 'Late'])): ?>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2"
                  style="font-size: 0.6rem;"><?= strtoupper($absensi->status) ?></span>
              <?php else: ?>
                <span class="text-muted" style="font-size: 0.6rem;">-</span>
              <?php endif; ?>

              <div class="history-photo-group">
                <?php if (!empty($absensi->foto_masuk)): ?>
                  <img src="<?= base_url($absensi->foto_masuk) ?>" class="foto-thumb" title="Selfie Masuk"
                    onclick="viewSelfie('<?= base_url($absensi->foto_masuk) ?>', 'Masuk - <?= date('H:i', strtotime($absensi->jam_masuk)) ?>')">
                <?php else: ?>
                  <div class="foto-thumb flex-center bg-light text-muted"
                    style="display: flex; align-items: center; justify-content: center; font-size: 0.7rem;"><i
                      class="bi bi-person-x"></i></div>
                <?php endif; ?>

                <?php if (!empty($absensi->foto_pulang)): ?>
                  <img src="<?= base_url($absensi->foto_pulang) ?>" class="foto-thumb" title="Selfie Pulang"
                    onclick="viewSelfie('<?= base_url($absensi->foto_pulang) ?>', 'Pulang - <?= date('H:i', strtotime($absensi->jam_pulang)) ?>')">
                <?php else: ?>
                  <div class="foto-thumb flex-center bg-light text-muted"
                    style="display: flex; align-items: center; justify-content: center; font-size: 0.7rem;"><i
                      class="bi bi-person-x"></i></div>
                <?php endif; ?>
              </div>
            </div>

            <div class="log-times-row">
              <div class="time-slot border-end pe-3">
                <span class="time-label">Masuk</span>
                <span
                  class="time-value"><?= !empty($absensi->jam_masuk) && $absensi->jam_masuk != '00:00:00' ? date('H:i', strtotime($absensi->jam_masuk)) : '--:--' ?></span>
              </div>
              <div class="time-slot">
                <span class="time-label">Pulang</span>
                <span
                  class="time-value"><?= !empty($absensi->jam_pulang) && $absensi->jam_pulang != '00:00:00' ? date('H:i', strtotime($absensi->jam_pulang)) : '--:--' ?></span>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="text-center py-4 bg-white rounded-3 border border-dashed">
        <i class="bi bi-calendar-x text-muted" style="font-size: 2rem;"></i>
        <p class="text-muted small mt-2">Tidak ada data riwayat</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  function viewSelfie(url, title) {
    Swal.fire({
      title: title,
      imageUrl: url,
      imageWidth: '100%',
      imageHeight: 'auto',
      imageAlt: 'Selfie Attendance',
      confirmButtonText: 'Tutup',
      confirmButtonColor: '#1e293b',
      borderRadius: '20px'
    });
  }

  let videoStream = null;

  async function startCamera() {
    // Check for Secure Context (HTTPS required for camera/gps on most browsers)
    if (!window.isSecureContext && location.hostname !== "localhost") {
      Swal.fire({
        icon: 'error',
        title: 'Koneksi Tidak Aman',
        text: 'Fitur Kamera & GPS hanya dapat berjalan di koneksi HTTPS (Secure). Mohon hubungi IT untuk memasang SSL.',
        footer: '<small>Error: Secure Context Required</small>'
      });
      return false;
    }

    try {
      videoStream = await navigator.mediaDevices.getUserMedia({
        video: {
          facingMode: "user",
          width: { ideal: 640 },
          height: { ideal: 480 }
        }
      });
      const video = document.getElementById('webcam-preview');
      if (video) {
        video.srcObject = videoStream;
        video.play();
      }
      return true;
    } catch (err) {
      console.error("Error accessing camera: ", err);
      let errorMsg = 'Tidak dapat mengakses kamera. Pastikan izin diberikan.';
      if (err.name === 'NotAllowedError') errorMsg = 'Izin kamera ditolak oleh pengguna.';
      if (err.name === 'NotFoundError') errorMsg = 'Perangkat kamera tidak ditemukan.';

      Swal.fire('Gagal', errorMsg, 'error');
      return false;
    }
  }

  function stopCamera() {
    if (videoStream) {
      videoStream.getTracks().forEach(track => track.stop());
      videoStream = null;
    }
  }


  /**
   * Fetch Geolocation
   */
  function getPosition() {
    return new Promise((resolve, reject) => {
      navigator.geolocation.getCurrentPosition(resolve, reject, {
        enableHighAccuracy: true,
        timeout: 15000, // 15 detik agar HP punya cukup waktu mencari sinyal satelit
        maximumAge: 10000
      });
    });
  }

  /**
   * Draw Text Watermark on Canvas
   */
  function drawWatermark(canvas, locationText) {
    const ctx = canvas.getContext('2d');
    const padding = 20;
    const fontSize = Math.max(14, canvas.width / 40);

    // Bottom Bar Background (Slimmer)
    ctx.fillStyle = "rgba(0, 0, 0, 0.5)";
    ctx.fillRect(0, canvas.height - (fontSize * 2.5), canvas.width, fontSize * 2.5);

    // Text Style
    ctx.fillStyle = "white";
    ctx.font = `bold ${fontSize}px Inter, sans-serif`;
    ctx.textAlign = "left";

    // Location info only
    ctx.fillText(locationText, padding, canvas.height - (fontSize * 1));
  }

  async function capturePhoto(locationText) {
    const video = document.getElementById('webcam-preview');
    const canvas = document.createElement('canvas');

    // Scale down image to save server bandwidth and storage
    const MAX_WIDTH = 600;
    let width = video.videoWidth;
    let height = video.videoHeight;

    if (width > MAX_WIDTH) {
      height = height * (MAX_WIDTH / width);
      width = MAX_WIDTH;
    }

    canvas.width = width;
    canvas.height = height;
    const ctx = canvas.getContext('2d');

    // 1. Draw mirrored video frame
    ctx.translate(canvas.width, 0);
    ctx.scale(-1, 1);
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    // 2. Reset scale to draw normal text
    ctx.setTransform(1, 0, 0, 1, 0, 0);

    // 3. Add Watermark
    drawWatermark(canvas, locationText);

    // Compress to 60% quality JPEG
    return canvas.toDataURL('image/jpeg', 0.6);
  }

  async function triggerAbsen(type) {
    const label = type === 'masuk' ? 'Masuk' : 'Pulang';

    // Show Loading while getting position (Mandatory Step)
    Swal.fire({
      title: 'Validasi Lokasi...',
      html: `Mencoba mendapatkan koordinat GPS untuk Absen ${label}...<br><small class="text-muted">Pastikan GPS aktif & izin lokasi diberikan.</small>`,
      allowOutsideClick: false,
      didOpen: () => { Swal.showLoading(); }
    });

    let locationInfo = "";
    let locationString = "";
    try {
      const pos = await getPosition();
      locationInfo = `LAT: ${pos.coords.latitude.toFixed(6)}, LONG: ${pos.coords.longitude.toFixed(6)}`;
      locationString = `Lat: ${pos.coords.latitude}, Long: ${pos.coords.longitude}`;
    } catch (err) {
      console.warn("Geolocation failed", err);
      let errorMsg = "Gagal mendapatkan lokasi.";

      if (err.code === 1) errorMsg = "Izin lokasi ditolak. Silakan aktifkan izin lokasi pada browser/aplikasi Anda.";
      if (err.code === 2) errorMsg = "Sinyal GPS tidak ditemukan atau lokasi tidak tersedia.";
      if (err.code === 3) errorMsg = "Waktu pencarian lokasi habis (Timeout). Pastikan Anda berada di ruang terbuka.";

      Swal.fire({
        icon: 'error',
        title: 'Lokasi Wajib Aktif',
        text: errorMsg,
        confirmButtonColor: '#dc3545'
      });
      return; // STOP process if GPS fails
    }

    // If we reach here, location is secured
    const result = await Swal.fire({
      title: `Selfie Absen ${label}`,
      html: `
        <div class="camera-preview-container">
          <video id="webcam-preview" autoplay playsinline muted></video>
          <div class="camera-grid"></div>
        </div>
        <p class="small text-muted mb-1"><i class="bi bi-geo-alt-fill text-danger"></i> ${locationInfo}</p>
        <p class="small text-muted">Pastikan wajah terlihat jelas & pencahayaan cukup</p>
      `,
      showCancelButton: true,
      confirmButtonText: `Absen ${label} Sekarang`,
      cancelButtonText: 'Batal',
      confirmButtonColor: type === 'masuk' ? '#16a34a' : '#f59e0b',
      reverseButtons: true,
      allowOutsideClick: false,
      didOpen: () => {
        startCamera();
      },
      preConfirm: async () => {
        try {
          const photoData = await capturePhoto(locationInfo);
          return photoData;
        } catch (e) {
          Swal.showValidationMessage(`Gagal mengambil gambar: ${e}`);
        }
      },
      willClose: () => {
        stopCamera();
      }
    });

    if (result.isConfirmed && result.value) {
      const photoData = result.value;
      const now = new Date();
      const jam = now.getHours().toString().padStart(2, '0') + ':' +
        now.getMinutes().toString().padStart(2, '0') + ':' +
        now.getSeconds().toString().padStart(2, '0');
      const tgl = now.getFullYear() + '-' +
        (now.getMonth() + 1).toString().padStart(2, '0') + '-' +
        now.getDate().toString().padStart(2, '0');

      if (type === 'masuk') {
        const form = document.getElementById('formAbsenMasuk');
        form.querySelector('input[name="tanggal"]').value = tgl;
        form.querySelector('input[name="jam_masuk"]').value = jam;
        document.getElementById('foto_data_masuk').value = photoData;
        document.getElementById('lokasi_data_masuk').value = locationString;
        form.submit();
      } else {
        const form = document.getElementById('formAbsenPulang');
        form.querySelector('input[name="tanggal"]').value = tgl;
        form.querySelector('input[name="jam_pulang"]').value = jam;
        document.getElementById('foto_data_pulang').value = photoData;
        document.getElementById('lokasi_data_pulang').value = locationString;
        form.submit();
      }
    }
  }
</script>