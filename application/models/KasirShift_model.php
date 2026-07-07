<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KasirShift_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_current_shift($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('status', 'open');
        $this->db->order_by('shift_id', 'DESC');
        $this->db->limit(1);

        $query = $this->db->get('kasir_shift');
        return $query->num_rows() > 0 ? $query->row() : null;
    }

    public function get_shift_by_id($shift_id)
    {
        $this->db->where('shift_id', $shift_id);
        $query = $this->db->get('kasir_shift');
        return $query->num_rows() > 0 ? $query->row() : null;
    }

    public function get_shift_history($user_id, $limit = 10)
    {
        $this->db->where('user_id', $user_id);
        $this->db->order_by('shift_id', 'DESC');
        $this->db->limit($limit);

        $query = $this->db->get('kasir_shift');
        return $query->result();
    }

    /**
     * Get all open shifts (for admin) with kasir information
     */
    public function get_all_open_shifts()
    {
        $this->db->select('ks.*, u.username');
        $this->db->from('kasir_shift ks');
        $this->db->join('users u', 'u.user_id = ks.user_id', 'left');
        $this->db->where('ks.status', 'open');
        $this->db->order_by('ks.shift_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get all shift history (for admin)
     */
    public function get_all_shift_history($limit = 20)
    {
        $this->db->order_by('shift_id', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get('kasir_shift');
        return $query->result();
    }

    public function create_shift($data)
    {
        $this->db->insert('kasir_shift', $data);
        return $this->db->insert_id();
    }

    public function update_shift($shift_id, $data)
    {
        $this->db->where('shift_id', $shift_id);
        return $this->db->update('kasir_shift', $data);
    }

    /**
     * Alias for get_shift_by_id to support legacy or different naming conventions
     */
    public function shift_detail($shift_id)
    {
        return $this->get_shift_by_id($shift_id);
    }

    public function get_total_cash_sales($shift_id)
    {
        $shift = $this->get_shift_by_id($shift_id);
        if (!$shift)
            return 0;

        // Join dengan tbl_pembayaran_detail untuk mendapatkan metode pembayaran
        $this->db->select('COALESCE(SUM(pd.jumlah), 0) as total_cash');
        $this->db->from('tbl_pembayaran pb');
        $this->db->join('tbl_pembayaran_detail pd', 'pd.pembayaran_id = pb.pembayaran_id', 'inner');
        $this->db->where('pb.kasir_id', $shift->user_id);
        $this->db->where('DATE(pb.tgl_bayar)', $shift->tanggal_shift);
        $this->db->where('pb.jam_bayar >=', date('H:i:s', strtotime($shift->jam_open)));
        if ($shift->jam_close) {
            $this->db->where('pb.jam_bayar <=', date('H:i:s', strtotime($shift->jam_close)));
        }
        $this->db->where('pd.metode', 'tunai'); // Metode cash biasanya 'tunai' di database

        $query = $this->db->get();
        $result = $query->row();
        return $result ? $result->total_cash : 0;
    }

    public function get_total_non_cash_sales($shift_id)
    {
        $shift = $this->get_shift_by_id($shift_id);
        if (!$shift)
            return 0;

        // Join dengan tbl_pembayaran_detail untuk mendapatkan metode pembayaran
        $this->db->select('COALESCE(SUM(pd.jumlah), 0) as total_non_cash');
        $this->db->from('tbl_pembayaran pb');
        $this->db->join('tbl_pembayaran_detail pd', 'pd.pembayaran_id = pb.pembayaran_id', 'inner');
        $this->db->where('pb.kasir_id', $shift->user_id);
        $this->db->where('DATE(pb.tgl_bayar)', $shift->tanggal_shift);
        $this->db->where('pb.jam_bayar >=', date('H:i:s', strtotime($shift->jam_open)));
        if ($shift->jam_close) {
            $this->db->where('pb.jam_bayar <=', date('H:i:s', strtotime($shift->jam_close)));
        }
        $this->db->where('pd.metode !=', 'tunai'); // Non-cash adalah semua metode selain 'tunai'

        $query = $this->db->get();
        $result = $query->row();
        return $result ? $result->total_non_cash : 0;
    }

    public function get_shift_transactions($shift_id)
    {
        $shift = $this->get_shift_by_id($shift_id);
        if (!$shift)
            return array();

        $this->db->select('pb.*, tk.konsumen_nama as konsumen_nama, tk.konsumen_email as konsumen_email, tk.konsumen_nohp as konsumen_nohp');
        $this->db->from('tbl_pembayaran pb');
        $this->db->join('tbl_konsumen tk', 'pb.konsumen_id = tk.konsumen_id', 'left');
        $this->db->where('pb.kasir_id', $shift->user_id);
        $this->db->where('DATE(pb.tgl_bayar)', $shift->tanggal_shift);
        $this->db->where('pb.jam_bayar >=', date('H:i:s', strtotime($shift->jam_open)));
        if ($shift->jam_close) {
            $this->db->where('pb.jam_bayar <=', date('H:i:s', strtotime($shift->jam_close)));
        }
        $this->db->order_by('pb.jam_bayar', 'ASC');

        $query = $this->db->get();
        return $query->result();
    }

    public function get_total_pengeluaran($shift_id)
    {
        $shift = $this->get_shift_by_id($shift_id);
        if (!$shift)
            return 0;

        $this->db->select('COALESCE(SUM(ph.pengeluaran_jumlah), 0) as total_pengeluaran');
        $this->db->from('tbl_pengeluaran ph');
        $this->db->where('ph.shift_id', $shift_id);

        $query = $this->db->get();
        $result = $query->row();
        return $result ? $result->total_pengeluaran : 0;
    }

    public function get_shift_pengeluaran_details($shift_id)
    {
        $shift = $this->get_shift_by_id($shift_id);
        if (!$shift)
            return array();

        $this->db->select('ph.*, kp.kategori_pengeluaran');
        $this->db->from('tbl_pengeluaran ph');
        $this->db->join('tbl_kategori_pengeluaran kp', 'ph.kategori_pengeluaran_id = kp.id', 'left');
        $this->db->where('ph.shift_id', $shift_id);
        $this->db->order_by('ph.pengeluaran_tgl', 'ASC');

        $query = $this->db->get();
        return $query->result();
    }

    public function check_shift_required($user_id)
    {
        $current_shift = $this->get_current_shift($user_id);
        return $current_shift ? true : false;
    }

    public function get_shift_summary($user_id, $date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }

        $this->db->where('user_id', $user_id);
        $this->db->where('tanggal_shift', $date);
        $this->db->order_by('shift_id', 'DESC');

        $query = $this->db->get('kasir_shift');
        return $query->result();
    }

    public function get_shift_statistics($user_id, $start_date = null, $end_date = null)
    {
        if (!$start_date) {
            $start_date = date('Y-m-01'); // First day of current month
        }
        if (!$end_date) {
            $end_date = date('Y-m-t'); // Last day of current month
        }

        $this->db->select('
            COUNT(*) as total_shifts,
            SUM(CASE WHEN status = "closed" THEN 1 ELSE 0 END) as closed_shifts,
            SUM(CASE WHEN status = "open" THEN 1 ELSE 0 END) as open_shifts,
            SUM(modal_awal) as total_modal,
            SUM(total_penjualan_tunai) as total_cash_sales,
            SUM(total_penjualan_non_tunai) as total_non_cash_sales,
            SUM(total_pengeluaran) as total_expenses,
            SUM(selisih) as total_difference
        ');
        $this->db->from('kasir_shift');
        $this->db->where('user_id', $user_id);
        $this->db->where('tanggal_shift >=', $start_date);
        $this->db->where('tanggal_shift <=', $end_date);

        $query = $this->db->get();
        return $query->row();
    }
}
