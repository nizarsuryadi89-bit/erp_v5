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
                <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">Menu Items</li>
            </ol>
            <span class="badge bg-light text-secondary border border-secondary ms-2" style="font-size: 0.8rem; padding: 4px 8px;">
                <?= number_format($jmlmenu) ?> Total
            </span>
        </div>
        
        <!-- Search Bar -->
        <div class="input-group" style="max-width: 450px;">
            <input type="text" class="form-control bg-light border-end-0 border-secondary" id="searchMenuInput" placeholder="Search Menu Name or URL..." style="border-radius: 4px 0 0 4px; box-shadow: none;">
            <span class="input-group-text bg-light border-secondary border-start-0" style="border-radius: 0 4px 4px 0;">
                <i class="bi bi-search text-muted"></i>
            </span>
        </div>
    </div>
    
    <div class="d-flex flex-wrap justify-content-between align-items-center">
        <!-- Primary Actions -->
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-primary fw-bold px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahMenu" style="border-radius: 4px; font-size: 0.9rem;">
                NEW
            </button>
            <button class="btn btn-outline-secondary fw-bold px-3 shadow-sm d-flex align-items-center gap-2" onclick="exportToExcel()" style="border-radius: 4px; font-size: 0.9rem;">
                <i class="fas fa-file-excel text-success"></i> Export
            </button>
        </div>
    </div>
</div>

<div class="app-content mt-2">
    <div class="container-fluid px-4">
        <!-- Main Table Card -->
        <div class="card animate-up border-0" style="animation-delay: 0.1s; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">

            <div class="px-4 py-2 border-bottom bg-light d-flex justify-content-between align-items-center">
                <div class="d-flex gap-2 align-items-center">
                    <span class="small text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Aksi Massal:</span>
                    <button type="button" class="btn btn-xs btn-outline-success rounded-pill fw-bold" id="btn-bulk-activate" style="font-size: 0.6rem;">
                        <i class="fas fa-check-circle me-1"></i> Aktifkan
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-danger rounded-pill fw-bold" id="btn-bulk-deactivate" style="font-size: 0.6rem;">
                        <i class="fas fa-times-circle me-1"></i> Matikan
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table premium-table table-hover mb-0" id="datatable-menu">
                    <thead>
                        <tr>
                            <th width="40" class="text-center">
                                <input class="form-check-input" type="checkbox" id="check-all-menu">
                            </th>
                            <th width="50" class="text-center">No</th>
                            <th>Nama Menu</th>
                            <th>Target URL & Urutan</th>
                            <th>Hak Akses (Role)</th>
                            <th class="text-center" width="80">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($tampil_menu as $dt): ?>
                            <tr>
                                <td class="text-center">
                                    <input class="form-check-input check-menu-item" type="checkbox" value="<?= $dt->id ?>">
                                </td>
                                <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="stat-v2-icon bg-light text-dark shadow-sm"
                                            style="width: 36px; height: 36px; border-radius: 8px;">
                                            <i class="<?= $dt->icon ?>"></i>
                                        </div>
                                        <div>
                                            <div class="fw-800 text-dark"><?= $dt->nama_menu ?></div>
                                            <div class="small text-muted text-uppercase fw-bold"
                                                style="font-size: 0.6rem; letter-spacing: 1px;"><?= $dt->alias ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="badge bg-light text-dark border font-monospace mb-1"
                                        style="font-size: 0.75rem;">/<?= $dt->link ?></div>
                                    <div class="small text-muted"><i class="fas fa-sort-amount-down me-1"></i> Order:
                                        <?= $dt->urutan ?>
                                    </div>
                                </td>
                                <td>
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
                                                data-type="menu"
                                                style="font-size: 0.6rem; font-weight: 600; text-transform: capitalize; cursor: pointer; transition: all 0.2s;">
                                                <?= strtolower($r->role_name) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input status-toggle-menu" type="checkbox" role="switch"
                                            data-id="<?= $dt->id ?>" <?= $dt->status == 1 ? 'checked' : '' ?>>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <a href="<?= base_url('menu/submenu/' . $dt->id) ?>"
                                            class="btn-submenu-p me-2 d-flex align-items-center">
                                            <i class="fas fa-layer-group me-1"></i> Submenu
                                        </a>
                                        <button type="button"
                                            class="btn-action-premium btn-duplicate-p btn-duplicate-menu me-2"
                                            data-id="<?= $dt->id ?>" data-nama_menu="<?= $dt->nama_menu ?>"
                                            data-alias="<?= $dt->alias ?>" data-icon="<?= $dt->icon ?>"
                                            data-link="<?= $dt->link ?>" data-urutan="<?= $dt->urutan ?>"
                                            data-access_levels='<?= json_encode(array_column($dt->access_levels ?: [], 'role_id')) ?>'
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Duplikat Menu">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        <button type="button" class="btn-action-premium btn-edit-p btn-edit-menu"
                                            data-id="<?= $dt->id ?>" data-nama_menu="<?= $dt->nama_menu ?>"
                                            data-alias="<?= $dt->alias ?>" data-icon="<?= $dt->icon ?>"
                                            data-link="<?= $dt->link ?>" data-urutan="<?= $dt->urutan ?>"
                                            data-access_levels='<?= json_encode(array_column($dt->access_levels ?: [], 'role_id')) ?>'
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Data Menu">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn-action-premium btn-delete-p btn-delete-menu"
                                            data-id="<?= $dt->id ?>" data-nama_menu="<?= $dt->nama_menu ?>"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus Menu Permanen">
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

