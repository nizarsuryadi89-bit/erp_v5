-- Simple SQL Script to fix detail_produksi table structure
-- Run this script in your MySQL database

-- Check if detail_produksi table exists, if not create it
CREATE TABLE IF NOT EXISTS `detail_produksi` (
  `detail_produksi_id` int(11) NOT NULL AUTO_INCREMENT,
  `produksi_id` int(11) NOT NULL,
  `routing_id` int(11) NOT NULL,
  `routing_urutan` int(11) NOT NULL,
  `mesin_id` int(11) DEFAULT NULL,
  `operator_id` int(11) DEFAULT NULL,
  `bahanbaku_id` int(11) DEFAULT NULL,
  `qty_input` decimal(10,2) NOT NULL,
  `qty_output` decimal(10,2) DEFAULT 0,
  `qty_missing` decimal(10,2) DEFAULT 0,
  `panjang_output` decimal(10,2) DEFAULT NULL,
  `lebar_output` decimal(10,2) DEFAULT NULL,
  `lokasi_proses` int(11) DEFAULT NULL,
  `alasan_missing` text DEFAULT NULL,
  `status_produksi` enum('0','1','2','3','4') DEFAULT '0',
  `waktu_mulai` datetime DEFAULT NULL,
  `waktu_selesai` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `lokasi_cetak` int(11) DEFAULT NULL,
  PRIMARY KEY (`detail_produksi_id`),
  KEY `idx_produksi_id` (`produksi_id`),
  KEY `idx_routing_id` (`routing_id`),
  KEY `idx_status_produksi` (`status_produksi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add missing columns if they don't exist (safe to run multiple times)
ALTER TABLE `detail_produksi` 
ADD COLUMN IF NOT EXISTS `mesin_id` int(11) DEFAULT NULL AFTER `routing_urutan`,
ADD COLUMN IF NOT EXISTS `operator_id` int(11) DEFAULT NULL AFTER `mesin_id`,
ADD COLUMN IF NOT EXISTS `bahanbaku_id` int(11) DEFAULT NULL AFTER `operator_id`,
ADD COLUMN IF NOT EXISTS `lokasi_proses` int(11) DEFAULT NULL AFTER `lebar_output`,
ADD COLUMN IF NOT EXISTS `alasan_missing` text DEFAULT NULL AFTER `lokasi_proses`,
ADD COLUMN IF NOT EXISTS `waktu_mulai` datetime DEFAULT NULL AFTER `alasan_missing`,
ADD COLUMN IF NOT EXISTS `waktu_selesai` datetime DEFAULT NULL AFTER `waktu_mulai`,
ADD COLUMN IF NOT EXISTS `lokasi_cetak` int(11) DEFAULT NULL AFTER `waktu_selesai`;

-- Add indexes if they don't exist
ALTER TABLE `detail_produksi` 
ADD INDEX IF NOT EXISTS `idx_produksi_id` (`produksi_id`),
ADD INDEX IF NOT EXISTS `idx_routing_id` (`routing_id`),
ADD INDEX IF NOT EXISTS `idx_status_produksi` (`status_produksi`),
ADD INDEX IF NOT EXISTS `idx_bahanbaku_id` (`bahanbaku_id`),
ADD INDEX IF NOT EXISTS `idx_lokasi_cetak` (`lokasi_cetak`);

-- Show the final table structure
DESCRIBE `detail_produksi`;

-- Show sample data if any exists
SELECT COUNT(*) as total_records FROM `detail_produksi`;

-- Show table information
SHOW TABLE STATUS LIKE 'detail_produksi';
