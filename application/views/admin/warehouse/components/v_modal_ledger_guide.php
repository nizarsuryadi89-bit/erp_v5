<!-- ================= SOP KARTU STOK & AUDIT LEDGER ================= -->
<div class="modal fade" id="modalLedgerGuide" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 28px;">
            <div class="modal-header border-0 py-4 px-4"
                style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%)">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center"
                        style="width: 42px; height: 42px; background: #6366f1;">
                        <i class="fas fa-scroll text-white fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-900 mb-0">SOP KARTU STOK & AUDIT LEDGER</h5>
                        <small class="fw-bold" style="color: #a5b4fc; opacity: 0.9;">Alat investigasi dan audit trail
                            untuk setiap pergerakan stok di gudang</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-3 border-end bg-light bg-opacity-50">
                        <div class="nav flex-column nav-pills p-3 gap-2" id="sop-tabs-ledger" role="tablist">
                            <button
                                class="nav-link active text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#led-concept" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-history text-primary" style="width: 20px;"></i> Apa itu Ledger?
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#led-columns" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-table text-success" style="width: 20px;"></i> Baca Kolom Ledger
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#led-navigate" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-search text-info" style="width: 20px;"></i> Cara Menelusuri
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#led-adjust" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-tools text-danger" style="width: 20px;"></i> Koreksi & Penyesuaian
                            </button>
                        </div>
                    </div>
                    <div class="col-md-9 px-0 bg-white">
                        <div class="tab-content h-100">

                            <!-- TAB 1: APA ITU LEDGER -->
                            <div class="tab-pane fade show active p-5" id="led-concept">
                                <h4 class="fw-900 text-dark mb-2">📜 Apa itu Kartu Stok (Ledger)?</h4>
                                <p class="text-muted small mb-4">Ledger adalah <strong>buku besar digital</strong> yang
                                    mencatat setiap pergerakan barang secara kronologis dan presisi. Ini adalah sumber
                                    kebenaran tertinggi dalam sistem gudang TGM.</p>

                                <div class="row g-4 mb-4">
                                    <div class="col-md-4">
                                        <div class="p-4 rounded-4 text-center h-100"
                                            style="background: #eff6ff; border: 2px solid #3b82f6;">
                                            <div class="text-primary mb-2" style="font-size: 2rem;"><i
                                                    class="fas fa-shoe-prints"></i></div>
                                            <h6 class="fw-900 text-primary">Jejak Audit Lengkap</h6>
                                            <p class="small text-muted mb-0">Setiap transaksi tercatat dengan: waktu
                                                persis, yang melakukan, jenis transaksi, dan referensi dokumen.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-4 rounded-4 text-center h-100"
                                            style="background: #fffbeb; border: 2px solid #f59e0b;">
                                            <div class="text-warning mb-2" style="font-size: 2rem;"><i
                                                    class="fas fa-balance-scale"></i></div>
                                            <h6 class="fw-900" style="color: #92400e;">Riwayat Saldo</h6>
                                            <p class="small text-muted mb-0">Lihat saldo stok setelah setiap transaksi —
                                                memudahkan verifikasi apakah alur stok konsisten.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-4 rounded-4 text-center h-100"
                                            style="background: #f0fdf4; border: 2px solid #22c55e;">
                                            <div class="text-success mb-2" style="font-size: 2rem;"><i
                                                    class="fas fa-search-dollar"></i></div>
                                            <h6 class="fw-900 text-success">Alat Investigasi</h6>
                                            <p class="small text-muted mb-0">Jika ada selisih stok, Ledger memungkinkan
                                                Anda melacak tepat di transaksi mana selisih mulai terjadi.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 rounded-4" style="background: #1e293b; color: white;">
                                    <h6 class="fw-900 mb-2" style="color: #fbbf24;"><i
                                            class="fas fa-lightbulb me-2"></i>Kapan Gunakan Ledger?</h6>
                                    <div class="row g-3 small">
                                        <div class="col-md-6">
                                            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.1);">
                                                <div class="fw-800 text-info mb-1">🔍 Untuk Investigasi Selisih</div>
                                                <div class="opacity-75">Jika stok sistem ≠ fisik, buka Ledger dan cari
                                                    di titik mana saldo mulai berbeda dari yang seharusnya.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.1);">
                                                <div class="fw-800 text-success mb-1">📊 Untuk Audit Rutin</div>
                                                <div class="opacity-75">Supervisor bisa review Ledger harian untuk
                                                    memastikan semua transaksi operator sudah lengkap dan benar.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 2: BACA KOLOM -->
                            <div class="tab-pane fade p-5" id="led-columns">
                                <h4 class="fw-900 text-dark mb-2">📋 Cara Membaca Kolom Ledger</h4>
                                <p class="text-muted small mb-4">Setiap baris di Ledger merepresentasikan satu
                                    transaksi. Pahami arti setiap kolom agar bisa mengaudit dengan tepat.</p>

                                <div class="d-flex flex-column gap-3 mb-4">
                                    <div class="p-3 rounded-3 d-flex gap-3 align-items-center"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <span class="badge bg-dark flex-shrink-0 px-3 py-2">TANGGAL & WAKTU</span>
                                        <div class="small text-muted">Timestamp persis kapan transaksi terjadi — akurat
                                            hingga detik. Berguna untuk mencocokkan dengan nota fisik.</div>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex gap-3 align-items-center"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <span class="badge bg-primary flex-shrink-0 px-3 py-2">JENIS TRANSAKSI</span>
                                        <div class="small text-muted"><span class="badge bg-success me-1">IN</span>
                                            untuk barang masuk (Pembelian / Opname +) | <span
                                                class="badge bg-danger me-1">OUT</span> untuk barang keluar (Dispatch /
                                            Opname -)</div>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex gap-3 align-items-center"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <span class="badge bg-success flex-shrink-0 px-3 py-2">QTY MASUK</span>
                                        <div class="small text-muted">Jumlah unit yang bertambah ke stok dalam satu
                                            transaksi ini. Dalam satuan produksi (terkecil).</div>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex gap-3 align-items-center"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <span class="badge bg-danger flex-shrink-0 px-3 py-2">QTY KELUAR</span>
                                        <div class="small text-muted">Jumlah unit yang berkurang dari stok dalam satu
                                            transaksi ini. Dalam satuan produksi (terkecil).</div>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex gap-3 align-items-center"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <span class="badge bg-secondary flex-shrink-0 px-3 py-2">SALDO AKHIR</span>
                                        <div class="small text-muted">Sisa stok setelah transaksi ini. Harus sama dengan
                                            Saldo Akhir baris sebelumnya ± QTY transaksi. Jika tidak cocok, ada
                                            transaksi yang hilang di antara keduanya.</div>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex gap-3 align-items-center"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <span class="badge bg-info flex-shrink-0 px-3 py-2">REFERENSI</span>
                                        <div class="small text-muted">Nomor dokumen asal transaksi (No. Faktur, No.
                                            Dispatch, dll). Gunakan ini untuk kembali ke transaksi asli jika perlu
                                            koreksi.</div>
                                    </div>
                                </div>

                                <div class="p-3 rounded-4" style="background: #f0fdf4; border: 1px solid #86efac;">
                                    <p class="small mb-0 fw-700 text-success"><i
                                            class="fas fa-lightbulb me-2"></i><strong>Tips Audit Cepat:</strong> Jika
                                        Saldo Akhir baris tertentu tiba-tiba turun drastis tanpa ada transaksi OUT yang
                                        besar, kemungkinan ada Dispatch yang datang dari arah lain (misal: dari gudang
                                        lain). Gunakan filter "Semua Gudang" untuk melihat gambaran lengkap.</p>
                                </div>
                            </div>

                            <!-- TAB 3: CARA MENELUSURI -->
                            <div class="tab-pane fade p-5" id="led-navigate">
                                <h4 class="fw-900 text-dark mb-2">🔍 Cara Menelusuri Selisih via Ledger</h4>
                                <p class="text-muted small mb-4">Langkah sistematis untuk menemukan akar penyebab
                                    selisih stok menggunakan Ledger.</p>

                                <div class="d-flex flex-column gap-3 mb-4">
                                    <div class="p-3 rounded-3 d-flex align-items-start gap-3"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="bg-primary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 32px; height: 32px; font-size: 0.85rem;">1</div>
                                        <div class="small"><strong>Filter Gudang:</strong> Pilih gudang yang bermasalah
                                            dari dropdown di atas tabel. Perubahan filter akan memuat ulang data Ledger
                                            secara real-time.</div>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-start gap-3"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="bg-primary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 32px; height: 32px; font-size: 0.85rem;">2</div>
                                        <div class="small"><strong>Cari Nama Material:</strong> Gunakan kolom pencarian
                                            untuk filter hanya menampilkan riwayat satu bahan tertentu.</div>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-start gap-3"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="bg-primary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 32px; height: 32px; font-size: 0.85rem;">3</div>
                                        <div class="small"><strong>Baca dari Atas ke Bawah (kronologis):</strong>
                                            Temukan titik di mana Saldo Akhir tidak konsisten dengan QTY transaksi di
                                            baris sebelumnya. Ini titik awal masalah.</div>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-start gap-3"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="bg-primary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 32px; height: 32px; font-size: 0.85rem;">4</div>
                                        <div class="small"><strong>Catat Nomor Referensi:</strong> Dari baris yang
                                            bermasalah, catat nomor referensi transaksi. Gunakan nomor ini untuk
                                            menemukan transaksi asli di menu Purchase atau Dispatch.</div>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-start gap-3"
                                        style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="bg-success rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 32px; height: 32px; font-size: 0.85rem;">5</div>
                                        <div class="small"><strong>Laporkan ke Admin:</strong> Setelah menemukan akar
                                            masalah, laporkan ke Admin/Supervisor dengan bukti (screenshot Ledger) untuk
                                            koreksi data resmi.</div>
                                    </div>
                                </div>

                                <div class="p-3 rounded-4" style="background: #fffbeb; border: 1px solid #fcd34d;">
                                    <p class="small mb-0 fw-700" style="color: #92400e;"><i
                                            class="fas fa-exclamation-triangle me-2 text-warning"></i><strong>Jangan
                                            koreksi sendiri</strong> dengan input data baru tanpa investigasi lengkap.
                                        Koreksi yang salah bisa menyebabkan selisih berlipat ganda.</p>
                                </div>
                            </div>

                            <!-- TAB 4: KOREKSI & PENYESUAIAN -->
                            <div class="tab-pane fade p-5" id="led-adjust">
                                <h4 class="fw-900 text-dark mb-2">🔧 Koreksi & Penyesuaian Manual</h4>
                                <p class="text-muted small mb-4">Fitur ini hanya untuk <strong>Admin /
                                        Supervisor</strong>. Jangan dilakukan oleh operator biasa tanpa otorisasi.</p>

                                <div class="p-4 rounded-4 mb-4 d-flex align-items-start gap-3"
                                    style="background: #fef2f2; border: 2px solid #ef4444;">
                                    <i class="fas fa-exclamation-triangle text-danger fs-4 flex-shrink-0 mt-1"></i>
                                    <div>
                                        <h6 class="fw-900 text-danger mb-1">⚠️ Penyesuaian Manual = Entri Khusus di
                                            Ledger</h6>
                                        <p class="small text-muted mb-0">Setiap penyesuaian manual akan membuat entri
                                            baru di Ledger dengan tipe "ADJUSTMENT". Ini bisa dilihat oleh semua
                                            supervisor dan meninggalkan jejak audit yang tidak bisa dihapus. Pastikan
                                            ada alasan yang jelas sebelum melakukan penyesuaian.</p>
                                    </div>
                                </div>

                                <div class="row g-4 mb-4">
                                    <div class="col-md-4">
                                        <div class="p-4 rounded-4 text-center h-100"
                                            style="background: #eff6ff; border: 2px solid #3b82f6;">
                                            <div class="bg-primary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center mx-auto mb-2"
                                                style="width: 40px; height: 40px;">1</div>
                                            <h6 class="fw-900 text-primary small">Pilih Material</h6>
                                            <p class="small text-muted mb-0">Cari material yang stoknya akan
                                                disesuaikan. Lihat saldo sistem saat ini di panel bawah.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-4 rounded-4 text-center h-100"
                                            style="background: #fffbeb; border: 2px solid #f59e0b;">
                                            <div class="rounded-circle text-dark fw-900 d-flex align-items-center justify-content-center mx-auto mb-2"
                                                style="width: 40px; height: 40px; background: #f59e0b;">2</div>
                                            <h6 class="fw-900 small" style="color: #92400e;">Input Stok Aktual</h6>
                                            <p class="small text-muted mb-0">Masukkan jumlah stok fisik yang seharusnya
                                                (hasil hitung langsung di rak).</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-4 rounded-4 text-center h-100"
                                            style="background: #f0fdf4; border: 2px solid #22c55e;">
                                            <div class="bg-success rounded-circle text-white fw-900 d-flex align-items-center justify-content-center mx-auto mb-2"
                                                style="width: 40px; height: 40px;">3</div>
                                            <h6 class="fw-900 text-success small">Auto-Sync</h6>
                                            <p class="small text-muted mb-0">Sistem menghitung selisih dan membuat entri
                                                penyesuaian otomatis di Ledger dengan timestamp.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <h6 class="fw-900 text-dark mb-2"><i
                                            class="fas fa-clipboard-list me-2 text-primary"></i>Wajib Didokumentasikan
                                    </h6>
                                    <p class="small text-muted mb-0">Setiap penyesuaian manual harus disertai
                                        <strong>alasan tertulis</strong> yang disimpan di kolom keterangan. Contoh:
                                        "Koreksi pasca opname 23 Feb 2026 - ditemukan 5 lembar kotor tidak terpakai"
                                        atau "Perbaikan kesalahan input Dispatch No. D-2024-099".</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 d-flex justify-content-between align-items-center"
                style="background: #f8fafc;">
                <div class="small text-muted"><i class="fas fa-info-circle me-1 text-primary"></i> Ledger adalah dokumen
                    audit resmi. Gunakan dengan tanggung jawab.</div>
                <button type="button" class="btn btn-dark px-5 py-2 fw-900 rounded-pill shadow-sm"
                    data-bs-dismiss="modal" style="background: #1e293b; border: none;">
                    <i class="fas fa-check me-2"></i>SAYA MENGERTI, TUTUP
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    #sop-tabs-ledger .nav-link {
        color: #64748b;
        background: white;
        text-align: left !important;
        border-radius: 12px;
        transition: all 0.2s ease;
        border: 1px solid #f1f5f9;
        margin-bottom: 5px;
    }

    #sop-tabs-ledger .nav-link:hover {
        background: #f8fafc;
        color: #1a1c23;
        transform: translateX(5px);
    }

    #sop-tabs-ledger .nav-link.active {
        background: #1e293b;
        color: white;
        border-color: #1e293b;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
</style>