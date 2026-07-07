<?php if (!function_exists('rp_pg')) {
    function rp_pg($n) {
        return 'Rp ' . number_format((float)$n, 0, ',', '.');
    }
} ?>
<!-- ==============================================
     PENGGAJIAN & PAYROLL (OWNER DASHBOARD)
=============================================== -->
<div class="app-container">
    <div class="fh-section-label" style="margin-top:0;">💰 Estimasi Beban Gaji (Payroll)</div>
    <div class="fh-card" style="margin-bottom:1.5rem; background:rgba(30, 58, 138, 0.03); border:1px solid rgba(30, 58, 138, 0.1);">
        <div class="fh-card-body" style="padding:15px;">
            <form method="GET" action="" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                <input type="hidden" name="perusahaan_id" value="<?= htmlspecialchars($this->input->get('perusahaan_id')) ?>">
                <label style="font-weight:600; color:#1e3a8a; font-size:0.9rem;">Bulan:</label>
                <input type="month" name="bulan" value="<?= $bulan_ini ?>" class="form-control" style="flex:1; border-radius:8px; border:1px solid #cbd5e1; padding:8px;" onchange="this.form.submit()">
            </form>
        </div>
    </div>

    <!-- Total Beban Payroll -->
    <div class="fh-card" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); color:white; margin-bottom:1.5rem; text-align:center; padding:20px;">
        <div style="font-size:0.85rem; opacity:0.9; margin-bottom:5px;">Total Estimasi Take Home Pay (THP)</div>
        <div style="font-size:2rem; font-weight:bold; letter-spacing:1px;"><?= rp_pg($total_thp) ?></div>
        
        <div style="display:flex; justify-content:space-between; margin-top:20px; font-size:0.8rem; border-top:1px solid rgba(255,255,255,0.2); padding-top:15px;">
            <div style="text-align:left;">
                <div style="opacity:0.8;">Gaji Pokok & Tunjangan</div>
                <div style="font-weight:bold;"><?= rp_pg($total_gaji_pokok) ?></div>
            </div>
            <div style="text-align:right;">
                <div style="opacity:0.8;">Total Lembur</div>
                <div style="font-weight:bold;"><?= rp_pg($total_lembur) ?></div>
            </div>
        </div>
        <?php if($total_kasbon > 0): ?>
        <div style="margin-top:10px; font-size:0.8rem; text-align:center; color:#fca5a5;">
            *Termasuk Pemotongan Kasbon: <?= rp_pg($total_kasbon) ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="fh-section-label">Rincian per Karyawan</div>
    <?php if(empty($salary_data)): ?>
        <div style="text-align:center; padding:2rem; color:#94a3b8; font-style:italic;">
            Belum ada data penggajian untuk periode ini.
        </div>
    <?php else: ?>
        <div class="fh-cards-area">
            <?php foreach($salary_data as $sd): ?>
                <?php 
                    // Handle objects vs arrays correctly
                    $nama = is_array($sd) ? $sd['nama_lengkap'] : $sd->nama_lengkap;
                    $jabatan = is_array($sd) ? ($sd['nama_jabatan'] ?? '-') : ($sd->nama_jabatan ?? '-');
                    $gaji = is_array($sd) ? $sd['gaji_pokok'] : $sd->gaji_pokok;
                    $lembur = is_array($sd) ? $sd['uang_lembur'] : $sd->uang_lembur;
                    $kasbon = is_array($sd) ? $sd['kasbon'] : $sd->kasbon;
                    $thp = is_array($sd) ? $sd['total_gaji'] : $sd->total_gaji;
                ?>
                <div class="fh-card" style="display:block; padding:15px;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
                        <div>
                            <div style="font-weight:600; color:#1e293b;"><?= htmlspecialchars($nama) ?></div>
                            <div style="font-size:0.8rem; color:#64748b;"><?= htmlspecialchars($jabatan) ?></div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:0.7rem; color:#64748b; margin-bottom:2px;">Take Home Pay</div>
                            <div style="font-weight:bold; color:#10b981; font-size:1.1rem;"><?= rp_pg($thp) ?></div>
                        </div>
                    </div>
                    
                    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; font-size:0.75rem; padding-top:12px; border-top:1px dashed #e2e8f0;">
                        <div>
                            <div style="color:#64748b;">Gaji Pokok</div>
                            <div style="font-weight:600; color:#1e293b;"><?= rp_pg($gaji) ?></div>
                        </div>
                        <div>
                            <div style="color:#64748b;">Lembur</div>
                            <div style="font-weight:600; color:#3b82f6;"><?= rp_pg($lembur) ?></div>
                        </div>
                        <div>
                            <div style="color:#64748b;">Kasbon</div>
                            <div style="font-weight:600; color:#ef4444;"><?= rp_pg($kasbon) ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
