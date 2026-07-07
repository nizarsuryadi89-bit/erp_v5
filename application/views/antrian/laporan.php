<?php
$this->load->view('layout_admin/head', $data);
$this->load->view('layout_admin/top_header', $data);
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <h4 class="mb-4"><i class="fas fa-chart-bar"></i> Laporan Antrian</h4>
        </div>
    </div>

    <!-- Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="<?= $tanggal_filter; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Counter</label>
                            <select name="counter_id" class="form-control">
                                <option value="">-- Semua Counter --</option>
                                <?php foreach ($counters as $c): ?>
                                    <option value="<?= $c['counter_id']; ?>" <?= ($counter_filter == $c['counter_id']) ? 'selected' : ''; ?>>
                                        <?= $c['counter_nama']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="<?= base_url('antrian/laporan'); ?>" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-info">
                <div class="card-body">
                    <p class="text-muted mb-2">Total Antrian</p>
                    <h3 class="mb-0"><?= $stats['total_antrian'] ?? 0; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-success">
                <div class="card-body">
                    <p class="text-muted mb-2">Antrian Selesai</p>
                    <h3 class="mb-0"><?= $stats['antrian_selesai'] ?? 0; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-danger">
                <div class="card-body">
                    <p class="text-muted mb-2">Antrian Dibatalkan</p>
                    <h3 class="mb-0"><?= $stats['antrian_batal'] ?? 0; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-warning">
                <div class="card-body">
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
            </div>
        </div>
    </div>

    <!-- Tabel Antrian -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Data Antrian</h5>
                    <button class="btn btn-light btn-sm" onclick="window.print()">
                        <i class="fas fa-print"></i> Cetak
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nomor Antrian</th>
                                    <th>Counter</th>
                                    <th>Waktu Ambil</th>
                                    <th>Waktu Panggil</th>
                                    <th>Waktu Selesai</th>
                                    <th>Status</th>
                                    <th>Operator</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($antrian_list)): ?>
                                    <?php $no = 1; foreach ($antrian_list as $a): ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td>
                                                <strong><?= $a['nomor_antrian']; ?></strong>
                                            </td>
                                            <td><?= $a['counter_nama']; ?></td>
                                            <td><?= date('H:i:s', strtotime($a['waktu_ambil'])); ?></td>
                                            <td>
                                                <?= $a['waktu_panggil'] ? date('H:i:s', strtotime($a['waktu_panggil'])) : '—'; ?>
                                            </td>
                                            <td>
                                                <?= $a['waktu_selesai'] ? date('H:i:s', strtotime($a['waktu_selesai'])) : '—'; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $status_class = '';
                                                $status_text = '';
                                                switch ($a['status']) {
                                                    case 'menunggu':
                                                        $status_class = 'warning';
                                                        $status_text = 'Menunggu';
                                                        break;
                                                    case 'dipanggil':
                                                        $status_class = 'info';
                                                        $status_text = 'Dipanggil';
                                                        break;
                                                    case 'sedang_dilayani':
                                                        $status_class = 'primary';
                                                        $status_text = 'Sedang Dilayani';
                                                        break;
                                                    case 'selesai':
                                                        $status_class = 'success';
                                                        $status_text = 'Selesai';
                                                        break;
                                                    case 'batal':
                                                        $status_class = 'danger';
                                                        $status_text = 'Dibatalkan';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge bg-<?= $status_class; ?>"><?= $status_text; ?></span>
                                            </td>
                                            <td><?= $a['username'] ?? '—'; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2" style="display: block;"></i>
                                            Tidak ada data antrian
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
</div>

<style media="print">
    .card-header button,
    .card:first-of-type,
    .row:first-of-type {
        display: none !important;
    }
    
    .table {
        font-size: 12px;
    }
</style>

<?php
$this->load->view('layout_admin/footer', $data);
$this->load->view('layout_admin/alert', $data);
?>
