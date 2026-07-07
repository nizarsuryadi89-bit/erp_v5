-- Script untuk membuat tabel tbl_material
-- Jalankan script ini di database MySQL

CREATE TABLE IF NOT EXISTS `tbl_material` (
  `material_id` INT(11) NOT NULL AUTO_INCREMENT,
  `bahan_id` INT(11) NOT NULL,
  `varian` VARCHAR(200) NOT NULL,
  `satuan_gudang_id` INT(11) NOT NULL COMMENT 'satuan penyimpanan di gudang',
  `konversi` DECIMAL(10,2) NOT NULL COMMENT 'berapa satuan produksi per 1 satuan gudang',
  `harga_jual` DECIMAL(15,2) DEFAULT NULL,
  `status` ENUM('0','1') DEFAULT '1' COMMENT '0=nonaktif, 1=aktif',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`material_id`),
  FOREIGN KEY (`bahan_id`) REFERENCES `tbl_bahan_baku`(`bahanbaku_id`) 
    ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (`satuan_gudang_id`) REFERENCES `tbl_satuan`(`satuan_id`) 
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tambahkan index untuk performa
ALTER TABLE `tbl_material` ADD INDEX `idx_bahan_id` (`bahan_id`);
ALTER TABLE `tbl_material` ADD INDEX `idx_varian` (`varian`);
ALTER TABLE `tbl_material` ADD INDEX `idx_status` (`status`);
