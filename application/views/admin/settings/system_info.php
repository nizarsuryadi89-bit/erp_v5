<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
  /* ==========================================================================
    PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - SYSTEM INFO
    ========================================================================== */
  :root {
    --primary-gradient: linear-gradient(135deg, #1a1c23 0%, #34495e 100%);
    --success-gradient: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
    --info-gradient: linear-gradient(135deg, #0ea5e9 0%, #38bdf8 100%);
    --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
  }

  .report-section-card {
    background: white;
    border-radius: 20px;
    border: none;
    box-shadow: var(--premium-shadow);
    overflow: hidden;
    margin-bottom: 2rem;
  }

  .section-header {
    background: var(--primary-gradient);
    padding: 1.25rem 1.75rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
  }

  .section-header h5 {
    margin: 0;
    font-weight: 800;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .info-group {
    padding: 1.5rem;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .info-group:last-child {
    border-bottom: none;
  }

  .info-label {
    font-size: 0.75rem;
    font-weight: 800;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .info-value {
    font-weight: 700;
    color: #1e293b;
  }

  .badge-premium {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 800;
    font-size: 0.7rem;
    letter-spacing: 0.5px;
  }

  @keyframes slideUpFade {
    from {
      opacity: 0;
      transform: translateY(15px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .animate-up {
    animation: slideUpFade 0.5s ease-out forwards;
  }
</style>

<div class="app-content mt-2">
  <div class="container-fluid">
    <div class="row g-4">
      <!-- App Info -->
      <div class="col-lg-6 animate-up">
        <div class="report-section-card h-100">
          <div class="section-header">
            <h5><i class="fas fa-microchip"></i> Framework & Core</h5>
            <span class="badge bg-white text-dark rounded-pill px-3 fw-bold">Active</span>
          </div>
          <div>
            <div class="info-group">
              <span class="info-label">Environment</span>
              <span class="badge bg-dark badge-premium text-white"><?= htmlspecialchars($info['environment']) ?></span>
            </div>
            <div class="info-group">
              <span class="info-label">PHP Engine</span>
              <span class="info-value text-primary">v<?= htmlspecialchars($info['php_version']) ?></span>
            </div>
            <div class="info-group">
              <span class="info-label">CodeIgniter</span>
              <span class="info-value">v<?= htmlspecialchars($info['ci_version']) ?></span>
            </div>
            <div class="info-group">
              <span class="info-label">Current Timezone</span>
              <span class="info-value"><?= htmlspecialchars($info['timezone']) ?></span>
            </div>
            <div class="info-group">
              <span class="info-label">Server Datetime</span>
              <span class="info-value font-monospace"><?= htmlspecialchars($info['datetime_now']) ?></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Database Info -->
      <div class="col-lg-6 animate-up" style="animation-delay: 0.1s">
        <div class="report-section-card h-100">
          <div class="section-header" style="background: var(--info-gradient)">
            <h5><i class="fas fa-database"></i> Persistence Layer</h5>
            <i class="fas fa-check-circle text-white"></i>
          </div>
          <div>
            <div class="info-group">
              <span class="info-label">Database Driver</span>
              <span class="info-value badge-premium bg-info-subtle text-info border border-info-subtle">
                <?= strtoupper(htmlspecialchars($info['db_driver'])) ?>
              </span>
            </div>
            <div class="info-group">
              <span class="info-label">Host Node</span>
              <span class="info-value"><?= htmlspecialchars($info['db_hostname']) ?></span>
            </div>
            <div class="info-group">
              <span class="info-label">Active Schema</span>
              <span class="info-value"><?= htmlspecialchars($info['db_database']) ?></span>
            </div>
            <div class="info-group">
              <span class="info-label">Connection Status</span>
              <span class="text-success fw-bold"><i class="fas fa-link me-1"></i> Established</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Server Info -->
      <div class="col-lg-12 animate-up" style="animation-delay: 0.2s">
        <div class="report-section-card">
          <div class="section-header" style="background: var(--success-gradient)">
            <h5><i class="fas fa-server"></i> Web Infrastructure</h5>
          </div>
          <div class="row g-0">
            <div class="col-md-4 border-end">
              <div class="info-group h-100 flex-column align-items-start gap-2">
                <span class="info-label">Web Server Software</span>
                <span class="info-value text-wrap"><?= htmlspecialchars($info['server_software']) ?></span>
              </div>
            </div>
            <div class="col-md-4 border-end">
              <div class="info-group h-100 flex-column align-items-start gap-2">
                <span class="info-label">Host Name / Identifier</span>
                <span class="info-value"><?= htmlspecialchars($info['server_name']) ?></span>
              </div>
            </div>
            <div class="col-md-4">
              <div class="info-group h-100 flex-column align-items-start gap-2">
                <span class="info-label">Server Interface / IP</span>
                <span
                  class="info-value font-monospace"><?= htmlspecialchars($info['server_addr'] ?: 'Localhost / 127.0.0.1') ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="text-center mt-4">
      <p class="small text-muted mb-0">TGM ERP System v3.0 | Generated at <?= date('Y-m-d H:i:s') ?></p>
    </div>
  </div>
</div>