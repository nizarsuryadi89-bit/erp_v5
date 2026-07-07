<!-- Profile Header -->
<div class="profile-header">
    <div class="profile-avatar-wrapper">
        <div class="profile-avatar">
            <?php if (!empty($karyawan_terpilih['foto']) && file_exists('assets/images/karyawan/' . $karyawan_terpilih['foto'])): ?>
                <img src="<?= base_url('assets/images/karyawan/' . $karyawan_terpilih['foto']) ?>" alt="Profile">
            <?php else: ?>
                <i class="bi bi-person-fill" style="font-size: 2.5rem; color: rgba(255,255,255,0.6);"></i>
            <?php endif; ?>
        </div>
        <div class="online-status"></div>
    </div>
    <div class="profile-name"><?= $karyawan_terpilih['nama_lengkap'] ?? $this->session->userdata('username') ?></div>
    <div class="profile-role"><?= $karyawan_terpilih['nama_jabatan'] ?? 'Personnel' ?></div>
    <div class="profile-badge">
        <i class="bi bi-shield-check me-2 text-info"></i>
        <?= $karyawan_terpilih['divisi_nama'] ?? 'TGM ERP System' ?>
    </div>
</div>

<!-- Main Content -->
<div class="content-container">
    <!-- Kontrak Alert -->
    <?php if (isset($kontrak_info) && $kontrak_info !== null): ?>
        <?php 
        $sisa_hari = $kontrak_info['sisa_hari'];
        $akhir_kontrak = date('d M Y', strtotime($kontrak_info['akhir_kontrak']));
        $pengajuan = $kontrak_info['pengajuan_terakhir'];
        
        $show_alert = false;
        $alert_bg = '';
        $alert_border = '';
        $alert_icon = '';
        $alert_title = '';
        $alert_desc = '';
        $btn_html = '';

        if ($pengajuan && $pengajuan['status'] === 'Pending') {
            $show_alert = true;
            $alert_bg = 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)';
            $alert_border = 'rgba(59, 130, 246, 0.3)';
            $alert_icon = '<i class="bi bi-hourglass-split text-white fs-3"></i>';
            $alert_title = 'Pengajuan Perpanjangan Kontrak';
            $alert_desc = 'Pengajuan perpanjangan kontrak Anda selama <b>' . $pengajuan['durasi_perpanjangan'] . ' bulan</b> sedang diproses oleh HRD.';
        } elseif ($pengajuan && $pengajuan['status'] === 'Approved') {
            $show_alert = true;
            $alert_bg = 'linear-gradient(135deg, #10b981 0%, #047857 100%)';
            $alert_border = 'rgba(16, 185, 129, 0.3)';
            $alert_icon = '<i class="bi bi-patch-check-fill text-white fs-3"></i>';
            $alert_title = 'Pengajuan Disetujui';
            $alert_desc = 'Selamat! Pengajuan perpanjangan kontrak Anda telah disetujui. Silakan hubungi HRD untuk info dokumen fisik.';
        } else {
            if ($sisa_hari <= 30) {
                $show_alert = true;
                if ($sisa_hari < 0) {
                    $alert_bg = 'linear-gradient(135deg, #ef4444 0%, #b91c1c 100%)';
                    $alert_border = 'rgba(239, 68, 68, 0.3)';
                    $alert_icon = '<i class="bi bi-exclamation-octagon-fill text-white fs-3 animate__animated animate__flash animate__infinite"></i>';
                    $alert_title = 'Kontrak Kerja Berakhir!';
                    $alert_desc = 'Masa kontrak kerja Anda telah berakhir pada <b>' . $akhir_kontrak . '</b> (' . abs($sisa_hari) . ' hari yang lalu). Mohon segera ajukan perpanjangan.';
                } elseif ($sisa_hari == 0) {
                    $alert_bg = 'linear-gradient(135deg, #f97316 0%, #ea580c 100%)';
                    $alert_border = 'rgba(249, 115, 22, 0.3)';
                    $alert_icon = '<i class="bi bi-exclamation-triangle-fill text-white fs-3 animate__animated animate__pulse animate__infinite"></i>';
                    $alert_title = 'Kontrak Berakhir Hari Ini!';
                    $alert_desc = 'Masa kontrak kerja Anda berakhir hari ini. Segera ajukan perpanjangan kontrak sekarang.';
                } else {
                    $alert_bg = 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)';
                    $alert_border = 'rgba(245, 158, 11, 0.3)';
                    $alert_icon = '<i class="bi bi-clock-fill text-white fs-3"></i>';
                    $alert_title = 'Kontrak Segera Berakhir';
                    $alert_desc = 'Masa kontrak kerja Anda tersisa <b>' . $sisa_hari . ' hari</b> lagi (Berakhir: ' . $akhir_kontrak . ').';
                }

                if ($pengajuan && $pengajuan['status'] === 'Rejected') {
                    $alert_desc .= '<br><span class="badge bg-danger mt-1">Pengajuan sebelumnya ditolak</span>';
                }

                $btn_html = '<button onclick="openAjukanKontrakModal()" class="btn btn-white btn-sm mt-3 fw-bold rounded-pill text-dark px-4 shadow-sm" style="background: white; border: none; font-size: 0.8rem; transition: transform 0.2s;"><i class="bi bi-pencil-square me-1"></i> Ajukan Perpanjangan</button>';
            }
        }
        ?>

        <?php if ($show_alert): ?>
            <div class="kontrak-alert animate__animated animate__fadeInDown mb-4" style="background: <?= $alert_bg ?>; border: 1px solid <?= $alert_border ?>;">
                <div class="d-flex align-items-start gap-3">
                    <div class="kontrak-alert-icon-wrapper d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(255, 255, 255, 0.2); border-radius: 12px; backdrop-filter: blur(4px); flex-shrink: 0;">
                        <?= $alert_icon ?>
                    </div>
                    <div class="flex-grow-1 text-white">
                        <h6 class="fw-bold mb-1" style="font-size: 0.95rem; text-shadow: 0 1px 2px rgba(0,0,0,0.1);"><?= $alert_title ?></h6>
                        <p class="mb-0 text-white-50" style="font-size: 0.8rem; line-height: 1.4; text-shadow: 0 1px 2px rgba(0,0,0,0.1);"><?= $alert_desc ?></p>
                        <?= $btn_html ?>
                    </div>
                </div>
            </div>
            
            <style>
                .kontrak-alert {
                    border-radius: 18px;
                    padding: 16px 20px;
                    box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.15);
                    position: relative;
                    overflow: hidden;
                }
                .kontrak-alert::before {
                    content: '';
                    position: absolute;
                    width: 150px;
                    height: 150px;
                    background: rgba(255, 255, 255, 0.05);
                    border-radius: 50%;
                    top: -50px;
                    right: -50px;
                }
                .kontrak-alert button:active {
                    transform: scale(0.95);
                }
            </style>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Attendance Warning -->
    <?php if ($hasilcek == 0 && isset($boleh_absen) && $boleh_absen): ?>
        <div class="absen-alert border-danger animate__animated animate__shakeX">
            <div class="absen-alert-icon">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="absen-alert-text">
                <h6 class="fw-bold">Belum Presensi!</h6>
                <p>Mohon segera lakukan absensi masuk untuk mencatat kehadiran Anda.</p> Klik disini untuk Melakukan
                Presensi <a href="<?= base_url('mobile/absen') ?>" class="btn btn-primary btn-sm">Presensi</a>
            </div>
        </div>
    <?php elseif (isset($is_late_today) && $is_late_today): ?>
        <div class="absen-alert border-warning animate__animated animate__headShake"
            style="border-left-color: #f59e0b; box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.1);">
            <div class="absen-alert-icon" style="background: #fef3c7; color: #f59e0b;">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="absen-alert-text">
                <h6 class="fw-bold" style="color: #92400e;">Anda Kesiangan!</h6>
                <p>Presensi Anda hari ini tercatat <b>Terlambat</b>. Mohon lebih memperhatikan jam masuk di hari berikutnya.
                </p>
                <div class="mt-2 small fw-bold">Jam Masuk: <span
                        class="text-danger"><?= date('H:i', strtotime($cekabsen['jam_masuk'])) ?></span></div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Birthday Celebration Section (Inline) -->
    <?php if (!empty($absen_hari_ini)): ?>
        <div class="absence-card animate__animated animate__fadeInDown">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-info-circle-fill text-warning fs-5"></i>
                <h6 class="fw-bold mb-0 text-white" style="font-size: 0.85rem;">Info Kehadiran Hari Ini</h6>
            </div>
            <div class="absence-list">
                <?php foreach ($absen_hari_ini as $u): ?>
                    <div class="d-flex align-items-center gap-2 mb-2 p-2 rounded" style="background: rgba(255,255,255,0.1);">
                        <img src="<?= !empty($u->foto) && file_exists('assets/images/karyawan/' . $u->foto) ? base_url('assets/images/karyawan/' . $u->foto) : base_url('assets/images/karyawan/default.png') ?>" 
                             onerror="this.onerror=null; this.src='<?= base_url('assets/images/karyawan/default.png') ?>'" 
                             class="rounded-circle border border-1 border-white shadow-sm" 
                             style="width: 32px; height: 32px; object-fit: cover;">
                        <div class="flex-grow-1">
                            <div class="text-white fw-bold" style="font-size: 0.75rem;"><?= htmlspecialchars($u->nama_lengkap) ?></div>
                            <div class="text-white-50" style="font-size: 0.65rem;"><?= htmlspecialchars($u->status . (!empty($u->keterangan) ? ' - ' . $u->keterangan : '')) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="absence-card animate__animated animate__fadeInDown" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.2);">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill text-white fs-2"></i>
                <div class="flex-grow-1 ms-2">
                    <h6 class="fw-bold mb-0 text-white" style="font-size: 0.85rem;">Kehadiran Sempurna!</h6>
                    <p class="mb-0 text-white-50" style="font-size: 0.7rem; line-height: 1.2;">
                        Semua rekan kerja tercatat hadir atau belum ada laporan absen hari ini.
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>
        
        <style>
            .absence-card {
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                border-radius: 16px;
                padding: 12px 15px;
                margin-bottom: 20px;
                box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.2);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
            .absence-list {
                max-height: 120px;
                overflow-y: auto;
            }
            .absence-list::-webkit-scrollbar {
                width: 4px;
            }
            .absence-list::-webkit-scrollbar-track {
                background: rgba(255,255,255,0.1);
                border-radius: 4px;
            }
            .absence-list::-webkit-scrollbar-thumb {
                background: rgba(255,255,255,0.3);
                border-radius: 4px;
            }
        </style>

    <!-- Announcements Section -->
    <?php if (!empty($pengumuman)): ?>
        <div class="section-header">
            <div class="section-title">
                <i class="bi bi-megaphone-fill text-danger"></i> Pengumuman Internal
            </div>
        </div>

        <div id="announcementCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php foreach ($pengumuman as $index => $p): ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                        <div class="announcement-card" onclick="showAnnouncement(<?= htmlspecialchars(json_encode($p)) ?>)"
                            style="cursor: pointer;">
                            <span class="announcement-badge badge-<?= strtolower($p->kategori) ?>">
                                <?= $p->kategori ?>
                            </span>
                            <span class="announcement-title"><?= $p->judul ?></span>
                            <div class="announcement-excerpt"><?= strip_tags($p->isi) ?></div>
                            <div class="announcement-footer">
                                <span><i class="bi bi-person-circle me-1"></i> <?= $p->pembuat ?></span>
                                <span><i class="bi bi-calendar3 me-1"></i>
                                    <?= date('d M Y', strtotime($p->created_at)) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($pengumuman) > 1): ?>
                <div class="carousel-indicators" style="bottom: 0;">
                    <?php foreach ($pengumuman as $index => $p): ?>
                        <button type="button" data-bs-target="#announcementCarousel" data-bs-slide-to="<?= $index ?>"
                            class="<?= $index === 0 ? 'active' : '' ?>"
                            style="background-color: #cbd5e1; width: 8px; height: 8px; border-radius: 50%; margin: 0 4px;"></button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Gamification Section -->
    <?php if ($performance): ?>
        <?php
        $score = $performance->skor_kinerja;
        $rank = 'STANDARD';
        $color = '#f59e0b'; // Yellow
        if ($score >= 95) {
            $rank = 'ELITE';
            $color = '#22c55e';
        } elseif ($score >= 85) {
            $rank = 'EXCELLENT';
            $color = '#3b82f6';
        } elseif ($score >= 70) {
            $rank = 'GOOD';
            $color = '#10b981';
        }

        // Circular Progress Calculation
        $radius = 36;
        $circumference = 2 * pi() * $radius;
        $offset = $circumference - ($score / 100 * $circumference);
        ?>
        <div class="section-header">
            <div class="section-title">
                <i class="bi bi-trophy-fill text-warning"></i> Pencapaian Anda
            </div>
            <span class="small text-muted">Bulan
                <?= date('F') ?>
            </span>
        </div>

        <div class="performance-card animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
            <div class="circular-progress">
                <svg>
                    <circle class="bg" cx="40" cy="40" r="<?= $radius ?>"></circle>
                    <circle class="progress" cx="40" cy="40" r="<?= $radius ?>"
                        style="stroke-dasharray: <?= $circumference ?>; stroke-dashoffset: <?= $circumference ?>; stroke: <?= $color ?>;"
                        id="progress-circle"></circle>
                </svg>
                <div class="progress-value">
                    <?= number_format($score, 0) ?>%
                </div>
            </div>
            <div class="perf-info">
                <h6>Status Kedisiplinan</h6>
                <span class="rank-text" style="color: <?= $color ?>">
                    <?= $rank ?>
                </span>
                <div class="perf-stats">
                    <div class="stat-badge"><i class="bi bi-check-circle-fill text-success"></i>
                        <?= $performance->total_hadir ?> Hadir
                    </div>
                    <div class="stat-badge"><i class="bi bi-rocket-takeoff-fill text-info"></i>
                        <?= $performance->total_spk ?> SPK
                    </div>
                    <div class="stat-badge"><i class="bi bi-clock-history text-warning"></i>
                        <?= $performance->total_telat ?> Telat
                    </div>
                    <div class="stat-badge"><i class="bi bi-x-circle-fill text-danger"></i>
                        <?= $performance->total_alpha ?> Alpha
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Animate progress bar after load
            setTimeout(() => {
                const circle = document.getElementById('progress-circle');
                if (circle) {
                    circle.style.strokeDashoffset = '<?= $offset ?>';
                }
            }, 600);
        </script>
    <?php endif; ?>

    <!-- Main Menu -->
    <div class="section-header">
        <div class="section-title">
            <i class="bi bi-window-stack"></i> Layanan Mandiri
        </div>
    </div>

    <div class="menu-grid">
        <a href="<?= base_url('mobile/absen') ?>" class="menu-item">
            <div class="menu-icon icon-red"><i class="bi bi-calendar2-check"></i></div>
            <span class="menu-label">Presensi</span>
        </a>
        <a href="<?= base_url('mobile/sakit') ?>" class="menu-item">
            <div class="menu-icon icon-blue"><i class="bi bi-envelope-paper"></i></div>
            <span class="menu-label">Izin</span>
        </a>
        <a href="<?= base_url('mobile/cuti') ?>" class="menu-item">
            <div class="menu-icon icon-green"><i class="bi bi-calendar-event"></i></div>
            <span class="menu-label">Cuti</span>
        </a>
        <a href="<?= base_url('mobile/lembur') ?>" class="menu-item">
            <div class="menu-icon icon-yellow"><i class="bi bi-clock"></i></div>
            <span class="menu-label">Lembur</span>
        </a>
        <a href="<?= base_url('mobile/direktori') ?>" class="menu-item">
            <div class="menu-icon icon-blue"><i class="bi bi-person-lines-fill"></i></div>
            <span class="menu-label">Direktori</span>
        </a>
        <a href="<?= base_url('mobile/helpdesk') ?>" class="menu-item">
            <div class="menu-icon icon-dark"><i class="bi bi-headset"></i></div>
            <span class="menu-label">Bantuan</span>
        </a>
        <a href="javascript:void(0)"
            onclick="openProtectedLink('<?= base_url('mobile/profile') ?>', 'Akses Profil', 'Verifikasi identitas untuk melihat data pribadi.')"
            class="menu-item">
            <div class="menu-icon icon-cyan"><i class="bi bi-person-badge"></i></div>
            <span class="menu-label">Profil</span>
        </a>
        <a href="javascript:void(0)"
            onclick="openProtectedLink('<?= base_url('mobile/kinerja') ?>', 'Akses Kinerja', 'Verifikasi identitas untuk melihat laporan kinerja.')"
            class="menu-item">
            <div class="menu-icon icon-purple"><i class="bi bi-graph-up-arrow"></i></div>
            <span class="menu-label">Kinerja</span>
        </a>
        <a href="<?= base_url('mobile/product') ?>" class="menu-item">
            <div class="menu-icon icon-pink"><i class="bi bi-box-seam"></i></div>
            <span class="menu-label">Katalog</span>
        </a>
        <a href="javascript:void(0)"
            onclick="openProtectedLink('<?= base_url('mobile/slip_gaji') ?>', 'Slip Gaji', 'Masukkan password untuk membuka slip gaji.')"
            class="menu-item">
            <div class="menu-icon icon-slate"><i class="bi bi-wallet2"></i></div>
            <span class="menu-label">Slip Gaji</span>
        </a>
        <?php if (isset($has_desktop_access) && $has_desktop_access): ?>
        <a href="javascript:void(0)" onclick="confirmAccessERP()" class="menu-item">
            <div class="menu-icon icon-dark"><i class="bi bi-display"></i></div>
            <span class="menu-label">Versi Desktop</span>
        </a>
        <?php endif; ?>
    </div>

    <!-- Owner / Management Section -->
    <?php if (in_array($this->session->userdata('level'), ['17'])): ?>
        <div class="custom-divider"></div>

        <div class="section-header">
            <div class="section-title">
                <i class="bi bi-kanban"></i> Management Console
            </div>
            <span class="badge bg-dark rounded-pill" style="font-size: 0.6rem;">OWNER</span>
        </div>

        <div class="menu-grid">
            <a href="<?= base_url('mobile/sdm') ?>" class="menu-item">
                <div class="menu-icon icon-green"><i class="bi bi-people"></i></div>
                <span class="menu-label">Data SDM</span>
            </a>
            <a href="<?= base_url('mobile/konsumen') ?>" class="menu-item">
                <div class="menu-icon icon-yellow"><i class="bi bi-trophy"></i></div>
                <span class="menu-label">Top Client</span>
            </a>
            <a href="<?= base_url('mobile/analisis') ?>" class="menu-item">
                <div class="menu-icon icon-dark"><i class="bi bi-boxes"></i></div>
                <span class="menu-label">Analisis Stok</span>
            </a>
            <a href="<?= base_url('mobile/owner') ?>" class="menu-item">
                <div class="menu-icon icon-slate"><i class="bi bi-person-check"></i></div>
                <span class="menu-label">Owner</span>
            </a>
            <a href="<?= base_url('mobile/keuangan') ?>" class="menu-item">
                <div class="menu-icon icon-green"><i class="bi bi-currency-dollar"></i></div>
                <span class="menu-label">Arus Kas</span>
            </a>
            <a href="<?= base_url('mobile/piutang') ?>" class="menu-item">
                <div class="menu-icon icon-red"><i class="bi bi-clipboard-minus"></i></div>
                <span class="menu-label">Piutang</span>
            </a>
            <a href="<?= base_url('mobile/hutang') ?>" class="menu-item">
                <div class="menu-icon icon-red"><i class="bi bi-wallet2"></i></div>
                <span class="menu-label">Hutang</span>
            </a>
            <a href="<?= base_url('mobile/audit') ?>" class="menu-item">
                <div class="menu-icon icon-dark"><i class="bi bi-shield-check"></i></div>
                <span class="menu-label">Audit Log</span>
            </a>
        </div>
    <?php endif; ?>

    <!-- HR Manager / SDM Section -->
    <?php if (in_array($this->session->userdata('level'), ['1', '16', '17', '21'])): ?>
        <div class="custom-divider"></div>

        <div class="section-header">
            <div class="section-title">
                <i class="bi bi-people"></i> HR Manager Console
            </div>
            <span class="badge bg-primary rounded-pill" style="font-size: 0.6rem;">SDM</span>
        </div>

        <div class="menu-grid">
            <a href="<?= base_url('mobile/laporan_absensi') ?>" class="menu-item">
                <div class="menu-icon icon-green"><i class="bi bi-calendar2-check"></i></div>
                <span class="menu-label">Absensi Karyawan</span>
            </a>
            <a href="<?= base_url('mobile/laporan_izin') ?>" class="menu-item">
                <div class="menu-icon icon-yellow"><i class="bi bi-envelope-paper"></i></div>
                <span class="menu-label">Pengajuan Izin</span>
            </a>
            <a href="<?= base_url('mobile/laporan_lembur') ?>" class="menu-item">
                <div class="menu-icon icon-red"><i class="bi bi-clock-history"></i></div>
                <span class="menu-label">Lembur Karyawan</span>
            </a>
            <a href="<?= base_url('mobile/sdm') ?>" class="menu-item">
                <div class="menu-icon icon-slate"><i class="bi bi-person-lines-fill"></i></div>
                <span class="menu-label">Data SDM</span>
            </a>
            <a href="<?= base_url('mobile/laporan_gaji') ?>" class="menu-item">
                <div class="menu-icon icon-blue"><i class="bi bi-wallet2"></i></div>
                <span class="menu-label">Gaji / Payroll</span>
            </a>
        </div>
    <?php endif; ?>

    <!-- Professional Action -->
    <?php if (in_array($this->session->userdata('level'), ['1', '2', '3', '4', '5', '6', '17', '19', '21'])): ?>
        <div class="custom-divider"></div>

        <!-- Download APK Section -->
        <div class="section-header">
            <div class="section-title">
                <i class="bi bi-phone-fill text-primary"></i> Mobile Application
            </div>
        </div>

        <div class="px-2 mb-3">
            <div class="row g-2">
                <div class="col-6">
                    <a href="<?= base_url('assets/app-releases/tgm_erp.apk') ?>"
                        class="apk-download-card h-100 flex-column align-items-start p-3 gap-2" download>
                        <div class="apk-icon">
                            <i class="bi bi-android2"></i>
                        </div>
                        <div class="apk-info">
                            <h6 class="mb-1" style="font-size: 0.85rem;">Android App</h6>
                            <span class="badge bg-primary rounded-pill" style="font-size: 0.45rem;">v.1.1.2 (STABLE)</span>
                        </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="javascript:void(0)" onclick="infoIosWeb()"
                        class="apk-download-card h-100 flex-column align-items-start p-3 gap-2">
                        <div class="apk-icon" style="background: #f1f5f9; color: #475569;">
                            <i class="bi bi-apple"></i>
                        </div>
                        <div class="apk-info">
                            <h6 class="mb-1" style="font-size: 0.85rem;">iOS Version</h6>
                            <span class="badge bg-info-subtle text-info rounded-pill" style="font-size: 0.45rem;">WEB
                                OPTIMIZED</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <?php if (isset($has_desktop_access) && $has_desktop_access): ?>
        <div class="text-center mt-3 mb-4">
            <a href="javascript:void(0)" onclick="confirmAccessERP()" class="desktop-link">
                <span>Versi Web/PC</span>
                <i class="bi bi-box-arrow-up-right"></i>
            </a>
        </div>
        <?php endif; ?>
    <?php endif; ?>

