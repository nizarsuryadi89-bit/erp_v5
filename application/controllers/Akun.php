<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller Akun - Manajemen Akun Pengguna
 * 
 * Controller ini menangani semua operasi terkait manajemen akun pengguna
 * termasuk CRUD, pencarian, filter, dan ekspor data.
 * 
 * @author TGM ERP System
 * @version 1.0
 * @since 2024
 */
class Akun extends MY_Controller
{
    /**
     * Constructor
     * 
     * Memuat model yang diperlukan dan melakukan inisialisasi
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_master');
    }

    /**
     * Memeriksa apakah user saat ini diizinkan mengubah target_user
     */
    private function _can_modify_user($target_user_id)
    {
        $target = $this->db->get_where('users', ['user_id' => $target_user_id])->row();
        if ($target && in_array($target->role_id, [1, 17])) {
            // Target adalah Developer/Owner. Hanya Developer/Owner yang boleh mengubahnya.
            if (!in_array($this->session->userdata('level'), [1, 17])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Menampilkan halaman utama manajemen akun
     * 
     * @return void
     */
    public function index()
    {
        // Get comprehensive statistics for initial load
        $data['total_rows'] = $this->db->count_all('users');
        $data['active_users'] = $this->db->where('active', 1)->count_all_results('users');
        $data['inactive_users'] = $this->db->where('active', 0)->count_all_results('users');
        $data['blocked_users'] = $this->db->where('active', 2)->count_all_results('users');

        // Get recent activity (last 7 days)
        $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')));
        $data['recent_users'] = $this->db->count_all_results('users');

        // Get users by role statistics
        $this->db->select('roles.role_name, COUNT(users.user_id) as total');
        $this->db->from('users');
        $this->db->join('roles', 'roles.role_id = users.role_id', 'left');
        $this->db->group_by('roles.role_id, roles.role_name');
        $data['users_by_role'] = $this->db->get()->result();

        // Get users by company statistics
        $this->db->select('tbl_perusahaan.nama_perusahaan, COUNT(users.user_id) as total');
        $this->db->from('users');
        $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = users.karyawan_id', 'left');
        $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_karyawan.perusahaan_id', 'left');
        $this->db->group_by('tbl_perusahaan.id_perusahaan, tbl_perusahaan.nama_perusahaan');
        $data['users_by_company'] = $this->db->get()->result();

        // Get system health metrics
        $data['system_health'] = $this->getSystemHealth();

        // Get recent activities
        $data['recent_activities'] = $this->getRecentActivities();

        // Get initial data for first page (will be replaced by AJAX)
        $this->db->select('users.*, tbl_karyawan.nama_lengkap as nama_karyawan, tbl_karyawan.foto, roles.role_name, tbl_perusahaan.nama_perusahaan, tbl_operator.operator_nama');
        $this->db->from('users');
        $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = users.karyawan_id', 'left');
        $this->db->join('roles', 'roles.role_id = users.role_id', 'left');
        $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_karyawan.perusahaan_id', 'left');
        $this->db->join('tbl_operator', 'tbl_operator.operator_id = users.operator_id', 'left');
        $this->db->order_by('roles.no_urut', 'asc');
        $this->db->limit(10, 0); // Get first 10 records for initial display
        $data['tampilData'] = $this->db->get()->result();

        // Get total records for initial pagination info
        $total_records = $this->db->count_all('users');
        $data['total_pages'] = ceil($total_records / 10);
        $data['current_page'] = 1;
        $data['total_records'] = $total_records;

        // Get unregistered employees for add form
        $subquery = $this->db->select('karyawan_id')->from('users')->get_compiled_select();
        $this->db->select('tbl_karyawan.*');
        $this->db->from('tbl_karyawan');
        $this->db->where("karyawan_id NOT IN ($subquery)", NULL, FALSE);
        $data['karyawan'] = $this->db->get()->result();

        // Get all employees for edit form
        $this->db->where("nik NOT LIKE 'STATION%'");
        $this->db->where("nik NOT LIKE 'TERM%'");
        $this->db->where('nama_lengkap NOT LIKE', '%Terminal%');
        $this->db->where('nama_lengkap NOT LIKE', '%OPERATOR%');
        $this->db->where('nama_lengkap NOT LIKE', '%DEPT%');
        $this->db->where('nama_lengkap NOT LIKE', '%DTG%');
        $this->db->where('nama_lengkap NOT LIKE', '%DTF%');
        $this->db->where('nama_lengkap NOT LIKE', '%WORK CENTER%');
        $this->db->where('nama_lengkap NOT LIKE', '%LARGE FORMAT%');
        $this->db->where('nama_lengkap NOT LIKE', '%A3+%');
        $data['list_karyawan'] = $this->db->get('tbl_karyawan')->result();

        // Get other required data
        $data['list_role'] = $this->db->get('roles')->result();
        $data['list_perusahaan'] = $this->db->get('tbl_perusahaan')->result();
        $data['list_operator'] = $this->db->get('tbl_operator')->result();

        // Load the correct view
        $this->load_admin_view('admin/akun/index', $data);
    }

    /**
     * AJAX method untuk mendapatkan data akun dengan pagination
     * 
     * @return void
     */
    public function get_data_ajax()
    {
        // Check if request is AJAX
        if (!$this->input->is_ajax_request()) {
            $this->output->set_status_header(400);
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $limit = 10; // Items per page
        $page = $this->input->post('page', true) ?: 1;
        $offset = ($page - 1) * $limit;

        // Get search and filter parameters
        $search = $this->input->post('search', true);
        $status = $this->input->post('status', true);
        $role = $this->input->post('role', true);
        $perusahaan = $this->input->post('perusahaan', true);

        // Get comprehensive statistics
        $total_rows = $this->db->count_all('users');
        $active_users = $this->db->where('active', 1)->count_all_results('users');
        $inactive_users = $this->db->where('active', 0)->count_all_results('users');
        $blocked_users = $this->db->where('active', 2)->count_all_results('users');

        // Build query for users with search and filter
        $this->db->select('users.*, tbl_karyawan.nama_lengkap as nama_karyawan, tbl_karyawan.foto, roles.role_name, tbl_perusahaan.nama_perusahaan, tbl_operator.operator_nama');
        $this->db->from('users');
        $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = users.karyawan_id', 'left');
        $this->db->join('roles', 'roles.role_id = users.role_id', 'left');
        $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_karyawan.perusahaan_id', 'left');
        $this->db->join('tbl_operator', 'tbl_operator.operator_id = users.operator_id', 'left');

        // Apply search filter
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('users.username', $search);
            $this->db->or_like('users.email', $search);
            $this->db->or_like('tbl_karyawan.nama_lengkap', $search);
            $this->db->group_end();
        }

        // Apply status filter
        if ($status !== '' && $status !== null) {
            $this->db->where('users.active', $status);
        }

        // Apply role filter
        if (!empty($role)) {
            $this->db->where('users.role_id', $role);
        }

        // Apply perusahaan filter
        if (!empty($perusahaan)) {
            $this->db->where('tbl_karyawan.perusahaan_id', $perusahaan);
        }

        $this->db->order_by('roles.no_urut', 'asc');
        $this->db->limit($limit, $offset);
        $data = $this->db->get()->result();

        // Get total records for pagination (without limit but with filters)
        $this->db->select('users.user_id');
        $this->db->from('users');
        $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = users.karyawan_id', 'left');
        $this->db->join('roles', 'roles.role_id = users.role_id', 'left');
        $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_karyawan.perusahaan_id', 'left');
        $this->db->join('tbl_operator', 'tbl_operator.operator_id = users.operator_id', 'left');

        // Apply same filters for count
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('users.username', $search);
            $this->db->or_like('users.email', $search);
            $this->db->or_like('tbl_karyawan.nama_lengkap', $search);
            $this->db->group_end();
        }

        if ($status !== '' && $status !== null) {
            $this->db->where('users.active', $status);
        }

        if (!empty($role)) {
            $this->db->where('users.role_id', $role);
        }

        if (!empty($perusahaan)) {
            $this->db->where('tbl_karyawan.perusahaan_id', $perusahaan);
        }

        $total_records = $this->db->count_all_results();
        $total_pages = ceil($total_records / $limit);
        $current_count = count($data);

        // Prepare response
        $response = [
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => (int) $page,
                'total_pages' => (int) $total_pages,
                'total_records' => (int) $total_records,
                'current_count' => (int) $current_count,
                'limit' => (int) $limit
            ],
            'stats' => [
                'total_rows' => (int) $total_rows,
                'active_users' => (int) $active_users,
                'inactive_users' => (int) $inactive_users,
                'blocked_users' => (int) $blocked_users
            ]
        ];

        $this->output->set_content_type('application/json');
        echo json_encode($response);
    }

    /**
     * Menambahkan akun pengguna baru
     * 
     * Memvalidasi input dan menyimpan data akun baru ke database
     * 
     * @return void
     */
    public function action_add_user()
    {
        // Set rules validasi
        $this->form_validation->set_rules('username', 'Username', 'required|trim|is_unique[users.username]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('karyawan_id', 'Nama Lengkap', 'required');
        $this->form_validation->set_rules('role_id', 'Hak Akses', 'required');

        if ($this->form_validation->run() == FALSE) {
            // Kembalikan ke halaman sebelumnya dengan pesan error
            $this->session->set_flashdata('error', validation_errors());
            redirect(base_url('akun'));
        }

        // Ambil input
        $user_data = [
            'username' => $this->input->post('username', TRUE),
            'password' => password_hash($this->input->post('password', TRUE), PASSWORD_DEFAULT),
            'email' => $this->input->post('email', TRUE),
            'karyawan_id' => $this->input->post('karyawan_id', TRUE),
            'role_id' => $this->input->post('role_id', TRUE),
            'operator_id' => $this->input->post('operator_id', true) ? $this->input->post('operator_id', TRUE) : null,
            'active' => 1, // default aktif
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Simpan ke database
        $insert = $this->db->insert('users', $user_data);

        if ($insert) {
            $this->session->set_flashdata('success', 'Akun berhasil ditambahkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambahkan akun.');
        }

        redirect(base_url('akun'));
    }

    /**
     * Mengedit data akun pengguna
     * 
     * Memvalidasi input dan memperbarui data akun yang ada
     * 
     * @return void
     */
    public function action_edit_user()
    {
        $user_id = $this->input->post('user_id', true);

        if (!$this->_can_modify_user($user_id)) {
            $this->session->set_flashdata('error', 'Akses ditolak! Anda tidak diizinkan memodifikasi akun Developer/Owner.');
            redirect('akun');
        }

        // Set validation rules
        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('karyawan_id', 'Nama Lengkap', 'required');
        $this->form_validation->set_rules('role_id', 'Hak Akses', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect(base_url('akun'));
        }

        // Check if username is unique (excluding current user)
        $existing_username = $this->db->where('username', $this->input->post('username', true))
            ->where('user_id !=', $user_id)
            ->get('users')->row();
        if ($existing_username) {
            $this->session->set_flashdata('error', 'Username sudah digunakan oleh akun lain.');
            redirect(base_url('akun'));
        }

        // Check if email is unique (excluding current user)
        $existing_email = $this->db->where('email', $this->input->post('email', true))
            ->where('user_id !=', $user_id)
            ->get('users')->row();
        if ($existing_email) {
            $this->session->set_flashdata('error', 'Email sudah digunakan oleh akun lain.');
            redirect(base_url('akun'));
        }

        // Update user data
        $user_data = [
            'username' => $this->input->post('username', TRUE),
            'email' => $this->input->post('email', TRUE),
            'karyawan_id' => $this->input->post('karyawan_id', TRUE),
            'role_id' => $this->input->post('role_id', TRUE),
            'operator_id' => $this->input->post('operator_id', true) ? $this->input->post('operator_id', TRUE) : null,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->where('user_id', $user_id);
        $update_user = $this->db->update('users', $user_data);

        // Update karyawan perusahaan_id if provided
        $karyawan_id = $this->input->post('karyawan_id', TRUE);
        $perusahaan_id = $this->input->post('perusahaan_id', TRUE);

        if ($karyawan_id && $perusahaan_id) {
            $this->db->where('karyawan_id', $karyawan_id);
            $update_karyawan = $this->db->update('tbl_karyawan', ['perusahaan_id' => $perusahaan_id]);
        }

        if ($update_user) {
            $this->session->set_flashdata('success', 'Akun berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui akun.');
        }
        redirect('akun');
    }

    /**
     * Reset password akun pengguna
     * 
     * Mengatur ulang password akun ke default password
     * 
     * @param int $user_id ID akun yang akan direset passwordnya
     * @return void
     */
    public function reset_password($user_id)
    {
        if (!$this->_can_modify_user($user_id)) {
            $this->session->set_flashdata('error', 'Akses ditolak! Anda tidak diizinkan mereset password Developer/Owner.');
            redirect('akun');
        }

        $this->db->where('user_id', $user_id);
        $update = $this->db->update('users', ['password' => password_hash('tgmprint', PASSWORD_DEFAULT)]);
        if ($update) {
            $this->session->set_flashdata('success', 'Password berhasil direset ke default.');
        } else {
            $this->session->set_flashdata('error', 'Gagal mereset password.');
        }
        redirect('akun');
    }

    /**
     * Menghapus akun pengguna
     * 
     * Menghapus akun pengguna dari database secara permanen
     * 
     * @param int $user_id ID akun yang akan dihapus
     * @return void
     */
    public function delete_user($user_id)
    {
        if ($user_id == $this->session->userdata('id')) {
            $this->session->set_flashdata('error', 'Akses ditolak! Anda tidak dapat menghapus akun Anda sendiri.');
            redirect('akun');
        }

        if (!$this->_can_modify_user($user_id)) {
            $this->session->set_flashdata('error', 'Akses ditolak! Anda tidak diizinkan menghapus akun Developer/Owner.');
            redirect('akun');
        }

        $this->db->where('user_id', $user_id);
        $delete = $this->db->delete('users');
        if ($delete) {
            $this->session->set_flashdata('success', 'Akun berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus akun.');
        }
        redirect('akun');
    }

    /**
     * Mendapatkan data akun dengan pencarian dan filter (AJAX)
     * 
     * Mengembalikan data akun dalam format JSON untuk keperluan AJAX
     * 
     * @return void
     */
    public function get_users_data()
    {
        // Get search parameters
        $search = $this->input->get('search', true);
        $status = $this->input->get('status', true);
        $role = $this->input->get('role', true);
        $perusahaan = $this->input->get('perusahaan', true);

        // Base query
        $this->db->select('users.*, tbl_karyawan.nama_lengkap as nama_karyawan, tbl_karyawan.foto, roles.role_name, tbl_perusahaan.nama_perusahaan');
        $this->db->from('users');
        $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = users.karyawan_id', 'left');
        $this->db->join('roles', 'roles.role_id = users.role_id', 'left');
        $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_karyawan.perusahaan_id', 'left');

        // Apply search filter
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('users.username', $search);
            $this->db->or_like('users.email', $search);
            $this->db->or_like('tbl_karyawan.nama_lengkap', $search);
            $this->db->group_end();
        }

        // Apply status filter
        if ($status !== '' && $status !== null) {
            $this->db->where('users.active', $status);
        }

        // Apply role filter
        if (!empty($role)) {
            $this->db->where('users.role_id', $role);
        }

        // Apply perusahaan filter
        if (!empty($perusahaan)) {
            $this->db->where('tbl_karyawan.perusahaan_id', $perusahaan);
        }

        $this->db->order_by('users.created_at', 'DESC');

        $result = $this->db->get()->result();

        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $result,
            'total' => count($result)
        ]);
    }

    /**
     * Mendapatkan detail akun berdasarkan ID (AJAX)
     * 
     * @param int $user_id ID akun yang akan diambil detailnya
     * @return void
     */
    public function get_user_detail($user_id)
    {
        $this->db->select('users.*, tbl_karyawan.nama_lengkap as nama_karyawan, tbl_karyawan.foto, tbl_karyawan.email as karyawan_email, tbl_karyawan.perusahaan_id, roles.role_name, tbl_perusahaan.nama_perusahaan');
        $this->db->from('users');
        $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = users.karyawan_id', 'left');
        $this->db->join('roles', 'roles.role_id = users.role_id', 'left');
        $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_karyawan.perusahaan_id', 'left');
        $this->db->where('users.user_id', $user_id);

        $user = $this->db->get()->row();

        if ($user) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $user
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ]);
        }
    }

    /**
     * Mengaktifkan/menonaktifkan akun pengguna
     * 
     * @param int $user_id ID akun yang akan diubah statusnya
     * @return void
     */
    public function toggle_user_status($user_id)
    {
        if ($user_id == $this->session->userdata('id')) {
            $this->session->set_flashdata('error', 'Akses ditolak! Anda tidak dapat menonaktifkan akun Anda sendiri.');
            redirect('akun');
        }

        if (!$this->_can_modify_user($user_id)) {
            $this->session->set_flashdata('error', 'Akses ditolak! Anda tidak diizinkan memodifikasi akun Developer/Owner.');
            redirect('akun');
        }

        // Get current status
        $this->db->select('active');
        $this->db->where('user_id', $user_id);
        $user = $this->db->get('users')->row();

        if ($user) {
            $new_status = ($user->active == 1) ? 0 : 1;
            $status_text = ($new_status == 1) ? 'diaktifkan' : 'dinonaktifkan';

            $this->db->where('user_id', $user_id);
            $update = $this->db->update('users', ['active' => $new_status]);

            if ($update) {
                $this->session->set_flashdata('success', "Akun berhasil $status_text.");
            } else {
                $this->session->set_flashdata('error', 'Gagal mengubah status akun.');
            }
        } else {
            $this->session->set_flashdata('error', 'Akun tidak ditemukan.');
        }

        redirect('akun');
    }

    /**
     * Memblokir akun pengguna
     * 
     * @param int $user_id ID akun yang akan diblokir
     * @return void
     */
    public function block_user($user_id)
    {
        if ($user_id == $this->session->userdata('id')) {
            $this->session->set_flashdata('error', 'Akses ditolak! Anda tidak dapat memblokir akun Anda sendiri.');
            redirect('akun');
        }

        if (!$this->_can_modify_user($user_id)) {
            $this->session->set_flashdata('error', 'Akses ditolak! Anda tidak diizinkan memblokir akun Developer/Owner.');
            redirect('akun');
        }

        $this->db->where('user_id', $user_id);
        $update = $this->db->update('users', ['active' => 2]);

        if ($update) {
            $this->session->set_flashdata('success', 'Akun berhasil diblokir.');
        } else {
            $this->session->set_flashdata('error', 'Gagal memblokir akun.');
        }

        redirect('akun');
    }

    /**
     * Mengekspor data akun ke Excel
     * 
     * Mengunduh file Excel berisi data semua akun pengguna
     * 
     * @return void
     */
    public function export_users()
    {
        // Load library PHPExcel
        $this->load->library('PHPExcel');

        // Get search and filter parameters
        $search = $this->input->get('search', true);
        $status = $this->input->get('status', true);
        $role = $this->input->get('role', true);
        $perusahaan = $this->input->get('perusahaan', true);

        // Get all users data with filters
        $this->db->select('users.*, tbl_karyawan.nama_lengkap as nama_karyawan, roles.role_name, tbl_perusahaan.nama_perusahaan');
        $this->db->from('users');
        $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = users.karyawan_id', 'left');
        $this->db->join('roles', 'roles.role_id = users.role_id', 'left');
        $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_karyawan.perusahaan_id', 'left');

        // Apply search filter
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('users.username', $search);
            $this->db->or_like('users.email', $search);
            $this->db->or_like('tbl_karyawan.nama_lengkap', $search);
            $this->db->group_end();
        }

        // Apply status filter
        if ($status !== '' && $status !== null) {
            $this->db->where('users.active', $status);
        }

        // Apply role filter
        if (!empty($role)) {
            $this->db->where('users.role_id', $role);
        }

        // Apply perusahaan filter
        if (!empty($perusahaan)) {
            $this->db->where('tbl_karyawan.perusahaan_id', $perusahaan);
        }

        $this->db->order_by('users.created_at', 'DESC');
        $users = $this->db->get()->result();

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("TGM ERP System")
            ->setLastModifiedBy("TGM ERP System")
            ->setTitle("Data Akun Pengguna")
            ->setSubject("Data Akun Pengguna")
            ->setDescription("Data akun pengguna sistem TGM ERP")
            ->setKeywords("akun pengguna")
            ->setCategory("Data Akun");

        // Add header
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Nama Karyawan')
            ->setCellValue('C1', 'Username')
            ->setCellValue('D1', 'Email')
            ->setCellValue('E1', 'Store')
            ->setCellValue('F1', 'Hak Akses')
            ->setCellValue('G1', 'Status')
            ->setCellValue('H1', 'Tanggal Dibuat');

        // Style header
        $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');

        // Add data
        $no = 1;
        $row = 2;
        foreach ($users as $user) {
            $status = '';
            switch ($user->active) {
                case 1:
                    $status = 'Aktif';
                    break;
                case 0:
                    $status = 'Tidak Aktif';
                    break;
                case 2:
                    $status = 'Diblokir';
                    break;
                default:
                    $status = 'Tidak Diketahui';
            }

            $objPHPExcel->getActiveSheet()
                ->setCellValue('A' . $row, $no++)
                ->setCellValue('B' . $row, $user->nama_karyawan)
                ->setCellValue('C' . $row, $user->username)
                ->setCellValue('D' . $row, $user->email)
                ->setCellValue('E' . $row, $user->nama_perusahaan)
                ->setCellValue('F' . $row, $user->role_name)
                ->setCellValue('G' . $row, $status)
                ->setCellValue('H' . $row, date('d/m/Y H:i', strtotime($user->created_at)));
            $row++;
        }

        // Auto size columns
        foreach (range('A', 'H') as $col) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
        }

        // Set active sheet index to the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to client browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Data_Akun_Pengguna_' . date('Y-m-d_H-i-s') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    /**
     * Mendapatkan metrics kesehatan sistem
     * 
     * @return array
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
     * Mendapatkan aktivitas terbaru
     * 
     * @return array
     */
    private function getRecentActivities()
    {
        $activities = [];

        // Get recent user creations
        $this->db->select('users.*, tbl_karyawan.nama_lengkap, roles.role_name');
        $this->db->from('users');
        $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = users.karyawan_id', 'left');
        $this->db->join('roles', 'roles.role_id = users.role_id', 'left');
        $this->db->where('users.created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')));
        $this->db->order_by('users.created_at', 'DESC');
        $this->db->limit(5);
        $recent_users = $this->db->get()->result();

        foreach ($recent_users as $user) {
            $activities[] = [
                'type' => 'user_created',
                'message' => "Akun baru dibuat untuk {$user->nama_lengkap}",
                'time' => $user->created_at,
                'user' => $user->nama_lengkap,
                'role' => $user->role_name
            ];
        }

        return $activities;
    }

    /**
     * Mendapatkan data karyawan berdasarkan ID (AJAX)
     * 
     * @param int $karyawan_id ID karyawan yang akan diambil datanya
     * @return void
     */
    public function get_karyawan_data($karyawan_id)
    {
        $this->db->select('karyawan_id, nama_lengkap, email, foto');
        $this->db->from('tbl_karyawan');
        $this->db->where('karyawan_id', $karyawan_id);

        $karyawan = $this->db->get()->row();

        if ($karyawan) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $karyawan
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan'
            ]);
        }
    }

    /**
     * Mendapatkan analytics yang komprehensif (AJAX)
     * 
     * @return void
     */
    public function get_analytics()
    {
        $analytics = [];

        // User growth over time
        $analytics['user_growth'] = $this->getUserGrowth();

        // Role distribution
        $analytics['role_distribution'] = $this->getRoleDistribution();

        // Company distribution
        $analytics['company_distribution'] = $this->getCompanyDistribution();

        // Activity trends
        $analytics['activity_trends'] = $this->getActivityTrends();

        // System performance
        $analytics['system_performance'] = $this->getSystemPerformance();

        // Security metrics
        $analytics['security_metrics'] = $this->getSecurityMetrics();

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $analytics
        ]);
    }

    /**
     * Mendapatkan pertumbuhan user
     */
    private function getUserGrowth()
    {
        $growth = [];

        // Last 30 days
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $this->db->where('DATE(created_at)', $date);
            $count = $this->db->count_all_results('users');

            $growth[] = [
                'date' => $date,
                'count' => $count
            ];
        }

        return $growth;
    }

    /**
     * Mendapatkan distribusi role
     */
    private function getRoleDistribution()
    {
        $this->db->select('roles.role_name, COUNT(users.user_id) as total');
        $this->db->from('users');
        $this->db->join('roles', 'roles.role_id = users.role_id', 'left');
        $this->db->group_by('roles.role_id, roles.role_name');
        $this->db->order_by('total', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Mendapatkan distribusi perusahaan
     */
    private function getCompanyDistribution()
    {
        $this->db->select('tbl_perusahaan.nama_perusahaan, COUNT(users.user_id) as total');
        $this->db->from('users');
        $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = users.karyawan_id', 'left');
        $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_karyawan.perusahaan_id', 'left');
        $this->db->group_by('tbl_perusahaan.id_perusahaan, tbl_perusahaan.nama_perusahaan');
        $this->db->order_by('total', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Mendapatkan tren aktivitas
     */
    private function getActivityTrends()
    {
        $trends = [];

        // Last 7 days activity
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));

            // New users
            $this->db->where('DATE(created_at)', $date);
            $new_users = $this->db->count_all_results('users');

            // Updated users
            $this->db->where('DATE(updated_at)', $date);
            $updated_users = $this->db->count_all_results('users');

            $trends[] = [
                'date' => $date,
                'new_users' => $new_users,
                'updated_users' => $updated_users
            ];
        }

        return $trends;
    }

    /**
     * Mendapatkan performa sistem
     */
    private function getSystemPerformance()
    {
        $performance = [];

        // Database performance
        $start_time = microtime(true);
        $this->db->count_all('users');
        $end_time = microtime(true);
        $performance['db_query_time'] = round(($end_time - $start_time) * 1000, 2); // milliseconds

        // Memory usage (simulated)
        $performance['memory_usage'] = round(memory_get_usage(true) / 1024 / 1024, 2); // MB

        // CPU usage (simulated)
        $performance['cpu_usage'] = rand(10, 50); // percentage

        return $performance;
    }

    /**
     * Mendapatkan metrics keamanan
     */
    private function getSecurityMetrics()
    {
        $security = [];

        // Blocked users
        $security['blocked_users'] = $this->db->where('active', 2)->count_all_results('users');

        // Inactive users
        $security['inactive_users'] = $this->db->where('active', 0)->count_all_results('users');

        // Users with default password (simulated check)
        $security['default_password_users'] = rand(0, 5);

        // Failed login attempts (simulated)
        $security['failed_logins'] = rand(0, 10);

        return $security;
    }

    /**
     * Bulk operations untuk multiple users
     * 
     * @return void
     */
    public function bulk_operations()
    {
        $action = $this->input->post('action', true);
        $user_ids = $this->input->post('user_ids', true);

        if (empty($user_ids)) {
            $this->session->set_flashdata('error', 'Tidak ada user yang dipilih.');
            redirect('akun');
        }

        $success_count = 0;
        $error_count = 0;

        switch ($action) {
            case 'activate':
                foreach ($user_ids as $user_id) {
                    if (!$this->_can_modify_user($user_id)) { $error_count++; continue; }
                    $this->db->where('user_id', $user_id);
                    if ($this->db->update('users', ['active' => 1])) {
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                }
                $message = "Berhasil mengaktifkan $success_count akun.";
                break;

            case 'deactivate':
                foreach ($user_ids as $user_id) {
                    if ($user_id == $this->session->userdata('id') || !$this->_can_modify_user($user_id)) { $error_count++; continue; }
                    $this->db->where('user_id', $user_id);
                    if ($this->db->update('users', ['active' => 0])) {
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                }
                $message = "Berhasil menonaktifkan $success_count akun.";
                break;

            case 'block':
                foreach ($user_ids as $user_id) {
                    if ($user_id == $this->session->userdata('id') || !$this->_can_modify_user($user_id)) { $error_count++; continue; }
                    $this->db->where('user_id', $user_id);
                    if ($this->db->update('users', ['active' => 2])) {
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                }
                $message = "Berhasil memblokir $success_count akun.";
                break;

            case 'reset_password':
                $new_hash = password_hash('tgmprint', PASSWORD_DEFAULT);
                foreach ($user_ids as $user_id) {
                    if (!$this->_can_modify_user($user_id)) { $error_count++; continue; }
                    $this->db->where('user_id', $user_id);
                    if ($this->db->update('users', ['password' => $new_hash])) {
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                }
                $message = "Berhasil reset password (menjadi 'tgmprint') untuk $success_count akun.";
                break;

            case 'delete':
                foreach ($user_ids as $user_id) {
                    if ($user_id == $this->session->userdata('id') || !$this->_can_modify_user($user_id)) { $error_count++; continue; }
                    if ($this->db->delete('users', ['user_id' => $user_id])) {
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                }
                $message = "Berhasil menghapus $success_count akun.";
                break;

            default:
                $this->session->set_flashdata('error', 'Aksi tidak valid.');
                redirect('akun');
        }

        if ($error_count > 0) {
            $message .= " Gagal memproses $error_count akun.";
        }

        $this->session->set_flashdata('success', $message);
        redirect('akun');
    }

    /**
     * Advanced search dengan multiple criteria
     * 
     * @return void
     */
    public function advanced_search()
    {
        $criteria = $this->input->post();

        $this->db->select('users.*, tbl_karyawan.nama_lengkap, tbl_karyawan.foto, roles.role_name, tbl_perusahaan.nama_perusahaan');
        $this->db->from('users');
        $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = users.karyawan_id', 'left');
        $this->db->join('roles', 'roles.role_id = users.role_id', 'left');
        $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_karyawan.perusahaan_id', 'left');

        // Apply advanced filters
        if (!empty($criteria['username'])) {
            $this->db->like('users.username', $criteria['username']);
        }

        if (!empty($criteria['email'])) {
            $this->db->like('users.email', $criteria['email']);
        }

        if (!empty($criteria['nama_karyawan'])) {
            $this->db->like('tbl_karyawan.nama_lengkap', $criteria['nama_karyawan']);
        }

        if (!empty($criteria['status'])) {
            $this->db->where('users.active', $criteria['status']);
        }

        if (!empty($criteria['role_id'])) {
            $this->db->where('users.role_id', $criteria['role_id']);
        }

        if (!empty($criteria['perusahaan_id'])) {
            $this->db->where('tbl_karyawan.perusahaan_id', $criteria['perusahaan_id']);
        }

        if (!empty($criteria['date_from'])) {
            $this->db->where('users.created_at >=', $criteria['date_from'] . ' 00:00:00');
        }

        if (!empty($criteria['date_to'])) {
            $this->db->where('users.created_at <=', $criteria['date_to'] . ' 23:59:59');
        }

        $this->db->order_by('users.created_at', 'DESC');

        $results = $this->db->get()->result();

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $results,
            'total' => count($results)
        ]);
    }
    // {
    //     $security = [];

    //     // Blocked users
    //     $security['blocked_users'] = $this->db->where('active', 2)->count_all_results('users');

    //     // Inactive users
    //     $security['inactive_users'] = $this->db->where('active', 0)->count_all_results('users');

    //     // Users with default password (simulated check)
    //     $security['default_password_users'] = rand(0, 5);

    //     // Failed login attempts (simulated)
    //     $security['failed_logins'] = rand(0, 10);

    //     return $security;
    // }

    /**
     * Mendapatkan statistik akun pengguna (AJAX) - Legacy function
     * 
     * Mengembalikan data statistik dalam format JSON
     * 
     * @return void
     */
    public function get_user_statistics()
    {
        // Get various statistics
        $total_users = $this->db->count_all('users');
        $active_users = $this->db->where('active', 1)->count_all_results('users');
        $inactive_users = $this->db->where('active', 0)->count_all_results('users');
        $blocked_users = $this->db->where('active', 2)->count_all_results('users');

        // Get users by role
        $this->db->select('roles.role_name, COUNT(users.user_id) as total');
        $this->db->from('users');
        $this->db->join('roles', 'roles.role_id = users.role_id', 'left');
        $this->db->group_by('roles.role_id');
        $users_by_role = $this->db->get()->result();

        // Get users by perusahaan
        $this->db->select('tbl_perusahaan.nama_perusahaan, COUNT(users.user_id) as total');
        $this->db->from('users');
        $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = users.karyawan_id', 'left');
        $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_karyawan.perusahaan_id', 'left');
        $this->db->group_by('tbl_perusahaan.id_perusahaan');
        $users_by_perusahaan = $this->db->get()->result();

        // Get recent users (last 7 days)
        $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')));
        $recent_users = $this->db->count_all_results('users');

        $statistics = [
            'total_users' => $total_users,
            'active_users' => $active_users,
            'inactive_users' => $inactive_users,
            'blocked_users' => $blocked_users,
            'users_by_role' => $users_by_role,
            'users_by_perusahaan' => $users_by_perusahaan,
            'recent_users' => $recent_users
        ];

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * Mencetak daftar akun pengguna
     * 
     * @return void
     */
    public function cetak()
    {
        $this->db->select('users.*, tbl_karyawan.nama_lengkap as nama_karyawan, roles.role_name');
        $this->db->from('users');
        $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = users.karyawan_id', 'left');
        $this->db->join('roles', 'roles.role_id = users.role_id', 'left');
        $this->db->order_by('roles.no_urut', 'asc');
        $data['tampilData'] = $this->db->get()->result();
        $data['title'] = 'Daftar Akun Pengguna';

        $this->load->view('admin/akun/cetak', $data);
    }
}

?>