# Laporan Pemasukan Keuangan Bulanan

## 📋 Ringkasan Implementasi

Laporan pemasukan keuangan bulanan telah diimplementasikan untuk memberikan analisis komprehensif tentang pendapatan perusahaan per bulan. Fitur ini menampilkan breakdown metode pembayaran, trend transaksi, dan analisis piutang dalam periode bulanan.

## 🎯 Fitur Utama

### ✅ Fitur yang Sudah Diimplementasikan:

1. **Dashboard Overview** - Statistik ringkasan bulanan
2. **Filter Bulanan** - Filter berdasarkan bulan dan tahun
3. **Breakdown Metode Pembayaran** - Analisis per metode pembayaran
4. **Progress Visualization** - Visualisasi distribusi pemasukan
5. **Tabbed Interface** - Interface tab untuk setiap metode pembayaran
6. **DataTables Integration** - Tabel dengan sorting, searching, dan export
7. **Export Functionality** - Export ke Excel, PDF, dan Print
8. **Responsive Design** - Tampilan yang optimal di semua device
9. **Animation Effects** - Animasi angka dan transisi yang smooth
10. **Consistent Styling** - Konsisten dengan template karyawan.php

## 🗄️ Struktur Database

### Query Utama:

#### 1. Total Pemasukan Bulanan
```sql
SELECT SUM(bayar_tunai + bayar_transfer + bayar_debit + bayar_ewallet) as totalna 
FROM tbl_pembayaran 
WHERE pembayaran_tgl BETWEEN '$bulan_ini' AND '$bulan_akhir'
```

#### 2. Breakdown Metode Pembayaran
```sql
-- Cash
SELECT SUM(bayar_tunai) as totalna 
FROM tbl_pembayaran 
WHERE pembayaran_tgl BETWEEN '$bulan_ini' AND '$bulan_akhir'

-- Debit/EDC
SELECT SUM(bayar_debit) as totalna 
FROM tbl_pembayaran 
WHERE pembayaran_tgl BETWEEN '$bulan_ini' AND '$bulan_akhir'

-- E-Wallet
SELECT SUM(bayar_ewallet) as totalna 
FROM tbl_pembayaran 
WHERE pembayaran_tgl BETWEEN '$bulan_ini' AND '$bulan_akhir'
```

#### 3. Piutang Bulanan
```sql
SELECT SUM(total_tagihan) as totalna 
FROM tbl_piutang 
WHERE DATE(created_at) BETWEEN '$bulan_ini' AND '$bulan_akhir'
```

#### 4. Transaksi per Metode
```sql
-- Cash Transactions
SELECT p.pembayaran_tgl as tanggal, p.pembayaran_jam as jam, p.nospk, 
       k.konsumen_nama as konsumen, k.konsumen_nohp as telepon,
       p.bayar_tunai as jumlah, p.grand_total as total,
       u.nama as kasir
FROM tbl_pembayaran p
LEFT JOIN tbl_konsumen k ON p.konsumen_id = k.konsumen_id
LEFT JOIN users u ON p.user_id = u.id
WHERE p.pembayaran_tgl BETWEEN '$bulan_ini' AND '$bulan_akhir'
AND p.bayar_tunai > 0
ORDER BY p.pembayaran_tgl DESC, p.pembayaran_jam DESC
```

## 🔧 Controller Implementation

### Method: `pemasukan_bulanan()`

**Lokasi:** `application/controllers/Laporan.php`

**Fitur:**
- Filter berdasarkan bulan dan tahun
- Perhitungan total pemasukan bulanan
- Breakdown per metode pembayaran
- Data transaksi terperinci
- Data piutang bulanan

**Parameter:**
- `bulan` (GET) - Format: YYYY-MM (contoh: 2024-01)

**Return Data:**
```php
$data = array(
    'controller'        => $this->uri->segment(1),
    'function'          => $this->uri->segment(2),
    'title'             => 'Laporan Pemasukan Bulanan',
    'perusahaan'        => $this->session->userdata('nama_perusahaan'),
    'bulan_ini'         => $bulan_ini,
    'bulan_akhir'       => $bulan_akhir,
    'total_pemasukan'   => $total_pemasukan,
    'total_pemasukan_piutang' => $total_pemasukan_piutang,
    'cash'              => $cash,
    'debit'             => $debit,
    'ewallet'           => $ewallet,
    'transactions'      => $transactions
);
```

