<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line me-2"></i>Laporan Keuangan
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-info me-2" onclick="loadLaporanSemuaCabang()">
                            <i class="fas fa-building me-1"></i>Semua Cabang
                        </button>
                        <button type="button" class="btn btn-sm btn-success me-2" onclick="printLaporan()">
                            <i class="fas fa-print me-1"></i>Print Laporan
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" onclick="exportLaporan()">
                            <i class="fas fa-download me-1"></i>Export Excel
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="perusahaan_filter">Pilih Cabang:</label>
                                <select class="form-control" id="perusahaan_filter" onchange="changePerusahaan()">
                                    <?php foreach ($all_perusahaan as $perusahaan): ?>
                                        <option value="<?= $perusahaan->id_perusahaan ?>"
                                            <?= ($perusahaan->id_perusahaan == $current_perusahaan_id) ? 'selected' : '' ?>>
                                            <?= $perusahaan->nama_perusahaan ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Calendar Section -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-calendar-alt me-2"></i>Kalender Keuangan -
                                        <?= date('F Y', strtotime($bulanini . '-01')) ?>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div id="calendar"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-chart-pie me-2"></i>Ringkasan Hari Ini
                                    </h5>
                                </div>
                                <div class="card-body" id="summary-today">
                                    <div class="text-center">
                                        <h4 class="text-muted">Pilih tanggal di kalender</h4>
                                        <p class="text-muted">untuk melihat laporan keuangan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Section -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-list me-2"></i>Detail Transaksi - <span
                                            id="selected-date"><?= date('d/m/Y', strtotime($selected_date)) ?></span>
                                    </h5>
                                </div>
                                <div class="card-body" id="detail-section">
                                    <div class="text-center">
                                        <h5 class="text-muted">Pilih tanggal di kalender untuk melihat detail transaksi
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Laporan Semua Cabang Section -->
                    <div class="row mt-4" id="semua-cabang-section" style="display: none;">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-building me-2"></i>Laporan Keuangan Semua Cabang
                                    </h5>
                                </div>
                                <div class="card-body" id="semua-cabang-content">
                                    <div class="text-center">
                                        <i class="fas fa-spinner fa-spin"></i> Memuat data...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

