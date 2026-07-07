# Implementasi Sistem Hutang (Accounts Payable)

## 📋 Ringkasan Implementasi

Sistem hutang telah diimplementasikan untuk menangani pembayaran kredit pada pembelian barang. Ketika metode pembayaran adalah "kredit", sistem akan otomatis membuat record hutang di tabel `tbl_hutang`.

## 🗄️ Struktur Database

### Tabel `tbl_hutang`
```sql
CREATE TABLE `tbl_hutang` (
  `hutang_id` INT AUTO_INCREMENT PRIMARY KEY,
  `pembelian_id` INT NOT NULL,              -- transaksi pembelian barang
  `supplier_id` INT NOT NULL,
  `total_tagihan` DECIMAL(15,2) NOT NULL,
  `total_bayar` DECIMAL(15,2) DEFAULT 0,
  `sisa_hutang` DECIMAL(15,2) GENERATED ALWAYS AS (`total_tagihan` - `total_bayar`) STORED,
  `jatuh_tempo` DATE NOT NULL,
  `status` ENUM('BELUM LUNAS','SEBAGIAN','LUNAS') DEFAULT 'BELUM LUNAS',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`pembelian_id`) REFERENCES `tbl_pembelian`(`pembelian_id`),
  FOREIGN KEY (`supplier_id`) REFERENCES `tbl_supplier`(`supplier_id`)
);
```

### Indexes untuk Performance
- `idx_hutang_pembelian_id` - untuk pencarian berdasarkan pembelian
- `idx_hutang_supplier_id` - untuk pencarian berdasarkan supplier
- `idx_hutang_status` - untuk filter berdasarkan status
- `idx_hutang_jatuh_tempo` - untuk sorting dan filter jatuh tempo
- `idx_hutang_created_at` - untuk sorting berdasarkan tanggal pembuatan

### Tabel `tbl_hutang_pembayaran`
```sql
CREATE TABLE `tbl_hutang_pembayaran` (
  `pembayaran_id` INT AUTO_INCREMENT PRIMARY KEY,
  `hutang_id` INT NOT NULL,
  `tanggal_bayar` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `jumlah_bayar` DECIMAL(15,2) NOT NULL,
  `metode_bayar` ENUM('cash','transfer') NOT NULL,
  `rekening_tujuan` VARCHAR(255) NULL,
  `keterangan` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`hutang_id`) REFERENCES `tbl_hutang`(`hutang_id`)
);
```

### Indexes untuk tbl_hutang_pembayaran
- `idx_hutang_pembayaran_hutang_id` - untuk pencarian berdasarkan hutang
- `idx_hutang_pembayaran_tanggal` - untuk sorting berdasarkan tanggal pembayaran
- `idx_hutang_pembayaran_metode` - untuk filter berdasarkan metode pembayaran

### Foreign Key Constraints
- `pembelian_id` → `tbl_pembelian(pembelian_id)` - Referensi ke transaksi pembelian
- `supplier_id` → `tbl_supplier(supplier_id)` - Referensi ke data supplier
- **Note:** Foreign key constraints tanpa CASCADE untuk mencegah penghapusan data yang tidak disengaja

**Keuntungan Foreign Key tanpa CASCADE:**
- Mencegah penghapusan data pembelian/supplier yang masih memiliki hutang
- Memaksa user untuk menghapus hutang terlebih dahulu sebelum menghapus data referensi
- Menjaga integritas data dan mencegah data orphan

## 🔧 Modifikasi Controller

### 1. Controller `Gudang.php` - Method `action_barang_masuk()`

**Perubahan:**
- Menambahkan kondisi untuk mengecek metode pembayaran
- Jika `metode_bayar = 'kredit'`, maka insert data ke `tbl_hutang`
- **Jatuh tempo diambil dari field `termin` di tabel `tbl_supplier`**
- Menghapus insert ke tabel lama `hutang_pembelian`

**Field `termin` di `tbl_supplier`:**
- Field `termin` berisi jumlah hari untuk jatuh tempo pembayaran
- Jika field `termin` kosong atau null, default menggunakan 30 hari
- Contoh: `termin = 60` berarti jatuh tempo 60 hari setelah tanggal pembelian

**Perubahan:**
- Menambahkan kondisi untuk mengecek metode pembayaran
- Jika `metode_bayar = 'kredit'`, maka insert data ke `tbl_hutang`
- Menghapus insert ke tabel lama `hutang_pembelian`

 **Kode yang Ditambahkan:**
 ```php
 // Insert data hutang jika metode pembayaran adalah kredit
 if ($metode_bayar == 'kredit') {
     // Ambil data termin dari supplier
     $supplier_data = $this->db->select('termin')->where('supplier_id', $supplier_id)->get('tbl_supplier')->row();
     $termin_days = $supplier_data ? $supplier_data->termin : 30; // Default 30 hari jika tidak ada termin
     
     $hutangData = [
         'pembelian_id'      => $pembelian_id,
         'supplier_id'       => $supplier_id,
         'total_tagihan'     => $totalHarga,
         'total_bayar'       => 0, // Belum ada pembayaran
         'jatuh_tempo'       => date('Y-m-d', strtotime("+{$termin_days} days")), // Jatuh tempo berdasarkan termin supplier
         'status'            => 'BELUM LUNAS'
     ];
     $this->db->insert('tbl_hutang', $hutangData);
 }
 ```

