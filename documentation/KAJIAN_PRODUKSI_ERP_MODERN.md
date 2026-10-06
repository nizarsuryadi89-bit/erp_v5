# Kajian proses produksi TGM ERP

Tanggal kajian: 5 Oktober 2026.

Kajian statis berfokus pada `application/views/admin/wip/produksi_list.php`, alur terkait dalam `application/controllers/Wip.php`, JavaScript, komponen tabel, `Produksi_model`, dan mesin stok `M_gudang`. Tidak menjalankan transaksi produksi, perubahan database, atau pengiriman notifikasi. Temuan merupakan perilaku yang terlihat pada kode; dampak pada data aktual belum diuji.

## Kesimpulan

Fondasi sudah tersedia: SPK, item produksi, routing, operator, mesin, BOM, konsumsi bahan, riwayat, dan serah terima PB. Prioritas pembenahan adalah menyatukan aturan proses di server. Mengubah tampilan `produksi_list.php` saja tidak cukup karena beberapa endpoint dapat mengubah data dengan aturan berbeda.

ERP modern bukan satu aturan baku yang mewajibkan setiap perusahaan memakai alur identik. Rancangan di bawah merupakan usulan untuk percetakan TGM, mengacu pada pemisahan work order, operasi, pencatatan bahan, dan pemeriksaan kualitas pada dokumentasi ERPNext dan Odoo.

## Peta alur saat ini

- `Wip::produksi()` mengambil data, menerapkan filter, menghitung statistik, dan membentuk kelompok pekerjaan.
- `produksi_list.php:178` memuat filter; baris 190 dan 195 memuat tabel SPK/item; baris 216 memuat modal; baris 221 memuat `assets/js/wip_produksi.js`.
- Tampilan SPK menutup pekerjaan melalui `selesai_spk_ajax`; tampilan item/batch memakai `batch_selesai_dan_stok` atau `advance_step_ajax`/`advance_step_lf_ajax`.
- Serah terima menggunakan `move_to_pb`, `batch_move_to_pb`, `kirim_pb_spk`, atau `process_all_ready_to_pb_ajax`.
- Pelaporan rijek di halaman memakai `lapor_masalah`; alur batch juga memiliki cabang rijek sendiri.
- Mesin stok utama memperbarui `tbl_stok_gudang` dan menulis `tbl_stok_log` melalui `M_gudang::update_stock_with_log()`.

## Temuan dan prioritas

