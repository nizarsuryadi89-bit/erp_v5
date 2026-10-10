<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    :root {
        --cashier-primary: #4361ee;
        --cashier-secondary: #3f37c9;
        --cashier-accent: #4895ef;
        --cashier-gradient: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%);
        --success-premium: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
        --warning-premium: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);
        --danger-premium: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    }

    .stat-card-kasir {
        border: none;
        border-radius: 24px;
        color: white;
        overflow: hidden;
        position: relative;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        height: 100%;
        background-color: #fff;
    }

    .stat-card-kasir:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    .stat-card-kasir .card-body {
        padding: 1.8rem;
        z-index: 2;
        position: relative;
    }

    .stat-card-kasir .icon-bg {
        position: absolute;
        right: -20px;
        bottom: -20px;
        font-size: 6rem;
        opacity: 0.12;
        transform: rotate(-15deg);
        transition: all 0.4s ease;
    }

    .stat-card-kasir:hover .icon-bg {
        transform: rotate(0deg) scale(1.1);
        opacity: 0.2;
    }

    .bg-gradient-kasir {
        background: var(--cashier-gradient);
    }

    .bg-gradient-success {
        background: var(--success-premium);
    }

    .bg-gradient-warning {
        background: var(--warning-premium);
    }

    .bg-gradient-danger {
        background: var(--danger-premium);
    }

    .action-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .task-card {
        background: white;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 20px;
        padding: 2rem 1rem;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none !important;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        position: relative;
        overflow: hidden;
    }

    .task-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: var(--cashier-primary);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .task-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(67, 97, 238, 0.15);
        border-color: rgba(67, 97, 238, 0.2);
    }

    .task-card:hover::after {
        transform: scaleX(1);
    }

    .task-card i {
        font-size: 3rem;
        margin-bottom: 5px;
        background: var(--cashier-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        transition: transform 0.3s ease;
    }

    .task-card:hover i {
        transform: scale(1.1);
    }

    .task-card .task-label {
        font-weight: 800;
        font-size: 0.9rem;
        color: #2b2d42;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .task-card .task-desc {
        font-size: 0.75rem;
        color: #8d99ae;
        font-weight: 500;
    }

    .bg-soft-primary {
        background: rgba(67, 97, 238, 0.1);
    }

    .text-primary {
        color: #4361ee !important;
    }

    .pulse-animation {
        animation: pulse-red 2s infinite;
    }

    @keyframes pulse-red {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(231, 76, 60, 0.7);
        }

        70% {
            transform: scale(1);
            box-shadow: 0 0 0 10px rgba(231, 76, 60, 0);
        }

        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(231, 76, 60, 0);
        }
    }
</style>

