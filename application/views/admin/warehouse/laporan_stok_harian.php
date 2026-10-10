<style>
:root{--primary-gradient:linear-gradient(135deg,#1a1c23 0%,#2c3e50 100%);--premium-shadow:0 10px 30px rgba(0,0,0,.05);--slate-dark:#1e293b;--slate-muted:#64748b;}
.report-hero{background:var(--primary-gradient);color:white;padding:2rem 0 4.5rem;border-radius:0 0 30px 30px;box-shadow:0 15px 35px rgba(0,0,0,.15);}
.report-content-area{margin-top:-3rem;position:relative;z-index:10;padding-bottom:2rem;}
.report-content-card{background:white;border-radius:24px;padding:2rem;box-shadow:var(--premium-shadow);border:1px solid #f1f5f9;}
.filter-bar{background:#f8fafc;border:1px solid #e2e8f0;border-radius:16px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;display:flex;align-items:center;flex-wrap:wrap;gap:12px;}
.filter-bar label{font-weight:700;font-size:.85rem;color:var(--slate-dark);margin:0;}
.filter-bar .form-control{border-radius:10px;border:1px solid #e2e8f0;height:38px;font-size:.85rem;}
.btn-filter{background:linear-gradient(135deg,#3c8dbc,#2471a3);color:white;border:none;border-radius:10px;padding:0 20px;height:38px;font-weight:700;font-size:.82rem;text-transform:uppercase;letter-spacing:.5px;cursor:pointer;}
.btn-back{background:rgba(255,255,255,.1);color:#94a3b8;border:1px solid rgba(255,255,255,.2);border-radius:10px;padding:0 16px;height:38px;font-weight:700;font-size:.82rem;text-decoration:none;display:inline-flex;align-items:center;gap:6px;}
.btn-back:hover{background:rgba(255,255,255,.2);color:white;}
.btn-print{background:linear-gradient(135deg,#1e293b,#334155);color:white;border:none;border-radius:10px;padding:0 18px;height:38px;font-weight:700;font-size:.82rem;cursor:pointer;display:inline-flex;align-items:center;gap:6px;text-decoration:none;}
.btn-print:hover{opacity:.88;color:white;}
.section-title{font-size:1rem;font-weight:800;color:var(--slate-dark);margin-bottom:1.25rem;display:flex;align-items:center;gap:10px;}
.section-title::before{content:'';display:inline-block;width:4px;height:20px;background:linear-gradient(135deg,#3c8dbc,#2471a3);border-radius:4px;}

/* ===== PRINT STYLES ===== */
@media print {
    .report-hero, .filter-bar, .btn-back, .btn-print,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate,
    .main-header, .main-sidebar, .content-header,
    .control-sidebar, footer { display: none !important; }
    .report-content-area { margin-top: 0 !important; }
    .report-content-card { box-shadow: none !important; border: none !important; border-radius: 0 !important; padding: 0 !important; }
    .section-title { font-size: 14pt; margin-bottom: 10px; }
    .section-title::before { display: none; }
    table { font-size: 9pt !important; width: 100% !important; }
    thead th { background-color: #1e293b !important; color: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .print-header { display: block !important; }
    body { background: white !important; }
    .content-wrapper { margin-left: 0 !important; }
}
.print-header { display: none; text-align: center; margin-bottom: 20px; }
.print-header h2 { font-size: 16pt; font-weight: 900; margin: 0 0 4px 0; }
.print-header p { font-size: 9pt; color: #666; margin: 0; }
.print-header hr { border-top: 2px solid #1e293b; margin: 8px 0; }
</style>

<div class="print-header">
    <h2>Laporan Pergerakan Stok Harian</h2>
    <p>Periode: <?= date('d/m/Y', strtotime($date_from)) ?> s/d <?= date('d/m/Y', strtotime($date_to)) ?> | Dicetak: <?= date('d/m/Y H:i') ?></p>
    <hr>
</div>

<div class="container-fluid px-4">
    <div class="report-hero">
        <div class="container-fluid px-5">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
                <div style="display:flex;align-items:center;gap:1.25rem;">
                    <div style="width:60px;height:60px;background:rgba(255,255,255,.1);border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;border:1px solid rgba(255,255,255,.2);">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <div>
                        <h1 style="font-size:1.8rem;font-weight:900;margin:0 0 .2rem 0;">Laporan Stok Harian</h1>
                        <p style="margin:0;color:#94a3b8;font-size:.85rem;">Pergerakan stok masuk dan keluar per hari</p>
                    </div>
                </div>
                <a href="<?= base_url('warehouse/analytics') ?>" class="btn-back"><i class="fa fa-arrow-left"></i> Kembali</a>
            </div>
        </div>
    </div>

    <div class="report-content-area">
        <div class="container-fluid px-5">
            <div class="report-content-card">

                <form action="<?= base_url('warehouse/laporan_stok_harian') ?>" method="GET" class="filter-bar">
                    <label>Dari</label>
                    <input type="date" class="form-control" name="date_from" value="<?= $date_from ?>" style="width:auto;">
                    <label>Sampai</label>
                    <input type="date" class="form-control" name="date_to" value="<?= $date_to ?>" style="width:auto;">
                    <select class="form-control" name="gudang_id">
                        <option value="">-- Semua Gudang --</option>
                        <?php foreach ($warehouses as $w): ?>
                            <option value="<?= $w->gudang_id ?>" <?= ($selected_warehouse == $w->gudang_id) ? 'selected' : '' ?>>
                                <?= $w->gudang_nama ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn-filter"><i class="fa fa-search"></i> &nbsp;Tampilkan</button>
                    <button type="button" class="btn-print" onclick="window.print()"><i class="fa fa-print"></i> &nbsp;Cetak</button>
                </form>

                <div class="section-title">Data Pergerakan Stok &nbsp;<small style="font-weight:400;font-size:.8rem;color:var(--slate-muted);">(<?= count($daily_stock) ?> baris)</small></div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="datatable-stok">
                        <thead style="background-color: #1e293b; color: white;">
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Material</th>
                                <th>Varian</th>
                                <th>Jenis Transaksi</th>
                                <th class="text-right">Stok Masuk</th>
                                <th class="text-right">Stok Keluar</th>
                                <th>Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($daily_stock as $row): ?>
                                <tr>
                                    <td><?= date('d-m-Y', strtotime($row->tanggal)) ?></td>
                                    <td><?= $row->bahan_nama ?></td>
                                    <td><?= $row->varian ?: '-' ?></td>
                                    <td>
                                        <span class="label label-<?= $row->trx_type == 'Pembelian' ? 'success' : ($row->trx_type == 'Mutation' ? 'info' : 'warning') ?>">
                                            <?= $row->trx_type ?>
                                        </span>
                                    </td>
                                    <td class="text-right text-success"><strong><?= (float)$row->total_masuk ?></strong></td>
                                    <td class="text-right">
                                        <?php if ((float)$row->total_keluar > 0): ?>
                                            <?php if ($row->satuan_produksi && $row->satuan_nama != $row->satuan_produksi && $row->trx_type == 'Produksi'): ?>
                                                <div class="text-danger" style="font-weight: 700;">
                                                    <?= (float)$row->total_produksi_keluar ?> <small><?= $row->satuan_produksi ?></small>
                                                </div>
                                                <div class="text-muted" style="font-size: 0.75rem;">
                                                    <i class="fa fa-exchange-alt" style="color: #0dcaf0;"></i> <?= (float)$row->total_keluar ?> <?= $row->satuan_nama ?>
                                                </div>
                                            <?php else: ?>
                                                <strong class="text-danger"><?= (float)$row->total_keluar ?></strong>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <strong class="text-danger">0</strong>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $row->satuan_nama ?></td>
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
$(document).ready(function() {
    $('#datatable-stok').DataTable({
        "language": {"url": "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"},
        "pageLength": 25,
        "order": [[0, "desc"]]
    });
});
</script>
