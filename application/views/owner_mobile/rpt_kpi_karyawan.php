<!-- ==============================================
     KINERJA & KPI KARYAWAN (OWNER DASHBOARD)
=============================================== -->
<div class="app-container">
    <div class="fh-section-label" style="margin-top:0;">🌟 Leaderboard Kedisiplinan Karyawan</div>
    <div class="fh-card" style="margin-bottom:1.5rem; background:rgba(30, 58, 138, 0.03); border:1px solid rgba(30, 58, 138, 0.1);">
        <div class="fh-card-body" style="padding:15px;">
            <form method="GET" action="" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                <input type="hidden" name="perusahaan_id" value="<?= htmlspecialchars($this->input->get('perusahaan_id')) ?>">
                <label style="font-weight:600; color:#1e3a8a; font-size:0.9rem;">Bulan:</label>
                <input type="month" name="bulan" value="<?= $bulan_ini ?>" class="form-control" style="flex:1; border-radius:8px; border:1px solid #cbd5e1; padding:8px;" onchange="this.form.submit()">
            </form>
        </div>
    </div>

    <?php if(empty($kpi_karyawan)): ?>
        <div style="text-align:center; padding:2rem; color:#94a3b8; font-style:italic;">
            Belum ada data evaluasi kinerja untuk periode ini.
        </div>
    <?php else: ?>
        <!-- Top 3 Performers highlight -->
        <?php $top_performers = array_slice($kpi_karyawan, 0, 3); ?>
        <?php if(count($top_performers) > 0): ?>
        <div style="display:flex; justify-content:center; gap:10px; margin-bottom:2rem; align-items:flex-end;">
            <!-- Rank 2 -->
            <?php if(isset($top_performers[1])): ?>
            <div style="flex:1; text-align:center; background:#f8fafc; border-radius:12px; padding:15px 10px; box-shadow:0 2px 4px rgba(0,0,0,0.05); border-bottom:4px solid #94a3b8;">
                <div style="font-size:1.2rem; margin-bottom:5px;">🥈</div>
                <div style="font-weight:bold; font-size:0.8rem; color:#1e293b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?= htmlspecialchars($top_performers[1]->nama_lengkap) ?>"><?= strtok(htmlspecialchars($top_performers[1]->nama_lengkap), " ") ?></div>
                <div style="font-size:0.9rem; font-weight:800; color:#1e3a8a; margin-top:5px;"><?= $top_performers[1]->score ?></div>
            </div>
            <?php endif; ?>
            
            <!-- Rank 1 -->
            <?php if(isset($top_performers[0])): ?>
            <div style="flex:1.2; text-align:center; background:#fffbf1; border-radius:12px; padding:20px 10px; box-shadow:0 4px 6px rgba(0,0,0,0.1); border-bottom:4px solid #f59e0b; position:relative; z-index:1;">
                <div style="position:absolute; top:-15px; left:50%; transform:translateX(-50%); font-size:1.5rem;">👑</div>
                <div style="font-size:1.5rem; margin-bottom:5px;">🥇</div>
                <div style="font-weight:bold; font-size:0.9rem; color:#1e293b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?= htmlspecialchars($top_performers[0]->nama_lengkap) ?>"><?= strtok(htmlspecialchars($top_performers[0]->nama_lengkap), " ") ?></div>
                <div style="font-size:1.1rem; font-weight:900; color:#b45309; margin-top:5px;"><?= $top_performers[0]->score ?></div>
            </div>
            <?php endif; ?>

            <!-- Rank 3 -->
            <?php if(isset($top_performers[2])): ?>
            <div style="flex:1; text-align:center; background:#fff7ed; border-radius:12px; padding:15px 10px; box-shadow:0 2px 4px rgba(0,0,0,0.05); border-bottom:4px solid #d97706;">
                <div style="font-size:1.2rem; margin-bottom:5px;">🥉</div>
                <div style="font-weight:bold; font-size:0.8rem; color:#1e293b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?= htmlspecialchars($top_performers[2]->nama_lengkap) ?>"><?= strtok(htmlspecialchars($top_performers[2]->nama_lengkap), " ") ?></div>
                <div style="font-size:0.9rem; font-weight:800; color:#1e3a8a; margin-top:5px;"><?= $top_performers[2]->score ?></div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="fh-section-label">Daftar Peringkat Lengkap</div>
        <div class="fh-cards-area">
            <?php foreach($kpi_karyawan as $index => $k): ?>
                <?php 
                    $pred_color = '#ef4444'; $pred_bg = '#fee2e2'; // Cukup (default)
                    if ($k->predicate == 'ISTIMEWA') { $pred_color = '#10b981'; $pred_bg = '#dcfce7'; }
                    else if ($k->predicate == 'BAIK') { $pred_color = '#3b82f6'; $pred_bg = '#dbeafe'; }
                ?>
                <div class="fh-card" style="display:flex; align-items:center; padding:15px; gap:15px;">
                    <div style="font-size:1.2rem; font-weight:bold; color:#94a3b8; width:25px; text-align:center;">
                        #<?= $index + 1 ?>
                    </div>
                    
                    <div style="flex:1;">
                        <div style="font-weight:600; color:#1e293b;"><?= htmlspecialchars($k->nama_lengkap) ?></div>
                        <div style="font-size:0.8rem; color:#64748b;"><?= htmlspecialchars($k->jabatan ?? '-') ?></div>
                        <div style="font-size:0.75rem; margin-top:4px;">
                            <span style="color:#ef4444; margin-right:8px;"><i class="fas fa-times-circle"></i> <?= $k->total_alpha ?> Alpha</span>
                            <span style="color:#f59e0b;"><i class="fas fa-clock"></i> <?= $k->total_terlambat ?> Telat</span>
                        </div>
                    </div>
                    
                    <div style="text-align:right;">
                        <div style="font-size:1.2rem; font-weight:bold; color:#1e3a8a;"><?= $k->score ?></div>
                        <div style="font-size:0.65rem; font-weight:600; padding:2px 6px; border-radius:4px; background:<?= $pred_bg ?>; color:<?= $pred_color ?>; display:inline-block; margin-top:3px;">
                            <?= $k->predicate ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
