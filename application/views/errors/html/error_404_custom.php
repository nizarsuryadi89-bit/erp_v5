<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | TGM ERP</title>
    <style>
        body {
            background-color: #f8f9fa;
            color: #343a40;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }
        .container {
            max-width: 600px;
            padding: 50px 40px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            border-top: 5px solid #dc3545;
        }
        .error-code {
            font-size: 130px;
            font-weight: 800;
            color: #dc3545;
            line-height: 1;
            margin-bottom: 10px;
            text-shadow: 4px 4px 0px rgba(220, 53, 69, 0.15);
            letter-spacing: -5px;
        }
        .error-title {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #212529;
        }
        .error-desc {
            font-size: 16px;
            color: #6c757d;
            margin-bottom: 35px;
            line-height: 1.6;
        }
        .btn-home {
            display: inline-block;
            padding: 14px 32px;
            background-color: #0d6efd;
            color: #fff;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
            border: 2px solid transparent;
        }
        .btn-home:hover {
            background-color: #0b5ed7;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(13, 110, 253, 0.4);
        }
        .btn-home:active {
            transform: translateY(1px);
        }
        .illustration {
            margin-bottom: 20px;
        }
        svg {
            width: 80px;
            height: auto;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="illustration">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <div class="error-code">404</div>
        <div class="error-title">Oops! Halaman Tidak Ditemukan</div>
        <div class="error-desc">Maaf, url atau link yang Anda masukkan salah, konten telah dihapus, atau Anda belum memiliki akses ke modul ini.</div>
        <a href="javascript:history.back()" class="btn-home">Kembali ke Halaman Sebelumnya</a>
    </div>
</body>
</html>
