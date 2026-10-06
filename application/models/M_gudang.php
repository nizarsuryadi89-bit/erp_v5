<?php
class M_gudang extends CI_model
{
    function getBahan($kode)
    {
        $this->db->where('tbl_bahan.bahan_id', $kode);
        $this->db->where('gudang_id', '1');
        $this->db->join('stok_gudang', 'tbl_bahan.bahan_id = stok_gudang.variant_id');
        $this->db->group_by('tbl_bahan.bahan_id');
        return $this->db->get('tbl_bahan');
    }

    function getAllBahan()
    {
        $this->db->join('stok_gudang', 'tbl_bahan.bahan_id = stok_gudang.variant_id');
        $this->db->join('tbl_satuan', 'tbl_bahan.satuan_id = tbl_satuan.satuan_id');
        return $this->db->get('tbl_bahan');
    }

    /**
     * Get Master Data Bahan (without stock)
     * Added to resolve IDE warnings in Warehouse controller
     */
    public function getDataBahan()
    {
        $this->db->join('tbl_satuan as satuan_produk', 'tbl_bahan.satuan_id = satuan_produk.satuan_id');
        $this->db->select('tbl_bahan.*, satuan_produk.satuan_nama as satuan_nama');
        $this->db->order_by('bahan_id', 'asc');
        return $this->db->get('tbl_bahan');
    }

    function getBahansatuan()
    {
        $this->db->join('tbl_satuan', 'tbl_bahan.satuan_id = tbl_satuan.satuan_id');
        $this->db->join('tbl_material', 'tbl_bahan.bahan_id = tbl_material.bahan_id');
        return $this->db->get('tbl_bahan');
    }

    function getBahansatuangudang()
    {
        $this->db->join('tbl_satuan', 'tbl_material.satuan_gudang_id = tbl_satuan.satuan_id');
        return $this->db->get('tbl_material');
    }

    function stokjasa($perusahaan)
    {
        $this->db->where('perusahaan_id', $perusahaan);
        $this->db->where('approve', '0');
        return $this->db->get('tbl_pengambilan');
    }

