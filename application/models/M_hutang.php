<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_hutang extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all hutang data with supplier information
     */
    public function getAllHutang($limit = null, $status = null)
    {
        $this->db->select('h.*, p.no_faktur, p.pembelian_tgl, s.supplier_nama, s.supplier_alamat, s.supplier_telp');
        $this->db->from('tbl_hutang h');
        $this->db->join('tbl_pembelian p', 'p.pembelian_id = h.pembelian_id');
        $this->db->join('tbl_supplier s', 's.supplier_id = h.supplier_id');

        if ($status) {
            $this->db->where('h.status', $status);
        }

        $this->db->order_by('h.jatuh_tempo', 'ASC');

        if ($limit) {
            $this->db->limit($limit);
        }

        return $this->db->get()->result();
    }

    /**
     * Get hutang summary statistics
     */
    public function getHutangSummary()
    {
        $this->db->select('
            COUNT(*) as total_hutang,
            SUM(total_tagihan) as total_tagihan,
            SUM(total_bayar) as total_bayar,
            SUM(sisa_hutang) as total_sisa_hutang,
            COUNT(CASE WHEN status = "LUNAS" THEN 1 END) as lunas,
            COUNT(CASE WHEN status = "SEBAGIAN" THEN 1 END) as sebagian,
            COUNT(CASE WHEN status = "BELUM LUNAS" THEN 1 END) as belum_lunas,
            COUNT(CASE WHEN jatuh_tempo <= CURDATE() AND status != "LUNAS" THEN 1 END) as jatuh_tempo
        ');
        $this->db->from('tbl_hutang');

        return $this->db->get()->row();
    }

    /**
     * Get hutang by ID
     */
    public function getHutangById($hutang_id)
    {
        $this->db->select('h.*, p.no_faktur, p.pembelian_tgl, s.supplier_nama, s.supplier_alamat, s.supplier_telp');
        $this->db->from('tbl_hutang h');
        $this->db->join('tbl_pembelian p', 'p.pembelian_id = h.pembelian_id');
        $this->db->join('tbl_supplier s', 's.supplier_id = h.supplier_id');
        $this->db->where('h.hutang_id', $hutang_id);

        return $this->db->get()->row();
    }

    /**
     * Get hutang by pembelian ID
     */
    public function getHutangByPembelianId($pembelian_id)
    {
        $this->db->select('h.*, p.no_faktur, p.pembelian_tgl, s.supplier_nama');
        $this->db->from('tbl_hutang h');
        $this->db->join('tbl_pembelian p', 'p.pembelian_id = h.pembelian_id');
        $this->db->join('tbl_supplier s', 's.supplier_id = h.supplier_id');
        $this->db->where('h.pembelian_id', $pembelian_id);

        return $this->db->get()->row();
    }

    /**
     * Update hutang payment
     */
    public function updateHutangPayment($hutang_id, $jumlah_bayar)
    {
        $this->db->trans_start();

        try {
            // Get current hutang data
            $hutang = $this->getHutangById($hutang_id);

            if (!$hutang) {
                $this->db->trans_rollback();
                return false;
            }

            // Calculate new total_bayar
            $new_total_bayar = $hutang->total_bayar + $jumlah_bayar;

            // Determine new status
            $new_status = 'BELUM LUNAS';
            if ($new_total_bayar >= $hutang->total_tagihan) {
                $new_status = 'LUNAS';
            } elseif ($new_total_bayar > 0) {
                $new_status = 'SEBAGIAN';
            }

            // Update hutang record
            $update_data = [
                'total_bayar' => $new_total_bayar,
                'status' => $new_status
            ];

            $this->db->where('hutang_id', $hutang_id);
            $this->db->update('tbl_hutang', $update_data);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return false;
            }

            return true;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error in updateHutangPayment: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update hutang payment with method
     */
    public function updateHutangPaymentWithMethod($hutang_id, $jumlah_bayar, $metode_bayar, $rekening_tujuan = null, $keterangan = null)
    {
        $this->db->trans_start();

        try {
            // Get current hutang data
            $hutang = $this->getHutangById($hutang_id);

            if (!$hutang) {
                $this->db->trans_rollback();
                return false;
            }

            // Calculate new total_bayar
            $new_total_bayar = $hutang->total_bayar + $jumlah_bayar;

            // Determine new status
            $new_status = 'BELUM LUNAS';
            if ($new_total_bayar >= $hutang->total_tagihan) {
                $new_status = 'LUNAS';
            } elseif ($new_total_bayar > 0) {
                $new_status = 'SEBAGIAN';
            }

            // Update hutang record
            $update_data = [
                'total_bayar' => $new_total_bayar,
                'status' => $new_status
            ];

            $this->db->where('hutang_id', $hutang_id);
            $this->db->update('tbl_hutang', $update_data);

            // Insert payment history
            $payment_data = [
                'hutang_id' => $hutang_id,
                'tanggal_bayar' => date('Y-m-d H:i:s'),
                'jumlah_bayar' => $jumlah_bayar,
                'metode_bayar' => $metode_bayar,
                'rekening_tujuan' => $rekening_tujuan,
                'keterangan' => $keterangan
            ];

            $this->db->insert('tbl_hutang_pembayaran', $payment_data);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return false;
            }

            return true;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error in updateHutangPaymentWithMethod: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get overdue hutang (jatuh tempo)
     */
    public function getOverdueHutang()
    {
        $this->db->select('h.*, p.no_faktur, p.pembelian_tgl, s.supplier_nama, s.supplier_tlp');
        $this->db->from('tbl_hutang h');
        $this->db->join('tbl_pembelian p', 'p.pembelian_id = h.pembelian_id');
        $this->db->join('tbl_supplier s', 's.supplier_id = h.supplier_id');
        $this->db->where('h.jatuh_tempo <= CURDATE()');
        $this->db->where('h.status !=', 'LUNAS');
        $this->db->order_by('h.jatuh_tempo', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Get hutang due today
     */
    public function getHutangDueToday()
    {
        $this->db->select('h.*, p.no_faktur, p.pembelian_tgl, s.supplier_nama, s.supplier_tlp');
        $this->db->from('tbl_hutang h');
        $this->db->join('tbl_pembelian p', 'p.pembelian_id = h.pembelian_id');
        $this->db->join('tbl_supplier s', 's.supplier_id = h.supplier_id');
        $this->db->where('h.jatuh_tempo', date('Y-m-d'));
        $this->db->where('h.status !=', 'LUNAS');
        $this->db->order_by('h.sisa_hutang', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get hutang by supplier
     */
    public function getHutangBySupplier($supplier_id)
    {
        $this->db->select('h.*, p.no_faktur, p.pembelian_tgl, s.supplier_nama');
        $this->db->from('tbl_hutang h');
        $this->db->join('tbl_pembelian p', 'p.pembelian_id = h.pembelian_id');
        $this->db->join('tbl_supplier s', 's.supplier_id = h.supplier_id');
        $this->db->where('h.supplier_id', $supplier_id);
        $this->db->where('h.status !=', 'LUNAS');
        $this->db->order_by('h.jatuh_tempo', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Get hutang summary by supplier
     */
    public function getHutangSummaryBySupplier($supplier_id)
    {
        $this->db->select('
            SUM(total_tagihan) as total_tagihan,
            SUM(total_bayar) as total_bayar,
            SUM(sisa_hutang) as total_sisa_hutang,
            COUNT(*) as total_transaksi,
            COUNT(CASE WHEN status = "LUNAS" THEN 1 END) as lunas,
            COUNT(CASE WHEN status = "SEBAGIAN" THEN 1 END) as sebagian,
            COUNT(CASE WHEN status = "BELUM LUNAS" THEN 1 END) as belum_lunas
        ');
        $this->db->from('tbl_hutang');
        $this->db->where('supplier_id', $supplier_id);

        return $this->db->get()->row();
    }

    /**
     * Delete hutang by pembelian ID (when pembelian is deleted)
     */
    public function deleteHutangByPembelianId($pembelian_id)
    {
        $this->db->where('pembelian_id', $pembelian_id);
        return $this->db->delete('tbl_hutang');
    }

    /**
     * Get hutang for export
     */
    public function getHutangForExport($status = null, $date_from = null, $date_to = null)
    {
        $this->db->select('
            h.hutang_id,
            p.no_faktur,
            s.supplier_nama,
            p.pembelian_tgl,
            h.jatuh_tempo,
            h.total_tagihan,
            h.total_bayar,
            h.sisa_hutang,
            h.status,
            h.created_at
        ');
        $this->db->from('tbl_hutang h');
        $this->db->join('tbl_pembelian p', 'p.pembelian_id = h.pembelian_id');
        $this->db->join('tbl_supplier s', 's.supplier_id = h.supplier_id');

        if ($status) {
            $this->db->where('h.status', $status);
        }

        if ($date_from) {
            $this->db->where('h.created_at >=', $date_from . ' 00:00:00');
        }

        if ($date_to) {
            $this->db->where('h.created_at <=', $date_to . ' 23:59:59');
        }

        $this->db->order_by('h.jatuh_tempo', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Add manual hutang (For Leasing, Operasional, etc.)
     */
    public function addManualHutang($data)
    {
        $this->db->trans_start();

        try {
            // First, create a dummy 'pembelian' record to link the hutang
            // because tbl_hutang requires pembelian_id
            $pemb_data = [
                'no_faktur' => $data['no_faktur'],
                'supplier_id' => $data['supplier_id'],
                'pembelian_tgl' => $data['tanggal_tagihan'],
                'pembelian_total' => $data['total_tagihan'],
                'metode_bayar' => 'kredit',
                'karyawan_id' => $this->session->userdata('id') ?: 1 // Fallback
            ];
            $this->db->insert('tbl_pembelian', $pemb_data);
            $pembelian_id = $this->db->insert_id();

            // Next, insert into tbl_hutang
            $hutang_data = [
                'pembelian_id' => $pembelian_id,
                'supplier_id' => $data['supplier_id'],
                'total_tagihan' => $data['total_tagihan'],
                'jatuh_tempo' => $data['jatuh_tempo'],
                'status' => 'BELUM LUNAS',
                'total_bayar' => 0
            ];
            $this->db->insert('tbl_hutang', $hutang_data);

            // Since it's a manual entry, we might want to log it specifically
            // This assumes you might have a mechanism or it's fine as is.
            // If you want to store ketererangan, you might need to add it to a notes table or similar, 
            // but for now, we just rely on standard fields.

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                return false;
            }
            return true;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error in addManualHutang: ' . $e->getMessage());
            return false;
        }
    }
}
