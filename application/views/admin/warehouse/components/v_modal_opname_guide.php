<!-- ================= SOP STOK OPNAME (LANGKAH 3 dari 6) ================= -->
<div class="modal fade" id="modalOpnameGuide" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 28px;">
            <div class="modal-header border-0 py-4 px-4"
                style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%)">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success rounded-3 p-2 d-flex align-items-center justify-content-center"
                        style="width: 42px; height: 42px;">
                        <i class="fas fa-clipboard-check text-white fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-900 mb-0">SOP LANGKAH 3: STOK OPNAME & REKONSILIASI</h5>
                        <small class="text-success opacity-75 fw-bold">Mencocokkan stok fisik dengan sistem — lakukan
                            secara berkala & jujur</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- Sidebar Tabs -->
                    <div class="col-md-3 border-end bg-light bg-opacity-50">
                        <div class="nav flex-column nav-pills p-3 gap-2" id="sop-tabs-opname" role="tablist">
                            <button
                                class="nav-link active text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#opn-workflow" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-project-diagram text-primary" style="width: 20px;"></i> Posisi Langkah
                                3
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#opn-concept" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-sync text-success" style="width: 20px;"></i> Apa itu Opname?
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#opn-steps" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-list-ol text-info" style="width: 20px;"></i> Cara Input Opname
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#opn-delta" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-calculator text-warning" style="width: 20px;"></i> Deviasi & Selisih
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#opn-sync" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-check-double text-danger" style="width: 20px;"></i> Sinkronisasi Stok
                            </button>
                        </div>
                    </div>
                    <!-- Content -->
                    <div class="col-md-9 px-0 bg-white">
                        <div class="tab-content h-100">

                            <!-- TAB 1: POSISI LANGKAH 3 -->
                            <div class="tab-pane fade show active p-5" id="opn-workflow">
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <span class="badge bg-success px-3 py-2 rounded-pill fw-800"
                                        style="font-size: 0.8rem;">LANGKAH 3 dari 6</span>
                                    <h4 class="fw-900 text-dark mb-0">Posisi Anda dalam Alur Kerja Gudang</h4>
                                </div>
                                <div class="p-4 rounded-4 mb-4 d-flex align-items-center gap-3"
                                    style="background: #f0fdf4; border: 2px solid #22c55e;">
                                    <div class="flex-shrink-0 text-success" style="font-size: 2rem;"><i
                                            class="fas fa-clipboard-check"></i></div>
                                    <div>
                                        <h6 class="fw-900 mb-1 text-success">📋 HALAMAN INI UNTUK MENCOCOKKAN STOK FISIK
                                            vs SISTEM</h6>
                                        <p class="small mb-0 text-muted">Stok Opname adalah proses <strong>menghitung
                                                barang secara fisik di gudang</strong>, lalu membandingkannya dengan
                                            angka di sistem. Jika ada selisih, Anda bisa langsung mensinkronisasi stok
                                            agar sistem kembali akurat.</p>
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-2">
                                    <div class="p-3 rounded-3 d-flex align-items-center gap-3 bg-light border">
                                        <div class="bg-secondary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 36px; height: 36px;">1</div>
                                        <div>
                                            <div class="fw-800 small">Registrasi Induk & Satuan Dasar</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">Sudah selesai di halaman
                                                Master Induk & Jasa</div>
                                        </div>
                                        <span class="badge bg-success ms-auto flex-shrink-0 px-3">✓ SELESAI</span>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-center gap-3 bg-light border">
                                        <div class="bg-secondary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 36px; height: 36px;">2</div>
                                        <div>
                                            <div class="fw-800 small">Varian, Konversi & Barcode</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">Sudah selesai di halaman
                                                Master Bahan & Material</div>
                                        </div>
                                        <span class="badge bg-success ms-auto flex-shrink-0 px-3">✓ SELESAI</span>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-center gap-3"
                                        style="background: #1e293b; border: 2px solid #6366f1;">
                                        <div class="bg-success rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 36px; height: 36px;">3</div>
                                        <div class="text-white">
                                            <div class="fw-900 small">LANGKAH INI — Stok Opname (Saldo Awal /
                                                Rekonsiliasi)</div>
                                            <div style="font-size: 0.7rem; opacity: 0.7;">Hitung fisik → bandingkan →
                                                sinkronisasi stok sistem</div>
                                        </div>
                                        <span class="badge bg-success ms-auto flex-shrink-0 px-3">AKTIF</span>
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
                                                gudang menuju area produksi</div>
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

                            <!-- TAB 2: APA ITU OPNAME -->
                            <div class="tab-pane fade p-5" id="opn-concept">
                                <h4 class="fw-900 text-dark mb-2">🔎 Apa itu Stok Opname?</h4>
                                <p class="text-muted small mb-4">Opname adalah proses <strong>verifikasi kebenaran data
                                        stok</strong> dengan membandingkan hitungan fisik di gudang dengan angka yang
                                    tersimpan di sistem ERP.</p>
                                <div class="row g-4 mb-4">
                                    <div class="col-md-4">
                                        <div class="p-4 rounded-4 text-center h-100"
                                            style="background: #eff6ff; border: 2px solid #3b82f6;">
                                            <div class="text-primary mb-2" style="font-size: 2rem;"><i
                                                    class="fas fa-search"></i></div>
                                            <h6 class="fw-900 text-primary">Deteksi Selisih</h6>
                                            <p class="small text-muted mb-0">Menemukan barang hilang, rusak, atau salah
                                                hitung yang belum tercatat sistem.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-4 rounded-4 text-center h-100"
                                            style="background: #f0fdf4; border: 2px solid #22c55e;">
                                            <div class="text-success mb-2" style="font-size: 2rem;"><i
                                                    class="fas fa-check-circle"></i></div>
                                            <h6 class="fw-900 text-success">Akurasi Data</h6>
                                            <p class="small text-muted mb-0">Data stok valid = perencanaan produksi dan
                                                pembelian jadi lebih tepat.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-4 rounded-4 text-center h-100"
                                            style="background: #fffbeb; border: 2px solid #f59e0b;">
                                            <div class="text-warning mb-2" style="font-size: 2rem;"><i
                                                    class="fas fa-file-invoice-dollar"></i></div>
                                            <h6 class="fw-900 text-warning" style="color: #92400e !important;">Audit
                                                Nilai Aset</h6>
                                            <p class="small text-muted mb-0">Manajemen bisa mengetahui nilai stok gudang
                                                yang sebenarnya untuk laporan keuangan.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-4 rounded-4" style="background: #1e293b; color: white;">
                                    <h6 class="fw-900 text-warning mb-2"><i class="fas fa-calendar-alt me-2"></i>Kapan
                                        Opname Dilakukan?</h6>
                                    <div class="row g-3 small">
                                        <div class="col-md-4">
                                            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.1);">
                                                <div class="fw-800 text-info mb-1">🔰 Opname Awal</div>
                                                <div class="opacity-75">Dilakukan saat pertama kali menggunakan sistem.
                                                    Input saldo awal semua bahan sebelum transaksi dimulai.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.1);">
                                                <div class="fw-800 text-success mb-1">📅 Opname Rutin</div>
                                                <div class="opacity-75">Dilakukan bulanan/mingguan untuk memastikan data
                                                    stok selalu akurat dan mendeteksi selisih lebih awal.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.1);">
                                                <div class="fw-800 text-warning mb-1">🚨 Opname Insidental</div>
                                                <div class="opacity-75">Dilakukan saat ada dugaan kehilangan barang,
                                                    kerusakan, atau laporan stok minus yang tidak wajar.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 3: CARA INPUT -->
                            <div class="tab-pane fade p-5" id="opn-steps">
                                <h4 class="fw-900 text-dark mb-2">📝 Cara Input Stok Opname (Smart Input)</h4>
                                <p class="text-muted small mb-4">Sistem dirancang dengan <strong>Smart Dual-Unit
                                        Input</strong> — Anda cukup memasukkan angka yang Anda lihat secara fisik di
                                    rak, sistem yang akan menghitung konversinya.</p>

                                <div class="d-flex flex-column gap-3 mb-4">
                                    <div class="p-3 rounded-3 d-flex align-items-start gap-3"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="bg-primary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 32px; height: 32px; font-size: 0.85rem;">1</div>
                                        <div class="small"><strong>Pilih Gudang & Material</strong> dari dropdown. Panel
                                            <em>"STOK SISTEM"</em> akan muncul menampilkan stok saat ini dalam satuan
                                            besar dan kecil.</div>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-start gap-3"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="bg-primary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 32px; height: 32px; font-size: 0.85rem;">2</div>
                                        <div class="small"><strong>Input Satuan Besar</strong> (Roll/Rim/Box): Masukkan
                                            jumlah kemasan utuh yang ada di rak. Misal: <kbd>5</kbd> Rim.</div>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-start gap-3"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="bg-primary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 32px; height: 32px; font-size: 0.85rem;">3</div>
                                        <div class="small"><strong>Input Satuan Kecil</strong> (Lembar/cm/Pcs): Masukkan
                                            sisa eceran. Misal: <kbd>250</kbd> Lembar. Isi <kbd>0</kbd> jika tidak ada
                                            sisa eceran.</div>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-start gap-3"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="bg-primary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 32px; height: 32px; font-size: 0.85rem;">4</div>
                                        <div class="small"><strong>Cek angka SELISIH</strong> yang muncul otomatis.
                                            Selisih hijau = cocok. Merah/oranye = ada perbedaan. Pastikan selisih masuk
                                            akal sebelum lanjut.</div>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-start gap-3"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="bg-success rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 32px; height: 32px; font-size: 0.85rem;">5</div>
                                        <div class="small"><strong>Klik Simpan</strong>. Jika toggle "Sinkronisasi
                                            Otomatis" aktif, stok sistem akan langsung disesuaikan dengan hasil hitung
                                            fisik.</div>
                                    </div>
                                </div>

                                <div class="p-4 rounded-4" style="background: #1e293b; color: white;">
                                    <h6 class="fw-900 text-info mb-3"><i class="fas fa-lightbulb me-2"></i>CONTOH REAL
                                        di Lapangan</h6>
                                    <div class="row g-3 small">
                                        <div class="col-md-6">
                                            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.1);">
                                                <div class="fw-800 text-warning mb-2">Contoh: Art Paper 150gr (Rim = 500
                                                    lbr)</div>
                                                <div class="opacity-75 mb-2">Di rak ada: <strong>5 Rim</strong> + sisa
                                                    <strong>250 Lembar</strong></div>
                                                <div class="d-flex gap-2 mb-1"><span
                                                        class="badge bg-primary px-3 py-1">5</span><span>← input di
                                                        kolom Rim</span></div>
                                                <div class="d-flex gap-2"><span
                                                        class="badge bg-info px-3 py-1">250</span><span>← input di kolom
                                                        Lembar</span></div>
                                                <hr class="opacity-20 my-2">
                                                <div class="text-warning fw-800">Total = 5.500 lbr dihitung otomatis
                                                    oleh sistem</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.1);">
                                                <div class="fw-800 text-warning mb-2">Contoh: Sticker Vinyl Ritrama
                                                    (Roll = 5000 cm)</div>
                                                <div class="opacity-75 mb-2">Di rak ada: <strong>3 Roll</strong> penuh +
                                                    <strong>800 cm</strong></div>
                                                <div class="d-flex gap-2 mb-1"><span
                                                        class="badge bg-primary px-3 py-1">3</span><span>← input di
                                                        kolom Roll</span></div>
                                                <div class="d-flex gap-2"><span
                                                        class="badge bg-info px-3 py-1">800</span><span>← input di kolom
                                                        cm</span></div>
                                                <hr class="opacity-20 my-2">
                                                <div class="text-warning fw-800">Total = 15.800 cm dihitung otomatis
                                                    oleh sistem</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 4: DEVIASI -->
                            <div class="tab-pane fade p-5" id="opn-delta">
                                <h4 class="fw-900 text-dark mb-2">📊 Memahami Deviasi (Selisih)</h4>
                                <p class="text-muted small mb-4">Sistem menghitung selisih antara stok fisik yang Anda
                                    input dengan stok yang tercatat di sistem. Gunakan ini untuk mengambil keputusan
                                    audit.</p>

                                <div class="d-flex flex-column gap-3 mb-4">
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-center"
                                        style="background: #f0fdf4; border: 2px solid #22c55e;">
                                        <div class="fw-900 text-success fs-4 flex-shrink-0">= 0</div>
                                        <div>
                                            <div class="fw-900 text-success small">MATCH — Stok Fisik SAMA dengan Sistem
                                            </div>
                                            <p class="small text-muted mb-0">Data akurat. Tidak perlu tindakan.
                                                Lanjutkan ke bahan berikutnya.</p>
                                        </div>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-center"
                                        style="background: #fffbeb; border: 2px solid #f59e0b;">
                                        <div class="fw-900 fs-4 flex-shrink-0" style="color: #d97706;">+ X</div>
                                        <div>
                                            <div class="fw-900 small" style="color: #92400e;">LEBIH — Fisik Lebih Banyak
                                                dari Sistem</div>
                                            <p class="small text-muted mb-0">Kemungkinan: ada barang masuk (pembelian)
                                                yang belum dicatat, atau ada return dari produksi yang belum diinput.
                                                Investigasi sebelum sinkronisasi.</p>
                                        </div>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-center"
                                        style="background: #fef2f2; border: 2px solid #ef4444;">
                                        <div class="fw-900 text-danger fs-4 flex-shrink-0">- X</div>
                                        <div>
                                            <div class="fw-900 text-danger small">KURANG — Fisik Lebih Sedikit dari
                                                Sistem</div>
                                            <p class="small text-muted mb-0">Kemungkinan: barang hilang/rusak, ada
                                                pengeluaran ke produksi yang belum dicatat, atau kesalahan hitung
                                                sebelumnya. Wajib investigasi sebelum sinkronisasi.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <h6 class="fw-900 text-dark mb-2"><i
                                            class="fas fa-search me-2 text-primary"></i>Langkah Investigasi Sebelum
                                        Sinkronisasi</h6>
                                    <ol class="small text-muted mb-0">
                                        <li class="mb-1">Hitung ulang barang fisik di rak — pastikan tidak ada salah
                                            hitung.</li>
                                        <li class="mb-1">Cek riwayat mutasi stok (Modul Ledger) — apakah ada transaksi
                                            yang belum masuk.</li>
                                        <li class="mb-1">Konfirmasi dengan operator produksi — apakah ada pengambilan
                                            bahan yang belum dicatat Dispatch.</li>
                                        <li>Dokumentasikan alasan selisih — simpan catatan untuk keperluan audit
                                            manajemen.</li>
                                    </ol>
                                </div>
                            </div>

                            <!-- TAB 5: SINKRONISASI -->
                            <div class="tab-pane fade p-5" id="opn-sync">
                                <h4 class="fw-900 text-dark mb-2">⚡ Sinkronisasi Stok Sistem</h4>
                                <p class="text-muted small mb-4">Setelah opname selesai dan selisih sudah diverifikasi,
                                    gunakan fitur sinkronisasi untuk menyesuaikan stok sistem dengan hasil fisik.</p>

                                <div class="p-4 rounded-4 mb-4 d-flex align-items-start gap-3"
                                    style="background: #eff6ff; border: 2px solid #3b82f6;">
                                    <i class="fas fa-toggle-on text-primary fs-4 flex-shrink-0 mt-1"></i>
                                    <div>
                                        <h6 class="fw-900 text-primary mb-1">Toggle "Otomatis Update Stok Sistem"</h6>
                                        <p class="small text-muted mb-0">Jika switch ini <strong>AKTIF (ON)</strong>,
                                            saat Anda menyimpan hasil opname, sistem akan <strong>langsung mengubah
                                                saldo stok</strong> material tersebut agar sama dengan hasil hitung
                                            fisik Anda. Jika OFF, hasil opname hanya tersimpan sebagai catatan audit
                                            tanpa mengubah stok.</p>
                                    </div>
                                </div>

                                <div class="p-4 rounded-4 mb-4 d-flex align-items-start gap-3"
                                    style="background: #fef2f2; border: 2px solid #ef4444;">
                                    <i class="fas fa-exclamation-triangle text-danger fs-4 flex-shrink-0 mt-1"></i>
                                    <div>
                                        <h6 class="fw-900 text-danger mb-1">⚠️ PERHATIAN — Sinkronisasi Tidak Bisa
                                            Dibatalkan</h6>
                                        <p class="small text-muted mb-2">Pastikan hitungan fisik Anda sudah <strong>100%
                                                benar</strong> sebelum melakukan sinkronisasi. Saldo stok akan langsung
                                            berubah dan mempengaruhi seluruh modul ERP (Monitoring, Dispatch, Laporan).
                                        </p>
                                        <div class="d-flex flex-column gap-1 small">
                                            <div class="d-flex gap-2"><i
                                                    class="fas fa-times text-danger flex-shrink-0 mt-1"></i>
                                                <div>Jangan sinkronisasi jika masih ragu dengan hasil hitung</div>
                                            </div>
                                            <div class="d-flex gap-2"><i
                                                    class="fas fa-times text-danger flex-shrink-0 mt-1"></i>
                                                <div>Jangan sinkronisasi saat operasi produksi sedang berjalan aktif
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2"><i
                                                    class="fas fa-check text-success flex-shrink-0 mt-1"></i>
                                                <div>Lakukan di luar jam produksi (misal: setelah jam kerja atau hari
                                                    Sabtu)</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 rounded-4" style="background: #f0fdf4; border: 2px solid #22c55e;">
                                    <h6 class="fw-900 text-success mb-2"><i class="fas fa-check-double me-2"></i>Setelah
                                        Sinkronisasi ✓</h6>
                                    <p class="small text-muted mb-2">Setelah opname dan sinkronisasi selesai, lanjutkan
                                        ke Langkah 4 untuk mencatat pembelian bahan baru dari supplier.</p>
                                    <a href="<?= base_url('warehouse/purchase') ?>"
                                        class="btn btn-sm btn-success fw-800 rounded-pill px-4">
                                        <i class="fas fa-arrow-right me-2"></i>Ke Langkah 4: Pembelian
                                    </a>
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
    #sop-tabs-opname .nav-link {
        color: #64748b;
        background: white;
        text-align: left !important;
        border-radius: 12px;
        transition: all 0.2s ease;
        border: 1px solid #f1f5f9;
        margin-bottom: 5px;
    }

    #sop-tabs-opname .nav-link:hover {
        background: #f8fafc;
        color: #1a1c23;
        transform: translateX(5px);
    }

    #sop-tabs-opname .nav-link.active {
        background: #1e293b;
        color: white;
        border-color: #1e293b;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
</style>