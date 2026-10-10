# Troubleshooting Foreign Key Constraint - Return Pembelian

## Masalah: Foreign Key Constraint Fails

### Error yang Ditemui:
```
Cannot add or update a child row: a foreign key constraint fails 
(`db_erp`.`tbl_return_pembelian`, CONSTRAINT `fk_return_karyawan` 
FOREIGN KEY (`karyawan_id`) REFERENCES `tbl_karyawan` (`karyawan_id`) ON DELETE CASCADE)
```

### Penyebab:
- `karyawan_id = 58` tidak ada di tabel `tbl_karyawan`
- User ID dari session tidak sesuai dengan data di tabel karyawan
- Foreign key constraint terlalu ketat

### Solusi:

#### 1. Cek Data Karyawan
Akses halaman debug untuk melihat data:
```
http://localhost/tgm_erp/gudang/debugReturn
```

#### 2. Jalankan Script SQL untuk Memperbaiki
Pilih salah satu solusi:

**A. Script dengan Foreign Key (Recommended):**
```sql
-- Jalankan file: fix_foreign_keys.sql
```

**B. Script tanpa Foreign Key (Untuk Testing):**
```sql
-- Jalankan file: create_return_tables_no_fk.sql
```

#### 3. Perbaiki Controller
Controller sudah diperbaiki untuk:
- Mengecek keberadaan karyawan
- Menggunakan default karyawan jika tidak ada
- Membuat karyawan default jika diperlukan

#### 4. Cek dan Perbaiki Data

**A. Cek data karyawan yang ada:**
```sql
SELECT * FROM tbl_karyawan;
```

**B. Cek user ID yang sedang login:**
```sql
-- Lihat di halaman debug atau cek session
```

**C. Insert karyawan default jika diperlukan:**
```sql
INSERT INTO tbl_karyawan (karyawan_id, karyawan_nama, karyawan_email, karyawan_nohp) 
VALUES (1, 'System User', 'system@example.com', '0000000000');
```

**D. Update karyawan_id yang tidak valid:**
```sql
UPDATE tbl_return_pembelian 
SET karyawan_id = 1 
WHERE karyawan_id NOT IN (SELECT karyawan_id FROM tbl_karyawan);
```

### Langkah-langkah Testing:

1. **Jalankan script SQL** untuk memperbaiki tabel
2. **Akses halaman debug** untuk cek data
3. **Test dengan form simple:**
   ```
   http://localhost/tgm_erp/gudang/addReturnPembelianSimple
   ```
4. **Isi form dan simpan** return pembelian
5. **Cek apakah data masuk** ke database

### File yang Perlu Diperiksa:

1. `fix_foreign_keys.sql` - Script untuk memperbaiki foreign key
2. `create_return_tables_no_fk.sql` - Script tanpa foreign key
3. `application/controllers/Gudang.php` - Method `actionAddReturnPembelian()`
4. Database tables: `tbl_karyawan`, `tbl_return_pembelian`

### Jika Masih Bermasalah:

1. **Gunakan script tanpa foreign key** untuk testing
2. **Cek log database** untuk error detail
3. **Pastikan semua tabel referensi ada** dan berisi data
4. **Test dengan data minimal** terlebih dahulu

### Catatan Penting:

- Foreign key constraint memastikan integritas data
- Untuk production, gunakan foreign key dengan ON DELETE SET NULL atau RESTRICT
- Untuk testing, bisa gunakan tabel tanpa foreign key
- Pastikan data karyawan selalu sinkron dengan user login 