<style>
    /* Enhanced Purchase Detail Styles */
    .purchase-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 2.5rem 0;
        margin-bottom: 2rem;
        border-radius: 0;
        box-shadow: 0 8px 30px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
    }
    
    .purchase-header::before {
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
    
    .purchase-details {
        background: white;
        border-radius: 0;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        margin-bottom: 2rem;
    }
    
    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .detail-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
        border-left: 4px solid #2c3e50;
    }
    
    .detail-label {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }
    
    .detail-value {
        font-size: 1.2rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
    }
    
    .total-amount {
        background: linear-gradient(135deg, #2c3e50, #34495e);
        color: white;
        border-radius: 8px;
        padding: 1.5rem;
        text-align: center;
    }
    
    .total-label {
        font-size: 1rem;
        opacity: 0.9;
        margin-bottom: 0.5rem;
    }
    
    .total-value {
        font-size: 2rem;
        font-weight: 800;
        margin: 0;
    }
    
    .purchase-table {
        background: white;
        border-radius: 0;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        overflow: hidden;
    }
    
    .table-header {
        background: linear-gradient(135deg, #2c3e50, #34495e);
        color: white;
        padding: 1.5rem;
    }
    
    .table-header h5 {
        margin: 0;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .table-responsive {
        overflow-x: auto;
    }
    
    .table {
        margin: 0;
    }
    
    .table th {
        background: #f8f9fa;
        border: none;
        padding: 1rem;
        font-weight: 600;
        color: #2c3e50;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.85rem;
    }
    
    .table td {
        border: none;
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
    }
    
    .table tbody tr:hover {
        background: #f8f9fa;
    }
    
    .price-input {
        border: 2px solid #e9ecef;
        border-radius: 6px;
        padding: 0.5rem;
        font-weight: 600;
        color: #2c3e50;
        transition: all 0.3s ease;
    }
    
    .price-input:focus {
        border-color: #2c3e50;
        box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.25);
        outline: none;
    }
    
    .update-btn {
        background: linear-gradient(135deg, #27ae60, #2ecc71);
        border: none;
        border-radius: 6px;
        color: white;
        padding: 0.5rem 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .update-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(39, 174, 96, 0.4);
        color: white;
    }
    
    .back-btn {
        background: linear-gradient(135deg, #6c757d, #495057);
        border: none;
        border-radius: 6px;
        color: white;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }
    
    .back-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.4);
        color: white;
        text-decoration: none;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .purchase-header {
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
        
        .details-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .purchase-details {
            padding: 1.5rem;
        }
        
        .table-responsive {
            font-size: 0.9rem;
        }
        
        .table th,
        .table td {
            padding: 0.75rem 0.5rem;
        }
        
        .price-input {
            font-size: 0.9rem;
            padding: 0.4rem;
        }
        
        .update-btn {
            padding: 0.4rem 0.8rem;
            font-size: 0.9rem;
        }
    }
</style>

<div class="app-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="purchase-header">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-7">
                    <div class="header-content">
                        <div class="header-icon">
                            <i class="fa fa-shopping-cart"></i>
                        </div>
                        <div class="header-text">
                            <h1 class="header-title">Detail Pembelian</h1>
                            <h3 class="header-subtitle"><?= $getData['supplier_nama'] ?></h3>
                            <p class="header-description">Informasi lengkap transaksi pembelian bahan baku dari supplier</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-5">
                    <div class="header-info">
                        <div class="info-card">
                            <a href="javascript:history.back()" class="back-btn" style="color: white; text-decoration: none;">
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
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fa fa-clock"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-value"><?= date('H:i') ?></div>
                                <div class="info-label">Waktu</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase Details -->
        <div class="purchase-details">
            <div class="details-grid">
                <div class="detail-item">
                    <div class="detail-label">Nama Supplier</div>
                    <div class="detail-value"><?= $getData['supplier_nama'] ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Tanggal Pembelian</div>
                    <div class="detail-value"><?= date_indo($getData['pembelian_tgl']) ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Jumlah Item</div>
                    <div class="detail-value"><?= count($tampilData) ?> Item</div>
                </div>
            </div>
            
            <?php
                $total_pembelian = 0;
                foreach ($tampilData as $value) {
                    $total_pembelian += $value->qty * $value->harga_beli;
                }
            ?>
            
            <div class="total-amount">
                <div class="total-label">Total Pembelian</div>
                <div class="total-value">Rp. <?= number_format($total_pembelian) ?>,-</div>
            </div>
        </div>

        <!-- Purchase Items Table -->
        <div class="purchase-table">
            <div class="table-header">
                <h5>
                    <i class="fa fa-list"></i>
                    Daftar Item Pembelian
                </h5>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Bahan Baku</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Harga</th>
                            <th class="text-center">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $no = 1;
                            foreach ($tampilData as $key => $value) {
                                $jumlah = $value->qty * $value->harga_beli;
                        ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td>
                                <strong><?= $value->varian ?></strong>
                            </td>
                            <td class="text-center">
                               
                                    <?= number_format($value->qty) ?>
                              
                            </td>
                            <td class="text-center">
                                <form action="<?= base_url('warehouse/updateHarga') ?>" method="post" class="d-flex align-items-center justify-content-center">
                                    <input type="hidden" name="pemb_detail_id" value="<?= $value->pemb_detail_id ?>">
                                    <input type="hidden" name="pembelian_id" value="<?= $pembelian ?>">
                                    <div class="input-group" style="max-width: 200px;">
                                        <input type="text" class="form-control price-input" name="pemb_harga" value="<?= number_format($value->harga_beli) ?>" style="text-align: right;">
                                        <div class="input-group-append">
                                            <button class="btn update-btn" type="submit">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </td>
                            <td class="text-center">
                                <strong style="color: #2c3e50;">Rp. <?= number_format($jumlah) ?>,-</strong>
                            </td>
                        </tr>
                        <?php
                            $no++;
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>