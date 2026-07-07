<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">

<!-- <div class="app-content"> -->
<!-- <div class="container-fluid"> -->
<!-- Header -->
<?php $this->load->view('layout_admin/header_modul'); ?>

<!-- Guide Button & Title -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-2">
        <a href="<?= base_url('hr') ?>" class="btn btn-white d-flex align-items-center justify-content-center shadow-sm"
            style="width: 32px; height: 32px; padding: 0; border-radius: 8px; border: 1px solid var(--tgm-slate-200); background: white; color: var(--tgm-slate-700);"
            title="Kembali ke Dashboard HR">
            <i class="bi bi-arrow-left fs-6"></i>
        </a>
        <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-id-card me-2 text-primary"></i>DASHBOARD PENGELOLAAN SDM
        </h5>
    </div>
    <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal"
        data-bs-target="#modalGuideKaryawan">
        <i class="fas fa-info-circle me-2"></i> PANDUAN FITUR
    </button>
</div>

<!-- Statistics Overview -->
<div class="hr-stats-overview mb-4">
    <div class="hr-stat-card">
        <div class="hr-stat-header">
            <div class="hr-stat-icon primary"><i class="fa fa-users"></i></div>
        </div>
        <div class="hr-stat-value"><?= number_format($total_karyawan) ?></div>
        <div class="hr-stat-label">Total Karyawan</div>
    </div>

    <div class="hr-stat-card">
        <div class="hr-stat-header">
            <div class="hr-stat-icon success"><i class="fa fa-user-check"></i></div>
        </div>
        <div class="hr-stat-value"><?= number_format($total_akun) ?></div>
        <div class="hr-stat-label">Akun Aktif</div>
    </div>

    <?php foreach ($company_stats as $stat): ?>
        <div class="hr-stat-card">
            <div class="hr-stat-header">
                <div class="hr-stat-icon warning"><i class="fa fa-building"></i></div>
                <button class="btn btn-sm btn-light btn-view-emp" data-id="<?= $stat->id_perusahaan ?>"
                    data-nama="<?= $stat->nama_perusahaan ?>" data-toggle="modal" data-target="#modalKaryawanPerusahaan">
                    <i class="fa fa-eye text-primary"></i>
                </button>
            </div>
            <div class="hr-stat-value"><?= number_format($stat->total_karyawan) ?></div>
            <div class="hr-stat-label"><?= $stat->nama_perusahaan ?></div>
        </div>
    <?php endforeach; ?>
</div>


<!-- Quick Actions -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <h6 class="fw-bold text-dark mb-3"><i class="fa fa-bolt text-warning me-2"></i>Aksi Cepat</h6>
        <div class="row g-2">
            <div class="col-6 col-md-2">
                <a href="<?= base_url('akun') ?>"
                    class="w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 shadow-sm"
                    style="background: linear-gradient(135deg, #3498db, #2980b9); border-radius: 12px; color: white; text-decoration: none; transition: transform 0.2s;">
                    <i class="fa fa-users mb-2 fs-5"></i>
                    <span class="small fw-bold text-center">Akun Karyawan</span>
                </a>
            </div>
            <div class="col-6 col-md-2">
                <a href="<?= base_url('karyawan/karyawan') ?>"
                    class="w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 shadow-sm"
                    style="background: linear-gradient(135deg, #2c3e50, #34495e); border-radius: 12px; color: white; text-decoration: none; transition: transform 0.2s;">
                    <i class="fa fa-list mb-2 fs-5"></i>
                    <span class="small fw-bold text-center">Semua Karyawan</span>
                </a>
            </div>
            <div class="col-6 col-md-2">
                <a href="<?= base_url('absensi') ?>"
                    class="w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 shadow-sm"
                    style="background: linear-gradient(135deg, #1abc9c, #16a085); border-radius: 12px; color: white; text-decoration: none; transition: transform 0.2s;">
                    <i class="fa fa-clock mb-2 fs-5"></i>
                    <span class="small fw-bold text-center">Absensi</span>
                </a>
            </div>
            <div class="col-6 col-md-2">
                <a href="<?= base_url('karyawan/rekap_cuti') ?>"
                    class="w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 shadow-sm"
                    style="background: linear-gradient(135deg, #1abc9c, #16a085); border-radius: 12px; color: white; text-decoration: none; transition: transform 0.2s;">
                    <i class="fa fa-calendar mb-2 fs-5"></i>
                    <span class="small fw-bold text-center">Rekap Cuti</span>
                </a>
            </div>
            <div class="col-6 col-md-2">
                <a href="<?= base_url('karyawan/penilaian_kinerja') ?>"
                    class="w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 shadow-sm"
                    style="background: linear-gradient(135deg, #de6686ff, #cb1bb1ff); border-radius: 12px; color: white; text-decoration: none; transition: transform 0.2s;">
                    <i class="fa fa-chart-line mb-2 fs-5"></i>
                    <span class="small fw-bold text-center">Penilaian Kinerja</span>
                </a>
            </div>
            <div class="col-6 col-md-2">
                <a href="<?= base_url('karyawan/penggajian') ?>"
                    class="w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 shadow-sm"
                    style="background: linear-gradient(135deg, #27ae60, #2ecc71); border-radius: 12px; color: white; text-decoration: none; transition: transform 0.2s;">
                    <i class="fa fa-database mb-2 fs-5"></i>
                    <span class="small fw-bold text-center">Penggajian</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row">
    <!-- Right: Division Summary -->
    <div class="col-md-12 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title text-dark mb-0"><i class="fa fa-building me-2"></i>Karyawan per Divisi
                    </h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%">No</th>
                                <th>Divisi</th>
                                <th class="text-center" style="width: 20%">Jumlah</th>
                                <th class="text-center" style="width: 20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($karyawan_by_divisi)):
                                $no = 1;
                                foreach ($karyawan_by_divisi as $divisi):
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <strong><?= $divisi->divisi_nama ?></strong>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-primary rounded-pill px-3"><?= number_format($divisi->jumlah_karyawan) ?></span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-light text-primary border"
                                                onclick="showDivisiModal('<?= $divisi->divisi_id ?>', '<?= $divisi->divisi_nama ?>')">
                                                <i class="fa fa-eye me-1"></i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Tidak ada data divisi</td>
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
</div>

