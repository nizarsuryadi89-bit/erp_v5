<style>
    body {
        background-color: #f4f6f9;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .invoice-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        margin-top: 30px;
        background: #fff;
        max-width: 400px;
        /* Matches thermal width roughly */
        margin-left: auto;
        margin-right: auto;
        padding: 20px;
    }

    /* Styles from cetak_invoice.php adapted */
    table.a,
    table.b,
    table.footer,
    table.bayar {
        width: 100%;
        /* Full width of the card */
        font-family: Arial, sans-serif;
        font-size: 12px;
        border: none;
    }

    table.a {
        height: auto;
    }

    p {
        text-align: center;
        font-size: 10px;
        margin: 2px 0;
    }

    hr {
        border: solid 0.5px #ddd;
        margin: 10px 0;
    }

    .qty-col {
        text-align: center;
        width: 15%;
    }

    .price-col {
        text-align: right;
        width: 25%;
    }

    .total-col {
        text-align: right;
        width: 25%;
    }

    .status-badge {
        font-size: 14px;
        padding: 10px 15px;
        border-radius: 50px;
        font-weight: 600;
        display: block;
        text-align: center;
        margin: 20px 0;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .status-pending {
        background: #fff8e1;
        color: #f39c12;
        border: 1px solid #ffe082;
    }

    .status-process {
        background: #e3f2fd;
        color: #3498db;
        border: 1px solid #90caf9;
    }

    .status-ready {
        background: #e8f5e9;
        color: #2ecc71;
        border: 1px solid #a5d6a7;
    }

    .status-taken {
        background: #ecf0f1;
        color: #7f8c8d;
        border: 1px solid #bdc3c7;
    }

    @media print {
        .d-print-none {
            display: none !important;
        }
    }
</style>

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

<style>
    /* ... (Existing styles maintained) ... */

    /* Timeline Styles */
    .timeline {
        position: relative;
        padding: 20px 0;
        list-style: none;
    }

    .timeline:before {
        content: " ";
        position: absolute;
        top: 0;
        bottom: 0;
        left: 20px;
        width: 2px;
        background-color: #e9ecef;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 20px;
        padding-left: 50px;
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .timeline-marker {
        position: absolute;
        left: 11px;
        top: 0;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid #ccc;
        background: #fff;
    }

    .timeline-item.active .timeline-marker {
        background: #28a745;
        border-color: #28a745;
    }

    .timeline-item.passed .timeline-marker {
        background: #28a745;
        border-color: #28a745;
    }

    .timeline-content h6 {
        margin-bottom: 5px;
        font-weight: bold;
    }

    .timeline-content p {
        text-align: left;
        margin: 0;
        color: #666;
    }

    /* Custom Tab Styles */
    .nav-pills .nav-link.active,
    .nav-pills .show>.nav-link {
        background-color: #007bff;
    }

    .nav-pills .nav-link {
        color: #495057;
        font-weight: 600;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="invoice-card">

            <!-- Tabs Navigation -->


            <div class="tab-content" id="pills-tabContent">

                <!-- Tab 1: Invoice -->
                <div class="tab-pane fade show active" id="pills-invoice" role="tabpanel"
                    aria-labelledby="pills-invoice-tab">

                    <!-- Header -->
                    <table class='a'>
                        <tr>
                            <td colspan="3" align="center">
                                <?php if (!empty($perusahaan['logo'])): ?>
                                    <img src="<?= base_url('assets/images/' . $perusahaan['logo']) ?>" width="50%">
                                <?php else: ?>
                                    <h3><?= $perusahaan['nama_perusahaan'] ?? 'TGM Printing' ?></h3>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <!-- Address Info -->
                        <tr>
                            <td colspan="3" align="center">
                                <p><?= $perusahaan['alamat_perusahaan'] ?? '' ?></p>
                                <p>Email: <?= $perusahaan['email'] ?? '' ?></p>
                                <hr>
                            </td>
                        </tr>

                        <!-- Order Info -->
                        <tr>
                            <td colspan="3" align="center">
                                <p style="font-size:16px; font-weight: bold;">No Invoice: <?= $nospk ?></p>
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
                            <td>Tgl Order</td>
                            <td>:</td>
                            <td>
                                <?php
                                $tgl = !empty($pembayaran->created_at) ? $pembayaran->created_at : $pembayaran->tgl_bayar . ' ' . $pembayaran->jam_bayar;
                                if (strtotime($tgl) > 0) {
                                    echo date_indo(date('Y-m-d', strtotime($tgl))) . ' / ' . date('H:i', strtotime($tgl));
                                } else {
                                    echo "-";
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Deskprint</td>
                            <td>:</td>
                            <td><?= $pembayaran->username ?? '-' ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" align="center">
                                <hr>
                            </td>
                        </tr>
                    </table>

                    <!-- Products Table -->
                    <style>
                        .tabel-struk {
                            border-collapse: collapse !important;
                            width: 100%;
                            border: 1px solid #000;
                        }

                        .tabel-struk td {
                            border: 1px solid #000;
                            padding: 4px;
                        }
                    </style>
                    <table class='a tabel-struk'>
                        <tr>
                            <th width="35%" style="text-align: left;">Product</th>
                            <th class="qty-col">Qty</th>
                            <th class="price-col">Harga</th>
                            <th class="total-col">Jumlah</th>
                        </tr>
                        <?php
                        $subtotal = 0;
                        $groupedItems = [];
                        foreach ($detailProduk as $dt) {
                            $catName = !empty($dt->kategori_nama) ? $dt->kategori_nama : 'LAINNYA';
                            $groupedItems[$catName][] = $dt;

                            $harga = invoice_detail_price($dt);
                            $jumlah = invoice_detail_total($dt, $harga);
                            $subtotal += $jumlah;
                        }
                        ?>

                        <?php foreach ($groupedItems as $categoryName => $items): ?>
                            <tr>
                                <td colspan="4"
                                    style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6; padding: 6px 10px;">
                                    <span class="badge bg-secondary">KATEGORI: <?= strtoupper($categoryName) ?></span>
                                </td>
                            </tr>
                            <?php foreach ($items as $dt):
                                $harga = invoice_detail_price($dt);
                                $jumlah = invoice_detail_total($dt, $harga);
                                ?>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <b><?= $dt->detail_product ?></b>
                                        <?php if (isset($dt->satuan_id) && $dt->satuan_id == 11): ?>
                                            <br><small><?= $dt->panjang ?> M
                                                <?php if (!empty($dt->lebar) && $dt->lebar != 0)
                                                    echo "(L: {$dt->lebar}cm)"; ?>
                                            </small>
                                        <?php elseif (isset($dt->panjang) && isset($dt->lebar) && $dt->panjang > 0 && $dt->lebar > 0): ?>
                                            <br><small><?= $dt->panjang ?> x <?= $dt->lebar ?></small>
                                        <?php elseif (isset($dt->panjang) && $dt->panjang > 0 && empty($dt->lebar)): ?>
                                            <br><small><?= $dt->panjang ?> M</small>
                                        <?php endif; ?>
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
                                        } else {
                                            $cat_sch = !empty($dt->kategori_schema) ? json_decode($dt->kategori_schema, true) : [];
                                            $prd_sch = !empty($dt->product_schema) ? json_decode($dt->product_schema, true) : [];
                                            if (is_array($cat_sch)) {
                                                foreach ($cat_sch as $s) if (isset($s['name'])) $schema_map[$s['name']] = $s;
                                            }
                                            if (is_array($prd_sch)) {
                                                foreach ($prd_sch as $s) if (isset($s['name'])) $schema_map[$s['name']] = $s;
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

                                            // Skip ket_1 and lebar for DTF because we already display it in P x L
                                            if ($dt->kategori_id == 9 && in_array($key_lower, ['ket_1', 'lebar'])) {
                                                continue;
                                            }

                                            if ($key_lower == 'sisi' && !$is_in_schema) { // Legacy: skip if schema handles it
                                                if (in_array($dt->kategori_id, [2, 5, 9]) || strtolower($dt->kategori_nama ?? '') == 'large format') {
                                                    continue;
                                                }
                                                if ($val == '1' || $val == '2') {
                                                    $val = $val . ' Sisi';
                                                }
                                                
                                                // Dedup
                                                $val_clean = strtolower(trim($val));
                                                if (!in_array($val_clean, $printed_values)) {
                                                    $meta_str .= "$val &bull; ";
                                                    $printed_values[] = $val_clean;
                                                }
                                                continue;
                                            }

                                            if ($key_lower == 'mesin_fallback' && !$is_in_schema) {
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
                                                $meta_str .= "$val &bull; ";
                                                $printed_values[] = $val_clean;
                                            }
                                        }

                                        if (!empty($meta_str)): ?>
                                            <br><small class="text-secondary"><?= rtrim($meta_str, ' &bull; ') ?></small>
                                        <?php else: ?>
                                            <?php if (isset($dt->ket_1) && !empty($dt->ket_1)): ?>
                                                <br><small> <?= $dt->ket_1 ?> Sisi</small>
                                            <?php endif; ?>
                                            <?php if (!empty($dt->side_id) && !empty($dt->side_name)): ?>
                                                <br><small><?= $dt->side_name ?></small>
                                            <?php elseif (!empty($dt->side_id)): ?>
                                                <br><small>Side ID: <?= $dt->side_id ?></small>
                                            <?php endif; ?>

                                            <?php if ($dt->kategori_id == 2 && !empty($dt->jenisprint_id)): ?>
                                                <?php
                                                $machineLabel = 'LAINNYA';
                                                if ($dt->jenisprint_id == 2)
                                                    $machineLabel = 'OUTDOOR';
                                                elseif ($dt->jenisprint_id == 3)
                                                    $machineLabel = 'INDOOR SOLVENT';
                                                elseif ($dt->jenisprint_id == 4)
                                                    $machineLabel = 'INDOOR UV';
                                                ?>
                                                <br><small>Mesin: <?= $machineLabel ?></small>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="qty-col"><?= $dt->qty ?> </td>
                                    <td class="price-col"><?= number_format($harga) ?></td>
                                    <td class="total-col"><?= number_format($jumlah) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </table>

                    <br>

                    <!-- Payment Summary -->
                    <table class='bayar'>
                        <?php
                        $diskon = isset($pembayaran->diskon_total) ? floatval($pembayaran->diskon_total) : (isset($pembayaran->diskon) ? floatval($pembayaran->diskon) : 0);
                        $ongkir = isset($pembayaran->ongkir) ? floatval($pembayaran->ongkir) : 0;
                        $total_tagihan_calc = (isset($pembayaran->total_tagihan) && $pembayaran->total_tagihan > 0) ? floatval($pembayaran->total_tagihan) : ($subtotal + $ongkir - $diskon);
                        $is_lunas = $pembayaran->total_bayar >= $total_tagihan_calc;
                        ?>
                        <tr>
                            <td width="50%"></td>
                            <td colspan="3">Sub Total</td>
                            <td align="right"><?= number_format($subtotal, 0, ".", ".") ?></td>
                        </tr>
                        <?php if ($diskon > 0): ?>
                            <tr>
                                <td></td>
                                <td colspan="3">Diskon</td>
                                <td align="right"><?= number_format($diskon, 0, ".", ".") ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($ongkir > 0): ?>
                            <tr>
                                <td></td>
                                <td colspan="3">Ongkir</td>
                                <td align="right"><?= number_format($ongkir, 0, ".", ".") ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($diskon > 0 || $ongkir > 0 || (isset($pembayaran->total_tagihan) && $pembayaran->total_tagihan != $subtotal)): ?>
                            <tr>
                                <td></td>
                                <td colspan="3">Total Tagihan</td>
                                <td align="right"><?= number_format($total_tagihan_calc, 0, ".", ".") ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td colspan="5">
                                <hr style="margin: 5px 0;">
                            </td>
                        </tr>
                        <!-- Payment Logic -->
                        <tr>
                            <td></td>
                            <td colspan="3">Total Bayar</td>
                            <td align="right"><?= number_format($pembayaran->total_bayar, 0, ".", ".") ?></td>
                        </tr>
                        <tr>
                            <td colspan="5">
                                <hr style="margin: 5px 0;">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="3"><?= ($is_lunas) ? 'Kembali' : 'Kekurangan' ?>
                            </td>
                            <td align="right">
                                <?= number_format(abs($pembayaran->total_bayar - $total_tagihan_calc), 0, ".", ".") ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center" style="padding-top: 15px;">
                                <div
                                    style="border: 2px solid #333; display: inline-block; padding: 5px 15px; font-weight: bold; border-radius: 5px;">
                                    <?= ($is_lunas) ? 'LUNAS' : 'BELUM LUNAS' ?>
                                </div>
                            </td>
                        </tr>
                        <br>

                        <?php if (!empty($rekening)): ?>


                            <script>
                                function copyToClipboard(text, element) {
                                    navigator.clipboard.writeText(text).then(function () {
                                        // Show feedback
                                        const feedback = element.querySelector('.copy-feedback');
                                        if (feedback) {
                                            feedback.classList.remove('d-none');
                                            setTimeout(() => {
                                                feedback.classList.add('d-none');
                                            }, 1500);
                                        }
                                    }, function (err) {
                                        console.error('Async: Could not copy text: ', err);
                                    });
                                }
                            </script>
                        <?php endif; ?>


                </div>


            </div>

        </div>

    </div>
</div>

<div class="text-center mt-3 mb-5 d-print-none" data-html2canvas-ignore="true">
    <button onclick="window.print()" class="btn btn-secondary me-2">
        <i class="fas fa-print me-1"></i> Print
    </button>
    <button onclick="downloadPDF()" class="btn btn-danger">
        <i class="fas fa-file-pdf me-1"></i> Download PDF
    </button>
</div>

<!-- html2pdf.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"
    integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!-- Load Bootstrap JS for Tabs if not already loaded in footer -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function downloadPDF() {
        // Select the element to be converted
        const element = document.querySelector('.invoice-card'); // Or #pills-invoice if you only want the tab content

        // Clone the element to remove the buttons from the PDF if they are inside .invoice-card
        // But here buttons are outside, so it should be fine. 
        // However, tabs are inside invoice-card. We might want to just print the active content.
        // Let's target the active tab content specifically or styling might be off.
        // Actually, the structure shows .invoice-card wraps everything.

        // Options for html2pdf
        const opt = {
            margin: [0, 0, 0, 0], // top, left, bottom, right
            filename: 'Invoice_<?= $nospk ?>.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: [80, 297], orientation: 'portrait' }
        };

        // If we select .invoice-card, it includes tabs and possibly buttons if they were inside.
        // Let's create a temporary container with just the invoice content to be clean

        // Generate PDF
        html2pdf().set(opt).from(element).save();
    }
</script>
