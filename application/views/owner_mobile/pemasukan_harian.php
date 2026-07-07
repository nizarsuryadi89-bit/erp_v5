<div class="app-container">
    
    <div style="display: flex; justify-content: flex-end; align-items: center; margin-bottom: 1.5rem; position: relative; z-index: 10;">
        <input type="date" class="form-control form-control-sm border-0 shadow-sm" 
               style="background: white; color: var(--primary); font-weight: 700; border-radius: 8px; text-align: right; width: auto;"
               value="<?= $hariini ?>" 
               onchange="window.location.href='?perusahaan_id=<?= $perusahaan_id ?>&tanggal='+this.value">
    </div>

    <!-- Summary Total Uang Masuk -->
    <div class="summary-card main-card mb-4 text-center">
        <div class="card-label">Total Uang Masuk Hari Ini</div>
        <div class="card-value" style="font-size: 2rem; color: var(--primary);">Rp <?= number_format($stat_keuangan['total_uang_masuk'], 0, ',', '.') ?></div>
        <div class="mt-2 text-muted" style="font-size: 0.8rem;">
            Dari <?= count($pemasukan_list) ?> struk/transaksi
        </div>
    </div>

    <!-- Ringkasan Cash Flow -->
    <div class="section-title mt-4">
        <i class="fas fa-wallet text-success"></i> Ringkasan Cash Flow
    </div>
    
    <div class="list-group list-group-flush mb-4" style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
        <!-- Tunai -->
        <div class="list-group-item p-3" style="border-bottom: 1px solid var(--gray-100);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="font-weight: 700; font-size: 0.9rem; color: var(--success);"><i class="fas fa-money-bill-wave"></i> Penerimaan Tunai</div>
                <div style="font-weight: 800; text-align: right;">Rp <?= number_format($summary['cash']['total'], 0, ',', '.') ?></div>
            </div>
        </div>
        
        <!-- Transfer Bank -->
        <div class="list-group-item p-3" style="border-bottom: 1px solid var(--gray-100);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <div style="font-weight: 700; font-size: 0.9rem;"><i class="fas fa-university text-primary"></i> Transfer Bank</div>
                <div style="font-weight: 800; text-align: right;">Rp <?= number_format($summary['transfer']['total'], 0, ',', '.') ?></div>
            </div>
            <?php foreach($breakdown_transfer as $bt): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; padding-left: 1.5rem; color: var(--primary); margin-top: 4px;">
                    <div><?= $bt['nama_bank'] ?></div>
                    <div style="text-align: right;"><?= number_format($bt['total'], 0, ',', '.') ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- EDC / Debit -->
        <div class="list-group-item p-3" style="border-bottom: 1px solid var(--gray-100);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <div style="font-weight: 700; font-size: 0.9rem;"><i class="fas fa-credit-card text-info"></i> EDC / Debit Card</div>
                <div style="font-weight: 800; text-align: right;">Rp <?= number_format($summary['debit']['total'], 0, ',', '.') ?></div>
            </div>
            <?php foreach($breakdown_edc as $be): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; padding-left: 1.5rem; color: var(--info); margin-top: 4px;">
                    <div><?= $be['edc_nama'] ?></div>
                    <div style="text-align: right;"><?= number_format($be['total'], 0, ',', '.') ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- E-Wallet -->
        <div class="list-group-item p-3">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <div style="font-weight: 700; font-size: 0.9rem;"><i class="fas fa-mobile-alt text-warning"></i> E-Wallet / QRIS</div>
                <div style="font-weight: 800; text-align: right;">Rp <?= number_format($summary['ewallet']['total'], 0, ',', '.') ?></div>
            </div>
            <?php foreach($breakdown_ewallet as $bw): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; padding-left: 1.5rem; color: var(--warning); margin-top: 4px;">
                    <div><?= $bw['ewallet_nama'] ?></div>
                    <div style="text-align: right;"><?= number_format($bw['total'], 0, ',', '.') ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Ringkasan Omset -->
    <div class="section-title mt-4">
        <i class="fas fa-chart-line text-info"></i> Sumber Omset (Sales)
    </div>
    <div class="list-group list-group-flush mb-4" style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
        <div class="list-group-item p-3" style="border-bottom: 1px solid var(--gray-100);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="font-weight: 600; font-size: 0.85rem;"><i class="fas fa-print"></i> Omset SPK Produksi</div>
                <div style="font-weight: 800; text-align: right;">Rp <?= number_format($stat_keuangan['omset_penjualan'] - $stat_keuangan['omset_manual'], 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="list-group-item p-3">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="font-weight: 600; font-size: 0.85rem;"><i class="fas fa-cash-register"></i> Omset POS Manual</div>
                <div style="font-weight: 800; text-align: right;">Rp <?= number_format($stat_keuangan['omset_manual'], 0, ',', '.') ?></div>
            </div>
        </div>
    </div>

    <!-- Pengeluaran -->
    <div class="section-title mt-4">
        <i class="fas fa-money-bill-wave text-danger"></i> Pengeluaran Operasional
    </div>
    
    <div class="list-group list-group-flush mb-4" style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
        <?php if(empty($list_pengeluaran)): ?>
            <div class="list-group-item p-3 text-center text-muted" style="font-size: 0.85rem;">
                Tidak ada pengeluaran hari ini.
            </div>
        <?php else: ?>
            <?php foreach($list_pengeluaran as $peng): ?>
                <div class="list-group-item p-3" style="border-bottom: 1px solid var(--gray-100);">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px;">
                        <div style="font-weight: 700; font-size: 0.85rem; color: var(--dark); max-width: 65%; line-height: 1.3;">
                            <?= htmlspecialchars($peng['pengeluaran_detail']) ?>
                        </div>
                        <div style="text-align: right; font-weight: 800; color: var(--danger); max-width: 35%;">
                            Rp <?= number_format($peng['pengeluaran_jumlah'], 0, ',', '.') ?>
                        </div>
                    </div>
                    <div style="font-size: 0.75rem; color: var(--gray-500);">
                        <?= $peng['pengeluaran_penerima'] ?> 
                        (<?= $peng['metode_bayar'] == 'tunai' ? 'Tunai' : 'Transfer' ?>)
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="list-group-item p-3" style="background: var(--danger); color: white;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="font-weight: 700; font-size: 0.9rem;">Total Pengeluaran</div>
                    <div style="font-weight: 800; text-align: right;">Rp <?= number_format($stat_keuangan['total_pengeluaran'], 0, ',', '.') ?></div>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>
