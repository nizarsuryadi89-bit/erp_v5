-- Create tbl_hutang table for credit purchases
CREATE TABLE `tbl_hutang` (
  `hutang_id` INT AUTO_INCREMENT PRIMARY KEY,
  `pembelian_id` INT NOT NULL,              -- transaksi pembelian barang
  `supplier_id` INT NOT NULL,
  `total_tagihan` DECIMAL(15,2) NOT NULL,
  `total_bayar` DECIMAL(15,2) DEFAULT 0,
  `sisa_hutang` DECIMAL(15,2) GENERATED ALWAYS AS (`total_tagihan` - `total_bayar`) STORED,
  `jatuh_tempo` DATE NOT NULL,
  `status` ENUM('BELUM LUNAS','SEBAGIAN','LUNAS') DEFAULT 'BELUM LUNAS',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`pembelian_id`) REFERENCES `tbl_pembelian`(`pembelian_id`),
  FOREIGN KEY (`supplier_id`) REFERENCES `tbl_supplier`(`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add indexes for better performance
CREATE INDEX `idx_hutang_pembelian_id` ON `tbl_hutang`(`pembelian_id`);
CREATE INDEX `idx_hutang_supplier_id` ON `tbl_hutang`(`supplier_id`);
CREATE INDEX `idx_hutang_status` ON `tbl_hutang`(`status`);
CREATE INDEX `idx_hutang_jatuh_tempo` ON `tbl_hutang`(`jatuh_tempo`);
CREATE INDEX `idx_hutang_created_at` ON `tbl_hutang`(`created_at`);
