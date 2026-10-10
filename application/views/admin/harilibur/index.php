<style>
    /* Ultra-Minimalist Attendance & Holiday Design */
    :root {
        --tgm-primary: #2563eb;
        --tgm-primary-dark: #1d4ed8;
        --tgm-rose: #e11d48;
        --tgm-emerald: #059669;
        --tgm-slate-50: #f8fafc;
        --tgm-slate-100: #f1f5f9;
        --tgm-slate-200: #e2e8f0;
        --tgm-slate-700: #334155;
        --tgm-slate-900: #0f172a;
    }

    .text-primary-tgm {
        color: var(--tgm-primary) !important;
    }

    .bg-primary-tgm {
        background-color: var(--tgm-primary) !important;
    }

    .btn-primary-tgm {
        background-color: var(--tgm-primary);
        color: white;
        border: none;
    }

    .btn-primary-tgm:hover {
        background-color: var(--tgm-primary-dark);
        color: white;
    }

    .clean-card {
        background: white;
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
    }

    .stats-minimal {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-box {
        padding: 1.5rem;
        border-top: 3px solid var(--tgm-slate-900);
        transition: transform 0.2s, box-shadow 0.2s;
        background: white;
    }

    .stat-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
    }

    .stat-box.primary {
        border-color: var(--tgm-primary);
    }

    .stat-box.success {
        border-color: var(--tgm-emerald);
    }

    .stat-box.warning {
        border-color: #f59e0b;
    }

    .stat-box.danger {
        border-color: var(--tgm-rose);
    }

    .table-simple thead th {
        background: #fdfdfd;
        color: var(--tgm-slate-700);
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        padding: 15px 12px;
        border-top: none;
        border-bottom: 2px solid var(--tgm-slate-100);
    }

    .table-simple tbody td {
        padding: 15px 12px;
        vertical-align: middle;
        color: var(--tgm-slate-900);
        border-bottom: 1px solid var(--tgm-slate-100);
        font-size: 0.9rem;
    }

    .font-mono {
        font-family: 'JetBrains Mono', 'Fira Code', monospace !important;
    }

    .input-time-clean {
        border: none;
        background: transparent;
        font-family: 'JetBrains Mono', monospace;
        font-weight: 900;
        color: var(--tgm-slate-900);
        text-align: center;
        width: 80px;
    }

    .input-time-clean:focus {
        outline: none;
        color: var(--tgm-primary);
    }

    .bg-slate-100 {
        background-color: #f1f5f9 !important;
    }
</style>

<div class="app-content">
    <?php $this->load->view('layout_admin/header_modul'); ?>
