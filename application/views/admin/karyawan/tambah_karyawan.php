<link rel="stylesheet" href="<?= base_url('assets/css/tgm-form-karyawan.css') ?>">
<div class="app-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="employee-header">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-7">
                    <div class="header-content">
                        <div class="header-icon">
                            <i class="fa fa-user-plus"></i>
                        </div>
                        <div class="header-text">
                            <h1 class="header-title">Tambah Karyawan Baru</h1>
                            <!-- <h3 class="header-subtitle"><?= $perusahaan ?></h3> -->
                            <p class="header-description">Masukkan informasi lengkap karyawan baru untuk menambahkan ke
                                dalam sistem</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-5">
                    <div class="info-card">
                        <a href="<?= base_url('karyawan') ?>" style="color: white; text-decoration: none;">
                            <i class="fa fa-arrow-left"></i>
                            Kembali
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <!-- Form Container -->
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="form-container">
                    <div class="form-header">
                        <h5>
                            <i class="fa fa-user-plus"></i>
                            Form Data Karyawan
                        </h5>
                    </div>

                    <form action="<?= base_url('karyawan/simpan_karyawan') ?>" method="post"
                        enctype="multipart/form-data">
                        <input type="hidden" name="karyawan_id" value="<?= $karyawan->karyawan_id ?>">

                        <div class="form-body">
                            <!-- Personal Information Section -->
                            <div class="form-section">
                                <h6 class="section-title">
                                    <i class="fa fa-user"></i>
                                    Informasi Pribadi
                                </h6>

                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label class="form-label required-field">Nama Lengkap</label>
                                        <input type="text" name="nama_lengkap" class="form-control"
                                            value="<?= $karyawan->nama_lengkap ?>" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">NIK</label>
                                        <input type="text" name="nip" class="form-control"
                                            value="<?= $karyawan->nik ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jenis Kelamin</label>
                                        <select name="jenis_kelamin" class="form-select">
                                            <option value="L" <?= $karyawan->jenis_kelamin == 'L' ? 'selected' : '' ?>>
                                                Laki-laki</option>
                                            <option value="P" <?= $karyawan->jenis_kelamin == 'P' ? 'selected' : '' ?>>
                                                Perempuan</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tanggal Lahir</label>
                                        <input type="date" name="tanggal_lahir" class="form-control"
                                            value="<?= $karyawan->tanggal_lahir ?>">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Alamat</label>
                                    <textarea name="alamat" class="form-control"
                                        rows="3"><?= $karyawan->alamat ?></textarea>
                                </div>
                            </div>

                            <!-- Work Information Section -->
                            <div class="form-section">
                                <h6 class="section-title">
                                    <i class="fa fa-briefcase"></i>
                                    Informasi Pekerjaan
                                </h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jabatan</label>
                                        <select name="jabatan_id" class="form-select">
                                            <?php foreach ($jabatan as $j): ?>
                                                <option value="<?= $j->jabatan_id ?>"
                                                    <?= $karyawan->jabatan_id == $j->jabatan_id ? 'selected' : '' ?>>
                                                    <?= $j->nama_jabatan ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Divisi</label>
                                        <select name="divisi_id" class="form-select">
                                            <?php foreach ($divisi as $d): ?>
                                                <option value="<?= $d->divisi_id ?>"
                                                    <?= $karyawan->divisi_id == $d->divisi_id ? 'selected' : '' ?>>
                                                    <?= $d->divisi_nama ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Lokasi Perusahaan</label>
                                        <select name="perusahaan_id" class="form-select">
                                            <?php foreach ($perusahaan as $l): ?>
                                                <option value="<?= $l->id_perusahaan ?>"
                                                    <?= $karyawan->perusahaan_id == $l->id_perusahaan ? 'selected' : '' ?>>
                                                    <?= $l->nama_perusahaan ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Status Karyawan</label>
                                        <select name="status_karyawan" id="status_karyawan" class="form-select">
                                            <option value="Tetap" <?= $karyawan->status_karyawan == 'Tetap' ? 'selected' : '' ?>>Tetap</option>
                                            <option value="Kontrak" <?= $karyawan->status_karyawan == 'Kontrak' ? 'selected' : '' ?>>Kontrak</option>
                                            <option value="Magang" <?= $karyawan->status_karyawan == 'Magang' ? 'selected' : '' ?>>Magang</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Sistem Gaji <span class="text-info" style="font-size: 0.8rem;">(Default: Bulanan)</span></label>
                                        <select name="jenis_gaji" class="form-select">
                                            <option value="Bulanan" <?= $karyawan->jenis_gaji == 'Bulanan' ? 'selected' : '' ?>>Bulanan (Sesuai Gaji Pokok)</option>
                                            <option value="Harian" <?= $karyawan->jenis_gaji == 'Harian' ? 'selected' : '' ?>>Harian (Gaji Pokok x Kehadiran)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">PIN Produksi (4-6 Digit)</label>
                                        <input type="password" name="pin_produksi" class="form-control" maxlength="6"
                                            placeholder="PIN Verifikasi Produksi" value="<?= $karyawan->pin_produksi ?>" inputmode="numeric">
                                        <div class="form-text small">Digunakan untuk verifikasi aksi di modul WIP.</div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Status Tunjangan Transportasi</label>
                                        <select name="transport_aktif" class="form-select">
                                            <option value="1" selected>AKTIF (Dapatkan Tunjangan 5%)</option>
                                            <option value="0">NON-AKTIF (Tidak Mendapatkan Transport)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tanggal Masuk</label>
                                        <input type="date" name="tanggal_masuk" class="form-control"
                                            value="<?= $karyawan->tanggal_masuk ?>">
                                    </div>
                                    <div class="col-md-6 mb-3" id="container_akhir_kontrak" style="display: none;">
                                        <label class="form-label">Tanggal Akhir Kontrak <span class="text-danger">*</span></label>
                                        <input type="date" name="akhir_kontrak" id="akhir_kontrak" class="form-control"
                                            value="<?= $karyawan->akhir_kontrak ?? '' ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information Section -->
                            <div class="form-section">
                                <h6 class="section-title">
                                    <i class="fa fa-phone"></i>
                                    Informasi Kontak
                                </h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Telepon</label>
                                        <input type="text" name="telepon" class="form-control"
                                            value="<?= $karyawan->telepon ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control"
                                            value="<?= $karyawan->email ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Documents Section -->
                        <div class="form-section">
                            <h6 class="section-title">
                                <i class="fa fa-file-text"></i>
                                Dokumen Kelengkapan
                            </h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">KTP (Identitas Utama)</label>
                                    <input type="file" name="dokumen_ktp" class="form-control"
                                        accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kartu Keluarga (KK)</label>
                                    <input type="file" name="dokumen_kk" class="form-control"
                                        accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">NPWP (Optional)</label>
                                    <input type="file" name="dokumen_npwp" class="form-control"
                                        accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Ijazah Terakhir</label>
                                    <input type="file" name="dokumen_ijazah" class="form-control"
                                        accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">CV / Daftar Riwayat Hidup</label>
                                    <input type="file" name="dokumen_cv" class="form-control"
                                        accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                            </div>

                            <h6 class="mt-4 mb-3 text-muted"
                                style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">Dokumen
                                Kepegawaian (HR Internal)</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Surat Kontrak Kerja (PKWT/PKWTT)</label>
                                    <input type="file" name="dokumen_kontrak" class="form-control" accept=".pdf">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Surat Pernyataan Karyawan</label>
                                    <input type="file" name="dokumen_pernyataan" class="form-control" accept=".pdf">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Form Data Karyawan (Scan/PDF)</label>
                                    <input type="file" name="dokumen_form_data" class="form-control"
                                        accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                            </div>
                        </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="<?= base_url('karyawan') ?>" class="btn btn-secondary">
                        <i class="fa fa-times"></i>
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i>
                        Simpan Data
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Add form validation
        const form = document.querySelector('form');
        const requiredFields = form.querySelectorAll('.required-field');

        form.addEventListener('submit', function (e) {
            let isValid = true;

            requiredFields.forEach(field => {
                const input = field.querySelector('input, select, textarea');
                if (!input || !input.value.trim()) {
                    isValid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang wajib diisi (ditandai dengan *)');
            }
        });

        // Add input focus effects
        const inputs = document.querySelectorAll('.form-control, .form-select');
        inputs.forEach(input => {
            input.addEventListener('focus', function () {
                this.parentElement.classList.add('focused');
            });

            input.addEventListener('blur', function () {
                this.parentElement.classList.remove('focused');
            });
        });

        // Add character counter for textarea
        const textarea = document.querySelector('textarea[name="alamat"]');
        if (textarea) {
            const counter = document.createElement('small');
            counter.className = 'text-muted mt-1 d-block';
            textarea.parentElement.appendChild(counter);

            function updateCounter() {
                const length = textarea.value.length;
                counter.textContent = `${length} karakter`;
            }

            textarea.addEventListener('input', updateCounter);
            updateCounter();
        }

        // Toggle Tanggal Akhir Kontrak visibility
        const statusSelect = document.getElementById('status_karyawan');
        const akhirKontrakContainer = document.getElementById('container_akhir_kontrak');
        const akhirKontrakInput = document.getElementById('akhir_kontrak');

        function toggleAkhirKontrak() {
            if (statusSelect.value === 'Kontrak' || statusSelect.value === 'Magang') {
                akhirKontrakContainer.style.display = 'block';
                akhirKontrakInput.setAttribute('required', 'required');
            } else {
                akhirKontrakContainer.style.display = 'none';
                akhirKontrakInput.removeAttribute('required');
                akhirKontrakInput.value = '';
            }
        }

        if (statusSelect) {
            statusSelect.addEventListener('change', toggleAkhirKontrak);
            toggleAkhirKontrak(); // Run once on load
        }
    });
</script>