<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Owner Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #0f172a;
            color: white;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            overflow: hidden;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            text-align: center;
        }
        .logo-area {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 800;
            margin: 0 0 0.5rem;
            letter-spacing: -0.5px;
        }
        p {
            color: #94a3b8;
            font-size: 0.9rem;
            margin: 0 0 2rem;
        }
        .pin-dots {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 2rem;
        }
        .dot {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #1e293b;
            border: 2px solid #334155;
            transition: all 0.2s;
        }
        .dot.filled {
            background: #3b82f6;
            border-color: #3b82f6;
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
        }
        .keypad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            max-width: 280px;
            margin: 0 auto;
        }
        .key {
            background: #1e293b;
            border: none;
            border-radius: 50px;
            height: 70px;
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.1s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .key:active {
            background: #3b82f6;
            transform: scale(0.95);
        }
        .key.action {
            background: transparent;
            font-size: 1.2rem;
            color: #94a3b8;
        }
        .key.action:active {
            color: white;
            background: rgba(255,255,255,0.1);
        }
        .error-msg {
            color: #ef4444;
            font-size: 0.85rem;
            margin-bottom: 1rem;
            height: 20px;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="logo-area">
        <i class="fas fa-lock"></i>
    </div>
    <h1>Akses Terkunci</h1>
    <p>Masukkan PIN Owner untuk melanjutkan</p>
    
    <div class="error-msg" id="errorMsg"></div>
    
    <div class="pin-dots" id="pinDots">
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
    </div>
    
    <div class="keypad">
        <button class="key" onclick="press(1)">1</button>
        <button class="key" onclick="press(2)">2</button>
        <button class="key" onclick="press(3)">3</button>
        <button class="key" onclick="press(4)">4</button>
        <button class="key" onclick="press(5)">5</button>
        <button class="key" onclick="press(6)">6</button>
        <button class="key" onclick="press(7)">7</button>
        <button class="key" onclick="press(8)">8</button>
        <button class="key" onclick="press(9)">9</button>
        <button class="key action" onclick="clearAll()"><i class="fas fa-times"></i></button>
        <button class="key" onclick="press(0)">0</button>
        <button class="key action" onclick="backspace()"><i class="fas fa-backspace"></i></button>
    </div>
</div>

<script>
    let currentPin = '';
    const MAX_PIN = 4;
    
    function updateDots() {
        const dots = document.querySelectorAll('.dot');
        dots.forEach((dot, index) => {
            if (index < currentPin.length) {
                dot.classList.add('filled');
            } else {
                dot.classList.remove('filled');
            }
        });
    }
    
    function press(num) {
        document.getElementById('errorMsg').innerText = '';
        if (currentPin.length < MAX_PIN) {
            currentPin += num.toString();
            updateDots();
            
            if (currentPin.length === MAX_PIN) {
                verifyPin();
            }
        }
    }
    
    function backspace() {
        if (currentPin.length > 0) {
            currentPin = currentPin.slice(0, -1);
            updateDots();
        }
    }
    
    function clearAll() {
        currentPin = '';
        updateDots();
    }
    
    function verifyPin() {
        // Send to server
        fetch('<?= base_url("Owner/verify_pin") ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'pin=' + currentPin
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.href = '<?= base_url("Owner/dashboard") ?>';
            } else {
                document.getElementById('errorMsg').innerText = data.msg;
                currentPin = '';
                updateDots();
            }
        });
    }
</script>

</body>
</html>
