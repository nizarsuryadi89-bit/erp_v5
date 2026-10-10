# Tahap 2 — Pemeriksaan QC akhir

Diaktifkan pada aplikasi lokal tanggal 6 Oktober 2026 (Asia/Jakarta). Fokus tahap ini adalah QC sebelum serah terima PB dan pencatatan permintaan reprint yang terhubung ke pemeriksaan. Pembuatan/pengerjaan work order reprint terpisah belum termasuk tahap ini.

## Alur operasional

1. Operator menyelesaikan semua tahap produksi/finishing seperti biasa. Hasil baik, rusak, dan pemakaian bahan tetap dicatat oleh workflow produksi.
2. Pada penyelesaian akhir setelah aktivasi, sistem membuat satu antrean QC dalam transaksi yang sama. Header produksi tetap `done`; status QC disimpan terpisah.
3. Buka tombol **Pemeriksaan QC** pada halaman produksi, atau `/wip/quality`.
4. Admin atau petugas QC yang ditunjuk memeriksa seluruh jumlah target, mengisi jumlah diterima dan ditolak. Keduanya harus berjumlah sama dengan target pemeriksaan.
5. Jika ada penolakan, alasan wajib diisi. Item berstatus **Ditahan**, sehingga tidak dapat diserahterimakan ke PB. Pemeriksa dapat mencatat permintaan reprint untuk jumlah yang ditolak.
6. Pemeriksaan ulang memerlukan catatan tindak lanjut dan membuat riwayat baru; pemeriksaan sebelumnya tetap ada. Pemeriksaan ulang menilai seluruh jumlah target, bukan menambah output produksi.
7. Jika seluruh target diterima, status QC **Lulus**. Item muncul dalam daftar Siap Serah Terima PB. Pengiriman ke PB tetap tindakan tersendiri.

Permintaan reprint dapat ditemukan melalui filter **Permintaan reprint**. Jumlah, alasan, pemeriksa, dan waktu tersedia di Riwayat. Permintaan tidak mereset produksi asal, tidak mengurangi stok, dan belum membuat pekerjaan baru. Riwayat permintaan tetap dapat ditemukan meskipun pemeriksaan berikutnya sudah lulus.

## Kompatibilitas dan data

- Ditambahkan hanya dua tabel InnoDB: `tbl_wip_qc` dan `tbl_wip_qc_inspection`.
- Instalasi lokal membuat kedua tabel kosong. Tidak ada UPDATE, ALTER, DELETE, backfill, atau normalisasi terhadap tabel ERP yang sudah ada.
- Item lama yang sudah selesai tanpa catatan QC tetap dapat diserahterimakan melalui alur PB sebelumnya. Item lama yang masih berjalan dan baru selesai setelah aktivasi akan masuk QC.
- Pemeriksaan menambahkan riwayat QC dan log produksi; tidak menimpa kuantitas/status/tanggal pada detail produksi atau mengubah stok.
- Menonaktifkan pendaftaran QC otomatis tidak menghapus kewajiban QC untuk item yang sudah terdaftar.
- Penyerahan ke konsumen sekarang mensyaratkan item sudah berada di PB, memeriksa cabang dan SPK (untuk penyerahan per SPK), mengunci header, dan memeriksa QC. Jalur bulk tidak lagi mengambil item `done` langsung ke konsumen.

## Hak akses dan konsistensi

Default pemeriksa adalah admin level 1/17. User tertentu dapat ditunjuk melalui `reviewer_user_ids` pada `application/config/wip_quality.php`. Petugas nonadmin tetap dibatasi cabang produksi. PIN operator tidak memberikan hak pemeriksa QC.

Pengiriman hasil QC menggunakan POST dan token formulir yang terikat sesi. Versi QC mencegah penyimpanan dari form lama. Identitas pengiriman membuat retry identik mengembalikan hasil yang sudah tersimpan tanpa duplikasi; identitas yang sama dengan isi berbeda ditolak. Riwayat inspeksi hanya ditambah. Keputusan yang sudah lulus tidak dapat ditimpa melalui form ini.

Enrolmen QC berada dalam transaksi penyelesaian produksi; gagal membuat QC membatalkan penyelesaian. Inspeksi dan audit disimpan dalam satu transaksi. Urutan penguncian header lalu QC konsisten dengan pengiriman PB. Server menolak handover ketika QC masih pending/hold, termasuk lewat jalur batch dan penyerahan ke konsumen.

## Instalasi lingkungan lain

Pasang seluruh kode terkait dengan `enabled = false` terlebih dahulu. Jalankan perintah CLI berikut, lalu aktifkan `enabled` setelah struktur berhasil diverifikasi:

```sh
/Applications/XAMPP/xamppfiles/bin/php application/migrations/install_wip_quality.php --install
```

Installer hanya membuat tabel QC jika belum ada dan memeriksa kolom/engine. Tidak memperbaiki atau mengubah tabel yang sudah ada. SQL tersedia di `application/migrations/20261006_wip_quality.sql`.

## Validasi

- 96 pemeriksaan regresi workflow produksi, termasuk enrolmen QC, rollback saat enrolmen gagal, handover PB, dan penyerahan konsumen.
- 36 pemeriksaan DOM produksi.
- 36 pemeriksaan QC pada database MySQL terpisah yang dibuat khusus dengan data fiktif lalu dihapus: akses, jumlah, hold/pass, retry, riwayat, rollback audit, engine, dan perebutan lock melalui dua koneksi.
- 20 pemeriksaan DOM QC dari render view PHP dengan data fiktif dan HTTP stub.
- Sintaks PHP/JavaScript yang berubah diperiksa.

```sh
/Applications/XAMPP/xamppfiles/bin/php application/tests/wip_workflow_test.php
node application/tests/wip_produksi_ui_test.cjs
/Applications/XAMPP/xamppfiles/bin/php application/tests/wip_quality_mysql_test.php --isolated-db
node application/tests/wip_quality_ui_test.cjs
```

Tidak dilakukan transaksi produksi/QC pada data ERP operasional. Browser aplikasi belum diuji langsung karena tidak ada browser terhubung pada sesi pengerjaan. Pengujian database menggunakan schema uji terpisah; tabel QC operasional tetap kosong saat instalasi selesai.
