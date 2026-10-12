<style>
    .status-tabs-wrapper {
        width: 100%;
        overflow-x: auto;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 4px;
        /* for scrollbar */
        margin-bottom: -4px;
    }

    .status-tabs-container {
        display: inline-flex;
        gap: 0.5rem;
        padding: 2px;
    }

    .status-tab {
        padding: 6px 14px;
        border-radius: 20px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        color: #495057;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        display: inline-flex;
        align-items: center;
    }

    .status-tab:hover {
        background: #e2e6ea;
        border-color: #dae0e5;
    }

    .status-tab.active {
        background: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Custom Tab Colors */
    .tab-semua {
        color: #6c757d;
        border-color: #ced4da;
    }

    .tab-semua:hover {
        background: rgba(108, 117, 125, 0.05);
    }

    .tab-semua.active {
        background: #6c757d;
        color: #fff;
        border-color: #6c757d;
    }

    .tab-pending {
        color: #6c757d;
        border-color: #ced4da;
    }

    .tab-pending:hover {
        background: rgba(108, 117, 125, 0.05);
    }

    .tab-pending.active {
        background: #6c757d;
        color: #fff;
        border-color: #6c757d;
    }

    .tab-rijek {
        color: #dc3545;
        border-color: #f5c2c7;
    }

    .tab-rijek:hover {
        background: rgba(220, 53, 69, 0.05);
    }

    .tab-rijek.active {
        background: #dc3545;
        color: #fff;
        border-color: #dc3545;
    }

    .tab-proses {
        color: #fd7e14;
        border-color: #ffc107;
    }

    .tab-proses:hover {
        background: rgba(253, 126, 20, 0.05);
    }

    .tab-proses.active {
        background: #fd7e14;
        color: #fff;
        border-color: #fd7e14;
    }

    .tab-selesai {
        color: #fd7e14;
        border-color: #ffc107;
    }

    .tab-selesai:hover {
        background: rgba(253, 126, 20, 0.05);
    }

    .tab-selesai.active {
        background: #fd7e14;
        color: #fff;
        border-color: #fd7e14;
    }

    .tab-pb {
        color: #198754;
        border-color: #a3cfbb;
    }

    .tab-pb:hover {
        background: rgba(25, 135, 84, 0.05);
    }

    .tab-pb.active {
        background: #198754;
        color: #fff;
        border-color: #198754;
    }

    .tab-konsumen {
        color: #0d6efd;
        border-color: #9ec5fe;
    }

    .tab-konsumen:hover {
        background: rgba(13, 110, 253, 0.05);
    }

    .tab-konsumen.active {
        background: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
    }

    .status-tab .badge-count {
        margin-left: 6px;
        padding: 2px 6px;
        border-radius: 12px;
        background: rgba(0, 0, 0, 0.08);
        font-size: 0.75rem;
        font-weight: 700;
    }

    .status-tab.active .badge-count {
        background: rgba(255, 255, 255, 0.25);
        color: #fff;
    }

    /* Hide scrollbar for clean look but allow scroll */
    .status-tabs-wrapper::-webkit-scrollbar {
        height: 4px;
    }

    .status-tabs-wrapper::-webkit-scrollbar-track {
        background: transparent;
    }

    .status-tabs-wrapper::-webkit-scrollbar-thumb {
        background: #ced4da;
        border-radius: 10px;
    }
</style>

<?php
$fs_view = $filter_status_view ?? 'pending';
$total_semua = (isset($stats['antrian']) ? $stats['antrian'] : 0) + (isset($stats['produksi']) ? $stats['produksi'] : 0) + (isset($stats['selesai']) ? $stats['selesai'] : 0) + (isset($stats['pb']) ? $stats['pb'] : 0);
?>

<div class="d-flex flex-column w-100 gap-2">
    <!-- Top Row: Operator & Metode Filters -->
    <div class="d-flex align-items-center gap-2 flex-wrap" id="categoryTabs">
        <?php if (empty($is_operator_mode)): ?>
            <label for="selectOperator" class="fw-bold mb-0 text-muted small"><i class="fas fa-industry me-1"></i>
                Workcenter:</label>
            <select class="form-select form-select-sm fw-bold border-primary shadow-sm text-dark"
                style="min-width: 200px; width: auto; border-radius: 8px;" id="selectOperator"
                onchange="updateWipFilter('operator', this.value);">
                <option value="all" <?= (isset($active_operator) && $active_operator === 'all') ? 'selected' : '' ?>>-- Semua
                    Workcenter --</option>
                <?php foreach ($tab_operators as $op): ?>
                    <?php if (!empty($op->operator_nama)): ?>
                        <option value="<?= $op->operator_id ?>" <?= (isset($active_operator) && $active_operator == $op->operator_id) ? 'selected' : '' ?>>
                            <?= strtoupper($op->operator_nama) ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        <?php else: ?>
            <div class="text-muted small fw-bold"><i class="fas fa-user-shield me-1"></i> OPERATOR VIEW</div>
        <?php endif; ?>

        <select class="form-select form-select-sm fw-bold border-info shadow-sm text-info-emphasis"
            style="width: auto; border-radius: 8px;" id="selectMetode"
            onchange="updateWipFilter('metode', this.value);">
            <option value="all" <?= (!isset($filter_metode) || $filter_metode === 'all') ? 'selected' : '' ?>>🛒 Semua
                Metode</option>
            <?php if (!empty($metode_list)): ?>
                <?php foreach ($metode_list as $m): ?>
                    <option value="<?= $m->id_jenis_transaksi ?>" <?= (isset($filter_metode) && $filter_metode == $m->id_jenis_transaksi) ? 'selected' : '' ?>>
                        <?= strtoupper($m->nama_jenis_transaksi) ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
            <option value="offline" <?= (isset($filter_metode) && $filter_metode === 'offline') ? 'selected' : '' ?>>🏪
                OFFLINE</option>
        </select>

        <!-- Date Filters & SOP -->
        <div class="d-flex align-items-center gap-1">
            <div class="d-inline-flex align-items-center bg-warning bg-opacity-10 text-dark px-2 py-1 rounded-3 border border-warning shadow-sm me-1 text-nowrap"
                style="font-size: 0.65rem;" data-bs-toggle="tooltip"
                title="SOP: Pastikan tanggal filter sesuai! (Halaman me-refresh otomatis tiap 60 detik)">
                <i class="fas fa-exclamation-triangle text-warning pulse-orange me-1"></i>
                <span class="fw-bold">Jangan Lupa Cek Tgl <i
                        class="fas fa-sync-alt fa-spin ms-1 text-primary"></i></span>
            </div>

            <input type="date" class="form-control form-control-sm fw-bold border-secondary shadow-sm text-secondary"
                style="border-radius: 8px;" id="filterDateStart"
                value="<?= isset($filter_date_start) ? $filter_date_start : '' ?>"
                onchange="updateWipFilter('date_start', this.value);" title="Tanggal Mulai (Berdasarkan Tanggal SPK)">
            <span class="text-muted fw-bold">-</span>
            <input type="date" class="form-control form-control-sm fw-bold border-secondary shadow-sm text-secondary"
                style="border-radius: 8px;" id="filterDateEnd"
                value="<?= isset($filter_date_end) ? $filter_date_end : '' ?>"
                onchange="updateWipFilter('date_end', this.value);" title="Tanggal Sampai (Berdasarkan Tanggal SPK)">
            <?php if ((isset($_GET['date_start']) && !empty($_GET['date_start'])) || (isset($_GET['date_end']) && !empty($_GET['date_end'])) || !isset($_GET['date_start'])): ?>
                <button class="btn btn-sm btn-light border shadow-sm text-danger rounded-circle px-2"
                    onclick="var u=new URL(window.location.href); u.searchParams.set('date_start', ''); u.searchParams.set('date_end', ''); u.searchParams.set('page','1'); window.location.href=u.toString();"
                    title="Tampilkan Semua Tanggal" style="margin-left: 2px;">
                    <i class="fas fa-times"></i>
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bottom Row: Status Tabs -->
    <div class="status-tabs-wrapper mt-1">
        <div class="status-tabs-container">
            <?php if (empty($is_finishing_workcenter)): ?>
            <div class="status-tab tab-semua <?= ($fs_view === 'all_active') ? 'active' : '' ?>"
                onclick="updateWipFilter('status_view', 'all_active')">
                Semua (Proses Aktif) <span class="badge-count"><?= $total_semua ?></span>
            </div>
            <div class="status-tab tab-pending <?= ($fs_view === 'pending' || empty($fs_view)) ? 'active' : '' ?>"
                onclick="updateWipFilter('status_view', 'pending')">
                Belum Di Proses <span class="badge-count"><?= isset($stats['antrian']) ? $stats['antrian'] : 0 ?></span>
            </div>
            <div class="status-tab tab-proses <?= ($fs_view === 'proses') ? 'active' : '' ?>"
                onclick="updateWipFilter('status_view', 'proses')">
                Proses Produksi <span
                    class="badge-count"><?= isset($stats['produksi']) ? $stats['produksi'] : 0 ?></span>
            </div>
            <?php endif; ?>
            <div class="status-tab tab-proses <?= ($fs_view === 'finishing') ? 'active' : '' ?>"
                onclick="updateWipFilter('status_view', 'finishing')">
                Finishing <span class="badge-count"><?= (int) ($stats['finishing'] ?? 0) ?></span>
            </div>
            <div class="status-tab tab-pb <?= ($fs_view === 'pb') ? 'active' : '' ?>"
                onclick="updateWipFilter('status_view', 'pb')">
                Selesai (Barang di PB) <span class="badge-count"><?= isset($stats['pb']) ? $stats['pb'] : 0 ?></span>
            </div>
            <div class="status-tab tab-konsumen <?= ($fs_view === 'konsumen') ? 'active' : '' ?>"
                onclick="updateWipFilter('status_view', 'konsumen')">
                Diserahkan (Konsumen) <span
                    class="badge-count"><?= isset($stats['konsumen']) ? $stats['konsumen'] : 0 ?></span>
            </div>
            <div class="status-tab tab-rijek <?= ($fs_view === 'rijek') ? 'active' : '' ?>"
                onclick="updateWipFilter('status_view', 'rijek')">
                Pengembalian <span class="badge-count"><?= isset($stats['rijek']) ? $stats['rijek'] : 0 ?></span>
            </div>
        </div>
    </div>
</div>
