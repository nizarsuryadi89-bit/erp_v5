<style>
.filter-bar {
    position: relative;
    z-index: 20;
    margin: 0 0 1.25rem;
    background: white;
    border-radius: 18px;
    box-shadow: 0 6px 24px rgba(0,0,0,.08);
    padding: 1rem;
}
.filter-bar label {
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
    display: block;
    margin-bottom: 4px;
}
.filter-bar input {
    width: 100%;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: .45rem .75rem;
    font-size: .85rem;
    background: #f8fafc;
    color: #1e3a8a;
    font-weight: 600;
}

.ko-card {
    background: white;
    border-radius: 18px;
    box-shadow: 0 3px 14px rgba(0,0,0,.05);
    border: 1px solid #f1f5f9;
    padding: 1rem;
    margin-bottom: 1rem;
}
.ko-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: .75rem;
}
.ko-name {
    font-size: .9rem;
    font-weight: 800;
    color: #1e293b;
}
.ko-badge {
    font-size: .7rem;
    font-weight: 700;
    padding: .25rem .6rem;
    border-radius: 8px;
    text-transform: uppercase;
}
.ko-badge.success { background: #dcfce7; color: #15803d; }
.ko-badge.warning { background: #fef3c7; color: #b45309; }
.ko-badge.danger { background: #fee2e2; color: #b91c1c; }
.ko-badge.primary { background: #dbeafe; color: #1d4ed8; }

.ko-progress-bg {
    background: #e2e8f0;
    border-radius: 6px;
    height: 8px;
    overflow: hidden;
    margin-bottom: 1rem;
}
.ko-progress-fill {
    height: 100%;
    border-radius: 6px;
}
.ko-progress-fill.success { background: linear-gradient(90deg, #10b981, #059669); }
.ko-progress-fill.warning { background: linear-gradient(90deg, #f59e0b, #d97706); }
.ko-progress-fill.danger { background: linear-gradient(90deg, #ef4444, #dc2626); }
.ko-progress-fill.primary { background: linear-gradient(90deg, #3b82f6, #2563eb); }

.ko-stats {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8fafc;
    padding: .75rem;
    border-radius: 12px;
    border: 1px solid #f1f5f9;
}
.ko-stat-item {
    flex: 1;
    text-align: center;
}
.ko-stat-item:not(:last-child) {
    border-right: 1px solid #e2e8f0;
}
.ko-stat-label {
    font-size: .65rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: 2px;
}
.ko-stat-val {
    font-size: .85rem;
    font-weight: 900;
}
.ko-stat-val.total { color: #475569; }
.ko-stat-val.success { color: #10b981; }
.ko-stat-val.warning { color: #f59e0b; }
.ko-stat-val.danger { color: #ef4444; }
</style>

<div class="app-container">
    <!-- Filter Bulan -->
    <div class="filter-bar">
        <form method="GET" action="">
            <input type="hidden" name="perusahaan_id" value="<?= $perusahaan_id ?>">
            <div>
                <label>Filter Periode Bulan</label>
                <input type="month" name="bulan" value="<?= $bulan_ini ?>" onchange="this.form.submit()">
            </div>
        </form>
    </div>

    <?php if (empty($kinerja)): ?>
        <div style="text-align: center; padding: 3rem 1.5rem; color: #94a3b8; font-style: italic;">
            <i class="fas fa-inbox fa-2x" style="margin-bottom: .75rem; opacity: 0.5;"></i>
            <div>Tidak ada data kinerja operator pada periode ini.</div>
        </div>
    <?php else: ?>
        <div class="fh-cards-area">
            <?php foreach ($kinerja as $k): 
                $total = (int)$k['total_tugas'];
                $selesai = (int)$k['selesai'];
                $proses = (int)$k['sedang_dikerjakan'];
                $belum = (int)$k['belum_dikerjakan'];
                
                $progress = $total > 0 ? round(($selesai / $total) * 100) : 0;
                $status_class = 'primary';
                if ($progress == 100) $status_class = 'success';
                elseif ($progress < 50) $status_class = 'danger';
                elseif ($progress < 80) $status_class = 'warning';
            ?>
            <div class="ko-card">
                <div class="ko-header">
                    <div class="ko-name"><?= htmlspecialchars($k['operator_nama'] ?: 'Unknown Operator') ?></div>
                    <span class="ko-badge <?= $status_class ?>"><?= $progress ?>% Selesai</span>
                </div>
                
                <div class="ko-progress-bg">
                    <div class="ko-progress-fill <?= $status_class ?>" style="width: <?= $progress ?>%"></div>
                </div>

                <div class="ko-stats">
                    <div class="ko-stat-item">
                        <div class="ko-stat-label">Total</div>
                        <div class="ko-stat-val total"><?= $total ?></div>
                    </div>
                    <div class="ko-stat-item">
                        <div class="ko-stat-label">Selesai</div>
                        <div class="ko-stat-val success"><?= $selesai ?></div>
                    </div>
                    <div class="ko-stat-item">
                        <div class="ko-stat-label">Proses</div>
                        <div class="ko-stat-val warning"><?= $proses ?></div>
                    </div>
                    <div class="ko-stat-item">
                        <div class="ko-stat-label">Belum</div>
                        <div class="ko-stat-val danger"><?= $belum ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

