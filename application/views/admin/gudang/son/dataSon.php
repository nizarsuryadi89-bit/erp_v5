<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
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

    .stat-card-premium {
        border: none;
        border-radius: 16px;
        transition: all 0.3s ease;
        background: white;
        box-shadow: var(--premium-shadow);
    }

    .stat-card-premium:hover {
        transform: translateY(-5px);
    }

    .stat-icon-box {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
</style>

<div class="app-content d-block">
    <div class="container-fluid py-3">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="<?= base_url('gudang') ?>"
                                class="text-decoration-none text-muted">Gudang</a></li>
                        <li class="breadcrumb-item active fw-bold text-dark">Stok Opname</li>
                    </ol>
                </nav>
                <h4 class="mb-0 fw-900 text-dark" style="letter-spacing: -0.5px;">MANAJEMEN STOK OPNAME</h4>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-white shadow-sm fw-800 px-3 rounded-pill"
                    onclick="window.history.back();">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </button>
                <a href="<?= base_url('warehouse/cetakSon/' . date('m')) ?>"
                    class="btn btn-dark shadow fw-800 px-4 rounded-pill" target="_blank">
                    <i class="bi bi-printer me-1"></i> Cetak Laporan Bulanan
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4 g-3">
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card-premium h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon-box bg-light text-dark shadow-sm">
                                <i class="bi bi-clipboard-data"></i>
                            </div>
                            <div>
                                <div class="small fw-800 text-muted text-uppercase mb-0"
                                    style="font-size: 0.65rem; letter-spacing: 0.5px;">Total Opname</div>
                                <div class="h5 mb-0 fw-900"><?= number_format($total_rows) ?> <small
                                        class="fw-normal text-muted fs-6">Unit</small></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card stat-card-premium h-100 border-start border-4 border-success">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon-box bg-success text-white shadow">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div>
                                <div class="small fw-800 text-muted text-uppercase mb-0"
                                    style="font-size: 0.65rem; letter-spacing: 0.5px;">Stok Sesuai</div>
                                <div class="h5 mb-0 fw-900 text-success">
                                    <?php
                                    $sesuai = $this->db->where('son_real = son_aplikasi')->get('tbl_stok_opname')->num_rows();
                                    echo number_format($sesuai);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card stat-card-premium h-100 border-start border-4 border-warning">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon-box bg-warning text-dark shadow">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                            <div>
                                <div class="small fw-800 text-muted text-uppercase mb-0"
                                    style="font-size: 0.65rem; letter-spacing: 0.5px;">Selisih Stok</div>
                                <div class="h5 mb-0 fw-900 text-warning">
                                    <?php
                                    $tidak_sesuai = $this->db->where('son_real != son_aplikasi')->get('tbl_stok_opname')->num_rows();
                                    echo number_format($tidak_sesuai);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card stat-card-premium h-100 border-start border-4 border-info">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon-box bg-info text-white shadow">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <div>
                                <div class="small fw-800 text-muted text-uppercase mb-0"
                                    style="font-size: 0.65rem; letter-spacing: 0.5px;">Tervalidasi</div>
                                <div class="h5 mb-0 fw-900 text-info">
                                    <?php
                                    $tervalidasi = $this->db->where('verifikasi', 1)->get('tbl_stok_opname')->num_rows();
                                    echo number_format($tervalidasi);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input Section -->
        <div class="report-section-card border-0 mb-4">
            <div class="section-header">
                <h5><i class="bi bi-plus-circle"></i> Pencatatan Stok Opname Baru</h5>
                <span class="badge bg-white text-dark small fw-bold px-3 py-1 rounded-pill">LIVE SYNC READY</span>
            </div>
            <div class="card-body p-4 bg-light">
                <form action="<?= base_url('warehouse/action_save_son') ?>" method="post" id="formOpname">
                    <div class="row g-4">
                        <div class="col-md-5">
                            <label class="form-label fw-800 text-muted small text-uppercase">1. Pilih Material / Scan
                                Barcode</label>
                            <div class="input-group shadow-sm">
                                <select name="material_id" id="material_id_opname" class="form-select select2" required>
                                    <option value="">-- Pilih Material --</option>
                                    <?php foreach ($bahan as $b): ?>
                                        <option value="<?= $b->material_id ?>" data-stok="<?= $b->stok_aplikasi ?>"
                                            data-satuan="<?= $b->satuan_nama ?>" data-barcode="<?= $b->barcode ?>">
                                            <?= $b->bahan_nama ?> (<?= $b->varian ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="row g-3">
                                <div class="col-sm-4">
                                    <label class="form-label fw-800 text-muted small text-uppercase">2. Stok
                                        Aplikasi</label>
                                    <div class="input-group">
                                        <input type="number" id="son_aplikasi_display"
                                            class="form-control fw-bold bg-white" readonly>
                                        <input type="hidden" name="son_aplikasi" id="son_aplikasi_hidden">
                                        <span class="input-group-text bg-white opacity-50 small label-satuan">-</span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label fw-800 text-muted small text-uppercase">3. Stok Real
                                        (Fisik)</label>
                                    <div class="input-group shadow-sm">
                                        <input type="number" step="0.001" name="son_real" id="son_real_input"
                                            class="form-control fw-900 border-primary" placeholder="0.000" required>
                                        <span class="input-group-text bg-white label-satuan">-</span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label fw-800 text-muted small text-uppercase">4. Selisih
                                        (Delta)</label>
                                    <div id="delta-display" class="form-control fw-900 text-center bg-white"
                                        style="font-size: 1.1rem; border: 2px dashed #dee2e6;">0</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4 align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex gap-4">
                                <div class="form-check form-switch p-0" style="padding-left: 2.5em !important;">
                                    <input class="form-check-input" type="checkbox" name="sync_now" value="1"
                                        id="syncNowCheck" checked>
                                    <label class="form-check-label fw-bold text-primary small" for="syncNowCheck">
                                        Otomatis Sinkronisasi & Update Stok Sistem
                                    </label>
                                </div>
                                <div class="text-muted small italic">
                                    <i class="bi bi-info-circle"></i> Jika dicentang, sistem akan langsung mengupdate
                                    `tbl_stok_gudang`.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <button type="submit" class="btn btn-dark fw-900 px-5 rounded-pill shadow-lg border-0"
                                style="background: var(--primary-gradient);">
                                <i class="bi bi-check-all me-2"></i> SIMPAN OPNAME
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="son_tgl" value="<?= date('Y-m-d') ?>">
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="report-section-card">
            <div class="section-header">
                <h5><i class="bi bi-clock-history"></i> Riwayat Stok Opname (Bulan Ini)</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-white text-dark fw-800 px-3 rounded-pill shadow-sm"
                        onclick="exportToExcel()">
                        <i class="bi bi-file-earmark-excel me-1"></i> EXCEL
                    </button>
                    <button class="btn btn-sm btn-white text-dark fw-800 px-3 rounded-pill shadow-sm"
                        onclick="printTable()">
                        <i class="bi bi-printer me-1"></i> PRINT
                    </button>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table premium-table table-hover mb-0" id="datatable-son">
                        <thead>
                            <tr>
                                <th class="text-center" width="50">NO</th>
                                <th>TANGGAL & WAKTU</th>
                                <th>NAMA MATERIAL</th>
                                <th class="text-center">STOK REAL</th>
                                <th class="text-center">STOK SISTEM</th>
                                <th class="text-center">SELISIH</th>
                                <th class="text-center">STATUS</th>
                                <th class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($tampilData as $dt):
                                $selisih = ($dt->son_real - $dt->son_aplikasi);

                                if ($selisih == 0) {
                                    $keterangan = 'Sesuai';
                                    $status_class = 'bg-success';
                                    $status_icon = 'bi-check-circle';
                                    $delta_color = 'text-success';
                                } elseif ($selisih > 0) {
                                    $keterangan = 'Stok Lebih';
                                    $status_class = 'bg-warning text-dark';
                                    $status_icon = 'bi-arrow-up-circle';
                                    $delta_color = 'text-warning';
                                } else {
                                    $keterangan = 'Stok Kurang';
                                    $status_class = 'bg-danger';
                                    $status_icon = 'bi-arrow-down-circle';
                                    $delta_color = 'text-danger';
                                }
                                ?>
                                <tr class="align-middle">
                                    <td class="text-center fw-800 text-muted small"><?= $no++ ?></td>
                                    <td>
                                        <div class="fw-800 text-dark" style="font-size: 0.85rem;">
                                            <?= date('d M Y', strtotime($dt->son_tgl)) ?>
                                        </div>
                                        <div class="small text-muted" style="font-size: 0.75rem;"><i
                                                class="bi bi-clock me-1"></i>
                                            <?= date('H:i', strtotime($dt->created_at ?? $dt->son_tgl)) ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-800 text-dark" style="font-size: 0.85rem;"><?= $dt->bahan_nama ?>
                                        </div>
                                        <div class="small text-muted text-uppercase fw-bold"
                                            style="font-size: 0.65rem; letter-spacing: 0.5px;"><?= $dt->varian ?></div>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge bg-white text-dark shadow-sm border fw-900 px-3 py-2 rounded-pill fs-6">
                                            <?= number_format($dt->son_real) ?>
                                        </span>
                                        <div class="small text-muted mt-1 fw-bold" style="font-size: 0.65rem;">
                                            <?= $dt->satuan_nama ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-muted fw-800 px-3 py-2 rounded-pill">
                                            <?= number_format($dt->son_aplikasi) ?>
                                        </span>
                                        <div class="small text-muted mt-1" style="font-size: 0.65rem;">
                                            <?= $dt->satuan_nama ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-900 <?= $delta_color ?>" style="font-size: 1.1rem;">
                                            <?= $selisih > 0 ? '+' : '' ?>     <?= number_format($selisih) ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge <?= $status_class ?> rounded-pill fw-800 px-3 py-2 small shadow-sm">
                                            <i class="<?= $status_icon ?> me-1"></i> <?= $keterangan ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <?php if ($dt->verifikasi == 0): ?>
                                                <form action="<?= base_url('warehouse/updateStokSon') ?>" method="post"
                                                    class="d-inline">
                                                    <input type="hidden" name="material_id" value="<?= $dt->material_id ?>">
                                                    <input type="hidden" name="son_tgl" value="<?= $dt->son_tgl ?>">
                                                    <input type="hidden" name="son_real" value="<?= $dt->son_real ?>">
                                                    <button type="submit"
                                                        class="btn btn-sm btn-primary rounded-circle shadow-sm"
                                                        style="width: 32px; height: 32px; padding: 0;"
                                                        title="Verifikasi & Sync">
                                                        <i class="bi bi-shield-check"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span
                                                    class="badge bg-success text-white rounded-pill fw-bold small px-3 py-2 shadow-sm">
                                                    <i class="bi bi-patch-check-fill me-1"></i> VALID
                                                </span>
                                            <?php endif; ?>
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

    <!-- XLSX Library for Excel Export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        $(document).ready(function () {
            // Initialize Select2 with scan support
            const materialSelect = $('#material_id_opname').select2({
                placeholder: "-- Cari Nama atau Scan Barcode --",
                allowClear: true,
                width: '100%'
            });

            // Handle Material Selection & Auto-fill (with AJAX for latest stock)
            materialSelect.on('change', function () {
                const selected = $(this).find(':selected');
                const materialId = $(this).val();

                if (!materialId) return;

                const satuan = selected.data('satuan') || '-';
                $('.label-satuan').text(satuan);

                // Fetch LATEST stock from server for precision
                $.ajax({
                    url: '<?= base_url("warehouse/get_current_stock_ajax") ?>',
                    type: 'GET',
                    data: { material_id: materialId },
                    dataType: 'json',
                    success: function (res) {
                        const stokAplikasi = res.qty || 0;
                        $('#son_aplikasi_display').val(stokAplikasi);
                        $('#son_aplikasi_hidden').val(stokAplikasi);

                        // Clear real input and delta
                        $('#son_real_input').val('').focus();
                        calculateDelta();
                    }
                });
            });

            // Calculate Delta Live
            function calculateDelta() {
                const app = parseFloat($('#son_aplikasi_hidden').val()) || 0;
                const real = parseFloat($('#son_real_input').val()) || 0;
                const delta = real - app;

                const display = $('#delta-display');
                display.text((delta > 0 ? '+' : '') + delta.toFixed(3));

                if (delta === 0) {
                    display.css('color', '#27ae60'); // Success green
                } else if (delta > 0) {
                    display.css('color', '#f39c12'); // Warning orange
                } else {
                    display.css('color', '#e74c3c'); // Danger red
                }
            }

            $('#son_real_input').on('input', calculateDelta);

            // Form Submission Confirmation
            $('#formOpname').on('submit', function (e) {
                e.preventDefault();
                const form = this;
                const isSync = $('#syncNowCheck').is(':checked');
                const delta = $('#delta-display').text();

                Swal.fire({
                    title: 'Konfirmasi Opname',
                    html: `Anda akan menyimpan data opname dengan selisih <b>${delta}</b>.<br>${isSync ? '<span class="text-danger fw-bold">Peringatan: Stok sistem akan langsung diperbarui!</span>' : ''}`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Print & Export Logic
            var table = $('#datatable-son').DataTable({
                "pageLength": 25,
                "order": [[1, "desc"]],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                },
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                "initComplete": function () {
                    updateShowingCount();
                }
            });

            function updateShowingCount() {
                var info = table.page.info();
                $('#showing-count').text(info.recordsDisplay);
            }

            // Export and Print functions
            window.exportToExcel = function () {
                var wb = XLSX.utils.table_to_book(document.getElementById('datatable-son'), { sheet: "Stok Opname" });
                XLSX.writeFile(wb, 'Data_Stok_Opname_' + new Date().toISOString().split('T')[0] + '.xlsx');
            };

            window.printTable = function () {
                var printWindow = window.open('', '_blank');
                var tableHtml = document.getElementById('datatable-son').outerHTML;
                printWindow.document.write(`
                    <html>
                        <head>
                            <title>Data Stok Opname</title>
                            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                            <style>
                                @media print {
                                    .btn, .dataTables_wrapper .dataTables_length, 
                                    .dataTables_wrapper .dataTables_filter,
                                    .dataTables_wrapper .dataTables_paginate { display: none !important; }
                                }
                            </style>
                        </head>
                        <body>
                            <h3>Data Stok Opname - <?= date('d/m/Y') ?></h3>
                            ${tableHtml}
                        </body>
                    </html>
                `);
                printWindow.document.close();
                printWindow.print();
            };

            // Focus barcode scanner
            window.focusBarcode = function () {
                $('#scan_barcode').focus();
            };

            // Auto-refresh every 5 minutes
            setInterval(function () {
                location.reload();
            }, 300000);
        });
    </script>
</div>