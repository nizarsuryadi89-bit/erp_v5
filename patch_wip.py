import re
import sys

with open('application/controllers/Wip.php', 'r') as f:
    content = f.read()

# 1. Add _apply_produksi_filters above produksi()
filter_func = """
    private function _apply_produksi_filters($filter_status_view, $active_operator, $is_admin, $filter_cabang, $perusahaan)
    {
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_produksi.konsumen_id', 'left');
        $this->db->join('tbl_product', 'tbl_product.product_id = tbl_produksi.product_id', 'left');
        $this->db->join('tbl_kategori', 'tbl_kategori.kategori_id = tbl_product.kategori_id', 'left');
        $this->db->join('tbl_subkategori', 'tbl_subkategori.subkategori_id = tbl_product.subkategori_id', 'left');
        $this->db->join('tbl_satuan', 'tbl_satuan.satuan_id = tbl_product.satuan_id', 'left');
        $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_produksi.perusahaan_id', 'left');
        $this->db->join('tbl_pemesanan', 'tbl_pemesanan.nospk = tbl_produksi.nospk', 'left');
        $this->db->join('tbl_harga_product', 'tbl_harga_product.harga_id = tbl_produksi.harga_id', 'left');
        $this->db->join('tbl_jenis_transaksi', 'tbl_jenis_transaksi.id_jenis_transaksi = tbl_pemesanan.id_jenis_transaksi', 'left');

        $this->db->group_start();
        $this->db->where_in('tbl_product.tipe_id', [1, 2]);
        $this->db->or_group_start();
        $this->db->where_in('tbl_product.kategori_id', [3, 10, 16]);
        $this->db->where('tbl_product.tipe_id', 3);
        $this->db->group_end();
        $this->db->group_end();

        if ($filter_status_view === 'pb') {
            $this->db->where('tbl_produksi.status_produksi', 'on_pb');
            $this->db->where('tbl_produksi.tgl_kirim_pb >=', date('Y-m-d 00:00:00', strtotime('-3 days')));
        } elseif ($filter_status_view === 'ready_pb') {
            $this->db->where('tbl_produksi.status_produksi', 'done');
        } elseif ($filter_status_view === 'proses') {
            $this->db->where('tbl_produksi.status_produksi', 'proses');
        } elseif ($filter_status_view === 'pending') {
            $this->db->group_start();
            $this->db->where('tbl_produksi.status_produksi', 'pending');
            $this->db->or_where('tbl_produksi.status_produksi', 'approved');
            $this->db->or_where('tbl_produksi.status_produksi', '');
            $this->db->or_where('tbl_produksi.status_produksi', '0');
            $this->db->group_end();
        } else {
            $this->db->where('tbl_produksi.status_produksi !=', 'on_pb');
            $this->db->where('tbl_produksi.status_produksi !=', 'on_costumer');
            $this->db->where('tbl_produksi.status_produksi !=', 'done');
        }
        $this->db->where('tbl_produksi.status_produksi IS NOT NULL');
        $this->db->where('tbl_pemesanan.status_pembayaran', 'paid');

        if ($active_operator !== 'all') {
            if ($filter_status_view !== 'ready_pb' && $filter_status_view !== 'pb') {
                $this->db->group_start();
                $this->db->where("(
                    SELECT r.operator_id 
                    FROM detail_produksi dp 
                    JOIN routing r ON r.routing_id = dp.routing_id 
                    WHERE dp.produksi_id = tbl_produksi.produksi_id AND dp.status_produksi != '2' 
                    ORDER BY dp.routing_urutan ASC 
                    LIMIT 1
                ) =", (int) $active_operator);
                $this->db->group_end();
            } else {
                $this->db->group_start();
                $this->db->where("(
                    SELECT r.operator_id 
                    FROM detail_produksi dp 
                    JOIN routing r ON r.routing_id = dp.routing_id 
                    WHERE dp.produksi_id = tbl_produksi.produksi_id 
                    ORDER BY dp.routing_urutan DESC 
                    LIMIT 1
                ) =", (int) $active_operator);
                $this->db->group_end();
            }
        }

        if (!$is_admin) {
            $this->db->group_start();
            $this->db->where('tbl_produksi.lokasi_cetak', $perusahaan);
            $this->db->or_group_start();
            $this->db->where("(tbl_produksi.lokasi_cetak IS NULL OR tbl_produksi.lokasi_cetak = '' OR tbl_produksi.lokasi_cetak = '0')", NULL, FALSE);
            $this->db->where('tbl_produksi.perusahaan_id', $perusahaan);
            $this->db->group_end();
            $this->db->group_end();
        } elseif ($is_admin && !empty($filter_cabang) && $filter_cabang !== 'all') {
            $this->db->group_start();
            $this->db->where('tbl_produksi.lokasi_cetak', $filter_cabang);
            $this->db->or_group_start();
            $this->db->where("(tbl_produksi.lokasi_cetak IS NULL OR tbl_produksi.lokasi_cetak = '' OR tbl_produksi.lokasi_cetak = '0')", NULL, FALSE);
            $this->db->where('tbl_produksi.perusahaan_id', $filter_cabang);
            $this->db->group_end();
            $this->db->group_end();
        }
    }

    public function produksi()
"""

content = content.replace("    public function produksi()", filter_func)

# 2. Replace the filtering logic in produksi() with the method call
regex_filter = re.compile(r"\$this->db->join\('tbl_konsumen'.*?\$filter_cabang = \$this->input->get\('cabang'\);", re.DOTALL)
# wait, the order of defining variables like filter_status_view and active_operator is important.

# Let's write a safer replace strategy.
