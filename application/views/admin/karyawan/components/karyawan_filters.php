<!-- Employee Control Panel (Filter & Logic) -->
<style>
    .filter-panel-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    }
    .filter-label {
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 0.35rem;
        display: block;
        text-transform: uppercase;
    }
    .filter-control {
        height: 40px !important;
        border-radius: 8px !important;
        border: 1px solid #e2e8f0 !important;
        background-color: #f8fafc !important;
        font-size: 0.85rem !important;
        font-weight: 600 !important;
        color: #1e293b !important;
    }
    .filter-control:focus {
        background-color: #ffffff !important;
        border-color: var(--tgm-primary) !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;
    }
    .filter-search-group .input-group-text {
        height: 40px;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-right: none;
        border-radius: 8px 0 0 8px !important;
        color: #94a3b8;
    }
    .filter-search-group .form-control {
        height: 40px;
        border-radius: 0 8px 8px 0 !important;
        border-left: none;
    }
    .filter-search-group:focus-within .input-group-text,
    .filter-search-group:focus-within .form-control {
        background-color: #ffffff !important;
        border-color: var(--tgm-primary) !important;
    }
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 40px !important;
        height: 40px !important;
        border-radius: 8px !important;
        border: 1px solid #e2e8f0 !important;
        background-color: #f8fafc !important;
        display: flex !important;
        align-items: center !important;
        font-size: 0.85rem !important;
        font-weight: 600 !important;
    }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        background-color: #ffffff !important;
        border-color: var(--tgm-primary) !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        padding-left: 10px !important;
        color: #1e293b !important;
    }
    .btn-filter-action {
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px !important;
    }
</style>

<div class="filter-panel-card mb-4 no-print">
    <div class="p-3 p-md-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-900 text-dark mb-0 ls-1">
                <i class="bi bi-funnel-fill me-2 text-primary-tgm"></i>KONFIGURASI PENYARINGAN
            </h6>
            <div class="badge bg-slate-50 text-primary-tgm border border-primary-tgm border-opacity-25 px-3 py-2 font-mono small rounded-pill">
                <i class="bi bi-person-check me-1"></i> MASTER DATABASE
            </div>
        </div>

        <form id="searchForm" class="row g-3 align-items-end">
            <!-- Cari -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-3">
                <label class="filter-label">Cari Karyawan</label>
                <div class="input-group filter-search-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control filter-control ps-1" id="search" name="search"
                        placeholder="Nama / NIK / Email..." value="<?= $this->input->get('search') ?>">
                </div>
            </div>

            <!-- Gender -->
            <div class="col-6 col-sm-6 col-md-4 col-lg-2 col-xl-2">
                <label class="filter-label">Gender</label>
                <select class="form-select filter-control select2-filter" id="gender_filter" name="gender">
                    <option value="">Semua Gender</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>

            <!-- Status -->
            <div class="col-6 col-sm-6 col-md-4 col-lg-2 col-xl-2">
                <label class="filter-label">Status Kerja</label>
                <select class="form-select filter-control select2-filter" id="status_filter" name="status">
                    <option value="">Semua Status</option>
                    <option value="Tetap">Tetap</option>
                    <option value="Kontrak">Kontrak</option>
                    <option value="Magang">Magang</option>
                    <option value="Outsourcing">Outsourcing</option>
                </select>
            </div>

            <!-- Jabatan -->
            <div class="col-6 col-sm-6 col-md-4 col-lg-2 col-xl-2">
                <label class="filter-label">Jabatan</label>
                <select class="form-select filter-control select2-filter" id="jabatan_filter" name="jabatan">
                    <option value="">Semua Jabatan</option>
                    <?php foreach ($all_jabatan as $j): ?>
                        <option value="<?= $j->jabatan_id ?>"><?= htmlspecialchars($j->nama_jabatan) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Cabang -->
            <div class="col-6 col-sm-6 col-md-4 col-lg-2 col-xl-2">
                <label class="filter-label">Cabang</label>
                <select class="form-select filter-control select2-filter" id="perusahaan_filter" name="perusahaan">
                    <option value="">Semua Cabang</option>
                    <?php foreach ($all_perusahaan as $p):
                        $is_restricted = ($this->session->userdata('perusahaan_id') != 1 && $this->session->userdata('level') != 21 && $this->session->userdata('perusahaan_id') != $p->id_perusahaan);
                        if ($is_restricted)
                            continue;
                        ?>
                        <option value="<?= $p->id_perusahaan ?>"><?= htmlspecialchars($p->nama_perusahaan) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Tombol Aksi -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-1 col-xl-1">
                <div class="d-flex gap-1">
                    <button type="button" id="btnResetFilter"
                        class="btn btn-slate-100 border text-muted btn-filter-action flex-fill" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                    <button type="submit" class="btn btn-primary-tgm fw-bold btn-filter-action flex-fill shadow-sm" title="Terapkan Filter">
                        <i class="bi bi-funnel-fill"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>