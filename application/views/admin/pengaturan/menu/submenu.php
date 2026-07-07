<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    /* ==========================================================================
    PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - SUBMENU MANAGEMENT
    ========================================================================== */
    :root {
        --primary-gradient: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        --success-gradient: linear-gradient(135deg, #059669 0%, #10b981 100%);
        --info-gradient: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    /* Stats Cards */
    .summary-stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
    }

    .stat-v2-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .stat-v2-label {
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.6rem;
        letter-spacing: 1px;
    }

    .stat-v2-value {
        font-size: 1.5rem;
        font-weight: 900;
        color: #1a1c23;
        line-height: 1;
    }

    /* Main Table Section */
    .report-section-card {
        background: white;
        border-radius: 24px;
        border: none;
        box-shadow: var(--premium-shadow);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .section-header {
        background: var(--primary-gradient);
        padding: 1.25rem 1.75rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
    }

    .section-header h5 {
        margin: 0;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .premium-table thead th {
        background: #f8fafc;
        border: none;
        color: #475569;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.65rem;
        letter-spacing: 1px;
        padding: 1.25rem 1.5rem;
    }

    .premium-table tbody td {
        padding: 1.25rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }

    /* Pill Badges */
    .badge-premium {
        padding: 0.5rem 0.85rem;
        border-radius: 8px;
        font-weight: 800;
        font-size: 0.65rem;
    }

    /* Action Buttons */
    .btn-action-premium {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        border: none;
        margin: 0 2px;
    }

    .btn-edit-p {
        background: #fff3cd;
        color: #856404;
    }

    .btn-delete-p {
        background: #fee2e2;
        color: #b91c1c;
    }

    .btn-edit-p:hover {
        background: #ffe69c;
        transform: translateY(-2px);
    }

    .btn-delete-p:hover {
        background: #fecaca;
        transform: translateY(-2px);
    }

    @keyframes slideUpFade {
        from {
            opacity: 0;
            transform: translateY(15px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-up {
        animation: slideUpFade 0.5s ease-out forwards;
    }

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

    .role-checkbox-card:has(input:checked) {
        border-color: #3b82f6;
        background: #eff6ff;
    }
</style>

<div class="app-content mt-2">
    <div class="container-fluid">
        <!-- Breadcrumb & Nav -->
        <div class="d-flex justify-content-between align-items-center mb-4 animate-up">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item small fw-bold"><a href="<?= base_url('menu') ?>"
                            class="text-decoration-none">Konfigurasi Menu</a></li>
                    <li class="breadcrumb-item active small fw-bold" aria-current="page">Manajemen Submenu</li>
                </ol>
            </nav>
            <a href="<?= base_url('menu') ?>" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Parent
            </a>
        </div>

        <!-- Quick Stats Overlay -->
        <div class="row g-3 mb-4 animate-up">
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm h-100">
                    <div class="stat-v2-icon text-white" style="background: var(--primary-gradient)"><i
                            class="fas fa-layer-group"></i></div>
                    <div>
                        <div class="stat-v2-label">Total Submenu</div>
                        <div class="stat-v2-value"><?= number_format($jmlmenu) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm h-100">
                    <div class="stat-v2-icon text-white" style="background: var(--success-gradient)"><i
                            class="fas fa-check-circle"></i></div>
                    <div>
                        <div class="stat-v2-label">Status Aktif</div>
                        <div class="stat-v2-value"><?= $active_submenu ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div
                    class="summary-stat-card shadow-sm h-100 d-flex flex-row align-items-center justify-content-between p-4 bg-dark text-white border-0">
                    <div>
                        <h6 class="fw-900 mb-1 text-white">Menu Induk: <?= $menu_info->nama_menu ?></h6>
                        <p class="small opacity-50 mb-0">URL Induk: /<?= $menu_info->link ?>/*</p>
                    </div>
                    <button class="btn btn-primary rounded-pill px-4 fw-800" data-bs-toggle="modal"
                        data-bs-target="#modalTambahSubmenu" style="background: var(--info-gradient); border: none;">
                        <i class="fas fa-plus-circle me-1"></i> Tambah Submenu
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="report-section-card animate-up" style="animation-delay: 0.1s">
            <div class="section-header">
                <h5><i class="fas fa-sitemap"></i> Daftar Submenu Tersedia</h5>
                <span class="badge bg-white text-dark rounded-pill px-3 fw-bold shadow-sm">Level Submenu</span>
            </div>

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
                                        <?php if (!empty($dt->access_levels)):
                                            $total_roles = count($dt->access_levels);
                                            $limit = 2; // Batasi maksimal role untuk tabel
                                            $i = 0;
                                            $tooltip_roles = [];
                                            foreach ($dt->access_levels as $lvl) {
                                                if ($i < $limit) {
                                                    echo '<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2" style="font-size: 0.6rem;">' . $lvl->role_name . '</span>';
                                                } else {
                                                    $tooltip_roles[] = $lvl->role_name;
                                                }
                                                $i++;
                                            }
                                            if ($total_roles > $limit):
                                                ?>
                                                <span class="badge bg-light text-secondary border rounded-pill px-2"
                                                    style="font-size: 0.6rem; cursor: help;" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="<?= implode(', ', $tooltip_roles) ?>">
                                                    +<?= ($total_roles - $limit) ?> Role Lainnya
                                                </span>
                                            <?php endif; else: ?>
                                            <span class="badge bg-danger text-white rounded-pill px-3"
                                                style="font-size: 0.6rem;">TIDAK ADA AKSES</span>
                                        <?php endif; ?>
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
                    <input type="text" name="link_submenu" class="form-control" list="listModulERP"
                        style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem" required
                        placeholder="Contoh: daftar_piutang">
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
                    <input type="text" name="link_submenu" id="edit_link_submenu" class="form-control"
                        list="listModulERP" style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem"
                        required>
                    <small class="text-muted mt-1 d-block" style="font-size: 0.7rem;"><i class="fas fa-info-circle"></i>
                        Ketik untuk mencari modul/halaman sistem.</small>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        // Inisiasi auto tooltips bootstrap
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        if ($.fn.DataTable) {
            $('#datatable-submenu').DataTable({
                "language": { "sSearch": "Cari Submenu:", "oPaginate": { "sNext": "<i class='fas fa-chevron-right'></i>", "sPrevious": "<i class='fas fa-chevron-left'></i>" } },
                "pageLength": 10,
                "dom": "<'row px-4 py-3'<'col-md-6'l><'col-md-6 d-flex justify-content-end text-end'f>>t<'row px-4 py-3'<'col-md-6'i><'col-md-6 d-flex justify-content-end text-end'p>>"
            });
        }

        // Toggle Status
        $(document).on('change', '.status-toggle', function () {
            const id = $(this).data('id');
            const status = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: '<?= base_url('menu/actionToggleSubmenuStatus') ?>',
                type: 'POST',
                data: { id: id, status: status },
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
            $('#edit_link_submenu').val($(this).data('link'));
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
    });
</script>