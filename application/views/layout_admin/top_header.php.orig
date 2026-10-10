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
    $parent_menus = $this->db->query("SELECT * FROM menu WHERE status = '1' ORDER BY urutan ASC")->result();
    $sub_results = $this->db->query("SELECT * FROM submenu WHERE status = '1' ORDER BY urutan ASC")->result();
    foreach ($sub_results as $sub) {
        $all_submenus[$sub->id_menu][] = $sub;
    }

    if (!$is_developer) {
        $access = $this->db->query("SELECT menu_id FROM tbl_menu_access WHERE role_id = ?", [$user_role_id])->result();
        foreach ($access as $a)
            $allowed_menu_ids[] = (int) $a->menu_id;
    }
}
?>

<!-- Main Header -->
<nav
    class="main-header navbar <?= (isset($layout_mode) && $layout_mode == 'top_nav') ? 'navbar-expand-lg navbar-dark bg-dark top-header-nav sticky-top' : 'navbar-expand navbar-white navbar-light bg-white border-bottom shadow-sm' ?>">
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
            <a href="<?= base_url('dashboard') ?>" class="navbar-brand d-flex align-items-center">
                <img src="<?= base_url('assets/images/app_icon.png') ?>" alt="Logo" class="me-2"
                    style="height: 24px; width: auto;">
                <span
                    class="brand-text fw-bold <?= (isset($layout_mode) && $layout_mode == 'top_nav') ? 'text-white' : 'text-dark' ?>">
                    TGM CORE <a href="<?= base_url('log_audit/changelog') ?>" class="text-decoration-none"><small class="opacity-50 fw-normal ms-1">v<?= isset($app_version) ? $app_version : '5.0' ?></small></a>
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
                    <?php if (!empty($parent_menus)): ?>
                        <?php foreach ($parent_menus as $mn):
                            $is_dashboard = (stripos($mn->link, 'dashboard') !== false || $mn->id == 1);
                            $has_access = $is_developer || $is_dashboard || in_array($mn->id, $allowed_menu_ids);

                            if (!$has_access)
                                continue;

                            $submenus = isset($all_submenus[$mn->id]) ? $all_submenus[$mn->id] : [];
                            $is_active = ($this->uri->segment(1) == $mn->link);
                            $menu_icon = !empty($mn->icon_fa) ? $mn->icon_fa : (!empty($mn->icon_menu) ? $mn->icon_menu : 'bi-circle');
                            $menu_name = !empty($mn->alias) ? $mn->alias : $mn->nama_menu;

                            if (!empty($submenus)): ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle <?= $is_active ? 'active text-warning' : '' ?>" href="#"
                                        id="dropdown-<?= $mn->id ?>" role="button" data-bs-toggle="dropdown" data-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi <?= $menu_icon ?> me-1"></i> <?= $menu_name ?>
                                    </a>
                                    <ul class="dropdown-menu border-0 shadow-lg mt-0" aria-labelledby="dropdown-<?= $mn->id ?>">
                                        <?php foreach ($submenus as $sub):
                                            $sub_active = ($this->uri->segment(1) == $mn->link && $this->uri->segment(2) == $sub->link_submenu); ?>
                                            <li>
                                                <a class="dropdown-item p-2 px-3 <?= $sub_active ? 'active' : '' ?>"
                                                    href="<?= base_url($mn->link . '/' . $sub->link_submenu) ?>">
                                                    <i class="bi bi-chevron-right opacity-50 me-2" style="font-size: 0.7rem;"></i>
                                                    <?= $sub->nama_submenu ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            <?php else: ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $is_active ? 'active text-warning' : '' ?>"
                                        href="<?= base_url($mn->link) ?>">
                                        <i class="bi <?= $menu_icon ?> me-1"></i> <?= $menu_name ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- SIDEBAR MODE TITLE -->
                    <li class="nav-item d-none d-sm-inline-block">
                        <span class="nav-link fw-bold text-dark ms-2">
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
                        <span class="fw-bold small d-none d-lg-inline">Tracking SPK</span>
                    </a>
                </li>

                <!-- Chat Team Quick Link -->
                <li class="nav-item me-2 d-flex align-items-center">
                    <a href="javascript:void(0)" onclick="toggleChat()"
                        class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-none border d-flex align-items-center position-relative"
                        title="Obrolan Tim">
                        <i class="bi bi-chat-dots-fill me-1"></i>
                        <span class="fw-bold small d-none d-lg-inline">Chatting Tim</span>
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
                        <span class="fw-bold small d-none d-lg-inline">Bantuan</span>
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
                            <span class="fw-bold text-dark small">
                                <?php
                                foreach ($available_branches as $ab) {
                                    if ($ab->id_perusahaan == $current_perusahaan_id)
                                        echo $ab->nama_perusahaan;
                                }
                                ?>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-2">
                            <span class="dropdown-item-text small text-muted text-uppercase fw-bold">Pilih Lokasi</span>
                            <div class="dropdown-divider"></div>
                            <?php foreach ($available_branches as $branch): ?>
                                <a href="<?= base_url('SwitchBranch?id=' . $branch->id_perusahaan) ?>"
                                    class="dropdown-item d-flex justify-content-between align-items-center p-2 px-3">
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
                        class="dropdown-menu dropdown-menu-lg dropdown-menu-end border-0 shadow-lg mt-2 overflow-hidden">
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
                        <li class="user-body p-0">
                            <a href="<?= base_url('mobile/profile') ?>" class="dropdown-item py-3 border-bottom">
                                <i class="bi bi-person me-2 text-primary"></i> My Profile
                            </a>
                            <a href="<?= base_url('konfigurasi') ?>" class="dropdown-item py-3">
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

