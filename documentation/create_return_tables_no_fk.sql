-- Script untuk membuat tabel return pembelian tanpa foreign key constraint
-- Jalankan script ini di database MySQL untuk testing

-- Drop existing tables if they exist
DROP TABLE IF EXISTS `tbl_return_pembelian_detail`;
DROP TABLE IF EXISTS `tbl_return_pembelian`;
DROP TABLE IF EXISTS `tbl_temp_return`;

-- Tabel untuk menyimpan data sementara return
CREATE TABLE `tbl_temp_return` (
  `temp_id` int(11) NOT NULL AUTO_INCREMENT,
  `bahan_id` int(11) NOT NULL,
  `qty_return` decimal(10,2) NOT NULL,
  `harga_return` decimal(15,2) NOT NULL,
  `alasan_return` text NOT NULL,
  PRIMARY KEY (`temp_id`),
  KEY `bahan_id` (`bahan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel header return pembelian (tanpa foreign key)
CREATE TABLE `tbl_return_pembelian` (
  `return_id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(11) NOT NULL,
  `return_tgl` date NOT NULL,
  `no_return` varchar(50) NOT NULL,
  `keterangan` text,
  `karyawan_id` int(11) NOT NULL,
  `return_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `perusahaan_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`return_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `karyawan_id` (`karyawan_id`),
  KEY `perusahaan_id` (`perusahaan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel detail return pembelian (tanpa foreign key)
CREATE TABLE `tbl_return_pembelian_detail` (
  `return_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `return_id` int(11) NOT NULL,
  `bahan_id` int(11) NOT NULL,
  `qty_return` decimal(10,2) NOT NULL,
  `harga_return` decimal(15,2) NOT NULL,
  `alasan_return` text NOT NULL,
  PRIMARY KEY (`return_detail_id`),
  KEY `return_id` (`return_id`),
  KEY `bahan_id` (`bahan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data untuk testing
INSERT INTO `tbl_karyawan` (`karyawan_id`, `karyawan_nama`, `karyawan_email`, `karyawan_nohp`) 
VALUES (1, 'System User', 'system@example.com', '0000000000')
ON DUPLICATE KEY UPDATE `karyawan_nama` = 'System User'; 