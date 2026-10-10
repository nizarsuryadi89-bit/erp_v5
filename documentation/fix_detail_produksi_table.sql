-- SQL Script to fix detail_produksi table structure
-- Run this script in your MySQL database to ensure all required fields are present

-- Check if fields exist and add them if they don't
-- Add bahanbaku_id field if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'detail_produksi' 
     AND COLUMN_NAME = 'bahanbaku_id') = 0,
    'ALTER TABLE `detail_produksi` ADD COLUMN `bahanbaku_id` INT(11) NULL DEFAULT NULL AFTER `lokasi_proses`;',
    'SELECT "bahanbaku_id field already exists" as message;'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add lokasi_cetak field if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'detail_produksi' 
     AND COLUMN_NAME = 'lokasi_cetak') = 0,
    'ALTER TABLE `detail_produksi` ADD COLUMN `lokasi_cetak` INT(11) NULL DEFAULT NULL AFTER `bahanbaku_id`;',
    'SELECT "lokasi_cetak field already exists" as message;'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add indexes if they don't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'detail_produksi' 
     AND INDEX_NAME = 'idx_bahanbaku_id') = 0,
    'ALTER TABLE `detail_produksi` ADD INDEX `idx_bahanbaku_id` (`bahanbaku_id`);',
    'SELECT "idx_bahanbaku_id index already exists" as message;'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'detail_produksi' 
     AND INDEX_NAME = 'idx_lokasi_cetak') = 0,
    'ALTER TABLE `detail_produksi` ADD INDEX `idx_lokasi_cetak` (`lokasi_cetak`);',
    'SELECT "idx_lokasi_cetak index already exists" as message;'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Ensure all required fields exist
-- Add operator_id field if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'detail_produksi' 
     AND COLUMN_NAME = 'operator_id') = 0,
    'ALTER TABLE `detail_produksi` ADD COLUMN `operator_id` INT(11) NULL DEFAULT NULL AFTER `mesin_id`;',
    'SELECT "operator_id field already exists" as message;'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add mesin_id field if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'detail_produksi' 
     AND COLUMN_NAME = 'mesin_id') = 0,
    'ALTER TABLE `detail_produksi` ADD COLUMN `mesin_id` INT(11) NULL DEFAULT NULL AFTER `routing_urutan`;',
    'SELECT "mesin_id field already exists" as message;'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add lokasi_proses field if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'detail_produksi' 
     AND COLUMN_NAME = 'lokasi_proses') = 0,
    'ALTER TABLE `detail_produksi` ADD COLUMN `lokasi_proses` INT(11) NULL DEFAULT NULL AFTER `lebar_output`;',
    'SELECT "lokasi_proses field already exists" as message;'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add alasan_missing field if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'detail_produksi' 
     AND COLUMN_NAME = 'alasan_missing') = 0,
    'ALTER TABLE `detail_produksi` ADD COLUMN `alasan_missing` TEXT NULL DEFAULT NULL AFTER `lokasi_proses`;',
    'SELECT "alasan_missing field already exists" as message;'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add waktu_mulai field if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'detail_produksi' 
     AND COLUMN_NAME = 'waktu_mulai') = 0,
    'ALTER TABLE `detail_produksi` ADD COLUMN `waktu_mulai` DATETIME NULL DEFAULT NULL AFTER `alasan_missing`;',
    'SELECT "waktu_mulai field already exists" as message;'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add waktu_selesai field if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'detail_produksi' 
     AND COLUMN_NAME = 'waktu_selesai') = 0,
    'ALTER TABLE `detail_produksi` ADD COLUMN `waktu_selesai` DATETIME NULL DEFAULT NULL AFTER `waktu_mulai`;',
    'SELECT "waktu_selesai field already exists" as message;'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verify the table structure
DESCRIBE `detail_produksi`;

-- Show current table structure
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'detail_produksi'
ORDER BY ORDINAL_POSITION;
