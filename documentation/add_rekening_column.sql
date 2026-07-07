-- Add rekening_id column to tbl_piutang_pembayaran table
ALTER TABLE `tbl_piutang_pembayaran` 
ADD COLUMN `rekening_id` INT(4) NULL AFTER `metode_bayar`,
ADD CONSTRAINT `fk_piutang_pembayaran_rekening` 
FOREIGN KEY (`rekening_id`) REFERENCES `tbl_rekening`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Add index for better performance
CREATE INDEX `idx_piutang_pembayaran_rekening` ON `tbl_piutang_pembayaran`(`rekening_id`);
