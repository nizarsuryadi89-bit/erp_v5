<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="fw-bold text-dark">Upload Gambar Produk</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('manufaktur/catalog') ?>" class="text-decoration-none">Katalog Produk</a></li>
                        <li class="breadcrumb-item active">Upload Gambar</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header bg-white border-bottom py-3">
                            <h4 class="card-title mb-0 fw-bold text-primary">
                                <i class="bi bi-image me-2"></i> <?= htmlspecialchars($product['product_nama']) ?>
                            </h4>
                        </div>
                        <form enctype="multipart/form-data" method="post" action="<?= base_url($controller.'/actionuploadaproduct')?>">
                            <div class="card-body p-4 p-md-5">
                                
                                <div class="row align-items-center mb-4 pb-3 border-bottom border-light">
                                    <label class="col-sm-4 col-form-label text-sm-end text-muted fw-medium">Gambar Saat Ini</label>
                                    <div class="col-sm-8">
                                        <?php 
                                        $img = (!empty($product['product_img']) && $product['product_img'] != 'official.png') 
                                                ? base_url('assets/images/product/'.$product['product_img']) 
                                                : ''; 
                                        ?>
                                        <div class="position-relative border rounded-3 p-2 bg-light d-inline-block shadow-sm" style="max-width: 250px;">
                                            <img src="<?= $img ?>" class="img-fluid rounded" alt="Gambar Produk" style="max-height: 200px; object-fit: contain; width: 100%;" onerror="this.src='https://placehold.co/300x300?text=Belum+Ada+Gambar'">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row align-items-start mt-3">
                                    <label class="col-sm-4 col-form-label text-sm-end fw-bold text-dark pt-2">Pilih Gambar Baru</label>
                                    <div class="col-sm-8">
                                        <input type="hidden" name="product_id" value="<?= $product['product_id']?>">
                                        <div class="input-group">
                                            <span class="input-group-text bg-white"><i class="bi bi-file-earmark-image text-primary"></i></span>
                                            <input type="file" name="product_img_1" class="form-control" accept="image/png, image/jpeg, image/webp" required>
                                        </div>
                                        <div class="form-text mt-2 text-muted">
                                            <i class="bi bi-info-circle me-1"></i> Format didukung: <strong>JPG, PNG, WEBP</strong>. Maksimal ukuran: <strong>2MB</strong>.<br>
                                            Disarankan menggunakan gambar resolusi berbentuk kotak (persegi).
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer bg-light px-4 py-3 d-flex justify-content-between align-items-center">
                                <a href="<?= base_url('manufaktur/catalog') ?>" class="btn btn-outline-secondary px-4 fw-medium rounded-pill">
                                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Katalog
                                </a>
                                <button type="submit" class="btn btn-primary px-4 fw-bold rounded-pill shadow-sm">
                                    <i class="bi bi-cloud-arrow-up me-1"></i> Upload Gambar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<style>
    .breadcrumb-item + .breadcrumb-item::before {
        content: ">";
        font-family: 'bootstrap-icons';
        font-size: 0.8rem;
        vertical-align: middle;
    }
</style>
