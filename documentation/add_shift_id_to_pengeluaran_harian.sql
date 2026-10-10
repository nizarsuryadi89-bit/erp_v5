-- Add shift_id field to tbl_pengeluaran_harian table
ALTER TABLE `tbl_pengeluaran_harian` 
ADD COLUMN `shift_id` INT(11) NULL AFTER `user_id`,
ADD INDEX `idx_shift_id` (`shift_id`),
ADD CONSTRAINT `fk_pengeluaran_harian_shift` 
FOREIGN KEY (`shift_id`) REFERENCES `kasir_shift`(`shift_id`) 
ON DELETE SET NULL ON UPDATE CASCADE;

-- Update existing records to link with current shift if possible
UPDATE `tbl_pengeluaran_harian` ph
LEFT JOIN `kasir_shift` ks ON ph.user_id = ks.user_id 
    AND ph.pengeluaran_tgl = ks.tanggal_shift
    AND ph.pengeluaran_tgl >= DATE(ks.jam_open)
    AND (ks.jam_close IS NULL OR ph.pengeluaran_tgl <= DATE(ks.jam_close))
SET ph.shift_id = ks.shift_id
WHERE ph.shift_id IS NULL;
