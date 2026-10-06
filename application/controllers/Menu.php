<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_menu');
    }

    /**
     * Main menu management page
     * Displays all menus with CRUD functionality
     */
    public function run_migration_check($silent = false)
    {
        // Protect this method
        // if ($this->session->userdata('level') != 1) {
        //     die("Access Denied");
        // }

        if (!$silent) {
            file_put_contents('migration_log.txt', "Starting Migration Check...\n");
        }

        // Helper for logging
        $log_msg = function ($msg) use ($silent) {
            if (!$silent) {
                echo $msg;
            }
            file_put_contents('migration_log.txt', $msg, FILE_APPEND);
        };

        $log_msg("Starting Migration Check...\n");

        // 1. Create table if not exists
        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_menu_access` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `menu_id` int(11) NOT NULL,
              `role_id` int(11) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `menu_id` (`menu_id`),
              KEY `role_id` (`role_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // [AUTO-FIX] Rename level_id to role_id if exists (Migration support)
        if ($this->db->field_exists('level_id', 'tbl_menu_access')) {
            $this->db->query("ALTER TABLE `tbl_menu_access` CHANGE `level_id` `role_id` INT(11) NOT NULL;");
        }
        $log_msg("Table tbl_menu_access checked.\n");

        // [NEW] Create submenu access table
        $this->db->query("CREATE TABLE IF NOT EXISTS `tbl_submenu_access` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `submenu_id` int(11) NOT NULL,
              `role_id` int(11) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `submenu_id` (`submenu_id`),
              KEY `role_id` (`role_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        $log_msg("Table tbl_submenu_access checked.\n");


        // 2. [DISABLED] Legacy Duplicate Menu Migration (Depends on dropped 'level' column)
        /*
        $duplicates = $this->db->query("
                SELECT link, COUNT(*) as count, GROUP_CONCAT(id) as ids 
                FROM menu 
                GROUP BY link 
                HAVING count > 1
            ")->result();

        $log_msg("Found " . count($duplicates) . " menu groups with duplicates.\n");

        foreach ($duplicates as $dup) {
            $ids = explode(',', $dup->ids);
            sort($ids); // Master ID is the smallest one
            $master_id = $ids[0];

            $log_msg("Processing Duplicate Group: {$dup->link}\n");
            $log_msg("Master ID: {$master_id}\n");

            // Process all IDs in this group
            foreach ($ids as $id) {
                // Get 'level' from this menu row
                $menu_row = $this->db->get_where('menu', ['id' => $id])->row();

                if ($menu_row) {
                    // Legacy populating tbl_menu_access logic removed
                }

                // Move submenus if this is not master_id
                if ($id != $master_id) {
                    $this->db->where('id_menu', $id);
                    $this->db->update('submenu', ['id_menu' => $master_id]);
                    $log_msg(" - Submenus moved from {$id} to {$master_id}\n");

                    // Delete duplicate menu row
                    $this->db->where('id', $id);
                    $this->db->delete('menu');
                    $log_msg(" - Duplicate Menu ID {$id} deleted.\n");
                }
            }
        }
        */

        // 3. [DISABLED] Legacy Single Menu Migration (Depends on dropped 'level' column)
        /*
        $singles = $this->db->query("
                SELECT link, COUNT(*) as count, id, level
                FROM menu 
                GROUP BY link 
                HAVING count = 1
            ")->result();

        foreach ($singles as $single) {
            // Legacy logic removed
        }
        */

        // 4. [NEW] Populate tbl_submenu_access based on tbl_menu_access
        // If a role has access to a parent menu, give them access to ALL submenus of that parent
        $log_msg("Checking Submenu Access Permissions...\n");

        $parent_access_list = $this->db->get('tbl_menu_access')->result();

        foreach ($parent_access_list as $pa) {
            $this->_syncSubmenuAccess($pa->menu_id, null, $pa->role_id);
        }

        $log_msg("Migration Completed.\n");
        if (!$silent) {
            $log_msg("</pre>");
        }
    }

    /**
     * [API] Ambil data matrix permission untuk System Permissions Panel
     * Returns: JSON { roles, menus: [{menu, submenus, accesses}] }
     */
    public function ajax_get_permissions()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $role_pk = 'divisi_id';
        $col_role = $this->db->field_exists('role_id', 'tbl_menu_access') ? 'role_id' : 'level_id';
        $col_menu = $this->db->field_exists('menu_id', 'tbl_menu_access') ? 'menu_id' : 'id_menu';
        $col_role_sub = $this->db->field_exists('role_id', 'tbl_submenu_access') ? 'role_id' : 'level_id';
        $col_parent = $this->db->field_exists('id_menu', 'submenu') ? 'id_menu' : 'menu_id';

        // Semua Role
        $role_pk = $this->db->field_exists('role_id', 'roles') ? 'role_id' : 'id';
        $roles = $this->db->select("$role_pk as id, role_name as name")->get('roles')->result();

        // Semua Menu + akses per role
        $menus_raw = $this->db->order_by('urutan', 'ASC')->get('menu')->result();

        $menus = [];
        foreach ($menus_raw as $m) {
            // IDs role yang punya akses ke menu ini
            $access_rows = $this->db->select($col_role . ' as role_id')
                ->get_where('tbl_menu_access', [$col_menu => $m->id])->result();
            $granted_roles = array_column($access_rows, 'role_id');

            // Sub-menus
            $subs_raw = $this->db->order_by('urutan', 'ASC')
                ->get_where('submenu', [$col_parent => $m->id])->result();
            $submenus = [];
            foreach ($subs_raw as $s) {
                $sub_access = $this->db->select($col_role_sub . ' as role_id')
                    ->get_where('tbl_submenu_access', ['submenu_id' => $s->id])->result();
                $submenus[] = [
                    'id' => $s->id,
                    'name' => $s->nama_submenu,
                    'granted_roles' => array_column($sub_access, 'role_id'),
                ];
            }

            $menus[] = [
                'id' => $m->id,
                'name' => $m->nama_menu,
                'icon' => $m->icon_fa ?? $m->icon ?? 'bi-circle',
                'granted_roles' => $granted_roles,
                'submenus' => $submenus,
            ];
        }

        echo json_encode(['status' => 'ok', 'roles' => $roles, 'menus' => $menus]);
    }

    /**
     * [API] Toggle akses MENU untuk satu role (POST)
     * Body: menu_id, role_id, grant (1/0)
     */
    public function ajax_toggle_menu_permission()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $menu_id = (int) $this->input->post('menu_id', true);
        $role_id = (int) $this->input->post('role_id', true);
        $grant = (int) $this->input->post('grant', true); // 1 = tambah, 0 = hapus

        $col_role = $this->db->field_exists('role_id', 'tbl_menu_access') ? 'role_id' : 'level_id';
        $col_menu = $this->db->field_exists('menu_id', 'tbl_menu_access') ? 'menu_id' : 'id_menu';

        if ($grant) {
            $exists = $this->db->get_where('tbl_menu_access', [$col_menu => $menu_id, $col_role => $role_id])->num_rows();
            if (!$exists) {
                $this->db->insert('tbl_menu_access', [$col_menu => $menu_id, $col_role => $role_id]);
                // Aktifkan status menu agar muncul di sidebar
                $this->db->where('id', $menu_id)->update('menu', ['status' => 1]);
            }
        } else {
            $this->db->where($col_menu, $menu_id)->where($col_role, $role_id)->delete('tbl_menu_access');
        }

        echo json_encode(['status' => 'ok', 'action' => $grant ? 'granted' : 'revoked']);
    }

    /**
     * [API] Toggle akses SUBMENU untuk satu role (POST)
     * Body: submenu_id, role_id, grant (1/0)
     */
    public function ajax_toggle_submenu_permission()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $submenu_id = (int) $this->input->post('submenu_id', true);
        $role_id = (int) $this->input->post('role_id', true);
        $grant = (int) $this->input->post('grant', true);

        $col_role_sub = $this->db->field_exists('role_id', 'tbl_submenu_access') ? 'role_id' : 'level_id';

        if ($grant) {
            $exists = $this->db->get_where('tbl_submenu_access', ['submenu_id' => $submenu_id, $col_role_sub => $role_id])->num_rows();
            if (!$exists) {
                $this->db->insert('tbl_submenu_access', ['submenu_id' => $submenu_id, $col_role_sub => $role_id]);
                // Aktifkan status submenu
                $this->db->where('id', $submenu_id)->update('submenu', ['status' => 1]);
            }
        } else {
            $this->db->where('submenu_id', $submenu_id)->where($col_role_sub, $role_id)->delete('tbl_submenu_access');
        }

        echo json_encode(['status' => 'ok', 'action' => $grant ? 'granted' : 'revoked']);
    }

    public function index()
    {
        $submenu_table_exists = $this->db->table_exists('tbl_submenu_access');

        // [PERBAIKAN KRITIS] Pastikan struktur tabel sinkron sebelum query apapun
        $tables = ['tbl_menu_access', 'tbl_submenu_access'];
        foreach ($tables as $tbl) {
            if ($this->db->table_exists($tbl)) {
                if ($this->db->field_exists('level_id', $tbl)) {
                    $this->db->query("ALTER TABLE `$tbl` CHANGE `level_id` `role_id` INT(11) NOT NULL;");
                }
                // Pastikan role_id adalah INT agar tidak gagal saat insert
                $this->db->query("ALTER TABLE `$tbl` MODIFY `role_id` INT(11) NOT NULL;");
            }
        }



        /* 
        if (!$submenu_table_exists || $missing_access > 0) {
            $this->run_migration_check(true);
        }
        */

        // Migration removed.
        // 1. Ambil Statistik
        $jmlmenu = $this->db->count_all('menu');
        $active_menu = $this->getActiveMenuCount();
        $inactive_menu = $this->getInactiveMenuCount();
        $total_submenu = $this->getTotalSubmenu();

        // 2. Ambil Roles
        $role_pk = $this->db->field_exists('role_id', 'roles') ? 'role_id' : 'id';
        $roles = $this->db->select("*, $role_pk as role_id")->order_by('role_name', 'ASC')->get('roles')->result();

        // 3. AMBIL DATA UTAMA DENGAN JOIN AKSES TINGKAT TINGGI
        // Menggunakan subquery atau join untuk mengambil semua role sekaligus agar lebih efisien
        $this->db->select('m.*');
        $this->db->from('menu m');
        $this->db->order_by('m.urutan', 'ASC');
        $menus = $this->db->get()->result();

        // PK Tabel Menu untuk enrichment
        $menu_pk = $this->db->field_exists('id', 'menu') ? 'id' : ($this->db->field_exists('id_menu', 'menu') ? 'id_menu' : 'id');
        $col_role = $this->db->field_exists('role_id', 'tbl_menu_access') ? 'role_id' : 'level_id';
        $col_menu = $this->db->field_exists('menu_id', 'tbl_menu_access') ? 'menu_id' : 'id_menu';
        
        // Primary Key tabel roles untuk join
        $role_pk_target = $this->db->field_exists('role_id', 'roles') ? 'role_id' : 'id';

        // 4. Enrich menus with access levels (Highly Robust Version)
        foreach ($menus as &$m) {
            // Definisikan ID utama menu
            $actual_id = isset($m->id) ? $m->id : (isset($m->id_menu) ? $m->id_menu : 0);
            
            // Samakan properti ke 'id' agar view tidak bingung
            $m->id = $actual_id;

            $this->db->select("ma.$col_role as role_id, r.role_name");
            $this->db->from('tbl_menu_access ma');
            $this->db->join('roles r', "r.$role_pk_target = ma.$col_role", 'left');
            $this->db->where("ma.$col_menu", (int)$actual_id);
            $query_res = $this->db->get()->result();

            // Jika role_name null (karena join left gagal), beri fallback nama role
            foreach ($query_res as &$res) {
                if (empty($res->role_name)) {
                    $res->role_name = "Role ID: " . $res->role_id;
                }
            }
            
            $m->access_levels = $query_res;
        }

        // 5. Final Data
        $data = [
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Manajemen Menu Utama',
            'table' => 'menu',
            'jmlmenu' => $jmlmenu,
            'active_menu' => $active_menu,
            'inactive_menu' => $inactive_menu,
            'total_submenu' => $total_submenu,
            'roles' => $roles,
            'tampil_menu' => $menus
        ];

        $this->load_admin_view('admin/pengaturan/menu/index', $data);
    }

    /**
     * Submenu management page
     * Displays submenus for a specific menu
     */
    public function submenu()
    {
        $controller = $this->uri->segment(1);
        $function = $this->uri->segment(2);
        $submenu = $this->uri->segment(3);

        if (!$submenu) {
            $this->session->set_flashdata('error', 'ID Menu tidak ditemukan');
            redirect('menu');
        }

        // Get menu information
        $menu_info = $this->db->get_where('menu', array('id' => $submenu))->row();
        if (!$menu_info) {
            $this->session->set_flashdata('error', 'Menu tidak ditemukan');
            redirect('menu');
        }

        // Deteksi PK Tabel Roles secara dinamis
        $role_pk = $this->db->field_exists('role_id', 'roles') ? 'role_id' : 'id';

        $data = array(
            'controller' => $controller,
            'function' => $function,
            'title' => 'Manajemen Submenu - ' . $menu_info->nama_menu,
            'table' => 'submenu',
            'menu_info' => $menu_info,
            'getsubmenu' => $this->db->order_by('urutan', 'ASC')->get_where('submenu', array('id_menu' => $submenu))->result(),
            'jmlmenu' => $this->db->get_where('submenu', array('id_menu' => $submenu))->num_rows(),
            'submenu' => $this->M_master->getmenu($controller)->result(),
            'total_submenu' => $this->getTotalSubmenu(),
            'active_submenu' => $this->getActiveSubmenuCount($submenu),
            'inactive_submenu' => $this->getInactiveSubmenuCount($submenu),
            'roles' => $this->db->order_by($role_pk, 'ASC')->get('roles')->result(),
            'all_users' => $this->db->select('user_id, username, email')->where('active', 1)->order_by('username', 'ASC')->get('users')->result()
        );

        // [NEW] Enrich submenus with access levels
        foreach ($data['getsubmenu'] as &$sm) {
            $sm->access_levels = $this->db->select("r.$role_pk as role_id, r.role_name")
                ->from('tbl_submenu_access sa')
                ->join('roles r', "r.$role_pk = sa.role_id")
                ->where('sa.submenu_id', $sm->id)
                ->get()
                ->result();
                
            // [NEW] Fetch user specific access
            $sm->user_access_levels = $this->db->select("u.user_id, u.username")
                ->from('tbl_user_submenu_access usa')
                ->join('users u', "u.user_id = usa.user_id")
                ->where('usa.submenu_id', $sm->id)
                ->get()
                ->result();
        }

        $this->load_admin_view('admin/pengaturan/menu/submenu', $data);
    }

    /**
     * Customer activity page
     * Displays customer transaction activities
     */
    public function aktivitas_konsumen()
    {
        $controller = $this->uri->segment(1);
        $function = $this->uri->segment(2);

        $data = array(
            'controller' => $controller,
            'function' => $function,
            'title' => 'CRM - Aktivitas Konsumen',
            'table' => 'tbl_konsumen',
            'tampil_data' => $this->M_transaksi->get_konsumen_dengan_transaksi()->result(),
            'total_rows' => $this->M_transaksi->get_konsumen_dengan_transaksi()->num_rows(),
            // 'menu' diambil otomatis dari MY_Controller
            'submenu' => $this->M_master->getmenu($controller)->result(),
            'total_konsumen' => $this->getTotalKonsumen(),
            'active_konsumen' => $this->getActiveKonsumenCount(),
            'recent_activities' => $this->getRecentActivities()
        );

        $this->load_admin_view('admin/transaksi/konsumen/aktivitas_konsumen', $data);
    }

    // ==================== MENU CRUD OPERATIONS ====================

    /**
     * Add new menu
     */
    public function actionAddMenu()
    {
        header('Content-Type: application/json');

        $nama_menu = $this->input->post('nama_menu', true);
        $alias = $this->input->post('alias', true);
        $icon = $this->input->post('icon', true) ?: $this->input->post('icon_menu', true);
        $link = $this->input->post('link', true);
        $urutan = $this->input->post('urutan', true);
        $status = $this->input->post('status', true);
        $access_levels = $this->input->post('access_levels', true);

        // Validation
        if (empty($nama_menu)) {
            echo json_encode(['status' => 'error', 'pesan' => 'Nama menu wajib diisi']);
            return;
        }

        $data = [
            'nama_menu' => $nama_menu,
            'alias' => $alias ?: $nama_menu,
            'icon' => $icon ?: 'bi bi-circle',
            'icon_fa' => $icon ?: 'bi bi-circle',
            'icon_menu' => $icon ?: 'bi bi-circle',
            'link' => $link ?: '#',
            'urutan' => $urutan ?: 0,
            'status' => $status !== null ? $status : 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $insert = $this->db->insert('menu', $data);
        if ($insert) {
            $menu_id = $this->db->insert_id();
            $this->_syncMenuAccess($menu_id, $access_levels);
            $this->session->unset_userdata('app_session_cache');
            echo json_encode(['status' => 'success', 'pesan' => 'Menu berhasil ditambahkan']);
        } else {
            echo json_encode(['status' => 'error', 'pesan' => 'Gagal menambahkan menu']);
        }
    }

    /**
     * Update menu
     */
    public function actionUpdateMenu()
    {
        header('Content-Type: application/json');

        $menu_id = $this->input->post('id', true) ?: $this->input->post('menu_id', true);
        $nama_menu = $this->input->post('nama_menu', true);
        $alias = $this->input->post('alias', true);
        $icon = $this->input->post('icon', true) ?: $this->input->post('icon_menu', true);
        $link = $this->input->post('link', true);
        $urutan = $this->input->post('urutan', true);
        $status = $this->input->post('status', true);
        $access_levels = $this->input->post('access_levels', true);

        // Validation
        if (empty($menu_id) || empty($nama_menu)) {
            echo json_encode(['status' => 'error', 'pesan' => 'ID menu dan nama menu wajib diisi']);
            return;
        }

        $data = [
            'nama_menu' => $nama_menu,
            'alias' => $alias ?: $nama_menu,
            'icon' => $icon,
            'icon_fa' => $icon,
            'icon_menu' => $icon,
            'link' => $link,
            'urutan' => $urutan ?: 0,
            'status' => $status !== null ? $status : 1,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->where('id', $menu_id);
        $update = $this->db->update('menu', $data);

        if ($update) {
            $this->_syncMenuAccess($menu_id, $access_levels);
            $this->session->unset_userdata('app_session_cache');
            echo json_encode(['status' => 'success', 'pesan' => 'Menu berhasil diperbarui']);
        } else {
            echo json_encode(['status' => 'error', 'pesan' => 'Gagal memperbarui menu']);
        }
    }

    
    /**
     * Inline Toggle Role Access
     */
    public function actionToggleRole()
    {
        header('Content-Type: application/json');
        
        $type = $this->input->post('type', true);
        $menu_id = $this->input->post('menu_id', true);
        $role_id = $this->input->post('role_id', true);
        
        if (empty($type) || empty($menu_id) || empty($role_id)) {
            echo json_encode(['status' => 'error', 'pesan' => 'Data tidak lengkap']);
            return;
        }

        $table = ($type === 'menu') ? 'tbl_menu_access' : 'tbl_submenu_access';
        $menu_col = ($type === 'menu') ? 'menu_id' : 'submenu_id';

        // Custom handling because id_menu or menu_id naming in tbl_menu_access
        if ($type === 'menu' && $this->db->field_exists('id_menu', 'tbl_menu_access')) {
            $menu_col = 'id_menu';
        }

        $exists = $this->db->get_where($table, [
            $menu_col => $menu_id,
            'role_id' => $role_id
        ])->row();

        if ($exists) {
            $this->db->where([$menu_col => $menu_id, 'role_id' => $role_id]);
            $this->db->delete($table);
            $action = 'removed';
        } else {
            $this->db->insert($table, [
                $menu_col => $menu_id,
                'role_id' => $role_id
            ]);
            $action = 'added';
        }

        $this->session->unset_userdata('app_session_cache');

        echo json_encode([
            'status' => 'success', 
            'action' => $action,
            'csrf_name' => $this->security->get_csrf_token_name(),
            'csrf_hash' => $this->security->get_csrf_hash()
        ]);
    }


    /**
     * Delete menu
     */
    public function actionDeleteMenu($menu_id)
    {
        header('Content-Type: application/json');

        if (empty($menu_id)) {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'ID menu tidak ditemukan'
            ]);
            return;
        }

        // Check if menu has submenus
        $this->db->where('id_menu', $menu_id);
        $has_submenus = $this->db->count_all_results('submenu') > 0;

        if ($has_submenus) {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'Menu tidak dapat dihapus karena memiliki submenu'
            ]);
            return;
        }

        $this->db->trans_start();
        $this->db->where('menu_id', $menu_id);
        $this->db->delete('tbl_menu_access');

        $this->db->where('id', $menu_id);
        $this->db->delete('menu');
        $this->db->trans_complete();

        if ($this->db->trans_status() === TRUE) {
            $this->session->unset_userdata('app_session_cache');
            echo json_encode([
                'status' => 'success',
                'pesan' => 'Menu berhasil dihapus'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'Gagal menghapus menu'
            ]);
        }
    }

    /**
     * Get menu data for editing
     */
    public function getMenuById($menu_id)
    {
        header('Content-Type: application/json');

        $menu = $this->db->get_where('menu', ['id' => $menu_id])->row();

        if ($menu) {
            echo json_encode([
                'status' => 'success',
                'data' => $menu
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'Menu tidak ditemukan'
            ]);
        }
    }

    // ==================== SUBMENU CRUD OPERATIONS ====================

    /**
     * Add new submenu
     */
    public function actionAddSubmenu()
    {
        header('Content-Type: application/json');

        $menu_id = $this->input->post('menu_id', true);
        $nama_submenu = $this->input->post('nama_submenu', true);
        $link_submenu = $this->input->post('link_submenu', true);
        $urutan = $this->input->post('urutan', true);
        $status = $this->input->post('status', true);

        // Validation
        if (empty($menu_id) || empty($nama_submenu) || empty($link_submenu)) {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'Menu, nama submenu, dan link submenu wajib diisi'
            ]);
            return;
        }

        // Check if menu exists
        $menu_exists = $this->db->get_where('menu', ['id' => $menu_id])->row();
        if (!$menu_exists) {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'Menu tidak ditemukan'
            ]);
            return;
        }

        // Check if submenu name already exists in the same menu
        $this->db->where('id_menu', $menu_id);
        $this->db->where('nama_submenu', $nama_submenu);
        if ($this->db->count_all_results('submenu') > 0) {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'Nama submenu sudah digunakan dalam menu ini'
            ]);
            return;
        }

        $data = [
            'id_menu' => $menu_id,
            'nama_submenu' => $nama_submenu,
            'link_submenu' => $link_submenu,
            'urutan' => $urutan ?: 0,
            'status' => $status ?: 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $insert = $this->db->insert('submenu', $data);

        if ($insert) {
            echo json_encode([
                'status' => 'success',
                'pesan' => 'Submenu berhasil ditambahkan'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'Gagal menambahkan submenu'
            ]);
        }
    }

    /**
     * Update submenu
     */
    public function actionUpdateSubmenu()
    {
        header('Content-Type: application/json');

        $submenu_id = $this->input->post('submenu_id', true);
        $menu_id = $this->input->post('id_menu', true) ? $this->input->post('id_menu', true) : $this->input->post('menu_id', true);
        $nama_submenu = $this->input->post('nama_submenu', true);
        $link_submenu = $this->input->post('link_submenu', true);
        $urutan = $this->input->post('urutan', true);
        $status = $this->input->post('status', true);

        // Validation
        if (empty($submenu_id) || empty($nama_submenu) || empty($link_submenu)) {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'ID submenu, nama submenu, dan link submenu wajib diisi'
            ]);
            return;
        }

        // Check if submenu exists
        $existing_submenu = $this->db->get_where('submenu', ['id' => $submenu_id])->row();
        if (!$existing_submenu) {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'Submenu tidak ditemukan'
            ]);
            return;
        }

        // Check if submenu name already exists in the same menu (excluding current submenu)
        $this->db->where('id_menu', $menu_id);
        $this->db->where('nama_submenu', $nama_submenu);
        $this->db->where('id !=', $submenu_id);
        if ($this->db->count_all_results('submenu') > 0) {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'Nama submenu sudah digunakan dalam menu ini'
            ]);
            return;
        }

        $data = [
            'id_menu' => $menu_id,
            'nama_submenu' => $nama_submenu,
            'link_submenu' => $link_submenu,
            'urutan' => $urutan ?: 0,
            'status' => $status ?: 1
        ];

        $this->db->where('id', $submenu_id);
        $update = $this->db->update('submenu', $data);

        if ($update) {
            echo json_encode([
                'status' => 'success',
                'pesan' => 'Submenu berhasil diperbarui'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'Gagal memperbarui submenu'
            ]);
        }
    }

    /**
     * Delete submenu
     */
    public function actionDeleteSubmenu($submenu_id)
    {
        header('Content-Type: application/json');

        if (empty($submenu_id)) {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'ID submenu tidak ditemukan'
            ]);
            return;
        }

        // Check if submenu exists
        $submenu = $this->db->get_where('submenu', ['id' => $submenu_id])->row();
        if (!$submenu) {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'Submenu tidak ditemukan'
            ]);
            return;
        }

        $this->db->where('id', $submenu_id);
        $delete = $this->db->delete('submenu');

        if ($delete) {
            echo json_encode([
                'status' => 'success',
                'pesan' => 'Submenu berhasil dihapus'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'Gagal menghapus submenu'
            ]);
        }
    }

    /**
     * Get submenu data for editing
     */
    public function getSubmenuById($submenu_id)
    {
        header('Content-Type: application/json');

        $submenu = $this->db->get_where('submenu', ['id' => $submenu_id])->row();

        if ($submenu) {
            echo json_encode([
                'status' => 'success',
                'data' => $submenu
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'Submenu tidak ditemukan'
            ]);
        }
    }

    /**
     * Toggle submenu status
     */
    public function actionToggleSubmenuStatus()
    {
        header('Content-Type: application/json');

        $id = $this->input->post('id', true);
        $status = $this->input->post('status', true); // 1 or 0

        if (empty($id)) {
            echo json_encode(['status' => 'error', 'pesan' => 'ID Submenu tidak valid']);
            return;
        }

        $this->db->where('id', $id);
        if ($this->db->update('submenu', ['status' => $status])) {
            $this->session->unset_userdata('app_session_cache');
            echo json_encode([
                'status' => 'success', 
                'pesan' => 'Status berhasil diubah',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]);
        } else {
            echo json_encode([
                'status' => 'error', 
                'pesan' => 'Gagal mengubah status',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]);
        }
    }

    /**
     * Toggle parent menu status
     */
    public function actionToggleMenuStatus()
    {
        header('Content-Type: application/json');

        $id = $this->input->post('id', true);
        $status = $this->input->post('status', true); // 1 or 0

        if (empty($id)) {
            echo json_encode(['status' => 'error', 'pesan' => 'ID Menu tidak valid']);
            return;
        }

        $this->db->where('id', $id);
        if ($this->db->update('menu', ['status' => $status])) {
            $this->session->unset_userdata('app_session_cache');
            echo json_encode([
                'status' => 'success', 
                'pesan' => 'Status Menu berhasil diubah',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]);
        } else {
            echo json_encode([
                'status' => 'error', 
                'pesan' => 'Gagal mengubah status menu',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]);
        }
    }

    /**
     * Bulk Toggle Menu Status
     */
    public function actionBulkToggleMenuStatus()
    {
        header('Content-Type: application/json');

        $ids = $this->input->post('ids', true);
        $status = $this->input->post('status', true);

        if (empty($ids) || !is_array($ids)) {
            echo json_encode(['status' => 'error', 'pesan' => 'Pilih menu terlebih dahulu']);
            return;
        }

        $this->db->where_in('id', $ids);
        if ($this->db->update('menu', ['status' => $status])) {
            $this->session->unset_userdata('app_session_cache');
            echo json_encode(['status' => 'success', 'pesan' => count($ids) . ' Menu berhasil diperbarui']);
        } else {
            echo json_encode(['status' => 'error', 'pesan' => 'Gagal memperbarui status secara massal']);
        }
    }

    // ==================== HELPER FUNCTIONS ====================

    /**
     * Get total submenu count
     */
    private function getTotalSubmenu()
    {
        return $this->db->count_all('submenu');
    }

    /**
     * Get active menu count
     */
    private function getActiveMenuCount()
    {
        $this->db->where('status', 1);
        $count = $this->db->count_all_results('menu');
        $this->db->reset_query();
        return $count;
    }

    /**
     * Get inactive menu count
     */
    private function getInactiveMenuCount()
    {
        $this->db->where('status', 0);
        $count = $this->db->count_all_results('menu');
        $this->db->reset_query();
        return $count;
    }

    /**
     * Get active submenu count for specific menu
     */
    private function getActiveSubmenuCount($menu_id)
    {
        $this->db->where('id_menu', $menu_id);
        $this->db->where('status', 1);
        return $this->db->count_all_results('submenu');
    }

    /**
     * Get inactive submenu count for specific menu
     */
    private function getInactiveSubmenuCount($menu_id)
    {
        $this->db->where('id_menu', $menu_id);
        $this->db->where('status', 0);
        return $this->db->count_all_results('submenu');
    }

    /**
     * Get total customer count
     */
    private function getTotalKonsumen()
    {
        return $this->db->count_all('tbl_konsumen');
    }

    /**
     * Get active customer count
     */
    private function getActiveKonsumenCount()
    {
        // Assuming there's a status field in konsumen table
        $this->db->where('status', 1);
        return $this->db->count_all_results('tbl_konsumen');
    }

    /**
     * Get recent customer activities
     */
    private function getRecentActivities()
    {
        // This would depend on your transaction structure
        // For now, returning empty array
        return [];
    }

    // ==================== LEGACY FUNCTIONS (DEPRECATED) ====================

    /**
     * @deprecated Use actionAddMenu() instead
     */
    function actionAdd()
    {
        $this->session->set_flashdata('error', 'Method ini sudah tidak digunakan. Gunakan actionAddMenu()');
        redirect('menu');
    }

    /**
     * Save menu (form submission)
     */
    public function aksi_simpan()
    {
        $nama_menu = $this->input->post('nama_menu', true);
        $alias = $this->input->post('alias', true);
        $icon = $this->input->post('icon', true);
        $link = $this->input->post('link', true);
        $urutan = $this->input->post('urutan', true);
        $access_levels = $this->input->post('access_levels', true);

        // Validation
        if (empty($nama_menu) || empty($alias) || empty($icon) || empty($link)) {
            $this->session->set_flashdata('error', 'Semua field wajib diisi');
            redirect('menu');
            return;
        }

        if (empty($access_levels)) {
            $this->session->set_flashdata('error', 'Minimal satu level user harus dipilih');
            redirect('menu');
            return;
        }

        // Check if menu name already exists
        $this->db->where('nama_menu', $nama_menu);
        if ($this->db->count_all_results('menu') > 0) {
            $this->session->set_flashdata('error', 'Nama menu sudah digunakan');
            redirect('menu');
            return;
        }

        // Use first level as legacy fallback
        $main_level = isset($access_levels[0]) ? $access_levels[0] : 0;

        $data = [
            'nama_menu' => $nama_menu,
            'alias' => $alias,
            'icon' => $icon,
            'icon_fa' => $icon, // For compatibility
            'icon_menu' => $icon,
            'link' => $link,
            'urutan' => $urutan ?: 0,
            // 'level' => $main_level, // [DEPRECATED] Column dropped
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $insert = $this->db->insert('menu', $data);

        if ($insert) {
            $menu_id = $this->db->insert_id();
            $this->_syncMenuAccess($menu_id, $access_levels);
            $this->session->set_flashdata('success', 'Menu berhasil ditambahkan');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambahkan menu');
        }

        redirect('menu');
    }

    /**
     * Update menu (form submission)
     */
    public function aksi_update()
    {
        // Check if AJAX request
        $is_ajax = $this->input->is_ajax_request();

        $id = $this->input->post('id', true);
        $nama_menu = $this->input->post('nama_menu', true);
        $alias = $this->input->post('alias', true);
        $icon = $this->input->post('icon', true);
        $link = $this->input->post('link', true);
        $urutan = $this->input->post('urutan', true);
        $access_levels = $this->input->post('access_levels', true);

        // Validation
        if (empty($id) || empty($nama_menu) || empty($alias) || empty($icon) || empty($link)) {
            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi']);
                return;
            }
            $this->session->set_flashdata('error', 'Semua field wajib diisi');
            redirect('menu');
            return;
        }

        // Check if menu exists
        $existing_menu = $this->db->get_where('menu', ['id' => $id])->row();
        if (!$existing_menu) {
            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Menu tidak ditemukan']);
                return;
            }
            $this->session->set_flashdata('error', 'Menu tidak ditemukan');
            redirect('menu');
            return;
        }

        // Check if menu name already exists (excluding current menu)
        $this->db->where('nama_menu', $nama_menu);
        $this->db->where('id !=', $id);
        if ($this->db->count_all_results('menu') > 0) {
            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Nama menu sudah digunakan']);
                return;
            }
            $this->session->set_flashdata('error', 'Nama menu sudah digunakan');
            redirect('menu');
            return;
        }

        // Use first level as legacy fallback
        $main_level = isset($access_levels[0]) ? $access_levels[0] : 0;

        $data = [
            'nama_menu' => $nama_menu,
            'alias' => $alias,
            'icon' => $icon,
            'icon_fa' => $icon, // For compatibility
            'icon_menu' => $icon,
            'link' => $link,
            'urutan' => $urutan ?: 0,
            // 'level' => $main_level, // [DEPRECATED] Column dropped
            'updated_at' => date('Y-m-d H:i:s')
        ];

        /*
        // Add level if provided (allow 0 as valid value)
        if ($level !== null && $level !== '') {
            $data['level'] = $level;
        }
        */

        $this->db->where('id', $id);
        $update = $this->db->update('menu', $data);

        if ($update) {
            $this->_syncMenuAccess($id, $access_levels);

            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Menu berhasil diperbarui']);
                return;
            }
            $this->session->set_flashdata('success', 'Menu berhasil diperbarui');
        } else {
            $error = $this->db->error();
            $error_msg = isset($error['message']) ? $error['message'] : 'Unknown error';
            log_message('error', 'Menu update error: ' . $error_msg);

            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui menu. Silakan coba lagi.']);
                return;
            }
            $this->session->set_flashdata('error', 'Gagal memperbarui menu. Silakan coba lagi.');
        }

        if (!$is_ajax) {
            redirect('menu');
        }
    }

    /**
     * Delete menu (form submission)
     */
    public function aksi_delete()
    {
        $id = $this->input->post('id', true);

        if (empty($id)) {
            $this->session->set_flashdata('error', 'ID menu tidak ditemukan');
            redirect('menu');
            return;
        }

        // Check if menu has submenus
        $this->db->where('id_menu', $id);
        $has_submenus = $this->db->count_all_results('submenu') > 0;

        if ($has_submenus) {
            $this->session->set_flashdata('error', 'Menu tidak dapat dihapus karena memiliki submenu');
            redirect('menu');
            return;
        }

        $this->db->trans_start();
        $this->db->where('menu_id', $id);
        $this->db->delete('tbl_menu_access');

        $this->db->where('id', $id);
        $this->db->delete('menu');
        $this->db->trans_complete();

        if ($this->db->trans_status() === TRUE) {
            $this->session->set_flashdata('success', 'Menu berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus menu');
        }

        redirect('menu');
    }

    /**
     * Save submenu (form submission - redirect based)
     */
    public function aksi_simpan_submenu()
    {
        $menu_id = $this->input->post('menu_id', true);
        $nama_submenu = $this->input->post('nama_submenu', true);
        $link_submenu = $this->input->post('link_submenu', true);
        $urutan = $this->input->post('urutan', true);

        // Validation
        if (empty($menu_id) || empty($nama_submenu) || empty($link_submenu)) {
            $this->session->set_flashdata('error', 'Menu, nama submenu, dan link submenu wajib diisi');
            redirect('menu/submenu/' . $menu_id);
            return;
        }

        // Check if menu exists
        $menu_exists = $this->db->get_where('menu', ['id' => $menu_id])->row();
        if (!$menu_exists) {
            $this->session->set_flashdata('error', 'Menu tidak ditemukan');
            redirect('menu');
            return;
        }

        // Check if submenu name already exists in the same menu
        $this->db->where('id_menu', $menu_id);
        $this->db->where('nama_submenu', $nama_submenu);
        if ($this->db->count_all_results('submenu') > 0) {
            $this->session->set_flashdata('error', 'Nama submenu sudah digunakan dalam menu ini');
            redirect('menu/submenu/' . $menu_id);
            return;
        }

        $data = [
            'id_menu' => $menu_id,
            'nama_submenu' => $nama_submenu,
            'link_submenu' => $link_submenu,
            'urutan' => $urutan ?: 0,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $insert = $this->db->insert('submenu', $data);
        $submenu_id = $this->db->insert_id(); // Get new ID

        // [NEW] Process Access Levels
        $access_levels = $this->input->post('access_levels', true);
        if (!empty($access_levels) && is_array($access_levels)) {
            foreach ($access_levels as $role_id) {
                // Insert submenu access
                $this->db->insert('tbl_submenu_access', [
                    'submenu_id' => $submenu_id,
                    'role_id' => $role_id
                ]);

                // [AUTO-FIX] Ensure role has access to the parent menu as well
                $has_menu_access = $this->db->where('menu_id', $menu_id)
                    ->where('role_id', $role_id)
                    ->count_all_results('tbl_menu_access');

                if ($has_menu_access == 0) {
                    $this->db->insert('tbl_menu_access', [
                        'menu_id' => $menu_id,
                        'role_id' => $role_id
                    ]);
                }
            }
        }

        // [NEW] Process User Specific Access Levels
        $user_access_levels = $this->input->post('user_access_levels', true);
        if (!empty($user_access_levels) && is_array($user_access_levels)) {
            foreach ($user_access_levels as $user_id) {
                $this->db->insert('tbl_user_submenu_access', [
                    'submenu_id' => $submenu_id,
                    'user_id' => $user_id
                ]);
                
                // [AUTO-FIX] Ensure user has access to the parent menu as well
                $has_menu_access = $this->db->where('menu_id', $menu_id)
                    ->where('user_id', $user_id)
                    ->count_all_results('tbl_user_menu_access');
                    
                if ($has_menu_access == 0) {
                    $this->db->insert('tbl_user_menu_access', [
                        'menu_id' => $menu_id,
                        'user_id' => $user_id
                    ]);
                }
            }
        }

        if ($insert) {
            $this->session->set_flashdata('success', 'Submenu berhasil ditambahkan');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambahkan submenu');
        }

        redirect('menu/submenu/' . $menu_id);
    }

    /**
     * Update submenu (form submission - redirect based)
     */
    public function aksi_edit_submenu()
    {
        $submenu_id = $this->input->post('submenu_id', true);
        $id_menu = $this->input->post('id_menu', true);
        $nama_submenu = trim($this->input->post('nama_submenu', true));
        $link_submenu = trim($this->input->post('link_submenu', true));
        $urutan = $this->input->post('urutan', true);

        // Validation
        if (empty($submenu_id) || !is_numeric($submenu_id)) {
            $this->session->set_flashdata('error', 'ID submenu tidak valid');
            if ($id_menu) {
                redirect('menu/submenu/' . $id_menu);
            } else {
                redirect('menu');
            }
            return;
        }

        if (empty($nama_submenu)) {
            $this->session->set_flashdata('error', 'Nama submenu wajib diisi');
            if ($id_menu) {
                redirect('menu/submenu/' . $id_menu);
            } else {
                redirect('menu');
            }
            return;
        }

        if (empty($link_submenu)) {
            $this->session->set_flashdata('error', 'Link submenu wajib diisi');
            if ($id_menu) {
                redirect('menu/submenu/' . $id_menu);
            } else {
                redirect('menu');
            }
            return;
        }

        // Check if submenu exists
        $existing_submenu = $this->db->get_where('submenu', ['id' => $submenu_id])->row();
        if (!$existing_submenu) {
            $this->session->set_flashdata('error', 'Submenu tidak ditemukan');
            redirect('menu');
            return;
        }

        // Get menu_id from existing submenu if not provided
        $menu_id = $id_menu ? $id_menu : $existing_submenu->id_menu;

        // Check if submenu name already exists in the same menu (excluding current submenu)
        $this->db->where('id_menu', $menu_id);
        $this->db->where('nama_submenu', $nama_submenu);
        $this->db->where('id !=', $submenu_id);
        if ($this->db->count_all_results('submenu') > 0) {
            $this->session->set_flashdata('error', 'Nama submenu sudah digunakan dalam menu ini');
            redirect('menu/submenu/' . $menu_id);
            return;
        }

        $data = [
            'nama_submenu' => $nama_submenu,
            'link_submenu' => $link_submenu,
            'urutan' => $urutan
        ];

        $this->db->where('id', $submenu_id);
        $update = $this->db->update('submenu', $data);

        // [NEW] Process Access Levels
        $access_levels = $this->input->post('access_levels', true);

        // 1. Clear existing access
        $this->db->delete('tbl_submenu_access', ['submenu_id' => $submenu_id]);

        // 2. Insert new access
        if (!empty($access_levels) && is_array($access_levels)) {
            foreach ($access_levels as $role_id) {
                // Insert submenu access
                $this->db->insert('tbl_submenu_access', [
                    'submenu_id' => $submenu_id,
                    'role_id' => $role_id
                ]);

                // [AUTO-FIX] Ensure role has access to the parent menu as well
                $has_menu_access = $this->db->where('menu_id', $menu_id)
                    ->where('role_id', $role_id)
                    ->count_all_results('tbl_menu_access');

                if ($has_menu_access == 0) {
                    $this->db->insert('tbl_menu_access', [
                        'menu_id' => $menu_id,
                        'role_id' => $role_id
                    ]);
                }
            }
        }

        // [NEW] Process User Specific Access Levels
        $user_access_levels = $this->input->post('user_access_levels', true);
        
        // 1. Clear existing user access
        $this->db->delete('tbl_user_submenu_access', ['submenu_id' => $submenu_id]);
        
        // 2. Insert new user access
        if (!empty($user_access_levels) && is_array($user_access_levels)) {
            foreach ($user_access_levels as $user_id) {
                $this->db->insert('tbl_user_submenu_access', [
                    'submenu_id' => $submenu_id,
                    'user_id' => $user_id
                ]);
                
                // [AUTO-FIX] Ensure user has access to the parent menu as well
                $has_menu_access = $this->db->where('menu_id', $menu_id)
                    ->where('user_id', $user_id)
                    ->count_all_results('tbl_user_menu_access');
                    
                if ($has_menu_access == 0) {
                    $this->db->insert('tbl_user_menu_access', [
                        'menu_id' => $menu_id,
                        'user_id' => $user_id
                    ]);
                }
            }
        }

        if ($update) {
            $this->session->set_flashdata('success', 'Submenu berhasil diperbarui');
        } else {
            $db_error = $this->db->error();
            $error_msg = 'Gagal memperbarui submenu';
            if (!empty($db_error['message'])) {
                log_message('error', 'Update submenu error: ' . $db_error['message']);
            }
            $this->session->set_flashdata('error', $error_msg);
        }

        redirect('menu/submenu/' . $menu_id);
    }

    /**
     * Delete submenu (GET request - redirect based)
     */
    public function delete_submenu($id)
    {
        if (empty($id)) {
            $this->session->set_flashdata('error', 'ID submenu tidak ditemukan');
            redirect('menu');
            return;
        }

        // Check if submenu exists
        $submenu = $this->db->get_where('submenu', ['id' => $id])->row();
        if (!$submenu) {
            $this->session->set_flashdata('error', 'Submenu tidak ditemukan');
            redirect('menu');
            return;
        }

        $menu_id = $submenu->id_menu;

        $this->db->where('id', $id);
        $delete = $this->db->delete('submenu');

        if ($delete) {
            $this->session->set_flashdata('success', 'Submenu berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus submenu');
        }

        redirect('menu/submenu/' . $menu_id);
    }
    // ==================== SUBMENU ACCESS MANAGEMENT ====================

    /**
     * Get submenu access data (roles and checked state)
     */
    public function getSubmenuAccess($submenu_id)
    {
        header('Content-Type: application/json');

        if (empty($submenu_id)) {
            echo json_encode(['status' => 'error', 'message' => 'ID Submenu required']);
            return;
        }

        // 1. Get all roles
        $this->db->order_by('role_id', 'ASC');
        $roles = $this->db->get('roles')->result();

        // 2. Get existing access for this submenu
        $existing_access = $this->db->get_where('tbl_submenu_access', ['submenu_id' => $submenu_id])->result();
        $access_role_ids = array_column($existing_access, 'role_id');

        // 3. Build response
        $access_data = [];
        foreach ($roles as $role) {
            $access_data[] = [
                'role_id' => $role->role_id,
                'role_name' => $role->role_name,
                'has_access' => in_array($role->role_id, $access_role_ids)
            ];
        }

        echo json_encode([
            'status' => 'success',
            'data' => $access_data
        ]);
    }

    /**
     * Update submenu access permissions
     */
    public function updateSubmenuAccess()
    {
        header('Content-Type: application/json');

        $submenu_id = $this->input->post('submenu_id', true);
        $roles = $this->input->post('roles', true); // Array of role_ids

        if (empty($submenu_id)) {
            echo json_encode(['status' => 'error', 'message' => 'ID Submenu required']);
            return;
        }

        // Start transaction
        $this->db->trans_start();

        // 1. Clear existing access
        $this->db->where('submenu_id', $submenu_id);
        $this->db->delete('tbl_submenu_access');

        // 2. Insert new access
        if (!empty($roles) && is_array($roles)) {
            foreach ($roles as $role_id) {
                $this->db->insert('tbl_submenu_access', [
                    'submenu_id' => $submenu_id,
                    'role_id' => $role_id
                ]);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === TRUE) {
            echo json_encode(['status' => 'success', 'message' => 'Hak akses submenu berhasil diperbarui']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui hak akses']);
        }
    }
    /**
     * Helper to fix orphaned permissions
     * Grants parent menu access if submenu access exists
     */
    public function fix_permissions()
    {
        // Get all submenu access rules
        // Join with submenu table to get the parent menu_id
        $this->db->select('tbl_submenu_access.role_id, submenu.id_menu');
        $this->db->from('tbl_submenu_access');
        $this->db->join('submenu', 'submenu.id = tbl_submenu_access.submenu_id');
        $query = $this->db->get();
        $submenu_access_list = $query->result();

        $count = 0;
        foreach ($submenu_access_list as $access) {
            // Check if parent menu access exists
            $check = $this->db->get_where('tbl_menu_access', [
                'menu_id' => $access->id_menu,
                'role_id' => $access->role_id
            ])->row();

            if (!$check) {
                // Insert missing access
                $this->db->insert('tbl_menu_access', [
                    'menu_id' => $access->id_menu,
                    'role_id' => $access->role_id
                ]);
                $count++;
            }
        }

        echo "<h2>Perbaikan Selesai</h2>";
        echo "<p>" . $count . " hak akses menu induk berhasil diperbaiki / ditambahkan.</p>";
        echo "<p>Silakan coba login kembali atau refresh halaman sidebar.</p>";
        echo "<a href='" . base_url('dashboard') . "'>Kembali ke Dashboard</a>";
    }
    /**
     * Helper to sync menu access and propagate to submenus
     */
    private function _syncMenuAccess($menu_id, $access_levels)
    {
        // Deteksi Kolom secara Otomatis
        $col_role = $this->db->field_exists('role_id', 'tbl_menu_access') ? 'role_id' : 'level_id';
        $col_menu = $this->db->field_exists('menu_id', 'tbl_menu_access') ? 'menu_id' : 'id_menu';

        $col_role_sub = $this->db->field_exists('role_id', 'tbl_submenu_access') ? 'role_id' : 'level_id';
        $col_sub = 'submenu_id'; // Biasanya tetap

        // 1. Bersihkan Data Lama
        $this->db->where($col_menu, (int) $menu_id);
        $this->db->delete('tbl_menu_access');

        // 2. Bersihkan Submenu
        $submenus = $this->db->get_where('submenu', ['id_menu' => (int) $menu_id])->result();
        $submenu_ids = array_column($submenus, 'id');
        if (!empty($submenu_ids)) {
            $this->db->where_in($col_sub, $submenu_ids);
            $this->db->delete('tbl_submenu_access');
        }

        // 3. Input Paksa
        if (!empty($access_levels) && is_array($access_levels)) {
            foreach ($access_levels as $lvl) {
                if ($lvl === null || $lvl === '')
                    continue;

                // Insert Menu Utama
                $this->db->insert('tbl_menu_access', [
                    $col_menu => (int) $menu_id,
                    $col_role => (int) $lvl
                ]);

                // Insert Submenu
                if (!empty($submenu_ids)) {
                    foreach ($submenu_ids as $sid) {
                        $this->db->insert('tbl_submenu_access', [
                            $col_sub => (int) $sid,
                            $col_role_sub => (int) $lvl
                        ]);
                    }
                }
            }
        }

        $this->session->unset_userdata('app_session_cache');
        return true;
    }

    /**
     * Helper to propagate access to submenus
     * (Deprecated: functionality moved into _syncMenuAccess for atomic sync)
     */
    private function _syncSubmenuAccess($menu_id, $submenu_id = null, $role_id = null)
    {
        if ($submenu_id && $role_id) {
            $exists = $this->db->get_where('tbl_submenu_access', ['submenu_id' => $submenu_id, 'role_id' => $role_id])->row();
            if (!$exists) {
                $this->db->insert('tbl_submenu_access', ['submenu_id' => $submenu_id, 'role_id' => $role_id]);
            }
        }
    }
}
?>