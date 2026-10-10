-- =====================================================
-- WAREHOUSE MANAGEMENT SYSTEM - DATABASE TABLES
-- =====================================================

-- 1. Create mutasi_gudang table for tracking stock movements
CREATE TABLE IF NOT EXISTS `mutasi_gudang` (
    `mutasi_id` int(11) NOT NULL AUTO_INCREMENT,
    `tanggal_mutasi` date NOT NULL,
    `waktu_mutasi` time NOT NULL,
    `jenis_mutasi` enum('MASUK','KELUAR','TRANSFER') NOT NULL,
    `gudang_asal` int(11) NULL DEFAULT NULL COMMENT 'Gudang asal (untuk transfer/keluar)',
    `gudang_tujuan` int(11) NULL DEFAULT NULL COMMENT 'Gudang tujuan (untuk transfer/masuk)',
    `bahan_id` int(11) NOT NULL,
    `qty_mutasi` decimal(15,2) NOT NULL,
    `satuan_id` int(11) NOT NULL,
    `keterangan` text NULL,
    `referensi_no` varchar(100) NULL DEFAULT NULL COMMENT 'No referensi (PO, SPK, dll)',
    `referensi_tipe` varchar(50) NULL DEFAULT NULL COMMENT 'Tipe referensi (PEMBELIAN, PRODUKSI, TRANSFER, dll)',
    `supplier_id` int(11) NULL DEFAULT NULL COMMENT 'Supplier (untuk mutasi masuk dari supplier)',
    `customer_id` int(11) NULL DEFAULT NULL COMMENT 'Customer (untuk mutasi keluar ke customer)',
    `created_by` int(11) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`mutasi_id`),
    KEY `idx_tanggal` (`tanggal_mutasi`),
    KEY `idx_jenis_mutasi` (`jenis_mutasi`),
    KEY `idx_bahan` (`bahan_id`),
    KEY `idx_gudang_asal` (`gudang_asal`),
    KEY `idx_gudang_tujuan` (`gudang_tujuan`),
    KEY `idx_referensi` (`referensi_no`, `referensi_tipe`),
    KEY `idx_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Create warehouse_locations table for warehouse management
CREATE TABLE IF NOT EXISTS `warehouse_locations` (
    `location_id` int(11) NOT NULL AUTO_INCREMENT,
    `gudang_id` int(11) NOT NULL,
    `location_name` varchar(100) NOT NULL,
    `location_code` varchar(20) NOT NULL,
    `location_type` enum('RACK','SHELF','BIN','AREA') NOT NULL DEFAULT 'AREA',
    `parent_location_id` int(11) NULL DEFAULT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `capacity` decimal(15,2) NULL DEFAULT NULL,
    `capacity_unit` varchar(20) NULL DEFAULT NULL,
    `description` text NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`location_id`),
    UNIQUE KEY `uk_location_code` (`location_code`),
    KEY `idx_gudang_id` (`gudang_id`),
    KEY `idx_parent_location` (`parent_location_id`),
    KEY `idx_location_type` (`location_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Create stock_movements table for detailed stock tracking
