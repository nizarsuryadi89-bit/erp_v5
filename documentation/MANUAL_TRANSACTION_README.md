# Modul Transaksi Penjualan Manual - TGM ERP

## Deskripsi
Modul transaksi penjualan manual memungkinkan petugas untuk membuat transaksi penjualan secara manual dengan input data konsumen, produk/jasa, dan metode pembayaran yang fleksibel.

## Fitur Utama

### 1. Form Input Transaksi
- **Tanggal Transaksi**: Input tanggal transaksi (default: hari ini)
- **Konsumen**: Select2 dropdown dengan data konsumen aktif
- **Produk/Jasa**: Select2 dropdown dengan data produk aktif
- **Jumlah**: Input jumlah produk/jasa (minimal 1)
- **Harga Satuan**: Input harga per unit dengan format Rupiah
- **Diskon**: Input diskon (opsional, default: 0)
- **Metode Pembayaran**: Pilihan metode pembayaran

### 2. Metode Pembayaran yang Didukung
- **Tunai**: Pembayaran langsung dengan uang tunai
- **Transfer Bank**: Pembayaran via transfer bank dengan pilihan rekening
- **Debit/EDC**: Pembayaran dengan kartu debit melalui mesin EDC
- **Kartu Kredit**: Pembayaran dengan kartu kredit
- **E-Wallet**: Pembayaran melalui aplikasi e-wallet
- **Piutang**: Pembayaran dengan sistem kredit/piutang
- **Lainnya**: Metode pembayaran lainnya

### 3. Validasi Form
- Semua field wajib diisi (kecuali diskon, referensi, dan keterangan)
- Validasi jumlah minimal 1
- Validasi harga satuan minimal 0
- Validasi diskon minimal 0
- Validasi tambahan untuk metode pembayaran tertentu:
  - Transfer: wajib pilih rekening
  - E-Wallet: wajib pilih e-wallet
  - Debit: wajib pilih mesin EDC

### 4. Perhitungan Otomatis
- Subtotal = Jumlah × Harga Satuan
- Total Tagihan = Subtotal - Diskon
- Perhitungan real-time saat input berubah

### 5. Integrasi Database
- **tbl_pemesanan**: Menyimpan data transaksi utama
- **tbl_pemesanan_detail**: Menyimpan detail produk yang dibeli
- **tbl_pembayaran**: Menyimpan data pembayaran
- **tbl_pembayaran_detail**: Menyimpan detail metode pembayaran
- **tbl_piutang**: Otomatis dibuat jika metode pembayaran = piutang

## Struktur File

### Controller: `application/controllers/Penjualan_manual.php`
```php
class Penjualan_manual extends CI_Controller {
    public function index()          // Tampilkan form
    public function simpan()         // Simpan transaksi
    public function get_produk_detail()    // AJAX: get detail produk
    public function get_konsumen_detail()  // AJAX: get detail konsumen
}
```

### Model: `application/models/Penjualan_manual_model.php`
```php
class Penjualan_manual_model extends CI_Model {
    public function get_konsumen()           // Get data konsumen
    public function get_produk()             // Get data produk
    public function get_metode_pembayaran()  // Get opsi metode pembayaran
    public function get_rekening()           // Get data rekening bank
    public function get_ewallet()            // Get data e-wallet
    public function get_edc()                // Get data mesin EDC
    public function simpan_transaksi()       // Simpan transaksi ke database
    public function get_transaksi_terakhir() // Get transaksi terakhir
}
```

### View: `application/views/admin/transaksi/manual.php`
- Form input dengan Bootstrap 5
- Select2 untuk dropdown
- SweetAlert2 untuk notifikasi
- Tabel transaksi terakhir

## Alur Kerja

### 1. Input Transaksi
1. User mengakses halaman transaksi manual
2. Mengisi form dengan data transaksi
3. Memilih metode pembayaran
4. Jika metode pembayaran tertentu, form tambahan muncul
5. Melihat perhitungan otomatis

### 2. Validasi
1. Client-side validation dengan JavaScript
2. Server-side validation dengan CodeIgniter Form Validation
3. Validasi tambahan untuk metode pembayaran tertentu

### 3. Penyimpanan Data
1. Mulai database transaction
2. Generate NO SPK otomatis (format: SPK-MAN-YYYYMMDD-XXXX)
3. Simpan ke tbl_pemesanan
4. Simpan detail ke tbl_pemesanan_detail
5. Simpan ke tbl_pembayaran
6. Jika bukan piutang, simpan detail pembayaran ke tbl_pembayaran_detail
7. Jika piutang, simpan ke tbl_piutang
8. Commit transaction

### 4. Notifikasi
1. SweetAlert2 menampilkan loading
2. Success/error message
3. Reset form jika berhasil
4. Reload halaman untuk update tabel

## Format NO SPK
```
SPK-MAN-YYYYMMDD-XXXX
Contoh: SPK-MAN-20250820-0001
```

