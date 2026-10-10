<!-- ==============================================
     KEHADIRAN & ABSENSI (OWNER DASHBOARD)
=============================================== -->
<div class="app-container">
    <div class="fh-section-label" style="margin-top:0;">📋 Ringkasan Kehadiran Karyawan</div>
    <div class="fh-card" style="margin-bottom:1rem; background:rgba(30, 58, 138, 0.03); border:1px solid rgba(30, 58, 138, 0.1);">
        <div class="fh-card-body" style="padding:15px;">
            <form method="GET" action="" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                <input type="hidden" name="perusahaan_id" value="<?= htmlspecialchars($this->input->get('perusahaan_id')) ?>">
                <label style="font-weight:600; color:#1e3a8a; font-size:0.9rem;">Bulan:</label>
                <input type="month" name="bulan" value="<?= $bulan_ini ?>" class="form-control" style="flex:1; border-radius:8px; border:1px solid #cbd5e1; padding:8px;" onchange="this.form.submit()">
            </form>
        </div>
    </div>

    <!-- Statistik Global -->
    <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:10px; margin-bottom:1.5rem;">
        <div class="fh-card" style="margin-bottom:0; text-align:center; padding:15px;">
            <div style="font-size:1.5rem; font-weight:bold; color:#10b981;"><?= $stats['hadir'] ?></div>
            <div style="font-size:0.8rem; color:#64748b;">Total Hadir</div>
        </div>
        <div class="fh-card" style="margin-bottom:0; text-align:center; padding:15px;">
            <div style="font-size:1.5rem; font-weight:bold; color:#f59e0b;"><?= $stats['telat'] ?></div>
            <div style="font-size:0.8rem; color:#64748b;">Total Telat</div>
        </div>
        <div class="fh-card" style="margin-bottom:0; text-align:center; padding:15px;">
            <div style="font-size:1.5rem; font-weight:bold; color:#ef4444;"><?= $stats['alpha'] ?></div>
            <div style="font-size:0.8rem; color:#64748b;">Total Alpha</div>
        </div>
        <div class="fh-card" style="margin-bottom:0; text-align:center; padding:15px;">
            <div style="font-size:1.5rem; font-weight:bold; color:#3b82f6;"><?= $stats['izin'] + $stats['sakit'] + $stats['cuti'] ?></div>
            <div style="font-size:0.8rem; color:#64748b;">Izin/Sakit/Cuti</div>
        </div>
    </div>

    <div class="fh-section-label">Daftar Karyawan</div>
    <?php if(empty($rekap_all)): ?>
        <div style="text-align:center; padding:2rem; color:#94a3b8; font-style:italic;">
            Belum ada data absensi untuk periode ini.
        </div>
    <?php else: ?>
        <div class="fh-cards-area">
            <?php foreach($rekap_all as $r): ?>
                <?php 
                    $total_days = $r->total_hadir + $r->total_alpha + $r->total_izin + $r->total_sakit + $r->total_cuti;
                    $persentase = ($total_days > 0) ? round(($r->total_hadir / $total_days) * 100) : 0;
                ?>
                <div class="fh-card" style="display:block; padding:15px;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
                        <div>
                            <div style="font-weight:600; color:#1e293b;"><?= htmlspecialchars($r->nama_lengkap) ?></div>
                            <div style="font-size:0.8rem; color:#64748b;"><?= htmlspecialchars($r->jabatan ?? '-') ?> • <?= htmlspecialchars($r->divisi ?? '-') ?></div>
                        </div>
                        <div style="text-align:right;">
                            <span class="fh-badge" style="background:<?= $persentase >= 90 ? '#dcfce7' : ($persentase >= 75 ? '#fef3c7' : '#fee2e2') ?>; color:<?= $persentase >= 90 ? '#166534' : ($persentase >= 75 ? '#92400e' : '#991b1b') ?>;">
                                <?= $persentase ?>%
                            </span>
                        </div>
                    </div>
                    
                    <div style="display:flex; justify-content:space-between; font-size:0.8rem; padding-top:10px; border-top:1px dashed #e2e8f0;">
                        <div style="text-align:center;">
                            <div style="color:#10b981; font-weight:bold;"><?= $r->total_hadir ?></div>
                            <div style="color:#64748b; font-size:0.7rem;">Hadir</div>
                        </div>
                        <div style="text-align:center;">
                            <div style="color:#f59e0b; font-weight:bold;"><?= $r->total_terlambat ?></div>
                            <div style="color:#64748b; font-size:0.7rem;">Telat</div>
                        </div>
                        <div style="text-align:center;">
                            <div style="color:#3b82f6; font-weight:bold;"><?= $r->total_izin + $r->total_sakit ?></div>
                            <div style="color:#64748b; font-size:0.7rem;">Izin/SKT</div>
                        </div>
                        <div style="text-align:center;">
                            <div style="color:#ef4444; font-weight:bold;"><?= $r->total_alpha ?></div>
                            <div style="color:#64748b; font-size:0.7rem;">Alpha</div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
