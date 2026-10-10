<div class="container-fluid px-4">
    <!-- Header & Description -->
    <div class="row mb-3">
        <div class="col-12">
            <h2 class="fw-bold mb-1"><i class="bi bi-badge"></i> Pengambilan Barang</h2>

        </div>
    </div>

    <!-- Summary Cards -->


    <!-- Filter/Search Bar -->
    <div class="row mb-3">
        <div class="col-12">
            <form class="row g-2 align-items-center" method="get" action="">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="spk" placeholder="Cari SPK/Nama Produk...">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="status">
                        <option value="">Status</option>
                        <option value="0">Menunggu</option>
                        <option value="1">Sedang Berjalan</option>
                        <option value="2">Finishing</option>
                        <option value="3">Pengambilan Barang</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-search"></i>
                        Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="row">
        <section class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle" id="datatable-details">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>SPK</th>
                                    <th>Lokasi Cetak</th>


                                    <th>Nama Konsumen</th>
                                    <th>Keterangan Produksi</th>
                                    <th>Dateline</th>
                                    <th>Status</th>
                                    <th>Progress</th>
                                    <th>Operator / Mesin</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($tampilData as $dt) {
                                    // Status badge
                                    if ($dt->status_produksi == '0') {
                                        $status = '<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Menunggu</span>';
                                    } elseif ($dt->status_produksi == '1') {
                                        $status = '<span class="badge bg-info text-dark"><i class="bi bi-play-circle"></i> Berjalan</span>';
                                    } elseif ($dt->status_produksi == '2') {
                                        $status = '<span class="badge bg-danger"><i class="bi bi-tools"></i> PB</span>';
                                    } else {
                                        $status = '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Selesai</span>';
                                    }
                                    // Progress
                                    $progress = 0;
                                    if ((int) $dt->qty > 0) {
                                        $progress = min(100, round(((int) $dt->qty_produksi / (int) $dt->qty) * 100));
                                    }
                                    ?>
                                    <tr>
                                        <td><?= $no ?></td>
                                        <td>
                                            <span class="fw-bold">#<?= $dt->nospk ?></span><br>

                                        </td>
                                        <td>
                                            <?php if ($dt->lokasi_cetak == '2'): ?>
                                                <span class="badge bg-secondary">TGM Pusat</span>
                                            <?php elseif ($dt->lokasi_cetak == '3'): ?>
                                                <span class="badge bg-light text-dark">TGM Merch</span>
                                            <?php endif; ?>
                                        </td>


                                        <td>
                                            <span class="fw-semibold"><?= $dt->konsumen_nama ?? '-' ?></span>
                                        </td>
                                        <td class="text-start">
                                            <span class="text-muted small">Bahan Utama : <?= $dt->product_nama ?> -
                                                <?= $dt->side_id ?> Side </span>
                                            <br>Kuantitas:
                                            <span class="fw-bold"><?= $dt->qty ?></span>
                                            <?php if (($dt->panjang <> '0.00') OR ($dt->lebar <> '0.00')): ?>
                                                <br><span class="badge bg-secondary"><?= $dt->panjang ?> x
                                                    <?= $dt->lebar ?></span>
                                            <?php endif; ?>
                                            <?= $dt->satuan_nama ?>
                                        </td>
                                        <td>
                                            <?= date_indo($dt->dateline_tgl) ?><br>
                                            <span class="text-muted small"><?= $dt->dateline_jam ?></span>
                                        </td>
                                        <td class="text-center">
                                            <?= $status ?>
                                        </td>
                                        <td style="min-width:120px;">
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar <?php
                                                if ($progress == 100)
                                                    echo 'bg-success';
                                                elseif ($progress >= 50)
                                                    echo 'bg-info';
                                                elseif ($progress > 0)
                                                    echo 'bg-warning';
                                                else
                                                    echo 'bg-secondary';
                                                ?>" role="progressbar" style="width: <?= $progress ?>%;"
                                                    aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100">
                                                    <?= $progress ?>%
                                                </div>
                                            </div>
                                            <div class="small text-muted mt-1">Produksi: <?= $dt->qty_produksi ?> /
                                                <?= $dt->qty ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-semibold"><?= $dt->operator_nama ?></span><br>
                                            <span class="text-muted small"><?= $dt->operator_keterangan ?></span>
                                        </td>
                                        <td>
                                            <?php
                                            if ($dt->status_produksi == '0') {

                                                ?>
                                                <button type="button" class="btn btn-success btn-sm mb-1" data-bs-toggle="modal"
                                                    data-bs-target="#modalProses<?= $dt->produksi_id ?>">
                                                    <i class="bi bi-gear"></i> Proses
                                                </button>
                                                <?php
                                            } elseif ($dt->status_produksi == '1') {
                                                ?>
                                                <button type="button" class="btn btn-warning btn-sm mb-1" data-bs-toggle="modal"
                                                    data-bs-target="#modalProses<?= $dt->produksi_id ?>">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </button>
                                                <a href="<?= base_url('produksi/selesai/' . $dt->produksi_id) ?>"
                                                    class="btn btn-success btn-sm mb-1"
                                                    onclick="return confirm('Apakah Anda yakin ingin menyelesaikan produksi ini, dan Kirim ke PB ?')">
                                                    <i class="bi bi-check-circle"></i> Selesai
                                                </a>
                                                <?php
                                            }
                                            ?>

                                            <button type="button" class="btn btn-outline-primary btn-sm mb-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalInvoice<?= $dt->produksi_id ?>">
                                                <i class="bi bi-receipt"></i>
                                            </button>

                                            <a href="<?= base_url('produksi/cek_invoice/' . $dt->nospk) ?>"
                                                class="btn btn-info btn-sm mb-1">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                    <?php $no++;
                                } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php foreach ($tampilData as $dt): ?>
                        <!-- Proses Produksi -->
                        <div class="modal fade" id="modalProses<?= $dt->produksi_id ?>" tabindex="-1"
                            aria-labelledby="modalLabel<?= $dt->produksi_id ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-fullscreen">
                                <form action="<?= base_url('produksi/proses') ?>" method="post">
                                    <input type="hidden" name="produksi_id" value="<?= $dt->produksi_id ?>">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="modalLabel<?= $dt->produksi_id ?>">
                                                <i class="bi bi-pencil-square me-2"></i>Proses Produksi - <?= $dt->nospk ?>
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-4">
                                                <!-- Kiri - Produk dan Bahan -->
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Proses Sekarang</label>
                                                        <?php
                                                        $routing = $this->M_master->getRoutingproduct($dt->product_id)->result();
                                                        if ($routing) {
                                                            $routing = $routing[0]->routing_name;
                                                        } else {
                                                            $routing = 'Tidak ada routing';
                                                        }
                                                        ?>
                                                        <input type="text" class="form-control" value="<?= $routing ?>"
                                                            readonly>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Proses Selanjutnya</label>
                                                        <?php
                                                        $routing = $this->M_master->getRoutingproductnext($dt->product_id)->result();
                                                        if ($routing) {
                                                            $routing = $routing[1]->routing_name;
                                                        } else {
                                                            $routing = 'Tidak Ada';
                                                        }
                                                        ?>
                                                        <input type="text" class="form-control" value="<?= $routing ?>"
                                                            readonly>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Nama Produk</label>
                                                        <input type="text" value="<?= $dt->product_nama ?>"
                                                            class="form-control" readonly>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Bahan Utama</label>
                                                        <select name="bahanbaku_id" id="" class="form-select" required>
                                                            <?php foreach ($bahanBaku as $bahan): ?>
                                                                <option value="<?= $bahan->bahanbaku_id ?>"
                                                                    <?= ($bahan->bahanbaku_id == $dt->bahanbaku_id) ? 'selected' : '' ?>><?= $bahan->bahanbaku_nama ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <div class="form-text">Pilih bahan utama yang digunakan untuk
                                                            produksi dan Jika Ada Perubahan Bahan</div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Jumlah Pesanan</label>
                                                        <input type="text" value="<?= $dt->qty ?>" class="form-control"
                                                            readonly>
                                                    </div>




                                                    <?php
                                                    if ($dt->panjang <> '0.00' || $dt->lebar <> '0.00') {

                                                        ?>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Panjang (meter)</label>
                                                            <div class="input-group">
                                                                <input type="text" name="panjang_produksi" class="form-control"
                                                                    value="<?= $dt->panjang ?>" readonly>
                                                                <input type="text" value="<?= $dt->panjang_produksi ?>"
                                                                    name="panjang_produksi" class="form-control"
                                                                    placeholder="Masukkan Panjang" required>
                                                                <span class="input-group-text">meter</span>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Lebar (meter)</label>
                                                            <div class="input-group">
                                                                <input type="text" name="lebar_produksi" class="form-control"
                                                                    value="<?= $dt->lebar ?>" readonly>
                                                                <input type="text" value="<?= $dt->lebar_produksi ?>"
                                                                    name="lebar_produksi" class="form-control"
                                                                    placeholder="Masukkan Lebar" required>
                                                                <span class="input-group-text">meter</span>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Jumlah Produksi</label>
                                                        <input type="text" value="" name="qty_produksi" class="form-control"
                                                            required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Jumlah Gagal Bahan</label>
                                                        <input type="text" value="" name="qty_missing" class="form-control"
                                                            required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Keterangan / Alasan Gagal
                                                            Print</label>
                                                        <textarea name="alasan_missing" id=""
                                                            class="form-control"></textarea>
                                                    </div>
                                                </div>

                                                <!-- Kanan - Status, Konsumen, Operator -->
                                                <div class="col-md-6">


                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Nama Konsumen</label>
                                                        <input type="text" value="<?= $dt->konsumen_nama ?? '-' ?>"
                                                            class="form-control" readonly>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Mesin yang Digunakan</label>
                                                        <select name="mesin_id" id="mesin_id" class="form-select" required>
                                                            <option value="">-- Pilih Mesin --</option>
                                                            <?php foreach ($mesin as $m): ?>
                                                                <option value="<?= $m->mesin_id ?>"
                                                                    <?= $dt->mesin_id == $m->mesin_id ? 'selected' : '' ?>>
                                                                    <?= $m->mesin_nama ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <div class="form-text">Pilih mesin yang akan digunakan untuk proses
                                                            produksi</div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Lokasi Cetak / Produksi</label>
                                                        <select name="lokasi_cetak" id="lokasi_cetak" class="form-select"
                                                            required>
                                                            <option value="">-- Pilih Lokasi --</option>
                                                            <?php foreach ($lokasi_cetak as $l): ?>
                                                                <option value="<?= $l->gudang_id ?>"
                                                                    <?= $dt->lokasi_cetak == $l->gudang_id ? 'selected' : '' ?>>
                                                                    <?= $l->gudang_lokasi ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <div class="form-text">Pilih lokasi cetak yang akan digunakan untuk
                                                            proses produksi</div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">
                                                <i class="bi bi-save me-1"></i>Simpan
                                            </button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                <i class="bi bi-x-circle me-1"></i>Tutup
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>


                        <div class="modal fade" id="modalInvoice<?= $dt->produksi_id ?>" tabindex="-1"
                            aria-labelledby="modalInvoiceLabel<?= $dt->produksi_id ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="modalInvoiceLabel<?= $dt->produksi_id ?>">Invoice
                                            Produksi - #<?= $dt->nospk ?></h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <dl class="row mb-0">
                                                    <dt class="col-sm-5">Nama Konsumen</dt>
                                                    <dd class="col-sm-7"><?= $dt->konsumen_nama ?? '-' ?></dd>
                                                    <dt class="col-sm-5">Produk</dt>
                                                    <dd class="col-sm-7"><?= $dt->product_nama ?? '-' ?></dd>
                                                    <dt class="col-sm-5">Bahan</dt>
                                                    <dd class="col-sm-7"><?= $dt->bahan_nama ?? '-' ?></dd>
                                                    <dt class="col-sm-5">Qty</dt>
                                                    <dd class="col-sm-7"><?= $dt->qty ?? '-' ?></dd>
                                                </dl>
                                            </div>
                                            <div class="col-md-6">
                                                <dl class="row mb-0">
                                                    <dt class="col-sm-5">Tanggal</dt>
                                                    <dd class="col-sm-7">
                                                        <?= date_indo($dt->dateline_tgl) . ' ' . $dt->dateline_jam ?>
                                                    </dd>
                                                    <dt class="col-sm-5">Operator</dt>
                                                    <dd class="col-sm-7"><?= $dt->operator_nama ?? '-' ?></dd>
                                                    <dt class="col-sm-5">Status Produksi</dt>
                                                    <dd class="col-sm-7">
                                                        <?php
                                                        if ($dt->status_produksi == '0')
                                                            echo '<span class="badge bg-warning text-dark">Menunggu</span>';
                                                        elseif ($dt->status_produksi == '1')
                                                            echo '<span class="badge bg-info text-dark">Berjalan</span>';
                                                        elseif ($dt->status_produksi == '2')
                                                            echo '<span class="badge bg-danger">PB</span>';
                                                        else
                                                            echo '<span class="badge bg-success">Selesai</span>';
                                                        ?>
                                                    </dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </div>
</div>