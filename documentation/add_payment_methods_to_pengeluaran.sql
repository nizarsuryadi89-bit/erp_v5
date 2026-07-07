-- Add payment method columns to tbl_pengeluaran table
-- This script adds support for multiple payment methods in pengeluaran

-- Add metode_bayar column
ALTER TABLE `tbl_pengeluaran` 
ADD COLUMN `metode_bayar` ENUM('tunai','transfer','debit','kartu_kredit','ewallet','lainnya') NULL AFTER `pengeluaran_penerima`;

-- Add rekening_tujuan column for transfer payments
ALTER TABLE `tbl_pengeluaran` 
ADD COLUMN `rekening_tujuan` VARCHAR(255) NULL AFTER `metode_bayar`;

-- Add ewallet_id column for e-wallet payments
ALTER TABLE `tbl_pengeluaran` 
ADD COLUMN `ewallet_id` INT(11) NULL AFTER `rekening_tujuan`;

-- Add edc_id column for debit/credit card payments
ALTER TABLE `tbl_pengeluaran` 
ADD COLUMN `edc_id` INT(2) NULL AFTER `ewallet_id`;

-- Add foreign key constraints
ALTER TABLE `tbl_pengeluaran` 
ADD CONSTRAINT `fk_pengeluaran_ewallet` 
FOREIGN KEY (`ewallet_id`) REFERENCES `tbl_ewallet`(`id`) 
ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `tbl_pengeluaran` 
ADD CONSTRAINT `fk_pengeluaran_edc` 
FOREIGN KEY (`edc_id`) REFERENCES `tbl_mesin_edc`(`edc_id`) 
ON DELETE SET NULL ON UPDATE CASCADE;

-- Add indexes for better performance
CREATE INDEX `idx_pengeluaran_metode_bayar` ON `tbl_pengeluaran`(`metode_bayar`);
CREATE INDEX `idx_pengeluaran_ewallet` ON `tbl_pengeluaran`(`ewallet_id`);
CREATE INDEX `idx_pengeluaran_edc` ON `tbl_pengeluaran`(`edc_id`);

-- Update existing records to have default metode_bayar
UPDATE `tbl_pengeluaran` SET `metode_bayar` = 'tunai' WHERE `metode_bayar` IS NULL;
