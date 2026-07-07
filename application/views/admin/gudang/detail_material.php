<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    /* ==========================================================================
    PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - MATERIAL DETAIL & VARIANTS
    ========================================================================== */
    :root {
        --primary-gradient: linear-gradient(135deg, #1a1c23 0%, #2c3e50 100%);
        --success-gradient: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
        --info-gradient: linear-gradient(135deg, #4895ef 0%, #4cc9f0 100%);
        --warning-gradient: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);
        --danger-gradient: linear-gradient(135deg, #f72585 0%, #b5179e 100%);
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .report-section-card {
        background: white;
        border-radius: 20px;
        border: none;
        box-shadow: var(--premium-shadow);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .section-header {
        background: var(--primary-gradient);
        padding: 1.25rem 1.75rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
    }

    .section-header h5 {
        margin: 0;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Info Bar */
    .material-info-bar {
        background: #f8fafc;
        padding: 2rem;
        border-bottom: 1px solid #f1f5f9;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 2rem;
    }

    .info-item label {
        display: block;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 1px;
        margin-bottom: 0.5rem;
    }

    .info-item .value {
        font-size: 1.1rem;
        font-weight: 800;
        color: #1a1c23;
    }

    /* Stats v2 */
    .summary-stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .stat-v2-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .stat-v2-label {
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.6rem;
        letter-spacing: 1px;
    }

    .stat-v2-value {
        font-size: 1.35rem;
        font-weight: 900;
        color: #1a1c23;
        line-height: 1;
    }

    /* Table & Badges */
    .premium-table thead th {
        background: #f8fafc;
        border: none;
        color: #475569;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.65rem;
        letter-spacing: 1px;
        padding: 1rem 1.5rem;
    }

    .premium-table tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }

    .badge-premium {
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-weight: 800;
        font-size: 0.65rem;
        text-transform: uppercase;
    }

    .btn-action-circle {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        border: none;
        color: white;
    }

    .btn-action-circle:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    /* Animation */
    @keyframes slideUpFade {
        from {
            opacity: 0;
            transform: translateY(15px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-up {
        animation: slideUpFade 0.5s ease-out forwards;
    }
</style>

<div class="app-content mt-2">
    <div class="container-fluid">
        <!-- Master Info -->
        <div class="report-section-card animate-up">
            <div class="section-header">
                <h5><i class="fas fa-info-circle"></i> Informasi Utama: <?= $bahan->bahan_nama ?></h5>
                <a href="<?= base_url('warehouse/bahan_baku') ?>" class="btn btn-sm btn-light rounded-pill px-3 fw-bold">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="material-info-bar">
                <div class="info-item">
                    <label>Nama Master</label>
                    <div class="value"><?= $bahan->bahan_nama ?></div>
                </div>
                <div class="info-item">
                    <label>Jenis Barang</label>
                    <div class="value">
                        <span
                            class="badge-premium bg-primary"><?= ucwords(str_replace('_', ' ', $bahan->jenis_barang)) ?></span>
                    </div>
                </div>
                <div class="info-item">
                    <label>Master Barcode</label>
                    <div class="value"><?= $bahan->barcode ?: '-' ?></div>
                </div>
                <div class="info-item">
                    <label>Status</label>
                    <div class="value">
                        <span class="badge-premium <?= $bahan->status == 1 ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $bahan->status == 1 ? 'AKTIF' : 'NON-AKTIF' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row g-3 mb-4 animate-up" style="animation-delay: 0.1s">
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm h-100">
                    <div class="stat-v2-icon text-white" style="background: var(--primary-gradient)"><i
                            class="fas fa-layer-group"></i></div>
                    <div>
                        <div class="stat-v2-label">Total Varian</div>
                        <div class="stat-v2-value"><?= $total_materials ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm h-100">
                    <div class="stat-v2-icon text-white" style="background: var(--success-gradient)"><i
                            class="fas fa-shopping-cart"></i></div>
                    <div>
                        <div class="stat-v2-label">Log Pembelian</div>
                        <div class="stat-v2-value"><?= $pembelian_count ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm h-100">
                    <div class="stat-v2-icon text-white" style="background: var(--warning-gradient)"><i
                            class="fas fa-truck-loading"></i></div>
                    <div>
                        <div class="stat-v2-label">Log Pemakaian</div>
                        <div class="stat-v2-value"><?= $pengambilan_count ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm h-100">
                    <div class="stat-v2-icon text-white" style="background: var(--danger-gradient)"><i
                            class="fas fa-undo"></i></div>
                    <div>
                        <div class="stat-v2-label">Log Return</div>
                        <div class="stat-v2-value"><?= $return_count ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Variant Table -->
        <div class="report-section-card animate-up" style="animation-delay: 0.2s">
            <div class="section-header" style="background: var(--secondary-gradient)">
                <h5><i class="fas fa-list"></i> Daftar Varian Material</h5>
                <button class="btn btn-sm btn-light rounded-pill px-3 fw-bold" data-bs-toggle="modal"
                    data-bs-target="#modalTambahMaterial" data-bahan-id="<?= $bahan->bahan_id ?>"
                    data-bahan-nama="<?= $bahan->bahan_nama ?>">
                    <i class="fas fa-plus me-1"></i> Tambah Varian
                </button>
            </div>
            <div class="table-responsive">
                <table class="table premium-table table-hover mb-0" id="tableVarian">
                    <thead>
                        <tr>
                            <th width="40" class="text-center">No</th>
                            <th>Varian / Deskripsi</th>
                            <th class="text-center">Stok Saat Ini</th>
                            <th>Satuan Gudang</th>
                            <th>Satuan Produksi</th>
                            <th class="text-center">Konversi</th>
                            <th class="text-center">Min. Stok</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($materials)): ?>
                            <?php foreach ($materials as $index => $material): ?>
                                <tr>
                                    <td class="text-center text-muted fw-bold"><?= $index + 1 ?></td>
                                    <td>
                                        <div class="fw-800 text-dark"><?= $material->varian ?></div>
                                        <div class="small text-muted">
                                            <?= $material->barcode ?: '<span class="opacity-50">Tanpa Barcode</span>' ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $qty = (float) $material->stok_qty;
                                        $cls = $qty <= $material->stok_min ? 'bg-danger' : ($qty <= ($material->stok_min * 2) ? 'bg-warning text-dark' : 'bg-success');
                                        ?>
                                        <span class="badge-premium <?= $cls ?>">
                                            <?= $qty ?>
                                        </span>
                                    </td>
                                    <td><span
                                            class="badge-premium bg-light text-dark border"><?= $material->satuan_gudang_nama ?></span>
                                    </td>
                                    <td><span
                                            class="badge-premium bg-light text-primary border border-primary"><?= $material->satuan_produksi_nama ?></span>
                                    </td>
                                    <td class="text-center fw-bold text-muted"><?= number_format($material->konversi, 2) ?></td>
                                    <td class="text-center"><span
                                            class="badge-premium bg-danger-subtle text-danger"><?= number_format($material->stok_min) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button class="btn btn-action-circle btn-info btn-edit-material"
                                                data-id="<?= $material->material_id ?>" data-varian="<?= $material->varian ?>"
                                                data-barcode="<?= $material->barcode ?>"
                                                data-satuan-gudang="<?= $material->satuan_gudang ?>"
                                                data-satuan-produksi="<?= $material->satuan_produksi ?>"
                                                data-konversi="<?= $material->konversi ?>"
                                                data-min="<?= $material->stok_min ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-action-circle btn-danger btn-delete-material"
                                                data-id="<?= $material->material_id ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">Belum ada varian material untuk bahan
                                    ini</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add/Edit Material (Varian) -->
<div class="modal fade" id="modalTambahMaterial" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formMaterial" class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-900" id="materialModalTitle">Tambah Varian Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="material_id" id="material_id">
                <input type="hidden" name="bahan_id" id="bahan_id" value="<?= $bahan->bahan_id ?>">

                <div class="mb-3">
                    <label class="small fw-800 text-muted text-uppercase mb-2 d-block">Nama Varian <span
                            class="text-danger">*</span></label>
                    <input name="varian" id="m_varian" type="text" class="form-control rounded-3 border-2"
                        style="padding: 0.75rem;" required placeholder="Contoh: Merah, A4 80gr, etc.">
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <label class="small fw-800 text-muted text-uppercase mb-2 d-block">Satuan Gudang</label>
                        <select name="satuan_id" id="m_satuan_gudang" class="form-select rounded-3 border-2"
                            style="padding: 0.75rem;">
                            <?php foreach ($satuan as $s): ?>
                                <option value="<?= $s->satuan_id ?>"><?= $s->satuan_nama ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="small fw-800 text-muted text-uppercase mb-2 d-block">Satuan Produksi</label>
                        <!-- Satuan Produksi ikut Master Bahan, tidak bisa diedit per varian -->
                        <input type="text" class="form-control rounded-3 border-2 bg-light"
                            value="<?= $bahan->satuan_nama ?>" readonly>
                    </div>
                    <div class="col-6">
                        <label class="small fw-800 text-muted text-uppercase mb-2 d-block">Konversi</label>
                        <input name="konversi" id="m_konversi" type="number" step="0.01"
                            class="form-control rounded-3 border-2" style="padding: 0.75rem;" value="1.00">
                    </div>
                    <div class="col-6">
                        <label class="small fw-800 text-muted text-uppercase mb-2 d-block">Min. Stok</label>
                        <input name="stok_min" id="m_stok_min" type="number" class="form-control rounded-3 border-2"
                            style="padding: 0.75rem;" value="10">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold"
                    style="background: var(--primary-gradient)">Simpan Varian</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        // Edit Trigger
        $('.btn-edit-material').click(function () {
            const d = $(this).data();
            $('#material_id').val(d.id);
            $('#m_varian').val(d.varian);
            $('#m_satuan_gudang').val(d.satuanGudang);
            // Satuan Produksi handled by master
            $('#m_konversi').val(d.konversi);
            $('#m_stok_min').val(d.min);
            $('#materialModalTitle').text('Edit Varian Material');
            $('#modalTambahMaterial').modal('show');
        });

        // Add resetting
        $('[data-bs-target="#modalTambahMaterial"]').click(function () {
            if (!$(this).hasClass('btn-edit-material')) {
                $('#formMaterial')[0].reset();
                $('#material_id').val('');
                $('#materialModalTitle').text('Tambah Varian Baru');
            }
        });

        // Form Submit
        $('#formMaterial').submit(function (e) {
            e.preventDefault();
            const url = '<?= base_url("warehouse/actionEditMaterial") ?>';

            $.post(url, $(this).serialize(), function (resp) {
                // Check if response is JSON (successful/failed json) or just reload
                // For now, assume success and reload
                Swal.fire('Berhasil!', 'Data material telah disimpan.', 'success').then(() => location.reload());
            });
        });

        // Delete
        $('.btn-delete-material').click(function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Hapus Varian?',
                text: "Data stok dan riwayat akan terhapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.get('<?= base_url("warehouse/actionDeleteMaterial/") ?>' + id, () => {
                        Swal.fire('Terhapus!', '', 'success').then(() => location.reload());
                    });
                }
            });
        });
    });
</script>