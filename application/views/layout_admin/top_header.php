<?php
// Ambil data user dari session/cache controller
$user_role_id = (int) $this->session->userdata('level');
$is_developer = ($this->session->userdata('username') === 'nizarsuryadi');

// Fetch dynamic menus for Top Nav Mode (Only if layout_mode is top_nav)
$parent_menus = [];
$all_submenus = [];
$allowed_menu_ids = [];

if (isset($layout_mode) && $layout_mode == 'top_nav') {
    // Dynamic fetch from menu and submenu for Top Nav layout
    // Use the menu array from MY_Controller to respect all permissions (including tbl_user_menu_access)
    $parent_menus = isset($menu) ? $menu : $this->db->query("SELECT * FROM menu WHERE status = '1' ORDER BY urutan ASC")->result();
    
    $col_role_sub = $this->db->field_exists('role_id', 'tbl_submenu_access') ? 'role_id' : 'level_id';
    $user_id = $this->session->userdata('id');
    
    $this->db->select('submenu.*');
    $this->db->from('submenu');
    if (!$is_developer && $user_role_id != 1) {
        $this->db->group_start();
        $this->db->where("submenu.id IN (SELECT submenu_id FROM tbl_submenu_access WHERE $col_role_sub = " . (int) $user_role_id . ")", NULL, FALSE);
        if ($this->db->table_exists('tbl_user_submenu_access')) {
            $this->db->or_where("submenu.id IN (SELECT submenu_id FROM tbl_user_submenu_access WHERE user_id = " . (int) $user_id . ")", NULL, FALSE);
        }
        $this->db->group_end();
    }
    $this->db->where('submenu.status', 1);
    $this->db->order_by('submenu.urutan', 'ASC');
    $sub_results = $this->db->get()->result();
    
    foreach ($sub_results as $sub) {
        $all_submenus[$sub->id_menu][] = $sub;
    }

    // We don't need allowed_menu_ids because $parent_menus is already filtered by MY_Controller
}

// Fetch Notifications
$this->db->where('user_id', $this->session->userdata('id'))->where('is_read', 0)->order_by('created_at', 'DESC')->limit(10);
$my_notifs = $this->db->get('tbl_notifikasi')->result();
$notif_count = count($my_notifs);
?>

