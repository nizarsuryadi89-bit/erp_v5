<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Controller - Enterprise Grade Core Controller
 * 
 * STANDARDISASI ERP MANUFAKTUR:
 * 1. Centralized Authentication & Authorization (RBAC)
 * 2. High Performance Session Caching (Mengurangi Query DB Berulang)
 * 3. Strict Read-Only Access Control (Based on HTTP Verbs)
 * 4. Modular Menu Management
 * 
 * @property M_auth $M_auth
 * @property Produk_model $Produk_model
 * @property CI_User_agent $agent
 * @property CI_DB_query_builder $db
 * @property CI_DB_utility $dbutil
 * @property CI_DB_forge $dbforge
 * @property CI_Input $input
 * @property CI_Session $session
 * @property CI_Router $router
 * @property CI_URI $uri
 * @property CI_Output $output
 * @property CI_Upload $upload
 * @property CI_Form_validation $form_validation
 * @property CI_Pagination $pagination
 * @property CI_Image_lib $image_lib
 * @property CI_Email $email
 * @property CI_Zip $zip
 * @property CI_Config $config
 * @property CI_Security $security
 * @property M_master $M_master
 * @property Absensi_model $Absensi_model
 * @property Penggajian_model $Penggajian_model
 * @property M_gudang $M_gudang
 * @property M_laporan $M_laporan
 * @property M_hutang $M_hutang
 * @property M_transaksi $M_transaksi
 * @property M_pembayaran $M_pembayaran
 * @property Produksi_model $Produksi_model
 * @property Antrian_model $Antrian_model
 * @property Payroll_model $Payroll_model
 * @property M_kiosk $M_kiosk
 * @property M_penjualan $M_penjualan
 * @property M_profile $M_profile
 * @property Chat_model $Chat_model
 * @property KasirShift_model $KasirShift_model
 * @property User_activity_model $User_activity_model
 * @property Konsumen_model $Konsumen_model
 * @property Search_model $Search_model
 * @property PHPExcel $excel
 * @property CIQRCode $ciqrcode
 * @property object $rajaongkir
 * @property Penjualan_manual_model $penjualan
 * @property M_gudang $m_gudang
 * @property Warehouse_model $warehouse
 * @property Firebase_cloud_messaging $firebase_cloud_messaging
 * @property Fungsi $fungsi
 */
class MY_Controller extends CI_Controller
{
    protected $data = [];       // Data global untuk View
    protected $conf = [];       // Konfigurasi Legacy (Backward Compat)
    protected $user_info = [];  // Cache User Session

    // WHITELIST: Controller/Method yang bisa diakses TANPA Login
    // Format: 'controller' => ['method1', 'method2'] atau '*' untuk semua
    protected $public_routes = [
        'auth' => '*',
        'api' => '*',
        'api_portal' => '*',
        'api_mobile' => '*',
        'api_tracking' => '*',
        'api_auth' => '*',
        'health' => '*',
        'searchai' => '*',
        'rajaongkir' => '*',
        'kiosk' => '*',
        'antrian' => ['index', 'ambil_nomor_interface', 'ambil_nomor_ajax', 'display', 'get_data_display', 'print_karcis', 'mobile'],
        'absensi' => ['absen_masuk', 'absen_pulang', 'izin_absen', 'scan'],
        'chat' => '*',
        'cron' => '*',
        'website' => '*',
        'lapor' => '*',
        'pengaturan' => ['stop_impersonating', 'aset_detail', 'konsumen_search_ajax'],
        'cekpesanan' => ['spk', 'cetak_invoice'],
        'katalog' => '*',
        'api_katalog' => '*',
        'tracking' => '*',
    ];

