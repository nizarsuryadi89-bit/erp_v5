<!-- Core Assets & Dependencies -->
<link href="<?= base_url('assets/select2/css/select2.min.css') ?>" rel="stylesheet" />
<script src="<?= base_url('assets/select2/js/select2.full.min.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Initialize Global Config -->
<script>
    const PRODUCT_ID = '<?= $getProduct['product_id'] ?? '' ?>';
    const KATEGORI_ID = '<?= $getProduct['kategori_id'] ?? '' ?>';
    const BASE_URL = '<?= base_url() ?>';
</script>

<!-- Product Logic Script -->
<script src="<?= base_url('assets/js/product-detail.js?v=' . time()) ?>"></script>

<!-- External CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css?v=' . time()) ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/product-detail.css?v=' . time()) ?>">
<style>
    /* Force inline select2 to stay behind modals */
    .table-pro .select2-container {
        z-index: 1 !important;
    }

    /* Ensure dropdowns appear on top */
    .select2-container--open,
    .select2-dropdown {
        z-index: 99999 !important;
    }

    .quick-jump-container .select2-container .select2-selection--single {
        height: 38px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 4px !important;
        background-color: #ffffff !important;
        display: flex !important;
        align-items: center !important;
        font-size: 0.88rem !important;
        font-weight: 500 !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        transition: all 0.2s ease !important;
    }
    .quick-jump-container .select2-container .select2-selection--single:hover {
        border-color: #0284c7 !important;
    }
    .quick-jump-container .select2-container .select2-selection--single .select2-selection__rendered {
        color: #334155 !important;
        padding-left: 10px !important;
        padding-right: 20px !important;
        line-height: normal !important;
    }
    .quick-jump-container .select2-container .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
        right: 8px !important;
    }
</style>

