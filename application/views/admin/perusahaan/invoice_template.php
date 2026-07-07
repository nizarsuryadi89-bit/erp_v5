<div class="app-content mt-2">
    <div class="container-fluid">
        <!-- Enhanced Header -->
        <?php $this->load->view('layout_admin/header_modul'); ?>

        <!-- Flash Messages -->
        <div id="alert-container">
            <?= $this->session->flashdata('message'); ?>
        </div>

        <!-- Quick Actions & Table -->
        <div class="report-section-card animate-up" style="animation-delay: 0.1s">
            <div class="section-header">
                <h5><i class="fas fa-receipt me-2"></i> Pengaturan Cetak Template Invoice</h5>
            </div>

            <div class="table-responsive">
                <table id="templateTable" class="table premium-table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th width="25%">Perusahaan / Cabang</th>
                            <th width="15%">Lebar Kertas</th>
                            <th width="15%">Ukuran Huruf</th>
                            <th width="15%">Logo & QR Code</th>
                            <th width="20%">Catatan Kaki (Footer)</th>
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
                                            <small class="text-muted" style="font-size: 0.7rem;">
                                                <?= $row->is_holding ? 'Holding / Pusat' : 'Cabang / Unit Bisnis' ?>
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 fw-bold">
                                        <i class="fas fa-print me-1"></i>
                                        <?= htmlspecialchars($row->invoice_paper ?: '80mm') ?>
                                    </span>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3 fw-bold">
                                        <i class="fas fa-text-height me-1"></i>
                                        <?= htmlspecialchars($row->invoice_font_size ?: 11) ?> px
                                    </span>
                                    <div class="small fw-bold mt-1 text-muted" style="font-size: 0.75rem;">
                                        Style: <?= htmlspecialchars($row->invoice_font_family ?: 'Roboto') ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="small fw-bold">
                                        Logo:
                                        <?= (!isset($row->invoice_show_logo) || $row->invoice_show_logo == 1) ? '<span class="text-success">Tampil (' . ($row->invoice_logo_width ?: 50) . '%)</span>' : '<span class="text-danger">Sembunyi</span>' ?>
                                    </div>
                                    <div class="small fw-bold">
                                        QR Code:
                                        <?= (!isset($row->invoice_show_qr) || $row->invoice_show_qr == 1) ? '<span class="text-success">Tampil (' . ($row->invoice_qr_width ?: 50) . '%)</span>' : '<span class="text-danger">Sembunyi</span>' ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-truncate text-muted small" style="max-width: 200px;"
                                        title="<?= htmlspecialchars($row->invoice_footer_note ?: '-') ?>">
                                        <?= htmlspecialchars($row->invoice_footer_note ?: 'Tidak Menerima KOMPLAIN atas Kerusakan/kesalahan PRINT DILUAR ACC atau APPROVAL') ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button" class="btn-action-premium btn-edit-p edit-template-btn"
                                            data-id="<?= $row->id_perusahaan ?>"
                                            data-nama="<?= htmlspecialchars($row->nama_perusahaan) ?>"
                                            data-logo="<?= $row->logo ?>"
                                            data-invoice-paper="<?= isset($row->invoice_paper) ? htmlspecialchars($row->invoice_paper) : '80mm' ?>"
                                            data-invoice-font-size="<?= isset($row->invoice_font_size) ? htmlspecialchars($row->invoice_font_size) : 11 ?>"
                                            data-invoice-font-family="<?= isset($row->invoice_font_family) ? htmlspecialchars($row->invoice_font_family) : 'Roboto' ?>"
                                            data-invoice-show-logo="<?= isset($row->invoice_show_logo) ? htmlspecialchars($row->invoice_show_logo) : 1 ?>"
                                            data-invoice-logo-width="<?= isset($row->invoice_logo_width) ? htmlspecialchars($row->invoice_logo_width) : 50 ?>"
                                            data-invoice-show-qr="<?= isset($row->invoice_show_qr) ? htmlspecialchars($row->invoice_show_qr) : 1 ?>"
                                            data-invoice-qr-width="<?= isset($row->invoice_qr_width) ? htmlspecialchars($row->invoice_qr_width) : 50 ?>"
                                            data-invoice-footer-note="<?= isset($row->invoice_footer_note) ? htmlspecialchars($row->invoice_footer_note) : '' ?>"
                                            data-invoice-section-spacing="<?= isset($row->invoice_section_spacing) ? htmlspecialchars($row->invoice_section_spacing) : 8 ?>"
                                            data-tag-paper="<?= isset($row->tag_paper) ? htmlspecialchars($row->tag_paper) : '80mm' ?>"
                                            data-tag-font-size="<?= isset($row->tag_font_size) ? htmlspecialchars($row->tag_font_size) : 11 ?>"
                                            data-tag-font-family="<?= isset($row->tag_font_family) ? htmlspecialchars($row->tag_font_family) : 'Roboto' ?>"
                                            data-tag-show-logo="<?= isset($row->tag_show_logo) ? htmlspecialchars($row->tag_show_logo) : 1 ?>"
                                            data-tag-logo-width="<?= isset($row->tag_logo_width) ? htmlspecialchars($row->tag_logo_width) : 50 ?>"
                                            data-tag-show-qr="<?= isset($row->tag_show_qr) ? htmlspecialchars($row->tag_show_qr) : 1 ?>"
                                            data-tag-qr-width="<?= isset($row->tag_qr_width) ? htmlspecialchars($row->tag_qr_width) : 50 ?>"
                                            data-tag-footer-note="<?= isset($row->tag_footer_note) ? htmlspecialchars($row->tag_footer_note) : '' ?>"
                                            data-tag-section-spacing="<?= isset($row->tag_section_spacing) ? htmlspecialchars($row->tag_section_spacing) : 8 ?>"
                                            title="Atur Template">
                                            <i class="fas fa-sliders-h text-primary"></i>
                                        </button>
                                        <a href="<?= base_url('pengaturan/test_print_invoice/' . $row->id_perusahaan) ?>"
                                            target="_blank" class="btn-action-premium btn-edit-p"
                                            style="background: #e2e8f0; border-radius: 8px;" title="Test Print/Cek Printer">
                                            <i class="fas fa-print text-secondary"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Standalone Invoice Template Settings Modal with Live Preview -->
