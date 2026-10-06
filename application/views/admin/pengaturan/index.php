<?php $this->load->view('layout_admin/header_modul'); ?>

<div class="product-catalog-shell" style="background-color: #f9f9f9; min-height: 100vh;">

    <!-- ====== ODOO-STYLE CONTROL PANEL ====== -->
    <div class="bg-white border-bottom px-4 py-3 mb-4 shadow-sm" style="position: sticky; top: 0; z-index: 900;">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-3">
                <ol class="breadcrumb mb-0" style="font-size: 1.2rem; font-weight: 500;">
                    <li class="breadcrumb-item text-muted">Technical</li>
                    <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">Settings</li>
                </ol>
            </div>
            
            <!-- Search Bar -->
            <div class="input-group" style="max-width: 350px;">
                <input type="text" class="form-control bg-light border-end-0 border-secondary" id="searchSettings" placeholder="Search settings..." style="border-radius: 4px 0 0 4px; box-shadow: none;" onkeyup="filterSettings()">
                <span class="input-group-text bg-light border-secondary border-start-0" style="border-radius: 0 4px 4px 0;">
                    <i class="bi bi-search text-muted"></i>
                </span>
            </div>
        </div>
        
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-primary fw-bold px-3 shadow-sm" style="border-radius: 4px; font-size: 0.9rem;" onclick="location.reload();">
                    Save
                </button>
                <button class="btn btn-light fw-bold px-3 shadow-sm border" style="border-radius: 4px; font-size: 0.9rem;">
                    Discard
                </button>
            </div>
        </div>
    </div>

    <div class="container-fluid px-4 pb-5">
        <div class="row">
            <!-- Left Sidebar Navigation -->
            <div class="col-md-3 col-lg-2">
                <div class="nav flex-column nav-pills settings-sidebar border-end pe-3" id="v-pills-tab" role="tablist" aria-orientation="vertical" style="position: sticky; top: 120px;">
                    <button class="nav-link text-start active fw-bold mb-1" id="tab-general" data-bs-toggle="pill" data-bs-target="#pane-general" type="button" role="tab">
                        <i class="fas fa-cog me-2"></i> General Settings
                    </button>
                    <button class="nav-link text-start fw-bold mb-1" id="tab-core" data-bs-toggle="pill" data-bs-target="#pane-core" type="button" role="tab">
                        <i class="fas fa-building me-2"></i> Core Management
                    </button>
                    <button class="nav-link text-start fw-bold mb-1" id="tab-production" data-bs-toggle="pill" data-bs-target="#pane-production" type="button" role="tab">
                        <i class="fas fa-industry me-2"></i> Production Engine
                    </button>
                    <button class="nav-link text-start fw-bold mb-1" id="tab-supply" data-bs-toggle="pill" data-bs-target="#pane-supply" type="button" role="tab">
                        <i class="fas fa-truck-loading me-2"></i> Supply Chain
                    </button>
                    <button class="nav-link text-start fw-bold mb-1" id="tab-wa" data-bs-toggle="pill" data-bs-target="#pane-wa" type="button" role="tab">
                        <i class="fab fa-whatsapp me-2"></i> WhatsApp Hub
                    </button>
                    <button class="nav-link text-start fw-bold mb-1" id="tab-system" data-bs-toggle="pill" data-bs-target="#pane-system" type="button" role="tab">
                        <i class="fas fa-shield-alt me-2"></i> System & Maint.
                    </button>
                </div>
            </div>

            <!-- Right Content Area -->
            <div class="col-md-9 col-lg-10 ps-lg-4">
                <div class="tab-content" id="v-pills-tabContent">
                    
                    <!-- ============================================== -->
                    <!-- 1. GENERAL SETTINGS -->
                    <!-- ============================================== -->
                    <div class="tab-pane fade show active" id="pane-general" role="tabpanel" tabindex="0">
                        <div class="setting-section mb-5">
                            <h5 class="fw-bold mb-4">Branding & App</h5>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 8px;">
                                        <div class="card-body">
                                            <form action="<?= base_url('pengaturan/update_app_name') ?>" method="POST">
                                                <div class="d-flex align-items-start gap-3">
                                                    <div class="mt-1"><input type="checkbox" class="form-check-input" checked disabled></div>
                                                    <div class="w-100">
                                                        <h6 class="fw-bold mb-1">Company / App Name</h6>
                                                        <p class="text-muted small mb-2">Display name for sidebar and header.</p>
                                                        <input type="text" name="app_name" class="form-control mb-2" value="<?= isset($app_name) ? $app_name : 'ERP CORE' ?>" required style="border-radius: 4px;">
                                                        <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold"><i class="fas fa-save me-1"></i> Apply</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 8px;">
                                        <div class="card-body">
                                            <form action="<?= base_url('pengaturan/update_login_image') ?>" method="POST" enctype="multipart/form-data">
                                                <div class="d-flex align-items-start gap-3">
                                                    <div class="mt-1"><input type="checkbox" class="form-check-input" checked disabled></div>
                                                    <div class="w-100">
                                                        <h6 class="fw-bold mb-1">Login Illustration</h6>
                                                        <p class="text-muted small mb-2">Image shown on the login screen.</p>
                                                        <input type="file" name="login_image" class="form-control mb-2" accept="image/*" required style="border-radius: 4px;">
                                                        <button type="submit" class="btn btn-outline-primary btn-sm px-3 fw-bold"><i class="fas fa-upload me-1"></i> Upload</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="setting-section mb-5">
                            <h5 class="fw-bold mb-4">Navigation & User Interface</h5>
                            <div class="row g-4">
                                <div class="col-md-6 setting-card-wrapper">
                                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 8px;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="mt-1"><input type="checkbox" class="form-check-input" checked disabled></div>
                                                <div class="w-100">
                                                    <h6 class="fw-bold mb-1 search-target">Menu Items & Structure</h6>
                                                    <p class="text-muted small mb-2 search-target">Manage sidebar navigation structure and role permissions.</p>
                                                    <a href="<?= base_url('menu') ?>" class="text-primary fw-bold small text-decoration-none"><i class="fas fa-external-link-alt me-1"></i> Manage Menus</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="setting-section mb-5">
                            <h5 class="fw-bold mb-4 text-danger">Danger Zone</h5>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="card h-100 border-danger shadow-sm bg-danger-subtle" style="border-radius: 8px;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="mt-1"><i class="fas fa-exclamation-triangle text-danger"></i></div>
                                                <div>
                                                    <h6 class="fw-bold mb-1 text-danger">Factory Reset</h6>
                                                    <p class="text-danger small mb-3">Wipes all transaction data permanently. Master data remains intact.</p>
                                                    <button type="button" class="btn btn-danger btn-sm px-4 fw-bold" onclick="confirmSystemReset()">Reset Transactions</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================== -->
                    <!-- 2. CORE MANAGEMENT -->
                    <!-- ============================================== -->
                    <div class="tab-pane fade" id="pane-core" role="tabpanel" tabindex="0">
                        <div class="setting-section mb-5">
                            <h5 class="fw-bold mb-4">Core Management & Users</h5>
                            <div class="row g-4">
                                <?php
                                $core_links = [
                                    ['title' => 'Master Perusahaan', 'desc' => 'Manage multi-company entities', 'link' => 'pengaturan/perusahaan'],
                                    ['title' => 'Kertas & Logo Invoice', 'desc' => 'Invoice templating and sizing', 'link' => 'pengaturan/invoice_template'],
                                    ['title' => 'User Akun', 'desc' => 'Manage system users & credentials', 'link' => 'pengaturan/user_account'],
                                    ['title' => 'Hak Akses & Role', 'desc' => 'Configure division roles', 'link' => 'pengaturan/divisi'],
                                    ['title' => 'Jabatan Kerja', 'desc' => 'Define organizational positions', 'link' => 'pengaturan/jabatan'],
                                    ['title' => 'Database Konsumen', 'desc' => 'Manage customer databases', 'link' => 'pengaturan/konsumen_list'],
                                ];
                                foreach($core_links as $l):
                                ?>
                                <div class="col-md-6 setting-card-wrapper">
                                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 8px;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="mt-1"><input type="checkbox" class="form-check-input" checked disabled></div>
                                                <div class="w-100">
                                                    <h6 class="fw-bold mb-1 search-target"><?= $l['title'] ?></h6>
                                                    <p class="text-muted small mb-2 search-target"><?= $l['desc'] ?></p>
                                                    <a href="<?= base_url($l['link']) ?>" class="text-primary fw-bold small text-decoration-none"><i class="fas fa-external-link-alt me-1"></i> Manage</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================== -->
                    <!-- 3. PRODUCTION ENGINE -->
                    <!-- ============================================== -->
                    <div class="tab-pane fade" id="pane-production" role="tabpanel" tabindex="0">
                        <div class="setting-section mb-5">
                            <h5 class="fw-bold mb-4">Production Engine</h5>
                            <div class="row g-4">
                                <?php
                                $prod_links = [
                                    ['title' => 'Daftar Mesin', 'desc' => 'Configure production machines', 'link' => 'pengaturan/mesin'],
                                    ['title' => 'Master Operator', 'desc' => 'Manage production operators', 'link' => 'pengaturan/operator'],
                                    ['title' => 'Master Satuan', 'desc' => 'UoM (Units of Measure)', 'link' => 'pengaturan/satuan'],
                                    ['title' => 'Kalender Kerja', 'desc' => 'Define holidays and work shifts', 'link' => 'pengaturan/harilibur'],
                                ];
                                foreach($prod_links as $l):
                                ?>
                                <div class="col-md-6 setting-card-wrapper">
                                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 8px;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="mt-1"><input type="checkbox" class="form-check-input" checked disabled></div>
                                                <div class="w-100">
                                                    <h6 class="fw-bold mb-1 search-target"><?= $l['title'] ?></h6>
                                                    <p class="text-muted small mb-2 search-target"><?= $l['desc'] ?></p>
                                                    <a href="<?= base_url($l['link']) ?>" class="text-primary fw-bold small text-decoration-none"><i class="fas fa-external-link-alt me-1"></i> Manage</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================== -->
                    <!-- 4. SUPPLY CHAIN & FINANCE -->
                    <!-- ============================================== -->
                    <div class="tab-pane fade" id="pane-supply" role="tabpanel" tabindex="0">
                        <div class="setting-section mb-5">
                            <h5 class="fw-bold mb-4">Supply Chain & Assets</h5>
                            <div class="row g-4">
                                <?php
                                $sup_links = [
                                    ['title' => 'Bahan Baku', 'desc' => 'Raw material management', 'link' => 'bahan'],
                                    ['title' => 'Master Supplier', 'desc' => 'Vendor and supplier data', 'link' => 'pengaturan/supplier'],
                                    ['title' => 'Kategori Produk', 'desc' => 'Product categories & taxonomy', 'link' => 'pengaturan/kategori'],
                                    ['title' => 'Katalog Produk', 'desc' => 'Main manufacturing products', 'link' => 'manufaktur'],
                                    ['title' => 'Aset EDC', 'desc' => 'EDC terminal management', 'link' => 'pengaturan/mesin_edc'],
                                    ['title' => 'Vault Rekening', 'desc' => 'Bank account configuration', 'link' => 'pengaturan/rekening'],
                                    ['title' => 'Master E-Wallet', 'desc' => 'Digital payment endpoints', 'link' => 'pengaturan/ewallet'],
                                ];
                                foreach($sup_links as $l):
                                ?>
                                <div class="col-md-6 setting-card-wrapper">
                                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 8px;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="mt-1"><input type="checkbox" class="form-check-input" checked disabled></div>
                                                <div class="w-100">
                                                    <h6 class="fw-bold mb-1 search-target"><?= $l['title'] ?></h6>
                                                    <p class="text-muted small mb-2 search-target"><?= $l['desc'] ?></p>
                                                    <a href="<?= base_url($l['link']) ?>" class="text-primary fw-bold small text-decoration-none"><i class="fas fa-external-link-alt me-1"></i> Manage</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================== -->
                    <!-- 5. WHATSAPP GATEWAY -->
                    <!-- ============================================== -->
                    <div class="tab-pane fade" id="pane-wa" role="tabpanel" tabindex="0">
                        <div class="setting-section mb-5">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold mb-0">WhatsApp Hub</h5>
                                <span class="badge <?= !empty($wa_setting->wa_token) ? 'bg-success' : 'bg-danger' ?> px-3 py-2" style="border-radius: 4px;">
                                    <i class="fas fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i> 
                                    <?= !empty($wa_setting->wa_token) ? 'Connected' : 'Disconnected' ?>
                                </span>
                            </div>

                            <div class="card border-0 shadow-sm mb-4" style="border-radius: 8px;">
                                <div class="card-body p-4">
                                    <form action="<?= base_url('pengaturan/update_wa_setting') ?>" method="POST">
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">API KEY (FONNTE)</label>
                                                <div class="input-group">
                                                    <input type="password" name="wa_token" class="form-control" value="<?= $wa_setting->wa_token ?? '' ?>" style="border-radius: 4px 0 0 4px;">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword(this)" style="border-radius: 0 4px 4px 0;"><i class="fas fa-eye"></i></button>
                                                </div>
                                                <p class="text-muted small mt-1">Required to connect with WA Provider.</p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">MANAGER NUMBER</label>
                                                <input type="text" name="manager_number" class="form-control" value="<?= $wa_setting->manager_number ?? '' ?>" placeholder="0812..." style="border-radius: 4px;">
                                                <p class="text-muted small mt-1">Target for internal admin alerts.</p>
                                            </div>
                                            <div class="col-12">
                                                <hr class="my-2 border-light">
                                                <h6 class="small fw-bold mt-3 mb-3 text-muted">AUTOMATION TRIGGERS</h6>
                                                <div class="row g-3">
                                                    <div class="col-md-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" name="notif_spk_baru" value="1" id="notif_spk_baru" <?= (!isset($wa_setting->notif_spk_baru) || $wa_setting->notif_spk_baru == 1) ? 'checked' : '' ?>>
                                                            <label class="form-check-label small fw-bold" for="notif_spk_baru">New SPK Alert</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" name="notif_pembayaran" value="1" id="notif_pembayaran" <?= (!isset($wa_setting->notif_pembayaran) || $wa_setting->notif_pembayaran == 1) ? 'checked' : '' ?>>
                                                            <label class="form-check-label small fw-bold" for="notif_pembayaran">Payment Alert</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" name="notif_pesanan_selesai" value="1" id="notif_pesanan_selesai" <?= (!isset($wa_setting->notif_pesanan_selesai) || $wa_setting->notif_pesanan_selesai == 1) ? 'checked' : '' ?>>
                                                            <label class="form-check-label small fw-bold" for="notif_pesanan_selesai">Order Ready</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" name="notif_pengambilan" value="1" id="notif_pengambilan" <?= (!isset($wa_setting->notif_pengambilan) || $wa_setting->notif_pengambilan == 1) ? 'checked' : '' ?>>
                                                            <label class="form-check-label small fw-bold" for="notif_pengambilan">Pickup Reminder</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mt-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" name="status" value="1" id="wa_on" <?= ($wa_setting->status ?? 1) == 1 ? 'checked' : '' ?>>
                                                            <label class="form-check-label small fw-bold text-success" for="wa_on">Enable Gateway Master</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mt-4">
                                                <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm" style="border-radius: 4px;">Save WA Config</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- WA Test -->
                            <div class="card border-0 shadow-sm" style="border-radius: 8px;">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3">Test Connectivity</h6>
                                    <div class="input-group mb-2" style="max-width: 400px;">
                                        <input type="text" id="test_number" class="form-control" placeholder="Target WA Number (e.g. 0812...)" style="border-radius: 4px 0 0 4px;">
                                        <button class="btn btn-secondary" type="button" onclick="testWa()" id="btn_test_wa" style="border-radius: 0 4px 4px 0;">Test Send</button>
                                    </div>
                                    <div id="test_status" class="small fw-bold mt-2"></div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- ============================================== -->
                    <!-- 6. SYSTEM & MAINTENANCE -->
                    <!-- ============================================== -->
                    <div class="tab-pane fade" id="pane-system" role="tabpanel" tabindex="0">
                        <div class="setting-section mb-5">
                            <h5 class="fw-bold mb-4">System Maintenance</h5>
                            
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 8px;">
                                        <div class="card-body p-4">
                                            <h6 class="fw-bold text-muted text-uppercase small mb-3">System Health</h6>
                                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom border-light">
                                                <span class="text-muted">Environment</span>
                                                <span class="fw-bold">PHP <?= PHP_VERSION ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom border-light">
                                                <span class="text-muted">Database</span>
                                                <span class="fw-bold"><?= strtoupper($system_info['db_driver']) ?> (<?= $system_info['db_database'] ?>)</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom border-light">
                                                <span class="text-muted">Memory Usage</span>
                                                <span class="fw-bold"><?= $performance_metrics['memory_usage'] ?>MB</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom border-light">
                                                <span class="text-muted">Query Time</span>
                                                <span class="fw-bold text-success"><?= $performance_metrics['db_query_time'] ?>ms</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom border-light">
                                                <span class="text-muted">DB Size</span>
                                                <span class="fw-bold text-primary"><?= $maintenance['db_size'] ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Status</span>
                                                <span class="fw-bold <?= $system_health['database'] === 'healthy' ? 'text-success' : 'text-danger' ?>">
                                                    <?= $system_health['database'] === 'healthy' ? 'OPTIMAL' : 'WARNING' ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 8px;">
                                        <div class="card-body p-4">
                                            <h6 class="fw-bold text-muted text-uppercase small mb-3">Maintenance Actions</h6>
                                            <div class="d-flex flex-column gap-2">
                                                <a href="<?= base_url('pengaturan/optimize_database') ?>" class="btn btn-light border text-start py-2">
                                                    <i class="fas fa-magic text-warning me-2" style="width: 20px;"></i> Optimize Database
                                                </a>
                                                <a href="<?= base_url('pengaturan/clear_cache') ?>" class="btn btn-light border text-start py-2">
                                                    <i class="fas fa-broom text-info me-2" style="width: 20px;"></i> Clear System Cache
                                                </a>
                                                <a href="<?= base_url('pengaturan/backup_database') ?>" class="btn btn-light border text-start py-2">
                                                    <i class="fas fa-download text-success me-2" style="width: 20px;"></i> Download DB Backup (SQL)
                                                </a>
                                                <a href="<?= base_url('pengaturan/audit_report') ?>" class="btn btn-light border text-start py-2">
                                                    <i class="fas fa-file-pdf text-danger me-2" style="width: 20px;"></i> Download Audit Report
                                                </a>
                                                <a href="<?= base_url('pengaturan/clear_system_logs') ?>" class="btn btn-outline-danger text-start py-2 mt-2" onclick="return confirm('Clear all system logs? This cannot be undone.');">
                                                    <i class="fas fa-trash-alt me-2" style="width: 20px;"></i> Clear System Logs
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* CSS Khusus Navigation Sidebar Odoo-style */
.settings-sidebar .nav-link {
    color: #475569;
    padding: 0.75rem 1rem;
    border-radius: 6px;
    font-size: 0.95rem;
    background: transparent;
    transition: background-color 0.2s, color 0.2s;
}
.settings-sidebar .nav-link:hover {
    background: #f1f5f9;
    color: #1e293b;
}
.settings-sidebar .nav-link.active {
    background: #e2e8f0;
    color: #0f172a;
}
.setting-section h5 {
    color: #334155;
    font-size: 1.15rem;
    border-bottom: 2px solid #e2e8f0;
    padding-bottom: 0.75rem;
}
/* Menyembunyikan card jika di-search tidak match */
.hidden-by-search {
    display: none !important;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    // Filter Settings (Live Search)
    function filterSettings() {
        const query = document.getElementById('searchSettings').value.toLowerCase();
        
        // Cek semua .setting-card-wrapper
        document.querySelectorAll('.setting-card-wrapper').forEach(card => {
            const texts = Array.from(card.querySelectorAll('.search-target')).map(el => el.innerText.toLowerCase());
            const match = texts.some(text => text.includes(query));
            if(match) {
                card.classList.remove('hidden-by-search');
            } else {
                card.classList.add('hidden-by-search');
            }
        });
    }
</script>