    public function __construct()
    {
        parent::__construct();

        // 1. Inisialisasi Environment Global
        date_default_timezone_set("Asia/Jakarta");
        // In development/offline mode, allow any method of Cekpesanan
        if (!defined('ENVIRONMENT') || ENVIRONMENT !== 'production') {
            $this->public_routes['cekpesanan'] = '*';
        }
        // Enforce HTTPS (if not already) – only in production
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'production') {
            $is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
            $is_localhost = isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);
            if (!$is_https && !$is_localhost) {
                $https_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                redirect($https_url);
            }
        }



        // 2. Security Guard (Auth & Access)
        $this->_check_auth();

        // 3. Load Core Data (Hanya jika Login)
        if ($this->session->userdata('logged_in')) {
            $this->_init_user_session();

            // Enforce access control and readonly restrictions
            $this->_enforce_access_control();
            $this->_enforce_readonly();

            // 4. AUTOMATIC AUDIT LOG (Hanya untuk aksi TULIS/POST)
            if ($this->input->method() === 'post') {
                $this->_auto_audit_log();
            }
        }
    }

    /**
     * ---------------------------------------------------------
     * CORE SECURITY & AUTHENTICATION
     * ---------------------------------------------------------
     */

    private function _check_auth()
    {
        $class = strtolower($this->router->class);
        $method = strtolower($this->router->method);

        // A. Cek Public Routes (Whitelist)
        if (isset($this->public_routes[$class])) {
            if ($this->public_routes[$class] === '*' || in_array($method, $this->public_routes[$class])) {
                return; // ByPass Login
            }
        }

        // B. Cek Session Login
        if ($this->session->userdata('logged_in') !== TRUE) {
            // Jika request via AJAX, return JSON 401 (Unauthorized) bukan redirect HTML
            if ($this->input->is_ajax_request()) {
                header('HTTP/1.1 401 Unauthorized');
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Sesi berakhir. Silakan login kembali.']);
                exit;
            }
            // Jika request biasa, redirect ke Login
            redirect('auth');
        }
    }

    /**
     * Mengamankan User Read-Only dari aksi Tulis/Edit/Hapus
     * Menggunakan pendekatan HTTP VERB (POST) yang lebih aman & standar daripada cek nama fungsi.
     */
    private function _enforce_readonly()
    {
        // Skip jika user bukan ReadOnly atau jika Developer
        if (empty($this->data['is_readonly']) || !empty($this->data['is_developer'])) {
            return;
        }

        $current_controller = strtolower($this->router->class);
        $current_method = strtolower($this->router->method);

        // Bypass Public Routes (Karena Read-Only tidak boleh memblokir API/Auth/Impersonate)
        if (isset($this->public_routes[$current_controller])) {
            $routes = $this->public_routes[$current_controller];
            if ($routes === '*' || (is_array($routes) && in_array($current_method, $routes))) {
                return;
            }
        }

        // Cek apakah request bersifat WRITE (POST, PUT, DELETE)
        // Standar Enterprise: Semua aksi simpan/update HARUS via POST.
        $request_method = strtoupper($this->input->method());

        // Double Protection: Cek keyword sensitif di URL (untuk legacy controller yang mungkin pakai GET untuk delete)
        $current_method = strtolower($this->router->method);

        // EXCEPTION: Aksi yang diperbolehkan meski Read-Only (Quick Add Master Data)
        $write_exceptions = ['supplier_add', 'supplier_generate_code', 'get_supplier_options'];
        if (in_array($current_method, $write_exceptions)) {
            return;
        }

        $write_keywords = ['save', 'update', 'delete', 'insert', 'aksi', 'proses', 'upload', 'hapus', 'edit'];

        $is_write_intent = false;
        foreach ($write_keywords as $kw) {
            if (strpos($current_method, $kw) !== false) {
                $is_write_intent = true;
                break;
            }
        }

        if ($request_method === 'POST' || $is_write_intent) {
            // Blokir Action
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                header('HTTP/1.1 403 Forbidden');
                echo json_encode(['status' => 'error', 'pesan' => 'AKSES DITOLAK: Mode Read-Only tidak mengizinkan perubahan data.']);
                exit;
            } else {
                $this->session->set_flashdata('error', 'AKSES DITOLAK: Mode Read-Only tidak mengizinkan perubahan data.');
                redirect($this->router->class); // Kembali ke index controller tsb
                exit;
            }
        }
    }

    /**
     * Enforce Access Control (RBAC)
     * Menggantikan logika akses yang lama dengan yang lebih terstruktur.
     */
    private function _enforce_access_control()
    {
        $current_controller = strtolower($this->router->class);
        $current_method = strtolower($this->router->method);

        // 1. Bypass Rules
        if (!empty($this->data['is_developer']))
            return;
        if ($current_controller == 'notifikasi' || $current_controller == 'dashboard' || $current_controller == 'support' || $current_controller == 'antrian' || $current_controller == 'switchbranch' || $current_controller == 'produksi_request' || $current_controller == 'tracking' || ($current_controller == 'transaksi' && $current_method == 'order_titipan') || ($current_controller == 'transaksi' && $current_method == 'preview_invoice') || ($current_controller == 'wip' && $current_method == 'view_spk_invoice') || ($current_controller == 'wip' && $current_method == 'produksi') || ($current_controller == 'wip' && $current_method == 'konsumsi_bahan') || ($current_controller == 'wip' && $current_method == 'ajax_detail_konsumsi') || ($current_controller == 'wip' && $current_method == 'cetak_konsumsi_bahan') || ($current_controller == 'wip' && $current_method == 'log_stok_gudang') || ($current_controller == 'wip' && $current_method == 'ajax_global_search') || ($current_controller == 'wip' && $current_method == 'get_spk_details_ajax') || ($current_controller == 'wip' && $current_method == 'print_tag') || ($current_controller == 'wip' && $current_method == 'print_tag_ids') || ($current_controller == 'konsumen' && $current_method == 'actionaddajax'))
            return;

        // Legacy username prefix bypass removed; access now based on menu permissions

        // Bypass Public Routes
        if (isset($this->public_routes[$current_controller])) {
            $routes = $this->public_routes[$current_controller];
            if ($routes === '*' || (is_array($routes) && in_array($current_method, $routes))) {
                return;
            }
        }

        $role_id = $this->session->userdata('level');
        if (!$role_id) {
            redirect('auth');
            return;
        }

        // Bypass for E-Commerce Division (Divisi ID = 9) to access Marketplace
        $cache = $this->session->userdata('app_session_cache');
        if ($current_controller == 'marketplace') {
            $is_ecommerce = (isset($cache['karyawan_foto']['divisi_id']) && $cache['karyawan_foto']['divisi_id'] == 9);
            if ($is_ecommerce) {
                return;
            }
        }

        // 2. Owner / Super Admin Bypass (Direct from Cache)
        $cache = $this->session->userdata('app_session_cache');
        if ($role_id != 1) {
            if (isset($cache['level_info']['is_super_admin']) && $cache['level_info']['is_super_admin'] == 1) {
                return;
            }
        }

        // 3 & 4. Permission Check via helper
        $cache = $this->session->userdata('app_session_cache');
        if ($this->_has_menu_access($current_controller, $current_method, $cache)) {
            return;
        }

        // Jika sampai sini, berarti tidak ada izin
        $username = isset($this->data['username']) ? $this->data['username'] : 'Unknown';
        log_message('error', "Unauthorized Access: User $username -> $current_controller/$current_method (Role: $role_id)");

        $this->session->set_flashdata('error', 'Akses Ditolak: Anda tidak memiliki izin mengakses modul ini.');
        redirect('dashboard');
        exit;
    }

    /**
     * ---------------------------------------------------------
     * DATA LOADING & CACHING (PERFORMANCE ENGINE)
     * ---------------------------------------------------------
     */

    private function _init_user_session()
    {
        // PERFORMANCE BOOST: 
        // Cek apakah data user & perusahaan sudah ada di session cache?
        // Jika belum (misal baru update code ini), paksa refresh cache.
        $needs_refresh = !$this->session->has_userdata('app_session_cache');

        // Jika impersonate dan cache belum ditandai milik impersonate
        if ($this->session->userdata('is_impersonating') && $this->session->userdata('app_session_cache')['is_impersonated_cache'] !== true) {
            $needs_refresh = true;
        }

        if ($needs_refresh) {
            $this->_refresh_session_cache();
        }

        $cache = $this->session->userdata('app_session_cache');

        // Set Global Property untuk Controller
        // Fallback untuk antisipasi jika cache korup/kosong
        $user_data = $cache['user_info'] ?? [];
        $level_info = $cache['level_info'] ?? [];
        $perusahaan = $cache['perusahaan'] ?? [];

        // Pre-calculate status before creating $this->data
        $is_dev_status = $this->_is_super_admin($user_data, $level_info);

        // SECURITY FIX: Jika sedang impersonate (Force Login), matikan bypass developer 
        // agar kita bisa melihat UI persis seperti user tersebut (Menu tersaring).
        if ($this->session->userdata('is_impersonating')) {
            $is_dev_status = false;
        }

        // LOGIC HYBRID NAVIGATION
        $active_role_id = $level_info['divisi_id'] ?? $this->session->userdata('level');

        // PENTING: Jika menyamar, paksa pakai level dari session asli
        if ($this->session->userdata('is_impersonating')) {
            $active_role_id = $this->session->userdata('level');
        }

        // [CACHED MENU] Mengambil menu dari session cache untuk optimasi performa ekstrim
        // Jika terjadi perubahan hak akses, user harus Logout dan Login kembali
        $menu_data = $cache['menu_data'] ?? [];

        // HYBRID NAVIGATION (ROLE BASED)
        // Default for everyone: TOP HEADER (Top Nav)
        $layout_mode = 'top_nav';
        $body_class = 'hold-transition layout-top-nav';

        // Check who gets the SIDEBAR (Privileged Users)
        $role_name = strtolower($level_info['divisi_nama'] ?? '');
        $force_sidebar = false;

        // 1. Developer & Super Admin
        if ($is_dev_status || ($level_info['is_super_admin'] ?? 0) == 1) {
            $force_sidebar = true;
        }

        // 2. Role Name Keywords (Owner, Admin, HR)
        $role_id_raw = (int) ($this->session->userdata('level') ?? 0);
        if (strpos($role_name, 'owner') !== false || in_array($role_id_raw, [8, 17]))
            $force_sidebar = true;
        if (strpos($role_name, 'admin') !== false || $role_id_raw == 1)
            $force_sidebar = true;
        if (strpos($role_name, 'hr') !== false || in_array($role_id_raw, [16, 21]))
            $force_sidebar = true;

        // Apply Sidebar Layout if forced
        if ($force_sidebar) {
            $layout_mode = 'sidebar';
            $body_class = 'hold-transition sidebar-mini layout-fixed';
        }

        // DEBUGGING / TESTING: Allow forcing layout via URL ?layout=top_nav
        if ($this->input->get('layout') == 'top_nav') {
            $layout_mode = 'top_nav';
            $body_class = 'hold-transition layout-top-nav';
        } elseif ($this->input->get('layout') == 'sidebar') {
            $layout_mode = 'sidebar';
            $body_class = 'hold-transition sidebar-mini layout-fixed';
        }

        // Setup variable $this->data yang dipakai di View
        $this->data = [
            'controller' => strtolower($this->router->class),
            'is_developer' => $is_dev_status,
            'is_super_admin' => $is_dev_status || ($level_info['is_super_admin'] ?? 0) == 1, // Gabungkan dev status dan super admin murni
            'function' => $this->router->method,
            'perusahaan' => $perusahaan['nama_perusahaan'] ?? 'TGM ERP',
            'logo' => $perusahaan['logo'] ?? 'default.png',
            'username' => $user_data['username'] ?? 'User',
            'hakakses' => $level_info['divisi_nama'] ?? 'Guest',

            'menu' => $menu_data,
            'layout_mode' => $layout_mode, // Pass layout decision to views
            'body_class' => $body_class,    // Pass body class to views

            'is_readonly' => $this->_check_is_readonly_context(),
            'current_perusahaan_id' => $perusahaan['id_perusahaan'] ?? 0,
            'available_branches' => $cache['available_branches'] ?? [],
            'foto' => $cache['karyawan_foto'] ?? [],
            'app_version' => defined('APP_VERSION') ? APP_VERSION : '5.0',

            // BACKWARD COMPATIBILITY
            'level' => $user_data,
        ];
        // Banyak controller lama mengakses langsung $this->conf['perusahaan']
        $this->conf = [
            'perusahaan' => $perusahaan,
            'hakakses' => $level_info,
            'userid' => $user_data,
            'available_branches' => $cache['available_branches'] ?? []
        ];
    }

    /**
     * Re-build session cache dari Database.
     * Dipanggil satu kali saat Login atau Switch Branch.
     */
    protected function _refresh_session_cache()
    {
        $user_id = $this->session->userdata('id');
        $level_id = (int) $this->session->userdata('level');
        $perusahaan_id = $this->session->userdata('perusahaan_id');

        if (!$user_id)
            return;

        // 1. Fetch Core Data
        $this->db->where('user_id', $user_id);
        $user = $this->db->get('users')->row_array();

        $this->db->where('id_perusahaan', $perusahaan_id);
        $perusahaan = $this->db->get('tbl_perusahaan')->row_array();

        $this->db->where('divisi_id', $level_id);
        $divisi = $this->db->get('tbl_divisi')->row_array();

        $foto = array();
        if ($user && isset($user['karyawan_id'])) {
            $this->db->where('karyawan_id', $user['karyawan_id']);
            $foto = $this->db->get('tbl_karyawan')->row_array();
        }

        $operator_info = null;
        if ($user && !empty($user['operator_id'])) {
            $this->db->where('operator_id', $user['operator_id']);
            $operator_info = $this->db->get('tbl_operator')->row_array();
        }

        // 2. Cek Super Admin / Developer / Switch Access
        $is_developer = (isset($user['username']) && $user['username'] === 'nizarsuryadi');

        // PENTING: Jika di tbl_divisi tidak ada, coba cari di tabel 'roles' (Migrasi Baru)
        if (!$divisi) {
            $role_pk = $this->db->field_exists('role_id', 'roles') ? 'role_id' : 'id';
            $roles_data = $this->db->get_where('roles', [$role_pk => $level_id])->row_array();
            if ($roles_data) {
                // Map ke format yang diharapkan MY_Controller (divisi_id & divisi_nama)
                $divisi = [
                    'divisi_id' => $roles_data[$role_pk],
                    'divisi_nama' => $roles_data['role_name'] ?? ($roles_data['name'] ?? 'Unknown Role'),
                    'is_super_admin' => $roles_data['is_admin'] ?? ($roles_data['is_super_admin'] ?? 0)
                ];
            }
        }


        $is_super = ($divisi && (isset($divisi['is_super_admin']) && $divisi['is_super_admin'] == 1) && $level_id != 1);

        // Multi-Branch Access Check: Owner (8/17) or explicitly Super Admin
        $can_switch = ($is_developer || $is_super || in_array($level_id, [1, 8, 17, 21]));

        // 3. Multi-Branch Logic
        $branches = [];
        if ($can_switch) {
            $branches = $this->db->get('tbl_perusahaan')->result();
        }

        // 4. Build Menu & Permissions Cache
        $karyawan_divisi_id = isset($foto['divisi_id']) ? (int) $foto['divisi_id'] : 0;
        $permissions = $this->_build_session_permissions($level_id, ($is_developer || $is_super), $user_id, $karyawan_divisi_id);

        $cache_payload = [
            'user_info' => $user,
            'perusahaan' => $perusahaan,
            'level_info' => $divisi,
            'karyawan_foto' => $foto,
            'operator_info' => $operator_info,
            'available_branches' => $branches,
            'is_super_admin' => ($is_developer || $is_super),
            'menu_data' => $permissions['menu_data'],
            'allowed_submenus' => $permissions['allowed_submenus'],
            'allowed_menus' => $permissions['allowed_menus'],
            'readonly_menus' => $permissions['readonly_menus'],
            'has_audit_table' => $permissions['has_audit_table'],
            'is_impersonated_cache' => $this->session->userdata('is_impersonating') ? true : false,
            'timestamp' => time()
        ];

        // Simpan ke CodeIgniter Session
        $this->session->set_userdata('app_session_cache', $cache_payload);
    }

    /**
     * Logic Menu Generator & Permissions Builder (Dijalankan SATU KALI saat login)
     */
    private function _build_session_permissions($role_id, $is_dev_context = false, $user_id = 0, $karyawan_divisi_id = 0)
    {
        $perms = [
            'menu_data' => [],
            'allowed_submenus' => [],
            'allowed_menus' => [],
            'readonly_menus' => [],
            'has_audit_table' => $this->db->table_exists('tbl_audit_log')
        ];

        // Deteksi Kolom Dinamis (Hanya Dieksekusi Sekali)
        $col_role = $this->db->field_exists('role_id', 'tbl_menu_access') ? 'role_id' : ($this->db->field_exists('level_id', 'tbl_menu_access') ? 'level_id' : 'role_id');
        $col_menu = $this->db->field_exists('menu_id', 'tbl_menu_access') ? 'menu_id' : ($this->db->field_exists('id_menu', 'tbl_menu_access') ? 'id_menu' : 'menu_id');
        $menu_pk_main = $this->db->field_exists('id', 'menu') ? 'id' : ($this->db->field_exists('id_menu', 'menu') ? 'id_menu' : 'id');

        // 1. MENU DATA
        if ($is_dev_context) {
            $this->db->select('*');
            $this->db->from('menu');
            $this->db->where('status', '1');
            $this->db->order_by('urutan', 'ASC');
            $perms['menu_data'] = $this->db->get()->result();
        } else {
            // Get Role Menus
            $this->db->select('menu.*, tbl_menu_access.is_readonly');
            $this->db->from('menu');
            $this->db->join('tbl_menu_access', "tbl_menu_access.$col_menu = menu.$menu_pk_main");
            $this->db->where("tbl_menu_access.$col_role", (int) $role_id);
            $this->db->where('menu.status', '1');
            $role_menus = $this->db->get()->result();

            // Get User Specific Menus
            $user_menus = [];
            if ($user_id > 0 && $this->db->table_exists('tbl_user_menu_access')) {
                $this->db->select('menu.*, tbl_user_menu_access.is_readonly');
                $this->db->from('menu');
                $this->db->join('tbl_user_menu_access', "tbl_user_menu_access.menu_id = menu.$menu_pk_main");
                $this->db->where('tbl_user_menu_access.user_id', (int) $user_id);
                $this->db->where('menu.status', '1');
                $user_menus = $this->db->get()->result();
            }

            // Get E-Commerce Menus (If User Belongs to Divisi E-Commerce but Role is different)
            $ecommerce_menus = [];
            if ($karyawan_divisi_id == 9 && $role_id != 9) {
                $this->db->select('menu.*, tbl_menu_access.is_readonly');
                $this->db->from('menu');
                $this->db->join('tbl_menu_access', "tbl_menu_access.$col_menu = menu.$menu_pk_main");
                $this->db->where("tbl_menu_access.$col_role", 9);
                $this->db->where('menu.status', '1');
                $ecommerce_menus = $this->db->get()->result();
            }

            // Merge and Deduplicate Menus
            $merged_menus = [];
            $menu_ids_added = [];

            $all_menus_raw = array_merge($role_menus, $user_menus, $ecommerce_menus);
            foreach ($all_menus_raw as $m) {
                if (!in_array($m->$menu_pk_main, $menu_ids_added)) {
                    $merged_menus[] = $m;
                    $menu_ids_added[] = $m->$menu_pk_main;
                }
            }

            // Sort by urutan
            usort($merged_menus, function ($a, $b) {
                return $a->urutan <=> $b->urutan;
            });

            $perms['menu_data'] = $merged_menus;

            foreach ($merged_menus as $m) {
                if (!empty($m->link)) {
                    $perms['allowed_menus'][] = $m->link;
                    if (isset($m->is_readonly) && $m->is_readonly == 1) {
                        $perms['readonly_menus'][] = $m->link;
                    }
                }
            }

            // ALLOWED SUBMENUS (Role)
            $allowed_sub_links = [];
            if ($this->db->table_exists('submenu') && $this->db->table_exists('tbl_submenu_access')) {
                $this->db->select('submenu.link_submenu');
                $this->db->from('submenu');
                $this->db->join('tbl_submenu_access', 'submenu.id = tbl_submenu_access.submenu_id');
                $this->db->where('tbl_submenu_access.role_id', (int) $role_id);
                $submenus = $this->db->get()->result();
                foreach ($submenus as $sm) {
                    if (!empty($sm->link_submenu)) {
                        $allowed_sub_links[] = $sm->link_submenu;
                    }
                }
            }

            // ALLOWED SUBMENUS (User Specific)
            if ($user_id > 0 && $this->db->table_exists('submenu') && $this->db->table_exists('tbl_user_submenu_access')) {
                $this->db->select('submenu.link_submenu');
                $this->db->from('submenu');
                $this->db->join('tbl_user_submenu_access', 'submenu.id = tbl_user_submenu_access.submenu_id');
                $this->db->where('tbl_user_submenu_access.user_id', (int) $user_id);
                $submenus_user = $this->db->get()->result();
                foreach ($submenus_user as $sm) {
                    if (!empty($sm->link_submenu)) {
                        $allowed_sub_links[] = $sm->link_submenu;
                    }
                }
            }

            // ALLOWED SUBMENUS (E-Commerce bypass)
            if ($karyawan_divisi_id == 9 && $role_id != 9 && $this->db->table_exists('submenu') && $this->db->table_exists('tbl_submenu_access')) {
                $this->db->select('submenu.link_submenu');
                $this->db->from('submenu');
                $this->db->join('tbl_submenu_access', 'submenu.id = tbl_submenu_access.submenu_id');
                $this->db->where('tbl_submenu_access.role_id', 9);
                $submenus_ec = $this->db->get()->result();
                foreach ($submenus_ec as $sm) {
                    if (!empty($sm->link_submenu)) {
                        $allowed_sub_links[] = $sm->link_submenu;
                    }
                }
            }

            $perms['allowed_submenus'] = array_unique($allowed_sub_links);
        }

        return $perms;
    }

    /**
     * Helper check Super Admin Context
     */
    private function _has_menu_access($controller, $method, $cache)
    {
        $allowed_submenus = $cache['allowed_submenus'] ?? [];
        foreach ($allowed_submenus as $link_raw) {
            $link = strtolower($link_raw);
            if (
                $link == $controller ||
                $link == $controller . '/' . $method ||
                $link == '../' . $controller ||
                $link == '../' . $controller . '/' . $method ||
                strpos($link, $controller . '/') === 0 ||
                strpos($link, '../' . $controller . '/') === 0
            ) {
                return true;
            }
        }
        $allowed_menus = $cache['allowed_menus'] ?? [];
        foreach ($allowed_menus as $link_raw) {
            $link = strtolower($link_raw);
            if (
                $link == $controller ||
                $link == '../' . $controller ||
                strpos($link, $controller . '/') === 0 ||
                strpos($link, '../' . $controller . '/') === 0
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Helper check Super Admin Context
     */
    private function _is_super_admin($user, $divisi)
    {
        // 1. Developer Bypass
        if (isset($user['username']) && $user['username'] === 'nizarsuryadi')
            return true;

        // 2. Explicit Case for Administrator (Role 1) - Must NOT be Super Admin
        $role_id = isset($divisi['divisi_id']) ? $divisi['divisi_id'] : (isset($user['level']) ? $user['level'] : 0);
        if ($role_id == 1)
            return false;

        // 3. Owner Check (ID 8 \u0026 17)
        if (in_array($role_id, [8, 17, 22]))
            return true;

        // 4. Fallback to DB Flag
        if ($divisi && isset($divisi['is_super_admin']) && $divisi['is_super_admin'] == 1)
            return true;

        return false;
    }

    // Helper cepat ambil status tanpa parameter (baca dari property $this->data)
    private function _is_super_admin_context()
    {
        return $this->data['is_developer'] ?? false;
    }

    /**
     * Cek status ReadOnly spesifik per controller
     */
    private function _check_is_readonly_context()
    {
        if ($this->_is_super_admin_context())
            return false;

        $cache = $this->session->userdata('app_session_cache');
        $readonly_menus = $cache['readonly_menus'] ?? [];
        $current_controller = strtolower($this->router->class);

        foreach ($readonly_menus as $link) {
            if (
                $link == $current_controller ||
                strpos($link, $current_controller . '/') === 0 ||
                $link == '../' . $current_controller ||
                strpos($link, '../' . $current_controller . '/') === 0
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * ---------------------------------------------------------
     * AUDIT SYSTEM (Enterprise Feature)
     * ---------------------------------------------------------
     */

    /**
     * Otomatis mencatat aktivitas POST (Write/Update/Delete)
     */
    private function _auto_audit_log()
    {
        $class = $this->router->class;
        $method = $this->router->method;

        // 1. Daftar Controller yang TIDAK perlu dicatat secara otomatis (High traffic / non-critical)
        $ignored_controllers = ['absensi', 'chat', 'notifikasi', 'antrian'];
        if (in_array(strtolower($class), $ignored_controllers))
            return;

        // 2. Daftar method spesifik yang TIDAK perlu dicatat
        $ignored_methods = ['get_data', 'search', 'ajax_list', 'get_notif'];
        if (in_array(strtolower($method), $ignored_methods))
            return;

        $captured_data = array_merge($this->input->get(), $this->input->post());

        // Jika tidak ada data sama sekali, tetap catat URI
        if (empty($captured_data))
            $captured_data = ['uri' => $this->uri->uri_string()];

        // Hilangkan data sensitif dari log (Password dsb)
        $sensitive_fields = ['password', 'old_password', 'new_password', 'confirm_password', 'pass', 'pin', 'pin_operator', 'operator_pin', 'supervisor_pin', 'pin_produksi', 'wip_qc_csrf'];
        foreach ($sensitive_fields as $field) {
            if (isset($captured_data[$field]))
                $captured_data[$field] = '********';
        }

        $log_content = [
            'url' => current_url(),
            'data' => $captured_data,
            'user_agent' => $this->input->user_agent()
        ];

        $this->_write_audit_entry(
            'WRITE',
            "User melakukan aksi di modul $class fungsi $method",
            json_encode($log_content)
        );
    }

    /**
     * Mencatat entri log ke Database
     */
    protected function _write_audit_entry($type, $action, $details = null)
    {
        // Gunakan cache boolean agar tidak menjalankan SHOW TABLES di setiap request
        $cache = $this->session->userdata('app_session_cache');
        $has_audit = isset($cache['has_audit_table']) ? $cache['has_audit_table'] : $this->db->table_exists('tbl_audit_log');

        if (!$has_audit) {
            return;
        }

        $data = [
            'user_id' => $this->session->userdata('id'),
            'username' => $this->data['username'] ?? 'System',
            'ip_address' => $this->input->ip_address(),
            'log_type' => $type, // INFO, WRITE, DELETE, LOGIN
            'log_action' => $action,
            'log_details' => $details,
            'controller' => $this->router->class,
            'method' => $this->router->method,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('tbl_audit_log', $data);
    }

    /**
     * Public helper untuk log manual dari controller lain
     * Contoh penggunaan: $this->_log_manual("Membatalkan Invoice #123", json_encode($data_lama));
     */
    public function _log_manual($action, $details = null, $type = 'INFO')
    {
        $this->_write_audit_entry($type, $action, $details);
    }

    /**
     * ---------------------------------------------------------
     * VIEW & HELPER METHODS
     * ---------------------------------------------------------
     */

    /**
     * Load view standar Admin
     */
    protected function load_admin_view($content_view, $additional_data = [])
    {
        // Hapus 'menu' dari additional_data agar tidak menimpa data menu dinamis dari MY_Controller
        unset($additional_data['menu']);
        $view_data = array_merge($this->data, $additional_data);

        // HYBRID PROXY: Jika diakses lewat Mobile Owner Dashboard
        if ($this->session->userdata('is_owner_proxy')) {
            $view_data['token'] = 'TGM2026';
            $view_data['perusahaan_id'] = $this->input->get('perusahaan_id') ?: $this->session->userdata('perusahaan_id');
            $view_data['hariini'] = date('Y-m-d');
            $view_data['all_perusahaan'] = $this->db->get_where('tbl_perusahaan', ['status' => '1'])->result();

            $this->load->view('owner_mobile/layout', $view_data);
            echo '<div class="app-container" style="max-width:100%; padding-top:20px;">';
            echo '<div class="summary-card" style="overflow-x:auto;">'; // Supaya tabel desktop bisa digeser
            $this->load->view($content_view, $view_data);
            echo '</div></div>';
            $this->load->view('owner_mobile/footer', $view_data);
            return;
        }

        // Load struktur halaman standar
        $this->load->view('layout_admin/head', $view_data);
        $this->load->view('layout_admin/top_header', $view_data);
        $this->load->view('layout_admin/sidebar', $view_data); // Enable Sidebar Left

        $this->load->view($content_view, $view_data);

        $this->load->view('layout_admin/bottom_nav', $view_data);
        $this->load->view('layout_admin/footer', $view_data);
        $this->load->view('layout_admin/alert', $view_data);
    }

    /**
     * Load view khusus DEVELOPER (Tema Berbeda: Dark/Industrial)
     */
    protected function load_dev_view($content_view, $additional_data = [])
    {
        // Hapus 'menu' dari additional_data agar tidak menimpa data menu dinamis dari MY_Controller
        unset($additional_data['menu']);
        $view_data = array_merge($this->data, $additional_data);
        $view_data['is_dev_theme'] = true; // Flag untuk CSS khusus di head

        // Load struktur halaman standar (head akan baca flag is_dev_theme)
        $this->load->view('layout_admin/head', $view_data);
        $this->load->view('layout_admin/top_header', $view_data);
        $this->load->view('layout_admin/sidebar', $view_data); // Enable Sidebar Left

        $this->load->view($content_view, $view_data);

        $this->load->view('layout_admin/bottom_nav', $view_data);
        $this->load->view('layout_admin/footer', $view_data);
        $this->load->view('layout_admin/alert', $view_data);
    }

    /**
     * Helper untuk Modul WIP / Produksi
     * Menentukan apakah user ini Operator Mesin atau Admin
     * @return int|null (Divisi ID jika operator, Null jika admin)
     */
    protected function get_operator_id_for_current_user()
    {
        // 1. Jika Super Admin / Developer -> Bisa lihat semua (Return NULL)
        if ($this->_is_super_admin_context()) {
            return null;
        }

        // 2. Owner (8/17) dan HR (21)
        // Diberikan akses 'Lihat Semua' untuk modul Operasional & SDM tertentu
        $role_id = $this->session->userdata('level');
        if (in_array($role_id, [8, 17, 21])) {
            $modul = strtolower($this->router->class);

            // Khusus modul Penggajian tetap difilter ketat jika bukan Super Admin
            if ($modul !== 'penggajian') {
                return null;
            }
        }

        // 3. User Biasa / Operator -> Hanya bisa lihat job divisinya sendiri
        $cache = $this->session->userdata('app_session_cache');
        if (isset($cache['operator_info']['operator_id']) && !empty($cache['operator_info']['operator_id'])) {
            return $cache['operator_info']['operator_id'];
        }

        if (isset($cache['level_info']['divisi_id'])) {
            return $cache['level_info']['divisi_id'];
        }

        return $role_id; // Fallback
    }

    /**
     * Helper untuk Modul WIP / Produksi
     * Mendapatkan kategori yang diizinkan untuk dikelola oleh user saat ini
     * @return array|null (Array Kategori ID jika operator, Null jika admin/bisa lihat semua)
     */
    protected function get_operator_kategori_ids()
    {
        if ($this->_is_super_admin_context()) {
            return null;
        }

        $role_id = (int) $this->session->userdata('level');
        if (in_array($role_id, [1, 8, 17, 21, 22], true)) {
            return null; // Admin/Owner see all
        }

        $cache = $this->session->userdata('app_session_cache');
        if (isset($cache['operator_info']['kategori_ids']) && !empty($cache['operator_info']['kategori_ids'])) {
            return explode(',', $cache['operator_info']['kategori_ids']);
        }

        // --- DYNAMIC USERNAME AND ROLE-BASED FALLBACK ---
        $username = strtolower($this->session->userdata('username') ?: '');
        if (!empty($username)) {
            if (strpos($username, 'uv') !== false || strpos($username, 'platbed') !== false || strpos($username, 'flatbed') !== false || strpos($username, 'rotary') !== false) {
                return ['3']; // Flatbed & Rotary
            }
            if (strpos($username, 'lf') !== false || strpos($username, 'large') !== false || strpos($username, 'format') !== false || strpos($username, 'outdoor') !== false || strpos($username, 'indoor') !== false) {
                return ['2']; // Large Format
            }
            if (strpos($username, 'sublim') !== false) {
                return ['4']; // Sublimasi
            }
            if (strpos($username, 'textile') !== false || strpos($username, 'tekstil') !== false) {
                return ['5']; // Tekstil
            }
            if (strpos($username, 'laser') !== false || strpos($username, 'cutting') !== false || strpos($username, 'cut') !== false) {
                return ['6']; // Laser Cut & Engravir
            }
            if (strpos($username, 'dtg') !== false || strpos($username, 'sablon') !== false) {
                return ['8']; // DTG
            }
            if (strpos($username, 'dtf') !== false) {
                return ['9']; // DTF
            }
            if (strpos($username, 'merch') !== false || strpos($username, 'merchandise') !== false) {
                return ['10']; // Merchandise
            }
            if (strpos($username, 'a3') !== false || strpos($username, 'digital') !== false || strpos($username, 'offset') !== false) {
                return ['1']; // Digital A3+
            }
        }

        // Check if operator name contains keywords (fallback if mapped but kategori_ids is null/empty)
        if (isset($cache['operator_info']['operator_nama'])) {
            $op_name = strtolower($cache['operator_info']['operator_nama']);
            if (strpos($op_name, 'uv') !== false || strpos($op_name, 'flatbed') !== false || strpos($op_name, 'platbed') !== false || strpos($op_name, 'rotary') !== false) {
                return ['3'];
            }
            if (strpos($op_name, 'large') !== false || strpos($op_name, 'format') !== false) {
                return ['2'];
            }
            if (strpos($op_name, 'sublim') !== false) {
                return ['4'];
            }
            if (strpos($op_name, 'tekstil') !== false || strpos($op_name, 'textile') !== false) {
                return ['5'];
            }
            if (strpos($op_name, 'dtf') !== false) {
                return ['9'];
            }
            if (strpos($op_name, 'dtg') !== false) {
                return ['8'];
            }
            if (strpos($op_name, 'laser') !== false || strpos($op_name, 'cutting') !== false) {
                return ['6'];
            }
            if (strpos($op_name, 'merch') !== false || strpos($op_name, 'merchandise') !== false) {
                return ['10'];
            }
            if (strpos($op_name, 'a3') !== false || strpos($op_name, 'digital') !== false) {
                return ['1'];
            }
        }

        return ['1']; // Default/Fallback: Digital A3+
    }

    // --- Utilitas ---

    public function get_config()
    {
        // Compatibility wrapper untuk controller lama yang panggil $this->get_config()
        return [
            'perusahaan' => $this->session->userdata('app_session_cache')['perusahaan'] ?? [],
            'hakakses' => $this->session->userdata('app_session_cache')['level_info'] ?? [],
            'userid' => $this->session->userdata('app_session_cache')['user_info'] ?? [],
            'available_branches' => $this->session->userdata('app_session_cache')['available_branches'] ?? []
        ];
    }

    public function get_common_data()
    {
        return $this->data;
    }
}
