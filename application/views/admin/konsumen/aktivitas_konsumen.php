<link rel="stylesheet" href="<?= base_url('assets/css/tgm-aktivitas-konsumen.css') ?>">
<div class="app-content mt-2">
    <div class="container-fluid">
        <!-- HEADER SECTION -->
        <?php $this->load->view('layout_admin/header_modul'); ?>

        <!-- NAVIGATION SHORTCUTS -->
        <div class="d-flex flex-wrap gap-2 mb-4 animate-up">
            <a href="<?= base_url('pengaturan/konsumen_dashboard') ?>" class="btn btn-outline-primary border-2 fw-bold btn-sm rounded-pill flex-grow-1 px-3 py-2">
                <i class="fas fa-tachometer-alt me-1"></i> DASHBOARD
            </a>
            <a href="<?= base_url('pengaturan/konsumen_list') ?>" class="btn btn-outline-success border-2 fw-bold btn-sm rounded-pill flex-grow-1 px-3 py-2">
                <i class="fas fa-database me-1"></i> DATABASE
            </a>
            <button type="button" class="btn btn-info fw-bold btn-sm rounded-pill flex-grow-1 text-white shadow-sm px-3 py-2">
                <i class="fas fa-chart-line me-1"></i> CRM ANALYTICS
            </button>
        </div>

    </div>

    <!-- STATISTICS CARDS -->
    <div class="row g-3 mb-4 animate-up" style="animation-delay: 0.1s">
        <div class="col-md-3">
            <div class="p-4 bg-white rounded-4 shadow-sm border-0 h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="rounded-3 p-2 bg-primary-subtle text-primary"><i class="fas fa-users-cog fa-lg"></i>
                    </div>
                    <div class="badge bg-primary-subtle text-primary rounded-pill">Total Active</div>
                </div>
                <h3 class="fw-900 mb-1"><?= $total_rows ?></h3>
                <div class="text-muted small fw-bold text-uppercase">Konsumen Bertransaksi</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-4 bg-white rounded-4 shadow-sm border-0 h-100 border-bottom border-success border-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="rounded-3 p-2 bg-success-subtle text-success"><i class="fas fa-shopping-bag fa-lg"></i>
                    </div>
                </div>
                <h3 class="fw-900 mb-1" id="total-transactions">-</h3>
                <div class="text-muted small fw-bold text-uppercase">Total Transaksi</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-4 bg-white rounded-4 shadow-sm border-0 h-100 border-bottom border-warning border-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="rounded-3 p-2 bg-warning-subtle text-warning"><i class="fas fa-wallet fa-lg"></i></div>
                </div>
                <h3 class="fw-900 mb-1 text-success" id="total-revenue">-</h3>
                <div class="text-muted small fw-bold text-uppercase">Total Revenue</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-4 bg-white rounded-4 shadow-sm border-0 h-100 border-bottom border-info border-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="rounded-3 p-2 bg-info-subtle text-info"><i class="fas fa-award fa-lg"></i></div>
                </div>
                <h3 class="fw-900 mb-1 text-info" id="total-points">-</h3>
                <div class="text-muted small fw-bold text-uppercase">Total Loyalty Point</div>
            </div>
        </div>
    </div>

    <!-- Period Selector -->
    <div class="period-selector">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0" style="color: #2c3e50;">Filter Periode:</h5>
            </div>
            <div class="col-md-6 text-end">
                <div class="btn-group" role="group">
                    <button type="button" class="btn active" onclick="filterPeriod('month')">Bulan Ini</button>
                    <button type="button" class="btn" onclick="filterPeriod('quarter')">3 Bulan</button>
                    <button type="button" class="btn" onclick="filterPeriod('year')">Tahun Ini</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Customers Section -->
    <div class="report-section-card p-4 mb-4">
        <h5 class="fw-900 mb-4 text-dark border-bottom pb-2">
            <i class="fas fa-trophy me-2 text-warning"></i>
            TOP 5 KONSUMEN BULAN INI
        </h5>
        <div class="row" id="top-customers-container">
            <?php
            $top_count = 0;
            foreach (array_slice($tampil_data, 0, 5) as $dt):
                $top_count++;
                $rank_class = $top_count == 1 ? 'gold' : ($top_count == 2 ? 'silver' : ($top_count == 3 ? 'bronze' : ''));
                ?>
                <div class="col-lg-6 col-md-12 mb-3">
                    <div class="top-customer-card border shadow-sm">
                        <div class="customer-rank <?= $rank_class ?> shadow-sm"><?= $top_count ?></div>
                        <div class="customer-info">
                            <div class="customer-name fs-6 fw-bold"><?= $dt->konsumen_nama ?></div>
                            <div class="customer-details">
                                <i class="fas fa-phone-alt me-1 text-success"></i><?= $dt->konsumen_nohp ?>
                            </div>
                        </div>
                        <div class="customer-stats">
                            <div class="transaction-count text-primary fw-900"><?= $dt->jumlah_transaksi ?> <small
                                    class="text-muted fw-normal">Trx</small></div>
                            <div class="transaction-amount text-success fw-bold">Rp
                                <?= number_format($dt->total_transaksi) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Activity Table -->
    <div class="report-section-card p-0 mb-4 overflow-hidden">
        <div class="section-header p-3 border-bottom bg-light">
            <h6 class="mb-0 fw-bold"><i class="fas fa-chart-bar me-2"></i>Rincian Performa Konsumen</h6>
        </div>
        <div class="table-responsive">
            <table class="table premium-table table-hover mb-0" id="datatable-details">
                <thead>
                    <tr>
                        <th style="width: 60px;">Rank</th>
                        <th style="width: 200px;">Konsumen</th>
                        <th style="width: 150px;">Kontak</th>
                        <th style="width: 120px;">Transaksi</th>
                        <th style="width: 150px;">Total Nilai</th>
                        <th style="width: 100px;">Point</th>
                        <th style="width: 120px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rank = 1;
                    foreach ($tampil_data as $dt):
                        $point = floor($dt->total_transaksi / 250000);
                        $initials = substr($dt->konsumen_nama, 0, 2);
                        $status_class = $point >= 10 ? 'success' : ($point >= 5 ? 'warning' : 'secondary');
                        $status_text = $point >= 10 ? 'VIP' : ($point >= 5 ? 'Regular' : 'Basic');
                        ?>
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-dark"><?= $rank ?></span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="customer-avatar"><?= $initials ?></div>
                                    <div>
                                        <div class="fw-bold" style="color: #2c3e50;"><?= $dt->konsumen_nama ?></div>
                                        <small class="text-muted">ID: <?= $dt->konsumen_id ?? 'N/A' ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <i class="bi bi-telephone me-2" style="color: #7f8c8d;"></i>
                                    <span><?= $dt->konsumen_nohp ?></span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary"><?= $dt->jumlah_transaksi ?></span>
                            </td>
                            <td class="text-end fw-bold" style="color: #27ae60;">
                                Rp <?= number_format($dt->total_transaksi) ?>
                            </td>
                            <td class="text-center">
                                <span class="point-badge"><?= $point ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-<?= $status_class ?>"><?= $status_text ?></span>
                            </td>
                        </tr>
                        <?php
                        $rank++;
                    endforeach;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Konsumen -->
