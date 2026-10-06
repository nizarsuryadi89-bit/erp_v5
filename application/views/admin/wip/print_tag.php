<?php
$tag_font_family = !empty($produksi->tag_font_family) ? $produksi->tag_font_family : (!empty($produksi->invoice_font_family) ? $produksi->invoice_font_family : 'Courier New');
$tag_font_size = !empty($produksi->tag_font_size) ? $produksi->tag_font_size : (!empty($produksi->invoice_font_size) ? $produksi->invoice_font_size : 11);
$tag_paper = !empty($produksi->tag_paper) ? $produksi->tag_paper : '80mm';
$tag_show_logo = isset($produksi->tag_show_logo) ? $produksi->tag_show_logo : 1;
$tag_logo_width = !empty($produksi->tag_logo_width) ? $produksi->tag_logo_width : 50;
$tag_show_qr = isset($produksi->tag_show_qr) ? $produksi->tag_show_qr : 1;
$tag_qr_width = !empty($produksi->tag_qr_width) ? $produksi->tag_qr_width : 50;
$tag_footer_note = !empty($produksi->tag_footer_note) ? $produksi->tag_footer_note : 'Tempelkan Tag ini pada barang yang sudah jadi';
$tag_section_spacing = !empty($produksi->tag_section_spacing) ? $produksi->tag_section_spacing : 8;

$font_css = "'Courier New', Courier, monospace";
if ($tag_font_family === 'Roboto') {
    $font_css = "'Roboto', sans-serif";
} elseif ($tag_font_family === 'Arial') {
    $font_css = "Arial, sans-serif";
} elseif ($tag_font_family === 'Inter') {
    $font_css = "'Inter', sans-serif";
} elseif ($tag_font_family === 'monospace') {
    $font_css = "monospace";
}

$cssPageSize = '80mm auto';
$containerMaxWidth = '300px';
if ($tag_paper === '58mm') {
    $cssPageSize = '58mm auto';
    $containerMaxWidth = '220px';
} elseif ($tag_paper === 'A6') {
    $cssPageSize = '105mm auto';
    $containerMaxWidth = '105mm';
}
$print_time = date('d/m/Y H:i');
?>
<!DOCTYPE html>
<html>

