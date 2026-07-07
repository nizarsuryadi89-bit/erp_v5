<?php $this->load->view('layout_admin/header_modul'); ?>

<div class="app-content mt-n4 pb-5">
    <div class="container-fluid">
        <div class="row align-items-center mb-4 pt-3">
            <div class="col-md-7">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-primary rounded-circle shadow-sm">
                        <i class="fas fa-exchange-alt text-white fs-4"></i>
                    </div>
                    <div>
                        <h4 class="performance-title mb-0">ORDER TITIPAN PRODUKSI</h4>
                        <p class="text-muted fw-bold mb-0 small text-uppercase letter-spacing-1">
                            Pesanan dari cabang lain diproduksi di sini
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-5 text-md-end">
                <a href="<?= site_url('dashboard') ?>" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                    <i class="fas fa-arrow-left me-2"></i>KEMBALI KE DASHBOARD
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-uppercase small fw-800 text-muted">No. Nota / SPK</th>
                                <th class="py-3 text-uppercase small fw-800 text-muted">Cabang Asal</th>
                                <th class="py-3 text-uppercase small fw-800 text-muted">Konsumen</th>
                                <th class="py-3 text-uppercase small fw-800 text-muted text-center">Item</th>
                                <th class="py-3 text-uppercase small fw-800 text-muted">Total</th>
                                <th class="py-3 text-uppercase small fw-800 text-muted">Tgl Order</th>
                                <th class="py-3 text-uppercase small fw-800 text-muted">Status Bayar</th>
                                <th class="py-3 text-uppercase small fw-800 text-muted text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="py-4">
                                            <i class="fas fa-folder-open fs-1 text-muted opacity-25 mb-3"></i>
                                            <p class="text-muted fw-bold">Belum ada pesanan titipan dari cabang lain saat
                                                ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $this->load->view('admin/transaksi/order_titipan_rows', ['orders' => $orders]); ?>
                            <?php endif; ?>
                            <tr id="loading-row" style="display: none;">
                                <td colspan="8" class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <div class="small text-muted mt-2 fw-bold">Memuat data selanjutnya...</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let loading = false;
    let hasMore = <?= !empty($orders) && count($orders) == 20 ? 'true' : 'false' ?>;
    
    // Throttle scroll listener
    let scrollTimeout;
    window.addEventListener('scroll', function() {
        if (scrollTimeout) return;
        scrollTimeout = setTimeout(function() {
            scrollTimeout = null;
            checkScroll();
        }, 100);
    });
    
    function checkScroll() {
        if (loading || !hasMore) return;
        
        // Load more when user is near bottom of the page
        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 500) {
            loadMore();
        }
    }
    
    function loadMore() {
        loading = true;
        currentPage++;
        document.getElementById('loading-row').style.display = 'table-row';
        
        $.ajax({
            url: '<?= site_url('transaksi/order_titipan') ?>',
            type: 'GET',
            data: { page: currentPage },
            success: function(response) {
                document.getElementById('loading-row').style.display = 'none';
                
                if (response && response.trim() !== '') {
                    const htmlContent = $(response);
                    const trs = htmlContent.filter('tr').length > 0 ? htmlContent.filter('tr') : htmlContent.find('tr');
                    
                    $('#loading-row').before(htmlContent);
                    
                    if (trs.length < 20) {
                        hasMore = false;
                    }
                } else {
                    hasMore = false;
                }
                
                loading = false;
                setTimeout(checkScroll, 200); // Small delay before next check
            },
            error: function() {
                document.getElementById('loading-row').style.display = 'none';
                loading = false;
                hasMore = false; 
            }
        });
    }
});
</script>