### 2. Controller `Laporan.php` - Method `hutang()`

**Perubahan:**
- Menggunakan model `M_hutang` yang baru
- Menambahkan filter berdasarkan status dan limit
- Menghitung summary statistics
- Mengirim data yang diperlukan ke view

**Fitur Baru:**
- Filter berdasarkan status hutang
- Limit jumlah data yang ditampilkan
- Summary statistics (total hutang, total bayar, sisa hutang, jatuh tempo)

### 3. Controller `Laporan.php` - Method `update_pembayaran_hutang()`

**Fitur Baru:**
- Update pembayaran hutang dengan metode pembayaran
- Validasi input data (metode pembayaran wajib)
- Validasi rekening tujuan untuk transfer
- Update status hutang otomatis (BELUM LUNAS → SEBAGIAN → LUNAS)
- Form validation untuk mencegah pembayaran melebihi sisa hutang

### 4. Controller `Laporan.php` - Method `export_laporan_hutang()`

**Fitur Baru:**
- Export laporan hutang ke Excel
- Filter berdasarkan status dan tanggal
- Format yang rapi dengan header dan styling

## 📊 Model Baru

### Model `M_hutang.php`

**Method yang Tersedia:**

1. **`getAllHutang($limit, $status)`**
   - Mengambil semua data hutang dengan join ke tabel pembelian dan supplier
   - Support filter berdasarkan status dan limit

2. **`getHutangSummary()`**
   - Mengambil summary statistics hutang
   - Total hutang, total bayar, sisa hutang, jumlah lunas/belum lunas/jatuh tempo

3. **`getHutangById($hutang_id)`**
   - Mengambil detail hutang berdasarkan ID

4. **`getHutangByPembelianId($pembelian_id)`**
   - Mengambil hutang berdasarkan ID pembelian

5. **`updateHutangPayment($hutang_id, $jumlah_bayar)`**
   - Update pembayaran hutang (legacy method)
   - Menggunakan database transaction
   - Update status otomatis berdasarkan total bayar

6. **`updateHutangPaymentWithMethod($hutang_id, $jumlah_bayar, $metode_bayar, $rekening_tujuan, $keterangan)`**
   - Update pembayaran hutang dengan metode pembayaran
   - Menyimpan riwayat pembayaran ke tbl_hutang_pembayaran
   - Menggunakan database transaction
   - Update status otomatis berdasarkan total bayar

6. **`getOverdueHutang()`**
   - Mengambil hutang yang sudah jatuh tempo

7. **`getHutangDueToday()`**
   - Mengambil hutang yang jatuh tempo hari ini

8. **`getHutangBySupplier($supplier_id)`**
   - Mengambil hutang berdasarkan supplier

9. **`getHutangSummaryBySupplier($supplier_id)`**
   - Mengambil summary hutang berdasarkan supplier

10. **`getHutangForExport($status, $date_from, $date_to)`**
    - Mengambil data hutang untuk export dengan filter

## 🎨 View yang Diperbarui

### View `application/views/admin/laporan/keuangan/hutang/index.php`

**Perubahan:**
- Menggunakan field yang benar dari tabel `tbl_hutang`
- Menampilkan total tagihan, total bayar, dan sisa hutang
- Status yang konsisten (BELUM LUNAS, SEBAGIAN, LUNAS)
- Export Excel yang terintegrasi

**Field yang Digunakan:**
- `total_tagihan` - Total tagihan hutang
- `total_bayar` - Total yang sudah dibayar
- `sisa_hutang` - Sisa hutang (generated column)
- `status` - Status hutang (BELUM LUNAS/SEBAGIAN/LUNAS)
- `jatuh_tempo` - Tanggal jatuh tempo

**Fitur Pembayaran:**
- **Tombol Bayar** - Hanya muncul untuk hutang yang belum lunas
- **Modal Pembayaran** - Interface yang user-friendly untuk input pembayaran
- **Real-time Preview** - Preview sisa hutang setelah pembayaran
- **Form Validation** - Validasi jumlah bayar tidak boleh melebihi sisa hutang
- **Currency Formatting** - Format angka dengan pemisah ribuan

## 🔄 Alur Kerja Sistem

### 1. Pembelian Barang dengan Kredit
```
1. User input pembelian barang
2. Pilih metode pembayaran = 'kredit'
3. Sistem insert ke tbl_pembelian
4. Sistem insert ke tbl_pembelian_detail
5. Sistem ambil data termin dari tbl_supplier
6. Sistem insert ke tbl_hutang dengan status 'BELUM LUNAS' dan jatuh tempo berdasarkan termin
7. Update stok barang
```

