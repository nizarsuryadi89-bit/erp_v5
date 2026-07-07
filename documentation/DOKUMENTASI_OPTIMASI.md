# Panduan Optimasi Performa Database TGM Core

Dokumen ini berisi langkah-langkah teknis untuk mengoptimalkan performa database **TGM Core** (MySQL/MariaDB) agar tetap kencang dan bebas lag saat data sudah mencapai ribuan hingga jutaan baris.

## 1. Menambahkan Database Index (Wajib)

Indexing adalah kunci utama kecepatan pencarian data. Tanpa index, server harus membaca seluruh baris tabel satu per satu. Jalankan perintah SQL berikut di phpMyAdmin atau terminal database online Anda:

```sql
-- 1. Optimasi Tabel Pemesanan (Pusat Transaksi)
ALTER TABLE tbl_pemesanan 
ADD INDEX idx_konsumen (konsumen_id), 
ADD INDEX idx_perusahaan (perusahaan_id), 
ADD INDEX idx_status_bayar (status_pembayaran), 
ADD INDEX idx_created (created_at);

-- 2. Optimasi Tabel SPK (Pusat Antrian Produksi)
ALTER TABLE tbl_spk 
ADD INDEX idx_konsumen_spk (konsumen_id), 
ADD INDEX idx_perusahaan_spk (perusahaan_id), 
ADD INDEX idx_user_spk (user_id), 
ADD INDEX idx_spk_time (spk_time);

-- 3. Optimasi Tabel Detail Pemesanan (Performa Kalkulasi & Nota)
ALTER TABLE tbl_pemesanan_detail 
ADD INDEX idx_nospk_detail (nospk), 
ADD INDEX idx_product_detail (product_id);

-- 4. Optimasi Tabel Pembayaran (Performa Laporan Keuangan)
ALTER TABLE tbl_pembayaran 
ADD INDEX idx_nospk_pembayaran (nospk),
ADD INDEX idx_perusahaan_bayar (perusahaan_id),
ADD INDEX idx_tgl_bayar (tgl_bayar);

-- 5. Optimasi Tabel Produksi (Melancarkan Antrean Produksi)
ALTER TABLE tbl_produksi 
ADD INDEX idx_nospk_prod (nospk), 
ADD INDEX idx_status_prod (status_produksi), 
ADD INDEX idx_perusahaan_prod (perusahaan_id),
ADD INDEX idx_tgl_selesai (tgl_selesai);
```

## 2. Optimasi Server Online ( .htaccess )

Agar pengiriman data CSS, JS, dan Gambar lebih cepat melalui internet, tambahkan konfigurasi kompresi Gzip dan Caching di file `.htaccess` pada root folder hosting Anda:

```apache
<IfModule mod_deflate.c>
  AddOutputFilterByType DEFLATE text/plain
  AddOutputFilterByType DEFLATE text/html
  AddOutputFilterByType DEFLATE text/xml
  AddOutputFilterByType DEFLATE text/css
  AddOutputFilterByType DEFLATE application/xml
  AddOutputFilterByType DEFLATE application/xhtml+xml
  AddOutputFilterByType DEFLATE application/rss+xml
  AddOutputFilterByType DEFLATE application/javascript
  AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresDefault "access plus 1 month"
  ExpiresByType image/x-icon "access plus 1 year"
  ExpiresByType image/jpeg "access plus 1 month"
  ExpiresByType image/png "access plus 1 month"
  ExpiresByType image/gif "access plus 1 month"
  ExpiresByType text/css "access plus 1 month"
  ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

## 3. Catatan Maintenance

- **Data Cleanup**: Lakukan archive data log jika sudah lebih dari 1 tahun.
- **Optimize Table**: Jalankan perintah `OPTIMIZE TABLE nama_tabel` secara berkala (misal 1 bulan sekali) untuk merapikan fragmentasi data.

---
*Dokumen ini dibuat secara otomatis oleh TGM Core Developer Assistant.*
