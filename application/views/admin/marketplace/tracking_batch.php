<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style>
    .spk-code {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 900;
        font-size: 1rem;
        color: #000;
        background: #f8fafc;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        letter-spacing: -0.5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        display: inline-block;
    }
</style>

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
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <div class="dashboard-text">
                        <p class="dashboard-subtitle">Modul Produksi</p>
                        <h1 class="dashboard-title">Tracking Batch Deadline</h1>
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
        
        <div class="d-flex justify-content-between align-items-center mb-3 d-print-none">
            <h4 class="mb-0 text-muted"><i class="fas fa-calendar-alt me-2"></i>Daftar Tenggat Waktu Pengiriman</h4>
            <button onclick="location.reload()" class="btn btn-success fw-bold text-white shadow-sm">
                <i class="fas fa-sync-alt me-1"></i> Refresh Data
            </button>
        </div>

        <?php if(empty($batches)): ?>
            <div class="card border-0 shadow-sm border-top border-success border-3">
                <div class="card-body text-center py-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success mb-3" width="64" height="64" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                    <h3 class="mb-2">Semua Aman!</h3>
                    <p class="text-muted">Tidak ada SPK marketplace yang masih pending atau diproses. Seluruh pesanan telah diserahkan atau dikirim.</p>
                </div>
            </div>
        <?php else: ?>
            
            <div class="row row-cards">
            <?php foreach($batches as $date => $spk_list): ?>
                <?php
                    // Hitung H-?
                    $today = date('Y-m-d');
                    $diff = (strtotime($date) - strtotime($today)) / (60 * 60 * 24);
                    
                    if ($date == '0000-00-00' || empty($date)) {
                        $title = "Tidak Ada Deadline";
                        $border_color = "border-secondary";
                        $icon = "🕰️";
                    } elseif ($diff == 0) {
                        $title = "Deadline HARI INI";
                        $border_color = "border-danger";
                        $icon = "🚨";
                    } elseif ($diff == 1) {
                        $title = "Deadline BESOK";
                        $border_color = "border-warning";
                        $icon = "⚠️";
                    } elseif ($diff < 0) {
                        $title = "Melewati Deadline (Terlambat " . abs($diff) . " hari)";
                        $border_color = "border-dark";
                        $icon = "💀";
                    } else {
                        $title = "Deadline " . date('d M Y', strtotime($date));
                        $border_color = "border-info";
                        $icon = "📅";
                    }
                ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm mb-4 border-top <?= $border_color ?> border-3">
                        <div class="card-header border-0 py-3 bg-white">
                            <h3 class="card-title text-dark fw-bold mb-0">
                                <span class="me-2 fs-3"><?= $icon ?></span>
                                <?= $title ?>
                            </h3>
                            <div class="card-actions">
                                <span class="badge bg-light text-secondary border shadow-sm px-2 py-1 fw-bold"><?= count($spk_list) ?> SPK</span>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter table-hover card-table">
                                <thead class="table-light text-muted small text-uppercase fw-bold">
                                    <tr>
                                        <th width="15%">No SPK</th>
                                        <th width="15%">Platform</th>
                                        <th width="20%">Konsumen</th>
                                        <th>Kurir & Resi</th>
                                        <th width="15%">Jam Deadline</th>
                                        <th width="10%">Status</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($spk_list as $row): ?>
                                    <tr>
                                        <td>
                                            <span class="spk-code"><?= $row->nospk ?></span>
                                        </td>
                                        <td>
                                            <?php 
                                                $plat = strtolower($row->marketplace_nama ?: '');
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
                                            <span class="badge" style="<?= $bg_style ?>"><?= strtoupper($row->marketplace_nama ?: 'Lainnya') ?></span>
                                        </td>
                                        <td><?= htmlspecialchars($row->konsumen_nama ?: '-') ?></td>
                                        <td>
                                            <div class="fw-semibold text-primary"><?= $row->nama_pengiriman ?: '-' ?></div>
                                            <div class="text-muted small"><?= $row->resi_marketplace ?: 'Resi belum diinput' ?></div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border px-2 py-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><polyline points="12 7 12 12 15 15" /></svg>
                                                <?= $row->dateline_jam ? date('H:i', strtotime($row->dateline_jam)) : '-' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                                if ($row->status_produksi == 'proses') {
                                                    echo '<span class="badge bg-warning text-dark">Proses</span>';
                                                } elseif ($row->status_produksi == 'on_pb') {
                                                    echo '<span class="badge bg-info text-white">Packing/PB</span>';
                                                } else {
                                                    echo '<span class="badge bg-primary text-white">Approved</span>';
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('marketplace/tracking?spk=' . urlencode($row->nospk)) ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                                Lacak
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
            
        <?php endif; ?>

    </div>
</div>