## 🎨 View Implementation

### File: `application/views/admin/laporan/keuangan/laporan_pemasukan_bulanan.php`

#### 1. Header Section
```html
<div class="monthly-income-header">
    <!-- Title, subtitle, description -->
    <!-- Info cards: Back button, Month, Time -->
</div>
```

#### 2. Statistics Overview
```html
<div class="stats-overview">
    <!-- Total Pemasukan -->
    <!-- Total Piutang -->
    <!-- Cash Bulan Ini -->
    <!-- Jumlah Transaksi -->
</div>
```

#### 3. Filter Section
```html
<div class="filter-section">
    <!-- Month picker -->
    <!-- Filter button -->
</div>
```

#### 4. Progress Visualization
```html
<div class="progress-section">
    <!-- Distribusi pemasukan per metode -->
    <!-- Cash, Transfer, EDC, E-Wallet, Piutang -->
</div>
```

#### 5. Tabbed Interface
```html
<ul class="nav nav-tabs" id="paymentTabs">
    <!-- Cash, Transfer, Debit, E-Wallet, Piutang tabs -->
</ul>

<div class="tab-content">
    <!-- Transaction tables for each payment method -->
</div>
```

## 📊 Komponen Tampilan

### 1. Statistics Cards
- **Total Pemasukan:** Total semua pemasukan + piutang
- **Total Piutang:** Total piutang bulanan
- **Cash Bulan Ini:** Total pembayaran cash
- **Jumlah Transaksi:** Total transaksi bulanan

