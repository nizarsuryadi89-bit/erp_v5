<style>
:root{--primary-gradient:linear-gradient(135deg,#1a1c23 0%,#2c3e50 100%);--premium-shadow:0 10px 30px rgba(0,0,0,.05);--slate-dark:#1e293b;--slate-muted:#64748b;}
.report-hero{background:var(--primary-gradient);color:white;padding:2rem 0 5rem;border-radius:0 0 30px 30px;box-shadow:0 15px 35px rgba(0,0,0,.15);}
.report-content-area{margin-top:-3.5rem;position:relative;z-index:10;padding-bottom:2rem;}
.report-content-card{background:white;border-radius:24px;padding:2rem;box-shadow:var(--premium-shadow);border:1px solid #f1f5f9;}
.btn-back{background:rgba(255,255,255,.1);color:#94a3b8;border:1px solid rgba(255,255,255,.2);border-radius:10px;padding:0 16px;height:38px;font-weight:700;font-size:.82rem;text-decoration:none;display:inline-flex;align-items:center;gap:6px;}
.btn-back:hover{background:rgba(255,255,255,.2);color:white;}
.btn-print{background:linear-gradient(135deg,#1e293b,#334155);color:white;border:none;border-radius:10px;padding:0 18px;height:38px;font-weight:700;font-size:.82rem;cursor:pointer;display:inline-flex;align-items:center;gap:6px;}
.btn-export{background:linear-gradient(135deg,#27ae60,#1e8449);color:white;border:none;border-radius:10px;padding:0 18px;height:38px;font-weight:700;font-size:.82rem;display:inline-flex;align-items:center;gap:6px;text-decoration:none;}
.btn-export:hover{opacity:.88;color:white;}
.section-title{font-size:1rem;font-weight:800;color:var(--slate-dark);margin-bottom:1.25rem;display:flex;align-items:center;gap:10px;}
.section-title::before{content:'';display:inline-block;width:4px;height:20px;background:linear-gradient(135deg,#f72585,#b5179e);border-radius:4px;}
.aging-bucket{border-radius:18px;padding:1.5rem;text-align:center;margin-bottom:1rem;}
.aging-bucket .val{font-size:1.6rem;font-weight:900;}
.aging-bucket .lbl{font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.5px;margin-top:4px;}
.risk-high{background:#fff1f2;border:1px solid #fecdd3;color:#f43f5e;}
.risk-med{background:#fffbeb;border:1px solid #fef3c7;color:#f59e0b;}
.risk-low{background:#ecfdf5;border:1px solid #d1fae5;color:#10b981;}
.risk-ok{background:#eff6ff;border:1px solid #dbeafe;color:#3b82f6;}
@media print{.report-hero,.btn-back,.btn-print,.btn-export,.dataTables_wrapper .dataTables_filter,.dataTables_wrapper .dataTables_length,.dataTables_wrapper .dataTables_info,.dataTables_wrapper .dataTables_paginate,.main-header,.main-sidebar,.content-header,.control-sidebar,footer{display:none !important;}.report-content-area{margin-top:0 !important;}.report-content-card{box-shadow:none !important;border:none !important;border-radius:0 !important;padding:0 !important;}table{font-size:9pt !important;}thead th{background-color:#1e293b !important;color:white !important;-webkit-print-color-adjust:exact;print-color-adjust:exact;}.print-header{display:block !important;}body{background:white !important;}.content-wrapper{margin-left:0 !important;}}
.print-header{display:none;text-align:center;margin-bottom:16px;}
.print-header h2{font-size:16pt;font-weight:900;margin:0 0 4px;}
.print-header p{font-size:9pt;color:#666;margin:0;}
.print-header hr{border-top:2px solid #1e293b;margin:8px 0;}
</style>

<div class="print-header">
    <h2>Laporan Aging Piutang</h2>
    <p>Dicetak: <?= date('d/m/Y H:i') ?> | Perusahaan: <?= $perusahaan ?></p><hr>
</div>

<div class="container-fluid px-4">
    <div class="report-hero">
        <div class="container-fluid px-5">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
                <div style="display:flex;align-items:center;gap:1.25rem;">
                    <div style="width:65px;height:65px;background:rgba(255,255,255,.1);border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:1.8rem;border:1px solid rgba(255,255,255,.2);"><i class="fa fa-clock-o"></i></div>
                    <div>
                        <h1 style="font-size:1.9rem;font-weight:900;margin:0 0 .25rem 0;">Aging Piutang</h1>
                        <p style="margin:0;color:#94a3b8;font-size:.88rem;">Analisis umur piutang per konsumen — identifikasi risiko penagihan</p>
                    </div>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <a href="<?= base_url('laporan/export_aging_piutang?perusahaan_id='.$current_perusahaan_id) ?>" class="btn-export"><i class="fa fa-file-excel-o"></i> Excel</a>
                    <button onclick="window.print()" class="btn-print"><i class="fa fa-print"></i> Cetak</button>
                    <button type="button" class="btn-kirim-owner no-print"
                        data-toggle="modal" data-target="#modalKirimOwner"
                        data-report-type="aging_piutang"
                        data-params='<?= json_encode(['perusahaan_id'=>(int)$current_perusahaan_id]) ?>'>
                        <i class="fa fa-paper-plane"></i> Kirim ke Owner
                    </button>
                    <a href="<?= base_url('laporan/financial_hub') ?>" class="btn-back"><i class="fa fa-arrow-left"></i> Kembali</a>
                </div>
            </div>
        </div>
    </div>

    <div class="report-content-area">
        <div class="container-fluid px-5">

            <!-- Aging Buckets Summary -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="aging-bucket risk-ok">
                        <div class="val">Rp <?= number_format($aging_summary->age_0_15 ?? 0, 0, ',', '.') ?></div>
                        <div class="lbl">🟢 0–15 Hari</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="aging-bucket risk-low">
                        <div class="val">Rp <?= number_format($aging_summary->age_16_30 ?? 0, 0, ',', '.') ?></div>
                        <div class="lbl">🟡 16–30 Hari</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="aging-bucket risk-med">
                        <div class="val">Rp <?= number_format($aging_summary->age_31_60 ?? 0, 0, ',', '.') ?></div>
                        <div class="lbl">🟠 31–60 Hari</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="aging-bucket risk-high">
                        <div class="val">Rp <?= number_format($aging_summary->age_over_60 ?? 0, 0, ',', '.') ?></div>
                        <div class="lbl">🔴 &gt;60 Hari (KRITIS)</div>
                    </div>
                </div>
            </div>

            <div class="report-content-card">
                <div class="section-title">Detail Piutang per Konsumen <small style="font-weight:400;font-size:.8rem;color:var(--slate-muted);">(<?= count($aging_detail) ?> konsumen)</small></div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tbl-aging">
                        <thead style="background-color:#1e293b;color:white;">
                            <tr>
                                <th>No SPK</th>
                                <th>Konsumen</th>
                                <th>No HP</th>
                                <th class="text-right">Total Tagihan</th>
                                <th class="text-right">Sudah Bayar</th>
                                <th class="text-right">Sisa Piutang</th>
                                <th class="text-center">Umur (Hari)</th>
                                <th class="text-center">Tanggal Transaksi</th>
                                <th class="text-center">Risiko</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($aging_detail as $row): 
                                $umur = (int)(strtotime(date('Y-m-d')) - strtotime($row->tgl_bayar)) / 86400;
                                $sisa = $row->total_tagihan - $row->total_bayar;
                                if ($umur <= 15) { $risk = 'Aman'; $badge = 'success'; }
                                elseif ($umur <= 30) { $risk = 'Perhatian'; $badge = 'primary'; }
                                elseif ($umur <= 60) { $risk = 'Waspada'; $badge = 'warning'; }
                                else { $risk = 'KRITIS'; $badge = 'danger'; }
                            ?>
                            <tr>
                                <td><code><?= $row->nospk ?></code></td>
                                <td><strong><?= $row->konsumen_nama ?></strong></td>
                                <td><?= $row->konsumen_nohp ?: '—' ?></td>
                                <td class="text-right">Rp <?= number_format($row->total_tagihan, 0, ',', '.') ?></td>
                                <td class="text-right">Rp <?= number_format($row->total_bayar, 0, ',', '.') ?></td>
                                <td class="text-right"><strong class="text-danger">Rp <?= number_format($sisa, 0, ',', '.') ?></strong></td>
                                <td class="text-center">
                                    <span style="font-weight:900;color:<?= $umur>60?'#f43f5e':($umur>30?'#f59e0b':'#10b981') ?>"><?= $umur ?> hari</span>
                                </td>
                                <td class="text-center"><?= date('d/m/Y', strtotime($row->tgl_bayar)) ?></td>
                                <td class="text-center"><span class="label label-<?= $badge ?>"><?= $risk ?></span></td>
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
(function _init_aging_piutang(){ if(typeof jQuery==="undefined"){return setTimeout(_init_aging_piutang,80);} var $=jQuery; (function(){
    $('#tbl-aging').DataTable({
        "language": {"url": "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"},
        "pageLength": 25,
        "order": [[6, "desc"]]
    });
})();
</script>
<?php $this->load->view('admin/laporan/partials/kirim_owner_modal'); ?>