CREATE TABLE IF NOT EXISTS `stock_movements` (
    `movement_id` int(11) NOT NULL AUTO_INCREMENT,
    `mutasi_id` int(11) NOT NULL,
    `bahan_id` int(11) NOT NULL,
    `gudang_id` int(11) NOT NULL,
    `location_id` int(11) NULL DEFAULT NULL,
    `movement_type` enum('IN','OUT','ADJUSTMENT') NOT NULL,
    `qty_before` decimal(15,2) NOT NULL DEFAULT 0,
    `qty_movement` decimal(15,2) NOT NULL,
    `qty_after` decimal(15,2) NOT NULL,
    `unit_cost` decimal(15,2) NULL DEFAULT NULL,
    `total_cost` decimal(15,2) NULL DEFAULT NULL,
    `batch_no` varchar(100) NULL DEFAULT NULL,
    `expiry_date` date NULL DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`movement_id`),
    KEY `idx_mutasi_id` (`mutasi_id`),
    KEY `idx_bahan_id` (`bahan_id`),
    KEY `idx_gudang_id` (`gudang_id`),
    KEY `idx_location_id` (`location_id`),
    KEY `idx_movement_type` (`movement_type`),
    KEY `idx_batch_no` (`batch_no`),
    KEY `idx_expiry_date` (`expiry_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Create warehouse_settings table for configuration
CREATE TABLE IF NOT EXISTS `warehouse_settings` (
    `setting_id` int(11) NOT NULL AUTO_INCREMENT,
    `setting_key` varchar(100) NOT NULL,
    `setting_value` text NOT NULL,
    `setting_description` text NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`setting_id`),
    UNIQUE KEY `uk_setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Insert default warehouse settings
INSERT INTO `warehouse_settings` (`setting_key`, `setting_value`, `setting_description`) VALUES
('default_gudang_utama', '1', 'ID Gudang Utama (Bahan Baku)'),
('default_gudang_produksi_1', '2', 'ID Gudang Produksi 1'),
('default_gudang_produksi_2', '3', 'ID Gudang Produksi 2'),
('enable_batch_tracking', '1', 'Enable batch number tracking'),
('enable_expiry_tracking', '1', 'Enable expiry date tracking'),
('enable_location_tracking', '1', 'Enable location tracking within warehouse'),
('auto_adjust_stock', '1', 'Automatically adjust stock on movement'),
('require_approval_transfer', '0', 'Require approval for inter-warehouse transfers'),
('require_approval_external', '1', 'Require approval for external movements (supplier/customer)');

-- 6. Insert default warehouse locations for existing warehouses
INSERT INTO `warehouse_locations` (`gudang_id`, `location_name`, `location_code`, `location_type`, `description`) VALUES
-- Gudang Utama (ID: 1)
(1, 'Area Penerimaan', 'G01-RCV', 'AREA', 'Area untuk menerima barang dari supplier'),
(1, 'Area Penyimpanan A', 'G01-STOR-A', 'AREA', 'Area penyimpanan bahan baku kategori A'),
(1, 'Area Penyimpanan B', 'G01-STOR-B', 'AREA', 'Area penyimpanan bahan baku kategori B'),
(1, 'Area Quality Control', 'G01-QC', 'AREA', 'Area untuk quality control'),
(1, 'Area Pengiriman', 'G01-SHIP', 'AREA', 'Area untuk pengiriman ke gudang produksi'),

-- Gudang Produksi 1 (ID: 2)
(2, 'Area Penerimaan', 'G02-RCV', 'AREA', 'Area untuk menerima barang dari gudang utama'),
(2, 'Area Produksi 1', 'G02-PROD-1', 'AREA', 'Area produksi line 1'),
(2, 'Area Produksi 2', 'G02-PROD-2', 'AREA', 'Area produksi line 2'),
(2, 'Area WIP', 'G02-WIP', 'AREA', 'Area work in progress'),
(2, 'Area Finished Goods', 'G02-FG', 'AREA', 'Area finished goods'),

-- Gudang Produksi 2 (ID: 3)
(3, 'Area Penerimaan', 'G03-RCV', 'AREA', 'Area untuk menerima barang dari gudang utama'),
(3, 'Area Produksi 1', 'G03-PROD-1', 'AREA', 'Area produksi line 1'),
(3, 'Area Produksi 2', 'G03-PROD-2', 'AREA', 'Area produksi line 2'),
(3, 'Area WIP', 'G03-WIP', 'AREA', 'Area work in progress'),
(3, 'Area Finished Goods', 'G03-FG', 'AREA', 'Area finished goods');

-- 7. Add indexes to existing tables for better performance
ALTER TABLE `tbl_stok_gudang` 
ADD INDEX `idx_gudang_bahan` (`gudang_id`, `bahan_id`),
ADD INDEX `idx_stok_aktif` (`stok_awal`, `stok_tambah`, `stok_kurang`);

-- 8. Add foreign key constraints (optional - uncomment if needed)
/*
ALTER TABLE `mutasi_gudang` 
ADD CONSTRAINT `fk_mutasi_bahan` FOREIGN KEY (`bahan_id`) REFERENCES `tbl_bahan`(`bahan_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
ADD CONSTRAINT `fk_mutasi_gudang_asal` FOREIGN KEY (`gudang_asal`) REFERENCES `tbl_gudang`(`gudang_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
ADD CONSTRAINT `fk_mutasi_gudang_tujuan` FOREIGN KEY (`gudang_tujuan`) REFERENCES `tbl_gudang`(`gudang_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
ADD CONSTRAINT `fk_mutasi_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `tbl_supplier`(`supplier_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
ADD CONSTRAINT `fk_mutasi_customer` FOREIGN KEY (`customer_id`) REFERENCES `tbl_konsumen`(`konsumen_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
ADD CONSTRAINT `fk_mutasi_created_by` FOREIGN KEY (`created_by`) REFERENCES `users`(`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `warehouse_locations` 
ADD CONSTRAINT `fk_location_gudang` FOREIGN KEY (`gudang_id`) REFERENCES `tbl_gudang`(`gudang_id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_location_parent` FOREIGN KEY (`parent_location_id`) REFERENCES `warehouse_locations`(`location_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `stock_movements` 
ADD CONSTRAINT `fk_movement_mutasi` FOREIGN KEY (`mutasi_id`) REFERENCES `mutasi_gudang`(`mutasi_id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_movement_bahan` FOREIGN KEY (`bahan_id`) REFERENCES `tbl_bahan`(`bahan_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
ADD CONSTRAINT `fk_movement_gudang` FOREIGN KEY (`gudang_id`) REFERENCES `tbl_gudang`(`gudang_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
ADD CONSTRAINT `fk_movement_location` FOREIGN KEY (`location_id`) REFERENCES `warehouse_locations`(`location_id`) ON DELETE RESTRICT ON UPDATE CASCADE;
*/
