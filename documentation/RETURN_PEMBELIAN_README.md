# Sistem Return Pembelian - TGM ERP

## Deskripsi
Sistem Return Pembelian adalah modul yang memungkinkan pengguna untuk mengelola return barang yang telah dibeli dari supplier. Sistem ini terintegrasi dengan modul gudang dan akan otomatis mengurangi stok gudang ketika return disimpan.

## Fitur Utama

### 1. Daftar Return Pembelian
- Menampilkan semua data return pembelian
- Informasi: No Return, Tanggal, Supplier, Karyawan, Total Return
- Aksi: Detail, Cetak, Hapus

### 2. Tambah Return Pembelian
- Form input return pembelian
- Pilih supplier dan pembelian yang akan diretur
- Input quantity return dan alasan return
- Validasi stok sebelum return
- Sistem temporary untuk menyimpan item return

### 3. Detail Return Pembelian
- Menampilkan detail lengkap return pembelian
- Informasi header dan detail item
- Tombol cetak dokumen

### 4. Cetak Return Pembelian
- Template cetak yang profesional
- Informasi lengkap return pembelian
- Tanda tangan karyawan, supplier, dan manager

## Struktur Database

### Tabel `tbl_return_pembelian` (Header)
- `return_id` - Primary key
- `no_return` - Nomor return
- `supplier_id` - ID supplier
- `return_tgl` - Tanggal return
- `return_waktu` - Timestamp
- `return_total` - Total nilai return
- `keterangan` - Keterangan return
- `karyawan_id` - ID karyawan yang membuat
- `perusahaan_id` - ID perusahaan

### Tabel `tbl_return_pembelian_detail` (Detail)
- `return_detail_id` - Primary key
- `return_id` - Foreign key ke header
- `bahan_id` - ID bahan yang diretur
- `qty_return` - Quantity yang diretur
- `harga_return` - Harga return per unit
- `alasan_return` - Alasan return
- `created_at` - Timestamp

### Tabel `tbl_temp_return` (Temporary)
- `temp_id` - Primary key
- `bahan_id` - ID bahan
- `qty_return` - Quantity return
- `harga_return` - Harga return
- `alasan_return` - Alasan return
- `created_at` - Timestamp

## Alur Kerja

### 1. Proses Input Return
1. User memilih menu "Tambah Return Pembelian"
2. User mengisi data header (supplier, tanggal, no return)
3. User memilih pembelian yang akan diretur
4. Sistem menampilkan detail pembelian
5. User input quantity dan alasan return untuk setiap item
6. Item ditambahkan ke temporary table
7. User review daftar return
8. User simpan return pembelian

### 2. Proses Penyimpanan
1. Validasi form input
2. Insert data ke `tbl_return_pembelian`
3. Insert detail ke `tbl_return_pembelian_detail`
4. Update stok gudang (mengurangi stok_tambah)
5. Clear temporary table
6. Log activity

### 3. Proses Hapus Return
1. Ambil detail return yang akan dihapus
2. Restore stok gudang (menambah stok_tambah)
3. Hapus detail return
4. Hapus header return

## Controller Methods

### Gudang Controller
- `returnPembelian()` - Daftar return pembelian
- `addReturnPembelian()` - Form tambah return
- `getPembelianDetail()` - AJAX get detail pembelian
- `addTempReturn()` - Tambah item ke temporary
- `deleteTempReturn()` - Hapus item dari temporary
- `actionAddReturnPembelian()` - Simpan return pembelian
- `detailReturnPembelian()` - Detail return pembelian
- `cetakReturnPembelian()` - Cetak return pembelian
- `deleteReturnPembelian()` - Hapus return pembelian

## Model Methods

### M_gudang Model
- `getDataReturnPembelian()` - Get semua data return
- `getDetailReturnPembelian()` - Get detail return
- `getPembelianForReturn()` - Get pembelian untuk return
- `getPembelianDetailForReturn()` - Get detail pembelian
- `getTempReturn()` - Get temporary return
- `getReturnPembelianById()` - Get return by ID
- `updateStokAfterReturn()` - Update stok setelah return

## Views

### 1. returnPembelian.php
- Daftar return pembelian
- Tabel dengan informasi lengkap
- Tombol aksi (detail, cetak, hapus)

### 2. addReturnPembelian.php
- Form input return pembelian
- Dropdown supplier dan pembelian
- Tabel detail pembelian (AJAX)
- Tabel temporary return
- JavaScript untuk interaksi

### 3. detailReturnPembelian.php
- Detail lengkap return pembelian
- Informasi header dan detail
- Tombol cetak

### 4. cetakReturnPembelian.php
- Template cetak profesional
- CSS untuk print
- Informasi lengkap return

## Validasi

### 1. Form Validation
- Supplier wajib diisi
- Tanggal return wajib diisi
- No return wajib diisi
- Quantity return harus > 0
- Alasan return wajib diisi

### 2. Business Logic Validation
- Item tidak boleh duplikat di temporary
- Quantity return tidak boleh melebihi quantity pembelian
- Stok harus mencukupi untuk return

## Integrasi dengan Sistem

### 1. Stok Gudang
- Return akan mengurangi `stok_tambah` di `tbl_stok_gudang`
- Hapus return akan mengembalikan stok

### 2. Activity Log
- Semua aksi return akan dicatat di `user_activity_log`
- Module: 'Gudang'
- Action: 'input_data_return_pembelian'

### 3. Session Management
- Menggunakan session user untuk karyawan_id
- Validasi login di setiap method

## Keamanan

### 1. Input Validation
- Form validation menggunakan CodeIgniter
- Sanitasi input untuk mencegah XSS
- Validasi tipe data

### 2. Authorization
- Cek session login
- Validasi hak akses user

### 3. Data Integrity
- Foreign key constraints
- Transaction handling
- Backup stok sebelum update

## Instalasi

### 1. Database
```sql
-- Jalankan script return_pembelian_tables.sql
-- Pastikan tabel tbl_supplier, tbl_bahan, tbl_karyawan sudah ada
```

### 2. File Upload
- Upload semua file view ke `application/views/admin/gudang/`
- Pastikan controller dan model sudah terupdate

### 3. Menu Integration
- Tambahkan menu "Return Pembelian" di sidebar
- Link ke `gudang/returnPembelian`

## Troubleshooting

### 1. Error "Table doesn't exist"
- Pastikan script SQL sudah dijalankan
- Cek nama tabel dan struktur

### 2. Error "Foreign key constraint"
- Pastikan data supplier, bahan, karyawan sudah ada
- Cek referensi foreign key

### 3. Stok tidak terupdate
- Cek method `updateStokAfterReturn()`
- Pastikan `tbl_stok_gudang` memiliki data untuk bahan tersebut

## Maintenance

### 1. Backup Data
- Backup tabel return pembelian secara berkala
- Backup sebelum update sistem

### 2. Cleanup Temporary
- Temporary table akan otomatis ter-clear setelah simpan
- Bisa ditambahkan cron job untuk cleanup manual

### 3. Monitoring
- Monitor penggunaan sistem return
- Cek log activity untuk audit trail 