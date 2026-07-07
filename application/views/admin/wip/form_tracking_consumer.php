<div class="app-content">
    <div class="content-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="page-title fw-800 text-primary fs-3 mb-0">
                    <i class="fas fa-eye me-2"></i> Client View Mirror
                </h1>
                <p class="text-muted small mb-0">Lihat halaman tracking persis seperti tampilan di HP konsumen.</p>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg rounded-4 border-0">
                    <div class="card-body p-5 text-center">
                        <div class="mb-4">
                            <span class="d-inline-block p-4 bg-primary bg-opacity-10 rounded-circle text-primary mb-3">
                                <i class="fas fa-search-location fa-3x"></i>
                            </span>
                            <h4 class="fw-900 text-dark">Tracking Simulator</h4>
                            <p class="text-muted small">Masukkan Nomor SPK untuk melihat progress.</p>
                        </div>

                        <?= $this->session->flashdata('message'); ?>

                        <form action="<?= base_url('wip/check_client_view') ?>" method="post">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control fw-bold fs-5 text-center rounded-3" id="spkInput"
                                    name="spk" placeholder="Contoh: 2-01-xxxx" required autocomplete="off" autofocus>
                                <label for="spkInput" class="text-center w-100">NOMOR SPK</label>
                            </div>
                            <button type="submit"
                                class="btn btn-primary btn-lg w-100 rounded-pill fw-900 shadow-sm py-3">
                                <i class="fas fa-search me-2"></i> CEK SEKARANG
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>