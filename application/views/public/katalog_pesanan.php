<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - <?= htmlspecialchars($perusahaan['nama_perusahaan'] ?? 'TGM Print') ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f3f4f5;
            color: #31353b;
        }
        .header {
            background: white;
            padding: 15px 0;
            box-shadow: 0 1px 6px 0 rgba(49,53,59,0.12);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .brand-logo {
            height: 40px;
            object-fit: contain;
        }
        .order-container {
            max-width: 800px;
            margin: 30px auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 6px 0 rgba(49,53,59,0.12);
            overflow: hidden;
        }
        .order-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7e9;
            background: #fafafa;
        }
        .order-header h4 {
            margin: 0;
            font-weight: 800;
            color: #212121;
        }
        .order-body {
            padding: 24px;
        }
        .product-item {
            display: flex;
            gap: 16px;
            padding-bottom: 20px;
            margin-bottom: 20px;
            border-bottom: 1px dashed #e5e7e9;
        }
        .product-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .product-img {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #e5e7e9;
        }
        .product-info {
            flex-grow: 1;
        }
        .product-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: #212121;
            margin-bottom: 4px;
        }
        .product-price {
            font-size: 0.95rem;
            color: #6d7588;
            margin-bottom: 8px;
        }
        .product-qty {
            font-weight: 800;
            color: #00aa5b;
            background: #e8f7f0;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-block;
        }
        .order-footer {
            padding: 20px 24px;
            background: #fafafa;
            border-top: 1px solid #e5e7e9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-katalog {
            background: #00aa5b;
            color: white;
            font-weight: 700;
            padding: 10px 24px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            transition: opacity 0.2s;
        }
        .btn-katalog:hover {
            opacity: 0.9;
            color: white;
        }
        .total-label {
            font-size: 1rem;
            color: #6d7588;
            font-weight: 600;
        }
        .total-value {
            font-size: 1.2rem;
            font-weight: 800;
            color: #212121;
        }
        
        @media (max-width: 768px) {
            .order-container {
                margin: 0;
                border-radius: 0;
                box-shadow: none;
                min-height: calc(100vh - 70px);
            }
        }
    </style>
</head>
<body>

<div class="header">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="<?= base_url('katalog') ?>" style="text-decoration: none;">
            <?php if(!empty($perusahaan['logo'])): ?>
                <img src="<?= base_url('assets/images/' . $perusahaan['logo']) ?>" alt="Logo" class="brand-logo">
            <?php else: ?>
                <h3 class="m-0 fw-bold text-success" style="font-family: 'Nunito', sans-serif; letter-spacing: -1px;"><?= $perusahaan['nama_perusahaan'] ?? 'TGM MERCH' ?></h3>
            <?php endif; ?>
        </a>
    </div>
</div>

<div class="container px-0 px-md-3">
    <div class="order-container">
        <div class="order-header d-flex justify-content-between align-items-center">
            <h4><i class="bi bi-receipt me-2 text-success"></i> <?= htmlspecialchars($title) ?></h4>
            <span class="badge bg-success rounded-pill px-3 py-2" style="font-size: 0.9rem;">
                <?= count($items) ?> Jenis Produk
            </span>
        </div>
        
        <div class="order-body">
            <?php if(empty($items)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-cart-x text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">Pesanan tidak ditemukan atau link tidak valid.</h5>
                    <a href="<?= base_url('katalog') ?>" class="btn btn-outline-success mt-3 rounded-pill fw-bold px-4">Kembali ke Katalog</a>
                </div>
            <?php else: ?>
                <?php foreach($items as $item): 
                    $img = !empty($item['product_img']) ? base_url('uploads/product/'.$item['product_img']) : base_url('assets/images/no-image.png');
                    
                    $price_str = '';
                    if ($item['min_price'] && $item['max_price']) {
                        if ($item['min_price'] == $item['max_price']) {
                            $price_str = 'Rp ' . number_format($item['min_price'], 0, ',', '.');
                        } else {
                            $price_str = 'Rp ' . number_format($item['min_price'], 0, ',', '.') . ' - ' . number_format($item['max_price'], 0, ',', '.');
                        }
                    } else {
                        $price_str = 'Harga hubungi admin';
                    }
                ?>
                <div class="product-item">
                    <img src="<?= $img ?>" alt="Product Image" class="product-img">
                    <div class="product-info">
                        <div class="product-name"><?= htmlspecialchars($item['product_nama']) ?></div>
                        <div class="product-price"><?= $price_str ?></div>
                        <div class="product-qty">
                            <i class="bi bi-box-seam me-1"></i> Jumlah: <?= $item['qty'] ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php if(!empty($items)): ?>
        <div class="order-footer">
            <div>
                <div class="total-label">Total Barang</div>
                <div class="total-value"><?= $total_items ?> Pcs</div>
            </div>
            <a href="<?= base_url('katalog') ?>" class="btn-katalog">
                Katalog <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
