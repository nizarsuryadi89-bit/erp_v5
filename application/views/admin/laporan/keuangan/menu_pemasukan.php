<?php $uri2 = $this->uri->segment(2); ?>
<div class="header-nav-main">
    <div class="nav-scroller mb-3">
        <ul class="nav nav-pills flex-nowrap overflow-auto text-nowrap" style="white-space: nowrap; gap: 0.5rem;">
            <li class="nav-item">
                <a href="<?= base_url('keuangan/laporan-keuangan-bulanan') ?>"
                    class="nav-link <?= ($uri2 == 'laporan-keuangan-bulanan') ? 'active' : '' ?>">
                    <i class="bi bi-list-ul me-1"></i> All
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('keuangan/laporan-cash-bulanan') ?>"
                    class="nav-link <?= ($uri2 == 'laporan-cash-bulanan') ? 'active' : '' ?>">
                    <i class="bi bi-cash me-1"></i> Cash
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('keuangan/laporan-transfer-bulanan') ?>"
                    class="nav-link <?= ($uri2 == 'laporan-transfer-bulanan') ? 'active' : '' ?>">
                    <i class="bi bi-arrow-left-right me-1"></i> Transfer
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('keuangan/laporan-edc-bulanan') ?>"
                    class="nav-link <?= ($uri2 == 'laporan-edc-bulanan') ? 'active' : '' ?>">
                    <i class="bi bi-credit-card-2-front me-1"></i> EDC
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('keuangan/laporan-ewallet-bulanan') ?>"
                    class="nav-link <?= ($uri2 == 'laporan-ewallet-bulanan') ? 'active' : '' ?>">
                    <i class="bi bi-phone me-1"></i> E-Wallet
                </a>
            </li>
            <!-- Laporan Pajak -->
            <li class="nav-item ms-lg-4">
                <a href="<?= base_url('keuangan/laporan-pajak-bulanan') ?>" class="nav-link text-dark">
                    <i class="bi bi-receipt me-1"></i> Pajak
                </a>
            </li>
        </ul>
    </div>
</div>