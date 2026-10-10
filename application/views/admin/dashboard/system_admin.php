<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;300;400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap');

    :root {
        --fin-primary: #0f172a;
        --fin-accent: #3b82f6;
        --fin-success: #10b981;
        --fin-danger: #ef4444;
        --fin-warning: #f59e0b;
        --fin-slate: #64748b;
        --glass-bg: rgba(255, 255, 255, 0.8);
        --glass-border: rgba(255, 255, 255, 0.2);
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #f8fafc;
    }

    .fin-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    .fin-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .bg-soft-blue {
        background: #eff6ff;
        color: #3b82f6;
    }

    .bg-soft-green {
        background: #ecfdf5;
        color: #10b981;
    }

    .bg-soft-red {
        background: #fef2f2;
        color: #ef4444;
    }

    .bg-soft-amber {
        background: #fffbeb;
        color: #f59e0b;
    }

    .currency {
        font-family: 'JetBrains Mono', monospace;
        letter-spacing: -1px;
    }

    .quick-action-btn {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        border: 1px solid #e2e8f0;
        text-decoration: none !important;
        transition: all 0.2s;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .quick-action-btn:hover {
        background: var(--fin-accent);
        border-color: var(--fin-accent);
    }

    .quick-action-btn:hover i,
    .quick-action-btn:hover span {
        color: white !important;
    }

    .quick-action-btn i {
        font-size: 1.75rem;
        color: var(--fin-slate);
    }

    .quick-action-btn span {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--fin-primary);
        text-transform: uppercase;
        text-align: center;
    }

    /* TikTok Shop Inspired Performance Section */
    .perf-section-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 4px;
    }

    .perf-section-sub {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 500;
    }

    .perf-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        padding: 1.5rem;
    }

    .perf-metric-item {
        border-right: 1px solid #f1f5f9;
        padding: 0 1.5rem;
    }

    .perf-metric-item:last-child {
        border-right: none;
    }

    .perf-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .perf-value {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        font-family: 'JetBrains Mono', monospace;
        margin-bottom: 4px;
    }

    .perf-trend {
        font-size: 0.75rem;
        font-weight: 700;
    }

    .trend-up { color: #10b981; }
    .trend-down { color: #ef4444; }

    .perf-filter-select {
        border: none;
        background: transparent;
        font-weight: 700;
        font-size: 0.9rem;
        color: #1e293b;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 8px;
    }

    .perf-filter-select:hover {
        background: #f1f5f9;
    }

    .table-custom {
        font-size: 0.85rem;
    }

    .table-custom thead th {
        background: #f1f5f9;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border: none;
        padding: 12px 15px;
    }

    .table-custom tbody td {
        padding: 12px 15px;
        vertical-align: middle;
        border-color: #f1f5f9;
    }

    .badge-soft {
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
    }

    .nav-hub {
        background: #f1f5f9;
        border-radius: 24px;
        padding: 2rem;
    }

    .module-card {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        border: 1px solid transparent;
        transition: all 0.2s;
    }

    .module-card:hover {
        border-color: var(--fin-accent);
        background: #fdfdfd;
    }

    .ad-glow {
        animation: glow 2s infinite;
    }

    @keyframes glow {
        0% {
            opacity: 0.5;
        }

        50% {
            opacity: 1;
            text-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
        }

        100% {
            opacity: 0.5;
        }
    }
</style>

<?php
// FINANCIAL LOGIC - Simplified (Data now passed from Controller)
$user_role_id = $this->session->userdata('level');
$this_username = $this->session->userdata('username');
$is_developer = !empty($is_developer) ? $is_developer : ($this_username === 'nizarsuryadi');

// Realtime SPK Transactions
$recent_spk = $this->db->select('s.nospk, s.spk_time, k.konsumen_nama, pr.status_produksi, p.total_tagihan, p.total_bayar')
    ->from('tbl_spk s')
    ->join('tbl_konsumen k', 's.konsumen_id = k.konsumen_id', 'left')
    ->join('tbl_produksi pr', 's.nospk = pr.nospk', 'left')
    ->join('tbl_pembayaran p', 's.nospk = p.nospk', 'left')
    ->group_by('s.nospk')
    ->order_by('s.id', 'DESC')
    ->limit(7)
    ->get()->result();
?>

<div class="app-content mt-n4 pb-5">
    <div class="container-fluid">
        <!-- Dashboard Header -->
        <div class="row align-items-center mb-4 pt-4">
            <div class="col-md-7">
                <span class="badge bg-soft-blue text-uppercase fw-800 px-3 py-2 mb-2">Pusat Kendali Keuangan</span>
                <h3 class="fw-900 text-dark mb-1">
                    Halo, <?= $this->session->userdata('username') ?>! 👋
                </h3>
                <p class="text-muted fw-bold mb-0">
                    Berikut adalah ringkasan kesehatan finansial perusahaan hari ini.
                </p>
            </div>
            <div class="col-md-5 text-md-end">
                <div class="d-inline-flex flex-column align-items-end">
                    <div class="text-dark fw-800 h5 mb-0"><?= date('l, d F Y') ?></div>
                    <div class="text-success small fw-bold"><i class="fas fa-circle ad-glow me-1"></i> Sistem Sinkron
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6">
                <div class="fin-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stat-icon bg-soft-blue">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <span class="text-muted small fw-bold">HARI INI</span>
                    </div>
                    <h5 class="text-slate fw-bold mb-1 small uppercase">Pemasukan Tunai</h5>
                    <h3 class="fw-900 currency mb-0">Rp <?= number_format($income_today, 0, ',', '.') ?></h3>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="fin-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stat-icon bg-soft-green">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <span class="text-muted small fw-bold">BULAN INI</span>
                    </div>
                    <h5 class="text-slate fw-bold mb-1 small uppercase">Omzet Berjalan</h5>
                    <h3 class="fw-900 currency mb-0">Rp <?= number_format($omzet_bulan, 0, ',', '.') ?></h3>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="fin-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stat-icon bg-soft-amber">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <span class="text-muted small fw-bold">GLOBAL</span>
                    </div>
                    <h5 class="text-slate fw-bold mb-1 small uppercase">Piutang Tertahan</h5>
                    <h3 class="fw-900 currency mb-0">Rp <?= number_format($piutang_total, 0, ',', '.') ?></h3>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="fin-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stat-icon bg-soft-red">
                            <i class="bi bi-cart-dash"></i>
                        </div>
                        <span class="text-muted small fw-bold">BULAN INI</span>
                    </div>
                    <h5 class="text-slate fw-bold mb-1 small uppercase">Biaya Operasional</h5>
                    <h3 class="fw-900 currency mb-0">Rp <?= number_format($expense_bulan, 0, ',', '.') ?></h3>
                </div>
            </div>
        </div>

        <!-- TikTok Shop Inspired Performance Panel -->
        <div class="mb-5">
            <div class="row align-items-end mb-3">
                <div class="col-md-8">
                    <h5 class="perf-section-title">Tinjau performa toko Anda</h5>
                    <p class="perf-section-sub mb-0">Ringkasan data metrik utama untuk memahami performa toko dan mengembangkan usaha Anda.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-inline-flex align-items-center bg-white border rounded-pill px-3 py-1 shadow-sm">
                        <i class="bi bi-calendar3 text-muted me-2 small"></i>
                        <select class="perf-filter-select" id="perfRangeSelect">
                            <option value="7days" <?= $perf_range == '7days' ? 'selected' : '' ?>>7 hari terakhir</option>
                            <option value="30days" <?= $perf_range == '30days' ? 'selected' : '' ?>>30 hari terakhir</option>
                            <option value="today" <?= $perf_range == 'today' ? 'selected' : '' ?>>Hari ini</option>
                        </select>
                    </div>
                </div>
            </div>

            <script>
                document.getElementById('perfRangeSelect').addEventListener('change', function() {
                    const range = this.value;
                    const url = new URL(window.location.href);
                    url.searchParams.set('perf_range', range);
                    window.location.href = url.href;
                });
            </script>

            <div class="perf-card shadow-sm">
                <div class="row g-0">
                    <!-- GMV -->
                    <div class="col-md-3 perf-metric-item">
                        <div class="perf-label">GMV <i class="bi bi-question-circle small opacity-50"></i></div>
                        <div class="perf-value">Rp <?= number_format($perf_gmv['value'], 0, ',', '.') ?></div>
                        <div class="perf-trend <?= $perf_gmv['growth'] >= 0 ? 'trend-up' : 'trend-down' ?>">
                            <i class="bi bi-caret-<?= $perf_gmv['growth'] >= 0 ? 'up' : 'down' ?>-fill"></i> 
                            <?= abs($perf_gmv['growth']) ?>%
                        </div>
                    </div>

                    <!-- Pembeli -->
                    <div class="col-md-3 perf-metric-item">
                        <div class="perf-label">Pembeli <i class="bi bi-question-circle small opacity-50"></i></div>
                        <div class="perf-value"><?= number_format($perf_cust['value'], 0, ',', '.') ?></div>
                        <div class="perf-trend <?= $perf_cust['growth'] >= 0 ? 'trend-up' : 'trend-down' ?>">
                            <i class="bi bi-caret-<?= $perf_cust['growth'] >= 0 ? 'up' : 'down' ?>-fill"></i> 
                            <?= abs($perf_cust['growth']) ?>%
                        </div>
                    </div>

                    <!-- Pesanan SKU -->
                    <div class="col-md-3 perf-metric-item">
                        <div class="perf-label">Pesanan SKU <i class="bi bi-question-circle small opacity-50"></i></div>
                        <div class="perf-value"><?= number_format($perf_sku['value'], 0, ',', '.') ?></div>
                        <div class="perf-trend <?= $perf_sku['growth'] >= 0 ? 'trend-up' : 'trend-down' ?>">
                            <i class="bi bi-caret-<?= $perf_sku['growth'] >= 0 ? 'up' : 'down' ?>-fill"></i> 
                            <?= abs($perf_sku['growth']) ?>%
                        </div>
                    </div>

                    <!-- Pengunjung -->
                    <div class="col-md-3 perf-metric-item">
                        <div class="perf-label">Kunjungan & Ops <i class="bi bi-question-circle small opacity-50"></i></div>
                        <div class="perf-value"><?= number_format($perf_visit['value'], 0, ',', '.') ?></div>
                        <div class="perf-trend <?= $perf_visit['growth'] >= 0 ? 'trend-up' : 'trend-down' ?>">
                            <i class="bi bi-caret-<?= $perf_visit['growth'] >= 0 ? 'up' : 'down' ?>-fill"></i> 
                            <?= abs($perf_visit['growth']) ?>%
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access & Recent Data -->
        <div class="row g-4 mb-5">
            <!-- Left: Quick Actions -->
            <div class="col-lg-4">
                <h5 class="fw-800 text-dark mb-3"><i class="bi bi-lightning-charge-fill text-warning"></i> Akses Cepat
                </h5>
                <div class="row g-3">
                    <div class="col-6">
                        <a href="<?= base_url('product') ?>" class="quick-action-btn shadow-sm">
                            <i class="bi bi-tools text-primary"></i>
                            <span>Manufaktur</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="<?= base_url('laporan/pemasukan_harian') ?>" class="quick-action-btn shadow-sm">
                            <i class="bi bi-file-earmark-bar-graph text-success"></i>
                            <span>Rekap Harian</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="<?= base_url('pengeluaran') ?>" class="quick-action-btn shadow-sm">
                            <i class="bi bi-plus-circle text-danger"></i>
                            <span>Input Pengeluaran</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="<?= base_url('transaksi/daftar_piutang') ?>" class="quick-action-btn shadow-sm">
                            <i class="bi bi-credit-card-2-front text-info"></i>
                            <span>Daftar Piutang</span>
                        </a>
                    </div>
                    <!-- Row 2 -->

                    <div class="col-6">
                        <a href="<?= base_url('konsumen') ?>" class="quick-action-btn shadow-sm">
                            <i class="bi bi-people text-primary"></i>
                            <span>Database Konsumen</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="<?= base_url('warehouse') ?>" class="quick-action-btn shadow-sm">
                            <i class="bi bi-box-seam text-secondary"></i>
                            <span>Stok & Gudang</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="<?= base_url('laporan/laba_rugi') ?>" class="quick-action-btn shadow-sm">
                            <i class="bi bi-pie-chart text-success"></i>
                            <span>Laba Rugi</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="<?= base_url('administrative/transaksi') ?>" class="quick-action-btn shadow-sm">
                            <i class="bi bi-shield-check text-info"></i>
                            <span>Audit Transaksi</span>
                        </a>
                    </div>
                    <div class="col-12 mt-3">
                        <a href="javascript:void(0)" onclick="$('#modalCekStok').modal('show')" class="quick-action-btn shadow-sm" style="border-color: #10b981; background: #ecfdf5; flex-direction: row; justify-content: start; gap: 15px; padding: 1rem 1.5rem;">
                            <i class="bi bi-box-seam text-success" style="font-size: 1.5rem;"></i>
                            <span class="text-success" style="font-size: 0.95rem; font-weight: 800;">CEK STOK GUDANG (LIVE)</span>
                        </a>
                    </div>
                </div>

                <div class="fin-card mt-4 p-4 bg-primary text-white border-0">
                    <h6 class="fw-bold mb-2">Informasi Kurs & Pajak</h6>
                    <p class="small mb-0 opacity-75">Seluruh perhitungan menggunakan mata uang IDR dan sudah termasuk
                        potongan biaya admin (bank/e-wallet).</p>
                    <hr class="opacity-25">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small fw-bold">Metode Terpopuler:</span>
                        <span class="badge bg-white text-primary fw-bold">QRIS / TRANSFER</span>
                    </div>
                </div>
            </div>

            <!-- Right: Audit Trail -->
            <div class="col-lg-8">
                <div class="fin-card h-100">
                    <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="fw-800 text-dark mb-0"><i class="bi bi-shield-check text-success"></i> Transaksi Realtime (SPK)</h5>
                        <a href="<?= base_url('laporan/rekap_spk') ?>"
                            class="btn btn-sm btn-light fw-bold text-muted">Lihat Semua</a>
                    </div>
                    <div class="p-0">
                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th>Waktu Transaksi</th>
                                        <th>Keterangan / Konsumen</th>
                                        <th>Status Produksi</th>
                                        <th class="text-end">Nominal Tagihan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_spk as $spk): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark">
                                                    <?= date('d/m/Y', strtotime($spk->spk_time)) ?>
                                                </div>
                                                <div class="text-muted extra-small"><?= date('H:i', strtotime($spk->spk_time)) ?></div>
                                            </td>
                                            <td>
                                                <div class="fw-bold"><?= $spk->konsumen_nama ?: 'Pelanggan Umum' ?></div>
                                                <div class="text-muted small italic">SPK: <?= $spk->nospk ?></div>
                                            </td>
                                            <td>
                                                <?php 
                                                $status = $spk->status_produksi;
                                                if($status == 'done') echo '<span class="badge-soft bg-soft-green">Selesai</span>';
                                                elseif($status == 'proses') echo '<span class="badge-soft bg-soft-amber">Di Proses</span>';
                                                elseif($status == 'approved') echo '<span class="badge-soft bg-soft-blue">Antri Produksi</span>';
                                                elseif($status == 'on_pb') echo '<span class="badge-soft bg-soft-amber">Di Packing</span>';
                                                elseif($status == 'on_costumer') echo '<span class="badge-soft bg-soft-green">Diserahkan</span>';
                                                else echo '<span class="badge-soft bg-light text-muted">Pending</span>';
                                                ?>
                                            </td>
                                            <td class="text-end fw-bold currency text-dark">Rp
                                                <?= number_format((float)$spk->total_tagihan, 0, ',', '.') ?>
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


        <!-- ============================================================ -->
        <!-- SYSTEM PERMISSIONS PANEL -->
        <!-- ============================================================ -->
        <!-- ============================================================ -->
        <!-- ACTIVE SESSIONS MONITOR -->
        <!-- ============================================================ -->
        <div class="row g-4 mb-5">
            <div class="col-lg-12">
                <div class="fin-card">
                    <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-800 text-dark mb-1"><i class="bi bi-people-fill text-success me-2"></i>Monitor Sesi Aktif</h5>
                            <p class="text-muted small mb-0">Daftar pengguna yang sedang terhubung ke sistem saat ini.</p>
                        </div>
                        <button onclick="location.reload()" class="btn btn-sm btn-soft-blue fw-bold">
                            <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                        </button>
                    </div>
                    <div class="p-0">
                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th>Pengguna</th>
                                        <th>Role / Level</th>
                                        <th>Waktu Login</th>
                                        <th>Status Perangkat</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // Ambil sesi login hari ini (indikator user aktif)
                                    $hari_ini = date('Y-m-d');
                                    $active_users = $this->db->select('ul.*, u.username, k.nama_lengkap, k.foto, r.role_name')
                                        ->from('user_log ul')
                                        ->join('users u', 'ul.user_id = u.user_id')
                                        ->join('tbl_karyawan k', 'u.karyawan_id = k.karyawan_id', 'left')
                                        ->join('roles r', 'u.role_id = r.role_id', 'left')
                                        ->where('ul.login_time >=', $hari_ini . ' 00:00:00')
                                        ->order_by('ul.login_time', 'DESC')
                                        ->get()->result();

                                    if(empty($active_users)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted small">Tidak ada sesi aktif terdeteksi.</td>
                                        </tr>
                                    <?php endif;

                                    foreach ($active_users as $au): 
                                        $is_me = ($au->user_id == $this->session->userdata('id'));
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="<?= base_url('assets/images/karyawan/' . ($au->foto ?: 'default.png')) ?>" 
                                                         class="rounded-circle border" width="32" height="32" alt="" loading="lazy">
                                                    <div>
                                                        <div class="fw-bold text-dark"><?= $au->nama_lengkap ?></div>
                                                        <div class="extra-small text-muted">@<?= $au->username ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-light text-primary"><?= $au->role_name ?></span></td>
                                            <?php
                                                $waktu_login = strtotime($au->login_time);
                                                $selisih = time() - $waktu_login;
                                                $jam = floor($selisih / 3600);
                                                $menit = floor(($selisih % 3600) / 60);
                                                
                                                if($jam > 0) {
                                                    $waktu_teks = $jam . ' jam ' . $menit . ' mnt lalu';
                                                } elseif($menit > 0) {
                                                    $waktu_teks = $menit . ' mnt lalu';
                                                } else {
                                                    $waktu_teks = 'Baru saja';
                                                }
                                            ?>
                                            <td>
                                                <div class="small fw-bold text-dark"><?= date('H:i', $waktu_login) ?> WIB</div>
                                                <div class="extra-small text-success fw-bold"><i class="bi bi-clock-history"></i> <?= $waktu_teks ?></div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi bi-laptop fs-6 text-muted"></i>
                                                    <span class="extra-small fw-bold text-muted"><?= $au->ip_address ?></span>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <?php if(!$is_me): ?>
                                                    <button onclick="terminateSession(<?= $au->id ?>, '<?= $au->nama_lengkap ?>')" 
                                                            class="btn btn-sm btn-outline-danger px-3 py-1 fw-bold rounded-pill" style="font-size: 0.65rem;">
                                                        PAKSA LOGOUT
                                                    </button>
                                                <?php else: ?>
                                                    <span class="badge bg-soft-green text-success px-3 py-2">SESI ANDA</span>
                                                <?php endif; ?>
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

        <!-- ============================================================ -->
        <!-- SYSTEM PERMISSIONS PANEL -->
        <!-- ============================================================ -->

        <div class="row g-4 mb-5">
            <div class="col-12">
                <div class="fin-card">
                    <div class="p-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="fw-800 text-dark mb-1"><i
                                    class="bi bi-shield-lock-fill text-primary me-2"></i>Hak Akses Sistem</h5>
                            <p class="text-muted small mb-0">Kelola hak akses menu &amp; submenu per role. Perubahan
                                berlaku <strong>instan</strong> tanpa reload halaman.</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label class="fw-bold text-dark small mb-0">Pilih Role:</label>
                            <select id="permRoleSelect" class="form-select form-select-sm" style="min-width:220px;">
                                <option value="">-- Pilih Role --</option>
                            </select>
                        </div>
                    </div>
                    <div id="permLoading" class="text-center py-5 text-muted" style="display:none;">
                        <div class="spinner-border spinner-border-sm me-2 text-primary"></div> Memuat data...
                    </div>
                    <div id="permMatrix" class="p-3" style="display:none;">
                        <div id="permMenuList"></div>
                    </div>
                    <div id="permEmpty" class="text-center py-5 text-muted">
                        <i class="bi bi-arrow-up-circle fs-1 text-primary opacity-50"></i>
                        <p class="mt-2 fw-bold">Pilih Role untuk mengatur permissions</p>
                    </div>
                </div>
            </div>
        </div>
        </div>

    </div>
</div>

<style>
    .perm-menu-row {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        margin-bottom: 8px;
        overflow: hidden;
    }

    .perm-menu-header {
        background: #f8fafc;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        transition: background 0.15s;
    }

    .perm-menu-header:hover {
        background: #eff6ff;
    }

    .perm-sub-row {
        padding: 8px 16px 8px 44px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-top: 1px solid #f1f5f9;
        transition: background 0.15s;
    }

    .perm-sub-row:hover {
        background: #f8fafc;
    }

    .form-switch .form-check-input {
        width: 2.5em;
        height: 1.3em;
        cursor: pointer;
    }
</style>

<script>
    const _PERM_API = '<?= base_url("menu/ajax_get_permissions") ?>';
    const _MENU_TOGGLE = '<?= base_url("menu/ajax_toggle_menu_permission") ?>';
    const _SUB_TOGGLE = '<?= base_url("menu/ajax_toggle_submenu_permission") ?>';
    const _CSRF_NAME = '<?= $this->security->get_csrf_token_name() ?>';
    const _CSRF_HASH = '<?= $this->security->get_csrf_hash() ?>';
    
    // Inject PHP Logic to JS for Read-Only rendering
    const isSuperAdmin = <?= (isset($is_developer) && $is_developer) ? 'true' : 'false' ?>;

    let _permData = null;

    $(document).ready(function () {
        $.ajax({
            url: _PERM_API, type: 'GET', dataType: 'json',
            success: function (res) {
                _permData = res;
                res.roles.forEach(function (r) {
                    $('#permRoleSelect').append('<option value="' + r.id + '">' + r.name + ' (ID: ' + r.id + ')</option>');
                });
            }
        });

        $('#permRoleSelect').on('change', function () {
            const roleId = parseInt($(this).val());
            if (!roleId || !_permData) { $('#permEmpty').show(); $('#permMatrix').hide(); return; }
            renderPermMatrix(roleId);
        });

        $(document).on('change', '.menu-toggle-chk', function () {
            const menuId = parseInt($(this).data('menu-id'));
            const roleId = parseInt($(this).data('role-id'));
            const grant = this.checked ? 1 : 0;
            const $el = $(this);
            const payload = { menu_id: menuId, role_id: roleId, grant: grant };
            payload[_CSRF_NAME] = _CSRF_HASH;
            $.post(_MENU_TOGGLE, payload, function (res) {
                if (res.status === 'ok') {
                    if (grant) { $('#sub-wrap-' + menuId).slideDown(200); showPT('Akses menu diberikan ✓', 'success'); }
                    else { $('#sub-wrap-' + menuId).slideUp(200); showPT('Akses menu dicabut', 'warning'); }
                    const m = _permData.menus.find(function (x) { return x.id == menuId; });
                    if (m) { if (grant) { if (!m.granted_roles.includes(roleId)) m.granted_roles.push(roleId); } else { m.granted_roles = m.granted_roles.filter(function (r) { return r != roleId; }); } }
                }
            }, 'json').fail(function () { showPT('Gagal menyimpan!', 'error'); $el.prop('checked', !$el.prop('checked')); });
        });

        $(document).on('change', '.sub-toggle-chk', function () {
            const subId = parseInt($(this).data('submenu-id'));
            const menuId = parseInt($(this).data('menu-id'));
            const roleId = parseInt($(this).data('role-id'));
            const grant = this.checked ? 1 : 0;
            const $el = $(this);
            const payload = { submenu_id: subId, role_id: roleId, grant: grant };
            payload[_CSRF_NAME] = _CSRF_HASH;
            $.post(_SUB_TOGGLE, payload, function (res) {
                if (res.status === 'ok') {
                    showPT(grant ? 'Submenu diaktifkan ✓' : 'Submenu dinonaktifkan', grant ? 'success' : 'warning');
                    const m = _permData.menus.find(function (x) { return x.id == menuId; });
                    if (m) {
                        const s = m.submenus.find(function (x) { return x.id == subId; });
                        if (s) { if (grant) { if (!s.granted_roles.includes(roleId)) s.granted_roles.push(roleId); } else { s.granted_roles = s.granted_roles.filter(function (r) { return r != roleId; }); } }
                        const gn = m.submenus.filter(function (x) { return x.granted_roles.map(Number).includes(roleId); }).length;
                        $('#badge-' + menuId).text(gn + '/' + m.submenus.length + ' Submenu');
                    }
                }
            }, 'json').fail(function () { showPT('Gagal menyimpan!', 'error'); $el.prop('checked', !$el.prop('checked')); });
        });
    });

    function renderPermMatrix(roleId) {
        const list = $('#permMenuList');
        list.empty();
        _permData.menus.forEach(function (menu) {
            const hasMenu = menu.granted_roles.map(Number).includes(roleId);
            
            // Sembunyikan menu yang tidak aktif jika BUKAN super admin
            if (!isSuperAdmin && !hasMenu) return;
            const subCount = menu.submenus.length;
            const grantedSub = menu.submenus.filter(function (s) { return s.granted_roles.map(Number).includes(roleId); }).length;
            let subRows = '';
            menu.submenus.forEach(function (sub) {
                const hasSub = sub.granted_roles.map(Number).includes(roleId);
                
                // Sembunyikan submenu yang tidak aktif jika BUKAN super admin
                if (!isSuperAdmin && !hasSub) return;
                let subInput = '';
                if (isSuperAdmin) {
                    subInput = '<div class="form-check form-switch mb-0"><input class="form-check-input sub-toggle-chk" type="checkbox" data-submenu-id="' + sub.id + '" data-menu-id="' + menu.id + '" data-role-id="' + roleId + '" ' + (hasSub ? 'checked' : '') + '></div>';
                } else {
                    subInput = hasSub ? '<span class="badge bg-soft-green text-success px-2 py-1"><i class="bi bi-check2"></i> Aktif</span>' : '<span class="badge bg-light text-muted px-2 py-1"><i class="bi bi-x"></i> Mati</span>';
                }

                subRows += '<div class="perm-sub-row">' +
                    '<div class="d-flex align-items-center gap-2"><i class="bi bi-arrow-return-right text-muted small"></i><span class="small text-dark">' + sub.name + '</span></div>' +
                    '<div>' + subInput + '</div>' +
                    '</div>';
            });
            const icon = (menu.icon && menu.icon !== '') ? menu.icon : 'bi-circle';
            const badgeHtml = subCount > 0 ? '<span class="badge bg-light text-secondary fw-bold" id="badge-' + menu.id + '">' + grantedSub + '/' + subCount + ' Submenu</span>' : '';
            const subWrap = subCount > 0 ? '<div id="sub-wrap-' + menu.id + '" style="display:' + (hasMenu ? 'block' : 'none') + '">' + subRows + '</div>' : '';
            
            let menuInput = '';
            if (isSuperAdmin) {
                menuInput = '<div class="form-check form-switch mb-0"><input class="form-check-input menu-toggle-chk" type="checkbox" data-menu-id="' + menu.id + '" data-role-id="' + roleId + '" ' + (hasMenu ? 'checked' : '') + '></div>';
            } else {
                menuInput = hasMenu ? '<span class="badge bg-soft-green text-success px-2 py-1 d-flex align-items-center gap-1"><i class="bi bi-check-circle-fill"></i> Buka</span>' : '<span class="badge bg-light text-muted px-2 py-1 d-flex align-items-center gap-1"><i class="bi bi-dash-circle-fill"></i> Kunci</span>';
            }

            list.append(
                '<div class="perm-menu-row">' +
                '<div class="perm-menu-header" onclick="$(\'#sub-wrap-' + menu.id + '\').slideToggle(200)">' +
                '<div class="d-flex align-items-center gap-3"><i class="bi ' + icon + ' text-primary"></i><span class="fw-700 text-dark">' + menu.name + '</span>' + badgeHtml + '</div>' +
                '<div class="d-flex align-items-center gap-2" onclick="event.stopPropagation()">' +
                '<span class="text-muted small ' + (!isSuperAdmin ? 'd-none d-md-inline' : '') + '">Akses Menu</span>' +
                menuInput +
                '</div>' +
                '</div>' + subWrap + '</div>'
            );
        });
        $('#permEmpty').hide();
        $('#permMatrix').show();
    }

    function showPT(msg, icon) {
        if (typeof Swal !== 'undefined') {
            Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2200, timerProgressBar: true })
                .fire({ icon: icon, title: msg });
        }
    }

    function terminateSession(logId, userName) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Putuskan Sesi?',
                text: 'Pengguna ' + userName + ' akan dipaksa keluar dari sistem.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Paksa Logout!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const payload = { log_id: logId };
                    payload[_CSRF_NAME] = _CSRF_HASH;
                    $.post('<?= base_url("dashboard/ajax_terminate_session") ?>', payload, function(res) {
                        if (res.status === 'ok') {
                            Swal.fire('Berhasil', 'Sesi telah diputus.', 'success').then(() => location.reload());
                        } else {
                            showPT('Gagal memutus sesi', 'error');
                        }
                    }, 'json');
                }
            });
        }
    }

    function confirmLogout() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Konfirmasi Logout', text: 'Apakah Anda yakin ingin keluar dari sistem?',
                icon: 'question', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya, Logout!'
            }).then(function (result) { if (result.isConfirmed) window.location.href = '<?= base_url("auth/logout") ?>'; });
        } else { if (confirm('Logout?')) window.location.href = '<?= base_url("auth/logout") ?>'; }
    }
