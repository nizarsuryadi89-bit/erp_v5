<div role="main" class="main shop py-4">
    <div class="container">
        <!-- DETAIL PRODUK -->
        <div class="row">
            <!-- Gambar Carousel -->
            <div class="col-12 col-lg-6 mb-4">
                <div class="owl-carousel owl-theme" data-plugin-options="{'items': 1}">
                    <?php for ($i = 1; $i <= 3; $i++): ?>
                        <?php if (!empty($getproduct['product_img_'.$i])): ?>
                            <div>
                                <img alt="Product Image <?= $i ?>" class="img-fluid rounded w-100" src="<?= base_url('assets/images/product/'.$getproduct['product_img_'.$i]) ?>">
                            </div>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Info Produk -->
            <div class="col-12 col-lg-6">
                <div class="summary entry-summary">
                    <h1 class="mb-2 font-weight-bold text-6 text-primary"><?= $getproduct['product_nama'] ?></h1>

                    <!-- Rating -->
                    <div class="mb-3">
                        <input type="text" class="d-none" value="3" data-plugin-star-rating data-plugin-options="{'displayOnly': true, 'color': 'primary', 'size':'xs'}">
                    </div>

                    <!-- Deskripsi -->
                    <p class="mb-4" style="text-align: justify;"><?= $getproduct['product_deskripsi'] ?></p>

                    <!-- Form Add to Cart -->
                    <form method="post" class="cart d-flex flex-column flex-sm-row align-items-start align-items-sm-center">
                        <div class="quantity quantity-lg me-sm-3 mb-3 mb-sm-0">
                            <input type="button" class="minus" value="-">
                            <input type="text" class="input-text qty text" value="1" name="quantity" min="1" step="1">
                            <input type="button" class="plus" value="+">
                        </div>
                        <button class="btn btn-primary btn-modern text-uppercase">Add to cart</button>
                    </form>

                    <!-- Kategori -->
                    <div class="product-meta mt-3">
                        <span class="posted-in">Categories: <a rel="tag" href="#">Accessories</a>, <a rel="tag" href="#">Bags</a>.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB INFORMASI PRODUK -->
        <div class="row">
            <div class="col">
                <div class="tabs tabs-product my-4">
                    <ul class="nav nav-tabs flex-wrap">
                        <li class="nav-item">
                            <a class="nav-link py-2 px-3 active" href="#productDescription" data-toggle="tab">Description</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-2 px-3" href="#productInfo" data-toggle="tab">Additional Information</a>
                        </li>
                    </ul>
                    <div class="tab-content p-3 border border-top-0">
                        <div class="tab-pane active" id="productDescription">
                            <p class="mb-0" style="text-align: justify;"><?= $getproduct['product_deskripsi'] ?></p>
                        </div>
                        <div class="tab-pane" id="productInfo">
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <th>Size</th>
                                        <td>Unique</td>
                                    </tr>
                                    <tr>
                                        <th>Colors</th>
                                        <td>Red, Blue</td>
                                    </tr>
                                    <tr>
                                        <th>Material</th>
                                        <td>100% Leather</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PRODUK TERKAIT -->
        <div class="row">
            <div class="col">
                <hr class="solid my-4">
                <h4 class="mb-3">Related <strong>Products</strong></h4>
                <div class="row products product-thumb-info-list mt-3">
                    <!-- Contoh produk terkait, sesuaikan dengan data dinamis -->
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <div class="col-6 col-md-4 col-lg-3 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <a href="#" class="position-relative">
                                    <img src="img/products/product-grey-<?= $i ?>.jpg" class="card-img-top img-fluid" alt="Product <?= $i ?>" style="object-fit: cover; height: 200px;">
                                </a>
                                <div class="card-body bg-light text-center p-3">
                                    <h5 class="card-title text-primary mb-2">Product <?= $i ?></h5>
                                    <span class="price text-dark font-weight-semibold">$99</span>
                                    <a href="#" class="btn btn-sm btn-primary mt-2 w-100 text-uppercase">Add to Cart</a>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</div>
