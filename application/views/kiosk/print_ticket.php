<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karcis Antrian -
        <?= $queue->queue_number ?>
    </title>

    <link
        href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&family=Plus+Jakarta+Sans:wght@600;800&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .ticket-container {
            background: white;
            width: 80mm;
            /* Thermal printer width */
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .ticket-header {
            border-bottom: 2px dashed #cbd5e1;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .branch-name {
            font-size: 1.2rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 5px;
        }

        .branch-location {
            font-size: 0.75rem;
            color: #64748b;
        }

        .queue-label {
            font-size: 0.9rem;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .queue-number {
            font-family: 'Roboto Mono', monospace;
            font-size: 4rem;
            font-weight: 800;
            color: #3b82f6;
            letter-spacing: 5px;
            margin: 10px 0;
            text-shadow: 2px 2px 4px rgba(59, 130, 246, 0.2);
        }

        .customer-info {
            background: #f8fafc;
            border-radius: 8px;
            padding: 12px;
            margin: 15px 0;
        }

        .customer-label {
            font-size: 0.7rem;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .customer-value {
            font-size: 0.9rem;
            color: #0f172a;
            font-weight: 700;
        }

        .qr-section {
            margin: 20px 0;
            padding: 15px;
            background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%);
            border-radius: 8px;
        }

        .qr-code {
            margin: 10px auto;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .qr-label {
            font-size: 0.85rem;
            color: #059669;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .qr-instruction {
            font-size: 0.7rem;
            color: #64748b;
            line-height: 1.4;
        }

        .ticket-footer {
            border-top: 2px dashed #cbd5e1;
            padding-top: 15px;
            margin-top: 15px;
        }

        .timestamp {
            font-family: 'Roboto Mono', monospace;
            font-size: 0.7rem;
            color: #94a3b8;
            margin-bottom: 5px;
        }

        .thank-you {
            font-size: 0.75rem;
            color: #0f172a;
            font-weight: 600;
            margin-top: 5px;
        }

        .buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-print {
            background: #3b82f6;
            color: white;
        }

        .btn-print:hover {
            background: #2563eb;
        }

        .btn-done {
            background: #10b981;
            color: white;
        }

        .btn-done:hover {
            background: #059669;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .ticket-container {
                width: 80mm;
                box-shadow: none;
                border-radius: 0;
            }

            .buttons {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="ticket-container">
        <div class="ticket-header">
            <div class="branch-name">
                <?= $branch->nama_perusahaan ?? 'TGM PRINT' ?>
            </div>
            <div class="branch-location">
                <i class="fas fa-map-marker-alt"></i>
                <?= $branch->alamat_perusahaan ?? 'Bandung' ?>
            </div>
        </div>

        <div class="queue-label">NOMOR ANTRIAN ANDA</div>
        <div class="queue-number">
            <?= $queue->queue_number ?>
        </div>

        <div class="customer-info">
            <div class="customer-label">NAMA</div>
            <div class="customer-value">
                <?= $queue->konsumen_nama ?>
            </div>
        </div>

        <div class="qr-section">
            <div class="qr-label">
                <i class="fas fa-mobile-alt me-1"></i>
                ORDER DARI HP ANDA
            </div>
            <div class="qr-code" id="qr-container"></div>
            <div class="qr-instruction">
                Scan QR code ini untuk melakukan<br>
                self order dari HP Anda sambil menunggu
            </div>
        </div>

        <div class="ticket-footer">
            <div class="timestamp">
                <i class="far fa-clock"></i>
                <?= date('d F Y • H:i', strtotime($queue->created_at)) ?>
            </div>
            <div class="thank-you">Terima kasih telah menggunakan InstaPrint</div>
        </div>

        <div class="buttons">
            <button onclick="window.print()" class="btn btn-print">
                <i class="fas fa-print"></i> PRINT
            </button>
            <a href="<?= site_url('kiosk/queue_landing?perusahaan_id=' . $branch->id_perusahaan) ?>"
                class="btn btn-done">
                <i class="fas fa-check"></i> SELESAI
            </a>
        </div>
    </div>

    <!-- QR Code Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        // Generate QR Code
        new QRCode(document.getElementById("qr-container"), {
            text: "<?= $qr_url ?>",
            width: 150,
            height: 150,
            colorDark: "#0f172a",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

        // Auto-print after 1 second (optional)
        // setTimeout(() => window.print(), 1000);
    </script>
</body>

</html>