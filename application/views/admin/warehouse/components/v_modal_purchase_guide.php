<!-- ================= SOP PEMBELIAN / PENERIMAAN BARANG (LANGKAH 4 dari 6) ================= -->
<div class="modal fade" id="modalPurchaseGuide" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 28px;">
            <div class="modal-header border-0 py-4 px-4"
                style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%)">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center"
                        style="width: 42px; height: 42px; background: #f59e0b;">
                        <i class="fas fa-shopping-basket text-white fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-900 mb-0">SOP LANGKAH 4: PEMBELIAN & PENERIMAAN BARANG</h5>
                        <small class="fw-bold" style="color: #fbbf24; opacity: 0.85;">Setiap barang dari supplier WAJIB
                            dicatat di sini untuk menambah stok</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-3 border-end bg-light bg-opacity-50">
                        <div class="nav flex-column nav-pills p-3 gap-2" id="sop-tabs-purchase" role="tablist">
                            <button
                                class="nav-link active text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#pur-workflow" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-project-diagram text-primary" style="width: 20px;"></i> Posisi Langkah
                                4
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#pur-flow" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-random text-warning" style="width: 20px;"></i> Alur Penerimaan
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#pur-cart" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-cart-plus text-success" style="width: 20px;"></i> Input & Keranjang
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#pur-final" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-file-invoice text-info" style="width: 20px;"></i> Data Wajib &
                                Finalisasi
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 fw-700 shadow-sm"
                                data-bs-toggle="pill" data-bs-target="#pur-debt" type="button"
                                style="border-radius: 12px; font-size: 0.85rem;">
                                <i class="fas fa-credit-card text-danger" style="width: 20px;"></i> Cash vs Hutang
                            </button>
                        </div>
                    </div>
                    <div class="col-md-9 px-0 bg-white">
                        <div class="tab-content h-100">

                            <!-- TAB 1: POSISI LANGKAH 4 -->
                            <div class="tab-pane fade show active p-5" id="pur-workflow">
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <span class="badge px-3 py-2 rounded-pill fw-800"
                                        style="font-size: 0.8rem; background: #f59e0b;">LANGKAH 4 dari 6</span>
                                    <h4 class="fw-900 text-dark mb-0">Posisi Anda dalam Alur Kerja Gudang</h4>
                                </div>
                                <div class="p-4 rounded-4 mb-4 d-flex align-items-center gap-3"
                                    style="background: #fffbeb; border: 2px solid #f59e0b;">
                                    <div class="flex-shrink-0" style="color: #d97706; font-size: 2rem;"><i
                                            class="fas fa-truck"></i></div>
                                    <div>
                                        <h6 class="fw-900 mb-1" style="color: #92400e;">🛒 HALAMAN INI UNTUK MENCATAT
                                            SETIAP BARANG MASUK DARI SUPPLIER</h6>
                                        <p class="small mb-0 text-muted">Setiap kali barang datang dari supplier dan
                                            diterima di gudang, <strong>wajib dicatat di sini</strong>. Transaksi ini
                                            akan langsung <u>menambah stok</u> di sistem dan mencatat nilai aset
                                            pembelian.</p>
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-2">
                                    <div class="p-3 rounded-3 d-flex align-items-center gap-3 bg-light border">
                                        <div class="bg-secondary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 36px; height: 36px;">1</div>
                                        <div>
                                            <div class="fw-800 small">Registrasi Induk & Satuan Dasar</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">Master Induk & Jasa</div>
                                        </div>
                                        <span class="badge bg-success ms-auto flex-shrink-0 px-3">✓ SELESAI</span>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-center gap-3 bg-light border">
                                        <div class="bg-secondary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 36px; height: 36px;">2</div>
                                        <div>
                                            <div class="fw-800 small">Varian, Konversi & Barcode</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">Master Bahan & Material
                                            </div>
                                        </div>
                                        <span class="badge bg-success ms-auto flex-shrink-0 px-3">✓ SELESAI</span>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-center gap-3 bg-light border">
                                        <div class="bg-secondary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 36px; height: 36px;">3</div>
                                        <div>
                                            <div class="fw-800 small">Stok Opname (Saldo Awal)</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">Rekonsiliasi stok fisik
                                                vs sistem</div>
                                        </div>
                                        <span class="badge bg-success ms-auto flex-shrink-0 px-3">✓ SELESAI</span>
                                    </div>
                                    <div class="p-3 rounded-3 d-flex align-items-center gap-3"
                                        style="background: #1e293b; border: 2px solid #6366f1;">
                                        <div class="rounded-circle text-dark fw-900 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 36px; height: 36px; background: #f59e0b;">4</div>
                                        <div class="text-white">
                                            <div class="fw-900 small">LANGKAH INI — Pembelian & Penerimaan Barang</div>
                                            <div style="font-size: 0.7rem; opacity: 0.7;">Catat setiap barang masuk dari
                                                supplier → stok bertambah otomatis</div>
                                        </div>
                                        <span class="badge ms-auto flex-shrink-0 px-3"
                                            style="background: #f59e0b; color: #1e293b;">AKTIF</span>
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

                            <!-- TAB 2: ALUR PENERIMAAN -->
                            <div class="tab-pane fade p-5" id="pur-flow">
                                <h4 class="fw-900 text-dark mb-2">🔄 Alur Proses Penerimaan Barang</h4>
                                <p class="text-muted small mb-4">Ikuti 3 tahap ini setiap kali barang dari supplier tiba
                                    di gudang dan siap dicatat ke sistem.</p>
                                <div class="row g-4 mb-4">
                                    <div class="col-md-4">
                                        <div class="p-4 rounded-4 text-center h-100"
                                            style="background: #eff6ff; border: 2px solid #3b82f6;">
                                            <div class="bg-primary rounded-circle text-white fw-900 d-flex align-items-center justify-content-center mx-auto mb-3"
                                                style="width: 48px; height: 48px; font-size: 1.3rem;">1</div>
                                            <h6 class="fw-900 text-primary">Verifikasi Fisik</h6>
                                            <p class="small text-muted mb-0">Hitung dan cocokkan barang fisik yang
                                                datang dengan surat jalan supplier sebelum input ke sistem.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-4 rounded-4 text-center h-100"
                                            style="background: #fffbeb; border: 2px solid #f59e0b;">
                                            <div class="rounded-circle text-dark fw-900 d-flex align-items-center justify-content-center mx-auto mb-3"
                                                style="width: 48px; height: 48px; font-size: 1.3rem; background: #f59e0b;">
                                                2</div>
                                            <h6 class="fw-900" style="color: #92400e;">Input ke Keranjang</h6>
                                            <p class="small text-muted mb-0">Tambahkan semua item dalam satu nota/faktur
                                                ke keranjang. Isi harga, QTY, dan satuan dengan benar.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-4 rounded-4 text-center h-100"
                                            style="background: #f0fdf4; border: 2px solid #22c55e;">
                                            <div class="bg-success rounded-circle text-white fw-900 d-flex align-items-center justify-content-center mx-auto mb-3"
                                                style="width: 48px; height: 48px; font-size: 1.3rem;">3</div>
                                            <h6 class="fw-900 text-success">Finalisasi & Simpan</h6>
                                            <p class="small text-muted mb-0">Isi data supplier, nomor faktur, dan metode
                                                pembayaran. Klik Simpan — stok otomatis bertambah.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-4 rounded-4" style="background: #fef2f2; border: 2px solid #ef4444;">
                                    <h6 class="fw-900 text-danger mb-2"><i
                                            class="fas fa-exclamation-triangle me-2"></i>TRANSAKSI TIDAK BISA DIUBAH
                                        SETELAH DISIMPAN</h6>
                                    <p class="small text-muted mb-0">Setelah tombol <strong>SIMPAN</strong> diklik, stok
                                        langsung bertambah dan transaksi terkunci. Jika ada kesalahan, hubungi Admin
                                        atau Supervisor untuk pembatalan. Oleh karena itu, <strong>pastikan semua data
                                            sudah benar sebelum menyimpan</strong>.</p>
                                </div>
                            </div>

                            <!-- TAB 3: INPUT KERANJANG -->
                            <div class="tab-pane fade p-5" id="pur-cart">
                                <h4 class="fw-900 text-dark mb-2">🛒 Cara Mengisi Keranjang Pembelian</h4>
                                <p class="text-muted small mb-4">Keranjang adalah daftar sementara semua barang dalam
                                    <strong>satu nota/faktur</strong>. Satu nota = satu transaksi.</p>
                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 h-100"
                                            style="background: #eff6ff; border: 2px solid #3b82f6;">
                                            <h6 class="fw-900 text-primary mb-3"><i
                                                    class="fas fa-info-circle me-2"></i>Field Wajib Diisi</h6>
                                            <ul class="list-unstyled d-flex flex-column gap-2 small mb-0">
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-dot-circle text-primary mt-1 flex-shrink-0"></i>
                                                    <div><strong>Pilih Bahan</strong>: Cari nama induk bahan dari master
                                                        data</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-dot-circle text-primary mt-1 flex-shrink-0"></i>
                                                    <div><strong>Pilih Varian</strong>: Pilih ukuran/kemasan yang
                                                        diterima (misal: Rim, Roll, Box)</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-dot-circle text-primary mt-1 flex-shrink-0"></i>
                                                    <div><strong>QTY</strong>: Jumlah yang diterima secara fisik (bukan
                                                        angka di nota jika berbeda)</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-dot-circle text-primary mt-1 flex-shrink-0"></i>
                                                    <div><strong>Harga Satuan</strong>: Harga beli per varian dari
                                                        faktur supplier</div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 h-100"
                                            style="background: #fffbeb; border: 2px solid #f59e0b;">
                                            <h6 class="fw-900 mb-3" style="color: #92400e;"><i
                                                    class="fas fa-magic me-2"></i>Tips Penting</h6>
                                            <ul class="list-unstyled d-flex flex-column gap-2 small mb-0">
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-check text-success mt-1 flex-shrink-0"></i>
                                                    <div>Input QTY sesuai <strong>fisik yang diterima</strong>, bukan
                                                        angka di nota jika berbeda</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-check text-success mt-1 flex-shrink-0"></i>
                                                    <div>Satu nota bisa berisi banyak item — tambahkan semuanya ke
                                                        keranjang sebelum finalisasi</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-times text-danger mt-1 flex-shrink-0"></i>
                                                    <div>Jangan buat 2 transaksi untuk 1 nota faktur yang sama (akan
                                                        menjadi double input)</div>
                                                </li>
                                                <li class="d-flex gap-2"><i
                                                        class="fas fa-times text-danger mt-1 flex-shrink-0"></i>
                                                    <div>Jangan input harga 0 — harga dipakai sebagai dasar HPP stok
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 rounded-4" style="background: #f0fdf4; border: 1px solid #86efac;">
                                    <p class="small mb-0 fw-700 text-success"><i
                                            class="fas fa-lightbulb me-2"></i><strong>Fitur Auto-Fill:</strong> Jika
                                        Anda datang dari menu <em>Rencana Belanja</em>, sistem akan otomatis membuka
                                        form dan memilih barang yang sudah direncanakan, sehingga proses input lebih
                                        cepat.</p>
                                </div>
                            </div>

                            <!-- TAB 4: DATA WAJIB & FINALISASI -->
                            <div class="tab-pane fade p-5" id="pur-final">
                                <h4 class="fw-900 text-dark mb-2">📋 Data Wajib & Proses Finalisasi</h4>
                                <p class="text-muted small mb-4">Setelah keranjang terisi, lengkapi data header
                                    transaksi berikut sebelum menyimpan.</p>
                                <div class="d-flex flex-column gap-3 mb-4">
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-start"
                                        style="background: #fef2f2; border: 2px solid #ef4444;">
                                        <span class="badge bg-danger flex-shrink-0 px-2 py-2 rounded-3">WAJIB</span>
                                        <div>
                                            <div class="fw-900 small text-dark mb-1">Nomor Faktur / No. Surat Jalan
                                            </div>
                                            <p class="small text-muted mb-0">Isi sesuai nomor yang tertera pada dokumen
                                                fisik supplier. Nomor ini digunakan untuk penelusuran jika ada komplain
                                                atau retur di kemudian hari. <strong>Jangan dikosongkan atau diisi
                                                    asal.</strong></p>
                                        </div>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-start"
                                        style="background: #fef2f2; border: 2px solid #ef4444;">
                                        <span class="badge bg-danger flex-shrink-0 px-2 py-2 rounded-3">WAJIB</span>
                                        <div>
                                            <div class="fw-900 small text-dark mb-1">Vendor / Supplier</div>
                                            <p class="small text-muted mb-0">Pilih nama supplier dari daftar master.
                                                Jika belum ada, tambahkan terlebih dahulu di menu <strong>Master
                                                    Supplier</strong>. Jangan biarkan field ini kosong.</p>
                                        </div>
                                    </div>
                                    <div class="p-4 rounded-4 d-flex gap-3 align-items-start"
                                        style="background: #fffbeb; border: 1px solid #fcd34d;">
                                        <span class="badge flex-shrink-0 px-2 py-2 rounded-3"
                                            style="background: #f59e0b; color: #1e293b;">PENTING</span>
                                        <div>
                                            <div class="fw-900 small text-dark mb-1">Tanggal Masuk</div>
                                            <p class="small text-muted mb-0">Default hari ini. Ubah jika Anda melakukan
                                                input untuk nota hari sebelumnya (backdate). Tanggal ini mempengaruhi
                                                laporan pembelian bulanan.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <h6 class="fw-900 text-dark mb-2"><i
                                            class="fas fa-history text-primary me-2"></i>Cek Panel Riwayat Sebelum
                                        Simpan</h6>
                                    <p class="small text-muted mb-0">Lihat daftar transaksi terakhir di panel kanan
                                        halaman. Pastikan nomor faktur yang Anda input <strong>belum pernah ada
                                            sebelumnya</strong> untuk menghindari double input.</p>
                                </div>
                            </div>

                            <!-- TAB 5: CASH VS HUTANG -->
                            <div class="tab-pane fade p-5" id="pur-debt">
                                <h4 class="fw-900 text-dark mb-2">💳 Metode Pembayaran: Cash vs Hutang</h4>
                                <p class="text-muted small mb-4">Pilih metode yang tepat agar laporan keuangan dan
                                    hutang vendor tercatat dengan akurat.</p>
                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 h-100"
                                            style="background: #f0fdf4; border: 2px solid #22c55e;">
                                            <div class="d-flex align-items-center gap-2 mb-3">
                                                <i class="fas fa-money-bill-wave text-success fs-4"></i>
                                                <h6 class="fw-900 text-success mb-0">CASH (Tunai / Transfer Langsung)
                                                </h6>
                                            </div>
                                            <p class="small text-muted mb-3">Gunakan jika nota <strong>sudah dibayar
                                                    lunas</strong> pada hari yang sama atau sebelum barang masuk.</p>
                                            <div class="d-flex flex-column gap-1 small">
                                                <div class="d-flex gap-2"><i
                                                        class="fas fa-check text-success flex-shrink-0 mt-1"></i>
                                                    <div>Langsung kurangi saldo kas perusahaan</div>
                                                </div>
                                                <div class="d-flex gap-2"><i
                                                        class="fas fa-check text-success flex-shrink-0 mt-1"></i>
                                                    <div>Tidak perlu isi tanggal jatuh tempo</div>
                                                </div>
                                                <div class="d-flex gap-2"><i
                                                        class="fas fa-check text-success flex-shrink-0 mt-1"></i>
                                                    <div>Tidak masuk ke daftar hutang vendor</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 h-100"
                                            style="background: #fffbeb; border: 2px solid #f59e0b;">
                                            <div class="d-flex align-items-center gap-2 mb-3">
                                                <i class="fas fa-credit-card fs-4" style="color: #d97706;"></i>
                                                <h6 class="fw-900 mb-0" style="color: #92400e;">HUTANG (Tempo / NET)
                                                </h6>
                                            </div>
                                            <p class="small text-muted mb-3">Gunakan jika nota <strong>belum
                                                    dibayar</strong> dan ada perjanjian pembayaran di kemudian hari.</p>
                                            <div class="d-flex flex-column gap-1 small">
                                                <div class="d-flex gap-2"><i
                                                        class="fas fa-check text-success flex-shrink-0 mt-1"></i>
                                                    <div>Wajib isi <strong>Tanggal Jatuh Tempo</strong> sesuai
                                                        kesepakatan</div>
                                                </div>
                                                <div class="d-flex gap-2"><i
                                                        class="fas fa-check text-success flex-shrink-0 mt-1"></i>
                                                    <div>Masuk ke laporan hutang vendor untuk ditagih kemudian</div>
                                                </div>
                                                <div class="d-flex gap-2"><i
                                                        class="fas fa-exclamation-triangle text-warning flex-shrink-0 mt-1"></i>
                                                    <div>Pembayaran dilakukan terpisah melalui modul Keuangan</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-4 rounded-4" style="background: #fef2f2; border: 2px solid #ef4444;">
                                    <h6 class="fw-900 text-danger mb-2"><i
                                            class="fas fa-exclamation-triangle me-2"></i>Jangan Salah Pilih Metode
                                        Bayar!</h6>
                                    <p class="small text-muted mb-0">Jika barang belum dibayar tapi dipilih Cash, sistem
                                        akan mengurangi saldo kas yang tidak seharusnya. Jika sudah dibayar tapi dipilih
                                        Hutang, tagihan menggantung di laporan vendor. Hubungi Admin untuk koreksi.</p>
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
    #sop-tabs-purchase .nav-link {
        color: #64748b;
        background: white;
        text-align: left !important;
        border-radius: 12px;
        transition: all 0.2s ease;
        border: 1px solid #f1f5f9;
        margin-bottom: 5px;
    }

    #sop-tabs-purchase .nav-link:hover {
        background: #f8fafc;
        color: #1a1c23;
        transform: translateX(5px);
    }

    #sop-tabs-purchase .nav-link.active {
        background: #1e293b;
        color: white;
        border-color: #1e293b;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
</style>