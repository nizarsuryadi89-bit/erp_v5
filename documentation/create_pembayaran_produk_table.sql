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
