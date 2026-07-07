-- Script untuk membuat tabel return pembelian
-- Jalankan script ini di database MySQL

-- Tabel untuk menyimpan data sementara return
CREATE TABLE IF NOT EXISTS `tbl_temp_return` (
  `temp_id` int(11) NOT NULL AUTO_INCREMENT,
  `bahan_id` int(11) NOT NULL,
  `qty_return` decimal(10,2) NOT NULL,
  `harga_return` decimal(15,2) NOT NULL,
  `alasan_return` text NOT NULL,
  PRIMARY KEY (`temp_id`),
  KEY `bahan_id` (`bahan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel header return pembelian
CREATE TABLE IF NOT EXISTS `tbl_return_pembelian` (
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
  KEY `perusahaan_id` (`perusahaan_id`),
  CONSTRAINT `fk_return_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `tbl_supplier` (`supplier_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_return_karyawan` FOREIGN KEY (`karyawan_id`) REFERENCES `tbl_karyawan` (`karyawan_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel detail return pembelian
CREATE TABLE IF NOT EXISTS `tbl_return_pembelian_detail` (
  `return_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `return_id` int(11) NOT NULL,
  `bahan_id` int(11) NOT NULL,
  `qty_return` decimal(10,2) NOT NULL,
  `harga_return` decimal(15,2) NOT NULL,
  `alasan_return` text NOT NULL,
  PRIMARY KEY (`return_detail_id`),
  KEY `return_id` (`return_id`),
  KEY `bahan_id` (`bahan_id`),
  CONSTRAINT `fk_return_detail_header` FOREIGN KEY (`return_id`) REFERENCES `tbl_return_pembelian` (`return_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_return_detail_bahan` FOREIGN KEY (`bahan_id`) REFERENCES `tbl_bahan` (`bahan_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data untuk testing (opsional)
-- INSERT INTO tbl_supplier (supplier_nama, supplier_alamat, supplier_nohp) VALUES ('Supplier Test', 'Alamat Test', '08123456789');
-- INSERT INTO tbl_karyawan (karyawan_nama, karyawan_email) VALUES ('Karyawan Test', 'test@example.com'); 