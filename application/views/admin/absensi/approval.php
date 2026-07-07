<div class="app-content font-mono">
    <!-- <div class="container-fluid"> -->
    <!-- Header -->
    <?php $this->load->view('layout_admin/header_modul'); ?>

    <!-- Guide Button Row -->
    <div class="container-fluid mt-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <a href="<?= base_url('hr') ?>"
                    class="btn btn-white d-flex align-items-center justify-content-center shadow-sm"
                    style="width: 42px; height: 42px; padding: 0; border-radius: 10px; border: 1px solid var(--tgm-slate-200); background: white; color: var(--tgm-slate-700);"
                    title="Kembali ke Dashboard HR">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-check-double me-2 text-primary"></i>PERSETUJUAN IZIN
                    & CUTI</h5>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= base_url('absensi/riwayat_approval') ?>"
                    class="btn btn-white border rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bi bi-clock-history me-2 text-primary"></i> RIWAYAT PENGAJUAN
                </a>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal"
                    data-bs-target="#modalGuideApproval">
                    <i class="fas fa-info-circle me-2"></i> PANDUAN FITUR
                </button>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100"
                    style="border-radius: 0; border-left: 4px solid #0d6efd !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-primary bg-opacity-10 p-2 me-3">
                                <i class="bi bi-hourglass-split text-primary fs-4"></i>
                            </div>
                            <div>
                                <h6 class="text-muted small mb-0">Total Pending</h6>
                                <h4 class="fw-bold mb-0"><?= count($pengajuan) ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            $today = date('Y-m-d');
            $izin_hari_ini = 0;
            $sakit_pending = 0;
            $cuti_pending = 0;
            foreach ($pengajuan as $p) {
                if (date('Y-m-d', strtotime($p->created_at)) == $today)
                    $izin_hari_ini++;
                if ($p->tipe_izin == 'sakit')
                    $sakit_pending++;
                if (in_array(strtolower($p->tipe_izin), ['cuti', 'cuti keluarga', 'cuti sakit', 'cuti melahirkan', 'cuti menikah']))
                    $cuti_pending++;
            }
            ?>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100"
                    style="border-radius: 0; border-left: 4px solid #198754 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-success bg-opacity-10 p-2 me-3">
                                <i class="bi bi-calendar-check text-success fs-4"></i>
                            </div>
                            <div>
                                <h6 class="text-muted small mb-0">Masuk Hari Ini</h6>
                                <h4 class="fw-bold mb-0"><?= $izin_hari_ini ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100"
                    style="border-radius: 0; border-left: 4px solid #dc3545 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-danger bg-opacity-10 p-2 me-3">
                                <i class="bi bi-heart-pulse text-danger fs-4"></i>
                            </div>
                            <div>
                                <h6 class="text-muted small mb-0">Pending Sakit</h6>
                                <h4 class="fw-bold mb-0"><?= $sakit_pending ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100"
                    style="border-radius: 0; border-left: 4px solid #0dcaf0 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-info bg-opacity-10 p-2 me-3">
                                <i class="bi bi-briefcase text-info fs-4"></i>
                            </div>
                            <div>
                                <h6 class="text-muted small mb-0">Pending Cuti</h6>
                                <h4 class="fw-bold mb-0"><?= $cuti_pending ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="report-section-card shadow-sm border-0">
                    <div
                        class="report-section-header bg-white py-3 d-flex justify-content-between align-items-center px-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="bg-dark p-2 me-3">
                                <i class="bi bi-patch-check text-white"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-dark">Persetujuan Izin & Cuti</h5>
                                <p class="text-muted small mb-0 mt-1">Kelola dan verifikasi pengajuan ketidakhadiran
                                    karyawan</p>
                            </div>
                        </div>
                        <button class="btn btn-dark btn-sm rounded-0 px-3" onclick="location.reload()">
                            <i class="bi bi-arrow-clockwise me-1"></i> REFRESH
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($pengajuan)): ?>
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <div class="bg-light d-inline-block p-4 rounded-circle mb-3">
                                        <i class="bi bi-check2-all fs-1 text-muted opacity-50"></i>
                                    </div>
                                </div>
                                <h5 class="text-dark fw-bold">Semua Beres!</h5>
                                <p class="text-muted small mb-0">Tidak ada pengajuan pending yang memerlukan tindakan.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-premium align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="50">NO</th>
                                            <th width="180">TANGGAL PENGAJUAN</th>
                                            <th>KARYAWAN</th>
                                            <th class="text-center">TIPE</th>
                                            <th class="text-center" width="150">DURASI</th>
                                            <th>KETERANGAN</th>
                                            <th class="text-center">DOKUMEN</th>
                                            <th class="text-center" width="120">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1;
                                        foreach ($pengajuan as $row): ?>
                                            <tr>
                                                <td class="text-center text-muted"><?= $no++ ?></td>
                                                <td>
                                                    <div class="fw-bold text-dark">
                                                        <?= date('d M Y', strtotime($row->created_at)) ?>
                                                    </div>
                                                    <div class="small text-muted">
                                                        <i class="bi bi-clock me-1"></i>
                                                        <?= date('H:i', strtotime($row->created_at)) ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm bg-light me-3 d-flex align-items-center justify-content-center text-primary fw-bold border"
                                                            style="width: 40px; height: 40px;">
                                                            <?= substr($row->nama_lengkap, 0, 1) ?>
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold text-dark"><?= $row->nama_lengkap ?></div>
                                                            <div class="text-muted small"><i
                                                                    class="bi bi-person-badge me-1"></i> <?= $row->jabatan ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <?php
                                                    $badge_class = 'bg-secondary';
                                                    $icon = 'bi-info-circle';
                                                    if ($row->tipe_izin == 'sakit') {
                                                        $badge_class = 'bg-danger-subtle text-danger border-danger';
                                                        $icon = 'bi-heart-pulse-fill';
                                                    } elseif ($row->tipe_izin == 'izin') {
                                                        $badge_class = 'bg-warning-subtle text-warning-emphasis border-warning';
                                                        $icon = 'bi-envelope-fill';
                                                    } elseif ($row->tipe_izin == 'cuti') {
                                                        $badge_class = 'bg-info-subtle text-info-emphasis border-info';
                                                        $icon = 'bi-calendar-event-fill';
                                                    }
                                                    ?>
                                                    <span class="badge rounded-pill <?= $badge_class ?> px-3 py-2 border">
                                                        <i class="bi <?= $icon ?> me-1"></i> <?= strtoupper($row->tipe_izin) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="fw-bold text-dark"><?= $row->durasi_jumlah ?>
                                                        <?= ucfirst($row->durasi_tipe) ?></div>
                                                    <div class="text-muted small">
                                                        Mulai: <?= date('d/m/y', strtotime($row->tanggal_mulai)) ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-muted small text-truncate" style="max-width: 200px;"
                                                        title="<?= $row->keterangan ?>">
                                                        <?= $row->keterangan ?>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($row->dokumen):
                                                        // Jika path sudah mengandung 'uploads/', gunakan langsung. Jika tidak (data lama/cuti), gunakan prefix aset.
                                                        $doc_url = (strpos($row->dokumen, 'uploads/') === 0) ? base_url($row->dokumen) : base_url('assets/uploads/cuti/' . $row->dokumen);
                                                        ?>
                                                        <button type="button"
                                                            class="btn btn-light btn-sm rounded-0 border btn-view-doc"
                                                            data-src="<?= $doc_url ?>"
                                                            data-type="<?= pathinfo($row->dokumen, PATHINFO_EXTENSION) ?>">
                                                            <i class="bi bi-file-earmark-text text-primary"></i> LIHAT
                                                        </button>
                                                    <?php else: ?>
                                                        <span class="text-muted small fst-italic">Kosong</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button type="button"
                                                            class="btn btn-success btn-action-square btn-approve"
                                                            data-url="<?= base_url('absensi/approve_action/' . $row->id) ?>"
                                                            data-nama="<?= $row->nama_lengkap ?>" title="Setujui">
                                                            <i class="bi bi-check-lg"></i>
                                                        </button>
                                                        <button type="button"
                                                            class="btn btn-danger btn-action-square btn-reject"
                                                            data-url="<?= base_url('absensi/reject_action/' . $row->id) ?>"
                                                            data-nama="<?= $row->nama_lengkap ?>" title="Tolak">
                                                            <i class="bi bi-x-lg"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Panduan Approval -->
