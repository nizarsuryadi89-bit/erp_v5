<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    // Prepare Company Data (Robust check)
    $c_name = $company->nama_perusahaan ?? 'TGM Printing';
    $c_address = $company->alamat_perusahaan ?? 'Jl. Soebrantas No. 123 Pekanbaru';
    $c_phone = $company->telepon ?? $company->no_telp ?? $company->no_wa ?? '0852-6548-8902';
    $c_website = $company->website ?? 'www.tgmprint.com';
    $c_ig = $company->instagram ?? '@' . str_replace(' ', '.', strtolower($c_name));
    $c_fb = $company->facebook ?? $c_name;

    // Logo Path Logic
    $logo_path = isset($company->logo) && !empty($company->logo) ? 'assets/images/logo/' . $company->logo : 'assets/images/logo.png';
    // Cek file exists (opsional, tapi bagus)
    if (!file_exists(FCPATH . $logo_path)) {
        // Cek deep inside 'assets/images/' directly 
        if (file_exists(FCPATH . 'assets/images/' . ($company->logo ?? ''))) {
            $logo_path = 'assets/images/' . $company->logo;
        } else {
            // Fallback ke logo default
            $logo_path = 'assets/images/logo.png';
        }
    }
    $c_logo = base_url($logo_path);
    ?>
    <title>InstaPrint Display - <?= $c_name ?></title>
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts: Poppins (Modern) & Nunito (Friendly/Rounded) -->
    <link
        href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Poppins:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <style>
        body {
            background-color: #0f172a;
            /* Slate 900 */
            min-height: 100vh;
            font-family: 'Nunito', sans-serif;
            /* Menggunakan Nunito sebagai default yang ramah */
            color: white;
            overflow: hidden;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        /* Layout Utama */
        .main-container {
            flex-grow: 1;
            /* Mengisi sisa ruang selain footer */
            display: flex;
            height: calc(100vh - 60px);
            /* Kurangi tinggi footer */
        }

        /* Footer Running Text */
        .info-footer {
            height: 60px;
            background: #111;
            color: white;
            display: flex;
            align-items: center;
            border-top: 4px solid #f59e0b;
            /* Aksen kuning */
            box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.5);
            z-index: 100;
            width: 100%;
        }

        .brand-box {
            background: #f59e0b;
            color: #000;
            height: 100%;
            padding: 0 30px;
            display: flex;
            align-items: center;
            font-weight: 900;
            font-size: 1.4rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.3);
            z-index: 2;
        }

        .marquee-container {
            flex-grow: 1;
            font-size: 1.4rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            overflow: hidden;
            white-space: nowrap;
        }

        .clock-box {
            background: #1e293b;
            height: 100%;
            padding: 0 30px;
            display: flex;
            align-items: center;
            font-family: 'Poppins', sans-serif;
            /* Ganti Courier New agar tidak kaku */
            font-weight: 700;
            font-size: 2rem;
            color: #fbbf24;
            border-left: 1px solid #333;
            z-index: 2;
            min-width: 150px;
            justify-content: center;
            letter-spacing: 1px;
        }

        /* Kolom Kiri: Iklan (65%) */
        .promo-section {
            width: 65%;
            height: 100%;
            background: #000;
            position: relative;
        }

        .promo-carousel,
        .carousel-inner,
        .carousel-item {
            height: 100%;
            width: 100%;
        }

        .promo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.7;
            /* Gelapkan sedikit agar teks terbaca */
        }

        .promo-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 50px;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.95), transparent);
        }

        .promo-overlay h1 {
            font-size: 3.5rem;
            font-weight: 800;
            text-shadow: 2px 2px 0 #000;
            margin-bottom: 10px;
            color: #fbbf24;
        }

        .promo-overlay p {
            font-size: 1.8rem;
            background: rgba(255, 255, 255, 0.1);
            display: inline-block;
            padding: 5px 20px;
            border-radius: 50px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Slide Sosial Media Custom Style */
        .social-slide {
            height: 100%;
            background: linear-gradient(135deg, #094b8e 0%, #06b6d4 100%);
            display: flex;
            /* Centering content */
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }

        .social-grid {
            display: flex;
            gap: 50px;
            margin-top: 40px;
        }

        .social-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 20px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            min-width: 250px;
        }

        .social-item i {
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .social-item h3 {
            font-size: 1.5rem;
            font-weight: 700;
        }

        /* Kolom Kanan: Antrian (35%) */
        .queue-sidebar {
            width: 35%;
            height: 100%;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            display: flex;
            /* Flexbox untuk layout vertikal */
            flex-direction: column;
            padding: 20px;
            box-shadow: -10px 0 30px rgba(0, 0, 0, 0.5);
            z-index: 10;
            border-left: 1px solid #334155;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .header h1 {
            font-size: 1.8rem;
            font-weight: 800;
            margin: 0;
            color: #fbbf24;
        }

        .header p {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .current-queue {
            background: white;
            border-radius: 20px;
            padding: 30px 20px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            flex-grow: 1;
            /* Isi ruang kosong vertical (penting!) */
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 300px;
            /* Minimal tinggi agar gagah */
        }

        .queue-label {
            color: #64748b;
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-family: 'Poppins', sans-serif;
        }

        .queue-number {
            font-size: 8rem;
            /* Sedikit diperbesar */
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            letter-spacing: -2px;
        }

        .customer-name {
            font-size: 2rem;
            font-weight: 700;
            color: #0ea5e9;
            margin-top: 10px;
        }

        /* COUNTER DISPLAY FIX */
        .loket-badge {
            font-size: 1.5rem;
            font-weight: 800;
            color: #fff;
            background: #f59e0b;
            padding: 10px 30px;
            border-radius: 50px;
            display: inline-block;
            margin-top: 10px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(245, 158, 11, 0.4);
            text-transform: uppercase;
        }

        .waiting-section {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 15px;
            flex-shrink: 0;
            /* Jangan menyusut */
            max-height: 40%;
            overflow-y: hidden;
        }

        .waiting-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 10px;
            color: #94a3b8;
        }

        .waiting-grid {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .waiting-item {
            background: rgba(30, 41, 59, 0.8);
            border-radius: 10px;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-left: 4px solid #3b82f6;
        }

        .waiting-number {
            font-size: 1.6rem;
            font-weight: 800;
            margin: 0;
            color: white;
        }

        .waiting-name {
            font-size: 1rem;
            color: #cbd5e1;
        }

        .footer {
            /* Footer sidebar antrian (hidden jika tidak perlu) */
            display: none;
        }

        .blink {
            animation: blink 1.5s infinite;
        }

        @keyframes blink {

            0%,
            50%,
            100% {
                opacity: 1;
            }

            25%,
            75% {
                opacity: 0.5;
            }
        }

        /* Utility */
        .w-icon {
            width: 30px;
            text-align: center;
            display: inline-block;
        }
    </style>
</head>

<body>

    <div class="main-container">
        <!-- SEKSI 1: IKLAN / VIDEO PROFIL (KIRI) -->
        <div class="promo-section">
            <div id="promoCarousel" class="carousel slide promo-carousel" data-bs-ride="carousel"
                data-bs-interval="8000"> <!-- slide tiap 8 detik -->
                <div class="carousel-inner">

                    <!-- Slide 1: Welcome -->
                    <div class="carousel-item active">
                        <img src="https://source.unsplash.com/1600x900/?printing,press" class="promo-img" alt="Welcome"
                            onerror="this.src='https://placehold.co/1600x900/1e3c72/fff?text=TGM+Printing'">
                        <div class="promo-overlay">
                            <h1>Selamat Datang di <?= $c_name ?></h1>
                            <p><i class="fas fa-check-circle text-warning"></i> Melayani dengan Sepenuh Hati &
                                Profesional</p>
                        </div>
                    </div>

                    <!-- Slide 2: Layanan -->
                    <div class="carousel-item">
                        <img src="https://source.unsplash.com/1600x900/?design,graphic" class="promo-img" alt="Service"
                            onerror="this.src='https://placehold.co/1600x900/2a5298/fff?text=Premium+Services'">
                        <div class="promo-overlay">
                            <h1>Solusi Cetak Premium</h1>
                            <p>Banner, Merchandise, Sticker & Undangan eksklusif.</p>
                        </div>
                    </div>

                    <!-- Slide 3: Social Media (Dynamic Data) -->
                    <div class="carousel-item">
                        <div class="social-slide">
                            <h1 class="display-3 fw-bold mb-4" style="text-shadow: 4px 4px 0 #000"><i
                                    class="fas fa-thumbs-up text-warning"></i> Follow Us!</h1>
                            <p class="fs-3 mb-5 opacity-75">Dapatkan Promo Menarik & Update Terbaru</p>

                            <div class="social-grid">
                                <div class="social-item">
                                    <i class="fab fa-instagram text-danger"></i>
                                    <h3><?= $c_ig ?></h3>
                                </div>
                                <div class="social-item">
                                    <i class="fab fa-facebook text-primary"></i>
                                    <h3><?= $c_fb ?></h3>
                                </div>
                                <div class="social-item">
                                    <i class="fab fa-whatsapp text-success"></i>
                                    <h3><?= $c_phone ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- SEKSI 2: ANTRIAN (KANAN) -->
        <div class="queue-sidebar">
            <div class="header">
                <div class="mb-4 text-center">
                    <img src="<?= $c_logo ?>" alt="Logo" class="img-fluid bg-white rounded-circle p-2 shadow-lg"
                        style="width: 100px; height: 100px; object-fit: contain; border: 3px solid #f59e0b;">
                </div>
                <h1><?= $c_name ?></h1>
                <p>InstaPrint - Queue System</p>
            </div>

            <div id="current-section">
                <!-- Placeholder Loading -->
                <div class="current-queue">
                    <div class="d-flex flex-column align-items-center justify-content-center h-100" style="opacity:0.5">
                        <i class="fas fa-circle-notch fa-spin fa-3x mb-3 text-muted"></i>
                        <h4 class="text-muted">Memuat Data...</h4>
                    </div>
                </div>
            </div>

            <div class="waiting-section">
                <div class="waiting-title">
                    <i class="fas fa-list-ol me-2"></i>Antrian Menunggu (<span id="waiting-count">0</span>)
                </div>
                <div id="waiting-list" class="waiting-grid"></div>
            </div>
        </div>
    </div>

    <!-- FOOTER: RUNNING TEXT -->
    <div class="info-footer">
        <div class="brand-box">
            <span><?= $c_name ?></span>
        </div>
        <div class="marquee-container">
            <marquee scrollamount="8">
                <span class="mx-5"><i class="fab fa-instagram w-icon"></i> <?= $c_ig ?></span>
                <span class="mx-5"><i class="fab fa-facebook w-icon"></i> <?= $c_fb ?></span>
                <span class="mx-5"><i class="fab fa-whatsapp w-icon"></i> <?= $c_phone ?> (CS Online)</span>
                <span class="mx-5"><i class="fas fa-globe w-icon"></i> <?= $c_website ?></span>
                <span class="mx-5 text-warning"><i class="fas fa-star w-icon"></i> PROMO: Diskon 10% Cetak Kalender 2026
                    - Pesan Sekarang!</span>
                <span class="mx-5"><i class="fas fa-map-marker-alt w-icon"></i> Lokasi: <?= $c_address ?></span>
            </marquee>
        </div>
        <div class="clock-box" id="digital-clock">
            00:00
        </div>
    </div>

    <!-- Hidden Button for Audio -->
    <button id="btn-enable-audio"
        class="btn btn-warning position-fixed top-0 start-0 m-4 shadow-lg rounded-pill px-4 border-white"
        style="z-index:9999; font-weight:bold; border: 3px solid white;">
        <i class="fas fa-volume-up me-2"></i> KLIK MENGAKTIFKAN SUARA
    </button>

    <!-- jQuery CDN with local fallback -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script>
        // Fallback to local if CDN fails
        if (typeof jQuery == 'undefined') {
            document.write(unescape("%3Cscript src='<?= base_url('assets/js/jquery.min.js') ?>'%3E%3C/script%3E"));
        }
    </script>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>

    <script>
        const BRANCH_ID = <?= isset($branch_id) ? (int) $branch_id : 0 ?>;
        const RATE_SPEED = 0.8;

        // Initialize enabled flag
        let audioEnabled = false;
        let lastCalledQueue = null;

        // Digital Clock
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            $('#digital-clock').text(timeString);
        }
        setInterval(updateClock, 1000);
        updateClock();

        $('#btn-enable-audio').click(function () {
            audioEnabled = true;
            $(this).fadeOut();

            // Test Sound
            const u = new SpeechSynthesisUtterance("Sistem antrian siap");
            u.lang = 'id-ID';
            window.speechSynthesis.speak(u);

            Toast.fire({ icon: 'success', title: 'Suara Aktif' });

            // Re-call current if any
            if (lastCalledQueue) speakQueue(lastCalledQueue);
        });

        // Function to speak (Slowed Down & Natural)
        function speakQueue(queueNumber, loketName = 'Deskprint 1') {
            if (!audioEnabled) return;

            // Format Text: "A-001" -> "A, Kosong, Kosong, Satu"
            // Tambahkan koma agar ada jeda ejaannya
            let textToSpeak = queueNumber.replace(/-/g, ', ').split('').join(' ');

            // Format Kalimat: Tambahkan banyak koma untuk jeda natural
            const text = `Nomor Antrian . . ${textToSpeak} . . Silakan Menuju . ${loketName}`;

            // 1. OPSI UTAMA: Google Translate TTS (Suara "Mbak Google" yang Jelas)
            const playGoogleTTS = () => {
                const encodedText = encodeURIComponent(text);
                // Menggunakan client=tw-ob biasanya lebih stabil
                const googleTTSUrl = `https://translate.google.com/translate_tts?ie=UTF-8&client=tw-ob&tl=id&q=${encodedText}`;

                const audio = new Audio(googleTTSUrl);
                audio.playbackRate = 0.9; // Sedikit diperlambat (default 1.0)

                const playPromise = audio.play();

                if (playPromise !== undefined) {
                    playPromise.catch(error => {
                        console.error("Google TTS Blocked/Error, switching to Robot:", error);
                        playRobotFallback(); // Fallback jika gagal (misal internet putus)
                    });
                }
            };

            // 2. OPSI CADANGAN: Browser Native (Robot)
            const playRobotFallback = () => {
                if (!window.speechSynthesis) return;

                const voices = window.speechSynthesis.getVoices();
                // Cari voice Indonesia, atau fallback ke default
                const indoVoice = voices.find(v => v.lang.includes('id') || v.lang.includes('ID') || v.name.includes('Indonesia'));

                const utterance = new SpeechSynthesisUtterance(text);
                if (indoVoice) utterance.voice = indoVoice;
                utterance.lang = 'id-ID';
                utterance.rate = 0.7; // Kecepatan 0.7 (Agak lambat)
                utterance.pitch = 1.0;

                window.speechSynthesis.cancel();
                window.speechSynthesis.speak(utterance);
            };

            // Eksekusi
            setTimeout(playGoogleTTS, 500);
        }

        // Ensure voices loaded
        if (window.speechSynthesis.onvoiceschanged !== undefined) {
            window.speechSynthesis.onvoiceschanged = window.speechSynthesis.getVoices;
        }

        function updateDisplay() {
            const params = BRANCH_ID > 0 ? { branch_id: BRANCH_ID } : {};
            $.ajax({
                url: '<?= site_url("kiosk/get_queue_status") ?>',
                method: 'GET',
                data: params,
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        // 1. Update Current
                        if (res.current) {
                            const loketName = res.current.loket_name || 'Deskprint 1';

                            const html = `
                                <div class="queue-label blink"><i class="fas fa-bell"></i> PANGGILAN</div>
                                <div class="queue-number">${res.current.queue_number}</div>
                                <div class="loket-badge"><i class="fas fa-print me-2"></i>${loketName}</div>
                                <div class="customer-name">${res.current.konsumen_nama || 'Pelanggan'}</div>
                            `;

                            let wrapper = $('#current-section').find('.current-queue');
                            if (wrapper.length === 0) {
                                $('#current-section').html('<div class="current-queue">' + html + '</div>');
                            } else {
                                wrapper.html(html);
                            }

                            if (lastCalledQueue !== res.current.queue_number) {
                                lastCalledQueue = res.current.queue_number;
                                speakQueue(res.current.queue_number, loketName);
                            }
                        } else {
                            // Empty
                            if (lastCalledQueue !== null) { lastCalledQueue = null; }
                            $('#current-section').html(`
                                <div class="current-queue">
                                    <div class="d-flex flex-column align-items-center justify-content-center h-100" style="opacity:0.5">
                                        <i class="fas fa-mug-hot fa-4x mb-3"></i>
                                        <h3>Menunggu Antrian</h3>
                                    </div>
                                </div>
                             `);
                        }

                        // 2. Update Waiting
                        $('#waiting-count').text(res.waiting_count);
                        if (res.waiting && res.waiting.length > 0) {
                            let waitingHtml = '';
                            // Hanya ambil 4 antrian teratas biar muat
                            res.waiting.slice(0, 4).forEach(q => {
                                waitingHtml += `
                                    <div class="waiting-item">
                                        <div class="waiting-number">${q.queue_number}</div>
                                        <div class="waiting-name text-truncate" style="max-width:150px;">${q.konsumen_nama || 'Customer'}</div>
                                    </div>
                                `;
                            });
                            $('#waiting-list').html(waitingHtml);
                        } else {
                            $('#waiting-list').html('<div class="text-center py-3 opacity-50"><i>Tidak ada antrian</i></div>');
                        }
                    }
                },
                error: function (e) { /* Silent fail */ }
            });
        }

        setInterval(updateDisplay, 3000);
        updateDisplay();
    </script>

</body>

</html>