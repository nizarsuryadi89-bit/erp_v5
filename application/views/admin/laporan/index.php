<link rel="stylesheet" href="<?= base_url('assets/css/tgm-laporan-index.css') ?>">

<div class="app-content">
    <!-- <div class="container-fluid"> -->
    <!-- Header -->
    <?php
    $this->load->view('layout_admin/header_modul');
    ?>

    <!-- Jika Yang Login Administrator / Owner Buatkan Dropdown untuk memilih Cabang mana yang akan dilihat data keuangan nya -->
    <?php if ($is_admin_owner && !empty($all_perusahaan)): ?>
        <div class="card border-0 shadow-sm mb-4"
            style="border-radius: 15px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-1 fw-bold text-dark"><i class="fas fa-building me-2 text-primary"></i> Pilih Cabang /
                            Perusahaan</h5>
                        <p class="text-muted small mb-0">Tampilkan data laporan berdasarkan unit bisnis yang dipilih.</p>
                    </div>
                    <div class="col-md-6 mt-3 mt-md-0">
                        <form action="<?= base_url('laporan/index') ?>" method="GET" id="perusahaanForm">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-store text-muted"></i></span>
                                <select class="form-select border-start-0 ps-0 fw-bold" name="perusahaan_id"
                                    onchange="this.form.submit()" style="height: 50px;">
                                    <?php foreach ($all_perusahaan as $p): ?>
                                        <option value="<?= $p->id_perusahaan ?>" <?= ($current_perusahaan_id == $p->id_perusahaan) ? 'selected' : '' ?>>
                                            <?= $p->nama_perusahaan ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <!-- Statistics Overview -->
    <div class="stats-overview">
        <!-- Pemasukan SPK -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon income">
                    <i class="fa fa-money-bill-wave"></i>
                </div>
                <div class="stat-info">
                    <h3>Rp <?= number_format($total_pendapatan ?? 0, 0, ',', '.') ?></h3>
                    <p>Transaksi SPK (Hari Ini)</p>
                </div>
            </div>
            <?php if (isset($total_pendapatan_kemarin)): ?>
                <div class="stat-footer mt-2 pt-2 border-top">
                    <?php
                    $diff = ($total_pendapatan ?? 0) - ($total_pendapatan_kemarin ?? 0);
                    $pct = ($total_pendapatan_kemarin > 0) ? ($diff / $total_pendapatan_kemarin) * 100 : 0;
                    $color = $diff >= 0 ? 'text-success' : 'text-danger';
                    $icon = $diff >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                    ?>
                    <small class="fw-bold <?= $color ?>">
                        <i class="fa <?= $icon ?> me-1"></i><?= number_format(abs($pct), 1) ?>%
                    </small>
                    <small class="text-muted ms-1">vs Kemarin</small>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pemasukan Piutang -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon profit">
                    <i class="fa fa-hand-holding-usd"></i>
                </div>
                <div class="stat-info">
                    <h3>Rp <?= number_format($total_piutang_hari_ini ?? 0, 0, ',', '.') ?></h3>
                    <p>Bayar Piutang (Hari Ini)</p>
                </div>
            </div>
        </div>

        <!-- Pengeluaran Hari Ini -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon expense">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="stat-info">
                    <h3>Rp <?= number_format($total_pengeluaran ?? 0, 0, ',', '.') ?></h3>
                    <p>Pengeluaran (Hari Ini)</p>
                </div>
            </div>
            <?php if (isset($total_pengeluaran_kemarin)): ?>
                <div class="stat-footer mt-2 pt-2 border-top">
                    <?php
                    $diff_exp = ($total_pengeluaran ?? 0) - ($total_pengeluaran_kemarin ?? 0);
                    $pct_exp = ($total_pengeluaran_kemarin > 0) ? ($diff_exp / $total_pengeluaran_kemarin) * 100 : 0;
                    $color_exp = $diff_exp <= 0 ? 'text-success' : 'text-danger'; // Less expense is good
                    $icon_exp = $diff_exp >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                    ?>
                    <small class="fw-bold <?= $color_exp ?>">
                        <i class="fa <?= $icon_exp ?> me-1"></i><?= number_format(abs($pct_exp), 1) ?>%
                    </small>
                    <small class="text-muted ms-1">vs Kemarin</small>
                </div>
            <?php endif; ?>
        </div>



        <!-- Ringkasan Keuangan -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon info"
                    style="background: linear-gradient(135deg, #3498db, #2980b9); color: white; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fa fa-wallet"></i>
                </div>
                <div class="stat-info">
                    <?php
                    $total_keuangan = ($total_pendapatan ?? 0) + ($total_piutang_hari_ini ?? 0) - ($total_pengeluaran ?? 0);
                    $laba_rugi_class = $total_keuangan >= 0 ? 'text-success' : 'text-danger';
                    ?>
                    <h3 class="<?= $laba_rugi_class ?>">Rp <?= number_format($total_keuangan, 0, ',', '.') ?></h3>
                    <p>Net Cash Flow (Hari Ini)</p>
                    <small class="fw-bold <?= $color_exp ?>">
                        <i class="fa <?= $icon_exp ?> me-1"></i><?= number_format(abs($pct_exp), 1) ?>%
                    </small>
                    <small class="text-muted ms-1">vs Kemarin</small>
                </div>

            </div>
        </div>
    </div>

    <!-- Report Cards -->
    <div class="report-cards">
        <div class="cards-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5><i class="fa fa-file-alt"></i> Laporan Keuangan</h5>
                </div>
                <div class="col-md-6 text-md-end">
                    <small>Pilih jenis laporan yang ingin Anda lihat</small>
                </div>
            </div>
        </div>

        <div class="cards-container">
            <div class="report-card">
                <div class="card-body">
                    <div class="report-icon dark">
                        <i class="fa fa-tachometer-alt"></i>
                    </div>
                    <div class="report-info">
                        <h6 class="report-title">Dashboard Terpadu</h6>
                        <p class="report-description">Dashboard keuangan komprehensif dengan analisis mendalam</p>
                        <a href="<?= base_url('laporan/dashboard_terpadu?perusahaan_id=' . $current_perusahaan_id) ?>"
                            class="report-action">
                            <i class="fa fa-chart-line"></i> Lihat Dashboard
                        </a>
                    </div>
                </div>
            </div>
            <!-- Laba Rugi Card -->
            <div class="report-card">
                <div class="card-body">
                    <div class="report-icon dark">
                        <i class="fa fa-print"></i>
                    </div>
                    <div class="report-info">
                        <h6 class="report-title">Laporan Harian (Official ERP)</h6>
                        <p class="report-description">Cetak laporan harian resmi format ERP konsolidasi</p>
                        <a href="<?= base_url('laporan/print_laporan_harian?date=' . date('Y-m-d')) ?>" target="_blank"
                            class="report-action">
                            <i class="fa fa-print"></i> Cetak Laporan
                        </a>
                    </div>
                </div>
            </div>

            <div class="report-card">
                <div class="card-body">
                    <div class="report-icon primary">
                        <i class="fa fa-calculator"></i>
                    </div>
                    <div class="report-info">
                        <h6 class="report-title">Laporan Laba Rugi</h6>
                        <p class="report-description">Analisis profitabilitas perusahaan secara detail</p>
                        <a href="<?= base_url('laporan/laba_rugi?perusahaan_id=' . $current_perusahaan_id) ?>"
                            class="report-action">
                            <i class="fa fa-chart-line"></i> Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pemasukan Harian Card -->
            <div class="report-card">
                <div class="card-body">
                    <div class="report-icon success">
                        <i class="fa fa-calendar-day"></i>
                    </div>
                    <div class="report-info">
                        <h6 class="report-title">Pemasukan Harian</h6>
                        <p class="report-description">Laporan pendapatan per hari dengan grafik</p>
                        <a href="<?= base_url('laporan/pemasukan_harian?perusahaan_id=' . $current_perusahaan_id) ?>"
                            class="report-action">
                            <i class="fa fa-money-bill-wave"></i> Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pemasukan Bulanan Card -->
            <div class="report-card">
                <div class="card-body">
                    <div class="report-icon info">
                        <i class="fa fa-calendar-alt"></i>
                    </div>
                    <div class="report-info">
                        <h6 class="report-title">Pemasukan Bulanan</h6>
                        <p class="report-description">Laporan pendapatan per bulan dengan trend</p>
                        <a href="<?= base_url('laporan/pemasukan_bulanan?perusahaan_id=' . $current_perusahaan_id) ?>"
                            class="report-action">
                            <i class="fa fa-chart-bar"></i> Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>

            <!-- Laporan Pajak Card -->
            <div class="report-card">
                <div class="card-body">
                    <div class="report-icon warning">
                        <i class="fa fa-file-invoice-dollar"></i>
                    </div>
                    <div class="report-info">
                        <h6 class="report-title">Laporan Pajak</h6>
                        <p class="report-description">Laporan perpajakan perusahaan yang lengkap</p>
                        <a href="<?= base_url('laporan/laporan_pajak?perusahaan_id=' . $current_perusahaan_id) ?>"
                            class="report-action">
                            <i class="fa fa-receipt"></i> Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>

            <!-- Export Excel Card -->


            <!-- Dashboard Terpadu Card -->


            <!-- Rekap Metode Pembayaran Card -->
            <div class="report-card">
                <div class="card-body">
                    <div class="report-icon warning">
                        <i class="fa fa-credit-card"></i>
                    </div>
                    <div class="report-info">
                        <h6 class="report-title">Legger Keuangan</h6>
                        <p class="report-description">Analisis pembayaran cash, transfer, EDC, dan e-wallet</p>
                        <a href="<?= base_url('laporan/legger?perusahaan_id=' . $current_perusahaan_id) ?>"
                            class="report-action">
                            <i class="fa fa-pie-chart"></i> Lihat Rekap
                        </a>
                    </div>
                </div>
            </div>

            <!-- Rekap Metode Pembayaran Card -->
            <div class="report-card">
                <div class="card-body">
                    <div class="report-icon info">
                        <i class="fa fa-chart-line"></i>
                    </div>
                    <div class="report-info">
                        <h6 class="report-title">Rekap Metode Pembayaran</h6>
                        <p class="report-description">Ringkasan keuangan dengan grafik</p>
                        <a href="<?= base_url('laporan/rekap_metode_pembayaran?perusahaan_id=' . $current_perusahaan_id) ?>"
                            class="report-action">
                            <i class="fa fa-upload"></i> Lihat Rekap
                        </a>

                    </div>
                </div>
            </div>

            <div class="report-card">
                <div class="card-body">
                    <div class="report-icon primary">
                        <i class="fa fa-credit-card"></i>
                    </div>
                    <div class="report-info">
                        <h6 class="report-title">Mutasi EDC</h6>
                        <p class="report-description">Detail arus masuk dan keluar via mesin EDC</p>
                        <a href="<?= base_url('laporan/mutasi_edc?perusahaan_id=' . $current_perusahaan_id) ?>"
                            class="report-action">
                            <i class="fa fa-calculator"></i> Lihat Mutasi
                        </a>
                    </div>
                </div>
            </div>

            <div class="report-card">
                <div class="card-body">
                    <div class="report-icon success">
                        <i class="fa fa-wallet"></i>
                    </div>
                    <div class="report-info">
                        <h6 class="report-title">Mutasi E-Wallet</h6>
                        <p class="report-description">Arus dana digital (GoPay, OVO, ShopeePay, dll)</p>
                        <a href="<?= base_url('laporan/mutasi_ewallet?perusahaan_id=' . $current_perusahaan_id) ?>"
                            class="report-action">
                            <i class="fa fa-history"></i> Lihat Mutasi
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pengeluaran Harian Card -->
            <div class="report-card">
                <div class="card-body">
                    <div class="report-icon danger">
                        <i class="fa fa-money-bill-wave"></i>
                    </div>
                    <div class="report-info">
                        <h6 class="report-title">Pengeluaran</h6>
                        <p class="report-description">Laporan pengeluaran perusahaan</p>
                        <a href="<?= base_url('laporan/pengeluaran?perusahaan_id=' . $current_perusahaan_id) ?>"
                            class="report-action">
                            <i class="fa fa-upload"></i> Lihat Rekap
                        </a>
                    </div>
                </div>
            </div>

            <div class="report-card">
                <div class="card-body">
                    <div class="report-icon info">
                        <i class="fa fa-credit-card"></i>
                    </div>
                    <div class="report-info">
                        <h6 class="report-title">Piutang</h6>
                        <p class="report-description">Laporan piutang perusahaan</p>
                        <a href="<?= base_url('laporan/laporan_piutang?perusahaan_id=' . $current_perusahaan_id) ?>"
                            class="report-action">
                            <i class="fa fa-credit-card"></i> Lihat Rekap
                        </a>
                    </div>
                </div>
            </div>

            <div class="report-card">
                <div class="card-body">
                    <div class="report-icon info">
                        <i class="fa fa-credit-card"></i>
                    </div>
                    <div class="report-info">
                        <h6 class="report-title">Hutang</h6>
                        <p class="report-description">Laporan hutang perusahaan</p>
                        <a href="<?= base_url('laporan/hutang?perusahaan_id=' . $current_perusahaan_id) ?>"
                            class="report-action">
                            <i class="fa fa-credit-card"></i> Lihat Rekap
                        </a>
                    </div>
                </div>
            </div>
            <div class="report-card">
                <div class="card-body">
                    <div class="report-icon success">
                        <i class="fa fa-money-bill-alt"></i>
                    </div>
                    <div class="report-info">
                        <h6 class="report-title">Mutasi Cash</h6>
                        <p class="report-description">Detail arus kas tunai masuk dan keluar harian</p>
                        <a href="<?= base_url('laporan/mutasi_cash?perusahaan_id=' . $current_perusahaan_id) ?>"
                            class="report-action">
                            <i class="fa fa-list-alt"></i> Lihat Mutasi
                        </a>
                    </div>
                </div>
            </div>

            <div class="report-card">
                <div class="card-body">
                    <div class="report-icon info">
                        <i class="fa fa-credit-card"></i>
                    </div>
                    <div class="report-info">
                        <h6 class="report-title">Transaksi Manual</h6>
                        <p class="report-description">Laporan Transaksi Manual</p>
                        <a href="<?= base_url('laporan/rekap_transaksi_manual?perusahaan_id=' . $current_perusahaan_id) ?>"
                            class="report-action">
                            <i class="fa fa-credit-card"></i> Lihat Rekap
                        </a>
                    </div>
                </div>
            </div>

            <div class="report-card">
                <div class="card-body">
                    <div class="report-icon warning">
                        <i class="fa fa-crown"></i>
                    </div>
                    <div class="report-info">
                        <h6 class="report-title">Produk Terlaris</h6>
                        <p class="report-description">Peringkat produk paling laku terjual</p>
                        <a href="<?= base_url('laporan/produk_terlaris?perusahaan_id=' . $current_perusahaan_id) ?>"
                            class="report-action">
                            <i class="fa fa-chart-line"></i> Lihat Peringkat
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="exportForm">
                    <div class="mb-3">
                        <label for="exportType" class="form-label">Jenis Laporan</label>
                        <select class="form-select" id="exportType" name="exportType" required>
                            <option value="">Pilih Jenis Laporan</option>
                            <option value="laba_rugi">Laporan Laba Rugi</option>
                            <option value="pemasukan_harian">Pemasukan Harian</option>
                            <option value="pemasukan_bulanan">Pemasukan Bulanan</option>
                            <option value="pajak">Laporan Pajak</option>
                            <option value="dashboard_terpadu">Dashboard Terpadu</option>
                            <option value="rekap_metode_pembayaran">Rekap Metode Pembayaran</option>
                            <option value="laporan_piutang">Laporan Piutang</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="exportPeriode" class="form-label">Periode</label>
                        <input type="month" class="form-control" id="exportPeriode" name="exportPeriode"
                            value="<?= date('Y-m') ?>">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="exportTglAwal" class="form-label">Tanggal Awal</label>
                                <input type="date" class="form-control" id="exportTglAwal" name="exportTglAwal">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="exportTglAkhir" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="exportTglAkhir" name="exportTglAkhir">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" onclick="exportReport()">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats Modal -->
