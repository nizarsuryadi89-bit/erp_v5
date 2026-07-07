<!-- Premium Header -->
<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    :root {
        --tgm-primary: #4338ca;
        --tgm-success: #15803d;
        --tgm-danger: #b91c1c;
        --tgm-warning: #b45309;
        --tgm-bg: #f8fafc;
    }

    .premium-stat-card {
        background: white;
        border-radius: 20px;
        padding: 1.25rem;
        border: 1px solid #edf2f7;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
    }

    .premium-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .stat-icon-circle {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        margin-bottom: 0.75rem;
    }

    .stat-label {
        font-size: 0.65rem;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 0.25rem;
    }

    .stat-value {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 800;
        font-size: 1.35rem;
        letter-spacing: -0.03em;
        color: #1e293b;
    }

    .table-premium thead th {
        background: white;
        color: #64748b;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        padding: 1.25rem 1rem;
        border-bottom: 2px solid #f1f5f9;
    }

    .table-premium tbody td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }

    .konsumen-pill {
        width: 40px;
        height: 40px;
        background: #f0fdf4;
        color: var(--tgm-success);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 1.1rem;
    }

    .btn-action-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        background: white;
        color: #64748b;
        transition: all 0.2s;
    }

    .btn-action-circle:hover {
        background: #f8fafc;
        transform: scale(1.1);
    }

    .mono {
        font-family: 'JetBrains Mono', monospace;
    }
</style>

<div class="app-content py-4 bg-light">
    <!-- Slim Statistics -->
    <div class="container-fluid mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="premium-stat-card border-start border-4 border-success">
                    <div class="stat-icon-circle bg-success bg-opacity-10 text-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-label">Konsumen Terdata Lunas</div>
                    <div class="stat-value text-success"><?= $konsumen_lunas ?> <small class="fw-normal fs-6">USER</small></div>
                </div>
            </div>
            
            <div class="col-md-3">
                <!-- Placeholder for future stats or just empty space -->
            </div>
            <div class="col-md-3">
            </div>

            <div class="col-md-3">
                <div class="d-flex flex-column gap-2 h-100 justify-content-center">
                    <button class="btn btn-white rounded-4 py-2 fw-bold shadow-sm border text-success" data-bs-toggle="modal" data-bs-target="#modalPosLunas" style="font-size: 0.8rem;">
                        <i class="fas fa-file-shield me-2"></i> PANDUAN POS LUNAS
                    </button>
                    <a href="<?= base_url('transaksi/daftar_piutang') ?>" class="btn btn-primary rounded-4 py-2 fw-bold shadow-sm" style="font-size: 0.8rem;">
                        <i class="fas fa-arrow-left me-2"></i> DATA BELUM LUNAS
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main List Table -->
    <div class="container-fluid">
        <div class="card border-0 shadow-sm" style="border-radius: 20px;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-premium mb-0" id="datatable-piutang-lunas">
                        <thead>
                            <tr>
                                <th class="ps-4">Konsumen</th>
                                <th>Rekapitulasi Pelunasan</th>
                                <th class="text-center pe-4">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($piutang)): foreach ($piutang as $p): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="konsumen-pill">
                                                <?= substr($p->konsumen_nama, 0, 1) ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark fs-6 cursor-pointer" onclick="showDetails(<?= $p->konsumen_id ?>, '<?= addslashes($p->konsumen_nama) ?>')">
                                                    <?= $p->konsumen_nama ?>
                                                </div>
                                                <div class="small text-muted mono">
                                                    <i class="fas fa-phone-alt me-1"></i><?= $p->konsumen_nohp ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-4">
                                            <div>
                                                <div class="small text-muted fw-bold text-uppercase ls-1">Status Keuangan</div>
                                                <div class="stat-value text-success" style="font-size: 1.1rem;">TERBAYAR LUNAS</div>
                                            </div>
                                            <div class="border-start ps-4">
                                                <div class="small text-muted fw-bold text-uppercase ls-1">Volume Nota</div>
                                                <div class="mono fw-bold text-dark"><?= $p->jumlah_nota ?> Transaksi Selesai</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center pe-4">
                                        <button class="btn-action-circle text-primary" onclick="showDetails(<?= $p->konsumen_id ?>, '<?= addslashes($p->konsumen_nama) ?>')" title="Lihat Riwayat & Cetak">
                                            <i class="fas fa-history"></i>
                                        </button>
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

<!-- Detail Piutang Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-check-double me-2 text-success"></i>Riwayat Lunas: <span
                        id="detailCustomerName"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">No SPK</th>
                                <th>Tanggal Lunas</th>
                                <th class="text-end">Total Transaksi</th>
                                <th class="text-center pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="detailPiutangBody">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- POS Piutang Lunas Modal -->
