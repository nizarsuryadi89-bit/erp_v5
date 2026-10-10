# Panduan Setup Pengujian

Proyek ini adalah aplikasi CodeIgniter 3. Untuk menjalankan Unit Test yang disediakan di `application/tests/`, Anda perlu menginstal pustaka `ci-phpunit-test`.

## Prasyarat

- Composer telah terinstal (`brew install composer` di Mac)

## Langkah Instalasi

1. **Instal Dependensi Pengujian**
    Jalankan perintah berikut di root proyek Anda (`/Applications/XAMPP/xamppfiles/htdocs/tgm_erp/`):

    ```bash
    composer require kenjis/ci-phpunit-test --dev
    ```

2. **Inisialisasi File Pengujian**
    Jika struktur direktori `tests` hilang atau tidak lengkap, jalankan:

    ```bash
    php vendor/kenjis/ci-phpunit-test/install.php
    ```

3. **Jalankan Pengujian**
    Eksekusi PHPUnit:

    ```bash
    vendor/bin/phpunit
    ```

    Atau jalankan tes Akun secara khusus:

    ```bash
    vendor/bin/phpunit application/tests/controllers/Akun_test.php
    ```

## Pemecahan Masalah

- Jika Anda melihat `Call to undefined method Akun_test::request()`, pastikan Anda meng-extend `TestCase` dari `ci-phpunit-test`.
- Jika koneksi database gagal, konfigurasikan `application/tests/phpunit.xml` atau pastikan konfigurasi `database.php` Anda berfungsi untuk mode CLI.
