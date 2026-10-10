<?php if (!empty($orders)): ?>
    <?php foreach ($orders as $order): ?>
        <tr>
            <td class="ps-4">
                <div class="fw-800 text-dark">
                    <?= $order->nospk ?>
                </div>
                <span class="badge bg-soft-primary text-primary small">PRODUKSI TITIPAN</span>
            </td>
            <td>
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-store text-primary small"></i>
                    <span class="fw-bold text-uppercase">
                        <?= $order->asal_cabang ?>
                    </span>
                </div>
            </td>
            <td>
                <div class="fw-bold text-dark">
                    <?= strtoupper($order->konsumen_nama) ?>
                </div>
            </td>
            <td class="text-center">
                <span class="badge bg-light text-dark border fw-bold">
                    <?= $order->jumlah_item ?> Item
                </span>
            </td>
            <td>
                <div class="fw-800 text-primary">Rp
                    <?= number_format($order->total_transaksi, 0, ',', '.') ?>
                </div>
            </td>
            <td>
                <div class="small fw-bold text-muted">
                    <?= date('d/m/Y', strtotime($order->created_at)) ?>
                </div>
                <div class="small text-muted opacity-75">
                    <?= date('H:i', strtotime($order->created_at)) ?>
                </div>
            </td>
            <td>
                <?php if ($order->status_pembayaran == 'paid'): ?>
                    <span class="badge bg-success rounded-pill px-3">LUNAS</span>
                <?php elseif ($order->status_pembayaran == 'pending'): ?>
                    <span class="badge bg-warning text-dark rounded-pill px-3">PENDING</span>
                <?php else: ?>
                    <span class="badge bg-danger rounded-pill px-3">
                        <?= strtoupper($order->status_pembayaran) ?>
                    </span>
                <?php endif; ?>
            </td>
            <td class="text-end pe-4">
                <div class="d-flex justify-content-end gap-2">
                    <!-- Cetak Invoice (Paid Only) -->
                    <?php if ($order->status_pembayaran == 'paid'): ?>
                        <a href="<?= site_url('transaksi/cetak_invoice_ulang/' . $order->nospk) ?>"
                            class="btn btn-sm btn-dark text-white rounded-3 px-3 shadow-sm border-0"
                            title="Cetak Invoice">
                            <i class="fas fa-print me-1"></i> CETAK INVOICE
                        </a>
                    <?php endif; ?>

                    <a href="<?= base_url('cekpesanan/spk/' . urlencode(encrypt_spk($order->nospk))) ?>"
                        class="btn btn-sm btn-outline-dark rounded-3 px-3 shadow-sm border-0"
                        target="_blank" title="Link Invoice">
                        <i class="fas fa-globe me-1"></i> WEB
                    </a>

                    <!-- <a href="<?= site_url('transaksi/preview_spk/' . $order->nospk) ?>"
                        class="btn btn-sm btn-info text-white rounded-3 px-3 shadow-sm border-0"
                        target="_blank">
                        <i class="fas fa-print me-1"></i> CETAK SPK
                    </a> -->

                    <?php if ($this->session->userdata('level') != 3): ?>
                        <button
                            onclick="window.location.href='<?= site_url('transaksi/pembayaran/' . $order->nospk) ?>'"
                            class="btn btn-sm btn-primary rounded-3 px-3 shadow-sm border-0">
                            <i class="fas fa-cash-register me-1"></i> PROSES
                        </button>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>
