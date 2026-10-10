<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">
<style>
    .roadmap-card {
        border: none;
        border-radius: 12px;
        transition: transform 0.2s;
    }

    .roadmap-card:hover {
        transform: translateY(-5px);
    }

    .status-badge {
        font-size: 0.7rem;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .status-live {
        background-color: #d1e7dd;
        color: #0f5132;
    }

    .status-progress {
        background-color: #fff3cd;
        color: #664d03;
    }

    .status-planned {
        background-color: #cfe2ff;
        color: #084298;
    }

    .status-idea {
        background-color: #f8f9fa;
        color: #6c757d;
        border: 1px solid #dee2e6;
    }

    .priority-dot {
        width: 10px;
        header height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }

    .p-high {
        background-color: #dc3545;
    }

    .p-medium {
        background-color: #ffc107;
    }

    .p-low {
        background-color: #0d6efd;
    }
</style>

<div class="app-content">
    <?php $this->load->view('layout_admin/header_modul'); ?>

    <div class="container-fluid">
        <!-- Add Button -->
        <div class="d-flex justify-content-end mb-4">
            <button class="btn btn-primary rounded-pill fw-bold px-4" data-bs-toggle="modal"
                data-bs-target="#addFeatureModal">
                <i class="fas fa-plus me-2"></i>Tambah Rencana Fitur
            </button>
        </div>

        <!-- Kanban Board Style Layout -->
        <div class="row g-4 horizontal-scroll">

            <!-- Column 1: Live / Done -->
            <div class="col-md-3">
                <div class="bg-light p-3 rounded-4 h-100 border">
                    <h6 class="fw-bold text-success mb-3 d-flex align-items-center">
                        <i class="fas fa-check-circle me-2"></i> SUDAH LIVE (
                        <?= count($live_list) ?>)
                    </h6>
                    <?php foreach ($live_list as $item): ?>
                        <div class="card roadmap-card shadow-sm mb-3">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-light text-dark border">
                                        <?= $item->category ?>
                                    </span>
                                    <small class="text-muted"><i class="fas fa-clock me-1"></i>
                                        <?= date('d M', strtotime($item->created_at)) ?>
                                    </small>
                                </div>
                                <h6 class="fw-bold mb-1">
                                    <?= $item->feature_name ?>
                                </h6>
                                <p class="text-muted small mb-3">
                                    <?= $item->description ?>
                                </p>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                                    </div>
                                    <span class="ms-2 small fw-bold text-success">100%</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Column 2: In Progress -->
            <div class="col-md-3">
                <div class="bg-light p-3 rounded-4 h-100 border">
                    <h6 class="fw-bold text-warning mb-3 d-flex align-items-center">
                        <i class="fas fa-spinner fa-spin me-2"></i> SEDANG DIKERJAKAN (
                        <?= count($inprogress_list) ?>)
                    </h6>
                    <?php foreach ($inprogress_list as $item): ?>
                        <div class="card roadmap-card shadow-sm mb-3 border-warning border-start border-4">
                            <div class="card-body p-3">
                                <span class="badge bg-warning text-dark mb-2">
                                    <?= $item->category ?>
                                </span>
                                <h6 class="fw-bold mb-1">
                                    <?= $item->feature_name ?>
                                </h6>
                                <p class="text-muted small mb-3">
                                    <?= $item->description ?>
                                </p>
                                <div class="d-flex align-items-center mt-2">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated"
                                            role="progressbar" style="width: <?= $item->progress ?>%"></div>
                                    </div>
                                    <span class="ms-2 small fw-bold text-dark">
                                        <?= $item->progress ?>%
                                    </span>
                                </div>
                                <button class="btn btn-sm btn-outline-dark w-100 mt-2 rounded-3"
                                    onclick="updateStatus(<?= $item->id ?>)">Update Progress</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Column 3: Planned -->
            <div class="col-md-3">
                <div class="bg-light p-3 rounded-4 h-100 border">
                    <h6 class="fw-bold text-primary mb-3 d-flex align-items-center">
                        <i class="fas fa-clipboard-list me-2"></i> RENCANA (
                        <?= count($planned_list) ?>)
                    </h6>
                    <?php foreach ($planned_list as $item): ?>
                        <div class="card roadmap-card shadow-sm mb-3">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between">
                                    <span class="badge bg-primary-subtle text-primary border">
                                        <?= $item->category ?>
                                    </span>
                                    <?php
                                    $p_class = ($item->priority == 'High') ? 'text-danger' :
                                        (($item->priority == 'Medium') ? 'text-warning' : 'text-primary');
                                    ?>
                                    <span class="small fw-bold <?= $p_class ?>">
                                        <?= $item->priority ?> Pr.
                                    </span>
                                </div>
                                <h6 class="fw-bold mt-2 mb-1">
                                    <?= $item->feature_name ?>
                                </h6>
                                <p class="text-muted small mb-0">
                                    <?= $item->description ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Column 4: Ideas -->
            <div class="col-md-3">
                <div class="bg-light p-3 rounded-4 h-100 border">
                    <h6 class="fw-bold text-secondary mb-3 d-flex align-items-center">
                        <i class="fas fa-lightbulb me-2"></i> IDE BARU (
                        <?= count($idea_list) ?>)
                    </h6>
                    <?php foreach ($idea_list as $item): ?>
                        <div class="card roadmap-card shadow-sm mb-3 border-secondary border-dashed">
                            <div class="card-body p-3">
                                <h6 class="fw-bold text-muted mb-1">
                                    <?= $item->feature_name ?>
                                </h6>
                                <p class="text-muted small mb-0 fst-italic">"
                                    <?= $item->description ?>"
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Add -->
<div class="modal fade" id="addFeatureModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Rencana Fitur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('roadmap/add') ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Fitur</label>
                        <input type="text" name="feature_name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="category" class="form-select">
                                <option>Communication</option>
                                <option>Reporting</option>
                                <option>Production</option>
                                <option>Inventory</option>
                                <option>HR</option>
                                <option>Customer</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prioritas</label>
                            <select name="priority" class="form-select">
                                <option value="High">Tinggi (High)</option>
                                <option value="Medium">Sedang (Medium)</option>
                                <option value="Low">Rendah (Low)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="Idea">Ide (Idea)</option>
                            <option value="Planned">Rencana (Planned)</option>
                            <option value="In-Progress">Sedang Dikerjakan</option>
                            <option value="Live">Selesai (Live)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi Singkat</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function updateStatus(id) {
        // Simple prompt for demo - in production use a better modal
        let newProgress = prompt("Masukkan Progress Terbaru (0-100):");
        if (newProgress !== null) {
            let status = 'In-Progress';
            if (newProgress == 100) status = 'Live';
            
            $.ajax({
                url: '<?= base_url('roadmap/update_status') ?>',
                type: 'POST',
                data: {
                    id: id,
                    progress: newProgress,
                    status: status
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'success') {
                        location.reload();
                    }
                }
            });
        }
    }
</script>