</script>

<!-- Modal Cek Stok Gudang -->
<div class="modal fade" id="modalCekStok" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-boxes me-2"></i> CEK STOK GUDANG (LIVE)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle w-100" id="tableCekStok">
                        <thead class="table-light text-muted small uppercase">
                            <tr>
                                <th>NAMA BAHAN</th>
                                <th>VARIAN / UKURAN</th>
                                <th>CABANG GUDANG</th>
                                <th class="text-start">SISA STOK (LIVE)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let stokTableLoaded = false;
        const modalStok = document.getElementById('modalCekStok');
        
        if (modalStok) {
            modalStok.addEventListener('show.bs.modal', function () {
                if (!stokTableLoaded) {
                    if ($.fn.DataTable.isDataTable('#tableCekStok')) {
                        $('#tableCekStok').DataTable().destroy();
                    }
                    
                    $('#tableCekStok').DataTable({
                        ajax: {
                            url: '<?= site_url('dashboard/ajax_get_stock_deskprint') ?>',
                            dataSrc: ''
                        },
                        columns: [
                            { data: 'bahan_nama', className: 'fw-bold' },
                            { data: 'varian' },
                            { data: 'cabang', render: function(data) { return '<span class="badge bg-light text-dark border">' + data + '</span>'; } },
                            { 
                                data: 'base_qty',
                                className: 'text-start fw-900',
                                render: function(data, type, row) {
                                    var qty = parseFloat(data) || 0;
                                    var konversi = parseFloat(row.konversi) || 1;
                                    var satGudang = row.satuan_gudang || '';
                                    var satDasar = row.satuan_dasar || '';
                                    
                                    if (konversi > 1) {
                                        var main_qty = Math.floor(qty / konversi);
                                        var small_qty = Math.round(qty % konversi);
                                        var html = '';
                                        
                                        if (main_qty !== 0 || small_qty === 0) {
                                            html += '<span class="text-primary" style="font-size: 1.1rem;">' + main_qty.toLocaleString('id-ID') + '</span> <span class="text-muted">' + satGudang + '</span>';
                                        }
                                        
                                        if (small_qty !== 0) {
                                            if (html !== '') html += '<br>';
                                            html += '<span class="text-info">' + small_qty.toLocaleString('id-ID') + '</span> <span class="text-muted small">' + satDasar + '</span>';
                                        }
                                        return html;
                                    } else {
                                        return '<span class="text-primary" style="font-size: 1.1rem;">' + qty.toLocaleString('id-ID') + '</span> <span class="text-muted">' + (satDasar || satGudang) + '</span>';
                                    }
                                }
                            }
                        ],
                        language: {
                            search: "Cari Material:",
                            lengthMenu: "Tampilkan _MENU_ data",
                            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ material",
                            infoEmpty: "Tidak ada data material",
                            zeroRecords: "Material tidak ditemukan",
                            paginate: { previous: "Sebelumnya", next: "Selanjutnya" }
                        }
                    });
                    stokTableLoaded = true;
                }
            });
        }
    });
</script>
</div>