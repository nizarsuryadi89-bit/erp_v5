# Kasir Shift Management System

## Overview
Sistem manajemen shift kasir yang memungkinkan kasir untuk membuka dan menutup shift dengan laporan keuangan yang akurat. Sistem ini memastikan bahwa kasir harus membuka shift terlebih dahulu sebelum dapat melakukan transaksi.

## Fitur Utama

### 1. **Manajemen Shift**
- **Buka Shift**: Kasir dapat membuka shift baru dengan mencatat modal awal
- **Tutup Shift**: Kasir dapat menutup shift dengan menghitung uang fisik dan selisih
- **Riwayat Shift**: Melihat riwayat shift yang telah dilakukan
- **Detail Shift**: Melihat detail lengkap shift termasuk transaksi

### 2. **Validasi Transaksi**
- Kasir (level 3) **harus** membuka shift sebelum dapat melakukan transaksi
- Owner (level 17) dan Administrator (level 1) tidak terpengaruh oleh validasi shift
- Sistem akan otomatis redirect ke halaman shift management jika tidak ada shift aktif

### 3. **Laporan Keuangan**
- Perhitungan otomatis total penjualan tunai dan non-tunai
- Perhitungan selisih kas (uang fisik vs perhitungan sistem)
- Export laporan ke Excel
- Catatan shift untuk dokumentasi

## Struktur Database

### Tabel `kasir_shift`
```sql
CREATE TABLE `kasir_shift` (
    `shift_id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,                 -- ID kasir (relasi ke tabel user)
    `tanggal_shift` DATE NOT NULL,              -- Tanggal shift
    `jam_open` DATETIME NOT NULL,               -- Waktu buka kasir
    `jam_close` DATETIME DEFAULT NULL,          -- Waktu tutup kasir
    `modal_awal` DECIMAL(15,2) NOT NULL,         -- Modal awal di laci kasir
    `total_penjualan_tunai` DECIMAL(15,2) DEFAULT 0.00,
    `total_penjualan_non_tunai` DECIMAL(15,2) DEFAULT 0.00,
    `total_pengeluaran` DECIMAL(15,2) DEFAULT 0.00, -- Kas kecil yang keluar
    `uang_fisik_tutup` DECIMAL(15,2) DEFAULT 0.00,  -- Uang fisik yang dihitung saat tutup
    `selisih` DECIMAL(15,2) DEFAULT 0.00,       -- Selisih kas
    `status` ENUM('open','closed') DEFAULT 'open',
    `catatan` TEXT,
    PRIMARY KEY (`shift_id`)
);
```

## File yang Dibuat

### 1. **Controller**
- `application/controllers/KasirShift.php` - Controller utama untuk manajemen shift

### 2. **Model**
- `application/models/KasirShift_model.php` - Model untuk operasi database shift

### 3. **Views**
- `application/views/admin/kasir_shift/index.php` - Dashboard utama shift management
- `application/views/admin/kasir_shift/open_shift.php` - Form buka shift
- `application/views/admin/kasir_shift/close_shift.php` - Form tutup shift
- `application/views/admin/kasir_shift/shift_detail.php` - Detail shift

### 4. **Helper**
- `application/helpers/shift_helper.php` - Helper functions untuk validasi shift

### 5. **Database**
- `kasir_shift_table.sql` - Script SQL untuk membuat tabel

## Cara Penggunaan

### 1. **Setup Database**
```sql
-- Jalankan script SQL untuk membuat tabel
source kasir_shift_table.sql;
```

### 2. **Akses Sistem**
1. Login sebagai kasir (level 3)
2. Akses menu "Kasir Shift Management" dari dashboard transaksi
3. Klik "Buka Shift Baru" untuk memulai shift
4. Isi modal awal dan catatan (opsional)
5. Klik "Buka Shift" untuk memulai

### 3. **Melakukan Transaksi**
- Setelah shift dibuka, kasir dapat melakukan transaksi normal
- Semua transaksi akan otomatis tercatat dalam shift yang aktif

### 4. **Menutup Shift**
1. Klik "Tutup Shift" pada dashboard
2. Isi uang fisik yang dihitung
3. Isi total pengeluaran (opsional)
4. Tambah catatan penutupan (opsional)
5. Klik "Tutup Shift" untuk mengakhiri

### 5. **Melihat Laporan**
- Lihat detail shift untuk informasi lengkap
- Export laporan ke Excel untuk dokumentasi
- Riwayat shift tersimpan untuk audit

## Validasi dan Keamanan

### 1. **Validasi Shift**
- Hanya kasir (level 3) yang perlu membuka shift
- Owner dan Administrator tidak terpengaruh
- Sistem otomatis redirect jika tidak ada shift aktif

### 2. **Validasi Data**
- Modal awal harus lebih dari 0
- Uang fisik saat tutup harus diisi
- Hanya satu shift aktif per kasir per hari

### 3. **Keamanan**
- Validasi user ID untuk memastikan kasir hanya mengakses shiftnya sendiri
- Foreign key constraint ke tabel users
- Session validation untuk setiap akses

## Integrasi dengan Sistem

### 1. **Transaksi Controller**
- Ditambahkan validasi shift di method `index()`, `createspk()`, dan `editSPK()`
- Helper function `redirect_if_no_shift()` untuk validasi

### 2. **Autoload Configuration**
- Helper `shift` ditambahkan ke autoload untuk akses global

### 3. **UI Integration**
- Link ke KasirShift management ditambahkan di dashboard transaksi
- Styling konsisten dengan template yang ada

## Perhitungan Keuangan

### 1. **Saat Buka Shift**
- Modal awal dicatat
- Waktu buka dicatat

### 2. **Saat Tutup Shift**
- Total penjualan tunai dihitung dari `tbl_pembayaran`
- Total penjualan non-tunai dihitung dari `tbl_pembayaran`
- Selisih = Uang Fisik - (Modal Awal + Penjualan Tunai - Pengeluaran)

### 3. **Laporan**
- Detail semua transaksi dalam shift
- Ringkasan keuangan lengkap
- Export ke Excel dengan format yang rapi

## Troubleshooting

### 1. **Error "Anda harus membuka shift terlebih dahulu"**
- Pastikan login sebagai kasir (level 3)
- Buka shift di menu KasirShift Management
- Jika sudah ada shift aktif, tutup dulu shift sebelumnya

### 2. **Error database**
- Pastikan tabel `kasir_shift` sudah dibuat
- Pastikan foreign key constraint ke tabel `users` sudah benar
- Cek koneksi database

### 3. **Shift tidak bisa ditutup**
- Pastikan ada transaksi dalam shift tersebut
- Pastikan semua field required sudah diisi
- Cek log error untuk detail masalah

## Pengembangan Selanjutnya

### 1. **Fitur Tambahan**
- Multi-shift per hari
- Shift handover antar kasir
- Notifikasi shift yang belum ditutup
- Dashboard real-time untuk monitoring

### 2. **Integrasi**
- Integrasi dengan sistem absensi
- Integrasi dengan sistem inventory
- API untuk mobile app

### 3. **Laporan**
- Laporan shift per periode
- Analisis performa kasir
- Dashboard management untuk supervisor

## Support

Untuk bantuan teknis atau pertanyaan, silakan hubungi tim development atau buat issue di repository project.
