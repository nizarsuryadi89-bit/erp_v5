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
    .font-mono {
        font-family: 'JetBrains Mono', monospace !important;
        font-size: 0.75rem;
    }
    .text-primary-tgm { color: var(--tgm-primary); }
    .btn-white { background: white; color: var(--slate-700); border: 1px solid var(--slate-200); font-weight: 800; border-radius: 12px; padding: 10px 24px; transition: all 0.3s; }
    .btn-white:hover { background: var(--slate-50); color: var(--tgm-primary); border-color: var(--tgm-primary); transform: translateY(-1px); }

    /* Clean Card System */
    .clean-card { background: white; border: 1px solid var(--slate-200); border-radius: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: all 0.3s; }
    
    /* Table System */
    .table-simple { width: 100%; border-collapse: separate; border-spacing: 0; }
    .table-simple thead th { background: var(--slate-50); color: var(--slate-500); font-weight: 800; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; padding: 1.2rem; border-bottom: 2px solid var(--slate-100); }
    .table-simple tbody td { padding: 1.2rem; border-bottom: 1px solid var(--slate-100); color: var(--slate-700); vertical-align: middle; }
    .table-simple tbody tr:hover td { background-color: var(--slate-50); }

</style>
<?php $this->load->view('layout_admin/header_modul'); ?>

    <!-- Premium Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-5 no-print">
        <div class="d-flex align-items-center gap-4">
            <a href="<?= base_url('hr') ?>" class="btn-white shadow-sm d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; padding: 0; border-radius: 16px;" title="Kembali ke Dashboard">
                <i class="bi bi-arrow-left fs-4" style="margin-left: -2px;"></i>
            </a>
            <div>
                <div class="small text-primary-tgm fw-900 ls-1 text-uppercase mb-1">Live Monitoring</div>
                <h1 class="fw-900 mb-0 ls-n1 text-dark">Sisa Cuti <span class="text-primary-tgm">Karyawan</span></h1>
            </div>
        </div>
    </div>

    <!-- Notifikasi Pengajuan Pending -->
    <?php if (!empty($pending_cuti)): ?>
    <div class="alert border-0 shadow-sm mb-4" style="border-radius: 20px; background-color: #fffbeb; border-left: 5px solid #f59e0b !important;">
        <div class="d-flex align-items-center mb-3">
            <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                <i class="bi bi-bell-fill fs-5 pulse-blue" style="animation: pulse-red 2s infinite;"></i>
            </div>
            <div>
                <h6 class="fw-900 mb-0 text-dark">Terdapat <?= count($pending_cuti) ?> Pengajuan Cuti Menunggu Persetujuan</h6>
                <small class="text-muted fw-bold">Mohon segera proses pengajuan karyawan di bawah ini.</small>
            </div>
        </div>
        
        <div class="bg-white rounded-3 p-3 shadow-sm mt-3">
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <tbody>
                        <?php foreach ($pending_cuti as $p): ?>
                        <tr class="border-bottom">
                            <td style="width: 30%;">
                                <div class="fw-800 text-dark" style="font-size: 0.9rem;"><?= strtoupper($p->nama_lengkap) ?></div>
                                <div class="font-mono text-muted"><?= $p->nama_jabatan ?></div>
                            </td>
                            <td style="width: 25%;">
                                <div class="fw-800 text-dark"><span class="badge bg-primary-subtle text-primary border border-primary-subtle"><?= $p->tipe_izin ?></span></div>
                                <div class="small fw-bold text-muted mt-1"><i class="bi bi-calendar-event text-warning"></i> <?= date('d M Y', strtotime($p->tanggal_mulai)) ?> <span class="mx-1">&bull;</span> <?= $p->durasi_jumlah ?> Hari</div>
                            </td>
                            <td style="width: 25%;">
                                <div class="small text-secondary fst-italic" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    "<?= htmlspecialchars($p->keterangan) ?>"
                                </div>
                            </td>
                            <td class="text-end" style="width: 20%;">
                                <a href="<?= site_url('absensi/approve_action/'.$p->id.'?ref=sisa_cuti') ?>" onclick="return confirm('Apakah Anda yakin ingin MENYETUJUI cuti ini?')" class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm me-1"><i class="bi bi-check2-circle"></i> Setujui</a>
                                <a href="<?= site_url('absensi/reject_action/'.$p->id.'?ref=sisa_cuti') ?>" onclick="return confirm('Apakah Anda yakin ingin MENOLAK cuti ini?')" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold shadow-sm"><i class="bi bi-x-circle"></i> Tolak</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Master Filter & Table workspace -->
    <div class="clean-card shadow-sm border-0 overflow-hidden">
        <div class="p-0">
            <!-- Table -->
            <div class="table-responsive">
                <table class="table-simple" id="tableSisaCuti">
                    <thead>
                        <tr>
                            <th width="50" class="text-center">#</th>
                            <th>DATA KARYAWAN</th>
                            <th class="text-center">CUTI TAHUNAN</th>
                            <th class="text-center">CUTI MENIKAH</th>
                            <th class="text-center">CUTI DUKA</th>
                            <th class="text-center">KELUARGA SAKIT</th>
                            <th class="text-center">ISTRI MELAHIRKAN</th>
                            <th class="text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($kuota_cuti)): ?>
                                <?php $no = 1;
                                foreach ($kuota_cuti as $row): ?>
                                        <tr>
                                            <td class="text-center font-mono small text-muted"><?= $no++ ?></td>
                                            <td>
                                                <div class="h6 fw-900 mb-0 ls-n1"><?= strtoupper($row->nama_lengkap) ?></div>
                                                <div class="small fw-700 text-muted ls-1" style="font-size: 0.65rem;">JABATAN: <?= $row->nama_jabatan ?></div>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <span class="badge bg-light text-dark border">SISA: <?= isset($row->sisa) ? $row->sisa : 0 ?></span>
                                                    <span class="badge bg-success text-white">TERPAKAI: <?= isset($row->terpakai) ? $row->terpakai : 0 ?></span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border">SISA: <?= isset($row->menikah_sisa) ? $row->menikah_sisa : 0 ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border">SISA: <?= isset($row->duka_sisa) ? $row->duka_sisa : 0 ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border">SISA: <?= isset($row->sakitkel_sisa) ? $row->sakitkel_sisa : 0 ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border">SISA: <?= isset($row->melahirkansuami_sisa) ? $row->melahirkansuami_sisa : 0 ?></span>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-primary" onclick="showRiwayat(<?= $row->karyawan_id ?>, '<?= addslashes($row->nama_lengkap) ?>')">
                                                    <i class="bi bi-clock-history"></i> Riwayat
                                                </button>
                                            </td>
                                        </tr>
                                <?php endforeach; ?>
                        <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="py-5">
                                            <i class="bi bi-calendar-x fs-1 text-slate-300 mb-3 d-block"></i>
                                            <h6 class="fw-900 text-slate-400 ls-1">TIDAK ADA DATA CUTI</h6>
                                        </div>
                                    </td>
                                </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
