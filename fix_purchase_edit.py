import re

with open('/Applications/XAMPP/xamppfiles/htdocs/tgm_erp/application/views/admin/warehouse/purchase_edit.php', 'r') as f:
    html = f.read()

# Replace Header Title
html = html.replace('<h1 class="header-title">Penerimaan & Pembelian Material</h1>', '<h1 class="header-title">Edit Data Pembelian</h1>')
html = html.replace('<p class="header-subtitle">Phase 2: Execution</p>', '<p class="header-subtitle">Phase 2: Execution - Mode Edit</p>')
html = html.replace('<a href="<?= base_url(\'warehouse\') ?>" class="btn btn-outline-light btn-premium rounded-pill px-4">', '<a href="<?= base_url(\'warehouse/laporan_pembelian\') ?>" class="btn btn-outline-light btn-premium rounded-pill px-4">')

# Modify Form Opening and Placement
# Remove old form start
html = html.replace('<form action="<?= base_url(\'warehouse/purchase_finalize\') ?>" method="post">', '')
html = html.replace('</form>\n            </div>\n        </div>', '</div>\n        </div>\n</form>')

# Add form start higher
html = html.replace('<div class="col-lg-8">', '<div class="col-lg-8">\n            <form action="<?= base_url(\'warehouse/purchase_update/\'.$pembelian->pembelian_id) ?>" method="post" id="formUpdatePurchase">')

# Modify Keranjang Penerimaan -> Edit Item
html = html.replace('<h5 class="card-title-premium mb-0"><i class="fa fa-shopping-basket text-primary"></i> Keranjang\n                        Penerimaan</h5>', '<h5 class="card-title-premium mb-0"><i class="fa fa-shopping-basket text-primary"></i> Item Material</h5>')
html = html.replace('data-bs-target="#modalAddItem">\n                        <i class="fa fa-plus me-2"></i>Tambah Material\n                    </button>', 'data-bs-target="#modalAddItem">\n                        <i class="fa fa-plus me-2"></i>Tambah Material (Opsional)\n                    </button>')

# Replace temp_items loop with details loop
old_tbody = """<?php if (empty($temp_items)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="opacity-25 mb-3"><i class="fa fa-shopping-cart fa-4x"></i></div>
                                        <h6 class="fw-900 text-muted">Keranjang masih kosong</h6>
                                        <p class="text-muted small">Mulai tambahkan material yang diterima dari supplier.
                                        </p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($temp_items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-800 text-dark"><?= $item->bahan_nama ?></div>
                                            <div class="small text-muted font-monospace mt-1"><?= $item->varian ?></div>
                                        </td>
                                        <td>
                                            <?php if ($item->rak_id): ?>
                                                <span class="badge-rack"><i class="fas fa-th me-1"></i>
                                                    <?= $item->rak_kode ?></span>
                                            <?php else: ?>
                                                <span class="text-muted small italic">Default Area</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-soft-blue text-primary py-2 px-3 fw-900 rounded-pill">
                                                <?= number_format($item->qty) ?>         <?= $item->satuan_nama ?>
                                            </span>
                                        </td>
                                        <td class="text-end text-muted small">Rp <?= number_format($item->harga, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-end fw-900 text-dark">Rp
                                            <?= number_format($item->qty * $item->harga, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url('warehouse/purchase_delete_item/' . $item->temp_id) ?>"
                                                class="btn btn-sm btn-light border text-danger rounded-pill px-3">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>"""

new_tbody = """<?php foreach ($details as $idx => $item): ?>
                                    <tr class="item-row">
                                        <td>
                                            <div class="fw-800 text-dark"><?= $item->bahan_nama ?></div>
                                            <div class="small text-muted font-monospace mt-1"><?= $item->varian ?></div>
                                            <input type="hidden" name="material_id[]" value="<?= $item->material_id ?>">
                                            <input type="hidden" name="rak_id[]" value="<?= $item->rak_id ?>">
                                        </td>
                                        <td>
                                            <?php if ($item->rak_id): ?>
                                                <span class="badge-rack"><i class="fas fa-th me-1"></i> <?= $item->rak_kode ?></span>
                                            <?php else: ?>
                                                <span class="text-muted small italic">Default Area</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center" style="width: 150px;">
                                            <div class="input-group input-group-sm">
                                                <input type="number" step="0.01" name="qty[]" class="form-control text-center fw-bold row-qty" value="<?= floatval($item->qty) ?>" required>
                                                <span class="input-group-text bg-light text-muted small"><?= $item->satuan_nama ?></span>
                                            </div>
                                        </td>
                                        <td class="text-end" style="width: 180px;">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                                <input type="text" name="harga[]" class="form-control text-end flex-grow-1 border-start-0 row-harga fw-bold" value="<?= number_format($item->harga_beli, 0, ',', '.') ?>" required>
                                            </div>
                                        </td>
                                        <td class="text-end fw-900 text-dark row-subtotal-display">Rp <?= number_format($item->qty * $item->harga_beli, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-light border text-danger rounded-pill px-3 btn-remove-row">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>"""

