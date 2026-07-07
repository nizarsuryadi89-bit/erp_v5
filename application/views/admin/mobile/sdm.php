<?php
// application/views/admin/mobile/sdm.php
?>
<style>
    /* Reset and ensure base body doesn't conflict */
    .opd-header {
        background: #fff;
        padding: 15px 20px;
        position: sticky;
        top: 0;
        z-index: 100;
        border-bottom: 1px solid #f0f0f0;
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

    /* Search Box */
    .search-box-container {
        padding: 15px 20px;
        background: #F9FAFB;
    }
    
    .search-box {
        background: #fff;
        border-radius: 50px;
        padding: 0.5rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid #E5E7EB;
    }

    .search-box i {
        color: #9CA3AF;
        font-size: 1.1rem;
    }

    .search-input {
        flex: 1;
        border: none;
        outline: none;
        font-size: 0.95rem;
        background: transparent;
        color: #374151;
    }

    .search-input::placeholder {
        color: #9CA3AF;
    }

    /* Employee Card */
    .employee-card {
        background: #fff;
        border-radius: 16px;
        padding: 15px;
        margin-bottom: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        border: 1px solid #F3F4F6;
        display: flex;
        align-items: center;
        transition: transform 0.15s;
    }

    .employee-card:active {
        transform: scale(0.98);
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

    .status-icon-badge {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #fff;
        font-size: 0.65rem;
        color: #fff;
    }

    .bg-status-online {
        background-color: #10B981; /* Green */
    }

    .bg-status-offline {
        background-color: #9CA3AF; /* Gray */
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

    .emp-email {
        font-size: 0.75rem;
        color: #4B5563;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .btn-detail {
        padding: 0.4rem 0.75rem;
        background: #EEF2FF;
        color: #4F46E5;
        border: none;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        white-space: nowrap;
    }
</style>

<!-- Header -->
<div class="opd-header d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center">
        <a href="<?= base_url('mobile') ?>" class="back-btn-circle me-3">
            <i class="bi bi-chevron-left" style="margin-left: -2px;"></i>
        </a>
        <div>
            <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.1rem;">Daftar Karyawan</h5>
            <div class="text-muted fw-bold mt-1" style="font-size: 0.65rem; letter-spacing: 0.5px;"><?= count($karyawan) ?> KARYAWAN</div>
        </div>
    </div>
</div>

<!-- Search Box -->
<div class="search-box-container">
    <div class="search-box">
        <i class="bi bi-search"></i>
        <input type="text" id="searchKaryawan" class="search-input" placeholder="Cari nama, email, jabatan...">
    </div>
</div>

<!-- Main Content Area -->
<div class="px-3 pb-5" style="background: #F9FAFB; min-height: 100vh;">
    
    <!-- Employee List -->
    <div id="karyawanList">
        <?php foreach ($karyawan as $p): 
            $avatar_url = !empty($p->foto) ? base_url('assets/images/karyawan/' . $p->foto) : base_url('assets/images/karyawan/default.png');
            $status_class = $p->status_aktif ? 'bg-status-online' : 'bg-status-offline';
        ?>
            <div class="employee-card" data-name="<?= strtolower($p->nama_lengkap) ?>"
                data-email="<?= strtolower($p->email) ?>" data-jabatan="<?= strtolower($p->jabatan ?? '') ?>">
                <div class="flex-shrink-0">
                    <div class="avatar-wrapper">
                        <img src="<?= $avatar_url ?>" class="avatar-img"
                            alt="<?= htmlspecialchars($p->nama_lengkap) ?>"
                            onerror="this.onerror=null; this.src='<?= base_url('assets/images/karyawan/default.png') ?>'">
                        <div class="status-icon-badge <?= $status_class ?>"></div>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3" style="min-width: 0;">
                    <div class="emp-name text-truncate"><?= htmlspecialchars($p->nama_lengkap) ?></div>
                    <?php if (isset($p->jabatan) && $p->jabatan): ?>
                        <div class="emp-title text-truncate"><?= htmlspecialchars($p->jabatan) ?></div>
                    <?php endif; ?>
                    <div class="emp-email text-truncate">
                        <i class="bi bi-envelope text-secondary"></i> <?= htmlspecialchars($p->email) ?>
                    </div>
                </div>
                <div class="ms-2">
                    <a href="<?= base_url('mobile/sdm_detail/' . $p->karyawan_id) ?>" class="btn-detail">
                        <i class="bi bi-eye"></i> Detail
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="text-center py-5" style="display: none;">
        <i class="bi bi-search text-muted h1 opacity-25"></i>
        <h6 class="fw-bold mt-3 text-muted">Tidak ada hasil</h6>
        <p class="text-muted small">Karyawan tidak ditemukan berdasarkan pencarian Anda.</p>
    </div>
</div>

<script>
    document.getElementById('searchKaryawan').addEventListener('input', function () {
        var keyword = this.value.toLowerCase().trim();
        var cards = document.querySelectorAll('.employee-card');
        var visibleCount = 0;

        cards.forEach(function (card) {
            var name = card.getAttribute('data-name') || '';
            var email = card.getAttribute('data-email') || '';
            var jabatan = card.getAttribute('data-jabatan') || '';

            if (name.indexOf(keyword) > -1 || email.indexOf(keyword) > -1 || jabatan.indexOf(keyword) > -1) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        document.getElementById('emptyState').style.display = visibleCount === 0 ? 'block' : 'none';
    });
</script>