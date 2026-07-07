<!-- PREMIUM STYLE -->
<style>
    :root { --marketplace-gradient: linear-gradient(135deg, #03AC0E 0%, #18181b 35%, #18181b 65%, #EE4D2D 100%); }
    .marketplace-dashboard { background: var(--marketplace-gradient); color: white; padding: 1.5rem 0 2rem 0; margin-bottom: 2rem; border-radius: 0 0 24px 24px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08); position: relative; overflow: hidden; }
    .marketplace-dashboard::after { content: ''; position: absolute; top: -50%; right: -10%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%); border-radius: 50%; z-index: 1; }
    .dashboard-content { display: flex; align-items: center; gap: 1.25rem; position: relative; z-index: 2; }
    .dashboard-icon { width: 52px; height: 52px; background: rgba(255, 255, 255, 0.12); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); color: white; }
    .dashboard-title { font-size: 1.75rem; font-weight: 900; margin: 0; letter-spacing: -0.5px; line-height: 1.2; color: white; }
    .dashboard-subtitle { font-size: 0.85rem; font-weight: 700; margin: 0 0 2px 0; opacity: 0.7; color: #e2e8f0; display: flex; align-items: center; gap: 10px; }
    .dashboard-subtitle::before { content: ''; width: 12px; height: 2px; background: #93c5fd; display: inline-block; }
    .btn-premium { border-radius: 10px; padding: 0.45rem 1rem; font-weight: 800; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px; transition: all 0.3s; text-decoration: none !important; display: inline-flex; align-items: center; justify-content: center; gap: 6px; }
    @media (max-width: 768px) {
        .marketplace-dashboard { padding: 2.5rem 0; border-radius: 0 0 25px 25px; }
        .dashboard-content { flex-direction: column; text-align: center; gap: 1.25rem; }
        .dashboard-icon { width: 70px; height: 70px; font-size: 2.2rem; border-radius: 20px; }
        .dashboard-title { font-size: 1.75rem; margin-bottom: 0.25rem; }
        .dashboard-subtitle { justify-content: center; font-size: 1rem; }
        .header-actions-mobile { justify-content: center !important; margin-top: 1rem; }
    }
</style>

<!-- PAGE HEADER -->
<div class="marketplace-dashboard d-print-none">
    <div class="container-xl px-4">
        <div class="row align-items-center">
            <div class="col-md-7">
                <div class="dashboard-content">
                    <div class="dashboard-icon">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="dashboard-text">
                        <p class="dashboard-subtitle">Modul Transaksi</p>
                        <h1 class="dashboard-title">Detail Marketplace (SPK <?= $detail['nospk'] ?>)</h1>
                    </div>
                </div>
            </div>
            <div class="col-md-5 text-end d-flex gap-2 justify-content-md-end justify-content-center header-actions-mobile flex-wrap position-relative mt-3 mt-md-0" style="z-index: 2;">
                <?php $this->load->view('admin/marketplace/_header_menu'); ?>
                <a href="<?= base_url('marketplace') ?>" class="btn btn-outline-light btn-premium shadow-sm ms-md-2" style="border-radius: 50px;">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
            <?php if($this->session->flashdata('success')): ?>
                <div class="alert alert-success">
                    <div class="alert-body">
                        <?= $this->session->flashdata('success') ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if($this->session->flashdata('error')): ?>
                <div class="alert alert-danger">
                    <div class="alert-body">
                        <?= $this->session->flashdata('error') ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Info SPK -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h4 class="card-title mb-0">Informasi Pesanan</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td width="40%" class="text-muted">No SPK</td>
                                    <td class="fw-bold">: <?= $detail['nospk'] ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tgl Dibuat</td>
                                    <td class="fw-semibold">: <?= date('d M Y H:i', strtotime($detail['created_at'])) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Platform</td>
                                    <td>: 
                                        <?php 
                                            $plat = strtolower($detail['marketplace_nama'] ?: '');
                                            $bg_style = 'background-color: #007bff;'; // default blue
                                            if (strpos($plat, 'tokopedia') !== false) {
                                                $bg_style = 'background-color: #42b549 !important; color: #fff;';
                                            } elseif (strpos($plat, 'shopee') !== false) {
                                                $bg_style = 'background-color: #ee4d2d !important; color: #fff;';
                                            } elseif (strpos($plat, 'tiktok') !== false) {
                                                $bg_style = 'background-color: #000000 !important; color: #fff;';
                                            } elseif (strpos($plat, 'lazada') !== false) {
                                                $bg_style = 'background-color: #0f146d !important; color: #fff;';
                                            }
                                        ?>
                                        <span class="badge" style="<?= $bg_style ?>"><?= $detail['marketplace_nama'] ?: 'LAINNYA' ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Konsumen</td>
                                    <td class="fw-semibold">: <?= $detail['konsumen_nama'] ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">No. HP</td>
                                    <td>: <?= $detail['konsumen_nohp'] ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Alamat</td>
                                    <td>: <?= $detail['konsumen_alamat'] ?: '-' ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h4 class="card-title mb-0">Pengiriman & Resi</h4>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('marketplace/update_resi') ?>" method="POST">
                                <input type="hidden" name="nospk" value="<?= $detail['nospk'] ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Kurir Pengiriman</label>
                                    <input type="text" class="form-control bg-light" value="<?= $detail['nama_pengiriman'] ?: 'Belum diset' ?>" readonly disabled>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nomor Resi Marketplace</label>
                                    <input type="text" name="resi_marketplace" class="form-control" value="<?= $detail['resi_marketplace'] ?>" placeholder="Masukkan nomor resi..." required>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" /><circle cx="12" cy="14" r="2" /><polyline points="14 4 14 8 8 8 8 4" /></svg> 
                                    Update Resi
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Rincian Item -->
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Item Pesanan</h4>
                            <div>
                                <a href="<?= base_url('transaksi/preview_invoice/'.$detail['nospk']) ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                                    Cetak SPK
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-vcenter table-hover table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" width="50">No</th>
                                            <th>Produk</th>
                                            <th>Detail</th>
                                            <th class="text-center" width="80">Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($items)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted p-4">Belum ada item / transaksi kosong</td>
                                        </tr>
                                        <?php else: ?>
                                            <?php $no=1; foreach($items as $i): ?>
                                            <tr>
                                                <td class="text-center text-muted small"><?= $no++ ?></td>
                                                <td><div class="fw-semibold text-dark"><?= $i->product_nama ?></div></td>
                                                <td>
                                                    <div class="small text-muted">
                                                        <?php
                                                            $detail_text = [];
                                                            if($i->panjang > 0 && $i->lebar > 0) $detail_text[] = "Ukuran: {$i->panjang} x {$i->lebar}";
                                                            if($i->ket_1) $detail_text[] = "Bahan: {$i->ket_1}";
                                                            if($i->finishing) $detail_text[] = "Finishing: {$i->finishing}";
                                                            echo implode("<br>", $detail_text) ?: '-';
                                                        ?>
                                                    </div>
                                                </td>
                                                <td class="text-center fw-semibold"><?= $i->qty ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
