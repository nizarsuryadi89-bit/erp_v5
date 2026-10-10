<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    :root {
        --op-primary: #f39c12;
        /* Industrial Orange */
        --op-secondary: #2c3e50;
        --op-accent: #e67e22;
        --op-dark: #34495e;
        --op-gradient: linear-gradient(135deg, #f39c12 0%, #d35400 100%);
        --status-ready: #3498db;
        --status-busy: #e74c3c;
        --status-done: #2ecc71;
    }

    .op-stat-card {
        border-radius: 15px;
        border: none;
        color: white;
        overflow: hidden;
        position: relative;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        height: 100%;
    }

    .op-stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .op-stat-card .card-body {
        padding: 1.5rem;
        position: relative;
        z-index: 2;
    }

    .op-stat-card .icon-bg {
        position: absolute;
        right: -10px;
        bottom: -10px;
        font-size: 5rem;
        opacity: 0.15;
        transform: rotate(-10deg);
    }

    .bg-gradient-op {
        background: var(--op-gradient);
    }

    .bg-gradient-blue {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    }

    .bg-gradient-green {
        background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
    }

    .bg-gradient-dark {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    }

    .machine-status-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        border-left: 5px solid #ccc;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        margin-bottom: 1rem;
    }

    .machine-status-card.active {
        border-left-color: var(--status-done);
    }

    .machine-status-card.idle {
        border-left-color: var(--status-ready);
    }

    .machine-status-card.error {
        border-left-color: var(--status-busy);
    }

    .op-shortcut {
        background: white;
        border-radius: 12px;
        padding: 1.2rem;
        text-align: center;
        text-decoration: none !important;
        color: var(--op-dark);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        transition: all 0.2s;
        border: 1px solid #eee;
    }

    .op-shortcut:hover {
        background: #fdfaf5;
        border-color: var(--op-primary);
        color: var(--op-primary);
        transform: scale(1.05);
    }

    .op-shortcut i {
        font-size: 2rem;
        color: var(--op-primary);
    }

    .queue-table-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .status-badge-op {
        padding: 5px 12px;
        border-radius: 6px;
        font-weight: 800;
        font-size: 0.7rem;
        text-transform: uppercase;
    }

    .bg-soft-blue {
        background: #ebf5fb;
        color: #2980b9;
    }

    .bg-soft-orange {
        background: #fef5e7;
        color: #d35400;
    }

    .bg-soft-green {
        background: #e9f7ef;
        color: #27ae60;
    }

    .btn-op-action {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .scroll-x-hidden {
        overflow-x: hidden;
    }
</style>

<div class="app-content mt-n4 pb-5">
    <div class="container-fluid">
        <!-- Header -->
        <div class="row align-items-center mb-4 pt-2">
            <div class="col-md-7">
                <h4 class="fw-900 text-dark mb-1"><i class="fas fa-cogs text-warning me-2"></i>LANTAI PRODUKSI:
                    <?= $this->session->userdata('nama_perusahaan') ?>
                </h4>
                <p class="text-muted fw-bold mb-0">Selamat bertugas,
                    <?= $this->session->userdata('username') ?>. Jaga kualitas & efisiensi mesin!
                </p>
            </div>
            <div class="col-md-5 text-md-end">
                <div class="d-inline-flex align-items-center bg-white p-2 rounded-pill shadow-sm border px-4">
                    <span class="badge bg-soft-orange text-warning fw-bold rounded-pill px-3 py-2 me-3">MODE
                        PRODUKSI</span>
                    <i class="fas fa-industry text-primary me-2"></i>
                    <span class="fw-900 text-dark">Status Produksi Langsung</span>
                </div>
            </div>
        </div>

        <!-- Metrics Row -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="op-stat-card bg-gradient-blue">
                    <div class="card-body">
                        <h6 class="text-uppercase fw-bold opacity-75 small mb-2">Total Antrian</h6>
                        <h2 class="fw-900 mb-1">
                            <?= $stats_produksi['total_queue'] ?>
                        </h2>
                        <p class="small fw-bold mb-0 opacity-75">Work Orders Menunggu</p>
                        <i class="fas fa-tasks icon-bg"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="op-stat-card bg-gradient-op">
                    <div class="card-body">
                        <h6 class="text-uppercase fw-bold opacity-75 small mb-2">Sedang Cetak</h6>
                        <h2 class="fw-900 mb-1">
                            <?= $stats_produksi['processing'] ?>
                        </h2>
                        <p class="small fw-bold mb-0 opacity-75">Mesin Sedang Berjalan</p>
                        <i class="fas fa-print icon-bg"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="op-stat-card bg-gradient-green">
                    <div class="card-body">
                        <h6 class="text-uppercase fw-bold opacity-75 small mb-2">Selesai Hari Ini</h6>
                        <h2 class="fw-900 mb-1">
                            <?= $stats_produksi['done_today'] ?>
                        </h2>
                        <p class="small fw-bold mb-0 opacity-75">Target Tercapai</p>
                        <i class="fas fa-check-double icon-bg"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="op-stat-card bg-gradient-dark">
                    <div class="card-body text-white">
                        <h6 class="text-uppercase fw-bold opacity-75 small mb-2">Siap untuk PB</h6>
                        <h2 class="fw-900 mb-1">
                            <?= $stats_produksi['on_pb'] ?>
                        </h2>
                        <p class="small fw-bold mb-0 opacity-75">Belum Diambil Konsumen</p>
                        <i class="fas fa-box-open icon-bg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <!-- Machine Status List -->


            <!-- Shortcuts and Tools -->
            <div class="col-lg-12">
                <h6 class="fw-900 text-dark mb-3 px-1"><i class="fas fa-bolt text-warning me-2"></i>PERALATAN PRODUKSI
                </h6>
                <div class="row g-3 row-cols-2 row-cols-md-6 mb-4">
                    <div class="col">
                        <a href="<?= site_url('Wip/produksi') ?>" class="op-shortcut">
                            <i class="fas fa-clipboard-list"></i>
                            <span class="fw-bold small">DAFTAR PRODUKSI</span>
                        </a>
                    </div>

                    <div class="col">
                        <a href="#" class="op-shortcut" data-bs-toggle="modal" data-bs-target="#modalCekStok">
                            <i class="fas fa-boxes text-success"></i>
                            <span class="fw-bold small">CEK STOK</span>
                        </a>
                    </div>

                    <div class="col">
                        <a href="#" class="op-shortcut" data-bs-toggle="modal" data-bs-target="#modalKonsumsiSaya">
                            <i class="fas fa-history text-info"></i>
                            <span class="fw-bold small">KONSUMSI SAYA</span>
                        </a>
                    </div>

                    <div class="col">
                        <a href="<?= site_url('tracking') ?>" class="op-shortcut">
                            <i class="fas fa-search-location text-warning"></i>
                            <span class="fw-bold small">PELACAKAN SPK</span>
                        </a>
                    </div>

                    <div class="col">
                        <a href="<?= site_url('produksi_request') ?>" class="op-shortcut border-primary"
                            style="background: #f0f7ff;">
                            <i class="fas fa-file-signature text-primary"></i>
                            <span class="fw-bold small text-primary">PENGAJUAN </span>
                        </a>
                    </div>

                    <div class="col">
                        <a href="#" class="op-shortcut border-success"
                            style="background: #f0fff4;" data-bs-toggle="modal" data-bs-target="#modalLihatTugas">
                            <i class="fas fa-tasks text-success"></i>
                            <span class="fw-bold small text-success">TUGAS SAYA </span>
                        </a>
                    </div>
                </div>

                <div class="queue-table-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-900 text-dark mb-0"><i class="fas fa-stream text-primary me-2"></i>ANTRIAN KERJA
                            (PRIORITAS)</h5>
                        <span class="badge bg-light text-dark fw-bold rounded-pill px-3 py-2">Pertama Masuk, Pertama
                            Keluar</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-muted small">
                                    <th class="border-0">SPK #</th>
                                    <th class="border-0">DESKRIPSI PRODUK</th>
                                    <th class="border-0">QTY</th>
                                    <th class="border-0 text-center">STATUS</th>
                                    <th class="border-0 text-end">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($active_queue)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5">Antrian produksi kosong.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($active_queue as $row): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-900 text-dark">
                                                    <?= $row->nospk ?>
                                                </div>
                                                <?php if ($row->perusahaan_id != $this->session->userdata('perusahaan_id')): ?>
                                                    <div class="mt-1">
                                                        <span
                                                            class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 small fw-bold animate__animated animate__headShake"
                                                            style="font-size: 0.6rem;">
                                                            <i class="fas fa-exchange-alt me-1"></i> TITIPAN:
                                                            <?= strtoupper($row->asal_cabang) ?>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                                <small class="text-muted fw-bold">
                                                    <?= $row->konsumen_nama ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="fw-bold small">
                                                    <?= $row->product_nama ?>
                                                </div>
                                                <small class="text-primary fw-bold">
                                                    <?= $row->detail_product ?>
                                                </small>
                                                <?php if (!empty($row->nama_routing)): ?>
                                                    <div class="mt-2">
                                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 small fw-bold" style="font-size: 0.65rem;">
                                                            <i class="fas fa-hammer me-1"></i> TUGAS SAAT INI: <?= strtoupper($row->nama_routing) ?>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><span class="fw-900">
                                                    <?= $row->qty ?>
                                                </span></td>
                                            <td class="text-center">
                                                <?php
                                                $st_cls = 'bg-soft-blue';
                                                if ($row->status_produksi == 'proses')
                                                    $st_cls = 'bg-soft-orange';
                                                ?>
                                                <span class="status-badge-op <?= $st_cls ?>">
                                                    <?= strtoupper($row->status_produksi) ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <button type="button" onclick="openControlModal(<?= $row->produksi_id ?>)"
                                                    class="btn btn-sm btn-light text-primary rounded-8 shadow-sm">
                                                    <i class="fas fa-play-circle"></i> PROSES
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL UNTUK KONTROL PRODUKSI LANGSUNG ================= -->

<!-- 1. Modal Control Center (Utama) -->
<div class="modal fade" id="modalControlCenter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-20 overflow-hidden">
            <div class="modal-header bg-gradient-op text-white p-4 border-0">
                <h5 class="modal-title fw-900"><i class="fas fa-microchip me-2"></i>KONTROL PRODUKSI</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Info Header -->
                <div class="p-4 bg-light border-bottom">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 id="ctrl_product_nama" class="fw-900 text-dark mb-1">Memuat...</h4>
                            <div class="d-flex gap-3 small fw-bold">
                                <span class="text-primary"><i class="fas fa-file-invoice me-1"></i> <span
                                        id="ctrl_nospk">-</span></span>
                                <span class="text-muted"><i class="fas fa-user me-1"></i> <span
                                        id="ctrl_konsumen">-</span></span>
                                <span class="text-muted"><i class="fas fa-tag me-1"></i> <span
                                        id="ctrl_detail">-</span></span>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="p-3 bg-white rounded-15 shadow-sm border d-inline-block">
                                <div class="small fw-bold text-muted text-uppercase mb-1">Jumlah Target</div>
                                <h3 id="ctrl_qty" class="fw-900 text-dark mb-0">0</h3>
                                <small class="text-muted" id="ctrl_satuan">Satuan</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Content -->
                <div id="loadingOverlay" class="text-center py-5">
                    <div class="spinner-border text-warning" role="status"></div>
                    <div class="mt-2 text-muted fw-bold">Menyelaraskan Alur Produksi...</div>
                </div>

                <div id="controlTableContainer" class="d-none">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light small fw-900 text-muted">
                                <tr>
                                    <th class="px-4 py-3" width="5%">LANGKAH</th>
                                    <th width="20%">ALUR</th>
                                    <th width="25%">OPERATOR / MESIN</th>
                                    <th width="10%" class="text-center">TARGET</th>
                                    <th width="10%" class="text-center">HASIL</th>
                                    <th width="15%" class="text-center">STATUS</th>
                                    <th class="px-4 text-end" width="15%">AKSI</th>
                                </tr>
                            </thead>
                            <tbody id="routingTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 border-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Tutup
                    Panel</button>
            </div>
        </div>
    </div>
</div>

<!-- 2. Modal Finalisasi (Selesaikan Langkah) -->
<div class="modal fade" id="modalFinishStep" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-20">
            <div class="modal-header bg-gradient-green text-white p-4 border-0">
                <h5 class="modal-title fw-900"><i class="fas fa-check-circle me-2"></i>SELESAIKAN LANGKAH</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="formFinishStep">
                <div class="modal-body p-4">
                    <input type="hidden" name="detail_produksi_id" id="finish_detail_id">
                    <input type="hidden" name="routing_urutan" id="finish_routing_urutan">

                    <div class="row g-3">
                        <div class="col-md-6 text-center border-end">
                            <label class="small fw-900 text-muted ls-1 mb-2">OUTPUT BERHASIL</label>
                            <input type="number" name="qty_output" id="finish_qty_output"
                                class="form-control form-control-lg fw-900 text-center text-success border-2" required>
                        </div>
                        <div class="col-md-6 text-center">
                            <label class="small fw-900 text-muted ls-1 mb-2">DIKIRIM KE (CABANG)</label>
                            <select name="lokasi_proses" id="finish_lokasi" class="form-select border-2 fw-bold"
                                required></select>
                        </div>

                        <div class="col-12">
                            <hr class="my-3 opacity-10">
                        </div>

                        <div class="col-md-12">
                            <label class="small fw-900 text-muted ls-1 mb-2">MESIN YANG DIGUNAKAN</label>
                            <select name="mesin_id" id="finish_mesin" class="form-select border-2 fw-bold"
                                required></select>
                        </div>

                        <div class="col-md-12">
                            <label class="small fw-900 text-muted ls-1 mb-2">MATERIAL (PILIHAN)</label>
                            <select name="material_id" id="finish_material" class="form-select border-2 fw-bold">
                                <option value="0">DEFAULT (SESUAI BOM)</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="small fw-900 text-muted ls-1 mb-2">CATATAN PRODUKSI</label>
                            <textarea name="alasan_missing" class="form-control border-2" rows="2"
                                placeholder="Catatan opsional..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-4 border-0">
                    <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none me-auto"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-5 fw-900 shadow-sm">SIMPAN HASIL <i
                            class="fas fa-save ms-2"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentProductionData = null;

    function openControlModal(id) {
        $('#modalControlCenter').modal('show');
        $('#loadingOverlay').removeClass('d-none');
        $('#controlTableContainer').addClass('d-none');

        $.ajax({
            url: '<?= site_url("wip/get_production_control_json") ?>',
            type: 'POST',
            data: { produksi_id: id },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    currentProductionData = res.data;
                    renderControlCenter(res.data);
                } else {
                    Swal.fire('Error', res.message, 'error');
                    $('#modalControlCenter').modal('hide');
                }
            },
            error: function () {
                Swal.fire('Error', 'Gagal menghubungi server.', 'error');
            }
        });
    }

    function renderControlCenter(data) {
        const p = data.produksi;
        const r = data.routing;

        // Header Info
        $('#ctrl_product_nama').text(p.product_nama);
        $('#ctrl_nospk').text('#' + p.nospk);
        $('#ctrl_konsumen').text(p.konsumen_nama);
        $('#ctrl_detail').text(p.detail_product || '-');
        $('#ctrl_qty').text(new Intl.NumberFormat().format(p.qty));
        $('#ctrl_satuan').text(p.satuan_nama || 'Satuan');

        // Table Body
        let html = '';
        if (r && r.length > 0) {
            r.forEach((item, index) => {
                let statusBadge = '';
                let actionBtn = '';
                const status = String(item.status_produksi || '0');

                if (status === '0') {
                    statusBadge = '<span class="badge bg-light text-muted border px-3 py-2 fw-bold">MENUNGGU</span>';
                    actionBtn = `<button onclick="startStep(${item.detail_produksi_id}, ${item.routing_urutan})" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-900"><i class="fas fa-play me-1"></i> MULAI</button>`;
                } else if (status === '1') {
                    statusBadge = '<span class="badge bg-warning text-dark px-3 py-2 fw-bold animate__animated animate__flash animate__infinite">PROSES</span>';
                    actionBtn = `<button onclick="finishStep(${item.detail_produksi_id}, ${item.routing_urutan})" class="btn btn-sm btn-success rounded-pill px-4 fw-900 shadow-sm"><i class="fas fa-check-circle me-1"></i> SELESAI</button>`;
                } else {
                    statusBadge = '<span class="badge bg-soft-green text-success px-3 py-2 fw-bold">SELESAI</span>';
                    actionBtn = '<span class="text-success fw-bold small"><i class="fas fa-check-double me-1"></i> Selesai</span>';
                }

                html += `
                <tr class="${status === '1' ? 'bg-soft-orange' : ''}">
                    <td class="px-4 text-center fw-900 text-muted">${item.routing_urutan}</td>
                    <td>
                        <div class="fw-900 text-dark">${item.routing_name}</div>
                        <div class="small text-muted">${item.waktu_mulai ? 'Mulai: ' + item.waktu_mulai : ''}</div>
                    </td>
                    <td>
                        <div class="small fw-bold text-dark"><i class="fas fa-user-circle me-1 text-primary"></i> ${item.operator_nama || '-'}</div>
                        <div class="small text-muted"><i class="fas fa-cog me-1"></i> ${item.mesin_nama || '-'}</div>
                    </td>
                    <td class="text-center fw-900">${new Intl.NumberFormat().format(p.qty)}</td>
                    <td class="text-center fw-900 text-success">${new Intl.NumberFormat().format(item.qty_output || 0)}</td>
                    <td class="text-center">${statusBadge}</td>
                    <td class="px-4 text-end">${actionBtn}</td>
                </tr>
            `;
            });
        } else {
            html = '<tr><td colspan="7" class="text-center py-5 text-muted fw-bold">Maaf, alur produksi belum dikonfigurasi.</td></tr>';
        }

        $('#routingTableBody').html(html);
        $('#loadingOverlay').addClass('d-none');
        $('#controlTableContainer').removeClass('d-none').addClass('animate__animated animate__fadeIn');
    }

    function startStep(id, urutan) {
        Swal.fire({
            title: 'Mulai Langkah Ini?',
            text: "Waktu pengerjaan akan mulai dihitung.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f39c12',
            confirmButtonText: 'Ya, Mulai Produksi!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= site_url("wip/start_process") ?>', {
                    detail_produksi_id: id,
                    routing_urutan: urutan
                }, function (res) {
                    if (res.success) {
                        // Update current data view without closing
                        openControlModal(currentProductionData.produksi.produksi_id);
                        // Refresh dash metrics in bg
                        updateDashboardStats();
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                }, 'json');
            }
        });
    }

    function finishStep(id, urutan) {
        const data = currentProductionData;
        $('#finish_detail_id').val(id);
        $('#finish_routing_urutan').val(urutan);
        $('#finish_qty_output').val(data.produksi.qty);

        // Populate Selects
        let mesinHtml = '<option value="">-- PILIH MESIN --</option><option value="0">-- MANUAL --</option>';
        data.machines.forEach(m => {
            mesinHtml += `<option value="${m.mesin_id}">${m.mesin_nama}</option>`;
        });
        $('#finish_mesin').html(mesinHtml);

        let lokasiHtml = '';
        data.locations.forEach(l => {
            lokasiHtml += `<option value="${l.id_perusahaan}" ${l.id_perusahaan == <?= $this->session->userdata('perusahaan_id') ?> ? 'selected' : ''}>${l.nama_perusahaan}</option>`;
        });
        $('#finish_lokasi').html(lokasiHtml);

        let matHtml = '<option value="0">DEFAULT (SESUAI BOM)</option>';
        data.materials.forEach(m => {
            matHtml += `<option value="${m.material_id}">${m.varian || m.material_id}</option>`;
        });
        $('#finish_material').html(matHtml);

        $('#modalFinishStep').modal('show');
    }

    $('#formFinishStep').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            url: '<?= site_url("wip/complete_process") ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    $('#modalFinishStep').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Langkah produksi telah diselesaikan.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    openControlModal(currentProductionData.produksi.produksi_id);
                    updateDashboardStats();
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            }
        });
    });

    function updateDashboardStats() {
        // Optionally trigger a reload of specific stats if needed, or just let operator reload
        // For simplicity, we just recommend reload after many tasks
    }
