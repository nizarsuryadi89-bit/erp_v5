-- Drop existing tables if they exist (be careful with this in production)
-- DROP TABLE IF EXISTS `tbl_pembayaran_detail`;
-- DROP TABLE IF EXISTS `tbl_pembayaran_produk`;
-- DROP TABLE IF EXISTS `tbl_pembayaran`;

-- Create new payment tables with proper structure

CREATE TABLE `tbl_pembayaran` (
  `pembayaran_id` INT AUTO_INCREMENT PRIMARY KEY,
  `nospk` VARCHAR(50) NOT NULL,                  -- relasi ke tbl_pemesanan
  `tgl_bayar` DATE NOT NULL DEFAULT (CURRENT_DATE),
  `jam_bayar` TIME NOT NULL DEFAULT (CURRENT_TIME),
  `total_tagihan` DECIMAL(15,2) NOT NULL,        -- total pesanan
  `total_bayar` DECIMAL(15,2) NOT NULL,          -- jumlah yang dibayar
  `status_lunas` ENUM('0','1') NOT NULL DEFAULT '0', -- 0=belum lunas, 1=lunas
  `kasir_id` INT NOT NULL,                       -- siapa yang memproses
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`nospk`) REFERENCES `tbl_pemesanan`(`nospk`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `tbl_pembayaran_detail` (
  `detail_id` INT AUTO_INCREMENT PRIMARY KEY,
  `pembayaran_id` INT NOT NULL,                  -- relasi ke header
  `metode` ENUM('tunai','transfer','debit','kartu_kredit','ewallet','lainnya') NOT NULL,
  `jumlah` DECIMAL(15,2) NOT NULL,
  `referensi` VARCHAR(100) DEFAULT NULL,         -- nomor transfer, no kartu, no transaksi ewallet, dll
  `bank_id` INT DEFAULT NULL,                    -- kalau transfer/debit
  `edc_id` INT DEFAULT NULL,                     -- kalau pakai mesin EDC
  `ewallet_id` INT DEFAULT NULL,                 -- kalau ewallet
  `keterangan` VARCHAR(255) DEFAULT NULL,        -- catatan tambahan
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`pembayaran_id`) REFERENCES `tbl_pembayaran`(`pembayaran_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `tbl_pembayaran_produk` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nospk` VARCHAR(50) NOT NULL,                  -- relasi ke tbl_pemesanan
  `product_id` INT NOT NULL,                     -- relasi ke tbl_product
  `qty` INT NOT NULL,                            -- quantity produk
  `diskon` DECIMAL(15,2) DEFAULT 0,              -- diskon per item
  `panjang` DECIMAL(10,2) DEFAULT 0,             -- panjang (jika ada)
  `lebar` DECIMAL(10,2) DEFAULT 0,               -- lebar (jika ada)
  `mesin_id` INT DEFAULT NULL,                   -- relasi ke mesin produksi
  `finishing_id` INT DEFAULT NULL,               -- relasi ke finishing
  `harga_id` INT NOT NULL,                       -- relasi ke tbl_harga_product
  `dateline_tgl` DATE DEFAULT NULL,              -- deadline tanggal
  `dateline_jam` TIME DEFAULT NULL,              -- deadline jam
  `lokasi_cetak` INT DEFAULT 0,                  -- lokasi cetak
  `approve` ENUM('0','1') DEFAULT '0',           -- 0=sampel dulu, 1=cetak semua
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`nospk`) REFERENCES `tbl_pemesanan`(`nospk`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `tbl_product`(`product_id`) ON DELETE CASCADE,
  FOREIGN KEY (`harga_id`) REFERENCES `tbl_harga_product`(`harga_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add indexes for better performance
ALTER TABLE `tbl_pembayaran` ADD INDEX `idx_nospk` (`nospk`);
ALTER TABLE `tbl_pembayaran` ADD INDEX `idx_tgl_bayar` (`tgl_bayar`);
ALTER TABLE `tbl_pembayaran` ADD INDEX `idx_kasir_id` (`kasir_id`);

ALTER TABLE `tbl_pembayaran_detail` ADD INDEX `idx_pembayaran_id` (`pembayaran_id`);
ALTER TABLE `tbl_pembayaran_detail` ADD INDEX `idx_metode` (`metode`);

ALTER TABLE `tbl_pembayaran_produk` ADD INDEX `idx_nospk` (`nospk`);
ALTER TABLE `tbl_pembayaran_produk` ADD INDEX `idx_product_id` (`product_id`);
