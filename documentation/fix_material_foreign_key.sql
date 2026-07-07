-- Script untuk memperbaiki foreign key constraint di tabel tbl_material
-- Jalankan script ini di database MySQL

-- 1. Drop foreign key constraint yang lama (jika ada)
ALTER TABLE `tbl_material` DROP FOREIGN KEY IF EXISTS `tbl_material_ibfk_1`;

-- 2. Drop index yang terkait (jika ada)
ALTER TABLE `tbl_material` DROP INDEX IF EXISTS `bahan_id`;

-- 3. Tambahkan foreign key constraint yang baru
ALTER TABLE `tbl_material` 
ADD CONSTRAINT `fk_material_bahan` 
FOREIGN KEY (`bahan_id`) REFERENCES `tbl_bahan`(`bahan_id`) 
ON UPDATE CASCADE ON DELETE RESTRICT;

-- 4. Tambahkan index untuk performa
ALTER TABLE `tbl_material` ADD INDEX `idx_bahan_id` (`bahan_id`);

-- 5. Verifikasi struktur tabel
DESCRIBE `tbl_material`;

-- 6. Tampilkan foreign key constraints
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'tbl_material' 
AND REFERENCED_TABLE_NAME IS NOT NULL;
