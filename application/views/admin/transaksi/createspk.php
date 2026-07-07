<!-- DYNAMIC SPK CREATION INTERFACE (CLEAN MODULAR VERSION)
    Refactored for maintainability
-->
<link rel="stylesheet" href="<?= base_url('assets/css/createspk.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/tgm-createspk.css') ?>">

<div class="app-content d-block">
    <div class="container-fluid py-3">

        <!-- PART 1: HEADER & INFO PELANGGAN -->
        <?php $this->load->view('admin/transaksi/createspk/parts/header_info'); ?>

        <!-- PART 2: FORM PILIH PRODUK & SPESIFIKASI -->
        <?php $this->load->view('admin/transaksi/createspk/parts/product_form'); ?>

        <!-- PART 3: TABEL TRANSAKSI & FOOTER -->
        <?php $this->load->view('admin/transaksi/createspk/parts/transaction_table'); ?>

        <!-- PART 4: MODALS (TAMBAH KONSUMEN & KIOSK) -->
        <?php $this->load->view('admin/transaksi/createspk/parts/modals'); ?>

    </div>
</div>

<!-- PART 5: JAVASCRIPT LOGIC -->
<?php $this->load->view('admin/transaksi/createspk/parts/scripts'); ?>