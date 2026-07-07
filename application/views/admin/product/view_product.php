<div class="app-content d-block">
    <div class="container-fluid py-3">
        <div class="card shadow-sm">
            <div class="card-header">
                <div class="row mg-files" data-sort-destination data-sort-id="media-gallery">
                    <?php foreach ($product as $pr): ?>
                        <div class="isotope-item document col-sm-6 col-md-4 col-lg-3">
                            <div class="thumbnail">
                                <div class="thumb-preview">
                                    <a class="thumb-image" href="<?= base_url('assets/images/product/' . $pr->product_img_1) ?>">
                                        <img src="<?= base_url('assets/images/product/' . $pr->product_img_1) ?>" 
                                             class="img-responsive img-fluid" 
                                             alt="<?= htmlspecialchars($pr->product_nama) ?>">
                                    </a>
                                </div>
                                <h3 class="mg-title text-bold text-center">
                                    <?= htmlspecialchars($pr->product_nama) ?>
                                </h3>
                                <hr>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