</div>

<!-- Modal Riwayat Cuti -->
<div class="modal fade" id="modalRiwayatCuti" tabindex="-1" aria-labelledby="modalRiwayatCutiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalRiwayatCutiLabel">Riwayat Cuti</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <h6 class="fw-bold mb-0 text-primary-tgm" id="riwayatKaryawanName">Nama Karyawan</h6>
                    <small class="text-muted">Tahun <?= date('Y') ?></small>
                </div>
                
                <div id="riwayatContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#tableSisaCuti').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
    });

    var currentKaryawanId = null;
    var currentKaryawanName = '';

    function showRiwayat(karyawanId, karyawanName) {
        currentKaryawanId = karyawanId;
        currentKaryawanName = karyawanName;

        $('#riwayatKaryawanName').text(karyawanName);
        $('#riwayatContent').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);
        
        var modal = new bootstrap.Modal(document.getElementById('modalRiwayatCuti'));
        modal.show();

        $.ajax({
            url: "<?= site_url('absensi/get_riwayat_cuti/') ?>" + karyawanId,
            type: "GET",
            success: function(response) {
                $('#riwayatContent').html(response);
            },
            error: function() {
                $('#riwayatContent').html('<div class="text-center text-danger p-3"><i class="bi bi-exclamation-triangle"></i> Gagal memuat data riwayat.</div>');
            }
        });
    }

    function hapusRiwayat(id) {
        if(confirm('Apakah Anda yakin ingin menghapus riwayat cuti yang ditolak ini?')) {
            $.ajax({
                url: "<?= site_url('absensi/delete_riwayat_cuti/') ?>" + id,
                type: "POST",
                dataType: "json",
                success: function(response) {
                    if(response.status == 'success') {
                        // Reload modal content smoothly
                        $.ajax({
                            url: "<?= site_url('absensi/get_riwayat_cuti/') ?>" + currentKaryawanId,
                            type: "GET",
                            success: function(response) {
                                $('#riwayatContent').html(response);
                            }
                        });
                    } else {
                        alert('Gagal menghapus data.');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan server.');
                }
            });
        }
    }
</script>
