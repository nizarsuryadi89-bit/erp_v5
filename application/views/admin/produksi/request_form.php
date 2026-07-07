<style>
    :root {
        --prod-gradient: linear-gradient(135deg, #0f172a 0%, #334155 100%);
        --accent: #3b82f6;
    }

    .header-prod {
        background: var(--prod-gradient);
        color: white;
        padding: 2rem 0 4rem 0;
        border-radius: 0 0 25px 25px;
        margin-bottom: -2.5rem;
    }

    .card-req {
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .btn-req {
        border-radius: 12px;
        padding: 0.8rem 1.5rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>

<div class="header-prod">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-900 mb-1">Pengajuan Material</h2>
                <p class="opacity-75 mb-0">Klik "+ Tambah" untuk ajukan stok ke Gudang Utama</p>
            </div>
            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-light rounded-pill px-4">
                <i class="fa fa-home me-2"></i>Dashboard
            </a>
        </div>
    </div>
</div>

<div class="container-fluid px-4 pb-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card card-req">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-900 mb-0"><i class="fa fa-list text-primary me-2"></i>Daftar Item Diambil</h5>
                        <button class="btn btn-primary btn-req btn-sm" data-bs-toggle="modal"
                            data-bs-target="#modalAdd">
                            <i class="fa fa-plus me-2"></i>Tambah Barang
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0">MATERIAL</th>
                                    <th class="text-center border-0">QTY</th>
                                    <th class="border-0">CATATAN</th>
                                    <th class="text-center border-0">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($temp_items)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="fa fa-shopping-basket fa-3x opacity-25 mb-3 d-block"></i>
                                            Belum ada item yang dipilih.
                                        </td>
                                    </tr>
                                <?php else:
                                    foreach ($temp_items as $t): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-800 text-dark">
                                                    <?= $t->bahan_nama ?>
                                                </div>
                                                <div class="small text-muted">
                                                    <?= $t->varian ?>
                                                </div>
                                            </td>
                                            <td class="text-center fw-900 text-primary">
                                                <?= number_format($t->qty) ?>
                                                <?= $t->satuan_nama ?>
                                            </td>
                                            <td class="small italic text-muted">
                                                <?= $t->keterangan ?: '-' ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= base_url('produksi_request/delete_item/' . $t->temp_id) ?>"
                                                    class="btn btn-sm btn-light text-danger rounded-pill">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (!empty($temp_items)): ?>
                        <form action="<?= base_url('produksi_request/finalize') ?>" method="post"
                            class="mt-4 pt-4 border-top">
                            <div class="row align-items-end g-3">
                                <div class="col-md-5">
                                    <label class="small fw-800 text-muted text-uppercase mb-2 d-block">Dikirim ke
                                        Gudang/Mesin:</label>
                                    <select name="gudang_id" class="form-select form-control-lg border-2" required>
                                        <option value="">-- Pilih Lokasi Produksi --</option>
                                        <?php foreach ($warehouses as $w):
                                            if ($w->gudang_id == 1)
                                                continue; ?>
                                            <option value="<?= $w->gudang_id ?>">
                                                <?= $w->gudang_nama ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-800 text-muted text-uppercase mb-2 d-block">Tanggal
                                        Pengambilan:</label>
                                    <input type="date" name="pengajuan_tgl" class="form-control form-control-lg border-2"
                                        value="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-success btn-req w-100 shadow-sm"
                                        onclick="return confirm('Kirim pengajuan sekarang?')">
                                        <i class="fa fa-paper-plane me-2"></i>Kirim
                                    </button>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-req">
                <div class="card-body p-4">
                    <h5 class="fw-900 mb-4"><i class="fa fa-history text-primary me-2"></i>Riwayat Terakhir</h5>
                    <div class="list-group list-group-flush">
                        <?php if (empty($recent_requests)): ?>
                            <div class="text-center py-4 text-muted small">Belum ada riwayat.</div>
                        <?php else:
                            foreach ($recent_requests as $r): ?>
                                <div class="list-group-item px-0 py-3 border-0 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-light text-dark rounded-pill">DOC#
                                            <?= str_pad($r->pengajuan_id, 5, '0', STR_PAD_LEFT) ?>
                                        </span>
                                        <?php if ($r->status == 0): ?>
                                            <span class="badge bg-warning text-dark px-2 rounded-pill">PENDING</span>
                                        <?php elseif ($r->status == 1): ?>
                                            <span class="badge bg-success text-white px-2 rounded-pill">PROCESSED</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger text-white px-2 rounded-pill">REJECTED</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="fw-800 text-dark small">
                                        <?= $r->gudang_nama ?>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.7rem;">
                                        <?= date('d M Y', strtotime($r->pengajuan_tgl)) ?> &bull;
                                        <?= $r->pengajuan_waktu ?>
                                    </div>
                                </div>
                            <?php endforeach; endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add -->
<div class="modal fade" id="modalAdd" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?= base_url('produksi_request/add_item') ?>" method="post"
            class="modal-content border-0 rounded-4">
            <div class="modal-header bg-primary text-white p-4 border-0">
                <h5 class="modal-title fw-900">Tambah Material</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="fw-bold small text-muted text-uppercase mb-2 d-block">Cari Nama Material</label>
                    <select name="material_id" class="form-select select2-modal" required>
                        <option value="">-- Pilih Item --</option>
                        <?php foreach ($materials as $m): ?>
                            <option value="<?= $m->material_id ?>">
                                <?= $m->bahan_nama ?> [
                                <?= $m->varian ?>]
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="fw-bold small text-muted text-uppercase mb-2 d-block">Jumlah (Qty)</label>
                    <input type="number" name="qty" class="form-control form-control-lg border-2" placeholder="0"
                        required>
                </div>
                <div class="mb-0">
                    <label class="fw-bold small text-muted text-uppercase mb-2 d-block">Catatan (Opsional)</label>
                    <textarea name="keterangan" class="form-control border-2" rows="2"
                        placeholder="Contoh: Untuk mesin A3+"></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="submit" class="btn btn-primary btn-req w-100">Tambahkan</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.select2-modal').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#modalAdd'),
            width: '100%'
        });
    });
</script>