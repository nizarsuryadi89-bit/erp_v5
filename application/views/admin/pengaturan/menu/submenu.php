<?php $this->load->view('layout_admin/header_modul'); ?>

<div class="product-catalog-shell">

<!-- ====== ODOO-STYLE CONTROL PANEL ====== -->
<div class="bg-white border-bottom px-4 py-3 mb-4 shadow-sm" style="position: sticky; top: 0; z-index: 900;">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <!-- Title -->
        <div class="d-flex align-items-center gap-3">
            <ol class="breadcrumb mb-0" style="font-size: 1.2rem; font-weight: 500;">
                <li class="breadcrumb-item text-muted">Technical</li>
                <li class="breadcrumb-item text-muted">User Interface</li>
                <li class="breadcrumb-item"><a href="<?= base_url('menu') ?>" class="text-decoration-none text-muted">Menu Items</a></li>
                <li class="breadcrumb-item active fw-bold text-dark" aria-current="page"><?= htmlspecialchars($menu_info->nama_menu) ?></li>
            </ol>
            <span class="badge bg-light text-secondary border border-secondary ms-2" style="font-size: 0.8rem; padding: 4px 8px;" title="URL Induk: /<?= htmlspecialchars($menu_info->link) ?>/*">
                <?= number_format($jmlmenu) ?> Submenu
            </span>
        </div>
        
        <!-- Search Bar -->
        <div class="input-group" style="max-width: 450px;">
            <input type="text" class="form-control bg-light border-end-0 border-secondary" id="searchSubmenuInput" placeholder="Search Submenu Name or URL..." style="border-radius: 4px 0 0 4px; box-shadow: none;">
            <span class="input-group-text bg-light border-secondary border-start-0" style="border-radius: 0 4px 4px 0;">
                <i class="bi bi-search text-muted"></i>
            </span>
        </div>
    </div>
    
    <div class="d-flex flex-wrap justify-content-between align-items-center">
        <!-- Primary Actions -->
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-primary fw-bold px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSubmenu" style="border-radius: 4px; font-size: 0.9rem;">
                NEW
            </button>
            <a href="<?= base_url('menu') ?>" class="btn btn-outline-secondary fw-bold px-3 shadow-sm d-flex align-items-center gap-2" style="border-radius: 4px; font-size: 0.9rem;">
                <i class="fas fa-arrow-left"></i> Back to Menus
            </a>
        </div>
    </div>
</div>

<div class="app-content mt-2">
    <div class="container-fluid px-4">
        <!-- Main Table Card -->
        <div class="card animate-up border-0" style="animation-delay: 0.1s; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">

            <div class="table-responsive">
                <table class="table premium-table table-hover mb-0" id="datatable-submenu">
                    <thead>
                        <tr>
                            <th width="40" class="text-center">No</th>
                            <th>Identitas & Akses Role</th>
                            <th>Target URL</th>
                            <th class="text-center" width="80">Urutan</th>
                            <th class="text-center" width="80">Status</th>
                            <th width="100" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($getsubmenu as $dt): ?>
                            <tr>
                                <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                                <td>
                                    <div class="fw-800 text-dark mb-2"><?= $dt->nama_submenu ?></div>
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php 
                                        $active_role_ids = array_column($dt->access_levels ?: [], 'role_id');
                                        foreach ($roles as $r): 
                                            $is_active = in_array($r->role_id, $active_role_ids);
                                            if ($is_active) {
                                                $badge_class = (strpos(strtolower($r->role_name), 'admin') !== false || strpos(strtolower($r->role_name), 'owner') !== false)
                                                    ? 'bg-primary-subtle text-primary border-primary-subtle'
                                                    : 'bg-success-subtle text-success border-success-subtle';
                                            } else {
                                                $badge_class = 'bg-light text-muted border-secondary-subtle opacity-50';
                                            }
                                        ?>
                                            <span class="badge <?= $badge_class ?> border rounded-pill px-2 py-1 role-toggle-badge"
                                                data-menu-id="<?= $dt->id ?>" 
                                                data-role-id="<?= $r->role_id ?>"
                                                data-type="submenu"
                                                style="font-size: 0.6rem; font-weight: 600; text-transform: capitalize; cursor: pointer; transition: all 0.2s;">
                                                <?= strtolower($r->role_name) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td>
                                    <code
                                        class="bg-light text-primary px-2 py-1 rounded small">/<?= $dt->link_submenu ?></code>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-light text-muted border fw-900 rounded-pill px-3"><?= $dt->urutan ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input status-toggle" type="checkbox" role="switch"
                                            data-id="<?= $dt->id ?>" <?= $dt->status == 1 ? 'checked' : '' ?>>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <button type="button" class="btn-action-premium btn-edit-p btn-edit-submenu"
                                            data-id="<?= $dt->id ?>" data-menu_id="<?= $dt->id_menu ?>"
                                            data-nama="<?= $dt->nama_submenu ?>" data-link="<?= $dt->link_submenu ?>"
                                            data-urutan="<?= $dt->urutan ?>"
                                            data-access_levels='<?= json_encode(array_column($dt->access_levels, 'role_id')) ?>'
                                            data-user_access_levels='<?= json_encode(array_column($dt->user_access_levels ?? [], 'user_id')) ?>'
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Submenu">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn-action-premium btn-delete-p btn-delete-submenu"
                                            data-id="<?= $dt->id ?>" data-nama="<?= $dt->nama_submenu ?>"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus Permanen">
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

