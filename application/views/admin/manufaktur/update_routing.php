<div class="container-fluid">
    <section class="card">
        <header class="card-header d-flex justify-content-between align-items-center">
            <a class="btn btn-dark" href="<?= base_url('manufaktur/routing/'.$segment4) ?>">Kembali</a>
            <button disabled class="btn btn-dark"><?= $title ?></button>
        </header>
        <div class="card-body">
            <section class="form-wizard" id="w2">
                <form action="<?= base_url('manufaktur/action_update_routing') ?>" method="post">
                    
                    <!-- Routing Name -->
                    <div class="mb-3">
                        <label for="routing_name" class="form-label">Routing Name</label>
                        <input type="hidden" name="routing_id" id="routing_id" class="form-control" value="<?= $routing['routing_id'] ?>" required>
                        <input type="hidden" name="product_id" id="product_id" class="form-control" value="<?= $routing['product_id'] ?>" required>
                        <input type="text" name="routing_name" id="routing_name" class="form-control" value="<?= $routing['routing_name'] ?>" required>
                        <!-- <small class="form-text text-muted">Masukkan nama routing yang menggambarkan urutan proses produksi.</small> -->
                    </div>

                    <div class="mb-3">
                        <label for="is_finishing" class="form-label">Tipe Tahapan</label>
                        <select name="is_finishing" id="is_finishing" class="form-select" required>
                            <option value="0" <?= ($routing['is_finishing'] == 0) ? 'selected' : '' ?>>Cetak / Produksi Normal</option>
                            <option value="1" <?= ($routing['is_finishing'] == 1) ? 'selected' : '' ?>>Finishing / QC / Packing</option>
                        </select>
                        <small class="form-text text-muted">Pilih "Finishing" agar tahapan ini masuk ke antrian tab Finishing pada WIP.</small>
                    </div>

                    <!-- Product Selection -->
                    

                    <!-- Routing Urutan -->
                    <div class="mb-3">
                        <label for="routing_urutan" class="form-label">Routing Urutan</label>
                        <input type="number" name="routing_urutan" id="routing_urutan" class="form-control" value="<?= $routing['routing_urutan'] ?>" required min="1">
                        <!-- <small class="form-text text-muted">Masukkan nomor urut langkah produksi dalam proses routing. Ini menentukan urutan pemrosesan produk.</small> -->
                    </div>

                    <!-- Mesin Selection -->
                    <div class="mb-3">
                        <label for="operator_id" class="form-label">Operator</label>
                        <select name="operator_id" id="operator_id" class="form-select">
                            <?php foreach ($operator_list as $m): ?>
                                <option value="<?= $m->operator_id ?>" <?= ($m->operator_id == $routing['operator_id']) ? 'selected' : '' ?>>
                                    <?= $m->operator_nama ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <!-- <small class="form-text text-muted">Pilih mesin yang digunakan untuk langkah ini dalam proses produksi.</small> -->
                    </div>
                    
                    <div class="mb-3">
                        <label for="bom_line_id" class="form-label">Built Of Material</label>
                        <select name="bom_line_id" id="bom_line_id" class="form-select">
                            <option value="0">Tanpa Bahan Produksi / Hanya Jasa</option>
                            <?php foreach ($bom_list as $m): ?>
                                <option value="<?= $m->bom_line_id ?>" <?= ($m->bom_line_id == $routing['bom_line_id']) ? 'selected' : '' ?>>
                                    <?= $m->bahan_nama ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <!-- <small class="form-text text-muted">Pilih mesin yang digunakan untuk langkah ini dalam proses produksi.</small> -->
                    </div>
                  
                    

                    <!-- Status Aktif -->
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" <?= ($routing['is_active']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Aktif</label>
                        <small class="form-text text-muted">Centang jika routing ini aktif dan digunakan dalam proses produksi saat ini.</small>
                    </div>

                    <input type="hidden" name="created_at" value="<?= $routing['created_at'] ?>">

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Update Routing</button>
                    </div>
                </form>
            </section>
        </div>
    </section>
</div>
