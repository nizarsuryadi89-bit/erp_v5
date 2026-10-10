# Troubleshooting Form Validation - Return Pembelian

## Masalah: "Semua field harus diisi" padahal sudah diisi

### Penyebab Umum:

1. **CSRF Token Missing**
2. **Form Action URL Salah**
3. **Input Name Attribute Tidak Sesuai**
4. **Session Expired**
5. **Database Connection Error**

### Langkah-langkah Pemeriksaan:

#### 1. Cek Form yang Digunakan
Ada 2 versi form:
- **Form Original**: `gudang/addReturnPembelian` (dengan AJAX)
- **Form Simple**: `gudang/addReturnPembelianSimple` (tanpa AJAX)

#### 2. Test dengan Form Simple
Akses form sederhana untuk testing:
```
http://localhost/tgm_erp/gudang/addReturnPembelianSimple
```

#### 3. Cek Debug Information
Akses halaman debug untuk melihat data:
```
http://localhost/tgm_erp/gudang/debugReturn
```

#### 4. Cek Log Error
Lihat file log di `application/logs/` untuk error detail.

### Solusi Step by Step:

#### A. Jika CSRF Token Error
**Gejala:** Error "The action you have requested is not allowed"

**Solusi:**
1. Nonaktifkan CSRF sementara di `application/config/config.php`:
```php
$config['csrf_protection'] = FALSE;
```

2. Atau tambahkan CSRF token ke form:
```php
<input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
```

#### B. Jika Form Action Salah
**Gejala:** Form tidak terkirim ke controller yang benar

**Solusi:**
1. Pastikan action URL benar:
```php
<form action="<?= base_url('gudang/addTempReturn') ?>" method="post">
```

2. Cek route di `application/config/routes.php`

#### C. Jika Input Name Tidak Sesuai
**Gejala:** Data tidak terkirim dengan nama yang benar

**Solusi:**
1. Pastikan name attribute sesuai dengan yang diharapkan controller:
```php
<input name="bahan_id" ...>
<input name="qty_return" ...>
<input name="harga_return" ...>
<input name="alasan_return" ...>
```

#### D. Jika Session Expired
**Gejala:** Error "Undefined index: user_id"

**Solusi:**
1. Login ulang ke sistem
2. Cek session data di debug page

#### E. Jika Database Error
**Gejala:** Error koneksi database

**Solusi:**
1. Cek konfigurasi database di `application/config/database.php`
2. Pastikan tabel `tbl_temp_return` sudah ada

### Testing dengan Form Simple:

1. **Buka form simple:**
   ```
   http://localhost/tgm_erp/gudang/addReturnPembelianSimple
   ```

2. **Isi form dengan data valid:**
   - Pilih Bahan
   - Masukkan Qty Return (minimal 1)
   - Masukkan Harga Return (minimal 0)
   - Masukkan Alasan Return

3. **Klik "Tambah"**
   - Cek apakah data masuk ke daftar return
   - Cek apakah ada pesan error

4. **Jika berhasil, isi form header:**
   - Pilih Supplier
   - Masukkan Tanggal Return
   - Masukkan No Return
   - Klik "Simpan Return"

### Debug Commands:

```php
// Cek POST data
echo "<pre>";
print_r($_POST);
echo "</pre>";

// Cek session
echo "<pre>";
print_r($this->session->userdata());
echo "</pre>";

// Cek database error
$error = $this->db->error();
print_r($error);
```

### File yang Perlu Diperiksa:

1. `application/views/admin/gudang/addReturnPembelian.php` - Form original
2. `application/views/admin/gudang/addReturnPembelian_simple.php` - Form simple
3. `application/controllers/Gudang.php` - Method `addTempReturn()`
4. `application/config/config.php` - CSRF settings
5. `application/config/database.php` - Database settings

### Jika Masih Bermasalah:

1. **Gunakan form simple** untuk testing
2. **Aktifkan error reporting** di `index.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

3. **Cek log CodeIgniter** di `application/logs/`

4. **Test dengan data minimal**:
   - Hanya 1 bahan
   - Qty = 1
   - Harga = 1000
   - Alasan = "Test"

5. **Hubungi developer** untuk bantuan lebih lanjut. 