<!-- Modal Tambah Submenu -->
<div class="modal fade" id="modalTambahSubmenu" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 rounded-4 shadow-lg" method="POST"
            action="<?= base_url('menu/aksi_simpan_submenu') ?>">
            <div class="modal-header border-0 bg-dark text-white p-4">
                <h5 class="modal-title fw-900"><i class="fas fa-folder-plus me-2"></i>Tambah Submenu Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="menu_id" value="<?= $this->uri->segment(3) ?>">
                <div class="mb-3">
                    <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Nama Submenu <span
                            class="text-danger">*</span></label>
                    <input type="text" name="nama_submenu" class="form-control"
                        style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem" required>
                </div>
                <div class="mb-3">
                    <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Tujuan Modul / Halaman (Alamat
                        URL) <span class="text-danger">*</span></label>
                    <select name="link_submenu" class="form-select select2-url" required style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
    <option value="">-- Pilih Tujuan atau Ketik Manual --</option>
    <optgroup label="General & Dashboard">
        <option value="dashboard">Dashboard Utama</option>
        <option value="#"># (Hanya Header/Folder)</option>
    </optgroup>
    <optgroup label="Sales & Penjualan">
        <option value="transaksi/transaksi_belumbayar">Halaman Kasir (Belum Bayar)</option>
        <option value="transaksi/createspk">Buat SPK Produksi</option>
        <option value="transaksi/daftar_piutang">Daftar Piutang</option>
        <option value="penjualan">Rekap Penjualan</option>
        <option value="konsumen/konsumen_list">Database Konsumen</option>
    </optgroup>
    <optgroup label="Produksi & Manufaktur">
        <option value="Wip">Dashboard Produksi / WIP</option>
        <option value="produksi">Layar Operator Produksi</option>
        <option value="manufaktur">Katalog / Manajemen Produk</option>
        <option value="Workcenter/operatorbersama">Workcenter - Operator Bersama</option>
        <option value="pb">Pengambilan Barang (PB)</option>
    </optgroup>
    <optgroup label="Gudang & Inventory">
        <option value="warehouse/stock">Manajemen Stok & Gudang</option>
        <option value="warehouse/dispatch">Mutasi Bahan & Logistik</option>
        <option value="warehouse/materials_jasa">Bahan Baku Produk</option>
    </optgroup>
    <optgroup label="Keuangan & Laporan">
        <option value="laporan/financial_hub">Dashboard Keuangan Utama</option>
        <option value="laporan/pemasukan_harian">Pemasukan Harian</option>
        <option value="laporan/pengeluaran">Pengeluaran Kas</option>
        <option value="laporan/laba_rugi">Laba Rugi (P&L)</option>
        <option value="laporan/mutasi_harian">Mutasi Saldo (Kas/Bank/EDC)</option>
    </optgroup>
    <optgroup label="HR & Karyawan">
        <option value="karyawan">Database Karyawan</option>
        <option value="absensi">Log Absensi Harian</option>
        <option value="karyawan/penggajian">Payroll / Penggajian</option>
        <option value="karyawan/penilaian_kinerja">Kinerja Karyawan</option>
    </optgroup>
    <optgroup label="Pengaturan Sistem">
        <option value="pengaturan">Dashboard Pengaturan & Sistem</option>
        <option value="menu">Manajemen Menu & Submenu</option>
        <option value="pengaturan/user_account">User & Password (Force Login)</option>
        <option value="errorlog">Log Audit / Security</option>
        <option value="backup/index">Backup Database</option>
    </optgroup>
