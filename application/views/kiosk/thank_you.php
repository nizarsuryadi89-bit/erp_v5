<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terima Kasih - TGM Print</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            padding: 20px;
        }

        .thank-you-card {
            background: white;
            border-radius: 24px;
            padding: 48px 32px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            animation: scaleIn 0.5s ease-out 0.2s both;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }

            to {
                transform: scale(1);
            }
        }

        .success-icon i {
            font-size: 48px;
            color: white;
        }

        h1 {
            font-size: 2rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 12px;
        }

        .subtitle {
            font-size: 1rem;
            color: #6b7280;
            margin-bottom: 32px;
        }

        .spk-box {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 24px;
        }

        .spk-label {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .spk-number {
            font-size: 1.5rem;
            font-weight: 800;
            color: #667eea;
            font-family: 'Courier New', monospace;
        }

        .btn-tracking {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 16px 32px;
            font-size: 1rem;
            font-weight: 600;
            width: 100%;
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }

        .btn-tracking:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-new-order {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
            border-radius: 12px;
            padding: 14px 32px;
            font-size: 0.95rem;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-new-order:hover {
            background: #f3f4f6;
            transform: translateY(-2px);
        }

        .info-text {
            font-size: 0.85rem;
            color: #9ca3af;
            margin-top: 24px;
            line-height: 1.6;
        }

        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
            margin: 24px 0;
        }
    </style>
</head>

<body>
    <div class="thank-you-card">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>

        <h1>Terima Kasih!</h1>
        <p class="subtitle">Pesanan Anda telah diproses oleh Deskprint</p>

        <div class="spk-box">
            <div class="spk-label">Nomor SPK Anda</div>
            <div class="spk-number">
                <?= $spk_no ?>
            </div>
        </div>

        <a href="<?= $tracking_url ?>" class="btn btn-tracking">
            <i class="fas fa-search-location me-2"></i>
            Lacak Pesanan Anda
        </a>



        <div class="divider"></div>

        <div class="info-text">
            <i class="fas fa-info-circle me-1"></i>
            Pesanan Anda sedang diproses oleh tim kami.<br>
            Gunakan nomor SPK di atas untuk melacak status pesanan Anda.
        </div>

        <div class="info-text mt-3">
            <strong>TGM Print</strong><br>
            Solusi Cetak Digital Terpercaya
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Auto redirect to tracking after 10 seconds
        setTimeout(() => {
            if (confirm('Lihat status pesanan sekarang?')) {
                window.location.href = '<?= $tracking_url ?>';
            }
        }, 10000);
    </script>
</body>

</html>