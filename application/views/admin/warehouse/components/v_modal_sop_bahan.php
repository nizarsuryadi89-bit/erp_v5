<!-- ================= SOP MASTER BAHAN & JASA (LANGKAH 1 dari 6) ================= -->
<div class="modal fade" id="modalSopBahan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content modal-premium border-0 shadow-lg" style="border-radius: 28px;">
            <div class="modal-header border-0 py-4 px-4"
                style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%)">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-warning rounded-3 p-2 d-flex align-items-center justify-content-center"
                        style="width: 42px; height: 42px;">
                        <i class="fas fa-book-open text-dark fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-900 mb-0">SOP LANGKAH 1: REGISTRASI MASTER BAHAN & JASA
                        </h5>
                        <small class="text-warning opacity-75 fw-bold">Fondasi utama sistem stok — WAJIB dipahami oleh
                            operator gudang</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- Sidebar Tabs -->
                    <div class="col-md-3 border-end bg-light bg-opacity-50">
                        <div class="nav flex-column nav-pills p-3 gap-2" id="sop-tabs-bahan" role="tablist">
                            <button
                                class="nav-link active text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#sop-workflow" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-project-diagram text-primary" style="width: 20px;"></i> Alur 6 Langkah
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#sop-unit" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-ruler-combined text-danger" style="width: 20px;"></i> Aturan Satuan
                                Dasar
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#sop-category" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-tags text-success" style="width: 20px;"></i> 3 Kategori Utama
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#sop-do-dont" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-exclamation-circle text-warning" style="width: 20px;"></i> Do & Don't
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#sop-tips" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-lightbulb text-warning" style="width: 20px;"></i> Tips & Efisiensi
                            </button>
                        </div>
                    </div>
                    <!-- Content -->
                    <div class="col-md-9 px-0 bg-white">
                        <div class="tab-content h-100">

                            <!-- ====== TAB 1: ALUR 6 LANGKAH ====== -->
                            <div class="tab-pane fade show active p-5" id="sop-workflow">
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <span class="badge bg-primary px-3 py-2 rounded-pill fw-800"
                                        style="font-size: 0.8rem;">LANGKAH 1 dari 6</span>
                                    <h4 class="fw-900 text-dark mb-0">Posisi Anda dalam Alur Kerja Gudang</h4>
                                </div>

                                <!-- Critical Alert -->
                                <div class="p-4 rounded-4 mb-4 d-flex align-items-center gap-3"
                                    style="background: #fff7ed; border: 2px solid #f97316;">
                                    <div class="flex-shrink-0 text-orange" style="color: #f97316; font-size: 2rem;"><i
                                            class="fas fa-exclamation-triangle"></i></div>
                                    <div>
                                        <h6 class="fw-900 mb-1" style="color: #9a3412;">⚡ HALAMAN INI HANYA UNTUK BAHAN
                                            PRODUKSI!</h6>
                                        <p class="small mb-0 text-muted">Halaman ini adalah <strong>database nama dasar
                                                barang</strong> yang digunakan dalam proses produksi. Anda mendaftarkan
                                            <u>nama umum</u> bahan dan <u>unit terkecilnya</u> saja. Stok fisik,
                                            konversi kemasan, harga, dan barcode akan diatur di Langkah 2.</p>
                                    </div>
                                </div>

                                <!-- 6-Step Overview -->
                                <div class="d-flex flex-column gap-2">
                                    <div class="p-3 rounded-3 d-flex align-items-center gap-3"
                                        style="background: #1e293b; border: 2px solid #6366f1;">
                                        <div class="bg-primary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 36px; height: 36px; font-size: 1rem;">1</div>
                                        <div class="text-white">
                                            <div class="fw-900 small">LANGKAH INI — Registrasi Induk & Satuan Dasar
                                            </div>
                                            <div style="font-size: 0.7rem; opacity: 0.7;">Daftarkan nama bahan dan
                                                tentukan satuan terkecilnya (Lembar, Cm, Pcs)</div>
                                        </div>
                                        <span class="badge bg-primary ms-auto flex-shrink-0 px-3">AKTIF</span>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-center gap-3 bg-light border">
                                        <div class="bg-secondary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 36px; height: 36px;">2</div>
                                        <div>
                                            <div class="fw-800 small">Varian & Unit Konversi</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">Atur bentuk fisik kemasan
                                                (Roll/Box/Rim) dan jumlah isinya</div>
                                        </div>
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

                            <!-- ====== TAB 2: ATURAN SATUAN ====== -->
                            <div class="tab-pane fade p-5" id="sop-unit">
                                <h4 class="fw-900 text-dark mb-2">⚖️ Aturan Emas: Satuan Terkecil</h4>
                                <p class="text-muted small mb-4">Satuan yang dipilih di Langkah 1 adalah <strong>unit
                                        hitung dasar</strong> seluruh sistem. Kesalahan di sini akan mempengaruhi
                                    seluruh perhitungan stok, konversi, dan laporan.</p>

                                <div class="p-4 rounded-4 mb-4 d-flex align-items-start gap-3"
                                    style="background: #fef2f2; border: 2px solid #ef4444;">
                                    <i class="fas fa-times-circle text-danger fs-4 flex-shrink-0 mt-1"></i>
                                    <div>
                                        <h6 class="fw-900 text-danger mb-1">DILARANG! Jangan gunakan satuan packing di
                                            sini</h6>
                                        <p class="small text-muted mb-2">Satuan seperti <strong>Roll, Rim, Pack, Dus,
                                                Box, Bal</strong> adalah satuan gudang/kemasan. Ini <u>bukan</u> satuan
                                            terkecil dan akan menyebabkan konversi stok yang salah.</p>
                                        <div class="d-flex gap-2 flex-wrap">
                                            <?php foreach (['Roll', 'Rim', 'Pack', 'Dus', 'Box', 'Bal', 'Karton', 'Lusin'] as $s): ?>
                                                <span
                                                    class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-1 fw-700">
                                                    <?= $s ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 rounded-4 mb-4 d-flex align-items-start gap-3"
                                    style="background: #f0fdf4; border: 2px solid #22c55e;">
                                    <i class="fas fa-check-circle text-success fs-4 flex-shrink-0 mt-1"></i>
                                    <div>
                                        <h6 class="fw-900 text-success mb-1">BENAR! Gunakan satuan terkecil / dasar</h6>
                                        <p class="small text-muted mb-2">Satuan yang merepresentasikan 1 (satu) unit
                                            paling kecil dari barang tersebut.</p>
                                        <div class="d-flex gap-2 flex-wrap">
                                            <?php foreach (['Lembar (Lbr)', 'Centimeter (cm)', 'Pieces (Pcs)', 'Meter (m)', 'Kilogram (Kg)', 'Liter (L)', 'Unit'] as $s): ?>
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1 fw-700">
                                                    <?= $s ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="fw-900 text-dark mb-3">📋 Contoh Referensi Nyata</h6>
                                <div class="table-responsive rounded-4 border">
                                    <table class="table table-sm mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="p-3 small fw-800 text-muted">Nama Bahan</th>
                                                <th class="p-3 small fw-800 text-danger">✗ SALAH (Jangan gunakan)</th>
                                                <th class="p-3 small fw-800 text-success">✓ BENAR (Input di Langkah 1)
                                                </th>
                                                <th class="p-3 small fw-800 text-primary">Satuan Kemasan (Input di
                                                    Langkah 2)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="p-3 small font-monospace fw-700">Art Paper 150gr</td>
                                                <td class="p-3 small font-monospace text-danger">Rim</td>
                                                <td class="p-3 small font-monospace text-success fw-800">Lembar</td>
                                                <td class="p-3 small font-monospace text-primary">Rim (500 lbr), Dus
                                                </td>
                                            </tr>
                                            <tr class="bg-light bg-opacity-50">
                                                <td class="p-3 small font-monospace fw-700">Sticker Vinyl Ritrama</td>
                                                <td class="p-3 small font-monospace text-danger">Roll</td>
                                                <td class="p-3 small font-monospace text-success fw-800">cm</td>
                                                <td class="p-3 small font-monospace text-primary">Roll (10.000 cm)</td>
                                            </tr>
                                            <tr>
                                                <td class="p-3 small font-monospace fw-700">Tinta Epson L3110</td>
                                                <td class="p-3 small font-monospace text-danger">Botol</td>
                                                <td class="p-3 small font-monospace text-success fw-800">ml</td>
                                                <td class="p-3 small font-monospace text-primary">Botol (70 ml), Set
                                                </td>
                                            </tr>
                                            <tr class="bg-light bg-opacity-50">
                                                <td class="p-3 small font-monospace fw-700">Banner Frontlite 280gr</td>
                                                <td class="p-3 small font-monospace text-danger">Roll</td>
                                                <td class="p-3 small font-monospace text-success fw-800">cm</td>
                                                <td class="p-3 small font-monospace text-primary">Roll (50 meter = 5000
                                                    cm)</td>
                                            </tr>
                                            <tr>
                                                <td class="p-3 small font-monospace fw-700">Baut Mur 8mm</td>
                                                <td class="p-3 small font-monospace text-danger">Pack</td>
                                                <td class="p-3 small font-monospace text-success fw-800">Pcs</td>
                                                <td class="p-3 small font-monospace text-primary">Pack (100 pcs), Kotak
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- ====== TAB 3: KATEGORI ====== -->
                            <div class="tab-pane fade p-5" id="sop-category">
                                <h4 class="fw-900 text-dark mb-2">🏷️ 3 Kategori Barang yang Bisa Didaftarkan</h4>
                                <p class="text-muted small mb-4">Pilih kategori yang tepat agar sistem bisa memproses
                                    barang dengan benar. Kategori menentukan alur stok dan laporan yang berbeda.</p>

                                <div class="d-flex flex-column gap-4">
                                    <!-- Kategori 1 -->
                                    <div class="p-4 rounded-4 d-flex gap-4"
                                        style="background: #f0fdf4; border: 2px solid #22c55e;">
                                        <div class="flex-shrink-0 bg-success rounded-3 text-white d-flex align-items-center justify-content-center"
                                            style="width: 48px; height: 48px; font-size: 1.4rem;">
                                            <i class="fas fa-industry"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span class="badge bg-success px-3 py-1 fw-800">PRODUKSI</span>
                                                <h6 class="fw-900 text-success mb-0">Induk Produksi (Bahan Baku)</h6>
                                            </div>
                                            <p class="small text-muted mb-2">Bahan mentah yang <strong>langsung
                                                    digunakan oleh mesin produksi</strong>. Stoknya berkurang otomatis
                                                setiap kali SPK (Surat Perintah Kerja) diselesaikan oleh operator
                                                produksi.</p>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <span class="badge bg-light text-dark border small">Kertas Art
                                                    Paper</span>
                                                <span class="badge bg-light text-dark border small">Sticker Vinyl</span>
                                                <span class="badge bg-light text-dark border small">Banner
                                                    Frontlite</span>
                                                <span class="badge bg-light text-dark border small">Tinta DTF</span>
                                                <span class="badge bg-light text-dark border small">Film Transfer</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Kategori 2 -->
                                    <div class="p-4 rounded-4 d-flex gap-4"
                                        style="background: #eff6ff; border: 2px solid #3b82f6;">
                                        <div class="flex-shrink-0 bg-primary rounded-3 text-white d-flex align-items-center justify-content-center"
                                            style="width: 48px; height: 48px; font-size: 1.4rem;">
                                            <i class="fas fa-shopping-basket"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span class="badge bg-primary px-3 py-1 fw-800">UMUM</span>
                                                <h6 class="fw-900 text-primary mb-0">Barang Umum / Operasional (ATK,
                                                    dll)</h6>
                                            </div>
                                            <p class="small text-muted mb-2">Barang untuk <strong>keperluan kantor atau
                                                    operasional</strong> yang tidak terhubung langsung dengan mesin
                                                produksi. Stoknya dikontrol manual lewat Dispatch.</p>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <span class="badge bg-light text-dark border small">Bolpoin</span>
                                                <span class="badge bg-light text-dark border small">Lakban</span>
                                                <span class="badge bg-light text-dark border small">Tisu</span>
                                                <span class="badge bg-light text-dark border small">Kertas HVS
                                                    Kantor</span>
                                                <span class="badge bg-light text-dark border small">Plastik
                                                    Packing</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Kategori 3 -->
                                    <div class="p-4 rounded-4 d-flex gap-4"
                                        style="background: #f5f3ff; border: 2px solid #8b5cf6;">
                                        <div class="flex-shrink-0 rounded-3 text-white d-flex align-items-center justify-content-center"
                                            style="width: 48px; height: 48px; font-size: 1.4rem; background: #8b5cf6;">
                                            <i class="fas fa-handshake"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span class="badge px-3 py-1 fw-800"
                                                    style="background: #8b5cf6;">JASA</span>
                                                <h6 class="fw-900 mb-0" style="color: #7c3aed;">Jasa Produksi (Non-Stok)
                                                </h6>
                                            </div>
                                            <p class="small text-muted mb-2">Layanan atau pekerjaan yang <strong>tidak
                                                    memiliki stok fisik</strong>, namun memiliki nilai biaya yang
                                                diperhitungkan dalam SPK dan invoice.</p>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <span class="badge bg-light text-dark border small">Jasa Desain</span>
                                                <span class="badge bg-light text-dark border small">Jasa Pasang /
                                                    Instalasi</span>
                                                <span class="badge bg-light text-dark border small">Jasa Finishing
                                                    Laminating</span>
                                                <span class="badge bg-light text-dark border small">Jasa Cutting</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ====== TAB 4: DO & DON'T ====== -->
                            <div class="tab-pane fade p-5" id="sop-do-dont">
                                <h4 class="fw-900 text-dark mb-2">⚡ Panduan Do & Don't Langkah 1</h4>
                                <p class="text-muted small mb-4">Ikuti panduan berikut untuk memastikan data yang Anda
                                    input tidak mengganggu sistem perhitungan stok secara keseluruhan.</p>

                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 h-100"
                                            style="background: #f0fdf4; border: 2px solid #22c55e;">
                                            <h6 class="fw-900 text-success mb-3"><i
                                                    class="fas fa-check-circle me-2"></i>LAKUKAN (DO)</h6>
                                            <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                                                <li class="d-flex gap-2 align-items-start">
                                                    <i class="fas fa-check text-success mt-1 flex-shrink-0"></i>
                                                    <div class="small"><strong>Gunakan nama umum/generik</strong> yang
                                                        dikenal tim (bukan kode supplier).<br><span
                                                            class="text-muted">Contoh: "Art Paper 150gr" bukan
                                                            "AP-150-PINDO"</span></div>
                                                </li>
                                                <li class="d-flex gap-2 align-items-start">
                                                    <i class="fas fa-check text-success mt-1 flex-shrink-0"></i>
                                                    <div class="small"><strong>Pilih satuan terkecil yang mewakili satu
                                                            unit paling kecil</strong> dari barang tersebut.</div>
                                                </li>
                                                <li class="d-flex gap-2 align-items-start">
                                                    <i class="fas fa-check text-success mt-1 flex-shrink-0"></i>
                                                    <div class="small"><strong>Cek dulu apakah bahan sudah ada</strong>
                                                        menggunakan fitur pencarian sebelum menambah baru.</div>
                                                </li>
                                                <li class="d-flex gap-2 align-items-start">
                                                    <i class="fas fa-check text-success mt-1 flex-shrink-0"></i>
                                                    <div class="small"><strong>Pilih kategori yang tepat</strong>
                                                        (Produksi/Umum/Jasa) agar laporan & alur stok benar.</div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 h-100"
                                            style="background: #fef2f2; border: 2px solid #ef4444;">
                                            <h6 class="fw-900 text-danger mb-3"><i
                                                    class="fas fa-times-circle me-2"></i>HINDARI (DON'T)</h6>
                                            <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                                                <li class="d-flex gap-2 align-items-start">
                                                    <i class="fas fa-times text-danger mt-1 flex-shrink-0"></i>
                                                    <div class="small"><strong>Jangan duplikat nama bahan</strong> yang
                                                        sudah ada dengan nama yang berbeda sedikit.<br><span
                                                            class="text-muted">Contoh: "Kertas A4" dan "Kertas A4 80gr"
                                                            bisa jadi duplikat.</span></div>
                                                </li>
                                                <li class="d-flex gap-2 align-items-start">
                                                    <i class="fas fa-times text-danger mt-1 flex-shrink-0"></i>
                                                    <div class="small"><strong>Jangan input satuan packing</strong>
                                                        (Roll, Rim, Box, Dus) sebagai satuan dasar di sini.</div>
                                                </li>
                                                <li class="d-flex gap-2 align-items-start">
                                                    <i class="fas fa-times text-danger mt-1 flex-shrink-0"></i>
                                                    <div class="small"><strong>Jangan hapus induk yang sudah punya
                                                            varian/stok</strong>. Hubungi admin jika perlu penghapusan.
                                                    </div>
                                                </li>
                                                <li class="d-flex gap-2 align-items-start">
                                                    <i class="fas fa-times text-danger mt-1 flex-shrink-0"></i>
                                                    <div class="small"><strong>Jangan input ukuran/dimensi di Langkah
                                                            1</strong>. Ukuran (A3+, 65x100) didaftarkan di Langkah 2
                                                        sebagai Varian.</div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ====== TAB 5: TIPS ====== -->
                            <div class="tab-pane fade p-5" id="sop-tips">
                                <h4 class="fw-900 text-dark mb-2">💡 Tips & Efisiensi Kerja Langkah 1</h4>
                                <p class="text-muted small mb-4">Gunakan tips ini agar proses input data lebih cepat,
                                    akurat, dan minim kesalahan.</p>

                                <div class="d-flex flex-column gap-3">
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-start"
                                        style="background: #fffbeb; border: 1px solid #fcd34d;">
                                        <div class="flex-shrink-0 bg-warning rounded-circle text-dark d-flex align-items-center justify-content-center fw-900"
                                            style="width: 36px; height: 36px;">1</div>
                                        <div>
                                            <div class="fw-800 text-dark small mb-1">Cari Dulu Sebelum Tambah</div>
                                            <p class="text-muted small mb-0">Sebelum klik "Tambah Bahan", ketik nama di
                                                kolom pencarian. Pastikan bahan belum terdaftar untuk mencegah duplikasi
                                                yang akan membingungkan saat Opname dan Pembelian.</p>
                                        </div>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-start"
                                        style="background: #eff6ff; border: 1px solid #93c5fd;">
                                        <div class="flex-shrink-0 bg-primary rounded-circle text-white d-flex align-items-center justify-content-center fw-900"
                                            style="width: 36px; height: 36px;">2</div>
                                        <div>
                                            <div class="fw-800 text-dark small mb-1">Langkah 1 Hanya Perlu Sekali (Setup
                                                Awal)</div>
                                            <p class="text-muted small mb-0">Sekarang Anda hanya perlu menambah bahan
                                                <strong>baru</strong>. Bahan yang sudah ada tidak perlu diinput ulang.
                                                Setelah semua bahan terdaftar, frekuensi penggunaan halaman ini akan
                                                sangat jarang.</p>
                                        </div>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-start"
                                        style="background: #f0fdf4; border: 1px solid #86efac;">
                                        <div class="flex-shrink-0 bg-success rounded-circle text-white d-flex align-items-center justify-content-center fw-900"
                                            style="width: 36px; height: 36px;">3</div>
                                        <div>
                                            <div class="fw-800 text-dark small mb-1">Setelah Tambah Bahan → Langsung ke
                                                Langkah 2</div>
                                            <p class="text-muted small mb-0">Bahan yang baru didaftarkan <strong>belum
                                                    bisa dipakai</strong> dalam stok sampai Anda menambah varian dan
                                                konversinya di Langkah 2. Gunakan tombol "Next" di banner alur untuk
                                                berpindah.</p>
                                        </div>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-start"
                                        style="background: #fdf4ff; border: 1px solid #d8b4fe;">
                                        <div class="flex-shrink-0 rounded-circle text-white d-flex align-items-center justify-content-center fw-900"
                                            style="width: 36px; height: 36px; background: #8b5cf6;">4</div>
                                        <div>
                                            <div class="fw-800 text-dark small mb-1">Export untuk Audit Berkala</div>
                                            <p class="text-muted small mb-0">Gunakan tombol "Export" untuk mengunduh
                                                daftar semua bahan ke file CSV. Cek secara berkala untuk memastikan
                                                tidak ada duplikasi atau kesalahan penamaan yang terakumulasi.</p>
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

<!-- HIGHLIGHT BANNER: diletakkan di halaman utama materials_jasa.php -->
<style>
    #sop-tabs-bahan .nav-link {
        color: #64748b;
        background: white;
        text-align: left !important;
        border-radius: 12px;
        transition: all 0.2s ease;
        border: 1px solid #f1f5f9;
        margin-bottom: 5px;
    }

    #sop-tabs-bahan .nav-link:hover {
        background: #f8fafc;
        color: #1a1c23;
        transform: translateX(5px);
    }

    #sop-tabs-bahan .nav-link.active {
        background: #1e293b;
        color: white;
        border-color: #1e293b;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .shadow-inner {
        box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
    }
</style>