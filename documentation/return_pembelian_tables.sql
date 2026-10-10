-- Create table for return purchase header
CREATE TABLE `tbl_return_pembelian` (
  `return_id` int(11) NOT NULL AUTO_INCREMENT,
  `no_return` varchar(50) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `return_tgl` date NOT NULL,
  `return_waktu` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `return_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `keterangan` text,
  `karyawan_id` int(11) NOT NULL,
  `perusahaan_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`return_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `karyawan_id` (`karyawan_id`),
  KEY `perusahaan_id` (`perusahaan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create table for return purchase detail
CREATE TABLE `tbl_return_pembelian_detail` (
  `return_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `return_id` int(11) NOT NULL,
  `bahan_id` int(11) NOT NULL,
  `qty_return` decimal(10,2) NOT NULL,
  `harga_return` decimal(15,2) NOT NULL,
  `alasan_return` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`return_detail_id`),
  KEY `return_id` (`return_id`),
  KEY `bahan_id` (`bahan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create table for temporary return items
CREATE TABLE `tbl_temp_return` (
  `temp_id` int(11) NOT NULL AUTO_INCREMENT,
  `bahan_id` int(11) NOT NULL,
  `qty_return` decimal(10,2) NOT NULL,
  `harga_return` decimal(15,2) NOT NULL,
  `alasan_return` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`temp_id`),
  KEY `bahan_id` (`bahan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add foreign key constraints
ALTER TABLE `tbl_return_pembelian`
  ADD CONSTRAINT `fk_return_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `tbl_supplier` (`supplier_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_return_karyawan` FOREIGN KEY (`karyawan_id`) REFERENCES `tbl_karyawan` (`karyawan_id`) ON DELETE CASCADE;

ALTER TABLE `tbl_return_pembelian_detail`
  ADD CONSTRAINT `fk_return_detail_header` FOREIGN KEY (`return_id`) REFERENCES `tbl_return_pembelian` (`return_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_return_detail_bahan` FOREIGN KEY (`bahan_id`) REFERENCES `tbl_bahan` (`bahan_id`) ON DELETE CASCADE;

ALTER TABLE `tbl_temp_return`
  ADD CONSTRAINT `fk_temp_return_bahan` FOREIGN KEY (`bahan_id`) REFERENCES `tbl_bahan` (`bahan_id`) ON DELETE CASCADE;

-- Add indexes for better performance
CREATE INDEX `idx_return_tgl` ON `tbl_return_pembelian` (`return_tgl`);
CREATE INDEX `idx_return_supplier` ON `tbl_return_pembelian` (`supplier_id`);
CREATE INDEX `idx_return_detail_return_id` ON `tbl_return_pembelian_detail` (`return_id`);
CREATE INDEX `idx_temp_return_bahan` ON `tbl_temp_return` (`bahan_id`); 