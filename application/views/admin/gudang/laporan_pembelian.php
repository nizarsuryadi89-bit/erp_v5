<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pembelian - TGM ERP</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">
</head>
<body>
<div class="app-content">
    <div class="container-fluid">
        <?php 
            $this->load->view('layout_admin/header_modul');
        ?>

        <div class="container-fluid">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stats-card h-100 fade-in-up">
                        <div class="card-body">
                            <div class="icon-wrapper">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                            <h3 id="totalTransaksi"><?= $total_transaksi ?></h3>
                            <p>Transaksi Hari Ini</p>
                            <small class="text-muted"><?= date('d F Y', strtotime($selected_date)) ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stats-card success h-100 fade-in-up">
                        <div class="card-body">
                            <div class="icon-wrapper">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                            <h3 id="totalNilai">Rp <?= number_format($total_nilai) ?></h3>
                            <p>Total Nilai Pembelian</p>
                            <small class="text-muted">Hari ini</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stats-card warning h-100 fade-in-up">
                        <div class="card-body">
                            <div class="icon-wrapper">
                                <i class="bi bi-people"></i>
                            </div>
                            <h3><?= count($supplier_list) ?></h3>
                            <p>Total Supplier</p>
                            <small class="text-muted">Aktif</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stats-card info h-100 fade-in-up">
                        <div class="card-body">
                            <div class="icon-wrapper">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <h3><?= count($bahan_list) ?></h3>
                            <p>Jenis Bahan Baku</p>
                            <small class="text-muted">Tersedia</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Calendar Section -->
                <div class="col-lg-8 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-calendar3 me-2"></i>Kalender Pembelian
                                </h5>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-light btn-sm" onclick="previousMonth()">
                                        <i class="bi bi-chevron-left"></i>
                                    </button>
                                    <span class="btn btn-outline-light btn-sm" id="currentMonth">
                                        <?= date('F Y', mktime(0, 0, 0, $selected_month, 1, $selected_year)) ?>
                                    </span>
                                    <button class="btn btn-outline-light btn-sm" onclick="nextMonth()">
                                        <i class="bi bi-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>

                <!-- Purchase Details Section -->
                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-list-ul me-2"></i>Detail Pembelian
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="purchaseDetails">
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-calendar-x fs-1"></i>
                                    <p class="mt-2">Pilih tanggal pada kalender untuk melihat detail pembelian</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Purchase Data Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-table me-2"></i>Data Pembelian - <span id="selectedDateDisplay"><?= date('d F Y', strtotime($selected_date)) ?></span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="purchaseTable">
                                <!-- Purchase data will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Detail Modal -->
    <div class="modal fade" id="purchaseDetailModal" tabindex="-1" aria-labelledby="purchaseDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="purchaseDetailModalLabel">
                        <i class="bi bi-receipt me-2"></i>Detail Pembelian
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalPurchaseDetail">
                    <!-- Purchase detail content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- FullCalendar Library -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    
    <script>
    let currentYear = <?= $selected_year ?>;
    let currentMonth = <?= $selected_month ?>;
    let selectedDate = '<?= $selected_date ?>';
    let calendarEvents = <?= json_encode($calendar_events) ?>;

    document.addEventListener('DOMContentLoaded', function() {
        initializeCalendar();
        loadPurchaseData(selectedDate);
    });

    function initializeCalendar() {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            initialDate: `${currentYear}-${String(currentMonth).padStart(2, '0')}-01`,
            headerToolbar: false,
            height: 'auto',
            dayMaxEvents: true,
            events: generateCalendarEvents(),
            dateClick: function(info) {
                const clickedDate = info.dateStr;
                selectedDate = clickedDate;
                loadPurchaseData(clickedDate);
                updateSelectedDateDisplay(clickedDate);
            },
            eventClick: function(info) {
                const clickedDate = info.event.startStr.split('T')[0];
                selectedDate = clickedDate;
                loadPurchaseData(clickedDate);
                updateSelectedDateDisplay(clickedDate);
            },
            eventDidMount: function(info) {
                // Add tooltip
                const event = info.event;
                const date = event.startStr.split('T')[0];
                const eventData = calendarEvents[date];
                if (eventData) {
                    info.el.title = `${eventData.jumlah_transaksi} transaksi - Rp ${formatNumber(eventData.total_nilai)}`;
                }
            }
        });
        calendar.render();
        window.calendar = calendar;
    }

    function generateCalendarEvents() {
        const events = [];
        for (const [date, data] of Object.entries(calendarEvents)) {
            events.push({
                title: `${data.jumlah_transaksi} transaksi`,
                date: date,
                backgroundColor: data.total_nilai > 1000000 ? '#dc3545' : (data.total_nilai > 500000 ? '#fd7e14' : '#28a745'),
                borderColor: data.total_nilai > 1000000 ? '#dc3545' : (data.total_nilai > 500000 ? '#fd7e14' : '#28a745'),
                textColor: '#ffffff',
                extendedProps: {
                    jumlah_transaksi: data.jumlah_transaksi,
                    total_nilai: data.total_nilai
                }
            });
        }
        return events;
    }

    function loadPurchaseData(date) {
        // Show loading
        document.getElementById('purchaseDetails').innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Memuat data pembelian...</p>
            </div>
        `;

        // AJAX request
        fetch('<?= base_url('warehouse/get_pembelian_by_date') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `date=${date}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updatePurchaseDetails(data.data);
                updatePurchaseTable(data.data);
                updateStatistics(data.data);
            } else {
                showError('Gagal memuat data: ' + data.message);
            }
        })
        .catch(error => {
            showError('Terjadi kesalahan saat memuat data');
        });
    }

    function updatePurchaseDetails(data) {
        const container = document.getElementById('purchaseDetails');
        
        if (data.pembelian_data.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="bi bi-calendar-x fs-1"></i>
                    <p class="mt-2">Tidak ada data pembelian pada tanggal ini</p>
                </div>
            `;
            return;
        }

        let html = `
            <div class="mb-3">
                <h6 class="text-success mb-2">
                    <i class="bi bi-calendar-check me-1"></i>
                    ${data.formatted_date}
                </h6>
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <h6 class="mb-0 text-primary">${data.total_transaksi}</h6>
                            <small class="text-muted">Transaksi</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <h6 class="mb-0 text-success">Rp ${formatNumber(data.total_nilai)}</h6>
                            <small class="text-muted">Total Nilai</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="purchase-summary">
        `;

        // Group by supplier
        const supplierGroups = {};
        data.pembelian_data.forEach(pembelian => {
            if (!supplierGroups[pembelian.supplier_nama]) {
                supplierGroups[pembelian.supplier_nama] = [];
            }
            supplierGroups[pembelian.supplier_nama].push(pembelian);
        });

        for (const [supplier, pembelians] of Object.entries(supplierGroups)) {
            const totalSupplier = pembelians.reduce((sum, p) => sum + parseFloat(p.pembelian_total), 0);
            html += `
                <div class="supplier-item mb-2 p-2 border rounded">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong class="text-primary">${supplier}</strong>
                            <br><small class="text-muted">${pembelians.length} transaksi</small>
                        </div>
                        <div class="text-end">
                            <strong class="text-success">Rp ${formatNumber(totalSupplier)}</strong>
                        </div>
                    </div>
                </div>
            `;
        }

        html += `
            </div>
            <div class="mt-3">
                <button class="btn btn-primary btn-sm w-100" onclick="showPurchaseDetailModal('${selectedDate}')">
                    <i class="bi bi-eye me-1"></i>Lihat Detail Lengkap
                </button>
            </div>
        `;

        container.innerHTML = html;
    }

    function updatePurchaseTable(data) {
        const container = document.getElementById('purchaseTable');
        
        if (data.pembelian_data.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-1"></i>
                    <p class="mt-2">Tidak ada data pembelian untuk ditampilkan</p>
                </div>
            `;
            return;
        }

        let html = `
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>No Faktur</th>
                            <th>Supplier</th>
                            <th>Tanggal</th>
                            <th>Total Pembelian</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        data.pembelian_data.forEach((pembelian, index) => {
            const totalValue = parseFloat(pembelian.pembelian_total) || 0;
            
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td><strong>${pembelian.no_faktur || 'N/A'}</strong></td>
                    <td>${pembelian.supplier_nama || 'N/A'}</td>
                    <td>${formatDate(pembelian.pembelian_tgl)}</td>
                    <td><span class="badge bg-success">Rp ${formatNumber(totalValue)}</span></td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="showPurchaseDetailModal('${selectedDate}', ${pembelian.pembelian_id})" title="Lihat Detail">
                            <i class="bi bi-eye"></i>
                        </button>
                        <a href="<?= base_url('warehouse/purchase_edit/') ?>${pembelian.pembelian_id}" class="btn btn-warning btn-sm" title="Edit Data">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </td>
                </tr>
            `;
        });

        html += `
                    </tbody>
                </table>
            </div>
        `;

        container.innerHTML = html;
    }

    function updateStatistics(data) {
        document.getElementById('totalTransaksi').textContent = data.total_transaksi;
        document.getElementById('totalNilai').textContent = `Rp ${formatNumber(data.total_nilai)}`;
    }

    function updateSelectedDateDisplay(date) {
        document.getElementById('selectedDateDisplay').textContent = formatDate(date);
    }

    function showPurchaseDetailModal(date, pembelianId = null) {
        const modal = new bootstrap.Modal(document.getElementById('purchaseDetailModal'));
        const modalBody = document.getElementById('modalPurchaseDetail');
        
        modalBody.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Memuat detail pembelian...</p>
            </div>
        `;
        
        modal.show();

        // Load purchase detail data
        fetch('<?= base_url('warehouse/get_pembelian_by_date') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `date=${date}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showPurchaseDetailContent(data.data, pembelianId);
            } else {
                modalBody.innerHTML = `
                    <div class="text-center text-danger py-4">
                        <i class="bi bi-exclamation-triangle fs-1"></i>
                        <p class="mt-2">Gagal memuat detail pembelian</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            modalBody.innerHTML = `
                <div class="text-center text-danger py-4">
                    <i class="bi bi-exclamation-triangle fs-1"></i>
                    <p class="mt-2">Terjadi kesalahan saat memuat data</p>
                </div>
            `;
        });
    }

    function showPurchaseDetailContent(data, pembelianId = null) {
        const modalBody = document.getElementById('modalPurchaseDetail');
        
        let html = `
            <div class="mb-3">
                <h6 class="text-primary">
                    <i class="bi bi-calendar-check me-1"></i>
                    Detail Pembelian - ${data.formatted_date}
                </h6>
            </div>
        `;

        if (pembelianId) {
            // Show specific purchase detail
            const pembelian = data.pembelian_data.find(p => p.pembelian_id == pembelianId);
            if (pembelian) {
                const details = data.pembelian_detail.filter(d => d.pembelian_id == pembelianId);
                html += generatePurchaseDetailHTML(pembelian, details);
            }
        } else {
            // Show all purchases for the date
            data.pembelian_data.forEach(pembelian => {
                const details = data.pembelian_detail.filter(d => d.pembelian_id == pembelian.pembelian_id);
                html += generatePurchaseDetailHTML(pembelian, details);
            });
        }

        modalBody.innerHTML = html;
    }

    function generatePurchaseDetailHTML(pembelian, details) {
        let html = `
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>No Faktur: ${pembelian.no_faktur}</strong>
                        </div>
                        <div class="col-md-6 text-end">
                            <strong class="text-success">Total: Rp ${formatNumber(pembelian.pembelian_total)}</strong>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <small class="text-muted">Supplier: ${pembelian.supplier_nama}</small>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">Tanggal: ${formatDate(pembelian.pembelian_tgl)}</small>
                            <br>
                            <a href="<?= base_url('warehouse/purchase_edit/') ?>${pembelian.pembelian_id}" class="btn btn-warning btn-sm mt-2">
                                <i class="bi bi-pencil me-1"></i> Edit Transaksi
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-secondary">
                                <tr>
                                    <th>No</th>
                                    <th>Bahan Baku</th>
                                    <th>Qty</th>
                                    <th>Satuan</th>
                                    <th>Harga</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
        `;

        details.forEach((detail, index) => {
            const qty = parseFloat(detail.qty || detail.QTY || detail.pemb_qty || 0);
            const harga = parseFloat(detail.harga_beli || detail.HARGA_BELI || detail.pemb_harga || 0);
            const total = qty * harga;
            const namaBahan = detail.bahan_nama || detail.BAHAN_NAMA || detail.varian || detail.VARIAN || 'N/A';
            const satuan = detail.satuan_nama || detail.SATUAN_NAMA || '-';

            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${namaBahan}</td>
                    <td>${qty}</td>
                    <td>${satuan}</td>
                    <td>Rp ${formatNumber(harga)}</td>
                    <td><strong>Rp ${formatNumber(total)}</strong></td>
                </tr>
            `;
        });

        html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;

        return html;
    }

    function previousMonth() {
        currentMonth--;
        if (currentMonth < 1) {
            currentMonth = 12;
            currentYear--;
        }
        updateCalendar();
    }

    function nextMonth() {
        currentMonth++;
        if (currentMonth > 12) {
            currentMonth = 1;
            currentYear++;
        }
        updateCalendar();
    }

    function updateCalendar() {
        // Update month display
        document.getElementById('currentMonth').textContent = 
            new Date(currentYear, currentMonth - 1).toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
        
        // Reload calendar data
        window.location.href = `<?= base_url('warehouse/laporan_pembelian') ?>?year=${currentYear}&month=${currentMonth}`;
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }

    function formatDate(dateStr) {
        return new Date(dateStr).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
    }

    function showError(message) {
        document.getElementById('purchaseDetails').innerHTML = `
            <div class="text-center text-danger py-4">
                <i class="bi bi-exclamation-triangle fs-1"></i>
                <p class="mt-2">${message}</p>
            </div>
        `;
    }

    function exportToExcel() {
        // Implementation for Excel export
        alert('Fitur export Excel akan segera tersedia');
    }
    </script>
</body>
</html>
