<?php
// Get data for dropdowns
$marketplaces = $this->db->get('tbl_marketplace')->result();
$pengiriman = $this->db->get('tbl_pengiriman')->result();
?>

<div class="header-wizard-container animate__animated animate__fadeIn">
    <!-- QUICK NAVIGATION BAR -->
    <div class="d-flex gap-2 mb-4 px-1 flex-wrap">
        <!-- <a href="<?= site_url('transaksi/transaksi_deskprint') ?>"
            class="btn btn-white fw-900 shadow-sm border px-3 d-flex align-items-center"
            style="height: 42px; border-radius: 8px;">
            <i class="fas fa-list-ul me-2 text-dark"></i> <span
                style="font-size: 13px; letter-spacing: 0.5px;">TRANSAKSI</span>
        </a> -->
        <a href="<?= site_url('transaksi/draft') ?>"
            class="btn btn-white fw-900 shadow-sm border px-3 d-flex align-items-center"
            style="height: 42px; border-radius: 8px;">
            <i class="fas fa-folder-open me-2 text-dark"></i> <span
                style="font-size: 13px; letter-spacing: 0.5px;">DRAFT</span>
        </a>
        <a href="<?= site_url('kiosk/data_order') ?>"
            class="btn btn-secondary shadow-sm px-3 d-flex align-items-center border-0"
            style="height: 42px; border-radius: 8px; background: #64748b;">
            <i class="fas fa-tablet-alt me-2"></i> <span style="font-size: 13px; letter-spacing: 0.5px;">ANTRIAN
                KIOSK</span>
        </a>
        <a href="#" class="btn btn-info text-white shadow-sm px-3 d-flex align-items-center border-0"
            style="height: 42px; border-radius: 8px; background: #0dcaf0;">
            <i class="fas fa-book-reader me-2"></i> <span style="font-size: 13px; letter-spacing: 0.5px;">PANDUAN</span>
        </a>
    </div>
    <?php if ($this->uri->segment(3) == NULL): ?>
        <!-- LANGKAH 1: METODE TRANSAKSI -->
        <div class="report-section-card mb-4">
            <div class="section-header py-2"
                style="background: var(--secondary-gradient); border-bottom: 2px solid rgba(255,255,255,0.1);">
                <h6 class="mb-0 text-white small fw-bold"><i class="fas fa-layer-group me-2"></i>LANGKAH 1: PILIH METODE
                    TRANSAKSI</h6>
                <span class="badge bg-primary shadow-sm" style="font-size: 0.6rem;">REQUIRED</span>
            </div>
            <div class="section-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="choice-card <?= ($getkosumen['id_jenis_transaksi'] ?? '1') == '1' ? 'active' : '' ?>"
                            onclick="selectOrderMethod(1, this)">
                            <div class="check-icon"><i class="fas fa-check-circle"></i></div>
                            <i class="fas fa-store-alt fa-2x mb-2 text-primary"></i>
                            <span class="fw-bold">ON THE SPOT / WALK-IN</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="choice-card <?= ($getkosumen['id_jenis_transaksi'] ?? '1') == '2' ? 'active' : '' ?>"
                            onclick="selectOrderMethod(2, this)">
                            <div class="check-icon"><i class="fas fa-check-circle"></i></div>
                            <i class="fas fa-shopping-basket fa-2x mb-2 text-warning"></i>
                            <span class="fw-bold">MARKETPLACE (RESI/TIKTOK)</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="choice-card <?= ($getkosumen['id_jenis_transaksi'] ?? '1') == '3' ? 'active' : '' ?>"
                            onclick="selectOrderMethod(3, this)">
                            <div class="check-icon"><i class="fas fa-check-circle"></i></div>
                            <i class="fab fa-whatsapp fa-2x mb-2 text-success"></i>
                            <span class="fw-bold">ORDERS VIA WHATSAPP / ONLINE</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- LANGKAH 2: DATA PELANGGAN -->
    <div class="report-section-card mb-4 overflow-visible">
        <div class="section-header py-2" style="background: var(--primary-gradient);">
            <h6 class="mb-0 text-white small fw-bold" id="step2_title">
                <i
                    class="fas fa-user-edit me-2"></i><?= $this->uri->segment(3) == NULL ? 'LANGKAH 2: LENGKAPI DATA PELANGGAN' : 'DATA PENERIMA & MANIFEST' ?>
            </h6>
            <div id="cust_badge_type">
                <?php if (($getkosumen['id_jenis_transaksi'] ?? '1') == '2'): ?>
                    <span class="badge bg-warning text-dark fw-bold border" style="font-size: 0.65rem;">ONE-TIME
                        CUSTOMER</span>
                <?php else: ?>
                    <span class="badge bg-light text-dark fw-bold border" style="font-size: 0.65rem;">DATABASE CRM</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="section-body p-4 bg-white">
            <form id="form-step-1">
                <input type="hidden" name="nospk" value="<?= $nospk ?>">
                <input type="hidden" name="id_jenis_transaksi" id="selected_jenis_transaksi"
                    value="<?= $getkosumen['id_jenis_transaksi'] ?? '1' ?>">

                <?php if ($this->uri->segment(3) == NULL): ?>
                    <!-- SEARCH & INPUT AREA -->
                    <div id="konsumen_db_search" class="animate__animated animate__fadeInUp"
                        style="<?= ($getkosumen['id_jenis_transaksi'] ?? '1') == '2' ? 'display:none;' : '' ?>">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">CARI DATA PELANGGAN
                                    DI DATABASE</label>
                                <div class="d-flex align-items-center bg-white border rounded-3 shadow-sm px-3"
                                    style="height: 50px;">
                                    <i class="fas fa-search text-primary me-2"></i>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <select id="konsumenSelect1" name="konsumen_id"
                                            class="form-control select2 border-0">
                                            <option value="">Ketik Nama / No. WA / Email Konsumen...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mt-3 mt-md-0 d-flex align-items-end" style="padding-top: 1.8rem;">
                                <button type="button" class="btn btn-dark w-100 py-3 shadow-sm fw-bold"
                                    data-bs-toggle="modal" data-bs-target="#modalTambahKonsumen">
                                    <i class="fas fa-user-plus me-2"></i> KONSUMEN BARU
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- MARKETPLACE SPECIAL AREA -->
                    <div id="konsumen_manual_input" class="animate__animated animate__fadeInUp"
                        style="<?= ($getkosumen['id_jenis_transaksi'] ?? '1') == '2' ? '' : 'display:none;' ?>">
                        <div class="row g-3">
                            <div class="col-md-5 border-end">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase mb-2">Pilih Channel
                                        Marketplace</label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-light"><i
                                                class="fas fa-shopping-cart text-primary"></i></span>
                                        <select name="marketplace_id" id="id_marketplace"
                                            class="form-select fw-bold border-primary-light">
                                            <?php foreach ($marketplaces as $m): ?>
                                                <option value="<?= $m->marketplace_id ?>">
                                                    <?= strtoupper($m->marketplace_nama) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase mb-2">Pilih Kurir /
                                        Expedisi</label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-light"><i
                                                class="fas fa-truck text-primary"></i></span>
                                        <select name="id_pengiriman" class="form-select fw-bold border-primary-light">
                                            <option value="0">-- Pilih Kurir --</option>
                                            <?php foreach ($pengiriman as $p): ?>
                                                <option value="<?= $p->id_pengiriman ?>"><?= strtoupper($p->nama_pengiriman) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small fw-bold text-muted text-uppercase mb-2">Deadline / Batch
                                        Produksi</label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-light"><i
                                                class="fas fa-clock text-primary"></i></span>
                                        <select name="batch_pengiriman" class="form-select fw-bold border-primary-light">
                                            <option value="">-- Pilih Batch --</option>
                                            <option value="Batch 1 (jam 12 Siang)">Batch 1 (jam 12 Siang)</option>
                                            <option value="Batch 2 (Jam 16 Sore)">Batch 2 (Jam 16 Sore)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">Nama Pembeli
                                            (App)</label>
                                        <input type="text" name="m_nama_konsumen" id="m_nama_konsumen"
                                            class="form-control border-dark shadow-sm" placeholder="Nama di Marketplace">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">No. Resi /
                                            Invoice</label>
                                        <input type="text" name="resi_marketplace"
                                            class="form-control fw-bold text-primary shadow-sm" value="-"
                                            onclick="this.select()">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">Alamat
                                            Lengkap / Kota</label>
                                        <textarea name="m_alamat_konsumen" id="m_alamat_konsumen"
                                            class="form-control border-dark shadow-sm" rows="3"
                                            placeholder="Masukkan alamat pengiriman..."></textarea>
                                    </div>
                                </div>
                                <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle me-1"></i> Data di atas
                                    digunakan untuk label manifest logistik.</small>
                            </div>
                        </div>
                    </div>

                    <!-- SUBMIT BUTTON -->
                    <div class="mt-4 pt-3 border-top text-center">
                        <button type="submit" id="submitBtn"
                            class="btn btn-premium btn-premium-primary rounded-pill px-5 py-3 shadow-lg fs-5 w-100"
                            disabled>
                            <i class="fas fa-rocket me-2 animate__animated animate__pulse animate__infinite"></i> MULAI BUAT
                            SPK SEKARANG
                        </button>
                    </div>

                <?php else: ?>
                    <!-- TAMPILAN SETELAH SPK DIBUAT (INFO MODE) -->
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <div
                                class="p-3 bg-light rounded-4 border text-center shadow-sm h-100 d-flex flex-column justify-content-center">
                                <p class="mb-0 text-muted small fw-bold text-uppercase">SPK NUMBER</p>
                                <div class="d-flex align-items-center justify-content-center gap-2"
                                    onclick="copySpk('<?= $nospk ?>', this)" title="Klik untuk menyalin"
                                    style="cursor: pointer;">
                                    <h4 class="mb-0 text-dark fw-bold font-monospace m-0"><?= $nospk ?></h4>
                                    <i class="fas fa-copy text-secondary"
                                        style="font-size: 1.1rem; transition: color 0.2s;"></i>
                                </div>

                                <script>
                                    if (typeof copySpk !== 'function') {
                                        function copySpk(text, element) {
                                            navigator.clipboard.writeText(text).then(function () {
                                                let icon = element.querySelector('i');
                                                let oldClass = icon.className;
                                                icon.className = 'fas fa-check-circle text-success';

                                                if (typeof Swal !== 'undefined') {
                                                    Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 1500 })
                                                        .fire({ icon: 'success', title: 'No SPK Disalin!' });
                                                }

                                                setTimeout(() => { icon.className = oldClass; }, 1500);
                                            }).catch(err => {
                                                console.error('Gagal menyalin text: ', err);
                                            });
                                        }
                                    }
                                </script>
                                <hr class="my-2">
                                <p class="mb-0 text-primary small fw-bold"><?= date('d F Y', strtotime($hariini)) ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-4 border bg-white shadow-sm h-100">
                                <div class="d-flex align-items-center">
                                    <div
                                        class="avatar-sm bg-primary-soft rounded-circle shadow-sm me-3 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-user-check text-primary"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <p class="mb-0 text-muted small fw-bold text-uppercase">NAMA KONSUMEN</p>
                                        <h6 class="mb-0 fw-bold text-dark text-truncate">
                                            <?= $getkosumen['konsumen_nama'] ?? 'UMUM' ?>
                                        </h6>
                                        <p class="mb-0 small text-success"><i
                                                class="fab fa-whatsapp me-1"></i><?= $getkosumen['konsumen_nohp'] ?? '-' ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div
                                class="p-3 bg-primary-soft rounded-4 border border-primary border-opacity-10 shadow-sm h-100">
                                <p class="mb-1 text-primary small fw-bold text-uppercase"><i
                                        class="fas fa-truck-loading me-2"></i>MANIFEST LOGISTIK</p>
                                <div class="d-flex gap-2 mb-2 flex-wrap">
                                    <div class="badge bg-white text-dark border shadow-sm p-2 px-3 rounded-pill">
                                        <i class="fas fa-truck me-2 text-primary"></i>
                                        <span
                                            class="fw-bold"><?= strtoupper($getkosumen['nama_pengiriman'] ?? 'BELUM DIPILIH') ?></span>
                                    </div>
                                    <div class="badge bg-white text-dark border shadow-sm p-2 px-3 rounded-pill">
                                        <i class="fas fa-history me-2 text-primary"></i>
                                        <span class="fw-bold"><?= $getkosumen['batch_pengiriman'] ?: 'TANPA BATCH' ?></span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center bg-white p-2 px-3 rounded-3 border small shadow-sm">
                                    <i class="fas fa-shopping-cart text-primary me-2"></i>
                                    <span
                                        class="fw-bold"><?= strtoupper($getkosumen['marketplace_nama'] ?? 'REGULER') ?></span>
                                    <span class="mx-2 text-muted">|</span>
                                    <span
                                        class="text-truncate font-monospace fw-bold text-primary"><?= $getkosumen['resi_marketplace'] ?? '-' ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<style>
    .bg-primary-soft {
        background-color: rgba(59, 130, 246, 0.08);
    }

    .border-primary-light {
        border-color: rgba(59, 130, 246, 0.2);
    }

    .header-wizard-container .choice-card {
        min-height: 120px;
    }

    .avatar-sm {
        width: 40px;
        height: 40px;
    }

    .header-wizard-container .select2-container--default .select2-selection--single {
        border: none !important;
        background: transparent !important;
    }

    .header-wizard-container .select2-container {
        width: 100% !important;
    }

    .btn-white {
        background: #fff;
        color: #334155;
        border-color: #e2e8f0;
    }

    .btn-white:hover {
        background: #f8fafc;
        color: #1e293b;
    }
</style>