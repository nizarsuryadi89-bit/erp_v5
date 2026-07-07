-- Add konsumen_id column to tbl_queue
-- Run this SQL in phpMyAdmin or MySQL client

ALTER TABLE `tbl_queue` 
ADD COLUMN `konsumen_id` INT NULL AFTER `branch_id`,
ADD INDEX `idx_konsumen` (`konsumen_id`);

-- Optional: Add foreign key constraint
-- ALTER TABLE `tbl_queue` 
-- ADD CONSTRAINT `fk_queue_konsumen` 
-- FOREIGN KEY (`konsumen_id`) REFERENCES `tbl_konsumen`(`konsumen_id`) 
-- ON DELETE SET NULL ON UPDATE CASCADE;
