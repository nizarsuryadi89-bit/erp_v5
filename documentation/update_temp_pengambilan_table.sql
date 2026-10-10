-- Update tbl_temp_pengambilan table structure
-- Change bahan_id to material_id

-- First, backup the existing data
CREATE TABLE tbl_temp_pengambilan_backup AS SELECT * FROM tbl_temp_pengambilan;

-- Drop the existing table
DROP TABLE tbl_temp_pengambilan;

-- Create new table with material_id
CREATE TABLE `tbl_temp_pengambilan` (
  `temp_id` int(11) NOT NULL AUTO_INCREMENT,
  `material_id` int(11) NOT NULL,
  `qty` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`temp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add index for better performance
ALTER TABLE `tbl_temp_pengambilan` ADD INDEX `idx_material_id` (`material_id`);
