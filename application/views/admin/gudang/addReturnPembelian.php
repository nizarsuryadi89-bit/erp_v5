<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    /* ==========================================================================
    PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - RETURN FORM
    ========================================================================== */
    :root {
        --primary-gradient: linear-gradient(135deg, #1a1c23 0%, #2c3e50 100%);
        --success-gradient: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
        --info-gradient: linear-gradient(135deg, #4895ef 0%, #4cc9f0 100%);
        --danger-gradient: linear-gradient(135deg, #f72585 0%, #b5179e 100%);
        --premium-shadow: 0 10px 30px rgba(0,0,0,0.05);
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
        margin: 0; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase; font-size: 0.9rem;
        display: flex; align-items: center; gap: 10px;
    }

    /* Form Styles */
    .form-control-premium {
        border: 2px solid #f1f5f9;
        border-radius: 12px;
        padding: 0.75rem 1.25rem;
        font-weight: 600;
        background: #f8fafc;
        transition: all 0.3s;
    }

    .form-control-premium:focus { border-color: #4895ef; background: white; box-shadow: 0 4px 15px rgba(72,149,239,0.1); }
    .form-label-premium { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 1px; margin-bottom: 0.5rem; display: block; }

    /* Table Styles */
    .premium-table thead th {
        background: #f8fafc; border: none; color: #475569; font-weight: 800; text-transform: uppercase;
        font-size: 0.65rem; letter-spacing: 1px; padding: 1rem 1.5rem;
    }

    .premium-table tbody td { padding: 1rem 1.5rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }

    .btn-premium {
        padding: 0.75rem 2rem; border-radius: 12px; font-weight: 800; text-transform: uppercase;
        letter-spacing: 0.5px; transition: all 0.3s; border: none; display: flex; align-items: center; justify-content: center; gap: 10px;
    }

    .btn-premium-primary { background: var(--primary-gradient); color: white; }
    .btn-premium-danger { background: var(--danger-gradient); color: white; }

    /* Animation */
    @keyframes slideUpFade { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    .animate-up { animation: slideUpFade 0.5s ease-out forwards; }
</style>

<div class="app-content mt-2">
    <div class="container-fluid">
        <!-- Header Info Card -->
        <div class="report-section-card animate-up">
            <div class="section-header">
                <h5><i class="fas fa-undo-alt"></i> Form Transaksi Return Pembelian</h5>
                <a href="<?= base_url('warehouse/returnPembelian') ?>" class="btn btn-sm btn-light rounded-pill px-3 fw-bold">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="p-4">
                <div class="row g-4">
                    <div class="col-md-9">
                        <div class="p-3 rounded-4 bg-light border border-dashed border-info h-100 d-flex align-items-center">
                            <div>
                                <h6 class="fw-900 text-info text-uppercase small mb-2"><i class="fas fa-info-circle me-1"></i> Prosedur Return</h6>
                                <p class="small text-muted mb-0">Pastikan Anda memilih <b>Supplier</b> terlebih dahulu, kemudian pilih <b>No Faktur</b> yang sesuai. Stok akan dikembalikan (keluar) secara otomatis setelah finalisasi.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="<?= base_url('warehouse/actionAddReturnPembelian') ?>" method="post" class="row g-4 mt-2">
                    <div class="col-md-4">
                        <label class="form-label-premium">Pilih Supplier</label>
                        <select name="supplier_id" id="supplier_id" class="form-select select2-supplier" required>
                            <option value="">-- Cari Supplier --</option>
                            <?php foreach ($pembelian_list as $p): ?><option value="<?= $p->supplier_id ?>"><?= $p->supplier_nama ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-premium">Pilih Faktur Pembelian (Asal)</label>
                        <select name="pembelian_id" id="pembelian_id" class="form-select select2-faktur" onchange="loadPembelianDetail()">
                            <option value="">-- Pilih Pembelian --</option>
                            <?php foreach ($pembelian_list as $p): ?>
                                <option value="<?= $p->pembelian_id ?>">
                                    <?= $p->no_faktur ?> - <?= $p->supplier_nama ?> (<?= date('d/m/y', strtotime($p->pembelian_tgl)) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-premium">No Referensi Return</label>
                        <input type="text" class="form-control form-control-premium" name="no_return" placeholder="Auto / Manual Ref" required>
                    </div>

                    <!-- AJax Body Content -->
                    <div id="pembelian_detail" class="col-12 mt-4" style="display: none;">
                        <div class="rounded-4 p-3 border bg-light">
                            <h6 class="fw-900 text-dark mb-3"><i class="fas fa-list-ul me-1"></i> Item Dalam Faktur Ini:</h6>
                            <div class="table-responsive bg-white rounded-3 shadow-sm">
                                <table class="table premium-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Bahan / Material</th>
                                            <th class="text-center">Qty Beli</th>
                                            <th class="text-center">Qty Return</th>
                                            <th>Alasan Pengembalian</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detail_body"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Temporary List -->
                    <div class="col-12 mt-5">
                        <h6 class="fw-900 text-danger text-uppercase small mb-3"><i class="fas fa-shopping-cart me-1"></i> Item Yang Akan Direturn:</h6>
                        <div class="table-responsive">
                            <table class="table premium-table table-hover border rounded-4 overflow-hidden">
                                <thead>
                                    <tr>
                                        <th width="40" class="text-center">No</th>
                                        <th>Material</th>
                                        <th class="text-center">Qty Return</th>
                                        <th class="text-end">Harga Beli</th>
                                        <th class="text-end">Potongan Estimasi</th>
                                        <th width="100" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; $total_return = 0; if(!empty($temp)): foreach ($temp as $v): 
                                        $sub = $v->qty_return * $v->harga_return; $total_return += $sub;
                                    ?>
                                        <tr>
                                            <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                                            <td>
                                                <div class="fw-800 text-dark"><?= $v->bahanbaku_nama ?></div>
                                                <div class="small text-muted"><?= $v->alasan_return ?></div>
                                            </td>
                                            <td class="text-center"><span class="badge bg-danger-subtle text-danger px-3 py-2 fw-900">- <?= number_format($v->qty_return) ?></span></td>
                                            <td class="text-end text-muted">Rp <?= number_format($v->harga_return, 0, ',', '.') ?></td>
                                            <td class="text-end fw-900 text-danger">Rp <?= number_format($sub, 0, ',', '.') ?></td>
                                            <td class="text-center">
                                                <a href="<?= base_url('warehouse/deleteTempReturn/' . $v->temp_id) ?>" class="btn btn-sm btn-outline-danger border-0"><i class="fas fa-trash-alt"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; else: ?>
                                        <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada item ditambahkan ke daftar return.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <td colspan="4" class="text-end fw-800 py-3">GRAND TOTAL RETURN:</td>
                                        <td class="text-end py-3"><div class="fw-900 text-danger fs-5">Rp <?= number_format($total_return, 0, ',', '.') ?></div></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="col-12 mt-4 pt-4 border-top">
                        <input type="hidden" name="return_total" value="<?= $total_return ?>">
                        <input type="hidden" name="return_tgl" value="<?= date('Y-m-d H:i:s') ?>">
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn-premium btn-premium-danger flex-grow-1" <?= empty($temp) ? 'disabled' : '' ?>>
                                <i class="fas fa-check-double"></i> Finalisasi Return Ke Supplier
                            </button>
                            <a href="<?= base_url('warehouse/returnPembelian') ?>" class="btn-premium btn-light border px-4">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-supplier, .select2-faktur').select2({ theme: 'bootstrap-5' });
    });

    function loadPembelianDetail() {
        const id = $('#pembelian_id').val();
        if(!id) { $('#pembelian_detail').hide(); return; }
        
        $.post('<?= base_url("warehouse/getPembelianDetail") ?>', { pembelian_id: id }, function(res) {
            let html = '';
            const data = JSON.parse(res);
            data.forEach(item => {
                html += `<tr>
                    <td><div class="fw-800">${item.bahanbaku_nama}</div><small class="text-muted">ID: ${item.bahanbaku_id}</small></td>
                    <td class="text-center"><span class="badge bg-light text-dark border">${item.pemb_qty}</span></td>
                    <td class="text-center"><input type="number" class="form-control form-control-sm text-center return-qty" style="max-width:80px; margin:0 auto;" data-id="${item.bahanbaku_id}" data-harga="${item.pemb_harga}" max="${item.pemb_qty}" min="1" placeholder="0"></td>
                    <td><input type="text" class="form-control form-control-sm return-alasan" placeholder="Alasan return..."></td>
                    <td class="text-center"><button type="button" class="btn btn-sm btn-primary rounded-circle" onclick="addToReturn(this)"><i class="fas fa-plus"></i></button></td>
                </tr>`;
            });
            $('#detail_body').html(html);
            $('#pembelian_detail').fadeIn();
        });
    }

    function addToReturn(btn) {
        const row = $(btn).closest('tr');
        const qInput = row.find('.return-qty');
        const data = {
            bahanbaku_id: qInput.data('id'),
            qty_return: qInput.val(),
            harga_return: qInput.data('harga'),
            alasan_return: row.find('.return-alasan').val()
        };

        if(!data.qty_return || data.qty_return <= 0) { Swal.fire('Oops!', 'Masukkan jumlah return yang valid.', 'warning'); return; }
        if(!data.alasan_return) { Swal.fire('Oops!', 'Wajib memberikan alasan return.', 'warning'); return; }

        // Post to Temp
        $.post('<?= base_url("warehouse/addTempReturn") ?>', data, function() { location.reload(); });
    }
</script>