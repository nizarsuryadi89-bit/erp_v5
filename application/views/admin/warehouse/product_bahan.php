<div class="app-content d-block">
    <div class="container-fluid py-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row g-3">
                    <?php foreach ($tampil as $pr): ?>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="card h-100">
                                <a href="<?= base_url('assets/images/product/' . $pr->product_img_1) ?>" target="_blank">
                                    <img src="<?= base_url('assets/images/product/' . $pr->product_img_1) ?>"
                                         class="card-img-top img-fluid"
                                         alt="<?= htmlspecialchars($pr->product_nama) ?>">
                                </a>
                                <div class="card-body p-2 text-center">
                                    <h6 class="card-title mb-0 fw-bold"><?= htmlspecialchars($pr->product_nama) ?></h6>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
