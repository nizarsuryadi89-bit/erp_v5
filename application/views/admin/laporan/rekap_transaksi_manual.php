<?php
/**
 * Rekap Transaksi Manual - Integrated Dashboard Style
 * Displays manual transaction logs with consistent premium design.
 */
$current_perusahaan_id = $this->input->get('perusahaan_id') ?: $this->session->userdata('perusahaan_id');
?>

<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">

<style>
    /* Ensure consistency with the main report page (dashboard.php) */
    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
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

    /* Color variations for stats */
    .stat-card.success::before { background: linear-gradient(90deg, #27ae60, #2ecc71); }
    .stat-card.info::before { background: linear-gradient(90deg, #3498db, #2980b9); }
    .stat-card.warning::before { background: linear-gradient(90deg, #f39c12, #d35400); }
    .stat-card.danger::before { background: linear-gradient(90deg, #e74c3c, #c0392b); }

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

    .stat-icon.success { background: linear-gradient(135deg, #27ae60, #2ecc71); }
    .stat-icon.info { background: linear-gradient(135deg, #3498db, #2980b9); }
    .stat-icon.warning { background: linear-gradient(135deg, #f39c12, #d35400); }
    .stat-icon.danger { background: linear-gradient(135deg, #e74c3c, #c0392b); }

    .stat-info h3 {
        font-size: 1.8rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
        line-height: 1;
    }

    .stat-info p {
        font-size: 0.8rem;
        color: #6c757d;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    /* Report Card Style */
    .report-section-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .section-header {
        background: linear-gradient(135deg, #2c3e50, #34495e);
        color: white;
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .section-header h5 {
        margin: 0;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .section-body {
        padding: 1.5rem;
    }

    .table th {
        background: #f8f9fa;
        color: #2c3e50;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        border-color: #e9ecef;
        padding: 1rem;
    }

    .table td {
        padding: 1rem;
        vertical-align: middle;
    }

    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 0;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .badge-paid { background: #e7f5ee; color: #1e7e34; border: 1px solid #c3e6cb; }
    .badge-pending { background: #fff9e6; color: #856404; border: 1px solid #ffeeba; }

    .nav-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
        text-decoration: none !important;
        display: block;
        height: 100%;
        position: relative;
    }

    .nav-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #2c3e50, #34495e);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .nav-card:hover::before { opacity: 1; }
    .nav-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        border-color: #2c3e50;
    }

    .nav-icon {
        width: 60px;
        height: 60px; margin: 0 auto 1rem;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.8rem; color: #2c3e50; background: #f8f9fa; border-radius: 50%;
        transition: all 0.3s ease;
    }

    .nav-card:hover .nav-icon { background: linear-gradient(135deg, #2c3e50, #34495e); color: white; }
    .nav-title { font-weight: 700; color: #2c3e50; }

    @media print {
        .app-content { padding-top: 0 !important; }
        .no-print { display: none !important; }
        .report-section-card { border: 1px solid #eee; box-shadow: none; }
    }
</style>

<div class="app-content">
    <!-- Header Modul (Consistent with Dashboard) -->
    <?php $this->load->view('layout_admin/header_modul'); ?>

    <div class="container-fluid">
        <!-- Branch Switcher & Filter Card -->
        <div class="report-section-card mb-4 no-print">
            <div class="section-body p-4">
                <form action="<?= base_url('laporan/rekap_transaksi_manual') ?>" method="GET" class="row align-items-center g-3">
                    <div class="col-md-5">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-light p-3 border text-primary">
                                <i class="fa fa-building fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Unit Bisnis / Cabang</h6>
                                <p class="text-muted small mb-0">Audit log transaksi input manual.</p>
                            </div>
                        </div>
                    </div>
                    <?php if ($is_admin_owner && !empty($all_perusahaan)): ?>
                            <div class="col-md-3">
                                <select class="form-select border-1 fw-bold" name="perusahaan_id" onchange="this.form.submit()" style="height: 50px; border-radius: 0;">
                                    <?php foreach ($all_perusahaan as $p): ?>
                                            <option value="<?= $p->id_perusahaan ?>" <?= ($current_perusahaan_id == $p->id_perusahaan) ? 'selected' : '' ?>>
                                                <?= $p->nama_perusahaan ?>
                                            </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                    <?php else: ?>
                            <input type="hidden" name="perusahaan_id" value="<?= $current_perusahaan_id ?>">
                            <div class="col-md-3"></div>
                    <?php endif; ?>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white" style="border-radius: 0;"><i class="fa fa-calendar-alt text-muted"></i></span>
                            <input type="month" name="bulan" class="form-control fw-bold" value="<?= $bulan ?>" onchange="this.form.submit()" style="height: 50px; border-radius: 0;">
                        </div>
                    </div>
                    <div class="col-md-1 text-end">
                        <button type="button" onclick="window.print()" class="btn btn-outline-dark py-2 px-3" style="border-radius: 0;">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="stats-overview">
            <div class="stat-card info">
                <div class="stat-header">
                    <div class="stat-icon info">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= number_format($total_transaksi) ?></h3>
                        <p>Total Transaksi</p>
                    </div>
                </div>
            </div>
            <div class="stat-card success">
                <div class="stat-header">
                    <div class="stat-icon success">
                         <i class="fas fa-wallet"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></h3>
                        <p>Total Omzet Manual</p>
                    </div>
                </div>
            </div>
            <div class="stat-card danger">
                <div class="stat-header">
                    <div class="stat-icon danger">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Rp <?= number_format($total_piutang, 0, ',', '.') ?></h3>
                        <p>Sisa Piutang</p>
                    </div>
                </div>
            </div>
            <div class="stat-card warning">
                <div class="stat-header">
                    <div class="stat-icon warning">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Rp <?= $total_transaksi > 0 ? number_format($total_pendapatan / $total_transaksi, 0, ',', '.') : '0' ?></h3>
                        <p>Rata-rata / TRX</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Transaction Table -->
        <div class="report-section-card">
            <div class="section-header">
                <h5><i class="fas fa-list-check me-2"></i>Rincian Transaksi Manual Periode <?= date('F Y', strtotime($bulan . '-01')) ?></h5>
                <div class="no-print d-flex gap-2">
                    <a href="<?= base_url('laporan/export_transaksi_manual?bulan=' . $bulan . '&perusahaan_id=' . $current_perusahaan_id) ?>" class="btn btn-sm btn-light py-1 px-3" style="border-radius: 0;">
                        <i class="fas fa-file-excel me-1 text-success"></i> EXCEL
                    </a>
                    <a href="<?= base_url('laporan/print_transaksi_manual?bulan=' . $bulan . '&perusahaan_id=' . $current_perusahaan_id) ?>" target="_blank" class="btn btn-sm btn-light py-1 px-3" style="border-radius: 0;">
                        <i class="fas fa-print me-1 text-primary"></i> PRINT
                    </a>
                </div>
            </div>
            <div class="section-body p-0">
                <div class="p-3 border-bottom no-print">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0" style="border-radius: 0; border-left: 4px solid #3498db !important;"><i class="fa fa-search text-muted"></i></span>
                        <input type="text" id="customSearch" class="form-control form-control-sm border-0 bg-light" placeholder="Cari SPK, Konsumen, atau Produk..." style="border-radius: 0;">
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="tableManual" class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>NO. SPK</th>
                                <th>TANGGAL</th>
                                <th>KONSUMEN</th>
                                <th>PRODUK</th>
                                <th class="text-center">QTY</th>
                                <th class="text-end">TOTAL TAGIHAN</th>
                                <th class="text-end">TERBAYAR</th>
                                <th class="text-center">STATUS</th>
                                <th>METODE</th>
                                <th>KET</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transaksi as $row): ?>
                                    <tr>
                                        <td class="fw-bold text-primary"><?= $row->nospk ?></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= date('d M Y', strtotime($row->created_at)) ?></div>
                                            <small class="text-muted"><?= date('H:i', strtotime($row->created_at)) ?> WIB</small>
                                        </td>
                                        <td>
                                            <div class="fw-bold"><?= $row->konsumen_nama ?></div>
                                            <small class="text-muted"><i class="fa fa-phone me-1 small"></i><?= $row->konsumen_nohp ?? '-' ?></small>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= $row->product_nama ?></div>
                                            <small class="text-muted"><?= $row->product_deskripsi ?: '-' ?></small>
                                        </td>
                                        <td class="text-center fw-bold"><?= number_format($row->qty) ?></td>
                                        <td class="text-end fw-bold text-dark">Rp <?= number_format($row->total_tagihan, 0, ',', '.') ?></td>
                                        <td class="text-end fw-bold text-success">Rp <?= number_format($row->total_bayar ?? 0, 0, ',', '.') ?></td>
                                        <td class="text-center">
                                            <span class="status-badge <?= $row->status_pembayaran == 'paid' ? 'badge-paid' : 'badge-pending' ?>">
                                                <?= $row->status_pembayaran == 'paid' ? 'LUNAS' : 'PIUTANG' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($row->metode): ?>
                                                    <div class="fw-bold"><?= strtoupper($row->metode) ?></div>
                                                    <small class="text-muted"><?= $row->referensi ?: '-' ?></small>
                                            <?php else: ?>
                                                    <span class="text-muted italic">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small class="text-muted d-inline-block" style="min-width: 120px;"><?= $row->keterangan ?: '-' ?></small>
                                        </td>
                                    </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Navigation -->
        <h5 class="fw-bold mb-3 mt-4 text-dark no-print"><i class="fas fa-directions text-primary me-2"></i>NAVIGASI LAPORAN</h5>
        <div class="row g-4 mb-5 no-print">
            <div class="col-6 col-md-3">
                <a href="<?= base_url('laporan/dashboard_terpadu?perusahaan_id=' . $current_perusahaan_id) ?>" class="nav-card">
                    <div class="nav-icon"><i class="fas fa-tachometer-alt"></i></div>
                    <div class="nav-title">Dashboard Utama</div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="<?= base_url('laporan/pemasukan_bulanan?perusahaan_id=' . $current_perusahaan_id) ?>" class="nav-card">
                    <div class="nav-icon"><i class="fas fa-coins"></i></div>
                    <div class="nav-title">Pemasukan Bulanan</div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="<?= base_url('laporan/index?perusahaan_id=' . $current_perusahaan_id) ?>" class="nav-card">
                    <div class="nav-icon"><i class="fas fa-home"></i></div>
                    <div class="nav-title">Semua Laporan</div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="<?= base_url('laporan/pemasukan_harian?perusahaan_id=' . $current_perusahaan_id) ?>" class="nav-card">
                    <div class="nav-icon"><i class="fas fa-hand-holding-usd"></i></div>
                    <div class="nav-title">Pemasukan Harian</div>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        const table = $('#tableManual').DataTable({
            pageLength: 25,
            order: [[1, 'desc']],
            language: { url: '<?= base_url('assets/js/datatables-id.json') ?>' },
            dom: 'rtip'
        });

        $('#customSearch').on('keyup', function () {
            table.search(this.value).draw();
        });
    });
</script>