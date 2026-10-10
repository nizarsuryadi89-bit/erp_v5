<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">

<div class="app-content">
    <div class="container-fluid">
        <!-- Header -->
        <?php $this->load->view('layout_admin/header_modul'); ?>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary"><i class="fa fa-keyboard me-2"></i>Input Absensi Manual</h5>
                <a href="<?= base_url('absensi') ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <div class="alert alert-info border-0 shadow-sm mb-4">
                    <i class="fa fa-info-circle me-2"></i> Fitur ini digunakan untuk menginput data absensi secara
                    manual (rapel) untuk tanggal-tanggal sebelumnya.
                </div>

                <div class="row g-3 mb-4 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Pilih Tanggal</label>
                        <input type="date" id="tanggal" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-8">
                        <button type="button" class="btn btn-primary" onclick="loadEmployees()">
                            <i class="fa fa-sync me-1"></i> Muat Data Karyawan
                        </button>
                        <button type="button" class="btn btn-success ms-2" onclick="setAllHadir()">
                            <i class="fa fa-check-double me-1"></i> Set Semua Hadir (08:00 - 18:00)
                        </button>
                    </div>
                </div>

                <form id="bulkForm">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="20%">Karyawan</th>
                                    <th width="15%">Status</th>
                                    <th width="15%">Jam Masuk</th>
                                    <th width="15%">Jam Pulang</th>
                                    <th width="30%">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody">
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        Silakan pilih tanggal dan klik "Muat Data Karyawan"
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="fa fa-save me-2"></i> Simpan Absensi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const API_URL_GET = '<?= base_url('absensi/get_daily_attendance_json') ?>';
    const API_URL_SAVE = '<?= base_url('absensi/save_manual_batch') ?>';

    $(document).ready(function () {
        // Optional: Auto load on date change
        // $('#tanggal').change(loadEmployees);
        loadEmployees(); // Load today by default
    });

    function loadEmployees() {
        const tanggal = $('#tanggal').val();
        if (!tanggal) {
            alert('Pilih tanggal terlebih dahulu');
            return;
        }

        $('#attendanceTableBody').html('<tr><td colspan="6" class="text-center py-5"><div class="spinner-border text-primary"></div><div class="mt-2">Memuat data...</div></td></tr>');

        $.ajax({
            url: API_URL_GET,
            type: 'GET',
            data: { tanggal: tanggal },
            dataType: 'json',
            success: function (res) {
                renderTable(res);
            },
            error: function () {
                $('#attendanceTableBody').html('<tr><td colspan="6" class="text-center text-danger py-4">Gagal memuat data. Periksa koneksi internet.</td></tr>');
            }
        });
    }

    function renderTable(data) {
        if (data.length === 0) {
            $('#attendanceTableBody').html('<tr><td colspan="6" class="text-center py-4">Tidak ada data karyawan aktif.</td></tr>');
            return;
        }

        let html = '';
        data.forEach((row, index) => {
            const status = row.status || 'Alpha'; // Default to Alpha if no record
            const jam_masuk = row.jam_masuk ? row.jam_masuk.substring(0, 5) : '';
            const jam_pulang = row.jam_pulang ? row.jam_pulang.substring(0, 5) : '';
            const keterangan = row.keterangan || '';
            const id_absensi = row.id || ''; // Existing ID if update

            // Select Options
            const statusOptions = ['Hadir', 'Izin', 'Sakit', 'Cuti', 'Cuti Keluarga', 'Cuti Sakit', 'Cuti Melahirkan', 'Cuti Menikah', 'Alpha'];
            let selectOptionsHtml = '';

            const optionLabels = {
                'Hadir': 'Hadir',
                'Izin': 'Izin',
                'Sakit': 'Sakit',
                'Cuti': 'Cuti Tahunan',
                'Cuti Keluarga': 'Cuti Keluarga',
                'Cuti Sakit': 'Cuti Sakit',
                'Cuti Melahirkan': 'Cuti Melahirkan',
                'Cuti Menikah': 'Cuti Menikah',
                'Alpha': 'Alpha'
            };

            statusOptions.forEach(opt => {
                const selected = status === opt ? 'selected' : '';
                selectOptionsHtml += `<option value="${opt}" ${selected}>${optionLabels[opt]}</option>`;
            });

            // Set Alpha default if null and user hasn't intereacted? No, let's keep it null-ish or generic.
            // Actually, for "Manual Input", it's better to default to empty or let them choose.
            // But user asked for backfill. Defaulting to 'Hadir' might be dangerous if they just click save.
            // Let's default to whatever is in DB, or 'Alpha' if new? Or just blank?
            // "Alpha" is safest "no show" default.

            html += `
                <tr>
                    <td class="text-center">${index + 1}
                        <input type="hidden" name="attendance[${index}][karyawan_id]" value="${row.karyawan_id}">
                        <input type="hidden" name="attendance[${index}][id]" value="${id_absensi}">
                    </td>
                    <td>
                        <div class="fw-bold">${row.nama_lengkap}</div>
                        <div class="small text-muted">NIK: ${row.nik} | Div: ${row.divisi_nama}</div>
                    </td>
                    <td>
                        <select class="form-select form-select-sm status-select" name="attendance[${index}][status]" onchange="statusChange(this)">
                            ${selectOptionsHtml}
                        </select>
                    </td>
                    <td>
                        <input type="time" class="form-control form-control-sm time-in" name="attendance[${index}][jam_masuk]" value="${jam_masuk}">
                    </td>
                    <td>
                        <input type="time" class="form-control form-control-sm time-out" name="attendance[${index}][jam_pulang]" value="${jam_pulang}">
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm" name="attendance[${index}][keterangan]" value="${keterangan}" placeholder="Keterangan...">
                    </td>
                </tr>
            `;
        });
        $('#attendanceTableBody').html(html);
    }

    function statusChange(select) {
        const row = $(select).closest('tr');
        const val = $(select).val();

        if (val === 'Hadir') {
            // Optional: Auto set time if empty?
            if (row.find('.time-in').val() === '') row.find('.time-in').val('08:00');
            if (row.find('.time-out').val() === '') row.find('.time-out').val('18:00');
        } else {
            // Clear times for non-present statuses usually, but maybe keep them if Izin Partial?
            // For simplicity, let's leave them.
            row.find('.time-in').val('');
            row.find('.time-out').val('');
        }
    }

    function setAllHadir() {
        $('.status-select').val('Hadir');
        $('.time-in').val('08:00');
        $('.time-out').val('18:00');
    }

    $('#bulkForm').on('submit', function (e) {
        e.preventDefault();

        // Confirmation
        if (!confirm('Apakah Anda yakin ingin menyimpan data absensi ini? data yang sudah ada di tanggal ini akan diperbarui.')) return;

        const formData = $(this).serializeArray();
        formData.push({ name: 'tanggal', value: $('#tanggal').val() });

        const btn = $(this).find('button[type="submit"]');
        const originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i> Menyimpan...');

        $.ajax({
            url: API_URL_SAVE,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (res) {
                if (res.status) {
                    alert('Data berhasil disimpan!');
                    loadEmployees(); // Refresh
                } else {
                    alert('Gagal: ' + res.message);
                }
            },
            error: function () {
                alert('Terjadi kesalahan server.');
            }, complete: function () {
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
</script>