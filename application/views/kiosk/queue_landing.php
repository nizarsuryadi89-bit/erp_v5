<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>InstaPrint - Ambil Antrian | <?= $branch_name ?></title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <style>
        :root {
            --primary: #1e3a8a;
            /* Navy Blue - ERP Theme */
            --primary-dark: #1e40af;
            --primary-light: #3b82f6;
            --accent: #60a5fa;
            /* Bright Blue Accent */
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --bg: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-radius: 16px;
            --navy-gradient: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #2563eb 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }

        .queue-container {
            max-width: 1100px;
            width: 100%;
            margin: 0 auto;
        }

        /* Header Hero - Compact */
        .header-hero {
            background: var(--navy-gradient);
            border-radius: 20px;
            padding: 25px 30px;
            text-align: center;
            color: white;
            box-shadow: 0 20px 60px rgba(30, 58, 138, 0.3);
            margin-bottom: 15px;
            position: relative;
            overflow: hidden;
            animation: slideDown 0.5s ease-out;
        }

        .header-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header-icon {
            font-size: 2.5rem;
            margin-bottom: 8px;
            animation: bounce 2s infinite;
            position: relative;
            z-index: 1;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .header-title {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .branch-badge {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 6px 18px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-block;
            border: 2px solid rgba(255, 255, 255, 0.3);
            position: relative;
            z-index: 1;
        }

        /* Main Grid Layout - Desktop 2 Columns */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        /* Flash Sale Banner - Compact */
        .flash-sale {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 15px;
            border-radius: var(--border-radius);
            text-align: center;
            margin-bottom: 15px;
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
            animation: slideUp 0.6s ease-out 0.2s both;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .flash-title {
            font-size: 1.1rem;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .flash-subtitle {
            font-size: 0.85rem;
            opacity: 0.95;
        }

        /* Form Card - Compact */
        .form-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            animation: slideUp 0.6s ease-out 0.3s both;
        }

        .info-box {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 2px solid var(--primary-light);
            border-radius: 14px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
        }

        .info-icon {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .info-text {
            font-size: 0.85rem;
            color: var(--text-main);
            font-weight: 600;
            line-height: 1.5;
        }

        .form-label {
            font-weight: 700;
            color: var(--primary);
            font-size: 0.9rem;
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            border-radius: 12px;
            padding: 14px 18px;
            border: 2px solid #e2e8f0;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all 0.3s;
            width: 100%;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(30, 58, 138, 0.1);
            outline: none;
        }

        .btn-queue {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 14px;
            font-weight: 800;
            font-size: 1.1rem;
            width: 100%;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.4);
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
        }

        .btn-queue:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(16, 185, 129, 0.5);
        }

        .btn-queue:active {
            transform: translateY(-1px);
        }

        /* Tips Banner - Compact */
        .tips-banner {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #fbbf24;
            border-radius: var(--border-radius);
            padding: 15px;
            margin-bottom: 15px;
            animation: slideUp 0.6s ease-out 0.4s both;
        }

        .tips-title {
            font-size: 1rem;
            font-weight: 800;
            color: #92400e;
            margin-bottom: 8px;
        }

        .tips-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .tips-list li {
            padding: 5px 0;
            color: #78350f;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .tips-list li i {
            color: #f59e0b;
            width: 20px;
        }

        /* WhatsApp Banner - Compact */
        .whatsapp-banner {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 15px;
            border-radius: var(--border-radius);
            text-align: center;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
            animation: slideUp 0.6s ease-out 0.5s both;
        }

        .wa-title {
            font-size: 1rem;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .wa-subtitle {
            font-size: 0.8rem;
            opacity: 0.95;
            margin-bottom: 10px;
        }

        .wa-btn {
            background: white;
            color: #059669;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 800;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            font-size: 0.9rem;
        }

        .wa-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(255, 255, 255, 0.3);
        }

        /* jQuery UI Autocomplete */
        .ui-autocomplete {
            border: none;
            border-radius: 12px;
            background: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            max-height: 250px;
            overflow-y: auto;
            padding: 8px;
            z-index: 1050 !important;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .ui-menu-item {
            list-style: none;
        }

        .ui-menu-item-wrapper {
            padding: 14px 18px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 600;
            color: var(--text-main);
        }

        .ui-menu-item-wrapper:hover,
        .ui-state-active {
            background: var(--navy-gradient) !important;
            color: white !important;
            border: none !important;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .main-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .flash-sale {
                margin-bottom: 0;
            }
        }

        @media (max-width: 575px) {
            body {
                padding: 10px;
            }

            .queue-container {
                padding: 0;
            }

            .header-hero {
                padding: 25px 20px;
                border-radius: 16px;
                margin-bottom: 12px;
            }

            .header-icon {
                font-size: 2.5rem;
            }

            .header-title {
                font-size: 1.6rem;
            }

            .main-grid {
                gap: 12px;
            }

            .form-card {
                padding: 20px 18px;
                border-radius: 16px;
            }

            .btn-queue {
                font-size: 1rem;
                padding: 15px;
            }

            .flash-title {
                font-size: 1rem;
            }

            .wa-title {
                font-size: 0.95rem;
            }
        }

        /* jQuery UI Autocomplete Dropdown Styling */
        .ui-autocomplete {
            max-height: 300px;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 9999 !important;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            padding: 8px 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .ui-menu-item {
            padding: 0;
            margin: 0;
        }

        .ui-menu-item .ui-menu-item-wrapper {
            padding: 12px 16px;
            border: none;
            margin: 0;
            cursor: pointer;
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
            color: #334155;
            font-size: 0.95rem;
        }

        .ui-menu-item .ui-menu-item-wrapper:hover,
        .ui-menu-item .ui-menu-item-wrapper.ui-state-active {
            background: linear-gradient(90deg, #eff6ff 0%, #dbeafe 100%);
            border-left-color: #1e3a8a;
            color: #1e3a8a;
            font-weight: 600;
            margin: 0;
        }

        .ui-helper-hidden-accessible {
            display: none;
        }

        /* Loading indicator for autocomplete */
        .ui-autocomplete-loading {
            background: white url('data:image/gif;base64,R0lGODlhEAAQAPIAAP///wAAAMLCwkJCQgAAAGJiYoKCgpKSkiH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWoheGxvYWQuaW5mbwAh+QQJCgAAACwAAAAAEAAQAAADMwi63P4wyklrE2MIOggZnAdOmGYJRbExwroUmcG2LmDEwnHQLVsYOd2mBzkYDAdKa+dIAAAh+QQJCgAAACwAAAAAEAAQAAADNAi63P5OjCEgG4QMu7DmikRxQlFUYDEZIGBMRVsaqHwctXXf7WEYB4Ag1xjihkMZsiUkKhIAIfkECQoAAAAsAAAAABAAEAAAAzYIujIjK8pByJDMlFYvBoVjHA70GU7xSUJhmKtwHPAKzLO9HMaoKwJZ7Rf8AYPDDzKpZBqfvwQAIfkECQoAAAAsAAAAABAAEAAAAzMIumIlK8oyhpHsnFZfhYumCYUhDAQxRIdhHBGqRoKw0R8DYlJd8z0fMDgsGo/IpHI5TAAAIfkECQoAAAAsAAAAABAAEAAAAzIIunInK0rnZBTwGPNMgQwmdsNgXGJUlIWEuR5oWUIpz8pAEAMe6TwfwyYsGo/IpFKSAAAh+QQJCgAAACwAAAAAEAAQAAADMwi6IMKQORfjdOe82p4wGccc4CEuQradylesojEMBgsUc2G7sDX3lQGBMLAJibufbSlKAAAh+QQJCgAAACwAAAAAEAAQAAADMgi63P7wCRHZnFVdmgHu2nFwlWCI3WGc3TSWhUFGxTAUkGCbtgENBMJAEJsxgMLWzpEAACH5BAkKAAAALAAAAAAQABAAAAMyCLrc/jDKSatlQtScKdceCAjDII7HcQ4EMTCpyrCuUBjCYRgHVtqlAiB1YhiCnlsRkAAAOwAAAAAAAAAAAA==') right center no-repeat;
            background-size: 16px 16px;
            padding-right: 30px;
        }
    </style>
</head>

<body>
    <div class="queue-container">
        <!-- Header Hero - Full Width -->
        <div class="header-hero">
            <div class="header-icon">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <h1 class="header-title">SELAMAT DATANG!</h1>
            <div class="welcome-text" style="font-size: 1.1rem; margin: 10px 0; position: relative; z-index: 1;">
                Anda berada di <strong><?= $branch_name ?></strong>
            </div>
            <div class="branch-badge">
                <i class="fas fa-map-marker-alt me-2"></i>
                Ambil Antrian Anda Sekarang
            </div>
        </div>

        <!-- Achievement Stats - Trust Indicators -->


        <!-- Main Grid: 2 Columns (Form Left, Banners Right) -->
        <div class="main-grid">
            <!-- LEFT COLUMN: Form Card -->
            <div class="form-card">
                <div class="info-box">
                    <div class="info-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <div class="info-text">
                        <strong>Kepuasan Anda Prioritas Kami!</strong><br>
                        Terima kasih telah mempercayai kami.<br>
                        Nikmati layanan cepat & berkualitas tinggi.
                    </div>
                </div>

                <form id="queueForm">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-user me-2"></i>Nama Lengkap
                        </label>
                        <input type="text" id="customer_name" class="form-control" placeholder="Masukkan nama Anda..."
                            required autocomplete="off">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-phone me-2"></i>Nomor HP / WhatsApp
                        </label>
                        <input type="tel" id="customer_phone" class="form-control" placeholder="08XXXXXXXXXX" required
                            inputmode="numeric">
                    </div>

                    <button type="submit" class="btn-queue">
                        <i class="fas fa-print me-2"></i> AMBIL ANTRIAN
                    </button>
                </form>

                <!-- New Customer Registration -->
                <div class="mt-4">
                    <button type="button" class="btn btn-outline-warning w-100 py-2 fw-bold"
                        style="border-width: 2px; border-radius: 12px; color: #b45309; border-color: #f59e0b;"
                        onclick="$('#register-section').slideToggle(); $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-user-plus me-2"></i>Pelanggan Baru? Daftar Di Sini</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </button>
                </div>

                <!-- Registration Form (Hidden) -->
                <div id="register-section"
                    style="display:none; background: #fffbeb; border: 2px dashed #f59e0b; border-radius: 12px; padding: 15px; margin-top: 10px; animation: fadeIn 0.3s;">
                    <h6 class="fw-bold text-dark mb-3" style="font-size: 0.9rem;"><i
                            class="fas fa-pen-fancy me-2"></i>Isi Data Diri Anda</h6>

                    <div class="mb-2">
                        <label class="small fw-bold text-muted">Nama Lengkap</label>
                        <input type="text" id="reg_name" class="form-control" placeholder="Contoh: Budi Santoso"
                            autocomplete="off">
                    </div>

                    <div class="mb-2">
                        <label class="small fw-bold text-muted">Nomor WhatsApp</label>
                        <input type="tel" id="reg_phone" class="form-control" placeholder="08xxxxxxxxxx"
                            inputmode="numeric">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="small fw-bold text-muted">Email (Opsional)</label>
                            <input type="email" id="reg_email" class="form-control" placeholder="email@contoh.com">
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold text-muted">Aamat (Opsional)</label>
                            <input type="text" id="reg_address" class="form-control" placeholder="Jalan...">
                        </div>
                    </div>

                    <button type="button" class="btn btn-warning w-100 fw-bold shadow-sm"
                        onclick="registerNewCustomer()">
                        <i class="fas fa-paper-plane me-2"></i> Daftar & Ambil Antrian
                    </button>
                </div>
            </div>

            <!-- RIGHT COLUMN: Banners Stack -->
            <div>
                <!-- Flash Sale Banner -->
                <div class="flash-sale">
                    <div class="flash-title">
                        <i class="fas fa-bolt me-2"></i>PROSES CEPAT 10 DETIK!
                    </div>
                    <div class="flash-subtitle">
                        Ambil antrian tanpa antri. Scan QR untuk order dari HP! 📱
                    </div>
                </div>

                <!-- Tips Banner -->
                <div class="tips-banner">
                    <div class="tips-title">
                        <i class="fas fa-lightbulb me-2"></i>Kenapa Memilih Kami?
                    </div>
                    <ul class="tips-list">
                        <li><i class="fas fa-check-circle me-2"></i>Kualitas Cetak Premium & Terjamin</li>
                        <li><i class="fas fa-check-circle me-2"></i>Harga Bersaing & Transparan</li>
                        <li><i class="fas fa-check-circle me-2"></i>Tim Profesional & Berpengalaman</li>
                    </ul>
                </div>

                <!-- WhatsApp Banner -->
                <div class="whatsapp-banner">
                    <div class="wa-title">
                        <i class="fab fa-whatsapp me-2"></i>Butuh Bantuan?
                    </div>
                    <div class="wa-subtitle">
                        Tim kami siap membantu Anda 24/7
                    </div>
                    <a href="https://wa.me/6281234567890?text=Halo,%20saya%20butuh%20bantuan%20untuk%20order"
                        target="_blank" class="wa-btn">
                        <i class="fab fa-whatsapp me-2"></i>Chat WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const BRANCH_ID = <?= $branch_id ?>;
        const BASE_URL = '<?= base_url() ?>';

        // jQuery UI Autocomplete for customer name (Case-Insensitive Search)
        $('#customer_name').autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: BASE_URL + '/kiosk/search_konsumen',
                    dataType: 'json',
                    data: { term: request.term },
                    success: function (data) {
                        if (data.length === 0) {
                            response([{ label: 'Tidak ada hasil', value: '', disabled: true }]);
                        } else {
                            response($.map(data, function (item) {
                                return {
                                    label: item.label, // Display name + phone in dropdown
                                    value: item.value, // Value to fill in input (name only)
                                    phone: item.phone  // Store phone for later
                                };
                            }));
                        }
                    },
                    error: function () {
                        response([{ label: 'Gagal memuat data', value: '', disabled: true }]);
                    }
                });
            },
            minLength: 2, // Start searching after 2 characters
            select: function (event, ui) {
                // Prevent selecting disabled items
                if (ui.item.disabled) {
                    return false;
                }

                // Auto-fill phone number when user selects from dropdown
                $('#customer_phone').val(ui.item.phone);

                // Optional: Show success feedback
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'success',
                    title: 'Data terisi otomatis!',
                    showConfirmButton: false,
                    timer: 1500
                });

                return true;
            },
            // Add visual feedback during search
            search: function () {
                $(this).addClass('ui-autocomplete-loading');
            },
            response: function () {
                $(this).removeClass('ui-autocomplete-loading');
            }
        });

        $('#queueForm').on('submit', function (e) {
            e.preventDefault();

            const name = $('#customer_name').val().trim();
            const phone = $('#customer_phone').val().trim();

            if (!name || !phone) {
                Swal.fire('Perhatian', 'Mohon lengkapi nama dan nomor HP', 'warning');
                return;
            }

            // Show loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Membuat nomor antrian Anda',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            // Submit to backend
            $.ajax({
                url: BASE_URL + '/kiosk/create_queue',
                method: 'POST',
                data: {
                    customer_name: name,
                    customer_phone: phone,
                    branch_id: BRANCH_ID
                },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        // Redirect to ticket page
                        window.location.href = BASE_URL + '/kiosk/print_ticket/' + response.queue_id;
                    } else {
                        Swal.fire('Error', response.message || 'Gagal membuat antrian', 'error');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    console.error('Response:', xhr.responseText);
                    Swal.fire('Error', 'Koneksi gagal: ' + error + '. Cek browser console untuk detail.', 'error');
                }
            });
        });

        // New Customer Register Logic
        function registerNewCustomer() {
            const name = $('#reg_name').val().trim();
            const phone = $('#reg_phone').val().trim();
            const email = $('#reg_email').val().trim();
            const address = $('#reg_address').val().trim();

            if (!name || !phone) {
                Swal.fire('Data Kurang', 'Nama dan Nomor WhatsApp wajib diisi!', 'warning');
                return;
            }

            // Loading
            Swal.fire({
                title: 'Mendaftarkan...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: '<?= site_url("kiosk/register_customer") ?>',
                method: 'POST',
                data: {
                    name: name,
                    phone: phone,
                    email: email,
                    address: address,
                    branch_id: BRANCH_ID
                },
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        // Auto fill main form
                        $('#customer_name').val(name);
                        $('#customer_phone').val(phone);

                        // Close regist form
                        $('#register-section').slideUp();

                        Swal.fire({
                            icon: 'success',
                            title: 'Pendaftaran Berhasil!',
                            text: res.message || 'Silakan klik tombol "AMBIL ANTRIAN" di atas untuk melanjutkan.',
                            timer: 2500,
                            showConfirmButton: false
                        });

                        // Scroll to top
                        $('html, body').animate({ scrollTop: 0 }, 'slow');
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Registration AJAX Error:', {
                        status: xhr.status,
                        statusText: status,
                        error: error,
                        response: xhr.responseText
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mendaftar',
                        text: 'Terjadi kesalahan saat mendaftar. Silakan coba lagi atau hubungi admin.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    </script>
</body>

</html>