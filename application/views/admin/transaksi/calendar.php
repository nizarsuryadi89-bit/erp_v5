<!-- External CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">

<style>
    /* Enhanced Calendar Management Styles */
    .calendar-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 2.5rem 0;
        margin-bottom: 2rem;
        border-radius: 0;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
        position: relative;
        overflow: hidden;
    }

    .calendar-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        pointer-events: none;
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
        border-radius: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .header-text {
        flex: 1;
    }

    .header-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin: 0 0 0.5rem 0;
        line-height: 1.2;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .header-subtitle {
        font-size: 1.3rem;
        font-weight: 400;
        margin: 0 0 0.75rem 0;
        opacity: 0.9;
        font-weight: 300;
    }

    .header-description {
        font-size: 1rem;
        margin: 0;
        opacity: 0.8;
        line-height: 1.5;
        max-width: 500px;
    }

    .header-info {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        position: relative;
        z-index: 2;
    }

    .info-card {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 0;
        padding: 1rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 120px;
        transition: all 0.3s ease;
    }

    .info-card:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }

    .info-icon {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .info-content {
        flex: 1;
    }

    .info-value {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .info-label {
        font-size: 0.8rem;
        opacity: 0.8;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 0;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
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
        background: linear-gradient(90deg, #2c3e50, #34495e);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .stat-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .stat-icon.unpaid {
        background: linear-gradient(135deg, #dc3545, #c82333);
    }

    .stat-icon.paid {
        background: linear-gradient(135deg, #28a745, #218838);
    }

    .stat-icon.total {
        background: linear-gradient(135deg, #2c3e50, #34495e);
    }

    .stat-icon.date {
        background: linear-gradient(135deg, #6c757d, #5a6268);
    }

    .stat-info h4 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
        line-height: 1;
    }

    .stat-info h6 {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    .stat-info small {
        font-size: 0.8rem;
        color: #6c757d;
        font-weight: 400;
    }

    .quick-actions {
        background: white;
        border-radius: 0;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        margin-bottom: 2rem;
    }

    .quick-actions h5 {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .action-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .action-btn {
        background: linear-gradient(135deg, #2c3e50, #34495e);
        color: white;
        border: none;
        border-radius: 0;
        padding: 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        text-align: center;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(44, 62, 80, 0.4);
        color: white;
        text-decoration: none;
    }

    .action-btn i {
        font-size: 1.5rem;
    }

    /* Calendar Card */
    .calendar-card {
        background: #fff;
        border-radius: 0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        overflow: hidden;
    }

    .calendar-card .card-header {
        background: linear-gradient(135deg, #2c3e50, #34495e);
        color: #fff;
        border-bottom: none;
        padding: 1rem 1.5rem;
    }

    .calendar-card .card-header h5 {
        font-weight: 600;
        margin: 0;
    }

    .calendar-controls {
        display: flex;
        align-items: center;
    }

    .calendar-controls button {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #fff;
        border-radius: 0;
        padding: 0.5rem 0.75rem;
        transition: all 0.3s ease;
    }

    .calendar-controls button:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.5);
        transform: translateY(-1px);
    }

    .calendar-controls span {
        color: #fff;
        font-weight: 600;
        min-width: 120px;
        text-align: center;
    }

    /* FullCalendar Customization */
    .fc-toolbar {
        display: none !important;
    }

    .fc-daygrid-day {
        transition: all 0.3s ease;
    }

    .fc-daygrid-day:hover {
        background-color: rgba(44, 62, 80, 0.05);
    }

    .fc-daygrid-day-number {
        font-weight: 600;
        color: #2c3e50;
    }

    .fc-daygrid-day.fc-day-today {
        background-color: rgba(44, 62, 80, 0.1) !important;
    }

    .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
        background: #2c3e50;
        color: #fff;
        border-radius: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 2px;
    }

    /* Transaction Tables */
    .transaction-table {
        background: #fff;
        border-radius: 0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
    }

    .transaction-table .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 1rem 1.5rem;
    }

    .transaction-table .card-header h6 {
        margin: 0;
        color: #2c3e50;
        font-weight: 600;
    }

    /* Status badges */
    .badge-paid {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        border-radius: 0;
    }

    .badge-unpaid {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        border-radius: 0;
    }

    /* Payment method colors */
    .payment-cash {
        color: #28a745;
    }

    .payment-transfer {
        color: #007bff;
    }

    .payment-edc {
        color: #6f42c1;
    }

    .payment-ewallet {
        color: #fd7e14;
    }
</style>

<div class="app-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="calendar-header">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-7">
                    <div class="header-content">
                        <div class="header-icon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <div class="header-text">
                            <h1 class="header-title">Kalender Penjualan</h1>
                            <h3 class="header-subtitle"><?= $perusahaan ?? 'Perusahaan' ?></h3>
                            <p class="header-description">Dashboard untuk Owner, Administrator, dan Kasir - Menampilkan
                                transaksi sudah bayar dan belum bayar</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-5">
                    <div class="header-info">
                        <div class="info-card">
                            <a href="<?= base_url('transaksi') ?>" style="color: white; text-decoration: none;">
                                <i class="fa fa-arrow-left"></i>
                                Kembali
                            </a>
                        </div>
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fa fa-calendar-alt"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-value"><?= date('d') ?></div>
                                <div class="info-label"><?= date('M Y') ?></div>
                            </div>
                        </div>
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fa fa-clock"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-value"><?= date('H:i') ?></div>
                                <div class="info-label">Waktu</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Overview -->
        <div class="stats-overview">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon unpaid">
                        <i class="fa fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h4 id="totalUnpaidCount"><?= $sales_data['total_unpaid'] ?></h4>
                        <h6>Belum Bayar</h6>
                        <small>Transaksi</small>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon paid">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h4 id="totalPaidCount"><?= $sales_data['total_paid'] ?></h4>
                        <h6>Sudah Bayar</h6>
                        <small>Transaksi</small>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon total">
                        <i class="fa fa-dollar-sign"></i>
                    </div>
                    <div class="stat-info">
                        <h4 id="totalDailyAmount">
                            Rp
                            <?= number_format($sales_data['total_paid_amount'] + $sales_data['total_unpaid_amount']) ?>
                        </h4>
                        <h6>Total Hari Ini</h6>
                        <small>Pendapatan</small>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon date">
                        <i class="fa fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h4 id="selectedDateDisplay">
                            <?= date('d/m/Y', strtotime($selected_date)) ?>
                        </h4>
                        <h6>Tanggal</h6>
                        <small>Dipilih</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Row -->
        <div class="row">
            <!-- Left Column: Calendar and Transaction Lists -->
            <div class="col-lg-8 col-md-12 mb-4">
                <!-- Calendar Section -->
                <div class="calendar-card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-calendar3 me-2"></i>Kalender Penjualan
                        </h5>
                        <div class="calendar-controls">
                            <button class="btn btn-sm" onclick="previousMonth()">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <span class="mx-2" id="currentMonthYear">
                                <?= date('F Y', mktime(0, 0, 0, $selected_month, 1, $selected_year)) ?>
                            </span>
                            <button class="btn btn-sm" onclick="nextMonth()">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="salesCalendar"></div>
                    </div>
                </div>

                <!-- Transaction Details (Moved here for better flow) -->
                <div class="row">
                    <!-- Unpaid Transactions -->
                    <div class="col-md-6 mb-4">
                        <div class="transaction-table h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="bi bi-clock text-danger me-2"></i>Belum Bayar
                                </h6>
                                <span class="badge badge-unpaid" id="unpaidBadge"><?= $sales_data['total_unpaid'] ?>
                                    transaksi</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>No SPK</th>
                                                <th>Pelanggan</th>
                                                <th class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="unpaidTransactions">
                                            <?php if (!empty($sales_data['unpaid'])): ?>
                                                <?php foreach ($sales_data['unpaid'] as $transaction): ?>
                                                    <tr>
                                                        <td>
                                                            <a href="<?= base_url('transaksi/createspk/' . $transaction->nospk) ?>"
                                                                class="text-decoration-none fw-bold">
                                                                <?= $transaction->nospk ?>
                                                            </a>
                                                            <div class="small text-muted"><?= $transaction->deskprint_name ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div><?= $transaction->konsumen_nama ?></div>
                                                            <small class="text-muted"><?= $transaction->konsumen_nohp ?></small>
                                                        </td>
                                                        <td class="text-danger fw-bold text-end">
                                                            Rp <?= number_format($transaction->total_penjualan ?? 0) ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-3">
                                                        <small>Tidak ada transaksi belum bayar</small>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paid Transactions -->
                    <div class="col-md-6 mb-4">
                        <div class="transaction-table h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="bi bi-check-circle text-success me-2"></i>Sudah Bayar
                                </h6>
                                <span class="badge badge-paid" id="paidBadge"><?= $sales_data['total_paid'] ?>
                                    transaksi</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>No SPK</th>
                                                <th>Pelanggan</th>
                                                <th class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="paidTransactions">
                                            <?php if (!empty($sales_data['paid'])): ?>
                                                <?php foreach ($sales_data['paid'] as $transaction): ?>
                                                    <tr>
                                                        <td>
                                                            <a href="<?= base_url('transaksi/cek_invoice/' . $transaction->nospk) ?>"
                                                                class="text-decoration-none fw-bold" target="_blank">
                                                                <?= $transaction->nospk ?>
                                                            </a>
                                                            <div class="small text-muted"><?= $transaction->kasir_name ?></div>
                                                        </td>
                                                        <td>
                                                            <div><?= $transaction->konsumen_nama ?></div>
                                                            <small class="text-muted"><?= $transaction->konsumen_nohp ?></small>
                                                        </td>
                                                        <td class="text-success fw-bold text-end">
                                                            Rp <?= number_format($transaction->sub_total) ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-3">
                                                        <small>Tidak ada transaksi sudah bayar</small>
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

            <!-- Right Column: Sidebar (Summary, Actions, Payment Methods) -->
            <div class="col-lg-4 col-md-12 mb-4">
                <!-- Quick Actions (Moved from top) -->
                <div class="quick-actions mb-4">
                    <h5>
                        <i class="fa fa-bolt me-2"></i>
                        Aksi Cepat
                    </h5>
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('transaksi/createspk') ?>" class="btn btn-outline-primary text-start">
                            <i class="fa fa-plus-circle me-2"></i> Buat SPK Baru
                        </a>
                        <a href="<?= base_url('transaksi/transaksi_belumbayar') ?>"
                            class="btn btn-outline-danger text-start">
                            <i class="fa fa-table me-2"></i> SPK Belum Bayar
                        </a>
                        <a href="<?= base_url('penjualan') ?>" class="btn btn-outline-success text-start">
                            <i class="fa fa-chart-bar me-2"></i> Laporan Penjualan
                        </a>
                    </div>
                </div>

                <!-- Daily Summary -->
                <div class="transaction-table mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-bar-chart me-2"></i>Ringkasan Harian
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h5 class="text-success mb-1" id="summaryPaidCount"><?= $sales_data['total_paid'] ?>
                                    </h5>
                                    <small class="text-muted">Sudah Bayar</small>
                                    <br>
                                    <small class="text-success fw-bold" id="summaryPaidAmount">Rp
                                        <?= number_format($sales_data['total_paid_amount']) ?></small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h5 class="text-danger mb-1" id="summaryUnpaidCount"><?= $sales_data['total_unpaid'] ?>
                                </h5>
                                <small class="text-muted">Belum Bayar</small>
                                <br>
                                <small class="text-danger fw-bold" id="summaryUnpaidAmount">Rp
                                    <?= number_format($sales_data['total_unpaid_amount']) ?></small>
                            </div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <h6 class="mb-1 text-muted">Total Pendapatan Hari Ini</h6>
                            <h3 class="text-primary mb-0 fw-bold" id="summaryTotalAmount">
                                Rp
                                <?= number_format($sales_data['total_paid_amount'] + $sales_data['total_unpaid_amount']) ?>
                            </h3>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="transaction-table">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-credit-card me-2"></i>Metode Pembayaran
                        </h6>
                    </div>
                    <div class="card-body" id="paymentMethods">
                        <?php if (!empty($sales_detail)): ?>
                            <?php foreach ($sales_detail as $method): ?>
                                <div
                                    class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2 last-border-0">
                                    <div>
                                        <span class="payment-<?= strtolower($method->metode) ?> fw-bold">
                                            <i class="bi bi-circle-fill me-1 small"></i><?= ucfirst($method->metode) ?>
                                        </span>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold">Rp <?= number_format($method->total) ?></div>
                                        <small class="text-muted"><?= $method->jumlah ?> transaksi</small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted text-center py-3 mb-0">Tidak ada data pembayaran</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- External JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let calendar;
    let currentYear = <?= $selected_year ?>;
    let currentMonth = <?= $selected_month ?>;
    let selectedDate = '<?= $selected_date ?>';

    // Calendar events data
    const calendarEvents = <?= json_encode($calendar_events) ?>;

    document.addEventListener('DOMContentLoaded', function () {
        initializeCalendar();
        updateSelectedDateDisplay();
    });

    function initializeCalendar() {
        const calendarEl = document.getElementById('salesCalendar');

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            initialDate: `${currentYear}-${String(currentMonth).padStart(2, '0')}-01`,
            locale: 'id',
            headerToolbar: false, // We'll use custom controls
            events: function (info, successCallback, failureCallback) {
                // Load events for the month
                loadCalendarEvents(info.startStr, info.endStr, successCallback);
            },
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

    function loadSalesData(date) {
        selectedDate = date;

        // Update URL without page reload
        const url = new URL(window.location);
        url.searchParams.set('date', date);
        window.history.pushState({}, '', url);

        // Show loading state
        showLoadingState();

        // Load sales data via AJAX
        fetch(`<?= base_url('transaksi/get_sales_by_date') ?>?date=${date}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateSalesData(data.data, data.detail);
                    updateSelectedDateDisplay();
                } else {
                    console.error('Error loading sales data:', data.error);
                    Swal.fire('Error', 'Gagal memuat data penjualan', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Terjadi kesalahan saat memuat data', 'error');
            })
            .finally(() => {
                hideLoadingState();
            });
    }

    function showLoadingState() {
        // Add loading overlay to calendar
        const calendarEl = document.getElementById('salesCalendar');
        if (!document.getElementById('loadingOverlay')) {
            const overlay = document.createElement('div');
            overlay.id = 'loadingOverlay';
            overlay.style.cssText = `
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        `;
            overlay.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
            calendarEl.style.position = 'relative';
            calendarEl.appendChild(overlay);
        }
    }

    function hideLoadingState() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.remove();
        }
    }

    function updateSalesData(salesData, salesDetail) {
        // Update statistics cards
        document.getElementById('totalPaidCount').textContent = salesData.total_paid || 0;
        document.getElementById('totalUnpaidCount').textContent = salesData.total_unpaid || 0;

        const totalAmount = (salesData.total_paid_amount || 0) + (salesData.total_unpaid_amount || 0);
        document.getElementById('totalDailyAmount').textContent = 'Rp ' + formatNumber(totalAmount);

        // Update summary section
        document.getElementById('summaryPaidCount').textContent = salesData.total_paid || 0;
        document.getElementById('summaryUnpaidCount').textContent = salesData.total_unpaid || 0;
        document.getElementById('summaryPaidAmount').textContent = 'Rp ' + formatNumber(salesData.total_paid_amount || 0);
        document.getElementById('summaryUnpaidAmount').textContent = 'Rp ' + formatNumber(salesData.total_unpaid_amount || 0);
        document.getElementById('summaryTotalAmount').textContent = 'Rp ' + formatNumber(totalAmount);

        // Update transaction tables
        updateTransactionTable('paidTransactions', salesData.paid || [], 'paid');
        updateTransactionTable('unpaidTransactions', salesData.unpaid || [], 'unpaid');

        // Update payment methods
        updatePaymentMethods(salesDetail);
    }

    function updateTransactionTable(tableId, transactions, type) {
        const tableBody = document.getElementById(tableId);
        const badgeElement = document.getElementById(type + 'Badge');

        if (!tableBody) {
            console.error(`Table body element with ID '${tableId}' not found`);
            return;
        }

        if (transactions.length > 0) {
            let html = '';
            transactions.forEach(transaction => {
                // Determine link based on transaction type
                let spkLink;
                if (type === 'paid') {
                    spkLink = `<a href="<?= base_url('transaksi/cek_invoice/') ?>${transaction.nospk}" class="text-decoration-none" target="_blank">${transaction.nospk}</a>`;
                } else if (type === 'unpaid') {
                    spkLink = `<a href="<?= base_url('transaksi/createspk/') ?>${transaction.nospk}" class="text-decoration-none">${transaction.nospk}</a>`;
                } else {
                    spkLink = `<a href="<?= base_url('transaksi/cek_invoice/') ?>${transaction.nospk}" class="text-decoration-none" target="_blank">${transaction.nospk}</a>`;
                }

                // Determine amount and styling based on type
                let amount, amountClass, staffName;
                if (type === 'paid') {
                    amount = transaction.sub_total;
                    amountClass = 'text-success';
                    staffName = transaction.kasir_name;
                } else {
                    amount = transaction.total_penjualan || 0;
                    amountClass = 'text-danger';
                    staffName = transaction.deskprint_name;
                }

                html += `
                <tr>
                    <td>${spkLink}</td>
                    <td>
                        <div>${transaction.konsumen_nama || 'N/A'}</div>
                        <small class="text-muted">${transaction.konsumen_nohp || 'N/A'}</small>
                    </td>
                    <td class="${amountClass} fw-bold">
                        Rp ${formatNumber(amount || 0)}
                    </td>
                    <td>
                        <small class="text-muted">${staffName || 'N/A'}</small>
                    </td>
                </tr>
            `;
            });
            tableBody.innerHTML = html;

            if (badgeElement) {
                badgeElement.textContent = transactions.length + ' transaksi';
            }
        } else {
            // Determine empty message based on type
            let emptyMessage;
            switch (type) {
                case 'paid':
                    emptyMessage = 'Tidak ada transaksi sudah bayar';
                    break;
                case 'unpaid':
                    emptyMessage = 'Tidak ada transaksi belum bayar';
                    break;
                default:
                    emptyMessage = 'Tidak ada transaksi';
            }

            tableBody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center text-muted py-3">
                    <i class="bi bi-inbox fa-2x mb-2"></i>
                    <p class="mb-0">${emptyMessage}</p>
                </td>
            </tr>
        `;

            if (badgeElement) {
                badgeElement.textContent = '0 transaksi';
            }
        }
    }

    function updatePaymentMethods(salesDetail) {
        const container = document.getElementById('paymentMethods');
        if (salesDetail && salesDetail.length > 0) {
            let html = '';
            salesDetail.forEach(method => {
                html += `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <span class="payment-${method.metode.toLowerCase()}">
                            <i class="bi bi-circle-fill me-1"></i>${method.metode.charAt(0).toUpperCase() + method.metode.slice(1)}
                        </span>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold">Rp ${formatNumber(method.total)}</div>
                        <small class="text-muted">${method.jumlah} transaksi</small>
                    </div>
                </div>
            `;
            });
            container.innerHTML = html;
        } else {
            container.innerHTML = '<p class="text-muted text-center">Tidak ada data pembayaran</p>';
        }
    }

    function updateSelectedDateDisplay() {
        const dateDisplay = document.getElementById('selectedDateDisplay');
        const date = new Date(selectedDate);
        dateDisplay.textContent = date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
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

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }

    function loadCalendarEvents(startStr, endStr, callback) {
        const startDate = new Date(startStr);
        const year = startDate.getFullYear();
        const month = startDate.getMonth() + 1;

        fetch(`<?= base_url('transaksi/get_calendar_events') ?>?year=${year}&month=${month}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    callback(data.data);
                } else {
                    console.warn('No data or error in response:', data);
                    callback([]);
                }
            })
            .catch(error => {
                console.error('Error loading calendar events:', error);
                callback([]);
            });
    }

    // Auto-refresh data every 5 minutes
    setInterval(() => {
        loadSalesData(selectedDate);
    }, 300000);
</script>