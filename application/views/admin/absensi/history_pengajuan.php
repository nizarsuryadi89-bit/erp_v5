<div class="app-content font-mono">
    <?php 
    $this->load->view('layout_admin/header_modul'); 
    $role_check_1 = strtolower($hakakses ?? '');
    $role_check_2 = strtolower($this->session->userdata('nama_level') ?? '');
    $is_hr_or_owner = (strpos($role_check_1, 'owner') !== false || strpos($role_check_1, 'hr') !== false || strpos($role_check_1, 'human resource') !== false ||
                       strpos($role_check_2, 'owner') !== false || strpos($role_check_2, 'hr') !== false || strpos($role_check_2, 'human resource') !== false);
    ?>

    <div class="container-fluid pt-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <a href="<?= base_url('hr') ?>"
                    class="btn btn-white d-flex align-items-center justify-content-center shadow-sm"
                    style="width: 42px; height: 42px; padding: 0; border-radius: 10px; border: 1px solid var(--tgm-slate-200); background: white; color: var(--tgm-slate-700);"
                    title="Kembali ke Dashboard HR">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-primary"></i>RIWAYAT
                        PENGAJUAN
                        IZIN & CUTI</h5>
                    <p class="text-muted small mb-0 mt-1">Daftar seluruh pengajuan yang telah diproses
                        (Disetujui/Ditolak)</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= base_url('absensi/approval') ?>"
                    class="btn btn-white border rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bi bi-hourglass-split me-2 text-warning"></i> ANTRIAN PENDING
                </a>
                <button class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise me-1"></i> REFRESH
                </button>
            </div>
        </div>

        <!-- Filter Page -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <div class="card-body p-4 bg-light" style="border-radius: 15px;">
                <form method="GET" action="<?= base_url('absensi/riwayat_approval') ?>" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Dari Tanggal</label>
                        <input type="date" class="form-control border-0 shadow-sm px-3" name="start_date"
                            value="<?= $start_date ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="fw-bold text-muted small text-uppercase mb-2">Sampai Tanggal</label>
                        <input type="date" class="form-control border-0 shadow-sm px-3" name="end_date"
                            value="<?= $end_date ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill py-2 shadow-sm">
                            <i class="bi bi-filter me-2"></i> FILTER RIWAYAT
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="report-section-card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-premium align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" width="50">NO</th>
                                <th width="150">TANGGAL DATA</th>
                                <th>KARYAWAN</th>
                                <th class="text-center">TIPE</th>
                                <th class="text-center">DURASI</th>
                                <th>KETERANGAN</th>
                                <th class="text-center">DOKUMEN</th>
                                <th class="text-center">STATUS</th>
                                <th class="text-center">DIPROSES OLEH</th>
                                <?php if ($is_hr_or_owner): ?>
                                    <th class="text-center" width="50"><i class="bi bi-trash"></i></th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($riwayat)): ?>
                                <tr>
                                    <td colspan="<?= $is_hr_or_owner ? '10' : '9' ?>" class="text-center py-5 text-muted">Belum ada riwayat pengajuan dalam
                                        periode ini.</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1;
                                foreach ($riwayat as $row): ?>
                                    <tr>
                                        <td class="text-center text-muted small">
                                            <?= $no++ ?>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark small">
                                                <?= date('d/m/Y', strtotime($row->tanggal_mulai)) ?>
                                            </div>
                                            <div class="small text-muted" style="font-size: 0.65rem;">Submit:
                                                <?= date('d/m/y H:i', strtotime($row->created_at)) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">
                                                <?= $row->nama_lengkap ?>
                                            </div>
                                            <div class="text-muted small" style="font-size: 0.7rem;">
                                                <?= $row->jabatan ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            $badge_class = 'bg-secondary';
                                            if ($row->tipe_izin == 'sakit')
                                                $badge_class = 'bg-danger-subtle text-danger border-danger';
                                            elseif ($row->tipe_izin == 'izin')
                                                $badge_class = 'bg-warning-subtle text-warning-emphasis border-warning';
                                            elseif ($row->tipe_izin == 'cuti')
                                                $badge_class = 'bg-info-subtle text-info-emphasis border-info';
                                            ?>
                                            <span class="badge rounded-pill <?= $badge_class ?> px-2 py-1 border small"
                                                style="font-size: 0.65rem;">
                                                <?= strtoupper($row->tipe_izin) ?>
                                            </span>
                                        </td>
                                        <td class="text-center small">
                                            <div class="fw-bold">
                                                <?= $row->durasi_jumlah ?>
                                                <?= ucfirst($row->durasi_tipe) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-muted small" style="white-space: normal; line-height: 1.4;">
                                                <?= nl2br($row->keterangan) ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($row->dokumen):
                                                $doc_url = (strpos($row->dokumen, 'uploads/') === 0) ? base_url($row->dokumen) : base_url('assets/uploads/cuti/' . $row->dokumen);
                                                ?>
                                                <div class="d-flex flex-column gap-1">
                                                    <button type="button"
                                                        class="btn btn-light btn-sm rounded-pill border btn-view-doc"
                                                        data-src="<?= $doc_url ?>"
                                                        data-type="<?= pathinfo($row->dokumen, PATHINFO_EXTENSION) ?>"
                                                        style="font-size: 0.6rem;">
                                                        <i class="bi bi-eye text-primary"></i> LIHAT
                                                    </button>
                                                    <a href="<?= $doc_url ?>" target="_blank" download
                                                        class="btn btn-white btn-sm rounded-pill border" style="font-size: 0.6rem;">
                                                        <i class="bi bi-download text-success"></i> UNDUH
                                                    </a>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted small fst-italic" style="font-size: 0.65rem;">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($row->status == 'Approved'): ?>
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1 rounded-pill fw-bold small">DISETUJUI</span>
                                            <?php else: ?>
                                                <span
                                                    class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-1 rounded-pill fw-bold small">DITOLAK</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="fw-bold text-dark small">
                                                <?= $row->admin_nama ?? 'System' ?>
                                            </div>
                                            <div class="text-muted" style="font-size: 0.65rem;">
                                                <?= $row->responded_at ? date('d/m/y H:i', strtotime($row->responded_at)) : '-' ?>
                                            </div>
                                        </td>
                                        <?php if ($is_hr_or_owner): ?>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle btn-delete-riwayat" data-id="<?= $row->id ?>" title="Hapus Data">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                        <?php endif; ?>
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

