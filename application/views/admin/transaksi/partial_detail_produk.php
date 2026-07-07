<table class="table table-bordered mb-0">
    <?php
    if (!function_exists('invoice_detail_metadata')) {
        function invoice_detail_metadata($item)
        {
            if (empty($item->metadata) || !is_string($item->metadata)) {
                return [];
            }

            $decoded = json_decode($item->metadata, true);
            return is_array($decoded) ? $decoded : [];
        }

        function invoice_detail_price($item)
        {
            $metadata = invoice_detail_metadata($item);
            if (isset($metadata['unit_price']) && is_numeric($metadata['unit_price'])) {
                return (float) $metadata['unit_price'];
            }

            return (float) ($item->harga_1 ?? 0);
        }

        function invoice_detail_total($item, $harga = null)
        {
            $metadata = invoice_detail_metadata($item);
            if (isset($metadata['subtotal']) && is_numeric($metadata['subtotal'])) {
                return (float) $metadata['subtotal'];
            }

            $harga = $harga === null ? invoice_detail_price($item) : (float) $harga;
            return $harga * (float) ($item->qty ?? 0);
        }
    }
    ?>
    <thead>
        <tr>
            <th>No</th>
            <th>Product</th>
            <th>Qty</th>
            <th>Harga</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $nomor = 1;
        foreach ($qdetail as $dtl):
            $harga = invoice_detail_price($dtl);
            $jumlah = invoice_detail_total($dtl, $harga);
        ?>
        <tr>
            <td><?= $nomor++ ?></td>
            <td><?= $dtl->product_nama ?></td>
            <td><?= $dtl->qty ?></td>
            <td class="text-end"><?= number_format($harga) ?></td>
            <td class="text-end"><?= number_format($jumlah) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table> 
