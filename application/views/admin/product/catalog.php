<style>
    :root {
        --premium-gradient: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        --soft-blue: #f8fafc;
        --accent-indigo: #6366f1;
    }

    .catalog-header {
        background: var(--premium-gradient);
        padding: 3rem 0;
        margin-bottom: 2rem;
        color: white;
        border-radius: 0 0 2rem 2rem;
    }

    .catalog-card {
        border: none;
        border-radius: 1.25rem;
        transition: all 0.3s ease;
        overflow: hidden;
        background: white;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .catalog-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .product-img-container {
        position: relative;
        padding-top: 75%; /* 4:3 Aspect Ratio */
        background: #f1f5f9;
        overflow: hidden;
    }

    .product-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .catalog-card:hover .product-img {
        transform: scale(1.1);
    }

    .category-badge {
        position: absolute;
        top: 1rem;
        left: 1rem;
        background: rgba(15, 23, 42, 0.8);
        backdrop-filter: blur(4px);
        color: white;
        padding: 0.35rem 0.75rem;
        border-radius: 50rem;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        z-index: 10;
    }

    .price-tag {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--accent-indigo);
    }

    .price-table {
        font-size: 0.75rem;
        margin-top: 1rem;
        border-radius: 0.75rem;
        overflow: hidden;
        border: 1px solid #f1f5f9;
    }

    .price-table th {
        background: #f8fafc;
        color: #64748b;
        font-weight: 700;
        padding: 0.5rem;
        text-transform: uppercase;
        font-size: 0.65rem;
    }

    .price-table td {
        padding: 0.5rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .search-container {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        padding: 1.5rem;
        border-radius: 1rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .bg-soft-indigo { background: #eef2ff !important; }
    .text-indigo { color: #4f46e5 !important; }
    .bg-soft-emerald { background: #ecfdf5 !important; }
    .text-emerald { color: #10b981 !important; }
    .border-faint { border-color: #f1f5f9 !important; }
    
    .x-small { font-size: 0.65rem; }
    .fw-900 { font-weight: 900; }
    
    .modal-content {
        border: none;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        border-radius: 1.5rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .form-control-premium {
        background: white;
        border: none;
        padding: 0.75rem 1.25rem;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .empty-catalog {
        padding: 5rem 0;
        text-align: center;
        color: #64748b;
    }

    /* Pagination Styling */
    .pagination-pro .page-link {
        border: none;
        padding: 0.75rem 1.25rem;
        margin: 0 0.25rem;
        border-radius: 0.75rem;
        color: #64748b;
        font-weight: 600;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .pagination-pro .page-link:hover {
        background: #f1f5f9;
        color: var(--accent-indigo);
    }

    .pagination-pro .page-item.active .page-link {
        background: var(--accent-indigo);
        color: white;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }

    .pagination-pro .page-item.disabled .page-link {
        background: transparent;
        opacity: 0.4;
    }
</style>

<div class="app-content p-0">
    <div class="catalog-header">
        <div class="container py-4">
            <div class="row align-items-center mb-4">
                <div class="col-md-6">
                    <h1 class="fw-bold mb-1">Katalog Produk</h1>
                    <p class="opacity-75 mb-0">Daftar harga lengkap dan spesifikasi produk manufaktur</p>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0 d-flex justify-content-md-end gap-2">
                    <a href="<?= base_url('manufaktur/catalog_print?q='.$q.'&kategori_id='.$kategori_id) ?>" target="_blank" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center">
                        <i class="bi bi-file-earmark-pdf-fill me-2 fs-5"></i> Download PDF Katalog
                    </a>
                    <a href="<?= base_url('manufaktur') ?>" class="btn btn-outline-light rounded-pill px-4 d-flex align-items-center">
                        <i class="bi bi-arrow-left me-2"></i> Dashboard
                    </a>
                </div>
            </div>

            <div class="search-container">
                <form action="<?= base_url('manufaktur/catalog') ?>" method="GET" class="row g-3">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-0 rounded-start-pill ps-3">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" name="q" class="form-control border-0 rounded-end-pill py-2" 
                                   placeholder="Cari nama produk..." value="<?= $q ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="kategori_id" class="form-select border-0 rounded-pill py-2">
                            <option value="">Semua Kategori</option>
                            <?php foreach($kategori_list as $k): ?>
                                <option value="<?= $k->kategori_id ?>" <?= $kategori_id == $k->kategori_id ? 'selected' : '' ?>>
                                    <?= $k->kategori_nama ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold">
                            Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <?php if(empty($products)): ?>
            <div class="empty-catalog">
                <i class="bi bi-folder-x fs-1 opacity-25 d-block mb-3"></i>
                <h3>Produk tidak ditemukan</h3>
                <p>Coba gunakan kata kunci pencarian atau kategori lain.</p>
                <a href="<?= base_url('manufaktur/catalog') ?>" class="btn btn-link">Reset Pencarian</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach($products as $p): ?>
                    <div class="col-lg-3 col-md-4">
                        <div class="catalog-card shadow-sm border border-faint">
                            <div class="product-img-container">
                                <span class="category-badge"><?= $p['kategori_nama'] ?></span>
                                <a href="<?= base_url('manufaktur/uploadproduct/'.$p['product_id']) ?>" class="btn btn-sm btn-light position-absolute shadow-sm" style="top: 10px; right: 10px; z-index: 10; border-radius: 50%; width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;" title="Ubah Gambar Produk">
                                    <i class="bi bi-camera-fill text-primary"></i>
                                </a>
                                <img src="<?= (!empty($p['product_img']) && $p['product_img'] != 'official.png') ? base_url('assets/images/product/'.$p['product_img']) : 'https://loremflickr.com/600/450/'.urlencode($p['kategori_nama'].','.$p['product_nama']).'?random='.rand(1,100) ?>" 
                                     class="product-img" alt="<?= $p['product_nama'] ?>" loading="lazy" onerror="this.src='https://placehold.co/600x450?text=No+Image'">
                            </div>
                            
                            <div class="p-4 flex-grow-1">
                                <div class="mb-1 text-uppercase text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">
                                    <?= $p['subkategori_nama'] ?>
                                </div>
                                <h4 class="fw-bold text-slate-900 mb-2"><?= $p['product_nama'] ?></h4>
                                
                                <div class="d-flex align-items-center justify-content-between mt-3">
                                    <div class="price-tag">
                                        <?php if($p['min_price'] > 0): ?>
                                            <span style="font-size: 0.7rem; color: #64748b; font-weight: 600; text-transform: uppercase; display: block;">Mulai dari</span>
                                            Rp <?= number_format($p['min_price'], 0, ',', '.') ?>
                                        <?php else: ?>
                                            <span class="text-muted" style="font-size: 0.9rem;">Harga Belum Setup</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-light text-slate-600 rounded-pill" style="font-size: 0.7rem; border: 1px solid #e2e8f0;">
                                            Per <?= $p['satuan_nama'] ?>
                                        </span>
                                    </div>
                                </div>

                                <?php if(!empty($p['prices'])): ?>
                                    <table class="table price-table mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Range (Qty)</th>
                                                <th class="text-end">Harga Unit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach(array_slice($p['prices'], 0, 3) as $pr): ?>
                                                <tr>
                                                    <td class="text-center fw-medium"><?= number_format($pr['qty_min'], 0, ',', '.') ?> - <?= number_format($pr['qty_max'], 0, ',', '.') ?></td>
                                                    <td class="text-end fw-bold text-slate-800">Rp <?= number_format($pr['harga_1'], 0, ',', '.') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <?php if(count($p['prices']) > 3): ?>
                                                <tr>
                                                    <td colspan="2" class="text-center text-muted italic" style="font-size: 0.65rem;">
                                                        + <?= count($p['prices']) - 3 ?> Qty range lainnya...
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                            
                            <div class="p-3 bg-light border-top d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-indigo text-accent-indigo fw-bold flex-grow-1 py-2" 
                                        style="background: #eef2ff;" data-bs-toggle="modal" data-bs-target="#modalPrices<?= $p['product_id'] ?>">
                                    <i class="bi bi-tag me-1"></i> Daftar Harga
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary fw-bold py-2 px-3" 
                                        onclick="copyLinkPublik('<?= base_url('katalog/produk/'.$p['product_id']) ?>', this)" title="Copy Link Katalog Publik">
                                    <i class="bi bi-link-45deg"></i>
                                </button>
                                <?php 
                                $wa_text = "Halo Kak! Berikut adalah spesifikasi produk dan daftar harga untuk *". $p['product_nama'] ."* dari katalog kami. \n\nSilakan klik link brosur di bawah ini untuk melihat detail lengkapnya:\n" . base_url('katalog/produk/'.$p['product_id']) . "\n\nTerima kasih!";
                                ?>
                                <a href="https://wa.me/?text=<?= urlencode($wa_text) ?>" target="_blank" class="btn btn-sm btn-success fw-bold py-2 px-3" title="Kirim penawaran via WhatsApp">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination & Info -->
            <div class="row mt-5 mb-4 align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="text-muted small mb-0">
                        Menampilkan <span class="fw-bold text-dark"><?= count($products) ?></span> dari <span class="fw-bold text-dark"><?= $total_products ?></span> produk
                    </p>
                </div>
                <div class="col-md-6">
                    <?php if($total_pages > 1): ?>
                        <nav aria-label="Page navigation" class="d-flex justify-content-center justify-content-md-end">
                            <ul class="pagination pagination-pro mb-0">
                                <!-- Previous Page -->
                                <li class="page-item <?= ($current_page <= 1) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= base_url('manufaktur/catalog?q='.$q.'&kategori_id='.$kategori_id.'&page='.($current_page-1)) ?>">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>
                                </li>

                                <!-- Page Numbers -->
                                <?php 
                                $start = max(1, $current_page - 2);
                                $end = min($total_pages, $current_page + 2);
                                
                                // Always show page 1
                                if($start > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= base_url('manufaktur/catalog?q='.$q.'&kategori_id='.$kategori_id.'&page=1') ?>">1</a>
                                    </li>
                                    <?php if($start > 2): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif;
                                endif;

                                for($i=$start; $i<=$end; $i++): ?>
                                    <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= base_url('manufaktur/catalog?q='.$q.'&kategori_id='.$kategori_id.'&page='.$i) ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor;

                                // Always show last page
                                if($end < $total_pages): ?>
                                    <?php if($end < $total_pages - 1): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= base_url('manufaktur/catalog?q='.$q.'&kategori_id='.$kategori_id.'&page='.$total_pages) ?>"><?= $total_pages ?></a>
                                    </li>
                                <?php endif; ?>

                                <!-- Next Page -->
                                <li class="page-item <?= ($current_page >= $total_pages) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= base_url('manufaktur/catalog?q='.$q.'&kategori_id='.$kategori_id.'&page='.($current_page+1)) ?>">
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Modals Grouped Outside Cards to prevent overflow clipping -->
            <?php foreach($products as $p): ?>
                <div class="modal fade" id="modalPrices<?= $p['product_id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                            <div class="modal-header bg-dark text-white border-0 p-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-indigo p-2 rounded-3 me-3">
                                        <i class="bi bi-currency-dollar fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="modal-title fw-bold mb-0"><?= $p['product_nama'] ?></h5>
                                        <p class="small text-white-50 mb-0">Daftar Harga Lengkap per <?= $p['satuan_nama'] ?></p>
                                    </div>
                                </div>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0" style="vertical-align: middle;">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="ps-4 py-3 border-0 text-muted small fw-bold text-uppercase">Keterangan Variasi</th>
                                                <th class="py-3 border-0 text-muted small fw-bold text-uppercase text-center">Range Qty</th>
                                                <th class="pe-4 py-3 border-0 text-muted small fw-bold text-uppercase text-end">Harga Unit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(!empty($p['prices'])): ?>
                                                <?php foreach($p['prices'] as $pr): ?>
                                                    <tr>
                                                        <td class="ps-4 py-3 border-bottom border-faint">
                                                            <div class="fw-bold text-slate-900"><?= $pr['detail_product'] ?: $p['product_nama'] ?></div>
                                                            <?php if(!empty($pr['ket_1_label']) || !empty($pr['jenisprint_label'])): ?>
                                                                <div class="x-small mt-1">
                                                                    <?php if(!empty($pr['ket_1_label'])): ?>
                                                                        <span class="badge bg-soft-indigo text-indigo py-1 px-2 rounded-pill me-1" style="font-size: 0.6rem;">
                                                                            <i class="bi bi-layers me-1"></i><?= $pr['ket_1_label'] ?>
                                                                        </span>
                                                                    <?php endif; ?>
                                                                    <?php if(!empty($pr['jenisprint_label'])): ?>
                                                                        <span class="badge bg-soft-emerald text-emerald py-1 px-2 rounded-pill" style="font-size: 0.6rem;">
                                                                            <i class="bi bi-cpu me-1"></i><?= $pr['jenisprint_label'] ?>
                                                                        </span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="py-3 text-center border-bottom border-faint fw-medium text-slate-600">
                                                            <span class="bg-light px-2 py-1 rounded small">
                                                                <?= number_format($pr['qty_min'], 0, ',', '.') ?> - <?= number_format($pr['qty_max'], 0, ',', '.') ?>
                                                            </span>
                                                        </td>
                                                        <td class="pe-4 py-3 text-end border-bottom border-faint">
                                                            <div class="fw-bold text-indigo fs-5">Rp <?= number_format($pr['harga_1'], 0, ',', '.') ?></div>
                                                            <div class="x-small text-muted">/ <?= $p['satuan_nama'] ?></div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="3" class="text-center py-5 text-muted">Belum ada strategi harga yang diatur.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer bg-light border-0 p-3 d-flex justify-content-between">
                                <button type="button" class="btn btn-danger rounded-pill px-4 fw-bold" onclick="downloadProductBrosur(<?= $p['product_id'] ?>, '<?= addslashes($p['product_nama']) ?>')">
                                    <i class="bi bi-file-earmark-pdf me-2"></i> Download Brosur PDF
                                </button>
                                <button type="button" class="btn btn-dark rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</div>

<!-- html2pdf.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function downloadProductBrosur(productId, productName) {
    const element = document.querySelector('#modalPrices' + productId + ' .modal-content');
    
    // Temporarily hide buttons for the PDF capture
    const footer = element.querySelector('.modal-footer');
    const closeBtn = element.querySelector('.btn-close');
    const originalFooterDisplay = footer.style.display;
    const originalCloseDisplay = closeBtn ? closeBtn.style.display : '';
    
    footer.style.display = 'none';
    if(closeBtn) closeBtn.style.display = 'none';

    // To ensure the dark header renders properly, we might need a small delay, 
    // but html2pdf handles it mostly fine.
    const opt = {
        margin:       [5, 5, 5, 5],
        filename:     'Brosur_' + productName.replace(/[^a-z0-9]/gi, '_').toLowerCase() + '.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(element).save().then(function() {
        // Restore buttons
        footer.style.display = originalFooterDisplay;
        if(closeBtn) closeBtn.style.display = originalCloseDisplay;
    });
}

function copyLinkPublik(link, btn) {
    navigator.clipboard.writeText(link).then(function() {
        // Show temporary feedback
        const icon = btn.querySelector('i');
        const originalClass = icon.className;
        icon.className = 'bi bi-check2-circle';
        btn.classList.replace('btn-outline-success', 'btn-success');
        btn.classList.add('text-white');
        
        setTimeout(() => {
            icon.className = originalClass;
            btn.classList.replace('btn-success', 'btn-outline-success');
            btn.classList.remove('text-white');
        }, 1500);
    }).catch(function(err) {
        alert('Gagal menyalin link: ' + err);
    });
}
</script>
