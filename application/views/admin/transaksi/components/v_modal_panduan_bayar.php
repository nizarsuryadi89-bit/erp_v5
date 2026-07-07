<!-- Modal Panduan Lengkap Menu Kasir (Pembayaran) -->
<style>
    #modalPanduanBayar .modal-body {
        font-size: 1.05rem;
        line-height: 1.6;
    }

    #modalPanduanBayar .nav-link {
        font-size: 1.1rem !important;
        padding: 12px 10px;
    }

    #modalPanduanBayar h5 {
        font-size: 1.4rem !important;
    }

    #modalPanduanBayar h6 {
        font-size: 1.2rem !important;
    }

    #modalPanduanBayar .small,
    #modalPanduanBayar small,
    #modalPanduanBayar ul.small,
    #modalPanduanBayar ol.small {
        font-size: 1rem !important;
    }

    #modalPanduanBayar .alert i {
        font-size: 1.2rem;
    }

    #modalPanduanBayar .accordion-button {
        font-size: 1.15rem;
    }

    #modalPanduanBayar .card-header {
        font-size: 1.15rem;
    }

    #modalPanduanBayar .nav-tabs .nav-link {
        color: #495057;
        border: none;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
        border-radius: 0;
    }

    #modalPanduanBayar .nav-tabs .nav-link.active {
        color: #0d6efd !important;
        background-color: transparent !important;
        border-bottom: 3px solid #0d6efd;
    }

    #modalPanduanBayar .nav-tabs .nav-link:hover {
        background-color: rgba(13, 110, 253, 0.05);
        border-bottom: 3px solid rgba(13, 110, 253, 0.2);
    }
</style>