<!-- Modal Viewer -->
<div class="modal fade" id="docPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 0;">
            <div class="modal-header bg-dark text-white border-0" style="border-radius: 0;">
                <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-text me-2"></i>PREVIEW DOKUMEN</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-light" id="docContent"></div>
            <div class="modal-footer bg-white border-0" style="border-radius: 0;">
                <button type="button" class="btn btn-outline-dark px-4 rounded-0 fw-bold"
                    data-bs-dismiss="modal">TUTUP</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.btn-view-doc').click(function () {
            var src = $(this).data('src');
            var type = $(this).data('type').toLowerCase();
            var content = '';

            if (['jpg', 'jpeg', 'png', 'gif'].includes(type)) {
                content = '<div class="text-center p-3"><img src="' + src + '" class="img-fluid border shadow-sm" style="max-height: 500px;"></div>';
            } else if (type === 'pdf') {
                content = '<iframe src="' + src + '" style="width:100%; height:600px;" frameborder="0"></iframe>';
            } else {
                content = '<div class="text-center my-5 py-4"><i class="bi bi-file-earmark-break fs-1 text-secondary mb-3"></i><p class="text-muted">Dokumen tidak dapat dipreview.</p><a href="' + src + '" target="_blank" class="btn btn-dark rounded-0 px-4 mt-2">DOWNLOAD FILE</a></div>';
            }

            $('#docContent').html(content);
            new bootstrap.Modal(document.getElementById('docPreviewModal')).show();
        });

        $('.btn-delete-riwayat').click(function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Hapus Riwayat?',
                text: "Data pengajuan ini akan dihapus permanen dan kuota cuti akan disesuaikan kembali (jika cuti).",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url("absensi/delete_riwayat_cuti/") ?>' + id,
                        type: 'POST',
                        dataType: 'json',
                        success: function(res) {
                            if (res.status == 'success') {
                                Swal.fire(
                                    'Terhapus!',
                                    'Riwayat pengajuan berhasil dihapus.',
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error', res.message || 'Gagal menghapus data', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                        }
                    });
                }
            });
        });
    });
</script>

<style>
    .btn-white {
        background: white;
        color: #333;
        border: 1px solid #ddd;
    }

    .table-premium thead th {
        background-color: #f8f9fa;
        font-weight: 800;
        font-size: 10px;
        letter-spacing: 0.5px;
        color: #666;
        text-transform: uppercase;
        padding: 15px 10px;
        border-bottom: 2px solid #eee;
    }

    .table-premium tbody td {
        padding: 12px 10px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.85rem;
    }

    .report-section-card {
        background: #fff;
        border-radius: 15px;
        overflow: hidden;
    }
</style>