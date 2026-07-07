<div class="app-content">
    <?php $this->load->view('layout_admin/header_modul'); ?>
    <div class="container-fluid">

        <!-- Statistics Overview -->
        <div class="row g-4 mb-4 animation-fade-up">
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 text-center py-4"
                    style="border-radius: 20px; transition: transform 0.3s ease;">
                    <div class="d-inline-flex align-items-center justify-content-center mb-3 mx-auto"
                        style="width: 70px; height: 70px; border-radius: 22px; background: rgba(44, 62, 80, 0.1);">
                        <i class="fa fa-money-bill-wave text-dark fs-2"></i>
                    </div>
                    <div class="card-body p-0">
                        <h6 class="text-muted text-uppercase mb-2 small fw-bold" style="letter-spacing: 1px;">Total
                            Pengeluaran</h6>
                        <h3 class="fw-bold text-dark mb-0">Rp <?= number_format($grandtotal, 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 text-center py-4"
                    style="border-radius: 20px; transition: transform 0.3s ease;">
                    <div class="d-inline-flex align-items-center justify-content-center mb-3 mx-auto"
                        style="width: 70px; height: 70px; border-radius: 22px; background: rgba(52, 152, 219, 0.1);">
                        <i class="fa fa-tags text-primary fs-2"></i>
                    </div>
                    <div class="card-body p-0">
                        <h6 class="text-muted text-uppercase mb-2 small fw-bold" style="letter-spacing: 1px;">Kategori
                            Aktif</h6>
                        <h3 class="fw-bold text-dark mb-0"><?= $kategori_count ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 text-center py-4"
                    style="border-radius: 20px; transition: transform 0.3s ease;">
                    <div class="d-inline-flex align-items-center justify-content-center mb-3 mx-auto"
                        style="width: 70px; height: 70px; border-radius: 22px; background: rgba(233, 30, 99, 0.1);">
                        <i class="fa fa-list text-danger fs-2"></i>
                    </div>
                    <div class="card-body p-0">
                        <h6 class="text-muted text-uppercase mb-2 small fw-bold" style="letter-spacing: 1px;">Total
                            Transaksi</h6>
                        <h3 class="fw-bold text-dark mb-0"><?= $total_transactions ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 text-center py-4"
                    style="border-radius: 20px; transition: transform 0.3s ease;">
                    <div class="d-inline-flex align-items-center justify-content-center mb-3 mx-auto"
                        style="width: 70px; height: 70px; border-radius: 22px; background: rgba(243, 156, 18, 0.1);">
                        <i class="fa fa-chart-bar text-warning fs-2"></i>
                    </div>
                    <div class="card-body p-0">
                        <h6 class="text-muted text-uppercase mb-2 small fw-bold" style="letter-spacing: 1px;">Rata-rata
                            / Transaksi</h6>
                        <h3 class="fw-bold text-dark mb-0">Rp
                            <?= number_format($average_per_transaction, 0, ',', '.') ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Summary -->
        <div class="row g-4 mb-4 animation-fade-up">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                    <div class="card-header bg-white py-3 border-0" style="border-radius: 20px 20px 0 0;">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fa fa-bolt text-warning me-2"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-primary px-4 py-2" data-bs-toggle="modal"
                                data-bs-target="#modalAddPengeluaran" style="border-radius: 12px;">
                                <i class="fa fa-plus-circle me-2"></i>Tambah Pengeluaran
                            </button>
                            <button class="btn btn-outline-dark px-4 py-2" style="border-radius: 12px;">
                                <i class="fa fa-print me-2"></i>Cetak Laporan
                            </button>
                            <button class="btn btn-outline-success px-4 py-2" style="border-radius: 12px;">
                                <i class="fa fa-file-excel me-2"></i>Export Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                    <div class="card-header bg-white py-3 border-0" style="border-radius: 20px 20px 0 0;">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fa fa-chart-pie text-primary me-2"></i>Ringkasan Pengeluaran per Kategori
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 px-4 py-3" width="5%">No</th>
                                        <th class="border-0 py-3" width="35%">Kategori Pengeluaran</th>
                                        <th class="border-0 py-3" width="30%">Total Pengeluaran</th>
                                        <th class="border-0 px-4 py-3 text-center" width="20%">Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($kategori as $value):
                                        $total = $kategori_totals[$value->id];
                                        if ($total > 0):
                                            $percent = $grandtotal > 0 ? round(($total / $grandtotal) * 100, 1) : 0;
                                            ?>
                                                    <tr>
                                                        <td class="px-4"><?= $no++ ?></td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3"
                                                                    style="width: 35px; height: 35px;">
                                                                    <i class="fa fa-tag text-primary small"></i>
                                                                </div>
                                                                <span class="fw-semibold"><?= $value->kategori_pengeluaran ?></span>
                                                            </div>
                                                        </td>
                                                        <td class="text-end fw-bold text-dark">
                                                            Rp <?= number_format($total, 0, ',', '.') ?>
                                                        </td>
                                                        <td class="px-4">
                                                            <div class="d-flex align-items-center">
                                                                <div class="progress flex-grow-1 me-2"
                                                                    style="height: 6px; border-radius: 10px; background: rgba(0,0,0,0.05);">
                                                                    <div class="progress-bar bg-primary" role="progressbar"
                                                                        style="width: <?= $percent ?>%; border-radius: 10px;"
                                                                        aria-valuenow="<?= $percent ?>" aria-valuemin="0"
                                                                        aria-valuemax="100"></div>
                                                                </div>
                                                                <span class="small text-muted fw-bold"><?= $percent ?>%</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                            <?php endif; endforeach; ?>
                                </tbody>
                                <tfoot class="bg-light fw-bold">
                                    <tr>
                                        <td colspan="2" class="text-end px-4 py-3 border-0">Grand Total</td>
                                        <td class="text-end py-3 border-0 text-primary fs-5">
                                            Rp <?= number_format($grandtotal, 0, ',', '.') ?>
                                        </td>
                                        <td class="px-4 py-3 border-0"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Pengeluaran -->
        <div class="row animation-fade-up">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                    <div class="card-header bg-white py-3 border-0" style="border-radius: 20px 20px 0 0;">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fa fa-list text-info me-2"></i>Detail Pengeluaran Bulan Ini
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover w-100" id="tableDetailPengeluaran">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 px-4 py-3">No</th>
                                        <th class="border-0 py-3">Jenis Pengeluaran</th>
                                        <th class="border-0 py-3">Keterangan</th>
                                        <th class="border-0 py-3">Jumlah</th>
                                        <th class="border-0 px-4 py-3">Penanggung Jawab</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1;
                                    foreach ($pengeluaran as $dt): ?>
                                            <tr>
                                                <td class="px-4"><?= $no++ ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3"
                                                            style="width: 35px; height: 35px;">
                                                            <i class="fa fa-tag text-info small"></i>
                                                        </div>
                                                        <span
                                                            class="fw-semibold text-dark"><?= $dt->kategori_pengeluaran ?></span>
                                                    </div>
                                                </td>
                                                <td class="text-muted"><?= $dt->pengeluaran_detail ?></td>
                                                <td class="fw-bold text-dark">
                                                    Rp <?= number_format($dt->pengeluaran_jumlah, 0, ',', '.') ?>
                                                </td>
                                                <td class="px-4">
                                                    <span class="badge bg-light text-dark border fw-medium px-3 py-2"
                                                        style="border-radius: 8px;">
                                                        <i class="fa fa-user-circle me-1 opacity-50"></i>
                                                        <?= $dt->pengeluaran_penerima ?>
                                                    </span>
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

<!-- Modal Tambah Pengeluaran Bulanan -->
<div class="modal fade" id="modalAddPengeluaran" tabindex="-1" aria-labelledby="modalAddPengeluaranLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?= base_url('keuangan/add_pengeluaran_bulanan') ?>" method="post" class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 bg-primary py-4 px-4" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title text-white fw-bold" id="modalAddPengeluaranLabel">
                    <i class="fa fa-plus-circle me-2"></i>Tambah Pengeluaran Bulanan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <label for="kategori" class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: 1px;">Kategori</label>
                    <div class="input-group overflow-hidden" style="border-radius: 12px; border: 1px solid #e0e0e0;">
                        <span class="input-group-text border-0 bg-light px-3"><i class="fa fa-tag text-primary"></i></span>
                        <select class="form-select border-0 py-3" name="kategori" id="kategori" required style="box-shadow: none;">
                            <option value="" disabled selected>Pilih Kategori</option>
                            <?php foreach ($kategori as $value): ?>
                                    <option value="<?= $value->id ?>"><?= $value->kategori_pengeluaran ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="keterangan" class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: 1px;">Keterangan</label>
                    <div class="input-group overflow-hidden" style="border-radius: 12px; border: 1px solid #e0e0e0;">
                        <span class="input-group-text border-0 bg-light px-3"><i class="fa fa-info-circle text-primary"></i></span>
                        <input type="text" name="keterangan" id="keterangan" class="form-control border-0 py-3" placeholder="Masukkan keterangan pengeluaran" required style="box-shadow: none;">
                        <input type="hidden" name="perusahaan_id" id="perusahaan_id" value="<?= $this->session->userdata('perusahaan_id') ?>">
                    </div>
                </div>
                <div class="mb-4">
                    <label for="jumlah" class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: 1px;">Jumlah (Rp)</label>
                    <div class="input-group overflow-hidden" style="border-radius: 12px; border: 1px solid #e0e0e0;">
                        <span class="input-group-text border-0 bg-light px-3"><i class="fa fa-money-bill-wave text-primary"></i></span>
                        <input type="text" name="jumlah" id="jumlah" class="form-control border-0 py-3 fw-bold text-dark" placeholder="0" required style="box-shadow: none;">
                    </div>
                </div>
                <div class="mb-0">
                    <label for="penerima" class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: 1px;">Penanggung Jawab</label>
                    <div class="input-group overflow-hidden" style="border-radius: 12px; border: 1px solid #e0e0e0;">
                        <span class="input-group-text border-0 bg-light px-3"><i class="fa fa-user text-primary"></i></span>
                        <input type="text" name="penerima" id="penerima" class="form-control border-0 py-3" placeholder="Nama penanggung jawab" required style="box-shadow: none;">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light px-4 py-2 fw-bold text-muted transition-all" data-bs-dismiss="modal" style="border-radius: 12px;">Batal</button>
                <button type="submit" class="btn btn-primary px-4 py-2 fw-bold shadow-sm transition-all" style="border-radius: 12px;">
                    <i class="fa fa-save me-2"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        const table = $('#tableDetailPengeluaran').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            },
            "pageLength": 10,
            "order": [[0, "asc"]],
            "columnDefs": [
                { "targets": [3], "className": "text-end" }
            ],
            "dom": '<"d-flex justify-content-between align-items-center mb-3"lf>rt<"d-flex justify-content-between align-items-center mt-3"ip>'
        });

        // Format input jumlah
        $('#jumlah').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            $(this).val(value ? parseInt(value).toLocaleString('id-ID') : '');
        });

        // Form submission formatting
        $('form').on('submit', function() {
            let jumlahInput = $('#jumlah');
            let rawValue = jumlahInput.val().replace(/\D/g, '');
            jumlahInput.val(rawValue);
        });
    });
</script>