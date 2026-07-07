# Dashboard Keuangan Terpadu - TGM ERP

## Deskripsi
Dashboard Keuangan Terpadu adalah fitur analisis komprehensif yang menampilkan data keuangan harian dan bulanan dalam satu halaman yang informatif. Dashboard ini menggabungkan berbagai metrik keuangan, trend analysis, dan visualisasi data untuk memberikan gambaran lengkap tentang performa keuangan perusahaan.

## Fitur Utama

### 1. **KPI Cards (Key Performance Indicators)**
- **Total Pendapatan**: Menampilkan total pendapatan periode dengan perbandingan pertumbuhan
- **Total Pengeluaran**: Menampilkan total pengeluaran dengan analisis trend
- **Profit Margin**: Menampilkan margin keuntungan dengan target 25%
- **Jumlah Transaksi**: Menampilkan jumlah transaksi dengan rata-rata harian

### 2. **Filter Periode Cepat**
- **Hari Ini**: Filter untuk data hari ini
- **Minggu Ini**: Filter untuk data minggu ini
- **Bulan Ini**: Filter untuk data bulan ini
- **Kuartal**: Filter untuk data kuartal ini
- **Custom Range**: Filter tanggal custom dengan form

### 3. **Visualisasi Data**
- **Trend Chart**: Grafik line chart untuk trend pendapatan vs pengeluaran
- **Payment Methods Chart**: Grafik pie chart untuk distribusi metode pembayaran
- **Responsive Design**: Chart yang responsif untuk berbagai ukuran layar

### 4. **Analisis Detail**
- **Cash Flow Analysis**: Tabel analisis arus kas harian dan bulanan
- **Top Performers**: Ranking metode pembayaran berdasarkan performa
- **Growth Indicators**: Indikator pertumbuhan dengan badge warna

### 5. **Quick Actions**
- Link cepat ke fitur-fitur terkait
- Export dashboard ke Excel
- Navigasi mudah ke laporan lain

## Struktur File

### Controller
- `application/controllers/Keuangan.php`
  - `dashboard_terpadu()`: Method utama dashboard
  - `get_comprehensive_financial_summary()`: Analisis ringkasan keuangan
  - `get_trend_analysis()`: Analisis trend data
  - `get_top_performers()`: Analisis top performers
  - `get_cash_flow_analysis()`: Analisis arus kas
  - `get_payment_methods_analysis()`: Analisis metode pembayaran
  - `export_dashboard()`: Export dashboard ke Excel

### View
- `application/views/admin/keuangan/dashboard_terpadu.php`: Halaman utama dashboard

### Integration
- `application/views/admin/keuangan/index.php`: Link ke dashboard terpadu

## Cara Penggunaan

### 1. Akses Dashboard
1. Login ke sistem TGM ERP
2. Navigasi ke menu **Keuangan**
3. Klik **Dashboard Terpadu** di halaman utama keuangan

### 2. Filter Data
1. Gunakan tombol periode cepat (Hari Ini, Minggu Ini, dll)
2. Atau gunakan form filter custom untuk range tanggal spesifik
3. Klik **Tampilkan** untuk melihat data
4. Klik **Reset** untuk kembali ke periode default

### 3. Analisis Data
1. Lihat KPI cards untuk overview cepat
2. Analisis trend melalui grafik line chart
3. Lihat distribusi metode pembayaran melalui pie chart
4. Review analisis cash flow dan top performers

### 4. Export Data
1. Klik tombol **Export** untuk download Excel
2. File akan terdownload dengan format: `Dashboard_Keuangan_Terpadu_YYYY-MM-DD_YYYY-MM-DD.xlsx`

## Struktur Data

### Financial Summary
```php
$financial_summary = [
    'total_pendapatan' => 50000000,      // Total pendapatan
    'total_pengeluaran' => 30000000,     // Total pengeluaran
    'pendapatan_growth' => '15.2',       // Pertumbuhan pendapatan (%)
    'pengeluaran_growth' => '8.5',       // Pertumbuhan pengeluaran (%)
    'profit_margin' => 40.0,             // Profit margin (%)
    'jumlah_transaksi' => 150,           // Jumlah transaksi
    'avg_transaction' => '5.0'           // Rata-rata transaksi per hari
];
```

