<!-- ================= SOP DISPATCH / DISTRIBUSI BARANG (LANGKAH 5 dari 6) ================= -->
<div class="modal fade" id="modalDispatchGuide" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 28px;">
            <div class="modal-header border-0 py-4 px-4"
                style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%)">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center"
                        style="width: 42px; height: 42px; background: #8b5cf6;">
                        <i class="fas fa-truck-loading text-white fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-900 mb-0">SOP LANGKAH 5: DISPATCH — DISTRIBUSI KE PRODUKSI
                        </h5>
                        <small class="fw-bold" style="color: #c4b5fd; opacity: 0.9;">Setiap barang keluar dari gudang ke
                            produksi WAJIB melalui proses ini</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-3 border-end bg-light bg-opacity-50">
                        <div class="nav flex-column nav-pills p-3 gap-2" id="sop-tabs-dispatch" role="tablist">
                            <button
                                class="nav-link active text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#dis-workflow" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-project-diagram text-primary" style="width: 20px;"></i> Posisi Langkah
                                5
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#dis-overview" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-random" style="width: 20px; color: #8b5cf6;"></i> Konsep Dispatch
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#dis-staging" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-boxes text-success" style="width: 20px;"></i> Staging & Picklist
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#dis-launch" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-paper-plane text-warning" style="width: 20px;"></i> Finalisasi & Kirim
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#dis-safety" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-shield-alt text-danger" style="width: 20px;"></i> Safety & FAQ
                            </button>
                        </div>
                    </div>
                    <div class="col-md-9 px-0 bg-white">
                        <div class="tab-content h-100">

                            <!-- TAB 1: POSISI LANGKAH 5 -->
                            <div class="tab-pane fade show active p-5" id="dis-workflow">
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <span class="badge px-3 py-2 rounded-pill fw-800"
                                        style="font-size: 0.8rem; background: #8b5cf6;">LANGKAH 5 dari 6</span>
                                    <h4 class="fw-900 text-dark mb-0">Posisi Anda dalam Alur Kerja Gudang</h4>
                                </div>
                                <div class="p-4 rounded-4 mb-4 d-flex align-items-center gap-3"
                                    style="background: #f5f3ff; border: 2px solid #8b5cf6;">
                                    <div class="flex-shrink-0" style="color: #8b5cf6; font-size: 2rem;"><i
                                            class="fas fa-truck-loading"></i></div>
                                    <div>
                                        <h6 class="fw-900 mb-1" style="color: #5b21b6;">🚚 HALAMAN INI UNTUK
                                            MENGELUARKAN BARANG DARI GUDANG KE PRODUKSI</h6>
                                        <p class="small mb-0 text-muted">Setiap kali operator produksi mengambil bahan
                                            dari rak gudang untuk digunakan dalam proses produksi, <strong>wajib dicatat
                                                di sini</strong>. Transaksi ini akan langsung <u>mengurangi stok</u> di
                                            gudang sumber.</p>
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-2">
                                    <div class="p-3 rounded-3 d-flex align-items-center gap-3 bg-light border">
                                        <div class="bg-secondary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 36px; height: 36px;">1-3</div>
                                        <div>
                                            <div class="fw-800 small">Setup Awal (Registrasi, Varian, Opname)</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">Sudah selesai — data
                                                bahan dan stok awal sudah ada di sistem</div>
                                        </div>
                                        <span class="badge bg-success ms-auto flex-shrink-0 px-3">✓ SELESAI</span>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-center gap-3 bg-light border">
                                        <div class="bg-secondary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 36px; height: 36px;">4</div>
                                        <div>
                                            <div class="fw-800 small">Pembelian — Stok Masuk dari Supplier</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">Setiap pembelian sudah
                                                dicatat dan stok bertambah</div>
                                        </div>
                                        <span class="badge bg-success ms-auto flex-shrink-0 px-3">✓ AKTIF</span>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-center gap-3"
                                        style="background: #1e293b; border: 2px solid #6366f1;">
                                        <div class="rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 36px; height: 36px; background: #8b5cf6;">5</div>
                                        <div class="text-white">
                                            <div class="fw-900 small">LANGKAH INI — Dispatch ke Produksi</div>
                                            <div style="font-size: 0.7rem; opacity: 0.7;">Keluarkan bahan dari gudang →
                                                stok berkurang → produksi bisa berjalan</div>
                                        </div>
                                        <span class="badge ms-auto flex-shrink-0 px-3"
                                            style="background: #8b5cf6;">AKTIF</span>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-center gap-3 bg-light border">
                                        <div class="bg-secondary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 36px; height: 36px;">6</div>
                                        <div>
                                            <div class="fw-800 small">Monitoring Stok Harian</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">Pantau live stok setelah
                                                dispatch berjalan</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 2: KONSEP DISPATCH -->
                            <div class="tab-pane fade p-5" id="dis-overview">
                                <h4 class="fw-900 text-dark mb-2">📦 Apa itu Dispatch?</h4>
                                <p class="text-muted small mb-4">Dispatch adalah proses <strong>transfer
                                        internal</strong> — memindahkan bahan dari gudang utama ke area produksi atau
                                    lokasi lain secara tercatat dalam sistem.</p>

                                <div class="p-4 rounded-4 mb-4 text-center"
                                    style="background: #f5f3ff; border: 2px solid #8b5cf6;">
                                    <div class="row align-items-center g-3">
                                        <div class="col">
                                            <div class="p-3 rounded-3 bg-white shadow-sm">
                                                <i class="fas fa-warehouse text-primary fs-3 d-block mb-2"></i>
                                                <div class="fw-900 text-dark small">GUDANG UTAMA</div>
                                                <div class="small text-muted">Sumber Stok | Stok BERKURANG</div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-arrow-right fa-2x" style="color: #8b5cf6;"></i>
                                        </div>
                                        <div class="col">
                                            <div class="p-3 rounded-3 bg-white shadow-sm">
                                                <i class="fas fa-industry fs-3 d-block mb-2"
                                                    style="color: #8b5cf6;"></i>
                                                <div class="fw-900 text-dark small">AREA PRODUKSI / WIP</div>
                                                <div class="small text-muted">Lokasi Konsumsi | Stok BERTAMBAH</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 rounded-4 mb-3" style="background: #fef2f2; border: 2px solid #ef4444;">
                                    <h6 class="fw-900 text-danger mb-2"><i
                                            class="fas fa-exclamation-triangle me-2"></i>ATURAN KRITIS</h6>
                                    <p class="small text-muted mb-0">Setiap bahan yang diambil dari rak gudang untuk
                                        digunakan produksi, sekecil apapun jumlahnya, <strong>wajib melalui
                                            Dispatch</strong>. Pengambilan tanpa Dispatch = stok sistem tidak akurat =
                                        laporan monitoring tidak bisa dipercaya.</p>
                                </div>

                                <div class="p-3 rounded-4" style="background: #f0fdf4; border: 1px solid #86efac;">
                                    <p class="small mb-0 fw-700 text-success"><i
                                            class="fas fa-shield-alt me-2"></i><strong>Perlindungan Sistem:</strong>
                                        Sistem <strong>tidak mengizinkan</strong> Dispatch melebihi stok yang tersedia.
                                        Jika stok tidak cukup, tombol Launch akan terblokir dan sistem akan menampilkan
                                        pesan error.</p>
                                </div>
                            </div>

                            <!-- TAB 3: STAGING & PICKLIST -->
                            <div class="tab-pane fade p-5" id="dis-staging">
                                <h4 class="fw-900 text-dark mb-2">📋 Staging & Picklist</h4>
                                <p class="text-muted small mb-4">Sebelum mengirim, buat daftar barang yang akan
                                    dikeluarkan (Picklist). Tahap ini memastikan tidak ada item yang terlewat.</p>
                                <div class="d-flex flex-column gap-3 mb-4">
                                    <div class="p-3 rounded-3 d-flex align-items-start gap-3"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 32px; height: 32px; font-size: 0.85rem; background: #8b5cf6;">
                                            1</div>
                                        <div class="small"><strong>Klik "Stage Material"</strong> — cari dan pilih bahan
                                            yang akan dikeluarkan. Sistem akan menampilkan stok sisa yang tersedia
                                            sebagai panduan.</div>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-start gap-3"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 32px; height: 32px; font-size: 0.85rem; background: #8b5cf6;">
                                            2</div>
                                        <div class="small"><strong>Isi Jumlah QTY</strong> — masukkan jumlah yang akan
                                            diambil. Jangan melebihi stok yang tersedia di sistem.</div>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-start gap-3"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 32px; height: 32px; font-size: 0.85rem; background: #8b5cf6;">
                                            3</div>
                                        <div class="small"><strong>Isi Keterangan (Opsional tapi Dianjurkan)</strong> —
                                            isi untuk SPK/Proyek terkait agar mutasi bisa dilacak dengan mudah. Contoh:
                                            "SPK-2024-012" atau "Pesanan Pak Budi".</div>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-start gap-3"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 32px; height: 32px; font-size: 0.85rem; background: #8b5cf6;">
                                            4</div>
                                        <div class="small"><strong>Ulangi untuk semua item</strong> dalam satu sesi
                                            pengambilan. Semua item dikumpulkan terlebih dahulu sebelum di-launch.</div>
                                    </div>
                                </div>
                                <div class="p-3 rounded-4" style="background: #fffbeb; border: 1px solid #fcd34d;">
                                    <p class="small mb-0 fw-700" style="color: #92400e;"><i
                                            class="fas fa-lightbulb me-2 text-warning"></i>Isi keterangan dengan
                                        referensi SPK agar tim admin bisa melacak penggunaan bahan per proyek dari
                                        laporan Ledger/Mutasi Stok.</p>
                                </div>
                            </div>

                            <!-- TAB 4: FINALISASI & KIRIM -->
                            <div class="tab-pane fade p-5" id="dis-launch">
                                <h4 class="fw-900 text-dark mb-2">🚀 Finalisasi & Launch Dispatch</h4>
                                <p class="text-muted small mb-4">Setelah semua item masuk Picklist, lakukan finalisasi
                                    untuk menyelesaikan proses pengiriman.</p>
                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 h-100"
                                            style="background: #eff6ff; border: 2px solid #3b82f6;">
                                            <h6 class="fw-900 text-primary mb-3"><i
                                                    class="fas fa-map-marker-alt me-2"></i>1. Pilih Gudang/Lokasi Tujuan
                                            </h6>
                                            <p class="small text-muted mb-0">Pilih lokasi tujuan (Area Produksi, WIP,
                                                Cabang, dll). Stok di lokasi tujuan akan bertambah setelah dispatch
                                                berhasil. Pilih dengan tepat agar tidak ada stok "nongkrong" di lokasi
                                                yang salah.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 h-100"
                                            style="background: #f5f3ff; border: 2px solid #8b5cf6;">
                                            <h6 class="fw-900 mb-3" style="color: #5b21b6;"><i
                                                    class="fas fa-paper-plane me-2"></i>2. Klik "Launch Distribution"
                                            </h6>
                                            <p class="small text-muted mb-0">Sistem akan memvalidasi ulang stok sebelum
                                                benar-benar memotong saldo gudang sumber. Jika validasi gagal (stok
                                                kurang), sistem akan menampilkan peringatan dan tidak melanjutkan
                                                proses.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 rounded-4 mb-3" style="background: #fef2f2; border: 2px solid #ef4444;">
                                    <h6 class="fw-900 text-danger mb-2"><i
                                            class="fas fa-exclamation-triangle me-2"></i>DISPATCH TIDAK BISA DIBATALKAN
                                        OLEH OPERATOR</h6>
                                    <p class="small text-muted mb-0">Setelah Launch berhasil, stok langsung berkurang di
                                        sistem. Pembatalan hanya bisa dilakukan oleh <strong>Admin/Supervisor</strong>
                                        melalui menu khusus. Pastikan semua data Picklist sudah benar sebelum Launch.
                                    </p>
                                </div>

                                <div class="p-3 rounded-4" style="background: #f0fdf4; border: 1px solid #86efac;">
                                    <h6 class="fw-900 text-success mb-1"><i class="fas fa-history me-2"></i>Monitor
                                        Panel "Recent Dispatches"</h6>
                                    <p class="small text-muted mb-0">Lihat panel kanan bawah untuk memastikan transaksi
                                        berhasil diproses dan tercatat dengan benar. Jika tidak muncul, muat ulang
                                        halaman atau hubungi Admin.</p>
                                </div>
                            </div>

                            <!-- TAB 5: SAFETY & FAQ -->
                            <div class="tab-pane fade p-5" id="dis-safety">
                                <h4 class="fw-900 text-dark mb-2">🛡️ Safety Check & FAQ</h4>
                                <p class="text-muted small mb-4">Jawaban untuk pertanyaan paling umum yang dihadapi
                                    operator saat menggunakan modul Dispatch.</p>
                                <div class="d-flex flex-column gap-3">
                                    <div class="p-4 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <h6 class="fw-900 text-dark mb-2">❓ Mengapa tombol "Launch" tidak bisa diklik?
                                        </h6>
                                        <p class="small text-muted mb-0">Tombol hanya aktif jika: (1) minimal ada satu
                                            item di Picklist, dan (2) sudah memilih Gudang/Lokasi Tujuan. Pastikan kedua
                                            kondisi ini terpenuhi.</p>
                                    </div>
                                    <div class="p-4 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <h6 class="fw-900 text-dark mb-2">❓ Stok fisik kurang dari sistem — bagaimana?
                                        </h6>
                                        <p class="small text-muted mb-0">Jangan lakukan Dispatch melebihi stok sistem.
                                            Lakukan <strong>Stock Opname</strong> terlebih dahulu untuk merekonsiliasi
                                            stok. Sistem tidak mengizinkan pengeluaran barang melebihi catatan untuk
                                            menjaga integritas data.</p>
                                    </div>
                                    <div class="p-4 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <h6 class="fw-900 text-dark mb-2">❓ Salah input QTY — sudah terlanjur Launch.
                                            Apa yang harus dilakukan?</h6>
                                        <p class="small text-muted mb-0">Segera hubungi
                                            <strong>Admin/Supervisor</strong> untuk pembatalan. Jangan coba membuat
                                            Dispatch baru dengan nilai negatif atau "reverse" sendiri — ini akan merusak
                                            histori mutasi.</p>
                                    </div>
                                    <div class="p-4 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <h6 class="fw-900 text-dark mb-2">❓ Apakah bisa Dispatch ke banyak lokasi dalam
                                            satu sesi?</h6>
                                        <p class="small text-muted mb-0">Tidak. Satu sesi Dispatch hanya bisa ke
                                            <strong>satu lokasi tujuan</strong>. Jika perlu kirim ke dua lokasi berbeda,
                                            buat dua sesi Dispatch terpisah.</p>
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
    #sop-tabs-dispatch .nav-link {
        color: #64748b;
        background: white;
        text-align: left !important;
        border-radius: 12px;
        transition: all 0.2s ease;
        border: 1px solid #f1f5f9;
        margin-bottom: 5px;
    }

    #sop-tabs-dispatch .nav-link:hover {
        background: #f8fafc;
        color: #1a1c23;
        transform: translateX(5px);
    }

    #sop-tabs-dispatch .nav-link.active {
        background: #1e293b;
        color: white;
        border-color: #1e293b;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
</style>