<?php $this->load->view('admin/pengaturan/_settings_nav'); ?>

    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-end mb-4 no-print">
            <div>
                <h3 class="fw-900 mb-0 ls-1">JAM KERJA & <span class="text-primary-tgm">HARI LIBUR</span></h3>
                <p class="small text-muted mb-0">Manajemen jadwal operasional dan kalender libur nasional.</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" data-bs-toggle="modal" data-bs-target="#modalSOPAttendance"
                    class="btn btn-white border rounded-pill px-4 fw-900 small shadow-sm">
                    <i class="bi bi-journal-text me-2 text-primary-tgm"></i> PANDUAN SOP
                </button>
                <button type="button" onclick="$('#formJamKerja').submit()"
                    class="btn btn-primary-tgm rounded-pill px-4 fw-900 small shadow-sm">
                    <i class="bi bi-cloud-check-fill me-2"></i> SIMPAN PERUBAHAN
                </button>
            </div>
        </div>

        <!-- Statistics Grid -->
        <div class="stats-minimal no-print">
            <div class="clean-card stat-box success">
                <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.75rem; opacity: 0.8;">Total
                    Libur <?= date('Y') ?></div>
                <div class="h3 fw-900 font-mono text-emerald mb-0 mt-1"><?= $stats->total_this_year ?> <small
                        class="fw-bold text-muted ls-0" style="font-size: 0.8rem;">HARI</small></div>
            </div>
            <div class="clean-card stat-box warning">
                <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.75rem; opacity: 0.8;">Libur
                    Mendatang</div>
                <div class="h3 fw-900 font-mono text-warning mb-0 mt-1"><?= $stats->upcoming ?> <small
                        class="fw-bold text-muted ls-0" style="font-size: 0.8rem;">EVENT</small></div>
            </div>
            <div class="clean-card stat-box primary">
                <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.75rem; opacity: 0.8;">Total
                    Data Lintas Tahun</div>
                <div class="h3 fw-900 font-mono text-primary-tgm mb-0 mt-1"><?= $stats->total_all ?> <small
                        class="fw-bold text-muted ls-0" style="font-size: 0.8rem;">RECORD</small></div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Daily Work Hours -->
            <div class="col-lg-12">
                <div class="clean-card overflow-hidden">
                    <div class="p-4 bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="fw-900 text-dark mb-0 ls-1"><i
                                class="bi bi-clock-history me-2 text-primary-tgm"></i>PENGATURAN JAM KERJA HARIAN</h6>
                    </div>
                    <form id="formJamKerja" action="<?= base_url('pengaturan/harilibur_update_jam_kerja') ?>"
                        method="POST">
                        <div class="table-responsive">
                            <table class="table table-simple mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">HARI</th>
                                        <th class="text-center">JAM MASUK</th>
                                        <th class="text-center">JAM PULANG</th>
                                        <th class="text-center">MULAI LEMBUR</th>
                                        <th class="text-center pe-4">STATUS LEMBUR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($list_jam_kerja as $bg): ?>
                                        <tr
                                            class="<?= ($bg->hari == 'Minggu') ? 'bg-rose bg-opacity-10' : (($bg->hari == 'Sabtu') ? 'bg-warning bg-opacity-10' : '') ?>">
                                            <td class="ps-4 fw-900 text-dark align-middle">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-white border text-primary-tgm rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm"
                                                        style="width: 35px; height: 35px; font-size: 0.75rem;">
                                                        <?= substr($bg->hari, 0, 1) ?>
                                                    </div>
                                                    <?= strtoupper($bg->hari) ?>
                                                </div>
                                                <input type="hidden" name="id[]" value="<?= $bg->id ?>">
                                            </td>
                                            <td class="text-center align-middle">
                                                <input type="time" name="jam_masuk[]" class="input-time-clean"
                                                    value="<?= substr($bg->jam_masuk, 0, 5) ?>" required>
                                            </td>
                                            <td class="text-center align-middle">
                                                <input type="time" name="jam_pulang[]" class="input-time-clean"
                                                    value="<?= substr($bg->jam_pulang, 0, 5) ?>" required>
                                            </td>
                                            <td class="text-center align-middle">
                                                <input type="time" name="jam_mulai_lembur[]"
                                                    class="input-time-clean text-emerald"
                                                    value="<?= substr($bg->jam_mulai_lembur, 0, 5) ?>" required>
                                            </td>
                                            <td class="text-center pe-4 align-middle">
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        <?= $bg->boleh_lembur == '1' ? 'checked' : '' ?>
                                                        onchange="this.nextElementSibling.value = this.checked ? 1 : 0">
                                                    <input type="hidden" name="boleh_lembur[]"
                                                        value="<?= $bg->boleh_lembur ?>">
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Holidays List -->
            <div class="col-lg-12">
                <div class="clean-card overflow-hidden">
                    <div class="p-4 bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="fw-900 text-dark mb-0 ls-1"><i
                                class="bi bi-calendar-event me-2 text-primary-tgm"></i>DAFTAR HARI LIBUR NASIONAL</h6>
                        <div class="d-flex gap-2">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <span class="input-group-text bg-slate-100 border-0"><i class="bi bi-search"></i></span>
                                <input type="text" id="holidaySearch" class="form-control border-0 bg-slate-100"
                                    placeholder="Cari event libur...">
                            </div>
                            <button type="button" class="btn btn-white border rounded-pill px-4 fw-900 small shadow-sm"
                                onclick="syncNationalHolidays()">
                                <i class="bi bi-cloud-download me-2 text-primary-tgm"></i> SYNC NASIONAL
                            </button>
                            <button type="button" class="btn btn-primary-tgm rounded-pill px-4 fw-900 small shadow-sm"
                                data-bs-toggle="modal" data-bs-target="#addHariLiburModal">
                                <i class="bi bi-plus-circle me-2"></i> INPUT BARU
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-simple mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4" width="80">NO</th>
                                    <th width="250">TANGGAL</th>
                                    <th>KETERANGAN / EVENT</th>
                                    <th class="text-center pe-4" width="150">AKSI</th>
                                </tr>
                            </thead>
                            <tbody id="holidayTableBody">
                                <?php if (!empty($hari_libur)): ?>
                                    <?php $no = 1;
                                    foreach ($hari_libur as $row): ?>
                                        <tr>
                                            <td class="ps-4 font-mono text-muted small"><?= $no++ ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-rose bg-opacity-10 text-rose rounded p-2 me-3">
                                                        <i class="bi bi-calendar-check-fill"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-900 text-dark small">
                                                            <?= date('d F Y', strtotime($row->tanggal)) ?>
                                                        </div>
                                                        <div class="text-muted text-uppercase fw-bold"
                                                            style="font-size: 0.65rem; opacity: 0.6; letter-spacing: 0.5px;">
                                                            <?= date('l', strtotime($row->tanggal)) ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="fw-bold text-dark"><?= strtoupper(htmlspecialchars($row->keterangan)) ?>
                                            </td>
                                            <td class="text-center pe-4">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button type="button" class="btn btn-slate-100 btn-sm rounded-pill px-3"
                                                        onclick="editHariLibur('<?= $row->id ?>', '<?= $row->tanggal ?>', '<?= htmlspecialchars($row->keterangan, ENT_QUOTES) ?>')">
                                                        <i class="bi bi-pencil-square text-primary-tgm"></i>
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-slate-100 btn-sm rounded-pill px-3 btn-delete"
                                                        data-url="<?= base_url('pengaturan/harilibur_delete/' . $row->id) ?>">
                                                        <i class="bi bi-trash3-fill text-rose"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="bi bi-calendar-x fa-3x mb-3 d-block opacity-25"></i>
                                            <h6 class="fw-900">Belum ada data hari libur</h6>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addHariLiburModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="bg-primary-tgm p-4 text-white">
                <h5 class="modal-title fw-900 ls-1 mb-0 text-uppercase"><i class="bi bi-plus-circle-fill me-2"></i>
                    Input Hari Libur</h5>
            </div>
            <form action="<?= base_url('pengaturan/harilibur_store') ?>" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="fw-900 text-muted small text-uppercase mb-2 ls-1">Tanggal Libur</label>
                        <input type="date" class="form-control border-2 bg-slate-50 fw-bold px-3 py-3 rounded-3"
                            name="tanggal" required>
                    </div>
                    <div class="mb-2">
                        <label class="fw-900 text-muted small text-uppercase mb-2 ls-1">Keterangan / Nama Event</label>
                        <textarea class="form-control border-2 bg-slate-50 fw-bold px-3 py-3 rounded-3"
                            name="keterangan" rows="3" required placeholder="Contoh: Hari Raya Idul Fitri"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-white rounded-pill px-4 fw-900 small"
                        data-bs-dismiss="modal">BATAL</button>
                    <button type="submit"
                        class="btn btn-primary-tgm rounded-pill px-5 fw-900 shadow-primary-tgm py-2">SIMPAN
                        DATA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editHariLiburModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="bg-primary-tgm p-4 text-white">
                <h5 class="modal-title fw-900 ls-1 mb-0 text-uppercase"><i class="bi bi-pencil-square me-2"></i> Koreksi
                    Hari Libur</h5>
            </div>
            <form action="<?= base_url('pengaturan/harilibur_update') ?>" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="edit_id">
                    <input type="hidden" name="original_tanggal" id="edit_original_tanggal">

                    <div class="mb-4">
                        <label class="fw-900 text-muted small text-uppercase mb-2 ls-1">Tanggal Libur</label>
                        <input type="date" class="form-control border-2 bg-slate-50 fw-bold px-3 py-3 rounded-3"
                            name="tanggal" id="edit_tanggal" required>
                    </div>
                    <div class="mb-2">
                        <label class="fw-900 text-muted small text-uppercase mb-2 ls-1">Keterangan / Nama Event</label>
                        <textarea class="form-control border-2 bg-slate-50 fw-bold px-3 py-3 rounded-3"
                            name="keterangan" id="edit_keterangan" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-white rounded-pill px-4 fw-900 small"
                        data-bs-dismiss="modal">BATAL</button>
                    <button type="submit"
                        class="btn btn-primary-tgm rounded-pill px-5 fw-900 shadow-primary-tgm py-2">UPDATE
                        DATA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SOP Modal -->
