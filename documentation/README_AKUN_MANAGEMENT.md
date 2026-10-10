# Sistem Manajemen Akun - TGM ERP

## Deskripsi
Sistem manajemen akun adalah modul yang memungkinkan administrator untuk mengelola akun pengguna dalam sistem TGM ERP. Modul ini menyediakan fitur lengkap untuk CRUD (Create, Read, Update, Delete) akun pengguna, pencarian, filter, dan ekspor data.

## Fitur Utama

### 1. Dashboard Statistik
- Total pengguna terdaftar
- Pengguna aktif
- Pengguna tidak aktif
- Pengguna diblokir
- Grafik distribusi berdasarkan role dan perusahaan

### 2. Manajemen Akun
- **Tambah Akun Baru**: Menambahkan akun untuk karyawan yang belum memiliki akun
- **Edit Akun**: Mengubah data akun yang sudah ada
- **Hapus Akun**: Menghapus akun secara permanen
- **Reset Password**: Mengatur ulang password ke default
- **Aktivasi/Deaktivasi**: Mengaktifkan atau menonaktifkan akun
- **Blokir Akun**: Memblokir akun karena pelanggaran

### 3. Pencarian dan Filter
- Pencarian berdasarkan username, email, atau nama karyawan
- Filter berdasarkan status akun (aktif/tidak aktif/diblokir)
- Filter berdasarkan hak akses (role)
- Filter berdasarkan perusahaan/store

### 4. Ekspor Data
- Ekspor data akun ke format Excel (.xlsx)
- Data lengkap dengan styling dan header

### 5. Pagination
- Navigasi halaman untuk data yang banyak
- Informasi jumlah data yang ditampilkan

## Struktur Database

