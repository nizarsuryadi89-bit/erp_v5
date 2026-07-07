# 🎯 QUICK START - BACA PERTAMA!

Sistem Antrian untuk TGM ERP sudah siap! Ikuti langkah-langkah di bawah:

---

## 📦 STEP 1: Import Database (5 menit)

Jalankan command ini di terminal/CMD:

```bash
mysql -u root -p tgm_erp < /Applications/XAMPP/xamppfiles/htdocs/tgm_erp/database/antrian_tables.sql
```

Atau:
1. Buka phpMyAdmin
2. Pilih database `tgm_erp`
3. Tab "Import"
4. Select file `antrian_tables.sql`
5. Klik "Go"

---

## 🌐 STEP 2: Test Akses (5 menit)

Buka di browser:

| Akses | URL | Siapa |
|-------|-----|-------|
| Ambil Nomor | http://localhost/tgm_erp/antrian | Customer |
| Operator | http://localhost/tgm_erp/antrian/operator/1 | Deskprint (login dulu) |
| Display | http://localhost/tgm_erp/antrian/display/1 | Monitor/TV (public) |
| Dashboard | http://localhost/tgm_erp/antrian | Admin |
| Laporan | http://localhost/tgm_erp/antrian/laporan | Admin (login dulu) |

---

## 🎨 STEP 3: Test Flow (10 menit)

### Scenario: Customer ambil nomor → Display update → Operator panggil

**Tab 1: Buka Customer Interface**
```
URL: http://localhost/tgm_erp/antrian
Action: Tap "Counter 1"
Result: Modal success + nomor antrian (misal: C1-001)
```

**Tab 2: Buka Display Panel**
```
URL: http://localhost/tgm_erp/antrian/display/1
Result: Nomor C1-001 muncul otomatis di display
```

**Tab 3: Login Sebagai Deskprint**
```
URL: http://localhost/tgm_erp/antrian/operator/1
Action: Klik "Panggil" untuk C1-001
Result: Status berubah "Dipanggil" (Tab 2 update otomatis)
Action: Klik "Mulai Layani"
Result: Status berubah "Sedang Dilayani"
Action: Klik "Selesai"
Result: Status berubah "Selesai" (antrian selesai)
```

**Tab 1: Lihat Dashboard**
```
URL: http://localhost/tgm_erp/antrian
Result: Statistik terupdate (1 antrian selesai)
```

---

## 📖 STEP 4: Baca Dokumentasi (20 menit)

| File | Apa |
|------|-----|
| **START_HERE_ANTRIAN.md** | Overview & deployment summary |
| **SISTEM_ANTRIAN_README.md** | Panduan lengkap semua fitur |
| **SETUP_CHECKLIST_ANTRIAN.md** | Setup step-by-step dengan testing |
| **ANTRIAN_INTEGRATION_GUIDE.md** | Integrasi dengan menu TGM ERP |

👉 **Mulai dari file pertama untuk pemahaman lengkap!**

---

## ⚙️ STEP 5: Setup Menu (Opsional, 10 menit)

Untuk menampilkan di menu sidebar TGM ERP:

```sql
-- Run query ini di phpMyAdmin
INSERT INTO menu (nama, link, icon, urutan, status) 
VALUES ('Sistem Antrian', 'antrian', 'fas fa-traffic-light', 10, 1);

-- Untuk Deskprint (role ID 2) bisa akses operator
INSERT INTO tbl_menu_access (role_id, menu_id) 
SELECT 2, id FROM menu WHERE link = 'antrian';
```

---

## 🎓 STEP 6: User Training (Sesuaikan)

### Untuk Customer:
```
1. Tap salah satu tombol counter di layar antrian
2. Ambil karcis yang keluar dari printer
3. Tunggu nomor dipanggil (lihat di display atau dengarkan)
```

### Untuk Operator/Deskprint:
```
1. Login dengan username & password Deskprint
2. Buka Panel Operator (http://localhost/tgm_erp/antrian/operator/1)
3. Pilih counter yang akan dilayani
4. Klik "Panggil" untuk antrian pertama
5. Setelah customer datang, klik "Mulai Layani"
6. Setelah selesai, klik "Selesai"
7. Antrian berikutnya siap dipanggil
```

### Untuk Admin:
```
1. Lihat statistik di dashboard
2. Buka laporan untuk detail antrian
3. Filter berdasarkan tanggal & counter sesuai kebutuhan
```

---

## ✅ CHECKLIST SEBELUM GO-LIVE

- [ ] Database sudah import (table ada)
- [ ] Semua URLs bisa diakses
- [ ] Test flow berhasil
- [ ] Printer sudah setup (jika pakai)
- [ ] User sudah training
- [ ] Menu sudah di-setup (jika mau)
- [ ] Backup database sudah dibuat

---

## 🆘 Ada Masalah?

### Display tidak update?
→ Refresh F5 atau clear browser cache

### Operator tidak bisa akses?
→ Login sebagai Deskprint (Level 2) dulu

### Nomor tidak tergenerate?
→ Check phpMyAdmin apakah tabel `tbl_antrian_counter` ada data

### Print tidak jalan?
→ Set default printer, gunakan Chrome/Firefox

### Bantuan lebih lengkap?
→ Lihat file `SISTEM_ANTRIAN_README.md` (section Troubleshooting)

---

## 📚 DOKUMENTASI TERSEDIA

Semua file dokumentasi di folder TGM ERP:

```
SISTEM_ANTRIAN_README.md          ← Baca ini untuk detail lengkap
SETUP_CHECKLIST_ANTRIAN.md        ← Baca ini untuk setup step-by-step
START_HERE_ANTRIAN.md             ← Baca ini untuk overview
ANTRIAN_INTEGRATION_GUIDE.md      ← Untuk integrasi dengan menu
FILE_MANIFEST_ANTRIAN.md          ← Daftar lengkap semua file
```

---

## 🎉 SELESAI!

Sistem Antrian Anda sudah siap pakai!

**Langkah berikutnya:**
1. ✅ Import database
2. ✅ Test URLs
3. ✅ Baca documentation
4. ✅ Setup menu (opsional)
5. ✅ User training
6. ✅ **GO LIVE! 🚀**

---

**Enjoy using Sistem Antrian! 🎊**

Untuk pertanyaan lebih detail, silakan baca dokumentasi yang sudah disediakan.
