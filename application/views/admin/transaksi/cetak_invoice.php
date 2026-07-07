<?php
// Safe default initialization of $perusahaan settings
$perusahaan_data = [];
if (!empty($perusahaan)) {
    if (is_object($perusahaan)) {
        $perusahaan_data = (array) $perusahaan;
    } elseif (is_array($perusahaan)) {
        $perusahaan_data = $perusahaan;
    }
}
$invoice_paper = !empty($perusahaan_data['invoice_paper']) ? $perusahaan_data['invoice_paper'] : '80mm';
$invoice_font_size = !empty($perusahaan_data['invoice_font_size']) ? (int) $perusahaan_data['invoice_font_size'] : 14; // default sesuai tbl_perusahaan
$invoice_font_family = !empty($perusahaan_data['invoice_font_family']) ? $perusahaan_data['invoice_font_family'] : 'Roboto';
$invoice_show_logo = isset($perusahaan_data['invoice_show_logo']) ? (int) $perusahaan_data['invoice_show_logo'] : 1;
$invoice_logo_width = !empty($perusahaan_data['invoice_logo_width']) ? (int) $perusahaan_data['invoice_logo_width'] : 50;
$invoice_show_qr = isset($perusahaan_data['invoice_show_qr']) ? (int) $perusahaan_data['invoice_show_qr'] : 1;
$invoice_qr_width = !empty($perusahaan_data['invoice_qr_width']) ? (int) $perusahaan_data['invoice_qr_width'] : 50;
$invoice_footer_note = !empty($perusahaan_data['invoice_footer_note']) ? $perusahaan_data['invoice_footer_note'] : '';
$invoice_section_spacing = isset($perusahaan_data['invoice_section_spacing']) ? (int) $perusahaan_data['invoice_section_spacing'] : 8;

