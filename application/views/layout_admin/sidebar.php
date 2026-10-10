<?php
// Pastikan state Query Builder bersih sebelum render sidebar dimulai
if (isset($this->db)) {
    $this->db->reset_query();
    $this->db->flush_cache();
}

// Helper Function to render menu items
// MOVED TO TOP to prevent 'undefined function' error
if (!function_exists('renderMenuItem')) {
    function renderMenuItem($mn, $context)
    {
        // Safety Check
        if (!isset($context->db) || !isset($mn))
            return;

        // RESET QUERY STATE
        // Penting: Mencegah sisa-sisa 'group_by' atau 'having' dari query di controller (Dashboard) 
        // mengganggu query menu/submenu di sini.
        $context->db->reset_query();
        $context->db->flush_cache();

        // 1. Get User Role & Developer Status
        $user_role_id = $context->session->userdata('level');
        // NOTE: avoid hardcoded username checks; rely on role id for developer privilege
        $is_developer = ($user_role_id == 22);

        // Optimize Super Admin Check (Gunakan data dari Controller)
        static $is_super_admin = null;
        if ($is_super_admin === null) {
            $is_super_admin = (isset($context->data['is_super_admin']) && $context->data['is_super_admin']);
            // Tambahkan bypass jika role ID = 1 (Administrator)
            if ($user_role_id == 1)
                $is_super_admin = true;
        }

        // 2. Akses Menu sudah diverifikasi di Controller (MY_Controller)
        // Jadi kita tidak perlu query database ulang di sini guna menjaga performa
        // dan menghindari konflik state database.

        // 3. Query Submenus
        // [DETEKSI KOLOM DINAMIS]
        $col_role_sub = $context->db->field_exists('role_id', 'tbl_submenu_access') ? 'role_id' : ($context->db->field_exists('level_id', 'tbl_submenu_access') ? 'level_id' : 'role_id');
        $col_parent = $context->db->field_exists('id_menu', 'submenu') ? 'id_menu' : 'menu_id';

        // RESET STATE SEBELUM QUERY
        $context->db->reset_query();

        $context->db->reset_query();
        $context->db->select('submenu.*');
        $context->db->from('submenu');

        if (!$is_developer && !$is_super_admin) {
            $user_id = $context->session->userdata('id');
            $context->db->group_start();
            $context->db->where("submenu.id IN (SELECT submenu_id FROM tbl_submenu_access WHERE $col_role_sub = " . (int) $user_role_id . ")", NULL, FALSE);
            if ($context->db->table_exists('tbl_user_submenu_access')) {
                $context->db->or_where("submenu.id IN (SELECT submenu_id FROM tbl_user_submenu_access WHERE user_id = " . (int) $user_id . ")", NULL, FALSE);
            }
            $context->db->group_end();
        }
        
        $context->db->where('submenu.status', '1');
        $context->db->where("submenu.$col_parent", $mn->id);
        $context->db->order_by('submenu.urutan', 'ASC');
        $submenus = $context->db->get()->result();

        // Bersihkan state setelah query submenu selesai
        $context->db->reset_query();

        // 4. Render HTML
        $uri1 = $context->uri->segment(1);
        $uri2 = $context->uri->segment(2);

        if (!empty($submenus)) {
            // HAS SUBMENU
            $is_active = false;
            foreach ($submenus as $sub) {
                if ($uri2 == $sub->link_submenu && $uri1 == $mn->link) {
                    $is_active = true;
                    break;
                }
            }

            // Classes
            $has_treeview = 'has-treeview';
            $is_menu_open = $is_active ? 'menu-open' : '';
            ?>
            <li class="nav-item <?= $has_treeview ?> <?= $is_menu_open ?>">
                <a href="#" class="nav-link <?= $is_active ? 'active' : '' ?>">
                    <i
                        class="nav-icon bi <?= htmlspecialchars($mn->icon_fa ? $mn->icon_fa : 'bi-circle', ENT_QUOTES, 'UTF-8') ?>"></i>
                    <p>
                        <?= htmlspecialchars($mn->alias ? $mn->alias : $mn->nama_menu, ENT_QUOTES, 'UTF-8') ?>
                        <i class="right bi bi-chevron-right nav-arrow"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview" style="<?= $is_active ? 'display:block;' : 'display:none;' ?>">
                    <?php foreach ($submenus as $sub):
                        $sub_active = ($uri2 == $sub->link_submenu && $uri1 == $mn->link);
                        $sub_href = htmlspecialchars(base_url($mn->link . '/' . $sub->link_submenu), ENT_QUOTES, 'UTF-8');
                        ?>
                        <li class="nav-item">
                            <a href="<?= $sub_href ?>" class="nav-link <?= $sub_active ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    <?= htmlspecialchars($sub->nama_submenu, ENT_QUOTES, 'UTF-8') ?>
                                    <?php if ($sub->link_submenu == 'spk_monitor'): ?>
                                        <span class="badge badge-danger right">NEW</span>
                                    <?php endif; ?>
                                </p>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <?php
        } else {
            // SINGLE MENU / CONTAINER / HEADER CHECK

            // Header Logic: Jika link kosong/# dan tidak punya submenu
            if (empty($mn->link) || $mn->link === '#' || $mn->link === '//') {
                ?>
                <li class="nav-header small fw-bolder text-uppercase mt-4 mb-1 px-4"
                    style="color: rgba(255,255,255,0.4); letter-spacing: 0.5px; font-size: 0.75rem;">
                    <?= htmlspecialchars($mn->alias ? $mn->alias : $mn->nama_menu, ENT_QUOTES, 'UTF-8') ?>
                </li>
                <?php
                return;
            }

            // Clean Link (Handle double slashes like //daftar_piutang)
            $clean_link = ltrim($mn->link, '/');
            $menu_active = ($context->uri->segment(1) == $clean_link);
            ?>
            <li class="nav-item">
                <a href="<?= htmlspecialchars(base_url($clean_link), ENT_QUOTES, 'UTF-8') ?>"
                    class="nav-link <?= $menu_active ? 'active' : '' ?>">
                    <i
                        class="nav-icon bi <?= htmlspecialchars($mn->icon_fa ? $mn->icon_fa : 'bi-circle', ENT_QUOTES, 'UTF-8') ?>"></i>
                    <p>
                        <?= htmlspecialchars($mn->alias ? $mn->alias : $mn->nama_menu, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </a>
            </li>
            <?php
        }
    }
}
?>

<?php
// HYBRID NAVIGATION CHECK
if (isset($layout_mode) && $layout_mode == 'top_nav') {
    return;
}
?>

<!--Sidebar Styles-->
<link rel="stylesheet" href="<?= base_url('assets/css/tgm-sidebar.css') ?>?v=<?= time() ?>">
<style>
    /* Override Bootstrap CSS Variables inside sidebar to prevent page-level inheritance */
    .app-sidebar,
    .app-sidebar * {
        --bs-nav-link-font-size: 14px;
        --bs-nav-link-padding-x: 16px;
        --bs-nav-link-padding-y: 10px;
        --bs-body-font-size: 14px;
        --bs-body-font-family: "Inter", "Source Sans 3", system-ui, -apple-system, sans-serif;
    }

    /* Odoo-style Modern Sidebar Customization */
    .app-sidebar {
        background-color: #111827 !important; /* Deep modern dark (Tailwind Gray-900) */
        border-right: none !important;
    }
    
    .sidebar-brand {
        border-bottom: 1px solid rgba(255,255,255,0.05) !important;
    }

    .sidebar-menu .nav-item .nav-link {
        border-radius: 8px; /* Rounded pill style */
        margin: 2px 12px;
        color: #9ca3af; /* Gray 400 */
        transition: all 0.2s ease-in-out;
    }

    .sidebar-menu .nav-item .nav-link:hover {
        background-color: rgba(255,255,255,0.05);
        color: #f3f4f6;
    }

    /* Active State (Odoo style: subtle highlight with primary text or background) */
    .sidebar-menu .nav-item .nav-link.active {
        background-color: #3b82f6 !important; /* Blue 500 */
        color: #ffffff !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        font-weight: 600;
    }

    /* Icons */
    .sidebar-menu .nav-item .nav-link i.nav-icon {
        font-size: 1.1rem;
        margin-right: 8px;
        transition: transform 0.2s;
    }
    .sidebar-menu .nav-item .nav-link:hover i.nav-icon {
        transform: scale(1.1);
    }
</style>

<!--begin::Sidebar-->
<aside class="app-sidebar sidebar-expand-lg shadow-lg" data-bs-theme="dark">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
        <a href="<?= base_url('dashboard') ?>"
            class="brand-link d-flex align-items-center justify-content-center w-100">
            <img src="<?= base_url('assets/images/app_icon.png') ?>" alt="Logo"
                class="brand-image opacity-80 shadow-sm me-2" style="height: 30px;">
            <span class="brand-text fw-bold"><?= get_app_name() ?> <a href="<?= base_url('log_audit/changelog') ?>" class="text-decoration-none"><small class="opacity-50 fw-normal ms-1">v<?= isset($app_version) ? $app_version : '5.0' ?></small></a></span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <!-- Pinned / Main System Links -->
                <li class="nav-header small text-uppercase fw-bold text-muted px-3 mb-2">Main Navigation</li>

                <?php
                $uri1 = $this->uri->segment(1);
                $is_dashboard = ($uri1 == 'dashboard' || $uri1 == '');
                ?>

                <li class="nav-item">
                    <a href="<?= base_url('dashboard') ?>" class="nav-link <?= $is_dashboard ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-speedometer2"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('mobile') ?>"
                        class="nav-link <?= ($this->uri->segment(1) == 'mobile') ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-phone-fill text-primary"></i>
                        <p>Mobile <span class="badge badge-primary right">APP</span></p>
                    </a>
                </li>

                <!-- <li class="nav-item">
                    <a href="<?= base_url('print_queue/dashboard') ?>"
                        class="nav-link <?= ($this->uri->segment(1) == 'print_queue') ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-printer-fill text-info"></i>
                        <p>Antrian Cetak SPK <span class="badge badge-danger right">NEW</span></p>
                    </a>
                </li> -->

                <!-- Dynamic Menus from Database -->
                <?php
                if (empty($menu)) {
                    $user_level = $this->session->userdata('level');
                    $user_id = $this->session->userdata('id');
                    $this->db->select('menu.*');
                    $this->db->from('menu');
                    $this->db->group_start();
                    $this->db->where("menu.id IN (SELECT menu_id FROM tbl_menu_access WHERE role_id = " . (int) $user_level . ")", NULL, FALSE);
                    if ($this->db->table_exists('tbl_user_menu_access')) {
                        $this->db->or_where("menu.id IN (SELECT menu_id FROM tbl_user_menu_access WHERE user_id = " . (int) $user_id . ")", NULL, FALSE);
                    }
                    $this->db->group_end();
                    $this->db->where('menu.status', '1');
                    $this->db->group_by('menu.id');
                    $this->db->order_by('menu.urutan', 'ASC');
                    $menu = $this->db->get()->result();
                }

                if (!empty($menu)):
                    $exclude_links = ['dashboard'];
                    foreach ($menu as $mn):
                        if (in_array(strtolower($mn->link), $exclude_links)) {
                            continue;
                        }
                        renderMenuItem($mn, $this);
                    endforeach;
                endif;
                ?>

                <!-- Special Access Manual Items (Optional) -->
                <!-- Menu ini sudah dipindahkan ke Database agar bisa diatur via Manajemen Menu -->


                <!-- Developer / Super Admin Tools -->
                <?php
                if (isset($this->data['is_developer']) && $this->data['is_developer']):
                    // Additional dev tools could go here
                endif;
                ?>



                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        // Sidebar Toggler Logic
                        const sidebarKey = 'tgm_sidebar_state';
                        const body = document.body;
                        const isMobile = () => window.innerWidth < 992;

                        // Check stored state (only for desktop)
                        if (!isMobile() && localStorage.getItem(sidebarKey) === 'collapsed') {
                            body.classList.add('sidebar-collapse');
                        }

                        // Handle sidebar toggle click (from top header)
                        const toggler = document.querySelector('[data-widget="pushmenu"]');
                        if (toggler) {
                            toggler.addEventListener('click', function (e) {
                                e.preventDefault();
                                e.stopPropagation();

                                if (isMobile()) {
                                    // Mobile: Toggle sidebar-open class on body
                                    body.classList.toggle('sidebar-open');
                                } else {
                                    // Desktop: Toggle sidebar-collapse
                                    body.classList.toggle('sidebar-collapse');
                                    // Save state
                                    if (body.classList.contains('sidebar-collapse')) {
                                        localStorage.setItem(sidebarKey, 'collapsed');
                                    } else {
                                        localStorage.removeItem(sidebarKey);
                                    }
                                }
                            });
                        }

                        // Close sidebar when clicking on backdrop (mobile only)
                        document.addEventListener('click', function (e) {
                            if (isMobile() && body.classList.contains('sidebar-open')) {
                                const sidebar = document.querySelector('.app-sidebar');
                                const toggleBtn = document.querySelector('[data-widget="pushmenu"]');

                                // Check if click is outside sidebar and not on toggle button
                                if (sidebar && !sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                                    body.classList.remove('sidebar-open');
                                }
                            }
                        });

                        // Close sidebar when clicking on a nav link (mobile only)
                        const navLinks = document.querySelectorAll('.sidebar-menu .nav-link');
                        navLinks.forEach(link => {
                            link.addEventListener('click', function (e) {
                                // Don't close if it's a parent menu item (has arrow)
                                const hasSubmenu = this.parentElement.classList.contains('has-treeview');

                                if (isMobile() && !hasSubmenu) {
                                    // Small delay to allow navigation to begin
                                    setTimeout(() => {
                                        body.classList.remove('sidebar-open');
                                    }, 150);
                                }
                            });
                        });

                        // Handle window resize - clean up states
                        let resizeTimer;
                        window.addEventListener('resize', function () {
                            clearTimeout(resizeTimer);
                            resizeTimer = setTimeout(function () {
                                if (!isMobile()) {
                                    // Remove mobile-specific class when resizing to desktop
                                    body.classList.remove('sidebar-open');
                                } else {
                                    // Remove desktop-specific class when resizing to mobile
                                    body.classList.remove('sidebar-collapse');
                                }
                            }, 250);
                        });

                        // ============================================
                        // CUSTOM TREEVIEW POLYFILL (FIX FOR ADMINLTE + BS5)
                        // ============================================
                        // Matikan handler AdminLTE yang mungkin konflik
                        $(document).off('click', '[data-widget="treeview"] .nav-link');

                        // Custom Handler untuk Sidebar Treeview
                        $('.nav-item.has-treeview > .nav-link').on('click', function (e) {
                            e.preventDefault();

                            var $link = $(this);
                            var $parent = $link.parent(); // li.nav-item
                            var $menu = $parent.find('> .nav-treeview'); // ul.nav-treeview

                            // Toggle Logic
                            if ($parent.hasClass('menu-open')) {
                                // Tutup
                                $menu.slideUp(300, function () {
                                    $parent.removeClass('menu-open');
                                });
                                // Putar panah balik
                                $link.find('.nav-arrow').css('transform', 'rotate(0deg)');
                            } else {
                                // Buka
                                // (Optional: Tutup menu lain yang terbuka - Accordion Effect)
                                var $container = $parent.closest('.sidebar-menu');
                                $container.find('.nav-item.menu-open').not($parent).each(function () {
                                    // Hanya tutup yang sejajar levelnya
                                    $(this).removeClass('menu-open');
                                    $(this).find('> .nav-treeview').slideUp(300);
                                    $(this).find('.nav-arrow').css('transform', 'rotate(0deg)');
                                });

                                $menu.slideDown(300, function () {
                                    $parent.addClass('menu-open');
                                });
                                // Putar panah
                                $link.find('.nav-arrow').css('transform', 'rotate(90deg)');
                            }
                        });

                        // Set Arrow state on load
                        $('.nav-item.has-treeview.menu-open .nav-arrow').css('transform', 'rotate(90deg)');
                    });


                    function confirmLogout() {
                        Swal.fire({
                            title: 'Konfirmasi Logout',
                            text: 'Apakah Anda yakin ingin keluar dari sistem?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Ya, Logout!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '<?= base_url('auth/logout') ?>';
                            }
                        });
                    }
                </script>

            </ul>
            <!--end::Sidebar Menu-->
        </nav>
    </div>
    <!--end::Sidebar Wrapper-->
</aside>

<!-- Begin Content Wrapper -->
<div class="content-wrapper">