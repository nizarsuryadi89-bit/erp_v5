TOTAL SPK QUERY: SELECT COUNT(DISTINCT tbl_produksi.nospk) as total_filtered_spk
FROM `tbl_produksi`
LEFT JOIN `tbl_product` ON `tbl_product`.`product_id` = `tbl_produksi`.`product_id`
LEFT JOIN `tbl_pemesanan` ON `tbl_pemesanan`.`nospk` = `tbl_produksi`.`nospk`
WHERE   (
`tbl_product`.`tipe_id` IN(1, 2)
OR    (
`tbl_product`.`kategori_id` IN(1, 2, 3, 4, 5, 8, 9, 10, 16, 22)
AND `tbl_product`.`tipe_id` = 3
  )
 )
AND `tbl_produksi`.`status_produksi` != 'on_costumer'
AND `tbl_produksi`.`status_produksi` IS NOT NULL
AND `tbl_pemesanan`.`status_pembayaran` = 'paid'
AND   (
EXISTS (
                    SELECT 1 
                    FROM detail_produksi dp 
                    JOIN routing r ON r.routing_id = dp.routing_id 
                    WHERE dp.produksi_id = tbl_produksi.produksi_id 
                    AND dp.status_produksi != '2' 
                    AND r.operator_id = 19
                )
 )
AND DATE(tbl_pemesanan.created_at) >= '2026-06-25'
AND DATE(tbl_pemesanan.created_at) <= '2026-07-02'
