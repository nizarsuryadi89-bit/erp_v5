# SISTEM ANTRIAN - DOKUMENTASI LENGKAP

## 📋 Daftar Isi
1. [Overview](#overview)
2. [Instalasi](#instalasi)
3. [Fitur Utama](#fitur-utama)
4. [Struktur Database](#struktur-database)
5. [Penggunaan](#penggunaan)
6. [API Endpoints](#api-endpoints)
7. [Konfigurasi](#konfigurasi)
8. [Troubleshooting](#troubleshooting)

---

## 🎯 Overview

Sistem Antrian adalah aplikasi terintegrasi dengan TGM ERP yang memudahkan manajemen antrian pelanggan. Sistem ini mencakup:

- **Interface Customer**: Tap layar untuk ambil nomor antrian
- **Panel Operator**: Operator (Deskprint) memanggil dan melayani pelanggan
- **Display Panel**: Papan informasi real-time untuk menampilkan antrian
- **Admin Dashboard**: Monitoring dan laporan antrian
- **Print Karcis**: Cetak nomor antrian otomatis

---

## 🚀 Instalasi

### 1. Import Database
```bash
# Jalankan SQL script untuk membuat tabel
mysql -u root -p nama_database < /path/to/antrian_tables.sql
```

Atau copy-paste isi file `database/antrian_tables.sql` ke phpMyAdmin.

### 2. File Structure
Pastikan struktur folder berikut ada:
```
application/
├── controllers/
│   └── Antrian.php (SUDAH DIBUAT)
├── models/
│   └── Antrian_model.php (SUDAH DIBUAT)
└── views/
    └── antrian/
        ├── ambil_nomor.php (SUDAH DIBUAT)
        ├── operator.php (SUDAH DIBUAT)
        ├── display.php (SUDAH DIBUAT)
        ├── dashboard.php (SUDAH DIBUAT)
        ├── laporan.php (SUDAH DIBUAT)
        └── karcis.php (SUDAH DIBUAT)
```

### 3. Restart Server (Opsional)
```bash
# Jika menggunakan Apache
sudo systemctl restart apache2

# Atau gunakan built-in PHP server
php -S localhost:8000
```

---

## ✨ Fitur Utama

### 1. **Ambil Nomor Antrian** (Public Interface)
- URL: `http://localhost/tgm_erp/antrian`
- **Fitur:**
  - Interface klik untuk ambil nomor
  - Pilih counter yang tersedia
  - Nomor antrian otomatis
  - Modal success dengan detail nomor
  - Opsi cetak karcis otomatis

### 2. **Panel Operator** (Deskprint)
- URL: `http://localhost/tgm_erp/antrian/operator/{counter_id}`
- **Akses:** Level 2 (Deskprint) atau Level 1 (Admin)
- **Fitur:**
  - Pilih counter yang akan dilayani
  - Tampilan nomor antrian yang sedang dipanggil (BIG)
  - Daftar antrian yang menunggu
  - Tombol Panggil → Mulai Layani → Selesai
  - Opsi batalkan antrian

### 3. **Display Panel** (Papan Informasi)
- URL: `http://localhost/tgm_erp/antrian/display/{counter_id}`
- **Fitur:**
  - Real-time update setiap 2 detik
  - Tampilan nomor antrian yang sedang dilayani (HUGE)
  - List antrian yang menunggu
  - Jam digital
  - Responsive untuk berbagai ukuran layar
  - Ideal untuk dipasang di monitor/TV

### 4. **Dashboard Admin**
- URL: `http://localhost/tgm_erp/antrian`
- **Fitur:**
  - Statistik antrian hari ini
  - Status semua counter
  - Quick actions
  - Akses ke semua panel

### 5. **Laporan Antrian**
- URL: `http://localhost/tgm_erp/antrian/laporan`
- **Fitur:**
  - Filter berdasarkan tanggal dan counter
  - Statistik lengkap
  - Tabel data antrian
  - Opsi cetak laporan

### 6. **Print Karcis**
- URL: `http://localhost/tgm_erp/antrian/print_karcis/{antrian_id}`
- **Fitur:**
  - Cetak otomatis nomor antrian
  - Format sesuai thermal printer (80mm)
  - Informasi lengkap (waktu, counter, barcode)

---

## 🗄️ Struktur Database

### Tabel: `tbl_antrian_counter`
```sql
-- Daftar counter/loket yang tersedia
- counter_id (PK)
- counter_nama (VARCHAR)
- counter_nomorurut (INT)
- status (ENUM: aktif/nonaktif)
- created_at, updated_at
```

### Tabel: `tbl_antrian`
```sql
-- Data antrian utama
- antrian_id (PK)
- nomor_antrian (VARCHAR, UNIQUE) - Format: C1-001
- counter_id (FK)
- status (ENUM: menunggu/dipanggil/sedang_dilayani/selesai/batal)
- waktu_ambil (TIMESTAMP)
- waktu_panggil (TIMESTAMP)
- waktu_selesai (TIMESTAMP)
- user_id (FK) - Operator yang melayani
- keterangan (TEXT)
```

### Tabel: `tbl_antrian_history`
```sql
-- Riwayat perubahan status antrian
- history_id (PK)
- antrian_id (FK)
- status_lama (VARCHAR)
- status_baru (VARCHAR)
- user_id (FK)
- waktu_perubahan (TIMESTAMP)
- keterangan (TEXT)
```

### Tabel: `tbl_antrian_stats`
```sql
-- Statistik antrian harian (untuk analytics)
- stats_id (PK)
- counter_id (FK)
- tanggal (DATE)
- total_antrian (INT)
- antrian_selesai (INT)
- antrian_batal (INT)
- rata_waktu_tunggu (INT) - dalam detik
- waktu_puncak (VARCHAR)
```

---

## 📖 Penggunaan

### A. Untuk Customer (Ambil Nomor)

1. **Akses Interface:**
   - Buka: `http://localhost/tgm_erp/antrian`
   - Atau arahkan customer ke halaman ini

2. **Ambil Nomor:**
   - Tap salah satu counter yang tersedia
   - Sistem akan generate nomor antrian otomatis
   - Modal success akan tampil dengan nomor

3. **Print Karcis (Opsional):**
   - Klik tombol "Cetak Karcis"
   - Browser akan membuka halaman print
   - Thermal printer akan cetak otomatis

4. **Tunggu Dipanggil:**
   - Lihat display panel
   - Atau dengarkan pengeras suara

### B. Untuk Operator/Deskprint

1. **Akses Panel Operator:**
   - Login sebagai Deskprint (Level 2) atau Admin
   - Buka: `http://localhost/tgm_erp/antrian/operator`
   - Pilih counter yang akan dilayani

2. **Memanggil Antrian:**
   - Klik tombol "Panggil" pada antrian yang menunggu
   - Nomor akan dipindahkan ke status "Dipanggil"
   - Display panel akan update otomatis

3. **Mulai Melayani:**
   - Setelah customer datang, klik "Mulai Layani"
   - Status berubah menjadi "Sedang Dilayani"

4. **Selesai Melayani:**
   - Setelah selesai, klik "Selesai"
   - Status berubah menjadi "Selesai"
   - Antrian berikutnya siap dipanggil

5. **Batalkan Antrian (jika diperlukan):**
   - Klik tombol "Batalkan"
   - Input alasan pembatalan
   - Status akan berubah menjadi "Batal"

### C. Untuk Display Panel (Monitor)

1. **Setup Display:**
   - Pasang monitor di area tunggu
   - Buka tab browser: `http://localhost/tgm_erp/antrian/display/1`
   - (Ganti `1` dengan counter_id yang sesuai)

2. **Auto Refresh:**
   - Panel akan auto-update setiap 2 detik
   - Tidak perlu di-refresh manual

3. **Informasi yang Ditampilkan:**
   - Nomor antrian yang sedang dilayani (BESAR)
   - List antrian yang menunggu (5 antrian)
   - Jam digital real-time

### D. Untuk Admin - Dashboard & Laporan

1. **Dashboard:**
   - Buka: `http://localhost/tgm_erp/antrian`
   - Lihat statistik harian
   - Akses panel operator dan display

2. **Laporan:**
   - Buka: `http://localhost/tgm_erp/antrian/laporan`
   - Filter berdasarkan tanggal dan counter
   - View tabel antrian yang detail
   - Cetak laporan

---

## 🔌 API Endpoints

### Public (Tanpa Login)
```
GET  /antrian                          - Interface ambil nomor
GET  /antrian/display/{counter_id}     - Display panel
GET  /antrian/get_data_display         - AJAX data display (refresh)
POST /antrian/ambil_nomor              - AJAX ambil nomor antrian
GET  /antrian/print_karcis/{id}        - Print karcis
```

### Protected (Dengan Login)
```
GET  /antrian/index                    - Dashboard admin
GET  /antrian/operator/{counter_id}    - Panel operator
GET  /antrian/laporan                  - Laporan antrian
POST /antrian/panggil_antrian_ajax     - AJAX panggil antrian
POST /antrian/mulai_layani_ajax        - AJAX mulai layani
POST /antrian/selesai_layani_ajax      - AJAX selesai layani
POST /antrian/batalkan_antrian_ajax    - AJAX batalkan antrian
```

### AJAX Request Examples

**1. Ambil Nomor Antrian**
```javascript
fetch('/tgm_erp/antrian/ambil_nomor', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest'
    },
    body: 'counter_id=1'
})
.then(r => r.json())
.then(data => console.log(data));
```

Response:
```json
{
    "status": "success",
    "data": {
        "antrian_id": 123,
        "nomor_antrian": "C1-001",
        "counter_id": 1,
        "counter_nama": "Counter 1",
        "status": "menunggu",
        "created_at": "2024-01-22 10:30:45"
    }
}
```

**2. Panggil Antrian**
```javascript
fetch('/tgm_erp/antrian/panggil_antrian_ajax', {
    method: 'POST',
    body: 'antrian_id=123'
})
```

**3. Get Data Display (Real-time)**
```javascript
fetch('/tgm_erp/antrian/get_data_display?counter_id=1')
.then(r => r.json())
.then(data => console.log(data));
```

Response:
```json
{
    "counter": { ... },
    "antrian_sekarang": { ... },
    "antrian_menunggu": [ ... ]
}
```

---

## ⚙️ Konfigurasi

### 1. Tambah Counter Baru
```sql
INSERT INTO tbl_antrian_counter (counter_nama, counter_nomorurut, status)
VALUES ('Counter 4', 4, 'aktif');
```

### 2. Nonaktifkan Counter
```sql
UPDATE tbl_antrian_counter SET status = 'nonaktif' WHERE counter_id = 2;
```

### 3. Reset Nomor Antrian (Daily)
```sql
-- Delete antrian kemarin (opsional, untuk archive)
DELETE FROM tbl_antrian WHERE DATE(waktu_ambil) < CURDATE();
```

### 4. Custom Prefix Nomor Antrian
Edit di `Antrian_model.php` - method `ambil_nomor_antrian()`:
```php
// Default: C1-001, C2-002 dst
// Bisa diubah ke: COUNTER-001, A-001, dsb
$counter_prefix = 'C' . $counter['counter_nomorurut'];
```

### 5. Interval Auto-Refresh Display
Edit di `application/views/antrian/display.php`:
```javascript
// Default: 2000ms (2 detik)
setInterval(() => { ... }, 2000);  // Ubah ke nilai lain jika diperlukan
```

### 6. Access Control (Role-Based)
Edit di `application/controllers/Antrian.php`:
```php
// Line di operator() method
// Sesuaikan level_id dengan role yang sesuai
if ($level_id != 2 && $level_id != 1) {
    // 2 = Deskprint, 1 = Admin
}
```

---

## 🔐 Security

### Authentication
- Panel Operator memerlukan login
- Display Panel public (tidak perlu login)
- Interface ambil nomor public

### Authorization
- Hanya Level 2 (Deskprint) atau Level 1 (Admin) yang bisa akses operator
- Backend check di `MY_Controller.php`

### Data Protection
- Semua AJAX request tervalidasi
- Query terlindungi dari SQL injection (CodeIgniter)
- Session-based access control

---

## 🛠️ Troubleshooting

### 1. "Akses Ditolak" pada Panel Operator
**Solusi:**
- Login dengan user Level 2 (Deskprint) atau Level 1 (Admin)
- Check di database: `SELECT * FROM roles;`

### 2. Display Panel Tidak Update
**Solusi:**
- Clear browser cache (Ctrl+F5)
- Check browser console untuk error
- Pastikan interval refresh `setInterval` sesuai

### 3. Nomor Antrian Tidak Tergenerate
**Solusi:**
- Check koneksi database
- Pastikan tabel `tbl_antrian_counter` ada dan berisi data
- Lihat error di browser console atau logs

### 4. Print Karcis Tidak Jalan
**Solusi:**
- Gunakan browser Chrome/Firefox (lebih reliable)
- Pastikan thermal printer sudah terpasang
- Set default printer ke thermal printer
- Edit `application/views/antrian/karcis.php` untuk custom width

### 5. Statistik Tidak Akurat
**Solusi:**
- Pastikan server timezone benar: `date_default_timezone_set("Asia/Jakarta");`
- Clear cache statistik
- Run manual stats calculation

### 6. Real-time Update Lambat
**Solusi:**
- Reduce interval refresh (ganti 2000 menjadi 1000 di display.php)
- Check server load/CPU
- Optimize database query (add index)

---

## 📝 Log & Monitoring

### Check Riwayat Antrian
```sql
SELECT * FROM tbl_antrian_history 
WHERE antrian_id = 123 
ORDER BY waktu_perubahan;
```

### Statistik Per Counter
```sql
SELECT counter_id, COUNT(*) as total, 
    SUM(CASE WHEN status='selesai' THEN 1 ELSE 0 END) as selesai
FROM tbl_antrian 
WHERE DATE(waktu_ambil) = CURDATE()
GROUP BY counter_id;
```

### Rata-rata Waktu Tunggu
```sql
SELECT counter_id, 
    AVG(TIMESTAMPDIFF(SECOND, waktu_ambil, waktu_selesai)) as avg_seconds
FROM tbl_antrian 
WHERE status = 'selesai' 
    AND DATE(waktu_ambil) = CURDATE()
GROUP BY counter_id;
```

---

## 📞 Support & Enhancement

Untuk pertanyaan atau enhancement:
1. Check documentation ini terlebih dahulu
2. Lihat troubleshooting section
3. Check browser console untuk error details
4. Hubungi tim development

---

## 📦 Versi & Changelog

**v1.0.0** - Initial Release
- Fitur dasar: ambil nomor, operator panel, display, laporan
- Database structure lengkap
- Authentication & Authorization
- Print karcis thermal

---

**Happy Queueing! 🎯**
