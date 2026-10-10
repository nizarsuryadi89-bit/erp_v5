<!-- Scripts Section -->
<script>
    $(document).ready(function () {
        // Initialize Select2
        $('.select2-filter').select2({
            theme: 'bootstrap-5',
            width: '100%',
            allowClear: true
        });

        $('.select2-modal').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $('#karyawanModal')
        });

        // --- Currency Masking ---
        function formatRupiah(angka) {
            if (!angka) return '0';
            let number_string = angka.toString().replace(/[^,\d]/g, ''),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return rupiah;
        }

        $(document).on('keyup', '.currency-input', function () {
            $(this).val(formatRupiah($(this).val()));
        });

        let searchTimeout;

        // Auto filter on input change
        $('#search').on('keyup input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(loadData, 500);
        });

        $('#gender_filter, #status_filter, #jabatan_filter, #divisi_filter, #perusahaan_filter').on('change', function () {
            loadData();
        });

        // Reset Filter
        $('#btnResetFilter').on('click', function () {
            $('#search').val('');
            $('#gender_filter').val('').trigger('change');
            $('#status_filter').val('').trigger('change');
            $('#jabatan_filter').val('').trigger('change');
            $('#divisi_filter').val('').trigger('change');
            $('#perusahaan_filter').val('').trigger('change');
            loadData();
        });

        $('#btnExportExcel').on('click', function () {
            const params = new URLSearchParams({
                search: $('#search').val() || '',
                gender: $('#gender_filter').val() || '',
                status: $('#status_filter').val() || '',
                jabatan: $('#jabatan_filter').val() || '',
                divisi: $('#divisi_filter').val() || '',
                perusahaan: $('#perusahaan_filter').val() || ''
            }).toString();

            window.open('<?= base_url("karyawan/export_karyawan_excel?") ?>' + params, '_blank');
        });

        $('#btnExportPdf').on('click', function () {
            const params = new URLSearchParams({
                search: $('#search').val() || '',
                gender: $('#gender_filter').val() || '',
                status: $('#status_filter').val() || '',
                jabatan: $('#jabatan_filter').val() || '',
                divisi: $('#divisi_filter').val() || '',
                perusahaan: $('#perusahaan_filter').val() || ''
            }).toString();

            window.open('<?= base_url("karyawan/export_karyawan_pdf?") ?>' + params, '_blank');
        });

        $('#payroll_jenis_gaji').on('change', function () {
            if ($(this).val() === 'Harian') {
                $('#label_gaji_pokok').text('Nominal Gaji Harian');
            } else {
                $('#label_gaji_pokok').text('Gaji Pokok Bulanan');
            }
        });

        $('#searchForm').on('submit', function (e) {
            e.preventDefault();
            loadData();
        });

        // Initial Filters from Controller
        const init = $('#initial-filters').data();
        if (init.gender) $('#gender_filter').val(init.gender).trigger('change.select2');
        if (init.status) $('#status_filter').val(init.status).trigger('change.select2');
        if (init.jabatan) $('#jabatan_filter').val(init.jabatan).trigger('change.select2');
        if (init.divisi) $('#divisi_filter').val(init.divisi).trigger('change.select2');
        if (init.perusahaan) $('#perusahaan_filter').val(init.perusahaan).trigger('change.select2');

        // Call initial load
        loadData();

        // Function to load and render table data
        function loadData() {
            const params = {
                search: $('#search').val(),
                gender: $('#gender_filter').val(),
                status: $('#status_filter').val(),
                jabatan: $('#jabatan_filter').val(),
                divisi: $('#divisi_filter').val(),
                perusahaan: $('#perusahaan_filter').val()
            };

            const tbody = $('#tableBody');

            // Show loading overlay in table
            if (tbody.find('.loading-row').length === 0) {
                tbody.prepend(`
                    <tr class="loading-row">
                        <td colspan="8" class="text-center py-3 bg-light bg-opacity-50">
                            <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                            <small class="text-primary fw-bold">Sedang menyaring...</small>
                        </td>
                    </tr>
                `);
            }
            tbody.addClass('opacity-75');

            $.ajax({
                url: '<?= base_url("karyawan/search_ajax") ?>',
                type: 'POST',
                data: params,
                dataType: 'json',
                success: function (res) {
                    tbody.removeClass('opacity-75');
                    if (res.success) {
                        renderTable(res.data);

                        // Update result count text
                        const total = res.stats ? res.stats.total : (res.total || 0);
                        $('#totalCount').text(`Menampilkan ${total} karyawan`);

                        // Update stats cards if available
                        if (res.stats) {
                            const s = res.stats;
                            $('.card-title:contains("Total Karyawan")').siblings('h3').text(s.total || 0);
                            $('.card-title:contains("Karyawan Laki-laki")').siblings('h3').text(s.male || 0);
                            $('.card-title:contains("Karyawan Perempuan")').siblings('h3').text(s.female || 0);
                            $('.card-title:contains("Karyawan Aktif")').siblings('h3').text(s.active || 0);
                        }
                    } else {
                        Swal.fire('Error', res.message || 'Gagal memuat data', 'error');
                    }
                },
                error: function () {
                    tbody.removeClass('opacity-50');
                    Swal.fire('Error', 'Terjadi kesalahan koneksi server', 'error');
                }
            });
        }

        function renderTable(data) {
            let html = '';
            if (data.length === 0) {
                html = `
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="mb-3"><i class="fa fa-user-times fa-3x opacity-25"></i></div>
                                <h5>Tidak ada data yang cocok</h5>
                                <p class="small">Coba ubah kata kunci atau filter Anda.</p>
                            </td>
                        </tr>`;
            } else {
                data.forEach((row, index) => {
                    const fotoSrc = row.foto
                        ? '<?= base_url("assets/images/karyawan/") ?>' + row.foto
                        : '<?= base_url("assets/images/karyawan/default.png") ?>';

                    // Parse Tenur
                    const start = new Date(row.tanggal_masuk);
                    const now = new Date();
                    let years = now.getFullYear() - start.getFullYear();
                    let months = now.getMonth() - start.getMonth();
                    if (months < 0) { years--; months += 12; }
                    const totalMonths = (years * 12) + months;

                    let warningHtml = '';
                    if (row.pengajuan_kontrak_status === 'Pending') {
                        warningHtml = `<span class="badge rounded-pill bg-primary text-white small" style="font-size: 0.75rem;"><i class="bi bi-hourglass-split me-1"></i> PENGAJUAN KONTRAK: ${row.pengajuan_kontrak_durasi} Bln</span>`;
                    } else if (row.pengajuan_kontrak_status === 'Approved') {
                        warningHtml = `<span class="badge rounded-pill bg-success text-white small" style="font-size: 0.75rem;"><i class="bi bi-patch-check-fill me-1"></i> KONTRAK DIPERPANJANG</span>`;
                    } else if (row.pengajuan_kontrak_status === 'Rejected') {
                        warningHtml = `<span class="badge rounded-pill bg-danger text-white small" style="font-size: 0.75rem;"><i class="bi bi-x-circle-fill me-1"></i> PERPANJANGAN DITOLAK</span>`;
                    } else if (totalMonths >= 22 && totalMonths <= 24) {
                        warningHtml = `<span class="badge rounded-pill bg-warning text-dark small" style="font-size: 0.75rem;"><i class="bi bi-clock-fill me-1"></i> CEK KONTRAK</span>`;
                    }

                    html += `
                            <tr>
                                <td class="ps-3 font-mono text-muted small">${index + 1}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="position-relative me-3">
                                            <img src="${fotoSrc}" onerror="this.onerror=null;this.src='<?= base_url("assets/images/karyawan/default.png") ?>'" class="avatar-clean shadow-sm">
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark mb-0 small">${row.nama_lengkap.toUpperCase()}</div>
                                            <small class="text-muted font-mono" style="font-size: 0.75rem;">NIK: ${row.nik}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark small">${years} Thn, ${months} Bln</div>
                                    ${warningHtml}
                                </td>
                                <td>
                                    <div class="fw-bold text-primary-tgm mb-0 small">${row.nama_jabatan}</div>
                                    <div class="small text-muted"><i class="bi bi-geo-alt-fill me-1"></i>${row.nama_perusahaan}</div>
                                </td>
                                <td>
                                    <div class="small fw-bold text-dark"><i class="bi bi-whatsapp text-success me-1"></i>${row.telepon}</div>
                                    <div class="small text-muted text-truncate" style="max-width: 140px;">${row.email || '-'}</div>
                                </td>
                                <td>
                                    <span class="badge px-3 py-2 ${row.status_karyawan === 'Tetap' ? 'bg-success' : (row.status_karyawan === 'Kontrak' ? 'bg-warning text-dark' : (row.status_karyawan === 'Magang' ? 'bg-info text-dark' : 'bg-secondary'))}">
                                        ${row.status_karyawan || 'N/A'}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-slate-50 border btn-sm rounded-pill px-3 py-1 small fw-bold" type="button" data-bs-toggle="dropdown">
                                            AKSI <i class="bi bi-chevron-down ms-1"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3 small fw-bold">
                                            ${row.pengajuan_kontrak_status === 'Pending' ? 
                                            '<li><a class="dropdown-item py-2 text-primary bg-primary bg-opacity-10" href="javascript:void(0)" onclick="prosesPerpanjanganKontrak(' + row.karyawan_id + ', \'' + row.nama_lengkap.replace(/'/g, "\\'") + '\', \'' + row.akhir_kontrak + '\')"><i class="bi bi-file-earmark-check-fill me-2 text-primary"></i> PROSES KONTRAK</a></li><li><hr class="dropdown-divider"></li>' 
                                            : ''}
                                            <li><a class="dropdown-item py-2" href="javascript:void(0)" onclick="viewKaryawan(${row.karyawan_id})"><i class="bi bi-person-circle me-2 text-primary-tgm"></i> DETAIL PROFIL</a></li>
                                            <li><a class="dropdown-item py-2" href="javascript:void(0)" onclick="editKaryawan(${row.karyawan_id})"><i class="bi bi-pencil-square me-2 text-warning"></i> EDIT DATA</a></li>
                                            <li><a class="dropdown-item py-2" href="javascript:void(0)" onclick="editPayroll(${row.karyawan_id}, '${row.nama_lengkap.replace(/'/g, "\\'")}', ${row.gaji_pokok || 0}, ${row.nominal_lembur || 0}, ${row.tunjangan_jabatan || 0}, '${fotoSrc}', '${row.jenis_gaji}', ${row.transport_aktif})"><i class="bi bi-cash-stack me-2 text-success"></i> GAJI & LEMBUR</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item py-2 text-danger" href="javascript:void(0)" onclick="deleteKaryawan(${row.karyawan_id})"><i class="bi bi-trash3-fill me-2"></i> HAPUS AKUN</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>`;
                });
            }
            $('#tableBody').html(html).removeClass('opacity-75');
        }

        // --- STATS VIEW AJAX ---
        $(document).on('click', '.btn-view-emp-stats', function () {
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            const modal = $('#modalBranchEmployees');
            const tbody = $('#branchTable tbody');

            $('#branchTitle').html(`<i class="bi bi-building me-2 text-primary-tgm"></i>STAFF: ${nama}`);
            tbody.html('<tr><td colspan="3" class="text-center py-4"><i class="fa fa-spinner fa-spin me-2"></i>Memuat staff...</td></tr>');
            modal.modal('show');

            $.ajax({
                url: '<?= base_url("karyawan/get_karyawan_by_perusahaan_ajax") ?>',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function (res) {
                    let html = '';
                    if (res.length > 0) {
                        res.forEach((item, i) => {
                            html += `
                            <tr>
                                <td class="ps-4 fw-bold text-dark small">${item.nama_lengkap.toUpperCase()}</td>
                                <td class="small fw-bold text-primary-tgm">${item.nama_jabatan || '<span class="text-muted small italic">N/A</span>'}</td>
                                <td class="text-center pe-4"><span class="badge-soft small ${item.jenis_kelamin == 'L' ? 'bg-primary-tgm bg-opacity-10 text-primary-tgm' : 'bg-rose bg-opacity-10 text-rose'}">${item.jenis_kelamin}</span></td>
                            </tr>`;
                        });
                    } else {
                        html = '<tr><td colspan="3" class="text-center py-4 text-muted small fw-bold">Belum ada staff terdaftar.</td></tr>';
                    }
                    tbody.html(html);
                }
            });
        });

        // Photo Preview on File Input Change
        $('#form_foto').on('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $('#photoPreview').attr('src', e.target.result);
                    $('#photoPreviewWrapper').removeClass('d-none');
                }
                reader.readAsDataURL(file);
            }
        });

        // Form Submit Actions
        $('#karyawanForm').on('submit', function (e) {
            e.preventDefault();
            const btn = $('#btnSubmitKaryawan');
            const oldText = btn.html();
            const isEdit = $('#form_karyawan_id').val() !== '';
            const url = isEdit ? '<?= base_url("karyawan/update_karyawan_ajax") ?>' : '<?= base_url("karyawan/simpan_karyawan_ajax") ?>';

            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>Menyimpan...');

            const formData = new FormData(this);

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (res) {
                    btn.prop('disabled', false).html(oldText);
                    // Handle both 'status' and 'success' property names from different API versions
                    const status = res.status !== undefined ? res.status : res.success;

                    if (status) {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false });
                        $('#karyawanModal').modal('hide');
                        loadData(); // This will also update stats via the success callback
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                        // Show tab if validation failed there
                        if (res.tab) {
                            $(`#${res.tab}-tab`).tab('show');
                        }
                    }
                    // Update stats from the server response
                    if (res.stats) {
                        const s = res.stats;
                        $('.card-title:contains("Total Karyawan")').siblings('h3').text(s.total || 0);
                        $('.card-title:contains("Karyawan Laki-laki")').siblings('h3').text(s.male || 0);
                        $('.card-title:contains("Karyawan Perempuan")').siblings('h3').text(s.female || 0);
                        $('.card-title:contains("Karyawan Aktif")').siblings('h3').text(s.active || 0);
                    }
                },
                error: function () {
                    btn.prop('disabled', false).html(oldText);
                    Swal.fire('Error', 'Gagal menghubungi server', 'error');
                }
            });
        });

        $('#payrollForm').on('submit', function (e) {
            e.preventDefault();
            const btn = $('#btnSubmitPayroll');
            const oldText = btn.html();

            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>Memproses...');

            // Clean currency values before sending
            const formData = $(this).serializeArray();
            formData.forEach(item => {
                if (item.name === 'gaji_pokok' || item.name === 'nominal_lembur' || item.name === 'tunjangan_jabatan') {
                    item.value = item.value.replace(/\./g, ''); // Remove dots
                }
            });

            $.ajax({
                url: '<?= base_url("karyawan/update_payroll_settings") ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (res) {
                    btn.prop('disabled', false).html(oldText);
                    if (res.success) {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false });
                        $('#payrollModal').modal('hide');
                        loadData(); // Just refresh table dynamically
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: function () {
                    btn.prop('disabled', false).html(oldText);
                }
            });
        });
    });

    // --- GLOBAL HELPER FUNCTIONS ---

    function viewKaryawan(id) {
        const body = $('#viewKaryawanBody');
        body.html('<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');
        $('#viewKaryawanModal').modal('show');

        $.ajax({
            url: '<?= base_url("karyawan/get_karyawan_json/") ?>' + id,
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (!res.success) {
                    body.html('<div class="p-4 text-center"><i class="fa fa-warning-circle text-danger fa-2x"></i><p>' + res.message + '</p></div>');
                    return;
                }
                const d = res.data;
                const foto = d.foto ? '<?= base_url("assets/images/karyawan/") ?>' + d.foto : '<?= base_url("assets/images/karyawan/default.png") ?>';

                let docsHtml = '';
                if (d.documents && d.documents.length > 0) {
                    d.documents.forEach(doc => {
                        docsHtml += `
                        <div class="col-6 mb-2">
                            <a href="<?= base_url("assets/documents/karyawan/") ?>${doc.file_path}" target="_blank" class="d-flex align-items-center p-2 bg-light rounded-3 text-decoration-none border-hover">
                                <i class="bi bi-file-earmark-pdf text-danger me-2 shadow-sm"></i>
                                <div class="text-dark small fw-bold text-truncate">${doc.jenis_dokumen.toUpperCase()}</div>
                            </a>
                        </div>`;
                    });
                } else {
                    docsHtml = '<div class="col-12 py-3 text-center text-muted small"><i class="bi bi-info-circle me-1"></i>Belum ada lampiran dokumen</div>';
                }

                body.html(`
                    <div class="bg-primary-tgm p-5 text-center text-white position-relative overflow-hidden">
                        <div class="position-relative z-index-1">
                            <img src="${foto}" class="rounded-circle border border-4 border-white shadow-lg mb-3" width="110" height="110" style="object-fit: cover; background: white;" onerror="this.onerror=null;this.src='<?= base_url("assets/images/karyawan/default.png") ?>'">
                            <h4 class="fw-900 mb-0 ls-1">${d.nama_lengkap.toUpperCase()}</h4>
                            <p class="mb-0 opacity-75 fw-bold small ls-1 mt-1">${d.nama_jabatan} — ${d.divisi_nama}</p>
                            <div class="badge bg-white text-primary-tgm mt-3 px-3 py-2 fw-900 rounded-pill shadow-sm small ls-1">${d.status_karyawan}</div>
                        </div>
                        <div class="position-absolute end-0 top-0 p-4 opacity-10"><i class="bi bi-person-badge fa-10x"></i></div>
                    </div>
                    <div class="p-4 bg-white">
                        <ul class="nav nav-tabs nav-fill border-0 bg-slate-50 rounded-pill p-1 mb-4">
                            <li class="nav-item"><button class="nav-link active rounded-pill fw-900 small py-2 border-0" data-bs-toggle="tab" data-bs-target="#v-info">INFORMASI PERSONAL</button></li>
                            <li class="nav-item"><button class="nav-link rounded-pill fw-900 small py-2 border-0" data-bs-toggle="tab" data-bs-target="#v-doc">ARSIP DOKUMEN</button></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="v-info">
                                <div class="row g-4">
                                    <div class="col-md-6 border-start border-3 border-primary-tgm ps-3"><label class="small text-muted fw-900 d-block ls-1">IDENTITAS / NIK</label><span class="fw-900 font-mono text-dark">${d.nik}</span></div>
                                    <div class="col-md-6 border-start border-3 border-emerald ps-3"><label class="small text-muted fw-900 d-block ls-1">WHATSAPP</label><span class="fw-900 font-mono text-emerald">${d.telepon}</span></div>
                                    <div class="col-md-6 border-start border-3 border-slate-200 ps-3"><label class="small text-muted fw-900 d-block ls-1">EMAIL</label><span class="fw-900 text-dark small">${d.email || '-'}</span></div>
                                    <div class="col-md-6 border-start border-3 border-slate-200 ps-3"><label class="small text-muted fw-900 d-block ls-1">LOKASI KERJA</label><span class="fw-900 text-dark small">${d.nama_perusahaan}</span></div>
                                    <div class="col-md-6 border-start border-3 border-slate-200 ps-3"><label class="small text-muted fw-900 d-block ls-1">TEMPAT/TGL LAHIR</label><span class="fw-900 text-dark small">${d.tempat_lahir}, ${d.tanggal_lahir}</span></div>
                                    <div class="col-md-6 border-start border-3 border-rose ps-3"><label class="small text-muted fw-900 d-block ls-1">JOIN DATE</label><span class="fw-900 font-mono text-rose">${d.tanggal_masuk}</span></div>
                                    <div class="col-12 bg-slate-50 p-3 rounded-3 mt-2"><label class="small text-muted fw-900 d-block ls-1">ALAMAT DOMISILI</label><span class="small fw-bold text-dark">${d.alamat || '-'}</span></div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="v-doc"><div class="row g-2">${docsHtml}</div></div>
                        </div>
                    </div>
                    <div class="p-3 bg-slate-50 border-top text-center">
                        <button type="button" class="btn btn-white border rounded-pill px-4 fw-900 small ls-1" data-bs-dismiss="modal">TUTUP PROFIL</button>
                    </div>
                `);
            }
        });
    }

    function addKaryawan() {
        $('#karyawanModalTitle').text('Tambah Karyawan Baru');
        $('#form_karyawan_id').val('');
        $('#karyawanForm')[0].reset();
        $('#photoPreviewWrapper').addClass('d-none');
        $('.select2-modal').val('').trigger('change');
        $('#info-tab').tab('show');
        $('#karyawanModal').modal('show');
    }

    function editKaryawan(id) {
        $('#karyawanModalTitle').text('Update Data Karyawan');
        $('#karyawanForm')[0].reset();
        $('#info-tab').tab('show');

        $.ajax({
            url: '<?= base_url("karyawan/get_karyawan_json/") ?>' + id,
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    const d = res.data;
                    $('#form_karyawan_id').val(d.karyawan_id);
                    $('#form_nama_lengkap').val(d.nama_lengkap);
                    $('#form_nik').val(d.nik);
                    $('#form_jabatan_id').val(d.jabatan_id).trigger('change');
                    $('#form_divisi_id').val(d.divisi_id).trigger('change');
                    $('#form_perusahaan_id').val(d.perusahaan_id).trigger('change');
                    $('#form_email').val(d.email);
                    $('#form_telepon').val(d.telepon);
                    $('#form_alamat').val(d.alamat);
                    $('#form_tanggal_masuk').val(d.tanggal_masuk);
                    $('#form_status_karyawan').val(d.status_karyawan);
                    $('#form_jenis_gaji').val(d.jenis_gaji);
                    $('#form_jenis_kelamin').val(d.jenis_kelamin);
                    $('#form_tempat_lahir').val(d.tempat_lahir);
                    $('#form_tanggal_lahir').val(d.tanggal_lahir);
                    $('#form_pin_produksi').val(d.pin_produksi);

                    if (d.foto) {
                        $('#photoPreview').attr('src', '<?= base_url("assets/images/karyawan/") ?>' + d.foto);
                        $('#photoPreviewWrapper').removeClass('d-none');
                    } else {
                        $('#photoPreviewWrapper').addClass('d-none');
                    }

                    $('#karyawanModal').modal('show');
                }
            }
        });
    }

    function editPayroll(id, nama, gaji, lembur, tunjangan, foto, jenisGaji, transportAktif) {
        $('#payroll_karyawan_id').val(id);
        $('#payroll_nama_karyawan').text(nama);
        $('#payroll_jenis_gaji').val(jenisGaji || 'Bulanan').trigger('change');
        $('#payroll_transport_aktif').val(transportAktif !== undefined ? transportAktif : 1);

        // Use the formatRupiah helper defined in document.ready if accessible, 
        // or just a simple regex for initial load
        const fmt = (num) => num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");

        $('#payroll_gaji_pokok').val(fmt(gaji || 0));
        $('#payroll_nominal_lembur').val(fmt(lembur || 0));
        $('#payroll_tunjangan_jabatan').val(fmt(tunjangan || 0));
        $('#payroll_avatar').attr('src', foto);

        // Fetch position info for modal
        const row = $(`a[onclick *= "viewKaryawan(${id})"]`).closest('tr');
        const pos = row.find('td:nth-child(4) .fw-semibold').text();
        $('#payroll_posisi_karyawan').text(pos);

        $('#payrollModal').modal('show');
    }

    function deleteKaryawan(id) {
        Swal.fire({
            title: 'Hapus Karyawan?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= base_url("karyawan/delete_karyawan/") ?>' + id;
            }
        });
    }

    function filterByPerusahaan(id) {
        $('#perusahaan_filter').val(id).trigger('change');
        // Scroll to table smoothly
        document.querySelector('.card-header').scrollIntoView({ behavior: 'smooth' });
    }

    function prosesPerpanjanganKontrak(karyawanId, namaKaryawan, akhirKontrak) {
        $.ajax({
            url: '<?= base_url("karyawan/get_pengajuan_kontrak_ajax") ?>',
            type: 'POST',
            data: { karyawan_id: karyawanId },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    const p = res.data;
                    const formatTgl = (tgl) => {
                        if (!tgl || tgl === 'null' || tgl === '0000-00-00') return 'Belum ditentukan';
                        const parts = tgl.split('-');
                        return `${parts[2]}/${parts[1]}/${parts[0]}`;
                    };

                    Swal.fire({
                        title: 'Proses Perpanjangan Kontrak',
                        html: `
                            <div class="text-start p-3 border rounded bg-light mb-3" style="font-size: 0.9rem;">
                                <div class="mb-2"><strong>Nama Karyawan:</strong> ${namaKaryawan.toUpperCase()}</div>
                                <div class="mb-2"><strong>Kontrak Saat Ini:</strong> <span class="badge bg-secondary">${formatTgl(akhirKontrak)}</span></div>
                                <div class="mb-2"><strong>Perpanjangan Diajukan:</strong> <span class="badge bg-primary">${p.durasi_perpanjangan} Bulan</span></div>
                                <div class="mb-0"><strong>Alasan / Keterangan Karyawan:</strong><br><span class="text-muted italic">"${p.alasan}"</span></div>
                            </div>
                            <div class="text-center fw-bold text-dark small mb-1">PILIH TINDAKAN PERSETUJUAN</div>
                        `,
                        showCancelButton: true,
                        showDenyButton: true,
                        confirmButtonText: '<i class="bi bi-check-circle-fill me-1"></i> SETUJUI (APPROVE)',
                        denyButtonText: '<i class="bi bi-x-circle-fill me-1"></i> TOLAK (REJECT)',
                        cancelButtonText: 'BATAL',
                        confirmButtonColor: '#10b981',
                        denyButtonColor: '#ef4444',
                        cancelButtonColor: '#6c757d',
                        borderRadius: '20px',
                        reverseButtons: true
                    }).then((result) => {
                        let status_keputusan = '';
                        if (result.isConfirmed) {
                            status_keputusan = 'Approved';
                        } else if (result.isDenied) {
                            status_keputusan = 'Rejected';
                        } else {
                            return; // Batal
                        }

                        // Kirim keputusan via AJAX
                        Swal.fire({
                            title: 'Memproses Keputusan...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: '<?= base_url("karyawan/aksi_proses_kontrak") ?>',
                            type: 'POST',
                            data: {
                                karyawan_id: karyawanId,
                                status: status_keputusan
                            },
                            dataType: 'json',
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Sukses!',
                                        text: response.message,
                                        confirmButtonColor: '#10b981',
                                        borderRadius: '20px'
                                    }).then(() => {
                                        loadData(); // Reload datatable secara dinamis
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: response.message,
                                        confirmButtonColor: '#ef4444',
                                        borderRadius: '20px'
                                    });
                                }
                            },
                            error: function () {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Terjadi kesalahan saat memproses keputusan.',
                                    confirmButtonColor: '#ef4444',
                                    borderRadius: '20px'
                                });
                            }
                        });
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'Gagal memuat data pengajuan dari server.', 'error');
            }
        });
    }

    function togglePinKaryawan() {
        const pinInput = document.getElementById('form_pin_produksi');
        const pinIcon = document.getElementById('icon_toggle_pin');
        if (pinInput.type === 'password') {
            pinInput.type = 'text';
            pinIcon.classList.remove('fa-eye');
            pinIcon.classList.add('fa-eye-slash');
        } else {
            pinInput.type = 'password';
            pinIcon.classList.remove('fa-eye-slash');
            pinIcon.classList.add('fa-eye');
        }
    }
</script>