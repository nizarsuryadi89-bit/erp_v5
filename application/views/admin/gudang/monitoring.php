<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    /* ==========================================================================
    PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - MONITORING STOK
    ========================================================================== */
    :root {
        --primary-gradient: linear-gradient(135deg, #1a1c23 0%, #2c3e50 100%);
        --success-gradient: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
        --info-gradient: linear-gradient(135deg, #4895ef 0%, #4cc9f0 100%);
        --warning-gradient: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);
        --danger-gradient: linear-gradient(135deg, #f72585 0%, #b5179e 100%);
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    /* Stats v2 Grid */
    .summary-stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .stat-v2-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .stat-v2-label {
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.6rem;
        letter-spacing: 1px;
    }

    .stat-v2-value {
        font-size: 1.5rem;
        font-weight: 900;
        color: #1a1c23;
        line-height: 1;
    }

    /* Main Table & Layout */
    .report-section-card {
        background: white;
        border-radius: 24px;
        border: none;
        box-shadow: var(--premium-shadow);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .section-header {
        background: var(--primary-gradient);
        padding: 1.25rem 1.75rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
    }

    .section-header h5 {
        margin: 0;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .premium-table thead th {
        background: #f8fafc;
        border: none;
        color: #475569;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.65rem;
        letter-spacing: 1px;
        padding: 1.25rem 1.5rem;
    }

    .premium-table tbody td {
        padding: 1.25rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }

    /* Type Badges */
    .type-pill {
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-weight: 800;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Animation */
    @keyframes slideUpFade {
        from {
            opacity: 0;
            transform: translateY(15px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-up {
        animation: slideUpFade 0.5s ease-out forwards;
    }

    /* Mobile view specific overrides */
    @media (max-width: 767.98px) {
        .stat-v2-value {
            font-size: 1.2rem;
        }

        .premium-table tbody td {
            padding: 1rem;
        }
    }
</style>

<div class="app-content mt-2">
    <div class="container-fluid">
        <?php
        $total_qty_masuk = 0;
        $total_qty_keluar = 0;
        $total_tr = count($monitoring);
        $today_tr = 0;
        foreach ($monitoring as $row) {
            $total_qty_masuk += (float) ($row->qty_masuk ?? 0);
            $total_qty_keluar += (float) ($row->qty_keluar ?? 0);
            if (date('Y-m-d', strtotime($row->tanggal)) == date('Y-m-d'))
                $today_tr++;
        }
        ?>

        <!-- Statistics Grid -->
        <div class="row g-3 mb-4 animate-up">
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm h-100">
                    <div class="stat-v2-icon text-white" style="background: var(--primary-gradient)"><i
                            class="fas fa-microchip"></i></div>
                    <div>
                        <div class="stat-v2-label">Total Volume TR</div>
                        <div class="stat-v2-value"><?= number_format($total_tr) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm h-100">
                    <div class="stat-v2-icon text-white" style="background: var(--success-gradient)"><i
                            class="fas fa-file-invoice"></i></div>
                    <div>
                        <div class="stat-v2-label">TR Hari Ini</div>
                        <div class="stat-v2-value"><?= number_format($today_tr) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm h-100">
                    <div class="stat-v2-icon text-white" style="background: var(--info-gradient)"><i
                            class="fas fa-arrow-down"></i></div>
                    <div>
                        <div class="stat-v2-label">In (Masuk)</div>
                        <div class="stat-v2-value text-success"><?= number_format($total_qty_masuk) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm h-100">
                    <div class="stat-v2-icon text-white" style="background: var(--danger-gradient)"><i
                            class="fas fa-arrow-up"></i></div>
                    <div>
                        <div class="stat-v2-label">Out (Keluar)</div>
                        <div class="stat-v2-value text-danger"><?= number_format($total_qty_keluar) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Monitoring Card -->
        <div class="report-section-card animate-up" style="animation-delay: 0.1s">
            <div class="section-header">
                <h5><i class="fas fa-stream"></i> Monitoring Log Persediaan (Real-time)</h5>
                <div class="d-flex gap-2">
                    <button onclick="exportToExcel()" class="btn btn-sm btn-light rounded-pill px-3 fw-bold"><i
                            class="fas fa-file-excel me-1"></i> Excel</button>
                    <button onclick="printTable()" class="btn btn-sm btn-light rounded-pill px-3 fw-bold"><i
                            class="fas fa-print me-1"></i> Print</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table premium-table table-hover mb-0" id="datatable-monitoring">
                    <thead>
                        <tr>
                            <th width="40" class="text-center">No</th>
                            <th>Timestamp</th>
                            <th>Material / Barang</th>
                            <th class="text-center">Tipe TR</th>
                            <th class="text-center">Masuk (+)</th>
                            <th class="text-center">Keluar (-)</th>
                            <th>Validator</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($monitoring as $row):
                            $badge = 'bg-secondary';
                            if (strpos(strtolower($row->tipe), 'pembelian') !== false)
                                $badge = 'bg-success';
                            elseif (strpos(strtolower($row->tipe), 'pengambilan') !== false)
                                $badge = 'bg-danger';
                            elseif (strpos(strtolower($row->tipe), 'return') !== false)
                                $badge = 'bg-warning text-dark';
                            ?>
                            <tr>
                                <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                                <td>
                                    <div class="fw-800 text-dark"><?= date('d/m/y', strtotime($row->tanggal)) ?></div>
                                    <div class="small text-muted font-monospace"><?= date('H:i', strtotime($row->waktu)) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-800 text-dark"><?= htmlspecialchars($row->varian ?? 'N/A') ?></div>
                                    <div class="small text-muted">BC: <?= $row->barcode ?: '-' ?></div>
                                </td>
                                <td class="text-center">
                                    <span class="type-pill <?= $badge ?> rounded-pill">
                                        <?= htmlspecialchars($row->tipe) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($row->qty_masuk > 0): ?>
                                        <div class="fw-900 text-success">+<?= number_format($row->qty_masuk) ?></div>
                                        <div class="small text-muted"><?= $row->satuan_nama ?></div>
                                    <?php else: ?>-<?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($row->qty_keluar > 0): ?>
                                        <div class="fw-900 text-danger">-<?= number_format($row->qty_keluar) ?></div>
                                        <div class="small text-muted"><?= $row->satuan_nama ?></div>
                                    <?php else: ?>-<?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="badge bg-primary-subtle text-primary rounded-pill px-2"
                                            style="font-size: 0.75rem;">
                                            <i
                                                class="fas fa-user-check me-1"></i><?= htmlspecialchars($row->nama_karyawan ?: 'System') ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    $(document).ready(function () {
        if ($.fn.DataTable) {
            $('#datatable-monitoring').DataTable({
                "language": { "sSearch": "Cari Log:", "oPaginate": { "sNext": "<i class='fas fa-chevron-right'></i>", "sPrevious": "<i class='fas fa-chevron-left'></i>" } },
                "pageLength": 50, "order": [[1, "desc"]],
                "dom": "<'row px-4 py-3'<'col-md-6'l><'col-md-6 d-flex justify-content-end text-end'f>>t<'row px-4 py-3'<'col-md-6'i><'col-md-6 d-flex justify-content-end text-end'p>>"
            });
        }
        window.exportToExcel = function () {
            var wb = XLSX.utils.table_to_book(document.getElementById('datatable-monitoring'), { sheet: "Log Monitoring" });
            XLSX.writeFile(wb, 'Log_Gudang_' + new Date().toISOString().slice(0, 10) + '.xlsx');
        };
        window.printTable = function () { window.print(); };
    });
</script>