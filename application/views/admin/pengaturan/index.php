<?php $this->load->view('layout_admin/header_modul'); ?>
<?php $this->load->view('admin/pengaturan/_settings_nav'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* ==========================================================================
    CLEAN ERP SETTINGS DESIGN SYSTEM
    ========================================================================== */
    :root {
        --tgm-primary: #4361ee;
        --tgm-bg: #f8fafc;
        --tgm-border: #e2e8f0;
        --tgm-text-main: #1e293b;
        --tgm-text-muted: #64748b;
        --premium-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .base-card {
        background: white;
        border: 1px solid var(--tgm-border);
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.2s ease;
    }

    .card-header-clean {
        padding: 1rem 1.25rem;
        background: #fff;
        border-bottom: 1px solid var(--tgm-border);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-header-clean h6 {
        margin: 0;
        font-weight: 700;
        color: var(--tgm-text-main);
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }

    /* Stats Section */
    .stat-pill {
        background: white;
        border: 1px solid var(--tgm-border);
        padding: 1.25rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .stat-pill-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .stat-pill-label {
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--tgm-text-muted);
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    .stat-pill-value {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--tgm-text-main);
        line-height: 1;
    }

    /* Module Hub */
    .hub-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 1rem;
        background: #fdfdfd;
        border: 1px solid var(--tgm-border);
        border-radius: 10px;
        text-decoration: none !important;
        color: var(--tgm-text-main);
        transition: all 0.2s;
    }

    .hub-link:hover {
        background: white;
        border-color: var(--tgm-primary);
        box-shadow: var(--premium-shadow);
        transform: translateY(-2px);
    }

    .hub-icon {
        width: 36px;
        height: 36px;
        background: white;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--tgm-border);
        font-size: 1rem;
    }

    .hub-info h6 {
        margin: 0;
        font-size: 0.85rem;
        font-weight: 700;
    }

    .hub-info .count {
        font-size: 0.65rem;
        color: var(--tgm-text-muted);
        text-transform: uppercase;
        font-weight: 600;
    }

    /* Responsive Grid fixes */
    .animate-in {
        animation: fadeIn 0.4s ease-out forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="app-content mt-3">
    <div class="container-fluid">
        <!-- Top Stats Row -->
        <div class="row g-3 mb-4 animate-in">
            <div class="col-6 col-md-3">
                <div class="stat-pill shadow-sm">
                    <div class="stat-pill-icon bg-primary-subtle text-primary"><i class="fas fa-users"></i></div>
                    <div>
                        <div class="stat-pill-label">Pengguna</div>
                        <div class="stat-pill-value"><?= number_format($users) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-pill shadow-sm">
                    <div class="stat-pill-icon bg-success-subtle text-success"><i class="fas fa-building"></i></div>
                    <div>
                        <div class="stat-pill-label">Entitas</div>
                        <div class="stat-pill-value"><?= number_format($store) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-pill shadow-sm">
                    <div class="stat-pill-icon bg-info-subtle text-info"><i class="fas fa-shield-alt"></i></div>
                    <div>
                        <div class="stat-pill-label">Roles</div>
                        <div class="stat-pill-value"><?= number_format($getdivisi) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-pill shadow-sm">
                    <div class="stat-pill-icon bg-warning-subtle text-warning"><i class="fas fa-microchip"></i></div>
                    <div>
                        <div class="stat-pill-label">System Load</div>
                        <div class="stat-pill-value"><?= $system_health['system_load'] ?>%</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Row -->
        <div class="row g-4 mb-4 animate-in" style="animation-delay: 0.05s">
            <div class="col-lg-7">
                <div class="base-card shadow-sm h-100">
                    <div class="card-header-clean">
                        <i class="fas fa-database text-warning"></i>
                        <h6>Database Table Load (Records)</h6>
                    </div>
                    <div class="p-3">
                        <canvas id="dbStatsChart" style="max-height: 200px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="base-card shadow-sm h-100">
                    <div class="card-header-clean">
                        <i class="fas fa-chart-pie text-info"></i>
                        <h6>User Role Distribution</h6>
                    </div>
                    <div class="p-3 d-flex align-items-center">
                        <div style="width: 150px;">
                            <canvas id="userRoleChart"></canvas>
                        </div>
                        <div class="ms-4 flex-grow-1">
                            <?php foreach (array_slice($users_by_role, 0, 4) as $role): ?>
                                <div class="d-flex justify-content-between small mb-1 text-truncate">
                                    <span class="text-muted text-truncate"
                                        style="max-width: 100px;"><?= $role->divisi_nama ?: 'No Role' ?></span>
                                    <span class="fw-bold"><?= $role->total ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Main Module Hub -->
            <div class="col-lg-8 animate-in" style="animation-delay: 0.1s">
                <div class="base-card shadow-sm h-100">
                    <div class="card-header-clean">
                        <i class="fas fa-grid-2 text-primary"></i>
                        <h6>Konfigurasi & Master Data</h6>
                    </div>
                    <div class="p-4">
                        <!-- Module Search -->
                        <div class="mb-4">
                            <div class="input-group input-group-sm shadow-sm"
                                style="border-radius: 20px; overflow: hidden;">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i
                                        class="fas fa-search"></i></span>
                                <input type="text" id="moduleSearch" class="form-control border-start-0 ps-0"
                                    placeholder="Cari pengaturan atau master data..." onkeyup="filterModules()">
                            </div>
                        </div>

                        <?php
                        $groups = [
                            'CORE MANAGEMENT' => [
                                ['title' => 'Master Perusahaan', 'count' => $store, 'link' => 'pengaturan/perusahaan', 'icon' => 'fa-university', 'color' => '#1e293b'],
                                ['title' => 'Kertas & Logo Invoice', 'count' => 'Template', 'link' => 'pengaturan/invoice_template', 'icon' => 'fa-receipt', 'color' => '#e74c3c', 'is_new' => true],
                                ['title' => 'User Akun', 'count' => $users, 'link' => 'pengaturan/user_account', 'icon' => 'fa-user-cog', 'color' => '#4361ee'],
                                ['title' => 'Hak Akses & Role', 'count' => $getdivisi, 'link' => 'pengaturan/divisi', 'icon' => 'fa-shield-alt', 'color' => '#7209b7'],
                                ['title' => 'Jabatan Kerja', 'count' => $getjabatan, 'link' => 'pengaturan/jabatan', 'icon' => 'fa-id-card', 'color' => '#3a0ca3'],
                            ],
                            'PRODUCTION ENGINE' => [
                                ['title' => 'Daftar Mesin', 'count' => $mesin, 'link' => 'pengaturan/mesin', 'icon' => 'fa-cogs', 'color' => '#2d3436'],
                                ['title' => 'Master Operator', 'count' => $getoperator, 'link' => 'pengaturan/operator', 'icon' => 'fa-hard-hat', 'color' => '#e67e22'],
                                ['title' => 'Master Satuan', 'count' => $getsatuan, 'link' => 'pengaturan/satuan', 'icon' => 'fa-ruler-combined', 'color' => '#f39c12'],
                                ['title' => 'Kalender Kerja', 'count' => 'Jadwal', 'link' => 'pengaturan/harilibur', 'icon' => 'fa-calendar-check', 'color' => '#d63031'],
                            ],
                            'ASET PERUSAHAAN' => [
                                ['title' => 'Aset EDC', 'count' => $getedc ?? 'Aset', 'link' => 'pengaturan/mesin_edc', 'icon' => 'fa-credit-card', 'color' => '#8e44ad'],
                                ['title' => 'Aset Lainnya', 'count' => $getaset ?? '0', 'link' => 'pengaturan/aset', 'icon' => 'fa-boxes', 'color' => '#16a085'],
                            ],
                            'SUPPLY CHAIN & FINANCE' => [
                                ['title' => 'Bahan Baku', 'count' => $bahanbaku, 'link' => 'bahan', 'icon' => 'fa-warehouse', 'color' => '#4361ee'],
                                ['title' => 'Master Supplier', 'count' => $getsupplier, 'link' => 'pengaturan/supplier', 'icon' => 'fa-shipping-fast', 'color' => '#2980b9'],
                                ['title' => 'Kategori Produk', 'count' => $getkategori, 'link' => 'pengaturan/kategori', 'icon' => 'fa-layer-group', 'color' => '#f72585'],
                                ['title' => 'Subkategori Produk', 'count' => 'Setup', 'link' => 'pengaturan/subkategori', 'icon' => 'fa-tags', 'color' => '#ff006e'],
                                ['title' => 'Katalog Produk', 'count' => $getproduct ?? 0, 'link' => 'manufaktur', 'icon' => 'fa-boxes', 'color' => '#3f37c9'],
                                ['title' => 'Vault Rekening', 'count' => $getrekening, 'link' => 'pengaturan/rekening', 'icon' => 'fa-wallet', 'color' => '#10ac84'],
                                ['title' => 'Master E-Wallet', 'count' => $getewallet ?? 'Aset', 'link' => 'pengaturan/ewallet', 'icon' => 'fa-mobile-alt', 'color' => '#ff9ff3'],
                                ['title' => 'Konfigurasi Nota', 'count' => 'Setup', 'link' => 'pengaturan/penomoran', 'icon' => 'fa-file-invoice', 'color' => '#4361ee'],
                            ],
                            'SYSTEM & CRM' => [
                                ['title' => 'Database Konsumen', 'count' => $konsumen, 'link' => 'pengaturan/konsumen_list', 'icon' => 'fa-users', 'color' => '#16a085'],
                                ['title' => 'Pengaturan Menu', 'count' => $getmenu, 'link' => 'menu', 'icon' => 'fa-tasks', 'color' => '#560bad'],
                                ['title' => 'WhatsApp Hub', 'count' => 'Gateway', 'link' => '#wa_hub', 'icon' => 'fab fa-whatsapp', 'color' => '#25D366'],
                                ['title' => 'Audit Log', 'count' => 'System', 'link' => 'errorlog', 'icon' => 'fa-terminal', 'color' => '#6c757d'],
                            ]
                        ];
                        ?>

                        <?php foreach ($groups as $group_name => $modules): ?>
                            <div class="module-group mb-4">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div
                                        style="width: 4px; height: 16px; background: var(--tgm-primary); border-radius: 2px;">
                                    </div>
                                    <h6 class="mb-0 text-muted fw-bold" style="font-size: 0.70rem; letter-spacing: 1px;">
                                        <?= $group_name ?>
                                    </h6>
                                </div>
                                <div class="row g-3">
                                    <?php foreach ($modules as $mod): ?>
                                        <div class="col-6 col-md-4 hub-item-wrapper">
                                            <a href="<?= (strpos($mod['link'], '#') === 0 || strpos($mod['link'], 'javascript:') === 0) ? $mod['link'] : base_url($mod['link']) ?>" class="hub-link" <?= strpos($mod['link'], '#') === 0 ? 'onclick="document.querySelector(\''.$mod['link'].'\').scrollIntoView({behavior:\'smooth\'}); return false;"' : '' ?>>
                                                <div class="hub-icon" style="color: <?= $mod['color'] ?>">
                                                    <?php
                                                    $icon_class = strpos($mod['icon'], 'fa') === 0 ? $mod['icon'] : 'fas ' . $mod['icon'];
                                                    if (strpos($mod['icon'], 'fa-') === 0 && strpos($mod['icon'], 'fab ') === false) {
                                                        $icon_class = 'fas ' . $mod['icon'];
                                                    }
                                                    ?>
                                                    <i class="<?= $icon_class ?>"></i>
                                                </div>
                                                <div class="hub-info">
                                                    <h6 class="module-title">
                                                        <?= $mod['title'] ?>
                                                        <?php if (!empty($mod['is_new'])): ?>
                                                            <span class="badge bg-danger text-white rounded-pill ms-1" style="font-size: 0.55rem; padding: 2px 6px; font-weight: 800; vertical-align: middle; letter-spacing: 0.5px;">NEW</span>
                                                        <?php endif; ?>
                                                    </h6>
                                                    <span class="count"><?= $mod['count'] ?> Item</span>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="col-lg-4 animate-in" style="animation-delay: 0.2s">
                <div class="base-card shadow-sm mb-4">
                    <div class="card-header-clean">
                        <i class="fas fa-heartbeat text-success"></i>
                        <h6>Status Sistem</h6>
                    </div>
                    <div class="p-3">
                        <div class="bg-light rounded-3 p-3 mb-2">
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">System Health</span>
                                <span
                                    class="fw-bold text-success"><?= $system_health['database'] === 'healthy' ? 'OPTIMAL' : 'WARNING' ?></span>
                            </div>
                            <div class="progress mb-2" style="height: 6px; border-radius: 10px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: <?= $system_health['user_distribution'] ?>%"></div>
                            </div>
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Environment</span>
                                <span class="fw-bold">PHP <?= PHP_VERSION ?></span>
                            </div>
                            <div class="d-flex justify-content-between small border-top pt-1 text-truncate">
                                <span class="text-muted">Database</span>
                                <span class="fw-bold"><?= strtoupper($system_info['db_driver']) ?>
                                    (<?= $system_info['db_database'] ?>)</span>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="border rounded-3 p-2 text-center">
                                    <div class="small text-muted mb-1">Memory</div>
                                    <div class="fw-bold"><?= $performance_metrics['memory_usage'] ?>MB</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded-3 p-2 text-center">
                                    <div class="small text-muted mb-1">Query</div>
                                    <div class="fw-bold text-primary"><?= $performance_metrics['db_query_time'] ?>ms
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="<?= base_url('pengaturan/audit_report') ?>"
                                class="btn btn-outline-primary btn-sm w-100 fw-bold shadow-sm"
                                style="border-radius: 8px;">
                                <i class="fas fa-file-export me-1"></i> DOWNLOAD SYSTEM AUDIT
                            </a>
                        </div>
                    </div>
                </div>

                <div class="base-card shadow-sm mb-4">
                    <div class="card-header-clean">
                        <i class="fas fa-tools text-primary"></i>
                        <h6>Maintenance & Reliability</h6>
                    </div>
                    <div class="p-3">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Ukuran Database</span>
                                <span class="fw-bold"><?= $maintenance['db_size'] ?></span>
                            </div>
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Backup Terakhir</span>
                                <span class="fw-bold text-success"><?= $maintenance['last_backup'] ?></span>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="<?= base_url('pengaturan/optimize_database') ?>"
                                class="btn btn-light btn-sm text-start shadow-sm">
                                <i class="fas fa-magic text-warning me-2"></i> Optimize Database
                            </a>
                            <a href="<?= base_url('pengaturan/clear_cache') ?>"
                                class="btn btn-light btn-sm text-start shadow-sm">
                                <i class="fas fa-broom text-info me-2"></i> Clear System Cache
                            </a>
                            <a href="<?= base_url('cron/cleanup_absensi/3') ?>"
                                class="btn btn-light btn-sm text-start shadow-sm"
                                onclick="return confirm('Anda yakin ingin menghapus fisik foto absensi yang lebih tua dari 3 bulan? (Data rekap absen akan tetap aman).');">
                                <i class="fas fa-trash-alt text-danger me-2"></i> Bersihkan Foto Absen Lama (>3 Bulan)
                            </a>
                            <a href="<?= base_url('pengaturan/clear_system_logs') ?>"
                                class="btn btn-light btn-sm text-start shadow-sm"
                                onclick="return confirm('PENTING: Anda yakin ingin mengosongkan riwayat Log Sistem (Audit Log, Activity Log, Page Visits, dan Backup Tables)? Data transaksi dan operasional TIDAK akan terhapus.');">
                                <i class="fas fa-trash-restore-alt text-danger me-2"></i> Bersihkan Log Sistem (Maintenance)
                            </a>
                            <a href="<?= base_url('pengaturan/backup_database') ?>"
                                class="btn btn-light btn-sm text-start shadow-sm">
                                <i class="fas fa-cloud-download-alt text-success me-2"></i> Download Database (SQL)
                            </a>
                        </div>
                        
                        <!-- To-Do List Maintenance -->
                        <div class="mt-4 pt-3 border-top border-light">
                            <h6 class="small fw-bold text-uppercase text-muted mb-3" style="letter-spacing: 0.5px;"><i class="fas fa-clipboard-list me-1 text-primary"></i> To-Do List Admin</h6>
                            
                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 35px; height: 35px;">
                                    <span class="fw-bold" style="font-size: 0.75rem;">1B</span>
                                </div>
                                <div>
                                    <h6 class="mb-0 small fw-bold text-dark">Setiap 1 Bulan</h6>
                                    <p class="text-muted mb-0" style="font-size: 0.75rem; line-height: 1.4;">Klik <b>Download Database</b> untuk mem-backup seluruh sistem ke komputer lokal (Flashdisk/Hardisk).</p>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2 me-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 35px; height: 35px;">
                                    <span class="fw-bold" style="font-size: 0.75rem;">3B</span>
                                </div>
                                <div>
                                    <h6 class="mb-0 small fw-bold text-dark">Setiap 3 Bulan</h6>
                                    <p class="text-muted mb-0" style="font-size: 0.75rem; line-height: 1.4;">Klik <b>Bersihkan Foto Absen Lama</b> agar ruang hardisk/SSD hosting server tidak cepat penuh.</p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start">
                                <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2 me-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 35px; height: 35px;">
                                    <span class="fw-bold" style="font-size: 0.75rem;">6B</span>
                                </div>
                                <div>
                                    <h6 class="mb-0 small fw-bold text-dark">Setiap 6 Bulan</h6>
                                    <p class="text-muted mb-0" style="font-size: 0.75rem; line-height: 1.4;">Klik <b>Bersihkan Log Sistem</b> dan <b>Optimize Database</b> untuk menjaga kecepatan query database agar tidak lemot.</p>
                                </div>
                            </div>
                        </div>
                        <!-- End To-Do List -->

                    </div>
                </div>

                <div class="base-card shadow-sm h-100">
                    <div class="card-header-clean">
                        <i class="fas fa-history text-info"></i>
                        <h6>Akses Terkini</h6>
                    </div>
                    <div class="p-3">
                        <?php if (!empty($recent_activities['recent_logins'])): ?>
                            <?php foreach ($recent_activities['recent_logins'] as $login): ?>
                                <div class="d-flex align-items-center gap-3 mb-3 border-bottom pb-2 last-no-border">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 32px; height: 32px; font-size: 0.75rem; font-weight: 700;">
                                        <?= strtoupper(substr($login->username, 0, 1)) ?>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="small fw-bold text-truncate"><?= $login->username ?></div>
                                        <div class="text-muted" style="font-size: 0.65rem;">
                                            <?= date('H:i, d M', strtotime($login->login_time)) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- WhatsApp Gateway Card -->
        <div id="wa_hub" class="base-card shadow-sm mb-4 animate-in" style="animation-delay: 0.3s">
            <div class="card-header-clean bg-white">
                <i class="fab fa-whatsapp text-success"></i>
                <h6>WhatsApp Gateway Configuration</h6>
                <div class="ms-auto">
                    <span
                        class="badge <?= !empty($wa_setting->wa_token) ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> rounded-pill">
                        <?= !empty($wa_setting->wa_token) ? 'Connected' : 'Disconnected' ?>
                    </span>
                </div>
            </div>
            <div class="p-4 border-bottom bg-light">
                <div class="row g-3">
                    <?php
                    $w_stats = [
                        ['label' => 'Total', 'val' => $wa_stats['total'], 'color' => 'primary'],
                        ['label' => 'Today', 'val' => $wa_stats['today'], 'color' => 'success'],
                        ['label' => 'Month', 'val' => $wa_stats['month'], 'color' => 'info'],
                        ['label' => 'Failed', 'val' => $wa_stats['failed'], 'color' => 'danger'],
                    ];
                    foreach ($w_stats as $ws): ?>
                        <div class="col-md-3">
                            <div
                                class="bg-white p-2 px-3 rounded-pill border border-<?= $ws['color'] ?> d-flex justify-content-between align-items-center">
                                <span class="small fw-bold text-<?= $ws['color'] ?>"><?= $ws['label'] ?></span>
                                <span class="h6 mb-0 fw-800"><?= number_format($ws['val']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="p-4">
                <div class="row g-4">
                    <div class="col-md-7 border-end pe-md-4">
                        <form action="<?= base_url('pengaturan/update_wa_setting') ?>" method="POST">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">API KEY (FONNTE)</label>
                                <div class="input-group input-group-sm">
                                    <input type="password" name="wa_token" class="form-control"
                                        value="<?= $wa_setting->wa_token ?? '' ?>">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePassword(this)"><i class="fas fa-eye"></i></button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">MANAGER NUMBER</label>
                                <input type="text" name="manager_number" class="form-control form-control-sm"
                                    value="<?= $wa_setting->manager_number ?? '' ?>" placeholder="0812...">
                            </div>
                            <div class="d-flex align-items-center gap-4 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" value="1" id="wa_on"
                                        <?= ($wa_setting->status ?? 1) == 1 ? 'checked' : '' ?>>
                                    <label class="form-check-label small fw-bold text-success" for="wa_on">Enable Gateway Master</label>
                                </div>
                            </div>
                            
                            <hr class="my-3">
                            <h6 class="small fw-bold text-muted mb-3"><i class="fas fa-robot me-2"></i>AUTOMATION RULES</h6>
                            
                            <div class="row g-2 mb-4">
                                <div class="col-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="notif_spk_baru" value="1" id="notif_spk_baru"
                                            <?= (!isset($wa_setting->notif_spk_baru) || $wa_setting->notif_spk_baru == 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label small fw-bold" for="notif_spk_baru">Notif SPK Baru</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="notif_pembayaran" value="1" id="notif_pembayaran"
                                            <?= (!isset($wa_setting->notif_pembayaran) || $wa_setting->notif_pembayaran == 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label small fw-bold" for="notif_pembayaran">Notif Pembayaran</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="notif_pesanan_selesai" value="1" id="notif_pesanan_selesai"
                                            <?= (!isset($wa_setting->notif_pesanan_selesai) || $wa_setting->notif_pesanan_selesai == 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label small fw-bold" for="notif_pesanan_selesai">Notif Pesanan Selesai</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="notif_pengambilan" value="1" id="notif_pengambilan"
                                            <?= (!isset($wa_setting->notif_pengambilan) || $wa_setting->notif_pengambilan == 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label small fw-bold" for="notif_pengambilan">Notif Pengambilan</label>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm px-4">Save Changes</button>
                        </form>
                    </div>
                    <div class="col-md-5 ps-md-4">
                        <div class="bg-light p-3 rounded-4 border">
                            <h6 class="small fw-bold mb-2">Test Connectivity</h6>
                            <div class="input-group input-group-sm mb-2">
                                <input type="text" id="test_number" class="form-control" placeholder="Target Number">
                                <button class="btn btn-success" type="button" onclick="testWa()"
                                    id="btn_test_wa">Test</button>
                            </div>
                            <div id="test_status" class="small mt-1"></div>
                        </div>
                    </div>
                </div>

                <!-- Recent WA Logs -->
                <div class="mt-4 border-top pt-4">
                    <h6 class="small fw-bold text-muted mb-3"><i class="fas fa-history me-2"></i>LOG PENGIRIMAN TERAKHIR</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.8rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th>WAKTU</th>
                                    <th>TARGET</th>
                                    <th>PESAN</th>
                                    <th class="text-center">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($wa_recent_logs)): ?>
                                    <?php foreach($wa_recent_logs as $log): ?>
                                    <tr>
                                        <td class="text-nowrap fw-bold text-muted"><?= date('d/m/Y H:i', strtotime($log->created_at)) ?></td>
                                        <td class="fw-bold"><?= $log->target ?></td>
                                        <td class="text-truncate" style="max-width: 300px;" title="<?= htmlspecialchars($log->message) ?>">
                                            <?= htmlspecialchars($log->message) ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if(strtolower($log->status) == 'success'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-2">Sukses</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 rounded-pill px-2" title="<?= htmlspecialchars($log->response) ?>">Gagal</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Belum ada log pengiriman.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="base-card border-danger bg-danger-subtle animate-in" style="animation-delay: 0.4s">
            <div class="p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h6 class="mb-1 text-danger fw-800"><i class="fas fa-exclamation-triangle me-2"></i>SISTEM RESET
                        (DANGER ZONE)</h6>
                    <p class="mb-0 small text-danger">Aksi ini akan menghapus permanen semua data transaksi. Gunakan
                        dengan sangat hati-hati!</p>
                </div>
                <button type="button" class="btn btn-danger btn-sm px-4 fw-bold" onclick="confirmSystemReset()">
                    RESET TRANSAKSI
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Charts Initialization
    const ctxDb = document.getElementById('dbStatsChart').getContext('2d');
    new Chart(ctxDb, {
        type: 'bar',
        data: {
            labels: [<?php foreach ($db_stats as $name => $s)
                echo "'" . ucfirst($name) . "',"; ?>],
            datasets: [{
                label: 'Records',
                data: [<?php foreach ($db_stats as $s)
                    echo $s['count'] . ","; ?>],
                backgroundColor: 'rgba(67, 97, 238, 0.2)',
                borderColor: 'rgba(67, 97, 238, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { x: { grid: { display: false } }, y: { grid: { display: false } } }
        }
    });

    const ctxRole = document.getElementById('userRoleChart').getContext('2d');
    new Chart(ctxRole, {
        type: 'doughnut',
        data: {
            labels: [<?php foreach ($users_by_role as $r)
                echo "'" . ($r->divisi_nama ?: 'No Role') . "',"; ?>],
            datasets: [{
                data: [<?php foreach ($users_by_role as $r)
                    echo $r->total . ","; ?>],
                backgroundColor: ['#4361ee', '#3a0ca3', '#7209b7', '#f72585', '#4cc9f0', '#b5179e'],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '70%',
            plugins: { legend: { display: false } }
        }
    });
</script>
<script>
    function confirmSystemReset() {
        Swal.fire({
            title: 'Hapus Semua Transaksi?',
            text: "Data Master (Customer, Produk) tetap aman. Transaksi akan hilang permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Reset!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Konfirmasi Sandi',
                    text: "Ketik 'HAPUS' untuk mengeksekusi.",
                    input: 'text',
                    showCancelButton: true,
                    preConfirm: (v) => {
                        if (v !== 'HAPUS') Swal.showValidationMessage('Ketik HAPUS untuk lanjut')
                    }
                }).then((res) => {
                    if (res.isConfirmed) window.location.href = "<?= base_url('pengaturan/reset_dummy_data') ?>";
                })
            }
        })
    }

    function togglePassword(btn) {
        const input = btn.closest('.input-group').querySelector('input');
        input.type = input.type === 'password' ? 'text' : 'password';
        btn.innerHTML = `<i class="fas fa-${input.type === 'password' ? 'eye' : 'eye-slash'}"></i>`;
    }

    function filterModules() {
        const query = document.getElementById('moduleSearch').value.toLowerCase();
        const items = document.querySelectorAll('.hub-item-wrapper');
        const groups = document.querySelectorAll('.module-group');

        items.forEach(item => {
            const title = item.querySelector('.module-title').innerText.toLowerCase();
            if (title.includes(query)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });

        // Hide group header if no items visible
        groups.forEach(group => {
            const visibleItems = Array.from(group.querySelectorAll('.hub-item-wrapper')).filter(i => i.style.display !== 'none');
            group.style.display = visibleItems.length > 0 ? 'block' : 'none';
        });
    }

    function testWa() {
        const num = document.getElementById('test_number').value;
        if (!num) return alert('Input number!');

        const btn = document.getElementById('btn_test_wa');
        const st = document.getElementById('test_status');
        btn.disabled = true;
        st.innerHTML = 'Sending...';

        $.post('<?= base_url('pengaturan/test_wa') ?>', { target_number: num }, (res) => {
            st.innerHTML = res.status === 'success' ? '<span class="text-success">Success! Check your WA.</span>' : '<span class="text-danger">Failed to send.</span>';
            btn.disabled = false;
        }, 'json').fail(() => {
            st.innerHTML = '<span class="text-danger">Server Error.</span>';
            btn.disabled = false;
        });
    }
</script>