<!-- External CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">

<style>
    /* ==========================================================================
   PREMIUM DESIGN SYSTEM (LEGGER THEME v2)
   ========================================================================== */
    :root {
        --primary-gradient: linear-gradient(135deg, #1a1c23 0%, #2c3e50 100%);
        --secondary-gradient: linear-gradient(135deg, #2d3436 0%, #000000 100%);
        --accent-gradient: linear-gradient(135deg, #f39c12 0%, #d35400 100%);
        --info-gradient: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        --success-gradient: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
        --warning-gradient: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);
        --danger-gradient: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        --glass-bg: rgba(255, 255, 255, 0.95);
    }

    .report-section-card {
        background: white;
        border-radius: 20px;
        border: none;
        box-shadow: var(--premium-shadow);
        overflow: hidden;
        margin-bottom: 2rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .section-header {
        background: var(--primary-gradient);
        padding: 1.5rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
    }

    .section-header h5 {
        margin: 0;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-header i {
        background: rgba(255, 255, 255, 0.2);
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 1.2rem;
    }

    /* Stats v2 */
    .summary-stat-card {
        background: white;
        padding: 2rem;
        border-radius: 25px;
        border-right: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        position: relative;
        transition: all 0.3s ease;
    }

    .summary-stat-card:hover {
        background: #f8fafc;
        transform: translateY(-5px);
    }

    .stat-v2-icon {
        width: 60px;
        height: 60px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .stat-v2-label {
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
    }

    .stat-v2-value {
        font-size: 2.25rem;
        font-weight: 900;
        color: #1a1c23;
        line-height: 1;
    }

    .stat-v2-trend {
        font-size: 0.85rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* Calendar Premium */
    .premium-calendar-container {
        padding: 1.5rem;
    }

    #salesCalendar {
        border: none !important;
        font-family: inherit;
    }

    .fc-header-toolbar {
        display: none !important;
    }

    .fc-daygrid-day {
        transition: all 0.2s ease;
    }

    .fc-daygrid-day:hover {
        background: #f1f5f9 !important;
        cursor: pointer;
    }

    .fc-daygrid-event {
        border-radius: 6px !important;
        padding: 3px 6px !important;
        font-weight: 700 !important;
        font-size: 0.75rem !important;
        border: none !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Table v2 */
    .premium-table {
        margin-bottom: 0;
    }

    .premium-table thead th {
        background: #f8fafc;
        border: none;
        color: #475569;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 1px;
        padding: 1.25rem 1.5rem;
    }

    .premium-table tbody td {
        padding: 1.25rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-weight: 500;
        color: #334155;
    }

    .premium-table tbody tr {
        transition: all 0.2s ease;
    }

    .premium-table tbody tr:hover {
        background-color: #f8fafc !important;
    }

    .spk-code {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 800;
        font-size: 0.95rem;
        color: #1a1c23;
        background: #f1f5f9;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
    }

    /* Date/Time Styling */
    .time-badge {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.85rem;
        font-weight: 700;
        color: #64748b;
    }

    .time-badge i {
        color: #3498db;
    }

    /* Action Buttons Premium */
    .btn-action-premium {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
        background: white;
        color: #64748b;
    }

    .btn-action-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        color: white;
    }

    .btn-view:hover {
        background: var(--info-gradient) !important;
        border-color: transparent;
    }

    .btn-print:hover {
        background: var(--success-gradient) !important;
        border-color: transparent;
    }

    .btn-globe:hover {
        background: var(--accent-gradient) !important;
        border-color: transparent;
    }

    .btn-delete:hover {
        background: var(--danger-gradient) !important;
        border-color: transparent;
    }

    /* Modal Styling */
    .modal-premium {
        border-radius: 25px !important;
        border: none !important;
        overflow: hidden;
    }

    .modal-premium .modal-header {
        background: var(--primary-gradient);
        padding: 1.75rem 2rem;
        border: none;
    }

    /* Company Indicator */
    .company-pill {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .company-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: currentColor;
    }
</style>

<!-- ================= HEADER SECTION ================= -->
<div class="app-content">
    <!-- <div class="container-fluid"> -->
    <!-- Header -->
    <?php
    $this->load->view('layout_admin/header_modul');
    ?>


    <?php if ($this->session->userdata('level') == 1 || $this->session->userdata('level') == 17 || $this->session->userdata('level') == 21): ?>
        <!-- Toolbar Section -->
        <div class="report-section-card mb-4"
            style="background: var(--glass-bg); backdrop-filter: blur(10px); border: 1px solid rgba(0,0,0,0.05);">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-12 text-end d-flex justify-content-end align-items-center gap-2">
                        <a href="<?= base_url('penjualan/best_selling') ?>"
                            class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-bold"
                            style="border-width: 2px; font-size: 0.75rem;">
                            <i class="fas fa-crown me-1"></i> BEST SELLING
                        </a>
                        <button onclick="new bootstrap.Modal(document.getElementById('modalSopPenjualan')).show()"
                            class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold"
                            style="border-width: 2px; font-size: 0.75rem;">
                            <i class="fas fa-book-open me-1"></i> PANDUAN SOP
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Statistics Overview v2 -->
    <div class="row g-4 mb-5 mt-2">
        <div class="col-xl-3 col-md-6">
            <div class="summary-stat-card shadow-sm h-100">
                <div class="stat-v2-icon text-white" style="background: var(--success-gradient)">
                    <i class="fas fa-wallet"></i>
                </div>
                <div>
                    <div class="stat-v2-label">TOTAL PENDAPATAN</div>
                    <div class="stat-v2-value text-success">Rp <?= number_format($total_sales, 0, ',', '.') ?></div>
                </div>
                <div class="stat-v2-trend text-success">
                    <i class="fas fa-arrow-up"></i> All Time High
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="summary-stat-card shadow-sm h-100">
                <div class="stat-v2-icon text-white" style="background: var(--info-gradient)">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <div class="stat-v2-label">VOLUME TRANSAKSI</div>
                    <div class="stat-v2-value text-info"><?= number_format($total_transactions) ?></div>
                </div>
                <div class="stat-v2-trend text-info">
                    <i class="fas fa-check-circle"></i> Berhasil Divalidasi
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="summary-stat-card shadow-sm h-100">
                <div class="stat-v2-icon text-white" style="background: var(--warning-gradient)">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="stat-v2-label">TOTAL PELANGGAN</div>
                    <div class="stat-v2-value text-warning"><?= number_format($total_customers) ?></div>
                </div>
                <div class="stat-v2-trend text-warning">
                    <i class="fas fa-user-check"></i> Database Aktif
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="summary-stat-card shadow-sm h-100">
                <div class="stat-v2-icon text-white" style="background: var(--accent-gradient)">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div>
                    <div class="stat-v2-label">KPI BULAN INI</div>
                    <div class="stat-v2-value text-danger" style="color: #d35400 !important;">
                        Rp <?= $monthly_sales ? number_format($monthly_sales->total_sales, 0, ',', '.') : '0' ?>
                    </div>
                </div>
                <div class="stat-v2-trend text-danger">
                    <i class="fas fa-chart-line"></i> Target Performance
                </div>
            </div>
        </div>
    </div>



    <!-- Calendar & Information Section -->
    <div class="row">
        <div class="col-lg-8">
            <div class="report-section-card">
                <div class="section-header">
                    <h5><i class="fas fa-calendar-alt"></i> Jurnal Penjualan Visual</h5>
                    <div class="calendar-controls d-flex align-items-center gap-3">
                        <button class="btn btn-sm btn-link text-white p-0" onclick="previousMonth()">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span class="fw-800 text-uppercase letter-spacing-1" id="currentMonthYear"
                            style="min-width: 150px; text-align: center;">
                            <?= date('F Y', mktime(0, 0, 0, $selected_month, 1, $selected_year)) ?>
                        </span>
                        <button class="btn btn-sm btn-link text-white p-0" onclick="nextMonth()">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <div class="premium-calendar-container">
                    <div id="salesCalendar"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Selected Date Info v2 -->
            <div class="report-section-card">
                <div class="section-header" style="background: var(--info-gradient)">
                    <h5><i class="fas fa-info-circle"></i> Ringkasan Harian</h5>
                </div>
                <div class="card-body p-4">
                    <div class="selected-date-info text-center">
                        <div class="text-muted small fw-bold text-uppercase mb-1">DATA PER TANGGAL</div>
                        <h3 class="fw-900 text-dark mb-4" id="selectedDateDisplay">
                            <?= date('d F Y', strtotime($selected_date)) ?>
                        </h3>

                        <div class="p-3 rounded-4 bg-light mb-3">
                            <div class="row g-0">
                                <div class="col-6 border-end">
                                    <div class="small fw-700 text-muted">TRANSAKSI</div>
                                    <h4 class="fw-900 text-primary mb-0" id="dailyTransactions">
                                        <?= $daily_transactions ?>
                                    </h4>
                                </div>
                                <div class="col-6">
                                    <div class="small fw-700 text-muted">NOMINAL</div>
                                    <h4 class="fw-900 text-success mb-0" id="dailySales">Rp
                                        <?= number_format($daily_sales, 0, ',', '.') ?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 opacity-10">

                    <div class="d-grid gap-3">
                        <button
                            class="btn btn-dark py-3 rounded-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-3 transition-all"
                            onclick="exportSalesData()" style="background: #1a1c23;">
                            <i class="fas fa-file-excel fs-5 text-success"></i>
                            <span>EXPORT LAPORAN (EXCEL)</span>
                        </button>
                        <div class="row g-2">
                            <div class="col-6">
                                <button class="btn btn-outline-secondary w-100 py-2 rounded-3 fw-bold"
                                    onclick="refreshData()">
                                    <i class="fas fa-sync-alt me-2"></i>RELOAD
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-outline-secondary w-100 py-2 rounded-3 fw-bold"
                                    onclick="viewSalesDetail()">
                                    <i class="fas fa-external-link-alt me-2"></i>DETAIL
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Legend Card Premium -->
            <div class="report-section-card">
                <div class="section-header" style="background: var(--secondary-gradient)">
                    <h5><i class="fas fa-tags"></i> Indikator Cabang</h5>
                </div>
                <div class="card-body p-4" id="companyLegend">
                    <!-- Legend populated by JS -->
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Detail Section Premium -->
    <div class="report-section-card">
        <div class="section-header">
            <h5>
                <i class="fas fa-list-ul"></i> Manifest Transaksi Harian -
                <span id="detailDateTitle" class="ms-2"><?= date('d F Y', strtotime($selected_date)) ?></span>
            </h5>
            <div class="d-flex align-items-center">
                <div class="input-group input-group-sm shadow-sm">
                    <span class="input-group-text bg-white border-0 text-muted rounded-start-pill px-3">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="searchTableInput" class="form-control border-0 rounded-end-pill py-2 px-3" 
                        placeholder="Cari SPK / Nama Konsumen..." onkeyup="filterSalesTable()" style="min-width: 250px;">
                </div>
            </div>
        </div>
        </div>
        
        <div class="card-header border-bottom px-4 pt-3 pb-0 bg-light" style="border-radius: 0;">
            <ul class="nav nav-tabs border-bottom-0" id="salesDetailTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold px-4 pb-3" id="tab-daftar-spk" data-bs-toggle="tab" data-bs-target="#content-spk" type="button" role="tab" style="color: #475569;">
                        <i class="fas fa-file-invoice me-2 text-primary"></i>Daftar SPK
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold px-4 pb-3" id="tab-rekap-konsumen" data-bs-toggle="tab" data-bs-target="#content-konsumen" type="button" role="tab" style="color: #475569;">
                        <i class="fas fa-users me-2 text-warning"></i>Rekap Per Konsumen
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content">
            <!-- TAB 1: DAFTAR SPK -->
            <div class="tab-pane fade show active" id="content-spk" role="tabpanel">
                <div class="table-responsive">
            <table class="table premium-table" id="salesDetailTable">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">NO</th>
                        <th width="15%">KODE SPK</th>
                        <th width="18%">CABANG/PERUSAHAAN</th>
                        <th width="22%">IDENTITAS KONSUMEN</th>
                        <th width="15%">WAKTU TRANSAKSI</th>
                        <th width="12%">TOTAL AKHIR</th>
                        <th width="12%">SUBTOTAL (HISTORI)</th>
                        <th class="text-center" width="13%">STATUS</th>
                        <th class="text-center" width="10%">AKSI</th>
                    </tr>
                </thead>
                <tbody id="salesDetailBody">
                    <?php if (!empty($sales_data)): ?>
                        <?php $no = 1;
                        foreach ($sales_data as $sale): ?>
                            <tr>
                                <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="spk-code"><?= $sale->nospk ?></span>
                                        <?php if (strpos($sale->nospk, 'MAN-') === 0): ?>
                                            <span
                                                class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25"
                                                style="font-size: 0.6rem; padding: 2px 6px;">MANUAL</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $comp_name = $sale->nama_perusahaan ?: 'N/A';
                                    $colors = ['#2196f3', '#9c27b0', '#4caf50', '#ff9800', '#e91e63', '#8bc34a', '#009688', '#ffc107', '#673ab7', '#3f51b5'];
                                    $hash = 0;
                                    for ($i = 0; $i < strlen($comp_name); $i++) {
                                        $hash = ord($comp_name[$i]) + (($hash << 5) - $hash);
                                    }
                                    $color = $colors[abs($hash) % count($colors)];
                                    ?>
                                    <div class="company-pill"
                                        style="color: <?= $color ?>; background: rgba(0,0,0,0.03); border: 1px solid <?= $color ?>20;">
                                        <div class="company-dot" style="background: <?= $color ?>"></div>
                                        <?= $comp_name ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-800 text-dark"><?= $sale->konsumen_nama ?: 'PENGUNJUNG' ?></div>
                                    <div class="small text-muted d-flex align-items-center gap-1">
                                        <i class="fas fa-phone-alt fs-xsmall"></i> <?= $sale->konsumen_nohp ?: '-' ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="time-badge">
                                        <i class="far fa-clock"></i>
                                        <?= date('H:i', strtotime($sale->jam_bayar)) ?>
                                        <span class="text-light-muted">|</span>
                                        <?= date('d/m/y', strtotime($sale->tgl_bayar)) ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="fw-900 text-success">
                                        Rp <?= number_format($sale->total_bayar, 0, ',', '.') ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="fw-900 text-success">
                                        Rp <?= number_format($sale->snapshot_subtotal, 0, ',', '.') ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $status_map = [
                                        '1' => ['label' => 'SUCCESS', 'class' => 'bg-success'],
                                        '0' => ['label' => 'PENDING', 'class' => 'bg-warning text-dark'],
                                    ];
                                    $status = $status_map[$sale->status_lunas] ?? ['label' => 'PENDING', 'class' => 'bg-secondary'];
                                    ?>
                                    <span class="badge rounded-pill <?= $status['class'] ?> px-3 py-2 fw-800"
                                        style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                        <?= $status['label'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn-action-premium btn-view"
                                            onclick="viewTransactionDetail('<?= $sale->nospk ?>')" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="<?= base_url('cekpesanan/spk/' . urlencode(encrypt_spk($sale->nospk))) ?>"
                                            target="_blank" class="btn-action-premium btn-globe" title="Link Invoice (Status)">
                                            <i class="fas fa-globe"></i>
                                        </a>
                                        <button class="btn-action-premium btn-print"
                                            onclick="viewTransactionDetail('<?= $sale->nospk ?>')" title="Print Invoice">
                                            <i class="fas fa-print"></i>
                                        </button>
                                        <?php if ($this->session->userdata('level') == 1 || $this->session->userdata('level') == 17): ?>
                                            <button class="btn-action-premium"
                                                style="background: white; border-color: #e2e8f0; color: #f39c12;"
                                                onmouseover="this.style.background='var(--warning-gradient)'; this.style.color='white';"
                                                onmouseout="this.style.background='white'; this.style.color='#f39c12';"
                                                onclick="rollbackTransaction('<?= $sale->nospk ?>')" title="Kembalikan (Rollback)">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                            <button class="btn-action-premium btn-delete"
                                                onclick="deleteTransaction('<?= $sale->nospk ?>')" title="Hapus Permanen">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="mb-3"><i class="fas fa-inbox fa-3x opacity-20"></i></div>
                                <div class="fw-bold fs-5">Tidak Ada Data Penjualan</div>
                                <div class="small">Silakan pilih tanggal lain pada kalender</div>
                            </td>
                        </tr>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
            </div>
            
            <!-- TAB 2: REKAP KONSUMEN -->
            <div class="tab-pane fade" id="content-konsumen" role="tabpanel">
                <div class="table-responsive">
                    <table class="table premium-table" id="rekapKonsumenTable">
                        <thead>
                            <tr>
                                <th class="text-center" width="5%">NO</th>
                                <th width="35%">IDENTITAS KONSUMEN</th>
                                <th class="text-center" width="20%">JUMLAH TRANSAKSI</th>
                                <th class="text-center" width="20%">TOTAL BELANJA</th>
                                <th class="text-center" width="20%">DAFTAR SPK</th>
                            </tr>
                        </thead>
                        <tbody id="rekapKonsumenBody">
                            <?php if (!empty($sales_data)): ?>
                                <?php 
                                $konsumenMap = [];
                                foreach($sales_data as $sale) {
                                    $name = $sale->konsumen_nama ?: 'PENGUNJUNG';
                                    $phone = $sale->konsumen_nohp ?: '-';
                                    $key = $name . '||' . $phone;
                                    
                                    if (!isset($konsumenMap[$key])) {
                                        $konsumenMap[$key] = [
                                            'name' => $name,
                                            'phone' => $phone,
                                            'count' => 0,
                                            'total' => 0,
                                            'spks' => []
                                        ];
                                    }
                                    $konsumenMap[$key]['count']++;
                                    $konsumenMap[$key]['total'] += (float)$sale->total_bayar;
                                    $konsumenMap[$key]['spks'][] = $sale->nospk;
                                }
                                
                                usort($konsumenMap, function($a, $b) {
                                    return $b['total'] <=> $a['total'];
                                });
                                ?>
                                <?php $no = 1; foreach ($konsumenMap as $k): ?>
                                    <tr>
                                        <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                                        <td>
                                            <div class="fw-800 text-dark"><?= htmlspecialchars($k['name']) ?></div>
                                            <div class="small text-muted d-flex align-items-center gap-1">
                                                <i class="fas fa-phone-alt fs-xsmall"></i> <?= htmlspecialchars($k['phone']) ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-800"><?= $k['count'] ?> Transaksi</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="fw-900 text-success">
                                                Rp <?= number_format($k['total'], 0, ',', '.') ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-secondary rounded-pill fw-bold px-3 shadow-sm" onclick="showSpkList('<?= htmlspecialchars($k['name'], ENT_QUOTES) ?>', '<?= implode(',', $k['spks']) ?>')">
                                                <i class="fas fa-list me-1"></i> Lihat SPK
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <div class="mb-3"><i class="fas fa-users-slash fa-3x opacity-20"></i></div>
                                        <div class="fw-bold fs-5">Tidak Ada Data Konsumen</div>
                                        <div class="small">Silakan pilih tanggal lain pada kalender</div>
                                    </td>
                                </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php $this->load->view('admin/penjualan/components/modal_sop_penjualan'); ?>

<!-- ================= JAVASCRIPT SECTION ================= -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let calendar;
    let currentYear = <?= $selected_year ?>;
    let currentMonth = <?= $selected_month ?>;
    let selectedDate = '<?= $selected_date ?>';
    let selectedPerusahaan = '<?= $selected_perusahaan ?>';
    let currentNospk = '';
    let userLevel = <?= $user_level ?>;
    let isAdmin = (userLevel == 1);
    let isOwner = (userLevel == 17);
    let canDelete = (isAdmin || isOwner);

    // Calendar events data
    const calendarEvents = <?= json_encode($calendar_events) ?>;

    // Company color mapping function
    function getCompanyColor(perusahaanId) {
        const colors = [
            '#2196f3', '#9c27b0', '#4caf50', '#ff9800', '#e91e63',
            '#8bc34a', '#009688', '#ffc107', '#673ab7', '#3f51b5'
        ];
        return colors[(perusahaanId - 1) % colors.length];
    }

    // Generate consistent color based on company name
    function getCompanyColorByName(companyName) {
        if (!companyName) return '#6c757d';

        let hash = 0;
        for (let i = 0; i < companyName.length; i++) {
            const char = companyName.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash; // Convert to 32-bit integer
        }

        const colors = [
            '#2196f3', '#9c27b0', '#4caf50', '#ff9800', '#e91e63',
            '#8bc34a', '#009688', '#ffc107', '#673ab7', '#3f51b5'
        ];

        return colors[Math.abs(hash) % colors.length];
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize current sales data with PHP data
        window.currentSalesData = {
            sales_data: <?= json_encode($sales_data) ?>
        };

        initializeCalendar();
        updateSelectedDateInfo();
        updateCompanyLegend();
    });

    function filterSalesTable() {
        const input = document.getElementById("searchTableInput");
        const filter = input.value.toLowerCase();
        const tbody = document.getElementById("salesDetailBody");
        const tr = tbody.getElementsByTagName("tr");

        for (let i = 0; i < tr.length; i++) {
            // Ensure we don't filter the "Tidak Ada Data" row
            if (tr[i].getElementsByTagName("td").length > 1) {
                const spkTd = tr[i].getElementsByTagName("td")[1];
                const custTd = tr[i].getElementsByTagName("td")[3];
                if (spkTd && custTd) {
                    const spkText = spkTd.textContent || spkTd.innerText;
                    const custText = custTd.textContent || custTd.innerText;
                    if (spkText.toLowerCase().indexOf(filter) > -1 || custText.toLowerCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
        
        // Also filter Rekap Konsumen Table
        const rekapBody = document.getElementById("rekapKonsumenBody");
        const rekapTr = rekapBody.getElementsByTagName("tr");
        for (let i = 0; i < rekapTr.length; i++) {
            if (rekapTr[i].getElementsByTagName("td").length > 1) {
                const custTd = rekapTr[i].getElementsByTagName("td")[1];
                if (custTd) {
                    const custText = custTd.textContent || custTd.innerText;
                    if (custText.toLowerCase().indexOf(filter) > -1) {
                        rekapTr[i].style.display = "";
                    } else {
                        rekapTr[i].style.display = "none";
                    }
                }
            }
        }
    }
    
    function showSpkList(name, spkString) {
        const spks = spkString.split(',');
        const badgesHtml = spks.map(s => `<span class="badge bg-light text-dark border m-1 fs-6 px-3 py-2 shadow-sm">${s}</span>`).join('');
        
        Swal.fire({
            title: '<div class="fs-5 mb-2 text-muted">Daftar SPK milik:</div><div class="fw-bold text-primary">' + name + '</div>',
            html: '<div class="d-flex flex-wrap justify-content-center mt-3">' + badgesHtml + '</div>',
            icon: 'info',
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#6c757d'
        });
    }

    function initializeCalendar() {
        const calendarEl = document.getElementById('salesCalendar');

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            initialDate: `${currentYear}-${String(currentMonth).padStart(2, '0')}-01`,
            locale: 'id',
            headerToolbar: false, // We'll use custom controls
            events: calendarEvents.map(event => ({
                ...event,
                textColor: '#ffffff'
            })),
            eventDisplay: 'block',
            dayMaxEvents: true,
            height: 'auto',

            eventClick: function (info) {
                const clickedDate = info.event.startStr;
                loadSalesData(clickedDate);
            },

            dateClick: function (info) {
                const clickedDate = info.dateStr;
                loadSalesData(clickedDate);
            },

            datesSet: function (info) {
                currentYear = info.view.currentStart.getFullYear();
                currentMonth = info.view.currentStart.getMonth() + 1;
                updateMonthYearDisplay();
            }
        });

        calendar.render();
    }

    function changeBranch() {
        const branchId = document.getElementById('branchFilter').value;
        const url = new URL(window.location.href);
        url.searchParams.set('perusahaan_id', branchId);
        window.location.href = url.toString();
    }

    function loadSalesData(date) {
        selectedDate = date;

        // Show loading
        Swal.fire({
            title: 'Memuat data...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // AJAX request to get sales data
        fetch('<?= base_url('penjualan/get_sales_by_date') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `date=${encodeURIComponent(date)}&perusahaan_id=${encodeURIComponent(selectedPerusahaan)}`
        })
            .then(response => response.json())
            .then(data => {
                Swal.close();

                if (data.status === 'success') {
                    // Store current data for legend
                    window.currentSalesData = data.data;
                    updateSalesDetail(data.data);
                    updateSelectedDateInfo();
                    updateCompanyLegend();
                } else {
                    Swal.fire('Error', data.message || 'Terjadi kesalahan', 'error');
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error:', error);
                Swal.fire('Error', 'Terjadi kesalahan saat memuat data', 'error');
            });
    }

    function updateSalesDetail(data) {
        const tbody = document.getElementById('salesDetailBody');
        const detailDateTitle = document.getElementById('detailDateTitle');

        detailDateTitle.textContent = new Date(selectedDate).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });

        if (data.sales_data && data.sales_data.length > 0) {
            let html = '';
            data.sales_data.forEach((sale, index) => {
                const statusMap = {
                    '1': { label: 'SUCCESS', class: 'bg-success' },
                    '0': { label: 'PENDING', class: 'bg-warning text-dark' }
                };
                const status = statusMap[sale.status_lunas] || { label: 'PENDING', class: 'bg-secondary' };
                const companyColor = getCompanyColorByName(sale.nama_perusahaan);

                const isManual = sale.nospk.startsWith('MAN-');
                html += `
                    <tr>
                        <td class="text-center text-muted fw-bold">${index + 1}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="spk-code">${sale.nospk}</span>
                                ${isManual ? '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size: 0.6rem; padding: 2px 6px;">MANUAL</span>' : ''}
                            </div>
                        </td>
                        <td>
                            <div class="company-pill" style="color: ${companyColor}; background: rgba(0,0,0,0.03); border: 1px solid ${companyColor}20;">
                                <div class="company-dot" style="background: ${companyColor}"></div>
                                ${sale.nama_perusahaan || 'N/A'}
                            </div>
                        </td>
                        <td>
                            <div class="fw-800 text-dark">${sale.konsumen_nama || 'PENGUNJUNG'}</div>
                            <div class="small text-muted d-flex align-items-center gap-1">
                                <i class="fas fa-phone-alt fs-xsmall"></i> ${sale.konsumen_nohp || '-'}
                            </div>
                        </td>
                        <td>
                            <div class="time-badge">
                                <i class="far fa-clock"></i>
                                ${new Date(sale.tgl_bayar + ' ' + sale.jam_bayar).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                                <span class="text-light-muted">|</span>
                                ${new Date(sale.tgl_bayar).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: '2-digit' })}
                            </div>
                        </td>
                        <td>
                            <div class="fw-900 text-success">
                                Rp ${formatNumber(sale.total_bayar)}
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="fw-900 text-success">
                                Rp ${formatNumber(sale.snapshot_subtotal)}
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-pill ${status.class} px-3 py-2 fw-800" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                ${status.label}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn-action-premium btn-view" onclick="viewTransactionDetail('${sale.nospk}')" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="<?= base_url('cekpesanan/spk/') ?>${sale.nospk_encrypted}" target="_blank" class="btn-action-premium btn-globe" title="Link Invoice (Status)">
                                    <i class="fas fa-globe"></i>
                                </a>
                                <button class="btn-action-premium btn-print" onclick="viewTransactionDetail('${sale.nospk}')" title="Print Invoice">
                                    <i class="fas fa-print"></i>
                                </button>
                                ${canDelete ? `
                                <button class="btn-action-premium" style="background: white; border-color: #e2e8f0; color: #f39c12;" onmouseover="this.style.background='var(--warning-gradient)'; this.style.color='white';" onmouseout="this.style.background='white'; this.style.color='#f39c12';" onclick="rollbackTransaction('${sale.nospk}')" title="Kembalikan (Rollback)">
                                    <i class="fas fa-undo"></i>
                                </button>
                                <button class="btn-action-premium btn-delete" onclick="deleteTransaction('${sale.nospk}')" title="Hapus Permanen">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                ` : ''}
                            </div>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        } else {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <div class="mb-3"><i class="fas fa-inbox fa-3x opacity-20"></i></div>
                        <div class="fw-bold fs-5">Tidak Ada Data Penjualan</div>
                        <div class="small">Silakan pilih tanggal lain pada kalender</div>
                    </td>
                </tr>
            `;
        }

        // --- UPDATE REKAP KONSUMEN TABLE ---
        const rekapBody = document.getElementById('rekapKonsumenBody');
        if (data.sales_data && data.sales_data.length > 0) {
            const konsumenMap = {};
            data.sales_data.forEach(sale => {
                const name = sale.konsumen_nama || 'PENGUNJUNG';
                const phone = sale.konsumen_nohp || '-';
                const key = name + '||' + phone;
                
                if (!konsumenMap[key]) {
                    konsumenMap[key] = {
                        name: name,
                        phone: phone,
                        count: 0,
                        total: 0,
                        spks: []
                    };
                }
                konsumenMap[key].count++;
                konsumenMap[key].total += parseFloat(sale.total_bayar || 0);
                konsumenMap[key].spks.push(sale.nospk);
            });
            
            // Convert to array and sort by total
            const konsumenArr = Object.values(konsumenMap).sort((a, b) => b.total - a.total);
            
            let rekapHtml = '';
            konsumenArr.forEach((k, idx) => {
                rekapHtml += `
                    <tr>
                        <td class="text-center text-muted fw-bold">${idx + 1}</td>
                        <td>
                            <div class="fw-800 text-dark">${k.name}</div>
                            <div class="small text-muted d-flex align-items-center gap-1">
                                <i class="fas fa-phone-alt fs-xsmall"></i> ${k.phone}
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-800">${k.count} Transaksi</span>
                        </td>
                        <td class="text-center">
                            <div class="fw-900 text-success">
                                Rp ${formatNumber(k.total)}
                            </div>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-secondary rounded-pill fw-bold px-3 shadow-sm" onclick="showSpkList('${k.name.replace(/'/g, "\\'")}', '${k.spks.join(',')}')">
                                <i class="fas fa-list me-1"></i> Lihat SPK
                            </button>
                        </td>
                    </tr>
                `;
            });
            rekapBody.innerHTML = rekapHtml;
        } else {
            rekapBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <div class="mb-3"><i class="fas fa-users-slash fa-3x opacity-20"></i></div>
                        <div class="fw-bold fs-5">Tidak Ada Data Konsumen</div>
                        <div class="small">Silakan pilih tanggal lain pada kalender</div>
                    </td>
                </tr>
            `;
        }
        // -----------------------------------

        document.getElementById('dailyTransactions').textContent = data.total_transactions;
        document.getElementById('dailySales').textContent = 'Rp ' + formatNumber(data.total_sales);
    }

    function updateSelectedDateInfo() {
        const selectedDateDisplay = document.getElementById('selectedDateDisplay');
        selectedDateDisplay.textContent = new Date(selectedDate).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
    }
    function updateCompanyLegend() {
        const legendContainer = document.getElementById('companyLegend');
        const companies = new Set();

        if (window.currentSalesData && window.currentSalesData.sales_data) {
            window.currentSalesData.sales_data.forEach(sale => {
                if (sale.nama_perusahaan) companies.add(sale.nama_perusahaan);
            });
        }

        let legendHTML = '<div class="row g-2">';
        companies.forEach(company => {
            const color = getCompanyColorByName(company);
            legendHTML += `
                <div class="col-12">
                    <div class="d-flex align-items-center p-2 rounded-3 border bg-light bg-opacity-50">
                        <div style="width: 12px; height: 12px; border-radius: 4px; margin-right: 12px; background-color: ${color}; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                        <span class="small fw-bold text-dark text-uppercase letter-spacing-1">${company}</span>
                    </div>
                </div>
            `;
        });
        legendHTML += '</div>';

        if (companies.size === 0) {
            legendHTML = '<div class="text-center py-3 text-muted small"><i class="fas fa-info-circle me-1"></i>Belum ada data cabang</div>';
        }

        legendContainer.innerHTML = legendHTML;
    }

    function updateMonthYearDisplay() {
        const monthYearDisplay = document.getElementById('currentMonthYear');
        const date = new Date(currentYear, currentMonth - 1, 1);
        monthYearDisplay.textContent = date.toLocaleDateString('id-ID', {
            month: 'long',
            year: 'numeric'
        });
    }

    function previousMonth() {
        calendar.prev();
    }

    function nextMonth() {
        calendar.next();
    }

    function viewTransactionDetail(transactionId) {
        currentNospk = transactionId;
        // Load transaction detail via AJAX
        fetch(`<?= base_url('penjualan/get_transaction_detail/') ?>${transactionId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('transactionDetailContent').innerHTML = data.html;
                    new bootstrap.Modal(document.getElementById('transactionDetailModal')).show();
                } else {
                    Swal.fire('Error', data.message || 'Terjadi kesalahan', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Terjadi kesalahan saat memuat detail', 'error');
            });
    }

    function exportSalesData() {
        const branchParam = selectedPerusahaan !== 'all' ? `&perusahaan_id=${selectedPerusahaan}` : '';
        const url = `<?= base_url('penjualan/export_sales') ?>?date=${selectedDate}${branchParam}`;
        window.open(url, '_blank');
    }

    function viewSalesDetail() {
        // Redirect to the main sales list page
        window.open(`<?= base_url('penjualan/daftar_penjualan') ?>`, '_blank');
    }

    function refreshData() {
        loadSalesData(selectedDate);
        updateCompanyLegend();
    }

    function printTransaction() {
        if (currentNospk) {
            window.open(`<?= base_url('transaksi/cetak_invoice/') ?>${currentNospk}`, '_blank');
        } else {
            Swal.fire('Error', 'No. SPK tidak ditemukan', 'error');
        }
    }

    function sendInvoiceWA(nospk) {
        Swal.fire({
            title: 'Kirim Invoice via WA?',
            text: "Invoice akan dikirim otomatis ke nomor WhatsApp konsumen.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#25D366',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fab fa-whatsapp me-2"></i>Kirim Sekarang',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Sedang Mengirim...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?= base_url('penjualan/send_invoice_wa') ?>',
                    type: 'POST',
                    data: { nospk: nospk },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                    }
                });
            }
        });
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }

    // Delete transaction function (only for admin/owner)
    function deleteTransaction(nospk) {
        Swal.fire({
            title: 'Hapus Transaksi?',
            html: `
            <div class="text-start">
                <div class="alert alert-warning" style="font-size: 0.9rem;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan!
                </div>
                <div style="margin: 20px 0;">
                    <p><strong>No. SPK:</strong> ${nospk}</p>
                </div>
                <div class="form-group mb-3 text-start">
                    <label class="fw-bold mb-2 text-dark">Alasan Penghapusan (Wajib):</label>
                    <textarea id="alasanHapusSPK" class="form-control" rows="2" placeholder="Contoh: Salah harga, Konsumen membatalkan, dsb..."></textarea>
                </div>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; font-size: 0.85rem;">
                    <p class="text-danger mb-2"><strong>Data yang akan terhapus ke seluruh sistem:</strong></p>
                    <ul class="text-danger" style="margin: 0; padding-left: 20px;">
                        <li>Data SPK Utama (tbl_spk)</li>
                        <li>Logistik & Pembayaran (tbl_pembayaran, tbl_pembayaran_detail)</li>
                        <li>Order & Item Pemesanan (tbl_pemesanan, tbl_pemesanan_detail, tbl_pembayaran_produk)</li>
                        <li>Data Produksi (tbl_produksi, tbl_flow_diagram)</li>
                        <li>Data Piutang Konsumen (tbl_piutang)</li>
                    </ul>
                </div>
            </div>
        `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus Permanen!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            width: '500px',
            preConfirm: () => {
                const alasan = document.getElementById('alasanHapusSPK').value.trim();
                if (!alasan) {
                    Swal.showValidationMessage('Alasan penghapusan wajib diisi!');
                }
                return alasan;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const alasan = result.value;

                // Show loading
                Swal.fire({
                    title: 'Menghapus transaksi...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // AJAX request to delete transaction
                fetch('<?= base_url('penjualan/delete_transaction') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'nospk=' + encodeURIComponent(nospk) + '&alasan=' + encodeURIComponent(alasan)
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message || 'Transaksi berhasil dihapus',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                // Reload sales data
                                loadSalesData(selectedDate);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message || 'Gagal menghapus transaksi'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menghapus transaksi'
                        });
                    });
            }
        });
    }

    // Rollback transaction function
    function rollbackTransaction(nospk) {
        Swal.fire({
            title: 'Kembalikan Transaksi?',
            html: `
            <div class="text-start">
                <div class="alert alert-info" style="font-size: 0.9rem;">
                    <i class="fas fa-undo me-2"></i>
                    <strong>Catatan:</strong> Fitur ini membatalkan status LUNAS dan SPK akan dialihkan kembali.
                </div>
                <div style="margin: 20px 0;">
                    <p><strong>No. SPK:</strong> <span class="badge bg-primary fs-6">${nospk}</span></p>
                </div>
                
                <div class="form-group text-start">
                    <label class="fw-bold mb-2">Pilih Tujuan Pengembalian SPK:</label>
                    
                    <div class="form-check form-check-custom py-2 px-4 mb-2 rounded border bg-light">
                        <input class="form-check-input mt-1" type="radio" name="rollback_target" id="targetKasir" value="kasir" checked>
                        <label class="form-check-label ps-2" for="targetKasir" style="cursor: pointer;">
                            <strong>Kembalikan ke Kasir</strong> 
                            <div class="text-muted small mt-1">
                                <i class="fas fa-times text-danger"></i> Menghapus Nota Pembayaran<br>
                                <i class="fas fa-times text-danger"></i> Menghapus Antrian Produksi<br>
                                <i class="fas fa-check text-success"></i> Data Pemesanan tetap ada, Kasir tinggal bayar ulang.
                            </div>
                        </label>
                    </div>

                    <div class="form-check form-check-custom py-2 px-4 rounded border bg-light">
                        <input class="form-check-input mt-1" type="radio" name="rollback_target" id="targetDeskprint" value="deskprint">
                        <label class="form-check-label ps-2" for="targetDeskprint" style="cursor: pointer;">
                            <strong>Kembalikan ke Deskprint</strong>
                            <div class="text-muted small mt-1">
                                <i class="fas fa-times text-danger"></i> Menghapus Pembayaran & Produksi<br>
                                <i class="fas fa-check text-success"></i> Mengembalikan seluruh rincian barang ke Keranjang SPK (Draft)<br>
                                <i class="fas fa-check text-success"></i> Deskprint bisa langsung merevisi/mengedit pesanan tanpa mulai dari awal.
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            <style>
                .form-check-custom:hover { background: #eef2f7 !important; border-color: #adb5bd !important; }
            </style>
        `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3498db',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-undo"></i> Proses Kembalikan',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            width: '550px',
            preConfirm: () => {
                const target = document.querySelector('input[name="rollback_target"]:checked');
                if (!target) {
                    Swal.showValidationMessage('Silakan pilih tujuan pengembalian');
                }
                return target ? target.value : null;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const target = result.value;

                // Show loading
                Swal.fire({
                    title: 'Memproses pemulihan data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // AJAX request to rollback transaction
                fetch('<?= base_url('penjualan/rollback_transaction') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'nospk=' + encodeURIComponent(nospk) + '&target=' + encodeURIComponent(target)
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message || 'Transaksi berhasil dikembalikan',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                // Reload sales data
                                loadSalesData(selectedDate);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message || 'Gagal mengembalikan transaksi'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat memproses rollback'
                        });
                    });
            }
        });
    }

    // Auto-refresh data every 5 minutes
    setInterval(() => {
        loadSalesData(selectedDate);
    }, 300000);
</script>