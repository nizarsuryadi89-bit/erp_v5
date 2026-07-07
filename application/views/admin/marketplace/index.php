<style>
    .spk-code {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 900;
        font-size: 0.9rem;
        color: #000;
        background: #f8fafc;
        padding: 0.3rem 0.6rem;
        border-radius: 6px;
        border: 2px solid #e2e8f0;
        letter-spacing: -0.5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .spk-code:hover {
        background: #e2e8f0;
        border-color: #cbd5e1;
    }
    .resi-badge {
        font-family: 'JetBrains Mono', monospace;
        background: #fff;
        border: 1px dashed #cbd5e1;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.85rem;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s;
    }
    .resi-badge:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
        color: #0f172a;
    }
    .table-hover tbody tr:hover {
        background-color: #f8fafc !important;
    }
    .filter-card {
        background: linear-gradient(145deg, #ffffff, #f8fafc);
        border: 1px solid #e2e8f0;
    }
</style>

<!-- PREMIUM STYLE -->
<style>
    :root { --marketplace-gradient: linear-gradient(135deg, #03AC0E 0%, #18181b 35%, #18181b 65%, #EE4D2D 100%); }
    .marketplace-dashboard { background: var(--marketplace-gradient); color: white; padding: 1.5rem 0 2rem 0; margin-bottom: 2rem; border-radius: 0 0 24px 24px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08); position: relative; overflow: hidden; }
    .marketplace-dashboard::after { content: ''; position: absolute; top: -50%; right: -10%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%); border-radius: 50%; z-index: 1; }
    .dashboard-content { display: flex; align-items: center; gap: 1.25rem; position: relative; z-index: 2; }
    .dashboard-icon { width: 52px; height: 52px; background: rgba(255, 255, 255, 0.12); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); color: white; }
    .dashboard-title { font-size: 1.75rem; font-weight: 900; margin: 0; letter-spacing: -0.5px; line-height: 1.2; color: white; }
    .dashboard-subtitle { font-size: 0.85rem; font-weight: 700; margin: 0 0 2px 0; opacity: 0.7; color: #e2e8f0; display: flex; align-items: center; gap: 10px; }
    .dashboard-subtitle::before { content: ''; width: 12px; height: 2px; background: #93c5fd; display: inline-block; }
    .btn-premium { border-radius: 10px; padding: 0.45rem 1rem; font-weight: 800; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px; transition: all 0.3s; text-decoration: none !important; display: inline-flex; align-items: center; justify-content: center; gap: 6px; }
    @media (max-width: 768px) {
        .marketplace-dashboard { padding: 2.5rem 0; border-radius: 0 0 25px 25px; }
        .dashboard-content { flex-direction: column; text-align: center; gap: 1.25rem; }
        .dashboard-icon { width: 70px; height: 70px; font-size: 2.2rem; border-radius: 20px; }
        .dashboard-title { font-size: 1.75rem; margin-bottom: 0.25rem; }
        .dashboard-subtitle { justify-content: center; font-size: 1rem; }
        .header-actions-mobile { justify-content: center !important; margin-top: 1rem; }
    }
</style>

<!-- PAGE HEADER -->
<div class="marketplace-dashboard d-print-none">
    <div class="container-xl px-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="dashboard-content">
                    <div class="dashboard-icon">
                        <i class="fas fa-list"></i>
                    </div>
                    <div class="dashboard-text">
                        <p class="dashboard-subtitle">Modul Transaksi</p>
                        <h1 class="dashboard-title">Daftar Pesanan</h1>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-end d-flex gap-2 justify-content-md-end justify-content-center header-actions-mobile flex-wrap position-relative mt-3 mt-md-0" style="z-index: 2;">
                <?php $this->load->view('admin/marketplace/_header_menu'); ?>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-12">
                
                <!-- Filter Section -->
                <section id="filter-section">
                    <div class="card mb-4 filter-card shadow-sm rounded-3">
                        <div class="card-body py-4">
                            <form action="<?= base_url('marketplace') ?>" method="GET" class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label mb-2 fw-bold text-secondary fs-6"><i class="far fa-calendar-alt me-1"></i> Tanggal Mulai</label>
                                    <input type="date" name="date_from" class="form-control form-control-lg bg-white" value="<?= $date_from ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label mb-2 fw-bold text-secondary fs-6"><i class="far fa-calendar-check me-1"></i> Tanggal Akhir</label>
                                    <input type="date" name="date_to" class="form-control form-control-lg bg-white" value="<?= $date_to ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label mb-2 fw-bold text-secondary fs-6"><i class="fas fa-store me-1"></i> Platform</label>
                                    <select name="marketplace_id" class="form-select form-select-lg select2 bg-white">
                                        <option value="">-- Semua Platform --</option>
                                        <?php foreach ($marketplaces as $m): ?>
                                            <option value="<?= $m->marketplace_id ?>" <?= $selected_market == $m->marketplace_id ? 'selected' : '' ?>>
                                                <?= $m->marketplace_nama ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label mb-2 fw-bold text-secondary fs-6"><i class="fas fa-money-bill-wave me-1"></i> Status Bayar</label>
                                    <select name="status" class="form-select form-select-lg bg-white">
                                        <option value="">-- Semua --</option>
                                        <option value="pending" <?= $selected_status == 'pending' ? 'selected' : '' ?>>Proses</option>
                                        <option value="success" <?= $selected_status == 'success' ? 'selected' : '' ?>>Lunas</option>
                                    </select>
                                </div>
                                <div class="col-md-1 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm" title="Terapkan Filter"><i class="fas fa-search"></i></button>
                                    <a href="<?= base_url('marketplace') ?>" class="btn btn-light border btn-lg w-100" title="Reset Filter"><i class="fas fa-sync-alt text-secondary"></i></a>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>

                <!-- Table Section -->
                <section id="table-section">
                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h3 class="card-title text-dark fw-bolder mb-0 d-flex align-items-center">
                                <div class="bg-primary text-white rounded p-2 me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="fas fa-receipt"></i>
                                </div>
                                Riwayat Pesanan
                            </h3>
                            <div class="card-actions">
                                <span class="badge bg-primary-lt text-primary border border-primary-subtle px-3 py-2 fw-bold rounded-pill" style="font-size: 0.8rem;">
                                    <?= number_format(count($transactions)) ?> Transaksi Ditemukan
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if ($this->session->flashdata('success')): ?>
                                <div class="alert alert-success m-3 rounded-3 shadow-sm border-0">
                                    <div class="d-flex">
                                        <div><i class="fas fa-check-circle me-2 fs-3"></i></div>
                                        <div><?= $this->session->flashdata('success') ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger m-3 rounded-3 shadow-sm border-0">
                                    <div class="d-flex">
                                        <div><i class="fas fa-exclamation-circle me-2 fs-3"></i></div>
                                        <div><?= $this->session->flashdata('error') ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="table-responsive">
                                <table class="table table-vcenter table-hover card-table mb-0" id="tableMarketplace">
                                    <thead class="table-light text-muted text-uppercase fw-bolder" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                        <tr>
                                            <th class="ps-4">Tgl & Waktu</th>
                                            <th>No SPK</th>
                                            <th>Konsumen</th>
                                            <th>Platform</th>
                                            <th>Kurir</th>
                                            <th>No. Resi</th>
                                            <th>Status</th>
                                            <th class="text-center pe-4">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($transactions as $t): ?>
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="fw-bold text-dark"><?= date('d M Y', strtotime($t->created_at)) ?></div>
                                                    <div class="text-muted small"><i class="far fa-clock me-1"></i><?= date('H:i', strtotime($t->created_at)) ?></div>
                                                </td>
                                                <td>
                                                    <span class="spk-code" onclick="copyToClipboard('<?= $t->nospk ?>')" title="Klik untuk Copy">
                                                        <?= $t->nospk ?> <i class="far fa-copy text-muted ms-1" style="font-size: 0.75rem;"></i>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2 text-primary fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            <?= strtoupper(substr($t->konsumen_nama, 0, 1)) ?>
                                                        </div>
                                                        <div class="fw-semibold text-dark"><?= $t->konsumen_nama ?></div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php
                                                    $plat = strtolower($t->marketplace_nama ?: '');
                                                    $bg_style = 'background-color: #e0f2fe; color: #0284c7;'; // default light blue
                                                    if (strpos($plat, 'tokopedia') !== false) {
                                                        $bg_style = 'background-color: #dcfce7 !important; color: #166534; border: 1px solid #bbf7d0;';
                                                    } elseif (strpos($plat, 'shopee') !== false) {
                                                        $bg_style = 'background-color: #ffedd5 !important; color: #9a3412; border: 1px solid #fed7aa;';
                                                    } elseif (strpos($plat, 'tiktok') !== false) {
                                                        $bg_style = 'background-color: #f1f5f9 !important; color: #0f172a; border: 1px solid #e2e8f0;';
                                                    } elseif (strpos($plat, 'lazada') !== false) {
                                                        $bg_style = 'background-color: #e0e7ff !important; color: #3730a3; border: 1px solid #c7d2fe;';
                                                    }
                                                    ?>
                                                    <span class="badge rounded-pill px-3 py-1 fw-bold" style="<?= $bg_style ?>">
                                                        <?= $t->marketplace_nama ?: 'LAINNYA' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if($t->nama_pengiriman): ?>
                                                        <span class="text-secondary fw-medium"><i class="fas fa-truck text-muted me-1 small"></i> <?= $t->nama_pengiriman ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted fst-italic">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if($t->resi_marketplace): ?>
                                                        <span class="resi-badge d-inline-flex align-items-center gap-1" onclick="copyToClipboard('<?= $t->resi_marketplace ?>')" title="Copy Resi">
                                                            <?= $t->resi_marketplace ?> <i class="far fa-copy text-muted" style="font-size: 0.7rem;"></i>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-muted border fst-italic fw-normal">Belum diinput</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($t->status_pembayaran == 'success'): ?>
                                                        <span class="badge bg-success-lt text-success px-2 py-1 border border-success-subtle rounded-pill"><i class="fas fa-check-circle me-1"></i> Lunas</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning-lt text-warning px-2 py-1 border border-warning-subtle rounded-pill"><i class="fas fa-clock me-1"></i> Proses</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center pe-4 d-print-none">
                                                    <div class="d-flex gap-1 justify-content-center">
                                                        <a href="<?= base_url('marketplace/detail/' . $t->nospk) ?>" class="btn btn-sm btn-primary btn-icon shadow-sm" title="Lihat Detail Pesanan">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?= base_url('transaksi/preview_invoice/' . $t->nospk) ?>" target="_blank" class="btn btn-sm btn-light border btn-icon shadow-sm text-secondary" title="Cetak / Preview SPK">
                                                            <i class="fas fa-print"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        if ($.fn.DataTable) {
            $('#tableMarketplace').DataTable({
                "order": [[0, "desc"]],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
                },
                "drawCallback": function(settings) {
                    // Re-initialize tooltips if any after draw
                }
            });
        }
    });

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Optional: Show a subtle toast or alert
            alert("Tersalin: " + text);
        }, function(err) {
            console.error('Gagal menyalin text: ', err);
        });
    }
</script>