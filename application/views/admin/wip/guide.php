<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --accent-blue: #3498db;
        --accent-green: #27ae60;
        --accent-orange: #f39c12;
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

    .guide-section {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid #f1f5f9;
        page-break-inside: avoid;
    }

    .section-title {
        font-weight: 900;
        color: var(--slate-dark);
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .print-only {
        display: none;
    }

    @media print {
        body { background: white; }
        .no-print { display: none !important; }
        .print-only { display: block; }
        .guide-section { box-shadow: none; border: 1px solid #ddd; }
        .guide-header { border-radius: 0; box-shadow: none; padding: 2rem 0; }
        .card, .alert, .accordion-item { border: 1px solid #ccc !important; box-shadow: none !important; }
    }
</style>

<div class="container-fluid px-0">
    <!-- HERO HEADER -->
    <div class="guide-header text-center">
        <div class="container py-2">
            <h1 class="fw-900 mb-2"><i class="fas fa-book-reader me-2"></i> Panduan Lengkap Operator</h1>
            <p class="opacity-75 lead fw-500">Modul Produksi WIP v2.0 - Standar Operasional Prosedur</p>
        </div>
    </div>

    <div class="container pb-5">
        
        <!-- SECTION 1: RINGKASAN -->
        <div class="guide-section border-start border-5 border-primary">
            <h4 class="section-title text-primary"><i class="fas fa-rocket"></i> Apa yang Baru di WIP v2?</h4>
            
            <div class="alert alert-success border-0 shadow-sm mb-3">
                <h6 class="fw-bold">✅ Filter Cabang & Titipan Antar-Cabang</h6>
                <ul class="mb-0 small">
                    <li><strong>Filter Cabang:</strong> Admin/Owner kini bisa memantau antrian khusus di cabang tertentu melalui dropdown cabang di kanan atas.</li>
                    <li><strong>Pekerjaan Titipan:</strong> SPK yang berasal dari cabang lain namun diproduksi di cabang Anda akan diberi label khusus <span class="badge bg-danger text-white">TITIPAN DARI ...</span> agar mudah dibedakan.</li>
                    <li><strong>Operator Mode:</strong> Daftar produksi secara otomatis terkunci pada lokasi kerja operator masing-masing demi keamanan.</li>
                </ul>
            </div>

            <div class="alert alert-info border-0 shadow-sm mb-3">
                <h6 class="fw-bold">✅ Mode "Per Bahan" & "Per SPK" (Khusus LF & A3+)</h6>
                <ul class="mb-0 small">
                    <li><strong>Per Bahan (Material-Centric):</strong> Item dikelompokkan berdasarkan nama bahan (misal: Art Carton 260gr, Flexy 280gr). Sangat mempercepat kerja karena operator tidak perlu bolak-balik ganti roll/kertas di mesin! Terdapat fitur Ripping & Nesting.</li>
                    <li><strong>Per SPK (Order-Centric):</strong> Tampilan klasik per nomor SPK untuk memantau apakah pesanan pelanggan sudah komplit atau belum.</li>
                </ul>
            </div>

            <div class="alert alert-primary border-0 shadow-sm mb-3">
                <h6 class="fw-bold">✅ Loading Ekstra Cepat & Pembagian Departemen</h6>
                <ul class="mb-0 small">
                    <li>Sistem sekarang membaca data per kategori (Server-Side). Saat Anda berpindah kategori (misal dari A3+ ke Merchandise), sistem akan memuat ulang hanya data departemen tersebut. Ini membuat halaman terbuka secara kilat (Instant Load).</li>
                </ul>
            </div>

            <div class="alert alert-warning border-0 shadow-sm mb-3">
                <h6 class="fw-bold">✅ Keamanan dengan PIN Verifikasi</h6>
                <ul class="mb-0 small">
                    <li>Semua aksi (Mulai, Selesai, Lapor Rijek) memerlukan <strong>PIN Produksi</strong> dari masing-masing operator. Jejak digital (Audit Trail) tercatat rapi.</li>
                </ul>
            </div>

            <hr class="my-4">

            <h5 class="fw-bold text-danger mb-3">
                <i class="fas fa-key me-2"></i>Cara Mendapatkan PIN Produksi
            </h5>
            <ol class="small mb-3">
                <li>Hubungi Admin atau HRD</li>
                <li>Minta PIN Produksi (4-6 digit)</li>
                <li><strong class="text-danger">JANGAN BAGIKAN PIN</strong> ke operator lain</li>
                <li>Jika lupa PIN, hubungi Admin untuk reset</li>
            </ol>

            <div class="alert alert-danger border-0 shadow-sm mb-0">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>PENTING:</strong> PIN adalah tanggung jawab pribadi. Jika ada masalah, yang
                bertanggung jawab adalah pemilik PIN!
            </div>
        </div>

        <!-- SECTION 2: ALUR KERJA -->
        <div class="guide-section border-start border-5 border-info">
            <h4 class="section-title text-info"><i class="fas fa-route"></i> Alur Kerja Produksi</h4>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-primary text-white fw-bold">
                            <i class="fas fa-filter me-2"></i>Langkah 1: Setup Tampilan
                        </div>
                        <div class="card-body">
                            <ol class="small mb-0">
                                <li>Jika Anda Admin, pilih <strong>Cabang</strong> dari dropdown pojok kanan atas.</li>
                                <li>Pilih <strong>Kategori Produksi</strong> (A3+, Large Format, Merchandise, dll) dari dropdown Kategori. Halaman akan reload sekilas khusus untuk menarik data Anda.</li>
                                <li>Gunakan mode <strong>Per Bahan</strong> untuk efisiensi produksi massal, atau <strong>Per SPK</strong> untuk memeriksa keseluruhan pesanan.</li>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-info text-white fw-bold">
                            <i class="fas fa-eye me-2"></i>Langkah 2: Lihat Daftar Pekerjaan
                        </div>
                        <div class="card-body">
                            <p class="small mb-2 fw-bold">Status Pekerjaan:</p>
                            <ul class="small mb-0">
                                <li>🟡 <strong>Pending:</strong> Belum dimulai</li>
                                <li>🔵 <strong>In Progress:</strong> Sedang dikerjakan</li>
                                <li>🟢 <strong>Done:</strong> Sudah selesai</li>
                                <li>📦 <strong>On PB:</strong> Sudah di Pengambilan Barang</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-success text-white fw-bold">
                            <i class="fas fa-play me-2"></i>Langkah 3: Mulai Pekerjaan
                        </div>
                        <div class="card-body">
                            <ol class="small mb-2">
                                <li>Cari pekerjaan yang mau dikerjakan</li>
                                <li>Klik tombol <strong>"Mulai"</strong> atau <strong>"Start"</strong></li>
                                <li>Masukkan <strong>PIN Produksi</strong> Anda</li>
                                <li>Klik <strong>"Konfirmasi"</strong></li>
                                <li>Status berubah menjadi <strong>"In Progress"</strong></li>
                            </ol>
                            <div class="alert alert-warning border-0 mb-0 small p-2">
                                <i class="fas fa-info-circle me-1"></i>
                                <strong>Catatan:</strong> Hanya 1 step yang bisa "In Progress" per SPK. Jika ada
                                step lain yang masih "In Progress", selesaikan dulu!
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-danger text-white fw-bold">
                            <i class="fas fa-check-circle me-2"></i>Langkah 4: Selesaikan Pekerjaan
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="fw-bold text-primary" style="font-size: 0.9rem;">Untuk Produk METER (Sticker, Banner Roll)</h6>
                                    <ol class="small mb-3">
                                        <li>Klik tombol <strong>"Selesai"</strong></li>
                                        <li>Masukkan:
                                            <ul>
                                                <li><strong>Panjang Terpakai (Meter):</strong> Masukkan panjang yang dipakai</li>
                                                <li><strong>Panjang Rusak (Meter):</strong> Jika ada yang rusak</li>
                                                <li><strong>Pilih Bahan:</strong> Pilih roll/material yang dipakai</li>
                                                <li><strong>PIN Operator:</strong> Masukkan PIN Anda</li>
                                            </ul>
                                        </li>
                                        <li>Klik <strong>"Proses"</strong></li>
                                    </ol>
                                </div>
                                <div class="col-md-4 border-start border-end px-3">
                                    <h6 class="fw-bold text-success" style="font-size: 0.9rem;">Untuk Produk PCS (X-Banner, Roll-Up, Neon Box)</h6>
                                    <ol class="small mb-3">
                                        <li>Klik tombol <strong>"Selesai"</strong></li>
                                        <li>Masukkan:
                                            <ul>
                                                <li><strong>Jumlah Diproduksi (Pcs):</strong> Masukkan jumlah yang diproduksi</li>
                                                <li><strong>Jumlah Rusak (Pcs):</strong> Jika ada yang rusak</li>
                                                <li><strong>Luas Otomatis:</strong> Sistem akan hitung otomatis (P × L × Qty)</li>
                                                <li><strong>Pilih Bahan:</strong> Pilih roll/material yang dipakai</li>
                                                <li><strong>PIN Operator:</strong> Masukkan PIN Anda</li>
                                            </ul>
                                        </li>
                                        <li>Klik <strong>"Proses"</strong></li>
                                    </ol>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="fw-bold text-warning" style="font-size: 0.9rem;">Untuk Produk A3+ / SATUAN (Cetak Photo, Flyer)</h6>
                                    <ol class="small mb-3">
                                        <li>Klik tombol <strong>"Selesai"</strong></li>
                                        <li>Masukkan:
                                            <ul>
                                                <li><strong>Jumlah Diproduksi (Lembar):</strong> Masukkan jumlah lembar yang dicetak</li>
                                                <li><strong>Jumlah Rusak (Lembar):</strong> Jika ada yang rusak/gagal cetak</li>
                                                <li><strong>Luas Otomatis:</strong> Sistem akan hitung berdasarkan ukuran lembar × qty</li>
                                                <li><strong>Pilih Bahan:</strong> Pilih jenis kertas/media yang dipakai (Art Paper, HVS, dll)</li>
                                                <li><strong>PIN Operator:</strong> Masukkan PIN Anda</li>
                                            </ul>
                                        </li>
                                        <li>Klik <strong>"Proses"</strong></li>
                                    </ol>
                                </div>
                            </div>

                            <div class="alert alert-info border-0 mb-0 small p-2 mt-2">
                                <i class="fas fa-calculator me-1"></i>
                                <strong>Auto-Calculate:</strong> Untuk produk PCS dan A3+/SATUAN, sistem otomatis menghitung luas total berdasarkan dimensi produk × jumlah yang diproduksi. Untuk produk METER, sistem langsung pakai panjang yang Anda input.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 3: TIPS & TRIK / TROUBLESHOOTING -->
        <div class="row g-4 mb-4" style="page-break-before: always;">
            <div class="col-md-6">
                <div class="guide-section border-start border-5 border-success h-100 mb-0">
                    <h4 class="section-title text-success"><i class="fas fa-lightbulb"></i> Tips & Trik</h4>
                    
                    <h6 class="fw-bold text-dark mb-2"><i class="fas fa-search me-2 text-primary"></i>Cara Cari Pekerjaan Cepat</h6>
                    <ul class="small mb-3">
                        <li>Gunakan <strong>Search Box</strong> di atas tabel</li>
                        <li>Ketik No SPK atau nama produk</li>
                    </ul>

                    <h6 class="fw-bold text-dark mb-2"><i class="fas fa-box me-2 text-warning"></i>Cara Pilih Bahan/Material</h6>
                    <ol class="small mb-2">
                        <li>Klik kolom <strong>"Pilih Bahan"</strong></li>
                        <li>Ketik nama bahan (misal: "Albatros")</li>
                        <li>Pilih dari dropdown yang muncul</li>
                        <li>Pastikan pilih roll yang masih ada stoknya</li>
                    </ol>
                    <div class="alert alert-warning border-0 small p-2 mb-3">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <strong>Catatan:</strong> Jika bahan tidak muncul, hubungi Admin Gudang!
                    </div>

                    <h6 class="fw-bold text-dark mb-2"><i class="fas fa-undo me-2 text-danger"></i>Jika Salah Input</h6>
                    <ul class="small mb-0">
                        <li><strong>Jika belum klik "Proses":</strong> Tutup modal dan buka lagi, input ulang dengan benar.</li>
                        <li><strong>Jika sudah klik "Proses":</strong> Hubungi Supervisor atau Admin. Mereka bisa koreksi di sistem. <strong class="text-danger">JANGAN</strong> input ulang sendiri.</li>
                    </ul>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="guide-section border-start border-5 border-danger h-100 mb-0">
                    <h4 class="section-title text-danger"><i class="fas fa-wrench"></i> Troubleshooting</h4>
                    
                    <div class="card border-danger mb-3">
                        <div class="card-header bg-danger text-white fw-bold py-2">
                            <i class="fas fa-key me-2"></i>Problem: PIN Tidak Valid
                        </div>
                        <div class="card-body small p-2">
                            <strong class="text-danger">Penyebab:</strong> PIN salah, belum diset oleh Admin, atau PIN untuk cabang lain.<br>
                            <strong class="text-success">Solusi:</strong> Cek ulang PIN Anda, hubungi Admin untuk verifikasi, atau minta reset PIN jika lupa.
                        </div>
                    </div>

                    <div class="card border-warning mb-3">
                        <div class="card-header bg-warning text-dark fw-bold py-2">
                            <i class="fas fa-box me-2"></i>Problem: Bahan Tidak Muncul
                        </div>
                        <div class="card-body small p-2">
                            <strong class="text-danger">Penyebab:</strong> Bahan belum diinput ke sistem, sudah habis, atau salah ketik nama bahan.<br>
                            <strong class="text-success">Solusi:</strong> Cek ejaan, hubungi Admin Gudang untuk cek stok, atau minta Admin input bahan baru.
                        </div>
                    </div>

                    <div class="card border-info mb-0">
                        <div class="card-header bg-info text-white fw-bold py-2">
                            <i class="fas fa-ban me-2"></i>Problem: Tombol "Mulai" Tidak Bisa Diklik
                        </div>
                        <div class="card-body small p-2">
                            <strong class="text-danger">Penyebab:</strong> Ada step lain yang masih "In Progress", atau pekerjaan sudah "Done".<br>
                            <strong class="text-success">Solusi:</strong> Selesaikan step yang "In Progress" dulu, atau hubungi Supervisor jika masih bingung.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4 no-print">
            <div class="col-12 text-center">
                <button class="btn btn-outline-primary px-5 py-3 fw-900 rounded-pill shadow-sm"
                    onclick="window.print()">
                    <i class="fas fa-print me-2"></i>CETAK PANDUAN PENGOPERASIAN
                </button>
            </div>
        </div>
    </div>
</div>
