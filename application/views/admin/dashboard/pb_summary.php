<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    :root {
        --pb-primary: #3498db;
        --pb-secondary: #2c3e50;
        --pb-accent: #2980b9;
        --pb-gradient: linear-gradient(135deg, #3498db 0%, #1abc9c 100%);
        --pb-success: #27ae60;
        --pb-warning: #f39c12;
        --pb-danger: #e74c3c;
    }

    .pb-stat-card {
        border-radius: 15px;
        border: none;
        color: white;
        overflow: hidden;
        position: relative;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        height: 100%;
    }

    .pb-stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
    }

    .pb-stat-card .card-body {
        padding: 1.5rem;
        z-index: 2;
        position: relative;
    }

    .pb-stat-card .icon-bg {
        position: absolute;
        right: -10px;
        bottom: -10px;
        font-size: 5rem;
        opacity: 0.15;
        transform: rotate(-10deg);
    }

    .bg-gradient-pb {
        background: var(--pb-gradient);
    }

    .bg-gradient-green {
        background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    }

    .bg-gradient-orange {
        background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
    }

    .pb-shortcut {
        background: white;
        border-radius: 12px;
        padding: 1.2rem;
        text-align: center;
        text-decoration: none !important;
        color: var(--pb-secondary);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        transition: all 0.2s;
        border: 1px solid #eee;
    }

    .pb-shortcut:hover {
        background: #f7fcff;
        border-color: var(--pb-primary);
        color: var(--pb-primary);
        transform: scale(1.05);
    }

    .pb-shortcut i {
        font-size: 2rem;
        color: var(--pb-primary);
    }

    .hub-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .status-badge-pb {
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 800;
        font-size: 0.7rem;
        text-transform: uppercase;
    }

    .bg-soft-blue {
        background: #e8f4fd;
        color: #3498db;
    }

    .bg-soft-green {
        background: #eafaf1;
        color: #27ae60;
    }

    .bg-soft-orange {
        background: #fff5e6;
        color: #d35400;
    }
</style>

<div class="app-content mt-n4 pb-5">
    <div class="container-fluid">
        <!-- Dashboard Header & Actions -->
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h4 class="fw-900 text-dark mb-1"><i class="fas fa-boxes me-2 pb-primary"></i> PB Hub & Logistics</h4>
                <p class="text-muted small fw-bold mb-0">Monitor & eksekusi penyerahan barang cetak ke pelanggan.</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0 d-flex flex-wrap justify-content-md-end justify-content-start gap-2">
                <a href="<?= base_url('tracking') ?>" class="btn btn-outline-dark rounded-pill px-4 fw-900 shadow-sm">
                    <i class="fas fa-search-location me-2"></i> TRACKING SPK
                </a>
                <a href="#" class="btn btn-outline-success rounded-pill px-4 fw-900 shadow-sm bg-white" data-bs-toggle="modal" data-bs-target="#modalCekStok">
                    <i class="fas fa-boxes me-2"></i> CEK STOK GUDANG
                </a>
                <a href="<?= base_url('pb') ?>" class="btn btn-primary rounded-pill px-4 fw-900 shadow-sm" style="background: var(--pb-gradient); border: none;">
                    <i class="fas fa-barcode me-2"></i> BUKA SISTEM PENGAMBILAN BARANG
                </a>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="pb-stat-card bg-gradient-orange">
                    <div class="card-body">
                        <h6 class="fw-bold mb-1 opacity-75"><i class="fas fa-box-open me-2"></i> SIAP DIAMBIL (DI PB)</h6>
                        <h2 class="fw-900 mb-0"><?= number_format($pb_stats['ready'] ?? 0) ?> Item</h2>
                        <i class="fas fa-boxes icon-bg"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="pb-stat-card bg-gradient-green">
                    <div class="card-body">
                        <h6 class="fw-bold mb-1 opacity-75"><i class="fas fa-check-double me-2"></i> DISELESAIKAN HARI INI</h6>
                        <h2 class="fw-900 mb-0"><?= number_format($pb_stats['done_today'] ?? 0) ?> Item</h2>
                        <i class="fas fa-handshake icon-bg"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="pb-stat-card bg-gradient-pb">
                    <div class="card-body">
                        <h6 class="fw-bold mb-1 opacity-75"><i class="fas fa-spinner me-2"></i> ANTRIAN PRODUKSI</h6>
                        <h2 class="fw-900 mb-0"><?= number_format($pb_stats['incoming'] ?? 0) ?> Item</h2>
                        <i class="fas fa-cogs icon-bg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Live Progress Board -->
        <div class="row mt-4 mb-5">
            <div class="col-12">
                <div class="hub-card p-0 overflow-hidden border border-4 border-dark shadow-lg">
                    <div class="bg-dark text-white p-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-900 mb-0 ls-1"><i class="fas fa-tv me-2 text-warning"></i> LIVE PROGRESS BOARD
                            (ANTRIAN CETAK & FINISHING)</h5>
                        <a href="<?= base_url('print_queue/progress_board') ?>" target="_blank"
                            class="btn btn-sm btn-outline-light rounded-pill px-4 fw-bold shadow-sm hover-rotate">
                            LAYAR PENUH <i class="fas fa-external-link-alt ms-2"></i>
                        </a>
                    </div>
                    <iframe src="<?= base_url('print_queue/progress_board') ?>"
                        style="width: 100%; height: 800px; border: none; background: #0f172a; display: block;"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cek Stok Gudang -->