<!-- Modal Tambah Menu -->
<div class="modal fade" id="modalTambahMenu" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="formTambahMenu" class="modal-content border-0 rounded-4 shadow-lg" method="POST"
            action="<?= base_url('menu/aksi_simpan') ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>"
                value="<?= $this->security->get_csrf_hash() ?>">
            <div class="modal-header border-0 bg-dark text-white p-4">
                <h5 class="modal-title fw-900" id="titleModalTambah"><i class="fas fa-plus-circle me-2"></i>Tambah Menu
                    Utama</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Nama Menu <span
                                class="text-danger">*</span></label>
                        <input type="text" name="nama_menu" class="form-control"
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                            required>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Alias (Nama Pendek/Judul)
                            <span class="text-danger">*</span></label>
                        <input type="text" name="alias" class="form-control"
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                            required>
                    </div>
                    <div class="col-md-12 mb-3"> <!-- Change to full width or suitable if we use select2 -->
                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Pilih Ikon Menu
                            <span class="text-danger">*</span></label>
                        <input type="hidden" name="icon" id="input_icon_add" value="bi-circle" required>
                        <div class="icon-grid-picker bg-light p-3"
                            style="border-radius: 12px; border: 2px solid #f1f5f9; display: grid; gap: 10px; grid-template-columns: repeat(auto-fill, minmax(50px, 1fr)); max-height: 200px; overflow-y: auto;">
                            <?php
                            $icons = [
                                'bi-house-door',
                                'bi-speedometer2',
                                'bi-grid-1x2-fill',
                                'bi-people',
                                'bi-person-badge',
                                'bi-cart-fill',
                                'bi-receipt',
                                'bi-cash-coin',
                                'bi-gear-wide-connected',
                                'bi-hammer',
                                'bi-tools',
                                'bi-clock-history',
                                'bi-box-seam',
                                'bi-archive',
                                'bi-stack',
                                'bi-truck',
                                'bi-wallet2',
                                'bi-bank',
                                'bi-currency-dollar',
                                'bi-calculator',
                                'bi-bar-chart-line',
                                'bi-journal-text',
                                'bi-file-earmark-bar-graph',
                                'bi-pie-chart',
                                'bi-sliders',
                                'bi-shield-lock',
                                'bi-person-gear',
                                'bi-database',
                                // Ekstra ikon populer
                                'bi-globe',
                                'bi-geo-alt',
                                'bi-heart',
                                'bi-envelope',
                                'bi-youtube',
                                'bi-instagram',
                                'bi-star',
                                'bi-bookmark',
                                'bi-bell',
                                'bi-tag',
                                'bi-chat-dots',
                                'bi-camera'
                            ];
                            foreach ($icons as $ic): ?>
                                <button type="button" class="btn btn-white border shadow-sm icon-btn-select"
                                    data-target="#input_icon_add" data-icon="<?= $ic ?>" title="<?= $ic ?>"
                                    style="height: 50px; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                                    <i class="bi <?= $ic ?> fs-5"></i>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Tujuan Modul (Alamat URL)
                            <span class="text-danger">*</span></label>
                        <select name="link" class="form-select select2-url" required style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
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

                        <small class="text-muted mt-1 d-block" style="font-size: 0.7rem;"><i
                                class="fas fa-info-circle"></i> Pilih modul dari daftar atau ketik manual.</small>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Posisi Urutan</label>
                        <input type="number" name="urutan" value="0" class="form-control"
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
                    </div>
                    <div class="col-12">
                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Pemberian Hak Akses (Role)
                            <span class="text-danger">*</span></label>
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
                        <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle me-1"></i> Klik untuk
                            mencentang satu atau lebih departemen yang diizinkan mengakses menu ini.</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                    data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow"
                    style="background: var(--primary-gradient)">Simpan Menu</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Menu -->
