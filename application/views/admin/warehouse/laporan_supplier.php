<style>
:root{--primary-gradient:linear-gradient(135deg,#1a1c23 0%,#2c3e50 100%);--premium-shadow:0 10px 30px rgba(0,0,0,.05);--slate-dark:#1e293b;--slate-muted:#64748b;}
.report-hero{background:var(--primary-gradient);color:white;padding:2rem 0 4.5rem;border-radius:0 0 30px 30px;box-shadow:0 15px 35px rgba(0,0,0,.15);}
.report-content-area{margin-top:-3rem;position:relative;z-index:10;padding-bottom:2rem;}
.report-content-card{background:white;border-radius:24px;padding:2rem;box-shadow:var(--premium-shadow);border:1px solid #f1f5f9;}
.filter-bar{background:#f8fafc;border:1px solid #e2e8f0;border-radius:16px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;display:flex;align-items:center;flex-wrap:wrap;gap:12px;}
.filter-bar label{font-weight:700;font-size:.85rem;color:var(--slate-dark);margin:0;}
.filter-bar .form-control{border-radius:10px;border:1px solid #e2e8f0;height:38px;font-size:.85rem;}
.btn-filter{background:linear-gradient(135deg,#00c0ef,#0099bb);color:white;border:none;border-radius:10px;padding:0 20px;height:38px;font-weight:700;cursor:pointer;}
.btn-back{background:rgba(255,255,255,.1);color:#94a3b8;border:1px solid rgba(255,255,255,.2);border-radius:10px;padding:0 16px;height:38px;font-weight:700;font-size:.82rem;text-decoration:none;display:inline-flex;align-items:center;gap:6px;}
.btn-back:hover{background:rgba(255,255,255,.2);color:white;}
.btn-print{background:linear-gradient(135deg,#1e293b,#334155);color:white;border:none;border-radius:10px;padding:0 18px;height:38px;font-weight:700;font-size:.82rem;cursor:pointer;display:inline-flex;align-items:center;gap:6px;}
.btn-print:hover{opacity:.88;color:white;}
.section-title{font-size:1rem;font-weight:800;color:var(--slate-dark);margin-bottom:1.25rem;display:flex;align-items:center;gap:10px;}
.section-title::before{content:'';display:inline-block;width:4px;height:20px;background:linear-gradient(135deg,#00c0ef,#0099bb);border-radius:4px;}
.grand-total-row th{font-size:1.05rem;background:#f8fafc !important;color:var(--slate-dark);}
.grand-total-value{color:#27ae60;font-size:1.15rem;font-weight:900;}
@media print {
    .report-hero,.filter-bar,.btn-back,.btn-print,
    .dataTables_wrapper .dataTables_filter,.dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_info,.dataTables_wrapper .dataTables_paginate,
    .main-header,.main-sidebar,.content-header,.control-sidebar,footer{display:none !important;}
    .report-content-area{margin-top:0 !important;}
    .report-content-card{box-shadow:none !important;border:none !important;border-radius:0 !important;padding:0 !important;}
    table{font-size:9pt !important;width:100% !important;}
    thead th{background-color:#1e293b !important;color:white !important;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
    tfoot th{background-color:#f8fafc !important;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
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
    <h2>Laporan Pembelian per Supplier</h2>
    <p>Periode: <?= date('d/m/Y', strtotime($date_from)) ?> s/d <?= date('d/m/Y', strtotime($date_to)) ?> | Dicetak: <?= date('d/m/Y H:i') ?></p>
    <hr>
</div>

<div class="container-fluid px-4">
    <div class="report-hero">
        <div class="container-fluid px-5">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
                <div style="display:flex;align-items:center;gap:1.25rem;">
                    <div style="width:60px;height:60px;background:rgba(255,255,255,.1);border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;border:1px solid rgba(255,255,255,.2);">
                        <i class="fa fa-truck"></i>
                    </div>
                    <div>
                        <h1 style="font-size:1.8rem;font-weight:900;margin:0 0 .2rem 0;">Pembelian per Supplier</h1>
                        <p style="margin:0;color:#94a3b8;font-size:.85rem;">Rekapitulasi total nilai dan transaksi per supplier</p>
                    </div>
                </div>
                <a href="<?= base_url('warehouse/analytics') ?>" class="btn-back"><i class="fa fa-arrow-left"></i> Kembali</a>
            </div>
        </div>
    </div>

    <div class="report-content-area">
        <div class="container-fluid px-5">
            <div class="report-content-card">

                <form action="<?= base_url('warehouse/laporan_supplier') ?>" method="GET" class="filter-bar">
                    <label>Dari</label>
                    <input type="date" class="form-control" name="date_from" value="<?= $date_from ?>" style="width:auto;">
                    <label>Sampai</label>
                    <input type="date" class="form-control" name="date_to" value="<?= $date_to ?>" style="width:auto;">
                    <button type="submit" class="btn-filter"><i class="fa fa-search"></i> &nbsp;Tampilkan</button>
                    <button type="button" class="btn-print" onclick="window.print()"><i class="fa fa-print"></i> &nbsp;Cetak</button>
                </form>

                <div class="section-title">Rekap Pembelian Berdasarkan Supplier</div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="datatable-supplier">
                        <thead style="background-color:#1e293b;color:white;">
                            <tr>
                                <th>No</th><th>Nama Supplier</th>
                                <th class="text-center">Total Transaksi</th>
                                <th class="text-right">Total Nilai Pembelian (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1; $grand_total = 0;
                            foreach ($supplier_summary as $row):
                                $grand_total += $row->total_nilai;
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row->supplier_nama ?></td>
                                    <td class="text-center"><?= $row->total_transaksi ?></td>
                                    <td class="text-right"><strong><?= number_format($row->total_nilai, 0, ',', '.') ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="grand-total-row">
                                <th colspan="3" class="text-right">GRAND TOTAL</th>
                                <th class="text-right grand-total-value">Rp <?= number_format($grand_total, 0, ',', '.') ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#datatable-supplier').DataTable({
        "language": {"url": "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"},
        "pageLength": 25, "order": [[3, "desc"]]
    });
});
</script>