<!-- Divisi Employee Modal -->
<div class="modal fade" id="divisiModal" tabindex="-1" aria-labelledby="divisiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #2c3e50, #34495e); color: white;">
                <h5 class="modal-title" id="divisiModalLabel">
                    <i class="fa fa-users me-2"></i>
                    <span id="modalDivisiTitle">Detail Karyawan Divisi</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="divisi-icon mb-3"
                                    style="width: 80px; height: 80px; background: linear-gradient(135deg, #2c3e50, #34495e); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; margin: 0 auto;">
                                    <i class="fa fa-users"></i>
                                </div>
                                <h4 class="text-dark mb-1" id="modalDivisiName">Nama Divisi</h4>
                                <p class="text-muted mb-0">Divisi Karyawan</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="text-dark mb-3">
                                    <i class="fa fa-chart-pie me-2"></i>Statistik Divisi
                                </h6>
                                <div class="text-center">
                                    <h5 class="text-primary mb-1" id="modalTotalKaryawan">0</h5>
                                    <small class="text-muted">Total Karyawan</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header" style="background: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                        <h6 class="mb-0 text-dark">
                            <i class="fa fa-list me-2"></i>Daftar Karyawan
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="modalEmployeeList">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Memuat data karyawan...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i>Tutup
                </button>
                <button type="button" class="btn btn-primary" onclick="exportDivisiData()">
                    <i class="fa fa-download me-1"></i>Export Data
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Animate statistics on page load
    document.addEventListener('DOMContentLoaded', function () {
        // Animate stat values
        const statValues = document.querySelectorAll('.hr-stat-value');
        statValues.forEach(stat => {
            const finalValue = parseInt(stat.textContent.replace(/,/g, ''));
            let currentValue = 0;
            const increment = finalValue / 50;

            const timer = setInterval(() => {
                currentValue += increment;
                if (currentValue >= finalValue) {
                    currentValue = finalValue;
                    clearInterval(timer);
                }
                stat.textContent = Math.floor(currentValue).toLocaleString();
            }, 30);
        });
    });

    // Modal functionality for divisi employee details
    let currentDivisiId = null;
    let currentDivisiName = null;

    function showDivisiModal(divisiId, divisiName) {
        currentDivisiId = divisiId;
        currentDivisiName = divisiName;

        // Update modal title and divisi name
        document.getElementById('modalDivisiTitle').textContent = `Detail Karyawan Divisi ${divisiName}`;
        document.getElementById('modalDivisiName').textContent = divisiName;

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('divisiModal'));
        modal.show();

        // Load employee data
        loadDivisiEmployees(divisiId);
    }

    function loadDivisiEmployees(divisiId) {
        // Show loading state
        document.getElementById('modalEmployeeList').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Memuat data karyawan...</p>
        </div>
    `;

        // Fetch employee data via AJAX
        fetch(`<?= base_url('karyawan/get_employees_by_divisi/') ?>${divisiId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayDivisiEmployees(data.employees, data.stats);
                } else {
                    document.getElementById('modalEmployeeList').innerHTML = `
                    <div class="text-center py-4">
                        <i class="fa fa-exclamation-triangle text-warning fa-2x mb-2"></i>
                        <p class="text-muted">Gagal memuat data karyawan</p>
                    </div>
                `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('modalEmployeeList').innerHTML = `
                <div class="text-center py-4">
                    <i class="fa fa-exclamation-triangle text-danger fa-2x mb-2"></i>
                    <p class="text-muted">Terjadi kesalahan saat memuat data</p>
                </div>
            `;
            });
    }

    function displayDivisiEmployees(employees, stats) {
        // Update statistics
        document.getElementById('modalTotalKaryawan').textContent = stats.total || 0;

        if (employees.length === 0) {
            document.getElementById('modalEmployeeList').innerHTML = `
            <div class="text-center py-4">
                <i class="fa fa-users text-muted fa-2x mb-2"></i>
                <p class="text-muted">Tidak ada karyawan dalam divisi ini</p>
            </div>
        `;
            return;
        }

        let html = `
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIP</th>
                        <th>Jabatan</th>
                        <th>Lokasi Cabang</th>
                        <th>Status</th>
                        <th>Gender</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
    `;

        employees.forEach((employee, index) => {
            const statusClass = employee.status === 'Aktif' ? 'success' : 'danger';
            const genderClass = employee.gender === 'Laki-laki' ? 'primary' : 'danger';
            const genderIcon = employee.gender === 'Laki-laki' ? 'fa-male' : 'fa-female';

            html += `
            <tr>
                <td>${index + 1}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar me-2" style="width: 35px; height: 35px; background: linear-gradient(135deg, #2c3e50, #34495e); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.8rem;">
                            ${employee.nama.charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <div class="fw-bold">${employee.nama}</div>
                            <small class="text-muted">@${employee.username || '-'}</small>
                            <br>
                            <small class="text-muted">${employee.email || '-'}</small>
                        </div>
                    </div>
                </td>
                <td><code>${employee.nip || '-'}</code></td>
                <td>
                    <span class="badge bg-info">${employee.jabatan || '-'}</span>
                </td>
                <td>
                    <span class="badge bg-secondary">
                        <i class="fa fa-building me-1"></i>${employee.lokasi_cabang || '-'}
                    </span>
                </td>
                <td>
                    <span class="badge bg-${statusClass}">${employee.status}</span>
                </td>
                <td>
                    <span class="badge bg-${genderClass}">
                        <i class="fa ${genderIcon} me-1"></i>${employee.gender}
                    </span>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="viewEmployee('${employee.id}')" title="Lihat Detail">
                            <i class="fa fa-eye"></i>
                        </button>
                        <button class="btn btn-outline-warning" onclick="editEmployee('${employee.id}')" title="Edit">
                            <i class="fa fa-edit"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        });

        html += `
                </tbody>
            </table>
        </div>
    `;

        document.getElementById('modalEmployeeList').innerHTML = html;
    }

    function viewEmployee(employeeId) {
        // Redirect to employee detail page
        window.open(`<?= base_url('karyawan/detail/') ?>${employeeId}`, '_blank');
    }

    function editEmployee(employeeId) {
        // Redirect to employee edit page
        window.open(`<?= base_url('karyawan/edit/') ?>${employeeId}`, '_blank');
    }

    function exportDivisiData() {
        if (!currentDivisiId) return;

        // Create export URL
        const exportUrl = `<?= base_url('karyawan/export_divisi/') ?>${currentDivisiId}`;

        // Create temporary link and trigger download
        const link = document.createElement('a');
        link.href = exportUrl;
        link.download = `karyawan_divisi_${currentDivisiName}_${new Date().toISOString().split('T')[0]}.xlsx`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>

<!-- Modal List Karyawan -->
<div class="modal fade" id="modalKaryawanPerusahaan" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
    aria-hidden="true" style="z-index: 9999;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Daftar Karyawan: <span id="namaPerusahaanTitle"></span></h5>
                <button type="button" class="close btn-close-modal" data-dismiss="modal" data-bs-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tableKaryawanModal">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Lengkap</th>
                                <th>Jabatan</th>
                                <th>L/P</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4" class="text-center">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-close-modal" data-dismiss="modal"
                    data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Force Close Modal Manual
        $(document).on('click', '.btn-close-modal', function () {
            $('#modalKaryawanPerusahaan').modal('hide');
        });

        // Delegated event listener untuk tombol yang dibuat dinamis atau pun statis
        $(document).on('click', '.btn-view-emp', function (e) {
            e.preventDefault();

            var btn = $(this);
            var id = btn.data('id');
            var nama = btn.data('nama');

            // Update Title Modal
            $('#namaPerusahaanTitle').text(nama);

            // Reset Isi Tabel
            $('#tableKaryawanModal tbody').html('<tr class="bg-light"><td colspan="4" class="text-center py-4"><h5 class="text-muted"><i class="fa fa-spinner fa-spin mr-2"></i> Sedang memuat data...</h5></td></tr>');

            // Panggil Modal secara manual agar lebih reliable
            $('#modalKaryawanPerusahaan').modal('show');

            // Panggil AJAX
            $.ajax({
                url: '<?= base_url("karyawan/get_karyawan_by_perusahaan_ajax") ?>',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function (response) {
                    var html = '';
                    if (response.length > 0) {
                        $.each(response, function (i, item) {
                            html += '<tr>';
                            html += '<td class="text-center">' + (i + 1) + '</td>';
                            html += '<td class="font-weight-bold">' + item.nama_lengkap + '</td>';
                            html += '<td>' + (item.nama_jabatan ? item.nama_jabatan : '<span class="badge badge-secondary">Belum ada jabatan</span>') + '</td>';
                            html += '<td class="text-center">' + item.jenis_kelamin + '</td>';
                            html += '</tr>';
                        });
                    } else {
                        html = '<tr><td colspan="4" class="text-center py-4"><i class="fa fa-info-circle text-info fa-2x mb-2"></i><br>Tidak ada karyawan terdaftar di perusahaan ini.</td></tr>';
                    }

                    // Beri sedikit delay agar loading terlihat (opsional)
                    setTimeout(function () {
                        $('#tableKaryawanModal tbody').html(html);
                    }, 300);
                },
                error: function (xhr, status, error) {
                    console.error(error);
                    $('#tableKaryawanModal tbody').html('<tr><td colspan="4" class="text-center text-danger py-3"><i class="fa fa-exclamation-triangle mr-2"></i> Gagal mengambil data. Silakan coba lagi.</td></tr>');
                }
            });
        });
    });
</script>

<!-- Modal Panduan Karyawan -->
<div class="modal fade" id="modalGuideKaryawan" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0 p-4"
                style="background: linear-gradient(135deg, #2c3e50 0%, #000000 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 45px; height: 45px;">
                        <i class="fas fa-users-cog fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">Panduan Pengelolaan Karyawan (SDM)</h5>
                        <p class="text-white-50 small mb-0">Navigasi pusat data, statistik divisi, dan akses cepat HR
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-3 bg-light border-end">
                        <div class="nav flex-column nav-pills p-3" id="v-pills-tab-emp" role="tablist">
                            <button class="nav-link active text-start py-3 px-4 fw-bold mb-2 border-0"
                                data-bs-toggle="pill" data-bs-target="#emp-stats" type="button">
                                <i class="fas fa-chart-line me-2"></i> Statistik & Perusahaan
                            </button>
                            <button class="nav-link text-start py-3 px-4 fw-bold mb-2 border-0" data-bs-toggle="pill"
                                data-bs-target="#emp-division" type="button">
                                <i class="fas fa-sitemap me-2"></i> Manajemen Divisi
                            </button>
                            <button class="nav-link text-start py-3 px-4 fw-bold mb-2 border-0" data-bs-toggle="pill"
                                data-bs-target="#emp-quick" type="button">
                                <i class="fas fa-bolt me-2"></i> Akses Modul Cepat
                            </button>
                            <button class="nav-link text-start py-3 px-4 fw-bold mb-2 border-0" data-bs-toggle="pill"
                                data-bs-target="#emp-action" type="button">
                                <i class="fas fa-user-edit me-2"></i> Detail & Profile
                            </button>
                        </div>
                    </div>
                    <div class="col-md-9 border-start">
                        <div class="tab-content p-5" id="v-pills-tabContent-emp">

                            <!-- Stats -->
                            <div class="tab-pane fade show active" id="emp-stats">
                                <h4 class="fw-bold mb-4">Pemantauan Populasi Karyawan</h4>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-4 border bg-light h-100">
                                            <h6 class="fw-bold text-dark"><i class="fas fa-users me-2"></i>Total
                                                Populasi</h6>
                                            <p class="small text-muted mb-0">Angka total karyawan terdaftar. Nilai ini
                                                akan menganimasi naik setiap kali halaman dibuka.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-4 border bg-light h-100">
                                            <h6 class="fw-bold text-dark"><i class="fas fa-building me-2"></i>Filter
                                                Perusahaan</h6>
                                            <p class="small text-muted">Gunakan ikon <i
                                                    class="fa fa-eye text-primary"></i> pada kartu perusahaan untuk
                                                melihat daftar nama spesifik di bawah PT tersebut.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Division -->
                            <div class="tab-pane fade" id="emp-division">
                                <h4 class="fw-bold mb-4">Pengelolaan Berbasis Divisi</h4>
                                <div
                                    class="bg-primary bg-opacity-10 p-4 rounded-4 mb-4 border border-primary border-opacity-25">
                                    <p class="small mb-0">Tabel <b>"Karyawan per Divisi"</b> membantu HR melihat
                                        distribusi beban kerja dan struktur organisasi secara ringkas.</p>
                                </div>
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item px-0 py-3">
                                        <div class="fw-bold small mb-1"><i class="fas fa-search me-2"></i>View Detail
                                            Divisi</div>
                                        <p class="small text-muted mb-0">Klik tombol <b>"Detail"</b> untuk membuka
                                            jendela pop-up yang memuat NIP, Jabatan, Cabang, dan Status setiap anggota
                                            divisi tersebut.</p>
                                    </div>
                                    <div class="list-group-item px-0 py-3">
                                        <div class="fw-bold small mb-1"><i class="fas fa-file-excel me-2"></i>Export
                                            Data</div>
                                        <p class="small text-muted mb-0">Di dalam modal divisi, terdapat tombol
                                            <b>Export Data</b> untuk mengunduh daftar karyawan divisi tersebut ke format
                                            Excel.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Access -->
                            <div class="tab-pane fade" id="emp-quick">
                                <h4 class="fw-bold mb-4">Pusat Navigasi (Aksi Cepat)</h4>
                                <p class="text-muted">Sidebar kiri menyediakan shortcut ke modul operasional HR lainnya:
                                </p>
                                <div class="row g-3">
                                    <div class="col-6 col-md-4">
                                        <div class="p-2 border rounded-3 text-center bg-light">
                                            <i class="fa fa-users text-primary mb-1"></i><br>
                                            <small class="fw-bold">Akun Karyawan</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="p-2 border rounded-3 text-center bg-light">
                                            <i class="fa fa-clock text-success mb-1"></i><br>
                                            <small class="fw-bold">Absensi</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="p-2 border rounded-3 text-center bg-light">
                                            <i class="fa fa-database text-info mb-1"></i><br>
                                            <small class="fw-bold">Penggajian</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="p-2 border rounded-3 text-center bg-light">
                                            <i class="fa fa-list text-dark mb-1"></i><br>
                                            <small class="fw-bold">Master Data</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="p-2 border rounded-3 text-center bg-light">
                                            <i class="fa fa-calendar text-warning mb-1"></i><br>
                                            <small class="fw-bold">Rekap Cuti</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="p-2 border rounded-3 text-center bg-light">
                                            <i class="fa fa-chart-line text-danger mb-1"></i><br>
                                            <small class="fw-bold">Penilaian</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="tab-pane fade" id="emp-action">
                                <h4 class="fw-bold mb-4">Akses Profil Individu</h4>
                                <p class="text-muted">Untuk melakukan perubahan data spesifik (Editing) atau melihat
                                    rekam jejak karyawan:</p>
                                <div class="guide-step mb-4">
                                    <div class="fw-bold text-dark">Langkah 1</div>
                                    <p class="small text-muted">Buka Detail Divisi atau Gunakan menu "Semua Karyawan".
                                    </p>
                                </div>
                                <div class="guide-step mb-4">
                                    <div class="fw-bold text-dark">Langkah 2</div>
                                    <p class="small text-muted">Gunakan tombol <i class="fa fa-eye text-primary"></i>
                                        untuk Review atau <i class="fa fa-edit text-warning"></i> untuk mengubah data
                                        master (NIP, Alamat, Payroll, dll).</p>
                                </div>
                                <div class="alert alert-warning border-0 small">
                                    <i class="fas fa-info-circle me-2"></i> Perubahan pada master data karyawan akan
                                    langsung berdampak pada modul Absensi dan Penggajian.
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-light">
                <button type="button" class="btn btn-secondary px-5 fw-bold rounded-pill" data-bs-dismiss="modal">Tutup
                    Panduan</button>
            </div>
        </div>
    </div>
</div>
</content>
```