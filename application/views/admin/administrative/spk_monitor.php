<?php
/**
 * View Monitoring Performa Deskprint
 * Lokasi: application/views/admin/administrative/spk_monitor.php
 */
?>

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap">
<style>
    :root {
        --primary-color: #4e73df;
        --secondary-color: #858796;
        --success-color: #1cc88a;
        --info-color: #36b9cc;
        --dark-color: #5a5c69;
        --border-color: #e3e6f0;
        --bg-light: #f8f9fc;
    }



    .header-monitor {
        background: white;
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 2rem;
    }

    .header-monitor h2 {
        font-weight: 800;
        color: var(--dark-color);
        margin-bottom: 0.25rem;
        letter-spacing: -0.5px;
    }

    .card-modern {
        border-radius: 12px;
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        background: white;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .card-modern:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 2rem rgba(0, 0, 0, 0.15);
        border-left: 5px solid var(--primary-color);
    }

    .card-title-modern {
        font-weight: 700;
        color: var(--primary-color);
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--dark-color);
    }

    .table-modern thead th {
        background-color: #f8f9fc;
        color: var(--primary-color);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        border-top: none;
        padding: 12px 15px;
    }

    .table-modern td {
        vertical-align: middle;
        padding: 12px 15px;
        font-size: 0.9rem;
        color: #444;
    }

    .badge-deskprint {
        background-color: rgba(78, 115, 223, 0.1);
        color: var(--primary-color);
        font-weight: 600;
        padding: 5px 10px;
        border-radius: 6px;
    }

    .filter-box {
        background: white;
        padding: 1.25rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        border: 1px solid var(--border-color);
    }

    .btn-filter {
        background-color: var(--primary-color);
        color: white;
        font-weight: 600;
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        border: none;
    }

    .btn-filter:hover {
        background-color: #2e59d9;
        color: white;
    }

    /* Minimalist Scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    ::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #999;
    }
</style>

<div class="app-content">
    <div class="header-monitor">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col">
                    <h2>Monitoring Performa Deskprint</h2>
                    <p class="text-muted small mb-0">Audit beban kerja dan produktivitas tim deskprint</p>
                </div>
                <div class="col-auto">
                    <div class="badge bg-light text-primary border p-2">
                        <i class="fas fa-calendar-alt me-1"></i>
                        <?= date('d M Y', strtotime($start_date)) ?> -
                        <?= date('d M Y', strtotime($end_date)) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Filter Form -->
        <div class="filter-box shadow-sm no-print">
            <form action="" method="GET" class="row align-items-end g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Mulai Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-filter w-100">
                        <i class="fas fa-search me-2"></i> Filter
                    </button>
                </div>
                <div class="col text-end">
                    <button type="button" onclick="window.print()" class="btn btn-outline-secondary">
                        <i class="fas fa-print me-2"></i> Cetak Laporan
                    </button>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-12 mb-3">
                <h5 class="fw-bold"><i class="fas fa-chart-line me-2 text-primary"></i>Total Capaian Tim</h5>
            </div>

            <!-- Global Dashboard Bar -->
            <div class="col-md-3 mb-4">
                <div class="card card-modern p-3 border-start border-4 border-primary">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Team SPK</div>
                    <div class="h3 mb-0 font-weight-bold text-gray-800"><?= number_format($global_stats['total_spk']) ?>
                    </div>
                    <div class="text-muted small mt-1">Seluruh personil deskprint</div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card card-modern p-3 border-start border-4 border-success">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Omset Tim</div>
                    <div class="h3 mb-0 font-weight-bold text-gray-800">Rp
                        <?= number_format($global_stats['total_omset'], 0, ',', '.') ?>
                    </div>
                    <div class="text-muted small mt-1">Akumulasi nilai transaksi</div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card card-modern p-3 border-start border-4 border-info">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Rata-rata/SPK</div>
                    <div class="h3 mb-0 font-weight-bold text-gray-800">
                        Rp
                        <?= $global_stats['total_spk'] > 0 ? number_format($global_stats['total_omset'] / $global_stats['total_spk'], 0, ',', '.') : 0 ?>
                    </div>
                    <div class="text-muted small mt-1">Nilai rata-rata pengerjaan</div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card card-modern p-3 border-start border-4 border-warning">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Status Lunas</div>
                    <div class="h3 mb-0 font-weight-bold text-gray-800">
                        <?= $global_stats['total_spk'] > 0 ? round($global_stats['paid_spk'] / $global_stats['total_spk'] * 100, 1) : 0 ?>%
                    </div>
                    <div class="text-muted small mt-1"><?= $global_stats['paid_spk'] ?> Lunas /
                        <?= $global_stats['pending_spk'] ?> Pending
                    </div>
                </div>
            </div>

            <div class="col-12 mb-3 mt-3">
                <h5 class="fw-bold"><i class="fas fa-tachometer-alt me-2 text-primary"></i>Produktivitas Individu</h5>
            </div>

            <?php if (empty($summary)): ?>
                <div class="col-12 text-center py-4">
                    <div class="alert alert-light border">Belum ada data pengerjaan SPK pada periode ini.</div>
                </div>
            <?php else: ?>
                <?php foreach ($summary as $s): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card card-modern p-4"
                            onclick="showDetailModal('<?= $s->username ?>', '<?= $s->nama_lengkap ?: $s->username ?>')">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <div class="card-title-modern text-primary fs-5 mb-0">
                                        <?= $s->nama_lengkap ?: $s->username ?>
                                    </div>
                                    <span class="badge bg-light text-primary border px-2 py-1 small">Top Performer</span>
                                </div>
                                <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                                    <i class="fas fa-user-check text-primary"></i>
                                </div>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="small text-muted">Total SPK</div>
                                    <div class="h4 fw-800 mb-0"><?= number_format($s->total_spk) ?></div>
                                </div>
                                <div class="col-6">
                                    <div class="small text-muted">Omset</div>
                                    <div class="h4 fw-800 mb-0 text-success">Rp
                                        <?= number_format($s->total_omset, 0, ',', '.') ?>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-3 opacity-5">

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-xs text-muted mb-0">Rata-rata / SPK</div>
                                    <div class="fw-bold text-dark">Rp
                                        <?= number_format($s->total_omset / $s->total_spk, 0, ',', '.') ?>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="text-primary small fw-bold">Detail <i class="fas fa-chevron-right ms-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Detailed SPK Table -->
        <div class="card card-modern border-0">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0 fw-bold">Detail Data Seluruh SPK</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern table-hover mb-0" id="tableMonitor">
                        <thead>
                            <tr>
                                <th>No. SPK</th>
                                <th>Tanggal</th>
                                <th>Konsumen</th>
                                <th>Jenis Transaksi</th>
                                <th>Deskprint</th>
                                <th>Total Tagihan</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($spk_list as $spk): ?>
                                <tr>
                                    <td class="fw-bold">
                                        <a href="javascript:void(0)" onclick="showSPKModal('<?= $spk->nospk ?>')"
                                            class="text-primary text-decoration-none">
                                            <?= $spk->nospk ?> <i class="fas fa-eye small ms-1"
                                                style="font-size: 0.75rem;"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <?= date('d/m/Y H:i', strtotime($spk->created_at)) ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold">
                                            <?= $spk->konsumen_nama ?: 'Umum' ?>
                                        </div>
                                        <div class="small text-muted">
                                            <?= $spk->konsumen_nohp ?>
                                        </div>
                                    </td>
                                    <td><span class="small">
                                            <?= $spk->nama_jenis_transaksi ?>
                                        </span></td>
                                    <td>
                                        <span class="badge-deskprint">
                                            <i class="fas fa-user-edit me-1"></i>
                                            <?= $spk->deskprint_name ?>
                                        </span>
                                    </td>
                                    <td class="fw-bold">Rp
                                        <?= number_format($spk->total_tagihan, 0, ',', '.') ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($spk->status_pembayaran == 'paid'): ?>
                                            <span class="badge bg-success small">LUNAS</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark small">PENDING</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Deskprint -->
<div class="modal fade" id="modalDetailDeskprint" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-bottom-0 p-4">
                <div>
                    <h5 class="modal-title fw-800" id="modalTitle">Detail Pengerjaan SPK</h5>
                    <p class="text-muted small mb-0" id="modalSubtitle">Daftar transaksi yang ditangani</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tableDetailDeskprint">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3" style="width: 50px;">NO</th>
                                <th>NO. SPK</th>
                                <th>TANGGAL</th>
                                <th>KONSUMEN</th>
                                <th>JENIS</th>
                                <th class="text-end">NOMINAL</th>
                                <th class="text-center pe-3">STATUS</th>
                            </tr>
                        </thead>
                        <tbody id="contentDetailDeskprint">
                            <!-- Data injected via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top-0 p-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail SPK (Mirror View) -->
<div class="modal fade" id="modalSPKDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-md-down modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; height: 90vh;">

            <div class="modal-body p-0 bg-light">

                <!-- Iframe Container for Invoice -->
                <iframe id="frameSPK" src="" style="width: 100%; height: 100%; border: none; display: none;"></iframe>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script>
    $(document).ready(function () {
        $('#tableMonitor').DataTable({
            "order": [[1, "desc"]],
            "pageLength": 25,
            "language": {
                "search": "Cari SPK/Konsumen:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "info": "Menampilkan _START_ s/d _END_ dari _TOTAL_ data"
            }
        });
    });

    function showDetailModal(username, fullName) {
        $('#modalTitle').text('Detail Pengerjaan SPK: ' + fullName);
        $('#modalSubtitle').text('Periode: <?= date('d M Y', strtotime($start_date)) ?> - <?= date('d M Y', strtotime($end_date)) ?>');

        // Show Loading
        $('#contentDetailDeskprint').html('<tr><td colspan="6" class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Sedang memuat data...</p></td></tr>');
        $('#modalDetailDeskprint').modal('show');

        // Fetch Data via AJAX
        $.ajax({
            url: '<?= base_url('administrative/get_deskprint_detail') ?>',
            type: 'GET',
            data: {
                username: username,
                start_date: '<?= $start_date ?>',
                end_date: '<?= $end_date ?>'
            },
            dataType: 'json',
            success: function (response) {
                let html = '';
                if (response.length > 0) {
                    $.each(response, function (i, item) {
                        let statusBadge = item.status_pembayaran === 'paid'
                            ? '<span class="badge bg-success small">LUNAS</span>'
                            : '<span class="badge bg-warning text-dark small">PENDING</span>';

                        let nominal = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(item.total_tagihan);

                        html += `
                            <tr>
                                <td class="ps-3 text-muted small">${i + 1}</td>
                                <td class="fw-bold text-primary">${item.nospk}</td>
                                <td>${moment(item.created_at).format('DD/MM/YYYY HH:i')}</td>
                                <td><div class="fw-bold">${item.konsumen_nama || 'Umum'}</div></td>
                                <td><span class="small">${item.nama_jenis_transaksi || '-'}</span></td>
                                <td class="text-end fw-bold">${nominal}</td>
                                <td class="text-center pe-3">${statusBadge}</td>
                            </tr>
                        `;
                    });
                } else {
                    html = '<tr><td colspan="6" class="text-center py-4">Tidak ada data ditemukan.</td></tr>';
                }
                $('#contentDetailDeskprint').html(html);
            },
            error: function () {
                $('#contentDetailDeskprint').html('<tr><td colspan="6" class="text-center py-4 text-danger">Gagal mengambil data. Silahkan coba lagi.</td></tr>');
            }
        });
    }

    function showSPKModal(nospk) {
        // Safe encode for URL Segment (Replace / with ~ to avoid 404 on some servers)
        let safeSpk = nospk.replace(/\//g, '~');
        let url = '<?= base_url('wip/view_spk_invoice/') ?>' + encodeURIComponent(safeSpk);

        // Tracking view can use standard encoding or query param if preferred, keeping existing for now
        let trackingUrl = '<?= base_url('wip/check_client_view/') ?>' + encodeURIComponent(nospk);

        // Reset state
        $('#iframeLoader').show();
        $('#frameSPK').hide().attr('src', url);
        $('#linkOpenNewTab').attr('href', trackingUrl); // Keep "Open New Tab" pointing to tracking for full view

        // Handle iframe load
        $('#frameSPK').off('load').on('load', function () {
            hideLoader();
        });

        $('#modalSPKDetail').modal('show');
    }

    function hideLoader() {
        $('#iframeLoader').hide();
        $('#frameSPK').show();
    }
</script>