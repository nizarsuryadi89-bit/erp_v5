

<style>
    /* Enhanced Unit Management Styles - Black & White Theme */
    .dashboard-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 2.5rem 0;
        margin-bottom: 2rem;
        border-radius: 0;
        box-shadow: 0 8px 30px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
    }
    
    .dashboard-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        pointer-events: none;
    }
    
    .header-content {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        position: relative;
        z-index: 2;
    }
    
    .header-icon {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .header-text {
        flex: 1;
    }
    
    .header-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin: 0 0 0.5rem 0;
        line-height: 1.2;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    
    .header-subtitle {
        font-size: 1.3rem;
        font-weight: 400;
        margin: 0 0 0.75rem 0;
        opacity: 0.9;
        font-weight: 300;
    }
    
    .header-description {
        font-size: 1rem;
        margin: 0;
        opacity: 0.8;
        line-height: 1.5;
        max-width: 500px;
    }
    
    .header-info {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        position: relative;
        z-index: 2;
    }
    
    .info-card {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 0;
        padding: 1rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 120px;
        transition: all 0.3s ease;
    }
    
    .info-card:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }
    
    .info-icon {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    
    .info-content {
        flex: 1;
    }
    
    .info-value {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 0.25rem;
    }
    
    .info-label {
        font-size: 0.8rem;
        opacity: 0.8;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: white;
        border-radius: 0;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #2c3e50, #34495e);
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }
    
    .stat-icon.primary { background: linear-gradient(135deg, #2c3e50, #34495e); }
    .stat-icon.success { background: linear-gradient(135deg, #27ae60, #2ecc71); }
    .stat-icon.warning { background: linear-gradient(135deg, #e67e22, #f39c12); }
    .stat-icon.info { background: linear-gradient(135deg, #3498db, #2980b9); }
    
    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
        line-height: 1;
    }
    
    .stat-label {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }
    
    .stat-change {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0;
        font-weight: 600;
    }
    
    .stat-change.positive {
        background: #d4edda;
        color: #155724;
    }
    
    .stat-change.negative {
        background: #f8d7da;
        color: #721c24;
    }
    
    .quick-actions {
        background: white;
        border-radius: 0;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        margin-bottom: 2rem;
    }
    
    .quick-actions h5 {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .action-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
    
    .action-btn {
        background: linear-gradient(135deg, #2c3e50, #34495e);
        color: white;
        border: none;
        border-radius: 0;
        padding: 1rem;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        text-align: center;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(44, 62, 80, 0.4);
        color: white;
        text-decoration: none;
    }
    
    .btn-view {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        border: none;
        border-radius: 0;
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.3rem;
        text-decoration: none;
    }
    
    .btn-view:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(52, 152, 219, 0.4);
        color: white;
        text-decoration: none;
    }
    
    .btn-edit {
        background: linear-gradient(135deg, #e67e22, #f39c12);
        color: white;
        border: none;
        border-radius: 0;
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    
    .btn-edit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(230, 126, 34, 0.4);
        color: white;
    }
    
    .btn-delete {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        border: none;
        border-radius: 0;
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    
    .btn-delete:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(231, 76, 60, 0.4);
        color: white;
    }
    
    .modal-content {
        border-radius: 0;
        border: 1px solid #e9ecef;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        background: #fff;
    }
    
    .modal-header {
        background: linear-gradient(135deg, #2c3e50, #34495e);
        color: white;
        border-bottom: 1px solid #e9ecef;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
    
    .modal-title {
        color: white;
        font-weight: 600;
    }
    
    .form-control, .form-select {
        border: 1px solid #e9ecef;
        border-radius: 0;
        padding: 0.75rem;
        font-size: 0.95rem;
        background: #ffffff;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #2c3e50;
        box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.25);
        background: #ffffff;
    }
    
    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }
    
    @media (max-width: 767.98px) {
        .dashboard-header {
            padding: 2rem 0;
        }
        
        .header-content {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }
        
        .header-icon {
            width: 60px;
            height: 60px;
            font-size: 2rem;
        }
        
        .header-title {
            font-size: 2rem;
        }
        
        .header-subtitle {
            font-size: 1.1rem;
        }
        
        .header-description {
            font-size: 0.9rem;
        }
        
        .header-info {
            justify-content: center;
            margin-top: 1rem;
        }
        
        .info-card {
            min-width: 100px;
            padding: 0.75rem;
        }
        
        .info-icon {
            width: 35px;
            height: 35px;
            font-size: 1rem;
        }
        
        .info-value {
            font-size: 1.2rem;
        }
        
        .info-label {
            font-size: 0.7rem;
        }
        
        .stats-overview {
            grid-template-columns: 1fr;
        }
        
        .action-buttons {
            grid-template-columns: 1fr;
        }
        
        .stat-value {
            font-size: 2rem;
        }
    }
    
    @media (max-width: 575.98px) {
        .header-info {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .info-card {
            min-width: auto;
            width: 100%;
            justify-content: center;
        }
        
        .stats-overview {
            grid-template-columns: 1fr;
        }
        
        .action-buttons {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="app-content">
    <div class="container-fluid">
        <!-- Enhanced Header -->
        <div class="dashboard-header">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-7">
                    <div class="header-content">
                        <div class="header-icon">
                            <i class="fa fa-ruler"></i>
                        </div>
                        <div class="header-text">
                            <h1 class="header-title">Unit Management</h1>
                            <h3 class="header-subtitle"><?= isset($perusahaan) ? $perusahaan : 'TGM ERP System' ?></h3>
                            <p class="header-description">Kelola semua satuan dan unit pengukuran dengan mudah dan efisien</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-5">
                    <div class="header-info">
                        <div class="info-card">
                            <a href="javascript:history.back()" style="color: white; text-decoration: none;">
                                <i class="fa fa-arrow-left"></i>
                                Kembali
                            </a>
                        </div>
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fa fa-calendar-alt"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-value"><?= date('d') ?></div>
                                <div class="info-label"><?= date('M Y') ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Overview -->
        <div class="stats-overview">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon primary">
                        <i class="fa fa-ruler"></i>
                    </div>
                    <div class="stat-change positive"></div>
                </div>
                <div class="stat-value"><?= $total_rows ?></div>
                <div class="stat-label">Total Satuan</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon success">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="stat-change positive"></div>
                </div>
                <div class="stat-value"><?= count(array_filter($tampilData, function($item) { 
                    return !empty($item->satuan_deskripsi); 
                })) ?></div>
                <div class="stat-label">Dengan Deskripsi</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon warning">
                        <i class="fa fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-change positive"></div>
                </div>
                <div class="stat-value"><?= count(array_filter($tampilData, function($item) { 
                    return empty($item->satuan_deskripsi); 
                })) ?></div>
                <div class="stat-label">Tanpa Deskripsi</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon info">
                        <i class="fa fa-chart-bar"></i>
                    </div>
                    <div class="stat-change positive"></div>
                </div>
                <div class="stat-value"><?= count(array_unique(array_column($tampilData, 'satuan_nama'))) ?></div>
                <div class="stat-label">Nama Unik</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h5><i class="fa fa-bolt text-dark"></i> Aksi Cepat</h5>
            <div class="action-buttons">
                <a href="<?= base_url($controller.'/formAdd')?>" class="action-btn">
                    <i class="fa fa-plus"></i>
                    Tambah Satuan
                </a>
                <button class="action-btn" onclick="exportToExcel()">
                    <i class="fa fa-download"></i>
                    Export Excel
                </button>
                <button class="action-btn" onclick="printTable()">
                    <i class="fa fa-print"></i>
                    Print Data
                </button>
            </div>
        </div>

        <!-- Unit Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fa fa-list me-2"></i>Daftar Satuan</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="datatable-details">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th style="width: 200px;">Satuan</th>
                                <th style="width: 400px;">Deskripsi</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $no = 1;
                                foreach ($tampilData as $dt):
                            ?>
                            <tr>
                                <td class="text-center"><?= $no ?></td>
                                <td>
                                    <div class="fw-bold text-primary"><?= $dt->satuan_nama ?></div>
                                    <small class="text-muted">ID: <?= $dt->satuan_id ?></small>
                                </td>
                                <td>
                                    <?php if (!empty($dt->satuan_deskripsi)): ?>
                                        <div class="text-dark"><?= $dt->satuan_deskripsi ?></div>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic">Tidak ada deskripsi</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="<?= base_url($controller.'/viewData/'.$dt->satuan_id)?>" class="btn btn-view btn-sm" title="Lihat Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url($controller.'/formUpdate/'.$dt->satuan_id)?>" class="btn btn-edit btn-sm" title="Edit Satuan">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url($controller.'/actionDelete/'.$dt->satuan_id)?>" class="btn btn-delete btn-sm" title="Hapus Satuan">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                                $no++;
                                endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Animate statistics on page load
    function animateStats() {
        const statValues = document.querySelectorAll('.stat-value');
        statValues.forEach(stat => {
            const finalValue = parseInt(stat.textContent);
            let currentValue = 0;
            const increment = finalValue / 50;
            
            const timer = setInterval(() => {
                currentValue += increment;
                if (currentValue >= finalValue) {
                    currentValue = finalValue;
                    clearInterval(timer);
                }
                stat.textContent = Math.floor(currentValue);
            }, 30);
        });
    }

    // Initialize statistics animation
    animateStats();
});

// Enhanced Export to Excel function
function exportToExcel() {
    const table = document.getElementById('datatable-details');
    const rows = Array.from(table.querySelectorAll('tr'));
    
    let csv = 'No,Satuan,Deskripsi\n';
    
    rows.slice(1).forEach((row, index) => {
        const cells = Array.from(row.querySelectorAll('td'));
        const rowData = [
            index + 1,
            cells[1]?.textContent?.trim() || '',
            cells[2]?.textContent?.trim() || ''
        ];
        csv += rowData.map(cell => `"${cell}"`).join(',') + '\n';
    });
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'data_satuan.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

// Enhanced Print function
function printTable() {
    const printWindow = window.open('', '_blank');
    const table = document.getElementById('datatable-details');
    
    let html = `
        <html>
        <head>
            <title>Data Satuan</title>
            <style>
                body { font-family: Arial, sans-serif; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #2c3e50; color: white; }
                h1 { color: #2c3e50; }
                .unit-info { margin-bottom: 20px; }
            </style>
        </head>
        <body>
            <h1>Data Satuan</h1>
            <div class="unit-info">
                <p><strong>Total Satuan:</strong> ${document.querySelector('.stat-value').textContent}</p>
                <p><strong>Tanggal Cetak:</strong> ${new Date().toLocaleDateString('id-ID')}</p>
            </div>
            ${table.outerHTML}
        </body>
        </html>
    `;
    
    printWindow.document.write(html);
    printWindow.document.close();
    printWindow.print();
}
</script>