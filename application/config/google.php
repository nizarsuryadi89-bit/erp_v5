<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Google OAuth 2.0 Configuration
|--------------------------------------------------------------------------
|
| Dapatkan Client ID dan Client Secret dari Google Cloud Console:
| https://console.cloud.google.com/apis/credentials
|
| Pastikan mendaftarkan "Authorized redirect URIs" di Google Console:
| - https://localhost/tgm_erp/auth/google_callback
| (atau http://localhost/tgm_erp/auth/google_callback jika tanpa SSL lokal)
| - https://[domain-anda]/auth/google_callback (untuk hosting/server live)
|
*/

$config['google_client_id'] = ''; // Contoh: 1234567890-abcdefg.apps.googleusercontent.com
$config['google_client_secret'] = ''; // Contoh: GOCSPX-xxxxxxxxx
$config['google_redirect_uri'] = ''; // Kosongkan agar otomatis: https://localhost/tgm_erp/auth/google_callback
