<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .coming-soon-card {
            background: #fff;
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            padding: 2.5rem 2rem;
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        .logo-placeholder {
            width: 70px;
            height: 70px;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem auto;
            font-size: 2.5rem;
            color: #764ba2;
        }
        .coming-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 0.5rem;
        }
        .coming-desc {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 2rem;
        }
        .countdown {
            display: flex;
            justify-content: center;
            gap: 1.2rem;
            margin-bottom: 1.5rem;
        }
        .countdown-item {
            background: #f8f9fa;
            border-radius: 0.75rem;
            padding: 0.7rem 1.1rem;
            min-width: 60px;
        }
        .countdown-item span {
            display: block;
            font-size: 1.5rem;
            font-weight: 700;
            color: #764ba2;
        }
        .countdown-item small {
            font-size: 0.8rem;
            color: #6c757d;
        }
        .footer-note {
            font-size: 0.9rem;
            color: #adb5bd;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <div class="coming-soon-card">
        <div class="logo-placeholder mb-3">
            <i class="fas fa-print"></i>
        </div>
        <div class="coming-title">Coming Soon</div>
        <div class="coming-desc">Fitur ini sedang dalam pengembangan.<br>Stay tuned for something amazing!</div>
        <div class="countdown" id="countdown">
            <div class="countdown-item">
                <span id="days">00</span>
                <small>Hari</small>
            </div>
            <div class="countdown-item">
                <span id="hours">00</span>
                <small>Jam</small>
            </div>
            <div class="countdown-item">
                <span id="minutes">00</span>
                <small>Menit</small>
            </div>
            <div class="countdown-item">
                <span id="seconds">00</span>
                <small>Detik</small>
            </div>
        </div>
        <div class="footer-note">
            &copy; <?= date('Y') ?> TGM ERP. All rights reserved.
        </div>
    </div>
    <script>
        // Set your launch date here (YYYY-MM-DD HH:MM:SS)
        const launchDate = new Date('2024-07-31T09:00:00').getTime();
        const daysEl = document.getElementById('days');
        const hoursEl = document.getElementById('hours');
        const minutesEl = document.getElementById('minutes');
        const secondsEl = document.getElementById('seconds');

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = launchDate - now;

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            daysEl.textContent = days < 10 ? '0' + days : days;
            hoursEl.textContent = hours < 10 ? '0' + hours : hours;
            minutesEl.textContent = minutes < 10 ? '0' + minutes : minutes;
            secondsEl.textContent = seconds < 10 ? '0' + seconds : seconds;
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    </script>
</body>
</html>