### 2. Pembayaran Hutang
```
1. User input pembayaran hutang dengan metode (cash/transfer)
2. Jika transfer, user input rekening tujuan
3. Sistem update total_bayar di tbl_hutang
4. Sistem insert riwayat pembayaran ke tbl_hutang_pembayaran
5. Sistem update status otomatis:
   - Jika total_bayar >= total_tagihan → 'LUNAS'
   - Jika total_bayar > 0 → 'SEBAGIAN'
   - Jika total_bayar = 0 → 'BELUM LUNAS'
```

### 3. Laporan Hutang
```
1. Sistem mengambil data dari tbl_hutang
2. Join dengan tbl_pembelian dan tbl_supplier
3. Menampilkan summary statistics
4. Filter berdasarkan status dan tanggal
5. Export ke Excel jika diperlukan
```

## 📈 Status Hutang

### Enum Values:
- **`BELUM LUNAS`** - Belum ada pembayaran sama sekali
- **`SEBAGIAN`** - Sudah ada pembayaran tapi belum lunas
- **`LUNAS`** - Sudah lunas (total_bayar >= total_tagihan)

### Auto Update Status:
Status akan otomatis diupdate setiap kali ada pembayaran:
```php
if ($new_total_bayar >= $hutang->total_tagihan) {
    $new_status = 'LUNAS';
} elseif ($new_total_bayar > 0) {
    $new_status = 'SEBAGIAN';
} else {
    $new_status = 'BELUM LUNAS';
}
```

## 🎯 Fitur Utama

### ✅ Fitur yang Sudah Diimplementasikan:
1. **Auto Create Hutang** - Otomatis membuat hutang saat pembelian kredit
2. **Dynamic Jatuh Tempo** - Jatuh tempo berdasarkan field `termin` di supplier
3. **Status Management** - Status otomatis update berdasarkan pembayaran
4. **Summary Statistics** - Dashboard dengan ringkasan hutang
5. **Filter & Search** - Filter berdasarkan status dan tanggal
6. **Export Excel** - Export laporan ke Excel
7. **Payment Interface** - Modal pembayaran hutang dengan validasi
8. **Responsive Design** - Tampilan yang konsisten dengan template karyawan
9. **Database Transaction** - Keamanan data dengan transaction
10. **Generated Column** - Sisa hutang otomatis terhitung

### 🔮 Fitur yang Bisa Ditambahkan:
1. **Payment History** - Riwayat pembayaran hutang
2. **Multiple Payment Methods** - Berbagai metode pembayaran
3. **Reminder System** - Notifikasi hutang jatuh tempo
4. **Interest Calculation** - Perhitungan bunga hutang
5. **Supplier Portal** - Portal untuk supplier melihat hutang

## 🚀 Cara Penggunaan

### 1. Jalankan SQL Script
```bash
# Jalankan script untuk membuat tabel
mysql -u username -p database_name < create_tbl_hutang.sql
```

### 2. Akses Laporan Hutang
```
URL: /laporan/hutang
```

### 3. Filter Data
```
URL: /laporan/hutang?status=BELUM_LUNAS&limit=25
```

### 4. Export Excel
```
URL: /laporan/export_laporan_hutang?status=BELUM_LUNAS
```

## 📝 Catatan Penting

1. **Backup Database** - Selalu backup database sebelum menjalankan script
2. **Test Environment** - Test di environment development terlebih dahulu
3. **Data Migration** - Jika ada data lama, perlu migrasi ke struktur baru
4. **Permission** - Pastikan user memiliki permission yang cukup
5. **Monitoring** - Monitor performance query setelah implementasi
6. **Termin Supplier** - Pastikan field `termin` di `tbl_supplier` sudah terisi dengan benar
7. **Foreign Key Safety** - Foreign key tanpa CASCADE untuk mencegah penghapusan data yang tidak disengaja

## 💡 Contoh Penggunaan Termin

### Setup Termin di Supplier:
```sql
-- Update termin untuk supplier tertentu
UPDATE tbl_supplier SET termin = 60 WHERE supplier_id = 1; -- 60 hari
UPDATE tbl_supplier SET termin = 30 WHERE supplier_id = 2; -- 30 hari
UPDATE tbl_supplier SET termin = 90 WHERE supplier_id = 3; -- 90 hari
```

### Hasil Jatuh Tempo:
- Supplier dengan `termin = 30` → Jatuh tempo 30 hari setelah pembelian
- Supplier dengan `termin = 60` → Jatuh tempo 60 hari setelah pembelian
- Supplier dengan `termin = NULL` → Jatuh tempo default 30 hari setelah pembelian

## 🔧 Troubleshooting

### Error: "Table tbl_hutang doesn't exist"
- Pastikan script SQL sudah dijalankan
- Cek nama database dan user permission

### Error: "Foreign key constraint fails"
- Pastikan data di tbl_pembelian dan tbl_supplier sudah ada
- Cek integritas data sebelum insert
- **Note:** Foreign key tanpa CASCADE, pastikan data referensi tidak dihapus

### Performance Issue
- Cek index yang sudah dibuat
- Monitor query execution time
- Optimize query jika diperlukan

---

**Implementasi ini memberikan sistem hutang yang robust dan terintegrasi dengan sistem pembelian yang sudah ada.**
