<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- PREMIUM STYLE -->
<style>
    :root { --marketplace-gradient: linear-gradient(135deg, #03AC0E 0%, #18181b 35%, #18181b 65%, #EE4D2D 100%); }
    .marketplace-dashboard { background: var(--marketplace-gradient); color: white; padding: 1.5rem 0 2rem 0; margin-bottom: 2rem; border-radius: 0 0 24px 24px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08); position: relative; overflow: hidden; }
    .marketplace-dashboard::after { content: ''; position: absolute; top: -50%; right: -10%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%); border-radius: 50%; z-index: 1; }
    .dashboard-content { display: flex; align-items: center; gap: 1.25rem; position: relative; z-index: 2; }
    .dashboard-icon { width: 52px; height: 52px; background: rgba(255, 255, 255, 0.12); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); color: white; }
    .dashboard-title { font-size: 1.75rem; font-weight: 900; margin: 0; letter-spacing: -0.5px; line-height: 1.2; color: white; }
    .dashboard-subtitle { font-size: 0.85rem; font-weight: 700; margin: 0 0 2px 0; opacity: 0.7; color: #e2e8f0; display: flex; align-items: center; gap: 10px; }
    .dashboard-subtitle::before { content: ''; width: 12px; height: 2px; background: #93c5fd; display: inline-block; }
    .btn-premium { border-radius: 10px; padding: 0.45rem 1rem; font-weight: 800; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px; transition: all 0.3s; text-decoration: none !important; display: inline-flex; align-items: center; justify-content: center; gap: 6px; }
    @media (max-width: 768px) {
        .marketplace-dashboard { padding: 2.5rem 0; border-radius: 0 0 25px 25px; }
        .dashboard-content { flex-direction: column; text-align: center; gap: 1.25rem; }
        .dashboard-icon { width: 70px; height: 70px; font-size: 2.2rem; border-radius: 20px; }
        .dashboard-title { font-size: 1.75rem; margin-bottom: 0.25rem; }
        .dashboard-subtitle { justify-content: center; font-size: 1rem; }
        .header-actions-mobile { justify-content: center !important; margin-top: 1rem; }
    }
</style>

<!-- PAGE HEADER -->
<div class="marketplace-dashboard d-print-none">
    <div class="container-xl px-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="dashboard-content">
                    <div class="dashboard-icon">
                        <i class="fas fa-search-location"></i>
                    </div>
                    <div class="dashboard-text">
                        <p class="dashboard-subtitle">Modul Transaksi</p>
                        <h1 class="dashboard-title">Tracking SPK</h1>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-end d-flex gap-2 justify-content-md-end justify-content-center header-actions-mobile flex-wrap position-relative mt-3 mt-md-0" style="z-index: 2;">
                <?php $this->load->view('admin/marketplace/_header_menu'); ?>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Search Form -->
        <div class="row justify-content-center mb-4">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form method="get" action="" id="searchForm">
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control" name="spk" id="spkInput" 
                                       placeholder="Masukkan nomor SPK Marketplace..." 
                                       value="<?= htmlspecialchars($search_spk ?? '') ?>" required>
                                <button class="btn btn-primary" type="submit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="10" cy="10" r="7" /><line x1="21" y1="21" x2="15" y2="15" /></svg>
                                    Lacak Pesanan
                                </button>
                            </div>
                            <div class="form-text text-center mt-2 text-muted">
                                Pencarian ini dibatasi hanya untuk transaksi dari platform Marketplace (Tokopedia, Shopee, dll).
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($spk_found) && $spk_found): ?>
            <!-- SPK Found - Show Tracking -->
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- SPK Info Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-primary text-white border-0 py-3 d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0 text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /></svg>
                                SPK #<?= htmlspecialchars($produksi->nospk) ?>
                            </h4>
                            <span class="badge bg-white text-primary fs-6">
                                <?= date('d/m/Y', strtotime($produksi->dateline_tgl ?? 'now')) ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6 border-end">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4 text-muted">Produk</dt>
                                        <dd class="col-sm-8 fw-semibold"><?= htmlspecialchars($produksi->product_nama ?? '-') ?></dd>
                                        <dt class="col-sm-4 text-muted">Konsumen</dt>
                                        <dd class="col-sm-8"><?= htmlspecialchars($produksi->konsumen_nama ?? '-') ?></dd>
                                        <dt class="col-sm-4 text-muted">Qty</dt>
                                        <dd class="col-sm-8">
                                            <?= number_format($produksi->qty ?? 0) ?> 
                                            <?= htmlspecialchars($produksi->satuan_nama ?? '') ?>
                                        </dd>
                                    </dl>
                                </div>
                                <div class="col-md-6 ps-4">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4 text-muted">Platform</dt>
                                        <dd class="col-sm-8">
                                            <?php 
                                                $plat = strtolower($produksi->marketplace_nama ?: '');
                                                $bg_style = 'background-color: #007bff;';
                                                if (strpos($plat, 'tokopedia') !== false) {
                                                    $bg_style = 'background-color: #42b549 !important; color: #fff;';
                                                } elseif (strpos($plat, 'shopee') !== false) {
                                                    $bg_style = 'background-color: #ee4d2d !important; color: #fff;';
                                                } elseif (strpos($plat, 'tiktok') !== false) {
                                                    $bg_style = 'background-color: #000000 !important; color: #fff;';
                                                } elseif (strpos($plat, 'lazada') !== false) {
                                                    $bg_style = 'background-color: #0f146d !important; color: #fff;';
                                                }
                                            ?>
                                            <span class="badge" style="<?= $bg_style ?>"><?= strtoupper($produksi->marketplace_nama) ?></span>
                                        </dd>
                                        <dt class="col-sm-4 text-muted">Kurir</dt>
                                        <dd class="col-sm-8 fw-semibold text-primary">
                                            <?= $produksi->nama_pengiriman ?: '-' ?>
                                        </dd>
                                        <dt class="col-sm-4 text-muted">No. Resi</dt>
                                        <dd class="col-sm-8">
                                            <code class="fw-bold fs-6 text-dark"><?= $produksi->resi_marketplace ?: '-' ?></code>
                                        </dd>
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
                            <h5 class="card-title mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="19" x2="20" y2="19" /><polyline points="4 15 8 9 12 11 16 6 20 10" /></svg>
                                Progress Produksi
                            </h5>
                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <div class="h2 text-primary fw-bold mb-1"><?= $overall_progress ?>%</div>
                                        <div class="text-muted small">Progress Keseluruhan</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <div class="h3 text-success fw-bold mb-1"><?= $completed ?></div>
                                        <div class="text-muted small">Selesai</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <div class="h3 text-warning fw-bold mb-1"><?= $in_progress ?></div>
                                        <div class="text-muted small">Sedang Proses</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <div class="h3 text-info fw-bold mb-1"><?= $in_pb ?></div>
                                        <div class="text-muted small">Diserahkan / Packing</div>
                                    </div>
                                </div>
                            </div>
                            <div class="progress progress-lg" style="height: 1.5rem;">
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
                        <div class="card-header bg-transparent border-0 py-3">
                            <h4 class="card-title mb-0">Rincian Tahapan</h4>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-vcenter table-hover table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" width="80">Tahap</th>
                                            <th>Proses</th>
                                            <th>Operator/Mesin</th>
                                            <th>Status</th>
                                            <th>Progress</th>
                                            <th>Waktu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($detail_produksi as $d): ?>
                                        <tr>
                                            <td class="text-center">
                                                <span class="avatar avatar-sm <?= (isset($d->status_produksi) && $d->status_produksi >= '2') ? 'bg-success text-white' : 'bg-secondary text-white' ?> rounded-circle">
                                                    <?= $d->routing_urutan ?? '?' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-dark"><?= htmlspecialchars($d->routing_name ?? 'N/A') ?></div>
                                                <?php if (!empty($d->alasan_missing)): ?>
                                                    <div class="small text-danger mt-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" /></svg>
                                                        <?= htmlspecialchars($d->alasan_missing) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="small">
                                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($d->operator_nama ?? '-') ?></div>
                                                    <div class="text-muted"><?= htmlspecialchars($d->mesin_nama ?? '-') ?></div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                    $statusKode = isset($d->status_produksi) ? (string)$d->status_produksi : '0';
                                                    if ($statusKode === '4') {
                                                        echo '<span class="badge bg-dark-lt text-dark">Diserahkan</span>';
                                                    } elseif ($statusKode === '3') {
                                                        echo '<span class="badge bg-primary-lt text-primary">Di PB</span>';
                                                    } elseif ($statusKode === '2') {
                                                        echo '<span class="badge bg-success-lt text-success">Selesai</span>';
                                                    } elseif ($statusKode === '1') {
                                                        echo '<span class="badge bg-warning-lt text-warning">Proses</span>';
                                                    } else {
                                                        echo '<span class="badge bg-secondary-lt text-secondary">Menunggu</span>';
                                                    }
                                                ?>
                                            </td>
                                            <td>
                                                <?php if (isset($d->qty_output) && isset($d->qty_input) && $d->qty_input > 0): ?>
                                                    <?php 
                                                        $qty_progress = round(($d->qty_output / $d->qty_input) * 100);
                                                        $progress_class = $qty_progress >= 100 ? 'bg-success' : ($qty_progress >= 50 ? 'bg-warning' : 'bg-info');
                                                    ?>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress flex-grow-1" style="height: 6px;">
                                                            <div class="progress-bar <?= $progress_class ?>" style="width: <?= $qty_progress ?>%"></div>
                                                        </div>
                                                        <div class="ms-2 small text-muted"><?= $qty_progress ?>%</div>
                                                    </div>
                                                    <div class="small text-muted mt-1">
                                                        <?= number_format($d->qty_output) ?> / <?= number_format($d->qty_input) ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="small text-muted">
                                                    <?php if ($d->waktu_mulai): ?>
                                                        <div><strong class="text-dark">Mulai:</strong> <?= date('d/m H:i', strtotime($d->waktu_mulai)) ?></div>
                                                    <?php endif; ?>
                                                    <?php if ($d->waktu_selesai): ?>
                                                        <div><strong class="text-dark">Selesai:</strong> <?= date('d/m H:i', strtotime($d->waktu_selesai)) ?></div>
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
                    <div class="card border-0 shadow-sm border-top border-warning border-3">
                        <div class="card-body text-center py-5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-warning mb-3" width="64" height="64" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" /></svg>
                            <h3 class="mb-2">SPK Tidak Ditemukan</h3>
                            <p class="text-muted"><?= htmlspecialchars($error_message ?? 'SPK yang Anda cari tidak ditemukan dalam sistem atau bukan transaksi marketplace.') ?></p>
                            <div class="mt-4">
                                <a href="<?= base_url('marketplace/tracking') ?>" class="btn btn-primary">
                                    Coba Cari Lagi
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
                    <div class="card border-0 shadow-sm bg-primary text-white">
                        <div class="card-body text-center py-5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-3 text-white" width="64" height="64" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /><path d="M10 13v.01" /><path d="M10 7v3" /></svg>
                            <h3 class="mb-2 text-white">Tracking Produksi Marketplace</h3>
                            <p class="text-white-50 fs-5 mb-0">
                                Gunakan kolom di atas untuk melacak SPK dari pesanan Shopee, Tokopedia, TikTok, dsb.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const spkInput = document.getElementById('spkInput');
        if (spkInput && !spkInput.value) {
            spkInput.focus();
        }
    });
</script>
