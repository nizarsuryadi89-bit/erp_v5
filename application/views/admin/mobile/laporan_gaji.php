<?php
// application/views/admin/mobile/laporan_gaji.php
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
        flex-direction: column;
    }

    .employee-card-body {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .avatar-wrapper {
        position: relative;
        width: 50px;
        height: 50px;
        flex-shrink: 0;
    }

    .avatar-img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #E5E7EB;
        background: #F3F4F6;
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
    }

    .salary-box {
        background: #F0FDF4;
        border: 1px solid #BBF7D0;
        border-radius: 12px;
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .salary-label {
        font-size: 0.75rem;
        color: #166534;
        font-weight: 700;
        text-transform: uppercase;
    }

    .salary-amount {
        font-size: 1.1rem;
        font-weight: 900;
        color: #16A34A;
    }
</style>

<!-- Header -->
<div class="opd-header d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center">
        <a href="<?= base_url('mobile') ?>" class="back-btn-circle me-3">
            <i class="bi bi-chevron-left" style="margin-left: -2px;"></i>
        </a>
        <div>
            <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.1rem;">Laporan Gaji</h5>
            <div class="text-muted fw-bold mt-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">ESTIMASI BULAN INI</div>
        </div>
    </div>
    
    <!-- Month Picker Filter -->
    <form action="" method="get" id="filterForm">
        <input type="month" name="bulan" class="form-control form-control-sm border-0 bg-light fw-bold text-primary" 
               style="border-radius: 50px; padding: 5px 15px; width: auto;"
               value="<?= $bulan_aktif ?>" onchange="document.getElementById('filterForm').submit();">
    </form>
</div>

<!-- Search Box -->
<div class="search-box-container">
    <div class="search-box">
        <i class="bi bi-search"></i>
        <input type="text" id="searchKaryawan" class="search-input" placeholder="Cari nama karyawan atau jabatan...">
    </div>
</div>

<!-- Main Content Area -->
<div class="px-3 pb-5" style="background: #F9FAFB; min-height: 100vh;">
    
    <!-- Summary -->
    <div class="mb-3 text-end">
        <span class="badge bg-white text-secondary border shadow-sm" style="font-size: 0.7rem; padding: 5px 10px;">
            Total Karyawan: <?= count($salary_data) ?>
        </span>
    </div>

    <!-- Employee List -->
    <div id="karyawanList">
        <?php foreach ($salary_data as $s): 
            $avatar_url = !empty($s['foto']) ? base_url('assets/images/karyawan/' . $s['foto']) : base_url('assets/images/karyawan/default.png');
        ?>
            <div class="employee-card data-item" data-name="<?= strtolower($s['nama_lengkap']) ?>"
                data-jabatan="<?= strtolower($s['nama_jabatan'] ?? '') ?>">
                
                <div class="employee-card-body">
                    <div class="avatar-wrapper">
                        <img src="<?= $avatar_url ?>" class="avatar-img" onerror="this.onerror=null; this.src='<?= base_url('assets/images/karyawan/default.png') ?>'">
                    </div>
                    <div class="flex-grow-1 ms-3" style="min-width: 0;">
                        <div class="emp-name text-truncate"><?= htmlspecialchars($s['nama_lengkap']) ?></div>
                        <div class="emp-title text-truncate"><?= htmlspecialchars($s['nama_jabatan']) ?></div>
                    </div>
                </div>

                <div class="salary-box">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-wallet2 text-success me-2" style="font-size: 1.2rem;"></i>
                        <span class="salary-label">Take Home Pay</span>
                    </div>
                    <div class="salary-amount">
                        Rp <?= number_format($s['total_gaji'], 0, ',', '.') ?>
                    </div>
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
        var cards = document.querySelectorAll('.data-item');
        var visibleCount = 0;

        cards.forEach(function (card) {
            var name = card.getAttribute('data-name') || '';
            var jabatan = card.getAttribute('data-jabatan') || '';

            if (name.indexOf(keyword) > -1 || jabatan.indexOf(keyword) > -1) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        document.getElementById('emptyState').style.display = visibleCount === 0 ? 'block' : 'none';
    });
</script>
