ALTER TABLE `tbl_pembayaran_produk`
    ADD COLUMN `snapshot_harga` DECIMAL(15,2) NULL AFTER `metadata`,
    ADD COLUMN `snapshot_hpp` DECIMAL(15,2) NULL AFTER `snapshot_harga`,
    ADD COLUMN `snapshot_detail_product` VARCHAR(200) NULL AFTER `snapshot_hpp`,
    ADD COLUMN `snapshot_qty_min` DECIMAL(15,2) NULL AFTER `snapshot_detail_product`,
    ADD COLUMN `snapshot_qty_max` DECIMAL(15,2) NULL AFTER `snapshot_qty_min`,
    ADD COLUMN `snapshot_ket_1` VARCHAR(50) NULL AFTER `snapshot_qty_max`,
    ADD COLUMN `snapshot_side_id` INT(11) NULL AFTER `snapshot_ket_1`,
    ADD COLUMN `snapshot_jenisprint_id` INT(11) NULL AFTER `snapshot_side_id`,
    ADD COLUMN `snapshot_subtotal` DECIMAL(15,2) NULL AFTER `snapshot_jenisprint_id`,
    ADD COLUMN `snapshot_source_harga_id` INT(11) NULL AFTER `snapshot_subtotal`,
    ADD COLUMN `snapshot_valid_from` DATETIME NULL AFTER `snapshot_source_harga_id`,
    ADD COLUMN `snapshot_valid_to` DATETIME NULL AFTER `snapshot_valid_from`;

ALTER TABLE `tbl_harga_product`
    MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ADD COLUMN `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`,
    ADD COLUMN `valid_from` DATETIME NULL AFTER `updated_at`,
    ADD COLUMN `valid_to` DATETIME NULL AFTER `valid_from`,
    ADD COLUMN `is_current` ENUM('1','0') NOT NULL DEFAULT '1' AFTER `valid_to`,
    ADD COLUMN `version_note` VARCHAR(255) NULL AFTER `is_current`;

UPDATE `tbl_harga_product`
SET
    `valid_from` = COALESCE(`valid_from`, `created_at`),
    `is_current` = COALESCE(`is_current`, '1')
WHERE 1;

CREATE INDEX `idx_tbl_pembayaran_produk_nospk_harga` ON `tbl_pembayaran_produk` (`nospk`, `harga_id`);
CREATE INDEX `idx_tbl_harga_product_product_current` ON `tbl_harga_product` (`product_id`, `is_current`, `valid_from`, `valid_to`);