<div class="app-content">
    <!-- ====== ODOO-STYLE FORM CONTROL PANEL ====== -->
    <div class="bg-white border-bottom px-4 py-3 mb-4 shadow-sm" style="position: sticky; top: 0; z-index: 900;">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
            <!-- Breadcrumbs & Title -->
            <div class="d-flex align-items-center gap-2">
                <a href="<?= base_url('manufaktur?kategori=' . $getProduct['kategori_id']) ?>"
                    class="text-decoration-none text-muted fw-bold" style="font-size: 1.3rem;">Products</a>
                <span class="text-muted fw-bold" style="font-size: 1.3rem;">/</span>
                <h4 class="mb-0 text-dark fw-bold" style="font-size: 1.3rem; letter-spacing: -0.5px;">
                    <?= $getProduct['product_nama'] ?>
                </h4>
            </div>

            <!-- Smart Buttons / Status -->
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-outline-secondary fw-bold" data-bs-toggle="modal"
                    data-bs-target="#modalSopProduk">
                    <i class="bi bi-journal-text me-1"></i> SOP
                </button>
                <?php if (isset($getProduct['status'])): ?>
                    <span
                        class="badge <?= $getProduct['status'] == 1 ? 'bg-success' : 'bg-danger' ?> px-3 py-2 border <?= $getProduct['status'] == 1 ? 'border-success' : 'border-danger' ?>"
                        style="font-size: 0.8rem;">
                        <i class="bi <?= $getProduct['status'] == 1 ? 'bi-check-circle' : 'bi-x-circle' ?> me-1"></i>
                        <?= $getProduct['status'] == 1 ? 'ACTIVE' : 'INACTIVE' ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mt-3">
            <div class="d-flex align-items-center gap-2">
                <a href="<?= base_url('manufaktur?kategori=' . $getProduct['kategori_id']) ?>"
                    class="btn btn-light border shadow-sm fw-bold px-3" style="border-radius: 4px; font-size: 0.9rem;">
                    <i class="bi bi-arrow-left me-1"></i> KEMBALI
                </a>
                <!-- <button class="btn btn-outline-primary fw-bold shadow-sm px-3"
                    onclick="copyToClipboard('<?= $getProduct['product_nama'] ?>')"
                    style="border-radius: 4px; font-size: 0.9rem;">
                    <i class="bi bi-clipboard me-1"></i> SALIN NAMA
                </button> -->
                <button type="button" class="btn btn-outline-secondary fw-bold shadow-sm px-3" data-bs-toggle="modal"
                    data-bs-target="#modalEditNamaProduk" style="border-radius: 4px; font-size: 0.9rem;">
                    <i class="bi bi-pencil-square me-1 text-primary"></i> Edit Nama Produk
                </button>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary fw-bold dropdown-toggle shadow-sm px-3" type="button"
                        data-bs-toggle="dropdown" style="border-radius: 4px; font-size: 0.9rem;">
                        <i class="bi bi-gear-fill me-1"></i> Actions
                    </button>
                    <ul class="dropdown-menu shadow-sm">
                        <li><a class="dropdown-item py-2" href="#" data-bs-toggle="modal"
                                data-bs-target="#modalEditNamaProduk"><i
                                    class="bi bi-pencil-square me-2 text-info"></i>Edit Nama Produk</a></li>
                        <li><a class="dropdown-item py-2" href="#" onclick="$('#bom-tab').tab('show')"><i
                                    class="bi bi-plus-circle me-2 text-primary"></i>Tambah BOM</a></li>
                        <li><a class="dropdown-item py-2" href="#" onclick="$('#routing-tab').tab('show')"><i
                                    class="bi bi-plus-circle me-2 text-warning"></i>Tambah Routing</a></li>
                    </ul>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- Quick Search & Jump to Product -->
                <div class="quick-jump-container" style="min-width: 250px; max-width: 320px;">
                    <select id="quickJumpProductSelect" class="form-select form-select-sm" style="width: 100%;">
                        <option value="">🔍 Cari & loncat produk...</option>
                    </select>
                </div>

                <?php if (!empty($prevProduct)): ?>
                    <a href="<?= base_url('manufaktur/detail/' . $prevProduct['product_id']) ?>"
                        class="btn btn-outline-primary shadow-sm fw-bold px-3"
                        style="border-radius: 4px; font-size: 0.9rem;"
                        title="<?= htmlspecialchars($prevProduct['product_nama']) ?>">
                        <i class="bi bi-arrow-left me-1"></i> SEBELUMNYA
                    </a>
                <?php endif; ?>

                <?php if (!empty($nextProduct)): ?>
                    <a href="<?= base_url('manufaktur/detail/' . $nextProduct['product_id']) ?>"
                        class="btn btn-primary shadow-sm fw-bold px-3" style="border-radius: 4px; font-size: 0.9rem;"
                        title="<?= htmlspecialchars($nextProduct['product_nama']) ?>">
                        SELANJUTNYA <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="container-fluid px-4 px-md-5">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="d-flex align-items-center gap-3 bg-white p-3 border rounded shadow-sm">
                    <div class="bg-light border rounded p-3 text-center" style="width: 80px; height: 80px;">
                        <i class="bi bi-box-seam text-secondary" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <span class="badge bg-primary-subtle text-primary mb-1 px-2 py-1 rounded-pill fw-medium"
                            style="font-size: 0.75rem;">
                            <?= $getProduct['kategori_nama'] ?>
                            <?= !empty($getProduct['subkategori_nama']) ? ' - ' . $getProduct['subkategori_nama'] : '' ?>
                        </span>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h4 class="fw-bold text-dark mb-0"><?= $getProduct['product_nama'] ?></h4>
                            <button type="button" class="btn btn-sm btn-link text-muted p-0 text-decoration-none"
                                data-bs-toggle="modal" data-bs-target="#modalEditNamaProduk" title="Edit Nama Produk">
                                <i class="bi bi-pencil-square text-primary" style="font-size: 1.1rem;"></i>
                            </button>
                        </div>
                        <div class="text-secondary small fw-medium">
                            <i class="bi bi-tags me-1"></i> Jenis: <strong
                                class="text-dark"><?= $getProduct['tipe_nama'] ?? '-' ?></strong>
                            <span class="mx-2 text-muted">|</span>
                            <i class="bi bi-box me-1"></i> Satuan: <?= $getProduct['satuan_nama'] ?? '-' ?>
                            <span class="mx-2 text-muted">|</span>
                            <i class="bi bi-upc-scan me-1"></i> ID Produk: <?= $getProduct['product_id'] ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <style>
                    /* Wizard Step Styling */
                    .wizard-tabs {
                        border-bottom: none;
                        gap: 0.5rem;
                        flex-wrap: nowrap;
                        overflow-x: auto;
                        padding-bottom: 0.5rem;
                    }

                    .wizard-tabs .nav-link {
                        border: 1px solid #e2e8f0;
                        border-radius: 8px;
                        color: #64748b;
                        font-weight: 600;
                        font-size: 0.8rem;
                        background: #f8fafc;
                        display: flex;
                        align-items: center;
                        white-space: nowrap;
                        transition: all 0.2s ease;
                    }

                    .wizard-tabs .nav-link .step-num {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        width: 24px;
                        height: 24px;
                        background: #cbd5e1;
                        color: #fff;
                        border-radius: 50%;
                        margin-right: 8px;
                        font-size: 0.7rem;
                    }

                    .wizard-tabs .nav-link.active {
                        background: #0284c7;
                        color: #fff;
                        border-color: #0284c7;
                        box-shadow: 0 4px 6px -1px rgba(2, 132, 199, 0.2);
                    }

                    .wizard-tabs .nav-link.active .step-num {
                        background: #fff;
                        color: #0284c7;
                    }

                    .wizard-tabs .nav-link:hover:not(.active) {
                        background: #e2e8f0;
                    }

                    /* Custom Scrollbar for Wizard Tabs */
                    .wizard-tabs::-webkit-scrollbar {
                        height: 6px;
                    }

                    .wizard-tabs::-webkit-scrollbar-track {
                        background: #f1f5f9;
                        border-radius: 4px;
                    }

                    .wizard-tabs::-webkit-scrollbar-thumb {
                        background: #cbd5e1;
                        border-radius: 4px;
                    }
                </style>

                <!-- Navigation Interface (Wizard) -->
                <ul class="nav nav-tabs wizard-tabs" id="productTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="form-builder-tab" data-bs-toggle="tab"
                            data-bs-target="#form_builder_tab_content" type="button">
                            <span class="step-num">1</span> SPESIFIKASI (FORM)
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="bom-tab" data-bs-toggle="tab" data-bs-target="#bom" type="button">
                            <span class="step-num">2</span> RESEP (BOM)
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="dynamic-bom-tab" data-bs-toggle="tab"
                            data-bs-target="#dynamic_bom_tab_content" type="button">
                            <span class="step-num">3</span> MAPPING BOM DINAMIS
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="routing-tab" data-bs-toggle="tab" data-bs-target="#routing"
                            type="button">
                            <span class="step-num">4</span> ALUR KERJA
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="hpp-tab" data-bs-toggle="tab" data-bs-target="#hpp" type="button">
                            <span class="step-num">5</span> ANALISA HPP & HARGA
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="harga-tab" data-bs-toggle="tab" data-bs-target="#harga"
                            type="button">
                            <span class="step-num">6</span> FINALISASI HARGA
                        </button>
                    </li>
                </ul>

                <div class="card border-0 shadow-sm p-4 text-bg-light">
                    <div class="tab-content" id="productTabContent">
                        <!-- Finalisasi Harga (ReadOnly Matrix) -->
                        <div class="tab-pane fade" id="harga" role="tabpanel">
                            <div class="card border-0 p-4 rounded-4 shadow-sm h-100">
                                <h6 class="fw-bold mb-4 text-uppercase letter-spacing-1 text-primary"><i
                                        class="bi bi-check-circle-fill me-2"></i> Finalisasi Matriks Harga Jual</h6>
                                <div id="profitMatrixContent">
                                    <div class="text-center py-5 text-muted">
                                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                        </div>
                                        Memuat matriks harga akhir...
                                    </div>
                                </div>
                                <hr class="my-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold mb-1">Status Publikasi Produk</h6>
                                        <p class="text-muted small mb-0">Aktifkan produk agar dapat digunakan dalam
                                            transaksi atau SPK.</p>
                                    </div>
                                    <button type="button" id="btnToggleProductStatus"
                                        class="btn <?= $getProduct['status'] == 1 ? 'btn-danger' : 'btn-success' ?> rounded-pill px-4 fw-bold shadow-sm"
                                        onclick="toggleProductStatus(<?= $getProduct['product_id'] ?>, <?= $getProduct['status'] == 1 ? 0 : 1 ?>)">
                                        <?= $getProduct['status'] == 1 ? '<i class="bi bi-x-circle me-2"></i>Nonaktifkan Produk' : '<i class="bi bi-rocket-takeoff me-2"></i>Launching Produk' ?>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Form Builder -->
                        <div class="tab-pane fade show active" id="form_builder_tab_content" role="tabpanel">
                            <?php
                            $fb_data = [
                                'kategori' => [
                                    'kategori_id' => $getProduct['product_id'],
                                    'kategori_nama' => $getProduct['product_nama'],
                                    'form_schema' => isset($getProduct['product_form_schema']) ? $getProduct['product_form_schema'] : ''
                                ],
                                'is_product' => true,
                                'is_tab' => true
                            ];
                            $this->load->view('admin/kategori/form_builder', $fb_data);
                            ?>
                            <div class="text-end mt-4 pt-3 border-top"><button type="button"
                                    class="btn btn-primary rounded-pill px-4"
                                    onclick="$('#bom-tab').tab('show')">Selanjutnya: Resep (BOM) <i
                                        class="bi bi-arrow-right ms-2"></i></button></div>
                        </div>

                        <!-- Dynamic BOM Mapping -->
                        <div class="tab-pane fade" id="dynamic_bom_tab_content" role="tabpanel">
                            <?php $this->load->view('admin/product/dynamic_bom'); ?>
                            <div class="text-end mt-4 pt-3 border-top"><button type="button"
                                    id="btn-selanjutnya-alur-kerja" class="btn btn-primary rounded-pill px-4"
                                    onclick="if(typeof saveAllDynamicBomAndNext === 'function'){ saveAllDynamicBomAndNext(); } else { $('#routing-tab').tab('show'); }">Selanjutnya:
                                    Alur Kerja <i class="bi bi-arrow-right ms-2"></i></button></div>
                        </div>

                        <!-- Bill of Materials -->
                        <div class="tab-pane fade" id="bom" role="tabpanel">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h5 class="fw-bold m-0"><i class="bi bi-gear-fill me-2 text-primary"></i>Bill of
                                    Materials Configuration</h5>
                                <div class="d-flex align-items-center gap-2">
                                    <button class="btn btn-danger rounded-3 shadow-sm d-none" id="btnBulkDeleteBOM">
                                        <i class="bi bi-trash me-1"></i> Hapus Terpilih (<span
                                            id="bulkDeleteBomCount">0</span>)
                                    </button>
                                    <button class="btn btn-outline-primary rounded-3 shadow-sm"
                                        onclick="openCopyModal()">
                                        <i class="bi bi-files me-1"></i> Copy BOM & Routing
                                    </button>
                                    <button class="btn btn-primary rounded-3 shadow-sm" data-bs-toggle="collapse"
                                        data-bs-target="#addBomSection">
                                        <i class="bi bi-plus-lg me-1"></i> Input Item Baru
                                    </button>
                                </div>
                            </div>

                            <div class="collapse mb-4" id="addBomSection">
                                <div class="card card-body bg-light border-0 rounded-4">
                                    <form id="addBomForm">
                                        <input type="hidden" id="bomProductIdInput" name="product_id"
                                            value="<?= $getProduct['product_id'] ?>">
                                        <input type="hidden" name="harga_id" value="">
                                        <?php
                                        $variations = $this->db->get_where('tbl_harga_product', ['product_id' => $getProduct['product_id']])->result();
                                        foreach ($variations as &$v) {
                                            $var_name = $v->detail_product ?: 'Variasi ID: ' . $v->harga_id;
                                            $meta_desc = [];
                                            if (!empty($v->metadata)) {
                                                $meta = json_decode($v->metadata, true);
                                                if (is_array($meta)) {
                                                    $ignored = ['product_id', 'nospk', 'qty', 'panjang', 'lebar', 'kategori_id', 'keterangan', 'approve', 'lokasi_cetak'];
                                                    foreach ($meta as $k => $val) {
                                                        if (!in_array($k, $ignored) && strpos($k, 'dim_') === false && strpos($k, '_id') === false && $val !== '') {
                                                            $meta_desc[] = $val;
                                                        }
                                                    }
                                                }
                                            }
                                            if (empty($meta_desc)) {
                                                if (!empty($v->ket_1) && $v->ket_1 !== '0')
                                                    $meta_desc[] = $v->ket_1;
                                                if (!empty($v->jenisprint_id) && $v->jenisprint_id !== '0')
                                                    $meta_desc[] = $v->jenisprint_id;
                                                if (!empty($v->side_id) && $v->side_id !== '0')
                                                    $meta_desc[] = $v->side_id;
                                            }
                                            if (!empty($meta_desc)) {
                                                $var_name .= ' (' . implode(' | ', $meta_desc) . ')';
                                            }
                                            $v->display_name = $var_name;
                                        }
                                        unset($v);
                                        ?>
                                        <script>
                                            window.productVariations = <?= json_encode($variations) ?>;
                                        </script>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold small">PILIH MATERIAL</label>
                                                <select class="form-select select2-bahan shadow-none" id="bahan_id"
                                                    name="bahan_id" required>
                                                    <option value="">-- Cari Material --</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-bold small text-warning"><i
                                                        class="bi bi-calculator me-1"></i>KAPASITAS</label>
                                                <input type="number" class="form-control border-warning"
                                                    id="add_kapasitas_kalkulator" placeholder="1 Lbr muat?" required>
                                                <div class="form-text mt-1 text-warning"
                                                    style="font-size: 0.65rem; line-height: 1.2;">
                                                    Ketik jumlah jadi
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-bold small text-muted">NET QTY</label>
                                                <input type="number" class="form-control bg-light" name="qty"
                                                    id="add_qty" step="any" required readonly>
                                                <div class="form-text mt-1 text-muted"
                                                    style="font-size: 0.65rem; line-height: 1.2;">
                                                    <i class="fas fa-info-circle"></i> Otomatis terisi
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <label class="form-label fw-bold small">SCRAP(%)</label>
                                                <input type="number" class="form-control" name="scrap_factor" step="0.1"
                                                    value="0">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-bold small">SATUAN</label>
                                                <input type="text" class="form-control bg-white" id="satuan" readonly>
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="submit"
                                                    class="btn btn-primary w-100 fw-bold">SIMPAN</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover table-sm align-middle" id="bomTable"
                                    style="font-size: 0.85rem;">
                                    <thead>
                                        <tr>
                                            <th width="30" class="text-center">
                                                <input class="form-check-input" type="checkbox" id="selectAllBOM">
                                            </th>
                                            <th width="50" class="text-center">#</th>
                                            <th>MATERIAL</th>
                                            <th>VARIASI</th>
                                            <th class="text-center">DIMENSI</th>
                                            <th class="text-center">NET QTY</th>
                                            <th class="text-center">SCRAP</th>
                                            <th class="text-center">GROSS REQ</th>
                                            <th class="text-end" title="Total Biaya (Gross Req &times; Harga Satuan)">
                                                EST. TOTAL BIAYA</th>
                                            <th class="text-center">SATUAN</th>
                                            <th class="text-center">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bomTableBody">
                                        <tr>
                                            <td colspan="11" class="text-center py-4">Loading BOM Data...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Manufacturing Operations -->
                        <div class="tab-pane fade" id="routing" role="tabpanel">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h5 class="fw-bold m-0"><i
                                        class="bi bi-diagram-3-fill me-2 text-warning"></i>Manufacturing Operations</h5>
                                <div class="d-flex align-items-center gap-2">
                                    <button class="btn btn-danger rounded-3 shadow-sm d-none" id="btnBulkDeleteRouting">
                                        <i class="bi bi-trash me-1"></i> Hapus Terpilih (<span
                                            id="bulkDeleteRoutingCount">0</span>)
                                    </button>
                                    <button class="btn btn-outline-warning rounded-3 shadow-sm"
                                        onclick="openCopyModal()">
                                        <i class="bi bi-files me-1"></i> Copy BOM & Routing
                                    </button>
                                    <button class="btn btn-warning text-white rounded-3 shadow-sm"
                                        data-bs-toggle="collapse" data-bs-target="#addRoutingSection">
                                        <i class="bi bi-plus-lg me-1"></i> Tambah Langkah
                                    </button>
                                </div>
                            </div>

                            <div class="collapse mb-4" id="addRoutingSection">
                                <div class="card card-body bg-light border-0 rounded-4">
                                    <form id="addRoutingForm">
                                        <input type="hidden" id="routingProductIdInput" name="product_id"
                                            value="<?= $getProduct['product_id'] ?>">
                                        <div class="row g-3">
                                            <div class="col-md-2">
                                                <label class="form-label fw-bold small">SEQ</label>
                                                <input type="number" class="form-control" name="routing_urutan"
                                                    placeholder="10" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold small">NAMA PROSES</label>
                                                <input type="text" class="form-control" name="routing_name"
                                                    placeholder="Setting, Printing, dll" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold small">OPERATOR / DEPT</label>
                                                <select class="form-select select2-operator" id="operator_id"
                                                    name="operator_id" required>
                                                    <option value="">-- Pilih --</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold small">BOM TERKAIT</label>
                                                <select class="form-select select2-bom" id="bom_line_id"
                                                    name="bom_line_id">
                                                    <option value="99999">-- Tanpa Material --</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold small text-primary"><i class="bi bi-tag me-1"></i>VARIASI</label>
                                                <select class="form-select" id="routing_harga_id" name="harga_id">
                                                    <option value="">-- Semua Variasi (Global) --</option>
                                                    <?php if (!empty($variations)): ?>
                                                        <?php foreach ($variations as $v): ?>
                                                            <option value="<?= $v->harga_id ?>"><?= htmlspecialchars($v->display_name ?? $v->detail_product) ?></option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-bold small">EST. WAKTU (MNT)</label>
                                                <input type="number" class="form-control" name="est_time" value="0">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold small">BIAYA TENAGA KERJA (RP)</label>
                                                <input type="text" class="form-control rupiah-input" name="labor_cost"
                                                    value="0">
                                            </div>
                                            <div class="col-md-3 d-flex align-items-end">
                                                <button type="submit"
                                                    class="btn btn-warning text-white w-100 fw-bold">SIMPAN LANGKAH
                                                    KERJA</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="30" class="text-center">
                                                <input class="form-check-input" type="checkbox" id="selectAllRouting">
                                            </th>
                                            <th width="80" class="text-center">SEQ</th>
                                            <th>OPERASI / LANGKAH</th>
                                            <th>VARIASI</th>
                                            <th>WORK CENTER</th>
                                            <th class="text-center">WAKTU</th>
                                            <th class="text-end">BIAYA</th>
                                            <th>BOM LINK</th>
                                            <th class="text-center">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody id="routingTableBody">
                                        <tr>
                                            <td colspan="9" class="text-center py-4">Loading Routing Data...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Financial Analysis -->
                        <div class="tab-pane fade" id="hpp" role="tabpanel">
                            <div class="row g-4 mb-4">
                                <!-- Production BOM Breakdown -->
                                <div class="col-lg-6">
                                    <div class="card border-0 bg-light p-4 rounded-4 shadow-sm h-100">
                                        <h6 class="fw-bold mb-4 text-uppercase letter-spacing-1">Production BOM
                                            Breakdown</h6>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="text-muted">Total Material</span>
                                            <span class="fw-bold" id="totalMaterialCost">Rp 0</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="text-muted">Total Tenaga Kerja</span>
                                            <span class="fw-bold" id="totalLaborCost">Rp 0</span>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between mb-4 pb-3 border-bottom border-dashed">
                                            <span class="text-muted">Overhead (5%)</span>
                                            <span class="fw-bold" id="totalOverheadCost">Rp 0</span>
                                        </div>
                                        <div
                                            class="p-4 bg-white rounded-3 border-start border-4 border-success mt-auto mb-3">
                                            <div class="small fw-bold text-muted mb-1 text-uppercase">Estimasi Total HPP
                                                / Unit (Termurah)</div>
                                            <div class="h3 fw-bold text-success mb-0" id="hppGrandTotal">Rp 0</div>
                                            <div class="form-text mt-2 text-muted"
                                                style="font-size: 0.7rem; line-height: 1.3;">
                                                <i class="fas fa-info-circle text-info"></i> Angka ini merupakan
                                                estimasi modal (base cost) <b>terendah</b>.
                                            </div>
                                        </div>

                                        <!-- HPP Per Variation List -->
                                        <div id="variationHppContainer" class="mt-3">
                                            <div class="small fw-bold text-muted mb-2 text-uppercase">HPP Berdasarkan
                                                Variasi</div>
                                            <div id="variationHppList" class="d-flex flex-column gap-2">
                                                <!-- Populated via JS -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Simulator Card -->
                                <style>
                                    .sim-input:focus {
                                        background-color: rgba(255, 255, 255, 0.15) !important;
                                        color: #ffffff !important;
                                        box-shadow: none !important;
                                        border-color: rgba(255, 255, 255, 0.3) !important;
                                    }

                                    .sim-input {
                                        transition: all 0.2s ease-in-out;
                                    }
                                </style>
                                <div class="col-lg-6">
                                    <div class="card border-0 rounded-4 shadow-sm h-100 overflow-hidden"
                                        style="background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);">
                                        <div class="card-body p-4">
                                            <h6
                                                class="fw-bold mb-3 text-uppercase text-white d-flex align-items-center gap-2">
                                                <i class="bi bi-calculator-fill text-info"></i> Simulator Harga
                                            </h6>

                                            <!-- Variation Selector -->
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold text-white-50">SIMULASI UNTUK
                                                    VARIASI</label>
                                                <select
                                                    class="form-select form-select-sm bg-white bg-opacity-10 border-0 text-white fw-semibold sim-input"
                                                    id="simVariasiSelect" style="backdrop-filter:blur(4px);">
                                                    <option value="all" class="text-dark">-- Semua Variasi (HPP
                                                        Termurah) --</option>
                                                </select>
                                            </div>

                                            <!-- HPP Reference -->
                                            <div class="rounded-3 mb-3 px-3 py-2"
                                                style="background: rgba(255,255,255,0.08);">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="small text-white-50">HPP Referensi</span>
                                                    <span class="fw-bold text-info" id="simHppRef">Rp 0</span>
                                                </div>
                                            </div>

                                            <!-- Target Margin Range -->
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold text-white-50">TARGET LABA
                                                    (%)</label>
                                                <div class="d-flex gap-2 align-items-center">
                                                    <div class="input-group input-group-sm">
                                                        <span
                                                            class="input-group-text bg-white bg-opacity-10 border-0 text-white-50"
                                                            style="font-size:0.7rem;">MIN</span>
                                                        <input type="number"
                                                            class="form-control bg-white bg-opacity-10 border-0 text-white fw-bold text-center sim-input"
                                                            id="simMarginMin" placeholder="10" min="0" max="99"
                                                            style="backdrop-filter:blur(4px);">
                                                        <span
                                                            class="input-group-text bg-white bg-opacity-10 border-0 text-white-50">%</span>
                                                    </div>
                                                    <span class="text-white-50">—</span>
                                                    <div class="input-group input-group-sm">
                                                        <span
                                                            class="input-group-text bg-white bg-opacity-10 border-0 text-white-50"
                                                            style="font-size:0.7rem;">MAX</span>
                                                        <input type="number"
                                                            class="form-control bg-white bg-opacity-10 border-0 text-white fw-bold text-center sim-input"
                                                            id="simMarginMax" placeholder="60" min="0" max="99"
                                                            style="backdrop-filter:blur(4px);">
                                                        <span
                                                            class="input-group-text bg-white bg-opacity-10 border-0 text-white-50">%</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Range Table Output -->
                                            <div id="simRangeTable" class="rounded-3 overflow-hidden"
                                                style="background: rgba(255,255,255,0.05);">
                                                <div class="text-white-50 text-center py-3 small">Isi target laba untuk
                                                    melihat rekomendasi harga</div>
                                            </div>

                                            <button type="button"
                                                class="btn btn-sm btn-info w-100 mt-3 text-dark fw-bold"
                                                id="btnApplySimulatedPrice">
                                                <i class="bi bi-magic me-1"></i>Terapkan ke Skala Harga
                                            </button>

                                            <hr class="border-white border-opacity-10 my-3">

                                            <!-- Manual price check -->
                                            <div class="mb-0">
                                                <label class="form-label small fw-semibold text-white-50">CEK HARGA JUAL
                                                    MANUAL</label>
                                                <div class="input-group input-group-sm">
                                                    <span
                                                        class="input-group-text bg-white bg-opacity-10 border-0 text-white-50">Rp</span>
                                                    <input type="text"
                                                        class="form-control bg-white bg-opacity-10 border-0 text-white fw-bold sim-input"
                                                        id="simHargaJual" placeholder="Masukkan harga jual">
                                                </div>
                                                <div class="mt-2 d-flex gap-3 align-items-center">
                                                    <span class="small text-white-50">Estimasi Margin:</span>
                                                    <b id="simSaranMargin" class="text-info">0%</b>
                                                    <span class="small text-white-50 ms-1">| Laba:</span>
                                                    <b id="simLabaAmount" class="text-success small">Rp 0</b>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="card border-0 border-top pt-4 mt-2">
                                        <?php $this->load->view('admin/product/add_harga/universal_pricing', ['hide_tracker' => true]); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

