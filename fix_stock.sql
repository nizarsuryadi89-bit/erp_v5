USE tgm_erp;

-- Normalize rak_id
UPDATE tbl_stok_gudang SET rak_id = NULL WHERE rak_id = '0' OR rak_id = '';

-- Merge duplicates
CREATE TEMPORARY TABLE temp_stok AS 
SELECT material_id, gudang_id, rak_id, SUM(qty) as total_qty, MIN(stok_id) as min_stok_id 
FROM tbl_stok_gudang 
GROUP BY material_id, gudang_id, IFNULL(rak_id, -1) 
HAVING COUNT(stok_id) > 1;

-- Update the primary row
UPDATE tbl_stok_gudang t
JOIN temp_stok s ON t.stok_id = s.min_stok_id
SET t.qty = s.total_qty;

-- Delete the duplicates
DELETE t FROM tbl_stok_gudang t
JOIN temp_stok s ON t.material_id = s.material_id AND t.gudang_id = s.gudang_id AND (t.rak_id = s.rak_id OR (t.rak_id IS NULL AND s.rak_id IS NULL))
WHERE t.stok_id != s.min_stok_id;

DROP TABLE temp_stok;
