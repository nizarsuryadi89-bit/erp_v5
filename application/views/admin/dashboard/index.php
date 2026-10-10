<?php
if ($this->session->userdata('level') == 10) {
    redirect('mobile');
    exit;
}
?>
<style>
    /* Dashboard-Specific Styles Restored */
    .dashboard-icon {
        background: #fff;
        color: #495057;
        border-radius: 15px;
        padding: 1.5rem 1rem;
        text-align: center;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(0, 0, 0, 0.05);
        height: 100%;
        text-decoration: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    }

    .dashboard-icon:hover {
        background: linear-gradient(135deg, #2c3e50, #34495e);
        color: #fff;
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
    }

    .dashboard-icon i {
        font-size: 2.2rem;
        margin-bottom: 0.75rem;
        transition: inherit;
    }

    .dashboard-icon span {
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .powered-by img {
        height: 30px;
        margin: 0 10px;
        opacity: 0.7;
        filter: grayscale(100%);
        transition: all 0.3s;
    }

    .powered-by img:hover {
        opacity: 1;
        filter: grayscale(0%);
    }
</style>

<?php $this->load->view('layout_admin/header_modul'); ?>

<div class="app-content mt-n4">
    <div class="container-fluid">

        <!-- Quick Access Menu (Removed) -->

        <!-- Menu Navigation Grid -->
        <h6 class="mb-4 text-muted fw-bold px-2"><i class="fa fa-th-large me-2"></i>Menu Utama Aplikasi</h6>
        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-4 mb-5">
            <?php foreach ($menu as $mn): ?>
                <div class="col">
                    <a href="<?= base_url($mn->link) ?>" class="dashboard-icon">
                        <i class="<?= $mn->icon_fa ?>"></i>
                        <span><?= $mn->alias ?></span>
                    </a>
                </div>
            <?php endforeach; ?>

            <!-- Additional Static Menus -->
            <div class="col">
                <a href="javascript:void(0)" onclick="confirmAccessMobile()" class="dashboard-icon">
                    <i class="fa fa-mobile-alt"></i>
                    <span>Versi Mobile</span>
                </a>
            </div>
            <div class="col">
                <a href="https://tgmprint.com" target="_blank" class="dashboard-icon">
                    <i class="fa fa-globe"></i>
                    <span>Website</span>
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-5 mb-4 powered-by">
            <p class="text-muted small mb-2">Powered By:</p>
            <div class="d-flex justify-content-center align-items-center gap-3">
                <img src="<?= base_url('assets/images/official.png') ?>" alt="Official" style="height: 40px;">
                <img src="<?= base_url('assets/images/merch.png') ?>" alt="Merch" style="height: 25px;">
                <img src="<?= base_url('assets/images/salaketik.png') ?>" alt="Salaketik" style="height: 35px;">
            </div>
            <div class="mt-3 text-muted small">
                &copy; <?= date('Y') ?> TGM Print ERP System. All rights reserved.
            </div>
        </div>

    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Mobile Access Confirmation
    function confirmAccessMobile() {
        Swal.fire({
            title: 'Akses Versi Mobile?',
            text: "Anda akan dialihkan ke tampilan dashboard mobile yang lebih ringan.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4361ee',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= base_url('mobile') ?>';
            }
        });
    }
</script>