## Struktur Database

### tbl_pemesanan
```sql
- nospk (VARCHAR) - NO SPK unik
- konsumen_id (INT) - ID konsumen
- deskprint_id (INT) - 0 untuk manual
- id_jenis_transaksi (INT) - 2 untuk manual
- perusahaan_id (INT) - ID perusahaan
- status_pembayaran (ENUM) - 'paid' atau 'pending'
- created_at (DATETIME)
```

### tbl_pemesanan_detail
```sql
- nospk (VARCHAR) - Referensi ke tbl_pemesanan
- produk_id (INT) - ID produk
- qty (INT) - Jumlah
- harga_id (INT) - ID harga produk
```

### tbl_pembayaran
```sql
- nospk (VARCHAR) - Referensi ke tbl_pemesanan
- konsumen_id (INT) - ID konsumen
- tgl_bayar (DATE) - Tanggal pembayaran
- total_tagihan (DECIMAL) - Total yang harus dibayar
- total_bayar (DECIMAL) - Total yang sudah dibayar
- status_lunas (ENUM) - '0' atau '1'
- kasir_id (INT) - ID kasir
- perusahaan_id (INT) - ID perusahaan
```

### tbl_pembayaran_detail
```sql
- pembayaran_id (INT) - Referensi ke tbl_pembayaran
- metode (ENUM) - Metode pembayaran
- jumlah (DECIMAL) - Jumlah pembayaran
- referensi (VARCHAR) - No bukti/referensi
- bank_id (INT) - ID rekening (untuk transfer)
- edc_id (INT) - ID mesin EDC (untuk debit)
- ewallet_id (INT) - ID e-wallet (untuk e-wallet)
- keterangan (TEXT) - Keterangan tambahan
```

### tbl_piutang (jika metode = piutang)
```sql
- nospk (VARCHAR) - Referensi ke tbl_pemesanan
- konsumen_id (INT) - ID konsumen
- perusahaan_id (INT) - ID perusahaan
- total_tagihan (DECIMAL) - Total tagihan
- total_bayar (DECIMAL) - 0 (belum bayar)
- jatuh_tempo (DATE) - Tanggal jatuh tempo
- status (ENUM) - 'BELUM LUNAS'
```

## Keamanan

### 1. Validasi Input
- Server-side validation untuk semua input
- Sanitasi data sebelum disimpan
- Validasi session user

### 2. Database Transaction
- Menggunakan database transaction untuk konsistensi data
- Rollback otomatis jika terjadi error

### 3. Access Control
- Validasi session user
- Cek hak akses user

## Dependencies

### CSS/JS Libraries
- Bootstrap 5
- Select2 (untuk dropdown)
- SweetAlert2 (untuk notifikasi)
- FontAwesome (untuk icons)

### Database Tables
- tbl_konsumen
- tbl_produk
- tbl_rekening
- tbl_ewallet
- tbl_mesin_edc
- tbl_harga_product
- tbl_pemesanan
- tbl_pemesanan_detail
- tbl_pembayaran
- tbl_pembayaran_detail
- tbl_piutang

## Cara Penggunaan

### 1. Akses Modul
```
URL: /penjualan_manual
Method: GET
```

### 2. Input Transaksi
1. Pilih tanggal transaksi
2. Pilih konsumen dari dropdown
3. Pilih produk/jasa dari dropdown
4. Input jumlah dan harga satuan
5. Input diskon (opsional)
6. Pilih metode pembayaran
7. Jika diperlukan, pilih detail metode pembayaran
8. Input referensi dan keterangan (opsional)
9. Klik "Simpan Transaksi"

### 3. Hasil
- Transaksi tersimpan di database
- NO SPK otomatis digenerate
- Jika piutang, entry otomatis dibuat di tbl_piutang
- Notifikasi sukses dengan SweetAlert2
- Form direset dan halaman di-reload

## Troubleshooting

### 1. Error Validasi
- Pastikan semua field wajib terisi
- Cek format input (angka, tanggal, dll)
- Pastikan metode pembayaran sesuai dengan field tambahan

### 2. Error Database
- Cek koneksi database
- Pastikan semua tabel ada dan struktur sesuai
- Cek foreign key constraints

### 3. Error JavaScript
- Pastikan semua library JS ter-load
- Cek console browser untuk error
- Pastikan jQuery tersedia

## Pengembangan Selanjutnya

### 1. Fitur Tambahan
- Upload bukti pembayaran
- Multiple produk dalam satu transaksi
- Template transaksi
- Export data transaksi

### 2. Integrasi
- Integrasi dengan printer thermal
- Integrasi dengan payment gateway
- Integrasi dengan accounting system

### 3. Reporting
- Laporan transaksi manual
- Analisis penjualan
- Dashboard transaksi

## Support
Untuk bantuan teknis atau pertanyaan, silakan hubungi tim development TGM ERP.
