<style>
:root{--primary-gradient:linear-gradient(135deg,#1a1c23 0%,#2c3e50 100%);--premium-shadow:0 10px 30px rgba(0,0,0,.05);--slate-dark:#1e293b;--slate-muted:#64748b;}
.report-hero{background:var(--primary-gradient);color:white;padding:2rem 0 5rem;border-radius:0 0 30px 30px;box-shadow:0 15px 35px rgba(0,0,0,.15);}
.report-content-area{margin-top:-3.5rem;position:relative;z-index:10;padding-bottom:2rem;}
.report-content-card{background:white;border-radius:24px;padding:2rem;box-shadow:var(--premium-shadow);border:1px solid #f1f5f9;}
.btn-back{background:rgba(255,255,255,.1);color:#94a3b8;border:1px solid rgba(255,255,255,.2);border-radius:10px;padding:0 16px;height:38px;font-weight:700;font-size:.82rem;text-decoration:none;display:inline-flex;align-items:center;gap:6px;}
.btn-back:hover{background:rgba(255,255,255,.2);color:white;}
.btn-print{background:linear-gradient(135deg,#1e293b,#334155);color:white;border:none;border-radius:10px;padding:0 18px;height:38px;font-weight:700;font-size:.82rem;cursor:pointer;display:inline-flex;align-items:center;gap:6px;}
.filter-bar{background:#f8fafc;border:1px solid #e2e8f0;border-radius:16px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;display:flex;align-items:center;flex-wrap:wrap;gap:12px;}
.section-title{font-size:1rem;font-weight:800;color:var(--slate-dark);margin-bottom:1.25rem;display:flex;align-items:center;gap:10px;}
.section-title::before{content:'';display:inline-block;width:4px;height:20px;background:linear-gradient(135deg,#1abc9c,#16a085);border-radius:4px;}
.margin-bar-wrap{width:100%;background:#f1f5f9;border-radius:50px;height:8px;margin-top:4px;}
.margin-bar{height:8px;border-radius:50px;}
@media print{.report-hero,.filter-bar,.btn-back,.btn-print,.dataTables_wrapper .dataTables_filter,.dataTables_wrapper .dataTables_length,.dataTables_wrapper .dataTables_info,.dataTables_wrapper .dataTables_paginate,.main-header,.main-sidebar,.content-header,.control-sidebar,footer{display:none !important;}.report-content-area{margin-top:0 !important;}.report-content-card{box-shadow:none !important;border:none !important;border-radius:0 !important;padding:0 !important;}table{font-size:9pt !important;}thead th{background-color:#1e293b !important;color:white !important;-webkit-print-color-adjust:exact;print-color-adjust:exact;}.print-header{display:block !important;}body{background:white !important;}.content-wrapper{margin-left:0 !important;}}
.print-header{display:none;text-align:center;margin-bottom:16px;}
.print-header h2{font-size:16pt;font-weight:900;margin:0 0 4px;}
.print-header p{font-size:9pt;color:#666;margin:0;}
.print-header hr{border-top:2px solid #1e293b;margin:8px 0;}
</style>

<div class="print-header">
    <h2>Laporan HPP & Margin Produk</h2>
    <p>Bulan: <?= date('F Y', strtotime($bulan.'-01')) ?> | Dicetak: <?= date('d/m/Y H:i') ?> | <?= $perusahaan ?></p><hr>
</div>

<div class="container-fluid px-4">
    <div class="report-hero">
        <div class="container-fluid px-5">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
                <div style="display:flex;align-items:center;gap:1.25rem;">
                    <div style="width:65px;height:65px;background:rgba(255,255,255,.1);border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:1.8rem;border:1px solid rgba(255,255,255,.2);"><i class="fa fa-tag"></i></div>
                    <div>
                        <h1 style="font-size:1.9rem;font-weight:900;margin:0 0 .25rem 0;">Analisis HPP & Margin Produk</h1>
                        <p style="margin:0;color:#94a3b8;font-size:.88rem;">Profitabilitas produk: pendapatan vs HPP, ranking margin terbaik</p>
                    </div>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <button onclick="window.print()" class="btn-print"><i class="fa fa-print"></i> Cetak</button>
                    <button type="button" class="btn-kirim-owner no-print"
                        data-toggle="modal" data-target="#modalKirimOwner"
                        data-report-type="hpp_margin"
                        data-params='<?= json_encode(['perusahaan_id'=>(int)$current_perusahaan_id,'bulan'=>$bulan]) ?>'>
                        <i class="fa fa-paper-plane"></i> Kirim ke Owner
                    </button>
                    <a href="<?= base_url('laporan/financial_hub') ?>" class="btn-back"><i class="fa fa-arrow-left"></i> Kembali</a>
                </div>
            </div>
        </div>
    </div>

    <div class="report-content-area">
        <div class="container-fluid px-5">
            <form class="filter-bar" action="" method="GET">
                <label style="font-weight:700;font-size:.85rem;margin:0;">Bulan</label>
                <input type="month" class="form-control" name="bulan" value="<?= $bulan ?>" style="width:auto;border-radius:10px;height:38px;">
                <?php if ($is_admin_owner): ?>
                <select class="form-control" name="perusahaan_id" style="width:auto;border-radius:10px;height:38px;">
                    <?php foreach ($all_perusahaan as $p): ?>
                        <option value="<?= $p->id_perusahaan ?>" <?= $p->id_perusahaan == $current_perusahaan_id ? 'selected' : '' ?>><?= $p->nama_perusahaan ?></option>
                    <?php endforeach; ?>
                </select>
                <?php endif; ?>
                <button type="submit" style="background:linear-gradient(135deg,#1abc9c,#16a085);color:white;border:none;border-radius:10px;padding:0 20px;height:38px;font-weight:700;cursor:pointer;"><i class="fa fa-search"></i> Tampilkan</button>
            </form>

            <?php
            $tot_pend = array_sum(array_column((array)$hpp_data, 'total_pendapatan'));
            $tot_hpp  = array_sum(array_column((array)$hpp_data, 'total_hpp'));
            $tot_mgn  = array_sum(array_column((array)$hpp_data, 'total_margin'));
            $avg_mgn  = $tot_pend > 0 ? ($tot_mgn/$tot_pend)*100 : 0;
            ?>
            <div class="row mb-4">
                <div class="col-md-3">
                    <div style="background:white;border-radius:16px;padding:1.25rem;box-shadow:var(--premium-shadow);border:1px solid #f1f5f9;text-align:center;margin-bottom:1rem;">
                        <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--slate-muted);">Total Pendapatan</div>
                        <div style="font-size:1.4rem;font-weight:900;color:#3b82f6;">Rp <?= number_format($tot_pend,0,',','.') ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div style="background:white;border-radius:16px;padding:1.25rem;box-shadow:var(--premium-shadow);border:1px solid #f1f5f9;text-align:center;margin-bottom:1rem;">
                        <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--slate-muted);">Total HPP</div>
                        <div style="font-size:1.4rem;font-weight:900;color:#f43f5e;">Rp <?= number_format($tot_hpp,0,',','.') ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div style="background:white;border-radius:16px;padding:1.25rem;box-shadow:var(--premium-shadow);border:1px solid #f1f5f9;text-align:center;margin-bottom:1rem;">
                        <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--slate-muted);">Total Margin Kotor</div>
                        <div style="font-size:1.4rem;font-weight:900;color:#10b981;">Rp <?= number_format($tot_mgn,0,',','.') ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div style="background:white;border-radius:16px;padding:1.25rem;box-shadow:var(--premium-shadow);border:1px solid #f1f5f9;text-align:center;margin-bottom:1rem;">
                        <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--slate-muted);">Avg Margin</div>
                        <div style="font-size:1.4rem;font-weight:900;color:<?= $avg_mgn>=20?'#10b981':($avg_mgn>=10?'#f59e0b':'#f43f5e') ?>;"><?= number_format($avg_mgn,1) ?>%</div>
                    </div>
                </div>
            </div>

            <div class="report-content-card">
                <div class="section-title">Ranking Profitabilitas Produk (<?= date('F Y', strtotime($bulan.'-01')) ?>)</div>
                <?php if (empty($hpp_data)): ?>
                <div class="alert alert-info">Tidak ada data transaksi pada bulan ini yang memiliki data HPP. Pastikan HPP produk sudah diisi di master produk.</div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tbl-hpp">
                        <thead style="background-color:#1e293b;color:white;">
                            <tr>
                                <th class="text-center">#</th>
                                <th>Nama Produk</th>
                                <th class="text-center">Qty Terjual</th>
                                <th class="text-right">Pendapatan</th>
                                <th class="text-right">HPP</th>
                                <th class="text-right">Margin Kotor</th>
                                <th class="text-center" style="min-width:140px;">Margin %</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($hpp_data as $idx => $row):
                                $m = (float)$row->avg_margin_pct;
                                $color = $m >= 30 ? '#10b981' : ($m >= 15 ? '#f59e0b' : '#f43f5e');
                                $badge_class = $m >= 30 ? 'success' : ($m >= 15 ? 'warning' : 'danger');
                            ?>
                            <tr>
                                <td class="text-center"><strong><?= $idx+1 ?></strong></td>
                                <td><strong><?= $row->product_nama ?></strong></td>
                                <td class="text-center"><?= number_format($row->total_qty,0,',','.') ?></td>
                                <td class="text-right">Rp <?= number_format($row->total_pendapatan,0,',','.') ?></td>
                                <td class="text-right text-danger">Rp <?= number_format($row->total_hpp,0,',','.') ?></td>
                                <td class="text-right text-success"><strong>Rp <?= number_format($row->total_margin,0,',','.') ?></strong></td>
                                <td class="text-center">
                                    <span class="label label-<?= $badge_class ?>"><?= number_format($m,1) ?>%</span>
                                    <div class="margin-bar-wrap"><div class="margin-bar" style="width:<?= min($m,100) ?>%;background:<?= $color ?>;"></div></div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr style="background:#f8fafc;font-weight:900;">
                                <td colspan="3"><strong>TOTAL</strong></td>
                                <td class="text-right"><strong>Rp <?= number_format($tot_pend,0,',','.') ?></strong></td>
                                <td class="text-right text-danger"><strong>Rp <?= number_format($tot_hpp,0,',','.') ?></strong></td>
                                <td class="text-right text-success"><strong>Rp <?= number_format($tot_mgn,0,',','.') ?></strong></td>
                                <td class="text-center"><strong><?= number_format($avg_mgn,1) ?>%</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
(function _init_hpp_margin(){ if(typeof jQuery==="undefined"){return setTimeout(_init_hpp_margin,80);} var $=jQuery; (function(){
    $('#tbl-hpp').DataTable({
        "language":{"url":"https://cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"},
        "pageLength":25,"order":[[5,"desc"]]
    });
})();
</script>
<?php $this->load->view('admin/laporan/partials/kirim_owner_modal'); ?>
