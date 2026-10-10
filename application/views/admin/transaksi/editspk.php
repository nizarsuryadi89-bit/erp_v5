<?php
// Initialize variables to prevent "Undefined variable" errors
$no = 1;
$total_rows = isset($jmlTmp) ? $jmlTmp : 0;
$items = isset($transaksi) ? $transaksi : [];
// Ensure totalPemesanan is initialized in the view or passed from controller, 
// using a local variable to sum up for this view's display.
$running_total = 0;

// Debugging (optional, remove in production)
// if (empty($items)) { echo "<!-- Debug: Data transaksi kosong or invalid variable -->"; }

if ($total_rows > 0 && !empty($items)) {
    foreach ($items as $dt) {
        // Safe property access with fallbacks
        $id_detail = $dt->id_detail ?? $dt->id ?? 0; // Accommodate different ID column names
        $detail_product = $dt->product_nama ?? $dt->detail_product ?? 'Produk Tidak Diketahui';
        $satuan_nama = $dt->satuan_nama ?? '-';
        $harga_aktif = $dt->harga_aktif ?? '1'; // Default to price 1
        $ket_1 = $dt->ket_1 ?? '';

        // Dynamic Price Field
        $hargaField = 'harga_' . $harga_aktif;
        $hargaSatuan = isset($dt->$hargaField) ? $dt->$hargaField : ($dt->harga_satuan ?? 0);

        // Calculation Logic
        $qty = $dt->qty ?? 0;
        $panjang = $dt->panjang ?? 0;
        $lebar = $dt->lebar ?? 0;
        $satuan_id = $dt->satuan_id ?? 0;

        $totalHargaItem = $hargaSatuan * $qty;

        // If Square Meter calculation (Satuan ID 2 or 4 usually)
        if ($satuan_id == '2' || $satuan_id == '4') {
            // Avoid multiplication by zero if dimensions are missing
            $dimensi = ($panjang > 0 && $lebar > 0) ? ($panjang * $lebar) : 1;
            // Logic dependent on your business rule: usually price * qty * area
            $totalHargaItem = $hargaSatuan * $qty * $dimensi;
        }

        $running_total += $totalHargaItem;
        ?>

        <tr class="row_item_<?= $id_detail ?>">
            <td class="text-center"><?= $no ?></td>
            <td>
                <strong><?= $detail_product ?></strong>
                <?php if (!empty($ket_1)): ?>
                    <br><small class="text-muted"><i class="fas fa-info-circle"></i> <?= $ket_1 ?></small>
                <?php endif; ?>
            </td>
            <td class="text-center"><?= $qty ?></td>
            <td class="text-center"><?= $satuan_nama ?></td>
            <td class="text-end">Rp <?= number_format($hargaSatuan, 0, ',', '.') ?></td>
            <td class="text-center">
                <?php if ($panjang > 0 || $lebar > 0): ?>
                    <span class="badge bg-light text-dark border"><?= $panjang ?> x <?= $lebar ?></span>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
            <!-- Status Approval Column -->
            <td class="text-center">
                <?php
                // Logic for approval status label (assuming 'status' or 'approve' column)
                $approval = $dt->approve ?? 1;
                if ($approval == 1) {
                    echo '<span class="badge bg-success">Lanjut Produksi</span>';
                } else {
                    echo '<span class="badge bg-warning text-dark">Sampel</span>';
                }
                ?>
            </td>
            <td class="text-end fw-bold">Rp <?= number_format($totalHargaItem, 0, ',', '.') ?></td>
            <td class="text-center">
                <!-- IMPORTANT: Class 'btn_del_permanent' matches the JS in editspk.php -->
                <button type="button" class="btn btn-danger btn-sm btn_del_permanent" data-id="<?= $id_detail ?>"
                    title="Hapus Item">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>

        <?php
        $no++;
    }
} else {
    // Empty State matching the colspan of editspk.php (9 columns)
    echo '<tr><td colspan="9" class="text-center py-4 text-muted"><em>Tidak ada item transaksi</em></td></tr>';
}
?>

<!-- Hidden Input to pass total to JS if needed -->
<input type="hidden" id="calculated_total" value="<?= $running_total ?>">