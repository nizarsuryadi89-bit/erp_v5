<style>
:root{--primary-gradient:linear-gradient(135deg,#1a1c23 0%,#2c3e50 100%);--premium-shadow:0 10px 30px rgba(0,0,0,.05);--slate-dark:#1e293b;--slate-muted:#64748b;}
.report-hero{background:var(--primary-gradient);color:white;padding:2rem 0 4.5rem;border-radius:0 0 30px 30px;box-shadow:0 15px 35px rgba(0,0,0,.15);}
.report-content-area{position:relative;z-index:10;padding-bottom:2rem;}
.report-content-card{background:white;border-radius:24px;padding:2rem;box-shadow:var(--premium-shadow);border:1px solid #f1f5f9;}
.alert-banner{background:#fff1f2;border:1px solid #fecdd3;border-radius:16px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:12px;}
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
.section-title::before{content:'';display:inline-block;width:4px;height:20px;background:linear-gradient(135deg,#f72585,#b5179e);border-radius:4px;}
</style>

<div class="container-fluid px-4">
    <div style="height: 35px;"></div>
    <div class="report-content-area">
        <div class="container-fluid px-5">
            <?php $this->load->view('admin/warehouse/components/sop_stepper', ['active_step' => 2]); ?>
            <div class="report-content-card">

                <?php if (count($stok_kritis) > 0): ?>
                <div class="alert-banner">
                    <i class="fa fa-bell" style="color:#f43f5e;font-size:1.3rem;"></i>
                    <div>
                        <strong style="color:#be123c;">Peringatan!</strong>
                        <span style="color:#9f1239;"> Terdapat <strong><?= count($stok_kritis) ?></strong> item dengan stok di bawah minimum. Segera lakukan pembelian.</span>
                    </div>
                </div>
                <?php else: ?>
                <div class="alert-banner" style="background:#f0fdf4;border-color:#bbf7d0;">
                    <i class="fa fa-check-circle" style="color:#16a34a;font-size:1.3rem;"></i>
                    <strong style="color:#15803d;">Semua stok dalam kondisi aman.</strong>
                </div>
                <?php endif; ?>

                <div class="section-title mb-2">Daftar Stok Kritis</div>
                <div class="alert alert-info py-2 small mb-3" style="background:#e0f2fe; border-color:#bae6fd; color:#0369a1; border-radius:10px;">
                    <i class="fa fa-info-circle me-1"></i> <strong>Informasi:</strong> Nilai <i>Stok Aktif</i> dan <i>Kekurangan</i> yang ditampilkan pada tabel ini adalah rekapitulasi (gabungan) dari seluruh gudang.
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dt-kritis">
                        <thead style="background:#1e293b;color:white;">
                            <tr>
                                <th>No</th><th>Nama Material</th><th>Varian</th><th>Gudang</th>
                                <th class="text-right">Stok Aktif</th>
                                <th class="text-right">Stok Min</th>
                                <th class="text-right">Kekurangan</th>
                                <th>Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $format_compound = function($qty_base, $konversi, $satuan_gudang, $satuan_produksi) {
                                    $divisor = max(1, (int) $konversi);
                                    $is_negative = $qty_base < 0;
                                    $abs_pieces = abs(round((float)$qty_base));
                                    $whole = floor($abs_pieces / $divisor);
                                    $pieces = (int) $abs_pieces % (int) $divisor;

                                    $f = "";
                                    if ($whole > 0 || ($whole == 0 && $pieces == 0)) {
                                        $f .= $whole . " " . $satuan_gudang;
                                    }
                                    if ($pieces > 0 && $konversi > 1) {
                                        $f .= ($f ? " " : "") . $pieces . " " . $satuan_produksi;
                                    }
                                    if ($is_negative && $f !== "0 " . $satuan_gudang) {
                                        $f = "-" . $f;
                                    }
                                    return $f;
                                };
                            
                            $no=1; foreach ($stok_kritis as $row): 
                                $konversi = (float) ($row->konversi > 0 ? $row->konversi : 1);
                                $stok_aktif_str = $format_compound($row->stok_aktif_base, $konversi, $row->satuan_gudang, $row->satuan_produksi);
                                $stok_min_str = $format_compound($row->stok_min_base, $konversi, $row->satuan_gudang, $row->satuan_produksi);
                                $kekurangan_str = $format_compound($row->kekurangan_base, $konversi, $row->satuan_gudang, $row->satuan_produksi);
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row->bahan_nama ?></td>
                                    <td><?= $row->varian ?: '-' ?></td>
                                    <td><?= $row->gudang_nama ?: '-' ?></td>
                                    <td class="text-right <?= $row->stok_aktif_base <= 0 ? 'text-danger' : 'text-warning' ?>">
                                        <strong><?= $stok_aktif_str ?></strong>
                                    </td>
                                    <td class="text-right fw-bold text-dark"><?= $stok_min_str ?></td>
                                    <td class="text-right text-danger"><strong><?= $kekurangan_str ?></strong></td>
                                    <td><?= $row->satuan_gudang ?></td>
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
    $('#dt-kritis').DataTable({
        "language":{"url":"https://cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"},
        "pageLength":25,"order":[[6,"desc"]]
    });
});
</script>
