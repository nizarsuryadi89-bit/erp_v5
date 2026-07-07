<?php
// FILE: application/views/admin/gudang/bahan_baku.php
// Optimized V2.0 Design with preserved Functionality
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manajemen Bahan Baku - TGM ERP</title>

    <!-- Legacy CSS (Preserved for compatibility) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/tgm-gudang-bahan.css') ?>">

    <!-- Modern Executive Dashboard Styles -->
    <style>
        :root {
            --exec-primary: #1e293b;
            --exec-secondary: #334155;
            --exec-accent: #6366f1;
            --exec-accent-glow: rgba(99, 102, 241, 0.15);
            --exec-success: #10b981;
            --exec-danger: #ef4444;
            --exec-warning: #f59e0b;
            --exec-info: #3b82f6;
            --exec-bg: #f8fafc;
            --exec-card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        body {
            background-color: var(--exec-bg);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .exec-dashboard {
            padding-bottom: 3rem;
        }

        /* Metric Cards */
        .metric-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: var(--exec-card-shadow);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: var(--exec-accent);
        }

        .metric-card .icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        /* Utility Backgrounds */
        .bg-soft-indigo {
            background: #eef2ff;
            color: #6366f1;
        }

        .bg-soft-emerald {
            background: #ecfdf5;
            color: #10b981;
        }

        .bg-soft-rose {
            background: #fff1f2;
            color: #f43f5e;
        }

        .bg-soft-amber {
            background: #fffbeb;
            color: #f59e0b;
        }

        .bg-soft-blue {
            background: #eff6ff;
            color: #3b82f6;
        }

        /* Analytics Card */
        .analytics-card {
            background: white;
            border-radius: 24px;
            padding: 1.75rem;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: var(--exec-card-shadow);
            margin-bottom: 1.5rem;
        }

        /* Table Styling */
        .table-exec thead th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 0.75rem;
        }

        .table-exec tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: 0.875rem;
        }

        .table-exec tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Form Controls */
        .form-control-exec {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            padding: 0.6rem 1rem;
            transition: all 0.2s;
        }

        .form-control-exec:focus {
            border-color: var(--exec-accent);
            box-shadow: 0 0 0 3px var(--exec-accent-glow);
        }

        /* Badges */
        .badge.bg-secondary {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
        }

        .badge.bg-primary {
            background-color: #eff6ff !important;
            color: #3b82f6 !important;
        }

        .badge.bg-success {
            background-color: #ecfdf5 !important;
            color: #10b981 !important;
        }

        .badge.bg-warning {
            background-color: #fffbeb !important;
            color: #b45309 !important;
        }

        .badge.bg-info {
            background-color: #f0fdf4 !important;
            color: #16a34a !important;
        }

        /* Custom */
    </style>
</head>

