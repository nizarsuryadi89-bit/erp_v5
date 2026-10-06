<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_auth');
        $this->load->library('form_validation');
        $this->load->library('session');
    }

    function index()
    {
        $this->session->unset_userdata('is_owner_proxy');
        $data = [
            'conf' => $this->db->get('tbl_config')->result(),
            'title' => 'Login'
        ];

        $this->load->view('layout_admin/head', $data);
        $this->load->view('auth/index', $data);
        $this->load->view('layout_admin/footer');
    }

    public function proses_login()
    {
        $this->session->unset_userdata('is_owner_proxy');
        $username = $this->input->post('username', TRUE);
        $inputPassword = $this->input->post('password', TRUE); // ← TANPA md5!

        $user = $this->M_auth->login($username)->row_array();

        if ($user) {
            if ($user['active'] == '1') {
                $dbPassword = $user['password'];

                // Cocokkan password
                if (password_verify($inputPassword, $dbPassword) || (strlen($dbPassword) <= 32 && md5($inputPassword) === $dbPassword)) {

                    // Jika hash lama masih md5, upgrade ke password_hash
                    if (strlen($dbPassword) <= 32) {
                        $newHash = password_hash($inputPassword, PASSWORD_DEFAULT);
                        $this->db->where('user_id', $user['user_id'])->update('users', ['password' => $newHash]);
                    }

                    // Jika dari auth login web (pasti tidak ada FCM token post). Namun di future jika ada input device token.
                    $fcm_token = $this->input->post('fcm_token', true);
                    if ($fcm_token) {
                        if ($this->db->field_exists('fcm_token', 'tbl_karyawan')) {
                            $this->db->where('karyawan_id', $user['karyawan_id']);
                            $this->db->update('tbl_karyawan', ['fcm_token' => $fcm_token]);
                        } else if ($this->db->field_exists('fcm_token', 'users')) {
                            $this->db->where('user_id', $user['user_id']);
                            $this->db->update('users', ['fcm_token' => $fcm_token]);
                        }
                    }

                    // Panggil handler login sukses terpusat
                    $this->_login_success_handler($user);

                } else {
                    // $this->session->set_flashdata('error', 'Password salah');
                    redirect('auth');
                    $this->session->unset_userdata('success');
                }
            } else {
                $this->session->set_flashdata('error', 'Akun belum aktif');
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('error', 'Username tidak terdaftar');
            redirect('auth');
        }
    }

    /**
     * Inisiasi Login dengan Google OAuth 2.0
     */
    public function google_login()
    {
        $this->session->unset_userdata('is_owner_proxy');
        $this->load->library('google_oauth');

        if (!$this->google_oauth->is_configured()) {
            $this->session->set_flashdata('error', 'Fitur Login Google belum dikonfigurasi (Client ID / Secret kosong). Silakan hubungi Administrator.');
            redirect('auth');
            return;
        }

        // Generate CSRF anti-tampering state
        $state = bin2hex(random_bytes(16));
        $this->session->set_userdata('oauth2state', $state);

        $auth_url = $this->google_oauth->get_login_url($state);
        redirect($auth_url);
    }

    /**
     * Callback dari Google setelah pengguna memilih akun
     */
    public function google_callback()
    {
        $this->session->unset_userdata('is_owner_proxy');
        $this->load->library('google_oauth');

        $error = $this->input->get('error');
        if ($error) {
            $this->session->set_flashdata('error', 'Login Google dibatalkan.');
            redirect('auth');
            return;
        }

        $code = $this->input->get('code');
        $state = $this->input->get('state');
        $session_state = $this->session->userdata('oauth2state');
        $this->session->unset_userdata('oauth2state');

        if (empty($code)) {
            $this->session->set_flashdata('error', 'Kode otorisasi Google tidak ditemukan.');
            redirect('auth');
            return;
        }

        // Verifikasi CSRF State
        if (!empty($session_state) && $state !== $session_state) {
            $this->session->set_flashdata('error', 'Validasi sesi Google tidak cocok. Silakan coba kembali.');
            redirect('auth');
            return;
        }

        // Dapatkan data profil user dari Google
        $result = $this->google_oauth->get_user_info($code);
        if (!$result['status']) {
            $this->session->set_flashdata('error', $result['error']);
            redirect('auth');
            return;
        }

        $google_user = $result['data'];
        $google_email = isset($google_user['email']) ? trim($google_user['email']) : '';
        $google_id = isset($google_user['sub']) ? $google_user['sub'] : null;

        if (empty($google_email)) {
            $this->session->set_flashdata('error', 'Akun Google Anda tidak menyediakan alamat email.');
            redirect('auth');
            return;
        }

        // 1. Cari user berdasarkan google_id terlebih dahulu
        $user = null;
        if (!empty($google_id)) {
            $user = $this->M_auth->get_by_google_id($google_id)->row_array();
        }

        // 2. Jika belum pernah tertaut, cari berdasarkan email
        if (!$user) {
            $user = $this->M_auth->get_by_email($google_email)->row_array();
            // Tautkan google_id ke user tersebut untuk login selanjutnya
            if ($user && !empty($google_id)) {
                $this->M_auth->update_google_id($user['user_id'], $google_id);
            }
        }

        // Jika email tidak terdaftar di sistem ERP
        if (!$user) {
            $this->session->set_flashdata('error', 'Email Google (' . htmlspecialchars($google_email) . ') belum terdaftar di sistem ERP. Silakan hubungi Administrator.');
            redirect('auth');
            return;
        }

        // Jika akun tidak aktif
        if ($user['active'] != '1') {
            $this->session->set_flashdata('error', 'Akun pengguna belum aktif.');
            redirect('auth');
            return;
        }

        // Login sukses
        $google_photo = isset($google_user['picture']) ? $google_user['picture'] : null;
        $this->_login_success_handler($user, $google_photo);
    }

    /**
     * Handler terpusat untuk setting session, log, dan redirect login sukses
     */
    private function _login_success_handler($user, $custom_photo = null)
    {
        $role_name = !empty($user['role_name']) ? $user['role_name'] : (!empty($user['role_nama']) ? $user['role_nama'] : '');
        $user_img = !empty($user['foto']) ? $user['foto'] : (!empty($user['images']) ? $user['images'] : $custom_photo);

        // Set session
        $data = array(
            'id' => $user['user_id'],
            'karyawan_id' => $user['karyawan_id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'level' => $user['role_id'],
            'nama_level' => $role_name,
            // Fallback to perusahaan_id1 (from users table) if perusahaan_id (from tbl_karyawan) is missing
            'perusahaan_id' => !empty($user['perusahaan_id']) ? $user['perusahaan_id'] : $user['perusahaan_id1'],
            'user_img' => $user_img,
            'logged_in' => TRUE,
        );
        $this->session->set_userdata($data);
        $this->session->set_flashdata('login', 'Berhasil login');

        // Log login
        $this->db->insert('user_log', [
            'user_id' => $user['user_id'],
            'username' => $user['username'],
            'login_time' => date('Y-m-d H:i:s')
        ]);

        // Update Last Login
        $this->db->where('user_id', $user['user_id'])->update('users', ['last_login' => date('Y-m-d H:i:s')]);

        // REDIRECT KHUSUS STATION MODE / AKUN PRODUKSI
        if ($user['role_id'] == 99) {
            redirect('wip/produksi');
        } elseif (strpos($user['username'], 'pb_') === 0) {
            redirect('Pb'); // Langsung ke Antrian Pengambilan Barang
        } elseif ($user['username'] == 'produksi' || strpos($user['username'], 'op_') === 0 || strpos($user['username'], 'fn_') === 0 || strpos($user['username'], 'produksi_') === 0) {
            redirect('wip/produksi'); // Langsung ke Antrian Produksi
        } else {
            // Deteksi jika login dari HP, arahkan ke mobile. Jika dari PC, ke Dashboard Desktop.
            $this->load->library('user_agent');
            if ($this->agent->is_mobile()) {
                redirect('mobile'); // Masuk ke halaman mobile
            } else {
                redirect('Dashboard'); // Kembali ke Dashbord ERP Utama Desktop
            }
        }
    }

    function logout()
    {
        $this->session->sess_destroy();
        redirect('auth');
    }

    /**
     * Login required page
     * Displayed when user tries to access protected page without logging in
     */
    public function login_required()
    {
        $username = $this->session->userdata('username');
        $roleid = $this->session->userdata('role_id');

        $data = [
            'perusahaan' => $this->db->get('tbl_perusahaan')->row_array(),
            'title' => 'Login Required'
        ];

        // Only load models if user data exists
        if ($username) {
            $this->load->model(['M_users', 'M_login', 'M_karyawan']);
            $data['users'] = $this->M_users->getUsers($username)->row_array();
            $data['cekusername'] = $this->M_login->cekusernamekasir($username)->row_array();
            $data['role_id'] = $roleid;
            $data['tampildata'] = $this->M_karyawan->tampildata()->result_array();
        }

        $this->load->view('please_login', $data);
    }

    /**
     * Access denied page
     * Displayed when user tries to access page they don't have permission for
     */
    public function access_denied()
    {
        $username = $this->session->userdata('username');
        $roleid = $this->session->userdata('role_id');

        $data = [
            'perusahaan' => $this->db->get('tbl_perusahaan')->row_array(),
            'title' => 'Access Denied'
        ];

        // Only load models if user data exists
        if ($username) {
            $this->load->model(['M_users', 'M_login', 'M_karyawan']);
            $data['users'] = $this->M_users->getUsers($username)->row_array();
            $data['cekusername'] = $this->M_login->cekusernamekasir($username)->row_array();
            $data['role_id'] = $roleid;
            $data['tampildata'] = $this->M_karyawan->tampildata()->result_array();
        }

        $this->load->view('access_denied', $data);
    }

}
