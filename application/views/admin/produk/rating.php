<link rel="stylesheet" href="<?= base_url('assets/css/produk_rating.css') ?>">

<div class="rating-container">
    <div class="page-title px-4 pt-4">
        <h3>Rating Produk</h3>
    </div>

    <!-- Channel Selector Card -->
    <div class="px-4">
        <div class="rating-card">
            <div class="market-tabs">
                <?php foreach ($channels as $c): ?>
                    <a href="<?= site_url('produk/rating?channel=' . $c['id']) ?>"
                        class="text-decoration-none market-item <?= $current_channel == $c['id'] ? 'active' : '' ?>">
                        <div class="bg-light p-2 rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 35px; height: 35px;">
                            <i class="<?= $c['icon'] ?> text-muted"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-7 text-dark"><?= $c['name'] ?></div>
                            <div class="small text-muted" style="font-size: 0.65rem;">
                                Performa Kanal
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="stats-grid">
                <div class="tanggapi-card">
                    <div class="stat-box">
                        <div class="stat-title">Ringkasan Kanal:
                            <?= $current_channel == 'semua' ? 'Global' : 'Spesifik' ?></div>

                        <div class="stat-item-inner d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small text-muted">Total Transaksi</div>
                                <div class="stat-count"><?= number_format($stats['total_sales']) ?></div>
                            </div>
                        </div>

                        <div class="stat-item-inner d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small text-muted">Total Omzet</div>
                                <div class="stat-count" style="font-size: 1.1rem; color: var(--market-primary);">Rp
                                    <?= number_format($stats['revenue']) ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-ulasan text-center py-4">
                    <h5 class="fw-bold mb-3">Ranking Produk Terlaris (Top 10)</h5>
                    <p class="text-muted small">Data peringkat dihitung berdasarkan volume penjualan dari kanal
                        <strong><?= $current_channel ?></strong></p>

                    <div class="d-flex justify-content-center gap-4 mt-4">
                        <div class="text-center">
                            <div class="stat-count text-success"><?= count($best_sellers) ?></div>
                            <div class="small text-muted">Produk Aktif</div>
                        </div>
                        <div class="border-start mx-3"></div>
                        <div class="text-center">
                            <div class="stat-count text-primary">
                                <?= array_sum(array_column($best_sellers, 'total_sold')) ?></div>
                            <div class="small text-muted">Item Terjual</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="px-4">
        <div class="rating-card p-3">
            <div class="row g-3">

                <div class="col-md-4 ms-auto">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i
                                class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0"
                            placeholder="Cari menurut ID pesanan, nama produk...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rating List -->
    <div class="px-4">
        <div class="rating-card">
            <div class="p-3 border-bottom">
                <h5 class="mb-0"><?= count($recent_ratings) ?> Rating</h5>
            </div>
            <div class="table-responsive">
                <table class="table rating-table mb-0">
                    <thead>
                        <tr>
                            <th width="300">Penilaian dan ulasan</th>
                            <th width="150">Balasan Anda</th>
                            <th width="250">ID Produk dan pesanan</th>
                            <th width="150">Nama pengguna</th>
                            <th width="150">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_ratings as $r): ?>
                            <tr>
                                <td>
                                    <div class="star-rating mb-2">
                                        <?php for ($i = 0; $i < $r->stars; $i++): ?>
                                            <i class="fas fa-star"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <div class="small text-muted mb-2"><?= $r->date ?></div>
                                    <div class="review-text"><?= $r->comment ?></div>
                                </td>
                                <td>
                                    <?php if ($r->replied): ?>
                                        <div class="badge bg-light text-dark border">Sudah dibalas</div>
                                    <?php else: ?>
                                        <div class="small text-muted">0 balasan</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="product-info-box">
                                        <div class="product-thumb">
                                            <i class="fas fa-box m-2 text-muted opacity-50"></i>
                                        </div>
                                        <div>
                                            <div class="id-pesanan fw-bold text-dark">ID Pesanan: <?= $r->order_id ?> <i
                                                    class="far fa-copy ms-1 text-muted"></i></div>
                                            <div class="small text-muted" style="font-size: 0.75rem;">
                                                <?= $r->product_name ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="customer-name"><?= $r->customer ?></div>
                                </td>
                                <td>
                                    <div class="action-links">
                                        <a href="#">Balas</a>
                                        <a href="#">Perincian ulasan</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Best Sellers Section (Additional Value) -->
    <div class="px-4 mt-4">
        <h5 class="mb-3">Produk Terlaris</h5>
        <div class="row">
            <?php foreach ($best_sellers as $item): ?>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-light p-3 rounded">
                                <i class="fas fa-trophy text-warning"></i>
                            </div>
                            <div>
                                <div class="fw-bold fs-7"><?= $item->product_nama ?></div>
                                <div class="small text-muted"><?= number_format($item->total_sold) ?> Terjual</div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>