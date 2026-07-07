<?php $this->load->view('layout_admin/header_modul'); ?>

<div class="app-content font-mono">
    <!-- Header Title & Back Button -->
    <div class="d-flex align-items-center gap-3 mb-4 mt-3">
        <a href="<?= base_url('hr') ?>" class="btn btn-white d-flex align-items-center justify-content-center shadow-sm"
            style="width: 42px; height: 42px; padding: 0; border-radius: 10px; border: 1px solid var(--tgm-slate-200); background: white; color: var(--tgm-slate-700);"
            title="Kembali ke Dashboard HR">
            <i class="bi bi-arrow-left fs-5"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0 text-dark"><i class="fas fa-chart-line me-2 text-primary"></i>PENILAIAN KINERJA</h4>
            <p class="small text-muted mb-0">Pemantauan produktivitas SPK karyawan</p>
        </div>
    </div>

    <div class="row">
        <section class="panel">

            <table class="table table-bordered table-striped mb-none" id="datatable-detail">
                <thead class="thead-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Jenis Kelamin</th>
                        <th>Jabatan</th>
                        <th>Jumlah Transaksi SPK</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($karyawan)): ?>
                        <?php $no = 1;
                        foreach ($karyawan as $k): ?>

                            <?php $jumlah_transaksi = $this->M_master->jml_spk($k->user_id)->row_array(); ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $k->nama_lengkap ?>         <?= $k->user_id ?></td>
                                <td><?= ($k->jenis_kelamin == 'L') ? 'Laki-laki' : 'Perempuan' ?></td>

                                <td><?= $k->nama_jabatan ?></td>
                                <td><?= $jumlah_transaksi['jum'] ?></td>

                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </div>

    <!-- end: page -->
</div>