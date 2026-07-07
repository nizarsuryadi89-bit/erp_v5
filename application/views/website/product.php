<div role="main" class="main shop py-4">
    <div class="container">
        <div class="masonry-loader masonry-loader-showing">
            <div class="row products product-thumb-info-list" data-plugin-masonry data-plugin-options="{'layoutMode': 'fitRows'}">
                <?php foreach ($product as $pr): ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4 d-flex">
                        <div class="card border-0 shadow-sm w-100 h-100">
                            <a href="<?= base_url('website/detail_product/'.$pr->product_id) ?>" class="position-relative">
                                <!-- Gambar Produk -->
                                <img src="<?= base_url('assets/images/product/'.$pr->product_img_1) ?>" alt="<?= $pr->product_nama ?>" class="card-img-top img-fluid rounded-top" style="object-fit: cover; height: 200px;">
                            </a>

                            <div class="card-body bg-light p-3 d-flex flex-column justify-content-between">
                                <!-- Nama Produk -->
                                <h5 class="card-title text-primary text-center mb-2" style="font-size: 1rem;">
                                    <?= $pr->product_nama ?>
                                </h5>

                                <!-- Tombol Detail -->
                                <a href="<?= base_url('website/detail_product/'.$pr->product_id) ?>" class="btn btn-primary btn-sm text-uppercase mt-auto w-100">
                                    Detail Product
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
