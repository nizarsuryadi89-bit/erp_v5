-- Create tbl_hutang_pembayaran table for hutang payment history
-- This table stores the payment history for hutang transactions

CREATE TABLE `tbl_hutang_pembayaran` (
  `pembayaran_id` INT AUTO_INCREMENT PRIMARY KEY,
  `hutang_id` INT NOT NULL,
  `tanggal_bayar` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `jumlah_bayar` DECIMAL(15,2) NOT NULL,
  `metode_bayar` ENUM('cash','transfer') NOT NULL,
  `rekening_tujuan` VARCHAR(255) NULL,
  `keterangan` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`hutang_id`) REFERENCES `tbl_hutang`(`hutang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add indexes for better performance
CREATE INDEX `idx_hutang_pembayaran_hutang_id` ON `tbl_hutang_pembayaran`(`hutang_id`);
CREATE INDEX `idx_hutang_pembayaran_tanggal` ON `tbl_hutang_pembayaran`(`tanggal_bayar`);
CREATE INDEX `idx_hutang_pembayaran_metode` ON `tbl_hutang_pembayaran`(`metode_bayar`);
