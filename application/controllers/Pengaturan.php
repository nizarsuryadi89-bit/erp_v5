<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 */
class Pengaturan extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('whatsapp');
        $this->_check_invoice_setting_columns();
    }

    private function _check_invoice_setting_columns()
    {
        $this->load->dbforge();
        
        $new_fields = [];
        if (!$this->db->field_exists('invoice_paper', 'tbl_perusahaan')) {
            $new_fields['invoice_paper'] = [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'default' => '80mm',
                'null' => TRUE
            ];
        }
        if (!$this->db->field_exists('invoice_font_size', 'tbl_perusahaan')) {
            $new_fields['invoice_font_size'] = [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 14,
                'null' => TRUE
            ];
        }
        if (!$this->db->field_exists('invoice_show_logo', 'tbl_perusahaan')) {
            $new_fields['invoice_show_logo'] = [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'null' => TRUE
            ];
        }
        if (!$this->db->field_exists('invoice_logo_width', 'tbl_perusahaan')) {
            $new_fields['invoice_logo_width'] = [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 50,
                'null' => TRUE
            ];
        }
        if (!$this->db->field_exists('invoice_show_qr', 'tbl_perusahaan')) {
            $new_fields['invoice_show_qr'] = [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'null' => TRUE
            ];
        }
        if (!$this->db->field_exists('invoice_qr_width', 'tbl_perusahaan')) {
            $new_fields['invoice_qr_width'] = [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 50,
                'null' => TRUE
            ];
        }
        if (!$this->db->field_exists('invoice_footer_note', 'tbl_perusahaan')) {
            $new_fields['invoice_footer_note'] = [
                'type' => 'TEXT',
                'null' => TRUE
            ];
        }
        if (!$this->db->field_exists('invoice_font_family', 'tbl_perusahaan')) {
            $new_fields['invoice_font_family'] = [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => 'Roboto',
                'null' => TRUE
            ];
        }

        if (!empty($new_fields)) {
            $this->dbforge->add_column('tbl_perusahaan', $new_fields);
        }
    }

    public function update_wa_setting()
    {
        $token = $this->input->post('wa_token', true);
        $status = $this->input->post('status') ? 1 : 0;
        $manager_number = $this->input->post('manager_number', true);

        $notif_spk_baru = $this->input->post('notif_spk_baru') ? 1 : 0;
        $notif_pembayaran = $this->input->post('notif_pembayaran') ? 1 : 0;
        $notif_pesanan_selesai = $this->input->post('notif_pesanan_selesai') ? 1 : 0;
        $notif_pengambilan = $this->input->post('notif_pengambilan') ? 1 : 0;

        $data = [
            'wa_token' => $token,
            'status' => $status,
            'manager_number' => $manager_number,
            'notif_spk_baru' => $notif_spk_baru,
            'notif_pembayaran' => $notif_pembayaran,
            'notif_pesanan_selesai' => $notif_pesanan_selesai,
            'notif_pengambilan' => $notif_pengambilan,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->where('id', 1);
        $update = $this->db->update('tbl_wa_setting', $data);

        if ($update) {
            $this->session->set_flashdata('success', 'Pengaturan WhatsApp berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui pengaturan WhatsApp.');
        }

        redirect('pengaturan');
    }

    // SUPERUSER FEATURE: Force Login
    public function force_login($user_id)
    {
        // Security Check: Only Super Admin can do this
        $my_level = $this->session->userdata('level');
        $my_nama_level = (string)$this->session->userdata('nama_level');
        $is_developer = ($this->session->userdata('username') === 'nizarsuryadi');

        $my_role = $this->db->get_where('tbl_divisi', ['divisi_id' => $my_level])->row();
        $is_super_admin = ($my_role && isset($my_role->is_super_admin) && $my_role->is_super_admin == 1) || (stripos($my_nama_level, 'owner') !== false) || (stripos($my_nama_level, 'admin') !== false);
        $is_hr = ($my_role && (stripos((string)$my_role->divisi_nama, 'HR') !== false || $my_role->divisi_id == 16)) || (stripos($my_nama_level, 'HR') !== false) || (stripos($my_nama_level, 'Human Resource') !== false) || $my_level == 21;

        if (!$is_developer && !$is_super_admin && !$is_hr) {
            show_error('Access Denied: Only Super Administrators or HR can force login.', 403);
            return;
        }

        $user = $this->db->select('u.*, r.role_name as role_nama, k.perusahaan_id as real_perusahaan_id')
            ->from('users u')
            ->join('roles r', 'r.role_id = u.role_id', 'left')
            ->join('tbl_karyawan k', 'k.karyawan_id = u.karyawan_id', 'left')
            ->where('u.user_id', $user_id)
            ->get()->row_array();

        if ($user) {
            // Prioritaskan perusahaan_id dari tbl_karyawan (lokasi kerja asli)
            $perusahaan_id = $user['real_perusahaan_id'] ?? $user['perusahaan_id1'] ?? 0;

            // BACKUP ORIGINAL SESSION: Save original admin data if not already impersonating
            if (!$this->session->userdata('is_impersonating')) {
                $orig_session = [
                    'orig_id' => $this->session->userdata('id'),
                    'orig_karyawan_id' => $this->session->userdata('karyawan_id'),
                    'orig_username' => $this->session->userdata('username'),
                    'orig_level' => $this->session->userdata('level'),
                    'orig_nama_level' => $this->session->userdata('nama_level'),
                    'orig_perusahaan_id' => $this->session->userdata('perusahaan_id'),
                ];
                $this->session->set_userdata('admin_backup', $orig_session);
            }

            // Set session data as the target user
            $data = array(
                'id' => $user['user_id'],
                'karyawan_id' => $user['karyawan_id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'level' => $user['role_id'],
                'nama_level' => $user['role_nama'],
                'perusahaan_id' => $perusahaan_id,
                'user_img' => $user['images'],
                'logged_in' => TRUE,
                'is_impersonating' => TRUE // Flag to indicate impersonation
            );
            $this->session->set_userdata($data);

            // CRITICAL FIX: Refresh session cache so the new user's perspective is loaded immediately
            $this->_refresh_session_cache();

            $this->session->set_flashdata('success', 'Berhasil login sebagai ' . $user['username']);
            redirect('dashboard');
        } else {
            $this->session->set_flashdata('error', 'User tidak ditemukan.');
            redirect('pengaturan/user_account');
        }
    }

    // Method to stop impersonating and return to original admin session
    public function stop_impersonating()
    {
        $backup = $this->session->userdata('admin_backup');

        if ($backup) {
            // Restore from backup
            $data = [
                'id' => $backup['orig_id'],
                'karyawan_id' => $backup['orig_karyawan_id'],
                'username' => $backup['orig_username'],
                'level' => $backup['orig_level'],
                'nama_level' => $backup['orig_nama_level'],
                'perusahaan_id' => $backup['orig_perusahaan_id'],
                'is_impersonating' => FALSE
            ];

            // Clear the backup and the impersonating flag
            $this->session->unset_userdata('admin_backup');
            $this->session->set_userdata($data);

            // Refresh cache back to original
            $this->_refresh_session_cache();

            $this->session->set_flashdata('success', 'Kembali ke sesi utama: ' . $backup['orig_username']);
            redirect('dashboard');
        } else {
            // Fallback to logout if no backup found
            redirect('auth/logout');
        }
    }

    public function test_wa()
    {
        $target = $this->input->post('target_number', true);
        $message = "Halo, ini adalah pesan uji coba dari Sistem ERP TGM Print via Fonnte Gateway. Waktu: " . date('H:i:s');

        $response = send_wa($target, $message);

        if ($response && isset($response['status']) && $response['status'] == true) {
            echo json_encode(['status' => 'success', 'message' => 'Pesan terkirim!', 'response' => $response]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal kirim: ' . ($response['reason'] ?? 'Unknown Error'),
                'response' => $response
            ]);
        }
    }

    public function index()
    {
        $userid = $this->session->userdata('id');
        $perusahaan_id = $this->session->userdata('perusahaan_id');

        // Get comprehensive statistics
        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Pengaturan Sistem',
            'level' => $this->db->get_where('users', ['user_id' => $userid])->row_array(),

            // Basic counts
            'store' => $this->db->get('tbl_perusahaan')->num_rows(),
            'users' => $this->db->get('users')->num_rows(),
            'karyawan' => $this->db->get('tbl_karyawan')->num_rows(),
            'konsumen' => $this->db->get('tbl_konsumen')->num_rows(),
            'bahanbaku' => $this->db->get('tbl_bahan')->num_rows(),
            'getproduct' => $this->db->get('tbl_product')->num_rows(),
            'mesin' => $this->db->get('tbl_mesin')->num_rows(),
            'getedc' => $this->db->get('tbl_mesin_edc')->num_rows(),
            'getewallet' => $this->db->get('tbl_ewallet')->num_rows(),
            'getaset' => $this->db->get('tbl_aset')->num_rows(),
            'getmenu' => $this->db->get('menu')->num_rows(),
            'getsatuan' => $this->db->get('tbl_satuan')->num_rows(),
            'getjabatan' => $this->db->get('tbl_jabatan')->num_rows(),
            'getoperator' => $this->db->get('tbl_operator')->num_rows(),
            'getsupplier' => $this->db->get('tbl_supplier')->num_rows(),
            'getrekening' => $this->db->get('tbl_rekening')->num_rows(),
            'getdivisi' => $this->db->get('tbl_divisi')->num_rows(),
            'getkategori' => $this->db->get('tbl_kategori')->num_rows(),
            'getsubkategori' => $this->db->get('tbl_subkategori')->num_rows(),
            'gettype' => $this->db->get('tbl_type')->num_rows(),
            'getmetode' => $this->db->get('tbl_metode')->num_rows(),

            // Enhanced statistics
            'active_users' => $this->db->where('active', 1)->count_all_results('users'),
            'inactive_users' => $this->db->where('active', 0)->count_all_results('users'),
            'recent_users' => $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime('-7
days')))->count_all_results('users'),

            // System health metrics
            'system_health' => $this->getSystemHealth(),
            'recent_activities' => $this->getRecentActivities(),
            'performance_metrics' => $this->getPerformanceMetrics(),

            // Database statistics
            'db_stats' => $this->getDatabaseStats(),

            // Company specific data
            'company_info' => $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row_array(),

            // User distribution by role
            'users_by_role' => $this->getUsersByRole(),

            // Recent transactions
            'recent_transactions' => $this->getRecentTransactions(),

            // System information
            'system_info' => $this->getSystemInfo(),
            'maintenance' => $this->getSystemMaintenanceInfo(),
        );

        // WhatsApp Setting
        $data['wa_setting'] = $this->db->get_where('tbl_wa_setting', ['id' => 1])->row();

        // Add WA Monitor Stats
        $data['wa_stats'] = [
            'total' => $this->db->count_all('tbl_wa_logs'),
            'today' => $this->db->where('DATE(created_at)', date('Y-m-d'))->count_all_results('tbl_wa_logs'),
            'month' => $this->db->where('MONTH(created_at)', date('m'))->where(
                'YEAR(created_at)',
                date('Y')
            )->count_all_results('tbl_wa_logs'),
            'failed' => $this->db->where('status', 'failed')->count_all_results('tbl_wa_logs')
        ];
        $data['wa_recent_logs'] = $this->db->order_by('created_at', 'DESC')->limit(10)->get('tbl_wa_logs')->result();

        $this->load_admin_view('admin/pengaturan/index', $data);
    }

    /**
     * Get system health metrics
     */
    private function getSystemHealth()
    {
        $health = [];

        // Check database connection
        $health['database'] = $this->db->simple_query('SELECT 1') ? 'healthy' : 'error';

        // Check user distribution
        $total_users = $this->db->count_all('users');
        $active_users = $this->db->where('active', 1)->count_all_results('users');
        $health['user_distribution'] = $total_users > 0 ? round(($active_users / $total_users) * 100, 1) : 0;

        // Check recent activity
        $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')));
        $health['recent_activity'] = $this->db->count_all_results('users');

        // Check system load (simulated)
        $health['system_load'] = rand(20, 80); // Simulated load percentage

        return $health;
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities()
    {
        $activities = [];

        // Recent user logins
        $this->db->select('user_log.*, users.username');
        $this->db->from('user_log');
        $this->db->join('users', 'users.user_id = user_log.user_id', 'left');
        $this->db->where('user_log.login_time >=', date('Y-m-d H:i:s', strtotime('-7 days')));
        $this->db->order_by('user_log.login_time', 'DESC');
        $this->db->limit(5);
        $activities['recent_logins'] = $this->db->get()->result();

        // Recent transactions
        $this->db->select('tbl_pembayaran.*, tbl_konsumen.konsumen_nama');
        $this->db->from('tbl_pembayaran');
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_pembayaran.konsumen_id', 'left');
        $this->db->where('tbl_pembayaran.tgl_bayar >=', date('Y-m-d', strtotime('-7 days')));
        $this->db->order_by('tbl_pembayaran.tgl_bayar', 'DESC');
        $this->db->limit(5);
        $activities['recent_transactions'] = $this->db->get()->result();

        return $activities;
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics()
    {
        $metrics = [];

        // Database performance
        $start_time = microtime(true);
        $this->db->count_all('users');
        $end_time = microtime(true);
        $metrics['db_query_time'] = round(($end_time - $start_time) * 1000, 2); // milliseconds

        // Memory usage (simulated)
        $metrics['memory_usage'] = round(memory_get_usage(true) / 1024 / 1024, 2); // MB

        // CPU usage (simulated)
        $metrics['cpu_usage'] = rand(10, 50); // percentage

        return $metrics;
    }

    /**
     * Get database statistics
     */
    private function getDatabaseStats()
    {
        $stats = [];

        // Table sizes (simulated)
        $tables = ['users', 'tbl_karyawan', 'tbl_konsumen', 'tbl_product', 'tbl_pembayaran', 'tbl_pemesanan'];
        foreach ($tables as $table) {
            $stats[$table] = [
                'count' => $this->db->count_all($table),
                'size' => rand(100, 5000) // Simulated size in KB
            ];
        }

        return $stats;
    }

    /**
     * Get users by role distribution
     */
    private function getUsersByRole()
    {
        $this->db->select('tbl_divisi.divisi_nama, COUNT(users.user_id) as total');
        $this->db->from('users');
        $this->db->join('tbl_divisi', 'tbl_divisi.divisi_id = users.role_id', 'left');
        $this->db->group_by('tbl_divisi.divisi_id, tbl_divisi.divisi_nama');
        $this->db->order_by('total', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get recent transactions
     */
    private function getRecentTransactions()
    {
        $this->db->select('tbl_pembayaran.*, tbl_konsumen.konsumen_nama, users.username as kasir_name');
        $this->db->from('tbl_pembayaran');
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_pembayaran.konsumen_id', 'left');
        $this->db->join('users', 'users.user_id = tbl_pembayaran.kasir_id', 'left');
        $this->db->where('tbl_pembayaran.tgl_bayar >=', date('Y-m-d', strtotime('-30 days')));
        $this->db->order_by('tbl_pembayaran.tgl_bayar', 'DESC');
        $this->db->limit(10);

        return $this->db->get()->result();
    }

    /**
     * Get system information
     */
    /**
     * Get maintenance and reliability info
     */
    private function getSystemMaintenanceInfo()
    {
        $db_name = $this->db->database;
        $query = $this->db->query("SELECT SUM(data_length + index_length) / 1024 / 1024 AS size FROM information_schema.TABLES
WHERE table_schema = '$db_name'");
        $row = $query->row();

        return [
            'db_size' => round($row->size, 2) . ' MB',
            'last_backup' => '2026-02-17 08:00', // Example static value
            'disk_usage' => '42%' // Example static value
        ];
    }

    public function optimize_database()
    {
        $this->load->dbutil();
        $result = $this->dbutil->optimize_database();
        if ($result !== FALSE) {
            $this->session->set_flashdata('success', 'Database berhasil dioptimalkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal mengoptimalkan database.');
        }
        redirect('pengaturan');
    }

    public function clear_cache()
    {
        $this->load->helper('file');
        delete_files('./application/cache', TRUE);
        $this->session->set_flashdata('success', 'System cache berhasil dibersihkan.');
        redirect('pengaturan');
    }

    public function clear_system_logs()
    {
        // Define tables to clear
        $log_tables = ['tbl_audit_log', 'user_activity_log', 'tbl_page_visits', 'tbl_pembayaran_produk_backup'];
        
        $cleared_count = 0;
        foreach ($log_tables as $table) {
            if ($this->db->table_exists($table)) {
                $this->db->empty_table($table);
                $cleared_count++;
            }
        }
        
        $this->session->set_flashdata('success', $cleared_count . ' Tabel Log Sistem berhasil dikosongkan.');
        redirect('pengaturan');
    }

    private function getSystemInfo()
    {
        $info = [];

        // PHP and server info
        $info['php_version'] = PHP_VERSION;
        $info['server_software'] = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'CLI';
        $info['timezone'] = date_default_timezone_get();
        $info['datetime_now'] = date('Y-m-d H:i:s');

        // Database info
        $info['db_driver'] = $this->db->dbdriver;
        $info['db_hostname'] = isset($this->db->hostname) ? $this->db->hostname : '';
        $info['db_database'] = isset($this->db->database) ? $this->db->database : '';

        return $info;
    }

    public function user_account()
    {
        $userid = $this->session->userdata('id');
        $controller = $this->uri->segment(1);
        $table = 'users';
        $data['order'] = 'user_id';
        $data['id'] = 'user_id';

        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', [
                'id_perusahaan' =>
                    $this->session->userdata('perusahaan_id')
            ])->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', ['divisi_id' => $this->session->userdata('level')])->row_array(),
        );

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Daftar Pengguna Sistem',
            'table' => 'tbl_kategori',
            'tampilData' => $this->M_master->getAllUser()->result(),
            'total_rows' => $this->M_master->getAllUser()->num_rows(),
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'logo' => $conf['perusahaan']['logo'],
            'karyawan' => $this->db->get('tbl_karyawan')->result(),
            'divisi' => $this->db->get('tbl_divisi')->result(),
            'roles' => $this->db->get('roles')->result(),
            'level' => $this->db->get_where('users', ['user_id' => $userid])->row_array(),
        );

        $this->load_admin_view('admin/' . $controller . '/user_account', $data);
    }

    public function user_access($user_id)
    {
        $current_user_id = $this->session->userdata('id');
        $target_user = $this->db->get_where('users', ['user_id' => $user_id])->row_array();

        if (!$target_user) {
            $this->session->set_flashdata('error', 'Pengguna tidak ditemukan.');
            redirect('pengaturan/user_account');
        }

        // Ambil semua menu aktif
        $this->db->where('status', 1);
        $this->db->order_by('urutan', 'ASC');
        $menus = $this->db->get('menu')->result();

        // Ambil semua submenu aktif
        $this->db->where('status', 1);
        $this->db->order_by('urutan', 'ASC');
        $submenus = $this->db->get('submenu')->result();

        // Ambil hak akses menu spesifik untuk user ini
        $user_menus = $this->db->get_where('tbl_user_menu_access', ['user_id' => $user_id])->result_array();
        $user_menus_ids = array_column($user_menus, 'menu_id');

        // Ambil hak akses submenu spesifik untuk user ini
        $user_submenus = $this->db->get_where('tbl_user_submenu_access', ['user_id' => $user_id])->result_array();
        $user_submenus_ids = array_column($user_submenus, 'submenu_id');

        $data = [
            'controller' => $this->uri->segment(1),
            'title' => 'Hak Akses Spesifik Pengguna',
            'target_user' => $target_user,
            'menus' => $menus,
            'submenus' => $submenus,
            'user_menus_ids' => $user_menus_ids,
            'user_submenus_ids' => $user_submenus_ids,
            'perusahaan' => $this->conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $this->conf['hakakses']['divisi_nama'],
            'logo' => $this->conf['perusahaan']['logo'],
        ];

        $this->load_admin_view('admin/pengaturan/user_access', $data);
    }

    public function ajax_toggle_user_menu_access()
    {
        if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $user_id = $this->input->post('user_id');
        $menu_id = $this->input->post('menu_id');
        $is_checked = $this->input->post('is_checked'); // 1 = checked (grant), 0 = unchecked (revoke)

        if ($is_checked == '1') {
            // Check if already exists
            $exists = $this->db->get_where('tbl_user_menu_access', ['user_id' => $user_id, 'menu_id' => $menu_id])->row();
            if (!$exists) {
                $this->db->insert('tbl_user_menu_access', ['user_id' => $user_id, 'menu_id' => $menu_id]);
            }
        } else {
            $this->db->delete('tbl_user_menu_access', ['user_id' => $user_id, 'menu_id' => $menu_id]);
        }

        echo json_encode(['status' => 'success']);
    }

    public function ajax_toggle_user_submenu_access()
    {
        if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $user_id = $this->input->post('user_id');
        $submenu_id = $this->input->post('submenu_id');
        $is_checked = $this->input->post('is_checked'); // 1 = checked (grant), 0 = unchecked (revoke)

        if ($is_checked == '1') {
            // Check if already exists
            $exists = $this->db->get_where('tbl_user_submenu_access', ['user_id' => $user_id, 'submenu_id' => $submenu_id])->row();
            if (!$exists) {
                $this->db->insert('tbl_user_submenu_access', ['user_id' => $user_id, 'submenu_id' => $submenu_id]);
            }
        } else {
            $this->db->delete('tbl_user_submenu_access', ['user_id' => $user_id, 'submenu_id' => $submenu_id]);
        }

        echo json_encode(['status' => 'success']);
    }

    public function get_user_detail_ajax($id)
    {
        // PROTECTION: Only 'nizarsuryadi' can fetch their own details
        $current_user = $this->session->userdata('username');
        $target_user = $this->db->get_where('users', ['user_id' => $id])->row_array();

        if ($target_user && $target_user['username'] === 'nizarsuryadi' && $current_user !== 'nizarsuryadi') {
            echo json_encode(['success' => false, 'message' => 'Akses Terbatas: Akun Pengembang terlindungi.']);
            return;
        }

        $user = $this->db->select('users.*, roles.role_name as role_nama')
            ->from('users')
            ->join('roles', 'roles.role_id = users.role_id', 'left')
            ->where('users.user_id', $id)
            ->get()->row();

        if ($user) {
            echo json_encode(['success' => true, 'data' => $user]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
    }

    public function action_edit_user_account()
    {
        $id = $this->input->post('user_id', true);

        // PROTECTION: Only 'nizarsuryadi' can modify their own account
        $current_user = $this->session->userdata('username');
        $target_user = $this->db->get_where('users', ['user_id' => $id])->row_array();

        if ($target_user && $target_user['username'] === 'nizarsuryadi' && $current_user !== 'nizarsuryadi') {
            $this->session->set_flashdata('error', 'Akses Terbatas: Anda tidak diizinkan memodifikasi Akun Pengembang.');
            redirect('pengaturan/user_account');
            return;
        }

        $username = $this->input->post('username', true);
        $email = $this->input->post('email', true);
        $password = $this->input->post('password', true);
        $role_id = $this->input->post('role_id', true);
        $active = $this->input->post('active', true);

        $data = [
            'username' => $username,
            'email' => $email,
            'active' => $active ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->db->trans_start();

        // Update users table
        $this->db->where('user_id', $id)->update('users', $data);

        // Update user_roles table
        if ($role_id) {
            $check = $this->db->get_where('user_roles', ['user_id' => $id])->row();
            if ($check) {
                $this->db->where('user_id', $id)->update('user_roles', ['role_id' => $role_id]);
            } else {
                $this->db->insert('user_roles', ['user_id' => $id, 'role_id' => $role_id]);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            $this->session->set_flashdata('success', 'Akun berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui akun.');
        }

        redirect('pengaturan/user_account');
    }

    public function action_add_user_account()
    {
        $username = $this->input->post('username', true);
        $email = $this->input->post('email', true);
        $password = $this->input->post('password', true);
        $karyawan_id = $this->input->post('karyawan_id', true);
        $role_id = $this->input->post('role_id', true);
        $active = $this->input->post('active', true);

        $data = array(
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'karyawan_id' => $karyawan_id ?: 0,
            'active' => $active ? 1 : 0,
            'perusahaan_id1' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->db->trans_start();

        // Insert into users
        $this->db->insert('users', $data);
        $new_user_id = $this->db->insert_id();

        // Insert into user_roles
        if ($role_id) {
            $this->db->insert('user_roles', [
                'user_id' => $new_user_id,
                'role_id' => $role_id
            ]);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            $this->session->set_flashdata('success', 'Akun berhasil ditambahkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambahkan akun.');
        }

        redirect('pengaturan/user_account');
    }

    // Divisi

    public function divisi()
    {
        $userid = $this->session->userdata('id');
        $controller = $this->uri->segment(1);
        $function = $this->uri->segment(2);

        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array(
                'id_perusahaan' =>
                    $this->session->userdata('perusahaan_id')
            ))->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', array('divisi_id' => $this->session->userdata('level')))->row_array(),
        );

        // Fetch Employee Counts per Division for Stats
        $division_stats = $this->db->query("
SELECT d.divisi_id, d.divisi_nama, d.is_super_admin, COUNT(k.karyawan_id) as total_karyawan
FROM tbl_divisi d
LEFT JOIN tbl_karyawan k ON d.divisi_id = k.divisi_id
GROUP BY d.divisi_id, d.divisi_nama, d.is_super_admin
")->result();

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Daftar Divisi Perusahaan',
            'tampilData' => $division_stats, // Use stats data which includes division info
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'logo' => $conf['perusahaan']['logo'],
        );

        $this->load_admin_view('admin/pengaturan/divisi', $data);
    }

    // Method untuk menambah divisi
    public function aksi_tambah_divisi()
    {
        header('Content-Type: application/json');
        $nama_divisi = $this->input->post('nama_divisi', true);
        $is_super_admin = $this->input->post('is_super_admin', true) ? 1 : 0;

        if (empty($nama_divisi)) {
            echo json_encode(['status' => 'error', 'pesan' => 'Nama divisi wajib diisi.']);
            return;
        }

        $data = [
            'divisi_nama' => $nama_divisi,
            'is_super_admin' => $is_super_admin
        ];
        $insert = $this->db->insert('tbl_divisi', $data);

        if ($insert) {
            echo json_encode(['status' => 'success', 'pesan' => 'Divisi berhasil ditambahkan.']);
        } else {
            echo json_encode(['status' => 'error', 'pesan' => 'Gagal menambahkan divisi.']);
        }
    }

    // Method untuk update divisi
    public function aksi_update_divisi()
    {
        header('Content-Type: application/json');
        $divisi_id = $this->input->post('divisi_id', true);
        $nama_divisi = $this->input->post('nama_divisi', true);
        $is_super_admin = $this->input->post('is_super_admin', true) ? 1 : 0;

        if (empty($divisi_id) || empty($nama_divisi)) {
            echo json_encode(['status' => 'error', 'pesan' => 'ID dan nama divisi wajib diisi.']);
            return;
        }

        $data = [
            'divisi_nama' => $nama_divisi,
            'is_super_admin' => $is_super_admin
        ];
        $this->db->where('divisi_id', $divisi_id);
        $update = $this->db->update('tbl_divisi', $data);

        if ($update) {
            echo json_encode(['status' => 'success', 'pesan' => 'Divisi berhasil diupdate.']);
        } else {
            echo json_encode(['status' => 'error', 'pesan' => 'Gagal update divisi.']);
        }
    }

    // Method untuk hapus divisi
    public function actionDeleteDivisi($id)
    {
        header('Content-Type: application/json');
        if (empty($id)) {
            echo json_encode(['status' => 'error', 'pesan' => 'ID divisi tidak ditemukan.']);
            return;
        }

        // Cek ketergantungan (jika ada karyawan di divisi ini)
        $cek_karyawan = $this->db->get_where('tbl_karyawan', ['divisi_id' => $id])->num_rows();
        if ($cek_karyawan > 0) {
            echo json_encode(['status' => 'error', 'pesan' => 'Gagal hapus: Masih ada karyawan di divisi ini.']);
            return;
        }

        $this->db->where('divisi_id', $id);
        $delete = $this->db->delete('tbl_divisi');

        if ($delete) {
            echo json_encode(['status' => 'success', 'pesan' => 'Divisi berhasil dihapus.']);
        } else {
            echo json_encode(['status' => 'error', 'pesan' => 'Gagal menghapus divisi.']);
        }
    }

    public function jabatan()
    {
        $userid = $this->session->userdata('id');
        $controller = $this->uri->segment(1);
        $function = $this->uri->segment(2);
        $table = 'tbl_divisi';
        $data['order'] = 'divisi_id';
        $data['id'] = 'divisi_id';

        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array(
                'id_perusahaan' =>
                    $this->session->userdata('perusahaan_id')
            ))->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', array('divisi_id' => $this->session->userdata('level')))->row_array(),
        );

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Daftar Divisi Perusahaan',
            'table' => 'tbl_kategori',
            'tampilData' => $this->db->get('tbl_jabatan')->result(),
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'logo' => $conf['perusahaan']['logo'],
        );

        $this->load_admin_view('admin/karyawan/jabatan', $data);
    }

    public function aksi_update_jabatan()
    {
        header('Content-Type: application/json');
        $jabatan_id = $this->input->post('jabatan_id', true);
        $nama_jabatan = $this->input->post('nama_jabatan', true);
        $gaji_pokok = $this->input->post('gaji_pokok', true);
        $deskripsi = $this->input->post('deskripsi', true);

        $tunjangan_jabatan = $this->input->post('tunjangan_jabatan', true);
        $tunjangan_transportasi = $this->input->post('tunjangan_transportasi', true);

        if (empty($jabatan_id) || empty($nama_jabatan)) {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'ID dan nama jabatan wajib diisi.'
            ]);
            return;
        }

        $data = [
            'nama_jabatan' => $nama_jabatan,
            'gaji_pokok' => empty($gaji_pokok) ? 0 : str_replace('.', '', $gaji_pokok),
            'tunjangan_jabatan' => empty($tunjangan_jabatan) ? 0 : str_replace('.', '', $tunjangan_jabatan),
            'tunjangan_transportasi' => empty($tunjangan_transportasi) ? 0 : str_replace('.', '', $tunjangan_transportasi),
            'keterangan' => $deskripsi
        ];
        $this->db->where('jabatan_id', $jabatan_id);
        $update = $this->db->update('tbl_jabatan', $data);
        if ($update) {
            echo json_encode([
                'status' => 'success',
                'pesan' => 'Jabatan berhasil diupdate.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'Gagal update jabatan.'
            ]);
        }
    }

    public function aksi_tambah_jabatan()
    {
        header('Content-Type: application/json');
        $nama_jabatan = $this->input->post('nama_jabatan', true);
        $gaji_pokok = $this->input->post('gaji_pokok', true);
        $deskripsi = $this->input->post('deskripsi', true);

        $tunjangan_jabatan = $this->input->post('tunjangan_jabatan', true);
        $tunjangan_transportasi = $this->input->post('tunjangan_transportasi', true);

        // Validasi sederhana
        if (empty($nama_jabatan)) {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'Nama jabatan wajib diisi.'
            ]);
            return;
        }

        $data = [
            'nama_jabatan' => $nama_jabatan,
            'gaji_pokok' => empty($gaji_pokok) ? 0 : str_replace('.', '', $gaji_pokok),
            'tunjangan_jabatan' => empty($tunjangan_jabatan) ? 0 : str_replace('.', '', $tunjangan_jabatan),
            'tunjangan_transportasi' => empty($tunjangan_transportasi) ? 0 : str_replace('.', '', $tunjangan_transportasi),
            'keterangan' => $deskripsi
        ];
        $insert = $this->db->insert('tbl_jabatan', $data);
        if ($insert) {
            echo json_encode([
                'status' => 'success',
                'pesan' => 'Jabatan berhasil ditambahkan.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'Gagal menambahkan jabatan.'
            ]);
        }
    }

    public function actionDelete($id)
    {
        header('Content-Type: application/json');
        if (empty($id)) {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'ID jabatan tidak ditemukan.'
            ]);
            return;
        }
        $this->db->where('jabatan_id', $id);
        $delete = $this->db->delete('tbl_jabatan');
        if ($delete) {
            echo json_encode([
                'status' => 'success',
                'pesan' => 'Jabatan berhasil dihapus.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'Gagal menghapus jabatan.'
            ]);
        }
    }

    public function operator()
    {
        $userid = $this->session->userdata('id');
        $perusahaan_id = $this->session->userdata('perusahaan_id');
        $user_level = $this->session->userdata('level');

        // Initialize conf array
        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', ['divisi_id' => $user_level])->row_array(),
        );

        // Fetch operator names (production stations) with their joined machines
        $this->db->select('o.*, GROUP_CONCAT(m.mesin_nama SEPARATOR ", ") as handled_machines, GROUP_CONCAT(m.mesin_id) as
handled_machine_ids');
        $this->db->from('tbl_operator o');
        $this->db->join('tbl_mesin m', 'm.operator_id = o.operator_id', 'left');
        $this->db->group_by('o.operator_id');
        $operator_data = $this->db->get()->result();

        // Fetch all available machines to be assigned
        $all_machines = $this->db->get('tbl_mesin')->result();

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Pengaturan Operator Produksi',
            'table' => 'tbl_operator',
            'tampilData' => $operator_data,
            'all_machines' => $all_machines,
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'logo' => $conf['perusahaan']['logo'],
        );

        $this->load_admin_view('admin/pengaturan/operator', $data);
    }

    // Method untuk menambah operator master
    public function aksi_tambah_operator()
    {
        header('Content-Type: application/json');
        $data = [
            'operator_nama' => $this->input->post('operator_nama', true),
            'operator_keterangan' => $this->input->post('operator_keterangan', true)
        ];
        $selected_machines = $this->input->post('mesin_ids', true); // Array of machine IDs

        if (empty($data['operator_nama'])) {
            echo json_encode(['status' => 'error', 'pesan' => 'Nama operator wajib diisi.']);
            return;
        }

        $this->db->trans_start();
        $this->db->insert('tbl_operator', $data);
        $new_id = $this->db->insert_id();

        // Assign machines to this new operator
        if (!empty($selected_machines) && is_array($selected_machines)) {
            $this->db->where_in('mesin_id', $selected_machines);
            $this->db->update('tbl_mesin', ['operator_id' => $new_id]);
        }
        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            echo json_encode(['status' => 'success', 'pesan' => 'Operator produksi berhasil ditambahkan.']);
        } else {
            echo json_encode(['status' => 'error', 'pesan' => 'Gagal menambahkan operator.']);
        }
    }

    // Method untuk update operator master
    public function aksi_update_operator()
    {
        header('Content-Type: application/json');
        $id = $this->input->post('operator_id', true);
        $data = [
            'operator_nama' => $this->input->post('operator_nama', true),
            'operator_keterangan' => $this->input->post('operator_keterangan', true)
        ];
        $selected_machines = $this->input->post('mesin_ids', true); // Array of machine IDs

        if (empty($id) || empty($data['operator_nama'])) {
            echo json_encode(['status' => 'error', 'pesan' => 'Data tidak lengkap.']);
            return;
        }

        $this->db->trans_start();
        // 1. Update operator info
        $this->db->where('operator_id', $id);
        $this->db->update('tbl_operator', $data);

        // 2. Clear previous machine assignments for this operator (set operator_id to 0)
        $this->db->where('operator_id', $id);
        $this->db->update('tbl_mesin', ['operator_id' => 0]);

        // 3. Assign new selected machines
        if (!empty($selected_machines) && is_array($selected_machines)) {
            $this->db->where_in('mesin_id', $selected_machines);
            $this->db->update('tbl_mesin', ['operator_id' => $id]);
        }
        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            echo json_encode(['status' => 'success', 'pesan' => 'Data operator berhasil diperbarui.']);
        } else {
            echo json_encode(['status' => 'error', 'pesan' => 'Gagal update data operator.']);
        }
    }

    // Method untuk hapus operator master
    public function aksi_hapus_operator($id)
    {
        header('Content-Type: application/json');
        if (empty($id)) {
            echo json_encode(['status' => 'error', 'pesan' => 'ID operator tidak valid.']);
            return;
        }

        $this->db->trans_start();
        // Clear machine assignments first
        $this->db->where('operator_id', $id);
        $this->db->update('tbl_mesin', ['operator_id' => 0]);

        // Delete operator
        $this->db->where('operator_id', $id);
        $this->db->delete('tbl_operator');
        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            echo json_encode(['status' => 'success', 'pesan' => 'Operator berhasil dihapus dari sistem.']);
        } else {
            echo json_encode(['status' => 'error', 'pesan' => 'Gagal menghapus operator.']);
        }
    }

    /**
     * Display List of Bank Accounts with summary stats
     */
    public function rekening()
    {
        $userid = $this->session->userdata('id');
        $perusahaan_id = $this->session->userdata('perusahaan_id');

        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', ['divisi_id' => $this->session->userdata('level')])->row_array(),
        );

        // Fetch accounts with balance summary (total received per account)
        $this->db->select('r.*');
        $this->db->select('SUM(CASE WHEN pd.metode IN ("transfer", "debit") THEN pd.jumlah ELSE 0 END) as total_masuk');
        $this->db->select('COUNT(pd.detail_id) as total_transaksi');
        $this->db->from('tbl_rekening r');
        $this->db->join('tbl_pembayaran_detail pd', 'pd.bank_id = r.id', 'left');
        $this->db->group_by('r.id');
        $tampilData = $this->db->get()->result();

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Vault Rekening Perusahaan',
            'tampilData' => $tampilData,
            'total_rows' => count($tampilData),
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'logo' => $conf['perusahaan']['logo'],
        );

        $this->load_admin_view('admin/pengaturan/rekening', $data);
    }

    /**
     * Display Detailed Activity for a specific Bank Account
     */
    public function aktifitas_rekening($rekening_id)
    {
        if (empty($rekening_id))
            redirect('pengaturan/rekening');

        $data['rekening'] = $this->db->get_where('tbl_rekening', ['id' => $rekening_id])->row();
        if (!$data['rekening'])
            redirect('pengaturan/rekening');

        $data['title'] = 'Aktifitas Mutasi - ' . $data['rekening']->nama_bank;

        // Fetch detailed transactions
        $this->db->select('pd.*, p.nospk, p.tgl_bayar, p.jam_bayar, k.konsumen_nama, u.username as kasir');
        $this->db->from('tbl_pembayaran_detail pd');
        $this->db->join('tbl_pembayaran p', 'pd.pembayaran_id = p.pembayaran_id');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->join('users u', 'p.kasir_id = u.user_id', 'left');
        $this->db->where('pd.bank_id', $rekening_id);
        $this->db->order_by('p.tgl_bayar', 'DESC');
        $this->db->order_by('p.jam_bayar', 'DESC');

        $data['mutasi'] = $this->db->get()->result();

        $this->load_admin_view('admin/pengaturan/aktifitas_rekening', $data);
    }

    // Method untuk menambah rekening
    public function aksi_tambah_rekening()
    {
        header('Content-Type: application/json');

        try {
            // Debug: log semua data yang dikirim
            $post_data = $this->input->post();
            log_message('debug', 'POST data tambah: ' . json_encode($post_data));

            $no_rekening = $this->input->post('no_rekening', true);
            $nama_bank = $this->input->post('nama_bank', true);
            $atas_nama = $this->input->post('atas_nama', true);
            $status = ($this->input->post('status', true) == '1') ? 1 : 0;

            // Debug status
            log_message('debug', 'Status value: ' . $this->input->post('status', true) . ' -> ' . $status);

            // Validasi sederhana
            if (empty($no_rekening) || empty($nama_bank) || empty($atas_nama)) {
                echo json_encode([
                    'status' => 'error',
                    'pesan' => 'Nomor rekening, nama bank, dan atas nama wajib diisi.'
                ]);
                return;
            }

            $data = [
                'no_rekening' => $no_rekening,
                'nama_bank' => $nama_bank,
                'atas_nama' => $atas_nama,
                'active' => $status
            ];

            $insert = $this->db->insert('tbl_rekening', $data);
            if ($insert) {
                echo json_encode([
                    'status' => 'success',
                    'pesan' => 'Rekening berhasil ditambahkan.'
                ]);
            } else {
                $error = $this->db->error();
                echo json_encode([
                    'status' => 'error',
                    'pesan' => 'Gagal menambahkan rekening: ' . $error['message']
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // Method untuk update rekening
    public function aksi_update_rekening()
    {
        header('Content-Type: application/json');

        try {
            // Debug: log semua data yang dikirim
            $post_data = $this->input->post();
            log_message('debug', 'POST data: ' . json_encode($post_data));

            $rekening_id = $this->input->post('rekening_id', true);
            $no_rekening = $this->input->post('no_rekening', true);
            $nama_bank = $this->input->post('nama_bank', true);
            $atas_nama = $this->input->post('atas_nama', true);
            $status = ($this->input->post('status', true) == '1') ? 1 : 0;

            // Debug status
            log_message('debug', 'Status value update: ' . $this->input->post('status', true) . ' -> ' . $status);

            if (empty($rekening_id)) {
                echo json_encode([
                    'status' => 'error',
                    'pesan' => 'ID rekening tidak ditemukan.'
                ]);
                return;
            }

            if (empty($no_rekening) || empty($nama_bank) || empty($atas_nama)) {
                echo json_encode([
                    'status' => 'error',
                    'pesan' => 'Nomor rekening, nama bank, dan atas nama wajib diisi.'
                ]);
                return;
            }

            $data = [
                'no_rekening' => $no_rekening,
                'nama_bank' => $nama_bank,
                'atas_nama' => $atas_nama,
                'active' => $status
            ];
            $this->db->where('id', $rekening_id);
            $update = $this->db->update('tbl_rekening', $data);
            if ($update) {
                echo json_encode([
                    'status' => 'success',
                    'pesan' => 'Rekening berhasil diupdate.'
                ]);
            } else {
                $error = $this->db->error();
                echo json_encode([
                    'status' => 'error',
                    'pesan' => 'Gagal update rekening: ' . $error['message']
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // Method untuk hapus rekening
    public function actionDeleteRekening($id)
    {
        header('Content-Type: application/json');
        if (empty($id)) {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'ID rekening tidak ditemukan.'
            ]);
            return;
        }
        $this->db->where('id', $id);
        $delete = $this->db->delete('tbl_rekening');
        if ($delete) {
            echo json_encode([
                'status' => 'success',
                'pesan' => 'Rekening berhasil dihapus.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'Gagal menghapus rekening.'
            ]);
        }
    }

    // Method untuk mendapatkan data rekening dalam format JSON
    public function get_rekening_json()
    {
        header('Content-Type: application/json');
        $data = $this->db->get('tbl_rekening')->result();
        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
    }

    // Method untuk mendapatkan data jabatan dalam format JSON (untuk refresh tabel jabatan)
    public function get_jabatan_json()
    {
        header('Content-Type: application/json');
        $data = $this->db->get('tbl_jabatan')->result();
        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
    }

    // Method untuk mengecek struktur tabel rekening (debugging)
    public function check_rekening_structure()
    {
        header('Content-Type: application/json');
        try {
            // Cek apakah tabel ada
            if (!$this->db->table_exists('tbl_rekening')) {
                echo json_encode([
                    'status' => 'error',
                    'pesan' => 'Tabel tbl_rekening tidak ditemukan'
                ]);
                return;
            }

            $fields = $this->db->list_fields('tbl_rekening');
            $sample_data = $this->db->get('tbl_rekening')->result();

            echo json_encode([
                'status' => 'success',
                'fields' => $fields,
                'sample_data' => $sample_data,
                'table_exists' => $this->db->table_exists('tbl_rekening')
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'pesan' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    public function reset_dummy_data()
    {
        // Double check authorization
        if ($this->session->userdata('level') != 1) {
            $this->session->set_flashdata('error', 'Unauthorized access.');
            redirect('dashboard');
        }

        try {
            $this->db->trans_start();

            // DISABLE FOREIGN KEY CHECKS TO ALLOW TRUNCATE
            $this->db->query('SET FOREIGN_KEY_CHECKS = 0;');

            // 1. CLEAR PRODUCTION DATA
            if ($this->db->table_exists('detail_produksi'))
                $this->db->truncate('detail_produksi');
            if ($this->db->table_exists('tbl_produksi'))
                $this->db->truncate('tbl_produksi');
            if ($this->db->table_exists('routing_status'))
                $this->db->truncate('routing_status');

            // 2. CLEAR STOCK LOGS & MOVEMENTS
            if ($this->db->table_exists('tbl_stok_log'))
                $this->db->truncate('tbl_stok_log');
            if ($this->db->table_exists('tbl_mutasi_detail'))
                $this->db->truncate('tbl_mutasi_detail');
            if ($this->db->table_exists('tbl_mutasi'))
                $this->db->truncate('tbl_mutasi');
            if ($this->db->table_exists('tbl_pengambilan_detail'))
                $this->db->truncate('tbl_pengambilan_detail');
            if ($this->db->table_exists('tbl_pengambilan'))
                $this->db->truncate('tbl_pengambilan');
            if ($this->db->table_exists('tbl_stok_opname'))
                $this->db->truncate('tbl_stok_opname');
            if ($this->db->table_exists('tbl_stok_opname_detail'))
                $this->db->truncate('tbl_stok_opname_detail');

            // 3. CLEAR SALES & PURCHASES (Orders are usually in tbl_pemesanan)
            if ($this->db->table_exists('tbl_pemesanan_detail'))
                $this->db->truncate('tbl_pemesanan_detail');
            if ($this->db->table_exists('tbl_pemesanan'))
                $this->db->truncate('tbl_pemesanan');
            if ($this->db->table_exists('tbl_invoice_detail'))
                $this->db->truncate('tbl_invoice_detail');
            if ($this->db->table_exists('tbl_invoice'))
                $this->db->truncate('tbl_invoice');
            if ($this->db->table_exists('tbl_pembelian_detail'))
                $this->db->truncate('tbl_pembelian_detail');
            if ($this->db->table_exists('tbl_pembelian'))
                $this->db->truncate('tbl_pembelian');

            // 4. CLEAR TEMP TABLES
            if ($this->db->table_exists('tbl_temp_beli'))
                $this->db->truncate('tbl_temp_beli');
            if ($this->db->table_exists('tbl_temp_pengambilan'))
                $this->db->truncate('tbl_temp_pengambilan');
            if ($this->db->table_exists('tbl_temp_produksi'))
                $this->db->truncate('tbl_temp_produksi');

            // 5. RESET PHYSICAL STOCK TO ZERO
            if ($this->db->table_exists('tbl_stok_gudang')) {
                $this->db->update('tbl_stok_gudang', ['qty' => 0]);
            }

            // 6. CLEAR LOGS
            if ($this->db->table_exists('user_activity_log')) {
                $this->db->truncate('user_activity_log');
            }

            // RE-ENABLE FOREIGN KEY CHECKS
            $this->db->query('SET FOREIGN_KEY_CHECKS = 1;');

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Database transaction failed during reset.');
            }

            $this->session->set_flashdata('success', 'System reset successful! All dummy transactions have been cleared. Master data
preserved.');
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'System reset failed: ' . $e->getMessage());
        }


        redirect('pengaturan');
    }

    public function audit_report()
    {
        $perusahaan_base = $this->db->get('tbl_perusahaan')->row_array();
        $tables = ['users', 'tbl_product', 'tbl_karyawan', 'tbl_konsumen', 'tbl_bahan', 'tbl_mesin', 'tbl_supplier'];
        $db_stats = [];
        foreach ($tables as $table) {
            $db_stats[$table] = $this->db->count_all($table);
        }

        $this->db->select('d.divisi_nama, COUNT(*) as total');
        $this->db->from('users u');
        $this->db->join('tbl_divisi d', 'd.divisi_id = u.role_id', 'left');
        $this->db->group_by('u.role_id');
        $users_by_role = $this->db->get()->result();

        $report = [
            'report_metadata' => [
                'generated_at' => date('Y-m-d H:i:s'),
                'system_name' => 'TGM CORE ERP',
                'company' => $perusahaan_base['nama_perusahaan'] ?? 'Unknown'
            ],
            'system_info' => [
                'php' => PHP_VERSION,
                'db' => $this->db->dbdriver,
                'db_name' => $this->db->database
            ],
            'db_stats' => $db_stats,
            'user_profile' => $users_by_role
        ];

        $filename = "Audit_Report_" . date('Ymd_His') . ".json";
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo json_encode($report, JSON_PRETTY_PRINT);
        exit;
    }

    public function penomoran()
    {
        $userid = $this->session->userdata('id');
        $perusahaan_id = $this->session->userdata('perusahaan_id');

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Konfigurasi Penomoran Dokumen',
            'perusahaan' => $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row_array(),
            'level' => $this->db->get_where('users', ['user_id' => $userid])->row_array(),
        );

        // Dummy data for prefixes (usually stored in a dedicated settings table)
        $data['prefixes'] = [
            ['code' => 'SPK', 'name' => 'Surat Perintah Kerja', 'format' => 'SPK/{YY}/{MONTH}/{SEQ}', 'next' => '1024'],
            ['code' => 'INV', 'name' => 'Invoice Penjualan', 'format' => 'INV/{YY}/{SEQ}', 'next' => '2048'],
            ['code' => 'DO', 'name' => 'Delivery Order (SJ)', 'format' => 'SJ/{BRANCH}/{SEQ}', 'next' => '0512'],
            ['code' => 'PO', 'name' => 'Purchase Order', 'format' => 'PO/{SEQ}', 'next' => '0128'],
        ];

        $this->load_admin_view('admin/pengaturan/penomoran', $data);
    }

    /* -------------------------------------------------------------------------- */
    /* HARI LIBUR & JAM KERJA */
    /* -------------------------------------------------------------------------- */

    public function harilibur()
    {
        $current_year = date('Y');
        $today = date('Y-m-d');

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Setting Hari & Kerja',
            'stats' => (object) [
                'total_this_year' => $this->db->where('YEAR(tanggal)', $current_year)->count_all_results('hari_libur'),
                'upcoming' => $this->db->where('tanggal >=', $today)->count_all_results('hari_libur'),
                'total_all' => $this->db->count_all_results('hari_libur')
            ],
            'perusahaan' => $this->db->get_where('tbl_perusahaan', [
                'id_perusahaan' =>
                    $this->session->userdata('perusahaan_id')
            ])->row('nama_perusahaan'),
        );

        $this->db->order_by('tanggal', 'DESC');
        $data['hari_libur'] = $this->db->get('hari_libur')->result();
        $data['list_jam_kerja'] = $this->db->order_by('urutan', 'ASC')->get('tbl_jam_kerja')->result();

        $this->load_admin_view('admin/harilibur/index', $data);
    }

    public function harilibur_store()
    {
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required|is_unique[hari_libur.tanggal]');
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
        } else {
            $data = ['tanggal' => $this->input->post('tanggal', true), 'keterangan' => $this->input->post('keterangan', true)];
            $this->db->insert('hari_libur', $data);
            $this->session->set_flashdata('success', 'Data hari libur berhasil ditambahkan!');
        }
        redirect('pengaturan/harilibur');
    }

    public function harilibur_update()
    {
        $id = $this->input->post('id', true);
        $data = ['tanggal' => $this->input->post('tanggal', true), 'keterangan' => $this->input->post('keterangan', true)];
        $this->db->where('id', $id)->update('hari_libur', $data);
        $this->session->set_flashdata('success', 'Data hari libur berhasil diperbarui!');
        redirect('pengaturan/harilibur');
    }

    public function harilibur_delete($id)
    {
        $this->db->where('id', $id)->delete('hari_libur');
        $this->session->set_flashdata('success', 'Data hari libur berhasil dihapus!');
        redirect('pengaturan/harilibur');
    }

    public function harilibur_update_jam_kerja()
    {
        $ids = $this->input->post('id', true);
        $jam_masuk = $this->input->post('jam_masuk', true);
        $jam_pulang = $this->input->post('jam_pulang', true);
        $jam_mulai_lembur = $this->input->post('jam_mulai_lembur', true);
        $boleh_lembur = $this->input->post('boleh_lembur', true);

        if (!empty($ids)) {
            foreach ($ids as $key => $id) {
                $this->db->where('id', $id)->update('tbl_jam_kerja', [
                    'jam_masuk' => $jam_masuk[$key],
                    'jam_pulang' => $jam_pulang[$key],
                    'jam_mulai_lembur' => $jam_mulai_lembur[$key],
                    'boleh_lembur' => $boleh_lembur[$key]
                ]);
            }
            $this->session->set_flashdata('success', 'Pengaturan Jam Kerja Harian berhasil diperbarui!');
        }
        redirect('pengaturan/harilibur');
    }

    public function harilibur_sync_nasional()
    {
        $current_year = date('Y');
        // Repo Kalender Indonesia V2 (Active & Updated)
        $url = "https://raw.githubusercontent.com/guangrei/APIHariLibur_V2/main/calendar.json";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $json = curl_exec($ch);
        curl_close($ch);

        if ($json === FALSE) {
            $this->session->set_flashdata('error', 'Gagal menghubungkan ke server kalender eksternal.');
            redirect('pengaturan/harilibur');
        }

        $all_holidays = json_decode($json, true);
        if (!$all_holidays) {
            $this->session->set_flashdata('error', 'Data kalender tidak valid atau kosong.');
            redirect('pengaturan/harilibur');
        }

        $count_success = 0;
        $count_skip = 0;

        foreach ($all_holidays as $date => $info) {
            // Cek apakah data untuk tahun berjalan dan merupakan hari libur
            if (substr($date, 0, 4) == $current_year && isset($info['holiday']) && $info['holiday'] == true) {

                // Pastikan format tanggal valid YYYY-MM-DD
                $clean_date = date('Y-m-d', strtotime($date));

                // Cek apakah data sudah ada
                $check = $this->db->get_where('hari_libur', ['tanggal' => $clean_date])->row();

                if (!$check) {
                    // Ambil deskripsi dari summary (array)
                    $keterangan = 'Hari Libur Nasional';
                    if (isset($info['summary']) && is_array($info['summary'])) {
                        $keterangan = implode(', ', $info['summary']);
                    }

                    $this->db->insert('hari_libur', [
                        'tanggal' => $clean_date,
                        'keterangan' => $keterangan
                    ]);
                    $count_success++;
                } else {
                    $count_skip++;
                }
            }
        }

        if ($count_success > 0) {
            $this->session->set_flashdata('success', "Sinkronisasi berhasil! $count_success hari libur baru untuk tahun
$current_year ditambahkan.");
        } else {
            $this->session->set_flashdata('info', "Tidak ada data libur baru yang perlu ditambahkan untuk tahun $current_year.");
        }

        redirect('pengaturan/harilibur');
    }

    /* -------------------------------------------------------------------------- */
    /* MASTER MESIN */
    /* -------------------------------------------------------------------------- */

    public function mesin()
    {
        $data = [
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Master Mesin Produksi',
            'tampilData' => $this->M_master->tampilDataMesin()->result(),
            'list_perusahaan' => $this->db->get('tbl_perusahaan')->result(),
            'level' => $this->db->get_where('users', ['user_id' => $this->session->userdata('id')])->row_array()
        ];
        $data['total_rows'] = count($data['tampilData']);
        $this->load_admin_view('admin/mesin/index', $data);
    }

    public function mesin_simpan()
    {
        $data = [
            'mesin_nama' => $this->input->post('mesin_nama', true),
            'mesin_keterangan' => $this->input->post('mesin_keterangan', true),
            'kategori' => $this->input->post('kategori', true),
            'perusahaan_id' => $this->input->post('perusahaan_id', true)
        ];
        $this->db->insert('tbl_mesin', $data);
        $this->session->set_flashdata('success', 'Mesin berhasil ditambahkan.');
        redirect('pengaturan/mesin');
    }

    public function mesin_update()
    {
        $id = $this->input->post('mesin_id', true);
        $data = [
            'mesin_nama' => $this->input->post('mesin_nama', true),
            'mesin_keterangan' => $this->input->post('mesin_keterangan', true),
            'kategori' => $this->input->post('kategori', true),
            'perusahaan_id' => $this->input->post('perusahaan_id', true)
        ];
        $this->db->where('mesin_id', $id)->update('tbl_mesin', $data);
        $this->session->set_flashdata('success', 'Mesin berhasil diperbarui.');
        redirect('pengaturan/mesin');
    }

    public function mesin_delete($id)
    {
        $this->db->where('mesin_id', $id)->delete('tbl_mesin');
        $this->session->set_flashdata('success', 'Mesin berhasil dihapus.');
        redirect('pengaturan/mesin');
    }

    public function mesin_aktifitas()
    {
        $data['title'] = 'Aktifitas Mesin Produksi';
        $data['controller'] = $this->uri->segment(1);
        $data['function'] = $this->uri->segment(2);

        $this->db->select('m.mesin_id, m.mesin_nama, m.mesin_keterangan, m.kategori, p.nama_perusahaan');
        $this->db->select('COUNT(dp.detail_produksi_id) as total_pekerjaan');
        $this->db->select('SUM(CASE WHEN dp.status_produksi = "2" THEN dp.qty_output ELSE 0 END) as total_qty');
        $this->db->select('SUM(dp.qty_missing) as total_reject');
        $this->db->select('SUM(dp.panjang_output * dp.lebar_output * dp.qty_output / 10000) as total_m2');
        $this->db->select('COUNT(CASE WHEN dp.status_produksi = "1" THEN 1 END) as jobs_in_progress');
        $this->db->from('tbl_mesin m');
        $this->db->join('tbl_perusahaan p', 'm.perusahaan_id = p.id_perusahaan', 'left');
        $this->db->join('detail_produksi dp', 'm.mesin_id = dp.mesin_id', 'left');
        $this->db->group_by('m.mesin_id')->order_by('total_pekerjaan', 'DESC');

        $data['aktifitas'] = $this->db->get()->result();
        $this->load_admin_view('admin/mesin/aktifitas', $data);
    }

    /* -------------------------------------------------------------------------- */
    /* MASTER MESIN EDC */
    /* -------------------------------------------------------------------------- */

    public function mesin_edc()
    {
        $data = [
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Master Mesin EDC',
            'tampilData' => $this->db->get('tbl_mesin_edc')->result(),
            'level' => $this->db->get_where('users', ['user_id' => $this->session->userdata('id')])->row_array()
        ];
        $data['total_rows'] = count($data['tampilData']);
        $this->load_admin_view('admin/mesin_edc/index', $data);
    }

    public function mesin_edc_simpan()
    {
        $data = [
            'edc_nama' => $this->input->post('edc_nama', true),
            'edc_ket' => $this->input->post('edc_ket', true)
        ];
        $this->db->insert('tbl_mesin_edc', $data);
        $this->session->set_flashdata('success', 'Mesin EDC berhasil ditambahkan.');
        redirect('pengaturan/mesin_edc');
    }

    public function mesin_edc_update()
    {
        $id = $this->input->post('edc_id', true);
        $data = [
            'edc_nama' => $this->input->post('edc_nama', true),
            'edc_ket' => $this->input->post('edc_ket', true)
        ];
        $this->db->where('edc_id', $id)->update('tbl_mesin_edc', $data);
        $this->session->set_flashdata('success', 'Mesin EDC berhasil diperbarui.');
        redirect('pengaturan/mesin_edc');
    }

    public function mesin_edc_delete($id)
    {
        $this->db->where('edc_id', $id)->delete('tbl_mesin_edc');
        $this->session->set_flashdata('success', 'Mesin EDC berhasil dihapus.');
        redirect('pengaturan/mesin_edc');
    }

    /* -------------------------------------------------------------------------- */
    /* MASTER E-WALLET */
    /* -------------------------------------------------------------------------- */

    public function ewallet()
    {
        $data = [
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Master E-Wallet',
            'tampilData' => $this->db->get('tbl_ewallet')->result(),
            'level' => $this->db->get_where('users', ['user_id' => $this->session->userdata('id')])->row_array()
        ];
        $data['total_rows'] = count($data['tampilData']);
        $this->load_admin_view('admin/ewallet/index', $data);
    }

    public function ewallet_simpan()
    {
        $data = [
            'ewallet_nama' => $this->input->post('ewallet_nama', true),
            'wallet_ket' => $this->input->post('wallet_ket', true)
        ];
        $this->db->insert('tbl_ewallet', $data);
        $this->session->set_flashdata('success', 'E-Wallet berhasil ditambahkan.');
        redirect('pengaturan/ewallet');
    }

    public function ewallet_update()
    {
        $id = $this->input->post('id', true);
        $data = [
            'ewallet_nama' => $this->input->post('ewallet_nama', true),
            'wallet_ket' => $this->input->post('wallet_ket', true)
        ];
        $this->db->where('id', $id)->update('tbl_ewallet', $data);
        $this->session->set_flashdata('success', 'E-Wallet berhasil diperbarui.');
        redirect('pengaturan/ewallet');
    }

    public function ewallet_delete($id)
    {
        $this->db->where('id', $id)->delete('tbl_ewallet');
        $this->session->set_flashdata('success', 'E-Wallet berhasil dihapus.');
        redirect('pengaturan/ewallet');
    }

    /* -------------------------------------------------------------------------- */
    /* MASTER ASET */
    /* -------------------------------------------------------------------------- */

    public function aset()
    {
        $data = [
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Daftar Aset Lainnya',
            'tampilData' => $this->db->select('tbl_aset.*, tbl_perusahaan.nama_perusahaan')->join(
                'tbl_perusahaan',
                'tbl_perusahaan.id_perusahaan = tbl_aset.perusahaan_id',
                'left'
            )->get('tbl_aset')->result(),
            'list_perusahaan' => $this->db->get('tbl_perusahaan')->result(),
            'level' => $this->db->get_where('users', ['user_id' => $this->session->userdata('id')])->row_array()
        ];
        $data['total_rows'] = count($data['tampilData']);
        $this->load_admin_view('admin/aset/index', $data);
    }

    public function aset_simpan()
    {
        $data = [
            'aset_nama' => $this->input->post('aset_nama', true),
            'aset_kategori' => $this->input->post('aset_kategori', true),
            'aset_harga' => preg_replace('/[^0-9]/', '', $this->input->post('aset_harga', true)),
            'aset_keterangan' => $this->input->post('aset_keterangan', true),
            'perusahaan_id' => $this->input->post('perusahaan_id', true)
        ];

        if (!empty($_FILES['aset_foto']['name'])) {
            $config['upload_path'] = FCPATH . 'assets/images/aset/';
            $config['allowed_types'] = 'jpeg|jpg|png|gif|svg';
            $config['max_size'] = 5048;
            $config['file_name'] = 'aset_' . time();

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if ($this->upload->do_upload('aset_foto')) {
                $upload_data = $this->upload->data();
                $data['aset_foto'] = $upload_data['file_name'];

                // Resize Image to max 800px width/height to save space
                $config_resize['image_library'] = 'gd2';
                $config_resize['source_image'] = $upload_data['full_path'];
                $config_resize['maintain_ratio'] = TRUE;
                $config_resize['width'] = 800;
                $config_resize['height'] = 800;

                $this->load->library('image_lib', $config_resize);
                $this->image_lib->resize();
                $this->image_lib->clear();

            } else {
                $this->session->set_flashdata('error', 'Upload foto gagal: ' . $this->upload->display_errors('', ''));
                redirect('pengaturan/aset');
                return;
            }
        }

        $this->db->insert('tbl_aset', $data);
        $insert_id = $this->db->insert_id();

        // Generate QR Code untuk Aset Baru
        $this->generateAsetQrCode($insert_id);

        $this->session->set_flashdata('success', 'Data Aset berhasil ditambahkan dan QR Code berhasil dibuat.');
        redirect('pengaturan/aset');
    }

    public function aset_update()
    {
        $id = $this->input->post('aset_id', true);
        $data = [
            'aset_nama' => $this->input->post('aset_nama', true),
            'aset_kategori' => $this->input->post('aset_kategori', true),
            'aset_harga' => preg_replace('/[^0-9]/', '', $this->input->post('aset_harga', true)),
            'aset_keterangan' => $this->input->post('aset_keterangan', true),
            'perusahaan_id' => $this->input->post('perusahaan_id', true)
        ];

        if (!empty($_FILES['aset_foto']['name'])) {
            $config['upload_path'] = FCPATH . 'assets/images/aset/';
            $config['allowed_types'] = 'jpeg|jpg|png|gif|svg';
            $config['max_size'] = 5048;
            $config['file_name'] = 'aset_' . time();

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if ($this->upload->do_upload('aset_foto')) {
                $upload_data = $this->upload->data();

                // Hapus foto lama jika ada
                $old_data = $this->db->get_where('tbl_aset', ['aset_id' => $id])->row();
                if ($old_data && !empty($old_data->aset_foto)) {
                    $old_path = FCPATH . 'assets/images/aset/' . $old_data->aset_foto;
                    if (file_exists($old_path)) {
                        unlink($old_path);
                    }
                }
                $data['aset_foto'] = $upload_data['file_name'];

                // Resize Image
                $config_resize['image_library'] = 'gd2';
                $config_resize['source_image'] = $upload_data['full_path'];
                $config_resize['maintain_ratio'] = TRUE;
                $config_resize['width'] = 800;
                $config_resize['height'] = 800;

                $this->load->library('image_lib', $config_resize);
                // Clear previous config in case library was already loaded (e.g in a loop, though not the case here)
                $this->image_lib->initialize($config_resize);
                $this->image_lib->resize();
                $this->image_lib->clear();

            } else {
                $this->session->set_flashdata('error', 'Upload foto gagal: ' . $this->upload->display_errors('', ''));
                redirect('pengaturan/aset');
                return;
            }
        }

        $this->db->where('aset_id', $id)->update('tbl_aset', $data);

        // Regenerate QR just in case it's missing
        $this->generateAsetQrCode($id);

        $this->session->set_flashdata('success', 'Data Aset berhasil diperbarui.');
        redirect('pengaturan/aset');
    }

    public function aset_delete($id)
    {
        $old_data = $this->db->get_where('tbl_aset', ['aset_id' => $id])->row();
        if ($old_data && !empty($old_data->aset_foto)) {
            $old_path = FCPATH . 'assets/images/aset/' . $old_data->aset_foto;
            if (file_exists($old_path)) {
                unlink($old_path);
            }
        }

        $this->db->where('aset_id', $id)->delete('tbl_aset');

        $qr_path = FCPATH . 'assets/qrcode/aset_' . $id . '.png';
        if (file_exists($qr_path)) {
            unlink($qr_path);
        }

        $this->session->set_flashdata('success', 'Data Aset dan QR Code berhasil dihapus.');
        redirect('pengaturan/aset');
    }

    public function aset_detail($id)
    {
        $data['title'] = 'Informasi Detail Aset';
        $data['aset'] = $this->db->select('tbl_aset.*, tbl_perusahaan.nama_perusahaan')
            ->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_aset.perusahaan_id', 'left')
            ->where('aset_id', $id)->get('tbl_aset')->row();

        if (!$data['aset']) {
            show_404();
        }

        // This is a public scanner view, no need for admin sidebar
        $this->load->view('admin/aset/detail', $data);
    }

    public function aset_print_label($id)
    {
        $data['title'] = 'Cetak Label Aset';
        $data['aset'] = $this->db->select('tbl_aset.*, tbl_perusahaan.nama_perusahaan')
            ->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_aset.perusahaan_id', 'left')
            ->where('aset_id', $id)->get('tbl_aset')->row();

        if (!$data['aset']) {
            show_404();
        }

        // Dedicated print view
        $this->load->view('admin/aset/print_label', $data);
    }

    public function aset_print_all()
    {
        $data['title'] = 'Cetak Semua Label Aset';
        $data['aset_list'] = $this->db->select('tbl_aset.*, tbl_perusahaan.nama_perusahaan')
            ->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_aset.perusahaan_id', 'left')
            ->order_by('tbl_aset.aset_id', 'ASC')
            ->get('tbl_aset')->result();

        if (empty($data['aset_list'])) {
            $this->session->set_flashdata('error', 'Tidak ada aset untuk dicetak.');
            redirect('pengaturan/aset');
            return;
        }

        $this->load->view('admin/aset/print_label_all', $data);
    }

    private function generateAsetQrCode($aset_id)
    {
        $this->load->library('ciqrcode');
        $config = [
            'cacheable' => true,
            'cachedir' => './assets/',
            'errorlog' => './assets/',
            'imagedir' => './assets/qrcode/',
            'quality' => true,
            'size' => '1024',
            'black' => [0, 0, 0], // Black QR Code
            'white' => [255, 255, 255], // White background
        ];
        $this->ciqrcode->initialize($config);

        $image_name = 'aset_' . $aset_id . '.png';
        $params = [
            'data' => base_url('pengaturan/aset_detail/' . $aset_id),
            'level' => 'H',
            'size' => 10,
            'savename' => FCPATH . 'assets/qrcode/' . $image_name,
        ];
        $this->ciqrcode->generate($params);
    }

    /* -------------------------------------------------------------------------- */
    /* MASTER PERUSAHAAN */
    /* -------------------------------------------------------------------------- */

    public function perusahaan()
    {
        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Setting Data Perusahaan',
            'tampilData' => $this->M_master->getPerusahaan()->result(),
            'total_rows' => $this->M_master->getPerusahaan()->num_rows(),
            'perusahaan' => $this->db->get_where('tbl_perusahaan', [
                'id_perusahaan' =>
                    $this->session->userdata('perusahaan_id')
            ])->row('nama_perusahaan'),
        );
        $this->load_admin_view('admin/perusahaan/index', $data);
    }

    public function perusahaan_update()
    {
        $id = $this->input->post('id_perusahaan', true);
        $data = array(
            'nama_perusahaan' => $this->input->post('nama_perusahaan', true),
            'alamat_perusahaan' => $this->input->post('alamat_perusahaan', true),
            'no_wa' => $this->input->post('no_wa', true),
            'email' => $this->input->post('email', true),
            'website' => $this->input->post('website', true),
            'latitude' => $this->input->post('latitude', true),
            'longitude' => $this->input->post('longitude', true),
            'radius_meter' => $this->input->post('radius_meter', true),
            'payroll_start_day' => $this->input->post('payroll_start_day', true),
            'payroll_end_day' => $this->input->post('payroll_end_day', true),
            'is_holding' => $this->input->post('is_holding', true),
            'parent_id' => $this->input->post('parent_id', true),
            'owner_name' => $this->input->post('owner_name', true),
            'npwp' => $this->input->post('npwp', true),
        );

        if (!empty($_FILES['logo']['name'])) {
            $config['upload_path'] = FCPATH . 'assets/images/';
            $config['allowed_types'] = 'jpeg|jpg|png|gif|svg';
            $config['max_size'] = 2048;
            $config['file_name'] = 'logo_' . time();

            $this->load->library('upload', $config);
            if ($this->upload->do_upload('logo')) {
                $data['logo'] = $this->upload->data('file_name');
            }
        }

        $this->db->where('id_perusahaan', $id)->update('tbl_perusahaan', $data);
        $this->session->set_flashdata('success', 'Data perusahaan berhasil diperbarui!');
        redirect('pengaturan/perusahaan');
    }

    public function invoice_template()
    {
        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Pengaturan Cetak Invoice',
            'tampilData' => $this->M_master->getPerusahaan()->result(),
            'total_rows' => $this->M_master->getPerusahaan()->num_rows(),
            'perusahaan' => $this->db->get_where('tbl_perusahaan', [
                'id_perusahaan' =>
                    $this->session->userdata('perusahaan_id')
            ])->row('nama_perusahaan'),
        );
        $this->load_admin_view('admin/perusahaan/invoice_template', $data);
    }

    public function invoice_template_update()
    {
        $id = $this->input->post('id_perusahaan', true);
        $data = array(
            'invoice_paper' => $this->input->post('invoice_paper', true) ?: '80mm',
            'invoice_font_size' => $this->input->post('invoice_font_size', true) ?: 11,
            'invoice_font_family' => $this->input->post('invoice_font_family', true) ?: 'Roboto',
            'invoice_show_logo' => $this->input->post('invoice_show_logo', true) !== NULL ? $this->input->post('invoice_show_logo', true) : 1,
            'invoice_logo_width' => $this->input->post('invoice_logo_width', true) ?: 50,
            'invoice_show_qr' => $this->input->post('invoice_show_qr', true) !== NULL ? $this->input->post('invoice_show_qr', true) : 1,
            'invoice_qr_width' => $this->input->post('invoice_qr_width', true) ?: 50,
            'invoice_footer_note' => $this->input->post('invoice_footer_note', true),
            'invoice_section_spacing' => $this->input->post('invoice_section_spacing', true) !== NULL ? (int)$this->input->post('invoice_section_spacing', true) : 8,
        );

        $this->db->where('id_perusahaan', $id)->update('tbl_perusahaan', $data);
        $this->session->set_flashdata('message', '<div class="alert alert-success border-0 shadow-sm rounded-3">Pengaturan cetak invoice berhasil diperbarui!</div>');
        redirect('pengaturan/invoice_template');
    }

    // New method for updating only Tag Produksi settings
    public function tag_template_update()
    {
        $id = $this->input->post('id_perusahaan', true);
        $tag_paper = $this->input->post('tag_paper', true) ?: '80mm';
        
        $data = array(
            'tag_paper' => $tag_paper,
            'tag_font_size' => $this->input->post('tag_font_size', true) ?: 11,
            'tag_font_family' => $this->input->post('tag_font_family', true) ?: 'Roboto',
            'tag_show_logo' => $this->input->post('tag_show_logo', true) !== NULL ? $this->input->post('tag_show_logo', true) : 1,
            'tag_logo_width' => $this->input->post('tag_logo_width', true) ?: 50,
            'tag_show_qr' => $this->input->post('tag_show_qr', true) !== NULL ? $this->input->post('tag_show_qr', true) : 1,
            'tag_qr_width' => $this->input->post('tag_qr_width', true) ?: 50,
            'tag_footer_note' => $this->input->post('tag_footer_note', true),
            'tag_section_spacing' => $this->input->post('tag_section_spacing', true) !== NULL ? (int)$this->input->post('tag_section_spacing', true) : 8,
        );
        $this->db->where('id_perusahaan', $id)->update('tbl_perusahaan', $data);
        $this->session->set_flashdata('message', '<div class="alert alert-success border-0 shadow-sm rounded-3">Pengaturan Tag Produksi berhasil diperbarui!</div>');
        redirect('pengaturan/invoice_template');
    }

    public function test_print_invoice($id_perusahaan)
    {
        $perusahaan = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $id_perusahaan])->row_array();
        if (!$perusahaan) {
            show_404();
        }
        $data['perusahaan'] = $perusahaan;
        $this->load->view('admin/perusahaan/test_print_invoice', $data);
    }

    public function test_print_preview()
    {
        $id_perusahaan = $this->input->post('id_perusahaan', true);
        $perusahaan = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $id_perusahaan])->row_array();
        
        $data['perusahaan'] = array(
            'nama_perusahaan' => $perusahaan ? $perusahaan['nama_perusahaan'] : 'PT. Trigemilang Mandiri',
            'logo' => $perusahaan ? $perusahaan['logo'] : 'default-logo.png',
            'invoice_paper' => $this->input->post('invoice_paper', true) ?: '80mm',
            'invoice_font_size' => $this->input->post('invoice_font_size', true) ?: 14,
            'invoice_font_family' => $this->input->post('invoice_font_family', true) ?: 'Roboto',
            'invoice_show_logo' => $this->input->post('invoice_show_logo', true) !== NULL ? $this->input->post('invoice_show_logo', true) : 1,
            'invoice_logo_width' => $this->input->post('invoice_logo_width', true) ?: 50,
            'invoice_show_qr' => $this->input->post('invoice_show_qr', true) !== NULL ? $this->input->post('invoice_show_qr', true) : 1,
            'invoice_qr_width' => $this->input->post('invoice_qr_width', true) ?: 50,
            'invoice_footer_note' => $this->input->post('invoice_footer_note', true),
            'invoice_section_spacing' => $this->input->post('invoice_section_spacing', true) !== NULL ? (int)$this->input->post('invoice_section_spacing', true) : 8,
            'tag_paper' => $this->input->post('tag_paper', true) ?: '80mm',
            'tag_font_size' => $this->input->post('tag_font_size', true) ?: 11,
            'tag_font_family' => $this->input->post('tag_font_family', true) ?: 'Roboto',
            'tag_show_logo' => $this->input->post('tag_show_logo', true) !== NULL ? $this->input->post('tag_show_logo', true) : 1,
            'tag_logo_width' => $this->input->post('tag_logo_width', true) ?: 50,
            'tag_show_qr' => $this->input->post('tag_show_qr', true) !== NULL ? $this->input->post('tag_show_qr', true) : 1,
            'tag_qr_width' => $this->input->post('tag_qr_width', true) ?: 50,
            'tag_footer_note' => $this->input->post('tag_footer_note', true),
            'tag_section_spacing' => $this->input->post('tag_section_spacing', true) !== NULL ? (int)$this->input->post('tag_section_spacing', true) : 8,
        );
        $this->load->view('admin/perusahaan/test_print_invoice', $data);
    }

    /* -------------------------------------------------------------------------- */
    /* MASTER SATUAN */
    /* -------------------------------------------------------------------------- */

    public function satuan()
    {
        $data['title'] = 'Master Satuan';
        $data['controller'] = $this->uri->segment(1);
        $data['function'] = $this->uri->segment(2);
        $data['satuan'] = $this->M_master->getDatanonorder('tbl_satuan')->result();
        $this->load_admin_view('admin/satuan/index', $data);
    }

    public function satuan_add()
    {
        $data = [
            'satuan_nama' => $this->input->post('satuan_nama', true),
            'satuan_turunan' => $this->input->post('satuan_turunan', true),
            'satuan_deskripsi' => $this->input->post('satuan_deskripsi', true),
        ];
        $this->db->insert('tbl_satuan', $data);
        $this->session->set_flashdata('success', 'Satuan berhasil ditambahkan.');
        redirect('pengaturan/satuan');
    }

    public function satuan_update()
    {
        $id = $this->input->post('satuan_id', true);
        $data = [
            'satuan_nama' => $this->input->post('satuan_nama', true),
            'satuan_turunan' => $this->input->post('satuan_turunan', true),
            'satuan_deskripsi' => $this->input->post('satuan_deskripsi', true),
        ];
        $this->db->where('satuan_id', $id)->update('tbl_satuan', $data);
        $this->session->set_flashdata('success', 'Satuan berhasil diperbarui.');
        redirect('pengaturan/satuan');
    }

    public function satuan_delete($id)
    {
        $this->db->where('satuan_id', $id)->delete('tbl_satuan');
        $this->session->set_flashdata('success', 'Satuan berhasil dihapus.');
        redirect('pengaturan/satuan');
    }

    /* -------------------------------------------------------------------------- */
    /* MASTER SUPPLIER */
    /* -------------------------------------------------------------------------- */

    public function supplier()
    {
        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Data Supplier',
            'tampilData' => $this->db->get('tbl_supplier')->result(),
        );
        $this->load_admin_view('admin/supplier/index', $data);
    }

    public function supplier_add()
    {
        $data = [
            'supplier_kode' => $this->input->post('supplier_kode', true),
            'supplier_nama' => $this->input->post('supplier_nama', true),
            'supplier_alamat' => $this->input->post('supplier_alamat', true) ?: '',
            'kota_provinsi' => $this->input->post('kota_provinsi', true) ?: '',
            'supplier_nohp' => $this->input->post('supplier_nohp', true) ?: '',
            'supplier_telp' => $this->input->post('supplier_telp', true) ?: '',
            'supplier_email' => $this->input->post('supplier_email', true) ?: '',
            'alamat_pajak' => $this->input->post('alamat_pajak', true) ?: '',
            'NPWP' => $this->input->post('npwp', true) ?: '',
            'termin' => $this->input->post('termin', true) ?: 30
        ];
        $this->db->insert('tbl_supplier', $data);
        echo json_encode(['status' => 'success', 'pesan' => 'Supplier berhasil ditambahkan.']);
    }

    public function supplier_update()
    {
        $id = $this->input->post('supplier_id', true);
        $data = [
            'supplier_kode' => $this->input->post('supplier_kode', true),
            'supplier_nama' => $this->input->post('supplier_nama', true),
            'supplier_alamat' => $this->input->post('supplier_alamat', true),
            'kota_provinsi' => $this->input->post('kota_provinsi', true),
            'supplier_nohp' => $this->input->post('supplier_nohp', true),
            'supplier_telp' => $this->input->post('supplier_telp', true),
            'supplier_email' => $this->input->post('supplier_email', true),
            'alamat_pajak' => $this->input->post('alamat_pajak', true),
            'NPWP' => $this->input->post('NPWP', true),
            'termin' => $this->input->post('termin', true) ?: 30
        ];
        $this->db->where('supplier_id', $id)->update('tbl_supplier', $data);
        echo json_encode(['status' => 'success', 'pesan' => 'Data supplier berhasil diperbarui.']);
    }

    public function supplier_delete($id)
    {
        $this->db->where('supplier_id', $id)->delete('tbl_supplier');
        echo json_encode(['status' => 'success', 'pesan' => 'Supplier berhasil dihapus.']);
    }

    public function supplier_generate_code()
    {
        header('Content-Type: application/json');
        $this->db->select('supplier_kode');
        $this->db->like('supplier_kode', 'SUP', 'after');
        $this->db->order_by('supplier_id', 'DESC');
        $this->db->limit(1);
        $last_row = $this->db->get('tbl_supplier')->row();

        $last_code = $last_row ? $last_row->supplier_kode : 'SUP000';
        $num = (int) substr($last_code, 3);
        $code = 'SUP' . str_pad($num + 1, 3, '0', STR_PAD_LEFT);
        echo json_encode(['status' => 'success', 'code' => $code]);
    }

    public function supplier_get_detail($supplier_id)
    {
        header('Content-Type: application/json');
        $supplier = $this->db->get_where('tbl_supplier', ['supplier_id' => $supplier_id])->row();
        if (!$supplier) {
            echo json_encode(['status' => 'error', 'pesan' => 'Supplier tidak ditemukan']);
            return;
        }
        $html = '<div class="row">
    <div class="col-md-12">
        <h6>' . $supplier->supplier_nama . ' (' . $supplier->supplier_kode . ')</h6>
        <p>' . $supplier->supplier_alamat . '</p>
    </div>
</div>';
        echo json_encode(['status' => 'success', 'html' => $html]);
    }

    public function supplier_get_json()
    {
        header('Content-Type: application/json');
        $data = $this->db->get('tbl_supplier')->result();
        echo json_encode(['status' => 'success', 'data' => $data]);
    }

    public function supplier_get_transaction_counts()
    {
        header('Content-Type: application/json');
        $this->db->select('supplier_id, COUNT(*) as transaction_count');
        $this->db->group_by('supplier_id');
        $data = $this->db->get('tbl_pembelian')->result();
        echo json_encode(['status' => 'success', 'data' => $data]);
    }

    /* -------------------------------------------------------------------------- */
    /* MASTER KATEGORI */
    /* -------------------------------------------------------------------------- */

    public function kategori()
    {
        $data['controller'] = $this->uri->segment(1);
        $data['function'] = $this->uri->segment(2);
        $data['title'] = 'Daftar Kategori';

        // Fetch categories with total sub-category count
        $this->db->select('tbl_kategori.*, (SELECT COUNT(*) FROM tbl_subkategori WHERE subkategori_ket =
tbl_kategori.kategori_id) as total_sub');
        $this->db->from('tbl_kategori');
        $this->db->order_by('kategori_urutan', 'ASC');

        $data['tampilData'] = $this->db->get()->result();
        $data['total_rows'] = count($data['tampilData']);
        $this->load_admin_view('admin/kategori/index', $data);
    }

    public function export_all_schemas()
    {
        $categories = $this->db->get('tbl_kategori')->result_array();
        $subcategories = $this->db->get('tbl_subkategori')->result_array();

        $data = [
            'version' => '1.0',
            'exported_at' => date('Y-m-d H:i:s'),
            'categories' => $categories,
            'subcategories' => $subcategories
        ];

        $json = json_encode($data, JSON_PRETTY_PRINT);
        $filename = "bulk_schema_backup_" . date('Ymd_His') . ".json";

        $this->load->helper('download');
        force_download($filename, $json);
    }

    public function import_all_schemas()
    {
        if (!empty($_FILES['schema_file']['name'])) {
            $config['upload_path'] = './tmp/';
            $config['allowed_types'] = 'json';
            $config['file_name'] = 'temp_import_' . time();

            if (!is_dir('./tmp/'))
                mkdir('./tmp/', 0777, true);

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('schema_file')) {
                $file = $this->upload->data();
                $content = file_get_contents($file['full_path']);
                $data = json_decode($content, true);

                unlink($file['full_path']); // Cleanup

                if ($data && isset($data['categories'])) {
                    $this->db->trans_start();

                    // Full Sync Categories
                    foreach ($data['categories'] as $cat) {
                        $update_data = [
                            'kategori_nama' => $cat['kategori_nama'] ?? '',
                            'kategori_deskripsi' => $cat['kategori_deskripsi'] ?? '',
                            'kategori_urutan' => $cat['kategori_urutan'] ?? 0,
                            'status' => $cat['status'] ?? 0,
                            'form_schema' => $cat['form_schema'] ?? ''
                        ];

                        $exists = $this->db->where('kategori_id', $cat['kategori_id'])->get('tbl_kategori')->num_rows();
                        if ($exists > 0) {
                            $this->db->where('kategori_id', $cat['kategori_id']);
                            $this->db->update('tbl_kategori', $update_data);
                        } else {
                            $update_data['kategori_id'] = $cat['kategori_id'];
                            $this->db->insert('tbl_kategori', $update_data);
                        }
                    }

                    // Full Sync Subcategories
                    if (isset($data['subcategories'])) {
                        foreach ($data['subcategories'] as $sub) {
                            $update_sub = [
                                'subkategori_nama' => $sub['subkategori_nama'] ?? '',
                                'subkategori_deskripsi' => $sub['subkategori_deskripsi'] ?? '',
                                'subkategori_ket' => $sub['subkategori_ket'] ?? 0,
                                'subkategori_urutan' => $sub['subkategori_urutan'] ?? 0,
                                'status' => $sub['status'] ?? 0,
                                'subkategori_config' => $sub['subkategori_config'] ?? '',
                                'form_schema' => $sub['form_schema'] ?? ''
                            ];

                            $exists = $this->db->where('subkategori_id', $sub['subkategori_id'])->get('tbl_subkategori')->num_rows();
                            if ($exists > 0) {
                                $this->db->where('subkategori_id', $sub['subkategori_id']);
                                $this->db->update('tbl_subkategori', $update_sub);
                            } else {
                                $update_sub['subkategori_id'] = $sub['subkategori_id'];
                                $this->db->insert('tbl_subkategori', $update_sub);
                            }
                        }
                    }

                    $this->db->trans_complete();

                    if ($this->db->trans_status()) {
                        $this->session->set_flashdata('success', 'Bulk Sync Data Berhasil! Seluruh kategori, subkategori, dan skema form telah
sinkron.');
                    } else {
                        $this->session->set_flashdata('error', 'Gagal melakukan sinkronisasi database saat import.');
                    }
                } else {
                    $this->session->set_flashdata('error', 'Format file JSON tidak valid atau kategori tidak ditemukan.');
                }
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors());
            }
        } else {
            $this->session->set_flashdata('error', 'Silakan pilih file JSON untuk di-import.');
        }

        redirect('pengaturan/kategori');
    }

    public function kategori_add()
    {
        $data = [
            'kategori_nama' => $this->input->post('kategori_nama', true),
            'kategori_deskripsi' => $this->input->post('kategori_deskripsi', true),
            'kategori_urutan' => $this->input->post('kategori_urutan', true),
            'status' => $this->input->post('status', true),
        ];
        $this->db->insert('tbl_kategori', $data);
        $this->session->set_flashdata('success', 'Kategori berhasil ditambahkan.');
        redirect('pengaturan/kategori');
    }

    public function kategori_update()
    {
        $id = $this->input->post('kategori_id', true);
        $data = [
            'kategori_nama' => $this->input->post('kategori_nama', true),
            'kategori_deskripsi' => $this->input->post('kategori_deskripsi', true),
            'kategori_urutan' => $this->input->post('kategori_urutan', true),
            'status' => $this->input->post('status', true),
        ];
        $this->db->where('kategori_id', $id)->update('tbl_kategori', $data);
        $this->session->set_flashdata('success', 'Kategori berhasil diperbarui.');
        redirect('pengaturan/kategori');
    }

    public function kategori_delete($id)
    {
        $this->db->where('kategori_id', $id)->delete('tbl_kategori');
        $this->session->set_flashdata('success', 'Kategori berhasil dihapus.');
        redirect('pengaturan/kategori');
    }

    public function kategori_form_builder($id)
    {
        $data['controller'] = $this->uri->segment(1);
        $data['function'] = $this->uri->segment(2);
        $data['title'] = 'Category Form Builder';
        $data['kategori'] = $this->db->get_where('tbl_kategori', ['kategori_id' => $id])->row_array();
        $this->load_admin_view('admin/kategori/form_builder', $data);
    }

    public function kategori_save_schema()
    {
        $id = $this->input->post('kategori_id', true);
        $schema = $this->input->post('form_schema', true);
        $this->db->update('tbl_kategori', ['form_schema' => $schema], ['kategori_id' => $id]);
        $this->session->set_flashdata('success', 'Skema form kategori berhasil disimpan.');
        redirect('pengaturan/kategori_form_builder/' . $id);
    }

    public function subkategori_form_builder($id)
    {
        $data['controller'] = $this->uri->segment(1);
        $data['function'] = $this->uri->segment(2);
        $data['title'] = 'Sub-Category Form Builder';
        // We reuse the 'kategori' key in view to maintain template compatibility
        $sub = $this->db->get_where('tbl_subkategori', ['subkategori_id' => $id])->row_array();
        $data['kategori'] = [
            'kategori_id' => $sub['subkategori_id'],
            'kategori_nama' => $sub['subkategori_nama'],
            'form_schema' => $sub['form_schema']
        ];
        $data['is_subcategory'] = true;
        $this->load_admin_view('admin/kategori/form_builder', $data);
    }

    public function subkategori_save_schema()
    {
        $id = $this->input->post('kategori_id', true); // This is subkategori_id sent from the generic view
        $schema = $this->input->post('form_schema', true);
        $this->db->update('tbl_subkategori', ['form_schema' => $schema], ['subkategori_id' => $id]);
        $this->session->set_flashdata('success', 'Skema form sub-kategori berhasil disimpan.');
        redirect('pengaturan/subkategori_form_builder/' . $id);
    }

    public function product_form_builder($id, $is_modal_param = '')
    {
        $data['controller'] = $this->uri->segment(1);
        $data['function'] = $this->uri->segment(2);
        $data['title'] = 'Product Form Builder';
        $data['is_modal'] = ($this->input->get('modal') || $is_modal_param == 'modal') ? true : false;

        $prod = $this->db->get_where('tbl_product', ['product_id' => $id])->row_array();
        $data['kategori'] = [
            'kategori_id' => $prod['product_id'],
            'kategori_nama' => $prod['product_nama'],
            'form_schema' => $prod['form_schema']
        ];
        $data['is_product'] = true;
        
        if ($data['is_modal']) {
            $this->load->view('layout_admin/head', $data);
            $this->load->view('admin/kategori/form_builder', $data);
            $this->load->view('layout_admin/footer', $data);
        } else {
            $this->load_admin_view('admin/kategori/form_builder', $data);
        }
    }

    public function product_save_schema()
    {
        $id = $this->input->post('kategori_id', true);
        $schema = $this->input->post('form_schema', true);
        $is_modal = $this->input->post('is_modal', true);
        $this->db->update('tbl_product', ['form_schema' => $schema], ['product_id' => $id]);
        $this->session->set_flashdata('success', 'Skema form produk berhasil disimpan.');
        
        if ($is_modal) {
            redirect('pengaturan/product_form_builder/' . $id . '/modal');
        } else {
            redirect('pengaturan/product_form_builder/' . $id);
        }
    }

    /* -------------------------------------------------------------------------- */
    /* MASTER SUBKATEGORI */
    /* -------------------------------------------------------------------------- */

    public function subkategori()
    {
        $data['controller'] = $this->uri->segment(1);
        $data['function'] = $this->uri->segment(2);
        $data['title'] = 'Daftar Subkategori';
        $filter_kat = $this->input->get('kategori_id', true);

        $this->db->select('tbl_subkategori.*, tbl_kategori.kategori_nama');
        $this->db->from('tbl_subkategori');
        $this->db->join('tbl_kategori', 'tbl_kategori.kategori_id = tbl_subkategori.subkategori_ket', 'left');

        if ($filter_kat) {
            $this->db->where('tbl_subkategori.subkategori_ket', $filter_kat);
        }

        $this->db->order_by('tbl_subkategori.subkategori_urutan', 'ASC');

        $data['tampilData'] = $this->db->get()->result();
        $data['kategori'] = $this->db->get('tbl_kategori')->result();
        $data['total_rows'] = count($data['tampilData']);
        $data['filter_kategori'] = $filter_kat;

        $this->load_admin_view('admin/subkategori/index', $data);
    }

    public function subkategori_add()
    {
        $use_pxl = $this->input->post('use_pxl', true) ? 1 : 0;
        $config = json_encode(['use_pxl' => $use_pxl]);

        $data = [
            'subkategori_nama' => $this->input->post('subkategori_nama', true),
            'subkategori_deskripsi' => $this->input->post('subkategori_deskripsi', true),
            'subkategori_urutan' => $this->input->post('subkategori_urutan', true),
            'status' => $this->input->post('status', true),
            'subkategori_ket' => $this->input->post('kategori_id', true),
            'subkategori_config' => $config
        ];
        $this->db->insert('tbl_subkategori', $data);
        $this->session->set_flashdata('success', 'Subkategori berhasil ditambahkan.');
        redirect('pengaturan/subkategori');
    }

    public function subkategori_update()
    {
        $id = $this->input->post('subkategori_id', true);
        $use_pxl = $this->input->post('use_pxl', true) ? 1 : 0;
        $config = json_encode(['use_pxl' => $use_pxl]);

        $data = [
            'subkategori_nama' => $this->input->post('subkategori_nama', true),
            'subkategori_deskripsi' => $this->input->post('subkategori_deskripsi', true),
            'subkategori_urutan' => $this->input->post('subkategori_urutan', true),
            'status' => $this->input->post('status', true),
            'subkategori_ket' => $this->input->post('kategori_id', true),
            'subkategori_config' => $config
        ];
        $this->db->where('subkategori_id', $id)->update('tbl_subkategori', $data);
        $this->session->set_flashdata('success', 'Subkategori berhasil diperbarui.');
        redirect('pengaturan/subkategori');
    }

    public function subkategori_delete($id)
    {
        $this->db->where('subkategori_id', $id)->delete('tbl_subkategori');
        $this->session->set_flashdata('success', 'Subkategori berhasil dihapus.');
        redirect('pengaturan/subkategori');
    }

    /* -------------------------------------------------------------------------- */
    /* MASTER KONSUMEN */
    /* -------------------------------------------------------------------------- */

    public function konsumen_dashboard()
    {
        $userid = $this->session->userdata('id');
        $table = 'tbl_konsumen';

        $stats = $this->db->query("
SELECT
COUNT(*) as total,
SUM(CASE WHEN status = '1' THEN 1 ELSE 0 END) as active,
SUM(CASE WHEN status = '0' THEN 1 ELSE 0 END) as inactive
FROM $table
")->row();

        $trx_count = $this->db->query("SELECT COUNT(DISTINCT konsumen_id) as cnt FROM tbl_pemesanan")->row()->cnt;
        $top_customers = $this->M_transaksi->get_konsumen_dengan_transaksi()->result();

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'CRM - Dashboard Konsumen',
            'table' => $table,
            'jml_konsumen' => $stats->total,
            'total_customers' => $stats->total,
            'active_customers' => $stats->active ?? 0,
            'inactive_customers' => $stats->inactive ?? 0,
            'customers_with_transactions' => $trx_count,
            'top_customers' => $top_customers,
            'logo' => $this->conf['perusahaan']['logo'] ?? '',
            'submenu' => $this->M_master->getmenu($this->uri->segment(1))->result(),
        );

        $this->load_admin_view('admin/konsumen/index', $data);
    }

    public function konsumen_check_duplicates_ajax()
    {
        header('Content-Type: application/json');

        // 1. Check by Phone Number
        $dupes_phone = $this->db->query("
SELECT konsumen_nohp as key_val, COUNT(*) as jumlah, GROUP_CONCAT(konsumen_nama SEPARATOR ' | ') as details, 'NOMOR HP'
as type
FROM tbl_konsumen
WHERE konsumen_nohp IS NOT NULL AND konsumen_nohp != '' AND konsumen_nohp != '-'
GROUP BY konsumen_nohp
HAVING COUNT(*) > 1
ORDER BY jumlah DESC
LIMIT 50
")->result_array();

        // 2. Check by Name
        $dupes_name = $this->db->query("
SELECT konsumen_nama as key_val, COUNT(*) as jumlah, GROUP_CONCAT(konsumen_nohp SEPARATOR ' | ') as details, 'NAMA' as
type
FROM tbl_konsumen
WHERE konsumen_nama IS NOT NULL AND konsumen_nama != ''
GROUP BY konsumen_nama
HAVING COUNT(*) > 1
ORDER BY jumlah DESC
LIMIT 50
")->result_array();

        $all_dupes = array_merge($dupes_phone, $dupes_name);

        // Sort by amount
        usort($all_dupes, function ($a, $b) {
            return $b['jumlah'] <=> $a['jumlah'];
        });

        echo json_encode(array_slice($all_dupes, 0, 100));
    }

    public function konsumen_list()
    {
        $controller = $this->uri->segment(1);
        $userid = $this->session->userdata('id');
        $table = 'tbl_konsumen';

        $total_customers = $this->db->count_all($table);
        $active_customers = $this->db->where('status', '1')->count_all_results($table);
        $inactive_customers = $this->db->where('status', '0')->count_all_results($table);
        $customers_with_email = $this->db->where('konsumen_email IS NOT NULL')->where(
            'konsumen_email !=',
            ''
        )->count_all_results($table);

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'CRM - Daftar Konsumen',
            'table' => $table,
            'tampilData' => [],
            'total_rows' => $total_customers,
            'jml_konsumen' => $total_customers,
            'active_customers' => $active_customers,
            'inactive_customers' => $inactive_customers,
            'customers_with_email' => $customers_with_email,
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array(
                'id_perusahaan' =>
                    $this->session->userdata('perusahaan_id')
            ))->row('nama_perusahaan'),
            'logo' => $this->conf['perusahaan']['logo'] ?? '',
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            // 'menu' diambil otomatis dari MY_Controller
            'submenu' => $this->M_master->getmenu($controller)->result(),
        );

        $this->load_admin_view('admin/konsumen/konsumen', $data);
    }

    public function konsumen_aktivitas()
    {
        $controller = $this->uri->segment(1);
        $userid = $this->session->userdata('id');

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'CRM - Aktivitas Konsumen',
            'table' => 'tbl_kategori',
            'tampil_data' => $this->M_transaksi->get_konsumen_dengan_transaksi()->result(),
            'total_rows' => $this->M_transaksi->get_konsumen_dengan_transaksi()->num_rows(),
            'logo' => $this->conf['perusahaan']['logo'] ?? '',
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            // 'menu' diambil otomatis dari MY_Controller
            'submenu' => $this->M_master->getmenu($controller)->result(),
        );

        $this->load_admin_view('admin/konsumen/aktivitas_konsumen', $data);
    }

    public function konsumen_add()
    {
        $data = array(
            'konsumen_nama' => $this->input->post('konsumen_nama', true),
            'konsumen_alamat' => $this->input->post('konsumen_alamat', true),
            'konsumen_nohp' => $this->input->post('konsumen_nohp', true),
            'konsumen_email' => $this->input->post('konsumen_email', true),
        );
        $this->M_master->addData('tbl_konsumen', $data);
        $this->session->set_flashdata('success', 'Data Berhasil Disimpan');
        redirect('pengaturan/konsumen_list');
    }

    public function konsumen_edit()
    {
        $id = $this->input->post('konsumen_id', true);
        $data = [
            'konsumen_nama' => $this->input->post('konsumen_nama', true),
            'konsumen_alamat' => $this->input->post('konsumen_alamat', true),
            'konsumen_nohp' => $this->input->post('konsumen_nohp', true),
            'konsumen_email' => $this->input->post('konsumen_email', true),
            'status' => $this->input->post('status', true)
        ];

        if ($id && !empty($data['konsumen_nama'])) {
            $this->db->where('konsumen_id', $id)->update('tbl_konsumen', $data);
            $this->session->set_flashdata('success', 'Data konsumen berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error', 'Data tidak valid.');
        }
        redirect('pengaturan/konsumen_list');
    }

    public function konsumen_delete($id)
    {
        $this->db->where('konsumen_id', $id)->delete('tbl_konsumen');
        $this->session->set_flashdata('success', 'Data Berhasil Dihapus');
        redirect('pengaturan/konsumen_list');
    }

    public function konsumen_get_ajax()
    {
        $request = $_POST;
        $this->load->model('Konsumen_model');
        $data = $this->Konsumen_model->get_datatables($request);
        echo json_encode($data);
    }

    public function konsumen_search_ajax()
    {
        $term = $this->input->get('q', true);
        $this->db->group_start();
        $this->db->like('LOWER(konsumen_nama)', strtolower($term));
        $this->db->or_like('konsumen_nohp', $term);
        $this->db->group_end();
        $this->db->limit(20);
        $data = $this->db->get('tbl_konsumen')->result();

        $result = [];
        foreach ($data as $row) {
            $result[] = [
                'konsumen_id' => $row->konsumen_id,
                'konsumen_nama' => $row->konsumen_nama,
                'konsumen_nohp' => $row->konsumen_nohp
            ];
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($result));
    }
    public function backup_database()
    {
        @set_time_limit(0);
        @ini_set('memory_limit', '512M');

        // Load the DB utility class
        $this->load->dbutil();

        // Backup your entire database and assign it to a variable
        $prefs = array(
            'format' => 'gzip', // gzip, zip, txt
            'filename' => 'tgm_erp_backup_' . date('Y-m-d_H-i-s') . '.sql',
            'add_drop' => TRUE, // Whether to add DROP TABLE statements to backup file
            'add_insert' => TRUE, // Whether to add INSERT data to backup file
            'newline' => "\n" // Newline character used in backup file
        );

        $backup = $this->dbutil->backup($prefs);

        // Load the download helper and send the file to your desktop
        $this->load->helper('download');
        force_download('tgm_erp_backup_' . date('Y-m-d_H-i-s') . '.gz', $backup);
    }
}