$font_css = "'Roboto', sans-serif";
if ($invoice_font_family === 'Arial') {
    $font_css = "Arial, sans-serif";
} elseif ($invoice_font_family === 'Courier New') {
    $font_css = "'Courier New', Courier, monospace";
} elseif ($invoice_font_family === 'Inter') {
    $font_css = "'Inter', sans-serif";
} elseif ($invoice_font_family === 'monospace') {
    $font_css = "monospace";
}
?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Roboto:wght@400;700&display=swap');

    @media print {
        @page {
            <?php if ($invoice_paper === 'A4'): ?>
                size: A4;
                margin: 15mm;
            <?php elseif ($invoice_paper === 'A5'): ?>
                size: A5;
                margin: 10mm;
            <?php elseif ($invoice_paper === '58mm'): ?>
                size: 58mm auto;
                margin: 0;
            <?php else: ?>
                size: 80mm auto;
                margin: 0;
            <?php endif; ?>
        }

        body {
            margin: 0;
        }
    }

    body {
        font-family: <?= $font_css ?>;
        font-size: <?= $invoice_font_size ?>px;
        width: <?= $invoice_paper === 'A4' ? '180mm' : ($invoice_paper === 'A5' ? '128mm' : ($invoice_paper === '58mm' ? '54mm' : ($invoice_paper === '80mm' ? '80mm' : '76mm')) ) ?>;
        margin: 0 auto;
        padding: 4px;
        color: #000;
        background: #fff;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    table.a,
    table.b,
    table.footer,
    table.bayar {
        font-family: <?= $font_css ?>;
        font-size: <?= $invoice_font_size ?>px;
        border: none;
    }

    /* Header Styling */
    table.a td {
        padding: <?= max(1, round($invoice_section_spacing / 4)) ?>px 0;
    }

    p {
        text-align: center;
        font-size: <?= $invoice_font_size ?>px;
        margin: 2px 0;
        line-height: 1.2;
    }

    hr {
        border: none;
        border-top: 2px dashed #000;
        margin: <?= $invoice_section_spacing ?>px 0;
    }

    /* Item List Styling */
    table.item-list {
        width: 100%;
        margin-top: <?= max(2, round($invoice_section_spacing / 2)) ?>px;
        margin-bottom: <?= max(2, round($invoice_section_spacing / 2)) ?>px;
    }

<?php /* Updated CSS for item list using dynamic font size and spacing */ ?>
    table.item-list th {
        font-weight: bold;
        border-bottom: 2px dashed #000;
        padding-bottom: <?= max(2, round($invoice_section_spacing / 2)) ?>px;
        font-size: <?= $invoice_font_size ?>px;
    }

    table.item-list td {
        border-bottom: 1px dotted #888;
        padding: <?= $invoice_section_spacing ?>px 0;
        vertical-align: top;
        font-size: <?= $invoice_font_size ?>px;
    }

    .qty-col {
        font-size: <?= $invoice_font_size ?>px;
        text-align: center;
    }
    
    .price-col, .total-col {
        font-size: <?= $invoice_font_size ?>px;
        text-align: right;
    }

    /* Footer Styling */
    table.bayar td {
        padding: <?= max(1, round($invoice_section_spacing / 4)) ?>px 0;
    }

    table.footer {
        margin-top: <?= $invoice_section_spacing ?>px;
    }

    .fw-bold {
        font-weight: bold;
    }

    .fs-large {
        font-size: 1.15em;
    }
</style>

<meta http-equiv='refresh' content='0.2;<?php echo base_url('transaksi/transaksi_belumbayar') ?>'>

<?php
if (!function_exists('invoice_detail_metadata')) {
    function invoice_detail_metadata($item)
    {
        if (empty($item->metadata) || !is_string($item->metadata)) {
            return [];
        }

        $decoded = json_decode($item->metadata, true);
        return is_array($decoded) ? $decoded : [];
    }

    function invoice_detail_price($item)
    {
        // Prefer the historic snapshot price stored in tbl_pembayaran_produk.snapshot_harga
        if (isset($item->snapshot_harga) && is_numeric($item->snapshot_harga)) {
            return (float) $item->snapshot_harga;
        }

        $metadata = invoice_detail_metadata($item);
        if (isset($metadata['unit_price']) && is_numeric($metadata['unit_price'])) {
            return (float) $metadata['unit_price'];
        }

        if (isset($item->unit_price) && is_numeric($item->unit_price)) {
            return (float) $item->unit_price;
        }

        return (float) ($item->harga_1 ?? 0);
    }

    function invoice_detail_total($item, $harga = null)
    {
        // Prefer snapshot subtotal if available (already normalized)
        if (isset($item->snapshot_subtotal) && is_numeric($item->snapshot_subtotal)) {
            return (float) $item->snapshot_subtotal;
        }

        $metadata = invoice_detail_metadata($item);
        if (isset($metadata['subtotal']) && is_numeric($metadata['subtotal'])) {
            return (float) $metadata['subtotal'];
        }

        if (isset($item->subtotal) && $item->subtotal !== null && $item->subtotal !== '' && is_numeric($item->subtotal)) {
            return (float) $item->subtotal;
        }

        $harga = $harga === null ? invoice_detail_price($item) : (float) $harga;
        $qty = (float) ($item->qty ?? 0);
        $panjang = (float) ($item->panjang ?? 0);
        $lebar = (float) ($item->lebar ?? 0);
        $satuan_id = isset($item->satuan_id) ? (int) $item->satuan_id : 0;
        $kategori_id = isset($item->kategori_id) ? (int) $item->kategori_id : 0;
        $ket_1 = isset($item->ket_1) ? (string) $item->ket_1 : '';

        if ($satuan_id === 11 || ($kategori_id === 9 && $ket_1 === '5')) {
            return $panjang * $harga * $qty;
        }
        if ($panjang != 0 && $lebar != 0) {
            return $panjang * $lebar * $harga * $qty;
        }
        if ($panjang != 0 && $lebar == 0) {
            return $panjang * $harga * $qty;
        }

        return $harga * $qty;
    }
}
?>

<body onload="window.print();">

    <table class='a'>
        <?php if ($invoice_show_logo === 1): ?>
            <tr>
                <td colspan="3" align="center">
                    <?php
                    $logo_file = !empty($perusahaan_data['logo']) ? $perusahaan_data['logo'] : 'default-logo.png';
                    ?>
                    <img src="<?= base_url('assets/images/' . $logo_file) ?>" width="<?= $invoice_logo_width ?>%">
                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <td colspan="3" style="padding-top: <?= $invoice_section_spacing ?>px;"></td>
        </tr>
        <tr>
            <td colspan="3" align="center">
                <p><?= !empty($perusahaan_data['alamat_perusahaan']) ? $perusahaan_data['alamat_perusahaan'] : '' ?></p>
            </td>
        </tr>
        <tr>
            <td colspan="3" align="center">
                <p>Email: <?= !empty($perusahaan_data['email']) ? $perusahaan_data['email'] : '' ?></p>
                <hr>
            </td>
        </tr>

        <tr>
            <td colspan="3" align="center">
                <p style="font-size:1.15em; font-weight: bold;">No Invoice: <?= $pembayaran->nospk ?></p>
            </td>
        </tr>
        <tr>
            <td colspan="3" align="center">
                <hr>
            </td>
        </tr>

        <tr>
            <td width="30%">Konsumen</td>
            <td width="5%">:</td>
            <td><?= $pembayaran->konsumen_nama ?></td>
        </tr>
        <tr>
            <td>HP</td>
            <td>:</td>
            <td><?= $pembayaran->konsumen_nohp ?></td>
        </tr>
        <tr>
            <td>Tanggal/Jam</td>
            <td>:</td>
            <td><?= date_indo($pembayaran->tgl_bayar) ?> / <?= date('H:i', strtotime($pembayaran->jam_bayar)) ?></td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td>:</td>
            <td><?= $pembayaran->kasir_name ?></td>
        </tr>
        <tr>
            <td>Deskprint</td>
            <td>:</td>
            <td>
                <?= $pembayaran->deskprint_name ?>
            </td>
        </tr>

    </table>
    <div style="height: <?= max(2, round($invoice_section_spacing / 2)) ?>px;"></div>
    <table class='a item-list'>
        <tr>
            <th width="40%" style="text-align: left;">Product</th>
            <th class="qty-col">Qty</th>
            <th class="price-col">Harga</th>
            <th class="total-col">Jumlah</th>
        </tr>
        <?php
        $groupedItems = [];
        foreach ($detailProduk as $dt) {
            $catName = !empty($dt->kategori_nama) ? $dt->kategori_nama : 'LAINNYA';
            $groupedItems[$catName][] = $dt;
        }
        ?>

        <?php foreach ($groupedItems as $categoryName => $items): ?>
            <tr>
                <td colspan="4" style="background-color: #f0f0f0; border-bottom: 1px solid #000; padding: 4px 0;">
                    <b>KATEGORI: <?= strtoupper($categoryName) ?></b>
                </td>
            </tr>
            <?php foreach ($items as $dt):
                $harga = invoice_detail_price($dt);
                $jumlah = invoice_detail_total($dt, $harga);
                ?>
                <tr>
                    <td>
                        <b><?= $dt->detail_product ?></b>
                        <?php
                        // Tampilkan Ukuran (P x L)
                        $p = floatval($dt->panjang);
                        $l = floatval($dt->lebar);
                        $l_text = '';
                        
                        if ($l == 0 && !empty($dt->metadata)) {
                            $meta = json_decode($dt->metadata, true);
                            if (is_array($meta)) {
                                if (!empty($meta['lebar'])) {
                                    $l_text = $meta['lebar'];
                                } elseif (!empty($meta['ket_1']) && in_array($dt->kategori_id, [5, 9])) {
                                    $l_text = $meta['ket_1'];
                                }
                            }
                        } else if ($l > 0) {
                            $l_text = $l . (isset($dt->satuan_nama) ? ' ' . $dt->satuan_nama : '');
                        }

                        if ($p > 0 && !empty($l_text)) {
                            echo "<br><small style='font-weight: bold;'>{$p} x {$l_text}</small>";
                        } elseif ($p > 0) {
                            $satuan = isset($dt->satuan_nama) ? ' ' . $dt->satuan_nama : '';
                            echo "<br><small style='font-weight: bold;'>{$p}{$satuan}</small>";
                        }
                        ?>

                        <?php
                        // Tampilkan Dateline khusus untuk kategori Large Format
                        if (isset($dt->kategori_id) && $dt->kategori_id == 2 && !empty($dt->dateline_tgl) && $dt->dateline_tgl != '0000-00-00') {
                            $dl_tgl = date('d-m-Y', strtotime($dt->dateline_tgl));
                            $dl_jam = !empty($dt->dateline_jam) ? substr($dt->dateline_jam, 0, 5) : '';
                            echo "<br><small style='font-weight: bold;'>Dateline: {$dl_tgl} {$dl_jam}</small>";
                        }
                        ?>

                        <?php
                        // [NEW] Render Metadata Dinamis
                        $combined_meta = [];
                        $meta_str = '';

                        // 1. Ambil dari tbl_harga_product (Metadata Harga) untuk menghilangkan Hardcode Mesin/Bahan
                        $harga_id = isset($dt->snapshot_source_harga_id) ? $dt->snapshot_source_harga_id : (isset($dt->harga_id) ? $dt->harga_id : 0);
                        if ($harga_id > 0) {
                            $CI =& get_instance();
                            $harga_row = $CI->db->get_where('tbl_harga_product', ['harga_id' => $harga_id])->row();
                            if ($harga_row && !empty($harga_row->metadata)) {
                                $h_meta = json_decode($harga_row->metadata, true);
                                if (is_array($h_meta)) {
                                    $combined_meta = array_merge($combined_meta, $h_meta);
                                }
                            } else {
                                // Fallback jika tidak ada metadata di harga_product (Transaksi lama)
                                if ($dt->kategori_id == 2) {
                                    $mesin_id = !empty($dt->jenisprint_id) ? $dt->jenisprint_id : (isset($dt->ket_1) ? $dt->ket_1 : null);
                                    if ($mesin_id == 2 || $mesin_id == '2') $combined_meta['mesin_fallback'] = "OUTDOOR";
                                    elseif ($mesin_id == 3 || $mesin_id == '3') $combined_meta['mesin_fallback'] = "INDOOR SOLVENT";
                                    elseif ($mesin_id == 4 || $mesin_id == '4') $combined_meta['mesin_fallback'] = "INDOOR UV";
                                }
                            }
                        }

                        // 2. Ambil dari tbl_produksi (Metadata Transaksi)
                        if (!empty($dt->metadata)) {
                            $t_meta = json_decode($dt->metadata, true);
                            if (is_array($t_meta)) {
                                $combined_meta = array_merge($combined_meta, $t_meta);
                            }
                        }

                        // Pastikan ket_1 ikut diproses jika belum ada
                        if (!empty($dt->ket_1) && !isset($combined_meta['ket_1'])) {
                            $combined_meta['ket_1'] = $dt->ket_1;
                        }

                        // [NEW] Kategori 1: Jika jenisprint_id ada, pakai sebagai ket_1 (1 Sisi / 2 Sisi)
                        if (isset($dt->kategori_id) && !in_array((int)$dt->kategori_id, [2, 5, 9])) {
                            if (empty($combined_meta['ket_1'])) {
                                if (!empty($dt->jenisprint_id)) {
                                    $combined_meta['ket_1'] = $dt->jenisprint_id;
                                } elseif (!empty($combined_meta['jenisprint_id'])) {
                                    $combined_meta['ket_1'] = $combined_meta['jenisprint_id'];
                                }
                            }
                        }

                        // [NEW] Resolve schema untuk label dinamis
                        $schema_map = [];
                        $raw_schema = $dt->product_form_schema ?? $dt->subcategory_form_schema ?? $dt->category_form_schema ?? '';
                        if (!empty($raw_schema)) {
                            $schema_arr = json_decode($raw_schema, true);
                            if (is_array($schema_arr)) {
                                foreach ($schema_arr as $field) {
                                    if (isset($field['name'])) {
                                        $schema_map[$field['name']] = $field;
                                    }
                                }
                            }
                        }

                        // 3. Render Combined Metadata
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
                            
                            // Skip technical fields
                            if (!$is_in_schema) {
                                if (in_array($key_lower, ['unit_price', 'subtotal', 'price_snapshot_at', 'hpp', 'range_id', 'jenisprint', 'user', 'kodeproduk', 'nospk']) || substr($key_lower, -3) === '_id' || strpos($key_lower, 'subkategori') !== false) {
                                    continue;
                                }
                            }

                            // Skip ket_1 and lebar for Tekstil/DTF because we already display it in P x L
                            if (in_array($dt->kategori_id, [5, 9]) && in_array($key_lower, ['ket_1', 'lebar'])) {
                                continue;
                            }

                            if ($key_lower == 'sisi') {
                                if (in_array($dt->kategori_id, [2, 5, 9]) || strtolower($dt->kategori_nama ?? '') == 'large format') {
                                    continue;
                                }
                                if ($val == '1' || $val == '2') {
                                    $val = $val . ' Sisi';
                                } elseif (strtolower(trim($val)) == '1 side') {
                                    $val = '1 Sisi';
                                } elseif (strtolower(trim($val)) == '2 side') {
                                    $val = '2 Sisi';
                                } elseif ($val == '3' || stripos($val, 'Tidak Ada') !== false) {
                                    continue;
                                }
                                
                                // Dedup
                                $val_clean = strtolower(trim($val));
                                if (!in_array($val_clean, $printed_values)) {
                                    $meta_str .= "$val &bull; ";
                                    $printed_values[] = $val_clean;
                                }
                                continue;
                            }

                            if ($key_lower == 'mesin_fallback') {
                                $val_clean = strtolower(trim($val));
                                if (!in_array($val_clean, $printed_values)) {
                                    $meta_str .= "$val &bull; ";
                                    $printed_values[] = $val_clean;
                                }
                                continue;
                            }

                            // Hilangkan label "Ket 1:", tampilkan isinya saja
                            if (strpos($key_lower, 'ket_') === 0 && !$is_in_schema) {
                                $val = trim($val, '[]');
                                if (!empty($val)) {
                                    if (isset($dt->kategori_id) && !in_array((int)$dt->kategori_id, [2, 5, 9])) {
                                        if ($val == '1' || strtolower(trim($val)) == '1 side') $val = '1 Sisi';
                                        elseif ($val == '2' || strtolower(trim($val)) == '2 side') $val = '2 Sisi';
                                        elseif ($val == '3' || stripos($val, 'Tidak Ada') !== false) continue;
                                    }
                                    
                                    $val_clean = strtolower(trim($val));
                                    if (!in_array($val_clean, $printed_values)) {
                                        $meta_str .= "$val &bull; ";
                                        $printed_values[] = $val_clean;
                                    }
                                }
                                continue;
                            }

                            $val_clean = strtolower(trim($val));
                            if (!in_array($val_clean, $printed_values)) {
                                $meta_str .= "$label: $val &bull; ";
                                $printed_values[] = $val_clean;
                            }
                        }

                        if (!empty($meta_str)) {
                            echo '<br><small>' . rtrim($meta_str, ' &bull; ') . '</small>';
                        }
                        ?>
                    </td>
                    <td class="qty-col"><?= $dt->qty ?> </td>
                    <td class="price-col"><?= number_format($harga) ?></td>
                    <td class="total-col"><?= number_format($jumlah) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </table>
    <div style="height: <?= max(2, round($invoice_section_spacing / 2)) ?>px;"></div>
    <table class='bayar'>
        <?php
        // Hitung subtotal dari detail produk
        $subtotal = 0;
        foreach ($detailProduk as $dt) {
            $harga = invoice_detail_price($dt);
            $jumlah = invoice_detail_total($dt, $harga);
            $subtotal += $jumlah;
        }

        // Hitung diskon dan ongkir (jika ada)
        $diskon = 0; // Bisa diambil dari form pembayaran jika disimpan
        $ongkir = 0; // Bisa diambil dari form pembayaran jika disimpan
        ?>
        <tr>
            <td rowspan="8" width="40%" align="center"></td>
            <td colspan="4">Sub Total</td>
            <td align="right"><?= number_format($subtotal, 0, ".", ".") ?></td>
        </tr>
        <?php if ($diskon > 0): ?>
            <tr>
                <td colspan="4">Diskon</td>
                <td align="right"><?= number_format($diskon, 0, ".", ".") ?></td>
            </tr>
        <?php endif; ?>
        <?php if ($ongkir > 0): ?>
            <tr>
                <td colspan="4">Ongkir</td>
                <td align="right"><?= number_format($ongkir, 0, ".", ".") ?></td>
            </tr>
        <?php endif; ?>
        <!-- <tr>
            <td colspan="4">Total Tagihan</td>
            <td align="right"><?= number_format($pembayaran->total_tagihan, 0, ".", ".") ?></td>
        </tr> -->
        <tr>
            <td colspan="4">Total Bayar</td>
            <td align="right"><?= number_format($pembayaran->total_bayar, 0, ".", ".") ?></td>
        </tr>

        <?php foreach ($detailBayar as $bayar): ?>
            <tr>
                <td colspan="4">
                    <?php
                    switch ($bayar->metode) {
                        case 'tunai':
                            echo 'Bayar Tunai';
                            break;
                        case 'transfer':
                            echo 'Transfer Bank';
                            break;
                        case 'debit':
                            echo 'Bayar EDC/QRIS';
                            break;
                        case 'ewallet':
                            echo 'E-Wallet';
                            break;
                        case 'lainnya':
                            echo 'Lainnya';
                            break;
                        default:
                            echo ucfirst($bayar->metode);
                    }
                    ?>
                    <?php if ($bayar->keterangan): ?>
                        <br><small><?= $bayar->keterangan ?></small>
                    <?php endif; ?>
                </td>
                <td align="right"><?= number_format($bayar->jumlah, 0, ".", ".") ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <table class='footer'>
        <tr>
            <td width="40%"></td>
            <td colspan="2">
                <?php
                // Hitung selisih
                $selisih = $pembayaran->total_bayar - $pembayaran->total_tagihan;

                if ($selisih > 0) {
                    // Ada kembalian (pembayaran lebih)
                    echo "Kembali " . str_repeat("&nbsp;", 20) . number_format($selisih, 0, ".", ".");
                    echo "<br>Status : LUNAS";
                } elseif ($selisih < 0) {
                    // Ada piutang (pembayaran kurang)
                    $piutang = abs($selisih);
                    echo "Piutang " . str_repeat("&nbsp;", 28) . "-" . number_format($piutang, 0, ".", ".");
                } else {
                    // Pembayaran pas
                    echo "Status : LUNAS";
                }
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="3" align="center">
                <hr>
                <?php if (!empty($invoice_footer_note)): ?>
                    <p><?= htmlspecialchars($invoice_footer_note) ?></p>
                <?php else: ?>
                    <p>Tidak Menerima <b>KOMPLAIN</b> atas Kerusakan/kesalahan <b>PRINT DILUAR ACC atau APPROVAL</b></p>
                <?php endif; ?>
            </td>
        </tr>
        <?php if ($invoice_show_qr === 1): ?>
            <tr>
                <td colspan="3" align="center">
                    <hr>
                    <?php
                    $qr_file = './assets/qrcode/' . $pembayaran->nospk . '.png';
                    if (!file_exists($qr_file)) {
                        $CI =& get_instance();
                        $CI->load->library('ciqrcode');
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
                        $CI->ciqrcode->initialize($config);
                        $params = [
                            'data' => "https://erp.tgmprint.com/cekpesanan/spk/" . urlencode(encrypt_spk($pembayaran->nospk)),
                            'level' => 'H',
                            'size' => 10,
                            'savename' => FCPATH . $config['imagedir'] . $pembayaran->nospk . '.png',
                        ];
                        $CI->ciqrcode->generate($params);
                    }
                    ?>
                    <img src="<?= base_url('./assets/qrcode/' . $pembayaran->nospk) ?>.png"
                        width="<?= $invoice_qr_width ?>%">
                    <p>Scan di sini untuk cek posisi pesanan anda</p>
                </td>
            </tr>
        <?php endif; ?>
    </table>

</body>