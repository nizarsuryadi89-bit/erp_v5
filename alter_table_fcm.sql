-- Jalankan query berikut di phpMyAdmin server online Anda (erp.tgmprint.com)
-- Pilih database db_tgmerp milik Anda, klik tab SQL, copy-paste baris berikut lalu klik "Go" (Kirim).

ALTER TABLE `tbl_karyawan` ADD COLUMN `fcm_token` TEXT DEFAULT NULL;
ALTER TABLE `users` ADD COLUMN `fcm_token` TEXT DEFAULT NULL;
