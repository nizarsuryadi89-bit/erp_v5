<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TGM Print</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap" rel="stylesheet">
    <style>
        .hero {
            background: url('https://source.unsplash.com/1600x600/?printing,print') no-repeat center center;
            background-size: cover;
            color: white;
            padding: 100px 0;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.8);
        }

        .section-title {
            margin-bottom: 2rem;
        }

        .service-icon {
            font-size: 40px;
            color: #007bff;
        }

        footer {
            background: #343a40;
            color: white;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('website/index') ?>">TGM Print</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#tentang">Tentang</a></li>
                    <li class="nav-item"><a class="nav-link" href="#layanan">Layanan</a></li>
                    <li class="nav-item"><a class="nav-link" href="#portofolio">Portofolio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#kontak">Kontak</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('auth') ?>">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>