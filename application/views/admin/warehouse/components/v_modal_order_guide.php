<!-- ================= SOP RENCANA BELANJA / PURCHASE ORDER ================= -->
<div class="modal fade" id="modalOrderGuide" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 28px;">
            <div class="modal-header border-0 py-4 px-4"
                style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%)">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center"
                        style="width: 42px; height: 42px; background: #10b981;">
                        <i class="fas fa-shopping-cart text-white fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-900 mb-0">SOP RENCANA BELANJA MATERIAL</h5>
                        <small class="fw-bold" style="color: #6ee7b7; opacity: 0.9;">Asisten cerdas deteksi stok kritis
                            & rekomendasi pembelian otomatis</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-3 border-end bg-light bg-opacity-50">
                        <div class="nav flex-column nav-pills p-3 gap-2" id="sop-tabs-order" role="tablist">
                            <button
                                class="nav-link active text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#ord-concept" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-brain text-primary" style="width: 20px;"></i> Cara Kerja Sistem
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#ord-priority" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-layer-group text-danger" style="width: 20px;"></i> Skala Prioritas
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#ord-steps" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-tasks text-success" style="width: 20px;"></i> Alur Pemesanan
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#ord-minstock" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-sliders-h text-warning" style="width: 20px;"></i> Atur Stok Minimal
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#ord-faq" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-question-circle text-info" style="width: 20px;"></i> FAQ
                            </button>
                        </div>
                    </div>
                    <div class="col-md-9 px-0 bg-white">
                        <div class="tab-content h-100">

                            <!-- TAB 1: CARA KERJA SISTEM -->
                            <div class="tab-pane fade show active p-5" id="ord-concept">
                                <h4 class="fw-900 text-dark mb-2">🧠 Cara Kerja Sistem Rencana Belanja</h4>
                                <p class="text-muted small mb-4">Halaman ini bukan daftar stok biasa. Ini adalah
                                    <strong>asisten cerdas</strong> yang secara otomatis mendeteksi bahan yang perlu
                                    segera dibeli berdasarkan kondisi stok saat ini.</p>

                                <div class="p-4 rounded-4 mb-4" style="background: #1e293b; color: white;">
                                    <h6 class="fw-900 mb-3" style="color: #6ee7b7;"><i
                                            class="fas fa-magic me-2"></i>Logika Otomatis Sistem</h6>
                                    <div class="row g-3 align-items-center text-center small">
                                        <div class="col">
                                            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.1);">
                                                <i class="fas fa-boxes fa-2x mb-2 text-info d-block"></i>
                                                <div class="fw-900">Stok Aktual</div>
                                                <div class="opacity-70 small">Cek kondisi stok saat ini</div>
                                            </div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-arrow-right text-warning fa-lg"></i>
                                        </div>
                                        <div class="col">
                                            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.1);">
                                                <i class="fas fa-less-than fa-2x mb-2 text-warning d-block"></i>
                                                <div class="fw-900">Lebih kecil dari</div>
                                                <div class="opacity-70 small">Perbandingan otomatis</div>
                                            </div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-arrow-right text-warning fa-lg"></i>
                                        </div>
                                        <div class="col">
                                            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.1);">
                                                <i class="fas fa-shield-alt fa-2x mb-2 text-success d-block"></i>
                                                <div class="fw-900">Stok Minimal</div>
                                                <div class="opacity-70 small">Batas amanmu per bahan</div>
                                            </div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-arrow-right text-warning fa-lg"></i>
                                        </div>
                                        <div class="col">
                                            <div class="p-3 rounded-3"
                                                style="background: rgba(17, 255, 120, 0.15); border: 1px solid #10b981;">
                                                <i class="fas fa-exclamation-circle fa-2x mb-2 text-yellow-300 d-block"
                                                    style="color: #fbbf24;"></i>
                                                <div class="fw-900" style="color: #fbbf24;">MUNCUL di Sini!</div>
                                                <div class="opacity-70 small">Perlu segera dibeli</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 h-100"
                                            style="background: #f0fdf4; border: 2px solid #22c55e;">
                                            <h6 class="fw-900 text-success mb-3"><i
                                                    class="fas fa-check-circle me-2"></i>Manfaat Utama</h6>
                                            <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-dot-circle text-success mt-1 flex-shrink-0"></i>
                                                    <div>Tidak perlu cek stok satu per satu secara manual setiap hari
                                                    </div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-dot-circle text-success mt-1 flex-shrink-0"></i>
                                                    <div>Daftar belanja muncul otomatis berdasarkan data real-time</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-dot-circle text-success mt-1 flex-shrink-0"></i>
                                                    <div>Tim purchasing bisa langsung tindak lanjut tanpa perlu bertanya
                                                        ke gudang</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-dot-circle text-success mt-1 flex-shrink-0"></i>
                                                    <div>Produksi tidak terhenti karena kehabisan bahan yang tidak
                                                        terpantau</div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 h-100"
                                            style="background: #fffbeb; border: 2px solid #f59e0b;">
                                            <h6 class="fw-900 mb-3" style="color: #92400e;"><i
                                                    class="fas fa-lightbulb me-2"></i>Kebiasaan Terbaik</h6>
                                            <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-clock text-warning mt-1 flex-shrink-0"></i>
                                                    <div><strong>Buka halaman ini setiap pagi</strong> — sebelum memulai
                                                        aktivitas produksi</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-clock text-warning mt-1 flex-shrink-0"></i>
                                                    <div><strong>Koordinasikan dengan purchasing</strong> jika ada item
                                                        berstatus HABIS pagi ini</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-clock text-warning mt-1 flex-shrink-0"></i>
                                                    <div><strong>Update Stok Minimal</strong> secara berkala agar
                                                        rekomendasi selalu relevan</div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 2: SKALA PRIORITAS -->
                            <div class="tab-pane fade p-5" id="ord-priority">
                                <h4 class="fw-900 text-dark mb-2">🚨 Skala Prioritas Pembelian</h4>
                                <p class="text-muted small mb-4">Jika anggaran terbatas dan tidak bisa membeli semua
                                    barang sekaligus, gunakan skala prioritas ini untuk menentukan urutan pembelian yang
                                    paling mendesak.</p>

                                <div class="d-flex flex-column gap-4 mb-4">
                                    <!-- PRIORITAS 1 -->
                                    <div class="p-4 rounded-4" style="background: #fef2f2; border: 3px solid #ef4444;">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <div class="bg-danger rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                                style="width: 44px; height: 44px; font-size: 1.2rem;">P1</div>
                                            <div>
                                                <div class="fw-900 text-danger" style="font-size: 1rem;">HABIS TOTAL —
                                                    Beli Hari Ini Juga!</div>
                                                <div class="small text-muted">Stok = 0 atau minus (negatif)</div>
                                            </div>
                                            <span class="badge bg-danger ms-auto px-3 py-2 fw-900">KRITIS</span>
                                        </div>
                                        <p class="small text-muted mb-2">Material ini sudah <strong>benar-benar
                                                kosong</strong>. Setiap menit tanpa stok = potensi terhentinya proses
                                            produksi yang membutuhkan bahan ini.</p>
                                        <div class="d-flex gap-2 align-items-center small">
                                            <i class="fas fa-bolt text-danger flex-shrink-0"></i>
                                            <div><strong>Tindakan:</strong> Hubungi supplier sekarang, kalau perlu beli
                                                COD/darurat. Informasikan ke supervisor dan produksi tentang kondisi
                                                ini.</div>
                                        </div>
                                    </div>

                                    <!-- PRIORITAS 2 -->
                                    <div class="p-4 rounded-4" style="background: #fffbeb; border: 3px solid #f59e0b;">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <div class="rounded-circle text-dark fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                                style="width: 44px; height: 44px; font-size: 1.2rem; background: #f59e0b;">
                                                P2</div>
                                            <div>
                                                <div class="fw-900" style="font-size: 1rem; color: #92400e;">LIMIT
                                                    KRITIS — Pesan Segera (Maks 1–2 Hari)</div>
                                                <div class="small text-muted">Stok ≤ Batas Minimal, masih ada sisa</div>
                                            </div>
                                            <span class="badge ms-auto px-3 py-2 fw-900"
                                                style="background: #f59e0b; color: #1e293b;">SEGERA</span>
                                        </div>
                                        <p class="small text-muted mb-2">Material masih ada, tapi sudah memasuki zona
                                            merah. Tanpa pembelian segera, barang ini bisa habis dalam <strong>1–2 hari
                                                kerja</strong> tergantung volume produksi.</p>
                                        <div class="d-flex gap-2 align-items-center small">
                                            <i class="fas fa-clock" style="color: #d97706; flex-shrink: 0;"></i>
                                            <div><strong>Tindakan:</strong> Buat Purchase Order hari ini. Coordinate
                                                dengan supplier untuk jadwal pengiriman tercepat yang memungkinkan.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 rounded-4" style="background: #f0fdf4; border: 2px solid #22c55e;">
                                    <h6 class="fw-900 text-success mb-2"><i
                                            class="fas fa-check-circle me-2"></i>Material TIDAK Muncul di Sini = Stok
                                        Masih Aman</h6>
                                    <p class="small text-muted mb-0">Jika sebuah material tidak muncul dalam daftar
                                        Rencana Belanja, artinya stoknya masih di atas batas minimal. <strong>Tidak
                                            perlu membeli saat ini.</strong> Fokuskan sumber daya pada item yang memang
                                        sudah muncul di daftar.</p>
                                </div>
                            </div>

                            <!-- TAB 3: ALUR PEMESANAN -->
                            <div class="tab-pane fade p-5" id="ord-steps">
                                <h4 class="fw-900 text-dark mb-2">🛒 Alur Pemesanan dari Rencana Belanja ke Pembelian
                                </h4>
                                <p class="text-muted small mb-4">Gunakan tombol <strong>"Buat Pembelian"</strong> di
                                    setiap baris item untuk langsung melanjutkan ke form transaksi pembelian. Proses ini
                                    dirancang semulus mungkin.</p>

                                <div class="d-flex flex-column gap-3 mb-4">
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-start"
                                        style="background: #eff6ff; border: 2px solid #3b82f6;">
                                        <div class="bg-primary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 40px; height: 40px;">1</div>
                                        <div>
                                            <div class="fw-900 text-dark mb-1">Review Daftar Rekomendasi</div>
                                            <p class="small text-muted mb-0">Baca daftar semua material yang muncul.
                                                Perhatikan badge status (HABIS / LIMIT) dan angka kekurangan stok.
                                                Sortir dari yang paling kritis menggunakan kolom STATUS.</p>
                                        </div>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-start"
                                        style="background: #eff6ff; border: 2px solid #3b82f6;">
                                        <div class="bg-primary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 40px; height: 40px;">2</div>
                                        <div>
                                            <div class="fw-900 text-dark mb-1">Hubungi Supplier & Konfirmasi Harga</div>
                                            <p class="small text-muted mb-0">Sebelum input ke sistem, konfirmasi
                                                ketersediaan dan harga terbaru ke supplier. Harga yang diinput ke sistem
                                                akan digunakan sebagai HPP (Harga Pokok Pembelian) stok.</p>
                                        </div>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-start"
                                        style="background: #f0fdf4; border: 2px solid #22c55e;">
                                        <div class="bg-success rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 40px; height: 40px;">3</div>
                                        <div>
                                            <div class="fw-900 text-dark mb-1">Klik "Buat Pembelian" pada Item</div>
                                            <p class="small text-muted mb-0">Klik tombol di baris material yang akan
                                                dibeli. Sistem akan <strong>otomatis membuka form Purchase</strong>
                                                dengan bahan tersebut sudah terisi. Anda tinggal mengisi QTY, harga,
                                                supplier, dan nomor faktur.</p>
                                        </div>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-start"
                                        style="background: #f0fdf4; border: 2px solid #22c55e;">
                                        <div class="bg-success rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 40px; height: 40px;">4</div>
                                        <div>
                                            <div class="fw-900 text-dark mb-1">Selesaikan Transaksi Purchase</div>
                                            <p class="small text-muted mb-0">Lengkapi form Purchase, simpan. Stok akan
                                                bertambah otomatis dan material tersebut akan <strong>menghilang dari
                                                    daftar Rencana Belanja</strong> begitu stoknya kembali di atas batas
                                                minimal.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-3 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <p class="small mb-0 fw-700 text-muted"><i
                                            class="fas fa-tag text-primary me-2"></i><strong>Tips:</strong> Gunakan
                                        kolom <strong>Catatan</strong> di form Purchase untuk mencantumkan nomor SPK
                                        atau batch produksi yang membutuhkan bahan ini. Memudahkan tracing pemakaian
                                        bahan per proyek.</p>
                                </div>
                            </div>

                            <!-- TAB 4: ATUR STOK MINIMAL -->
                            <div class="tab-pane fade p-5" id="ord-minstock">
                                <h4 class="fw-900 text-dark mb-2">⚙️ Cara Mengatur Stok Minimal</h4>
                                <p class="text-muted small mb-4">Stok Minimal adalah angka pemicu (trigger) yang
                                    menentukan kapan sebuah bahan masuk ke daftar Rencana Belanja. Angka ini
                                    <strong>wajib diisi dengan cermat</strong> untuk setiap bahan.</p>

                                <div class="p-4 rounded-4 mb-4 d-flex align-items-start gap-3"
                                    style="background: #eff6ff; border: 2px solid #3b82f6;">
                                    <i class="fas fa-map-marker-alt text-primary fs-4 flex-shrink-0 mt-1"></i>
                                    <div>
                                        <h6 class="fw-900 text-primary mb-1">Cara Mengubah Stok Minimal</h6>
                                        <p class="small text-muted mb-0">Buka halaman <strong>Monitoring Stok</strong> →
                                            klik tombol <strong>Edit</strong> pada baris bahan yang ingin diubah → isi
                                            kolom <em>"Min. Stok"</em> → Simpan. Perubahan langsung berlaku dan
                                            mempengaruhi tampilan Rencana Belanja.</p>
                                    </div>
                                </div>

                                <h6 class="fw-900 text-dark mb-3">📐 Cara Menghitung Stok Minimal yang Tepat</h6>
                                <div class="p-4 rounded-4 mb-4" style="background: #1e293b; color: white;">
                                    <div class="text-center py-3">
                                        <div class="d-inline-block px-5 py-3 rounded-3"
                                            style="background: rgba(255,255,255,0.1); font-size: 1rem;">
                                            <span class="fw-900 text-info">Min. Stok</span>
                                            <span class="mx-3 text-white opacity-50">=</span>
                                            <span class="fw-900 text-warning">Konsumsi Per Hari</span>
                                            <span class="mx-2 text-white opacity-50">×</span>
                                            <span class="fw-900 text-success">Lead Time (Hari)</span>
                                        </div>
                                    </div>
                                    <p class="small text-white opacity-75 mb-0 text-center mt-2">Lead Time = jumlah hari
                                        rata-rata dari pesan sampai barang datang dari supplier</p>
                                </div>

                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 h-100"
                                            style="background: #f0fdf4; border: 2px solid #22c55e;">
                                            <h6 class="fw-900 text-success mb-3">✅ Contoh Perhitungan Benar</h6>
                                            <div class="small text-muted d-flex flex-column gap-1">
                                                <div><strong>Art Paper 150gr A3+</strong></div>
                                                <div>Konsumsi: ±200 lembar/hari</div>
                                                <div>Lead time supplier: 3 hari</div>
                                                <div>Safety buffer: +1 hari</div>
                                                <hr class="my-2">
                                                <div class="fw-900 text-success">Min. Stok = 200 × (3+1) = <u>800
                                                        lembar</u></div>
                                                <div class="text-muted">Saat stok ≤ 800 lbr → muncul di Rencana Belanja
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 h-100"
                                            style="background: #fef2f2; border: 2px solid #ef4444;">
                                            <h6 class="fw-900 text-danger mb-3">❌ Contoh yang Salah</h6>
                                            <div class="small text-muted d-flex flex-column gap-1">
                                                <div><strong>Min. Stok = 0</strong> (tidak diisi)</div>
                                                <div class="text-danger">→ Bahan tidak pernah muncul di Rencana Belanja
                                                    sampai benar-benar habis</div>
                                                <hr class="my-2">
                                                <div><strong>Min. Stok = 10.000 lbr</strong> (terlalu tinggi)</div>
                                                <div class="text-danger">→ Bahan selalu muncul di Rencana Belanja
                                                    meskipun stok masih sangat banyak → alarm fatigue</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 5: FAQ -->
                            <div class="tab-pane fade p-5" id="ord-faq">
                                <h4 class="fw-900 text-dark mb-2">❓ FAQ — Pertanyaan Umum</h4>
                                <p class="text-muted small mb-4">Jawaban untuk pertanyaan yang paling sering muncul dari
                                    tim purchasing dan operator gudang.</p>

                                <div class="d-flex flex-column gap-3">
                                    <div class="p-4 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <h6 class="fw-900 text-dark mb-2">Barang sudah dibeli tapi masih muncul di sini
                                            — kenapa?</h6>
                                        <p class="small text-muted mb-0">Barang baru menghilang dari daftar setelah
                                            <strong>transaksi Purchase sudah disimpan (Finalize)</strong> dan stok di
                                            sistem bertambah melewati batas minimal. Jika pembelian sudah dilakukan tapi
                                            belum diinput ke sistem, barang tetap muncul karena sistem belum tahu ada
                                            pembelian.</p>
                                    </div>
                                    <div class="p-4 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <h6 class="fw-900 text-dark mb-2">Sistem bilang stok masih aman, tapi di rak
                                            sudah terlihat sedikit — kenapa?</h6>
                                        <p class="small text-muted mb-0">Ada dua kemungkinan: (1) Angka <strong>Stok
                                                Minimal terlalu rendah</strong> — perlu dinaikkan. (2) Ada
                                            <strong>pengambilan barang yang belum di-Dispatch</strong> ke sistem — stok
                                            fisik berkurang tapi sistem belum tahu. Lakukan Dispatch yang tertunda lalu
                                            review ulang angka Stok Minimal.</p>
                                    </div>
                                    <div class="p-4 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <h6 class="fw-900 text-dark mb-2">Apakah saya bisa input pembelian langsung
                                            tanpa lewat halaman ini?</h6>
                                        <p class="small text-muted mb-0">Ya. Halaman Rencana Belanja hanya alat bantu
                                            untuk mengetahui <em>apa yang perlu dibeli</em>. Transaksi Purchase tetap
                                            bisa dilakukan langsung dari menu <strong>Pembelian</strong> tanpa harus
                                            melalui halaman ini. Namun, melewati halaman ini sangat dianjurkan agar
                                            tidak ada item kritis yang terlewat.</p>
                                    </div>
                                    <div class="p-4 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <h6 class="fw-900 text-dark mb-2">Daftar terlalu panjang — ada terlalu banyak
                                            barang yang perlu dibeli. Apa yang salah?</h6>
                                        <p class="small text-muted mb-0">Kemungkinan: (1) Banyak Dispatch yang belum
                                            diinput sehingga stok sistem lebih tinggi dari fisik. (2) Angka Stok Minimal
                                            di banyak bahan terlalu tinggi sehingga terlalu mudah terpicu. (3) Memang
                                            sedang dalam periode permintaan produksi tinggi. Lakukan Opname untuk
                                            rekalibrasi data stok.</p>
                                    </div>
                                    <div class="p-4 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <h6 class="fw-900 text-dark mb-2">Bisakah mengekspor / mencetak daftar Rencana
                                            Belanja untuk diberikan ke supplier?</h6>
                                        <p class="small text-muted mb-0">Gunakan tombol <strong>Export / Cetak</strong>
                                            yang tersedia di sudut kanan atas halaman untuk mengunduh daftar dalam
                                            format PDF atau Excel. Berguna untuk dikirim ke supplier sebagai Purchase
                                            Order awal.</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 d-flex justify-content-between align-items-center"
                style="background: #f8fafc;">
                <div class="small text-muted"><i class="fas fa-info-circle me-1 text-primary"></i> Cek halaman ini
                    setiap pagi sebelum memulai produksi.</div>
                <button type="button" class="btn btn-dark px-5 py-2 fw-900 rounded-pill shadow-sm"
                    data-bs-dismiss="modal" style="background: #1e293b; border: none;">
                    <i class="fas fa-check me-2"></i>SAYA MENGERTI, LANJUTKAN
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    #sop-tabs-order .nav-link {
        color: #64748b;
        background: white;
        text-align: left !important;
        border-radius: 12px;
        transition: all 0.2s ease;
        border: 1px solid #f1f5f9;
        margin-bottom: 5px;
    }

    #sop-tabs-order .nav-link:hover {
        background: #f8fafc;
        color: #1a1c23;
        transform: translateX(5px);
    }

    #sop-tabs-order .nav-link.active {
        background: #1e293b;
        color: white;
        border-color: #1e293b;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
</style>