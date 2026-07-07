<?php
// application/views/admin/warehouse/requests.php
?>

<!-- PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - WAREHOUSE MATERIAL REQUESTS -->
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #1a1c23 0%, #2c3e50 100%);
        --success-gradient: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
        --info-gradient: linear-gradient(135deg, #4895ef 0%, #4cc9f0 100%);
        --warning-gradient: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);
        --danger-gradient: linear-gradient(135deg, #f72585 0%, #b5179e 100%);
        --slate-soft: #f8fafc;
        --slate-muted: #64748b;
        --slate-dark: #1e293b;
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        --accent-blue: #6366f1;
    }

    body {
        background-color: #f8fafc;
        /* font-family: 'Inter', system-ui, -apple-system, sans-serif; */
    }

    /* Standard Header */
    .warehouse-header-section {
        background: var(--primary-gradient);
        color: white;
        padding: 1.5rem 0 3.25rem 0;
        margin-bottom: 0;
        border-radius: 0 0 24px 24px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        position: relative;
        overflow: hidden;
    }

    .warehouse-header-section::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -5%;
        width: 280px;
        height: 280px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        z-index: 1;
    }

    .header-content,
    .header-actions {
        position: relative;
        z-index: 10;
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 1.25rem;
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
        letter-spacing: -0.5px;
    }

    .header-subtitle {
        font-size: 0.7rem;
        font-weight: 600;
        margin: 0 0 2px 0;
        opacity: 0.7;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #94a3b8;
    }

    /* Step Badge */
    .step-indicator {
        background: white;
        border-radius: 28px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-top: -2rem;
        margin-bottom: 2rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid rgba(255, 255, 255, 0.8);
        position: relative;
        z-index: 20;
    }

    .status-bridge {
        flex: 1;
        padding: 1.5rem;
        border-radius: 20px;
        text-align: center;
        background: #f8fafc;
        border: 2px solid #f1f5f9;
    }

    .status-bridge.active {
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .status-label {
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--slate-muted);
        letter-spacing: 1.5px;
        margin-bottom: 0.5rem;
        display: block;
    }

    .status-val {
        font-size: 1.25rem;
        font-weight: 900;
        color: var(--slate-dark);
    }

    /* Content Area */
    .content-card {
        background: white;
        border-radius: 28px;
        padding: 2.25rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid #f1f5f9;
        margin-bottom: 2rem;
    }

    .card-title-premium {
        font-size: 1.25rem;
        font-weight: 900;
        color: var(--slate-dark);
        margin-bottom: 1.75rem;
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    /* Custom Form Elements */
    .form-control-premium {
        background: #f8fafc;
        border: 2px solid #f1f5f9;
        border-radius: 14px;
        padding: 0.85rem 1.25rem;
        font-weight: 600;
        transition: all 0.25s;
    }

    .form-control-premium:focus {
        background: white;
        border-color: var(--accent-blue);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .btn-premium {
        border-radius: 50px;
        padding: 0.45rem 1.1rem;
        font-weight: 700;
        font-size: 0.72rem;
        letter-spacing: 0.5px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .btn-premium-primary {
        background: var(--primary-gradient);
        border: none;
        color: white !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .btn-premium-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    /* Table Styles */
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
        color: var(--slate-dark);
    }

    .badge-status-pending {
        background: #fef3c7;
        color: #d97706;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.7rem;
        text-transform: uppercase;
    }

    .badge-status-approved {
        background: #dcfce7;
        color: #166534;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.7rem;
        text-transform: uppercase;
    }

    .badge-status-rejected {
        background: #fee2e2;
        color: #991b1b;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.7rem;
        text-transform: uppercase;
    }
</style>

<div class="warehouse-header-section">
    <div class="container-fluid px-5">
        <div class="row align-items-start">
            <div class="col-md-7">
                <div class="header-content">
                    <div class="header-icon">
                        <i class="fa fa-file-signature"></i>
                    </div>
                    <div class="header-text">
                        <p class="header-subtitle">Request Control System</p>
                        <h1 class="header-title">Pengajuan Material Produksi</h1>
                    </div>
                </div>
            </div>
            <div class="col-md-5 text-end header-actions d-flex justify-content-end align-items-center gap-2">
                <a href="<?= base_url('warehouse') ?>" class="btn btn-outline-light btn-premium rounded-pill px-4">
                    <i class="fa fa-arrow-left me-2"></i>DASHBOARD
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-5">
    <!-- Quick Stats -->
    <div class="step-indicator">
        <div class="status-bridge active">
            <span class="status-label">Menunggu Verifikasi</span>
            <div class="status-val text-danger">
                <?= count(array_filter($recent_requests, function ($r) {
                    return $r->status == 0;
                })) ?> Pengajuan
            </div>
        </div>
        <div class="status-bridge">
            <span class="status-label">Total Permintaan</span>
            <div class="status-val text-dark">
                <?= count($recent_requests) ?> Dokumen
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- New Request Form / Items List -->
        <div class="col-lg-8">
            <div class="content-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title-premium mb-0"><i class="fa fa-list-check text-primary"></i> Daftar Item
                        Pengajuan
                    </h5>
                    <button class="btn btn-premium btn-premium-primary" data-bs-toggle="modal"
                        data-bs-target="#modalAddItem">
                        <i class="fa fa-plus me-2"></i>Tambah Barang
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-premium mb-0">
                        <thead>
                            <tr>
                                <th width="60">NO</th>
                                <th>DETAIL MATERIAL</th>
                                <th class="text-center">QTY</th>
                                <th class="text-center">CATATAN</th>
                                <th class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($temp_items)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="opacity-25 mb-3"><i class="fa fa-shopping-cart fa-4x text-danger"></i>
                                        </div>
                                        <h6 class="fw-900 text-muted">Daftar item masih kosong</h6>
                                        <p class="text-muted small">Silakan tambahkan material yang ingin diajukan ke
                                            gudang.</p>
                                    </td>
                                </tr>
                            <?php else:
                                $no = 1;
                                foreach ($temp_items as $tmp): ?>
                                    <tr>
                                        <td class="text-muted fw-bold">
                                            <?= $no++ ?>
                                        </td>
                                        <td>
                                            <div class="fw-800 text-dark">
                                                <?= $tmp->bahan_nama ?>
                                            </div>
                                            <div class="small text-muted">
                                                <?= $tmp->varian ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-soft-blue text-primary px-3 py-2 rounded-pill fw-900">
                                                <?= number_format($tmp->qty) ?>
                                                <?= $tmp->satuan_nama ?>
                                            </span>
                                        </td>
                                        <td class="text-center italic small text-muted">
                                            <?= $tmp->keterangan ?: '-' ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url('warehouse/requests_delete_item/' . $tmp->temp_id) ?>"
                                                class="btn btn-sm btn-light border text-danger rounded-pill px-3">
                                                <i class="fa fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (!empty($temp_items)): ?>
                    <form action="<?= base_url('warehouse/requests_finalize') ?>" method="post"
                        class="mt-5 pt-4 border-top">
                        <div class="row align-items-end g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-800 text-muted uppercase">Gudang Tujuan <span
                                        class="text-danger">*</span></label>
                                <select name="gudang_id" class="form-select form-control-premium" required>
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
                                <label class="form-label small fw-800 text-muted uppercase">Tanggal Pengajuan</label>
                                <input type="date" name="pengajuan_tgl" class="form-control form-control-premium"
                                    value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-premium btn-premium-primary w-100 py-3">
                                    <i class="fa fa-paper-plane me-2"></i>Kirim Pengajuan
                                </button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent History -->
        <div class="col-lg-4">
            <div class="content-card">
                <h5 class="card-title-premium"><i class="fa fa-history text-dark"></i> Riwayat Terbaru</h5>
                <div class="list-group list-group-flush">
                    <?php if (empty($recent_requests)): ?>
                        <div class="text-center py-4">
                            <p class="text-muted small">Belum ada riwayat pengajuan.</p>
                        </div>
                    <?php else:
                        foreach ($recent_requests as $req): ?>
                            <div class="list-group-item px-0 py-3 bg-transparent border-bottom">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <div class="fw-900 text-dark" style="font-size: 0.9rem;">DOC#
                                            <?= str_pad($req->pengajuan_id, 5, '0', STR_PAD_LEFT) ?>
                                        </div>
                                        <div class="small text-muted">
                                            <?= date('d M Y', strtotime($req->pengajuan_tgl)) ?> &bull;
                                            <?= $req->gudang_nama ?>
                                        </div>
                                    </div>
                                    <?php if ($req->status == 0): ?>
                                        <span class="badge-status-pending">Pending</span>
                                    <?php elseif ($req->status == 1): ?>
                                        <span class="badge-status-approved">SUDAH DIPROSES</span>
                                    <?php else: ?>
                                        <span class="badge-status-rejected">DITOLAK</span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($req->status == 0): ?>
                                    <div class="d-flex align-items-center gap-2 mt-2">
                                        <button onclick="viewDetail(<?= $req->pengajuan_id ?>)"
                                            class="btn btn-sm btn-light border rounded-pill flex-grow-1 fw-800">
                                            <i class="fa fa-eye me-1"></i>
                                        </button>
                                        <button onclick="processRequest(<?= $req->pengajuan_id ?>)"
                                            class="btn btn-sm btn-success rounded-pill px-3 fw-800">
                                            <i class="fa fa-check me-1"></i> VERIFY
                                        </button>
                                        <a href="<?= base_url('warehouse/requests_reject/' . $req->pengajuan_id) ?>"
                                            onclick="return confirm('Tolak pengajuan ini?')"
                                            class="btn btn-sm btn-outline-danger rounded-pill px-2 fw-800">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </div>
                                <?php elseif ($req->status == 1): ?>
                                    <div class="d-flex align-items-center gap-2 mt-2">
                                        <button class="btn btn-sm btn-outline-success rounded-pill flex-grow-1 fw-800" disabled>
                                            <i class="fa fa-check-double me-1"></i> SUDAH DIPROSES
                                        </button>
                                        <button onclick="viewDetail(<?= $req->pengajuan_id ?>)"
                                            class="btn btn-sm btn-light border rounded-pill fw-800 text-primary">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <div class="d-flex align-items-center gap-2 mt-2">
                                        <button class="btn btn-sm btn-outline-danger rounded-pill flex-grow-1 fw-800" disabled>
                                            <i class="fa fa-times-circle me-1"></i> DITOLAK
                                        </button>
                                        <button onclick="viewDetail(<?= $req->pengajuan_id ?>)"
                                            class="btn btn-sm btn-light border rounded-pill fw-800">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Item -->
<div class="modal fade" id="modalAddItem" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?= base_url('warehouse/requests_add_item') ?>" method="post"
            class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header px-4 py-4 text-white" style="background: var(--primary-gradient);">
                <h5 class="modal-title fw-900"><i class="fa fa-cart-plus me-2"></i>Tambah List Pengajuan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <div class="mb-4">
                    <label class="form-label-premium">Pilih Material / Produk</label>
                    <select name="material_id" class="form-select select2-modal" required>
                        <option value="">-- Cari Nama Material --</option>
                        <?php foreach ($materials as $m): ?>
                            <option value="<?= $m->material_id ?>">
                                <?= $m->bahan_nama ?> [
                                <?= $m->varian ?>]
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label-premium">Jumlah (QTY)</label>
                    <input type="number" step="any" name="qty" class="form-control form-control-premium" required
                        value="1">
                </div>
                <div class="mb-0">
                    <label class="form-label-premium">Keterangan / Tujuan Penggunaan</label>
                    <textarea name="keterangan" class="form-control form-control-premium" rows="2"
                        placeholder="Contoh: Untuk pesanan SPK#123..."></textarea>
                </div>
            </div>
            <div class="modal-footer p-4 pt-0 border-0">
                <button type="submit" class="btn btn-premium btn-premium-primary w-100 py-3">
                    <i class="fa fa-add-to-queue me-2"></i>Masukkan Daftar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header px-4 py-4 bg-light">
                <h5 class="modal-title fw-900"><i class="fa fa-file-invoice me-2 text-primary"></i>Detail Pengajuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-premium mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>NAMA MATERIAL</th>
                                <th class="text-center">QTY REQUEST</th>
                                <th class="text-center">STOK READY</th>
                                <th class="text-center">CATATAN</th>
                            </tr>
                        </thead>
                        <tbody id="detailContent">
                            <!-- JS Content -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-light fw-800 rounded-pill px-5"
                    data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.select2-modal').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#modalAddItem'),
            width: '100%'
        });
    });

    function viewDetail(id) {
        $('#detailContent').html('<tr><td colspan="3" class="text-center py-4"><i class="fa fa-sync fa-spin me-2"></i>Loading...</td></tr>');
        $('#modalDetail').modal('show');

        $.getJSON('<?= base_url('warehouse/requests_get_detail_json/') ?>' + id, function (res) {
            if (res.status === 'success') {
                let html = '';
                res.data.forEach(item => {
                    let stockBadge = item.stock_utama >= item.qty ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger';
                    html += `
                    <tr>
                        <td>
                            <div class="fw-800 text-dark">${item.bahan_nama}</div>
                            <div class="small text-muted">${item.varian}</div>
                        </td>
                        <td class="text-center fw-900 text-primary">${parseFloat(item.qty).toLocaleString()} ${item.satuan_nama}</td>
                        <td class="text-center">
                            <span class="badge ${stockBadge} px-3 py-2 rounded-pill fw-900 border">
                                ${parseFloat(item.stock_utama).toLocaleString()} ${item.satuan_nama}
                            </span>
                        </td>
                        <td class="text-center italic small text-muted">${item.keterangan || '-'}</td>
                    </tr>
                `;
                });
                $('#detailContent').html(html);
            }
        });
    }

    function processRequest(id) {
        Swal.fire({
            title: 'Proses Dokumen #' + id.toString().padStart(5, '0') + '?',
            text: "Item akan dipindahkan ke daftar staging Dispatch (Pengiriman).",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#27ae60',
            confirmButtonText: 'Ya, Proses ke Pengiriman',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                $.ajax({
                    url: '<?= base_url('warehouse/ajax_process_request_to_dispatch/') ?>' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function (res) {
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = '<?= base_url("warehouse/dispatch") ?>';
                            });
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    }
                });
            }
        });
    }
</script>