<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produksi_model extends CI_Model
{

    // Ambil semua data produksi beserta detailnya
    public function get_all_produksi($operator_id = null)
    {
        $this->db->select('p.*, k.konsumen_nama, pr.product_nama, s.satuan_nama');
        $this->db->from('tbl_produksi p');
        // $this->db->join('detail_produksi dp', 'p.produksi_id = dp.produksi_id', 'left');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->join('tbl_product pr', 'p.product_id = pr.product_id', 'left');
        $this->db->join('tbl_satuan s', 'pr.satuan_id = s.satuan_id', 'left');
        // $this->db->join('tbl_mesin m', 'dp.mesin_id = m.mesin_id', 'left');
        // $this->db->join('tbl_operator o', 'dp.operator_id = o.operator_id', 'left');

        $this->db->join('tbl_pemesanan pm', 'p.nospk = pm.nospk', 'inner');
        $this->db->where('pm.status_pembayaran', 'paid');

        // Filter by operator_id if provided (for operator users)
        // Join with detail_produksi because operator_id is there
        if ($operator_id !== null) {
            $this->db->join('detail_produksi dp', 'p.produksi_id = dp.produksi_id', 'inner');
            $this->db->where('dp.operator_id', $operator_id);
        }

        $this->db->order_by('p.produksi_id', 'DESC');
        $this->db->group_by('p.produksi_id'); // Group by produksi_id instead of nospk to avoid missing items
        return $this->db->get()->result();
    }

    // Ambil produksi berdasarkan ID (header only)
    public function get_produksi_by_id($produksi_id)
    {
        $this->db->select('p.*, k.konsumen_nama, pr.product_nama, pr.kategori_id, s.satuan_nama, hp.detail_product, hp.jenisprint_id');
        $this->db->from('tbl_produksi p');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->join('tbl_product pr', 'p.product_id = pr.product_id', 'left');
        $this->db->join('tbl_satuan s', 'pr.satuan_id = s.satuan_id', 'left');
        $this->db->join('tbl_harga_product hp', 'p.harga_id = hp.harga_id', 'left');
        $this->db->where('p.produksi_id', $produksi_id);
        return $this->db->get()->row();
    }

    // Ambil detail produksi berdasarkan ID produksi
    // Ambil detail produksi berdasarkan ID produksi
    public function get_detail_produksi_by_id($produksi_id, $operator_id = null)
    {
        $this->db->select('dp.*, m.mesin_nama, pr.product_nama, bl.quantity, bh.bahan_nama as bahan_nama, o.operator_nama, r.routing_name, p.nama_perusahaan');
        $this->db->from('detail_produksi dp');
        $this->db->join('tbl_mesin m', 'dp.mesin_id = m.mesin_id', 'left');
        $this->db->join('tbl_operator o', 'dp.operator_id = o.operator_id', 'left');
        $this->db->join('routing r', 'dp.routing_id = r.routing_id', 'left');
        $this->db->join('bom_lines bl', 'bl.bom_line_id = r.bom_line_id', 'left');
        $this->db->join('tbl_bahan bh', 'bh.bahan_id = bl.bahan_id', 'left');
        $this->db->join('tbl_product pr', 'pr.product_id = bl.product_id', 'left');
        $this->db->join('tbl_perusahaan p', 'p.id_perusahaan = dp.lokasi_proses', 'left');
        $this->db->where('dp.produksi_id', $produksi_id);

        // Filter by operator_id if provided (for operator users)
        if ($operator_id !== null) {
            $this->db->where('dp.operator_id', $operator_id);
        }

        $this->db->order_by('dp.routing_urutan', 'ASC');
        return $this->db->get()->result();
    }

    public function get_summary($operator_id = null, $perusahaan_id = null)
    {
        $this->db->select('
            COUNT(DISTINCT CASE WHEN p.status_produksi = "approved" THEN p.nospk END) as total_queue,
            COUNT(DISTINCT CASE WHEN p.status_produksi = "proses" THEN p.nospk END) as processing,
            COUNT(DISTINCT CASE WHEN p.status_produksi = "on_pb" THEN p.nospk END) as on_pb,
            COUNT(DISTINCT CASE WHEN p.status_produksi = "on_costumer" THEN p.nospk END) as done,
            COUNT(DISTINCT CASE WHEN p.status_produksi = "done" AND DATE(p.update_time) = CURDATE() THEN p.nospk END) as done_today
        ');
        $this->db->from('tbl_produksi p');
        $this->db->join('tbl_pemesanan pm', 'p.nospk = pm.nospk', 'inner');
        $this->db->where('pm.status_pembayaran', 'paid');

        if ($operator_id !== null) {
            $this->db->join('detail_produksi dp', 'p.produksi_id = dp.produksi_id', 'inner');
            $this->db->where('dp.operator_id', $operator_id);
        }

        if ($perusahaan_id !== null) {
            $this->db->where('p.perusahaan_id', $perusahaan_id);
        }

        $summary = $this->db->get()->row();

        // Add Lead Time calculation
        $summary->avg_lead_time = $this->get_average_lead_time($perusahaan_id);

        return $summary;
    }

    public function get_kinerja_operator($start_date = null, $end_date = null)
    {
        $this->db->select("
            o.operator_id,
            o.operator_nama,
            COUNT(dp.detail_produksi_id) as total_tugas,
            SUM(CASE WHEN dp.status_produksi = '0' THEN 1 ELSE 0 END) as belum_dikerjakan,
            SUM(CASE WHEN dp.status_produksi = '1' THEN 1 ELSE 0 END) as sedang_dikerjakan,
            SUM(CASE WHEN dp.status_produksi = '2' THEN 1 ELSE 0 END) as selesai
        ");
        $this->db->from('tbl_operator o');
        $this->db->join('detail_produksi dp', 'o.operator_id = dp.operator_id', 'left');
        
        if ($start_date && $end_date) {
            $this->db->join('tbl_produksi p', 'dp.produksi_id = p.produksi_id', 'left');
            $this->db->join('tbl_pemesanan pm', 'p.nospk = pm.nospk', 'left');
            $this->db->where("DATE(pm.created_at) >=", $start_date);
            $this->db->where("DATE(pm.created_at) <=", $end_date);
        }
        
        $this->db->group_by('o.operator_id, o.operator_nama');
        
        return $this->db->get()->result_array();
    }

    /**
     * Get Average Lead Time in hours (from created to finished/on_pb)
     */
    public function get_average_lead_time($perusahaan_id = null)
    {
        $this->db->select('AVG(TIMESTAMPDIFF(HOUR, pm.created_at, p.update_time)) as avg_hours');
        $this->db->from('tbl_produksi p');
        $this->db->join('tbl_pemesanan pm', 'p.nospk = pm.nospk', 'inner');
        $this->db->where_in('p.status_produksi', ['done', 'on_pb', 'on_costumer']); // Completed or on PB

        if ($perusahaan_id) {
            $this->db->where('p.perusahaan_id', $perusahaan_id);
        }

        $res = $this->db->get()->row();
        return $res ? round($res->avg_hours, 1) : 0;
    }

    // Data produksi yang memiliki minimal satu routing berstatus PB (status_produksi = '3')
    public function get_produksi_pb()
    {
        $this->db->select('p.*, k.konsumen_nama, pr.product_nama, s.satuan_nama');
        $this->db->from('tbl_produksi p');
        $this->db->join('detail_produksi dp', 'p.produksi_id = dp.produksi_id', 'inner');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->join('tbl_product pr', 'p.product_id = pr.product_id', 'left');
        $this->db->join('tbl_satuan s', 'pr.satuan_id = s.satuan_id', 'left');
        $this->db->where('p.status_produksi', 'on_pb');


        $this->db->group_by('p.produksi_id');
        $this->db->order_by('p.produksi_id', 'DESC');
        return $this->db->get()->result();
    }

    // Daftar detail_produksi yang berstatus PB (status_produksi = '3')
    public function get_detail_pb()
    {
        $this->db->select('p.produksi_id, p.nospk, k.konsumen_nama, pem.status_pembayaran, pem.created_at as tgl_spk, p.tgl_kirim_pb, p.perusahaan_id, tp.nama_perusahaan as asal_cabang');
        $this->db->select('jt.nama_jenis_transaksi, tm.marketplace_nama');
        $this->db->select('pi.status as piutang_status');
        $this->db->select('COUNT(p.produksi_id) as total_items');
        $this->db->select('SUM(p.qty) as total_qty');
        // Logic: Try to get SUM of last step qty_output. If that is 0, fallback to p.qty (the ordered qty).
        $this->db->select('(SELECT SUM(
                                COALESCE(
                                    NULLIF((SELECT dp_sub.qty_output FROM detail_produksi dp_sub WHERE dp_sub.produksi_id = p_inner.produksi_id ORDER BY dp_sub.routing_urutan DESC LIMIT 1), 0),
                                    p_inner.qty
                                )
                            )
                            FROM tbl_produksi p_inner
                            WHERE p_inner.nospk = p.nospk
                           ) as total_qty_output', FALSE);
        $this->db->from('tbl_produksi p');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->join('tbl_pemesanan pem', 'pem.nospk = p.nospk', 'left');
        $this->db->join('tbl_jenis_transaksi jt', 'jt.id_jenis_transaksi = pem.id_jenis_transaksi', 'left');
        $this->db->join('tbl_marketplace tm', 'tm.marketplace_id = pem.marketplace_id', 'left');
        $this->db->join('tbl_piutang pi', 'pi.nospk = p.nospk', 'left');
        $this->db->join('tbl_perusahaan tp', 'tp.id_perusahaan = p.perusahaan_id', 'left');
        $this->db->where('p.status_produksi', 'on_pb');


        $this->db->group_by('p.nospk');
        $this->db->order_by('p.tgl_kirim_pb', 'DESC');

        return $this->db->get()->result();
    }

    // Data produksi yang sudah diserahkan ke konsumen (status_produksi = '4')
    public function get_produksi_handover()
    {
        $this->db->select('p.*, k.konsumen_nama, pr.product_nama, s.satuan_nama');
        $this->db->from('tbl_produksi p');
        $this->db->join('detail_produksi dp', 'p.produksi_id = dp.produksi_id', 'inner');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->join('tbl_product pr', 'p.product_id = pr.product_id', 'left');
        $this->db->join('tbl_satuan s', 'pr.satuan_id = s.satuan_id', 'left');
        $this->db->where('p.status_produksi', 'on_costumer');


        $this->db->group_by('p.produksi_id');
        $this->db->order_by('dp.waktu_selesai', 'DESC');
        return $this->db->get()->result();
    }

    // Daftar detail_produksi yang sudah diserahkan ke konsumen (status_produksi = '4')
    public function get_detail_handover()
    {
        $this->db->select('p.nospk, k.konsumen_nama, pem.status_pembayaran, pem.created_at as tgl_spk, MAX(p.catatan) as catatan, p.perusahaan_id, MAX(p.foto_handover) as foto_handover, tp.nama_perusahaan as asal_cabang');
        $this->db->select('MAX(jt.nama_jenis_transaksi) as nama_jenis_transaksi, MAX(tm.marketplace_nama) as marketplace_nama');
        $this->db->select('MAX(pi.status) as piutang_status');
        $this->db->select('COUNT(p.produksi_id) as total_items');
        $this->db->select('MAX(p.update_time) as max_update');
        $this->db->from('tbl_produksi p');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->join('tbl_pemesanan pem', 'pem.nospk = p.nospk', 'left');
        $this->db->join('tbl_jenis_transaksi jt', 'jt.id_jenis_transaksi = pem.id_jenis_transaksi', 'left');
        $this->db->join('tbl_marketplace tm', 'tm.marketplace_id = pem.marketplace_id', 'left');
        $this->db->join('tbl_piutang pi', 'pi.nospk = p.nospk', 'left');
        $this->db->join('tbl_perusahaan tp', 'tp.id_perusahaan = p.perusahaan_id', 'left');
        $this->db->where('p.status_produksi', 'on_costumer');


        $this->db->group_by('p.nospk');
        $this->db->order_by('max_update', 'DESC');
        // Limit removed to ensure accurate counts in the summary box
        return $this->db->get()->result();
    }

    // Ambil produksi berdasarkan nomor SPK
    public function get_produksi_by_spk($nospk)
    {
        $this->db->select('p.*, k.konsumen_nama, pr.product_nama, s.satuan_nama, pm.marketplace_id, pm.resi_marketplace, pm.batch_pengiriman, m.marketplace_nama, exp.nama_pengiriman');
        $this->db->from('tbl_produksi p');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->join('tbl_product pr', 'p.product_id = pr.product_id', 'left');
        $this->db->join('tbl_satuan s', 'pr.satuan_id = s.satuan_id', 'left');
        $this->db->join('tbl_pemesanan pm', 'p.nospk = pm.nospk', 'left');
        $this->db->join('tbl_marketplace m', 'pm.marketplace_id = m.marketplace_id', 'left');
        $this->db->join('tbl_pengiriman exp', 'pm.id_pengiriman = exp.id_pengiriman', 'left');
        $this->db->where('p.nospk', $nospk);
        return $this->db->get()->row();
    }

    // Ambil single detail produksi berdasarkan detail_produksi_id
    public function get_detail_produksi_by_id_single($detail_produksi_id)
    {
        $this->db->select('dp.*, m.mesin_nama, pr.product_nama, bl.quantity, bh.varian as bahan_nama, o.operator_nama, r.routing_name, p.nama_perusahaan, k.konsumen_nama');
        $this->db->from('detail_produksi dp');
        $this->db->join('tbl_mesin m', 'dp.mesin_id = m.mesin_id', 'left');
        $this->db->join('tbl_operator o', 'dp.operator_id = o.operator_id', 'left');
        $this->db->join('routing r', 'dp.routing_id = r.routing_id', 'left');
        $this->db->join('bom_lines bl', 'bl.bom_line_id = r.bom_line_id', 'left');
        $this->db->join('tbl_material bh', 'bh.material_id = bl.bahan_id', 'left');
        $this->db->join('tbl_product pr', 'pr.product_id = prod.product_id', 'left');
        $this->db->join('tbl_perusahaan p', 'p.id_perusahaan = dp.lokasi_proses', 'left');
        $this->db->join('tbl_produksi prod', 'dp.produksi_id = prod.produksi_id', 'left');
        $this->db->join('tbl_konsumen k', 'prod.konsumen_id = k.konsumen_id', 'left');
        $this->db->where('dp.detail_produksi_id', $detail_produksi_id);
        return $this->db->get()->row();
    }

    // Ambil data WIP untuk display antrian
    public function get_wip_display($limit = 10)
    {
        $this->db->select('p.nospk, k.konsumen_nama, pr.product_nama');
        $this->db->from('tbl_produksi p');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->join('tbl_product pr', 'p.product_id = pr.product_id', 'left');
        $this->db->where('p.status_produksi', 'proses');

        $perusahaan = $this->session->userdata('perusahaan_id');
        if ($perusahaan) {
            $this->db->where('p.perusahaan_id', $perusahaan);
        }

        $this->db->group_by('p.nospk'); // Group by SPK to show unique jobs not every item
        $this->db->order_by('p.update_time', 'DESC'); // Show latest updated first
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    // Helper: Ambil breakdown quantity per satuan untuk display UI
    public function get_qty_breakdown($nospk)
    {
        $this->db->select('pr.product_nama, s.satuan_nama');
        $this->db->select('SUM(p.qty) as total_qty');

        // Calculate ready qty using the same logic as get_detail_pb
        $this->db->select('SUM(
                                COALESCE(
                                    NULLIF((SELECT dp_sub.qty_output FROM detail_produksi dp_sub WHERE dp_sub.produksi_id = p.produksi_id ORDER BY dp_sub.routing_urutan DESC LIMIT 1), 0),
                                    p.qty
                                )
                           ) as total_ready', FALSE);

        $this->db->from('tbl_produksi p');
        $this->db->join('tbl_product pr', 'p.product_id = pr.product_id', 'left');
        $this->db->join('tbl_satuan s', 'pr.satuan_id = s.satuan_id', 'left');
        $this->db->where('p.nospk', $nospk);
        $this->db->group_by('p.product_id'); // Group by Product to show distinct items
        $this->db->order_by('total_qty', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Helper: Ambil breakdown item untuk beberapa SPK sekaligus (Optimasi Performa)
     */
    public function get_qty_breakdown_batched($nospk_array)
    {
        if (empty($nospk_array)) {
            return [];
        }

        $this->db->select('p.nospk, pr.product_nama, s.satuan_nama');
        $this->db->select('SUM(p.qty) as total_qty');
        $this->db->select('SUM(
                                COALESCE(
                                    NULLIF((SELECT dp_sub.qty_output FROM detail_produksi dp_sub WHERE dp_sub.produksi_id = p.produksi_id ORDER BY dp_sub.routing_urutan DESC LIMIT 1), 0),
                                    p.qty
                                )
                           ) as total_ready', FALSE);

        $this->db->from('tbl_produksi p');
        $this->db->join('tbl_product pr', 'p.product_id = pr.product_id', 'left');
        $this->db->join('tbl_satuan s', 'pr.satuan_id = s.satuan_id', 'left');
        $this->db->where_in('p.nospk', $nospk_array);
        $this->db->group_by('p.nospk');
        $this->db->group_by('p.product_id');
        $this->db->order_by('p.nospk', 'DESC');
        $this->db->order_by('total_qty', 'DESC');

        $res = $this->db->get()->result();
        $grouped = [];
        foreach ($res as $row) {
            $grouped[$row->nospk][] = $row;
        }
        return $grouped;
    }

}