<div class="modal fade" id="templateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form action="<?= base_url('pengaturan/invoice_template_update') ?>" method="post"
            class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-dark text-white border-0 p-4">
                <h5 class="modal-title fw-900"><i class="fas fa-sliders-h me-2 text-primary"></i>Desain Template Invoice
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="id_perusahaan" id="template_id">
                <input type="hidden" name="active_tab" id="active_tab" value="invoice">
                <ul class="nav nav-tabs" id="templateTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="invoice-tab" data-bs-toggle="tab" data-bs-target="#invoice-pane" type="button" role="tab">Invoice</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="tag-tab" data-bs-toggle="tab" data-bs-target="#tag-pane" type="button" role="tab">Tag Produksi</button>
                    </li>
                </ul>
                <div class="tab-content pt-3">
                    <div class="tab-pane fade show active" id="invoice-pane" role="tabpanel">
                        <div class="row g-4">
                            <!-- Form Controls (Left Column) -->
                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label class="small text-uppercase fw-bold text-muted mb-2 d-block">PERUSAHAAN /<br> CABANG</label>
                                    <input type="text" id="template_nama_display" class="form-control bg-light border-0 fw-bold" readonly style="border-radius: 12px; padding: 0.75rem 1.25rem;">
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">JENIS / LEBAR KERTAS</label>
                                        <select name="invoice_paper" id="template_invoice_paper" class="form-select" style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
                                            <option value="80mm">80mm (Thermal Printer)</option>
                                            <option value="58mm">58mm (Thermal Kecil)</option>
                                            <option value="A4">A4 (Standard Printer)</option>
                                            <option value="A5">A5 (Setengah A4)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">UKURAN HURUF (FONT SIZE)</label>
                                        <div class="input-group">
                                            <input type="number" name="invoice_font_size" id="template_invoice_font_size" class="form-control" min="6" placeholder="11" style="border: 2px solid #f1f5f9; border-radius: 12px 0 0 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
                                            <span class="input-group-text bg-white border-start-0" style="border: 2px solid #f1f5f9; border-radius: 0 12px 12px 0;">px</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">JENIS HURUF (FONT FAMILY)</label>
                                        <select name="invoice_font_family" id="template_invoice_font_family" class="form-select" style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
                                            <option value="Roboto">Roboto (Clean Modern - Default)</option>
                                            <option value="Arial">Arial (Standard Sans-serif)</option>
                                            <option value="Courier New">Courier New (Classic Thermal Typewriter)</option>
                                            <option value="Inter">Inter (Premium Sans)</option>
                                            <option value="monospace">Monospace (Terminal Console)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">JARAK ANTAR BAGIAN (SPACING)</label>
                                        <div class="input-group">
                                            <input type="number" name="invoice_section_spacing" id="template_invoice_section_spacing" class="form-control" min="2" max="30" placeholder="8" style="border: 2px solid #f1f5f9; border-radius: 12px 0 0 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
                                            <span class="input-group-text bg-white border-start-0" style="border: 2px solid #f1f5f9; border-radius: 0 12px 12px 0;">px</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">TAMPILKAN LOGO</label>
                                        <select name="invoice_show_logo" id="template_invoice_show_logo" class="form-select" style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
                                            <option value="1">Ya, Tampilkan Logo</option>
                                            <option value="0">Tidak, Sembunyikan Logo</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">LEBAR LOGO (%)</label>
                                        <div class="input-group">
                                            <input type="number" name="invoice_logo_width" id="template_invoice_logo_width" class="form-control" min="10" max="100" placeholder="50" style="border: 2px solid #f1f5f9; border-radius: 12px 0 0 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
                                            <span class="input-group-text bg-white border-start-0" style="border: 2px solid #f1f5f9; border-radius: 0 12px 12px 0;">%</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">TAMPILKAN QR CODE</label>
                                        <select name="invoice_show_qr" id="template_invoice_show_qr" class="form-select" style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
                                            <option value="1">Ya, Tampilkan QR Code Pelacakan</option>
                                            <option value="0">Tidak, Sembunyikan QR Code</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">LEBAR QR CODE (%)</label>
                                        <div class="input-group">
                                            <input type="number" name="invoice_qr_width" id="template_invoice_qr_width" class="form-control" min="10" max="100" placeholder="50" style="border: 2px solid #f1f5f9; border-radius: 12px 0 0 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
                                            <span class="input-group-text bg-white border-start-0" style="border: 2px solid #f1f5f9; border-radius: 0 12px 12px 0;">%</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">CATATAN KAKI (FOOTER NOTE)</label>
                                        <textarea name="invoice_footer_note" id="template_invoice_footer_note" class="form-control" rows="4" style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;" placeholder="Contoh: Terima Kasih Atas Kunjungan Anda"></textarea>
                                    </div>
                                </div>
                            </div>
                            <!-- Live Preview (Right Column) -->
                            <div class="col-lg-6 border-start ps-lg-4 d-flex flex-column align-items-center bg-light py-4 rounded-4" style="max-height: 520px; overflow-y: auto;">
                                <h6 class="small text-uppercase fw-bold text-muted mb-3"><i class="fas fa-eye me-1 text-primary"></i> LIVE PREVIEW INVOICE</h6>
                                <div id="receipt_preview" class="receipt-simulator shadow-sm border p-4 bg-white text-dark" style="font-family: 'Roboto', 'Arial', sans-serif; min-height: 380px; transition: all 0.3s ease; box-sizing: border-box;">
                                    <!-- Logo Preview -->
                                    <div id="receipt_logo_container" class="text-center mb-2">
                                        <img id="receipt_logo_img" src="<?php echo base_url('assets/images/default-logo.png') ?>" alt="Logo" style="max-height: 70px; object-fit: contain; width: 50%;">
                                    </div>
                                    <!-- Header -->
                                    <div class="text-center receipt-header">
                                        <div id="receipt_comp_name" class="fw-bold" style="font-size: 1.1em; line-height: 1.2;">PT. Trigemilang Mandiri</div>
                                        <div style="font-size: 0.85em; opacity: 0.85; margin-top: 2px;">Jl. Danau Sunter Utara No. 12, Sunter Agung</div>
                                        <div style="font-size: 0.8em; opacity: 0.8;">Email: info@trigemilang.com</div>
                                        <hr style="border: none; border-top: 2px dashed #000; margin: 8px 0;">
                                        <div class="fw-bold" style="font-size: 1.05em; letter-spacing: 0.5px;">No Invoice: INV-20260519001</div>
                                        <hr style="border: none; border-top: 2px dashed #000; margin: 8px 0;">
                                    </div>
                                    <!-- Metadata Table -->
                                    <table style="width: 100%; border-collapse: collapse; font-size: 0.9em; margin-bottom: 8px; border: none;">
                                        <tr style="border: none;">
                                            <td width="30%" style="padding: 1px 0; border: none;">Konsumen</td>
                                            <td width="5%" style="padding: 1px 0; border: none;">:</td>
                                            <td style="padding: 1px 0; border: none;">Budi Santoso</td>
                                        </tr>
                                        <tr style="border: none;">
                                            <td style="padding: 1px 0; border: none;">HP</td>
                                            <td style="padding: 1px 0; border: none;">:</td>
                                            <td style="padding: 1px 0; border: none;">08123456789</td>
                                        </tr>
                                        <tr style="border: none;">
                                            <td style="padding: 1px 0; border: none;">Kasir</td>
                                            <td style="padding: 1px 0; border: none;">:</td>
                                            <td style="padding: 1px 0; border: none;">Nizarsuryadi</td>
                                        </tr>
                                    </table>
                                    <!-- Items Table -->
                                    <table style="width: 100%; border-collapse: collapse; font-size: 0.9em; margin-bottom: 8px; border: none;">
                                        <thead>
                                            <tr style="border-bottom: 2px dashed #000;">
                                                <th style="text-align: left; padding: 4px 0; font-weight: bold; border: none;">Product</th>
                                                <th style="text-align: center; padding: 4px 0; font-weight: bold; width: 15%; border: none;">Qty</th>
                                                <th style="text-align: right; padding: 4px 0; font-weight: bold; width: 25%; border: none;">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr style="border-bottom: 1px dotted #888;">
                                                <td style="padding: 6px 0; vertical-align: top; border: none;"><b>Cetak Banner Albatros</b><br><span style="font-size: 0.85em; opacity: 0.85;">2 x 1 M</span></td>
                                                <td style="text-align: center; padding: 6px 0; vertical-align: top; border: none;">1</td>
                                                <td style="text-align: right; padding: 6px 0; vertical-align: top; border: none;">120.000</td>
                                            </tr>
                                            <tr style="border-bottom: 1px dotted #888;">
                                                <td style="padding: 6px 0; vertical-align: top; border: none;"><b>Jasa Desain Standar</b></td>
                                                <td style="text-align: center; padding: 6px 0; vertical-align: top; border: none;">1</td>
                                                <td style="text-align: right; padding: 6px 0; vertical-align: top; border: none;">50.000</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <hr style="border: none; border-top: 2px dashed #000; margin: 8px 0;">
                                    <!-- Calculations -->
                                    <table style="width: 100%; border-collapse: collapse; font-size: 0.9em; margin-bottom: 8px; border: none;">
                                        <tr style="border: none;">
                                            <td style="padding: 2px 0; border: none;">Sub Total</td>
                                            <td style="text-align: right; padding: 2px 0; border: none;">170.000</td>
                                        </tr>
                                        <tr style="border: none; font-weight: bold;">
                                            <td style="padding: 2px 0; border: none;">Total Bayar</td>
                                            <td style="text-align: right; padding: 2px 0; border: none;">170.000</td>
                                        </tr>
                                        <tr style="border: none;">
                                            <td style="padding: 2px 0; border: none;">Status</td>
                                            <td style="text-align: right; padding: 2px 0; font-weight: bold; border: none;" class="text-success">LUNAS</td>
                                        </tr>
                                    </table>
                                    <div id="receipt_footer_section" class="text-center" style="font-size: 0.9em; line-height: 1.3;">
                                        <hr style="border: none; border-top: 2px dashed #000; margin: 8px 0;">
                                        <div id="receipt_footer_note_val">Tidak Menerima <b>KOMPLAIN</b> atas Kerusakan/kesalahan <b>PRINT DILUAR ACC atau APPROVAL</b></div>
                                    </div>
                                    <div id="receipt_qr_section" class="text-center mt-2">
                                        <hr style="border: none; border-top: 2px dashed #000; margin: 8px 0;">
                                        <div id="receipt_qr_wrapper" class="d-inline-block border p-1 bg-white mb-1 shadow-xs" style="width: 50%;">
                                            <i id="receipt_qr_icon" class="fas fa-qrcode text-dark" style="font-size: 38px !important; display: block; margin: 0 auto; text-align: center;"></i>
                                        </div>
                                        <div style="font-size: 0.75em; opacity: 0.8; margin-top: 2px;">Scan di sini untuk cek posisi pesanan anda</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tag-pane" role="tabpanel">
                        <div class="row g-4">
                            <!-- Form Controls (Left Column) -->
                            <div class="col-lg-6">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">JENIS / LEBAR KERTAS</label>
                                        <select name="tag_paper" id="template_tag_paper" class="form-select" style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
                                            <option value="80mm">80mm (Thermal Printer)</option>
                                            <option value="58mm">58mm (Thermal Kecil)</option>
                                            <option value="A6">A6 (Kertas Label Kecil)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">UKURAN HURUF (FONT SIZE)</label>
                                        <div class="input-group">
                                            <input type="number" name="tag_font_size" id="template_tag_font_size" class="form-control" min="6" placeholder="11" style="border: 2px solid #f1f5f9; border-radius: 12px 0 0 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
                                            <span class="input-group-text bg-white border-start-0" style="border: 2px solid #f1f5f9; border-radius: 0 12px 12px 0;">px</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">JENIS HURUF (FONT FAMILY)</label>
                                        <select name="tag_font_family" id="template_tag_font_family" class="form-select" style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
                                            <option value="Roboto">Roboto (Clean Modern - Default)</option>
                                            <option value="Arial">Arial (Standard Sans-serif)</option>
                                            <option value="Courier New">Courier New (Classic Thermal Typewriter)</option>
                                            <option value="Inter">Inter (Premium Sans)</option>
                                            <option value="monospace">Monospace (Terminal Console)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">JARAK ANTAR BAGIAN (SPACING)</label>
                                        <div class="input-group">
                                            <input type="number" name="tag_section_spacing" id="template_tag_section_spacing" class="form-control" min="2" max="30" placeholder="8" style="border: 2px solid #f1f5f9; border-radius: 12px 0 0 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
                                            <span class="input-group-text bg-white border-start-0" style="border: 2px solid #f1f5f9; border-radius: 0 12px 12px 0;">px</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">TAMPILKAN LOGO</label>
                                        <select name="tag_show_logo" id="template_tag_show_logo" class="form-select" style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
                                            <option value="1">Ya, Tampilkan Logo</option>
                                            <option value="0">Tidak, Sembunyikan Logo</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">LEBAR LOGO (%)</label>
                                        <div class="input-group">
                                            <input type="number" name="tag_logo_width" id="template_tag_logo_width" class="form-control" min="10" max="100" placeholder="50" style="border: 2px solid #f1f5f9; border-radius: 12px 0 0 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
                                            <span class="input-group-text bg-white border-start-0" style="border: 2px solid #f1f5f9; border-radius: 0 12px 12px 0;">%</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">TAMPILKAN QR CODE</label>
                                        <select name="tag_show_qr" id="template_tag_show_qr" class="form-select" style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
                                            <option value="1">Ya, Tampilkan QR Code Pelacakan</option>
                                            <option value="0">Tidak, Sembunyikan QR Code</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">LEBAR QR CODE (%)</label>
                                        <div class="input-group">
                                            <input type="number" name="tag_qr_width" id="template_tag_qr_width" class="form-control" min="10" max="100" placeholder="50" style="border: 2px solid #f1f5f9; border-radius: 12px 0 0 12px; padding: 0.75rem 1.25rem; font-weight: 600;">
                                            <span class="input-group-text bg-white border-start-0" style="border: 2px solid #f1f5f9; border-radius: 0 12px 12px 0;">%</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="small text-uppercase fw-bold text-muted mb-2 d-block">CATATAN KAKI (FOOTER NOTE)</label>
                                        <textarea name="tag_footer_note" id="template_tag_footer_note" class="form-control" rows="3" style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 1.25rem; font-weight: 600;" placeholder="Contoh: Pastikan Tag Selalu Terpasang pada Barang"></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Live Preview (Right Column) -->
                            <div class="col-lg-6 border-start ps-lg-4 d-flex flex-column align-items-center bg-light py-4 rounded-4" style="max-height: 520px; overflow-y: auto;">
                                <h6 class="small text-uppercase fw-bold text-muted mb-3"><i class="fas fa-eye me-1 text-primary"></i> LIVE PREVIEW TAG PRODUKSI</h6>
                                <div id="tag_preview_sim" class="receipt-simulator shadow-sm border p-4 bg-white text-dark" style="font-family: 'Roboto', 'Arial', sans-serif; min-height: 380px; transition: all 0.3s ease; box-sizing: border-box;">
                                    
                                    <div id="tag_logo_container" class="text-center mb-2">
                                        <img id="tag_logo_img" src="<?php echo base_url('assets/images/default-logo.png') ?>" alt="Logo" style="max-height: 70px; object-fit: contain; width: 50%;">
                                    </div>
                                    
                                    <div class="text-center receipt-header">
                                        <div id="tag_comp_name" class="fw-bold" style="font-size: 1.1em; line-height: 1.2;">PT. Trigemilang Mandiri</div>
                                        <div style="font-size: 1em; margin-top: 5px; font-weight: bold; border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 5px 0;">TAG PRODUKSI</div>
                                        <div class="fw-bold" style="font-size: 1.05em; letter-spacing: 0.5px; margin-top: 5px;">SPK: SPK-2026-0001</div>
                                    </div>
                                    
                                    <table style="width: 100%; border-collapse: collapse; font-size: 0.9em; margin-bottom: 8px; border: none; margin-top: 8px;">
                                        <tr style="border: none;">
                                            <td width="35%" style="padding: 2px 0; border: none; font-weight: bold;">TGL</td>
                                            <td width="5%" style="padding: 2px 0; border: none; font-weight: bold;">:</td>
                                            <td style="padding: 2px 0; border: none;">26/06/2026</td>
                                        </tr>
                                        <tr style="border: none;">
                                            <td style="padding: 2px 0; border: none; font-weight: bold;">CUSTOMER</td>
                                            <td style="padding: 2px 0; border: none; font-weight: bold;">:</td>
                                            <td style="padding: 2px 0; border: none;">Budi Santoso</td>
                                        </tr>
                                        <tr style="border: none;">
                                            <td style="padding: 2px 0; border: none; font-weight: bold;">DIVISI</td>
                                            <td style="padding: 2px 0; border: none; font-weight: bold;">:</td>
                                            <td style="padding: 2px 0; border: none;">Digital A3+</td>
                                        </tr>
                                        <tr style="border: none;">
                                            <td style="padding: 2px 0; border: none; font-weight: bold; vertical-align: top;">ITEM</td>
                                            <td style="padding: 2px 0; border: none; font-weight: bold; vertical-align: top;">:</td>
                                            <td style="padding: 2px 0; border: none; font-weight: bold; font-size: 1.1em;">Cetak Banner Albatros</td>
                                        </tr>
                                        <tr style="border: none;">
                                            <td style="padding: 2px 0; border: none; font-weight: bold;">QTY</td>
                                            <td style="padding: 2px 0; border: none; font-weight: bold;">:</td>
                                            <td style="padding: 2px 0; border: none; font-weight: bold;">10 Pcs</td>
                                        </tr>
                                        <tr style="border: none;">
                                            <td style="padding: 2px 0; border: none; font-weight: bold; vertical-align: top;">KET</td>
                                            <td style="padding: 2px 0; border: none; font-weight: bold; vertical-align: top;">:</td>
                                            <td style="padding: 2px 0; border: none;">Tanpa Laminasi, Ukuran 2x1</td>
                                        </tr>
                                    </table>
                                    
                                    <div id="tag_footer_section" class="text-center" style="font-size: 0.9em; line-height: 1.3;">
                                        <hr style="border: none; border-top: 2px dashed #000; margin: 8px 0;">
                                        <div id="tag_footer_note_val">Tempelkan Tag ini pada barang yang sudah jadi</div>
                                    </div>
                                    
                                    <div id="tag_qr_section" class="text-center mt-2">
                                        <hr style="border: none; border-top: 2px dashed #000; margin: 8px 0;">
                                        <div id="tag_qr_wrapper" class="d-inline-block border p-1 bg-white mb-1 shadow-xs" style="width: 50%;">
                                            <i id="tag_qr_icon" class="fas fa-qrcode text-dark" style="font-size: 38px !important; display: block; margin: 0 auto; text-align: center;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
            </div>
            <div class="modal-footer border-0 p-4 pt-0 d-flex justify-content-between w-100">
                <div>
                    <button type="button" onclick="testPrintModal()"
                        class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                        <i class="fas fa-print me-2"></i>CEK PRINTER (TEST)
                    </button>
                </div>
                <div>
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                        data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow"
                        style="background: var(--primary-gradient)">
                        <i class="fas fa-save me-2"></i>SIMPAN TEMPLATE
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    /* Premium style for receipt mockup */
    .receipt-simulator {
        border-radius: 4px;
        background-color: #fff;
        line-height: 1.3;
        color: #000 !important;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08) !important;
        border: 1px solid #ddd !important;
    }
