<style>
    body {
        background-color: #ffffff; /* Tokopedia uses white background for detail page */
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        color: #212121;
    }
    
    /* Navbar (Copied from index for consistency) */
    .public-navbar {
        background: #ffffff;
        box-shadow: 0 1px 6px 0 rgba(49,53,59,0.12);
        padding: 10px 0;
        position: sticky;
        top: 0;
        z-index: 1000;
    }
    .public-navbar .container {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .brand-logo {
        max-height: 40px;
    }

    /* Breadcrumb */
    .breadcrumb-custom {
        padding: 20px 0 10px 0;
        font-size: 0.85rem;
        color: #00aa5b;
        font-weight: 600;
    }
    .breadcrumb-custom a {
        color: #00aa5b;
        text-decoration: none;
    }
    .breadcrumb-custom .separator {
        margin: 0 8px;
        color: #9fa6b0;
        font-weight: 400;
    }
    .breadcrumb-custom .current-page {
        color: #31353b;
        font-weight: 400;
    }

    /* Layout */
    .detail-layout {
        display: flex;
        gap: 30px;
        align-items: flex-start;
        margin-top: 15px;
    }
    .col-image {
        width: 348px;
        flex-shrink: 0;
    }
    .col-info {
        flex-grow: 1;
        min-width: 0;
    }
    .col-action {
        width: 320px;
        flex-shrink: 0;
    }

    /* Image Gallery */
    .main-image-wrapper {
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e5e7e9;
        position: relative;
        padding-top: 100%; /* Square */
    }
    .main-image-wrapper img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .thumbnail-list {
        display: flex;
        gap: 12px;
        margin-top: 12px;
    }
    .thumbnail-item {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        border: 1px solid #e5e7e9;
        overflow: hidden;
        cursor: pointer;
    }
    .thumbnail-item.active {
        border: 2px solid #00aa5b;
    }
    .thumbnail-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Product Info Area */
    .product-title {
        font-size: 1.3rem;
        font-weight: 800;
        color: #212121;
        margin: 0 0 10px 0;
        line-height: 1.4;
    }
    .product-stats {
        display: flex;
        align-items: center;
        gap: 15px;
        font-size: 0.85rem;
        color: #31353b;
        margin-bottom: 15px;
    }
    .product-stats .rating {
        font-weight: 800;
    }
    .product-stats .rating i {
        color: #ffc400;
    }
    .product-price {
        font-size: 2rem;
        font-weight: 800;
        color: #212121;
        margin-bottom: 20px;
    }
    
    /* Info Tabs */
    .info-tabs {
        display: flex;
        gap: 30px;
        border-bottom: 1px solid #e5e7e9;
        margin-top: 20px;
        margin-bottom: 20px;
    }
    .info-tab-item {
        padding: 12px 0;
        color: #6d7588;
        font-weight: 700;
        font-size: 1rem;
        text-decoration: none;
        border-bottom: 3px solid transparent;
        transition: color 0.2s;
    }
    .info-tab-item.active {
        color: #00aa5b;
        border-bottom-color: #00aa5b;
    }

    /* Meta Info */
    .meta-list {
        list-style: none;
        padding: 0;
        margin: 0 0 20px 0;
        font-size: 0.9rem;
        color: #31353b;
    }
    .meta-list li {
        margin-bottom: 8px;
    }
    .meta-list span.label {
        color: #6d7588;
        display: inline-block;
        width: 100px;
    }
    .meta-list a {
        color: #00aa5b;
        font-weight: 700;
        text-decoration: none;
    }

    /* Description */
    .product-desc {
        font-size: 0.95rem;
        color: #31353b;
        line-height: 1.6;
        white-space: pre-line;
        margin-bottom: 30px;
    }

    /* Price Table */
    .price-table-container {
        margin-top: 20px;
        margin-bottom: 30px;
    }
    .price-table-title {
        font-size: 1.1rem;
        font-weight: 800;
        margin-bottom: 12px;
        color: #212121;
    }
    .price-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }
    .price-table th, .price-table td {
        padding: 12px 16px;
        border: 1px solid #e5e7e9;
    }
    .price-table th {
        background: #f8fafc;
        color: #6d7588;
        font-weight: 700;
        text-align: left;
    }
    .price-table td {
        color: #31353b;
    }
    .variant-name {
        font-weight: 700;
    }
    .variant-meta {
        font-size: 0.8rem;
        color: #6d7588;
        margin-top: 4px;
    }
    .price-val {
        font-weight: 800;
        color: #00aa5b;
    }

    /* Floating Action Card */
    .action-card {
        border: 1px solid #e5e7e9;
        border-radius: 12px;
        padding: 16px;
        background: white;
        position: sticky;
        top: 80px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
    }
    .action-card-title {
        font-size: 1rem;
        font-weight: 800;
        margin-bottom: 16px;
    }
    .qty-control {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 16px;
    }
    .qty-input-group {
        display: flex;
        align-items: center;
        border: 1px solid #e5e7e9;
        border-radius: 8px;
        overflow: hidden;
    }
    .qty-btn {
        width: 32px;
        height: 32px;
        background: white;
        border: none;
        color: #00aa5b;
        font-weight: 800;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .qty-input {
        width: 40px;
        height: 32px;
        border: none;
        border-left: 1px solid #e5e7e9;
        border-right: 1px solid #e5e7e9;
        text-align: center;
        font-weight: 600;
        outline: none;
    }
    .stok-info {
        font-size: 0.85rem;
        color: #31353b;
    }
    .subtotal-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .subtotal-label {
        color: #6d7588;
        font-size: 0.9rem;
    }
    .subtotal-val {
        font-weight: 800;
        font-size: 1.1rem;
        color: #212121;
    }
    
    /* Buttons */
    .btn-tokopedia {
        display: block;
        width: 100%;
        text-align: center;
        padding: 10px;
        border-radius: 8px;
        font-weight: 800;
        font-size: 0.95rem;
        text-decoration: none;
        margin-bottom: 10px;
        transition: opacity 0.2s;
        border: none;
        cursor: pointer;
    }
    .btn-tokopedia:hover {
        opacity: 0.9;
        color: white;
    }
    .btn-keranjang {
        background: #00aa5b;
        color: white;
    }
    .btn-beli {
        background: white;
        color: #00aa5b;
        border: 1px solid #00aa5b;
    }
    .btn-beli:hover {
        color: #00aa5b;
        background: #f3fcf7;
    }
    .btn-wa {
        background: #25d366;
        color: white;
    }
    .btn-pdf {
        background: #1e293b;
        color: white;
    }

    /* Action Links Footer */
    .action-links {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #e5e7e9;
        font-size: 0.85rem;
        font-weight: 700;
        color: #31353b;
    }
    .action-links div {
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .action-links div:hover {
        color: #00aa5b;
    }

    /* Product Cards (Similar Products) */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 16px;
    }
    .product-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 6px 0 rgba(49,53,59,0.12);
        display: flex;
        flex-direction: column;
        text-decoration: none;
        color: inherit;
        position: relative;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px 0 rgba(49,53,59,0.16);
        color: inherit;
    }
    .product-img-wrapper {
        position: relative;
        padding-top: 100%; /* Square */
        background: #f1f5f9;
        overflow: hidden;
    }
    .product-img-wrapper img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .product-body-card {
        padding: 12px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .product-title-card {
        font-size: 0.9rem;
        line-height: 1.4;
        color: #212121;
        margin: 0 0 8px 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .product-price-card {
        font-size: 1rem;
        font-weight: 800;
        color: #212121;
        margin-bottom: 2px;
    }
    .product-stats-card {
        font-size: 0.8rem;
        color: #6d7588;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: auto;
    }

    @media (max-width: 992px) {
        .detail-layout {
            flex-direction: column;
        }
        .col-image, .col-info, .col-action {
            width: 100%;
        }
        .action-card {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .public-navbar .container {
            flex-wrap: wrap;
            padding-top: 10px;
            padding-bottom: 10px;
        }
        .navbar-search-form {
            width: 100%;
            order: 3;
            margin: 10px 0 0 0 !important;
            max-width: 100% !important;
        }
        .navbar-icons {
            margin-left: auto;
        }
        .btn-chat-wa {
            padding: 0.4rem !important;
            border-radius: 50% !important;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-chat-wa span {
            display: none !important;
        }
        .btn-chat-wa i {
            margin: 0 !important;
            font-size: 1.2rem;
        }
        .product-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        .product-body-card {
            padding: 8px;
        }
        .product-title-card {
            font-size: 0.8rem;
            margin-bottom: 4px;
        }
        .product-price-card {
            font-size: 0.9rem;
        }
        .product-stats-card {
            font-size: 0.75rem;
        }
    }
</style>

<div class="public-navbar">
    <div class="container">
        <a href="<?= base_url('katalog') ?>" style="text-decoration: none;">
            <?php if(!empty($perusahaan['logo'])): ?>
                <img src="<?= base_url('assets/images/' . $perusahaan['logo']) ?>" alt="Logo" class="brand-logo">
            <?php else: ?>
                <h3 class="m-0 fw-bold text-success" style="font-family: 'Nunito', sans-serif; letter-spacing: -1px;"><?= $perusahaan['nama_perusahaan'] ?? 'TGM MERCH' ?></h3>
            <?php endif; ?>
        </a>
        <!-- Navbar Search -->
        <form action="<?= base_url('katalog') ?>" method="GET" class="navbar-search-form d-flex align-items-center flex-grow-1 mx-2 mx-md-4" style="max-width: 600px;">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 rounded-start-pill text-muted px-3" style="border-color: #e5e7e9;">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" name="q" class="form-control border-start-0 rounded-end-pill shadow-none" style="border-color: #e5e7e9; font-size: 0.95rem;" placeholder="Cari produk di toko ini..." value="">
            </div>
        </form>

        <div class="navbar-icons d-flex align-items-center gap-2 gap-md-3">
            <button class="btn border-0 position-relative p-2" onclick="openCart()" style="color: #6d7588;">
                <i class="bi bi-cart3 fs-4"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge" style="display: none; font-size: 0.7rem;">
                    0
                </span>
            </button>
            <?php 
            $wa_number = !empty($perusahaan['no_wa']) ? $perusahaan['no_wa'] : (!empty($perusahaan['telepon']) ? $perusahaan['telepon'] : '');
            if(!empty($wa_number)): 
                $wa_number = preg_replace('/[^0-9]/', '', $wa_number);
                if(substr($wa_number, 0, 1) == '0') $wa_number = '62' . substr($wa_number, 1);
            ?>
                <a href="https://wa.me/<?= $wa_number ?>" target="_blank" class="btn-chat-wa btn btn-outline-success rounded-pill d-flex align-items-center gap-2 px-3 fw-bold" style="font-size: 0.9rem;">
                    <i class="bi bi-whatsapp"></i> <span>Chat WhatsApp</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container pb-5">
    
    <div class="breadcrumb-custom">
        <a href="<?= base_url('katalog') ?>">Beranda</a>
        <span class="separator">/</span>
        <a href="<?= base_url('katalog?kategori_id=' . $product['kategori_id']) ?>"><?= htmlspecialchars($product['kategori_nama']) ?></a>
        <span class="separator">/</span>
        <span class="current-page"><?= htmlspecialchars($product['product_nama']) ?></span>
    </div>

    <div class="detail-layout">
        
        <!-- Left: Image Gallery -->
        <div class="col-image">
            <div class="main-image-wrapper">
                <?php 
                $img = !empty($product['product_img']) && $product['product_img'] != 'official.png' 
                    ? base_url('assets/images/product/' . $product['product_img']) 
                    : 'https://loremflickr.com/600/600/printing,design/all?random=' . $product['product_id'];
                ?>
                <img src="<?= $img ?>" alt="<?= htmlspecialchars($product['product_nama']) ?>">
            </div>
        </div>

        <!-- Middle: Product Content -->
        <div class="col-info">
            <h1 class="product-title"><?= htmlspecialchars($product['product_nama']) ?></h1>
            
            <div class="product-stats">
                <span class="rating"><i class="bi bi-star-fill"></i> 4.9</span>
                <span>(1.234 rating)</span>
                <span>&bull;</span>
                <span><?= isset($product['terjual']) && $product['terjual'] > 0 ? $product['terjual'] . ' terjual' : 'Baru' ?></span>
            </div>

            <div class="product-price">
                <?php if(!empty($product['prices'])): ?>
                    <?php if($product['min_price'] != $product['max_price']): ?>
                        Rp <?= number_format($product['min_price'], 0, ',', '.') ?> - Rp <?= number_format($product['max_price'], 0, ',', '.') ?>
                    <?php else: ?>
                        Rp <?= number_format($product['min_price'], 0, ',', '.') ?>
                    <?php endif; ?>
                <?php else: ?>
                    Harga Belum Tersedia
                <?php endif; ?>
            </div>

            <div class="info-tabs">
                <a href="javascript:void(0)" class="info-tab-item active" onclick="switchTab('detail', this)">Detail Produk</a>
                <a href="javascript:void(0)" class="info-tab-item" onclick="switchTab('info', this)">Info Penting</a>
            </div>

            <ul class="meta-list">
                <li><span class="label">Kondisi:</span> Baru</li>
                <li><span class="label">Berat Satuan:</span> 100 g</li>
                <li><span class="label">Min. Beli:</span> 1 <?= htmlspecialchars($product['satuan_nama']) ?></li>
                <li><span class="label">Kategori:</span> <a href="<?= base_url('katalog?kategori_id=' . $product['kategori_id']) ?>"><?= htmlspecialchars($product['kategori_nama']) ?></a></li>
                <li><span class="label">Etalase:</span> <a href="<?= base_url('katalog') ?>">Semua Etalase</a></li>
            </ul>

            <div id="tab-detail" class="tab-content">
                <div class="product-desc">
                    <?= !empty($product['product_deskripsi']) ? $product['product_deskripsi'] : 'Tidak ada deskripsi.' ?>
                </div>
            </div>

            <div id="tab-info" class="tab-content" style="display: none;">
                <div class="product-desc">
                    <?= !empty($product['product_deskripsi']) ? $product['product_deskripsi'] : 'Tidak ada info penting.' ?>
                </div>
            </div>

            <?php if(!empty($product['prices'])): ?>
            <div class="price-table-container">
                <h3 class="price-table-title">Daftar Harga Grosir & Spesifikasi</h3>
                <div style="overflow-x: auto; border-radius: 8px; border: 1px solid #e5e7e9;">
                    <table class="price-table">
                        <thead>
                            <tr>
                                <th>Spesifikasi / Variasi</th>
                                <th style="text-align: center;">Jumlah (<?= htmlspecialchars($product['satuan_nama']) ?>)</th>
                                <th style="text-align: right;">Harga Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($product['prices'] as $pr): ?>
                            <tr>
                                <td>
                                    <div class="variant-name"><?= $pr['detail_product'] ?: $product['product_nama'] ?></div>
                                    <div class="variant-meta">
                                        <?php 
                                        $infos = [];
                                        if(!empty($pr['metadata'])) {
                                            $meta = json_decode($pr['metadata'], true);
                                            if(is_array($meta)) {
                                                foreach($meta as $val) {
                                                    // Remove brackets if they exist for cleaner display
                                                    $infos[] = trim($val, '[]');
                                                }
                                            }
                                        } else {
                                            if(!empty($pr['ket_1_label'])) $infos[] = $pr['ket_1_label'];
                                            if(!empty($pr['jenisprint_label'])) $infos[] = $pr['jenisprint_label'];
                                        }
                                        echo implode(' &bull; ', $infos);
                                        ?>
                                    </div>
                                </td>
                                <td style="text-align: center; font-weight: 600;">
                                    <?= number_format($pr['qty_min'], 0, ',', '.') ?> - <?= number_format($pr['qty_max'], 0, ',', '.') ?>
                                </td>
                                <td style="text-align: right;" class="price-val">
                                    Rp <?= number_format($pr['harga_1'], 0, ',', '.') ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

        </div>

        <!-- Right: Floating Action Card -->
        <div class="col-action">
            <div class="action-card">
                <h3 class="action-card-title">Atur jumlah dan catatan</h3>
                
                <div class="d-flex align-items-center gap-2 mb-3">
                    <img src="<?= $img ?>" alt="thumb" style="width: 40px; height: 40px; border-radius: 4px; object-fit: cover;">
                    <span style="font-size: 0.9rem; font-weight: 600;"><?= htmlspecialchars(substr($product['product_nama'], 0, 20)) ?>...</span>
                </div>

                <div class="qty-control">
                    <div class="qty-input-group">
                        <button class="qty-btn" onclick="updateQty(-1)">&minus;</button>
                        <input type="number" id="order-qty" class="qty-input" value="1" min="1">
                        <button class="qty-btn" onclick="updateQty(1)">+</button>
                    </div>
                </div>

                <!-- Real Action Buttons -->
                <a href="<?= base_url('katalog/cetak/' . $product['product_id'] . '?print=1') ?>" target="_blank" class="btn-tokopedia btn-pdf mb-3">
                    <i class="bi bi-printer-fill me-1"></i> Cetak Brosur PDF
                </a>

                <?php 
                $wa_number = !empty($perusahaan['no_wa']) ? $perusahaan['no_wa'] : (!empty($perusahaan['telepon']) ? $perusahaan['telepon'] : '');
                if(!empty($wa_number)): 
                    $wa_number = preg_replace('/[^0-9]/', '', $wa_number);
                    if(substr($wa_number, 0, 1) == '0') $wa_number = '62' . substr($wa_number, 1);
                    $product_url = base_url('katalog/produk/' . $product['product_id']);
                ?>
                <div style="display: flex; gap: 10px;">
                    <button onclick="addToCart({id: '<?= $product['product_id'] ?>', name: '<?= addslashes($product['product_nama']) ?>', img: '<?= $img ?>', url: '<?= $product_url ?>', qty: document.getElementById('order-qty').value})" class="btn-tokopedia btn-beli" style="margin-bottom: 0;">
                        + Keranjang
                    </button>
                    <button onclick="sendToWA('<?= $wa_number ?>', '<?= $product['product_id'] ?>')" class="btn-tokopedia btn-wa" style="margin-bottom: 0;">
                        <i class="bi bi-whatsapp"></i> Beli
                    </button>
                </div>
                <?php endif; ?>

                <div class="mt-4 p-3 rounded" style="background-color: #f8fafc; border: 1px dashed #cbd5e1; font-size: 0.85rem; color: #475569;">
                    <strong class="d-block mb-2" style="color: #0f172a;"><i class="bi bi-info-circle me-1"></i> Panduan Pemesanan (SOP):</strong>
                    <ol class="ps-3 mb-0" style="line-height: 1.6;">
                        <li>Pilih produk dan tentukan <strong>Jumlah Pesanan</strong>.</li>
                        <li>Klik <strong>+ Keranjang</strong> jika ingin menambah produk lain, atau <strong>Beli</strong> untuk pesan 1 jenis.</li>
                        <li>Klik tombol hijau <strong>Checkout via WhatsApp</strong> di Keranjang.</li>
                        <li>Sistem akan membuatkan draf pesan otomatis, silakan kirim ke WA Admin kami.</li>
                        <li>Admin akan merespons dengan total biaya & panduan pembayaran.</li>
                    </ol>
                </div>
            </div>
        </div>

    </div>
    
    <?php if(!empty($similar_products)): ?>
    <div class="mt-5 pt-4 border-top">
        <h3 class="mb-4 fw-bold" style="font-size: 1.3rem;">Produk Sejenis yang Mungkin Anda Suka</h3>
        <div class="product-grid">
            <?php foreach($similar_products as $sp): ?>
                <?php 
                $sp_img = !empty($sp['product_img']) && $sp['product_img'] != 'official.png' 
                    ? base_url('assets/images/product/' . $sp['product_img']) 
                    : 'https://loremflickr.com/300/300/printing,design/all?random=' . $sp['product_id'];
                ?>
                <a href="<?= base_url('katalog/produk/' . $sp['product_id']) ?>" class="product-card">
                    <div class="product-img-wrapper">
                        <img src="<?= $sp_img ?>" alt="<?= htmlspecialchars($sp['product_nama']) ?>" loading="lazy">
                    </div>
                    <div class="product-body-card">
                        <h3 class="product-title-card"><?= htmlspecialchars($sp['product_nama']) ?></h3>
                        <div class="product-price-card">
                            <?php if($sp['min_price'] && $sp['max_price']): ?>
                                <?php if($sp['min_price'] != $sp['max_price']): ?>
                                    Rp <?= number_format($sp['min_price'], 0, ',', '.') ?> - Rp <?= number_format($sp['max_price'], 0, ',', '.') ?>
                                <?php else: ?>
                                    Rp <?= number_format($sp['min_price'], 0, ',', '.') ?>
                                <?php endif; ?>
                            <?php else: ?>
                                Harga Hubungi Admin
                            <?php endif; ?>
                        </div>
                        <div class="product-stats-card">
                            <span><i class="bi bi-star-fill text-warning"></i> 5.0</span>
                            <span><?= isset($sp['terjual']) && $sp['terjual'] > 0 ? $sp['terjual'] . ' terjual' : 'Baru' ?></span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<!-- Footer Simple -->
<footer class="bg-white py-4 border-top mt-5">
    <div class="container text-center text-muted fs-7">
        <p class="mb-0">&copy; <?= date('Y') ?> <?= $perusahaan['nama_perusahaan'] ?? 'Tgm Merch' ?>. All rights reserved.</p>
    </div>
</footer>

<?php $this->load->view('public/cart_ui'); ?>

<script>
    function switchTab(tabId, el) {
        // Remove active class from all tabs
        document.querySelectorAll('.info-tab-item').forEach(function(tab) {
            tab.classList.remove('active');
        });
        // Add active class to clicked tab
        el.classList.add('active');
        
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(function(content) {
            content.style.display = 'none';
        });
        
        // Show the target tab content
        document.getElementById('tab-' + tabId).style.display = 'block';
    }

    function updateQty(change) {
        let input = document.getElementById('order-qty');
        let current = parseInt(input.value) || 1;
        let newVal = current + change;
        if(newVal < 1) newVal = 1;
        input.value = newVal;
    }

    function sendToWA(phone, productId) {
        let qty = document.getElementById('order-qty').value || 1;
        
        let orderLink = `<?= base_url('katalog/pesanan?cart=') ?>${productId}-${qty}`;

        let text = `Halo Admin, saya ingin memesan produk.\n\n`;
        text += `Berikut adalah rincian pesanan saya:\n`;
        text += `${orderLink}\n\n`;
        text += `Mohon dibantu untuk rekap total biaya dan pembayarannya ya. Terima kasih!`;

        let waLink = `https://wa.me/${phone}?text=${encodeURIComponent(text)}`;
        window.open(waLink, '_blank');
    }
</script>

</body>
</html>
