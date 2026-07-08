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
        $store = strtoupper($first_item->cabang_asal ?: 'TGM ERP');
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
                    <td style="font-weight: bold;">Lokasi</td>
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

                $meta = !empty($item_detail->metadata) ? json_decode($item_detail->metadata, true) : [];

                // Backward Compatibility for old items that didn't save schema correctly
                if (!isset($meta['ket_1']) && isset($item_detail->db_ket_1) && $item_detail->db_ket_1 !== '') {
                    $meta['ket_1'] = $item_detail->db_ket_1;
                }
                if (!isset($meta['jenisprint_id']) && isset($item_detail->db_jenisprint_id) && $item_detail->db_jenisprint_id !== '') {
                    $meta['jenisprint_id'] = $item_detail->db_jenisprint_id;
                }
                if (!isset($meta['side_id']) && isset($item_detail->side_id) && $item_detail->side_id !== '') {
                    $meta['side_id'] = $item_detail->side_id;
                }

                // Prepare Schema mapping for proper label display
                $cat_sch = !empty($item_detail->kategori_schema) ? json_decode($item_detail->kategori_schema, true) : [];
                $prd_sch = !empty($item_detail->product_schema) ? json_decode($item_detail->product_schema, true) : [];
                $merged_schema = [];
                if (is_array($cat_sch)) {
                    foreach ($cat_sch as $s)
                        if (isset($s['name']))
                            $merged_schema[$s['name']] = $s;
                }
                if (is_array($prd_sch)) {
                    foreach ($prd_sch as $s)
                        if (isset($s['name']))
                            $merged_schema[$s['name']] = $s;
                }

                // Clean key fallback for metadata (e.g. jenisprint instead of jenisprint_id)
                foreach ($merged_schema as $k => $field) {
                    if (!isset($meta[$k]) || $meta[$k] === '') {
                        $clean_k = str_replace('_id', '', $k);
                        if (isset($meta[$clean_k]) && $meta[$clean_k] !== '') {
                            $meta[$k] = $meta[$clean_k];
                        }
                    }
                }

                $get_label = function ($key, $val) use ($merged_schema) {
                    if (isset($merged_schema[$key]['options'][$val])) {
                        return $merged_schema[$key]['options'][$val];
                    }
                    return $val;
                };

                if (!empty($meta['warna'])) {
                    $specs[] = "Warna: " . strtoupper($get_label('warna', $meta['warna']));
                }

                // Add Mesin / Proses (e.g. Indoor, Outdoor, Epson, Konica)
                if (!empty($meta['jenisprint_id'])) {
                    $val_label = $get_label('jenisprint_id', $meta['jenisprint_id']);
                    // For Kategori 1/2 without schema, fallback hardcode if necessary, but DB mostly matches now
                    // Some older data has raw values like "1" or "2"
                    if ($val_label === '1' && $item_detail->kategori_id == 2)
                        $val_label = 'Indoor Eco Solvent';
                    if ($val_label === '2' && $item_detail->kategori_id == 2)
                        $val_label = 'Outdoor';
                    if ($val_label === '3' && $item_detail->kategori_id == 2)
                        $val_label = 'Indoor UV';
                    if ($val_label === '1' && $item_detail->kategori_id == 1)
                        $val_label = 'Konica';
                    if ($val_label === '3' && $item_detail->kategori_id == 1)
                        $val_label = 'Fujixerox';
                    $specs[] = strtoupper($val_label);
                }

                // Add Bahan / Material (except for Cat 5,9 which handle it specially below)
                if (!empty($meta['ket_1']) && !in_array($item_detail->kategori_id, [5, 9])) {
                    $specs[] = strtoupper($get_label('ket_1', $meta['ket_1']));
                }

                // Add Sisi / Muka
                if (!empty($meta['sisi'])) {
                    $specs[] = strtoupper($get_label('sisi', $meta['sisi']));
                } elseif (!empty($meta['side_id'])) {
                    $specs[] = strtoupper($get_label('side_id', $meta['side_id']));
                }

                // Add Finishing if exists
                if (!empty($meta['finishing_id'])) {
                    $specs[] = strtoupper($get_label('finishing_id', $meta['finishing_id']));
                }

                // Add LEBAR KAIN / LEBAR ROLL info from Schema if applicable (Kategori 5 / 9)
                if (in_array($item_detail->kategori_id, [5, 9])) {
                    $widthCode = $meta['ket_1'] ?? '0';

                    if ($widthCode !== '0') {
                        $cat_sch = !empty($item_detail->kategori_schema) ? json_decode($item_detail->kategori_schema, true) : [];
                        $prd_sch = !empty($item_detail->product_schema) ? json_decode($item_detail->product_schema, true) : [];

                        $merged = [];
                        if (is_array($cat_sch)) {
                            foreach ($cat_sch as $s)
                                if (isset($s['name']))
                                    $merged[$s['name']] = $s;
                        }
                        if (is_array($prd_sch)) {
                            foreach ($prd_sch as $s)
                                if (isset($s['name']))
                                    $merged[$s['name']] = $s;
                        }

                        if (isset($merged['ket_1']['options'][$widthCode])) {
                            $wNameStr = strtoupper($merged['ket_1']['options'][$widthCode]);

                            $pStr = $p . " M";
                            $idx = array_search($pStr, $specs);
                            if ($idx !== false) {
                                // Combine Width and Length -> "1.2 M x 3 M"
                                $specs[$idx] = str_replace(',', ' x', $wNameStr) . " x " . $pStr;
                            } else {
                                // Format the wNameStr properly
                                $formatted_wNameStr = str_replace(',', ' x', $wNameStr);
                                array_unshift($specs, $formatted_wNameStr);
                            }
                        }
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
                        <td style="font-weight: bold;"><?= $qty ?> PCS</td>
                    </tr>
                    <?php if (!empty($spec_text)): ?>
                        <tr>
                            <td style="font-weight: bold; vertical-align: top; font-size: 0.85em;">KET</td>s
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