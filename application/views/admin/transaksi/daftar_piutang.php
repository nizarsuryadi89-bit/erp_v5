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
        background: #eef2ff;
        color: var(--tgm-primary);
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

    .btn-pay-all {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .btn-wa {
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #dcfce7;
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
                <div class="premium-stat-card border-start border-4 border-primary">
                    <div class="stat-icon-circle bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-label">Konsumen Berpiutang</div>
                    <div class="stat-value"><?= $konsumen_berpiutang ?> <small class="fw-normal fs-6">USER</small></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="premium-stat-card border-start border-4 border-danger">
                    <div class="stat-icon-circle bg-danger bg-opacity-10 text-danger">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <div class="stat-label">Total Dana Tertahan</div>
                    <div class="stat-value text-danger">Rp <?= number_format($total_piutang, 0, ',', '.') ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="premium-stat-card border-start border-4 border-warning">
                    <div class="stat-icon-circle bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <?php 
                        $total_nota = 0;
                        if (!empty($piutang)) {
                            foreach ($piutang as $p) {
                                $total_nota += $p->jumlah_nota;
                            }
                        }
                    ?>
                    <div class="stat-label">Akumulasi Nota</div>
                    <div class="stat-value"><?= $total_nota ?> <small class="fw-normal fs-6">BILL</small></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-flex flex-column gap-2 h-100 justify-content-center">
                    <button class="btn btn-white rounded-4 py-2 fw-bold shadow-sm border text-primary"
                        data-bs-toggle="modal" data-bs-target="#modalSopPiutang" style="font-size: 0.8rem;">
                        <i class="fas fa-book-open me-2"></i> PANDUAN SOP PENAGIHAN
                    </button>
                    <a href="<?= base_url('transaksi/daftar_piutang_lunas') ?>"
                        class="btn btn-dark rounded-4 py-2 fw-bold shadow-sm" style="font-size: 0.8rem;">
                        <i class="fas fa-history me-2 text-warning"></i> LIHAT RIWAYAT LUNAS
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
                    <table class="table table-premium mb-0" id="datatable-piutang">
                        <thead>
                            <tr>
                                <th class="ps-4">Identitas Konsumen</th>
                                <th>Status Tagihan</th>
                                <th class="text-center pe-4">Opsi Kontrol</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($piutang)):
                                foreach ($piutang as $p): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="konsumen-pill">
                                                    <?= substr($p->konsumen_nama, 0, 1) ?>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-6 cursor-pointer"
                                                        onclick="showDetails(<?= $p->konsumen_id ?>, '<?= addslashes($p->konsumen_nama) ?>')">
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
                                                    <div class="small text-muted fw-bold text-uppercase ls-1">Piutang Akumulatif
                                                    </div>
                                                    <div class="stat-value text-danger">Rp
                                                        <?= number_format($p->total_piutang, 0, ',', '.') ?></div>
                                                </div>
                                                <div class="border-start ps-4">
                                                    <div class="small text-muted fw-bold text-uppercase ls-1">Volume Nota</div>
                                                    <div class="mono fw-bold text-dark"><?= $p->jumlah_nota ?> Transaksi</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center pe-4">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn-action-circle text-primary"
                                                    onclick="showDetails(<?= $p->konsumen_id ?>, '<?= addslashes($p->konsumen_nama) ?>')"
                                                    title="Breakdown Nota">
                                                    <i class="fas fa-search-dollar"></i>
                                                </button>
                                                <button class="btn-action-circle btn-pay-all"
                                                    onclick="showCollectivePayment(<?= $p->konsumen_id ?>, '<?= addslashes($p->konsumen_nama) ?>', <?= $p->total_piutang ?>)"
                                                    title="Pelunasan Massal">
                                                    <i class="fas fa-receipt"></i>
                                                </button>
                                                <a href="https://api.whatsapp.com/send?phone=<?= preg_replace('/[^0-9]/', '', $p->konsumen_nohp) ?>&text=Halo <?= $p->konsumen_nama ?>, kami dari TGM ingin menginformasikan sisa tagihan Anda sebesar Rp <?= number_format($p->total_piutang, 0, ',', '.') ?>. Mohon bantuannya untuk segera melakukan pelunasan. Terima kasih."
                                                    class="btn-action-circle btn-wa" target="_blank" title="Kirim Pengingat WA">
                                                    <i class="fab fa-whatsapp"></i>
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
</div>

<!-- Detail Piutang Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Detail Piutang: <span
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
                                <th>Tanggal</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Sisa</th>
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

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-cash-register me-2 text-primary"></i>Pembayaran Piutang
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="paymentForm" onsubmit="processPayment(event)">
                <div class="modal-body p-4">
                    <!-- Summary Card -->
                    <div class="alert alert-light border d-flex align-items-center mb-4" role="alert">
                        <i class="fas fa-user-circle fa-2x me-3 text-secondary"></i>
                        <input type="hidden" id="isCollective" value="0">
                        <div>
                            <div class="fw-bold text-dark" id="customerName">Nama Konsumen</div>
                            <div class="d-flex align-items-center gap-3 small text-muted">
                                <span id="nospkContainer"><i class="fas fa-file-alt me-1"></i> <span
                                        id="nospk">-</span></span>
                                <span class="text-danger fw-bold">Sisa: <span id="totalReceivable">Rp 0</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Jumlah Pembayaran <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">Rp</span>
                            <input type="number" class="form-control form-control-lg fw-bold text-primary"
                                id="paymentAmount" name="payment_amount" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Metode Pembayaran</label>
                        <select class="form-select" id="paymentMethod" name="payment_method" required
                            onchange="togglePaymentSubFields()">
                            <option value="cash">Tunai</option>
                            <option value="transfer">Transfer Bank</option>
                            <option value="qris">QRIS / E-Wallet</option>
                            <option value="edc">EDC / Mesin Debit</option>
                        </select>
                    </div>

                    <!-- Sub Fields for Bank Transfer -->
                    <div class="mb-3 d-none" id="bankField">
                        <label class="form-label small fw-bold">Bank Penerima <span class="text-danger">*</span></label>
                        <select class="form-select" id="bankId" name="bank_id">
                            <option value="">-- Pilih Bank --</option>
                            <?php foreach ($banks as $b): ?>
                                <option value="<?= $b->id ?>"><?= $b->nama_bank ?> - <?= $b->no_rekening ?> (a/n
                                    <?= $b->atas_nama ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Sub Fields for EDC -->
                    <div class="mb-3 d-none" id="edcField">
                        <label class="form-label small fw-bold">Mesin EDC <span class="text-danger">*</span></label>
                        <select class="form-select" id="edcId" name="edc_id">
                            <option value="">-- Pilih EDC --</option>
                            <?php foreach ($edcs as $e): ?>
                                <option value="<?= $e->edc_id ?>"><?= $e->edc_nama ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Sub Fields for E-Wallet -->
                    <div class="mb-3 d-none" id="walletField">
                        <label class="form-label small fw-bold">E-Wallet <span class="text-danger">*</span></label>
                        <select class="form-select" id="walletId" name="wallet_id">
                            <option value="">-- Pilih E-Wallet --</option>
                            <?php foreach ($wallets as $w): ?>
                                <option value="<?= $w->id ?>"><?= $w->ewallet_nama ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Catatan</label>
                        <textarea class="form-control" id="paymentNotes" rows="2"
                            placeholder="Catatan pembayaran..."></textarea>
                    </div>

                    <input type="hidden" id="paymentDate" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-link text-muted text-decoration-none"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4" id="submitPayment">
                        <i class="fas fa-check-circle me-2"></i> Proses Bayar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SOP Piutang Modal -->
<div class="modal fade" id="modalSopPiutang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 25px;">
            <div class="modal-header border-0 pb-0 ps-4 pt-4">
                <h5 class="modal-title fw-900"><i class="fas fa-shield-alt text-primary me-2"></i>SOP MANAJEMEN PIUTANG
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-4 h-100">
                            <h6 class="fw-bold text-dark"><i class="fas fa-search-dollar me-2 text-primary"></i>1.
                                Monitoring & Penagihan</h6>
                            <ul class="small text-muted ps-3 mb-0">
                                <li class="mb-2">Gunakan tombol <b>Kaca Pembesar</b> untuk melihat rincian nota mana
                                    saja yang belum lunas.</li>
                                <li class="mb-2">Gunakan tombol <b>WhatsApp</b> untuk mengirim pesan pengingat otomatis
                                    ke nomor konsumen.</li>
                                <li>Pastikan data piutang diperiksa minimal setiap sore sebelum tutup buku toko.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-4 h-100">
                            <h6 class="fw-bold text-dark"><i class="fas fa-cash-register me-2 text-success"></i>2. Input
                                Pembayaran</h6>
                            <ul class="small text-muted ps-3 mb-0">
                                <li class="mb-2">Pelunasan nota tunggal dilakukan melalui modal detail (klik ikon kaca
                                    pembesar).</li>
                                <li class="mb-2">Gunakan tombol <b>Pelunasan Massal (Ikon Nota)</b> jika konsumen
                                    membayar semua tagihannya sekaligus.</li>
                                <li>Pilih metode pembayaran (Cash/Transfer/EDC) dengan teliti agar laporan kas sinkron.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="mt-4 p-3 border border-warning border-opacity-25 bg-warning bg-opacity-10 rounded-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning me-3"></i>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Catatan Penting Keuangan</h6>
                            <p class="small text-dark mb-0 opacity-75">Dilarang menghapus invoice yang sudah ada
                                piutangnya tanpa seizin Owner. Gunakan fitur koreksi jika terjadi kesalahan input.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4">
                <button type="button" class="btn btn-primary rounded-pill px-5 fw-bold" data-bs-dismiss="modal">Saya
                    Mengerti</button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentCustomerId = null;
    let currentTotalReceivable = 0;
    let currentNospk = null;

    $(document).ready(function () {
        if ($('#datatable-piutang').length) {
            $('#datatable-piutang').DataTable({
                responsive: true,
                language: {
                    search: "",
                    searchPlaceholder: "Cari Konsumen / Nomor SPK...",
                    lengthMenu: "_MENU_",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ Konsumen",
                    paginate: { first: "First", last: "Last", next: "›", previous: "‹" },
                    emptyTable: "Tidak ada data piutang"
                },
                pageLength: 25,
                columnDefs: [
                    { orderable: false, targets: [2] }
                ],
                dom: "<'row px-4 pt-4 mb-2'<'col-sm-6 d-flex align-items-center'l><'col-sm-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row p-4'<'col-sm-5'i><'col-sm-7'p>>",
                "drawCallback": function () {
                    $('.dataTables_filter input').addClass('form-control form-control-sm rounded-pill px-3 py-2 border-light shadow-sm bg-light');
                    $('.dataTables_length select').addClass('form-select form-select-sm rounded-pill px-3 border-light shadow-sm bg-light');
                    $('.paginate_button').addClass('rounded-circle');
                }
            });
        }
    });

    function showDetails(konsumenId, name) {
        $('#detailCustomerName').text(name);
        $('#detailPiutangBody').html('<tr><td colspan="5" class="text-center p-4"><i class="fas fa-spinner fa-spin me-2"></i>Memuat data...</td></tr>');

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
                    resp.data.forEach(item => {
                        let btnAksi = '';
                        if (parseFloat(item.sisa_piutang) <= 0) {
                            // Jika lunas, tampilkan tombol cetak invoice
                            btnAksi = `
                                <a href="<?= base_url('transaksi/cetak_invoice_ulang/') ?>${item.nospk}" target="_blank" class="btn btn-sm btn-info text-white" title="Cetak Invoice Lunas">
                                    <i class="fas fa-print"></i>
                                </a>
                            `;
                        } else {
                            // Jika belum lunas, tampilkan tombol bayar
                            btnAksi = `
                                <button class="btn btn-sm btn-success" onclick="showPaymentFromDetail('${item.nospk}', '${name}', ${item.sisa_piutang}, ${konsumenId})">
                                    <i class="fas fa-wallet"></i>
                                </button>
                            `;
                        }

                        html += `
                            <tr>
                                <td class="ps-3 fw-bold text-primary">${item.nospk}</td>
                                <td>${item.tgl_bayar}</td>
                                <td class="text-end">Rp ${new Intl.NumberFormat('id-ID').format(item.total_transaksi)}</td>
                                <td class="text-end fw-bold text-danger">Rp ${new Intl.NumberFormat('id-ID').format(item.sisa_piutang)}</td>
                                <td class="text-center pe-3">
                                    ${btnAksi}
                                </td>
                            </tr>
                        `;
                    });
                    $('#detailPiutangBody').html(html);
                } else {
                    $('#detailPiutangBody').html('<tr><td colspan="5" class="text-center p-4 text-muted">Tidak ada data piutang</td></tr>');
                }
            },
            error: function () {
                $('#detailPiutangBody').html('<tr><td colspan="5" class="text-center p-4 text-danger">Gagal memuat data</td></tr>');
            }
        });
    }

    function showPaymentFromDetail(nospk, name, total, konsumenId) {
        // Close detail modal first
        bootstrap.Modal.getInstance(document.getElementById('detailsModal')).hide();

        // Use timeout to ensure detailsModal is closed before opening paymentModal
        setTimeout(() => {
            showPaymentModal(konsumenId, name, total, nospk);
        }, 300);
    }

    function togglePaymentSubFields() {
        const method = $('#paymentMethod').val();

        // Hide all first
        $('#bankField, #edcField, #walletField').addClass('d-none');
        $('#bankId, #edcId, #walletId').prop('required', false);

        if (method === 'transfer') {
            $('#bankField').removeClass('d-none');
            $('#bankId').prop('required', true);
        } else if (method === 'edc') {
            $('#edcField').removeClass('d-none');
            $('#edcId').prop('required', true);
        } else if (method === 'qris') {
            $('#walletField').removeClass('d-none');
            $('#walletId').prop('required', true);
        }
    }

    function showCollectivePayment(konsumenId, customerName, totalPiutang) {
        currentCustomerId = konsumenId;
        currentTotalReceivable = parseFloat(totalPiutang);
        currentNospk = null;

        $('#isCollective').val('1');
        $('#customerName').text(customerName);
        $('#nospkContainer').hide();
        $('#totalReceivable').text('Rp ' + new Intl.NumberFormat('id-ID').format(currentTotalReceivable));

        $('#paymentAmount').val(currentTotalReceivable);
        $('#paymentNotes').val('Pelunasan Semua Piutang (Kolektif)');
        $('#paymentMethod').val('cash');
        togglePaymentSubFields();

        new bootstrap.Modal(document.getElementById('paymentModal')).show();
    }

    function showPaymentModal(konsumenId, customerName, totalReceivable, nospk) {
        currentCustomerId = konsumenId;
        currentTotalReceivable = parseFloat(totalReceivable);
        currentNospk = nospk;

        $('#isCollective').val('0');
        $('#customerName').text(customerName);
        $('#nospkContainer').show();
        $('#nospk').text(nospk);
        $('#totalReceivable').text('Rp ' + new Intl.NumberFormat('id-ID').format(currentTotalReceivable));

        $('#paymentAmount').val(currentTotalReceivable);
        $('#paymentNotes').val('Pelunasan ' + nospk);
        $('#paymentMethod').val('cash');
        togglePaymentSubFields();

        new bootstrap.Modal(document.getElementById('paymentModal')).show();
    }

    function processPayment(event) {
        event.preventDefault();

        const amount = parseFloat($('#paymentAmount').val());
        const isCollective = $('#isCollective').val() === '1';

        if (!amount || amount <= 0) {
            Swal.fire('Error', 'Jumlah pembayaran tidak valid', 'warning');
            return;
        }
        if (amount > currentTotalReceivable) {
            Swal.fire('Error', 'Pembayaran melebihi sisa piutang', 'warning');
            return;
        }

        const btn = $('#submitPayment');
        const originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');

        const url = isCollective ? '<?= base_url("transaksi/process_payment_collective") ?>' : '<?= base_url("transaksi/process_payment") ?>';

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                konsumen_id: currentCustomerId,
                nospk: currentNospk,
                payment_amount: amount,
                payment_method: $('#paymentMethod').val(),
                bank_id: $('#bankId').val(),
                edc_id: $('#edcId').val(),
                wallet_id: $('#walletId').val(),
                payment_date: $('#paymentDate').val(),
                payment_notes: $('#paymentNotes').val()
            },
            dataType: 'json',
            success: function (resp) {
                if (resp.success) {
                    Swal.fire('Berhasil', 'Pembayaran berhasil disimpan', 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Gagal', resp.message || 'Gagal menyimpan pembayaran', 'error');
                    btn.prop('disabled', false).html(originalText);
                }
            },
            error: function () {
                Swal.fire('Error', 'Terjadi kesalahan server', 'error');
                btn.prop('disabled', false).html(originalText);
            }
        });
    }
</script>