<?php
// application/views/admin/warehouse/report_stock.php
?>

<!-- PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - ACCUMULATED STOCK REPORT -->
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #1a1c23 0%, #2c3e50 100%);
        --success-gradient: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
        --info-gradient: linear-gradient(135deg, #4895ef 0%, #4cc9f0 100%);
        --slate-soft: #f8fafc;
        --slate-muted: #64748b;
        --slate-dark: #1e293b;
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    /* Standard Header */
    .warehouse-header-section {
        background: var(--primary-gradient);
        color: white;
        padding: 1.5rem 0 3.25rem 0;
        margin-bottom: 0;
        border-radius: 0 0 24px 24px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        position: relative;
        overflow: hidden;
    }

    .header-content {
        position: relative;
        z-index: 10;
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .header-icon {
        width: 56px;
        height: 56px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        flex-shrink: 0;
    }

    .header-title {
        font-size: 1.6rem;
        font-weight: 900;
        margin: 0;
        letter-spacing: -0.5px;
    }

    /* Content */
    .report-container {
        margin-top: -2rem;
        position: relative;
        z-index: 20;
    }

    .content-card {
        background: white;
        border-radius: 28px;
        padding: 2rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid #f8fafc;
        margin-bottom: 3rem;
    }

    .table-premium thead th {
        background: #f8fafc;
        border: none;
        color: var(--slate-muted);
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 1px;
        padding: 1.25rem 1rem;
    }

    .table-premium tbody td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f8fafc;
        color: var(--slate-dark);
        font-size: 0.9rem;
    }

    .wh-col {
        background: #fdfdfd;
        font-weight: 700;
        text-align: center;
    }

    .total-col {
        background: #f1f5f9;
        font-weight: 900;
        text-align: center;
        color: #1e293b;
    }

    .btn-premium {
        border-radius: 50px;
        padding: 0.45rem 1.1rem;
        font-weight: 700;
        font-size: 0.72rem;
        letter-spacing: 0.5px;
        transition: all 0.3s;
    }
</style>

<div class="warehouse-header-section">
    <div class="container-fluid px-5">
        <div class="row align-items-center">
            <div class="col-md-9">
                <div class="header-content">
                    <div class="header-icon">
                        <i class="fa fa-file-invoice"></i>
                    </div>
                    <div>
                        <p class="mb-1 opacity-75 fw-bold text-uppercase"
                            style="letter-spacing: 2px; font-size: 0.8rem;">Warehouse Intelligence</p>
                        <h1 class="header-title">Laporan Akumulasi Stok</h1>
                    </div>
                </div>
            </div>
            <div class="col-md-3 text-end">
                <a href="<?= base_url('warehouse') ?>" class="btn btn-outline-light btn-premium rounded-pill">
                    <i class="fa fa-arrow-left me-2"></i>Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-5 report-container">
    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-900 mb-0"><i class="fa fa-table me-2 text-primary"></i> Rekapitulasi Stok Per Gudang</h5>
            <div class="d-flex gap-2">
                <button onclick="exportExcel()" class="btn btn-success btn-premium shadow-sm">
                    <i class="fa fa-file-excel me-2"></i> EXCEL
                </button>
                <button onclick="window.print()" class="btn btn-dark btn-premium shadow-sm">
                    <i class="fa fa-print me-2"></i> PRINT
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-premium" id="tableReport">
                <thead>
                    <tr>
                        <th width="50" class="text-center">NO</th>
                        <th>MATERIAL & VARIAN</th>
                        <th class="text-center">UNIT</th>
                        <?php foreach ($warehouses as $w): ?>
                            <th class="text-center">
                                <?= strtoupper($w->gudang_nama) ?>
                            </th>
                        <?php endforeach; ?>
                        <th class="text-center bg-light">TOTAL AKUMULASI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($report_data as $row): ?>
                        <tr>
                            <td class="text-center fw-bold opacity-50">
                                <?= $no++ ?>
                            </td>
                            <td class="fw-800">
                                <?= htmlspecialchars($row['name']) ?>
                            </td>
                            <td class="text-center"><span class="badge bg-light text-dark fw-bold">
                                    <?= $row['unit'] ?>
                                </span></td>
                            <?php foreach ($warehouses as $w): ?>
                                <td class="wh-col">
                                    <?= number_format($row['warehouses'][$w->gudang_id], 2) ?>
                                </td>
                            <?php endforeach; ?>
                            <td class="total-col">
                                <?= number_format($row['total_qty'], 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/table2excel@1.0.4/dist/table2excel.min.js"></script>
<script>
    $(document).ready(function () {
        $('#tableReport').DataTable({
            "pageLength": 50,
            "order": [[1, "asc"]]
        });
    });

    function exportExcel() {
        var table2excel = new Table2Excel();
        table2excel.export(document.querySelectorAll("#tableReport"), "Laporan_Stok_Akumulasi_<?= date('Y-m-d') ?>");
    }
</script>