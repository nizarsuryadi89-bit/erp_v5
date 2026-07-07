-- SQL Script to add bahanbaku_id and lokasi_cetak fields to detail_produksi table
-- Run this script in your MySQL database to enable material replacement and print location functionality

-- Add bahanbaku_id field to detail_produksi table
ALTER TABLE `detail_produksi` 
ADD COLUMN `bahanbaku_id` INT(11) NULL DEFAULT NULL AFTER `lokasi_proses`,
ADD INDEX `idx_bahanbaku_id` (`bahanbaku_id`);

-- Add lokasi_cetak field to detail_produksi table
ALTER TABLE `detail_produksi` 
ADD COLUMN `lokasi_cetak` INT(11) NULL DEFAULT NULL AFTER `bahanbaku_id`,
ADD INDEX `idx_lokasi_cetak` (`lokasi_cetak`);

-- Add foreign key constraint (optional - uncomment if you want referential integrity)
-- ALTER TABLE `detail_produksi` 
-- ADD CONSTRAINT `fk_detail_produksi_bahanbaku` 
-- FOREIGN KEY (`bahanbaku_id`) REFERENCES `tbl_bahan`(`bahanbaku_id`) 
-- ON DELETE SET NULL ON UPDATE CASCADE;

-- ALTER TABLE `detail_produksi` 
-- ADD CONSTRAINT `fk_detail_produksi_lokasi_cetak` 
-- FOREIGN KEY (`lokasi_cetak`) REFERENCES `tbl_perusahaan`(`id_perusahaan`) 
-- ON DELETE SET NULL ON UPDATE CASCADE;

-- Add comments to the fields
ALTER TABLE `detail_produksi` 
MODIFY COLUMN `bahanbaku_id` INT(11) NULL DEFAULT NULL COMMENT 'ID bahan baku yang digunakan (jika berbeda dari BOM default)';

ALTER TABLE `detail_produksi` 
MODIFY COLUMN `lokasi_cetak` INT(11) NULL DEFAULT NULL COMMENT 'ID lokasi cetak untuk menentukan gudang yang akan dikurangi stoknya';

-- Verify the changes
DESCRIBE `detail_produksi`;