</div>



<script>
    function confirmAccessERP() {
        Swal.fire({
            title: 'Akses Desktop ERP?',
            text: 'Beralih ke tampilan desktop untuk akses laporan dan modul manajemen lengkap.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1e293b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-check-circle me-1"></i> Ya, Lanjutkan',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            borderRadius: '15px'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'https://erp.tgmprint.com/dashboard';
            }
        });
    }

    function infoIosWeb() {
        Swal.fire({
            title: 'TGM ERP for iOS',
            html: `
                <div class="text-start p-2">
                    <p class="mb-3">Nikmati pengalaman seperti aplikasi asli di iPhone Anda tanpa instalasi:</p>
                    <ol class="small text-dark mb-4">
                        <li class="mb-2">Buka Link ini di Browser <b>Safari</b>.</li>
                        <li class="mb-2">Klik ikon <b>Bagikan (Share)</b> <i class="bi bi-box-arrow-up text-primary"></i> di bagian bawah layar.</li>
                        <li class="mb-2">Cari dan pilih menu <b>"Add to Home Screen"</b> atau <b>"Tambah ke Layar Utama"</b>.</li>
                        <li class="mb-0">Klik <b>Add (Tambah)</b> di pojok kanan atas.</li>
                    </ol>
                    <div class="alert alert-success border-0 rounded-4 py-2 px-3 mb-0 small">
                        <i class="bi bi-check-circle-fill me-1"></i> Ikon TGM ERP akan muncul di layar utama Anda dan siap digunakan kapan saja!
                    </div>
                </div>
            `,
            icon: 'success',
            confirmButtonText: 'SAYA MENGERTI',
            confirmButtonColor: '#1e293b',
            borderRadius: '25px',
            showClass: {
                popup: 'animate__animated animate__fadeInUp'
            }
        });
    }

    // Expert Security Alert: Default Password Detection
    <?php if (isset($is_default_password) && $is_default_password): ?>
        setTimeout(() => {
            Swal.fire({
                title: '🛡️ Lindungi Akun Anda!',
                html: `
                    <div class="text-center p-2">
                        <p class="mb-3 text-dark">Sistem mendeteksi Anda masih menggunakan <b>Password Default</b>. </p>
                        <div class="alert alert-warning border-0 rounded-4 py-2 px-3 mb-3">
                            <i class="bi bi-shield-exclamation me-1"></i> Mohon ganti segera demi keamanan data perusahaan & pribadi.
                        </div>
                    </div>
                `,
                icon: 'warning',
                confirmButtonText: '<i class="bi bi-key-fill me-2"></i> GANTI PASSWORD SEKARANG',
                confirmButtonColor: '#198754',
                showCancelButton: true,
                cancelButtonText: 'Lewati',
                cancelButtonColor: '#adb5bd',
                backdrop: `rgba(0,0,0,0.85)`,
                allowOutsideClick: false,
                borderRadius: '25px',
                showClass: { popup: 'animate__animated animate__tada' }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url('mobile/reset_pass') ?>';
                }
            });
        }, 1500);
    <?php endif; ?>

    function showAnnouncement(p) {
        const badgeClass = `badge-${p.kategori.toLowerCase()}`;
        Swal.fire({
            title: `<div class="text-start fs-5 fw-bold">${p.judul}</div>`,
            html: `
                <div class="text-start">
                    <span class="announcement-badge ${badgeClass} mb-3">${p.kategori}</span>
                    <div class="text-muted small mb-3 border-bottom pb-2">
                        <i class="bi bi-person-circle me-1"></i> ${p.pembuat} &bull; 
                        <i class="bi bi-calendar3 me-1"></i> ${new Date(p.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                    </div>
                    <div class="announcement-detail-content">
                        ${p.isi}
                    </div>
                </div>
            `,
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#1e293b',
            padding: '1.5rem',
            borderRadius: '25px',
            showClass: { popup: 'animate__animated animate__fadeInUp' }
        });
    }

    function openAjukanKontrakModal() {
        Swal.fire({
            title: 'Form Perpanjangan Kontrak',
            html: `
                <div class="text-start p-2">
                    <label class="form-label fw-bold text-dark small mb-1">Durasi Perpanjangan yang Diajukan</label>
                    <select id="swal_durasi" class="form-select form-control mb-3" style="border-radius: 10px; font-size: 0.9rem;">
                        <option value="3">3 Bulan</option>
                        <option value="6" selected>6 Bulan</option>
                        <option value="12">12 Bulan (1 Tahun)</option>
                        <option value="24">24 Bulan (2 Tahun)</option>
                    </select>
                    
                    <label class="form-label fw-bold text-dark small mb-1">Alasan Pengajuan / Keterangan</label>
                    <textarea id="swal_alasan" class="form-control" rows="4" placeholder="Tulis alasan mengapa Anda mengajukan perpanjangan kontrak kerja..." style="border-radius: 10px; font-size: 0.9rem; resize: none;"></textarea>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-send-fill me-1"></i> Kirim Pengajuan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#1e293b',
            cancelButtonColor: '#ef4444',
            borderRadius: '20px',
            reverseButtons: true,
            focusConfirm: false,
            preConfirm: () => {
                const durasi = Swal.getPopup().querySelector('#swal_durasi').value;
                const alasan = Swal.getPopup().querySelector('#swal_alasan').value;
                if (!durasi) {
                    Swal.showValidationMessage(`Silakan pilih durasi perpanjangan`);
                }
                if (!alasan || alasan.trim().length < 10) {
                    Swal.showValidationMessage(`Silakan isi alasan pengajuan minimal 10 karakter`);
                }
                return { durasi: durasi, alasan: alasan };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading spinner
                Swal.fire({
                    title: 'Mengirim Pengajuan...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // AJAX Request menggunakan Fetch API
                const formData = new FormData();
                formData.append('durasi', result.value.durasi);
                formData.append('alasan', result.value.alasan);
                
                const getCookie = (name) => {
                    const value = `; ${document.cookie}`;
                    const parts = value.split(`; ${name}=`);
                    if (parts.length === 2) return parts.pop().split(';').shift();
                }
                const csrfName = '<?= $this->security->get_csrf_token_name() ?>';
                const csrfHash = getCookie(csrfName);
                if (csrfHash) {
                    formData.append(csrfName, csrfHash);
                }

                fetch('<?= base_url("mobile/aksi_pengajuan_kontrak") ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error("HTTP error! Status: " + response.status);
                    }
                    return response.text();
                })
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                confirmButtonColor: '#10b981',
                                borderRadius: '20px'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message,
                                confirmButtonColor: '#ef4444',
                                borderRadius: '20px'
                            });
                        }
                    } catch (e) {
                        console.error("Server response was not JSON:", text);
                        throw new Error("Respon server tidak valid (bukan JSON)");
                    }
                })
                .catch(error => {
                    console.error("AJAX Error:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: error.message || 'Terjadi kesalahan pada sistem. Silakan coba lagi.',
                        confirmButtonColor: '#ef4444',
                        borderRadius: '20px'
                    });
                });
            }
        });
    }
</script>