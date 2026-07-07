<?php
// application/views/admin/warehouse/racks.php
?>

<!-- PREMIUM DESIGN SYSTEM - RACK MANAGEMENT -->
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #1a1c23 0%, #2c3e50 100%);
        --accent-blue: #6366f1;
        --slate-soft: #f8fafc;
        --slate-muted: #64748b;
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .warehouse-header-section {
        background: var(--primary-gradient);
        color: white;
        padding: 1.5rem 0 3.25rem 0;
        border-radius: 0 0 24px 24px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        position: relative;
        overflow: hidden;
    }

    .header-icon {
        width: 56px;
        height: 56px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        flex-shrink: 0;
    }

    .header-title {
        font-size: 1.6rem;
        font-weight: 900;
        margin: 0;
    }

    .stats-container {
        margin-top: -2rem;
        position: relative;
        z-index: 10;
        margin-bottom: 2rem;
    }

    .metric-card {
        background: white;
        border-radius: 24px;
        padding: 1.5rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .content-card {
        background: white;
        border-radius: 28px;
        padding: 2rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid #f8fafc;
        margin-bottom: 2rem;
    }

    .table-premium thead th {
        background: #f8fafc;
        border: none;
        color: var(--slate-muted);
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 1px;
        padding: 1.25rem 1.5rem;
    }

    .table-premium tbody td {
        padding: 1.25rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f8fafc;
    }

    .badge-rack {
        padding: 0.5rem 1rem;
        border-radius: 10px;
        font-weight: 800;
        font-size: 0.75rem;
        background: #eff6ff;
        color: #3b82f6;
    }

    .form-control-premium {
        background: #f8fafc;
        border: 2px solid #f1f5f9;
        border-radius: 14px;
        padding: 0.75rem 1rem;
        font-weight: 600;
    }
</style>

<div class="warehouse-header-section">
    <div class="container-fluid px-5">
        <div class="row align-items-center">
            <div class="col-md-7 d-flex align-items-center gap-4">
                <div class="header-icon">
                    <i class="fas fa-th"></i>
                </div>
                <div>
                    <p class="text-uppercase fw-bold opacity-50 mb-1 letter-spacing-1">Configuration</p>
                    <h1 class="header-title">Master Rak (Bin Locations)</h1>
                </div>
            </div>
            <div class="col-md-5 text-end h-100">
                <button class="btn btn-outline-light btn-premium rounded-pill px-4 me-2" data-bs-toggle="modal"
                    data-bs-target="#modalAddRak">
                    <i class="fas fa-plus me-2"></i>TAMBAH RAK BARU
                </button>
                <a href="<?= base_url('warehouse') ?>" class="btn btn-outline-light btn-premium rounded-pill px-4">
                    <i class="fas fa-arrow-left me-2"></i>DASHBOARD
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-5 pb-5">
    <div class="stats-container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="bg-primary text-white p-3 rounded-4">
                        <i class="fas fa-warehouse fa-lg"></i>
                    </div>
                    <div>
                        <p class="small text-muted mb-0 fw-bold text-uppercase">Total Gudang</p>
                        <h3 class="fw-900 mb-0">
                            <?= count($warehouses) ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="bg-info text-white p-3 rounded-4">
                        <i class="fas fa-th-large fa-lg"></i>
                    </div>
                    <div>
                        <p class="small text-muted mb-0 fw-bold text-uppercase">Total Rak Terdaftar</p>
                        <h3 class="fw-900 mb-0">
                            <?= count($racks) ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="bg-success text-white p-3 rounded-4">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                    <div>
                        <p class="small text-muted mb-0 fw-bold text-uppercase">Status Sistem</p>
                        <h3 class="fw-900 mb-0">OPTIMIZED</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-card">
        <div class="table-responsive">
            <table class="table table-premium align-middle mb-0" id="tableRacks">
                <thead>
                    <tr>
                        <th>LOKASI GUDANG</th>
                        <th>KODE RAK / NAMA</th>
                        <th>KETERANGAN</th>
                        <th class="text-center">ISIAN RAK</th>
                        <th class="text-center">STATUS</th>
                        <th class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($racks as $r): ?>
                        <tr>
                            <td>
                                <div class="fw-800 text-dark">
                                    <?= $r->gudang_nama ?>
                                </div>
                            </td>
                            <td>
                                <div class="badge-rack mb-1 d-inline-block">
                                    <?= $r->rak_kode ?>
                                </div>
                                <div class="fw-bold text-dark small">
                                    <?= $r->rak_nama ?>
                                </div>
                            </td>
                            <td class="small text-muted">
                                <?= $r->keterangan ?: '-' ?>
                            </td>
                            <td class="text-center">
                                <?php if ($r->total_items > 0): ?>
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-800"
                                        onclick="viewRackContents(<?= $r->rak_id ?>, '<?= $r->rak_kode ?>')">
                                        <i class="fas fa-boxes me-1"></i> <?= $r->total_items ?> Items
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted small italic">Kosong</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($r->is_active == 1): ?>
                                    <span
                                        class="badge bg-soft-success text-success px-3 py-2 rounded-pill small fw-800">ACTIVE</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted px-3 py-2 rounded-pill small fw-800">INACTIVE</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-light p-2 rounded-3 me-1"
                                    onclick="editRak(<?= htmlspecialchars(json_encode($r)) ?>)">
                                    <i class="fas fa-edit text-primary"></i>
                                </button>
                                <button class="btn btn-sm btn-light p-2 rounded-3" onclick="deleteRak(<?= $r->rak_id ?>)">
                                    <i class="fas fa-trash text-danger"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal View Contents -->
<div class="modal fade" id="modalViewContents" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header px-4 py-4" style="background: var(--primary-gradient); color: white;">
                <h5 class="modal-title fw-900"><i class="fas fa-boxes me-2"></i>Isi Material: <span id="view_rak_kode_title">-</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="rack_contents_loader" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted fw-bold">Mengambil data material...</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-premium align-middle mb-0" id="tableRackContents" style="display:none;">
                        <thead>
                            <tr>
                                <th class="ps-4">MATERIAL / VARIAN</th>
                                <th class="text-end">STOK</th>
                                <th>SATUAN</th>
                                <th class="text-center pe-4">STATUS</th>
                            </tr>
                        </thead>
                        <tbody id="rack_contents_body">
                            <!-- Data populated via AJAX -->
                        </tbody>
                    </table>
                </div>
                <div id="rack_empty_state" class="text-center py-5" style="display:none;">
                    <i class="fas fa-box-open fa-3x text-muted opacity-25 mb-3"></i>
                    <h6 class="fw-900 text-muted">Rak ini kosong</h6>
                </div>
            </div>
            <div class="modal-footer p-3 border-0 bg-light">
                <button type="button" class="btn btn-dark rounded-pill px-4 fw-800" data-bs-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add Rak -->
<div class="modal fade" id="modalAddRak" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form id="formAddRak">
                <div class="modal-header bg-dark text-white p-4">
                    <h5 class="modal-title fw-900 border-0"><i class="fas fa-plus-circle me-2"></i>Tambah Rak Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-800 text-muted text-uppercase">Penempatan Gudang</label>
                        <select name="gudang_id" class="form-select form-control-premium" required>
                            <?php foreach ($warehouses as $w): ?>
                                <option value="<?= $w->gudang_id ?>">
                                    <?= $w->gudang_nama ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-800 text-muted text-uppercase">Kode Rak</label>
                            <input type="text" name="rak_kode" class="form-control form-control-premium"
                                placeholder="Ex: RAK-A1" required>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label small fw-800 text-muted text-uppercase">Nama Rak</label>
                            <input type="text" name="rak_nama" class="form-control form-control-premium"
                                placeholder="Ex: Rak Sisi Timur" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-800 text-muted text-uppercase">Keterangan</label>
                        <textarea name="keterangan" class="form-control form-control-premium" rows="2"
                            placeholder="Detail lokasi atau spesifikasi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer p-4 border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-800"
                        data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-800">SIMPAN RAK</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Rak -->
<div class="modal fade" id="modalEditRak" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form id="formEditRak">
                <input type="hidden" name="rak_id" id="edit_rak_id">
                <div class="modal-header bg-primary text-white p-4">
                    <h5 class="modal-title fw-900"><i class="fas fa-edit me-2"></i>Edit Detail Rak</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-800 text-muted text-uppercase">Penempatan Gudang</label>
                        <select name="gudang_id" id="edit_gudang_id" class="form-select form-control-premium" required>
                            <?php foreach ($warehouses as $w): ?>
                                <option value="<?= $w->gudang_id ?>">
                                    <?= $w->gudang_nama ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-800 text-muted text-uppercase">Kode Rak</label>
                            <input type="text" name="rak_kode" id="edit_rak_kode"
                                class="form-control form-control-premium" required>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label small fw-800 text-muted text-uppercase">Nama Rak</label>
                            <input type="text" name="rak_nama" id="edit_rak_nama"
                                class="form-control form-control-premium" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-800 text-muted text-uppercase">Keterangan</label>
                        <textarea name="keterangan" id="edit_keterangan" class="form-control form-control-premium"
                            rows="2"></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-800 text-muted text-uppercase d-block">Status Aktif</label>
                        <div class="form-check form-switch p-0 ms-4">
                            <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active"
                                value="1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-4 border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-800"
                        data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-800">UPDATE DATA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#tableRacks').DataTable({
            "order": [[0, "asc"]],
            "pageLength": 10
        });

        // Add Rak Handler
        $('#formAddRak').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?= base_url("warehouse/ajax_add_rak") ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (res) {
                    if (res.status == 'success') {
                        Swal.fire('Berhasil', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                }
            });
        });

        // Update Rak Handler
        $('#formEditRak').on('submit', function (e) {
            e.preventDefault();
            const data = $(this).serializeArray();
            // Since checkbox isn't sent if unchecked
            let active = 0;
            data.forEach(item => { if (item.name == 'is_active') active = 1; });
            data.push({ name: 'is_active', value: active });

            $.ajax({
                url: '<?= base_url("warehouse/ajax_update_rak") ?>',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function (res) {
                    if (res.status == 'success') {
                        Swal.fire('Berhasil', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                }
            });
        });
    });

    function editRak(data) {
        $('#edit_rak_id').val(data.rak_id);
        $('#edit_gudang_id').val(data.gudang_id);
        $('#edit_rak_kode').val(data.rak_kode);
        $('#edit_rak_nama').val(data.rak_nama);
        $('#edit_keterangan').val(data.keterangan);
        $('#edit_is_active').prop('checked', data.is_active == 1);
        $('#modalEditRak').modal('show');
    }

    function deleteRak(id) {
        Swal.fire({
            title: 'Hapus Rak?',
            text: "Pastikan tidak ada stok yang terikat di rak ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url("warehouse/ajax_delete_rak/") ?>' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function (res) {
                        if (res.status == 'success') {
                            Swal.fire('Dihapus!', res.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    }
                });
            }
        });
    }

    function viewRackContents(rak_id, rak_kode) {
        $('#view_rak_kode_title').text(rak_kode);
        $('#modalViewContents').modal('show');
        $('#rack_contents_loader').show();
        $('#tableRackContents, #rack_empty_state').hide();
        
        $.ajax({
            url: '<?= base_url("warehouse/ajax_get_rack_contents/") ?>' + rak_id,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                $('#rack_contents_loader').hide();
                if (res.status == 'success' && res.data.length > 0) {
                    let html = '';
                    res.data.forEach(item => {
                        let statusClass = 'bg-soft-success text-success';
                        let statusLabel = 'AMAN';
                        
                        if (parseFloat(item.qty) <= 0) {
                            statusClass = 'bg-soft-danger text-danger';
                            statusLabel = 'HABIS';
                        } else if (parseFloat(item.qty) <= parseFloat(item.stok_min)) {
                            statusClass = 'bg-soft-warning text-warning';
                            statusLabel = 'LIMIT';
                        }
                        
                        html += `<tr>
                            <td class="ps-4">
                                <div class="fw-800 text-dark">${item.bahan_nama}</div>
                                <div class="small text-muted">${item.varian}</div>
                            </td>
                            <td class="text-end fw-900 fs-5 text-primary">${parseFloat(item.qty).toLocaleString()}</td>
                            <td class="fw-bold text-muted small uppercase">${item.satuan_nama}</td>
                            <td class="text-center pe-4">
                                <span class="badge-premium ${statusClass} py-1 px-3">${statusLabel}</span>
                            </td>
                        </tr>`;
                    });
                    $('#rack_contents_body').html(html);
                    $('#tableRackContents').show();
                } else {
                    $('#rack_empty_state').show();
                }
            },
            error: function() {
                $('#rack_contents_loader').hide();
                Swal.fire('Error', 'Gagal mengambil data isi rak.', 'error');
            }
        });
    }
</script>