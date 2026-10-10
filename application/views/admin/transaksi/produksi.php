<div class="app-content">
    <div class="container-fluid">
        <!-- Header -->
        <?php $this->load->view('layout_admin/header_modul'); ?>

        <!-- Stats Overview -->
        <?php
        // Group data first
        $groupedData = [];
        foreach ($tampilData as $dt) {
            $groupedData[$dt->nospk][] = $dt;
        }

        $total_qty = 0;
        $sedang_produksi = 0;
        $di_pb = 0;
        $total_routing = 0;
        $routing_done = 0;

        foreach ($groupedData as $nospk => $items) {
            $has_progress = false;
            $has_pb = false;
            foreach ($items as $item) {
                $st = isset($item->status_produksi) ? (string) $item->status_produksi : '0';
                if ($st === '1')
                    $has_progress = true;
                if ($st === '3')
                    $has_pb = true;
                if ($st === '2' || $st === '3' || $st === '4')
                    $routing_done++;

                $total_qty += (int) $item->qty;
                $total_routing++;
            }

            if ($has_pb)
                $di_pb++;
            elseif ($has_progress)
                $sedang_produksi++;
        }
        ?>

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center p-3 h-100">
                    <small class="text-muted text-uppercase fw-bold">Total Item Produksi</small>
                    <h3 class="fw-900 mb-0"><?= number_format($total_qty) ?></h3>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center p-3 h-100 border-start border-warning border-4">
                    <small class="text-muted text-uppercase fw-bold">Posisi: Operator</small>
                    <h3 class="fw-900 mb-0 text-warning"><?= $sedang_produksi ?> <small class="fs-6">SPK</small></h3>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center p-3 h-100 border-start border-primary border-4">
                    <small class="text-muted text-uppercase fw-bold">Posisi: PB</small>
                    <h3 class="fw-900 mb-0 text-primary"><?= $di_pb ?> <small class="fs-6">SPK</small></h3>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center p-3 h-100 border-start border-success border-4">
                    <small class="text-muted text-uppercase fw-bold">Global Progress</small>
                    <h3 class="fw-900 mb-0 text-success"><?= $total_routing > 0 ? round(($routing_done / $total_routing) * 100) : 0 ?>%</h3>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="fas fa-chart-line me-2"></i>Tracking Posisi SPK</h6>
                <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Cari SPK / Konsumen..." style="width: 250px;">
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="productionTable">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th width="15%">SPK & Konsumen</th>
                                <th width="20%">Produk</th>
                                <th>Alur Proses (Tracking)</th>
                                <th width="15%" class="text-center">Posisi Terakhir</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($groupedData as $nospk => $dataGroup):
                                // Filter items not in PB (optional based on user preference, but user said "report position", so we show everything current)
                                usort($dataGroup, function ($a, $b) {
                                    return (int) $a->routing_urutan - (int) $b->routing_urutan;
                                });
                                $firstData = $dataGroup[0];

                                // Determine Current Position
                                $current_pos = 'Menunggu';
                                $pos_badge = 'bg-secondary';

                                foreach ($dataGroup as $dt) {
                                    $st = isset($dt->status_produksi) ? (string) $dt->status_produksi : '0';
                                    if ($st === '1') {
                                        $current_pos = 'Dikerjakan: ' . $dt->operator_nama;
                                        $pos_badge = 'bg-warning text-dark';
                                    } elseif ($st === '3') {
                                        $current_pos = 'Di Pengambilan (PB)';
                                        $pos_badge = 'bg-primary';
                                        break; // PB is final in this report context
                                    }
                                }
                                ?>
                                <tr data-search="<?= strtolower($nospk . ' ' . ($firstData->konsumen_nama ?? '') . ' ' . $firstData->product_nama) ?>">
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td>
                                        <div class="fw-bold text-dark">#<?= $nospk ?></div>
                                        <div class="small text-muted"><?= $firstData->konsumen_nama ?? '-' ?></div>
                                    </td>
                                    <td>
                                        <?php
                                        $shown = [];
                                        foreach ($dataGroup as $item) {
                                            $key = $item->product_nama . '_' . $item->qty;
                                            if (!in_array($key, $shown)) {
                                                $shown[] = $key;
                                                echo '<div class="small fw-bold">' . $item->product_nama . ' <span class="text-muted">(Qty: ' . $item->qty . ')</span></div>';
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <?php foreach ($dataGroup as $dt):
                                                $st = isset($dt->status_produksi) ? (string) $dt->status_produksi : '0';
                                                $icon = 'fa-circle';
                                                $col = 'secondary';
                                                if ($st === '1') {
                                                    $icon = 'fa-sync fa-spin';
                                                    $col = 'warning';
                                                } elseif ($st === '2' || $st === '3' || $st === '4') {
                                                    $icon = 'fa-check-circle';
                                                    $col = 'success';
                                                }
                                                ?>
                                                    <div class="badge bg-light text-dark border p-2 d-flex align-items-center" title="Operator: <?= $dt->operator_nama ?>">
                                                        <i class="fas <?= $icon ?> text-<?= $col ?> me-1"></i>
                                                        <span style="font-size: 10px;"><?= $dt->routing_name ?></span>
                                                    </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?= $pos_badge ?> px-3 py-2"><?= $current_pos ?></span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalDetail<?= preg_replace("/[^a-zA-Z0-9]/", "", $nospk) ?>">
                                            <i class="fas fa-eye me-1"></i>Detail
                                        </button>
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

<!-- Modal Detail Reporting -->
<?php foreach ($groupedData as $nospk => $dataGroup):
    $firstData = $dataGroup[0];
    $cleanID = preg_replace("/[^a-zA-Z0-9]/", "", $nospk);
    ?>
    <div class="modal fade" id="modalDetail<?= $cleanID ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white">
                    <h6 class="modal-title">Laporan Progress Produksi #<?= $nospk ?></h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Konsumen</small>
                            <span class="fw-bold"><?= $firstData->konsumen_nama ?? '-' ?></span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Tanggal SPK</small>
                            <span class="fw-bold"><?= date_indo($firstData->dateline_tgl) ?></span>
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">Status Pekerjaan Per Tahap</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Urutan</th>
                                    <th>Tahap Proses</th>
                                    <th>Nama Operator</th>
                                    <th>Status</th>
                                    <th>Out Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dataGroup as $item):
                                    $st = isset($item->status_produksi) ? (string) $item->status_produksi : '0';
                                    $lbl = 'Belum Mulai';
                                    $cls = 'bg-secondary';
                                    if ($st === '1') {
                                        $lbl = 'Sedang Dikerjakan';
                                        $cls = 'bg-warning text-dark';
                                    } elseif ($st === '2') {
                                        $lbl = 'Selesai di Operator';
                                        $cls = 'bg-info';
                                    } elseif ($st === '3') {
                                        $lbl = 'Selesai (Di PB)';
                                        $cls = 'bg-primary';
                                    } elseif ($st === '4') {
                                        $lbl = 'Selesai (Handover)';
                                        $cls = 'bg-success';
                                    }
                                    ?>
                                    <tr>
                                        <td><?= $item->routing_urutan ?></td>
                                        <td class="fw-bold"><?= $item->routing_name ?></td>
                                        <td><?= $item->operator_nama ?></td>
                                        <td><span class="badge <?= $cls ?>"><?= $lbl ?></span></td>
                                        <td><?= isset($item->qty_produksi) ? $item->qty_produksi : '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <a href="<?= base_url('produksi/cetak_label/' . $nospk) ?>" target="_blank" class="btn btn-warning btn-sm">
                        <i class="fas fa-print me-1"></i>Cetak Label
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    let val = this.value.toLowerCase();
    document.querySelectorAll('#productionTable tbody tr').forEach(row => {
        row.style.display = row.getAttribute('data-search').includes(val) ? '' : 'none';
    });
});
</script>
