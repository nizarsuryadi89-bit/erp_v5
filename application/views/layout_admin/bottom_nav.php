<!-- Bottom Navigation Bar for Mobile -->
<div class="bottom-nav d-lg-none">
    <div class="container-fluid h-100">
        <div class="row h-100">
            <div class="col h-100">
                <a href="<?= base_url('dashboard') ?>" class="nav-item <?= ($this->uri->segment(1) == 'dashboard') ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span>Home</span>
                </a>
            </div>
            <div class="col h-100">
                <a href="<?= base_url('transaksi') ?>" class="nav-item <?= ($this->uri->segment(1) == 'transaksi') ? 'active' : '' ?>">
                    <i class="bi bi-cart-plus"></i>
                    <span>SPK</span>
                </a>
            </div>
            <div class="col h-100">
                <a href="<?= base_url('wip/produksi') ?>" class="nav-item <?= ($this->uri->segment(1) == 'wip' || $this->uri->segment(1) == 'pb') ? 'active' : '' ?>">
                    <i class="bi bi-box-seam"></i>
                    <span>WIP</span>
                </a>
            </div>
            <div class="col h-100">
                <a href="<?= base_url('SearchAI') ?>" class="nav-item <?= ($this->uri->segment(1) == 'SearchAI') ? 'active' : '' ?>">
                    <i class="bi bi-stars text-primary"></i>
                    <span>AI</span>
                </a>
            </div>
            <div class="col h-100">
                <a href="javascript:void(0)" class="nav-item" id="mobileSidebarToggle">
                    <i class="bi bi-list"></i>
                    <span>Menu</span>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 65px;
    background: #fff;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    z-index: 1040;
    padding-bottom: env(safe-area-inset-bottom);
    display: flex;
    align-items: center;
}

.bottom-nav .nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #6c757d;
    text-decoration: none;
    font-size: 0.7rem;
    padding-top: 5px;
    transition: all 0.2s;
}

.bottom-nav .nav-item i {
    font-size: 1.4rem;
    margin-bottom: 2px;
}

.bottom-nav .nav-item span {
    font-weight: 500;
}

.bottom-nav .nav-item.active {
    color: #0d6efd;
}

.bottom-nav .nav-item.active i {
    color: #0d6efd;
}

/* Dark mode compatibility if needed */
[data-bs-theme="dark"] .bottom-nav {
    background: #212529;
    border-top: 1px solid #343a40;
}

/* Adjust padding for the content wrapper to avoid being covered by bottom nav */
@media (max-width: 991.98px) {
    .content-wrapper {
        margin-bottom: 70px !important;
    }
}
</style>

<script>
$(document).ready(function() {
    $('#mobileSidebarToggle').on('click', function() {
        $('body').toggleClass('sidebar-open');
    });
});
</script>
