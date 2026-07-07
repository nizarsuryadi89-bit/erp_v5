# Rekap Transaksi Manual - TGM ERP

## Overview
Fitur rekap transaksi manual memungkinkan admin untuk melihat ringkasan dan detail transaksi penjualan manual yang telah dilakukan dalam periode tertentu. Fitur ini memberikan insight tentang performa transaksi manual, analisis metode pembayaran, dan tracking piutang.

## Fitur Utama

### 1. Dashboard Header
- **Judul**: "Rekap Transaksi Manual"
- **Subtitle**: "Laporan Ringkasan Transaksi Penjualan Manual"
- **Deskripsi**: Menampilkan periode laporan yang sedang ditampilkan
- **Info Card**: Menampilkan periode laporan

### 2. Statistics Overview
Menampilkan 4 kartu statistik utama:
- **Total Transaksi**: Jumlah transaksi manual dalam periode
- **Total Pendapatan**: Total pendapatan dari transaksi manual
- **Total Piutang**: Total piutang yang belum lunas
- **Rata-rata Transaksi**: Rata-rata nilai per transaksi

### 3. Payment Method Summary
Menampilkan rekap metode pembayaran yang digunakan:
- Tunai
- Transfer Bank
- Debit/EDC
- Kartu Kredit
- E-Wallet
- Piutang
- Lainnya

### 4. Filter Section
- **Filter Bulan**: Dropdown untuk memilih periode laporan (format YYYY-MM)
- **Tombol Tampilkan**: Untuk memproses filter

### 5. Detail Transaksi Table
Tabel dengan kolom-kolom:
- No. SPK
- Tanggal & Waktu
- Data Konsumen (Nama, Telepon)
- Produk (Nama, Deskripsi)
- Quantity
- Total Tagihan
- Total Bayar
- Status Pembayaran (Lunas/Piutang)
- Metode Pembayaran
- Keterangan

### 6. Export & Print Features
- **Export Excel**: Download laporan dalam format Excel
- **Print**: Cetak laporan dalam format yang dioptimalkan untuk printer

## File yang Dibuat/Dimodifikasi

### 1. Controller: `application/controllers/Laporan.php`
#### Fungsi yang Ditambahkan:

**`rekap_transaksi_manual()`**
- Mengambil data transaksi manual untuk bulan tertentu
- Menghitung statistik ringkasan
- Menampilkan view rekap transaksi manual

**`export_transaksi_manual()`**
- Mengekspor data ke format Excel
- Menggunakan library PHPExcel
- Format file: `Rekap_Transaksi_Manual_YYYY-MM.xlsx`

**`print_transaksi_manual()`**
- Menampilkan view untuk cetak
- Format yang dioptimalkan untuk printer

### 2. Model: `application/models/M_laporan.php`
#### Fungsi yang Ditambahkan:

**`get_rekap_transaksi_manual($bulan)`**
- Query untuk mengambil data transaksi manual
- Join dengan tabel terkait (konsumen, produk, pembayaran, dll)
- Filter berdasarkan perusahaan_id dan periode
- Return data dalam format object

### 3. Views

**`application/views/admin/laporan/rekap_transaksi_manual.php`**
- View utama dengan dashboard modern
- CSS konsisten dengan template karyawan.php
- DataTables integration
- Responsive design

**`application/views/admin/laporan/print_transaksi_manual.php`**
- View untuk cetak
- Format yang dioptimalkan untuk printer
- Layout yang bersih dan mudah dibaca

## Struktur Database

### Tabel yang Digunakan:
1. **`tbl_pemesanan`** - Data transaksi utama
2. **`tbl_pemesanan_detail`** - Detail produk dalam transaksi
3. **`tbl_konsumen`** - Data konsumen
4. **`tbl_product`** - Data produk
5. **`tbl_pembayaran`** - Data pembayaran
6. **`tbl_pembayaran_detail`** - Detail metode pembayaran
7. **`tbl_rekening`** - Data rekening bank
8. **`tbl_ewallet`** - Data e-wallet
9. **`tbl_mesin_edc`** - Data mesin EDC

### Field Kunci:
- `id_jenis_transaksi = 2` (untuk transaksi manual)
- `perusahaan_id` (untuk filter berdasarkan perusahaan)
- `created_at` (untuk filter periode)

## Cara Penggunaan

### 1. Akses Laporan
```
URL: /laporan/rekap_transaksi_manual
Method: GET
Parameter: bulan (opsional, default: bulan saat ini)
```

### 2. Filter Data
- Pilih bulan yang diinginkan dari dropdown
- Klik tombol "Tampilkan"
- Data akan diperbarui sesuai periode yang dipilih

### 3. Export Data
- Klik tombol "Export Excel" untuk download file Excel
- Klik tombol "Print" untuk membuka halaman cetak

### 4. Navigasi Tabel
- Gunakan fitur search untuk mencari data spesifik
- Gunakan pagination untuk navigasi halaman
- Sort kolom dengan mengklik header tabel

## Fitur Teknis

### 1. DataTables Integration
- Search functionality
- Pagination
- Column sorting
- Export buttons (Excel, PDF, Print)

### 2. Responsive Design
- Mobile-friendly layout
- Grid system yang adaptif
- Font size yang responsif

### 3. CSS Consistency
- Menggunakan template dari `karyawan.php`
- Color scheme yang konsisten
- Typography yang seragam

### 4. Error Handling
- Validasi input bulan
- Handling data kosong
- Graceful error messages

## Dependencies

### 1. Libraries
- **PHPExcel**: Untuk export Excel
- **DataTables**: Untuk tabel interaktif
- **Bootstrap 5**: Untuk layout dan styling
- **Font Awesome**: Untuk icons

### 2. JavaScript
- **jQuery**: Untuk DOM manipulation
- **DataTables**: Untuk tabel enhancement
- **Chart.js**: Untuk visualisasi data (jika diperlukan)

## Security Considerations

1. **Session Validation**: Menggunakan `perusahaan_id` dari session
2. **Input Validation**: Validasi parameter bulan
3. **SQL Injection Prevention**: Menggunakan Query Builder
4. **Access Control**: Menggunakan `MY_Controller` untuk authentication

## Performance Optimization

1. **Database Indexing**: Pastikan index pada field yang sering digunakan
2. **Query Optimization**: Menggunakan JOIN yang efisien
3. **Pagination**: DataTables pagination untuk data besar
4. **Caching**: Bisa ditambahkan caching untuk data yang jarang berubah

## Future Enhancements

1. **Chart Visualization**: Menambahkan grafik untuk trend transaksi
2. **Advanced Filtering**: Filter berdasarkan konsumen, produk, status
3. **Real-time Updates**: Auto-refresh data
4. **Email Reports**: Kirim laporan via email
5. **Scheduled Reports**: Laporan otomatis per periode

## Troubleshooting

### Common Issues:

1. **Data Tidak Muncul**
   - Periksa `perusahaan_id` di session
   - Periksa `id_jenis_transaksi = 2`
   - Periksa format tanggal di database

2. **Export Excel Error**
   - Pastikan library PHPExcel terinstall
   - Periksa permission folder untuk write file

3. **Print Layout Rusak**
   - Periksa CSS print media queries
   - Pastikan browser support print styling

### Debug Tips:
- Gunakan `log_message()` untuk debugging
- Periksa query dengan `$this->db->last_query()`
- Validasi data dengan `var_dump()` atau `print_r()`
