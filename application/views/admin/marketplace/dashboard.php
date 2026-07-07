<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
    /* ==========================================================================
    PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - MARKETPLACE DASHBOARD
    ========================================================================== */
    :root {
        --primary-gradient: linear-gradient(135deg, #1a1c23 0%, #2c3e50 100%);
        --success-gradient: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
        --info-gradient: linear-gradient(135deg, #4895ef 0%, #4cc9f0 100%);
        --warning-gradient: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);
        --danger-gradient: linear-gradient(135deg, #f72585 0%, #b5179e 100%);
        --marketplace-gradient: linear-gradient(135deg, #03AC0E 0%, #18181b 35%, #18181b 65%, #EE4D2D 100%);
        --slate-soft: #f8fafc;
        --slate-muted: #64748b;
        --slate-dark: #1e293b;
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        --accent-blue: #6366f1;
    }

    body {
        background-color: #f8fafc;
    }

    /* Executive Hero Header */
    .marketplace-dashboard {
        background: var(--marketplace-gradient);
        color: white;
        padding: 1.5rem 0 3.25rem 0;
        margin-bottom: 0;
        border-radius: 0 0 24px 24px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        position: relative;
        overflow: hidden;
    }

    .marketplace-dashboard::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%);
        border-radius: 50%;
        z-index: 1;
    }

    .dashboard-content {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        position: relative;
        z-index: 2;
    }

    .dashboard-icon {
        width: 52px;
        height: 52px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        color: white;
    }

    .dashboard-title {
        font-size: 1.75rem;
        font-weight: 900;
        margin: 0;
        letter-spacing: -0.5px;
        line-height: 1.2;
    }

    .dashboard-subtitle {
        font-size: 0.85rem;
        font-weight: 700;
        margin: 0 0 2px 0;
        opacity: 0.7;
        color: #e2e8f0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .dashboard-subtitle::before {
        content: '';
        width: 12px;
        height: 2px;
        background: #93c5fd;
        display: inline-block;
    }

    .btn-premium {
        border-radius: 10px;
        padding: 0.45rem 1rem;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.5px;
        transition: all 0.3s;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    /* Metric Cards */
    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.75rem;
        margin-top: -2.5rem;
        position: relative;
        z-index: 10;
        padding-bottom: 2.5rem;
    }

    .stat-card {
        background: white;
        border-radius: 24px;
        padding: 1.75rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid rgba(255, 255, 255, 0.8);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        border-color: var(--accent-blue);
    }

    .stat-header {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        margin-bottom: 0.5rem;
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
    }

    .stat-icon.trx { background: var(--marketplace-gradient); }
    .stat-icon.omset-today { background: var(--success-gradient); }
    .stat-icon.omset-month { background: var(--info-gradient); }
    .stat-icon.pending { background: var(--warning-gradient); }

    .stat-info h3 {
        font-size: 2.25rem;
        font-weight: 900;
        margin: 0;
        color: var(--slate-dark);
        line-height: 1;
    }
    
    .stat-info h3.text-success { color: #10b981 !important; }
    .stat-info h3.text-info { color: #3b82f6 !important; }
    .stat-info h3.text-warning { color: #f59e0b !important; }

    .stat-info p {
        font-size: 0.8rem;
        color: var(--slate-muted);
        margin: 0;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Custom Platform Settings Card */
    .premium-card {
        background: white;
        border-radius: 28px;
        padding: 2rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid #f1f5f9;
        margin-bottom: 2.5rem;
    }
    
    .premium-card h4 {
        color: var(--slate-dark);
        font-weight: 900;
        margin-bottom: 1.5rem;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    @media (max-width: 768px) {
        .marketplace-dashboard {
            padding: 2.5rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 25px 25px;
        }

        .dashboard-content {
            flex-direction: column;
            text-align: center;
            gap: 1.25rem;
        }

        .dashboard-icon {
            width: 70px;
            height: 70px;
            font-size: 2.2rem;
            border-radius: 20px;
        }

        .dashboard-title {
            font-size: 1.75rem;
            margin-bottom: 0.25rem;
        }

        .dashboard-subtitle {
            justify-content: center;
            font-size: 1rem;
        }

        .stats-overview {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-top: -3.5rem;
            padding: 0 10px 1.5rem 10px;
        }

        .stat-card {
            padding: 1.25rem;
            border-radius: 20px;
        }
        
        .stat-icon {
            width: 44px;
            height: 44px;
            font-size: 1.2rem;
        }
        
        .stat-info h3 {
            font-size: 1.5rem;
        }
        
        .stat-info p {
            font-size: 0.65rem;
        }
        
        .premium-card {
            padding: 1.5rem;
            border-radius: 24px;
        }
        
        .header-actions-mobile {
            justify-content: center !important;
            margin-top: 1rem;
        }
    }
</style>

<div class="container-fluid px-4 p-0">
    <!-- ================= DASHBOARD HEADER ================= -->
    <div class="marketplace-dashboard">
        <div class="container-fluid px-4 px-md-5">
            <div class="row align-items-center warehouse-header-row">
                <div class="col-md-6">
                    <div class="dashboard-content">
                        <div class="dashboard-icon">
                            <i class="fas fa-store"></i>
                        </div>
                        <div class="dashboard-text">
                            <p class="dashboard-subtitle">Modul Transaksi</p>
                            <h1 class="dashboard-title">Dashboard Marketplace</h1>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-end d-flex gap-2 justify-content-end header-actions-mobile flex-wrap position-relative" style="z-index: 2;">
                    <?php $this->load->view('admin/marketplace/_header_menu'); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid px-4 px-md-5">
        <!-- ================= HIGH-LEVEL STATS ================= -->
        <div class="stats-overview">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon trx"><i class="fas fa-shopping-bag"></i></div>
                    <div class="stat-info">
                        <h3><?= number_format($metrics['trx_today']) ?></h3>
                        <p>Trx Hari Ini</p>
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon omset-today"><i class="fas fa-wallet"></i></div>
                    <div class="stat-info">
                        <h3 class="text-success" style="font-size: 1.5rem; line-height: 2.25rem;">Rp <?= number_format($metrics['omset_today'], 0, ',', '.') ?></h3>
                        <p>Omset Hari Ini</p>
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon omset-month"><i class="fas fa-chart-line"></i></div>
                    <div class="stat-info">
                        <h3 class="text-info" style="font-size: 1.5rem; line-height: 2.25rem;">Rp <?= number_format($metrics['omset_month'], 0, ',', '.') ?></h3>
                        <p>Omset Bulan Ini</p>
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon pending"><i class="fas fa-clock"></i></div>
                    <div class="stat-info">
                        <h3 class="text-warning"><?= number_format($metrics['pending_produksi']) ?></h3>
                        <p>Pending Produksi</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= PLATFORMS MANAGEMENT ================= -->
        <div class="premium-card">
            <h4><i class="fas fa-cogs text-primary"></i> Pengaturan Platform Marketplace</h4>
            <div class="table-responsive mt-4">
                <table class="table table-vcenter table-hover card-table table-striped-columns">
                    <thead class="table-light text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        <tr>
                            <th width="20%">Platform</th>
                            <th width="15%" class="text-center">Status</th>
                            <th width="20%">Biaya Admin (%)</th>
                            <th>Tautan Toko (URL)</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($platforms as $p): ?>
                        <tr>
                            <td>
                                <?php 
                                    $plat = strtolower($p->marketplace_nama ?: '');
                                    $bg_style = 'background-color: #007bff;';
                                    if (strpos($plat, 'tokopedia') !== false) {
                                        $bg_style = 'background-color: #42b549 !important; color: #fff; box-shadow: 0 4px 10px rgba(66, 181, 73, 0.3);';
                                    } elseif (strpos($plat, 'shopee') !== false) {
                                        $bg_style = 'background-color: #ee4d2d !important; color: #fff; box-shadow: 0 4px 10px rgba(238, 77, 45, 0.3);';
                                    } elseif (strpos($plat, 'tiktok') !== false) {
                                        $bg_style = 'background-color: #000000 !important; color: #fff; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);';
                                    } elseif (strpos($plat, 'lazada') !== false) {
                                        $bg_style = 'background-color: #0f146d !important; color: #fff; box-shadow: 0 4px 10px rgba(15, 20, 109, 0.3);';
                                    }
                                ?>
                                <span class="badge px-4 py-2 fs-6 rounded-pill" style="<?= $bg_style ?>">
                                    <?= htmlspecialchars($p->marketplace_nama) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <label class="form-check form-switch form-switch-lg d-flex justify-content-center m-0">
                                    <input class="form-check-input update-platform shadow-sm" type="checkbox" 
                                           data-id="<?= $p->marketplace_id ?>" 
                                           data-field="is_active" 
                                           <?= ($p->is_active == 1) ? 'checked' : '' ?> style="cursor: pointer;">
                                </label>
                            </td>
                            <td>
                                <div class="input-group input-group-flat shadow-sm rounded">
                                    <input type="number" class="form-control update-platform-input border-0" 
                                           data-id="<?= $p->marketplace_id ?>" 
                                           data-field="biaya_admin_persen" 
                                           value="<?= htmlspecialchars($p->biaya_admin_persen) ?>" 
                                           step="0.01" min="0" max="100" style="background: #f8fafc;">
                                    <span class="input-group-text border-0 bg-light text-muted">%</span>
                                </div>
                            </td>
                            <td>
                                <input type="text" class="form-control update-platform-input shadow-sm border-light" 
                                       data-id="<?= $p->marketplace_id ?>" 
                                       data-field="url_toko" 
                                       value="<?= htmlspecialchars($p->url_toko) ?>" 
                                       placeholder="https://..." style="background: #f8fafc;">
                            </td>
                            <td class="text-center">
                                <?php if(!empty($p->url_toko)): ?>
                                    <a href="<?= htmlspecialchars($p->url_toko) ?>" target="_blank" class="btn btn-outline-primary btn-sm btn-icon shadow-sm rounded-circle" title="Buka Toko">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-outline-secondary btn-sm btn-icon shadow-sm rounded-circle disabled">
                                        <i class="fas fa-external-link-alt"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 pt-3 border-top text-end">
                <small class="text-muted fw-bold"><i class="fas fa-info-circle me-1 text-primary"></i> Perubahan tersimpan secara otomatis (Auto-save).</small>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    function savePlatformData(id, field, value, element) {
        $(element).addClass('is-valid');
        
        $.ajax({
            url: '<?= base_url("marketplace/update_platform") ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                id: id,
                field: field,
                value: value
            },
            success: function(res) {
                if(res.status === 'success') {
                    setTimeout(() => $(element).removeClass('is-valid'), 1000);
                    if(field === 'url_toko') {
                        location.reload();
                    }
                } else {
                    alert('Gagal menyimpan: ' + res.message);
                    $(element).removeClass('is-valid').addClass('is-invalid');
                }
            },
            error: function() {
                alert('Terjadi kesalahan jaringan.');
                $(element).removeClass('is-valid').addClass('is-invalid');
            }
        });
    }

    $('.update-platform').on('change', function() {
        const id = $(this).data('id');
        const field = $(this).data('field');
        const value = $(this).is(':checked') ? 1 : 0;
        savePlatformData(id, field, value, this);
    });

    let typingTimer;
    const doneTypingInterval = 800;

    $('.update-platform-input').on('keyup change', function() {
        clearTimeout(typingTimer);
        const element = this;
        const id = $(this).data('id');
        const field = $(this).data('field');
        const value = $(this).val();

        typingTimer = setTimeout(function() {
            savePlatformData(id, field, value, element);
        }, doneTypingInterval);
    });
});
</script>
