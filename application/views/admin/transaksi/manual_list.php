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

    .btn-action-circle {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        background: white;
        color: #64748b;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-action-circle:hover {
        background: #f8fafc;
        transform: scale(1.1);
        color: var(--tgm-primary);
    }

    .mono {
        font-family: 'JetBrains Mono', monospace;
    }

    .badge-pill {
        padding: 0.4rem 0.8rem;
        font-weight: 700;
        font-size: 0.7rem;
        letter-spacing: 0.02em;
        border-radius: 100px;
    }

    .management-hub {
        background: white;
        border-radius: 20px;
        padding: 1rem 1.5rem;
        border: 1px solid #edf2f7;
    }
</style>

<?php
// Calculate simple stats based on loaded transactions
$today = date('Y-m-d');
$today_count = 0;
$today_sales = 0;
$pending_count = 0;
foreach ($transactions as $t) {
    if (date('Y-m-d', strtotime($t->transaction_date)) == $today) {
        $today_count++;
        $today_sales += $t->grand_total;
    }
    if ($t->status == 'pending') {
        $pending_count++;
    }
}
?>

<div class="app-content py-4 bg-light">
    <!-- Slim Statistics -->
    <div class="container-fluid mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="premium-stat-card border-start border-4 border-primary">
                    <div class="stat-icon-circle bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-shopping-basket"></i>
                    </div>
                    <div class="stat-label">Transaksi Hari Ini</div>
                    <div class="stat-value"><?= $today_count ?> <small class="fw-normal fs-6">TRX</small></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="premium-stat-card border-start border-4 border-success">
                    <div class="stat-icon-circle bg-success bg-opacity-10 text-success">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="stat-label">Omzet Hari Ini</div>
                    <div class="stat-value text-success">Rp <?= number_format($today_sales, 0, ',', '.') ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="premium-stat-card border-start border-4 border-warning">
                    <div class="stat-icon-circle bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-label">Status Pending</div>
                    <div class="stat-value text-warning"><?= $pending_count ?> <small
                            class="fw-normal fs-6">NOTAS</small></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-flex flex-column gap-2 h-100 justify-content-center px-2">
                    <a href="<?= base_url('penjualan/create') ?>"
                        class="btn btn-primary rounded-4 py-2 fw-bold shadow-sm">
                        <i class="fas fa-plus-circle me-2"></i> INPUT TRANSAKSI
                    </a>
                    <button class="btn btn-white rounded-4 py-2 fw-bold shadow-sm border text-muted"
                        data-bs-toggle="modal" data-bs-target="#modalSopManual" style="font-size: 0.8rem;">
                        <i class="fas fa-book-open me-2"></i> PANDUAN KERJA
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Management Hub (Quick Actions & Filter Toggle) -->
    <div class="container-fluid mb-4">
        <div class="management-hub shadow-sm">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h6 class="mb-0 fw-800 text-dark">
                        <i class="fas fa-tasks me-2 text-primary"></i> MANAGEMENT HUB
                    </h6>
                    <small class="text-muted">Kelola data transaksi penjualan manual dan laporan harian</small>
                </div>
                <div class="col-md-6 text-end">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="<?= base_url('penjualan/manual_export') . '?' . http_build_query($filters) ?>"
                            class="btn btn-outline-success btn-sm rounded-pill px-3">
                            <i class="fas fa-file-excel me-1"></i> Export Excel
                        </a>
                        <button type="button" class="btn btn-outline-info btn-sm rounded-pill px-3"
                            onclick="toggleFilters()">
                            <i class="fas fa-filter me-1"></i> Filter Data
                        </button>
                        <button type="button" class="btn btn-light btn-sm rounded-pill px-3 border"
                            onclick="window.location.reload()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="container-fluid mb-4" id="filterCard"
        style="<?= (!empty($filters['search']) || !empty($filters['status']) || !empty($filters['date_from'])) ? '' : 'display:none;' ?>">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <form method="GET" action="<?= base_url('penjualan/manual_list') ?>">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Kata Kunci</label>
                            <input type="text" class="form-control rounded-3" name="search"
                                value="<?= $filters['search'] ?>" placeholder="Cari nota / konsumen...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted">Status</label>
                            <select class="form-select rounded-3" name="status">
                                <option value="">Semua Status</option>
                                <option value="pending" <?= $filters['status'] == 'pending' ? 'selected' : '' ?>>Pending
                                </option>
                                <option value="completed" <?= $filters['status'] == 'completed' ? 'selected' : '' ?>>
                                    Selesai</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted">Pembayaran</label>
                            <select class="form-select rounded-3" name="payment_status">
                                <option value="">Semua</option>
                                <option value="paid" <?= $filters['payment_status'] == 'paid' ? 'selected' : '' ?>>Lunas
                                </option>
                                <option value="unpaid" <?= $filters['payment_status'] == 'unpaid' ? 'selected' : '' ?>>
                                    Belum</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Rentang Tanggal</label>
                            <div class="input-group">
                                <input type="date" class="form-control rounded-3" name="date_from"
                                    value="<?= $filters['date_from'] ?>">
                                <input type="date" class="form-control rounded-3" name="date_to"
                                    value="<?= $filters['date_to'] ?>">
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-dark w-100 rounded-3 py-2">
                                <i class="fas fa-search me-1"></i> Tampilkan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Main List Table -->
