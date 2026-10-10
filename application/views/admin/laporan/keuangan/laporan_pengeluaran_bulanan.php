<?php
/**
 * Laporan Pengeluaran Bulanan - Adapted to match Pemasukan Harian concept
 */
$current_perusahaan_id = $this->input->get('perusahaan_id') ?: $this->session->userdata('perusahaan_id');
?>

<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">
<style>
    .stat-card-modern {
        background: #fff;
        border: 1px solid #eef2f7;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .stat-card-modern .label {
        font-size: 13px;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .stat-card-modern .value {
        font-size: 24px;
        font-weight: 700;
        color: #343a40;
        margin-bottom: 0;
    }

    .stat-card-modern .icon {
        position: absolute;
        right: 15px;
        top: 20px;
        font-size: 32px;
        opacity: 0.1;
    }

    .stat-primary { border-bottom: 3px solid #4e73df; }
    .stat-success { border-bottom: 3px solid #1cc88a; }
    .stat-warning { border-bottom: 3px solid #f6c23e; }
    .stat-danger { border-bottom: 3px solid #e74a3b; }
    .stat-info { border-bottom: 3px solid #36b9cc; }

    .table-modern th {
        background-color: #f8f9fc;
        color: #4e73df;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        border-bottom: 2px solid #e3e6f0;
    }

    .table-modern td {
        vertical-align: middle;
        color: #5a5c69;
    }

    .card-header-clean {
        background: white;
        border-bottom: 1px solid #e3e6f0;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header-clean h6 {
        margin: 0;
        font-weight: 700;
        color: #4e73df;
    }

    @media print {
        .no-print { display: none !important; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; }
        .bg-gradient-primary { background: #eee !important; color: #000 !important; }
    }
</style>

<div class="app-content">
    <?php $this->load->view('layout_admin/header_modul'); ?>

    <div class="container-fluid">
        <!-- Date Filter -->
        <div class="card mb-4 no-print border-0 shadow-sm">
            <div class="card-body p-3">
                <form action="<?= base_url('laporan/laporan_pengeluaran_bulanan') ?>" method="GET" class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <label class="small text-muted fw-bold">PERIODE BULAN</label>
                        <input type="month" class="form-control" name="bulan" value="<?= $bulanini ?>" required onchange="this.form.submit()">
                    </div>
                    <div class="col-md-8 text-end">
                        <label class="d-block small text-muted fw-bold">&nbsp;</label>
                        <button type="button" onclick="window.print()" class="btn btn-secondary px-4">
                            <i class="fas fa-print me-1"></i> Cetak
                        </button>
                        <!-- Assuming export function exists or using client-side export -->
                        <!-- If server-side export exists, link it here. For now validation: just print. -->
                    </div>
                </form>
            </div>
        </div>

        <!-- STATS CARDS -->
        <div class="row g-3 mb-4">
            <!-- 1. Total Pengeluaran -->
            <div class="col-xl-3 col-md-6">
                <div class="stat-card-modern stat-danger">
                    <div class="label text-danger">Total Pengeluaran</div>
                    <div class="value">Rp <?= number_format($total_pengeluaran ?? 0, 0, ',', '.') ?></div>
                    <div class="small text-muted mt-2">Periode: <?= date('F Y', strtotime($bulanini . '-01')) ?></div>
                    <i class="fas fa-money-bill-wave icon text-danger"></i>
                </div>
            </div>

            <!-- 2. Jumlah Transaksi -->
            <div class="col-xl-3 col-md-6">
                <div class="stat-card-modern stat-primary">
                    <div class="label text-primary">Jumlah Transaksi</div>
                    <div class="value"><?= count($pengeluaran_list ?? []) ?></div>
                    <div class="small text-muted mt-2">Total item pengeluaran</div>
                    <i class="fas fa-list icon text-primary"></i>
                </div>
            </div>

            <!-- 3. Rata-rata -->
            <div class="col-xl-3 col-md-6">
                <div class="stat-card-modern stat-info">
                    <div class="label text-info">Rata-rata Transaksi</div>
                    <?php
                    $avg = (count($pengeluaran_list) > 0) ? ($total_pengeluaran / count($pengeluaran_list)) : 0;
                    ?>
                    <div class="value">Rp <?= number_format($avg, 0, ',', '.') ?></div>
                    <div class="small text-muted mt-2">Per transaksi</div>
                    <i class="fas fa-chart-pie icon text-info"></i>
                </div>
            </div>

            <!-- 4. Cash Flow Info (Static or calc) -->
            <div class="col-xl-3 col-md-6">
                <div class="stat-card-modern stat-warning">
                    <div class="label text-warning">Periode Laporan</div>
                    <div class="value h5 pt-1"><?= date('F Y', strtotime($bulanini . '-01')) ?></div>
                    <div class="small text-muted mt-1"><?= $perusahaan ?? 'All Branches' ?></div>
                    <i class="fas fa-calendar icon text-warning"></i>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- LEFT COLUMN: SUMMARY BREAKDOWN -->
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 mb-4 h-100">
                    <div class="card-header-clean bg-gradient-danger text-white">
                        <h6 class="text-white"><i class="fas fa-chart-pie me-2"></i>Rekapitulasi Pengeluaran</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Metode Pembayaran</th>
                                        <th class="text-end pe-4">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($metode_pengeluaran)): ?>
                                            <?php foreach ($metode_pengeluaran as $metode => $total): ?>
                                                    <tr>
                                                        <td class="ps-4">
                                                            <div class="fw-bold text-dark"><?= ucfirst($metode) ?></div>
                                                            <div class="small text-muted"><?= number_format(($total / ($total_pengeluaran > 0 ? $total_pengeluaran : 1)) * 100, 1) ?>% dari total</div>
                                                        </td>
                                                        <td class="text-end pe-4 text-dark fw-bold">
                                                            Rp <?= number_format($total, 0, ',', '.') ?>
                                                        </td>
                                                    </tr>
                                            <?php endforeach; ?>
                                    <?php else: ?>
                                            <tr><td colspan="2" class="text-center py-3 text-muted">Data tidak tersedia</td></tr>
                                    <?php endif; ?>
                                    
                                    <tr class="table-primary border-top border-primary fw-bold">
                                        <td class="ps-4 text-primary">TOTAL</td>
                                        <td class="text-end pe-4 text-primary">
                                            Rp <?= number_format($total_pengeluaran ?? 0, 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: TRANSACTION LIST -->
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header-clean">
                        <h6><i class="fas fa-list-ul me-2"></i>Daftar Pengeluaran Bulanan</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-modern table-hover mb-0" id="datatable-pengeluaran">
                                <thead>
                                    <tr>
                                        <th class="ps-3">Tanggal</th>
                                        <th>Keterangan Reference</th>
                                        <th>Metode</th>
                                        <th class="text-end pe-3">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($pengeluaran_list)): ?>
                                            <?php foreach ($pengeluaran_list as $item): ?>
                                                    <tr>
                                                        <td class="ps-3">
                                                            <div class="fw-bold text-dark"><?= date('d/m/Y', strtotime($item->pengeluaran_tgl)) ?></div>
                                                            <div class="small text-muted"><?= date('H:i', strtotime($item->pengeluaran_tgl)) ?></div>
                                                        </td>
                                                        <td>
                                                            <div class="fw-bold text-primary"><?= $item->kategori_nama ?? 'Umum' ?></div>
                                                            <div class="small text-dark"><?= $item->pengeluaran_ket ?? '-' ?></div>
                                                            <?php if (!empty($item->kategori_ket)): ?>
                                                                    <div class="small text-muted fst-italic"><?= $item->kategori_ket ?></div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-light text-dark border">
                                                                <?= ucfirst($item->metode_bayar ?? 'tunai') ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-end pe-3 fw-bold text-danger">
                                                            Rp <?= number_format($item->pengeluaran_jumlah, 0, ',', '.') ?>
                                                        </td>
                                                    </tr>
                                            <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if (empty($pengeluaran_list)): ?>
                                <div class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fs-1 mb-3 opacity-25"></i>
                                    <p>Belum ada pengeluaran pada periode ini.</p>
                                </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#datatable-pengeluaran').DataTable({
            responsive: true,
            language: { url: '<?= base_url('assets/js/datatables-id.json') ?>' },
            pageLength: 15,
            order: [[0, 'desc']], // Sort by Date
            dom: "<'row'<'col-sm-6'f><'col-sm-6 text-end'B>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
                { extend: 'excel', text: '<i class="fas fa-file-excel me-1"></i> Excel', className: 'btn btn-success btn-sm' },
                { extend: 'print', text: '<i class="fas fa-print me-1"></i> Print', className: 'btn btn-secondary btn-sm' }
            ]
        });
    });
</script>