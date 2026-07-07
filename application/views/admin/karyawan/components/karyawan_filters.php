<!-- Employee Control Panel (Filter & Logic) -->
<div class="clean-card mb-4 no-print overflow-hidden">
    <div class="p-4 bg-white">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h6 class="fw-900 text-dark mb-0 ls-1"><i class="bi bi-funnel-fill me-2 text-primary-tgm"></i>KONFIGURASI
                PENYARINGAN</h6>
            <div class="badge bg-white text-primary-tgm border border-primary-tgm border-opacity-25 px-3 py-2 font-mono small"
                style="border-radius: 50px; box-shadow: 0 2px 4px rgba(37, 99, 235, 0.05);">
                <i class="bi bi-person-check me-2"></i> MASTER DATABASE
            </div>
        </div>

        <form id="searchForm" class="row g-2 align-items-center">
            <div class="col-md-2">
                <div class="input-group">
                    <span class="input-group-text bg-slate-100 border-end-0 text-dark small fw-900 px-3">CARI</span>
                    <input type="text" class="form-control border-start-0 ps-0 fw-bold" id="search" name="search"
                        placeholder="Nama / NIK..." value="<?= $this->input->get('search') ?>">
                </div>
            </div>
            <div class="col-md-2">
                <div class="input-group">
                    <span class="input-group-text bg-slate-100 border-end-0 text-dark small fw-900 px-3">GENDER</span>
                    <select class="form-select border-start-0 ps-0 fw-bold select2-filter" id="gender_filter"
                        name="gender">
                        <option value="">Semua</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="input-group">
                    <span class="input-group-text bg-slate-100 border-end-0 text-dark small fw-900 px-3">STATUS</span>
                    <select class="form-select border-start-0 ps-0 fw-bold select2-filter" id="status_filter"
                        name="status">
                        <option value="">Semua</option>
                        <option value="Tetap">Tetap</option>
                        <option value="Kontrak">Kontrak</option>
                        <option value="Magang">Magang</option>
                        <option value="Outsourcing">Outsourcing</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="input-group">
                    <span class="input-group-text bg-slate-100 border-end-0 text-dark small fw-900 px-3">JABATAN</span>
                    <select class="form-select border-start-0 ps-0 fw-bold select2-filter" id="jabatan_filter"
                        name="jabatan">
                        <option value="">Semua</option>
                        <?php foreach ($all_jabatan as $j): ?>
                            <option value="<?= $j->jabatan_id ?>"><?= htmlspecialchars($j->nama_jabatan) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="input-group">
                    <span class="input-group-text bg-slate-100 border-end-0 text-dark small fw-900 px-3">CABANG</span>
                    <select class="form-select border-start-0 ps-0 fw-bold select2-filter" id="perusahaan_filter"
                        name="perusahaan">
                        <option value="">Semua</option>
                        <?php foreach ($all_perusahaan as $p):
                            $is_restricted = ($this->session->userdata('perusahaan_id') != 1 && $this->session->userdata('level') != 21 && $this->session->userdata('perusahaan_id') != $p->id_perusahaan);
                            if ($is_restricted)
                                continue;
                            ?>
                            <option value="<?= $p->id_perusahaan ?>"><?= htmlspecialchars($p->nama_perusahaan) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="button" id="btnResetFilter"
                    class="btn btn-slate-100 border flex-grow-1 rounded-pill fw-900 small py-2 text-muted">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </button>
                <button type="submit" class="btn btn-primary-tgm flex-grow-1 rounded-pill fw-900 small py-2 px-3">
                    <i class="bi bi-search me-2"></i> FILTER DATA
                </button>
            </div>
        </form>
    </div>
</div>