<div class="modal fade" id="modalEditMenu" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="formEditMenu" class="modal-content border-0 rounded-4 shadow-lg" method="POST"
            action="<?= base_url('menu/aksi_update') ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>"
                value="<?= $this->security->get_csrf_hash() ?>">
            <div class="modal-header border-0 bg-dark text-white p-4">
                <h5 class="modal-title fw-900"><i class="fas fa-edit me-2"></i>Update Konfigurasi Menu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="id" id="edit_menu_id">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Nama Menu <span
                                class="text-danger">*</span></label>
                        <input type="text" name="nama_menu" id="edit_nama_menu" class="form-control"
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                            required>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Alias (Nama Pendek/Judul)
                            <span class="text-danger">*</span></label>
                        <input type="text" name="alias" id="edit_alias" class="form-control"
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                            required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Pilih Ikon Menu
                            <span class="text-danger">*</span></label>
                        <input type="hidden" name="icon" id="edit_icon_input" value="bi-circle" required>
                        <div class="icon-grid-picker bg-light p-3"
                            style="border-radius: 12px; border: 2px solid #f1f5f9; display: grid; gap: 10px; grid-template-columns: repeat(auto-fill, minmax(50px, 1fr)); max-height: 200px; overflow-y: auto;">
                            <?php foreach ($icons as $ic): ?>
                                <button type="button" class="btn btn-white border shadow-sm icon-btn-select"
                                    data-target="#edit_icon_input" data-icon="<?= $ic ?>" title="<?= $ic ?>"
                                    style="height: 50px; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                                    <i class="bi <?= $ic ?> fs-5"></i>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Tujuan Modul (Alamat URL)
                            <span class="text-danger">*</span></label>
                        <select name="link" id="edit_link" class="form-select select2-url" required style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
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

                        <small class="text-muted mt-1 d-block" style="font-size: 0.7rem;"><i
                                class="fas fa-info-circle"></i> Ketik untuk mencari modul sistem.</small>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Posisi Urutan</label>
                        <input type="number" name="urutan" id="edit_urutan" class="form-control"
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
                    </div>
                    <div class="col-12">
                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Pemberian Hak Akses (Role)
                            <span class="text-danger">*</span></label>
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
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                    data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow"
                    style="background: var(--primary-gradient)">Update Konfigurasi</button>
            </div>
        </form>
    </div>
</div>

