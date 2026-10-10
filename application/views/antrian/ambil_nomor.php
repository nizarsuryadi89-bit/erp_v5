<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambil Nomor Antrian</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- AdminLTE & Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/adminlte.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">

    <style>
        :root {
            --bg-dark: #121212;
            --card-bg: #1e1e1e;
            --text-primary: #ffffff;
            --text-secondary: #b0b0b0;
            --accent-color: #333333;
            --modal-bg: #222222;
        }

        body {
            background: var(--bg-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Source Sans 3', sans-serif;
            position: relative;
            overflow-x: hidden;
            color: var(--text-primary);
        }

        .header {
            text-align: center;
            color: var(--text-primary);
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 10px;
            letter-spacing: -1px;
            text-transform: uppercase;
        }

        .header p {
            font-size: 1.2rem;
            color: var(--text-secondary);
            font-weight: 300;
        }

        /* Card Style */
        .card-custom {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            border: 1px solid #333;
            padding: 2rem;
            position: relative;
            overflow: hidden;
            color: white;
        }

        .counter-btn {
            background: transparent;
            border: 2px solid #555;
            border-radius: 8px;
            padding: 40px 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            width: 100%;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .counter-btn:hover {
            border-color: #fff;
            background: #2a2a2a;
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.5);
        }

        .counter-btn:active {
            transform: translateY(-2px);
        }

        .counter-btn i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #fff;
        }

        .counter-btn-text {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .counter-btn-label {
            font-size: 0.9rem;
            color: var(--text-secondary);
            font-weight: 400;
        }

        .info-text {
            color: var(--text-secondary);
            margin-top: 30px;
            font-size: 0.95rem;
            text-align: center;
            line-height: 1.6;
        }

        /* Modal Styles */
        .modal-content {
            background: var(--modal-bg);
            color: white;
            border-radius: 10px;
            border: 1px solid #444;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        }

        .modal-header {
            background: #111;
            color: white;
            border-bottom: 1px solid #333;
            border-radius: 10px 10px 0 0;
            padding: 1.5rem;
        }

        .modal-body {
            padding: 2.5rem;
            text-align: center;
        }

        .nomor-antrian {
            font-size: 5rem;
            font-weight: 800;
            color: #fff;
            margin: 1.5rem 0;
            font-family: 'Source Sans 3', monospace;
            line-height: 1;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        }

        .counter-info {
            font-size: 1.2rem;
            color: var(--text-secondary);
            font-weight: 300;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .keterangan {
            background: #111;
            padding: 1.5rem;
            border: 1px solid #444;
            margin: 1.5rem 0;
            border-left: 4px solid #fff;
            text-align: left;
        }

        .btn-print {
            background: #fff;
            color: #000;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-print:hover {
            background: #ddd;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.1);
        }

        .btn-close-modal {
            background: transparent;
            color: #fff;
            border: 1px solid #555;
            padding: 12px 30px;
            border-radius: 5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            margin-left: 10px;
        }

        .btn-close-modal:hover {
            background: #333;
            color: white;
            border-color: #777;
        }

        /* Loading Spinner */
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="container-ambil">
        <div class="header">
            <h1>SISTEM ANTRIAN</h1>
            <p><?= isset($perusahaan['nama_perusahaan']) ? $perusahaan['nama_perusahaan'] : 'TGM ERP'; ?></p>
        </div>

        <div class="card-custom">
            <button class="counter-btn" onclick="ambilNomor()">
                <i class="fas fa-ticket-alt"></i>
                <div class="counter-btn-text">Ambil Nomor Antrian</div>
                <div class="counter-btn-label">Sistem akan otomatis menentukan deskprint kosong</div>
            </button>

            <div class="info-text">
                <i class="fas fa-info-circle me-1"></i> Tekan tombol di atas. Sistem akan otomatis menempatkan Anda ke
                deskprint yang tersedia.
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: #111; border: 1px solid #444; color: white;">
                <div class="modal-body">
                    <div class="spinner"></div>
                    <div class="loading-text" style="font-weight: 300; letter-spacing: 1px;">MENGAMBIL NOMOR ANTRIAN...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white"><i class="fas fa-check-circle me-2"></i> NOMOR ANTRIAN ANDA</h5>
                </div>
                <div class="modal-body">
                    <div class="counter-info">COUNTER <strong id="selectedCounter"></strong></div>
                    <div class="nomor-antrian" id="nomorAntrian">A-001</div>

                    <div class="keterangan">
                        <div class="mb-2">
                            <small class="text-secondary text-uppercase"
                                style="font-size: 0.75rem; letter-spacing: 1px;">Waktu Ambil</small><br>
                            <strong id="waktuAmbil" class="text-white"></strong>
                        </div>
                        <div>
                            <small class="text-secondary text-uppercase"
                                style="font-size: 0.75rem; letter-spacing: 1px;">Estimasi Tunggu</small><br>
                            <strong id="estimasiWaktu" class="text-white">~ 5 Menit</strong>
                        </div>
                    </div>

                    <div class="alert alert-dark border-secondary" style="font-size: 0.9rem; color: #b0b0b0;">
                        <i class="fas fa-bell me-2 text-warning"></i> Mohon menunggu, nomor Anda akan dipanggil.
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        <button type="button" class="btn-print" onclick="printKarcis()">
                            <i class="fas fa-print me-2"></i> Cetak Karcis
                        </button>
                        <button type="button" class="btn-close-modal" onclick="location.reload()">
                            <i class="fas fa-redo me-2"></i> Ambil Lagi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-danger">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-circle me-2"></i> TERJADI KESALAHAN</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="errorMessage" class="text-danger mb-4">Gagal mengambil nomor antrian. Silakan coba lagi.</p>
                    <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">TUTUP</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentAntrian = null;
        let printWindow = null;

        function ambilNomor(counterId) {
            // Show loading
            new bootstrap.Modal(document.getElementById('loadingModal')).show();

            // Prepare body
            let body = '';
            if (counterId) {
                body = 'counter_id=' + encodeURIComponent(counterId);
            }

            fetch('<?= base_url('antrian/ambil_nomor_ajax'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: body
            })
                .then(response => response.json())
                .then(data => {
                    // Hide loading
                    bootstrap.Modal.getInstance(document.getElementById('loadingModal')).hide();

                    if (data.status === 'success') {
                        currentAntrian = data.data;

                        // Set modal data
                        const counterLabel = data.data.counter_kode ? 'DESK ' + data.data.counter_kode : data.data.counter_nama;
                        document.getElementById('selectedCounter').textContent = counterLabel;
                        document.getElementById('nomorAntrian').textContent = data.data.nomor_antrian;
                        document.getElementById('waktuAmbil').textContent = new Date(data.data.waktu_ambil).toLocaleString('id-ID', {
                            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
                        });

                        // Show success modal
                        new bootstrap.Modal(document.getElementById('successModal')).show();

                        // Auto reload after 5 seconds to be ready for next customer
                        setTimeout(() => {
                            location.reload();
                        }, 5000);

                        // Handle print DIRECT
                        const printUrl = '<?= base_url('antrian/print_karcis_direct/'); ?>' + currentAntrian.antrian_id;
                        fetch(printUrl)
                            .then(r => r.json())
                            .then(res => {
                                if (res.status === 'error') {
                                    alert("Gagal Print: " + res.message);
                                }
                            });
                    } else {
                        handleError(data.message || 'Gagal mengambil nomor antrian');
                    }
                })
                .catch(error => {
                    handleError(error);
                });
        }

        function handleError(msg) {
            bootstrap.Modal.getInstance(document.getElementById('loadingModal')).hide();
            document.getElementById('errorMessage').textContent = msg;
            new bootstrap.Modal(document.getElementById('errorModal')).show();
            if (printWindow && !printWindow.closed) printWindow.close();
        }

        function printKarcis() {
            if (!currentAntrian) return;
            const printUrl = '<?= base_url('antrian/print_karcis_direct/'); ?>' + currentAntrian.antrian_id;
            fetch(printUrl)
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        alert("Mencetak ulang...");
                    } else {
                        alert("Gagal Print: " + res.message);
                    }
                });
        }
    </script>
</body>

</html>