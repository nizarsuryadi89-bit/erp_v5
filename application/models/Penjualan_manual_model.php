<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penjualan_manual_model extends CI_Model
{

    public function get_konsumen()
    {
        $this->db->where('status', '1');
        $this->db->order_by('konsumen_nama', 'ASC');
        return $this->db->get('tbl_konsumen')->result();
    }

    public function get_konsumen_by_id($konsumen_id)
    {
        return $this->db->get_where('tbl_konsumen', ['konsumen_id' => $konsumen_id])->row();
    }

    public function get_produk()
    {
        $this->db->select('p.*, COALESCE(hp.harga_aktif, 0) as harga_aktif');
        $this->db->from('tbl_product p');
        $this->db->join('tbl_harga_product hp', 'hp.product_id = p.product_id', 'left');
        $this->db->order_by('p.product_nama', 'ASC');
        return $this->db->get()->result();
    }

    public function get_produk_by_id($produk_id)
    {
        return $this->db->get_where('tbl_product', ['product_id' => $produk_id])->row();
    }

    public function get_metode_pembayaran()
    {
        return [
            'tunai' => 'Tunai',
            'transfer' => 'Transfer Bank',
            'debit' => 'Debit/EDC',
            'kartu_kredit' => 'Kartu Kredit',
            'ewallet' => 'E-Wallet',
            'piutang' => 'Piutang',
            'lainnya' => 'Lainnya'
        ];
    }

    public function get_rekening()
    {
        $this->db->order_by('nama_bank', 'ASC');
        return $this->db->get('tbl_rekening')->result();
    }

    public function get_ewallet()
    {
        return $this->db->get('tbl_ewallet')->result();
    }

    public function get_edc()
    {
        return $this->db->get('tbl_mesin_edc')->result();
    }

    public function simpan_transaksi($post)
    {
        try {
            $this->db->trans_start();

            $perusahaan_id = (int) $this->session->userdata('perusahaan_id') ?: 1;
            $kasir_id = (int) $this->session->userdata('id') ?: 1;
            $tanggal_transaksi = !empty($post['tanggal_transaksi']) ? $post['tanggal_transaksi'] : date('Y-m-d');
            $products = $post['products'] ?? [];

            if (empty($products))
                throw new Exception('Detail produk kosong.');

            // Calculate totals
            $total_tagihan = 0;
            foreach ($products as $product) {
                $total_tagihan += (floatval($product['jumlah']) * floatval($product['harga_satuan']));
            }

            $nospk = $this->generate_nospk();

            // 0. tbl_spk (Induk Transaksi ERP)
            $spk_data = [
                'nospk' => $nospk,
                'no_urut_spk' => $this->get_next_no_urut($perusahaan_id, $kasir_id),
                'user_id' => $kasir_id,
                'konsumen_id' => $post['konsumen_id'],
                'metode_pesan' => 1, // On The Spot (Direct)
                'perusahaan_id' => $perusahaan_id,
                'spk_time' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('tbl_spk', $spk_data);

            // 1. tbl_pemesanan
            $pemesanan_data = [
                'nospk' => $nospk,
                'konsumen_id' => $post['konsumen_id'],
                'deskprint_id' => $kasir_id,
                'id_jenis_transaksi' => 1, // On The Spot
                'id_pengiriman' => 0,
                'perusahaan_id' => $perusahaan_id,
                'status_pembayaran' => ($post['metode_bayar'] == 'piutang') ? 'pending' : 'paid',
                'tanggal' => $tanggal_transaksi,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('tbl_pemesanan', $pemesanan_data);

            // 2. Details
            foreach ($products as $product) {
                // Handle manual product input (if string/new tag from Select2)
                $p_id = $this->resolve_product_id($product['product_id']);

                $qty = $product['jumlah'];
                $h_satuan = $product['harga_satuan'];
                $h_id = $this->get_harga_id($p_id);

                $data_detail = [
                    'nospk' => $nospk,
                    'product_id' => $p_id,
                    'qty' => $qty,
                    'harga_id' => $h_id,
                    'dateline_tgl' => date('Y-m-d'),
                    'dateline_jam' => date('H:i:s'),
                    'posisi_barang' => 1,
                    'lokasi_cetak' => 1,
                    'approve' => 1,
                    'metadata' => json_encode([
                        'unit_price' => $h_satuan,
                        'subtotal' => $h_satuan * $qty,
                        'product_name' => $product['product_name'] ?? null,
                        'price_snapshot_at' => date('Y-m-d H:i:s')
                    ])
                ];
                
                if ($this->db->field_exists('snapshot_harga', 'tbl_pemesanan_detail')) {
                    $data_detail['snapshot_harga'] = $h_satuan;
                }
                if ($this->db->field_exists('snapshot_subtotal', 'tbl_pemesanan_detail')) {
                    $data_detail['snapshot_subtotal'] = $h_satuan * $qty;
                }
                if ($this->db->field_exists('unit_price', 'tbl_pemesanan_detail')) {
                    $data_detail['unit_price'] = $h_satuan;
                }
                if ($this->db->field_exists('subtotal', 'tbl_pemesanan_detail')) {
                    $data_detail['subtotal'] = $h_satuan * $qty;
                }
                // Insert into production queue detail
                $this->db->insert('tbl_pemesanan_detail', $data_detail);

                // Insert into payment item detail (Used by Invoice)
                $pembayaran_produk = [
                    'nospk' => $nospk,
                    'product_id' => $p_id,
                    'qty' => $qty,
                    'harga_id' => $h_id,
                    'dateline_tgl' => date('Y-m-d'),
                    'dateline_jam' => date('H:i:s'),
                    'approve' => '1',
                    'metadata' => json_encode([
                        'unit_price' => $h_satuan,
                        'subtotal' => $h_satuan * $qty,
                        'product_name' => $product['product_name'] ?? null
                    ])
                ];
                if ($this->db->field_exists('snapshot_harga', 'tbl_pembayaran_produk')) {
                    $pembayaran_produk['snapshot_harga'] = $h_satuan;
                }
                if ($this->db->field_exists('snapshot_subtotal', 'tbl_pembayaran_produk')) {
                    $pembayaran_produk['snapshot_subtotal'] = $h_satuan * $qty;
                }
                if ($this->db->field_exists('snapshot_detail_product', 'tbl_pembayaran_produk')) {
                    $pembayaran_produk['snapshot_detail_product'] = $product['product_name'] ?? null;
                }
                if ($this->db->field_exists('snapshot_source_harga_id', 'tbl_pembayaran_produk')) {
                    $pembayaran_produk['snapshot_source_harga_id'] = $h_id;
                }
                $this->db->insert('tbl_pembayaran_produk', $pembayaran_produk);
            }

            // 3. tbl_pembayaran
            $pembayaran_data = [
                'nospk' => $nospk,
                'konsumen_id' => $post['konsumen_id'],
                'tgl_bayar' => $tanggal_transaksi,
                'jam_bayar' => date('H:i:s'),
                'total_tagihan' => $total_tagihan,
                'total_bayar' => ($post['metode_bayar'] == 'piutang') ? 0 : $total_tagihan,
                'status_lunas' => ($post['metode_bayar'] == 'piutang') ? '0' : '1',
                'kasir_id' => $kasir_id,
                'perusahaan_id' => $perusahaan_id
            ];
            $this->db->insert('tbl_pembayaran', $pembayaran_data);
            $pembayaran_id = $this->db->insert_id();

            // 4. tbl_pembayaran_detail (MAPPING METODE)
            if ($post['metode_bayar'] != 'piutang') {
                $metode_mapped = $post['metode_bayar'];
                if ($metode_mapped == 'cash')
                    $metode_mapped = 'tunai';

                $allowed_metode = ['tunai', 'transfer', 'debit', 'kartu_kredit', 'ewallet', 'lainnya'];
                if (!in_array($metode_mapped, $allowed_metode)) {
                    $metode_mapped = 'tunai';
                }

                $p_detail = [
                    'pembayaran_id' => $pembayaran_id,
                    'metode' => $metode_mapped,
                    'jumlah' => $total_tagihan,
                    'referensi' => $post['referensi'] ?? null,
                    'bank_id' => ($post['metode_bayar'] == 'transfer') ? ($post['rekening_id'] ?? null) : null,
                    'edc_id' => ($post['metode_bayar'] == 'debit') ? ($post['edc_id'] ?? null) : null,
                    'ewallet_id' => ($post['metode_bayar'] == 'ewallet') ? ($post['ewallet_id'] ?? null) : null,
                    'keterangan' => $post['keterangan'] ?? null
                ];
                $this->db->insert('tbl_pembayaran_detail', $p_detail);
            }

            // 5. tbl_piutang
            if ($post['metode_bayar'] == 'piutang') {
                $konsumen = $this->get_konsumen_by_id($post['konsumen_id']);
                $termin = (isset($konsumen->termin) && $konsumen->termin > 0) ? $konsumen->termin : 30;
                $this->db->insert('tbl_piutang', [
                    'nospk' => $nospk,
                    'konsumen_id' => $post['konsumen_id'],
                    'perusahaan_id' => $perusahaan_id,
                    'total_tagihan' => $total_tagihan,
                    'total_bayar' => 0,
                    'jatuh_tempo' => date('Y-m-d', strtotime("+{$termin} days")),
                    'status' => 'BELUM LUNAS'
                ]);
            }

            $this->generateQrCode($nospk);
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $db_err = $this->db->error();
                $err_details = !empty($db_err['message']) ? $db_err['message'] . ' (Code: ' . $db_err['code'] . ')' : 'Database transaction failed inside trans_complete.';
                log_message('error', 'ERP Manual Trans Detailed MySQL Error: ' . $err_details);
                throw new Exception('Database transaction failed: ' . $err_details);
            }

            return ['status' => true, 'nospk' => $nospk, 'jumlah_produk' => count($products)];
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'ERP Manual Trans Error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function generate_nospk()
    {
        $prefix = 'MAN-';
        $date = date('dmy');
        $pattern = $prefix . $date . '-%';

        // Check MAX sequence across both tbl_pemesanan and tbl_spk to prevent duplicate keys
        $q1 = $this->db->query("SELECT MAX(CAST(SUBSTRING_INDEX(nospk, '-', -1) AS UNSIGNED)) as max_num FROM tbl_pemesanan WHERE nospk LIKE ?", [$pattern])->row();
        $q2 = $this->db->query("SELECT MAX(CAST(SUBSTRING_INDEX(nospk, '-', -1) AS UNSIGNED)) as max_num FROM tbl_spk WHERE nospk LIKE ?", [$pattern])->row();

        $max_num = max((int) ($q1->max_num ?? 0), (int) ($q2->max_num ?? 0));
        $next_num = str_pad($max_num + 1, 3, '0', STR_PAD_LEFT);

        return $prefix . $date . '-' . $next_num;
    }

    private function get_next_no_urut($perusahaan_id, $user_id)
    {
        $this->db->select('no_urut_spk');
        $this->db->where('perusahaan_id', $perusahaan_id);
        $this->db->where('user_id', $user_id);
        $this->db->order_by('no_urut_spk', 'DESC');
        $this->db->limit(1);
        $res = $this->db->get('tbl_spk')->row();
        return $res ? ($res->no_urut_spk + 1) : 1;
    }

    private function generateQrCode($nospk)
    {
        try {
            $target_dir = FCPATH . 'assets/qrcode/';
            if (!is_dir($target_dir)) {
                @mkdir($target_dir, 0777, true);
            }

            $this->load->library('ciqrcode');
            $config = [
                'cacheable' => true,
                'cachedir' => './assets/',
                'errorlog' => './assets/',
                'imagedir' => './assets/qrcode/',
                'quality' => true,
                'size' => '1024',
                'black' => [0, 0, 0],
                'white' => [255, 255, 255],
            ];
            $this->ciqrcode->initialize($config);
            $params = [
                'data' => "https://erp.tgmprint.com/cekpesanan/spk/" . urlencode(encrypt_spk($nospk)),
                'level' => 'H',
                'size' => 10,
                'savename' => $target_dir . $nospk . '.png',
            ];
            $this->ciqrcode->generate($params);
        } catch (\Throwable $e) {
            log_message('error', 'Generate QR Code warning: ' . $e->getMessage());
        }
    }

    public function get_harga_id($product_id)
    {
        $harga = $this->db->get_where('tbl_harga_product', ['product_id' => $product_id])->row();
        return $harga ? $harga->harga_id : 1;
    }

    public function get_transaksi_terakhir($limit = 10)
    {
        $this->db->select('p.*, k.konsumen_nama, pb.total_tagihan, pb.status_lunas, COUNT(pp.id) as jumlah_produk');
        $this->db->from('tbl_pemesanan p');
        $this->db->join('tbl_konsumen k', 'k.konsumen_id = p.konsumen_id');
        $this->db->join('tbl_pembayaran_produk pp', 'pp.nospk = p.nospk', 'left');
        $this->db->join('tbl_pembayaran pb', 'pb.nospk = p.nospk', 'left');
        $this->db->group_by('p.nospk');
        $this->db->order_by('p.created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    public function get_transaksi_by_nospk($nospk)
    {
        $this->db->select('p.*, k.konsumen_nama, k.konsumen_alamat, k.konsumen_nohp, k.konsumen_email, 
                          pb.pembayaran_id, pb.total_tagihan, pb.total_bayar, pb.tgl_bayar, pb.jam_bayar, pb.status_lunas,
                          pbd.metode, pbd.referensi, pbd.keterangan, pbd.bank_id, pbd.ewallet_id, pbd.edc_id,
                          r.nama_bank, r.no_rekening, r.atas_nama,
                          ew.ewallet_nama,
                          edc.edc_nama');
        $this->db->from('tbl_pemesanan p');
        $this->db->join('tbl_konsumen k', 'k.konsumen_id = p.konsumen_id');
        $this->db->join('tbl_pembayaran pb', 'pb.nospk = p.nospk', 'left');
        $this->db->join('tbl_pembayaran_detail pbd', 'pbd.pembayaran_id = pb.pembayaran_id', 'left');
        $this->db->join('tbl_rekening r', 'r.id = pbd.bank_id', 'left');
        $this->db->join('tbl_ewallet ew', 'ew.id = pbd.ewallet_id', 'left');
        $this->db->join('tbl_mesin_edc edc', 'edc.edc_id = pbd.edc_id', 'left');
        $this->db->where('p.nospk', $nospk);
        $transaksi = $this->db->get()->row();

        if ($transaksi) {
            $this->db->select('pd.*, pr.product_nama, hp.harga_1, pp.metadata, pp.qty as pp_qty');
            $this->db->from('tbl_pemesanan_detail pd');
            $this->db->join('tbl_product pr', 'pr.product_id = pd.product_id', 'left');
            $this->db->join('tbl_harga_product hp', 'hp.harga_id = pd.harga_id', 'left');
            $this->db->join('tbl_pembayaran_produk pp', 'pp.nospk = pd.nospk AND pp.product_id = pd.product_id', 'left');
            $this->db->where('pd.nospk', $nospk);
            $products = $this->db->get()->result();

            foreach ($products as $p) {
                // Gunakan qty dari pemesanan_detail
                $p->qty = $p->qty ?? $p->pp_qty ?? 1;
                
                // Fallback harga dari tbl_harga_product
                $p->unit_price = $p->harga_1 ?? 0;

                if (!empty($p->metadata)) {
                    $meta = json_decode($p->metadata, true);
                    if (isset($meta['unit_price']))
                        $p->unit_price = $meta['unit_price'];
                    if (!empty($meta['product_name']))
                        $p->product_nama = $meta['product_name'];
                }
            }
            $transaksi->products = $products;
        }

        return $transaksi;
    }
    public function get_transactions($filters = [])
    {
        $this->db->select('p.nospk as transaction_id, p.nospk as transaction_number, p.created_at as transaction_date, 
                          k.konsumen_nama as customer_name, k.konsumen_nohp as customer_phone,
                          pb.total_tagihan as grand_total, 
                          p.status_pembayaran as payment_status');
        $this->db->from('tbl_pemesanan p');
        $this->db->join('tbl_konsumen k', 'k.konsumen_id = p.konsumen_id');
        $this->db->join('tbl_pembayaran pb', 'pb.nospk = p.nospk', 'left');

        // Only show transactions created via manual POS (prefixed with MAN-)
        $this->db->like('p.nospk', 'MAN-', 'after');

        if (!empty($filters['search'])) {
            $this->db->group_start()
                ->like('p.nospk', $filters['search'])
                ->or_like('k.konsumen_nama', $filters['search'])
                ->or_like('k.konsumen_nohp', $filters['search'])
                ->group_end();
        }

        if (!empty($filters['payment_status'])) {
            $this->db->where('p.status_pembayaran', $filters['payment_status']);
        }

        if (!empty($filters['date_from']))
            $this->db->where('DATE(p.created_at) >=', $filters['date_from']);
        if (!empty($filters['date_to']))
            $this->db->where('DATE(p.created_at) <=', $filters['date_to']);

        $this->db->order_by('p.created_at', 'DESC');
        $results = $this->db->get()->result();

        // Mapping statuses to match manual_list.php expected keys
        foreach ($results as $r) {
            if ($r->payment_status == 'paid') {
                $r->payment_status = 'paid';
                $r->status = 'completed';
            } else if ($r->payment_status == 'canceled') {
                $r->payment_status = 'unpaid';
                $r->status = 'cancelled';
            } else {
                $r->payment_status = 'unpaid';
                $r->status = 'pending';
            }
        }

        return $results;
    }

    public function get_transaction_stats($perusahaan_id = null)
    {
        $this->db->like('nospk', 'MAN-', 'after');
        $total_rows = $this->db->count_all_results('tbl_pemesanan');

        $this->db->like('nospk', 'MAN-', 'after');
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        $today_count = $this->db->count_all_results('tbl_pemesanan');

        $this->db->like('nospk', 'MAN-', 'after');
        $this->db->where('status_pembayaran', 'paid');
        $paid_count = $this->db->count_all_results('tbl_pemesanan');

        $this->db->select_sum('total_tagihan');
        $this->db->like('nospk', 'MAN-', 'after');
        $revenue = $this->db->get('tbl_pembayaran')->row()->total_tagihan ?: 0;

        return [
            'total' => $total_rows,
            'today' => $today_count,
            'paid' => $paid_count,
            'unpaid' => $total_rows - $paid_count,
            'total_revenue' => $revenue
        ];
    }

    public function get_transaction_by_id($id)
    {
        $this->db->select('p.nospk as transaction_id, p.nospk as transaction_number, p.created_at as transaction_date, 
                          k.konsumen_nama as customer_name, k.konsumen_nohp as customer_phone, k.konsumen_alamat as customer_address, k.konsumen_email as customer_email,
                          pb.total_tagihan as grand_total, pb.total_bayar, pb.status_lunas,
                          p.status_pembayaran as payment_status');
        $this->db->from('tbl_pemesanan p');
        $this->db->join('tbl_konsumen k', 'k.konsumen_id = p.konsumen_id');
        $this->db->join('tbl_pembayaran pb', 'pb.nospk = p.nospk', 'left');
        $this->db->where('p.nospk', $id);
        $res = $this->db->get()->row();

        if ($res) {
            // Map status
            if ($res->payment_status == 'paid')
                $res->status = 'completed';
            else if ($res->payment_status == 'canceled')
                $res->status = 'cancelled';
            else
                $res->status = 'pending';
        }
        return $res;
    }

    public function get_transaction_items($id)
    {
        $transaction = $this->db->get_where('tbl_pemesanan', ['nospk' => $id])->row();
        if (!$transaction)
            return [];

        $this->db->select('pp.*, pr.product_nama');
        $this->db->from('tbl_pembayaran_produk pp');
        $this->db->join('tbl_product pr', 'pr.product_id = pp.product_id');
        $this->db->where('pp.nospk', $transaction->nospk);
        $items = $this->db->get()->result();

        foreach ($items as $item) {
            $item->product_name = $item->product_nama;
            $item->quantity = $item->qty;

            // Extract price from metadata or fallback
            $item->unit_price = 0;
            if ($item->metadata) {
                $meta = json_decode($item->metadata, true);
                $item->unit_price = $meta['unit_price'] ?? 0;
                if (!empty($meta['product_name']))
                    $item->product_name = $meta['product_name'];
            }
            $item->total_price = $item->unit_price * $item->quantity;
        }

        return $items;
    }

    public function get_payment_history($id)
    {
        return $this->db->get_where('manual_payment_history', ['transaction_id' => $id])->result();
    }

    public function resolve_product_id($input)
    {
        // If numeric, assume it's an existing ID
        if (is_numeric($input)) {
            return (int) $input;
        }

        // If string, search by name first
        $existing = $this->db->get_where('tbl_product', ['product_nama' => $input])->row();
        if ($existing) {
            return $existing->product_id;
        }

        // Create new product if not found
        $this->db->insert('tbl_product', [
            'product_nama' => $input,
            'kategori_id' => 1, // Default category: Digital A3+
            'product_deskripsi' => 'Manual entry via POS',
            'status' => '1'
        ]);
        $new_id = $this->db->insert_id();

        // Initialize price entry for the new product with required fields
        $this->db->insert('tbl_harga_product', [
            'product_id' => $new_id,
            'detail_product' => $input,
            'qty_min' => 1,
            'qty_max' => 99999,
            'harga_aktif' => '1',
            'is_active' => '1',
            'jenisprint_id' => 0
        ]);

        return $new_id;
    }
}
