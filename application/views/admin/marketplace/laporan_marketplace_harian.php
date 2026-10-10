<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

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
            <div class="col-md-7">
                <div class="dashboard-content">
                    <div class="dashboard-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="dashboard-text">
                        <p class="dashboard-subtitle">Laporan Keuangan</p>
                        <h1 class="dashboard-title">Laporan Marketplace <span class="ms-1" style="font-size: 1rem; opacity: 0.8;">(HARIAN)</span></h1>
                    </div>
                </div>
                <p class="text-white-50 small mb-0 mt-3 position-relative" style="z-index: 2;">
                    Transaksi marketplace <strong>bypass kasir</strong> — langsung masuk produksi. Data bersumber dari <code>tbl_pemesanan</code> + <code>tbl_pembayaran_produk</code>.
                </p>
            </div>
            <div class="col-md-5 text-end d-flex gap-2 justify-content-md-end justify-content-center header-actions-mobile flex-wrap position-relative mt-3 mt-md-0" style="z-index: 2;">
                <?php $this->load->view('admin/marketplace/_header_menu'); ?>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <!-- FILTER -->
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-body py-2">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label mb-1 fw-semibold">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control form-control-sm" value="<?= $hariini ?>" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1 fw-semibold">Platform Marketplace</label>
                        <select name="marketplace_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">— Semua Platform —</option>
                            <?php foreach ($all_marketplace as $mk): ?>
                            <option value="<?= $mk->marketplace_id ?>" <?= ($mk->marketplace_id == $market_id_filter) ? 'selected' : '' ?>>
                                <?= $mk->marketplace_nama ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                        <a href="<?= base_url('marketplace/laporan_marketplace_harian') ?>" class="btn btn-outline-secondary btn-sm ms-1">Reset</a>
                    </div>
                    <div class="col-auto ms-auto d-flex gap-2">
                        <a href="<?= site_url('marketplace/laporan_marketplace') ?>" class="btn btn-outline-info btn-sm fw-bold" title="Versi Bulanan">
                            <i class="fas fa-calendar-alt me-1"></i> Versi Bulanan
                        </a>
                        <button type="button" onclick="window.print()" class="btn btn-secondary btn-sm fw-bold">
                            <i class="fas fa-print me-1"></i> Cetak
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- STAT CARDS -->
        <div class="row row-deck row-cards mb-3">
            <?php $sk = $stat_keuangan; ?>

            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <span class="me-2" style="font-size:1.5rem">🧾</span>
                            <span class="text-muted small fw-semibold">Total Transaksi Selesai</span>
                        </div>
                        <div class="h2 fw-bold text-primary mb-0"><?= number_format($sk['total_transaksi']) ?></div>
                        <?php if ($sk['total_pending_trx'] > 0): ?>
                        <small class="text-warning">+ <?= $sk['total_pending_trx'] ?> pending</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <span class="me-2" style="font-size:1.5rem">💰</span>
                            <span class="text-muted small fw-semibold">Omset Marketplace</span>
                        </div>
                        <div class="h2 fw-bold text-success mb-0">Rp <?= number_format($sk['omset_penjualan'], 0, ',', '.') ?></div>
                        <small class="text-muted">Sudah masuk produksi</small>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <span class="me-2" style="font-size:1.5rem">📊</span>
                            <span class="text-muted small fw-semibold">Rata-rata per Transaksi</span>
                        </div>
                        <?php $avg = $sk['total_transaksi'] > 0 ? $sk['omset_penjualan'] / $sk['total_transaksi'] : 0; ?>
                        <div class="h2 fw-bold text-info mb-0">Rp <?= number_format($avg, 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <span class="me-2" style="font-size:1.5rem">⏳</span>
                            <span class="text-muted small fw-semibold">Estimasi Pending</span>
                        </div>
                        <div class="h2 fw-bold text-warning mb-0">Rp <?= number_format($sk['total_pending'], 0, ',', '.') ?></div>
                        <small class="text-muted">Belum masuk produksi</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- ALERT INFO -->
        <div class="alert alert-info mb-3" style="border-left:4px solid #0d6efd;">
            <div class="d-flex">
                <div class="me-2" style="font-size:1.2rem">ℹ️</div>
                <div>
                    <strong>Cara kerja Marketplace:</strong> Transaksi marketplace <em>bypass kasir</em>, langsung masuk antrian produksi begitu SPK dibuat.
                    Nilai omset dihitung dari <strong>snapshot harga saat order dibuat</strong>. Pembayaran dari Shopee/Tokopedia/dll masuk secara periodik dari platform tersebut.
                </div>
            </div>
        </div>

        <div class="row row-cards mb-3">
            <!-- BREAKDOWN PER PLATFORM -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <h4 class="card-title mb-0">📊 Per Platform Marketplace</h4>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($by_platform)): ?>
                        <div class="p-4 text-center text-muted">Belum ada data marketplace periode ini.</div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Platform</th>
                                        <th class="text-center">Trx</th>
                                        <th class="text-end">Omset</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $total_grand_omset = array_sum(array_column($by_platform, 'total_omset'));
                                foreach ($by_platform as $bp):
                                    $pct = $total_grand_omset > 0 ? ($bp['total_omset'] / $total_grand_omset * 100) : 0;
                                ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($bp['platform']) ?></div>
                                        <div class="progress mt-1" style="height:3px">
                                            <div class="progress-bar bg-orange" style="width:<?= round($pct) ?>%"></div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-azure-lt"><?= $bp['jumlah_transaksi'] ?></span>
                                    </td>
                                    <td class="text-end text-success fw-semibold">
                                        Rp <?= number_format($bp['total_omset'], 0, ',', '.') ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-light fw-bold">
                                    <tr>
                                        <td>TOTAL</td>
                                        <td class="text-center"><?= $sk['total_transaksi'] ?></td>
                                        <td class="text-end text-success">Rp <?= number_format($sk['omset_penjualan'], 0, ',', '.') ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- TRANSAKSI PENDING -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pb-0 d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">⏳ SPK Marketplace Pending</h4>
                        <span class="badge bg-warning-lt text-warning"><?= count($pending_list) ?> belum masuk produksi</span>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($pending_list)): ?>
                        <div class="p-4 text-center text-muted">
                            <span style="font-size:1.5rem">✅</span><br>
                            Semua pesanan marketplace sudah masuk produksi.
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-warning">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>No. SPK</th>
                                        <th>Platform</th>
                                        <th>Resi</th>
                                        <th class="text-end">Estimasi Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($pending_list as $p): ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($p['tanggal'])) ?></td>
                                    <td>
                                        <a href="javascript:void(0)" class="fw-semibold text-primary btn-detail-spk" data-nospk="<?= $p['nospk'] ?>">
                                            <?= htmlspecialchars($p['nospk']) ?>
                                        </a>
                                    </td>
                                    <td><span class="badge bg-orange-lt text-orange"><?= htmlspecialchars($p['platform']) ?></span></td>
                                    <td class="text-muted small"><?= htmlspecialchars($p['resi'] ?: '-') ?></td>
                                    <td class="text-end text-warning fw-semibold">
                                        Rp <?= number_format($p['total_estimasi'], 0, ',', '.') ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-light fw-bold">
                                    <tr>
                                        <td colspan="4" class="text-end pe-3">Total Pending</td>
                                        <td class="text-end text-warning">Rp <?= number_format($sk['total_pending'], 0, ',', '.') ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABEL TRANSAKSI SELESAI -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">🧾 Transaksi Marketplace Selesai (<?= date('d M Y', strtotime($hariini)) ?>)</h4>
                <span class="badge bg-success-lt text-success"><?= count($pemasukan_list) ?> transaksi</span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($pemasukan_list)): ?>
                <div class="p-5 text-center text-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2 text-muted" width="48" height="48" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                    <div>Tidak ada transaksi marketplace selesai untuk tanggal <strong><?= date('d M Y', strtotime($hariini)) ?></strong></div>
                    <small class="text-muted">Pastikan transaksi marketplace sudah berstatus <code>paid</code></small>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-vcenter table-hover table-sm mb-0" id="tbl-marketplace">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center" style="width:40px">#</th>
                                <th>Tanggal</th>
                                <th>No. SPK</th>
                                <th>Platform</th>
                                <th>Resi / Order ID</th>
                                <th>Konsumen</th>
                                <th class="text-end">Nilai Transaksi</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1; $grand_total = 0; foreach ($pemasukan_list as $row): $grand_total += $row['total_tagihan']; ?>
                        <tr>
                            <td class="text-center text-muted small"><?= $no++ ?></td>
                            <td>
                                <div class="fw-semibold"><?= date('d M Y', strtotime($row['tanggal'])) ?></div>
                                <small class="text-muted"><?= substr($row['jam'], 0, 5) ?></small>
                            </td>
                            <td>
                                <a href="javascript:void(0)" class="fw-semibold text-primary btn-detail-spk" data-nospk="<?= $row['nospk'] ?>">
                                    <?= htmlspecialchars($row['nospk']) ?>
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-orange-lt text-orange"><?= htmlspecialchars($row['platform']) ?></span>
                            </td>
                            <td class="text-muted small"><?= htmlspecialchars($row['resi'] ?: '-') ?></td>
                            <td>
                                <div><?= htmlspecialchars($row['konsumen'] ?: '-') ?></div>
                                <?php if ($row['telepon']): ?>
                                <small class="text-muted"><?= $row['telepon'] ?></small>
                                <?php endif; ?>
                            </td>
                            <td class="text-end fw-semibold text-success">
                                Rp <?= number_format($row['total_tagihan'], 0, ',', '.') ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success-lt text-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xs me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="5 12 10 17 20 7"/></svg>
                                    Di Produksi
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="6" class="text-end pe-3">TOTAL (<?= count($pemasukan_list) ?> transaksi)</td>
                                <td class="text-end text-success">Rp <?= number_format($grand_total, 0, ',', '.') ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div><!-- /container-xl -->
