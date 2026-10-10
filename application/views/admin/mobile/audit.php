<div class="row g-3">
    <!-- Log Filter -->
    <div class="col-12 mt-2">
        <div class="alert alert-info border-0 rounded-4 shadow-sm d-flex align-items-center mb-4">
            <i class="bi bi-shield-check fs-1 me-3 text-primary"></i>
            <div>
                <h6 class="fw-bold mb-1">Audit Trail System</h6>
                <div class="small opacity-75">Memantau 20 aktivitas sensitif terakhir untuk keamanan data.</div>
            </div>
        </div>
    </div>

    <!-- Timeline Logs -->
    <div class="col-12">
        <div class="timeline p-2">
            <?php foreach ($logs as $log):
                // Determine icon & color based on action
                $icon = 'bi-circle';
                $color = 'text-secondary';
                $bg = 'bg-light';

                if (strpos($log->action_type, 'DELETE') !== false) {
                    $icon = 'bi-trash-fill';
                    $color = 'text-danger';
                    $bg = 'bg-danger-subtle';
                } elseif (strpos($log->action_type, 'UPDATE') !== false || strpos($log->action_type, 'EDIT') !== false) {
                    $icon = 'bi-pencil-fill';
                    $color = 'text-warning';
                    $bg = 'bg-warning-subtle';
                } elseif (strpos($log->action_type, 'LOGIN') !== false) {
                    $icon = 'bi-door-open-fill';
                    $color = 'text-success';
                    $bg = 'bg-success-subtle';
                }
                ?>

                <div class="d-flex mb-4 position-relative">
                    <!-- Line -->
                    <div class="position-absolute top-0 bottom-0 start-0 border-start border-2 border-light ms-3"
                        style="z-index: 0;"></div>

                    <!-- Icon -->
                    <div class="flex-shrink-0 position-relative" style="z-index: 1;">
                        <div class="rounded-circle <?= $bg ?> d-flex align-items-center justify-content-center border border-white shadow-sm"
                            style="width: 36px; height: 36px;">
                            <i class="<?= $icon ?> <?= $color ?>"></i>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="flex-grow-1 ms-3">
                        <div class="card border-0 shadow-sm" style="border-radius: 12px; border-top-left-radius: 4px;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-light text-dark border">
                                        <?= $log->username ?>
                                    </span>
                                    <small class="text-muted" style="font-size: 0.7rem;"><i class="bi bi-clock me-1"></i>
                                        <?= date('d M H:i', strtotime($log->created_at)) ?>
                                    </small>
                                </div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.9rem;">
                                    <?= $log->action_type ?>
                                </h6>
                                <p class="mb-0 text-muted small lh-sm">
                                    <?= $log->description ?>
                                </p>
                                <div class="mt-2 text-end">
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill"
                                        style="font-size: 0.6rem;">IP:
                                        <?= $log->ip_address ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($logs)): ?>
                <div class="text-center py-5">
                    <img src="<?= base_url('assets/img/empty.svg') ?>" class="img-fluid mb-3 w-25 opacity-50">
                    <p class="text-muted">Belum ada log aktivitas tercatat.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>