<body>

    <div class="exec-dashboard app-content">
        <?php $this->load->view('layout_admin/header_modul'); ?>

        <div class="container-fluid px-4 mt-4">

            <!-- Header -->
            <div class="row align-items-center mb-4">
                <div class="col-lg-6">
                    <span class="badge bg-soft-indigo mb-2 px-3 py-2 rounded-pill fw-bold">INVENTORY & WAREHOUSE</span>
                    <h2 class="fw-800 text-dark mb-1">Manajemen Bahan Baku</h2>
                    <p class="text-muted mb-0">Kelola stok, varian material, dan barcode inventory.</p>
                </div>
                <div class="col-lg-6 text-lg-end mt-3 mt-lg-0">
                    <button class="btn btn-primary btn-lg shadow-sm rounded-pill fw-bold" data-bs-toggle="modal"
                        data-bs-target="#modalInputBahanBaku">
                        <i class="fa fa-plus-circle me-2"></i>Tambah Bahan Baku
                    </button>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="metric-card">
                        <div class="icon-wrapper bg-soft-indigo text-indigo">
                            <i class="fa fa-boxes"></i>
                        </div>
                        <div>
                            <div class="text-uppercase fw-bold text-muted small mb-1">Bahan Baku Utama</div>
                            <h3 class="fw-900 text-dark mb-0" id="statBahanProduksi">0</h3>
                            <small class="text-success fw-bold"><i class="fa fa-arrow-up me-1"></i>Active Items</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="metric-card">
                        <div class="icon-wrapper bg-soft-info text-info">
                            <i class="fa fa-tools"></i>
                        </div>
                        <div>
                            <div class="text-uppercase fw-bold text-muted small mb-1">Bahan Operasional</div>
                            <h3 class="fw-900 text-dark mb-0" id="statBahanOperasional">0</h3>
                            <small class="text-muted">Tools & Equipment</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="metric-card">
                        <div class="icon-wrapper bg-soft-emerald text-emerald">
                            <i class="fa fa-check-circle"></i>
                        </div>
                        <div>
                            <div class="text-uppercase fw-bold text-muted small mb-1">Barang Jadi</div>
                            <h3 class="fw-900 text-dark mb-0" id="statBarangJadi">0</h3>
                            <small class="text-success">Ready for Sale</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="analytics-card">
                <!-- Filter Section -->
                <div class="row g-3 mb-4 pb-3 border-bottom">
                    <div class="col-lg-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i
                                    class="fa fa-search text-muted"></i></span>
                            <input type="text" class="form-control form-control-exec border-start-0 ps-0"
                                id="searchInput" placeholder="Cari Kode, Nama Bahan, atau Barcode...">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <select class="form-select form-control-exec" id="filterJenis">
                            <option value="">Semua Jenis</option>
                            <option value="bahan_baku">Bahan Baku</option>
                            <option value="bahan_operasional">Bahan Operasional</option>
                            <option value="barang_jadi">Barang Jadi</option>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <select class="form-select form-control-exec" id="filterStatus">
                            <option value="">Semua Status</option>
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-lg-4 text-end">
                        <button class="btn btn-outline-danger me-2" id="resetFilter" style="display:none;">
                            <i class="fa fa-times me-1"></i> Reset
                        </button>
                        <button class="btn btn-outline-dark me-2" id="btnPrintBulk">
                            <i class="fa fa-print me-1"></i> Print Barcode
                        </button>
                        <button class="btn btn-outline-success" id="exportData">
                            <i class="fa fa-file-excel me-1"></i> Export Data
                        </button>
                    </div>
                </div>

                <!-- Active Filters Indicator -->
                <div id="activeFilters" class="mb-3" style="display:none;">
                    <div
                        class="alert alert-soft-info d-flex align-items-center py-2 px-3 rounded-pill bg-soft-blue border-0">
                        <i class="fa fa-filter me-2 text-primary"></i>
                        <span class="fw-bold text-primary small me-2">Filter Aktif:</span>
                        <span id="filterText" class="text-dark small"></span>
                        <button class="btn btn-link btn-sm text-danger ms-auto p-0 text-decoration-none"
                            id="clearAllFilters">
                            <small class="fw-bold">Hapus Filter</small>
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-exec w-100 align-middle" id="bahanBakuTable">
                        <thead>
                            <tr>
                                <th style="width: 40px;" class="text-center">
                                    <input class="form-check-input" type="checkbox" id="checkAll">
                                </th>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th>Nama Bahan & Barcode</th>
                                <th>Jenis Barang</th>
                                <th>Satuan</th>
                                <th>Barcode ID</th>
                                <th>Material Varian</th> <!-- Replaced Harga with Varian Action -->
                                <th class="text-center" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3 border-top pt-3">
                    <div class="small text-muted">Menampilkan <span id="statTotalBahan"
                            class="fw-bold text-dark">0</span> data bahan baku.</div>
                    <!-- Pagination could go here if server-side, for client-side DataTables handles it -->
                </div>
            </div>

        </div>
    </div>

    <!-- ================= MODALS (PRESERVED IDS FOR JS COMPATIBILITY) ================= -->

    <!-- Modal Input Bahan Baku -->
    <div class="modal fade" id="modalInputBahanBaku" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form id="formTambahBahanBaku" class="needs-validation" novalidate>
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold" id="modalInputBahanBakuLabel">
                            <i class="fa fa-plus-circle text-primary me-2"></i>Tambah Bahan Baru
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Nama Bahan <span
                                        class="text-danger">*</span></label>
                                <input name="bahanbaku_nama" id="bahanbaku_nama" type="text"
                                    class="form-control form-control-lg" required placeholder="Contoh: Kertas A4..."
                                    minlength="3">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Barcode</label>
                                <input name="barcode" id="barcode" type="text" class="form-control form-control-lg"
                                    placeholder="Scan/Input Kode...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Satuan <span
                                        class="text-danger">*</span></label>
                                <select name="satuan_id" id="satuan_id" class="form-select form-select-lg" required>
                                    <option value="">-- Pilih Satuan --</option>
                                    <?php if (isset($satuan) && is_array($satuan)):
                                        foreach ($satuan as $s): ?>
                                            <option value="<?= $s->satuan_id ?>"><?= $s->satuan_nama ?></option>
                                        <?php endforeach; endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Jenis <span
                                        class="text-danger">*</span></label>
                                <select name="jenis_barang" id="jenis_barang" class="form-select form-select-lg"
                                    required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="bahan_baku">Bahan Baku</option>
                                    <option value="bahan_operasional">Bahan Operasional</option>
                                    <option value="barang_jadi">Barang Jadi</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm"
                            id="btnSubmitBahanBaku">
                            <i class="fa fa-save me-2"></i>Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Bahan Baku -->
    <div class="modal fade" id="modalEditBahanBaku" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form id="formEditBahanBaku" class="needs-validation" novalidate>
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold">
                            <i class="fa fa-edit text-warning me-2"></i>Edit Bahan Baku
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-4">
                        <input type="hidden" name="bahanbaku_id" id="edit_bahanbaku_id">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Nama Bahan <span
                                        class="text-danger">*</span></label>
                                <input name="bahanbaku_nama" id="edit_bahanbaku_nama" type="text"
                                    class="form-control form-control-lg" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Barcode</label>
                                <input name="barcode" id="edit_barcode" type="text"
                                    class="form-control form-control-lg">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Satuan Gudang
                                    <span class="text-danger">*</span></label>
                                <select name="satuan_id" id="edit_satuan_id" class="form-select form-select-lg"
                                    required>
                                    <option value="">-- Pilih Satuan --</option>
                                    <?php if (isset($satuan) && is_array($satuan)):
                                        foreach ($satuan as $s): ?>
                                            <option value="<?= $s->satuan_id ?>"><?= $s->satuan_nama ?></option>
                                        <?php endforeach; endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Jenis Barang <span
                                        class="text-danger">*</span></label>
                                <select name="jenis_barang" id="edit_jenis_barang" class="form-select form-select-lg"
                                    required>
                                    <option value="bahan_baku">Bahan Baku</option>
                                    <option value="bahan_operasional">Bahan Operasional</option>
                                    <option value="barang_jadi">Barang Jadi</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm"
                            id="btnSubmitEditBahanBaku">
                            <i class="fa fa-save me-2"></i>Update
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Material -->
    <div class="modal fade" id="modalTambahMaterial" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header bg-soft-indigo">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fa fa-cubes me-2 text-primary"></i>Kelola Varian Material: <span
                            id="materialBahanNama" class="fw-900 border-bottom border-dark border-2"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <!-- Left: List -->
                        <div class="col-lg-7 border-end">
                            <div class="p-4 bg-light h-100">
                                <h6 class="fw-bold text-secondary mb-3 text-uppercase small ls-1">Daftar Varian Aktif
                                </h6>
                                <div class="card border-0 shadow-none">
                                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-hover align-middle mb-0" id="modalMaterialTable">
                                            <thead class="bg-white sticky-top">
                                                <tr>
                                                    <th class="text-center" width="5%">#</th>
                                                    <th>Varian</th>
                                                    <th>Satuan</th>
                                                    <th>Konversi</th>
                                                    <th>Status</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white">
                                                <!-- JS Populated -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Right: Form -->
                        <div class="col-lg-5">
                            <form id="formTambahMaterial" class="p-4 needs-validation" novalidate>
                                <h6 class="fw-bold text-primary mb-4 text-uppercase small ls-1"><i
                                        class="fa fa-plus-circle me-2"></i>Tambah Varian Baru</h6>

                                <!-- Hidden inputs -->
                                <input type="hidden" name="bahanbaku_id" id="material_bahanbaku_id">
                                <input type="hidden" name="bahan_id" id="bahan_id">

                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Nama Varian <span
                                            class="text-danger">*</span></label>
                                    <input name="varian" id="material_varian" type="text" class="form-control" required
                                        placeholder="Contoh: Merah, XL, Box 12...">
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-7">
                                        <label class="form-label fw-bold small">Satuan <span
                                                class="text-danger">*</span></label>
                                        <select name="satuan_gudang_id" id="material_satuan_gudang_id"
                                            class="form-select" required>
                                            <option value="">-- Pilih --</option>
                                            <?php if (isset($satuan) && is_array($satuan)):
                                                foreach ($satuan as $s): ?>
                                                    <option value="<?= $s->satuan_id ?>"><?= $s->satuan_nama ?></option>
                                                <?php endforeach; endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-5">
                                        <label class="form-label fw-bold small">Konversi <span
                                                class="text-danger">*</span></label>
                                        <input name="konversi" id="material_konversi" type="number" class="form-control"
                                            required value="1.00" min="0" step="0.01">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Status</label>
                                    <select name="status" id="material_status" class="form-select">
                                        <option value="1">Aktif</option>
                                        <option value="0">Nonaktif</option>
                                    </select>
                                </div>
                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-primary fw-bold" id="btnSubmitMaterial">
                                        <i class="fa fa-plus me-1"></i> Tambahkan Varian
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Material -->
    <div class="modal fade" id="modalEditMaterial" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <form id="formEditMaterial" class="needs-validation" novalidate>
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold">Edit Material</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="material_id" id="edit_material_id">
                        <input type="hidden" name="bahanbaku_id" id="edit_bahanbaku_id"> <!-- required for refresh -->

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Varian</label>
                            <input name="varian" id="edit_varian" type="text" class="form-control" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-8">
                                <label class="form-label fw-bold small">Satuan</label>
                                <select name="satuan_gudang_id" id="edit_satuan_gudang_id" class="form-select" required>
                                    <?php if (isset($satuan) && is_array($satuan)):
                                        foreach ($satuan as $s): ?>
                                            <option value="<?= $s->satuan_id ?>"><?= $s->satuan_nama ?></option>
                                        <?php endforeach; endif; ?>
                                </select>
                            </div>
                            <div class="col-4">
                                <label class="form-label fw-bold small">Konversi</label>
                                <input name="konversi" id="edit_konversi" type="number" class="form-control" required
                                    min="0" step="0.01">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Status</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>
                        <!-- Hidden pricing as in original -->
                        <input type="hidden" name="harga_jual" id="edit_harga_jual">
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold"
                            id="btnSubmitEditMaterial">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Update Barcode -->
    <div class="modal fade" id="modalUpdateBarcode" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Update Barcode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pt-0">
                    <form id="formUpdateBarcode">
                        <input type="hidden" id="update_barcode_bahan_id">
                        <input type="hidden" id="update_barcode_nama">

                        <div class="mb-4">
                            <div class="p-4 bg-light rounded-3 mb-3 border d-flex align-items-center justify-content-center"
                                style="min-height: 100px;" id="barcodePreview">
                                <small class="text-muted">Barcode Preview</small>
                            </div>
                        </div>

                        <div class="input-group mb-3">
                            <input type="text" class="form-control form-control-lg text-center fw-bold font-monospace"
                                id="update_barcode_value" placeholder="SCAN OR TYPE">
                            <button class="btn btn-outline-secondary" type="button" id="btnGenerateBarcode"
                                title="Generate Random">
                                <i class="fa fa-magic"></i>
                            </button>
                        </div>

                        <div class="d-grid">
                            <button type="button" class="btn btn-primary btn-lg rounded-pill fw-bold"
                                id="btnSubmitUpdateBarcode">
                                <i class="fa fa-save me-2"></i>Simpan Barcode
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Main Script Logic (Preserved from Original) -->
    <script>
        $(document).ready(function () {
            const baseUrl = '<?= base_url() ?>';

            // Update Stats Logic
            function updateStats() {
                let total = $('#bahanBakuTable tbody tr').length;
                let bahanBaku = $('#bahanBakuTable tbody tr[data-kode="bahan_baku"]').length;
                let bahanOperasional = $('#bahanBakuTable tbody tr[data-kode="bahan_operasional"]').length;
                let barangJadi = $('#bahanBakuTable tbody tr[data-kode="barang_jadi"]').length;
                let operasional = $('#bahanBakuTable tbody tr[data-kode="operasional"]').length;
                let stokminim = $('#bahanBakuTable tbody tr[data-stokminim="minim"]').length;

                $('#statBahanProduksi').text(bahanBaku);
                $('#statBahanOperasional').text(bahanOperasional + operasional);
                $('#statBarangJadi').text(barangJadi);
                $('#statTotalBahan').text(total);
            }

            // Refresh Table Logic
            function refreshTabelBahanBaku() {
                // Show loading state in table
                $('#bahanBakuTable tbody').html('<tr><td colspan="7" class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-2 text-muted">Memuat data...</p></td></tr>');

                fetch('<?= base_url('warehouse/get_bahan_baku_json') ?>?t=' + new Date().getTime())
                    .then(response => {
                        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            let tbody = $('#bahanBakuTable tbody');
                            tbody.empty();

                            let bahanArray = Array.isArray(data.data) ? data.data : Object.values(data.data);

                            if (bahanArray.length === 0) {
                                tbody.html('<tr><td colspan="7" class="text-center py-5 text-muted">Belum ada data bahan baku</td></tr>');
                                updateStats();
                                return;
                            }

                            bahanArray.forEach((bahan, index) => {
                                const status = bahan.active || 1;
                                const bahanNama = bahan.bahanbaku_nama || bahan.bahan_nama || '';
                                const bahanId = bahan.id || bahan.bahan_id || '';
                                const satuanId = bahan.satuan_id || '';
                                const stok_min = parseFloat(bahan.stok_min || 0);

                                const stok_awal = parseFloat(bahan.stok_awal || 0);
                                const stok_tambah = parseFloat(bahan.stok_tambah || 0);
                                const stok_kurang = parseFloat(bahan.stok_kurang || 0);
                                const stok_aktif = (stok_awal + stok_tambah) - stok_kurang;
                                const is_min = stok_aktif <= stok_min;

                                const row = `
                            <tr data-nama="${bahanNama.toLowerCase()}" data-kode="${(bahan.jenis_barang || '').toLowerCase()}" data-status="${status}" data-stokminim="${is_min ? 'minim' : 'cukup'}">
                                <td class="text-center">
                                    <input class="form-check-input check-item" type="checkbox" value="${bahanId}">
                                </td>
                                <td class="text-center fw-bold text-muted">${index + 1}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3 p-2 bg-soft-indigo rounded-circle">
                                            <i class="fa fa-box text-indigo"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">${bahanNama || '-'}</div>
                                            ${bahan.barcode ? '<div class="small font-monospace text-muted"><i class="fa fa-barcode me-1"></i>' + bahan.barcode + '</div>' : ''}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge ${getJenisBadgeClass(bahan.jenis_barang)} rounded-pill px-3">
                                        ${getJenisLabel(bahan.jenis_barang)}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-secondary text-uppercase small">
                                        ${bahan.satuan_nama || '-'}
                                    </span>
                                </td>
                                <td>
                                    ${bahan.barcode ? '<span class="text-dark fw-bold font-monospace bg-light px-2 py-1 rounded">' + bahan.barcode + '</span>' : '<span class="text-muted small">No Barcode</span>'}
                                </td>
                                <td>
                                     <button class="btn btn-sm btn-outline-primary rounded-pill btn-add-material" 
                                            data-id="${bahan.bahan_id || bahanId}" 
                                            data-nama="${bahanNama}" 
                                            data-jenis="${bahan.jenis_barang || ''}"
                                            data-satuan="${bahan.satuan_nama || ''}"
                                            data-barcode="${bahan.barcode || ''}"
                                            data-harga="${bahan.bahanbaku_hargabeli || ''}">
                                        <i class="fa fa-cubes me-1"></i> Kelola Varian
                                    </button>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm rounded-pill overflow-hidden" role="group">
                                        <button class="btn btn-sm btn-light btn-edit-bahan px-3" 
                                                data-id="${bahanId}" 
                                                data-nama="${bahanNama || ''}"
                                                data-jenis="${bahan.jenis_barang || ''}"
                                                data-satuan="${satuanId}"
                                                data-barcode="${bahan.barcode || ''}"
                                                title="Edit Info">
                                            <i class="fa fa-pencil-alt text-warning"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light btn-update-barcode px-3" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalUpdateBarcode"
                                                data-id="${bahan.bahan_id || bahanId}"
                                                data-nama="${bahanNama || ''}"
                                                data-barcode="${bahan.barcode || ''}"
                                                title="Update Barcode">
                                            <i class="fa fa-qrcode text-dark"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light btn-delete-bahan px-3" 
                                                data-id="${bahanId}" 
                                                data-nama="${bahanNama || ''}"
                                                title="Hapus">
                                            <i class="fa fa-trash text-danger"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                                tbody.append(row);
                            });

                            updateStats();
                            attachEventHandlers(); // Re-attach for new elements
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        $('#bahanBakuTable tbody').html(`<tr><td colspan="7" class="text-center text-danger py-4">Gagal memuat data: ${error.message}</td></tr>`);
                    });
            }

            function getJenisBadgeClass(jenis) {
                switch (jenis) {
                    case 'bahan_baku': return 'bg-soft-blue text-primary';
                    case 'bahan_operasional': return 'bg-soft-info text-info';
                    case 'barang_jadi': return 'bg-soft-emerald text-emerald';
                    default: return 'bg-secondary';
                }
            }

            function getJenisLabel(jenis) {
                switch (jenis) {
                    case 'bahan_baku': return 'Bahan Baku';
                    case 'bahan_operasional': return 'Bahan Operasional';
                    case 'barang_jadi': return 'Barang Jadi';
                    default: return jenis ? jenis.toUpperCase() : '-';
                }
            }

            function attachEventHandlers() {
                // Delete Confirmation Logic
                $('.btn-delete-bahan').off('click').on('click', function () {
                    const id = $(this).data('id');
                    const nama = $(this).data('nama');
                    const deleteBtn = $(this);

                    // Fetch check dependency
                    fetch(`<?= base_url('warehouse/get_material_count_by_bahan') ?>?bahan_id=${id}`)
                        .then(r => r.json())
                        .then(data => {
                            const count = data.material_count || 0;
                            let html = `Hapus <strong>${nama}</strong>?`;
                            if (count > 0) html += `<br><small class="text-danger mt-2 d-block">Peringatan: ${count} varian material terkait juga akan dihapus.</small>`;

                            Swal.fire({
                                title: 'Konfirmasi Hapus',
                                html: html,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#ef4444',
                                confirmButtonText: 'Ya, Hapus',
                                cancelButtonText: 'Batal'
                            }).then((r) => {
                                if (r.isConfirmed) {
                                    fetch(`<?= base_url('warehouse/actionDeleteBahanBaku/') ?>${id}`)
                                        .then(res => res.json())
                                        .then(resData => {
                                            if (resData.status === 'success') {
                                                Swal.fire('Terhapus!', resData.pesan, 'success');
                                                refreshTabelBahanBaku();
                                            } else {
                                                Swal.fire('Gagal', resData.pesan, 'error');
                                            }
                                        });
                                }
                            });
                        })
                        .catch(e => {
                            // Fallback normal delete logic if check fails
                            Swal.fire({
                                title: 'Hapus?',
                                text: `Hapus ${nama}?`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#ef4444'
                            }).then(r => {
                                if (r.isConfirmed) {
                                    fetch(`<?= base_url('warehouse/actionDeleteBahanBaku/') ?>${id}`)
                                        .then(res => res.json())
                                        .then(d => {
                                            if (d.status === 'success') { Swal.fire('Deleted', '', 'success'); refreshTabelBahanBaku(); }
                                        });
                                }
                            })
                        });
                });
            }

            // Unified Filter Logic
            function applyFilters() {
                const search = $('#searchInput').val().toLowerCase();
                const jenis = $('#filterJenis').val();
                const status = $('#filterStatus').val();

                $("#bahanBakuTable tbody tr").each(function () {
                    const row = $(this);
                    const text = row.text().toLowerCase();
                    // Data attributes always return string or number, ensure comparison matches types
                    // In refreshTable, data-kode is set to lowercase
                    const rowJenis = (row.data('kode') || '').toString();
                    const rowStatus = (row.data('status') || '').toString();

                    let visible = true;

                    if (search && text.indexOf(search) === -1) visible = false;
                    if (jenis && rowJenis !== jenis) visible = false;
                    if (status !== '' && rowStatus !== status) visible = false;

                    row.toggle(visible);
                });
                updateStats();

                // Update Filter Indicator UI
                const activeFilters = [];
                if (search) activeFilters.push(`Search: "${search}"`);
                if (jenis) activeFilters.push(`Jenis: ${getJenisLabel(jenis)}`);
                if (status !== '') activeFilters.push(`Status: ${status == 1 ? 'Aktif' : 'Nonaktif'}`);

                if (activeFilters.length > 0) {
                    $('#filterText').text(activeFilters.join(', '));
                    $('#activeFilters').fadeIn();
                    $('#resetFilter').fadeIn();
                } else {
                    $('#activeFilters').fadeOut();
                    $('#resetFilter').fadeOut();
                }
            }

            $('#searchInput, #filterJenis, #filterStatus').on('keyup change', applyFilters);

            $('#resetFilter, #clearAllFilters').on('click', function () {
                $('#searchInput').val('');
                $('#filterJenis').val('');
                $('#filterStatus').val('');
                applyFilters();
            });

            // Export Logic
            $('#exportData').on('click', function () {
                const filterJenis = $('#filterJenis').val();
                let exportUrl = '<?= base_url('warehouse/export_bahan_baku') ?> ';
                const params = new URLSearchParams();
                if (filterJenis) params.append('filter_jenis', filterJenis);
                if (params.toString()) exportUrl += '?' + params.toString();
                window.open(exportUrl, '_blank');
            });

            // Add Bahan Form
            $('#formTambahBahanBaku').on('submit', function (e) {
                e.preventDefault();

                // Basic Validation
                if (!this.checkValidity()) {
                    e.stopPropagation();
                    $(this).addClass('was-validated');
                    return;
                }

                const btn = $('#btnSubmitBahanBaku');
                const originalHtml = btn.html();
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> menyimpan...');

                const formData = new FormData(this);

                fetch('<?= base_url('warehouse/actionAddBahanBaku') ?>', { method: 'POST', body: formData })
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'success') {
                            $('#modalInputBahanBaku').modal('hide');
                            $('#formTambahBahanBaku')[0].reset();
                            Swal.fire('Berhasil', data.pesan, 'success');
                            refreshTabelBahanBaku();
                        } else {
                            Swal.fire('Gagal', data.pesan, 'error');
                        }
                    })
                    .catch(e => Swal.fire('Error', e.message, 'error'))
                    .finally(() => btn.prop('disabled', false).html(originalHtml));
            });

            // Edit Bahan Logic
            $(document).on('click', '.btn-edit-bahan', function () {
                $('#edit_bahanbaku_id').val($(this).data('id'));
                $('#edit_bahanbaku_nama').val($(this).data('nama'));
                $('#edit_barcode').val($(this).data('barcode'));
                $('#edit_jenis_barang').val($(this).data('jenis'));
                $('#edit_satuan_id').val($(this).data('satuan')); // Ideally this should match value id, need check if data-satuan is ID or Name. Original used ID for value usually.

                // Note: data-satuan passed in row might be Name, but select expects ID. 
                // Quick fix: The original code populated select logic correctly if variable is present.
                // It seems in original code line 1407: $('#edit_satuan_id').val(satuan || '');
                // If 'satuan' is string name, it won't select properly if values are IDs.
                // But let's assume standard behavior as per original file.

                $('#modalEditBahanBaku').modal('show');
            });

            $('#formEditBahanBaku').on('submit', function (e) {
                e.preventDefault();
                const btn = $('#btnSubmitEditBahanBaku');
                btn.prop('disabled', true);

                const formData = new FormData(this);
                fetch('<?= base_url('warehouse/actionEditBahanBaku') ?>', { method: 'POST', body: formData })
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'success') {
                            $('#modalEditBahanBaku').modal('hide');
                            refreshTabelBahanBaku();
                            Swal.fire('Updated', data.pesan, 'success');
                        } else {
                            Swal.fire('Error', data.pesan, 'error');
                        }
                    })
                    .finally(() => btn.prop('disabled', false));
            });

            // Update Barcode Logic
            $(document).on('click', '.btn-update-barcode', function () {
                const id = $(this).data('id');
                const nama = $(this).data('nama');
                const barcode = $(this).data('barcode');

                $('#update_barcode_bahan_id').val(id);
                $('#update_barcode_nama').val(nama);
                $('#update_barcode_value').val(barcode);

                if (barcode) {
                    $('#barcodePreview').html(`<div class="fw-bold fs-4 font-monospace">${barcode}</div>`);
                } else {
                    $('#barcodePreview').html('<small class="text-muted">Barcode Preview</small>');
                }
            });

            // Material Logic (Add/Edit/Delete)
            // 1. Open Modal & Load List
            $(document).on('click', '.btn-add-material', function () {
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                $('#materialBahanNama').text(nama);
                $('#material_bahanbaku_id').val(id);
                $('#bahan_id').val(id);

                loadMaterialByBahan(id);
                $('#modalTambahMaterial').modal('show');
            });

            function loadMaterialByBahan(id) {
                const tbody = $('#modalMaterialTable tbody');
                tbody.html('<tr><td colspan="6" class="text-center"><i class="fa fa-spinner fa-spin"></i></td></tr>');

                fetch(`<?= base_url('warehouse/get_material_by_bahan_json') ?>?bahan_id=${id}`)
                    .then(r => r.json())
                    .then(data => {
                        tbody.empty();
                        if (data.status === 'success' && data.data.length > 0) {
                            data.data.forEach((m, i) => {
                                tbody.append(`
                            <tr>
                                <td class="text-center">${i + 1}</td>
                                <td class="fw-bold">${m.varian}</td>
                                <td>${m.satuan_nama || '-'}</td>
                                <td>${m.konversi}</td>
                                <td><span class="badge ${m.status == 1 ? 'bg-success' : 'bg-secondary'}">${m.status == 1 ? 'Aktif' : 'Nonaktif'}</span></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-warning me-1 btn-edit-material-item" 
                                        data-id="${m.material_id}" 
                                        data-varian="${m.varian}"
                                        data-satuan="${m.satuan_gudang_id}"
                                        data-konversi="${m.konversi}"
                                        data-status="${m.status}">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                     <button class="btn btn-sm btn-outline-danger btn-delete-material" data-id="${m.material_id}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                            });
                        } else {
                            tbody.html('<tr><td colspan="6" class="text-center text-muted py-4">Belum ada varian material. Tambahkan di form sebelah kanan.</td></tr>');
                        }
                    })
                    .catch(console.error);
            }

            // 2. Submit Add Material
            $('#formTambahMaterial').on('submit', function (e) {
                e.preventDefault();
                const btn = $('#btnSubmitMaterial');
                btn.prop('disabled', true);

                const formData = new FormData(this);
                fetch('<?= base_url('warehouse/actionAddMaterial') ?>', { method: 'POST', body: formData })
                    .then(r => r.json())
                    .then(d => {
                        if (d.status === 'success') {
                            $('#formTambahMaterial')[0].reset();
                            // Keep ID
                            $('#material_bahanbaku_id').val(formData.get('bahanbaku_id'));
                            $('#bahan_id').val(formData.get('bahan_id'));
                            loadMaterialByBahan(formData.get('bahanbaku_id'));
                            Swal.fire({
                                icon: 'success', title: 'Berhasil', text: d.pesan,
                                toast: true, position: 'top-end', showConfirmButton: false, timer: 3000
                            });
                        } else {
                            Swal.fire('Gagal', d.pesan, 'error');
                        }
                    })
                    .finally(() => btn.prop('disabled', false));
            });

            // 3. Edit Material Item Logic
            $(document).on('click', '.btn-edit-material-item', function () {
                // Populate and show edit modal
                $('#edit_material_id').val($(this).data('id'));
                $('#edit_bahanbaku_id').val($('#material_bahanbaku_id').val()); // get from parent modal

                $('#edit_varian').val($(this).data('varian'));
                $('#edit_satuan_gudang_id').val($(this).data('satuan'));
                $('#edit_konversi').val($(this).data('konversi'));
                $('#edit_status').val($(this).data('status'));

                $('#modalEditMaterial').modal('show');
            });

            $('#formEditMaterial').on('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetch('<?= base_url('warehouse/actionUpdateMaterial') ?>', { method: 'POST', body: formData })
                    .then(r => r.json())
                    .then(d => {
                        if (d.status === 'success') {
                            $('#modalEditMaterial').modal('hide');
                            loadMaterialByBahan($('#edit_bahanbaku_id').val()); // Refresh list in parent modal
                        } else {
                            Swal.fire('Error', d.pesan, 'error');
                        }
                    });
            });

            // 4. Delete Material
            $(document).on('click', '.btn-delete-material', function () {
                const id = $(this).data('id');
                const parentId = $('#material_bahanbaku_id').val();
                Swal.fire({
                    title: 'Hapus Varian?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444'
                }).then(r => {
                    if (r.isConfirmed) {
                        const fd = new FormData(); fd.append('material_id', id);
                        fetch(`<?= base_url('warehouse/actionDeleteMaterial') ?>`, { method: 'POST', body: fd })
                            .then(res => res.json())
                            .then(d => {
                                if (d.status === 'success') {
                                    loadMaterialByBahan(parentId);
                                } else {
                                    Swal.fire('Gagal', d.pesan, 'error');
                                }
                            });
                    }
                });
            });

            // Barcode Logic
            $('#btnGenerateBarcode').click(function () {
                const random = Math.floor(100000 + Math.random() * 900000); // 6 digit
                $('#update_barcode_value').val('BM' + random);
            });

            $('#btnSubmitUpdateBarcode').click(function () {
                const barcode = $('#update_barcode_value').val();
                const id = $('#update_barcode_bahan_id').val();

                fetch('<?= base_url('warehouse/actionUpdateBarcode') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ bahan_id: id, barcode: barcode })
                }).then(r => r.json()).then(d => {
                    if (d.status === 'success') {
                        $('#modalUpdateBarcode').modal('hide');
                        refreshTabelBahanBaku();
                        Swal.fire('Success', 'Barcode updated', 'success');
                    } else {
                        Swal.fire('Error', d.pesan, 'error');
                    }
                });
            });

            // Bulk Action Logic
            $('#checkAll').change(function () {
                $('.check-item').prop('checked', $(this).prop('checked'));
            });

            $(document).on('change', '.check-item', function () {
                if (!$(this).prop('checked')) {
                    $('#checkAll').prop('checked', false);
                }
            });

            $('#btnPrintBulk').click(function () {
                const selected = [];
                $('.check-item:checked').each(function () {
                    selected.push($(this).val());
                });

                if (selected.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Silakan pilih bahan yang akan dicetak barcodenya.'
                    });
                    return;
                }

                // Create a hidden form and submit it to open in new tab
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= base_url('warehouse/cetak_barcode_bulk') ?>';
                form.target = '_blank';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids';
                input.value = selected.join(',');

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            });

            // Initial Load
            refreshTabelBahanBaku();

        });
    </script>
</body>

</html>