<!-- Main Header -->
<div class="tgm-top-header-shell">
<nav
    class="main-header navbar tgm-top-header <?= (isset($layout_mode) && $layout_mode == 'top_nav') ? 'navbar-expand-lg navbar-dark bg-dark top-header-nav sticky-top' : 'navbar-expand navbar-white navbar-light bg-white border-bottom shadow-sm' ?>">
    <div class="container-fluid">
        <!-- Brand & Toggle Section -->
        <div class="d-flex align-items-center">
            <!-- Sidebar Toggle (Only if Sidebar Mode) -->
            <?php if (!isset($layout_mode) || $layout_mode == 'sidebar'): ?>
                <ul class="navbar-nav me-2">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                    </li>
                </ul>
            <?php endif; ?>

            <!-- Brand Logo -->
            <a href="<?= base_url('dashboard') ?>" class="navbar-brand d-flex align-items-center <?= (isset($layout_mode) && $layout_mode == 'top_nav') ? '' : 'd-lg-none' ?>">
                <img src="<?= base_url('assets/images/app_icon.png') ?>" alt="Logo" class="me-2"
                    style="height: 24px; width: auto;">
                <span
                    class="brand-text fw-bold <?= (isset($layout_mode) && $layout_mode == 'top_nav') ? 'text-white' : 'text-dark' ?>">
                    <?= get_app_name() ?> <a href="<?= base_url('log_audit/changelog') ?>" class="text-decoration-none"><small class="opacity-50 fw-normal ms-1">v<?= isset($app_version) ? $app_version : '5.0' ?></small></a>
                </span>
            </a>

            <!-- Mobile Toggle for Top Nav -->
            <?php if (isset($layout_mode) && $layout_mode == 'top_nav'): ?>
                <button class="navbar-toggler border-0 ms-2" type="button" data-bs-toggle="collapse"
                    data-bs-target="#topNavbar">
                    <span class="bi bi-list fs-1 text-white"></span>
                </button>
            <?php endif; ?>
        </div>

        <!-- Collapsible Content -->
        <div class="<?= (isset($layout_mode) && $layout_mode == 'top_nav') ? 'collapse navbar-collapse' : 'd-flex flex-fill' ?>"
            id="topNavbar">
            <!-- Left Side Links -->
            <ul
                class="navbar-nav me-auto align-items-center <?= (isset($layout_mode) && $layout_mode == 'top_nav') ? 'main-module-nav' : '' ?>">
                <!-- TOP NAV DYNAMIC MENU -->
                <?php if (isset($layout_mode) && $layout_mode == 'top_nav'): ?>
                    <?php if (!empty($parent_menus)): 
                        // User-requested custom grouping to save horizontal space
                        $custom_mapping = [
                            'pembayaran kasir' => 'Point of Sale',
                            'pembayaran - kasir' => 'Point of Sale',
                            'transaksi manual' => 'Point of Sale',
                            'transaksi belum di proses' => 'Point of Sale',
                            'order / spk' => 'Point of Sale',
                            'pos' => 'Point of Sale',
                            'point of sale' => 'Point of Sale',

                            'database konsumen' => 'Konsumen',
                            'piutang konsumen' => 'Konsumen',
                            'piutang' => 'Konsumen',
                            'konsumen' => 'Konsumen',

                            'mutasi cash' => 'Keuangan',
                            'mutasi transfer' => 'Keuangan',
                            'mutasi edc' => 'Keuangan',
                            'mutasi ewallet' => 'Keuangan',
                            'pengeluaran' => 'Keuangan',
                            'mutasi bahan' => 'Keuangan',
                            'mutasi' => 'Keuangan',
                            'keuangan' => 'Keuangan',

                            'absensi karyawan' => 'Absensi',
                            'lembur' => 'Absensi',
                            'overtime / lembur' => 'Absensi',
                            'absensi' => 'Absensi',
                        ];

                        $rendered_items = [];
                        $custom_groups = [];

                        // 1. Process all menus and submenus
                        foreach ($parent_menus as $mn) {
                            $submenus = isset($all_submenus[$mn->id]) ? $all_submenus[$mn->id] : [];
                            $is_header = (empty($mn->link) || $mn->link === '#' || $mn->link === '//') && empty($submenus);
                            if ($is_header) continue;

                            $mn_label = !empty($mn->alias) ? $mn->alias : $mn->nama_menu;
                            $mn_icon = !empty($mn->icon_fa) ? $mn->icon_fa : (!empty($mn->icon_menu) ? $mn->icon_menu : 'bi-circle');
                            $parent_key = strtolower(trim($mn_label));

                            if (empty($submenus)) {
                                // Standalone parent menu
                                if (isset($custom_mapping[$parent_key])) {
                                    $group_name = $custom_mapping[$parent_key];
                                    $custom_groups[$group_name][] = [
                                        'label' => $mn_label,
                                        'link' => base_url($mn->link),
                                        'icon' => $mn_icon
                                    ];
                                } else {
                                    $rendered_items[] = [
                                        'type' => 'link',
                                        'label' => $mn_label,
                                        'link' => base_url($mn->link),
                                        'icon' => $mn_icon
                                    ];
                                }
                            } else {
                                // Parent menu with submenus
                                $unmapped_children = [];
                                
                                foreach ($submenus as $sub) {
                                    $sub_label = $sub->nama_submenu;
                                    $sub_key = strtolower(trim($sub_label));
                                    
                                    // If submenu matches, use it. Else if parent matches, group everything there.
                                    $target_group = isset($custom_mapping[$sub_key]) ? $custom_mapping[$sub_key] : (isset($custom_mapping[$parent_key]) ? $custom_mapping[$parent_key] : null);
                                    
                                    if ($target_group) {
                                        $custom_groups[$target_group][] = [
                                            'label' => $sub_label,
                                            'link' => base_url($mn->link . '/' . $sub->link_submenu),
                                            'icon' => 'bi-circle'
                                        ];
                                    } else {
                                        $unmapped_children[] = [
                                            'label' => $sub_label,
                                            'link' => base_url($mn->link . '/' . $sub->link_submenu),
                                            'icon' => 'bi-circle'
                                        ];
                                    }
                                }

                                if (!empty($unmapped_children)) {
                                    $rendered_items[] = [
                                        'type' => 'dropdown',
                                        'id' => $mn->id,
                                        'label' => $mn_label,
                                        'icon' => $mn_icon,
                                        'children' => $unmapped_children
                                    ];
                                }
                            }
                        }

                        // 2. Render Custom Groups First
                        $group_icons = [
                            'Point of Sale' => 'bi-cart',
                            'Konsumen' => 'bi-people',
                            'Keuangan' => 'bi-wallet2',
                            'Absensi' => 'bi-clock-history'
                        ];

                        foreach ($custom_groups as $g_name => $g_items) {
                            $g_icon = isset($group_icons[$g_name]) ? $group_icons[$g_name] : 'bi-folder';
                            $g_id = 'group-' . md5($g_name);
                            ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-white" href="#" id="<?= $g_id ?>" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi <?= $g_icon ?> me-1"></i> <?= $g_name ?>
                                </a>
                                <ul class="dropdown-menu border-0 shadow-lg mt-0 bg-white" aria-labelledby="<?= $g_id ?>" style="background-color: #ffffff !important;">
                                    <?php foreach ($g_items as $item): ?>
                                        <li>
                                            <a class="dropdown-item p-2 px-3 bg-white text-dark dropdown-hover-light" href="<?= $item['link'] ?>">
                                                <i class="bi <?= $item['icon'] ?> text-secondary me-2" style="font-size: 0.7rem;"></i>
                                                <?= $item['label'] ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                            <?php
                        }

                        // 3. Render Remaining Items
                        foreach ($rendered_items as $item) {
                            if ($item['type'] === 'dropdown'): ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle text-white" href="#" id="dropdown-<?= $item['id'] ?>" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi <?= $item['icon'] ?> me-1"></i> <?= $item['label'] ?>
                                    </a>
                                    <ul class="dropdown-menu border-0 shadow-lg mt-0 bg-white" aria-labelledby="dropdown-<?= $item['id'] ?>" style="background-color: #ffffff !important;">
                                        <?php foreach ($item['children'] as $child): ?>
                                            <li>
                                                <a class="dropdown-item p-2 px-3 bg-white text-dark dropdown-hover-light" href="<?= $child['link'] ?>">
                                                    <i class="bi <?= $child['icon'] ?> text-secondary me-2" style="font-size: 0.7rem;"></i>
                                                    <?= $child['label'] ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            <?php else: ?>
                                <li class="nav-item">
                                    <a class="nav-link text-white" href="<?= $item['link'] ?>">
                                        <i class="bi <?= $item['icon'] ?> me-1"></i> <?= $item['label'] ?>
                                    </a>
                                </li>
                            <?php endif; 
                        } ?>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- SIDEBAR MODE TITLE -->
                    <li class="nav-item d-none d-sm-inline-block">
                        <span class="nav-link fw-bold text-dark ms-2 text-nowrap">
                            <?= isset($title) ? $title : 'Dashboard' ?>
                        </span>
                    </li>
                <?php endif; ?>

                <!-- IMPERSONATION ALERT (Shared) -->
                <?php if ($this->session->userdata('is_impersonating')): ?>
                    <li class="nav-item ms-lg-3">
                        <div class="d-flex align-items-center bg-warning text-dark px-2 rounded-pill shadow-sm animate-pulse"
                            style="height: 26px; font-size: 0.7rem;">
                            <i class="bi bi-incognito me-1"></i>
                            <span class="fw-bold d-none d-md-inline">MENYAMAR:
                                <?= strtoupper($this->session->userdata('username')) ?></span>
                            <a href="<?= base_url('pengaturan/stop_impersonating') ?>"
                                class="btn btn-xs btn-dark rounded-circle ms-1 d-flex align-items-center justify-content-center"
                                style="width: 18px; height: 18px; border: none;">
                                <i class="bi bi-x" style="font-size: 12px;"></i>
                            </a>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ms-auto align-items-center">

                <!-- Tracking SPK Quick Link -->
                <li class="nav-item me-2 d-flex align-items-center">
                    <a href="javascript:void(0)" onclick="openGlobalSearchModal()"
                        class="btn btn-outline-info btn-sm rounded-pill px-3 shadow-none border d-flex align-items-center position-relative"
                        title="Tracking Lokasi SPK">
                        <i class="bi bi-search me-1"></i>
                        <span class="fw-bold small d-none d-xl-inline text-nowrap">Tracking SPK</span>
                    </a>
                </li>

                <!-- Chat Team Quick Link -->
                <li class="nav-item me-2 d-flex align-items-center">
                    <a href="javascript:void(0)" onclick="toggleChat()"
                        class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-none border d-flex align-items-center position-relative"
                        title="Obrolan Tim">
                        <i class="bi bi-chat-dots-fill me-1"></i>
                        <span class="fw-bold small d-none d-xl-inline text-nowrap">Chatting Tim</span>
                        <span id="chat-header-badge"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm d-none"
                            style="font-size: 0.65rem;">
                            NEW
                        </span>
                    </a>
                </li>

                <!-- Support Ticket Quick Link -->
                <li class="nav-item me-2 d-flex align-items-center">
                    <?php
                    $ticket_count = 0;
                    if ($is_developer) {
                        $ticket_count = $this->db->where_in('status', ['OPEN', 'PENDING'])->count_all_results('tbl_support_tickets');
                    } else {
                        // Cek apakah user ini Super Admin via DB
                        $role_check = $this->db->get_where('tbl_divisi', ['divisi_id' => $user_role_id])->row();
                        if ($role_check && $role_check->is_super_admin == 1) {
                            $ticket_count = $this->db->where_in('status', ['OPEN', 'PENDING'])->count_all_results('tbl_support_tickets');
                        }
                    }
                    ?>
                    <a href="<?= base_url('support') ?>"
                        class="btn btn-outline-warning btn-sm rounded-pill px-3 shadow-none border d-flex align-items-center position-relative"
                        title="Lapor BUG / Ajukan Fitur">
                        <i class="bi bi-headset me-1"></i>
                        <span class="fw-bold small d-none d-xl-inline text-nowrap">Bantuan</span>
                        <?php if ($ticket_count > 0): ?>
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm"
                                style="font-size: 0.65rem;">
                                <?= $ticket_count ?>
                                <span class="visually-hidden">unread reports</span>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>

                <!-- Notification Bell -->
                <?php if ($notif_count > 0): ?>
                <li class="nav-item dropdown me-2 d-flex align-items-center">
                    <a class="nav-link btn btn-light btn-sm rounded-circle shadow-none border d-flex align-items-center justify-content-center position-relative" data-bs-toggle="dropdown" href="#" style="width: 32px; height: 32px;">
                        <i class="bi bi-bell-fill text-dark"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm" style="font-size: 0.6rem;">
                            <?= $notif_count ?>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end border-0 shadow-lg mt-2 bg-white" style="background-color: #ffffff !important;">
                        <span class="dropdown-item dropdown-header bg-light fw-bold text-dark border-bottom"><?= $notif_count ?> Notifikasi Baru</span>
                        <?php foreach ($my_notifs as $ntf): ?>
                            <div class="dropdown-divider m-0" style="border-color: #f0f0f0;"></div>
                            <a href="<?= base_url('notifikasi/baca/' . $ntf->id) ?>" class="dropdown-item py-2 px-3 bg-white text-dark dropdown-hover-light">
                                <div class="fw-bold small text-dark"><?= htmlspecialchars($ntf->judul) ?></div>
                                <span class="text-secondary small" style="white-space: normal;"><?= htmlspecialchars($ntf->pesan) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </li>
                <?php endif; ?>

                <style>
                .dropdown-hover-light:hover {
                    background-color: #f8f9fa !important;
                }
                </style>

                <!-- Date Widget -->


                <!-- Branch Switcher -->
                <?php
                $can_switch = ($is_developer || (isset($is_super_admin) && $is_super_admin) || in_array($user_role_id, [1, 8, 17, 21]));
                if ($can_switch && !empty($available_branches) && count($available_branches) > 1):
                    ?>
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link btn btn-light btn-sm rounded-pill px-3 shadow-none border dropdown-toggle d-flex align-items-center"
                            data-bs-toggle="dropdown" data-toggle="dropdown" href="#">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                            <span class="fw-bold text-dark small d-inline-block text-truncate" style="max-width: 150px; vertical-align: middle;">
                                <?php
                                foreach ($available_branches as $ab) {
                                    if ($ab->id_perusahaan == $current_perusahaan_id)
                                        echo $ab->nama_perusahaan;
                                }
                                ?>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-2 bg-white" style="background-color: #ffffff !important;">
                            <span class="dropdown-item-text small text-muted text-uppercase fw-bold">Pilih Lokasi</span>
                            <div class="dropdown-divider m-0" style="border-color: #f0f0f0;"></div>
                            <?php foreach ($available_branches as $branch): ?>
                                <a href="<?= base_url('SwitchBranch?id=' . $branch->id_perusahaan) ?>"
                                    class="dropdown-item d-flex justify-content-between align-items-center p-2 px-3 bg-white text-dark dropdown-hover-light">
                                    <?= $branch->nama_perusahaan ?>
                                    <?php if ($branch->id_perusahaan == $current_perusahaan_id): ?>
                                        <i class="bi bi-check-circle-fill text-success ms-3"></i>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </li>
                <?php endif; ?>

                <!-- User Dropdown (Modern Style with Old Data Logic) -->
                <li class="nav-item dropdown user-menu">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" data-toggle="dropdown">
                        <img src="<?= base_url('assets/images/karyawan/' . ($foto['foto'] ?? 'default.png')) ?>"
                            class="user-image img-circle shadow-sm" alt="User Image">
                        <span
                            class="d-none d-md-inline fw-semibold <?= (isset($layout_mode) && $layout_mode == 'top_nav') ? 'text-white' : '' ?>">
                            <?= ucfirst($this->session->userdata('username')) ?>
                        </span>
                    </a>
                    <ul
                        class="dropdown-menu dropdown-menu-lg dropdown-menu-end border-0 shadow-lg mt-2 bg-white overflow-hidden" style="background-color: #ffffff !important;">
                        <li class="user-header bg-primary text-white p-4">
                            <img src="<?= base_url('assets/images/karyawan/' . ($foto['foto'] ?? 'default.png')) ?>"
                                class="img-circle elevation-2 border-2 border-white mb-2"
                                style="width: 80px; height: 80px; object-fit: cover;">
                            <p class="mb-0">
                                <span class="fw-bold"><?= ucfirst($this->session->userdata('username')) ?></span>
                                <small
                                    class="d-block opacity-75 mt-1"><?= isset($hakakses) ? strtoupper($hakakses) : 'USER' ?></small>
                            </p>
                        </li>
                        <li class="user-body p-0 bg-white">
                            <a href="<?= base_url('mobile/profile') ?>" class="dropdown-item py-3 border-bottom text-dark bg-white dropdown-hover-light">
                                <i class="bi bi-person me-2 text-primary"></i> My Profile
                            </a>
                            <a href="<?= base_url('konfigurasi') ?>" class="dropdown-item py-3 text-dark bg-white dropdown-hover-light">
                                <i class="bi bi-gear me-2 text-info"></i> Account Settings
                            </a>
                        </li>
                        <li class="user-footer bg-light p-2">
                            <a href="<?= base_url('auth/logout') ?>" class="btn btn-danger btn-block w-100"
                                onclick="return confirm('Yakin logout?')">
                                <i class="bi bi-power me-1"></i> Sign Out
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
</div>

