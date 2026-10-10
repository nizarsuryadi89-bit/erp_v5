<!-- Container Fluid -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Pengeluaran Shift</h1>
    </div>

    <?php if (!$current_shift): ?>
        <div class="row justify-content-center">
            <div class="col-xl-8">
                <div class="alert alert-danger shadow-sm border-left-danger" role="alert">
                    <h4 class="alert-heading"><i class="fas fa-exclamation-triangle mr-2"></i>Shift Belum Dibuka</h4>
                    <p class="mb-0">Anda belum membuka shift. Silakan buka shift terlebih dahulu sebelum mencatat
                        pengeluaran.</p>
                    <hr>
                    <a href="<?= base_url('KasirShift/open_shift') ?>" class="btn btn-danger btn-sm">Buka Shift Sekarang</a>
                </div>
            </div>
        </div>
    <?php else: ?>

        <div class="row">
            <!-- Input Form -->
            <div class="col-lg-5 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Input Pengeluaran Baru</h6>
                        <span class="badge badge-success px-2 py-1">Shift #<?= $current_shift->shift_id ?></span>
                    </div>
                    <div class="card-body">
                        <form id="formPengeluaran" autocomplete="off">

                            <div class="form-group">
                                <label for="kategori_id" class="font-weight-bold small text-uppercase">Kategori</label>
                                <select name="kategori_id" id="kategori_id" class="form-control" required>
                                    <option value="" disabled selected>-- Pilih Kategori --</option>
                                    <?php if (!empty($categories)):
                                        foreach ($categories as $cat): ?>
                                            <option value="<?= $cat->id ?>"><?= $cat->kategori_pengeluaran ?></option>
                                        <?php endforeach; else: ?>
                                        <option value="0">Umum / Lainnya</option>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="jumlah" class="font-weight-bold small text-uppercase">Jumlah (Rp)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text font-weight-bold">Rp</span>
                                    </div>
                                    <input type="text" class="form-control form-control-lg font-weight-bold text-primary"
                                        id="jumlah" name="jumlah" placeholder="0" required onkeyup="formatCurrency(this)">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="keterangan" class="font-weight-bold small text-uppercase">Keterangan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3"
                                    placeholder="Contoh: Beli sabun, galon, dll..." required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block shadow-sm font-weight-bold py-2">
                                <i class="fas fa-save mr-2"></i> SIMPAN PENGELUARAN
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- History Table -->
            <div class="col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Riwayat Pengeluaran (Shift Ini)</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="dataTablePengeluaran" width="100%"
                                cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Kategori</th>
                                        <th>Keterangan</th>
                                        <th>Waktu</th>
                                        <th class="text-right">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($expenses)):
                                        $no = 1;
                                        $total = 0;
                                        foreach ($expenses as $exp):
                                            $total += $exp->pengeluaran_jumlah;
                                            ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td>
                                                    <span
                                                        class="badge badge-secondary"><?= $exp->kategori_pengeluaran ?: 'Umum' ?></span>
                                                </td>
                                                <td><?= $exp->keterangan ?></td>
                                                <td class="small"><?= date('H:i', strtotime($exp->pengeluaran_tgl)) ?></td>
                                                <td class="text-right font-weight-bold">Rp
                                                    <?= number_format($exp->pengeluaran_jumlah, 0, ',', '.') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr class="bg-light font-weight-bold">
                                            <td colspan="4" class="text-right">Total Pengeluaran:</td>
                                            <td class="text-right text-danger">Rp <?= number_format($total, 0, ',', '.') ?></td>
                                        </tr>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">Belum ada pengeluaran pada shift
                                                ini.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>
<!-- /.container-fluid -->

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Format Currency Input
    function formatCurrency(input) {
        let value = input.value.replace(/\D/g, '');
        input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Wait for jQuery
    document.addEventListener("DOMContentLoaded", function (event) {
        var checkJquery = setInterval(function () {
            if (typeof $ !== 'undefined') {
                clearInterval(checkJquery);

                $(document).ready(function () {
                    }
                    // Init DataTable if available
                    if ($.fn.DataTable) {
                        if ($.fn.DataTable.isDataTable('#dataTablePengeluaran')) {
                            $('#dataTablePengeluaran').DataTable().destroy();
                        }
                        $('#dataTablePengeluaran').DataTable({
                            "pageLength": 10,
                            "ordering": false
                        });
                    }

                    $('#formPengeluaran').on('submit', function (e) {
                        e.preventDefault();

                        const form = $(this);
                        const btn = form.find('button[type="submit"]');
                        const originalText = btn.html();

                        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...');

                        $.ajax({
                            url: '<?= base_url('transaksi/input_pengeluaran') ?>',
                            type: 'POST',
                            data: form.serialize(),
                            dataType: 'json',
                            success: function (res) {
                                if (res.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: res.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        // Reload page to update table
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: res.message
                                    });
                                    btn.prop('disabled', false).html(originalText);
                                }
                            },
                            error: function (xhr, status, error) {
                                console.error(xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'System Error',
                                    text: 'Terjadi kesalahan: ' + error
                                });
                                btn.prop('disabled', false).html(originalText);
                            }
                        });
                    });
                });
            }
        }, 100);
    });
</script>