<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    /* ==========================================================================
    TGM PREMIUM UI/UX - MASTER SUPPLIER
    ========================================================================== */
    :root {
        --fin-primary: #0f172a;
        --fin-accent: #3b82f6;
        --fin-success: #10b981;
        --fin-danger: #ef4444;
        --fin-warning: #f59e0b;
        --fin-info: #06b6d4;
        --fin-bg: #f8fafc;
        --card-radius: 20px;
    }

    .app-content {
        background-color: var(--fin-bg);
        min-height: 100vh;
        font-family: 'Inter', sans-serif;
    }

    /* Filter HUD Styling */
    .filter-hud {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        margin-bottom: 2rem;
    }

    .filter-hud .form-select,
    .filter-hud .form-control {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 0.6rem 1rem;
        font-weight: 700;
        font-size: 0.85rem;
    }

    /* Premium Stat Cards */
    .cash-stat-card {
        background: white;
        border-radius: var(--card-radius);
        padding: 1.5rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .cash-stat-card.action-card {
        background: var(--fin-success);
        text-align: center;
        cursor: pointer;
        padding: 0;
    }

    .cash-stat-card.action-card button {
        height: 100%;
        width: 100%;
        padding: 1.5rem;
        border-radius: var(--card-radius);
    }

    .cash-stat-card.action-card:hover {
        background: #059669;
    }

    .cash-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    }

    .cash-stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        margin-bottom: 0.75rem;
    }

    .cash-stat-value {
        font-size: 1.4rem;
        font-weight: 900;
        color: var(--fin-primary);
        letter-spacing: -0.5px;
        line-height: 1.2;
    }

    .cash-stat-label {
        font-size: 0.7rem;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.25rem;
    }

    /* Content Card */
    .content-card {
        background: white;
        border-radius: var(--card-radius);
        border: 1px solid #e2e8f0;
        box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .card-header-premium {
        background: var(--fin-primary);
        color: white;
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Table Styling */
    .table-tx thead th {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        padding: 1rem 1.25rem;
        font-weight: 800;
        color: #64748b;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .table-tx tbody td {
        padding: 1rem 1.25rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .monospaced {
        font-family: 'JetBrains Mono', 'SF Mono', monospace;
    }

    /* Action Button */
    .btn-action-modern {
        background: var(--fin-bg);
        color: var(--fin-primary);
        border: 1px solid #cbd5e1;
        padding: 0.4rem 0.6rem;
        border-radius: 8px;
        font-weight: 800;
        font-size: 0.75rem;
        transition: all 0.2s;
    }

    .btn-action-modern.edit:hover {
        background: var(--fin-info);
        color: white;
        border-color: var(--fin-info);
    }

    .btn-action-modern.delete:hover {
        background: var(--fin-danger);
        color: white;
        border-color: var(--fin-danger);
    }
</style>

<div class="app-content mt-3 pb-5">
    <?php $this->load->view('layout_admin/header_modul'); ?>
<?php $this->load->view('admin/pengaturan/_settings_nav'); ?>

    <div class="container-fluid py-4">
        <!-- Flash Messages -->
        <div id="alert-container">
            <?= $this->session->flashdata('message'); ?>
        </div>

        <?php $total_suppliers = count($tampilData); ?>

        <!-- TOP FILTER HUD -->
        <div class="filter-hud">
            <div class="row align-items-center g-3">
                <div class="col-xl-6 col-lg-12">
                    <div class="d-flex align-items-center gap-3">
                        <a href="<?= base_url('hr') ?>"
                            class="btn btn-outline-secondary border-2 fw-900 rounded-3 px-3 shadow-sm d-flex align-items-center justify-content-center"
                            style="height: 48px;" title="Kembali ke Dashboard">
                            <i class="fas fa-arrow-left fs-5"></i>
                        </a>
                        <div class="p-3 bg-light rounded-3 text-primary border shadow-sm">
                            <i class="fas fa-truck-loading fs-4"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h6 class="mb-0 fw-900 text-dark ls-n1">MASTER SUPPLIER</h6>
                                <span
                                    class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2 py-1"
                                    style="font-size: 0.65rem; font-weight: 800; letter-spacing: 0.5px;"><?= number_format($total_suppliers) ?>
                                    VENDOR AKTIF</span>
                            </div>
                            <p class="text-muted small mb-0 fw-bold">Manajemen Data Pemasok & Vendor</p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-lg-12 text-end">
                    <div class="d-flex gap-2 justify-content-end">
                        <div class="input-group" style="max-width: 250px;">
                            <span class="input-group-text bg-white border-end-0"><i
                                    class="fa fa-search text-muted"></i></span>
                            <input type="text" id="customSearch" class="form-control border-start-0"
                                placeholder="Cari nama, alamat...">
                        </div>
                        <button type="button" class="btn btn-success rounded-3 py-2 px-3 fw-900 shadow-sm"
                            data-bs-toggle="modal" data-bs-target="#modalSupplier">
                            <i class="fas fa-plus-circle me-1"></i> TAMBAH
                        </button>
                        <button type="button" class="btn btn-outline-primary border-2 fw-900 rounded-3 px-3 shadow-sm"
                            onclick="location.reload()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <button type="button" class="btn btn-success rounded-3 py-2 px-3 fw-900 shadow-sm"
                            id="btnExportExcel">
                            <i class="fas fa-file-excel"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>



        <!-- MAIN TABLE CARD -->
        <div class="content-card shadow-sm">
            <div class="card-header-premium">
                <h6 class="fw-900 mb-0 ls-n1"><i class="fas fa-list me-2 opacity-50"></i>DAFTAR MITRA SUPPLIER</h6>
            </div>

            <div class="table-responsive">
                <table class="table table-tx table-hover mb-0 w-100" id="tableSupplier">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">NO</th>
                            <th>KODE</th>
                            <th>NAMA SUPPLIER</th>
                            <th>KONTAK</th>
                            <th>ALAMAT & KOTA</th>
                            <th class="text-center">TERMIN</th>
                            <th class="text-center no-print" style="width: 120px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody id="supplierTableBody">
                        <?php $no = 1;
                        foreach ($tampilData as $dt): ?>
                            <tr>
                                <td class="text-center fw-bold text-muted"><?= $no++ ?></td>
                                <td>
                                    <span
                                        class="badge bg-light text-dark border px-3 py-2 fw-bold rounded-pill monospaced"><?= $dt->supplier_kode ?></span>
                                </td>
                                <td>
                                    <div class="fw-800 text-dark"><?= $dt->supplier_nama ?></div>
                                    <?php if ($dt->NPWP): ?>
                                        <div class="small text-muted fw-bold mt-1" style="font-size: 0.65rem;"><i
                                                class="fas fa-id-card me-1"></i> NPWP: <?= $dt->NPWP ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <div class="small fw-bold text-dark"><i
                                                class="fas fa-phone-alt me-2 text-success"></i> <?= $dt->supplier_nohp ?>
                                        </div>
                                        <?php if ($dt->supplier_email): ?>
                                            <div class="small text-muted"><i class="fas fa-envelope me-2 text-primary"></i>
                                                <?= $dt->supplier_email ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="small fw-bold text-dark text-uppercase"><?= $dt->kota_provinsi ?></div>
                                    <div class="small text-muted text-truncate"
                                        style="max-width: 250px; font-size: 0.7rem;"><?= $dt->supplier_alamat ?></div>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3 py-1 fw-bold">
                                        <?= $dt->termin ?> Hari
                                    </span>
                                </td>
                                <td class="text-center no-print">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" class="btn-action-modern edit btn-edit-supplier"
                                            title="Edit Supplier" data-id="<?= $dt->supplier_id ?>"
                                            data-kode="<?= htmlspecialchars($dt->supplier_kode) ?>"
                                            data-nama="<?= htmlspecialchars($dt->supplier_nama) ?>"
                                            data-alamat="<?= htmlspecialchars($dt->supplier_alamat) ?>"
                                            data-kota="<?= htmlspecialchars($dt->kota_provinsi) ?>"
                                            data-nohp="<?= htmlspecialchars($dt->supplier_nohp) ?>"
                                            data-telp="<?= htmlspecialchars($dt->supplier_telp) ?>"
                                            data-email="<?= htmlspecialchars($dt->supplier_email) ?>"
                                            data-alamat-pajak="<?= htmlspecialchars($dt->alamat_pajak) ?>"
                                            data-npwp="<?= htmlspecialchars($dt->NPWP) ?>" data-termin="<?= $dt->termin ?>">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                        <button type="button" class="btn-action-modern delete btn-hapus-supplier"
                                            title="Hapus Supplier" data-id="<?= $dt->supplier_id ?>"
                                            data-nama="<?= htmlspecialchars($dt->supplier_nama) ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Supplier -->
<div class="modal fade" id="modalSupplier" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="formTambahSupplier" autocomplete="off"
            class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-circle p-2 me-3">
                        <i class="fas fa-truck-loading text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-900 ls-1 mb-0 text-uppercase">Tambah Mitra Supplier</h5>
                        <small class="text-white-50 fw-bold">Registrasi vendor/pemasok baru</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4 p-md-5 bg-light">
                <div class="row g-4 bg-white p-4 rounded-4 shadow-sm">
                    <div class="col-md-4">
                        <label class="form-label fw-900 text-dark small text-uppercase ls-1">Kode Supplier <span
                                class="text-danger">*</span></label>
                        <input type="text" name="supplier_kode"
                            class="form-control form-control-premium fw-bold text-primary" required maxlength="30"
                            placeholder="Auto..." id="add_supplier_kode">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-900 text-dark small text-uppercase ls-1">Nama Perusahaan / Supplier
                            <span class="text-danger">*</span></label>
                        <input type="text" name="supplier_nama" class="form-control form-control-premium fw-bold"
                            required maxlength="100">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-900 text-dark small text-uppercase ls-1">No. HP / WhatsApp <span
                                class="text-danger">*</span></label>
                        <input type="text" name="supplier_nohp" class="form-control form-control-premium fw-bold"
                            required maxlength="20">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-900 text-dark small text-uppercase ls-1">Email</label>
                        <input type="email" name="supplier_email" class="form-control form-control-premium fw-bold"
                            maxlength="50">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-900 text-dark small text-uppercase ls-1">Alamat Lengkap <span
                                class="text-danger">*</span></label>
                        <textarea name="supplier_alamat" class="form-control form-control-premium fw-bold" rows="2"
                            required maxlength="100"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-900 text-dark small text-uppercase ls-1">Kota / Provinsi <span
                                class="text-danger">*</span></label>
                        <input type="text" name="kota_provinsi" class="form-control form-control-premium fw-bold"
                            required maxlength="40">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-900 text-dark small text-uppercase ls-1">Termin Pembayaran</label>
                        <div class="input-group">
                            <input type="number" name="termin"
                                class="form-control form-control-premium fw-bold border-end-0" value="30" min="0"
                                max="365">
                            <span class="input-group-text bg-light fw-bold text-muted border-start-0">Hari</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-900 text-dark small text-uppercase ls-1">NPWP (Opsional)</label>
                        <input type="text" name="npwp" class="form-control form-control-premium fw-bold" maxlength="40">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-900 text-dark small text-uppercase ls-1">Alamat Pajak
                            (Opsional)</label>
                        <textarea name="alamat_pajak" class="form-control form-control-premium fw-bold" rows="1"
                            maxlength="255"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white p-4 border-top">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-800"
                    data-bs-dismiss="modal">BATAL</button>
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-900 shadow">SIMPAN DATA</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Supplier -->
<div class="modal fade" id="modalEditSupplier" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="formEditSupplier" autocomplete="off"
            class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <input type="hidden" name="supplier_id" id="edit_supplier_id">
            <div class="modal-header bg-dark text-white p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-info rounded-circle p-2 me-3">
                        <i class="fas fa-edit text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-900 ls-1 mb-0 text-uppercase">Update Mitra Supplier</h5>
                        <small class="text-white-50 fw-bold">Edit informasi vendor/pemasok</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4 p-md-5 bg-light">
                <div class="row g-4 bg-white p-4 rounded-4 shadow-sm">
                    <div class="col-md-4">
                        <label class="form-label fw-900 text-dark small text-uppercase ls-1">Kode Supplier <span
                                class="text-danger">*</span></label>
                        <input type="text" name="supplier_kode" id="edit_supplier_kode"
                            class="form-control form-control-premium fw-bold text-info" required maxlength="30">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-900 text-dark small text-uppercase ls-1">Nama Perusahaan / Supplier
                            <span class="text-danger">*</span></label>
                        <input type="text" name="supplier_nama" id="edit_supplier_nama"
                            class="form-control form-control-premium fw-bold" required maxlength="100">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-900 text-dark small text-uppercase ls-1">No. HP / WhatsApp <span
                                class="text-danger">*</span></label>
                        <input type="text" name="supplier_nohp" id="edit_supplier_nohp"
                            class="form-control form-control-premium fw-bold" required maxlength="20">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-900 text-dark small text-uppercase ls-1">Email</label>
                        <input type="email" name="supplier_email" id="edit_supplier_email"
                            class="form-control form-control-premium fw-bold" maxlength="50">
                    </div>
                    <!-- Hidden -->
                    <input type="hidden" name="supplier_telp" id="edit_supplier_telp">
                    <div class="col-md-12">
                        <label class="form-label fw-900 text-dark small text-uppercase ls-1">Alamat Lengkap <span
                                class="text-danger">*</span></label>
                        <textarea name="supplier_alamat" id="edit_supplier_alamat"
                            class="form-control form-control-premium fw-bold" rows="2" required
                            maxlength="100"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-900 text-dark small text-uppercase ls-1">Kota / Provinsi <span
                                class="text-danger">*</span></label>
                        <input type="text" name="kota_provinsi" id="edit_kota_provinsi"
                            class="form-control form-control-premium fw-bold" required maxlength="40">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-900 text-dark small text-uppercase ls-1">Termin Pembayaran</label>
                        <div class="input-group">
                            <input type="number" name="termin" id="edit_termin"
                                class="form-control form-control-premium fw-bold border-end-0" min="0" max="365">
                            <span class="input-group-text bg-light fw-bold text-muted border-start-0">Hari</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-900 text-dark small text-uppercase ls-1">NPWP (Opsional)</label>
                        <input type="text" name="npwp" id="edit_npwp" class="form-control form-control-premium fw-bold"
                            maxlength="40">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-900 text-dark small text-uppercase ls-1">Alamat Pajak
                            (Opsional)</label>
                        <textarea name="alamat_pajak" id="edit_alamat_pajak"
                            class="form-control form-control-premium fw-bold" rows="1" maxlength="255"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white p-4 border-top">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-800"
                    data-bs-dismiss="modal">BATAL</button>
                <button type="submit" class="btn btn-info text-white rounded-pill px-5 fw-900 shadow">UPDATE
                    DATA</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
<script>
    $(document).ready(function () {
        // Initialize DataTable
        const table = $('#tableSupplier').DataTable({
            pageLength: 50,
            language: {
                url: '<?= base_url('assets/js/datatables-id.json') ?>',
                info: "MENAMPILKAN _START_ - _END_ DARI _TOTAL_ SUPPLIER",
                paginate: { "previous": "<i class='fas fa-chevron-left'></i>", "next": "<i class='fas fa-chevron-right'></i>" }
            },
            dom: "<'row'<'col-sm-12'tr>>" +
                "<'row p-4'<'col-sm-5 small fw-bold text-muted'i><'col-sm-7'p>>",
            columnDefs: [
                { targets: 0, orderable: false, searchable: false },
                { targets: 6, orderable: false, searchable: false }
            ]
        });

        // Client-side DataTables search
        $("#customSearch").on("keyup", function () {
            table.search(this.value).draw();
        });

        // Export to Excel
        $('#btnExportExcel').on('click', function () {
            var exportTable = document.getElementById('tableSupplier');
            var wb = XLSX.utils.table_to_book(exportTable, { sheet: "Master Supplier" });
            XLSX.writeFile(wb, 'Master_Supplier_TGM_ERP.xlsx');
        });

        // Auto-generate supplier code
        $('#add_supplier_kode').on('focus', function () {
            if (!this.value) {
                fetch('<?= base_url('pengaturan/supplier_generate_code') ?>')
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success') {
                            this.value = res.code;
                        }
                    });
            }
        });

        // Add
        $('#formTambahSupplier').on('submit', function (e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> menyimpan...');
            const formData = new FormData(this);
            fetch('<?= base_url('pengaturan/supplier_add') ?>', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        location.reload();
                    } else {
                        btn.prop('disabled', false).html('SIMPAN DATA');
                        Swal.fire('Gagal', res.pesan, 'error');
                    }
                });
        });

        // Edit Modal Trigger
        $(document).on('click', '.btn-edit-supplier', function () {
            const btn = $(this);
            $('#edit_supplier_id').val(btn.data('id'));
            $('#edit_supplier_kode').val(btn.data('kode'));
            $('#edit_supplier_nama').val(btn.data('nama'));
            $('#edit_supplier_alamat').val(btn.data('alamat'));
            $('#edit_kota_provinsi').val(btn.data('kota'));
            $('#edit_supplier_nohp').val(btn.data('nohp'));
            $('#edit_supplier_email').val(btn.data('email'));
            $('#edit_alamat_pajak').val(btn.data('alamat-pajak'));
            $('#edit_npwp').val(btn.data('npwp'));
            $('#edit_termin').val(btn.data('termin'));
            new bootstrap.Modal(document.getElementById('modalEditSupplier')).show();
        });

        // Update
        $('#formEditSupplier').on('submit', function (e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> updating...');
            const formData = new FormData(this);
            fetch('<?= base_url('pengaturan/supplier_update') ?>', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        location.reload();
                    } else {
                        btn.prop('disabled', false).html('UPDATE DATA');
                        Swal.fire('Gagal', res.pesan, 'error');
                    }
                });
        });

        // Delete
        $(document).on('click', '.btn-hapus-supplier', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Hapus Supplier?',
                text: `Yakin ingin menghapus ${$(this).data('nama')}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'YA, HAPUS'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('<?= base_url('pengaturan/supplier_delete/') ?>' + id, { method: 'POST' })
                        .then(res => res.json())
                        .then(res => {
                            if (res.status === 'success') location.reload();
                            else Swal.fire('Gagal', res.pesan, 'error');
                        });
                }
            });
        });

        setTimeout(() => $('#alert-container .alert').fadeOut(), 3000);
    });
</script>