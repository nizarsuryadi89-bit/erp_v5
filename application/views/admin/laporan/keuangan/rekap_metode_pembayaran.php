<style>
    /* Premium Rekap Metode UI */
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

    :root {
        --primary-gradient: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        --success-gradient: linear-gradient(135deg, #28a745, #20c997);
        --danger-gradient: linear-gradient(135deg, #dc3545, #e83e8c);
        --warning-gradient: linear-gradient(135deg, #ffc107, #fd7e14);
        --info-gradient: linear-gradient(135deg, #17a2b8, #6f42c1);
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }

    body {
        font-family: 'Outfit', sans-serif;
        background-color: #f8f9fa;
        color: #2c3e50;
    }

    .report-header {
        background: var(--primary-gradient);
        color: white;
        padding: 3rem 0;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        position: relative;
        z-index: 2;
    }

    .header-icon {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .header-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin: 0;
        line-height: 1.2;
    }

    .header-subtitle {
        font-size: 1.3rem;
        opacity: 0.9;
        font-weight: 300;
        margin-top: 0.5rem;
    }

    .branch-switcher-card {
        border-radius: 15px;
        background: white;
        margin-top: -3.5rem;
        position: relative;
        z-index: 10;
        border: none;
        box-shadow: var(--premium-shadow);
    }

    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid #eef2f7;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--primary-gradient);
    }

    .stat-card.cash::before {
        background: var(--success-gradient);
    }

    .stat-card.piutang::before {
        background: var(--danger-gradient);
    }

    .stat-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 0.5rem;
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        color: white;
        border-radius: 10px;
    }

    .stat-icon.total {
        background: var(--primary-gradient);
    }

    .stat-icon.cash {
        background: var(--success-gradient);
    }

    .stat-icon.transfer {
        background: var(--info-gradient);
    }

    .stat-icon.edc {
        background: var(--warning-gradient);
    }

    .stat-icon.ewallet {
        background: var(--info-gradient);
    }

    .stat-icon.piutang {
        background: var(--danger-gradient);
    }

    .stat-info h3 {
        font-size: 1.3rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
    }

    .stat-info p {
        font-size: 0.7rem;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        margin: 0;
        letter-spacing: 0.5px;
    }

    .report-card {
        background: white;
        border: 1px solid #eef2f7;
        box-shadow: var(--premium-shadow);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .card-header-premium {
        background: #fff;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #f1f3f5;
    }

    .card-header-premium h5 {
        margin: 0;
        font-weight: 700;
        font-size: 1.1rem;
        color: #2c3e50;
    }

    .table-premium {
        width: 100%;
        border-collapse: collapse;
    }

    .table-premium th {
        background: #f8f9fa;
        color: #2c3e50;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        padding: 1rem;
        border-bottom: 2px solid #eef2f7;
        text-align: center;
    }

    .table-premium td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f6f8fb;
        font-size: 0.9rem;
    }

    .table-premium tbody tr:hover {
        background-color: #fcfdfe;
    }

    .method-badge {
        padding: 0.4rem 0.6rem;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.75rem;
        display: inline-block;
        min-width: 80px;
        text-align: center;
    }

    .badge-cash {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .badge-transfer {
        background: #e3f2fd;
        color: #1565c0;
    }

    .badge-edc {
        background: #fff3e0;
        color: #ef6c00;
    }

    .badge-ewallet {
        background: #f3e5f5;
        color: #7b1fa2;
    }

    .badge-piutang {
        background: #ffebee;
        color: #c62828;
    }

    .chart-container {
        padding: 1.5rem;
        height: 300px;
    }

    @media print {

        .report-header,
        .branch-switcher-card,
        .filter-section,
        .no-print {
            display: none !important;
        }

        .report-card {
            border: 1px solid #000;
            box-shadow: none;
        }

        body {
            background: white;
        }
    }
</style>

<div class="app-content">
    <!-- Header -->
    <header class="report-header">
        <div class="container-fluid px-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="header-content">
                        <div class="header-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                        <div class="header-text">
                            <h1 class="header-title">Rekap Metode Pembayaran</h1>
                            <h3 class="header-subtitle"><?= $perusahaan ?> - <?= date('d F Y', strtotime($hariini)) ?>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="no-print">
                        <a href="<?= base_url('laporan') ?>" class="btn btn-outline-secondary fw-bold px-4 py-2"><i
                                class="fas fa-sync-alt me-2"></i>Kembali</a>
                        <button onclick="window.print()" class="btn btn-light fw-bold px-4 py-2"><i
                                class="fas fa-print me-2"></i>CETAK</button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container-fluid px-4">
        <!-- Branch Switcher -->
        <?php if ($is_admin_owner && !empty($all_perusahaan)): ?>
            <div class="card branch-switcher-card mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-1 fw-bold text-dark"><i class="fas fa-building me-2 text-primary"></i> Pilih Unit
                                Bisnis</h5>
                            <p class="text-muted small mb-0">Filter data berdasarkan cabang untuk analisis masuknya dana.
                            </p>
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <form action="" method="GET">
                                <div class="row g-2">
                                    <div class="col-8">
                                        <select class="form-select fw-bold" name="perusahaan_id" style="height: 50px;">
                                            <?php foreach ($all_perusahaan as $p): ?>
                                                <option value="<?= $p->id_perusahaan ?>"
                                                    <?= ($current_perusahaan_id == $p->id_perusahaan) ? 'selected' : '' ?>>
                                                    <?= $p->nama_perusahaan ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <input type="hidden" name="tanggal" value="<?= $hariini ?>">
                                        <button type="submit" class="btn btn-primary w-100 fw-bold"
                                            style="height: 50px;">GANTI</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Stats Overview -->
        <div class="stats-overview">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon total"><i class="fas fa-wallet"></i></div>
                    <div class="stat-info">
                        <p>Total Dana Masuk</p>
                        <h3>Rp <?= number_format($payment_methods['total_all'], 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            <div class="stat-card cash">
                <div class="stat-header">
                    <div class="stat-icon cash"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="stat-info">
                        <p>Tunai (Cash)</p>
                        <h3>Rp <?= number_format($payment_methods['cash']['total'], 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon transfer"><i class="fas fa-university"></i></div>
                    <div class="stat-info">
                        <p>Transfer Bank</p>
                        <h3>Rp <?= number_format($payment_methods['transfer']['total'], 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon edc"><i class="fas fa-credit-card"></i></div>
                    <div class="stat-info">
                        <p>EDC / Debit</p>
                        <h3>Rp <?= number_format($payment_methods['edc']['total'], 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon ewallet"><i class="fas fa-mobile-alt"></i></div>
                    <div class="stat-info">
                        <p>E-Wallet</p>
                        <h3>Rp <?= number_format($payment_methods['ewallet']['total'], 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Filter & Chart -->
            <div class="col-lg-4">
                <div class="report-card no-print">
                    <div class="card-header-premium">
                        <h5><i class="fas fa-filter me-2"></i>Filter Tanggal</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="" method="GET">
                            <input type="hidden" name="perusahaan_id" value="<?= $current_perusahaan_id ?>">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">PILIH TANGGAL</label>
                                <input type="date" name="tanggal" class="form-control fw-bold" value="<?= $hariini ?>"
                                    style="height: 45px;">
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary fw-bold w-100 py-3"
                                        style="background: var(--primary-gradient); border: none;">PROSES</button>
                                </div>
                                <div class="col-6">
                                    <a href="<?= base_url('laporan/rekap_metode_pembayaran') ?>"
                                        class="btn btn-secondary fw-bold w-100 py-3">HARI INI</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="report-card">
                    <div class="card-header-premium">
                        <h5><i class="fas fa-chart-pie me-2"></i>Distribusi Kas</h5>
                    </div>
                    <div class="chart-container text-center">
                        <canvas id="paymentMethodsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Table -->
            <div class="col-lg-8">
                <div class="report-card">
                    <div class="card-header-premium">
                        <h5><i class="fas fa-table me-2"></i>Ringkasan Volume Transaksi</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table-premium">
                            <thead>
                                <tr>
                                    <th class="text-start">METODE PEMBAYARAN</th>
                                    <th class="text-end">NOMINAL TOTAL</th>
                                    <th>KONTRIBUSI (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-start"><i class="fas fa-money-bill-wave text-success me-2"></i>
                                        <strong>Tunai (Cash)</strong>
                                    </td>
                                    <td class="text-end fw-bold">Rp
                                        <?= number_format($payment_methods['cash']['total'], 0, ',', '.') ?>
                                    </td>
                                    <td class="text-center"><span
                                            class="badge-cash method-badge"><?= number_format($payment_methods['cash']['percentage'], 1) ?>%</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-start"><i class="fas fa-university text-primary me-2"></i>
                                        <strong>Transfer Bank</strong>
                                    </td>
                                    <td class="text-end fw-bold">Rp
                                        <?= number_format($payment_methods['transfer']['total'], 0, ',', '.') ?>
                                    </td>
                                    <td class="text-center"><span
                                            class="badge-transfer method-badge"><?= number_format($payment_methods['transfer']['percentage'], 1) ?>%</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-start"><i class="fas fa-credit-card text-warning me-2"></i>
                                        <strong>Mesin EDC / Debit</strong>
                                    </td>
                                    <td class="text-end fw-bold">Rp
                                        <?= number_format($payment_methods['edc']['total'], 0, ',', '.') ?>
                                    </td>
                                    <td class="text-center"><span
                                            class="badge-edc method-badge"><?= number_format($payment_methods['edc']['percentage'], 1) ?>%</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-start"><i class="fas fa-mobile-alt text-info me-2"></i>
                                        <strong>Dompet Digital (E-Wallet)</strong>
                                    </td>
                                    <td class="text-end fw-bold">Rp
                                        <?= number_format($payment_methods['ewallet']['total'], 0, ',', '.') ?>
                                    </td>
                                    <td class="text-center"><span
                                            class="badge-ewallet method-badge"><?= number_format($payment_methods['ewallet']['percentage'], 1) ?>%</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-start"><i class="fas fa-ellipsis-h text-secondary me-2"></i>
                                        <strong>Lainnya / Kredit</strong>
                                    </td>
                                    <td class="text-end fw-bold">Rp
                                        <?= number_format($payment_methods['lainnya']['total'] + $payment_methods['kartu_kredit']['total'], 0, ',', '.') ?>
                                    </td>
                                    <td class="text-center"><span
                                            class="badge-piutang method-badge"><?= number_format($payment_methods['lainnya']['percentage'] + $payment_methods['kartu_kredit']['percentage'], 1) ?>%</span>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr style="background: #f8f9fa;">
                                    <th class="text-start">TOTAL OMSET HARI INI</th>
                                    <th class="text-end text-dark" style="font-size: 1.1rem;">Rp
                                        <?= number_format($payment_methods['total_all'], 0, ',', '.') ?>
                                    </th>
                                    <th class="text-center">100.0%</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Table -->
        <div class="report-card mt-4">
            <div class="card-header-premium">
                <h5><i class="fas fa-list me-2"></i>Data Rincian Pembayaran Masuk</h5>
            </div>
            <div class="table-responsive">
                <table class="table-premium" id="datatable-rekap-detail">
                    <thead>
                        <tr>
                            <th>WAKTU</th>
                            <th class="text-start">NO SPK / KONSUMEN</th>
                            <th>METODE</th>
                            <th class="text-end">JUMLAH</th>
                            <th>KETERANGAN / REF</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payment_methods_data as $pm): ?>
                            <tr>
                                <td class="text-center"><?= date('H:i', strtotime($pm->jam_bayar)) ?></td>
                                <td class="text-start">
                                    <div class="fw-bold text-dark"><?= $pm->nospk ?></div>
                                    <div class="small text-muted">Trans: ID #<?= $pm->pembayaran_id ?></div>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $mclass = 'badge-secondary';
                                    $mname = ucfirst($pm->metode);
                                    if (strtolower($pm->metode) == 'tunai') {
                                        $mclass = 'badge-cash';
                                        $mname = 'CASH';
                                    }
                                    if (strtolower($pm->metode) == 'transfer') {
                                        $mclass = 'badge-transfer';
                                        $mname = 'BANK';
                                    }
                                    if (strtolower($pm->metode) == 'debit') {
                                        $mclass = 'badge-edc';
                                        $mname = 'EDC';
                                    }
                                    if (strtolower($pm->metode) == 'ewallet') {
                                        $mclass = 'badge-ewallet';
                                        $mname = 'WALLET';
                                    }
                                    ?>
                                    <span class="method-badge <?= $mclass ?>"><?= $mname ?></span>
                                </td>
                                <td class="text-end fw-bold text-primary">Rp <?= number_format($pm->jumlah, 0, ',', '.') ?>
                                </td>
                                <td class="text-start small">
                                    <?php if ($pm->nama_bank): ?>
                                        <i class="fas fa-university me-1"></i> <?= $pm->nama_bank ?> (<?= $pm->no_rekening ?>)
                                    <?php elseif ($pm->edc_nama): ?>
                                        <i class="fas fa-credit-card me-1"></i> <?= $pm->edc_nama ?>
                                    <?php elseif ($pm->ewallet_nama): ?>
                                        <i class="fas fa-mobile-alt me-1"></i> <?= $pm->ewallet_nama ?>
                                    <?php endif; ?>
                                    <?= $pm->referensi ? '<br><span class="text-muted">Ref: ' . $pm->referensi . '</span>' : '' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function () {
        // Initialize DataTable
        $('#datatable-rekap-detail').DataTable({
            responsive: true,
            language: { url: '<?= base_url('assets/js/datatables-id.json') ?>' },
            dom: 'Bfrtip',
            buttons: [
                {
                    text: '<i class="fas fa-file-excel me-1"></i> EXCEL',
                    className: 'btn btn-success btn-sm',
                    action: function (e, dt, node, config) {
                        window.location.href = '<?= base_url("laporan/export_rekap_metode_pembayaran?perusahaan_id=" . $current_perusahaan_id . "&tanggal=" . $hariini) ?>';
                    }
                },
                { extend: 'print', text: '<i class="fas fa-print me-1"></i> PRINT', className: 'btn btn-info btn-sm' }
            ],
            pageLength: 25,
            order: [[0, 'desc']]
        });

        // Initialize Pie Chart
        const ctx = document.getElementById('paymentMethodsChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Tunai', 'Transfer', 'EDC', 'E-Wallet', 'Lainnya'],
                datasets: [{
                    data: [
                        <?= $payment_methods['cash']['total'] ?>,
                        <?= $payment_methods['transfer']['total'] ?>,
                        <?= $payment_methods['edc']['total'] ?>,
                        <?= $payment_methods['ewallet']['total'] ?>,
                        <?= $payment_methods['lainnya']['total'] + $payment_methods['kartu_kredit']['total'] ?>
                    ],
                    backgroundColor: ['#28a745', '#17a2b8', '#ffc107', '#6f42c1', '#6c757d'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, font: { family: 'Outfit', size: 12 } } }
                }
            }
        });
    });
</script>