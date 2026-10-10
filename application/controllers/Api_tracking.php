<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_tracking extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");
        
        // Setup CORS for external portal access
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        
        if ($this->input->method() === 'options') {
            exit;
        }

        $this->load->helper('security');
    }

    private function _resolve_and_verify_spk($nospk)
    {
        if ($nospk == null) {
            return [null, null];
        }

        $nospk_plain = urldecode($nospk);
        if (function_exists('decrypt_spk')) {
            $decrypted = decrypt_spk($nospk_plain);
            if ($decrypted && preg_match('/^[A-Za-z0-9\-]{1,30}$/', $decrypted)) {
                $nospk = $decrypted;
            } else {
                $nospk = $nospk_plain;
            }
        } else {
            $nospk = $nospk_plain;
        }

        if (!preg_match('/^[A-Za-z0-9\-]{1,30}$/', $nospk)) {
            return [null, null];
        }

        $pembayaran_data = $this->M_transaksi->getcekpesanan($nospk)->row();

        if (!$pembayaran_data) {
            return [null, null];
        }

        return [$nospk, $pembayaran_data];
    }

    public function spk()
    {
        $nospk_param = $this->input->get('spk', true);
        
        if (empty($nospk_param)) {
            echo json_encode(['status' => 'error', 'message' => 'No SPK provided']);
            return;
        }

        list($nospk, $pembayaran_data) = $this->_resolve_and_verify_spk($nospk_param);

        if (!$pembayaran_data) {
            echo json_encode(['status' => 'error', 'message' => 'Pesanan tidak ditemukan']);
            return;
        }

        // Ambil data perusahaan
        $perusahaan_id = isset($pembayaran_data->perusahaan_id) ? $pembayaran_data->perusahaan_id : 1;
        $perusahaan = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row_array();

        $this->load->model('M_pembayaran');
        $is_paid = !empty($pembayaran_data->bayar_nospk);

        $items = [];
        if ($is_paid) {
            $items = $this->M_pembayaran->get_pembayaran_produk($nospk);
        }

        if (empty($items)) {
            $items = $this->M_transaksi->getDetailPesan($nospk)->result();
        }

        // Aggregate Production Statuses
        $cekflow_all = $this->M_transaksi->getcekflow($nospk)->result_array();
        $status_produksi_global = null;
        $production_statuses = [];
        $is_all_done = true;
        $is_any_proses = false;
        $is_all_taken = true;
        $is_any_taken = false;

        if (count($cekflow_all) > 0) {
            foreach ($cekflow_all as $cf) {
                $raw_stat = strtolower(trim($cf['status_produksi']));
                $stat = 0;
                if ($raw_stat === '4' || $raw_stat === 'on_costumer') $stat = 4;
                elseif ($raw_stat === '3' || $raw_stat === 'done' || $raw_stat === 'on_pb') $stat = 3;
                elseif ($raw_stat === '2' || $raw_stat === 'proses') $stat = 2;
                elseif ($raw_stat === '1' || $raw_stat === 'setting') $stat = 1;
                else $stat = (int)$raw_stat;

                $production_statuses[$cf['product_id']] = $stat;

                if ($stat < 3) $is_all_done = false;
                if ($stat == 2 || $stat == 1) $is_any_proses = true;
                if ($stat < 4) $is_all_taken = false;
                if ($stat == 4) $is_any_taken = true;
            }

            if ($is_all_taken) {
                $status_produksi_global = '4'; // on_costumer
            } elseif ($is_all_done && $is_any_taken) {
                // BEST PRACTICE FIX: If ALL items are at least Done (3), and AT LEAST ONE is Taken (4), 
                // we assume the entire order was taken. This handles non-production items left at 'done'.
                $status_produksi_global = '4';
                foreach ($production_statuses as $pid => $s) {
                    if ($s == 3) $production_statuses[$pid] = 4;
                }
            } elseif ($is_all_done) {
                $status_produksi_global = '3'; // done (siap diambil)
            } elseif ($is_any_proses || !$is_all_done) {
                $status_produksi_global = '2'; // proses
            }
        }

        $formatted_items = [];
        foreach ($items as $item) {
            if (empty($item->metadata) && !empty($item->product_metadata)) {
                $item->metadata = $item->product_metadata;
            } else if (!empty($item->metadata) && !empty($item->product_metadata)) {
                $meta_arr = json_decode($item->metadata, true);
                $prod_meta = json_decode($item->product_metadata, true);
                if (is_array($meta_arr) && is_array($prod_meta)) {
                    $item->metadata = json_encode(array_merge($prod_meta, $meta_arr));
                }
            }

            // Hitung harga dan subtotal menggunakan fallback sederhana jika fungsi invoice_detail_price belum ada
            $meta = json_decode($item->metadata, true) ?: [];
            $harga = $item->harga_1 ?? ($item->unit_price ?? 0);
            
            $harga_id = isset($item->snapshot_source_harga_id) ? $item->snapshot_source_harga_id : (isset($item->harga_id) ? $item->harga_id : 0);
            $harga_row = null;
            if ($harga_id > 0) {
                $harga_row = $this->db->get_where('tbl_harga_product', ['harga_id' => $harga_id])->row();
            }
            
            if (isset($item->snapshot_harga) && is_numeric($item->snapshot_harga) && $item->snapshot_harga > 0) {
                $harga = $item->snapshot_harga;
            } else if (isset($meta['unit_price'])) {
                $harga = $meta['unit_price'];
            } else if ($harga_row && is_numeric($harga_row->harga)) {
                $harga = $harga_row->harga;
            }

            // Hitung Subtotal persis seperti di invoice_detail_total
            $subtotal = 0;
            if (isset($item->snapshot_subtotal) && is_numeric($item->snapshot_subtotal) && $item->snapshot_subtotal > 0) {
                $subtotal = $item->snapshot_subtotal;
            } elseif (isset($meta['subtotal']) && is_numeric($meta['subtotal'])) {
                $subtotal = $meta['subtotal'];
            } elseif (isset($item->subtotal) && $item->subtotal !== null && $item->subtotal !== '' && is_numeric($item->subtotal) && $item->subtotal > 0) {
                $subtotal = $item->subtotal;
            } else {
                $qty = (float) ($item->qty ?? 0);
                $panjang = (float) ($item->panjang ?? 0);
                $lebar = (float) ($item->lebar ?? 0);
                $satuan_id = isset($item->satuan_id) ? (int) $item->satuan_id : 0;
                $kategori_id = isset($item->kategori_id) ? (int) $item->kategori_id : 0;
                $ket_1 = isset($item->ket_1) ? (string) $item->ket_1 : '';

                if ($satuan_id === 11 || ($kategori_id === 9 && $ket_1 === '5')) {
                    $subtotal = $panjang * $harga * $qty;
                } elseif ($panjang != 0 && $lebar != 0) {
                    $subtotal = $panjang * $lebar * $harga * $qty;
                } elseif ($panjang != 0 && $lebar == 0) {
                    $subtotal = $panjang * $harga * $qty;
                } else {
                    $subtotal = $harga * $qty;
                }
            }

            // Keterangan / Metadata dinamis
            $combined_meta = [];
            $meta_str = '';

            $harga_id = isset($item->snapshot_source_harga_id) ? $item->snapshot_source_harga_id : (isset($item->harga_id) ? $item->harga_id : 0);
            if ($harga_id > 0) {
                $harga_row = $this->db->get_where('tbl_harga_product', ['harga_id' => $harga_id])->row();
                if ($harga_row && !empty($harga_row->metadata)) {
                    $h_meta = json_decode($harga_row->metadata, true);
                    if (is_array($h_meta)) {
                        $combined_meta = array_merge($combined_meta, $h_meta);
                    }
                }
            }

            if (!empty($item->metadata)) {
                $t_meta = json_decode($item->metadata, true);
                if (is_array($t_meta)) {
                    $combined_meta = array_merge($combined_meta, $t_meta);
                }
            }

            if (!empty($item->ket_1) && !isset($combined_meta['ket_1'])) {
                $combined_meta['ket_1'] = $item->ket_1;
            }

            $schema_map = [];
            $raw_schema = $item->product_form_schema ?? $item->subcategory_form_schema ?? $item->category_form_schema ?? '';
            
            $schema_arr = [];
            if (!empty($raw_schema)) {
                $schema_arr = json_decode($raw_schema, true) ?: [];
            } else {
                // Fallback query if not joined
                $cat_id = $item->kategori_id ?? 0;
                $prod_id = $item->product_id ?? 0;
                if ($cat_id) {
                    $cat = $this->db->get_where('tbl_kategori', ['kategori_id' => $cat_id])->row();
                    if ($cat && !empty($cat->form_schema)) {
                        $sc = json_decode($cat->form_schema, true);
                        if (is_array($sc)) $schema_arr = array_merge($schema_arr, $sc);
                    }
                }
                if ($prod_id) {
                    $prod = $this->db->get_where('tbl_product', ['product_id' => $prod_id])->row();
                    if ($prod) {
                        if (!empty($prod->form_schema)) {
                            $sc = json_decode($prod->form_schema, true);
                            if (is_array($sc)) $schema_arr = array_merge($schema_arr, $sc);
                        }
                        if (!empty($prod->subkategori_id)) {
                            $sub = $this->db->get_where('tbl_subkategori', ['subkategori_id' => $prod->subkategori_id])->row();
                            if ($sub && !empty($sub->form_schema)) {
                                $sc = json_decode($sub->form_schema, true);
                                if (is_array($sc)) $schema_arr = array_merge($schema_arr, $sc);
                            }
                        }
                    }
                }
            }

            if (is_array($schema_arr)) {
                foreach ($schema_arr as $field) {
                    if (isset($field['name'])) {
                        $schema_map[$field['name']] = $field;
                    }
                }
            }

            $printed_values = [];
            
            // Add P x L to meta string if it's Large format or others
            $p = floatval($item->panjang ?? 0);
            $l = floatval($item->lebar ?? 0);
            $l_text = '';

            if ($l == 0 && !empty($item->metadata)) {
                $tmp_meta = json_decode($item->metadata, true);
                if (is_array($tmp_meta)) {
                    if (!empty($tmp_meta['lebar'])) $l_text = $tmp_meta['lebar'];
                    elseif (!empty($tmp_meta['ket_1']) && isset($item->kategori_id) && $item->kategori_id == 9) $l_text = $tmp_meta['ket_1'];
                }
            } else if ($l > 0) {
                $l_text = $l . (isset($item->satuan_nama) ? ' ' . $item->satuan_nama : '');
            }

            $variasi_list = [];

            if ($p > 0 && !empty($l_text)) {
                $printed_values[] = "{$p} x {$l_text}";
                $meta_str .= "{$p} x {$l_text} &bull; ";
                $variasi_list[] = ['label' => 'Ukuran', 'value' => "{$p} x {$l_text}"];
            } elseif ($p > 0) {
                $satuan = isset($item->satuan_nama) ? ' ' . $item->satuan_nama : '';
                $printed_values[] = "{$p}{$satuan}";
                $meta_str .= "{$p}{$satuan} &bull; ";
                $variasi_list[] = ['label' => 'Ukuran', 'value' => "{$p}{$satuan}"];
            }

            foreach ($combined_meta as $key => $val) {
                $key_lower = strtolower($key);
                $is_in_schema = isset($schema_map[$key]);

                $label = ucwords(str_replace('_', ' ', $key));

                if ($is_in_schema) {
                    $field = $schema_map[$key];
                    if (isset($field['label']) && !empty($field['label'])) {
                        $label = $field['label'];
                    }
                    if (isset($field['options']) && is_array($field['options']) && isset($field['options'][$val])) {
                        $val = $field['options'][$val];
                    }
                }

                if (!$is_in_schema) {
                    if (in_array($key_lower, ['unit_price', 'subtotal', 'price_snapshot_at', 'hpp', 'range_id', 'jenisprint', 'user', 'kodeproduk', 'nospk']) || substr($key_lower, -3) === '_id' || strpos($key_lower, 'subkategori') !== false) {
                        continue;
                    }
                }

                if (isset($item->kategori_id) && $item->kategori_id == 9 && in_array($key_lower, ['ket_1', 'lebar'])) continue;

                if ($key_lower == 'sisi') {
                    if (isset($item->kategori_id) && in_array($item->kategori_id, [2, 5, 9])) continue;
                    if ($val == '1' || $val == '2') $val = $val . ' Sisi';
                    elseif (strtolower(trim($val)) == '1 side') $val = '1 Sisi';
                    elseif (strtolower(trim($val)) == '2 side') $val = '2 Sisi';
                    elseif ($val == '3' || stripos($val, 'Tidak Ada') !== false) continue;
                }

                if (strpos($key_lower, 'ket_') === 0 && !$is_in_schema) {
                    $val = trim($val, '[]');
                    if (empty($val)) continue;
                }

                $val_clean = strtolower(trim($val));
                if (!in_array($val_clean, $printed_values)) {
                    $meta_str .= "$val &bull; ";
                    $printed_values[] = $val_clean;
                    $variasi_list[] = ['label' => $label, 'value' => $val];
                }
            }

            $keterangan = rtrim($meta_str, ' &bull; ');

            $formatted_items[] = [
                'nama' => $item->detail_product ?? $item->product_nama ?? 'Item',
                'qty' => $item->qty ?? 1,
                'harga' => $harga,
                'subtotal' => $subtotal,
                'kategori' => $item->kategori_nama ?? '',
                'keterangan' => $keterangan,
                'variasi' => $variasi_list,
                'status_produksi' => $production_statuses[$item->product_id] ?? null
            ];
        }

        $total_items = 0;
        foreach ($formatted_items as $f_item) {
            $total_items += (int)($f_item['subtotal'] ?? 0);
        }

        $total_tagihan = (!empty($pembayaran_data->total_tagihan) && (int)$pembayaran_data->total_tagihan > 0) ? (int)$pembayaran_data->total_tagihan : $total_items;
        $total_bayar = (int)($pembayaran_data->total_bayar ?? 0);
        $sisa = $total_tagihan - $total_bayar;
        if ($sisa < 0) $sisa = 0;

        $status_bayar = strtolower($pembayaran_data->status_bayar_trx ?? $pembayaran_data->status_pembayaran ?? 'unpaid');
        $is_lunas = ($status_bayar === 'paid' || ($total_tagihan > 0 && $sisa <= 0 && $total_bayar > 0));
        $has_payment = !empty($pembayaran_data->bayar_nospk) || $total_bayar > 0;

        $detail_bayar = $this->M_transaksi->getDetailPembayaran($nospk)->result();
        $cekflow = !empty($cekflow_all) ? $cekflow_all[0] : null;
        $rekening = $this->db->get('tbl_rekening')->result();

        // Check if payment record exists
        $has_pembayaran_record = !empty($pembayaran_data->bayar_nospk);

        // Check piutang data ONLY if recorded in tbl_piutang
        $piutang_data = $this->db->get_where('tbl_piutang', ['nospk' => $nospk])->row();
        $is_piutang = !empty($piutang_data);
        $jatuh_tempo = (!empty($piutang_data->jatuh_tempo) && $piutang_data->jatuh_tempo != '0000-00-00') ? date('d-m-Y', strtotime($piutang_data->jatuh_tempo)) : null;

        // Resolve Dateline
        $first_detail = $this->db->select('dateline_tgl, dateline_jam')
            ->where('nospk', $nospk)
            ->where('dateline_tgl !=', '0000-00-00')
            ->get('tbl_pemesanan_detail')->row();
        $dateline_formatted = '-';
        if ($first_detail && !empty($first_detail->dateline_tgl) && $first_detail->dateline_tgl != '0000-00-00') {
            $dateline_formatted = date('d-m-Y', strtotime($first_detail->dateline_tgl));
            if (!empty($first_detail->dateline_jam) && $first_detail->dateline_jam != '00:00:00') {
                $dateline_formatted .= ' ' . substr($first_detail->dateline_jam, 0, 5);
            }
        } elseif ($jatuh_tempo) {
            $dateline_formatted = 'Tempo: ' . $jatuh_tempo;
        }

        $timestamps = [
            'created' => (!empty($pembayaran_data->created_at)) ? date('d M Y, H:i', strtotime($pembayaran_data->created_at)) : null,
            'paid' => ($has_pembayaran_record && !empty($pembayaran_data->tgl_bayar) && !empty($pembayaran_data->jam_bayar)) ? date('d M Y, H:i', strtotime($pembayaran_data->tgl_bayar . ' ' . $pembayaran_data->jam_bayar)) : null,
            'produksi_mulai' => (!empty($cekflow['tgl_mulai'])) ? date('d M Y, H:i', strtotime($cekflow['tgl_mulai'])) : null,
            'produksi_selesai' => (!empty($cekflow['tgl_selesai'])) ? date('d M Y, H:i', strtotime($cekflow['tgl_selesai'])) : null,
            'diambil' => (!empty($cekflow['tgl_kirim_pb'])) ? date('d M Y, H:i', strtotime($cekflow['tgl_kirim_pb'])) : null
        ];

        $has_valid_payment = $has_pembayaran_record && ($total_bayar > 0 || $is_piutang);
        $status_pembayaran_val = $is_lunas ? 'paid' : ($is_piutang ? 'piutang' : (($has_pembayaran_record && $total_bayar > 0) ? 'dp' : 'unpaid'));

        echo json_encode([
            'status' => 'success',
            'data' => [
                'konsumen' => $pembayaran_data->konsumen_nama ?? '-',
                'nohp' => $pembayaran_data->konsumen_nohp ?? '-',
                'email' => $pembayaran_data->konsumen_email ?? '-',
                'alamat' => $pembayaran_data->konsumen_alamat ?? '-',
                'nospk' => $nospk,
                'tanggal' => (!empty($pembayaran_data->created_at)) ? date('d-m-Y H:i', strtotime($pembayaran_data->created_at)) : (date('d-m-Y', strtotime($pembayaran_data->tgl_bayar)) . ' ' . $pembayaran_data->jam_bayar),
                'kasir' => $pembayaran_data->kasir_name ?? '-',
                'deskprint' => $pembayaran_data->username ?? '-',
                'dateline' => $dateline_formatted,
                'is_paid' => $is_lunas,
                'is_lunas' => $is_lunas,
                'is_piutang' => $is_piutang,
                'jatuh_tempo' => $jatuh_tempo,
                'has_payment' => $has_valid_payment,
                'status_produksi_global' => $status_produksi_global,
                'total_tagihan' => $total_tagihan,
                'total_bayar' => $total_bayar,
                'diskon' => (int)($pembayaran_data->diskon_total ?? $pembayaran_data->diskon ?? 0),
                'ongkir' => (float)($pembayaran_data->ongkir ?? 0),
                'sisa_tagihan' => $sisa,
                'status_pembayaran' => $status_pembayaran_val,
                'items' => $formatted_items,
                'rekening' => $rekening,
                'flow' => $cekflow,
                'timestamps' => $timestamps,
                'perusahaan' => $perusahaan
            ]
        ]);
    }
}
