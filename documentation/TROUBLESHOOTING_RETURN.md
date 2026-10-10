# Troubleshooting Return Pembelian System

## Masalah: Data Return Tidak Masuk ke Database

### Langkah-langkah Pemeriksaan:

#### 1. Cek Tabel Database
Jalankan script `check_return_tables.php` di browser untuk memastikan tabel sudah ada:
```
http://localhost/tgm_erp/check_return_tables.php
```

Jika tabel belum ada, jalankan SQL script `create_return_tables.sql` di database MySQL.

#### 2. Cek Debug Data
Akses halaman debug untuk melihat data yang ada:
```
http://localhost/tgm_erp/gudang/debugReturn
```

#### 3. Periksa Log Error
Cek file log CodeIgniter di `application/logs/` untuk melihat error yang terjadi.

#### 4. Cek Session Data
Pastikan session user masih aktif dan memiliki data yang diperlukan:
- `user_id`
- `perusahaan_id`

#### 5. Cek Form Input
Pastikan semua field required terisi:
- `supplier_id`
- `return_tgl`
- `no_return`
- `bahan_id`
- `qty_return`
- `harga_return`
- `alasan_return`

### Kemungkinan Penyebab dan Solusi:

#### A. Tabel Database Tidak Ada
**Gejala:** Error "Table doesn't exist"
**Solusi:** Jalankan SQL script untuk membuat tabel

#### B. Foreign Key Constraint Error
**Gejala:** Error "Cannot add or update a child row"
**Solusi:** Pastikan data supplier_id dan karyawan_id ada di tabel referensi

#### C. Session Expired
**Gejala:** Error "Undefined index: user_id"
**Solusi:** Login ulang ke sistem

#### D. Form Validation Error
**Gejala:** Redirect ke halaman add tanpa pesan error
**Solusi:** Periksa semua field required terisi

#### E. Database Connection Error
**Gejala:** Error koneksi database
**Solusi:** Periksa konfigurasi database di `application/config/database.php`

### Testing Step by Step:

1. **Test Temp Table Insert:**
   - Buka halaman Add Return Pembelian
   - Isi form dengan data valid
   - Klik "Tambah ke Daftar"
   - Cek apakah data masuk ke `tbl_temp_return`

2. **Test Final Save:**
   - Setelah ada data di temp table
   - Isi form header (supplier, tanggal, no return)
   - Klik "Simpan Return"
   - Cek apakah data masuk ke `tbl_return_pembelian` dan `tbl_return_pembelian_detail`

3. **Test Stock Update:**
   - Cek apakah stok berkurang setelah return disimpan
   - Lihat di halaman stok gudang

### Debug Commands:

```php
// Cek data temp
$temp_data = $this->db->get('tbl_temp_return')->result();
print_r($temp_data);

// Cek error database
$errors = $this->db->error();
print_r($errors);

// Cek session
print_r($this->session->userdata());
```

### File yang Perlu Diperiksa:

1. `application/controllers/Gudang.php` - Method `addTempReturn()` dan `actionAddReturnPembelian()`
2. `application/models/M_gudang.php` - Method `updateStokAfterReturn()`
3. `application/views/admin/gudang/addReturnPembelian.php` - Form view
4. Database tables: `tbl_temp_return`, `tbl_return_pembelian`, `tbl_return_pembelian_detail`

### Jika Masih Bermasalah:

1. Aktifkan error reporting di `index.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

2. Cek log CodeIgniter di `application/logs/`

3. Tambahkan debugging di controller:
```php
log_message('debug', 'Data being inserted: ' . json_encode($data));
```

4. Hubungi developer untuk bantuan lebih lanjut. 