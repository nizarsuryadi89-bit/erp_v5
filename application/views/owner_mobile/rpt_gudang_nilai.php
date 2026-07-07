<!-- ==============================================
     NILAI ASET INVENTARIS (OWNER DASHBOARD)
 =============================================== -->
<?php
if (!function_exists('format_stok_gudang')) {
    function format_stok_gudang($qty_base, $konversi, $satuan_gudang, $satuan_produksi) {
        $konversi = (float)($konversi ?: 1);
        $qty_base = (float)$qty_base;
        if ($qty_base <= 0) {
            return '0 ' . $satuan_gudang;
        }
        
        $qty_stock = $qty_base / $konversi;
        $whole = floor($qty_stock);
        $small = round(($qty_stock - $whole) * $konversi, 2);
        
        // Jika tidak ada remainder, tampilkan bulat gudang
        if ($konversi <= 1 || $small <= 0) {
            return number_format($whole, 0, ',', '.') . ' ' . $satuan_gudang;
        }
        
        // Jika ada remainder, tampilkan kombinasi
        if ($whole <= 0) {
            return number_format($small, 1, ',', '.') . ' ' . $satuan_produksi;
        }
        
        return number_format($whole, 0, ',', '.') . ' ' . $satuan_gudang . ' + ' . number_format($small, 1, ',', '.') . ' ' . $satuan_produksi;
    }
}
?>
<div class="app-container">
    <div class="fh-section-label" style="margin-top:0;">🪙 Ringkasan Nilai Aset Inventaris</div>

    <!-- Valuasi Total Highlight -->
    <div class="fh-card" style="margin-bottom:1.5rem; background:linear-gradient(135deg, rgba(16, 185, 129, 0.05), rgba(5, 150, 105, 0.02)); border:1px solid rgba(16, 185, 129, 0.15); border-radius:24px; padding:20px; text-align:center;">
        <div style="font-size:0.8rem; font-weight:700; color:#059669; text-transform:uppercase; letter-spacing:1px; margin-bottom:5px;">Total Valuasi Aset Stok</div>
        <div style="font-size:1.8rem; font-weight:900; color:#047857; letter-spacing:-0.5px;">Rp <?= number_format($total_aset, 0, ',', '.') ?></div>
        <div style="font-size:0.75rem; color:#64748b; margin-top:8px; display:flex; align-items:center; justify-content:center; gap:5px;">
            <i class="fas fa-info-circle"></i> Berdasarkan harga beli terakhir material terpilih.
        </div>
    </div>

    <!-- Statistik Ringkas -->
    <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:10px; margin-bottom:1.5rem;">
        <div class="fh-card" style="margin-bottom:0; text-align:center; padding:15px; border-left:4px solid #10b981;">
            <div style="font-size:1.4rem; font-weight:800; color:#10b981;"><?= count($nilai_list) ?></div>
            <div style="font-size:0.75rem; color:#64748b; font-weight:500; margin-top:2px;">Item Bernilai</div>
        </div>
        <div class="fh-card" style="margin-bottom:0; text-align:center; padding:15px; border-left:4px solid #3b82f6;">
            <?php 
                $total_qty_active = 0;
                foreach($nilai_list as $n) {
                    $total_qty_active += (float)$n->stok_aktif;
                }
            ?>
            <div style="font-size:1.4rem; font-weight:800; color:#3b82f6;"><?= number_format($total_qty_active, 1, ',', '.') ?></div>
            <div style="font-size:0.75rem; color:#64748b; font-weight:500; margin-top:2px;">Total Qty (Base)</div>
        </div>
    </div>

    <div class="fh-section-label">Rincian Valuasi per Item</div>
    
    <?php if(empty($nilai_list)): ?>
        <div style="text-align:center; padding:2rem; color:#94a3b8; font-style:italic;">
            Belum ada material bernilai aset yang terdaftar saat ini.
        </div>
    <?php else: ?>
        <div class="fh-cards-area">
            <?php foreach($nilai_list as $row): ?>
                <?php 
    $harga_beli = (float)($row->harga_beli_terakhir ?? 0);
    $nilai_stok = (float)($row->nilai_stok ?? 0);
    $satuan_prod = $row->satuan_produksi ?: 'Pcs';
    $konversi = (float)($row->konversi ?? 1);
?>
                <div class="fh-card" style="display:block; padding:15px; margin-bottom:0.75rem;">
                    <!-- Card Header -->
                    <div style="margin-bottom:10px;">
                        <div style="font-weight:700; color:#1e293b; font-size:0.9rem; line-height:1.3;">
                            <?= htmlspecialchars($row->bahan_nama) ?> - <?= htmlspecialchars($row->varian) ?>
                        </div>
                        <div style="font-size:0.75rem; color:#64748b; margin-top:4px; display:flex; align-items:center; gap:5px;">
                            <i class="fas fa-warehouse" style="color:#94a3b8; font-size:0.7rem;"></i> 
                            <?= htmlspecialchars($row->gudang_nama) ?>
                        </div>
                    </div>

                    <!-- Valuation Detail Grid -->
                    <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:10px; background:rgba(241, 245, 249, 0.5); padding:10px; border-radius:12px; border:1px solid #f1f5f9; margin-bottom:10px;">
                        <div>
                            <div style="font-size:0.7rem; color:#64748b; text-transform:uppercase; font-weight:600;">Stok Aktif</div>
                            <div style="font-size:0.8rem; font-weight:700; color:#1e293b; margin-top:1px;">
                                <?= format_stok_gudang($row->stok_aktif, $row->konversi, $row->unit, $satuan_prod) ?>
                            </div>
                        </div>
                        <div>
                            <div style="font-size:0.7rem; color:#64748b; text-transform:uppercase; font-weight:600;">Harga Terakhir</div>
                            <div style="font-size:0.8rem; font-weight:700; color:#1e293b; margin-top:1px;">
                                <?php
                                    $harga_per_prod = $konversi > 0 ? $harga_beli / $konversi : $harga_beli;
                                ?>
                                Rp <?= number_format($harga_per_prod, 0, ',', '.') ?> / <?= htmlspecialchars($satuan_prod) ?>
                            </div>
                        </div>
                    </div>

                    <!-- Total Value Row -->
                    <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.85rem; padding-top:8px; border-top:1px dashed #e2e8f0;">
                        <span style="font-weight:600; color:#475569;">Nilai Aset:</span>
                        <span style="font-weight:800; color:#047857; font-size:0.9rem;">
                            Rp <?= number_format($nilai_stok, 0, ',', '.') ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
