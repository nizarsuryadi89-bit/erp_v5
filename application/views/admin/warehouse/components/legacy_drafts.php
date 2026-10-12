<?php if (!empty($legacy_drafts)): ?>
<div class="alert alert-warning mb-4">
    <strong>Draft lama belum memiliki petugas.</strong>
    <div class="small mb-2">Ambil draft yang Anda kerjakan ke keranjang Anda sebelum melanjutkan.</div>
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead><tr><th>Material</th><th>Jumlah</th><th>Petugas</th></tr></thead>
            <tbody>
            <?php foreach ($legacy_drafts as $draft): ?>
                <tr>
                    <td><?= html_escape((string)($draft->bahan_nama ?? 'Material #' . $draft->material_id)) ?>
                        <?= html_escape((string)($draft->varian ?? '')) ?></td>
                    <td><?= html_escape((string)$draft->qty) ?></td>
                    <td>
                        <form method="post" action="<?= base_url('warehouse/claim_legacy_draft') ?>">
                            <input type="hidden" name="kind" value="<?= html_escape($draft_kind) ?>">
                            <input type="hidden" name="temp_id" value="<?= (int)$draft->temp_id ?>">
                            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                            <button type="submit" class="btn btn-sm btn-outline-dark">Ambil ke Keranjang Saya</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
