<div class="row" style="padding-bottom: 60px;">
    <section class="panel">
            <header class="panel-heading">
                Sistem Absensi Lembur Karyawan
            </header>
            <div class="panel-body">
                <div class="col-md-6 col-xs-12">
                <form action="<?= base_url('karyawan/aksi_simpan_lembur') ?>" method="post">
                    
                    <div class="form-group">
                        <label class="col-md-3 col-xs-12">Karyawan</label>
                        <select name="" class="col-md-9 col-xs-12" required disabled>
                            <?php foreach($listkaryawan as $row): ?>
                                <option value="<?= $row->karyawan_id ?>"
                                    <?= isset($karyawan_terpilih['karyawan_id']) && $karyawan_terpilih['karyawan_id'] == $row->karyawan_id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($row->nama_lengkap, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="karyawan_id" value="<?= $karyawan_terpilih['karyawan_id']?> ">
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 col-xs-12">Tanggal</label>
                        <input type="date" name="tanggal" class="col-md-9 col-xs-12" required value="<?= date('Y-m-d')?>" readonly>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 col-xs-12">Jam Mulai</label>
                        <input type="time" name="jam_mulai" class="col-md-9 col-xs-12" required>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 col-xs-12">Jam Selesai</label>
                        <input type="time" name="jam_selesai" class="col-md-9 col-xs-12" required>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 col-xs-12">Kegiatan</label>
                        <textarea name="kegiatan" class="col-md-9 col-xs-12" rows="3"></textarea>
                    </div>
                    <hr>
                    <p>Pengajuan Lembur Hanya untuk Hari Berjalan</p>
                    <button type="submit" class="btn btn-success col-xs-12">Simpan</button>
                    <br>
                    <!-- <button type="button" class="btn btn-secondary col-xs-12" data-dismiss="modal">Batal</button> -->
                    
                </form>
                </div>
                
                <div class="col-md-6 col-xs-12">

                <div class="table-responsive">
                    <h3>Daftar Lembur Bulan Berjalan</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                        <th>No</th>
                        <th>Nama Karyawan</th>
                        <th>Tanggal</th>
                        <th>Waktu Lembur</th>
                        <th>Kegiatan</th>
                        <th>Waktu Pengajuan</th>
                        <th>Status</th>
                        
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach($karyawan as $row): ?>
                        <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row->nama_lengkap ?></td>
                        <td><?= date_indo($row->tanggal) ?></td>
                        <td><?= $row->jam_mulai ?> - <?= $row->jam_selesai ?></td>
                        <td><?= $row->kegiatan ?></td>
                        <td><?= $row->created_at ?></td>
                        <td><?= $row->status ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            </div>
                        </div>
        
        <!-- end: page -->
    </section>
</div>
