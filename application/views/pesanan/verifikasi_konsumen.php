<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Verifikasi Pesanan <?= htmlspecialchars($nospk) ?></h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center mt-5">
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 rounded-lg">
                        <div class="card-body p-4 text-center">
                            <h4 class="mb-3 text-primary"><i class="fas fa-lock"></i> Perlindungan Privasi</h4>
                            <p class="text-muted">Sistem kami mendeteksi Anda mengakses tautan ini sebagai tamu. Untuk melihat pesanan, silakan buktikan kepemilikan pesanan ini dengan memasukkan <b>4 Digit Terakhir</b> dari nomor WhatsApp pemesan yang tercatat.</p>
                            
                            <?php if ($this->session->flashdata('error_pin')): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?= $this->session->flashdata('error_pin') ?>
                                </div>
                            <?php endif; ?>

                            <h5 class="mb-4">Nomor HP: <?= $mask_nohp ?></h5>

                            <form action="<?= current_url() ?>" method="POST">
                                <div class="form-group">
                                    <input type="text" name="pin_hp" class="form-control form-control-lg text-center font-weight-bold" placeholder="Masukkan 4 Digit Terakhir" maxlength="4" required autofocus autocomplete="off" style="letter-spacing: 0.5em; font-size: 1.5rem;">
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg btn-block mt-4">Buka Pesanan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
