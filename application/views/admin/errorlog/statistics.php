<div class="container-fluid px-4">
    <!-- Header & Description -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1"><i class="bi bi-graph-up"></i> Error Log Statistics</h2>
                    <p class="text-muted mb-0">Comprehensive analysis and statistics of application error logs.</p>
                </div>
                <div class="btn-group">
                    <a href="<?= base_url('errorlog') ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Logs
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
                            <h4 class="mb-0"><?= $stats['total_errors'] ?></h4>
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
                            <h4 class="mb-0"><?= isset($stats['errors_by_severity']['error']) ? $stats['errors_by_severity']['error'] : 0 ?></h4>
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
                            <h4 class="mb-0"><?= isset($stats['errors_by_severity']['warning']) ? $stats['errors_by_severity']['warning'] : 0 ?></h4>
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
                            <h4 class="mb-0"><?= count($stats['recent_errors']) ?></h4>
                            <small>Recent Errors (7 days)</small>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-clock-history fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Errors by Severity Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-pie-chart"></i> Errors by Severity</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($stats['errors_by_severity'])): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-check-circle text-success fs-1"></i>
                            <p class="mt-3">No errors to display</p>
                        </div>
                    <?php else: ?>
                        <canvas id="severityChart" width="400" height="200"></canvas>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Errors by Date Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-bar-chart"></i> Errors by Date (Last 30 Days)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($stats['errors_by_date'])): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-check-circle text-success fs-1"></i>
                            <p class="mt-3">No errors to display</p>
                        </div>
                    <?php else: ?>
                        <canvas id="dateChart" width="400" height="200"></canvas>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Errors Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="bi bi-clock-history"></i> Recent Errors (Last 7 Days)</h5>
        </div>
        <div class="card-body">
            <?php if (empty($stats['recent_errors'])): ?>
                <div class="text-center py-4">
                    <i class="bi bi-check-circle text-success fs-1"></i>
                    <h5 class="mt-3">No recent errors</h5>
                    <p class="text-muted">Great! No errors have been logged in the last 7 days.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Date/Time</th>
                                <th>Severity</th>
                                <th>Message</th>
                                <th>File</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($stats['recent_errors'], 0, 10) as $log): ?>
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
                                        <div class="text-truncate" style="max-width: 400px;" title="<?= htmlspecialchars($log['message']) ?>">
                                            <?= htmlspecialchars($log['message']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= $log['filename'] ?>
                                        </small>
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

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Errors by Severity Chart
    <?php if (!empty($stats['errors_by_severity'])): ?>
    const severityCtx = document.getElementById('severityChart').getContext('2d');
    const severityChart = new Chart(severityCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_keys($stats['errors_by_severity'])) ?>,
            datasets: [{
                data: <?= json_encode(array_values($stats['errors_by_severity'])) ?>,
                backgroundColor: [
                    '#dc3545', // Error - Red
                    '#ffc107', // Warning - Yellow
                    '#17a2b8', // Info - Blue
                    '#6c757d'  // Debug - Gray
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    <?php endif; ?>

    // Errors by Date Chart
    <?php if (!empty($stats['errors_by_date'])): ?>
    const dateCtx = document.getElementById('dateChart').getContext('2d');
    
    // Get last 30 days data
    const last30Days = [];
    const last30DaysData = [];
    
    for (let i = 29; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        const dateStr = date.toISOString().split('T')[0];
        last30Days.push(dateStr);
        last30DaysData.push(<?= json_encode($stats['errors_by_date']) ?>[dateStr] || 0);
    }
    
    const dateChart = new Chart(dateCtx, {
        type: 'line',
        data: {
            labels: last30Days,
            datasets: [{
                label: 'Errors',
                data: last30DaysData,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
    <?php endif; ?>
});
</script>
