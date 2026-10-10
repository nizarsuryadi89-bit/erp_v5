# Rencana Implementasi & Pengembangan TGM ERP Mobile

Ini adalah panduan teknis agar aplikasi Flutter Anda berjalan lancar, aman, dan mudah dikembangkan untuk 5 fitur utama.

## 1. Absensi Geotagging + Foto Selfie 📍📸

**Tantangan**: GPS Mocking (Lokasi Palsu), Sinyal Buruk, Ukuran Foto Besar.

**Solusi & Saran**:

1. **Validasi Jarak (Haversine Formula)**:
    * Backend harus menyimpan koordinat Lat/Long kantor pusat/cabang.
    * Saat user kirim absen, hitung jarak antara User GPS vs Kantor.
    * Jika jarak > 50 meter (toleransi), tolak absensi.
2. **Kompresi Gambar**:
    * Jangan kirim foto resolusi asli kamera (bisa 5MB+). Gunakan package `flutter_image_compress` di Flutter.
    * Resize ke max 800x600 px kualitas 80% (sekitar 100KB). Ini mempercepat upload.
3. **Anti-Mock Location**:
    * Di Flutter, gunakan `Geolocator.isLocationServiceEnabled` dan cek properti `isMock` (di Android).
    * Tolak jika terdeteksi Fake GPS.
4. **Offline Support (Opsional)**:
    * Jika internet mati, simpan data absen di SQFLite lokal dulu.
    * Service background akan upload otomatis saat internet kembali.

## 2. Profil Karyawan 👤

**Tantangan**: Data tidak realtime, Sinkronisasi.

**Solusi**:

1. **Cache First Strategy**:
    * Simpan data profil di `SharedPreferences` saat login.
    * Tampilkan data dari cache dulu (biar instan), baru request ke API untuk update (background).
2. **Edit Data Terbatas**:
    * Izinkan user edit: No HP, Email, Alamat, Foto.
    * JANGAN izinkan edit: Jabatan, Gaji, Divisi (ini wewenang HRD).

## 3. Daftar Gaji (Payroll) 💰

**Tantangan**: Privasi & Keamanan Data.

**Solusi**:

1. **Enkripsi PDF**:
    * Saat generate Slip Gaji di PHP, password-protect PDF-nya (misal pakai Tanggal Lahir).
    * Atau, jangan kirim PDF mentah. Kirim JSON data rincian gaji, biar Flutter yang render tampilannya.
2. **Endpoint Khusus**:
    * Buat `Api_mobile.php/list_gaji` (GET List Bulan) dan `Api_mobile.php/detail_gaji` (GET Rincian).
    * Flutter menampilkan List Bulan -> Klik -> Masuk UI Slip Gaji.

## 4. Pengajuan Cuti, Izin, Sakit 📝

**Tantangan**: Validasi Kuota Cuti & Bukti Surat Dokter.

**Solusi**:

1. **Sistem Approval**:
    * User mengajukan -> Status "PENDING".
    * Notif masuk ke Dashboard Web Admin/HRD.
    * Admin Approve -> Status "APPROVED" -> Potong kuota cuti.
2. **Upload Multi-File**:
    * Untuk Sakit, wajib upload foto surat dokter.
    * Gunakan `MultipartRequest` di Flutter.

## 5. Pengajuan Lembur ⏰

**Tantangan**: Lembur fiktif.

**Solusi**:

1. **Check-in & Check-out Lembur**:
    * Mirip absen, lembur harus ada jam mulai dan jam selesai.
    * Bisa ditambahkan validasi GPS (harus di kantor saat mulai/selesai lembur).
2. **Validasi SPKL**:
    * Lembur hanya valid jika ada Surat Perintah Kerja Lembur (SPKL) dari atasan.
    * User pilih referensi SPKL saat mau mulai lembur.

---

## Roadmap Pengembangan (Next Steps) 🚀

Kami telah menyiapkan fondasi Backend (`Api_mobile.php`) yang sudah **Smart-Detect** kolom database, jadi Anda tidak perlu pusing soal error SQL lagi.

Langkah selanjutnya yang kami sarankan:

1. **Buat Tabel `tbl_pengajuan`** di database untuk menampung Cuti/Izin/Lembur.
2. **Buat Endpoint API Baru** untuk fitur-fitur di atas.
3. **Bikin UI Form** di Flutter.

Apakah Anda ingin kita mulai mengerjakan nomor 4 (Pengajuan Cuti/Izin) sekarang?
