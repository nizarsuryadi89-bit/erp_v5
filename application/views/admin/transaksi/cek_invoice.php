<style>
    table.a {
        border: solid 0px;
        width: 300px;
        font-family: lato;
        font-size: 14px;
        height: 75px;

    }

    table.b {
        border: solid 0px;
        width: 300px;
        font-family: lato;
        font-size: 14px;
        height: 50px;

    }

    table.footer {

        width: 300px;
        font-family: lato;
        font-size: 14px;
        height: 30px;
        /* align:right; */

    }

    table.bayar {

        width: 300px;
        font-family: lato;
        font-size: 12px;
        height: 30px;

    }

    p {
        text-align: center;
        font-size: 10px;
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

    hr {
        border: solid 0.5px;
    }
</style>
</head>

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

        return (float) ($item->harga_1 ?? 0);
    }

    function invoice_detail_total($item, $harga = null)
    {
        $metadata = invoice_detail_metadata($item);
        if (isset($metadata['subtotal']) && is_numeric($metadata['subtotal'])) {
            return (float) $metadata['subtotal'];
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

<section class="panel">
    <header class="panel-heading">


        <a href="<?= base_url('transaksi/produksi') ?>" class="btn btn-warning"><I class="fa fa-backward"></I> Kembali
        </a>
    </header>

    <div class="panel-body">
        <div class="col-sm-6">
            <table class='a'>

                <tr>
                    <td colspan="3" align="center"><img src="<?= base_url('assets/images/' . $perusahaan['logo']) ?>"
                            width="50%"></td>
                </tr>
                <tr>
                    <td colspan="3"><br></td>
                </tr>
                <tr>
                    <td colspan="3" align="center">
                        <p style="font-size:10px;"><?= @$perusahaan['alamat_perusahaan'] ?>
                    </td>
                    </p>
                </tr>

                <tr>
                    <td colspan="3" align="center">
                        <p style="font-size:10px;">Email : <?= @$perusahaan['email'] ?>
                            <hr>
                    </td>
                </tr>

                <tr>
                    <td colspan="3" align="center">
                        <h3>
                            <?= $pembayaran['nospk'] ?>
                        </h3>
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
                    <td><?= $pembayaran['konsumen_nama'] ?></td>
                </tr>
                <tr>
                    <td width="30%">HP</td>
                    <td width="5%">:</td>
                    <td><?= $pembayaran['konsumen_nohp'] ?></td>
                </tr>
                <?php
                $tanggal = $pembayaran['pembayaran_tgl'];
                $jam = $pembayaran['pembayaran_jam'];
                $tgldateline = substr($pembayaran['dateline_tgl'], 0, 10);

                ?>

                <tr>
                    <td width="30%">Tanggal/Jam</td>
                    <td width="5%">:</td>
                    <td><?= date_indo(@$tanggal) ?> / <?= date_indo(@$jam) ?></td>
                </tr>
                <tr>
                    <td width="30%">Kasir</td>
                    <td width="5%">:</td>
                    <td><?= $pembayaran['username'] ?></td>
                </tr>

                <!-- <tr><td width="30%">Dateline:</td><td width="5%">: </td> <td><?= date_indo($tgldateline) ?> - <?= @$data_transaksi['dateline_jam'] ?> </td></tr> -->

                <tr>
                    <td colspan="3" align="center">
                        <hr>
                    </td>
                </tr>
            </table>

            <table class='a'>

                <tr>
                    <th width="40%" style="text-align: left;">Product</th>
                    <th class="qty-col">Qty</th>
                    <th class="price-col">Harga</th>
                    <th class="total-col">Jumlah</th>
                </tr>
                <?php
                $groupedBilling = [];
                foreach ($detailBayar as $dt) {
                    $catName = !empty($dt->kategori_nama) ? $dt->kategori_nama : 'LAINNYA';
                    $groupedBilling[$catName][] = $dt;
                }
                ?>

                <?php foreach ($groupedBilling as $categoryName => $items): ?>
                    <tr>
                        <td colspan="5" style="background-color: #f0f0f0; border-bottom: 1px solid #000; padding: 4px 0;">
                            <b>KATEGORI: <?= strtoupper($categoryName) ?></b>
                        </td>
                    </tr>
                    <?php foreach ($items as $dt):
                        $harga = invoice_detail_price($dt);
                        $jumlah = invoice_detail_total($dt, $harga);
                        ?>
                        <tr>
                            <td>
                                <?= $dt->detail_product ?>
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
                            <td class="qty-col"><?= $dt->qty ?></td>
                            <td class="price-col"><?= number_format($harga) ?></td>
                            <td class="total-col"><?= number_format($jumlah) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </table>
            <br>



            <table class='footer' border="0">

                <tr>
                    <td align='center' colspan='3'>
                        <hr>
                        <p>Tidak Menerima <b>KOMPLAIN</b> atas Kerusakan/kesalahan
                            <b>PRINT DILUAR ACC atau APPROVAL</b>
                        </p>
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-sm-6">
            <h3 class="text-center">PROSES PRODUKSI</h3>
            <hr>
            <table class="table">
                <tr>
                    <th>Nama Produk</th>
                    <th>Qty</th>
                </tr>
                <?php
                $groupedProd = [];
                foreach ($detailprod as $prod) {
                    $catName = !empty($prod->kategori_nama) ? $prod->kategori_nama : 'LAINNYA';
                    $groupedProd[$catName][] = $prod;
                }
                ?>

                <?php foreach ($groupedProd as $categoryName => $items): ?>
                    <tr>
                        <td colspan="2" style="background-color: #f8f9fa;">
                            <b>KATEGORI: <?= strtoupper($categoryName) ?></b>
                        </td>
                    </tr>
                    <?php foreach ($items as $prod): ?>
                        <tr>
                            <td>
                                <?= $prod->product_nama ?>
                                <br>
                                <b><?= $prod->detail_product ?></b>
                                <?php if ($prod->kategori_id == 2 && !empty($prod->jenisprint_id)): ?>
                                    <?php
                                    $machineLabel = 'LAINNYA';
                                    if ($prod->jenisprint_id == 2)
                                        $machineLabel = 'OUTDOOR';
                                    elseif ($prod->jenisprint_id == 3)
                                        $machineLabel = 'INDOOR SOLVENT';
                                    elseif ($prod->jenisprint_id == 4)
                                        $machineLabel = 'INDOOR UV';
                                    ?>
                                    <br><small>Mesin: <?= $machineLabel ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= $prod->qty ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                <?php
                if ($cekproduksi['status_produksi'] == '0') {
                    ?>
                    <tr>
                        <td colspan="2">
                            <form action="<?= base_url('transaksi/proses_produksi') ?>" method="post">
                                <input type="hidden" name="status_produksi" value="1">
                                <input type="hidden" name="produksi_id" value="<?= $cekproduksi['produksi_id'] ?>">
                                <input type="hidden" name="nospk" value="<?= $pembayaran['nospk'] ?>">
                                <button class="btn btn-danger btn-block" type="submit" name="produksi">Produksi</button>
                            </form>
                        </td>
                    </tr>
                    <?php
                } elseif ($cekproduksi['status_produksi'] == '1') {
                    ?>
                    <tr>
                        <td colspan="2">
                            <form action="<?= base_url('transaksi/produksi_done') ?>" method="post">
                                <input type="hidden" name="status_produksi" value="2">
                                <input type="hidden" name="produksi_id" value="<?= $cekproduksi['produksi_id'] ?>">
                                <input type="hidden" name="nospk" value="<?= $pembayaran['nospk'] ?>">
                                <button class="btn btn-warning btn-block" type="submit" name="produksi">Selesaikan</button>
                            </form>
                        </td>
                    </tr>
                    <?php
                } elseif ($cekproduksi['status_produksi'] == '2') {
                    if ($cekproduksi['finishing_id'] == '1') {
                        ?>
                        <tr>
                            <td colspan="2">
                                <button class="btn btn-success btn-block disabled" type="submit" name="produksi">Kirim Ke OP
                                    Finishing</button>
                                <a href="<?= base_url('transaksi/spk_finishing/' . $pembayaran['nospk']) ?>"
                                    class="btn btn-primary btn-block">Cetak Label SPK untuk OP Finishing</a>
                            </td>
                        </tr>
                        <?php
                    } else {
                        ?>
                        <tr>
                            <td colspan="2">
                                <button class="btn btn-success btn-block disabled" type="submit" name="produksi">Produk sudah di
                                    PB</button>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </table>
        </div>
    </div>

    </div>
    </div>
    </div>
