<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    /* ==========================================================================
    PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - OUTGOING TRANSACTION (PRODUCTION)
    ========================================================================== */
    :root {
        --primary-gradient: linear-gradient(135deg, #1a1c23 0%, #2c3e50 100%);
        --success-gradient: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
        --info-gradient: linear-gradient(135deg, #4895ef 0%, #4cc9f0 100%);
        --warning-gradient: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);
        --danger-gradient: linear-gradient(135deg, #f72585 0%, #b5179e 100%);
        --premium-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }

    /* Transfer Flow Visualization */
    .transfer-bridge {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        display: flex;
        align-items: center;
        gap: 2rem;
        margin-bottom: 2rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid #f1f5f9;
    }

    .bridge-node {
        flex: 1;
        padding: 1.5rem;
        border-radius: 16px;
        text-align: center;
        position: relative;
    }

    .node-source { background: #f8fafc; border: 2px solid #e2e8f0; }
    .node-dest { background: #f0fdf4; border: 2px solid #dcfce7; }

    .node-label {
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 1px;
        margin-bottom: 0.5rem;
        display: block;
    }

    .node-name { font-size: 1.1rem; font-weight: 900; color: #1a1c23; }

    .bridge-arrow {
        width: 48px;
        height: 48px;
        background: var(--primary-gradient);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        z-index: 2;
    }

    /* Section Cards */
    .report-section-card {
        background: white;
        border-radius: 20px;
        border: none;
        box-shadow: var(--premium-shadow);
        overflow: hidden;
        height: 100%;
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
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Premium Table */
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
        padding: 1.25rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }

    /* Form Styles */
    .form-control-premium {
        border: 2px solid #f1f5f9;
        border-radius: 12px;
        padding: 0.75rem 1.25rem;
        font-weight: 600;
        background: #f8fafc;
    }

    .form-label-premium {
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 1px;
        margin-bottom: 0.5rem;
        display: block;
    }

    .btn-premium {
        padding: 0.75rem 2rem;
        border-radius: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .btn-premium-primary { background: var(--primary-gradient); color: white; }
    .btn-premium-success { background: var(--success-gradient); color: white; }

    .qty-pill {
        background: rgba(72, 149, 239, 0.1);
        color: #4895ef;
        padding: 0.3rem 0.75rem;
        border-radius: 8px;
        font-weight: 900;
    }

    /* Animation */
    @keyframes slideUpFade {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-up { animation: slideUpFade 0.5s ease-out forwards; }
</style>

<div class="app-content mt-2">
    <div class="container-fluid">
        <!-- Visual Bridge -->
        <div class="transfer-bridge animate-up">
            <div class="bridge-node node-source">
                <span class="node-label">Sumber Persediaan</span>
                <div class="node-name"><i class="fas fa-warehouse me-2 text-primary"></i>Gudang Utama (Stok)</div>
            </div>
            <div class="bridge-arrow"><i class="fas fa-arrow-right"></i></div>
            <div class="bridge-node node-dest">
                <span class="node-label">Tujuan Distribusi</span>
                <div class="node-name"><i class="fas fa-industry me-2 text-success"></i>Unit Produksi / WIP</div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left: Picklist -->
            <div class="col-lg-8">
                <div class="report-section-card animate-up" style="animation-delay: 0.1s">
                    <div class="section-header">
                        <h5><i class="fas fa-clipboard-list"></i> Picklist Material (Transfer List)</h5>
                        <button class="btn btn-sm btn-light rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahBahan">
                            <i class="fas fa-plus me-1"></i> Tambah Item
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table premium-table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th width="40" class="text-center">No</th>
                                    <th>Deskripsi Material</th>
                                    <th class="text-center">Qty Transfer</th>
                                    <th class="text-center">Satuan</th>
                                    <th width="100" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($temp)): ?>
                                    <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada item yang dipilih untuk ditransfer.</td></tr>
                                <?php else: $no = 1; foreach ($temp as $dt): ?>
                                    <tr>
                                        <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                                        <td>
                                            <div class="fw-800 text-dark"><?= $dt->bahan_nama ?></div>
                                            <div class="small text-muted">ID: #<?= $dt->bahan_id ?></div>
                                        </td>
                                        <td class="text-center"><span class="qty-pill"><?= number_format($dt->qty) ?></span></td>
                                        <td class="text-center text-muted small fw-bold"><?= $dt->satuan_nama ?></td>
                                        <td class="text-center">
                                            <a href="<?= base_url('warehouse/deleteTempAmbil/' . $dt->temp_id) ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right: Dispatcher -->
            <div class="col-lg-4">
                <div class="report-section-card animate-up" style="animation-delay: 0.2s">
                    <div class="section-header" style="background: var(--secondary-gradient)">
                        <h5><i class="fas fa-paper-plane"></i> Dispatcher Control</h5>
                    </div>
                    <div class="p-4">
                        <?= form_open('warehouse/actionAddpengambilan') ?>
                            <div class="mb-4">
                                <label class="form-label-premium">Tanggal Dispatch</label>
                                <input type="date" class="form-control form-control-premium" name="pengambilan_tgl" required value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="mb-4">
                                <label class="form-label-premium">Petugas Pelaksana</label>
                                <select class="form-select form-control-premium" name="karyawan_id" required>
                                    <option value="">-- Pilih Petugas --</option>
                                    <?php foreach ($karyawan as $b): ?><option value="<?= $b->karyawan_id ?>"><?= $b->nama_lengkap ?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-5">
                                <label class="form-label-premium">Gudang Tujuan</label>
                                <select class="form-select form-control-premium" name="gudang_id" required>
                                    <option value="">-- Pilih Lokasi Target --</option>
                                    <?php foreach ($gudang as $b): ?><option value="<?= $b->gudang_id ?>"><?= $b->gudang_nama ?></option><?php endforeach; ?>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn-premium btn-premium-primary w-100 py-3" <?= empty($temp) ? 'disabled' : '' ?>>
                                <i class="fas fa-rocket"></i> Launch Distribution
                            </button>
                        <?= form_close() ?>
                        
                        <div class="mt-4 p-3 rounded-4 bg-light border border-dashed border-primary">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fas fa-shield-alt text-primary fa-lg"></i>
                                <div>
                                    <span class="d-block fw-900 text-dark small">Safe-Check Enabled</span>
                                    <span class="d-block text-muted" style="font-size: 0.65rem;">Stok akan diverifikasi otomatis saat pengiriman diluncurkan.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Staging Material -->
<div class="modal fade" id="modalTambahBahan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 rounded-4 shadow-lg" method="post" action="<?= base_url('warehouse/action_add_barang_keluar') ?>">
            <div class="modal-header border-0 bg-dark text-white p-4">
                <h5 class="modal-title fw-900"><i class="fas fa-layer-group me-2"></i>Stage Material</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <label class="form-label-premium text-dark">Pilih Material (Stok Tersedia)</label>
                    <select class="form-select form-control-premium select2-bahan" id="select_material" required>
                        <option value="">-- Cari Nama Material --</option>
                        <?php foreach ($material_ready as $m): ?>
                            <option value="<?= $m->material_id ?>" data-bahan="<?= $m->bahan_id ?>" data-max="<?= $m->jumlah_stok ?>">
                                <?= $m->bahan_nama ?> [<?= $m->varian ?>] - Tersedia: <?= number_format($m->jumlah_stok, 2) ?> <?= $m->satuan_nama ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="material_id" id="post_material_id">
                    <input type="hidden" name="bahan_id" id="post_bahan_id">
                </div>

                <div class="mb-4">
                    <label class="form-label-premium text-dark">Kuantitas Transfer</label>
                    <input type="number" step="0.01" class="form-control form-control-premium" name="qty" id="qty" min="0.01" required placeholder="0.00">
                    <div id="maxHint" class="small text-muted mt-1" style="display:none">Maksimal: <span id="textMax">0</span></div>
                </div>

                <div class="mb-0">
                    <label class="form-label-premium text-dark">Catatan / Remarks</label>
                    <textarea class="form-control form-control-premium" name="keterangan" rows="2" placeholder="Tulis catatan distribusi jika diperlukan..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" style="background: var(--primary-gradient)">Masukkan List</button>
            </div>
        </form>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-bahan').select2({ theme: 'bootstrap-5', dropdownParent: $('#modalTambahBahan'), placeholder: '-- Cari Nama Material --' });

        $('#select_material').on('change', function() {
            const opt = $(this).find(':selected');
            if(opt.val()) {
                $('#post_material_id').val(opt.val());
                $('#post_bahan_id').val(opt.data('bahan'));
                $('#textMax').text(opt.data('max'));
                $('#qty').attr('max', opt.data('max'));
                $('#maxHint').fadeIn();
            } else {
                $('#maxHint').hide();
                $('#post_material_id').val('');
                $('#post_bahan_id').val('');
            }
        });
    });
</script>