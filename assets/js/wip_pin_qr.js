/* QR fills the existing PIN field. The production endpoint verifies the PIN on submit. */
(function (window, $) {
    'use strict';
    window.WipPinQr = {
        scanner: null,
        library: null,
        queue: Promise.resolve(),
        generation: 0,
        active: false,
        initialized: false,

        init() {
            if (this.initialized || !$('#compScanPinQr').length) return;
            this.initialized = true;
            $('#compScanPinQr').on('click', () => this.active ? this.close() : this.open());
            $('#compStopPinQr').on('click', () => this.close());
            $('#compPinQrFile').on('change', event => this.scanFile(event.target.files[0]));
            $('#compOperatorPin').on('input', () => this.message(''));
            $('#modalNestingCompletion').on('hide.bs.modal', () => this.reset());
            window.addEventListener('pagehide', () => this.close());
        },

        message(text, error = false) {
            $('#compPinQrStatus').text(text).toggleClass('text-danger', error).toggleClass('text-success', !error && !!text);
        },

        enqueue(work) {
            // Serialize camera startup, stop and image decoding, including slow permission prompts.
            this.queue = this.queue.then(work).catch(() => {
                if (this.active) this.message('Scanner belum dapat digunakan. Coba lagi atau ketik PIN.', true);
            });
            return this.queue;
        },

        loadLibrary() {
            if (window.Html5Qrcode) return Promise.resolve();
            if (!this.library) {
                this.library = new Promise((resolve, reject) => {
                    const script = document.createElement('script');
                    script.src = $('#compPinQrPanel').attr('data-scanner-src');
                    script.onload = () => window.Html5Qrcode ? resolve() : reject(new Error('Scanner unavailable'));
                    script.onerror = () => { script.remove(); reject(new Error('Scanner unavailable')); };
                    document.head.appendChild(script);
                }).catch(error => { this.library = null; throw error; });
            }
            return this.library;
        },

        getScanner() {
            if (!this.scanner) {
                this.scanner = new window.Html5Qrcode('compPinQrReader', {
                    formatsToSupport: [window.Html5QrcodeSupportedFormats.QR_CODE], verbose: false
                });
            }
            return this.scanner;
        },

        async stopCamera() {
            if (this.scanner && this.scanner.isScanning) await this.scanner.stop();
            if (this.scanner) this.scanner.clear();
        },

        showPanel() {
            this.active = true;
            $('#compPinQrPanel').prop('hidden', false);
            $('#compScanPinQr').attr('aria-expanded', 'true');
            return ++this.generation;
        },

        open() {
            const generation = this.showPanel();
            this.message('Menyiapkan kamera…');
            return this.enqueue(async () => {
                await this.stopCamera();
                await this.loadLibrary();
                if (!this.active || generation !== this.generation) return;
                if (!window.isSecureContext || !navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    this.message('Kamera memerlukan HTTPS/localhost dan izin browser. Pilih gambar QR atau ketik PIN.', true);
                    return;
                }
                const scanner = this.getScanner();
                try {
                    await scanner.start({ facingMode: 'environment' }, { fps: 10 },
                        text => this.accept(text, generation), () => {});
                    if (!this.active || generation !== this.generation) await this.stopCamera();
                    else this.message('Kamera aktif. Arahkan QR PIN petugas ke kamera.');
                } catch (error) {
                    if (this.active && generation === this.generation)
                        this.message('Kamera tidak tersedia atau izinnya ditolak. Pilih gambar QR atau ketik PIN.', true);
                }
            });
        },

        scanFile(file) {
            if (!file) return this.queue;
            if (!/^image\//.test(file.type || '')) {
                this.message('Pilih file gambar QR.', true);
                return this.queue;
            }
            const generation = this.showPanel();
            this.message('Membaca gambar QR…');
            return this.enqueue(async () => {
                await this.stopCamera();
                await this.loadLibrary();
                if (!this.active || generation !== this.generation) return;
                try {
                    const text = await this.getScanner().scanFile(file, false);
                    this.accept(text, generation);
                } catch (error) {
                    if (this.active && generation === this.generation)
                        this.message('QR tidak terbaca. Pilih gambar yang lebih jelas atau ketik PIN.', true);
                } finally {
                    $('#compPinQrFile').val('');
                }
            });
        },

        accept(text, generation) {
            if (!this.active || generation !== this.generation) return false;
            const pin = String(text).trim();
            if (!/^[0-9]{4,6}$/.test(pin)) {
                this.message('QR harus berisi PIN produksi 4–6 digit.', true);
                return false;
            }
            $('#compOperatorPin').val(pin).trigger('input');
            this.close();
            this.message('PIN terbaca dari QR. Tekan Konfirmasi Selesai untuk verifikasi petugas.');
            $('#compOperatorPin').trigger('focus');
            return true;
        },

        close() {
            this.active = false;
            ++this.generation;
            this.message('');
            $('#compPinQrPanel').prop('hidden', true);
            $('#compScanPinQr').attr('aria-expanded', 'false');
            $('#compPinQrFile').val('');
            return this.enqueue(() => this.stopCamera());
        },

        reset() {
            this.close();
            $('#compOperatorPin').val('');
            this.message('');
        }
    };
})(window, jQuery);
