<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">

<style>
    :root {
        --tgm-primary: #2563eb;
        --tgm-primary-soft: #eff6ff;
        --slate-50: #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --slate-300: #cbd5e1;
        --slate-400: #94a3b8;
        --slate-500: #64748b;
        --slate-600: #475569;
        --slate-700: #334155;
        --slate-800: #1e293b;
        --slate-900: #0f172a;
        --emerald: #10b981;
        --rose: #f43f5e;
        --amber: #f59e0b;
        --sky: #0ea5e9;
    }

    .app-content { background-color: #f8fafc; padding: 2rem; min-height: 100vh; }
    
    /* Typography & Utilities */
    .fw-800 { font-weight: 800; }
    .fw-900 { font-weight: 900; }
    .ls-n1 { letter-spacing: -1px; }
    .ls-1 { letter-spacing: 1px; }
    .text-primary-tgm { color: var(--tgm-primary); }
    .btn-white { background: white; color: var(--slate-700); border: 1px solid var(--slate-200); font-weight: 800; border-radius: 12px; padding: 10px 24px; transition: all 0.3s; }
    .btn-white:hover { background: var(--slate-50); color: var(--tgm-primary); border-color: var(--tgm-primary); transform: translateY(-1px); }

    /* Clean Card System */
    .clean-card { background: white; border: 1px solid var(--slate-200); border-radius: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: all 0.3s; padding: 2rem;}
    .clean-card:hover { border-color: var(--tgm-primary); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }

    /* Table System */
    .table-simple { width: 100%; border-collapse: separate; border-spacing: 0; }
    .table-simple thead th { background: var(--slate-50); color: var(--slate-500); font-weight: 800; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; padding: 1.2rem; border-bottom: 2px solid var(--slate-100); }
    .table-simple tbody td { padding: 1.2rem; border-bottom: 1px solid var(--slate-100); color: var(--slate-700); vertical-align: middle; }
    .table-simple tbody tr:hover td { background-color: var(--slate-50); }

    /* Soft Badges */
    .badge-soft { font-weight: 900; font-size: 0.65rem; padding: 6px 14px; border-radius: 8px; letter-spacing: 0.5px; text-transform: uppercase; }
    .bg-emerald { background-color: #d1fae5; color: #065f46; }
    .bg-warning { background-color: #fef3c7; color: #92400e; }
    .bg-rose { background-color: #fee2e2; color: #991b1b; }

    .btn-sm-action { border-radius: 8px; padding: 6px 12px; font-weight: 700; font-size: 0.75rem; border: none; transition: all 0.2s;}
    .btn-sm-action.approve { background: var(--emerald); color: white; }
    .btn-sm-action.approve:hover { background: #059669; transform: translateY(-1px); box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3); }
    .btn-sm-action.reject { background: var(--rose); color: white; }
    .btn-sm-action.reject:hover { background: #e11d48; transform: translateY(-1px); box-shadow: 0 4px 6px -1px rgba(244, 63, 94, 0.3); }

    @media print {
        .no-print { display: none !important; }
        .app-content { padding: 0; background: white; }
        .clean-card { border: none; box-shadow: none; padding: 0; }
    }
</style>

<?php $this->load->view('layout_admin/header_modul'); ?>

<div class="app-content">
    <!-- Premium Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-5 no-print">
        <div class="d-flex align-items-center gap-4">
            <a href="<?= base_url('absensi') ?>" class="btn-white shadow-sm d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; padding: 0; border-radius: 16px;" title="Kembali ke Absensi">
                <i class="bi bi-arrow-left fs-4" style="margin-left: -2px;"></i>
            </a>
            <div>
                <div class="small text-primary-tgm fw-900 ls-1 text-uppercase mb-1">MANAJEMEN ABSENSI</div>
                <h1 class="fw-900 mb-0 ls-n1 text-dark">Koreksi <span class="text-primary-tgm">Kehadiran</span></h1>
            </div>
        </div>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4" style="background: #ecfdf5; color: #065f46; font-weight: 600;">
            <i class="bi bi-check-circle-fill me-2"></i> <?= $this->session->flashdata('success') ?>
        </div>
    <?php endif; ?>

    <div class="clean-card">
        <div class="table-responsive">
            <table class="table-simple" id="koreksiTable">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="20%">Karyawan</th>
                        <th width="15%">Tanggal Absen</th>
                        <th width="12%">Jam Usulan</th>
                        <th width="20%">Alasan</th>
                        <th width="13%">Status</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($pengajuan as $p): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?= !empty($p->foto) && file_exists('assets/images/karyawan/' . $p->foto) ? base_url('assets/images/karyawan/' . $p->foto) : base_url('assets/images/karyawan/default.png') ?>" 
                                         onerror="this.onerror=null; this.src='<?= base_url('assets/images/karyawan/default.png') ?>'" 
                                         class="rounded-circle shadow-sm" 
                                         style="width: 42px; height: 42px; object-fit: cover;">
                                    <div>
                                        <div class="fw-800 text-dark" style="font-size: 0.9rem;"><?= htmlspecialchars($p->nama_lengkap) ?></div>
                                        <div class="text-muted" style="font-size: 0.75rem; font-family: 'JetBrains Mono', monospace;">NIK: <?= htmlspecialchars($p->nik) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-800"><?= date('d M Y', strtotime($p->tanggal_absen)) ?></div>
                                <div class="text-muted" style="font-size: 0.75rem;">Pengajuan: <?= date('d/m H:i', strtotime($p->created_at)) ?></div>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <?php if(!empty($p->jam_masuk_baru)): ?>
                                        <span class="badge-soft" style="background: var(--slate-100); color: var(--slate-700);"><i class="bi bi-box-arrow-in-right me-1"></i> <?= substr($p->jam_masuk_baru, 0, 5) ?></span>
                                    <?php endif; ?>
                                    <?php if(!empty($p->jam_pulang_baru)): ?>
                                        <span class="badge-soft" style="background: var(--slate-100); color: var(--slate-700);"><i class="bi bi-box-arrow-left me-1"></i> <?= substr($p->jam_pulang_baru, 0, 5) ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="text-muted" style="font-size: 0.85rem; line-height: 1.5; max-width: 250px;">
                                    <?= nl2br(htmlspecialchars($p->alasan)) ?>
                                </div>
                            </td>
                            <td>
                                <?php if ($p->status == 'Pending'): ?>
                                    <span class="badge-soft bg-warning">Pending</span>
                                <?php elseif ($p->status == 'Disetujui'): ?>
                                    <span class="badge-soft bg-emerald">Disetujui</span>
                                <?php else: ?>
                                    <span class="badge-soft bg-rose">Ditolak</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($p->status == 'Pending'): ?>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="<?= base_url('absensi/approve_koreksi/'.$p->id) ?>" class="btn-sm-action approve text-decoration-none" onclick="return confirm('Apakah Anda yakin menyetujui koreksi absen ini?')"><i class="bi bi-check-lg me-1"></i> Setujui</a>
                                        <a href="<?= base_url('absensi/reject_koreksi/'.$p->id) ?>" class="btn-sm-action reject text-decoration-none" onclick="return confirm('Apakah Anda yakin menolak koreksi absen ini?')"><i class="bi bi-x-lg me-1"></i> Tolak</a>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted" style="font-size: 0.8rem; font-weight: 600;"><i class="bi bi-lock-fill me-1"></i> Selesai</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(function () {
        if ($.fn.DataTable.isDataTable('#koreksiTable')) {
            $('#koreksiTable').DataTable().destroy();
        }
        $('#koreksiTable').DataTable({
            "responsive": true, 
            "lengthChange": false, 
            "autoWidth": false,
            "order": [[2, "desc"]],
            "pageLength": 10,
            "language": {
                "search": "Cari Karyawan / Alasan:",
                "paginate": {
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                },
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data"
            },
            "dom": '<"d-flex justify-content-between align-items-center mb-4"f>rt<"d-flex justify-content-between align-items-center mt-4"ip>'
        });
    });
</script>