<?php $this->load->view('admin/product/components/detail/v_modal_edit_bom_detail'); ?>


<?php $this->load->view('admin/product/components/detail/v_modal_edit_routing_detail'); ?>


<?php $this->load->view('admin/product/components/detail/v_modal_copy_setup', [
    'variations' => isset($variations) ? $variations : [],
    'getProduct' => isset($getProduct) ? $getProduct : []
]); ?>
<?php $this->load->view('admin/product/components/v_modal_sop_produk'); ?>

<!-- Modal Edit Nama Produk -->
<div class="modal fade" id="modalEditNamaProduk" tabindex="-1" aria-labelledby="modalEditNamaProdukLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark" id="modalEditNamaProdukLabel">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit Nama Produk
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditNamaProduk" onsubmit="event.preventDefault(); simpanNamaProduk();">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="inputEditNamaProduk" class="form-label fw-bold small text-muted text-uppercase">Nama
                            Produk</label>
                        <input type="text" class="form-control form-control-lg fs-6" id="inputEditNamaProduk"
                            value="<?= htmlspecialchars($getProduct['product_nama'], ENT_QUOTES) ?>" required
                            placeholder="Ketik nama produk...">
                        <div class="form-text text-muted small mt-1">Perubahan nama produk akan langsung diperbarui ke
                            database.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-light border px-3 fw-medium"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSimpanNamaProduk" class="btn btn-primary px-4 fw-bold">
                        <i class="bi bi-check-lg me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function simpanNamaProduk() {
        var newName = $('#inputEditNamaProduk').val().trim();
        if (!newName) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Nama produk tidak boleh kosong!'
            });
            return;
        }

        var btn = $('#btnSimpanNamaProduk');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyimpan...');

        $.ajax({
            url: BASE_URL + 'manufaktur/rename_product_ajax',
            type: 'POST',
            dataType: 'json',
            data: {
                product_id: PRODUCT_ID,
                product_nama: newName
            },
            success: function (res) {
                btn.prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i> Simpan');
                if (res.status === 'success') {
                    $('#modalEditNamaProduk').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1200,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message || 'Gagal mengubah nama produk'
                    });
                }
            },
            error: function (xhr, status, error) {
                btn.prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i> Simpan');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan sistem'
                });
            }
        });
    }