<div class="app-content mt-n4 pb-5">
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row align-items-center mb-5 pt-3">
            <div class="col-md-7">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-gradient-kasir rounded-circle shadow-sm">
                        <i class="fas fa-desktop text-white fs-4"></i>
                    </div>
                    <div>
                        <h4 class="performance-title mb-0">WORKSPACE KASIR</h4>
                        <p class="text-muted fw-bold mb-0 small text-uppercase letter-spacing-1">
                            <?= $this->session->userdata('nama_perusahaan') ?> • Unit:
                            <?= $this->session->userdata('username') ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-5 text-md-end">
                <div class="d-inline-flex align-items-center bg-white p-2 rounded-pill shadow-sm border px-4">
                    <i class="fas fa-clock text-primary me-2"></i>
                    <span id="live-clock" class="fw-900 text-dark" style="min-width: 80px;">00:00:00</span>
                    <div class="vr mx-3" style="height: 20px;"></div>
                    <span class="badge bg-soft-primary text-primary fw-bold text-uppercase">
                        <?= date('d M Y') ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Major Tasks Grid - THE CORE OF KASIR FLOW -->
        <div class="mb-5">
            <h6 class="fw-900 text-dark mb-4 text-uppercase letter-spacing-1">
                <i class="fas fa-tasks text-primary me-2"></i>Tugas Utama Kasir
            </h6>
            <div class="action-grid">
                <!-- Task 1: Bayar SPK -->
                <a href="<?= site_url('transaksi/transaksi_belumbayar') ?>" class="task-card">
                    <i class="fas fa-money-check-alt"></i>
                    <div class="task-info">
                        <div class="task-label">Bayar SPK</div>
                        <div class="task-desc">Proses nota produksi</div>
                    </div>
                </a>

                <!-- Task 2: Titipan Produksi -->
                <a href="<?= site_url('transaksi/order_titipan') ?>" class="task-card border-primary border-dashed"
                    style="border-style: dashed !important; background: #f8faff;">
                    <i class="fas fa-exchange-alt"></i>
                    <div class="task-info">
                        <div class="task-label text-primary">Produksi Titipan</div>
                        <div class="task-desc">Order dari cabang lain</div>
                    </div>
                    <?php if ($total_titipan > 0): ?>
                        <span
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow">
                            <?= $total_titipan ?>
                        </span>
                    <?php endif; ?>
                </a>

                <!-- Task 2: Terima Piutang -->
                <a href="<?= site_url('transaksi/daftar_piutang') ?>" class="task-card">
                    <i class="fas fa-hand-holding-usd"></i>
                    <div class="task-info">
                        <div class="task-label">Terima Piutang</div>
                        <div class="task-desc">Pelunasan konsumen</div>
                    </div>
                </a>

                <!-- Task: Cari Transaksi Konsumen -->
                <a href="#" class="task-card border-info border-dashed" style="border-style: dashed !important; background: #f0f8ff;" data-bs-toggle="modal" data-bs-target="#modalCariTransaksi">
                    <i class="fas fa-search-dollar text-info"></i>
                    <div class="task-info">
                        <div class="task-label text-info">Cek Transaksi</div>
                        <div class="task-desc">Histori SPK Konsumen</div>
                    </div>
                </a>


                <!-- Task 5: Transaksi Manual -->
                <a href="<?= site_url('penjualan_manual') ?>" class="task-card">
                    <i class="fas fa-keyboard"></i>
                    <div class="task-info">
                        <div class="task-label">Jual Manual</div>
                        <div class="task-desc">Input nota langsung</div>
                    </div>
                </a>

                <!-- Task 4: Pengeluaran Cash -->
                <a href="<?= site_url('laporan/pengeluaran') ?>" class="task-card">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <div class="task-info">
                        <div class="task-label">Pengeluaran</div>
                        <div class="task-desc">Input biaya harian</div>
                    </div>
                </a>

                <!-- Task 3: Transaksi Harian -->
                <!-- <a href="<?= site_url('laporan/pemasukan_harian') ?>" class="task-card">
                    <i class="fas fa-receipt"></i>
                    <div class="task-info">
                        <div class="task-label">Laporan Harian</div>
                        <div class="task-desc">Hasil transaksi hari ini</div>
                    </div>
                </a> -->

                <!-- Task 6: Rekonsiliasi -->
                <!-- <a href="<?= site_url('KasirShift') ?>" class="task-card">
                    <i class="fas fa-balance-scale"></i>
                    <div class="task-info">
                        <div class="task-label">Rekonsiliasi</div>
                        <div class="task-desc">Cocokan fisik uang</div>
                    </div>
                </a> -->
            </div>
        </div>

        <!-- Stat Overview -->
        <div class="row g-4 mb-5">
            <!-- <div class="col-md-3">
                <a href="<?= site_url('transaksi/order_titipan') ?>" class="text-decoration-none">
                    <div class="stat-card-kasir shadow-sm border border-primary bg-light"
                        style="background-image: radial-gradient(circle at 10% 20%, rgba(67, 97, 238, 0.05) 0%, rgba(255, 255, 255, 0) 90%);">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="stat-icon-small bg-primary p-2 rounded-3">
                                    <i class="fas fa-exchange-alt text-white"></i>
                                </div>
                                <?php if ($total_titipan > 0): ?>
                                    <span class="badge bg-danger pulse-animation">BUTUH PROSES</span>
                                <?php endif; ?>
                            </div>
                            <h6 class="text-primary small fw-bold text-uppercase mb-1">Titipan Produksi</h6>
                            <h4 class="fw-900 text-dark mb-0">
                                <?= $total_titipan ?? 0 ?> <small class="text-muted fw-normal">Nota</small>
                            </h4>
                        </div>
                    </div>
                </a>
            </div> -->

            <div class="col-md-3">
                <div class="stat-card-kasir shadow-sm border border-light">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="stat-icon-small bg-soft-primary p-2 rounded-3">
                                <i class="fas fa-wallet text-primary"></i>
                            </div>
                            <span class="trend-indicator trend-up">Aktif</span>
                        </div>
                        <h6 class="text-muted small fw-bold text-uppercase mb-1">Penjualan Hari Ini</h6>
                        <h4 class="fw-900 text-dark mb-0">Rp
                            <?= number_format($total_trxday['total'] ?? 0, 0, ',', '.') ?>
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card-kasir shadow-sm border border-light">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="stat-icon-small bg-soft-warning p-2 rounded-3">
                                <i class="fas fa-clock text-warning"></i>
                            </div>
                        </div>
                        <h6 class="text-muted small fw-bold text-uppercase mb-1">SPK Belum Bayar</h6>
                        <h4 class="fw-900 text-dark mb-0"><?= $total_belumbayar ?? 0 ?> <small
                                class="text-muted fw-normal">Nota</small></h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card-kasir shadow-sm border border-light">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="stat-icon-small bg-soft-success p-2 rounded-3">
                                <i class="fas fa-check-double text-success"></i>
                            </div>
                        </div>
                        <h6 class="text-muted small fw-bold text-uppercase mb-1">Transaksi Selesai</h6>
                        <h4 class="fw-900 text-dark mb-0"><?= $total_penj_hariini['total'] ?? 0 ?> <small
                                class="text-muted fw-normal">Nota</small></h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card-kasir shadow-sm border border-light">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="stat-icon-small bg-soft-danger p-2 rounded-3">
                                <i class="fas fa-hand-holding-usd text-danger"></i>
                            </div>
                        </div>
                        <h6 class="text-muted small fw-bold text-uppercase mb-1">Total Piutang</h6>
                        <h4 class="fw-900 text-danger mb-0">Rp <?= number_format($total_piutang ?? 0, 0, ',', '.') ?>
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visual Analytics & System Updates -->
        <div class="row g-4">
            <!-- Grafik Omzet -->
            <div class="col-xl-8 col-lg-7">
                <div class="chart-box h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="fw-900 text-dark mb-0"><i class="fas fa-chart-area text-primary me-2"></i>Ringkasan Omzet Bulanan <?= date('Y') ?></h6>
                        <div class="badge bg-soft-primary text-primary fw-bold px-3 py-2 rounded-pill">
                            Data Terupdate
                        </div>
                    </div>
                    <div style="height: 300px;">
                        <canvas id="kasirChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Widget Pembaruan Sistem -->
            <div class="col-xl-4 col-lg-5">
                <div class="stat-card-kasir shadow-sm border border-light h-100" style="background: linear-gradient(145deg, #ffffff, #f8fafc);">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="fw-900 text-dark mb-0"><i class="fas fa-bullhorn text-warning me-2"></i>Pembaruan Sistem</h6>
                            <span class="badge bg-danger pulse-animation rounded-pill">Baru ✨</span>
                        </div>
                        
                        <div class="flex-grow-1 overflow-auto" style="max-height: 300px; padding-right: 5px;">
                            <?php if(!empty($system_updates)): ?>
                                <?php foreach($system_updates as $update): 
                                    $icons = ['BUG' => 'fa-bug text-danger', 'FEATURE' => 'fa-star text-warning', 'SUPPORT' => 'fa-headset text-info', 'ENHANCEMENT' => 'fa-rocket text-primary'];
                                    $icon_class = isset($icons[strtoupper($update->category)]) ? $icons[strtoupper($update->category)] : 'fa-check-circle text-success';
                                    $bg_class = isset($icons[strtoupper($update->category)]) ? str_replace('text-', 'bg-soft-', explode(' ', $icons[strtoupper($update->category)])[1]) : 'bg-soft-success';
                                ?>
                                <div class="mb-4 pb-3 border-bottom border-light">
                                    <div class="d-flex align-items-start">
                                        <div class="<?= $bg_class ?> <?= explode(' ', $icon_class)[1] ?> p-2 rounded flex-shrink-0 mt-1 me-3">
                                            <i class="fas <?= explode(' ', $icon_class)[0] ?>"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1 fs-6"><?= htmlspecialchars($update->feature_name) ?></h6>
                                            <p class="text-muted small mb-1 lh-sm"><?= htmlspecialchars($update->description) ?></p>
                                            <span class="text-secondary text-uppercase" style="font-size: 0.65rem; font-weight: 800;">
                                                <?= date('M Y', strtotime($update->created_at)) ?> • <?= htmlspecialchars($update->category) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted small text-center mt-4 fw-bold">Belum ada pembaruan sistem terbaru.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cari Transaksi Konsumen -->