<div class="container-fluid">
    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium mb-0" id="datatable-manual">
                    <thead>
                        <tr>
                            <th class="ps-4">No. Transaksi & Waktu</th>
                            <th>Pelanggan & Sales</th>
                            <th class="text-end">Total Belanja</th>
                            <th class="text-center">Status Nota</th>
                            <th class="text-center">Pembayaran</th>
                            <th class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($transactions)):
                            foreach ($transactions as $t): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark fs-6"><?= $t->transaction_number ?></div>
                                        <div class="small text-muted mono">
                                            <i
                                                class="far fa-calendar-alt me-1"></i><?= date('d M Y', strtotime($t->transaction_date)) ?>
                                            <span class="mx-1 text-silver">•</span>
                                            <i class="far fa-clock me-1"></i><?= date('H:i', strtotime($t->created_at)) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= $t->customer_name ?: 'Pelanggan Umum' ?></div>
                                        <div class="small text-muted">
                                            <i class="fas fa-user-tie me-1 text-silver"></i><?= $t->sales_person ?: '-' ?>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <div class="mono fw-bold text-dark fs-5">Rp
                                            <?= number_format($t->grand_total, 0, ',', '.') ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $statusConfig = [
                                            'completed' => ['bg' => '#dcfce7', 'color' => '#15803d', 'label' => 'SELESAI'],
                                            'pending' => ['bg' => '#fef9c3', 'color' => '#854d0e', 'label' => 'PENDING'],
                                            'cancelled' => ['bg' => '#fee2e2', 'color' => '#991b1b', 'label' => 'BATAL']
                                        ];
                                        $st = $statusConfig[$t->status] ?? $statusConfig['pending'];
                                        ?>
                                        <span class="badge-pill"
                                            style="background: <?= $st['bg'] ?>; color: <?= $st['color'] ?>;">
                                            <?= $st['label'] ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $payConfig = [
                                            'paid' => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'label' => 'LUNAS'],
                                            'unpaid' => ['bg' => '#fff1f2', 'color' => '#e11d48', 'label' => 'BELUM'],
                                            'partial' => ['bg' => '#ecfeff', 'color' => '#0891b2', 'label' => 'SEBAGIAN']
                                        ];
                                        $pc = $payConfig[$t->payment_status] ?? $payConfig['unpaid'];
                                        ?>
                                        <span class="badge-pill"
                                            style="background: <?= $pc['bg'] ?>; color: <?= $pc['color'] ?>; border: 1px solid <?= $pc['bg'] ?>;">
                                            <?= $pc['label'] ?>
                                        </span>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="<?= base_url('penjualan/manual_detail/' . $t->transaction_id) ?>"
                                                class="btn-action-circle text-primary" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('penjualan/cetak_invoice/' . $t->transaction_id) ?>"
                                                target="_blank" class="btn-action-circle text-dark" title="Cetak Nota">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <a href="<?= base_url('transaksi/manual_edit/' . $t->transaction_id) ?>"
                                                class="btn-action-circle text-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn-action-circle text-danger"
                                                onclick="deleteTransaction('<?= $t->transaction_id ?>')" title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
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