</select>

                    <small class="text-muted mt-1 d-block" style="font-size: 0.7rem;"><i class="fas fa-info-circle"></i>
                        Pilih modul/halaman dari daftar atau ketik manual.</small>
                </div>
                <div class="row g-3">
                    <div class="col-4">
                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Posisi Urutan</label>
                        <input type="number" name="urutan" class="form-control" value="0"
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem">
                    </div>
                    <div class="col-8">
                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Nama Menu Induk</label>
                        <input type="text" class="form-control bg-light" value="<?= $menu_info->nama_menu ?>" disabled
                            style="border-radius: 12px; padding: 0.75rem">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Pemberian Hak Akses (Role) <span
                            class="text-danger">*</span></label>
                    <div class="d-flex flex-wrap gap-2 p-3 bg-light"
                        style="border-radius: 12px; border: 2px solid #f1f5f9;">
                        <?php foreach ($roles as $d): ?>
                            <label class="role-checkbox-card shadow-sm">
                                <input class="form-check-input m-0" type="checkbox" name="access_levels[]"
                                    value="<?= $d->role_id ?>">
                                <span class="fw-bold small text-muted"><?= $d->role_name ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- [NEW] Akses Per-User -->
                <div class="mt-4">
                    <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Pemberian Hak Akses (Individual User)</label>
                    <div class="p-3 bg-light" style="border-radius: 12px; border: 2px solid #f1f5f9;">
                        <select name="user_access_levels[]" class="form-select user-select2" multiple="multiple" style="width: 100%;">
                            <?php if(!empty($all_users)): foreach($all_users as $u): ?>
                                <option value="<?= $u->user_id ?>"><?= $u->username ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                        <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle"></i> Opsional: Berikan akses khusus untuk user tertentu meskipun rolenya tidak diizinkan.</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-800"
                    style="background: var(--info-gradient); border: none;">Simpan Submenu</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Submenu -->
