<!-- Mobile Keuangan View - Consistent Dark Theme -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php
$this->load->model('M_transaksi');
$currentMonth = date('Y-m');

// Pendapatan
$pendapatanBulanIni = $this->db->query("
    SELECT DATE(tgl_bayar) as tanggal,
           SUM(CASE WHEN perusahaan_id = '2' THEN total_bayar ELSE 0 END) as store1,
           SUM(CASE WHEN perusahaan_id = '3' THEN total_bayar ELSE 0 END) as store2,
           SUM(total_bayar) as total
    FROM tbl_pembayaran 
    WHERE DATE_FORMAT(tgl_bayar, '%Y-%m') = ?
    GROUP BY DATE(tgl_bayar) ORDER BY tanggal ASC
", array($currentMonth))->result();

$labels = array();
$dataStore1 = array();
$dataStore2 = array();
$totalPendapatan = 0;
foreach ($pendapatanBulanIni as $row) {
    $labels[] = date('d/m', strtotime($row->tanggal));
    $dataStore1[] = (int) $row->store1;
    $dataStore2[] = (int) $row->store2;
    $totalPendapatan += (int) $row->total;
}

// Pengeluaran
$pengeluaranBulanIni = $this->db->query("
    SELECT DATE(pengeluaran_tgl) as tanggal,
           SUM(CASE WHEN perusahaan_id = '2' THEN pengeluaran_jumlah ELSE 0 END) as store1,
           SUM(CASE WHEN perusahaan_id = '3' THEN pengeluaran_jumlah ELSE 0 END) as store2,
           SUM(pengeluaran_jumlah) as total
    FROM tbl_pengeluaran 
    WHERE DATE_FORMAT(pengeluaran_tgl, '%Y-%m') = ?
    GROUP BY DATE(pengeluaran_tgl) ORDER BY tanggal ASC
", array($currentMonth))->result();

$labelsPengeluaran = array();
$dataPengeluaran1 = array();
$dataPengeluaran2 = array();
$totalPengeluaran = 0;
foreach ($pengeluaranBulanIni as $row) {
    $labelsPengeluaran[] = date('d/m', strtotime($row->tanggal));
    $dataPengeluaran1[] = (int) $row->store1;
    $dataPengeluaran2[] = (int) $row->store2;
    $totalPengeluaran += (int) $row->total;
}

$saldo = $totalPendapatan - $totalPengeluaran;
?>

<style>
    :root {
        --primary: #1a1a2e;
        --secondary: #16213e;
    }

    * {
        -webkit-tap-highlight-color: transparent;
        box-sizing: border-box;
    }

    body {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        min-height: 100vh;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        margin: 0;
        padding: 0;
    }

    .page-header {
        padding: 1.5rem 1rem;
        color: #fff;
        text-align: center;
    }

    .page-header h5 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .page-header small {
        opacity: 0.7;
        font-size: 0.8rem;
    }

    .content-container {
        background: #f5f5f5;
        border-radius: 24px 24px 0 0;
        min-height: calc(100vh - 100px);
        padding: 1.25rem;
    }

    /* Summary Cards */
    .summary-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .summary-card {
        padding: 1rem;
        border-radius: 14px;
        text-align: center;
        color: #fff;
    }

    .summary-card.income {
        background: linear-gradient(135deg, #16a34a, #22c55e);
    }

    .summary-card.expense {
        background: linear-gradient(135deg, #dc2626, #ef4444);
    }

    .summary-card .icon {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .summary-card .number {
        font-size: 1rem;
        font-weight: 700;
    }

    .summary-card .label {
        font-size: 0.7rem;
        opacity: 0.9;
    }

    /* Stat Card */
    .stat-card {
        background: #fff;
        border-radius: 14px;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .stat-card-header {
        padding: 0.6rem 0.875rem;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.8rem;
        font-weight: 600;
        color: #333;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        background: #fafafa;
    }

    .stat-card-body {
        padding: 0.875rem;
    }

    /* Chart */
    .chart-container {
        height: 160px;
        width: 100%;
    }

    /* Legend */
    .chart-legend {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 0.5rem;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.7rem;
        color: #666;
    }

    .legend-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    /* Data List */
    .data-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f5f5f5;
    }

    .data-item:last-child {
        border-bottom: none;
    }

    .data-date {
        font-size: 0.8rem;
        font-weight: 600;
        color: #333;
    }

    .data-stores {
        font-size: 0.7rem;
        color: #888;
    }

    .data-total {
        font-size: 0.8rem;
        font-weight: 600;
    }

    .text-green {
        color: #16a34a;
    }

    .text-red {
        color: #dc2626;
    }

    /* Saldo Box */
    .saldo-box {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
        color: #fff;
    }

    .saldo-box .label {
        font-size: 0.75rem;
        opacity: 0.8;
    }

    .saldo-box .number {
        font-size: 1.25rem;
        font-weight: 700;
    }

    /* Buttons */
    .btn-back {
        width: 100%;
        padding: 0.8rem;
        background: #f3f4f6;
        color: #333;
        border: none;
        border-radius: 12px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        margin-top: 0.5rem;
    }

    .btn-back:active {
        background: #e5e7eb;
    }
</style>

<!-- Header -->
<div class="page-header">
    <h5><i class="bi bi-cash-coin me-2"></i>Keuangan</h5>
    <small>Periode <?= date('F Y') ?></small>
</div>

<!-- Content -->
<div class="content-container">

    <!-- Summary Cards -->
    <div class="summary-row">
        <div class="summary-card income">
            <div class="icon"><i class="bi bi-graph-up-arrow"></i></div>
            <div class="number">Rp <?= number_format($totalPendapatan / 1000000, 1) ?>jt</div>
            <div class="label">Pendapatan</div>
        </div>
        <div class="summary-card expense">
            <div class="icon"><i class="bi bi-graph-down-arrow"></i></div>
            <div class="number">Rp <?= number_format($totalPengeluaran / 1000000, 1) ?>jt</div>
            <div class="label">Pengeluaran</div>
        </div>
    </div>

    <!-- Pendapatan Chart -->
    <div class="stat-card">
        <div class="stat-card-header">
            <i class="bi bi-graph-up text-success"></i> Grafik Pendapatan
        </div>
        <div class="stat-card-body">
            <div class="chart-legend">
                <span class="legend-item"><span class="legend-dot" style="background:#16a34a"></span> Store 1</span>
                <span class="legend-item"><span class="legend-dot" style="background:#2563eb"></span> Store 2</span>
            </div>
            <div class="chart-container">
                <canvas id="chartPendapatan"></canvas>
            </div>
        </div>
    </div>

    <!-- Pendapatan List -->
    <div class="stat-card">
        <div class="stat-card-header">
            <i class="bi bi-list-check text-success"></i> Data Pendapatan
        </div>
        <div class="stat-card-body">
            <?php foreach ($pendapatanBulanIni as $row): ?>
                <div class="data-item">
                    <div>
                        <div class="data-date"><?= date('d M', strtotime($row->tanggal)) ?></div>
                        <div class="data-stores">S1: Rp <?= number_format($row->store1) ?> | S2: Rp
                            <?= number_format($row->store2) ?></div>
                    </div>
                    <div class="data-total text-green">Rp <?= number_format($row->total) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Pengeluaran Chart -->
    <div class="stat-card">
        <div class="stat-card-header">
            <i class="bi bi-graph-down text-danger"></i> Grafik Pengeluaran
        </div>
        <div class="stat-card-body">
            <div class="chart-legend">
                <span class="legend-item"><span class="legend-dot" style="background:#dc2626"></span> Store 1</span>
                <span class="legend-item"><span class="legend-dot" style="background:#2563eb"></span> Store 2</span>
            </div>
            <div class="chart-container">
                <canvas id="chartPengeluaran"></canvas>
            </div>
        </div>
    </div>

    <!-- Pengeluaran List -->
    <div class="stat-card">
        <div class="stat-card-header">
            <i class="bi bi-list-check text-danger"></i> Data Pengeluaran
        </div>
        <div class="stat-card-body">
            <?php foreach ($pengeluaranBulanIni as $row): ?>
                <div class="data-item">
                    <div>
                        <div class="data-date"><?= date('d M', strtotime($row->tanggal)) ?></div>
                        <div class="data-stores">S1: Rp <?= number_format($row->store1) ?> | S2: Rp
                            <?= number_format($row->store2) ?></div>
                    </div>
                    <div class="data-total text-red">Rp <?= number_format($row->total) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Saldo -->
    <div class="stat-card">
        <div class="stat-card-header">
            <i class="bi bi-wallet2 text-primary"></i> Ringkasan
        </div>
        <div class="stat-card-body">
            <div class="saldo-box">
                <div class="label">Saldo Bulan Ini</div>
                <div class="number">Rp <?= number_format($saldo) ?></div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <a href="<?= base_url('mobile') ?>" class="btn-back">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>

</div>

<script>
    // Pendapatan Chart
    new Chart(document.getElementById('chartPendapatan'), {
        type: 'line',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [
                { label: 'Store 1', data: <?= json_encode($dataStore1) ?>, borderColor: '#16a34a', tension: 0.3, borderWidth: 2, fill: false },
                { label: 'Store 2', data: <?= json_encode($dataStore2) ?>, borderColor: '#2563eb', tension: 0.3, borderWidth: 2, fill: false }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { font: { size: 9 } } },
                y: { beginAtZero: true, ticks: { font: { size: 9 }, callback: function (v) { return (v / 1000000).toFixed(1) + 'jt'; } } }
            }
        }
    });

    // Pengeluaran Chart
    new Chart(document.getElementById('chartPengeluaran'), {
        type: 'line',
        data: {
            labels: <?= json_encode($labelsPengeluaran) ?>,
            datasets: [
                { label: 'Store 1', data: <?= json_encode($dataPengeluaran1) ?>, borderColor: '#dc2626', tension: 0.3, borderWidth: 2, fill: false },
                { label: 'Store 2', data: <?= json_encode($dataPengeluaran2) ?>, borderColor: '#2563eb', tension: 0.3, borderWidth: 2, fill: false }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { font: { size: 9 } } },
                y: { beginAtZero: true, ticks: { font: { size: 9 }, callback: function (v) { return (v / 1000000).toFixed(1) + 'jt'; } } }
            }
        }
    });
</script>