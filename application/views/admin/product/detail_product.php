<div class="row">
    <section class="panel form-wizard" id="w2">
        <header class="panel-heading dark">
            <div class="panel-actions">
                <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
            </div>
            <h2 class="panel-title">
                <a href="<?= base_url($controller . '/product') ?>" class="btn btn-warning btn-sm">
                    <i class="fa fa-backward"></i> Kembali
                </a>
                <span class="ml-sm text-white">Detail Product: <strong><?= $getProduct['product_nama'] ?>
                    </strong></span>
            </h2>
        </header>

        <div class="panel-body">
            <div class="tabs">
                <ul class="nav nav-tabs nav-justify">
                    <li class="active">
                        <a href="#tab-product" data-toggle="tab" class="text-center">
                            <span class="badge hidden-xs">1</span>
                            Informasi Product
                        </a>
                    </li>
                    <li>
                        <a href="#tab-detail" data-toggle="tab" class="text-center">
                            <span class="badge hidden-xs">2</span>
                            Detail (Harga & BOM)
                        </a>
                    </li>
                    <li>
                        <a href="#tab-manufacture" data-toggle="tab" class="text-center">
                            <span class="badge hidden-xs">3</span>
                            Manufacturing
                        </a>
                    </li>
                    <li>
                        <a href="#tab-image" data-toggle="tab" class="text-center">
                            <span class="badge hidden-xs">4</span>
                            Gambar Product
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Information Tab -->
                    <div id="tab-product" class="tab-pane active">
                        <?php $this->load->view('admin/product/form_update'); ?>
                    </div>

                    <!-- Detail (Price & BOM) Tab -->
                    <div id="tab-detail" class="tab-pane">
                        <?php $this->load->view('admin/product/detail'); ?>
                    </div>

                    <!-- Manufacture Tab -->
                    <div id="tab-manufacture" class="tab-pane">
                        <?php $this->load->view('admin/product/manufactur'); ?>
                    </div>

                    <!-- Image Tab -->
                    <div id="tab-image" class="tab-pane">
                        <?php $this->load->view('admin/product/form_upload'); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>