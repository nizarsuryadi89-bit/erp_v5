<?php
/**
 * Laporan Kinerja Operator / Divisi Produksi
 */
?>

<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">

<style>
    :root {
        --fin-primary: #1e293b;
        --fin-accent: #3b82f6;
        --fin-success: #10b981;
        --fin-warning: #f59e0b;
        --fin-danger: #ef4444;
        --fin-bg: #f8fafc;
        --card-radius: 20px;
    }

    .app-content {
        background-color: var(--fin-bg);
        min-height: 100vh;
        font-family: 'Inter', sans-serif;
    }

    .filter-hud {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        margin-bottom: 2rem;
    }

    .filter-hud .form-select,
    .filter-hud .form-control {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 0.6rem 1rem;
        font-weight: 700;
        font-size: 0.85rem;
    }

    .stat-card {
        background: white;
        border-radius: var(--card-radius);
        padding: 1.5rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 150px;
        height: 150px;
        background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0));
        border-radius: 50%;
        transform: translate(30%, -30%);
        pointer-events: none;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .content-card {
        background: white;
        border-radius: var(--card-radius);
        padding: 2rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    }

    .table-modern {
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table-modern th {
        border-top: none;
        border-bottom: 2px solid #e2e8f0;
        color: #64748b;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 1rem;
    }
    
    .table-modern td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.85rem;
        font-weight: 600;
        color: #334155;
    }
    
    .progress-bar-modern {
        height: 8px;
        border-radius: 10px;
        background-color: #e2e8f0;
        overflow: hidden;
        display: flex;
    }

    .progress-modern-segment {
        height: 100%;
    }
</style>

<div class="app-content mt-3 pb-5">
    <?php $this->load->view('layout_admin/header_modul'); ?>
    <div class="content-header pt-4 pb-2">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-8">
                    <h1 class="m-0 fw-900 text-dark" style="font-size: 1.75rem; letter-spacing: -1px;">
                        Kinerja Operator / Divisi Produksi
                    </h1>
                    <p class="text-muted fw-bold mb-0 mt-1" style="font-size: 0.9rem;">
                        Pantau produktivitas dan beban kerja setiap divisi produksi.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <!-- Filter HUD -->
            <form method="GET" action="<?= site_url('wip/kinerja_operator') ?>" class="filter-hud">
                <div class="row align-items-end g-3">
                    <div class="col-md-3">
                        <label class="form-label text-muted fw-bold small text-uppercase mb-1">Tgl Awal</label>
                        <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted fw-bold small text-uppercase mb-1">Tgl Akhir</label>
                        <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100 fw-bold rounded-3" style="padding: 0.6rem;">
                            <i class="fas fa-filter me-2"></i> Terapkan Filter
                        </button>
                    </div>
                    <div class="col-md-3">
                        <a href="<?= site_url('wip/kinerja_operator') ?>" class="btn btn-light border w-100 fw-bold rounded-3" style="padding: 0.6rem;">
                            <i class="fas fa-sync me-2"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <?php
            $total_tugas_all = 0;
            $total_selesai_all = 0;
            $total_proses_all = 0;
            $total_belum_all = 0;

            foreach($kinerja as $k) {
                $total_tugas_all += $k['total_tugas'];
                $total_selesai_all += $k['selesai'];
                $total_proses_all += $k['sedang_dikerjakan'];
                $total_belum_all += $k['belum_dikerjakan'];
            }
            ?>

            <!-- Top Stats -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="stat-card" style="border-left: 4px solid var(--fin-accent);">
                        <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: var(--fin-accent);">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <p class="text-muted fw-bold small text-uppercase mb-1">Total Tugas</p>
                        <h3 class="fw-900 text-dark mb-0"><?= number_format($total_tugas_all) ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="border-left: 4px solid var(--fin-success);">
                        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--fin-success);">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <p class="text-muted fw-bold small text-uppercase mb-1">Selesai (Done)</p>
                        <h3 class="fw-900 text-dark mb-0"><?= number_format($total_selesai_all) ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="border-left: 4px solid var(--fin-warning);">
                        <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: var(--fin-warning);">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                        <p class="text-muted fw-bold small text-uppercase mb-1">Sedang Dikerjakan</p>
                        <h3 class="fw-900 text-dark mb-0"><?= number_format($total_proses_all) ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="border-left: 4px solid var(--fin-danger);">
                        <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1); color: var(--fin-danger);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <p class="text-muted fw-bold small text-uppercase mb-1">Belum Dikerjakan</p>
                        <h3 class="fw-900 text-dark mb-0"><?= number_format($total_belum_all) ?></h3>
                    </div>
                </div>
            </div>

            <!-- Main Table -->
            <div class="content-card">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                    <h5 class="fw-900 text-dark mb-0"><i class="fas fa-users text-primary me-2"></i> Rincian Per Divisi</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-modern table-hover mb-0">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="25%">Nama Divisi / Operator</th>
                                <th class="text-center">Belum Dikerjakan</th>
                                <th class="text-center">Sedang Proses</th>
                                <th class="text-center">Selesai</th>
                                <th class="text-center">Total Tugas</th>
                                <th width="20%">Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($kinerja)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Tidak ada data kinerja pada rentang tanggal ini.</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($kinerja as $row): 
                                    $t_tugas = $row['total_tugas'] > 0 ? $row['total_tugas'] : 1; // prevent div by zero
                                    $pct_selesai = ($row['selesai'] / $t_tugas) * 100;
                                    $pct_proses = ($row['sedang_dikerjakan'] / $t_tugas) * 100;
                                    $pct_belum = ($row['belum_dikerjakan'] / $t_tugas) * 100;
                                ?>
                                    <tr>
                                        <td class="text-muted fw-bold"><?= $no++ ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded p-2 me-3" style="background: rgba(59, 130, 246, 0.1); color: var(--fin-accent);">
                                                    <i class="fas fa-user-cog"></i>
                                                </div>
                                                <span class="fw-bold fs-6 text-dark"><?= htmlspecialchars($row['operator_nama']) ?></span>
                                            </div>
                                        </td>
                                        <td class="text-center fw-bold">
                                            <span class="badge bg-danger-subtle text-danger px-2 py-1 fs-7"><?= number_format($row['belum_dikerjakan']) ?></span>
                                        </td>
                                        <td class="text-center fw-bold">
                                            <span class="badge bg-warning-subtle text-warning px-2 py-1 fs-7"><?= number_format($row['sedang_dikerjakan']) ?></span>
                                        </td>
                                        <td class="text-center fw-bold">
                                            <span class="badge bg-success-subtle text-success px-2 py-1 fs-7"><?= number_format($row['selesai']) ?></span>
                                        </td>
                                        <td class="text-center fw-900 fs-6">
                                            <?= number_format($row['total_tugas']) ?>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-between mb-1" style="font-size: 0.7rem; font-weight: 800;">
                                                <span class="text-success"><?= number_format($pct_selesai, 1) ?>% Selesai</span>
                                                <span class="text-muted"><?= number_format($row['total_tugas']) ?></span>
                                            </div>
                                            <div class="progress-bar-modern">
                                                <div class="progress-modern-segment bg-success" style="width: <?= $pct_selesai ?>%;" title="Selesai: <?= $row['selesai'] ?>"></div>
                                                <div class="progress-modern-segment bg-warning" style="width: <?= $pct_proses ?>%;" title="Proses: <?= $row['sedang_dikerjakan'] ?>"></div>
                                                <div class="progress-modern-segment bg-danger" style="width: <?= $pct_belum ?>%;" title="Belum: <?= $row['belum_dikerjakan'] ?>"></div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
</div>
