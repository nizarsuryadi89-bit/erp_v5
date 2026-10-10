<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_laporan extends CI_Model
{

    /**
     * Helper to get companies IDs for query
     * If 1 is selected, it returns [2, 3] for consolidated report
     */
    private function _get_ids($perusahaan_id)
    {
        if (is_array($perusahaan_id)) {
            return $perusahaan_id;
        }

        // If ID is 1 or empty/null, return all branches (Consolidated)
        if ($perusahaan_id == 1 || empty($perusahaan_id)) {
            // Fetch all active company IDs except the header company (ID 1)
            $companies = $this->db->select('id_perusahaan')->get_where('tbl_perusahaan', ['id_perusahaan >=' => 1])->result();
            $ids = [];
            foreach ($companies as $c) {
                $ids[] = $c->id_perusahaan;
            }
            return !empty($ids) ? $ids : [2, 3]; // Fallback to 2, 3 if table is empty
        }

        return [$perusahaan_id];
    }

    /**
     * Total pengeluaran bulanan dari tbl_pengeluaran_bulanan
     */
    public function total_pengeluaran_bulanan($perusahaan_id, $bulanini)
    {
        $ids = $this->_get_ids($perusahaan_id);
        $this->db->select_sum('pengeluaran_jumlah', 'pengeluaran_bulanan');
        $this->db->from('tbl_pengeluaran');
        $this->db->where_in('perusahaan_id', $ids);
        $this->db->where("DATE_FORMAT(pengeluaran_tgl, '%Y-%m')", $bulanini);
        return $this->db->get();
    }

    /**
     * Menghitung laba rugi berdasarkan detail pemesanan dan pembayaran
     */
    public function getLabaRugi($perusahaan_id, $bulanini)
    {
        $ids = implode(',', $this->_get_ids($perusahaan_id));
        $sql = "SELECT 
                    SUM(a.qty * (b.harga_1 - b.hpp)) AS laba,
                    SUM(a.qty * b.harga_1) AS total_pendapatan,
                    SUM(a.qty * b.hpp) AS total_hpp
                FROM tbl_pemesanan_detail a 
                    INNER JOIN tbl_harga_product b ON b.harga_id = a.harga_id
                    INNER JOIN tbl_pembayaran c ON a.nospk = c.nospk
                WHERE c.perusahaan_id IN ($ids) 
                    AND DATE_FORMAT(c.tgl_bayar, '%Y-%m') = " . $this->db->escape($bulanini);

        return $this->db->query($sql);
    }

    /**
     * Total pendapatan dari detail pemesanan dan pembayaran
     */
    public function total_pendapatan($perusahaan_id, $bulanini)
    {
        $ids = implode(',', $this->_get_ids($perusahaan_id));
        $sql = "SELECT SUM(a.qty * b.harga_1) AS total_pendapatan 
                FROM tbl_pemesanan_detail a 
                    INNER JOIN tbl_harga_product b ON b.harga_id = a.harga_id
                    INNER JOIN tbl_pembayaran c ON a.nospk = c.nospk
                WHERE c.perusahaan_id IN ($ids) 
                    AND DATE_FORMAT(c.tgl_bayar, '%Y-%m') = " . $this->db->escape($bulanini);

        return $this->db->query($sql);
    }

    /**
     * Total pengeluaran bulanan dari tbl_pengeluaran_bulanan
     */
    public function pengeluaran_bulanan($perusahaan_id, $bulanini)
    {
        $ids = implode(',', $this->_get_ids($perusahaan_id));
        $sql = "SELECT SUM(pengeluaran_jumlah) AS pengeluaran_bulanan 
                FROM tbl_pengeluaran 
                WHERE perusahaan_id IN ($ids) 
                    AND DATE_FORMAT(pengeluaran_tgl, '%Y-%m') = " . $this->db->escape($bulanini);

        return $this->db->query($sql);
    }

    /**
     * Total pemasukan bulanan dari tbl_pembayaran
     */
    public function pemasukan_bulanan($perusahaan_id, $bulanini)
    {
        $ids = implode(',', $this->_get_ids($perusahaan_id));
        $sql = "SELECT SUM(total_tagihan) AS total_pemasukan_bulanan 
                FROM tbl_pembayaran 
                WHERE perusahaan_id IN ($ids) 
                    AND DATE_FORMAT(tgl_bayar, '%Y-%m') = " . $this->db->escape($bulanini);

        return $this->db->query($sql);
    }

    /**
     * Total pengeluaran harian (hari ini) dari tbl_pengeluaran_harian
     */
    public function pengeluaran($perusahaan_id)
    {
        $ids = implode(',', $this->_get_ids($perusahaan_id));
        $sql = "SELECT SUM(pengeluaran_jumlah) AS pengeluaran 
                FROM tbl_pengeluaran 
                WHERE perusahaan_id IN ($ids) 
                    AND pengeluaran_tgl = CURDATE()";

        return $this->db->query($sql);
    }

    /**
     * Get pengeluaran harian untuk bulan tertentu
     */
    function getpengeluaranharian($perusahaan_id, $bulanini = null)
    {
        if ($bulanini === null) {
            $bulanini = date('Y-m');
        }

        $ids = $this->_get_ids($perusahaan_id);
        $this->db->select('p.*, kp.kategori_pengeluaran, kp.keterangan_pengeluaran');
        $this->db->from('tbl_pengeluaran p');
        $this->db->join('tbl_kategori_pengeluaran kp', 'kp.id = p.kategori_pengeluaran_id', 'left');
        $this->db->where_in('p.perusahaan_id', $ids);
        $this->db->where("DATE_FORMAT(p.pengeluaran_tgl, '%Y-%m') =", $bulanini);
        $this->db->order_by('p.pengeluaran_tgl', 'DESC');

        return $this->db->get();
    }

    function gettotalpengeluaran($perusahaan_id, $bulanini = null)
    {
        $ids = $this->_get_ids($perusahaan_id);
        $this->db->select('SUM(pengeluaran_jumlah) as total');

        $this->db->from('tbl_pengeluaran');
        $this->db->where_in('perusahaan_id', $ids);
        $this->db->where("DATE_FORMAT(pengeluaran_tgl, '%Y-%m') =", $bulanini);
        return $this->db->get();
    }

    /**
     * List detail pengeluaran bulanan
     */
    public function pengeluaran_bulanan_list($perusahaan_id, $bulanini)
    {
        $ids = implode(',', $this->_get_ids($perusahaan_id));
        $sql = "SELECT * 
                FROM tbl_pengeluaran 
                WHERE perusahaan_id IN ($ids) 
                    AND DATE_FORMAT(pengeluaran_tgl, '%Y-%m') = " . $this->db->escape($bulanini);

        return $this->db->query($sql);
    }

    /**
     * List detail pengeluaran harian untuk bulan tertentu
     */
    public function pengeluaran_harian_list($perusahaan_id, $bulanini)
    {
        $ids = implode(',', $this->_get_ids($perusahaan_id));
        $sql = "SELECT * 
                FROM tbl_pengeluaran 
                WHERE perusahaan_id IN ($ids) 
                    AND DATE_FORMAT(pengeluaran_tgl, '%Y-%m') = " . $this->db->escape($bulanini);

        return $this->db->query($sql);
    }

    /**
     * Total pengeluaran bulanan dari tbl_pengeluaran_harian untuk periode tertentu
     */
    public function pengeluaran_bulanan_period($perusahaan_id, $bulanini)
    {
        $ids = implode(',', $this->_get_ids($perusahaan_id));
        $sql = "SELECT SUM(pengeluaran_jumlah) AS pengeluaran_bulanan 
                FROM tbl_pengeluaran 
                WHERE perusahaan_id IN ($ids) 
                    AND DATE_FORMAT(pengeluaran_tgl, '%Y-%m') = " . $this->db->escape($bulanini);

        return $this->db->query($sql);
    }

    /**
     * Get rekap transaksi manual untuk bulan tertentu
     */
    public function get_rekap_transaksi_manual($perusahaan_id, $bulan)
    {
        $ids = $this->_get_ids($perusahaan_id);
        $this->db->select('p.*, k.konsumen_nama, k.konsumen_alamat, k.konsumen_nohp, 
                          pr.product_nama, pr.product_deskripsi,
                          pd.qty, pd.harga_id,
                          pb.total_tagihan, pb.total_bayar, pb.tgl_bayar, pb.jam_bayar, pb.status_lunas,
                          pbd.metode, pbd.referensi, pbd.keterangan,
                          r.nama_bank, r.no_rekening, r.atas_nama,
                          e.ewallet_nama,
                          me.edc_nama');
        $this->db->from('tbl_pemesanan p');
        $this->db->join('tbl_konsumen k', 'k.konsumen_id = p.konsumen_id');
        $this->db->join('tbl_pemesanan_detail pd', 'pd.nospk = p.nospk');
        $this->db->join('tbl_product pr', 'pr.product_id = pd.product_id');
        $this->db->join('tbl_pembayaran pb', 'pb.nospk = p.nospk', 'left');
        $this->db->join('tbl_pembayaran_detail pbd', 'pbd.pembayaran_id = pb.pembayaran_id', 'left');
        $this->db->join('tbl_rekening r', 'r.id = pbd.bank_id', 'left');
        $this->db->join('tbl_ewallet e', 'e.id = pbd.ewallet_id', 'left');
        $this->db->join('tbl_mesin_edc me', 'me.edc_id = pbd.edc_id', 'left');
        $this->db->where_in('p.perusahaan_id', $ids);
        $this->db->where('p.id_jenis_transaksi', 2); // manual transactions
        // $this->db->where("DATE_FORMAT(p.created_at, '%Y-%m')", $bulan);
        $this->db->order_by('p.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get Receivables Aging (Grouping unpaid invoices by age)
     */
    public function get_piutang_aging($perusahaan_id = null)
    {
        $ids = $this->_get_ids($perusahaan_id);

        $this->db->select("
            SUM(CASE WHEN DATEDIFF(CURRENT_DATE, DATE(created_at)) <= 15 THEN (total_tagihan - total_bayar) ELSE 0 END) as age_0_15,
            SUM(CASE WHEN DATEDIFF(CURRENT_DATE, DATE(created_at)) BETWEEN 16 AND 30 THEN (total_tagihan - total_bayar) ELSE 0 END) as age_16_30,
            SUM(CASE WHEN DATEDIFF(CURRENT_DATE, DATE(created_at)) > 30 THEN (total_tagihan - total_bayar) ELSE 0 END) as age_over_30
        ");
        $this->db->from('tbl_pembayaran');
        $this->db->where('status_lunas', '0');
        if (!empty($ids)) {
            $this->db->where_in('perusahaan_id', $ids);
        }

        return $this->db->get()->row();
    }

    /**
     * Get daily breakdown of Revenue and Expenses for a specific month
     */
    public function get_daily_laba_rugi($perusahaan_id, $bulanini)
    {
        $ids_arr = $this->_get_ids($perusahaan_id);
        $ids = implode(',', $ids_arr);

        // Get all dates in the month that have transactions or expenses
        // Note: Using placeholders for safety, but IN clause needs careful handling
        $sql = "
            SELECT 
                dates.date,
                COALESCE(SUM(pembayaran.total_tagihan), 0) as total_pemasukan,
                COALESCE(SUM(pengeluaran.pengeluaran_jumlah), 0) as total_pengeluaran
            FROM (
                SELECT DISTINCT tgl_bayar as date FROM tbl_pembayaran 
                WHERE perusahaan_id IN ($ids) AND DATE_FORMAT(tgl_bayar, '%Y-%m') = ?
                UNION
                SELECT DISTINCT pengeluaran_tgl as date FROM tbl_pengeluaran 
                WHERE perusahaan_id IN ($ids) AND DATE_FORMAT(pengeluaran_tgl, '%Y-%m') = ?
            ) as dates
            LEFT JOIN tbl_pembayaran pembayaran ON dates.date = pembayaran.tgl_bayar 
                AND pembayaran.perusahaan_id IN ($ids)
            LEFT JOIN tbl_pengeluaran pengeluaran ON dates.date = pengeluaran.pengeluaran_tgl 
                AND pengeluaran.perusahaan_id IN ($ids)
            GROUP BY dates.date
            ORDER BY dates.date ASC
        ";

        return $this->db->query($sql, array($bulanini, $bulanini));
    }

}
