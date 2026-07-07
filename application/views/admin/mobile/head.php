<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
  <title><?= $title ?? 'TGM ERP' ?></title>

  <!-- CSS Libraries -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Custom Mobile App Styles -->
  <link rel="stylesheet" href="<?= base_url('assets/css/mobile_dashboard.css?v=' . time()) ?>">

  <style>
    :root {
      --primary: #1a1a2e;
      --secondary: #16213e;
      --accent: #0f3460;
      --success: #198754;
      --warning: #f59e0b;
      --danger: #dc3545;
      --info: #0dcaf0;
      --bg-light: #f8fafc;
    }

    * {
      -webkit-tap-highlight-color: transparent;
      box-sizing: border-box;
    }

    body {
      background-color: #0f172a;
      background-image:
        radial-gradient(at 0% 0%, rgba(26, 26, 46, 0.8) 0, transparent 50%),
        radial-gradient(at 50% 0%, rgba(15, 52, 96, 0.5) 0, transparent 50%),
        radial-gradient(at 100% 0%, rgba(22, 33, 62, 0.8) 0, transparent 50%);
      min-height: 100vh;
      font-family: 'Inter', sans-serif;
      margin: 0;
      padding: 0;
      color: #1e293b;
      overflow-x: hidden;
    }

    /* App Shell Wrapper */
    .app-shell {
      max-width: 100%;
      margin: 0 auto;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      position: relative;
      padding-bottom: 80px;
      /* Spacer for bottom nav */
    }

    @media (min-width: 768px) {
      .app-shell {
        max-width: 480px;
        box-shadow: 0 0 50px rgba(0, 0, 0, 0.5);
        margin: 20px auto;
        min-height: calc(100vh - 40px);
        border-radius: 30px;
        overflow: hidden;
      }

      body {
        padding: 20px 0;
        background-attachment: fixed;
      }
    }

    /* Page Headers */
    .custom-page-header {
      padding: 2rem 1.5rem 1.5rem;
      color: #fff;
      text-align: center;
    }

    .custom-page-header h5 {
      font-weight: 700;
      margin-bottom: 0.25rem;
      letter-spacing: -0.5px;
    }

    .custom-page-header p {
      font-size: 0.85rem;
      opacity: 0.7;
      margin: 0;
    }

    /* Content Container */
    .content-container {
      flex: 1;
      background: var(--bg-light);
      border-top-left-radius: 30px;
      border-top-right-radius: 30px;
      padding: 1.5rem;
      position: relative;
      z-index: 20;
      margin-top: -10px;
      box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.1);
    }

    /* Section Titles */
    .section-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1.25rem;
      padding: 0 0.25rem;
    }

    .section-title {
      font-size: 0.85rem;
      font-weight: 700;
      color: #1e293b;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      display: flex;
      align-items: center;
      gap: 0.6rem;
    }

    /* Premium Cards */
    .stat-card {
      background: #fff;
      border-radius: 20px;
      padding: 1.25rem;
      margin-bottom: 1rem;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
      border: 1px solid #f1f5f9;
    }

    /* Interactive Elements */
    .premium-btn {
      border-radius: 15px;
      font-weight: 600;
      padding: 0.8rem 1.5rem;
      transition: all 0.2s;
    }

    /* Safe Areas */
    @supports (padding-bottom: env(safe-area-inset-bottom)) {
      .app-shell {
        padding-bottom: calc(85px + env(safe-area-inset-bottom));
      }
    }
  </style>
</head>

<body>
  <div class="app-shell">
    <?php if ($this->session->userdata('is_impersonating')): ?>
      <div class="impersonation-alert animate__animated animate__pulse animate__infinite"
        style="background: #f59e0b; color: #000; padding: 10px; text-align: center; font-weight: bold; position: sticky; top: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; gap: 10px;">
        <i class="bi bi-incognito"></i>
        <span>MENYAMAR: <?= strtoupper($this->session->userdata('username')) ?></span>
        <a href="<?= base_url('pengaturan/stop_impersonating') ?>" class="btn btn-dark btn-sm rounded-pill py-0 px-2"
          style="font-size: 0.7rem;">STOP</a>
      </div>
    <?php endif; ?>