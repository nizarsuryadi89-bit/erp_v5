<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kelola_pesanan extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Models are already loaded or handled by MY_Controller properties
    }

    public function index()
    {
        $status_filter = $this->input->get('status', true) ?: 'semua';
        
        // Stats Calculation (Always total)
        $stats = [
            'perlu_dikirim' => $this->_count_by_status('paid', 'draft'),
            'dalam_proses' => $this->_count_by_status('paid', 'proses'),
            'siap_pb' => $this->_count_by_status('paid', 'done'),
            'dibatalkan' => $this->_count_by_status('cancel'),
            'terlambat' => $this->_count_late_orders(),
            'total_aktif' => $this->_count_total_aktif()
        ];

        $data = [
            'title' => 'Kelola Pesanan',
            'stats' => $stats,
            'current_status' => $status_filter,
            'orders' => $this->_get_order_list($status_filter)
        ];

        $this->load_admin_view('admin/kelola_pesanan/index', $data);
    }

    private function _count_by_status($pay_status, $prod_status = null)
    {
        $this->db->from('tbl_pemesanan pm');
        $this->db->where('pm.status_pembayaran', $pay_status);
        if ($prod_status) {
            $this->db->join('tbl_produksi prod', 'prod.nospk = pm.nospk');
            $this->db->where('prod.status_produksi', $prod_status);
        }

        $level = $this->session->userdata('level');
        if (!in_array((int) $level, [1, 17], true)) {
            $this->db->where('pm.perusahaan_id', $this->session->userdata('perusahaan_id'));
        }

        return $this->db->count_all_results();
    }

    private function _count_late_orders()
    {
        $today = date('Y-m-d');
        $this->db->from('tbl_pemesanan pm');
        $this->db->join('tbl_pemesanan_detail pd', 'pd.nospk = pm.nospk');
        $this->db->where('pm.status_pembayaran', 'paid');
        $this->db->where('pd.dateline_tgl <', $today);

        $level = $this->session->userdata('level');
        if (!in_array((int) $level, [1, 17], true)) {
            $this->db->where('pm.perusahaan_id', $this->session->userdata('perusahaan_id'));
        }

        $this->db->group_by('pm.nospk'); // Ensure we count unique SPKs
        return $this->db->get()->num_rows();
    }

    private function _count_total_aktif()
    {
        $this->db->where_in('status_pembayaran', ['pending', 'paid']);
        $level = $this->session->userdata('level');
        if (!in_array((int) $level, [1, 17], true)) {
            $this->db->where('perusahaan_id', $this->session->userdata('perusahaan_id'));
        }
        return $this->db->count_all_results('tbl_pemesanan');
    }

    private function _get_order_list($status = 'semua')
    {
        $perusahaan = $this->session->userdata('perusahaan_id');
        $level = $this->session->userdata('level');

        $this->db->select('pm.*, k.konsumen_nama, k.konsumen_nohp, u.username as cs_name, m.marketplace_nama, pgr.nama_pengiriman');
        $this->db->from('tbl_pemesanan pm');
        $this->db->join('tbl_konsumen k', 'k.konsumen_id = pm.konsumen_id');
        $this->db->join('users u', 'u.user_id = pm.deskprint_id', 'left');
        $this->db->join('tbl_marketplace m', 'm.marketplace_id = pm.marketplace_id', 'left');
        $this->db->join('tbl_pengiriman pgr', 'pgr.id_pengiriman = pm.id_pengiriman', 'left');

        // Apply Status Filter
        if ($status !== 'semua') {
            if ($status === 'perlu_dikirim') {
                $this->db->join('tbl_produksi prod_f', 'prod_f.nospk = pm.nospk');
                $this->db->where('pm.status_pembayaran', 'paid');
                $this->db->where('prod_f.status_produksi', 'draft');
            } elseif ($status === 'dalam_proses') {
                $this->db->join('tbl_produksi prod_f', 'prod_f.nospk = pm.nospk');
                $this->db->where('pm.status_pembayaran', 'paid');
                $this->db->where('prod_f.status_produksi', 'proses');
            } elseif ($status === 'siap_pb') {
                $this->db->join('tbl_produksi prod_f', 'prod_f.nospk = pm.nospk');
                $this->db->where('pm.status_pembayaran', 'paid');
                $this->db->where('prod_f.status_produksi', 'done');
            } elseif ($status === 'dibatalkan') {
                $this->db->where('pm.status_pembayaran', 'cancel');
            } elseif ($status === 'selesai') {
                $this->db->join('tbl_produksi prod_f', 'prod_f.nospk = pm.nospk');
                $this->db->where_in('prod_f.status_produksi', ['on_pb', 'selesai']);
            }
            $this->db->group_by('pm.nospk');
        }

        if (!in_array((int) $level, [1, 17], true)) {
            $this->db->where('pm.perusahaan_id', $perusahaan);
        }

        $this->db->order_by('pm.created_at', 'DESC');
        $this->db->limit(100);
        $query = $this->db->get();

        $result = [];
        foreach ($query->result() as $row) {
            // Get items for each order
            $row->items = $this->db->select('pp.*, pr.product_nama, prod.status_produksi, hp.harga_1 as unit_price, pp.snapshot_subtotal, pp.snapshot_harga')
                ->from('tbl_pembayaran_produk pp')
                ->join('tbl_product pr', 'pr.product_id = pp.product_id')
                ->join('tbl_produksi prod', 'prod.nospk = pp.nospk AND prod.product_id = pp.product_id', 'left')
                ->join('tbl_harga_product hp', 'hp.harga_id = pp.harga_id', 'left')
                ->where('pp.nospk', $row->nospk)
                ->get()->result();

            // Get total price
            $total = 0;
            foreach ($row->items as $item) {
                // Prefer snapshot_subtotal if present (historical price snapshot)
                if (!empty($item->snapshot_subtotal)) {
                    $sub = (float) $item->snapshot_subtotal;
                } else {
                    // Determine unit price: snapshot_harga > hp.harga_1 > 0
                    $unit = 0.0;
                    if (!empty($item->snapshot_harga)) {
                        $unit = (float) $item->snapshot_harga;
                    } elseif (isset($item->unit_price) && $item->unit_price !== null) {
                        $unit = (float) $item->unit_price;
                    }

                    if ($item->panjang > 0 && $item->lebar > 0) {
                        $sub = (float) $item->panjang * (float) $item->lebar * (float) $item->qty * $unit;
                    } else {
                        $sub = (float) $item->qty * $unit;
                    }
                }
                $total += $sub;
            }
            $row->total_order = $total;

            $result[] = $row;
        }

        return $result;
    }

}

