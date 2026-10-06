# Refaktor WIP tahap 1

Perubahan kode pada 5 Oktober 2026. Tidak ada migrasi, perubahan schema, pembaruan massal status, perhitungan ulang stok historis, atau transaksi ERP yang dijalankan selama pengerjaan.

## Perilaku baru

- Semua aksi utama mulai, catat hasil, batal mulai, dan serah terima PB di `Wip` menggunakan `Wip_workflow_model` sebagai batas transaksi.
- Server memeriksa cabang, divisi operator pada routing, status item, pembayaran sesuai kebijakan lama, urutan tahap, dan identitas tahap yang dikirim form.
- Form baru mengirim snapshot ID/versi tahap. Hasil parsial, retry, permintaan lama setelah batal/mulai ulang, dan hasil rijek tidak boleh menggandakan konsumsi.
- Hasil produk baik/rusak terpisah dari pemakaian bahan. Jumlah hasil adalah tambahan pada tahap aktif; jumlah bahan memakai satuan dasar material. Tahap tetap aktif ketika hasil baik belum memenuhi qty order.
- Tahap dengan kebutuhan bahan pada BOM tidak dapat ditutup dengan konsumsi nol. Mesin yang dipilih harus aktif dan sesuai lokasi; tahap cetak barang dengan BOM memerlukan mesin, dengan pengecualian merchandise mengikuti perilaku UI sebelumnya.
- Hasil, header, konsumsi, dan log ditulis bersama. Kegagalan stok/log membatalkan seluruh batch. Item/material dikunci dalam urutan stabil; saldo stok memakai pembaruan delta bersyarat.
- Konsumsi tidak boleh membuat stok negatif. Saldo historis negatif atau duplikat tidak diperbaiki otomatis; request ditolak untuk ditinjau. Jika rak tidak dipilih, konsumsi dialokasikan ke saldo rak yang ada menurut urutan `stok_id`, dengan ledger terpisah per rak.
- Nilai konversi kosong/nol tetap dibaca sebagai faktor 1, mengikuti perilaku legacy. Master material tidak ditulis ulang.
- Konsumsi utama batch sekarang direferensikan per item dan tahap, termasuk hasil gagal. Alokasi sementara berdasarkan qty order dan dijelaskan pada log; alokasi berdasarkan area/panjang atau pengukuran per item dapat diperluas pada tahap berikutnya. Bahan sekunder berasal dari input aktual pada form, bukan pengurangan seluruh BOM secara otomatis.
- Handover PB merupakan aksi terpisah setelah seluruh tahap selesai. Aksi selesai tidak otomatis mengirim jasa/finishing ke PB. Handover dan penyerahan konsumen tidak menimpa status, waktu, atau operator tahap produksi.
- Reprint dari produksi/PB menjadi permintaan yang tercatat untuk ditinjau, tanpa reset tahap. Penimpaan qty hasil historis, revisi mundur yang mereset operasi, dan editor konsumsi/operator lama ditolak. Editor spesifikasi hanya tersedia sebelum pengerjaan.
- Endpoint mutasi pada WIP mensyaratkan POST. Tautan PB di tabel dan control center diganti menjadi POST.
- Nilai detail legacy `2`, `3`, `done`, `on_pb`, dan `on_costumer` dibaca sebagai selesai. Normalisasi tampilan dilakukan di memori; tidak mengubah nilai database. Riwayat legacy yang bertentangan dengan header ditolak ketika dimutasi, bukan diperbaiki diam-diam.
- Membuka preview/snapshot tidak lagi menyisipkan routing. Routing yang belum ada hanya dibuat saat aksi Mulai, dalam transaksi dan berdasarkan master yang ada.
- Antrean tidak lagi dibatasi otomatis ke order tujuh hari terakhir. Filter tanggal eksplisit tetap bekerja. Tab Finishing terpisah dari Siap Serah Terima PB. Pencarian pada daftar tetap dibatasi cabang.
- Refresh berhenti saat ada pilihan batch, pencarian, modal, input fokus, atau request aktif; respons diperiksa ulang sebelum mengganti tabel dan pagination.
- Nama field CSRF inline mengikuti konfigurasi, token respons WIP diperbarui, dan PIN tidak ditulis ke catatan/audit otomatis. Konfigurasi CSRF global aplikasi tidak diubah karena memerlukan inventarisasi request modul lain.

## Batas tahap ini

Tahap ini memperkuat alur yang menggunakan tabel dan status saat ini. Belum membuat entitas QC terpisah, persetujuan QC, work order rework baru, penerimaan PB dua pihak, reservasi material, BOM/routing revision snapshot, kapasitas mesin, atau penutupan parsial dengan toleransi.

Permintaan reprint tidak otomatis membuat pekerjaan baru. Hasil kurang pada tahap aktif dapat dicatat bertahap. Produksi ulang pekerjaan yang telah selesai memerlukan rancangan transaksi rework terpisah, dengan referensi ke pekerjaan asal.

