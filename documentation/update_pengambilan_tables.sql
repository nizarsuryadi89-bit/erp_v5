-- Update tables for pengambilan process
-- Ensure all tables have correct structure

-- 1. Update tbl_pengambilan_detail to use material_id and ensure correct structure
ALTER TABLE `tbl_pengambilan_detail` 
CHANGE COLUMN `bahan_id` `material_id` int(11) NOT NULL;

-- Ensure pengambilan_qty field exists (not qty)
ALTER TABLE `tbl_pengambilan_detail` 
CHANGE COLUMN `qty` `pengambilan_qty` decimal(10,2) NOT NULL;

-- 2. Update tbl_mutasi_detail to use material_id (if exists)
-- If table doesn't exist, create it
CREATE TABLE IF NOT EXISTS `tbl_mutasi_detail` (
  `mutasi_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `mutasi_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `qty` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mutasi_detail_id`),
  KEY `idx_mutasi_id` (`mutasi_id`),
  KEY `idx_material_id` (`material_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Update tbl_mutasi (if exists)
-- If table doesn't exist, create it
CREATE TABLE IF NOT EXISTS `tbl_mutasi` (
  `mutasi_id` int(11) NOT NULL AUTO_INCREMENT,
  `mutasi_tgl` date NOT NULL,
  `gudang_asal` int(11) NOT NULL,
  `gudang_tujuan` int(11) NOT NULL,
  `keterangan` text,
  `status` enum('Pending','Selesai','Batal') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mutasi_id`),
  KEY `idx_gudang_asal` (`gudang_asal`),
  KEY `idx_gudang_tujuan` (`gudang_tujuan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Ensure tbl_stok_gudang has correct structure
ALTER TABLE `tbl_stok_gudang` 
CHANGE COLUMN `bahan_id` `material_id` int(11) NOT NULL;

-- 5. Add indexes for better performance
ALTER TABLE `tbl_pengambilan_detail` ADD INDEX `idx_pengambilan_id` (`pengambilan_id`);
ALTER TABLE `tbl_pengambilan_detail` ADD INDEX `idx_material_id` (`material_id`);
ALTER TABLE `tbl_stok_gudang` ADD INDEX `idx_material_gudang` (`material_id`, `gudang_id`);

-- 6. Update any existing data to use material_id instead of bahan_id
-- (This should be done carefully based on your existing data mapping)
