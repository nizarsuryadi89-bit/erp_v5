<!-- External CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">

<?php $this->load->view('admin/product/components/v_product_styles'); ?>

<?php
$pagination_base_params = $current_filters ?? ['per_page' => $per_page];
$buildPageUrl = function ($pageNumber) use ($pagination_base_params) {
    $params = $pagination_base_params;
    $params['page'] = $pageNumber;
    $params = array_filter($params, function ($value) {
        return $value !== null && $value !== '';
    });
    return base_url('manufaktur?' . http_build_query($params));
};
?>

<div class="product-catalog-shell">

<!-- ====== ODOO-STYLE CONTROL PANEL ====== -->
<div class="bg-white border-bottom px-4 py-3 mb-4 shadow-sm" style="position: sticky; top: 57px; z-index: 900;">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <!-- Title -->
        <div class="d-flex align-items-center gap-3">
            <h4 class="mb-0 text-dark fw-bold" style="font-size: 1.4rem; letter-spacing: -0.5px;">Products</h4>
            <span class="badge bg-light text-secondary border border-secondary" style="font-size: 0.8rem; padding: 4px 8px;">
                <?= number_format($total_products) ?> Total
            </span>
        </div>
        
        <!-- Search Bar -->
        <div class="input-group" style="max-width: 450px;">
            <input type="text" class="form-control bg-light border-end-0 border-secondary" id="searchInput" placeholder="Search product name..." value="<?= $this->input->get('q') ?>" style="border-radius: 4px 0 0 4px; box-shadow: none;">
            
            <?php if(!empty($this->input->get('q')) || !empty($this->input->get('kategori'))): ?>
            <span class="input-group-text bg-light border-secondary border-start-0 border-end-0 px-2" style="cursor: pointer;"
                onclick="document.getElementById('searchInput').value=''; document.getElementById('filterKategori').value=''; filterTable();"
                title="Kosongkan Pencarian & Kategori">
                <i class="bi bi-x-circle text-danger"></i>
            </span>
            <?php endif; ?>
            
            <span class="input-group-text bg-light border-secondary border-start-0" style="border-radius: 0 4px 4px 0; cursor: pointer;" onclick="filterTable()">
                <i class="bi bi-search text-muted"></i>
            </span>
        </div>
    </div>
    
    <div class="d-flex flex-wrap justify-content-between align-items-center">
        <!-- Primary Actions -->
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-primary fw-bold px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalInputData" style="border-radius: 4px; font-size: 0.9rem;">
                NEW
            </button>
            
            <div class="dropdown">
                <button class="btn btn-outline-secondary fw-bold dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 4px; font-size: 0.9rem;">
                    <i class="bi bi-gear-fill me-1"></i> Actions
                </button>
                <ul class="dropdown-menu shadow-sm" style="font-size: 0.9rem;">
                    <li><h6 class="dropdown-header text-uppercase" style="font-size:0.7rem; letter-spacing: 1px;">Konfigurasi Masal</h6></li>
                    <li><a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#modalBulkPriceUpdate"><i class="bi bi-cash-coin me-2 text-warning"></i>Adjust Harga Massal</a></li>
                    <li><a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#modalBulkSetup"><i class="bi bi-layers me-2 text-danger"></i>Bulk Setup WIP</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><h6 class="dropdown-header text-uppercase" style="font-size:0.7rem; letter-spacing: 1px;">Master Data</h6></li>
                    <li><a class="dropdown-item py-2" href="#" onclick="$('#modalkategori').modal('show')"><i class="bi bi-tags me-2 text-primary"></i>Kelola Kategori</a></li>
                    <li><a class="dropdown-item py-2" href="#" onclick="$('#modalsubkategori').modal('show')"><i class="bi bi-diagram-2 me-2 text-info"></i>Kelola Sub Kategori</a></li>
                    <li><a class="dropdown-item py-2" href="<?= base_url('warehouse/materials') ?>"><i class="bi bi-box-seam me-2 text-success"></i>Bahan Baku (Gudang)</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item py-2" href="<?= base_url('manufaktur/catalog') ?>"><i class="bi bi-journal-text me-2 text-dark"></i>Mode Katalog (Grid)</a></li>
                    <li><a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#modalSopProduk"><i class="bi bi-info-circle me-2 text-muted"></i>SOP / Panduan</a></li>
                </ul>
            </div>

            <button id="btnDeleteSelected" class="btn btn-outline-danger shadow-sm ms-2" style="display:none; border-radius: 4px; font-size: 0.9rem;">
                <i class="bi bi-trash"></i> Hapus (<span id="selectedCount">0</span>)
            </button>
        </div>
        
        <!-- Right Filters & View Switcher -->
        <div class="d-flex gap-3 align-items-center">
            <!-- Filter Dropdown -->
            <div class="dropdown">
                <button class="btn btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown" style="border-radius: 4px; font-size: 0.9rem;">
                    <i class="bi bi-funnel text-muted me-1"></i> Filter
                </button>
                <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm border-0" style="width: 280px;">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Kategori</label>
                        <select class="form-select form-select-sm" id="filterKategori">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($kategori as $kat): ?>
                                <option value="<?= $kat->kategori_id ?>" <?= $this->input->get('kategori') == $kat->kategori_id ? 'selected' : '' ?>><?= $kat->kategori_nama ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Status</label>
                        <select class="form-select form-select-sm" id="filterStatus">
                            <option value="">Semua Status</option>
                            <option value="complete" <?= $this->input->get('status') == 'complete' ? 'selected' : '' ?>>Ready</option>
                            <option value="partial" <?= $this->input->get('status') == 'partial' ? 'selected' : '' ?>>Partial</option>
                            <option value="empty" <?= $this->input->get('status') == 'empty' ? 'selected' : '' ?>>Empty</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Sortir A-Z</label>
                        <select class="form-select form-select-sm" id="filterSort">
                            <option value="asc" <?= $this->input->get('sort', TRUE) !== 'desc' ? 'selected' : '' ?>>A-Z</option>
                            <option value="desc" <?= $this->input->get('sort', TRUE) === 'desc' ? 'selected' : '' ?>>Z-A</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Baris per Halaman</label>
                        <select class="form-select form-select-sm" id="pageSize">
                            <?php foreach ([20, 50, 100, 200] as $pageSizeOption): ?>
                                <option value="<?= $pageSizeOption ?>" <?= (int) $per_page === $pageSizeOption ? 'selected' : '' ?>><?= $pageSizeOption ?> / Hal</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button class="btn btn-sm btn-outline-danger w-100 fw-bold" onclick="resetFilters()">Reset Filter</button>
                </div>
            </div>

            <!-- View Switcher -->
            <div class="product-view-switch border rounded d-flex bg-light p-1 shadow-sm" aria-label="Tampilan produk" style="border-radius: 4px;">
                <button type="button" class="btn btn-sm product-view-btn active border-0" data-view="list" title="List view" style="border-radius: 2px;">
                    <i class="bi bi-list-ul"></i>
                </button>
                <button type="button" class="btn btn-sm product-view-btn border-0" data-view="card" title="Card view" style="border-radius: 2px;">
                    <i class="bi bi-grid-3x3-gap"></i>
                </button>
            </div>
            
            <!-- Mini Pagination Nav -->
            <div class="d-none d-md-flex align-items-center gap-2">
                <span class="text-muted small fw-bold" style="font-size: 0.85rem;">
                    <?= $offset + 1 ?>-<?= min($offset + $per_page, $total_products) ?> / <?= $total_products ?>
                </span>
                <div class="btn-group shadow-sm">
                    <a href="<?= $current_page > 1 ? $buildPageUrl($current_page - 1) : '#' ?>" class="btn btn-sm btn-light border <?= $current_page <= 1 ? 'disabled' : '' ?>"><i class="bi bi-chevron-left"></i></a>
                    <a href="<?= $current_page < $total_pages ? $buildPageUrl($current_page + 1) : '#' ?>" class="btn btn-sm btn-light border <?= $current_page >= $total_pages ? 'disabled' : '' ?>"><i class="bi bi-chevron-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 px-md-5">

    <!-- ====== CONTENT CARD ====== -->
    <div class="content-card shadow-sm border-0" style="background: transparent;">
        <div class="alphabet-filter-bar mb-3 bg-white p-2 rounded shadow-sm border">
            <?php
            $current_starts_with = $this->input->get('starts_with');
            ?>
            <button type="button" class="btn alphabet-filter-btn <?= empty($current_starts_with) ? 'active' : '' ?>"
                data-starts-with="">ALL</button>
            <?php foreach (($alphabet_filters ?? []) as $letter): ?>
                <button type="button" class="btn alphabet-filter-btn <?= $current_starts_with == $letter ? 'active' : '' ?>"
                    data-starts-with="<?= $letter ?>"><?= $letter ?></button>
            <?php endforeach; ?>
        </div>


        <div class="d-flex justify-content-between mb-2">
            <div>
                <button id="btnDeleteSelected" class="btn btn-danger btn-sm shadow-sm" style="display:none; border-radius: 8px;">
                    <i class="bi bi-trash me-1"></i> Hapus <span id="selectedCount">0</span> Terpilih
                </button>
            </div>
        </div>

        <div id="productTableView" class="product-data-view active" style="display:block;">
            <div class="table-responsive">
                <table class="table table-hover table-premium mb-0" id="productTable">
                    <thead>
                        <?php
                        $current_orderby = $this->input->get('orderby') ?: 'product_nama';
                        $current_sort = $this->input->get('sort') ?: 'asc';
                        $next_sort = $current_sort == 'asc' ? 'desc' : 'asc';

                        function get_sort_icon($field, $current_orderby, $current_sort)
                        {
                            if ($current_orderby != $field)
                                return '<i class="bi bi-arrow-down-up sort-icon"></i>';
                            return $current_sort == 'asc' ? '<i class="bi bi-sort-alpha-down sort-icon"></i>' : '<i class="bi bi-sort-alpha-up-alt sort-icon"></i>';
                        }
                        ?>
                        <tr>
                            <th width="3%" class="text-center align-middle">
                                <input type="checkbox" id="checkAll" class="form-check-input shadow-sm border-secondary" style="cursor: pointer;">
                            </th>
                            <th width="4%"
                                class="text-center sortable <?= $current_orderby == 'product_id' ? 'active' : '' ?>"
                                onclick="sortBy('product_id', '<?= $current_orderby == 'product_id' ? $next_sort : 'asc' ?>')">
                                ID <?= get_sort_icon('product_id', $current_orderby, $current_sort) ?>
                            </th>
                            <th width="22%" class="sortable <?= $current_orderby == 'product_nama' ? 'active' : '' ?>"
                                onclick="sortBy('product_nama', '<?= $current_orderby == 'product_nama' ? $next_sort : 'asc' ?>')">
                                Nama Produk <?= get_sort_icon('product_nama', $current_orderby, $current_sort) ?>
                            </th>
                            <th width="18%">Klasifikasi</th>
                            <th width="16%">Spesifikasi</th>
                            <th width="12%">Status Setup</th>
                            <th width="15%">Harga &amp; HPP</th>
                            <?php if ($this->session->userdata('level') == '1'): ?>
                                <th width="8%" class="text-center">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $start_no = $offset + 1;
                        if (empty($tampilData)) {
                            echo '<tr><td colspan="10" class="text-center py-5 text-muted">Belum ada data produk tersedia</td></tr>';
                        } else {
                            foreach ($tampilData as $dt):
                                $jml_detail = $this->M_master->jml_detail($dt->product_id)->num_rows();
                                $jml_routing = $this->M_master->jml_routing($dt->product_id)->num_rows();
                                $jml_bom = $this->M_master->jml_bom($dt->product_id)->num_rows();
                                $is_complete = ($jml_detail > 0 && $jml_bom > 0 && $jml_routing > 0);
                                $status_str = $is_complete ? 'complete' : (($jml_detail > 0 || $jml_bom > 0 || $jml_routing > 0) ? 'incomplete' : 'empty');
                                ?>
                                <tr data-product="<?= strtolower($dt->product_nama) ?>"
                                    data-kategori="<?= strtolower(isset($dt->kategori_nama) ? $dt->kategori_nama : 'N/A') ?>"
                                    data-status="<?= $status_str ?>">
                                    <td class="text-center align-middle">
                                        <input type="checkbox" class="form-check-input checkItem shadow-sm border-secondary" value="<?= $dt->product_id ?>" style="cursor: pointer;">
                                    </td>
                                    <td class="text-center text-muted fw-bold" style="font-size: 0.8rem;">
                                        <div class="mb-1"><?= $start_no++ ?></div>
                                        <?php
                                        if (preg_match('/\[ADJUST HARGA (.*?): (.*?)\]/', $dt->product_deskripsi ?? '', $matches)):
                                            $last_date = $matches[1];
                                            ?>
                                            <div class="text-primary" style="font-size: 0.6rem; font-weight: 500;"
                                                title="Terakhir Update Harga">
                                                <i class="bi bi-clock-history me-1"></i><?= $last_date ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <a href="<?= base_url('manufaktur/detail/' . $dt->product_id) ?>"
                                                class="fw-bold text-slate-800 text-decoration-none hover-primary"
                                                style="font-size: 0.95rem;">
                                                <?= $dt->product_nama ?>
                                            </a>

                                            <?php if (strpos($dt->product_deskripsi ?? '', '[ADJUST HARGA') !== false): ?>
                                                <span class="status-badge"
                                                    style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;"
                                                    title="Harga telah disesuaikan secara massal">
                                                    <i class="fas fa-tag me-1" style="font-size: 0.6rem;"></i> PRICE ADJ
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="fw-bold text-slate-900" style="font-size: 0.95rem;">
                                            <?= isset($dt->kategori_nama) ? $dt->kategori_nama : '-' ?>
                                        </div>
                                        <div class="text-slate-600 fw-medium" style="font-size: 0.85rem;">
                                            <?= isset($dt->subkategori_nama) ? $dt->subkategori_nama : '-' ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1 mb-2">
                                            <div
                                                style="font-size: 0.62rem; color: #64748b; font-weight: 900; text-transform: uppercase; letter-spacing: 0.8px; display: flex; align-items: center;">
                                                <?= isset($dt->tipe_nama) ? $dt->tipe_nama : '-' ?>
                                            </div>
                                            <?php if (isset($dt->side_name) && $dt->side_name != '-'): ?>
                                                <div class="badge rounded-pill"
                                                    style="background: #f0f7ff; color: #0284c7; border: 1px solid #e0f2fe; font-size: 0.65rem; padding: 0.15rem 0.5rem; font-weight: 800;">
                                                    <i class="bi bi-layers-fill me-1 opacity-50"></i><?= $dt->side_name ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="badge rounded-pill"
                                                style="background: #eef2ff; color: #6366f1; border: 1px solid #e0e7ff; font-size: 0.65rem; padding: 0.15rem 0.5rem; font-weight: 800;">
                                                <i class="bi bi-shop me-1 opacity-50"></i><?= isset($dt->metode_nama) ? $dt->metode_nama : '-' ?>
                                            </div>
                                        </div>

                                        <div class="fw-bold text-slate-700 mb-1" style="font-size: 0.85rem;">
                                            <i class="bi bi-box-seam me-1 text-muted"></i><?= isset($dt->satuan_nama) ? $dt->satuan_nama : '-' ?>
                                        </div>

                                        <?php
                                        $active_schema = !empty($dt->product_schema) ? $dt->product_schema :
                                            (!empty($dt->subkategori_schema) ? $dt->subkategori_schema :
                                                (!empty($dt->kategori_schema) ? $dt->kategori_schema : null));

                                        if ($active_schema) {
                                            $schema_arr = json_decode($active_schema, true);
                                            if (is_array($schema_arr)) {
                                                $schema_limit = 0;
                                                foreach ($schema_arr as $field) {
                                                    if ($schema_limit >= 3)
                                                        break;
                                                    if (isset($field['type']) && in_array($field['type'], ['select', 'radio']) && !empty($field['options'])) {
                                                        $clean_options = array_filter(array_values($field['options']), function ($val) {
                                                            return trim($val) !== '' && stripos($val, 'Tidak Dipilih') === false && stripos($val, 'Pilih') === false;
                                                        });
                                                        if (!empty($clean_options)) {
                                                            $label = !empty($field['label']) ? $field['label'] : 'Pilihan';
                                                            $options_str = implode(', ', $clean_options);
                                                            echo '<div class="text-truncate" style="max-width: 150px; font-size: 0.72rem; line-height: 1.3;" title="' . htmlspecialchars($label . ': ' . $options_str) . '">';
                                                            echo '<span class="fw-bold text-slate-600">' . htmlspecialchars($label) . ':</span> <span class="text-muted">' . htmlspecialchars($options_str) . '</span>';
                                                            echo '</div>';
                                                            $schema_limit++;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        ?>

                                        <?php if (!empty($dt->product_deskripsi)): ?>
                                            <div class="text-muted mt-1 text-truncate" style="max-width: 150px; font-size: 0.75rem;"
                                                title="<?= htmlspecialchars($dt->product_deskripsi) ?>">
                                                <i class="bi bi-info-circle me-1 opacity-50"></i><?= htmlspecialchars($dt->product_deskripsi) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                            <?php
                                            $setup_bg = '#fee2e2';
                                            $setup_color = '#b91c1c';
                                            $setup_text = 'EMPTY';
                                            if ($is_complete) {
                                                $setup_bg = '#dcfce7';
                                                $setup_color = '#15803d';
                                                $setup_text = 'READY';
                                            } elseif ($jml_detail > 0 || $jml_bom > 0 || $jml_routing > 0) {
                                                $setup_bg = '#fef3c7';
                                                $setup_color = '#b45309';
                                                $setup_text = 'PARTIAL';
                                            }

                                            $meta_stats = $this->db->query("
                                                SELECT 
                                                    COUNT(*) as total_variasi,
                                                    SUM(CASE WHEN metadata IS NOT NULL AND metadata != '' AND metadata != '[]' THEN 1 ELSE 0 END) as dengan_metadata
                                                FROM tbl_harga_product 
                                                WHERE product_id = ?
                                            ", [$dt->product_id])->row();

                                            $total_v = $meta_stats->total_variasi ?? 0;
                                            $with_m = $meta_stats->dengan_metadata ?? 0;

                                            $meta_bg = '#f1f5f9';
                                            $meta_color = '#64748b';
                                            $meta_text = 'NO PRICE';

                                            if ($total_v > 0) {
                                                if ($with_m == $total_v) {
                                                    $meta_bg = '#e0f2fe';
                                                    $meta_color = '#0369a1';
                                                    $meta_text = 'VAR READY';
                                                } elseif ($with_m > 0) {
                                                    $meta_bg = '#fffbeb';
                                                    $meta_color = '#d97706';
                                                    $meta_text = 'VAR ' . $with_m . '/' . $total_v;
                                                } else {
                                                    $meta_bg = '#fef2f2';
                                                    $meta_color = '#dc2626';
                                                    $meta_text = 'VAR EMPTY';
                                                }
                                            }
                                            ?>
                                            <div class="setup-status-group">
                                                <span class="setup-status-pill" style="background: <?= $setup_bg ?>; color: <?= $setup_color ?>;">
                                                    <?= $setup_text ?>
                                                </span>
                                                <span class="setup-status-pill" style="background: <?= $meta_bg ?>; color: <?= $meta_color ?>;">
                                                    <?= $meta_text ?>
                                                </span>
                                            </div>
                                    </td>
                                    <td>
                                        <?php
                                        $avg_price = 0;
                                        if ($dt->min_price > 0 && $dt->max_price > 0) {
                                            $avg_price = ($dt->min_price + $dt->max_price) / 2;
                                        } elseif ($dt->min_price > 0) {
                                            $avg_price = $dt->min_price;
                                        }
                                        
                                        if ($avg_price > 0):
                                        ?>
                                            <div style="font-size: 0.85rem; font-weight: 800; color: #0f172a; margin-bottom: 0.4rem;">
                                                Rp <?= number_format($avg_price, 0, ',', '.') ?>
                                                <span style="font-size: 0.65rem; font-weight: 600; color: #64748b; margin-left: 4px;">(Rata-Rata)</span>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted d-block mb-2" style="font-size: 0.8rem;">Belum Diatur</span>
                                        <?php endif; ?>
                                        
                                        <?php if ($dt->min_price > 0 || $dt->calculated_hpp > 0): ?>
                                            <div class="d-flex flex-column gap-1">
                                                <div
                                                    style="font-size: 0.72rem; color: #64748b; display: inline-flex; align-items: center; gap: 4px; background: #f1f5f9; padding: 2px 8px; border-radius: 4px; width: fit-content;">
                                                    <i class="bi bi-tag-fill" style="font-size: 0.65rem;"></i>
                                                    <span style="font-weight: 700;">
                                                        <?php if ($dt->min_price > 0): ?>
                                                            Rp <?= number_format($dt->min_price, 0, ',', '.') ?> -
                                                            <?= number_format($dt->max_price, 0, ',', '.') ?>
                                                        <?php else: ?>
                                                            Price N/A
                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                                <div
                                                    style="font-size: 0.72rem; color: #ef4444; display: inline-flex; align-items: center; gap: 4px; background: #fef2f2; padding: 2px 8px; border-radius: 4px; border: 1px solid #fee2e2; width: fit-content;">
                                                    <i class="bi bi-calculator-fill" style="font-size: 0.65rem;"></i>
                                                    <span style="font-weight: 800;">HPP: Rp
                                                        <?= number_format($dt->calculated_hpp, 0, ',', '.') ?></span>
                                                </div>

                                                <?php
                                                if (preg_match('/\[ADJUST HARGA (.*?): (.*?)\]/', $dt->product_deskripsi ?? '', $matches)):
                                                    $adj_val = $matches[2];
                                                    $adj_color = (strpos($adj_val, '+') !== false) ? '#059669' : '#dc2626';
                                                    ?>
                                                    <div
                                                        style="font-size: 0.68rem; color: <?= $adj_color ?>; font-weight: 800; background: white; padding: 1px 6px; border: 1px solid <?= $adj_color ?>40; border-radius: 4px; width: fit-content;">
                                                        <i class="fas <?= (strpos($adj_val, '+') !== false) ? 'fa-arrow-up' : 'fa-arrow-down' ?> me-1"></i>
                                                        <?= $adj_val ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <?php if ($this->session->userdata('level') == '1'): ?>
                                        <td class="text-center">
                                            <div class="d-flex gap-1 justify-content-center">
                                                <button type="button" class="btn btn-light btn-action text-primary"
                                                    onclick="openEditProductModal(<?= $dt->product_id ?>)" data-bs-toggle="tooltip"
                                                    title="Step 1: Identitas Produk (Edit)">
                                                    <i class="bi bi-pencil-fill" style="font-size: 0.75rem;"></i>
                                                </button>
                                                <button class="btn btn-light btn-action text-info"
                                                    onclick="cloneProduct(<?= $dt->product_id ?>, '<?= addslashes($dt->product_nama) ?>')"
                                                    title="Duplicate">
                                                    <i class="bi bi-copy" style="font-size: 0.75rem;"></i>
                                                </button>
                                                <button class="btn btn-light btn-action text-danger btn-delete-product"
                                                    data-href="<?= base_url('manufaktur/deleteproduct/' . $dt->product_id) ?>"
                                                    data-product-name="<?= htmlspecialchars($dt->product_nama) ?>" title="Delete">
                                                    <i class="bi bi-trash-fill" style="font-size: 0.75rem;"></i>
                                                </button>
                                            </div>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach;
                        } // end else ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="productCardView" class="product-data-view">
            <?php if (empty($tampilData)): ?>
                <div class="text-center py-5 text-muted">Belum ada data produk tersedia</div>
            <?php else: ?>
                <div class="product-card-grid">
                    <?php foreach ($tampilData as $dt): ?>
                        <?php
                        $jml_detail = $this->M_master->jml_detail($dt->product_id)->num_rows();
                        $jml_routing = $this->M_master->jml_routing($dt->product_id)->num_rows();
                        $jml_bom = $this->M_master->jml_bom($dt->product_id)->num_rows();
                        $is_complete = ($jml_detail > 0 && $jml_bom > 0 && $jml_routing > 0);
                        $card_status = $is_complete ? 'READY' : (($jml_detail > 0 || $jml_bom > 0 || $jml_routing > 0) ? 'PARTIAL' : 'EMPTY');
                        $card_status_bg = $is_complete ? '#dcfce7' : (($jml_detail > 0 || $jml_bom > 0 || $jml_routing > 0) ? '#fef3c7' : '#fee2e2');
                        $card_status_color = $is_complete ? '#15803d' : (($jml_detail > 0 || $jml_bom > 0 || $jml_routing > 0) ? '#b45309' : '#b91c1c');
                        $card_price = $dt->min_price > 0 ? 'Rp ' . number_format($dt->min_price, 0, ',', '.') : 'Harga belum diatur';
                        ?>
                        <div class="product-card-item">
                            <div>
                                <?php
                                $card_image = !empty($dt->product_img)
                                    ? base_url('assets/images/product/' . $dt->product_img)
                                    : base_url('assets/images/noproduct.jpg');
                                ?>
                                <div class="product-card-image-wrap">
                                    <img src="<?= $card_image ?>" alt="<?= htmlspecialchars($dt->product_nama) ?>" class="product-card-image" loading="lazy">
                                </div>
                                <div class="product-card-top">
                                    <span class="product-card-id">#<?= $dt->product_id ?></span>
                                    <span class="product-card-status" style="background: <?= $card_status_bg ?>; color: <?= $card_status_color ?>;">
                                        <?= $card_status ?>
                                    </span>
                                </div>
                                <a href="<?= base_url('manufaktur/detail/' . $dt->product_id) ?>" class="product-card-name text-decoration-none">
                                    <?= $dt->product_nama ?>
                                </a>
                                <div class="product-card-meta">
                                    <div><strong>Kategori:</strong> <?= isset($dt->kategori_nama) ? $dt->kategori_nama : '-' ?></div>
                                    <div><strong>Sub:</strong> <?= isset($dt->subkategori_nama) ? $dt->subkategori_nama : '-' ?></div>
                                    <div><strong>Satuan:</strong> <?= isset($dt->satuan_nama) ? $dt->satuan_nama : '-' ?></div>
                                </div>
                            </div>
                            <div>
                                <div class="product-card-price"><?= $card_price ?></div>
                                <div class="mt-3 d-flex gap-2">
                                    <a href="<?= base_url('manufaktur/detail/' . $dt->product_id) ?>" class="btn btn-sm btn-dark flex-fill" style="border-radius:0;">Detail</a>
                                    <?php if ($this->session->userdata('level') == '1'): ?>
                                        <button type="button" class="btn btn-sm btn-light border" style="border-radius:0;" onclick="openEditProductModal(<?= $dt->product_id ?>)"><i class="bi bi-pencil"></i></button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <div class="text-muted small fw-500">
                    Menampilkan <span
                        class="fw-bold text-dark"><?= $offset + 1 ?>–<?= min($offset + $per_page, $total_products) ?></span>
                    dari <span class="fw-bold text-dark"><?= $total_products ?></span> produk
                </div>
                <nav aria-label="Product pagination">
                    <ul class="pagination pagination-sm mb-0">
                        <?php if ($current_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link border-0 rounded-circle mx-1 text-secondary"
                                    href="<?= $buildPageUrl($current_page - 1) ?>">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $current_page + 2);
                        if ($start_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link border-0 rounded-circle mx-1 text-secondary"
                                    href="<?= $buildPageUrl(1) ?>">1</a>
                            </li>
                            <?php if ($start_page > 2): ?>
                                <li class="page-item disabled"><span class="page-link border-0">…</span></li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                <a class="page-link border-0 rounded-circle mx-1 <?= $i == $current_page ? 'shadow-sm' : 'text-secondary' ?>"
                                    href="<?= $buildPageUrl($i) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($end_page < $total_pages): ?>
                            <?php if ($end_page < $total_pages - 1): ?>
                                <li class="page-item disabled"><span class="page-link border-0">…</span></li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link border-0 rounded-circle mx-1 text-secondary"
                                    href="<?= $buildPageUrl($total_pages) ?>"><?= $total_pages ?></a>
                            </li>
                        <?php endif; ?>

                        <?php if ($current_page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link border-0 rounded-circle mx-1 text-secondary"
                                    href="<?= $buildPageUrl($current_page + 1) ?>">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>

    </div><!-- /content-card -->
</div><!-- /container-fluid px-5 -->
</div><!-- /product-catalog-shell -->

<script>
    function setProductView(view) {
        const listView = document.getElementById('productTableView');
        const cardView = document.getElementById('productCardView');
        const buttons = document.querySelectorAll('.product-view-btn');

        if (!listView || !cardView) return;

        const isList = view === 'list';
        listView.style.display = isList ? 'block' : 'none';
        cardView.classList.toggle('active', !isList);
        listView.classList.toggle('active', isList);

        buttons.forEach((btn) => {
            btn.classList.toggle('active', btn.dataset.view === view);
        });

        try {
            localStorage.setItem('productViewMode', view);
        } catch (e) {}
    }

    document.addEventListener('DOMContentLoaded', function () {
        const savedView = localStorage.getItem('productViewMode') || 'list';
        setProductView(savedView);

        document.querySelectorAll('.product-view-btn').forEach((button) => {
            button.addEventListener('click', function () {
                setProductView(this.dataset.view);
            });
        });
    });
</script>

<?php $this->load->view('admin/product/components/v_modal_form_builder'); ?>


<?php $this->load->view('admin/product/components/v_modal_pricing'); ?>


<?php $this->load->view('admin/product/components/v_modal_routing'); ?>


<?php $this->load->view('admin/product/components/v_modal_edit_routing'); ?>


<?php $this->load->view('admin/product/components/v_modal_bom'); ?>

<?php $this->load->view('admin/product/components/v_modal_edit_product'); ?>


<?php $this->load->view('admin/product/components/v_modal_input_data'); ?>


<!-- Modal Bulk Setup -->
<?php $this->load->view('admin/product/components/v_modal_bulk_price'); ?>


<?php $this->load->view('admin/product/components/v_modal_bulk_setup'); ?>


<?php $this->load->view('admin/product/components/v_modal_kategori'); ?>


<?php $this->load->view('admin/product/components/v_modal_subkategori'); ?>


<?php $this->load->view('admin/product/components/v_modal_sop_produk'); ?>
<?php $this->load->view('admin/product/components/v_product_scripts'); ?>
