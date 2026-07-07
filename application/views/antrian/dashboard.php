<?php
// $this->load->view('layout_admin/head', $data);
// $this->load->view('layout_admin/top_header', $data);
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <h4 class="mb-4"><i class="fas fa-traffic-light"></i> Dashboard Antrian</h4>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2">Total Antrian Hari Ini</p>
                            <h3 class="mb-0"><?= $stats['total_antrian'] ?? 0; ?></h3>
                        </div>
                        <i class="fas fa-list fa-3x text-primary opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2">Antrian Selesai</p>
                            <h3 class="mb-0"><?= $stats['antrian_selesai'] ?? 0; ?></h3>
                        </div>
                        <i class="fas fa-check-circle fa-3x text-success opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2">Antrian Dibatalkan</p>
                            <h3 class="mb-0"><?= $stats['antrian_batal'] ?? 0; ?></h3>
                        </div>
                        <i class="fas fa-times-circle fa-3x text-danger opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2">Tingkat Penyelesaian</p>
                            <h3 class="mb-0">
                                <?php 
                                $total = $stats['total_antrian'] ?? 0;
                                $selesai = $stats['antrian_selesai'] ?? 0;
                                $persen = $total > 0 ? round(($selesai / $total) * 100) : 0;
                                echo $persen . '%';
                                ?>
                            </h3>
                        </div>
                        <i class="fas fa-percentage fa-3x text-warning opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Counter Status -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-door-open"></i> Status Counter</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php if (!empty($counters)): ?>
                            <?php foreach ($counters as $counter): ?>
                                <?php 
                                $antrian_sekarang = $this->Antrian_model->get_antrian_display($counter['counter_id']);
                                $antrian_menunggu = $this->Antrian_model->get_antrian_menunggu($counter['counter_id']);
                                ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title"><?= $counter['counter_nama']; ?></h6>
                                            <div class="mb-3">
                                                <small class="text-muted">Sedang Dilayani:</small>
                                                <div class="h5 mb-0">
                                                    <?= $antrian_sekarang ? $antrian_sekarang['nomor_antrian'] : '—'; ?>
                                                </div>
                                            </div>
                                            <div>
                                                <small class="text-muted">Menunggu:</small>
                                                <div class="h5 mb-0">
                                                    <?= count($antrian_menunggu); ?> antrian
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light">
                                            <a href="<?= base_url('antrian/operator/' . $counter['counter_id']); ?>" class="btn btn-sm btn-primary btn-block">
                                                <i class="fas fa-headset"></i> Buka Panel Operator
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Belum ada counter yang aktif
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('antrian'); ?>" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-home"></i> Ambil Nomor Antrian
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('antrian/laporan'); ?>" class="btn btn-outline-info btn-block">
                                <i class="fas fa-file-alt"></i> Laporan Antrian
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('antrian/display/1'); ?>" class="btn btn-outline-warning btn-block" target="_blank">
                                <i class="fas fa-tv"></i> Buka Display Panel
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('antrian/operator/1'); ?>" class="btn btn-outline-success btn-block">
                                <i class="fas fa-headset"></i> Panel Operator
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->load->view('layout_admin/footer', $data);
$this->load->view('layout_admin/alert', $data);
?>
