<?php $this->load->view('layout_admin/header_modul'); ?>
<style>
    /* Styling khusus UX Role Checkbox */
    .role-checkbox-card {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.5rem 0.8rem;
        cursor: pointer;
        transition: all 0.2s;
        margin-bottom: 0;
        display: flex;
        align-items: center;
        gap: 8px;
        background: white;
    }

    .role-checkbox-card:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }

    .role-checkbox-card input:checked+span {
        color: #0f172a !important;
        font-weight: 800 !important;
    }

    /* Mengubah warna border jika dicentang (didukung browser modern) */
    .role-checkbox-card:has(input:checked) {
        border-color: #3b82f6;
        background: #eff6ff;
    }

    /* Style untuk button duplikat */
    .btn-duplicate-p {
        background: #fef9c3;
        color: #854d0e;
        border: 1px solid #fde047;
    }

    .btn-duplicate-p:hover {
        background: #fde047;
        color: #713f12;
    }
</style>


<div class="app-content mt-2">
    <div class="container-fluid">
        <!-- Quick Stats -->
        <div class="row g-3 mb-4 animate-up">
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--primary-gradient)"><i
                            class="fas fa-list-check"></i></div>
                    <div>
                        <div class="stat-v2-label">Total Menu Utama</div>
                        <div class="stat-v2-value"><?= number_format($jmlmenu) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--success-gradient)"><i
                            class="fas fa-check-circle"></i></div>
                    <div>
                        <div class="stat-v2-label">Menu Aktif</div>
                        <div class="stat-v2-value"><?= $active_menu ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--info-gradient)"><i
                            class="fas fa-link"></i></div>
                    <div>
                        <div class="stat-v2-label">Total Submenu</div>
                        <div class="stat-v2-value"><?= number_format($total_submenu) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm d-flex flex-row align-items-center justify-content-center">
                    <button class="btn btn-primary rounded-pill px-4 fw-800 w-100 h-100"
                        style="background: var(--primary-gradient); border: none;" data-bs-toggle="modal"
                        data-bs-target="#modalTambahMenu">
                        <i class="fas fa-plus-circle me-2"></i> Tambah Menu
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="report-section-card animate-up" style="animation-delay: 0.1s">
            <div class="section-header">
                <h5><i class="fas fa-bars"></i> Manajemen Menu Utama</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-white text-dark rounded-pill px-3 fw-bold shadow-sm"
                        onclick="exportToExcel()">
                        <i class="fas fa-file-excel text-success me-1"></i> Excel
                    </button>
                    <button class="btn btn-sm btn-white text-dark rounded-pill px-3 fw-bold shadow-sm"
                        onclick="printTable()">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                </div>
            </div>

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
                                    <?php if (!empty($dt->access_levels)): ?>
                                        <div class="d-flex flex-wrap gap-1">
                                                <?php foreach ($dt->access_levels as $lvl):
                                                    // Logika warna badge: Biru untuk role utama/admin, Hijau untuk operasional
                                                    $badge_class = (strpos(strtolower($lvl->role_name), 'admin') !== false || strpos(strtolower($lvl->role_name), 'owner') !== false)
                                                        ? 'bg-primary-subtle text-primary border-primary-subtle'
                                                        : 'bg-success-subtle text-success border-success-subtle';
                                                    ?>
                                                    <span class="badge <?= $badge_class ?> border rounded-pill px-2 py-1"
                                                        style="font-size: 0.6rem; font-weight: 600; text-transform: capitalize;">
                                                        <?= strtolower($lvl->role_name) ?>
                                                    </span>
                                                <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1"
                                            style="font-size: 0.6rem; font-weight: 600;">Belum Ada Akses</span>
                                    <?php endif; ?>
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
                        <input type="text" name="link" class="form-control" list="listModulERP"
                            placeholder="Contoh: transaksi"
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                            required>
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
                        <input type="text" name="link" id="edit_link" class="form-control" list="listModulERP"
                            style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                            required>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        // Inisiasi auto tooltips bootstrap untuk action button
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        if ($.fn.DataTable) {
            $('#datatable-menu').DataTable({
                "language": { "sSearch": "Cari Menu:", "oPaginate": { "sNext": "<i class='fas fa-chevron-right'></i>", "sPrevious": "<i class='fas fa-chevron-left'></i>" } },
                "pageLength": 10,
                "dom": "<'row px-4 py-3'<'col-md-6'l><'col-md-6 d-flex justify-content-end text-end'f>>t<'row px-4 py-3'<'col-md-6'i><'col-md-6 d-flex justify-content-end text-end'p>>"
            });
        }

        // Toggle Status Menu
        $(document).on('change', '.status-toggle-menu', function () {
            const self = $(this);
            const id = self.data('id');
            const status = self.is(':checked') ? 1 : 0;

            $.ajax({
                url: '<?= base_url('menu/actionToggleMenuStatus') ?>',
                type: 'POST',
                data: {
                    id: id,
                    status: status,
                    '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
                },
                dataType: 'json',
                success: function (res) {
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
            $('#edit_link').val($(this).data('link'));
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
            $('input[name="link"]').val($(this).data('link'));
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
                    $.ajax({
                        url: '<?= base_url('menu/actionBulkToggleMenuStatus') ?>',
                        type: 'POST',
                        data: {
                            ids: selectedIds,
                            status: status,
                            '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
                        },
                        dataType: 'json',
                        success: function (res) {
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
</script>