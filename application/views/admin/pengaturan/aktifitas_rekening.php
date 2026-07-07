<div class="p-4" style="background: #f8fafc; min-height: 100vh;">
    <!-- HEADER SECTION -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h4 class="fw-900 text-dark mb-1 text-uppercase ls-1">
                <i class="fas fa-history me-2 text-primary"></i> Aktifitas Mutasi Rekening
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small fw-bold">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>" class="text-muted">ERP</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('pengaturan/rekening') ?>"
                            class="text-muted">Rekening</a></li>
                    <li class="breadcrumb-item active text-primary">Mutasi Bank</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="text-end me-3">
                <div class="text-muted small fw-800 text-uppercase ls-1">Saldo Terakumulasi</div>
                <div class="fw-900 fs-4 text-success">Rp
                    <?= number_format(array_sum(array_column($mutasi, 'jumlah')), 0, ',', '.') ?>
                </div>
            </div>
            <button type="button" class="btn btn-primary btn-premium rounded-pill px-4 shadow-sm"
                onclick="window.print()">
                <i class="fas fa-print me-2"></i> CETAK MUTASI
            </button>
        </div>
    </div>

    <!-- ACCOUNT INFO CARD -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white overflow-hidden position-relative">
        <div class="position-absolute top-0 end-0 p-4 opacity-10">
            <i class="fas fa-university fa-6x"></i>
        </div>
        <div class="row align-items-center">
            <div class="col-md-auto">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-4 d-flex align-items-center justify-content-center"
                    style="width: 80px; height: 80px;">
                    <i class="fas fa-wallet fs-2"></i>
                </div>
            </div>
            <div class="col-md">
                <h5 class="fw-900 text-dark mb-1">
                    <?= $rekening->no_rekening ?>
                </h5>
                <div class="d-flex align-items-center gap-3 mt-2">
                    <span class="badge bg-primary rounded-pill px-3 py-2 fw-800">
                        <?= strtoupper($rekening->nama_bank) ?>
                    </span>
                    <span class="text-muted fw-800 small text-uppercase ls-1"><i class="fas fa-user me-1"></i>
                        <?= $rekening->atas_nama ?>
                    </span>
                </div>
            </div>
            <div class="col-md-auto text-end">
                <div class="text-muted small fw-800 text-uppercase ls-1 mb-1">Status Rekening</div>
                <span
                    class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2 fw-800">
                    <i class="fas fa-check-circle me-1"></i> ACTIVE
                </span>
            </div>
        </div>
    </div>

    <!-- MUTASI TABLE CARD -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden shadow-hover transition-all">
        <div class="card-header bg-white py-3 px-4 border-bottom border-light">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="fw-800 text-dark mb-0 ls-1 text-uppercase">Riwayat Transaksi Masuk</h6>
                </div>
                <div class="col-auto">
                    <div class="input-group input-group-sm bg-light rounded-pill px-2">
                        <span class="input-group-text bg-transparent border-0 text-muted">
                            <i class="fas fa-search small"></i>
                        </span>
                        <input type="text" id="mutationSearch"
                            class="form-control border-0 bg-transparent form-control-sm small"
                            placeholder="Cari mutasi...">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="bg-light bg-opacity-50">
                            <th class="ps-4 py-3 fw-900 text-muted small text-uppercase ls-1">WAKTU</th>
                            <th class="py-3 fw-900 text-muted small text-uppercase ls-1">NO. REFERENSI / SPK</th>
                            <th class="py-3 fw-900 text-muted small text-uppercase ls-1">KETERANGAN</th>
                            <th class="py-3 fw-900 text-muted small text-uppercase ls-1">SUMBER DANA</th>
                            <th class="pe-4 py-3 fw-900 text-muted small text-uppercase ls-1 text-end">NOMINAL</th>
                        </tr>
                    </thead>
                    <tbody id="mutationTableBody">
                        <?php if (empty($mutasi)): ?>
                            <tr>
                                <td colspan="5" class="py-5 text-center">
                                    <i class="fas fa-receipt fs-1 text-muted opacity-25 mb-3"></i>
                                    <h6 class="fw-800 text-muted">Belum ada aktifitas mutasi pada rekening ini.</h6>
                                </td>
                            </tr>
                        <?php else:
                            foreach ($mutasi as $m): ?>
                                <tr class="border-light border-bottom">
                                    <td class="ps-4">
                                        <div class="fw-800 text-dark">
                                            <?= date('d M Y', strtotime($m->tgl_bayar)) ?>
                                        </div>
                                        <div class="text-muted small fw-bold opacity-75">
                                            <?= $m->jam_bayar ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-900 text-primary small ls-1">
                                            <?= $m->nospk ?>
                                        </div>
                                        <div class="text-muted small fw-bold mt-1" style="font-size: 0.75rem;">
                                            REF:
                                            <?= $m->referensi ?: '-' ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-800 text-dark small text-uppercase">
                                            <?= $m->keterangan ?>
                                        </div>
                                        <div class="text-muted small fw-bold" style="font-size: 0.7rem;">
                                            KASIR:
                                            <?= strtoupper($m->kasir) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-pill bg-light border p-2 me-2 d-flex align-items-center justify-content-center"
                                                style="width: 32px; height: 32px;">
                                                <i class="fas fa-user-tag small text-muted"></i>
                                            </div>
                                            <div class="fw-bolder text-dark small">
                                                <?= strtoupper($m->konsumen_nama) ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="fw-900 text-success">Rp
                                            <?= number_format($m->jumlah, 0, ',', '.') ?>
                                        </div>
                                        <div class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 fw-800"
                                            style="font-size: 0.6rem;">
                                            CREDIT <i class="fas fa-arrow-down ms-1"></i>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach;
                        endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#mutationSearch").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#mutationTableBody tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>

<style>
    .ls-1 {
        letter-spacing: 0.5px;
    }

    .fw-800 {
        font-weight: 800;
    }

    .fw-900 {
        font-weight: 900;
    }

    .transition-all {
        transition: all 0.3s ease;
    }

    .shadow-hover:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }

    .btn-premium {
        background: #1e293b;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-premium:hover {
        background: #0f172a;
        transform: translateY(-1px);
    }

    @media print {

        .app-header,
        .app-sidebar,
        .card-header .col-auto,
        .btn-premium {
            display: none !important;
        }

        .p-4 {
            padding: 0 !important;
        }

        .card {
            box-shadow: none !important;
            border: 1px solid #eee !important;
        }
    }
</style>