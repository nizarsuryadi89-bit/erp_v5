<!-- Page Header -->
<div class="custom-page-header">
    <h5><i class="bi bi-clock-fill me-2 text-warning"></i>Absensi Lembur</h5>
    <p>Komitmen tinggi untuk pekerjaan ekstra</p>
</div>

<style>
    .lembur-container {
        padding: 0 5%;
        margin-top: 1.5rem;
    }

    .status-badge {
        font-size: 0.75rem;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .badge-off {
        background: #fee2e2;
        color: #ef4444;
    }

    .badge-on {
        background: #dcfce7;
        color: #22c55e;
    }

    .badge-wait {
        background: #fef9c3;
        color: #ca8a04;
    }

    .tap-area {
        background: white;
        border-radius: 30px;
        padding: 2.5rem 2rem;
        text-align: center;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        border: 2px dashed #e2e8f0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .tap-area:active {
        transform: scale(0.95);
        background: #f8fafc;
    }

    .tap-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin: 0 auto 1.5rem;
        box-shadow: 0 10px 20px rgba(245, 158, 11, 0.3);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4);
        }

        70% {
            box-shadow: 0 0 0 20px rgba(245, 158, 11, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 0);
        }
    }

    .report-card {
        background: white;
        border-radius: 25px;
        padding: 1.5rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .premium-input {
        border: 1.5px solid #e2e8f0;
        border-radius: 15px;
        padding: 0.75rem 1rem;
        background: #f8fafc;
        width: 100%;
        transition: all 0.2s;
    }

    .premium-input:focus {
        border-color: #f59e0b;
        background: white;
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
        outline: none;
    }

    .file-upload-box {
        border: 1.5px dashed #cbd5e1;
        border-radius: 15px;
        padding: 1.5rem;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        margin-top: 0.5rem;
    }

    .preview-img {
        width: 100%;
        max-height: 200px;
        object-fit: cover;
        border-radius: 15px;
        margin-top: 1rem;
        display: none;
    }

    /* Camera Modal Styles (Sync with Absen) */
    .camera-preview-container {
        position: relative;
        width: 100%;
        margin: 0 auto 1rem;
        aspect-ratio: 4/5;
        background: #000;
        border-radius: 16px;
        overflow: hidden;
        border: 2px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    #webcam-preview {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transform: scaleX(-1);
    }

    .camera-grid {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        box-sizing: border-box;
    }

    .camera-grid::after {
        content: '';
        position: absolute;
        top: 10%;
        left: 10%;
        width: 80%;
        height: 80%;
        border: 2px dashed rgba(255, 255, 255, 0.4);
        border-radius: 20px;
    }
</style>

<div class="lembur-container">

    <?php if ($lembur_aktif): ?>
        <!-- STATE 2: SEDANG LEMBUR (ACTIVE SESSION) -->
        <div class="text-center mb-4">
            <div class="status-badge badge-wait">
                <i class="bi bi-lightning-charge-fill"></i> Sesi Lembur Aktif
            </div>
            <h4 class="fw-bold mb-1"><?= date('H:i', strtotime($lembur_aktif['jam_mulai'])) ?></h4>
            <p class="text-muted small">Waktu mulai lembur Anda</p>
        </div>

        <div class="report-card">
            <form action="<?= base_url('mobile/aksi_selesai_lembur') ?>" method="post" enctype="multipart/form-data"
                id="finishLemburForm">
                <input type="hidden" name="id_lembur" value="<?= $lembur_aktif['id'] ?>">

                <div class="mb-3">
                    <label class="small fw-bold text-muted mb-2">APA YANG ANDA KERJAKAN?</label>
                    <textarea name="kegiatan" class="premium-input" rows="4"
                        placeholder="Contoh: Menyelesaikan desain banner promo..." required></textarea>
                </div>

                <div class="mb-4">
                    <label class="small fw-bold text-muted mb-2">FOTO KEGIATAN</label>
                    <div class="file-upload-box" id="btnCaptureFoto" onclick="openCamera()">
                        <i class="bi bi-camera-fill fs-2 text-muted"></i>
                        <p class="small text-muted mb-0 mt-1">Ketuk untuk Ambil Foto Langsung</p>
                        <input type="hidden" name="foto_base64" id="foto_base64">
                    </div>
                    <img id="imagePreview" class="preview-img">
                </div>

                <button type="submit" class="action-btn w-100 py-3"
                    style="background: linear-gradient(135deg, #22c55e, #15803d);">
                    <i class="bi bi-stop-circle-fill me-2"></i> TUTUP LEMBUR
                </button>
            </form>
        </div>

    <?php else: ?>
        <!-- STATE 1: SEBELUM MULAI (NO ACTIVE SESSION) -->
        <?php if ($boleh_mulai): ?>
            <div class="text-center py-5">
                <div class="tap-area" onclick="mulaiLembur()">
                    <div class="tap-icon">
                        <i class="bi bi-hand-index-thumb-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-1">MULAI LEMBUR BARU</h5>
                    <p class="text-muted small mb-0">Ketuk layar untuk memulai sesi kerja ekstra Anda</p>
                </div>

                <form id="mulaiLemburForm" action="<?= base_url('mobile/aksi_mulai_lembur') ?>" method="post" hidden></form>
            </div>
        <?php else: ?>
            <div class="text-center py-5 opacity-75">
                <div class="tap-area border-0 bg-light" style="box-shadow: none;">
                    <div class="tap-icon grayscale" style="background: #94a3b8; box-shadow: none; animation: none;">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <?php if ($boleh_lembur_hari_ini == '0'): ?>
                        <h5 class="fw-bold text-muted mb-1">TIDAK TERSEDIA</h5>
                        <p class="text-muted small mb-0">Lembur tidak dibuka untuk hari ini</p>
                    <?php else: ?>
                        <h5 class="fw-bold text-muted mb-1">BELUM WAKTUNYA</h5>
                        <p class="text-muted small mb-0">Absen lembur hanya dibuka Pagi (06:00 - <?= $jam_masuk_kerja ?>) atau Sore
                            (Mulai <?= $jam_mulai_lembur ?> WIB).</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- STATE 3: RIWAYAT LEMBUR HARI INI (SHOW IF COMPLETED SESSIONS EXIST) -->
    <?php if (!empty($lembur_selesai) && is_array($lembur_selesai)): ?>
        <hr class="mt-5 mb-4 border-secondary opacity-25">
        <h6 class="fw-bold mb-3 px-2 text-muted"><i class="bi bi-clock-history me-2"></i>Riwayat Lembur Hari Ini</h6>

        <?php foreach ($lembur_selesai as $ls): ?>
            <div class="text-center mb-4">
                <div class="report-card text-start border border-success border-opacity-25" style="background: #f8fafc;">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="status-badge badge-on m-0 py-1 px-2" style="font-size: 0.65rem;">
                            <i class="bi bi-check-circle-fill"></i> Selesai
                        </span>
                    </div>

                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Mulai</span>
                        <span class="fw-bold"><?= date('H:i', strtotime($ls['jam_mulai'])) ?> WIB</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 small">
                        <span class="text-muted">Selesai</span>
                        <span class="fw-bold"><?= date('H:i', strtotime($ls['jam_selesai'])) ?> WIB</span>
                    </div>
                    <hr class="my-2 border-secondary opacity-25">
                    <div class="mb-2">
                        <p class="small text-muted mb-1" style="font-size:0.75rem;">Laporan Kegiatan:</p>
                        <p class="small fst-italic mb-2" style="font-size:0.85rem;"><?= $ls['kegiatan'] ?></p>
                    </div>
                    <?php if ($ls['foto_kegiatan']): ?>
                        <img src="<?= base_url('assets/images/lembur/' . $ls['foto_kegiatan']) ?>"
                            class="img-fluid rounded-3 shadow-sm mt-2" style="width: 100%; height: 120px; object-fit: cover;">
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="alert alert-warning border-0 shadow-sm mt-4" style="border-radius: 20px; background: #fffbeb;">
        <div class="d-flex gap-3">
            <i class="bi bi-info-circle-fill fs-4 text-warning"></i>
            <div>
                <h6 class="fw-bold text-dark mb-1 small">Informasi Absensi</h6>
                <ul class="extra-small text-muted mb-0 ps-3" style="font-size: 0.75rem;">
                    <?php if ($boleh_lembur_hari_ini == '1'): ?>
                        <li>Lembur dimulai minimal jam Pagi (06:00 -
                            <?= $jam_masuk_kerja ?>) atau Sore (Mulai
                            <?= $jam_mulai_lembur ?>).
                        </li>
                    <?php else: ?>
                        <li class="text-danger fw-bold">Hari ini tidak ada lembur.</li>
                    <?php endif; ?>
                    <li>Wajib menyertakan foto kegiatan saat pulang.</li>
                    <li>Pastikan laporan pekerjaan diisi dengan lengkap.</li>
                </ul>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let videoStream = null;

    async function startCamera() {
        if (!window.isSecureContext && location.hostname !== "localhost") {
            Swal.fire({
                icon: 'error',
                title: 'Koneksi Tidak Aman',
                text: 'Fitur Kamera hanya dapat berjalan di koneksi HTTPS (Secure).'
            });
            return false;
        }

        try {
            videoStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: "environment", // Use BACK camera for activity photo
                    width: { ideal: 1280 },
                    height: { ideal: 960 }
                }
            });
            const video = document.getElementById('webcam-preview');
            if (video) {
                video.srcObject = videoStream;
                video.play();
            }
            return true;
        } catch (err) {
            console.error("Error accessing camera: ", err);
            Swal.fire('Gagal', 'Tidak dapat mengakses kamera. Pastikan izin diberikan.', 'error');
            return false;
        }
    }

    function stopCamera() {
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            videoStream = null;
        }
    }

    function getPosition() {
        return new Promise((resolve, reject) => {
            navigator.geolocation.getCurrentPosition(resolve, reject, {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            });
        });
    }

    function drawWatermark(canvas, text) {
        const ctx = canvas.getContext('2d');
        const padding = 20;
        const fontSize = Math.max(12, canvas.width / 45);

        // Bottom Bar Background
        ctx.fillStyle = "rgba(0, 0, 0, 0.6)";
        ctx.fillRect(0, canvas.height - (fontSize * 3), canvas.width, fontSize * 3);

        // Text Style
        ctx.fillStyle = "white";
        ctx.font = `bold ${fontSize}px Inter, sans-serif`;
        ctx.textAlign = "left";

        ctx.fillText(text, padding, canvas.height - (fontSize * 1.2));
    }

    async function capturePhoto(locationInfo) {
        const video = document.getElementById('webcam-preview');
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');

        // Draw frame
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Combine Timestamp & GPS
        const now = new Date();
        const timestamp = now.toLocaleString('id-ID', {
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });

        const fullWatermark = `${timestamp} WIB | ${locationInfo}`;
        drawWatermark(canvas, fullWatermark);

        return canvas.toDataURL('image/jpeg', 0.8);
    }

    async function openCamera() {
        // Step 1: Get Position
        Swal.fire({
            title: 'Mendeteksi Lokasi...',
            html: 'Mendapatkan koordinat GPS untuk bukti lembur...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        let locationInfo = "GPS Tidak Aktif";
        try {
            const pos = await getPosition();
            locationInfo = `LAT: ${pos.coords.latitude.toFixed(6)}, LONG: ${pos.coords.longitude.toFixed(6)}`;
        } catch (err) {
            console.warn("GPS failed", err);
        }

        // Step 2: Open Camera with Location Info
        const result = await Swal.fire({
            title: `Ambil Foto Kegiatan`,
            html: `
                <div class="camera-preview-container">
                    <video id="webcam-preview" autoplay playsinline muted style="transform: none;"></video>
                    <div class="camera-grid"></div>
                </div>
                <div class="small text-muted mb-2">
                    <i class="bi bi-geo-alt-fill text-danger me-1"></i> ${locationInfo}
                </div>
                <p class="extra-small text-muted">Pastikan bukti pekerjaan terlihat jelas</p>
            `,
            showCancelButton: true,
            confirmButtonText: `Ambil Foto`,
            cancelButtonText: 'Batal',
            confirmButtonColor: '#22c55e',
            reverseButtons: true,
            allowOutsideClick: false,
            didOpen: () => {
                startCamera();
            },
            preConfirm: async () => {
                try {
                    const photoData = await capturePhoto(locationInfo);
                    return photoData;
                } catch (e) {
                    Swal.showValidationMessage(`Gagal: ${e}`);
                }
            },
            willClose: () => {
                stopCamera();
            }
        });

        if (result.isConfirmed && result.value) {
            const photoData = result.value;
            document.getElementById('foto_base64').value = photoData;
            const preview = document.getElementById('imagePreview');
            preview.src = photoData;
            preview.style.display = 'block';

            // Update UI box
            const box = document.getElementById('btnCaptureFoto');
            box.style.background = '#dcfce7';
            box.style.borderColor = '#22c55e';
            box.querySelector('p').innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Foto Berhasil Diambil';
        }
    }

    async function mulaiLembur() {
        Swal.fire({
            title: 'Mendeteksi Lokasi...',
            html: `Mencoba mendapatkan koordinat GPS untuk mulai lembur...<br><small class="text-muted">Pastikan GPS aktif & izin lokasi diberikan.</small>`,
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        let locationString = "";
        try {
            const pos = await getPosition();
            locationString = `Lat: ${pos.coords.latitude.toFixed(6)}, Long: ${pos.coords.longitude.toFixed(6)}`;
            Swal.close();
        } catch (err) {
            let errorMsg = "Gagal mendapatkan lokasi.";
            if (err.code === 1) errorMsg = "Izin lokasi ditolak. Silakan aktifkan izin lokasi pada browser/aplikasi Anda.";
            if (err.code === 2) errorMsg = "Sinyal GPS tidak ditemukan atau lokasi tidak tersedia.";
            if (err.code === 3) errorMsg = "Waktu pencarian lokasi habis (Timeout). Pastikan Anda berada di ruang terbuka.";
            
            Swal.fire({
                icon: 'warning',
                title: 'Lokasi Tidak Ditemukan',
                text: errorMsg + ' Apakah Anda tetap ingin mulai lembur tanpa lokasi?',
                showCancelButton: true,
                confirmButtonText: 'Ya, Lanjut',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitMulaiLembur("");
                }
            });
            return;
        }

        submitMulaiLembur(locationString);
    }

    function submitMulaiLembur(locationString) {
        Swal.fire({
            title: 'Mulai Lembur?',
            text: "Sesi lembur Anda akan dicatat mulai pukul <?= date('H:i') ?> WIB",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            confirmButtonText: 'Ya, Mulai!',
            cancelButtonText: 'Batal',
            borderRadius: '25px'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.getElementById('mulaiLemburForm');
                
                // Hapus input lokasi lama jika ada (untuk mencegah duplikasi)
                let oldInput = document.getElementById('lokasi_lembur');
                if(oldInput) { oldInput.remove(); }

                let inputLoc = document.createElement('input');
                inputLoc.type = 'hidden';
                inputLoc.name = 'lokasi';
                inputLoc.id = 'lokasi_lembur';
                inputLoc.value = locationString;
                form.appendChild(inputLoc);
                form.submit();
            }
        });
    }

    document.getElementById('finishLemburForm')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const hasPhoto = document.getElementById('foto_base64').value !== '';

        if (!hasPhoto) {
            Swal.fire({
                icon: 'error',
                title: 'Foto Belum Ada',
                text: 'Wajib mengambil foto kegiatan langsung sebelum menutup lembur.',
                borderRadius: '25px'
            });
            return;
        }

        Swal.fire({
            title: 'Tutup Lembur?',
            text: "Laporan kegiatan akan disimpan dan durasi lembur akan dihitung.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#22c55e',
            confirmButtonText: 'Ya, Selesai!',
            cancelButtonText: 'Batal',
            borderRadius: '25px'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
</script>