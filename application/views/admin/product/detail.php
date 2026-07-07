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
<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/product-detail.css') ?>">

<div class="app-content">
    <?php
    $this->load->view('layout_admin/header_modul', [
        'title' => 'Konfigurasi Produk',
        'subtitle' => 'Manufaktur',
        'back_url' => base_url('manufaktur?kategori=' . $getProduct['kategori_id']),
        'breadcrumb' => [
            ['name' => 'Manufaktur', 'url' => base_url('manufaktur')],
            ['name' => 'Konfigurasi Produk', 'url' => base_url('manufaktur')],
            ['name' => $getProduct['product_nama'], 'url' => base_url('manufaktur')],
        ]
    ])
        ?>
    <!-- <div class="page-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="header-breadcrumb">
                        <i class="bi bi-box-seam-fill"></i>
                        <span>Manufaktur</span>
                        <i class="bi bi-chevron-right small opacity-50"></i>
                        <span>Konfigurasi Produk</span>
                        <i class="bi bi-chevron-right small opacity-50"></i>
                        <span class="text-primary fw-bold"><?= $getProduct['product_nama'] ?></span>
                    </div>
                    <h1 class="header-title">
                        <?= $getProduct['product_nama'] ?>
                        <?php if (isset($getProduct['status'])): ?>
                            <span
                                class="status-pill <?= $getProduct['status'] == 1 ? 'status-active' : 'status-inactive' ?>">
                                <?= $getProduct['status'] == 1 ? 'ACTIVE' : 'INACTIVE' ?>
                            </span>
                        <?php endif; ?>
                    </h1>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <a href="<?= base_url('manufaktur') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                    <button class="btn btn-primary rounded-pill px-4 ms-2 shadow-sm"
                        onclick="copyToClipboard('<?= $getProduct['product_nama'] ?>')">
                        <i class="bi bi-clipboard-check me-2"></i>Salin Nama
                    </button>
                </div>
            </div>
        </div>
    </div> -->

    <div class="container-fluid px-4">
        <!-- Removed Key Metrics Dashboard -->

        <div class="row">
            <div class="col-12 mb-4">
                <div class="card border-0 shadow-sm rounded-4" style="background: linear-gradient(145deg, #ffffff, #f8fafc);">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="badge bg-primary-subtle text-primary mb-2 px-3 py-2 rounded-pill fw-medium">
                                    <?= $getProduct['kategori_nama'] ?> <?= !empty($getProduct['subkategori_nama']) ? ' - ' . $getProduct['subkategori_nama'] : '' ?>
                                </span>
                                <h3 class="fw-bold text-dark mb-1"><?= $getProduct['product_nama'] ?></h3>
                                <div class="text-secondary small">
                                    <i class="bi bi-box-seam me-1"></i> Satuan: <?= $getProduct['satuan_nama'] ?? '-' ?> 
                                    <span class="mx-2 text-muted">|</span>
                                    <i class="bi bi-upc-scan me-1"></i> ID Produk: <?= $getProduct['product_id'] ?>
                                </div>
                            </div>
                            <div class="text-end">
                                <?php if (isset($getProduct['status'])): ?>
                                    <span class="badge <?= $getProduct['status'] == 1 ? 'bg-success' : 'bg-danger' ?> rounded-pill px-3 py-2 shadow-sm">
                                        <?= $getProduct['status'] == 1 ? '<i class="bi bi-check-circle me-1"></i> ACTIVE' : '<i class="bi bi-x-circle me-1"></i> INACTIVE' ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12">
                <!-- Navigation Interface -->
                <ul class="nav nav-tabs" id="productTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="harga-tab" data-bs-toggle="tab" data-bs-target="#harga"
                            type="button">SKALA HARGA</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="bom-tab" data-bs-toggle="tab" data-bs-target="#bom"
                            type="button">MANAGEMEN BOM</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="routing-tab" data-bs-toggle="tab" data-bs-target="#routing"
                            type="button">ALUR KERJA</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="hpp-tab" data-bs-toggle="tab" data-bs-target="#hpp"
                            type="button">ANALISA PROFIT</button>
                    </li>
                </ul>

                <div class="card border-0 shadow-sm p-4 text-bg-light">
                    <div class="tab-content" id="productTabContent">
                        <!-- Pricing Strategy -->
                        <div class="tab-pane fade show active" id="harga" role="tabpanel">
                            <?php $this->load->view('admin/product/add_harga/universal_pricing', ['hide_tracker' => true]); ?>
                        </div>

                        <!-- Bill of Materials -->
                        <div class="tab-pane fade" id="bom" role="tabpanel">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h5 class="fw-bold m-0"><i class="bi bi-gear-fill me-2 text-primary"></i>Bill of
                                    Materials Configuration</h5>
                                <button class="btn btn-primary rounded-3 shadow-sm" data-bs-toggle="collapse"
                                    data-bs-target="#addBomSection">
                                    <i class="bi bi-plus-lg me-1"></i> Input Item Baru
                                </button>
                            </div>

                            <div class="collapse mb-4" id="addBomSection">
                                <div class="card card-body bg-light border-0 rounded-4">
                                    <form id="addBomForm">
                                        <input type="hidden" id="bomProductIdInput" name="product_id"
                                            value="<?= $getProduct['product_id'] ?>">
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-12">
                                                <label class="form-label fw-bold small text-primary">TERAPKAN KE
                                                    VARIASI</label>
                                                <select class="form-select shadow-none" name="harga_id">
                                                    <option value="">-- Semua Variasi (Global) --</option>
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
                                                            if (!empty($v->ket_1) && $v->ket_1 !== '0') $meta_desc[] = $v->ket_1;
                                                            if (!empty($v->jenisprint_id) && $v->jenisprint_id !== '0') $meta_desc[] = $v->jenisprint_id;
                                                            if (!empty($v->side_id) && $v->side_id !== '0') $meta_desc[] = $v->side_id;
                                                        }
                                                        if (!empty($meta_desc)) {
                                                            $var_name .= ' (' . implode(' | ', $meta_desc) . ')';
                                                        }
                                                        $v->display_name = $var_name;
                                                    }
                                                    unset($v);

                                                    foreach ($variations as $var):
                                                        ?>
                                                        <option value="<?= $var->harga_id ?>"><?= $var->display_name ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold small">PILIH MATERIAL</label>
                                                <select class="form-select select2-bahan shadow-none" id="bahan_id"
                                                    name="bahan_id" required>
                                                    <option value="">-- Cari Material --</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-bold small">NET QTY</label>
                                                <input type="number" class="form-control" name="qty" step="0.01"
                                                    required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-bold small">SCRAP (%)</label>
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
                                <table class="table table-hover" id="bomTable">
                                    <thead>
                                        <tr>
                                            <th width="50" class="text-center">#</th>
                                            <th>MATERIAL</th>
                                            <th>VARIASI</th>
                                            <th class="text-center">DIMENSI</th>
                                            <th class="text-center">NET QTY</th>
                                            <th class="text-center">SCRAP</th>
                                            <th class="text-center">GROSS REQ</th>
                                            <th class="text-center">SATUAN</th>
                                            <th class="text-center">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bomTableBody">
                                        <tr>
                                            <td colspan="9" class="text-center py-4">Loading BOM Data...</td>
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
                                <button class="btn btn-warning text-white rounded-3 shadow-sm" data-bs-toggle="collapse"
                                    data-bs-target="#addRoutingSection">
                                    <i class="bi bi-plus-lg me-1"></i> Tambah Langkah
                                </button>
                            </div>

                            <div class="collapse mb-4" id="addRoutingSection">
                                <div class="card card-body bg-light border-0 rounded-4">
                                    <form id="addRoutingForm">
                                        <input type="hidden" id="routingProductIdInput" name="product_id"
                                            value="<?= $getProduct['product_id'] ?>">
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-12">
                                                <label class="form-label fw-bold small text-primary">TERAPKAN KE
                                                    VARIASI</label>
                                                <select class="form-select shadow-none" name="harga_id">
                                                    <option value="">-- Semua Variasi (Global) --</option>
                                                    <?php foreach ($variations as $var): ?>
                                                        <option value="<?= $var->harga_id ?>">
                                                            <?= $var->display_name ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
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
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold small">EST. WAKTU (MENIT)</label>
                                                <input type="number" class="form-control" name="est_time" value="0">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold small">BIAYA TENAGA KERJA (RP)</label>
                                                <input type="text" class="form-control rupiah-input" name="labor_cost"
                                                    value="0">
                                            </div>
                                            <div class="col-md-6 d-flex align-items-end">
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
                                            <td colspan="8" class="text-center py-4">Loading Routing Data...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Financial Analysis -->
                        <div class="tab-pane fade" id="hpp" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-lg-4">
                                    <div class="card border-0 bg-light p-4 rounded-4 shadow-sm">
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
                                        <div class="p-4 bg-white rounded-3 border-start border-4 border-success">
                                            <div class="small fw-bold text-muted mb-1 text-uppercase">Estimasi Total HPP
                                                / Unit</div>
                                            <div class="h3 fw-bold text-success mb-0" id="hppGrandTotal">Rp 0</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="card border-0 border-start ps-lg-4 h-100">
                                        <h6 class="fw-bold mb-4 text-uppercase letter-spacing-1">Profitability Matrix
                                            Analysis</h6>
                                        <div id="profitMatrixContent">
                                            <div class="text-center py-5 text-muted">
                                                <div class="spinner-border spinner-border-sm text-primary me-2"
                                                    role="status"></div>
                                                Membangun analisa keterkaitan harga dan biaya...
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
</div>