<div class="modal fade" id="modalKonsumen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-dark text-white p-4 border-0">
                <h5 class="modal-title fw-900 text-uppercase"><i class="fas fa-user-plus me-2"></i> Tambah Konsumen Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="formKonsumen" method="POST" action="<?= base_url('pengaturan/konsumen_add') ?>">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Nama Lengkap <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control fw-bold" name="konsumen_nama" required
                                placeholder="Nama Konsumen"
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;">
                        </div>
                        <div class="col-12">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Alamat Lengkap <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control fw-bold" name="konsumen_alamat" rows="2" required
                                placeholder="Alamat Domisili"
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small text-uppercase mb-2">WhatsApp / HP <span
                                    class="text-danger">*</span></label>
                            <input type="tel" class="form-control fw-bold" name="konsumen_nohp" placeholder="08..."
                                required maxlength="15" minlength="10"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Email</label>
                            <input type="email" class="form-control fw-bold" name="konsumen_email"
                                placeholder="email@contoh.com"
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                        data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow"
                        style="background: var(--primary-gradient)">SIMPAN DATA</button>
                </div>
            </form>
        </div>
    </div>
</div>

</div>

<script>
    $(document).ready(function () {
        // Calculate and display statistics
        function updateStats() {
            const tableData = <?= json_encode($tampil_data) ?>;
            let totalTransactions = 0;
            let totalRevenue = 0;
            let totalPoints = 0;

            tableData.forEach(customer => {
                totalTransactions += parseInt(customer.jumlah_transaksi);
                totalRevenue += parseInt(customer.total_transaksi);
                totalPoints += Math.floor(customer.total_transaksi / 250000);
            });

            $('#total-transactions').text(totalTransactions.toLocaleString());
            $('#total-revenue').text('Rp ' + totalRevenue.toLocaleString());
            $('#total-points').text(totalPoints.toLocaleString());
        }

        // Initialize stats
        updateStats();

        // Period filter functionality
        window.filterPeriod = function (period) {
            // Remove active class from all buttons
            $('.period-selector .btn').removeClass('active');
            // Add active class to clicked button
            event.target.classList.add('active');

            // Here you would typically make an AJAX call to get filtered data
            // For now, we'll just show a message
            console.log('Filtering for period:', period);

        // You can implement AJAX call here to get data for different periods
        // Example:
        /*
        $.ajax({
            url: '<?= base_url("konsumen/get_activity_data") ?>',
            method: 'POST',
                data: { period: period },
            success: function(response) {
                // Update table and stats with new data
                updateTable(response.data);
                updateStats(response.data);
            }
        });
        */
    };
});



    $(document).ready(function () {

        // Initialize DataTable
        if ($.fn.DataTable) {
            $('#datatable-details').DataTable({
                responsive: true,
                order: [[4, 'desc']], // Sort by total value descending
                pageLength: 25,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Tidak ada data yang ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                }
            });
        }
    });
</script>