| Prioritas | Bukti kode | Dampak | Perbaikan target |
|---|---|---|---|
| P0 | `Produksi_model.php:424`, khususnya 460–465: `change_status()` menerima status tanpa matriks transisi dan menimpa status semua detail menjadi `done`/`on_pb`. Routing lain memeriksa `0/1/2`. | Status item dan operasi memakai arti berbeda; persentase selesai dan riwayat operator yang mencari `2` bisa salah. | Pisahkan status operasi dari produksi dan serah terima. Header dihitung dari hasil operasi; serah terima tidak mengubah status operasi. |
| P0 | `Wip.php:1270`: `batch_status_produksi()` menerima `status` dari request lalu memperbarui semua ID; tidak ada daftar status/transisi yang diizinkan di method ini. | Endpoint dapat melewati aturan mulai/selesai jika hanya mengandalkan tombol UI. | Endpoint menerima aksi bisnis, bukan status bebas. Validasi status lama, penugasan, cabang, dan prasyarat di server. |
| P0 | `Wip.php:1528`, 2049, 3747, 4199: validasi stok memiliki kondisi `false && ...`. `M_gudang.php:691–709` menghitung saldo baru tanpa pemeriksaan saldo negatif atau row lock. | Konsumsi melebihi stok dan kehilangan pembaruan saldo saat transaksi bersamaan berisiko terjadi. | Validasi satuan dasar dan gudang/rak yang sama dengan posting; kunci saldo atau gunakan pembaruan atomik. Kekurangan bahan menjadi kondisi tertahan. Kebijakan pengecualian dibuat eksplisit dan diaudit. |
| P0 | `Produksi_model.php:365`: `set_status_pb()` memeriksa cabang, tetapi tidak mensyaratkan status `done`, kelengkapan operasi, hasil QC, atau qty layak diserahkan. | Request langsung berpotensi mengirim pekerjaan yang belum siap ke PB. | Syarat handover diperiksa di server; simpan pengirim, penerima, qty, dan waktu. |
| P0 | `Wip.php:1714` menghasilkan status `finishing`; `v_filter_tabs.php:268–271` memberi label Finishing/QC kepada `ready_pb`; `_apply_produksi_filters()` pada 114–142 mengambil `done` atau sebagian `proses`, bukan `finishing`. | Item berstatus finishing tidak masuk filter/tab finishing tersebut; label QC tidak membuktikan pemeriksaan QC sudah ada. | Tentukan satu model: finishing sebagai operasi routing, QC sebagai pemeriksaan tersendiri. Filter dan penghitung harus mengikuti definisi yang sama. |
| P1 | `Wip.php:1447–1463` dan 1563–1597: reprint mereset seluruh tahap, waktu, operator, dan mesin. Kuantitas rijek tidak menjadi work order baru yang terstruktur. | Bukti pekerjaan pertama hilang; produksi ulang parsial dan tambahan biaya sulit ditelusuri. | Buat siklus/work order rework yang merujuk pekerjaan asal dan qty bermasalah. Pertahankan catatan operasi dan bahan yang sudah terpakai. |
| P1 | `Wip.php:1439–1440`: PIN dimasukkan ke `catatan`; `MY_Controller.php:753` daftar masking audit POST hanya berisi nama field password. | Kredensial operator berpotensi tersimpan dalam catatan dan audit. | Simpan identitas operator terverifikasi; redaksi seluruh field PIN dari log. Jangan menulis nilai PIN ke catatan. |
| P1 | `Wip.php:1757–1760`: seluruh konsumsi utama batch direferensikan ke `$ids[0]`, dengan detail `0`. Jika seluruh outcome gagal, `$any_step_updated` tetap false sehingga konsumsi utama dilewati. | Biaya/traceability bahan batch melekat pada item pertama; bahan rijek dapat tidak tercatat pada jalur tersebut. | Dokumen batch memiliki baris alokasi konsumsi per item/operasi; bahan terpakai dicatat untuk hasil baik maupun scrap. |
| P1 | `Wip.php:1781–1808`: bahan sekunder dianggap sudah dikonsumsi hanya karena pernah ada log material untuk produksi tersebut; baris 1811 dan 1896 memakai `$operator_id` yang tidak didefinisikan dalam method. | Konsumsi parsial/rework bahan yang sama bisa terlewat; atribusi operator tidak andal. | Identitas konsumsi per transaksi, operasi, material, dan siklus; operator menjadi parameter eksplisit. |
| P1 | `Wip.php:3271–3303` dapat menyelesaikan operasi pending tanpa input hasil aktual; `finish_all_spk_items():1060–1062` mengasumsikan output sama dengan order. | Selesai administratif tidak selalu berarti jumlah fisik tercapai. | Catat hasil baik, rusak, sisa pekerjaan, dan penggunaan bahan aktual; jalur cepat tetap harus memenuhi aturan yang sama. |
| P1 | `Wip.php:3716` mengambil detail hanya berdasarkan `detail_id` sesudah pemeriksaan cabang berdasarkan `produksi_id`; tidak mencocokkan keduanya. Method batch lain tidak semuanya menerapkan pembatasan record. | Pemeriksaan akses pada satu ID belum menjamin detail yang ditulis milik record tersebut. | Ambil detail beserta header dalam satu pemeriksaan; validasi cabang/penugasan untuk setiap item batch. Login dan akses menu di `MY_Controller` tetap perlu, tetapi tidak menggantikan pemeriksaan record. |
| P1 | `v_table_spk_view.php:345` memicu `kirim_pb_spk` melalui GET. `Wip.php:4534` menulis status dan dapat mengirim WA. `config.php:474` CSRF nonaktif; inline AJAX `produksi_list.php:399` memakai nama token literal, berbeda dari konfigurasi. | Perubahan status melalui tautan dan perlindungan request belum konsisten. | Semua mutasi melalui POST; CSRF dengan nama token dinamis dan mekanisme pembaruan token. Aktifkan bertahap setelah inventarisasi request aplikasi. |
| P1 | `_apply_produksi_filters():224–236` default membatasi tanggal pembuatan order ke tujuh hari terakhir; pencarian global pada 97–105 keluar sebelum filter cabang. | Pekerjaan lama yang belum selesai dapat hilang dari antrean default; cakupan pencarian global perlu hak akses khusus. | Antrean aktif tidak dibatasi umur order secara default. Pencarian global menjadi izin eksplisit dengan cakupan data yang jelas. |
| P2 | `produksi_list.php:545–584` mengganti isi tabel tiap 60 detik ketika tidak ada modal/input fokus; tidak mempertahankan checkbox terpilih atau memperbarui pagination. | Pilihan batch dapat hilang; pencarian dan jumlah baris/pagination dapat tidak selaras setelah refresh. | Endpoint data khusus, refresh tertunda saat ada pilihan/perubahan, pertahankan state UI, dan perbarui penghitung/pagination bersama tabel. |

