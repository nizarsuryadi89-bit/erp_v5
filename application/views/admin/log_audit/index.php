<div class="p-0 m-0 bg-light">
    <!-- Header Section: Modern Gradient -->
    <div class="bg-primary text-white pb-5 pt-4 shadow-sm"
        style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); border-radius: 0 0 20px 20px; padding: 2rem !important;">
        <div class="container-fluid p-0">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="bg-white text-primary p-3 rounded-circle mr-3 shadow-lg d-flex align-items-center justify-content-center"
                        style="width: 55px; height: 55px;">
                        <i class="fas fa-history fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 font-weight-bold mb-0">Audit Trail</h1>
                        <p class="opacity-75 mb-0 small">Log Aktivitas Sistem & Keamanan</p>
                    </div>
                </div>
                <div class="text-right d-none d-md-block">
                    <a href="<?= base_url('log_audit/purge?days=3') ?>"
                        class="btn btn-sm btn-light text-danger rounded-pill px-3 py-2 fw-bold shadow-sm mr-2"
                        onclick="return confirm('Apakah Anda yakin ingin menghapus semua log yang lebih tua dari 3 hari? Tindakan ini tidak dapat dibatalkan.')">
                        <i class="fas fa-trash-alt mr-1"></i> Bersihkan Log Lama
                    </a>
                    <span class="badge badge-light px-3 py-2 rounded-pill shadow-sm">
                        Total 500 Log Terbaru
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid px-3" style="margin-top: -30px;">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-lg" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-hover custom-table" id="table-audit">
                                <thead>
                                    <tr>
                                        <th width="150" class="ps-4">Waktu</th>
                                        <th>Informasi Aktivitas</th>
                                        <th width="80" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($logs as $log):
                                        $type_badge = 'bg-soft-info text-info';
                                        $icon = 'fa-info-circle';
                                        if ($log['log_type'] == 'WRITE') {
                                            $type_badge = 'bg-soft-success text-success';
                                            $icon = 'fa-plus-circle';
                                        } elseif ($log['log_type'] == 'DELETE') {
                                            $type_badge = 'bg-soft-danger text-danger';
                                            $icon = 'fa-trash-alt';
                                        }
                                        ?>
                                        <tr>
                                            <td class="ps-4 align-middle">
                                                <div class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">
                                                    <?= date('H:i:s', strtotime($log['created_at'])) ?>
                                                </div>
                                                <div class="text-muted" style="font-size: 0.8rem;">
                                                    <?= date('d M Y', strtotime($log['created_at'])) ?>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <div class="d-flex align-items-start gap-3">
                                                    <div class="status-indicator <?= $type_badge ?> mt-1 shadow-sm">
                                                        <i class="fas <?= $icon ?>"></i>
                                                    </div>
                                                    <div>
                                                        <div class="mb-1 d-flex align-items-center flex-wrap gap-2">
                                                            <span class="fw-bold text-primary" style="font-size: 0.9rem;">
                                                                <i class="fas fa-user-circle me-1"></i> <?= $log['username'] ?>
                                                            </span>
                                                            <span class="badge <?= $type_badge ?> rounded-pill" style="font-size: 0.65rem; padding: 3px 8px;">
                                                                <?= $log['log_type'] ?>
                                                            </span>
                                                            <?php if($log['ip_address']): ?>
                                                            <span class="text-muted" style="font-size: 0.75rem;">
                                                                <i class="fas fa-network-wired mx-1"></i> <?= $log['ip_address'] ?>
                                                            </span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <?php
                                                            $action_text = $log['log_action'];
                                                            // Jika ini adalah log otomatis "User melakukan aksi di modul..."
                                                            if (strpos($action_text, 'User melakukan aksi di modul') === 0) {
                                                                $dict = [
                                                                    'batch_selesai_dan_stok' => 'Menyelesaikan Produksi & Memotong Stok',
                                                                    'advance_step_lf_ajax' => 'Meningkatkan Tahapan Produksi (LF)',
                                                                    'move_to_pb' => 'Memindahkan SPK ke Pengambilan Barang',
                                                                    'tambah' => 'Membuat Data Baru',
                                                                    'edit' => 'Mengubah Data',
                                                                    'hapus' => 'Menghapus Data',
                                                                    'delete' => 'Menghapus Data',
                                                                    'save' => 'Menyimpan Data',
                                                                    'update' => 'Memperbarui Data'
                                                                ];
                                                                
                                                                $method = strtolower($log['method']);
                                                                $controller = strtoupper($log['controller']);
                                                                
                                                                $human_method = isset($dict[$method]) ? $dict[$method] : ucwords(str_replace('_', ' ', $method));
                                                                $action_text = "<span class='text-primary fw-bold'>[$controller]</span> $human_method";
                                                            } else {
                                                                // Highlight kata kunci penting jika ada (untuk log manual)
                                                                $action_text = preg_replace('/(\[.*?\])/', '<span class="text-primary fw-bold">$1</span>', $action_text);
                                                            }
                                                        ?>
                                                        <div class="text-dark mb-1" style="font-size: 0.95rem;">
                                                            <?= $action_text ?>
                                                        </div>
                                                        <?php if (strpos($log['log_action'], 'User melakukan aksi di modul') === 0): ?>
                                                        <div class="text-muted" style="font-size: 0.75rem;">
                                                            <code class="text-muted bg-light px-1 rounded"><?= strtolower($log['controller']) ?>/<?= strtolower($log['method']) ?></code>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                <button class="btn btn-action-pill btn-detail"
                                                    data-id="<?= $log['log_id'] ?>" title="Lihat Detail Teknis">
                                                    <i class="fas fa-search-plus"></i>
                                                </button>
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
</div>

