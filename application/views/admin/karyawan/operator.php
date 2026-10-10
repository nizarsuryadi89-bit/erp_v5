<div class="app-content mt-2">
    <div class="container-fluid">
        <!-- HEADER SECTION REPLACED WITH STANDARD MODULE HEADER -->
        <?php $this->load->view('layout_admin/header_modul'); ?>

        <!-- Flash Messages -->
        <div id="alert-container">
            <?= $this->session->flashdata('message'); ?>
        </div>

        <!-- STATISTICS SECTION -->
        <div class="row g-3 mb-4 animate-up">
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--primary-gradient)">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <div>
                        <div class="stat-v2-label">Total Operator</div>
                        <div class="stat-v2-value"><?= count($tampilData) ?></div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--success-gradient)">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <div class="stat-v2-label">Terkoneksi Email</div>
                        <div class="stat-v2-value">
                            <?= count(array_filter($tampilData ?? [], function ($item) {
                                return !empty($item->email);
                            })) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--warning-gradient)">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div>
                        <div class="stat-v2-label">Terkoneksi Telp</div>
                        <div class="stat-v2-value">
                            <?= count(array_filter($tampilData ?? [], function ($item) {
                                return !empty($item->telepon);
                            })) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--info-gradient)">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <div class="stat-v2-label">Status Aktif</div>
                        <div class="stat-v2-value">
                            <?= count(array_filter($tampilData ?? [], function ($item) {
                                return !empty($item->tanggal_masuk);
                            })) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN TABLE CARD -->
        <div class="report-section-card animate-up" style="animation-delay: 0.1s">
            <div class="section-header">
                <h5><i class="fas fa-users-cog me-2"></i> Daftar Karyawan Operator</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-light btn-premium rounded-pill px-3 shadow-sm btn-sm fw-bold"
                        onclick="exportToExcel()">
                        <i class="fas fa-file-excel text-success me-1"></i> Excel
                    </button>
                    <button class="btn btn-light btn-premium rounded-pill px-3 shadow-sm btn-sm fw-bold"
                        onclick="printTable()">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                    <a href="<?= base_url('karyawan') ?>"
                        class="btn btn-light btn-premium rounded-pill px-3 shadow-sm btn-sm fw-bold">
                        <i class="fas fa-users me-1"></i> Semua Karyawan
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table premium-table table-hover mb-0" id="datatable-operator">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Identitas</th>
                            <th>Jabatan & Divisi</th>
                            <th>Kontak</th>
                            <th>Tanggal Masuk</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tampilData)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted p-5 fw-bold">Tidak ada data karyawan divisi
                                    operator.</td>
                            </tr>
                        <?php else:
                            $no = 1;
                            foreach ($tampilData as $row):
                                ?>
                                <tr>
                                    <td class="text-center fw-bold text-muted"><?= $no++ ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light p-2 rounded-3 shadow-sm border me-3 d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px;">
                                                <i class="fas fa-user small text-dark opacity-50"></i>
                                            </div>
                                            <div>
                                                <div class="fw-900 text-dark"><?= htmlspecialchars($row->nama_lengkap) ?></div>
                                                <div class="text-muted small fw-bold text-uppercase ls-1"
                                                    style="font-size: 0.65rem;">
                                                    NIK: <?= htmlspecialchars($row->nik) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <span
                                                class="badge bg-primary-subtle text-primary border border-opacity-25 rounded-pill px-3 py-1 fw-bold align-self-start"
                                                style="font-size: 0.65rem;">
                                                <?= strtoupper(htmlspecialchars($row->nama_jabatan ?? $row->jabatan_id)) ?>
                                            </span>
                                            <span
                                                class="badge bg-light text-dark border rounded-pill px-3 py-1 fw-bold align-self-start"
                                                style="font-size: 0.65rem;">
                                                <i class="fas fa-layer-group me-1 text-muted"></i>
                                                <?= strtoupper(htmlspecialchars($row->nama_divisi ?? $row->divisi_id)) ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <?php if (!empty($row->telepon)): ?>
                                                <div class="fw-bold text-dark small"><i
                                                        class="fas fa-phone-alt text-success me-2"></i>
                                                    <?= htmlspecialchars($row->telepon) ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($row->email)): ?>
                                                <div class="fw-bold text-muted small"><i class="fas fa-envelope text-info me-2"></i>
                                                    <?= htmlspecialchars($row->email) ?></div>
                                            <?php endif; ?>
                                            <?php if (empty($row->telepon) && empty($row->email)): ?>
                                                <span class="text-muted small fst-italic">Tidak ada kontak</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark small">
                                            <i class="fas fa-calendar-check text-warning me-2"></i>
                                            <?= date('d M Y', strtotime($row->tanggal_masuk)) ?>
                                        </div>
                                        <div class="text-muted small" style="font-size: 0.7rem;">
                                            Bergabung
                                            <?= floor((time() - strtotime($row->tanggal_masuk)) / (60 * 60 * 24 * 365)) ?> tahun
                                            lalu
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        if ($.fn.DataTable) {
            $('#datatable-operator').DataTable({
                "pageLength": 10,
                "language": { "sSearch": "Cari Operator:", "oPaginate": { "sNext": "<i class='fas fa-chevron-right'></i>", "sPrevious": "<i class='fas fa-chevron-left'></i>" } },
                "dom": "<'row px-4 py-3'<'col-md-6'l><'col-md-6 d-flex justify-content-end text-end'f>>t<'row px-4 py-3'<'col-md-6'i><'col-md-6 d-flex justify-content-end text-end'p>>"
            });
        }

        // Auto-hide alert after 5 seconds
        setTimeout(function () {
            $('#alert-container .alert').fadeOut('slow');
        }, 5000);
    });

    function exportToExcel() {
        // Simple CSV export logic
        let csv = 'No,Nama Karyawan,NIK,Jabatan,Divisi,Telepon,Email,Tanggal Masuk\n';

        // We need to iterate over the actual data rows, possibly from DataTable or DOM
        // For simplicity, let's grab from DOM visible rows or all rows if not paginated securely
        // Better approaches use server-side or data array. Here we strip from DOM.

        const rows = document.querySelectorAll('#datatable-operator tbody tr');
        rows.forEach((row, index) => {
            // Skip empty message row
            if (row.cells.length < 2) return;

            const no = index + 1;

            // Name & NIK are in cell 1
            const nameDiv = row.cells[1].querySelector('.fw-900');
            const nikDiv = row.cells[1].querySelector('.text-uppercase');
            const name = nameDiv ? nameDiv.innerText.trim() : '';
            const nik = nikDiv ? nikDiv.innerText.replace('NIK:', '').trim() : '';

            // Jabatan & Divisi in cell 2
            const pills = row.cells[2].querySelectorAll('.badge');
            const jabatan = pills[0] ? pills[0].innerText.trim() : '';
            const divisi = pills[1] ? pills[1].innerText.trim() : '';

            // Contacts in cell 3
            const phoneEl = row.cells[3].querySelector('.fa-phone-alt');
            const emailEl = row.cells[3].querySelector('.fa-envelope');
            const phone = phoneEl ? phoneEl.parentElement.innerText.trim() : '';
            const email = emailEl ? emailEl.parentElement.innerText.trim() : '';

            // Date in cell 4
            const dateEl = row.cells[4].querySelector('.fw-bold');
            const tgl = dateEl ? dateEl.innerText.trim() : '';

            const rowData = [no, name, nik, jabatan, divisi, phone, email, tgl];
            csv += rowData.map(v => `"${v}"`).join(',') + '\n';
        });

        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'data_operator_' + new Date().getTime() + '.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }

    function printTable() {
        window.print();
    }
</script>