html = html.replace(old_tbody, new_tbody)

# Update supplier
html = html.replace('<?php foreach ($suppliers as $s): ?>', '<?php foreach ($suppliers as $s): ?>\n                                        <option value="<?= $s->supplier_id ?>" <?= $s->supplier_id == $pembelian->supplier_id ? \'selected\' : \'\' ?>><?= $s->supplier_nama ?></option>')
html = html.replace('<?php foreach ($suppliers as $s): ?>\n                                        <option value="<?= $s->supplier_id ?>"><?= $s->supplier_nama ?></option>', '<?php foreach ($suppliers as $s): ?>')
html = html.replace('value="<?= date(\'Y-m-d\') ?>" required>', 'value="<?= $pembelian->pembelian_tgl ?>" required>')
html = html.replace('placeholder="Ex: INV-2024-001" required>', 'value="<?= $pembelian->no_faktur ?>" required>')

# Update metode bayar options
mb_opt = f"""<option value="Cash" <?= $pembelian->metode_bayar == 'tunai' || $pembelian->metode_bayar == 'cash' ? 'selected' : '' ?>>Cash (Tunai)</option>
                                    <option value="Transfer" <?= $pembelian->metode_bayar == 'transfer' ? 'selected' : '' ?>>Transfer Bank</option>
                                    <option value="Hutang" <?= $pembelian->metode_bayar == 'hutang' || $pembelian->metode_bayar == 'kredit' ? 'selected' : '' ?>>Hutang (Termin/Kredit)</option>"""
old_mb = """<option value="Cash">Cash (Tunai)</option>
                                    <option value="Transfer">Transfer Bank</option>
                                    <option value="Hutang">Hutang (Termin/Kredit)</option>"""
html = html.replace(old_mb, mb_opt)

# Rekening
html = html.replace('id="input_rekening_asal" style="display: none;"', 'id="input_rekening_asal" style="display: <?= $pembelian->metode_bayar == \'transfer\' ? \'block\' : \'none\' ?>;"')
html = html.replace('id="input_rekening_tujuan" style="display: none;"', 'id="input_rekening_tujuan" style="display: <?= $pembelian->metode_bayar == \'transfer\' ? \'block\' : \'none\' ?>;"')
html = html.replace('<?php foreach ($rekening as $r): ?>', '<?php foreach ($rekening as $r): ?>\n                                    <option value="<?= $r->id ?>" <?= ($pengeluaran && $pengeluaran->rekening_id == $r->id) ? \'selected\' : \'\' ?>><?= $r->nama_bank ?> - <?= $r->no_rekening ?></option>')
html = html.replace('<?php foreach ($rekening as $r): ?>\n                                    <option value="<?= $r->id ?>">\n                                        <?= $r->nama_bank ?> -\n                                        <?= $r->no_rekening ?>\n                                    </option>', '<?php foreach ($rekening as $r): ?>')

html = html.replace('placeholder="Bank / No Rek / Nama">', 'value="<?= $pengeluaran ? $pengeluaran->rekening_tujuan : \'\' ?>" placeholder="Bank / No Rek / Nama">')

html = html.replace('<button type="submit" class="btn btn-premium btn-premium-primary w-100 py-3 font-lg"\n                                <?= empty($temp_items) ? \'disabled\' : \'\' ?>>\n                                <i class="fa fa-check-double me-2"></i> SIMPAN & PROSES TRANSAKSI MASUK\n                            </button>', '<button type="submit" class="btn btn-warning btn-premium w-100 py-3 font-lg">\n                                <i class="fa fa-save me-2"></i> UPDATE TRANSAKSI PEMBELIAN\n                            </button>')


with open('/Applications/XAMPP/xamppfiles/htdocs/tgm_erp/application/views/admin/warehouse/purchase_edit.php', 'w') as f:
    f.write(html)

