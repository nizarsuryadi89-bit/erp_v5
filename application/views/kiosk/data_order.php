<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrian Kiosk Deskprint</title>
    <!-- Assets (using CDN for simplicity or local if available) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
        }

        .queue-card {
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .queue-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
        }

        .queue-ref {
            font-weight: 800;
            font-size: 1.1rem;
            color: #6366f1;
        }

        .queue-meta {
            font-size: 0.85rem;
            color: #64748b;
        }

        .status-pill {
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 20px;
            background: #e0e7ff;
            color: #4338ca;
            font-weight: 600;
        }

        .btn-process {
            border-radius: 8px;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="fas fa-tablet-alt me-2 text-warning"></i>Antrian Kiosk</h4>
                <div class="text-muted small">Update Terakhir: <span id="last_update">Baru saja</span></div>
            </div>
            <div>
                <a href="<?= site_url('admin') ?>" class="btn btn-outline-secondary me-2"><i
                        class="fas fa-arrow-left me-1"></i> Dashboard</a>
                <button onclick="location.reload()" class="btn btn-light border"><i
                        class="fas fa-sync-alt"></i></button>
            </div>
        </div>

        <?php if (empty($orders)): ?>
            <div class="text-center py-5">
                <div class="mb-3"><i class="fas fa-check-circle fa-4x text-success opacity-25"></i></div>
                <h5 class="fw-bold text-muted">Tidak ada antrian</h5>
                <p class="text-muted">Semua pesanan kiosk sudah diproses.</p>
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($orders as $o): ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card shadow-sm queue-card h-100">
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="queue-ref">
                                        <?= $o->group_ref ?>
                                    </div>
                                    <div class="queue-meta">
                                        <?= $o->time_ago ?>
                                    </div>
                                </div>

                                <h5 class="fw-bold mb-1 text-dark">
                                    <?= $o->nama ?>
                                </h5>
                                <div class="queue-meta mb-3"><i class="fab fa-whatsapp me-1 text-success"></i>
                                    <?= $o->wa ?: '-' ?>
                                </div>

                                <div class="bg-light p-2 rounded mb-3 small border"
                                    style="max-height: 150px; overflow-y: auto;">
                                    <?= $o->item_summary ?>
                                </div>

                                <div
                                    class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top border-light">
                                    <div>
                                        <div class="fw-bold text-dark">
                                            <?= 'Rp ' . number_format($o->est_total, 0, ',', '.') ?>
                                        </div>
                                        <div class="queue-meta">
                                            <?= $o->item_count ?> Item
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btn-process px-4 py-2"
                                        onclick="processOrder('<?= $o->group_ref ?>')">
                                        PROSES <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function processOrder(ref) {
            Swal.fire({
                title: 'Proses Pesanan?',
                text: "Akan membuat SPK baru untuk " + ref,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                confirmButtonText: 'Ya, Proses',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Memproses...', didOpen: () => Swal.showLoading() });

                    $.post('<?= site_url('kiosk/fetch_for_import') ?>', { no_ref: ref }, function (res) {
                        try {
                            if (typeof res === 'string') res = JSON.parse(res);
                            if (res.status === 'success') {
                                window.location.href = res.redirect;
                            } else {
                                Swal.fire('Gagal', res.msg, 'error');
                            }
                        } catch (e) {
                            console.error(e, res);
                            Swal.fire('Error', 'Format data salah', 'error');
                        }
                    }).fail(function () {
                        Swal.fire('Error', 'Koneksi gagal', 'error');
                    });
                }
            });
        }

        // Auto Refresh every 30s
        setInterval(() => {
            location.reload();
        }, 30000);
    </script>
</body>

</html>