<?php
/**
 * Test Controller Akun
 * Cakupan: Alur Login, Kontrol Akses, dan Logout
 * 
 * Persyaratan: kenjis/ci-phpunit-test
 */
class Akun_test extends TestCase
{
    public function setUp()
    {
        $this->resetInstance();
        $this->CI->load->model('M_auth');
        $this->CI->load->model('M_master');

        // Reset sesi untuk setiap tes
        $_SESSION = [];
    }

    /**
     * Skenario Test: Akses Tanpa Otentikasi
     * Harapan: Redirect ke Auth
     */
    public function test_index_not_logged_in()
    {
        $this->request('GET', 'akun');
        $this->assertRedirect('auth');
    }

    /**
     * Skenario Test: Alur Lengkap (Login -> Akses Akun -> Logout)
     */
    public function test_login_to_logout_lifecycle()
    {
        // ----------------------------------------------------------------
        // 1. LOGIN
        // ----------------------------------------------------------------
        // Opsi A: Simulasikan POST ke Auth/proses_login (Memerlukan kredensial valid yang diketahui)
        // $this->request('POST', 'auth/proses_login', ['username' => 'admin', 'password' => 'password']);

        // Opsi B: Set Sesi Secara Manual (Lebih andal untuk Unit Test controller secara terisolasi)
        $session_data = [
            'id' => 1,
            'username' => 'test_admin',
            'email' => 'admin@tgm.com',
            'level' => 1, // Peran Admin
            'role_id' => 1,
            'logged_in' => TRUE,
            'perusahaan_id' => 1
        ];
        $this->CI->session->set_userdata($session_data);

        // Verifikasi kita sudah "login"
        $this->assertTrue($this->CI->session->userdata('logged_in'));

        // ----------------------------------------------------------------
        // 2. AKSES AKUN (INDEX)
        // ----------------------------------------------------------------
        $output = $this->request('GET', 'akun');

        // Pastikan: Halaman dimuat dengan sukses (status 200 tersirat jika tidak ada redirect/error)
        // Pastikan: Berisi elemen UI spesifik dari 'admin/akun/index'
        $this->assertStringContainsString('Data Akun Pengguna', $output);
        $this->assertStringContainsString('Tambah Akun', $output);

        // ----------------------------------------------------------------
        // 3. AMBIL DATA (AJAX) 
        // ----------------------------------------------------------------
        // Simulasikan request AJAX ke get_data_ajax
        $this->request('POST', 'akun/get_data_ajax', [
            'page' => 1,
            'search' => ''
        ]);
        // Catatan: Outputnya JSON, biasanya assertJSON tapi di CI3 output mungkin perlu capturing buffer
        // Di ci-phpunit-test, request() mengembalikan string output.

        // ----------------------------------------------------------------
        // 4. LOGOUT
        // ----------------------------------------------------------------
        $this->request('GET', 'auth/logout');

        // Pastikan: Redirect ke Auth
        $this->assertRedirect('auth');

        // Pastikan: Sesi dihancurkan
        // Catatan: session_destroy() dalam mock mungkin tidak membersihkan array $_SESSION secara mendalam tergantung driver,
        // tapi pengecekan sesi CI harusnya gagal.
        $this->assertNull($this->CI->session->userdata('logged_in'));
    }

    /**
     * Test Validasi pada Tambah User
     */
    public function test_add_user_validation_fail()
    {
        // Simulasikan Login
        $this->CI->session->set_userdata(['logged_in' => TRUE, 'level' => 1]);

        // Post data kosong
        $this->request('POST', 'akun/action_add_user', []);

        // Seharusnya redirect kembali ke akun karena validasi gagal
        $this->assertRedirect('akun');

        // Verifikasi flashdata error ada
        $this->assertNotEmpty($this->CI->session->flashdata('error'));
    }
}
