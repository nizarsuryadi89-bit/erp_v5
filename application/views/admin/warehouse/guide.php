<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #1a1c23 0%, #2c3e50 100%);
        --accent-blue: #6366f1;
        --slate-soft: #f8fafc;
        --slate-dark: #1e293b;
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .guide-header {
        background: var(--primary-gradient);
        color: white;
        padding: 3rem 0;
        margin-bottom: 2.5rem;
        border-radius: 0 0 30px 30px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }

    .guide-step {
        background: white;
        border-radius: 24px;
        padding: 2.5rem;
        margin-bottom: 2rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid #f1f5f9;
        position: relative;
        transition: transform 0.3s ease;
    }

    .guide-step:hover {
        transform: translateY(-5px);
        border-color: var(--accent-blue);
    }

    .step-number {
        position: absolute;
        top: -20px;
        left: 40px;
        width: 50px;
        height: 50px;
        background: var(--accent-blue);
        color: white;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 1.5rem;
        box-shadow: 0 5px 15px rgba(99, 102, 241, 0.4);
    }

    .guide-icon {
        width: 60px;
        height: 60px;
        background: var(--slate-soft);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: var(--accent-blue);
        margin-bottom: 1.5rem;
    }

    .guide-title {
        font-weight: 900;
        color: var(--slate-dark);
        margin-bottom: 1rem;
        font-size: 1.5rem;
    }

    .guide-text {
        color: #64748b;
        line-height: 1.6;
        font-size: 1rem;
    }

    .flow-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: var(--slate-soft);
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.85rem;
        color: var(--slate-dark);
        margin-bottom: 1.5rem;
        border: 1px solid #e2e8f0;
    }

    .flow-badge i {
        color: var(--accent-blue);
    }

    .action-list {
        list-style: none;
        padding: 0;
        margin: 1.5rem 0 0 0;
    }

    .action-list li {
        padding: 8px 0;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        font-weight: 600;
        color: #475569;
    }

    .action-list li i {
        margin-top: 4px;
        color: #22c55e;
    }

    .tip-box {
        background: #f0f9ff;
        border-radius: 16px;
        padding: 1.25rem;
        border-left: 4px solid #0ea5e9;
        margin-top: 1.5rem;
    }

    .tip-box h6 {
        color: #0369a1;
        font-weight: 800;
        margin-bottom: 5px;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
    }

    .tip-box p {
        margin: 0;
        font-size: 0.85rem;
        color: #0c4a6e;
    }
</style>

