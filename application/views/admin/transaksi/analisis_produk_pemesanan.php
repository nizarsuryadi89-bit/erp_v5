<style>
    .product-stat-card {
        transition: transform 0.2s;
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .product-stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.12);
    }

    .stat-icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .table-custom thead th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 600;
        border-bottom: 2px solid #e9ecef;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    .rank-badge {
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: bold;
        color: white;
    }

    .rank-1 {
        background: linear-gradient(135deg, #FFD700, #FDB931);
        box-shadow: 0 2px 5px rgba(253, 185, 49, 0.4);
    }

    .rank-2 {
        background: linear-gradient(135deg, #E0E0E0, #BDBDBD);
        box-shadow: 0 2px 5px rgba(189, 189, 189, 0.4);
    }

    .rank-3 {
        background: linear-gradient(135deg, #CD7F32, #A0522D);
        box-shadow: 0 2px 5px rgba(160, 82, 45, 0.4);
    }
</style>

<div class="app-content">
    <!-- Header -->
    <?php
    $data['title'] = isset($title) ? $title : 'Analisis Produk';
    $data['perusahaan'] = isset($perusahaan) ? $perusahaan : 'TGM ERP';
    $this->load->view('layout_admin/header_modul', $data);
    ?>

    <!-- Top Stats (Most Ordered Highlight) -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h6 class="text-muted fw-bold mb-3">
                <i class="fas fa-trophy text-warning me-2"></i>Produk Terlaris Bulan Ini
            </h6>
        </div>
        <?php if (!empty($most_ordered)): ?>
            <?php
            $colors = ['primary', 'success', 'info', 'warning'];
            $icons = ['box', 'check-circle', 'star', 'fire'];
            foreach (array_slice($most_ordered, 0, 4) as $index => $item):
                $color = $colors[$index % 4];
                $icon = $icons[$index % 4];
                ?>
                <div class="col-xl-3 col-md-6">
                    <div class="product-stat-card bg-white h-100 p-3">
                        <div class="d-flex align-items-center mb-3">
                            <div class="stat-icon-circle bg-<?= $color ?> bg-opacity-10 text-<?= $color ?> me-3">
                                <span class="rank-badge rank-<?= $index + 1 ?> position-absolute top-0 start-0 translate-middle"
                                    style="font-size: 0.7rem; width: 20px; height: 20px; <?= $index > 2 ? 'display:none;' : '' ?>">
                                    <?= $index + 1 ?>
                                </span>
                                <i class="fas fa-<?= $icon ?>"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark text-truncate" style="max-width: 150px;"
                                    title="<?= $item->product_nama ?>">
                                    <?= $item->product_nama ?>
                                </h6>
                                <small class="text-muted">Terjual: <span class="fw-bold text-<?= $color ?>">
                                        <?= $item->total_ordered ?>
                                    </span> Pcs</small>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-end border-top pt-2">
                            <small class="text-muted">Potensi Revenue</small>
                            <span class="fw-bold text-dark">Rp
                                <?= number_format($item->total_potential_revenue, 0, ',', '.') ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-light border text-center text-muted">Belum ada data penjualan bulan ini.</div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Detailed Analysis Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-primary"><i class="fas fa-chart-pie me-2"></i>Detail Analisis Produk
                        (Paid vs
                        Unpaid)</h6>
                    <div class="card-actions">
                        <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print me-1"></i> Cetak Laporan
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-custom align-middle" id="dataTableAnalisis"
                            width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="text-center bg-light" width="5%">No</th>
                                    <th rowspan="2" class="bg-light">Produk</th>
                                    <th rowspan="2" class="text-end bg-light">Harga</th>
                                    <th colspan="2" class="text-center text-success bg-success bg-opacity-10">Lunas
                                        (Paid)</th>
                                    <th colspan="2" class="text-center text-danger bg-danger bg-opacity-10">Belum Bayar
                                        (Unpaid)
                                    </th>
                                    <th colspan="2" class="text-center text-primary bg-primary bg-opacity-10">Total
                                        Keseluruhan
                                    </th>
                                </tr>
                                <tr>
                                    <!-- Paid -->
                                    <th class="text-center text-success bg-success bg-opacity-10"
                                        style="font-size: 0.7rem; border-top: none;">Qty</th>
                                    <th class="text-end text-success bg-success bg-opacity-10"
                                        style="font-size: 0.7rem; border-top: none;">Revenue</th>

                                    <!-- Unpaid -->
                                    <th class="text-center text-danger bg-danger bg-opacity-10"
                                        style="font-size: 0.7rem; border-top: none;">Qty</th>
                                    <th class="text-end text-danger bg-danger bg-opacity-10"
                                        style="font-size: 0.7rem; border-top: none;">Revenue</th>

                                    <!-- Total -->
                                    <th class="text-center text-primary bg-primary bg-opacity-10"
                                        style="font-size: 0.7rem; border-top: none;">Total Items</th>
                                    <th class="text-end text-primary bg-primary bg-opacity-10"
                                        style="font-size: 0.7rem; border-top: none;">Total Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                if (!empty($product_summary)):
                                    foreach ($product_summary as $row):
                                        ?>
                                        <tr>
                                            <td class="text-center text-muted">
                                                <?= $no++ ?>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark">
                                                    <?= $row->product_nama ?>
                                                </div>
                                                <small class="text-muted text-monospace">
                                                    <?= $row->product_kode ?>
                                                </small>
                                            </td>
                                            <td class="text-end">Rp
                                                <?= number_format($row->harga_1, 0, ',', '.') ?>
                                            </td>

                                            <!-- Paid Stats -->
                                            <td
                                                class="text-center fw-bold text-success bg-success bg-opacity-10 border-success-subtle">
                                                <?= $row->total_sold_paid ?>
                                            </td>
                                            <td
                                                class="text-end text-success font-monospace small bg-success bg-opacity-10 border-success-subtle">
                                                Rp
                                                <?= number_format($row->total_revenue_paid, 0, ',', '.') ?>
                                            </td>

                                            <!-- Unpaid Stats -->
                                            <td
                                                class="text-center fw-bold text-danger bg-danger bg-opacity-10 border-danger-subtle">
                                                <?= $row->total_sold_unpaid ?>
                                            </td>
                                            <td
                                                class="text-end text-danger font-monospace small bg-danger bg-opacity-10 border-danger-subtle">
                                                Rp
                                                <?= number_format($row->total_revenue_unpaid, 0, ',', '.') ?>
                                            </td>

                                            <!-- Total Stats -->
                                            <td
                                                class="text-center fw-bold text-primary bg-primary bg-opacity-10 border-primary-subtle">
                                                <?= $row->total_ordered ?>
                                            </td>
                                            <td
                                                class="text-end fw-bold text-primary font-monospace bg-primary bg-opacity-10 border-primary-subtle">
                                                Rp
                                                <?= number_format($row->total_revenue, 0, ',', '.') ?>
                                            </td>
                                        </tr>
                                        <?php
                                    endforeach;
                                else:
                                    ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                            <p class="mb-0">Tidak ada data transaksi untuk periode ini.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Page specific script -->
<script>
    $(document).ready(function () {
        $('#dataTableAnalisis').DataTable({
            "order": [[8, "desc"]], // Sort by Total Revenue (column index 8)
            "pageLength": 25,
            dom: 'Bfrtip',
            buttons: [
                { extend: 'copy', className: 'btn btn-outline-secondary btn-sm' },
                { extend: 'csv', className: 'btn btn-outline-primary btn-sm' },
                { extend: 'excel', className: 'btn btn-outline-success btn-sm' },
                { extend: 'print', className: 'btn btn-outline-info btn-sm' }
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari Produk...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: '<i class="fas fa-chevron-right"></i>',
                    previous: '<i class="fas fa-chevron-left"></i>'
                }
            }
        });
    });
</script>