</script>

<!-- Modal Lihat Tugas -->
<div class="modal fade" id="modalLihatTugas" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-light p-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-tasks me-2 text-success"></i> Tugas & Tanggung Jawab Saya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <label class="form-label fw-bold">DAFTAR ROUTING / PROSES YANG DITANGANI</label>
                    <div class="table-responsive mt-2">
                        <table class="table table-hover table-bordered align-middle text-start mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="10%" class="text-center">NO</th>
                                    <th>NAMA TUGAS / PROSES ROUTING</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($operator_tasks)): ?>
                                    <?php 
                                        $tasks = explode(',', $operator_tasks);
                                        $no = 1;
                                        foreach($tasks as $t): 
                                            $task_name = trim($t);
                                            if (empty($task_name)) continue;
                                    ?>
                                        <tr>
                                            <td class="text-center fw-bold text-muted"><?= $no++ ?></td>
                                            <td class="fw-bold text-dark"><i class="fas fa-check-circle text-success me-2"></i><?= htmlspecialchars($task_name) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="2" class="text-center text-muted fw-bold fst-italic py-4">Belum ada tugas routing yang terdaftar untuk akun ini.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                <button type="button" class="btn btn-light rounded-pill px-5 fw-bold shadow-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cek Stok Gudang -->
<div class="modal fade" id="modalCekStok" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-boxes me-2"></i> CEK STOK GUDANG (LIVE)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle w-100" id="tableCekStok">
                        <thead class="table-light text-muted small uppercase">
                            <tr>
                                <th>NAMA BAHAN</th>
                                <th>VARIAN / UKURAN</th>
                                <th>CABANG GUDANG</th>
                                <th class="text-start">SISA STOK (LIVE)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Riwayat Konsumsi Saya -->
