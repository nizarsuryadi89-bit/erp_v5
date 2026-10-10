<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Operator Antrian</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- AdminLTE & Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/adminlte.css') ?>">

    <style>
        body {
            background-color: #f4f6f9;
        }

        .nomor-besar {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }
    </style>
</head>

<body>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-headset"></i> Panel Operator Antrian</h5>
                    </div>
                    <div class="card-body">
                        <!-- Pilih Counter -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <form method="POST" class="d-flex gap-2">
                                    <select name="counter_id" class="form-control" onchange="this.form.submit()">
                                        <option value="">-- Pilih Counter --</option>
                                        <?php
                                        $counters = $this->Antrian_model->get_all_counter();
                                        foreach ($counters as $c):
                                            ?>
                                            <option value="<?= $c['counter_id']; ?>"
                                                <?= ($selected_counter == $c['counter_id']) ? 'selected' : ''; ?>>
                                                <?= $c['counter_kode'] ? $c['counter_kode'] . ' - ' : ''; ?>
                                                <?= $c['counter_nama']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            </div>
                        </div>

                        <?php if ($selected_counter): ?>
                            <div class="row">
                                <!-- Antrian Sedang Dipanggil -->
                                <div class="col-md-6 mb-4">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0"><i class="fas fa-megaphone"></i> Sedang Dipanggil</h6>
                                        </div>
                                        <div class="card-body text-center">
                                            <?php if ($antrian_sekarang): ?>
                                                <div class="mb-3">
                                                    <small
                                                        class="text-muted"><?= $antrian_sekarang['counter_kode'] ? 'Desk ' . $antrian_sekarang['counter_kode'] : $antrian_sekarang['counter_nama']; ?></small>
                                                </div>
                                                <div class="nomor-besar"
                                                    style="font-size: 72px; font-weight: bold; color: #007bff; font-family: monospace;">
                                                    <?= $antrian_sekarang['nomor_antrian']; ?>
                                                </div>
                                                <div class="mt-3">
                                                    <small class="text-muted">
                                                        Status:
                                                        <strong><?= ucfirst(str_replace('_', ' ', $antrian_sekarang['status'])); ?></strong>
                                                    </small>
                                                </div>
                                                <div class="mt-3">
                                                    <?php if ($antrian_sekarang['status'] == 'dipanggil'): ?>
                                                        <button class="btn btn-success btn-sm"
                                                            onclick="mulaiLayani(<?= $antrian_sekarang['antrian_id']; ?>)">
                                                            <i class="fas fa-check"></i> Mulai Layani
                                                        </button>
                                                    <?php elseif ($antrian_sekarang['status'] == 'sedang_dilayani'): ?>
                                                        <button class="btn btn-primary btn-sm"
                                                            onclick="selesaiLayani(<?= $antrian_sekarang['antrian_id']; ?>)">
                                                            <i class="fas fa-check-circle"></i> Selesai
                                                        </button>
                                                    <?php endif; ?>
                                                    <button class="btn btn-danger btn-sm"
                                                        onclick="batalkanAntrian(<?= $antrian_sekarang['antrian_id']; ?>)">
                                                        <i class="fas fa-times"></i> Batalkan
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <div style="padding: 40px 20px; color: #999;">
                                                    <i class="fas fa-inbox fa-3x mb-3" style="display: block;"></i>
                                                    <p>Tidak ada antrian yang sedang dipanggil</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Antrian Menunggu -->
                                <div class="col-md-6 mb-4">
                                    <div class="card border-warning">
                                        <div class="card-header bg-warning text-dark">
                                            <h6 class="mb-0"><i class="fas fa-clock"></i> Menunggu
                                                (<?= count($antrian_menunggu); ?>)</h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <?php if (!empty($antrian_menunggu)): ?>
                                                <div class="list-group list-group-flush">
                                                    <?php foreach ($antrian_menunggu as $a): ?>
                                                        <div class="list-group-item">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <h6 class="mb-1"
                                                                        style="font-size: 24px; font-family: monospace; font-weight: bold;">
                                                                        <?= $a['nomor_antrian']; ?>
                                                                    </h6>
                                                                    <small class="text-muted">
                                                                        <?= date('H:i', strtotime($a['waktu_ambil'])); ?>
                                                                    </small>
                                                                </div>
                                                                <button class="btn btn-primary btn-sm"
                                                                    onclick="panggilAntrian(<?= $a['antrian_id']; ?>)">
                                                                    <i class="fas fa-play"></i> Panggil
                                                                </button>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <div style="padding: 40px 20px; text-align: center; color: #999;">
                                                    <p>Tidak ada antrian yang menunggu</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Silakan pilih counter terlebih dahulu
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Batalkan Antrian -->
    <div class="modal fade" id="batalkanModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Batalkan Antrian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Pembatalan</label>
                        <textarea class="form-control" id="alasanBatal" rows="3"
                            placeholder="Masukkan alasan pembatalan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" onclick="confirmBatalkan()">Batalkan Antrian</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let currentAntrian = null;

        function panggilAntrian(antrianId) {
            fetch('<?= base_url('antrian/panggil_antrian_ajax'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'antrian_id=' + antrianId
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        location.reload();
                    } else {
                        alert(data.message || 'Gagal memanggil antrian');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan');
                });
        }

        function mulaiLayani(antrianId) {
            fetch('<?= base_url('antrian/mulai_layani_ajax'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'antrian_id=' + antrianId
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        location.reload();
                    } else {
                        alert(data.message || 'Gagal memulai layanan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan');
                });
        }

        function selesaiLayani(antrianId) {
            fetch('<?= base_url('antrian/selesai_layani_ajax'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'antrian_id=' + antrianId
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        location.reload();
                    } else {
                        alert(data.message || 'Gagal menyelesaikan antrian');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan');
                });
        }

        function batalkanAntrian(antrianId) {
            currentAntrian = antrianId;
            new bootstrap.Modal(document.getElementById('batalkanModal')).show();
        }

        function confirmBatalkan() {
            const alasan = document.getElementById('alasanBatal').value;

            fetch('<?= base_url('antrian/batalkan_antrian_ajax'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'antrian_id=' + currentAntrian + '&keterangan=' + encodeURIComponent(alasan)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        bootstrap.Modal.getInstance(document.getElementById('batalkanModal')).hide();
                        location.reload();
                    } else {
                        alert(data.message || 'Gagal membatalkan antrian');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan');
                });
        }
    </script>

    <!-- Flashdata Alert Logic -->
    <?php if ($this->session->flashdata('success')): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '<?= $this->session->flashdata('success') ?>',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        </script>
        <?php $this->session->unset_userdata('success'); endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '<?= $this->session->flashdata('error') ?>',
                timer: 4000,
                showConfirmButton: true,
                confirmButtonColor: '#d33'
            });
        </script>
        <?php $this->session->unset_userdata('error'); endif; ?>

</body>

</html>