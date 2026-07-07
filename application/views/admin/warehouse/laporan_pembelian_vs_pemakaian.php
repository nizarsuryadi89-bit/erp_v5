<style>
:root{--primary-gradient:linear-gradient(135deg,#1a1c23 0%,#2c3e50 100%);--premium-shadow:0 10px 30px rgba(0,0,0,.05);--slate-dark:#1e293b;--slate-muted:#64748b;}
.report-hero{background:var(--primary-gradient);color:white;padding:2rem 0 4.5rem;border-radius:0 0 30px 30px;box-shadow:0 15px 35px rgba(0,0,0,.15);}
.report-content-area{margin-top:-3rem;position:relative;z-index:10;padding-bottom:2rem;}
.report-content-card{background:white;border-radius:24px;padding:2rem;box-shadow:var(--premium-shadow);border:1px solid #f1f5f9;}
.filter-bar{background:#f8fafc;border:1px solid #e2e8f0;border-radius:16px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;display:flex;align-items:center;flex-wrap:wrap;gap:12px;}
.filter-bar label{font-weight:700;font-size:.85rem;color:var(--slate-dark);margin:0;}
.filter-bar .form-control{border-radius:10px;border:1px solid #e2e8f0;height:38px;font-size:.85rem;}
.btn-filter{background:linear-gradient(135deg,#6366f1,#4338ca);color:white;border:none;border-radius:10px;padding:0 20px;height:38px;font-weight:700;cursor:pointer;}
.btn-back{background:rgba(255,255,255,.1);color:#94a3b8;border:1px solid rgba(255,255,255,.2);border-radius:10px;padding:0 16px;height:38px;font-weight:700;font-size:.82rem;text-decoration:none;display:inline-flex;align-items:center;gap:6px;}
.btn-back:hover{background:rgba(255,255,255,.2);color:white;}
.btn-print{background:linear-gradient(135deg,#1e293b,#334155);color:white;border:none;border-radius:10px;padding:0 18px;height:38px;font-weight:700;font-size:.82rem;cursor:pointer;display:inline-flex;align-items:center;gap:6px;}
.btn-print:hover{opacity:.88;color:white;}
@media print{.report-hero,.btn-back,.btn-print,.filter-bar,.dataTables_wrapper .dataTables_filter,.dataTables_wrapper .dataTables_length,.dataTables_wrapper .dataTables_info,.dataTables_wrapper .dataTables_paginate,.main-header,.main-sidebar,.content-header,.control-sidebar,footer{display:none !important;}.report-content-area{margin-top:0 !important;}.report-content-card{box-shadow:none !important;border:none !important;border-radius:0 !important;padding:0 !important;}table{font-size:9pt !important;width:100% !important;}thead th{background-color:#1e293b !important;color:white !important;-webkit-print-color-adjust:exact;print-color-adjust:exact;}.print-header{display:block !important;}body{background:white !important;}.content-wrapper{margin-left:0 !important;}}
.print-header{display:none;text-align:center;margin-bottom:20px;}
.print-header h2{font-size:16pt;font-weight:900;margin:0 0 4px 0;}
.print-header p{font-size:9pt;color:#666;margin:0;}
.print-header hr{border-top:2px solid #1e293b;margin:8px 0;}
.section-title{font-size:1rem;font-weight:800;color:var(--slate-dark);margin-bottom:1.25rem;display:flex;align-items:center;gap:10px;}
.section-title::before{content:'';display:inline-block;width:4px;height:20px;background:linear-gradient(135deg,#6366f1,#4338ca);border-radius:4px;}
</style>

<div class="container-fluid px-4">
    <div class="report-hero">
        <div class="container-fluid px-5">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
                <div style="display:flex;align-items:center;gap:1.25rem;">
                    <div style="width:60px;height:60px;background:rgba(99,102,241,.2);border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;border:1px solid rgba(99,102,241,.3);">
                        <i class="fa fa-balance-scale" style="color:#818cf8;"></i>
                    </div>
                    <div>
                        <h1 style="font-size:1.8rem;font-weight:900;margin:0 0 .2rem 0;">Pembelian vs Pemakaian</h1>
                        <p style="margin:0;color:#94a3b8;font-size:.85rem;">Perbandingan bahan yang dibeli versus yang terpakai dalam satu periode</p>
                    </div>
                </div>
                <a href="<?= base_url('warehouse/analytics') ?>" class="btn-back"><i class="fa fa-arrow-left"></i> Kembali</a>
                <button onclick="window.print()" class="btn-print"><i class="fa fa-print"></i> Cetak</button>
            </div>
        </div>
    </div>

    <div class="report-content-area">
        <div class="container-fluid px-5">
            <div class="report-content-card">

                <form action="<?= base_url('warehouse/laporan_pembelian_vs_pemakaian') ?>" method="GET" class="filter-bar">
                    <label>Dari</label>
                    <input type="date" class="form-control" name="date_from" value="<?= $date_from ?>" style="width:auto;">
                    <label>Sampai</label>
                    <input type="date" class="form-control" name="date_to" value="<?= $date_to ?>" style="width:auto;">
                    <button type="submit" class="btn-filter"><i class="fa fa-search"></i> &nbsp;Tampilkan</button>
                </form>

                <div class="section-title">Perbandingan Pembelian vs Pemakaian per Material</div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dt-pvp">
                        <thead style="background:#1e293b;color:white;">
                            <tr>
                                <th>No</th><th>Nama Material</th><th>Varian</th>
                                <th class="text-right" style="background:#1e8449;">Total Dibeli</th>
                                <th class="text-right" style="background:#b5179e;">Total Terpakai</th>
                                <th class="text-right">Sisa Bersih</th>
                                <th>Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=1; foreach ($comparison as $row): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row->bahan_nama ?></td>
                                    <td><?= $row->varian ?: '-' ?></td>
                                    <?php 
                                        $konv = (float)$row->konversi > 0 ? (float)$row->konversi : 1;
                                        $show_prod = (!empty($row->satuan_produksi) && !empty($row->satuan_nama) && strtolower(trim($row->satuan_produksi)) !== strtolower(trim($row->satuan_nama)));
                                        
                                        $format_mixed = function($qty) use ($konv, $show_prod, $row) {
                                            if (!$show_prod || $konv <= 1) return '<strong>' . number_format($qty, 2) . '</strong>';
                                            
                                            $is_neg = $qty < 0;
                                            $qty_abs = abs($qty);
                                            $whole = floor($qty_abs);
                                            $remainder_gudang = $qty_abs - $whole;
                                            $remainder_prod = $remainder_gudang * $konv;
                                            
                                            $parts = [];
                                            if ($whole > 0) $parts[] = '<strong>' . $whole . '</strong> <small>' . htmlspecialchars($row->satuan_nama) . '</small>';
                                            if (round($remainder_prod, 2) > 0) $parts[] = '<strong>' . number_format($remainder_prod, 2) . '</strong> <small>' . htmlspecialchars($row->satuan_produksi) . '</small>';
                                            
                                            if (empty($parts)) return '<strong>0</strong>';
                                            return ($is_neg ? '-' : '') . implode(' ', $parts);
                                        };
                                    ?>
                                    <td class="text-right text-success">
                                        <?= $format_mixed((float)$row->total_dibeli) ?>
                                    </td>
                                    <td class="text-right text-danger">
                                        <?= $format_mixed((float)$row->total_terpakai) ?>
                                    </td>
                                    <td class="text-right" style="color:<?= (float)$row->sisa_bersih >= 0 ? '#27ae60' : '#e74c3c' ?>;">
                                        <?= $format_mixed((float)$row->sisa_bersih) ?>
                                    </td>
                                    <td>
                                        <strong><?= $row->satuan_nama ?></strong>
                                        <?php if ($show_prod && $konv > 1): ?>
                                            <div class="small text-muted mt-1" style="font-size:0.7rem;">(1 <?= $row->satuan_nama ?> = <?= $konv ?> <?= $row->satuan_produksi ?>)</div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function(){
    $('#dt-pvp').DataTable({
        "language":{"url":"https://cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"},
        "pageLength":25,"order":[[3,"desc"]]
    });
});
</script>