### Tabel `users`
```sql
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `karyawan_id` int(11) DEFAULT NULL,
  `perusahaan_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
);
```

### Relasi Tabel
- `users.karyawan_id` → `tbl_karyawan.karyawan_id`
- `users.perusahaan_id` → `tbl_perusahaan.id_perusahaan`
- `users.role_id` → `roles.role_id`

## File yang Terlibat

### Controller
- `application/controllers/Akun.php` - Controller utama untuk manajemen akun

### View
- `application/views/admin/akun/index.php` - Halaman utama manajemen akun

### Assets
- `assets/js/akun.js` - JavaScript untuk interaksi AJAX
- `assets/css/akun.css` - Styling khusus untuk halaman akun

## Fungsi-fungsi Controller

### 1. `index($page = 1)`
**Deskripsi**: Menampilkan halaman utama manajemen akun
**Parameter**: 
- `$page` (int): Nomor halaman untuk pagination

**Fitur**:
- Menampilkan statistik akun
- Data akun dengan pagination
- Daftar karyawan yang belum memiliki akun
- Data role dan perusahaan untuk form

### 2. `action_add_user()`
**Deskripsi**: Menambahkan akun pengguna baru
**Validasi**:
- Username: required, unique
- Password: required, min 6 karakter
- Email: required, valid email, unique
- Karyawan: required
- Perusahaan: required
- Role: required

### 3. `action_edit_user()`
**Deskripsi**: Mengedit data akun pengguna
**Validasi**:
- Username: required, unique (kecuali akun yang sedang diedit)
- Email: required, valid email, unique (kecuali akun yang sedang diedit)
- Karyawan: required
- Perusahaan: required
- Role: required

### 4. `reset_password($user_id)`
**Deskripsi**: Reset password akun ke default
**Parameter**:
- `$user_id` (int): ID akun yang akan direset

### 5. `delete_user($user_id)`
**Deskripsi**: Menghapus akun pengguna
**Parameter**:
- `$user_id` (int): ID akun yang akan dihapus

### 6. `get_users_data()` (AJAX)
**Deskripsi**: Mendapatkan data akun dengan pencarian dan filter
**Parameter GET**:
- `search`: Kata kunci pencarian
- `status`: Filter status (0/1/2)
- `role`: Filter role ID
- `perusahaan`: Filter perusahaan ID

**Response**: JSON dengan data akun

### 7. `get_user_detail($user_id)` (AJAX)
**Deskripsi**: Mendapatkan detail akun berdasarkan ID
**Parameter**:
- `$user_id` (int): ID akun

**Response**: JSON dengan detail akun

### 8. `toggle_user_status($user_id)`
**Deskripsi**: Mengaktifkan/menonaktifkan akun
**Parameter**:
- `$user_id` (int): ID akun

### 9. `block_user($user_id)`
**Deskripsi**: Memblokir akun
**Parameter**:
- `$user_id` (int): ID akun

### 10. `export_users()`
**Deskripsi**: Mengekspor data akun ke Excel
**Output**: File Excel (.xlsx) dengan data semua akun

### 11. `get_user_statistics()` (AJAX)
**Deskripsi**: Mendapatkan statistik akun
**Response**: JSON dengan data statistik

## Status Akun

| Kode | Status | Deskripsi |
|------|--------|-----------|
| 1 | Aktif | Akun dapat digunakan untuk login |
| 0 | Tidak Aktif | Akun dinonaktifkan sementara |
| 2 | Diblokir | Akun diblokir karena pelanggaran |

## Keamanan

### Validasi Input
- Semua input divalidasi menggunakan CodeIgniter Form Validation
- Username dan email harus unik
- Password minimal 6 karakter
- Sanitasi input menggunakan `$this->input->post(..., TRUE)`

### Hak Akses
- Hanya administrator yang dapat mengakses modul ini
- Validasi session dan role di constructor

### Password
- Password di-hash menggunakan MD5 (disarankan upgrade ke bcrypt)
- Default password: "default123"

## Penggunaan

### 1. Menambah Akun Baru
1. Klik tombol "Tambah Akun Baru"
2. Pilih karyawan dari dropdown
3. Username dan email akan terisi otomatis
4. Masukkan password
5. Pilih perusahaan dan hak akses
6. Klik "Simpan"

### 2. Mengedit Akun
1. Klik tombol edit (ikon pensil) pada baris akun
2. Modal akan muncul dengan data akun
3. Ubah data yang diperlukan
4. Klik "Simpan Perubahan"

### 3. Reset Password
1. Klik tombol reset password (ikon kunci)
2. Konfirmasi reset password
3. Password akan direset ke "default123"

### 4. Menghapus Akun
1. Klik tombol hapus (ikon tempat sampah)
2. Konfirmasi penghapusan
3. Akun akan dihapus permanen

### 5. Pencarian dan Filter
1. Gunakan kotak pencarian untuk mencari berdasarkan username, email, atau nama
2. Gunakan dropdown filter untuk memfilter berdasarkan status, role, atau perusahaan
3. Data akan diperbarui secara real-time menggunakan AJAX

### 6. Ekspor Data
1. Klik tombol "Ekspor Excel"
2. File Excel akan diunduh otomatis

## Troubleshooting

### Masalah Umum

1. **Akun tidak muncul di daftar**
   - Periksa apakah akun memiliki `karyawan_id` yang valid
   - Periksa relasi dengan tabel `tbl_karyawan`

2. **Error saat menambah akun**
   - Periksa validasi form
   - Pastikan username dan email unik
   - Periksa apakah karyawan sudah memiliki akun

3. **Error saat edit akun**
   - Periksa apakah `user_id` valid
   - Periksa validasi username dan email

4. **Password tidak bisa direset**
   - Periksa apakah `user_id` valid
   - Periksa koneksi database

### Log Error
Error akan dicatat di:
- `application/logs/` - Log aplikasi CodeIgniter
- Browser console - Error JavaScript

## Pengembangan Selanjutnya

### Fitur yang Direncanakan
1. **Audit Trail**: Mencatat semua perubahan akun
2. **Bulk Operations**: Operasi massal (reset password, aktivasi, dll)
3. **Import Data**: Import akun dari Excel
4. **Password Policy**: Kebijakan password yang lebih ketat
5. **Two-Factor Authentication**: Autentikasi dua faktor
6. **Account Lockout**: Pembatasan login setelah gagal berulang

### Peningkatan Keamanan
1. **Upgrade Password Hashing**: Dari MD5 ke bcrypt
2. **Rate Limiting**: Pembatasan request per menit
3. **Session Management**: Manajemen session yang lebih baik
4. **Input Sanitization**: Sanitasi input yang lebih ketat

## Dependencies

### Library CodeIgniter
- Form Validation
- Database
- Session
- Template

### Library Eksternal
- PHPExcel (untuk ekspor Excel)
- Select2 (untuk dropdown yang lebih baik)
- SweetAlert2 (untuk konfirmasi)

### CSS Framework
- Bootstrap 5
- Bootstrap Icons

## Kontribusi

Untuk berkontribusi pada pengembangan modul ini:
1. Fork repository
2. Buat branch fitur baru
3. Commit perubahan
4. Push ke branch
5. Buat Pull Request

## Lisensi

Modul ini adalah bagian dari sistem TGM ERP dan tunduk pada lisensi yang sama.

## Kontak

Untuk pertanyaan atau dukungan teknis, hubungi tim pengembangan TGM ERP. 