<style>
    body {
        background-color: #ffffff; /* Clean white background */
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        color: #212121;
    }
    
    /* Navbar */
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

    /* Store Profile Header */
    .store-header-container {
        border-bottom: 1px solid #e5e7e9;
        margin-bottom: 20px;
    }
    .store-profile {
        padding: 24px 0;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
    }
    .store-info-left {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .store-logo-wrapper {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        overflow: hidden;
        border: 1px solid #e5e7e9;
        flex-shrink: 0;
    }
    .store-logo-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .store-details h1 {
        font-size: 1.25rem;
        font-weight: 800;
        margin: 0 0 4px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .store-details h1 i.verified-badge {
        color: #8b5cf6; /* Tokopedia official purple-ish or green */
        font-size: 1rem;
    }
    .store-location {
        font-size: 0.85rem;
        color: #6d7588;
        margin-bottom: 12px;
    }
    .store-actions {
        display: flex;
        gap: 12px;
    }
    .btn-follow {
        border: 1px solid #00aa5b;
        color: #00aa5b;
        background: white;
        border-radius: 8px;
        padding: 6px 32px;
        font-weight: 700;
        font-size: 0.85rem;
        transition: background 0.2s;
    }
    .btn-follow:hover {
        background: #f3fcf7;
        color: #00aa5b;
    }
    .btn-chat {
        border: 1px solid #00aa5b;
        color: #00aa5b;
        background: white;
        border-radius: 8px;
        padding: 6px 24px;
        font-weight: 700;
        font-size: 0.85rem;
        transition: background 0.2s;
    }
    .btn-chat:hover {
        background: #f3fcf7;
        color: #00aa5b;
    }
    .btn-icon {
        border: 1px solid #e5e7e9;
        background: white;
        color: #6d7588;
        border-radius: 8px;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }
    
    .store-stats {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
    }
    .store-stats i.star-icon {
        color: #ffc400;
        font-size: 1.1rem;
    }
    .store-stats-value {
        font-weight: 800;
        color: #212121;
    }
    .store-stats-label {
        color: #6d7588;
        font-size: 0.75rem;
        display: block;
        margin-top: 2px;
    }

    /* Tabs */
    .store-tabs {
        display: flex;
        gap: 30px;
        margin-top: 10px;
    }
    .store-tab-item {
        padding: 12px 0;
        color: #6d7588;
        font-weight: 700;
        font-size: 1rem;
        text-decoration: none;
        border-bottom: 3px solid transparent;
        transition: color 0.2s;
    }
    .store-tab-item:hover {
        color: #00aa5b;
    }
    .store-tab-item.active {
        color: #00aa5b;
        border-bottom-color: #00aa5b;
    }

    /* Main Layout */
    .catalog-layout {
        display: flex;
        gap: 30px;
        margin-top: 20px;
        align-items: flex-start;
    }

    /* Sidebar (Etalase) */
    .sidebar-wrapper {
        width: 250px;
        flex-shrink: 0;
        border: 1px solid #e5e7e9;
        border-radius: 12px;
        padding: 16px 0;
        position: sticky;
        top: 80px;
    }
    .sidebar-title {
        font-size: 1rem;
        font-weight: 800;
        color: #212121;
        padding: 0 16px 12px 16px;
        margin: 0;
    }
    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .sidebar-menu li {
        margin: 0;
    }
    .sidebar-menu a {
        display: flex;
        align-items: center;
        padding: 10px 16px;
        color: #31353b;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 600;
        transition: background 0.2s, color 0.2s;
    }
    .sidebar-menu a:hover {
        color: #00aa5b;
        background: #f8fafc;
    }
    .sidebar-menu a.active {
        background: #f3fcf7;
        color: #00aa5b;
        font-weight: 700;
    }
    .sidebar-badge {
        background: #e0004d;
        color: white;
        font-size: 0.6rem;
        font-weight: 800;
        padding: 2px 6px;
        border-radius: 4px;
        margin-left: 8px;
    }

    /* Content Area */
    .content-wrapper {
        flex-grow: 1;
        min-width: 0;
    }
    .content-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .content-title {
        font-size: 1.1rem;
        font-weight: 800;
        margin: 0;
    }
    .sort-control {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.9rem;
        font-weight: 600;
        color: #31353b;
    }
    .sort-control select {
        padding: 6px 30px 6px 12px;
        border-radius: 8px;
        border: 1px solid #e5e7e9;
        font-size: 0.9rem;
        font-weight: 600;
        color: #31353b;
        outline: none;
        appearance: none;
        background: url('data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 10L12 15L17 10" stroke="%236D7588" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>') no-repeat right 8px center;
        background-color: white;
        cursor: pointer;
    }

    /* Product Cards */
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
    .product-discount-tag {
        position: absolute;
        top: 8px;
        left: 0;
        background: #e0004d;
        color: white;
        font-size: 0.75rem;
        font-weight: 800;
        padding: 4px 8px;
        border-radius: 0 8px 8px 0;
        z-index: 2;
    }
    .product-body {
        padding: 12px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .product-title {
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
    .product-price {
        font-size: 1rem;
        font-weight: 800;
        color: #212121;
        margin-bottom: 2px;
    }
    .product-price-strike {
        font-size: 0.75rem;
        color: #6d7588;
        text-decoration: line-through;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .product-discount-label {
        color: #e0004d;
        font-weight: 800;
    }
    .product-bonus {
        font-size: 0.7rem;
        font-weight: 800;
        color: #ff8b00; /* Tokopedia orange */
        margin-bottom: 8px;
    }
    .product-footer {
        margin-top: auto;
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        color: #6d7588;
    }
    .product-footer i {
        color: #ffc400;
    }
    .options-menu {
        position: absolute;
        bottom: 12px;
        right: 12px;
        color: #6d7588;
        font-size: 1.2rem;
    }
    .options-menu:hover {
        color: #00aa5b;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
        border: 1px solid #e5e7e9;
    }

    @media (max-width: 768px) {
        .catalog-layout {
            flex-direction: column;
            gap: 15px;
        }
        .sidebar-wrapper {
            width: 100%;
            position: static;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 0;
        }
        .sidebar-title {
            display: none; /* Hide title on mobile to save space */
        }
        .sidebar-menu {
            display: flex;
            overflow-x: auto;
            gap: 10px;
            padding-bottom: 5px;
        }
        .sidebar-menu::-webkit-scrollbar {
            height: 4px;
        }
        .sidebar-menu::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .sidebar-menu a {
            white-space: nowrap;
            padding: 6px 12px;
            border: 1px solid #e5e7e9;
            border-radius: 20px;
            background: white;
            font-size: 0.85rem;
        }
        .sidebar-menu a.active {
            background: #00aa5b;
            color: white;
            border-color: #00aa5b;
        }
        .sidebar-menu a:hover {
            background: #e5e7e9;
            color: #31353b;
        }
        .sidebar-menu a.active:hover {
            background: #008f4c;
            color: white;
        }
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
        .store-profile {
            flex-direction: column;
            align-items: stretch;
            gap: 20px;
        }
        .store-info-left {
            align-items: flex-start;
        }
        .store-logo-wrapper {
            width: 60px;
            height: 60px;
        }
        .store-details h1 {
            font-size: 1.1rem;
        }
        .store-actions {
            flex-wrap: wrap;
        }
        .btn-follow, .btn-chat {
            flex-grow: 1;
            text-align: center;
        }
        .store-stats {
            display: none;
        }
        .product-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        .product-body {
            padding: 8px;
        }
        .product-title {
            font-size: 0.8rem;
            margin-bottom: 4px;
        }
        .product-price {
            font-size: 0.9rem;
        }
        .product-footer {
            font-size: 0.75rem;
        }
        .content-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
    }
</style>

<div class="public-navbar">
    <div class="container">
        <?php if(!empty($perusahaan['logo'])): ?>
            <img src="<?= base_url('assets/images/' . $perusahaan['logo']) ?>" alt="Logo" class="brand-logo">
        <?php else: ?>
            <h3 class="m-0 fw-bold text-success" style="font-family: 'Nunito', sans-serif; letter-spacing: -1px;"><?= $perusahaan['nama_perusahaan'] ?? 'TGM MERCH' ?></h3>
        <?php endif; ?>
        
        <!-- Navbar Search -->
        <form action="<?= base_url('katalog') ?>" method="GET" class="navbar-search-form d-flex align-items-center flex-grow-1 mx-2 mx-md-4" style="max-width: 600px;">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 rounded-start-pill text-muted px-3" style="border-color: #e5e7e9;">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" name="q" class="form-control border-start-0 rounded-end-pill shadow-none" style="border-color: #e5e7e9; font-size: 0.95rem;" placeholder="Cari produk di TGM Print..." value="<?= htmlspecialchars($q ?? '') ?>">
                <?php if($kategori_id): ?>
                    <input type="hidden" name="kategori_id" value="<?= $kategori_id ?>">
                <?php endif; ?>
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

<div class="store-header-container bg-white">
    <div class="container">
        <div class="store-profile">
            <div class="store-info-left">
                <div class="store-logo-wrapper">
                    <?php if(!empty($perusahaan['logo'])): ?>
                        <img src="<?= base_url('assets/images/' . $perusahaan['logo']) ?>" alt="Logo Toko">
                    <?php else: ?>
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($perusahaan['nama_perusahaan'] ?? 'TGM') ?>&background=1E293B&color=fff&size=150" alt="Logo Toko">
                    <?php endif; ?>
                </div>
                <div class="store-details">
                    <h1>
                        <i class="bi bi-patch-check-fill verified-badge"></i>
                        <?= $perusahaan['nama_perusahaan'] ?? 'Tgm Merch' ?>
                    </h1>
                    <!-- DUMMY DATA FOR LOCATION -->
                    <div class="store-location">Kota Bekasi</div>
                    <div class="store-actions">
                        <button class="btn-follow">Following</button>
                        <button class="btn-chat">Chat Penjual</button>
                        <button class="btn-icon"><i class="bi bi-info-circle"></i></button>
                        <button class="btn-icon"><i class="bi bi-share"></i></button>
                    </div>
                </div>
            </div>

            <!-- DUMMY DATA FOR STATS -->
            <div class="store-stats text-end text-md-start">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-star-fill star-icon"></i>
                    <div class="store-stats-value">
                        4.9 (7.895) &bull; 247 rb terjual
                        <span class="store-stats-label">Rating & Ulasan</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="store-tabs">
            <a href="#" class="store-tab-item">Beranda</a>
            <a href="<?= base_url('katalog') ?>" class="store-tab-item active">Produk</a>
            <a href="#" class="store-tab-item">Ulasan</a>
        </div>
    </div>
</div>

<div class="container pb-5">
    
    <div class="catalog-layout">
        
        <!-- Sidebar (Etalase Toko) -->
        <aside class="sidebar-wrapper">
            <h3 class="sidebar-title">Etalase Toko (<?= count($kategori_list) + 1 ?>)</h3>
            <ul class="sidebar-menu">
                <li>
                    <a href="<?= base_url('katalog'.(!empty($q) ? '?q='.urlencode($q) : '')) ?>" class="<?= empty($kategori_id) ? 'active' : '' ?>">
                        Semua Produk
                    </a>
                </li>
                
                <?php foreach($kategori_list as $kat): ?>
                    <li>
                        <a href="<?= base_url('katalog?kategori_id='.$kat->kategori_id.(!empty($q) ? '&q='.$q : '')) ?>" class="<?= ($kategori_id == $kat->kategori_id) ? 'active' : '' ?>">
                           <?= htmlspecialchars($kat->kategori_nama) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>

        <!-- Product Content -->
        <main class="content-wrapper">
            
            <div class="content-header">
                <?php 
                    $active_kategori_name = 'Semua Produk';
                    if (!empty($kategori_id)) {
                        foreach($kategori_list as $kat) {
                            if($kat->kategori_id == $kategori_id) {
                                $active_kategori_name = $kat->kategori_nama;
                                break;
                            }
                        }
                    }
                ?>
                <h2 class="content-title">
                    <?= htmlspecialchars($active_kategori_name) ?>
                    <?php if(!empty($q)): ?>
                        <span class="text-muted fw-normal fs-6"> - Mencari "<?= htmlspecialchars($q) ?>"</span>
                    <?php endif; ?>
                </h2>

                <div class="sort-control">
                    <span>Urutkan</span>
                    <select>
                        <option>Terbaru</option>
                        <option>Paling Sesuai</option>
                        <option>Harga Tertinggi</option>
                        <option>Harga Terendah</option>
                        <option>Ulasan</option>
                    </select>
                </div>
            </div>

            <?php if(empty($products)): ?>
                <div class="empty-state">
                    <img src="<?= base_url('assets/img/empty.svg') ?>" style="width: 150px; opacity: 0.3" class="mb-3" alt="Empty">
                    <h4 class="text-muted fw-bold">Oops, produk nggak ditemukan</h4>
                    <p class="text-muted">Coba kata kunci lain atau cek etalase toko lainnya.</p>
                    <a href="<?= base_url('katalog') ?>" class="btn btn-success fw-bold px-4 mt-2">Lihat Semua Produk</a>
                </div>
            <?php else: ?>
                <div class="product-grid">
                    <?php foreach($products as $index => $p): ?>
                        <a href="<?= base_url('katalog/produk/'.$p['product_id']) ?>" class="product-card">
                            <div class="product-img-wrapper">
                                <?php 
                                $img = !empty($p['product_img']) && $p['product_img'] != 'official.png' 
                                    ? base_url('assets/images/product/' . $p['product_img']) 
                                    : 'https://loremflickr.com/400/400/printing,design/all?random=' . $p['product_id'];
                                ?>
                                <img src="<?= $img ?>" alt="<?= htmlspecialchars($p['product_nama']) ?>" loading="lazy">
                            </div>

                            <div class="product-body">
                                <h3 class="product-title"><?= htmlspecialchars($p['product_nama']) ?></h3>
                                
                                <div class="product-price">
                                    <?php if(!empty($p['prices'])): ?>
                                        Rp <?= number_format($p['min_price'], 0, ',', '.') ?>
                                    <?php else: ?>
                                        <span class="text-muted fs-6 fw-normal">Harga blm tersedia</span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-stats-card">
                                    <span><i class="bi bi-star-fill text-warning"></i> 4.9</span>
                                    <span><?= isset($p['terjual']) && $p['terjual'] > 0 ? $p['terjual'] . ' terjual' : 'Baru' ?></span>
                                </div>
                                <div style="height: 18px; margin-bottom: 4px;"></div>
                                <div style="height: 14px; margin-bottom: 8px;"></div>

                                <div class="product-footer">
                                    <!-- Dummy Rating & Sold Data -->
                                </div>
                            </div>
                            
                            <!-- Context Menu Icon (Optional) -->
                            <i class="bi bi-three-dots options-menu"></i>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="d-flex justify-content-center mt-5">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-md">
                            <li class="page-item <?= ($current_page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link text-success fw-bold" href="<?= base_url('katalog?page=' . ($current_page - 1) . '&q=' . urlencode($q) . '&kategori_id=' . $kategori_id) ?>">&laquo;</a>
                            </li>
                            
                            <?php
                            $start_page = max(1, $current_page - 2);
                            $end_page = min($total_pages, $current_page + 2);
                            
                            if ($start_page > 1) {
                                echo '<li class="page-item"><a class="page-link text-success" href="'.base_url('katalog?page=1&q=' . urlencode($q) . '&kategori_id=' . $kategori_id).'">1</a></li>';
                                if ($start_page > 2) echo '<li class="page-item disabled"><span class="page-link text-muted border-0">...</span></li>';
                            }

                            for ($i = $start_page; $i <= $end_page; $i++): ?>
                                <li class="page-item <?= ($current_page == $i) ? 'active' : '' ?>">
                                    <a class="page-link <?= ($current_page == $i) ? 'bg-success border-success text-white' : 'text-success' ?> fw-bold" href="<?= base_url('katalog?page=' . $i . '&q=' . urlencode($q) . '&kategori_id=' . $kategori_id) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; 

                            if ($end_page < $total_pages) {
                                if ($end_page < $total_pages - 1) echo '<li class="page-item disabled"><span class="page-link text-muted border-0">...</span></li>';
                                echo '<li class="page-item"><a class="page-link text-success" href="'.base_url('katalog?page='.$total_pages.'&q=' . urlencode($q) . '&kategori_id=' . $kategori_id).'">'.$total_pages.'</a></li>';
                            }
                            ?>

                            <li class="page-item <?= ($current_page >= $total_pages) ? 'disabled' : '' ?>">
                                <a class="page-link text-success fw-bold" href="<?= base_url('katalog?page=' . ($current_page + 1) . '&q=' . urlencode($q) . '&kategori_id=' . $kategori_id) ?>">&raquo;</a>
                            </li>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>
            <?php endif; ?>

        </main>

    </div>
</div>

<footer class="bg-white py-4 border-top mt-5">
    <div class="container text-center text-muted fs-7">
        <p class="mb-0">&copy; <?= date('Y') ?> <?= $perusahaan['nama_perusahaan'] ?? 'Tgm Merch' ?>. All rights reserved.</p>
    </div>
</footer>

<?php $this->load->view('public/cart_ui'); ?>

<!-- Include Bootstrap Bundle JS if needed -->
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script> -->
<script>
    // Example: Highlight selection in Sort dropdown
    document.querySelector('.sort-control select').addEventListener('change', function() {
        console.log('Sort by: ', this.value);
    });
</script>
</body>
</html>
