<!-- Global SPK Search Modal -->
<div class="modal fade" id="globalSpkSearchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header text-white px-4 py-3" style="background: linear-gradient(135deg, #2c3e50, #3498db);">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-search me-2"></i> Lacak SPK / Konsumen Global
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="mb-4 text-center">
                    <p class="text-muted small mb-0">Cari berdasarkan Nomor SPK, Nama Produk, atau Nama Konsumen di seluruh data produksi</p>
                </div>
                
                <div class="form-group mb-0 position-relative">
                    <select id="globalSpkSelect" class="form-control" style="width: 100%;"></select>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openGlobalSearchModal() {
        $('#globalSpkSearchModal').modal('show');
    }

    $(document).ready(function() {
        if($.fn.select2) {
            $('#globalSpkSelect').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#globalSpkSearchModal'),
                placeholder: 'Ketik Nomor SPK, Nama Produk, atau Nama Konsumen...',
                minimumInputLength: 2,
                language: {
                    inputTooShort: function() { return 'Ketik minimal 2 huruf/angka...'; },
                    searching: function() { return 'Mencari...'; },
                    noResults: function() { return 'Tidak ditemukan'; }
                },
                ajax: {
                    url: '<?= site_url('wip/ajax_global_search') ?>',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term };
                    },
                    processResults: function (data) {
                        return { results: data.results };
                    },
                    cache: true
                },
                templateResult: function(repo) {
                    if (repo.loading) return repo.text;
                    if (repo.is_show_all) {
                        return $(`<div class="fw-bold text-primary py-2"><i class="fas fa-search me-2"></i>${repo.text}</div>`);
                    }
                    if (repo.is_product) {
                        return $(`<div class="fw-bold text-dark py-1"><i class="fas fa-box me-2 text-primary"></i>${repo.text} <span class="badge bg-primary bg-opacity-10 text-primary border ms-1" style="font-size:0.6rem;">Data Produk</span></div>`);
                    }
                    if (repo.is_consumer) {
                        return $(`<div class="fw-bold text-dark py-1"><i class="fas fa-user-circle me-2 text-secondary"></i>${repo.text} <span class="badge bg-light text-dark border ms-1" style="font-size:0.6rem;">Data Konsumen</span></div>`);
                    }
                    var prodHtml = repo.product_nama ? `<div class="small text-secondary fw-semibold"><i class="fas fa-box-open me-1"></i> ${repo.product_nama}</div>` : '';
                    var markup = `
                        <div class="d-flex justify-content-between align-items-center py-1">
                            <div>
                                <div class="fw-bold text-dark">
                                    <span class="text-primary">[${repo.nospk}]</span> ${repo.konsumen}
                                </div>
                                ${prodHtml}
                                <div class="small text-muted">
                                    <i class="fas fa-industry me-1"></i> ${repo.kategori}
                                </div>
                            </div>
                            <div>
                                <span class="badge bg-${repo.status_color}">${repo.status}</span>
                            </div>
                        </div>
                    `;
                    return $(markup);
                },
                templateSelection: function(repo) {
                    return repo.nospk ? repo.text : repo.text;
                }
            });

            $('#globalSpkSelect').on('select2:select', function(e) {
                var data = e.params.data;
                if(data && data.nospk) {
                    var u = new URL('<?= site_url('wip/produksi') ?>', window.location.origin);
                    u.searchParams.set('page', '1');
                    u.searchParams.set('status_view', 'all_active');
                    u.searchParams.set('search_spk', data.nospk);
                    var currentUrl = new URL(window.location.href);
                    if (currentUrl.searchParams.get('operator')) {
                        u.searchParams.set('operator', currentUrl.searchParams.get('operator'));
                    }
                    if (currentUrl.searchParams.get('group_mode')) {
                        u.searchParams.set('group_mode', currentUrl.searchParams.get('group_mode'));
                    }
                    window.location.href = u.toString();
                }
            });
            
            // Auto-focus after open
            $('#globalSpkSearchModal').on('shown.bs.modal', function () {
                $('#globalSpkSelect').select2('open');
            });
        }
    });
</script>
