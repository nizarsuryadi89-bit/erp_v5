<div class="app-content d-block">
    <div class="container-fluid py-3">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Tambah Return Pembelian (Simple Version)</h5>
                <a href="<?= base_url('warehouse/returnPembelian') ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <?php if($this->session->flashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $this->session->flashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $this->session->flashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Simple form untuk test add temp -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Tambah Item ke Daftar Return</h6>
                    </div>
                    <div class="card-body">
                        <form action="<?= base_url('warehouse/addTempReturn') ?>" method="post">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="bahan_id" class="form-label">Bahan <span class="text-danger">*</span></label>
                                        <select name="bahan_id" id="bahan_id" class="form-select" required>
                                            <option value="">Pilih Bahan</option>
                                            <?php foreach($this->db->get('tbl_bahan')->result() as $bahan): ?>
                                                <option value="<?= $bahan->bahan_id ?>"><?= $bahan->bahan_nama ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="qty_return" class="form-label">Qty Return <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="qty_return" name="qty_return" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="harga_return" class="form-label">Harga Return <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="harga_return" name="harga_return" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="alasan_return" class="form-label">Alasan Return <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="alasan_return" name="alasan_return" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="submit" class="btn btn-primary d-block w-100">
                                            <i class="bi bi-plus"></i> Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Daftar Return -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Daftar Return</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Bahan</th>
                                        <th>Qty Return</th>
                                        <th>Harga Return</th>
                                        <th>Alasan Return</th>
                                        <th>Sub Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $no = 1;
                                        $total_return = 0;
                                        if($temp) {
                                            foreach ($temp as $key => $value) {
                                                $subtotal = $value->qty_return * $value->harga_return;
                                                $total_return += $subtotal;
                                    ?>
                                    <tr>
                                        <td><?= $no ?></td>
                                        <td><?= $value->bahan_nama ?></td>
                                        <td><?= number_format($value->qty_return) ?></td>
                                        <td>Rp. <?= number_format($value->harga_return) ?></td>
                                        <td><?= $value->alasan_return ?></td>
                                        <td class="text-end">Rp. <?= number_format($subtotal) ?></td>
                                        <td>
                                            <a href="<?= base_url('warehouse/deleteTempReturn/'.$value->temp_id) ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Yakin ingin menghapus?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                                $no++;
                                            }
                                        }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-end">Total Return:</th>
                                        <th class="text-end">Rp. <?= number_format($total_return) ?></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Form untuk simpan return -->
                <?php if(!empty($temp)): ?>
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Simpan Return Pembelian</h6>
                    </div>
                    <div class="card-body">
                        <form action="<?= base_url('warehouse/actionAddReturnPembelian') ?>" method="post">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="supplier_id" class="form-label">Supplier <span class="text-danger">*</span></label>
                                        <select name="supplier_id" id="supplier_id" class="form-select" required>
                                            <option value="">Pilih Supplier</option>
                                            <?php foreach($this->db->get('tbl_supplier')->result() as $supplier): ?>
                                                <option value="<?= $supplier->supplier_id ?>"><?= $supplier->supplier_nama ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="return_tgl" class="form-label">Tanggal Return <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="return_tgl" name="return_tgl" value="<?= date('Y-m-d') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="no_return" class="form-label">No Return <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="no_return" name="no_return" placeholder="Masukkan nomor return" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="keterangan" class="form-label">Keterangan</label>
                                        <textarea class="form-control" id="keterangan" name="keterangan" rows="2" placeholder="Keterangan return"></textarea>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="return_total" value="<?= $total_return ?>">
                            <div class="text-end">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-save"></i> Simpan Return
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div> 