<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking SPK - TGM ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .tracking-card { transition: transform 0.2s; }
        .tracking-card:hover { transform: translateY(-2px); }
        .status-badge { font-size: 0.8rem; }
        .progress { height: 25px; }
        .step-indicator { position: relative; }
        .step-indicator::after { 
            content: ''; 
            position: absolute; 
            top: 50%; 
            left: 100%; 
            width: 100%; 
            height: 2px; 
            background: #dee2e6; 
            z-index: 1; 
        }
        .step-indicator:last-child::after { display: none; }
        .step-completed::after { background: #198754; }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
                    <a href="<?= base_url() ?>" class="btn btn-outline-primary mb-2 mb-md-0">
                        <i class="bi bi-arrow-left me-2"></i>Kembali ke Website
                    </a>
                    <h1 class="display-5 fw-bold text-primary mb-0 text-center">
                        <i class="bi bi-search-heart"></i> Tracking SPK
                    </h1>
                    <div class="d-none d-md-block" style="width: 120px;"></div> <!-- Spacer untuk balance -->
                </div>
                <p class="lead text-muted">Pantau progress produksi SPK Anda secara real-time</p>
            </div>
        </div>

        <!-- Search Form -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form method="get" action="" id="searchForm">
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control" name="spk" id="spkInput" 
                                       placeholder="Masukkan nomor SPK..." 
                                       value="<?= htmlspecialchars($search_spk ?? '') ?>" required>
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-search"></i> Cari SPK
                                </button>
                            </div>
                            <div class="form-text text-center mt-2">
                                Contoh: SPK-2024-001 atau 2024001
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($spk_found) && $spk_found): ?>
            <!-- SPK Found - Show Tracking -->
            <div class="row">
                <div class="col-12">
                    <!-- SPK Info Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-primary text-white py-3">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h4 class="mb-1">
                                        <i class="bi bi-file-earmark-text me-2"></i>
                                        SPK #<?= htmlspecialchars($produksi->nospk) ?>
                                    </h4>
                                    <p class="mb-0">Status Tracking Produksi</p>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <span class="badge bg-light text-dark fs-6">
                                        <?= date('d/m/Y', strtotime($produksi->dateline_tgl ?? 'now')) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4">Produk</dt>
                                        <dd class="col-sm-8 fw-semibold"><?= htmlspecialchars($produksi->product_nama ?? '-') ?></dd>
                                        <dt class="col-sm-4">Konsumen</dt>
                                        <dd class="col-sm-8"><?= htmlspecialchars($produksi->konsumen_nama ?? '-') ?></dd>
                                        <dt class="col-sm-4">Qty</dt>
                                        <dd class="col-sm-8">
                                            <?= number_format($produksi->qty ?? 0) ?> 
                                            <?= htmlspecialchars($produksi->satuan_nama ?? '') ?>
                                        </dd>
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4">Deadline</dt>
                                        <dd class="col-sm-8">
                                            <?= date('d/m/Y', strtotime($produksi->dateline_tgl ?? 'now')) ?><br>
                                            <small class="text-muted"><?= $produksi->dateline_jam ?? '' ?></small>
                                        </dd>
                                        <?php if (($produksi->panjang ?? '0.00') !== '0.00' || ($produksi->lebar ?? '0.00') !== '0.00'): ?>
                                        <dt class="col-sm-4">Dimensi</dt>
                                        <dd class="col-sm-8">
                                            <?= $produksi->panjang ?> × <?= $produksi->lebar ?> meter
                                        </dd>
                                        <?php endif; ?>

                                        <?php if (!empty($produksi->marketplace_nama)): ?>
                                        <hr class="my-2 opacity-50">
                                        <dt class="col-sm-4">Platform</dt>
                                        <dd class="col-sm-8">
                                            <span class="badge bg-dark fw-bold"><?= strtoupper($produksi->marketplace_nama) ?></span>
                                        </dd>
                                        <dt class="col-sm-4">Pengiriman</dt>
                                        <dd class="col-sm-8">
                                            <span class="fw-bold text-primary"><?= $produksi->nama_pengiriman ?></span>
                                            <span class="badge bg-info text-dark ms-2"><?= $produksi->batch_pengiriman ?></span>
                                        </dd>
                                        <dt class="col-sm-4">No. Resi</dt>
                                        <dd class="col-sm-8">
                                            <code class="fw-bold fs-6"><?= $produksi->resi_marketplace ?></code>
                                        </dd>
                                        <?php endif; ?>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Overview -->
                    <?php
                        $total_routing = count($detail_produksi);
                        $completed = 0;
                        $in_progress = 0;
                        $in_pb = 0;
                        $delivered = 0;
                        
                        foreach ($detail_produksi as $d) {
                            $status = isset($d->status_produksi) ? (string)$d->status_produksi : '0';
                            if ($status === '2' || $status === '3' || $status === '4') $completed++;
                            if ($status === '1') $in_progress++;
                            if ($status === '3') $in_pb++;
                            if ($status === '4') $delivered++;
                        }
                        
                        $overall_progress = $total_routing > 0 ? round(($completed / $total_routing) * 100) : 0;
                    ?>
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="bi bi-graph-up me-2"></i>Progress Overview
                            </h5>
                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <div class="h4 text-primary mb-1"><?= $overall_progress ?>%</div>
                                        <small class="text-muted">Progress Keseluruhan</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <div class="h5 text-success mb-1"><?= $completed ?></div>
                                        <small class="text-muted">Selesai</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <div class="h5 text-warning mb-1"><?= $in_progress ?></div>
                                        <small class="text-muted">Sedang Proses</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <div class="h5 text-info mb-1"><?= $in_pb ?></div>
                                        <small class="text-muted">Di PB</small>
                                    </div>
                                </div>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: <?= $completed > 0 ? ($completed/$total_routing)*100 : 0 ?>%">
                                    <?= $completed ?> Selesai
                                </div>
                                <div class="progress-bar bg-warning" style="width: <?= $in_progress > 0 ? ($in_progress/$total_routing)*100 : 0 ?>%">
                                    <?= $in_progress ?> Proses
                                </div>
                                <div class="progress-bar bg-info" style="width: <?= $in_pb > 0 ? ($in_pb/$total_routing)*100 : 0 ?>%">
                                    <?= $in_pb ?> PB
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Routing Steps -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0">
                                <i class="bi bi-list-ol me-2"></i>Detail Proses Produksi
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="80">Step</th>
                                            <th>Proses</th>
                                            <th>Operator/Mesin</th>
                                            <th>Status</th>
                                            <th>Progress</th>
                                            <th>Waktu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($detail_produksi as $d): ?>
                                        <tr class="tracking-card">
                                            <td class="text-center">
                                                <div class="step-indicator <?= (isset($d->status_produksi) && $d->status_produksi >= '2') ? 'step-completed' : '' ?>">
                                                    <span class="badge bg-secondary rounded-circle p-2">
                                                        <?= $d->routing_urutan ?? '?' ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold"><?= htmlspecialchars($d->routing_name ?? 'N/A') ?></div>
                                                <?php if (!empty($d->alasan_missing)): ?>
                                                    <div class="small text-muted">
                                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                                        <?= htmlspecialchars($d->alasan_missing) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="small">
                                                    <div class="fw-semibold"><?= htmlspecialchars($d->operator_nama ?? '-') ?></div>
                                                    <div class="text-muted"><?= htmlspecialchars($d->mesin_nama ?? '-') ?></div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                    $statusKode = isset($d->status_produksi) ? (string)$d->status_produksi : '0';
                                                    if ($statusKode === '4') {
                                                        echo '<span class="badge bg-dark status-badge"><i class="bi bi-check-circle me-1"></i>Diserahkan</span>';
                                                    } elseif ($statusKode === '3') {
                                                        echo '<span class="badge bg-primary status-badge"><i class="bi bi-box-seam me-1"></i>Di PB</span>';
                                                    } elseif ($statusKode === '2') {
                                                        echo '<span class="badge bg-success status-badge"><i class="bi bi-check-circle me-1"></i>Selesai</span>';
                                                    } elseif ($statusKode === '1') {
                                                        echo '<span class="badge bg-warning status-badge"><i class="bi bi-gear me-1"></i>Proses</span>';
                                                    } else {
                                                        echo '<span class="badge bg-secondary status-badge"><i class="bi bi-clock me-1"></i>Menunggu</span>';
                                                    }
                                                ?>
                                            </td>
                                            <td>
                                                <?php if (isset($d->qty_output) && isset($d->qty_input) && $d->qty_input > 0): ?>
                                                    <?php 
                                                        $qty_progress = round(($d->qty_output / $d->qty_input) * 100);
                                                        $progress_class = $qty_progress >= 100 ? 'bg-success' : ($qty_progress >= 50 ? 'bg-warning' : 'bg-info');
                                                    ?>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar <?= $progress_class ?>" style="width: <?= $qty_progress ?>%">
                                                            <?= $qty_progress ?>%
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">
                                                        <?= number_format($d->qty_output) ?> / <?= number_format($d->qty_input) ?>
                                                    </small>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="small">
                                                    <?php if ($d->waktu_mulai): ?>
                                                        <div>Mulai: <?= date('d/m H:i', strtotime($d->waktu_mulai)) ?></div>
                                                    <?php endif; ?>
                                                    <?php if ($d->waktu_selesai): ?>
                                                        <div>Selesai: <?= date('d/m H:i', strtotime($d->waktu_selesai)) ?></div>
                                                    <?php endif; ?>
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
            </div>

        <?php elseif (isset($spk_found) && !$spk_found && !empty($search_spk)): ?>
            <!-- SPK Not Found -->
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                            <h4 class="mt-3 text-danger">SPK Tidak Ditemukan</h4>
                            <p class="text-muted"><?= htmlspecialchars($error_message ?? 'SPK yang Anda cari tidak ditemukan dalam sistem.') ?></p>
                                                         <div class="mt-3">
                                 <a href="<?= base_url('tracking/spk') ?>" class="btn btn-primary">
                                     <i class="bi bi-search me-1"></i>Cari SPK Lain
                                 </a>
                             </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Initial State - Welcome -->
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-search-heart text-primary" style="font-size: 4rem;"></i>
                            <h4 class="mt-3">Selamat Datang di Tracking SPK</h4>
                            <p class="text-muted">
                                Masukkan nomor SPK Anda di atas untuk melihat progress produksi secara real-time.
                            </p>
                            <div class="row g-3 mt-4">
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <i class="bi bi-clock-history text-info" style="font-size: 2rem;"></i>
                                        <h6 class="mt-2">Real-time Updates</h6>
                                        <small class="text-muted">Progress produksi yang selalu up-to-date</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <i class="bi bi-graph-up text-success" style="font-size: 2rem;"></i>
                                        <h6 class="mt-2">Progress Tracking</h6>
                                        <small class="text-muted">Pantau setiap tahap produksi</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <i class="bi bi-phone text-primary" style="font-size: 2rem;"></i>
                                        <h6 class="mt-2">Mobile Friendly</h6>
                                        <small class="text-muted">Akses dari mana saja</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Footer -->
        <div class="row mt-5">
            <div class="col-12 text-center">
                <p class="text-muted">
                    <small>&copy; <?= date('Y') ?> TGM ERP. Sistem Tracking SPK Produksi.</small>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-focus on SPK input
        document.addEventListener('DOMContentLoaded', function() {
            const spkInput = document.getElementById('spkInput');
            if (spkInput) {
                spkInput.focus();
            }
        });

        // Form validation
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            const spkValue = document.getElementById('spkInput').value.trim();
            if (!spkValue) {
                e.preventDefault();
                alert('Masukkan nomor SPK terlebih dahulu.');
                return false;
            }
        });
    </script>
</body>
</html>