<!-- SOP Manual List Modal -->
<div class="modal fade" id="modalSopManual" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 25px;">
            <div class="modal-header border-0 pb-0 ps-4 pt-4">
                <h5 class="modal-title fw-900"><i class="fas fa-shield-alt text-primary me-2"></i>SOP TRANSAKSI MANUAL
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-4 h-100">
                            <h6 class="fw-bold text-dark"><i class="fas fa-plus-circle me-2 text-primary"></i>1. Input &
                                Validasi</h6>
                            <ul class="small text-muted ps-3 mb-0">
                                <li class="mb-2">Gunakan tombol <b>INPUT TRANSAKSI</b> untuk mencatat penjualan barang
                                    jadi atau jasa non-SPK.</li>
                                <li class="mb-2">Selalu pastikan nama pelanggan dan nominal sudah sesuai sebelum
                                    menyimpan.</li>
                                <li>Pilih metode pembayaran yang akurat (Cash/EDC/Transfer) untuk sinkronisasi kas.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-4 h-100">
                            <h6 class="fw-bold text-dark"><i class="fas fa-print me-2 text-success"></i>2. Pengelolaan
                                Dokumen</h6>
                            <ul class="small text-muted ps-3 mb-0">
                                <li class="mb-2">Gunakan ikon <b>Printer</b> untuk mencetak nota kepada konsumen jika
                                    diperlukan.</li>
                                <li class="mb-2">Status <b>PENDING</b> berarti transaksi belum selesai atau barang belum
                                    diserahkan.</li>
                                <li>Semua pembatalan transaksi harus dikonfirmasi melalui manajer cabang.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="mt-4 p-3 border border-info border-opacity-25 bg-info bg-opacity-10 rounded-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-lightbulb fa-2x text-info me-3"></i>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Tips Efisiensi</h6>
                            <p class="small text-dark mb-0 opacity-75">Gunakan fitur <b>Export Excel</b> di Management
                                Hub untuk melakukan rekapitulasi penjualan mingguan atau bulanan secara mandiri.</p>
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
</div>
</div>

<script>
    function toggleFilters() {
        $('#filterCard').slideToggle(300);
    }

    function deleteTransaction(id) {
        Swal.fire({
            title: 'Hapus Transaksi?',
            text: "Data permanen akan dihapus dari sistem!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                confirmButton: 'rounded-pill px-4',
                cancelButton: 'rounded-pill px-4'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= base_url('penjualan/manual_delete/') ?>' + id;
            }
        });
    }

    $(document).ready(function () {
        if ($('#datatable-manual').length) {
            $('#datatable-manual').DataTable({
                responsive: true,
                language: {
                    search: "",
                    searchPlaceholder: "Cari Nota / Konsumen...",
                    lengthMenu: "_MENU_",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ Transaksi",
                    paginate: { first: "First", last: "Last", next: "›", previous: "‹" },
                    emptyTable: "Tidak ada data transaksi"
                },
                pageLength: 25,
                columnDefs: [
                    { orderable: false, targets: [5] }
                ],
                dom: "<'row px-4 pt-4 mb-2'<'col-sm-6 d-flex align-items-center'l><'col-sm-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row p-4'<'col-sm-5'i><'col-sm-7'p>>",
                "drawCallback": function () {
                    $('.dataTables_filter input').addClass('form-control form-control-sm rounded-pill px-3 py-2 border-light shadow-sm bg-light');
                    $('.dataTables_length select').addClass('form-select form-select-sm rounded-pill px-3 border-light shadow-sm bg-light');
                    $('.paginate_button').addClass('rounded-circle');

                    // Initialize tooltips after draw
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                    tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl)
                    });
                }
            });
        }
    });
</script>