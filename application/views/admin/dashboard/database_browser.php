<?php $this->load->view('layout_admin/header_modul'); ?>

<div class="app-content mt-n4 pb-5">
    <div class="container-fluid">
        <!-- Header -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body d-flex justify-content-between align-items-center py-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0 font-monospace"><i class="fas fa-table text-primary me-2"></i>
                        <?= $table_name ?>
                    </h5>
                    <small class="text-muted">Total Rows: <strong>
                            <?= number_format($total_rows) ?>
                        </strong></small>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= site_url('dashboard/developer_portal') ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Portal
                    </a>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 70vh;">
                    <table class="table table-bordered table-striped table-hover table-sm mb-0 font-monospace small">
                        <thead class="bg-light sticky-top" style="z-index: 10;">
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <?php foreach ($columns as $col): ?>
                                    <th>
                                        <?= $col ?>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($rows)): ?>
                                <tr>
                                    <td colspan="<?= count($columns) + 1 ?>" class="text-center py-5 text-muted">
                                        <i class="fas fa-box-open fa-3x mb-3 opacity-50"></i><br>
                                        Table is empty.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $i = $offset + 1;
                                foreach ($rows as $row): ?>
                                    <tr>
                                        <td class="text-center text-muted">
                                            <?= $i++ ?>
                                        </td>
                                        <?php foreach ($columns as $col): ?>
                                            <td class="text-truncate" style="max-width: 200px;"
                                                title="<?= htmlspecialchars($row[$col]) ?>">
                                                <?= htmlspecialchars(mb_strimwidth($row[$col], 0, 50, "...")) ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                <span class="text-muted small">Showing
                    <?= $offset + 1 ?> to
                    <?= min($offset + $limit, $total_rows) ?> of
                    <?= $total_rows ?>
                </span>

                <div class="btn-group">
                    <?php if ($offset > 0): ?>
                        <a href="<?= site_url('dashboard/view_table_data?table=' . $table_name . '&offset=' . max(0, $offset - $limit)) ?>"
                            class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-chevron-left"></i> Prev
                        </a>
                    <?php else: ?>
                        <button class="btn btn-sm btn-outline-secondary" disabled><i class="fas fa-chevron-left"></i>
                            Prev</button>
                    <?php endif; ?>

                    <?php if (($offset + $limit) < $total_rows): ?>
                        <a href="<?= site_url('dashboard/view_table_data?table=' . $table_name . '&offset=' . ($offset + $limit)) ?>"
                            class="btn btn-sm btn-outline-primary">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php else: ?>
                        <button class="btn btn-sm btn-outline-secondary" disabled>Next <i
                                class="fas fa-chevron-right"></i></button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>