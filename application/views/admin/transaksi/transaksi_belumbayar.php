<!DOCTYPE html>
<html>

<head>
    <title>Transaksi Belum Bayar - TGM ERP</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">
</head>

<body>
    <!-- <div class="app-content"> -->
    <!-- <div class="container-fluid"> -->
    <!-- Header -->
    <?php
    $this->load->view('layout_admin/header_modul');
    ?>

    <!-- Quick Actions -->
    <!-- <div class="quick-actions"> -->

    <!-- <div class="row">
                    <div class="col-md-4 col-xl-4">
                        <div class="card widget-card-1">
                            <div class="card-block-small">
                                <i class="feather icon-file-text bg-c-yellow card1-icon"></i>
                                <span class="text-c-yellow f-w-600">Total Pesanan Hari Ini</span>
                                <h4><?= $total_penjualan_harian ?> SPK</h4>
                                <div>
                                    <span class="f-left m-t-10 text-muted"> <i
                                            class="text-c-yellow f-16 feather icon-alert-triangle m-r-10"></i>Jumlah SPK
                                        masuk hari ini</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-xl-4">
                        <div class="card widget-card-1">
                            <div class="card-block-small">
                                <i class="feather icon-pie-chart bg-c-blue card1-icon"></i>
                                <span class="text-c-blue f-w-600">Total Penjualan (Nominal)</span>
                                <h4>Rp <?= number_format($total_nominal_harian, 0, ',', '.') ?></h4>
                                <div>
                                    <span class="f-left m-t-10 text-muted"> <i
                                            class="text-c-blue f-16 feather icon-alert-triangle m-r-10"></i>Total
                                        Omset yang sudah dibayar</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-xl-4">
                        <div class="card widget-card-1">
                            <div class="card-block-small">
                                <i class="feather icon-users bg-c-green card1-icon"></i>
                                <span class="text-c-green f-w-600">Total Pelanggan Hari Ini</span>
                                <h4><?= $total_pelanggan_harian ?> User</h4>
                                <div>
                                    <span class="f-left m-t-10 text-muted"> <i
                                            class="text-c-green f-16 feather icon-alert-triangle m-r-10"></i>Total
                                        Pelanggan yang order hari ini</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->


    <!-- <div class="action-grid">
                    <a href="<?= base_url('transaksi/createspk') ?>" class="action-btn">
                        <i class="fa fa-plus-circle"></i>
                        <div class="action-content">
                            <span class="action-title">Buat SPK Baru</span>
                            <small class="action-subtitle">Oleh Kasir / Owner / Administrator</small>
                        </div>
                    </a>

                    <a href="<?= base_url('keuangan/pengeluaran_harian') ?>" class="action-btn">
                        <i class="fa fa-calendar"></i>
                        <div class="action-content">
                            <span class="action-title">Pengeluaran Kasir</span>
                            <small class="action-subtitle">Laporan pengeluaran harian</small>
                        </div>
                    </a>

                    <a href="<?= base_url('transaksi/calendar_view') ?>" class="action-btn">
                        <i class="fa fa-calendar-alt"></i>
                        <div class="action-content">
                            <span class="action-title">Kalender Penjualan</span>
                            <small class="action-subtitle">View penjualan per tanggal</small>
                        </div>
                    </a>

                    <a href="<?= base_url('transaksi/transaksi_kasir') ?>" class="action-btn">
                        <i class="fa fa-chart-bar"></i>
                        <div class="action-content">
                            <span class="action-title">Laporan Transaksi Kasir</span>
                            <small class="action-subtitle"><?= $username ?></small>
                        </div>
                    </a>

                    <a href="<?= base_url('KasirShift') ?>" class="action-btn">
                        <i class="fa fa-cash-register"></i>
                        <div class="action-content">
                            <span class="action-title">Kasir Shift Management</span>
                            <small class="action-subtitle">Kelola shift kasir</small>
                        </div>
                    </a>

                    <?php if (in_array($this->session->userdata('role_id'), [1, 2])): // Administrator atau Owner ?>
                        <a href="<?= base_url('transaksi/kasir_shift_rekap') ?>" class="action-btn">
                            <i class="fa fa-chart-line"></i>
                            <div class="action-content">
                                <span class="action-title">Rekap Kasir Shift</span>
                                <small class="action-subtitle">Laporan lengkap performa kasir</small>
                            </div>
                        </a>
                    <?php endif; ?>

                    <a href="<?= base_url('transaksi/analisis_produk_pemesanan') ?>" class="action-btn">
                        <i class="fa fa-chart-bar"></i>
                        <div class="action-content">
                            <span class="action-title">Analisis Produk</span>
                            <small class="action-subtitle">Analisis produk dari pemesanan</small>
                        </div>
                    </a>
                </div> -->

    <style>
        .action-grid {
            display: flex;
            flex-direction: row;
            gap: 15px;
            margin: 20px 0;
            overflow-x: auto;
            padding-bottom: 10px;
        }

        .action-btn {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            background: linear-gradient(135deg, rgb(2, 6, 24) 0%, rgb(27, 18, 36) 100%);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            min-height: 70px;
            min-width: 250px;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            color: white;
            text-decoration: none;
        }

        .action-btn i {
            font-size: 1.5rem;
            margin-right: 12px;
            min-width: 30px;
            text-align: center;
        }

        .action-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .action-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 3px;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .action-subtitle {
            font-size: 0.8rem;
            opacity: 0.9;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Custom scrollbar for horizontal scroll */
        .action-grid::-webkit-scrollbar {
            height: 8px;
        }

        .action-grid::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .action-grid::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .action-grid::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .action-btn {
                min-width: 220px;
                padding: 12px 16px;
            }

            .action-title {
                font-size: 0.95rem;
            }

            .action-subtitle {
                font-size: 0.75rem;
            }
        }

        @media (max-width: 768px) {
            .action-grid {
                gap: 12px;
                padding-bottom: 8px;
            }

            .action-btn {
                min-width: 200px;
                padding: 10px 14px;
                min-height: 60px;
            }

            .action-btn i {
                font-size: 1.3rem;
                margin-right: 10px;
                min-width: 25px;
            }

            .action-title {
                font-size: 0.9rem;
            }

            .action-subtitle {
                font-size: 0.7rem;
            }
        }

        @media (max-width: 480px) {
            .action-grid {
                gap: 10px;
            }

            .action-btn {
                min-width: 180px;
                padding: 8px 12px;
                min-height: 55px;
            }

            .action-btn i {
                font-size: 1.2rem;
                margin-right: 8px;
                min-width: 22px;
            }
        }
    </style>
    <!-- </div> -->

    <!-- Filter Bar -->
    <div class="filter-wrapper mb-4">
        <div class="filter-bar bg-white shadow-sm border p-3 rounded-4">
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <div class="dropdown">
                        <button
                            class="btn btn-light border-0 rounded-3 px-3 py-2 fw-semibold d-flex align-items-center gap-2"
                            type="button" data-bs-toggle="dropdown">
                            <i class="feather icon-list text-primary"></i>
                            <span>Urutkan</span>
                        </button>
                        <ul class="dropdown-menu shadow-lg border-0 mt-2 p-2 rounded-3">
                            <li><a class="dropdown-item rounded-2 mb-1 sort-trigger" data-col="2" data-order="desc"
                                    href="#"><i class="feather icon-arrow-down me-2 text-muted"></i>Terbaru</a></li>
                            <li><a class="dropdown-item rounded-2 mb-1 sort-trigger" data-col="2" data-order="asc"
                                    href="#"><i class="feather icon-arrow-up me-2 text-muted"></i>Terlama</a></li>
                            <li><a class="dropdown-item rounded-2 sort-trigger" data-col="7" data-order="desc"
                                    href="#"><i class="feather icon-dollar-sign me-2 text-muted"></i>Nominal
                                    Terbesar</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-auto border-start ps-3">
                    <div class="dropdown">
                        <button
                            class="btn btn-light border-0 rounded-3 px-3 py-2 fw-semibold d-flex align-items-center gap-2"
                            type="button" data-bs-toggle="dropdown">
                            <i class="feather icon-calendar text-warning"></i>
                            <span id="date-label">Hari ini / Tanggal</span>
                        </button>
                        <ul class="dropdown-menu shadow-lg border-0 mt-2 p-2 rounded-3">
                            <li><a class="dropdown-item rounded-2 mb-1 date-filter active" data-range="today"
                                    href="#">Hari Ini</a></li>
                            <li><a class="dropdown-item rounded-2 mb-1 date-filter" data-range="yesterday"
                                    href="#">Kemarin</a></li>
                            <li><a class="dropdown-item rounded-2 date-filter" data-range="all" href="#">Semua Waktu</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-auto border-start ps-3">
                    <div class="dropdown">
                        <button
                            class="btn btn-light border-0 rounded-3 px-3 py-2 fw-semibold d-flex align-items-center gap-2"
                            type="button" data-bs-toggle="dropdown">
                            <i class="feather icon-user text-info"></i>
                            <span id="deskprint-label">Deskprint</span>
                        </button>
                        <ul class="dropdown-menu shadow-lg border-0 mt-2 p-2 rounded-3" id="deskprint-list"
                            style="max-height: 300px; overflow-y: auto;">
                            <li><a class="dropdown-item rounded-2 deskprint-filter active" data-value=""
                                    href="#">Semua</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-12 col-lg border-start ps-lg-3 ms-lg-2">
                    <div class="search-box position-relative">
                        <i class="feather icon-search position-absolute top-50 translate-middle-y ms-3 text-muted"
                            style="z-index: 5;"></i>
                        <input type="text" id="customSearch"
                            class="form-control border-0 bg-light rounded-pill ps-5 py-2 fs-6 shadow-none"
                            placeholder="Cari SPK / Pelanggan / No. Telepon..."
                            style="height: 40px; font-size: 0.85rem;">
                    </div>
                </div>


            </div>
        </div>
    </div>

    <style>
        .filter-wrapper {
            position: sticky;
            top: 0;
            z-index: 101;
            /* Higher than header if needed */
            transition: all 0.3s ease;
        }

        .filter-bar {
            background: #ffffff !important;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            transition: box-shadow 0.3s ease;
        }

        .filter-bar:hover {
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06) !important;
        }

        .dropdown-item.active {
            background: #f0f7ff !important;
            color: #0d6efd !important;
            font-weight: 600;
        }

        .dropdown-item i {
            transition: all 0.2s;
        }

        .dropdown-item:hover i {
            transform: translateX(3px);
        }

        #customSearch:focus {
            background: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1) !important;
            border: 1px solid #0d6efd !important;
        }

        @media (max-width: 991px) {
            .border-start {
                border-left: none !important;
                border-top: 1px solid #eee;
                padding-top: 10px;
                padding-left: 0 !important;
                margin-left: 0 !important;
            }

            .col-12 {
                margin-top: 10px;
            }
        }

        .transaction-cards {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }

        .cards-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
            padding: 1rem 1.5rem !important;
            border-radius: 16px 16px 0 0 !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            margin-bottom: 0 !important;
        }

        .btn-refresh-modern {
            background: rgba(255, 255, 255, 0.12) !important;
            border: 1px solid rgba(255, 255, 255, 0.18) !important;
            color: #ffffff !important;
            font-size: 0.8rem !important;
            padding: 8px 18px !important;
            border-radius: 50px !important;
            transition: all 0.2s ease !important;
            font-weight: 600 !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-refresh-modern:hover {
            background: rgba(255, 255, 255, 0.22) !important;
            border-color: rgba(255, 255, 255, 0.35) !important;
            color: #ffffff !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        }

        .btn-refresh-modern i {
            color: #38bdf8 !important;
            font-size: 0.9rem;
        }
    </style>

    <div class="transaction-cards mt-2">
        <div class="cards-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 fw-semibold text-white d-flex align-items-center gap-2"
                style="font-size: 1.1rem; letter-spacing: 0.3px;">
                <i class="fa fa-list-ul text-info"></i>
                Daftar Transaksi Belum Bayar
            </h5>
            <button class="btn btn-refresh-modern shadow-none" onclick="reloadTabelTransaksi()">
                <i class="feather icon-refresh-cw"></i>Segarkan Data
            </button>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 0 0 16px 16px !important; overflow: hidden;">
            <div class="card-body p-0">
                <div id="tabel-transaksi-belumbayar" class="p-3">
                    <?php $this->load->view('admin/transaksi/partial_tabel_transaksi', ['transaksi_belumbayar' => $transaksi_belumbayar]); ?>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>

    <!-- JS & MODAL PREVIEW INJECTED AT MAIN VIEW ONLY FOR STABILITY -->
    <script>
        let currentPreviewData = null;

        $(document).ready(function () {
            let table;

            function initDataTable() {
                if ($.fn.DataTable.isDataTable('#datatable-details')) {
                    $('#datatable-details').DataTable().destroy();
                }

                table = $('#datatable-details').DataTable({
                    "dom": '<"d-flex align-items-center justify-content-between mb-3"l>rt<"d-flex align-items-center justify-content-between mt-3"ip>',
                    "pagingType": "full_numbers",
                    "language": {
                        "emptyTable": "Tidak ada transaksi ditemukan",
                        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        "paginate": { "previous": "<", "next": ">" }
                    },
                    "order": [[2, 'desc']],
                    "drawCallback": function () {
                        $('.dataTables_paginate > .pagination').addClass('pagination-sm mb-0');
                    }
                });

                populateDeskprint();
            }

            function populateDeskprint() {
                let deskprints = [];
                // Deskprint column is index 4
                table.column(4).data().each(function (val) {
                    let cleanVal = $('<div>').html(val).text().trim();
                    if (cleanVal && deskprints.indexOf(cleanVal) === -1) {
                        deskprints.push(cleanVal);
                    }
                });

                let listHtml = '<li><a class="dropdown-item rounded-2 mb-1 deskprint-filter active" data-value="" href="#">Semua Pelaksana</a></li>';
                deskprints.sort().forEach(function (name) {
                    listHtml += `<li><a class="dropdown-item rounded-2 mb-1 deskprint-filter" data-value="${name}" href="#">${name}</a></li>`;
                });
                $('#deskprint-list').html(listHtml);
            }

            // Initial Load
            initDataTable();

            // Filters
            $('#customSearch').on('keyup', function () { table.search(this.value).draw(); });

            $(document).on('click', '.sort-trigger', function (e) {
                e.preventDefault();
                $('.sort-trigger').removeClass('active');
                $(this).addClass('active');
                table.order([$(this).data('col'), $(this).data('order')]).draw();
            });

            $(document).on('click', '.date-filter', function (e) {
                e.preventDefault();
                let range = $(this).data('range');
                $('.date-filter').removeClass('active');
                $(this).addClass('active');

                let label = "Semua Waktu";
                if (range === 'today') { label = "Hari Ini"; table.column(2).search('Hari Ini').draw(); }
                else if (range === 'yesterday') { label = "Kemarin"; table.column(2).search('Kemarin').draw(); }
                else { table.column(2).search('').draw(); }

                $('#date-label').text(label);
            });

            $(document).on('click', '.deskprint-filter', function (e) {
                e.preventDefault();
                let val = $(this).data('value');
                $('.deskprint-filter').removeClass('active');
                $(this).addClass('active');
                table.column(4).search(val).draw();
                $('#deskprint-label').text(val || 'Semua Deskprint');
            });

            // Global Functions
            window.reloadTabelTransaksi = function () {
                $('#tabel-transaksi-belumbayar').fadeTo(200, 0.5);
                const branchId = "<?= $selected_branch ?>";
                const url = "<?= base_url('transaksi/tabel_transaksi_belumbayar') ?>?branch_id=" + branchId;

                $("#tabel-transaksi-belumbayar").load(url, function () {
                    $("#tabel-transaksi-belumbayar").fadeTo(200, 1);
                    initDataTable();
                    Swal.fire({
                        toast: true, position: 'top-end', icon: 'success',
                        title: 'Data telah diperbarui', showConfirmButton: false, timer: 1500
                    });
                });
            };

            // --- BUSINESS LOGIC FUNCTIONS ---
            window.formatRupiah = function (angka, prefix = 'Rp ') {
                var number_string = angka.toString(),
                    sisa = number_string.length % 3,
                    rupiah = number_string.substr(0, sisa),
                    ribuan = number_string.substr(sisa).match(/\d{3}/g);

                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                return prefix + rupiah;
            }

            window.previewTransaksi = function (nospk) {
                Swal.fire({ title: 'Memuat data...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

                $.post("<?= base_url('transaksi/get_transaction_preview_ajax') ?>", { nospk: nospk }, function (res) {
                    Swal.close();
                    if (res.status === 'success') {
                        const data = res.data;
                        currentPreviewData = data;
                        let html = `
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-body p-4" id="invoice-area">
                                <div class="border-bottom pb-4 mb-4 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="fw-bold mb-1 text-primary">INVOICE</h4>
                                        <p class="text-muted mb-0 small">No. SPK: <span class="fw-bold text-dark">#${data.nospk}</span></p>
                                    </div>
                                    <div class="text-end">
                                        <h5 class="fw-bold mb-0 text-dark"><?= isset($perusahaan) ? $perusahaan : 'TGM PRINT' ?></h5>
                                        <small class="text-muted">Digital Printing Solutions</small>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-6">
                                        <label class="text-muted small fw-bold text-uppercase mb-1 d-block">Pelanggan:</label>
                                        <h6 class="fw-bold mb-1">${data.konsumen}</h6>
                                        <p class="text-muted mb-0 small"><i class="fab fa-whatsapp me-1 text-success"></i>${data.nohp}</p>
                                    </div>
                                    <div class="col-6 text-end border-start">
                                        <label class="text-muted small fw-bold text-uppercase mb-1 d-block">Detail:</label>
                                        <p class="mb-1 small"><span class="text-muted">Tanggal:</span> <strong>${data.tanggal}</strong></p>
                                        <p class="mb-0 small"><span class="text-muted">Metode:</span> <span class="badge bg-light text-dark border-0 shadow-sm">${data.metode}</span></p>
                                    </div>
                                </div>

                                <div class="table-responsive mb-4">
                                    <table class="table table-borderless align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="ps-3 rounded-start small py-2">Item</th>
                                                <th class="text-center small py-2">Qty</th>
                                                <th class="text-end pe-3 rounded-end small py-2">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;

                        let grandTotal = 0;
                        data.items.forEach(item => {
                            grandTotal += item.subtotal;
                            html += `
                                <tr class="border-bottom">
                                    <td class="ps-3 py-3">
                                        <div class="fw-bold text-dark">${item.nama}</div>
                                        ${item.dimensi ? `<div class="small text-primary mt-1">${item.dimensi}</div>` : ''}
                                    </td>
                                    <td class="text-center py-3 fw-semibold">${item.qty}</td>
                                    <td class="text-end pe-3 py-3 fw-bold text-dark">${formatRupiah(item.subtotal)}</td>
                                </tr>`;
                        });

                        html += `
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2" class="text-end pt-4 pb-0 fw-bold">Grand Total</td>
                                                <td class="text-end pe-3 pt-4 pb-0 fw-bold text-primary fs-5">${formatRupiah(grandTotal)}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="alert alert-info border-0 rounded-3 mb-0 d-flex align-items-center gap-3">
                                    <i class="fas fa-info-circle fs-4"></i>
                                    <div class="small">Scan Link Invoice untuk melihat status produksi dan pembayaran secara real-time.</div>
                                </div>
                            </div>
                        </div>`;

                        document.getElementById('previewContent').innerHTML = html;
                        currentPreviewData.grandTotalFormatted = formatRupiah(grandTotal);
                        new bootstrap.Modal(document.getElementById('modalPreview')).show();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }, 'json');
            }

            window.copyInvoiceText = function () {
                if (!currentPreviewData) return;
                const data = currentPreviewData;
                let text = `*INVOICE #${data.nospk}*\n`;
                text += `Pelanggan: ${data.konsumen}\n`;
                text += `--------------------------------\n`;
                data.items.forEach(item => {
                    text += `• ${item.nama} (x${item.qty}) - ${formatRupiah(item.subtotal)}\n`;
                    if (item.dimensi) {
                        text += `  ${item.dimensi}\n`;
                    }
                });
                text += `--------------------------------\n`;
                text += `*Total: ${data.grandTotalFormatted}*\n`;
                text += `\nLink Invoice: <?= base_url() ?>cekpesanan/spk/${data.nospk_encrypted_safe}\n`;
                text += `Terima Kasih - <?= isset($perusahaan) ? $perusahaan : 'TGM PRINT' ?>`;

                navigator.clipboard.writeText(text).then(() => {
                    Swal.fire({ icon: 'success', title: 'Tersalin!', showConfirmButton: false, timer: 1000 });
                });
            }

            window.kirimWA = function () {
                if (!currentPreviewData) return;
                const data = currentPreviewData;
                let text = `*Halo ${data.konsumen},*\n\nBerikut rincian pesanan Anda:\n*No SPK:* #${data.nospk}\n\n`;
                data.items.forEach(item => {
                    text += `• ${item.nama} (x${item.qty}): ${formatRupiah(item.subtotal)}\n`;
                    if (item.dimensi) {
                        text += `  ${item.dimensi}\n`;
                    }
                });
                text += `\n*Total Tagihan: ${data.grandTotalFormatted}*\n\n`;
                text += `Cek detail di: <?= base_url() ?>cekpesanan/spk/${data.nospk_encrypted_safe}\n\nTerima kasih!`;
                window.open(`https://api.whatsapp.com/send?phone=${data.nohp}&text=${encodeURIComponent(text)}`, '_blank');
            }

            window.hapusSPK = function (nospk) {
                Swal.fire({
                    title: 'Hapus SPK?', text: "Data akan dihapus permanen!", icon: 'warning',
                    showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post("<?= base_url('transaksi/delete') ?>", { nospk: nospk }, function (res) {
                            if (res.status === 'success') { reloadTabelTransaksi(); }
                            else { Swal.fire('Gagal!', res.message, 'error'); }
                        }, 'json');
                    }
                });
            }

            window.tarikBalikSPK = function (nospk) {
                Swal.fire({
                    title: 'Tarik Balik?', text: "SPK akan ditarik ke antrian draft.", icon: 'question',
                    showCancelButton: true, confirmButtonColor: '#ffc107', cancelButtonColor: '#3085d6', confirmButtonText: 'Ya, Tarik!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post("<?= base_url('transaksi/edit_pending_spk') ?>", { nospk: nospk }, function (res) {
                            if (res && res.status === 'error') { Swal.fire('Gagal!', res.message, 'error'); }
                            else { window.location.href = '<?= base_url("transaksi") ?>'; }
                        }, 'json').fail(() => { window.location.href = '<?= base_url("transaksi") ?>'; });
                    }
                });
            }
        });
    </script>

    <!-- Modal Preview Transaksi -->
    <div class="modal fade" id="modalPreview" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 bg-light p-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                        <i class="feather icon-file-text me-2 text-primary"></i>Rincian Transaksi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 bg-light" id="previewContent">
                    <!-- Loaded via JS -->
                </div>
                <div class="modal-footer border-0 bg-white p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold"
                        data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-info text-white rounded-pill px-4 shadow-sm"
                        onclick="copyInvoiceText()">
                        <i class="fas fa-copy me-2"></i>Salin Teks
                    </button>
                    <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm" onclick="kirimWA()">
                        <i class="fab fa-whatsapp me-2"></i>Kirim WA
                    </button>
                    <button type="button" class="btn btn-primary rounded-pill px-5 py-2 shadow-lg fw-bold"
                        onclick="prosesBayarDariPreview()">
                        <i class="fas fa-credit-card me-2"></i>BAYAR SEKARANG
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>

<script>
    window.prosesBayarDariPreview = function () {
        if (!currentPreviewData || !currentPreviewData.nospk_encrypted) return;

        // Mengarahkan langsung ke halaman pembayaran menggunakan nospk yang ter-encrypt asli dari PHP
        window.location.href = "<?= base_url('transaksi/bayar/') ?>" + currentPreviewData.nospk_encrypted;
    }
</script>

</html>