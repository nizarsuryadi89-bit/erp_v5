<div class="row">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">
                    <a href="<?= base_url('karyawan/jabatan') ?>">Kembali</a> Tambah Data tambah_jabatan
                </h2>
            </header>
            <div class="panel-body text-left">
                <div class="container">
                    <form action="<?= base_url('karyawan/aksi_simpan_jabatan') ?>" method="post">
                        <div class="form-group">
                            <label>Nama Jabatan</label>
                            <input type="text" name="nama_jabatan" class="form-control"
                                placeholder="Masukkan nama jabatan" required>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Gaji Pokok</label>
                                    <input type="number" name="gaji_pokok" class="form-control" placeholder="0"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tunjangan Jabatan</label>
                                    <input type="number" name="tunjangan_jabatan" class="form-control" placeholder="0"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tunjangan Transportasi</label>
                                    <input type="number" name="tunjangan_transportasi" class="form-control"
                                        placeholder="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Deskripsi (opsional)</label>
                            <textarea name="deskripsi" class="form-control" rows="3"
                                placeholder="Deskripsi tugas jabatan..."></textarea>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="<?= base_url('jabatan') ?>" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>