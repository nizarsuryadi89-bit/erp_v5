<style>
:root{--primary-gradient:linear-gradient(135deg,#1a1c23 0%,#2c3e50 100%);--premium-shadow:0 10px 30px rgba(0,0,0,.05);--slate-dark:#1e293b;--slate-muted:#64748b;}
.report-hero{background:var(--primary-gradient);color:white;padding:2rem 0 4.5rem;border-radius:0 0 30px 30px;box-shadow:0 15px 35px rgba(0,0,0,.15);}
.report-content-area{margin-top:-3rem;position:relative;z-index:10;padding-bottom:2rem;}
.report-content-card{background:white;border-radius:24px;padding:2rem;box-shadow:var(--premium-shadow);border:1px solid #f1f5f9;margin-bottom:1.5rem;}
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
                        <i class="fa fa-line-chart" style="color:#818cf8;"></i>
                    </div>
                    <div>
                        <h1 style="font-size:1.8rem;font-weight:900;margin:0 0 .2rem 0;">Tren Pembelian Bulanan</h1>
                        <p style="margin:0;color:#94a3b8;font-size:.85rem;">Grafik pengeluaran pembelian bahan baku 12 bulan terakhir</p>
                    </div>
                </div>
                <a href="<?= base_url('warehouse/analytics') ?>" class="btn-back">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="report-content-area">
        <div class="container-fluid px-5">

            <!-- Chart Card -->
            <div class="report-content-card">
                <div class="section-title">Grafik Nilai Pembelian per Bulan</div>
                <canvas id="trendChart" style="max-height:350px;"></canvas>
            </div>

            <!-- Table Card -->
            <div class="report-content-card">
                <div class="section-title">Tabel Rekap Bulanan</div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dt-trend">
                        <thead style="background:#1e293b;color:white;">
                            <tr>
                                <th>No</th>
                                <th>Bulan</th>
                                <th class="text-center">Total Transaksi</th>
                                <th class="text-right">Total Nilai Pembelian (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $grand = 0;
                            foreach ($trend as $row):
                                $grand += $row->total_nilai;
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row->bulan_label ?></td>
                                    <td class="text-center"><?= $row->total_transaksi ?></td>
                                    <td class="text-right"><strong><?= number_format($row->total_nilai, 0, ',', '.') ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr style="background:#f8fafc;">
                                <th colspan="3" class="text-right">TOTAL 12 BULAN</th>
                                <th class="text-right" style="color:#27ae60;font-size:1.1rem;">
                                    Rp <?= number_format($grand, 0, ',', '.') ?>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
$(document).ready(function(){
    $('#dt-trend').DataTable({
        "language": {"url": "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"},
        "pageLength": 12,
        "order": [[0, "asc"]]
    });

    var raw = <?= $trend_json ?>;
    var labels = raw.map(function(r){ return r.bulan_label; });
    var values = raw.map(function(r){ return parseFloat(r.total_nilai); });

    new Chart(document.getElementById('trendChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Pembelian (Rp)',
                data: values,
                backgroundColor: 'rgba(99,102,241,0.6)',
                borderColor: '#6366f1',
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx){
                            return 'Rp ' + ctx.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    ticks: {
                        callback: function(v){
                            return 'Rp ' + (v/1000000).toFixed(1) + 'jt';
                        }
                    }
                }
            }
        }
    });
});
</script>
