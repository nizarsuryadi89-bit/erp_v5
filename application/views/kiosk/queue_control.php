<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InstaPrint - Queue Control |
        <?= $branch_name ?>
    </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .queue-card {
            transition: all 0.2s;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .queue-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            border-color: #0d6efd;
        }

        .queue-number {
            font-size: 2rem;
            font-weight: 800;
            color: #0d6efd;
        }

        .current-queue-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
        }

        .current-number {
            font-size: 4rem;
            font-weight: 900;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .btn-call {
            font-size: 1.1rem;
            padding: 15px 30px;
            font-weight: 600;
        }

        .badge-waiting {
            font-size: 1rem;
            padding: 8px 16px;
        }
    </style>
</head>

<body>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="fas fa-users-cog me-2 text-primary"></i>InstaPrint Control Center
                </h3>
                <p class="text-muted mb-0">
                    <?= $branch_name ?>
                </p>
            </div>
            <div>
                <a href="<?= site_url('kiosk/display_board?perusahaan_id=' . $branch_id) ?>" target="_blank"
                    class="btn btn-outline-primary me-2">
                    <i class="fas fa-tv me-1"></i> Buka Display Board
                </a>
                <button onclick="location.reload()" class="btn btn-light border">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>

        <div class="row">
            <!-- Current Queue -->
            <div class="col-md-4">
                <div class="current-queue-section text-center">
                    <h5 class="mb-3"><i class="fas fa-bell me-2"></i>Sedang Dipanggil</h5>
                    <div id="current-queue">
                        <div class="text-center py-4">
                            <i class="fas fa-clock fa-3x mb-3" style="opacity: 0.5;"></i>
                            <p class="mb-0">Belum ada yang dipanggil</p>
                        </div>
                    </div>
                    <button id="btn-complete" class="btn btn-light btn-lg mt-4 d-none" onclick="completeQueue()">
                        <i class="fas fa-check me-2"></i>Selesai Dilayani
                    </button>
                </div>

                <!-- Quick Stats -->
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title fw-bold mb-3">Statistik Hari Ini</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Menunggu:</span>
                            <span class="fw-bold text-primary" id="stat-waiting">0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Selesai:</span>
                            <span class="fw-bold text-success" id="stat-served">0</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Total:</span>
                            <span class="fw-bold" id="stat-total">0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Waiting Queues -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-list me-2"></i>Daftar Antrian
                            <span class="badge bg-warning text-dark badge-waiting ms-2" id="waiting-count">0</span>
                        </h5>
                    </div>
                    <div class="card-body" id="waiting-list" style="max-height: 600px; overflow-y: auto;">
                        <!-- Dynamic content -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const BRANCH_ID = <?= $branch_id ?>; // For display only
        let currentQueueNumber = null;

        function updateQueue() {
            // Branch ID automatically retrieved from session on server side
            $.get('<?= site_url("kiosk/get_queue_status") ?>', function (res) {
                if (res.status === 'success') {
                    // Update current
                    if (res.current) {
                        currentQueueNumber = res.current.queue_number;
                        $('#current-queue').html(`
                            <div class="current-number">${res.current.queue_number}</div>
                            <div class="fs-4 mt-2">${res.current.konsumen_nama || 'Customer'}</div>
                        `);
                        $('#btn-complete').removeClass('d-none');
                    } else {
                        currentQueueNumber = null;
                        $('#current-queue').html(`
                            <div class="text-center py-4">
                                <i class="fas fa-clock fa-3x mb-3" style="opacity: 0.5;"></i>
                                <p class="mb-0">Belum ada yang dipanggil</p>
                            </div>
                        `);
                        $('#btn-complete').addClass('d-none');
                    }

                    // Update waiting list
                    $('#waiting-count').text(res.waiting_count);
                    $('#stat-waiting').text(res.waiting_count);
                    $('#stat-served').text(res.served_count || 0);
                    $('#stat-total').text((res.waiting_count + (res.served_count || 0)));

                    if (res.waiting.length > 0) {
                        let html = '<div class="row g-3">';
                        res.waiting.forEach(q => {
                            html += `
                                <div class="col-md-6">
                                    <div class="card queue-card h-100" onclick="callQueue('${q.queue_number}')">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="queue-number">${q.queue_number}</div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-1 fw-bold">${q.konsumen_nama || 'Customer'}</h6>
                                                    <small class="text-muted">
                                                        <i class="far fa-clock me-1"></i>${formatTime(q.created_at)}
                                                    </small>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <button class="btn btn-primary btn-sm">
                                                        <i class="fas fa-phone"></i> Panggil
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                        $('#waiting-list').html(html);
                    } else {
                        $('#waiting-list').html(`
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-check-circle fa-4x mb-3"></i>
                                <h5>Tidak ada antrian menunggu</h5>
                                <p>Semua antrian sudah dilayani</p>
                            </div>
                        `);
                    }
                }
            });
        }

        function callQueue(queueNumber) {
            Swal.fire({
                title: 'Panggil Antrian?',
                html: `Memanggil nomor <strong>${queueNumber}</strong>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Panggil!',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (result.isConfirmed) {
                    // Branch ID automatically from session
                    $.post('<?= site_url("kiosk/call_queue") ?>', {
                        queue_number: queueNumber
                    }, function (res) {
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: `Antrian ${queueNumber} dipanggil`,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            updateQueue();
                        } else {
                            Swal.fire('Gagal', res.msg, 'error');
                        }
                    });
                }
            });
        }

        function completeQueue() {
            if (!currentQueueNumber) return;

            Swal.fire({
                title: 'Selesai Dilayani?',
                html: `Menandai <strong>${currentQueueNumber}</strong> sudah selesai`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Selesai',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (result.isConfirmed) {
                    // Branch ID automatically from session
                    $.post('<?= site_url("kiosk/complete_queue") ?>', {
                        queue_number: currentQueueNumber
                    }, function (res) {
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Selesai!',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            updateQueue();
                        }
                    });
                }
            });
        }

        function formatTime(datetime) {
            const date = new Date(datetime);
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${hours}:${minutes}`;
        }

        // Auto-refresh every 5 seconds
        updateQueue();
        setInterval(updateQueue, 5000);
    </script>
</body>

</html>