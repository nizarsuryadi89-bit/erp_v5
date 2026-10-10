# Detail Produksi Insert Fix - Langkah-langkah Lengkap

## Masalah
Proses insert `detail_produksi` belum bekerja dengan baik. Ketika transaksi diproses, sistem seharusnya otomatis membuat record di `tbl_produksi` dan `detail_produksi`, tetapi ini gagal.

## Langkah-langkah Perbaikan

### Langkah 1: Jalankan SQL Script untuk Memperbaiki Struktur Database

1. **Buka phpMyAdmin** atau MySQL client Anda
2. **Jalankan script SQL** berikut:

```sql
-- Jalankan fix_detail_produksi_simple.sql
-- Script ini akan memperbaiki struktur tabel detail_produksi
```

3. **Verifikasi hasil** dengan melihat output:
   - Tabel `detail_produksi` harus memiliki semua field yang diperlukan
   - Tidak ada error saat menjalankan script

### Langkah 2: Test Database Structure

1. **Kunjungi URL test**:
   ```
   http://your-domain/wip/test_db
   ```

2. **Periksa output** yang diharapkan:
   ```
   Database connection: OK
   detail_produksi table fields: ["detail_produksi_id", "produksi_id", "routing_id", ...]
   All required fields present
   Read from detail_produksi: OK
   Insert test: SUCCESS
   Test insert rolled back
   ```

### Langkah 3: Test Detail Produksi Insert

1. **Kunjungi URL test khusus**:
   ```
   http://your-domain/wip/test_detail_produksi_insert
   ```

2. **Periksa output** untuk:
   - Jumlah produk yang tersedia
   - Data routing untuk produk
   - Struktur tabel detail_produksi
   - Hasil test insert

### Langkah 4: Periksa Data Routing

Jika test menunjukkan "No routing data found", ini adalah penyebab utama masalah. Lakukan:

1. **Periksa tabel routing**:
   ```sql
   SELECT COUNT(*) FROM routing;
   SELECT * FROM routing LIMIT 5;
   ```

2. **Jika tabel routing kosong**, Anda perlu:
   - Membuat data routing untuk setiap produk
   - Atau menggunakan produk yang sudah memiliki routing

### Langkah 5: Test Transaksi Real

1. **Buat transaksi baru** di sistem
2. **Periksa log file** di:
   ```
   application/logs/log-YYYY-MM-DD.php
   ```

3. **Cari log entries** berikut:
   ```
   === START insertProduksi ===
   insertProduksi called with item: ...
   Getting routing for product_id: ...
   Routings found: X
   Produksi ID generated: X
   Detail produksi inserted successfully with ID: X
   === END insertProduksi ===
   ```

### Langkah 6: Troubleshooting

#### Jika Masih Ada Error:

1. **Periksa Log File**:
   - Buka `application/logs/log-YYYY-MM-DD.php`
   - Cari error messages terkait `detail_produksi`

2. **Periksa Database**:
   ```sql
   -- Periksa struktur tabel
   DESCRIBE detail_produksi;
   
   -- Periksa data yang ada
   SELECT COUNT(*) FROM detail_produksi;
   SELECT * FROM detail_produksi ORDER BY detail_produksi_id DESC LIMIT 5;
   ```

3. **Periksa Routing Data**:
   ```sql
   -- Periksa routing untuk produk tertentu
   SELECT * FROM routing WHERE product_id = [PRODUCT_ID];
   ```

#### Jika Tidak Ada Data Routing:

1. **Buat data routing** untuk produk:
   ```sql
   INSERT INTO routing (product_id, routing_name, routing_urutan, operator_id, bahan_id) 
   VALUES (1, 'Proses 1', 1, 1, 1);
   ```

2. **Atau gunakan produk yang sudah ada routing**

## Expected Behavior Setelah Fix

1. **Transaction Processing**: Ketika pembayaran diproses, record produksi otomatis dibuat
2. **Production Records**: Setiap produk dalam transaksi membuat satu record di `tbl_produksi`
3. **Detail Records**: Setiap langkah routing untuk setiap produk membuat record di `detail_produksi`
4. **Status Tracking**: Semua detail record dimulai dengan `status_produksi = '0'` (pending)
5. **WIP Tracking**: Halaman WIP tracking akan menampilkan item produksi baru

## Monitoring

1. **Periksa log file** secara berkala untuk error
2. **Verifikasi data integrity** produksi
3. **Periksa konfigurasi routing** yang hilang

## Support

Jika masalah masih berlanjut:

1. **Jalankan test methods** yang telah dibuat
2. **Periksa log file** untuk error spesifik
3. **Verifikasi struktur database** dengan test methods
4. **Test dengan transaksi sederhana** untuk mengisolasi masalah

## File yang Telah Dimodifikasi

1. **`application/models/M_pembayaran.php`** - Enhanced dengan logging detail
2. **`application/controllers/Wip.php`** - Added test methods
3. **`fix_detail_produksi_simple.sql`** - SQL script untuk memperbaiki struktur
4. **`DETAIL_PRODUKSI_INSERT_FIX.md`** - Dokumentasi ini

## Catatan Penting

- **Routing Data**: Masalah utama biasanya adalah tidak ada data routing untuk produk
- **Table Structure**: Pastikan tabel `detail_produksi` memiliki semua field yang diperlukan
- **Logging**: Gunakan log file untuk debugging masalah
- **Test Methods**: Gunakan test methods yang telah dibuat untuk verifikasi
