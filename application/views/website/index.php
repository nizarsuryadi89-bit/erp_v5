<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TGM Digital Print - Solusi Percetakan Terbaik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1a1a2e;
            --secondary: #16213e;
            --accent: #0f3460;
            --light: #f8f9fa;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: system-ui, -apple-system, 'Segoe UI', sans-serif;
            color: #333;
            line-height: 1.6;
        }

        /* Navbar */
        .navbar {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary) !important;
        }

        .nav-link {
            font-weight: 500;
            color: #555 !important;
        }

        .nav-link:hover {
            color: var(--primary) !important;
        }

        /* Hero */
        .hero {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #fff;
            padding: 100px 0 60px;
            min-height: 80vh;
            display: flex;
            align-items: center;
        }

        .hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 1.5rem;
        }

        /* Section */
        .section {
            padding: 60px 0;
        }

        .section-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .section-subtitle {
            color: #666;
            margin-bottom: 2rem;
        }

        /* Cards */
        .service-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            height: 100%;
            transition: transform 0.2s;
        }

        .service-card:hover {
            transform: translateY(-5px);
        }

        .service-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: #fff;
            font-size: 1.5rem;
        }

        .service-card h5 {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .service-card p {
            color: #666;
            font-size: 0.9rem;
        }

        /* Stats */
        .stats-section {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #fff;
            padding: 40px 0;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Portfolio */
        .portfolio-item {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .portfolio-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        /* Contact */
        .contact-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        /* Footer */
        .footer {
            background: var(--primary);
            color: #fff;
            padding: 40px 0 20px;
        }

        .footer h5 {
            font-size: 1rem;
            margin-bottom: 1rem;
        }

        .footer p,
        .footer a {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .footer a:hover {
            color: #fff;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
        }

        .btn-primary:hover {
            opacity: 0.9;
        }

        /* SPK Tracking Box */
        .tracking-box {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Login Card */
        .login-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            max-width: 350px;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 1.8rem;
            }

            .hero {
                padding: 80px 0 40px;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-printer me-2"></i>TGM Digital Print
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#beranda">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#layanan">Layanan</a></li>
                    <li class="nav-item"><a class="nav-link" href="#portofolio">Portofolio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#kontak">Kontak</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('tracking/spk') ?>"><i
                                class="bi bi-search me-1"></i>Tracking</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section id="beranda" class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4">
                    <h1>Solusi Percetakan Digital Terbaik</h1>
                    <p>Layanan percetakan berkualitas tinggi dengan teknologi modern dan harga terjangkau.</p>
                    <div class="d-flex gap-2 mb-4">
                        <a href="#layanan" class="btn btn-light">Lihat Layanan</a>
                        <a href="#kontak" class="btn btn-outline-light">Hubungi Kami</a>
                    </div>

                    <!-- SPK Tracking -->
                    <div class="tracking-box">
                        <h6 class="mb-2"><i class="bi bi-search-heart me-2"></i>Track SPK Anda</h6>
                        <form action="<?= base_url('tracking/spk') ?>" method="get" class="d-flex gap-2">
                            <input type="text" name="spk" class="form-control" placeholder="Nomor SPK..." required>
                            <button type="submit" class="btn btn-light">Track</button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6">
                    <!-- Login Form -->
                    <div class="login-card mx-auto">
                        <h5 class="text-center mb-3"><i class="bi bi-box-arrow-in-right me-2"></i>Login</h5>
                        <form action="<?= base_url('auth/proses_login') ?>" method="post">
                            <div class="mb-3">
                                <label class="form-label small">Username / Email</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="stats-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-6 col-md-3 mb-3">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Proyek Selesai</div>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <div class="stat-number">200+</div>
                    <div class="stat-label">Klien Puas</div>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <div class="stat-number">10+</div>
                    <div class="stat-label">Tahun Pengalaman</div>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Support</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Layanan -->
    <section id="layanan" class="section bg-light">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="section-title">Layanan Kami</h2>
                <p class="section-subtitle">Solusi lengkap untuk kebutuhan percetakan Anda</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-printer"></i></div>
                        <h5>Digital Printing</h5>
                        <p>Cetak cepat untuk brosur, flyer, kartu nama dengan kualitas tinggi.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-building"></i></div>
                        <h5>Offset Printing</h5>
                        <p>Cetakan jumlah besar seperti buku, katalog, majalah dengan harga ekonomis.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-palette"></i></div>
                        <h5>Desain Grafis</h5>
                        <p>Tim desainer profesional untuk desain yang menarik.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-bag"></i></div>
                        <h5>Sablon & Bordir</h5>
                        <p>Sablon dan bordir untuk kaos, topi, tas dengan kualitas terbaik.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-flag"></i></div>
                        <h5>Spanduk & Banner</h5>
                        <p>Cetak spanduk dan banner outdoor tahan cuaca.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-box"></i></div>
                        <h5>Packaging</h5>
                        <p>Solusi kemasan custom untuk produk Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Portofolio -->
    <section id="portofolio" class="section">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="section-title">Portofolio</h2>
                <p class="section-subtitle">Hasil karya terbaik kami</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="portfolio-item">
                        <img src="https://source.unsplash.com/400x300/?business-card" alt="Kartu Nama">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="portfolio-item">
                        <img src="https://source.unsplash.com/400x300/?brochure" alt="Brosur">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="portfolio-item">
                        <img src="https://source.unsplash.com/400x300/?banner" alt="Banner">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="portfolio-item">
                        <img src="https://source.unsplash.com/400x300/?packaging" alt="Packaging">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="portfolio-item">
                        <img src="https://source.unsplash.com/400x300/?tshirt" alt="Kaos">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="portfolio-item">
                        <img src="https://source.unsplash.com/400x300/?book" alt="Buku">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Kontak -->
    <section id="kontak" class="section bg-light">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="section-title">Hubungi Kami</h2>
                <p class="section-subtitle">Siap melayani kebutuhan percetakan Anda</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="contact-card">
                        <h5 class="mb-3"><i class="bi bi-envelope me-2"></i>Kirim Pesan</h5>
                        <form>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" placeholder="Nama" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="email" class="form-control" placeholder="Email" required>
                                </div>
                                <div class="col-12">
                                    <input type="tel" class="form-control" placeholder="Telepon">
                                </div>
                                <div class="col-12">
                                    <textarea class="form-control" rows="4" placeholder="Pesan Anda"
                                        required></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Kirim Pesan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="contact-card">
                        <h5 class="mb-3"><i class="bi bi-geo-alt me-2"></i>Informasi Kontak</h5>
                        <div class="mb-3">
                            <strong>Alamat</strong><br>
                            <span class="text-muted">Jl. Letnan Marsaid No.46, Bekasi Selatan</span>
                        </div>
                        <div class="mb-3">
                            <strong>Telepon</strong><br>
                            <span class="text-muted">0822-1342-4749</span>
                        </div>
                        <div class="mb-3">
                            <strong>Email</strong><br>
                            <span class="text-muted">tgmprint@gmail.com</span>
                        </div>
                        <div class="mb-3">
                            <strong>Jam Kerja</strong><br>
                            <span class="text-muted">Senin - Sabtu, 08:00 - 17:00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-3">
                    <h5><i class="bi bi-printer me-2"></i>TGM Digital Print</h5>
                    <p>Solusi percetakan terbaik untuk semua kebutuhan bisnis Anda.</p>
                </div>
                <div class="col-lg-4 mb-3">
                    <h5>Link</h5>
                    <ul class="list-unstyled">
                        <li><a href="#beranda" class="text-decoration-none">Beranda</a></li>
                        <li><a href="#layanan" class="text-decoration-none">Layanan</a></li>
                        <li><a href="<?= base_url('tracking/spk') ?>" class="text-decoration-none">Tracking SPK</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-3">
                    <h5>Ikuti Kami</h5>
                    <a href="#" class="text-decoration-none me-2"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-decoration-none me-2"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-decoration-none me-2"><i class="bi bi-whatsapp"></i></a>
                    <a href="#" class="text-decoration-none"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
            <hr class="my-3" style="border-color: rgba(255,255,255,0.2);">
            <p class="text-center mb-0">&copy; 2024 TGM Digital Print. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                e.preventDefault();
                const target = document.querySelector(a.getAttribute('href'));
                if (target) target.scrollIntoView({ behavior: 'smooth' });
            });
        });
    </script>
</body>

</html>