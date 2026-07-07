<?php $this->load->view('layout_admin/header_modul'); ?>

<link rel="stylesheet" href="<?= base_url('assets/css/tgm-gudang-masuk.css') ?>">

<div class="app-content mt-2">
    <div class="container-fluid">
        <!-- Quick Stats -->
        <div class="row g-3 mb-4 animate-up">
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm h-100">
                    <div class="stat-v2-icon text-white" style="background: var(--primary-gradient)"><i
                            class="fas fa-file-invoice"></i></div>
                    <div>
                        <div class="stat-v2-label">Total Faktur</div>
                        <div class="stat-v2-value"><?= count($tampilData) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm h-100">
                    <div class="stat-v2-icon text-white" style="background: var(--success-gradient)"><i
                            class="fas fa-coins"></i></div>
                    <?php
                    $grand_total = 0;
                    foreach ($tampilData as $d) {
                        $total_row = $this->db->select('SUM(qty * harga_beli) as total')
                            ->where('pembelian_id', $d->pembelian_id)
                            ->get('tbl_pembelian_detail')->row()->total;
                        $grand_total += $total_row;
                    }
                    ?>
                    <div>
                        <div class="stat-v2-label">Grand Total</div>
                        <div class="stat-v2-value" style="font-size: 1.1rem;">Rp
                            <?= number_format($grand_total, 0, ',', '.') ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm h-100">
                    <div class="stat-v2-icon text-white" style="background: var(--warning-gradient)"><i
                            class="fas fa-truck"></i></div>
                    <div>
                        <div class="stat-v2-label">Supplier Aktif</div>
                        <div class="stat-v2-value"><?= count(array_unique(array_column($tampilData, 'supplier_id'))) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm h-100">
                    <div class="stat-v2-icon text-white" style="background: var(--danger-gradient)"><i
                            class="fas fa-undo"></i></div>
                    <div>
                        <div class="stat-v2-label">Ada Return</div>
                        <div class="stat-v2-value text-danger">
                            <?= count(array_filter($tampilData, function ($v) {
                                return $v->return_count > 0;
                            })) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="report-section-card animate-up" style="animation-delay: 0.1s">
            <div class="section-header">
                <h5><i class="fas fa-shopping-cart"></i> Log Barang Masuk (Pembelian)</h5>
                <div class="d-flex gap-2">
                    <a href="<?= base_url('warehouse/purchase') ?>"
                        class="btn-premium btn-premium-primary text-white text-decoration-none">
                        <i class="fas fa-plus-circle me-1"></i> Transaksi Pembelian Baru
                    </a>
                    <button id="btnExport" class="btn btn-sm btn-outline-light rounded-pill px-3 fw-bold">
                        <i class="fas fa-file-excel me-1"></i> Export
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table premium-table table-hover mb-0" id="datatable-details">
                    <thead>
                        <tr>
                            <th width="40" class="text-center">No</th>
                            <th>Faktur / Referensi</th>
                            <th>Waktu Transaksi</th>
                            <th>Partner (Supplier)</th>
                            <th class="text-end">Nominal</th>
                            <th class="text-center">Kondisi</th>
                            <th width="120" class="text-center">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($tampilData as $dt):
                            $nilai_pembelian = $this->db->select('SUM(qty * harga_beli) as total')
                                ->where('pembelian_id', $dt->pembelian_id)
                                ->get('tbl_pembelian_detail')->row()->total ?: 0;
                            ?>
                            <tr>
                                <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                                <td>
                                    <span class="faktur-pill"><?= $dt->no_faktur ?></span>
                                    <div class="small text-muted mt-1">ID: #<?= $dt->pembelian_id ?></div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= date('d M Y', strtotime($dt->pembelian_tgl)) ?></div>
                                    <div class="small text-muted"><?= date('H:i', strtotime($dt->pembelian_tgl)) ?> WIB
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-800 text-dark"><?= $dt->supplier_nama ?></div>
                                    <div class="small text-primary"><i class="fas fa-tag me-1"></i> Partner Terdaftar</div>
                                </td>
                                <td class="text-end">
                                    <div class="fw-900 text-dark">Rp <?= number_format($nilai_pembelian, 0, ',', '.') ?>
                                    </div>
                                    <div class="small text-muted">
                                        <?= $this->db->where('pembelian_id', $dt->pembelian_id)->get('tbl_pembelian_detail')->num_rows() ?>
                                        Items
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge-premium <?= $dt->return_count > 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' ?>">
                                        <?= $dt->return_count > 0 ? 'ADA RETURN' : 'NORMAL' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button onclick="viewDetail(<?= $dt->pembelian_id ?>)" class="btn-circle-action"
                                            title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="<?= base_url('warehouse/cetakPembelian/' . $dt->pembelian_id) ?>"
                                            target="_blank" class="btn-circle-action" title="Cetak">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <button class="btn-circle-action btn-circle-delete btn-delete"
                                            data-url="<?= base_url('warehouse/deletePembelian/' . $dt->pembelian_id) ?>"
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

<!-- Premium Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-dark text-white p-4">
                <h5 class="modal-title fw-900"><i class="fas fa-file-invoice-dollar me-2"></i>Rincian Transaksi Masuk
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="modal-content-body">
                <div class="p-5 text-center">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-3 text-muted fw-bold">Menyiapkan rincian...</p>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
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
                    "sSearch": "Saring Data:",
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
                title: 'Hapus Transaksi?',
                text: 'Stok yang telah masuk akan ditarik kembali secara otomatis!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f72585',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => { if (result.isConfirmed) window.location.href = url; });
        });
    });

    function viewDetail(id) {
        $('#detailModal').modal('show');
        $('#modal-content-body').load('<?= base_url("warehouse/get_detail_pembelian_modal/") ?>' + id);
    }
</script>