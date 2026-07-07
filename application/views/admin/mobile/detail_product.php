<style>
  .product-detail-hero {
    background: linear-gradient(135deg, #1e293b, #334155);
    padding: 3rem 1.5rem 2.5rem;
    color: #fff;
    text-align: center;
    border-bottom-left-radius: 35px;
    border-bottom-right-radius: 35px;
    position: relative;
    z-index: 10;
  }

  .product-detail-hero h4 {
    font-weight: 800;
    margin-bottom: 0.75rem;
    letter-spacing: -0.5px;
    line-height: 1.3;
  }

  .category-pill {
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(10px);
    padding: 0.5rem 1.25rem;
    border-radius: 100px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #fff;
    display: inline-block;
    border: 1px solid rgba(255, 255, 255, 0.1);
  }

  .product-content {
    margin-top: -20px;
    padding: 2rem 1.5rem 1.5rem;
    background: #f8fafc;
    min-height: 60vh;
  }

  .price-card {
    background: #fff;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
    border: 1px solid #f1f5f9;
    margin-bottom: 1.25rem;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .price-card:active {
    transform: scale(0.97);
    box-shadow: none;
  }

  .price-card-header {
    background: #f1f5f9;
    padding: 0.8rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    font-size: 0.7rem;
    font-weight: 800;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 1px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .price-card-body {
    padding: 1.25rem 1.5rem;
  }

  .price-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 0.35rem;
  }

  .price-range {
    font-size: 0.85rem;
    color: #64748b;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .price-value-box {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    padding-top: 5px;
  }

  .price-value {
    color: #059669;
    font-weight: 800;
    font-size: 1.35rem;
    line-height: 1;
  }

  .price-unit {
    color: #94a3b8;
    font-size: 0.75rem;
    font-weight: 600;
    padding-bottom: 3px;
  }

  .back-btn {
    background: #fff;
    border: 2px solid #e2e8f0;
    color: #475569;
    font-weight: 700;
    font-size: 0.85rem;
    padding: 0.8rem;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.2s;
    margin-top: 1rem;
  }

  .back-btn:active {
    background: #f1f5f9;
    transform: translateY(2px);
  }
</style>

<div class="product-detail-hero">
  <div class="mb-3">
    <div class="bg-warning bg-opacity-20 rounded-pill d-inline-flex p-3" style="border: 2px solid rgba(255,193,7,0.3)">
      <i class="bi bi-box-seam-fill text-warning fs-1"></i>
    </div>
  </div>
  <h4><?= htmlspecialchars($getProduct['product_nama']) ?></h4>
  <div class="category-pill">
    <i class="bi bi-tag-fill me-1 text-warning"></i>
    <?= htmlspecialchars($getProduct['kategori_nama'] ?? 'Katalog Umum') ?>
  </div>
</div>

<div class="product-content">
  <div class="section-header">
    <div class="section-title"><i class="bi bi-currency-dollar me-2 text-success"></i>Daftar Varian & Harga</div>
    <span class="badge bg-light text-dark shadow-sm border rounded-pill px-3 py-2"
      style="font-size: 0.65rem; font-weight: 700;">
      <?= count($getHarga) ?> PILIHAN
    </span>
  </div>

  <!-- Pricing list -->
  <div class="pricing-list">
    <?php if (!empty($getHarga)): ?>
      <?php foreach ($getHarga as $p): ?>
        <div class="price-card">
          <div class="price-card-header">
            <span>UNIT: <?= htmlspecialchars($p->satuan_nama ?? '-') ?></span>
            <i class="bi bi-chevron-right opacity-50"></i>
          </div>
          <div class="price-card-body">
            <div class="price-title"><?= htmlspecialchars($p->detail_product ?: $getProduct['product_nama']) ?></div>
            <div class="price-range">
              <i class="bi bi-layers-half me-1"></i>
              <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-10 rounded-pill px-2">Range: <?= htmlspecialchars($p->range_text ?? '-') ?></span>
            </div>
            <div class="price-value-box">
              <div class="price-value">Rp <?= number_format($p->harga_1) ?></div>
              <div class="price-unit">Estimasi / <?= $p->satuan_nama ?></div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="text-center py-5 bg-white rounded-4 border border-dashed shadow-sm">
        <i class="bi bi-search text-muted opacity-25" style="font-size: 3.5rem;"></i>
        <h6 class="mt-3 text-muted">Harga Belum Tersedia</h6>
        <p class="small text-muted px-4 mb-0">Silakan hubungi admin atau bagian pemasaran untuk informasi harga produk
          ini.</p>
      </div>
    <?php endif; ?>
  </div>

  <a href="<?= base_url('mobile/product') ?>" class="back-btn shadow-sm">
    <i class="bi bi-arrow-left"></i>
    <span>KEMBALI KE KATALOG</span>
  </a>
</div>