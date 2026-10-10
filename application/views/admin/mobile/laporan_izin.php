<?php
// application/views/admin/mobile/laporan_izin.php
// Ensure variables are countable to prevent PHP 7.4+ errors
$list_izin = isset($list_izin) && (is_array($list_izin) || is_object($list_izin)) ? $list_izin : [];
$bulan_aktif = isset($bulan_aktif) ? $bulan_aktif : date('Y-m');
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
    .border-status-disetujui {
        border-color: #10B981;
    }

    .border-status-ditolak {
        border-color: #EF4444;
    }

    .border-status-menunggu {
        border-color: #F59E0B;
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

    .bg-status-disetujui {
        background-color: #10B981;
    }

    .bg-status-ditolak {
        background-color: #EF4444;
    }

    .bg-status-menunggu {
        background-color: #F59E0B;
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
    
    .pill-warning {
        background: #FFFBEB;
        color: #D97706;
    }

    .pill-secondary {
        background: #F3F4F6;
        color: #6B7280;
    }
</style>

<!-- Header -->
<div class="opd-header d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center">
        <a href="<?= base_url('mobile') ?>" class="back-btn-circle me-3">
            <i class="bi bi-chevron-left" style="margin-left: -2px;"></i>
        </a>
        <div>
            <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.1rem;">Laporan Izin/Cuti</h5>
            <div class="text-muted fw-bold mt-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">DAFTAR PENGAJUAN</div>
        </div>
    </div>
    
    <!-- Month Picker Filter -->
    <form action="" method="get" id="filterForm">
        <input type="month" name="bulan" class="form-control form-control-sm border-0 bg-light fw-bold text-primary" 
               style="border-radius: 50px; padding: 5px 15px; width: auto;"
               value="<?= $bulan_aktif ?>" onchange="document.getElementById('filterForm').submit();">
    </form>
</div>

<!-- Tabs (Optional, matching design) -->
<div class="tab-nav-custom">
    <div class="tab-item active">PENGAJUAN</div>
    <div class="tab-item">STATISTIK</div>
</div>

<!-- Filter Pills -->
<div class="filter-pills-container">
    <div class="filter-pill active" onclick="filterData('semua', this)">Semua</div>
    <div class="filter-pill" onclick="filterData('menunggu', this)">Menunggu</div>
    <div class="filter-pill" onclick="filterData('disetujui', this)">Disetujui</div>
    <div class="filter-pill" onclick="filterData('ditolak', this)">Ditolak</div>
</div>

<!-- Main Content Area -->
<div class="px-3 pb-5 mt-2" style="background: #F9FAFB; min-height: 100vh;">
    
    <div id="data-container">
        
        <?php foreach ($list_izin as $izin): 
            $status_kehadiran = strtolower($izin->status);
            
            // Determine Status Logic
            $status_class = 'menunggu';
            $border_class = 'border-status-menunggu';
            $bg_class = 'bg-status-menunggu';
            $icon = 'bi-hourglass-split';
            $pill_class = 'pill-warning';
            $pill_text = 'MENUNGGU';

            if ($status_kehadiran == 'disetujui' || $status_kehadiran == 'approved') {
                $status_class = 'disetujui';
                $border_class = 'border-status-disetujui';
                $bg_class = 'bg-status-disetujui';
                $icon = 'bi-check2';
                $pill_class = 'pill-success';
                $pill_text = 'DISETUJUI';
            } elseif ($status_kehadiran == 'ditolak' || $status_kehadiran == 'rejected') {
                $status_class = 'ditolak';
                $border_class = 'border-status-ditolak';
                $bg_class = 'bg-status-ditolak';
                $icon = 'bi-x';
                $pill_class = 'pill-danger';
                $pill_text = 'DITOLAK';
            }

            $avatar_url = !empty($izin->foto) ? base_url('assets/images/karyawan/' . $izin->foto) : base_url('assets/images/karyawan/default.png');
            $tanggal_mulai = !empty($izin->tanggal_mulai) ? date('d M Y', strtotime($izin->tanggal_mulai)) : '-';
            $jenis_izin = strtoupper($izin->tipe_izin);
            $durasi = $izin->durasi_jumlah . ' ' . $izin->durasi_tipe;
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
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="emp-name text-truncate"><?= $izin->nama_lengkap ?></div>
                            <div class="emp-title text-truncate"><?= $izin->nama_jabatan ?></div>
                        </div>
                        <div class="pill-badge <?= $pill_class ?>"><?= $pill_text ?></div>
                    </div>
                    
                    <div class="d-flex flex-wrap gap-2 mt-1">
                        <span class="badge bg-light text-dark border shadow-sm" style="font-size: 0.65rem;">
                            <i class="bi bi-tag text-primary me-1"></i> <?= $jenis_izin ?>
                        </span>
                        <span class="badge bg-light text-dark border shadow-sm" style="font-size: 0.65rem;">
                            <i class="bi bi-calendar3 text-secondary me-1"></i> <?= $tanggal_mulai ?>
                        </span>
                        <span class="badge bg-light text-dark border shadow-sm" style="font-size: 0.65rem;">
                            <i class="bi bi-stopwatch text-info me-1"></i> <?= $durasi ?>
                        </span>
                    </div>
                    
                    <div class="mt-2 pt-2 border-top">
                        <div class="text-muted" style="font-size: 0.7rem;"><i class="bi bi-chat-left-text me-1"></i> <?= $izin->keterangan ?></div>
                    </div>
                    
                    <?php if ($izin->dokumen): ?>
                        <div class="mt-2">
                            <button type="button" onclick="showDocument('<?= base_url('assets/uploads/cuti/' . $izin->dokumen) ?>', '<?= addslashes($izin->nama_lengkap) ?>')"
                                class="btn btn-sm btn-outline-info rounded-pill" style="font-size: 0.7rem; padding: 2px 10px;">
                                <i class="bi bi-file-earmark-text me-1"></i> Lihat Dokumen
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <div id="no-data-msg" class="text-center py-5 <?= !empty($list_izin) ? 'd-none' : '' ?>">
            <i class="bi bi-inbox text-muted h1 opacity-25"></i>
            <h6 class="fw-bold mt-3 text-muted">Tidak ada data</h6>
            <p class="text-muted small">Belum ada pengajuan izin di bulan ini.</p>
        </div>
        
    </div>
</div>

<!-- Document Modal -->
<div class="modal fade" id="documentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-light border-0 py-2">
                <h6 class="modal-title fw-bold" id="documentTitle" style="font-size: 0.9rem;">Dokumen Izin</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 text-center bg-dark">
                <img id="documentImage" src="" class="img-fluid d-none" style="max-height: 70vh; object-fit: contain;">
                <iframe id="documentIframe" src="" class="d-none" style="width: 100%; height: 70vh; border: none; background: #fff;"></iframe>
            </div>
            <div class="modal-footer border-0 py-2 bg-light">
                <a id="documentDownloadBtn" href="" target="_blank" class="btn btn-sm btn-primary rounded-pill w-100">
                    <i class="bi bi-download me-1"></i> Buka / Download Asli
                </a>
            </div>
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

    function showDocument(url, name) {
        document.getElementById('documentTitle').innerText = 'Dokumen - ' + name;
        document.getElementById('documentDownloadBtn').href = url;
        
        const imgEl = document.getElementById('documentImage');
        const iframeEl = document.getElementById('documentIframe');
        
        // Check extension
        const ext = url.split('.').pop().toLowerCase();
        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
            imgEl.src = url;
            imgEl.classList.remove('d-none');
            iframeEl.classList.add('d-none');
        } else {
            iframeEl.src = url;
            iframeEl.classList.remove('d-none');
            imgEl.classList.add('d-none');
        }
        
        const modal = new bootstrap.Modal(document.getElementById('documentModal'));
        modal.show();
    }
</script>