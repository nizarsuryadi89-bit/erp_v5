<!-- ================= SOP MONITORING STOK HARIAN (LANGKAH 6 dari 6) ================= -->
<div class="modal fade" id="modalStockGuide" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 28px;">
            <div class="modal-header border-0 py-4 px-4"
                style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%)">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center"
                        style="width: 42px; height: 42px; background: #0ea5e9;">
                        <i class="fas fa-chart-line text-white fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-900 mb-0">SOP LANGKAH 6: MONITORING STOK HARIAN</h5>
                        <small class="fw-bold" style="color: #7dd3fc; opacity: 0.9;">Pusat kendali real-time inventaris
                            gudang — pantau setiap hari</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-3 border-end bg-light bg-opacity-50">
                        <div class="nav flex-column nav-pills p-3 gap-2" id="sop-tabs-stock" role="tablist">
                            <button
                                class="nav-link active text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#stk-workflow" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-project-diagram text-primary" style="width: 20px;"></i> Posisi Langkah
                                6
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#stk-overview" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-tachometer-alt text-info" style="width: 20px;"></i> Dashboard Monitor
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#stk-status" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-traffic-light text-success" style="width: 20px;"></i> Klasifikasi
                                Status
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#stk-columns" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-table text-warning" style="width: 20px;"></i> Arti Kolom Tabel
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#stk-audit" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-clipboard-check text-danger" style="width: 20px;"></i> Audit & Akurasi
                            </button>
                        </div>
                    </div>
                    <div class="col-md-9 px-0 bg-white">
                        <div class="tab-content h-100">

                            <!-- TAB 1: POSISI LANGKAH 6 -->
                            <div class="tab-pane fade show active p-5" id="stk-workflow">
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <span class="badge px-3 py-2 rounded-pill fw-800"
                                        style="font-size: 0.8rem; background: #0ea5e9;">LANGKAH 6 dari 6</span>
                                    <h4 class="fw-900 text-dark mb-0">Anda di Langkah Terakhir — Monitoring</h4>
                                </div>
                                <div class="p-4 rounded-4 mb-4 d-flex align-items-center gap-3"
                                    style="background: #f0f9ff; border: 2px solid #0ea5e9;">
                                    <div class="flex-shrink-0" style="color: #0ea5e9; font-size: 2rem;"><i
                                            class="fas fa-chart-line"></i></div>
                                    <div>
                                        <h6 class="fw-900 mb-1" style="color: #0c4a6e;">📊 HALAMAN INI ADALAH PUSAT
                                            KONTROL STOK REAL-TIME</h6>
                                        <p class="small mb-0 text-muted">Langkah 6 adalah monitoring berkelanjutan. Ini
                                            bukan setup sekali jadi — halaman ini digunakan <strong>setiap hari</strong>
                                            untuk memantau sisa stok, mendeteksi barang hampir habis, dan memastikan
                                            tidak ada stok negatif.</p>
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-2">
                                    <div class="p-3 rounded-3 d-flex align-items-center gap-3 bg-light border">
                                        <div class="bg-secondary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 36px; height: 36px; font-size: 0.75rem;">1-2</div>
                                        <div>
                                            <div class="fw-800 small">Registrasi & Varian (Setup Sekali)</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">Data master sudah ada dan
                                                tidak perlu diubah sesering ini</div>
                                        </div>
                                        <span class="badge bg-success ms-auto flex-shrink-0 px-3">✓</span>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-center gap-3 bg-light border">
                                        <div class="bg-secondary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 36px; height: 36px;">3</div>
                                        <div>
                                            <div class="fw-800 small">Opname (Berkala)</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">Dilakukan bulanan atau
                                                saat ada selisih</div>
                                        </div>
                                        <span class="badge bg-success ms-auto flex-shrink-0 px-3">✓</span>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-center gap-3 bg-light border">
                                        <div class="bg-secondary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 36px; height: 36px;">4</div>
                                        <div>
                                            <div class="fw-800 small">Pembelian (Per Transaksi)</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">Dicatat setiap ada barang
                                                masuk dari supplier</div>
                                        </div>
                                        <span class="badge bg-success ms-auto flex-shrink-0 px-3">✓</span>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-center gap-3 bg-light border">
                                        <div class="bg-secondary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 36px; height: 36px;">5</div>
                                        <div>
                                            <div class="fw-800 small">Dispatch (Harian)</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">Dicatat setiap ada bahan
                                                keluar ke produksi</div>
                                        </div>
                                        <span class="badge bg-success ms-auto flex-shrink-0 px-3">✓</span>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-center gap-3"
                                        style="background: #1e293b; border: 2px solid #6366f1;">
                                        <div class="rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 36px; height: 36px; background: #0ea5e9;">6</div>
                                        <div class="text-white">
                                            <div class="fw-900 small">LANGKAH INI — Monitoring Stok Harian</div>
                                            <div style="font-size: 0.7rem; opacity: 0.7;">Pantau setiap hari — stok
                                                update real-time setiap ada transaksi masuk/keluar</div>
                                        </div>
                                        <span class="badge ms-auto flex-shrink-0 px-3"
                                            style="background: #0ea5e9;">AKTIF</span>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 2: DASHBOARD MONITOR -->
                            <div class="tab-pane fade p-5" id="stk-overview">
                                <h4 class="fw-900 text-dark mb-2">📊 Memahami Dashboard Monitor</h4>
                                <p class="text-muted small mb-4">Tabel monitoring menampilkan kondisi stok <strong>semua
                                        bahan secara real-time</strong>. Setiap perubahan stok (masuk/keluar) akan
                                    langsung terlihat di sini.</p>
                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 h-100"
                                            style="background: #eff6ff; border: 2px solid #3b82f6;">
                                            <h6 class="fw-900 text-primary mb-3"><i
                                                    class="fas fa-search me-2"></i>Pencarian & Filter</h6>
                                            <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-dot-circle text-primary mt-1 flex-shrink-0"></i>
                                                    <div><strong>Cari Nama</strong>: Ketik nama bahan di kotak pencarian
                                                        untuk filter instan</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-dot-circle text-primary mt-1 flex-shrink-0"></i>
                                                    <div><strong>Scan Barcode</strong>: Scan barcode fisik barang →
                                                        sistem langsung menyorot barisnya</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-dot-circle text-primary mt-1 flex-shrink-0"></i>
                                                    <div><strong>Filter Gudang</strong>: Ganti dropdown lokasi untuk
                                                        melihat stok per gudang/lokasi</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-dot-circle text-primary mt-1 flex-shrink-0"></i>
                                                    <div><strong>Filter Status</strong>: Tampilkan hanya barang "HAMPIR
                                                        HABIS" atau "HABIS" untuk tindakan cepat</div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 h-100"
                                            style="background: #f0fdf4; border: 2px solid #22c55e;">
                                            <h6 class="fw-900 text-success mb-3"><i
                                                    class="fas fa-chart-bar me-2"></i>Kartu Ringkasan</h6>
                                            <p class="small text-muted mb-2">Kartu di bagian atas menampilkan kondisi
                                                global gudang:</p>
                                            <ul class="list-unstyled small d-flex flex-column gap-1 mb-0">
                                                <li class="d-flex gap-2"><span
                                                        class="badge bg-success flex-shrink-0">Aman</span>
                                                    <div>Total item dengan stok di atas batas minimum</div>
                                                </li>
                                                <li class="d-flex gap-2"><span
                                                        class="badge bg-warning flex-shrink-0">Limit</span>
                                                    <div>Item mendekati habis — perlu segera reorder</div>
                                                </li>
                                                <li class="d-flex gap-2"><span
                                                        class="badge bg-danger flex-shrink-0">Habis</span>
                                                    <div>Item kosong — produksi bisa terhenti!</div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 rounded-4" style="background: #fffbeb; border: 1px solid #fcd34d;">
                                    <p class="small mb-0 fw-700" style="color: #92400e;"><i
                                            class="fas fa-sync me-2 text-warning"></i>Klik tombol
                                        <strong>Refresh</strong> jika halaman ini dibuka dalam waktu lama untuk
                                        memastikan data mutasi terbaru sudah termuat.</p>
                                </div>
                            </div>

                            <!-- TAB 3: STATUS STOK -->
                            <div class="tab-pane fade p-5" id="stk-status">
                                <h4 class="fw-900 text-dark mb-2">🚦 Klasifikasi Status Barang</h4>
                                <p class="text-muted small mb-4">Setiap bahan memiliki status yang ditentukan
                                    berdasarkan perbandingan antara stok aktual dengan batas minimum (stok minimal) yang
                                    sudah diatur per bahan.</p>
                                <div class="d-flex flex-column gap-3 mb-4">
                                    <div class="p-4 rounded-4 d-flex gap-4 align-items-center"
                                        style="background: #f0fdf4; border: 2px solid #22c55e;">
                                        <div class="text-center flex-shrink-0" style="width: 70px;">
                                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center"
                                                style="width: 44px; height: 44px; background: #22c55e;">
                                                <i class="fas fa-check text-white fs-5"></i>
                                            </div>
                                            <div class="fw-900 text-success small mt-1">AMAN</div>
                                        </div>
                                        <div>
                                            <div class="fw-900 text-success small mb-1">Stok Aktual > Batas Minimum
                                            </div>
                                            <p class="small text-muted mb-1">Barang tersedia dalam jumlah cukup. Tidak
                                                memerlukan tindakan segera.</p>
                                            <div class="small text-muted"><strong>Tindakan:</strong> Tidak ada. Pantau
                                                saja secara rutin.</div>
                                        </div>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-4 align-items-center"
                                        style="background: #fffbeb; border: 2px solid #f59e0b;">
                                        <div class="text-center flex-shrink-0" style="width: 70px;">
                                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center"
                                                style="width: 44px; height: 44px; background: #f59e0b;">
                                                <i class="fas fa-exclamation text-white fs-5"></i>
                                            </div>
                                            <div class="fw-900 small mt-1" style="color: #d97706;">LIMIT</div>
                                        </div>
                                        <div>
                                            <div class="fw-900 small mb-1" style="color: #92400e;">Stok Aktual ≤ Batas
                                                Minimum</div>
                                            <p class="small text-muted mb-1">Barang mendekati titik habis. Perlu segera
                                                direncanakan pengadaannya.</p>
                                            <div class="small text-muted"><strong>Tindakan:</strong> Buat rencana
                                                belanja di menu <em>Rencana Belanja</em> atau hubungi bagian purchasing.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-4 align-items-center"
                                        style="background: #fef2f2; border: 2px solid #ef4444;">
                                        <div class="text-center flex-shrink-0" style="width: 70px;">
                                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center"
                                                style="width: 44px; height: 44px; background: #ef4444;">
                                                <i class="fas fa-times text-white fs-5"></i>
                                            </div>
                                            <div class="fw-900 text-danger small mt-1">HABIS</div>
                                        </div>
                                        <div>
                                            <div class="fw-900 text-danger small mb-1">Stok Aktual ≤ 0</div>
                                            <p class="small text-muted mb-1">Barang kosong total. Produksi yang
                                                membutuhkan bahan ini bisa terhambat.</p>
                                            <div class="small text-muted"><strong>Tindakan:</strong> <span
                                                    class="text-danger fw-800">SEGERA</span> lakukan pembelian darurat.
                                                Informasikan ke supervisor/produksi.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 4: ARTI KOLOM TABEL -->
                            <div class="tab-pane fade p-5" id="stk-columns">
                                <h4 class="fw-900 text-dark mb-2">📋 Penjelasan Kolom Tabel Monitor</h4>
                                <p class="text-muted small mb-4">Pahami setiap kolom di tabel monitoring agar bisa
                                    membaca kondisi stok dengan tepat.</p>
                                <div class="d-flex flex-column gap-3">
                                    <div class="p-3 rounded-3 d-flex gap-3 align-items-start"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <span class="badge bg-dark flex-shrink-0 px-3 py-2 rounded-3 small">STOK
                                            AWAL</span>
                                        <div class="small text-muted">Saldo stok di awal periode (bulan berjalan).
                                            Dihitung dari hasil Opname awal + semua mutasi masuk sebelum periode
                                            berjalan.</div>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex gap-3 align-items-start"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <span
                                            class="badge bg-primary flex-shrink-0 px-3 py-2 rounded-3 small">PEMBELIAN</span>
                                        <div class="small text-muted">Total barang masuk dari supplier melalui transaksi
                                            Purchase (Langkah 4) pada periode berjalan. Stok bertambah setiap ada
                                            pembelian tervalidasi.</div>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex gap-3 align-items-start"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <span
                                            class="badge bg-warning text-dark flex-shrink-0 px-3 py-2 rounded-3 small">PRODUKSI</span>
                                        <div class="small text-muted">Total bahan yang sudah keluar melalui Dispatch
                                            (Langkah 5) ke area produksi. Semakin besar angka ini, semakin banyak bahan
                                            yang terpakai produksi.</div>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex gap-3 align-items-start"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <span class="badge bg-success flex-shrink-0 px-3 py-2 rounded-3 small">STOK
                                            AKHIR</span>
                                        <div class="small text-muted">Stok real-time saat ini = Stok Awal + Pembelian −
                                            Produksi. Angka inilah yang menentukan STATUS barang (Aman/Limit/Habis).
                                        </div>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex gap-3 align-items-start"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <span class="badge bg-secondary flex-shrink-0 px-3 py-2 rounded-3 small">MIN
                                            STOK</span>
                                        <div class="small text-muted">Batas minimum yang ditetapkan per bahan. Jika Stok
                                            Akhir ≤ Min Stok → STATUS berubah menjadi LIMIT. Diatur di halaman Master
                                            Bahan.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 5: AUDIT & AKURASI -->
                            <div class="tab-pane fade p-5" id="stk-audit">
                                <h4 class="fw-900 text-dark mb-2">✅ Menjaga Akurasi Data</h4>
                                <p class="text-muted small mb-4">Data stok yang akurat adalah fondasi bisnis yang sehat.
                                    Ikuti panduan ini untuk mencegah data stok menjadi kacau.</p>
                                <div class="d-flex flex-column gap-3 mb-4">
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-start"
                                        style="background: #fffbeb; border: 1px solid #fcd34d;">
                                        <div class="flex-shrink-0 bg-warning rounded-circle text-dark d-flex align-items-center justify-content-center fw-900"
                                            style="width: 36px; height: 36px;">1</div>
                                        <div>
                                            <div class="fw-800 text-dark small mb-1">Jika Stok Sistem ≠ Stok Fisik</div>
                                            <p class="text-muted small mb-0">Segera lakukan <strong>Stock
                                                    Opname</strong> (Langkah 3). Jangan biarkan selisih menumpuk —
                                                semakin lama dibiarkan, semakin sulit dilacak penyebabnya.</p>
                                        </div>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-start"
                                        style="background: #eff6ff; border: 1px solid #93c5fd;">
                                        <div class="flex-shrink-0 bg-primary rounded-circle text-white d-flex align-items-center justify-content-center fw-900"
                                            style="width: 36px; height: 36px;">2</div>
                                        <div>
                                            <div class="fw-800 text-dark small mb-1">Jika Ada Stok Negatif (Minus)</div>
                                            <p class="text-muted small mb-0">Stok minus menandakan ada transaksi yang
                                                tidak tercatat (Dispatch yang belum diinput). Telusuri via menu
                                                <strong>Mutasi Stok/Ledger</strong> untuk menemukan akar masalahnya.</p>
                                        </div>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-start"
                                        style="background: #f0fdf4; border: 1px solid #86efac;">
                                        <div class="flex-shrink-0 bg-success rounded-circle text-white d-flex align-items-center justify-content-center fw-900"
                                            style="width: 36px; height: 36px;">3</div>
                                        <div>
                                            <div class="fw-800 text-dark small mb-1">Audit Cepat dengan Scan Barcode
                                            </div>
                                            <p class="text-muted small mb-0">Scan barcode barang fisik langsung di kolom
                                                pencarian halaman ini. Sistem akan menampilkan baris data bahan
                                                tersebut, sehingga Anda bisa langsung membandingkan stok layar dengan
                                                rak fisik.</p>
                                        </div>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-start"
                                        style="background: #fdf4ff; border: 1px solid #d8b4fe;">
                                        <div class="flex-shrink-0 rounded-circle text-white d-flex align-items-center justify-content-center fw-900"
                                            style="width: 36px; height: 36px; background: #8b5cf6;">4</div>
                                        <div>
                                            <div class="fw-800 text-dark small mb-1">Lihat Riwayat Mutasi</div>
                                            <p class="text-muted small mb-0">Gunakan menu <strong>Ledger / Mutasi
                                                    Stok</strong> untuk melihat history lengkap masuk-keluar setiap
                                                bahan. Ini alat investigasi terbaik jika ada ketidaksesuaian stok yang
                                                tidak bisa dijelaskan.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-4 rounded-4" style="background: #f0fdf4; border: 2px solid #22c55e;">
                                    <h6 class="fw-900 text-success mb-2">🎉 Selamat! Anda telah memahami alur 6 Langkah
                                        Gudang</h6>
                                    <p class="small text-muted mb-0">Dengan data yang lengkap di semua 6 langkah, sistem
                                        ERP dapat berjalan optimal. Monitoring stok akan selalu akurat, laporan
                                        pembelian bisa dipercaya, dan tim produksi bisa bekerja tanpa gangguan
                                        kekurangan bahan.</p>
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
    #sop-tabs-stock .nav-link {
        color: #64748b;
        background: white;
        text-align: left !important;
        border-radius: 12px;
        transition: all 0.2s ease;
        border: 1px solid #f1f5f9;
        margin-bottom: 5px;
    }

    #sop-tabs-stock .nav-link:hover {
        background: #f8fafc;
        color: #1a1c23;
        transform: translateX(5px);
    }

    #sop-tabs-stock .nav-link.active {
        background: #1e293b;
        color: white;
        border-color: #1e293b;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
</style>