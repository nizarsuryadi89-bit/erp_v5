<div class="header-nav-main header-nav-main-effect-1 header-nav-main-sub-effect-1">
    <?php $uri2 = $this->uri->segment(2); ?>
    <nav class="mb-3">
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a href="<?= base_url('keuangan/laporan-keuangan-harian') ?>"
                    class="nav-link btn btn-dark <?= ($uri2 == 'laporan-keuangan-harian') ? 'active' : '' ?>">
                    <i class="fa fa-list"></i> All
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('keuangan/laporan-cash-harian') ?>"
                    class="nav-link btn btn-dark <?= ($uri2 == 'laporan-cash-harian') ? 'active' : '' ?>">
                    <i class="fa fa-money"></i> Cash
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('keuangan/laporan-transfer-harian') ?>"
                    class="nav-link btn btn-dark <?= ($uri2 == 'laporan-transfer-harian') ? 'active' : '' ?>">
                    <i class="fa fa-exchange"></i> Transfer
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('keuangan/laporan-edc-harian') ?>"
                    class="nav-link btn btn-dark <?= ($uri2 == 'laporan-edc-harian') ? 'active' : '' ?>">
                    <i class="fa fa-credit-card"></i> EDC
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('keuangan/laporan-ewallet-harian') ?>"
                    class="nav-link btn btn-dark <?= ($uri2 == 'laporan-ewallet-harian') ? 'active' : '' ?>">
                    <i class="fa fa-mobile"></i> E-Wallet
                </a>
            </li>



        </ul>
    </nav>

</div>