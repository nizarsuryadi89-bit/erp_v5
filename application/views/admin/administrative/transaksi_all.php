<?php
/**
 * Master Log Transaksi - TGM Premium Edition (Simplified Audit)
 * Optimized for Performance
 */
$total_omzet = 0;
$total_bayar = 0;
$total_piutang = 0;
$total_anomali = 0;

foreach ($transaksi as $tr) {
    $total_omzet += $tr->grand_total;
    $total_bayar += $tr->total_bayar;
    $total_piutang += $tr->kurang_bayar;
    if ($tr->anomali)
        $total_anomali++;
}
$this->load->view('layout_admin/header_modul');
?>

<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">
<style>
    :root {
        --tgm-indigo: #3730a3;
        --tgm-emerald: #065f46;
        --tgm-rose: #9f1239;
        --tgm-slate: #1e293b;
        --tgm-border: #f1f5f9;
    }

    .clean-card {
        border: none;
        border-radius: 16px;
        background: white;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    }

    .table-simple thead th {
        background: white;
        color: #64748b;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        padding: 16px 12px;
        border-bottom: 2px solid var(--tgm-border);
        white-space: nowrap;
    }

    .table-simple tbody td {
        padding: 18px 12px;
        vertical-align: middle;
        border-bottom: 1px solid var(--tgm-border);
        color: var(--tgm-slate);
        font-size: 0.85rem;
    }

    .font-mono {
        font-family: 'JetBrains Mono', 'Fira Code', monospace !important;
        font-size: 0.85rem;
    }

    .fw-900 {
        font-weight: 900;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 0.65rem;
        font-weight: 800;
        letter-spacing: 0.02em;
    }

    .anomali-indicator {
        background: rgba(244, 63, 94, 0.05);
    }

    .btn-action {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: all 0.2s;
        border: 1px solid #e2e8f0;
        background: white;
        color: #64748b;
        cursor: pointer;
    }

    .btn-action:hover {
        background: #f1f5f9;
        color: var(--tgm-indigo);
        transform: translateY(-2px);
    }

    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>

