# TGM Complaint & Trouble Center — XAMPP/offline

Modul ERP v5 untuk impor ekspor WhatsApp, klasifikasi awal komplain/trouble,
tiket manual, penugasan PIC, perkembangan penanganan, bukti asli, dan rangkuman.
Cabang mengikuti sesi ERP yang aktif. Default bekerja tanpa internet/API key.

## Pasang di MacBook

Lokasi proyek: `/Applications/XAMPP/xamppfiles/htdocs/tgm_erp`.
Gunakan PHP 7.4+ dengan mysqli dan mbstring; database MySQL/MariaDB mendukung
InnoDB dan utf8mb4. Jalankan Apache dan MySQL dari XAMPP.

1. Ekspor backup database lokal melalui phpMyAdmin dan simpan salinan folder
   proyek sebelum pemasangan. Jangan sertakan database asli atau kredensial
   saat berbagi ZIP. Modul tidak memerlukan data pelanggan untuk pengujian.
2. Ambil branch fitur dalam checkout repo `nizarsuryadi89-bit/erp_v5`:

   ```bash
   cd /Applications/XAMPP/xamppfiles/htdocs/tgm_erp
   git status --short
   git fetch origin codex/tgm-complaint-center-offline
   git switch --track origin/codex/tgm-complaint-center-offline
   ```

   Bila perubahan lokal belum disimpan, simpan/commit dahulu sebelum berpindah
   branch. Jangan gunakan reset/checkout paksa. Periksa `git remote -v` bila
   `origin` bukan repo yang disebutkan. Bila sudah berada di branch fitur,
   gunakan `git pull --ff-only`. Jika folder bukan checkout Git, unduh ZIP
   branch fitur dan salin hanya file yang tercantum di PR ke folder proyek;
   cocokkan perubahan `MY_Controller.php` apabila versi lokal lebih baru.

3. Installer memakai konfigurasi database ERP lokal yang sudah ada:

   ```bash
   /Applications/XAMPP/xamppfiles/bin/php tools/complaint.php install
   ```

   Installer membuat **empat tabel baru** dan mendaftarkan menu/hak akses
   pengelola. Tidak menghapus atau mengubah transaksi/tabel ERP lama. Perintah
   bisa diulang. DDL MySQL tidak atomik: bila gagal sebagian, perbaiki penyebab
   lalu ulangi. Jika akses CLI database berbeda, impor
   `database/complaint_center.sql` di database ERP lewat phpMyAdmin, kemudian
   jalankan `php tools/complaint.php menu` menggunakan PHP XAMPP.

4. Logout/login ERP untuk memperbarui cache hak akses. Buka
   `http://localhost/tgm_erp/index.php/complaint`. Pengelola default mengikuti
   level sesi 1, 8, 17, 22 dan super admin/developer ERP. Daftarkan hak akses
   menu Complaint Center untuk PIC lewat pengaturan hak akses ERP; PIC tetap
   hanya dapat memperbarui tiket yang ditugaskan kepadanya. Akses baca tidak
   mengizinkan perubahan. Akses dan data dibatasi cabang aktif.

## Cara memakai

- Di WhatsApp, ekspor percakapan **tanpa media**. Pilih .txt pada **Impor Chat**,
  isi nama/ID percakapan yang konsisten, dan pilih urutan tanggal DMY atau MDY.
  Pratinjau wajib sebelum impor. Batas 2 MB, 500 pesan, 16.000 byte per pesan.
  Format Android/iOS dengan waktu 24 jam atau AM/PM didukung; tanggal ISO yang
  bukan format ekspor ditolak, agar pesan tidak tergabung secara keliru.
- Impor menyimpan pesan asli dan memproses hingga 50 pesan dengan aturan
  lokal. Gunakan **Proses antrean offline** untuk sisanya. Klasifikasi heuristik
  bisa keliru, terutama pesan dengan beberapa masalah atau campuran masalah
  selesai dan masalah baru. Semua tiket otomatis ditandai **perlu tinjauan**.
  Pesan lain tetap ada di kotak masuk dan bisa dibuatkan tiket secara manual.
- Deduplikasi impor memakai cabang, nama percakapan, waktu, pengirim, dan teks.
  Impor ulang identik dilewati. Dua pesan yang betul-betul identik pada waktu
  yang sama juga dianggap satu. Pesan hanya diringkas dari teks, bukan media.
- Pengelola menetapkan PIC dan **ASSIGNED**. PIC memperbarui **ON_PROGRESS**
  lalu **RESOLVED** dengan solusi; pengelola memverifikasi dan **CLOSED**.
  Catatan perkembangan dan perubahan status masuk riwayat. Pembukaan kembali
  oleh pengelola mengembalikan status **OPEN**, dengan PIC dikosongkan.
- Pilih mesin dan SPK nyata dari cabang aktif. Nomor SPK yang tidak dikenal
  tidak otomatis dibuat. Rangkuman menghitung tiket yang dilaporkan pada
  periode terpilih dengan status saat ini. SPK terkait tidak selalu terlambat.
- Follow-up dapat ditautkan lewat **ID tiket** yang tertera di halaman detail.
  Penautan bukti tidak otomatis menutup tiket. Unduh rangkuman .txt atau cetak
  ke PDF; pengiriman WhatsApp dilakukan manual.