Modul selain jalur produksi/PB yang disebut di atas belum diaudit menyeluruh. Semua pemanggil mesin stok bersama sekarang menggunakan lock material yang sama; perubahan stok langsung di luar mesin ini tetap perlu ditinjau pada tahap lanjutan. Jangan menyimpulkan bahwa seluruh aplikasi telah memenuhi seluruh praktik ERP modern.

## Prasyarat dan kompatibilitas

Tidak menambahkan kolom/tabel. Struktur yang dipakai adalah struktur yang sudah dirujuk controller sebelumnya, antara lain `tbl_produksi`, `detail_produksi`, `tbl_produksi_log`, `tbl_material`, `tbl_stok_gudang`, `tbl_stok_log`, dan `routing`.

Sebelum mutasi, layanan memeriksa keberadaan tabel dan engine InnoDB. Jika tabel tidak mendukung transaksi, aksi ditolak tanpa menjalankan migrasi otomatis. Perubahan status header tidak menulis ulang detail operasi. SPK lama tanpa routing tetapi sudah berstatus selesai tetap bisa diserahterimakan tanpa menciptakan riwayat palsu.

## Validasi

Pengujian tidak memuat bootstrap/config ERP dan tidak membuka koneksi database. PHP memakai adapter tabel dalam memori; DOM memakai jsdom dengan seluruh metode HTTP diganti stub.

```sh
/Applications/XAMPP/xamppfiles/bin/php application/tests/wip_workflow_test.php
node application/tests/wip_produksi_ui_test.cjs
```

Pemeriksaan mencakup transisi, urutan routing, operator/cabang, hasil parsial, replay, batal/mulai ulang, rollback batch, kegagalan ledger, satuan, konsumsi beberapa rak, saldo duplikat, kompatibilitas history legacy, POST payload, hasil aktual pada form, dan refresh saat operator memilih pekerjaan.

Pemeriksaan schema lokal secara baca saja dicoba, tetapi koneksi database tidak tersedia pada sesi ini. Karena itu pengujian integrasi pada salinan database dan pengujian tampilan dengan sesi aplikasi nyata belum dilakukan. Pemeriksaan engine saat runtime bersifat perlindungan, bukan pengganti uji integrasi.

## Berkas utama

- `application/libraries/Production_workflow.php`: aturan dan pemetaan nilai legacy.
- `application/models/Wip_workflow_model.php`: akses, lock, transaksi operasi, dan audit.
- `application/controllers/Wip.php`: adapter endpoint dan validasi material/mesin.
- `application/models/Produksi_model.php`: transisi header tanpa menimpa operasi.
- `application/models/M_gudang.php`: posting konsumsi dengan saldo/ledger yang konsisten.
- `application/controllers/Pb.php`: mempertahankan history pada penyerahan dan permintaan produksi ulang.
- `application/core/MY_Controller.php`: redaksi field PIN pada audit.
- `assets/js/wip_produksi.js` dan view WIP terkait: snapshot tahap, hasil aktual, POST handover, serta filter/refresh.
- `application/tests/wip_workflow_test.php` dan `application/tests/wip_produksi_ui_test.cjs`: pengujian offline.

## Perbaikan pilihan mesin setelah uji pengguna

Daftar mesin kini menggunakan ID item terpilih dan lokasi cetaknya, dengan aturan yang sama seperti validasi penyimpanan. Akun admin tidak lagi mendapat mesin cabang lain untuk item tersebut. Batch lintas lokasi hanya dapat memakai mesin yang ditandai bersama. Sesi pengguna menentukan akses; PIN menentukan pekerja, bukan lokasi mesin.

Pemeriksaan baca saja database lokal melalui socket XAMPP berhasil: Konika 1060 terdaftar pada perusahaan 3 (TGM Merch), sedangkan Konika 10500/3070 pada perusahaan 2 (TGM Official). Tidak ada perubahan master atau transaksi yang dijalankan. Pengujian penyimpanan langsung masih belum dilakukan. Pengujian offline setelah perbaikan: 83 pemeriksaan PHP dan 26 DOM, seluruhnya lolos.

## Penyesuaian mulai dan saran hasil

Sesuai permintaan pengguna, mulai produksi menggunakan sesi login tanpa input PIN. Akses cabang dan divisi tetap diperiksa; log mencatat user yang memulai. Jika akun terhubung ke karyawan, identitas tersebut dipakai; akun tanpa hubungan karyawan tidak diberi identitas pekerja buatan. Verifikasi pada penyelesaian tetap mengikuti form penyelesaian.

Form hasil baik pada daftar, batch, finishing, dan preview SPK menyarankan sisa target tahap aktif (target dikurangi hasil baik yang sudah tercatat, minimum nol). Nilai dapat diedit sebelum disimpan. Data historis tidak diperbarui saat form dibuka.

Pemakaian aktual bahan utama juga diisi awal dengan estimasi kebutuhan item terpilih: sisa target × kebutuhan BOM, atau estimasi panjang untuk bahan roll. Nilainya dapat diedit sebelum konfirmasi untuk memasukkan pemakaian nyata, termasuk bahan rusak/test print. Jasa dan finishing tanpa bahan utama mendapat nilai nol.
