<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        <div class="card mt-4">
            <div class="card-header bg-light border-bottom">
                <a href="<?= base_url('karyawan') ?>" class="btn btn-link p-0 me-2">&larr; Kembali</a>
                <span class="fw-bold">Edit Data Karyawan</span>
            </div>
            <div class="card-body">
                <form action="<?= base_url('karyawan/aksi_edit_karyawan') ?>" method="post"
                    enctype="multipart/form-data">
                    <input type="hidden" name="karyawan_id" value="<?= $karyawan->karyawan_id ?>">

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control"
                            value="<?= $karyawan->nama_lengkap ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">NIK</label>
                        <input type="text" name="nip" class="form-control" value="<?= $karyawan->nik ?>">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="L" <?= $karyawan->jenis_kelamin == 'L' ? 'selected' : '' ?>>Laki-laki
                                </option>
                                <option value="P" <?= $karyawan->jenis_kelamin == 'P' ? 'selected' : '' ?>>Perempuan
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control"
                                value="<?= $karyawan->tanggal_lahir ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jabatan</label>
                        <select name="jabatan_id" class="form-select">
                            <?php foreach ($jabatan as $j): ?>
                                <option value="<?= $j->jabatan_id ?>" <?= $karyawan->jabatan_id == $j->jabatan_id ? 'selected' : '' ?>>
                                    <?= $j->nama_jabatan ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Divisi</label>
                        <select name="divisi_id" class="form-select">
                            <?php foreach ($divisi as $d): ?>
                                <option value="<?= $d->divisi_id ?>" <?= $karyawan->divisi_id == $d->divisi_id ? 'selected' : '' ?>>
                                    <?= $d->divisi_nama ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lokasi Perusahaan</label>
                        <select name="perusahaan_id" class="form-select">
                            <?php foreach ($perusahaan as $l): ?>
                                <option value="<?= $l->id_perusahaan ?>" <?= $karyawan->perusahaan_id == $l->id_perusahaan ? 'selected' : '' ?>>
                                    <?= $l->nama_perusahaan ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2"><?= $karyawan->alamat ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Telepon</label>
                            <input type="text" name="telepon" class="form-control" value="<?= $karyawan->telepon ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= $karyawan->email ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk" class="form-control"
                                value="<?= $karyawan->tanggal_masuk ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status_karyawan" id="status_karyawan" class="form-select">
                                <option value="Tetap" <?= $karyawan->status_karyawan == 'Tetap' ? 'selected' : '' ?>>Tetap
                                </option>
                                <option value="Kontrak" <?= $karyawan->status_karyawan == 'Kontrak' ? 'selected' : '' ?>>
                                    Kontrak</option>
                                <option value="Magang" <?= $karyawan->status_karyawan == 'Magang' ? 'selected' : '' ?>>
                                    Magang</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3" id="container_akhir_kontrak" style="display: none;">
                            <label class="form-label">Tanggal Akhir Kontrak <span class="text-danger">*</span></label>
                            <input type="date" name="akhir_kontrak" id="akhir_kontrak" class="form-control"
                                value="<?= $karyawan->akhir_kontrak ?? '' ?>">
                        </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sistem Gaji <span class="text-info"
                                    style="font-size: 0.8rem;">(Default: Bulanan)</span></label>
                            <select name="jenis_gaji" class="form-select">
                                <option value="Bulanan" <?= $karyawan->jenis_gaji == 'Bulanan' ? 'selected' : '' ?>>Bulanan
                                    (Sesuai Gaji Pokok)</option>
                                <option value="Harian" <?= $karyawan->jenis_gaji == 'Harian' ? 'selected' : '' ?>>Harian
                                    (Gaji
                                    Pokok x Kehadiran)</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">PIN Produksi (4-6 Digit)</label>
                                <input type="password" name="pin_produksi" class="form-control" maxlength="6"
                                    placeholder="PIN Verifikasi Produksi" value="<?= $karyawan->pin_produksi ?>"
                                    inputmode="numeric">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tunjangan Transportasi</label>
                                <select name="transport_aktif" class="form-select">
                                    <option value="1" <?= $karyawan->transport_aktif == 1 ? 'selected' : '' ?>>AKTIF
                                        (Dapatkan 5%)</option>
                                    <option value="0" <?= $karyawan->transport_aktif == 0 ? 'selected' : '' ?>>NON-AKTIF
                                        (Rp 0)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="<?= base_url('karyawan') ?>" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Toggle Tanggal Akhir Kontrak visibility
    const statusSelect = document.getElementById('status_karyawan');
    const akhirKontrakContainer = document.getElementById('container_akhir_kontrak');
    const akhirKontrakInput = document.getElementById('akhir_kontrak');

    function toggleAkhirKontrak() {
        if (statusSelect.value === 'Kontrak' || statusSelect.value === 'Magang') {
            akhirKontrakContainer.style.display = 'block';
            akhirKontrakInput.setAttribute('required', 'required');
        } else {
            akhirKontrakContainer.style.display = 'none';
            akhirKontrakInput.removeAttribute('required');
            // If we don't want to clear on edit immediately, we can leave the value alone
            // akhirKontrakInput.value = ''; 
        }
    }

    if (statusSelect) {
        statusSelect.addEventListener('change', toggleAkhirKontrak);
        toggleAkhirKontrak(); // Run once on load
    }
});
</script>