<div class="modal fade" id="modalCekStok" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-boxes me-2"></i> CEK STOK GUDANG (LIVE)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle w-100" id="tableCekStok">
                        <thead class="table-light text-muted small uppercase">
                            <?php $cabang_list = $this->db->order_by('id_perusahaan', 'ASC')->get('tbl_perusahaan')->result(); ?>
                            <tr>
                                <th style="min-width: 250px;">NAMA BAHAN</th>
                                <th style="min-width: 250px;">VARIAN / UKURAN</th>
                                <?php foreach ($cabang_list as $c): ?>
                                    <th class="text-center" style="min-width: 120px;"><?= strtoupper($c->nama_perusahaan) ?>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let stokTableLoaded = false;
        const modalStok = document.getElementById('modalCekStok');

        if (modalStok) {
            modalStok.addEventListener('show.bs.modal', function () {
                if (!stokTableLoaded) {
                    if ($.fn.DataTable.isDataTable('#tableCekStok')) {
                        $('#tableCekStok').DataTable().destroy();
                    }

                    $('#tableCekStok').DataTable({
                        ajax: {
                            url: '<?= site_url('dashboard/ajax_get_stock_deskprint_pivoted') ?>',
                            dataSrc: ''
                        },
                        columns: [
                            { data: 'bahan_nama', className: 'fw-bold' },
                            { data: 'varian' },
                            <?php foreach ($cabang_list as $c): ?>
                                    {
                                    data: 'branch_<?= $c->id_perusahaan ?>',
                                    defaultContent: '0',
                                    className: 'text-center fw-900',
                                    render: function (data, type, row) {
                                        var qty = parseFloat(data) || 0;
                                        var konversi = parseFloat(row.konversi) || 1;
                                        var satGudang = row.satuan_gudang || '';
                                        var satDasar = row.satuan_dasar || '';

                                        if (qty === 0) return '<span class="text-muted">-</span>';

                                        if (konversi > 1) {
                                            var main_qty = Math.floor(qty / konversi);
                                            var small_qty = Math.round(qty % konversi);
                                            var html = '';

                                            if (main_qty !== 0 || small_qty === 0) {
                                                html += '<span class="text-primary" style="font-size: 1.1rem;">' + main_qty.toLocaleString('id-ID') + '</span> <span class="text-muted">' + satGudang + '</span>';
                                            }

                                            if (small_qty !== 0) {
                                                if (html !== '') html += '<br>';
                                                html += '<span class="text-info">' + small_qty.toLocaleString('id-ID') + '</span> <span class="text-muted small">' + satDasar + '</span>';
                                            }
                                            return html;
                                        } else {
                                            return '<span class="text-primary" style="font-size: 1.1rem;">' + qty.toLocaleString('id-ID') + '</span> <span class="text-muted">' + (satDasar || satGudang) + '</span>';
                                        }
                                    }
                                },
                            <?php endforeach; ?>
                        ],
                        language: {
                            search: "Cari Material:",
                            lengthMenu: "Tampilkan _MENU_ data",
                            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ material",
                            infoEmpty: "Tidak ada data material",
                            zeroRecords: "Material tidak ditemukan",
                            paginate: {
                                first: "Pertama",
                                last: "Terakhir",
                                next: "Selanjutnya",
                                previous: "Sebelumnya"
                            }
                        },
                        pageLength: 10,
                        order: [[0, 'asc']]
                    });
                    stokTableLoaded = true;
                } else {
                    $('#tableCekStok').DataTable().ajax.reload(null, false);
                }
            });
        }
    });
</script>