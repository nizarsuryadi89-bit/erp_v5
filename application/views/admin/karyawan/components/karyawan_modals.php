<!-- Add/Edit Karyawan Modal -->
<div class="modal fade" id="karyawanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form id="karyawanForm" enctype="multipart/form-data">
                <input type="hidden" name="karyawan_id" id="form_karyawan_id">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-900 ls-1" id="karyawanModalTitle"><i
                            class="bi bi-person-fill-add me-2 text-primary-tgm"></i>TAMBAH KARYAWAN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <ul class="nav nav-pills nav-fill bg-slate-50 rounded-pill p-1 mb-4" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active rounded-pill fw-bold small py-2" id="info-tab"
                                data-bs-toggle="tab" data-bs-target="#tab-info" type="button">1. DATA PERSONAL</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link rounded-pill fw-bold small py-2" id="doc-tab" data-bs-toggle="tab"
                                data-bs-target="#tab-doc" type="button">2. BERKAS DOKUMEN</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Tab Data Personal -->
                        <div class="tab-pane fade show active" id="tab-info">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">NAMA LENGKAP <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control bg-slate-50 border-0 fw-bold rounded-3"
                                        name="nama_lengkap" id="form_nama_lengkap" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">NIK KARYAWAN <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control bg-slate-50 border-0 fw-bold rounded-3"
                                        name="nik" id="form_nik" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted">JABATAN <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select select2-modal" name="jabatan_id" id="form_jabatan_id"
                                        required>
                                        <option value="">Pilih Jabatan</option>
                                        <?php foreach ($all_jabatan as $j): ?>
                                            <option value="<?= $j->jabatan_id ?>"><?= $j->nama_jabatan ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted">DIVISI <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select select2-modal" name="divisi_id" id="form_divisi_id"
                                        required>
                                        <option value="">Pilih Divisi</option>
                                        <?php foreach ($all_divisi as $d): ?>
                                            <option value="<?= $d->divisi_id ?>"><?= $d->divisi_nama ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted">CABANG <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select select2-modal" name="perusahaan_id"
                                        id="form_perusahaan_id" required>
                                        <option value="">Pilih Cabang</option>
                                        <?php foreach ($all_perusahaan as $p): ?>
                                            <option value="<?= $p->id_perusahaan ?>"><?= $p->nama_perusahaan ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">TELEPON / WA <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control bg-slate-50 border-0 fw-bold rounded-3"
                                        name="telepon" id="form_telepon" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">EMAIL</label>
                                    <input type="email" class="form-control bg-slate-50 border-0 fw-bold rounded-3"
                                        name="email" id="form_email">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted">STATUS KERJA</label>
                                    <select class="form-select bg-slate-50 border-0 fw-bold rounded-3"
                                        name="status_karyawan" id="form_status_karyawan">
                                        <option value="Tetap">Tetap</option>
                                        <option value="Kontrak">Kontrak</option>
                                        <option value="Magang">Magang</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted">SISTEM GAJI</label>
                                    <select class="form-select bg-slate-50 border-0 fw-bold rounded-3" name="jenis_gaji"
                                        id="form_jenis_gaji">
                                        <option value="Bulanan">Bulanan</option>
                                        <option value="Harian">Harian</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted">GENDER</label>
                                    <select class="form-select bg-slate-50 border-0 fw-bold rounded-3"
                                        name="jenis_kelamin" id="form_jenis_kelamin">
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted">PIN PRODUKSI</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control bg-slate-50 border-0 fw-bold rounded-start-3"
                                            name="pin_produksi" id="form_pin_produksi" maxlength="6"
                                            placeholder="4-6 Digit">
                                        <button class="btn bg-slate-50 border-0 text-muted rounded-end-3" type="button" onclick="togglePinKaryawan()">
                                            <i class="fas fa-eye" id="icon_toggle_pin"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted">TGL MASUK</label>
                                    <input type="date" class="form-control bg-slate-50 border-0 fw-bold rounded-3"
                                        name="tanggal_masuk" id="form_tanggal_masuk" value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted">TEMPAT LAHIR</label>
                                    <input type="text" class="form-control bg-slate-50 border-0 fw-bold rounded-3"
                                        name="tempat_lahir" id="form_tempat_lahir">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted">TANGGAL LAHIR</label>
                                    <input type="date" class="form-control bg-slate-50 border-0 fw-bold rounded-3"
                                        name="tanggal_lahir" id="form_tanggal_lahir">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">ALAMAT LENGKAP</label>
                                    <textarea class="form-control bg-slate-50 border-0 fw-bold rounded-3" name="alamat"
                                        id="form_alamat" rows="2"></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">FOTO PROFIL</label>
                                    <div class="d-flex align-items-center bg-slate-50 p-2 rounded-3 border">
                                        <div id="photoPreviewWrapper" class="me-3 d-none">
                                            <img src="<?= base_url('assets/images/karyawan/default.png') ?>"
                                                id="photoPreview" class="avatar-clean shadow-sm">
                                        </div>
                                        <input type="file" class="form-control border-0 bg-transparent x-small fw-bold"
                                            name="foto" id="form_foto" accept="image/*">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Dokumen -->
                        <div class="tab-pane fade" id="tab-doc">
                            <div class="alert alert-warning rounded-4 border-0 small mb-4">
                                <i class="fa fa-info-circle me-2"></i> Unggah berkas dalam format PDF atau Gambar (Maks
                                5MB per file).
                            </div>
                            <div class="row g-3">
                                <?php
                                $docs = [
                                    'ktp' => 'KTP (Identitas)',
                                    'kk' => 'Kartu Keluarga',
                                    'npwp' => 'NPWP',
                                    'ijazah' => 'Ijazah Terakhir',
                                    'cv' => 'CV / Resume',
                                    'kontrak' => 'Surat Kontrak',
                                    'pernyataan' => 'Surat Pernyataan',
                                    'form_data' => 'Formulir Data'
                                ];
                                foreach ($docs as $key => $label): ?>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">
                                            <?= $label ?>
                                        </label>
                                        <div class="input-group input-group-sm rounded-3 overflow-hidden shadow-sm">
                                            <span class="input-group-text bg-white border-end-0"><i
                                                    class="fa fa-file-text-o"></i></span>
                                            <input type="file" class="form-control border-start-0"
                                                name="dokumen_<?= $key ?>" accept=".pdf,.jpg,.jpeg,.png">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-slate-50 border fw-bold rounded-pill px-4 x-small"
                        data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-primary-tgm rounded-pill px-5 fw-bold x-small shadow-sm"
                        id="btnSubmitKaryawan">SIMPAN PERUBAHAN</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Karyawan Modal -->