<div class="modal fade" id="quickStatsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Statistik Keuangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <canvas id="pendapatanChart"></canvas>
                    </div>
                    <div class="col-md-6">
                        <canvas id="pengeluaranChart"></canvas>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Metrik</th>
                                        <th class="text-end">Nilai</th>
                                        <th class="text-end">Perubahan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Total Pendapatan</td>
                                        <td class="text-end">Rp
                                            <?= number_format($total_pendapatan ?? 0, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-end text-success">+5.2%</td>
                                    </tr>
                                    <tr>
                                        <td>Laba Kotor</td>
                                        <td class="text-end">Rp <?= number_format($total_laba ?? 0, 0, ',', '.') ?></td>
                                        <td class="text-end text-success">+3.8%</td>
                                    </tr>
                                    <tr>
                                        <td>Total Pengeluaran</td>
                                        <td class="text-end">Rp
                                            <?= number_format($total_pengeluaran ?? 0, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-end text-danger">+2.1%</td>
                                    </tr>
                                    <tr>
                                        <td>Laba Bersih</td>
                                        <td class="text-end">Rp
                                            <?= number_format(($total_laba ?? 0) - ($total_pengeluaran ?? 0), 0, ',', '.') ?>
                                        </td>
                                        <td class="text-end text-success">+4.5%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showExportModal() {
        $('#exportModal').modal('show');
    }

    function showQuickStats() {
        $('#quickStatsModal').modal('show');

        // Create charts when modal is shown
        setTimeout(function () {
            createCharts();
        }, 100);
    }

    function exportReport() {
        const exportType = $('#exportType').val();
        const periode = $('#exportPeriode').val();
        const tglAwal = $('#exportTglAwal').val();
        const tglAkhir = $('#exportTglAkhir').val();

        if (!exportType) {
            alert('Pilih jenis laporan terlebih dahulu');
            return;
        }

        let url = '';
        switch (exportType) {
            case 'laba_rugi':
                url = '<?= base_url("laporan/export_laba_rugi") ?>'; // Note: check if this method exists
                break;
            case 'pajak':
                url = '<?= base_url("laporan/export_pajak") ?>';
                break;
            case 'dashboard_terpadu':
                url = '<?= base_url("laporan/export_dashboard") ?>';
                break;
            case 'rekap_metode_pembayaran':
                url = '<?= base_url("laporan/export_rekap_metode_pembayaran") ?>';
                break;
            case 'pemasukan_harian':
            case 'pemasukan_bulanan':
                url = '<?= base_url("laporan/export_laporan_keuangan") ?>';
                url += '?date=' + (tglAwal || '<?= date("Y-m-d") ?>') + '&';
                break;
            case 'laporan_piutang':
                url = '<?= base_url("laporan/export_laporan_piutang") ?>';
                break;
            default:
                alert('Export untuk laporan ini belum tersedia');
                return;
        }

        // Add parameters
        url += '?perusahaan_id=<?= $current_perusahaan_id ?>&';
        if (periode) url += 'periode=' + periode + '&';
        if (tglAwal) url += 'tgl_awal=' + tglAwal + '&';
        if (tglAkhir) url += 'tgl_akhir=' + tglAkhir + '&';

        window.open(url, '_blank');
        $('#exportModal').modal('hide');
    }

    function createCharts() {
        // Pendapatan Chart
        const ctx1 = document.getElementById('pendapatanChart').getContext('2d');
        new Chart(ctx1, {
            type: 'doughnut',
            data: {
                labels: ['Pendapatan', 'HPP'],
                datasets: [{
                    data: [<?= $total_pendapatan ?? 0 ?>, <?= $total_hpp ?? 0 ?>],
                    backgroundColor: ['#28a745', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Komposisi Pendapatan'
                    }
                }
            }
        });

        // Pengeluaran Chart
        const ctx2 = document.getElementById('pengeluaranChart').getContext('2d');
        new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: ['Pengeluaran Harian', 'Pengeluaran Bulanan'],
                datasets: [{
                    data: [<?= $pengeluaran_harian ?? 0 ?>, <?= $pengeluaran_bulanan ?? 0 ?>],
                    backgroundColor: ['#ffc107', '#fd7e14']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Komposisi Pengeluaran'
                    }
                }
            }
        });
    }

    // Animate stat values on page load
    $(document).ready(function () {
        const statValues = document.querySelectorAll('.stat-info h3');
        statValues.forEach(stat => {
            const text = stat.textContent;
            const numericValue = text.replace(/[^\d]/g, '');

            if (numericValue) {
                const finalValue = parseInt(numericValue);
                let currentValue = 0;
                const increment = finalValue / 50;

                const timer = setInterval(() => {
                    currentValue += increment;
                    if (currentValue >= finalValue) {
                        currentValue = finalValue;
                        clearInterval(timer);
                    }

                    // Format with currency
                    const formattedValue = 'Rp ' + Math.floor(currentValue).toLocaleString('id-ID');
                    stat.textContent = formattedValue;
                }, 30);
            }
        });
    });
</script>