<div class="modal fade" id="modalSOPAttendance" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="bg-primary-tgm p-4 text-white position-relative">
                <div class="position-relative z-index-1">
                    <h5 class="modal-title fw-900 ls-1 mb-0"><i class="bi bi-info-circle-fill me-2"></i>PANDUAN SOP
                        ABSENSI & LIBUR</h5>
                    <p class="small mb-0 opacity-75 mt-1">Standar Pengaturan Waktu Operasional - TGM ERP</p>
                </div>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="bg-slate-50 p-4 rounded-4 h-100 border border-primary-tgm border-opacity-10">
                            <h6 class="fw-900 text-primary-tgm mb-3"><i class="bi bi-clock me-2"></i>KONFIGURASI WAKTU
                            </h6>
                            <ul class="small text-muted mb-0 ps-3 lh-lg">
                                <li><b>Jam Masuk:</b> Batas awal mulai bekerja.</li>
                                <li><b>Jam Pulang:</b> Batas akhir durasi normal.</li>
                                <li><b>Mulai Lembur:</b> Pemicu kalkulasi overtime di atas gaji pokok.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-slate-50 p-4 rounded-4 h-100 border border-rose border-opacity-10">
                            <h6 class="fw-900 text-rose mb-3"><i class="bi bi-calendar-x me-2"></i>PENGELOLAAN LIBUR
                            </h6>
                            <ul class="small text-muted mb-0 ps-3 lh-lg">
                                <li>Input semua libur nasional agar sistem tidak mencatat <code>ALPHA</code> secara
                                    otomatis.</li>
                                <li>Gunakan fitur <b>SYNC NASIONAL</b> untuk menarik data kalender resmi secara otomatis
                                    setiap tahunnya.</li>
                                <li>Libur yang telah diinput akan membebaskan potongan gaji harian staff.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-primary-tgm w-100 rounded-pill fw-900 small py-3"
                    data-bs-dismiss="modal">SAYA MENGERTI & SIAP MENGATUR</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Client-side search
        $("#holidaySearch").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#holidayTableBody tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Delete confirmation
        $('.btn-delete').click(function () {
            const url = $(this).data('url');
            Swal.fire({
                title: 'HAPUS HARI LIBUR?',
                text: "Data yang dihapus akan mempengaruhi perhitungan absensi & gaji!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#e11d48',
                confirmButtonText: 'YA, HAPUS',
                cancelButtonText: 'BATAL',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });

    function syncNationalHolidays() {
        Swal.fire({
            title: 'SINKRONISASI KALENDER?',
            text: "Sistem akan mengambil data libur nasional secara otomatis untuk tahun <?= date('Y') ?>.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#e11d48',
            confirmButtonText: 'YA, SYNC SEKARANG',
            cancelButtonText: 'BATAL',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Sinkronisasi...',
                    text: 'Mohon tunggu sebentar...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                window.location.href = '<?= base_url('pengaturan/harilibur_sync_nasional') ?>';
            }
        });
    }

    function editHariLibur(id, tanggal, keterangan) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_original_tanggal').value = tanggal;
        document.getElementById('edit_tanggal').value = tanggal;
        document.getElementById('edit_keterangan').value = keterangan;

        var modal = new bootstrap.Modal(document.getElementById('editHariLiburModal'));
        modal.show();
    }
</script>