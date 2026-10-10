<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    /* ==========================================================================
    PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - USER MANAGEMENT
    ========================================================================== */
    :root {
        --primary-gradient: linear-gradient(135deg, #1a1c23 0%, #2c3e50 100%);
        --success-gradient: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
        --info-gradient: linear-gradient(135deg, #4895ef 0%, #4cc9f0 100%);
        --danger-gradient: linear-gradient(135deg, #f72585 0%, #b5179e 100%);
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

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
        font-size: 0.9rem;
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

    /* User Avatar Circle */
    .user-pill-avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        color: #4361ee;
        font-size: 0.9rem;
    }

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

    .btn-edit {
        background: #fff3cd;
        color: #856404;
    }

    .btn-delete {
        background: #f8d7da;
        color: #721c24;
    }

    .btn-edit:hover {
        background: #ffe69c;
        transform: translateY(-2px);
    }

    .btn-delete:hover {
        background: #f5c2c7;
        transform: translateY(-2px);
    }

    /* Form Styles */
    .form-control-premium {
        border: 2px solid #f1f5f9;
        border-radius: 12px;
        padding: 0.75rem 1.25rem;
        font-weight: 600;
        transition: all 0.3s;
        background: #f8fafc;
    }

    .form-control-premium:focus {
        border-color: #4361ee;
        background: white;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.1);
    }

    .form-label-premium {
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 1px;
        margin-bottom: 0.5rem;
        display: block;
    }

    /* Animation */
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
</style>

<div class="app-content mt-2">
    <div class="container-fluid">
        <!-- Main User Table Card -->
        <div class="report-section-card animate-up">
            <div class="section-header">
                <h5><i class="fas fa-users-cog"></i> Manajemen Pengguna Sistem</h5>
                <button type="button" class="btn btn-sm btn-light rounded-pill px-3 fw-bold shadow-sm"
                    data-bs-toggle="modal" data-bs-target="#modalUser">
                    <i class="fas fa-plus-circle me-1 text-primary"></i> Tambah Pengguna
                </button>
            </div>

            <div class="table-responsive">
                <table class="table premium-table table-hover mb-0" id="datatable-users">
                    <thead>
                        <tr>
                            <th width="40" class="text-center">No</th>
                            <th>Informasi Pengguna</th>
                            <th>Hak Akses (Role)</th>
                            <th class="text-center">Status</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($tampilData as $dt):
                            $status_class = ($dt->status == 1) ? 'bg-success' : 'bg-danger';
                            $status_label = ($dt->status == 1) ? 'AKTIF' : 'SUSPENDED';
                            ?>
                            <tr>
                                <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="user-pill-avatar">
                                            <?= strtoupper(substr($dt->username, 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-800 text-dark"><?= $dt->username ?></div>
                                            <div class="small text-muted"><?= $dt->email ?: 'no-email@system.com' ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div
                                        class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3">
                                        <i class="fas fa-user-shield me-1"></i> <?= $dt->role_nama ?: 'No Role' ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?= $status_class ?> rounded-pill px-3"
                                        style="font-size: 0.65rem; font-weight: 800;">
                                        <?= $status_label ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">

                                        <?php
                                        $my_username = $this->session->userdata('username');
                                        $my_level = $this->session->userdata('level');
                                        $my_nama_level = (string)$this->session->userdata('nama_level');
                                        $my_role = $this->db->get_where('tbl_divisi', ['divisi_id' => $my_level])->row();
                                        $is_dev = ($my_username === 'nizarsuryadi');
                                        $is_owner = ($my_role && isset($my_role->is_super_admin) && $my_role->is_super_admin == 1) || (stripos($my_nama_level, 'owner') !== false);
                                        $is_hr = ($my_role && (stripos((string)$my_role->divisi_nama, 'HR') !== false || $my_role->divisi_id == 16)) || (stripos($my_nama_level, 'HR') !== false) || (stripos($my_nama_level, 'Human Resource') !== false) || $my_level == 21;

                                        if ($is_dev || $is_owner || $is_hr): ?>
                                            <a href="javascript:void(0)" class="btn-action-premium btn-edit bg-dark text-white"
                                                title="Force Login (Masuk sebagai user ini)"
                                                onclick="confirmForceLogin('<?= $dt->user_id ?>', '<?= addslashes($dt->username) ?>')">
                                                <i class="fas fa-sign-in-alt"></i>
                                            </a>
                                        <?php endif; ?>

                                        <a href="<?= base_url('pengaturan/user_access/' . $dt->user_id) ?>"
                                            class="btn-action-premium bg-info text-white" title="Hak Akses Spesifik">
                                            <i class="fas fa-key"></i>
                                        </a>
                                        <a href="<?= base_url('pengaturan/edit_user_account/' . $dt->user_id) ?>"
                                            class="btn-action-premium btn-edit" title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="javascript:void(0)" onclick="confirmDelete('<?= $dt->user_id ?>')"
                                            class="btn-action-premium btn-delete" title="Delete User">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
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

<!-- Modal Tambah User -->
<div class="modal fade" id="modalUser" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 rounded-4 shadow-lg" method="post"
            action="<?= base_url('pengaturan/action_add_user_account') ?>">
            <div class="modal-header border-0 bg-dark text-white p-4">
                <h5 class="modal-title fw-900"><i class="fas fa-user-plus me-2"></i>Tambah Akun Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label-premium">Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-premium" name="username" required
                        placeholder="Gunakan huruf & angka">
                </div>
                <div class="mb-3">
                    <label class="form-label-premium">Alamat Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control form-control-premium" name="email" required
                        placeholder="user@company.com">
                </div>
                <div class="mb-3">
                    <label class="form-label-premium">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control form-control-premium" name="password" required
                        placeholder="Min. 6 karakter">
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label-premium">Karyawan Terkait</label>
                        <select name="karyawan_id" class="form-select form-control-premium">
                            <option value="">-- Pilih Karyawan --</option>
                            <?php foreach ($karyawan as $k): ?>
                                <option value="<?= $k->karyawan_id ?>"><?= $k->karyawan_nama ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-premium">Divisi / Role <span class="text-danger">*</span></label>
                        <select name="divisi_id" class="form-select form-control-premium" required>
                            <option value="">-- Pilih Role --</option>
                            <?php foreach ($divisi as $d): ?>
                                <option value="<?= $d->divisi_id ?>"><?= $d->divisi_nama ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mt-4 p-3 bg-light rounded-3 border">
                    <div class="form-check form-switch p-0 d-flex justify-content-between align-items-center">
                        <label class="fw-bold small text-dark mb-0">Status Akun Aktif</label>
                        <input class="form-check-input ms-0" type="checkbox" name="active" value="1" checked
                            style="width: 40px; height: 20px;">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                    data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow"
                    style="background: var(--primary-gradient)">Simpan Akun</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        if ($.fn.DataTable) {
            $('#datatable-users').DataTable({
                "language": { "sSearch": "Cari Pengguna:", "oPaginate": { "sNext": "<i class='fas fa-chevron-right'></i>", "sPrevious": "<i class='fas fa-chevron-left'></i>" } },
                "pageLength": 10,
                "dom": "<'row px-4 py-3'<'col-md-6'l><'col-md-6 d-flex justify-content-end text-end'f>>t<'row px-4 py-3'<'col-md-6'i><'col-md-6 d-flex justify-content-end text-end'p>>"
            });
        }
    });

    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Akun?',
            text: "Data login untuk pengguna ini akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f72585',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('pengaturan/actionDelete/') ?>" + id;
            }
        });
    }

    function confirmForceLogin(id, username) {
        Swal.fire({
            title: 'Force Login?',
            text: "Anda akan login sebagai " + username + ". Lanjutkan?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1a1c23',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Login!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('pengaturan/force_login/') ?>" + id;
            }
        });
    }
</script>