<style>
    /* 1. FAIL-SAFE: Pure CSS Hover Dropdown (Desktop Only) */
    @media (min-width: 992px) {
        .tgm-top-header.top-header-nav .nav-item.dropdown:hover>.dropdown-menu {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            margin-top: 0 !important;
            top: 100% !important;
            position: absolute !important;
        }

        /* Khusus untuk menu di sebelah kanan agar tidak terpotong */
        .tgm-top-header.top-header-nav .nav-item.dropdown.user-menu:hover>.dropdown-menu,
        .tgm-top-header.top-header-nav .nav-item.dropdown:hover>.dropdown-menu-end {
            right: 0 !important;
            left: auto !important;
        }

        /* Default alignment untuk menu kiri */
        .tgm-top-header.top-header-nav .nav-item.dropdown:hover>.dropdown-menu:not(.dropdown-menu-end) {
            left: 0 !important;
            right: auto !important;
        }

        /* Mencegah kursor 'lepas' saat berpindah ke menu */
        .tgm-top-header.top-header-nav .dropdown-menu::before {
            content: "";
            position: absolute;
            top: -10px;
            left: 0;
            width: 100%;
            height: 10px;
            background: transparent;
        }
    }

    /* 2. Global Layout & Visibility Fixes */
    .tgm-top-header-shell,
    .tgm-top-header,
    .tgm-top-header .navbar-collapse,
    .tgm-top-header .main-module-nav,
    .tgm-top-header .navbar-nav,
    .tgm-top-header .nav-item.dropdown,
    .tgm-top-header .main-header {
        overflow: visible !important;
    }

    /* 3. Dropdown Menu Styling */
    .tgm-top-header .dropdown-menu {
        z-index: 9999 !important;
        background-color: #ffffff !important;
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
        min-width: 220px;
        padding: 0.5rem 0 !important;
        border-radius: 8px !important;
    }

    .tgm-top-header .dropdown-item {
        color: #333 !important;
        padding: 0.6rem 1.25rem !important;
        font-size: 0.85rem !important;
        border-bottom: 1px solid rgba(0, 0, 0, 0.03);
        transition: background-color 0.2s ease;
    }

    .tgm-top-header .dropdown-item:last-child {
        border-bottom: none;
    }

    .tgm-top-header .dropdown-item:hover {
        background-color: #f8f9fa !important;
        color: #0d6efd !important;
    }

    /* Dropdown Manual Show (For JS fallback) */
    .tgm-top-header .dropdown-menu.show {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof jQuery !== 'undefined') {
            const $ = jQuery;

            // Toggle Click (Sangat berguna untuk Mobile/Tablet)
            $(document).on('click', '.tgm-top-header.top-header-nav .dropdown-toggle', function (e) {
                if (window.innerWidth >= 992) return; // Biarkan Hover yang bekerja di Desktop

                e.preventDefault();
                e.stopPropagation();

                const $parent = $(this).closest('.nav-item.dropdown');
                const $target = $parent.find('.dropdown-menu').first();
                const isOpen = $target.hasClass('show');

                $('.tgm-top-header.top-header-nav .dropdown-menu').removeClass('show');
                if (!isOpen) $target.addClass('show');
            });

            $(document).on('click', function (e) {
                if (!$(e.target).closest('.tgm-top-header.top-header-nav .dropdown').length) {
                    $('.tgm-top-header.top-header-nav .dropdown-menu').removeClass('show');
                }
            });
        }

        // Efek Scroll Header
        const nav = document.querySelector('.tgm-top-header.top-header-nav');
        if (nav) {
            window.addEventListener('scroll', function () {
                if (window.scrollY > 30) {
                    nav.style.backgroundColor = "rgba(15, 17, 21, 0.98)";
                    nav.classList.add('shadow-lg');
                } else {
                    nav.style.backgroundColor = "";
                    nav.classList.remove('shadow-lg');
                }
            });
        }
    });
</script>