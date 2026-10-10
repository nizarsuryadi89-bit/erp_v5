<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    /* ==========================================================================
    PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - OUTGOING MATERIAL LOG
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

    .officer-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .officer-avatar {
        width: 32px;
        height: 32px;
        background: var(--primary-gradient);
        color: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 12px;
    }

    .badge-premium {
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-weight: 800;
        font-size: 0.65rem;
        text-transform: uppercase;
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


        <!-- Main Card -->
        <div class="report-section-card animate-up" style="animation-delay: 0.1s">
            <div class="section-header">
                <h5><i class="fas fa-exchange-alt"></i> Daftar Distribusi Barang (Keluar)</h5>
                <div class="d-flex gap-2">
                    <a href="<?= base_url('warehouse/PengambilanBahan') ?>"
                        class="btn btn-sm btn-light rounded-pill px-3 fw-bold">
                        <i class="fas fa-plus me-1"></i> Tambah Baru
                    </a>
                    <button id="btnExport" class="btn btn-sm btn-outline-light rounded-pill px-3 fw-bold">
                        <i class="fas fa-download me-1"></i> Xlsx
                    </button>
                </div>
            </div>

            
            <!-- Custom Tabs -->
            <ul class="nav nav-tabs nav-tabs-custom mb-4 px-4 pt-3 border-0" id="gudangTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold px-4 py-3 border-0 bg-transparent text-dark position-relative" 
                            id="pusat-tab" data-bs-toggle="tab" data-bs-target="#pusat" type="button" role="tab" 
                            style="font-size:0.95rem;">
                        <i class="fas fa-building me-2 text-primary"></i>Gudang Pusat
                        <div class="active-indicator" style="position:absolute; bottom:0; left:15%; right:15%; height:3px; background:var(--primary-gradient); border-radius:3px 3px 0 0; display:block;"></div>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold px-4 py-3 border-0 bg-transparent text-muted position-relative" 
                            id="cabang-tab" data-bs-toggle="tab" data-bs-target="#cabang" type="button" role="tab"
                            style="font-size:0.95rem;">
                        <i class="fas fa-store me-2 text-info"></i>Gudang Cabang
                        <div class="active-indicator" style="position:absolute; bottom:0; left:15%; right:15%; height:3px; background:var(--info-gradient); border-radius:3px 3px 0 0; display:none;"></div>
                    </button>
                </li>
            </ul>
            <style>
                .nav-tabs-custom .nav-link.active {
                    color: #1a1c23 !important;
                }
                .nav-tabs-custom .nav-link.active .active-indicator {
                    display: block !important;
                }
                .nav-tabs-custom .nav-link:hover {
                    color: #1a1c23 !important;
                }
            </style>
            
            <div class="tab-content" id="gudangTabsContent">
                <!-- PUSAT TAB -->
                <div class="tab-pane fade show active" id="pusat" role="tabpanel">
<div class="table-responsive">
                <table class="table premium-table table-hover mb-0 datatable-gudang w-100" style="width:100%" id="datatable-pusat">
                    <thead>
                        <tr>
                            <th width="100" class="text-center">No. Referensi</th>
                            <th>Waktu & Tanggal</th>
                            <th>Tujuan Gudang</th>
                            <th class="text-center">Varian</th>
                            <th class="text-center">Total Qty</th>
                            <th class="text-center">Status</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($tampilData) && is_array($tampilData)):
                            $no = 1;
                            foreach ($tampilData as $dt):
                                $isPending = ($dt->pending_count ?? 0) > 0;
                                if (strpos(strtolower($dt->gudang_lokasi ?? 'pusat'), 'pusat') === false) continue;
                                ?>
                                <tr>
                                    <td class="text-center">
                                        <div class="fw-800 text-dark fs-7">DSP-<?= str_pad($dt->pengambilan_id, 5, '0', STR_PAD_LEFT) ?></div>
                                    </td>
                                    <td data-order="<?= $dt->pengambilan_tgl ?> <?= $dt->pengambilan_waktu ?>">
                                        <div class="fw-bold text-dark"><?= date('d F Y', strtotime($dt->pengambilan_tgl)) ?></div>
                                        <div class="small text-muted">
                                            <i class="far fa-clock me-1"></i>
                                            <?= (empty($dt->pengambilan_waktu) || $dt->pengambilan_waktu == '00:00:00') ? '--:--' : date('H:i', strtotime($dt->pengambilan_waktu)) ?> WIB
                                        </div>
                                    </td>
                                    <td data-order="<?= $dt->gudang_nama ?? 'N/A' ?>">
                                        <div class="fw-800 text-primary">
                                            <i class="fas fa-warehouse me-2 opacity-50"></i><?= $dt->gudang_nama ?? 'N/A' ?>
                                        </div>
                                        <div class="small text-muted">Lokasi Internal</div>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge-premium bg-light text-dark border"><?= number_format($dt->item_count ?? 0) ?>
                                            Items</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-900 text-dark"><?= number_format($dt->qty_total ?? 0, 2) ?></div>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge-premium <?= $isPending ? 'bg-warning-subtle text-warning' : 'bg-info-subtle text-info' ?>">
                                            <?= $isPending ? 'PROSES' : 'SELESAI' ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="javascript:void(0)" onclick="openModalDetail('<?= base_url('warehouse/cetakPengambilan/' . $dt->pengambilan_id) ?>')"
                                                class="btn-circle-action" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('warehouse/cetakPengambilan/' . $dt->pengambilan_id) ?>"
                                                target="_blank" class="btn-circle-action" title="Cetak">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                    </tbody>
                </table>
            
                </div>
                </div>
                <!-- CABANG TAB -->
                <div class="tab-pane fade" id="cabang" role="tabpanel">
<div class="table-responsive">
                <table class="table premium-table table-hover mb-0 datatable-gudang w-100" style="width:100%" id="datatable-cabang">
                    <thead>
                        <tr>
                            <th width="100" class="text-center">No. Referensi</th>
                            <th>Waktu & Tanggal</th>
                            <th>Tujuan Gudang</th>
                            <th class="text-center">Varian</th>
                            <th class="text-center">Total Qty</th>
                            <th class="text-center">Status</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($tampilData) && is_array($tampilData)):
                            $no = 1;
                            foreach ($tampilData as $dt):
                                $isPending = ($dt->pending_count ?? 0) > 0;
                                if (strpos(strtolower($dt->gudang_lokasi ?? ''), 'cabang') === false) continue;
                                ?>
                                <tr>
                                    <td class="text-center">
                                        <div class="fw-800 text-dark fs-7">DSP-<?= str_pad($dt->pengambilan_id, 5, '0', STR_PAD_LEFT) ?></div>
                                    </td>
                                    <td data-order="<?= $dt->pengambilan_tgl ?> <?= $dt->pengambilan_waktu ?>">
                                        <div class="fw-bold text-dark"><?= date('d F Y', strtotime($dt->pengambilan_tgl)) ?></div>
                                        <div class="small text-muted">
                                            <i class="far fa-clock me-1"></i>
                                            <?= (empty($dt->pengambilan_waktu) || $dt->pengambilan_waktu == '00:00:00') ? '--:--' : date('H:i', strtotime($dt->pengambilan_waktu)) ?> WIB
                                        </div>
                                    </td>
                                    <td data-order="<?= $dt->gudang_nama ?? 'N/A' ?>">
                                        <div class="fw-800 text-primary">
                                            <i class="fas fa-warehouse me-2 opacity-50"></i><?= $dt->gudang_nama ?? 'N/A' ?>
                                        </div>
                                        <div class="small text-muted">Lokasi Internal</div>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge-premium bg-light text-dark border"><?= number_format($dt->item_count ?? 0) ?>
                                            Items</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-900 text-dark"><?= number_format($dt->qty_total ?? 0, 2) ?></div>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge-premium <?= $isPending ? 'bg-warning-subtle text-warning' : 'bg-info-subtle text-info' ?>">
                                            <?= $isPending ? 'PROSES' : 'SELESAI' ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="javascript:void(0)" onclick="openModalDetail('<?= base_url('warehouse/cetakPengambilan/' . $dt->pengambilan_id) ?>')"
                                                class="btn-circle-action" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('warehouse/cetakPengambilan/' . $dt->pengambilan_id) ?>"
                                                target="_blank" class="btn-circle-action" title="Cetak">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                    </tbody>
                </table>
            
                </div>
            </div>
</div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    $(document).ready(function () {
        if ($.fn.DataTable) {
            $('.datatable-gudang').DataTable({
                "language": {
                    "sSearch": "Cari Distribusi:",
                    "oPaginate": { "sNext": "<i class='fas fa-chevron-right'></i>", "sPrevious": "<i class='fas fa-chevron-left'></i>" }
                },
                "pageLength": 25,
                "autoWidth": false,
                "ordering": true,
                "order": [[2, 'asc'], [1, 'desc']],
                "columnDefs": [
                    { "visible": false, "targets": 2 },
                    { "orderable": false, "targets": [0, 3, 6] }
                ],
                "drawCallback": function (settings) {
                    var api = this.api();
                    var rows = api.rows({ page: 'current' }).nodes();
                    var last = null;

                    api.column(2, { page: 'current' }).data().each(function (group, i) {
                        var groupName = $('<div>' + group + '</div>').find('.fw-800').text().trim();
                        if (last !== groupName) {
                            $(rows).eq(i).before(
                                '<tr class="group" style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0; border-top: 2px solid #e2e8f0;"><td colspan="7" class="fw-800 text-dark px-4 py-3" style="font-size: 1rem;"><i class="fas fa-warehouse me-2 text-primary"></i>' + groupName + '</td></tr>'
                            );
                            last = groupName;
                        }
                    });
                },
                "dom": "<'row px-4 py-3'<'col-md-6'l><'col-md-6 d-flex justify-content-end text-end'f>>t<'row px-4 py-3'<'col-md-6'i><'col-md-6 d-flex justify-content-end text-end'p>>"
            });
        }

        window.exportToExcel = function () {
            var activeTable = document.querySelector('.tab-pane.active table');
            if(activeTable){
                var wb = XLSX.utils.table_to_book(activeTable, { sheet: "Distribusi" });
                XLSX.writeFile(wb, 'Log_Distribusi_Gudang.xlsx');
            }
        };

        // Handle tab change to update indicator
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('.active-indicator').hide();
            $(e.target).find('.active-indicator').show();
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        });
    });

    if (typeof openModalDetail !== 'function') {
        window.openModalDetail = function(url) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    html: '<iframe src="' + url + '" style="width:100%; height:75vh; border:none; border-radius: 10px;"></iframe>',
                    width: '850px',
                    padding: '0',
                    showCloseButton: true,
                    showConfirmButton: false,
                    background: '#fff',
                    customClass: {
                        popup: 'rounded-4 shadow-lg'
                    }
                });
            } else {
                window.open(url, '_blank', 'width=850,height=600');
            }
        }
    }
</script>