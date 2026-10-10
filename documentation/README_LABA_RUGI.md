# Aplikasi Pelaporan Laba Rugi Perusahaan

## Deskripsi
Aplikasi pelaporan laba rugi (profit and loss) yang komprehensif untuk perusahaan. Aplikasi ini menyediakan analisis keuangan yang detail dengan berbagai fitur pelaporan, filtering, dan export data.

## Fitur Utama

### 1. Dashboard Laporan Keuangan
- **Ringkasan Keuangan**: Menampilkan total pendapatan, laba kotor, pengeluaran, dan laba/rugi bersih
- **Kartu Navigasi**: Akses cepat ke berbagai jenis laporan
- **Statistik Visual**: Grafik dan chart untuk analisis keuangan
- **Export Data**: Kemampuan export ke Excel dengan berbagai filter

### 2. Laporan Laba Rugi
- **Struktur Laba Rugi Standar**: Pendapatan, HPP, Laba Kotor, Beban Operasional, Laba/Rugi Bersih
- **Filter Periode**: Filter berdasarkan bulan atau rentang tanggal
- **Detail Transaksi**: Tampilan detail pendapatan dan pengeluaran
- **Visualisasi Data**: Chart dan grafik untuk analisis

### 3. Laporan Pendapatan
- **Pemasukan Harian**: Laporan pendapatan per hari
- **Pemasukan Bulanan**: Laporan pendapatan per bulan
- **Analisis Metode Pembayaran**: Cash, Debit, E-wallet

### 4. Laporan Pajak
- **Laporan Perpajakan**: Data untuk keperluan pajak
- **Filter Metode Pembayaran**: Tunai, Debit, E-wallet
- **Export Excel**: Export data pajak ke Excel

## Struktur Database

### Tabel Utama
1. **tbl_pembayaran**: Data pembayaran utama
2. **tbl_pembayaran_detail**: Detail item pembayaran
3. **tbl_harga_product**: Harga dan HPP produk
4. **tbl_pengeluaran_harian**: Pengeluaran harian
5. **tbl_pengeluaran_bulanan**: Pengeluaran bulanan
6. **tbl_konsumen**: Data konsumen
7. **tbl_perusahaan**: Data perusahaan

## File yang Dibuat/Dimodifikasi

### 1. Model (`application/models/M_laporan.php`)
```php
// Method utama yang ditambahkan:
- getLabaRugi($perusahaan_id, $periode)
- total_pendapatan($perusahaan_id, $periode)
- total_pengeluaran_harian($perusahaan_id, $tanggal, $periode)
- total_pengeluaran_bulanan($perusahaan_id, $periode)
- getLabaRugiLengkap($perusahaan_id, $periode)
- getLabaRugiDetail($perusahaan_id, $periode, $tgl_awal, $tgl_akhir)
```

### 2. Controller (`application/controllers/Laporan.php`)
```php
// Method yang ditambahkan/dimodifikasi:
- index() - Dashboard utama
- laba_rugi() - Laporan laba rugi
- get_laba_rugi_detail() - AJAX untuk detail data
- export_laba_rugi() - Export ke Excel
```

### 3. View (`application/views/admin/laporan/`)
- **index.php**: Dashboard utama dengan kartu navigasi
- **laba_rugi.php**: Halaman laporan laba rugi lengkap

## Cara Penggunaan

### 1. Akses Dashboard
```
URL: /laporan
Method: GET
```

### 2. Laporan Laba Rugi
```
URL: /laporan/laba_rugi
Method: GET
Parameter: periode, tgl_awal, tgl_akhir (opsional)
```

### 3. Export Excel
```
URL: /laporan/export_laba_rugi
Method: GET
Parameter: periode, tgl_awal, tgl_akhir (opsional)
```

### 4. Detail Data via AJAX
```
URL: /laporan/get_laba_rugi_detail
Method: POST
Parameter: periode, tgl_awal, tgl_akhir
```

## Fitur Keamanan

### 1. SQL Injection Protection
- Menggunakan parameterized queries
- Escape semua input user
- Validasi parameter

### 2. Session Management
- Validasi login user
- Cek hak akses perusahaan
- Session timeout

### 3. Data Filtering
- Filter berdasarkan perusahaan_id
- Validasi periode dan tanggal
- Sanitasi input

## Struktur Laporan Laba Rugi

```
PENDAPATAN
├── Total Pendapatan Penjualan
├── Total HPP
└── Laba Kotor

BEBAN OPERASIONAL
├── Pengeluaran Harian
├── Pengeluaran Bulanan
└── Total Beban Operasional

LABA/RUGI BERSIH
```

## Komponen UI/UX

### 1. Dashboard Cards
- Kartu dengan gradient background
- Icon yang relevan
- Link langsung ke laporan

### 2. Filter Section
- Input periode (month picker)
- Input tanggal awal/akhir
- Button filter dan reset

### 3. Summary Cards
- Small box dengan warna berbeda
- Icon dan angka yang jelas
- Responsive design

### 4. Data Tables
- Tabel dengan border dan stripe
- Sorting dan pagination
- Export functionality

### 5. Charts
- Chart.js integration
- Bar chart untuk perbandingan
- Doughnut chart untuk komposisi

## Dependencies

### 1. Frontend
- Bootstrap 4/5
- Font Awesome
- Chart.js
- jQuery
- DataTables (opsional)

### 2. Backend
- CodeIgniter 3
- PHPExcel library
- MySQL/MariaDB

## Konfigurasi

### 1. Database
Pastikan tabel berikut sudah ada:
```sql
- tbl_pembayaran
- tbl_pembayaran_detail
- tbl_harga_product
- tbl_pengeluaran_harian
- tbl_pengeluaran_bulanan
- tbl_konsumen
- tbl_perusahaan
```

### 2. Session
Pastikan session data berikut tersedia:
```php
- logged_in
- perusahaan_id
- user_id
- level
```

### 3. Helper
Load helper berikut di autoload:
```php
- tgl_indo_helper
```

## Troubleshooting

### 1. Data Tidak Muncul
- Cek koneksi database
- Pastikan perusahaan_id ada di session
- Cek struktur tabel

### 2. Export Excel Error
- Pastikan PHPExcel library terinstall
- Cek permission folder
- Pastikan memory limit cukup

### 3. Chart Tidak Muncul
- Pastikan Chart.js terload
- Cek console browser untuk error
- Pastikan data tidak null

## Pengembangan Selanjutnya

### 1. Fitur yang Bisa Ditambahkan
- Laporan per departemen
- Analisis trend bulanan/tahunan
- Forecasting laba rugi
- Dashboard real-time
- Notifikasi email laporan

### 2. Optimasi
- Caching query database
- Lazy loading untuk data besar
- Pagination untuk detail data
- Compression untuk export file

### 3. Integrasi
- API untuk mobile app
- Webhook untuk sistem lain
- Integration dengan accounting software
- Multi-currency support

## Support

Untuk pertanyaan atau masalah teknis, silakan hubungi:
- Email: support@tgm-erp.com
- Dokumentasi: /docs/laba-rugi
- Issue Tracker: GitHub Issues

## License

Aplikasi ini dikembangkan untuk TGM ERP System.
Copyright © 2025 TGM ERP. All rights reserved. 