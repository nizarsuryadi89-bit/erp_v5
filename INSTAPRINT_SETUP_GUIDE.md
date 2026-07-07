# InstaPrint TGM Core - Setup Guide

## 🚀 Cara Setup Kiosk di Setiap Cabang

### **Langkah Setup:**

#### 1. **Identifikasi Branch ID**

Cek `tbl_perusahaan` untuk mengetahui `id_perusahaan` setiap cabang:

```sql
SELECT id_perusahaan, nama_perusahaan FROM tbl_perusahaan;
```

Contoh hasil:

- ID: 1 → TGM Kopo
- ID: 2 → TGM Soekarno Hatta

#### 2. **Buat Desktop Shortcut**

**Untuk Cabang 1 (Kopo):**

- URL: `http://localhost/tgm_erp/kiosk/index?perusahaan_id=1`
- Nama Shortcut: "InstaPrint - Kopo"

**Untuk Cabang 2 (Soekarno Hatta):**

- URL: `http://localhost/tgm_erp/kiosk/index?perusahaan_id=2`
- Nama Shortcut: "InstaPrint - Soekarno Hatta"

#### 3. **Cara Membuat Shortcut di Windows:**

1. Klik kanan di Desktop → New → Shortcut
2. Masukkan URL sesuai cabang
3. Beri nama yang jelas (contoh: "InstaPrint - Kopo")
4. Klik Finish
5. (Optional) Klik kanan shortcut → Properties → Change Icon → Pilih icon printer

#### 4. **Cara Membuat Shortcut di Mac:**

1. Buka Safari/Chrome
2. Buka URL Kiosk dengan parameter branch
3. Drag URL bar ke Desktop
4. Rename shortcut sesuai cabang

#### 5. **Fullscreen Mode (Kiosk Mode)**

**Chrome:**

```
chrome.exe --kiosk --app=http://localhost/tgm_erp/kiosk/index?perusahaan_id=1
```

**Firefox:**

```
firefox.exe -kiosk http://localhost/tgm_erp/kiosk/index?perusahaan_id=1
```

---

## ✅ **Verifikasi Setup**

Setelah setup, pastikan:

1. ✅ Badge hijau menampilkan nama cabang yang benar
2. ✅ Order masuk ke antrian cabang yang sesuai  
3. ✅ Page title menunjukkan "InstaPrint TGM Core - [Nama Cabang]"

---

## 🎯 **Best Practices**

1. **Pin shortcut** ke taskbar untuk akses cepat
2. **Set browser to auto-open** saat komputer startup
3. **Disable browser close button** (kiosk mode) untuk prevent accidental exit
4. **Auto-refresh** sudah built-in jika ada error

---

## 🔧 **Troubleshooting**

**Problem: Badge menampilkan "TGM Print" (default)**

- **Cause:** Branch ID tidak valid atau tidak ditemukan di database
- **Solution:** Cek parameter `?perusahaan_id=X` dan pastikan ID ada di `tbl_perusahaan`

**Problem: Order masuk ke cabang yang salah**

- **Cause:** Salah URL atau shortcut
- **Solution:** Verifikasi URL memiliki parameter `?perusahaan_id=X` yang benar

---

## 📱 **Mobile/Tablet Access**

Jika ingin akses dari tablet atau device lain di jaringan lokal:

```
http://192.168.1.xxx/tgm_erp/kiosk/index?perusahaan_id=1
```

Ganti `192.168.1.xxx` dengan IP address server.

---

## 🎨 **Kustomisasi Lebih Lanjut**

Jika ingin tampilan berbeda per cabang (warna, logo, dll):

1. Buat view terpisah per cabang, atau
2. Tambahkan conditional CSS based on `$branch_id`

---

**Author:** InstaPrint Development Team  
**Last Updated:** 2026-02-07
