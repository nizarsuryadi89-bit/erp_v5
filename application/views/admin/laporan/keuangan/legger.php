<?php
/**
 * Buku Besar Bulanan (Legger) - Konsisten dengan Laporan Hub Baru
 */
?>
<style>
:root{--primary-gradient:linear-gradient(135deg,#1a1c23 0%,#2c3e50 100%);--premium-shadow:0 10px 30px rgba(0,0,0,.05);--slate-dark:#1e293b;--slate-muted:#64748b;}
.report-hero{background:var(--primary-gradient);color:white;padding:2rem 0 5.5rem;border-radius:0 0 30px 30px;box-shadow:0 15px 35px rgba(0,0,0,.15);position:relative;overflow:hidden;}
.report-hero::after{content:'';position:absolute;top:-60px;right:-60px;width:280px;height:280px;background:radial-gradient(circle,rgba(99,102,241,.12) 0%,transparent 70%);border-radius:50%;}
.report-content-area{margin-top:-4rem;position:relative;z-index:10;padding-bottom:2rem;}
.report-content-card{background:white;border-radius:24px;padding:2rem;box-shadow:var(--premium-shadow);border:1px solid #f1f5f9;margin-bottom:1.5rem;}
.btn-back{background:rgba(255,255,255,.1);color:#94a3b8;border:1px solid rgba(255,255,255,.2);border-radius:10px;padding:0 16px;height:38px;font-weight:700;font-size:.82rem;text-decoration:none;display:inline-flex;align-items:center;gap:6px;}
.btn-back:hover{background:rgba(255,255,255,.2);color:white;text-decoration:none;}
.btn-print{background:linear-gradient(135deg,#1e293b,#334155);color:white;border:none;border-radius:10px;padding:0 18px;height:38px;font-weight:700;font-size:.82rem;cursor:pointer;display:inline-flex;align-items:center;gap:6px;}
.btn-export{background:linear-gradient(135deg,#27ae60,#1e8449);color:white;border:none;border-radius:10px;padding:0 18px;height:38px;font-weight:700;font-size:.82rem;display:inline-flex;align-items:center;gap:6px;text-decoration:none;cursor:pointer;}
.btn-export:hover{opacity:.88;color:white;text-decoration:none;}
.filter-bar{background:#f8fafc;border:1px solid #e2e8f0;border-radius:16px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;display:flex;align-items:center;flex-wrap:wrap;gap:12px;}
.section-title{font-size:1rem;font-weight:800;color:var(--slate-dark);margin-bottom:1.25rem;display:flex;align-items:center;gap:10px;}
.section-title::before{content:'';display:inline-block;width:4px;height:20px;background:linear-gradient(135deg,#6366f1,#4338ca);border-radius:4px;}
/* KPI Cards */
.kpi-strip{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:1.5rem;}
.kpi-card{border-radius:18px;padding:1.25rem 1.5rem;border:1px solid #f1f5f9;background:white;box-shadow:var(--premium-shadow);}
.kpi-card .lbl{font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:var(--slate-muted);margin-bottom:.3rem;}
.kpi-card .val{font-size:1.3rem;font-weight:900;color:var(--slate-dark);line-height:1.1;}
.kpi-card .sub{font-size:.72rem;color:var(--slate-muted);margin-top:2px;}
/* Method badges in table */
.mbadge{display:inline-block;padding:.3rem .7rem;border-radius:8px;font-size:.78rem;font-weight:700;}
.mb-cash{background:#ecfdf5;color:#059669;}
.mb-tf{background:#eff6ff;color:#2563eb;}
.mb-edc{background:#fff7ed;color:#d97706;}
.mb-ew{background:#faf5ff;color:#7c3aed;}
.mb-piu{background:#fff1f2;color:#dc2626;}
/* Print styles */
@media print{
    .report-hero,.filter-bar,.btn-back,.btn-print,.btn-export,
    .dataTables_wrapper .dataTables_filter,.dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_info,.dataTables_wrapper .dataTables_paginate,
    .main-header,.main-sidebar,.content-header,.control-sidebar,footer,
    #chart-col,.no-print{display:none !important;}
    .report-content-area{margin-top:0 !important;}
    .report-content-card{box-shadow:none !important;border:1px solid #e2e8f0 !important;border-radius:8px !important;padding:1rem !important;}
    .kpi-strip{grid-template-columns:repeat(6,1fr);gap:.5rem;}
    .kpi-card{padding:.6rem;box-shadow:none !important;border:1px solid #ddd !important;}
    table{font-size:8.5pt !important;}
    thead th{background-color:#1e293b !important;color:white !important;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
    .print-header{display:block !important;}
    body{background:white !important;}
    .content-wrapper{margin-left:0 !important;}
}
.print-header{display:none;text-align:center;margin-bottom:16px;}
.print-header h2{font-size:15pt;font-weight:900;margin:0 0 3px;}
.print-header p{font-size:8.5pt;color:#666;margin:0;}
.print-header hr{border-top:2px solid #1e293b;margin:7px 0;}
</style>

<!-- Print Header -->
<div class="print-header">
    <h2>Buku Besar Bulanan (Legger)</h2>
    <p>Periode: <?= date('d M Y', strtotime($mulai)) ?> s/d <?= date('d M Y', strtotime($selesai)) ?> &nbsp;|&nbsp; <?= $perusahaan ?> &nbsp;|&nbsp; Dicetak: <?= date('d/m/Y H:i') ?></p>
    <hr>
</div>

<div class="container-fluid px-4">

    <!-- ===== HERO HEADER ===== -->
    <div class="report-hero">
        <div class="container-fluid px-5">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
                <!-- Title -->
                <div style="display:flex;align-items:center;gap:1.25rem;">
                    <div style="width:65px;height:65px;background:rgba(255,255,255,.1);border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:1.8rem;border:1px solid rgba(255,255,255,.2);">
                        <i class="fa fa-book"></i>
                    </div>
                    <div>
                        <h1 style="font-size:1.9rem;font-weight:900;margin:0 0 .25rem 0;">Buku Besar Bulanan</h1>
                        <p style="margin:0;color:#94a3b8;font-size:.88rem;">
                            Legger Keuangan &mdash;
                            <?= date('d M Y', strtotime($mulai)) ?> s/d <?= date('d M Y', strtotime($selesai)) ?>
                            &nbsp;|&nbsp; <i class="fa fa-building-o"></i> <?= $perusahaan ?>
                        </p>
                    </div>
                </div>
                <!-- Action Buttons -->
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <a href="<?= base_url('laporan/export_legger?perusahaan_id='.$current_perusahaan_id.'&mulai='.$mulai.'&selesai='.$selesai) ?>" class="btn-export">
                        <i class="fa fa-file-excel-o"></i> Export Excel
                    </a>
                    <button onclick="window.print()" class="btn-print"><i class="fa fa-print"></i> Cetak</button>
                    <button type="button" class="btn-kirim-owner no-print"
                        data-toggle="modal" data-target="#modalKirimOwner"
                        data-report-type="legger"
                        data-params='<?= json_encode(['perusahaan_id'=>(int)$current_perusahaan_id,'mulai'=>$mulai,'selesai'=>$selesai]) ?>'>
                        <i class="fa fa-paper-plane"></i> Kirim ke Owner
                    </button>
                    <a href="<?= base_url('laporan/financial_hub') ?>" class="btn-back"><i class="fa fa-arrow-left"></i> Kembali</a>
                </div>
            </div>
        </div>
    </div>
    <!-- ===== END HERO ===== -->

    <div class="report-content-area">
        <div class="container-fluid px-5">

            <!-- Filter Bar -->
            <form class="filter-bar no-print" action="" method="GET">
                <label style="font-weight:700;font-size:.85rem;margin:0;">Periode</label>
                <input type="date" class="form-control" name="mulai" value="<?= $mulai ?>" style="width:auto;border-radius:10px;height:38px;">
                <span style="color:var(--slate-muted);font-weight:700;">s/d</span>
                <input type="date" class="form-control" name="selesai" value="<?= $selesai ?>" style="width:auto;border-radius:10px;height:38px;">
                <?php if ($is_admin_owner): ?>
                <select class="form-control" name="perusahaan_id" style="width:auto;border-radius:10px;height:38px;">
                    <?php foreach ($all_perusahaan as $p): ?>
                        <option value="<?= $p->id_perusahaan ?>" <?= $p->id_perusahaan == $current_perusahaan_id ? 'selected' : '' ?>><?= $p->nama_perusahaan ?></option>
                    <?php endforeach; ?>
                </select>
                <?php endif; ?>
                <button type="submit" style="background:linear-gradient(135deg,#6366f1,#4338ca);color:white;border:none;border-radius:10px;padding:0 20px;height:38px;font-weight:700;cursor:pointer;">
                    <i class="fa fa-refresh"></i>&nbsp; Tampilkan
                </button>
                <!-- Shortcut Months -->
                <div style="margin-left:auto;display:flex;gap:6px;">
                    <?php
                    $shortcuts = [
                        'Bln Ini'  => [date('Y-m-01'), date('Y-m-t')],
                        'Bln Lalu' => [date('Y-m-01', strtotime('-1 month')), date('Y-m-t', strtotime('-1 month'))],
                        '3 Bln'    => [date('Y-m-01', strtotime('-2 months')), date('Y-m-t')],
                    ];
                    foreach ($shortcuts as $lbl => [$s, $e]):
                    ?>
                    <a href="?mulai=<?= $s ?>&selesai=<?= $e ?>&perusahaan_id=<?= $current_perusahaan_id ?>"
                       style="background:white;border:1px solid #e2e8f0;border-radius:8px;padding:0 12px;height:34px;display:inline-flex;align-items:center;font-size:.78rem;font-weight:700;color:var(--slate-muted);text-decoration:none;">
                        <?= $lbl ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </form>

            <!-- KPI Strip -->
            <div class="kpi-strip">
                <div class="kpi-card">
                    <div class="lbl">Grand Total</div>
                    <div class="val" style="color:#6366f1;">Rp <?= number_format($grandtotal, 0, ',', '.') ?></div>
                    <div class="sub"><?= count($legger) ?> hari transaksi</div>
                </div>
                <div class="kpi-card">
                    <div class="lbl">Tunai / Cash</div>
                    <div class="val" style="color:#059669;">Rp <?= number_format($cash, 0, ',', '.') ?></div>
                    <div class="sub"><?= $grandtotal > 0 ? number_format(($cash/$grandtotal)*100, 1) : 0 ?>% dari total</div>
                </div>
                <div class="kpi-card">
                    <div class="lbl">Transfer Bank</div>
                    <div class="val" style="color:#2563eb;">Rp <?= number_format($transfer, 0, ',', '.') ?></div>
                    <div class="sub"><?= $grandtotal > 0 ? number_format(($transfer/$grandtotal)*100, 1) : 0 ?>% dari total</div>
                </div>
                <div class="kpi-card">
                    <div class="lbl">EDC / Debit</div>
                    <div class="val" style="color:#d97706;">Rp <?= number_format($edc, 0, ',', '.') ?></div>
                    <div class="sub"><?= $grandtotal > 0 ? number_format(($edc/$grandtotal)*100, 1) : 0 ?>% dari total</div>
                </div>
                <div class="kpi-card">
                    <div class="lbl">E-Wallet</div>
                    <div class="val" style="color:#7c3aed;">Rp <?= number_format($ewallet, 0, ',', '.') ?></div>
                    <div class="sub"><?= $grandtotal > 0 ? number_format(($ewallet/$grandtotal)*100, 1) : 0 ?>% dari total</div>
                </div>
                <div class="kpi-card">
                    <div class="lbl">Piutang Terbentuk</div>
                    <div class="val" style="color:#dc2626;">Rp <?= number_format($piutang, 0, ',', '.') ?></div>
                    <div class="sub"><?= $grandtotal > 0 ? number_format(($piutang/$grandtotal)*100, 1) : 0 ?>% dari total</div>
                </div>
            </div>

            <!-- Main Content Row -->
            <div class="row">
                <!-- Chart -->
                <div class="col-xl-4 no-print" id="chart-col">
                    <div class="report-content-card" style="padding:1.5rem;">
                        <div class="section-title" style="margin-bottom:1rem;">Komposisi Metode Bayar</div>
                        <canvas id="pie-legger" style="max-height:260px;"></canvas>
                        <div style="margin-top:1.5rem;">
                            <?php
                            $items = [
                                ['Tunai','#059669',$cash],
                                ['Transfer','#2563eb',$transfer],
                                ['EDC/Debit','#d97706',$edc],
                                ['E-Wallet','#7c3aed',$ewallet],
                                ['Piutang','#dc2626',$piutang],
                            ];
                            foreach ($items as [$lbl, $col, $val]):
                                $pct = $grandtotal > 0 ? ($val/$grandtotal)*100 : 0;
                            ?>
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.6rem;font-size:.82rem;">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <span style="width:10px;height:10px;border-radius:50%;background:<?= $col ?>;display:inline-block;"></span>
                                    <span style="font-weight:600;color:var(--slate-dark);"><?= $lbl ?></span>
                                </div>
                                <span style="font-weight:800;color:<?= $col ?>;"><?= number_format($pct, 1) ?>%</span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="col-xl-8">
                    <div class="report-content-card">
                        <div class="section-title">Rekapitulasi Kas Harian</div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="tbl-legger" style="font-size:.85rem;">
                                <thead style="background-color:#1e293b;color:white;">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th class="text-right">Tunai</th>
                                        <th class="text-right">Transfer</th>
                                        <th class="text-right">EDC</th>
                                        <th class="text-right">E-Wallet</th>
                                        <th class="text-right">Piutang</th>
                                        <th class="text-right">Total Harian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($legger as $row): ?>
                                    <tr>
                                        <td>
                                            <strong><?= date('d M Y', strtotime($row->tgl_bayar)) ?></strong><br>
                                            <small class="text-muted"><?= date('l', strtotime($row->tgl_bayar)) ?></small>
                                        </td>
                                        <td class="text-right"><span class="mbadge mb-cash">Rp <?= number_format($row->total_cash, 0, ',', '.') ?></span></td>
                                        <td class="text-right"><span class="mbadge mb-tf">Rp <?= number_format($row->total_transfer, 0, ',', '.') ?></span></td>
                                        <td class="text-right"><span class="mbadge mb-edc">Rp <?= number_format($row->total_edc, 0, ',', '.') ?></span></td>
                                        <td class="text-right"><span class="mbadge mb-ew">Rp <?= number_format($row->total_ewallet, 0, ',', '.') ?></span></td>
                                        <td class="text-right"><span class="mbadge mb-piu">Rp <?= number_format($row->total_piutang, 0, ',', '.') ?></span></td>
                                        <td class="text-right"><strong style="color:#6366f1;">Rp <?= number_format($row->total_harian, 0, ',', '.') ?></strong></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr style="background:#f8fafc;font-weight:900;">
                                        <td><strong>AKUMULASI TOTAL</strong></td>
                                        <td class="text-right" style="color:#059669;"><strong>Rp <?= number_format($cash, 0, ',', '.') ?></strong></td>
                                        <td class="text-right" style="color:#2563eb;"><strong>Rp <?= number_format($transfer, 0, ',', '.') ?></strong></td>
                                        <td class="text-right" style="color:#d97706;"><strong>Rp <?= number_format($edc, 0, ',', '.') ?></strong></td>
                                        <td class="text-right" style="color:#7c3aed;"><strong>Rp <?= number_format($ewallet, 0, ',', '.') ?></strong></td>
                                        <td class="text-right" style="color:#dc2626;"><strong>Rp <?= number_format($piutang, 0, ',', '.') ?></strong></td>
                                        <td class="text-right"><strong style="color:#6366f1;font-size:1rem;">Rp <?= number_format($grandtotal, 0, ',', '.') ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Nav -->
            <div class="no-print">
                <div style="font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:1.5px;color:var(--slate-muted);margin:1.5rem 0 1rem;padding-left:10px;border-left:3px solid #6366f1;">Navigasi Cepat</div>
                <div style="display:flex;flex-wrap:wrap;gap:10px;">
                    <?php
                    $navs = [
                        ['laporan/pemasukan_harian?perusahaan_id='.$current_perusahaan_id, 'fa-calendar-check-o', 'Harian'],
                        ['laporan/pemasukan_bulanan?perusahaan_id='.$current_perusahaan_id, 'fa-calendar', 'Bulanan'],
                        ['laporan/laba_rugi?perusahaan_id='.$current_perusahaan_id, 'fa-balance-scale', 'Laba Rugi'],
                        ['laporan/aging_piutang?perusahaan_id='.$current_perusahaan_id, 'fa-clock-o', 'Aging Piutang'],
                        ['laporan/tren_keuangan?perusahaan_id='.$current_perusahaan_id, 'fa-line-chart', 'Tren 12 Bln'],
                        ['laporan/arus_kas?perusahaan_id='.$current_perusahaan_id, 'fa-exchange', 'Arus Kas'],
                        ['laporan/financial_hub', 'fa-th-large', 'Hub Laporan'],
                    ];
                    foreach ($navs as [$url, $icon, $lbl]):
                    ?>
                    <a href="<?= base_url($url) ?>"
                       style="background:white;border:1px solid #e2e8f0;border-radius:12px;padding:.55rem 1.1rem;display:inline-flex;align-items:center;gap:8px;font-weight:700;font-size:.8rem;color:var(--slate-dark);text-decoration:none;transition:all .25s;">
                        <i class="fa <?= $icon ?>" style="color:#6366f1;"></i> <?= $lbl ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

        </div><!-- /container -->
    </div><!-- /report-content-area -->

</div><!-- /container-fluid -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function initLegger() {
    if (typeof jQuery === 'undefined') { return setTimeout(initLegger, 80); }
    // DataTable
    jQuery('#tbl-legger').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json' },
        dom: 'lrtip',
        pageLength: 31,
        order: [[0, 'asc']]
    });

    // Donut Chart
    new Chart(document.getElementById('pie-legger').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Tunai', 'Transfer', 'EDC/Debit', 'E-Wallet', 'Piutang'],
            datasets: [{
                data: [<?= $cash ?>, <?= $transfer ?>, <?= $edc ?>, <?= $ewallet ?>, <?= $piutang ?>],
                backgroundColor: ['#059669', '#2563eb', '#d97706', '#7c3aed', '#dc2626'],
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 12
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (c) {
                            var total = c.dataset.data.reduce(function(a, b){ return a+b; }, 0);
                            var pct = total > 0 ? ((c.raw / total) * 100).toFixed(1) : 0;
                            return c.label + ': Rp ' + c.raw.toLocaleString('id-ID') + ' (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });
})();
</script>
<?php $this->load->view('admin/laporan/partials/kirim_owner_modal'); ?>
