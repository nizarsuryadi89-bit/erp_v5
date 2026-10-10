<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $title ?>
    </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --primary: #1e3a8a;
            --primary-dark: #172554;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #0f172a;
            --light: #f8fafc;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .dashboard-header {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .dashboard-header h1 {
            font-weight: 800;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
            font-size: 2.5rem;
        }

        .dashboard-header p {
            color: #64748b;
            margin: 0.5rem 0 0 0;
            font-size: 1.1rem;
        }

        .branch-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .branch-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            border-color: var(--primary);
        }

        .branch-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f1f5f9;
        }

        .branch-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            margin-right: 1rem;
        }

        .branch-title {
            flex: 1;
        }

        .branch-title h3 {
            margin: 0;
            font-weight: 700;
            color: var(--dark);
            font-size: 1.5rem;
        }

        .branch-title small {
            color: #64748b;
            font-size: 0.9rem;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .btn-action {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            border: 2px solid;
            font-size: 0.95rem;
        }

        .btn-queue {
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            color: white;
            border-color: var(--success);
        }

        .btn-queue:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
            color: white;
        }

        .btn-display {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border-color: var(--primary);
        }

        .btn-display:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
            color: white;
        }

        .btn-control {
            background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
            color: white;
            border-color: var(--warning);
        }

        .btn-control:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
            color: white;
        }

        .quick-links {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin-top: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .quick-links h5 {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 1rem;
        }

        .quick-link-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            text-decoration: none;
            color: #475569;
            transition: all 0.3s ease;
        }

        .quick-link-item:hover {
            background: #f1f5f9;
            color: var(--primary);
            padding-left: 1rem;
        }

        .quick-link-item i {
            margin-right: 0.75rem;
            width: 20px;
        }

        @media (max-width: 768px) {
            .dashboard-header h1 {
                font-size: 1.8rem;
            }

            .action-buttons {
                grid-template-columns: 1fr;
            }

            .branch-header {
                flex-direction: column;
                text-align: center;
            }

            .branch-icon {
                margin-right: 0;
                margin-bottom: 1rem;
            }
        }

        .badge-id {
            background: #e0e7ff;
            color: #3730a3;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .footer-note {
            text-align: center;
            color: white;
            margin-top: 3rem;
            font-size: 0.9rem;
            opacity: 0.9;
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <!-- Header -->
        <div class="dashboard-header">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h1><i class="fas fa-th-large me-2"></i> Panel Utama InstaPrint</h1>
                    <p><i class="fas fa-info-circle me-1"></i> Akses cepat ke semua cabang - Antrian & Monitor Layar</p>
                </div>
                <div>
                    <a href="<?= base_url('kiosk/data_order') ?>" class="btn btn-outline-primary rounded-pill px-4">
                        <i class="fas fa-database me-2"></i> Data Pesanan
                    </a>
                </div>
            </div>
        </div>

        <!-- Branch Cards -->
        <?php if (!empty($branches)): ?>
            <?php foreach ($branches as $branch): ?>
                <div class="branch-card animate__animated animate__fadeInUp">
                    <div class="branch-header">
                        <div class="branch-icon">
                            <i class="fas fa-store"></i>
                        </div>
                        <div class="branch-title">
                            <h3>
                                <?= $branch->nama_perusahaan ?>
                            </h3>
                            <small><span class="badge-id">ID: #
                                    <?= $branch->id_perusahaan ?>
                                </span></small>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <a href="<?= site_url('kiosk/queue_landing?perusahaan_id=' . $branch->id_perusahaan) ?>"
                            class="btn-action btn-queue" target="_blank">
                            <i class="fas fa-ticket-alt"></i>
                            Ambil Antrian
                        </a>

                        <a href="<?= site_url('kiosk/display_board?perusahaan_id=' . $branch->id_perusahaan) ?>"
                            class="btn-action btn-display" target="_blank">
                            <i class="fas fa-tv"></i>
                            Layar Antrian
                        </a>

                        <a href="<?= site_url('kiosk/queue_control?perusahaan_id=' . $branch->id_perusahaan) ?>"
                            class="btn-action btn-control" target="_blank">
                            <i class="fas fa-cog"></i>
                            Kontrol Antrian
                        </a>

                        <a href="<?= site_url('kiosk/order_form?perusahaan_id=' . $branch->id_perusahaan) ?>" class="btn-action"
                            style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white; border-color: #8b5cf6;"
                            target="_blank">
                            <i class="fas fa-shopping-cart"></i>
                            Formulir Pesanan
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="branch-card text-center">
                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                <h4>Tidak ada cabang yang tersedia</h4>
                <p class="text-muted">Silakan tambahkan cabang di menu Perusahaan</p>
            </div>
        <?php endif; ?>

        <!-- Quick Links -->
        <div class="quick-links">
            <h5><i class="fas fa-link me-2"></i> Tautan Cepat Lainnya</h5>
            <a href="<?= base_url('kiosk/data_order') ?>" class="quick-link-item">
                <i class="fas fa-clipboard-list"></i>
                Data Pesanan Kiosk
            </a>
            <a href="<?= base_url('transaksi/createspk') ?>" class="quick-link-item">
                <i class="fas fa-file-invoice"></i>
                Impor Pesanan ke SPK
            </a>
            <a href="<?= base_url('dashboard') ?>" class="quick-link-item">
                <i class="fas fa-home"></i>
                Kembali ke Dashboard Admin
            </a>
        </div>

        <!-- Footer -->
        <div class="footer-note">
            <p><i class="fas fa-heart text-danger"></i> Sistem Kiosk InstaPrint • Didukung oleh TGM Print</p>
            <p><small>Halaman ini untuk admin dan operator. Pastikan semua tautan dibuka di tab terpisah.</small></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>