<div class="container-fluid px-0">
    <div class="guide-header">
        <div class="container py-2 text-center">
            <h1 class="fw-900 mb-2">Panduan Operasional Gudang</h1>
            <p class="opacity-75 lead fw-500">Standar Prosedur (SOP) untuk Akurasi Manajemen Stok TGM ERP</p>
        </div>
    </div>

    <div class="container pb-5">
        <!-- STEP 0: INITIALIZATION (NEW) -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="guide-step" style="border: 2px solid var(--accent-blue); background: #f5f7ff;">
                    <div class="step-number" style="background: #1a1c23;">00</div>
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="guide-title text-primary"><i class="fa fa-play-circle me-2"></i>Persiapan Awal
                                (Untuk Pengguna Baru)</h3>
                            <p class="lead fw-600 text-dark mb-4">
                                "Saat pertama kali sistem digunakan, stok di komputer mungkin masih NOL. Mari selaraskan
                                data dunia nyata ke dalam sistem dengan <strong>Stok Opname Awal</strong>."
                            </p>

                            <div class="timeline-steps d-flex flex-column gap-3">
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 30px; height: 30px; font-size: 0.8rem;">1</div>
                                    <div>
                                        <h6 class="fw-800 mb-1">Cetak / Siapkan Daftar Barang</h6>
                                        <p class="small text-muted mb-0">Buka menu <strong>Master Material</strong>,
                                            pastikan semua jenis barang sudah terdaftar namanya.</p>
                                    </div>
                                </div>
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 30px; height: 30px; font-size: 0.8rem;">2</div>
                                    <div>
                                        <h6 class="fw-800 mb-1">Hitung Manual di Gudang</h6>
                                        <p class="small text-muted mb-0">Petugas berkeliling gudang untuk menghitung
                                            jumlah fisik setiap material secara teliti.</p>
                                    </div>
                                </div>
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 30px; height: 30px; font-size: 0.8rem;">3</div>
                                    <div>
                                        <h6 class="fw-800 mb-1">Input ke Menu 'Stok Opname'</h6>
                                        <p class="small text-muted mb-0">Pilih barangnya, biarkan 'Stok Aplikasi'
                                            bernilai 0, lalu isi <strong>'Stok Real'</strong> dengan hasil hitung manual
                                            Anda.</p>
                                    </div>
                                </div>
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 30px; height: 30px; font-size: 0.8rem;"><i
                                            class="fa fa-check"></i></div>
                                    <div>
                                        <h6 class="fw-800 mb-1 text-success">Aktifkan 'Sync Now'</h6>
                                        <p class="small text-muted mb-0">Klik Simpan. Sekarang stok sistem akan berubah
                                            dari 0 menjadi angka yang Anda masukkan tadi!</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center d-none d-md-block">
                            <div class="p-4 bg-white rounded-4 shadow-sm border">
                                <i class="fa fa-boxes fa-4x text-accent-blue mb-3"></i>
                                <div class="h5 fw-900 mb-2">Dunia Nyata</div>
                                <i class="fa fa-arrow-down text-muted mb-2"></i>
                                <div class="h5 fw-900 text-success">Sistem ERP</div>
                                <p class="small text-muted mt-3 mb-0">Sinkronisasi satu arah untuk memulai saldo awal.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- STEP 1: MASTER DATA -->
            <div class="col-md-6 mb-4">
                <div class="guide-step h-100">
                    <div class="step-number">01</div>
                    <div class="guide-icon">
                        <i class="fa fa-database"></i>
                    </div>
                    <h3 class="guide-title">Persiapan Master Data</h3>
                    <div class="flow-badge">
                        <i class="fa fa-sync"></i> Master Bahan & Varian
                    </div>
                    <p class="guide-text">
                        Pastikan setiap item memiliki data dasar yang lengkap sebelum melakukan transaksi apapun.
                    </p>
                    <ul class="action-list">
                        <li><i class="fa fa-check-circle"></i> Gunakan <strong>Barcode Asli</strong> yang tertera pada
                            kemasan produk jika ada.</li>
                        <li><i class="fa fa-check-circle"></i> Jika produk tidak memiliki barcode, silakan <strong>Cetak
                                Barcode</strong> dari sistem (Menu Label).</li>
                        <li><i class="fa fa-check-circle"></i> <strong>Set Konversi Satuan:</strong> Sangat penting!
                            Masukkan nilai konversi (misal: 1 Rim = 500 Lembar) agar laporan pemakaian tampil akurat
                            dalam unit produksi.</li>
                        <li><i class="fa fa-check-circle"></i> Gunakan Fitur 'Varian' untuk spesifikasi berbeda.</li>
                    </ul>
                    <div class="tip-box">
                        <h6>💡 Pro-Tip</h6>
                        <p>Barcode yang konsisten adalah kunci kecepatan saat proses Scan di tahap berikutnya.</p>
                    </div>
                </div>
            </div>

            <!-- STEP 2: PURCHASE -->
            <div class="col-md-6 mb-4">
                <div class="guide-step h-100">
                    <div class="step-number">02</div>
                    <div class="guide-icon">
                        <i class="fa fa-truck-loading"></i>
                    </div>
                    <h3 class="guide-title">Penerimaan Barang (Purchase)</h3>
                    <div class="flow-badge">
                        <i class="fa fa-arrow-right"></i> Supplier &rarr; Gudang Utama
                    </div>
                    <p class="guide-text">
                        Mencatat material yang masuk dari vendor/pemasok ke saldo stok sistem.
                    </p>
                    <ul class="action-list">
                        <li><i class="fa fa-check-circle"></i> Pilih Supplier dan Input No. Faktur Fisik.</li>
                        <li><i class="fa fa-check-circle"></i> Scan/Input Qty sesuai barang yang datang.</li>
                        <li><i class="fa fa-check-circle"></i> Pilih Metode Bayar (Hutang akan otomatis tercatat).</li>
                    </ul>
                    <div class="tip-box" style="background: #fff7ed; border-left-color: #f97316;">
                        <h6 style="color: #9a3412;">⚠️ Penting</h6>
                        <p>Jangan selesaikan transaksi jika Qty di sistem tidak sama dengan Surat Jalan fisik.</p>
                    </div>
                </div>
            </div>

            <!-- STEP 3: DISPATCH -->
            <div class="col-md-6 mb-4">
                <div class="guide-step h-100">
                    <div class="step-number">03</div>
                    <div class="guide-icon">
                        <i class="fa fa-paper-plane"></i>
                    </div>
                    <h3 class="guide-title">Pengiriman Ke Produksi</h3>
                    <div class="flow-badge">
                        <i class="fa fa-exchange-alt"></i> Gudang Utama &rarr; Produksi 1/2
                    </div>
                    <p class="guide-text">
                        Memindahkan tanggung jawab stok dari gudang besar ke lantai kerja produksi.
                    </p>
                    <ul class="action-list">
                        <li><i class="fa fa-check-circle"></i> Pilih Gudang Tujuan (Produksi 1 atau 2).</li>
                        <li><i class="fa fa-check-circle"></i> Scan material yang dibawa keluar.</li>
                        <li><i class="fa fa-check-circle"></i> Klik Finalize untuk update stok kedua belah pihak.</li>
                    </ul>
                    <div class="tip-box" style="background: #eff6ff; border-left-color: #3b82f6;">
                        <h6 style="color: #1e40af;">📡 Pantauan Real-Time</h6>
                        <p>Pantau status pengiriman melalui sidebar <strong>"Live Dispatch"</strong> untuk memastikan
                            barang benar-benar dikirim keluar pusat.</p>
                    </div>
                </div>
            </div>

            <!-- STEP 4: OPNAME -->
            <div class="col-md-6 mb-4">
                <div class="guide-step h-100">
                    <div class="step-number">04</div>
                    <div class="guide-icon">
                        <i class="fa fa-clipboard-check"></i>
                    </div>
                    <h3 class="guide-title">Audit Stok (Opname)</h3>
                    <div class="flow-badge">
                        <i class="fa fa-search"></i> Sistem vs Fisik
                    </div>
                    <p class="guide-text">
                        Proses rekonsiliasi berkala untuk memastikan angka di komputer sesuai dengan rak gudang.
                    </p>
                    <ul class="action-list">
                        <li><i class="fa fa-check-circle"></i> Lakukan hitung fisik di lokasi gudang.</li>
                        <li><i class="fa fa-check-circle"></i> Masukkan angka aktual ke kolom 'Stock Real'.</li>
                        <li><i class="fa fa-check-circle"></i> Gunakan 'Sync Now' jika ingin langsung mengoreksi saldo.
                        </li>
                    </ul>
                </div>
            </div>

            <!-- STEP 5: MONITORING (NEW) -->
            <div class="col-md-12 mb-4">
                <div class="guide-step" style="border-left: 10px solid #f43f5e; background: #fff1f2;">
                    <div class="step-number" style="background: #f43f5e;">05</div>
                    <div class="row align-items-center">
                        <div class="col-md-9">
                            <h3 class="guide-title"><i class="fas fa-chart-line me-2 text-danger"></i>Monitoring &
                                Akurasi Lanjut</h3>
                            <p class="guide-text fw-600 text-dark">
                                "Gudang kini lebih cerdas! Anda tidak hanya melihat angka stok statis, tapi juga
                                pergerakan real-time."
                            </p>
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="fw-800 text-danger"><i class="fas fa-layer-group me-1"></i> Dual Unit
                                        Display</h6>
                                    <p class="small text-muted">Setiap stok kini tampil dalam dua satuan sekaligus
                                        (misal: <strong>Rim</strong> & <strong>Lembar</strong>). Memudahkan pengecekan
                                        unit besar di rak maupun unit kecil pemakaian produksi.</p>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="fw-800 text-danger"><i class="fas fa-hourglass-half me-1"></i> Stok
                                        Tersedia (Net)</h6>
                                    <p class="small text-muted">Angka <strong>Stok Tersedia</strong> secara otomatis
                                        dikurangi oleh pemakaian produksi bulan ini. Anda tahu saldo bersih yang siap
                                        digunakan tanpa perlu hitung manual.</p>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="fw-800 text-danger"><i class="fas fa-satellite-dish me-1"></i> Live
                                        Feedback</h6>
                                    <p class="small text-muted">Sidebar <strong>Live Consumption</strong> memberikan
                                        sinyal visual (Pulse) setiap kali operator mesin mengambil bahan, memastikan
                                        data sistem selalu 'Up-to-Date'.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center d-none d-md-block">
                            <div class="bg-white p-3 rounded-4 shadow-sm">
                                <i class="fas fa-broadcast-tower fa-4x text-danger mb-2"></i>
                                <div class="badge bg-soft-danger text-danger rounded-pill px-3 py-1 fw-800">100%
                                    REALTIME</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm p-4 text-center rounded-4 mb-5" style="background: #eff6ff;">
                    <h4 class="fw-900 text-primary mb-3">Butuh Bantuan Lebih Lanjut?</h4>
                    <p class="text-muted mb-4">Silakan hubungi IT Support atau Supervisor Gudang jika menemui kendala
                        pada sinkronisasi data.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="<?= base_url('warehouse') ?>" class="btn btn-primary px-4 fw-800 rounded-pill">
                            <i class="fa fa-arrow-left me-2"></i>Kembali ke Dashboard
                        </a>
                        <button class="btn btn-outline-primary px-4 fw-800 rounded-pill" onclick="window.print()">
                            <i class="fa fa-print me-2"></i>Cetak Panduan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>