<!-- Datalist untuk Autocomplete Modul ERP -->
<datalist id="listModulERP">
    <option value="dashboard">Dashboard Overview</option>
    <option value="transaksi">Transaksi & SPK Produksi</option>
    <option value="pengaturan/konsumen_list">Penerimaan piutang / Database Konsumen</option>
    <option value="laporan/pemasukan_harian">Laporan Kasir Harian</option>
    <option value="laporan/pengeluaran">Input Pengeluaran Kas</option>
    <option value="gudang">Manajemen Gudang & Stok</option>
    <option value="inventori">Inventaris Barang Jadi</option>
    <option value="pengaturan/satuan">Manajemen Satuan Produk</option>
    <option value="pengaturan/kategori">Kategori & Form Produk</option>
    <option value="pengaturan/supplier">Master Data Supplier</option>
    <option value="bahan">Manajemen Bahan Baku</option>
    <option value="manufaktur">Alur & Status Produksi</option>
    <option value="pengaturan/konsumen_list">Database Pelanggan / Konsumen</option>
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
<script>
    let csrfName = '<?= $this->security->get_csrf_token_name() ?>';
    let csrfHash = '<?= $this->security->get_csrf_hash() ?>';

    $(document).ready(function () {
        // Inisiasi auto tooltips bootstrap untuk action button
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        if ($.fn.DataTable) {
            var tableMenu = $('#datatable-menu').DataTable({
                "language": { "sSearch": "Cari Menu:", "oPaginate": { "sNext": "<i class='fas fa-chevron-right'></i>", "sPrevious": "<i class='fas fa-chevron-left'></i>" } },
                "pageLength": 10,
                "dom": "<'row px-4 py-3'<'col-md-6'l><'col-md-6 d-none justify-content-end text-end'f>>t<'row px-4 py-3'<'col-md-6'i><'col-md-6 d-flex justify-content-end text-end'p>>"
            });
            
            // Bind custom search input to datatable
            $('#searchMenuInput').on('keyup', function () {
                tableMenu.search(this.value).draw();
            });
        }

        // Toggle Status Menu
        $(document).on('change', '.status-toggle-menu', function () {
            const self = $(this);
            const id = self.data('id');
            const status = self.is(':checked') ? 1 : 0;

            let payload = {
                id: id,
                status: status
            };
            payload[csrfName] = csrfHash;

            $.ajax({
                url: '<?= base_url('menu/actionToggleMenuStatus') ?>',
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
                        self.prop('checked', !status);
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Gagal menghubungi server', 'error');
                    self.prop('checked', !status);
                }
            });
        });

        // Edit button click
        $(document).on('click', '.btn-edit-menu', function () {
            $('#edit_menu_id').val($(this).data('id'));
            $('#edit_nama_menu').val($(this).data('nama_menu'));
            $('#edit_alias').val($(this).data('alias'));
            $('#edit_icon_input').val($(this).data('icon')).trigger('change');
            var val = $(this).data('link');
            if ($('#edit_link').find("option[value='" + val + "']").length) {
                $('#edit_link').val(val).trigger('change');
            } else { 
                var newOption = new Option(val, val, true, true);
                $('#edit_link').append(newOption).trigger('change');
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

            $('#modalEditMenu').modal('show');
        });

        // Duplicate button click
        $(document).on('click', '.btn-duplicate-menu', function () {
            // Reset form first
            $('#formTambahMenu')[0].reset();
            $('.role-checkbox-card input').prop('checked', false);

            $('#titleModalTambah').html('<i class="fas fa-copy me-2"></i>Duplikasi Menu Utama');

            // Fill inputs
            $('input[name="nama_menu"]').val($(this).data('nama_menu') + ' (Copy)');
            $('input[name="alias"]').val($(this).data('alias'));
            $('#input_icon_add').val($(this).data('icon')).trigger('change');
            var val = $(this).data('link');
            if ($('select[name="link"]').find("option[value='" + val + "']").length) {
                $('select[name="link"]').val(val).trigger('change');
            } else { 
                var newOption = new Option(val, val, true, true);
                $('select[name="link"]').append(newOption).trigger('change');
            }
            $('input[name="urutan"]').val($(this).data('urutan'));

            let accessLevels = $(this).data('access_levels');
            try {
                if (typeof accessLevels === 'string') accessLevels = JSON.parse(accessLevels);
            } catch (e) { accessLevels = []; }

            // Check roles
            if (Array.isArray(accessLevels)) {
                accessLevels.forEach(function (lvl) {
                    $('#modalTambahMenu input[value="' + lvl + '"]').prop('checked', true);
                });
            }

            $('#modalTambahMenu').modal('show');
        });

        // Reset title when modal add is closed
        $('#modalTambahMenu').on('hidden.bs.modal', function () {
            $('#titleModalTambah').html('<i class="fas fa-plus-circle me-2"></i>Tambah Menu Utama');
            $('#formTambahMenu')[0].reset();
            $('.role-checkbox-card input').prop('checked', false);
        });

        // Delete button click
        $(document).on('click', '.btn-delete-menu', function () {
            const id = $(this).data('id');
            const nama = $(this).data('nama_menu');

            Swal.fire({
                title: 'Hapus Menu?',
                text: `Menghapus menu "${nama}" akan menghapus semua submenu dan akses terkait. Lanjutkan?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f72585',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?= base_url('menu/actionDeleteMenu/') ?>" + id,
                        type: "GET",
                        dataType: "JSON",
                        success: function (res) {
                            if (res.status == 'success') {
                                Swal.fire('Berhasil!', res.pesan, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Gagal!', res.pesan, 'error');
                            }
                        }
                    });
                }
            });
        });

        // ICON PICKER GRID LOGIC

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
        $(document).on('click', '.icon-btn-select', function () {
            let target = $(this).data('target');
            $(target).val($(this).data('icon')).trigger('change');

            // Visual feedback
            let container = $(this).closest('.icon-grid-picker');
            container.find('.icon-btn-select').removeClass('btn-primary text-white border-primary').addClass('btn-white text-dark border');
            $(this).removeClass('btn-white text-dark border').addClass('btn-primary text-white border-primary');
        });

        function syncIconGrid(targetId) {
            let val = $(targetId).val();
            let container = $(targetId).siblings('.icon-grid-picker');
            container.find('.icon-btn-select').removeClass('btn-primary text-white border-primary').addClass('btn-white text-dark border');
            if (val) {
                container.find('.icon-btn-select[data-icon="' + val + '"]').removeClass('btn-white text-dark border').addClass('btn-primary text-white border-primary');
            }
        }

        $('#input_icon_add').on('change', function () { syncIconGrid('#input_icon_add'); });
        $('#edit_icon_input').on('change', function () { syncIconGrid('#edit_icon_input'); });
        // Check All Menu Logic
        $('#check-all-menu').on('change', function () {
            $('.check-menu-item').prop('checked', $(this).is(':checked'));
        });

        // Bulk Action (Activate/Deactivate)
        $('#btn-bulk-activate, #btn-bulk-deactivate').on('click', function () {
            const isActivate = $(this).attr('id') === 'btn-bulk-activate';
            const status = isActivate ? 1 : 0;
            const selectedIds = $('.check-menu-item:checked').map(function () {
                return $(this).val();
            }).get();

            if (selectedIds.length === 0) {
                Swal.fire('Info', 'Pilih minimal satu menu terlebih dahulu', 'info');
                return;
            }

            Swal.fire({
                title: isActivate ? 'Aktifkan Massal?' : 'Matikan Massal?',
                text: `Anda akan mengubah status ${selectedIds.length} menu sekaligus. Lanjutkan?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: isActivate ? '#28a745' : '#dc3545',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    let payload = {
                        ids: selectedIds,
                        status: status
                    };
                    payload[csrfName] = csrfHash;

                    $.ajax({
                        url: '<?= base_url('menu/actionBulkToggleMenuStatus') ?>',
                        type: 'POST',
                        data: payload,
                        dataType: 'json',
                        success: function (res) {
                            if (res.csrf_name && res.csrf_hash) {
                                csrfName = res.csrf_name;
                                csrfHash = res.csrf_hash;
                            }
                            if (res.status === 'success') {
                                Swal.fire('Berhasil!', res.pesan, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Error', res.pesan, 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error', 'Gagal menghubungi server', 'error');
                        }
                    });
                }
            });
        });
    });

    function exportToExcel() {
        let table = document.getElementById("datatable-menu");
        let html = table.outerHTML;
        let a = document.createElement('a');
        a.href = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
        a.download = 'Konfigurasi_Menu_' + new Date().toISOString().slice(0, 10) + '.xls';
        a.click();
    }

    function printTable() {
        window.print();
    }

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