<div class="app-content py-4">

    <!-- Minimal Header -->
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-4 no-print">
            <div>
                <h3 class="fw-900 mb-0 ls-1">AUDIT <span class="text-indigo">TRANSAKSI</span></h3>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <p class="small text-muted mb-0">Monitor sirkulasi dana SPK dan anomali input.</p>
                    <span class="badge bg-rose bg-opacity-10 text-rose border border-rose border-opacity-25 px-2 py-1"
                        style="font-size: 0.6rem; letter-spacing: 0.05em;">
                        <i class="fas fa-lock me-1"></i> OWNER & ADMIN ONLY
                    </span>
                </div>
            </div>
            <button class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal"
                data-bs-target="#modalSopAudit">
                <i class="fas fa-shield-alt me-2 text-warning"></i> PROSEDUR AUDIT
            </button>
        </div>
    </div>


    <!-- Filter Minimal -->
    <div class="clean-card p-4 mb-4 no-print border">
        <form action="<?= base_url('administrative/transaksi') ?>" method="GET" class="row g-3">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted small fw-bold">PERIODE
                        TANGGAL</span>
                    <input type="date" name="start_date" class="form-control border-start-0 ps-0"
                        value="<?= $start_date ?>">
                    <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
                </div>
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select select2">
                    <option value="all" <?= $status_filter == 'all' ? 'selected' : '' ?>>SEMUA STATUS BAYAR (
                        <?= $stats['total_count'] ?>)
                    </option>
                    <option value="paid" <?= $status_filter == 'paid' ? 'selected' : '' ?>>SUDAH BAYAR (
                        <?= $stats['paid_count'] ?>)
                    </option>
                    <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>BELUM BAYAR (
                        <?= $stats['pending_count'] ?>)
                    </option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-indigo w-100 fw-bold py-2 rounded-3 shadow">
                    <i class="fas fa-filter me-2"></i> SEARCH DATA
                </button>
            </div>
        </form>
    </div>

    <!-- Stat Row -->
    <div class="row g-3 mb-4 no-print">
        <div class="col-md-3">
            <div class="clean-card p-3 border-start border-4 border-indigo">
                <div class="small fw-bold text-muted mb-1 text-uppercase">Total SPK</div>
                <div class="h5 fw-900 font-mono mb-0">Rp <?= number_format($total_omzet, 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="clean-card p-3 border-start border-4 border-success">
                <div class="small fw-bold text-muted mb-1 text-uppercase">Total Masuk</div>
                <div class="h5 fw-900 font-mono text-emerald mb-0">Rp
                    <?= number_format($total_bayar, 0, ',', '.') ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="clean-card p-3 border-start border-4 border-warning">
                <div class="small fw-bold text-muted mb-1 text-uppercase">Total Piutang</div>
                <div class="h5 fw-900 font-mono text-warning mb-0">Rp
                    <?= number_format($total_piutang, 0, ',', '.') ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="clean-card p-3 border-start border-4 border-rose">
                <div class="small fw-bold text-muted mb-1 text-uppercase">Baris Anomali</div>
                <div class="h5 fw-900 font-mono text-rose mb-0"><?= $total_anomali ?> MISMATCH</div>
            </div>
        </div>
    </div>

    <!-- Slim Full-Channel Audit Table -->
    <div class="clean-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-simple mb-0" id="auditTable">
                <thead>
                    <tr>
                        <th class="ps-4">No. SPK / Tanggal</th>
                        <th>Konsumen</th>
                        <th class="text-end">Tagihan</th>
                        <th class="text-end text-emerald">Cash</th>
                        <th class="text-end text-info">Transfer</th>
                        <th class="text-end text-dark">EDC</th>
                        <th class="text-end text-violet">E-Wallet</th>
                        <th class="text-end text-warning">Piutang</th>
                        <th class="text-center">Status</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transaksi as $tr):
                        $status_class = ($tr->status_pembayaran == 'paid') ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger';
                        ?>
                        <tr class="<?= $tr->anomali ? 'anomali-indicator' : '' ?>">
                            <td class="ps-4">
                                <div class="fw-bold text-dark">#<?= $tr->nospk ?></div>
                                <div class="x-small text-muted" style="font-size: 0.65rem;">
                                    <?= date('d/m/y H:i', strtotime($tr->created_at)) ?>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark text-truncate" style="max-width: 120px;">
                                    <?= $tr->konsumen_nama ?>
                                </div>
                            </td>
                            <td class="text-end fw-bold font-mono">Rp <?= number_format($tr->grand_total, 0, ',', '.') ?>
                            </td>
                            <td class="text-end font-mono text-emerald fw-bold">
                                <?= ($tr->bayar_tunai > 0) ? number_format($tr->bayar_tunai, 0, ',', '.') : '<span class="opacity-25">-</span>' ?>
                            </td>
                            <td class="text-end font-mono text-info fw-bold">
                                <?= ($tr->bayar_transfer > 0) ? number_format($tr->bayar_transfer, 0, ',', '.') : '<span class="opacity-25">-</span>' ?>
                            </td>
                            <td class="text-end font-mono text-dark fw-bold">
                                <?= ($tr->bayar_edc > 0) ? number_format($tr->bayar_edc, 0, ',', '.') : '<span class="opacity-25">-</span>' ?>
                            </td>
                            <td class="text-end font-mono text-violet fw-bold">
                                <?= ($tr->bayar_ewallet > 0) ? number_format($tr->bayar_ewallet, 0, ',', '.') : '<span class="opacity-25">-</span>' ?>
                            </td>
                            <td
                                class="text-end font-mono <?= ($tr->kurang_bayar > 0) ? 'text-warning fw-900 border-start border-warning border-opacity-10 bg-warning bg-opacity-5' : 'text-muted opacity-50' ?>">
                                <?= ($tr->kurang_bayar > 0) ? number_format($tr->kurang_bayar, 0, ',', '.') : '-' ?>
                            </td>
                            <td class="text-center">
                                <span
                                    class="status-badge <?= $status_class ?>"><?= strtoupper($tr->status_pembayaran) ?></span>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn-action" onclick="loadKoreksi(this)" data-spk="<?= $tr->nospk ?>"
                                        title=" Koreksi">
                                        <i class="fas fa-wrench"></i>
                                    </button>
                                    <a href="<?= base_url('transaksi/cetak_invoice_ulang/' . $tr->nospk) ?>" target="_blank"
                                        class="btn-action" title="Print">
                                        <i class="fas fa-print"></i>
                                    </a>
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