<style>
    /* 1. FAIL-SAFE: Pure CSS Hover Dropdown (Desktop Only) */
    @media (min-width: 992px) {
        .top-header-nav .nav-item.dropdown:hover>.dropdown-menu {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            margin-top: 0 !important;
            top: 100% !important;
            position: absolute !important;
        }

        /* Khusus untuk menu di sebelah kanan agar tidak terpotong */
        .top-header-nav .nav-item.dropdown.user-menu:hover>.dropdown-menu,
        .top-header-nav .nav-item.dropdown:hover>.dropdown-menu-end {
            right: 0 !important;
            left: auto !important;
        }

        /* Default alignment untuk menu kiri */
        .top-header-nav .nav-item.dropdown:hover>.dropdown-menu:not(.dropdown-menu-end) {
            left: 0 !important;
            right: auto !important;
        }

        /* Mencegah kursor 'lepas' saat berpindah ke menu */
        .top-header-nav .dropdown-menu::before {
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
    .top-header-nav,
    .navbar-collapse,
    .main-module-nav,
    .navbar-nav,
    .nav-item.dropdown,
    .main-header {
        overflow: visible !important;
    }

    /* 3. Dropdown Menu Styling */
    .top-header-nav .dropdown-menu {
        z-index: 9999 !important;
        background-color: #1a1a1a !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5) !important;
        min-width: 200px;
        padding: 0.5rem 0 !important;
    }

    .top-header-nav .dropdown-item {
        color: rgba(255, 255, 255, 0.8) !important;
        padding: 0.8rem 1.25rem !important;
        font-size: 0.85rem !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .top-header-nav .dropdown-item:hover {
        background-color: #3b82f6 !important;
        color: #fff !important;
    }

    /* Dropdown Manual Show (For JS fallback) */
    .top-header-nav .dropdown-menu.show {
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
            $(document).on('click', '.top-header-nav .dropdown-toggle', function (e) {
                if (window.innerWidth >= 992) return; // Biarkan Hover yang bekerja di Desktop

                e.preventDefault();
                e.stopPropagation();

                const $parent = $(this).closest('.nav-item.dropdown');
                const $target = $parent.find('.dropdown-menu').first();
                const isOpen = $target.hasClass('show');

                $('.top-header-nav .dropdown-menu').removeClass('show');
                if (!isOpen) $target.addClass('show');
            });

            $(document).on('click', function (e) {
                if (!$(e.target).closest('.top-header-nav .dropdown').length) {
                    $('.top-header-nav .dropdown-menu').removeClass('show');
                }
            });
        }

        // Efek Scroll Header
        const nav = document.querySelector('.top-header-nav');
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