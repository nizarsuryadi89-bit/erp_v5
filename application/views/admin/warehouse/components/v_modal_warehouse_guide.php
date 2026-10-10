<!-- ================= SOP PANDUAN UMUM WAREHOUSE MANAGEMENT ================= -->
<div class="modal fade" id="modalWarehouseGuide" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 28px;">
            <div class="modal-header border-0 py-4 px-4"
                style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%)">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center"
                        style="width: 42px; height: 42px; background: #6366f1;">
                        <i class="fas fa-book-reader text-white fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-900 mb-0">PANDUAN SISTEM WAREHOUSE TGM</h5>
                        <small class="fw-bold" style="color: #a5b4fc; opacity: 0.9;">Panduan implementasi, operasional,
                            dan audit untuk Management & Operator</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-3 border-end bg-light bg-opacity-50">
                        <div class="nav flex-column nav-pills p-3 gap-2" id="sop-tabs-warehouse" role="tablist">
                            <button
                                class="nav-link active text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#wh-alur6" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-stream text-primary" style="width: 20px;"></i> Alur 6 Langkah
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#wh-implementasi" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-rocket text-success" style="width: 20px;"></i> Panduan Implementasi
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#wh-operasional" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-cogs text-warning" style="width: 20px;"></i> Operasional Harian
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#wh-audit" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-clipboard-check text-danger" style="width: 20px;"></i> Pengawasan &
                                Audit
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#wh-troubleshoot" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-tools text-info" style="width: 20px;"></i> Troubleshoot & Kontak
                            </button>
                        </div>
                    </div>
                    <div class="col-md-9 px-0 bg-white">
                        <div class="tab-content h-100">

                            <!-- TAB 1: ALUR 6 LANGKAH -->
                            <div class="tab-pane fade show active p-5" id="wh-alur6">
                                <h4 class="fw-900 text-dark mb-2">🗺️ Alur 6 Langkah Sistem Gudang TGM</h4>
                                <p class="text-muted small mb-4">Sistem Warehouse ERP bekerja dalam satu alur
                                    terintegrasi. Semua 6 langkah harus dilakukan secara berurutan untuk pertama kali.
                                    Setelah setup, Langkah 4–6 berjalan setiap hari.</p>

                                <div class="d-flex flex-column gap-3 mb-4">
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-center"
                                        style="background: #eff6ff; border: 2px solid #3b82f6;">
                                        <div class="bg-primary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 44px; height: 44px; font-size: 1.1rem;">1</div>
                                        <div class="flex-grow-1">
                                            <div class="fw-900 text-dark mb-1">Registrasi Induk & Satuan Dasar <span
                                                    class="badge bg-secondary ms-2" style="font-size: 0.65rem;">SETUP
                                                    SEKALI</span></div>
                                            <div class="small text-muted">Daftarkan semua nama bahan dengan satuan
                                                terkecilnya (Lembar, cm, Pcs, ml). Ini fondasi seluruh sistem.</div>
                                        </div>
                                        <a href="<?= base_url('warehouse/materials_jasa') ?>"
                                            class="btn btn-sm btn-primary flex-shrink-0 fw-800 px-3">Buka →</a>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-center"
                                        style="background: #f0fdf4; border: 2px solid #22c55e;">
                                        <div class="bg-success rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 44px; height: 44px; font-size: 1.1rem;">2</div>
                                        <div class="flex-grow-1">
                                            <div class="fw-900 text-dark mb-1">Varian, Konversi & Barcode <span
                                                    class="badge bg-secondary ms-2" style="font-size: 0.65rem;">SETUP
                                                    SEKALI</span></div>
                                            <div class="small text-muted">Daftarkan bentuk fisik kemasan (Roll/Rim/Box)
                                                dan isi konversinya. Cetak label barcode untuk ditempel di barang.</div>
                                        </div>
                                        <a href="<?= base_url('warehouse/materials') ?>"
                                            class="btn btn-sm btn-success flex-shrink-0 fw-800 px-3">Buka →</a>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-center"
                                        style="background: #fffbeb; border: 2px solid #f59e0b;">
                                        <div class="rounded-circle text-dark fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 44px; height: 44px; font-size: 1.1rem; background: #f59e0b;">3
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-900 text-dark mb-1">Stok Opname (Saldo Awal) <span
                                                    class="badge bg-secondary ms-2"
                                                    style="font-size: 0.65rem;">BERKALA</span></div>
                                            <div class="small text-muted">Hitung fisik → input ke sistem → sinkronisasi.
                                                Wajib dilakukan pertama kali sebelum operasional dimulai.</div>
                                        </div>
                                        <a href="<?= base_url('warehouse/opname') ?>"
                                            class="btn btn-sm flex-shrink-0 fw-800 px-3"
                                            style="background: #f59e0b; color: #1e293b;">Buka →</a>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-center"
                                        style="background: #f5f3ff; border: 2px solid #8b5cf6;">
                                        <div class="rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 44px; height: 44px; font-size: 1.1rem; background: #8b5cf6;">4
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-900 text-dark mb-1">Pembelian / Penerimaan Barang <span
                                                    class="badge bg-success ms-2"
                                                    style="font-size: 0.65rem;">HARIAN</span></div>
                                            <div class="small text-muted">Catat setiap barang datang dari supplier. Stok
                                                otomatis bertambah. Bisa Cash atau Hutang.</div>
                                        </div>
                                        <a href="<?= base_url('warehouse/purchase') ?>"
                                            class="btn btn-sm flex-shrink-0 fw-800 px-3"
                                            style="background: #8b5cf6; color: white;">Buka →</a>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-center"
                                        style="background: #fef2f2; border: 2px solid #ef4444;">
                                        <div class="bg-danger rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 44px; height: 44px; font-size: 1.1rem;">5</div>
                                        <div class="flex-grow-1">
                                            <div class="fw-900 text-dark mb-1">Dispatch ke Produksi <span
                                                    class="badge bg-success ms-2"
                                                    style="font-size: 0.65rem;">HARIAN</span></div>
                                            <div class="small text-muted">Catat setiap bahan keluar dari gudang ke
                                                produksi. Stok otomatis berkurang. Wajib per transaksi.</div>
                                        </div>
                                        <a href="<?= base_url('warehouse/dispatch') ?>"
                                            class="btn btn-sm btn-danger flex-shrink-0 fw-800 px-3">Buka →</a>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-center"
                                        style="background: #f0f9ff; border: 2px solid #0ea5e9;">
                                        <div class="rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 44px; height: 44px; font-size: 1.1rem; background: #0ea5e9;">6
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-900 text-dark mb-1">Monitoring Stok Real-Time <span
                                                    class="badge bg-success ms-2"
                                                    style="font-size: 0.65rem;">HARIAN</span></div>
                                            <div class="small text-muted">Dashboard utama — pantau sisa stok semua
                                                bahan, deteksi yang hampir habis, dan pastikan tidak ada stok minus.
                                            </div>
                                        </div>
                                        <a href="<?= base_url('warehouse/stock') ?>"
                                            class="btn btn-sm flex-shrink-0 fw-800 px-3"
                                            style="background: #0ea5e9; color: white;">Buka →</a>
                                    </div>
                                </div>

                                <div class="p-4 rounded-4" style="background: #1e293b; color: white;">
                                    <h6 class="fw-900 text-yellow-300 mb-1" style="color: #fbbf24;"><i
                                            class="fas fa-gem me-2"></i>Prinsip Emas Warehouse TGM</h6>
                                    <p class="small mb-0 opacity-75">"<strong>BARANG PINDAH = DATA PINDAH</strong>" —
                                        Setiap pergerakan barang, sekecil apapun, harus direkam dalam sistem di waktu
                                        yang sama (real-time). Kejujuran dan kedisiplinan input adalah 90% keberhasilan
                                        sistem ini.</p>
                                </div>
                            </div>

                            <!-- TAB 2: PANDUAN IMPLEMENTASI -->
                            <div class="tab-pane fade p-5" id="wh-implementasi">
                                <h4 class="fw-900 text-dark mb-2">🚀 Panduan Implementasi Sistem Baru</h4>
                                <p class="text-muted small mb-4">Untuk pertama kali menggunakan sistem, ikuti 4 Fase
                                    berikut secara berurutan. Jangan loncat fase!</p>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 h-100"
                                            style="background: #eff6ff; border: 2px solid #3b82f6;">
                                            <h6 class="fw-900 text-primary mb-3"><i class="fas fa-cog me-2"></i>FASE 1 —
                                                Persiapan Master Data <small
                                                    class="text-muted fw-normal">(Management)</small></h6>
                                            <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-check-circle text-primary mt-1 flex-shrink-0"></i>
                                                    <div><strong>Mapping lokasi:</strong> Tentukan pembagian gudang
                                                        (Utama, Produksi/WIP, Cabang)</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-check-circle text-primary mt-1 flex-shrink-0"></i>
                                                    <div><strong>Daftarkan semua bahan</strong> dan satuan terkecilnya
                                                        di Langkah 1</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-check-circle text-primary mt-1 flex-shrink-0"></i>
                                                    <div><strong>Buat semua varian & konversi</strong> di Langkah 2.
                                                        Cetak barcode.</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-check-circle text-primary mt-1 flex-shrink-0"></i>
                                                    <div><strong>Set angka Stok Minimal</strong> per bahan untuk trigger
                                                        reorder otomatis</div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 h-100"
                                            style="background: #fffbeb; border: 2px solid #f59e0b;">
                                            <h6 class="fw-900 mb-3" style="color: #92400e;"><i
                                                    class="fas fa-box-open me-2"></i>FASE 2 — Inisialisasi Stok Awal
                                                <small class="text-muted fw-normal">(Operator + Mngmt)</small></h6>
                                            <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-flag text-warning mt-1 flex-shrink-0"></i>
                                                    <div><strong>Cut-off sementara:</strong> Hentikan aktivitas
                                                        kirim/terima barang fisik</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-arrow-right text-warning mt-1 flex-shrink-0"></i>
                                                    <div><strong>Hitung manual</strong> semua barang di setiap
                                                        rak/gudang. Catat di kertas.</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-arrow-right text-warning mt-1 flex-shrink-0"></i>
                                                    <div><strong>Input saldo awal</strong> via menu Stock Opname
                                                        (Langkah 3)</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-sync text-warning mt-1 flex-shrink-0"></i>
                                                    <div><strong>Klik Simpan & Sync</strong> — sistem siap beroperasi!
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 h-100"
                                            style="background: #f0fdf4; border: 2px solid #22c55e;">
                                            <h6 class="fw-900 text-success mb-3"><i class="fas fa-running me-2"></i>FASE
                                                3 — Operasional Real-Time <small
                                                    class="text-muted fw-normal">(Operator)</small></h6>
                                            <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-plus-circle text-success mt-1 flex-shrink-0"></i>
                                                    <div>Input setiap kiriman supplier via menu
                                                        <strong>Pembelian</strong> saat barang tiba</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-minus-circle text-success mt-1 flex-shrink-0"></i>
                                                    <div>Wajib input <strong>Dispatch</strong> setiap barang keluar ke
                                                        produksi</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-desktop text-success mt-1 flex-shrink-0"></i>
                                                    <div>Pantau <strong>Live Monitor</strong> untuk permintaan bahan
                                                        dari departemen produksi</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-scroll text-success mt-1 flex-shrink-0"></i>
                                                    <div>Pastikan Kartu Stok (Ledger) sesuai nota fisik sebelum jam
                                                        kerja berakhir</div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 h-100"
                                            style="background: #fef2f2; border: 2px solid #ef4444;">
                                            <h6 class="fw-900 text-danger mb-3"><i
                                                    class="fas fa-chart-line me-2"></i>FASE 4 — Pengawasan & Audit
                                                <small class="text-muted fw-normal">(Management)</small></h6>
                                            <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-eye text-danger mt-1 flex-shrink-0"></i>
                                                    <div>Pantau <strong>Live Monitor</strong> di Dashboard untuk kinerja
                                                        operator harian</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-search text-danger mt-1 flex-shrink-0"></i>
                                                    <div>Gunakan menu <strong>Ledger</strong> untuk menelusuri selisih
                                                        barang via audit trail</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-clipboard-check text-danger mt-1 flex-shrink-0"></i>
                                                    <div>Lakukan <strong>Opname Berkala</strong> (mingguan/bulanan) —
                                                        minimal spot-check 10 item acak</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-file-invoice-dollar text-danger mt-1 flex-shrink-0"></i>
                                                    <div>Tarik <strong>Laporan Valuasi</strong> stok untuk keperluan
                                                        laporan keuangan manajemen</div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 3: OPERASIONAL HARIAN -->
                            <div class="tab-pane fade p-5" id="wh-operasional">
                                <h4 class="fw-900 text-dark mb-2">⚙️ Checklist Operasional Harian Operator</h4>
                                <p class="text-muted small mb-4">Lakukan checklist ini setiap hari kerja untuk
                                    memastikan data gudang selalu akurat dan up-to-date.</p>

                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 h-100"
                                            style="background: #f0fdf4; border: 2px solid #22c55e;">
                                            <h6 class="fw-900 text-success mb-3">🌅 Awal Hari (Sebelum Produksi Mulai)
                                            </h6>
                                            <div class="d-flex flex-column gap-2 small">
                                                <div class="d-flex gap-2 align-items-start"><i
                                                        class="fas fa-square text-muted mt-1 flex-shrink-0"></i>
                                                    <div>Cek halaman <strong>Monitoring Stok</strong> — ada barang
                                                        dengan status LIMIT atau HABIS?</div>
                                                </div>
                                                <div class="d-flex gap-2 align-items-start"><i
                                                        class="fas fa-square text-muted mt-1 flex-shrink-0"></i>
                                                    <div>Cek halaman <strong>Rencana Belanja</strong> — koordinasi
                                                        dengan purchasing jika ada item kritis</div>
                                                </div>
                                                <div class="d-flex gap-2 align-items-start"><i
                                                        class="fas fa-square text-muted mt-1 flex-shrink-0"></i>
                                                    <div>Cek <strong>Live Monitor</strong> untuk permintaan dispatch
                                                        dari produksi yang masuk semalam</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 h-100"
                                            style="background: #eff6ff; border: 2px solid #3b82f6;">
                                            <h6 class="fw-900 text-primary mb-3">🌆 Akhir Hari (Sebelum Pulang)</h6>
                                            <div class="d-flex flex-column gap-2 small">
                                                <div class="d-flex gap-2 align-items-start"><i
                                                        class="fas fa-square text-muted mt-1 flex-shrink-0"></i>
                                                    <div>Pastikan semua <strong>nota pembelian hari ini</strong> sudah
                                                        diinput ke sistem Purchase</div>
                                                </div>
                                                <div class="d-flex gap-2 align-items-start"><i
                                                        class="fas fa-square text-muted mt-1 flex-shrink-0"></i>
                                                    <div>Pastikan semua <strong>pengambilan bahan</strong> ke produksi
                                                        sudah dicatat via Dispatch</div>
                                                </div>
                                                <div class="d-flex gap-2 align-items-start"><i
                                                        class="fas fa-square text-muted mt-1 flex-shrink-0"></i>
                                                    <div>Cocokkan angka <strong>total transaksi hari ini</strong> dengan
                                                        catatan fisik nota/surat jalan</div>
                                                </div>
                                                <div class="d-flex gap-2 align-items-start"><i
                                                        class="fas fa-square text-muted mt-1 flex-shrink-0"></i>
                                                    <div>Laporkan ke Supervisor jika ada selisih atau kejadian luar
                                                        biasa</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 rounded-4" style="background: #1e293b; color: white;">
                                    <h6 class="fw-900 mb-3" style="color: #fbbf24;"><i
                                            class="fas fa-exclamation-triangle me-2"></i>3 Hal yang TIDAK BOLEH
                                        Dilakukan Operator</h6>
                                    <div class="row g-3 small">
                                        <div class="col-md-4">
                                            <div class="p-3 rounded-3"
                                                style="background: rgba(239,68,68,0.2); border: 1px solid #ef4444;">
                                                <div class="fw-900 text-red-300 mb-1" style="color: #fca5a5;">❌ Input
                                                    Mundur Tanpa Izin</div>
                                                <div class="opacity-75">Jangan backdate transaksi lebih dari 1 hari
                                                    tanpa persetujuan Supervisor. Ini merusak laporan harian.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-3 rounded-3"
                                                style="background: rgba(239,68,68,0.2); border: 1px solid #ef4444;">
                                                <div class="fw-900 mb-1" style="color: #fca5a5;">❌ Biarkan Stok Minus
                                                </div>
                                                <div class="opacity-75">Jika stok minus muncul, segera investigasi dan
                                                    laporkan. Bukan diabaikan atau diakali dengan input fiktif.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-3 rounded-3"
                                                style="background: rgba(239,68,68,0.2); border: 1px solid #ef4444;">
                                                <div class="fw-900 mb-1" style="color: #fca5a5;">❌ Ambil Bahan Tanpa
                                                    Dispatch</div>
                                                <div class="opacity-75">Setiap pengambilan bahan dari rak, sekecil
                                                    apapun, WAJIB diinput Dispatch. Tidak ada pengecualian.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 4: PENGAWASAN & AUDIT -->
                            <div class="tab-pane fade p-5" id="wh-audit">
                                <h4 class="fw-900 text-dark mb-2">🔍 Pengawasan & Audit (Management)</h4>
                                <p class="text-muted small mb-4">Panduan untuk Manajer/Supervisor dalam memantau
                                    kualitas data dan kinerja operator gudang.</p>
                                <div class="d-flex flex-column gap-3 mb-4">
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-start"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="rounded-3 text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 40px; height: 40px; background: #6366f1;"><i
                                                class="fas fa-tachometer-alt"></i></div>
                                        <div>
                                            <div class="fw-800 text-dark small mb-1">Dashboard Monitoring Real-Time
                                            </div>
                                            <p class="text-muted small mb-0">Buka halaman <strong>Monitoring
                                                    Stok</strong> setiap pagi untuk melihat kondisi stok global.
                                                Perhatikan kartu "HABIS" dan "LIMIT" — ini sinyal bahwa purchasing harus
                                                segera bertindak.</p>
                                        </div>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-start"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="rounded-3 text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 40px; height: 40px; background: #0ea5e9;"><i
                                                class="fas fa-scroll"></i></div>
                                        <div>
                                            <div class="fw-800 text-dark small mb-1">Audit Trail via Ledger</div>
                                            <p class="text-muted small mb-0">Gunakan menu <strong>Kartu Stok
                                                    (Ledger)</strong> untuk menelusuri riwayat mutasi barang. Sangat
                                                berguna jika ada laporan selisih yang tidak bisa dijelaskan oleh
                                                operator.</p>
                                        </div>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-start"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="rounded-3 text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 40px; height: 40px; background: #22c55e;"><i
                                                class="fas fa-clipboard-check"></i></div>
                                        <div>
                                            <div class="fw-800 text-dark small mb-1">Opname Spot-Check Acak (Mingguan)
                                            </div>
                                            <p class="text-muted small mb-0">Pilih 10–15 item secara acak setiap minggu
                                                untuk dihitung fisik dan dibandingkan dengan sistem. Ini cara efektif
                                                mendeteksi kecurangan atau kelalaian operator lebih awal.</p>
                                        </div>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-start"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="rounded-3 text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 40px; height: 40px; background: #f59e0b;"><i
                                                class="fas fa-file-invoice-dollar"></i></div>
                                        <div>
                                            <div class="fw-800 text-dark small mb-1">Laporan Nilai Aset Stok (Bulanan)
                                            </div>
                                            <p class="text-muted small mb-0">Gunakan laporan valuasi untuk mengetahui
                                                total nilai stok gudang saat ini berdasarkan harga beli terakhir.
                                                Berguna untuk neraca keuangan dan laporan manajemen.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 5: TROUBLESHOOT -->
                            <div class="tab-pane fade p-5" id="wh-troubleshoot">
                                <h4 class="fw-900 text-dark mb-2">🔧 Troubleshoot & Kontak Bantuan</h4>
                                <p class="text-muted small mb-4">Solusi cepat untuk masalah paling umum yang ditemui di
                                    lapangan.</p>

                                <div class="d-flex flex-column gap-3 mb-4">
                                    <div class="p-4 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="fw-900 text-dark small mb-2">❓ Stok di sistem tidak cocok dengan
                                            fisik di rak</div>
                                        <p class="text-muted small mb-0">Lakukan <strong>Stock Opname</strong> untuk
                                            rekonsiliasi. Gunakan menu Ledger untuk menelusuri transaksi mana yang
                                            menyebabkan selisih sebelum sinkronisasi.</p>
                                    </div>
                                    <div class="p-4 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="fw-900 text-dark small mb-2">❓ Stok muncul minus / negatif</div>
                                        <p class="text-muted small mb-0">Ada Dispatch yang lebih besar dari stok.
                                            Kemungkinan: ada pembelian yang belum diinput, atau ada Dispatch ganda.
                                            Telusuri via Ledger → hubungi Admin untuk koreksi.</p>
                                    </div>
                                    <div class="p-4 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="fw-900 text-dark small mb-2">❓ Data tidak tersimpan / error saat
                                            submit</div>
                                        <p class="text-muted small mb-0">Pastikan semua field wajib sudah diisi. Coba
                                            refresh halaman (F5). Jika masih gagal, screenshot error dan kirim ke IT
                                            Support TGM melalui grup koordinasi.</p>
                                    </div>
                                    <div class="p-4 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="fw-900 text-dark small mb-2">❓ Ingin batalkan transaksi yang sudah
                                            tersimpan</div>
                                        <p class="text-muted small mb-0">Pembatalan hanya bisa dilakukan oleh
                                            <strong>Admin/Supervisor</strong> melalui menu Riwayat Transaksi. Operator
                                            tidak bisa membatalkan sendiri untuk menjaga integritas data.</p>
                                    </div>
                                </div>

                                <div class="p-4 rounded-4" style="background: #1e293b; color: white;">
                                    <h6 class="fw-900 mb-2" style="color: #fbbf24;"><i
                                            class="fas fa-headset me-2"></i>Hubungi IT Support TGM</h6>
                                    <p class="small mb-0 opacity-75">Jika masalah tidak bisa diselesaikan sendiri dan
                                        mempengaruhi operasional, segera hubungi Admin Sistem / IT Support TGM melalui
                                        <strong>grup WhatsApp koordinasi internal</strong> atau buat tiket support.
                                        Sertakan: nama halaman, screenshot error, dan langkah yang sudah dilakukan.</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 d-flex justify-content-between align-items-center"
                style="background: #f8fafc;">
                <div class="small text-muted"><i class="fas fa-info-circle me-1 text-primary"></i> Panduan ini berlaku
                    untuk semua operator dan management gudang TGM.</div>
                <button type="button" class="btn btn-dark px-5 py-2 fw-900 rounded-pill shadow-sm"
                    data-bs-dismiss="modal" style="background: #1e293b; border: none;">
                    <i class="fas fa-check me-2"></i>SAYA MENGERTI, TUTUP
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    #sop-tabs-warehouse .nav-link {
        color: #64748b;
        background: white;
        text-align: left !important;
        border-radius: 12px;
        transition: all 0.2s ease;
        border: 1px solid #f1f5f9;
        margin-bottom: 5px;
    }

    #sop-tabs-warehouse .nav-link:hover {
        background: #f8fafc;
        color: #1a1c23;
        transform: translateX(5px);
    }

    #sop-tabs-warehouse .nav-link.active {
        background: #1e293b;
        color: white;
        border-color: #1e293b;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
</style>