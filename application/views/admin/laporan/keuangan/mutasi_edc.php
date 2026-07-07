<?php
/**
 * Mutasi EDC (System Record)
 * Comparing System Records (Transactions & AR) with EDC Machine Statements
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

    .card-header-clean {
        background: #fff;
        border-bottom: 1px solid #eef2f7;
        padding: 15px 20px;
    }

    @media print {
        @page {
            size: A4 landscape;
            margin: 1cm;
        }

        .no-print {
            display: none !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }

        .app-content {
            padding: 0 !important;
            margin: 0 !important;
        }

        body {
            background-color: white !important;
            font-size: 11px !important;
        }

        .table th,
        .table td {
            padding: 8px 4px !important;
            border: 1px solid #eee !important;
        }

        .print-header {
            display: block !important;
            border-bottom: 3px double #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
    }

    .print-header {
        display: none;
    }
</style>

<div class="app-content">
    <?php $this->load->view('layout_admin/header_modul'); ?>

    <!-- Print Header -->
    <div class="print-header text-center w-100">
        <h2 class="mb-0 fw-bold">
            <?= strtoupper($this->session->userdata('nama_perusahaan') ?? 'TGM ERP SYSTEM') ?>
        </h2>
        <h4 class="mb-1">LAPORAN MUTASI MESIN EDC (DEBIT/KREDIT)</h4>
        <p class="mb-0">Periode:
            <?= date('d/m/Y', strtotime($start_date)) ?> s/d
            <?= date('d/m/Y', strtotime($end_date)) ?>
        </p>
        <div class="small text-muted mt-1">Dicetak pada:
            <?= date('d/m/Y H:i') ?>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Mutasi EDC (Versi Sistem)</h1>
            <div class="no-print">
                <button onclick="window.print()" class="btn btn-light border shadow-sm">
                    <i class="fas fa-print me-1"></i> Cetak Laporan
                </button>
            </div>
        </div>

        <div class="card mb-4 no-print border-0 shadow-sm">
            <div class="card-header-clean">
                <h6 class="m-0 font-weight-bold text-primary">Filter Mutasi</h6>
            </div>
            <div class="card-body">
                <form action="" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="small text-muted fw-bold">TANGGAL MULAI</label>
                        <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="small text-muted fw-bold">TANGGAL AKHIR</label>
                        <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="small text-muted fw-bold">PILIH MESIN EDC</label>
                        <select name="edc_id" class="form-select" required>
                            <option value="">-- Pilih Mesin EDC --</option>
                            <?php foreach ($edc_list as $e): ?>
                                <option value="<?= $e->edc_id ?>" <?= ($selected_edc == $e->edc_id) ? 'selected' : '' ?>>
                                    <?= $e->edc_nama ?> (
                                    <?= $e->edc_ket ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Tampilkan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($selected_edc): ?>
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header-clean d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Transaksi EDC (System Record)</h6>
                    <div>
                        <span class="badge bg-success p-2">Total Masuk: Rp
                            <?= number_format($total_masuk, 0, ',', '.') ?>
                        </span>
                        <span class="badge bg-danger p-2">Total Keluar: Rp
                            <?= number_format($total_keluar, 0, ',', '.') ?>
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="dataTable" width="100%" cellspacing="0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Tanggal & Jam</th>
                                    <th>No. Referensi (SPK/Faktur)</th>
                                    <th>Sumber Dana</th>
                                    <th>Keterangan</th>
                                    <th class="text-end pe-4">Jumlah Masuk</th>
                                    <th class="text-end pe-4">Jumlah Keluar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($mutasi)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Tidak ada data transaksi untuk
                                            periode ini</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($mutasi as $m): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <?= date('d/m/Y H:i', strtotime($m['tanggal'] . ' ' . ($m['jam'] ?? '00:00:00'))) ?>
                                            </td>
                                            <td>
                                                <a href="#" class="text-primary fw-bold">
                                                    <?= $m['nospk'] ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?php if ($m['tipe'] == 'transaksi'): ?>
                                                    <span class="badge bg-primary">Transaksi Langsung</span>
                                                <?php elseif ($m['tipe'] == 'piutang'): ?>
                                                    <span class="badge bg-warning text-dark">Pelunasan Piutang</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Pengeluaran Operasional</span>
                                                <?php endif; ?>
                                                <div class="small text-muted mt-1">
                                                    <?= $m['konsumen'] ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?= $m['keterangan'] ?? '-' ?>
                                            </td>
                                            <?php if ($m['arus'] == 'masuk'): ?>
                                                <td class="text-end fw-bold text-success pe-4">
                                                    Rp
                                                    <?= number_format($m['jumlah'], 0, ',', '.') ?>
                                                </td>
                                                <td></td>
                                            <?php else: ?>
                                                <td></td>
                                                <td class="text-end fw-bold text-danger pe-4">
                                                    Rp
                                                    <?= number_format($m['jumlah'], 0, ',', '.') ?>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="bg-light fw-bold">
                                <tr>
                                    <td colspan="4" class="text-end">TOTAL PERIODE INI</td>
                                    <td class="text-end text-success pe-4">Rp
                                        <?= number_format($total_masuk, 0, ',', '.') ?>
                                    </td>
                                    <td class="text-end text-danger pe-4">Rp
                                        <?= number_format($total_keluar, 0, ',', '.') ?>
                                    </td>
                                </tr>
                                <tr class="bg-primary text-white">
                                    <td colspan="4" class="text-end">NET EDC BALANCE (PERIOD)</td>
                                    <td colspan="2" class="text-end pe-4">Rp
                                        <?= number_format($total_masuk - $total_keluar, 0, ',', '.') ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    $(document).ready(function () {
        if ($('#dataTable').length && !$.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable({
                "order": [[0, "asc"]],
                "pageLength": 50,
                "language": {
                    "search": "Cari Transaksi:",
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "zeroRecords": "Tidak ada data yang ditemukan",
                    "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                    "infoEmpty": "Tidak ada data tersedia",
                    "infoFiltered": "(difilter dari _MAX_ total data)"
                }
            });
        }
    });
</script>