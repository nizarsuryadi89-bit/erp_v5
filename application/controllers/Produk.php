<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Produk extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_transaksi');
        $this->load->model('M_penjualan');
        $this->load->model('Produk_model');
    }

    public function rating()
    {
        $perusahaan_id = $this->session->userdata('perusahaan_id');
        $channel_filter = $this->input->get('channel', true) ?: 'semua';
        
        // 1. Get Channels (Jenis Transaksi)
        $channels = $this->_get_channels();

        // 2. Fetch Global Best Sellers using M_penjualan
        $best_sellers = $this->M_penjualan->getBestSellingProducts('month', $perusahaan_id);

        // 3. Fetch Recent Activity across all channels
        $this->db->select('pm.*, jt.nama_jenis_transaksi, k.konsumen_nama, m.marketplace_nama')
            ->from('tbl_pemesanan pm')
            ->join('tbl_jenis_transaksi jt', 'jt.id_jenis_transaksi = pm.id_jenis_transaksi')
            ->join('tbl_konsumen k', 'k.konsumen_id = pm.konsumen_id')
            ->join('tbl_marketplace m', 'm.marketplace_id = pm.marketplace_id', 'left')
            ->where('pm.perusahaan_id', $perusahaan_id);
        
        if ($channel_filter !== 'semua') {
            $this->db->where('pm.id_jenis_transaksi', $channel_filter);
        }
        
        $recent_activity = $this->db->order_by('pm.created_at', 'DESC')->limit(12)->get()->result();

        $data = [
            'title' => 'Ranking & Rating Produk',
            'current_channel' => $channel_filter,
            'channels' => $channels,
            'stats' => $this->_calculate_global_stats($perusahaan_id, $channel_filter),
            'best_sellers' => array_slice($best_sellers, 0, 10),
            'recent_ratings' => $this->_map_activity_to_ranking($recent_activity)
        ];

        $this->load_admin_view('admin/produk/rating', $data);
    }

    private function _get_channels()
    {
        // Add "Semua" as default
        $channels = [['id' => 'semua', 'name' => 'Semua Kanal', 'icon' => 'fas fa-globe']];
        
        $res = $this->db->get('tbl_jenis_transaksi')->result();
        foreach ($res as $row) {
            $icon = 'fas fa-shopping-bag';
            if (stripos($row->nama_jenis_transaksi, 'offline') !== false || stripos($row->nama_jenis_transaksi, 'kasir') !== false) $icon = 'fas fa-store';
            if (stripos($row->nama_jenis_transaksi, 'wa') !== false || stripos($row->nama_jenis_transaksi, 'online') !== false) $icon = 'fab fa-whatsapp';
            
            $channels[] = [
                'id' => $row->id_jenis_transaksi,
                'name' => $row->nama_jenis_transaksi,
                'icon' => $icon
            ];
        }
        return $channels;
    }

    private function _calculate_global_stats($perusahaan_id, $channel = 'semua')
    {
        $this->db->select('COUNT(*) as total_orders, SUM(total_bayar) as total_revenue');
        $this->db->from('tbl_pembayaran');
        $this->db->where('tbl_pembayaran.perusahaan_id', $perusahaan_id);
        $this->db->where('status_lunas', '1');
        
        if ($channel !== 'semua') {
            $this->db->join('tbl_pemesanan', 'tbl_pemesanan.nospk = tbl_pembayaran.nospk');
            $this->db->where('tbl_pemesanan.id_jenis_transaksi', $channel);
        }
        
        $res = $this->db->get()->row();

        return [
            'total_sales' => $res->total_orders ?? 0,
            'revenue' => $res->total_revenue ?? 0,
            'active_channel' => $channel
        ];
    }

    private function _map_activity_to_ranking($activity)
    {
        $mapped = [];
        foreach ($activity as $a) {
            $mapped[] = (object)[
                'date' => date('d M Y, H:i', strtotime($a->created_at)),
                'stars' => 5, // Status keunggulan produk (5 star as default top tier)
                'comment' => "Transaksi Berhasil via " . $a->nama_jenis_transaksi . ($a->marketplace_nama ? " (".$a->marketplace_nama.")" : ""),
                'customer' => $a->konsumen_nama,
                'order_id' => $a->nospk,
                'product_name' => "Order Global #" . $a->nospk,
                'replied' => true
            ];
        }
        return $mapped;
    }


}