<div class="modal fade" id="modalKonsumsiSaya" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-history me-2"></i> RIWAYAT KONSUMSI SAYA (LIVE)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle w-100" id="tableKonsumsiSaya">
                        <thead class="table-light text-muted small uppercase">
                            <tr>
                                <th>TANGGAL</th>
                                <th>NO SPK / PRODUK</th>
                                <th>BAHAN KONSUMSI</th>
                                <th class="text-start">KONSUMSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let stokTableLoaded = false;
        const modalStok = document.getElementById('modalCekStok');

        if (modalStok) {
            modalStok.addEventListener('show.bs.modal', function () {
                if (!stokTableLoaded) {
                    if ($.fn.DataTable.isDataTable('#tableCekStok')) {
                        $('#tableCekStok').DataTable().destroy();
                    }

                    $('#tableCekStok').DataTable({
                        ajax: {
                            url: '<?= site_url('dashboard/ajax_get_stock_deskprint') ?>',
                            dataSrc: ''
                        },
                        columns: [
                            { data: 'bahan_nama', className: 'fw-bold' },
                            { data: 'varian' },
                            { data: 'cabang', render: function (data) { return '<span class="badge bg-light text-dark border">' + data + '</span>'; } },
                            {
                                data: 'base_qty',
                                className: 'text-start fw-900',
                                render: function (data, type, row) {
                                    var qty = parseFloat(data) || 0;
                                    var konversi = parseFloat(row.konversi) || 1;
                                    var satGudang = row.satuan_gudang || '';
                                    var satDasar = row.satuan_dasar || '';

                                    if (konversi > 1) {
                                        var main_qty = Math.floor(qty / konversi);
                                        var small_qty = Math.round(qty % konversi);
                                        var html = '';

                                        if (main_qty !== 0 || small_qty === 0) {
                                            html += '<span class="text-primary" style="font-size: 1.1rem;">' + main_qty.toLocaleString('id-ID') + '</span> <span class="text-muted">' + satGudang + '</span>';
                                        }

                                        if (small_qty !== 0) {
                                            if (html !== '') html += '<br>';
                                            html += '<span class="text-info">' + small_qty.toLocaleString('id-ID') + '</span> <span class="text-muted small">' + satDasar + '</span>';
                                        }
                                        return html;
                                    } else {
                                        return '<span class="text-primary" style="font-size: 1.1rem;">' + qty.toLocaleString('id-ID') + '</span> <span class="text-muted">' + (satDasar || satGudang) + '</span>';
                                    }
                                }
                            }
                        ],
                        language: {
                            search: "Cari Material:",
                            lengthMenu: "Tampilkan _MENU_ data",
                            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ material",
                            infoEmpty: "Tidak ada data material",
                            zeroRecords: "Material tidak ditemukan",
                            paginate: {
                                first: "Pertama",
                                last: "Terakhir",
                                next: "Selanjutnya",
                                previous: "Sebelumnya"
                            }
                        },
                        pageLength: 10,
                        order: [[0, 'asc']]
                    });
                    stokTableLoaded = true;
                } else {
                    $('#tableCekStok').DataTable().ajax.reload(null, false);
                }
            });
        }

        let konsTableLoaded = false;
        const modalKons = document.getElementById('modalKonsumsiSaya');

        if (modalKons) {
            modalKons.addEventListener('show.bs.modal', function () {
                if (!konsTableLoaded) {
                    if ($.fn.DataTable.isDataTable('#tableKonsumsiSaya')) {
                        $('#tableKonsumsiSaya').DataTable().destroy();
                    }

                    $('#tableKonsumsiSaya').DataTable({
                        ajax: {
                            url: '<?= site_url('dashboard/ajax_get_operator_consumption') ?>',
                            dataSrc: ''
                        },
                        columns: [
                            {
                                data: 'created_at',
                                render: function (data) {
                                    return new Date(data).toLocaleString('id-ID', {
                                        dateStyle: 'medium',
                                        timeStyle: 'short'
                                    });
                                }
                            },
                            {
                                data: 'nospk',
                                render: function (data, type, row) {
                                    return '<span class="fw-900 text-dark">' + data + '</span><br><small class="text-muted">' + (row.product_nama || '-') + '</small>';
                                }
                            },
                            {
                                data: 'bahan_nama',
                                render: function (data, type, row) {
                                    return '<span class="fw-bold">' + data + '</span><br><small class="text-info">' + (row.material_nama || '') + '</small>';
                                }
                            },
                            {
                                data: 'qty',
                                className: 'text-start fw-900',
                                render: function (data, type, row) {
                                    var qty = Math.abs(parseFloat(data)) || 0;
                                    var konversi = parseFloat(row.konversi) || 1;
                                    var satGudang = row.satuan_gudang || '';
                                    var satDasar = row.satuan_produksi || '';

                                    var qty_prod = qty * konversi;
                                    return '<span class="text-danger">-' + qty_prod.toLocaleString('id-ID') + '</span> <span class="text-muted">' + satDasar + '</span>';
                                }
                            }
                        ],
                        language: {
                            search: "Cari Konsumsi:",
                            lengthMenu: "Tampilkan _MENU_ data",
                            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                            infoEmpty: "Tidak ada riwayat konsumsi",
                            zeroRecords: "Riwayat tidak ditemukan",
                            paginate: {
                                first: "Pertama",
                                last: "Terakhir",
                                next: "Selanjutnya",
                                previous: "Sebelumnya"
                            }
                        },
                        pageLength: 10,
                        order: [[0, 'desc']]
                    });
                    konsTableLoaded = true;
                } else {
                    $('#tableKonsumsiSaya').DataTable().ajax.reload(null, false);
                }
            });
        }
    });
</script>