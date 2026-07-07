<div class="row" style="padding-bottom: 60px;">
        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <section class="panel">
                <div class="panel-body">
                    <div class="thumb-info mb-md">
                        <img src="<?= base_url('')?>/assets/images/user/salaketik.png" class="rounded img-fluid" width="100%">
                        <div class="thumb-info-title">
                            <span class="thumb-info-inner"><?= $profile['nama_lengkap']?></span>
                            <span class="thumb-info-type"><?= $profile['nama_jabatan']?></span>
                        </div>
                    </div>

                    <!-- <div class="widget-toggle-expand mb-md">
                        <div class="widget-header">
                            <h6>Profile Completion</h6>
                            <div class="widget-toggle">+</div>
                        </div>
                        <div class="widget-content-collapsed">
                            <div class="progress progress-xs light">
                                <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;">
                                    60%
                                </div>
                            </div>
                        </div>
                        <div class="widget-content-expanded">
                            <ul class="simple-todo-list">
                                <li class="completed">Update Profile Picture</li>
                                <li class="completed">Change Personal Information</li>
                                <li>Update Social Media</li>
                                <li>Follow Someone</li>
                            </ul>
                        </div>
                    </div> -->

                    <hr class="dotted short">

                    <h6 class="text-muted">Tentang</h6>
                    <p></p>
                    <div class="clearfix">
                        <a class="text-uppercase text-muted pull-right" href="#">(View All)</a>
                    </div>

                    <hr class="dotted short">

                    <div class="social-icons-list">
                        <a rel="tooltip" data-placement="bottom" target="_blank" href="http://www.facebook.com" data-original-title="Facebook"><i class="fa fa-facebook"></i><span>Facebook</span></a>
                        <a rel="tooltip" data-placement="bottom" href="http://www.twitter.com" data-original-title="Twitter"><i class="fa fa-twitter"></i><span>Twitter</span></a>
                        <a rel="tooltip" data-placement="bottom" href="http://www.linkedin.com" data-original-title="Linkedin"><i class="fa fa-linkedin"></i><span>Linkedin</span></a>
                    </div>

                </div>
            </section>


            
        </div>
        <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">

            <div class="tabs">
                
                <div class="tab-content">
                    <div id="overview" class="tab-pane active">
                        <div class="timeline timeline-simple mt-xlg mb-md">
                            <form class="form-horizontal" method="post" action="<?= base_url('profile/aksi_edit_profile')?>">
                            <h4 class="mb-xlg">Informasi Personal</h4>
                            <fieldset>
                                <div class="form-group">
                                    <label class="col-md-3 control-label" for="profileFirstName">Nama Lengkap</label>
                                    <div class="col-md-8">
                                        <input type="hidden" class="form-control" name = "karyawan_id" id="profileFirstName" value="<?= $profile['karyawan_id']?>">
                                        <input type="text" class="form-control" name = "nama_lengkap" id="profileFirstName" value="<?= $profile['nama_lengkap']?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label" for="profileFirstName">Jenis Kelamin</label>
                                    <div class="col-md-8">
                                        <select name="jenis_kelamin" class="form-control">
                                            <option value="L" <?= $profile['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                            <option value="P" <?= $profile['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label" for="profileLastName">Tempat Lahir</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="tempat_lahir" id="profileLastName" value="<?= $profile['tempat_lahir']?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label" for="profileAddress">Tanggal Lahir</label>
                                    <div class="col-md-8">
                                        <input type="date" class="form-control" name="tanggal_lahir" id="profileAddress" value="<?= $profile['tanggal_lahir']?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label" for="profileAddress">No WA Aktif</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="telepon" id="profileAddress" value="<?= $profile['telepon']?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label" for="profileAddress">Alamat Email</label>
                                    <div class="col-md-8">
                                        <input type="email" class="form-control" name="email" id="profileAddress" value="<?= $profile['email']?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label" for="profileCompany">Alamat Rumah</label>
                                    <div class="col-md-8">
                                        <textarea name="alamat" id="" class="form-control"><?= $profile['alamat']?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label" for="profileCompany">Foto Profile</label>
                                    <div class="col-md-8">
                                        <?php if (!empty($karyawan->foto)) : ?>
                                            <img src="<?= base_url('assets/images/karyawan/' . $profile['foto']) ?>" width="100" class="mb-2">
                                        <?php endif; ?>
                                        <input type="file" name="foto" class="form-control-file">
                                        <small class="form-text text-muted">Format: JPG/PNG. Max: 2MB.</small>
                                    </div>
                                </div>
                            </fieldset>
                            <div class="panel-footer">
                                <div class="row">
                                    <div class="col-md-9 col-md-offset-3">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <button type="reset" class="btn btn-default">Reset</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                            <hr class="dotted tall">
                            <h4 class="mb-xlg">Ubah Password</h4>
                            <form class="form-horizontal" method="post" action="<?= base_url('profile/ganti_password')?>">
                            <fieldset class="mb-xl">
                                <div class="form-group">
                                    <label class="col-md-3 control-label" for="profileNewPassword">Password Lama</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="profileNewPassword" name="password_lama">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label" for="profileNewPassword">Password Baru</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="profileNewPassword" name="password_baru">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label" for="profileNewPasswordRepeat">Ulangi Password Baru</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="profileNewPasswordRepeat" name="konfirmasi">
                                    </div>
                                </div>
                            </fieldset>
                            <div class="panel-footer">
                                <div class="row">
                                    <div class="col-md-9 col-md-offset-3">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <button type="reset" class="btn btn-default">Reset</button>
                                    </div>
                                </div>
                            </div>

                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
            <h4 class="mb-md">Kinerja Karyawan</h4>
            <ul class="simple-card-list mb-xlg">
                <li class="primary">
                    <h3>Jumlah Kehadiran</h3>
                    <p>26</p>
                </li>
                <li class="primary">
                    <h3>Jumlah Lembur</h3>
                    <p><?= $jam_lembur['total_jam_lembur'] ?> Jam</p>
                </li>
                <li class="primary">
                    <h3>Jumlah Ketidakhadiran</h3>
                    <p>0</p>
                </li>
                <li class="dark">
                    <a href="<?= base_url('karyawan/pengajuan_lembur')?>" class="btn btn-dark form-control">Pengajuan Lembur</a>
                </li>
            </ul>
        </div>

    </div>