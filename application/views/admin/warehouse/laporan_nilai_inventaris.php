<style>
:root{--primary-gradient:linear-gradient(135deg,#1a1c23 0%,#2c3e50 100%);--premium-shadow:0 10px 30px rgba(0,0,0,.05);--slate-dark:#1e293b;--slate-muted:#64748b;}
.report-hero{background:var(--primary-gradient);color:white;padding:2rem 0 4.5rem;border-radius:0 0 30px 30px;box-shadow:0 15px 35px rgba(0,0,0,.15);}
.report-content-area{margin-top:-3rem;position:relative;z-index:10;padding-bottom:2rem;}
.report-content-card{background:white;border-radius:24px;padding:2rem;box-shadow:var(--premium-shadow);border:1px solid #f1f5f9;}
.summary-box{background:#f8fafc;border:1px solid #e2e8f0;border-radius:16px;padding:1.5rem 2rem;margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;}
.summary-val{font-size:1.8rem;font-weight:900;color:#27ae60;}
.summary-label{font-size:.85rem;color:var(--slate-muted);font-weight:700;}
.btn-back{background:rgba(255,255,255,.1);color:#94a3b8;border:1px solid rgba(255,255,255,.2);border-radius:10px;padding:0 16px;height:38px;font-weight:700;font-size:.82rem;text-decoration:none;display:inline-flex;align-items:center;gap:6px;}
.btn-back:hover{background:rgba(255,255,255,.2);color:white;}
.btn-print{background:linear-gradient(135deg,#1e293b,#334155);color:white;border:none;border-radius:10px;padding:0 18px;height:38px;font-weight:700;font-size:.82rem;cursor:pointer;display:inline-flex;align-items:center;gap:6px;text-decoration:none;}
.btn-print:hover{opacity:.88;color:white;}
.section-title{font-size:1rem;font-weight:800;color:var(--slate-dark);margin-bottom:1.25rem;display:flex;align-items:center;gap:10px;}
.section-title::before{content:'';display:inline-block;width:4px;height:20px;background:linear-gradient(135deg,#f39c12,#e67e22);border-radius:4px;}
@media print {
    .report-hero,.btn-back,.btn-print,.summary-box,
    .dataTables_wrapper .dataTables_filter,.dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_info,.dataTables_wrapper .dataTables_paginate,
    .main-header,.main-sidebar,.content-header,.control-sidebar,footer{display:none !important;}
    .report-content-area{margin-top:0 !important;}
    .report-content-card{box-shadow:none !important;border:none !important;border-radius:0 !important;padding:0 !important;}
    table{font-size:9pt !important;width:100% !important;}
    thead th{background-color:#1e293b !important;color:white !important;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
    .print-header{display:block !important;}
    body{background:white !important;}
    .content-wrapper{margin-left:0 !important;}
}
.print-header{display:none;text-align:center;margin-bottom:20px;}
.print-header h2{font-size:16pt;font-weight:900;margin:0 0 4px 0;}
.print-header p{font-size:9pt;color:#666;margin:0;}
.print-header hr{border-top:2px solid #1e293b;margin:8px 0;}
</style>

<div class="print-header">
    <h2>Laporan Nilai Inventaris Gudang</h2>
    <p>Dicetak: <?= date('d/m/Y H:i') ?></p>
    <hr>
</div>

<div class="container-fluid px-4">
    <div class="report-hero">
        <div class="container-fluid px-5">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
                <div style="display:flex;align-items:center;gap:1.25rem;">
                    <div style="width:60px;height:60px;background:rgba(255,255,255,.1);border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;border:1px solid rgba(255,255,255,.2);">
                        <i class="fa fa-money"></i>
                    </div>
                    <div>
                        <h1 style="font-size:1.8rem;font-weight:900;margin:0 0 .2rem 0;">Nilai Inventaris Gudang</h1>
                        <p style="margin:0;color:#94a3b8;font-size:.85rem;">Total nilai aset bahan baku yang tersimpan di gudang saat ini</p>
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

                <div class="summary-box">
                    <div>
                        <div class="summary-label">TOTAL NILAI INVENTARIS (estimasi)</div>
                        <div class="summary-val">Rp <?= number_format($total_nilai, 0, ',', '.') ?></div>
                        <small style="color:var(--slate-muted);">*Dihitung dari harga beli terakhir × stok aktif</small>
                    </div>
                    <div style="text-align:right;">
                        <div class="summary-label">JUMLAH ITEM</div>
                        <div style="font-size:1.5rem;font-weight:900;color:var(--slate-dark);"><?= count($inventaris) ?></div>
                        <small style="color:var(--slate-muted);">jenis material aktif</small>
                    </div>
                </div>

                <div class="section-title">Rincian Nilai per Material</div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dt-inventaris">
                        <thead style="background:#1e293b;color:white;">
                            <tr>
                                <th>No</th><th>Nama Material</th><th>Varian</th><th>Gudang</th>
                                <th class="text-right">Stok Aktif</th><th>Satuan</th>
                                <th class="text-right">Harga Beli Terakhir</th>
                                <th class="text-right">Nilai Stok (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=1; foreach ($inventaris as $row): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row->bahan_nama ?></td>
                                    <td><?= $row->varian ?: '-' ?></td>
                                    <td><?= $row->gudang_nama ?: '-' ?></td>
                                    <td class="text-right"><?= number_format((float)$row->stok_aktif, 2) ?></td>
                                    <td><?= $row->satuan_nama ?></td>
                                    <td class="text-right"><?= $row->harga_beli_terakhir ? number_format($row->harga_beli_terakhir, 0, ',', '.') : '<span class="label label-warning">Belum ada</span>' ?></td>
                                    <td class="text-right"><strong style="color:#27ae60;"><?= $row->nilai_stok ? number_format($row->nilai_stok, 0, ',', '.') : '-' ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr style="background:#f8fafc;">
                                <th colspan="7" class="text-right">GRAND TOTAL</th>
                                <th class="text-right" style="color:#27ae60;font-size:1.1rem;">Rp <?= number_format($total_nilai, 0, ',', '.') ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function(){
    $('#dt-inventaris').DataTable({
        "language":{"url":"https://cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"},
        "pageLength":25,"order":[[7,"desc"]]
    });
});
</script>