</style>

<script>
    $(document).ready(function () {
        // Initialize DataTable if plugin exists
        if ($.fn.DataTable) {
            $('#templateTable').DataTable({
                "pageLength": 10,
                "language": {
                    "sSearch": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data"
                },
                "dom": "<'row px-4 py-3'<'col-md-6'l><'col-md-6 d-flex justify-content-end text-end'f>>t<'row px-4 py-3'<'col-md-6'i><'col-md-6 d-flex justify-content-end text-end'p>>"
            });
        }

        // Live Preview Dynamic Renderer
        function updateInvoicePreview() {
            const paper = $('#template_invoice_paper').val();
            const fontSize = parseInt($('#template_invoice_font_size').val()) || 11;
            const showLogo = $('#template_invoice_show_logo').val();
            const logoWidth = parseInt($('#template_invoice_logo_width').val()) || 50;
            const showQr = $('#template_invoice_show_qr').val();
            const qrWidth = parseInt($('#template_invoice_qr_width').val()) || 50;
            const footerNote = $('#template_invoice_footer_note').val();
            const spacing = parseInt($('#template_invoice_section_spacing').val()) || 8;

            const receipt = $('#receipt_preview');

            // 1. Handle Paper width simulation
            if (paper === '80mm') {
                receipt.css({ 'width': '300px', 'max-width': '300px' });
            } else if (paper === '58mm') {
                receipt.css({ 'width': '220px', 'max-width': '220px' });
                        } else if (paper === 'A4') {
                receipt.css({ 'width': '210mm', 'max-width': '210mm' });
            } else if (paper === 'A5') {
                receipt.css({ 'width': '148mm', 'max-width': '148mm' });
            }

            // 2. Font sizing & Font family
            const fontFamily = $('#template_invoice_font_family').val() || 'Roboto';
            let fontCss = "'Roboto', sans-serif";
            if (fontFamily === 'Arial') {
                fontCss = "Arial, sans-serif";
            } else if (fontFamily === 'Courier New') {
                fontCss = "'Courier New', Courier, monospace";
            } else if (fontFamily === 'Inter') {
                fontCss = "'Inter', sans-serif";
            } else if (fontFamily === 'monospace') {
                fontCss = "monospace";
            }
            receipt.css({ 'font-size': fontSize + 'px', 'font-family': fontCss });
            receipt.find('table').css({ 'font-size': (fontSize - 1) + 'px', 'font-family': fontCss });
            receipt.find('.receipt-header').css({ 'font-size': fontSize + 'px', 'font-family': fontCss });

            // 3. Logo toggle & size
            if (showLogo === '1') {
                $('#receipt_logo_container').show();
                $('#receipt_logo_img').css('width', logoWidth + '%');
            } else {
                $('#receipt_logo_container').hide();
            }

            // 4. QR Code toggle & size
            if (showQr === '1') {
                $('#receipt_qr_section').show();
                $('#receipt_qr_wrapper').css('width', qrWidth + '%');
                // Proportional icon font-size
                $('#receipt_qr_icon').css('font-size', Math.max(16, Math.round(qrWidth * 0.75)) + 'px');
            } else {
                $('#receipt_qr_section').hide();
            }

            // 5. Footer note text
            if (footerNote.trim()) {
                $('#receipt_footer_note_val').text(footerNote);
            } else {
                $('#receipt_footer_note_val').html('Tidak Menerima <b>KOMPLAIN</b> atas Kerusakan/kesalahan <b>PRINT DILUAR ACC atau APPROVAL</b>');
            }

            // 6. Section spacing simulation
            receipt.find('hr').css({ 'margin': spacing + 'px 0' });
            receipt.find('table').css({ 'margin-bottom': spacing + 'px' });

            // Metadata table rows padding
            receipt.find('table').eq(0).find('td').css({ 'padding-top': Math.max(1, Math.round(spacing / 4)) + 'px', 'padding-bottom': Math.max(1, Math.round(spacing / 4)) + 'px' });

            // Item list table td/th padding
            receipt.find('table').eq(1).find('td, th').css({ 'padding-top': spacing + 'px', 'padding-bottom': spacing + 'px' });

            // Calculation table rows padding
            receipt.find('table').eq(2).find('td').css({ 'padding-top': Math.max(1, Math.round(spacing / 4)) + 'px', 'padding-bottom': Math.max(1, Math.round(spacing / 4)) + 'px' });

            $('#receipt_logo_container').css({ 'margin-bottom': spacing + 'px' });
            $('#receipt_qr_section').css({ 'margin-top': spacing + 'px' });
            $('#receipt_footer_section').css({ 'margin-top': spacing + 'px' });
        }

        // Bind events to update preview instantly
        $('#template_invoice_paper, #template_invoice_font_size, #template_invoice_font_family, #template_invoice_show_logo, #template_invoice_logo_width, #template_invoice_show_qr, #template_invoice_qr_width, #template_invoice_footer_note, #template_invoice_section_spacing').on('input change', function () {
            updateInvoicePreview();
        });

        // Edit template button handler
        $('.edit-template-btn').on('click', function () {
            const data = $(this).data();
            $('#template_id').val(data.id);
            $('#template_nama_display').val(data.nama);
            $('#receipt_comp_name').text(data.nama);

            // Dynamically set logo inside mockup
            const logoPath = data.logo ? '<?= base_url('assets/images/') ?>' + data.logo : '<?= base_url('assets/images/default-logo.png') ?>';
            $('#receipt_logo_img').attr('src', logoPath);

            // Populate form values
            // Populate Tag Produksi form values
            $('#template_tag_paper').val(data.tagPaper || '80mm');
            $('#template_tag_font_size').val(data.tagFontSize || 11);
            $('#template_tag_font_family').val(data.tagFontFamily || 'Roboto');
            $('#template_tag_show_logo').val(data.tagShowLogo !== undefined ? data.tagShowLogo : 1);
            $('#template_tag_logo_width').val(data.tagLogoWidth || 50);
            $('#template_tag_show_qr').val(data.tagShowQr !== undefined ? data.tagShowQr : 1);
            $('#template_tag_qr_width').val(data.tagQrWidth || 50);
            $('#template_tag_footer_note').val(data.tagFooterNote || '');
            $('#template_tag_section_spacing').val(data.tagSectionSpacing || 8);

            // Trigger preview renderer
            updateInvoicePreview();
            updateTagPreview();

            $('#templateModal').modal('show');
        });

        // Auto-hide alert after 5 seconds
        setTimeout(function () {
            $('#alert-container .alert').fadeOut('slow');
        }, 5000);

        // Tab change handler to set active_tab
        $('#templateTab button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr('id'); // active tab button id
            if (target === 'invoice-tab') {
                $('#active_tab').val('invoice');
            } else {
                $('#active_tab').val('tag');
            }
        });

        // Update preview for Tag Produksi
        function updateTagPreview() {
            const paper = $('#template_tag_paper').val();
            const fontSize = parseInt($('#template_tag_font_size').val()) || 11;
            const showLogo = $('#template_tag_show_logo').val();
            const logoWidth = parseInt($('#template_tag_logo_width').val()) || 50;
            const showQr = $('#template_tag_show_qr').val();
            const qrWidth = parseInt($('#template_tag_qr_width').val()) || 50;
            const footerNote = $('#template_tag_footer_note').val();
            const spacing = parseInt($('#template_tag_section_spacing').val()) || 8;

            const receipt = $('#tag_preview_sim');

            // 1. Handle Paper width simulation
            if (paper === '80mm') {
                receipt.css({ 'width': '300px', 'max-width': '300px' });
            } else if (paper === '58mm') {
                receipt.css({ 'width': '220px', 'max-width': '220px' });
            } else if (paper === 'A6') {
                receipt.css({ 'width': '105mm', 'max-width': '105mm' });
            }

            // 2. Font sizing & Font family
            const fontFamily = $('#template_tag_font_family').val() || 'Roboto';
            let fontCss = "'Roboto', sans-serif";
            if (fontFamily === 'Arial') {
                fontCss = "Arial, sans-serif";
            } else if (fontFamily === 'Courier New') {
                fontCss = "'Courier New', Courier, monospace";
            } else if (fontFamily === 'Inter') {
                fontCss = "'Inter', sans-serif";
            } else if (fontFamily === 'monospace') {
                fontCss = "monospace";
            }
            receipt.css({ 'font-size': fontSize + 'px', 'font-family': fontCss });
            receipt.find('table').css({ 'font-size': (fontSize - 1) + 'px', 'font-family': fontCss });
            receipt.find('.receipt-header').css({ 'font-size': fontSize + 'px', 'font-family': fontCss });

            // 3. Logo toggle & size
            if (showLogo === '1') {
                $('#tag_logo_container').show();
                $('#tag_logo_img').css('width', logoWidth + '%');
            } else {
                $('#tag_logo_container').hide();
            }

            // 4. QR Code toggle & size
            if (showQr === '1') {
                $('#tag_qr_section').show();
                $('#tag_qr_wrapper').css('width', qrWidth + '%');
                $('#tag_qr_icon').css('font-size', Math.max(16, Math.round(qrWidth * 0.75)) + 'px');
            } else {
                $('#tag_qr_section').hide();
            }

            // 5. Footer note text
            if (footerNote.trim()) {
                $('#tag_footer_note_val').text(footerNote);
            } else {
                $('#tag_footer_note_val').html('Tempelkan Tag ini pada barang yang sudah jadi');
            }

            // 6. Section spacing simulation
            receipt.find('hr').css({ 'margin': spacing + 'px 0' });
            receipt.find('table').css({ 'margin-bottom': spacing + 'px', 'margin-top': spacing + 'px' });
            receipt.find('table td').css({ 'padding-top': Math.max(1, Math.round(spacing / 4)) + 'px', 'padding-bottom': Math.max(1, Math.round(spacing / 4)) + 'px' });
            $('#tag_logo_container').css({ 'margin-bottom': spacing + 'px' });
            $('#tag_qr_section').css({ 'margin-top': spacing + 'px' });
            $('#tag_footer_section').css({ 'margin-top': spacing + 'px' });
        }

        // Bind tag preview changes
        $('#template_tag_paper, #template_tag_font_size, #template_tag_font_family, #template_tag_show_logo, #template_tag_logo_width, #template_tag_show_qr, #template_tag_qr_width, #template_tag_footer_note, #template_tag_section_spacing').on('input change', function () {
            updateTagPreview();
        });

        // Form submit handler to set appropriate action
        $('#templateModal form').on('submit', function (e) {
            var active = $('#active_tab').val();
            if (active === 'invoice') {
                $(this).attr('action', '<?= base_url('pengaturan/invoice_template_update') ?>');
            } else {
                $(this).attr('action', '<?= base_url('pengaturan/tag_template_update') ?>');
            }
        });
    }); // end of document ready

    // Global handler for test print inside modal
    window.testPrintModal = function () {
        const form = $('#templateModal form');
        const originalAction = form.attr('action');
        const originalTarget = form.attr('target') || '';

        form.attr('action', '<?= base_url('pengaturan/test_print_preview') ?>');
        form.attr('target', '_blank');
        form.submit();

        // Restore form defaults
        form.attr('action', originalAction);
        form.attr('target', originalTarget);
    };
</script>