- File `application/tests/complaint/fixtures/whatsapp_android.txt` berisi contoh fiktif.
  SPK DEMO-001/002 hanya contoh dan tidak dibuat di ERP oleh installer.

## Pengujian

```bash
/Applications/XAMPP/xamppfiles/bin/php application/tests/complaint/run.php
```

Tes mandiri tanpa database/API: parser ekspor dan roundtrip pratinjau, batas
impor, klasifikasi/negasi, transisi/PIC/solusi, CSRF/POST/hak tulis, validasi HMAC, isolasi cabang
webhook, dan render/escape HTML. Tes integrasi memakai database uji acak yang
dibuat lalu dihapus sendiri, **tidak membaca konfigurasi/database ERP**:

```bash
COMPLAINT_TEST_DB_ALLOW_CREATE=1 /Applications/XAMPP/xamppfiles/bin/php application/tests/complaint/database.php
```

Variabel opsional: `COMPLAINT_TEST_DB_HOST`, `COMPLAINT_TEST_DB_PORT`,
`COMPLAINT_TEST_DB_USER`, `COMPLAINT_TEST_DB_PASSWORD`. Akun tes perlu izin
CREATE/DROP DATABASE. Jangan tulis password asli ke repo. Cakupan: migrasi
ulang, PIC/mesin/SPK lintas cabang, rollback, optimistic locking, lifecycle,
deduplikasi, lease worker, follow-up, dan agregasi laporan. Tes ini memakai
query builder CodeIgniter dari `system/` proyek. Lakukan uji pemasangan dan
alur UI di XAMPP sebelum dipakai operasional; database lokal belum diperiksa
dari sesi Codex ini.

## Integrasi online opsional (belum aktif)

Default tidak membaca WhatsApp secara langsung dan tidak memakai AI.
WhatsApp/gateway memerlukan adapter sesuai penyedia yang benar-benar dipakai;
endpoint di bawah adalah **kontrak generik**, bukan API Meta yang langsung
bisa dihubungkan. XAMPP yang sepenuhnya offline tidak menerima webhook internet.

Endpoint POST `index.php/complaint_webhook`, body JSON UTF-8:

```json
{"message_id":"provider-message-001","chat_id":"operasional-tgm","sender":"Operator","sent_at":"2026-10-06T14:30:00+07:00","text":"Mesin A3 macet, SPK 261006001 tertunda."}
```

Konfigurasi `complaint_webhook_sources` di `application/config/complaint.php`:
source → ID cabang, secret acak minimal 32 byte dari environment, dan daftar
chat yang diizinkan. Cabang dalam payload tidak dipakai. Header:

- `X-TGM-Source`: nama source yang dikonfigurasi.
- `X-TGM-Timestamp`: Unix timestamp saat pengiriman, toleransi 300 detik.
- `X-TGM-Signature`: hex huruf kecil HMAC-SHA256 atas
  `source + "\n" + timestamp + "\n" + raw_body`, memakai secret source.

Pesan masuk antrean; ulang pengiriman memakai message_id provider yang sama
bersifat idempoten. Proses antrean menggunakan `php tools/complaint.php run 50 1`
(50 pesan, cabang 1). Worker menyimpan hasil dengan lease token dan retry
maksimal 3; lease kadaluwarsa 5 menit. Pesan gagal bisa diantrekan ulang melalui UI.
Gunakan HTTPS saat integrasi publik. Jika CSRF global ERP kelak diaktifkan,
konfigurasi pengecualian hanya endpoint webhook ini; autentikasi HMAC tetap wajib.

AI opsional hanya pada worker CLI, membutuhkan internet, curl, dan environment
`TGM_COMPLAINT_AI_ENABLED=1`, `OPENAI_API_KEY`, `TGM_COMPLAINT_AI_MODEL` dengan
model yang mendukung Structured Outputs. Integrasi memakai Chat Completions
JSON Schema; gagal/konfigurasi kosong kembali ke aturan. Mengaktifkan AI
mengirim teks pesan ke OpenAI. Hasil tetap perlu tinjauan; AI tidak mengubah
PIC/mesin/status selesai atau menjalankan perintah. Belum diuji dengan API nyata.

## Perubahan inti dan hosting berikutnya

Satu perubahan pada `MY_Controller.php`: controller `complaint` dikecualikan
dari auto-audit POST agar isi impor chat tidak disalin ke log global. Modul
mencatat ID/counter aksi serta riwayat tiket sendiri. Tidak ada bypass RBAC.
Modul baru tidak mengubah konfigurasi database, auth, gateway, atau workflow FTP.

Branch fitur ini tidak memicu workflow deploy `production`. Saat nanti deploy
hosting, pasang migrasi dan menu secara eksplisit: workflow lama mengecualikan
folder database/documentation/application/tests; installer tools tersedia lewat CLI. Pastikan PHP/mysqli/mbstring tersedia
dan Document Root/target FTP sudah benar sebelum memindahkan branch production.
Rollback kode dengan kembali ke branch sebelumnya; tabel baru dapat dibiarkan
untuk menjaga bukti, tanpa menghapus transaksi ERP. Jangan hapus tabel baru
sebelum ekspor bukti/tiket apabila sudah digunakan.
