<div class="app-content d-block">
    <div class="container-fluid py-3">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <a class="btn btn-dark" href="<?= base_url('product') ?>">Kembali</a>
                    <button disabled class="btn btn-dark">Duplikat Product</button>
                </div>
            </div>
            <div class="card-body">
                <section class="panel form-wizard" id="w2">
                    <form class="form-horizontal form-bordered" method="post"
                        action="<?= base_url('product/actionAdd') ?>">
                        <div class="tab-content">
                            <div id="w2-account" class="tab-pane active">

                                <!-- Kategori & Subkategori -->
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Kategori</label>
                                    <div class="col-md-5">
                                        <select data-plugin-selectTwo name="kategori_id" id="kategori_id"
                                            class="subkategori form-control" readonly>
                                            <?php foreach ($kategori as $k): ?>
                                                <option value="<?= $k->kategori_id ?>"
                                                    <?= ($k->kategori_id == $getProduct['kategori_id']) ? 'selected' : '' ?>>
                                                    <?= $k->kategori_nama ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-5">
                                        <select data-plugin-selectTwo name="subkategori_id" id="subkategori_id"
                                            class="subkategori form-control" readonly>
                                            <option value="0">-- Pilih Sub Kategori --</option>
                                            <?php foreach ($subkategori as $k): ?>
                                                <option value="<?= $k->subkategori_id ?>"
                                                    <?= ($k->subkategori_id == $getproduct['subkategori_id']) ? 'selected' : '' ?>>
                                                    <?= $k->subkategori_nama ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Nama Produk, Tipe, Satuan -->
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Nama <?= $controller ?></label>
                                    <div class="col-md-5">
                                        <input name="product_nama" class="form-control" id="product_nama" type="text"
                                            required placeholder="Nama Product"
                                            value="<?= $getproduct['product_nama'] ?>">
                                    </div>

                                    <div class="col-md-3">
                                        <select data-plugin-selectTwo name="tipe_id" id="tipe_id" class="form-control">
                                            <option value="">-- Pilih Satuan --</option>
                                            <?php foreach ($type as $k): ?>
                                                <option value="<?= $k->tipe_id ?>" <?= ($k->tipe_id == $getproduct['tipe_id']) ? 'selected' : '' ?>>
                                                    <?= $k->tipe_nama ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <select data-plugin-selectTwo name="satuan_id" id="satuan_id"
                                            class="form-control">
                                            <option value="">-- Pilih Satuan --</option>
                                            <?php foreach ($satuan as $k): ?>
                                                <option value="<?= $k->satuan_id ?>"
                                                    <?= ($k->satuan_id == $getproduct['satuan_id']) ? 'selected' : '' ?>>
                                                    <?= $k->satuan_nama ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Deskripsi -->
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Keterangan <?= $controller ?></label>
                                    <div class="col-md-10">
                                        <textarea name="product_deskripsi" id="product_deskripsi" class="form-control"
                                            rows="3" style="white-space: pre-wrap; font-family: lato;">
                                        <?= htmlspecialchars($getproduct['product_deskripsi'] ?? '') ?>
                                    </textarea>
                                    </div>
                                </div>

                                <!-- Tombol Submit -->
                                <div class="form-group text-center">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <a href="<?= base_url($controller . '/') ?>" class="btn btn-warning">Batal</a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>
            </div>

            <div class="card-body">
                <?php $this->load->view('admin/product/add_harga/universal_pricing'); ?>

            </div>
        </div>
        </section>

    </div>
</div>