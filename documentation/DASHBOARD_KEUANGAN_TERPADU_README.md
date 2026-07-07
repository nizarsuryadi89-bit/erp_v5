# Dashboard Keuangan Terpadu - Dokumentasi

## Overview
Dashboard Keuangan Terpadu adalah fitur komprehensif yang menampilkan analisis keuangan harian dan bulanan dengan visualisasi data yang interaktif. Fitur ini telah diintegrasikan ke dalam controller `Laporan.php` untuk konsistensi dengan sistem laporan yang ada.

## Fitur Utama

### 1. Dashboard Terpadu (`dashboard_terpadu`)
- **URL**: `/laporan/dashboard_terpadu`
- **Deskripsi**: Dashboard komprehensif dengan KPI cards, grafik trend, dan analisis mendalam

#### Komponen Dashboard:
- **KPI Cards**: Total Pendapatan, Total Pengeluaran, Profit Margin, Jumlah Transaksi
- **Grafik Trend**: Line chart pendapatan vs pengeluaran
- **Grafik Pie**: Distribusi metode pembayaran
- **Analisis Cash Flow**: Tabel analisis arus kas harian dan bulanan
- **Top Performers**: Ranking metode pembayaran berdasarkan performa
- **Filter Periode**: Filter tanggal mulai, akhir, dan spesifik
- **Quick Actions**: Link cepat ke fitur laporan lainnya

### 2. Rekap Metode Pembayaran (`rekap_metode_pembayaran`)
- **URL**: `/laporan/rekap_metode_pembayaran`
- **Deskripsi**: Analisis detail pembayaran berdasarkan metode (Cash, Transfer, EDC, E-Wallet)

#### Komponen Rekap:
- **Summary Cards**: Total pemasukan dan breakdown per metode
- **Grafik Pie**: Distribusi metode pembayaran
- **Grafik Bar**: Perbandingan nilai metode pembayaran
- **Detail Tables**: Breakdown per bank, mesin EDC, dan e-wallet
- **Transaction List**: Daftar lengkap transaksi dengan DataTables
- **Export Excel**: Export data ke format Excel

## Struktur Data

### Perhitungan Pendapatan
Menggunakan `SUM(total_tagihan)` dari tabel `tbl_pembayaran` sesuai dengan penjelasan user:
```php
$pendapatan_hari_ini = $this->db->select('SUM(total_tagihan) as total_pendapatan')
                                ->from('tbl_pembayaran')
                                ->where('pembayaran_tgl', $hariini)
                                ->where('perusahaan_id', $perusahaan_id)
                                ->get()
                                ->row_array();
```

### Metode Pembayaran
- **Cash**: `SUM(bayar_tunai)`
- **Transfer**: `SUM(bayar_transfer)` per rekening bank
- **EDC**: `SUM(bayar_debit)` per mesin EDC
- **E-Wallet**: `SUM(bayar_ewallet)` per provider e-wallet

## Method Controller

### Dashboard Terpadu
```php
public function dashboard_terpadu()
```
- Menampilkan dashboard dengan filter periode
- Memanggil helper methods untuk analisis data
- Menggunakan Chart.js untuk visualisasi

### Helper Methods
```php
private function get_comprehensive_financial_summary()
private function get_trend_analysis()
private function get_top_performers()
private function get_cash_flow_analysis()
private function get_payment_methods_analysis()
private function calculate_payment_methods()
```

### Export Methods
```php
public function export_dashboard()
public function export_rekap_metode_pembayaran()
```

## View Files

### 1. `dashboard_terpadu.php`
- Layout responsive dengan Bootstrap
- KPI cards dengan animasi
- Chart.js integration
- Filter periode interaktif
- Quick action buttons

### 2. `rekap_metode_pembayaran.php`
- Summary cards untuk setiap metode pembayaran
- Grafik pie dan bar chart
- Detail tables untuk breakdown
- DataTables untuk transaction list
- Export functionality

## Integrasi dengan Sistem

### Navigation
- Link ditambahkan di halaman index laporan (`index.php`)
- Card untuk Dashboard Terpadu dan Rekap Metode Pembayaran
- Quick access dari dashboard utama

### Export Modal
- Opsi export untuk dashboard terpadu dan rekap metode pembayaran
- JavaScript handler untuk export functionality
- Parameter periode dan tanggal

## Dependencies

### Frontend Libraries
- **Chart.js**: Untuk visualisasi grafik
- **DataTables**: Untuk tabel interaktif
- **Bootstrap**: Untuk layout dan styling
- **Font Awesome**: Untuk icons

### Backend Libraries
- **PHPExcel**: Untuk export Excel
- **CodeIgniter**: Framework utama

## Cara Penggunaan

### 1. Akses Dashboard Terpadu
1. Buka menu Laporan
2. Klik card "Dashboard Terpadu"
3. Gunakan filter periode untuk mengubah rentang data
4. Lihat KPI cards dan grafik analisis

### 2. Akses Rekap Metode Pembayaran
1. Buka menu Laporan
2. Klik card "Rekap Metode Pembayaran"
3. Pilih tanggal yang diinginkan
4. Lihat breakdown metode pembayaran

### 3. Export Data
1. Klik tombol "Export Excel" di halaman yang diinginkan
2. Atau gunakan modal export di halaman index
3. Pilih jenis laporan dan periode
4. Download file Excel

## Konfigurasi

### Database Tables
- `tbl_pembayaran`: Data transaksi pembayaran
- `tbl_rekening`: Data rekening bank
- `tbl_mesin_edc`: Data mesin EDC
- `tbl_ewallet`: Data provider e-wallet

### Session Data
- `perusahaan_id`: ID perusahaan aktif
- `user_id`: ID user yang login
- `level`: Level akses user

## Troubleshooting

### Common Issues
1. **Data tidak muncul**: Periksa `perusahaan_id` dan tanggal filter
2. **Grafik error**: Pastikan Chart.js library ter-load
3. **Export gagal**: Periksa permission folder dan PHPExcel library

### Debug Tips
- Gunakan `log_message()` untuk debugging
- Periksa query database dengan `$this->db->last_query()`
- Validasi data dengan `var_dump()` atau `print_r()`

## Update History

### v1.0 (Current)
- Implementasi dashboard terpadu
- Implementasi rekap metode pembayaran
- Export functionality
- Integration dengan sistem laporan existing
- Responsive design
- Chart.js visualizations

## Future Enhancements
- Real-time data updates
- Advanced filtering options
- Custom chart configurations
- PDF export functionality
- Email report scheduling
- Mobile app integration