<head>
    <title>Print Tag - <?= isset($company_name) ? $company_name : 'TGM ERP' ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Roboto:wght@400;700&display=swap');

        * {
            box-sizing: border-box;
        }

        body {
            font-family:
                <?= $font_css ?>
            ;
            margin: 0;
            padding: 10px;
            background: #e0e0e0;
            color: #000;
            font-size:
                <?= $tag_font_size ?>
                px;
            line-height: 1.3;
        }

        .tag-container {
            width: 100%;
            max-width:
                <?= $containerMaxWidth ?>
            ;
            margin: 0 auto 15px auto;
            background: #fff;
            padding: 10px;
            position: relative;
            page-break-inside: avoid;
        }

        .tag-header {
            text-align: center;
            font-size: 1.1em;
            margin-bottom:
                <?= $tag_section_spacing ?>
                px;
        }

        .tag-logo-container {
            text-align: center;
            margin-bottom:
                <?= $tag_section_spacing ?>
                px;
        }

        .tag-logo-img {
            max-height: 70px;
            object-fit: contain;
            width:
                <?= $tag_logo_width ?>
                %;
        }

        .tag-title {
            font-size: 1em;
            margin-top: 5px;
            font-weight: bold;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 5px 0;
        }

        table.tag-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9em;
            border: none;
            margin-top:
                <?= $tag_section_spacing ?>
                px;
            margin-bottom:
                <?= $tag_section_spacing ?>
                px;
        }

        table.tag-table td {
            padding:
                <?= max(1, floor($tag_section_spacing / 4)) ?>
                px 0;
            border: none;
        }

        .tag-footer-section {
            text-align: center;
            font-size: 0.9em;
            margin-top:
                <?= $tag_section_spacing ?>
                px;
        }

        hr.tag-divider {
            border: none;
            border-top: 2px dashed #000;
            margin:
                <?= $tag_section_spacing ?>
                px 0;
        }

        hr.tag-divider-thin {
            border: none;
            border-top: 1px dashed #000;
            margin:
                <?= $tag_section_spacing ?>
                px 0;
        }

        .tag-qr-section {
            text-align: center;
            margin-top:
                <?= $tag_section_spacing ?>
                px;
        }

        .page-break {
            page-break-after: always;
            break-after: page;
            display: block;
            height: 0;
            border: none;
            margin: 0;
            padding: 0;
        }

        .no-print-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #27ae60;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            font-family: Arial, sans-serif;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white;
                padding: 0;
                margin: 0;
            }

            .tag-container {
                margin-bottom: 0;
                padding: 5px;
            }

            @page {
                size:
                    <?= $cssPageSize ?>
                ;
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <button class="no-print-btn no-print" onclick="window.print()">
        CETAK TAG SEKARANG
    </button>

    <?php
    // Group items by SPK
    $grouped_spk = [];
    foreach ($items as $item_detail) {
        $grouped_spk[$item_detail->nospk][] = $item_detail;
    }

    $total_spks = count($grouped_spk);
    $index = 0;

    foreach ($grouped_spk as $nospk => $spk_items):
        // Data Extraction from the first item
        $first_item = $spk_items[0];
        
        $nospk_parts = explode('-', $nospk);
        $spk_cabang_id = isset($nospk_parts[0]) ? (int)$nospk_parts[0] : 1;
        $store = 'TGM OFFICIAL (PUSAT)';
        if ($spk_cabang_id == 3) {
            $store = 'TGM MERCH (CABANG)';
        } elseif ($spk_cabang_id > 3) {
            $store = 'CABANG LAIN';
        }
        $store = strtoupper($store);
        $work_center = strtoupper($first_item->kategori_nama ?: 'LAIN-LAIN');
        $pelanggan = strtoupper($first_item->konsumen_nama);

        $channel = isset($first_item->nama_jenis_transaksi) ? strtoupper($first_item->nama_jenis_transaksi) : 'OFFLINE';
        if (isset($first_item->marketplace_nama) && !empty($first_item->marketplace_nama)) {
            $channel .= ' (' . strtoupper($first_item->marketplace_nama) . ')';
        }
        if (isset($first_item->resi_marketplace) && !empty($first_item->resi_marketplace)) {
            $channel .= '<br><small style="font-size: 0.8em; font-weight: normal;">RESI: ' . $first_item->resi_marketplace . '</small>';
        }
        $produksi_name = isset($first_item->nama_perusahaan) ? strtoupper($first_item->nama_perusahaan) : strtoupper(isset($company_name) ? $company_name : 'TGM PRINT (PUSAT)');

        // Lokasi Pesanan
        $lokasi_cetak = $first_item->lokasi_cetak ?? 0;
        $lokasi_nama = 'CABANG LAIN';
        if ($lokasi_cetak == 2) {
            $lokasi_nama = 'TGM OFFICIAL (PUSAT)';
        } elseif ($lokasi_cetak == 3) {
            $lokasi_nama = 'TGM MERCH (CABANG)';
        } else {
            $lokasi_nama = 'OFFLINE/CABANG';
        }


        $logo_url = base_url('assets/images/default-logo.png');
        if (isset($first_item->logo) && !empty($first_item->logo)) {
            $logo_url = base_url('assets/images/' . $first_item->logo);
        }
        ?>

        <div class="tag-container">
            <?php if ($tag_show_logo == 1): ?>
                <div class="tag-logo-container">
                    <img src="<?= $logo_url ?>" alt="Logo" class="tag-logo-img">
                </div>
            <?php endif; ?>

            <div class="tag-header">
                <div style="font-weight: bold; line-height: 1.2;"><?= $produksi_name ?></div>
                <div class="tag-title">TAG PRODUKSI</div>
                <div style="font-weight: bold; font-size: 0.95em; letter-spacing: 0.5px; margin-top: 5px;">SPK:
                    <?= $nospk ?>
                </div>
            </div>

            <table class="tag-table">
                <tr>
                    <td width="35%" style="font-weight: bold;">TGL</td>
                    <td width="5%" style="font-weight: bold;">:</td>
                    <td><?= isset($first_item->tgl_spk) ? date('d/m/Y', strtotime($first_item->tgl_spk)) : date('d/m/Y') ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">CUSTOMER</td>
                    <td style="font-weight: bold;">:</td>
                    <td><?= $pelanggan ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Work Center</td>
                    <td style="font-weight: bold;">:</td>
                    <td><?= $work_center ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">LOKASI PESAN</td>
                    <td style="font-weight: bold;">:</td>
                    <td><?= $store ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">LOKASI PRODUKSI</td>
                    <td style="font-weight: bold;">:</td>
                    <td><?= $lokasi_nama ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">SUMBER</td>
                    <td style="font-weight: bold;">:</td>
                    <td><?= $channel ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">DESKPRINT</td>
                    <td style="font-weight: bold;">:</td>
                    <td><?= isset($first_item->deskprint_nama) ? strtoupper($first_item->deskprint_nama) : '-' ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">OPERATOR</td>
                    <td style="font-weight: bold;">:</td>
                    <td><?= isset($first_item->nama_operator) ? strtoupper($first_item->nama_operator) : '-' ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">PEMBAYARAN</td>
                    <td style="font-weight: bold;">:</td>
                    <td>
                        <?php
                        $status_bayar = isset($first_item->status_pembayaran) ? $first_item->status_pembayaran : 'pending';
                        $sisa = isset($first_item->sisa_tagihan) ? (float) $first_item->sisa_tagihan : null;

                        // If it's explicitly 'paid' AND (we don't have payment data OR the remaining balance is <= 0)
                        if ($status_bayar == 'paid' && ($sisa === null || $sisa <= 0)) {
                            echo '<span style="font-weight: 900; border: 1px solid #000; padding: 2px 6px; border-radius: 4px; display: inline-block;">LUNAS</span>';
                        } else {
                            echo '<span style="font-weight: 900; border: 1px solid #000; padding: 2px 6px; border-radius: 4px; display: inline-block;">BELUM LUNAS</span>';
                        }
                        ?>
                    </td>
                </tr>
            </table>

            <hr class="tag-divider">

            <?php foreach ($spk_items as $item_detail):
                $produk = strtoupper($item_detail->product_nama);
                $qty = floatval($item_detail->qty);
                $specs = [];

                if ($item_detail->kategori_id != 1) {
                    $p = (float) ($item_detail->panjang ?? 0);
                    $l = (float) ($item_detail->lebar ?? 0);

                    if ($item_detail->kategori_id == 2) {
                        if ($p > 0 || $l > 0) {
                            $specs[] = $p . " x " . $l . " M";
                        }
                    } elseif (isset($item_detail->satuan_id) && $item_detail->satuan_id == 11) {
                        if ($p > 0 || $l > 0) {
                            $lebarStr = ($l > 0) ? "(L: " . $l . "cm)" : "";
                            $specs[] = $p . " M " . $lebarStr;
                        }
                    } elseif ($p > 0 && $l > 0) {
                        $specs[] = $p . " x " . $l . " cm";
                    } elseif ($p > 0 && $l <= 0) {
                        $specs[] = $p . " M";
                    } elseif ($p <= 0 && $l > 0) {
                        $specs[] = "Lebar: " . $l . " cm";
                    }
                }

                // [NEW] Kombinasi Metadata Cerdas (Sesuai Form Builder)
                $combined_meta = [];
                
                // 1. Ambil dari tbl_harga_product
                $harga_id = isset($item_detail->snapshot_source_harga_id) ? $item_detail->snapshot_source_harga_id : (isset($item_detail->harga_id) ? $item_detail->harga_id : 0);
                if ($harga_id > 0) {
                    $CI =& get_instance();
                    $harga_row = $CI->db->get_where('tbl_harga_product', ['harga_id' => $harga_id])->row();
                    if ($harga_row && !empty($harga_row->metadata)) {
                        $h_meta = json_decode($harga_row->metadata, true);
                        if (is_array($h_meta)) {
                            $combined_meta = array_merge($combined_meta, $h_meta);
                        }
                    }
                }

                // 2. Ambil dari tbl_produksi metadata
                $meta = !empty($item_detail->metadata) ? json_decode($item_detail->metadata, true) : [];
                if (is_array($meta)) {
                    $combined_meta = array_merge($combined_meta, $meta);
                }

                $ik1 = !empty($item_detail->db_ket_1) ? $item_detail->db_ket_1 : (!empty($item_detail->ket_1) ? $item_detail->ket_1 : null);
                if (!empty($ik1) && !isset($combined_meta['ket_1'])) {
                    $combined_meta['ket_1'] = $ik1;
                }
                
                $ijp = !empty($item_detail->db_jenisprint_id) ? $item_detail->db_jenisprint_id : (!empty($item_detail->jenisprint_id) ? $item_detail->jenisprint_id : null);
                if (!empty($ijp) && !isset($combined_meta['jenisprint_id'])) {
                    $combined_meta['jenisprint_id'] = $ijp;
                }
                
                if (!empty($item_detail->ket_2) && !isset($combined_meta['ket_2'])) {
                    $combined_meta['ket_2'] = $item_detail->ket_2;
                }
                if (!empty($item_detail->ket_3) && !isset($combined_meta['ket_3'])) {
                    $combined_meta['ket_3'] = $item_detail->ket_3;
                }
                
                if (isset($item_detail->kategori_id) && $item_detail->kategori_id == 1) {
                    if (empty($combined_meta['ket_1'])) {
                        if (!empty($ijp)) {
                            $combined_meta['ket_1'] = $ijp;
                        } elseif (!empty($combined_meta['jenisprint_id'])) {
                            $combined_meta['ket_1'] = $combined_meta['jenisprint_id'];
                        }
                    }
                }

                if (empty($combined_meta['sisi'])) {
                    $jp = !empty($ijp) ? $ijp : (!empty($combined_meta['jenisprint_id']) ? $combined_meta['jenisprint_id'] : null);
                    if ($jp == '1' || $jp == '2') {
                        $combined_meta['sisi'] = $jp;
                    }
                }

                // 3. Resolve Schema Map
                $schema_map = [];
                $schema_arr = [];
                
                // Merge schemas sequentially (Category -> Subcategory -> Product) so specific overrides general
                if (!empty($item_detail->kategori_schema)) {
                    $sc = json_decode($item_detail->kategori_schema, true);
                    if (is_array($sc)) $schema_arr = array_merge($schema_arr, $sc);
                }
                if (!empty($item_detail->subkategori_schema)) {
                    $sc = json_decode($item_detail->subkategori_schema, true);
                    if (is_array($sc)) $schema_arr = array_merge($schema_arr, $sc);
                }
                if (!empty($item_detail->product_schema)) {
                    $sc = json_decode($item_detail->product_schema, true);
                    if (is_array($sc)) $schema_arr = array_merge($schema_arr, $sc);
                }

                if (!empty($schema_arr)) {
                    foreach ($schema_arr as $field) {
                        if (isset($field['name'])) {
                            $schema_map[$field['name']] = $field;
                        }
                    }
                }

                // 4. Print values
                $printed_values = [];
                foreach ($combined_meta as $key => $val) {
                    $key_lower = strtolower($key);
                    $is_in_schema = isset($schema_map[$key]);
                    $label = ucwords(str_replace('_', ' ', $key));

                    if ($is_in_schema) {
                        $field = $schema_map[$key];
                        if (!empty($field['label'])) {
                            $label = $field['label'];
                        }
                        if (isset($field['options']) && is_array($field['options']) && isset($field['options'][$val])) {
                            $val = $field['options'][$val];
                        }
                    }

                    if (!$is_in_schema) {
                        if (in_array($key_lower, ['unit_price', 'subtotal', 'price_snapshot_at', 'hpp', 'range_id', 'user', 'kodeproduk', 'nospk']) || substr($key_lower, -3) === '_id' || strpos($key_lower, 'subkategori') !== false) {
                            continue;
                        }
                    }

                    if (in_array($item_detail->kategori_id, [5, 9]) && in_array($key_lower, ['ket_1', 'lebar'])) {
                        $widthCode = $val;
                        $wNameStr = strtoupper($val);
                        if ($is_in_schema && isset($schema_map[$key]['options'][$widthCode])) {
                            $wNameStr = strtoupper($schema_map[$key]['options'][$widthCode]);
                        }
                        $pStr = (float)($item_detail->panjang ?? 0) . " M";
                        $idx = array_search($pStr, $specs);
                        if ($idx !== false) {
                            $specs[$idx] = str_replace(',', ' x', $wNameStr) . " x " . $pStr;
                        } else {
                            $formatted_wNameStr = str_replace(',', ' x', $wNameStr);
                            array_unshift($specs, $formatted_wNameStr);
                        }
                        continue;
                    }

                    if ($key_lower == 'sisi') {
                        if (in_array($item_detail->kategori_id, [2, 9]) || strtolower($item_detail->kategori_nama ?? '') == 'large format') {
                            continue;
                        }
                        if ($val == '1' || $val == '2') {
                            $val = $val . ' Sisi';
                        }
                        $val_clean = strtolower(trim($val));
                        if (!in_array($val_clean, $printed_values)) {
                            $specs[] = strtoupper($val);
                            $printed_values[] = $val_clean;
                        }
                        continue;
                    }

                    if (strpos($key_lower, 'ket_') === 0 && !$is_in_schema) {
                        $val = trim($val, '[]');
                        if (!empty($val)) {
                            if (isset($item_detail->kategori_id) && $item_detail->kategori_id == 1 && ($val == '1' || $val == '2')) {
                                $val = $val . ' Sisi';
                            }
                            
                            // Jika setelah diformat nilainya masih murni angka (seperti ID mesin atau data kosong bernilai 1),
                            // kita sembunyikan agar tidak tampil angka rancu seperti '1' di Jasa Desain
                            if (is_numeric($val)) {
                                continue;
                            }

                            $val_clean = strtolower(trim($val));
                            if (!in_array($val_clean, $printed_values)) {
                                $specs[] = strtoupper($val);
                                $printed_values[] = $val_clean;
                            }
                        }
                        continue;
                    }

                    $val_clean = strtolower(trim($val));
                    if (!in_array($val_clean, $printed_values)) {
                        $specs[] = strtoupper($label) . ": " . strtoupper($val);
                        $printed_values[] = $val_clean;
                    }
                }

                $specs = array_unique($specs);
                $spec_text = implode(', ', $specs);
                if (!empty($item_detail->catatan)) {
                    $spec_text .= (!empty($spec_text) ? ' - ' : '') . htmlspecialchars($item_detail->catatan);
                }
                ?>
                <table class="tag-table" style="margin-top: 0; margin-bottom: 5px;">
                    <tr>
                        <td width="35%" style="font-weight: bold; vertical-align: top;">ITEM</td>
                        <td width="5%" style="font-weight: bold; vertical-align: top;">:</td>
                        <td style="font-weight: bold; font-size: 1.1em;"><?= $produk ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">QTY</td>
                        <td style="font-weight: bold;">:</td>
                        <?php 
                        $satuan_display = 'PCS';
                        if (!empty($item_detail->satuan_nama)) {
                            $satuan_temp = strtoupper(trim($item_detail->satuan_nama));
                            // Jika satuannya mengandung M atau METER, paksa jadi PCS untuk label
                            if ($satuan_temp === 'M' || strpos($satuan_temp, 'METER') !== false) {
                                $satuan_display = 'PCS';
                            } else {
                                $satuan_display = $satuan_temp;
                            }
                        }
                        
                        // Force PCS untuk Large Format
                        if (isset($item_detail->kategori_id) && $item_detail->kategori_id == 2) {
                            $satuan_display = 'PCS';
                        }
                        ?>
                        <td style="font-weight: bold;"><?= $qty ?> <?= $satuan_display ?></td>
                    </tr>
                    <?php if (!empty($spec_text)): ?>
                        <tr>
                            <td style="font-weight: bold; vertical-align: top; font-size: 0.85em;">KET</td>
                            <td style="font-weight: bold; vertical-align: top; font-size: 0.85em;">:</td>
                            <td style="font-size: 0.85em;"><?= $spec_text ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php
                    $item_dl = '-';
                    if (!empty($item_detail->dateline_tgl) && $item_detail->dateline_tgl !== '0000-00-00' && $item_detail->dateline_tgl !== '0000-00-00 00:00:00') {
                        $time = !empty($item_detail->dateline_jam) ? $item_detail->dateline_jam : '00:00';
                        $item_dl = date('d/m/Y H:i', strtotime($item_detail->dateline_tgl . ' ' . $time));
                    }
                    if ($item_dl !== '-'): ?>
                        <tr>
                            <td style="font-weight: bold; vertical-align: top; padding-top: 5px;">DL</td>
                            <td style="font-weight: bold; vertical-align: top; padding-top: 5px;">:</td>
                            <td style="font-weight: 900; font-size: 1.1em; padding-top: 5px;"><?= $item_dl ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
                <hr class="tag-divider-thin">
            <?php endforeach; ?>

            <?php if (!empty($tag_footer_note)): ?>
                <div class="tag-footer-section">
                    <hr class="tag-divider">
                    <div><?= htmlspecialchars($tag_footer_note) ?></div>
                </div>
            <?php endif; ?>

            <?php if ($tag_show_qr == 1): ?>
                <div class="tag-qr-section">
                    <hr class="tag-divider">
                    <?php
                    $nospk_safe = str_replace('/', '_', $nospk);
                    $qr_file_path = FCPATH . 'assets/qrcode/' . $nospk_safe . '.png';
                    if (!file_exists($qr_file_path)) {
                        $CI =& get_instance();
                        $CI->load->library('ciqrcode');
                        $config['cacheable'] = true;
                        $config['cachedir'] = './assets/';
                        $config['errorlog'] = './assets/';
                        $config['imagedir'] = './assets/qrcode/';
                        $config['quality'] = true;
                        $config['size'] = '1024';
                        $config['black'] = array(255, 255, 255);
                        $config['white'] = array(0, 0, 0);
                        $CI->ciqrcode->initialize($config);

                        $params['data'] = $nospk;
                        $params['level'] = 'H';
                        $params['size'] = 10;
                        $params['savename'] = $qr_file_path;
                        $CI->ciqrcode->generate($params);
                    }
                    ?>
                    <img src="<?= base_url('assets/qrcode/' . $nospk_safe . '.png') ?>" alt="QR Code SPK"
                        style="width: <?= $tag_qr_width ?>%; max-width: 150px; height: auto; object-fit: contain; margin: 0 auto;">
                </div>
            <?php endif; ?>
        </div>

        <?php if ($index < $total_spks - 1): ?>
            <div class="page-break"></div>
        <?php endif; ?>
        <?php $index++; ?>
    <?php endforeach; ?>
</body>

</html>