Ada upaya proteksi yang sudah baik: sejumlah method memakai transaksi database, `affected_rows()` pada update bersyarat, filter cabang, dan log stok. Namun proteksi tidak merata. Jeda tiga detik bukan pengganti identitas transaksi untuk mencegah request yang sama diproses ulang. `update_warehouse_stock():2830–2841` juga mengabaikan nilai balik mesin stok dan mengembalikan true; keberhasilan posting harus diteruskan dan kegagalan membatalkan transaksi induk.

## Alur target untuk TGM

1. SPK dikonfirmasi dan dirilis sesuai kebijakan pembayaran/kredit perusahaan. Daftar saat ini hanya mengambil `paid`; ini perlu dipertahankan atau dikonfigurasi berdasarkan kebijakan bisnis, bukan diasumsikan semua ERP wajib lunas sebelum produksi.
2. Item produksi mendapat salinan BOM, spesifikasi, revisi file, dan routing saat dirilis. Perubahan master sesudah itu tidak otomatis mengubah pekerjaan berjalan.
3. Material direservasi atau diterbitkan sesuai metode gudang perusahaan; kekurangan bahan terlihat sebagai alasan tertahan.
4. Operasi dijalankan menurut dependensi: prepress/RIP bila diperlukan, cetak, lalu finishing yang diwajibkan produk. Operasi independen dapat paralel jika routing mengizinkan.
5. Operator mencatat mesin, waktu, output baik, output rusak, dan bahan aktual per operasi/batch. Qty bahan dan qty produk memiliki satuan masing-masing.
6. Hasil melewati QC yang diwajibkan. Kegagalan QC menghasilkan scrap atau rework pada qty terkait dengan referensi ke pekerjaan asal.
7. Qty yang lolos QC diserahkan ke PB dengan bukti penerimaan. Handover parsial diperbolehkan hanya jika kebijakan mendukung; saldo sisanya tetap terbuka.
8. Penyerahan ke konsumen memiliki transaksi dan bukti tersendiri. Notifikasi pesanan lengkap hanya dipicu ketika seluruh qty yang diwajibkan siap; notifikasi parsial harus menyatakan parsial.

Contoh: pesanan 100 pcs menghasilkan 95 pcs baik dan 5 pcs rusak. Catat 95 sebagai hasil baik dan 5 sebagai scrap/rework, beserta seluruh bahan yang dipakai. Jika tidak ada toleransi penutupan, pekerjaan belum lengkap sampai kekurangan 5 pcs terpenuhi. Reprint hanya 5 pcs dan tidak menghapus riwayat 100 pcs percobaan pertama.

## Model status dan tanggung jawab

| Objek | Status usulan | Pemilik keputusan |
|---|---|---|
| Produksi per item | draft, released, in_progress, completed, cancelled | Perencanaan/supervisor; completed dihitung dari operasi dan kuantitas yang diwajibkan |
| Operasi | pending, ready, running, paused, completed, cancelled | Operator yang ditugaskan; dependensi menentukan ready |
| QC | pending, passed, failed | Petugas QC/role yang diberi kewenangan |
| Handover | draft, dispatched, received, cancelled | Produksi mengirim, PB mengakui penerimaan |
| Exception | hold dengan kode alasan; rework yang merujuk pekerjaan asal | Supervisor untuk keputusan pengecualian |

Nama di atas usulan model, bukan instruksi mengganti seluruh nilai legacy sekaligus. Finishing tetap merupakan operasi; pengiriman tidak menimpa status operasi completed. Pisahkan operator yang ditugaskan dari operator aktual jika keduanya perlu dicatat.

## Perubahan halaman produksi

- Tampilan SPK berfungsi sebagai ringkasan; tampilan item/operasi menjadi antrean kerja operator. Pengelompokan bahan/mesin tetap berguna untuk nesting, tetapi tidak melewati dependensi dan kompatibilitas produksi.
- Gunakan tab dengan arti konsisten: Siap Dikerjakan, Sedang Dikerjakan, Menunggu Bahan/Tertahan, Menunggu QC, Siap PB, Sudah Diterima PB, Rework. Finishing dapat menjadi filter work center.
- Setiap baris memperlihatkan SPK, produk/spesifikasi, tenggat, operasi aktif, operator/mesin, target dan hasil aktual, serta alasan tertahan.
- Tombol mengikuti aksi yang diperbolehkan server: Mulai, Jeda/Lanjutkan, Catat Hasil, Lapor Masalah, Ajukan QC, Serahkan ke PB. Hasil seluruh SPK tidak disimpulkan dari subset item yang sedang tampil.
- Semua ringkasan harus jelas unit hitungnya: jumlah SPK, item produksi, atau operasi. Saat ini beberapa statistik memakai COUNT produksi_id sementara banner menyebut SPK.
- Preview BOM adalah estimasi yang berasal dari aturan perhitungan server; `produksi_list.php:487` hanya mengalikan bom_qty dengan qty sehingga tidak cukup untuk semua bahan berbasis area, roll, atau formula dinamis.
- Teks produk/catatan yang dimasukkan ke template HTML di baris 248 dan 465–514 harus di-escape atau dirender sebagai text node.

