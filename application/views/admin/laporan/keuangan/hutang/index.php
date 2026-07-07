<?php
/**
 * Laporan Hutang Perusahaan - Premium UI/UX Edition
 * Displays company payables with supplier grouping and payment management.
 */
?>

<!-- DataTables RowGroup -->
<link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.4.1/css/rowGroup.dataTables.min.css">
<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">

<style>
    /* ==========================================================================
    TGM PREMIUM ACCOUNTS PAYABLE (HUTANG)
    ========================================================================== */
    :root {
        --fin-primary: #0f172a;
        --fin-accent: #3b82f6;
        --fin-success: #10b981;
        --fin-danger: #ef4444;
        --fin-warning: #f59e0b;
        --fin-info: #06b6d4;
        --fin-bg: #f8fafc;
        --card-radius: 20px;
    }

    .app-content {
        background-color: var(--fin-bg);
        min-height: 100vh;
        font-family: 'Inter', sans-serif;
    }

    /* Filter HUD Styling */
    .filter-hud {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        margin-bottom: 2rem;
    }

    .filter-hud .form-select,
    .filter-hud .form-control {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 0.6rem 1rem;
        font-weight: 700;
        font-size: 0.85rem;
    }

    /* Premium Stat Cards */
    .cash-stat-card {
        background: white;
        border-radius: var(--card-radius);
        padding: 1.5rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .cash-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    }

    .cash-stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        margin-bottom: 0.75rem;
    }

    .cash-stat-value {
        font-size: 1.4rem;
        font-weight: 900;
        color: var(--fin-primary);
        letter-spacing: -0.5px;
        line-height: 1.2;
    }

    .cash-stat-label {
        font-size: 0.7rem;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.25rem;
    }

    /* Content Card */
    .content-card {
        background: white;
        border-radius: var(--card-radius);
        border: 1px solid #e2e8f0;
        box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .card-header-premium {
        background: var(--fin-primary);
        color: white;
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Table Styling */
    .table-tx thead th {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        padding: 1rem 1.25rem;
        font-weight: 800;
        color: #64748b;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .table-tx tbody td {
        padding: 1rem 1.25rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
    }

    .monospaced {
        font-family: 'JetBrains Mono', 'SF Mono', monospace;
    }

    /* Badges */
    .badge-premium {
        padding: 0.4rem 0.75rem;
        border-radius: 8px;
        font-weight: 800;
        font-size: 0.65rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .badge-success-soft {
        background: #dcfce7;
        color: #166534;
    }

    .badge-warning-soft {
        background: #fef9c3;
        color: #854d0e;
    }

    .badge-danger-soft {
        background: #fee2e2;
        color: #991b1b;
    }

    /* Row Group Styling */
    .dtrg-group {
        background: #f8fafc !important;
    }

    .dtrg-group td {
        padding: 0.75rem 1.5rem !important;
        border-left: 4px solid var(--fin-accent) !important;
        background: #eff6ff !important;
    }

    .subtotal-label {
        font-weight: 900;
        color: var(--fin-primary);
        font-size: 0.8rem;
    }

    .subtotal-value {
        font-weight: 900;
        color: var(--fin-danger);
        font-family: 'JetBrains Mono', monospace;
    }

    /* Action Button */
    .btn-pay-modern {
        background: var(--fin-success);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 10px;
        font-weight: 800;
        font-size: 0.7rem;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    }

    .btn-pay-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(16, 185, 129, 0.3);
        color: white;
    }

    @media print {
        .no-print {
            display: none !important;
        }

        .app-content {
            background: white;
            padding: 0 !important;
        }

        .content-card {
            box-shadow: none;
            border: 1px solid #eee;
        }
    }
</style>

<div class="app-content mt-3 pb-5">
    <?php $this->load->view('layout_admin/header_modul'); ?>

    <div class="container-fluid py-4">
        <!-- TOP FILTER HUD -->
        <div class="filter-hud no-print">
            <form action="<?= base_url('laporan/hutang') ?>" method="GET" class="row align-items-center g-3">
                <div class="col-xl-4 col-lg-12">
                    <div class="d-flex align-items-center gap-3">
                        <a href="<?= base_url('hr') ?>"
                            class="btn btn-outline-secondary border-2 fw-900 rounded-3 px-3 shadow-sm d-flex align-items-center justify-content-center"
                            style="height: 48px;" title="Kembali ke Dashboard">
                            <i class="fas fa-arrow-left fs-5"></i>
                        </a>
                        <div class="p-3 bg-light rounded-3 text-danger border shadow-sm">
                            <i class="fas fa-file-invoice-dollar fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-900 text-dark ls-n1">LEDGER HUTANG</h6>
                            <p class="text-muted small mb-0 fw-bold">Monitoring & Pembayaran Supplier</p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-6">
                    <?php if ($is_admin_owner && !empty($all_perusahaan)): ?>
                        <select class="form-select" name="perusahaan_id" onchange="this.form.submit()">
                            <?php foreach ($all_perusahaan as $p): ?>
                                <option value="<?= $p->id_perusahaan ?>" <?= ($current_perusahaan_id == $p->id_perusahaan) ? 'selected' : '' ?>>
                                    🏢 <?= $p->nama_perusahaan ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <input type="text" class="form-control" value="<?= $perusahaan ?>" readonly>
                        <input type="hidden" name="perusahaan_id" value="<?= $current_perusahaan_id ?>">
                    <?php endif; ?>
                </div>

                <div class="col-xl-4 col-lg-6 text-end">
                    <div class="d-flex gap-2 justify-content-end">
                        <div class="input-group" style="max-width: 200px;">
                            <span class="input-group-text bg-white border-end-0"><i
                                    class="fa fa-search text-muted"></i></span>
                            <input type="text" id="customSearch" class="form-control border-start-0"
                                placeholder="Cari data...">
                        </div>
                        <button type="button" class="btn btn-outline-primary border-2 fw-900 rounded-3 px-3 shadow-sm"
                            onclick="location.reload()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <button type="button" class="btn btn-dark rounded-3 py-2 px-3 fw-900 shadow-sm"
                            onclick="window.print()" title="Cetak Data">
                            <i class="fas fa-print"></i>
                        </button>
                        <button type="button" class="btn btn-success rounded-3 py-2 px-3 fw-900 shadow-sm"
                            id="btnExportExcel" title="Export ke Excel">
                            <i class="fas fa-file-excel"></i>
                        </button>
                        <button type="button" class="btn btn-primary rounded-3 py-2 px-3 fw-900 shadow-sm"
                            data-bs-toggle="modal" data-bs-target="#modalAddHutang"
                            title="Input Manual (Leasing/Lain-lain)">
                            <i class="fas fa-plus"></i> <span class="d-none d-xl-inline">TAGIHAN</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- KPI GRID -->
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6">
                <div class="cash-stat-card border-bottom border-danger border-4">
                    <div class="cash-stat-icon bg-danger bg-opacity-10 text-danger"><i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="cash-stat-label">Total Hutang</div>
                    <div class="cash-stat-value">Rp <?= number_format($total_hutang, 0, ',', '.') ?></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="cash-stat-card border-bottom border-success border-4">
                    <div class="cash-stat-icon bg-success bg-opacity-10 text-success"><i
                            class="fas fa-check-double"></i></div>
                    <div class="cash-stat-label">Total Terbayar</div>
                    <div class="cash-stat-value text-success">Rp <?= number_format($total_bayar, 0, ',', '.') ?></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="cash-stat-card border-bottom border-warning border-4">
                    <div class="cash-stat-icon bg-warning bg-opacity-10 text-warning"><i
                            class="fas fa-balance-scale-right"></i></div>
                    <div class="cash-stat-label">Sisa Hutang</div>
                    <div class="cash-stat-value text-warning">Rp <?= number_format($total_sisa, 0, ',', '.') ?></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="cash-stat-card border-bottom border-info border-4">
                    <div class="cash-stat-icon bg-info bg-opacity-10 text-info"><i class="fas fa-clock"></i></div>
                    <div class="cash-stat-label">Nota Jatuh Tempo</div>
                    <div class="cash-stat-value text-info"><?= number_format($jatuh_tempo_count) ?> <small
                            class="fs-6">FAKTUR</small></div>
                </div>
            </div>
        </div>

        <!-- MAIN TABLE CARD -->
        <div class="content-card shadow-sm">
            <div class="card-header-premium">
                <h6 class="fw-900 mb-0 ls-n1"><i class="fas fa-stream me-2 opacity-50"></i>RINCIAN HUTANG SUPPLIER</h6>
                <div class="d-flex gap-2">
                    <select
                        class="form-select form-select-sm border-0 bg-white bg-opacity-10 text-white fw-bold py-1 px-3"
                        id="statusFilter" style="border-radius: 8px;">
                        <option value="" class="text-dark">SEMUA STATUS</option>
                        <option value="BELUM LUNAS" <?= ($status_filter == 'BELUM LUNAS') ? 'selected' : '' ?>
                            class="text-dark">BELUM LUNAS</option>
                        <option value="SEBAGIAN" <?= ($status_filter == 'SEBAGIAN') ? 'selected' : '' ?> class="text-dark">
                            SEBAGIAN</option>
                        <option value="LUNAS" <?= ($status_filter == 'LUNAS') ? 'selected' : '' ?> class="text-dark">LUNAS
                        </option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table id="hutangTable" class="table table-tx table-hover mb-0 w-100">
                    <thead>
                        <tr>
                            <th class="text-center" width="50">NO</th>
                            <th>SUPPLIER</th>
                            <th>NO FAKTUR</th>
                            <th>TGL BELI</th>
                            <th class="text-end">TAGIHAN</th>
                            <th class="text-end text-success">TERBAYAR</th>
                            <th class="text-end text-danger">SISA</th>
                            <th class="text-center">JATUH TEMPO</th>
                            <th class="text-center">STATUS</th>
                            <th class="text-center no-print">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        $today = date('Y-m-d');
                        foreach ($tampilData as $dt): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= $dt->supplier_nama ?></td>
                                <td>
                                    <div class="fw-900 text-dark"><?= $dt->no_faktur ?></div>
                                    <small class="fw-bold text-muted text-uppercase"
                                        style="font-size: 0.65rem;">#<?= $dt->supplier_id ?> -
                                        <?= $dt->supplier_nama ?></small>
                                </td>
                                <td class="monospaced small fw-bold"><?= date('d M Y', strtotime($dt->pembelian_tgl)) ?>
                                </td>
                                <td class="text-end fw-bold monospaced">Rp
                                    <?= number_format($dt->total_tagihan, 0, ',', '.') ?>
                                </td>
                                <td class="text-end text-success fw-bold monospaced">Rp
                                    <?= number_format($dt->total_bayar, 0, ',', '.') ?>
                                </td>
                                <td class="text-end text-danger fw-900 monospaced">Rp
                                    <?= number_format($dt->sisa_hutang, 0, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <div
                                            class="monospaced small fw-900 <?= ($dt->jatuh_tempo <= $today && $dt->status != 'LUNAS') ? 'text-danger' : 'text-muted' ?>">
                                            <?= date('d/m/y', strtotime($dt->jatuh_tempo)) ?>
                                        </div>
                                        <?php if ($dt->status != 'LUNAS'):
                                            $jt = strtotime($dt->jatuh_tempo);
                                            $now = strtotime($today);
                                            if ($jt < $now): ?>
                                                <span class="badge bg-danger rounded-pill mt-1"
                                                    style="font-size: 0.55rem; padding: 0.2rem 0.5rem;">LEWAT</span>
                                            <?php elseif ($jt == $now): ?>
                                                <span class="badge bg-warning text-dark rounded-pill mt-1"
                                                    style="font-size: 0.55rem; padding: 0.2rem 0.5rem;">HARI INI</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $cls = 'badge-danger-soft';
                                    if ($dt->status == 'LUNAS')
                                        $cls = 'badge-success-soft';
                                    if ($dt->status == 'SEBAGIAN')
                                        $cls = 'badge-warning-soft';
                                    ?>
                                    <span class="badge badge-premium <?= $cls ?>"><?= $dt->status ?></span>
                                </td>
                                <td class="text-center no-print">
                                    <?php if ($dt->status != 'LUNAS'): ?>
                                        <button class="btn-pay-modern"
                                            onclick="showPaymentModal('<?= $dt->hutang_id ?>', '<?= addslashes($dt->supplier_nama) ?>', '<?= $dt->no_faktur ?>', '<?= $dt->sisa_hutang ?>')">
                                            <i class="fas fa-coins me-1"></i> BAYAR
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Hutang Manual -->
<div class="modal fade" id="modalAddHutang" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="<?= base_url('laporan/tambah_hutang_manual') ?>" method="POST"
            class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px;">
                        <i class="fas fa-file-invoice text-primary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-900 ls-1 mb-0 text-uppercase">Input Tagihan Manual</h5>
                        <small class="text-white-50 fw-bold">Tambahkan hutang leasing, sewa, atau operasional</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4 p-md-5 bg-light">
                <div class="row g-4 bg-white p-4 rounded-4 shadow-sm">
                    <div class="col-md-6">
                        <label class="form-label fw-900 text-dark small text-uppercase ls-1">Pihak Penagih
                            (Supplier/Vendor)</label>
                        <select class="form-select form-control-premium fw-bold" name="supplier_id" required>
                            <option value="" disabled selected>Pilih Penagih...</option>
                            <?php if (!empty($all_supplier)):
                                foreach ($all_supplier as $sup): ?>
                                    <option value="<?= $sup->supplier_id ?>"><?= $sup->supplier_nama ?></option>
                                <?php endforeach; endif; ?>
                        </select>
                        <small class="text-muted" style="font-size: 0.7rem;">Tidak ada di list? <a
                                href="<?= site_url('pengaturan/supplier') ?>">Tambah Master Supplier</a></small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-900 text-dark small text-uppercase ls-1">Nomor
                            Referensi/Faktur</label>
                        <input type="text" class="form-control form-control-premium fw-bold" name="no_faktur" required
                            placeholder="Mis: LEASING-01, SEWA-2026, INVOICE-X">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-900 text-dark small text-uppercase ls-1">Tanggal Tagihan
                            Diterima</label>
                        <input type="date" class="form-control form-control-premium fw-bold" name="tanggal_tagihan"
                            required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-900 text-danger small text-uppercase ls-1">Jatuh Tempo
                            Pembayaran</label>
                        <input type="date" class="form-control form-control-premium fw-bold text-danger"
                            name="jatuh_tempo" required>
                    </div>

                    <div class="col-12 mt-4">
                        <div class="p-4 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-3">
                            <label class="form-label fw-900 text-dark small text-uppercase ls-1 mb-2">Total Tagihan
                                (Rp)</label>
                            <div class="input-group">
                                <span
                                    class="input-group-text bg-white border-warning border-end-0 fw-bold text-muted">Rp</span>
                                <input type="text"
                                    class="form-control fw-900 text-warning fs-4 border-warning border-start-0 ps-0"
                                    id="inputTotalTagihan" name="total_tagihan" required placeholder="0">
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-900 text-dark small text-uppercase ls-1">Keterangan /
                            Deskripsi</label>
                        <textarea class="form-control form-control-premium" name="keterangan" rows="2"
                            placeholder="Detail keperluan... misal: Cicilan Mesin Potong Ke-12"></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-white p-4 border-top">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-800"
                    data-bs-dismiss="modal">BATALKAN</button>
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-900 shadow">SIMPAN HUTANG</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pembayaran (Premium Style) -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="paymentForm" action="<?= base_url('laporan/update_pembayaran_hutang') ?>" method="POST"
            class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-circle p-2 me-3">
                        <i class="fas fa-wallet text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-900 ls-1 mb-0 text-uppercase">Input Pembayaran Hutang</h5>
                        <small class="text-white-50 fw-bold">Validasi pengeluaran dana supplier</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-light">
                <input type="hidden" id="hutangId" name="hutang_id">

                <div class="bg-primary bg-opacity-10 p-4 border-bottom border-primary border-opacity-10">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <small class="text-muted d-block fw-bold text-uppercase ls-1">Informasi Faktur</small>
                            <h4 id="supplierName" class="fw-900 text-dark mb-1 fs-5">-</h4>
                            <div class="d-flex gap-2 align-items-center">
                                <span class="badge bg-white text-dark border fw-bold" id="noFaktur">-</span>
                            </div>
                        </div>
                        <div class="col-md-5 text-md-end mt-3 mt-md-0">
                            <small class="text-muted d-block fw-bold text-uppercase ls-1">Sisa Tagihan</small>
                            <h3 id="sisaHutangDisplay" class="fw-900 text-danger mb-0 monospaced">-</h3>
                        </div>
                    </div>
                </div>

                <div class="p-4 p-md-5 bg-white">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-900 text-dark small text-uppercase ls-1">Jumlah
                                Pembayaran</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 fw-bold text-muted">Rp</span>
                                <input type="text"
                                    class="form-control form-control-premium fw-900 text-primary fs-5 border-start-0 ps-0"
                                    id="jumlahBayar" name="jumlah_bayar" required placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-900 text-dark small text-uppercase ls-1">Metode
                                Pembayaran</label>
                            <select class="form-select form-control-premium fw-bold" id="metodeBayar"
                                name="metode_bayar" required>
                                <option value="" disabled selected>Pilih...</option>
                                <option value="cash">TUNAI (CASH LACI)</option>
                                <option value="transfer">BANK TRANSFER</option>
                            </select>
                        </div>
                        <div class="col-12" id="transferDetails" style="display: none;">
                            <div class="p-3 bg-info bg-opacity-10 border border-info border-opacity-20 rounded-3">
                                <label class="form-label fw-900 text-info small text-uppercase ls-1">Detail Bank /
                                    Rekening Tujuan</label>
                                <input type="text"
                                    class="form-control form-control-premium border-info border-opacity-25"
                                    name="rekening_tujuan" placeholder="Contoh: BCA - 12345678 a/n Supplier">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-900 text-dark small text-uppercase ls-1">Catatan
                                Internal</label>
                            <textarea class="form-control form-control-premium" name="keterangan" rows="2"
                                placeholder="Detail cicilan atau penanggung jawab..."></textarea>
                        </div>
                    </div>

                    <div id="paymentPreview" class="mt-4 p-4 rounded-4 bg-light border border-dashed text-center"
                        style="display: none;">
                        <small class="text-muted d-block fw-bold mb-1 uppercase">SISA SETELAH PEMBAYARAN:</small>
                        <h4 id="previewSisa" class="fw-900 text-dark mb-0">-</h4>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light p-4 border-top">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-800"
                    data-bs-dismiss="modal">BATALKAN</button>
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-900 shadow">SIMPAN PEMBAYARAN</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.datatables.net/rowgroup/1.4.1/js/dataTables.rowGroup.min.js"></script>

<script>
    $(document).ready(function () {
        const table = $('#hutangTable').DataTable({
            pageLength: <?= $limit_filter ?: 50 ?>,
            order: [[1, 'asc']],
            language: {
                url: '<?= base_url('assets/js/datatables-id.json') ?>',
                info: "MENAMPILKAN _START_ - _END_ DARI _TOTAL_ DATA",
                paginate: { "previous": "<i class='fas fa-chevron-left'></i>", "next": "<i class='fas fa-chevron-right'></i>" }
            },
            dom: "<'row'<'col-sm-12'tr>>" +
                "<'row p-4'<'col-sm-5 small fw-bold text-muted'i><'col-sm-7'p>>",
            rowGroup: {
                dataSrc: 1,
                startRender: function (rows, group) {
                    let totalSisa = rows.data().pluck(6).reduce(function (a, b) {
                        let valA = typeof a === 'string' ? a.replace(/[^\d]/g, '') : a;
                        let valB = typeof b === 'string' ? b.replace(/[^\d]/g, '') : b;
                        return parseInt(valA) + parseInt(valB);
                    }, 0);

                    return $('<tr/>').append('<td colspan="9" class="dtrg-group"><div class="d-flex justify-content-between align-items-center"><span class="subtotal-label"><i class="fas fa-truck-loading me-2 opacity-50"></i>' + group.toUpperCase() + '</span> <span class="badge bg-white border text-danger fw-900 monospaced px-3 py-2 rounded-3">Total Tagihan: Rp ' + totalSisa.toLocaleString('id-ID') + '</span></div></td>');
                }
            },
            columnDefs: [{ visible: false, targets: 1 }]
        });

        $('#customSearch').on('keyup', function () { table.search(this.value).draw(); });

        $('#statusFilter').on('change', function () {
            if (this.value) table.column(8).search('^' + this.value + '$', true, false).draw();
            else table.column(8).search('').draw();
        });

        // Format input rupiah for Tambah Hutang Manual
        $('#inputTotalTagihan').on('input', function () {
            let value = this.value.replace(/[^\d]/g, '');
            if (value) {
                $(this).val(parseInt(value).toLocaleString('id-ID'));
            } else {
                $(this).val('');
            }
        });

        $('#jumlahBayar').on('input', function () {
            let value = this.value.replace(/[^\d]/g, '');
            if (value) {
                $(this).val(parseInt(value).toLocaleString('id-ID'));
                const sisaNow = parseInt($('#hutangSisaRaw').val());
                const bayar = parseInt(value);
                const remaining = sisaNow - bayar;
                $('#previewSisa').text(remaining <= 0 ? 'LUNAS (DONE)' : 'Rp ' + remaining.toLocaleString('id-ID'));
                $('#paymentPreview').fadeIn();
            } else {
                $('#paymentPreview').fadeOut();
            }
        });

        $('#metodeBayar').on('change', function () {
            const met = $(this).val();
            $('#transferDetails').hide();
            if (met === 'transfer') $('#transferDetails').fadeIn();
        });

        $('#btnExportExcel').on('click', function () {
            window.location.href = '<?= base_url('laporan/export_laporan_hutang') ?>';
        });

        $('#paymentForm').on('submit', function (e) {
            const rawBayar = $('#jumlahBayar').val().replace(/[^\d]/g, '');
            const sisaNow = parseInt($('#hutangSisaRaw').val());
            if (parseInt(rawBayar) > sisaNow) {
                e.preventDefault();
                Swal.fire('Oops!', 'Jumlah bayar tidak boleh melebihi sisa hutang!', 'error');
                return false;
            }
            $('#jumlahBayar').val(rawBayar);
            return true;
        });
    });

    function showPaymentModal(id, supplier, faktur, sisa) {
        $('#hutangId').val(id);
        $('#supplierName').text(supplier);
        $('#noFaktur').text(faktur);
        $('#sisaHutangDisplay').text('Rp ' + parseInt(sisa).toLocaleString('id-ID'));
        if ($('#hutangSisaRaw').length == 0) $('body').append('<input type="hidden" id="hutangSisaRaw">');
        $('#hutangSisaRaw').val(sisa);
        $('#jumlahBayar').val('');
        $('#transferDetails').hide();
        $('#paymentPreview').hide();
        $('#paymentModal').modal('show');
    }
</script>