<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Marketplace_model extends CI_Model {

    public function get_transaksi_marketplace($perusahaan_id = null, $marketplace_id = null, $status = null, $date_from = null, $date_to = null)
    {
        $this->db->select('p.*, k.konsumen_nama, m.marketplace_nama, m.marketplace_id as m_id, exp.nama_pengiriman, s.resi_marketplace');
        $this->db->from('tbl_pemesanan p');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->join('tbl_marketplace m', 'p.marketplace_id = m.marketplace_id', 'left');
        $this->db->join('tbl_pengiriman exp', 'p.id_pengiriman = exp.id_pengiriman', 'left');
        $this->db->join('tbl_spk s', 'p.nospk = s.nospk', 'left');
        $this->db->where('p.id_jenis_transaksi', 2); // 2 = Marketplace

        if ($perusahaan_id !== null) {
            $this->db->where('p.perusahaan_id', $perusahaan_id);
        }

        if (!empty($marketplace_id)) {
            $this->db->where('p.marketplace_id', $marketplace_id);
        }

        if (!empty($status)) {
            $this->db->where('p.status_pembayaran', $status);
        }

        if (!empty($date_from)) {
            $this->db->where('DATE(p.created_at) >=', $date_from);
        }

        if (!empty($date_to)) {
            $this->db->where('DATE(p.created_at) <=', $date_to);
        }

        $this->db->order_by('p.created_at', 'DESC');
        return $this->db->get();
    }

    public function get_detail_transaksi($nospk)
    {
        $this->db->select('p.*, k.konsumen_nama, k.konsumen_nohp, k.konsumen_alamat, m.marketplace_nama, exp.nama_pengiriman, s.resi_marketplace, s.spk_time');
        $this->db->from('tbl_pemesanan p');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->join('tbl_marketplace m', 'p.marketplace_id = m.marketplace_id', 'left');
        $this->db->join('tbl_pengiriman exp', 'p.id_pengiriman = exp.id_pengiriman', 'left');
        $this->db->join('tbl_spk s', 'p.nospk = s.nospk', 'left');
        $this->db->where('p.nospk', $nospk);
        return $this->db->get()->row_array();
    }

    public function update_resi($nospk, $resi)
    {
        $this->db->where('nospk', $nospk);
        return $this->db->update('tbl_spk', ['resi_marketplace' => $resi]);
    }

    public function get_platforms()
    {
        return $this->db->get('tbl_marketplace')->result();
    }

    public function update_platform($id, $data)
    {
        $this->db->where('marketplace_id', $id);
        return $this->db->update('tbl_marketplace', $data);
    }

    public function get_dashboard_metrics($perusahaan_id = null)
    {
        // 1. Total transaksi hari ini
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        $this->db->where('id_jenis_transaksi', 2); // 2 = Marketplace
        if ($perusahaan_id) $this->db->where('perusahaan_id', $perusahaan_id);
        $trx_today = $this->db->count_all_results('tbl_pemesanan');

        // 2. Omset hari ini (total_terbayar)
        $this->db->select_sum('pp.snapshot_subtotal');
        $this->db->from('tbl_pemesanan p');
        $this->db->join('tbl_pembayaran_produk pp', 'p.nospk = pp.nospk', 'left');
        $this->db->where('DATE(p.created_at)', date('Y-m-d'));
        $this->db->where('p.id_jenis_transaksi', 2);
        if ($perusahaan_id) $this->db->where('p.perusahaan_id', $perusahaan_id);
        $omset_today = $this->db->get()->row()->snapshot_subtotal ?? 0;

        // 3. Omset bulan ini
        $this->db->select_sum('pp.snapshot_subtotal');
        $this->db->from('tbl_pemesanan p');
        $this->db->join('tbl_pembayaran_produk pp', 'p.nospk = pp.nospk', 'left');
        $this->db->where('MONTH(p.created_at)', date('m'));
        $this->db->where('YEAR(p.created_at)', date('Y'));
        $this->db->where('p.id_jenis_transaksi', 2);
        if ($perusahaan_id) $this->db->where('p.perusahaan_id', $perusahaan_id);
        $omset_month = $this->db->get()->row()->snapshot_subtotal ?? 0;

        // 4. Pending produksi
        $this->db->from('tbl_produksi p');
        $this->db->join('tbl_pemesanan pm', 'p.nospk = pm.nospk', 'inner');
        $this->db->where('pm.id_jenis_transaksi', 2);
        $this->db->where_not_in('p.status_produksi', ['done', 'on_costumer']);
        if ($perusahaan_id) $this->db->where('p.perusahaan_id', $perusahaan_id);
        $pending_produksi = $this->db->count_all_results();

        return [
            'trx_today' => $trx_today,
            'omset_today' => $omset_today,
            'omset_month' => $omset_month,
            'pending_produksi' => $pending_produksi
        ];
    }
}