<!-- Modern Digital Audit SOP -->
<div class="modal fade" id="modalSopAudit" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-3"
                        style="width: 40px; height: 40px;">
                        <i class="fas fa-shield-alt text-dark"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-900 ls-1 mb-0 uppercase">Standard Operating Procedure</h5>
                        <p class="small text-muted mb-0 opacity-75">Digital Audit & Monitoring Transaksi</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-4 bg-light border-end p-4">
                        <h6 class="fw-900 text-dark mb-4 ls-1">TUJUAN MODUL</h6>
                        <ul class="list-unstyled small text-muted lh-lg">
                            <li class="mb-3 d-flex">
                                <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                <span>Menyamakan catatan kasir dengan uang riil di bank/laci.</span>
                            </li>
                            <li class="mb-3 d-flex">
                                <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                <span>Mendeteksi kebocoran dana atau salah input secara dini.</span>
                            </li>
                            <li class="mb-3 d-flex">
                                <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                <span>Memastikan status **PAID** benar-benar memiliki dana masuk.</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-8 p-4">
                        <div class="mb-4">
                            <h6 class="fw-900 text-indigo mb-3 ls-1 mt-2">1. CARA MENDETEKSI ANOMALI</h6>
                            <div class="p-3 rounded-3 border-start border-4 border-rose bg-rose bg-opacity-5 mb-3">
                                <p class="small fw-bold text-dark mb-1">Row Berwarna Merah / Mismatch</p>
                                <p class="x-small text-muted mb-0">Terjadi jika **Tagihan Dealer** ≠ **Total Bayar
                                    Finance**. Kasir mungkin lupa menginput breakdown pembayaran atau ada kesalahan
                                    nominal.</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-900 text-indigo mb-3 ls-1">2. ALUR VERIFIKASI (SCAN > VERIFY > FIX)</h6>
                            <div class="timeline-sop small ps-3 border-start border-2 border-slate">
                                <div class="mb-3 position-relative">
                                    <div class="fw-bold text-dark">Langkah 1: Cek Mutasi Digital</div>
                                    <p class="text-muted mb-0">Periksa kolom **EDC** & **WALLET**. Cocokan dengan
                                        print-out mesin atau dashboard Midtrans/E-wallet Anda.</p>
                                </div>
                                <div class="mb-3 position-relative">
                                    <div class="fw-bold text-dark">Langkah 2: Gunakan Tombol Koreksi <i
                                            class="fas fa-wrench text-warning"></i></div>
                                    <p class="text-muted mb-0">Klik tombol kunci inggris untuk memperbaiki data jika
                                        ditemukan ketidaksinkronan.</p>
                                </div>
                                <div class="mb-0 position-relative">
                                    <div class="fw-bold text-dark">Langkah 3: Sinkronisasi Status</div>
                                    <p class="text-muted mb-0">Jangan mengubah status menjadi **PAID** jika sisa
                                        **PIUTANG** belum Rp 0 di sistem.</p>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning border-0 rounded-3 d-flex align-items-center mb-0">
                            <i class="fas fa-exclamation-triangle fs-4 me-3"></i>
                            <div class="small fw-bold">PENTING: Audit dilakukan setiap hari sebelum tutup toko (EOY)
                                untuk mencegah tagihan menggantung di hari berikutnya.</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 bg-white">
                <button type="button" class="btn btn-dark w-100 rounded-pill fw-900 py-3 shadow"
                    data-bs-dismiss="modal">SAYA MENGERTI & SIAP AUDIT</button>
            </div>
        </div>
    </div>
</div>

<style>
    .timeline-sop>div:before {
        content: "";
        position: absolute;
        left: -21px;
        top: 5px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--tgm-indigo);
        border: 2px solid white;
    }
</style>

