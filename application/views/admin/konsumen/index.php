<div class="app-content mt-2">
    <div class="container-fluid">
        <!-- HEADER SECTION -->
        <?php $this->load->view('layout_admin/header_modul'); ?>

        <!-- Flash Messages -->
        <div id="alert-container">
            <?= $this->session->flashdata('message'); ?>
        </div>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-check-circle fs-5"></i>
                    <div class="fw-bold"><?= $this->session->flashdata('success') ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-exclamation-triangle fs-5"></i>
                    <div class="fw-bold"><?= $this->session->flashdata('error') ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <!-- AKSI CEPAT & NAVIGASI -->
        <div class="card border-0 shadow-sm mb-4 animate-up">
            <div class="card-body p-3 d-flex flex-wrap gap-2 align-items-center">
                <h6 class="fw-bold mb-0 me-3 d-none d-md-block"><i class="fas fa-bolt text-warning me-2"></i>Aksi Cepat:
                </h6>

                <button class="btn btn-primary text-white fw-bold text-nowrap rounded-pill px-3 shadow-sm">
                    <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                </button>
                <a href="<?= base_url('pengaturan/konsumen_list') ?>"
                    class="btn btn-outline-success fw-bold text-nowrap rounded-pill px-3">
                    <i class="fas fa-database me-1"></i> Database
                </a>
                <a href="<?= base_url('pengaturan/konsumen_aktivitas') ?>"
                    class="btn btn-outline-info fw-bold text-nowrap rounded-pill px-3">
                    <i class="fas fa-history me-1"></i> Aktivitas
                </a>

                <div class="vr mx-2 d-none d-md-block"></div>
                <hr class="d-md-none w-100 my-1 opacity-25">

                <button class="btn btn-outline-primary fw-bold text-nowrap rounded-pill px-3 shadow-sm"
                    data-bs-toggle="modal" data-bs-target="#modalKonsumen">
                    <i class="fas fa-plus me-1"></i> Tambah
                </button>
                <button class="btn btn-outline-success fw-bold text-nowrap rounded-pill px-3"
                    onclick="exportCustomers()">
                    <i class="fas fa-file-excel me-1"></i> Export Data
                </button>
            </div>
        </div>

        <!-- STATISTICS VIEW -->
        <div class="row g-3 mb-4 animate-up" style="animation-delay: 0.1s">
            <div class="col-md-4">
                <div class="p-3 bg-white rounded-4 shadow-sm border-0 h-100 d-flex align-items-center">
                    <div class="rounded-circle bg-light p-3 me-3 text-primary"><i class="fas fa-users fa-2x"></i></div>
                    <div>
                        <div class="text-muted small fw-bold">TOTAL DATABASE</div>
                        <h3 class="fw-900 mb-0"><?= number_format($total_customers) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-white rounded-4 shadow-sm border-0 h-100 d-flex align-items-center">
                    <div class="rounded-circle bg-light p-3 me-3 text-success"><i class="fas fa-user-check fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold">KONSUMEN AKTIF</div>
                        <h3 class="fw-900 mb-0"><?= number_format($active_customers) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-primary text-white rounded-4 shadow-sm border-0 h-100 d-flex align-items-center justify-content-between"
                    style="background: var(--primary-gradient)">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-white bg-opacity-25 p-3 me-3"><i class="fas fa-plus fa-2x"></i>
                        </div>
                        <div>
                            <div class="text-white text-opacity-75 small fw-bold">ENTRY DATA</div>
                            <div class="fw-bold">TAMBAH BARU</div>
                        </div>
                    </div>
                    <button class="btn btn-white btn-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal"
                        data-bs-target="#modalKonsumen">KLIK DISINI</button>
                </div>
            </div>
        </div>

        <!-- MAIN TABLE CARD -->
        <div class="report-section-card animate-up" style="animation-delay: 0.1s">
            <div class="section-header">
                <h5><i class="fas fa-list me-2"></i> Top 10 Konsumen Teratas</h5>
                <div class="d-flex gap-2">
                    <a href="<?= base_url('pengaturan/konsumen_list') ?>"
                        class="btn btn-light btn-premium rounded-pill px-3 shadow-sm btn-sm fw-bold">
                        <i class="fas fa-database me-2"></i>Database Full
                    </a>
                    <button type="button" class="btn btn-light btn-premium rounded-pill px-3 shadow-sm btn-sm fw-bold"
                        onclick="exportCustomers()">
                        <i class="fas fa-file-excel text-success me-1"></i> Export
                    </button>
                    <button type="button" class="btn btn-light btn-premium rounded-pill px-3 shadow-sm btn-sm fw-bold"
                        onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table premium-table table-hover mb-0" id="customerTable">
                    <thead>
                        <tr>
                            <th class="ps-4">Konsumen</th>
                            <th>Kontak Personal</th>
                            <th>Status Akun</th>
                            <th class="text-end pe-4">Total Transaksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($top_customers)): ?>
                            <?php
                            $limit = 0;
                            foreach ($top_customers as $customer):
                                if ($limit >= 10)
                                    break;
                                $limit++;
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light p-2 rounded-circle border me-3 text-secondary d-flex align-items-center justify-content-center shadow-sm"
                                                style="width: 45px; height: 45px;">
                                                <i class="fas fa-user-tie fa-lg"></i>
                                            </div>
                                            <div>
                                                <div class="fw-800 text-dark"><?= $customer->konsumen_nama ?></div>
                                                <div class="small text-muted fw-bold"><i
                                                        class="fas fa-map-marker-alt me-1 text-danger opacity-50"></i><?= $customer->konsumen_alamat ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <?php if ($customer->konsumen_nohp): ?>
                                                <div class="small fw-bold text-dark"><i
                                                        class="fas fa-phone-alt me-2 text-success"></i>
                                                    <?= $customer->konsumen_nohp ?></div>
                                            <?php else: ?>
                                                <div class="small text-muted">-</div>
                                            <?php endif; ?>

                                            <?php if ($customer->konsumen_email): ?>
                                                <div class="small text-muted"><i class="fas fa-envelope me-2 text-primary"></i>
                                                    <?= $customer->konsumen_email ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $status = $customer->status ?? '1';
                                        if ($status == '1'): ?>
                                            <span
                                                class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 fw-bold">Active</span>
                                        <?php else: ?>
                                            <span
                                                class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 fw-bold">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <span class="fw-900 text-dark fs-6">Rp
                                            <?= number_format($customer->total_transaksi ?? 0) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="fas fa-users-slash fs-1 text-muted opacity-25 mb-3"></i>
                                    <h6 class="fw-800 text-muted">Belum ada data transaksi konsumen.</h6>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Konsumen -->
