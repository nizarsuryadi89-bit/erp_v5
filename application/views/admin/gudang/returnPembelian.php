<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    /* ==========================================================================
    PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - RETURN LOG
    ========================================================================== */
    :root {
        --primary-gradient: linear-gradient(135deg, #1a1c23 0%, #2c3e50 100%);
        --success-gradient: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
        --info-gradient: linear-gradient(135deg, #4895ef 0%, #4cc9f0 100%);
        --warning-gradient: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);
        --danger-gradient: linear-gradient(135deg, #f72585 0%, #b5179e 100%);
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .report-section-card {
        background: white;
        border-radius: 20px;
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
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Stats v2 */
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
        font-size: 1.35rem;
        font-weight: 900;
        color: #1a1c23;
        line-height: 1;
    }

    /* Table Styles */
    .premium-table thead th {
        background: #f8fafc;
        border: none;
        color: #475569;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.65rem;
        letter-spacing: 1px;
        padding: 1rem 1.5rem;
    }

    .premium-table tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }

    .faktur-pill {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 800;
        font-size: 0.8rem;
        background: #fff5f5;
        color: #f72585;
        padding: 0.3rem 0.6rem;
        border-radius: 6px;
        border: 1px solid #ffe3e3;
    }

    .btn-circle-action {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        border: 1px solid #e2e8f0;
        background: white;
        color: #475569;
    }

    .btn-circle-action:hover {
        background: #1a1c23;
        color: white;
        border-color: #1a1c23;
        transform: translateY(-2px);
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
</style>

<div class="app-content mt-2">
    <div class="container-fluid">
        <?php
        $tampilData = isset($tampilData) && is_array($tampilData) ? $tampilData : [];
        $total_return = count($tampilData);
        $total_nilai = array_sum(array_column($tampilData, 'return_total'));
        $bulan_ini = count(array_filter($tampilData, function ($v) {
            return date('Y-m', strtotime($v->return_tgl)) == date('Y-m'); }));
        $hari_ini = count(array_filter($tampilData, function ($v) {
            return date('Y-m-d', strtotime($v->return_tgl)) == date('Y-m-d'); }));
        ?>

        <!-- Quick Stats -->
        <div class="row g-3 mb-4 animate-up">
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm h-100">
                    <div class="stat-v2-icon text-white" style="background: var(--primary-gradient)"><i
                            class="fas fa-undo-alt"></i></div>
                    <div>
                        <div class="stat-v2-label">Total Return</div>
                        <div class="stat-v2-value"><?= number_format($total_return) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm h-100">
                    <div class="stat-v2-icon text-white" style="background: var(--danger-gradient)"><i
                            class="fas fa-coins"></i></div>
                    <div>
                        <div class="stat-v2-label">Nilai Return</div>
                        <div class="stat-v2-value" style="font-size: 1.1rem;">Rp
                            <?= number_format($total_nilai, 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm h-100">
                    <div class="stat-v2-icon text-white" style="background: var(--info-gradient)"><i
                            class="fas fa-calendar-alt"></i></div>
                    <div>
                        <div class="stat-v2-label">Bulan Ini</div>
                        <div class="stat-v2-value"><?= number_format($bulan_ini) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm h-100">
                    <div class="stat-v2-icon text-white" style="background: var(--warning-gradient)"><i
                            class="fas fa-history"></i></div>
                    <div>
                        <div class="stat-v2-label">Hari Ini</div>
                        <div class="stat-v2-value"><?= number_format($hari_ini) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="report-section-card animate-up" style="animation-delay: 0.1s">
            <div class="section-header">
                <h5><i class="fas fa-sync"></i> Log Return Pembelian (Pengembalian Ke Supplier)</h5>
                <div class="d-flex gap-2">
                    <a href="<?= base_url('warehouse/addReturnPembelian') ?>"
                        class="btn btn-sm btn-light rounded-pill px-3 fw-bold">
                        <i class="fas fa-plus me-1"></i> Tambah Return
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table premium-table table-hover mb-0" id="datatable-details">
                    <thead>
                        <tr>
                            <th width="40" class="text-center">No</th>
                            <th>No Return / Ref</th>
                            <th>Waktu Transaksi</th>
                            <th>Partner (Supplier)</th>
                            <th class="text-end">Total Nilai</th>
                            <th>Keterangan</th>
                            <th width="120" class="text-center">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($tampilData as $dt): ?>
                            <tr>
                                <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                                <td>
                                    <span class="faktur-pill"><?= $dt->no_return ?></span>
                                    <div class="small text-muted mt-1">Ref: #<?= $dt->return_id ?></div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= date('d M Y', strtotime($dt->return_tgl)) ?></div>
                                    <div class="small text-muted"><?= date('H:i', strtotime($dt->return_tgl)) ?> WIB</div>
                                </td>
                                <td>
                                    <div class="fw-800 text-dark"><?= $dt->supplier_nama ?></div>
                                    <div class="small text-danger"><i class="fas fa-exchange-alt me-1"></i> Outgoing Return
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="fw-900 text-danger">Rp <?= number_format($dt->return_total, 0, ',', '.') ?>
                                    </div>
                                </td>
                                <td class="small text-muted"><?= $dt->keterangan ?: '-' ?></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="<?= base_url('warehouse/detailReturnPembelian/' . $dt->return_id) ?>"
                                            class="btn-circle-action" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('warehouse/cetakReturnPembelian/' . $dt->return_id) ?>"
                                            target="_blank" class="btn-circle-action" title="Cetak">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <button class="btn-circle-action btn-circle-delete btn-delete"
                                            data-url="<?= base_url('warehouse/deleteReturnPembelian/' . $dt->return_id) ?>"
                                            title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        if ($.fn.DataTable) {
            $('#datatable-details').DataTable({
                "language": {
                    "sSearch": "Cari Return:",
                    "oPaginate": { "sNext": "<i class='fas fa-chevron-right'></i>", "sPrevious": "<i class='fas fa-chevron-left'></i>" }
                },
                "pageLength": 25,
                "ordering": false,
                "dom": "<'row px-4 py-3'<'col-md-6'l><'col-md-6 d-flex justify-content-end text-end'f>>t<'row px-4 py-3'<'col-md-6'i><'col-md-6 d-flex justify-content-end text-end'p>>"
            });
        }

        $(document).on('click', '.btn-delete', function (e) {
            const url = $(this).data('url');
            Swal.fire({
                title: 'Hapus Log Return?',
                text: 'Data penghapusan ini akan mengembalikan histori stok!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f72585',
                confirmButtonText: 'Ya, Hapus'
            }).then((result) => { if (result.isConfirmed) window.location.href = url; });
        });
    });
</script>