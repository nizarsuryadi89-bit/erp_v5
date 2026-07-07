<?php
$bom_data = $this->M_master->getbombykode($getproduk['product_id'])->row();
$use_pxl = (!isset($subkategori_config['use_pxl']) || $subkategori_config['use_pxl'] == 1);
?>

<?php if ($use_pxl): ?>
    <div class="row align-items-center mb-2 animate__animated animate__fadeIn">
        <div class="col-sm-4">
            <label class="form-label small fw-bold text-muted mb-0">DIMENSI (PxL)</label>
        </div>
        <div class="col-sm-8">
            <div class="input-group input-group-sm">
                <input type="number" class="form-control fw-bold" name="panjang" placeholder="P"
                    value="<?= isset($bom_data->panjang) ? $bom_data->panjang : '' ?>" step="0.01">
                <span class="input-group-text bg-light text-muted">x</span>
                <input type="number" class="form-control fw-bold" name="lebar" placeholder="L"
                    value="<?= isset($bom_data->lebar) ? $bom_data->lebar : '' ?>" step="0.01">
                <span class="input-group-text bg-light text-muted">cm</span>
            </div>
        </div>
    </div>
<?php endif; ?>