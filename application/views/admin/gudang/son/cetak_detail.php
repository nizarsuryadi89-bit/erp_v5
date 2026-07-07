<!DOCTYPE html>
<html>
<head>
    <title>Cetak Detail Stok Opname</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        .table th, .table td { border: 1px solid #333; padding: 8px; text-align: left; }
        .table th { background: #eee; }
        .header { text-align: center; margin-bottom: 20px; }
        .btn-print { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Detail Stok Opname</h2>
    </div>
    <table class="table">
        <tr>
            <th>Tanggal Stok Opname</th>
            <td><?= isset($detail->son_tgl) ? date_indo($detail->son_tgl) : '-' ?></td>
        </tr>
        <tr>
            <th>Barcode</th>
            <td class="text-center">
                 <?php if (isset($detail->barcode) && !empty($detail->barcode)): ?>
                    <img src="<?= base_url('assets/uploads/barcode/'.$detail->barcode.'.png') ?>" alt="<?= $detail->barcode ?>">
                    <br>
                    <?= $detail->barcode ?>
                 <?php else: ?>
                     -
                 <?php endif; ?>
                
            </td>
        </tr>
        <tr>
            <th>Nama Barang</th>
            <td><?= isset($detail->bahan_nama) ? $detail->bahan_nama : '-' ?></td>
        </tr>
        <tr>
            <th>Stok Real</th>
            <td><?= isset($detail->son_real) ? $detail->son_real : '-' ?> <?= isset($detail->satuan_nama) ? $detail->satuan_nama : '' ?></td>
        </tr>
        <tr>
            <th>Stok Aplikasi</th>
            <td><?= isset($detail->son_aplikasi) ? $detail->son_aplikasi : '-' ?> <?= isset($detail->satuan_nama) ? $detail->satuan_nama : '' ?></td>
        </tr>
        <tr>
            <th>Selisih</th>
            <td>
                <?php
                    if (isset($detail->son_real) && isset($detail->son_aplikasi)) {
                        $selisih = $detail->son_real - $detail->son_aplikasi;
                        echo '<strong class="'.($selisih >= 0 ? 'text-success' : 'text-danger').'">'.$selisih.'</strong> ';
                        echo isset($detail->satuan_nama) ? $detail->satuan_nama : '';
                    } else {
                        echo '-';
                    }
                ?>
            </td>
        </tr>
    </table>
    <div class="btn-print">
        <button onclick="window.print()">Print</button>
    </div>
</body>
</html>