</div>

<!-- Modal Edit BOM -->
<div class="modal fade" id="modalEditBOM" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 1.25rem;">
            <div class="modal-header border-0 bg-light p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="bi bi-pencil-square text-primary fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-slate-900">Edit Material BOM</h5>
                        <p class="text-muted small mb-0">Perbarui kuantitas dan faktor scrap</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBomForm">
                <div class="modal-body p-4">
                    <input type="hidden" name="bom_line_id" id="edit_bom_line_id">
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-primary">TERAPKAN KE VARIASI</label>
                        <select class="form-select shadow-none" name="harga_id" id="edit_harga_id_bom">
                            <option value="">-- Semua Variasi (Global) --</option>
                            <?php foreach ($variations as $var): ?>
                                <option value="<?= $var->harga_id ?>">
                                    <?= $var->display_name ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4 p-3 bg-slate-50 rounded-3 border">
                        <label class="form-label fw-bold small text-uppercase letter-spacing-1 text-muted">Material
                            Target</label>
                        <div id="edit_material_name_display" class="h6 fw-bold text-slate-800 mb-0"></div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">KUANTITAS (NET)</label>
                            <input type="number" name="qty" id="edit_qty" class="form-control" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">SCRAP FACTOR (%)</label>
                            <input type="number" name="scrap_factor" id="edit_scrap_factor" class="form-control"
                                step="0.1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Routing -->
