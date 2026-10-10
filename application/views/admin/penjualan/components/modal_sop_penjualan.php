<!-- ================= SOP GUIDANCE MODAL ================= -->
<div class="modal fade" id="modalSopPenjualan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content modal-premium border-0 shadow-lg">
            <div class="modal-header border-0 py-4 px-4"
                style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%)">
                <h5 class="modal-title d-flex align-items-center gap-3 text-white fw-800">
                    <div class="bg-white bg-opacity-20 rounded-3 p-2 d-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px; backdrop-filter: blur(5px);">
                        <i class="fas fa-book-open fs-5"></i>
                    </div>
                    <span>STANDARD OPERATING PROCEDURE (SOP) PENJUALAN</span>
                </h5>
                <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- Sidebar Tabs -->
                    <div class="col-md-3 border-end bg-light bg-opacity-50">
                        <div class="nav flex-column nav-pills p-3 gap-2" id="sop-tabs" role="tablist">
                            <button
                                class="nav-link active text-start py-3 px-4 border-0 d-flex align-items-center gap-3 transition-all fw-700"
                                data-bs-toggle="pill" data-bs-target="#sop-new" type="button"
                                style="border-radius: 12px; font-size: 0.9rem;">
                                <i class="fas fa-star text-warning opacity-75" style="width: 20px;"></i> Fitur Terbaru
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 transition-all fw-700"
                                data-bs-toggle="pill" data-bs-target="#sop-dashboard" type="button"
                                style="border-radius: 12px; font-size: 0.9rem;">
                                <i class="fas fa-desktop opacity-50" style="width: 20px;"></i> Monitoring Dashboard
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 transition-all fw-700"
                                data-bs-toggle="pill" data-bs-target="#sop-audit" type="button"
                                style="border-radius: 12px; font-size: 0.9rem;">
                                <i class="fas fa-clipboard-check opacity-50" style="width: 20px;"></i> Audit Transaksi
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 transition-all fw-700"
                                data-bs-toggle="pill" data-bs-target="#sop-notif" type="button"
                                style="border-radius: 12px; font-size: 0.9rem;">
                                <i class="fab fa-whatsapp opacity-50" style="width: 20px;"></i> Notifikasi & Nota
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 transition-all fw-700"
                                data-bs-toggle="pill" data-bs-target="#sop-export" type="button"
                                style="border-radius: 12px; font-size: 0.9rem;">
                                <i class="fas fa-file-export opacity-50" style="width: 20px;"></i> Pelaporan & Export
                            </button>
                            <button
                                class="nav-link text-start py-3 px-4 border-0 d-flex align-items-center gap-3 transition-all fw-700"
                                data-bs-toggle="pill" data-bs-target="#sop-admin" type="button"
                                style="border-radius: 12px; font-size: 0.9rem;">
                                <i class="fas fa-user-shield opacity-50" style="width: 20px;"></i> Fungsi Manajerial
                            </button>
                        </div>
                    </div>
                    <!-- Content -->
                    <div class="col-md-9 px-0 bg-white">
                        <div class="tab-content h-100">
                            <!-- Fitur Terbaru -->
                            <div class="tab-pane fade show active p-5" id="sop-new">
                                <h4 class="fw-900 text-dark mb-4">Pembaruan Sistem (Release Notes)</h4>

                                <div
                                    class="p-4 rounded-4 bg-primary bg-opacity-10 border border-primary border-opacity-25 mb-4">
                                    <h6 class="fw-900 text-primary mb-3"><i class="fas fa-box-open me-2"></i> TARIK
                                        KERANJANG DESKPRINT (ROLLBACK)</h6>
                                    <p class="small text-dark fw-700 mb-2">Efisiensi pemulihan ketika pesanan salah
                                        ukuran, spesifikasi, atau kuantitas.</p>
                                    <ul class="small text-muted mb-0 lh-lg">
                                        <li>Terdapat di modul <strong>Manifest Transaksi</strong> (Akses: Admin/Owner).
                                        </li>
                                        <li>Tombol <strong>Rollback <i class="fas fa-undo"></i> -> Ke Deskprint</strong>
                                            sekarang tidak lagi langsung menghapus isi detail produk yang sudah
                                            capek-capek diketik ke dalam pesanan.</li>
                                        <li>Seluruh rincian akan diekstrak dan disalin kembali utuh sebagai draf
                                            sementara <strong>(Keranjang SPK)</strong>, sehingga Deskprint bisa
                                            melanjutkannya lagi melalui menu <strong>Create SPK</strong>, merevisi
                                            datanya secepat kilat tanpa repot mengetik ulang dari dari awal!</li>
                                    </ul>
                                </div>

                                <div
                                    class="p-4 rounded-4 bg-danger bg-opacity-10 border border-danger border-opacity-25 mb-4">
                                    <h6 class="fw-900 text-danger mb-3"><i class="fas fa-user-shield me-2"></i> ALASAN
                                        HAPUS SPK PERMANEN</h6>
                                    <p class="small text-dark fw-700 mb-2">Menutup celah hilangnya data transaksi secara
                                        kasat mata demi Audit mumpuni.</p>
                                    <ul class="small text-muted mb-0 lh-lg">
                                        <li>Terdapat di modul <strong>Manifest Transaksi</strong> (Akses: Admin/Owner).
                                        </li>
                                        <li>Sekarang <strong>TIDAK BISA</strong> menghapus transaksi menggunakan tombol
                                            Delete <i class="fas fa-trash-alt"></i> secara sembarangan, user kini
                                            dicegat dan diwajibkan untuk mengetikkan <strong>Alasan Penghapusan</strong>
                                            secara eksplisit terlebih dahulu di dalam kotak dialog.</li>
                                        <li>Semua alasan yang diberikan tersebut otomatis dicatat detail, dan diarsip
                                            rapi ke dalam tabel database <strong>Log Audit Sistem</strong> yang hanya
                                            bersifat Read-Only untuk menghindari penipuan/tindakan jahil!</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Dashboard -->
                            <div class="tab-pane fade p-5" id="sop-dashboard">
                                <h4 class="fw-900 text-dark mb-4">Navigasi Monitoring Dashboard</h4>
                                <div class="d-flex flex-column gap-4">
                                    <div class="sop-step d-flex gap-4">
                                        <div class="step-num text-white rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 45px; height: 45px; flex-shrink: 0; font-weight: 800; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
                                            1</div>
                                        <div>
                                            <h6 class="fw-800 text-dark mb-2">Melihat Performa Bulanan</h6>
                                            <p class="text-muted small">Gunakan widget <strong>KPI BULAN INI</strong>
                                                dan <strong>TOTAL PENDAPATAN</strong> di baris atas untuk melihat
                                                performa akumulatif secara real-time.</p>
                                        </div>
                                    </div>
                                    <div class="sop-step d-flex gap-4">
                                        <div class="step-num text-white rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 45px; height: 45px; flex-shrink: 0; font-weight: 800; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
                                            2</div>
                                        <div>
                                            <h6 class="fw-800 text-dark mb-2">Interaksi Kalender Sales</h6>
                                            <p class="text-muted small">Warna pada kalender menunjukkan volume
                                                penjualan: <span class="badge bg-danger">Merah (< 100rb)</span>, <span
                                                            class="badge bg-warning text-dark">Kuning (> 100rb)</span>,
                                                        <span class="badge bg-info">Biru (> 500rb)</span>, <span
                                                            class="badge bg-success">Hijau (> 1jt)</span>. Klik pada
                                                        tanggal tertentu untuk melihat detail transaksi di bawah.</p>
                                        </div>
                                    </div>
                                    <div class="sop-step d-flex gap-4">
                                        <div class="step-num text-white rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 45px; height: 45px; flex-shrink: 0; font-weight: 800; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
                                            3</div>
                                        <div>
                                            <h6 class="fw-800 text-dark mb-2">Filter Konsolidasi Cabang</h6>
                                            <p class="text-muted small">Untuk Owner/Admin Pusat, gunakan filter
                                                <strong>LOKASI/CABANG</strong> di bagian atas untuk memantau performa
                                                cabang spesifik atau melihat data konsolidasi (Gabungan).
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Audit -->
                            <div class="tab-pane fade p-5" id="sop-audit">
                                <h4 class="fw-900 text-dark mb-4">Prosedur Audit & Manifest Transaksi</h4>
                                <div class="alert alert-info border-0 rounded-4 p-4 mb-4"
                                    style="background: rgba(59, 130, 246, 0.1); color: #1e40af;">
                                    <h6 class="fw-800"><i class="fas fa-info-circle me-2"></i> Audit Harian</h6>
                                    Setiap akhir shift, kasir/admin wajib memverifikasi manifest transaksi harian dengan
                                    uang fisik yang diterima di laci kasir (Cash on Hand).
                                </div>
                                <ul class="list-group list-group-flush gap-3 mt-4">
                                    <li class="list-group-item bg-transparent border-bottom-0 p-0">
                                        <div class="d-flex align-items-start gap-3">
                                            <i class="fas fa-check-circle text-success fs-5 mt-1"></i>
                                            <div>
                                                <div class="fw-800 text-dark">Verifikasi Jenis Transaksi</div>
                                                <div class="text-muted small">Bedakan antara <strong>SPK
                                                        PRODUKSI</strong> (Order normal) dan <strong>POS MANUAL</strong>
                                                    (Badge BIRU) untuk memisahkan stok bahan.</div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item bg-transparent border-bottom-0 p-0">
                                        <div class="d-flex align-items-start gap-3">
                                            <i class="fas fa-check-circle text-success fs-5 mt-1"></i>
                                            <div>
                                                <div class="fw-800 text-dark">Pemeriksaan Item Detail</div>
                                                <div class="text-muted small">Klik ikon mata <i class="fas fa-eye"></i>
                                                    untuk membedah isi belanjaan konsumen. Pastikan QTY dan harga satuan
                                                    sudah sesuai dengan pricelist yang berlaku.</div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <!-- Notifikasi -->
                            <div class="tab-pane fade p-5" id="sop-notif">
                                <h4 class="fw-900 text-dark mb-4">Pengiriman Nota & WhatsApp</h4>
                                <div class="bg-light p-4 rounded-4 border mb-4">
                                    <div class="fw-800 text-dark mb-2">Cara Mengirim Nota ke WhatsApp:</div>
                                    <ol class="small text-muted mb-0 lh-lg">
                                        <li>Buka <strong>Manifest Transaksi</strong> pada tanggal yang diinginkan.</li>
                                        <li>Klik ikon mata <i class="fas fa-eye text-primary"></i> pada transaksi
                                            target.</li>
                                        <li>Klik tombol hijau <strong>KIRIM NOTA WA</strong> pada bagian atas modal
                                            rincian.</li>
                                        <li>Pastikan nomor WhatsApp konsumen yang terdaftar sudah benar dan berformat
                                            internasional (628xxx).</li>
                                    </ol>
                                </div>
                                <p class="small text-danger fw-700 bg-danger bg-opacity-10 p-3 rounded-3"><i
                                        class="fas fa-exclamation-triangle me-1"></i> Jika notifikasi gagal dikirim, cek
                                    koneksi internet server atau status API WhatsApp pada Dashboard Utility.</p>
                            </div>

                            <!-- Export -->
                            <div class="tab-pane fade p-5" id="sop-export">
                                <h4 class="fw-900 text-dark mb-4">Export Laporan & Arsip</h4>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 border h-100 shadow-sm"
                                            style="border-top: 5px solid #27ae60 !important; background: #fafafa;">
                                            <h6 class="fw-800 text-dark mb-3"><i
                                                    class="fas fa-file-excel text-success me-2"></i> Rekap Harian (XLSX)
                                            </h6>
                                            <p class="text-muted small mb-0">Klik tombol <strong>EXPORT LAPORAN
                                                    (EXCEL)</strong> di panel sebelah kanan untuk mengunduh rekap
                                                transaksi untuk tanggal terpilih.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 border h-100 shadow-sm"
                                            style="border-top: 5px solid #3498db !important; background: #fafafa;">
                                            <h6 class="fw-800 text-dark mb-3"><i
                                                    class="fas fa-print text-primary me-2"></i> Cetak Invoice Fisik</h6>
                                            <p class="text-muted small mb-0">Gunakan tombol printer <i
                                                    class="fas fa-print"></i> pada kolom aksi manifest untuk mencetak
                                                invoice ukuran A4 atau Thermal 80mm.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Admin -->
                            <div class="tab-pane fade p-5" id="sop-admin">
                                <h4 class="fw-900 text-dark mb-4">Fungsi Koreksi & Manajerial</h4>

                                <!-- SOP Rollback -->
                                <div
                                    class="p-4 rounded-4 bg-warning bg-opacity-10 border border-warning border-opacity-25 mb-4">
                                    <h6 class="fw-900 text-dark mb-3" style="color: #d97706 !important;"><i
                                            class="fas fa-undo me-2"></i>
                                        KEMBALIKAN TRANSAKSI (ROLLBACK)</h6>
                                    <p class="small text-dark fw-700 mb-2">Gunakan tombol Oranye (<i
                                            class="fas fa-undo"></i>) untuk merevisi transaksi tanpa harus menghapus
                                        bersih SPK-nya.</p>
                                    <ul class="small text-muted mb-0 lh-lg">
                                        <li><strong>Ke Kasir:</strong> Membatalkan status pembayaran/lunas. Invoice
                                            dicabut, namun keranjang belanja dibiarkan utuh di antrean meja Kasir untuk
                                            diulang pembayarannya.</li>
                                        <li><strong>Ke Deskprint:</strong> Mengosongkan data pembayaran dan produksi,
                                            lalu menarik seluruh rincian barang kembali menjadi Draft Keranjang di
                                            halaman Create SPK. Deskprint bisa langsung mengedit (revisi qty/ukuran
                                            kotor) dan memroses ulang pesanan.</li>
                                    </ul>
                                </div>

                                <!-- SOP Hapus -->
                                <div
                                    class="p-4 rounded-4 bg-danger bg-opacity-10 border border-danger border-opacity-25 mb-4">
                                    <h6 class="fw-900 text-danger mb-3"><i class="fas fa-trash-alt me-2"></i>
                                        PENGHAPUSAN TRANSAKSI PERMANEN</h6>
                                    <p class="small text-dark fw-700 mb-2">Tombol Merah (<i
                                            class="fas fa-trash-alt"></i>) hanya untuk Administrator/Owner dan memiliki
                                        efek penghapusan massal.</p>
                                    <ul class="small text-muted mb-0 lh-lg">
                                        <li>Tindakan ini menyapu bersih data transaksi di seluruh cabang tabel:
                                            Pembayaran, SPK, hingga sisa cicilan hutang konsumen.</li>
                                        <li><strong>Anda wajib memasukkan teks alasan/keterangan penghapusan</strong>.
                                        </li>
                                        <li>Waktu eksekusi, Nama Admin, Alamat IP, dan Alasan Hapus yang diinput
                                            tersebut akan dicatat dan terkunci ke dalam buku besar <strong>Log Audit
                                                Sistem</strong>.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-light shadow-inner d-flex justify-content-center">
                <button type="button" class="btn btn-dark px-5 py-3 fw-900 rounded-pill shadow-lg transition-all"
                    data-bs-dismiss="modal" style="background: #1e1b4b;">
                    SAYA SUDAH MEMAHAMI SOP INI
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    #sop-tabs .nav-link {
        color: #64748b;
        background: transparent;
        text-align: left !important;
        border-radius: 12px;
        transition: all 0.2s ease;
    }

    #sop-tabs .nav-link:hover {
        background: rgba(79, 70, 229, 0.05);
        color: #4f46e5;
    }

    #sop-tabs .nav-link.active {
        background: white;
        color: #4f46e5;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .shadow-inner {
        box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
    }
</style>
<div class="modal fade" id="transactionDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-premium border-0 shadow-lg">
            <div class="modal-header border-0 py-4 px-4">
                <h5 class="modal-title d-flex align-items-center gap-3 text-white fw-800">
                    <div class="bg-white bg-opacity-20 rounded-3 p-2 d-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px; backdrop-filter: blur(5px);">
                        <i class="fas fa-file-invoice-dollar fs-5"></i>
                    </div>
                    <span>LOG MANIFEST TRANSAKSI</span>
                </h5>
                <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="transactionDetailContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer border-0 p-4 bg-light">
                <button type="button" class="btn btn-light px-4 py-2 fw-800 rounded-3" data-bs-dismiss="modal">
                    TUTUP JENDELA
                </button>
                <button type="button"
                    class="btn btn-dark px-4 py-2 fw-800 rounded-3 d-flex align-items-center gap-2 shadow-sm"
                    style="background: var(--primary-gradient)" onclick="printTransaction()">
                    <i class="fas fa-print"></i> CETAK DOKUMEN
                </button>
            </div>
        </div>
    </div>
</div>