<script>
    let selectedDate = '<?= $selected_date ?>';
    let selectedPerusahaanId = '<?= $current_perusahaan_id ?>';
    let calendar;

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize calendar
        const calendarEl = document.getElementById('calendar');

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            initialDate: '<?= $bulanini ?>-01',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth'
            },
            locale: 'id',
            height: 'auto',
            dayMaxEvents: true,
            events: function (info, successCallback, failureCallback) {
                // Load events for the month
                loadCalendarEvents(info.startStr, info.endStr, successCallback);
            },
            dateClick: function (info) {
                selectedDate = info.dateStr;
                loadLaporanByDate(selectedDate);
                updateSelectedDateDisplay(selectedDate);
                // Hide semua cabang section
                document.getElementById('semua-cabang-section').style.display = 'none';
            },
            eventClick: function (info) {
                selectedDate = info.event.startStr.split('T')[0];
                loadLaporanByDate(selectedDate);
                updateSelectedDateDisplay(selectedDate);
                // Hide semua cabang section
                document.getElementById('semua-cabang-section').style.display = 'none';
            }
        });

        calendar.render();

        // Load initial data for today
        loadLaporanByDate(selectedDate);

        // Calendar events will be loaded automatically by FullCalendar
    });

    function loadLaporanSemuaCabang() {
        // Show section
        document.getElementById('semua-cabang-section').style.display = 'block';
        document.getElementById('semua-cabang-content').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data...</div>';

        fetch('<?= base_url('laporan/get_laporan_semua_cabang') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'date=' + selectedDate
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Response text:', text);
                        throw new Error('Invalid JSON response');
                    }
                });
            })
            .then(data => {
                if (data.success) {
                    updateSemuaCabangSection(data.data);
                } else {
                    showError('Gagal memuat data: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error loading laporan semua cabang:', error);
                showError('Terjadi kesalahan saat memuat data: ' + error.message);

                document.getElementById('semua-cabang-content').innerHTML = `
            <div class="text-center">
                <i class="fas fa-exclamation-triangle text-warning"></i>
                <h5 class="text-muted">Gagal memuat data</h5>
                <p class="text-muted">Silakan coba lagi</p>
            </div>
        `;
            });
    }

    function updateSemuaCabangSection(data) {
        let html = `
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Cabang</th>
                        <th>Total Pemasukan</th>
                        <th>Total Pengeluaran</th>
                        <th>Laba Bersih</th>
                        <th>Jumlah Transaksi</th>
                        <th>Jumlah Pengeluaran</th>
                    </tr>
                </thead>
                <tbody>
    `;

        let totalPemasukan = 0;
        let totalPengeluaran = 0;
        let totalLaba = 0;
        let totalTransaksi = 0;
        let totalItemPengeluaran = 0;

        data.forEach((item, index) => {
            totalPemasukan += item.total_pemasukan;
            totalPengeluaran += item.total_pengeluaran;
            totalLaba += item.laba_bersih;
            totalTransaksi += item.jumlah_transaksi;
            totalItemPengeluaran += item.jumlah_pengeluaran;

            html += `
            <tr>
                <td>${index + 1}</td>
                <td><strong>${item.nama_perusahaan}</strong></td>
                <td class="text-end text-success">Rp ${formatNumber(item.total_pemasukan)}</td>
                <td class="text-end text-danger">Rp ${formatNumber(item.total_pengeluaran)}</td>
                <td class="text-end ${item.laba_bersih >= 0 ? 'text-success' : 'text-danger'}">Rp ${formatNumber(item.laba_bersih)}</td>
                <td class="text-center">${item.jumlah_transaksi}</td>
                <td class="text-center">${item.jumlah_pengeluaran}</td>
            </tr>
        `;
        });

        html += `
                </tbody>
                <tfoot class="table-dark">
                    <tr>
                        <th colspan="2">TOTAL</th>
                        <th class="text-end text-success">Rp ${formatNumber(totalPemasukan)}</th>
                        <th class="text-end text-danger">Rp ${formatNumber(totalPengeluaran)}</th>
                        <th class="text-end ${totalLaba >= 0 ? 'text-success' : 'text-danger'}">Rp ${formatNumber(totalLaba)}</th>
                        <th class="text-center">${totalTransaksi}</th>
                        <th class="text-center">${totalItemPengeluaran}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;

        document.getElementById('semua-cabang-content').innerHTML = html;
    }

    function loadCalendarEvents(start, end, callback) {
        try {
            // Handle case when start is undefined (called from changePerusahaan)
            let yearMonth;
            if (!start) {
                // Get current month from calendar
                const currentDate = calendar.getDate();
                yearMonth = currentDate.toISOString().substring(0, 7);
            } else {
                yearMonth = start.substring(0, 7);
            }

            fetch('<?= base_url('laporan/get_daily_totals_by_month') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'year_month=' + yearMonth + '&perusahaan_id=' + selectedPerusahaanId
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('Response text:', text);
                            throw new Error('Invalid JSON response');
                        }
                    });
                })
                .then(data => {
                    if (data.success && data.data) {
                        const events = data.data.map(item => ({
                            title: 'Rp ' + formatNumber(item.total_harian || 0) + ' (' + (item.jumlah_transaksi || 0) + ' transaksi)',
                            date: item.tanggal,
                            backgroundColor: (item.total_harian || 0) > 0 ? '#28a745' : '#6c757d',
                            borderColor: (item.total_harian || 0) > 0 ? '#28a745' : '#6c757d',
                            textColor: '#ffffff',
                            extendedProps: {
                                total: item.total_harian || 0,
                                count: item.jumlah_transaksi || 0
                            }
                        }));
                        callback(events);
                    } else {
                        console.warn('No data or error in response:', data);
                        callback([]);
                    }
                })
                .catch(error => {
                    console.error('Error loading calendar events:', error);
                    callback([]);
                });
        } catch (error) {
            console.error('Error in loadCalendarEvents:', error);
            callback([]);
        }
    }

    function changePerusahaan() {
        selectedPerusahaanId = document.getElementById('perusahaan_filter').value;
        if (selectedDate) {
            loadLaporanByDate(selectedDate);
            // Refresh calendar events
            calendar.refetchEvents();
        }
        // Hide semua cabang section
        document.getElementById('semua-cabang-section').style.display = 'none';
    }

    function loadLaporanByDate(date) {
        // Show loading state
        document.getElementById('summary-today').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data...</div>';
        document.getElementById('detail-section').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data...</div>';

        fetch('<?= base_url('laporan/get_laporan_by_date') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'date=' + date + '&perusahaan_id=' + selectedPerusahaanId
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Response text:', text);
                        throw new Error('Invalid JSON response');
                    }
                });
            })
            .then(data => {
                if (data.success) {
                    updateSummaryToday(data.data);
                    updateDetailSection(data.data);
                } else {
                    showError('Gagal memuat data: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error loading laporan:', error);
                showError('Terjadi kesalahan saat memuat data: ' + error.message);

                // Show empty state
                document.getElementById('summary-today').innerHTML = `
            <div class="text-center">
                <i class="fas fa-exclamation-triangle text-warning"></i>
                <h5 class="text-muted">Gagal memuat data</h5>
                <p class="text-muted">Silakan coba lagi</p>
            </div>
        `;
                document.getElementById('detail-section').innerHTML = `
            <div class="text-center">
                <i class="fas fa-exclamation-triangle text-warning"></i>
                <h5 class="text-muted">Gagal memuat data</h5>
                <p class="text-muted">Silakan coba lagi</p>
            </div>
        `;
            });
    }

    function updateSummaryToday(data) {
        const totalPemasukan = data.total_pemasukan || 0;
        const totalPengeluaran = data.total_pengeluaran || 0;
        const labaBersih = data.laba_bersih || 0;
        const metodePembayaran = data.metode_pembayaran || {};

        const summaryHtml = `
        <div class="row text-center">
            <div class="col-6 mb-3">
                <div class="bg-success text-white p-3 rounded">
                    <h4 class="mb-1">Rp ${formatNumber(totalPemasukan)}</h4>
                    <small>Total Pemasukan</small>
                </div>
            </div>
            <div class="col-6 mb-3">
                <div class="bg-danger text-white p-3 rounded">
                    <h4 class="mb-1">Rp ${formatNumber(totalPengeluaran)}</h4>
                    <small>Total Pengeluaran</small>
                </div>
            </div>
            <div class="col-12">
                <div class="bg-primary text-white p-3 rounded">
                    <h4 class="mb-1">Rp ${formatNumber(labaBersih)}</h4>
                    <small>Laba Bersih</small>
                </div>
            </div>
        </div>
        
        <div class="mt-3">
            <h6>Metode Pembayaran:</h6>
            ${Object.keys(metodePembayaran).length > 0 ?
                Object.entries(metodePembayaran).map(([metode, total]) =>
                    `<div class="d-flex justify-content-between mb-1">
                        <span>${metode}</span>
                        <span class="fw-bold">Rp ${formatNumber(total)}</span>
                    </div>`
                ).join('') :
                '<div class="text-muted">Tidak ada transaksi</div>'
            }
        </div>
    `;

        document.getElementById('summary-today').innerHTML = summaryHtml;
    }

    function updateDetailSection(data) {
        const detailPembayaran = data.detail_pembayaran || [];
        const detailPengeluaran = data.detail_pengeluaran || [];

        const detailHtml = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-success">
                    <i class="fas fa-arrow-up me-1"></i>Pemasukan (${detailPembayaran.length} transaksi)
                </h6>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>No SPK</th>
                                <th>Konsumen</th>
                                <th>Metode</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${detailPembayaran.length > 0 ?
                detailPembayaran.map(item => `
                                    <tr>
                                        <td>${item.nospk || '-'}</td>
                                        <td>${item.konsumen_nama || '-'}</td>
                                        <td><span class="badge bg-info">${item.metode || 'Tidak Diketahui'}</span></td>
                                        <td class="text-end">Rp ${formatNumber(item.grand_total || 0)}</td>
                                    </tr>
                                `).join('') :
                '<tr><td colspan="4" class="text-center text-muted">Tidak ada transaksi</td></tr>'
            }
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <h6 class="text-danger">
                    <i class="fas fa-arrow-down me-1"></i>Pengeluaran (${detailPengeluaran.length} item)
                </h6>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Detail Pengeluaran</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${detailPengeluaran.length > 0 ?
                detailPengeluaran.map(item => `
                                    <tr>
                                        <td>
                                            <div><strong>${item.kategori_pengeluaran || 'Umum'}</strong></div>
                                            <small class="text-muted">${item.pengeluaran_detail || '-'}</small>
                                            <div><small class="text-muted">Penerima: ${item.pengeluaran_penerima || '-'}</small></div>
                                        </td>
                                        <td class="text-end">Rp ${formatNumber(item.pengeluaran_jumlah || 0)}</td>
                                    </tr>
                                `).join('') :
                '<tr><td colspan="2" class="text-center text-muted">Tidak ada pengeluaran</td></tr>'
            }
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;

        document.getElementById('detail-section').innerHTML = detailHtml;
    }

    function updateSelectedDateDisplay(date) {
        const formattedDate = new Date(date).toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        document.getElementById('selected-date').textContent = formattedDate;
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }

    function showError(message) {
        // Show error using SweetAlert or alert
        alert(message);
    }

    function printLaporan() {
        if (!selectedDate) {
            alert('Pilih tanggal terlebih dahulu');
            return;
        }

        // Redirect to print URL
        window.open('<?= base_url('laporan/print_laporan_harian') ?>?date=' + selectedDate + '&perusahaan_id=' + selectedPerusahaanId, '_blank');
    }

    function exportLaporan() {
        if (!selectedDate) {
            alert('Pilih tanggal terlebih dahulu');
            return;
        }

        // Redirect to export URL
        window.open('<?= base_url('laporan/export_laporan_keuangan') ?>?date=' + selectedDate + '&perusahaan_id=' + selectedPerusahaanId, '_blank');
    }
</script>

<link rel="stylesheet" href="<?= base_url('assets/css/tgm-laporan-keuangan.css') ?>">