<div class="modal fade" id="modalPanduanBayar" tabindex="-1" aria-labelledby="modalPanduanBayarLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white border-0"
                style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
                <h5 class="modal-title fw-bold" id="modalPanduanBayarLabel">
                    <i class="fas fa-book-reader me-2"></i>
                    Panduan Lengkap - Modul Kasir & Pembayaran v1.0
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs nav-fill border-bottom-0" role="tablist" style="background: #ffffff;">
                    <li class="nav-item">
                        <a class="nav-link active fw-bold" data-bs-toggle="tab" href="#bayarOverview">
                            <i class="fas fa-info-circle me-1"></i> Ringkasan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-bold" data-bs-toggle="tab" href="#bayarWorkflow">
                            <i class="fas fa-tasks me-1"></i> Alur Kerja
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-bold" data-bs-toggle="tab" href="#bayarMetode">
                            <i class="fas fa-credit-card me-1"></i> Metode Bayar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-bold" data-bs-toggle="tab" href="#bayarFitur">
                            <i class="fas fa-star me-1"></i> Fitur Unggulan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-bold" data-bs-toggle="tab" href="#bayarTrouble">
                            <i class="fas fa-wrench me-1"></i> Troubleshooting
                        </a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content p-4">
                    <!-- Tab 1: Overview -->
                    <div class="tab-pane fade show active" id="bayarOverview">
                        <h5 class="fw-bold text-primary mb-3">
                            <i class="fas fa-wallet me-2"></i>Modul Pembayaran
                        </h5>

                        <div class="alert alert-primary border-0 shadow-sm mb-3">
                            <h6 class="fw-bold">✅ Fungsi Utama Halaman Ini</h6>
                            <ul class="mb-0 small">
                                <li><strong>Checkout:</strong> Tahap akhir pemilihan metode pembayaran dan pelunasan
                                    tagihan.</li>
                                <li><strong>Validasi Biaya:</strong> Memasukkan diskon, ongkir, dan menghitung grand
                                    total otomatis.</li>
                                <li><strong>Rekonsiliasi:</strong> Mencatat arus kas masuk sesuai cabang dan rekening
                                    yang dipilih.</li>
                                <li><strong>Komunikasi:</strong> Mengirim rincian nota ke WhatsApp konsumen
                                    sebelum/sesudah bayar.</li>
                            </ul>
                        </div>

                        <div class="alert alert-info border-0 shadow-sm mb-3">
                            <h6 class="fw-bold">✅ Penting: Status Berkas</h6>
                            <ul class="mb-0 small">
                                <li><strong>Belum Bayar:</strong> SPK tertahan di sistem kasir dan belum masuk antrian
                                    produksi.</li>
                                <li><strong>Lunas/DP:</strong> SPK akan otomatis diteruskan ke bagian
                                    <strong>Produksi</strong>.
                                </li>
                                <li><strong>Struk:</strong> Sistem akan otomatis menanyakan cetak struk setelah tombol
                                    Simpan diklik.</li>
                            </ul>
                        </div>

                        <hr class="my-4">

                        <h5 class="fw-bold text-dark mb-3">
                            <i class="fas fa-clock me-2"></i>Update Dateline Produksi
                        </h5>
                        <p class="small">Di halaman ini, Kasir juga bisa melakukan penyesuaian <strong>Dateline (Tgl &
                                Jam Selesai)</strong> per item jika diperlukan, sebelum transaksi benar-benar disimpan.
                        </p>
                    </div>

                    <!-- Tab 2: Workflow -->
                    <div class="tab-pane fade" id="bayarWorkflow">
                        <h5 class="fw-bold text-primary mb-4">
                            <i class="fas fa-route me-2"></i>Alur Proses Pembayaran
                        </h5>

                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-primary text-white fw-bold">
                                1. Peninjauan (Preview)
                            </div>
                            <div class="card-body">
                                <ul class="small">
                                    <li>Klik tombol <strong>"Preview Transaksi"</strong> untuk melihat ringkasan item.
                                    </li>
                                    <li>Gunakan fitur <strong>WA</strong> untuk mengirim rincian ke konsumen jika mereka
                                        meminta konfirmasi harga.</li>
                                    <li>Pastikan Qty dan Spesifikasi sudah sesuai dengan fisik order/file.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-info text-white fw-bold">
                                2. Input Biaya Tambahan (Jika Ada)
                            </div>
                            <div class="card-body">
                                <ul class="small">
                                    <li>Klik <strong>"Proses Pembayaran"</strong> untuk membuka modal kasir.</li>
                                    <li><strong>Diskon:</strong> Isi jika pelanggan mendapatkan potongan (mengurangi
                                        total).</li>
                                    <li><strong>Ongkir:</strong> Isi jika pesanan dikirim (menambah total).</li>
                                    <li>Sistem akan menghitung <strong>Grand Total</strong> secara real-time.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-success text-white fw-bold">
                                3. Eksekusi Pembayaran
                            </div>
                            <div class="card-body">
                                <ul class="small">
                                    <li>Pilih <strong>Metode Bayar</strong> (Tunai, Transfer, dll).</li>
                                    <li>Input jumlah uang yang diterima.</li>
                                    <li>Pilih <strong>Cabang Penerima</strong> dan <strong>Rekening/EDC</strong> yang
                                        digunakan.</li>
                                    <li>Klik <strong>"Simpan & Cetak Struk"</strong>.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 3: Metode Bayar -->
                    <div class="tab-pane fade" id="bayarMetode">
                        <h5 class="fw-bold text-primary mb-4">
                            <i class="fas fa-credit-card me-2"></i>Penjelasan Metode Pembayaran
                        </h5>

                        <div class="accordion" id="metodeAccordion">
                            <div class="accordion-item border-0 shadow-sm mb-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#metodeCash">
                                        💰 TUNAI / CASH
                                    </button>
                                </h2>
                                <div id="metodeCash" class="accordion-collapse collapse show"
                                    data-bs-parent="#metodeAccordion">
                                    <div class="accordion-body small">
                                        Input nominal uang fisik yang diberikan konsumen. Sistem akan menghitung
                                        <strong>Kembalian</strong> secara otomatis. Pastikan uang sudah dihitung manual
                                        sebelum input.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item border-0 shadow-sm mb-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#metodeTransfer">
                                        🏦 TRANSFER BANK
                                    </button>
                                </h2>
                                <div id="metodeTransfer" class="accordion-collapse collapse"
                                    data-bs-parent="#metodeAccordion">
                                    <div class="accordion-body small">
                                        Wajib memilih <strong>Rekening Tujuan</strong> yang sesuai. Kasir harus
                                        memastikan dana sudah masuk ke mutasi bank sebelum melakukan finalisasi
                                        transaksi di sistem.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item border-0 shadow-sm mb-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#metodePiutang">
                                        🤝 PIUTANG (DP)
                                    </button>
                                </h2>
                                <div id="metodePiutang" class="accordion-collapse collapse"
                                    data-bs-parent="#metodeAccordion">
                                    <div class="accordion-body small">
                                        Digunakan jika konsumen membayar DP (Uang Muka) atau jika pelanggan corporate
                                        yang bayar belakangan (Tempo).
                                        <ul>
                                            <li>Input nominal DP (bisa Rp 0 jika full piutang).</li>
                                            <li>Pilih cara bayar DP-nya (Cash/Transfer/EDC).</li>
                                            <li>Sistem akan mencatat sisa tagihan sebagai <strong>Piutang</strong> atas
                                                nama konsumen tersebut.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 4: Fitur Unggulan -->
                    <div class="tab-pane fade" id="bayarFitur">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm bg-light">
                                    <div class="card-body">
                                        <h6 class="fw-bold text-success"><i class="fab fa-whatsapp me-2"></i>WhatsApp
                                            Integration</h6>
                                        <p class="small">Anda bisa mengirim nota digital ke pelanggan dalam satu klik.
                                            Pesan sudah termasuk rincian item, nomor SPK, dan total tagihan.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm bg-light">
                                    <div class="card-body">
                                        <h6 class="fw-bold text-primary"><i class="fas fa-image me-2"></i>Capture
                                            Preview</h6>
                                        <p class="small">Tombol icon gambar di modal preview berfungsi untuk
                                            men-download ringkasan nota dalam bentuk file <strong>PNG/Gambar</strong>.
                                            Sangat berguna untuk dikirim ke grup internal atau pelanggan.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm bg-light">
                                    <div class="card-body">
                                        <h6 class="fw-bold text-warning"><i class="fas fa-keyboard me-2"></i>Autofill
                                            Smart Input</h6>
                                        <p class="small">Saat memilih metode (Cash/Transfer/DLL), sistem secara otomatis
                                            mengisi jumlah bayar sesuai Grand Total. Jika pelanggan bayar pas, Anda
                                            tidak perlu mengetik nominal lagi!</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 5: Troubleshooting -->
                    <div class="tab-pane fade" id="bayarTrouble">
                        <div class="alert alert-danger mb-3">
                            <h6 class="fw-bold"><i class="fas fa-exclamation-circle me-2"></i>Error: "Pilih Rekening /
                                EDC"</h6>
                            <p class="small mb-0">Jika muncul peringatan ini saat submit, artinya Anda memilih metode
                                nontunai namun belum memilih bank penerima atau terminal EDC. Silakan pilih di dropdown
                                yang tersedia.</p>
                        </div>
                        <div class="alert alert-warning mb-3">
                            <h6 class="fw-bold"><i class="fas fa-edit me-2"></i>Salah Input Nominal?</h6>
                            <p class="small mb-0">Jika transaksi sudah disimpan namun nominalnya salah, Anda harus
                                meminta <strong>Supervisor/Admin</strong> untuk melakukan koreksi melalui menu Laporan
                                Transaksi atau pembatalan SPK.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>