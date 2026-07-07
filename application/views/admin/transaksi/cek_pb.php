<style>
    .table-custom {
        width: 100%;
        font-family: Arial, sans-serif;
        font-size: 14px;
    }
    .table-custom td,
    .table-custom th {
        padding: 10px;
        vertical-align: middle;
    }
    .table-custom td.text-center {
        text-align: center;
    }
    .table-custom td.text-end {
        text-align: end;
    }
    .table-custom td.text-success {
        color: green;
    }
    .table-custom td.text-danger {
        color: red;
    }
    .footer p {
        font-size: 10px;
    }
    .footer {
        margin-top: 20px;
        border-top: 1px solid #ccc;
        padding-top: 10px;
        text-align: center;
    }
    .footer img {
        max-width: 50%;
        margin-top: 10px;
    }
</style>

<div class="app-content d-block">
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
            return $harga * (float) ($item->qty ?? 0);
        }
    }
    ?>
    <div class="container-fluid py-3">
        <!-- Back Button & Title -->
        <div class="d-flex justify-content-between mb-3">
            <a href="<?= base_url('produksi/pb') ?>" class="btn btn-warning">
                <i class="fa fa-backward"></i> Kembali
            </a>
        </div>

        <!-- Product and Payment Details -->
        <div class="card shadow-sm mb-3">
            <div class="card-header text-center">
                <img src="<?= base_url('assets/images/' . $perusahaan['logo']) ?>" class="img-fluid mb-2" style="max-width: 20%;" alt="Logo Perusahaan">
                <p style="font-size: 10px;"><?= @$perusahaan['alamat_perusahaan'] ?></p>
                <p style="font-size: 10px;">Email: <?= @$perusahaan['email'] ?></p>
                <hr>
                <h3><?= $pembayaran['nospk'] ?></h3>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-custom">
                    <tr>
                        <td width="30%"><strong>Konsumen</strong></td>
                        <td width="5%">:</td>
                        <td><?= $pembayaran['konsumen_nama'] ?></td>
                    </tr>
                    <tr>
                        <td><strong>HP</strong></td>
                        <td>:</td>
                        <td><?= $pembayaran['konsumen_nohp'] ?></td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal/Jam</strong></td>
                        <td>:</td>
                        <td><?= date_indo(@$pembayaran['pembayaran_tgl']) ?> / <?= date_indo(@$pembayaran['pembayaran_jam']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Kasir</strong></td>
                        <td>:</td>
                        <td><?= $pembayaran['username'] ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Product List Table -->
        <div class="card shadow-sm mb-3">
            <div class="card-header">
                <h5 class="mb-0">Detail Produk</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-custom">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Uk</th>
                                <th>Harga</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detailBayar as $dt): 
                                $harga = invoice_detail_price($dt);
                                $jumlah = invoice_detail_total($dt, $harga);
                            ?>
                                <tr>
                                    <td><?= $dt->detail_product ?></td>
                                    <td class="text-center"><?= $dt->qty ?></td>
                                    <td></td>
                                    <td class="text-end"><?= number_format($harga) ?></td>
                                    <td class="text-end"><?= number_format($jumlah) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Footer Information -->
        <div class="footer">
            <p>Tidak Menerima <strong>KOMPLAIN</strong> atas Kerusakan/Kesalahan <strong>PRINT DILUAR ACC atau APPROVAL</strong></p>
            <hr>
            <img src="<?= base_url('assets/qrcode/'.$pembayaran['nospk'].'.png')?>" alt="QR Code" width="120" height="120">
            <p>Scan di sini untuk cek posisi pesanan anda</p>
        </div>

        <!-- Pengambilan Barang -->
        <div class="card shadow-sm mt-4">
            <div class="card-header text-center">
                <h3>PENGAMBILAN BARANG</h3>
                <hr>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Produk</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <form action="<?= base_url('transaksi/action_pengambilan')?>" method="post">
                            <?php foreach ($detailprod as $dt): ?>
                                <tr>
                                    <td><?= $dt->product_nama ?></td>
                                    <td class="text-center"><?= $dt->qty ?></td>
                                    <td>
                                        <input type="hidden" name="id[]" value="<?= $dt->produksi_id ?>">
                                        <input type="hidden" name="nospk" value="<?= $dt->nospk ?>">
                                        <?php if ($dt->status_produksi == '3'): ?>
                                            <input type="checkbox" name="status[]" value="3" checked> Selesai
                                        <?php else: ?>
                                            <input type="checkbox" name="status[]" value="3"> <b>Selesaikan</b>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="3">
                                    <button class="btn btn-success btn-block" type="submit" name="produksi">Selesaikan Transaksi</button>
                                </td>
                            </tr>
                        </form>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