<div class="modal fade" id="modalCariTransaksi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-info text-white p-4 border-0">
                <h5 class="modal-title fw-900 text-uppercase"><i class="fas fa-search-dollar me-2"></i> Cari Histori Transaksi Konsumen</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="row mb-4">
                    <div class="col-md-8 mx-auto">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body p-4 text-center">
                                <h6 class="fw-bold mb-3 text-muted">Ketik Nama atau Nomor HP Konsumen</h6>
                                <select id="select-konsumen-cari" class="form-select form-select-lg w-100" data-placeholder="Pilih Konsumen..."></select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="hasil-pencarian-transaksi" class="d-none">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-list me-2 text-primary"></i>Daftar Transaksi: <span id="nama-konsumen-terpilih" class="text-primary"></span></h6>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2" id="total-transaksi-badge">0 SPK</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" id="table-histori-transaksi">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">No SPK</th>
                                            <th>Tanggal</th>
                                            <th>Total (Rp)</th>
                                            <th>Status Bayar</th>
                                            <th>Status Produksi</th>
                                            <th>Cabang</th>
                                            <th class="text-center pe-4">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-histori-transaksi">
                                        <!-- Data will be populated here via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="loading-pencarian" class="text-center py-5 d-none">
                    <div class="spinner-border text-info mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h6 class="text-muted fw-bold">Mencari data transaksi...</h6>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-white">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">TUTUP</button>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Inisialisasi Select2 untuk pencarian konsumen di dalam modal
    $(document).ready(function() {
        if ($.fn.select2) {
            $('#select-konsumen-cari').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#modalCariTransaksi'),
                ajax: {
                    url: '<?= base_url("transaksi/search_konsumen") ?>',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term // parameter pencarian
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.konsumen_nama + (item.konsumen_nohp ? ' (' + item.konsumen_nohp + ')' : ''),
                                    id: item.konsumen_id,
                                    nama: item.konsumen_nama
                                }
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 3,
                language: {
                    inputTooShort: function() {
                        return "Ketik minimal 3 karakter...";
                    },
                    noResults: function() {
                        return "Konsumen tidak ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                }
            }).on('select2:select', function(e) {
                var data = e.params.data;
                cariTransaksiKonsumen(data.id, data.nama);
            });
        }
    });

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }

    function cariTransaksiKonsumen(konsumen_id, nama_konsumen) {
        $('#hasil-pencarian-transaksi').addClass('d-none');
        $('#loading-pencarian').removeClass('d-none');
        $('#nama-konsumen-terpilih').text(nama_konsumen);
        $('#tbody-histori-transaksi').empty();

        $.ajax({
            url: '<?= base_url("transaksi/get_transaksi_by_konsumen_ajax") ?>',
            type: 'POST',
            data: { konsumen_id: konsumen_id },
            dataType: 'json',
            success: function(response) {
                $('#loading-pencarian').addClass('d-none');
                
                if(response.status === 'success') {
                    const data = response.data;
                    $('#total-transaksi-badge').text(data.length + ' SPK');
                    
                    if(data.length > 0) {
                        let html = '';
                        data.forEach(function(item) {
                            // Badge Status Pembayaran
                            let statusBayarBadge = '';
                            if(item.status_pembayaran === 'paid') {
                                statusBayarBadge = '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1"><i class="fas fa-check-circle me-1"></i>Lunas</span>';
                            } else if(item.status_pembayaran === 'dp') {
                                statusBayarBadge = '<span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1"><i class="fas fa-adjust me-1"></i>DP (Kurang Rp '+formatRupiah(item.sisa_pembayaran)+')</span>';
                            } else {
                                statusBayarBadge = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1"><i class="fas fa-times-circle me-1"></i>Belum Bayar</span>';
                            }

                            // Badge Status Produksi (WIP)
                            let statusProdBadge = '';
                            if(item.status_wip == 'Selesai (Di Konsumen)' || item.status_wip == 'Selesai (Barang di PB)' || item.status_produksi == 'done' || item.status_produksi == '2') {
                                statusProdBadge = '<span class="badge bg-success"><i class="fas fa-check-double me-1"></i>Selesai</span>';
                            } else if(item.status_wip && item.status_wip != 'Selesai') {
                                statusProdBadge = '<span class="badge bg-info"><i class="fas fa-spinner fa-spin me-1"></i>' + item.status_wip + '</span>';
                            } else {
                                statusProdBadge = '<span class="badge bg-secondary">Pending</span>';
                            }

                            html += `
                                <tr>
                                    <td class="ps-4 fw-bold">
                                        <a href="#" class="text-primary text-decoration-none" onclick="previewSPK('${item.nospk}'); return false;">${item.nospk}</a>
                                    </td>
                                    <td>
                                        <div class="small fw-bold">${item.tanggal}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">${item.waktu}</div>
                                    </td>
                                    <td class="fw-900 text-dark">Rp ${formatRupiah(item.total_pembayaran)}</td>
                                    <td>${statusBayarBadge}</td>
                                    <td>${statusProdBadge}</td>
                                    <td><span class="badge bg-light text-dark border">${item.nama_perusahaan}</span></td>
                                    <td class="text-center pe-4">
                                        <button class="btn btn-sm btn-primary rounded-pill fw-bold shadow-sm" onclick="previewSPK('${item.nospk}')">
                                            <i class="fas fa-eye me-1"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        $('#tbody-histori-transaksi').html(html);
                    } else {
                        $('#tbody-histori-transaksi').html('<tr><td colspan="7" class="text-center py-4 text-muted fw-bold">Tidak ada histori transaksi untuk konsumen ini.</td></tr>');
                    }
                    
                    $('#hasil-pencarian-transaksi').removeClass('d-none');
                } else {
                    alert('Terjadi kesalahan: ' + response.message);
                }
            },
            error: function() {
                $('#loading-pencarian').addClass('d-none');
                alert('Terjadi kesalahan koneksi server.');
            }
        });
    }

    // Modal iframe helper for previewing SPK (Reusing existing standard)
    function previewSPK(nospk) {
        let previewModal = document.getElementById('previewSPKModal');
        if (!previewModal) {
            // Create modal dynamically if it doesn't exist on the page
            const modalHtml = `
            <div class="modal fade" id="previewSPKModal" tabindex="-1" aria-labelledby="previewSPKModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content rounded-4 shadow-lg border-0 overflow-hidden" style="height: 90vh;">
                        <div class="modal-header bg-dark text-white border-0 py-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-file-invoice text-white"></i>
                                </div>
                                <div>
                                    <h5 class="modal-title fw-bold mb-0" id="previewSPKModalLabel">Detail Dokumen SPK</h5>
                                    <small class="text-white-50 spk-number-display">Pratinjau</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm" onclick="printIframeSPK()">
                                    <i class="fas fa-print me-2"></i>Cetak Dokumen
                                </button>
                                <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        </div>
                        <div class="modal-body p-0 position-relative bg-light">
                            <div id="spkLoader" class="position-absolute top-50 start-50 translate-middle text-center z-3">
                                <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <h6 class="fw-bold text-muted">Memuat dokumen...</h6>
                            </div>
                            <iframe id="iframePreviewSPK" style="width: 100%; height: 100%; border: none;" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>`;
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            previewModal = document.getElementById('previewSPKModal');
            
            // Handle iframe loading state
            document.getElementById('iframePreviewSPK').onload = function() {
                document.getElementById('spkLoader').classList.add('d-none');
            };
        }
        
        // Show loader and set URL
        document.getElementById('spkLoader').classList.remove('d-none');
        document.querySelector('#previewSPKModal .spk-number-display').textContent = 'No SPK: ' + nospk;
        
        // URL cetak_invoice dengan parameter preview via Wip aman untuk kasir
        const safeNospk = nospk.replace(/\//g, '~');
        const url = '<?= base_url("wip/view_spk_invoice/") ?>' + safeNospk;
        document.getElementById('iframePreviewSPK').src = url;
        
        // Show modal
        const modal = new bootstrap.Modal(previewModal);
        modal.show();
    }

    function printIframeSPK() {
        const iframe = document.getElementById('iframePreviewSPK');
        if(iframe && iframe.contentWindow) {
            iframe.contentWindow.print();
        }
    }

    function updateClock() {

        const now = new Date();
        const timeStr = now.getHours().toString().padStart(2, '0') + ':' +
            now.getMinutes().toString().padStart(2, '0') + ':' +
            now.getSeconds().toString().padStart(2, '0');
        const clockElement = document.getElementById('live-clock');
        if (clockElement) clockElement.textContent = timeStr;
    }
    setInterval(updateClock, 1000);
    updateClock();

    $(document).ready(function () {
        const ctx = document.getElementById('kasirChart');
        if (!ctx) return;

        const months = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sept", "Okt", "Nov", "Des"];
        const salesData = [
            <?= $jan['total'] ?? 0 ?>, <?= $feb['total'] ?? 0 ?>, <?= $mar['total'] ?? 0 ?>,
            <?= $apr['total'] ?? 0 ?>, <?= $mei['total'] ?? 0 ?>, <?= $jun['total'] ?? 0 ?>,
            <?= $jul['total'] ?? 0 ?>, <?= $ags['total'] ?? 0 ?>, <?= $sept['total'] ?? 0 ?>,
            <?= $okt['total'] ?? 0 ?>, <?= $nov['total'] ?? 0 ?>, <?= $des['total'] ?? 0 ?>
        ];

        new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Omzet',
                    data: salesData,
                    borderColor: '#4361ee',
                    backgroundColor: 'rgba(67, 97, 238, 0.08)',
                    borderWidth: 5,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4361ee',
                    pointBorderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function (context) {
                                return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#f0f0f0' },
                        ticks: {
                            callback: function (value) {
                                if (value >= 1000000) return 'Rp ' + (value / 1000000) + 'jt';
                                return 'Rp ' + value.toLocaleString();
                            }
                        }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>