## Urutan implementasi

1. **Satukan definisi dan tutup jalur bypass.** Inventarisasi endpoint beserta pemanggilnya, pisahkan status operasi/header/QC/handover, buat layanan transisi terpusat dan pemeriksaan akses record. Semua endpoint lama harus memakai aturan yang sama atau ditolak secara eksplisit setelah pemanggil dipindahkan.
2. **Benahi transaksi hasil dan stok.** Posting hasil, konsumsi, dan audit dalam satu transaksi; penolakan hasil stok memicu rollback; locking atau update atomik; kunci request unik untuk retry; konsistensi gudang/rak/satuan dasar; alokasi konsumsi batch. Jangan sekadar menghapus `false &&` sebelum konversi satuan dan data saldo diverifikasi.
3. **Tambahkan QC, rework, dan handover terukur.** Riwayat lama dipertahankan. Terapkan aturan kuantitas dan penerimaan PB, termasuk kebijakan parsial dan notifikasi.
4. **Selaraskan UI dan filter.** Antrean pekerjaan lama tetap terlihat; tab finishing/QC benar; tombol menampilkan aksi yang valid; refresh mempertahankan pilihan dan pencarian.
5. **Kembangkan perencanaan dan costing.** Tenggat, prioritas, kapasitas mesin, durasi standar versus aktual, reservasi material, serta biaya aktual bahan/mesin/operator per SPK.

Migrasi perlu memeriksa enum/tipe kolom, indeks unik saldo per material–gudang–rak, engine tabel, variasi nilai status legacy, dan pemanggil di modul PB/Transaksi/Manufaktur. Jangan memetakan nilai detail `on_pb` menjadi bukti setiap operasi pernah dikerjakan tanpa memeriksa catatan historis. Pemeriksaan schema dan data tersebut belum dilakukan dalam kajian ini.

## Kriteria penerimaan untuk implementasi

- Operasi bergantung tidak dapat dimulai sebelum prasyaratnya selesai; request langsung tunduk pada aturan yang sama.
- Request yang dikirim ulang tidak menambah konsumsi atau menyelesaikan tahap berikutnya.
- Dua transaksi bersamaan tidak kehilangan mutasi saldo dan tidak mengonsumsi stok melebihi kebijakan yang berlaku.
- Kegagalan posting stok membatalkan pencatatan hasil dan perubahan status.
- Material, gudang/rak, konversi, dan operator sesuai transaksi; konsumsi hasil rusak tetap tercatat.
- Item tidak dapat diserahkan ke PB sebelum kuantitasnya tersedia dan QC wajib lolos.
- Rework parsial mempertahankan riwayat awal dan mencatat konsumsi tambahan bahan yang sama.
- User lintas cabang/operasi ditolak tanpa izin terkait; PIN tidak muncul pada catatan atau audit.
- Pekerjaan aktif berumur lebih dari tujuh hari tetap muncul; finishing/QC masuk antrean yang benar.
- Refresh tidak menghilangkan pilihan batch; pekerjaan yang diambil operator lain ditolak dengan respons konflik yang jelas.

## Referensi praktik ERP

- [ERPNext Work Order](https://docs.frappe.io/erpnext/work-order): dokumen produksi yang menghubungkan kuantitas, operasi, dan material.
- [ERPNext Job Card](https://docs.frappe.io/erpnext/job-card): pencatatan operasi, operator, dan waktu kerja.
- [ERPNext Stock Entry](https://docs.frappe.io/erpnext/stock-entry): pemisahan transfer material, konsumsi produksi, dan penerimaan hasil; konsumsi dapat terjadi dalam beberapa transaksi.
- [Odoo Work Order Dependencies](https://www.odoo.com/documentation/18.0/applications/inventory_and_mrp/manufacturing/advanced_configuration/work_order_dependencies.html): dependensi dan kesiapan operasi.
- [Odoo Quality Checks](https://www.odoo.com/documentation/18.0/applications/inventory_and_mrp/quality/quality_management/quality_checks.html): pemeriksaan kualitas terkait order/operasi dengan hasil pass/fail.