<div class="modal fade" id="viewKaryawanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div id="viewKaryawanBody">
                <div class="text-center py-5">
                    <div class="spinner-grow text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Memuat data...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payroll Settings Modal -->
<div class="modal fade" id="payrollModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form id="payrollForm">
                <input type="hidden" name="karyawan_id" id="payroll_karyawan_id">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-900 ls-1"><i class="bi bi-cash-stack me-2 text-primary-tgm"></i>PAYROLL
                        CONFIG</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 pt-0">
                    <div class="clean-card mb-4 bg-slate-50 border-0 shadow-none"
                        style="border-radius: 12px !important;">
                        <div class="card-body p-3 d-flex align-items-center">
                            <img src="" id="payroll_avatar" class="avatar-clean shadow-sm me-3"
                                style="width: 50px; height: 50px;">
                            <div>
                                <h6 class="fw-bold text-dark mb-0" id="payroll_nama_karyawan">-</h6>
                                <div class="x-small text-muted fw-bold ls-1" id="payroll_posisi_karyawan">-</div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">SISTEM PENGUPAHAN</label>
                            <select class="form-select bg-slate-50 border-0 fw-bold rounded-3" name="jenis_gaji"
                                id="payroll_jenis_gaji">
                                <option value="Bulanan">GAJI BULANAN (TETAP)</option>
                                <option value="Harian">GAJI HARIAN (PER KEHADIRAN)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted" id="label_gaji_pokok">NOMINAL
                                GAJI</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-slate-50 border-end-0 text-dark fw-bold px-3">Rp</span>
                                <input type="text"
                                    class="form-control border-start-0 ps-0 fw-900 font-mono currency-input"
                                    name="gaji_pokok" id="payroll_gaji_pokok" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">UPAH LEMBUR / JAM</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-slate-50 border-end-0 text-dark fw-bold px-3">Rp</span>
                                <input type="text"
                                    class="form-control border-start-0 ps-0 fw-900 font-mono currency-input"
                                    name="nominal_lembur" id="payroll_nominal_lembur" required>
                            </div>
                            <div class="mt-2 x-small text-muted fw-bold">
                                <i class="bi bi-info-circle-fill me-1 text-primary-tgm"></i> Digunakan untuk kalkulasi
                                otomatis lembur kerjaan & bulanan.
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">TUNJANGAN JABATAN</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-slate-50 border-end-0 text-dark fw-bold px-3">Rp</span>
                                <input type="text"
                                    class="form-control border-start-0 ps-0 fw-900 font-mono currency-input"
                                    name="tunjangan_jabatan" id="payroll_tunjangan_jabatan" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">TUNJANGAN TRANSPORTASI (5%)</label>
                            <select class="form-select bg-slate-50 border-0 fw-bold rounded-3" name="transport_aktif"
                                id="payroll_transport_aktif">
                                <option value="1">AKTIF (DAPATKAN 5%)</option>
                                <option value="0">NON-AKTIF (RP 0)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-slate-50 border fw-bold rounded-pill px-4 x-small"
                        data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-emerald text-white fw-bold rounded-pill px-5 x-small shadow-sm"
                        id="btnSubmitPayroll">
                        <i class="bi bi-check-circle-fill me-2"></i>SIMPAN DATA PAYROLL
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Cabang Karyawan -->
<div class="modal fade" id="modalBranchEmployees" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 p-4">
                <h5 class="modal-title fw-bold" id="branchTitle"><i
                        class="fa fa-building text-primary-tgm me-2"></i>Daftar
                    Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="branchTable">
                        <thead class="bg-light shadow-sm">
                            <tr>
                                <th class="ps-4">Nama Lengkap</th>
                                <th>Posisi / Jabatan</th>
                                <th class="text-center pe-4">Gender</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 p-3">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- SOP Guide Modal -->