<!-- Modal Detail Log -->
<div class="modal fade" id="modalDetailLog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-search text-primary mr-2"></i> Log Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-lg">
                            <label class="text-muted small text-uppercase font-weight-bold mb-1 d-block">URL
                                Endpoint</label>
                            <p id="log-url" class="font-weight-bold text-dark mb-0 text-break small"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-lg h-100">
                            <label class="text-muted small text-uppercase font-weight-bold mb-1 d-block">User
                                Agent</label>
                            <p id="log-ua" class="small text-muted mb-0"></p>
                        </div>
                    </div>
                </div>
                <div class="rounded-lg overflow-hidden shadow-sm">
                    <div class="bg-dark p-2 text-white d-flex justify-content-between align-items-center">
                        <span class="small font-weight-bold ml-2"><i class="fas fa-code mr-2 text-success"></i> Payload
                            JSON</span>
                        <button class="btn btn-xs btn-outline-light border-0" onclick="copyJson()"><i
                                class="fas fa-copy"></i></button>
                    </div>
                    <pre id="log-json" class="bg-dark text-success m-0 p-3"
                        style="max-height: 400px; overflow: auto; font-size: 13px;"></pre>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#table-audit').DataTable({
            "order": [[0, "desc"]],
            "pageLength": 25,
            "language": {
                "search": "",
                "searchPlaceholder": "Cari data log...",
                "lengthMenu": "_MENU_",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                "paginate": {
                    "next": "›",
                    "previous": "‹"
                }
            },
            "drawCallback": function () {
                $('.dataTables_filter input').addClass('form-control form-control-sm rounded-pill px-3 py-1 border-light shadow-sm');
                $('.dataTables_length select').addClass('form-control form-control-sm rounded-pill px-3 py-1 border-light shadow-sm');
                $('.paginate_button').addClass('rounded-circle');
            }
        });

        $(document).on('click', '.btn-detail', function () {
            const id = $(this).data('id');
            const $btn = $(this);
            $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

            $.getJSON('<?= base_url('log_audit/detail/') ?>' + id, function (data) {
                let details = {};
                try {
                    details = JSON.parse(data.log_details);
                } catch (e) {
                    details = data.log_details;
                }

                $('#log-url').text(details.url || '-');
                $('#log-ua').text(details.user_agent || '-');

                const formattedJson = JSON.stringify(details.data || details, null, 4);
                $('#log-json').text(formattedJson);

                $('#modalDetailLog').modal('show');
                $btn.html('<i class="fas fa-eye mr-1"></i> Lihat').prop('disabled', false);
            });
        });
    });

    function copyJson() {
        const text = document.getElementById('log-json').innerText;
        navigator.clipboard.writeText(text).then(() => {
            alert('JSON copied to clipboard!');
        });
    }
</script>

<style>
    /* Force layout to touch edges */
    .content-wrapper {
        padding: 0 !important;
        background: #f8fafc !important;
    }

    .main-footer {
        margin-top: 0 !important;
    }

    /* Scope Tags Styling */
    .scope-tag {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .bg-soft-primary {
        background: #e0e7ff;
        color: #4338ca;
    }

    .bg-soft-info {
        background: #e0f2fe;
        color: #0369a1;
    }

    .bg-soft-success {
        background: #dcfce7;
        color: #15803d;
    }

    .bg-soft-danger {
        background: #fee2e2;
        color: #b91c1c;
    }

    /* Table Styling */
    .custom-table {
        border-collapse: separate;
        border-spacing: 0 0;
    }

    .custom-table thead th {
        background: #f8fafc;
        border: none;
        color: #64748b;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 1.25rem 1rem !important;
        border-bottom: 2px solid #f1f5f9 !important;
    }

    .custom-table tbody td {
        padding: 1.25rem 1rem !important;
        vertical-align: middle;
        font-size: 0.85rem;
        border-bottom: 1px solid #f1f5f9;
        color: #475569;
    }

    .custom-table tbody tr:hover {
        background-color: #f1f5f9 !important;
    }

    .rounded-lg {
        border-radius: 12px;
    }

    .status-indicator {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 14px;
        flex-shrink: 0;
    }

    .btn-action-pill {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: white;
        border: 1px solid #e2e8f0;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        cursor: pointer;
    }

    .btn-action-pill:hover {
        background: #0d6efd;
        color: white;
        border-color: #0d6efd;
        transform: scale(1.1);
    }

    /* DataTables Overrides */
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 1.5rem;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #0d6efd !important;
        color: white !important;
        border-color: #0d6efd !important;
    }
</style>
</style>