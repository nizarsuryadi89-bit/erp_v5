# SISTEM ANTRIAN - SETUP CHECKLIST

## ✅ Pre-Installation

- [ ] Database TGM ERP sudah di-setup
- [ ] CodeIgniter sudah running
- [ ] Folder `application/views/antrian/` ada
- [ ] User dengan role Deskprint (Level 2) sudah ada

---

## 📥 Step 1: Import Database

```bash
# Pilih salah satu cara:

# Cara 1: Via Terminal MySQL
mysql -u root -p tgm_erp < /Applications/XAMPP/xamppfiles/htdocs/tgm_erp/database/antrian_tables.sql

# Cara 2: Via phpMyAdmin
# - Buka http://localhost/phpmyadmin
# - Select database 'tgm_erp'
# - Tab 'Import'
# - Select file: antrian_tables.sql
# - Klik 'Go'
```

**Verifikasi:**
```sql
SHOW TABLES LIKE 'tbl_antrian%';
-- Harus muncul: tbl_antrian, tbl_antrian_counter, tbl_antrian_history, tbl_antrian_stats
```

- [ ] Database tables created successfully
- [ ] Default counters inserted (3 counter)

---

## 📂 Step 2: Copy Files

Semua file sudah dibuat otomatis di lokasi berikut:

```
✓ application/core/MY_Controller.php (sudah ada)
✓ application/models/Antrian_model.php (✅ BARU)
✓ application/controllers/Antrian.php (✅ BARU)
✓ application/views/antrian/ambil_nomor.php (✅ BARU)
✓ application/views/antrian/operator.php (✅ BARU)
✓ application/views/antrian/display.php (✅ BARU)
✓ application/views/antrian/dashboard.php (✅ BARU)
✓ application/views/antrian/laporan.php (✅ BARU)
✓ application/views/antrian/karcis.php (✅ BARU)
✓ database/antrian_tables.sql (✅ BARU)
✓ SISTEM_ANTRIAN_README.md (✅ BARU)
```

- [ ] Semua file sudah di-copy ke lokasi yang benar
- [ ] File permissions OK (readable oleh web server)

---

## 🧪 Step 3: Testing

### Test 1: Database Connection
```
GET http://localhost/tgm_erp/antrian
Harusnya: Akses interface ambil nomor (tidak error)
```
- [ ] Database connection OK

### Test 2: Ambil Nomor (Public)
```
GET http://localhost/tgm_erp/antrian
1. Klik salah satu counter
2. Muncul modal success dengan nomor antrian
3. Klik 'Cetak Karcis' (print dialog)
```
- [ ] Ambil nomor berhasil
- [ ] Nomor tergenerate dengan format C1-001
- [ ] Print dialog muncul

### Test 3: Operator Panel (Protected)
```
GET http://localhost/tgm_erp/antrian/operator/1
1. Login sebagai Deskprint (Level 2) atau Admin
2. Pilih counter
3. Klik "Panggil" pada antrian
4. Klik "Mulai Layani"
5. Klik "Selesai"
```
- [ ] Login required untuk akses
- [ ] Panel operator bisa di-akses
- [ ] Status antrian berubah saat di-panggil
- [ ] Antrian bisa dibatalkan

### Test 4: Display Panel (Public)
```
GET http://localhost/tgm_erp/antrian/display/1
1. Buka di 2 tab berbeda
2. Di tab 1: ambil nomor antrian
3. Di tab 2 (display): lihat nomor muncul otomatis
4. Buka panel operator, panggil antrian
5. Display harus update otomatis
```
- [ ] Display panel muncul dengan baik
- [ ] Real-time update setiap 2 detik
- [ ] Nomor antrian ditampilkan dengan besar
- [ ] List menunggu terupdate

### Test 5: Admin Dashboard
```
GET http://localhost/tgm_erp/antrian
Login sebagai Admin
1. Lihat statistik kartu
2. Klik "Laporan Antrian"
3. Filter berdasarkan tanggal & counter
4. Cetak laporan
```
- [ ] Dashboard statistik muncul
- [ ] Laporan bisa diakses
- [ ] Filter bekerja dengan benar
- [ ] Print laporan berfungsi

