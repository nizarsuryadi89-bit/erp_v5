<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Google_oauth
{
    protected $CI;
    protected $client_id;
    protected $client_secret;
    protected $redirect_uri;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->config->load('google', TRUE);

        $this->client_id     = $this->CI->config->item('google_client_id', 'google');
        $this->client_secret = $this->CI->config->item('google_client_secret', 'google');
        $redirect_uri        = $this->CI->config->item('google_redirect_uri', 'google');

        if (empty($redirect_uri)) {
            $this->redirect_uri = site_url('auth/google_callback');
        } else {
            $this->redirect_uri = $redirect_uri;
        }
    }

    /**
     * Memeriksa apakah Client ID dan Secret sudah diisi
     */
    public function is_configured()
    {
        return (!empty($this->client_id) && !empty($this->client_secret));
    }

    /**
     * Mengambil Redirect URI aktif
     */
    public function get_redirect_uri()
    {
        return $this->redirect_uri;
    }

    /**
     * Membuat Google OAuth login URL
     */
    public function get_login_url($state)
    {
        $endpoint = 'https://accounts.google.com/o/oauth2/v2/auth';
        $params = [
            'response_type' => 'code',
            'client_id'     => $this->client_id,
            'redirect_uri'  => $this->redirect_uri,
            'scope'         => 'openid email profile',
            'state'         => $state,
            'access_type'   => 'online',
            'prompt'        => 'select_account'
        ];

        return $endpoint . '?' . http_build_query($params);
    }

    /**
     * Menukar code dengan access token lalu mengambil info profil user
     */
    public function get_user_info($code)
    {
        if (!$this->is_configured()) {
            return [
                'status' => false,
                'error'  => 'Google Client ID atau Client Secret belum dikonfigurasi di application/config/google.php'
            ];
        }

        // 1. Tukar Code dengan Access Token
        $token_url = 'https://oauth2.googleapis.com/token';
        $post_data = [
            'code'          => $code,
            'client_id'     => $this->client_id,
            'client_secret' => $this->client_secret,
            'redirect_uri'  => $this->redirect_uri,
            'grant_type'    => 'authorization_code'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $token_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Aman untuk lingkungan XAMPP lokal
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            return [
                'status' => false,
                'error'  => 'Gagal menghubungi server Google: ' . $curl_error
            ];
        }

        $token_data = json_decode($response, true);
        if ($http_code !== 200 || empty($token_data['access_token'])) {
            $err_desc = isset($token_data['error_description']) ? $token_data['error_description'] : (isset($token_data['error']) ? $token_data['error'] : 'Gagal mendapatkan token autentikasi Google.');
            return [
                'status' => false,
                'error'  => $err_desc
            ];
        }

        $access_token = $token_data['access_token'];

        // 2. Ambil profil user dari Google UserInfo endpoint
        $userinfo_url = 'https://www.googleapis.com/oauth2/v3/userinfo';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $userinfo_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $access_token,
            'Accept: application/json'
        ]);

        $userinfo_response = curl_exec($ch);
        $userinfo_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $userinfo_curl_error = curl_error($ch);
        curl_close($ch);

        if ($userinfo_curl_error) {
            return [
                'status' => false,
                'error'  => 'Gagal mengambil profil akun Google: ' . $userinfo_curl_error
            ];
        }

        $user_profile = json_decode($userinfo_response, true);
        if ($userinfo_http_code !== 200 || empty($user_profile['email'])) {
            return [
                'status' => false,
                'error'  => 'Gagal membaca informasi email dari akun Google.'
            ];
        }

        return [
            'status' => true,
            'data'   => $user_profile
        ];
    }
}