<div class="modal fade" id="modalGuideApproval" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0 p-4"
                style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-success rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 45px; height: 45px;">
                        <i class="fas fa-clipboard-check fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">Panduan Persetujuan Izin & Cuti</h5>
                        <p class="text-white-50 small mb-0">Manajemen verifikasi pengajuan ketidakhadiran karyawan</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-3 bg-light border-end">
                        <div class="nav flex-column nav-pills p-3" id="v-pills-tab-app" role="tablist">
                            <button class="nav-link active text-start py-3 px-4 fw-bold mb-2 border-0"
                                data-bs-toggle="pill" data-bs-target="#app-overview" type="button">
                                <i class="fas fa-tachometer-alt me-2"></i> Ringkasan Status
                            </button>
                            <button class="nav-link text-start py-3 px-4 fw-bold mb-2 border-0" data-bs-toggle="pill"
                                data-bs-target="#app-types" type="button">
                                <i class="fas fa-tags me-2"></i> Tipe Pengajuan
                            </button>
                            <button class="nav-link text-start py-3 px-4 fw-bold mb-2 border-0" data-bs-toggle="pill"
                                data-bs-target="#app-doc" type="button">
                                <i class="fas fa-file-medical me-2"></i> Verifikasi Bukti
                            </button>
                            <button class="nav-link text-start py-3 px-4 fw-bold mb-2 border-0" data-bs-toggle="pill"
                                data-bs-target="#app-action" type="button">
                                <i class="fas fa-user-check me-2"></i> Alur Eksekusi
                            </button>
                        </div>
                    </div>
                    <div class="col-md-9 border-start">
                        <div class="tab-content p-5" id="v-pills-tabContent-app">

                            <!-- Overview -->
                            <div class="tab-pane fade show active" id="app-overview">
                                <h4 class="fw-bold mb-4">Dashboard Monitoring Approval</h4>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-4 border bg-light">
                                            <h6 class="fw-bold text-primary mb-2">Total Pending</h6>
                                            <p class="small text-muted mb-0">Jumlah seluruh antrean pengajuan yang
                                                menunggu keputusan HR.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-4 border bg-light">
                                            <h6 class="fw-bold text-success mb-2">Izin Hari Ini</h6>
                                            <p class="small text-muted mb-0">Jumlah pengajuan yang baru masuk pada
                                                tanggal hari ini.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <div class="alert alert-info border-0 small">
                                            <i class="fas fa-sync-alt me-2"></i> Gunakan tombol <b>REFRESH</b> di pojok
                                            kanan atas untuk menarik data terbaru jika ada karyawan yang baru saja
                                            submit via aplikasi.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Types -->
                            <div class="tab-pane fade" id="app-types">
                                <h4 class="fw-bold mb-4">Klasifikasi Izin & Cuti</h4>
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item px-0 py-3 d-flex align-items-start gap-3">
                                        <span class="badge bg-danger p-2"><i class="fas fa-heartbeat"></i> SAKIT</span>
                                        <div>
                                            <div class="fw-bold">Pengajuan Sakit</div>
                                            <p class="small text-muted mb-0">Wajib diverifikasi dengan Surat Keterangan
                                                Dokter (SKD). Jika tanpa bukti, HR berhak menolak atau mengalihkan ke
                                                Potongan Gaji.</p>
                                        </div>
                                    </div>
                                    <div class="list-group-item px-0 py-3 d-flex align-items-start gap-3">
                                        <span class="badge bg-warning text-dark p-2"><i class="fas fa-envelope"></i>
                                            IZIN</span>
                                        <div>
                                            <div class="fw-bold">Pengajuan Izin Kepentingan</div>
                                            <p class="small text-muted mb-0">Izin mendadak atau terencana (misal: urusan
                                                keluarga, kedukaan, dll).</p>
                                        </div>
                                    </div>
                                    <div class="list-group-item px-0 py-3 d-flex align-items-start gap-3">
                                        <span class="badge bg-info p-2"><i class="fas fa-calendar-alt"></i> CUTI</span>
                                        <div>
                                            <div class="fw-bold">Cuti Tahunan / Bersama</div>
                                            <p class="small text-muted mb-0">Pengurangan kuota cuti tahunan karyawan.
                                                Pastikan durasi yang diajukan masuk akal.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Document Verification -->
                            <div class="tab-pane fade" id="app-doc">
                                <h4 class="fw-bold mb-4">Verifikasi Bukti Digital</h4>
                                <div class="card border-0 bg-light rounded-4 overflow-hidden">
                                    <div class="card-body p-4 text-center">
                                        <i class="fas fa-search-plus fa-3x text-muted mb-3"></i>
                                        <p class="text-dark fw-bold mb-2">Gunakan Tombol "LIHAT"</p>
                                        <p class="small text-muted mb-0">Sistem akan membuka <b>Viewer Pop-up</b> khusus
                                            untuk Gambar maupun PDF. Anda bisa langsung memverifikasi keaslian dokumen
                                            tanpa perlu pindah halaman atau download manual.</p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <h6 class="fw-bold small text-uppercase">Tips:</h6>
                                    <ul class="small text-muted">
                                        <li>Pastikan tanggal di Surat Dokter sesuai dengan tanggal yang diajukan
                                            karyawan.</li>
                                        <li>Jika dokumen Blur atau tidak terbaca, Anda bisa mengontak karyawan via Menu
                                            Chat/WA.</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Execution Workflow -->
                            <div class="tab-pane fade" id="app-action">
                                <h4 class="fw-bold mb-4">Prosedur Pengambilan Keputusan</h4>
                                <div class="d-flex gap-4 mb-4">
                                    <div
                                        class="flex-grow-1 p-3 rounded-4 border-start border-4 border-success bg-success bg-opacity-10 text-center">
                                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                            style="width: 40px; height: 40px;">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div class="fw-bold">SETUJUI</div>
                                        <p class="small text-muted mb-0">Status absensi pada hari tsb akan otomatis
                                            berubah menjadi "Hadir (S/I/C)" dan tidak dihitung sebagai Alpha.</p>
                                    </div>
                                    <div
                                        class="flex-grow-1 p-3 rounded-4 border-start border-4 border-danger bg-danger bg-opacity-10 text-center">
                                        <div class="bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                            style="width: 40px; height: 40px;">
                                            <i class="fas fa-times"></i>
                                        </div>
                                        <div class="fw-bold">TOLAK</div>
                                        <p class="small text-muted mb-0">Pengajuan dibatalkan. Karyawan akan mendapatkan
                                            notifikasi dan hari tsb akan tetap dianggap tidak hadir jika tidak ada absen
                                            manual.</p>
                                    </div>
                                </div>
                                <div class="p-3 rounded-4 bg-light border">
                                    <p class="small mb-0"><i class="fas fa-shield-alt text-primary me-2"></i> Setiap
                                        tindakan disertai <b>Konfirmasi Keamanan</b> (Confirmation Dialog) untuk
                                        mencegah kesalahan klik.</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-light">
                <button type="button" class="btn btn-secondary px-5 fw-bold rounded-pill" data-bs-dismiss="modal">Tutup
                    Panduan</button>
            </div>
        </div>
    </div>