</div><!-- /page-body -->

<style>
@media print {
    .page-header, form, .alert { display: none !important; }
    .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; }
    .btn { display: none !important; }
}
</style>

<!-- ===================== MODAL DETAIL SPK ===================== -->
<div class="modal fade" id="modalDetailSpk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">

            <!-- Loading Spinner -->
            <div id="modalSpinner" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                <p class="mt-2 text-muted">Memuat data transaksi...</p>
            </div>

            <!-- Modal Content (hidden until loaded) -->
            <div id="modalContent" style="display:none;">
                <!-- Header -->
                <div class="modal-header flex-column align-items-center border-0 pb-0 pt-4" style="background: linear-gradient(135deg, #1a1a2e, #16213e);">
                    <p class="text-white-50 text-uppercase small fw-bold letter-spacing mb-1">IDENTITAS TRANSAKSI</p>
                    <h2 id="modalNospk" class="text-white fw-900 display-6 mb-3"></h2>
                    <div class="d-flex gap-2 mb-3">
                        <span id="modalStatusBadge" class="badge fs-6 px-3 py-2"></span>
                        <a id="modalInvoiceBtn" href="#" target="_blank" class="btn btn-sm btn-light fw-bold">
                            <i class="fas fa-print me-1"></i> Cetak Invoice
                        </a>
                    </div>
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-0">
                    <!-- Detail Info Row -->
                    <div class="row g-0 border-bottom">
                        <!-- Detail Logistik -->
                        <div class="col-md-6 p-4 border-end">
                            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-truck me-2"></i>DETAIL LOGISTIK</h6>
                            <table class="table table-borderless table-sm mb-0">
                                <tr><td class="text-muted pe-3" style="width:130px">Tgl Transaksi</td><td class="fw-bold" id="modalTgl"></td></tr>
                                <tr><td class="text-muted">Jam Input</td><td class="fw-bold" id="modalJam"></td></tr>
                                <tr><td class="text-muted">Metode Utama</td><td id="modalMetode"></td></tr>
                                <tr><td class="text-muted">Terminal</td><td class="fw-bold text-primary" id="modalTerminal"></td></tr>
                                <tr><td class="text-muted">Kasir</td><td class="fw-bold" id="modalKasir"></td></tr>
                            </table>
                        </div>
                        <!-- Profil Konsumen -->
                        <div class="col-md-6 p-4">
                            <h6 class="fw-bold text-success mb-3"><i class="fas fa-user-circle me-2"></i>PROFIL KONSUMEN</h6>
                            <div class="fw-900 fs-5 mb-1" id="modalKonsumenNama"></div>
                            <div class="text-muted small mb-1" id="modalKonsumenEmail"></div>
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <i class="fas fa-phone text-success"></i>
                                <span class="fw-bold" id="modalKonsumenHp"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Rincian Item -->
                    <div class="p-4">
                        <h6 class="fw-bold text-warning mb-3"><i class="fas fa-list me-2"></i>RINCIAN ITEM PESANAN</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover" id="modalItemTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>PRODUK</th>
                                        <th class="text-center">QTY</th>
                                        <th class="text-end">HARGA</th>
                                        <th class="text-end">SUBTOTAL</th>
                                    </tr>
                                </thead>
                                <tbody id="modalItemBody"></tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <td colspan="4" class="text-end fw-bold">SUBTOTAL BRUTO</td>
                                        <td class="text-end fw-bold" id="modalSubtotalBruto"></td>
                                    </tr>
                                    <tr style="background: #f0f9f0;">
                                        <td colspan="4" class="text-end fw-bold fs-6">NET REVENUE</td>
                                        <td class="text-end fw-bold fs-5 text-success" id="modalNetRevenue"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ==================== END MODAL DETAIL SPK ==================== -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    var BASE_URL = '<?= base_url() ?>';
    
    // Click tombol detail (delegasi event)
    $(document).on('click', '.btn-detail-spk', function() {
        var nospk = $(this).data('nospk');
        
        // Reset modal
        $('#modalContent').hide();
        $('#modalSpinner').show();
        
        // Buka modal
        var modal = new bootstrap.Modal(document.getElementById('modalDetailSpk'));
        modal.show();
        
        // Ambil data via AJAX
        $.ajax({
            url: BASE_URL + 'laporan/ajax_detail_spk',
            type: 'POST',
            data: { nospk: nospk },
            dataType: 'json',
            success: function(res) {
                $('#modalSpinner').hide();
                if (!res.success) {
                    alert('Gagal memuat data: ' + res.message);
                    return;
                }
                
                var spk = res.spk;
                var items = res.items;
                var fmt = function(n) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(n || 0); };
                
                // Status Badge
                var statusMap = {
                    'done': ['bg-success', 'Selesai'],
                    'proses': ['bg-warning text-dark', 'Di Proses'],
                    'approved': ['bg-primary', 'Antri Produksi'],
                    'on_pb': ['bg-info text-dark', 'Di Packing'],
                    'on_costumer': ['bg-success', 'Diserahkan']
                };
                var statusData = statusMap[spk.status_produksi] || ['bg-secondary', 'Pending'];
                
                // Fill header
                $('#modalNospk').text(spk.nospk);
                $('#modalStatusBadge').attr('class', 'badge fs-6 px-3 py-2 ' + statusData[0]).text(statusData[1]);
                $('#modalInvoiceBtn').attr('href', BASE_URL + 'cekpesanan/cetak_invoice/' + spk.nospk);
                
                // Fill Logistik
                $('#modalTgl').text(spk.tgl_transaksi);
                $('#modalJam').text(spk.jam_transaksi);
                $('#modalMetode').html('<span class="badge bg-indigo-lt">' + spk.jenis_transaksi + '</span>');
                $('#modalTerminal').text(spk.nama_perusahaan);
                $('#modalKasir').text(spk.kasir || 'MARKETPLACE');
                
                // Fill Konsumen
                $('#modalKonsumenNama').text(spk.konsumen_nama);
                $('#modalKonsumenEmail').text(spk.konsumen_email || '-');
                $('#modalKonsumenHp').text(spk.konsumen_nohp || 'Belum ada nomor HP');
                
                // Fill Items
                var tbody = '';
                var subtotalBruto = 0;
                
                $.each(items, function(i, item) {
                    var hrg = parseFloat(item.harga_1);
                    var sub = parseFloat(item.subtotal);
                    subtotalBruto += sub;
                    
                    var pnj = parseFloat(item.panjang);
                    var lbr = parseFloat(item.lebar);
                    var dmns = '';
                    if (pnj > 0 && lbr > 0) dmns = '<small class="text-muted ms-2">[' + pnj + 'x' + lbr + ']</small>';
                    else if (pnj > 0) dmns = '<small class="text-muted ms-2">[' + pnj + 'm]</small>';
                    
                    tbody += '<tr>' +
                                '<td class="text-muted">' + (i+1) + '</td>' +
                                '<td><div class="fw-bold text-dark">' + item.detail_product + dmns + '</div>' +
                                '<div class="small text-muted">' + (item.keterangan || '') + '</div></td>' +
                                '<td class="text-center">' + item.qty + '</td>' +
                                '<td class="text-end">' + fmt(hrg) + '</td>' +
                                '<td class="text-end fw-bold">' + fmt(sub) + '</td>' +
                             '</tr>';
                });
                
                $('#modalItemBody').html(tbody);
                $('#modalSubtotalBruto').text(fmt(subtotalBruto));
                $('#modalNetRevenue').text(fmt(spk.total_tagihan || subtotalBruto));
                
                $('#modalContent').fadeIn();
            },
            error: function() {
                $('#modalSpinner').hide();
                alert('Terjadi kesalahan jaringan.');
            }
        });
    });
</script>
