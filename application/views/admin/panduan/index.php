<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">
<style>
    .flow-step {
        position: relative;
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border-left: 5px solid #0d6efd;
        transition: transform 0.3s;
        z-index: 2;
    }

    .flow-step:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }

    .flow-arrow {
        font-size: 2rem;
        color: #adb5bd;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .step-icon {
        width: 50px;
        height: 50px;
        background: #e9ecef;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #0d6efd;
        margin-bottom: 15px;
    }

    .benefits-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 30px;
    }

    /* Connecting lines for desktop */
    @media (min-width: 768px) {
        .flow-container {
            display: flex;
            justify-content: space-between;
            position: relative;
            padding: 20px 0;
        }

        .flow-line {
            position: absolute;
            top: 50%;
            left: 50px;
            right: 50px;
            height: 4px;
            background: #e9ecef;
            z-index: 1;
            transform: translateY(-50%);
        }
    }
</style>

<div class="app-content">
    <?php $this->load->view('layout_admin/header_modul'); ?>

    <div class="container-fluid">

        <!-- Header Section -->
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="fw-bold text-dark">Alur Kerja Sistem ERP TGM Print</h2>
                <p class="text-muted">Panduan visual proses bisnis dari Pesanan Masuk hingga Barang Diterima.</p>
            </div>
        </div>

        <!-- FLOWCHART VISUALIZATION -->
        <div class="row g-4 mb-5 position-relative">
            <!-- Line connector (hidden on mobile) -->
            <div class="d-none d-md-block position-absolute"
                style="top: 40px; left: 5%; right: 5%; height: 3px; background: #dee2e6; z-index: 0;"></div>

            <!-- Step 1: Customer -->
            <div class="col-md-2 col-6">
                <div class="flow-step text-center border-primary">
                    <div class="step-icon bg-primary-subtle text-primary mx-auto">
                        <i class="fas fa-users"></i>
                    </div>
                    <h6 class="fw-bold">1. PELANGGAN</h6>
                    <small class="text-muted d-block lh-sm">Datang ke toko / Chat WA untuk order.</small>
                    <span class="badge bg-primary mt-2">Start</span>
                </div>
            </div>

            <!-- Step 2: Deskprint -->
            <div class="col-md-2 col-6">
                <div class="flow-step text-center border-info">
                    <div class="step-icon bg-info-subtle text-info mx-auto">
                        <i class="fas fa-desktop"></i>
                    </div>
                    <h6 class="fw-bold">2. CS/DESKPRINT</h6>
                    <small class="text-muted d-block lh-sm">Input SPK di ERP. Pilih bahan & spek.</small>
                    <span class="badge bg-info text-dark mt-2">Creates SPK</span>
                </div>
            </div>

            <!-- Step 3: Gudang & Produksi -->
            <div class="col-md-2 col-6">
                <div class="flow-step text-center border-warning">
                    <div class="step-icon bg-warning-subtle text-warning mx-auto">
                        <i class="fas fa-print"></i>
                    </div>
                    <h6 class="fw-bold">3. PRODUKSI</h6>
                    <small class="text-muted d-block lh-sm">Operator cek SPK -> Ambil Bahan -> Cetak.</small>
                    <span class="badge bg-warning text-dark mt-2">Working</span>
                </div>
            </div>

            <!-- Step 4: Finishing -->
            <div class="col-md-2 col-6">
                <div class="flow-step text-center border-secondary">
                    <div class="step-icon bg-secondary-subtle text-secondary mx-auto">
                        <i class="fas fa-cut"></i>
                    </div>
                    <h6 class="fw-bold">4. FINISHING</h6>
                    <small class="text-muted d-block lh-sm">Potong / Laminasi -> Packing Barang.</small>
                    <span class="badge bg-secondary mt-2">Processing</span>
                </div>
            </div>

            <!-- Step 5: Kasir  -->
            <div class="col-md-2 col-6">
                <div class="flow-step text-center border-success">
                    <div class="step-icon bg-success-subtle text-success mx-auto">
                        <i class="fas fa-cash-register"></i>
                    </div>
                    <h6 class="fw-bold">5. KASIR</h6>
                    <small class="text-muted d-block lh-sm">Terima Bayar -> Cetak Invoice -> WA Otomatis.</small>
                    <span class="badge bg-success mt-2">Payment</span>
                </div>
            </div>

            <!-- Step 6: Owner -->
            <div class="col-md-2 col-6">
                <div class="flow-step text-center border-dark">
                    <div class="step-icon bg-dark-subtle text-dark mx-auto">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h6 class="fw-bold">6. OWNER</h6>
                    <small class="text-muted d-block lh-sm">Monitor Laporan & Grafik Realtime.</small>
                    <span class="badge bg-dark mt-2">Monitoring</span>
                </div>
            </div>
        </div>

        <!-- IMPORTANCE SECTION -->
        <div class="row g-4">
            <div class="col-md-8">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                        <h5 class="fw-bold"><i class="fas fa-book-reader me-2 text-primary"></i> Tata Cara Penggunaan
                            per Divisi</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="accordion" id="accordionGuide">
                            <!-- CS Guide -->
                            <div class="accordion-item border-0 mb-2 shadow-sm rounded">
                                <h2 class="accordion-header">
                                    <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#guideCS">
                                        <i class="fas fa-headset me-2 text-info"></i> Panduan CS / Deskprint
                                    </button>
                                </h2>
                                <div id="guideCS" class="accordion-collapse collapse show"
                                    data-bs-parent="#accordionGuide">
                                    <div class="accordion-body bg-light rounded-bottom">
                                        <ul class="mb-0 small">
                                            <li>Buka menu <strong>Data Konsumen</strong> untuk cek pelanggan lama atau
                                                tambah baru.</li>
                                            <li>Masuk ke <strong>SPK Baru</strong>, pilih Nama Pelanggan.</li>
                                            <li>Pilih Produk, Bahan, dan Ukuran (Harga akan muncul otomatis).</li>
                                            <li>Klik <strong>Kirim ke Kasir</strong>. (Otomatis mengirim WA "Pesanan
                                                Diterima" ke pelanggan).</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Produksi Guide -->
                            <div class="accordion-item border-0 mb-2 shadow-sm rounded">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#guideProd">
                                        <i class="fas fa-cogs me-2 text-warning"></i> Panduan Operator Produksi
                                    </button>
                                </h2>
                                <div id="guideProd" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionGuide">
                                    <div class="accordion-body bg-light rounded-bottom">
                                        <ul class="mb-0 small">
                                            <li>Buka menu <strong>Operator Produksi</strong> untuk melihat antrian SPK
                                                Masuk.</li>
                                            <li>Klik tombol <strong>Proses</strong> pada SPK yang akan dikerjakan.</li>
                                            <li>Perhatikan detail pesanan dan <strong>Catatan Finishing</strong>
                                                (Potong, Laminasi, dll).</li>
                                            <li>Jika pekerjaan selesai, klik tombol <strong>Selesai</strong> agar status
                                                berubah dan siap diambil/dibayar.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Pengambilan Barang Guide -->
                            <div class="accordion-item border-0 mb-2 shadow-sm rounded">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#guidePB">
                                        <i class="fas fa-box-open me-2 text-danger"></i> Panduan Pengambilan Barang
                                        (Gudang)
                                    </button>
                                </h2>
                                <div id="guidePB" class="accordion-collapse collapse" data-bs-parent="#accordionGuide">
                                    <div class="accordion-body bg-light rounded-bottom">
                                        <ul class="mb-0 small">
                                            <li>Buka menu <strong>Pengambilan Barang</strong> (Bisa scan Barcode SPK).
                                            </li>
                                            <li>Masukkan Nomor SPK untuk membuka form pengambilan.</li>
                                            <li>Pilih <strong>Bahan Baku</strong> yang diambil dari gudang
                                                (Kertas/Tinta/dll).</li>
                                            <li>Masukkan Qty yang diambil, lalu klik <strong>Simpan</strong>.</li>
                                            <li>Stok gudang akan otomatis berkurang sesuai input.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Gudang Guide -->
                            <div class="accordion-item border-0 mb-2 shadow-sm rounded">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#guideGudang">
                                        <i class="fas fa-boxes me-2 text-primary"></i> Panduan Petugas Gudang (Stok &
                                        Supplier)
                                    </button>
                                </h2>
                                <div id="guideGudang" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionGuide">
                                    <div class="accordion-body bg-light rounded-bottom">
                                        <ul class="mb-0 small">
                                            <li><strong>Barang Masuk:</strong> Buka menu <strong>Material</strong> ->
                                                Klik Tambah Stok jika ada barang datang dari Supplier.</li>
                                            <li><strong>Stok Opname:</strong> Lakukan minimal 1x sebulan. Buka menu
                                                <strong>Stok Opname</strong> untuk sesuaikan jumlah fisik & sistem.</li>
                                            <li><strong>Cek Riwayat:</strong> Gunakan menu <strong>Kartu Stok</strong>
                                                untuk melacak siapa yang mengambil barang jika ada selisih.</li>
                                            <li><strong>Master Data:</strong> Tambahkan jenis kertas/tinta baru di menu
                                                <strong>Material</strong> sebelum bisa digunakan transaksi.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Kasir Guide -->
                            <div class="accordion-item border-0 mb-2 shadow-sm rounded">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#guideKasir">
                                        <i class="fas fa-calculator me-2 text-success"></i> Panduan Kasir
                                    </button>
                                </h2>
                                <div id="guideKasir" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionGuide">
                                    <div class="accordion-body bg-light rounded-bottom">
                                        <ul class="mb-0 small">
                                            <li>Buka menu <strong>Pembayaran Kasir</strong>.</li>
                                            <li>Pilih SPK yang akan dibayar.</li>
                                            <li>Masukkan jumlah bayar (Tunai/Transfer/QRIS).</li>
                                            <li>Klik <strong>Simpan Transaksi</strong>. (Otomatis kirim WA Lunas & Cetak
                                                Struk).</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="benefits-card h-100 shadow">
                    <h4 class="fw-bold mb-4"><i class="fas fa-star me-2"></i> Mengapa ERP Penting?</h4>

                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle fa-2x text-white-50"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="fw-bold mb-1">Cegah Kebocoran Uang</h6>
                            <p class="small mb-0 opacity-75">Setiap rupiah yang masuk dan keluar tercatat otomatis.
                                Tidak ada lagi "uang nyelip".</p>
                        </div>
                    </div>

                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0">
                            <i class="fas fa-boxes fa-2x text-white-50"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="fw-bold mb-1">Stok Selalu Akurat</h6>
                            <p class="small mb-0 opacity-75">Bahan otomatis berkurang saat SPK dibuat. Mencegah
                                kehabisan bahan mendadak.</p>
                        </div>
                    </div>

                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0">
                            <i class="fas fa-rocket fa-2x text-white-50"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="fw-bold mb-1">Pelayanan Cepat = Pelanggan Puas</h6>
                            <p class="small mb-0 opacity-75">Nota via WA, proses cepat, data pelanggan tersimpan rapi.
                                Pelanggan akan kembali lagi.</p>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top border-white border-opacity-25 text-center">
                        <small class="d-block mb-2 opacity-75">Didukung oleh:</small>
                        <h5 class="fw-900 mb-0">TGM Core ERP Sistem v4.0</h5>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>