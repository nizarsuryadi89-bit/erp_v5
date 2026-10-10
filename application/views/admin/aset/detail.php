<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $title ?>
    </title>
    <!-- Use standard Bootstrap 5 from CDN for the cleanest standalone page -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        body {
            background: #f1f5f9;
            font-family: 'Inter', sans-serif;
            color: #1e293b;
        }

        .asset-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: none;
        }

        .asset-header {
            background: linear-gradient(135deg, #16a085 0%, #1abc9c 100%);
            padding: 3rem 1.5rem 2rem;
            text-align: center;
            color: white;
            position: relative;
        }

        .asset-icon-box {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
            border: 4px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            background: rgba(255, 255, 255, 0.95);
            color: #16a085;
            font-weight: 800;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            letter-spacing: 1px;
        }

        .info-row {
            display: flex;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            align-items: center;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            flex: 0 0 40%;
            font-weight: 700;
            color: #64748b;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            flex: 1;
            font-weight: 800;
            color: #0f172a;
            text-align: right;
        }

        .footer-brand {
            text-align: center;
            padding: 2rem 1rem;
            color: #94a3b8;
            font-size: 0.8rem;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <div class="container py-4" style="max-width: 480px;">
        <div class="asset-card animate__animated animate__fadeInUp">
            <div class="asset-header">
                <div class="status-badge"><i class="fas fa-check-circle me-1"></i> DATA VALID</div>
                <?php if (!empty($aset->aset_foto) && file_exists(FCPATH . 'assets/images/aset/' . $aset->aset_foto)): ?>
                    <div
                        style="width: 120px; height: 120px; border-radius: 50%; border: 4px solid rgba(255, 255, 255, 0.4); box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1); margin: 0 auto 1.5rem; overflow: hidden; background: #fff;">
                        <img src="<?= base_url('assets/images/aset/' . $aset->aset_foto) ?>"
                            style="width: 100%; height: 100%; object-fit: cover;" alt="Foto Aset">
                    </div>
                <?php else: ?>
                    <div class="asset-icon-box">
                        <i class="fas fa-box-open"></i>
                    </div>
                <?php endif; ?>
                <h3 class="fw-bolder mb-1" style="font-weight: 900 !important;">
                    <?= htmlspecialchars(strtoupper($aset->aset_nama)) ?>
                </h3>
                <div class="opacity-75 small fw-bold mt-2" style="letter-spacing: 2px;">ID: ASET-
                    <?= str_pad($aset->aset_id, 4, '0', STR_PAD_LEFT) ?>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="info-row">
                    <div class="info-label">Kategori</div>
                    <div class="info-value">
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill shadow-sm">
                            <?= htmlspecialchars($aset->aset_kategori) ?>
                        </span>
                    </div>
                </div>

                <div class="info-row" style="background-color: #f8fafc;">
                    <div class="info-label d-flex align-items-center"><i
                            class="fas fa-map-marker-alt text-danger me-2 fs-5"></i> LOKASI</div>
                    <div class="info-value text-primary fs-5" style="letter-spacing: 0.5px;">
                        <?= htmlspecialchars(strtoupper($aset->nama_perusahaan ?? 'Pusat')) ?>
                    </div>
                </div>

                <?php if ($this->session->userdata('logged_in')): ?>
                    <!-- Harga hanya ditampilkan jika user internal yang login mem-scan (keamanan data) -->
                    <div class="info-row">
                        <div class="info-label">Nilai Aset</div>
                        <div class="info-value text-success fs-5">Rp
                            <?= number_format($aset->aset_harga, 0, ',', '.') ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="info-row flex-column align-items-start">
                    <div class="info-label mb-3 pb-2 border-bottom w-100"><i class="fas fa-info-circle me-1"></i>
                        SPESIFIKASI / KETERANGAN</div>
                    <div class="info-value text-start text-muted fw-semibold lh-lg w-100 bg-light p-3 rounded-3"
                        style="font-size: 0.9rem;">
                        <?= nl2br(htmlspecialchars($aset->aset_keterangan ?: '-')) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-brand mt-4">
            <i class="fas fa-shield-alt text-success mb-2 fs-3 d-block"></i>
            <div style="letter-spacing: 1px;">SISTEM INVENTARIS TGM PRINT</div>
            <div class="small fw-normal mt-1 opacity-75">&copy;
                <?= date('Y') ?> All Rights Reserved
            </div>
        </div>
    </div>
</body>

</html>