<div class="modal fade" id="modalSOPKaryawan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="bg-primary-tgm p-4 text-white position-relative">
                <div class="position-relative z-index-1">
                    <h5 class="modal-title fw-900 ls-1 mb-0"><i class="bi bi-info-circle-fill me-2"></i>PANDUAN SOP
                        ADMINISTRASI KARYAWAN</h5>
                    <p class="small mb-0 opacity-75 mt-1">Standar Prosedur Operasional Manajemen SDM - TGM ERP</p>
                </div>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-4"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="bg-slate-50 p-4 rounded-4 h-100 border border-primary-tgm border-opacity-10">
                            <h6 class="fw-900 text-primary-tgm mb-3"><i class="bi bi-person-plus-fill me-2"></i>ENTRI
                                DATA
                                BARU</h6>
                            <ul class="small text-muted mb-0 ps-3 lh-lg">
                                <li>Pastikan <b>NIK</b> sesuai dengan KTP asli.</li>
                                <li>Nomor <b>WhatsApp</b> wajib aktif untuk Slip Gaji.</li>
                                <li><b>Join Date</b> menentukan akurasi perhitungan THR.</li>
                                <li>Unggah berkas KTP & Kontrak pada tab Berkas.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-slate-50 p-4 rounded-4 h-100 border border-emerald border-opacity-10">
                            <h6 class="fw-900 text-emerald mb-3"><i class="bi bi-cash-coin me-2"></i>PENGATURAN PAYROLL
                            </h6>
                            <ul class="small text-muted mb-0 ps-3 lh-lg">
                                <li>Gunakan tombol <b>AKSI > GAJI & LEMBUR</b>.</li>
                                <li>Input <b>Gaji Pokok</b> sesuai kontrak terbaru.</li>
                                <li><b>Upah Lembur/Jam</b> dihitung dari Gaji/173.</li>
                                <li>Cek tab <b>Payroll</b> untuk validasi final.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="bg-primary-tgm bg-opacity-10 p-4 rounded-4">
                            <h6 class="fw-900 text-primary-tgm mb-2 small"><i
                                    class="bi bi-shield-check me-2"></i>CATATAN
                                PENTING</h6>
                            <p class="small text-dark fw-bold mb-0">
                                Segala perubahan data karyawan akan tercatat di System Logs. Indikator <span
                                    class="badge bg-warning text-dark px-2">Cek Kontrak</span> akan muncul otomatis jika
                                tenure mencapai 22-24 bulan untuk mengingatkan perpanjangan kontrak.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button"
                    class="btn btn-primary-tgm w-100 rounded-pill fw-900 small py-3 shadow-primary-tgm"
                    data-bs-dismiss="modal">SAYA MENGERTI & SIAP BEKERJA</button>
            </div>
        </div>
    </div>
</div>