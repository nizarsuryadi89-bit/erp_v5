<?php
class M_pembayaran extends CI_Model
{
    private function decodeMetadataArray($metadata)
    {
        if (is_array($metadata)) {
            return $metadata;
        }

        if (empty($metadata) || !is_string($metadata)) {
            return [];
        }

        $decoded = json_decode($metadata, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function calculateSnapshotSubtotal($item, $harga_satuan)
    {
        $qty = (float) ($item->qty ?? 0);
        $panjang = (float) ($item->panjang ?? 0);
        $lebar = (float) ($item->lebar ?? 0);
        $harga_satuan = (float) $harga_satuan;
        $satuan_id = isset($item->satuan_id) ? (int) $item->satuan_id : 0;
        $kategori_id = isset($item->kategori_id) ? (int) $item->kategori_id : 0;
        $ket_1 = isset($item->ket_1) ? (string) $item->ket_1 : '';

        if ($satuan_id === 11) {
            $subtotal = $qty * $panjang * $harga_satuan;
        } else if ($kategori_id === 9 && $ket_1 === '5') {
            $subtotal = $qty * $panjang * $harga_satuan;
        } else if ($panjang != 0 && $lebar != 0) {
            $subtotal = $qty * $panjang * $lebar * $harga_satuan;
        } else if ($panjang != 0 && $lebar == 0) {
            $subtotal = $qty * $panjang * $harga_satuan;
        } else {
            $subtotal = $qty * $harga_satuan;
        }

        $harga_minimal = isset($item->harga_minimal) ? (float) $item->harga_minimal : 0;
        if ($harga_minimal > 0 && $subtotal < $harga_minimal) {
            return $harga_minimal;
        }

        return $subtotal;
    }

    private function buildPembayaranProdukSnapshot($item)
    {
        $metadata = $this->decodeMetadataArray($item->metadata ?? null);
        $harga_satuan = isset($metadata['unit_price']) && is_numeric($metadata['unit_price'])
            ? (float) $metadata['unit_price']
            : (float) ($item->harga_1 ?? 0);

        $subtotal = isset($metadata['subtotal']) && is_numeric($metadata['subtotal'])
            ? (float) $metadata['subtotal']
            : $this->calculateSnapshotSubtotal($item, $harga_satuan);

        $metadata['unit_price'] = $harga_satuan;
        $metadata['subtotal'] = (float) round($subtotal);
        $metadata['price_snapshot_at'] = $metadata['price_snapshot_at'] ?? date('Y-m-d H:i:s');

        $snapshot = [
            'metadata' => json_encode($metadata),
        ];

        if ($this->db->field_exists('snapshot_harga', 'tbl_pembayaran_produk')) {
            $snapshot['snapshot_harga'] = $harga_satuan;
        }
        if ($this->db->field_exists('snapshot_hpp', 'tbl_pembayaran_produk')) {
            $snapshot['snapshot_hpp'] = (float) ($item->hpp ?? 0);
        }
        if ($this->db->field_exists('snapshot_detail_product', 'tbl_pembayaran_produk')) {
            $snapshot['snapshot_detail_product'] = $item->detail_product ?? null;
        }
        if ($this->db->field_exists('snapshot_qty_min', 'tbl_pembayaran_produk')) {
            $snapshot['snapshot_qty_min'] = (float) ($item->qty_min ?? 0);
        }
        if ($this->db->field_exists('snapshot_qty_max', 'tbl_pembayaran_produk')) {
            $snapshot['snapshot_qty_max'] = (float) ($item->qty_max ?? 0);
        }
        if ($this->db->field_exists('snapshot_ket_1', 'tbl_pembayaran_produk')) {
            $snapshot['snapshot_ket_1'] = isset($item->ket_1) ? (string) $item->ket_1 : null;
        }
        if ($this->db->field_exists('snapshot_side_id', 'tbl_pembayaran_produk')) {
            $snapshot['snapshot_side_id'] = isset($item->side_id) ? (int) $item->side_id : null;
        }
        if ($this->db->field_exists('snapshot_jenisprint_id', 'tbl_pembayaran_produk')) {
            $snapshot['snapshot_jenisprint_id'] = isset($item->jenisprint_id) ? (int) $item->jenisprint_id : null;
        }
        if ($this->db->field_exists('snapshot_subtotal', 'tbl_pembayaran_produk')) {
            $snapshot['snapshot_subtotal'] = (float) round($subtotal);
        }
        if ($this->db->field_exists('snapshot_source_harga_id', 'tbl_pembayaran_produk')) {
            $snapshot['snapshot_source_harga_id'] = isset($item->harga_id) ? (int) $item->harga_id : null;
        }
        if ($this->db->field_exists('snapshot_valid_from', 'tbl_pembayaran_produk')) {
            $snapshot['snapshot_valid_from'] = $item->valid_from ?? null;
        }
        if ($this->db->field_exists('snapshot_valid_to', 'tbl_pembayaran_produk')) {
            $snapshot['snapshot_valid_to'] = $item->valid_to ?? null;
        }

        return $snapshot;
    }

    public function prosesTransaksi($post, $perusahaan, $userid)
    {
        $this->db->trans_start();

        $pembayaran_id = $this->insertPembayaran($post, $perusahaan, $userid);
        $this->insertPembayaranDetail($post, $pembayaran_id);
        $this->insertDetailDanProduksi($post['nospk'], $perusahaan);

        // Handle piutang if payment is incomplete
        $this->handlePiutang($post, $perusahaan, $userid);

        $this->updateStatusPemesanan($post['nospk'], $post);
        $this->generateQrCode($post['nospk']);

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            return ['status' => 'success', 'message' => 'Data Berhasil Dikirim'];
        } else {
            return ['status' => 'error', 'message' => 'Gagal menyimpan data'];
        }
    }

    private function insertPembayaran($post, $perusahaan, $userid)
    {
        // Calculate total payment amount
        $total_bayar = 0;
        $total_bayar += rupiahToInt($post['bayar_tunai'] ?? 0);
        $total_bayar += rupiahToInt($post['bayar_transfer'] ?? 0);
        $total_bayar += rupiahToInt($post['bayar_debit'] ?? 0);
        $total_bayar += rupiahToInt($post['bayar_ewallet'] ?? 0);
        $total_bayar += rupiahToInt($post['bayar_dp'] ?? 0); // For piutang method
        $total_bayar += rupiahToInt($post['bayar_qris'] ?? 0);

        // Determine if payment is complete
        $total_tagihan = rupiahToInt($post['grandtotal']);
        $status_lunas = ($total_bayar >= $total_tagihan) ? '1' : '0';

        $data = [
            'nospk' => $post['nospk'],
            'tgl_bayar' => $post['pembayaran_tgl'],
            'jam_bayar' => $post['pembayaran_jam'],
            'total_tagihan' => $total_tagihan,
            'total_bayar' => $total_bayar,
            'status_lunas' => $status_lunas,
            'kasir_id' => $userid,
            'perusahaan_id' => $perusahaan,
            'konsumen_id' => $post['konsumen_id'],
            'diskon_total' => isset($post['diskon']) ? rupiahToInt($post['diskon']) : 0,
            'ongkir' => isset($post['ongkir']) ? rupiahToInt($post['ongkir']) : 0,
        ];

        // Check if pembayaran for this nospk already exists
        $existing = $this->db->get_where('tbl_pembayaran', ['nospk' => $post['nospk']])->row();
        if ($existing) {
            $this->db->where('pembayaran_id', $existing->pembayaran_id);
            $this->db->update('tbl_pembayaran', $data);
            // Delete old details to avoid accumulation if they are re-submitting checkout
            $this->db->delete('tbl_pembayaran_detail', ['pembayaran_id' => $existing->pembayaran_id]);
            return $existing->pembayaran_id;
        } else {
            $this->db->insert('tbl_pembayaran', $data);
            return $this->db->insert_id();
        }
    }

    private function insertPembayaranDetail($post, $pembayaran_id)
    {
        $metode = $post['metode'];
        $details = [];

        // Handle different payment methods
        switch ($metode) {
            case 'cash':
                if (!empty($post['bayar_tunai']) && rupiahToInt($post['bayar_tunai']) > 0) {
                    $details[] = [
                        'pembayaran_id' => $pembayaran_id,
                        'metode' => 'tunai',
                        'jumlah' => rupiahToInt($post['bayar_tunai']),
                        'referensi' => null,
                        'bank_id' => null,
                        'edc_id' => null,
                        'ewallet_id' => null,
                        'keterangan' => 'Pembayaran tunai'
                    ];
                }
                break;

            case 'transfer':
                if (!empty($post['bayar_transfer']) && rupiahToInt($post['bayar_transfer']) > 0) {
                    $bank_id = (!empty($post['rekening_id']) && $post['rekening_id'] != "0") ? $post['rekening_id'] : null;
                    $details[] = [
                        'pembayaran_id' => $pembayaran_id,
                        'metode' => 'transfer',
                        'jumlah' => rupiahToInt($post['bayar_transfer']),
                        'referensi' => null,
                        'bank_id' => $bank_id,
                        'edc_id' => null,
                        'ewallet_id' => null,
                        'keterangan' => 'Transfer bank'
                    ];
                }
                break;

            case 'edc':
                if (!empty($post['bayar_debit']) && rupiahToInt($post['bayar_debit']) > 0) {
                    $edc_id = (!empty($post['id_edc']) && $post['id_edc'] != "0") ? $post['id_edc'] : null;
                    $details[] = [
                        'pembayaran_id' => $pembayaran_id,
                        'metode' => 'debit',
                        'jumlah' => rupiahToInt($post['bayar_debit']),
                        'referensi' => $post['nomor_debit'] ?? null,
                        'bank_id' => null,
                        'edc_id' => $edc_id,
                        'ewallet_id' => null,
                        'keterangan' => 'Pembayaran EDC/QRIS'
                    ];
                }
                break;

            case 'ewallet':
                if (!empty($post['bayar_ewallet']) && rupiahToInt($post['bayar_ewallet']) > 0) {
                    $ewallet_id = (!empty($post['ewallet_id']) && $post['ewallet_id'] != "0") ? $post['ewallet_id'] : null;
                    $details[] = [
                        'pembayaran_id' => $pembayaran_id,
                        'metode' => 'ewallet',
                        'jumlah' => rupiahToInt($post['bayar_ewallet']),
                        'referensi' => null,
                        'bank_id' => null,
                        'edc_id' => null,
                        'ewallet_id' => $ewallet_id,
                        'keterangan' => 'Pembayaran E-Wallet'
                    ];
                }
                break;

            case 'qris':
                if (!empty($post['bayar_qris']) && rupiahToInt($post['bayar_qris']) > 0) {
                    $bank_id = (!empty($post['rekening_qris_id']) && $post['rekening_qris_id'] != "0") ? $post['rekening_qris_id'] : null;
                    $details[] = [
                        'pembayaran_id' => $pembayaran_id,
                        'metode' => 'qris',
                        'jumlah' => rupiahToInt($post['bayar_qris']),
                        'referensi' => null,
                        'bank_id' => $bank_id,
                        'edc_id' => null,
                        'ewallet_id' => null,
                        'keterangan' => 'Pembayaran QRIS'
                    ];
                }
                break;

            case 'piutang':
                if (!empty($post['bayar_dp']) && rupiahToInt($post['bayar_dp']) > 0) {
                    $metode_dp = $post['metode_dp'] ?? 'cash';
                    $bank_id = null;
                    $edc_id = null;
                    $ket_metode = 'Tunai';

                    if ($metode_dp === 'transfer') {
                        $metode_db = 'transfer';
                        $bank_id = (!empty($post['rekening_id_dp']) && $post['rekening_id_dp'] != "0") ? $post['rekening_id_dp'] : null;
                        $ket_metode = 'Transfer';
                    } elseif ($metode_dp === 'edc') {
                        $metode_db = 'debit';
                        $edc_id = (!empty($post['id_edc_dp']) && $post['id_edc_dp'] != "0") ? $post['id_edc_dp'] : null;
                        $ket_metode = 'EDC/QRIS';
                    } else {
                        $metode_db = 'tunai';
                    }

                    $details[] = [
                        'pembayaran_id' => $pembayaran_id,
                        'metode' => $metode_db,
                        'jumlah' => rupiahToInt($post['bayar_dp']),
                        'referensi' => null,
                        'bank_id' => $bank_id,
                        'edc_id' => $edc_id,
                        'ewallet_id' => null,
                        'keterangan' => "DP ($ket_metode) - Sisa piutang: " . rupiahToInt($post['piutang'])
                    ];
                }
                break;
        }

        // Insert all payment details
        foreach ($details as $detail) {
            $this->db->insert('tbl_pembayaran_detail', $detail);
        }
    }

    public function insertDetailDanProduksi($nospk, $perusahaan)
    {
        log_message('debug', 'insertDetailDanProduksi called with nospk: ' . $nospk . ', perusahaan: ' . $perusahaan);

        // [MEKANISME AMAN REVISI: Bersihkan data produksi redundan/double]
        $this->_cleanup_existing_produksi($nospk);

        // Load M_transaksi model if not already loaded
        if (!isset($this->M_transaksi)) {
            $this->load->model('M_transaksi');
        }

        $details = $this->M_transaksi->getDetailPesan($nospk)->result();
        log_message('debug', 'Details found: ' . count($details));

        if (!$details) {
            log_message('error', 'Tidak ada detail pesanan untuk nospk: ' . $nospk);
            return false;
        }

        foreach ($details as $item) {
            log_message('debug', 'Processing item: ' . json_encode($item));

            // Insert ke tbl_pembayaran_produk (untuk detail produk yang dibayar)
            $pembayaran_produk_data = [
                'nospk' => $item->nospk,
                'product_id' => $item->product_id,
                'qty' => $item->qty,
                'diskon' => $item->diskon,
                'panjang' => $item->panjang,
                'lebar' => $item->lebar,
                'mesin_id' => $item->mesin_id,
                'finishing_id' => $item->finishing_id,
                'harga_id' => $item->harga_id,
                'dateline_tgl' => $item->dateline_tgl,
                'dateline_jam' => $item->dateline_jam,
                'lokasi_cetak' => $item->lokasi_cetak,
                'approve' => $item->approve,
                'keterangan' => $item->keterangan ?? null,
            ];
            $pembayaran_produk_data = array_merge($pembayaran_produk_data, $this->buildPembayaranProdukSnapshot($item));

            $detail_insert_result = $this->db->insert('tbl_pembayaran_produk', $pembayaran_produk_data);

            if (!$detail_insert_result) {
                log_message('error', 'Gagal insert ke tbl_pembayaran_produk: ' . $this->db->last_query());
                log_message('error', 'DB Error: ' . $this->db->error()['message']);
            } else {
                log_message('debug', 'Pembayaran produk inserted successfully');
            }

            // Insert ke tbl_produksi dan detail_produksi
            $produksi_insert_result = $this->insertProduksi($item, $perusahaan);

            if (!$produksi_insert_result) {
                log_message('error', 'Gagal insert ke tbl_produksi untuk item: ' . $item->product_id);
            } else {
                log_message('debug', 'Produksi inserted successfully for item: ' . $item->product_id);
            }
        }

        log_message('debug', 'insertDetailDanProduksi completed successfully');
        return true;
    }

    /**
     * CLEANUP MEKANISME (Mencegah Double Data Produksi Saat Kasir Revisi & Bayar Ulang)
     */
    private function _cleanup_existing_produksi($nospk)
    {
        // 1. Ambil data Produksi yang sudah ada dari SPK tersebut
        $old_produksi = $this->db->get_where('tbl_produksi', ['nospk' => $nospk])->result();
        $prod_ids = [];
        foreach ($old_produksi as $prod) {
            $prod_ids[] = $prod->produksi_id;
        }

        if (!empty($prod_ids)) {
            // Evaluasi Progress Operator: 
            // Jika kita ganti total, record qty_output operator akan hilang.
            // Solusi aman: Kita Hapus yang status_produksi != 2 atau jika mau bersih, hapus semua 
            // karena revisi SPK berarti spec material / qty berubah, operator harus ikut SPK baru.

            // Hapus routing detail produksi lama
            $this->db->where_in('produksi_id', $prod_ids);
            $this->db->delete('detail_produksi');

            // Hapus tabel produksi master lama  
            $this->db->where('nospk', $nospk);
            $this->db->delete('tbl_produksi');
        }

        // Hapus pembayaran produk lama
        $this->db->where('nospk', $nospk);
        $this->db->delete('tbl_pembayaran_produk');
    }

    private function insertProduksi($item, $perusahaan)
    {
        // Debug: Log the incoming data
        log_message('debug', '=== START insertProduksi ===');
        log_message('debug', 'insertProduksi called with item: ' . json_encode($item));
        log_message('debug', 'insertProduksi called with perusahaan: ' . $perusahaan);

        // Load M_master model if not already loaded
        if (!isset($this->M_master)) {
            $this->load->model('M_master');
            log_message('debug', 'M_master model loaded');
        }

        // Ambil semua data routing berdasarkan product_id dan harga_id (jika ada variasi)
        log_message('debug', 'Getting routing for product_id: ' . $item->product_id . ' and harga_id: ' . ($item->harga_id ?? 'NULL'));
        $routings = $this->M_master->routingbykode($item->product_id, $item->harga_id ?? null)->result_array();
        log_message('debug', 'Routings found: ' . count($routings));
        log_message('debug', 'Routings data: ' . json_encode($routings));

        // Pastikan konsumen_id tersedia
        $konsumen_id = isset($item->konsumen_id) ? $item->konsumen_id : null;
        log_message('debug', 'Initial konsumen_id: ' . $konsumen_id);

        // Jika konsumen_id tidak ada, ambil dari tbl_spk
        if (!$konsumen_id) {
            log_message('debug', 'Konsumen ID not found in item, checking SPK table for nospk: ' . $item->nospk);
            $spk_data = $this->db->get_where('tbl_spk', ['nospk' => $item->nospk])->row();
            if ($spk_data) {
                $konsumen_id = $spk_data->konsumen_id;
                log_message('debug', 'Konsumen ID from SPK: ' . $konsumen_id);
            } else {
                log_message('warning', 'SPK data not found for nospk: ' . $item->nospk);
            }
        }

        // Data untuk tbl_produksi sesuai dengan struktur tabel yang diberikan
        $produksi_data = [
            'dateline_tgl' => $item->dateline_tgl,
            'konsumen_id' => $konsumen_id,
            'dateline_jam' => $item->dateline_jam,
            'product_id' => $item->product_id,
            'harga_id' => $item->harga_id,
            'qty' => $item->qty,
            'panjang' => $item->panjang,
            'lebar' => $item->lebar,
            'nospk' => $item->nospk,
            'created_by' => $this->session->userdata('id') ?? 0,
            'perusahaan_id' => $perusahaan,
            'update_time' => date('Y-m-d H:i:s'),
            'approve' => $item->approve,
            'catatan' => $item->keterangan ?? null,
            'status_produksi' => (empty($routings)) ? 'done' : 'approved',
            'lokasi_cetak' => $item->lokasi_cetak,
            'tgl_selesai' => (empty($routings)) ? date('Y-m-d H:i:s') : '0000-00-00 00:00:00',
            'tgl_kirim_pb' => '0000-00-00 00:00:00',
            'foto_handover' => '',
            'metadata' => $item->metadata ?? null
        ];

        log_message('debug', 'Produksi data to insert: ' . json_encode($produksi_data));

        // Insert tbl_produksi
        log_message('debug', 'Inserting into tbl_produksi...');
        $produksi_insert_result = $this->db->insert('tbl_produksi', $produksi_data);
        if (!$produksi_insert_result) {
            log_message('error', 'Gagal insert ke tbl_produksi: ' . $this->db->last_query());
            log_message('error', 'DB Error: ' . $this->db->error()['message']);
            return false;
        }

        $produksi_id = $this->db->insert_id();
        log_message('debug', 'Produksi ID generated: ' . $produksi_id);

        if (!$produksi_id) {
            log_message('error', 'Gagal mendapatkan produksi_id');
            return false;
        }

        // Insert awal ke detail_produksi
        if ($routings && count($routings) > 0) {
            log_message('debug', 'Starting to insert detail_produksi records...');
            foreach ($routings as $index => $routing) {
                log_message('debug', 'Processing routing ' . ($index + 1) . ': ' . json_encode($routing));

                // Check if required routing fields exist
                if (!isset($routing['routing_id'])) {
                    log_message('error', 'routing_id not found in routing data: ' . json_encode($routing));
                    continue;
                }

                if (!isset($routing['routing_urutan'])) {
                    log_message('error', 'routing_urutan not found in routing data: ' . json_encode($routing));
                    continue;
                }

                $detail_data = [
                    'produksi_id' => $produksi_id,
                    'routing_id' => $routing['routing_id'],
                    'routing_urutan' => $routing['routing_urutan'],
                    'urutan_sebenarnya' => null, // akan diisi saat proses produksi
                    'mesin_id' => null, // diisi saat proses mulai
                    'operator_id' => isset($routing['operator_id']) ? $routing['operator_id'] : null,
                    'qty_input' => $item->qty,
                    'qty_output' => 0,
                    'qty_missing' => 0,
                    'panjang_output' => $item->panjang,
                    'lebar_output' => $item->lebar,
                    'lokasi_proses' => $item->lokasi_cetak,
                    'material_id' => isset($routing['material_id']) ? $routing['material_id'] : null,
                    'alasan_missing' => null,
                    'catatan' => $item->keterangan ?? null,
                    'status_produksi' => '0', // pending sesuai enum
                    'biaya_proses' => 0.00,
                    'waktu_mulai' => null,
                    'waktu_selesai' => null,
                    'update_time' => date('Y-m-d H:i:s')
                ];

                log_message('debug', 'Detail produksi data to insert: ' . json_encode($detail_data));

                // Check if detail_produksi table exists and has required fields
                $table_exists = $this->db->table_exists('detail_produksi');
                log_message('debug', 'detail_produksi table exists: ' . ($table_exists ? 'YES' : 'NO'));

                if ($table_exists) {
                    $fields = $this->db->list_fields('detail_produksi');
                    log_message('debug', 'detail_produksi table fields: ' . json_encode($fields));

                    // Check if all required fields exist
                    $required_fields = ['produksi_id', 'routing_id', 'routing_urutan', 'qty_input', 'qty_output', 'qty_missing', 'panjang_output', 'lebar_output', 'status_produksi', 'update_time', 'biaya_proses'];
                    $missing_fields = [];
                    foreach ($required_fields as $field) {
                        if (!in_array($field, $fields)) {
                            $missing_fields[] = $field;
                        }
                    }

                    if (!empty($missing_fields)) {
                        log_message('error', 'Missing required fields in detail_produksi: ' . json_encode($missing_fields));
                        continue;
                    }
                }

                log_message('debug', 'Attempting to insert detail_produksi record...');
                $detail_insert_result = $this->db->insert('detail_produksi', $detail_data);
                if (!$detail_insert_result) {
                    log_message('error', 'Gagal insert ke detail_produksi: ' . $this->db->last_query());
                    log_message('error', 'DB Error: ' . $this->db->error()['message']);
                    log_message('error', 'Error Code: ' . $this->db->error()['code']);
                } else {
                    $detail_id = $this->db->insert_id();
                    log_message('debug', 'Detail produksi inserted successfully with ID: ' . $detail_id);
                }
            }
        } else {
            log_message('warning', 'No routings found for product_id: ' . $item->product_id . '. Attempting Auto-Deduct Stok Barang Jadi.');
            $this->autoDeductStock($item, $perusahaan, $produksi_id);
        }

        log_message('debug', '=== END insertProduksi ===');
        return true;
    }

    /**
     * [NEW] Auto-Deduct Stock for Finished Goods (Barang Jadi) bypassing WIP
     */
    private function autoDeductStock($item, $perusahaan, $produksi_id)
    {
        log_message('debug', 'START autoDeductStock for Produksi ID: ' . $produksi_id);
        $this->load->model('M_gudang');

        // Fetch BOMs for this product and variation (if applicable)
        $this->db->where('product_id', $item->product_id);
        if (!empty($item->harga_id)) {
            $this->db->group_start();
            $this->db->where('harga_id', $item->harga_id);
            $this->db->or_where('harga_id IS NULL', null, false);
            $this->db->or_where('harga_id', 0);
            $this->db->group_end();
        }
        $boms = $this->db->get('bom_lines')->result();

        if (empty($boms) && !empty($item->harga_id)) {
            // [NEW] Fallback Logic: Check if another price tier of the SAME variation has a BOM
            $current_harga = $this->db->get_where('tbl_harga_product', ['harga_id' => $item->harga_id])->row();
            if ($current_harga && !empty($current_harga->metadata)) {
                $sibling_hargas = $this->db->select('harga_id')
                    ->where('product_id', $item->product_id)
                    ->where('metadata', $current_harga->metadata)
                    ->where('harga_id !=', $item->harga_id)
                    ->get('tbl_harga_product')->result_array();
                
                if (!empty($sibling_hargas)) {
                    $sibling_ids = array_column($sibling_hargas, 'harga_id');
                    $this->db->where('product_id', $item->product_id);
                    $this->db->where_in('harga_id', $sibling_ids);
                    $boms = $this->db->get('bom_lines')->result();
                }
            }
        }

        if (empty($boms)) {
            log_message('debug', 'No BOM found for auto-deduct. Skipping.');
            return;
        }

        foreach ($boms as $bom) {
            $bom_qty = (float) $bom->quantity;
            $order_qty = (float) $item->qty;
            $panjang = (float) ($item->panjang ?: 0);
            $lebar = (float) ($item->lebar ?: 0);
            $berat = 0;

            $calc_type = isset($bom->calc_type) ? $bom->calc_type : 'fixed';
            $qty_usage = 0;

            switch ($calc_type) {
                case 'area':
                    $qty_usage = ($panjang * $lebar) * $bom_qty * $order_qty;
                    break;
                case 'length':
                    $qty_usage = $panjang * $bom_qty * $order_qty;
                    break;
                case 'weight':
                    $qty_usage = $berat * $bom_qty * $order_qty;
                    break;
                case 'fixed':
                default:
                    $qty_usage = $bom_qty * $order_qty;
                    break;
            }

            // Apply scrap factor
            $scrap = isset($bom->scrap_factor) ? (float) $bom->scrap_factor : 0;
            if ($scrap > 0) {
                $qty_usage = $qty_usage * (1 + ($scrap / 100));
            }

            if ($qty_usage > 0 && !empty($bom->bahan_id)) {
                $gudang_id = $item->lokasi_cetak ?: 1;

                // Ambil informasi material untuk konversi Satuan Produksi -> Satuan Gudang
                $mat = $this->db->select('m.konversi, m.varian, s.satuan_nama as satuan_gudang, sp.satuan_nama as satuan_produksi')
                    ->from('tbl_material m')
                    ->join('tbl_bahan b', 'm.bahan_id = b.bahan_id', 'left')
                    ->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id', 'left')
                    ->join('tbl_satuan sp', 'b.satuan_id = sp.satuan_id', 'left')
                    ->where('m.material_id', $bom->bahan_id)
                    ->get()->row();

                $konversi = ($mat && (float)$mat->konversi > 0) ? (float)$mat->konversi : 1;
                $sat_prod = ($mat && $mat->satuan_produksi) ? $mat->satuan_produksi : 'unit';
                $sat_gudang = ($mat && $mat->satuan_gudang) ? $mat->satuan_gudang : 'unit';

                // Konversi pengurangan stok ke Satuan Gudang karena update_stock_with_log mengalikan $qty * $konversi
                $warehouse_reduction = (float) $qty_usage / $konversi;

                $catatan = "Auto-Deduct Barang Jadi SPK: {$item->nospk}. Pakai: {$qty_usage} {$sat_prod}";
                if ($konversi != 1) {
                    $catatan .= " [Konversi: " . round($warehouse_reduction, 4) . " {$sat_gudang}]";
                }

                $this->M_gudang->update_stock_with_log(
                    $bom->bahan_id, // This is material_id despite the column name bahan_id in bom_lines
                    $gudang_id,
                    -$warehouse_reduction, // negative to deduct in Satuan Gudang
                    'Produksi',
                    $produksi_id,
                    $catatan
                );
                log_message('debug', "Auto-Deducted {$qty_usage} {$sat_prod} (WH: {$warehouse_reduction} {$sat_gudang}) for material_id: {$bom->bahan_id}");
            }
        }
        
        log_message('debug', 'END autoDeductStock');
    }


    public function insertPiutang($post, $perusahaan)
    {
        $piutang = isset($post['piutang']) ? rupiahToInt($post['piutang']) : 0;
        if ($piutang <= 0)
            return; // Tidak perlu insert jika piutang <= 0

        $input_piutang = array(
            'nospk' => $post['nospk'],
            'konsumen_id' => $post['konsumen_id'],
            'piutang_tgl' => $post['pembayaran_tgl'],
            'piutang_jam' => $post['pembayaran_jam'],
            'piutang_total' => $piutang,
            'tempo_tgl' => date('Y-m-d', strtotime('+7 days')),
            'piutang_bayar' => 0,
            'piutang_sisa' => $piutang,

            'piutang_status' => 'Belum Lunas',
            'perusahaan_id' => $perusahaan,
        );

        $this->db->insert('tbl_piutang', $input_piutang);
    }



    private function updateStatusPemesanan($nospk, $post = null)
    {
        $data = ['status_pembayaran' => 'paid'];

        // Sinkronisasi tanggal SPK dengan tanggal pembayaran Kasir
        // Ini memastikan SPK muncul di antrian Produksi (WIP) dan Laporan pada hari yang tepat
        if ($post && !empty($post['pembayaran_tgl'])) {
            $jam = !empty($post['pembayaran_jam']) ? $post['pembayaran_jam'] : date('H:i:s');
            $data['tanggal'] = $post['pembayaran_tgl'];
            $data['created_at'] = $post['pembayaran_tgl'] . ' ' . $jam;
        }

        // Update status pembayaran di tbl_pemesanan
        $this->db->where('nospk', $nospk);
        $this->db->update('tbl_pemesanan', $data);
    }

    private function generateQrCode($nospk)
    {
        $config = [
            'cacheable' => true,
            'cachedir' => './assets/',
            'errorlog' => './assets/',
            'imagedir' => './assets/qrcode/',
            'quality' => true,
            'size' => '1024',
            'black' => [224, 255, 255],
            'white' => [70, 130, 180],
        ];
        $this->ciqrcode->initialize($config);

        $image_name = $nospk . '.png';
        $params = [
            'data' => "https://erp.tgmprint.com/cekpesanan/spk/" . urlencode(encrypt_spk($nospk)),
            'level' => 'H',
            'size' => 10,
            'savename' => FCPATH . $config['imagedir'] . $image_name,
        ];
        $this->ciqrcode->generate($params);
    }

    // Method untuk mengambil data pembayaran berdasarkan tanggal
    public function get_pembayaran_by_date($date)
    {
        try {
            $this->db->select('p.*, k.konsumen_nama, k.konsumen_nohp, u.nama_lengkap as kasir_name');
            $this->db->from('tbl_pembayaran p');
            $this->db->join('tbl_pemesanan pm', 'pm.nospk = p.nospk');
            $this->db->join('tbl_konsumen k', 'k.konsumen_id = pm.konsumen_id');
            $this->db->join('users u', 'u.user_id = p.kasir_id');

            $this->db->where('DATE(p.tgl_bayar)', $date);
            $this->db->order_by('p.tgl_bayar', 'ASC');

            $result = $this->db->get()->result();
            return $result ?: array();

        } catch (Exception $e) {
            log_message('error', 'Error in get_pembayaran_by_date: ' . $e->getMessage());
            return array();
        }
    }

    // Method untuk mengambil data pembayaran berdasarkan tanggal dan perusahaan
    public function get_pembayaran_by_date_perusahaan($date, $perusahaan_id)
    {
        try {
            $this->db->select('tbl_pembayaran.*, tbl_konsumen.konsumen_nama, tbl_konsumen.konsumen_nohp');
            $this->db->from('tbl_pembayaran');
            $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_pembayaran.konsumen_id');

            // Cek apakah tabel rekening ada dan join jika ada
            if ($this->db->table_exists('tbl_rekening')) {
                $this->db->select('tbl_rekening.nama_bank, tbl_rekening.no_rekening, tbl_rekening.atas_nama');
                $this->db->join('tbl_rekening', 'tbl_rekening.id = tbl_pembayaran.rekening_id', 'left');
            }

            $this->db->where('DATE(tbl_pembayaran.pembayaran_tgl)', $date);
            $this->db->where('tbl_pembayaran.perusahaan_id', $perusahaan_id);
            $this->db->order_by('tbl_pembayaran.pembayaran_tgl', 'ASC');

            $result = $this->db->get()->result();
            return $result ?: array();

        } catch (Exception $e) {
            log_message('error', 'Error in get_pembayaran_by_date_perusahaan: ' . $e->getMessage());
            return array();
        }
    }

    // Method untuk mengambil data pembayaran berdasarkan bulan
    public function get_pembayaran_by_month($year_month)
    {
        try {
            $this->db->select('tbl_pembayaran.*, tbl_konsumen.konsumen_nama, tbl_konsumen.konsumen_nohp');
            $this->db->from('tbl_pembayaran');
            $this->db->join('tbl_konsumen', 'tbl_konsumen.konsumen_id = tbl_pembayaran.konsumen_id');

            // Cek apakah tabel rekening ada dan join jika ada
            if ($this->db->table_exists('tbl_rekening')) {
                $this->db->select('tbl_rekening.nama_bank, tbl_rekening.no_rekening, tbl_rekening.atas_nama');
                $this->db->join('tbl_rekening', 'tbl_rekening.id = tbl_pembayaran.rekening_id', 'left');
            }

            $this->db->where('DATE_FORMAT(tbl_pembayaran.pembayaran_tgl, "%Y-%m") =', $year_month);
            $this->db->order_by('tbl_pembayaran.pembayaran_tgl', 'ASC');

            $result = $this->db->get()->result();
            return $result ?: array();

        } catch (Exception $e) {
            log_message('error', 'Error in get_pembayaran_by_month: ' . $e->getMessage());
            return array();
        }
    }

    // Method untuk menghitung total pembayaran per hari dalam bulan tertentu
    public function get_daily_totals_by_month($year_month)
    {
        try {
            $this->db->select('DATE(tgl_bayar) as tanggal, SUM(total_tagihan) as total_harian, COUNT(*) as jumlah_transaksi');
            $this->db->from('tbl_pembayaran');
            $this->db->where('DATE_FORMAT(tgl_bayar, "%Y-%m") =', $year_month);
            $this->db->group_by('DATE(tgl_bayar)');
            $this->db->order_by('tanggal', 'ASC');

            $result = $this->db->get()->result();
            return $result ?: array();

        } catch (Exception $e) {
            log_message('error', 'Error in get_daily_totals_by_month: ' . $e->getMessage());
            return array();
        }
    }

    // Method untuk mengambil detail pembayaran berdasarkan pembayaran_id
    public function get_pembayaran_detail($pembayaran_id)
    {
        try {
            $this->db->select('pd.*, r.nama_bank, r.no_rekening, r.atas_nama, e.ewallet_nama');
            $this->db->from('tbl_pembayaran_detail pd');
            $this->db->join('tbl_rekening r', 'r.id = pd.bank_id', 'left');
            $this->db->join('tbl_ewallet e', 'e.id = pd.ewallet_id', 'left');
            $this->db->where('pd.pembayaran_id', $pembayaran_id);
            $this->db->order_by('pd.detail_id', 'ASC');

            $result = $this->db->get()->result();
            return $result ?: array();

        } catch (Exception $e) {
            log_message('error', 'Error in get_pembayaran_detail: ' . $e->getMessage());
            return array();
        }
    }

    // Method untuk mengambil data pembayaran berdasarkan nospk
    public function get_pembayaran_by_nospk($nospk)
    {
        try {
            $this->db->select('p.*, k.konsumen_nama, k.konsumen_nohp, u.username as kasir_name, dp.username as deskprint_name');
            $this->db->from('tbl_pembayaran p');
            $this->db->join('tbl_pemesanan pm', 'pm.nospk = p.nospk');
            $this->db->join('tbl_konsumen k', 'k.konsumen_id = pm.konsumen_id');
            $this->db->join('users u', 'u.user_id = p.kasir_id');
            $this->db->join('users dp', 'dp.user_id = pm.deskprint_id', 'left');

            $this->db->where('p.nospk', $nospk);

            $result = $this->db->get()->row();
            return $result;

        } catch (Exception $e) {
            log_message('error', 'Error in get_pembayaran_by_nospk: ' . $e->getMessage());
            return null;
        }
    }

    // Method untuk mengambil detail produk dari tbl_pembayaran_produk
    public function get_pembayaran_produk($nospk)
    {
        try {
            $has_snapshot_harga = $this->db->field_exists('snapshot_harga', 'tbl_pembayaran_produk');
            $has_snapshot_detail = $this->db->field_exists('snapshot_detail_product', 'tbl_pembayaran_produk');
            $has_snapshot_qty_min = $this->db->field_exists('snapshot_qty_min', 'tbl_pembayaran_produk');
            $has_snapshot_qty_max = $this->db->field_exists('snapshot_qty_max', 'tbl_pembayaran_produk');
            $has_snapshot_ket_1 = $this->db->field_exists('snapshot_ket_1', 'tbl_pembayaran_produk');
            $has_snapshot_side_id = $this->db->field_exists('snapshot_side_id', 'tbl_pembayaran_produk');
            $has_snapshot_jenisprint = $this->db->field_exists('snapshot_jenisprint_id', 'tbl_pembayaran_produk');
            $has_snapshot_subtotal = $this->db->field_exists('snapshot_subtotal', 'tbl_pembayaran_produk');

            $select_snapshot_detail = $has_snapshot_detail ? 'COALESCE(pp.snapshot_detail_product, hp.detail_product)' : 'hp.detail_product';
            $select_snapshot_harga = $has_snapshot_harga ? 'COALESCE(pp.snapshot_harga, hp.harga_1)' : 'hp.harga_1';
            $select_snapshot_side = $has_snapshot_side_id ? 'COALESCE(pp.snapshot_side_id, hp.side_id)' : 'hp.side_id';
            $select_snapshot_ket = $has_snapshot_ket_1 ? 'COALESCE(pp.snapshot_ket_1, hp.ket_1)' : 'hp.ket_1';
            $select_snapshot_jenis = $has_snapshot_jenisprint ? 'COALESCE(pp.snapshot_jenisprint_id, hp.jenisprint_id)' : 'hp.jenisprint_id';
            $select_snapshot_qty_min = $has_snapshot_qty_min ? 'COALESCE(pp.snapshot_qty_min, hp.qty_min)' : 'hp.qty_min';
            $select_snapshot_qty_max = $has_snapshot_qty_max ? 'COALESCE(pp.snapshot_qty_max, hp.qty_max)' : 'hp.qty_max';
            $select_snapshot_subtotal = $has_snapshot_subtotal ? 'pp.snapshot_subtotal' : 'NULL AS snapshot_subtotal';

            $this->db->select("
                pp.*,
                p.kategori_id,
                p.satuan_id,
                k.kategori_nama,
                {$select_snapshot_detail} AS detail_product,
                {$select_snapshot_harga} AS harga_1,
                hp.harga_aktif,
                {$select_snapshot_side} AS side_id,
                {$select_snapshot_side} AS real_side_id,
                {$select_snapshot_ket} AS ket_1,
                {$select_snapshot_jenis} AS jenisprint_id,
                {$select_snapshot_qty_min} AS qty_min,
                {$select_snapshot_qty_max} AS qty_max,
                {$select_snapshot_subtotal},
                s.satuan_nama,
                sd.side_name,
                sd.side_name AS real_side_name,
                p.form_schema as product_form_schema,
                k.form_schema as category_form_schema,
                sk.form_schema as subcategory_form_schema
            ", FALSE);
            $this->db->from('tbl_pembayaran_produk pp');
            $this->db->join('tbl_product p', 'p.product_id = pp.product_id');
            $this->db->join('tbl_kategori k', 'k.kategori_id = p.kategori_id', 'left');
            $this->db->join('tbl_subkategori sk', 'sk.subkategori_id = p.subkategori_id', 'left');
            $this->db->join('tbl_harga_product hp', 'hp.harga_id = pp.harga_id');
            $this->db->join('tbl_satuan s', 's.satuan_id = p.satuan_id', 'left');
            $join_side = $has_snapshot_side_id ? 'sd.side_id = COALESCE(pp.snapshot_side_id, hp.side_id)' : 'sd.side_id = hp.side_id';
            $this->db->join('tbl_side sd', $join_side, 'left', false);
            $this->db->where('pp.nospk', $nospk);
            $this->db->order_by('pp.id', 'ASC');

            $result = $this->db->get()->result();
            return $result ?: array();

        } catch (Exception $e) {
            log_message('error', 'Error in get_pembayaran_produk: ' . $e->getMessage());
            return array();
        }
    }

    // Method untuk menghitung total pembayaran per hari dalam bulan tertentu berdasarkan perusahaan
    public function get_daily_totals_by_month_perusahaan($year_month, $perusahaan_id)
    {
        try {
            $this->db->select('DATE(pembayaran_tgl) as tanggal, SUM(grand_total) as total_harian, COUNT(*) as jumlah_transaksi');
            $this->db->from('tbl_pembayaran');
            $this->db->where('DATE_FORMAT(pembayaran_tgl, "%Y-%m") =', $year_month);
            if (is_array($perusahaan_id)) {
                $this->db->where_in('perusahaan_id', $perusahaan_id);
            } else {
                $this->db->where('perusahaan_id', $perusahaan_id);
            }
            $this->db->group_by('DATE(pembayaran_tgl)');
            $this->db->order_by('tanggal', 'ASC');

            $result = $this->db->get()->result();
            return $result ?: array();

        } catch (Exception $e) {
            log_message('error', 'Error in get_daily_totals_by_month_perusahaan: ' . $e->getMessage());
            return array();
        }
    }

    /**
     * Handle piutang when payment is incomplete
     */
    private function handlePiutang($post, $perusahaan, $userid)
    {
        $total_tagihan = rupiahToInt($post['grandtotal']);
        $total_bayar = $this->calculateTotalBayar($post);

        // If payment is complete, no need to handle piutang
        if ($total_bayar >= $total_tagihan) {
            return;
        }

        $sisa_piutang = $total_tagihan - $total_bayar;

        // Check if piutang already exists for this nospk
        $existing_piutang = $this->getPiutangByNospk($post['nospk']);

        if ($existing_piutang) {
            // Update existing piutang
            $this->updatePiutang($existing_piutang->piutang_id, $total_bayar, $sisa_piutang);
        } else {
            // Create new piutang
            $this->createPiutang($post, $perusahaan, $total_tagihan, $total_bayar, $sisa_piutang);
        }
    }

    /**
     * Calculate total payment amount
     */
    private function calculateTotalBayar($post)
    {
        $total_bayar = 0;
        $total_bayar += rupiahToInt($post['bayar_tunai'] ?? 0);
        $total_bayar += rupiahToInt($post['bayar_transfer'] ?? 0);
        $total_bayar += rupiahToInt($post['bayar_debit'] ?? 0);
        $total_bayar += rupiahToInt($post['bayar_ewallet'] ?? 0);
        $total_bayar += rupiahToInt($post['bayar_dp'] ?? 0);

        return $total_bayar;
    }

    /**
     * Get piutang by nospk
     */
    private function getPiutangByNospk($nospk)
    {
        return $this->db->get_where('tbl_piutang', ['nospk' => $nospk])->row();
    }

    /**
     * Create new piutang record
     */
    private function createPiutang($post, $perusahaan, $total_tagihan, $total_bayar, $sisa_piutang)
    {
        // Get konsumen_id from pemesanan
        $pemesanan = $this->db->get_where('tbl_pemesanan', ['nospk' => $post['nospk']])->row();

        if (!$pemesanan) {
            log_message('error', 'Pemesanan not found for nospk: ' . $post['nospk']);
            return false;
        }

        // Calculate jatuh tempo (default 30 days from today)
        $jatuh_tempo = date('Y-m-d', strtotime('+30 days'));

        // Determine status based on payment amount
        $status = 'BELUM LUNAS';
        if ($total_bayar > 0) {
            $status = 'SEBAGIAN';
        }

        $data = [
            'nospk' => $post['nospk'],
            'konsumen_id' => $pemesanan->konsumen_id,
            'perusahaan_id' => $perusahaan,
            'total_tagihan' => $total_tagihan,
            'total_bayar' => $total_bayar,
            'jatuh_tempo' => $jatuh_tempo,
            'status' => $status
        ];

        return $this->db->insert('tbl_piutang', $data);
    }

    /**
     * Update existing piutang record
     */
    private function updatePiutang($piutang_id, $total_bayar, $sisa_piutang)
    {
        // Determine status based on remaining amount
        $status = ($sisa_piutang <= 0) ? 'LUNAS' : 'SEBAGIAN';

        $data = [
            'total_bayar' => $total_bayar,
            'status' => $status
        ];

        return $this->db->update('tbl_piutang', $data, ['piutang_id' => $piutang_id]);
    }

    /**
     * Get piutang list for reporting
     */
    public function getPiutangList($perusahaan_id, $status = null, $limit = null)
    {
        $this->db->select('p.*, k.konsumen_nama, k.konsumen_nohp, k.konsumen_id, pm.created_at');
        $this->db->from('tbl_piutang p');
        $this->db->join('tbl_konsumen k', 'k.konsumen_id = p.konsumen_id');
        $this->db->join('tbl_pemesanan pm', 'pm.nospk = p.nospk');
        if (is_array($perusahaan_id)) {
            $this->db->where_in('p.perusahaan_id', $perusahaan_id);
        } else {
            $this->db->where('p.perusahaan_id', $perusahaan_id);
        }

        if ($status) {
            $this->db->where('p.status', $status);
        }

        // Group by konsumen first, then by jatuh tempo
        $this->db->order_by('k.konsumen_nama', 'ASC');
        $this->db->order_by('p.jatuh_tempo', 'ASC');

        if ($limit) {
            $this->db->limit($limit);
        }

        return $this->db->get()->result();
    }

    /**
     * Get piutang summary for dashboard
     */
    public function getPiutangSummary($perusahaan_id)
    {
        $this->db->select('
                COUNT(*) as total_piutang,
                SUM(sisa_piutang) as total_sisa_piutang,
                COUNT(CASE WHEN status = "BELUM LUNAS" THEN 1 END) as belum_lunas,
                COUNT(CASE WHEN status = "SEBAGIAN" THEN 1 END) as sebagian,
                COUNT(CASE WHEN status = "LUNAS" THEN 1 END) as lunas,
                COUNT(CASE WHEN jatuh_tempo < CURDATE() AND status != "LUNAS" THEN 1 END) as jatuh_tempo
            ');
        $this->db->from('tbl_piutang');
        if (is_array($perusahaan_id)) {
            $this->db->where_in('perusahaan_id', $perusahaan_id);
        } else {
            $this->db->where('perusahaan_id', $perusahaan_id);
        }

        return $this->db->get()->row();
    }

    /**
     * Update piutang when additional payment is made
     */
    public function updatePiutangPayment($nospk, $additional_payment)
    {
        $piutang = $this->getPiutangByNospk($nospk);

        if (!$piutang) {
            return false;
        }

        $new_total_bayar = $piutang->total_bayar + $additional_payment;
        $sisa_piutang = $piutang->total_tagihan - $new_total_bayar;
        $status = ($sisa_piutang <= 0) ? 'LUNAS' : 'SEBAGIAN';

        $data = [
            'total_bayar' => $new_total_bayar,
            'status' => $status
        ];

        return $this->db->update('tbl_piutang', $data, ['piutang_id' => $piutang->piutang_id]);
    }

    /**
     * Update piutang with payment method and history
     */
    public function updatePiutangPaymentWithMethod($nospk, $additional_payment, $metode_bayar, $keterangan = null, $rekening_id = null, $ewallet_id = null, $edc_id = null)
    {
        $this->db->trans_start();

        try {
            $piutang = $this->getPiutangByNospk($nospk);

            if (!$piutang) {
                $this->db->trans_rollback();
                return false;
            }

            // Update piutang record
            $new_total_bayar = $piutang->total_bayar + $additional_payment;
            $sisa_piutang = $piutang->total_tagihan - $new_total_bayar;
            $status = ($sisa_piutang <= 0) ? 'LUNAS' : 'SEBAGIAN';

            $piutang_data = [
                'total_bayar' => $new_total_bayar,
                'status' => $status
            ];

            $this->db->update('tbl_piutang', $piutang_data, ['piutang_id' => $piutang->piutang_id]);

            // Insert payment history
            $payment_history_data = [
                'piutang_id' => $piutang->piutang_id,
                'tanggal_bayar' => date('Y-m-d H:i:s'),
                'jumlah_bayar' => $additional_payment,
                'metode_bayar' => $metode_bayar,
                'keterangan' => $keterangan
            ];

            // Add rekening_id if transfer payment
            if ($metode_bayar == 'transfer' && $rekening_id) {
                $payment_history_data['rekening_id'] = $rekening_id;
            }

            // Add ewallet_id if e-wallet payment
            if ($metode_bayar == 'ewallet' && $ewallet_id) {
                $payment_history_data['ewallet_id'] = $ewallet_id;
            }

            // Add edc_id if debit payment
            if ($metode_bayar == 'debit' && $edc_id) {
                $payment_history_data['edc_id'] = $edc_id;
            }

            $this->db->insert('tbl_piutang_pembayaran', $payment_history_data);

            // SYNC to tbl_pembayaran_detail for Mutasi Reports
            $pembayaran = $this->db->get_where('tbl_pembayaran', ['nospk' => $nospk])->row();
            if ($pembayaran) {
                $db_method = $metode_bayar;
                if ($db_method == 'cash')
                    $db_method = 'tunai';
                if ($db_method == 'debit')
                    $db_method = 'debit'; // just to be sure
                if ($db_method == 'qris')
                    $db_method = 'ewallet';

                $this->db->insert('tbl_pembayaran_detail', [
                    'pembayaran_id' => $pembayaran->pembayaran_id,
                    'metode' => $db_method,
                    'jumlah' => $additional_payment,
                    'bank_id' => $rekening_id,
                    'ewallet_id' => $ewallet_id,
                    'edc_id' => $edc_id,
                    'keterangan' => 'Pelunasan Piutang: ' . $keterangan,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return false;
            }

            return true;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error in updatePiutangPaymentWithMethod: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all unpaid piutang for a specific consumer
     */
    public function getUnpaidPiutangByKonsumen($konsumen_id)
    {
        $this->db->select('p.*, pm.created_at as tgl_spk');
        $this->db->from('tbl_piutang p');
        $this->db->join('tbl_pemesanan pm', 'pm.nospk = p.nospk');
        $this->db->where('p.konsumen_id', $konsumen_id);
        $this->db->where_in('p.status', ['BELUM LUNAS', 'SEBAGIAN']);
        $this->db->order_by('pm.created_at', 'ASC'); // Pay oldest first
        return $this->db->get()->result();
    }

    /**
     * Process collective payment for a consumer
     */
    public function processCollectivePayment($konsumen_id, $total_amount, $metode, $keterangan, $rekening_id = null, $ewallet_id = null, $edc_id = null)
    {
        $this->db->trans_start();
        try {
            $unpaid = $this->getUnpaidPiutangByKonsumen($konsumen_id);
            $remaining_payment = $total_amount;

            foreach ($unpaid as $p) {
                if ($remaining_payment <= 0)
                    break;

                $current_sisa = $p->total_tagihan - $p->total_bayar;
                $payment_for_this = min($remaining_payment, $current_sisa);

                $new_total_bayar = $p->total_bayar + $payment_for_this;
                $new_sisa = $p->total_tagihan - $new_total_bayar;
                $new_status = ($new_sisa <= 0) ? 'LUNAS' : 'SEBAGIAN';

                // Update Piutang
                $this->db->update('tbl_piutang', [
                    'total_bayar' => $new_total_bayar,
                    'status' => $new_status
                ], ['piutang_id' => $p->piutang_id]);

                // Insert History
                $history = [
                    'piutang_id' => $p->piutang_id,
                    'tanggal_bayar' => date('Y-m-d H:i:s'),
                    'jumlah_bayar' => $payment_for_this,
                    'metode_bayar' => $metode,
                    'keterangan' => $keterangan . " (Pembayaran Kolektif)",
                    'rekening_id' => $rekening_id,
                    'ewallet_id' => $ewallet_id,
                    'edc_id' => $edc_id
                ];
                $this->db->insert('tbl_piutang_pembayaran', $history);

                // SYNC to tbl_pembayaran_detail for Mutasi Reports
                $pembayaran = $this->db->get_where('tbl_pembayaran', ['nospk' => $p->nospk])->row();
                if ($pembayaran) {
                    $db_method = $metode;
                    if ($db_method == 'cash')
                        $db_method = 'tunai';
                    if ($db_method == 'debit')
                        $db_method = 'debit';
                    if ($db_method == 'qris')
                        $db_method = 'ewallet';

                    $this->db->insert('tbl_pembayaran_detail', [
                        'pembayaran_id' => $pembayaran->pembayaran_id,
                        'metode' => $db_method,
                        'jumlah' => $payment_for_this,
                        'bank_id' => $rekening_id,
                        'ewallet_id' => $ewallet_id,
                        'edc_id' => $edc_id,
                        'keterangan' => 'Pelunasan Piutang (Kolektif): ' . $keterangan,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }

                $remaining_payment -= $payment_for_this;
            }

            $this->db->trans_complete();
            return $this->db->trans_status();
        } catch (Exception $e) {
            $this->db->trans_rollback();
            return false;
        }
    }
    /**
     * Get payment history for piutang
     */
    public function getPiutangPaymentHistory($nospk)
    {
        try {
            $this->db->select('pp.*, p.nospk, r.nama_bank, r.no_rekening, r.atas_nama, e.ewallet_nama, e.wallet_ket, me.edc_nama, me.edc_ket');
            $this->db->from('tbl_piutang_pembayaran pp');
            $this->db->join('tbl_piutang p', 'p.piutang_id = pp.piutang_id');
            $this->db->join('tbl_rekening r', 'r.id = pp.rekening_id', 'left');
            $this->db->join('tbl_ewallet e', 'e.id = pp.ewallet_id', 'left');
            $this->db->join('tbl_mesin_edc me', 'me.edc_id = pp.edc_id', 'left');
            $this->db->where('p.nospk', $nospk);
            $this->db->order_by('pp.tanggal_bayar', 'DESC');

            $result = $this->db->get()->result();

            // Format tanggal
            foreach ($result as $row) {
                $row->tanggal_bayar = date('d/m/Y H:i', strtotime($row->tanggal_bayar));
            }

            return $result;

        } catch (Exception $e) {
            log_message('error', 'Error in getPiutangPaymentHistory: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get datatables data for mutasi rekening
     */
    public function get_datatables_mutasi($request, $rekening_id)
    {
        $columns = [
            0 => 'p.tgl_bayar',
            1 => 'p.nospk',
            2 => 'pd.keterangan',
            3 => 'k.konsumen_nama',
            4 => 'pd.jumlah'
        ];

        $search = $request['search']['value'] ?? '';
        $limit = $request['length'] ?? 10;
        $start = $request['start'] ?? 0;
        $filter_month = $request['filter_month'] ?? '';

        $this->db->select('pd.*, p.nospk, p.tgl_bayar, p.jam_bayar, pd.referensi, k.konsumen_nama, u.username as kasir');
        $this->db->from('tbl_pembayaran_detail pd');
        $this->db->join('tbl_pembayaran p', 'pd.pembayaran_id = p.pembayaran_id');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->join('users u', 'p.kasir_id = u.user_id', 'left');
        $this->db->where('pd.bank_id', $rekening_id);

        if (!empty($filter_month)) {
            $this->db->where("DATE_FORMAT(p.tgl_bayar, '%Y-%m') = ", $filter_month);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('p.nospk', $search);
            $this->db->or_like('k.konsumen_nama', $search);
            $this->db->or_like('pd.keterangan', $search);
            $this->db->or_like('pd.referensi', $search);
            $this->db->group_end();
        }

        // Count filtered records
        $totalFiltered = $this->db->count_all_results('', false);

        if (!empty($request['order'])) {
            $order_col_idx = $request['order']['0']['column'];
            $order_dir = $request['order']['0']['dir'];
            $order_column = $columns[$order_col_idx] ?? 'p.tgl_bayar';
            $this->db->order_by($order_column, $order_dir);
            if ($order_column === 'p.tgl_bayar') {
                $this->db->order_by('p.jam_bayar', $order_dir);
            }
        } else {
            $this->db->order_by('p.tgl_bayar', 'DESC');
            $this->db->order_by('p.jam_bayar', 'DESC');
        }

        $this->db->limit($limit, $start);
        $query = $this->db->get();

        $data = [];
        foreach ($query->result() as $row) {
            $data[] = [
                'tgl_bayar' => $row->tgl_bayar,
                'jam_bayar' => $row->jam_bayar,
                'nospk' => $row->nospk,
                'referensi' => $row->referensi,
                'keterangan' => $row->keterangan,
                'kasir' => strtoupper($row->kasir),
                'konsumen_nama' => strtoupper($row->konsumen_nama),
                'jumlah' => $row->jumlah
            ];
        }

        $this->db->where('bank_id', $rekening_id);
        $totalData = $this->db->count_all_results('tbl_pembayaran_detail');

        return [
            "draw" => isset($request['draw']) ? intval($request['draw']) : 0,
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        ];
    }
}