</div>

<style>
    .font-mono {
        font-family: 'JetBrains Mono', monospace !important;
        font-size: 0.75rem;
    }

    .btn-action-square {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0;
        padding: 0;
    }

    .table-premium thead th {
        background-color: #f8f9fa;
        border-top: none;
        font-weight: 700;
        font-size: 11px;
        letter-spacing: 0.5px;
        color: #333;
        text-transform: uppercase;
        padding: 15px 10px;
        border-bottom: 2px solid #eee;
    }

    .table-premium tbody td {
        padding: 15px 10px;
        border-bottom: 1px solid #f0f0f0;
    }

    .report-section-card {
        background: #fff;
        border-radius: 0;
    }
</style>

<script>
    $(document).ready(function () {
        // Flash Messages
        <?php if ($this->session->flashdata('success')): ?>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '<?= $this->session->flashdata('success') ?>',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Opps...',
                text: '<?= $this->session->flashdata('error') ?>',
                confirmButtonColor: '#3085d6'
            });
        <?php endif; ?>

        // Document Viewer
        $('.btn-view-doc').click(function () {
            var src = $(this).data('src');
            var type = $(this).data('type').toLowerCase();
            var content = '';

            if (['jpg', 'jpeg', 'png', 'gif'].includes(type)) {
                content = '<div class="text-center p-3"><img src="' + src + '" class="img-fluid border shadow-sm" style="max-height: 500px;"></div>';
            } else if (type === 'pdf') {
                content = '<iframe src="' + src + '" style="width:100%; height:600px;" frameborder="0"></iframe>';
            } else {
                content = '<div class="text-center my-5 py-4"><i class="bi bi-file-earmark-break fs-1 text-secondary mb-3"></i><p class="text-muted">Dokumen tidak dapat dipreview secara langsung.</p><a href="' + src + '" target="_blank" class="btn btn-dark rounded-0 px-4 mt-2"><i class="bi bi-download me-2"></i> DOWNLOAD FILE</a></div>';
            }

            $('#docContent').html(content);
            new bootstrap.Modal(document.getElementById('docPreviewModal')).show();
        });

        // Approve Confirmation
        $('.btn-approve').click(function (e) {
            e.preventDefault();
            var url = $(this).data('url');
            var nama = $(this).data('nama');

            Swal.fire({
                title: 'Konfirmasi Persetujuan',
                text: "Apakah Anda yakin ingin menyetujui pengajuan ketidakhadiran untuk " + nama + "?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-check-circle me-2"></i> YA, SETUJUI',
                cancelButtonText: 'BATAL',
                reverseButtons: true,
                borderRadius: 0
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });

        // Reject Confirmation
        $('.btn-reject').click(function (e) {
            e.preventDefault();
            var url = $(this).data('url');
            var nama = $(this).data('nama');

            Swal.fire({
                title: 'Tolak Pengajuan?',
                text: "Anda akan menolak pengajuan izin untuk " + nama + ". Berikan alasan jika diperlukan pada langkah selanjutnya.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-x-circle me-2"></i> YA, TOLAK',
                cancelButtonText: 'BATAL',
                reverseButtons: true,
                borderRadius: 0
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
</script>

<!-- Document Preview Modal -->
<div class="modal fade" id="docPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 0;">
            <div class="modal-header bg-dark text-white border-0" style="border-radius: 0;">
                <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-text me-2"></i>PREVIEW DOKUMEN</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-light" id="docContent">
                <!-- Content injected by JS -->
            </div>
            <div class="modal-footer bg-white border-0" style="border-radius: 0;">
                <button type="button" class="btn btn-outline-dark px-4 rounded-0 fw-bold"
                    data-bs-dismiss="modal">TUTUP</button>
            </div>
        </div>
    </div>
</div>