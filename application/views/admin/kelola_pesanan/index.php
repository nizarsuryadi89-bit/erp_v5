<link rel="stylesheet" href="<?= base_url('assets/css/kelola_pesanan.css') ?>">

<div class="kelola-pesanan-wrapper">
    <!-- Header -->
    <header class="order-header d-flex justify-content-between align-items-center">
        <h1 class="order-title">Kelola Pesanan</h1>
        <div class="header-actions">
            <!-- Search like top right in screenshot -->
            <div class="input-group" style="width: 300px;">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari ID pesanan, Produk, User...">
            </div>
        </div>
    </header>

    <!-- Stats -->
    <section class="stat-card-container">
        <div class="stat-item">
            <span class="stat-label">HARUS DIKIRIM</span>
            <span class="stat-value text-danger"><?= number_format($stats['perlu_dikirim']) ?></span>
        </div>
        <div class="stat-item">
            <span class="stat-label">DALAM PROSES</span>
            <span class="stat-value text-primary"><?= number_format($stats['dalam_proses']) ?></span>
        </div>
        <div class="stat-item">
            <span class="stat-label">SIAP PB</span>
            <span class="stat-value text-success"><?= number_format($stats['siap_pb']) ?></span>
        </div>
        <div class="stat-item">
            <span class="stat-label">TERLAMBAT</span>
            <span class="stat-value"><?= number_format($stats['terlambat']) ?></span>
        </div>
        <div class="stat-item">
            <span class="stat-label">DIBATALKAN</span>
            <span class="stat-value"><?= number_format($stats['dibatalkan']) ?></span>
        </div>
    </section>

    <!-- Tabs Menu -->
    <nav class="order-tabs-container">
        <ul class="nav nav-tabs nav-tabs-market" id="orderTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link <?= $current_status == 'semua' ? 'active' : '' ?>" href="<?= site_url('kelola_pesanan?status=semua') ?>">Semua</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_status == 'perlu_dikirim' ? 'active' : '' ?>" href="<?= site_url('kelola_pesanan?status=perlu_dikirim') ?>">Perlu dikirim</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_status == 'dalam_proses' ? 'active' : '' ?>" href="<?= site_url('kelola_pesanan?status=dalam_proses') ?>">Dalam proses</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_status == 'siap_pb' ? 'active' : '' ?>" href="<?= site_url('kelola_pesanan?status=siap_pb') ?>">Pengecekan (PB)</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_status == 'selesai' ? 'active' : '' ?>" href="<?= site_url('kelola_pesanan?status=selesai') ?>">Selesai</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_status == 'dibatalkan' ? 'active' : '' ?>" href="<?= site_url('kelola_pesanan?status=dibatalkan') ?>">Dibatalkan</a>
            </li>
        </ul>
    </nav>

    <!-- Content Table -->
    <section class="order-table-area">
        <div class="card-order-list">
            <div class="table-responsive">
                <table class="table market-table mb-0">
                    <thead>
                        <tr>
                            <th width="50" class="text-center">
                                <input type="checkbox" class="form-check-input">
                            </th>
                            <th width="250">PESANAN</th>
                            <th width="150">PEMBELI</th>
                            <th>PRODUK</th>
                            <th width="150">STATUS PESANAN</th>
                            <th width="150">PENGIRIMAN</th>
                            <th width="120" class="text-end">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $o): ?>
                                <tr>
                                    <td class="text-center"><input type="checkbox" class="form-check-input"></td>
                                    <td>
                                        <div class="fw-bold mb-1">#<?= $o->nospk ?></div>
                                        <div class="small text-muted"><?= date('d M Y, H:i', strtotime($o->created_at)) ?></div>
                                        <?php if ($o->marketplace_nama): ?>
                                            <span class="badge bg-light text-dark border mt-2" style="font-size: 0.6rem;">
                                                <i class="fas fa-shopping-bag me-1"></i> <?= $o->marketplace_nama ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold fs-7"><?= $o->konsumen_nama ?></div>
                                        <div class="small text-muted"><?= $o->konsumen_nohp ?></div>
                                    </td>
                                    <td>
                                        <?php foreach ($o->items as $idx => $item): ?>
                                            <div class="product-info <?= $idx > 0 ? 'mt-3 pt-3 border-top' : '' ?>">
                                                <div class="product-img">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="product-name"><?= $item->product_nama ?></span>
                                                    <span class="product-qty"><?= (float)$item->qty ?> pcs | <?= $item->panjang > 0 ? $item->panjang . 'x' . $item->lebar . 'm' : 'Standard' ?></span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                        <?php if (count($o->items) > 1): ?>
                                            <!-- Optionally show more items in a list -->
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $pay_status = $o->status_pembayaran;
                                        // Simplified aggregate status
                                        $aggStatus = 'BARU';
                                        $aggClass = 'bg-p-wait';
                                        
                                        if ($pay_status == 'paid') {
                                            $aggStatus = 'SIAP CETAK';
                                            $aggClass = 'bg-p-process';
                                            
                                            // Check production
                                            $allDone = true;
                                            $anyProses = false;
                                            foreach($o->items as $it) {
                                                if($it->status_produksi == 'proses') $anyProses = true;
                                                if($it->status_produksi != 'done' && $it->status_produksi != 'on_pb') $allDone = false;
                                            }
                                            
                                            if ($anyProses) {
                                                $aggStatus = 'PROSES KERJA';
                                                $aggClass = 'bg-p-process';
                                            } elseif ($allDone) {
                                                $aggStatus = 'SIAP PB';
                                                $aggClass = 'bg-p-done';
                                            }
                                        } elseif ($pay_status == 'cancel') {
                                            $aggStatus = 'DIBATALKAN';
                                            $aggClass = 'bg-p-cancel';
                                        }
                                        ?>
                                        <span class="badge-market <?= $aggClass ?>"><?= $aggStatus ?></span>
                                    </td>
                                    <td>
                                        <div class="small fw-bold"><?= $o->nama_pengiriman ?: 'Ambil Sendiri' ?></div>
                                        <div class="small text-muted"><?= $o->no_resi ?: 'No Resi -' ?></div>
                                    </td>
                                    <td class="text-end">
                                        <div class="text-price">Rp <?= number_format($o->total_order) ?></div>
                                        <div class="small text-muted" style="font-size: 0.65rem;">Lunas</div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="py-5">
                                        <i class="fas fa-box-open fa-3x text-muted opacity-25 mb-3"></i>
                                        <p class="text-muted">Tidak ada pesanan ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<script>
    // Tab filtering could be added here via AJAX to make it faster without page reload
</script>
