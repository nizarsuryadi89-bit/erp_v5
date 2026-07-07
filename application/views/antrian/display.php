<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Antrian - TGM ERP</title>
    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css'); ?>">

    <style>
        :root {
            --bg-color: #121212;
            --header-bg: #000000;
            --header-text: #ffffff;
            --main-panel-bg: #1e1e1e;
            /* Monochrome Scale */
            --card-1: #333333;
            --card-2: #333333;
            --card-3: #333333;
            --card-4: #333333;
            --footer-bg: #000000;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-color);
            background-image: url('');
            background-repeat: no-repeat;
            background-position: center center;
            background-attachment: fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            color: #e0e0e0;
        }

        /* HEADER */
        .header {
            background: var(--header-bg);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 4px solid rgba(0, 0, 0, 0.1);
            height: 100px;
        }

        .header-logo img {
            height: 60px;
            margin-right: 15px;
            background: white;
            padding: 5px;
            border-radius: 5px;
        }

        .header-text h1 {
            font-size: 24px;
            margin: 0;
            font-weight: 700;
            text-transform: uppercase;
        }

        .header-text p {
            margin: 0;
            font-size: 14px;
            opacity: 0.9;
        }

        .header-clock {
            text-align: right;
        }

        .time-display {
            font-size: 36px;
            font-weight: 700;
            line-height: 1;
        }

        .date-display {
            font-size: 14px;
            font-weight: 400;
            opacity: 0.8;
        }

        /* MAIN BODY */
        .main-container {
            flex: 1;
            padding: 20px;
            display: grid;
            grid-template-columns: 35% 65%;
            /* Left 35%, Right 65% */
            grid-template-rows: 1fr 180px;
            /* Top part fills, Bottom part fixed height */
            gap: 20px;
            height: calc(100vh - 140px);
            /* Minus header & marquee */
        }

        /* LEFT: ACTIVE NUMBER */
        .active-panel {
            grid-column: 1 / 2;
            grid-row: 1 / 2;
            background: var(--main-panel-bg);
            border-radius: 10px;
            color: white;
            display: flex;
            flex-direction: column;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }

        .active-header {
            background: rgba(0, 0, 0, 0.1);
            padding: 15px;
            text-align: center;
            font-size: 28px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .active-number-box {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgba(255, 255, 255, 0.1);
        }

        .active-number {
            font-size: 140px;
            font-weight: 900;
        }

        .active-footer {
            background: rgba(0, 0, 0, 0.2);
            padding: 15px;
            text-align: center;
            font-size: 32px;
            font-weight: 700;
        }

        /* RIGHT: CAROUSEL */
        .media-panel {
            grid-column: 2 / 3;
            grid-row: 1 / 2;
            background: #222;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .carousel,
        .carousel-inner,
        .carousel-item {
            height: 100%;
        }

        .carousel-item img {
            object-fit: cover;
            height: 100%;
            width: 100%;
        }

        /* BOTTOM GRID cards (Using Grid Row 2) */
        .bottom-grid {
            grid-column: 1 / 3;
            grid-row: 2 / 3;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .info-card {
            border-radius: 10px;
            color: white;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            border-left: 5px solid #555;
        }

        /* Specific Colors (Monochrome Borders) */
        .card-pink {
            background: var(--card-1);
            border-left-color: #ffffff;
            /* High Priority */
        }

        .card-orange {
            background: var(--card-2);
            border-left-color: #aaaaaa;
            /* Medium Priority */
        }

        .card-red {
            background: var(--card-3);
            border-left-color: #666666;
            /* WIP New */
        }

        .card-blue {
            background: var(--card-4);
            border-left-color: #444444;
            /* WIP Process */
        }

        .card-header {
            background: rgba(0, 0, 0, 0.1);
            padding: 10px;
            text-align: center;
            font-size: 18px;
            font-weight: 600;
        }

        .card-body-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 42px;
            font-weight: 800;
        }

        .card-sub {
            padding: 5px;
            text-align: center;
            font-size: 14px;
            background: rgba(0, 0, 0, 0.05);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* MARQUEE FOOTER */
        .marquee-footer {
            background: #0d1e30;
            color: white;
            height: 40px;
            display: flex;
            align-items: center;
            overflow: hidden;
            white-space: nowrap;
            border-top: 2px solid #ffffff;
        }

        .marquee-content {
            display: inline-block;
            padding-left: 100%;
            animation: marquee 20s linear infinite;
        }

        @keyframes marquee {
            0% {
                transform: translate(0, 0);
            }

            100% {
                transform: translate(-100%, 0);
            }
        }

        /* Overlay */
        #clickOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            cursor: pointer;
            flex-direction: column;
        }
    </style>
</head>

<body>

    <!-- OVERLAY UTK AUDIO -->
    <div id="clickOverlay">
        <div style="font-size: 80px; margin-bottom: 20px;"><i class="fas fa-hand-pointer"></i></div>
        <h1>KLIK LAYAR UNTUK MEMULAI</h1>
        <p>Aplikasi Display Antrian TGM ERP</p>
    </div>

    <!-- HEADER -->
    <div class="header">
        <div class="d-flex align-items-center">
            <div class="header-logo">
                <img src="<?= isset($perusahaan['logo']) && !empty($perusahaan['logo']) ? base_url('assets/images/' . $perusahaan['logo']) : base_url('assets/images/logo.png') ?>"
                    onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgdmlld0JveD0iMCAwIDYwIDYwIj4KICA8cmVjdCB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIGZpbGw9IiMzMzMiIC8+PC9zdmc+'"
                    alt="Logo">
            </div>

        </div>
        <div class="header-clock">
            <div class="time-display" id="clockTime">00:00:00</div>
            <div class="date-display" id="clockDate">Senin, 01 Januari 2024</div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-container">

        <!-- 1. LEFT: ACTIVE CALL -->
        <div class="active-panel">
            <div class="active-header">NOMOR ANTRIAN</div>
            <div class="active-number-box">
                <div class="active-number" id="nomorSekarang">---</div>
            </div>
            <div class="active-footer" id="counterSekarang">LOKET --</div>
        </div>

        <!-- 2. RIGHT: MEDIA SLIDER -->
        <!-- 2. RIGHT: MEDIA SLIDER (YOUTUBE) -->
        <div class="media-panel">
            <iframe width="100%" height="100%"
                src="https://www.youtube.com/embed/uIbaHoN_TMc?list=RDuIbaHoN_TMc&autoplay=1&mute=1&loop=1&controls=0"
                title="YouTube video player" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen style="border-radius: 10px;">
            </iframe>
        </div>

        <!-- 3. BOTTOM: GRID INFO (WIP & ANTRIAN) -->
        <div class="bottom-grid">

            <!-- Card 1: Next Queue -->
            <div class="info-card card-pink">
                <div class="card-header"><i class="fas fa-bullhorn"></i> Antrian Berikutnya</div>
                <div class="card-body-content" id="nextQueue">---</div>
                <div class="card-sub">Menunggu Dipanggil</div>
            </div>

            <!-- Card 2: Total Waiting -->
            <div class="info-card card-orange">
                <div class="card-header"><i class="fas fa-users"></i> Sisa Antrian</div>
                <div class="card-body-content" id="totalWaiting">0</div>
                <div class="card-sub">Orang Menunggu</div>
            </div>

            <!-- Card 3: WIP 1 -->
            <div class="info-card card-red">
                <div class="card-header"><i class="fas fa-file-invoice"></i> WIP Terkini</div>
                <div class="card-body-content" style="font-size: 24px;" id="wip1_spk">---</div>
                <div class="card-sub" id="wip1_product">Belum ada data</div>
            </div>

            <!-- Card 4: WIP 2 -->
            <div class="info-card card-blue">
                <div class="card-header"><i class="fas fa-cogs"></i> WIP Proses</div>
                <div class="card-body-content" style="font-size: 24px;" id="wip2_spk">---</div>
                <div class="card-sub" id="wip2_product">Belum ada data</div>
            </div>

        </div>
    </div>

    <!-- FOOTER MARQUEE -->
    <div class="marquee-footer">
        <div class="marquee-content">
            Selamat Datang di TGM ERP Antrian System. Budayakan antri untuk kenyamanan bersama. Jam operasional
            Senin-Jumat 08.00 - 17.00 WIB. Terima kasih atas kunjungan Anda.
        </div>
    </div>

    <!-- HIDDEN BTN -->
    <button id="btnTestSound" style="position:fixed;bottom:0;right:0;opacity:0;">Test</button>

    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
    <script>
        // CONFIG
        const counterId = <?= isset($counter['counter_id']) ? $counter['counter_id'] : 1; ?>;
        let lastAntrian = '';
        let audioEnabled = false;

        // AUDIO SETUP
        const synth = window.speechSynthesis;
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        let voices = [];

        function loadVoices() { voices = synth.getVoices(); }
        loadVoices();
        if (speechSynthesis.onvoiceschanged !== undefined) speechSynthesis.onvoiceschanged = loadVoices;

        // INIT
        function startSystem() {
            if (!audioEnabled) {
                audioEnabled = true;
                document.getElementById('clickOverlay').style.display = 'none';
                if (audioCtx.state === 'suspended') audioCtx.resume();
                speak("Sistem Antrian Siap", false);
                if (document.documentElement.requestFullscreen) {
                    // document.documentElement.requestFullscreen().catch(()=>{});
                }
            }
        }
        document.getElementById('clickOverlay').addEventListener('click', startSystem);
        document.getElementById('btnTestSound').addEventListener('click', () => {
            startSystem(); speak("Test Audio 1 2 3", true);
        });

        // AUDIO FUNCTIONS
        function playChime() {
            if (audioCtx.state === 'suspended') audioCtx.resume();
            const t = audioCtx.currentTime;

            // Ding
            const o1 = audioCtx.createOscillator();
            const g1 = audioCtx.createGain();
            o1.connect(g1); g1.connect(audioCtx.destination);
            o1.frequency.value = 523.25;
            g1.gain.setValueAtTime(0.5, t);
            g1.gain.exponentialRampToValueAtTime(0.001, t + 1.2);
            o1.start(t); o1.stop(t + 1.2);

            // Dong
            const o2 = audioCtx.createOscillator();
            const g2 = audioCtx.createGain();
            o2.connect(g2); g2.connect(audioCtx.destination);
            o2.frequency.setValueAtTime(392.00, t + 0.6);
            g2.gain.setValueAtTime(0.5, t + 0.6);
            g2.gain.exponentialRampToValueAtTime(0.001, t + 2.0);
            o2.start(t + 0.6); o2.stop(t + 2.0);
        }

        function speak(text, chime = true) {
            if (!audioEnabled) return;
            synth.cancel();
            if (chime) playChime();
            setTimeout(() => {
                const u = new SpeechSynthesisUtterance(text);
                let v = voices.find(x => x.name.includes("Google Bahasa Indonesia")) || voices.find(x => x.lang.includes("id"));
                if (v) u.voice = v;
                u.lang = 'id-ID';
                u.rate = 0.85;
                synth.speak(u);

                const el = document.querySelector('.active-panel');
                el.style.backgroundColor = "#ffffff"; // Flash White
                el.style.color = "#000"; // Black text during flash
                setTimeout(() => {
                    el.style.backgroundColor = "";
                    el.style.color = "";
                }, 600);
            }, chime ? 1500 : 0);
        }

        function formatSpeak(num, loc) {
            if (!num) return "";
            let s = "";
            for (let c of num) {
                if (c === '-') s += " ";
                else if (!isNaN(parseInt(c))) {
                    const words = ['kosong', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan'];
                    s += words[parseInt(c)] + " ";
                } else s += c + " ";
            }
            let locName = loc.replace(/Counter/gi, 'Deskprint');
            // If it implies a code/number without specific type, prepend Deskprint
            if (!/deskprint/i.test(locName) && !/loket/i.test(locName)) {
                locName = "Deskprint " + locName;
            }
            return "Nomor Antrian, " + s + ",, Silakan ke " + locName;
        }

        // POLLING
        setInterval(() => {
            const d = new Date();
            document.getElementById('clockTime').innerText = d.toLocaleTimeString('id-ID', { hour12: false });
            document.getElementById('clockDate').innerText = d.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }, 1000);

        setInterval(() => {
            fetch('<?= base_url('antrian/get_data_display'); ?>?counter_id=' + counterId, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(r => r.text())
                .then(txt => {
                    try {
                        const data = JSON.parse(txt);

                        // 1. Active Call
                        if (data.antrian_sekarang) {
                            const num = data.antrian_sekarang.nomor_antrian;
                            // Prioritize master_counter_kode (current config) then ticket's code
                            const loc = data.antrian_sekarang.master_counter_kode || data.antrian_sekarang.counter_kode || data.antrian_sekarang.counter_nama || 'Loket --';
                            const time = data.antrian_sekarang.waktu_panggil;
                            document.getElementById('nomorSekarang').innerText = num;
                            document.getElementById('counterSekarang').innerText = loc.toUpperCase();

                            if (time && time !== lastAntrian) {
                                if (data.antrian_sekarang.status === 'dipanggil') {
                                    lastAntrian = time;
                                    speak(formatSpeak(num, loc), true);
                                } else lastAntrian = time;
                            }
                        } else {
                            document.getElementById('nomorSekarang').innerText = "---";
                            document.getElementById('counterSekarang').innerText = "OFFLINE";
                        }

                        // 2. Waiting List Stats
                        if (data.antrian_menunggu && data.antrian_menunggu.length > 0) {
                            document.getElementById('nextQueue').innerText = data.antrian_menunggu[0].nomor_antrian;
                            document.getElementById('totalWaiting').innerText = data.antrian_menunggu.length;
                        } else {
                            document.getElementById('nextQueue').innerText = "-";
                            document.getElementById('totalWaiting').innerText = "0";
                        }

                        // 3. WIP Stats
                        if (data.production_wip && data.production_wip.length > 0) {
                            const w1 = data.production_wip[0];
                            document.getElementById('wip1_spk').innerText = w1.nospk;
                            document.getElementById('wip1_product').innerText = w1.product_nama.substring(0, 20) + '...';

                            if (data.production_wip.length > 1) {
                                const w2 = data.production_wip[1];
                                document.getElementById('wip2_spk').innerText = w2.nospk;
                                document.getElementById('wip2_product').innerText = w2.product_nama.substring(0, 20) + '...';
                            }
                        } else {
                            document.getElementById('wip1_spk').innerText = "---";
                            document.getElementById('wip1_product').innerText = "Idle";
                            document.getElementById('wip2_spk').innerText = "---";
                            document.getElementById('wip2_product').innerText = "Idle";
                        }

                    } catch (e) { console.error(e); }
                });
        }, 1500);

    </script>
</body>

</html>