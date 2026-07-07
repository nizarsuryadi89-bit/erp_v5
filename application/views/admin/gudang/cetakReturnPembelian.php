<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Return Pembelian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            width: 120px;
            font-weight: bold;
        }
        .info-value {
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        .signature {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 5px;
        }
        @media print {
            body {
                margin: 0;
                padding: 10px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()">Cetak</button>
        <button onclick="window.close()">Tutup</button>
    </div>

    <div class="header">
        <h2>RETURN PEMBELIAN</h2>
        <p>PT. TGM PRINTING</p>
        <p>Jl. Contoh No. 123, Jakarta</p>
        <p>Telp: (021) 1234567 | Email: info@tgmprinting.com</p>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">No Return:</div>
            <div class="info-value"><?= $getData['no_return'] ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal Return:</div>
            <div class="info-value"><?= date('d/m/Y', strtotime($getData['return_tgl'])) ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Supplier:</div>
            <div class="info-value"><?= $getData['supplier_nama'] ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Karyawan:</div>
            <div class="info-value"><?= $getData['nama_lengkap'] ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Keterangan:</div>
            <div class="info-value"><?= $getData['keterangan'] ?: '-' ?></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="30%">Bahan</th>
                <th width="10%">Satuan</th>
                <th width="15%">Qty Return</th>
                <th width="20%">Harga Return</th>
                <th width="20%">Sub Total</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $no = 1;
                $total_return = 0;
                foreach ($tampilData as $key => $value) {
                    $subtotal = $value->qty_return * $value->harga_return;
                    $total_return += $subtotal;
            ?>
            <tr>
                <td class="text-center"><?= $no ?></td>
                <td><?= $value->bahan_nama ?></td>
                <td class="text-center"><?= $value->satuan_nama ?></td>
                <td class="text-right"><?= number_format($value->qty_return) ?></td>
                <td class="text-right">Rp. <?= number_format($value->harga_return) ?></td>
                <td class="text-right">Rp. <?= number_format($subtotal) ?></td>
            </tr>
            <?php
                    $no++;
                }
            ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-right">Total Return:</td>
                <td class="text-right">Rp. <?= number_format($total_return) ?></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div class="signature">
            <div class="signature-line">Karyawan</div>
        </div>
        <div class="signature">
            <div class="signature-line">Supplier</div>
        </div>
        <div class="signature">
            <div class="signature-line">Manager Gudang</div>
        </div>
    </div>

    <div style="margin-top: 20px; font-size: 10px; color: #666;">
        <p><strong>Catatan:</strong></p>
        <ul>
            <li>Dokumen ini dicetak pada: <?= date('d/m/Y H:i:s') ?></li>
            <li>Return pembelian ini akan mengurangi stok gudang</li>
            <li>Pastikan barang yang diretur sesuai dengan kondisi yang disepakati</li>
        </ul>
    </div>
</body>
</html> 