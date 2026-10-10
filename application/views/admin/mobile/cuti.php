<!-- Page Header -->
<div class="custom-page-header">
    <h5><i class="bi bi-calendar2-heart-fill me-2 text-success"></i>Permohonan Cuti</h5>
    <p>Kelola rencana libur dan izin khusus Anda</p>
</div>

<!-- Main Content -->
<div class="content-container">

    <!-- Quota Information -->
    <div class="stat-card border-0 text-white mb-4"
        style="background: linear-gradient(135deg, #1e293b, #0f172a); border-radius: 25px;">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart-fill me-2 text-warning"></i><span id="stTitle">Status Kuota <?= date('Y') ?></span>
            </h6>
            <span class="badge bg-white text-dark rounded-pill shadow-sm px-3" id="stSisaPill"><?= $sisa_quota ?> Hari Sisa</span>
        </div>
        <div class="row text-center g-2 mt-2">
            <div class="col-3">
                <div class="bg-white bg-opacity-10 rounded-4 py-2">
                    <h5 class="mb-0 fw-bold" style="font-size: 1rem;" id="stAwal"><?= $quota_total ?></h5>
                    <small class="opacity-50" style="font-size: 0.55rem;">Awal</small>
                </div>
            </div>
            <div class="col-3">
                <div class="bg-white bg-opacity-10 rounded-4 py-2 border border-success border-opacity-25">
                    <h5 class="mb-0 fw-bold text-success" style="font-size: 1rem;" id="stDisetujui"><?= $used_approved ?></h5>
                    <small class="opacity-50" style="font-size: 0.55rem;">Disetujui</small>
                </div>
            </div>
            <div class="col-3">
                <div class="bg-white bg-opacity-10 rounded-4 py-2 border border-warning border-opacity-25">
                    <h5 class="mb-0 fw-bold text-warning" style="font-size: 1rem;" id="stPending"><?= $used_pending ?></h5>
                    <small class="opacity-50" style="font-size: 0.55rem;">Pending</small>
                </div>
            </div>
            <div class="col-3">
                <div class="bg-white bg-opacity-10 rounded-4 py-2 border border-info border-opacity-25">
                    <h5 class="mb-0 fw-bold text-info" style="font-size: 1rem;" id="stSisa"><?= $sisa_quota ?></h5>
                    <small class="opacity-50" style="font-size: 0.55rem;">Sisa</small>
                </div>
            </div>
        </div>
    </div>

    <?php if (!$eligible_tahunan): ?>
        <div class="alert alert-warning border-0 shadow-sm mb-4" style="border-radius: 20px; background: #fffbeb;">
            <div class="d-flex gap-3">
                <i class="bi bi-exclamation-triangle-fill fs-3 text-warning"></i>
                <div>
                    <h6 class="fw-bold text-dark mb-1">Masa Kerja < 1 Tahun</h6>
                            <p class="small text-muted mb-0">Anda hanya dapat mengambil Cuti Khusus hingga masa kerja
                                mencapai 12 bulan.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Form Pengajuan -->
    <div class="stat-card shadow-sm border-0 mb-4">
        <style>
            .premium-label {
                font-size: 0.75rem;
                font-weight: 700;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 0.5rem;
                display: block;
            }

            .premium-input {
                border: 1.5px solid #e2e8f0;
                border-radius: 12px;
                padding: 0.75rem 1rem;
                font-size: 0.95rem;
                transition: all 0.2s;
                background: #f8fafc;
            }

            .premium-input:focus {
                border-color: var(--accent);
                background: #fff;
                box-shadow: 0 0 0 4px rgba(15, 52, 96, 0.1);
                outline: none;
            }

            .premium-select {
                appearance: none;
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
                background-repeat: no-repeat;
                background-position: right 1rem center;
                background-size: 16px 12px;
            }
        </style>

        <form action="<?= base_url('mobile/cuti_save') ?>" method="post" enctype="multipart/form-data" id="cutiForm">
            <div class="mb-3">
                <label class="premium-label">Tipe Cuti</label>
                <select name="tipe_cuti" id="tipe_cuti" class="premium-input premium-select w-100" required>
                    <option value="" selected disabled>-- Pilih Jenis --</option>
                    <option value="cuti" <?= (!$eligible_tahunan || $sisa_quota <= 0) ? 'disabled' : '' ?>>Cuti
                        Tahunan (Sisa: <?= $sisa_quota ?> Hari)</option>
                    <option value="cuti menikah" <?= ($menikah_sisa <= 0) ? 'disabled' : '' ?>>Cuti Menikah (Sisa: <?= $menikah_sisa ?> Hari)</option>
                    <option value="cuti duka" <?= ($duka_sisa <= 0) ? 'disabled' : '' ?>>Duka Cita (Sisa: <?= $duka_sisa ?> Hari)</option>
                    <option value="cuti keluarga sakit" <?= ($sakitkel_sisa <= 0) ? 'disabled' : '' ?>>Keluarga Sakit (Sisa: <?= $sakitkel_sisa ?> Hari)</option>
                    <option value="cuti melahirkan suami" <?= ($melahirkansuami_sisa <= 0) ? 'disabled' : '' ?>>Cuti Melahirkan Bagi Suami (Sisa: <?= $melahirkansuami_sisa ?> Hari)</option>
                    <option value="cuti melahirkan" <?= ($melahirkan_sisa <= 0) ? 'disabled' : '' ?>>Cuti Melahirkan (Sisa: <?= $melahirkan_sisa ?> Hari)</option>
                </select>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-7">
                    <label class="premium-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="premium-input w-100" required
                        value="<?= date('Y-m-d') ?>">
                    <p class="text-danger mt-1" style="font-size: 0.65rem; line-height: 1.2;">
                        *Jika di tengah hari cuti ada Tanggal Merah / Hari Minggu, silakan ajukan kembali pada hari
                        kerja.
                    </p>
                </div>
                <div class="col-5">
                    <label class="premium-label">Lama (Hari)</label>
                    <input type="number" name="jumlah_hari" id="jumlah_hari" class="premium-input w-100" placeholder="0"
                        min="1" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="premium-label">Keperluan</label>
                <textarea name="keperluan" class="premium-input w-100" rows="3"
                    placeholder="Contoh: Acara keluarga, Keperluan mendesak, dll" required></textarea>
            </div>

            <div class="mb-4">
                <label class="premium-label">Lampiran Bukti (Opsional)</label>
                <div class="upload-container position-relative" style="cursor: pointer;">
                    <input type="file" name="dokumen" id="dokumen" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" accept=".pdf,.jpg,.jpeg,.png" style="cursor: pointer; z-index: 10;">
                    <div class="upload-ui border border-2 border-dashed rounded-4 p-4 text-center" style="border-color: #cbd5e1; background: #f8fafc; transition: all 0.3s;">
                        <i class="bi bi-cloud-arrow-up-fill fs-2 text-primary-tgm opacity-75 mb-2 d-block"></i>
                        <h6 class="fw-bold text-dark mb-1" id="fileName" style="font-size: 0.85rem;">Ketuk untuk unggah file</h6>
                        <p class="small text-muted mb-0" style="font-size: 0.65rem;">Max file: 10MB (PDF/JPG/PNG)</p>
                    </div>
                </div>
            </div>

            <button type="submit" id="submitBtn" class="action-btn w-100 py-3"
                style="background: linear-gradient(135deg, #16a34a, #15803d);">
                <i class="bi bi-send-check-fill me-2"></i> KIRIM PERMOHONAN
            </button>
        </form>
    </div>

    <!-- History -->
    <div class="section-header">
        <div class="section-title"><i class="bi bi-clock-history me-2"></i>Riwayat Pengajuan</div>
    </div>
    <div class="history-list">
        <?php if (!empty($history)): ?>
            <?php foreach ($history as $h): ?>
                <div class="stat-card py-3 px-4 mb-2 shadow-sm border-0">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="mb-1">
                                <?php
                                $type_label = str_replace('_', ' ', $h->tipe_izin);
                                $type_color = 'text-primary';
                                if (strpos($h->tipe_izin, 'sakit') !== false)
                                    $type_color = 'text-danger';
                                if (strpos($h->tipe_izin, 'duka') !== false || strpos($h->tipe_izin, 'meninggal') !== false)
                                    $type_color = 'text-dark';
                                ?>
                                <span class="badge bg-light <?= $type_color ?> border p-1 px-2 text-uppercase"
                                    style="font-size: 0.6rem; letter-spacing: 0.5px;">
                                    <?= $type_label ?>
                                </span>
                            </div>
                            <span
                                class="fw-bold text-dark small d-block"><?= date('d F Y', strtotime($h->tanggal_mulai)) ?></span>
                            <small class="text-muted" style="font-size: 0.65rem;">Durasi: <?= $h->durasi_jumlah ?> Hari</small>
                        </div>
                        <?php
                        $badge_class = 'bg-warning-subtle text-warning';
                        if ($h->status == 'Approved' || $h->status == 'Disetujui')
                            $badge_class = 'bg-success-subtle text-success';
                        if ($h->status == 'Rejected' || $h->status == 'Ditolak')
                            $badge_class = 'bg-danger-subtle text-danger';
                        ?>
                        <span class="badge <?= $badge_class ?> rounded-pill px-3"><?= $h->status ?></span>
                    </div>
                    <p class="text-muted small mb-0 fst-italic">"<?= htmlspecialchars($h->keterangan ?? '-') ?>"</p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-4 bg-white rounded-4 border border-dashed">
                <p class="text-muted small mb-0">Belum ada riwayat cuti.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    <?php if ($this->session->flashdata('success')): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '<?= $this->session->flashdata('success') ?>',
            borderRadius: '20px'
        });
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        Swal.fire({
            icon: 'error',
            title: 'Opps...',
            text: '<?= $this->session->flashdata('error') ?>',
            borderRadius: '20px'
        });
    <?php endif; ?>

    document.getElementById('cutiForm').addEventListener('submit', function (e) {
        e.preventDefault();

        // Simple validation
        const tipe = document.getElementById('tipe_cuti').value;
        const hari = document.getElementById('jumlah_hari').value;
        const keperluan = document.querySelector('textarea[name="keperluan"]').value;
        const submitBtn = document.getElementById('submitBtn');

        if (!tipe || !hari || !keperluan) {
            Swal.fire({
                icon: 'error',
                title: 'Data Belum Lengkap',
                text: 'Harap isi semua data (Tipe, Lama Hari, dan Keperluan) sebelum mengirim.',
                borderRadius: '20px'
            });
            return;
        }

        Swal.fire({
            title: 'Kirim Permohonan?',
            text: 'Permohonan akan diteruskan ke HRD untuk ditinjau.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            confirmButtonText: 'Ya, Ajukan',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            borderRadius: '20px'
        }).then((result) => {
            if (result.isConfirmed) {
                // Disable the submit button immediately to prevent double clicks
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> MEMPROSES...';
                }
                
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang mengirim permohonan, mohon tunggu.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                document.getElementById('cutiForm').submit();
            }
        });
    });
    document.getElementById('dokumen').addEventListener('change', function(e) {
        const fileName = e.target.files[0] ? e.target.files[0].name : 'Ketuk untuk unggah file';
        const ui = document.querySelector('.upload-ui');
        document.getElementById('fileName').textContent = fileName;
        
        if(e.target.files[0]) {
            ui.style.borderColor = '#16a34a';
            ui.style.background = '#f0fdf4';
            ui.querySelector('i').classList.replace('bi-cloud-arrow-up-fill', 'bi-check-circle-fill');
            ui.querySelector('i').classList.replace('text-primary-tgm', 'text-success');
        } else {
            ui.style.borderColor = '#cbd5e1';
            ui.style.background = '#f8fafc';
            ui.querySelector('i').classList.replace('bi-check-circle-fill', 'bi-cloud-arrow-up-fill');
            ui.querySelector('i').classList.replace('text-success', 'text-primary-tgm');
        }
    });

    // Dynamic Quota Widget Logic
    const quotaData = {
        'cuti': {
            title: 'Tahunan <?= date('Y') ?>',
            awal: <?= $quota_total ?>,
            disetujui: <?= $used_approved ?>,
            pending: <?= $used_pending ?>,
            sisa: <?= $sisa_quota ?>
        },
        'cuti menikah': {
            title: 'Menikah',
            awal: <?= isset($limits['cuti menikah']) ? $limits['cuti menikah'] : 7 ?>,
            disetujui: <?= isset($usage_special_approved['cuti menikah']) ? $usage_special_approved['cuti menikah'] : 0 ?>,
            pending: <?= isset($usage_special_pending['cuti menikah']) ? $usage_special_pending['cuti menikah'] : 0 ?>,
            sisa: <?= isset($menikah_sisa) ? $menikah_sisa : 0 ?>
        },
        'cuti duka': {
            title: 'Duka Cita',
            awal: <?= isset($limits['cuti duka']) ? $limits['cuti duka'] : 3 ?>,
            disetujui: <?= isset($usage_special_approved['cuti duka']) ? $usage_special_approved['cuti duka'] : 0 ?>,
            pending: <?= isset($usage_special_pending['cuti duka']) ? $usage_special_pending['cuti duka'] : 0 ?>,
            sisa: <?= isset($duka_sisa) ? $duka_sisa : 0 ?>
        },
        'cuti keluarga sakit': {
            title: 'Keluarga Sakit',
            awal: <?= isset($limits['cuti keluarga sakit']) ? $limits['cuti keluarga sakit'] : 2 ?>,
            disetujui: <?= isset($usage_special_approved['cuti keluarga sakit']) ? $usage_special_approved['cuti keluarga sakit'] : 0 ?>,
            pending: <?= isset($usage_special_pending['cuti keluarga sakit']) ? $usage_special_pending['cuti keluarga sakit'] : 0 ?>,
            sisa: <?= isset($sakitkel_sisa) ? $sakitkel_sisa : 0 ?>
        },
        'cuti melahirkan suami': {
            title: 'Istri Melahirkan',
            awal: <?= isset($limits['cuti melahirkan suami']) ? $limits['cuti melahirkan suami'] : 3 ?>,
            disetujui: <?= isset($usage_special_approved['cuti melahirkan suami']) ? $usage_special_approved['cuti melahirkan suami'] : 0 ?>,
            pending: <?= isset($usage_special_pending['cuti melahirkan suami']) ? $usage_special_pending['cuti melahirkan suami'] : 0 ?>,
            sisa: <?= isset($melahirkansuami_sisa) ? $melahirkansuami_sisa : 0 ?>
        },
        'cuti melahirkan': {
            title: 'Cuti Melahirkan',
            awal: <?= isset($limits['cuti melahirkan']) ? $limits['cuti melahirkan'] : 90 ?>,
            disetujui: <?= isset($usage_special_approved['cuti melahirkan']) ? $usage_special_approved['cuti melahirkan'] : 0 ?>,
            pending: <?= isset($usage_special_pending['cuti melahirkan']) ? $usage_special_pending['cuti melahirkan'] : 0 ?>,
            sisa: <?= isset($melahirkan_sisa) ? $melahirkan_sisa : 0 ?>
        }
    };

    document.getElementById('tipe_cuti').addEventListener('change', function(e) {
        const type = this.value;
        const data = quotaData[type];
        if (data) {
            document.getElementById('stTitle').textContent = 'Status Kuota ' + data.title;
            document.getElementById('stAwal').textContent = data.awal;
            document.getElementById('stDisetujui').textContent = data.disetujui;
            document.getElementById('stPending').textContent = data.pending;
            document.getElementById('stSisa').textContent = data.sisa;
            document.getElementById('stSisaPill').textContent = data.sisa + ' Hari Sisa';
        } else {
            // Revert to annual if empty
            const def = quotaData['cuti'];
            document.getElementById('stTitle').textContent = 'Status Kuota ' + def.title;
            document.getElementById('stAwal').textContent = def.awal;
            document.getElementById('stDisetujui').textContent = def.disetujui;
            document.getElementById('stPending').textContent = def.pending;
            document.getElementById('stSisa').textContent = def.sisa;
            document.getElementById('stSisaPill').textContent = def.sisa + ' Hari Sisa';
        }
    });

</script>