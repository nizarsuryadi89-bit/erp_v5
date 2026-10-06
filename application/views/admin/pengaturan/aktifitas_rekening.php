<div class="container-fluid p-4">
    <!-- HEADER SECTION -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="fas fa-history me-2 text-secondary"></i> Mutasi Rekening
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= base_url('pengaturan/rekening') ?>" class="text-decoration-none">Rekening</a></li>
                    <li class="breadcrumb-item active">Mutasi - <?= $rekening->nama_bank ?></li>
                </ol>
            </nav>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-outline-secondary shadow-sm" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Cetak Mutasi
            </button>
        </div>
    </div>

    <!-- ACCOUNT INFO CARD -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body d-flex justify-content-between align-items-center py-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-light rounded p-3 text-secondary">
                    <i class="fas fa-university fs-4"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold"><?= $rekening->no_rekening ?></h5>
                    <div class="text-muted small">
                        <?= strtoupper($rekening->nama_bank) ?> &bull; A/N: <?= $rekening->atas_nama ?>
                    </div>
                </div>
            </div>
            <div class="text-end">
                <div class="text-muted small mb-1">Total Saldo Terakumulasi</div>
                <h4 class="mb-0 text-success fw-bold">Rp <?= number_format($total_saldo, 0, ',', '.') ?></h4>
            </div>
        </div>
    </div>

    <!-- MUTASI TABLE CARD -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold">Riwayat Transaksi Masuk</h6>
            <div class="d-flex gap-2">
                <input type="month" id="filterMonth" class="form-control form-control-sm text-muted" style="width: 150px;" value="<?= date('Y-m') ?>">
                <div style="width: 200px;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white text-muted border-end-0"><i class="fas fa-search"></i></span>
                        <input type="text" id="mutationSearch" class="form-control border-start-0" placeholder="Cari data...">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="mutationTable" class="table table-hover table-striped align-middle mb-0" style="width:100%">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-4">Waktu</th>
                            <th>No. Referensi / SPK</th>
                            <th>Keterangan</th>
                            <th>Sumber Dana</th>
                            <th class="text-end pe-4">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        var table = $('#mutationTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            lengthChange: false,
            pageLength: 15,
            dom: "t<'row px-3 py-3'<'col-md-6 text-muted small'i><'col-md-6 d-flex justify-content-end'p>>",
            ajax: {
                url: "<?= base_url('pengaturan/aktifitas_rekening_json/' . $rekening->id) ?>",
                type: "POST",
                data: function(d) {
                    d.filter_month = $('#filterMonth').val();
                }
            },
            columns: [
                {
                    data: "tgl_bayar",
                    className: "ps-4",
                    render: function (data, type, row) {
                        // format date d/m/Y
                        var dateObj = new Date(data);
                        var d = ("0" + dateObj.getDate()).slice(-2);
                        var m = ("0" + (dateObj.getMonth() + 1)).slice(-2);
                        var y = dateObj.getFullYear();
                        return d + '/' + m + '/' + y + '<br><span class="text-muted">' + row.jam_bayar + '</span>';
                    }
                },
                {
                    data: "nospk",
                    render: function (data, type, row) {
                        var ref = row.referensi ? row.referensi : '-';
                        return '<span class="fw-bold text-primary">' + data + '</span><br>' +
                               '<span class="text-muted">Ref: ' + ref + '</span>';
                    }
                },
                {
                    data: "keterangan",
                    render: function (data, type, row) {
                        return data + '<br><span class="text-muted">Kasir: ' + row.kasir + '</span>';
                    }
                },
                {
                    data: "konsumen_nama"
                },
                {
                    data: "jumlah",
                    className: "text-end pe-4",
                    render: function (data, type, row) {
                        return '<span class="text-success fw-bold">Rp ' + new Intl.NumberFormat('id-ID').format(data) + '</span>';
                    }
                }
            ],
            drawCallback: function () {
                $('.pagination .page-item .page-link').addClass('rounded-circle mx-1 fw-bold border-0 shadow-sm');
                $('.pagination .page-item.active .page-link').addClass('bg-dark border-dark');
            }
        });

        $("#mutationSearch").on("keyup", function () {
            table.search(this.value).draw();
        });

        $("#filterMonth").on("change", function () {
            table.draw();
        });
    });
</script>

<style>
    @media print {
        .app-header,
        .app-sidebar,
        .card-header,
        button {
            display: none !important;
        }

        .p-4 {
            padding: 0 !important;
        }

        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
    }
</style>