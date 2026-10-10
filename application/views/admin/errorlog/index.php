<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    /* ==========================================================================
    PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - LOG MONITOR
    ========================================================================== */
    :root {
        --primary-gradient: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        --danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        --info-gradient: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    /* Summary Stat Cards */
    .summary-stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
    }

    .summary-stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--premium-shadow);
    }

    .stat-v2-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .stat-v2-label {
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.6rem;
        letter-spacing: 1px;
    }

    .stat-v2-value {
        font-size: 1.5rem;
        font-weight: 900;
        color: #1a1c23;
        line-height: 1;
    }

    /* Tables */
    .report-section-card {
        background: white;
        border-radius: 24px;
        border: none;
        box-shadow: var(--premium-shadow);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .section-header {
        background: var(--primary-gradient);
        padding: 1.25rem 1.75rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
    }

    .section-header h5 {
        margin: 0;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .premium-table thead th {
        background: #f8fafc;
        border: none;
        color: #475569;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.65rem;
        letter-spacing: 1px;
        padding: 1.25rem 1.5rem;
    }

    .premium-table tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.85rem;
    }

    /* Log Message Stylings */
    .log-msg {
        font-family: 'JetBrains Mono', 'Fira Code', monospace;
        font-size: 0.75rem;
        max-width: 450px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: #334155;
    }

    .badge-severity {
        padding: 0.4rem 0.75rem;
        border-radius: 6px;
        font-weight: 800;
        font-size: 0.6rem;
        letter-spacing: 0.5px;
    }

    /* Action Buttons */
    .btn-action-premium {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        border: none;
        margin: 0 2px;
    }

    .btn-view-p {
        background: #e0f2fe;
        color: #0369a1;
    }

    .btn-dl-p {
        background: #f1f5f9;
        color: #475569;
    }

    .btn-view-p:hover {
        background: #bae6fd;
        transform: translateY(-2px);
    }

    .btn-dl-p:hover {
        background: #e2e8f0;
        transform: translateY(-2px);
    }

    /* Animations */
    @keyframes slideUpFade {
        from {
            opacity: 0;
            transform: translateY(15px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-up {
        animation: slideUpFade 0.5s ease-out forwards;
    }
</style>

<div class="app-content mt-2">
    <div class="container-fluid">
        <!-- Search & Filter Bar -->
        <div class="report-section-card p-4 mb-4 animate-up">
            <form method="GET" action="<?= base_url('ErrorLog') ?>" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Filter Tanggal</label>
                    <input type="date" name="date" class="form-control rounded-3 border-2"
                        value="<?= $filters['date'] ?? '' ?>" style="background: #f8fafc;">
                </div>
                <div class="col-md-3">
                    <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Log Severity</label>
                    <select name="severity" class="form-select rounded-3 border-2" style="background: #f8fafc;">
                        <option value="">All Severities</option>
                        <option value="error" <?= ($filters['severity'] === 'error') ? 'selected' : '' ?>>ERROR (Critical)
                        </option>
                        <option value="warning" <?= ($filters['severity'] === 'warning') ? 'selected' : '' ?>>WARNING
                            (Alert)</option>
                        <option value="info" <?= ($filters['severity'] === 'info') ? 'selected' : '' ?>>INFO (General)
                        </option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Cari Pesan Error</label>
                    <input type="text" name="search" class="form-control rounded-3 border-2"
                        placeholder="Search keywords..." value="<?= $filters['search'] ?? '' ?>"
                        style="background: #f8fafc;">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold shadow-sm"
                        style="background: var(--primary-gradient); padding: 0.6rem 0;">
                        <i class="fas fa-search me-1"></i> Apply Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Pulse Summary Stats -->
        <div class="row g-3 mb-4 animate-up" style="animation-delay: 0.1s">
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--primary-gradient)"><i
                            class="fas fa-microchip"></i></div>
                    <div>
                        <div class="stat-v2-label">Total Events</div>
                        <div class="stat-v2-value"><?= number_format($total_logs) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--danger-gradient)"><i
                            class="fas fa-shield-virus"></i></div>
                    <div>
                        <div class="stat-v2-label">Critical Alerts</div>
                        <div class="stat-v2-value">
                            <?= count(array_filter($logs, function ($log) {
                                return $log['severity'] === 'error';
                            })) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--warning-gradient)"><i
                            class="fas fa-triangle-exclamation"></i></div>
                    <div>
                        <div class="stat-v2-label">Warnings</div>
                        <div class="stat-v2-value">
                            <?= count(array_filter($logs, function ($log) {
                                return $log['severity'] === 'warning';
                            })) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm">
                    <div class="stat-v2-icon text-white" style="background: var(--success-gradient)"><i
                            class="fas fa-file-waveform"></i></div>
                    <div>
                        <div class="stat-v2-label">Log Files Swapped</div>
                        <div class="stat-v2-value"><?= count(array_unique(array_column($logs, 'filename'))) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Log List -->
        <div class="report-section-card animate-up" style="animation-delay: 0.2s">
            <div class="section-header">
                <h5><i class="fas fa-terminal"></i> System Activity Stream</h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-danger rounded-pill px-4 fw-bold shadow-sm"
                        data-bs-toggle="modal" data-bs-target="#clearLogsModal">
                        <i class="fas fa-trash-alt me-1"></i> Flush Logs
                    </button>
                    <a href="<?= base_url('errorlog/statistics') ?>"
                        class="btn btn-sm btn-white text-dark rounded-pill px-3 fw-bold shadow-sm">
                        <i class="fas fa-chart-line me-1"></i> Insights
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table premium-table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="150">Timestamp</th>
                            <th width="110">Dampak</th>
                            <th>Analisa & Solusi (Bahasa Indonesia)</th>
                            <th width="200">Teknis (Asli)</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="5" class="py-5 text-center">
                                    <div class="opacity-50">
                                        <i class="fas fa-check-double fa-3x mb-3 text-success"></i>
                                        <h6 class="fw-bold">No issues detected in current view.</h6>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                                <?php
                                $impact_class = 'bg-secondary text-white';
                                $icon = 'fa-info-circle';
                                switch (strtolower($log['severity'])) {
                                    case 'error':
                                        $impact_class = 'bg-danger text-white';
                                        $icon = 'fa-bug';
                                        break;
                                    case 'warning':
                                        $impact_class = 'bg-warning text-dark';
                                        $icon = 'fa-triangle-exclamation';
                                        break;
                                    case 'info':
                                        $impact_class = 'bg-info text-white';
                                        $icon = 'fa-circle-info';
                                        break;
                                }
                                ?>
                                <tr>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark mb-1" style="font-size: 0.75rem;"><i
                                                class="far fa-clock me-1 text-muted"></i>
                                            <?= date('H:i:s', strtotime($log['datetime'])) ?></div>
                                        <div class="small text-muted" style="font-size: 0.65rem;">
                                            <?= date('d M Y', strtotime($log['datetime'])) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $simple_lvl = $log['simple_level'] ?? 'INFO';
                                        $impact_class = 'bg-secondary';
                                        if ($simple_lvl == 'KRITIS')
                                            $impact_class = 'bg-danger';
                                        if ($simple_lvl == 'RINGAN')
                                            $impact_class = 'bg-warning text-dark';
                                        if ($simple_lvl == 'INFO')
                                            $impact_class = 'bg-info';
                                        ?>
                                        <span
                                            class="badge-severity <?= $impact_class ?> d-inline-flex align-items-center gap-1">
                                            <i class="fas <?= $icon ?>"></i> <?= $simple_lvl ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-900 text-dark mb-1" style="font-size: 0.85rem;">
                                            <?= $log['indo_message'] ?></div>
                                        <div class="small text-primary fw-bold"><i class="fas fa-lightbulb me-1"></i>Solusi:
                                            <?= $log['solution'] ?></div>
                                    </td>
                                    <td>
                                        <div class="log-msg mb-1" title="<?= htmlspecialchars($log['message']) ?>"
                                            style="max-width: 200px;">
                                            <?= htmlspecialchars($log['message']) ?>
                                        </div>
                                        <code class="small text-muted" style="font-size: 0.6rem;"><?= $log['filename'] ?></code>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center">
                                            <button type="button" class="btn-action-premium btn-view-p" data-bs-toggle="modal"
                                                data-bs-target="#logDetailModal" data-datetime="<?= $log['datetime'] ?>"
                                                data-severity="<?= $log['simple_level'] ?>"
                                                data-message="<?= htmlspecialchars($log['indo_message']) ?>"
                                                data-solution="<?= htmlspecialchars($log['solution']) ?>"
                                                data-filename="<?= $log['filename'] ?>"
                                                data-full-line="<?= htmlspecialchars($log['full_line']) ?>">
                                                <i class="fas fa-search-plus"></i>
                                            </button>
                                            <a href="<?= base_url('errorlog/download/' . $log['filename']) ?>"
                                                class="btn-action-premium btn-dl-p">
                                                <i class="fas fa-file-download"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($pagination): ?>
                <div class="p-4 bg-light border-top">
                    <?= $pagination ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Log Detail -->
<div class="modal fade" id="logDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-0 bg-dark text-white p-4">
                <h5 class="modal-title fw-900"><i class="fas fa-bug-slash me-2"></i>Analisa Kerusakan Sistem</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <label class="small text-uppercase fw-bold text-muted mb-1 d-block">Waktu Kejadian</label>
                        <div id="modal-datetime" class="fw-bold text-dark bg-white p-2 rounded border"></div>
                    </div>
                    <div class="col-6">
                        <label class="small text-uppercase fw-bold text-muted mb-1 d-block">Tingkat Dampak</label>
                        <div id="modal-severity" class="fw-bold text-dark bg-white p-2 rounded border text-uppercase">
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="small text-uppercase fw-bold text-muted mb-1 d-block">Analisa Masalah (Bahasa
                        Indonesia)</label>
                    <div id="modal-message" class="bg-white p-3 rounded border fw-900 fs-5 text-dark"
                        style="border-left: 4px solid #3b82f6 !important;">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="small text-uppercase fw-bold text-muted mb-1 d-block">Saran Tindakan / Solusi</label>
                    <div id="modal-solution"
                        class="bg-success bg-opacity-10 text-success p-3 rounded border border-success border-opacity-25 fw-bold">
                    </div>
                </div>
                <div class="mb-0">
                    <label class="small text-uppercase fw-bold text-muted mb-1 d-block">Dokumentasi Teknis (Raw
                        Error)</label>
                    <div id="modal-full-line" class="bg-dark text-success p-3 rounded font-monospace"
                        style="font-size: 0.7rem; white-space: pre-wrap; word-break: break-all;"></div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0 bg-light">
                <button type="button" class="btn btn-dark rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Tutup
                    Analisa</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Flush Logs -->
<div class="modal fade" id="clearLogsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 bg-danger text-white p-4">
                <h5 class="modal-title fw-900"><i class="fas fa-bolt me-2"></i>Flush All Logs</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="fw-bold text-dark">Warning: This operation will permanently wipe all system log archives from
                    the storage.</p>
                <div class="alert alert-danger-subtle border-danger rounded-3 small">
                    <i class="fas fa-exclamation-triangle me-2"></i> System performance might slightly increase, but
                    diagnostic history will be lost.
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <form method="POST" action="<?= base_url('ErrorLog/clear') ?>" class="d-inline">
                    <input type="hidden" name="confirm" value="yes">
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Execute Flush</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const logModal = document.getElementById('logDetailModal');
        if (logModal) {
            logModal.addEventListener('show.bs.modal', function (event) {
                const btn = event.relatedTarget;
                document.getElementById('modal-datetime').textContent = btn.getAttribute('data-datetime');
                document.getElementById('modal-severity').textContent = btn.getAttribute('data-severity');
                document.getElementById('modal-message').textContent = btn.getAttribute('data-message');
                document.getElementById('modal-solution').textContent = btn.getAttribute('data-solution');
                document.getElementById('modal-full-line').textContent = btn.getAttribute('data-full-line');
            });
        }
    });
</script>