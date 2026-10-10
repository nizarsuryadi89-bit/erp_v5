# Rekap Metode Pembayaran - TGM ERP

## Deskripsi
Fitur Rekap Metode Pembayaran menampilkan ringkasan pembayaran berdasarkan metode pembayaran (Cash, Transfer, EDC, dan E-Wallet) untuk periode tertentu. Fitur ini memberikan analisis detail tentang distribusi pembayaran dan membantu dalam pengambilan keputusan keuangan.

## Fitur Utama

### 1. Dashboard Ringkasan
- **Summary Cards**: Menampilkan total untuk setiap metode pembayaran
- **Tabel Ringkasan**: Detail breakdown pembayaran dan piutang
- **Persentase**: Perhitungan persentase setiap metode pembayaran
- **Grafik Pie Chart**: Visualisasi distribusi metode pembayaran

### 2. Detail Per Metode Pembayaran
- **Cash/Tunai**: Total pembayaran tunai dan piutang
- **Transfer**: Breakdown per bank dan nomor rekening
- **EDC**: Detail per mesin EDC
- **E-Wallet**: Breakdown per provider e-wallet

### 3. Filter dan Export
- **Filter Tanggal**: Pemilihan tanggal untuk laporan
- **Export Excel**: Export data ke format Excel
- **Print**: Cetak laporan

## Struktur File

### Controller
- `application/controllers/Keuangan.php`
  - `rekap_metode_pembayaran()`: Method utama untuk menampilkan halaman
  - `calculate_payment_methods()`: Method untuk kalkulasi data
  - `export_rekap_metode_pembayaran()`: Method untuk export Excel
  - `get_safe_piutang_data()`: Helper method untuk data piutang

### View
- `application/views/admin/keuangan/rekap_metode_pembayaran.php`: Halaman utama rekap

### Database
- `tbl_pembayaran`: Tabel utama transaksi pembayaran
- `tbl_pembayaran_piutang`: Tabel pembayaran piutang
- `tbl_rekening`: Master data rekening bank
- `tbl_mesin_edc`: Master data mesin EDC
- `tbl_ewallet`: Master data e-wallet

## Cara Penggunaan

### 1. Akses Menu
1. Login ke sistem TGM ERP
2. Navigasi ke menu **Keuangan**
3. Klik **Rekap Metode Pembayaran** di dashboard

### 2. Filter Data
1. Pilih tanggal yang diinginkan
2. Klik **Tampilkan** untuk melihat data
3. Klik **Hari Ini** untuk kembali ke tanggal hari ini

### 3. Export Data
1. Klik tombol **Export Excel** untuk download file Excel
2. File akan otomatis terdownload dengan format: `Rekap_Metode_Pembayaran_YYYY-MM-DD.xlsx`

## Struktur Data

### Payment Methods Array
```php
$payment_methods = [
    'cash' => [
        'pembayaran' => 1000000,    // Total pembayaran cash
        'piutang' => 50000,         // Total piutang cash
        'total' => 1050000,         // Total keseluruhan
        'percentage' => 45.2        // Persentase dari total
    ],
    'transfer' => [
        'pembayaran' => 800000,
        'piutang' => 30000,
        'total' => 830000,
        'percentage' => 35.7,
        'details' => [              // Detail per bank
            ['nama' => 'BCA (1234567890)', 'total' => 500000],
            ['nama' => 'BRI (0987654321)', 'total' => 300000]
        ]
    ],
    'edc' => [...],
    'ewallet' => [...],
    'total_all' => 2340000
];
```

## Komponen Visual

### 1. Summary Cards
- **Cash**: Background hijau dengan icon money-bill-wave
- **Transfer**: Background biru dengan icon university
- **EDC**: Background kuning dengan icon credit-card
- **E-Wallet**: Background cyan dengan icon mobile-alt

### 2. Tabel Ringkasan
- Header dengan warna gelap
- Baris berwarna sesuai metode pembayaran
- Badge persentase dengan warna yang sesuai
- Total keseluruhan di baris terakhir

### 3. Grafik Pie Chart
- Menggunakan Chart.js
- Warna konsisten dengan summary cards
- Tooltip menampilkan nilai dan persentase
- Legend di bagian bawah

## Integrasi dengan Sistem

### 1. Dashboard Keuangan
- Link ditambahkan di dashboard utama keuangan
- Icon pie-chart untuk membedakan dengan menu lain
- Posisi strategis setelah "Total Transaksi Harian"

### 2. Menu Navigation
- Terintegrasi dengan sistem menu yang ada
- Menggunakan layout admin yang konsisten
- Breadcrumb dan header yang sesuai

### 3. Export System
- Menggunakan library PHPExcel yang sudah ada
- Format file konsisten dengan export lainnya
- Naming convention yang standar

## Keamanan dan Validasi

### 1. Authentication
- Cek session login user
- Validasi hak akses keuangan
- Redirect ke login jika tidak terautentikasi

### 2. Data Validation
- Validasi format tanggal
- Sanitasi input parameter
- Error handling untuk query database

### 3. Error Handling
- Try-catch untuk operasi database
- Fallback untuk data yang tidak tersedia
- Log error untuk debugging

## Performance Optimization

### 1. Database Queries
- Query yang efisien dengan JOIN yang tepat
- Index pada kolom yang sering digunakan
- Caching untuk data master

### 2. Frontend Optimization
- Lazy loading untuk grafik
- Minified CSS dan JavaScript
- Responsive design untuk mobile

## Troubleshooting

### 1. Data Tidak Muncul
- Cek koneksi database
- Pastikan tabel master data terisi
- Verifikasi tanggal yang dipilih

### 2. Grafik Tidak Tampil
- Cek koneksi internet untuk CDN Chart.js
- Pastikan JavaScript enabled
- Cek console browser untuk error

### 3. Export Gagal
- Cek permission folder uploads
- Pastikan library PHPExcel terinstall
- Verifikasi memory limit PHP

## Update dan Maintenance

### 1. Menambah Metode Pembayaran Baru
1. Tambah data di tabel master
2. Update method `calculate_payment_methods()`
3. Tambah case di view dan controller

### 2. Modifikasi Tampilan
1. Edit file view `rekap_metode_pembayaran.php`
2. Update CSS untuk styling
3. Test responsive design

### 3. Backup dan Restore
- Backup file controller dan view
- Backup data master payment methods
- Dokumentasi perubahan

## Support dan Kontak

Untuk bantuan teknis atau pertanyaan tentang fitur ini, silakan hubungi:
- Email: support@tgm-erp.com
- Phone: +62-xxx-xxx-xxxx
- Documentation: https://docs.tgm-erp.com

---

**Versi**: 1.0  
**Tanggal Update**: <?= date('d F Y') ?>  
**Developer**: TGM ERP Team