<div class="modal fade" id="modalEditSubmenu" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formEditSubmenu" class="modal-content border-0 rounded-4 shadow-lg" method="POST"
            action="<?= base_url('menu/aksi_edit_submenu') ?>">
            <div class="modal-header border-0 bg-primary text-white p-4">
                <h5 class="modal-title fw-900"><i class="fas fa-edit me-2"></i>Update Data Submenu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="submenu_id" id="edit_submenu_id">
                <input type="hidden" name="id_menu" id="edit_id_menu">
                <div class="mb-3">
                    <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Nama Submenu</label>
                    <input type="text" name="nama_submenu" id="edit_nama_submenu" class="form-control"
                        style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem" required>
                </div>
                <div class="mb-3">
                    <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Tujuan Modul / Halaman (Alamat
                        URL) <span class="text-danger">*</span></label>
                    <select name="link_submenu" id="edit_link_submenu" class="form-select select2-url" required style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
                        <option value="">-- Pilih Tujuan atau Ketik Manual --</option>
                        <optgroup label="General & Dashboard">
                            <option value="dashboard">Dashboard Utama</option>
                            <option value="#"># (Hanya Header/Folder)</option>
                        </optgroup>
                        <optgroup label="Sales & Penjualan">
                            <option value="transaksi/transaksi_belumbayar">Halaman Kasir (Belum Bayar)</option>
                            <option value="transaksi/createspk">Buat SPK Produksi</option>
                            <option value="transaksi/daftar_piutang">Daftar Piutang</option>
                            <option value="penjualan">Rekap Penjualan</option>
                            <option value="konsumen/konsumen_list">Database Konsumen</option>
                        </optgroup>
                        <optgroup label="Produksi & Manufaktur">
                            <option value="Wip">Dashboard Produksi / WIP</option>
                            <option value="produksi">Layar Operator Produksi</option>
                            <option value="manufaktur">Katalog / Manajemen Produk</option>
                            <option value="Workcenter/operatorbersama">Workcenter - Operator Bersama</option>
                            <option value="pb">Pengambilan Barang (PB)</option>
                        </optgroup>
                        <optgroup label="Gudang & Inventory">
                            <option value="warehouse/stock">Manajemen Stok & Gudang</option>
                            <option value="warehouse/dispatch">Mutasi Bahan & Logistik</option>
                            <option value="warehouse/materials_jasa">Bahan Baku Produk</option>
                        </optgroup>
                        <optgroup label="Keuangan & Laporan">
                            <option value="laporan/financial_hub">Dashboard Keuangan Utama</option>
                            <option value="laporan/pemasukan_harian">Pemasukan Harian</option>
                            <option value="laporan/pengeluaran">Pengeluaran Kas</option>
                            <option value="laporan/laba_rugi">Laba Rugi (P&L)</option>
                            <option value="laporan/mutasi_harian">Mutasi Saldo (Kas/Bank/EDC)</option>
                        </optgroup>
                        <optgroup label="HR & Karyawan">
                            <option value="karyawan">Database Karyawan</option>
                            <option value="absensi">Log Absensi Harian</option>
                            <option value="karyawan/penggajian">Payroll / Penggajian</option>
                            <option value="karyawan/penilaian_kinerja">Kinerja Karyawan</option>
                        </optgroup>
                        <optgroup label="Pengaturan Sistem">
                            <option value="pengaturan">Dashboard Pengaturan & Sistem</option>
                            <option value="menu">Manajemen Menu & Submenu</option>
                            <option value="pengaturan/user_account">User & Password (Force Login)</option>
                            <option value="errorlog">Log Audit / Security</option>
                            <option value="backup/index">Backup Database</option>
                        </optgroup>
                    </select>
                    <small class="text-muted mt-1 d-block" style="font-size: 0.7rem;"><i class="fas fa-info-circle"></i>
                        Ketik untuk mencari modul/halaman sistem, atau ketik alamat URL manual.</small>
                </div>
                <div class="mb-3">
                    <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Sorting Order</label>
                    <input type="number" name="urutan" id="edit_urutan" class="form-control"
                        style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem">
                </div>
                <div class="mt-4">
                    <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Pemberian Hak Akses
                        (Role)</label>
                    <div class="d-flex flex-wrap gap-2 p-3 bg-light"
                        style="border-radius: 12px; border: 2px solid #f1f5f9;">
                        <?php foreach ($roles as $d): ?>
                            <label class="role-checkbox-card shadow-sm">
                                <input class="form-check-input m-0 edit-role-checkbox" type="checkbox"
                                    name="access_levels[]" value="<?= $d->role_id ?>" id="edit_role_<?= $d->role_id ?>">
                                <span class="fw-bold small text-muted"><?= $d->role_name ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- [NEW] Akses Per-User Edit -->
                <div class="mt-4">
                    <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Pemberian Hak Akses (Individual User)</label>
                    <div class="p-3 bg-light" style="border-radius: 12px; border: 2px solid #f1f5f9;">
                        <select name="user_access_levels[]" id="edit_user_access_levels" class="form-select user-select2" multiple="multiple" style="width: 100%;">
                            <?php if(!empty($all_users)): foreach($all_users as $u): ?>
                                <option value="<?= $u->user_id ?>"><?= $u->username ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                        <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle"></i> Opsional: Berikan akses khusus untuk user tertentu meskipun rolenya tidak diizinkan.</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-800 shadow">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Datalist untuk Autocomplete Modul ERP -->
