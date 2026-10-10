-- Script untuk memperbaiki foreign key constraint
-- Jalankan script ini di database MySQL

-- 1. Drop foreign key constraints yang bermasalah
ALTER TABLE `tbl_return_pembelian` DROP FOREIGN KEY `fk_return_karyawan`;
ALTER TABLE `tbl_return_pembelian` DROP FOREIGN KEY `fk_return_supplier`;

-- 2. Recreate foreign key constraints dengan ON DELETE SET NULL atau RESTRICT
ALTER TABLE `tbl_return_pembelian` 
ADD CONSTRAINT `fk_return_karyawan` 
FOREIGN KEY (`karyawan_id`) REFERENCES `tbl_karyawan` (`karyawan_id`) 
ON DELETE SET NULL;

ALTER TABLE `tbl_return_pembelian` 
ADD CONSTRAINT `fk_return_supplier` 
FOREIGN KEY (`supplier_id`) REFERENCES `tbl_supplier` (`supplier_id`) 
ON DELETE RESTRICT;

-- 3. Drop foreign key constraints di detail table
ALTER TABLE `tbl_return_pembelian_detail` DROP FOREIGN KEY `fk_return_detail_header`;
ALTER TABLE `tbl_return_pembelian_detail` DROP FOREIGN KEY `fk_return_detail_bahan`;

-- 4. Recreate foreign key constraints di detail table
ALTER TABLE `tbl_return_pembelian_detail` 
ADD CONSTRAINT `fk_return_detail_header` 
FOREIGN KEY (`return_id`) REFERENCES `tbl_return_pembelian` (`return_id`) 
ON DELETE CASCADE;

ALTER TABLE `tbl_return_pembelian_detail` 
ADD CONSTRAINT `fk_return_detail_bahan` 
FOREIGN KEY (`bahan_id`) REFERENCES `tbl_bahan` (`bahan_id`) 
ON DELETE RESTRICT;

-- 5. Insert default karyawan jika tidak ada
INSERT IGNORE INTO `tbl_karyawan` (`karyawan_id`, `karyawan_nama`, `karyawan_email`, `karyawan_nohp`) 
VALUES (1, 'System User', 'system@example.com', '0000000000');

-- 6. Update karyawan_id yang tidak valid di return pembelian
UPDATE `tbl_return_pembelian` 
SET `karyawan_id` = 1 
WHERE `karyawan_id` NOT IN (SELECT `karyawan_id` FROM `tbl_karyawan`);

-- 7. Set karyawan_id menjadi NULL jika tidak ada default karyawan
-- UPDATE `tbl_return_pembelian` SET `karyawan_id` = NULL WHERE `karyawan_id` = 1 AND (SELECT COUNT(*) FROM `tbl_karyawan`) = 0; 