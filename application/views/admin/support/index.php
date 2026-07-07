<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-ticket-perforated me-2"></i> Support Ticket System
                        </h5>
                        <p class="text-muted small mb-0">Laporkan BUG, ajukan Fitur Baru, atau minta bantuan teknis</p>
                    </div>
                    <a href="<?= base_url('support/create') ?>" class="btn btn-primary shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i> Buat Ticket Baru
                    </a>
                </div>
                <div class="card-body">
                    <?php if ($this->session->flashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= $this->session->flashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="ticketTable">
                            <thead class="bg-light">
                                <tr>
                                    <th># Ticket</th>
                                    <th>Subject</th>
                                    <th>Modul</th>
                                    <?php if ($is_admin): ?>
                                        <th>User</th>
                                    <?php endif; ?>
                                    <th>Kategori</th>
                                    <th>Prioritas</th>
                                    <th>Status</th>
                                    <th>Dibuat</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tickets as $t):
                                    $status_badge = 'bg-secondary';
                                    if ($t->status == 'OPEN')
                                        $status_badge = 'bg-info';
                                    elseif ($t->status == 'IN-PROGRESS')
                                        $status_badge = 'bg-warning text-dark';
                                    elseif ($t->status == 'RESOLVED')
                                        $status_badge = 'bg-success';
                                    elseif ($t->status == 'CLOSED')
                                        $status_badge = 'bg-dark';

                                    $priority_badge = 'text-muted';
                                    if ($t->priority == 'URGENT')
                                        $priority_badge = 'text-danger fw-bold';
                                    elseif ($t->priority == 'HIGH')
                                        $priority_badge = 'text-warning fw-bold';

                                    $cat_icon = 'bi-question-circle';
                                    if ($t->category == 'BUG')
                                        $cat_icon = 'bi-bug-fill text-danger';
                                    elseif ($t->category == 'FEATURE')
                                        $cat_icon = 'bi-lightbulb-fill text-warning';
                                    ?>
                                    <tr>
                                        <td class="fw-bold text-monospace">
                                            <?= $t->ticket_number ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('support/detail/' . $t->id) ?>"
                                                class="text-dark fw-bold text-decoration-none">
                                                <?= $t->subject ?>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border"><?= $t->module_name ?></span>
                                        </td>
                                        <?php if ($is_admin): ?>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-2 bg-primary text-white text-center rounded-circle"
                                                        style="width: 30px; line-height:30px;">
                                                        <?= strtoupper(substr($t->nama_lengkap ?? '?', 0, 1)) ?>
                                                    </div>
                                                    <span class="small">
                                                        <?= $t->nama_lengkap ?? $t->username ?>
                                                    </span>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                        <td>
                                            <i class="bi <?= $cat_icon ?> me-1"></i>
                                            <?= $t->category ?>
                                        </td>
                                        <td><span class="<?= $priority_badge ?> small">
                                                <?= $t->priority ?>
                                            </span></td>
                                        <td><span class="badge <?= $status_badge ?>">
                                                <?= $t->status ?>
                                            </span></td>
                                        <td class="small text-muted">
                                            <?= date('d M Y H:i', strtotime($t->created_at)) ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url('support/detail/' . $t->id) ?>"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> Detail
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
</div>

<script>
    $(document).ready(function () {
        if (!$.fn.DataTable.isDataTable('#ticketTable')) {
            $('#ticketTable').DataTable({
                "order": [[0, "desc"]],
                "language": {
                    "emptyTable": "Belum ada ticket support.",
                    "zeroRecords": "Tidak ada data yang cocok dengan pencarian.",
                    "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Lanjut",
                        "previous": "Kembali"
                    },
                    "search": "Cari:"
                }
            });
        }
    });
</script>