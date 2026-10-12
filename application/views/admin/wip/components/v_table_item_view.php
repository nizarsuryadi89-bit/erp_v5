<?php 
function renderSearchBox(string $targetSelector) { 
    $unique_id = 'inlineSearch_' . substr(md5($targetSelector . rand()), 0, 6);
    echo '
    <div style="width: 280px;">
        <select id="' . $unique_id . '" class="form-control" style="width: 100%;"></select>
    </div>
    <script>
    $(document).ready(function() {
        if($.fn.select2) {
            $("#' . $unique_id . '").select2({
                theme: "bootstrap-5",
                placeholder: "Cari SPK / Produk / Konsumen...",
                minimumInputLength: 2,
                language: {
                    inputTooShort: function() { return "Ketik 2 huruf/angka..."; },
                    searching: function() { return "Mencari..."; },
                    noResults: function() { return "Tidak ditemukan"; }
                },
                ajax: {
                    url: "' . site_url('wip/ajax_global_search') . '",
                    dataType: "json",
                    delay: 250,
                    data: function (params) { return { q: params.term }; },
                    processResults: function (data) { return { results: data.results }; },
                    cache: true
                },
                templateResult: function(repo) {
                    if (repo.loading) return repo.text;
                    if (repo.is_show_all) {
                        return $(`<div class="fw-bold text-primary py-2"><i class="fas fa-search me-2"></i>${repo.text}</div>`);
                    }
                    if (repo.is_product) {
                        return $(`<div class="fw-bold text-dark py-1" style="font-size:0.75rem;"><i class="fas fa-box me-1 text-primary"></i>${repo.text} <span class="badge bg-primary bg-opacity-10 text-primary border ms-1" style="font-size:0.55rem;">Produk</span></div>`);
                    }
                    if (repo.is_consumer) {
                        return $(`<div class="fw-bold text-dark py-1" style="font-size:0.75rem;"><i class="fas fa-user-circle me-1 text-secondary"></i>${repo.text} <span class="badge bg-light text-dark border ms-1" style="font-size:0.55rem;">Konsumen</span></div>`);
                    }
                    var prodHtml = repo.product_nama ? `<div class="text-muted text-truncate" style="font-size:0.68rem;" title="${repo.product_nama}"><i class="fas fa-box-open me-1 text-primary"></i>${repo.product_nama}</div>` : "";
                    return $(`
                        <div class="d-flex justify-content-between align-items-center py-1">
                            <div class="pe-2" style="max-width: 68%;">
                                <div class="fw-bold text-dark text-truncate" style="font-size:0.75rem;" title="${repo.konsumen}">
                                    <span class="text-primary">[${repo.nospk}]</span> ${repo.konsumen}
                                </div>
                                ${prodHtml}
                            </div>
                            <div>
                                <span class="badge bg-${repo.status_color}" style="font-size:0.65rem;">${repo.status}</span>
                            </div>
                        </div>
                    `);
                },
                templateSelection: function(repo) { return repo.nospk ? repo.nospk : repo.text; }
            });

            $("#' . $unique_id . '").on("select2:select", function(e) {
                var data = e.params.data;
                if(data && data.nospk) {
                    var u = new URL(window.location.href);
                    u.searchParams.set("page", "1");
                    u.searchParams.set("status_view", "all_active");
                    u.searchParams.set("search_spk", data.nospk);
                    window.location.href = u.toString();
                }
            });
        }
    });
    </script>
    '; 
}

$group_mode = $group_mode ?? $_GET['group_mode'] ?? 'bahan';
?>

<div class="w-100">
    <div class="d-flex align-items-center justify-content-between mb-3 bg-white p-3 rounded shadow-sm border-start border-4 border-primary">
        <div class="d-flex flex-column gap-1">
            <div class="d-flex align-items-center gap-2">
                <h5 class="fw-900 text-primary mb-0" style="letter-spacing: 1px;"><i class="fas fa-list me-2"></i>ANTRIAN PEKERJAAN</h5>
            </div>
            <div><span class="badge bg-primary bg-opacity-10 text-primary">Daftar Item</span></div>
        </div>
        
        <div class="d-flex align-items-center gap-2">
            <div class="btn-group btn-group-sm" role="group" aria-label="Group Mode">
                <button type="button" class="btn <?= $group_mode === 'bahan' ? 'btn-primary' : 'btn-outline-primary' ?> fw-bold" style="font-size: 0.65rem;" onclick="switchGroupMode('bahan')">
                    <i class="fas fa-layer-group me-1"></i>Group Bahan
                </button>
                <button type="button" class="btn <?= $group_mode === 'spk' ? 'btn-primary' : 'btn-outline-primary' ?> fw-bold" style="font-size: 0.65rem;" onclick="switchGroupMode('spk')">
                    <i class="fas fa-file-invoice me-1"></i>Group SPK
                </button>
            </div>
            <?php renderSearchBox('#tableItemsAll'); ?>
        </div>
    </div>
    
    <div class="bg-white rounded shadow-sm overflow-hidden border">
        <?php 
        $current_view_mode = (!empty($is_finishing_workcenter) || ($filter_status_view ?? '') === 'finishing') ? 'finishing' : 'print';
        $this->load->view('admin/wip/components/v_table_item_view_core', [
            'raw_items' => $raw_items,
            'table_id' => 'tableItemsAll',
            'view_mode' => $current_view_mode,
            'group_mode' => $group_mode,
            'current_operator_id' => $current_operator_id ?? 0,
            'active_operator' => $active_operator ?? 'all',
            'is_super_admin' => $is_super_admin ?? false,
            'mesin_printing_map' => $mesin_printing_map ?? [],
            'category_schemas' => $category_schemas ?? [],
            'is_urgent_item' => $is_urgent_item ?? false,
            'filter_status_view' => $filter_status_view ?? 'pending',
            'is_finishing_workcenter' => $is_finishing_workcenter ?? false
        ]); 
        ?>
    </div>
</div>
