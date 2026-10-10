<style>
    /* Edit Attendance Styles */
    .edit-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 0 0 20px 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }

    .edit-form {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        margin-bottom: 2rem;
    }

    .edit-form h5 {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 0.75rem;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-control:focus {
        border-color: #2c3e50;
        box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.25);
        outline: none;
    }

    .form-control.is-invalid {
        border-color: #e74c3c;
        box-shadow: 0 0 0 0.2rem rgba(231, 76, 60, 0.25);
    }

    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: #e74c3c;
    }

    .btn-group {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .btn-save {
        background: linear-gradient(135deg, #27ae60, #2ecc71);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(39, 174, 96, 0.4);
        color: white;
    }

    .btn-cancel {
        background: linear-gradient(135deg, #95a5a6, #7f8c8d);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-cancel:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(149, 165, 166, 0.4);
        color: white;
        text-decoration: none;
    }

    .attendance-info {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border-left: 4px solid #2c3e50;
    }

    .attendance-info h6 {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-label {
        font-weight: 600;
        color: #2c3e50;
        min-width: 100px;
    }

    .info-value {
        color: #6c757d;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-hadir {
        background: #d4edda;
        color: #155724;
    }

    .status-izin {
        background: #fff3cd;
        color: #856404;
    }

    .status-sakit {
        background: #d1ecf1;
        color: #0c5460;
    }

    .status-alpha {
        background: #f8d7da;
        color: #721c24;
    }

    @media (max-width: 767.98px) {
        .edit-form {
            padding: 1.5rem;
        }

        .btn-group {
            flex-direction: column;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="app-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="edit-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="fw-bold mb-2">Edit Data Absensi</h2>
                    <h4 class="fw-light mb-0"><?= $perusahaan ?></h4>
                    <p class="mb-0 opacity-75">Perbarui informasi kehadiran karyawan</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-flex align-items-center justify-content-md-end gap-3">
                        <div class="text-center">
                            <div class="h4 mb-0"><?= date('d') ?></div>
                            <small class="opacity-75"><?= date('M Y') ?></small>
                        </div>
                        <div class="text-center">
                            <div class="h4 mb-0"><?= date('H:i') ?></div>
                            <small class="opacity-75">Waktu</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Attendance Info -->
        <div class="attendance-info">
            <h6><i class="fa fa-info-circle"></i> Informasi Absensi Saat Ini</h6>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Nama:</span>
                    <span class="info-value"><?= $absensi->nama_lengkap ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">NIK:</span>
                    <span class="info-value"><?= $absensi->nik ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tanggal:</span>
                    <span class="info-value"><?= date('d F Y', strtotime($absensi->tanggal)) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span class="status-badge status-<?= strtolower($absensi->status) ?>">
                        <?= $absensi->status ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="edit-form">
            <h5><i class="fa fa-edit text-dark"></i> Edit Data Absensi</h5>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa fa-exclamation-triangle me-2"></i>
                    <?= $this->session->flashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= base_url('absensi/update') ?>" id="editForm">
                <input type="hidden" name="id" value="<?= $absensi->id ?>">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="karyawan_id">Karyawan <span class="text-danger">*</span></label>
                            <select class="form-control" id="karyawan_id" name="karyawan_id" required>
                                <option value="">Pilih Karyawan</option>
                                <?php foreach ($karyawan_list as $karyawan): ?>
                                    <option value="<?= $karyawan->karyawan_id ?>"
                                        <?= $absensi->karyawan_id == $karyawan->karyawan_id ? 'selected' : '' ?>>
                                        <?= $karyawan->nama_lengkap ?> (<?= $karyawan->nik ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal"
                                value="<?= $absensi->tanggal ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="jam_masuk">Jam Masuk</label>
                            <input type="time" class="form-control" id="jam_masuk" name="jam_masuk"
                                value="<?= $absensi->jam_masuk ? date('H:i', strtotime($absensi->jam_masuk)) : '' ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="jam_pulang">Jam Pulang</label>
                            <input type="time" class="form-control" id="jam_pulang" name="jam_pulang"
                                value="<?= $absensi->jam_pulang ? date('H:i', strtotime($absensi->jam_pulang)) : '' ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="">Pilih Status</option>
                                <option value="Hadir" <?= $absensi->status == 'Hadir' ? 'selected' : '' ?>>Hadir</option>
                                <option value="Izin" <?= $absensi->status == 'Izin' ? 'selected' : '' ?>>Izin</option>
                                <option value="Sakit" <?= $absensi->status == 'Sakit' ? 'selected' : '' ?>>Sakit</option>
                                <option value="Cuti" <?= $absensi->status == 'Cuti' ? 'selected' : '' ?>>Cuti Tahunan
                                </option>
                                <option value="Cuti Keluarga" <?= $absensi->status == 'Cuti Keluarga' ? 'selected' : '' ?>>
                                    Cuti Keluarga</option>
                                <option value="Cuti Sakit" <?= $absensi->status == 'Cuti Sakit' ? 'selected' : '' ?>>Cuti
                                    Sakit</option>
                                <option value="Cuti Melahirkan" <?= $absensi->status == 'Cuti Melahirkan' ? 'selected' : '' ?>>Cuti Melahirkan</option>
                                <option value="Cuti Menikah" <?= $absensi->status == 'Cuti Menikah' ? 'selected' : '' ?>>
                                    Cuti Menikah</option>
                                <option value="Alpha" <?= $absensi->status == 'Alpha' ? 'selected' : '' ?>>Alpha</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"
                                placeholder="Masukkan keterangan (opsional)"><?= $absensi->keterangan ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-save">
                        <i class="fa fa-save"></i> Simpan Perubahan
                    </button>
                    <a href="<?= base_url('absensi') ?>" class="btn btn-cancel">
                        <i class="fa fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('editForm');
        const karyawanSelect = document.getElementById('karyawan_id');
        const tanggalInput = document.getElementById('tanggal');
        const jamMasukInput = document.getElementById('jam_masuk');
        const jamPulangInput = document.getElementById('jam_pulang');
        const statusSelect = document.getElementById('status');

        // Form validation
        form.addEventListener('submit', function (e) {
            let isValid = true;

            // Reset previous validation states
            document.querySelectorAll('.form-control').forEach(input => {
                input.classList.remove('is-invalid');
            });
            document.querySelectorAll('.invalid-feedback').forEach(feedback => {
                feedback.remove();
            });

            // Validate required fields
            if (!karyawanSelect.value) {
                showError(karyawanSelect, 'Karyawan harus dipilih');
                isValid = false;
            }

            if (!tanggalInput.value) {
                showError(tanggalInput, 'Tanggal harus diisi');
                isValid = false;
            }

            if (!statusSelect.value) {
                showError(statusSelect, 'Status harus dipilih');
                isValid = false;
            }

            // Validate time logic
            if (jamMasukInput.value && jamPulangInput.value) {
                if (jamMasukInput.value >= jamPulangInput.value) {
                    showError(jamPulangInput, 'Jam pulang harus lebih besar dari jam masuk');
                    isValid = false;
                }
            }

            // Validate date not in future
            const selectedDate = new Date(tanggalInput.value);
            const today = new Date();
            today.setHours(23, 59, 59, 999); // End of today

            if (selectedDate > today) {
                showError(tanggalInput, 'Tanggal tidak boleh di masa depan');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });

        function showError(input, message) {
            input.classList.add('is-invalid');
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = message;
            input.parentNode.appendChild(feedback);
        }

        // Auto-fill time based on status
        statusSelect.addEventListener('change', function () {
            if (this.value === 'Hadir') {
                if (!jamMasukInput.value) {
                    jamMasukInput.value = '08:00';
                }
            } else {
                jamMasukInput.value = '';
                jamPulangInput.value = '';
            }
        });

        // Auto-calculate work hours
        function calculateWorkHours() {
            if (jamMasukInput.value && jamPulangInput.value) {
                const masuk = new Date('2000-01-01 ' + jamMasukInput.value);
                const pulang = new Date('2000-01-01 ' + jamPulangInput.value);

                if (pulang > masuk) {
                    const diffMs = pulang - masuk;
                    const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
                    const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

                    console.log(`Jam kerja: ${diffHours} jam ${diffMinutes} menit`);
                }
            }
        }

        jamMasukInput.addEventListener('change', calculateWorkHours);
        jamPulangInput.addEventListener('change', calculateWorkHours);
    });
</script>