    function stok_minim()
    {
        return $this->db->query("SELECT * FROM stok_gudang a inner join 
                                        tbl_bahan b on a.variant_id = b.bahan_id inner join
                                            tbl_satuan c on b.satuan_id = c.satuan_id
                                               ");
    }

    function getSonDetail($id)
    {
        $this->db->where('son_id', $id);
        $this->db->join('tbl_material', 'tbl_material.material_id = tbl_stok_opname.material_id');
        $this->db->join('tbl_bahan', 'tbl_bahan.bahan_id = tbl_material.bahan_id');
        $this->db->join('tbl_satuan', 'tbl_material.satuan_gudang_id = tbl_satuan.satuan_id');

        return $this->db->get('tbl_stok_opname');
    }

    function stokjasa_masuk($perusahaan)
    {
        $this->db->where('tbl_pengambilan.perusahaan_id', $perusahaan);
        $this->db->where('tbl_pengambilan_detail.approve', '0');
        $this->db->join('tbl_pengambilan_detail', 'tbl_pengambilan_detail.pengambilan_id = tbl_pengambilan.pengambilan_id');
        $this->db->join('tbl_bahan', 'tbl_bahan.bahan_id = tbl_pengambilan_detail.bahan_id');
        $this->db->join('tbl_stok_bahan', 'tbl_stok_bahan.bahan_id = tbl_bahan.bahan_id');
        return $this->db->get('tbl_pengambilan');
    }

    function getStokgudang()
    {
        $this->db->join('tbl_material', 'tbl_material.material_id = tbl_stok_gudang.material_id');
        $this->db->join('tbl_satuan', 'tbl_material.satuan_gudang_id = tbl_satuan.satuan_id');
        $this->db->group_by('tbl_material.material_id');
        return $this->db->get('tbl_stok_gudang');
    }




    function getAllBahanbaku()
    {
        $this->db->select('tbl_bahan.*, tbl_satuan.satuan_nama, 
            (SELECT COUNT(material_id) FROM tbl_material WHERE tbl_material.bahan_id = tbl_bahan.bahan_id) as jml_varian,
            (SELECT pd.harga_beli FROM tbl_pembelian_detail pd JOIN tbl_pembelian p ON pd.pembelian_id = p.pembelian_id JOIN tbl_material m ON pd.material_id = m.material_id WHERE m.bahan_id = tbl_bahan.bahan_id ORDER BY p.pembelian_tgl DESC LIMIT 1) as harga_beli,
            (SELECT AVG(pd.harga_beli) FROM tbl_pembelian_detail pd JOIN tbl_material m ON pd.material_id = m.material_id WHERE m.bahan_id = tbl_bahan.bahan_id) as rata_rata_beli
        ');
        $this->db->from('tbl_bahan');
        $this->db->join('tbl_satuan', 'tbl_bahan.satuan_id = tbl_satuan.satuan_id', 'left');
        return $this->db->get();
    }


    function getGudangBahan()
    {
        $this->db->join('tbl_bahan', 'tbl_bahan.bahan_id = stok_gudang.variant_id');
        $this->db->join('tbl_satuan', 'tbl_bahan.satuan_id = tbl_satuan.satuan_id');
        return $this->db->get('stok_gudang');
    }

    function getdatabyId($id)
    {
        $this->db->where('bahan_id', $id);
        return $this->db->get('tbl_bahan');
    }

    function updateData($table, $id, $update)
    {
        $this->db->where($id);
        return $this->db->update($table, $update);
    }

    function getTemp($table)
    {
        if ($table == 'tbl_temp_beli') {
            $this->db->join('tbl_material', "$table.material_id = tbl_material.material_id", 'left');
            $this->db->join('tbl_satuan', 'tbl_material.satuan_gudang_id = tbl_satuan.satuan_id', 'left');
            $this->db->join('tbl_bahan', 'tbl_material.bahan_id = tbl_bahan.bahan_id', 'left');
            $this->db->join('tbl_rak', "$table.rak_id = tbl_rak.rak_id", 'left');
        } else {
            $this->db->join('tbl_bahan', "$table.bahan_id = tbl_bahan.bahan_id", 'left');
            $this->db->join('tbl_satuan', 'tbl_bahan.satuan_id = tbl_satuan.satuan_id', 'left');
        }

        return $this->db->get($table);
    }

    function deleteData($table, $id)
    {
        $this->db->where($id);
        return $this->db->delete($table);
    }

    function getLastId($table, $field)
    {
        $this->db->select_max($field);
        $result = $this->db->get($table);

        // If table is empty or no records, return 0
        if ($result->num_rows() == 0 || $result->row()->$field === null) {
            return $this->db->select("0 as $field")->get($table);
        }

        return $result;
    }

    function getStok($material)
    {
        $this->db->where('material_id', $material);
        $this->db->where('gudang_id', 1);
        return $this->db->get('tbl_stok_gudang');
    }

    function getStokBahan($bahan)
    {
        $this->db->where('bahan_id', $bahan);
        return $this->db->get('tbl_stok_bahan');
    }

    function harga_max($bahan)
    {
        return $this->db->query("SELECT max(pemb_harga) FROM tbl_pembelian_detail WHERE bahan_id = $bahan");
    }

    function getDataPembelian()
    {
        // $perusahaan = $this->session->userdata('perusahaan_id');
        // $this->db->where('perusahaan_id', $perusahaan);
        $this->db->join('tbl_supplier', 'tbl_pembelian.supplier_id = tbl_supplier.supplier_id');
        return $this->db->get('tbl_pembelian');
    }

    // function getdetailpembelian(){
    //     return $this->db->get('tbl_pembelian_detail');
    // }

    function getDataPengambilan()
    {
        $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = tbl_pengambilan.karyawan_id');
        $this->db->join('tbl_gudang', 'tbl_gudang.gudang_id = tbl_pengambilan.gudang_id');
        return $this->db->get('tbl_pengambilan');
    }

    function getPengambilanWithDetails($month = null, $year = null)
    {
        // Menggunakan query yang lebih eksplisit untuk memastikan perhitungan yang akurat
        $this->db->select('tbl_pengambilan.*, tbl_karyawan.nama_lengkap, tbl_gudang.gudang_nama, tbl_gudang.gudang_lokasi');

        // Gunakan subquery untuk menghitung jumlah item
        $this->db->select('(
                SELECT COUNT(*) 
                FROM tbl_pengambilan_detail 
                WHERE tbl_pengambilan_detail.pengambilan_id = tbl_pengambilan.pengambilan_id
            ) as item_count', FALSE);

        // Gunakan subquery untuk menghitung total qty
        $this->db->select('(
                SELECT IFNULL(SUM(qty), 0) 
                FROM tbl_pengambilan_detail 
                WHERE tbl_pengambilan_detail.pengambilan_id = tbl_pengambilan.pengambilan_id
            ) as qty_total', FALSE);

        // Cek apakah kolom approve ada di tabel tbl_pengambilan_detail
        // Jika tidak ada, gunakan 0 sebagai nilai default untuk pending_count
        $this->db->select('0 as pending_count', FALSE);

        $this->db->from('tbl_pengambilan');
        $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = tbl_pengambilan.karyawan_id', 'left');
        $this->db->join('tbl_gudang', 'tbl_gudang.gudang_id = tbl_pengambilan.gudang_id', 'left');

        if ($month !== null) {
            $this->db->where('MONTH(tbl_pengambilan.pengambilan_tgl)', $month);
        }
        if ($year !== null) {
            $this->db->where('YEAR(tbl_pengambilan.pengambilan_tgl)', $year);
        }
        $this->db->order_by('pengambilan_tgl', 'desc');
        return $this->db->get();
    }

    public function getItemCountPengambilan()
    {
        // Query untuk menghitung jumlah item per pengambilan_id
        $this->db->select('pengambilan_id, COUNT(*) AS item_count');
        $this->db->from('tbl_pengambilan_detail');
        $this->db->group_by('pengambilan_id'); // Mengelompokkan berdasarkan pengambilan_id
        $query = $this->db->get(); // Mengambil data dari tabel detail pengambilan
        return $query; // Mengembalikan hasil query
    }

    function getDataStokOpname()
    {
        $bulanini = date('Y-m');
        $this->db->select('tbl_stok_opname.*, tbl_material.varian, tbl_material.konversi, tbl_bahan.bahan_nama, s.satuan_nama, sp.satuan_nama as satuan_produksi, g.gudang_nama');
        $this->db->like('son_tgl', $bulanini);
        $this->db->join('tbl_material', 'tbl_material.material_id = tbl_stok_opname.material_id');
        $this->db->join('tbl_bahan', 'tbl_bahan.bahan_id = tbl_material.bahan_id');
        $this->db->join('tbl_satuan s', 'tbl_material.satuan_gudang_id = s.satuan_id', 'left');
        $this->db->join('tbl_satuan sp', 'tbl_bahan.satuan_id = sp.satuan_id', 'left');
        $this->db->join('tbl_gudang g', 'g.gudang_id = tbl_stok_opname.gudang_id', 'left');
        return $this->db->get('tbl_stok_opname');
    }



    function getdetailPembelian($id)
    {
        $this->db->join('tbl_pembelian', 'tbl_pembelian_detail.pembelian_id = tbl_pembelian.pembelian_id', 'left');
        $this->db->join('tbl_supplier', 'tbl_pembelian.supplier_id = tbl_supplier.supplier_id', 'left');
        $this->db->join('tbl_material', 'tbl_pembelian_detail.material_id = tbl_material.material_id', 'left');
        $this->db->join('tbl_bahan', 'tbl_material.bahan_id = tbl_bahan.bahan_id', 'left');
        $this->db->where('tbl_pembelian_detail.pembelian_id', $id);
        return $this->db->get('tbl_pembelian_detail');
    }

    function getdetailPengambilan($id)
    {
        $this->db->join('tbl_pengambilan', 'tbl_pengambilan_detail.pengambilan_id = tbl_pengambilan.pengambilan_id', 'left');
        $this->db->join('tbl_karyawan', 'tbl_pengambilan.karyawan_id = tbl_karyawan.karyawan_id', 'left');
        $this->db->join('tbl_material', 'tbl_pengambilan_detail.material_id = tbl_material.material_id', 'left');
        $this->db->join('tbl_bahan', 'tbl_material.bahan_id = tbl_bahan.bahan_id', 'left');
        $this->db->join('tbl_satuan', 'tbl_bahan.satuan_id = tbl_satuan.satuan_id', 'left');
        $this->db->join('tbl_satuan as satuan_gudang', 'tbl_material.satuan_gudang_id = satuan_gudang.satuan_id', 'left');
        $this->db->select('tbl_pengambilan_detail.*, tbl_pengambilan.*, tbl_karyawan.nama_lengkap, tbl_karyawan.telepon, tbl_karyawan.email, tbl_material.*, tbl_bahan.*, tbl_satuan.satuan_nama as satuan_nama_dasar, satuan_gudang.satuan_nama as nama_satuan_gudang');
        $this->db->where('tbl_pengambilan_detail.pengambilan_id', $id);
        return $this->db->get('tbl_pengambilan_detail');
    }

    function getbelibahan()
    {

        $this->db->join('tbl_supplier', 'tbl_pembelian.supplier_id = tbl_supplier.supplier_id', 'left');
        $this->db->order_by('tbl_pembelian.pembelian_tgl', 'DESC');

        return $this->db->get('tbl_pembelian');
    }

    function getLaporanPembelian()
    {
        $this->db->select('tbl_pembelian.*, tbl_supplier.supplier_nama, tbl_supplier.supplier_alamat, tbl_supplier.supplier_telp');
        $this->db->join('tbl_supplier', 'tbl_pembelian.supplier_id = tbl_supplier.supplier_id');
        $this->db->order_by('pembelian_tgl', 'DESC');
        return $this->db->get('tbl_pembelian');
    }

    function getPembelianByDate($date)
    {
        $this->db->select('tbl_pembelian.*, tbl_supplier.supplier_nama, tbl_supplier.supplier_alamat, tbl_supplier.supplier_telp');
        $this->db->join('tbl_supplier', 'tbl_pembelian.supplier_id = tbl_supplier.supplier_id');
        $this->db->where('DATE(pembelian_tgl)', $date);
        $this->db->order_by('pembelian_tgl', 'DESC');
        $result = $this->db->get('tbl_pembelian');

        // Calculate total for each pembelian by summing details
        $pembelian_data = $result->result();
        foreach ($pembelian_data as $pembelian) {
            $detail_total = $this->db->select('SUM(qty * harga_beli) as total')
                ->where('pembelian_id', $pembelian->pembelian_id)
                ->get('tbl_pembelian_detail')->row();
            $pembelian->pembelian_total = $detail_total ? $detail_total->total : 0;
        }

        return $pembelian_data;
    }

    function getPembelianDetailByDate($date)
    {
        $this->db->select('
            tbl_pembelian_detail.*, 
            tbl_pembelian_detail.qty as qty, 
            tbl_pembelian_detail.harga_beli as harga_beli,
            tbl_pembelian.pembelian_tgl, 
            tbl_pembelian.no_faktur, 
            tbl_supplier.supplier_nama, 
            tbl_material.varian as varian, 
            tbl_satuan.satuan_nama as satuan_nama, 
            tbl_bahan.bahan_nama as bahan_nama
        ');
        $this->db->join('tbl_pembelian', 'tbl_pembelian_detail.pembelian_id = tbl_pembelian.pembelian_id');
        $this->db->join('tbl_supplier', 'tbl_pembelian.supplier_id = tbl_supplier.supplier_id');
        $this->db->join('tbl_material', 'tbl_pembelian_detail.material_id = tbl_material.material_id');
        $this->db->join('tbl_bahan', 'tbl_material.bahan_id = tbl_bahan.bahan_id', 'left');
        $this->db->join('tbl_satuan', 'tbl_material.satuan_gudang_id = tbl_satuan.satuan_id');
        $this->db->where('DATE(tbl_pembelian.pembelian_tgl)', $date);
        $this->db->order_by('tbl_pembelian.pembelian_tgl', 'DESC');
        return $this->db->get('tbl_pembelian_detail');
    }

    function getPembelianCalendarData($year, $month)
    {
        // Use a more complex query to calculate totals from details
        $this->db->select('DATE(p.pembelian_tgl) as tanggal, COUNT(DISTINCT p.pembelian_id) as jumlah_transaksi, SUM(pd.qty * pd.harga_beli) as total_nilai');
        $this->db->from('tbl_pembelian p');
        $this->db->join('tbl_pembelian_detail pd', 'p.pembelian_id = pd.pembelian_id', 'left');
        $this->db->where('YEAR(p.pembelian_tgl)', $year);
        $this->db->where('MONTH(p.pembelian_tgl)', $month);
        $this->db->group_by('DATE(p.pembelian_tgl)');
        return $this->db->get();
    }

    function getbahanPembelian($id)
    {
        $bulanini = date('Y-m');
        // print_r($bulanini);
        $this->db->join('tbl_pembelian', 'tbl_pembelian_detail.pembelian_id = tbl_pembelian.pembelian_id');
        $this->db->join('tbl_supplier', 'tbl_pembelian.supplier_id = tbl_supplier.supplier_id');
        $this->db->join('tbl_material', 'tbl_pembelian_detail.material_id = tbl_material.material_id');
        $this->db->where('tbl_pembelian_detail.material_id', $id);
        $this->db->like('pembelian_tgl', $bulanini);
        return $this->db->get('tbl_pembelian_detail');
    }

    function getbahanPengambilan($id)
    {
        $bulanini = date('Y-m');
        // print_r($bulanini);

        $this->db->join('tbl_pengambilan', 'tbl_pengambilan_detail.pengambilan_id = tbl_pengambilan.pengambilan_id');
        $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_pengambilan.perusahaan_id');
        $this->db->join('tbl_karyawan', 'tbl_pengambilan.karyawan_id = tbl_karyawan.karyawan_id');
        $this->db->join('tbl_material', 'tbl_pengambilan_detail.material_id = tbl_material.material_id');
        $this->db->where('tbl_pengambilan_detail.material_id', $id);
        $this->db->like('pengambilan_tgl', $bulanini);
        return $this->db->get('tbl_pengambilan_detail');
    }

    function search_bahan($title)
    {
        $this->db->like('barcode', $title, 'both');
        $this->db->order_by('blog_title', 'ASC');
        $this->db->limit(10);
        return $this->db->get('blog')->result();
    }

    function cekSon($material_id, $son_tgl)
    {
        $this->db->where('material_id', $material_id);
        $this->db->like('son_tgl', $son_tgl);
        return $this->db->get('tbl_stok_opname');
    }

    function getMonitoringStok()
    {
        return $this->db->query("
                SELECT 
                    pd.bahan_id,
                    s.satuan_nama,
                    b.bahan_nama,
                    SUM(pd.qty) as total_masuk,
                    SUM(pd2.qty) as total_keluar,
                    (SUM(pd.qty) - SUM(pd2.qty)) as stok_akhir
                FROM tbl_pembelian_detail pd
                LEFT JOIN tbl_pengambilan_detail pd2 ON pd.bahan_id = pd2.bahan_id
                JOIN tbl_bahan b ON pd.bahan_id = b.bahan_id
                JOIN tbl_satuan s ON b.satuan_id = s.satuan_id
                GROUP BY pd.bahan_id
            ");
    }

    function getMonitoringTransaksi()
    {
        // Get pembelian transactions
        $pembelian = $this->db->select('
                pd.detail_id as id,
                pd.material_id,
                pd.qty as qty_masuk,
                0 as qty_keluar,
                p.pembelian_tgl as tanggal,
                p.pembelian_tgl as waktu,
                "Pembelian" as tipe,
                m.varian,
                m.barcode,
                s.satuan_nama,
                COALESCE(k.nama_lengkap, "System") as nama_karyawan,
                sup.supplier_nama as nama_supplier,
                p.pembelian_id,
                0 as pengambilan_id
            ')
            ->from('tbl_pembelian_detail pd')
            ->join('tbl_pembelian p', 'pd.pembelian_id = p.pembelian_id')
            ->join('tbl_material m', 'pd.material_id = m.material_id')
            ->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id')
            ->join('tbl_supplier sup', 'p.supplier_id = sup.supplier_id')
            ->join('tbl_karyawan k', 'p.karyawan_id = k.karyawan_id', 'left')
            ->get()->result();

        // Get pengambilan transactions
        $pengambilan = $this->db->select('
                pd.detail_id as id,
                pd.material_id,
                0 as qty_masuk,
                pd.qty as qty_keluar,
                p.pengambilan_tgl as tanggal,
                p.pengambilan_tgl as waktu,
                "Pengambilan" as tipe,
                m.varian,
                m.barcode,
                s.satuan_nama,
                COALESCE(k.nama_lengkap, "System") as nama_karyawan,
                COALESCE(g.gudang_nama, "Gudang Utama") as nama_supplier,
                0 as pembelian_id,
                p.pengambilan_id
            ')
            ->from('tbl_pengambilan_detail pd')
            ->join('tbl_pengambilan p', 'pd.pengambilan_id = p.pengambilan_id')
            ->join('tbl_material m', 'pd.material_id = m.material_id')
            ->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id')
            ->join('tbl_karyawan k', 'p.karyawan_id = k.karyawan_id', 'left')
            ->join('tbl_gudang g', 'p.gudang_id = g.gudang_id', 'left')
            ->get()->result();

        // Get return transactions
        $return = $this->db->select('
                rd.return_detail_id as id,
                rd.material_id,
                0 as qty_masuk,
                rd.qty_return as qty_keluar,
                r.return_tgl as tanggal,
                r.return_tgl as waktu,
                "Return" as tipe,
                m.varian,
                m.barcode,
                s.satuan_nama,
                COALESCE(k.nama_lengkap, "System") as nama_karyawan,
                sup.supplier_nama as nama_supplier,
                0 as pembelian_id,
                0 as pengambilan_id
            ')
            ->from('tbl_return_pembelian_detail rd')
            ->join('tbl_return_pembelian r', 'rd.return_id = r.return_id')
            ->join('tbl_material m', 'rd.material_id = m.material_id')
            ->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id')
            ->join('tbl_supplier sup', 'r.supplier_id = sup.supplier_id')
            ->join('tbl_karyawan k', 'r.karyawan_id = k.karyawan_id', 'left')
            ->get()->result();

        // Combine all transactions
        $all_transactions = array_merge($pembelian, $pengambilan, $return);

        // Sort by date descending
        usort($all_transactions, function ($a, $b) {
            return strtotime($b->tanggal) - strtotime($a->tanggal);
        });

        return $all_transactions;
    }

    // Return Pembelian Functions
    function getDataReturnPembelian()
    {
        $this->db->join('tbl_supplier', 'tbl_return_pembelian.supplier_id = tbl_supplier.supplier_id');
        $this->db->join('tbl_karyawan', 'tbl_return_pembelian.karyawan_id = tbl_karyawan.karyawan_id');
        $this->db->order_by('return_tgl', 'desc');
        return $this->db->get('tbl_return_pembelian');
    }

    function getDetailReturnPembelian($id)
    {
        $this->db->join('tbl_return_pembelian', 'tbl_return_pembelian_detail.return_id = tbl_return_pembelian.return_id');
        $this->db->join('tbl_supplier', 'tbl_return_pembelian.supplier_id = tbl_supplier.supplier_id');
        $this->db->join('tbl_bahan', 'tbl_return_pembelian_detail.bahan_id = tbl_bahan.bahan_id');
        $this->db->join('tbl_satuan', 'tbl_bahan.satuan_id = tbl_satuan.satuan_id');
        $this->db->where('tbl_return_pembelian_detail.return_id', $id);
        return $this->db->get('tbl_return_pembelian_detail');
    }

    function getPembelianForReturn()
    {
        $this->db->join('tbl_supplier', 'tbl_pembelian.supplier_id = tbl_supplier.supplier_id');
        $this->db->where('pembelian_tgl >=', date('Y-m-d', strtotime('-30 days')));
        $this->db->order_by('pembelian_tgl', 'desc');
        return $this->db->get('tbl_pembelian');
    }

    function getPembelianDetailForReturn($pembelian_id)
    {
        $this->db->join('tbl_bahan', 'tbl_pembelian_detail.bahan_id = tbl_bahan.bahan_id');
        $this->db->join('tbl_satuan', 'tbl_bahan.satuan_id = tbl_satuan.satuan_id');
        $this->db->where('pembelian_id', $pembelian_id);
        return $this->db->get('tbl_pembelian_detail');
    }

    function getTempReturn($table)
    {
        $this->db->join('tbl_bahan', "$table.bahan_id = tbl_bahan.bahan_id");
        $this->db->join('tbl_satuan', 'tbl_bahan.satuan_id = tbl_satuan.satuan_id');
        return $this->db->get($table);
    }

    function getReturnPembelianById($id)
    {
        $this->db->join('tbl_supplier', 'tbl_return_pembelian.supplier_id = tbl_supplier.supplier_id');
        $this->db->join('tbl_karyawan', 'tbl_return_pembelian.karyawan_id = tbl_karyawan.karyawan_id');
        $this->db->where('return_id', $id);
        return $this->db->get('tbl_return_pembelian');
    }

    function updateStokAfterReturn($bahan_id, $qty_return)
    {
        // Get current stock
        $stok = $this->getStok($bahan_id)->row_array();

        if ($stok) {
            $where = [
                'bahan_id' => $bahan_id,
                'gudang_id' => '1'
            ];
            $new_stok_tambah = $stok['stok_tambah'] - $qty_return;

            // Ensure stock doesn't go negative
            if ($new_stok_tambah < 0) {
                $new_stok_tambah = 0;
            }

            $update = [
                'stok_tambah' => $new_stok_tambah

            ];

            $result = $this->db->where($where)->update('stok_gudang', $update);

            // Log the update for debugging
            if (!$result) {
                log_message('error', 'Failed to update stock for bahan_id: ' . $bahan_id . ', qty_return: ' . $qty_return);
            }

            return $result;
        } else {
            // If no stock record exists, create one
            $insert_data = [
                'bahan_id' => $bahan_id,
                'stok_awal' => 0,
                'stok_tambah' => 0,
                'stok_kurang' => 0
            ];

            $result = $this->db->insert('stok_gudang', $insert_data);

            if ($result) {
                // Now try to update again
                return $this->updateStokAfterReturn($bahan_id, $qty_return);
            }

            return false;
        }
    }

    function getReturnSummaryByBahan()
    {
        $this->db->select('bahan_id, SUM(qty_return) as total_return, COUNT(*) as jumlah_return');
        $this->db->from('tbl_return_pembelian_detail');
        $this->db->group_by('bahan_id');
        return $this->db->get();
    }

    function getReturnByBahan($bahan_id)
    {
        $this->db->select('tbl_return_pembelian_detail.*, tbl_return_pembelian.return_tgl, tbl_return_pembelian.no_return, tbl_supplier.supplier_nama');
        $this->db->from('tbl_return_pembelian_detail');
        $this->db->join('tbl_return_pembelian', 'tbl_return_pembelian.return_id = tbl_return_pembelian_detail.return_id');
        $this->db->join('tbl_supplier', 'tbl_return_pembelian.supplier_id = tbl_supplier.supplier_id');
        $this->db->where('tbl_return_pembelian_detail.bahan_id', $bahan_id);
        $this->db->order_by('tbl_return_pembelian.return_tgl', 'desc');
        return $this->db->get();
    }

    function checkReturnByFaktur($no_faktur)
    {
        $this->db->where('no_return', $no_faktur);
        return $this->db->get('tbl_return_pembelian');
    }

    function getReturnBySupplier($supplier_id, $start_date = null)
    {
        $this->db->where('supplier_id', $supplier_id);
        if ($start_date) {
            $this->db->where('return_tgl >=', $start_date);
        }
        return $this->db->get('tbl_return_pembelian');
    }

    function checkReturnBySupplierAndDate($supplier_id, $pembelian_date)
    {
        $this->db->where('supplier_id', $supplier_id);
        $this->db->where('return_tgl >=', $pembelian_date);
        return $this->db->get('tbl_return_pembelian');
    }

    public function getStokGudang1($bahan_id, $gudang_id = 1)
    {
        $this->db->where('bahan_id', $bahan_id);
        $this->db->where('gudang_id', $gudang_id);
        $row = $this->db->get('stok_gudang')->row_array();
        if ($row)
            return $row;
        return [
            'stok_awal' => 0,
            'stok_tambah' => 0,
            'stok_kurang' => 0
        ];
    }

    public function getStokgudangByGudang($gudang_id)
    {
        $this->db->where('gudang_id', $gudang_id);
        $this->db->join('tbl_bahan', 'tbl_bahan.bahan_id = stok_gudang.variant_id');
        $this->db->join('tbl_satuan', 'tbl_bahan.satuan_id = tbl_satuan.satuan_id');
        return $this->db->get('stok_gudang')->result();
    }

    /**
     * Best Practice Stock Engine
     * Updates tbl_stok_gudang and logs the transaction in tbl_stok_log
     */
    private $stock_storage_checked = false;

    private function assert_stock_storage()
    {
        if ($this->stock_storage_checked) return;
        $rows = $this->db->query("SHOW TABLE STATUS WHERE Name IN ('tbl_material','tbl_stok_gudang','tbl_stok_log')")->result();
        $found = [];
        foreach ($rows as $row) {
            if (strtolower((string) $row->Engine) !== 'innodb') throw new DomainException('Tabel stok belum mendukung transaksi aman.');
            $found[] = $row->Name;
        }
        if (array_diff(['tbl_material', 'tbl_stok_gudang', 'tbl_stok_log'], $found)) throw new DomainException('Tabel stok belum lengkap.');
        $this->stock_storage_checked = true;
    }

    /** Checked posting for production only; existing non-production behavior is retained. */
    private function post_production_stock($material_id, $gudang_id, $qty, $trx_id, $catatan, $rak_id, $karyawan_id)
    {
        $started = false;
        try {
            if (!is_numeric($qty) || !is_finite((float) $qty) || (float) $qty >= 0 || (int) $material_id < 1 || (int) $gudang_id < 1) {
                throw new DomainException('Material, lokasi, atau jumlah konsumsi tidak valid.');
            }
            $rak_id = empty($rak_id) ? null : (int) $rak_id;
            $this->assert_stock_storage();
            if (!$this->db->trans_begin()) throw new RuntimeException('Transaksi stok tidak tersedia.');
            $started = true;
            // Lock the material first, including when a balance row has not yet been created.
            $material = $this->db->query('SELECT konversi FROM tbl_material WHERE material_id = ? FOR UPDATE', [(int) $material_id])->row();
            if (!$material) throw new DomainException('Material tidak ditemukan.');
            // Keep the legacy factor 1 for unset/zero conversion without modifying its master data.
            $factor = empty($material->konversi) ? 1 : $material->konversi;
            if (!is_numeric($factor) || !is_finite((float) $factor) || (float) $factor <= 0) throw new DomainException('Konversi satuan material tidak valid.');
            $delta = (float) $qty * (float) $factor;
            if (!is_finite($delta)) throw new DomainException('Konversi konsumsi bahan tidak valid.');
            if ($rak_id === null) {
                $rows = $this->db->query('SELECT * FROM tbl_stok_gudang WHERE material_id = ? AND gudang_id = ? ORDER BY stok_id FOR UPDATE', [(int) $material_id, (int) $gudang_id])->result();
            } else {
                $rows = $this->db->query('SELECT * FROM tbl_stok_gudang WHERE material_id = ? AND gudang_id = ? AND rak_id = ? ORDER BY stok_id FOR UPDATE', [(int) $material_id, (int) $gudang_id, $rak_id])->result();
            }
            if (!$rows) throw new DomainException('Saldo material pada gudang/rak tidak tersedia.');
            $seen = []; $available = 0;
            foreach ($rows as $row) {
                $rack_key = (int) ($row->rak_id ?? 0);
                if (isset($seen[$rack_key])) throw new DomainException('Saldo material pada gudang/rak duplikat. Tinjau stok sebelum produksi.');
                if ((float) $row->qty < 0) throw new DomainException('Saldo historis negatif. Tinjau stok gudang sebelum produksi.');
                $seen[$rack_key] = true;
                $available += (float) $row->qty;
            }
            if ($available < abs($delta)) throw new DomainException('Stok bahan tidak mencukupi pada lokasi produksi. Ajukan bahan ke gudang.');
            // Without an explicit rack, allocate across existing balances in stable stock-row order.
            // Each rack gets its own ledger entry; total consumption remains the operator's actual input.
            $remaining = abs($delta);
            foreach ($rows as $current) {
                if ($remaining <= 0) break;
                $base_use = min((float) $current->qty, $remaining);
                if ($base_use <= 0) continue;
                $balance = (float) $current->qty - $base_use;
                $updated = $this->db->query('UPDATE tbl_stok_gudang SET qty = qty + ?, last_update = ? WHERE stok_id = ? AND qty + ? >= 0',
                    [-$base_use, date('Y-m-d H:i:s'), $current->stok_id, -$base_use]);
                if (!$updated || $this->db->affected_rows() !== 1) throw new DomainException('Saldo stok berubah; muat ulang dan periksa bahan.');
                if (!$this->db->insert('tbl_stok_log', ['material_id' => $material_id, 'gudang_id' => $gudang_id,
                    'rak_id' => $current->rak_id, 'trx_type' => 'Produksi', 'trx_id' => $trx_id,
                    'qty_in' => 0, 'qty_out' => $base_use / (float) $factor, 'qty_base' => $base_use, 'qty_balance' => $balance,
                    'user_id' => $this->session->userdata('id_user') ?: $this->session->userdata('id'),
                    'karyawan_id' => $karyawan_id, 'catatan' => $catatan, 'created_at' => date('Y-m-d H:i:s')])) throw new RuntimeException('Gagal mencatat mutasi bahan.');
                $remaining -= $base_use;
            }
            if (!$this->db->trans_status() || !$this->db->trans_commit()) throw new RuntimeException('Posting stok gagal.');
            $started = false;
            return true;
        } catch (Exception $e) {
            if ($started) $this->db->trans_rollback();
            throw $e; // Production transaction must also roll back results/header/logs.
        }
    }

    public function update_stock_with_log($material_id, $gudang_id, $qty, $type, $trx_id, $catatan = '', $rak_id = NULL, $karyawan_id = NULL)
    {
        if ($type === 'Produksi') {
            return $this->post_production_stock($material_id, $gudang_id, $qty, $trx_id, $catatan, $rak_id, $karyawan_id);
        }
        // Other callers of this shared engine take the same material lock. Otherwise an
        // inventory receipt/adjustment could overwrite a concurrent production deduction.
        if (!$material_id || !$this->db->trans_begin()) return false;
        try {
            $this->db->query('SELECT material_id FROM tbl_material WHERE material_id = ? FOR UPDATE', [(int) $material_id]);
            $posted = $this->post_nonproduction_stock($material_id, $gudang_id, $qty, $type, $trx_id, $catatan, $rak_id, $karyawan_id);
            if (!$posted || !$this->db->trans_status()) { $this->db->trans_rollback(); return false; }
            return $this->db->trans_commit();
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Stock posting failed: ' . $e->getMessage());
            return false;
        }
    }

    private function post_nonproduction_stock($material_id, $gudang_id, $qty, $type, $trx_id, $catatan = '', $rak_id = NULL, $karyawan_id = NULL)
    {

        if (empty($material_id)) {
            return false;
        }

        // Normalize rak_id to prevent duplicate "ATUR RAK" entries
        if (empty($rak_id) || $rak_id == 0 || $rak_id === '') {
            $rak_id = NULL;
        }

        // 1. Get Material Info (For Conversion)
        $material = $this->db->select('konversi, varian')->get_where('tbl_material', ['material_id' => $material_id])->row();
        $konversi = $material ? (float) ($material->konversi ?: 1) : 1;

        // 2. CONVERT TO BASE UNIT (INTERNAL Standard)
        // Everything in tbl_stok_gudang will now be in the smallest unit (e.g., Lembar, not RIM)
        $true_qty_change = $qty * $konversi;

        // 3. Get current stock at specific WAREHOUSE and RACK
        $where_clause = [
            'material_id' => $material_id,
            'gudang_id' => $gudang_id,
            'rak_id' => $rak_id
        ];

        $current = $this->db->where($where_clause)->get('tbl_stok_gudang')->row();

        $old_qty = $current ? (float) $current->qty : 0; // Already in Base Unit
        $new_qty = $old_qty + $true_qty_change;

        // 4. Update or Insert tbl_stok_gudang
        if ($current) {
            $this->db->where('stok_id', $current->stok_id)
                ->update('tbl_stok_gudang', [
                    'qty' => $new_qty,
                    'last_update' => date('Y-m-d H:i:s')
                ]);
        } else {
            $this->db->insert('tbl_stok_gudang', [
                'material_id' => $material_id,
                'gudang_id' => $gudang_id,
                'rak_id' => $rak_id,
                'qty' => $new_qty
            ]);
        }

        // 5. Log to tbl_stok_log
        // We record both the Transaction Qty (Input) and the Internal Qty (Base)
        $log_data = [
            'material_id' => $material_id,
            'gudang_id' => $gudang_id,
            'rak_id' => $rak_id,
            'trx_type' => $type,
            'trx_id' => $trx_id,
            'qty_in' => ($qty > 0) ? $qty : 0,  // Stored in Trans Unit for report
            'qty_out' => ($qty < 0) ? abs($qty) : 0, // Stored in Trans Unit
            'qty_base' => abs($true_qty_change), // Stored precisely in Base Unit
            'qty_balance' => $new_qty, // Stored in Base Unit
            'user_id' => $this->session->userdata('id'),
            'karyawan_id' => $karyawan_id,
            'catatan' => $catatan . " (Total Base: " . ($qty * $konversi) . ")",
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('tbl_stok_log', $log_data);
    }


    /**
     * Get aggregated Daily Material Consumption
     * Used in Admin & Owner Dashboards
     */
    public function get_daily_consumption($date = null, $gudang_id = null)
    {
        if (!$date)
            $date = date('Y-m-d');

        $this->db->select('
            m.varian as material_name, 
            b.bahan_nama, 
            s.satuan_nama as satuan_gudang, 
            sp.satuan_nama as satuan_produksi,
            m.konversi,
            SUM(sl.qty_out) as total_usage_gudang,
            SUM(sl.qty_base) as total_usage_produksi,
            COUNT(sl.log_id) as freq
        ');
        $this->db->from('tbl_stok_log sl');
        $this->db->join('tbl_material m', 'sl.material_id = m.material_id', 'left');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id', 'left');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id', 'left');
        $this->db->join('tbl_satuan sp', 'b.satuan_id = sp.satuan_id', 'left');

        $this->db->where('DATE(sl.created_at)', $date);
        $this->db->where('sl.trx_type', 'Produksi');

        if ($gudang_id) {
            $this->db->where('sl.gudang_id', $gudang_id);
        }

        $this->db->group_by('sl.material_id');
        $this->db->order_by('total_usage_gudang', 'DESC');

        return $this->db->get()->result();
    }

    public function get_warehouse_stock($gudang_id)
    {
        return $this->db->select('m.material_id, m.barcode, b.bahan_nama, m.varian, SUM(s.qty) as qty_base, (SUM(s.qty) / IFNULL(m.konversi, 1)) as qty_stock, sat.satuan_nama as satuan_gudang, m.konversi, sp.satuan_nama as satuan_produksi')
            ->from('tbl_stok_gudang s')
            ->join('tbl_material m', 'm.material_id = s.material_id')
            ->join('tbl_bahan b', 'b.bahan_id = m.bahan_id')
            ->join('tbl_satuan sat', 'm.satuan_gudang_id = sat.satuan_id', 'left')
            ->join('tbl_satuan sp', 'b.satuan_id = sp.satuan_id', 'left')
            ->where('s.gudang_id', $gudang_id)
            ->group_by('m.material_id')
            ->get()->result();
    }

    public function get_all_warehouse_stock()
    {
        return $this->db->select('s.gudang_id, m.material_id, m.barcode, b.bahan_nama, m.varian, SUM(s.qty) as qty_base, sat.satuan_nama as satuan_gudang, m.konversi, sp.satuan_nama as satuan_produksi')
            ->from('tbl_stok_gudang s')
            ->join('tbl_material m', 'm.material_id = s.material_id')
            ->join('tbl_bahan b', 'b.bahan_id = m.bahan_id')
            ->join('tbl_satuan sat', 'm.satuan_gudang_id = sat.satuan_id', 'left')
            ->join('tbl_satuan sp', 'b.satuan_id = sp.satuan_id', 'left')
            ->group_by(['s.gudang_id', 'm.material_id'])
            ->get()->result();
    }

    public function getStokGudangTotalByMaterialId($material_id)
    {
        $this->db->select_sum('qty');
        $this->db->where('material_id', $material_id);
        $result = $this->db->get('tbl_stok_gudang')->row();
        return $result ? (float) $result->qty : 0;
    }

    // ==============================================
    // PENGAMBILAN / DISPATCH CALENDAR REPORT METHODS
    // ==============================================

    function getPengambilanCalendarData($year, $month)
    {
        $this->db->select('DATE(p.pengambilan_tgl) as tanggal, COUNT(DISTINCT p.pengambilan_id) as jumlah_transaksi, SUM(pd.qty) as total_qty');
        $this->db->from('tbl_pengambilan p');
        $this->db->join('tbl_pengambilan_detail pd', 'p.pengambilan_id = pd.pengambilan_id', 'left');
        $this->db->where('YEAR(p.pengambilan_tgl)', $year);
        $this->db->where('MONTH(p.pengambilan_tgl)', $month);
        $this->db->group_by('DATE(p.pengambilan_tgl)');
        return $this->db->get();
    }

    function getPengambilanByDate($date)
    {
        $this->db->select('p.*, k.nama_lengkap as karyawan_nama, g.gudang_nama');
        $this->db->from('tbl_pengambilan p');
        $this->db->join('tbl_karyawan k', 'p.karyawan_id = k.karyawan_id', 'left');
        $this->db->join('tbl_gudang g', 'p.gudang_id = g.gudang_id', 'left');
        $this->db->where('DATE(p.pengambilan_tgl)', $date);
        return $this->db->get()->result();
    }

    function getPengambilanDetailByDate($date)
    {
        $this->db->select('pd.*, m.varian, b.bahan_nama, p.pengambilan_tgl, p.karyawan_id, s.satuan_nama');
        $this->db->from('tbl_pengambilan_detail pd');
        $this->db->join('tbl_pengambilan p', 'pd.pengambilan_id = p.pengambilan_id');
        $this->db->join('tbl_material m', 'pd.material_id = m.material_id');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'b.satuan_id = s.satuan_id', 'left');
        $this->db->where('DATE(p.pengambilan_tgl)', $date);
        return $this->db->get();
    }
}
