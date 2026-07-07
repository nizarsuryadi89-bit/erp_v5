<style>
    :root {
        --tgm-indigo: #4f46e5;
        --tgm-slate-100: #f1f5f9;
        --tgm-slate-200: #e2e8f0;
        --tgm-slate-700: #334155;
        --tgm-slate-900: #0f172a;
    }

    .config-card {
        background: white;
        border: 1px solid var(--tgm-slate-200);
        border-radius: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    .config-card:hover {
        border-color: var(--tgm-indigo);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .config-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--tgm-slate-100);
        background: var(--tgm-slate-50);
    }

    .config-body {
        padding: 1.5rem;
    }

    .config-group-label {
        font-size: 0.7rem;
        font-weight: 800;
        color: var(--tgm-indigo);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-label {
        font-weight: 700;
        color: var(--tgm-slate-900);
        font-size: 0.9rem;
    }

    .form-control {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid var(--tgm-slate-200);
        font-weight: 600;
        transition: all 0.2s;
    }

    .form-control:focus {
        border-color: var(--tgm-indigo);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }

    .config-desc {
        font-size: 0.8rem;
        color: var(--tgm-slate-500);
        margin-top: 0.5rem;
    }

    .save-bar {
        position: sticky;
        bottom: 1.5rem;
        z-index: 100;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(8px);
        border: 1px solid var(--tgm-slate-200);
        border-radius: 100px;
        padding: 0.75rem 1.5rem;
        margin-top: 2rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .badge-group {
        font-size: 0.65rem;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        background: var(--tgm-slate-100);
        color: var(--tgm-slate-700);
    }
</style>

<div class="app-content">
    <?php $this->load->view('layout_admin/header_modul'); ?>

    <div class="container pt-4 pb-5">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center gap-3">
                <a href="<?= base_url('karyawan/absen_lembur') ?>"
                    class="btn btn-white shadow-sm d-flex align-items-center justify-content-center"
                    style="width: 42px; height: 42px; border-radius: 10px;">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>
                    <h4 class="fw-900 mb-0 ls-1">KEBIJAKAN <span class="text-indigo">LEMBUR</span></h4>
                    <p class="small text-muted mb-0">Atur parameter perhitungan lembur global</p>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-7">
                <div class="clean-card p-4 border h-100" style="border-radius: 12px; background: #fff;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-info-circle-fill text-primary"></i>
                        <h6 class="fw-800 mb-0">INFORMASI JAM KERJA REGULER</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0 text-center small">
                            <thead class="bg-light">
                                <tr>
                                    <?php foreach ($jam_kerja as $jk): ?>
                                        <th class="fw-bold">
                                            <?= strtoupper($jk->hari) ?>
                                        </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <?php foreach ($jam_kerja as $jk): ?>
                                        <td>
                                            <div class="fw-bold text-dark">
                                                <?= substr($jk->jam_masuk, 0, 5) ?> -
                                                <?= substr($jk->jam_pulang, 0, 5) ?>
                                            </div>
                                            <?php if ($jk->hari == 'Sabtu'): ?>
                                                <span class="badge bg-warning text-dark" style="font-size: 0.6rem;">PULANG
                                                    AWAL</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="x-small text-muted mt-2 mb-0 italic">* Gunakan jam pulang di atas sebagai acuan saat
                        menentukan
                        <b>Jam Mulai Wajib</b> di bawah.
                    </p>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="clean-card p-4 border h-100" style="border-radius: 12px; background: #f8fafc;">
                    <!-- Section: Usage SOP -->
                    <div class="mb-3">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-info-circle-fill text-indigo"></i>
                            <h6 class="fw-800 mb-0 text-dark">SOP PENGATURAN JAM LEMBUR</h6>
                        </div>
                        <div class="small text-muted lh-sm">
                            <ol class="ps-3 mb-0">
                                <li class="mb-2">
                                    <b class="text-dark">Sistem Tabel Dinamis</b>: Sekarang Anda bebas menentukan berapa
                                    *Hitungan Jam Efektif (Tarif)* untuk rentang waktu tertentu.
                                </li>
                                <li class="mb-2">
                                    <b class="text-dark">Acuan Jam Pulang Kantor</b>: Usahakan sinkron antara batas
                                    lembur pagi dengan jam masuk kantor, dan batas jam pulang kantor dengan batas jam
                                    mulai lembur sore.
                                </li>
                                <li class="mb-0">
                                    <b class="text-dark">Hitungan Otomatis</b>: Saat karyawan mengisi lembur atau check
                                    out, sistem akan otomatis mencocokkan jam kerjanya dengan tabel di bawah.
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <ul class="nav nav-pills custom-pills mt-4 mb-3" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active px-4 fw-bold rounded-pill shadow-sm" id="pills-weekday-tab"
                    data-bs-toggle="pill" data-bs-target="#pills-weekday" type="button" role="tab" aria-selected="true">
                    <i class="bi bi-calendar-event me-2"></i> Hari Senin - Jumat
                </button>
            </li>
            <li class="nav-item ms-2" role="presentation">
                <button class="nav-link px-4 fw-bold rounded-pill shadow-sm" id="pills-weekend-tab"
                    data-bs-toggle="pill" data-bs-target="#pills-weekend" type="button" role="tab"
                    aria-selected="false">
                    <i class="bi bi-calendar-check me-2"></i> Khusus Hari Sabtu
                </button>
            </li>
        </ul>

        <div class="tab-content" id="pills-tabContent">
            <!-- Tab Senin-Jumat -->
            <div class="tab-pane fade show active" id="pills-weekday" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-calendar-week me-2 text-primary"></i>Daftar Sesi Senin - Jumat</h6>
                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm btn-add-jam" data-hari="Senin-Jumat">
                        <i class="bi bi-plus-circle me-1"></i> TAMBAH SESI
                    </button>
                </div>
                <div class="card border-0 shadow-sm overflow-hidden mb-4" style="border-radius: 12px;">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-center">
                            <thead class="bg-indigo text-white">
                                <tr>
                                    <th class="py-3 ps-4 text-start">Sesi Lembur</th>
                                    <th class="py-3">Jam Masuk (Dari)</th>
                                    <th class="py-3">Jam Selesai (S.d)</th>
                                    <th class="py-3 shadow-sm bg-white text-dark rounded-top-2 mx-1 px-3 d-inline-block mt-n1 mb-n1"
                                        style="transform: scale(1.05);">Total Efektif (Tarif Jam)</th>
                                    <th class="py-3 pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $weekday_configs = array_filter($jam_configs, function ($c) {
                                    return $c->hari == 'Senin-Jumat';
                                });
                                foreach ($weekday_configs as $cfg):
                                    ?>
                                    <tr>
                                        <td class="ps-4 text-start fw-bold text-dark">
                                            <?= $cfg->keterangan ?>
                                            <?php if (isset($cfg->is_wajib) && $cfg->is_wajib == 1): ?>
                                                <span
                                                    class="badge bg-warning bg-opacity-10 text-warning border border-warning ms-2"
                                                    style="font-size:0.65rem;">WAJIB</span>
                                            <?php else: ?>
                                                <span
                                                    class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary ms-2"
                                                    style="font-size:0.65rem;">TAMBAHAN</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div
                                                class="d-inline-flex align-items-center bg-light rounded px-3 py-2 fw-bold font-mono">
                                                <i class="bi bi-clock me-2 text-muted small"></i>
                                                <?= substr($cfg->jam_mulai, 0, 5) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div
                                                class="d-inline-flex align-items-center bg-light rounded px-3 py-2 fw-bold font-mono">
                                                <i class="bi bi-clock me-2 text-muted small"></i>
                                                <?= substr($cfg->jam_selesai, 0, 5) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-success border border-success text-white fw-bold shadow-sm"
                                                style="font-size: 0.9rem; padding: 0.6rem 1.2rem; border-radius: 50px; letter-spacing: 0.5px;">
                                                <?= $cfg->total_jam ?> Jam
                                            </span>
                                        </td>
                                        <td class="pe-4">
                                            <button type="button"
                                                class="btn btn-sm btn-light border btn-edit-jam rounded-circle p-2"
                                                data-id="<?= $cfg->id ?>" data-ket="<?= $cfg->keterangan ?>"
                                                data-mulai="<?= substr($cfg->jam_mulai, 0, 5) ?>"
                                                data-selesai="<?= substr($cfg->jam_selesai, 0, 5) ?>"
                                                data-total="<?= $cfg->total_jam ?>"
                                                data-wajib="<?= isset($cfg->is_wajib) ? $cfg->is_wajib : 0 ?>">
                                                <i class="bi bi-pencil-fill text-primary"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light border btn-delete-jam rounded-circle p-2 ms-1" data-id="<?= $cfg->id ?>">
                                                <i class="bi bi-trash-fill text-danger"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab Sabtu -->
            <div class="tab-pane fade" id="pills-weekend" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-calendar-event me-2 text-primary"></i>Daftar Sesi Sabtu</h6>
                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm btn-add-jam" data-hari="Sabtu">
                        <i class="bi bi-plus-circle me-1"></i> TAMBAH SESI
                    </button>
                </div>
                <div class="card border-0 shadow-sm overflow-hidden mb-4" style="border-radius: 12px;">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-center">
                            <thead class="bg-indigo text-white">
                                <tr>
                                    <th class="py-3 ps-4 text-start">Sesi Lembur</th>
                                    <th class="py-3">Jam Masuk (Dari)</th>
                                    <th class="py-3">Jam Selesai (S.d)</th>
                                    <th class="py-3 shadow-sm bg-white text-dark rounded-top-2 mx-1 px-3 d-inline-block mt-n1 mb-n1"
                                        style="transform: scale(1.05);">Total Efektif (Tarif Jam)</th>
                                    <th class="py-3 pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sat_configs = array_filter($jam_configs, function ($c) {
                                    return $c->hari == 'Sabtu';
                                });
                                foreach ($sat_configs as $cfg):
                                    ?>
                                    <tr>
                                        <td class="ps-4 text-start fw-bold text-dark">
                                            <?= $cfg->keterangan ?>
                                            <?php if (isset($cfg->is_wajib) && $cfg->is_wajib == 1): ?>
                                                <span
                                                    class="badge bg-warning bg-opacity-10 text-warning border border-warning ms-2"
                                                    style="font-size:0.65rem;">WAJIB</span>
                                            <?php else: ?>
                                                <span
                                                    class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary ms-2"
                                                    style="font-size:0.65rem;">TAMBAHAN</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div
                                                class="d-inline-flex align-items-center bg-light rounded px-3 py-2 fw-bold font-mono">
                                                <i class="bi bi-clock me-2 text-muted small"></i>
                                                <?= substr($cfg->jam_mulai, 0, 5) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div
                                                class="d-inline-flex align-items-center bg-light rounded px-3 py-2 fw-bold font-mono">
                                                <i class="bi bi-clock me-2 text-muted small"></i>
                                                <?= substr($cfg->jam_selesai, 0, 5) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-success border border-success text-white fw-bold shadow-sm"
                                                style="font-size: 0.9rem; padding: 0.6rem 1.2rem; border-radius: 50px; letter-spacing: 0.5px;">
                                                <?= $cfg->total_jam ?> Jam
                                            </span>
                                        </td>
                                        <td class="pe-4">
                                            <button type="button"
                                                class="btn btn-sm btn-light border btn-edit-jam rounded-circle p-2"
                                                data-id="<?= $cfg->id ?>" data-ket="<?= $cfg->keterangan ?>"
                                                data-mulai="<?= substr($cfg->jam_mulai, 0, 5) ?>"
                                                data-selesai="<?= substr($cfg->jam_selesai, 0, 5) ?>"
                                                data-total="<?= $cfg->total_jam ?>"
                                                data-wajib="<?= isset($cfg->is_wajib) ? $cfg->is_wajib : 0 ?>">
                                                <i class="bi bi-pencil-fill text-primary"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light border btn-delete-jam rounded-circle p-2 ms-1" data-id="<?= $cfg->id ?>">
                                                <i class="bi bi-trash-fill text-danger"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <?php $this->load->view('admin/karyawan/jam_lembur_config_modal'); ?>
        <hr class="my-5 opacity-10">

        <form id="formConfigLembur">
            <?php
            $groups = [
                'uang_makan' => ['icon' => 'bi-cup-hot', 'title' => 'Uang Makan Lembur', 'desc' => 'Nominal uang makan berdasarkan jam tutup lembur (contoh: 15000)']
            ];

            foreach ($groups as $group_key => $group_info):
                $current_group_configs = array_filter($configs, function ($c) use ($group_key) {
                    return $c->config_group == $group_key;
                });
                if (empty($current_group_configs))
                    continue;
                ?>
                <div class="mb-5">
                    <div class="d-flex align-items-center gap-3 mb-3 mt-4">
                        <div class="bg-indigo rounded-3 p-2 text-white">
                            <i class="<?= $group_info['icon'] ?> fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-800 mb-0 text-dark">
                                <?= $group_info['title'] ?>
                            </h6>
                            <p class="small text-muted mb-0">
                                <?= $group_info['desc'] ?>
                            </p>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 12px;">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="ps-4 py-3" style="width: 300px;">Nama Pengaturan</th>
                                        <th class="py-3" style="width: 250px;">Input / Nilai Baru</th>
                                        <th class="py-3 pe-4">Keterangan / Deskripsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($current_group_configs as $cfg): ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-dark">
                                                <?= $cfg->label ?>
                                                <div class="x-small text-muted fw-normal font-mono"><?= $cfg->config_key ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if (strpos($cfg->config_key, 'time') !== false || strpos($cfg->config_key, 'start') !== false || strpos($cfg->config_key, 'end') !== false): ?>
                                                    <input type="time" name="config[<?= $cfg->config_key ?>]" class="form-control"
                                                        value="<?= $cfg->config_value ?>">
                                                <?php elseif (strpos($cfg->config_key, 'multiplier') !== false): ?>
                                                    <div class="input-group">
                                                        <input type="number" step="0.1" name="config[<?= $cfg->config_key ?>]"
                                                            class="form-control" value="<?= $cfg->config_value ?>">
                                                        <span
                                                            class="input-group-text bg-light border-start-0 text-muted fw-bold">X</span>
                                                    </div>
                                                <?php else: ?>
                                                    <input type="text" name="config[<?= $cfg->config_key ?>]" class="form-control"
                                                        value="<?= $cfg->config_value ?>">
                                                <?php endif; ?>
                                            </td>
                                            <td class="pe-4">
                                                <span class="small text-muted"><?= $cfg->deskripsi ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="save-bar d-flex align-items-center justify-content-between">
                <div class="d-none d-md-block">
                    <span class="badge-group"><i class="bi bi-info-circle me-1"></i> Perubahan akan langsung berdampak
                        pada perhitungan baru.</span>
                </div>
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" id="btnSaveConfig">
                    <i class="bi bi-cloud-arrow-up me-2"></i> SIMPAN PERUBAHAN
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Handler Simpan Konfigurasi Umum (Uang Makan, dll)
        $('#formConfigLembur').on('submit', function (e) {
            e.preventDefault();
            const btn = $('#btnSaveConfig');
            const originalText = btn.html();
            btn.html('<i class="bi bi-hourglass-split me-2"></i> MENYIMPAN...').prop('disabled', true);

            $.ajax({
                url: '<?= base_url("karyawan/update_pengaturan_lembur_ajax") ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Tersimpan!',
                            text: 'Kebijakan lembur berhasil diperbarui.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Gagal', res.message || 'Terjadi kesalahan sistem', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Gagal menghubungi server', 'error');
                },
                complete: function () {
                    btn.html(originalText).prop('disabled', false);
                }
            });
        });

        // Handler Edit Sesi Lembur
        $(document).on('click', '.btn-edit-jam', function () {
            const id = $(this).data('id');
            const ket = $(this).data('ket');
            const mulai = $(this).data('mulai');
            const selesai = $(this).data('selesai');
            const total = $(this).data('total');
            const wajib = $(this).data('wajib');
            const hari = $(this).closest('.tab-pane').attr('id') == 'pills-weekday' ? 'Senin-Jumat' : 'Sabtu';

            $('#jamModalTitle').text('Ubah Sesi Lembur');
            $('#edit_jam_id').val(id);
            $('#edit_jam_hari').val(hari).prop('disabled', true);
            $('#edit_jam_keterangan').val(ket).prop('readonly', true);
            $('#edit_jam_mulai').val(mulai);
            $('#edit_jam_selesai').val(selesai);
            $('#edit_total_jam').val(total);
            $('#edit_is_wajib').prop('checked', wajib == 1);
            $('#modalEditJamLembur').modal('show');
        });

        // Handler Tambah Sesi Baru
        $(document).on('click', '.btn-add-jam', function () {
            const hari = $(this).data('hari');
            $('#formEditJamLembur')[0].reset();
            $('#jamModalTitle').text('Tambah Sesi Lembur Baru');
            $('#edit_jam_id').val('');
            $('#edit_jam_hari').val(hari).prop('disabled', false);
            $('#edit_jam_keterangan').val('').prop('readonly', false);
            $('#modalEditJamLembur').modal('show');
        });

        // Handler Submit Form Jam Lembur (Add & Edit)
        $('#formEditJamLembur').on('submit', function (e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            const originalText = btn.html();

            // Enable disabled fields temporarily for serialization
            $('#edit_jam_hari').prop('disabled', false);

            btn.html('<i class="bi bi-hourglass-split me-2"></i> ...').prop('disabled', true);

            $.ajax({
                url: '<?= base_url("karyawan/update_jam_lembur_ajax") ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                        // Restore disable state if failed during edit
                        if ($('#edit_jam_id').val() != '') $('#edit_jam_hari').prop('disabled', true);
                        btn.html(originalText).prop('disabled', false);
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Gagal memproses data', 'error');
                    btn.html(originalText).prop('disabled', false);
                }
            });
        });

        // Handler Hapus Sesi Lembur
        $(document).on('click', '.btn-delete-jam', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Hapus Sesi?',
                text: "Sesi lembur ini tidak akan muncul lagi di laporan harian.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url("karyawan/delete_jam_lembur_ajax") ?>',
                        type: 'POST',
                        data: { id: id },
                        dataType: 'json',
                        success: function (res) {
                            if (res.success) {
                                Swal.fire('Dihapus!', res.message, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        }
                    });
                }
            });
        });
    });
</script>