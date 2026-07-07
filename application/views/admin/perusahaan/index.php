<div class="app-content mt-2">
    <div class="container-fluid">
        <!-- Enhanced Header -->
        <?php $this->load->view('layout_admin/header_modul'); ?>
<?php $this->load->view('admin/pengaturan/_settings_nav'); ?>

        <!-- Flash Messages -->
        <div id="alert-container">
            <?= $this->session->flashdata('message'); ?>
        </div>

        <!-- Statistics Overview -->
        <div class="row g-3 mb-4 animate-up">
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--primary-gradient)">
                        <i class="fas fa-building"></i>
                    </div>
                    <div>
                        <div class="stat-v2-label">Total Perusahaan</div>
                        <div class="stat-v2-value" data-value="<?= count($tampilData) ?>">0</div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--success-gradient)">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <div class="stat-v2-label">Perusahaan Aktif</div>
                        <div class="stat-v2-value" data-value="<?= count($tampilData) ?>">0</div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--warning-gradient)">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div>
                        <div class="stat-v2-label">Lokasi Cabang</div>
                        <div class="stat-v2-value" data-value="<?= count($tampilData) ?>">0</div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--info-gradient)">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <div class="stat-v2-label">Email Terintegrasi</div>
                        <div class="stat-v2-value" data-value="<?= count($tampilData) ?>">0</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Table -->
        <div class="report-section-card animate-up" style="animation-delay: 0.1s">
            <div class="section-header">
                <h5><i class="fas fa-building me-2"></i> Manajemen Data Perusahaan</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-white text-dark rounded-pill px-3 fw-bold shadow-sm"
                        onclick="exportData()">
                        <i class="fas fa-file-excel text-success me-1"></i> Export
                    </button>
                    <button class="btn btn-sm btn-white text-dark rounded-pill px-3 fw-bold shadow-sm"
                        onclick="printData()">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="perusahaanTable" class="table premium-table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th width="25%">Perusahaan</th>
                            <th width="30%">Alamat & Lokasi</th>
                            <th width="15%">Kontak</th>
                            <th width="15%">Status & Email</th>
                            <th class="text-center" width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($tampilData as $row): ?>
                            <tr>
                                <td class="text-center fw-bold text-muted"><?= $no++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-light p-1 rounded shadow-sm border">
                                            <img src="<?= base_url('assets/images/' . ($row->logo ?: 'default-logo.png')) ?>"
                                                style="width: 40px; height: 40px; object-fit: contain;" alt="Logo">
                                        </div>
                                        <div>
                                            <div class="fw-800 text-dark">
                                                <?= htmlspecialchars($row->nama_perusahaan) ?>
                                            </div>
                                            <div class="d-flex align-items-center gap-1 mt-1">
                                                <?php if ($row->is_holding): ?>
                                                    <span
                                                        class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2"
                                                        style="font-size: 0.65rem;">HOLDING</span>
                                                <?php else: ?>
                                                    <span
                                                        class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2"
                                                        style="font-size: 0.65rem;">CABANG</span>
                                                <?php endif; ?>
                                                <small class="text-primary fw-bold"
                                                    style="font-size: 0.7rem;"><?= htmlspecialchars($row->website ?: '-') ?></small>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 250px;">
                                        <i class="fas fa-map-marker-alt text-danger me-1 small"></i>
                                        <?= htmlspecialchars($row->alamat_perusahaan) ?>
                                    </div>
                                    <?php if ($row->latitude && $row->longitude): ?>
                                        <div class="small text-muted mt-1">
                                            <i class="fas fa-location-arrow me-1 x-small"></i>
                                            <?= $row->latitude ?>, <?= $row->longitude ?>
                                            <span
                                                class="badge bg-light text-dark border ms-1 rounded-pill"><?= $row->radius_meter ?? 100 ?>m</span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark" style="font-size: 0.85rem;">
                                        <i
                                            class="fab fa-whatsapp text-success me-1"></i><?= htmlspecialchars($row->no_wa) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="small fw-bold text-dark">
                                        <i
                                            class="fas fa-envelope text-muted me-1"></i><?= htmlspecialchars($row->email ?: '-') ?>
                                    </div>
                                    <span
                                        class="badge bg-success-subtle text-success border border-success-subtle rounded-pill mt-1"
                                        style="font-size: 0.65rem;">AKTIF</span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn-action-premium btn-edit-p edit-btn"
                                        data-id="<?= $row->id_perusahaan ?>"
                                        data-nama="<?= htmlspecialchars($row->nama_perusahaan) ?>"
                                        data-alamat="<?= htmlspecialchars($row->alamat_perusahaan) ?>"
                                        data-wa="<?= htmlspecialchars($row->no_wa) ?>"
                                        data-email="<?= htmlspecialchars($row->email) ?>"
                                        data-website="<?= htmlspecialchars($row->website) ?>"
                                        data-lat="<?= $row->latitude ?>" data-long="<?= $row->longitude ?>"
                                        data-radius="<?= $row->radius_meter ?>"
                                        data-payroll-start="<?= isset($row->payroll_start_day) ? $row->payroll_start_day : 26 ?>"
                                        data-payroll-end="<?= isset($row->payroll_end_day) ? $row->payroll_end_day : 25 ?>"
                                        data-is-holding="<?= $row->is_holding ?>" data-parent-id="<?= $row->parent_id ?>"
                                        data-owner="<?= htmlspecialchars($row->owner_name) ?>"
                                        data-npwp="<?= htmlspecialchars($row->npwp) ?>" data-logo="<?= $row->logo ?>"
                                        data-invoice-paper="<?= isset($row->invoice_paper) ? htmlspecialchars($row->invoice_paper) : '80mm' ?>"
                                        data-invoice-font-size="<?= isset($row->invoice_font_size) ? htmlspecialchars($row->invoice_font_size) : 14 ?>"
                                        data-invoice-show-logo="<?= isset($row->invoice_show_logo) ? htmlspecialchars($row->invoice_show_logo) : 1 ?>"
                                        data-invoice-logo-width="<?= isset($row->invoice_logo_width) ? htmlspecialchars($row->invoice_logo_width) : 50 ?>"
                                        data-invoice-show-qr="<?= isset($row->invoice_show_qr) ? htmlspecialchars($row->invoice_show_qr) : 1 ?>"
                                        data-invoice-footer-note="<?= isset($row->invoice_footer_note) ? htmlspecialchars($row->invoice_footer_note) : '' ?>"
                                        title="Edit Data">
                                        <i class="fas fa-pen-nib"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Refactored Single Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="<?= base_url('pengaturan/perusahaan_update') ?>" method="post" enctype="multipart/form-data"
            class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-dark text-white border-0 p-4">
                <h5 class="modal-title fw-900"><i class="fas fa-edit me-2"></i>Edit Data Perusahaan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="id_perusahaan" id="edit_id">

                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="row g-3">
                            <!-- Identity -->
                            <div class="col-md-6">
                                <label class="small text-uppercase fw-bold text-muted mb-2 d-block">NAMA PERUSAHAAN /
                                    CABANG</label>
                                <input type="text" name="nama_perusahaan" id="edit_nama" class="form-control"
                                    style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-uppercase fw-bold text-muted mb-2 d-block">TIPE ENTITAS</label>
                                <select name="is_holding" id="edit_is_holding" class="form-select"
                                    style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
                                    <option value="0">Cabang / Unit Bisnis</option>
                                    <option value="1">Perusahaan Utama (Holding)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="small text-uppercase fw-bold text-muted mb-2 d-block">OWNER /
                                    PEMILIK</label>
                                <input type="text" name="owner_name" id="edit_owner" class="form-control"
                                    style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                                    placeholder="Nama Pemilik">
                            </div>
                            <div class="col-md-6">
                                <label class="small text-uppercase fw-bold text-muted mb-2 d-block">NPWP
                                    PERUSAHAAN</label>
                                <input type="text" name="npwp" id="edit_npwp" class="form-control"
                                    style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                                    placeholder="00.000.000.0-000.000">
                            </div>

                            <div class="col-md-6">
                                <label class="small text-uppercase fw-bold text-muted mb-2 d-block">INDUK
                                    PERUSAHAAN</label>
                                <select name="parent_id" id="edit_parent_id" class="form-select"
                                    style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
                                    <option value="0">Tidak Ada (Berdiri Sendiri)</option>
                                    <?php foreach ($tampilData as $p): ?>
                                        <?php if ($p->is_holding): ?>
                                            <option value="<?= $p->id_perusahaan ?>"><?= $p->nama_perusahaan ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-uppercase fw-bold text-muted mb-2 d-block">NO TELP / WA</label>
                                <input type="text" name="no_wa" id="edit_wa" class="form-control"
                                    style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                                    required>
                            </div>

                            <!-- Online Presence -->
                            <div class="col-md-6">
                                <label class="small text-uppercase fw-bold text-muted mb-2 d-block">EMAIL RESMI</label>
                                <input type="email" name="email" id="edit_email" class="form-control"
                                    style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
                            </div>
                            <div class="col-md-6">
                                <label class="small text-uppercase fw-bold text-muted mb-2 d-block">WEBSITE</label>
                                <input type="text" name="website" id="edit_website" class="form-control"
                                    style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
                            </div>

                            <div class="col-12">
                                <hr class="my-1 text-muted opacity-25">
                            </div>

                            <!-- Location -->
                            <div class="col-12 mt-1 mb-0">
                                <span class="badge bg-light text-dark border"><i
                                        class="fas fa-map-marker-alt me-1 text-danger"></i> KOORDINAT LOKASI</span>
                            </div>
                            <div class="col-md-5">
                                <label class="small text-uppercase fw-bold text-muted mb-2 d-block">LATITUDE</label>
                                <input type="text" name="latitude" id="edit_latitude" class="form-control"
                                    style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                                    placeholder="-6.xxxxx">
                            </div>
                            <div class="col-md-5">
                                <label class="small text-uppercase fw-bold text-muted mb-2 d-block">LONGITUDE</label>
                                <input type="text" name="longitude" id="edit_longitude" class="form-control"
                                    style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                                    placeholder="106.xxxxx">
                            </div>
                            <div class="col-md-2">
                                <label class="small text-uppercase fw-bold text-muted mb-2 d-block"
                                    title="Radius dalam meter">RADIUS</label>
                                <input type="number" name="radius_meter" id="edit_radius" class="form-control"
                                    style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"
                                    placeholder="100">
                            </div>

                            <div class="col-12">
                                <label class="small text-uppercase fw-bold text-muted mb-2 d-block">ALAMAT
                                    LENGKAP</label>
                                <textarea name="alamat_perusahaan" id="edit_alamat" class="form-control" rows="3"
                                    style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;"></textarea>
                            </div>

                            <div class="col-12">
                                <hr class="my-1 text-muted opacity-25">
                            </div>

                            <!-- Payroll Settings -->
                            <div class="col-12 mt-1 mb-0">
                                <span class="badge bg-light text-dark border"><i
                                        class="fas fa-calendar-alt me-1 text-success"></i> PERIODE GAJI</span>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-uppercase fw-bold text-muted mb-2 d-block">TANGGAL AWAL
                                    (CUTOFF)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"
                                        style="border: 2px solid #f1f5f9; border-radius: 12px 0 0 12px;">Tgl</span>
                                    <input type="number" name="payroll_start_day" id="edit_payroll_start"
                                        class="form-control border-start-0" min="1" max="31" placeholder="26"
                                        style="border: 2px solid #f1f5f9; border-radius: 0 12px 12px 0; padding: 0.75rem 1.25rem; font-weight: 600;">
                                </div>
                                <div class="form-text x-small mt-1">Bulan Lalu</div>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-uppercase fw-bold text-muted mb-2 d-block">TANGGAL AKHIR
                                    (TUTUP)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"
                                        style="border: 2px solid #f1f5f9; border-radius: 12px 0 0 12px;">Tgl</span>
                                    <input type="number" name="payroll_end_day" id="edit_payroll_end"
                                        class="form-control border-start-0" min="1" max="31" placeholder="25"
                                        style="border: 2px solid #f1f5f9; border-radius: 0 12px 12px 0; padding: 0.75rem 1.25rem; font-weight: 600;">
                                </div>
                                <div class="form-text x-small mt-1">Bulan Ini</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 text-center border-start ps-4">
                        <label class="small text-uppercase fw-bold text-muted mb-3 d-block">LOGO PERUSAHAAN</label>
                        <div class="mx-auto bg-light p-2 rounded-3 border"
                            style="width: 150px; height: 150px; display: flex; align-items: center; justify-content: center;">
                            <img id="logoPreview" src="<?= base_url('assets/images/default-logo.png') ?>" alt="Preview"
                                style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        </div>
                        <div class="mt-3">
                            <input type="file" name="logo" id="logoInput" class="form-control form-control-sm d-none"
                                accept="image/*">
                            <button type="button" class="btn btn-outline-primary rounded-pill px-4 fw-bold shadow-sm"
                                onclick="$('#logoInput').click()">
                                <i class="fas fa-upload me-1"></i> Ganti Logo
                            </button>
                            <div class="mt-2 text-muted fw-bold" style="font-size: 0.7rem;">Max 2MB (JPG, PNG, SVG)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                    data-bs-dismiss="modal">BATAL</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow"
                    style="background: var(--primary-gradient)">
                    <i class="fas fa-save me-2"></i>SIMPAN PERUBAHAN
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Animate numbers
        $('.stat-v2-value').each(function () {
            var $this = $(this);
            var countTo = $this.data('value');
            $({ countNum: $this.text() }).animate({
                countNum: countTo
            }, {
                duration: 1500,
                easing: 'swing',
                step: function () {
                    $this.text(Math.floor(this.countNum));
                },
                complete: function () {
                    $this.text(this.countNum);
                }
            });
        });

        // Initialize DataTable if plugin exists
        if ($.fn.DataTable) {
            $('#perusahaanTable').DataTable({
                "pageLength": 10,
                "language": {
                    "sSearch": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data"
                },
                "dom": "<'row px-4 py-3'<'col-md-6'l><'col-md-6 d-flex justify-content-end text-end'f>>t<'row px-4 py-3'<'col-md-6'i><'col-md-6 d-flex justify-content-end text-end'p>>"
            });
        }

        // Edit button handler
        $('.edit-btn').on('click', function () {
            const data = $(this).data();
            $('#edit_id').val(data.id);
            $('#edit_nama').val(data.nama);
            $('#edit_wa').val(data.wa);
            $('#edit_email').val(data.email);
            $('#edit_website').val(data.website);
            $('#edit_alamat').val(data.alamat);
            $('#edit_latitude').val(data.lat);
            $('#edit_longitude').val(data.long);
            $('#edit_radius').val(data.radius || 100);
            $('#edit_payroll_start').val(data.payrollStart || 26);
            $('#edit_payroll_end').val(data.payrollEnd || 25);

            // New Hierarchy fields
            $('#edit_is_holding').val(data.isHolding || 0);
            $('#edit_parent_id').val(data.parentId || 0);
            $('#edit_owner').val(data.owner || '');
            $('#edit_npwp').val(data.npwp || '');

            // Invoice Template Fields
            $('#edit_invoice_paper').val(data.invoicePaper || '80mm');
            $('#edit_invoice_font_size').val(data.invoiceFontSize || 14);
            $('#edit_invoice_show_logo').val(data.invoiceShowLogo !== undefined ? data.invoiceShowLogo : 1);
            $('#edit_invoice_logo_width').val(data.invoiceLogoWidth || 50);
            $('#edit_invoice_show_qr').val(data.invoiceShowQr !== undefined ? data.invoiceShowQr : 1);
            $('#edit_invoice_footer_note').val(data.invoiceFooterNote || '');

            const logoPath = data.logo ? '<?= base_url('assets/images/') ?>' + data.logo : '<?= base_url('assets/images/default-logo.png') ?>';
            $('#logoPreview').attr('src', logoPath);

            $('#editModal').modal('show');
        });

        // Logo preview handler
        $('#logoInput').on('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $('#logoPreview').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });

        // Auto-hide alert after 5 seconds
        setTimeout(function () {
            $('#alert-container .alert').fadeOut('slow');
        }, 5000);
    });

    function exportData() {
        // Simple CSV export logic
        let csv = 'No,Nama Perusahaan,Alamat,No Telp,Email,Website\n';
        $('#perusahaanTable tbody tr').each(function (i) {
            const row = $(this);
            const data = [
                i + 1,
                row.find('.fw-800.text-dark').text().trim(),
                row.find('td:nth-child(3)').text().trim().replace(/,/g, ''),
                row.find('td:nth-child(4)').text().trim(),
                row.find('.small i.fa-envelope').parent().text().trim(),
                row.find('.text-primary').text().trim()
            ];
            csv += data.join(',') + '\n';
        });

        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'data_perusahaan_' + new Date().getTime() + '.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }

    function printData() {
        window.print();
    }
</script>