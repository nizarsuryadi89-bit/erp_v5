<div class="container-fluid px-4">
    <!-- Header & Description -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1"><i class="bi bi-file-text"></i> Error Log Details</h2>
                    <p class="text-muted mb-0">Detailed view of error log: <?= $filename ?></p>
                </div>
                <div class="btn-group">
                    <a href="<?= base_url('errorlog') ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Logs
                    </a>
                    <a href="<?= base_url('errorlog/download/' . $filename) ?>" class="btn btn-primary">
                        <i class="bi bi-download"></i> Download
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= $total_errors ?></h4>
                            <small>Total Errors</small>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-exclamation-triangle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= count(array_filter($logs, function($log) { return $log['severity'] === 'error'; })) ?></h4>
                            <small>Critical Errors</small>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-exclamation-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= count(array_filter($logs, function($log) { return $log['severity'] === 'warning'; })) ?></h4>
                            <small>Warnings</small>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-exclamation-triangle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= count(array_filter($logs, function($log) { return $log['severity'] === 'info'; })) ?></h4>
                            <small>Info</small>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-info-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Logs Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-list-ul"></i> Error Logs - <?= $filename ?>
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($logs)): ?>
                <div class="text-center py-4">
                    <i class="bi bi-check-circle text-success fs-1"></i>
                    <h5 class="mt-3">No errors found</h5>
                    <p class="text-muted">This log file contains no errors.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Date/Time</th>
                                <th>Severity</th>
                                <th>Message</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td>
                                        <small class="text-muted">
                                            <?= date('Y-m-d H:i:s', strtotime($log['datetime'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php
                                        $severity_class = 'bg-secondary';
                                        switch (strtolower($log['severity'])) {
                                            case 'error':
                                                $severity_class = 'bg-danger';
                                                break;
                                            case 'warning':
                                                $severity_class = 'bg-warning text-dark';
                                                break;
                                            case 'info':
                                                $severity_class = 'bg-info';
                                                break;
                                            case 'debug':
                                                $severity_class = 'bg-secondary';
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?= $severity_class ?>">
                                            <?= ucfirst($log['severity']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 500px;" title="<?= htmlspecialchars($log['message']) ?>">
                                            <?= htmlspecialchars($log['message']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#logDetailModal" 
                                                data-datetime="<?= $log['datetime'] ?>" 
                                                data-severity="<?= $log['severity'] ?>" 
                                                data-message="<?= htmlspecialchars($log['message']) ?>" 
                                                data-filename="<?= $log['filename'] ?>"
                                                data-full-line="<?= htmlspecialchars($log['full_line']) ?>">
                                            <i class="bi bi-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Log Detail Modal -->
<div class="modal fade" id="logDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Error Log Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Date/Time:</strong>
                        <p id="modal-datetime"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Severity:</strong>
                        <p id="modal-severity"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <strong>File:</strong>
                        <p id="modal-filename"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <strong>Message:</strong>
                        <pre id="modal-message" class="bg-light p-3 rounded"></pre>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <strong>Full Log Line:</strong>
                        <pre id="modal-full-line" class="bg-light p-3 rounded"></pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Log Detail Modal
document.addEventListener('DOMContentLoaded', function() {
    const logDetailModal = document.getElementById('logDetailModal');
    if (logDetailModal) {
        logDetailModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const datetime = button.getAttribute('data-datetime');
            const severity = button.getAttribute('data-severity');
            const message = button.getAttribute('data-message');
            const filename = button.getAttribute('data-filename');
            const fullLine = button.getAttribute('data-full-line');

            document.getElementById('modal-datetime').textContent = datetime;
            document.getElementById('modal-severity').textContent = severity;
            document.getElementById('modal-filename').textContent = filename;
            document.getElementById('modal-message').textContent = message;
            document.getElementById('modal-full-line').textContent = fullLine;
        });
    }
});
</script>
