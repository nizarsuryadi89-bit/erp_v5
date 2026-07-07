<?php
class M_transaksi extends CI_Model
{
    public function get_legger($tanggal_awal, $tanggal_selesai, $ids_sql)
    {
        return $this->db->query("SELECT p.tgl_bayar, 
                                    SUM(CASE WHEN pd.metode = 'tunai' THEN pd.jumlah ELSE 0 END) AS total_cash, 
                                    SUM(CASE WHEN pd.metode = 'transfer' THEN pd.jumlah ELSE 0 END) AS total_transfer, 
                                    SUM(CASE WHEN pd.metode = 'debit' THEN pd.jumlah ELSE 0 END) AS total_edc, 
                                    SUM(CASE WHEN pd.metode = 'ewallet' THEN pd.jumlah ELSE 0 END) AS total_ewallet, 
                                    SUM(CASE WHEN pd.metode = 'lainnya' THEN pd.jumlah ELSE 0 END) AS total_piutang, 
                                    (SELECT SUM(total_tagihan) FROM tbl_pembayaran WHERE tgl_bayar = p.tgl_bayar AND perusahaan_id IN ($ids_sql)) AS total_harian 
                                    FROM tbl_pembayaran p 
                                    LEFT JOIN tbl_pembayaran_detail pd ON p.pembayaran_id = pd.pembayaran_id
                                    WHERE (p.tgl_bayar >= '$tanggal_awal') 
                                    AND (p.tgl_bayar <= '$tanggal_selesai') 
                                    AND (p.perusahaan_id IN ($ids_sql))
                                    GROUP BY p.tgl_bayar ORDER BY p.tgl_bayar ASC");
    }

    function getcashbulan()
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        $hariini = date('Y-m');
        return $this->db->query("SELECT SUM(pd.jumlah) as total FROM tbl_pembayaran p 
                                    INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk
                                    INNER JOIN tbl_pembayaran_detail pd ON p.pembayaran_id = pd.pembayaran_id
                                    WHERE pm.perusahaan_id = $perusahaan AND p.tgl_bayar like'$hariini%' AND pd.metode = 'tunai' ");
    }

    function cek_harga($product, $qty)
    {
        return $this->db->query("SELECT * FROM tbl_harga_product WHERE product_id = $product");
    }

    function gethutang()
    {
        return $this->db->query("
                SELECT SUM(sisa_hutang) AS total_hutang
                FROM tbl_hutang
                ");

    }

    function getpembelian()
    {
        return $this->db->query("
                SELECT SUM(pembelian_total) AS total_pembelian
                FROM tbl_pembelian
                ");
    }

    public function getkeuanganmobile()
    {
        $bulanini = date('Y-m');
        $this->db->select('p.tgl_bayar, p.total_tagihan, p.total_bayar');
        $this->db->from('tbl_pembayaran p');
        $this->db->like('p.tgl_bayar', $bulanini);
        $this->db->group_by('p.tgl_bayar');

        return $this->db->get();
    }

    public function jmltransaksi($tgl, $store)
    {
        $tglini = $this->db->escape($tgl);
        $query = $this->db->query("
                SELECT SUM(p.total_tagihan) AS jumlah 
                FROM tbl_pembayaran p 
                INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk
                WHERE p.tgl_bayar = $tglini AND pm.perusahaan_id = $store");
        return $query;
    }

    public function getTransaksiBelumBayar()
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        $level = $this->session->userdata('level');

        // Admin (1) and Owner (17) can access all branches
        if (!in_array((int) $level, [1, 17], true)) {
            $this->db->where('tbl_pemesanan.perusahaan_id', $perusahaan);
        }

        $this->db->select('tbl_pemesanan.*, tbl_konsumen.konsumen_nama, tbl_konsumen.konsumen_nohp, 
                              users.username as nama_lengkap, tbl_jenis_transaksi.nama_jenis_transaksi, 
                              tbl_marketplace.marketplace_nama, 
                              ROUND(COALESCE(SUM(
                                GREATEST(
                                    CASE 
                                        WHEN (tbl_pemesanan_detail.panjang > 0 AND tbl_pemesanan_detail.lebar > 0) 
                                        THEN (tbl_pemesanan_detail.panjang * tbl_pemesanan_detail.lebar * tbl_pemesanan_detail.qty * tbl_harga_product.harga_1) 
                                        ELSE (tbl_pemesanan_detail.qty * tbl_harga_product.harga_1) 
                                    END,
                                    COALESCE(tbl_harga_product.harga_minimal, 0)
                                )
                              ), 0)) as total_transaksi');
        $this->db->from('tbl_pemesanan');
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_pemesanan.konsumen_id');
        $this->db->join('users', 'users.user_id = tbl_pemesanan.deskprint_id');
        $this->db->join('tbl_jenis_transaksi', 'tbl_jenis_transaksi.id_jenis_transaksi = tbl_pemesanan.id_jenis_transaksi');
        $this->db->join('tbl_marketplace', 'tbl_marketplace.marketplace_id = tbl_pemesanan.marketplace_id', 'left');
        $this->db->join('tbl_pemesanan_detail', 'tbl_pemesanan_detail.nospk = tbl_pemesanan.nospk', 'left');
        $this->db->join('tbl_harga_product', 'tbl_harga_product.harga_id = tbl_pemesanan_detail.harga_id', 'left');
        $this->db->where('tbl_pemesanan.status_pembayaran', 'pending');
        $this->db->group_by('tbl_pemesanan.nospk');
        $this->db->order_by('tbl_pemesanan.created_at', 'desc');
        return $this->db->get();
    }

    public function getpengeluaran()
    {
        $bulanini = date('Y-m');
        $this->db->select('pengeluaran_tgl');
        $this->db->from('tbl_pengeluaran');
        $this->db->like('pengeluaran_tgl', $bulanini);
        $this->db->group_by('pengeluaran_tgl');

        return $this->db->get();
    }

    public function jmlpengeluaran($tgl, $store)
    {
        $tglini = $this->db->escape($tgl);
        $query = $this->db->query("
                SELECT SUM(pengeluaran_jumlah) AS jumlah 
                FROM tbl_pengeluaran 
                WHERE pengeluaran_tgl = $tglini AND perusahaan_id = $store");
        return $query;
    }

    public function product_terlaris()
    {
        $bulan = date('Y-m');
        return $this->db->query("SELECT 
                                        pp.product_id,
                                        pp.nospk,
                                        p.product_nama,
                                        s.satuan_nama,
                                        pm.tgl_bayar,
                                        SUM(pp.qty) AS total_qty
                                    FROM 
                                        tbl_pembayaran_produk pp 
                                    INNER JOIN 
                                        tbl_product p ON pp.product_id = p.product_id 
                                    INNER JOIN 
                                        tbl_satuan s ON p.satuan_id = s.satuan_id
                                    INNER JOIN 
                                        tbl_pembayaran pm ON pp.nospk = pm.nospk
                                    WHERE 
                                        pm.tgl_bayar LIKE '$bulan%'
                                    GROUP BY 
                                        pp.product_id,
                                        pp.nospk,
                                        p.product_nama,
                                        s.satuan_nama,
                                        pm.tgl_bayar
                                    ORDER BY 
                                        total_qty DESC
                                    LIMIT 10

                                ");
    }






    public function get_transaksi()
    {
        $user = $this->session->userdata('id');
        return $this->db->query("SELECT SUM(total_tagihan) as totalna FROM tbl_pembayaran WHERE kasir_id = $user");

    }

    public function get_konsumen_dengan_transaksi()
    {
        return $this->db->query("SELECT k.konsumen_id, k.konsumen_nama, k.konsumen_nohp, 
                                        COUNT(DISTINCT pm.nospk) AS jumlah_transaksi, 
                                        COALESCE(SUM(p.total_tagihan), 0) AS total_transaksi 
                                        FROM tbl_konsumen k 
                                        JOIN tbl_pemesanan pm ON k.konsumen_id = pm.konsumen_id
                                        LEFT JOIN tbl_pembayaran p ON pm.nospk = p.nospk
                                        GROUP BY k.konsumen_id, k.konsumen_nama 
                                        ORDER BY total_transaksi DESC
                                        LIMIT 10
                                    ");
    }

    // function getTotalPiutang(){
    //     return $this->db->query("SELECT SUM(piutang_sisa) as total_piutang FROM tbl_piutang");
    // }

    function total_produk_terjual($date)
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        return $this->db->query("SELECT SUM(pp.qty) as total FROM tbl_pembayaran_produk pp 
                                        INNER JOIN tbl_pembayaran p ON pp.nospk = p.nospk 
                                        INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk
                                            WHERE p.tgl_bayar like'$date%' and pm.perusahaan_id = $perusahaan
                                    ");
    }

    function cekRange($qty)
    {
        return $this->db->query("SELECT * FROM tbl_range WHERE range_min >= '$qty' OR range_max <= '$qty'");

    }

    function update($table, $data, $where)
    {
        $this->db->where($where);
        return $this->db->update($table, $data);
    }

    function deleteTemp($product, $kodena)
    {
        $this->db->where('product_id', $product);
        $this->db->where('kode_trx', $kodena);
        return $this->db->delete('tbl_temp_transaksi');
    }

    /**
     * Universal Pricing Function
     * Handles Qty range, Varian (ket_1), and Machine (jenisprint_id)
     */
    function get_product_price($product_id, $qty, $params = [])
    {
        $ket_1 = isset($params['ket_1']) ? $params['ket_1'] : 0;
        $jenisprint_id = isset($params['jenisprint_id']) ? $params['jenisprint_id'] : 0;

        $this->db->where('product_id', $product_id);

        // TIRED PRICING LOGIC
        $lookup_qty = ($qty > 0 && $qty < 1) ? 1 : $qty;
        $this->db->where('qty_min <=', $lookup_qty);

        $this->db->group_start();
        $this->db->where('qty_max >=', $lookup_qty);
        $this->db->or_where('qty_max', 0);
        $this->db->or_where('qty_max <=', 1);
        $this->db->group_end();

        // Flexible mapping for ket_1 and jenisprint_id to prevent admin confusion
        // If they use ket_1 or jenisprint_id in Form Builder, we match it against EITHER column in DB
        // Because legacy system used to swap these fields for Digital Print
        if (!empty($jenisprint_id) || !empty($ket_1)) {
            $this->db->group_start();
                $this->db->group_start();
                    if (!empty($jenisprint_id)) {
                        $this->db->where('jenisprint_id', $jenisprint_id);
                    }
                    if (!empty($ket_1)) {
                        $this->db->where('ket_1', $ket_1);
                    }
                $this->db->group_end();

                // Cross-matching if only one was provided (to handle mismatched UI vs Pricing config)
                if (!empty($ket_1) && empty($jenisprint_id)) {
                    $this->db->or_where('jenisprint_id', $ket_1);
                }
                if (!empty($jenisprint_id) && empty($ket_1)) {
                    $this->db->or_where('ket_1', $jenisprint_id);
                }
            $this->db->group_end();
        }

        // Sort to get the most specific tier first (highest min qty that fits)
        $this->db->order_by('qty_min', 'DESC');
        $this->db->order_by('jenisprint_id', 'DESC');
        $this->db->order_by('ket_1', 'DESC');

        return $this->db->get('tbl_harga_product');
    }

    function cekhargakategori1($product, $qty, $ket_1)
    {
        return $this->get_product_price($product, $qty, ['ket_1' => $ket_1]);
    }

    function cekharganormal($product, $qty)
    {
        return $this->get_product_price($product, $qty);
    }

    function cekHargaKategori2($product, $jenis)
    {
        return $this->get_product_price($product, 1, ['jenisprint_id' => $jenis]); // Category 2 base price usually for 1m2
    }

    function cekHargaProductD($product, $qty)
    {
        return $this->db->query("SELECT * FROM tbl_harga_product 
                                        WHERE product_id = '$product' 
                                                AND jml_minimum <= '$qty' 
                                                        ORDER BY jml_minimum
                                                            DESC LIMIT 0,1
                                    
                                    ");
    }


    function cekHargaProductC($product, $ket, $field)
    {
        return $this->db->query("SELECT * FROM tbl_harga_product 
                                        WHERE product_id = '$product' 
                                            AND $field = '$ket' 
                                    ");
    }


    function cekHargaProductJ($product, $qty, $sisi, $ket2)
    {
        return $this->db->query("SELECT * FROM tbl_harga_product 
                                        WHERE product_id = '$product' 
                                            AND ket_1 = '$sisi'
                                                
                                                    AND jml_minimum <= '$qty' 
                                                        Order By jml_minimum 
                                                            DESC LIMIT 0,1 ");
    }

    function cekHargaProductK($product, $qty)
    {
        return $this->db->query("SELECT * FROM tbl_harga_product 
                                        WHERE product_id = '$product' 
                                                AND jml_minimum <= '$qty' 
                                                    Order By jml_minimum 
                                                        DESC LIMIT 0,1 ");
    }

    function cekHargaProductI($product)
    {
        return $this->db->query("SELECT * FROM tbl_harga_product WHERE harga_id = '$product' ");
    }

    function cekHargaProductF($product, $ket_1, $ket_2)
    {
        return $this->db->query("SELECT * FROM tbl_harga_product 
                                        WHERE product_id = '$product' AND ket_1 = '$ket_1' 
                                    ");
    }
    function cekHargaProductG($product, $ket_1, $ket_2, $ket_3)
    {
        return $this->db->query("SELECT * FROM tbl_harga_product 
                                        WHERE product_id = '$product' AND ket_1 = '$ket_1' AND ket_3 = '$ket_3'
                                    ");
    }

    function cekHargaProductE($product, $panjang)
    {
        return $this->db->query("SELECT * FROM tbl_harga_product WHERE product_id = $product 
                                        AND (jml_minimum <= $panjang) order By jml_minimum DESC LIMIT 0,1
                                                            
                                    ");
    }

    function cekHargaProductH($product, $ket_1)
    {
        return $this->db->query("SELECT * FROM tbl_harga_product 
                                        WHERE product_id = '$product' AND ket_1 = '$ket_1'
                                    ");
    }

    function cekProduct($kategori, $subkategori, $bahan)
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        $this->db->join('tbl_kategori', 'tbl_kategori.kategori_id = tbl_product.kategori_id');
        $this->db->join('tbl_subkategori', 'tbl_subkategori.subkategori_id = tbl_product.subkategori_id');
        $this->db->where('tbl_product.kategori_id', $kategori);
        $this->db->where('tbl_product.subkategori_id', $subkategori);
        $this->db->where('bahan_id', $bahan);
        return $this->db->get('tbl_product');
    }

    function tampilTmp($kode)
    {
        $user = $this->session->userdata('id');
        $this->db->select('
        tbl_temp_transaksi.*,
        tbl_satuan.*, 
        tbl_harga_product.harga_1, 
        tbl_harga_product.harga_minimal, 
        tbl_harga_product.harga_aktif, 
        tbl_harga_product.ket_1, 
        tbl_harga_product.harga_id, 
        tbl_harga_product.detail_product, 
        tbl_harga_product.range_id, 
        tbl_harga_product.jml_minimum, 
        tbl_harga_product.jenisprint_id, 
        tbl_konsumen.*, 
        tbl_product.kategori_id,
        tbl_kategori.form_schema as category_form_schema,
        tbl_subkategori.form_schema as subcategory_form_schema,
        tbl_product.form_schema as product_form_schema
    ');
        $this->db->where('no_spk', $kode);
        $this->db->join('tbl_harga_product', 'tbl_harga_product.harga_id = tbl_temp_transaksi.harga_id');
        $this->db->join('tbl_product', 'tbl_product.product_id = tbl_temp_transaksi.product_id');
        $this->db->join('tbl_kategori', 'tbl_kategori.kategori_id = tbl_product.kategori_id', 'left');
        $this->db->join('tbl_subkategori', 'tbl_subkategori.subkategori_id = tbl_product.subkategori_id', 'left');
        $this->db->join('tbl_satuan', 'tbl_satuan.satuan_id = tbl_product.satuan_id');
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_temp_transaksi.konsumen_id', 'left');
        return $this->db->get('tbl_temp_transaksi');
    }

    function tampilTmp_editspk($kode)
    {
        $user = $this->session->userdata('id');
        $this->db->select('tbl_pemesanan_detail.*,tbl_satuan.*, tbl_harga_product.harga_1, tbl_harga_product.harga_minimal, tbl_harga_product.harga_aktif, tbl_harga_product.ket_1, tbl_harga_product.harga_id, tbl_harga_product.detail_product, tbl_harga_product.range_id, tbl_harga_product.jml_minimum, tbl_product.form_schema as product_form_schema, tbl_kategori.form_schema as category_form_schema, tbl_subkategori.form_schema as subcategory_form_schema');
        $this->db->where('nospk', $kode);
        $this->db->join('tbl_harga_product', 'tbl_harga_product.harga_id = tbl_pemesanan_detail.harga_id');
        $this->db->join('tbl_product', 'tbl_product.product_id = tbl_pemesanan_detail.product_id');
        $this->db->join('tbl_satuan', 'tbl_satuan.satuan_id = tbl_product.satuan_id');
        $this->db->join('tbl_kategori', 'tbl_kategori.kategori_id = tbl_product.kategori_id', 'left');
        $this->db->join('tbl_subkategori', 'tbl_subkategori.subkategori_id = tbl_product.subkategori_id', 'left');
        return $this->db->get('tbl_pemesanan_detail');
    }

    function deleteTmp($kode)
    {
        $user = $this->session->userdata('id');
        $this->db->where('user_id', $user);
        $this->db->delete('tbl_temp_transaksi');
    }

    function setKonsumen($konsummen, $kode)
    {
        $user = $this->session->userdata('id');
        $this->db->where('user_id', $user);
        $this->db->where('kode_trx', $kode);
        $this->db->update('tbl_temp_transaksi', ['konsumen_id' => $konsummen]);
    }

    function getNoSPK($user, $perusahaan)
    {
        $month = date('m');
        $year = date('Y');

        return $this->db->query(
            "SELECT no_urut_spk FROM tbl_spk 
             WHERE perusahaan_id = $perusahaan 
             AND user_id = $user 
             AND MONTH(spk_time) = $month 
             AND YEAR(spk_time) = $year
             ORDER BY no_urut_spk DESC LIMIT 0,1"
        );
    }

    function getDataPemesanan()
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        $this->db->where('tbl_pemesanan.perusahaan_id', $perusahaan);
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_pemesanan.konsumen_id');
        $this->db->join('users', 'users.user_id = tbl_pemesanan.deskprint_id');
        $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = users.karyawan_id');
        $this->db->join('tbl_jenis_transaksi', 'tbl_jenis_transaksi.id_jenis_transaksi = tbl_pemesanan.id_jenis_transaksi');
        $this->db->join('tbl_marketplace', 'tbl_marketplace.marketplace_id = tbl_pemesanan.marketplace_id', 'left');
        $this->db->join('tbl_pengiriman', 'tbl_pengiriman.id_pengiriman = tbl_pemesanan.id_pengiriman', 'left');

        // Batasi maksimal data 2 bulan terakhir agar memori tidak jebol
        $dua_bulan_lalu = date('Y-m-d', strtotime('-2 months'));
        $this->db->where('tbl_pemesanan.created_at >=', $dua_bulan_lalu);

        // $this->db->join('tbl_pembayaran', 'tbl_pembayaran.nospk = tbl_pemesanan.nospk', 'left');
        $this->db->order_by('tbl_pemesanan.created_at', 'desc');
        return $this->db->get('tbl_pemesanan');
    }

    public function getCountPemesanankasir($status, $perusahaan_id = null)
    {
        if ($perusahaan_id !== null) {
            if ($status == '0') {
                $this->db->where('tbl_pemesanan.perusahaan_id', $perusahaan_id);
            } else {
                $this->db->where('perusahaan_id', $perusahaan_id);
            }
        } else {
            $level = $this->session->userdata('level');
            if (!in_array((int) $level, [1, 2, 17, 21], true)) {
                $sess_perusahaan = $this->session->userdata('perusahaan_id');
                if ($status == '0') {
                    $this->db->where('tbl_pemesanan.perusahaan_id', $sess_perusahaan);
                } else {
                    $this->db->where('perusahaan_id', $sess_perusahaan);
                }
            }
        }
        $this->db->where('status_pembayaran', $status == '0' ? 'pending' : ($status == '1' ? 'paid' : $status));
        return $this->db->count_all_results('tbl_pemesanan');
    }

    function getDataPemesanankasir($status, $perusahaan_override = null)
    {
        $perusahaan = $perusahaan_override ?? $this->session->userdata('perusahaan_id');
        $level = $this->session->userdata('level');

        // Admin (1), Manager (2), Owner (17) and HR (21) can access all branches UNLESS a specific branch is filtered
        // KASIR (3) can access all but will use filter.
        if ($perusahaan_override !== null) {
            $this->db->where('tbl_pemesanan.perusahaan_id', $perusahaan_override);
        } else {
            if (!in_array((int) $level, [1, 2, 17, 21], true)) {
                $this->db->where('tbl_pemesanan.perusahaan_id', $perusahaan);
            }
        }

        // $this->db->where('tbl_pemesanan.deskprint_id', $user);
        $this->db->select('
            tbl_konsumen.*, 
            tbl_karyawan.nama_lengkap, 
            tbl_jenis_transaksi.*, 
            tbl_pengiriman.*, 
            users.username, 
            tbl_marketplace.*, 
            detail_agg.lokasi_cetak AS id_lokasi_cetak,
            tg.gudang_nama AS lokasi_cabang,
            tbl_pemesanan.*,
            detail_agg.jumlah_item,
            COALESCE(NULLIF(max_pay.total_tagihan, 0), detail_agg.total_detail) AS total_transaksi
        ');
        $this->db->where('tbl_pemesanan.status_pembayaran', $status == '0' ? 'pending' : ($status == '1' ? 'paid' : $status));
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_pemesanan.konsumen_id');
        $this->db->join('users', 'users.user_id = tbl_pemesanan.deskprint_id');
        $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = users.karyawan_id');
        $this->db->join('tbl_jenis_transaksi', 'tbl_jenis_transaksi.id_jenis_transaksi = tbl_pemesanan.id_jenis_transaksi');
        $this->db->join('tbl_marketplace', 'tbl_marketplace.marketplace_id = tbl_pemesanan.marketplace_id', 'left');
        $this->db->join('tbl_pengiriman', 'tbl_pengiriman.id_pengiriman = tbl_pemesanan.id_pengiriman', 'left');
        $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_pemesanan.perusahaan_id', 'left');
        
        // Optimasi: Gunakan LEFT JOIN dengan derived table alih-alih subquery
        $this->db->join('(
            SELECT pd.nospk, 
                   COUNT(*) as jumlah_item,
                   MAX(pd.lokasi_cetak) as lokasi_cetak,
                   ROUND(SUM(
                       GREATEST(
                           CASE 
                               WHEN (pr.satuan_id = 11) THEN (pd.panjang * pd.qty * hp.harga_1)
                               WHEN (pd.panjang > 0 AND pd.lebar > 0) THEN (pd.panjang * pd.lebar * pd.qty * hp.harga_1) 
                               WHEN (pd.panjang > 0 AND (pd.lebar = 0 OR pd.lebar IS NULL)) THEN (pd.panjang * pd.qty * hp.harga_1)
                               ELSE (pd.qty * hp.harga_1) 
                           END,
                           COALESCE(hp.harga_minimal, 0)
                       )
                   )) as total_detail
            FROM tbl_pemesanan_detail pd 
            LEFT JOIN tbl_harga_product hp ON hp.harga_id = pd.harga_id 
            LEFT JOIN tbl_product pr ON pr.product_id = pd.product_id 
            GROUP BY pd.nospk
        ) as detail_agg', 'detail_agg.nospk = tbl_pemesanan.nospk', 'left');
        $this->db->join('tbl_gudang tg', 'tg.gudang_id = detail_agg.lokasi_cetak', 'left');
        $this->db->join('(
            SELECT p1.nospk, p1.total_tagihan
            FROM tbl_pembayaran p1
            INNER JOIN (
                SELECT nospk, MAX(pembayaran_id) as max_id FROM tbl_pembayaran GROUP BY nospk
            ) p2 ON p1.nospk = p2.nospk AND p1.pembayaran_id = p2.max_id
        ) as max_pay', 'max_pay.nospk = tbl_pemesanan.nospk', 'left');
        
        // Batasi maksimal data 2 bulan terakhir agar memori tidak jebol
        $dua_bulan_lalu = date('Y-m-d', strtotime('-2 months'));
        $this->db->where('tbl_pemesanan.created_at >=', $dua_bulan_lalu);
        
        $this->db->order_by('tbl_pemesanan.created_at', 'desc');
        return $this->db->get('tbl_pemesanan');
    }

    function transaksideskprint($user)
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        // $user       = $this->session->userdata('id');

        // Filter untuk bulan berjalan
        $bulanini = date('Y-m');

        // Select eksplisit untuk memastikan field created_at dari tbl_pemesanan
        // Menggunakan tbl_pemesanan.* untuk mengambil semua field dari tabel pemesanan termasuk created_at
        // Field created_at yang digunakan adalah dari tbl_pemesanan.created_at
        $this->db->select('tbl_pemesanan.*, 
                              tbl_konsumen.konsumen_nama, 
                              tbl_konsumen.konsumen_nohp, 
                              users.username, 
                              users.username,
                              tbl_jenis_transaksi.nama_jenis_transaksi, 
                              tbl_marketplace.marketplace_nama');

        $this->db->from('tbl_pemesanan');
        $this->db->where('tbl_pemesanan.perusahaan_id', $perusahaan);
        $this->db->where('tbl_pemesanan.deskprint_id', $user);
        $this->db->like('tbl_pemesanan.created_at', $bulanini);
        // $this->db->where('tbl_pemesanan.status_pembayaran', $status);
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_pemesanan.konsumen_id');
        $this->db->join('users', 'users.user_id = tbl_pemesanan.deskprint_id');
        // $this->db->join('tbl_karyawan','tbl_karyawan.karyawan_id = users.karyawan_id');
        $this->db->join('tbl_jenis_transaksi', 'tbl_jenis_transaksi.id_jenis_transaksi = tbl_pemesanan.id_jenis_transaksi');
        $this->db->join('tbl_marketplace', 'tbl_marketplace.marketplace_id = tbl_pemesanan.marketplace_id', 'left');
        // $this->db->join('tbl_pengiriman','tbl_pengiriman.id_pengiriman = tbl_pemesanan.id_pengiriman','left');
        $this->db->order_by('tbl_pemesanan.created_at', 'desc');
        return $this->db->get();
    }

    function getbayarsukses($status, $perusahaan_override = null)
    {
        $perusahaan = $perusahaan_override ?? $this->session->userdata('perusahaan_id');
        $user = $this->session->userdata('id');
        $level = $this->session->userdata('level');

        if ($perusahaan_override !== null) {
            $this->db->where('tbl_pemesanan.perusahaan_id', $perusahaan_override);
        } else {
            if (!in_array((int) $level, [1, 2, 17, 21], true)) {
                $this->db->where('tbl_pemesanan.perusahaan_id', $perusahaan);
            }
        }

        $this->db->where('tbl_pemesanan.status_pembayaran', $status == '0' ? 'pending' : ($status == '1' ? 'paid' : $status));
        if (!in_array((int) $level, [1, 2, 17, 21], true)) {
            $this->db->where('tbl_pembayaran.kasir_id', $user);
        }
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_pemesanan.konsumen_id');
        $this->db->join('users', 'users.user_id = tbl_pemesanan.deskprint_id');
        $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = users.karyawan_id');
        $this->db->join('tbl_jenis_transaksi', 'tbl_jenis_transaksi.id_jenis_transaksi = tbl_pemesanan.id_jenis_transaksi');
        $this->db->join('tbl_marketplace', 'tbl_marketplace.marketplace_id = tbl_pemesanan.marketplace_id', 'left');
        $this->db->join('tbl_pengiriman', 'tbl_pengiriman.id_pengiriman = tbl_pemesanan.id_pengiriman', 'left');

        $this->db->join('tbl_pembayaran', 'tbl_pembayaran.nospk = tbl_pemesanan.nospk', 'left');
        
        // Batasi maksimal data 2 bulan terakhir agar memori tidak jebol
        $dua_bulan_lalu = date('Y-m-d', strtotime('-2 months'));
        $this->db->where('tbl_pemesanan.created_at >=', $dua_bulan_lalu);
        
        $this->db->group_by('tbl_pemesanan.nospk');
        $this->db->order_by('tbl_pemesanan.created_at', 'desc');
        return $this->db->get('tbl_pemesanan');
    }

    function getpenjualanall()
    {
        $bulanini = date('Y-m');
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_pemesanan.konsumen_id');
        $this->db->join('users', 'users.user_id = tbl_pemesanan.deskprint_id');
        $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = users.karyawan_id');
        $this->db->join('tbl_jenis_transaksi', 'tbl_jenis_transaksi.id_jenis_transaksi = tbl_pemesanan.id_jenis_transaksi');
        $this->db->join('tbl_marketplace', 'tbl_marketplace.marketplace_id = tbl_pemesanan.marketplace_id', 'left');
        $this->db->join('tbl_pengiriman', 'tbl_pengiriman.id_pengiriman = tbl_pemesanan.id_pengiriman', 'left');
        $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_pemesanan.perusahaan_id');
        $this->db->join('tbl_pembayaran', 'tbl_pembayaran.nospk = tbl_pemesanan.nospk', 'left');
        $this->db->like('created_at', $bulanini);
        $this->db->order_by('tbl_pemesanan.perusahaan_id', 'asc');
        $this->db->order_by('tbl_pemesanan.created_at', 'desc');

        return $this->db->get('tbl_pemesanan');
    }

    function getDetailPemesanan()
    {
        $nospk = decrypt_url($this->uri->segment(3));

        $this->db->where('tbl_pemesanan.nospk', $nospk);
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_pemesanan.konsumen_id');
        $this->db->join('users', 'users.user_id = tbl_pemesanan.deskprint_id');
        $this->db->join('tbl_jenis_transaksi', 'tbl_jenis_transaksi.id_jenis_transaksi = tbl_pemesanan.id_jenis_transaksi');
        $this->db->join('tbl_marketplace', 'tbl_marketplace.marketplace_id = tbl_pemesanan.marketplace_id', 'left');
        $this->db->join('tbl_pemesanan_detail', 'tbl_pemesanan_detail.nospk = tbl_pemesanan.nospk');
        $this->db->join('tbl_harga_product', 'tbl_harga_product.harga_id = tbl_pemesanan_detail.harga_id');
        return $this->db->get('tbl_pemesanan');
    }

    function getDetail()
    {
        $nospk = decrypt_url($this->uri->segment(3));
        $this->db->select('tbl_pemesanan_detail.*, tbl_product.product_nama, tbl_kategori.kategori_nama, tbl_product.kategori_id, tbl_product.satuan_id, tbl_harga_product.harga_1, tbl_harga_product.harga_aktif, tbl_harga_product.ket_1, tbl_harga_product.harga_id, tbl_harga_product.detail_product, tbl_harga_product.range_id, tbl_harga_product.jml_minimum, tbl_harga_product.jenisprint_id, tbl_harga_product.harga_minimal, tbl_product.form_schema as product_form_schema, tbl_kategori.form_schema as category_form_schema, tbl_subkategori.form_schema as subcategory_form_schema');
        $this->db->where('nospk', $nospk);
        $this->db->join('tbl_product', 'tbl_product.product_id = tbl_pemesanan_detail.product_id');
        $this->db->join('tbl_kategori', 'tbl_kategori.kategori_id = tbl_product.kategori_id', 'left');
        $this->db->join('tbl_subkategori', 'tbl_subkategori.subkategori_id = tbl_product.subkategori_id', 'left');
        $this->db->join('tbl_harga_product', 'tbl_harga_product.harga_id = tbl_pemesanan_detail.harga_id');
        return $this->db->get('tbl_pemesanan_detail');
    }

    function getDetailPesan($nospk)
    {
        // Align selection and snapshot precedence with M_pembayaran->get_pembayaran_produk
        $this->db->select("tbl_pemesanan_detail.*, tbl_pemesanan_detail.metadata, tbl_product.product_nama, tbl_kategori.kategori_nama, tbl_product.kategori_id, tbl_product.satuan_id,
            COALESCE(pp.snapshot_harga, tbl_harga_product.harga_1) AS harga_1,
            tbl_harga_product.harga_aktif,
            tbl_harga_product.ket_1,
            tbl_harga_product.harga_id,
            COALESCE(pp.snapshot_detail_product, tbl_harga_product.detail_product) AS detail_product,
            tbl_harga_product.range_id,
            tbl_harga_product.jml_minimum,
            tbl_harga_product.jenisprint_id,
            tbl_harga_product.harga_minimal,
            tbl_spk.konsumen_id,
            pp.snapshot_harga,
            pp.snapshot_subtotal AS snapshot_subtotal,
            tbl_product.form_schema as product_form_schema,
            tbl_kategori.form_schema as category_form_schema,
            tbl_subkategori.form_schema as subcategory_form_schema,
            tbl_harga_product.metadata AS product_metadata", FALSE);

        $this->db->where('tbl_pemesanan_detail.nospk', $nospk);
        $this->db->join('tbl_product', 'tbl_product.product_id = tbl_pemesanan_detail.product_id');
        $this->db->join('tbl_kategori', 'tbl_kategori.kategori_id = tbl_product.kategori_id', 'left');
        $this->db->join('tbl_subkategori', 'tbl_subkategori.subkategori_id = tbl_product.subkategori_id', 'left');
        $this->db->join('tbl_harga_product', 'tbl_harga_product.harga_id = tbl_pemesanan_detail.harga_id');
        $this->db->join('tbl_spk', 'tbl_spk.nospk = tbl_pemesanan_detail.nospk', 'left');

        // Join to a subquery that picks the latest pembayaran_produk row per group
        $nospk_esc = $this->db->escape($nospk);
        $sub = "(
            SELECT ppx.* FROM tbl_pembayaran_produk ppx
            JOIN (
                SELECT MAX(id) AS id FROM tbl_pembayaran_produk
                WHERE nospk = {$nospk_esc}
                GROUP BY nospk, product_id, harga_id, panjang, lebar
            ) mpx ON ppx.id = mpx.id
        ) pp";

        $this->db->join($sub, 'pp.nospk = tbl_pemesanan_detail.nospk AND pp.product_id = tbl_pemesanan_detail.product_id AND pp.harga_id = tbl_pemesanan_detail.harga_id', 'left', FALSE);

        return $this->db->get('tbl_pemesanan_detail');
    }

    function deletePesanan($table, $nospk)
    {
        $this->db->where('nospk', $nospk);
        return $this->db->delete($table);
    }

    /**
     * Alias for getDetailPesan to support legacy or different naming conventions
     */
    public function detail_pemesanan($nospk)
    {
        return $this->getDetailPesan($nospk);
    }

    function getPenjualan()
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        $bulanini = date('Y-m');
        $this->db->where('tbl_pembayaran.perusahaan_id', $perusahaan);
        $this->db->like('tbl_pembayaran.tbl_bayar', $bulanini, 'after');
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_pembayaran.konsumen_id');
        // $this->db->join('tbl_jenis_transaksi','tbl_jenis_transaksi.id_jenis_transaksi = tbl_pembayaran.id_jenis_transaksi');
        // $this->db->join('tbl_marketplace','tbl_marketplace.marketplace_id = tbl_pembayaran.marketplace_id', 'left');

        $this->db->join('users', 'users.user_id = tbl_pembayaran.kasir_id');
        // $this->db->order_by('tbl_pembayaran.id', 'desc');
        return $this->db->get('tbl_pembayaran');
    }

    function getBayarmetode($bayar = null)
    {
        //print_r($bayar);
        $perusahaan = $this->session->userdata('perusahaan_id');
        $bulanini = date('Y-m');
        $this->db->where('tbl_pembayaran.perusahaan_id', $perusahaan);
        $this->db->like('tbl_pembayaran.tbl_bayar', $bulanini, 'after');
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_pembayaran.konsumen_id');
        $this->db->join('tbl_jenis_transaksi', 'tbl_jenis_transaksi.id_jenis_transaksi = tbl_pembayaran.id_jenis_transaksi');
        $this->db->join('tbl_marketplace', 'tbl_marketplace.marketplace_id = tbl_pembayaran.marketplace_id', 'left');

        $this->db->join('users', 'users.user_id = tbl_pembayaran.kasir_id');
        // $this->db->order_by('tbl_pembayaran.id', 'desc');
        return $this->db->get('tbl_pembayaran');
    }

    function getPenjualanday($bayar)
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        $hariini = date('Y-m-d');
        if ($bayar == '1') {
            $this->db->where('bayar_tunai <>', 0);
        } elseif ($bayar == '2') {
            $this->db->where('bayar_transfer <>', 0);
        } elseif ($bayar == '3') {
            $this->db->where('bayar_debit <>', 0);
        } elseif ($bayar == '4') {
            $this->db->where('bayar_ewallet <>', 0);
        } else {

        }

        $this->db->where('tbl_pembayaran.perusahaan_id', $perusahaan);
        $this->db->where('tbl_pembayaran.tbl_bayar', $hariini);
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_pembayaran.konsumen_id');
        $this->db->join('tbl_jenis_transaksi', 'tbl_jenis_transaksi.id_jenis_transaksi = tbl_pembayaran.id_jenis_transaksi');
        $this->db->join('tbl_marketplace', 'tbl_marketplace.marketplace_id = tbl_pembayaran.marketplace_id', 'left');

        $this->db->join('users', 'users.user_id = tbl_pembayaran.kasir_id');
        // $this->db->order_by('tbl_pembayaran.id', 'desc');
        return $this->db->get('tbl_pembayaran');
    }

    function getPembayaran($nospk = null)
    {
        if (empty($nospk)) {
            // Return empty result if no SPK provided
            return $this->db->get_where('tbl_pembayaran', array('pembayaran_id' => 0));
        }

        // $perusahaan = $this->session->userdata('perusahaan_id');
        // $this->db->where('tbl_pembayaran.perusahaan_id', $perusahaan);
        $this->db->select('tbl_pembayaran.*, tbl_konsumen.konsumen_nama, tbl_konsumen.konsumen_nohp, users.username as kasir_name');
        $this->db->where('tbl_pembayaran.nospk', $nospk);
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_pembayaran.konsumen_id');
        $this->db->join('tbl_pembayaran_detail', 'tbl_pembayaran_detail.pembayaran_id = tbl_pembayaran.pembayaran_id');
        // $this->db->join('tbl_rekening','tbl_rekening.id = tbl_pembayaran.rekening_id', 'left');
        // $this->db->join('tbl_ewallet','tbl_ewallet.id = tbl_pembayaran.ewallet_id', 'left');
        $this->db->join('users', 'users.user_id = tbl_pembayaran.kasir_id');
        return $this->db->get('tbl_pembayaran');

    }

    function getPengambilan($nospk)
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        $this->db->where('nospk', $nospk);
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_pembayaran.konsumen_id');
        $this->db->join('users', 'users.user_id = tbl_pembayaran.kasir_id');
        return $this->db->get('tbl_pembayaran');

    }

    function getcekpesanan($nospk)
    {
        $this->db->select('tbl_pemesanan.*, tbl_pemesanan.status_pembayaran as status_bayar_trx, tbl_konsumen.*, users.username, kasir.username as kasir_name, tbl_pembayaran.tgl_bayar, tbl_pembayaran.jam_bayar, tbl_pembayaran.total_bayar, tbl_pembayaran.total_tagihan, tbl_pembayaran.nospk as bayar_nospk');
        $this->db->from('tbl_pemesanan');
        $this->db->where('tbl_pemesanan.nospk', $nospk);
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_pemesanan.konsumen_id', 'left');
        $this->db->join('users', 'users.user_id = tbl_pemesanan.deskprint_id', 'left');
        $this->db->join('tbl_pembayaran', 'tbl_pembayaran.nospk = tbl_pemesanan.nospk', 'left');
        $this->db->join('users kasir', 'kasir.user_id = tbl_pembayaran.kasir_id', 'left');
        return $this->db->get();
    }

    function getcekflow($nospk)
    {
        return $this->db->get_where("tbl_produksi", array('nospk' => $nospk));
    }



    function getDetailPembayaran($nospk)
    {
        $this->db->where('nospk', $nospk);
        $this->db->join('tbl_pembayaran_detail', 'tbl_pembayaran_detail.pembayaran_id = tbl_pembayaran.pembayaran_id');
        return $this->db->get('tbl_pembayaran');
    }

    function getDetailPembayaranAll()
    {
        $divisi = $this->session->userdata('level');
        $this->db->select('tbl_pembayaran_detail.*, tbl_pembayaran.*, tbl_product.*, tbl_bahan.*, tbl_harga_product.detail_product, tbl_konsumen.konsumen_nama, tbl_konsumen.konsumen_nohp');
        $this->db->where('tbl_pembayaran_detail.mesin_id', $divisi);
        $this->db->join('tbl_pembayaran', 'tbl_pembayaran.nospk = tbl_pembayaran_detail.nospk');
        $this->db->join('tbl_harga_product', 'tbl_harga_product.harga_id = tbl_pembayaran_detail.harga_id');
        $this->db->join('tbl_product', 'tbl_product.product_id = tbl_pembayaran_detail.product_id');
        $this->db->join('tbl_bahan', 'tbl_bahan.bahan_id = tbl_product.bahan_id');
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_pembayaran.konsumen_id');
        return $this->db->get('tbl_pembayaran_detail');
    }

    function getTotalTrx($perusahaan_id = null)
    {
        $perusahaan_id = $perusahaan_id ?: $this->session->userdata('perusahaan_id') ?: 0;
        $current_month = date('m');
        $current_year = date('Y');

        $query = "SELECT SUM(p.total_tagihan) as total 
                        FROM tbl_pembayaran p 
                        INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk
                        WHERE pm.perusahaan_id = ? 
                            AND MONTH(p.tgl_bayar) = ? 
                            AND YEAR(p.tgl_bayar) = ?";

        return $this->db->query($query, [$perusahaan_id, $current_month, $current_year]);
    }

    function getTotalNominalTrxharian($perusahaan_id = null)
    {
        $current_date = date('Y-m-d');
        $this->db->select('SUM(p.total_tagihan) as total');
        $this->db->from('tbl_pembayaran p');
        $this->db->join('tbl_pemesanan pm', 'p.nospk = pm.nospk');

        if ($perusahaan_id !== null) {
            $this->db->where('pm.perusahaan_id', $perusahaan_id);
        }

        $this->db->where('DATE(p.tgl_bayar)', $current_date);
        return $this->db->get();
    }

    function getTotalTrxharian($perusahaan_id = null)
    {
        $current_date = date('Y-m-d');
        if ($perusahaan_id !== null) {
            $this->db->where('perusahaan_id', $perusahaan_id);
        }
        $this->db->like('created_at', $current_date);
        return $this->db->get('tbl_pemesanan');
    }

    function get_konsumen_harian($perusahaan_id = null)
    {
        $current_date = date('Y-m-d');
        $this->db->select('tbl_pembayaran.konsumen_id');
        $this->db->from('tbl_pembayaran');
        $this->db->join('tbl_pemesanan', 'tbl_pembayaran.nospk = tbl_pemesanan.nospk');

        if ($perusahaan_id !== null) {
            $this->db->where('tbl_pemesanan.perusahaan_id', $perusahaan_id);
        }

        $this->db->where('DATE(tbl_pembayaran.tgl_bayar)', $current_date);
        $this->db->group_by('tbl_pembayaran.konsumen_id');
        return $this->db->get();
    }



    function transaksi_bulanan($bulan)
    {
        $perusahaan = $this->session->userdata('perusahaan_id') ?: 0;
        $bulanini = date('Y-' . $bulan);

        return $this->db->query("SELECT SUM(p.total_tagihan) as total FROM tbl_pembayaran p 
                                    INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk 
                                    WHERE pm.perusahaan_id = ? AND p.tgl_bayar like ?", [$perusahaan, $bulanini . '%']);
    }

    function getTotalTrxmetode($bayarna)
    {
        $perusahaan = $this->session->userdata('perusahaan_id') ?: 0;
        $bulanini = date('Y-m');
        return $this->db->query("SELECT SUM(p.total_tagihan) as total FROM tbl_pembayaran p 
                                    INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk 
                                    INNER JOIN tbl_pembayaran_detail pd ON p.pembayaran_id = pd.pembayaran_id
                                    WHERE pm.perusahaan_id = ? AND p.tgl_bayar like ? AND pd.metode = ?", [$perusahaan, $bulanini . '%', $bayarna]);
    }

    function getTotalday()
    {
        $perusahaan = $this->session->userdata('perusahaan_id') ?: 0;
        $hariini = date('Y-m-d');
        return $this->db->query("SELECT SUM(p.total_tagihan) as total FROM tbl_pembayaran p 
                                    INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk 
                                    WHERE pm.perusahaan_id = ? AND p.tgl_bayar = ? ", [$perusahaan, $hariini]);
    }

    function gettransaksibulan($perusahaan, $metode)
    {
        // Mendapatkan bulan dan tahun saat ini
        $current_month = date('m'); // Bulan saat ini
        $current_year = date('Y'); // Tahun saat ini

        // Query untuk menghitung total berdasarkan metode pembayaran pada bulan ini
        return $this->db->query("SELECT SUM(pd.jumlah) as total  
                                        FROM tbl_pembayaran p 
                                        INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk
                                        INNER JOIN tbl_pembayaran_detail pd ON p.pembayaran_id = pd.pembayaran_id
                                        WHERE pm.perusahaan_id = $perusahaan 
                                        AND pd.metode = '$metode'
                                        AND MONTH(p.tgl_bayar) = $current_month 
                                        AND YEAR(p.tgl_bayar) = $current_year");
    }

    function gettransaksiharian($perusahaan, $metode)
    {
        // Mendapatkan tanggal saat ini
        $current_date = date('Y-m-d'); // Format tanggal: YYYY-MM-DD

        // Query untuk menghitung total berdasarkan metode pembayaran per hari
        return $this->db->query("SELECT SUM(pd.jumlah) as total  
                                        FROM tbl_pembayaran p 
                                        INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk
                                        INNER JOIN tbl_pembayaran_detail pd ON p.pembayaran_id = pd.pembayaran_id
                                        WHERE pm.perusahaan_id = $perusahaan 
                                        AND pd.metode = '$metode'
                                        AND DATE(p.tgl_bayar) = '$current_date'");
    }



    function getcashharian()
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        $hariini = date('Y-m-d');
        return $this->db->query("SELECT SUM(pd.jumlah) as total FROM tbl_pembayaran p 
                                    INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk
                                    INNER JOIN tbl_pembayaran_detail pd ON p.pembayaran_id = pd.pembayaran_id
                                    WHERE pm.perusahaan_id = $perusahaan AND p.tgl_bayar like'$hariini%' AND pd.metode = 'tunai' ");
    }

    function getpiutangbulan()
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        $hariini = date('Y-m');
        return $this->db->query("SELECT SUM(p.total_tagihan - p.total_bayar) as total FROM tbl_pembayaran p 
                                    INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk
                                    WHERE pm.perusahaan_id = $perusahaan AND p.tgl_bayar like'$hariini%' AND (p.total_tagihan - p.total_bayar) > 0");
    }

    function getpiutangharian()
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        $hariini = date('Y-m-d');
        return $this->db->query("SELECT SUM(p.total_tagihan - p.total_bayar) as total FROM tbl_pembayaran p 
                                            INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk
                                            WHERE pm.perusahaan_id = $perusahaan AND p.tgl_bayar like'$hariini%' AND (p.total_tagihan - p.total_bayar) > 0");
    }

    function gettransferbulan()
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        $hariini = date('Y-m');
        return $this->db->query("SELECT SUM(pd.jumlah) as total FROM tbl_pembayaran p 
                                    INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk
                                    INNER JOIN tbl_pembayaran_detail pd ON p.pembayaran_id = pd.pembayaran_id
                                    WHERE pm.perusahaan_id = $perusahaan AND p.tgl_bayar like'$hariini%' AND pd.metode = 'transfer' ");
    }
    function gettransferharian()
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        $hariini = date('Y-m-d');
        return $this->db->query("SELECT SUM(pd.jumlah) as total FROM tbl_pembayaran p 
                                    INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk
                                    INNER JOIN tbl_pembayaran_detail pd ON p.pembayaran_id = pd.pembayaran_id
                                    WHERE pm.perusahaan_id = $perusahaan AND p.tgl_bayar like'$hariini%' AND pd.metode = 'transfer' ");
    }

    function getedcbulan()
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        $hariini = date('Y-m');
        return $this->db->query("SELECT SUM(pd.jumlah) as total FROM tbl_pembayaran p 
                                    INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk
                                    INNER JOIN tbl_pembayaran_detail pd ON p.pembayaran_id = pd.pembayaran_id
                                    WHERE pm.perusahaan_id = $perusahaan AND p.tgl_bayar like'$hariini%' AND pd.metode = 'debit' ");
    }

    function getedcharian()
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        $hariini = date('Y-m-d');
        return $this->db->query("SELECT SUM(pd.jumlah) as total FROM tbl_pembayaran p 
                                    INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk
                                    INNER JOIN tbl_pembayaran_detail pd ON p.pembayaran_id = pd.pembayaran_id
                                    WHERE pm.perusahaan_id = $perusahaan AND p.tgl_bayar like'$hariini%' AND pd.metode = 'debit' ");
    }

    function getewalletbulan()
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        $hariini = date('Y-m');
        return $this->db->query("SELECT SUM(pd.jumlah) as total FROM tbl_pembayaran p 
                                    INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk
                                    INNER JOIN tbl_pembayaran_detail pd ON p.pembayaran_id = pd.pembayaran_id
                                    WHERE pm.perusahaan_id = $perusahaan AND p.tgl_bayar like'$hariini%' AND pd.metode = 'ewallet' ");
    }

    function getewalletharian()
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        $hariini = date('Y-m-d');
        return $this->db->query("SELECT SUM(pd.jumlah) as total FROM tbl_pembayaran p 
                                    INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk
                                    INNER JOIN tbl_pembayaran_detail pd ON p.pembayaran_id = pd.pembayaran_id
                                    WHERE pm.perusahaan_id = $perusahaan AND p.tgl_bayar like'$hariini%' AND pd.metode = 'ewallet' ");
    }

    function getPiutang()
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        // $this->db->where('tbl_piutang.perusahaan_id', $perusahaan);
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_piutang.konsumen_id');
        $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = tbl_piutang.perusahaan_id');
        $this->db->group_by('tbl_piutang.konsumen_id');
        return $this->db->get('tbl_piutang');
    }


    function get_pemesanan()
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        $this->db->where('perusahaan_id', $perusahaan);
        return $this->db->get('tbl_pemesanan');
    }

    // function getPiutangKonsumen($konsumen){
    //     $this->db->where('tbl_piutang.konsumen_id', $konsumen);
    //     $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_piutang.konsumen_id');
    //     return $this->db->get('tbl_piutang');
    // }

    function getPiutangSPK($nospk)
    {
        $this->db->where('nospk', $nospk);
        return $this->db->get('tbl_piutang');
    }

    function getoldpiutang($konsumen)
    {
        $this->db->where('tbl_piutang.konsumen_id', $konsumen);
        $this->db->where('tbl_piutang.piutang_status', 'BELUM LUNAS');
        $this->db->order_by('tbl_piutang.piutang_tgl', 'desc');
        $this->db->LIMIT(1);
        return $this->db->get('tbl_piutang');
    }

    function updatebayar($idpiutang, $bayarna)
    {
        $this->db->where('id', $idpiutang);
        return $this->db->update('tbl_piutang', $bayarna);
    }

    function totalPiutangKonsumen($konsumen)
    {
        return $this->db->query("SELECT SUM(tbl_piutang.piutang_sisa) as total FROM tbl_piutang WHERE konsumen_id = '$konsumen'");
    }

    function getPengambilanAll()
    {
        $this->db->where('status_transaksi', '3');
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_pembayaran.konsumen_id');
        return $this->db->get('tbl_pembayaran');
    }

    function getPengambilanBarang()
    {
        $divisi = $this->session->userdata('level');
        $this->db->where('status_trx', '2');
        $this->db->join('tbl_harga_product', 'tbl_harga_product.harga_id = tbl_pembayaran_detail.harga_id');
        $this->db->join('tbl_product', 'tbl_product.product_id = tbl_pembayaran_detail.product_id');
        $this->db->join('tbl_bahan', 'tbl_bahan.bahan_id = tbl_product.bahan_id');
        return $this->db->get('tbl_pembayaran_detail');
    }

    function getPemasukan($tanggal_awal, $tanggal_akhir)
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        $this->db->select('p.*, k.konsumen_nama, k.konsumen_nohp');
        $this->db->from('tbl_pembayaran p');
        $this->db->join('tbl_pemesanan pm', 'pm.nospk = p.nospk');
        $this->db->join('tbl_konsumen k', 'k.konsumen_id = pm.konsumen_id');
        $this->db->where('pm.perusahaan_id', $perusahaan);
        $this->db->where('p.tgl_bayar >=', $tanggal_awal);
        $this->db->where('p.tgl_bayar <=', $tanggal_akhir);

        return $this->db->get();
    }

    function getMetodeBayar($tanggal_awal, $tanggal_akhir, $field)
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        $this->db->select('p.*, pd.metode, pd.jumlah');
        $this->db->from('tbl_pembayaran p');
        $this->db->join('tbl_pemesanan pm', 'pm.nospk = p.nospk');
        $this->db->join('tbl_pembayaran_detail pd', 'pd.pembayaran_id = p.pembayaran_id');
        $this->db->where('pm.perusahaan_id', $perusahaan);
        $this->db->where('p.tgl_bayar >=', $tanggal_awal);
        $this->db->where('p.tgl_bayar <=', $tanggal_akhir);

        $this->db->where('pd.metode', $field);
        return $this->db->get();
    }

    function getMetodeBayarharian($hariini, $field)
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        $this->db->select('p.*, pd.metode, pd.jumlah');
        $this->db->from('tbl_pembayaran p');
        $this->db->join('tbl_pemesanan pm', 'pm.nospk = p.nospk');
        $this->db->join('tbl_pembayaran_detail pd', 'pd.pembayaran_id = p.pembayaran_id');
        $this->db->where('pm.perusahaan_id', $perusahaan);
        $this->db->where('p.tgl_bayar', $hariini);
        $this->db->where('pd.metode', $field);
        return $this->db->get();
    }

    function getMetodeBayarbulanan($bulanini, $field)
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        $this->db->select('p.*, pd.metode, pd.jumlah');
        $this->db->from('tbl_pembayaran p');
        $this->db->join('tbl_pemesanan pm', 'pm.nospk = p.nospk');
        $this->db->join('tbl_pembayaran_detail pd', 'pd.pembayaran_id = p.pembayaran_id');
        $this->db->where('pm.perusahaan_id', $perusahaan);
        $this->db->like('p.tgl_bayar', $bulanini, 'after');
        $this->db->where('pd.metode', $field);
        return $this->db->get();
    }

    function totalCash($tanggal_awal, $tanggal_akhir, $table)
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        return $this->db->query("SELECT SUM(pd.jumlah) as totalna FROM tbl_pembayaran p 
                                    INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk
                                    INNER JOIN tbl_pembayaran_detail pd ON p.pembayaran_id = pd.pembayaran_id
                                    WHERE (p.tgl_bayar BETWEEN '$tanggal_awal' AND '$tanggal_akhir')  
                                    AND pm.perusahaan_id = $perusahaan
                                    AND pd.metode = 'tunai'
                                    ");
    }

    function totalCashharian($hariini, $metode, $table)
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        return $this->db->query("SELECT SUM(pd.jumlah) as totalna FROM tbl_pembayaran p 
                                    INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk
                                    INNER JOIN tbl_pembayaran_detail pd ON p.pembayaran_id = pd.pembayaran_id
                                    WHERE (p.tgl_bayar = '$hariini') AND (pm.perusahaan_id = $perusahaan)
                                    AND pd.metode = '$metode'
                                    ");
    }

    function totalCashbulanan($bulanini, $metode, $table)
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        return $this->db->query("SELECT SUM(pd.jumlah) as totalna FROM tbl_pembayaran p 
                                    INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk
                                    INNER JOIN tbl_pembayaran_detail pd ON p.pembayaran_id = pd.pembayaran_id
                                    WHERE (p.tgl_bayar like'$bulanini%') AND pm.perusahaan_id = $perusahaan
                                    AND pd.metode = '$metode'
                                    ");
    }

    function totalPemasukan($tanggal_awal, $tanggal_akhir, $table)
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        return $this->db->query("SELECT SUM(pd.jumlah) as totalna FROM tbl_pembayaran p 
                                    INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk
                                    INNER JOIN tbl_pembayaran_detail pd ON p.pembayaran_id = pd.pembayaran_id
                                    WHERE (p.tgl_bayar BETWEEN '$tanggal_awal' AND '$tanggal_akhir') 
                                    AND pm.perusahaan_id = $perusahaan
                                    AND pd.metode IN ('tunai', 'debit', 'ewallet', 'transfer')
                                    ");
    }

    function totalPemasukanharian($hariini, $table)
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        return $this->db->query("SELECT SUM(pd.jumlah) as totalna FROM tbl_pembayaran p 
                                        INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk
                                        INNER JOIN tbl_pembayaran_detail pd ON p.pembayaran_id = pd.pembayaran_id
                                        WHERE p.tgl_bayar = '$hariini' AND pm.perusahaan_id = $perusahaan
                                        AND pd.metode IN ('tunai', 'debit', 'ewallet', 'transfer')");
    }

    function totalPemasukanbulanan($bulanini, $table)
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        return $this->db->query("SELECT SUM(pd.jumlah) as totalna FROM tbl_pembayaran p 
                                    INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk
                                    INNER JOIN tbl_pembayaran_detail pd ON p.pembayaran_id = pd.pembayaran_id
                                    WHERE p.tgl_bayar like'$bulanini%' AND pm.perusahaan_id = $perusahaan
                                    AND pd.metode IN ('tunai', 'debit', 'ewallet', 'transfer')");
    }

    function getProduksi($nospk)
    {
        $this->db->where('nospk', $nospk);
        $this->db->where('status_trx', '2');
        return $this->db->get('tbl_pembayaran_detail');
    }

    function getPemasukanHarian()
    {
        $hariini = date('Y-m-d');
        $perusahaan = $this->session->userdata('perusahaan_id');

        $this->db->select('p.*, k.konsumen_nama, k.konsumen_nohp');
        $this->db->from('tbl_pembayaran p');
        $this->db->join('tbl_pemesanan pm', 'pm.nospk = p.nospk');
        $this->db->join('tbl_konsumen k', 'k.konsumen_id = pm.konsumen_id');
        $this->db->where('pm.perusahaan_id', $perusahaan);
        $this->db->where('p.tgl_bayar', $hariini);
        return $this->db->get();
    }

    function getpemasukanbulanan($bulanini)
    {
        // $hariini = date('2022-06-04');
        $perusahaan = $this->session->userdata('perusahaan_id');
        $this->db->select('p.*, k.konsumen_nama, k.konsumen_nohp');
        $this->db->from('tbl_pembayaran p');
        $this->db->join('tbl_pemesanan pm', 'pm.nospk = p.nospk');
        $this->db->join('tbl_konsumen k', 'k.konsumen_id = pm.konsumen_id');
        $this->db->where('pm.perusahaan_id', $perusahaan);
        $this->db->like('p.tgl_bayar', $bulanini, 'after');
        return $this->db->get();
    }

    function getpajakbulanan($bulanini)
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        $this->db->select('p.*, k.konsumen_nama, k.konsumen_nohp');
        $this->db->from('tbl_pembayaran p');
        $this->db->join('tbl_pemesanan pm', 'pm.nospk = p.nospk');
        $this->db->join('tbl_konsumen k', 'k.konsumen_id = pm.konsumen_id');
        $this->db->where('pm.perusahaan_id', $perusahaan);
        $this->db->where('(p.total_tagihan - p.total_bayar)', 0);
        $this->db->like('p.tgl_bayar', $bulanini, 'after');
        return $this->db->get();
    }

    function getpendapatan($bulanini, $field)
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        return $this->db->query("SELECT p.*, k.konsumen_nama, k.konsumen_nohp FROM tbl_pembayaran p 
                                    INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk
                                    INNER JOIN tbl_konsumen k ON pm.konsumen_id = k.konsumen_id 
                                    INNER JOIN tbl_pembayaran_detail pd ON p.pembayaran_id = pd.pembayaran_id
                                    WHERE pd.metode = '$field' AND pm.perusahaan_id = $perusahaan AND p.tgl_bayar like'$bulanini%' ");
    }



    function gettransfer($bulanini, $field)
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        return $this->db->query("SELECT p.*, k.konsumen_nama, k.konsumen_nohp, r.nama_bank FROM tbl_pembayaran p 
                                    INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk
                                    INNER JOIN tbl_konsumen k ON pm.konsumen_id = k.konsumen_id 
                                    INNER JOIN tbl_pembayaran_detail pd ON p.pembayaran_id = pd.pembayaran_id
                                    INNER JOIN tbl_rekening r ON pd.bank_id = r.id
                                    WHERE pd.metode = '$field' AND pm.perusahaan_id = $perusahaan
                                    AND p.tgl_bayar like'$bulanini%'");
    }

    function getbayaredcbulan($bulanini, $field)
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        return $this->db->query("SELECT p.*, k.konsumen_nama, k.konsumen_nohp, e.nama_edc FROM tbl_pembayaran p 
                                    INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk
                                    INNER JOIN tbl_konsumen k ON pm.konsumen_id = k.konsumen_id 
                                    INNER JOIN tbl_pembayaran_detail pd ON p.pembayaran_id = pd.pembayaran_id
                                    INNER JOIN tbl_mesin_edc e ON pd.edc_id = e.edc_id
                                    WHERE pd.metode = '$field' AND pm.perusahaan_id = $perusahaan 
                                    AND p.tgl_bayar like'$bulanini%'");
    }

    function getbayaewalletcbulan($bulanini, $field)
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        return $this->db->query("SELECT p.*, k.konsumen_nama, k.konsumen_nohp, ew.nama_ewallet FROM tbl_pembayaran p 
                                    INNER JOIN tbl_pemesanan pm ON p.nospk = pm.nospk
                                    INNER JOIN tbl_konsumen k ON pm.konsumen_id = k.konsumen_id 
                                    INNER JOIN tbl_pembayaran_detail pd ON p.pembayaran_id = pd.pembayaran_id
                                    INNER JOIN tbl_ewallet ew ON pd.ewallet_id = ew.id
                                    WHERE pd.metode = '$field' AND pm.perusahaan_id = $perusahaan
                                    AND p.tgl_bayar like'$bulanini%'");
    }

    function pengeluarantotal($cek)
    {
        $perusahaan = $this->session->userdata('perusahaan_id');

        return $this->db->query("SELECT SUM(pengeluaran_jumlah) as total FROM tbl_pengeluaran 
                                        WHERE pengeluaran_tgl like'$cek%' and perusahaan_id = $perusahaan ");
    }

    function pengeluarantotal_bulanan($cek)
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        return $this->db->query("SELECT SUM(pengeluaran_jumlah) as total FROM tbl_pengeluaran_bulanan 
                                        WHERE pengeluaran_tanggal like '$cek%' and perusahaan_id = $perusahaan");
    }





    function getpengeluaranbulanan($bulanini, $tahunini)
    {
        $this->db->where('pengeluaran_tahun', $tahunini);
        $this->db->where('pengeluaran_bulan', $bulanini);
        $this->db->join('tbl_kategori_pengeluaran', 'tbl_kategori_pengeluaran.id = tbl_pengeluaran_bulanan.kategori_pengeluaran_id');
        return $this->db->get('tbl_pengeluaran_bulanan');
    }

    function jml_transaksi($user)
    {
        return $this->db->query("SELECT COUNT(nospk) as jml FROM tbl_pemesanan WHERE deskprint_id = '$user'");
    }

    function total_transaksi($user)
    {
        $bulanini = date('Y-m');
        return $this->db->query("SELECT COALESCE(SUM(
                                        CASE 
                                            WHEN pd.panjang != 0 AND pd.lebar != 0 THEN 
                                                pd.panjang * pd.lebar * pd.qty * hp.harga_1
                                            ELSE 
                                                pd.qty * hp.harga_1
                                        END
                                    ), 0) as jml 
                                    FROM tbl_pemesanan pm 
                                    JOIN tbl_pemesanan_detail pd ON pm.nospk = pd.nospk 
                                    JOIN tbl_harga_product hp ON pd.harga_id = hp.harga_id
                                    WHERE pm.deskprint_id = '$user' 
                                    AND DATE_FORMAT(pm.created_at, '%Y-%m') = '$bulanini'");
    }

    function total_trx($nospk)
    {
        return $this->db->query("SELECT 
                SUM(
                    IF(
                        t.temp_panjang != 0 AND t.temp_lebar != 0,
                        t.temp_panjang * t.temp_lebar * t.temp_qty * h.harga_1,
                        t.temp_qty * h.harga_1
                    )
                ) AS total_pemesanan
                FROM tbl_temp_transaksi t
                JOIN tbl_harga_product h ON t.harga_id = h.harga_id
                WHERE t.no_spk = '$nospk' 
            ");
    }

    function get_sub_category($category_id)
    {
        $this->db->where('kat_finishing_id', $category_id);
        return $this->db->get('tbl_product');
    }

    function get_finishing($id)
    {
        $hasil = $this->db->query("SELECT * FROM tbl_harga_product WHERE product_id ='$id'");
        return $hasil->result();
    }

    function get_largeformat($id)
    {
        $hasil = $this->db->query("SELECT * FROM tbl_bahan WHERE kategori_kode ='$id'");
        return $hasil->result();
    }

    function getProduksina($operator_id = null)
    {
        $perusahaan = $this->session->userdata('perusahaan_id');

        // Filter berdasarkan perusahaan jika bukan perusahaan dengan id '1'
        if ($perusahaan <> '1') {
            $this->db->where('tbl_produksi.lokasi_cetak', $perusahaan);
        }

        // Filter berdasarkan operator_id jika diberikan
        // if($operator_id !== null && $operator_id > 0){
        //     $this->db->where('tbl_produksi.operator_id', $operator_id);
        // }

        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_produksi.konsumen_id');
        $this->db->join('tbl_product', 'tbl_product.product_id = tbl_produksi.product_id');
        $this->db->join('routing', 'routing.product_id = tbl_product.product_id');

        $this->db->order_by('tbl_produksi.produksi_id', 'desc');


        return $this->db->get('tbl_produksi');
    }

    function getProduksina1($produksi_id, $routing_urutan)
    {
        $perusahaan = $this->session->userdata('perusahaan_id');

        // Filter berdasarkan perusahaan jika bukan perusahaan dengan id '1'
        if ($perusahaan <> '1') {
            $this->db->where('tbl_produksi.lokasi_cetak', $perusahaan);
        }

        // Melakukan JOIN antar tabel
        $this->db->where('produksi_id', $produksi_id);
        $this->db->where('routing_urutan', $routing_urutan);
        $this->db->join('tbl_product', 'tbl_product.product_id = tbl_produksi.product_id');
        $this->db->join('routing', 'routing.product_id = tbl_product.product_id');
        $this->db->join('tbl_bahan', 'tbl_bahan.bahan_id = tbl_product.bahan_id');
        $this->db->join('tbl_operator', 'tbl_operator.operator_id = routing.operator_id');
        $this->db->join('tbl_spk', 'tbl_spk.nospk = tbl_produksi.nospk');
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_spk.konsumen_id');
        $this->db->join('tbl_harga_product', 'tbl_harga_product.harga_id = tbl_produksi.harga_id');
        $this->db->join('tbl_satuan', 'tbl_satuan.satuan_id = tbl_product.satuan_id');

        // Menggunakan DISTINCT untuk menghindari data duplikat
        $this->db->distinct();

        // Mengurutkan berdasarkan ID produksi secara menurun
        $this->db->order_by('produksi_id', 'desc');

        // Menambahkan GROUP BY jika ingin mengelompokkan berdasarkan nospk
        // $this->db->group_by('routing.routing_id');

        return $this->db->get('tbl_produksi');
    }





    function getPB()
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        $this->db->where('tbl_produksi.lokasi_cetak', $perusahaan);
        $this->db->where('tbl_produksi.status_produksi', '3');
        $this->db->join('tbl_product', 'tbl_product.product_id = tbl_produksi.product_id');
        $this->db->join('routing', 'routing.product_id = tbl_product.product_id');
        $this->db->join('tbl_bahan', 'tbl_bahan.bahan_id = tbl_product.bahan_id');
        // $this->db->join('tbl_operator','tbl_operator.operator_id = tbl_produksi.operator_id');
        $this->db->join('tbl_spk', 'tbl_spk.nospk = tbl_produksi.nospk');
        $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_spk.konsumen_id');
        $this->db->join('tbl_harga_product', 'tbl_harga_product.harga_id = tbl_produksi.harga_id');
        $this->db->join('tbl_satuan', 'tbl_satuan.satuan_id = tbl_product.satuan_id');

        $this->db->order_by('produksi_id', 'desc');
        // $this->db->group_by('tbl_produksi.nospk');
        return $this->db->get('tbl_produksi');
    }


    function getOPProduksina($op)
    {
        $this->db->join('tbl_product', 'tbl_product.product_id = tbl_produksi.product_id');
        $this->db->where('tbl_product.step_2', $op);
        $this->db->join('tbl_bahan', 'tbl_bahan.bahan_id = tbl_product.bahan_id');
        // $this->db->join('tbl_operator','tbl_operator.operator_id = tbl_produksi.operator_id');
        $this->db->order_by('produksi_id', 'desc');
        return $this->db->get('tbl_produksi');
    }


    function detailProduksina($id)
    {
        $this->db->where('produksi_id', $id);
        //$this->db->join('tbl_harga_product','tbl_harga_product.product_id = tbl_produksi.product_id');
        $this->db->join('tbl_product', 'tbl_product.product_id = tbl_produksi.product_id');
        // $this->db->join('tbl_bahan', 'tbl_bahan.bahan_id = tbl_product.bahan_id');
        // $this->db->join('tbl_operator','tbl_operator.operator_id = tbl_produksi.operator_id');
        //$this->db->group_by('tbl_produksi.product_id');
        return $this->db->get('tbl_produksi');
    }

    function detailpengambilan($nospk)
    {
        $this->db->where('nospk', $nospk);
        //$this->db->join('tbl_harga_product','tbl_harga_product.product_id = tbl_produksi.product_id');
        $this->db->join('tbl_product', 'tbl_product.product_id = tbl_produksi.product_id');
        $this->db->join('tbl_bahan', 'tbl_bahan.bahan_id = tbl_product.bahan_id');
        $this->db->join('tbl_operator', 'tbl_operator.operator_id = tbl_produksi.operator_id');
        //$this->db->group_by('tbl_produksi.product_id');
        return $this->db->get('tbl_produksi');
    }

    function detailproduk($id)
    {
        $this->db->select('tbl_produksi.*, tbl_product.product_nama, tbl_harga_product.detail_product, tbl_harga_product.harga_1, tbl_harga_product.harga_aktif');
        $this->db->from('tbl_produksi');
        $this->db->join('tbl_product', 'tbl_product.product_id = tbl_produksi.product_id');
        $this->db->join('tbl_harga_product', 'tbl_harga_product.harga_id = tbl_produksi.harga_id', 'left');
        $this->db->where('tbl_produksi.nospk', $id);
        return $this->db->get();
    }


    function cekproduksi($id)
    {
        $this->db->where('produksi_id', $id);
        return $this->db->get('tbl_produksi');
    }



    function getkonsumen($id, $nospk = null)
    {
        $this->db->select('tbl_konsumen.*, tbl_spk.nospk, tbl_spk.metode_pesan as id_jenis_transaksi, tbl_spk.id_marketplace as marketplace_id, tbl_spk.resi_marketplace, tbl_spk.id_pengiriman, tbl_spk.batch_pengiriman, tbl_jenis_transaksi.nama_jenis_transaksi, tbl_marketplace.marketplace_nama, tbl_pengiriman.nama_pengiriman');
        $this->db->from('tbl_konsumen');

        if ($nospk) {
            $this->db->join('tbl_spk', 'tbl_spk.konsumen_id = tbl_konsumen.konsumen_id');
            $this->db->where('tbl_spk.nospk', $nospk);
        } else {
            // Fallback to latest SPK for this consumer if nospk not provided
            $this->db->join('tbl_spk', 'tbl_spk.konsumen_id = tbl_konsumen.konsumen_id', 'left');
            $this->db->where('tbl_konsumen.konsumen_id', $id);
            $this->db->order_by('tbl_spk.nospk', 'DESC');
        }

        $this->db->join('tbl_jenis_transaksi', 'tbl_jenis_transaksi.id_jenis_transaksi = tbl_spk.metode_pesan', 'left');
        $this->db->join('tbl_marketplace', 'tbl_marketplace.marketplace_id = tbl_spk.id_marketplace', 'left');
        $this->db->join('tbl_pengiriman', 'tbl_pengiriman.id_pengiriman = tbl_spk.id_pengiriman', 'left');

        if ($nospk) {
            return $this->db->get();
        } else {
            $this->db->limit(1);
            return $this->db->get();
        }
    }

    public function getPiutangKonsumen()
    {
        $this->db->select('
                  p.nospk,
                  p.tgl_bayar,
                  p.jam_bayar,
                  k.konsumen_id,
                  k.konsumen_nama,
                  k.konsumen_nohp,
                  k.konsumen_alamat,
                  p.total_tagihan as total_transaksi,
                  (p.total_tagihan - p.total_bayar) as sisa_piutang,
                  p.total_bayar as total_bayar
                  
              ');
        $this->db->from('tbl_pembayaran p');
        $this->db->join('tbl_pemesanan pm', 'pm.nospk = p.nospk');
        $this->db->join('tbl_konsumen k', 'k.konsumen_id = pm.konsumen_id');
        $this->db->where('(p.total_tagihan - p.total_bayar) > 0');
        $this->db->order_by('p.tgl_bayar', 'DESC');
        return $this->db->get();
    }

    public function getPiutangGrouped($access_params = [])
    {
        // Use a subquery to consolidate nospk if duplicates exist to prevent double counting
        $subquery = "(SELECT nospk, MAX(total_tagihan) as total_tagihan, MAX(total_bayar) as total_bayar FROM tbl_pembayaran GROUP BY nospk) p";

        $this->db->select('
                  k.konsumen_id,
                  k.konsumen_nama,
                  k.konsumen_nohp,
                  k.konsumen_alamat,
                  SUM(p.total_tagihan - p.total_bayar) as total_piutang,
                  COUNT(p.nospk) as jumlah_nota
              ');
        $this->db->from($subquery);
        $this->db->join('tbl_pemesanan pm', 'pm.nospk = p.nospk');
        $this->db->join('tbl_konsumen k', 'k.konsumen_id = pm.konsumen_id');
        $this->db->where('(p.total_tagihan - p.total_bayar) > 0');
        
        if (!empty($access_params['filter_branch']) && $access_params['filter_branch'] == true) {
            if (!empty($access_params['filter_by_user']) && $access_params['filter_by_user'] == true) {
                $this->db->where('pm.perusahaan_id', $access_params['perusahaan_id']);
            } else {
                $this->db->where('pm.perusahaan_id', $access_params['branch_id']);
            }
        }
        
        $this->db->group_by('k.konsumen_id');
        $this->db->order_by('total_piutang', 'DESC');
        return $this->db->get();
    }

    public function getPiutangDetail($konsumen_id, $access_params = [])
    {
        $subquery = "(SELECT nospk, MAX(total_tagihan) as total_tagihan, MAX(total_bayar) as total_bayar, MAX(tgl_bayar) as tgl_bayar, MAX(jam_bayar) as jam_bayar FROM tbl_pembayaran GROUP BY nospk) p";
        $this->db->select('
                  p.nospk,
                  p.tgl_bayar,
                  p.jam_bayar,
                  p.total_tagihan as total_transaksi,
                  (p.total_tagihan - p.total_bayar) as sisa_piutang,
                  p.total_bayar as total_bayar
              ');
        $this->db->from($subquery);
        $this->db->join('tbl_pemesanan pm', 'pm.nospk = p.nospk');
        $this->db->where('pm.konsumen_id', $konsumen_id);
        
        if (!empty($access_params['filter_branch']) && $access_params['filter_branch'] == true) {
            if (!empty($access_params['filter_by_user']) && $access_params['filter_by_user'] == true) {
                $this->db->where('pm.perusahaan_id', $access_params['perusahaan_id']);
            } else {
                $this->db->where('pm.perusahaan_id', $access_params['branch_id']);
            }
        }
        
        // Remove restriction: allow all history to be shown so we can print paid invoices
        // $this->db->where('(p.total_tagihan - p.total_bayar) > 0'); 
        $this->db->group_by('p.nospk');
        $this->db->order_by('p.tgl_bayar', 'ASC');
        return $this->db->get();
    }

    public function getTotalPiutang($access_params = [])
    {
        // Subquery to handle potential duplicates at the source
        $subquery = "(SELECT nospk, MAX(total_tagihan) as total_tagihan, MAX(total_bayar) as total_bayar FROM tbl_pembayaran GROUP BY nospk) p";
        $this->db->select('SUM(p.total_tagihan - p.total_bayar) as total_piutang');
        $this->db->from($subquery);
        $this->db->join('tbl_pemesanan pm', 'pm.nospk = p.nospk');
        $this->db->where('(p.total_tagihan - p.total_bayar) > 0');
        
        if (!empty($access_params['filter_branch']) && $access_params['filter_branch'] == true) {
            if (!empty($access_params['filter_by_user']) && $access_params['filter_by_user'] == true) {
                $this->db->where('pm.perusahaan_id', $access_params['perusahaan_id']);
            } else {
                $this->db->where('pm.perusahaan_id', $access_params['branch_id']);
            }
        }
        
        $result = $this->db->get()->row();
        return $result ? $result->total_piutang : 0;
    }

    public function getKonsumenBerpiutang($access_params = [])
    {
        $subquery = "(SELECT nospk, MAX(total_tagihan) as total_tagihan, MAX(total_bayar) as total_bayar FROM tbl_pembayaran GROUP BY nospk) p";
        $this->db->select('DISTINCT(pm.konsumen_id)');
        $this->db->from($subquery);
        $this->db->join('tbl_pemesanan pm', 'pm.nospk = p.nospk');
        $this->db->where('(p.total_tagihan - p.total_bayar) > 0');
        
        if (!empty($access_params['filter_branch']) && $access_params['filter_branch'] == true) {
            if (!empty($access_params['filter_by_user']) && $access_params['filter_by_user'] == true) {
                $this->db->where('pm.perusahaan_id', $access_params['perusahaan_id']);
            } else {
                $this->db->where('pm.perusahaan_id', $access_params['branch_id']);
            }
        }
        
        return $this->db->get();
    }

    public function getPiutangLunasGrouped($access_params = [])
    {
        // Consolidate paid debts
        $subquery = "(SELECT nospk, MAX(total_tagihan) as total_tagihan, MAX(total_bayar) as total_bayar FROM tbl_pembayaran GROUP BY nospk) p";

        $this->db->select('
                  k.konsumen_id,
                  k.konsumen_nama,
                  k.konsumen_nohp,
                  k.konsumen_alamat,
                  COUNT(p.nospk) as jumlah_nota
              ');
        $this->db->from($subquery);
        $this->db->join('tbl_pemesanan pm', 'pm.nospk = p.nospk');
        $this->db->join('tbl_konsumen k', 'k.konsumen_id = pm.konsumen_id');
        // Logic: fully paid or overpaid
        $this->db->where('(p.total_tagihan - p.total_bayar) <= 0.5');
        
        if (!empty($access_params['filter_branch']) && $access_params['filter_branch'] == true) {
            if (!empty($access_params['filter_by_user']) && $access_params['filter_by_user'] == true) {
                $this->db->where('pm.perusahaan_id', $access_params['perusahaan_id']);
            } else {
                $this->db->where('pm.perusahaan_id', $access_params['branch_id']);
            }
        }
        
        $this->db->group_by('k.konsumen_id');
        $this->db->order_by('k.konsumen_nama', 'ASC');
        return $this->db->get();
    }

    public function getKonsumenLunas($access_params = [])
    {
        $subquery = "(SELECT nospk, MAX(total_tagihan) as total_tagihan, MAX(total_bayar) as total_bayar FROM tbl_pembayaran GROUP BY nospk) p";
        $this->db->select('DISTINCT(pm.konsumen_id)');
        $this->db->from($subquery);
        $this->db->join('tbl_pemesanan pm', 'pm.nospk = p.nospk');
        $this->db->where('(p.total_tagihan - p.total_bayar) <= 0.5');
        
        if (!empty($access_params['filter_branch']) && $access_params['filter_branch'] == true) {
            if (!empty($access_params['filter_by_user']) && $access_params['filter_by_user'] == true) {
                $this->db->where('pm.perusahaan_id', $access_params['perusahaan_id']);
            } else {
                $this->db->where('pm.perusahaan_id', $access_params['branch_id']);
            }
        }
        
        return $this->db->get();
    }

    /**
     * Delete SPK and all its descendants (related tables)
     */
    function delete_spk_complete($nospk)
    {
        $this->db->trans_start();

        try {
            // Helper function to check table existence bypassing CI3 cache
            $has_table = function ($table) {
                $q = $this->db->query("SHOW TABLES LIKE '$table'");
                return ($q && $q->num_rows() > 0);
            };

            // 1. Delete detail_produksi (requires produksi_id)
            if ($has_table('tbl_produksi')) {
                $produksi = $this->db->select('produksi_id')->get_where('tbl_produksi', ['nospk' => $nospk])->result();
                foreach ($produksi as $p) {
                    if ($has_table('detail_produksi')) {
                        $this->db->delete('detail_produksi', ['produksi_id' => $p->produksi_id]);
                    }
                }
            }

            // 2. Delete tbl_piutang_pembayaran / tbl_pembayaran_piutang (requires piutang_id or nospk)
            if ($has_table('tbl_piutang')) {
                // Ensure nospk exists in tbl_piutang before selecting
                $q_piutang = $this->db->query("SHOW COLUMNS FROM `tbl_piutang` LIKE 'nospk'");
                if ($q_piutang && $q_piutang->num_rows() > 0) {
                    $piutang = $this->db->select('piutang_id')->get_where('tbl_piutang', ['nospk' => $nospk])->result();
                    foreach ($piutang as $pi) {
                        if ($has_table('tbl_piutang_pembayaran')) {
                            $this->db->delete('tbl_piutang_pembayaran', ['piutang_id' => $pi->piutang_id]);
                        }
                    }
                }
            }

            // 3. Delete tbl_pembayaran_detail (requires pembayaran_id)
            if ($has_table('tbl_pembayaran')) {
                $pembayaran = $this->db->select('pembayaran_id')->get_where('tbl_pembayaran', ['nospk' => $nospk])->result();
                foreach ($pembayaran as $pm) {
                    if ($has_table('tbl_pembayaran_detail')) {
                        $this->db->delete('tbl_pembayaran_detail', ['pembayaran_id' => $pm->pembayaran_id]);
                    }
                }
            }

            // 4. Delete tables that use no_spk directly
            if ($has_table('tbl_temp_transaksi')) {
                $this->db->delete('tbl_temp_transaksi', ['no_spk' => $nospk]);
            }

            // 5. Delete tables that use nospk directly (Order: Child -> Parent)
            $direct_tables = [
                'tbl_pembayaran_produk',
                'tbl_pembayaran_piutang',
                'tbl_piutang',
                'tbl_pembayaran',
                'tbl_produksi',
                'tbl_pengambilan_detail',
                'tbl_pengambilan',
                'tbl_pemesanan_detail',
                'tbl_spk',
                'tbl_pemesanan'
            ];

            foreach ($direct_tables as $table) {
                if ($has_table($table)) {
                    // Force check column using direct query to bypass CI field_exists cache
                    $field_exists = $this->db->query("SHOW COLUMNS FROM `$table` LIKE 'nospk'");
                    if ($field_exists && $field_exists->num_rows() > 0) {
                        $this->db->delete($table, ['nospk' => $nospk]);
                    }
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Error deleting SPK #' . $nospk . ': ' . $e->getMessage());
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }


}

?>