---

## 🔧 Step 4: Configuration

### Konfigurasi Timezone (Penting!)
File: `application/core/MY_Controller.php` (baris 18)
```php
date_default_timezone_set("Asia/Jakarta"); // Sesuaikan dengan timezone Anda
```
- [ ] Timezone sudah sesuai

### Konfigurasi Access Control
File: `application/controllers/Antrian.php` (method `operator()`)
- [ ] Operator panel hanya untuk Deskprint (Level 2)

### Konfigurasi Counter
File: Database `tbl_antrian_counter`
```sql
INSERT INTO tbl_antrian_counter (counter_nama, counter_nomorurut, status)
VALUES ('Counter 4', 4, 'aktif');
```
- [ ] Counter sudah sesuai dengan kebutuhan

---

## 🌐 Step 5: URL Routes

Pastikan routes berikut berfungsi:

| URL | Method | Access | Purpose |
|-----|--------|--------|---------|
| `/tgm_erp/antrian` | GET | Public | Ambil nomor / Dashboard |
| `/tgm_erp/antrian/operator/{id}` | GET | Protected (Level 2+) | Panel Operator |
| `/tgm_erp/antrian/display/{id}` | GET | Public | Display Panel |
| `/tgm_erp/antrian/laporan` | GET | Protected | Laporan |

- [ ] Semua routes accessible
- [ ] Access control bekerja

---

## 🖨️ Step 6: Printer Setup (Opsional)

### Jika menggunakan Thermal Printer (80mm):

1. **Install Printer Driver**
   - Download dari website printer manufacturer
   - Install dengan benar

2. **Test Print:**
   ```
   GET http://localhost/tgm_erp/antrian/print_karcis/1
   Klik print (Ctrl+P)
   Select printer: Thermal Printer
   Klik print
   ```

3. **Adjust Karcis Size (jika perlu):**
   File: `application/views/antrian/karcis.php`
   ```css
   @page {
       size: 80mm 150mm;  /* Ubah sesuai ukuran printer */
   }
   ```

- [ ] Printer terdeteksi
- [ ] Test print berhasil
- [ ] Format sesuai dengan printer

---

## 📊 Step 7: Data Backup

Sebelum production, setup backup:

```bash
# Backup database setiap hari
mysqldump -u root -p tgm_erp > backup_$(date +%Y%m%d).sql

# Atau setup automated backup (cron job)
```

- [ ] Backup strategy sudah ditentukan
- [ ] Test restore dari backup

---

## 🚀 Step 8: Production Deployment

- [ ] Ganti localhost menjadi domain/IP production
- [ ] Setup SSL/HTTPS untuk keamanan
- [ ] Configure proper logging
- [ ] Setup monitoring & alerts
- [ ] User training untuk operator

---

## 📋 Final Checklist

- [ ] Database OK
- [ ] Semua files OK
- [ ] Testing OK
- [ ] Configuration OK
- [ ] Printer OK (jika ada)
- [ ] Backup OK
- [ ] Production ready

---

## 🎯 Ready to Deploy!

Jika semua checklist sudah dicentang, sistem antrian siap digunakan.

### Quick Start URLs:
```
1. Ambil Nomor: http://localhost/tgm_erp/antrian
2. Panel Operator: http://localhost/tgm_erp/antrian/operator/1
3. Display Panel: http://localhost/tgm_erp/antrian/display/1
4. Dashboard Admin: http://localhost/tgm_erp/antrian
5. Laporan: http://localhost/tgm_erp/antrian/laporan
```

---

## 📞 Troubleshooting Quick Reference

| Problem | Solution |
|---------|----------|
| "Akses Ditolak" pada operator | Login sebagai Deskprint (L2) atau Admin (L1) |
| Display tidak update | Refresh F5 atau clear cache |
| Nomor tidak tergenerate | Check tbl_antrian_counter ada data |
| Print tidak berfungsi | Set default printer, gunakan Chrome |
| Statistik salah | Check timezone di MY_Controller |

---

**Selamat! Sistem antrian sudah siap digunakan! 🎉**
