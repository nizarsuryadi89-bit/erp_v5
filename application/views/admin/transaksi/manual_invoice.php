<style>
    table.a,
    table.b,
    table.footer,
    table.bayar {
        width: 300px;
        font-family: 'calibri', sans-serif;
        font-size: 12px;
        border: none;
    }

    table.a {
        height: 75px;
    }

    p {
        text-align: center;
        font-size: 10px;
    }

    hr {
        border: solid 0.5px;
    }

    table.item-list {
        border-collapse: collapse;
    }

    table.item-list td {
        border: 0.5px solid black;
        padding: 3px;
    }

    .badge-manual {
        background: #000;
        color: #fff;
        padding: 2px 5px;
        font-size: 10px;
        font-weight: bold;
        display: block;
        text-align: center;
        margin-bottom: 5px;
    }
</style>

<!-- <body onload="window.print();"> -->

<table class='a'>
    <tr>
        <td colspan="3" align="center">
            <img src="<?= base_url('assets/images/' . $perusahaan['logo']) ?>" width="50%">
        </td>
    </tr>
    <tr>
        <td colspan="3" align="center">
            <div class="badge-manual">MANUAL TRANSACTION RECORD</div>
            <p><?= @$perusahaan['alamat_perusahaan'] ?></p>
            <hr>
        </td>
    </tr>

    <tr>
        <td colspan="3" align="center">
            <p style="font-size:14px; font-weight: bold;">No. SPK: <?= $pembayaran->nospk ?></p>
        </td>
    </tr>
    <tr>
        <td colspan="3">
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
        <td>Tanggal</td>
        <td>:</td>
        <td><?= date_indo($pembayaran->tgl_bayar) ?> / <?= date('H:i', strtotime($pembayaran->jam_bayar)) ?></td>
    </tr>
</table>
<br>
<table class='a item-list'>
    <thead>
        <tr style="background: #eee;">
            <th>Item / Produk</th>
            <th align="center">Qty</th>
            <th align="right">Harga</th>
            <th align="right">Sub</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $calculated_subtotal = 0;
        foreach ($detailProduk as $dt):
            $line_total = $dt->qty * $dt->unit_price; // and handle other logic if needed, but manual items usually have unit_price stored
            $calculated_subtotal += $line_total;
            ?>
            <tr>
                <td><?= $dt->product_id ?> - <?= $dt->product_nama ?></td>
                <td align="center"><?= $dt->qty ?></td>
                <td align="right"><?= number_format($dt->unit_price) ?></td>
                <td align="right"><?= number_format($line_total) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<br>

<table class='bayar'>
    <tr>
        <td rowspan="8" width="30%"></td>
        <td colspan="4">Sub Total</td>
        <td align="right"><?= number_format($calculated_subtotal) ?></td>
    </tr>
    <tr style="font-weight: bold; background: #eee;">
        <td colspan="4">TOTAL TAGIHAN</td>
        <td align="right">Rp <?= number_format($pembayaran->total_tagihan, 0, ",", ".") ?></td>
    </tr>
    <tr>
        <td colspan="6">
            <hr>
        </td>
    </tr>
    <?php if (!empty($detailBayar)):
        foreach ($detailBayar as $bayar): ?>
            <tr>
                <td colspan="4">
                    <?= strtoupper($bayar->metode) ?>
                </td>
                <td align="right"><?= number_format($bayar->jumlah, 0, ",", ".") ?></td>
            </tr>
        <?php endforeach; endif; ?>
    <tr style="font-weight: bold; border-top: 1px dashed black;">
        <td colspan="4">SUDAH DIBAYAR</td>
        <td align="right">Rp <?= number_format($pembayaran->total_bayar, 0, ",", ".") ?></td>
    </tr>
</table>

<table class='footer'>
    <tr>
        <td width="30%"></td>
        <td colspan="2">
            <?php
            $selisih = $pembayaran->total_bayar - $pembayaran->total_tagihan;
            if ($selisih > 0) {
                echo "Kembali: Rp " . number_format($selisih);
                echo "<br><b>LUNAS</b>";
            } elseif ($selisih < 0) {
                echo "Piutang: Rp " . number_format(abs($selisih));
                echo "<br><b>BELUM LUNAS</b>";
            } else {
                echo "<b>LUNAS</b>";
            }
            ?>
        </td>
    </tr>
    <tr>
        <td colspan="3" align="center">
            <hr>
            <p>Dokumen ini adalah bukti transaksi internal/manual yang sah di sistem ERP TGM.</p>
            <hr>
            <img src="<?= base_url('assets/qrcode/' . $pembayaran->nospk) ?>.png" width="40%">
            <p>Scan untuk validasi transaksi</p>
        </td>
    </tr>
</table>

</body>