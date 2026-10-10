-- Script untuk memperbaiki field active di tabel tbl_bahan
-- Jalankan script ini di database MySQL

USE tgm2025;

-- 1. Perbaiki field active di tbl_bahan
ALTER TABLE `tbl_bahan` MODIFY COLUMN `active` ENUM('0','1') NOT NULL DEFAULT '1';

-- 2. Update semua record yang memiliki active = NULL menjadi '1'
UPDATE `tbl_bahan` SET `active` = '1' WHERE `active` IS NULL OR `active` = '';

-- 3. Verifikasi perubahan
DESCRIBE `tbl_bahan`;

-- 4. Tampilkan beberapa sample data
SELECT `bahan_id`, `bahan_nama`, `active` FROM `tbl_bahan` LIMIT 5;
