-- Fix tbl_pengambilan_detail table structure
-- This script safely updates the table structure

-- First, check if the table exists and show its current structure
DESCRIBE tbl_pengambilan_detail;

-- Backup the table first
CREATE TABLE tbl_pengambilan_detail_backup AS SELECT * FROM tbl_pengambilan_detail;

-- Drop and recreate the table with correct structure
DROP TABLE IF EXISTS tbl_pengambilan_detail;

CREATE TABLE `tbl_pengambilan_detail` (
  `pengambilan_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `pengambilan_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `pengambilan_qty` decimal(10,2) NOT NULL,
  `gudang_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pengambilan_detail_id`),
  KEY `idx_pengambilan_id` (`pengambilan_id`),
  KEY `idx_material_id` (`material_id`),
  KEY `idx_gudang_id` (`gudang_id`),
  CONSTRAINT `fk_detail_pengambilan` FOREIGN KEY (`pengambilan_id`) REFERENCES `tbl_pengambilan` (`pengambilan_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_detail_material` FOREIGN KEY (`material_id`) REFERENCES `tbl_material` (`material_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_detail_gudang` FOREIGN KEY (`gudang_id`) REFERENCES `tbl_gudang` (`gudang_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Restore data from backup (if needed)
-- INSERT INTO tbl_pengambilan_detail (pengambilan_id, material_id, pengambilan_qty, gudang_id)
-- SELECT pengambilan_id, material_id, pengambilan_qty, gudang_id FROM tbl_pengambilan_detail_backup;
