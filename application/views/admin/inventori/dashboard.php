

<!-- start: page -->
<section class="panel text-center">
<!-- <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                        <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                    </div>
            
                    <h2 class="panel-title"><?= $title ?></h2>
                </header> -->
    <div class="col-md-12 col-lg-12 col-xl-12">
            <h3 class="text-weight-semibold text-left text-dark text-uppercase mb-md mt-lg"> Status Inventory </h3>
            <hr>
            <div class="row">
                <div class="col-md-3 col-xl-3">
                    <section class="panel">
                        <div class="panel-body bg-dark">
                            <div class="widget-summary">
                                <div class="widget-summary-col widget-summary-col-icon">
                                    <div class="summary-icon">
                                        <i class="fa fa-database"></i>
                                    </div>
                                </div>
                                <div class="widget-summary-col">
                                    <div class="summary">
                                        <h4 class="title">Kategori</h4>
                                        <div class="info">
                                            <strong class="amount"><?= $jml_kategori ?></strong>
                                        </div>
                                    </div>
                                    <div class="summary-footer">
                                    <a class="text-uppercase" href="<?= base_url('warehouse/kategori') ?>">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="col-md-3 col-xl-3">
                    <section class="panel">
                        <div class="panel-body bg-dark">
                            <div class="widget-summary">
                                <div class="widget-summary-col widget-summary-col-icon">
                                    <div class="summary-icon">
                                        <i class="fa fa-database"></i>
                                    </div>
                                </div>
                                <div class="widget-summary-col">
                                    <div class="summary">
                                        <h4 class="title">Sub Kategori</h4>
                                        <div class="info">
                                            <strong class="amount"><?= $jml_subkategori ?></strong>
                                        </div>
                                    </div>
                                    <div class="summary-footer">
                                    <a class="text-uppercase" href="<?= base_url('warehouse/subkategori') ?>">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="col-md-3 col-xl-3">
                    <section class="panel">
                        <div class="panel-body bg-dark">
                            <div class="widget-summary">
                                <div class="widget-summary-col widget-summary-col-icon">
                                    <div class="summary-icon">
                                        <i class="fa fa-database"></i>
                                    </div>
                                </div>
                                <div class="widget-summary-col">
                                    <div class="summary">
                                        <h4 class="title">Bahan Baku</h4>
                                        <div class="info">
                                            <strong class="amount"><?= $total_bahan ?></strong>
                                        </div>
                                    </div>
                                    <div class="summary-footer">
                                    <a class="text-uppercase" href="<?= base_url('warehouse/bahan') ?>">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="col-md-3 col-xl-3">
                    <section class="panel">
                        <div class="panel-body bg-dark">
                            <div class="widget-summary">
                                <div class="widget-summary-col widget-summary-col-icon">
                                    <div class="summary-icon">
                                        <i class="fa fa-database"></i>
                                    </div>
                                </div>
                                <div class="widget-summary-col">
                                    <div class="summary">
                                        <h4 class="title">Product</h4>
                                        <div class="info">
                                            <strong class="amount"><?= $total_product ?></strong>
                                        </div>
                                    </div>
                                    <div class="summary-footer">
                                    <a class="text-uppercase" href="<?= base_url('warehouse/product') ?>">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                
            </div>
            <div class="row">
            <div class="col-md-3 col-xl-3">
                    <section class="panel">
                        <div class="panel-body bg-dark">
                            <div class="widget-summary">
                                <div class="widget-summary-col widget-summary-col-icon">
                                    <div class="summary-icon">
                                        <i class="fa fa-align-justify"></i>
                                    </div>
                                </div>
                                <div class="widget-summary-col">
                                    <div class="summary">
                                        <h4 class="title">Supplier</h4>
                                        <div class="info">
                                            <strong class="amount"><?= $jml_supplier ?></strong>
                                        </div>
                                    </div>
                                    <div class="summary-footer">
                                    <a class="text-uppercase" href="<?= base_url('inventori/supplier') ?>">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="col-md-3 col-xl-3">
                    <section class="panel">
                        <div class="panel-body bg-dark">
                            <div class="widget-summary">
                                <div class="widget-summary-col widget-summary-col-icon">
                                    <div class="summary-icon">
                                        <i class="fa fa-users"></i>
                                    </div>
                                </div>
                                <div class="widget-summary-col">
                                    <div class="summary">
                                        <h4 class="title">Karyawan</h4>
                                        <div class="info">
                                            <strong class="amount"><?= $jml_karyawan ?></strong>
                                        </div>
                                    </div>
                                    <div class="summary-footer">
                                    <a class="text-uppercase" href="<?= base_url('humanresource/karyawan') ?>">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="col-md-3 col-xl-3">
                    <section class="panel">
                        <div class="panel-body bg-dark">
                            <div class="widget-summary">
                                <div class="widget-summary-col widget-summary-col-icon">
                                    <div class="summary-icon">
                                        <i class="fa fa-users"></i>
                                    </div>
                                </div>
                                <div class="widget-summary-col">
                                    <div class="summary">
                                        <h4 class="title">Konsumen</h4>
                                        <div class="info">
                                            <strong class="amount"><?= $total_konsumen ?></strong>
                                        </div>
                                    </div>
                                    <div class="summary-footer">
                                    <a class="text-uppercase" href="<?= base_url('konsumen') ?>">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="col-md-3 col-xl-3">
                    <section class="panel">
                        <div class="panel-body bg-dark">
                            <div class="widget-summary">
                                <div class="widget-summary-col widget-summary-col-icon">
                                    <div class="summary-icon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                </div>
                                <div class="widget-summary-col">
                                    <div class="summary">
                                        <h4 class="title">Users</h4>
                                        <div class="info">
                                            <strong class="amount"><?= $jml_users ?></strong>
                                        </div>
                                    </div>
                                    <div class="summary-footer">
                                    <a class="text-uppercase" href="<?= base_url('konfigurasi/user_account') ?>">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    
</section>