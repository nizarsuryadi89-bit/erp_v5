<!-- Modal Panduan Lengkap Create SPK -->
<style>
    #modalPanduanSPK .modal-body {
        font-size: 1.05rem;
        line-height: 1.6;
    }

    #modalPanduanSPK .nav-link {
        font-size: 1.1rem !important;
        padding: 12px 10px;
    }

    #modalPanduanSPK h5 {
        font-size: 1.4rem !important;
    }

    #modalPanduanSPK h6 {
        font-size: 1.2rem !important;
    }

    #modalPanduanSPK .small,
    #modalPanduanSPK small,
    #modalPanduanSPK ul.small,
    #modalPanduanSPK ol.small {
        font-size: 1rem !important;
    }

    #modalPanduanSPK .alert i {
        font-size: 1.2rem;
    }

    #modalPanduanSPK .accordion-button {
        font-size: 1.15rem;
    }

    #modalPanduanSPK .card-header {
        font-size: 1.15rem;
    }
</style>

<div class="modal fade" id="modalPanduanSPK" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white fw-bold">
                    <i class="fas fa-book-reader me-2"></i>
                    Panduan Lengkap - Modul Create SPK (Deskprint) v2.0
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs nav-fill border-bottom" role="tablist" style="background: #f8f9fa;">
                    <li class="nav-item">
                        <a class="nav-link active fw-bold" data-bs-toggle="tab" href="#spkOverview">
                            <i class="fas fa-info-circle me-1"></i> Ringkasan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-bold" data-bs-toggle="tab" href="#spkWorkflow">
                            <i class="fas fa-tasks me-1"></i> Alur Kerja
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-bold" data-bs-toggle="tab" href="#spkTips">
                            <i class="fas fa-lightbulb me-1"></i> Tips & Trik
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-bold" data-bs-toggle="tab" href="#spkTrouble">
                            <i class="fas fa-wrench me-1"></i> Troubleshooting
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-bold" data-bs-toggle="tab" href="#spkExamples">
                            <i class="fas fa-file-alt me-1"></i> Contoh Kasus
                        </a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content p-4">
                    <!-- Tab 1: Overview -->
                    <div class="tab-pane fade show active" id="spkOverview">
                        <h5 class="fw-bold text-primary mb-3">
                            <i class="fas fa-rocket me-2"></i>Apa itu Create SPK?
                        </h5>

                        <div class="alert alert-success border-0 shadow-sm mb-3">
                            <h6 class="fw-bold">✅ Fungsi Utama Modul Ini</h6>
                            <ul class="mb-0 small">
                                <li><strong>Create SPK</strong> adalah halaman untuk membuat invoice/nota pesanan baru
                                </li>
                                <li>Digunakan oleh <strong>CS / Sales / Deskprint</strong> untuk merekam pesanan
                                    konsumen</li>
                                <li>Sistem akan generate <strong>Nomor SPK otomatis</strong> untuk tracking produksi
                                </li>
                                <li>Data SPK akan diteruskan ke <strong>Kasir</strong> untuk pembayaran, lalu ke
                                    <strong>Produksi</strong>
                                </li>
                            </ul>
                        </div>

                        <div class="alert alert-info border-0 shadow-sm mb-3">
                            <h6 class="fw-bold">✅ Fitur Keyboard Shortcut</h6>
                            <ul class="mb-0 small">
                                <li><strong>Tab / Enter:</strong> Berpindah antar field otomatis</li>
                                <li><strong>Auto-Focus:</strong> Sistem akan langsung focus ke field berikutnya setelah
                                    input</li>
                                <li><strong>Keyboard-First:</strong> Semua proses bisa tanpa mouse untuk kecepatan
                                    maksimal</li>
                                <li><strong>Keuntungan:</strong> Input data lebih cepat 2-3x lipat!</li>
                            </ul>
                        </div>

                        <div class="alert alert-warning border-0 shadow-sm mb-3">
                            <h6 class="fw-bold">✅ Sistem 2 Langkah</h6>
                            <ul class="mb-0 small">
                                <li><strong>Langkah 1:</strong> Pilih konsumen & metode pesan → Generate SPK</li>
                                <li><strong>Langkah 2:</strong> Tambah item produk satu per satu ke SPK</li>
                                <li><strong>Langkah 3:</strong> Kirim ke kasir untuk pembayaran</li>
                            </ul>
                        </div>

                        <hr class="my-4">

                        <h5 class="fw-bold text-danger mb-3">
                            <i class="fas fa-user-tie me-2"></i>Siapa yang Menggunakan?
                        </h5>
                        <ol class="small">
                            <li><strong>Customer Service (CS):</strong> Terima order dari pelanggan walk-in atau telepon
                            </li>
                            <li><strong>Sales:</strong> Input orderan dari marketplace atau online</li>
                            <li><strong>Staff Deskprint:</strong> Rekap data pesanan dari kiosk atau order manual</li>
                        </ol>

                        <div class="alert alert-danger border-0 shadow-sm mt-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>PENTING:</strong> Pastikan data konsumen & spesifikasi produk sudah benar sebelum
                            kirim ke kasir. Data yang salah akan mengganggu proses produksi!
                        </div>
                    </div>

                    <!-- Tab 2: Workflow -->
                    <div class="tab-pane fade" id="spkWorkflow">
                        <h5 class="fw-bold text-primary mb-4">
                            <i class="fas fa-route me-2"></i>Alur Kerja Membuat SPK
                        </h5>

                        <!-- Step 1 -->
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-primary text-white fw-bold">
                                <i class="fas fa-user-check me-2"></i>Langkah 1: Pilih Konsumen & Metode Pesan
                            </div>
                            <div class="card-body">
                                <h6 class="fw-bold text-dark">A. Pilih Konsumen</h6>
                                <ol class="small mb-3">
                                    <li>Klik field <strong>"PILIH KONSUMEN"</strong></li>
                                    <li>Ketik nama atau nomor WA konsumen</li>
                                    <li>Pilih dari dropdown yang muncul</li>
                                    <li>Jika konsumen baru, klik <strong>"TAMBAH BARU"</strong>:
                                        <ul>
                                            <li>Isi Nama Lengkap</li>
                                            <li>Isi No. Telepon/WA</li>
                                            <li>Email (opsional)</li>
                                            <li>Alamat (opsional)</li>
                                            <li>Klik <strong>"SIMPAN DATA"</strong></li>
                                        </ul>
                                    </li>
                                </ol>

                                <h6 class="fw-bold text-dark">B. Pilih Metode Pesanan</h6>
                                <ul class="small mb-3">
                                    <li><strong>ON SITE:</strong> Konsumen datang langsung ke toko</li>
                                    <li><strong>MARKETPLACE:</strong> Order dari Tokopedia/Shopee/TikTok
                                        <ul>
                                            <li>Pilih platform</li>
                                            <li>Masukkan No. Resi</li>
                                        </ul>
                                    </li>
                                    <li><strong>ONLINE:</strong> Order via WA/Telepon/Email</li>
                                </ul>

                                <div class="bg-light p-3 rounded">
                                    <p class="small mb-0 fw-bold text-primary">
                                        <i class="fas fa-keyboard me-1"></i> Super Fast: Setelah pilih konsumen, tekan
                                        TAB 2x lalu Enter untuk langsung submit!
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-info text-white fw-bold">
                                <i class="fas fa-box-open me-2"></i>Langkah 2: Tambah Item Produk
                            </div>
                            <div class="card-body">
                                <p class="small mb-2">Setelah SPK dibuat, Anda akan masuk ke halaman input item:</p>

                                <h6 class="fw-bold text-primary mt-3">BLOK 1: Pilih Produk</h6>
                                <ol class="small mb-3">
                                    <li>Klik field <strong>"CARI NAMA PRODUK / JASA"</strong> (atau scan barcode)</li>
                                    <li>Ketik nama produk, misal: "Banner", "X-Banner", "Sticker"</li>
                                    <li>Pilih dari dropdown</li>
                                    <li>Sistem akan auto-load spesifikasi di BLOK 2</li>
                                </ol>

                                <h6 class="fw-bold text-success mt-3">BLOK 2: Spesifikasi Teknis</h6>
                                <p class="small mb-2">Form ini <strong>dinamis</strong> tergantung kategori produk:</p>
                                <ul class="small mb-3">
                                    <li><strong>Kategori & Jenis:</strong> Otomatis terisi (disabled)</li>
                                    <li><strong>Sisi Cetak:</strong> 1 Sisi / 2 Sisi / Tidak Ada (untuk non-cetak)
                                        <ul>
                                            <li>Gunakan tombol segmented untuk pilih</li>
                                        </ul>
                                    </li>
                                    <li><strong>Panjang & Lebar:</strong>
                                        <ul>
                                            <li>Untuk produk custom ukuran (banner, sticker roll, dll)</li>
                                            <li>Masukkan dalam <strong>METER</strong></li>
                                            <li>Tekan Enter setelah isi Panjang → auto focus ke Lebar</li>
                                            <li>Tekan Enter setelah isi Lebar → auto focus ke Qty</li>
                                        </ul>
                                    </li>
                                    <li><strong>Ukuran Tetap:</strong> Untuk produk seperti X-Banner, Roll-Up (ukuran
                                        sudah tetap)</li>
                                </ul>

                                <h6 class="fw-bold text-warning mt-3">BLOK 3: Rincian Pesanan</h6>
                                <ol class="small mb-3">
                                    <li><strong>Qty:</strong> Jumlah pesanan
                                        <ul>
                                            <li>Untuk produk meter: isi jumlah meter</li>
                                            <li>Untuk produk pcs: isi jumlah unit</li>
                                            <li><strong>Tekan Enter</strong> untuk langsung submit item!</li>
                                        </ul>
                                    </li>
                                    <li><strong>Approval:</strong>
                                        <ul>
                                            <li>🚀 <strong>LANJUT:</strong> Produksi langsung</li>
                                            <li>🧪 <strong>SAMPEL:</strong> Butuh approval dulu</li>
                                        </ul>
                                    </li>
                                    <li><strong>Lokasi:</strong> Produksi di OFFICIAL atau MERCH</li>
                                </ol>

                                <div class="alert alert-success border-0 mb-0 small">
                                    <i class="fas fa-rocket me-1"></i>
                                    <strong>Workflow Super Cepat:</strong><br>
                                    Pilih Produk → Enter → (Auto focus) Sisi → Enter → Panjang → Enter → Lebar → Enter →
                                    Qty → Enter → DONE! 🎉
                                </div>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-success text-white fw-bold">
                                <i class="fas fa-cash-register me-2"></i>Langkah 3: Kirim ke Kasir
                            </div>
                            <div class="card-body">
                                <ol class="small mb-2">
                                    <li>Pastikan semua item sudah sesuai di tabel</li>
                                    <li>Cek <strong>Total Item</strong> dan <strong>Total Biaya</strong> di summary bar
                                    </li>
                                    <li>Klik tombol <strong>"KIRIM KE KASIR SEKARANG"</strong></li>
                                    <li>SPK akan masuk ke sistem kasir untuk pembayaran</li>
                                    <li>Setelah lunas, SPK otomatis masuk ke antrian produksi</li>
                                </ol>

                                <div class="alert alert-warning border-0 mb-0 small">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Catatan:</strong> Setelah kirim ke kasir, Anda tidak bisa edit SPK ini lagi.
                                    Pastikan semua data sudah benar!
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 3: Tips -->
                    <div class="tab-pane fade" id="spkTips">
                        <h5 class="fw-bold text-primary mb-4">
                            <i class="fas fa-lightbulb me-2"></i>Tips & Trik Create SPK
                        </h5>

                        <div class="accordion" id="spkTipsAccordion">
                            <div class="accordion-item border-0 shadow-sm mb-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#spkTip1">
                                        <i class="fas fa-search me-2 text-primary"></i>Cara Cari Konsumen Cepat
                                    </button>
                                </h2>
                                <div id="spkTip1" class="accordion-collapse collapse show"
                                    data-bs-parent="#spkTipsAccordion">
                                    <div class="accordion-body small">
                                        <ul class="mb-2">
                                            <li>Ketik <strong>nama lengkap</strong> konsumen</li>
                                            <li>Atau ketik <strong>nomor WA</strong> (misal: 0812)</li>
                                            <li>Dropdown akan filter otomatis</li>
                                            <li>Pilih dengan Enter atau klik</li>
                                        </ul>
                                        <div class="alert alert-info border-0 mb-0">
                                            <i class="fas fa-keyboard me-1"></i> <strong>Pro Tip:</strong> Setelah
                                            dropdown muncul, gunakan arrow ↑↓ untuk navigasi, tekan Enter untuk pilih
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0 shadow-sm mb-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#spkTip2">
                                        <i class="fas fa-barcode me-2 text-success"></i>Cara Pakai Barcode Scanner
                                    </button>
                                </h2>
                                <div id="spkTip2" class="accordion-collapse collapse"
                                    data-bs-parent="#spkTipsAccordion">
                                    <div class="accordion-body small">
                                        <ol class="mb-2">
                                            <li>Pastikan cursor di field <strong>"CARI NAMA PRODUK"</strong></li>
                                            <li>Scan barcode produk</li>
                                            <li>Sistem otomatis load spesifikasi</li>
                                            <li>Langsung isi ukuran & qty</li>
                                        </ol>
                                        <div class="alert alert-success border-0 mb-0">
                                            <i class="fas fa-bolt me-1"></i> <strong>Super Fast Mode:</strong> Scan →
                                            Enter → Ukuran → Enter → Qty → Enter. Total waktu: ~5 detik/item!
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0 shadow-sm mb-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#spkTip3">
                                        <i class="fas fa-edit me-2 text-warning"></i>Cara Edit/Hapus Item
                                    </button>
                                </h2>
                                <div id="spkTip3" class="accordion-collapse collapse"
                                    data-bs-parent="#spkTipsAccordion">
                                    <div class="accordion-body small">
                                        <h6 class="fw-bold text-danger">Hapus Item:</h6>
                                        <ul class="mb-3">
                                            <li>Klik tombol <strong class="text-danger">🗑️ (Delete)</strong> di kolom
                                                Aksi</li>
                                            <li>Konfirmasi "Ya, Hapus"</li>
                                            <li>Item akan dihapus & total biaya auto update</li>
                                        </ul>

                                        <h6 class="fw-bold text-success">Edit Item:</h6>
                                        <ul class="mb-0">
                                            <li>Hapus item yang salah</li>
                                            <li>Tambah ulang dengan data yang benar</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0 shadow-sm mb-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#spkTip4">
                                        <i class="fas fa-copy me-2 text-info"></i>Cara Salin Nomor SPK
                                    </button>
                                </h2>
                                <div id="spkTip4" class="accordion-collapse collapse"
                                    data-bs-parent="#spkTipsAccordion">
                                    <div class="accordion-body small">
                                        <ol class="mb-2">
                                            <li>Lihat nomor SPK di pojok kanan atas (contoh: 2-16-0226-349)</li>
                                            <li>Klik tombol <strong>"SALIN NOMOR"</strong></li>
                                            <li>Nomor SPK otomatis tersalin ke clipboard</li>
                                            <li>Paste (Ctrl+V / Cmd+V) ke WA/Email konsumen</li>
                                        </ol>
                                        <div class="alert alert-success border-0 mb-0">
                                            <i class="fas fa-phone me-1"></i> <strong>Best Practice:</strong> Kirim
                                            nomor SPK ke konsumen via WA untuk tracking produksi
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0 shadow-sm mb-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#spkTip5">
                                        <i class="fas fa-tablet-alt me-2 text-warning"></i>Import dari Kiosk
                                    </button>
                                </h2>
                                <div id="spkTip5" class="accordion-collapse collapse"
                                    data-bs-parent="#spkTipsAccordion">
                                    <div class="accordion-body small">
                                        <h6 class="fw-bold text-primary">Apa itu Kiosk?</h6>
                                        <p class="mb-2">Kiosk adalah sistem self-order untuk konsumen yang datang ke
                                            toko. Mereka bisa input pesanan sendiri di tablet.</p>

                                        <h6 class="fw-bold text-success">Cara Import dari Kiosk:</h6>
                                        <ol class="mb-2">
                                            <li>Klik tombol <strong>"IMPORT KIOSK"</strong> di halaman Step 1</li>
                                            <li>Akan muncul daftar antrian pesanan dari kiosk</li>
                                            <li>Cek detail konsumen & item pesanan</li>
                                            <li>Klik <strong>"PROSES"</strong> untuk buat SPK otomatis</li>
                                            <li>Semua data (konsumen + item) akan auto-fill!</li>
                                        </ol>

                                        <div class="alert alert-warning border-0 mb-0">
                                            <i class="fas fa-exclamation-triangle me-1"></i> <strong>Catatan:</strong>
                                            Cek ulang data sebelum kirim ke kasir, terutama untuk custom size
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 4: Troubleshooting -->
                    <div class="tab-pane fade" id="spkTrouble">
                        <h5 class="fw-bold text-danger mb-4">
                            <i class="fas fa-wrench me-2"></i>Troubleshooting
                        </h5>

                        <div class="card border-danger mb-3">
                            <div class="card-header bg-danger text-white fw-bold">
                                <i class="fas fa-search me-2"></i>Problem: Produk Tidak Muncul di Pencarian
                            </div>
                            <div class="card-body small">
                                <h6 class="fw-bold text-danger">Penyebab:</h6>
                                <ul>
                                    <li>Produk belum di-input ke master data</li>
                                    <li>Produk tidak aktif</li>
                                    <li>Salah ketik nama produk</li>
                                </ul>
                                <h6 class="fw-bold text-success">Solusi:</h6>
                                <ul class="mb-0">
                                    <li>Cek ejaan nama produk (gunakan keyword umum: "banner", "sticker", "x-banner")
                                    </li>
                                    <li>Hubungi Admin untuk cek status produk</li>
                                    <li>Minta Admin tambah produk baru jika belum ada</li>
                                </ul>
                            </div>
                        </div>

                        <div class="card border-warning mb-3">
                            <div class="card-header bg-warning text-dark fw-bold">
                                <i class="fas fa-calculator me-2"></i>Problem: Harga Tidak Muncul / Error
                            </div>
                            <div class="card-body small">
                                <h6 class="fw-bold text-danger">Penyebab:</h6>
                                <ul>
                                    <li>Harga produk belum di-set untuk kombinasi spesifikasi tertentu</li>
                                    <li>Ukuran di luar range yang tersedia</li>
                                    <li>Mesin/jenis cetak belum di-set harganya</li>
                                </ul>
                                <h6 class="fw-bold text-success">Solusi:</h6>
                                <ul class="mb-2">
                                    <li>Screenshot error message (akan muncul detail SQL query)</li>
                                    <li>Catat kombinasi: Produk + Ukuran + Sisi</li>
                                    <li>Lapor ke Admin/Supervisor untuk update harga di master data</li>
                                    <li><strong>JANGAN</strong> paksa input item (akan error di payment)</li>
                                </ul>
                                <div class="alert alert-info border-0 mb-0">
                                    <i class="fas fa-phone me-1"></i> Sambil menunggu Admin fix, tanyakan estimasi harga
                                    ke supervisor untuk info konsumen
                                </div>
                            </div>
                        </div>

                        <div class="card border-info mb-3">
                            <div class="card-header bg-info text-white fw-bold">
                                <i class="fas fa-ban me-2"></i>Problem: Tombol "Kirim ke Kasir" Tidak Aktif
                            </div>
                            <div class="card-body small">
                                <h6 class="fw-bold text-danger">Penyebab:</h6>
                                <ul>
                                    <li>Belum ada item di SPK (tabel kosong)</li>
                                </ul>
                                <h6 class="fw-bold text-success">Solusi:</h6>
                                <ul class="mb-0">
                                    <li>Tambahkan minimal 1 item produk ke SPK</li>
                                    <li>Tombol akan otomatis aktif setelah ada item</li>
                                </ul>
                            </div>
                        </div>

                        <div class="card border-secondary mb-3">
                            <div class="card-header bg-secondary text-white fw-bold">
                                <i class="fas fa-network-wired me-2"></i>Problem: Koneksi Timeout / Loading Terus
                            </div>
                            <div class="card-body small">
                                <h6 class="fw-bold text-danger">Penyebab:</h6>
                                <ul>
                                    <li>Koneksi internet bermasalah</li>
                                    <li>Server sedang lambat</li>
                                    <li>Database sedang maintenance</li>
                                </ul>
                                <h6 class="fw-bold text-success">Solusi:</h6>
                                <ul class="mb-0">
                                    <li>Refresh halaman (F5)</li>
                                    <li>Cek koneksi internet</li>
                                    <li>Tunggu 1-2 menit lalu coba lagi</li>
                                    <li>Jika masih error, hubungi IT Support</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 5: Examples -->
                    <div class="tab-pane fade" id="spkExamples">
                        <h5 class="fw-bold text-success mb-4">
                            <i class="fas fa-file-alt me-2"></i>Contoh Kasus Nyata
                        </h5>

                        <!-- Example 1 -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-primary text-white fw-bold">
                                <i class="fas fa-image me-2"></i>Kasus 1: Order X-Banner Walk-In Customer
                            </div>
                            <div class="card-body">
                                <h6 class="fw-bold text-dark">Situasi:</h6>
                                <ul class="small">
                                    <li>Konsumen datang langsung ke toko (walk-in)</li>
                                    <li>Nama: Bpk. Sastro</li>
                                    <li>WA: 081234567890</li>
                                    <li>Pesanan: X-Banner 60x160, 5 pcs</li>
                                    <li>Lokasi produksi: OFFICIAL</li>
                                </ul>

                                <h6 class="fw-bold text-dark">Langkah CS:</h6>
                                <div class="bg-light p-3 rounded mb-2">
                                    <p class="small mb-1 fw-bold text-primary">Step 1: Pilih Konsumen</p>
                                    <ol class="small mb-0">
                                        <li>Buka halaman Create SPK</li>
                                        <li>Klik field "PILIH KONSUMEN"</li>
                                        <li>Ketik "Sastro" atau "0812"</li>
                                        <li>Jika muncul → pilih. Jika tidak ada → klik "TAMBAH BARU"</li>
                                        <li>Isi form:
                                            <ul>
                                                <li>Nama: Bpk. Sastro</li>
                                                <li>WA: 081234567890</li>
                                            </ul>
                                        </li>
                                        <li>Klik "SIMPAN DATA"</li>
                                        <li>Pilih metode: <strong>ON SITE</strong> (karena walk-in)</li>
                                        <li>Klik "MULAI BUAT SPK SEKARANG"</li>
                                    </ol>
                                </div>

                                <div class="bg-light p-3 rounded mb-2">
                                    <p class="small mb-1 fw-bold text-success">Step 2: Tambah Item</p>
                                    <ol class="small mb-0">
                                        <li>Di Blok 1, ketik "X-Banner" → pilih "X-Banner 60x160"</li>
                                        <li>Sistem auto-load spesifikasi (kategori: Large Format, ukuran tetap: 60x160)
                                        </li>
                                        <li>Di Blok 2, pilih Sisi: "1 Sisi" (default)</li>
                                        <li>Di Blok 3:
                                            <ul>
                                                <li>Qty: 5</li>
                                                <li>Approval: LANJUT</li>
                                                <li>Lokasi: OFFICIAL</li>
                                            </ul>
                                        </li>
                                        <li>Tekan Enter atau klik "TAMBAHKAN ITEM"</li>
                                    </ol>
                                </div>

                                <div class="alert alert-success border-0 mb-0">
                                    <h6 class="fw-bold">Hasil:</h6>
                                    <ul class="small mb-0">
                                        <li>Item muncul di tabel</li>
                                        <li>Total: Rp 125,000 (5 × Rp 25,000)</li>
                                        <li>Klik "KIRIM KE KASIR SEKARANG"</li>
                                        <li>SPK siap di-process kasir!</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Example 2 -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-success text-white fw-bold">
                                <i class="fas fa-shopping-cart me-2"></i>Kasus 2: Order Marketplace Tokopedia
                            </div>
                            <div class="card-body">
                                <h6 class="fw-bold text-dark">Situasi:</h6>
                                <ul class="small">
                                    <li>Order masuk dari Tokopedia</li>
                                    <li>Konsumen: Ibu Rina</li>
                                    <li>Resi: TPD-2024-12345678</li>
                                    <li>Pesanan: Sticker Vinyl Custom, 2.5 meter × 1.2m lebar</li>
                                </ul>

                                <h6 class="fw-bold text-dark">Langkah Sales:</h6>
                                <div class="bg-light p-3 rounded mb-2">
                                    <p class="small mb-1 fw-bold text-primary">Step 1: Pilih Konsumen & Marketplace</p>
                                    <ol class="small mb-0">
                                        <li>Pilih konsumen "Ibu Rina" (atau tambah baru)</li>
                                        <li>Pilih metode: <strong>MARKETPLACE</strong></li>
                                        <li>Dropdown muncul:
                                            <ul>
                                                <li>Platform: TOKOPEDIA</li>
                                                <li>No. Resi: TPD-2024-12345678</li>
                                            </ul>
                                        </li>
                                        <li>Klik "MULAI BUAT SPK"</li>
                                    </ol>
                                </div>

                                <div class="bg-light p-3 rounded mb-2">
                                    <p class="small mb-1 fw-bold text-success">Step 2: Tambah Item Custom Size</p>
                                    <ol class="small mb-0">
                                        <li>Ketik "Sticker Vinyl" → pilih dari dropdown</li>
                                        <li>Pilih Sisi: "1 Sisi"</li>
                                        <li><strong>Input ukuran:</strong>
                                            <ul>
                                                <li>Panjang: 2.5 (meter)</li>
                                                <li>Lebar: 1.2 (meter)</li>
                                            </ul>
                                        </li>
                                        <li>Qty: 1 (karena sudah include panjang 2.5m)</li>
                                        <li>Klik "TAMBAHKAN ITEM"</li>
                                    </ol>
                                </div>

                                <div class="alert alert-success border-0 mb-0">
                                    <h6 class="fw-bold">Hasil:</h6>
                                    <ul class="small mb-0">
                                        <li>Item: Sticker Vinyl 2.5m × 1.2m (1 Sisi)</li>
                                        <li>Sistem auto hitung: Luas = 2.5 × 1.2 = 3 m²</li>
                                        <li>Harga: Rp 150,000 (3m² × Rp 50,000/m²)</li>
                                        <li>Metode di invoice: MARKETPLACE - TOKOPEDIA (Resi: TPD-2024-12345678)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Example 3 -->
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-warning text-dark fw-bold">
                                <i class="fas fa-layer-group me-2"></i>Kasus 3: Order Multiple Item (Banner + Sticker +
                                Roll-Up)
                            </div>
                            <div class="card-body">
                                <h6 class="fw-bold text-dark">Situasi:</h6>
                                <ul class="small">
                                    <li>Konsumen: PT. Maju Jaya</li>
                                    <li>Pesanan:
                                        <ul>
                                            <li>Banner 3m × 1m (2 Sisi) - 2 buah</li>
                                            <li>Sticker Vinyl 1m × 0.5m - 10 buah</li>
                                            <li>Roll-Up 85x200 - 3 buah</li>
                                        </ul>
                                    </li>
                                </ul>

                                <h6 class="fw-bold text-dark">Langkah CS:</h6>
                                <div class="bg-light p-3 rounded mb-2">
                                    <p class="small mb-1 fw-bold">Setelah buat SPK, tambahkan item satu per satu:</p>
                                    <ol class="small mb-0">
                                        <li><strong>Item 1 - Banner:</strong>
                                            <ul>
                                                <li>Pilih produk: "Banner Flexy"</li>
                                                <li>Sisi: 2 Sisi</li>
                                                <li>Panjang: 3, Lebar: 1</li>
                                                <li>Qty: 2</li>
                                                <li>Enter → Item masuk</li>
                                            </ul>
                                        </li>
                                        <li><strong>Item 2 - Sticker:</strong>
                                            <ul>
                                                <li>Pilih produk: "Sticker Vinyl"</li>
                                                <li>Sisi: 1 Sisi</li>
                                                <li>Panjang: 1, Lebar: 0.5</li>
                                                <li>Qty: 10</li>
                                                <li>Enter → Item masuk</li>
                                            </ul>
                                        </li>
                                        <li><strong>Item 3 - Roll-Up:</strong>
                                            <ul>
                                                <li>Pilih produk: "Roll-Up 85x200"</li>
                                                <li>Sisi: 1 Sisi (default)</li>
                                                <li>Ukuran: Tetap (no input)</li>
                                                <li>Qty: 3</li>
                                                <li>Enter → Item masuk</li>
                                            </ul>
                                        </li>
                                    </ol>
                                </div>

                                <div class="alert alert-success border-0 mb-0">
                                    <h6 class="fw-bold">Hasil Akhir di Tabel:</h6>
                                    <table class="table table-sm table-bordered mb-2">
                                        <thead class="bg-light">
                                            <tr class="small">
                                                <th>Produk</th>
                                                <th>Ukuran</th>
                                                <th>Qty</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="small">
                                            <tr>
                                                <td>Banner Flexy (2 Sisi)</td>
                                                <td>3m × 1m</td>
                                                <td>2</td>
                                                <td>Rp 360,000</td>
                                            </tr>
                                            <tr>
                                                <td>Sticker Vinyl (1 Sisi)</td>
                                                <td>1m × 0.5m</td>
                                                <td>10</td>
                                                <td>Rp 250,000</td>
                                            </tr>
                                            <tr>
                                                <td>Roll-Up 85x200</td>
                                                <td>85cm × 200cm</td>
                                                <td>3</td>
                                                <td>Rp 525,000</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <p class="small mb-0 fw-bold text-primary">
                                        <i class="fas fa-check-circle me-1"></i> Total SPK: Rp 1,135,000 (3 item, 15
                                        unit total)
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Tutup
                </button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i>Cetak Panduan
                </button>
            </div>
        </div>
    </div>
</div>