<div class="modal fade" id="modalPosLunas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 25px;">
            <div class="modal-header border-0 pb-0 ps-4 pt-4">
                <h5 class="modal-title fw-900 text-success"><i class="fas fa-check-double me-2"></i>POS RIWAYAT PELUNASAN</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-4 h-100 border-start border-4 border-success">
                            <h6 class="fw-bold text-dark"><i class="fas fa-archive me-2 text-success"></i>1. Arsip & Verifikasi</h6>
                            <ul class="small text-muted ps-3 mb-0">
                                <li class="mb-2">Gunakan halaman ini untuk memverifikasi bahwa pembayaran konsumen benar-benar telah masuk ke sistem.</li>
                                <li class="mb-2">Klik ikon <b>History</b> untuk melihat tanggal pasti kapan pelunasan dilakukan.</li>
                                <li>Pastikan jumlah nota lunas sesuai dengan rekapitulasi harian kasir.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-4 h-100 border-start border-4 border-info">
                            <h6 class="fw-bold text-dark"><i class="fas fa-print me-2 text-info"></i>2. Cetak Ulang Invoice</h6>
                            <ul class="small text-muted ps-3 mb-0">
                                <li class="mb-2">Jika konsumen meminta bukti lunas di kemudian hari, Anda dapat mencetaknya melalui tombol <b>Cetak Invoice</b> di dalam modal detail.</li>
                                <li class="mb-2">Invoice lunas akan otomatis menampilkan status "LUNAS" atau "Sisa: 0".</li>
                                <li>Selalu gunakan printer thermal/standar sesuai kebutuhan konsumen.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="mt-4 p-3 border border-success border-opacity-25 bg-success bg-opacity-10 rounded-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle fa-2x text-success me-3"></i>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Standard Akurasi Data</h6>
                            <p class="small text-dark mb-0 opacity-75">Data di halaman ini adalah data final. Jika ditemukan ketidaksesuaian nominal antara sistem dan bukti transfer fisik, segera lapor ke bagian Accounting/Owner.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 text-center">
                <button type="button" class="btn btn-success rounded-pill px-5 fw-bold" data-bs-dismiss="modal">Pahami Prosedur</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        if ($('#datatable-piutang-lunas').length) {
            $('#datatable-piutang-lunas').DataTable({
                responsive: true,
                language: {
                    search: "",
                    searchPlaceholder: "Cari Nama Konsumen...",
                    lengthMenu: "_MENU_",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ Record",
                    paginate: { first: "First", last: "Last", next: "›", previous: "‹" },
                    emptyTable: "Tidak ada riwayat pelunasan"
                },
                pageLength: 25,
                columnDefs: [
                    { orderable: false, targets: [2] }
                ],
                dom: "<'row px-4 pt-4 mb-2'<'col-sm-6 d-flex align-items-center'l><'col-sm-6'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row p-4'<'col-sm-5'i><'col-sm-7'p>>",
                "drawCallback": function() {
                    $('.dataTables_filter input').addClass('form-control form-control-sm rounded-pill px-3 py-2 border-light shadow-sm bg-light');
                    $('.dataTables_length select').addClass('form-select form-select-sm rounded-pill px-3 border-light shadow-sm bg-light');
                    $('.paginate_button').addClass('rounded-circle');
                }
            });
        }
    });

    function showDetails(konsumenId, name) {
        $('#detailCustomerName').text(name);
        $('#detailPiutangBody').html('<tr><td colspan="4" class="text-center p-4"><i class="fas fa-spinner fa-spin me-2"></i>Memuat data...</td></tr>');

        const detailsModal = new bootstrap.Modal(document.getElementById('detailsModal'));
        detailsModal.show();

        $.ajax({
            url: '<?= base_url("transaksi/get_piutang_detail_ajax") ?>',
            type: 'POST',
            data: { konsumen_id: konsumenId },
            dataType: 'json',
            success: function (resp) {
                if (resp.success && resp.data.length > 0) {
                    let html = '';
                    const paidItems = resp.data.filter(item => parseFloat(item.sisa_piutang) <= 0);

                    if (paidItems.length > 0) {
                        paidItems.forEach(item => {
                            html += `
                                <tr>
                                    <td class="ps-3 fw-bold text-primary">${item.nospk}</td>
                                    <td class="small text-muted">${item.tgl_bayar}</td>
                                    <td class="text-end fw-bold mono text-success">Rp ${new Intl.NumberFormat('id-ID').format(item.total_transaksi)}</td>
                                    <td class="text-center pe-3">
                                        <a href="<?= base_url('transaksi/cetak_invoice_ulang/') ?>${item.nospk}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-3" title="Cetak Invoice">
                                            <i class="fas fa-print me-1"></i> Cetak Invoice
                                        </a>
                                    </td>
                                </tr>
                            `;
                        });
                        $('#detailPiutangBody').html(html);
                    } else {
                        $('#detailPiutangBody').html('<tr><td colspan="4" class="text-center p-4 text-muted">Tidak ada data lunas</td></tr>');
                    }
                } else {
                    $('#detailPiutangBody').html('<tr><td colspan="4" class="text-center p-4 text-muted">Tidak ada data</td></tr>');
                }
            },
            error: function () {
                $('#detailPiutangBody').html('<tr><td colspan="4" class="text-center p-4 text-danger">Gagal memuat data</td></tr>');
            }
        });
    }
</script>