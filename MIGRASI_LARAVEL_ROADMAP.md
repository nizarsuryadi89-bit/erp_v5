# Roadmap & Konteks Migrasi TGM ERP (CI 3 -> Laravel 11)

**PENTING UNTUK AI:** 
Jika *user* meminta Anda untuk melanjutkan migrasi Laravel dengan melampirkan atau merujuk ke dokumen ini, bacalah seluruh dokumen ini untuk memahami konteks sistem asli dan rencana yang telah disepakati sebelumnya.

---

## 1. Konteks Sistem Saat Ini
- **Sistem Lama:** CodeIgniter 3 (TGM ERP v5).
- **Database:** MySQL (`tgmprin2_erp_2026` di *production*, `tgm_erp26` di lokal).
- **Karakteristik Unik:**
  - Menggunakan RBAC (Role-Based Access Control) yang ketat melalui tabel `tbl_portal_settings` dan `tbl_karyawan` / `tbl_operator`.
  - Modul Inti: Autentikasi, Absensi, Point of Sale, Produksi (WIP) dengan *Dynamic BOM* (Bill of Materials).
  - Terdapat logika pencabangan produksi kompleks, di mana SPK (Surat Perintah Kerja) bisa diteruskan ke antrean *Finishing* atau langsung ke PB (Pengambilan Barang).

## 2. Visi Migrasi
- **Tujuan Utama:** 
  1. *Maintainability* jangka panjang.
  2. Implementasi antrean (*queue*), *history logs* sistematis, dan kesiapan API untuk *mobile*.
- **Metode Migrasi:** Paralel (Sistem CI 3 tetap berjalan, Laravel dibangun di folder terpisah dan mengambil alih modul secara bertahap).
- **Aturan Database:** **JANGAN PERNAH** menghapus atau mengubah struktur tabel lama. Laravel harus beradaptasi (mapping) ke tabel-tabel lama menggunakan model *Eloquent*. 

## 3. Rencana Jalan (Roadmap) Eksekusi

### Fase 1: Fondasi & Autentikasi
1. **Setup Awal:** 
   - Install Laravel 11.
   - Hubungkan koneksi database di `.env` ke database TGM ERP lama.
2. **Kustomisasi Autentikasi (PENTING):**
   - Abaikan tabel `users` bawaan Laravel.
   - Buat model *Eloquent* `Karyawan` / `Operator` yang me-*mapping* ke tabel `tbl_karyawan`.
   - Setup Laravel *Guard* kustom agar fitur `Auth::user()` bisa menggunakan data login lama.
   - Buat sistem manajemen izin akses sederhana (*Role & Permission*) meniru logika lama.

### Fase 2: Modul Non-Kritis & Master Data
1. **Master Data:** Buat CRUD untuk Data Karyawan, Produk, Material, Kategori.
2. **Absensi:** Buat ulang UI dan fungsi Absensi serta Rekapnya. Modul ini dipilih sebagai uji coba (Pilot Project) karena minim dependensi.

### Fase 3: Modul Kompleks (Core ERP)
1. **WIP (Work In Progress) & Produksi:**
   - Ubah logika percabangan `status_produksi` (0, 1, 2, 'finishing', 'on_pb') ke dalam State Machine yang lebih rapi menggunakan Laravel.
   - Migrasikan logika pemotongan stok otomatis & *Dynamic BOM*.
2. **Laporan Keuangan & Penjualan:** Migrasi halaman rekap dengan memanfaatkan *Query Builder* & Koleksi Laravel yang lebih efisien.

## 4. Instruksi untuk AI Selanjutnya
- Saat memulai, periksa versi Laravel dan pastikan koneksi database sukses.
- Jangan asumsikan struktur tabel standar Laravel. Selalu periksa struktur tabel asli (misal `DESCRIBE tbl_karyawan`) sebelum membuat model.
- Gunakan arsitektur *Service Pattern* atau *Repository Pattern* untuk modul WIP karena kompleksitas bisnis logiknya tinggi.

*(Dibuat pada: 21 September 2026)*
