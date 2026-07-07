<!-- ==============================================
     STOK KRITIS (OWNER DASHBOARD)
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
    <div class="fh-section-label" style="margin-top:0;">⚠️ Ringkasan Stok Kritis</div>

    <!-- Statistik Ringkas -->
    <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:10px; margin-bottom:1.5rem;">
        <div class="fh-card" style="margin-bottom:0; text-align:center; padding:15px; border-left:4px solid #ef4444;">
            <div style="font-size:1.6rem; font-weight:800; color:#ef4444;"><?= count($kritis_list) ?></div>
            <div style="font-size:0.75rem; color:#64748b; font-weight:500; margin-top:2px;">Item Kritis</div>
        </div>
        <div class="fh-card" style="margin-bottom:0; text-align:center; padding:15px; border-left:4px solid #f59e0b;">
            <?php 
                $total_shortage = 0;
                foreach($kritis_list as $k) {
                    $total_shortage += (float)$k->kekurangan;
                }
            ?>
            <div style="font-size:1.6rem; font-weight:800; color:#f59e0b;"><?= number_format($total_shortage, 1, ',', '.') ?></div>
            <div style="font-size:0.75rem; color:#64748b; font-weight:500; margin-top:2px;">Total Kurang (Base)</div>
        </div>
    </div>

    <!-- Alert / Banner -->
    <div class="alert alert-danger" style="border-radius:15px; border:none; background:rgba(239, 68, 68, 0.08); color:#b91c1c; font-size:0.8rem; line-height:1.4; display:flex; gap:10px; align-items:flex-start; padding:12px 15px; margin-bottom:1.5rem;">
        <i class="fas fa-exclamation-triangle" style="font-size:1rem; margin-top:2px;"></i>
        <div>
            <strong>Pemberitahuan Reorder:</strong> Daftar di bawah menampilkan bahan baku dengan level stok saat ini kurang dari atau sama dengan batas minimum aman yang telah ditentukan. Segera jadwalkan pembelian ulang.
        </div>
    </div>

    <div class="fh-section-label">Daftar Bahan Baku di Bawah Batas Minim</div>
    
    <?php if(empty($kritis_list)): ?>
        <div class="fh-card" style="text-align:center; padding:2rem; color:#10b981; font-style:italic;">
            <i class="fas fa-check-circle" style="font-size:2rem; margin-bottom:10px; display:block;"></i>
            Semua bahan baku dalam kondisi aman (di atas batas minimum).
        </div>
    <?php else: ?>
        <div class="fh-cards-area">
            <?php foreach($kritis_list as $row): ?>
                <?php 
                    $satuan_prod = $row->satuan_produksi ?: 'Pcs';
                ?>
                <div class="fh-card" style="display:block; padding:15px; margin-bottom:0.75rem; border-left:4px solid #ef4444;">
                    <!-- Card Header -->
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
                        <div>
                            <div style="font-weight:700; color:#1e293b; font-size:0.9rem; line-height:1.3;">
                                <?= htmlspecialchars($row->bahan_nama) ?> - <?= htmlspecialchars($row->varian) ?>
                            </div>
                            <div style="font-size:0.75rem; color:#64748b; margin-top:4px; display:flex; align-items:center; gap:5px;">
                                <i class="fas fa-warehouse" style="color:#94a3b8; font-size:0.7rem;"></i> 
                                <?= htmlspecialchars($row->gudang_nama) ?>
                            </div>
                        </div>
                        <div style="text-align:right;">
                            <span class="fh-badge" style="background:#fee2e2; color:#b91c1c; font-weight:800; font-size:0.75rem; padding:4px 8px; border-radius:6px;">
                                Kurang <?= format_stok_gudang($row->kekurangan, $row->konversi, $row->unit, $satuan_prod) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Metrics Breakdown -->
                    <div style="display:flex; justify-content:space-between; font-size:0.8rem; padding-top:10px; border-top:1px dashed #e2e8f0; margin-top:10px;">
                        <div style="text-align:center; flex:1;">
                            <div style="color:#ef4444; font-weight:700; font-size:0.85rem;"><?= format_stok_gudang($row->stok_aktif, $row->konversi, $row->unit, $satuan_prod) ?></div>
                            <div style="color:#64748b; font-size:0.7rem; margin-top:2px;">Stok Aktif</div>
                        </div>
                        <div style="width:1px; background:#e2e8f0; margin:0 10px;"></div>
                        <div style="text-align:center; flex:1;">
                            <div style="color:#475569; font-weight:700; font-size:0.85rem;"><?= format_stok_gudang($row->stok_min, $row->konversi, $row->unit, $satuan_prod) ?></div>
                            <div style="color:#64748b; font-size:0.7rem; margin-top:2px;">Stok Minimal</div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