### Trend Analysis
```php
$trend_analysis = [
    'labels' => ['01/12', '02/12', '03/12'],     // Label tanggal
    'pendapatan' => [1000000, 1200000, 1100000], // Data pendapatan
    'pengeluaran' => [800000, 900000, 850000]    // Data pengeluaran
];
```

### Payment Methods
```php
$payment_methods = [
    'cash' => ['total' => 20000000, 'percentage' => 40.0],
    'transfer' => ['total' => 15000000, 'percentage' => 30.0],
    'edc' => ['total' => 10000000, 'percentage' => 20.0],
    'ewallet' => ['total' => 5000000, 'percentage' => 10.0]
];
```

## Komponen Visual

### 1. KPI Cards
- **Primary Border**: Biru untuk pendapatan
- **Success Border**: Hijau untuk pengeluaran
- **Info Border**: Cyan untuk profit margin
- **Warning Border**: Kuning untuk jumlah transaksi
- **Icons**: FontAwesome icons yang relevan
- **Growth Indicators**: Badge dengan warna sesuai trend

### 2. Charts
- **Line Chart**: Trend pendapatan vs pengeluaran
- **Doughnut Chart**: Distribusi metode pembayaran
- **Responsive**: Menyesuaikan ukuran layar
- **Interactive**: Tooltip dan legend yang informatif

### 3. Tables
- **Bordered Tables**: Tabel dengan border untuk analisis
- **Color-coded**: Badge warna untuk indikator
- **Responsive**: Scroll horizontal pada mobile

## Integrasi dengan Sistem

### 1. Menu Navigation
- Terintegrasi dengan menu keuangan utama
- Posisi strategis sebagai entry point
- Icon speedometer untuk membedakan dengan menu lain

### 2. Data Sources
- Menggunakan model `M_transaksi` yang sudah ada
- Query database yang efisien
- Caching untuk performa optimal

### 3. Export System
- Menggunakan library PHPExcel
- Format konsisten dengan export lainnya
- Naming convention standar

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

### 3. Chart.js Optimization
- CDN loading untuk performa
- Responsive charts
- Memory management untuk chart instances

## Troubleshooting

### 1. Data Tidak Muncul
- Cek koneksi database
- Pastikan tabel master data terisi
- Verifikasi tanggal yang dipilih
- Cek log error di console browser

### 2. Grafik Tidak Tampil
- Cek koneksi internet untuk CDN Chart.js
- Pastikan JavaScript enabled
- Cek console browser untuk error
- Verifikasi data yang dikirim ke chart

### 3. Export Gagal
- Cek permission folder uploads
- Pastikan library PHPExcel terinstall
- Verifikasi memory limit PHP
- Cek format tanggal yang dikirim

### 4. Filter Tidak Berfungsi
- Cek JavaScript console untuk error
- Verifikasi form submission
- Pastikan parameter URL benar
- Cek method GET/POST

## Update dan Maintenance

### 1. Menambah Metrik Baru
1. Tambah field di method helper
2. Update view untuk menampilkan data
3. Tambah styling jika diperlukan
4. Test dengan data real

### 2. Modifikasi Chart
1. Edit JavaScript chart configuration
2. Update data structure jika diperlukan
3. Test responsive behavior
4. Verifikasi tooltip dan legend

### 3. Custom Filter
1. Tambah parameter di controller
2. Update query database
3. Modifikasi form filter
4. Test dengan berbagai kondisi

## Support dan Kontak

Untuk bantuan teknis atau pertanyaan tentang fitur ini, silakan hubungi:
- Email: support@tgm-erp.com
- Phone: +62-xxx-xxx-xxxx
- Documentation: https://docs.tgm-erp.com

---

**Versi**: 1.0  
**Tanggal Update**: <?= date('d F Y') ?>  
**Developer**: TGM ERP Team
