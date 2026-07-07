<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    :root {
        --intake-primary: #6366f1;
        --intake-secondary: #4f46e5;
        --intake-accent: #818cf8;
        --intake-gradient: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
        --glass-white: rgba(255, 255, 255, 0.95);
        --shadow-premium: 0 10px 40px rgba(0, 0, 0, 0.06);
    }

    .intake-card {
        border: none;
        border-radius: 24px;
        color: white;
        overflow: hidden;
        position: relative;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        box-shadow: var(--shadow-premium);
        height: 100%;
        background-color: #fff;
    }

    .intake-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 50px rgba(99, 102, 241, 0.15);
    }

    .intake-card .card-body {
        padding: 1.8rem;
        z-index: 2;
        position: relative;
    }

    .intake-card .icon-bg {
        position: absolute;
        right: -15px;
        bottom: -15px;
        font-size: 5.5rem;
        opacity: 0.1;
        transform: rotate(-10deg);
        transition: all 0.4s ease;
    }

    .intake-card:hover .icon-bg {
        transform: rotate(0deg) scale(1.1);
        opacity: 0.15;
    }

    .bg-gradient-intake {
        background: var(--intake-gradient);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .btn-create-spk {
        background: var(--intake-gradient);
        color: white !important;
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 15px;
        transition: all 0.3s ease;
        box-shadow: 0 15px 35px rgba(99, 102, 241, 0.3);
        height: 100%;
    }

    .btn-create-spk:hover {
        transform: scale(1.02);
        box-shadow: 0 20px 45px rgba(99, 102, 241, 0.4);
        filter: brightness(1.1);
    }

    .btn-create-spk i {
        font-size: 3.5rem;
    }

    .btn-create-spk span {
        font-weight: 800;
        font-size: 1.2rem;
        letter-spacing: 0.5px;
    }

    .shortcut-item {
        background: white;
        border-radius: 20px;
        padding: 1.2rem;
        text-align: center;
        transition: all 0.3s ease;
        text-decoration: none !important;
        color: #374151;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        border: 1px solid #f3f4f6;
    }

    .shortcut-item:hover {
        background: #f9fafb;
        transform: translateY(-5px);
        border-color: var(--intake-primary);
        color: var(--intake-primary);
    }

    .shortcut-item i {
        font-size: 2rem;
        color: var(--intake-primary);
    }

    .table-container {
        background: white;
        border-radius: 24px;
        padding: 1.5rem;
        box-shadow: var(--shadow-premium);
    }

    .status-pill {
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }

        50% {
            transform: scale(1.1);
            opacity: 0.8;
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
</style>

<div class="app-content mt-n4 pb-5">
    <div class="container-fluid">
        <!-- Welcome Header -->
        <div class="row align-items-center mb-4 pt-2">
            <div class="col-md-7">
                <h4 class="fw-900 text-dark mb-1">FRONT-DESK INTAKE: <?= $this->session->userdata('nama_perusahaan') ?>
                </h4>
                <p class="text-muted fw-bold mb-0">Selamat datang kembali, <?= $this->session->userdata('username') ?>.
                    Fokus pada kepuasan konsumen hari ini!</p>
            </div>
            <div class="col-md-5 text-md-end">
                <div class="d-inline-flex align-items-center bg-white p-2 rounded-pill shadow-sm border px-4">
                    <span class="badge bg-soft-primary text-primary fw-bold rounded-pill px-3 py-2 me-3">GATEKEEPER
                        MODE</span>
                    <i class="fas fa-clock text-primary me-2"></i>
                    <span class="fw-900 text-dark"><?= date('H:i') ?></span>
                </div>
            </div>
        </div>

        <!-- Metric Row -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="intake-card bg-gradient-intake text-white">
                    <div class="card-body">
                        <h6 class="text-uppercase fw-bold opacity-75 small mb-2">SPK SAYA (HARI INI)</h6>
                        <h2 class="fw-900 mb-1"><?= $personal_spk_today ?></h2>
                        <p class="small fw-bold mb-0 opacity-75">Nota Yang Anda Tangani</p>
                        <i class="fas fa-file-signature icon-bg"></i>
                    </div>
                </div>
            </div>
            <!-- <div class="col-md-4">
                <div class="intake-card bg-gradient-success text-white">
                    <div class="card-body">
                        <h6 class="text-uppercase fw-bold opacity-75 small mb-2">HANDLING OMZET</h6>
                        <h2 class="fw-900 mb-1">Rp <?= number_format($personal_omzet_today, 0, ',', '.') ?></h2>
                        <p class="small fw-bold mb-0 opacity-75">Nilai Transaksi Hari Ini</p>
                        <i class="fas fa-wallet icon-bg"></i>
                    </div>
                </div>
            </div> -->
            <!-- <div class="col-md-3">
                <div class="intake-card bg-white border">
                    <div class="card-body text-dark">
                        <h6 class="text-uppercase fw-bold text-muted small mb-2">INTAKE CABANG</h6>
                        <h2 class="fw-900 mb-1 text-dark">Rp <?= number_format($branch_omzet_today, 0, ',', '.') ?></h2>
                        <p class="small fw-bold mb-0 text-muted">Total Omzet Cabang</p>
                        <i class="fas fa-store icon-bg text-dark opacity-10"></i>
                    </div>
                </div>
            </div> -->
            <div class="col-md-6">
                <a href="#" data-bs-toggle="modal" data-bs-target="#modalKonsumen"
                    class="text-decoration-none transition-hover d-block h-100">
                    <div class="intake-card bg-white border h-100">
                        <div class="card-body text-dark">
                            <h6 class="text-uppercase fw-bold text-muted small mb-2">TOTAL KONSUMEN</h6>
                            <h2 class="fw-900 mb-1 text-dark"><?= $this->db->count_all('tbl_konsumen') ?></h2>
                            <p class="small fw-bold mb-0 text-muted">Database Pelanggan Aktif</p>
                            <i class="fas fa-users icon-bg text-dark opacity-10"></i>
                        </div>
                    </div>
                </a>
            </div>

            <style>
                .transition-hover:hover {
                    transform: translateY(-5px);
                    transition: all 0.3s ease;
                }

                .transition-hover:hover .intake-card {
                    border-color: #0d6efd !important;
                    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
                }
            </style>
        </div>

        <div class="row g-4 mb-5">
            <!-- Big Action -->
            <div class="col-lg-5">
                <a href="<?= site_url('transaksi/createspk') ?>" class="btn-create-spk">
                    <i class="fas fa-file-import"></i>
                    <span>BUAT PESANAN BARU (SPK)</span>
                    <small class="opacity-75">Klik di sini untuk input data konsumen & produk</small>
                </a>
            </div>

            <!-- Shortcuts -->
            <div class="col-lg-7">
                <h6 class="fw-900 text-dark mb-3 px-1"><i class="fas fa-th-large text-primary me-2"></i>NAVIGASI CEPAT
                </h6>
                
                <?php 
                $karyawan_id = $this->session->userdata('karyawan_id');
                $karyawan = $this->db->get_where('tbl_karyawan', ['karyawan_id' => $karyawan_id])->row();
                $is_ecommerce = ($karyawan && $karyawan->divisi_id == 9) || $this->session->userdata('level') == 9;
                ?>
                
                <div class="row g-3">
                    <?php if ($is_ecommerce): ?>
                    <div class="col-4">
                        <a href="<?= site_url('marketplace/dashboard') ?>" class="shortcut-item" style="border-color: #f59e0b; background-color: #fffbeb;">
                            <i class="fas fa-store text-warning"></i>
                            <span class="fw-bold small">E-COMMERCE DESK</span>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <div class="col-4">
                        <a href="<?= site_url('konsumen') ?>" class="shortcut-item">
                            <i class="fas fa-user-plus"></i>
                            <span class="fw-bold small">DATABASE USER</span>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="<?= site_url('transaksi') ?>" class="shortcut-item">
                            <i class="fas fa-search"></i>
                            <span class="fw-bold small">CARI NOTA / SPK</span>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="<?= site_url('transaksi/transaksi_belumbayar') ?>" class="shortcut-item">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <span class="fw-bold small">DAFTAR TRANSAKSI BELUM BAYAR</span>
                        </a>
                    </div>

                    <!-- <div class="col-4">
                        <a href="<?= site_url('produksi') ?>" class="shortcut-item">
                            <i class="fas fa-clipboard-list"></i>
                            <span class="fw-bold small">STATUS PRODUKSI</span>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="<?= site_url('transaksi/daftar-pengambilan-barang') ?>" class="shortcut-item">
                            <i class="fas fa-shopping-bag"></i>
                            <span class="fw-bold small">PENGAMBILAN</span>
                        </a>
                    </div> -->
                    <div class="col-4">
                        <a href="#" class="shortcut-item" data-bs-toggle="modal" data-bs-target="#modalCekStok">
                            <i class="fas fa-boxes text-success"></i>
                            <span class="fw-bold small">CEK STOK GUDANG</span>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="#" class="shortcut-item" data-bs-toggle="modal" data-bs-target="#modalOperatorAntrian"
                            style="position: relative;">
                            <i class="fas fa-bullhorn text-danger"></i>
                            <span class="fw-bold small">PANGGIL ANTRIAN</span>
                            <?php if (isset($has_active_queue) && $has_active_queue > 0): ?>
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                    style="margin-left: -20px; margin-top: 10px; animation: pulse 1.5s infinite;">
                                    <?= $has_active_queue ?>
                                    <span class="visually-hidden">waiting</span>
                                </span>
                            <?php endif; ?>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="#" class="shortcut-item" data-bs-toggle="modal" data-bs-target="#modalKonsumen">
                            <i class="fas fa-address-book" style="color: var(--intake-primary);"></i>
                            <span class="fw-bold small">MANAJEMEN KONSUMEN</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Recent Intake -->
        <div class="row">
            <!-- ... existing table code ... -->
        </div>
    </div>
</div>

<!-- ===================== MODAL MANAJEMEN KONSUMEN ===================== -->
<div class="modal fade" id="modalKonsumen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background: var(--intake-gradient);">
                <h5 class="modal-title"><i class="fas fa-address-book me-2"></i> MANAJEMEN DATA KONSUMEN</h5>
                <div class="ms-auto d-flex gap-2 me-2">
                    <button class="btn btn-sm btn-light fw-bold" id="btnTambahKonsumen" data-bs-toggle="modal"
                        data-bs-target="#modalFormKonsumen" data-bs-dismiss="modal">
                        <i class="fas fa-plus me-1"></i> Tambah Konsumen
                    </button>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle" id="tableKonsumenDeskprint"
                        style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Konsumen</th>
                                <th>No. HP</th>
                                <th>Email</th>
                                <th>Alamat</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form Tambah/Edit Konsumen -->
<div class="modal fade" id="modalFormKonsumen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header" style="background: var(--intake-gradient);">
                <h5 class="modal-title text-white" id="formKonsumenTitle"><i class="fas fa-user-plus me-2"></i> Tambah
                    Konsumen Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formKonsumen">
                    <input type="hidden" id="fk_konsumen_id" name="konsumen_id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Konsumen <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="fk_nama" name="konsumen_nama"
                            placeholder="Masukkan nama lengkap" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">No. HP / WhatsApp</label>
                        <input type="text" class="form-control" id="fk_nohp" name="konsumen_nohp"
                            placeholder="Contoh: 081234567890">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" class="form-control" id="fk_email" name="konsumen_email"
                            placeholder="nama@email.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alamat</label>
                        <textarea class="form-control" id="fk_alamat" name="konsumen_alamat" rows="2"
                            placeholder="Alamat lengkap konsumen"></textarea>
                    </div>
                    <div class="mb-3" id="fk_status_wrap" style="display:none;">
                        <label class="form-label fw-bold">Status</label>
                        <select class="form-select" id="fk_status" name="status">
                            <option value="1">Aktif</option>
                            <option value="0">Non-Aktif</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light fw-bold" id="btnKembaliKeList">Batal</button>
                <button type="button" class="btn fw-bold text-white" style="background: var(--intake-gradient);"
                    id="btnSimpanKonsumen">
                    <i class="fas fa-save me-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>
<!-- ============ END MODAL KONSUMEN ============ -->

<!-- Modal Operator Antrian -->
<div class="modal fade" id="modalOperatorAntrian" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" style="height: 90vh;">
        <div class="modal-content h-100">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-bullhorn me-2"></i> OPERATOR PANGGILAN ANTRIAN</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="height: 100%;">
                <iframe src="<?= site_url('antrian/operator') ?>"
                    style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>
<div class="col-12">
    <div class="table-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-900 text-dark mb-0"><i class="fas fa-history text-primary me-2"></i>SPK TERAKHIR
                YANG ANDA TANGANI</h5>
            <a href="<?= site_url('transaksi') ?>"
                class="btn btn-sm btn-light fw-bold text-primary rounded-pill px-3">Lihat Semua History</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-muted small uppercase">
                        <th class="border-0"># NO SPK</th>
                        <th class="border-0">WAKTU</th>
                        <th class="border-0">KONSUMEN</th>
                        <th class="border-0">ITEM</th>
                        <th class="border-0 text-center">PEMBAYARAN</th>
                        <th class="border-0 text-end">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent_spks)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">Belum ada SPK yang dibuat hari ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recent_spks as $spk): ?>
                            <tr>
                                <td><span class="fw-900 text-dark"><?= $spk->nospk ?></span></td>
                                <td><small
                                        class="fw-bold text-muted"><?= date('H:i d/m', strtotime($spk->created_at)) ?></small>
                                </td>
                                <td>
                                    <div class="fw-bold"><?= $spk->konsumen_nama ?></div>
                                </td>
                                <td><span class="badge bg-light text-dark fw-bold"><?= $spk->total_item ?>
                                        Produk</span></td>
                                <td class="text-center">
                                    <?php
                                    $p_class = 'bg-soft-warning text-warning';
                                    if ($spk->status_pembayaran == 'paid')
                                        $p_class = 'bg-soft-success text-success';
                                    ?>
                                    <span class="status-pill <?= $p_class ?>"><?= $spk->status_pembayaran ?></span>
                                </td>
                                <td class="text-end">
                                    <a href="<?= site_url('cekpesanan/cetak_invoice/' . urlencode(encrypt_spk($spk->nospk))) ?>"
                                        target="_blank" class="btn btn-sm btn-icon btn-light rounded-circle text-primary">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <!-- <a href="<?= site_url('transaksi/editSPK/' . $spk->nospk) ?>"
                                        class="btn btn-sm btn-icon btn-light rounded-circle text-info ms-1">
                                        <i class="fas fa-edit"></i>
                                    </a> -->
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</div>
</div>

<!-- Modal Cek Stok Gudang -->
<div class="modal fade" id="modalCekStok" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-boxes me-2"></i> CEK STOK GUDANG (LIVE)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle w-100" id="tableCekStok">
                        <thead class="table-light text-muted small uppercase">
                            <?php $cabang_list = $this->db->order_by('id_perusahaan', 'ASC')->get('tbl_perusahaan')->result(); ?>
                            <tr>
                                <th style="min-width: 250px;">NAMA BAHAN</th>
                                <th style="min-width: 250px;">VARIAN / UKURAN</th>
                                <?php foreach ($cabang_list as $c): ?>
                                    <th class="text-center" style="min-width: 120px;"><?= strtoupper($c->nama_perusahaan) ?>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let stokTableLoaded = false;
        const modalStok = document.getElementById('modalCekStok');

        if (modalStok) {
            modalStok.addEventListener('show.bs.modal', function () {
                if (!stokTableLoaded) {
                    if ($.fn.DataTable.isDataTable('#tableCekStok')) {
                        $('#tableCekStok').DataTable().destroy();
                    }

                    $('#tableCekStok').DataTable({
                        ajax: {
                            url: '<?= site_url('dashboard/ajax_get_stock_deskprint_pivoted') ?>',
                            dataSrc: ''
                        },
                        columns: [
                            { data: 'bahan_nama', className: 'fw-bold' },
                            { data: 'varian' },
                            <?php foreach ($cabang_list as $c): ?>
                                    {
                                    data: 'branch_<?= $c->id_perusahaan ?>',
                                    defaultContent: '0',
                                    className: 'text-center fw-900',
                                    render: function (data, type, row) {
                                        var qty = parseFloat(data) || 0;
                                        var konversi = parseFloat(row.konversi) || 1;
                                        var satGudang = row.satuan_gudang || '';
                                        var satDasar = row.satuan_dasar || '';

                                        if (qty === 0) return '<span class="text-muted">-</span>';

                                        if (konversi > 1) {
                                            var main_qty = Math.floor(qty / konversi);
                                            var small_qty = Math.round(qty % konversi);
                                            var html = '';

                                            if (main_qty !== 0 || small_qty === 0) {
                                                html += '<span class="text-primary" style="font-size: 1.1rem;">' + main_qty.toLocaleString('id-ID') + '</span> <span class="text-muted">' + satGudang + '</span>';
                                            }

                                            if (small_qty !== 0) {
                                                if (html !== '') html += '<br>';
                                                html += '<span class="text-info">' + small_qty.toLocaleString('id-ID') + '</span> <span class="text-muted small">' + satDasar + '</span>';
                                            }
                                            return html;
                                        } else {
                                            return '<span class="text-primary" style="font-size: 1.1rem;">' + qty.toLocaleString('id-ID') + '</span> <span class="text-muted">' + (satDasar || satGudang) + '</span>';
                                        }
                                    }
                                },
                            <?php endforeach; ?>
                        ],
                        language: {
                            search: "Cari Material:",
                            lengthMenu: "Tampilkan _MENU_ data",
                            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ material",
                            infoEmpty: "Tidak ada data material",
                            zeroRecords: "Material tidak ditemukan",
                            paginate: {
                                first: "Pertama",
                                last: "Terakhir",
                                next: "Selanjutnya",
                                previous: "Sebelumnya"
                            }
                        },
                        pageLength: 10,
                        order: [[0, 'asc']]
                    });
                    stokTableLoaded = true;
                } else {
                    $('#tableCekStok').DataTable().ajax.reload(null, false);
                }
            });
        }
    });
