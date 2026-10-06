<!-- ================= SOP MANUFACTURING & PRODUCT SETUP ================= -->
<div class="modal fade" id="modalSopProduk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content modal-premium border-0 shadow-lg" style="border-radius: 28px;">
            <div class="modal-header border-0 py-4 px-4"
                style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%)">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary rounded-3 p-2 d-flex align-items-center justify-content-center"
                        style="width: 42px; height: 42px;">
                        <i class="fas fa-microchip text-white fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-900 mb-0">SOP STRATEGI PRODUK & MANUFAKTUR</h5>
                        <small class="text-info opacity-75 fw-bold">Standar Baku Akurasi Harga Pokok Produksi (HPP) &
                            Margin Keuntungan</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- Sidebar Tabs -->
                    <div class="col-md-3 border-end bg-light bg-opacity-50">
                                                <div class="nav flex-column nav-pills p-3 gap-2" id="sop-tabs-produk" role="tablist">
                            <button
                                class="nav-link active text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#sop-pricing" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-tags text-warning" style="width: 20px;"></i> 1. Skala Harga
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#sop-bom" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-flask text-danger" style="width: 20px;"></i> 2. Managemen BOM
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#sop-routing" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-cogs text-success" style="width: 20px;"></i> 3. Alur Kerja
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#sop-profit" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-chart-line text-info" style="width: 20px;"></i> 4. Analisa Profit
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#sop-builder" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-drafting-compass text-pink" style="width: 20px; color: #db2777;"></i> 5. Form Builder
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#sop-identitas" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-fingerprint text-primary" style="width: 20px;"></i> Identitas Produk
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#sop-final" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-shield-alt text-dark" style="width: 20px;"></i> Mutlak & Larangan
                            </button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="col-md-9 px-0 bg-white">
                        <div class="tab-content h-100">

                                                        <!-- ====== TAB 4: PROFIT ====== -->
                            <div class="tab-pane fade p-5" id="sop-profit">
                                <h4 class="fw-900 text-dark mb-4">4. Analisa Profit (HPP)</h4>
                                <div class="p-4 rounded-4 mb-4" style="background: #e0f2fe; border: 2px solid #38bdf8;">
                                    <h6 class="fw-900 text-info mb-2"><i class="fas fa-chart-pie me-2"></i>FUNGSI UTAMA</h6>
                                    <p class="small text-muted mb-0">Tab ini berfungsi untuk mengkalkulasi dan memastikan bahwa struktur biaya (Bahan Baku + Tenaga Kerja) dengan harga jual yang Anda tentukan di Skala Harga sudah profit / menguntungkan.</p>
                                </div>
                                <div class="bg-light p-4 rounded-4">
                                    <h6 class="fw-900 d-flex align-items-center gap-2 mb-3"><i class="fas fa-list-ol text-info"></i> Cara Membaca Analisa Profit:</h6>
                                    <ul class="small text-muted mb-0 ps-3" style="line-height: 1.8;">
                                        <li>Cek <strong>HPP (Harga Pokok Penjualan)</strong>: Akumulasi dari Biaya Bahan Baku + Tenaga Kerja + Overhead 5%.</li>
                                        <li>Cek <strong>Margin Laba</strong>: Pastikan setiap baris dari skala harga memiliki margin laba yang positif (warna hijau). Jika ada yang merah (minus), Anda harus segera mengevaluasi BOM / Routing / Harga Jual-nya!</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- ====== TAB 0: FORM BUILDER ====== -->
                            <div class="tab-pane fade p-5" id="sop-builder">
                                <h4 class="fw-900 text-dark mb-4">5. Konfigurasi Form Builder (Dinamis)</h4>

                                <div class="p-4 rounded-4 mb-4 d-flex align-items-center gap-3"
                                    style="background: #fdf2f8; border: 2px solid #db2777;">
                                    <div class="flex-shrink-0 text-pink" style="font-size: 2rem; color: #db2777;"><i
                                            class="fas fa-cogs"></i></div>
                                    <div>
                                        <h6 class="fw-900 mb-1" style="color: #db2777;">HIERARKI SISTEM: Mana yang
                                            Menang?</h6>
                                        <p class="small mb-0 text-muted">Sistem akan mencari form dengan urutan:
                                            <strong>Produk</strong> > <strong>Sub-Kategori</strong> >
                                            <strong>Kategori</strong>.
                                            Jika Produk memiliki form sendiri, maka form Kategori akan diabaikan.
                                        </p>
                                    </div>
                                </div>

                                <div class="d-flex flex-column gap-4">
                                    <div class="d-flex gap-3 align-items-start">
                                        <div class="bg-pink text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 28px; height: 28px; font-size: 0.8rem; font-weight: 900; background-color: #db2777;">
                                            1
                                        </div>
                                        <div>
                                            <p class="fw-800 mb-1">Tentukan Parameter Variasi</p>
                                            <p class="small text-muted mb-2">Gunakan Builder untuk menentukan apa saja
                                                yang harus diinput Kasir (Sisi, Ukuran, Laminasi, dll). Setiap parameter
                                                berjenis <strong>Check/Select</strong> akan otomatis menjadi:</p>
                                            <ul class="small text-muted ps-3 mb-0">
                                                <li>Pilihan input di Kasir (Frontend)</li>
                                                <li>Kolom penentu harga di <strong>Matriks Harga</strong></li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-3 align-items-start border-top pt-3">
                                        <div class="bg-pink text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 28px; height: 28px; font-size: 0.8rem; font-weight: 900; background-color: #db2777;">
                                            2
                                        </div>
                                        <div>
                                            <p class="fw-800 mb-1">Aksi Pink Icon (Build Dynamic Form)</p>
                                            <p class="small text-muted">Klik Icon <strong>Pink (Builder)</strong> pada
                                                daftar produk. Jika icon berwarna cerah, berarti produk tersebut
                                                memiliki custom form khusus (Override). Gunakan ini jika produk bersifat
                                                sangat unik.</p>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-3 align-items-start border-top pt-3">
                                        <div class="bg-pink text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 28px; height: 28px; font-size: 0.8rem; font-weight: 900; background-color: #db2777;">
                                            3
                                        </div>
                                        <div>
                                            <p class="fw-800 mb-1 text-pink" style="color: #db2777;">Variable ID Otomatis (Auto-Generate)</p>
                                            <p class="small text-muted mb-2">Kini Anda <strong>tidak perlu lagi memikirkan atau mengetik Variable ID secara manual</strong>. Sistem otomatis membuatnya!</p>
                                            <ul class="small text-muted ps-3 mb-2">
                                                <li>Saat Anda mengetik "Label Input" (misal: "Jenis Print" atau "Sisi"), sistem akan langsung mem-parsing teks tersebut menjadi <code>jenisprint_id</code> atau <code>sisi_id</code> secara otomatis.</li>
                                                <li>Fitur auto-generate ini berguna mencegah kesalahan ketik (typo) yang bisa menyebabkan kalkulator harga error saat di kasir.</li>
                                                <li><strong>Sangat Praktis:</strong> Cukup fokus pada pengisian opsi dropdown harga/spesifikasinya saja tanpa pusing memikirkan teknis penamaan ID (Kecuali Anda mengedit via JSON schema lanjutan).</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ====== TAB 1: IDENTITAS ====== -->
                            <div class="tab-pane fade p-5" id="sop-identitas">
                                <h4 class="fw-900 text-dark mb-4">Identitas Produk</h4>

                                <div class="p-4 rounded-4 mb-4 d-flex align-items-center gap-3"
                                    style="background: #eff6ff; border: 2px solid #3b82f6;">
                                    <div class="flex-shrink-0 text-primary" style="font-size: 2rem;"><i
                                            class="fas fa-info-circle"></i></div>
                                    <div>
                                        <h6 class="fw-900 mb-1 text-primary">PRINSIP UTAMA: Kategori Adalah Koentji</h6>
                                        <p class="small mb-0 text-muted">Setiap produk <strong>WAJIB</strong> masuk ke
                                            kategori yang benar. Kategori menentukan bagaimana "Formulir Harga" akan
                                            merespon (Misal: Digital A3 memiliki skema harga 1-sisi / 2-sisi otomatis).
                                        </p>
                                    </div>
                                </div>

                                <div class="d-flex flex-column gap-3">
                                    <div class="d-flex gap-3 align-items-start">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 28px; height: 28px; font-size: 0.8rem; font-weight: 900;">A
                                        </div>
                                        <div>
                                            <p class="fw-800 mb-1">Nama Produk yang Unik</p>
                                            <p class="small text-muted">Gunakan format: [Jenis Produk] + [Spesifikasi
                                                Utama]. Contoh: <strong>"Brosur A4 Art Paper 150gr"</strong>. Hindari
                                                nama ambigu seperti "Brosur Murah".</p>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-3 align-items-start border-top pt-3">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 28px; height: 28px; font-size: 0.8rem; font-weight: 900;">B
                                        </div>
                                        <div>
                                            <p class="fw-800 mb-1">Satuan Jual</p>
                                            <p class="small text-muted">Tentukan bagaimana kasir menjual produk ini
                                                (Pcs, Lembar, Meter, atau Set). Satuan ini akan menjadi pembagi harga
                                                pada matriks harga jual.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-3 align-items-start border-top pt-3">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 28px; height: 28px; font-size: 0.8rem; font-weight: 900;">C
                                        </div>
                                        <div>
                                            <p class="fw-800 mb-1">Klasifikasi Jenis Produk</p>
                                            <p class="small text-muted mb-2">Sangat penting menentukan jenis produk agar alur sistem berjalan akurat:</p>
                                            <ul class="small text-muted ps-3 mb-0">
                                                <li class="mb-1"><strong class="text-dark">Barang Produksi:</strong> Produk yang harus diproduksi (Ada SPK, butuh Resep/BOM & Alur Mesin). Contoh: Spanduk, Brosur, Stempel.</li>
                                                <li class="mb-1"><strong class="text-dark">Barang Jadi:</strong> Produk jadi siap jual langsung dari etalase/gudang tanpa perlu SPK produksi. Contoh: Tinta Botolan, Flashdisk, Pulpen, Frame Jadi.</li>
                                                <li class="mb-1"><strong class="text-dark">Jasa Produksi:</strong> Jasa pengerjaan yang menggunakan mesin produksi (Ada SPK & Mesin) namun material dibawa oleh customer. Contoh: Jasa Print UV, Jasa Potong Laser.</li>
                                                <li class="mb-1"><strong class="text-dark">Jasa Layanan:</strong> Layanan murni yang tidak masuk mesin dan tidak pakai bahan baku gudang. Contoh: Jasa Desain, Jasa Setting, Jasa Pemasangan.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ====== TAB 2: BOM ====== -->
                            <div class="tab-pane fade p-5" id="sop-bom">
                                <h4 class="fw-900 text-dark mb-2">2. Managemen BOM (Bill of Materials)</h4>
                                <p class="text-muted small mb-4">BOM adalah resep rahasia produk. Akurasi HPP ditentukan
                                    di sini.</p>

                                <div class="p-4 rounded-4 mb-4" style="background: #fef2f2; border: 2px solid #ef4444;">
                                    <h6 class="fw-900 text-danger mb-2"><i
                                            class="fas fa-exclamation-triangle me-2"></i>ATURAN EMAS RESEP</h6>
                                    <ul class="small text-muted mb-0 ps-3">
                                        <li><strong>Scrap Factor:</strong> Selalu input margin error (misal 2% untuk
                                            limbah potong).</li>
                                        <li><strong>Auto-Cost:</strong> Sistem akan mengambil harga beli terakhir dari
                                            Gudang secara otomatis.</li>
                                        <li><strong>Wajib Ada Bahan:</strong> Produk fisik tanpa bahan baku adalah
                                            <strong>ILLEGAL</strong> dalam sistem ERP ini.
                                        </li>
                                    </ul>
                                </div>

                                <div class="p-4 rounded-4 mb-4" style="background: #eff6ff; border: 2px solid #3b82f6;">
                                    <h6 class="fw-900 text-primary mb-2"><i
                                            class="fas fa-calculator me-2"></i>TIPE KALKULASI BOM</h6>
                                    <p class="small text-muted mb-2">Pilih cara sistem memotong bahan baku di gudang:</p>
                                    <ul class="small text-muted mb-0 ps-3">
                                        <li class="mb-1"><strong>1. Fixed:</strong> (Sangat Direkomendasikan untuk produk uk. tetap seperti Banner Rollup, Kartu Nama). Operator menghitung LUAS material sekali saja di awal lalu input di *Kebutuhan Per Pcs*. Saat diproduksi, sistem memotong: <code>Kebutuhan BOM x Total Qty Order</code>.</li>
                                        <li class="mb-1"><strong>2. Area (P x L):</strong> (Untuk Spanduk/Custom). Sistem memotong berdasarkan ukuran order: <code>(Panjang Order x Lebar Order) x Kebutuhan BOM x Total Qty Order</code>.</li>
                                        <li class="mb-1"><strong>3. Length (P):</strong> Sistem memotong: <code>Panjang Order x Kebutuhan BOM x Total Qty Order</code>.</li>
                                    </ul>
                                </div>

                                <div class="bg-light p-4 rounded-4">
                                    <h6 class="fw-900 d-flex align-items-center gap-2 mb-3"><i
                                            class="fas fa-list-ol text-danger"></i> Cara Input BOM:</h6>
                                    <ol class="small text-muted mb-0">
                                        <li class="mb-2">Klik Indikator <strong>BOM</strong> (Warna Merah/Orange).</li>
                                        <li class="mb-2">Pilih Bahan dari Database Gudang (Hanya bahan yang sudah
                                            diregistrasi yang muncul).</li>
                                        <li class="mb-2">Input <strong>Quantity per Unit</strong> (Misal: Untuk 1 kartu
                                            nama perlu 0.01 lembar kertas besar).</li>
                                        <li>Simpan. Indikator akan berubah menjadi Hijau jika data sudah lengkap.</li>
                                    </ol>
                                </div>

                                <div class="p-4 rounded-4 mt-4" style="background: #f0fdf4; border: 2px solid #22c55e;">
                                    <h6 class="fw-900 text-success mb-2"><i class="fas fa-copy me-2"></i>FITUR: COPY BOM & ROUTING (KLONING CEPAT)</h6>
                                    <p class="small text-muted mb-2">Jika Anda memiliki puluhan variasi produk (misal perbedaan ukuran atau finishing) dan resep BOM / Routing-nya hampir sama, Anda <strong>tidak perlu menginput ulang dari nol</strong>. Gunakan fitur Kloning ini untuk efisiensi:</p>
                                    <ul class="small text-muted mb-0 ps-3">
                                        <li class="mb-1">Klik tombol <strong><i class="bi bi-files"></i> Copy BOM & Routing</strong>.</li>
                                        <li class="mb-1">Pilih <strong>Variasi Sumber</strong> (variasi yang BOM dan Routing-nya sudah Anda input lengkap).</li>
                                        <li class="mb-1">Centang satu atau lebih <strong>Variasi Tujuan</strong> yang akan ditimpa dengan data sumber tersebut.</li>
                                        <li class="mb-0">Klik <strong>Terapkan Sekarang</strong>. Seluruh Resep & Langkah Kerja akan otomatis tersalin. Anda bisa mengedit kecil-kecilan di tujuan setelah disalin jika diperlukan.</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- ====== TAB 3: ROUTING ====== -->
                            <div class="tab-pane fade p-5" id="sop-routing">
                                <h4 class="fw-900 text-dark mb-2">3. Alur Kerja (Routing)</h4>
                                <p class="text-muted small mb-4">Mendefinisikan langkah kerja, mesin yang digunakan, dan
                                    biaya tenaga kerja.</p>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded-4">
                                            <h6 class="fw-900 small mb-2 text-success">Langkah 1: Setup Urutan</h6>
                                            <p class="text-muted" style="font-size: 0.75rem;">Input urutan kerja (10,
                                                20, 30...). Tujuannya agar jika ada langkah tambahan di tengah, urutan
                                                tidak perlu di-reset semua.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded-4">
                                            <h6 class="fw-900 small mb-2 text-success">Langkah 2: Labor Cost</h6>
                                            <p class="text-muted" style="font-size: 0.75rem;">Input perkiraan biaya
                                                tukang/operator per unit. Ini akan ditambahkan ke HPP sebagai biaya
                                                produksi.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 rounded-4 mt-4 d-flex align-items-start gap-4"
                                    style="background: #f0fdf4; border: 1px solid #22c55e;">
                                    <i class="fas fa-clock text-success fs-3"></i>
                                    <div>
                                        <h6 class="fw-900 text-success mb-1">Standard Time</h6>
                                        <p class="small text-muted mb-0">Input estimasi waktu per unit. Ini sangat
                                            krusial bagi Departemen Produksi untuk melihat <strong>Load Mesin</strong>
                                            dan target penyelesaian SPK.</p>
                                    </div>
                                </div>

                                <div class="p-4 rounded-4 mt-4" style="background: #f0fdf4; border: 2px solid #22c55e;">
                                    <h6 class="fw-900 text-success mb-2"><i class="fas fa-copy me-2"></i>FITUR: COPY BOM & ROUTING (KLONING CEPAT)</h6>
                                    <p class="small text-muted mb-2">Sama halnya dengan Manajemen BOM, Anda juga bisa menyalin Alur Kerja (Routing) dari satu variasi ke variasi lain secara instan:</p>
                                    <ul class="small text-muted mb-0 ps-3">
                                        <li class="mb-1">Klik tombol <strong><i class="bi bi-files"></i> Copy BOM & Routing</strong>.</li>
                                        <li class="mb-1">Pilih <strong>Variasi Sumber</strong> yang langkah kerjanya sudah lengkap.</li>
                                        <li class="mb-1">Centang <strong>Variasi Tujuan</strong>.</li>
                                        <li class="mb-0">Klik <strong>Terapkan Sekarang</strong>. Perhatikan bahwa fitur ini akan menyalin KEDUA data sekaligus (Resep Material dan Langkah Kerja).</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- ====== TAB 4: PRICING ====== -->
                            <div class="tab-pane fade show active p-5" id="sop-pricing">
                                <h4 class="fw-900 text-dark mb-2">1. Skala Harga (Matriks Harga)</h4>
                                <p class="text-muted small mb-4">Menentukan harga jual bertingkat (Grosir) berdasarkan
                                    kuantitas.</p>

                                <div class="p-4 rounded-4 mb-4" style="background: #fffbeb; border: 2px solid #f59e0b;">
                                    <h6 class="fw-900 text-warning d-flex align-items-center gap-2 mb-2">
                                        <i class="fas fa-magic"></i> FITUR MAGIC: KLONING 2 SISI
                                    </h6>
                                    <p class="small text-muted mb-0">Untuk kategori tertentu (seperti Digital Print),
                                        Anda cukup input harga 1 Sisi, lalu gunakan tombol <strong>"Copy ke 2
                                            Sisi"</strong> dengan markup persentase (Misal: 80% dari harga 1 sisi).
                                        Sistem akan membuatkan baris harga baru secara instan!</p>
                                </div>

                                <table class="table table-sm small table-bordered mb-0 text-center">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Range Qty</th>
                                            <th>Tujuan</th>
                                            <th>Logika Harga</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1 - 10</td>
                                            <td>Retail</td>
                                            <td class="text-danger fw-bold">Margin Tinggi</td>
                                        </tr>
                                        <tr>
                                            <td>11 - 100</td>
                                            <td>Semi Grosir</td>
                                            <td class="text-primary fw-bold">Margin Sedang</td>
                                        </tr>
                                        <tr>
                                            <td>101 > </td>
                                            <td>Reseller</td>
                                            <td class="text-success fw-bold">Margin Tipis</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="bg-light p-4 rounded-4 mt-4">
                                    <h6 class="fw-900 d-flex align-items-center gap-2 mb-3"><i class="fas fa-list-ol text-primary"></i> Cara Membuat Skala Harga:</h6>
                                    <ul class="small text-muted mb-0 ps-3" style="line-height: 1.8;">
                                        <li><strong>Tambah Range:</strong> Klik tombol <strong>+ Tambah Range</strong> di pojok kanan atas tabel untuk membuat baris variasi/kuantitas baru.</li>
                                        <li><strong>Simpan (Save):</strong> Klik <strong>Icon Disket Hijau</strong> di baris terkait untuk menyimpan satu per satu, ATAU klik <strong>Simpan Perubahan Matriks</strong> di bawah untuk menyimpan massal.</li>
                                        <li><strong>Duplikat:</strong> Klik <strong>Icon Copy (Kertas) Biru</strong> untuk menyalin rentang jumlah & harga dari baris tersebut ke baris baru. Berguna jika harga beda tipis atau untuk rentang kuantitas berikutnya.</li>
                                        <li><strong>Hapus:</strong> Klik <strong>Icon Tempat Sampah Merah</strong> untuk menghapus baris skala harga yang salah atau tidak digunakan.</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- ====== TAB 5: DO & DON'T ====== -->
                            <div class="tab-pane fade p-5" id="sop-final">
                                <h4 class="fw-900 text-dark mb-4">⚡ Larangan & Standar Mutlak</h4>

                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 h-100" style="background-color: #fef2f2; border: 1px solid #fecaca;">
                                            <h6 class="fw-900 text-danger mb-3 font-monospace">❌ DILARANG KERAS (DON'T)
                                            </h6>
                                            <ul class="list-unstyled d-flex flex-column gap-3 mb-0 small text-dark">
                                                <li><i class="fas fa-times me-2 text-danger"></i> <strong>Jangan menjual
                                                        rugi:</strong> Hindari input harga jual di bawah angka HPP
                                                    (Tulisan HPP di matriks harga adalah panduan Anda).</li>
                                                <li><i class="fas fa-times me-2 text-danger"></i> <strong>Jangan duplikat
                                                        produk:</strong> Gunakan fitur "Copy Produk" jika ingin membuat
                                                    varian serupa.</li>
                                                <li><i class="fas fa-times me-2 text-danger"></i> <strong>Jangan hapus produk yang
                                                        ada stok:</strong> Ini merusak database transaksi masa lalu.
                                                    Gunakan fitur "Draft/Non-Aktif".</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 h-100" style="background-color: #f0fdf4; border: 1px solid #bbf7d0;">
                                            <h6 class="fw-900 text-success mb-3 font-monospace">✅ WAJIB DILAKUKAN (DO)
                                            </h6>
                                            <ul class="list-unstyled d-flex flex-column gap-3 mb-0 small text-dark">
                                                <li><i class="fas fa-check me-2 text-success"></i> <strong>Update Berkala:</strong>
                                                    Update BOM jika ada kenaikan harga bahan baku signifikan dari
                                                    supplier.</li>
                                                <li><i class="fas fa-check me-2 text-success"></i> <strong>Analisis Margin:</strong>
                                                    Gunakan modal "Profit Analysis" (Icon Grafik) untuk memastikan tiap
                                                    produk menghasilkan untung.</li>
                                                <li><i class="fas fa-check me-2 text-success"></i> <strong>Cek Indikator:</strong>
                                                    Pastikan semua icon (BOM, Routing, Price) berwarna
                                                    <strong>Hijau</strong> sebelum produk dipasarkan ke Kasir.
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 d-flex justify-content-between align-items-center"
                style="background: #f8fafc;">
                <div class="small text-muted"><i class="fas fa-info-circle me-1 text-primary"></i> Data Setup yang
                    akurat adalah kunci profitabilitas perusahaan.</div>
                <button type="button" class="btn btn-dark px-5 py-2 fw-900 rounded-pill shadow-sm"
                    data-bs-dismiss="modal">
                    SAYA MENGERTI, SETUP PRODUK SEKARANG
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    #sop-tabs-produk .nav-link {
        color: #64748b;
        background: white;
        text-align: left !important;
        border-radius: 12px;
        transition: all 0.2s ease;
        border: 1px solid #f1f5f9;
        margin-bottom: 5px;
    }

    #sop-tabs-produk .nav-link:hover {
        background: #f8fafc;
        transform: translateX(5px);
    }

    #sop-tabs-produk .nav-link.active {
        background: #0f172a;
        color: white;
        border-color: #0f172a;
    }

    .fw-900 {
        font-weight: 900;
    }

    .fw-800 {
        font-weight: 800;
    }

    .fw-700 {
        font-weight: 700;
    }
</style>