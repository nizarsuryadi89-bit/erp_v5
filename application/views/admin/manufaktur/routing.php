<div class="app-content d-block">
    <div class="container-fluid py-3">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="panel-title"><?= $title ?> : <?= $getproduct['product_nama'] ?></h2>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <p>Jumlah Data: <?= $total_rows ?></p>
                    </div>
                    <div class="col-sm-6 text-end">
                        <a href="<?= base_url('manufaktur')?>" class="btn btn-primary">Home</a>
                        <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalInputData">
                            <i class="fa fa-plus"></i> Tambah Routing
                        </button>
                    </div>
                </div>

                <table class="table table-bordered table-striped" id="datatable-detail">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Nama Product / Items</th>
                            <th width="15%">Nama Routing</th>
                            <!-- <th width="15%">Status</th> -->
                            <th width="15%">Operator</th>
                            <th width="25%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($tampilData)): ?>
                            <?php 
                            $no = 1;
                            foreach ($tampilData as $dt): 
                                $status = ($dt->is_active == '1') ? 'Aktif' : 'Tidak Aktif';
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $dt->product_nama ?></td>
                                    <td><strong><?= $dt->routing_name ?></strong></td>
                                    <!-- <td><?= $status ?></td> -->
                                    <td><?= $dt->operator_nama ?></td>
                                    <td class="text-center">
                                        <!-- <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditRouting" 
                                                data-id="<?= $dt->routing_id ?>" 
                                                data-product="<?= $dt->product_nama ?>"
                                                data-routing-name="<?= $dt->routing_name ?>"
                                                data-routing-urutan="<?= $dt->routing_urutan ?>"
                                                data-mesin-id="<?= $dt->operator_id ?>"
                                                data-status="<?= $dt->is_active ?>"
                                                data-durasi="<?= $dt->durasi ?>"
                                                data-keterangan="<?= $dt->keterangan ?>"
                                                data-is-finishing="<?= $dt->is_finishing ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button> -->
                                        <a href="<?= base_url('manufaktur/updaterouting/'.$dt->routing_id.'/'.$dt->product_id)?>" class="btn btn-primary btn-sm">Update Routing</a>
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalDeleteRouting" 
                                                data-id="<?= $dt->routing_id ?>" 
                                                data-product="<?= $dt->product_nama ?>"
                                                data-routing="<?= $dt->routing_name ?>">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Tidak ada data routing untuk product ini
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Routing -->
<div class="modal fade" id="modalEditRouting" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= base_url('manufaktur/aksi_update_routing_modal') ?>" method="post" id="formEditRouting">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Routing</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="routing_id" id="edit_routing_id">
                    <input type="hidden" name="product_id" value="<?= $getproduct['product_id'] ?>">
                    
                    <div class="mb-3">
                        <label for="edit_product_nama" class="form-label">Nama Product</label>
                        <input type="text" id="edit_product_nama" class="form-control" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_routing_urutan" class="form-label">Nomor Urut <span class="text-danger">*</span></label>
                        <input type="number" name="routing_urutan" id="edit_routing_urutan" class="form-control" required min="1">
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_routing_name" class="form-label">Nama Routing <span class="text-danger">*</span></label>
                        <input type="text" name="routing_name" id="edit_routing_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_is_finishing" class="form-label">Tipe Tahapan <span class="text-danger">*</span></label>
                        <select name="is_finishing" id="edit_is_finishing" class="form-control" required>
                            <option value="0">Cetak / Produksi Normal</option>
                            <option value="1">Finishing / QC / Packing</option>
                        </select>
                        <small class="text-muted">Pilih "Finishing" agar tahapan ini masuk ke antrian tab Finishing.</small>
                    </div>
                     <div class="mb-3">
                        <label for="edit_bom_line_id" class="form-label">Built Of Material <span class="text-danger">*</span></label>
                        <select name="bom_line_id" id="edit_bom_line_id" class="form-control" required>
                            <option value="">Pilih BOM</option>
                            <?php foreach ($bom as $m): ?>
                                <option value="<?= $m->bom_line_id ?>"><?= $m->bahan_id ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_operator_id" class="form-label">Operator <span class="text-danger">*</span></label>
                        <select name="operator_id" id="operator_id" class="form-control" required>
                            <option value="">Pilih Operator</option>
                            <?php foreach ($operator_list as $m): ?>
                                <option value="<?= $m->operator_id ?>"><?= $m->operator_nama ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Delete Routing -->
