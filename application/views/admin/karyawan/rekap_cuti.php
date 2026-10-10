<div class="row mb-3">
    <!-- Filter Section (Mirip Penilaian Kinerja) -->
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="" method="get">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label class="form-label text-muted fw-bold small">Pilih Tahun</label>
                            <select name="tahun" class="form-select" onchange="this.form.submit()">
                                <?php
                                $current_year = date('Y');
                                for ($i = $current_year; $i >= $current_year - 5; $i--): ?>
                                    <option value="<?= $i ?>" <?= $tahun == $i ? 'selected' : '' ?>>
                                        <?= $i ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-9 text-end">
                            <!-- Tombol Export (Placeholder jika belum ada fungsinya) -->
                            <!-- <a href="<?= base_url('karyawan/rekap_cuti_excel?tahun=' . $tahun) ?>" class="btn btn-success">
                                <i class="fas fa-file-excel me-1"></i> Export Excel
                            </a> -->
                            <button type="button" onclick="window.print()" class="btn btn-secondary">
                                <i class="fas fa-print me-1"></i> Print
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Data Table Section -->
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-primary"><i class="fa fa-calendar-alt me-2"></i> Rekap Cuti Karyawan</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle" id="datatable">
                        <thead class="bg-light text-center">
                            <tr>
                                <th width="5%" rowspan="2" style="vertical-align: middle;">No</th>
                                <th rowspan="2" style="vertical-align: middle;">Nama Karyawan</th>
                                <th rowspan="2" style="vertical-align: middle;">Divisi</th>
                                <th colspan="3" class="bg-primary text-white">Manajemen Cuti</th>
                                <th colspan="3" class="bg-secondary text-white">Absensi Lain</th>
                            </tr>
                            <tr>
                                <!-- Sub Header Cuti -->
                                <th class="bg-primary text-white bg-opacity-75">Jatah</th>
                                <th class="bg-primary text-white bg-opacity-75">Terpakai</th>
                                <th class="bg-primary text-white bg-opacity-75">Sisa</th>

                                <!-- Sub Header Lain -->
                                <th class="bg-secondary text-white bg-opacity-75">Sakit</th>
                                <th class="bg-secondary text-white bg-opacity-75">Izin</th>
                                <th class="bg-secondary text-white bg-opacity-75">Alpha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($list_karyawan as $k):
                                if (empty($k->jatah_cuti)) {
                                    $k->jatah_cuti = 12;
                                }
                                ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td>
                                        <div class="fw-bold"><?= $k->nama_lengkap ?></div>
                                        <small class="text-muted"><?= $k->nik ?> - <?= $k->nama_jabatan ?></small>
                                    </td>
                                    <td><?= $k->divisi_nama ?></td>

                                    <!-- Kolom Cuti -->
                                    <td class="text-center fw-bold bg-light" style="font-size:1.1em;"><?= $k->jatah_cuti ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($k->total_cuti > 0): ?>
                                            <span class="text-danger fw-bold"
                                                style="font-size:1.1em;"><?= $k->total_cuti ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center fw-bold text-success bg-light" style="font-size:1.1em;">
                                        <?= $k->sisa_cuti ?></td>

                                    <!-- Kolom Lain -->
                                    <td class="text-center"><?= $k->total_sakit > 0 ? $k->total_sakit : '-' ?></td>
                                    <td class="text-center"><?= $k->total_izin > 0 ? $k->total_izin : '-' ?></td>
                                    <td class="text-center">
                                        <?= $k->total_alpha > 0 ? '<span class="text-danger fw-bold">' . $k->total_alpha . '</span>' : '-' ?>
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

<script>
    $(document).ready(function () {
        $('#datatable').DataTable({
            "pageLength": 25,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
    });
</script>