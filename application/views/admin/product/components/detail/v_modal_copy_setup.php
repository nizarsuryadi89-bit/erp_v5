<?php
if (!isset($variations) || !is_array($variations)) {
    if (isset($getProduct['product_id'])) {
        $ci =& get_instance();
        $raw_vars = $ci->db->get_where('tbl_harga_product', ['product_id' => $getProduct['product_id']])->result();
        if (!empty($raw_vars)) {
            foreach ($raw_vars as &$v) {
                $var_name = !empty($v->detail_product) ? $v->detail_product : 'Variasi ID: ' . $v->harga_id;
                $meta_desc = [];
                if (!empty($v->metadata)) {
                    $meta = json_decode($v->metadata, true);
                    if (is_array($meta)) {
                        $ignored = ['product_id', 'nospk', 'qty', 'panjang', 'lebar', 'kategori_id', 'keterangan', 'approve', 'lokasi_cetak'];
                        foreach ($meta as $k => $val) {
                            if (!in_array($k, $ignored) && strpos($k, 'dim_') === false && strpos($k, '_id') === false && $val !== '') {
                                $meta_desc[] = $val;
                            }
                        }
                    }
                }
                if (empty($meta_desc)) {
                    if (!empty($v->ket_1) && $v->ket_1 !== '0') $meta_desc[] = $v->ket_1;
                    if (!empty($v->jenisprint_id) && $v->jenisprint_id !== '0') $meta_desc[] = $v->jenisprint_id;
                    if (!empty($v->side_id) && $v->side_id !== '0') $meta_desc[] = $v->side_id;
                }
                if (!empty($meta_desc)) {
                    $var_name .= ' (' . implode(' | ', $meta_desc) . ')';
                }
                $v->display_name = $var_name;
            }
            unset($v);
            $variations = $raw_vars;
        } else {
            $variations = [];
        }
    } else {
        $variations = [];
    }
}
?>
<!-- Modal Copy BOM & Routing -->
<div class="modal fade" id="modalCopySetup" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 1.25rem;">
            <div class="modal-header border-0 bg-light p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="bi bi-files text-info fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-slate-900">Salin Setup ke Variasi Lain</h5>
                        <p class="text-muted small mb-0">Terapkan BOM & Routing dari satu variasi ke variasi lainnya dengan cepat.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="copySetupForm">
                <div class="modal-body p-4">
                    <div class="alert alert-warning small mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Perhatian:</strong> BOM dan Routing pada variasi tujuan yang Anda centang akan dihapus dan diganti dengan konfigurasi yang baru dari variasi sumber.
                    </div>
                    
                    <div class="mb-4 p-3 bg-slate-50 rounded-3 border">
                        <label class="form-label fw-bold small text-primary">COPY DARI SUMBER INI:</label>
                        <select class="form-select shadow-none" name="source_harga_id" id="copy_source_harga_id" required>
                            <option value="">-- Semua Variasi (Global) --</option>
                            <?php if (!empty($variations) && is_array($variations)): ?>
                                <?php foreach ($variations as $var): ?>
                                    <option value="<?= $var->harga_id ?>">
                                        <?= !empty($var->display_name) ? $var->display_name : ($var->detail_product ?: 'Variasi ID: ' . $var->harga_id) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">PASTE KE VARIASI BERIKUT:</label>
                        <div class="d-flex justify-content-between mb-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="$('.copy-target-checkbox').prop('checked', true)">Pilih Semua</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="$('.copy-target-checkbox').prop('checked', false)">Batalkan Semua</button>
                        </div>
                        <div class="border rounded-3 p-3 overflow-auto" style="max-height: 250px; background: #fff;">
                            <div class="form-check mb-2 pb-2 border-bottom">
                                <input class="form-check-input copy-target-checkbox" type="checkbox" name="target_harga_ids[]" value="" id="target_global">
                                <label class="form-check-label" for="target_global">
                                    -- Semua Variasi (Global) --
                                </label>
                            </div>
                            <?php if (!empty($variations) && is_array($variations)): ?>
                                <?php foreach ($variations as $var): ?>
                                    <div class="form-check mb-2 pb-2 border-bottom">
                                        <input class="form-check-input copy-target-checkbox" type="checkbox" name="target_harga_ids[]" value="<?= $var->harga_id ?>" id="target_<?= $var->harga_id ?>">
                                        <label class="form-check-label" for="target_<?= $var->harga_id ?>">
                                            <?= !empty($var->display_name) ? $var->display_name : ($var->detail_product ?: 'Variasi ID: ' . $var->harga_id) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-muted small py-2">
                                    <i class="bi bi-info-circle me-1"></i> Tidak ada variasi khusus pada produk ini.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 text-end">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info text-white rounded-pill px-4 fw-bold shadow-sm">Terapkan Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>