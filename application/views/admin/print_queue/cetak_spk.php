<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <!-- Modern Typography -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&family=Space+Grotesk:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            color: #1e293b;
            background-color: #ffffff;
            margin: 0;
            padding: 20px;
            line-height: 1.4;
        }

        .spk-card {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #0f172a;
            padding: 30px;
            border-radius: 16px;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            position: relative;
        }

        /* Header Style */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px double #0f172a;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .brand-section h1 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 26px;
            font-weight: 900;
            margin: 0;
            letter-spacing: -0.5px;
            color: #0f172a;
        }

        .brand-section p {
            margin: 4px 0 0 0;
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
        }

        .spk-badge {
            background-color: #0f172a;
            color: #ffffff;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        /* Barcode Area */
        .barcode-container {
            text-align: right;
        }

        #barcode {
            margin-top: 5px;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-block {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 15px 20px;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            width: 120px;
            font-weight: 600;
            color: #475569;
        }

        .info-val {
            flex: 1;
            color: #0f172a;
            font-weight: 700;
        }

        .deadline-highlight {
            color: #ef4444 !important;
            font-size: 15px;
        }

        /* Table Style */
        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        .product-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            text-align: left;
            padding: 12px 15px;
            font-size: 13px;
            letter-spacing: 0.5px;
        }

        .product-table td {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
            vertical-align: top;
        }

        .product-table tr:last-child td {
            border-bottom: 2px solid #0f172a;
        }

        .prod-name {
            font-weight: 700;
            font-size: 15px;
            color: #0f172a;
        }

        .prod-spec {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
            font-family: 'Space Grotesk', sans-serif;
        }

        .qty-badge {
            background-color: #e2e8f0;
            color: #0f172a;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 13px;
        }

        /* Signature Blocks */
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            text-align: center;
            margin-top: 40px;
        }

        .sig-block {
            border-top: 1px dashed #94a3b8;
            padding-top: 15px;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
        }

        .sig-space {
            height: 60px;
        }

        /* Print Settings */
        .no-print-bar {
            background-color: #f1f5f9;
            padding: 15px 25px;
            border-radius: 10px;
            max-width: 800px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #cbd5e1;
        }

        .btn-print {
            background-color: #0f172a;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .btn-print:hover {
            background-color: #1e293b;
        }

        .btn-close {
            background-color: #ffffff;
            color: #475569;
            border: 1px solid #cbd5e1;
            padding: 10px 20px;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
        }

        .btn-close:hover {
            background-color: #f8fafc;
        }

        @media print {
            .no-print-bar {
                display: none;
            }

            body {
                padding: 0;
                background-color: #ffffff;
            }

            .spk-card {
                border: none;
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>

<body>

    <!-- Control bar for operators -->
    <div class="no-print-bar">
        <div>
            <strong style="color: #0f172a;">Antrian SPK Produksi</strong>
            <span style="color: #64748b; font-size: 13px; margin-left: 10px;">Siap Cetak ke Printer Lantai 2</span>
        </div>
        <div>
            <button onclick="window.close()" class="btn-close">Tutup Halaman</button>
            <button onclick="window.print()" class="btn-print">Cetak Sekarang (Print)</button>
        </div>
    </div>

    <!-- Printable SPK Ticket Card -->
    <div class="spk-card">
        <!-- Header -->
        <div class="header">
            <div class="brand-section">
                <h1>SURAT PERINTAH KERJA</h1>
                <p>DIVISI PRODUKSI LANTAI 2 &middot; TGM ERP SYSTEM</p>
            </div>
            <div class="barcode-container">
                <span class="spk-badge">SPK PRODUKSI</span>
                <div>
                    <svg id="barcode"></svg>
                </div>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="info-grid">
            <div class="info-block">
                <div class="info-row">
                    <span class="info-label">No SPK</span>
                    <span class="info-val" style="font-family: 'Space Grotesk', sans-serif; letter-spacing: 0.5px;"><?= $nospk ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Pelanggan</span>
                    <span class="info-val"><?= strtoupper($pemesanan->konsumen_nama ?? '-') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">No HP</span>
                    <span class="info-val"><?= $pemesanan->konsumen_nohp ?? '-' ?></span>
                </div>
            </div>
            <div class="info-block">
                <div class="info-row">
                    <span class="info-label">Tanggal Masuk</span>
                    <span class="info-val"><?= date('d M Y H:i', strtotime($pemesanan->created_at)) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Deadline</span>
                    <span class="info-val deadline-highlight">
                        <?php
                        $dateline = '-';
                        if (!empty($pemesanan->dateline_tgl)) {
                            $dateline = date('d M Y', strtotime($pemesanan->dateline_tgl));
                        } elseif (!empty($pemesanan->dateline)) {
                            $dateline = date('d M Y', strtotime($pemesanan->dateline));
                        }
                        echo $dateline;
                        ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Deskprint</span>
                    <span class="info-val"><?= strtoupper($pemesanan->username ?? '-') ?></span>
                </div>
            </div>
        </div>

        <!-- Product Table -->
        <table class="product-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="65%">Item Spesifikasi Produksi</th>
                    <th width="15%" style="text-align: center;">Qty</th>
                    <th width="15%" style="text-align: center;">Satuan</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($items as $item): ?>
                    <tr>
                        <td style="font-weight: 700; color: #64748b;"><?= $no++ ?></td>
                        <td>
                            <div class="prod-name"><?= $item->product_nama ?></div>
                            <div class="prod-spec">
                                <!-- Render Ukuran / Dimensions -->
                                <?php if (!empty($item->panjang) || !empty($item->lebar)): ?>
                                    <span>Ukuran: <?= $item->panjang ?? 0 ?> x <?= $item->lebar ?? 0 ?> cm</span> &bull; 
                                <?php endif; ?>

                                <!-- Render Metadata Dinamis -->
                                <?php
                                $meta_str = '';
                                if (!empty($item->metadata)) {
                                    $meta_json = json_decode($item->metadata, true);
                                    if (is_array($meta_json)) {
                                        foreach ($meta_json as $key => $val) {
                                            // Format the key
                                            $label = ucwords(str_replace('_', ' ', $key));
                                            
                                            // Skip some technical keys like unit_price/subtotal from showing on SPK 
                                            // because operators don't need price info, just specs
                                            if (in_array($key, ['unit_price', 'subtotal', 'price_snapshot_at', 'hpp', 'range_id', 'user', 'kodeproduk', 'nospk']) || substr($key, -3) === '_id') {
                                                continue;
                                            }
                                            
                                            if ($key === 'jenisprint') $label = 'Mesin';
                                            
                                            if ($key == 'sisi') {
                                                if (in_array($item->kategori_id, [2, 9]) || strtolower($item->kategori_nama ?? '') == 'large format') {
                                                    continue;
                                                }
                                                if ($val == '1' || $val == '2') {
                                                    $val = $val . ' Sisi';
                                                }
                                            }
                                            $meta_str .= "<span><strong>$label:</strong> $val</span> &bull; ";
                                        }
                                    }
                                }

                                // Fallback jika tidak ada metadata (Transaksi Lama)
                                if (empty($meta_str) && isset($item->ket_1)) {
                                    if (in_array($item->kategori_id, [2, 9]) || strtolower($item->kategori_nama ?? '') == 'large format') {
                                        // Skip fallback side for Large Format
                                    } else {
                                        if ($item->ket_1 == '1') $meta_str = "<span>1 Sisi (Single)</span> &bull; ";
                                        elseif ($item->ket_1 == '2') $meta_str = "<span>2 Sisi (Bolak-Balik)</span> &bull; ";
                                    }
                                }

                                echo rtrim($meta_str, ' &bull; ');
                                ?>

                                <!-- Render finishing / notes description -->
                                <?php if (!empty($item->keterangan)): ?>
                                    <div style="margin-top: 5px; font-weight: 600; color: #1e293b; background-color: #f1f5f9; padding: 6px 10px; border-radius: 6px; border-left: 3px solid #0f172a;">
                                        Catatan: <?= $item->keterangan ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td style="text-align: center; font-weight: 700;">
                            <span class="qty-badge"><?= $item->qty ?></span>
                        </td>
                        <td style="text-align: center; color: #475569; font-weight: 600;">
                            <?= $item->satuan_nama ?? 'Pcs' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Signatures Section -->
        <div class="signatures">
            <div>
                <div class="sig-space"></div>
                <div class="sig-block">Dibuat (Deskprint)</div>
            </div>
            <div>
                <div class="sig-space"></div>
                <div class="sig-block">Kasir Pembayaran</div>
            </div>
            <div>
                <div class="sig-space"></div>
                <div class="sig-block">Operator Lantai 2</div>
            </div>
        </div>
    </div>

    <!-- JsBarcode Library -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        // Render Barcode
        JsBarcode("#barcode", "<?= $nospk ?>", {
            format: "CODE128",
            width: 1.5,
            height: 35,
            displayValue: true,
            fontSize: 10,
            font: "Space Grotesk",
            margin: 0,
            textMargin: 4
        });
    </script>
</body>

</html>
