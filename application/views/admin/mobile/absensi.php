<!-- Mobile Absensi View - Consistent Dark Theme -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

<style>
  :root {
    --primary: #1a1a2e;
    --secondary: #16213e;
  }

  * {
    -webkit-tap-highlight-color: transparent;
  }

  body {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    min-height: 100vh;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    margin: 0;
    padding: 0;
  }

  /* Header */
  .app-header {
    padding: 1.5rem;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .header-info h5 {
    margin: 0 0 0.25rem;
    font-size: 1.1rem;
    font-weight: 600;
  }

  .header-info small {
    display: block;
    opacity: 0.7;
    font-size: 0.8rem;
  }

  .profile-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 2px solid rgba(255, 255, 255, 0.3);
    object-fit: cover;
    background: rgba(255, 255, 255, 0.1);
  }

  /* Content Container */
  .content-container {
    background: #f5f5f5;
    border-radius: 24px 24px 0 0;
    min-height: calc(100vh - 120px);
    padding: 1.5rem;
  }

  /* Absen Button */
  .absen-section {
    text-align: center;
    padding: 2rem 0;
    margin-bottom: 1.5rem;
  }

  .btn-absen {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    border: none;
    background: linear-gradient(135deg, #16a34a, #15803d);
    color: #fff;
    font-size: 1.1rem;
    font-weight: 600;
    box-shadow: 0 8px 25px rgba(22, 163, 74, 0.4);
    cursor: pointer;
    transition: transform 0.15s;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
  }

  .btn-absen:active {
    transform: scale(0.95);
  }

  .btn-absen i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
  }

  .absen-time {
    font-size: 0.85rem;
    color: #666;
    margin-top: 1rem;
  }

  /* Info Card */
  .info-card {
    background: #fff;
    border-radius: 16px;
    padding: 1rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .info-card i {
    font-size: 1.25rem;
    color: var(--primary);
  }

  .info-card span {
    font-size: 0.9rem;
    color: #333;
  }

  /* Section Title */
  .section-title {
    font-size: 0.7rem;
    font-weight: 600;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  /* Menu Grid */
  .menu-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.75rem;
  }

  .menu-item {
    background: #fff;
    border-radius: 16px;
    padding: 1rem 0.5rem;
    text-align: center;
    text-decoration: none;
    transition: transform 0.15s;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
  }

  .menu-item:active {
    transform: scale(0.95);
  }

  .menu-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.5rem;
    font-size: 1.25rem;
  }

  .menu-label {
    font-size: 0.7rem;
    font-weight: 500;
    color: #333;
  }

  /* Icon Colors */
  .icon-blue {
    background: #dbeafe;
    color: #2563eb;
  }

  .icon-yellow {
    background: #fef3c7;
    color: #d97706;
  }

  .icon-green {
    background: #dcfce7;
    color: #16a34a;
  }

  .icon-red {
    background: #fee2e2;
    color: #dc2626;
  }

  .icon-purple {
    background: #f3e8ff;
    color: #9333ea;
  }

  .icon-gray {
    background: #f3f4f6;
    color: #4b5563;
  }

  .icon-cyan {
    background: #cffafe;
    color: #0891b2;
  }

  .icon-dark {
    background: #e5e7eb;
    color: #1f2937;
  }
</style>

<!-- Header -->
<div class="app-header">
  <div class="header-info">
    <h5>TGM Print</h5>
    <small><?= $karyawan_terpilih['nama_lengkap'] ?? $this->session->userdata('username') ?></small>
    <small><?= $karyawan_terpilih['nama_jabatan'] ?? 'Karyawan' ?></small>
  </div>
  <?php if (!empty($karyawan_terpilih['foto']) && file_exists('assets/images/karyawan/' . $karyawan_terpilih['foto'])): ?>
    <img src="<?= base_url('assets/images/karyawan/' . $karyawan_terpilih['foto']) ?>" class="profile-avatar"
      alt="Profile">
  <?php else: ?>
    <div class="profile-avatar d-flex align-items-center justify-content-center">
      <i class="bi bi-person-fill" style="font-size: 1.5rem; color: rgba(255,255,255,0.7);"></i>
    </div>
  <?php endif; ?>
</div>

<!-- Content -->
<div class="content-container">

  <!-- Absen Section -->
  <div class="absen-section">
    <form id="absenForm" method="post" action="<?= base_url('absensi/hadir') ?>">
      <input type="hidden" name="karyawan_id" value="<?= $karyawan_terpilih['karyawan_id'] ?? 1 ?>">
      <input type="hidden" name="tanggal" value="<?= date('Y-m-d') ?>">
      <input type="hidden" name="waktu" value="<?= date('H:i:s') ?>">
      <button type="button" class="btn-absen" onclick="confirmAbsen()">
        <i class="bi bi-box-arrow-in-right"></i>
        Masuk
      </button>
    </form>
    <div class="absen-time">
      <i class="bi bi-clock"></i> <?= date('d M Y - H:i') ?>
    </div>
  </div>

  <!-- Info Card -->
  <div class="info-card">
    <i class="bi bi-person-badge"></i>
    <span><?= $karyawan_terpilih['nama_lengkap'] ?? $this->session->userdata('username') ?></span>
  </div>

  <!-- Menu Section -->
  <div class="section-title">
    <i class="bi bi-grid-fill"></i> Menu
  </div>

  <div class="menu-grid">
    <a href="<?= base_url('mobile/profile') ?>" class="menu-item">
      <div class="menu-icon icon-blue"><i class="bi bi-person-fill"></i></div>
      <span class="menu-label">Profile</span>
    </a>
    <a href="<?= base_url('dashboard') ?>" class="menu-item">
      <div class="menu-icon icon-purple"><i class="bi bi-display"></i></div>
      <span class="menu-label">ERP</span>
    </a>
    <a href="<?= base_url('mobile/slip_gaji') ?>" class="menu-item">
      <div class="menu-icon icon-green"><i class="bi bi-cash-coin"></i></div>
      <span class="menu-label">Slip Gaji</span>
    </a>
    <a href="<?= base_url('mobile/sakit') ?>" class="menu-item">
      <div class="menu-icon icon-yellow"><i class="bi bi-calendar-check"></i></div>
      <span class="menu-label">Izin</span>
    </a>
    <a href="<?= base_url('mobile/lembur') ?>" class="menu-item">
      <div class="menu-icon icon-red"><i class="bi bi-clock-fill"></i></div>
      <span class="menu-label">Lembur</span>
    </a>
    <a href="<?= base_url('mobile/pinjaman') ?>" class="menu-item">
      <div class="menu-icon icon-cyan"><i class="bi bi-wallet2"></i></div>
      <span class="menu-label">Pinjaman</span>
    </a>
    <a href="<?= base_url('mobile/checklog') ?>" class="menu-item">
      <div class="menu-icon icon-gray"><i class="bi bi-list-check"></i></div>
      <span class="menu-label">Checklog</span>
    </a>
    <a href="<?= base_url('auth/logout') ?>" class="menu-item" onclick="return confirm('Yakin ingin logout?')">
      <div class="menu-icon icon-dark"><i class="bi bi-box-arrow-right"></i></div>
      <span class="menu-label">Logout</span>
    </a>
  </div>
</div>

<script>
  function confirmAbsen() {
    if (confirm('Yakin ingin melakukan absen masuk sekarang?')) {
      document.getElementById('absenForm').submit();
    }
  }
</script>