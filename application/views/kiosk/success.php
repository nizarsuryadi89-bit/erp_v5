<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $title ?>
    </title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .success-card {
            background: white;
            border-radius: 30px;
            padding: 50px 30px;
            text-align: center;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .check-icon {
            width: 80px;
            height: 80px;
            background: #22c55e;
            color: white;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 25px;
            animation: scaleIn 0.5s ease-out;
        }

        .ref-box {
            background: #f1f5f9;
            border-radius: 20px;
            padding: 25px;
            margin: 30px 0;
            border: 2px solid #e2e8f0;
        }

        .ref-id {
            font-size: 3rem;
            font-weight: 800;
            letter-spacing: 2px;
            color: #1e3a8a;
            display: block;
        }

        @keyframes scaleIn {
            0% {
                transform: scale(0);
            }

            100% {
                transform: scale(1);
            }
        }

        .btn-kiosk-back {
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 15px 30px;
            font-weight: 700;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .btn-kiosk-back:hover {
            background: #1e40af;
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>

    <div class="success-card">
        <div class="check-icon">
            <i class="fas fa-check"></i>
        </div>
        <h2 class="fw-800 text-dark mb-2">BERHASIL TERKIRIM!</h2>
        <p class="text-muted fw-500">Tunjukkan Nomor Referensi berikut ke Deskprint saat giliran anda tiba:</p>

        <div class="ref-box">
            <span class="text-muted small fw-bold text-uppercase">Nomor Referensi Anda:</span>
            <span class="ref-id">
                <?= $ref ?>
            </span>
        </div>

        <p class="small text-secondary mb-4 px-3">Simpan nomor ini atau ambil foto layar. Operator kami akan menarik
            data anda untuk memproses pesanan lebih cepat.</p>

        <a href="<?= site_url('kiosk') ?>" class="btn btn-kiosk-back">
            KEMBALI KE BERANDA <i class="fas fa-home ms-2"></i>
        </a>
    </div>

</body>

</html>