</script>

<script>
    $(document).ready(function () {
        // Restore tab from localStorage if available
        var activeTab = localStorage.getItem('activeTab_detail_' + PRODUCT_ID);
        if (!activeTab) {
            activeTab = localStorage.getItem('last_active_product_tab');
        }
        if (activeTab) {
            var tabBtn = $('#' + activeTab);
            if (tabBtn.length > 0) {
                var tabObj = new bootstrap.Tab(tabBtn[0]);
                tabObj.show();
            }
        }

        // Save tab to localStorage when clicked
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            var tabId = $(e.target).attr('id');
            localStorage.setItem('activeTab_detail_' + PRODUCT_ID, tabId);
            localStorage.setItem('last_active_product_tab', tabId);
        });

        // Initialize Quick Jump Product Select2
        $('#quickJumpProductSelect').select2({
            placeholder: '🔍 Cari & loncat produk...',
            allowClear: true,
            width: '100%',
            ajax: {
                url: BASE_URL + 'manufaktur/search_product',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term || ''
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.results || []
                    };
                },
                cache: true
            },
            minimumInputLength: 0
        }).on('select2:select', function (e) {
            var pid = e.params && e.params.data ? e.params.data.id : null;
            if (pid) {
                window.location.href = BASE_URL + 'manufaktur/detail/' + pid;
            }
        });

        // Shortcut: press '/' or Ctrl+K to open product search (when not inside inputs)
        $(document).on('keydown', function (e) {
            if ((e.ctrlKey && e.key === 'k') || (e.key === '/' && !$(e.target).is('input, textarea, select'))) {
                e.preventDefault();
                $('#quickJumpProductSelect').select2('open');
            }
        });

        // Kalkulator Kapasitas
        $('#add_kapasitas_kalkulator').on('input', function () {
            var capacity = parseFloat($(this).val());
            if (capacity && capacity > 0) {
                $('#add_qty').val(Number((1 / capacity).toFixed(4)));
            } else {
                $('#add_qty').val('');
            }
        });

        $('#edit_kapasitas_kalkulator').on('input', function () {
            var capacity = parseFloat($(this).val());
            if (capacity && capacity > 0) {
                $('#edit_qty').val(Number((1 / capacity).toFixed(4)));
            } else {
                $('#edit_qty').val('');
            }
        });
    });
</script>