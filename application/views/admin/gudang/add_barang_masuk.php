<style>
    /* Enhanced Inventory Management Styles - Consistent with Karyawan Template */
    .inventory-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 2.5rem 0;
        margin-bottom: 2rem;
        border-radius: 0;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
        position: relative;
        overflow: hidden;
    }

    .inventory-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        pointer-events: none;
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        position: relative;
        z-index: 2;
    }

    .header-icon {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .header-text {
        flex: 1;
    }

    .header-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin: 0 0 0.5rem 0;
        line-height: 1.2;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .header-subtitle {
        font-size: 1.3rem;
        font-weight: 400;
        margin: 0 0 0.75rem 0;
        opacity: 0.9;
        font-weight: 300;
    }

    .header-description {
        font-size: 1rem;
        margin: 0;
        opacity: 0.8;
        line-height: 1.5;
        max-width: 500px;
    }

    .header-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        position: relative;
        z-index: 2;
    }

    .action-btn {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        border-radius: 0;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        backdrop-filter: blur(10px);
    }

    .action-btn:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        color: white;
        text-decoration: none;
    }

    .action-btn.primary {
        background: rgba(52, 152, 219, 0.8);
        border-color: rgba(52, 152, 219, 0.3);
    }

    .action-btn.warning {
        background: rgba(241, 196, 15, 0.8);
        border-color: rgba(241, 196, 15, 0.3);
    }

    .action-btn.secondary {
        background: rgba(149, 165, 166, 0.8);
        border-color: rgba(149, 165, 166, 0.3);
    }

    /* Main Content Styles */
    .main-content {
        background: white;
        border-radius: 0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .content-header {
        background: linear-gradient(135deg, #2c3e50, #34495e);
        color: white;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .content-header h5 {
        margin: 0;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .content-body {
        padding: 1.5rem;
    }

    /* Table Styles */
    .inventory-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1.5rem;
    }

    .inventory-table th {
        background: #f8f9fa;
        color: #2c3e50;
        font-weight: 600;
        padding: 1rem;
        text-align: left;
        border-bottom: 2px solid #e9ecef;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .inventory-table td {
        padding: 1rem;
        border-bottom: 1px solid #f8f9fa;
        vertical-align: middle;
        font-size: 0.95rem;
    }

    .inventory-table tbody tr {
        transition: all 0.3s ease;
    }

    .inventory-table tbody tr:hover {
        background: #f8f9fa;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .inventory-table .text-right {
        text-align: right;
    }

    .inventory-table .text-left {
        text-align: left;
    }

    .inventory-table .text-center {
        text-align: center;
    }

    /* Form Styles */
    .form-section {
        background: white;
        border-radius: 0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        overflow: hidden;
    }

    .form-header {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        padding: 1.5rem;
    }

    .form-header h5 {
        margin: 0;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-body {
        padding: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-control,
    .form-select {
        border: 1px solid #e9ecef;
        border-radius: 0;
        padding: 0.75rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }

    .input-group-text {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 0;
        color: #6c757d;
    }

    .form-text {
        font-size: 0.8rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }

    /* Button Styles */
    .btn {
        border-radius: 0;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        transition: all 0.3s ease;
        border: none;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .btn-primary {
        background: linear-gradient(135deg, #3498db, #2980b9);
    }

    .btn-warning {
        background: linear-gradient(135deg, #f39c12, #e67e22);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #95a5a6, #7f8c8d);
    }

    .btn-danger {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
    }

    .btn-success {
        background: linear-gradient(135deg, #27ae60, #2ecc71);
    }

    /* Modal Styles */
    .modal-content {
        border-radius: 0;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .modal-header {
        background: linear-gradient(135deg, #2c3e50, #34495e);
        color: white;
        border-bottom: none;
        padding: 1.5rem;
    }

    .modal-title {
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        border-top: 1px solid #e9ecef;
        padding: 1rem 1.5rem;
    }

    /* Select2 Custom Styling */
    .select2-container--bootstrap-5 .select2-selection {
        height: 42px !important;
        border: 1px solid #e9ecef !important;
        border-radius: 0 !important;
    }

    .select2-container--bootstrap-5 .select2-selection--single {
        padding: 0.75rem !important;
    }

    .select2-container--bootstrap-5 .select2-selection__rendered {
        line-height: 1.5 !important;
        padding-left: 0 !important;
    }

    .select2-container--bootstrap-5 .select2-selection__arrow {
        height: 40px !important;
    }

    .select2-container--bootstrap-5 .select2-dropdown {
        border: 1px solid #e9ecef !important;
        border-radius: 0 !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
    }

    .select2-container--bootstrap-5 .select2-results__option {
        padding: 0.75rem !important;
    }

    .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] {
        background-color: #3498db !important;
        color: white !important;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            text-align: center;
        }

        .header-actions {
            justify-content: center;
            flex-wrap: wrap;
        }

        .inventory-table {
            font-size: 0.8rem;
        }

        .inventory-table th,
        .inventory-table td {
            padding: 0.5rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }
    }

    /* Animation Classes */
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .slide-in {
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            transform: translateX(-100%);
        }

        to {
            transform: translateX(0);
        }
    }
</style>

<div class="app-content d-block">
    <!-- <div class="container-fluid py-3"> -->
    <!-- ================= HEADER SECTION ================= -->
    <?php $this->load->view('layout_admin/header_modul'); ?>

    <!-- ================= MAIN CONTENT ================= -->
    <div class="main-content fade-in">
        <div class="content-header">
            <h5 class="mb-0"><i class="fa fa-shopping-cart me-2"></i>Keranjang Pembelian</h5>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalProduk">
                <i class="fa fa-plus me-1"></i> Tambah Produk
            </button>
        </div>
        <div class="content-body">
            <table class="table table-bordered table-hover fs-5">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th class="text-left">Nama Bahan</th>
                        <th class="text-right">QTY</th>
                        <th class="text-right">Harga Beli</th>
                        <th class="text-right">Jumlah</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $total = 0;
                    foreach ($temp as $dt):
                        $jumlah = $dt->qty * $dt->harga;
                        $total += $jumlah;
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="text-left"><?= $dt->varian ?></td>
                            <td class="text-right"><?= number_format($dt->qty) ?></td>
                            <td class="text-right"><?= number_format($dt->harga) ?></td>
                            <td class="text-right"><?= number_format($jumlah) ?></td>
                            <td class="text-center">
                                <a href="<?= base_url('gudang/deleteTempbeli/' . $dt->temp_id) ?>"
                                    class="btn btn-danger btn-sm">
                                    <i class="fa fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="table-success">
                        <td colspan="4" class="text-end"><strong>TOTAL</strong></td>
                        <td class="text-right"><strong class="fs-5"><?= number_format($total) ?></strong></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= FORM SECTION ================= -->
    <div class="form-section fade-in">
        <div class="form-header">
            <h5><i class="fa fa-clipboard-list me-2"></i>Form Data Pembelian Bahan Baku</h5>
        </div>
        <div class="form-body">
            <?= validation_errors('<div class="alert alert-danger">', '</div>'); ?>
            <?= form_open('gudang/action_barang_masuk', ['class' => 'row g-3', 'autocomplete' => 'off']); ?>
            <div class="col-md-3">
                <label class="form-label">Tanggal Belanja <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    <input type="date" class="form-control" name="pembelian_tgl" value="<?= date('Y-m-d') ?>" required>
                    <input type="hidden" class="form-control" name="pembelian_total" value="<?= $total ?>">
                </div>
                <div class="form-text">Tanggal transaksi pembelian bahan baku.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label">No Faktur <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-file-invoice"></i></span>
                    <input type="text" class="form-control" name="no_faktur" placeholder="No Faktur Pembelian"
                        value="<?= set_value('no_faktur'); ?>" required>
                </div>
                <div class="form-text">Masukkan nomor faktur pembelian (jika ada).</div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Nama Supplier <span class="text-danger">*</span></label>
                <div class="input-group">

                    <select name="supplier_id" id="supplier_id" class="form-select select2-supplier" required>
                        <option value="">-- Pilih Supplier --</option>
                        <?php foreach ($supplier as $b): ?>
                            <option value="<?= $b->supplier_id ?>"><?= $b->supplier_nama ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-text">Pilih supplier tempat pembelian bahan baku.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Jenis Pembayaran <span class="text-danger">*</span></label>
                <div class="input-group">

                    <select name="metode_bayar" id="metode_bayar" class="form-select" required>
                        <option value="">-- Pilih Metode Pembayaran --</option>
                        <option value="cash">Cash</option>
                        <option value="kredit">Kredit</option>
                    </select>
                </div>
                <div class="form-text">Pilih jenis pembayaran.</div>
            </div>
            <div class="col-12 mt-4">
                <hr>
                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="fa fa-save me-2"></i> Simpan Pembelian
                </button>
            </div>
            </form>
        </div>
    </div>
</div>
</div>

<!-- Modal Tambah Produk -->
<div class="modal fade" id="modalProduk" tabindex="-1" aria-labelledby="modalProdukLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form id="formTambahProdukModal" method="post" action="<?= base_url('gudang/action_add_barang_masuk') ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalProdukLabel">
                        <i class="fa fa-plus me-2"></i>Tambah Barang Masuk
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8 mb-2">
                            <label class="form-label">Nama Bahan / Material <span class="text-danger">*</span></label>
                            <select class="form-select select2-bahan" name="material_id" id="material_id" required>
                                <option value="">-- Pilih Bahan Baku --</option>
                                <?php
                                $temp_ids = array();
                                if (isset($temp) && is_array($temp)) {
                                    foreach ($temp as $t) {
                                        $temp_ids[] = $t->material_id; // Filter by material_id, not bahan_id
                                    }
                                }
                                ?>
                                <?php foreach ($material as $m): ?>
                                    <?php if (!in_array($m->material_id, $temp_ids)): ?>
                                        <option value="<?= $m->material_id ?>" data-satuan="<?= $m->satuan_nama ?>"
                                            data-stok="-" data-harga="-">
                                            <?= $m->varian ?> - <?= $m->satuan_nama ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text text-muted">Pilih bahan baku yang akan dibeli. Hanya bahan yang belum
                                ada di keranjang yang tampil.</div>
                            <div class="mt-2" id="infoBahan" style="display:none;">
                                <span class="badge bg-info fs-6 me-2"><i class="fa fa-cube"></i> Satuan: <span
                                        id="infoSatuan">-</span></span>
                                <span class="badge bg-secondary fs-6 me-2"><i class="fa fa-archive"></i> Stok: <span
                                        id="infoStok">-</span></span>
                                <span class="badge bg-success fs-6"><i class="fa fa-money-bill"></i> Harga Terakhir:
                                    <span id="infoHarga">-</span></span>
                            </div>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label">Qty <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="qty" min="1" required id="qtyInput">
                                <span class="input-group-text" id="qtySatuan">-</span>
                            </div>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="form-label">Harga Beli <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" name="harga" min="0" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-lg w-50"><i class="fa fa-save me-2"></i>
                        Simpan</button>
                    <button type="button" class="btn btn-secondary btn-lg w-25" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Supplier -->
<div class="modal fade" id="modalSupplier" tabindex="-1" aria-labelledby="modalSupplierLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="<?= base_url('gudang/actionAddSupplier') ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSupplierLabel">
                        <i class="fa fa-building me-2"></i>Tambah Supplier Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info d-flex align-items-center mb-3">
                        <i class="fa fa-info-circle me-2"></i>
                        Isi data supplier dengan lengkap untuk kemudahan transaksi berikutnya.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Supplier</label>
                        <input type="text" name="supplier_nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No HP</label>
                        <input type="text" name="supplier_nohp" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="supplier_alamat" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="supplier_email" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- Include Select2 CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
    rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialize Select2 for Supplier dropdown
        $('.select2-supplier').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Pilih Supplier --',
            allowClear: true,
            width: '100%',
            language: {
                noResults: function () {
                    return "Tidak ada supplier ditemukan";
                },
                searching: function () {
                    return "Mencari...";
                }
            }
        });

        // Initialize Select2 for Bahan dropdown in modal
        $('.select2-bahan').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Pilih Bahan Baku --',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#modalProduk'),
            language: {
                noResults: function () {
                    return "Tidak ada bahan baku ditemukan";
                },
                searching: function () {
                    return "Mencari...";
                }
            },
            templateResult: function (data) {
                if (data.loading) return data.text;
                if (data.id === '') return data.text;

                var $option = $(data.element);
                var satuan = $option.data('satuan') || '';
                var stok = $option.data('stok') || '';
                var harga = $option.data('harga') || '';

                var $result = $('<div class="d-flex justify-content-between align-items-center">');
                $result.append('<span>' + data.text + '</span>');
                $result.append('<small class="text-muted">' + satuan + ' | Stok: ' + stok + '</small>');

                return $result;
            }
        });

        // Initialize Select2 for Satuan dropdown
        $('.select2-satuan').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Pilih Satuan --',
            allowClear: true,
            width: '100%',
            language: {
                noResults: function () {
                    return "Tidak ada satuan ditemukan";
                },
                searching: function () {
                    return "Mencari...";
                }
            }
        });

        // Initialize Select2 for Jenis Barang dropdown
        $('.select2-jenis').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Pilih Jenis --',
            allowClear: true,
            width: '100%',
            minimumResultsForSearch: Infinity,
            language: {
                noResults: function () {
                    return "Tidak ada jenis ditemukan";
                }
            }
        });

        // Satuan Qty update
        function updateQtySatuan() {
            var satuan = $('#material_id').find('option:selected').data('satuan') || '-';
            $('#qtySatuan').text(satuan);
        }

        $('#material_id').on('change', updateQtySatuan);
        updateQtySatuan();

        // Info bahan dinamis
        function updateInfoBahan() {
            var opt = $('#material_id').find('option:selected');
            var satuan = opt.data('satuan') || '-';
            var stok = opt.data('stok') || '-';
            var harga = opt.data('harga') || '-';
            if (opt.val()) {
                $('#infoBahan').show();
                $('#infoSatuan').text(satuan);
                $('#infoStok').text(stok);
                $('#infoHarga').text(harga);
            } else {
                $('#infoBahan').hide();
            }
        }

        $('#material_id').on('change', updateInfoBahan);
        updateInfoBahan();

        // Validasi visual
        $('#formTambahProdukModal input, #formTambahProdukModal select').on('invalid', function () {
            $(this).addClass('is-invalid');
        }).on('input change', function () {
            $(this).removeClass('is-invalid');
        });



        // Tambah Bahan Baku Modal (AJAX)
        $('#formTambahBahanBakuModal').on('submit', function (e) {
            e.preventDefault();
            var form = this;
            var btn = $('#btnSubmitBahanBakuModal');
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Menyimpan...');
            var formData = new FormData(form);
            fetch('<?= base_url('gudang/actionAddBahanBaku') ?>', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.pesan,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            form.reset();
                            $('.select2-satuan').val('').trigger('change');
                            $('.select2-jenis').val('').trigger('change');

                            var modal = bootstrap.Modal.getInstance(document.getElementById('modalTambahBahanBaku'));
                            modal.hide();

                            // Refresh select bahan baku di modal produk
                            fetch('<?= base_url('gudang/get_bahan_baku_json') ?>')
                                .then(res => res.json())
                                .then(res => {
                                    if (res.status === 'success') {
                                        var bahanSelect = $('#bahan_id');
                                        var old = bahanSelect.val();
                                        bahanSelect.html('<option value="">-- Pilih Bahan Baku --</option>');
                                        res.data.forEach(function (b) {
                                            var opt = $('<option></option>')
                                                .val(b.bahanbaku_id)
                                                .text(b.bahanbaku_nama)
                                                .attr('data-satuan', b.satuan_nama || '-')
                                                .attr('data-stok', b.stok_aktif || '-');
                                            bahanSelect.append(opt);
                                        });
                                        bahanSelect.val(old).trigger('change');

                                        $('.select2-bahan').select2({
                                            theme: 'bootstrap-5',
                                            placeholder: '-- Pilih Bahan Baku --',
                                            allowClear: true,
                                            width: '100%',
                                            language: {
                                                noResults: function () {
                                                    return "Tidak ada bahan baku ditemukan";
                                                },
                                                searching: function () {
                                                    return "Mencari...";
                                                }
                                            }
                                        });
                                    }
                                });
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: data.pesan
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menyimpan data'
                    });
                })
                .finally(() => {
                    btn.prop('disabled', false).html('<i class="fa fa-save me-1"></i>Simpan Bahan Baku');
                });
        });

        // Auto-focus on search input when modal opens
        $('#modalProduk').on('shown.bs.modal', function () {
            $('#searchBahanInput').focus();
        });

        // Currency input formatting
        $('.currency-input').on('input', function () {
            var value = $(this).val().replace(/[^\d]/g, '');
            if (value) {
                value = parseInt(value).toLocaleString('id-ID');
                $(this).val(value);
            }
        });
    });
</script>