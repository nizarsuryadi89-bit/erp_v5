<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    :root {
        --dev-primary: #0f172a;
        --dev-accent: #38bdf8;
        --dev-bg: #f8fafc;
        --dev-card-bg: #ffffff;
        --dev-glass: rgba(15, 23, 42, 0.9);
    }

    body {
        background-color: var(--dev-bg);
    }

    .dev-container {
        padding: 0.5rem 1rem;
    }

    .dev-header {
        background: var(--dev-glass);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 1rem 1.5rem;
        margin-bottom: 1rem;
        color: white;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        border-color: var(--dev-accent);
    }

    .role-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .role-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .role-card:hover {
        border-color: var(--dev-accent);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .role-card-header {
        padding: 1.25rem;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #f1f5f9;
        background: #f8fafc;
    }

    .role-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #e0f2fe;
        color: #0369a1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-right: 1rem;
    }

    .role-name {
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        font-size: 1.1rem;
    }

    .role-card-body {
        padding: 1.25rem;
    }

    .role-card-footer {
        padding: 1rem 1.25rem;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
    }

    .preview-btn {
        width: 100%;
        padding: 0.75rem;
        border-radius: 8px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.8rem;
        transition: all 0.2s;
    }

    .preview-btn-primary {
        background: #0f172a;
        color: white;
        border: none;
    }

    .preview-btn-primary:hover {
        background: #334155;
        color: #38bdf8;
    }

    .badge-dev {
        font-size: 0.7rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .glow-text {
        text-shadow: 0 0 10px rgba(56, 189, 248, 0.5);
    }

    .matrix-table th {
        font-size: 0.7rem !important;
        white-space: nowrap;
        background: #f8fafc;
        vertical-align: middle;
    }

    .matrix-table td {
        text-align: center;
        padding: 0.75rem 0.5rem;
    }

    .access-icon {
        font-size: 1.1rem;
        cursor: pointer;
        transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .access-icon:hover {
        transform: scale(1.4);
    }

    .access-icon.loading {
        opacity: 0.5;
        pointer-events: none;
        animation: rotate 1s linear infinite;
    }

    @keyframes rotate {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .text-success-premium {
        color: #10b981;
    }

    .text-warning-premium {
        color: #f59e0b;
    }

    .text-danger-premium {
        color: #ef4444;
    }
</style>

<div class="dev-container">
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="dev-header">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-info text-dark mb-1 me-2 p-1 px-2" style="font-size: 0.65rem;">DEVELOPER MODE</span>
                        <span class="text-info opacity-75" style="font-size: 0.7rem;">System version 4.2.0-STABLE</span>
                    </div>
                    <h3 class="fw-900 glow-text mb-1">Pusat Kendali Pengembang</h3>
                    <p class="text-white-50 small mb-0">Selamat datang kembali,
                        <strong><?= ucfirst($this->session->userdata('username')) ?></strong>. Anda memiliki akses
                        mutlak ke seluruh lapisan sistem ERP.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end d-none d-lg-block">
                    <i class="fas fa-microchip fa-3x opacity-25"></i>
                </div>
            </div>
        </div>

        <!-- Banners Row (Divided by 2 columns) -->
        <div class="row mb-4 g-3">
            <!-- Quick Access Impersonate Banner -->
            <div class="<?= !empty($roadmap_live) ? 'col-lg-6' : 'col-12' ?>">
                <div class="stat-card h-100 border-0 bg-primary text-white d-flex justify-content-between align-items-center p-3 shadow-sm"
                    style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-25 rounded p-2 me-3 flex-shrink-0">
                            <i class="fas fa-user-secret fa-lg text-info"></i>
                        </div>
                        <div>
                            <h6 class="fw-900 mb-0">Impersonate & User Management</h6>
                            <p class="mb-0 text-white-50" style="font-size: 0.75rem;">Masuk sebagai user lain untuk pengujian hak akses atau
                                troubleshooting data karyawan.</p>
                        </div>
                    </div>
                    <a href="<?= site_url('pengaturan/user_account') ?>"
                        class="btn btn-info btn-sm fw-900 px-3 rounded-pill shadow-sm ms-2 flex-shrink-0">
                        BUKA <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- System Roadmap Live Updates -->
            <?php if(!empty($roadmap_live)): ?>
            <div class="col-lg-6">
                <div class="stat-card h-100 border-0 bg-success text-white d-flex align-items-center p-3 shadow-sm"
                    style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                    <div class="bg-white bg-opacity-25 rounded p-2 me-3 flex-shrink-0">
                        <i class="fas fa-rocket fa-lg text-white"></i>
                    </div>
                    <div class="flex-grow-1 overflow-hidden" style="min-width: 0;">
                        <h6 class="fw-900 mb-1">Live Updates (Roadmap)</h6>
                        <marquee behavior="scroll" direction="left" scrollamount="5" class="mb-0 text-white" style="font-size: 0.8rem; opacity: 0.9;">
                            <?php foreach($roadmap_live as $rm): ?>
                                <span class="me-4"><i class="fas fa-check-circle text-white opacity-75 me-1"></i> <strong><?= $rm->title ?? $rm->nama_fitur ?? 'Update' ?>:</strong> <?= strip_tags($rm->description ?? $rm->deskripsi ?? '') ?> <span class="opacity-50">(<?= date('d/m/Y', strtotime($rm->created_at ?? $rm->tgl_rilis ?? date('Y-m-d'))) ?>)</span></span>
                            <?php endforeach; ?>
                        </marquee>
                    </div>
                    <a href="<?= site_url('Roadmap') ?>" class="btn btn-light btn-sm fw-900 px-3 rounded-pill shadow-sm text-success ms-3 flex-shrink-0">
                        DETAIL <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- 1. Runtime & Operational Pulse -->
        <div class="row g-4 mb-5">
            <!-- Server Runtime -->
            <div class="col-md-6 col-lg-3">
                <div class="stat-card h-100 border-info border-opacity-25">
                    <div class="small fw-800 text-info text-uppercase mb-3"><i class="fas fa-microchip me-2"></i>Server
                        Runtime</div>
                    <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                        <span class="text-muted small">PHP Version</span>
                        <span class="fw-bold text-dark"><?= $server_stats['php_version'] ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                        <span class="text-muted small">Memory Limit</span>
                        <span class="fw-bold text-dark"><?= $server_stats['memory_limit'] ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                        <span class="text-muted small">Max Execution</span>
                        <span class="fw-bold text-dark"><?= $server_stats['max_execution_time'] ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Upload Max</span>
                        <span class="fw-bold text-dark"><?= $server_stats['upload_max_filesize'] ?></span>
                    </div>
                </div>
            </div>

            <!-- Operational Pulse -->
            <div class="col-md-6 col-lg-3">
                <div class="stat-card h-100 border-warning border-opacity-25">
                    <div class="small fw-800 text-warning text-uppercase mb-3"><i
                            class="fas fa-heartbeat me-2"></i>Operational Pulse</div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small">WA Gateway</span>
                            <span
                                class="badge bg-<?= $wa_status == 'ONLINE' ? 'success' : 'danger' ?>"><?= $wa_status ?></span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small">Error Log Today</span>
                            <span
                                class="fw-bold <?= $error_count > 0 ? 'text-danger animate__animated animate__flash' : 'text-success' ?>">
                                <?= $error_count ?> Issues
                            </span>
                        </div>
                        <?php if ($error_count > 0): ?>
                            <a href="<?= site_url('ErrorLog') ?>" class="btn btn-xs btn-outline-danger w-100 mt-1">Check
                                Logs</a>
                        <?php endif; ?>
                    </div>

                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">Active Users (1h)</span>
                            <span class="fw-bold text-info"><?= $active_users_now ?> Online</span>
                        </div>
                    </div>

                    <a href="<?= site_url('pengaturan/user_account') ?>"
                        class="btn btn-warning btn-sm w-100 fw-bold border-0 shadow-sm"
                        style="background: #f59e0b; color: #000;">
                        <i class="fas fa-user-secret me-2"></i> QUICK IMPERSONATE
                    </a>
                </div>
            </div>

            <!-- Database Insights -->
            <div class="col-md-12 col-lg-6">
                <div class="stat-card h-100 border-primary border-opacity-25">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="small fw-800 text-primary text-uppercase"><i class="fas fa-database me-2"></i>Heavy
                            Tables (Top 5)</div>
                        <div>
                            <a href="<?= site_url('dashboard/database_browser_index') ?>"
                                class="badge bg-primary text-decoration-none hover-dark me-2">
                                <i class="fas fa-list me-1"></i> View All
                            </a>
                            <span class="badge bg-primary">Total: <?= $db_size ?> MB</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-borderless table-hover mb-0 small">
                            <thead class="text-muted">
                                <tr>
                                    <th>Table Name</th>
                                    <th class="text-end">Rows</th>
                                    <th class="text-end">Size (MB)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($db_stats as $tbl): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= site_url('dashboard/view_table_data?table=' . $tbl->table_name) ?>"
                                                class="fw-bold text-dark text-decoration-none hover-primary">
                                                <i class="fas fa-table me-2 text-muted small"></i><?= $tbl->table_name ?>
                                            </a>
                                        </td>
                                        <td class="text-end font-monospace"><?= number_format($tbl->table_rows) ?></td>
                                        <td class="text-end font-monospace text-primary"><?= $tbl->size_mb ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Audit Activity Stream -->
        <h4 class="fw-900 text-dark mb-4 text-uppercase"><i class="fas fa-stream text-primary me-2"></i>Live Audit
            Activity Stream</h4>
        <div class="card border-0 shadow-sm mb-5 p-3" style="background: #f8fafc;">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="text-muted small text-uppercase">
                        <tr>
                            <th>Timestamp</th>
                            <th>Operator</th>
                            <th>Action</th>
                            <th>Module</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recent_logs)): ?>
                            <?php foreach ($recent_logs as $log): ?>
                                <tr>
                                    <td class="small font-monospace">
                                        <?= date('H:i:s d/m', strtotime($log->created_at)) ?>
                                    </td>
                                    <td><span class="badge bg-secondary">
                                            <?= $log->username ?>
                                        </span></td>
                                    <td>
                                        <?php if ($log->log_type == 'DELETE'): ?>
                                            <span class="badge bg-danger">DELETE</span>
                                        <?php elseif ($log->log_type == 'WRITE'): ?>
                                            <span class="badge bg-success">WRITE</span>
                                        <?php else: ?>
                                            <span class="badge bg-info">
                                                <?= $log->log_type ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><code class="small"><?= $log->controller ?>/<?= $log->method ?></code></td>
                                    <td class="small">
                                        <?= $log->log_action ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3 italic">No recent activity detected.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="text-end mt-2">
                <a href="<?= site_url('Log_audit') ?>"
                    class="btn btn-sm btn-link text-primary fw-bold text-decoration-none">
                    <i class="fas fa-external-link-alt me-1"></i> Buka Full Audit Trail
                </a>
            </div>
        </div>

        <!-- Role Security Audit Matrix -->
        <h4 class="fw-900 text-dark mb-4 text-uppercase"><i class="fas fa-shield-alt text-info me-2"></i>Security Audit
            Matrix (System Permissions)</h4>

        <div class="card border-0 shadow-sm mb-5 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0 matrix-table">
                        <thead class="bg-light">
                            <?php
                            // Hitung jumlah role yang benar-benar ditampilkan (Excluding ID 18)
                            $display_role_count = 0;
                            foreach ($roles as $r)
                                if ($r->role_id != 18)
                                    $display_role_count++;
                            $actual_colspan = $display_role_count + 2; // +1 for Menu, +1 for No
                            ?>
                            <tr>
                                <th class="text-center" style="width: 50px;">No.</th>
                                <th class="ps-4" style="width: 250px;">Menu & Controller Path</th>
                                <?php foreach ($roles as $role): ?>
                                    <?php if ($role->role_id == 18)
                                        continue; ?>
                                    <th class="text-center">
                                        <div class="small fw-800 text-dark"><?= $role->role_name ?></div>
                                        <div class="font-monospace text-muted" style="font-size: 0.6rem;">ID:
                                            <?= $role->role_id ?>
                                        </div>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $no = 1;
                            $stats = []; // Array untuk menyimpan hitungan [role_id][status]
                            foreach ($all_menus as $menu):
                                ?>
                                <?php
                                // Extract controller from link
                                $link_parts = explode('/', $menu->link);
                                $ctrl_name = strtolower($link_parts[0]);
                                if ($ctrl_name == '..')
                                    $ctrl_name = $link_parts[1] ?? '';

                                // Check for submenus
                                $has_submenus = !empty($submenus_by_parent[$menu->id]);
                                $row_id = 'menu-row-' . $menu->id;
                                $sub_row_id = 'sub-row-' . $menu->id;
                                ?>
                                <tr id="<?= $row_id ?>">
                                    <td class="text-center fw-bold text-muted small"><?= $no++ ?></td>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <?php if ($has_submenus): ?>
                                                <button
                                                    class="btn btn-sm btn-link text-muted p-0 me-2 toggle-submenu-visibility"
                                                    data-target="#<?= $sub_row_id ?>" style="width: 20px;">
                                                    <i class="fas fa-chevron-right transition-transform"></i>
                                                </button>
                                            <?php else: ?>
                                                <span style="width: 28px;"></span>
                                            <?php endif; ?>
                                            <?php 
                                            // Handle PK id vs id_menu
                                            $m_id = $menu->id ?? ($menu->id_menu ?? '');
                                            ?>
                                            <span class="menu-status-toggle pointer" data-id="<?= $m_id ?>">
                                                <?php if ($menu->status != 1): ?>
                                                    <span
                                                        class="badge bg-soft-danger text-danger border border-danger opacity-75 ms-2"
                                                        style="font-size: 0.5rem;" title="Click to Activate">INACTIVE</span>
                                                <?php else: ?>
                                                    <span class="badge bg-soft-success text-success border border-success ms-2"
                                                        style="font-size: 0.5rem;" title="Click to Deactivate">ACTIVE</span>
                                                <?php endif; ?>
                                            </span>

                                            <i class="fas <?= $menu->icon_fa ?> text-muted mx-3" style="width: 20px;"></i>
                                            <div class="<?= $menu->status != 1 ? 'opacity-50' : '' ?>">
                                                <div class="fw-800 text-dark small"><?= $menu->nama_menu ?></div>
                                                <div class="font-monospace text-muted" style="font-size: 0.65rem;">
                                                    /<?= $menu->link ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <?php foreach ($roles as $role): ?>
                                        <?php if ($role->role_id == 18)
                                            continue; ?>
                                        <td class="access-cell" data-role="<?= $role->role_id ?>" data-menu="<?= $menu->id ?>">
                                            <?php
                                            $has_access = isset($access_map[$role->role_id][$menu->id]);
                                            $is_readonly = isset($readonly_map[$role->role_id][$ctrl_name]);

                                            // LOGIKA COUNTER STATISTIK
                                            if (in_array($role->role_id, [22])) {
                                                @$stats[$role->role_id]['full']++;
                                            } elseif (!$has_access) {
                                                @$stats[$role->role_id]['hidden']++;
                                            } elseif ($is_readonly) {
                                                @$stats[$role->role_id]['readonly']++;
                                            } else {
                                                @$stats[$role->role_id]['full']++;
                                            }
                                            ?>
                                            <?php if (in_array($role->role_id, [22])): // Dev exception (Immutable Super Admin) ?>
                                                <i class="fas fa-check-circle text-success-premium access-icon no-toggle"
                                                    title="Full Access (Locked for Root)"></i>
                                            <?php elseif (!$has_access): ?>
                                                <i class="fas fa-times-circle text-danger-premium opacity-25 access-icon toggle-visibility"
                                                    title="Hidden (Click to Show)"></i>
                                            <?php elseif ($is_readonly): ?>
                                                <i class="fas fa-lock text-warning-premium access-icon toggle-readonly"
                                                    title="Read Only (Click to Unlock)"></i>
                                            <?php else: ?>
                                                <i class="fas fa-check-circle text-success-premium access-icon toggle-readonly"
                                                    title="Full Access (Click to Lock)"></i>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>

                                <?php if ($has_submenus): ?>
                                    <tr id="<?= $sub_row_id ?>" class="submenu-row d-none bg-light">
                                        <td colspan="<?= $actual_colspan ?>" class="p-0">
                                            <table class="table mb-0 table-borderless">
                                                <tbody>
                                                    <?php foreach ($submenus_by_parent[$menu->id] as $sub): ?>
                                                        <tr>
                                                            <td class="ps-5" style="width: 300px; padding-left: 3.5rem !important;">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fas fa-level-up-alt fa-rotate-90 text-muted me-3 ms-2 opacity-50"
                                                                        style="font-size: 0.8rem;"></i>
                                                                    <div>
                                                                        <div class="fw-bold text-secondary small">
                                                                            <?= $sub->nama_submenu ?>
                                                                        </div>
                                                                        <div class="font-monospace text-muted opacity-75"
                                                                            style="font-size: 0.6rem;">
                                                                            <?= $sub->link_submenu ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <?php foreach ($roles as $role): ?>
                                                                <?php if ($role->role_id == 18)
                                                                    continue; ?>
                                                                <td class="access-cell sub-access-cell text-center"
                                                                    data-role="<?= $role->role_id ?>" data-submenu="<?= $sub->id ?>">
                                                                    <?php
                                                                    $sub_access = isset($submenu_access_map[$role->role_id][$sub->id]);
                                                                    ?>
                                                                    <?php if (in_array($role->role_id, [22])): ?>
                                                                        <i class="fas fa-check text-success opacity-50 small"
                                                                            title="Included"></i>
                                                                    <?php elseif ($sub_access): ?>
                                                                        <i class="fas fa-check-square text-success access-icon toggle-submenu"
                                                                            title="Access Granted"></i>
                                                                    <?php else: ?>
                                                                        <i class="far fa-square text-muted opacity-50 access-icon toggle-submenu"
                                                                            title="Access Denied"></i>
                                                                    <?php endif; ?>
                                                                </td>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-light">
                            <!-- Summary Baris 1: Full Access -->
                            <tr class="bg-white">
                                <td colspan="2" class="ps-4 fw-800 text-success-premium small text-uppercase">Total Full
                                    Access (✓)</td>
                                <?php foreach ($roles as $role):
                                    if ($role->role_id == 18)
                                        continue; ?>
                                    <td class="text-center fw-900 text-success-premium">
                                        <?= $stats[$role->role_id]['full'] ?? 0 ?></td>
                                <?php endforeach; ?>
                            </tr>
                            <!-- Summary Baris 2: Read Only -->
                            <tr class="bg-white border-top-0">
                                <td colspan="2" class="ps-4 fw-800 text-warning-premium small text-uppercase">Total Read
                                    Only (🔒)</td>
                                <?php foreach ($roles as $role):
                                    if ($role->role_id == 18)
                                        continue; ?>
                                    <td class="text-center fw-900 text-warning-premium">
                                        <?= $stats[$role->role_id]['readonly'] ?? 0 ?></td>
                                <?php endforeach; ?>
                            </tr>
                            <!-- Summary Baris 3: Hidden -->
                            <tr class="bg-white border-top-0">
                                <td colspan="2" class="ps-4 fw-800 text-danger-premium small text-uppercase">Total
                                    Hidden (X)</td>
                                <?php foreach ($roles as $role):
                                    if ($role->role_id == 18)
                                        continue; ?>
                                    <td class="text-center fw-900 text-danger-premium opacity-50">
                                        <?= $stats[$role->role_id]['hidden'] ?? 0 ?></td>
                                <?php endforeach; ?>
                            </tr>

                            <tr class="bg-light">
                                <td colspan="<?= $actual_colspan ?>" class="p-4 text-center border-top-2"
                                    style="border-top: 2px solid #e2e8f0;">
                                    <div class="d-flex flex-column align-items-center gap-3">
                                        <div
                                            class="d-inline-flex gap-4 small fw-bold text-muted bg-white px-4 py-2 rounded-pill shadow-sm">
                                            <span><i class="fas fa-check-circle text-success-premium me-1"></i> FULL
                                                ACCESS</span>
                                            <span><i class="fas fa-lock text-warning-premium me-1"></i> READ-ONLY</span>
                                            <span><i
                                                    class="fas fa-times-circle text-danger-premium opacity-25 me-1"></i>
                                                HIDDEN</span>
                                        </div>
                                        <div class="alert alert-info border-0 shadow-sm mb-0 small"
                                            style="max-width: 600px;">
                                            <i class="fas fa-mouse-pointer me-2"></i> <strong>Tip:</strong> Klik pada
                                            ikon di atas untuk mengubah hak akses secara langsung!
                                            <br>Matriks ini mendeteksi <code>controller</code> dari link menu untuk
                                            menerapkan aturan pembatasan edit.
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-lg-12">
                <div class="stat-card border-0 bg-info bg-opacity-10">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 bg-info rounded-circle">
                            <i class="fas fa-info-circle text-white"></i>
                        </div>
                        <div>
                            <h6 class="fw-900 text-dark mb-1">Developer Tip: Manajemen Akses Cepat</h6>
                            <p class="text-muted small mb-0">Matriks di atas membantu Anda mengaudit keamanan sistem
                                secara visual. Jika ada link yang tidak aktif atau salah hak akses, Anda dapat segera
                                menyesuaikannya melalui <strong>Setting Menu</strong> untuk visibilitas (👁️) atau
                                mengedit <code>MY_Controller.php</code> untuk pembatasan edit (🔒).</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Configuration & Atomic Tools -->
        <h4 class="fw-900 text-dark mb-4 text-uppercase"><i class="fas fa-cogs text-secondary me-2"></i>System
            Configuration & Maintenance</h4>
        <div class="row g-4 mb-5">
            <div class="col-lg-8">
                <div class="stat-card h-100">
                    <h5 class="fw-900 text-dark mb-4 text-uppercase">
                        <i class="fas fa-tools text-info me-2"></i>Advanced Configuration
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="list-group list-group-flush">
                                <a href="<?= site_url('Roadmap') ?>"
                                    class="list-group-item list-group-item-action border-0 px-0 d-flex justify-content-between align-items-center bg-info bg-opacity-10 rounded mb-2">
                                    <div class="ms-2">
                                        <i class="fas fa-map-signs text-info me-3"></i>
                                        <span class="fw-bold text-info">System Roadmap</span>
                                    </div>
                                    <i class="fas fa-chevron-right text-info small me-2"></i>
                                </a>
                                <a href="<?= site_url('Support') ?>"
                                    class="list-group-item list-group-item-action border-0 px-0 d-flex justify-content-between align-items-center bg-primary bg-opacity-10 rounded mb-3">
                                    <div class="ms-2">
                                        <i class="fas fa-headset text-primary me-3"></i>
                                        <span class="fw-bold text-primary">Support Tickets</span>
                                    </div>
                                    <i class="fas fa-chevron-right text-primary small me-2"></i>
                                </a>
                                <a href="<?= site_url('Menu') ?>"
                                    class="list-group-item list-group-item-action border-0 px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-list text-primary me-3"></i>
                                        <span class="fw-bold">Setting Menu</span>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted small"></i>
                                </a>
                                <a href="<?= site_url('Backup') ?>"
                                    class="list-group-item list-group-item-action border-0 px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-database text-warning me-3"></i>
                                        <span class="fw-bold">Setting Database</span>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted small"></i>
                                </a>
                                <a href="<?= site_url('Pengaturan/rekening') ?>"
                                    class="list-group-item list-group-item-action border-0 px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-file-invoice-dollar text-success me-3"></i>
                                        <span class="fw-bold">Setting Keuangan</span>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted small"></i>
                                </a>
                                <a href="<?= site_url('log_audit/changelog') ?>"
                                    class="list-group-item list-group-item-action border-0 px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-file-invoice-dollar text-success me-3"></i>
                                        <span class="fw-bold">Log Perform Application</span>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted small"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="list-group list-group-flush border-start ps-md-3">
                                <a href="<?= site_url('Warehouse/categories') ?>"
                                    class="list-group-item list-group-item-action border-0 px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-tags text-info me-3"></i>
                                        <span class="fw-bold">Setting Kategori</span>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted small"></i>
                                </a>
                                <a href="<?= site_url('Warehouse/subcategories') ?>"
                                    class="list-group-item list-group-item-action border-0 px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-tag text-info me-3 opacity-75"></i>
                                        <span class="fw-bold">Sub Kategori</span>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted small"></i>
                                </a>
                                <a href="<?= site_url('administrative/transaksi') ?>"
                                    class="list-group-item list-group-item-action border-0 px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-exclamation-triangle text-danger me-3"></i>
                                        <span class="fw-bold">Koreksi Transaksi</span>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted small"></i>
                                </a>
                                <a href="<?= site_url('log_audit/') ?>"
                                    class="list-group-item list-group-item-action border-0 px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-exclamation-triangle text-danger me-3"></i>
                                        <span class="fw-bold">Log Activity </span>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted small"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <!-- Maintenance "Atomic" Buttons -->
                <div class="stat-card h-100 bg-slate-100 border-0">
                    <h5 class="fw-900 text-dark mb-4 text-uppercase">Atomic Actions</h5>

                    <a href="<?= base_url('dashboard/force_activate_all_menus') ?>"
                        class="btn btn-success w-100 mb-4 text-start shadow-sm hover-lift action-confirm"
                        data-message="Ini akan memaksa SELURUH menu utama dan submenu menjadi AKTIF secara global. Lanjutkan?">
                        <i class="fas fa-power-off me-2"></i> <strong>FORCE ACTIVATE ALL MENUS</strong>
                    </a>

                    <a href="<?= site_url('Warehouse/ajax_sync_stock') ?>"
                        class="btn btn-outline-primary w-100 mb-3 text-start"
                        onclick="return confirm('Proses sinkronisasi stok akan memakan waktu. Lanjutkan?')">
                        <i class="fas fa-sync-alt me-2"></i> Sync Stock Engine
                    </a>

                    <a href="<?= site_url('Backup/cleanup') ?>"
                        class="btn btn-outline-warning w-100 mb-3 text-start text-warning-dark"
                        onclick="return confirm('Optimize database tables?')">
                        <i class="fas fa-broom me-2"></i> Optimize Database
                    </a>

                    <a href="<?= site_url('dashboard/prune_system_logs') ?>"
                        class="btn btn-outline-warning w-100 mb-3 text-start text-warning-dark"
                        onclick="return confirm('Hapus log user & aktivitas sistem lebih lama dari 30 hari?')">
                        <i class="fas fa-user-clock me-2"></i> Prune System Logs
                    </a>

                    <hr class="my-3">

                    <h6 class="fw-800 text-slate-500 mb-3 text-uppercase small">Warehouse Management</h6>

                    <a href="<?= site_url('dashboard/init_warehouse_stock') ?>"
                        class="btn btn-outline-success w-100 mb-3 text-start"
                        onclick="return confirm('Inisialisasi stok akan membuat entri baru untuk semua material yang belum terdaftar di gudang utama. Lanjutkan?')">
                        <i class="fas fa-boxes me-2"></i> Init Stock SKU (Master)
                    </a>

                    <a href="<?= site_url('dashboard/wipe_warehouse_data') ?>"
                        class="btn btn-outline-danger w-100 mb-3 text-start"
                        onclick="return confirm('PERINGATAN KRITIKAL!!!\n\nTindakan ini akan MENGHAPUS SELURUH data stok, riwayat log, pembelian dan pengambilan barang di gudang secara permanen.\n\nSangat disarankan hanya dilakukan pada sistem baru yang belum live.\n\nApakah Anda YAKIN?')">
                        <i class="fas fa-trash-alt me-2"></i> Wipe Warehouse Data
                    </a>

                    <hr class="my-3">

                    <a href="<?= site_url('auth/logout') ?>" class="btn btn-danger w-100 text-start">
                        <i class="fas fa-sign-out-alt me-2"></i> Secure Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        // 0. Toggle Global Menu Status (Active/Inactive)
        $('.menu-status-toggle').on('click', function () {
            const btn = $(this);
            const id = btn.data('id');
            const icon = btn.find('.badge');

            icon.html('<i class="fas fa-spinner fa-spin"></i>');

            $.post('<?= base_url("dashboard/ajax_toggle_menu_status") ?>', { menu_id: id }, function (res) {
                const data = JSON.parse(res);
                if (data.status === 'active') {
                    btn.html('<span class="badge bg-soft-success text-success border border-success ms-2" style="font-size: 0.5rem;">ACTIVE</span>');
                    btn.closest('tr').find('.opacity-50').removeClass('opacity-50');
                } else {
                    btn.html('<span class="badge bg-soft-danger text-danger border border-danger opacity-75 ms-2" style="font-size: 0.5rem;">INACTIVE</span>');
                    btn.closest('tr').find('.fw-800').parent().addClass('opacity-50');
                }

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Menu Status Updated',
                    showConfirmButton: false,
                    timer: 2000
                });
            });
        });

        // 1. Toggle Visibility (Switch between Hidden and Full Access)
        $('.matrix-table').on('click', '.toggle-visibility', function () {
            const icon = $(this);
            const cell = icon.closest('.access-cell');
            const role_id = cell.data('role');
            const menu_id = cell.data('menu');

            icon.addClass('loading fa-spinner fa-spin').removeClass('fa-times-circle');

            $.ajax({
                url: '<?= site_url('dashboard/ajax_toggle_visibility') ?>',
                type: 'POST',
                data: { role_id, menu_id },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'added') {
                        icon.removeClass('loading fa-spinner fa-spin opacity-25 fa-times-circle text-danger-premium')
                            .addClass('fa-check-circle text-success-premium toggle-readonly')
                            .removeClass('toggle-visibility')
                            .attr('title', 'Full Access (Click to Lock)');
                    } else {
                        icon.removeClass('loading fa-spinner fa-spin text-success-premium fa-check-circle text-warning-premium fa-lock')
                            .addClass('fa-times-circle text-danger-premium opacity-25 toggle-visibility')
                            .removeClass('toggle-readonly')
                            .attr('title', 'Hidden (Click to Show)');
                    }
                }
            });
        });

        // 2. Toggle Read-Only (Switch between Full Access and Lock)
        $('.matrix-table').on('contextmenu click', '.toggle-readonly', function (e) {
            if (e.type === 'contextmenu') {
                e.preventDefault();
                const icon = $(this);
                const cell = icon.closest('.access-cell');
                const role_id = cell.data('role');
                const menu_id = cell.data('menu');

                icon.addClass('loading fa-spinner fa-spin');

                $.ajax({
                    url: '<?= site_url('dashboard/ajax_toggle_visibility') ?>',
                    type: 'POST',
                    data: { role_id, menu_id },
                    success: function () {
                        icon.removeClass('loading fa-spinner fa-spin text-success-premium fa-check-circle text-warning-premium fa-lock')
                            .addClass('fa-times-circle text-danger-premium opacity-25 toggle-visibility')
                            .removeClass('toggle-readonly')
                            .attr('title', 'Hidden (Click to Show)');
                    }
                });
                return;
            }

            const icon = $(this);
            const cell = icon.closest('.access-cell');
            const role_id = cell.data('role');
            const menu_id = cell.data('menu');

            icon.addClass('loading fa-spinner fa-spin');

            $.ajax({
                url: '<?= site_url('dashboard/ajax_toggle_readonly') ?>',
                type: 'POST',
                data: { role_id, menu_id },
                dataType: 'json',
                success: function (response) {
                    icon.removeClass('loading fa-spinner fa-spin');
                    if (response.status === 'locked') {
                        icon.removeClass('fa-check-circle text-success-premium').addClass('fa-lock text-warning-premium').attr('title', 'Read Only (Click to Unlock)');
                    } else {
                        icon.removeClass('fa-lock text-warning-premium').addClass('fa-check-circle text-success-premium').attr('title', 'Full Access (Click to Lock)');
                    }
                }
            });
        });

        // Toggle Submenu Visibility (Accordion)
        $('.toggle-submenu-visibility').on('click', function (e) {
            e.stopPropagation(); // prevent row click if needed
            const target = $(this).data('target');
            const icon = $(this).find('i');

            $(target).toggleClass('d-none');

            if ($(target).hasClass('d-none')) {
                icon.removeClass('fa-rotate-90');
            } else {
                icon.addClass('fa-rotate-90');
            }
        });

        // Toggle Submenu Access (Checkbox)
        $(document).on('click', '.access-icon.toggle-submenu', function () {
            if ($(this).hasClass('loading')) return;

            const icon = $(this);
            const cell = icon.closest('.access-cell');
            const role_id = cell.data('role');
            const submenu_id = cell.data('submenu');

            icon.removeClass('fa-check-square fa-square text-success text-muted opacity-50')
                .addClass('fa-spinner fa-spin text-primary loading');

            $.ajax({
                url: '<?= site_url('dashboard/ajax_toggle_submenu_access') ?>',
                type: 'POST',
                data: { role_id, submenu_id },
                dataType: 'json',
                success: function (res) {
                    icon.removeClass('fa-spinner fa-spin text-primary loading');
                    if (res.status === 'added') {
                        icon.addClass('fas fa-check-square text-success');
                    } else {
                        icon.addClass('far fa-square text-muted opacity-50');
                    }
                },
                error: function () {
                    icon.removeClass('fa-spinner fa-spin text-primary loading')
                        .addClass('fa-exclamation-triangle text-danger');
                }
            });
        });

    });
</script>