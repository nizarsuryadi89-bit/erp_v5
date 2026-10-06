<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pengiriman - TGM ERP</title>
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
                            <p>Pengiriman Hari Ini</p>
                            <small class="text-muted d-block pb-2" id="tanggalTransaksi"><?= date('d F Y', strtotime($selected_date)) ?></small>
                            <small class="text-primary fw-bold border-top pt-2 d-block">
                                <i class="bi bi-calendar-month me-1"></i> Bulan Ini: <?= $total_transaksi_bulan ?> Pengiriman
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stats-card success h-100 fade-in-up">
                        <div class="card-body">
                            <div class="icon-wrapper">
                                <i class="bi bi-box-arrow-up"></i>
                            </div>
                            <h3 id="totalNilai"><?= number_format($total_qty) ?></h3>
                            <p>Total Item Dikirim</p>
                            <small class="text-muted d-block pb-2" id="tanggalNilai">Hari ini</small>
                            <small class="text-success fw-bold border-top pt-2 d-block">
                                <i class="bi bi-wallet2 me-1"></i> Bulan Ini: Item <?= number_format($total_qty_bulan) ?>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stats-card warning h-100 fade-in-up">
                        <div class="card-body">
                            <div class="icon-wrapper">
                                <i class="bi bi-people"></i>
                            </div>
                            <h3><?= is_array($karyawan_list) || is_object($karyawan_list) ? count($karyawan_list) : 0 ?></h3>
                            <p>Total Karyawan Penerima</p>
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
                            <h3><?= is_array($bahan_list) || is_object($bahan_list) ? count($bahan_list) : 0 ?></h3>
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
                                    <i class="bi bi-calendar3 me-2"></i>Kalender Pengiriman
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
                                <i class="bi bi-list-ul me-2"></i>Detail Pengiriman
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="purchaseDetails">
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-calendar-x fs-1"></i>
                                    <p class="mt-2">Pilih tanggal pada kalender untuk melihat detail pengiriman</p>
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
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center flex-wrap">
                            <h5 class="mb-0">
                                <i class="bi bi-table me-2"></i>Data Pengiriman - <span id="selectedDateDisplay"><?= date('d F Y', strtotime($selected_date)) ?></span>
                            </h5>
                            <div class="d-flex gap-2 align-items-center mt-2 mt-md-0">
                                <input type="text" id="searchPengiriman" class="form-control form-control-sm" placeholder="Cari data..." onkeyup="searchTable()" style="width: 200px;">
                                <a href="javascript:void(0)" onclick="cetakLaporanHarian()" class="btn btn-primary btn-sm text-nowrap">
                                    <i class="bi bi-printer me-1"></i>Cetak Laporan
                                </a>
                            </div>
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
                        <i class="bi bi-receipt me-2"></i>Detail Pengiriman
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
                    info.el.title = `${eventData.jumlah_transaksi} PENGIRIMAN - Item ${formatNumber(eventData.total_qty)}`;
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
                title: `${data.jumlah_transaksi} PENGIRIMAN`,
                date: date,
                backgroundColor: data.total_qty > 50 ? '#dc3545' : (data.total_qty > 20 ? '#fd7e14' : '#28a745'),
                borderColor: data.total_qty > 50 ? '#dc3545' : (data.total_qty > 20 ? '#fd7e14' : '#28a745'),
                textColor: '#ffffff',
                extendedProps: {
                    jumlah_transaksi: data.jumlah_transaksi,
                    total_qty: data.total_qty
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
                <p class="mt-2">Memuat data pengiriman...</p>
            </div>
        `;

        // AJAX request
        fetch('<?= base_url('warehouse/get_pengiriman_by_date') ?>', {
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
        
        if (data.pengiriman_data.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="bi bi-calendar-x fs-1"></i>
                    <p class="mt-2">Tidak ada data pengiriman pada tanggal ini</p>
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
                            <small class="text-muted">Pengiriman</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <h6 class="mb-0 text-success">${formatNumber(data.total_qty)} Item</h6>
                            <small class="text-muted">Total Item</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="purchase-summary">
        `;

        // Group by destination
        const destinationGroups = {};
        data.pengiriman_data.forEach(pengiriman => {
            if (!destinationGroups[pengiriman.gudang_nama]) {
                destinationGroups[pengiriman.gudang_nama] = [];
            }
            destinationGroups[pengiriman.gudang_nama].push(pengiriman);
        });

        for (const [tujuan, pengirimans] of Object.entries(destinationGroups)) {
            let totalTujuan = 0;
            pengirimans.forEach(p => {
                const details = data.pengiriman_detail.filter(d => d.pengambilan_id == p.pengambilan_id);
                details.forEach(d => totalTujuan += parseFloat(d.qty || 0));
            });
            
            html += `
                <div class="tujuan-item mb-2 p-2 border rounded">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong class="text-primary">${tujuan || 'N/A'}</strong>
                            <br><small class="text-muted">${pengirimans.length} pengiriman</small>
                        </div>
                        <div class="text-end">
                            <strong class="text-success">${formatNumber(totalTujuan)} Item</strong>
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
        
        if (data.pengiriman_data.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-1"></i>
                    <p class="mt-2">Tidak ada data pengiriman untuk ditampilkan</p>
                </div>
            `;
            return;
        }

        let html = `
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">NO</th>
                            <th width="15%">ID PENGIRIMAN</th>
                            <th width="15%">TOKO TUJUAN</th>
                            <th width="15%">PENERIMA</th>
                            <th width="25%">RINGKASAN ITEM</th>
                            <th width="15%">TANGGAL</th>
                            <th width="10%">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        data.pengiriman_data.forEach((pengiriman, index) => {
            let itemSummary = [];
            let details = data.pengiriman_detail.filter(d => d.pengambilan_id == pengiriman.pengambilan_id);
            details.forEach(d => {
                let itemName = d.bahan_nama;
                if (d.varian && d.varian.trim() !== '') {
                    itemName += ' - ' + d.varian;
                }
                itemSummary.push(itemName + ' (' + parseFloat(d.qty) + ' ' + (d.satuan_nama || '') + ')');
            });
            let ringkasan = itemSummary.length > 0 ? itemSummary.join(', ') : '-';
            
            // Limit summary length for table view
            let displayRingkasan = ringkasan;
            if (displayRingkasan.length > 60) {
                displayRingkasan = displayRingkasan.substring(0, 60) + '...';
            }
            
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td><strong>${pengiriman.pengambilan_id || 'N/A'}</strong></td>
                    <td>${pengiriman.gudang_nama || '-'}</td>
                    <td>${pengiriman.karyawan_nama || '-'}</td>
                    <td><small title="${ringkasan}">${displayRingkasan}</small></td>
                    <td>${formatDate(pengiriman.pengambilan_tgl)}</td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="showPurchaseDetailModal('${selectedDate}', ${pengiriman.pengambilan_id})" title="Lihat Detail">
                            <i class="bi bi-eye"></i>
                        </button>
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
        document.getElementById('totalNilai').textContent = `Item ${formatNumber(data.total_qty)}`;
        
        // Also update the daily date label
        if(document.getElementById('tanggalTransaksi')) {
            document.getElementById('tanggalTransaksi').textContent = formatDate(selectedDate);
        }
        if(document.getElementById('tanggalNilai')) {
            document.getElementById('tanggalNilai').textContent = formatDate(selectedDate);
        }
    }

    function updateSelectedDateDisplay(date) {
        document.getElementById('selectedDateDisplay').textContent = formatDate(date);
    }

    function showPurchaseDetailModal(date, pengirimanId = null) {
        const modal = new bootstrap.Modal(document.getElementById('purchaseDetailModal'));
        const modalBody = document.getElementById('modalPurchaseDetail');
        
        modalBody.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Memuat detail pengiriman...</p>
            </div>
        `;
        
        modal.show();

        // Load purchase detail data
        fetch('<?= base_url('warehouse/get_pengiriman_by_date') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `date=${date}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showPurchaseDetailContent(data.data, pengirimanId);
            } else {
                modalBody.innerHTML = `
                    <div class="text-center text-danger py-4">
                        <i class="bi bi-exclamation-triangle fs-1"></i>
                        <p class="mt-2">Gagal memuat detail pengiriman</p>
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

    function showPurchaseDetailContent(data, pengirimanId = null) {
        const modalBody = document.getElementById('modalPurchaseDetail');
        
        let html = `
            <div class="mb-3">
                <h6 class="text-primary">
                    <i class="bi bi-calendar-check me-1"></i>
                    Detail Pengiriman - ${data.formatted_date}
                </h6>
            </div>
        `;

        if (pengirimanId) {
            // Show specific purchase detail
            const pengiriman = data.pengiriman_data.find(p => p.pengambilan_id == pengirimanId);
            if (pengiriman) {
                const details = data.pengiriman_detail.filter(d => d.pengambilan_id == pengirimanId);
                html += generatePurchaseDetailHTML(pengiriman, details);
            }
        } else {
            // Show all purchases for the date
            data.pengiriman_data.forEach(pengiriman => {
                const details = data.pengiriman_detail.filter(d => d.pengambilan_id == pengiriman.pengambilan_id);
                html += generatePurchaseDetailHTML(pengiriman, details);
            });
        }

        modalBody.innerHTML = html;
    }

    function generatePurchaseDetailHTML(pengiriman, details) {
        
        let totalQty = 0;
        details.forEach(d => totalQty += parseFloat(d.qty));

        let html = `
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>ID Pengiriman: ${pengiriman.pengambilan_id}</strong>
                        </div>
                        <div class="col-md-6 text-end">
                            <strong class="text-success">Total: ${formatNumber(totalQty)} Item</strong>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <small class="text-muted">Tujuan Gudang: ${pengiriman.gudang_nama}</small><br>
                            <small class="text-muted">Officer: ${pengiriman.karyawan_nama || 'N/A'}</small>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">Tanggal: ${formatDate(pengiriman.pengambilan_tgl)}</small>
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
                                </tr>
                            </thead>
                            <tbody>
        `;

        details.forEach((detail, index) => {
            const qty = parseFloat(detail.qty || 0);
            const namaBahan = (detail.bahan_nama || 'N/A') + (detail.varian ? ' - ' + detail.varian : '');
            const satuan = detail.satuan_nama || detail.satuan || '-';

            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${namaBahan}</td>
                    <td>${qty}</td>
                    <td>${satuan}</td>
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
        window.location.href = `<?= base_url('warehouse/laporan_pengiriman') ?>?year=${currentYear}&month=${currentMonth}`;
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

    function searchTable() {
        let input = document.getElementById("searchPengiriman");
        let filter = input.value.toLowerCase();
        let table = document.querySelector("#purchaseTable table");
        if (!table) return;
        let tr = table.getElementsByTagName("tr");
        
        for (let i = 1; i < tr.length; i++) {
            let textContent = tr[i].textContent || tr[i].innerText;
            if (textContent.toLowerCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }

    function cetakLaporanHarian() {
        window.open('<?= base_url('warehouse/cetak_laporan_pengiriman_harian?date=') ?>' + selectedDate, '_blank');
    }
    </script>
</body>
</html>
