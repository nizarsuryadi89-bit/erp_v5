<div class="row">
    <section class="panel">
        <div class="panel-body">
            <div class="row">

                <div class="col-sm-6">
                    Jumlah Data : <?= $total_rows ?>
                </div>
                <div class="col-sm-6 text-right">
                    <div class="mb-md">
                        <button type="button" class="btn btn-primary" data-toggle="modal"
                            data-target="#modalTambahLembur">
                            Tambah Konsumen
                        </button>
                    </div>
                </div>
            </div>

            <table class="table table-bordered table-striped mb-none" id="datatable-details">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Konsumen</th>
                        <th>No HP</th>
                        <th>Jumlah Transaksi</th>
                        <th>Total Transaksi</th>
                        <th>Point</th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    foreach ($tampil_data as $dt) {
                        $point = $dt->total_transaksi / 250000;
                        ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $dt->konsumen_nama ?></td>
                            <td><?= $dt->konsumen_nohp ?></td>
                            <td><?= $dt->jumlah_transaksi ?></td>
                            <td class="text-right">Rp. <?= number_format($dt->total_transaksi) ?></td>
                            <td class="text-right"><?= floor($point) ?></td>

                        </tr>
                        <?php
                        $no++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>
    <!-- end: page -->
    </section>
</div>

<div class="modal fade" id="modalTambahLembur" tabindex="-1" role="dialog" aria-labelledby="modalLabelLembur"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="waForm" method="POST" action="<?= base_url('pengaturan/konsumen_add') ?>" novalidate>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabelLembur">Tambah Data Routing</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Konsumen</label>
                        <input type="text" name="konsumen_nama" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Alamat</label>
                        <input type="text" name="konsumen_alamat" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="wa_number">Nomor WhatsApp:</label>
                        <input type="tel" id="wa_number" name="konsumen_nohp" placeholder="+6281234567890" required
                            pattern="^\+?\d{8,15}$" class="form-control" />
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" name="konsumen_email" class="form-control" required>
                    </div>

                    <div id="error" class="error"></div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    </div>
                </div>
        </form>
    </div>
</div>