<datalist id="listModulERP">
    <option value="dashboard">Dashboard Overview</option>
    <option value="transaksi">Transaksi & SPK Produksi</option>
    <option value="transaksi/daftar_piutang">Penerimaan Piutang Konsumen</option>
    <option value="laporan/pemasukan_harian">Laporan Kasir Harian</option>
    <option value="laporan/pengeluaran">Input Pengeluaran Kas</option>
    <option value="gudang">Manajemen Gudang & Stok</option>
    <option value="inventori">Inventaris Barang Jadi</option>
    <option value="bahan">Manajemen Bahan Baku</option>
    <option value="manufaktur">Alur & Status Produksi</option>
    <option value="konsumen">Database Pelanggan / Konsumen</option>
    <option value="karyawan">Data Karyawan & HR</option>
    <option value="penjualan_manual">Penjualan Langsung (Manual)</option>
    <option value="KasirShift">Rekonsiliasi / Tutup Shift</option>
    <option value="pengaturan">Pengaturan Sistem Umum</option>
    <option value="menu">Konfigurasi Struktur Menu</option>
</datalist>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11">
        // Initialize Select2 for URL with Tags
        if($.fn.select2) {
            $('.select2-url').select2({
                theme: 'bootstrap-5',
                tags: true,
                placeholder: '-- Pilih Tujuan atau Ketik Manual --',
                dropdownParent: function() {
                    return $(this).closest('.modal');
                }
            });
        }
    </script>
<!-- [NEW] Select2 for User Selection -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js">
        // Initialize Select2 for URL with Tags
        if($.fn.select2) {
            $('.select2-url').select2({
                theme: 'bootstrap-5',
                tags: true,
                placeholder: '-- Pilih Tujuan atau Ketik Manual --',
                dropdownParent: function() {
                    return $(this).closest('.modal');
                }
            });
        }
    </script>

