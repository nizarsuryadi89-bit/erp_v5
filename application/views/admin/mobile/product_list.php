<?php if (!empty($produk)): ?>
  <div class="row g-3">
    <?php foreach ($produk as $p): ?>
      <div class="col-12 product-entry">
        <style>
          .product-card-mini {
            background: #fff;
            border-radius: 20px;
            padding: 1rem;
            display: flex;
            gap: 1rem;
            align-items: center;
            text-decoration: none;
            transition: all 0.2s;
            border: 1px solid #f1f5f9;
          }

          .product-card-mini:active {
            transform: scale(0.97);
            background: #f8fafc;
          }

          .product-icon-wrap {
            width: 50px;
            height: 50px;
            background: #f0f9ff;
            color: #0ea5e9;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
          }

          .product-details h6 {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.2rem;
          }

          .product-details p {
            font-size: 0.75rem;
            color: #64748b;
            margin: 0;
          }
        </style>
        <a href="<?= base_url('mobile/produk_detail/' . $p->product_id) ?>" class="product-card-mini shadow-sm">
          <div class="product-icon-wrap">
            <i class="bi bi-box"></i>
          </div>
          <div class="product-details flex-grow-1">
            <h6><?= htmlspecialchars($p->product_nama) ?></h6>
            <div class="d-flex align-items-center gap-2">
              <p><i
                  class="bi bi-bookmark-fill me-1 text-info"></i><?= htmlspecialchars($p->kategori_nama ?? 'Uncategorized') ?>
              </p>
              <span class="text-muted d-none d-xs-inline" style="font-size: 0.65rem;">• Cek Range Harga</span>
            </div>
          </div>
          <div class="text-end">
            <i class="bi bi-chevron-right text-muted"></i>
          </div>
        </a>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Pagination -->
  <?php if (isset($total_pages) && $total_pages > 1): ?>
    <nav aria-label="Page navigation" class="mt-4">
      <ul class="pagination pagination-sm justify-content-center border-0">
        <style>
          .pagination-sm .page-item .page-link {
            border-radius: 10px;
            margin: 0 3px;
            border: none;
            background: #fff;
            color: #64748b;
            padding: 0.5rem 0.75rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
          }

          .pagination-sm .page-item.active .page-link {
            background: var(--primary);
            color: #fff;
          }
        </style>
        <?php if ($page > 1): ?>
          <li class="page-item">
            <a class="page-link" href="#" data-ci-pagination-page="<?= $page - 1 ?>"><i class="bi bi-chevron-left"></i></a>
          </li>
        <?php endif; ?>

        <?php
        $start = max(1, $page - 2);
        $end = min($total_pages, $page + 2);
        for ($i = $start; $i <= $end; $i++):
          ?>
          <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
            <a class="page-link" href="#" data-ci-pagination-page="<?= $i ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
          <li class="page-item">
            <a class="page-link" href="#" data-ci-pagination-page="<?= $page + 1 ?>"><i class="bi bi-chevron-right"></i></a>
          </li>
        <?php endif; ?>
      </ul>
    </nav>
  <?php endif; ?>

<?php else: ?>
  <div class="text-center py-5 bg-white rounded-4 border border-dashed">
    <i class="bi bi-search text-muted opacity-50" style="font-size: 3rem;"></i>
    <h6 class="mt-3 text-muted">Produk tidak ditemukan</h6>
    <p class="small text-muted px-4">Coba gunakan kata kunci lain untuk mencari produk yang Anda inginkan.</p>
  </div>
<?php endif; ?>