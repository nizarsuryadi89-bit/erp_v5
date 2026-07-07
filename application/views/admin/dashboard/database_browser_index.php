<?php $this->load->view('layout_admin/header_modul'); ?>

<div class="app-content mt-n4 pb-5">
    <div class="container-fluid">
        <!-- Header -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body d-flex justify-content-between align-items-center py-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0 font-monospace"><i
                            class="fas fa-database text-primary me-2"></i>Full Database Index</h5>
                    <small class="text-muted">Total Size: <strong>
                            <?= $total_db_size ?> MB
                        </strong></small>
                </div>
                <div>
                    <input type="text" id="tableSearch" class="form-control form-control-sm"
                        placeholder="Search tables..." style="width: 250px;">
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= site_url('dashboard/developer_portal') ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Portal
                    </a>
                </div>
            </div>
        </div>

        <!-- Tables List -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="dbTable">
                        <thead class="bg-light text-uppercase small fw-bold text-muted sticky-top">
                            <tr>
                                <th class="ps-4">Table Name</th>
                                <th class="text-end">Rows</th>
                                <th class="text-end pe-4">Size (MB)</th>
                                <th class="text-end pe-4" style="width: 100px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tables as $tbl): ?>
                                <tr class="table-row">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-table text-muted me-3 opacity-50"></i>
                                            <span class="fw-bold text-dark font-monospace table-name">
                                                <?= $tbl->table_name ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-end font-monospace text-muted">
                                        <?= number_format($tbl->table_rows) ?>
                                    </td>
                                    <td class="text-end font-monospace text-primary pe-4">
                                        <?= $tbl->size_mb ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="<?= site_url('dashboard/view_table_data?table=' . $tbl->table_name) ?>"
                                            class="btn btn-xs btn-outline-primary rounded-pill px-3">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('tableSearch').addEventListener('keyup', function () {
        let filter = this.value.toUpperCase();
        let rows = document.querySelectorAll('#dbTable .table-row');

        rows.forEach(row => {
            let name = row.querySelector('.table-name').textContent.toUpperCase();
            if (name.indexOf(filter) > -1) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });
</script>