<!-- Modal PIN Auth -->
<div class="modal fade" id="modalPinKoreksi" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white p-4 justify-content-center border-0 position-relative">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                <div class="text-center">
                    <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2"
                        style="width: 50px; height: 50px;">
                        <i class="fas fa-lock text-dark fs-4"></i>
                    </div>
                    <h6 class="modal-title fw-900 ls-1 mb-0 uppercase">AUTHORIZATION</h6>
                </div>
            </div>
            <div class="modal-body p-4 bg-light text-center">
                <p class="small text-muted mb-3">Masukkan PIN (Admin/Owner) untuk membuka akses mode koreksi transaksi.</p>
                <input type="password" id="input_pin_koreksi" class="form-control form-control-lg text-center fw-bold fs-4 mb-3"
                    placeholder="••••" maxlength="6" autocomplete="new-password">
                <button type="button" id="btnVerifyPin" class="btn btn-dark w-100 rounded-pill fw-900 shadow py-2"
                    onclick="verifyPinKoreksi()">
                    VERIFIKASI PIN
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Koreksi (Simplified) -->
<div class="modal fade" id="modalKoreksi" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="<?= base_url('administrative/update_pembayaran') ?>" method="POST"
            class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white p-4">
                <h5 class="modal-title fw-900 ls-1 mb-0"><i class="fas fa-wrench me-2 text-warning"></i>KOREKSI #<span
                        id="label_spk"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <input type="hidden" name="nospk" id="edit_nospk">

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-white rounded-3 border h-100">
                            <label class="small text-muted fw-bold d-block mb-1">TAGIHAN SPK</label>
                            <h4 class="fw-900 font-mono mb-0" id="summ_total_spk">Rp 0</h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-white rounded-3 border h-100 border-start border-4 border-success"
                            id="summ_status_border">
                            <label class="small text-muted fw-bold d-block mb-1">TOTAL BAYAR (KASIR)</label>
                            <h4 class="fw-900 font-mono text-emerald mb-0" id="summ_total_bayar">Rp 0</h4>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="small fw-bold text-muted mb-2">STATUS PEMBAYARAN</label>
                        <select name="status_pembayaran" id="edit_status" class="form-select select2">
                            <option value="pending">PENDING (BELUM LUNAS)</option>
                            <option value="paid">PAID (LUNAS)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold text-muted mb-2">METODE TRANSAKSI</label>
                        <select name="id_jenis_transaksi" id="edit_jenis" class="form-select select2">
                            <?php foreach ($jenis_transaksi as $jt): ?>
                                <option value="<?= $jt->id_jenis_transaksi ?>"><?= $jt->nama_jenis_transaksi ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 mt-4">
                        <h6 class="fw-bold border-bottom pb-2">INPUT NOMINAL BREAKDOWN</h6>
                    </div>

                    <div class="col-md-4">
                        <label class="x-small fw-bold text-muted mb-1 d-block">TUNAI/CASH</label>
                        <input type="number" name="bayar_tunai" id="edit_tunai"
                            class="form-control calc-input fw-bold font-mono">
                    </div>
                    <div class="col-md-4">
                        <label class="x-small fw-bold text-muted mb-1 d-block">BANK TRANSFER</label>
                        <input type="number" name="bayar_transfer" id="edit_transfer"
                            class="form-control calc-input fw-bold font-mono">
                    </div>
                    <div class="col-md-4">
                        <label class="x-small fw-bold text-muted mb-1 d-block">E-WALLET</label>
                        <input type="number" name="bayar_ewallet" id="edit_ewallet"
                            class="form-control calc-input fw-bold font-mono">
                    </div>
                    <div class="col-md-6">
                        <label class="x-small fw-bold text-muted mb-1 d-block">DEBIT/CREDIT (EDC)</label>
                        <input type="number" name="bayar_debit" id="edit_debit"
                            class="form-control calc-input fw-bold font-mono">
                    </div>
                    <div class="col-md-6">
                        <label class="x-small fw-bold text-muted mb-1 d-block">CATATAN AUDIT</label>
                        <input type="text" name="catatan" id="edit_catatan" class="form-control"
                            placeholder="Alasan koreksi data...">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 bg-white">
                <button type="button" class="btn btn-light px-4 rounded-pill fw-bold"
                    data-bs-dismiss="modal">BATAL</button>
                <button type="submit" class="btn btn-dark px-5 rounded-pill fw-900 shadow">SIMPAN HASIL AUDIT</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.select2').each(function () {
            var $this = $(this);
            var parent = $this.parent();
            $this.select2({
                theme: 'bootstrap-5',
                width: '100%',
                dropdownParent: parent.hasClass('modal-body') ? parent : null
            });
        });

        $('#auditTable').DataTable({
            "order": [],
            "pageLength": 25,
            "language": {
                "search": "",
                "searchPlaceholder": "Cari SPK / Konsumen / Catatan...",
                "info": "AUDIT BARIS _START_ - _END_ DARI _TOTAL_",
                "lengthMenu": "TAMPILKAN _MENU_",
                "emptyTable": "Tidak ada data transaksi",
                "zeroRecords": "Data tidak ditemukan",
                "paginate": {
                    "previous": "<i class='fas fa-chevron-left'></i>",
                    "next": "<i class='fas fa-chevron-right'></i>"
                }
            },
            "dom": "<'row mb-3 no-print'<'col-sm-6'l><'col-sm-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row mt-3 no-print'<'col-sm-5'i><'col-sm-7'p>>",
        });

        $('#input_pin_koreksi').on('keypress', function (e) {
            if (e.which === 13) {
                verifyPinKoreksi();
            }
        });
    });

    let currentGrandTotal = 0;
    let pendingKoreksiBtn = null;
    let pendingKoreksiSpk = null;

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(number);
    }

    function calculateAuditBalance() {
        const tunai = parseFloat(document.getElementById('edit_tunai').value) || 0;
        const transfer = parseFloat(document.getElementById('edit_transfer').value) || 0;
        const ewallet = parseFloat(document.getElementById('edit_ewallet').value) || 0;
        const debit = parseFloat(document.getElementById('edit_debit').value) || 0;

        const totalBayar = tunai + transfer + ewallet + debit;
        document.getElementById('summ_total_bayar').innerText = formatRupiah(totalBayar);
    }

    function loadKoreksi(btn) {
        pendingKoreksiBtn = btn;
        pendingKoreksiSpk = $(btn).data('spk');
        
        $('#input_pin_koreksi').val('');
        const pinModal = new bootstrap.Modal(document.getElementById('modalPinKoreksi'));
        pinModal.show();
        
        setTimeout(() => {
            $('#input_pin_koreksi').focus();
        }, 500);
    }

    function verifyPinKoreksi() {
        const pin = $('#input_pin_koreksi').val();
        if(!pin) {
            alert("Harap masukkan PIN!");
            return;
        }

        const btnV = $('#btnVerifyPin');
        const oriText = btnV.html();
        btnV.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

        $.ajax({
            url: '<?= base_url("administrative/verify_pin_koreksi") ?>',
            type: 'POST',
            data: { pin: pin },
            dataType: 'json',
            success: function(res) {
                btnV.html(oriText).prop('disabled', false);
                if(res.success) {
                    $('#modalPinKoreksi').modal('hide');
                    // Fetch data form
                    fetchDataKoreksi(pendingKoreksiBtn, pendingKoreksiSpk);
                } else {
                    alert(res.message);
                    $('#input_pin_koreksi').val('').focus();
                }
            },
            error: function() {
                btnV.html(oriText).prop('disabled', false);
                alert("Terjadi kesalahan koneksi saat memverifikasi PIN.");
            }
        });
    }

    function fetchDataKoreksi(btn, nospk) {
        const originalHtml = $(btn).html();
        $(btn).html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

        $.ajax({
            url: '<?= base_url("administrative/get_transaksi_audit_json") ?>',
            type: 'POST',
            data: { nospk: nospk },
            dataType: 'json',
            success: function (res) {
                $(btn).html(originalHtml).prop('disabled', false);
                if (res.status === 'success') {
                    openKoreksiModal(res);
                } else {
                    alert('Gagal mengambil data transaksi.');
                }
            },
            error: function () {
                $(btn).html(originalHtml).prop('disabled', false);
                alert('Terjadi kesalahan koneksi.');
            }
        });
    }

    function openKoreksiModal(res) {
        const data = res.header;
        const pays = res.payments;

        currentGrandTotal = parseFloat(data.grand_total);
        document.getElementById('edit_nospk').value = data.nospk;
        document.getElementById('label_spk').innerText = data.nospk;
        document.getElementById('edit_status').value = data.status_pembayaran;
        document.getElementById('edit_jenis').value = data.id_jenis_transaksi;

        $('#edit_status, #edit_jenis').trigger('change');

        document.getElementById('edit_tunai').value = Math.round(pays.tunai || 0);
        document.getElementById('edit_transfer').value = Math.round(pays.transfer || 0);
        document.getElementById('edit_ewallet').value = Math.round(pays.ewallet || 0);
        document.getElementById('edit_debit').value = Math.round((pays.debit || 0) + (pays.kartu_kredit || 0));
        document.getElementById('edit_catatan').value = data.catatan || '';

        document.getElementById('summ_total_spk').innerText = formatRupiah(currentGrandTotal);
        calculateAuditBalance();

        new bootstrap.Modal(document.getElementById('modalKoreksi')).show();
    }

    document.querySelectorAll('.calc-input').forEach(input => {
        input.addEventListener('input', calculateAuditBalance);
    });
</script>