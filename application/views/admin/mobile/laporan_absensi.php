<?php
// Ensure variables are countable to prevent PHP 7.4+ errors
$log_absensi = isset($log_absensi) && (is_array($log_absensi) || is_object($log_absensi)) ? $log_absensi : [];
$belum_hadir = isset($belum_hadir) && (is_array($belum_hadir) || is_object($belum_hadir)) ? $belum_hadir : [];
?>
<style>
    /* Reset and ensure base body doesn't conflict */
    .opd-header {
        background: #fff;
        padding: 15px 20px;
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .back-btn-circle {
        width: 45px;
        height: 45px;
        background: #FFC107;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.3rem;
        box-shadow: 0 4px 8px rgba(255, 193, 7, 0.4);
        text-decoration: none;
    }

    .back-btn-circle:hover,
    .back-btn-circle:active {
        color: #fff;
        transform: scale(0.95);
    }

    .tab-nav-custom {
        display: flex;
        justify-content: space-around;
        background: #fff;
        padding: 15px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .tab-nav-custom .tab-item {
        font-size: 0.8rem;
        font-weight: 800;
        color: #9CA3AF;
        text-transform: uppercase;
        cursor: pointer;
        padding-bottom: 5px;
    }

    .tab-nav-custom .tab-item.active {
        color: #2563EB;
        border-bottom: 3px solid #2563EB;
    }

    .filter-pills-container {
        display: flex;
        overflow-x: auto;
        white-space: nowrap;
        padding: 15px 20px;
        background: #F9FAFB;
        gap: 10px;
        scrollbar-width: none;
        /* Firefox */
    }

    .filter-pills-container::-webkit-scrollbar {
        display: none;
        /* Safari and Chrome */
    }

    .filter-pill {
        padding: 8px 20px;
        border-radius: 50px;
        background: #fff;
        color: #6B7280;
        font-size: 0.85rem;
        font-weight: 600;
        border: 1px solid #E5E7EB;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .filter-pill.active {
        background: #3B82F6;
        color: #fff;
        border-color: #3B82F6;
        box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
    }

    .employee-card {
        background: #fff;
        border-radius: 16px;
        padding: 15px;
        margin-bottom: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        border: 1px solid #F3F4F6;
    }

    .avatar-wrapper {
        position: relative;
        width: 60px;
        height: 60px;
    }

    .avatar-img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 2.5px solid #E5E7EB;
        background: #F3F4F6;
    }

    /* Dynamic Border Colors */
    .border-status-hadir {
        border-color: #10B981;
    }

    .border-status-telat {
        border-color: #EF4444;
    }

    .border-status-cuti {
        border-color: #6366F1;
    }

    .border-status-belum {
        border-color: #9CA3AF;
    }

    .status-icon-badge {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #fff;
        font-size: 0.65rem;
        color: #fff;
    }

    .bg-status-hadir {
        background-color: #10B981;
    }

    .bg-status-telat {
        background-color: #EF4444;
    }

    .bg-status-cuti {
        background-color: #6366F1;
    }

    .bg-status-belum {
        background-color: #9CA3AF;
    }

    .emp-name {
        font-size: 0.95rem;
        font-weight: 800;
        color: #1F2937;
        margin-bottom: 2px;
    }

    .emp-title {
        font-size: 0.7rem;
        color: #6B7280;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.3px;
        margin-bottom: 8px;
    }

    .time-info {
        font-size: 0.75rem;
        color: #4B5563;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .time-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .time-item i {
        font-size: 0.9rem;
    }

    .pill-badge {
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
    }

    .pill-primary {
        background: #EEF2FF;
        color: #4F46E5;
    }

    .pill-danger {
        background: #FEF2F2;
        color: #DC2626;
    }

    .pill-success {
        background: #F0FDF4;
        color: #16A34A;
    }

    .pill-secondary {
        background: #F3F4F6;
        color: #6B7280;
    }
</style>

<!-- Header -->
<div class="opd-header d-flex align-items-center">
    <a href="<?= base_url('mobile') ?>" class="back-btn-circle me-3">
        <i class="bi bi-chevron-left" style="margin-left: -2px;"></i>
    </a>
    <div>
        <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.1rem;">Live Laporan Abseensi</h5>
        <div class="text-muted fw-bold mt-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">LAPORAN HARIAN</div>
    </div>
</div>

<!-- Dummy Tabs -->
<div class="tab-nav-custom">
    <div class="tab-item active">KEHADIRAN</div>
    <div class="tab-item">PERFORMA</div>
    <div class="tab-item">RED ZONE</div>
</div>

<!-- Filter Pills -->
<div class="filter-pills-container">
    <div class="filter-pill active" onclick="filterData('semua', this)">Semua</div>
    <div class="filter-pill" onclick="filterData('hadir', this)">Hadir</div>
    <div class="filter-pill" onclick="filterData('terlambat', this)">Terlambat</div>
    <div class="filter-pill" onclick="filterData('cuti', this)">Cuti/Izin</div>
    <div class="filter-pill" onclick="filterData('belum', this)">Belum Absen</div>
</div>

<!-- Main Content Area -->
<div class="px-3 pb-5 mt-2" style="background: #F9FAFB; min-height: 100vh;">

    <div id="data-container">

        <!-- SUDAH ABSEN (Hadir, Terlambat, Cuti) -->
        <?php foreach ($log_absensi as $log):

            // Determine Status Logic
            $is_telat = ($log->terlambat > 0);
            $is_cuti = in_array(strtolower($log->status_kehadiran), ['sakit', 'izin', 'cuti']);

            if ($is_telat) {
                $status_class = 'terlambat';
                $border_class = 'border-status-telat';
                $bg_class = 'bg-status-telat';
                $icon = 'bi-exclamation-lg';
                $pill_class = 'pill-danger';
                $pill_text = 'TELAT ' . floor($log->terlambat / 60) . 'm';
            } elseif ($is_cuti) {
                $status_class = 'cuti';
                $border_class = 'border-status-cuti';
                $bg_class = 'bg-status-cuti';
                $icon = 'bi-car-front-fill'; // Mockup uses car for Dinas Luar/Cuti
                $pill_class = 'pill-primary';
                $pill_text = strtoupper($log->status_kehadiran);
            } else {
                $status_class = 'hadir';
                $border_class = 'border-status-hadir';
                $bg_class = 'bg-status-hadir';
                $icon = 'bi-check-lg';
                $pill_class = 'pill-primary';
                $pill_text = 'KANTOR';
            }

            // Fallback for avatar
            $avatar_url = !empty($log->foto) ? base_url('assets/images/karyawan/' . $log->foto) : base_url('assets/images/karyawan/default.png');
            $jam_masuk = !empty($log->jam_masuk) ? date('H:i', strtotime($log->jam_masuk)) : '-';
            $jam_pulang = (!empty($log->jam_pulang) && $log->jam_pulang != '00:00:00') ? date('H:i', strtotime($log->jam_pulang)) : '-';
            ?>
            <div class="employee-card data-item" data-status="<?= $status_class ?>">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-wrapper">
                            <img src="<?= $avatar_url ?>" class="avatar-img <?= $border_class ?>" onerror="this.onerror=null; this.src='<?= base_url('assets/images/karyawan/default.png') ?>'">
                            <div class="status-icon-badge <?= $bg_class ?>">
                                <i class="bi <?= $icon ?>"></i>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3" style="min-width: 0;">
                        <div class="emp-name text-truncate"><?= $log->nama_lengkap ?></div>
                        <div class="emp-title text-truncate"><?= $log->nama_jabatan ?></div>

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div class="time-info">
                                <div class="time-item">
                                    <i class="bi bi-clock text-secondary"></i> <?= $jam_masuk ?>
                                </div>
                                <div class="time-item">
                                    <i class="bi bi-building text-primary"></i> <?= $jam_pulang ?>
                                </div>
                            </div>
                            <div class="pill-badge <?= $pill_class ?>"><?= $pill_text ?></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- BELUM ABSEN -->
        <?php foreach ($belum_hadir as $row):
            $avatar_url = !empty($row->foto) ? base_url('assets/images/karyawan/' . $row->foto) : base_url('assets/images/karyawan/default.png');
            ?>
            <div class="employee-card data-item" data-status="belum">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-wrapper">
                            <img src="<?= $avatar_url ?>" class="avatar-img border-status-belum">
                            <div class="status-icon-badge bg-status-belum">
                                <i class="bi bi-dash"></i>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3" style="min-width: 0;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="emp-name text-truncate"><?= $row->nama_lengkap ?></div>
                                <div class="emp-title text-truncate"><?= $row->nama_jabatan ?></div>
                            </div>
                            <!-- WA Button -->
                            <a href="<?= base_url('mobile/kirim_teguran_wa/' . $row->karyawan_id) ?>"
                                class="btn btn-sm btn-success rounded-circle shadow-sm"
                                style="width: 28px; height: 28px; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem;"
                                onclick="return confirm('Kirim teguran WA ke <?= $row->nama_lengkap ?>?')">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <div class="time-info">
                                <div class="time-item opacity-50">
                                    <i class="bi bi-clock text-secondary"></i> -
                                </div>
                                <div class="time-item opacity-50">
                                    <i class="bi bi-building text-primary"></i> -
                                </div>
                            </div>
                            <div class="pill-badge pill-secondary">BELUM ABSEN</div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <div id="no-data-msg" class="text-center py-5 d-none">
            <i class="bi bi-inbox text-muted h1 opacity-25"></i>
            <h6 class="fw-bold mt-3 text-muted">Tidak ada data</h6>
        </div>

    </div>
</div>

<script>
    function filterData(status, element) {
        // Update active pill styling
        document.querySelectorAll('.filter-pill').forEach(pill => {
            pill.classList.remove('active');
        });
        element.classList.add('active');

        // Filter logic
        let visibleCount = 0;
        document.querySelectorAll('.data-item').forEach(item => {
            if (status === 'semua' || item.getAttribute('data-status') === status) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Show empty message if nothing visible
        const noDataMsg = document.getElementById('no-data-msg');
        if (visibleCount === 0) {
            noDataMsg.classList.remove('d-none');
        } else {
            noDataMsg.classList.add('d-none');
        }
    }
</script>