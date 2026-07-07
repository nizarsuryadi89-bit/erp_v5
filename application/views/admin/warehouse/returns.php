<?php
// application/views/admin/warehouse/returns.php
?>

<!-- PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - PURCHASE RETURNS -->
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #1a1c23 0%, #2c3e50 100%);
        --danger-gradient: linear-gradient(135deg, #f72585 0%, #b5179e 100%);
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        --slate-dark: #1e293b;
        --slate-muted: #64748b;
    }

    /* Header */
    .warehouse-header-section {
        background: var(--primary-gradient);
        color: white;
        padding: 1.5rem 0 3.25rem 0;
        margin-bottom: 0;
        border-radius: 0 0 24px 24px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        position: relative;
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

    /* Content */
    .content-container {
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
        color: var(--slate-muted);
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.7rem;
        padding: 1.25rem 1rem;
        border: none;
    }

    .table-premium tbody td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f8fafc;
        color: var(--slate-dark);
    }

    .btn-premium {
        border-radius: 50px;
        padding: 0.45rem 1.1rem;
        font-weight: 700;
        font-size: 0.72rem;
        letter-spacing: 0.5px;
    }
</style>

<div class="warehouse-header-section">
    <div class="container-fluid px-5">
        <div class="row align-items-center">
            <div class="col-md-9">
                <div class="d-flex align-items-center gap-4">
                    <div class="header-icon">
                        <i class="fa fa-reply-all"></i>
                    </div>
                    <div>
                        <p class="mb-1 opacity-75 fw-bold text-uppercase" style="letter-spacing: 2px;">Supplier Reverse
                            Logistics</p>
                        <h1 class="mb-0 fw-900 fs-1">Retur Pembelian</h1>
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

<div class="container-fluid px-5 content-container">
    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-900 mb-0"><i class="fa fa-history me-2 text-danger"></i> Daftar Retur Ke Supplier</h5>
            <button class="btn btn-danger btn-premium shadow-sm"
                style="background: var(--danger-gradient); border: none;">
                <i class="fa fa-plus-circle me-2"></i> BUAT RETUR BARU
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-premium" id="tableReturns">
                <thead>
                    <tr>
                        <th width="150">TANGGAL</th>
                        <th>NO. RETUR</th>
                        <th>SUPPLIER</th>
                        <th class="text-end">TOTAL NILAI</th>
                        <th class="text-center">STATUS</th>
                        <th class="text-center">ADMIN</th>
                        <th class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($returns as $r): ?>
                        <tr>
                            <td class="fw-bold">
                                <?= date('d M Y', strtotime($r->return_tgl)) ?>
                            </td>
                            <td class="fw-800 text-primary">#<?= $r->no_return ?></td>
                            <td class="fw-800"><?= $r->supplier_nama ?></td>
                            <td class="text-end fw-900 text-dark">
                                Rp <?= number_format($r->return_total, 0, ',', '.') ?>
                            </td>
                            <td class="text-center">
                                <span
                                    class="badge bg-soft-success text-success px-3 py-2 rounded-pill fw-bold">COMPLETED</span>
                            </td>
                            <td class="text-center">
                                <div class="badge bg-light text-muted px-3 py-2 rounded-pill border">
                                    <i class="fa fa-user-circle me-1"></i> <?= $r->username ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-light border p-2 rounded-3"><i class="fa fa-eye"></i></button>
                                <button class="btn btn-sm btn-light border p-2 rounded-3 text-danger"><i
                                        class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#tableReturns').DataTable();
    });
</script>