<script>
    let csrfName = '<?= $this->security->get_csrf_token_name() ?>';
    let csrfHash = '<?= $this->security->get_csrf_hash() ?>';

    $(document).ready(function () {
        // Init Select2
        if ($('.user-select2').length) {
            $('.user-select2').select2({
                placeholder: "Pilih User...",
                allowClear: true,
                dropdownParent: $('#modalTambahSubmenu') // Will update dynamically
            });
            // Fix Select2 in modals
            $('#modalTambahSubmenu').on('shown.bs.modal', function () {
                $(this).find('.user-select2').select2({ dropdownParent: $(this) });
            });
            $('#modalEditSubmenu').on('shown.bs.modal', function () {
                $(this).find('.user-select2').select2({ dropdownParent: $(this) });
            });
        }

        // Inisiasi auto tooltips bootstrap
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        if ($.fn.DataTable) {
            var tableSubmenu = $('#datatable-submenu').DataTable({
                "language": { "sSearch": "Cari Submenu:", "oPaginate": { "sNext": "<i class='fas fa-chevron-right'></i>", "sPrevious": "<i class='fas fa-chevron-left'></i>" } },
                "pageLength": 10,
                "dom": "<'row px-4 py-3'<'col-md-6'l><'col-md-6 d-none justify-content-end text-end'f>>t<'row px-4 py-3'<'col-md-6'i><'col-md-6 d-flex justify-content-end text-end'p>>"
            });
            
            // Bind custom search input to datatable
            $('#searchSubmenuInput').on('keyup', function () {
                tableSubmenu.search(this.value).draw();
            });
        }

        // Toggle Status
        $(document).on('change', '.status-toggle', function () {
            const id = $(this).data('id');
            const status = $(this).is(':checked') ? 1 : 0;

            let payload = { id: id, status: status };
            payload[csrfName] = csrfHash;

            $.ajax({
                url: '<?= base_url('menu/actionToggleSubmenuStatus') ?>',
                type: 'POST',
                data: payload,
                dataType: 'json',
                success: function (res) {
                    if (res.csrf_name && res.csrf_hash) {
                        csrfName = res.csrf_name;
                        csrfHash = res.csrf_hash;
                    }
                    if (res.status === 'success') {
                        const toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        toast.fire({ icon: 'success', title: res.pesan });
                    } else {
                        Swal.fire('Error', res.pesan, 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Gagal menghubungi server', 'error');
                }
            });
        });

        // Edit Submenu
        $(document).on('click', '.btn-edit-submenu', function () {
            $('#edit_submenu_id').val($(this).data('id'));
            $('#edit_id_menu').val($(this).data('menu_id'));
            $('#edit_nama_submenu').val($(this).data('nama'));
            var val = $(this).data('link');
            if ($('#edit_link_submenu').find("option[value='" + val + "']").length) {
                $('#edit_link_submenu').val(val).trigger('change');
            } else { 
                var newOption = new Option(val, val, true, true);
                $('#edit_link_submenu').append(newOption).trigger('change');
            } 
            $('#edit_urutan').val($(this).data('urutan'));

            let accessLevels = $(this).data('access_levels');
            try {
                if (typeof accessLevels === 'string') accessLevels = JSON.parse(accessLevels);
            } catch (e) { accessLevels = []; }

            // Reset checklist pada form edit
            $('.edit-role-checkbox').prop('checked', false);
            // Centang sesuai dengan yang sudah tersimpan
            if (Array.isArray(accessLevels)) {
                accessLevels.forEach(function (lvl) {
                    $('#edit_role_' + lvl).prop('checked', true);
                });
            }

            // [NEW] Set User Access Levels
            let userAccessLevels = $(this).data('user_access_levels');
            try {
                if (typeof userAccessLevels === 'string') userAccessLevels = JSON.parse(userAccessLevels);
            } catch (e) { userAccessLevels = []; }
            
            if (Array.isArray(userAccessLevels)) {
                $('#edit_user_access_levels').val(userAccessLevels).trigger('change');
            } else {
                $('#edit_user_access_levels').val(null).trigger('change');
            }

            $('#modalEditSubmenu').modal('show');
        });

        // Delete Submenu
        $(document).on('click', '.btn-delete-submenu', function () {
            const id = $(this).data('id');
            const nama = $(this).data('nama');

            Swal.fire({
                title: 'Hapus Submenu?',
                text: `Yakin ingin menghapus submenu "${nama}"? Data akan terhapus secara permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f72585',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "<?= base_url('menu/delete_submenu/') ?>" + id;
                }
            });
        });

        // TOGGLE ROLE BADGE INLINE
        $(document).on('click', '.role-toggle-badge', function () {
            const badge = $(this);
            const menuId = badge.data('menu-id');
            const roleId = badge.data('role-id');
            const type = badge.data('type');
            const roleName = badge.text().trim().toLowerCase();

            const isCurrentlyActive = !badge.hasClass('bg-light');
            
            const adminClasses = 'bg-primary-subtle text-primary border-primary-subtle';
            const userClasses = 'bg-success-subtle text-success border-success-subtle';
            const inactiveClasses = 'bg-light text-muted border-secondary-subtle opacity-50';

            // Optimistic Update
            if (isCurrentlyActive) {
                badge.removeClass(adminClasses + ' ' + userClasses).addClass(inactiveClasses);
            } else {
                badge.removeClass(inactiveClasses).addClass((roleName.includes('admin') || roleName.includes('owner')) ? adminClasses : userClasses);
            }

            let payload = {
                type: type,
                menu_id: menuId,
                role_id: roleId
            };
            payload[csrfName] = csrfHash;

            $.ajax({
                url: "<?= base_url('menu/actionToggleRole') ?>",
                type: "POST",
                data: payload,
                dataType: "JSON",
                success: function (res) {
                    if (res.csrf_name && res.csrf_hash) {
                        csrfName = res.csrf_name;
                        csrfHash = res.csrf_hash;
                    }
                    if (res.status !== 'success') {
                        location.reload();
                    }
                },
                error: function () {
                    location.reload();
                }
            });
        });

    });

        // Initialize Select2 for URL with Tags
        if($.fn.select2) {
            $('.select2-url').select2({
                theme: 'bootstrap-5',
                tags: true,
                placeholder: '-- Pilih Tujuan atau Ketik Manual --',
                dropdownParent: function() {
                    return $(this).closest('.modal');
                }
            });
        }
    </script></div>
