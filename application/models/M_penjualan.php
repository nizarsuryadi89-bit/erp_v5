<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_penjualan extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Helper to apply access control rules to a query
     */
    public function apply_access_control($access_params, $alias = '')
    {
        if (!$access_params || !$access_params['filter_branch']) {
            return;
        }

        $prefix = $alias ? $alias . '.' : '';

        if (isset($access_params['filter_by_user']) && $access_params['filter_by_user']) {
            $this->db->where($prefix . 'kasir_id', $access_params['branch_id']);
        } else if (isset($access_params['filter_by_branch']) && $access_params['filter_by_branch']) {
            $this->db->where($prefix . 'perusahaan_id', $access_params['branch_id']);
        }
    }

    // ==================== CALENDAR OPERATIONS ====================

    /**
     * Get sales calendar data for a specific month
     */
    public function getSalesCalendarData($year, $month, $access_params = null)
    {
        $this->db->select('DATE(tgl_bayar) as tanggal, COUNT(*) as jumlah_transaksi, SUM(total_bayar) as total_penjualan');
        $this->db->from('tbl_pembayaran');
        $this->db->where('YEAR(tgl_bayar)', $year);
        $this->db->where('MONTH(tgl_bayar)', $month);
        $this->db->where('status_lunas', '1'); // Only completed transactions

        // Apply access control if provided
        if ($access_params && $access_params['filter_branch']) {
            if (isset($access_params['filter_by_user']) && $access_params['filter_by_user']) {
                // Filter by user (kasir can only see their own transactions)
                $this->db->where('kasir_id', $access_params['branch_id']);
            } else if (isset($access_params['filter_by_branch']) && $access_params['filter_by_branch']) {
                // Filter by branch (admin/owner can only see their company's transactions)
                $this->db->where('perusahaan_id', $access_params['branch_id']);
            }
        }

        $this->db->group_by('DATE(tgl_bayar)');
        $this->db->order_by('tanggal', 'ASC');

        $result = $this->db->get()->result();

        // Format data for FullCalendar
        $events = [];
        foreach ($result as $row) {
            $events[] = [
                'title' => number_format($row->total_penjualan, 0, ',', '.'),
                'start' => $row->tanggal,
                'jumlah_transaksi' => $row->jumlah_transaksi,
                'total_penjualan' => $row->total_penjualan,
                'backgroundColor' => $this->getColorByAmount($row->total_penjualan),
                'borderColor' => $this->getColorByAmount($row->total_penjualan),
                'extendedProps' => [
                    'transactions' => $row->jumlah_transaksi,
                    'amount' => $row->total_penjualan
                ]
            ];
        }

        return $events;
    }

    /**
     * Get color based on sales amount
     */
    private function getColorByAmount($amount)
    {
        if ($amount >= 1000000)
            return '#28a745'; // Green for high sales
        if ($amount >= 500000)
            return '#17a2b8'; // Blue for medium sales
        if ($amount >= 100000)
            return '#ffc107'; // Yellow for low sales
        return '#dc3545'; // Red for very low sales
    }

    // ==================== SALES DATA OPERATIONS ====================

    /**
     * Get sales data by date
     */
    public function getSalesByDate($date, $access_params = null)
    {
        $this->db->select('p.*, k.konsumen_nama, k.konsumen_email, k.konsumen_nohp, pr.nama_perusahaan');
        $this->db->from('tbl_pembayaran p');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->join('tbl_perusahaan pr', 'p.perusahaan_id = pr.id_perusahaan', 'left');
        // Apply access control if provided
        $this->db->where('DATE(p.tgl_bayar)', $date);
        $this->db->where('p.status_lunas', '1'); // Only completed transactions

        // Apply access control if provided
        if ($access_params && $access_params['filter_branch']) {
            if (isset($access_params['filter_by_user']) && $access_params['filter_by_user']) {
                // Filter by user (kasir can only see their own transactions)
                $this->db->where('p.kasir_id', $access_params['branch_id']);
            } else if (isset($access_params['filter_by_branch']) && $access_params['filter_by_branch']) {
                // Filter by branch (admin/owner can only see their company's transactions)
                $this->db->where('p.perusahaan_id', $access_params['branch_id']);
            }
        }

        $this->db->order_by('p.tgl_bayar', 'DESC');

        return $this->db->get()->result();
    }
    
    /**
     * Get sales data by date QUERY (For unbuffered export)
     */
    public function getSalesByDateQuery($date, $access_params = null)
    {
        $this->db->select('p.*, k.konsumen_nama, k.konsumen_email, k.konsumen_nohp, pr.nama_perusahaan');
        $this->db->from('tbl_pembayaran p');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->join('tbl_perusahaan pr', 'p.perusahaan_id = pr.id_perusahaan', 'left');
        $this->db->where('DATE(p.tgl_bayar)', $date);
        $this->db->where('p.status_lunas', '1');

        if ($access_params && $access_params['filter_branch']) {
            if (isset($access_params['filter_by_user']) && $access_params['filter_by_user']) {
                $this->db->where('p.kasir_id', $access_params['branch_id']);
            } else if (isset($access_params['filter_by_branch']) && $access_params['filter_by_branch']) {
                $this->db->where('p.perusahaan_id', $access_params['branch_id']);
            }
        }

        $this->db->order_by('p.tgl_bayar', 'DESC');

        return $this->db->get();
    }

    /**
     * Get sales detail by date
     */
    public function getSalesDetailByDate($date, $access_params = null)
    {
        $has_snapshot_harga = $this->db->field_exists('snapshot_harga', 'tbl_pembayaran_produk');
        $has_snapshot_detail = $this->db->field_exists('snapshot_detail_product', 'tbl_pembayaran_produk');
        $has_snapshot_subtotal = $this->db->field_exists('snapshot_subtotal', 'tbl_pembayaran_produk');
        $select_harga = $has_snapshot_harga ? 'COALESCE(pp.snapshot_harga, hd.harga_1)' : 'hd.harga_1';
        $select_detail = $has_snapshot_detail ? 'COALESCE(pp.snapshot_detail_product, hd.detail_product)' : 'hd.detail_product';
        $select_subtotal = $has_snapshot_subtotal ? 'pp.snapshot_subtotal AS snapshot_subtotal' : 'NULL AS snapshot_subtotal';

        $this->db->select("pp.*, p.tgl_bayar, pr.product_nama, {$select_detail} as detail_product, {$select_harga} as harga_1, {$select_subtotal}", FALSE);
        $this->db->from('tbl_pembayaran_produk pp');
        $this->db->join('tbl_pembayaran p', 'pp.nospk = p.nospk');
        $this->db->join('tbl_product pr', 'pp.product_id = pr.product_id', 'left');
        $this->db->join('tbl_harga_product hd', 'pp.harga_id = hd.harga_id', 'left');
        $this->db->where('DATE(p.tgl_bayar)', $date);
        $this->db->where('p.status_lunas', '1'); // Only completed transactions
        if ($access_params && $access_params['filter_branch']) {
            if (isset($access_params['filter_by_user']) && $access_params['filter_by_user']) {
                // Filter by user (kasir can only see their own transactions)
                $this->db->where('p.kasir_id', $access_params['branch_id']);
            } else if (isset($access_params['filter_by_branch']) && $access_params['filter_by_branch']) {
                // Filter by branch (admin/owner can only see their company's transactions)
                $this->db->where('p.perusahaan_id', $access_params['branch_id']);
            }
        }

        $this->db->order_by('p.tgl_bayar', 'DESC');
        $this->db->order_by('pp.id', 'ASC');

        return $this->db->get()->result();
    }
    
    /**
     * Get sales detail by date QUERY (For unbuffered export)
     */
    public function getSalesDetailByDateQuery($date, $access_params = null)
    {
        $has_snapshot_harga = $this->db->field_exists('snapshot_harga', 'tbl_pembayaran_produk');
        $has_snapshot_detail = $this->db->field_exists('snapshot_detail_product', 'tbl_pembayaran_produk');
        $has_snapshot_subtotal = $this->db->field_exists('snapshot_subtotal', 'tbl_pembayaran_produk');
        $select_harga = $has_snapshot_harga ? 'COALESCE(pp.snapshot_harga, hd.harga_1)' : 'hd.harga_1';
        $select_detail = $has_snapshot_detail ? 'COALESCE(pp.snapshot_detail_product, hd.detail_product)' : 'hd.detail_product';
        $select_subtotal = $has_snapshot_subtotal ? 'pp.snapshot_subtotal AS snapshot_subtotal' : 'NULL AS snapshot_subtotal';

        $this->db->select("pp.*, p.tgl_bayar, pr.product_nama, {$select_detail} as detail_product, {$select_harga} as harga_1, {$select_subtotal}", FALSE);
        $this->db->from('tbl_pembayaran_produk pp');
        $this->db->join('tbl_pembayaran p', 'pp.nospk = p.nospk');
        $this->db->join('tbl_product pr', 'pp.product_id = pr.product_id', 'left');
        $this->db->join('tbl_harga_product hd', 'pp.harga_id = hd.harga_id', 'left');
        $this->db->where('DATE(p.tgl_bayar)', $date);
        $this->db->where('p.status_lunas', '1');
        
        if ($access_params && $access_params['filter_branch']) {
            if (isset($access_params['filter_by_user']) && $access_params['filter_by_user']) {
                $this->db->where('p.kasir_id', $access_params['branch_id']);
            } else if (isset($access_params['filter_by_branch']) && $access_params['filter_by_branch']) {
                $this->db->where('p.perusahaan_id', $access_params['branch_id']);
            }
        }

        $this->db->order_by('p.tgl_bayar', 'DESC');
        $this->db->order_by('pp.id', 'ASC');

        return $this->db->get();
    }

    /**
     * Get total sales amount
     */
    public function getTotalSales($access_params = null)
    {
        $this->db->select('SUM(total_bayar) as total');
        $this->db->from('tbl_pembayaran');
        $this->db->where('status_lunas', '1');

        // Apply access control if provided
        if ($access_params && $access_params['filter_branch']) {
            if (isset($access_params['filter_by_user']) && $access_params['filter_by_user']) {
                // Filter by user (kasir can only see their own transactions)
                $this->db->where('kasir_id', $access_params['branch_id']);
            } else if (isset($access_params['filter_by_branch']) && $access_params['filter_by_branch']) {
                // Filter by branch (admin/owner can only see their company's transactions)
                $this->db->where('perusahaan_id', $access_params['branch_id']);
            }
        }

        $result = $this->db->get()->row();
        return $result ? $result->total : 0;
    }

    /**
     * Get total transactions count
     */
    public function getTotalTransactions($access_params = null)
    {
        $this->db->where('status_lunas', '1');

        // Apply access control if provided
        if ($access_params && $access_params['filter_branch']) {
            if (isset($access_params['filter_by_user']) && $access_params['filter_by_user']) {
                // Filter by user (kasir can only see their own transactions)
                $this->db->where('kasir_id', $access_params['branch_id']);
            } else if (isset($access_params['filter_by_branch']) && $access_params['filter_by_branch']) {
                // Filter by branch (admin/owner can only see their company's transactions)
                $this->db->where('perusahaan_id', $access_params['branch_id']);
            }
        }

        return $this->db->count_all_results('tbl_pembayaran');
    }

    /**
     * Get total customers count
     */
    public function getTotalCustomers($access_params = null)
    {
        if ($access_params && $access_params['filter_branch']) {
            // Count unique customers from the specific branch or user
            $this->db->select('COUNT(DISTINCT p.konsumen_id) as total');
            $this->db->from('tbl_pembayaran p');
            $this->db->where('p.status_lunas', '1');

            if (isset($access_params['filter_by_user']) && $access_params['filter_by_user']) {
                // Filter by user (kasir can only see their own transactions)
                $this->db->where('p.kasir_id', $access_params['branch_id']);
            } else if (isset($access_params['filter_by_branch']) && $access_params['filter_by_branch']) {
                // Filter by branch (admin/owner can only see their company's transactions)
                $this->db->where('p.perusahaan_id', $access_params['branch_id']);
            }

            $result = $this->db->get()->row();
            return $result ? $result->total : 0;
        } else {
            return $this->db->count_all_results('tbl_konsumen');
        }
    }

    /**
     * Get monthly sales data
     */
    public function getMonthlySales($year, $month, $access_params = null)
    {
        $this->db->select('SUM(total_bayar) as total_sales, COUNT(*) as total_transactions');
        $this->db->from('tbl_pembayaran');
        $this->db->where('YEAR(tgl_bayar)', $year);
        $this->db->where('MONTH(tgl_bayar)', $month);
        $this->db->where('status_lunas', '1');

        // Apply access control if provided
        if ($access_params && $access_params['filter_branch']) {
            if (isset($access_params['filter_by_user']) && $access_params['filter_by_user']) {
                // Filter by user (kasir can only see their own transactions)
                $this->db->where('kasir_id', $access_params['branch_id']);
            } else if (isset($access_params['filter_by_branch']) && $access_params['filter_by_branch']) {
                // Filter by branch (admin/owner can only see their company's transactions)
                $this->db->where('perusahaan_id', $access_params['branch_id']);
            }
        }

        return $this->db->get()->row();
    }

    /**
     * Get recent sales
     */
    public function getRecentSales($limit = 10)
    {
        $this->db->select('p.*, k.konsumen_nama');
        $this->db->from('tbl_pembayaran p');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->where('p.status_lunas', '1');
        $this->db->order_by('p.tgl_bayar', 'DESC');
        $this->db->limit($limit);

        return $this->db->get()->result();
    }

    /**
     * Get daily statistics
     */
    public function getDailyStats($date)
    {
        $this->db->select('
            COUNT(*) as total_transactions,
            SUM(total_bayar) as total_sales,
            AVG(total_bayar) as average_sale,
            COUNT(DISTINCT konsumen_id) as unique_customers
        ');
        $this->db->from('tbl_pembayaran');
        $this->db->where('DATE(tgl_bayar)', $date);
        $this->db->where('status_lunas', '1');

        return $this->db->get()->row();
    }

    // ==================== PAYMENT METHODS ====================

    /**
     * Get payment methods
     */
    public function getPaymentMethods()
    {
        $this->db->select('DISTINCT metode as metode');
        $this->db->from('tbl_pembayaran');
        $this->db->where('status_lunas', '1');
        $this->db->where('metode IS NOT NULL');
        $this->db->where('metode !=', '');

        return $this->db->get()->result();
    }

    /**
     * Get sales by payment method
     */
    public function getSalesByPaymentMethod($method)
    {
        $this->db->select('p.*, k.konsumen_nama');
        $this->db->from('tbl_pembayaran p');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->where('p.metode', $method);
        $this->db->where('p.status_lunas', '1');
        $this->db->order_by('p.tgl_bayar', 'DESC');

        return $this->db->get()->result();
    }

    // ==================== BEST SELLING PRODUCTS ====================

    /**
     * Get best selling products
     */
    public function getBestSellingProducts($period = 'month', $perusahaan_id = null)
    {
        $date_condition = $this->getDateCondition($period);

        $this->db->select('
            pr.product_id,
            pr.product_nama,
            st.satuan_nama,
            hd.harga_1,
            SUM(
                CASE 
                    WHEN pd.panjang > 0 AND pd.lebar > 0 THEN (pd.panjang * pd.lebar * pd.qty)
                    ELSE pd.qty 
                END
            ) as total_sold,
            SUM(
                CASE 
                    WHEN pd.panjang > 0 AND pd.lebar > 0 THEN (pd.panjang * pd.lebar * pd.qty * hd.harga_1)
                    ELSE (pd.qty * hd.harga_1)
                END
            ) as total_revenue,
            COUNT(DISTINCT pb.nospk) as transaction_count
        ');
        $this->db->from('tbl_pemesanan_detail pd');
        $this->db->join('tbl_pembayaran pb', 'pd.nospk = pb.nospk');
        $this->db->join('tbl_product pr', 'pd.product_id = pr.product_id', 'left');
        $this->db->join('tbl_satuan st', 'pr.satuan_id = st.satuan_id', 'left');
        $this->db->join('tbl_harga_product hd', 'pd.harga_id = hd.harga_id', 'left');

        if ($perusahaan_id) {
            if (is_array($perusahaan_id)) {
                $this->db->where_in('pb.perusahaan_id', $perusahaan_id);
            } else {
                $this->db->where('pb.perusahaan_id', $perusahaan_id);
            }
        }

        $this->db->where('pb.status_lunas', '1');
        $this->db->where($date_condition);
        $this->db->group_by('pr.product_id');
        $this->db->order_by('total_sold', 'DESC');

        return $this->db->get()->result();
    }

    // Fungsi untuk mengambil data product dari pemesanan detail yang sudah dibayar
    public function getProductsFromPemesananDetail($period = 'month', $perusahaan_id = null)
    {
        $date_condition = $this->getDateCondition($period);

        if (!$perusahaan_id) {
            $perusahaan_id = $this->session->userdata('perusahaan_id');
        }

        $this->db->select('
            pr.product_id,
            pr.product_nama,
            pr.product_kode,
            pr.product_kategori,
            pr.product_subkategori,
            hd.harga_1,
            hd.harga_2,
            hd.harga_3,
            SUM(pd.qty) as total_ordered,
            SUM(pd.qty * hd.harga_1) as total_revenue,
            COUNT(DISTINCT p.nospk) as total_transactions,
            p.perusahaan_id
        ');
        $this->db->from('tbl_pemesanan_detail pd');
        $this->db->join('tbl_pemesanan p', 'pd.nospk = p.nospk');
        $this->db->join('tbl_pembayaran pb', 'p.nospk = pb.nospk', 'left');
        $this->db->join('tbl_product pr', 'pd.product_id = pr.product_id', 'left');
        $this->db->join('tbl_harga_product hd', 'pr.product_id = hd.product_id', 'left');
        $this->db->where('p.perusahaan_id', $perusahaan_id);
        $this->db->where('pb.status_lunas', '1'); // Hanya yang sudah lunas
        $this->db->where($date_condition);
        $this->db->group_by('pr.product_id');
        $this->db->order_by('total_ordered', 'DESC');

        return $this->db->get()->result();
    }

    // Fungsi untuk mengambil detail produk per transaksi
    public function getProductTransactionDetails($nospk = null, $perusahaan_id = null)
    {
        if (!$perusahaan_id) {
            $perusahaan_id = $this->session->userdata('perusahaan_id');
        }

        $this->db->select('
            pd.pemesanan_detail_id,
            pd.product_id,
            pd.qty,
            pd.harga_satuan,
            pd.subtotal,
            pr.product_nama,
            pr.product_kode,
            pr.product_kategori,
            pr.product_subkategori,
            p.nospk,
            p.tanggal_pemesanan,
            p.status_pemesanan,
            pb.pembayaran_id,
            pb.total_bayar,
            pb.status_lunas,
            pb.tgl_bayar
        ');
        $this->db->from('tbl_pemesanan_detail pd');
        $this->db->join('tbl_pemesanan p', 'pd.nospk = p.nospk');
        $this->db->join('tbl_pembayaran pb', 'p.nospk = pb.nospk', 'left');
        $this->db->join('tbl_product pr', 'pd.product_id = pr.product_id', 'left');
        $this->db->where('p.perusahaan_id', $perusahaan_id);

        if ($nospk) {
            $this->db->where('p.nospk', $nospk);
        }

        $this->db->order_by('p.tanggal_pemesanan', 'DESC');

        return $this->db->get()->result();
    }

    // Fungsi untuk mendapatkan rekap produk berdasarkan pemesanan dan pembayaran


    public function getProductSummaryFromPemesanan($period = 'month', $perusahaan_id = null)
    {
        $date_condition = $this->getDateCondition($period);

        if (!$perusahaan_id) {
            $perusahaan_id = $this->session->userdata('perusahaan_id');
        }

        $this->db->select('
            pr.product_id,
            pr.product_nama,
            pr.barcode_product as product_kode,
            hd.harga_1,
            SUM(CASE WHEN pb.status_lunas = "1" THEN pd.qty ELSE 0 END) as total_sold_paid,
            SUM(CASE WHEN pb.status_lunas = "1" THEN pd.qty * hd.harga_1 ELSE 0 END) as total_revenue_paid,
            SUM(CASE WHEN pb.status_lunas = "0" OR pb.status_lunas IS NULL THEN pd.qty ELSE 0 END) as total_sold_unpaid,
            SUM(CASE WHEN pb.status_lunas = "0" OR pb.status_lunas IS NULL THEN pd.qty * hd.harga_1 ELSE 0 END) as total_revenue_unpaid,
            SUM(pd.qty) as total_ordered,
            SUM(pd.qty * hd.harga_1) as total_revenue,
            COUNT(DISTINCT CASE WHEN pb.status_lunas = "1" THEN p.nospk END) as total_transactions_paid,
            COUNT(DISTINCT p.nospk) as total_transactions,
            p.perusahaan_id
        ');
        $this->db->from('tbl_pemesanan_detail pd');
        $this->db->join('tbl_pemesanan p', 'pd.nospk = p.nospk');
        $this->db->join('tbl_pembayaran pb', 'p.nospk = pb.nospk', 'left');
        $this->db->join('tbl_product pr', 'pd.product_id = pr.product_id', 'left');
        $this->db->join('tbl_harga_product hd', 'pr.product_id = hd.product_id', 'left');
        $this->db->where('p.perusahaan_id', $perusahaan_id);
        $this->db->where($date_condition);
        $this->db->group_by('pr.product_id');
        $this->db->order_by('total_ordered', 'DESC');

        return $this->db->get()->result();
    }
    // Fungsi untuk mendapatkan produk yang paling sering dipesan
    public function getMostOrderedProducts($period = 'month', $perusahaan_id = null, $limit = 10)
    {
        $date_condition = $this->getDateCondition($period);

        if (!$perusahaan_id) {
            $perusahaan_id = $this->session->userdata('perusahaan_id');
        }

        $this->db->limit($limit);
        $this->db->select('
            pr.product_id,
            pr.product_nama,
            pr.barcode_product as product_kode,
            hd.harga_1,
            SUM(pd.qty) as total_ordered,
            COUNT(DISTINCT p.nospk) as total_orders,
            AVG(pd.qty) as avg_qty_per_order,
            SUM(pd.qty * hd.harga_1) as total_potential_revenue,
            p.perusahaan_id');
        $this->db->from('tbl_pemesanan_detail pd');
        $this->db->join('tbl_pemesanan p', 'pd.nospk = p.nospk');
        $this->db->join('tbl_pembayaran pb', 'p.nospk = pb.nospk', 'left');
        $this->db->join('tbl_product pr', 'pd.product_id = pr.product_id', 'left');
        $this->db->join('tbl_harga_product hd', 'pr.product_id = hd.product_id', 'left');
        $this->db->where('p.perusahaan_id', $perusahaan_id);
        $this->db->where($date_condition);
        $this->db->group_by('pr.product_id');
        $this->db->order_by('total_ordered', 'DESC');

        return $this->db->get()->result();
    }


    // Fungsi untuk mendapatkan produk berdasarkan status pembayaran
    public function getProductsByPaymentStatus($payment_status = 'paid', $period = 'month', $perusahaan_id = null)
    {
        $date_condition = $this->getDateCondition($period);

        if (!$perusahaan_id) {
            $perusahaan_id = $this->session->userdata('perusahaan_id');
        }

        $this->db->select('
            pr.product_id,
            pr.product_nama,
            pr.barcode_product as product_kode,
            hd.harga_1,
            SUM(pd.qty) as total_qty,
            SUM(pd.qty * hd.harga_1) as total_revenue,
            COUNT(DISTINCT p.nospk) as total_transactions,
            p.perusahaan_id
        ');
        $this->db->from('tbl_pemesanan_detail pd');
        $this->db->join('tbl_pemesanan p', 'pd.nospk = p.nospk');
        $this->db->join('tbl_pembayaran pb', 'p.nospk = pb.nospk', 'left');
        $this->db->join('tbl_product pr', 'pd.product_id = pr.product_id', 'left');
        $this->db->join('tbl_harga_product hd', 'pr.product_id = hd.product_id', 'left');
        $this->db->where('p.perusahaan_id', $perusahaan_id);
        $this->db->where($date_condition);

        if ($payment_status === 'paid') {
            $this->db->where('pb.status_lunas', '1');
        } elseif ($payment_status === 'unpaid') {
            $this->db->where('pb.status_lunas = "0" OR pb.status_lunas IS NULL');
        }

        $this->db->group_by('pr.product_id');
        $this->db->order_by('total_revenue', 'DESC');

        return $this->db->get()->result();
    }



    public function getTopProducts($limit = 10, $period = 'month')
    {
        $date_condition = $this->getDateCondition($period);

        $this->db->select('
            pr.product_id,
            pr.product_nama,
            st.satuan_nama,
            SUM(
                CASE 
                    WHEN pd.panjang > 0 AND pd.lebar > 0 THEN (pd.panjang * pd.lebar * pd.qty)
                    ELSE pd.qty 
                END
            ) as total_sold,
            SUM(
                CASE 
                    WHEN pd.panjang > 0 AND pd.lebar > 0 THEN (pd.panjang * pd.lebar * pd.qty * hd.harga_1)
                    ELSE (pd.qty * hd.harga_1)
                END
            ) as total_revenue,
            COUNT(DISTINCT pb.nospk) as transaction_count
        ');
        $this->db->from('tbl_pemesanan_detail pd');
        $this->db->join('tbl_pembayaran pb', 'pd.nospk = pb.nospk');
        $this->db->join('tbl_product pr', 'pd.product_id = pr.product_id', 'left');
        $this->db->join('tbl_satuan st', 'pr.satuan_id = st.satuan_id', 'left');
        $this->db->join('tbl_harga_product hd', 'pd.harga_id = hd.harga_id', 'left');
        $this->db->where('pb.status_lunas', '1');
        $this->db->where($date_condition);
        $this->db->group_by('pr.product_id');
        $this->db->order_by('total_sold', 'DESC');
        $this->db->limit($limit);

        return $this->db->get()->result();
    }

    /**
     * Get date condition based on period
     */
    private function getDateCondition($period)
    {
        switch ($period) {
            case 'week':
                return 'pb.tgl_bayar >= DATE_SUB(NOW(), INTERVAL 7 DAY)';
            case 'month':
                return 'pb.tgl_bayar >= DATE_SUB(NOW(), INTERVAL 30 DAY)';
            case 'quarter':
                return 'pb.tgl_bayar >= DATE_SUB(NOW(), INTERVAL 90 DAY)';
            case 'year':
                return 'pb.tgl_bayar >= DATE_SUB(NOW(), INTERVAL 365 DAY)';
            default:
                return '1=1'; // All time
        }
    }

    // ==================== SALES STATISTICS ====================

    /**
     * Get sales statistics
     */
    public function getSalesStatistics($period = 'month', $access_params = null)
    {
        $date_condition = $this->getDateCondition($period);

        $this->db->select('
            COUNT(*) as total_transactions,
            SUM(total_bayar) as total_sales,
            AVG(total_bayar) as average_sale,
            COUNT(DISTINCT konsumen_id) as unique_customers,
            MIN(total_bayar) as min_sale,
            MAX(total_bayar) as max_sale
        ');
        $this->db->from('tbl_pembayaran pb');
        $this->db->where('status_lunas', '1');
        $this->db->where($date_condition);

        $this->apply_access_control($access_params, 'pb');

        return $this->db->get()->row();
    }

    /**
     * Get sales chart data
     */
    public function getSalesChartData($period = 'week', $access_params = null)
    {
        switch ($period) {
            case 'week':
                return $this->getWeeklyChartData($access_params);
            case 'month':
                return $this->getMonthlyChartData($access_params);
            case 'year':
                return $this->getYearlyChartData($access_params);
            default:
                return $this->getDailyChartData($access_params);
        }
    }

    /**
     * Get daily chart data
     */
    private function getDailyChartData($access_params = null)
    {
        $this->db->select('
            DATE(tgl_bayar) as date,
            COUNT(*) as transactions,
            SUM(total_bayar) as sales
        ');
        $this->db->from('tbl_pembayaran');
        $this->db->where('tgl_bayar >= DATE_SUB(NOW(), INTERVAL 30 DAY)');
        $this->db->where('status_lunas', '1');

        $this->apply_access_control($access_params);

        $this->db->group_by('DATE(tgl_bayar)');
        $this->db->order_by('date', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Get weekly chart data
     */
    private function getWeeklyChartData($access_params = null)
    {
        $this->db->select('
            YEARWEEK(tgl_bayar) as week,
            COUNT(*) as transactions,
            SUM(total_bayar) as sales
        ');
        $this->db->from('tbl_pembayaran');
        $this->db->where('tgl_bayar >= DATE_SUB(NOW(), INTERVAL 12 WEEK)');
        $this->db->where('status_lunas', '1');

        $this->apply_access_control($access_params);

        $this->db->group_by('YEARWEEK(tgl_bayar)');
        $this->db->order_by('week', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Get monthly chart data
     */
    private function getMonthlyChartData($access_params = null)
    {
        $this->db->select('
            DATE_FORMAT(tgl_bayar, "%Y-%m") as month,
            COUNT(*) as transactions,
            SUM(total_bayar) as sales
        ');
        $this->db->from('tbl_pembayaran');
        $this->db->where('tgl_bayar >= DATE_SUB(NOW(), INTERVAL 12 MONTH)');
        $this->db->where('status_lunas', '1');

        $this->apply_access_control($access_params);

        $this->db->group_by('DATE_FORMAT(tgl_bayar, "%Y-%m")');
        $this->db->order_by('month', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Get yearly chart data
     */
    private function getYearlyChartData($access_params = null)
    {
        $this->db->select('
            YEAR(tgl_bayar) as year,
            COUNT(*) as transactions,
            SUM(total_bayar) as sales
        ');
        $this->db->from('tbl_pembayaran');
        $this->db->where('status_lunas', '1');

        $this->apply_access_control($access_params);

        $this->db->group_by('YEAR(tgl_bayar)');
        $this->db->order_by('year', 'ASC');

        return $this->db->get()->result();
    }

    // ==================== SEARCH AND FILTER ====================

    /**
     * Search sales transactions
     */
    public function searchSales($keyword, $date_from = null, $date_to = null, $status = null)
    {
        $this->db->select('p.*, k.konsumen_nama, k.konsumen_email');
        $this->db->from('tbl_pembayaran p');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');

        if ($keyword) {
            $this->db->group_start();
            $this->db->like('p.pembayaran_id', $keyword);
            $this->db->or_like('k.konsumen_nama', $keyword);
            $this->db->or_like('k.konsumen_email', $keyword);
            $this->db->group_end();
        }

        if ($date_from) {
            $this->db->where('p.tgl_bayar >=', $date_from);
        }

        if ($date_to) {
            $this->db->where('p.tgl_bayar <=', $date_to);
        }

        if ($status !== null) {
            $this->db->where('p.status_lunas', $status);
        }

        $this->db->order_by('p.tgl_bayar', 'DESC');

        return $this->db->get()->result();
    }

    // ==================== EXPORT FUNCTIONS ====================

    /**
     * Get sales data for export
     */
    public function getSalesForExport($date_from = null, $date_to = null)
    {
        $this->db->select('
            p.pembayaran_id,
            p.tgl_bayar,
            p.jam_bayar,
            p.total_bayar,
            p.status_lunas,
            p.metode,
            k.konsumen_nama,
            k.konsumen_email,
            k.konsumen_nohp
        ');
        $this->db->from('tbl_pembayaran p');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');

        if ($date_from) {
            $this->db->where('p.tgl_bayar >=', $date_from);
        }

        if ($date_to) {
            $this->db->where('p.tgl_bayar <=', $date_to);
        }

        $this->db->order_by('p.tgl_bayar', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get sales detail for export
     */
    public function getSalesDetailForExport($date_from = null, $date_to = null)
    {
        $this->db->select('
            pb.pembayaran_id,
            pb.tgl_bayar,
            pr.product_nama,
            pd.qty,
            hd.harga_1,
            (pd.qty * hd.harga_1) as subtotal,
            k.konsumen_nama
        ');
        $this->db->from('tbl_pemesanan_detail pd');
        $this->db->join('tbl_pembayaran pb', 'pd.nospk = pb.nospk');
        $this->db->join('tbl_product pr', 'pd.product_id = pr.product_id', 'left');
        $this->db->join('tbl_harga_product hd', 'pr.product_id = hd.product_id', 'left');
        $this->db->join('tbl_konsumen k', 'pb.konsumen_id = k.konsumen_id', 'left');

        if ($date_from) {
            $this->db->where('pb.tgl_bayar >=', $date_from);
        }

        if ($date_to) {
            $this->db->where('pb.tgl_bayar <=', $date_to);
        }

        $this->db->order_by('pb.tgl_bayar', 'DESC');

        return $this->db->get()->result();
    }

    // ==================== UTILITY FUNCTIONS ====================

    /**
     * Format currency
     */
    public function formatCurrency($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Get status text
     */
    public function getStatusText($status)
    {
        switch ($status) {
            case '1':
                return 'Selesai';
            case '2':
                return 'Proses';
            case '3':
                return 'Batal';
            case '4':
                return 'Pending';
            default:
                return 'Tidak Diketahui';
        }
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass($status)
    {
        switch ($status) {
            case '1':
                return 'badge-success';
            case '2':
                return 'badge-warning';
            case '3':
                return 'badge-danger';
            case '4':
                return 'badge-secondary';
            default:
                return 'badge-secondary';
        }
    }
}
?>
