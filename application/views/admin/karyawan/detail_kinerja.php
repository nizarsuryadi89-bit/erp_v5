<style>
    /* Detail Performance Styles */
    .detail-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 2.5rem 0;
        margin-bottom: 2rem;
        border-radius: 0;
        box-shadow: 0 8px 30px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
    }
    
    .detail-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        pointer-events: none;
    }
    
    .employee-info {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 0;
        padding: 1.5rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        position: relative;
        z-index: 2;
    }
    
    .employee-avatar {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 1rem;
    }
    
    .employee-details h3 {
        margin: 0 0 0.5rem 0;
        font-weight: 700;
    }
    
    .employee-details p {
        margin: 0.25rem 0;
        opacity: 0.9;
    }
    
    .performance-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .summary-card {
        background: white;
        border-radius: 0;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .summary-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #2c3e50, #34495e);
    }
    
    .summary-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .summary-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }
    
    .summary-icon {
        width: 50px;
        height: 50px;
        border-radius: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }
    
    .summary-icon.activities { background: linear-gradient(135deg, #27ae60, #2ecc71); }
    .summary-icon.score { background: linear-gradient(135deg, #e67e22, #f39c12); }
    .summary-icon.days { background: linear-gradient(135deg, #3498db, #2980b9); }
    .summary-icon.average { background: linear-gradient(135deg, #9b59b6, #8e44ad); }
    
    .summary-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
        line-height: 1;
    }
    
    .summary-label {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }
    
    .chart-section {
        background: white;
        border-radius: 0;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        margin-bottom: 2rem;
    }
    
    .chart-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .chart-header h5 {
        margin: 0;
        color: #2c3e50;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .activity-chart {
        height: 300px;
        background: #f8f9fa;
        border-radius: 0;
        display: flex;
        align-items: end;
        justify-content: space-around;
        padding: 1rem;
        gap: 2px;
    }
    
    .chart-bar {
        background: linear-gradient(135deg, #2c3e50, #34495e);
        border-radius: 0;
        min-width: 20px;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .chart-bar:hover {
        transform: scaleY(1.1);
        background: linear-gradient(135deg, #27ae60, #2ecc71);
    }
    
    .chart-label {
        position: absolute;
        bottom: -25px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.7rem;
        color: #6c757d;
        font-weight: 600;
        white-space: nowrap;
    }
    
    .activity-breakdown {
        background: white;
        border-radius: 0;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        margin-bottom: 2rem;
    }
    
    .breakdown-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 0;
        border-bottom: 1px solid #e9ecef;
    }
    
    .breakdown-item:last-child {
        border-bottom: none;
    }
    
    .breakdown-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .breakdown-icon {
        width: 40px;
        height: 40px;
        border-radius: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: white;
    }
    
    .breakdown-icon.input { background: linear-gradient(135deg, #27ae60, #2ecc71); }
    .breakdown-icon.edit { background: linear-gradient(135deg, #e67e22, #f39c12); }
    .breakdown-icon.delete { background: linear-gradient(135deg, #e74c3c, #c0392b); }
    .breakdown-icon.print { background: linear-gradient(135deg, #3498db, #2980b9); }
    .breakdown-icon.other { background: linear-gradient(135deg, #95a5a6, #7f8c8d); }
    
    .breakdown-details h6 {
        margin: 0;
        color: #2c3e50;
        font-weight: 600;
    }
    
    .breakdown-details p {
        margin: 0;
        color: #6c757d;
        font-size: 0.8rem;
    }
    
    .breakdown-stats {
        text-align: right;
    }
    
    .breakdown-count {
        font-size: 1.2rem;
        font-weight: 700;
        color: #2c3e50;
    }
    
    .breakdown-score {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .back-button {
        background: linear-gradient(135deg, #2c3e50, #34495e);
        color: white;
        border: none;
        border-radius: 0;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .back-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(44, 62, 80, 0.4);
        color: white;
        text-decoration: none;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    
    @media (max-width: 767.98px) {
        .detail-header {
            padding: 2rem 0;
        }
        
        .employee-info {
            text-align: center;
        }
        
        .employee-avatar {
            margin: 0 auto 1rem auto;
        }
        
        .performance-summary {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .summary-value {
            font-size: 2rem;
        }
        
        .activity-chart {
            height: 200px;
        }
        
        .chart-bar {
            min-width: 15px;
        }
        
        .chart-label {
            font-size: 0.6rem;
        }
    }
    
    @media (max-width: 575.98px) {
        .performance-summary {
            grid-template-columns: 1fr;
        }
        
        .activity-chart {
            height: 150px;
        }
        
        .breakdown-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .breakdown-stats {
            text-align: left;
        }
    }
</style>

<div class="app-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="detail-header">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="employee-info">
                        <div class="employee-avatar">
                            <i class="fa fa-user"></i>
                        </div>
                        <div class="employee-details">
                            <h3><?= $karyawan->nama_lengkap ?></h3>
                            <p><strong>NIK:</strong> <?= $karyawan->nik ?></p>
                            <p><strong>Jabatan:</strong> <?= $karyawan->nama_jabatan ?></p>
                            <p><strong>Divisi:</strong> <?= $karyawan->divisi_nama ?></p>
                            <p><strong>Perusahaan:</strong> <?= $karyawan->nama_perusahaan ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="<?= base_url('karyawan/penilaian_kinerja?bulan=' . $bulan) ?>" class="back-button">
                        <i class="fa fa-arrow-left"></i>
                        Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>

        <!-- Performance Summary -->
        <div class="performance-summary">
            <?php 
            $total_activities = 0;
            $total_score = 0;
            $total_days = 0;
            $avg_score = 0;
            
            if (!empty($daily_activity)) {
                foreach ($daily_activity as $day) {
                    $total_activities += $day->total_aktivitas;
                    $total_score += $day->skor_harian;
                    $total_days++;
                }
                $avg_score = $total_days > 0 ? $total_score / $total_days : 0;
            }
            ?>
            
            <div class="summary-card">
                <div class="summary-header">
                    <div class="summary-icon activities">
                        <i class="fa fa-tasks"></i>
                    </div>
                </div>
                <div class="summary-value"><?= number_format($total_activities) ?></div>
                <div class="summary-label">Total Aktivitas</div>
            </div>
            
            <div class="summary-card">
                <div class="summary-header">
                    <div class="summary-icon score">
                        <i class="fa fa-star"></i>
                    </div>
                </div>
                <div class="summary-value"><?= number_format($total_score) ?></div>
                <div class="summary-label">Total Skor</div>
            </div>
            
            <div class="summary-card">
                <div class="summary-header">
                    <div class="summary-icon days">
                        <i class="fa fa-calendar"></i>
                    </div>
                </div>
                <div class="summary-value"><?= $total_days ?></div>
                <div class="summary-label">Hari Aktif</div>
            </div>
            
            <div class="summary-card">
                <div class="summary-header">
                    <div class="summary-icon average">
                        <i class="fa fa-chart-line"></i>
                    </div>
                </div>
                <div class="summary-value"><?= number_format($avg_score, 1) ?></div>
                <div class="summary-label">Rata-rata Skor/Hari</div>
            </div>
        </div>

        <!-- Activity Chart -->
        <div class="chart-section">
            <div class="chart-header">
                <h5><i class="fa fa-chart-bar text-primary"></i> Aktivitas Harian - <?= date('F Y', strtotime($bulan . '-01')) ?></h5>
            </div>
            
            <?php if (!empty($daily_activity)): ?>
                <div class="activity-chart">
                    <?php 
                    $max_score = 0;
                    foreach ($daily_activity as $day) {
                        if ($day->skor_harian > $max_score) {
                            $max_score = $day->skor_harian;
                        }
                    }
                    
                    foreach ($daily_activity as $day): 
                        $height_percentage = $max_score > 0 ? ($day->skor_harian / $max_score) * 100 : 0;
                    ?>
                        <div class="chart-bar" style="height: <?= $height_percentage ?>%;" title="Tanggal: <?= date('d/m/Y', strtotime($day->tanggal)) ?>, Skor: <?= $day->skor_harian ?>">
                            <div class="chart-label"><?= date('d', strtotime($day->tanggal)) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fa fa-chart-bar"></i>
                    <h5>Tidak ada data aktivitas</h5>
                    <p>Belum ada data aktivitas untuk periode ini.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Activity Breakdown -->
        <div class="activity-breakdown">
            <div class="chart-header">
                <h5><i class="fa fa-list text-success"></i> Rincian Aktivitas</h5>
            </div>
            
            <?php if (!empty($activity_breakdown)): ?>
                <?php foreach ($activity_breakdown as $activity): ?>
                    <?php 
                    $icon_class = 'other';
                    $icon_name = 'fa-cog';
                    
                    if (strpos($activity->action, 'input') !== false) {
                        $icon_class = 'input';
                        $icon_name = 'fa-plus';
                    } elseif (strpos($activity->action, 'edit') !== false) {
                        $icon_class = 'edit';
                        $icon_name = 'fa-edit';
                    } elseif (strpos($activity->action, 'delete') !== false) {
                        $icon_class = 'delete';
                        $icon_name = 'fa-trash';
                    } elseif (strpos($activity->action, 'cetak') !== false || strpos($activity->action, 'print') !== false) {
                        $icon_class = 'print';
                        $icon_name = 'fa-print';
                    }
                    ?>
                    <div class="breakdown-item">
                        <div class="breakdown-info">
                            <div class="breakdown-icon <?= $icon_class ?>">
                                <i class="fa <?= $icon_name ?>"></i>
                            </div>
                            <div class="breakdown-details">
                                <h6><?= ucwords(str_replace('_', ' ', $activity->action)) ?></h6>
                                <p>Jenis aktivitas sistem</p>
                            </div>
                        </div>
                        <div class="breakdown-stats">
                            <div class="breakdown-count"><?= number_format($activity->jumlah) ?></div>
                            <div class="breakdown-score">Skor: <?= number_format($activity->total_skor) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fa fa-list"></i>
                    <h5>Tidak ada rincian aktivitas</h5>
                    <p>Belum ada data rincian aktivitas untuk periode ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate summary values
    const summaryValues = document.querySelectorAll('.summary-value');
    summaryValues.forEach(summary => {
        const finalValue = parseFloat(summary.textContent.replace(/,/g, ''));
        
        if (!isNaN(finalValue)) {
            let currentValue = 0;
            const increment = finalValue / 50;
            const isDecimal = summary.textContent.includes('.');
            
            const timer = setInterval(() => {
                currentValue += increment;
                if (currentValue >= finalValue) {
                    currentValue = finalValue;
                    clearInterval(timer);
                }
                
                if (isDecimal) {
                    summary.textContent = currentValue.toFixed(1);
                } else {
                    summary.textContent = Math.floor(currentValue).toLocaleString();
                }
            }, 30);
        }
    });
    
    // Animate chart bars
    const chartBars = document.querySelectorAll('.chart-bar');
    chartBars.forEach(bar => {
        const finalHeight = bar.style.height;
        bar.style.height = '0%';
        
        setTimeout(() => {
            bar.style.transition = 'height 1s ease';
            bar.style.height = finalHeight;
        }, 500);
    });
    
    // Add tooltips for chart bars
    chartBars.forEach(bar => {
        const title = bar.getAttribute('title');
        if (title) {
            bar.addEventListener('mouseenter', function() {
                const tooltip = document.createElement('div');
                tooltip.className = 'tooltip';
                tooltip.textContent = title;
                tooltip.style.cssText = `
                    position: absolute;
                    background: #333;
                    color: white;
                    padding: 0.5rem;
                    border-radius: 4px;
                    font-size: 0.8rem;
                    z-index: 1000;
                    pointer-events: none;
                    white-space: nowrap;
                `;
                document.body.appendChild(tooltip);
                
                const rect = this.getBoundingClientRect();
                tooltip.style.left = rect.left + 'px';
                tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';
                
                this.addEventListener('mouseleave', function() {
                    tooltip.remove();
                });
            });
        }
    });
    
    // Add hover effects for breakdown items
    const breakdownItems = document.querySelectorAll('.breakdown-item');
    breakdownItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(5px)';
            this.style.transition = 'transform 0.2s ease';
            this.style.backgroundColor = '#f8f9fa';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
            this.style.backgroundColor = 'transparent';
        });
    });
});
</script>