<div class="modal fade" id="modalEditRouting" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 1.25rem;">
            <div class="modal-header border-0 bg-light p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="bi bi-diagram-3-fill text-warning fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-slate-900">Ubah Langkah Kerja</h5>
                        <p class="text-muted small mb-0">Sesuaikan urutan dan biaya operasional</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="editRoutingForm">
                <div class="modal-body p-4">
                    <input type="hidden" name="routing_id" id="edit_routing_id">
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-primary">TERAPKAN KE VARIASI</label>
                        <select class="form-select shadow-none" name="harga_id" id="edit_harga_id_routing">
                            <option value="">-- Semua Variasi (Global) --</option>
                            <?php foreach ($variations as $var): ?>
                                <option value="<?= $var->harga_id ?>">
                                    <?= $var->display_name ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted">SEQUENCE</label>
                            <input type="number" name="routing_urutan" id="edit_routing_urutan"
                                class="form-control bg-light fw-bold" required>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label fw-bold small text-muted">NAMA OPERASI / PROSES</label>
                            <input type="text" name="routing_name" id="edit_routing_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">WORK CENTER / DEPT</label>
                            <select name="operator_id" id="edit_operator_id" class="form-select" required></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">LINK MATERIAL BOM</label>
                            <select name="bom_line_id" id="edit_bom_line_id_routing" class="form-select"></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">EST. WAKTU (MENIT)</label>
                            <input type="number" name="est_time" id="edit_est_time" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">BIAYA KERJA (RP)</label>
                            <input type="text" name="labor_cost" id="edit_labor_cost" class="form-control rupiah-input">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 text-end">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-white rounded-pill px-4 fw-bold shadow-sm">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>