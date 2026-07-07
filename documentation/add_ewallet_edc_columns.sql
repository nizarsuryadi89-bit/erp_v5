-- Add ewallet_id and edc_id columns to tbl_piutang_pembayaran table
ALTER TABLE `tbl_piutang_pembayaran` 
ADD COLUMN `ewallet_id` INT(11) NULL AFTER `rekening_id`,
ADD COLUMN `edc_id` INT(2) NULL AFTER `ewallet_id`,
ADD CONSTRAINT `fk_piutang_pembayaran_ewallet` 
FOREIGN KEY (`ewallet_id`) REFERENCES `tbl_ewallet`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
ADD CONSTRAINT `fk_piutang_pembayaran_edc` 
FOREIGN KEY (`edc_id`) REFERENCES `tbl_mesin_edc`(`edc_id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Add indexes for better performance
CREATE INDEX `idx_piutang_pembayaran_ewallet` ON `tbl_piutang_pembayaran`(`ewallet_id`);
CREATE INDEX `idx_piutang_pembayaran_edc` ON `tbl_piutang_pembayaran`(`edc_id`);
