<!-- Rekapitulasi Penjualan Marketplace - Premium Design -->
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #1a1c23 0%, #2c3e50 100%);
        --marketplace-gradient: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
        --glass-bg: rgba(255, 255, 255, 0.95);
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }

    .report-card {
        background: white;
        border-radius: 20px;
        border: none;
        box-shadow: var(--premium-shadow);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .report-header {
        background: var(--primary-gradient);
        padding: 1.5rem 2rem;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .market-badge {
        padding: 5px 12px;
        border-radius: 8px;
        font-weight: 800;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: 1px solid rgba(0,0,0,0.1);
    }

    /* Row Color Themes */
    .row-shopee { border-left: 6px solid #ee4d2d !important; background-color: rgba(238, 77, 45, 0.03) !important; }
    .row-tokopedia { border-left: 6px solid #42b549 !important; background-color: rgba(66, 181, 73, 0.03) !important; }
    .row-tiktok { border-left: 6px solid #000000 !important; background-color: rgba(37, 244, 238, 0.05) !important; } /* Tiktok Cyan/Black Tint */
    .row-lazada { border-left: 6px solid #8e44ad !important; background-color: rgba(142, 68, 173, 0.03) !important; }
    .row-blibli { border-left: 6px solid #0095da !important; background-color: rgba(0, 149, 218, 0.03) !important; }
    .row-bukalapak { border-left: 6px solid #e74c3c !important; background-color: rgba(231, 76, 60, 0.03) !important; }

    .badge-tokopedia { background: #42b549; color: white; }
    .badge-shopee { background: #ee4d2d; color: white; }
    .badge-tiktok { background: #000000; color: white; }
    .badge-lazada { background: #8e44ad; color: white; }
    .badge-blibli { background: #0095da; color: white; }
    .badge-bukalapak { background: #e74c3c; color: white; }
    .badge-other { background: #64748b; color: white; }

    .summary-box {
        background: white;
        padding: 1.5rem;
        border-radius: 15px;
        border: 1px solid #f1f5f9;
        transition: transform 0.3s ease;
    }
    .summary-box:hover { transform: translateY(-5px); }
    
    .spk-pill {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 800;
        background: #f8fafc;
        padding: 4px 10px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        font-size: 0.85rem;
    }

    .premium-table thead th {
        background: #f8fafc;
        color: #64748b;
        font-weight: 800;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 1rem 1.5rem;
        border: none;
    }

    .premium-table tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }

    .filter-bar {
        background: #f8fafc;
        padding: 1.5rem;
        border-radius: 15px;
        margin-bottom: 2rem;
    }
</style>

<div class="app-content">
    <?php $this->load->view('layout_admin/header_modul'); ?>

    <!-- Summary Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="summary-box shadow-sm border-start border-4 border-warning">
                <div class="text-muted small fw-bold mb-1">TOTAL OMZET MARKETPLACE</div>
                <h2 class="fw-900 text-dark">Rp <?= number_format($total_omzet_all, 0, ',', '.') ?></h2>
                <div class="small text-warning fw-bold"><i class="fas fa-chart-line me-1"></i> Periode Terpilih</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-box shadow-sm border-start border-4 border-primary">
                <div class="text-muted small fw-bold mb-1">VOLUME TRANSAKSI</div>
                <h2 class="fw-900 text-dark"><?= number_format($total_trx) ?> <small class="fs-6 text-muted">Pesanan</small></h2>
                <div class="small text-primary fw-bold"><i class="fas fa-shopping-bag me-1"></i> Berhasil Diproses</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-box shadow-sm border-start border-4 border-info">
                <div class="text-muted small fw-bold mb-1">CABANG / PERUSAHAAN</div>
                <h2 class="fw-900 text-dark"><?= $perusahaan['nama_perusahaan'] ?? 'SELURUH CABANG' ?></h2>
                <div class="small text-info fw-bold"><i class="fas fa-building me-1"></i> Filter Akses</div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar border shadow-sm">
        <form method="GET" action="<?= base_url('transaksi/rekap_transaksi_marketplace') ?>" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-800 text-muted">DARI TANGGAL</label>
                <input type="date" name="date_from" class="form-control fw-bold" value="<?= $date_from ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-800 text-muted">SAMPAI TANGGAL</label>
                <input type="date" name="date_to" class="form-control fw-bold" value="<?= $date_to ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-800 text-muted">CHANNEL MARKETPLACE</label>
                <select name="marketplace_id" class="form-select fw-bold">
                    <option value="">-- SEMUA CHANNEL --</option>
                    <option value="1" <?= $selected_market == '1' ? 'selected' : '' ?>>TOKOPEDIA</option>
                    <option value="2" <?= $selected_market == '2' ? 'selected' : '' ?>>SHOPEE</option>
                    <option value="3" <?= $selected_market == '3' ? 'selected' : '' ?>>TIKTOK SHOP</option>
                    <option value="4" <?= $selected_market == '4' ? 'selected' : '' ?>>LAZADA</option>
                    <option value="5" <?= $selected_market == '5' ? 'selected' : '' ?>>BLIBLI</option>
                    <option value="6" <?= $selected_market == '6' ? 'selected' : '' ?>>BUKALAPAK</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">
                    <i class="fas fa-search me-2"></i> FILTER DATA
                </button>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="report-card border shadow-sm">
        <div class="report-header">
            <h5 class="mb-0 fw-800"><i class="fas fa-table me-2"></i> DETAIL MANIFEST PENJUALAN</h5>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-sm btn-light fw-bold px-3">
                    <i class="fas fa-print me-1"></i> CETAK
                </button>
                <button class="btn btn-sm btn-success fw-bold px-3">
                    <i class="fas fa-file-excel me-1"></i> EXPORT
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table premium-table mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">#</th>
                        <th width="15%">NO. SPK</th>
                        <th width="15%">TANGGAL</th>
                        <th width="20%">DATA PEMBELI</th>
                        <th width="15%">CHANNEL</th>
                        <th width="15%">NO. RESI</th>
                        <th class="text-end" width="15%">TOTAL OMZET</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 opacity-20"></i>
                                <p class="fw-bold mb-0">Tidak ada data transaksi ditemukan untuk periode ini.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $n = 1; foreach ($transactions as $t): ?>
                            <?php 
                                $m_classes = [
                                    1 => 'badge-tokopedia',
                                    2 => 'badge-shopee',
                                    3 => 'badge-tiktok',
                                    4 => 'badge-lazada',
                                    5 => 'badge-blibli',
                                    6 => 'badge-bukalapak'
                                ];
                                $row_classes = [
                                    1 => 'row-tokopedia',
                                    2 => 'row-shopee',
                                    3 => 'row-tiktok',
                                    4 => 'row-lazada',
                                    5 => 'row-blibli',
                                    6 => 'row-bukalapak'
                                ];
                                $m_names = [
                                    1 => 'TOKOPEDIA',
                                    2 => 'SHOPEE',
                                    3 => 'TIKTOK',
                                    4 => 'LAZADA',
                                    5 => 'BLIBLI',
                                    6 => 'BUKALAPAK'
                                ];
                                $badge_class = $m_classes[$t->marketplace_id] ?? 'badge-other';
                                $row_class = $row_classes[$t->marketplace_id] ?? '';
                                $market_name = $m_names[$t->marketplace_id] ?? 'LAINNYA';
                            ?>
                            <tr class="<?= $row_class ?>">
                                <td class="text-center text-muted fw-bold"><?= $n++ ?></td>
                                <td><span class="spk-pill"><?= $t->nospk ?></span></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= date('d/m/Y', strtotime($t->created_at)) ?></div>
                                    <div class="small text-muted"><?= date('H:i', strtotime($t->created_at)) ?> WIB</div>
                                </td>
                                <td>
                                    <div class="fw-900 text-dark"><?= strtoupper($t->konsumen_nama) ?></div>
                                    <div class="small text-muted text-truncate" style="max-width: 200px;"><?= $t->konsumen_alamat ?></div>
                                </td>
                                <td><span class="market-badge <?= $badge_class ?>"><?= $market_name ?></span></td>
                                <td><code class="fw-bold text-primary"><?= $t->resi_marketplace ?: '-' ?></code></td>
                                <td class="text-end fw-900 text-success">Rp <?= number_format($t->total_omzet, 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($transactions)): ?>
                <tfoot class="bg-light">
                    <tr>
                        <td colspan="6" class="text-end py-3 fw-900 text-dark">TOTAL SELURUHNYA :</td>
                        <td class="text-end py-3 fw-900 text-success fs-5">Rp <?= number_format($total_omzet_all, 0, ',', '.') ?></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