<div class="modal fade" id="modalDeleteRouting" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus routing ini?</p>
                <div class="alert alert-warning">
                    <strong>Product:</strong> <span id="delete_product_nama"></span><br>
                    <strong>Routing:</strong> <span id="delete_routing_nama"></span>
                </div>
            </div>
            <div class="modal-footer">
                <form action="<?= base_url('manufaktur/aksi_delete_routing_modal') ?>" method="post" style="display: inline;">
                    <input type="hidden" name="routing_id" id="delete_routing_id">
                    <input type="hidden" name="product_id" value="<?= $getproduct['product_id'] ?>">
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>

<!-- Script untuk Modal -->
<script>
$(document).ready(function() {
    // Modal Edit
    $('#modalEditRouting').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var routing_id = button.data('id');
        var product_nama = button.data('product');
        var routing_name = button.data('routing-name');
        var routing_urutan = button.data('routing-urutan');
        var operator_id = button.data('operator-id');
        var status = button.data('status');
        var durasi = button.data('durasi');
        var keterangan = button.data('keterangan');
        
        var modal = $(this);
        modal.find('#edit_routing_id').val(routing_id);
        modal.find('#edit_product_nama').val(product_nama);
        modal.find('#edit_routing_name').val(routing_name);
        modal.find('#edit_routing_urutan').val(routing_urutan);
        modal.find('#edit_operator_id').val(operator_id);
        modal.find('#edit_durasi').val(durasi);
        modal.find('#edit_keterangan').val(keterangan);
        modal.find('#edit_is_active').prop('checked', status == '1');
        // Fetch routing data for is_finishing from the row if available, otherwise default to 0
        var is_finishing = button.data('is-finishing') || '0';
        modal.find('#edit_is_finishing').val(is_finishing);
    });
    
    // Modal Delete
    $('#modalDeleteRouting').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var routing_id = button.data('id');
        var product_nama = button.data('product');
        var routing_nama = button.data('routing');
        
        var modal = $(this);
        modal.find('#delete_routing_id').val(routing_id);
        modal.find('#delete_product_nama').text(product_nama);
        modal.find('#delete_routing_nama').text(routing_nama);
    });

    // Add form validation
    $('#formEditRouting').on('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        $(this).addClass('was-validated');
    });
});
</script>

<!-- Modal Tambah Routing -->
<div class="modal fade" id="modalInputData" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= base_url('manufaktur/action_add_routing') ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Routing</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="product_id" value="<?= $getproduct['product_id'] ?>">
                    <div class="mb-3">
                        <label for="routing_urutan" class="form-label">Nomor Urut <span class="text-danger">*</span></label>
                        <input type="number" name="routing_urutan" id="routing_urutan" class="form-control" required min="1" value="<?= $next_routing_number ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="routing_name" class="form-label">Nama Routing</label>
                        <select name="routing_name" id="routing_name" class="form-control" required>
                            <!-- <option value="">-- Pilih Routing --</option> -->
                            <?php foreach ($routing_list as $routing): ?>
                                <option value="<?= $routing->nama_routing ?>"><?= $routing->nama_routing ?></option>
                            <?php endforeach; ?>
                        </select>
                        <!-- <input type="text" name="routing_name" id="routing_name" class="form-control" required> -->
                    </div>
                    <div class="mb-3">
                        <label for="is_finishing" class="form-label">Tipe Tahapan <span class="text-danger">*</span></label>
                        <select name="is_finishing" id="is_finishing" class="form-control" required>
                            <option value="0">Cetak / Produksi Normal</option>
                            <option value="1">Finishing / QC / Packing</option>
                        </select>
                        <small class="text-muted">Pilih "Finishing" agar tahapan ini masuk ke antrian tab Finishing.</small>
                    </div>

                    <div class="mb-3">
                        <label for="mesin_id" class="form-label">Operator</label>
                        <select name="mesin_id" id="mesin_id" class="form-control">
                            <?php foreach ($operator_list as $m): ?>
                                <option value="<?= $m->operator_id ?>">
                                    <?= $m->operator_nama ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- <div class="mb-3">
                        <label for="durasi" class="form-label">Durasi (menit)</label>
                        <input type="number" name="durasi" id="durasi" class="form-control" required>
                    </div> -->

                    <!-- <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control"></textarea>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1">
                        <label class="form-check-label" for="is_active">Aktif</label>
                    </div> -->
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Add this to existing script
    function updateRoutingNumber() {
        $.ajax({
            url: '<?= base_url('manufaktur/get_next_routing_number_ajax') ?>',
            type: 'POST',
            data: {
                product_id: <?= json_encode((string) $getproduct['product_id']) ?>
            },
            dataType: 'json',
            success: function(response) {
                $('#routing_urutan').val(response.next_number);
            }
        });
    }

    // Update number when modal opens
    $('#modalInputData').on('show.bs.modal', function() {
        updateRoutingNumber();
    });
});
</script>