</script>

<script>
    // ============================================================
    // CRUD KONSUMEN - Dashboard Desk Print
    // ============================================================
    var BASE_URL_DS = '<?= site_url() ?>';
    var konsumenTable = null;
    var isEditMode = false;

    $(document).ready(function () {

        // ---- Inisialisasi DataTables saat modal dibuka ----
        $('#modalKonsumen').on('shown.bs.modal', function () {
            if (!konsumenTable) {
                konsumenTable = $('#tableKonsumenDeskprint').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: BASE_URL_DS + 'dashboard/konsumen_get_ajax',
                        type: 'POST'
                    },
                    columns: [
                        { data: 'no', className: 'text-center', orderable: false },
                        { data: 'konsumen_nama', render: function (d) { return '<span class="fw-bold">' + (d || '-') + '</span>'; } },
                        { data: 'konsumen_nohp', render: function (d) { return d ? '<i class="fas fa-phone me-1 text-success"></i>' + d : '<span class="text-muted">-</span>'; } },
                        { data: 'konsumen_email', render: function (d) { return d ? '<small>' + d + '</small>' : '<span class="text-muted">-</span>'; } },
                        { data: 'konsumen_alamat', render: function (d) { return d ? '<small class="text-muted">' + d + '</small>' : '-'; } },
                        {
                            data: 'status', className: 'text-center', render: function (d) {
                                return d == '1' ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Non-Aktif</span>';
                            }
                        },
                        {
                            data: 'konsumen_id', className: 'text-center', orderable: false, render: function (id, t, row) {
                                return `<div class="d-flex justify-content-center gap-1">
                            <button class="btn btn-sm btn-outline-primary btn-edit-konsumen"
                                data-id="${id}" data-nama="${row.konsumen_nama}" data-nohp="${row.konsumen_nohp || ''}"
                                data-email="${row.konsumen_email || ''}" data-alamat="${row.konsumen_alamat || ''}" data-status="${row.status}"
                                title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-hapus-konsumen" data-id="${id}" data-nama="${row.konsumen_nama}" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>`;
                            }
                        }
                    ],
                    language: {
                        search: "Cari:", lengthMenu: "Tampilkan _MENU_ data",
                        info: "Menampilkan _START_-_END_ dari _TOTAL_ konsumen",
                        infoEmpty: "Tidak ada data", zeroRecords: "Konsumen tidak ditemukan",
                        paginate: { first: "«", last: "»", next: "›", previous: "‹" },
                        processing: '<div class="text-center py-3"><div class="spinner-border text-primary"></div></div>'
                    },
                    pageLength: 10,
                    order: [[1, 'asc']]
                });
            } else {
                konsumenTable.ajax.reload(null, false);
            }
        });

        // ---- Tombol TAMBAH dari modal list ----
        $('#btnTambahKonsumen').on('click', function () {
            isEditMode = false;
            $('#formKonsumenTitle').html('<i class="fas fa-user-plus me-2"></i> Tambah Konsumen Baru');
            $('#formKonsumen')[0].reset();
            $('#fk_konsumen_id').val('');
            $('#fk_status_wrap').hide();
        });

        // ---- Tombol EDIT (delegasi karena DataTable dinamis) ----
        $(document).on('click', '.btn-edit-konsumen', function () {
            isEditMode = true;
            var btn = $(this);
            $('#fk_konsumen_id').val(btn.data('id'));
            $('#fk_nama').val(btn.data('nama'));
            $('#fk_nohp').val(btn.data('nohp'));
            $('#fk_email').val(btn.data('email'));
            $('#fk_alamat').val(btn.data('alamat'));
            $('#fk_status').val(btn.data('status'));
            $('#fk_status_wrap').show();
            $('#formKonsumenTitle').html('<i class="fas fa-user-edit me-2"></i> Edit Data Konsumen');

            // Tutup modal list, buka modal form
            var modalList = bootstrap.Modal.getInstance(document.getElementById('modalKonsumen'));
            if (modalList) modalList.hide();
            var modalForm = new bootstrap.Modal(document.getElementById('modalFormKonsumen'));
            modalForm.show();
        });

        // ---- Tombol HAPUS ----
        $(document).on('click', '.btn-hapus-konsumen', function () {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            if (!confirm('Apakah Anda yakin ingin menghapus konsumen "' + nama + '"?\n\nTindakan ini tidak dapat dibatalkan.')) return;

            $.ajax({
                url: BASE_URL_DS + 'dashboard/konsumen_delete/' + id,
                type: 'GET',
                success: function () {
                    if (konsumenTable) konsumenTable.ajax.reload(null, false);
                    showToast('success', 'Konsumen "' + nama + '" berhasil dihapus.');
                },
                error: function () { showToast('danger', 'Gagal menghapus konsumen.'); }
            });
        });

        // ---- Tombol SIMPAN (Tambah / Edit) ----
        $('#btnSimpanKonsumen').on('click', function () {
            var nama = $('#fk_nama').val().trim();
            if (!nama) { alert('Nama konsumen wajib diisi!'); $('#fk_nama').focus(); return; }

            var url = isEditMode ? BASE_URL_DS + 'dashboard/konsumen_edit' : BASE_URL_DS + 'dashboard/konsumen_add';
            var formData = $('#formKonsumen').serialize();

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function () {
                    // Tutup modal form, buka kembali modal list
                    var modalForm = bootstrap.Modal.getInstance(document.getElementById('modalFormKonsumen'));
                    if (modalForm) modalForm.hide();
                    var modalList = new bootstrap.Modal(document.getElementById('modalKonsumen'));
                    modalList.show();
                    if (konsumenTable) konsumenTable.ajax.reload(null, false);
                    showToast('success', isEditMode ? 'Data konsumen berhasil diperbarui!' : 'Konsumen baru berhasil ditambahkan!');
                },
                error: function () { showToast('danger', 'Terjadi kesalahan saat menyimpan data.'); }
            });
        });

        // ---- Tombol Batal di form (kembali ke daftar) ----
        $('#btnKembaliKeList').on('click', function () {
            var modalForm = bootstrap.Modal.getInstance(document.getElementById('modalFormKonsumen'));
            if (modalForm) modalForm.hide();
            var modalList = new bootstrap.Modal(document.getElementById('modalKonsumen'));
            modalList.show();
        });

        // ---- Helper: Toast Notification ----
        function showToast(type, msg) {
            var toastEl = $('<div class="toast align-items-center text-white border-0 position-fixed" style="bottom:20px;right:20px;z-index:9999;min-width:300px;">' +
                '<div class="d-flex"><div class="toast-body fw-bold"><i class="fas fa-check-circle me-2"></i>' + msg + '</div>' +
                '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>');
            toastEl.addClass(type === 'success' ? 'bg-success' : 'bg-danger');
            $('body').append(toastEl);
            var t = new bootstrap.Toast(toastEl[0], { delay: 3000 });
            t.show();
            toastEl[0].addEventListener('hidden.bs.toast', function () { toastEl.remove(); });
        }
    });
</script>