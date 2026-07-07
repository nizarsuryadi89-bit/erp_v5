<style>
    table.a,
    table.b,
    table.footer,
    table.bayar {
        width: 300px;
        font-family: Arial, sans-serif;
        font-size: 12px;
        border: none;
    }

    table.a {
        height: 75px;
    }

    table.b {
        height: 50px;
    }

    table.footer,
    table.bayar {
        height: 30px;
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

        if ($panjang != 0 && $lebar != 0) {
            return $panjang * $lebar * $harga * $qty;
        }

        return $harga * $qty;
    }
}
?>



<table class='a'>
    <tr>
        <td colspan="3" align="center"><img src="<?= base_url('assets/images/' . $perusahaan['logo']) ?>" width="50%">
        </td>
    </tr>
    <tr>
        <td colspan="3"><br></td>
    </tr>
    <tr>
        <td colspan="3" align="center">
            <p>
                <?= @$perusahaan['alamat_perusahaan'] ?>
            </p>
        </td>
    </tr>
    <tr>
        <td colspan="3" align="center">
            <p>Email:
                <?= @$perusahaan['email'] ?>
            </p>
            <hr>
        </td>
    </tr>

    <tr>
        <td colspan="3" align="center">
            <p style="font-size:16px; font-weight: bold;">No Invoice:
                <?= $pembayaran->nospk ?>
            </p>
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
        <td>
            <?= $pembayaran->konsumen_nama ?>
        </td>
    </tr>
    <tr>
        <td>HP</td>
        <td>:</td>
        <td>
            <?= $pembayaran->konsumen_nohp ?>
        </td>
    </tr>
    <tr>
        <td>Tanggal/Jam</td>
        <td>:</td>
        <td>
            <?= date_indo($pembayaran->tgl_bayar) ?> /
            <?= date('H:i', strtotime($pembayaran->jam_bayar)) ?>
        </td>
    </tr>
    <tr>
        <td>Kasir</td>
        <td>:</td>
        <td>
            <?= $pembayaran->kasir_name ?>
        </td>
    </tr>

</table>
<br>
<table class='a item-list'>
    <tr>
        <th width="35%" style="text-align: left; border: 0.5px solid black; padding: 3px;">Product</th>
        <th class="qty-col" style="border: 0.5px solid black; padding: 3px;">Qty</th>
        <th class="price-col" style="border: 0.5px solid black; padding: 3px;">Harga</th>
        <th class="total-col" style="border: 0.5px solid black; padding: 3px;">Jumlah</th>
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
                    <b>
                        <?= $dt->detail_product ?>
                    </b>
                    <?php if ($dt->panjang != 0 && $dt->lebar != 0): ?>
                        <?= $dt->panjang ?> x
                        <?= $dt->lebar ?>
                    <?php endif; ?>
                    <?php
                    // Logic for displaying Side (1 Sisi / 2 Sisi)
                    $display_side = '';

                    $p_side_id = !empty($dt->real_side_id) ? $dt->real_side_id : (isset($dt->side_id) ? $dt->side_id : '');
                    $p_side_name = !empty($dt->real_side_name) ? $dt->real_side_name : (isset($dt->side_name) ? $dt->side_name : '');
                    $p_ket_1 = isset($dt->ket_1) ? $dt->ket_1 : '';
                    if (isset($dt->kategori_id) && $dt->kategori_id == 1 && empty($p_ket_1) && !empty($dt->jenisprint_id)) {
                        $p_ket_1 = $dt->jenisprint_id;
                    }

                    if (!empty($p_side_name)) {
                        $display_side = $p_side_name;
                    } elseif (!empty($p_side_id)) {
                        if ($p_side_id == 1)
                            $display_side = "1 Sisi";
                        elseif ($p_side_id == 2)
                            $display_side = "2 Sisi";
                        else
                            $display_side = "Side: " . $p_side_id;
                    } elseif (!empty($p_ket_1)) {
                        $display_side = $p_ket_1 . " Sisi";
                    }
                    ?>

                    <?php if (!empty($display_side)): ?>
                        <br><small>
                            <?= $display_side ?>
                        </small>
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
                </td>
                <td class="qty-col">
                    <?= $dt->qty ?>
                </td>

                <td class="price-col">
                    <?= number_format($harga) ?>
                </td>
                <td class="total-col">
                    <?= number_format($jumlah) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
</table>

<br>

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
        <td align="right">
            <?= number_format($subtotal, 0, ".", ".") ?>
        </td>
    </tr>
    <?php if ($diskon > 0): ?>
        <tr>
            <td colspan="4">Diskon</td>
            <td align="right">
                <?= number_format($diskon, 0, ".", ".") ?>
            </td>
        </tr>
    <?php endif; ?>
    <?php if ($ongkir > 0): ?>
        <tr>
            <td colspan="4">Ongkir</td>
            <td align="right">
                <?= number_format($ongkir, 0, ".", ".") ?>
            </td>
        </tr>
    <?php endif; ?>
    <tr>
        <td colspan="4">Total Tagihan</td>
        <td align="right">
            <?= number_format($pembayaran->total_tagihan, 0, ".", ".") ?>
        </td>
    </tr>
    <tr>
        <td colspan="4">Total Bayar</td>
        <td align="right">
            <?= number_format($pembayaran->total_bayar, 0, ".", ".") ?>
        </td>
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
                    <br><small>
                        <?= $bayar->keterangan ?>
                    </small>
                <?php endif; ?>
            </td>
            <td align="right">
                <?= number_format($bayar->jumlah, 0, ".", ".") ?>
            </td>
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
            <p>Tidak Menerima <b>KOMPLAIN</b> atas Kerusakan/kesalahan <b>PRINT DILUAR ACC atau APPROVAL</b></p>
        </td>
    </tr>
    <tr>
        <td colspan="3" align="center">
            <hr>
            <img src="<?= base_url('./assets/qrcode/' . $pembayaran->nospk) ?>.png" width="50%">
            <p>Scan di sini untuk cek posisi pesanan anda</p>
        </td>
    </tr>
</table>

</body>