### 2. Progress Bar
- **Cash:** Warna hijau (#28a745)
- **Transfer:** Warna biru (#007bff)
- **Piutang:** Warna kuning (#ffc107)
- **EDC:** Warna cyan (#17a2b8)
- **E-Wallet:** Warna orange (#fd7e14)

### 3. Tabbed Tables
- **Cash Transactions:** Transaksi pembayaran cash
- **Transfer Transactions:** Transaksi pembayaran transfer
- **Debit Transactions:** Transaksi pembayaran EDC
- **E-Wallet Transactions:** Transaksi pembayaran e-wallet
- **Piutang Transactions:** Transaksi piutang

## 🎯 Metode Pembayaran yang Didukung

| Metode | Icon | Color | Description |
|--------|------|-------|-------------|
| **Cash** | `fa-money-bill-wave` | Green | Pembayaran tunai |
| **Transfer** | `fa-university` | Blue | Pembayaran transfer bank |
| **EDC** | `fa-credit-card` | Cyan | Pembayaran kartu debit/kredit |
| **E-Wallet** | `fa-wallet` | Orange | Pembayaran e-wallet |
| **Piutang** | `fa-handshake` | Yellow | Transaksi piutang |

## 🔄 Alur Kerja

### 1. Akses Laporan
```
URL: /laporan/pemasukan_bulanan
```

### 2. Filter Bulan
```
URL: /laporan/pemasukan_bulanan?bulan=2024-01
```

### 3. Data Processing
```
1. Ambil parameter bulan dari URL
2. Hitung range tanggal (bulan_ini sampai bulan_akhir)
3. Query total pemasukan per metode
4. Query transaksi detail per metode
5. Query data piutang bulanan
6. Render view dengan data lengkap
```

### 4. User Interaction
```
1. User pilih bulan di filter
2. Sistem reload dengan data bulan yang dipilih
3. User bisa switch tab untuk melihat metode pembayaran berbeda
4. User bisa export data ke Excel/PDF/Print
5. User bisa search dan sort data di tabel
```

## 📱 Responsive Design

### 1. Desktop View
- Grid layout 4 kolom untuk statistics
- Side-by-side filter form
- Full-width progress bar
- Horizontal tab navigation

### 2. Tablet View
- Grid layout 2 kolom untuk statistics
- Stacked filter form
- Responsive progress bar
- Horizontal tab navigation

### 3. Mobile View
- Grid layout 1 kolom untuk statistics
- Full-width filter form
- Compact progress bar
- Vertical tab navigation

## 🎨 CSS Styling

### 1. Consistent with Karyawan Template
- Header gradient: `linear-gradient(135deg, #2c3e50 0%, #34495e 100%)`
- Card shadows: `0 4px 15px rgba(0,0,0,0.08)`
- Border radius: `0` (square design)
- Typography: Consistent font weights and sizes

### 2. Interactive Elements
- Hover effects pada cards dan buttons
- Smooth transitions: `all 0.3s ease`
- Transform effects: `translateY(-5px)`
- Backdrop blur effects

### 3. Color Scheme
- Primary: `#2c3e50` (Header background)
- Success: `#28a745` (Cash payments)
- Primary: `#007bff` (Transfer payments)
- Info: `#17a2b8` (EDC payments)
- Warning: `#ffc107` (E-Wallet payments)
- Secondary: `#6c757d` (Piutang payments)

## 📈 DataTables Features

### 1. Built-in Features
- **Sorting:** Sort berdasarkan kolom apapun
- **Searching:** Pencarian global dan per kolom
- **Pagination:** Navigasi halaman yang smooth
- **Responsive:** Tabel yang responsive

### 2. Export Options
- **Excel Export:** Export ke format .xlsx
- **PDF Export:** Export ke format .pdf
- **Print:** Print langsung dari browser

### 3. Customization
- **Language:** Bahasa Indonesia
- **Page Length:** 25 records per page
- **Buttons:** Custom styling untuk export buttons
- **Titles:** Dynamic titles berdasarkan bulan

## 🚀 Cara Penggunaan

### 1. Akses Laporan
```
Dashboard → Laporan → Pemasukan Bulanan
```

### 2. Filter Data
```
1. Pilih bulan dan tahun di filter
2. Klik "Tampilkan" atau tekan Enter
3. Data akan reload sesuai bulan yang dipilih
```

### 3. Navigasi Tab
```
1. Klik tab untuk melihat metode pembayaran berbeda
2. Setiap tab menampilkan transaksi sesuai metode
3. DataTables tersedia di setiap tab
```

### 4. Export Data
```
1. Klik tombol export di tabel (Excel/PDF/Print)
2. File akan di-download atau print dialog muncul
3. Data ter-export sesuai tab yang aktif
```

## 💡 Tips Penggunaan

### 1. Performance Optimization
- Gunakan filter bulan untuk mengurangi data yang dimuat
- Export data dalam batch untuk file yang besar
- Gunakan search untuk menemukan transaksi spesifik

### 2. Data Analysis
- Bandingkan trend antar bulan
- Analisis distribusi metode pembayaran
- Monitor piutang bulanan

### 3. Reporting
- Export data untuk analisis lebih lanjut
- Gunakan print untuk dokumentasi fisik
- Share PDF report dengan stakeholder

## 🔧 Troubleshooting

### 1. Data Tidak Muncul
- Cek parameter bulan di URL
- Pastikan ada transaksi di bulan yang dipilih
- Cek koneksi database

### 2. Export Error
- Pastikan browser mendukung download
- Cek permission folder download
- Pastikan data tidak terlalu besar

### 3. Performance Issue
- Kurangi jumlah data dengan filter yang lebih spesifik
- Gunakan pagination untuk data yang besar
- Refresh halaman jika terjadi lag

## 📝 Catatan Penting

1. **Data Real-time:** Data diambil langsung dari database
2. **Filter Wajib:** Filter bulan diperlukan untuk performa
3. **Export Limit:** Export dibatasi untuk data yang tidak terlalu besar
4. **Browser Support:** Pastikan browser mendukung JavaScript modern
5. **Mobile Friendly:** Responsive design untuk mobile dan tablet

---

**Laporan pemasukan bulanan memberikan analisis komprehensif tentang keuangan perusahaan dengan interface yang user-friendly dan fitur yang lengkap.**