<div class="modal fade" id="modalKonsumen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-dark text-white p-4 border-0">
                <h5 class="modal-title fw-900 text-uppercase"><i class="fas fa-user-plus me-2"></i> Tambah Konsumen Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="formKonsumen" method="POST" action="<?= base_url('pengaturan/konsumen_add') ?>">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Nama Lengkap <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control fw-bold" name="konsumen_nama" required
                                placeholder="Nama Konsumen"
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;">
                        </div>
                        <div class="col-12">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Alamat Lengkap <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control fw-bold" name="konsumen_alamat" rows="2" required
                                placeholder="Alamat Domisili"
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small text-uppercase mb-2">WhatsApp / HP <span
                                    class="text-danger">*</span></label>
                            <input type="tel" class="form-control fw-bold" name="konsumen_nohp" placeholder="08..."
                                required maxlength="15" minlength="10"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small text-uppercase mb-2">Email</label>
                            <input type="email" class="form-control fw-bold" name="konsumen_email"
                                placeholder="email@contoh.com"
                                style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                        data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow"
                        style="background: var(--primary-gradient)">
                        SIMPAN DATA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</div>

<script>
    function escapeCSV(text) {
        if (!text) return '""';
        // Escape double quotes by doubling them and wrapping the whole thing in double quotes
        return '"' + text.toString().replace(/"/g, '""').trim() + '"';
    }

    function exportCustomers() {
        const table = document.querySelector('#customerTable');
        if (!table) return;

        let csvContent = "Nama,Email,Telepon,Status,Total Transaksi\n";
        const rows = table.querySelectorAll('tbody tr');

        rows.forEach(row => {
            if (row.cells.length < 4) return;

            const name = row.cells[0].querySelector('.fw-800') ? row.cells[0].querySelector('.fw-800').innerText : '';

            // For Email & Phone, we extract the text precisely
            const emailEl = row.cells[1].querySelector('.text-primary');
            const email = emailEl ? emailEl.innerText.trim() : '';

            const phoneEl = row.cells[1].querySelector('.text-success');
            const phone = phoneEl ? phoneEl.innerText.trim() : '';

            const status = row.cells[2].innerText.trim();
            const total = row.cells[3].innerText.trim().replace('Rp', '').replace(/\./g, '').trim();

            const rowData = [
                escapeCSV(name),
                escapeCSV(email),
                escapeCSV(phone),
                escapeCSV(status),
                escapeCSV(total)
            ];

            csvContent += rowData.join(',') + "\n";
        });

        const blob = new Blob(["\ufeff" + csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'data_dashboard_konsumen_' + new Date().toISOString().slice(0, 10) + '.csv';
        link.click();
    }



    // Auto-hide alert after 5 seconds
    setTimeout(function () {
        $('#alert-container .alert').fadeOut('slow');
    }, 5000);
</script>