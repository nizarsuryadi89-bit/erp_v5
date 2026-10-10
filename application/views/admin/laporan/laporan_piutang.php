<?php
/**
 * Laporan Piutang Konsumen - Integrated Dashboard Style
 * Displays consumer receivables with consistent styling and payment management.
 */
?>

<!-- DataTables Buttons & RowGroup -->
<link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.4.1/css/rowGroup.dataTables.min.css">
<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">

<link rel="stylesheet" href="<?= base_url('assets/css/tgm-laporan-piutang.css') ?>">

<div class="app-content">
    <!-- Header Modul (Consistent with Dashboard) -->
    <?php $this->load->view('layout_admin/header_modul'); ?>

    <div class="container-fluid">
        <!-- Branch Switcher Card (Dashboard Style) -->
        <?php if ($is_admin_owner && !empty($all_perusahaan)): ?>
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 0; border: 1px solid #e9ecef;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-1 fw-bold text-dark"><i class="fas fa-building me-2 text-primary"></i> Pilih
                                Cabang / Perusahaan</h5>
                            <p class="text-muted small mb-0">Tampilkan laporan piutang berdasarkan unit bisnis.</p>
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <form action="<?= base_url('laporan/laporan_piutang') ?>" method="GET" id="perusahaanForm">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0" style="border-radius: 0;"><i
                                            class="fas fa-store text-muted"></i></span>
                                    <select class="form-select border-start-0 ps-0 fw-bold" name="perusahaan_id"
                                        onchange="this.form.submit()" style="height: 50px; border-radius: 0;">
                                        <?php foreach ($all_perusahaan as $p): ?>
                                            <option value="<?= $p->id_perusahaan ?>"
                                                <?= ($current_perusahaan_id == $p->id_perusahaan) ? 'selected' : '' ?>>
                                                <?= $p->nama_perusahaan ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Statistics Overview -->
        <div class="stats-overview">
            <div class="stat-card info">
                <div class="stat-header">
                    <div class="stat-icon info">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= number_format($piutang_summary->total_piutang ?? 0) ?></h3>
                        <p>Total Nota Piutang</p>
                    </div>
                </div>
            </div>

            <div class="stat-card danger">
                <div class="stat-header">
                    <div class="stat-icon danger">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Rp <?= number_format($piutang_summary->total_sisa_piutang ?? 0, 0, ',', '.') ?></h3>
                        <p>Sisa Piutang</p>
                    </div>
                </div>
            </div>

            <div class="stat-card warning">
                <div class="stat-header">
                    <div class="stat-icon warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= number_format($piutang_summary->jatuh_tempo ?? 0) ?></h3>
                        <p>Jatuh Tempo</p>
                    </div>
                </div>
            </div>

            <div class="stat-card success">
                <div class="stat-header">
                    <div class="stat-icon success">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= number_format($piutang_summary->lunas ?? 0) ?></h3>
                        <p>Nota Lunas</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Ledger Table -->
        <div class="report-section-card">
            <div class="section-header no-print">
                <div class="d-flex align-items-center gap-2">
                    <h5><i class="fas fa-list-ul me-2"></i>Daftar Ledger Piutang</h5>
                </div>
                <div class="d-flex gap-2">
                    <div class="input-group" style="width: 250px;">
                        <input type="text" id="customSearch" class="form-control form-control-sm border-0"
                            placeholder="Cari konsumen/SPK..." style="border-radius: 0;">
                        <span class="input-group-text bg-light border-0" style="border-radius: 0;"><i
                                class="fa fa-search text-muted"></i></span>
                    </div>
                    <select class="form-select form-select-sm border-0" id="statusFilter"
                        style="width: 150px; border-radius: 0;">
                        <option value="">Semua Status</option>
                        <option value="BELUM LUNAS" <?= ($status_filter == 'BELUM LUNAS') ? 'selected' : '' ?>>Belum Lunas
                        </option>
                        <option value="SEBAGIAN" <?= ($status_filter == 'SEBAGIAN') ? 'selected' : '' ?>>Sebagian</option>
                        <option value="LUNAS" <?= ($status_filter == 'LUNAS') ? 'selected' : '' ?>>Lunas</option>
                    </select>
                    <button onclick="window.print()" class="btn btn-sm btn-light border-0" style="border-radius: 0;"><i
                            class="fas fa-print"></i></button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="piutangTable" class="table table-hover mb-0 w-100">
                    <thead>
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Konsumen</th>
                            <th class="text-end">Tagihan</th>
                            <th class="text-end">Terbayar</th>
                            <th class="text-end">Sisa Piutang</th>
                            <th class="text-center">No SPK</th>
                            <th class="text-center">Jatuh Tempo</th>
                            <th class="text-center">Status</th>
                            <th class="text-center no-print">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($piutang_list as $row): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= $row->konsumen_nama ?></div>
                                    <small class="text-muted"><i class="fas fa-phone-alt me-1"
                                            style="font-size: 0.7rem;"></i><?= $row->konsumen_nohp ?></small>
                                </td>
                                <td class="text-end">Rp <?= number_format($row->total_tagihan, 0, ',', '.') ?></td>
                                <td class="text-end text-success">Rp <?= number_format($row->total_bayar, 0, ',', '.') ?>
                                </td>
                                <td
                                    class="text-end fw-bold <?= ($row->sisa_piutang > 0) ? 'text-danger' : 'text-success' ?>">
                                    Rp <?= number_format($row->sisa_piutang, 0, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border fw-normal"
                                        style="font-size: 0.7rem;"><?= $row->nospk ?></span>
                                </td>
                                <td class="text-center">
                                    <small><?= date('d/m/y', strtotime($row->jatuh_tempo)) ?></small>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $cls = 'badge-belum-lunas';
                                    if ($row->status == 'LUNAS')
                                        $cls = 'badge-lunas';
                                    if ($row->status == 'SEBAGIAN')
                                        $cls = 'badge-sebagian';
                                    ?>
                                    <span class="status-badge <?= $cls ?>"><?= $row->status ?></span>
                                </td>
                                <td class="text-center no-print">
                                    <div class="d-flex justify-content-center">
                                        <button class="btn-action btn-history me-1" title="Riwayat Pembayaran"
                                            onclick="viewPaymentHistory('<?= $row->nospk ?>', '<?= $row->konsumen_nama ?>')">
                                            <i class="fas fa-history"></i>
                                        </button>
                                        <button class="btn-action btn-wa" title="Kirim Tagihan WA"
                                            onclick="sendPiutangWA('<?= $row->nospk ?>', '<?= $row->konsumen_nama ?>', '<?= $row->konsumen_nohp ?>', '<?= $row->sisa_piutang ?>', '<?= $row->jatuh_tempo ?>')">
                                            <i class="fab fa-whatsapp"></i>
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
</div>
</div>
</div>

<!-- Modal Riwayat -->
<div class="modal fade" id="historyModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 0;">
            <div class="modal-header bg-dark text-white" style="border-radius: 0;">
                <h5 class="modal-title"><i class="fas fa-history me-2"></i>Riwayat Pembayaran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="bg-light p-3 border-bottom">
                    <h6 id="historyCustomerName" class="fw-bold mb-1">Nama Konsumen</h6>
                    <small id="historyNospk" class="text-muted">No SPK: -</small>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3" width="50">No</th>
                                <th>Tanggal</th>
                                <th class="text-end">Jumlah</th>
                                <th>Metode</th>
                                <th>Ket</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                            <!-- JS Inject -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.datatables.net/rowgroup/1.4.1/js/dataTables.rowGroup.min.js"></script>

<script>
    $(document).ready(function () {
        const table = $('#piutangTable').DataTable({
            pageLength: 50,
            order: [[1, 'asc']],
            language: { url: '<?= base_url('assets/js/datatables-id.json') ?>' },
            dom: 'Brtip',
            rowGroup: {
                dataSrc: 1,
                startRender: function (rows, group) {
                    let totalSisa = rows.data().pluck(4).reduce((a, b) => {
                        let valA = typeof a === 'string' ? a.replace(/[^\d]/g, '') : a;
                        let valB = typeof b === 'string' ? b.replace(/[^\d]/g, '') : b;
                        return parseInt(valA) + parseInt(valB);
                    }, 0);

                    return $('<tr/>').append('<td colspan="9" class="bg-light shadow-sm"><div class="d-flex justify-content-between align-items-center"><span><i class="fas fa-user-circle me-2 text-primary"></i>' + group + '</span> <span class="badge bg-danger rounded-0 px-3 py-2">Total Piutang: Rp ' + totalSisa.toLocaleString('id-ID') + '</span></div></td>');
                }
            },
            columnDefs: [
                { visible: true, targets: 1 },
                { orderable: false, targets: [0, 8] }
            ],
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel me-1"></i> Excel',
                    className: 'btn btn-outline-success btn-sm rounded-0 me-1'
                }
            ]
        });

        $('#customSearch').on('keyup', function () { table.search(this.value).draw(); });
        $('#statusFilter').on('change', function () {
            if (this.value) table.column(7).search('^' + this.value + '$', true, false).draw();
            else table.column(7).search('').draw();
        });

    });

    function viewPaymentHistory(nospk, konsumen) {
        $('#historyCustomerName').text(konsumen);
        $('#historyNospk').text('No SPK: ' + nospk);
        $('#historyTableBody').html('<tr><td colspan="5" class="text-center py-4">Loading...</td></tr>');
        $('#historyModal').modal('show');

        $.ajax({
            url: '<?= base_url('laporan/get_payment_history') ?>',
            type: 'POST',
            data: { nospk: nospk },
            dataType: 'json',
            success: function (res) {
                if (res.success && res.data.length > 0) {
                    let html = '';
                    res.data.forEach((p, i) => {
                        html += `<tr>
                        <td class="ps-3">${i + 1}</td>
                        <td>${p.tanggal_bayar}</td>
                        <td class="text-end fw-bold">Rp ${parseInt(p.jumlah_bayar).toLocaleString('id-ID')}</td>
                        <td>${p.metode_bayar.toUpperCase()}</td>
                        <td><small>${p.keterangan || '-'}</small></td>
                    </tr>`;
                    });
                    $('#historyTableBody').html(html);
                } else {
                    $('#historyTableBody').html('<tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada riwayat.</td></tr>');
                }
            }
        });
    }

    function sendPiutangWA(nospk, nama, nohp, sisa, tempo) {
        if (!nohp) {
            Swal.fire('Error', 'Nomor WhatsApp konsumen tidak ditemukan.', 'error');
            return;
        }

        const sisaFormatted = parseInt(sisa).toLocaleString('id-ID');
        const tglTempo = new Date(tempo).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

        Swal.fire({
            title: 'Kirim Tagihan?',
            text: `Kirim pengingat piutang Rp ${sisaFormatted} ke ${nama}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#25D366',
            confirmButtonText: '<i class="fab fa-whatsapp me-2"></i>Kirim Sekarang'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Mengirim...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

                $.ajax({
                    url: '<?= base_url('laporan/send_piutang_wa') ?>',
                    type: 'POST',
                    data: { nospk: nospk, nama: nama, nohp: nohp, sisa: sisa, tempo: tglTempo },
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            Swal.fire('Terkirim!', 'Tagihan berhasil dikirim via WA.', 'success');
                        } else {
                            Swal.fire('Gagal!', res.message, 'error');
                        }
                    },
                    error: function () { Swal.fire('Error!', 'Gagal menghubungkan ke server.', 'error'); }
                });
            }
        });
    }
</script>

<style>
    .btn-wa { background: #25D366; color: white; border: none; }
    .btn-wa:hover { background: #128C7E; color: white; }
</style>