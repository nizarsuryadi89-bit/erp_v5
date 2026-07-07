<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Piutang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 12px;
        }
        .table-a, .table-b, .table-footer, .table-bayar {
            border: none;
            width: 100%;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        p {
            text-align: center;
            font-size: 10px;
        }
        hr {
            border: solid 0.5px;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="<?= base_url($controller.'/piutang'); ?>" class="btn btn-dark">Kembali</a>
        <button class="btn btn-primary" disabled>Pembayaran Piutang <?= $nospk ?></button>
    </div>

    <div class="row">
        <div class="col-md-6">
            <table class="table-a">
                <tr><td colspan="3" class="text-center"><img src="<?= base_url('assets/images/'.$perusahaan['logo']) ?>" class="img-fluid"></td></tr>
                <tr><td colspan="3" class="text-center"><p><?= @$perusahaan['alamat_perusahaan'] ?></p></td></tr>
                <tr><td colspan="3" class="text-center"><p>Email : <?= @$perusahaan['email'] ?></p><hr></td></tr>
                <tr><td>Konsumen</td><td>:</td><td><?= $pembayaran['konsumen_nama'] ?></td></tr>
                <tr><td>HP</td><td>:</td><td><?= $pembayaran['konsumen_nohp'] ?></td></tr>
                <tr><td>Tanggal/Jam</td><td>:</td><td><?= date_indo(@$tanggal) ?> / <?= date_indo(@$jam) ?></td></tr>
                <tr><td>Kasir</td><td>:</td><td><?= $pembayaran['username'] ?></td></tr>
                <tr><td colspan="3"><hr></td></tr>
            </table>

            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Uk</th>
                        <th class="text-end">Harga</th>
                        <th class="text-end">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detailBayar as $dt): ?>
                        <?php $harga = $dt->{'harga_' . $dt->harga_aktif}; ?>
                        <tr>
                            <td><?= $dt->detail_product ?></td>
                            <td class="text-center"><?= $dt->qty ?></td>
                            <td></td>
                            <td class="text-end"><?= number_format($harga) ?></td>
                            <td class="text-end"><?= number_format($harga * $dt->qty) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <table class="table table-bordered mt-3">
                <tbody>
                    <tr><td colspan="5">Total</td><td class="text-end"><?= number_format($pembayaran['sub_total']) ?></td></tr>
                    <tr><td colspan="5">Diskon</td><td class="text-end"><?= number_format($pembayaran['diskon']) ?></td></tr>
                    <tr><td colspan="5">Ongkir</td><td class="text-end"><?= number_format($pembayaran['ongkir']) ?></td></tr>
                    <tr><td colspan="5">Grand Total</td><td class="text-end"><?= number_format($pembayaran['grand_total']) ?></td></tr>
                    <?php if ($pembayaran['bayar_tunai']): ?>
                    <tr><td colspan="5">Bayar Tunai</td><td class="text-end"><?= number_format($pembayaran['bayar_tunai']) ?></td></tr>
                    <?php endif; ?>
                    <?php if ($pembayaran['bayar_debit']): ?>
                    <tr><td colspan="5">Bayar EDC</td><td class="text-end"><?= number_format($pembayaran['bayar_debit']) ?></td></tr>
                    <?php endif; ?>
                    <?php if ($pembayaran['bayar_transfer']): ?>
                    <tr><td colspan="5">Transfer</td><td class="text-end"><?= number_format($pembayaran['bayar_transfer']) ?></td></tr>
                    <tr><td colspan="6">No. Rek: <?= $pembayaran['no_rekening'] ?></td></tr>
                    <?php endif; ?>
                    <?php if ($pembayaran['bayar_ewallet']): ?>
                    <tr><td colspan="5"><?= $pembayaran['ewallet_nama'] ?></td><td class="text-end"><?= number_format($pembayaran['bayar_ewallet']) ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalBayar"><i class="fa fa-save"></i> Bayar</button>
    </div>
</div>

<!-- Modal Bayar -->
<div class="modal fade" id="modalBayar" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= base_url('keuangan/action_bayar_piutang'); ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Pembayaran SPK No <?= $nospk ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="tunai" class="form-label">Bayar Tunai</label>
                            <input type="number" class="form-control" name="tunai" id="tunai">
                        </div>
                        <div class="col-md-6">
                            <label for="debit" class="form-label">Bayar EDC</label>
                            <input type="number" class="form-control" name="debit" id="debit">
                        </div>
                        <div class="col-md-6">
                            <label for="transfer" class="form-label">Transfer</label>
                            <input type="number" class="form-control" name="transfer" id="transfer">
                        </div>
                        <div class="col-md-6">
                            <label for="no_rekening" class="form-label">No. Rekening</label>
                            <input type="text" class="form-control" name="no_rekening" id="no_rekening">
                        </div>
                        <div class="col-md-6">
                            <label for="ewallet" class="form-label">Bayar E-Wallet</label>
                            <input type="number" class="form-control" name="ewallet" id="ewallet">
                        </div>
                        <div class="col-md-6">
                            <label for="ewallet_nama" class="form-label">Nama E-Wallet</label>
                            <input type="text" class="form-control" name="ewallet_nama" id="ewallet_nama">
                        </div>
                        <div class="col-md-6">
                            <label for="grandtotal" class="form-label">Grand Total</label>
                            <input type="number" class="form-control" name="grandtotal" id="grandtotal" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="kurang" class="form-label">Kurang Bayar</label>
                            <input type="number" class="form-control" name="kurang" id="kurang" readonly>
                        </div>
                        <input type="hidden" id="sub_total" value="<?= $pembayaran['grand_total'] ?>">
                        <input type="hidden" name="id_pembayaran" value="<?= $pembayaran['id'] ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        ['tunai', 'debit', 'transfer', 'ewallet'].forEach(id => {
            document.getElementById(id)?.addEventListener('keyup', updateKurang);
        });

        function updateKurang() {
            const sub_total = parseInt(document.getElementById('sub_total').value || 0);
            const tunai = parseInt(document.getElementById('tunai').value || 0);
            const debit = parseInt(document.getElementById('debit').value || 0);
            const transfer = parseInt(document.getElementById('transfer').value || 0);
            const ewallet = parseInt(document.getElementById('ewallet').value || 0);

            const totalBayar = tunai + debit + transfer + ewallet;
            const kurang = sub_total - totalBayar;
            document.getElementById('kurang').value = kurang;
            document.getElementById('grandtotal').value = sub_total;
        }
    });
</script>
</body>
</html>
