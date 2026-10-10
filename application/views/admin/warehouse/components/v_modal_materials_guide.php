<!-- ================= SOP MASTER BAHAN & MATERIAL (LANGKAH 2 dari 6) ================= -->
<div class="modal fade" id="modalMaterialsGuide" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content modal-premium border-0 shadow-lg" style="border-radius: 28px;">
            <div class="modal-header border-0 py-4 px-4"
                style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%)">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary rounded-3 p-2 d-flex align-items-center justify-content-center"
                        style="width: 42px; height: 42px;">
                        <i class="fas fa-cubes text-white fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-900 mb-0">SOP LANGKAH 2: VARIAN, KONVERSI & BARCODE</h5>
                        <small class="text-info opacity-75 fw-bold">Menentukan bentuk fisik barang di gudang — WAJIB
                            dilakukan setelah Langkah 1</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- Sidebar Tabs -->
                    <div class="col-md-3 border-end bg-light bg-opacity-50">
                        <div class="nav flex-column nav-pills p-3 gap-2" id="sop-tabs-material" role="tablist">
                            <button
                                class="nav-link active text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#mat-workflow" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-project-diagram text-primary" style="width: 20px;"></i> Posisi Langkah
                                2
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#mat-varian" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-cubes text-success" style="width: 20px;"></i> Cara Kelola Varian
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#mat-konversi" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-exchange-alt text-danger" style="width: 20px;"></i> Rumus Konversi
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#mat-barcode" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-barcode text-warning" style="width: 20px;"></i> Barcode & Scan
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#mat-tips" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-lightbulb text-warning" style="width: 20px;"></i> Tips & Best Practice
                            </button>
                        </div>
                    </div>
                    <!-- Content -->
                    <div class="col-md-9 px-0 bg-white">
                        <div class="tab-content h-100">

                            <!-- ====== TAB 1: POSISI LANGKAH 2 ====== -->
                            <div class="tab-pane fade show active p-5" id="mat-workflow">
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <span class="badge bg-primary px-3 py-2 rounded-pill fw-800"
                                        style="font-size: 0.8rem;">LANGKAH 2 dari 6</span>
                                    <h4 class="fw-900 text-dark mb-0">Posisi Anda dalam Alur Kerja Gudang</h4>
                                </div>

                                <!-- Critical Alert -->
                                <div class="p-4 rounded-4 mb-4 d-flex align-items-center gap-3"
                                    style="background: #eff6ff; border: 2px solid #3b82f6;">
                                    <div class="flex-shrink-0" style="color: #3b82f6; font-size: 2rem;"><i
                                            class="fas fa-info-circle"></i></div>
                                    <div>
                                        <h6 class="fw-900 mb-1" style="color: #1e40af;">📦 HALAMAN INI UNTUK MENGATUR
                                            BENTUK FISIK BARANG DI GUDANG</h6>
                                        <p class="small mb-0 text-muted">Setelah Langkah 1 mendaftarkan nama induk,
                                            <strong>Langkah 2 ini menentukan bagaimana barang tersebut disimpan dan
                                                diperhitungkan stoknya di gudang</strong>. Anda mendaftarkan
                                            <u>varian/kemasan</u> (contoh: Roll, Rim, Box) beserta <u>isi
                                                konversinya</u> (berapa lembar dalam 1 Roll).</p>
                                    </div>
                                </div>

                                <!-- 6-Step Overview -->
                                <div class="d-flex flex-column gap-2">
                                    <div class="p-3 rounded-3 d-flex align-items-center gap-3 bg-light border">
                                        <div class="bg-secondary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 36px; height: 36px;">1</div>
                                        <div>
                                            <div class="fw-800 small">Registrasi Induk & Satuan Dasar</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">Daftarkan nama bahan dan
                                                satuan terkecilnya — sudah selesai di halaman sebelumnya</div>
                                        </div>
                                        <span class="badge bg-success ms-auto flex-shrink-0 px-3">SELESAI</span>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-center gap-3"
                                        style="background: #1e293b; border: 2px solid #6366f1;">
                                        <div class="bg-primary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 36px; height: 36px; font-size: 1rem;">2</div>
                                        <div class="text-white">
                                            <div class="fw-900 small">LANGKAH INI — Varian, Konversi & Barcode</div>
                                            <div style="font-size: 0.7rem; opacity: 0.7;">Daftarkan bentuk fisik kemasan
                                                (Roll/Box) dan isi konversinya. Cetak barcode label.</div>
                                        </div>
                                        <span class="badge bg-primary ms-auto flex-shrink-0 px-3">AKTIF</span>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-center gap-3 bg-light border">
                                        <div class="bg-secondary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 36px; height: 36px;">3</div>
                                        <div>
                                            <div class="fw-800 small">Stok Opname (Saldo Awal)</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">Input hitungan fisik
                                                untuk sinkronisasi awal stok sistem</div>
                                        </div>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-center gap-3 bg-light border">
                                        <div class="bg-secondary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 36px; height: 36px;">4</div>
                                        <div>
                                            <div class="fw-800 small">Pembelian (Stok Masuk)</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">Catat setiap pembelian
                                                bahan dari supplier</div>
                                        </div>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-center gap-3 bg-light border">
                                        <div class="bg-secondary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 36px; height: 36px;">5</div>
                                        <div>
                                            <div class="fw-800 small">Dispatch (Kirim ke Produksi)</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">Keluarkan bahan dari
                                                gudang menuju area produksi/cabang</div>
                                        </div>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-center gap-3 bg-light border">
                                        <div class="bg-secondary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 36px; height: 36px;">6</div>
                                        <div>
                                            <div class="fw-800 small">Monitoring Stok Harian</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">Pantau live stok, sisa
                                                bahan, dan status gudang</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ====== TAB 2: CARA KELOLA VARIAN ====== -->
                            <div class="tab-pane fade p-5" id="mat-varian">
                                <h4 class="fw-900 text-dark mb-2">📦 Cara Mengelola Varian Material</h4>
                                <p class="text-muted small mb-4">Varian adalah <strong>bentuk fisik kemasan barang di
                                        gudang</strong>. Satu induk bahan bisa memiliki banyak varian dengan satuan
                                    kemasan yang berbeda.</p>

                                <!-- Step to access -->
                                <div class="p-3 rounded-4 mb-4 d-flex align-items-center gap-3"
                                    style="background: #f0fdf4; border: 2px solid #22c55e;">
                                    <i class="fas fa-mouse-pointer text-success fs-4 flex-shrink-0"></i>
                                    <div>
                                        <h6 class="fw-900 text-success mb-1">Cara Mengakses</h6>
                                        <p class="small text-muted mb-0">Klik tombol <span
                                                class="badge bg-primary px-2">ATUR VARIAN & KONVERSI</span> pada baris
                                            bahan di tabel. Modal kelola varian akan terbuka, menampilkan daftar varian
                                            yang sudah ada (kiri) dan form tambah varian baru (kanan).</p>
                                    </div>
                                </div>

                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 h-100"
                                            style="background: #eff6ff; border: 2px solid #3b82f6;">
                                            <h6 class="fw-900 text-primary mb-3"><i
                                                    class="fas fa-plus-circle me-2"></i>Menambah Varian Baru</h6>
                                            <ul class="list-unstyled d-flex flex-column gap-2 mb-0 small">
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-dot-circle text-primary mt-1 flex-shrink-0"></i>
                                                    <div><strong>Nama Varian</strong>: Isi dengan ukuran atau kemasan
                                                        fisik (contoh: A3+, Roll 65cm, Box, Rim 500lb)</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-dot-circle text-primary mt-1 flex-shrink-0"></i>
                                                    <div><strong>Satuan Stok/Gudang</strong>: Pilih satuan kemasan ini
                                                        (Roll, Rim, Box, Pcs, dll)</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-dot-circle text-primary mt-1 flex-shrink-0"></i>
                                                    <div><strong>Isi (Konversi)</strong>: Jumlah satuan terkecil dalam 1
                                                        unit varian ini. <em class="text-danger">Bagian paling
                                                            kritis!</em></div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-dot-circle text-primary mt-1 flex-shrink-0"></i>
                                                    <div><strong>Barcode</strong>: Opsional. Bisa diisi manual, scan,
                                                        atau auto-generate.</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-dot-circle text-primary mt-1 flex-shrink-0"></i>
                                                    <div><strong>Status Aktif</strong>: Nonaktifkan jika varian tidak
                                                        lagi digunakan, agar tidak muncul di menu operasional.</div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 h-100"
                                            style="background: #fef9c3; border: 2px solid #eab308;">
                                            <h6 class="fw-900 text-warning mb-3" style="color: #92400e !important;"><i
                                                    class="fas fa-lightbulb me-2"></i>Kapan Harus Buat Varian?</h6>
                                            <p class="small text-muted mb-3">Buat varian baru jika barang memiliki
                                                <strong>ukuran, kemasan, atau spesifikasi fisik berbeda</strong> yang
                                                stoknya dihitung secara terpisah di gudang.</p>
                                            <div class="d-flex flex-column gap-2 small">
                                                <div class="d-flex gap-2 align-items-start">
                                                    <span class="badge bg-success flex-shrink-0">✓</span>
                                                    <div>Tinta Epson 003 → varian: <strong>Black, Cyan, Magenta,
                                                            Yellow</strong> (stok tiap warna berbeda)</div>
                                                </div>
                                                <div class="d-flex gap-2 align-items-start">
                                                    <span class="badge bg-success flex-shrink-0">✓</span>
                                                    <div>Art Paper 150gr → varian: <strong>A3+, 64x90cm,
                                                            65x100cm</strong> (ukuran berbeda, stok berbeda)</div>
                                                </div>
                                                <div class="d-flex gap-2 align-items-start">
                                                    <span class="badge bg-success flex-shrink-0">✓</span>
                                                    <div>Kertas HVS 80gr → varian: <strong>Rim 500 lbr, Dus (5
                                                            Rim)</strong></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ====== TAB 3: RUMUS KONVERSI ====== -->
                            <div class="tab-pane fade p-5" id="mat-konversi">
                                <h4 class="fw-900 text-dark mb-2">🔢 Rumus & Logika Konversi Stok</h4>
                                <p class="text-muted small mb-4">Kolom <strong>Konversi</strong> adalah jantung dari
                                    seluruh sistem perhitungan stok otomatis. Isi dengan benar!</p>

                                <div class="p-4 rounded-4 mb-4" style="background: #1e293b; color: white;">
                                    <h6 class="fw-900 text-warning mb-2">📐 Formula Konversi:</h6>
                                    <div class="text-center py-3">
                                        <div class="d-inline-block px-5 py-3 rounded-3"
                                            style="background: rgba(255,255,255,0.1); font-size: 1.1rem;">
                                            <span class="fw-900 text-info">1 Satuan Varian (Kemasan)</span>
                                            <span class="mx-3 text-white opacity-50">=</span>
                                            <span class="fw-900 text-warning">X</span>
                                            <span class="mx-2 text-white opacity-50">×</span>
                                            <span class="fw-900 text-success">Satuan Produksi (Terkecil)</span>
                                        </div>
                                    </div>
                                    <p class="small text-white opacity-75 mb-0 text-center mt-2">Angka X inilah yang
                                        diisi di kolom "Konversi"</p>
                                </div>

                                <h6 class="fw-900 text-dark mb-3">📋 Contoh Konversi Nyata</h6>
                                <div class="table-responsive rounded-4 border mb-4">
                                    <table class="table table-sm mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="p-3 small fw-800">Nama Induk</th>
                                                <th class="p-3 small fw-800 text-muted">Satuan Produksi (Langkah 1)</th>
                                                <th class="p-3 small fw-800 text-primary">Nama Varian (Langkah 2)</th>
                                                <th class="p-3 small fw-800 text-primary">Satuan Varian</th>
                                                <th class="p-3 small fw-800 text-danger">Konversi (Isi di sini)</th>
                                                <th class="p-3 small fw-800 text-success">Artinya</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="p-3 small fw-700">Art Paper 150gr</td>
                                                <td class="p-3 small text-muted">Lembar (lbr)</td>
                                                <td class="p-3 small fw-700 text-primary">Rim</td>
                                                <td class="p-3 small">Rim</td>
                                                <td class="p-3 small fw-900 text-danger">500</td>
                                                <td class="p-3 small text-success">1 Rim = 500 Lembar</td>
                                            </tr>
                                            <tr class="bg-light bg-opacity-50">
                                                <td class="p-3 small fw-700">Sticker Vinyl Ritrama</td>
                                                <td class="p-3 small text-muted">cm</td>
                                                <td class="p-3 small fw-700 text-primary">Roll 65cm</td>
                                                <td class="p-3 small">Roll</td>
                                                <td class="p-3 small fw-900 text-danger">5000</td>
                                                <td class="p-3 small text-success">1 Roll = 5000 cm (50 meter)</td>
                                            </tr>
                                            <tr>
                                                <td class="p-3 small fw-700">Tinta Epson 003</td>
                                                <td class="p-3 small text-muted">ml</td>
                                                <td class="p-3 small fw-700 text-primary">Botol</td>
                                                <td class="p-3 small">Botol</td>
                                                <td class="p-3 small fw-900 text-danger">65</td>
                                                <td class="p-3 small text-success">1 Botol = 65 ml</td>
                                            </tr>
                                            <tr class="bg-light bg-opacity-50">
                                                <td class="p-3 small fw-700">Baut Mur 8mm</td>
                                                <td class="p-3 small text-muted">Pcs</td>
                                                <td class="p-3 small fw-700 text-primary">Pack</td>
                                                <td class="p-3 small">Pack</td>
                                                <td class="p-3 small fw-900 text-danger">100</td>
                                                <td class="p-3 small text-success">1 Pack = 100 Pcs</td>
                                            </tr>
                                            <tr>
                                                <td class="p-3 small fw-700">Jasa Desain</td>
                                                <td class="p-3 small text-muted">Unit</td>
                                                <td class="p-3 small fw-700 text-primary">Unit</td>
                                                <td class="p-3 small">Unit</td>
                                                <td class="p-3 small fw-900 text-danger">1</td>
                                                <td class="p-3 small text-success">Tidak ada konversi (1:1)</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="p-4 rounded-4" style="background: #fef2f2; border: 2px solid #ef4444;">
                                    <h6 class="fw-900 text-danger mb-2"><i
                                            class="fas fa-exclamation-triangle me-2"></i>KONVERSI SALAH = STOK KACAU!
                                    </h6>
                                    <p class="small text-muted mb-0">Jika Anda mengisi konversi <strong>1</strong> untuk
                                        barang yang seharusnya <strong>500</strong>, sistem akan menghitung stok 500x
                                        lebih sedikit dari seharusnya. Akibatnya: stok akan minus saat produksi
                                        berjalan. <strong>Pastikan angka konversi 100% benar sebelum menyimpan.</strong>
                                    </p>
                                </div>
                            </div>

                            <!-- ====== TAB 4: BARCODE & SCAN ====== -->
                            <div class="tab-pane fade p-5" id="mat-barcode">
                                <h4 class="fw-900 text-dark mb-2">🔍 Barcode, Scanning & Label</h4>
                                <p class="text-muted small mb-4">Barcode menghubungkan barang fisik dengan sistem
                                    digital. Gunakan fitur ini untuk mempercepat proses penerimaan dan pengeluaran
                                    barang di gudang.</p>

                                <!-- Scan Mode -->
                                <div class="p-4 rounded-4 mb-4 d-flex align-items-start gap-3"
                                    style="background: #fffbeb; border: 2px solid #f59e0b;">
                                    <i class="fas fa-bolt text-warning fs-4 flex-shrink-0 mt-1"></i>
                                    <div>
                                        <h6 class="fw-900 mb-1" style="color: #92400e;">🚀 Fitur Continuous Scanning
                                            (Scan Massal Tanpa Mouse)</h6>
                                        <p class="small text-muted mb-2">Sistem dirancang untuk input barcode massal
                                            tanpa perlu menyentuh mouse. Cocok untuk gudang dengan banyak varian baru.
                                        </p>
                                        <ol class="small text-muted mb-0 ps-3">
                                            <li>Klik kolom <strong>Barcode</strong> pada baris pertama di tabel varian.
                                            </li>
                                            <li>Arahkan scanner ke barcode fisik barang → scanner otomatis mengisi kode.
                                            </li>
                                            <li>Tekan <kbd>ENTER</kbd> → sistem <strong>menyimpan otomatis</strong> dan
                                                kursor berpindah ke baris berikutnya.</li>
                                            <li>Ulangi sampai semua varian memiliki barcode.</li>
                                        </ol>
                                    </div>
                                </div>

                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 h-100"
                                            style="background: #f0fdf4; border: 2px solid #22c55e;">
                                            <h6 class="fw-900 text-success mb-3"><i
                                                    class="fas fa-magic me-2"></i>Auto-Generate Barcode</h6>
                                            <p class="small text-muted mb-2">Jika barang tidak memiliki barcode asli
                                                dari supplier, gunakan fitur <strong>Generate otomatis</strong>:</p>
                                            <ol class="small text-muted mb-0 ps-3">
                                                <li>Buka modal barcode (klik ikon gerigi/cog di baris varian).</li>
                                                <li>Klik tombol <kbd><i class="fas fa-magic"></i></kbd> untuk membuat
                                                    kode unik acak.</li>
                                                <li>Klik <strong>Simpan Barcode</strong>.</li>
                                                <li>Cetak label dan temple di barang fisik.</li>
                                            </ol>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 h-100"
                                            style="background: #eff6ff; border: 2px solid #3b82f6;">
                                            <h6 class="fw-900 text-primary mb-3"><i class="fas fa-print me-2"></i>Cetak
                                                Label Barcode</h6>
                                            <p class="small text-muted mb-2">Dua cara cetak label:</p>
                                            <div class="d-flex flex-column gap-2 small">
                                                <div class="d-flex gap-2 align-items-start">
                                                    <span class="badge bg-primary flex-shrink-0">1</span>
                                                    <div><strong>Single:</strong> Buka modal barcode satu varian → klik
                                                        tombol Print. Bisa juga Download PNG untuk cetak dari luar
                                                        sistem.</div>
                                                </div>
                                                <div class="d-flex gap-2 align-items-start">
                                                    <span class="badge bg-success flex-shrink-0">2</span>
                                                    <div><strong>Bulk (Massal):</strong> Centang checkbox di sisi kiri
                                                        tabel varian → klik <strong>"Cetak Terpilih"</strong> untuk
                                                        mencetak banyak label sekaligus.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-3 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <p class="small mb-0 fw-700 text-muted"><i
                                            class="fas fa-exclamation-triangle text-warning me-2"></i><strong>Tips
                                            Printer:</strong> Gunakan printer label thermal untuk hasil terbaik. Untuk
                                        printer biasa, gunakan kertas stiker A4 ukuran label 52×30mm atau sesuaikan
                                        margin cetak.</p>
                                </div>
                            </div>

                            <!-- ====== TAB 5: TIPS ====== -->
                            <div class="tab-pane fade p-5" id="mat-tips">
                                <h4 class="fw-900 text-dark mb-2">💡 Tips & Best Practice Langkah 2</h4>
                                <p class="text-muted small mb-4">Ikuti panduan ini agar data varian Anda akurat,
                                    konsisten, dan tidak menyebabkan error stok di kemudian hari.</p>

                                <div class="d-flex flex-column gap-3">
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-start"
                                        style="background: #fef2f2; border: 1px solid #fca5a5;">
                                        <div class="flex-shrink-0 bg-danger rounded-circle text-white d-flex align-items-center justify-content-center fw-900"
                                            style="width: 36px; height: 36px;">!</div>
                                        <div>
                                            <div class="fw-800 text-dark small mb-1">Jangan Hapus Varian yang Sudah
                                                Punya Stok</div>
                                            <p class="text-muted small mb-0">Jika varian sudah pernah digunakan dalam
                                                transaksi (pembelian/dispatch), jangan dihapus karena akan merusak
                                                riwayat stok. Cukup ubah statusnya menjadi <strong>Nonaktif</strong>
                                                agar tidak muncul di menu operasional.</p>
                                        </div>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-start"
                                        style="background: #fffbeb; border: 1px solid #fcd34d;">
                                        <div class="flex-shrink-0 bg-warning rounded-circle text-dark d-flex align-items-center justify-content-center fw-900"
                                            style="width: 36px; height: 36px;">1</div>
                                        <div>
                                            <div class="fw-800 text-dark small mb-1">Verifikasi Ulang Konversi Sebelum
                                                Simpan</div>
                                            <p class="text-muted small mb-0">Lihat bagian bawah form tambah varian — ada
                                                keterangan otomatis: <em>"1 [Satuan Varian] = [Konversi] [Satuan
                                                    Produksi]"</em>. Pastikan kalimat ini masuk akal sebelum klik
                                                Tambahkan.</p>
                                        </div>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-start"
                                        style="background: #eff6ff; border: 1px solid #93c5fd;">
                                        <div class="flex-shrink-0 bg-primary rounded-circle text-white d-flex align-items-center justify-content-center fw-900"
                                            style="width: 36px; height: 36px;">2</div>
                                        <div>
                                            <div class="fw-800 text-dark small mb-1">Nama Varian Harus Informatif &
                                                Singkat</div>
                                            <p class="text-muted small mb-0">Nama varian sebaiknya langsung
                                                menggambarkan spesifikasi fisiknya. Contoh yang baik: <strong>"Roll
                                                    65cm"</strong>, <strong>"A3+ 32x48"</strong>, <strong>"Box
                                                    500gr"</strong>. Hindari nama samar seperti "Varian 1" atau
                                                "Default".</p>
                                        </div>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-start"
                                        style="background: #f0fdf4; border: 1px solid #86efac;">
                                        <div class="flex-shrink-0 bg-success rounded-circle text-white d-flex align-items-center justify-content-center fw-900"
                                            style="width: 36px; height: 36px;">3</div>
                                        <div>
                                            <div class="fw-800 text-dark small mb-1">Satu Induk = Semua Ukurannya
                                                sebagai Varian</div>
                                            <p class="text-muted small mb-0">Daftarkan seluruh ukuran/kemasan yang
                                                mungkin dimiliki oleh satu bahan dalam satu sesi. Ini memudahkan tim
                                                purchasing saat membeli dan tim produksi saat mengambil bahan dari
                                                gudang sesuai ukuran yang dibutuhkan SPK.</p>
                                        </div>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-start"
                                        style="background: #fdf4ff; border: 1px solid #d8b4fe;">
                                        <div class="flex-shrink-0 rounded-circle text-white d-flex align-items-center justify-content-center fw-900"
                                            style="width: 36px; height: 36px; background: #8b5cf6;">4</div>
                                        <div>
                                            <div class="fw-800 text-dark small mb-1">Setelah Langkah 2 Selesai → Lakukan
                                                Opname Awal</div>
                                            <p class="text-muted small mb-0">Setelah semua varian terdaftar dan barcode
                                                ditempel, lanjutkan ke <strong>Langkah 3: Stok Opname</strong> untuk
                                                mengisi saldo awal stok fisik. Tanpa opname, stok sistem akan selalu nol
                                                meskipun gudang penuh barang.</p>
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
                <div class="small text-muted"><i class="fas fa-info-circle me-1 text-primary"></i> Pertanyaan? Hubungi
                    admin atau buka tiket support.</div>
                <button type="button" class="btn btn-dark px-5 py-2 fw-900 rounded-pill shadow-sm"
                    data-bs-dismiss="modal" style="background: #1e293b; border: none;">
                    <i class="fas fa-check me-2"></i>SAYA MENGERTI, LANJUTKAN
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    #sop-tabs-material .nav-link {
        color: #64748b;
        background: white;
        text-align: left !important;
        border-radius: 12px;
        transition: all 0.2s ease;
        border: 1px solid #f1f5f9;
        margin-bottom: 5px;
    }

    #sop-tabs-material .nav-link:hover {
        background: #f8fafc;
        color: #1a1c23;
        transform: translateX(5px);
    }

    #sop-tabs-material .nav-link.active {
        background: #1e293b;
        color: white;
        border-color: #1e293b;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
</style>