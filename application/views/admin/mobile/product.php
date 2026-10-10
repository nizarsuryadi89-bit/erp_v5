<!-- Page Header -->
<div class="custom-page-header">
    <h5><i class="bi bi-box-seam-fill me-2 text-info"></i>Katalog Produk</h5>
    <p>Cek ketersediaan stok dan harga real-time</p>
</div>

<!-- Main Content -->
<div class="content-container">

    <!-- Search Box -->
    <div class="stat-card p-2 mb-4 shadow-sm border-0 d-flex align-items-center" style="border-radius: 15px;">
        <i class="bi bi-search ms-3 text-muted"></i>
        <input type="text" id="searchInput" class="form-control border-0 bg-transparent shadow-none py-2"
            placeholder="Cari nama produk..." style="font-size: 0.95rem;">
    </div>

    <!-- Product Container -->
    <div class="section-header">
        <div class="section-title"><i class="bi bi-tag-fill me-2 text-primary"></i>List Produk</div>
        <span id="productCount" class="badge bg-secondary rounded-pill px-3"
            style="font-size: 0.65rem;">Loading...</span>
    </div>

    <div id="productList" class="mb-4">
        <!-- AJAX content loaded here -->
        <?php $this->load->view('admin/mobile/product_list'); ?>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function loadProduct(page) {
        page = page || 1;
        var keyword = $('#searchInput').val();

        // Show subtle loading state
        $('#productList').css('opacity', '0.5');

        $.get("<?= base_url('mobile/ajax_list') ?>", { page: page, keyword: keyword }, function (data) {
            $('#productList').html(data).css('opacity', '1');

            // Update item count if possible (custom logic could be added to backend)
            var count = $('.product-entry').length;
            $('#productCount').text(count + ' Item');
        });
    }

    $(document).ready(function () {
        loadProduct();

        $('#searchInput').on('input', function () {
            // Add debounce if necessary, for now direct load
            loadProduct();
        });

        $(document).on('click', '.pagination a', function (e) {
            e.preventDefault();
            var page = $(this).attr("data-ci-pagination-page");
            if (page) {
                loadProduct(page);
                // Smooth scroll to top of list
                $('html, body').animate({
                    scrollTop: $(".section-header").offset().top - 100
                }, 400);
            }
        });
    });
</script>